<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
$category_count = '--';
$current_user_id = $_SESSION['user_id'] ?? null;
$current_user_role = $_SESSION['role'] ?? 'user';
?>

<!-- Content Wrapper. Contains page content -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Test Categories <small class="text-muted">(<span id="categoryCount">0</span>)</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Test Categories</li>
{{ ... }}
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
                            <h3 class="card-title">Test Category Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#categoryModal" onclick="openAddCategoryModal()">
                                    <i class="fas fa-plus"></i> Add Category
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
                            <!-- Removed custom search and per-page controls; DataTables provides these -->
                            <table id="categoriesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th class="sortable" data-key="id">ID <span class="sort-indicator"></span></th>
                                        <th class="sortable" data-key="name">Name <span class="sort-indicator"></span></th>
                                        <th class="sortable" data-key="description">Description <span class="sort-indicator"></span></th>
                                        <th>Test Count</th>
                                        <th class="sortable" data-key="added_by_username">Added By <span class="sort-indicator"></span></th>
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

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId" name="id">
                    <div class="form-group">
                        <label for="categoryName">Name *</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="categoryDescription">Description</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">Save Category</button>
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
                t += '<tr>'+
                    '<td>'+(idx+1)+'</td>'+ // S.No.
                    '<td>'+c.id+'</td>'+
                    '<td>'+ (c.name||'') +'</td>'+
                    '<td>'+ (c.description||'') +'</td>'+
                    '<td>'+ (c.test_count||0) +'</td>'+
                    '<td>'+ ((c.added_by_username && c.added_by_username!='')?c.added_by_username:(c.added_by||'')) +'</td>'+
                    '<td><button class="btn btn-sm btn-info view-category" data-id="'+c.id+'" onclick="viewCategory('+c.id+')">View</button> '+
                        '<button class="btn btn-sm btn-primary edit-category" data-id="'+c.id+'">Edit</button> '+
                        '<button class="btn btn-sm btn-danger delete-category" data-id="'+c.id+'">Delete</button></td>'+
                    '</tr>';
            });
            $('#categoriesTable tbody').html(t);
            // Reinitialize DataTable
            initDataTable('#categoriesTable');
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