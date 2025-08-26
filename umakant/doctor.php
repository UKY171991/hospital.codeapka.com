<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Doctors</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Doctors</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Doctor Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#doctorModal" onclick="openAddDoctorModal()">
                                    <i class="fas fa-plus"></i> Add Doctor
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="doctorsSearch" class="form-control" placeholder="Search doctors by name, specialization, email...">
                                        <div class="input-group-append">
                                            <button id="doctorsSearchClear" class="btn btn-outline-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto text-right">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="doctorsPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <table id="doctorsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Qualification</th>
                                        <th>Specialization</th>
                                        <th>Hospital</th>
                                        <th>Contact</th>
                                        <th>Percent</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Doctor Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="doctorModalLabel">Add Doctor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="doctorForm">
                    <input type="hidden" id="doctorId" name="id">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doctorName">Name *</label>
                                    <input type="text" class="form-control" id="doctorName" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="doctorQualification">Qualification</label>
                                    <input type="text" class="form-control" id="doctorQualification" name="qualification">
                                </div>
                                <div class="form-group">
                                    <label for="doctorHospital">Hospital</label>
                                    <input type="text" class="form-control" id="doctorHospital" name="hospital">
                                </div>
                                <div class="form-group">
                                    <label for="doctorPhone">Phone</label>
                                    <input type="text" class="form-control" id="doctorPhone" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doctorSpecialization">Specialization</label>
                                    <input type="text" class="form-control" id="doctorSpecialization" name="specialization">
                                </div>
                                <div class="form-group">
                                    <label for="doctorContact">Contact No</label>
                                    <input type="text" class="form-control" id="doctorContact" name="contact_no">
                                </div>
                                <div class="form-group">
                                    <label for="doctorPercent">Percent</label>
                                    <input type="number" step="0.01" class="form-control" id="doctorPercent" name="percent" value="0.00">
                                </div>
                                <div class="form-group">
                                    <label for="doctorEmail">Email</label>
                                    <input type="email" class="form-control" id="doctorEmail" name="email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="doctorRegistration">Registration No</label>
                                    <input type="text" class="form-control" id="doctorRegistration" name="registration_no">
                                </div>
                                <div class="form-group">
                                    <label for="doctorAddress">Address</label>
                                    <textarea class="form-control" id="doctorAddress" name="address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDoctorBtn">Save Doctor</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
function loadDoctors(){
    $.get('ajax/doctor_api.php',{action:'list'},function(resp){
    if(resp.success){ var t=''; resp.data.forEach(function(r){ t += '<tr>'+
            '<td>'+r.id+'</td>'+
            '<td>'+ (r.name||'') +'</td>'+
            '<td>'+ (r.qualification||'') +'</td>'+
            '<td>'+ (r.specialization||'') +'</td>'+
            '<td>'+ (r.hospital||'') +'</td>'+
            '<td>'+ (r.contact_no||r.phone||'') +'</td>'+
            '<td>'+ (r.percent||'') +'</td>'+
            '<td>'+ (r.email||'') +'</td>'+
            '<td><button class="btn btn-sm btn-info view-doctor" data-id="'+r.id+'">View</button> '+
                '<button class="btn btn-sm btn-warning edit-doctor" data-id="'+r.id+'">Edit</button> '+
                '<button class="btn btn-sm btn-danger delete-doctor" data-id="'+r.id+'">Delete</button></td>'+
            '</tr>'; }); $('#doctorsTable tbody').html(t);} else toastr.error('Failed to load'); },'json');
}

function openAddDoctorModal(){ $('#doctorForm')[0].reset(); $('#doctorId').val(''); $('#doctorModal').modal('show'); }

$(function(){
    loadDoctors();
    $('#saveDoctorBtn').click(function(){ var data=$('#doctorForm').serialize() + '&action=save'; $.post('ajax/doctor_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#doctorModal').modal('hide'); loadDoctors(); } else toastr.error(resp.message||'Save failed'); }, 'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); }); });

    $('#doctorsTable').on('click', '.edit-doctor', function(){ var id=$(this).data('id'); $.get('ajax/doctor_api.php',{action:'get',id:id}, function(resp){ if(resp.success){ var d=resp.data; $('#doctorId').val(d.id); $('#doctorName').val(d.name); $('#doctorQualification').val(d.qualification); $('#doctorSpecialization').val(d.specialization); $('#doctorHospital').val(d.hospital); $('#doctorContact').val(d.contact_no); $('#doctorPhone').val(d.phone); $('#doctorPercent').val(d.percent); $('#doctorEmail').val(d.email); $('#doctorRegistration').val(d.registration_no); $('#doctorAddress').val(d.address); $('#doctorModal').modal('show'); } else toastr.error('Doctor not found'); },'json'); });

    $('#doctorsTable').on('click', '.delete-doctor', function(){ if(!confirm('Delete doctor?')) return; var id=$(this).data('id'); $.post('ajax/doctor_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadDoctors(); } else toastr.error(resp.message||'Delete failed'); },'json'); });
});
</script>
