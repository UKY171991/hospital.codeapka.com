<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
$category_count = '--';
$current_user_id = $_SESSION['user_id'] ?? null;
$current_user_role = $_SESSION['role'] ?? 'user';
$current_username = $_SESSION['username'] ?? 'You';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="mb-1">Test Categories</h1>
                    <span class="text-muted">Manage and organize categories for laboratory tests.</span>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Test Categories</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-4 col-sm-6">
                    <div class="info-box category-summary">
                        <span class="info-box-icon bg-primary"><i class="fas fa-tags"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Categories</span>
                            <span class="info-box-number" id="categoryCount">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="info-box category-summary">
                        <span class="info-box-icon bg-success"><i class="fas fa-user"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Current User</span>
                            <span class="info-box-number" id="currentUserLabel"><?php echo htmlspecialchars($current_username); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="info-box category-summary">
                        <span class="info-box-icon bg-info"><i class="fas fa-search"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Search Categories</span>
                            <input type="text" class="form-control form-control-sm mt-2" id="categorySearch" placeholder="Type to filter table">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-layer-group mr-2"></i>Category Directory</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm mr-2" onclick="openAddCategoryModal()" data-toggle="modal" data-target="#categoryModal">
                                    <i class="fas fa-plus mr-1"></i> Add Category
                                </button>
                                <button type="button" class="btn btn-default btn-sm" id="refreshCategories">
                                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="categoriesTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width:70px;">#</th>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th class="text-center">Tests</th>
                                            <th>Added By</th>
                                            <th style="width:150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <span class="badge badge-primary text-uppercase" id="categoryModalBadge">New</span>
                    <h5 class="modal-title mt-2" id="categoryModalLabel">Add Category</h5>
                    <small class="text-muted">Provide category name and optional description to organize your tests.</small>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-0">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId" name="id">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-semibold" for="categoryName">Category Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                </div>
                                <input type="text" class="form-control" id="categoryName" name="name" placeholder="e.g., Hematology" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="font-weight-semibold" for="categoryAddedBy">Added By</label>
                            <input type="text" class="form-control" id="categoryAddedBy" value="<?php echo htmlspecialchars($_SESSION['username'] ?? 'You'); ?>" disabled>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-semibold" for="categoryDescription">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="4" placeholder="Describe when this category should be used (optional)"></textarea>
                        <small class="form-text text-muted">Helpful descriptions make it easier for collaborators to pick the right category.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">
                    <i class="fas fa-save mr-1"></i>Save Category
                </button>
            </div>
        </div>
    </div>
</div>


<!-- DataTables CSS/JS (CDN) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Global DataTable initializer -->
<script src="assets/js/common.js"></script>

<?php require_once 'inc/footer.php'; ?>

<?php // include separated read-only view modal for categories
require_once __DIR__ . '/inc/category_view_modal.php'; ?>

<script>
const TEST_CATEGORY_API = 'patho_api/test_category.php';
const CURRENT_USER_ID = <?php echo (int)($current_user_id ?? 0); ?>;
const CURRENT_USER_ROLE = <?php echo json_encode($current_user_role); ?>;

function loadCategories(){
    const params = { action: 'list' };
    if (CURRENT_USER_ID) {
        params.user_id = CURRENT_USER_ID;
    }

    $.getJSON(TEST_CATEGORY_API, params, function(resp){
        if(resp.success){
            var $table = $('#categoriesTable');
            // Destroy existing DataTable instance if it exists
            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
            }

            var t='';
            resp.data.forEach(function(c, idx){
                var description = (c.description && c.description.length > 80) ? c.description.substring(0, 77) + '…' : (c.description||'');
                var addedBy = (c.added_by_username && c.added_by_username!=='')?c.added_by_username:(c.added_by||'');
                t += '<tr>'+
                    '<td class="text-center">'+(idx+1)+'</td>'+ // S.No.
                    '<td><span class="badge badge-light border">#'+c.id+'</span></td>'+
                    '<td><strong>'+ (c.name||'') +'</strong></td>'+
                    '<td>'+ description +'</td>'+
                    '<td class="text-center"><span class="badge badge-pill badge-info">'+ (c.test_count||0) +'</span></td>'+
                    '<td><span class="text-muted"><i class="fas fa-user mr-1 text-secondary"></i>'+ addedBy +'</span></td>'+
                    '<td class="text-nowrap">'+
                        '<button class="btn btn-sm btn-outline-primary mr-1" data-id="'+c.id+'" onclick="viewCategory('+c.id+')"><i class="fas fa-eye"></i></button>'+ 
                        '<button class="btn btn-sm btn-outline-info mr-1 edit-category" data-id="'+c.id+'"><i class="fas fa-edit"></i></button>'+ 
                        '<button class="btn btn-sm btn-outline-danger delete-category" data-id="'+c.id+'"><i class="fas fa-trash"></i></button>'+
                    '</td>'+ 
                    '</tr>';
            });
            $('#categoriesTable tbody').html(t);
            // Reinitialize DataTable
            initDataTable('#categoriesTable', {
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                language: {
                    emptyTable: 'No categories available yet. Click "New Category" to add one.'
                }
            });
            $('#categoryCount').text(resp.total ?? resp.data.length ?? 0);
        } else {
            toastr.error('Failed to load categories');
            $('#categoryCount').text('0');
        }
    }).fail(function(xhr){
        var msg = xhr.responseText || 'Server error';
        try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){}
        toastr.error(msg);
        $('#categoryCount').text('0');
    });
}

function openAddCategoryModal(){ $('#categoryForm')[0].reset(); $('#categoryId').val(''); $('#categoryModal').modal('show'); }

// global fallback used by inline onclick on View buttons
function viewCategory(id){
    try{
        $.getJSON(TEST_CATEGORY_API,{action:'get',id:id}, function(resp){
            if(resp.success){
                var d = resp.data;
                var html = '<table class="table table-sm table-borderless">' +
                    '<tr><th>ID</th><td>'+(d.id||'')+'</td></tr>' +
                    '<tr><th>Name</th><td>'+(d.name||'')+'</td></tr>' +
                    '<tr><th>Description</th><td>'+(d.description||'')+'</td></tr>' +
                    '<tr><th>Test Count</th><td>'+(d.test_count||0)+'</td></tr>' +
                    '<tr><th>Added By</th><td>'+((d.added_by_username && d.added_by_username!='')?d.added_by_username:(d.added_by||''))+'</td></tr>' +
                    '</table>';
                $('#categoryViewModal .category-view-content').html(html);
                $('#categoryViewModal').modal('show');
            } else toastr.error('Category not found');
        },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
    }catch(err){ console.error('viewCategory error', err); toastr.error('Error: '+(err.message||err)); }
}

$(function(){
    // Initial load of categories
    loadCategories();

    // Save, edit, delete handlers
    $('#saveCategoryBtn').click(function(){
        var data=$('#categoryForm').serialize() + '&action=save';
        $.post(TEST_CATEGORY_API, data, function(resp){
            if(resp.success){
                toastr.success(resp.message||'Saved');
                $('#categoryModal').modal('hide');
                loadCategories(); // Refresh table after save
            } else {
                toastr.error(resp.message||'Save failed');
            }
        }, 'json').fail(function(xhr){
            var msg = xhr.responseText || 'Server error';
            try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){}
            toastr.error(msg);
        });
    });

    $(document).on('click', '.edit-category', function(){
        try{
            var id=$(this).data('id');
            $.getJSON(TEST_CATEGORY_API,{action:'get',id:id}, function(resp){
                if(resp.success){
                    var d=resp.data;
                    $('#categoryId').val(d.id);
                    $('#categoryName').val(d.name);
                    $('#categoryDescription').val(d.description);
                    $('#categoryModalLabel').text('Edit Category');
                    $('#categoryModalBadge').text('Edit').removeClass('badge-primary').addClass('badge-info');
                    $('#saveCategoryBtn').show();
                    $('#categoryForm').find('input,textarea,select').prop('disabled', false);
                    $('#categoryModal').modal('show');
                } else {
                    toastr.error('Category not found');
                }
            },'json').fail(function(xhr){
                var msg = xhr.responseText || 'Server error';
                try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){}
                toastr.error(msg);
            });
        }catch(err){
            toastr.error('Error: '+(err.message||err));
        }
    });

    $(document).on('click', '.delete-category', function(){
        try{
            if(!confirm('Delete category?')) return;
            var id=$(this).data('id');
            $.post(TEST_CATEGORY_API,{action:'delete',id:id}, function(resp){
                if(resp.success){
                    toastr.success(resp.message);
                    loadCategories(); // Refresh table after delete
                } else {
                    toastr.error(resp.message||'Delete failed');
                }
            }, 'json').fail(function(xhr){
                var msg = xhr.responseText || 'Server error';
                try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){}
                toastr.error(msg);
            });
        }catch(err){
            toastr.error('Error: '+(err.message||err));
        }
    });

    $('#categoryModal').on('hidden.bs.modal', function(){
        $('#categoryForm').find('input,textarea,select').prop('disabled', false);
        $('#saveCategoryBtn').show();
        $('#categoryModalLabel').text('Add Category');
    });
});
</script>