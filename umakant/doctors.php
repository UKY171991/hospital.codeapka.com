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
          <div class="col-12"><h1 class="m-0">Doctors</h1></div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        <div class="card">
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-3">
                <label for="filterAddedBy">Filter: Added By</label>
                <select id="filterAddedBy" class="form-control">
                  <option value="">All</option>
                </select>
              </div>
            </div>
            <table class="table table-bordered table-sm" id="doctorTable">
                <thead>
                  <tr>
                    <th>View</th>
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
      data: function(d){
        // DataTables server-side params are in d; add action and optional added_by filter
        d.action = 'list';
        var addedBy = $('#filterAddedBy').val();
        if (addedBy) d.added_by = addedBy;
      },
      dataSrc: 'data'
    },
    columns: [
      // View - will be filled in drawCallback to handle paging and ordering
      { data: null, orderable: false, searchable: false, render: function(data,type,row){
        return '<div class="btn-group btn-group-sm action-buttons" role="group" aria-label="Actions" style="white-space:nowrap;">'
           + '<button class="btn btn-primary btn-action view-btn" data-id="'+row.id+'">View</button>'
           + '</div>';
      } },
      { data: 'id' },
      { data: 'name', className: 'text-truncate', defaultContent: '' },
      { data: 'hospital', className: 'text-truncate', defaultContent: '' },
      { data: 'contact_no', defaultContent: '' },
      { data: 'percent', className: 'text-right', render: function(d){ return (d===null||d===undefined)? '': d; }, createdCell: function(td,cellData,rowData,row,col){ $(td).css('padding-right','12px'); } },
      { data: 'added_by_username', className: 'text-truncate', defaultContent: '' },
      { data: 'created_at' },
      { data: null, orderable: false, searchable: false, render: function(data,type,row){
        return '<div class="btn-group btn-group-sm action-buttons" role="group" aria-label="Actions" style="white-space:nowrap;">'
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

  // Populate 'Added By' dropdown using users API
  function loadAddedByOptions(){
    $.get('ajax/user_api.php', { action: 'list' }, function(r){
      if(r && r.success){
        var select = $('#filterAddedBy');
        select.find('option:not(:first)').remove();
        r.data.forEach(function(u){
          // Use username or full_name for display
          var label = u.full_name || u.username || u.email || ('user-'+u.id);
          select.append($('<option>').val(u.id).text(label));
        });
      } else {
        console.warn('Failed to load users for filter', r);
      }
    }, 'json').fail(function(){ console.warn('Failed to load users for filter'); });
  }

  loadAddedByOptions();

  // Reload table when filter changes
  $('#filterAddedBy').change(function(){
    table.ajax.reload();
  });

  // View handler (delegated)
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
