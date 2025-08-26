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
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="patientsSearch" class="form-control" placeholder="Search patients by name, mobile or UHID...">
                                        <div class="input-group-append">
                                            <button id="patientsSearchClear" class="btn btn-outline-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto text-right">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="patientsPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <table id="patientsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>UHID</th>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
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
            '<td><button class="btn btn-sm btn-info view-patient" data-id="'+p.id+'" onclick="viewPatient('+p.id+')">View</button> '+
                '<button class="btn btn-sm btn-warning edit-patient" data-id="'+p.id+'">Edit</button> '+
                '<button class="btn btn-sm btn-danger delete-patient" data-id="'+p.id+'">Delete</button></td>'+
            '</tr>'; }); $('#patientsTable tbody').html(t);
        } else toastr.error('Failed to load patients');
    },'json');
}

// fallback global function used by inline onclick on View buttons
function viewPatient(id){
    try{
        console.debug('viewPatient() called', id);
        $.get('ajax/patient_api.php',{action:'get',id:id}, function(resp){
            if(resp.success){
                var d=resp.data;
                $('#patientId').val(d.id);
                $('#patientName').val(d.name);
                $('#patientMobile').val(d.mobile);
                $('#patientFatherHusband').val(d.father_husband);
                $('#patientAddress').val(d.address);
                $('#patientSex').val(d.sex);
                $('#patientAge').val(d.age);
                $('#patientAgeUnit').val(d.age_unit||'Years');
                $('#patientUHID').val(d.uhid);
                $('#patientModalLabel').text('View Patient');
                $('#patientForm').find('input,textarea,select').prop('disabled', true);
                $('#savePatientBtn').hide();
                $('#patientModal').modal('show');
            } else toastr.error('Patient not found');
        },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
    }catch(err){ console.error('viewPatient error', err); toastr.error('Error: '+(err.message||err)); }
}

function openAddPatientModal(){
    $('#patientForm')[0].reset();
    $('#patientId').val('');
    // ensure modal is editable for add
    $('#patientModalLabel').text('Add Patient');
    $('#patientForm').find('input,textarea,select').prop('disabled', false);
    $('#savePatientBtn').show();
    $('#patientModal').modal('show');
}

$(function(){
    loadPatients();

    // search/filter UI
    $('#patientsSearch').on('input', function(){ var q=$(this).val().toLowerCase().trim(); if(!q){ $('#patientsTable tbody tr').show(); return; } $('#patientsTable tbody tr').each(function(){ var row=$(this); var text=row.text().toLowerCase(); row.toggle(text.indexOf(q)!==-1); }); });
    $('#patientsSearchClear').click(function(e){ e.preventDefault(); $('#patientsSearch').val(''); $('#patientsSearch').trigger('input'); });

    $('#savePatientBtn').click(function(){ var data = $('#patientForm').serialize() + '&action=save'; $.post('ajax/patient_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#patientModal').modal('hide'); loadPatients(); } else toastr.error(resp.message||'Save failed'); },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); }); });

    // edit - use document delegation to be robust against dynamic table rebuilds
    $(document).on('click', '.edit-patient', function(){
        try{
            console.debug('edit-patient clicked', this, $(this).data('id'));
            var id=$(this).data('id');
            $.get('ajax/patient_api.php',{action:'get',id:id}, function(resp){
                if(resp.success){
                    var d=resp.data;
                    $('#patientId').val(d.id);
                    $('#patientName').val(d.name);
                    $('#patientMobile').val(d.mobile);
                    $('#patientFatherHusband').val(d.father_husband);
                    $('#patientAddress').val(d.address);
                    $('#patientSex').val(d.sex);
                    $('#patientAge').val(d.age);
                    $('#patientAgeUnit').val(d.age_unit||'Years');
                    $('#patientUHID').val(d.uhid);
                    // make editable for edit
                    $('#patientModalLabel').text('Edit Patient');
                    $('#patientForm').find('input,textarea,select').prop('disabled', false);
                    $('#savePatientBtn').show();
                    $('#patientModal').modal('show');
                } else toastr.error('Patient not found');
            },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('edit handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // view handler - opens modal in read-only mode; attach to document for robustness
    $(document).on('click', '.view-patient', function(){
        try{
            console.debug('view-patient clicked', this, $(this).data('id'));
            var id=$(this).data('id');
            $.get('ajax/patient_api.php',{action:'get',id:id}, function(resp){
                if(resp.success){
                    var d=resp.data;
                    $('#patientId').val(d.id);
                    $('#patientName').val(d.name);
                    $('#patientMobile').val(d.mobile);
                    $('#patientFatherHusband').val(d.father_husband);
                    $('#patientAddress').val(d.address);
                    $('#patientSex').val(d.sex);
                    $('#patientAge').val(d.age);
                    $('#patientAgeUnit').val(d.age_unit||'Years');
                    $('#patientUHID').val(d.uhid);
                    // make read-only for view
                    $('#patientModalLabel').text('View Patient');
                    $('#patientForm').find('input,textarea,select').prop('disabled', true);
                    $('#savePatientBtn').hide();
                    $('#patientModal').modal('show');
                } else toastr.error('Patient not found');
            },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('view handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    $(document).on('click', '.delete-patient', function(){
        try{
            if(!confirm('Delete patient?')) return;
            var id=$(this).data('id');
            $.post('ajax/patient_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadPatients(); } else toastr.error(resp.message||'Delete failed'); },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('delete handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // restore modal to editable default when closed
    $('#patientModal').on('hidden.bs.modal', function(){
        $('#patientForm').find('input,textarea,select').prop('disabled', false);
        $('#savePatientBtn').show();
        $('#patientModalLabel').text('Add Patient');
    });
});
</script>