<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
require_once 'inc/connection.php';

$uploadsDir = __DIR__ . '/uploads';
$files = [];

// Prefer DB table if exists
try{
    $stmt = $pdo->query("SHOW TABLES LIKE 'zip_uploads'");
    $hasTable = $stmt->fetch() ? true : false;
}catch(Throwable $e){ $hasTable = false; }

if($hasTable){
  // join users table if present to show uploader username
  $stmt = $pdo->query('SELECT z.id, z.file_name, z.original_name, z.relative_path, z.file_size, z.uploaded_by, z.status, z.created_at, u.username as uploaded_by_username FROM zip_uploads z LEFT JOIN users u ON z.uploaded_by = u.id ORDER BY z.created_at DESC');
  $files = $stmt->fetchAll();
} else {
    // fallback: list files in uploads directory
    if(is_dir($uploadsDir)){
        $dir = new DirectoryIterator($uploadsDir);
        foreach($dir as $fileinfo){
            if($fileinfo->isFile()){
                $files[] = [
                    'file_name' => $fileinfo->getFilename(),
                    'original_name' => $fileinfo->getFilename(),
                    'relative_path' => 'uploads/' . $fileinfo->getFilename(),
                    'file_size' => $fileinfo->getSize(),
                    'created_at' => date('Y-m-d H:i:s', $fileinfo->getMTime())
                ];
            }
        }
    }
}
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Uploaded Files</h1></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="card">
        <div class="card-body">
          <table class="table table-bordered table-striped" id="uploadsTable">
            <thead><tr><th>#</th><th>Name</th><th>Size (MB)</th><th>Uploaded</th><th>Uploaded By</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach($files as $i => $f): ?>
                <tr>
                  <td><?php echo $i+1; ?></td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($f['relative_path']); ?>" target="_blank">Download</a>
                    &nbsp;
                    <a href="<?php echo htmlspecialchars($f['relative_path']); ?>" target="_blank"><?php echo htmlspecialchars($f['original_name'] ?? $f['file_name']); ?></a>
                  </td>
                  <td>
                    <?php
                      $bytes = isset($f['file_size']) ? floatval($f['file_size']) : 0;
                      $mb = $bytes / (1024 * 1024);
                      echo number_format($mb, 2) . ' MB';
                    ?>
                  </td>
                  <td>
                    <?php
                      $raw = $f['created_at'] ?? null;
                      $out = '';
                      if($raw){
                        // Try parse common formats, prefer DB Y-m-d H:i:s
                        $tz = new DateTimeZone('Asia/Kolkata');
                        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $raw, $tz);
                        if(!$dt){
                          // fallback: try strtotime
                          $ts = strtotime($raw);
                          if($ts !== false){
                            $dt = new DateTime('@' . $ts);
                            $dt->setTimezone($tz);
                          }
                        }
                        if($dt){
                          $out = $dt->format('d-m-Y H:i:s');
                        } else {
                          $out = htmlspecialchars($raw);
                        }
                      }
                  </div>

                <?php require_once 'inc/footer.php'; ?>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
          </div>
        </div>
      </div>

      <script>
      $(function(){
        $('#uploadsTable').DataTable();

        $(document).on('click', '.delete-upload', function(){
          var $btn = $(this);
          var file = $btn.data('file');
          var $row = $btn.closest('tr');
          // store target row on modal for later removal
          $('#confirmDeleteModal').data('targetRow', $row).data('targetFile', file).modal('show');
        });

        $('#confirmDeleteBtn').on('click', function(){
          var $modal = $('#confirmDeleteModal');
          var file = $modal.data('targetFile');
          var $row = $modal.data('targetRow');
          var $confirm = $(this);
          if(!file){ toastr.error('No file selected'); return; }
          // close modal and disable button while request runs
          $modal.modal('hide');
          $confirm.prop('disabled', true).text('Deleting...');

          $.ajax({
            url: 'ajax/upload_file.php',
            method: 'POST',
            data: { action: 'delete', file: file },
            dataType: 'json'
          }).done(function(resp){
            console.log('Delete response:', resp);
            if(resp && resp.success){
              toastr.success('File deleted');
              // remove row from table gracefully
              if($row && $row.length){
                $row.fadeOut(250, function(){
                  var table = $('#uploadsTable').DataTable();
                  // if DataTable exists remove row via API
                  if($.fn.dataTable.isDataTable('#uploadsTable')){
                    table.row($(this)).remove().draw(false);
                  } else { $(this).remove(); }
                });
              } else {
                setTimeout(function(){ location.reload(); }, 500);
              }
            } else {
              toastr.error('Delete failed: ' + (resp && resp.message ? resp.message : 'Unknown'));
              console.error('Delete error:', resp);
            }
          }).fail(function(xhr, status, err){
            toastr.error('Server error while deleting');
            console.error('AJAX fail:', xhr.status, xhr.responseText, status, err);
          }).always(function(){
            $confirm.prop('disabled', false).text('Delete');
          });
        });
      });
      </script>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
$(function(){
  // initialize DataTable after jQuery is loaded
  $('#uploadsTable').DataTable();

  $(document).on('click', '.delete-upload', function(){
    var $btn = $(this);
    var file = $btn.data('file');
    var $row = $btn.closest('tr');
    // store target row on modal for later removal
    $('#confirmDeleteModal').data('targetRow', $row).data('targetFile', file).modal('show');
  });

  $('#confirmDeleteBtn').on('click', function(){
    var $modal = $('#confirmDeleteModal');
    var file = $modal.data('targetFile');
    var $row = $modal.data('targetRow');
    var $confirm = $(this);
    if(!file){ toastr.error('No file selected'); return; }
    // close modal and disable button while request runs
    $modal.modal('hide');
    $confirm.prop('disabled', true).text('Deleting...');

    $.ajax({
      url: 'ajax/upload_file.php',
      method: 'POST',
      data: { action: 'delete', file: file },
      dataType: 'json'
    }).done(function(resp){
      console.log('Delete response:', resp);
      if(resp && resp.success){
        toastr.success('File deleted');
        // remove row from table gracefully
        if($row && $row.length){
          $row.fadeOut(250, function(){
            var table = $('#uploadsTable').DataTable();
            // if DataTable exists remove row via API
            if($.fn.dataTable.isDataTable('#uploadsTable')){
              table.row($(this)).remove().draw(false);
            } else { $(this).remove(); }
          });
        } else {
          setTimeout(function(){ location.reload(); }, 500);
        }
      } else {
        toastr.error('Delete failed: ' + (resp && resp.message ? resp.message : 'Unknown'));
        console.error('Delete error:', resp);
      }
    }).fail(function(xhr, status, err){
      toastr.error('Server error while deleting');
      console.error('AJAX fail:', xhr.status, xhr.responseText, status, err);
    }).always(function(){
      $confirm.prop('disabled', false).text('Delete');
    });
  });
});
</script>
