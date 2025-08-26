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
                    <h1>Test Entries</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Test Entries</li>
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
                            <h3 class="card-title">Test Entry Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#entryModal" onclick="openAddEntryModal()">
                                    <i class="fas fa-plus"></i> Add Entry
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
                                        <input id="entriesSearch" class="form-control" placeholder="Search entries by patient, doctor, or test...">
                                        <div class="input-group-append">
                                            <button id="entriesSearchClear" class="btn btn-outline-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto text-right">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="entriesPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <table id="entriesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Test</th>
                                        <th>Entry Date</th>
                                        <th>Result</th>
                                        <th>Unit</th>
                                        <th>Status</th>
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

<!-- Entry Modal -->
<div class="modal fade" id="entryModal" tabindex="-1" role="dialog" aria-labelledby="entryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entryModalLabel">Add Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="entryForm">
                    <input type="hidden" id="entryId" name="id">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="entryPatientId">Patient *</label>
                                    <select class="form-control" id="entryPatientId" name="patient_id" required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="entryDoctorId">Doctor *</label>
                                    <select class="form-control" id="entryDoctorId" name="doctor_id" required>
                                        <option value="">Select Doctor</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="entryTestId">Test *</label>
                                    <select class="form-control" id="entryTestId" name="test_id" required>
                                        <option value="">Select Test</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="entryEntryDate">Entry Date *</label>
                                    <input type="datetime-local" class="form-control" id="entryEntryDate" name="entry_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="entryResultValue">Result Value</label>
                                    <input type="text" class="form-control" id="entryResultValue" name="result_value">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="entryUnit">Unit</label>
                                    <input type="text" class="form-control" id="entryUnit" name="unit">
                                </div>
                                <div class="form-group">
                                    <label for="entryRemarks">Remarks</label>
                                    <textarea class="form-control" id="entryRemarks" name="remarks" rows="4"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="entryStatus">Status *</label>
                                    <select class="form-control" id="entryStatus" name="status" required>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEntryBtn">Save Entry</button>
            </div>
        </div>
    </div>
 </div>

<?php require_once 'inc/footer.php'; ?>

<script>
function loadDropdownsForEntry(){
    $('#entryPatientId, #entryDoctorId, #entryTestId').empty().append('<option value="">Select</option>');
    $.get('ajax/patient_api.php',{action:'list',ajax:1},function(r){ if(r.success){ var s=''; r.data.forEach(function(p){ s += '<option value="'+p.id+'">'+(p.name||'')+'</option>'; }); $('#entryPatientId').append(s);} },'json');
    $.get('ajax/doctor_api.php',{action:'list',ajax:1},function(r){ if(r.success){ var s=''; r.data.forEach(function(p){ s += '<option value="'+p.id+'">'+(p.name||'')+'</option>'; }); $('#entryDoctorId').append(s);} },'json');
    $.get('ajax/test_api.php',{action:'list',ajax:1},function(r){ if(r.success){ var s=''; r.data.forEach(function(p){ s += '<option value="'+p.id+'">'+(p.name||'')+'</option>'; }); $('#entryTestId').append(s);} },'json');
}

function loadEntries(){
    $.get('ajax/entry_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success){
            var t=''; resp.data.forEach(function(e){
                t += '<tr>'+
                    '<td>'+e.id+'</td>'+
                    '<td>'+ (e.patient_name||'') +'</td>'+
                    '<td>'+ (e.doctor_name||'') +'</td>'+
                    '<td>'+ (e.test_name||'') +'</td>'+
                    '<td>'+ (e.entry_date||'') +'</td>'+
                    '<td>'+ (e.result_value||'') +'</td>'+
                    '<td>'+ (e.unit||'') +'</td>'+
                    '<td>'+ (e.status||'') +'</td>'+
                    '<td><button class="btn btn-sm btn-info view-entry" data-id="'+e.id+'" onclick="viewEntry('+e.id+')">View</button> '+
                        '<button class="btn btn-sm btn-warning edit-entry" data-id="'+e.id+'">Edit</button> '+
                        '<button class="btn btn-sm btn-danger delete-entry" data-id="'+e.id+'">Delete</button></td>'+
                    '</tr>';
            });
            $('#entriesTable tbody').html(t);
            applyEntriesFilters();
        } else toastr.error('Failed to load entries');
    },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
}

function openAddEntryModal(){
    $('#entryForm')[0].reset(); $('#entryId').val(''); $('#entryForm').find('input,textarea,select').prop('disabled', false); $('#saveEntryBtn').show(); $('#entryModalLabel').text('Add Entry'); $('#entryModal').modal('show');
}

// client-side filtering + per-page
function applyEntriesFilters(){
    var q = $('#entriesSearch').val().toLowerCase().trim();
    var per = parseInt($('#entriesPerPage').val()||10,10);
    var shown = 0;
    $('#entriesTable tbody tr').each(function(){
        var row = $(this);
        var text = row.text().toLowerCase();
        var matches = !q || text.indexOf(q) !== -1;
        if(matches && shown < per){ row.show(); shown++; } else { row.toggle(matches && shown < per); }
    });
}

$(function(){
    loadDropdownsForEntry();
    loadEntries();

    $('#saveEntryBtn').click(function(){
        var data = $('#entryForm').serialize() + '&action=save&ajax=1';
        $.post('ajax/entry_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#entryModal').modal('hide'); loadEntries(); } else toastr.error(resp.message||'Save failed'); },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
    });

    // client-side search
    $('#entriesSearch').on('input', function(){ applyEntriesFilters(); });
    $('#entriesSearchClear').click(function(e){ e.preventDefault(); $('#entriesSearch').val(''); applyEntriesFilters(); });
    $('#entriesPerPage').change(function(){ applyEntriesFilters(); });

    // delegated edit handler
    $(document).on('click', '.edit-entry', function(){
        try{
            var id = $(this).data('id');
            $.get('ajax/entry_api.php',{action:'get',id:id,ajax:1}, function(r){
                if(r.success){ var d=r.data;
                    $('#entryId').val(d.id);
                    $('#entryPatientId').val(d.patient_id);
                    $('#entryDoctorId').val(d.doctor_id);
                    $('#entryTestId').val(d.test_id);
                    // convert server 'YYYY-MM-DD HH:MM:SS' to datetime-local 'YYYY-MM-DDTHH:MM'
                    function toLocal(v){ if(!v) return ''; return v.replace(' ','T').slice(0,16); }
                    $('#entryEntryDate').val(toLocal(d.entry_date||''));
                    $('#entryResultValue').val(d.result_value);
                    $('#entryUnit').val(d.unit);
                    $('#entryRemarks').val(d.remarks);
                    $('#entryStatus').val(d.status);
                    $('#entryForm').find('input,textarea,select').prop('disabled', false);
                    $('#saveEntryBtn').show();
                    $('#entryModalLabel').text('Edit Entry');
                    $('#entryModal').modal('show');
                } else toastr.error('Entry not found');
            },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('edit-entry handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // delegated delete handler
    $(document).on('click', '.delete-entry', function(){
        try{
            if(!confirm('Delete entry?')) return;
            var id=$(this).data('id');
            $.post('ajax/entry_api.php',{action:'delete',id:id,ajax:1}, function(resp){ if(resp.success){ toastr.success(resp.message); loadEntries(); } else toastr.error(resp.message||'Delete failed'); },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('delete-entry handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // restore modal state on hide
    $('#entryModal').on('hidden.bs.modal', function(){
        $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', false);
        $('#saveEntryBtn').show();
        $('#entryModalLabel').text('Add Entry');
    });
});

// global view fallback
window.viewEntry = function(id){
    try{
        $.get('ajax/entry_api.php',{action:'get',id:id,ajax:1}, function(r){
            if(r.success){ var d=r.data;
                $('#entryId').val(d.id);
                $('#entryPatientId').val(d.patient_id);
                $('#entryDoctorId').val(d.doctor_id);
                $('#entryTestId').val(d.test_id);
                function toLocal(v){ if(!v) return ''; return v.replace(' ','T').slice(0,16); }
                $('#entryEntryDate').val(toLocal(d.entry_date||''));
                $('#entryResultValue').val(d.result_value);
                $('#entryUnit').val(d.unit);
                $('#entryRemarks').val(d.remarks);
                $('#entryStatus').val(d.status);
                $('#entryModalLabel').text('View Entry');
                $('#entryForm').find('input,textarea,select').prop('disabled', true);
                $('#saveEntryBtn').hide();
                $('#entryModal').modal('show');
            } else toastr.error('Entry not found');
        },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
    }catch(err){ console.error('viewEntry error', err); toastr.error('Error: '+(err.message||err)); }
}

</script>