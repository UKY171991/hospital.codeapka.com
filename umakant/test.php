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
                                            <label for="testMin">Min Value (General)</label>
                                            <input type="number" class="form-control" id="testMin" name="min" step="0.01">
                                        </div>
                                    <div class="form-group col-md-6">
                                        <label for="testMax">Max Value (General)</label>
                                        <input type="number" class="form-control" id="testMax" name="max" step="0.01">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="testMinMale">Min Value (Male)</label>
                                        <input type="number" class="form-control" id="testMinMale" name="min_male" step="0.01">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="testMaxMale">Max Value (Male)</label>
                                        <input type="number" class="form-control" id="testMaxMale" name="max_male" step="0.01">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="testMinFemale">Min Value (Female)</label>
                                        <input type="number" class="form-control" id="testMinFemale" name="min_female" step="0.01">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="testMaxFemale">Max Value (Female)</label>
                                        <input type="number" class="form-control" id="testMaxFemale" name="max_female" step="0.01">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="testSubHeading">Sub Heading</label>
                                    <select class="form-control" id="testSubHeading" name="sub_heading">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                
                                <!-- method removed -->
                                <div class="form-group">
                                    <label for="testPrintNewPage">Print on New Page</label>
                                    <select class="form-control" id="testPrintNewPage" name="print_new_page">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
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

<!-- View Test Modal -->
<div class="modal fade" id="viewTestModal" tabindex="-1" role="dialog" aria-labelledby="viewTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTestModalLabel">Test Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewTestBody">
                <!-- details injected by JS -->
                <div class="text-center text-muted py-4">Loading...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
function addTestToTable(testData) {
    // Check if table is currently empty (has only one row with colspan)
    var isEmptyTable = $('#testsTable tbody tr').length === 1 && 
                      $('#testsTable tbody tr:first td').attr('colspan') === '7';
    
    // Add row to table
    var newRow = '<tr>' +
        '<td></td>' + // S.No. will be handled by DataTable
        '<td>' + testData.id + '</td>' +
        '<td>' + (testData.category_name || '') + '</td>' +
        '<td>' + (testData.name || '') + '</td>' +
        '<td>' + (testData.price || '') + '</td>' +
        '<td>' + (testData.added_by_username || '') + '</td>' +
        '<td><button class="btn btn-sm btn-info view-test" data-id="' + testData.id + '" onclick="viewTest(' + testData.id + ')">View</button> ' +
            '<button class="btn btn-sm btn-warning edit-test" data-id="' + testData.id + '">Edit</button> ' +
            '<button class="btn btn-sm btn-danger delete-test" data-id="' + testData.id + '">Delete</button></td>' +
        '</tr>';
    
    // If DataTable is present, use its API to add the row for better UX
    if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
        try {
            var table = $('#testsTable').DataTable();
            // Build row data array matching columns: S.No. (auto), ID, Category, Name, Price, Added By, Actions
            var actions = '<button class="btn btn-sm btn-info view-test" data-id="' + testData.id + '" onclick="viewTest(' + testData.id + ')">View</button> ' +
                          '<button class="btn btn-sm btn-warning edit-test" data-id="' + testData.id + '">Edit</button> ' +
                          '<button class="btn btn-sm btn-danger delete-test" data-id="' + testData.id + '">Delete</button>';
                // Add row without immediate draw to avoid inconsistent meta indexes
                var rowNode = table.row.add([
                    '', // placeholder for serial column (rendered by DataTable)
                    testData.id,
                    testData.category_name || '',
                    testData.name || '',
                    testData.price || '',
                    testData.added_by_username || '',
                    actions
                ]).node();

                // Ensure newest appears at top: order by ID desc and go to first page, then perform a full draw
                try{
                    table.order([[1, 'desc']]).page('first').draw();
                    // Recompute serial numbers for visible page to avoid meta inconsistencies
                    try{
                        var nodes = table.rows({ order: 'applied', page: 'current' }).nodes();
                        $(nodes).each(function(i, row){ $(row).find('td:first').text(i + 1); });
                    }catch(eNum){ /* ignore numbering issues */ }
                }catch(e){
                    // fallback to drawing table to recalc numbering
                    try{ table.draw(); }catch(e2){}
                }
            return;
        } catch (e) {
            console.warn('Failed to add row via DataTable API, falling back to DOM:', e);
        }
    }

    // Fallback when no DataTable exists: insert into DOM
    if (isEmptyTable) {
        // Replace the empty message row with the new row
        $('#testsTable tbody').html(newRow);
    } else {
        // Add new row to existing table
        $('#testsTable tbody').prepend(newRow);
    }
    // update serial numbers for regular table
    updateSerialNumbers();
}

function updateSerialNumbers() {
    $('#testsTable tbody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function loadCategoriesForTests(){
    $.get('ajax/test_category_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success){ var s=''; resp.data.forEach(function(c){ s += '<option value="'+c.id+'">'+(c.name||'')+'</option>'; }); $('#testCategoryId').append(s); }
        else toastr.error('Failed to load categories');
    },'json');
}

function loadTests(){
    $.get('ajax/test_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success && Array.isArray(resp.data)){
            var t=''; 
            if(resp.data.length === 0) {
                t = '<tr><td colspan="7" class="text-center py-4"><i class="fas fa-info-circle text-muted mr-2"></i>No tests found</td></tr>';
            } else {
                resp.data.forEach(function(x, idx){ t += '<tr>'+
                            '<td></td>'+ // S.No. - will be handled by DataTable
                            '<td>'+x.id+'</td>'+
                            '<td>'+ (x.category_name||'') +'</td>'+
                            '<td>'+ (x.name||'') +'</td>'+
                            '<td>'+ (x.price||'') +'</td>'+
                            '<td>'+ (x.added_by_username||'') +'</td>'+
                            '<td><button class="btn btn-sm btn-info view-test" data-id="'+x.id+'" onclick="viewTest('+x.id+')">View</button> '+
                                '<button class="btn btn-sm btn-warning edit-test" data-id="'+x.id+'">Edit</button> '+
                                '<button class="btn btn-sm btn-danger delete-test" data-id="'+x.id+'">Delete</button></td>'+
                        '</tr>'; });
            }
            
            // Always destroy and recreate DataTable to avoid conflicts
            if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
                $('#testsTable').DataTable().destroy();
            }
            
            // Set table content
            $('#testsTable tbody').html(t);
            
            // Initialize DataTable only if there are records
            if(resp.data.length > 0 && typeof initDataTable === 'function'){
                initDataTable('#testsTable', { order: [[1, 'desc']] });
            }
            // Don't initialize DataTable for empty tables - let it show the message naturally
        } else {
            // Clear table and show error message
            $('#testsTable tbody').html('<tr><td colspan="7" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mr-2"></i>Failed to load tests</td></tr>');
            toastr.error('Failed to load tests');
        }
    },'json').fail(function(xhr){ 
        $('#testsTable tbody').html('<tr><td colspan="7" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading tests</td></tr>');
        var msg = xhr.responseText || 'Server error'; 
        try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} 
        toastr.error(msg); 
    });
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

    $('#saveTestBtn').click(function(){ 
        // Validate min/max ranges before submitting
        function validateTestRanges(){
            var pairs = [
                {min:'#testMin', max:'#testMax', label:'General'},
                {min:'#testMinMale', max:'#testMaxMale', label:'Male'},
                {min:'#testMinFemale', max:'#testMaxFemale', label:'Female'}
            ];
            for(var i=0;i<pairs.length;i++){
                var p = pairs[i];
                var vMin = $(p.min).val().trim();
                var vMax = $(p.max).val().trim();
                if(vMin === '' || vMax === '') continue; // nothing to validate
                var nMin = parseFloat(vMin);
                var nMax = parseFloat(vMax);
                if(isNaN(nMin) || isNaN(nMax)){
                    toastr.error(p.label + ' range must be numeric');
                    $(p.min).focus();
                    return false;
                }
                if(nMax < nMin){
                    toastr.error('Max Value ('+p.label+') cannot be less than Min Value ('+p.label+').');
                    $(p.max).focus();
                    return false;
                }
            }
            return true;
        }

        if(!validateTestRanges()) return; // abort save if invalid

        var isEdit = $('#testId').val() !== '';
        // ensure new gender fields are included
        var data = $('#testForm').serialize() + '&action=save&ajax=1'; 
        
        $.post('ajax/test_api.php', data, function(resp){ 
            if(resp.success){ 
                toastr.success(resp.message||'Saved'); 
                $('#testModal').modal('hide'); 
                
                if(resp.data && !isEdit) { 
                    // New record - add to table directly
                    addTestToTable(resp.data); 
                } else { 
                    // Update - reload table
                    loadTests(); 
                } 
                
                // Reset form after successful save
                $('#testForm')[0].reset(); 
                $('#testId').val(''); 
            } else { 
                toastr.error(resp.message||'Save failed'); 
            } 
        }, 'json').fail(function(xhr){ 
            var msg = xhr.responseText || 'Server error'; 
            try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} 
            toastr.error(msg); 
        }); 
    });

    // DataTables provides search and paging; removed custom filters

    // delegated edit handler
    $(document).on('click', '.edit-test', function(){
        try{
            console.debug('edit-test clicked', $(this).data('id'));
            var id=$(this).data('id');
            $.get('ajax/test_api.php',{action:'get',id:id,ajax:1}, function(resp){
                if(resp.success){
                    var d = resp.data || {};
                    // populate fields
                    $('#testId').val(d.id);
                    $('#testCategoryId').val(d.category_id);
                    $('#testName').val(d.name);
                    $('#testDescription').val(d.description);
                    $('#testPrice').val(d.price);
                    $('#testUnit').val(d.unit);
                    $('#testMin').val(d.min);
                    $('#testMax').val(d.max);
                    // gender-specific ranges
                    $('#testMinMale').val(d.min_male);
                    $('#testMaxMale').val(d.max_male);
                    $('#testMinFemale').val(d.min_female);
                    $('#testMaxFemale').val(d.max_female);
                    $('#testSubHeading').val(d.sub_heading);
                    $('#testPrintNewPage').val(d.print_new_page);
                    // ensure form inputs are enabled for editing and show save
                    $('#testForm').find('input,textarea,select').prop('disabled', false);
                    $('#saveTestBtn').show();
                    $('#testModal').modal('show');
                } else {
                    toastr.error('Test not found');
                }
            },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('edit-test handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // delegated delete handler
    $(document).on('click', '.delete-test', function(){
        try{
            var id = $(this).data('id');
            if(!confirm('Delete test?')) return;
            
            $.post('ajax/test_api.php', {
                action: 'delete',
                id: id,
                ajax: 1
            }, function(resp){
                if(resp.success){
                    toastr.success(resp.message || 'Test deleted successfully');
                    loadTests(); // Reload the table
                } else {
                    toastr.error(resp.message || 'Delete failed');
                }
            }, 'json').fail(function(xhr){
                var msg = xhr.responseText || 'Server error';
                try{ 
                    var j = JSON.parse(xhr.responseText || '{}'); 
                    if(j.message) msg = j.message;
                } catch(e){} 
                toastr.error('Delete failed: ' + msg);
            });
        } catch(err){ 
            console.error('delete-test handler error', err); 
            toastr.error('Error: ' + (err.message || err)); 
        }
    });

    // global view - show full details in dedicated view modal
    window.viewTest = function(id){
        try{
            $.get('ajax/test_api.php',{action:'get',id:id,ajax:1}, function(resp){
                if(resp.success){
                    var d = resp.data || {};
                    var html = '';
                    html += '<div class="container-fluid">';
                    html += '<div class="row">';
                    // Left column: main info
                    html += '<div class="col-md-7">';
                    html += '  <h4 class="mb-1">' + escapeHtml(d.name || '') + ' <small class="text-muted">#' + escapeHtml(d.id || '') + '</small></h4>';
                    if(d.description) html += '<p class="text-muted">' + escapeHtml(d.description) + '</p>';
                    html += '  <div class="row">';
                    html += '    <div class="col-sm-6"><strong>Category</strong><div>' + escapeHtml(d.category_name||'') + '</div></div>';
                    html += '    <div class="col-sm-6"><strong>Price</strong><div>' + escapeHtml(d.price||'') + '</div></div>';
                    html += '    <div class="col-sm-6 mt-2"><strong>Unit</strong><div>' + escapeHtml(d.unit||'') + '</div></div>';
                    html += '    <div class="col-sm-6 mt-2"><strong>Sub Heading</strong><div>' + (d.sub_heading? 'Yes':'No') + '</div></div>';
                    html += '  </div>'; // row
                    html += '</div>'; // col-md-7

                    // Right column: metadata and actions
                    html += '<div class="col-md-5">';
                    html += '  <div class="card border-0">';
                    html += '    <div class="card-body p-2">';
                    html += '      <p class="mb-1"><small class="text-muted">Added By</small><br><strong>' + escapeHtml(d.added_by_username||'') + '</strong></p>';
                    html += '      <p class="mb-1"><small class="text-muted">Print New Page</small><br><strong>' + (d.print_new_page? 'Yes':'No') + '</strong></p>';
                    html += '      <p class="mb-0"><small class="text-muted">Default Result</small><br>' + escapeHtml(d.default_result||'') + '</p>';
                    html += '    </div>'; 
                    html += '  </div>';
                    html += '</div>'; // col-md-5

                    html += '</div>'; // row

                    // Ranges table
                    html += '<hr/>';
                    html += '<h6 class="mb-2">Reference Ranges</h6>';
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-sm table-bordered">';
                    html += '<thead class="thead-light"><tr><th>Scope</th><th>Min</th><th>Max</th></tr></thead>';
                    html += '<tbody>';
                    html += '<tr><td>General</td><td>' + escapeHtml(d.min||'') + '</td><td>' + escapeHtml(d.max||'') + '</td></tr>';
                    html += '<tr><td>Male</td><td>' + escapeHtml(d.min_male||'') + '</td><td>' + escapeHtml(d.max_male||'') + '</td></tr>';
                    html += '<tr><td>Female</td><td>' + escapeHtml(d.min_female||'') + '</td><td>' + escapeHtml(d.max_female||'') + '</td></tr>';
                    html += '</tbody></table></div>';

                    if(d.reference_range){ html += '<p class="mt-2"><strong>Reference Note:</strong> ' + escapeHtml(d.reference_range) + '</p>'; }

                    html += '</div>'; // container-fluid

                    $('#viewTestBody').html(html);
                    $('#viewTestModal').modal('show');
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