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
                    <th>Sr No</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Hospital</th>
                    <th>Contact No</th>
                    <th>Percent</th>
                    <th>Added By</th>
                    <th>Created At</th>
                    <th style="white-space:nowrap; width:150px;">Actions</th>
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
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="doctorForm">
      <div class="modal-header">
        <h5 class="modal-title" id="doctorModalLabel">Add Doctor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="doctorId">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="name">Name</label>
            <input class="form-control" name="name" id="name" required>
          </div>

          <div class="form-group col-md-6">
            <label for="hospital">Hospital</label>
            <input class="form-control" name="hospital" id="hospital">
          </div>

          <div class="form-group col-md-6">
            <label for="contact_no">Contact No</label>
            <input class="form-control" name="contact_no" id="contact_no">
          </div>

          <div class="form-group col-md-6">
            <label for="percent">Percent</label>
            <div class="input-group">
              <input class="form-control text-right" name="percent" id="percent" type="number" step="0.01" min="0">
              <div class="input-group-append"><span class="input-group-text">%</span></div>
            </div>
          </div>

          <div class="form-group col-12">
            <label for="address">Address</label>
            <textarea class="form-control" name="address" id="address" rows="3"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
      </form>
    </div>
  </div>
</div>

<?php include_once 'inc/footer.php'; ?>

<!-- Page-specific CSS/JS -->
<link rel="stylesheet" href="umakant/assets/css/doctors.css">
<script src="umakant/assets/js/doctors.js"></script>

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
      // Sr No - will be filled in drawCallback to handle paging and ordering
      { data: null, orderable: false, searchable: false, defaultContent: '' },
  { data: 'id' },
  { data: 'name', className: 'text-truncate', defaultContent: '' },
  { data: 'hospital', className: 'text-truncate', defaultContent: '' },
  { data: 'contact_no', defaultContent: '' },
  { data: 'percent', className: 'text-right', render: function(d){ return (d===null||d===undefined)? '': d; }, createdCell: function(td,cellData,rowData,row,col){ $(td).css('padding-right','12px'); } },
  { data: 'added_by_username', className: 'text-truncate', defaultContent: '' },
  { data: 'created_at' },
    { data: null, orderable: false, searchable: false, render: function(data,type,row){
      return '<div class="btn-group btn-group-sm action-buttons" role="group" aria-label="Actions" style="white-space:nowrap;">'
         + '<button class="btn btn-primary btn-action view-btn" data-id="'+row.id+'" style="margin-right:6px;">View</button>'
         + '<button class="btn btn-info edit-btn" data-id="'+row.id+'" style="margin-right:6px;">Edit</button>'
         + '<button class="btn btn-danger del-btn" data-id="'+row.id+'">Delete</button>'
         + '</div>';
    } }
    ],
    // After table redrawn, populate Sr No column correctly (1..n per current ordering/page)
    drawCallback: function(settings){
      var api = this.api();
      var start = api.page.info().start;
      api.column(0, {page:'current'} ).nodes().each(function(cell, i){
        cell.innerHTML = start + i + 1;
      });
    },
    dom: 'Bfrtip',
    buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
    pageLength: 25
  });

  // Open add modal
  $('#addBtn').click(function(){
    $('#doctorForm')[0].reset();
    $('#doctorId').val('');
    $('#doctorModalLabel').text('Add Doctor');
    $('#doctorModal').modal('show');
  });

  // Edit handler (delegated)
  $('#doctorTable tbody').on('click', '.edit-btn', function(){
    var id = $(this).data('id');
    $.get('ajax/doctor_api.php', { action: 'get', id: id }, function(r){
      if(r.success){
        var d = r.data;
        $('#doctorId').val(d.id);
        $('#doctorModalLabel').text('Edit Doctor');
        $('#name').val(d.name);
        $('#hospital').val(d.hospital);
        $('#contact_no').val(d.contact_no);
        $('#address').val(d.address);
        // format percent to two decimals when present
        $('#percent').val((d.percent||'') === '' ? '' : parseFloat(d.percent).toFixed(2));
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
        // Build a cleaner two-column Bootstrap layout for details with truncation tooltips
        var fields = [
          ['Name', d.name],
          ['Hospital', d.hospital],
          ['Contact No', d.contact_no],
          ['Percent', d.percent],
          ['Address', d.address],
          ['Added By', d.added_by_username],
          ['Created At', d.created_at]
        ];

        var html = '<div class="container-fluid">';
        html += '<div class="row">';
        html += '<div class="col-12">';
        html += '<table class="table table-borderless table-sm">';
        html += '<tbody>';
        fields.forEach(function(f){
          var label = f[0];
          var val = f[1] || '';
          var safe = $('<div>').text(val).html(); // escape
          html += '<tr>' +
                    '<th style="width:25%; text-align:left; vertical-align:middle;">'+label+'</th>' +
                    '<td style="width:75%;">' +
                      '<div class="text-truncate" style="max-width:100%;" title="'+ safe +'">'+ safe +'</div>' +
                    '</td>' +
                  '</tr>';
        });
        html += '</tbody></table></div></div></div>';

        // Prefer utils.showViewModal when available, otherwise show modal directly
        if(window.utils && typeof window.utils.showViewModal === 'function'){
          window.utils.showViewModal('Doctor Details', html);
        } else {
          console.warn('utils.showViewModal not available - using direct modal fallback');
          $('#globalViewModalLabel').text('Doctor Details');
          $('#globalViewModalBody').html(html);
          $('#globalViewModal').modal('show');
          // initialize tooltips inside modal for truncated fields
          $('#globalViewModal [title]').tooltip({ container: '#globalViewModal' });
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
