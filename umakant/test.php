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
                    <h1>Tests</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Tests</li>
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
                            <h3 class="card-title">Test Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#testModal" onclick="openAddTestModal()">
                                    <i class="fas fa-plus"></i> Add Test
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
                            <!-- Using DataTables for search, sorting and paging -->
                            <table id="testsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Added By</th>
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

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="testModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testModalLabel">Add Test</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="testForm">
                    <input type="hidden" id="testId" name="id">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="testName">Name *</label>
                                    <input type="text" class="form-control" id="testName" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="testDescription">Description</label>
                                    <textarea class="form-control" id="testDescription" name="description" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="testCategoryId">Category *</label>
                                    <select class="form-control" id="testCategoryId" name="category_id" required>
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="testPrice">Price *</label>
                                    <input type="number" class="form-control" id="testPrice" name="price" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label for="testUnit">Unit</label>
                                    <input type="text" class="form-control" id="testUnit" name="unit">
                                </div>
                                <!-- specimen removed -->
                            </div>
                            <div class="col-md-6">
                                <!-- Default Result and Reference Range removed -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="testMin">Min Value</label>
                                        <input type="number" class="form-control" id="testMin" name="min" step="0.01">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="testMax">Max Value</label>
                                        <input type="number" class="form-control" id="testMax" name="max" step="0.01">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="testSubHeading">Sub Heading</label>
                                    <select class="form-control" id="testSubHeading" name="sub_heading">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="testCode">Test Code</label>
                                    <!-- removed -->
                                </div>
                                <!-- method removed -->
                                <div class="form-group">
                                    <label for="testPrintNewPage">Print on New Page</label>
                                    <select class="form-control" id="testPrintNewPage" name="print_new_page">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="testShortcut">Shortcut</label>
                                    <!-- removed -->
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTestBtn">Save Test</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
function loadCategoriesForTests(){
    $.get('ajax/test_category_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success){ var s=''; resp.data.forEach(function(c){ s += '<option value="'+c.id+'">'+(c.name||'')+'</option>'; }); $('#testCategoryId').append(s); }
        else toastr.error('Failed to load categories');
    },'json');
}

function loadTests(){
    $.get('ajax/test_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success && Array.isArray(resp.data)){
            var t=''; resp.data.forEach(function(x, idx){ t += '<tr>'+
                        '<td>'+(idx+1)+'</td>'+ // S.No.
                        '<td>'+x.id+'</td>'+
                        '<td>'+ (x.category_name||'') +'</td>'+
                        '<td>'+ (x.name||'') +'</td>'+
                        '<td>'+ (x.price||'') +'</td>'+
                        '<td>'+ (x.added_by_username||'') +'</td>'+
                        '<td><button class="btn btn-sm btn-info view-test" data-id="'+x.id+'" onclick="viewTest('+x.id+')">View</button> '+
                            '<button class="btn btn-sm btn-warning edit-test" data-id="'+x.id+'">Edit</button> '+
                            '<button class="btn btn-sm btn-danger delete-test" data-id="'+x.id+'">Delete</button></td>'+
                    '</tr>'; }); $('#testsTable tbody').html(t);
            // initialize DataTable (default order by ID desc — column index 1)
            if(typeof initDataTable === 'function'){
                initDataTable('#testsTable', { order: [[1, 'desc']] });
            } else {
                console.warn('initDataTable is not defined; ensure assets/js/common.js is loaded');
            }
        } else toastr.error('Failed to load tests');
    },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
}

function applyTestsFilters(){
    var q = $('#testsSearch').val().toLowerCase().trim();
    var per = parseInt($('#testsPerPage').val()||10,10);
    var shown = 0;
    $('#testsTable tbody tr').each(function(){
        var row = $(this);
        var text = row.text().toLowerCase();
        var matches = !q || text.indexOf(q) !== -1;
        if(matches && shown < per){ row.show(); shown++; } else { row.toggle(matches && shown < per); }
    });
}

function openAddTestModal(){ $('#testForm')[0].reset(); $('#testId').val(''); $('#testModal').modal('show'); }

$(function(){
    loadCategoriesForTests();
    loadTests();

    $('#saveTestBtn').click(function(){ var data = $('#testForm').serialize() + '&action=save&ajax=1'; $.post('ajax/test_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#testModal').modal('hide'); loadTests(); } else toastr.error(resp.message||'Save failed'); }, 'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); }); });

    // DataTables provides search and paging; removed custom filters

    // delegated edit handler
    $(document).on('click', '.edit-test', function(){
        try{
            console.debug('edit-test clicked', $(this).data('id'));
            var id=$(this).data('id');
            $.get('ajax/test_api.php',{action:'get',id:id,ajax:1}, function(resp){ if(resp.success){ var d=resp.data; $('#testId').val(d.id); $('#testCategoryId').val(d.category_id); $('#testName').val(d.name); $('#testDescription').val(d.description); $('#testPrice').val(d.price); $('#testUnit').val(d.unit); $('#testMin').val(d.min); $('#testMax').val(d.max); $('#testSubHeading').val(d.sub_heading); $('#testPrintNewPage').val(d.print_new_page); $('#testModal').modal('show'); } else toastr.error('Test not found'); },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('edit-test handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // delegated delete handler
    $(document).on('click', '.delete-test', function(){
        try{
            if(!confirm('Delete test?')) return; var id=$(this).data('id'); $.post('ajax/test_api.php',{action:'delete',id:id,ajax:1}, function(resp){ if(resp.success){ toastr.success(resp.message); loadTests(); } else toastr.error(resp.message||'Delete failed'); }, 'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('delete-test handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // global fallback for view - show full details in modal
    window.viewTest = function(id){
        try{
            $.get('ajax/test_api.php',{action:'get',id:id}, function(resp){
                if(resp.success){
                    var d = resp.data || {};
                    $('#testModalLabel').text('View Test');
                    // populate form fields for viewing
                    $('#testId').val(d.id);
                    $('#testCategoryId').val(d.category_id);
                    $('#testName').val(d.name);
                    $('#testDescription').val(d.description);
                    $('#testPrice').val(d.price);
                    $('#testUnit').val(d.unit);
                    $('#testMin').val(d.min);
                    $('#testMax').val(d.max);
                    $('#testSubHeading').val(d.sub_heading);
                    $('#testPrintNewPage').val(d.print_new_page);
                    // disable inputs and show modal
                    $('#testForm').find('input,textarea,select').prop('disabled', true);
                    $('#saveTestBtn').hide();
                    $('#testModal').modal('show');
                } else {
                    toastr.error('Test not found');
                }
            }, 'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ toastr.error('Error: '+(err.message||err)); }
    }

    // restore modal state on close
    $('#testModal').on('hidden.bs.modal', function(){
        $('#testForm').find('input,textarea,select').prop('disabled', false);
        $('#saveTestBtn').show();
        $('#testModalLabel').text('Add Test');
    });
});
</script>