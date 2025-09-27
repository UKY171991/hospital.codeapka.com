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
              <div class="col-md-9 text-right">
                <button class="btn btn-success" id="addDoctorBtn">Add New Doctor</button>
              </div>
            </div>
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


<!-- Edit Doctor Modal -->
<div class="modal fade" id="editDoctorModal" tabindex="-1" role="dialog" aria-labelledby="editDoctorModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editDoctorModalLabel">Edit Doctor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editDoctorForm">
          <input type="hidden" id="editDoctorId" name="id">
          <div class="form-group">
            <label for="editDoctorName">Name</label>
            <input type="text" class="form-control" id="editDoctorName" name="name" required>
          </div>
          <div class="form-group">
            <label for="editDoctorHospital">Hospital</label>
            <input type="text" class="form-control" id="editDoctorHospital" name="hospital">
          </div>
          <div class="form-group">
            <label for="editDoctorContactNo">Contact No</label>
            <input type="text" class="form-control" id="editDoctorContactNo" name="contact_no">
          </div>
          <div class="form-group">
            <label for="editDoctorPercent">Percent</label>
            <input type="number" step="0.01" class="form-control" id="editDoctorPercent" name="percent">
          </div>
          <div class="form-group">
            <label for="editDoctorAddress">Address</label>
            <textarea class="form-control" id="editDoctorAddress" name="address"></textarea>
          </div>
          <div class="form-group">
            <label for="editDoctorAddedBy">Added By</label>
            <select class="form-control" id="editDoctorAddedBy" name="added_by">
              <!-- Options will be loaded via JavaScript -->
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveDoctorChanges">Save changes</button>
      </div>
    </div>
  </div>
</div>

<!-- Add Doctor Modal -->
<div class="modal fade" id="addDoctorModal" tabindex="-1" role="dialog" aria-labelledby="addDoctorModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDoctorModalLabel">Add New Doctor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addDoctorForm">
          <div class="form-group">
            <label for="addDoctorName">Name</label>
            <input type="text" class="form-control" id="addDoctorName" name="name" required>
          </div>
          <div class="form-group">
            <label for="addDoctorHospital">Hospital</label>
            <input type="text" class="form-control" id="addDoctorHospital" name="hospital">
          </div>
          <div class="form-group">
            <label for="addDoctorContactNo">Contact No</label>
            <input type="text" class="form-control" id="addDoctorContactNo" name="contact_no">
          </div>
          <div class="form-group">
            <label for="addDoctorPercent">Percent</label>
            <input type="number" step="0.01" class="form-control" id="addDoctorPercent" name="percent">
          </div>
          <div class="form-group">
            <label for="addDoctorAddress">Address</label>
            <textarea class="form-control" id="addDoctorAddress" name="address"></textarea>
          </div>
          <div class="form-group">
            <label for="addDoctorAddedBy">Added By</label>
            <select class="form-control" id="addDoctorAddedBy" name="added_by">
              <!-- Options will be loaded via JavaScript -->
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="createDoctor">Add Doctor</button>
      </div>
    </div>
  </div>
</div>

<?php include_once 'inc/footer.php'; ?>


<!-- Page-specific CSS/JS -->
<link rel="stylesheet" href="assets/css/doctors.css">

<script>
  $(function(){
    window.doctorTable = $('#doctorTable').DataTable({
      processing: true,
      responsive: true,
      ajax: {
        url: 'ajax/doctor_api.php',
        data: function(d){
          d.action = 'list';
          var addedBy = $('#filterAddedBy').val();
          if (addedBy) d.added_by = addedBy;
        },
        dataSrc: 'data'
      },
      columns: [
        { data: 'id' },
        { data: 'name', className: 'text-truncate', defaultContent: '' },
        { data: 'hospital', className: 'text-truncate', defaultContent: '' },
        { data: 'contact_no', defaultContent: '' },
        { data: 'percent', className: 'text-right', render: function(d){ return (d===null||d===undefined)? '': d; }, createdCell: function(td,cellData,rowData,row,col){ $(td).css('padding-right','12px'); } },
        { data: 'added_by_username', className: 'text-truncate', defaultContent: '' },
        { data: 'created_at' },
        { data: null, orderable: false, searchable: false, render: function(data,type,row){
          return '<div class="btn-group btn-group-sm action-buttons" role="group" aria-label="Actions" style="white-space:nowrap;">'
             + '<button class="btn btn-primary btn-action view-btn mr-1" data-id="'+row.id+'">View</button>'
             + '<button class="btn btn-info btn-action edit-btn mr-1" data-id="'+row.id+'">Edit</button>'
             + '<button class="btn btn-danger del-btn" data-id="'+row.id+'">Delete</button>'
             + '</div>';
        } }
      ],
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
  });
</script>

<script src="assets/js/doctors.js"></script>
