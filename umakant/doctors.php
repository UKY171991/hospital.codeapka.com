<?php
require_once 'inc/connection.php';
@include_once 'inc/seed_admin.php';
include_once 'inc/header.php';
include_once 'inc/sidebar.php';
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0">Doctors</h1></div>
          <div class="col-sm-6 text-right"><button id="addBtn" class="btn btn-primary">Add Doctor</button></div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body">
            <table class="table table-bordered table-sm" id="doctorTable">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Hospital</th>
                    <th>Contact No</th>
                    <th>Percent</th>
                    <th>Added By</th>
                    <th>Created At</th>
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

<!-- Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="doctorForm">
      <div class="modal-header"><h5 class="modal-title">Doctor</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
  <div class="modal-body">
        <input type="hidden" name="id" id="doctorId">
        <div class="form-group"><label>Name</label><input class="form-control" name="name" id="name" required></div>
  <!-- Qualification and Specialization removed (not used by API) -->
  <div class="form-group"><label>Hospital</label><input class="form-control" name="hospital" id="hospital"></div>
  <div class="form-group"><label>Contact No</label><input class="form-control" name="contact_no" id="contact_no"></div>
  <!-- Phone removed (not used) -->
        <div class="form-group"><label>Address</label><textarea class="form-control" name="address" id="address"></textarea></div>
  <!-- Registration No removed (not used) -->
  <div class="form-group"><label>Percent</label><input class="form-control" name="percent" id="percent" type="number" step="0.01"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
      </form>
    </div>
  </div>
</div>

<?php include_once 'inc/footer.php'; ?>

<script>
$(function(){
  // Initialize DataTable with Ajax source and buttons
  var table = $('#doctorTable').DataTable({
    processing: true,
    responsive: true,
    ajax: {
      url: 'ajax/doctor_api.php',
      data: { action: 'list' },
      dataSrc: 'data'
    },
    columns: [
      { data: 'id' },
      { data: 'name' },
      { data: 'hospital' },
      { data: 'contact_no' },
      { data: 'percent', render: function(d){ return (d===null||d===undefined)? '': d; } },
      { data: 'added_by_username', defaultContent: '' },
      { data: 'created_at' },
    { data: null, orderable: false, searchable: false, render: function(data,type,row){
      return '<div class="btn-group btn-group-sm btn-group-vertical action-buttons" role="group" aria-label="Actions">'
         + '<button class="btn btn-primary btn-action view-btn" data-id="'+row.id+'">View</button>'
         + '<button class="btn btn-info edit-btn" data-id="'+row.id+'">Edit</button>'
         + '<button class="btn btn-danger del-btn" data-id="'+row.id+'">Delete</button>'
         + '</div>';
    } }
    ],
    dom: 'Bfrtip',
    buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
    pageLength: 25
  });

  // Open add modal
  $('#addBtn').click(function(){ $('#doctorForm')[0].reset(); $('#doctorId').val(''); $('#doctorModal').modal('show'); });

  // Edit handler (delegated)
  $('#doctorTable tbody').on('click', '.edit-btn', function(){
    var id = $(this).data('id');
    $.get('ajax/doctor_api.php', { action: 'get', id: id }, function(r){
      if(r.success){
        var d = r.data;
        $('#doctorId').val(d.id);
        $('#name').val(d.name);
        $('#hospital').val(d.hospital);
        $('#contact_no').val(d.contact_no);
        $('#address').val(d.address);
        $('#percent').val(d.percent);
        $('#doctorModal').modal('show');
      } else {
        toastr.error(r.message || 'Failed to load doctor');
      }
    }, 'json');
  });

  // View handler (defensive). Attach to document to survive DataTable redraws,
  // log activity for debugging, and gracefully fall back if `utils` isn't ready.
  $(document).on('click', '#doctorTable .view-btn', function(e){
    e.preventDefault();
    console.log('view-btn clicked', this);
    var id = $(this).data('id');
    if(!id){ console.warn('view-btn clicked but data-id missing'); return; }

    $.get('ajax/doctor_api.php', { action: 'get', id: id }, function(r){
      console.log('ajax/doctor_api.get response', r);
      if(r && r.success){
        var d = r.data;
        var html = '<dl class="row">' +
          '<dt class="col-sm-4">Name</dt><dd class="col-sm-8">'+(d.name||'')+'</dd>' +
          '<dt class="col-sm-4">Hospital</dt><dd class="col-sm-8">'+(d.hospital||'')+'</dd>' +
          '<dt class="col-sm-4">Contact No</dt><dd class="col-sm-8">'+(d.contact_no||'')+'</dd>' +
          '<dt class="col-sm-4">Percent</dt><dd class="col-sm-8">'+(d.percent||'')+'</dd>' +
          '<dt class="col-sm-4">Address</dt><dd class="col-sm-8">'+(d.address||'')+'</dd>' +
          '<dt class="col-sm-4">Added By</dt><dd class="col-sm-8">'+(d.added_by_username||'')+'</dd>' +
          '<dt class="col-sm-4">Created At</dt><dd class="col-sm-8">'+(d.created_at||'')+'</dd>' +
          '</dl>';

        // Prefer utils.showViewModal when available, otherwise show modal directly
        if(window.utils && typeof window.utils.showViewModal === 'function'){
          window.utils.showViewModal('Doctor Details', html);
        } else {
          console.warn('utils.showViewModal not available - using direct modal fallback');
          $('#globalViewModalLabel').text('Doctor Details');
          $('#globalViewModalBody').html(html);
          $('#globalViewModal').modal('show');
        }
      } else {
        console.error('Failed to load doctor:', r && r.message);
        toastr.error((r && r.message) || 'Failed to load');
      }
    }, 'json').fail(function(xhr){
      console.error('Ajax error fetching doctor', xhr);
      toastr.error('Server error');
    });
  });

  // Save handler
  $('#doctorForm').submit(function(e){
    e.preventDefault();
    $.post('ajax/doctor_api.php', $(this).serialize() + '&action=save', function(resp){
      if(resp.success){
        toastr.success(resp.message || 'Saved');
        $('#doctorModal').modal('hide');
        table.ajax.reload(null, false);
      } else {
        toastr.error(resp.message || 'Save failed');
      }
    }, 'json').fail(function(){ toastr.error('Server error'); });
  });

  // Delete handler
  $('#doctorTable tbody').on('click', '.del-btn', function(){
    if(!confirm('Delete doctor?')) return;
    var id = $(this).data('id');
    $.post('ajax/doctor_api.php', { action: 'delete', id: id }, function(resp){
      if(resp.success){
        toastr.success(resp.message || 'Deleted');
        table.ajax.reload(null, false);
      } else {
        toastr.error(resp.message || 'Delete failed');
      }
    }, 'json').fail(function(){ toastr.error('Server error'); });
  });

});
</script>
