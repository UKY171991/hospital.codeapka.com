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
                    <h1>Patients</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Patients</li>
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
                            <h3 class="card-title">Patient Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#patientModal" onclick="openAddPatientModal()">
                                    <i class="fas fa-plus"></i> Add Patient
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
                            <table id="patientsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>UHID</th>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
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

<!-- Patient Modal -->
<div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="patientModalLabel">Add Patient</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="patientForm">
                    <input type="hidden" id="patientId" name="id">
                    <div class="form-group">
                        <label for="patientName">Name *</label>
                        <input type="text" class="form-control" id="patientName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="patientMobile">Mobile *</label>
                        <input type="text" class="form-control" id="patientMobile" name="mobile" required>
                    </div>
                    <div class="form-group">
                        <label for="patientFatherHusband">Father/Husband Name</label>
                        <input type="text" class="form-control" id="patientFatherHusband" name="father_husband">
                    </div>
                    <div class="form-group">
                        <label for="patientAddress">Address</label>
                        <textarea class="form-control" id="patientAddress" name="address" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="patientSex">Gender</label>
                        <select class="form-control" id="patientSex" name="sex">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="patientAge">Age</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control" id="patientAge" name="age" min="0">
                            </div>
                            <div class="col-6">
                                <select class="form-control" id="patientAgeUnit" name="age_unit">
                                    <option value="Years">Years</option>
                                    <option value="Months">Months</option>
                                    <option value="Days">Days</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="patientUHID">UHID</label>
                        <input type="text" class="form-control" id="patientUHID" name="uhid">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="savePatientBtn">Save Patient</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
function loadPatients(){
    $.get('ajax/patient_api.php',{action:'list'},function(resp){
        if(resp.success){ var t=''; resp.data.forEach(function(p){ t += '<tr>'+
                    '<td>'+p.id+'</td>'+
                    '<td>'+ (p.uhid||'') +'</td>'+
                    '<td>'+ (p.name||'') +'</td>'+
                    '<td>'+ (p.age||'') +'</td>'+
                    '<td>'+ (p.gender||'') +'</td>'+
                    '<td>'+ (p.phone||'') +'</td>'+
                    '<td>'+ (p.email||'') +'</td>'+
                    '<td><button class="btn btn-sm btn-info view-patient" data-id="'+p.id+'">View</button> '+
                            '<button class="btn btn-sm btn-warning edit-patient" data-id="'+p.id+'">Edit</button> '+
                            '<button class="btn btn-sm btn-danger delete-patient" data-id="'+p.id+'">Delete</button></td>'+
                    '</tr>'; }); $('#patientsTable tbody').html(t);
        } else toastr.error('Failed to load patients');
    },'json');
}

function openAddPatientModal(){ $('#patientForm')[0].reset(); $('#patientId').val(''); $('#patientModal').modal('show'); }

$(function(){
    loadPatients();
    $('#savePatientBtn').click(function(){ var data = $('#patientForm').serialize() + '&action=save'; $.post('ajax/patient_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#patientModal').modal('hide'); loadPatients(); } else toastr.error(resp.message||'Save failed'); },'json'); });

    $('#patientsTable').on('click', '.edit-patient', function(){ var id=$(this).data('id'); $.get('ajax/patient_api.php',{action:'get',id:id}, function(resp){ if(resp.success){ var d=resp.data; $('#patientId').val(d.id); $('#patientName').val(d.name); $('#patientMobile').val(d.mobile); $('#patientFatherHusband').val(d.father_husband); $('#patientAddress').val(d.address); $('#patientSex').val(d.sex); $('#patientAge').val(d.age); $('#patientAgeUnit').val(d.age_unit||'Years'); $('#patientUHID').val(d.uhid); $('#patientModal').modal('show'); } else toastr.error('Patient not found'); },'json'); });

    $('#patientsTable').on('click', '.delete-patient', function(){ if(!confirm('Delete patient?')) return; var id=$(this).data('id'); $.post('ajax/patient_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadPatients(); } else toastr.error(resp.message||'Delete failed'); },'json'); });
});
</script>