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
                  <td><?php echo htmlspecialchars($f['created_at'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($f['uploaded_by_username'] ?? ($f['uploaded_by'] ?? '')); ?></td>
                  <td>
                    <button class="btn btn-sm btn-danger delete-upload" data-file="<?php echo htmlspecialchars($f['file_name']); ?>">Delete</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
  
      <!-- Delete confirmation modal -->
      <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmDeleteLabel">Confirm Delete</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Are you sure you want to delete this uploaded file? This cannot be undone.
            </div>
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
          var file = $(this).data('file');
          $('#confirmDeleteModal').modal('show');
          $('#confirmDeleteBtn').off('click').on('click', function(){
            $.post('ajax/upload_file.php', { action: 'delete', file: file }, function(resp){
              if(resp.success){ location.reload(); } else { toastr.error(resp.message||'Delete failed'); }
            }, 'json').fail(function(){ toastr.error('Server error'); });
          });
        });
      });
      </script>
</div>

<?php require_once 'inc/footer.php'; ?>
<script>
$(function(){
  $('#uploadsTable').DataTable();

  $(document).on('click', '.delete-upload', function(){
    if(!confirm('Delete file?')) return;
    var file = $(this).data('file');
    $.post('ajax/upload_file.php', { action: 'delete', file: file }, function(resp){
      if(resp.success){ location.reload(); } else { toastr.error(resp.message||'Delete failed'); }
    }, 'json').fail(function(){ toastr.error('Server error'); });
  });
});
</script>
