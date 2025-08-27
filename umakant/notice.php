<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Notices</h1></div>
                <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Notices</li></ol></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Notice Management</h3>
                            <div class="card-tools">
                                <button class="btn btn-primary" id="addNoticeBtn"><i class="fas fa-plus"></i> Add Notice</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="noticesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Active</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Notice Modal -->
<div class="modal fade" id="noticeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="noticeModalLabel">Add Notice</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <form id="noticeForm">
          <input type="hidden" name="id" id="noticeId">
          <div class="form-row">
            <div class="form-group col-md-6"><label>Title</label><input class="form-control" name="title" id="noticeTitle" required></div>
            <div class="form-group col-md-3"><label>Start Date</label><input class="form-control" name="start_date" id="noticeStart" type="datetime-local"></div>
            <div class="form-group col-md-3"><label>End Date</label><input class="form-control" name="end_date" id="noticeEnd" type="datetime-local"></div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-3"><label>Active</label><select class="form-control" name="active" id="noticeActive"><option value="1">Yes</option><option value="0">No</option></select></div>
          </div>
          <div class="form-group"><label>Content</label><textarea class="form-control" name="content" id="noticeContent"></textarea></div>
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button id="saveNoticeBtn" class="btn btn-primary">Save Notice</button></div>
    </div>
  </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
function renderNotices(data){
  var t=''; data.forEach(function(n,idx){
    t += '<tr>'+
    '<td>'+(idx+1)+'</td>'+
    '<td>'+n.id+'</td>'+
    '<td>'+(n.title||'')+'</td>'+
    '<td>'+(n.start_date||'')+'</td>'+
    '<td>'+(n.end_date||'')+'</td>'+
    '<td>'+(n.active==1?'Yes':'No')+'</td>'+
  '<td>'+(n.added_by_username||'')+'</td>'+
  '<td><button class="btn btn-sm btn-info view-notice" data-id="'+n.id+'">View</button> '
     +'<button class="btn btn-sm btn-warning edit-notice" data-id="'+n.id+'">Edit</button> '
     +'<button class="btn btn-sm btn-danger delete-notice" data-id="'+n.id+'">Delete</button></td>'+
    '</tr>';
  }); $('#noticesTable tbody').html(t); initDataTable('#noticesTable');
}

function loadNotices(){ $.get('ajax/notice_api.php',{action:'list'}, function(resp){ if(resp.success) renderNotices(resp.data); else toastr.error(resp.message||'Failed'); },'json'); }

function openAddNoticeModal(){ $('#noticeForm')[0].reset(); $('#noticeId').val(''); $('#noticeModal').modal('show'); }

$(function(){
  loadNotices();

  // Add button opens empty, editable modal
  $('#addNoticeBtn').on('click', function(){
    $('#noticeForm')[0].reset();
    $('#noticeId').val('');
    $('#noticeForm').find('input,textarea,select').prop('disabled', false);
    $('#saveNoticeBtn').show();
    $('#noticeModalLabel').text('Add Notice');
    $('#noticeModal').modal('show');
  });

  // Edit handler: populate and make editable
  $(document).on('click','.edit-notice', function(){
    var id=$(this).data('id');
    $.get('ajax/notice_api.php',{action:'get',id:id}, function(resp){
      if(resp.success){ var n=resp.data;
        $('#noticeId').val(n.id); $('#noticeTitle').val(n.title); $('#noticeContent').val(n.content);
        $('#noticeStart').val(n.start_date? n.start_date.replace(' ', 'T') : '');
        $('#noticeEnd').val(n.end_date? n.end_date.replace(' ', 'T') : '');
        $('#noticeActive').val(n.active? '1':'0');
        $('#noticeForm').find('input,textarea,select').prop('disabled', false);
        $('#saveNoticeBtn').show();
        $('#noticeModalLabel').text('Edit Notice');
        $('#noticeModal').modal('show');
      } else toastr.error('Not found');
    },'json');
  });

  // Delete via AJAX
  $(document).on('click','.delete-notice', function(){ if(!confirm('Delete?')) return; var id=$(this).data('id'); $.post('ajax/notice_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadNotices(); } else toastr.error(resp.message||'Delete failed'); },'json'); });

  // Save handler
  $('#saveNoticeBtn').click(function(){ var data=$('#noticeForm').serialize()+'&action=save'; $.post('ajax/notice_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message); $('#noticeModal').modal('hide'); setTimeout(loadNotices,200); } else toastr.error(resp.message||'Save failed'); },'json'); });

  // View via delegated handler - shows modal readonly
  $(document).on('click','.view-notice', function(){ var id=$(this).data('id'); $.get('ajax/notice_api.php',{action:'get',id:id}, function(resp){ if(resp.success){ var n=resp.data; $('#noticeId').val(n.id); $('#noticeTitle').val(n.title); $('#noticeContent').val(n.content); $('#noticeStart').val(n.start_date? n.start_date.replace(' ', 'T') : ''); $('#noticeEnd').val(n.end_date? n.end_date.replace(' ', 'T') : ''); $('#noticeActive').val(n.active? '1':'0'); $('#noticeModal').modal('show'); $('#noticeForm').find('input,textarea,select').prop('disabled', true); $('#saveNoticeBtn').hide(); } else toastr.error('Not found'); },'json'); });

  // Reset modal on close
  $('#noticeModal').on('hidden.bs.modal', function(){ $('#noticeForm')[0].reset(); $('#noticeForm').find('input,textarea,select').prop('disabled', false); $('#saveNoticeBtn').show(); });
});

function viewNotice(id){ $.get('ajax/notice_api.php',{action:'get',id:id}, function(resp){ if(resp.success){ var n=resp.data; $('#noticeId').val(n.id); $('#noticeTitle').val(n.title); $('#noticeContent').val(n.content); $('#noticeStart').val(n.start_date? n.start_date.replace(' ', 'T') : ''); $('#noticeEnd').val(n.end_date? n.end_date.replace(' ', 'T') : ''); $('#noticeActive').val(n.active? '1':'0'); $('#noticeModal').modal('show'); $('#noticeForm').find('input,textarea,select').prop('disabled', true); $('#saveNoticeBtn').hide(); } else toastr.error('Not found'); },'json'); }
</script>
