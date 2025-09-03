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
                    <th>Qualification</th>
                    <th>Specialization</th>
                    <th>Hospital</th>
                    <th>Contact No</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Registration No</th>
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
        <div class="form-group"><label>Qualification</label><input class="form-control" name="qualification" id="qualification"></div>
        <div class="form-group"><label>Specialization</label><input class="form-control" name="specialization" id="specialization"></div>
  <div class="form-group"><label>Hospital</label><input class="form-control" name="hospital" id="hospital"></div>
  <div class="form-group"><label>Contact No</label><input class="form-control" name="contact_no" id="contact_no"></div>
        <div class="form-group"><label>Phone</label><input class="form-control" name="phone" id="phone"></div>
        <div class="form-group"><label>Email</label><input class="form-control" name="email" id="email" type="email"></div>
        <div class="form-group"><label>Address</label><textarea class="form-control" name="address" id="address"></textarea></div>
        <div class="form-group"><label>Registration No</label><input class="form-control" name="registration_no" id="registration_no"></div>
  <div class="form-group"><label>Percent</label><input class="form-control" name="percent" id="percent" type="number" step="0.01"></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save</button></div>
      </form>
    </div>
  </div>
</div>

<?php include_once 'inc/footer.php'; ?>

<script>
function loadDoctors(){
  $.get('ajax/doctor_api.php',{action:'list'},function(resp){
    if(resp.success){
   var rows = resp.data; var t='';
   rows.forEach(function(r){
     t += '<tr>'+
       '<td>'+r.id+'</td>'+
       '<td>'+ (r.name||'') +'</td>'+
       '<td>'+ (r.qualification||'') +'</td>'+
       '<td>'+ (r.specialization||'') +'</td>'+
       '<td>'+ (r.hospital||'') +'</td>'+
       '<td>'+ (r.contact_no||'') +'</td>'+
       '<td>'+ (r.phone||'') +'</td>'+
       '<td>'+ (r.email||'') +'</td>'+
       '<td>'+ (r.registration_no||'') +'</td>'+
       '<td>'+ (r.percent!==null && r.percent!==undefined ? r.percent : '') +'</td>'+
       '<td>'+ (r.added_by_username||'') +'</td>'+
       '<td>'+ (r.created_at||'') +'</td>'+
       '<td><button class="btn btn-sm btn-info edit-btn" data-id="'+r.id+'">Edit</button> '+
         '<button class="btn btn-sm btn-danger del-btn" data-id="'+r.id+'">Delete</button></td>'+
       '</tr>';
   });
      $('#doctorTable tbody').html(t);
    } else { toastr.error('Failed to load'); }
  },'json');
}

$(function(){
  loadDoctors();
  $('#addBtn').click(function(){ $('#doctorForm')[0].reset(); $('#doctorId').val(''); $('#doctorModal').modal('show'); });
  $('#doctorTable').on('click','.edit-btn', function(){
    var id=$(this).data('id');
    $.get('ajax/doctor_api.php',{action:'get',id:id}, function(r){
      if(r.success){
        var d=r.data;
        $('#doctorId').val(d.id);
        $('#name').val(d.name);
        $('#qualification').val(d.qualification);
        $('#specialization').val(d.specialization);
        $('#hospital').val(d.hospital);
        $('#contact_no').val(d.contact_no);
        $('#phone').val(d.phone);
        $('#email').val(d.email);
        $('#address').val(d.address);
        $('#registration_no').val(d.registration_no);
        $('#percent').val(d.percent);
        $('#doctorModal').modal('show');
      }
    },'json');
  });
  $('#doctorForm').submit(function(e){ e.preventDefault(); $.post('ajax/doctor_api.php', $(this).serialize() + '&action=save', function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#doctorModal').modal('hide'); loadDoctors(); } else toastr.error('Save failed'); }, 'json'); });
  $('#doctorTable').on('click','.del-btn', function(){ if(!confirm('Delete doctor?')) return; var id=$(this).data('id'); $.post('ajax/doctor_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadDoctors(); } else toastr.error('Delete failed'); }, 'json'); });
});
</script>
