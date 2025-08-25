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
                    <h1>Test Categories</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Test Categories</li>
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
                            <table id="categoriesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Created At</th>
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

<?php require_once 'inc/footer.php'; ?>

<script>
function loadCategories(){
    $.get('ajax/test_category_api.php',{action:'list'},function(resp){
        if(resp.success){ var t=''; resp.data.forEach(function(c){ t += '<tr>'+
                '<td>'+c.id+'</td>'+
                '<td>'+ (c.name||'') +'</td>'+
                '<td>'+ (c.description||'') +'</td>'+
                '<td>'+ (c.created_at||'') +'</td>'+
                '<td><button class="btn btn-sm btn-info view-category" data-id="'+c.id+'">View</button> '+
                        '<button class="btn btn-sm btn-warning edit-category" data-id="'+c.id+'">Edit</button> '+
                        '<button class="btn btn-sm btn-danger delete-category" data-id="'+c.id+'">Delete</button></td>'+
                '</tr>'; }); $('#categoriesTable tbody').html(t);
        } else toastr.error('Failed to load categories');
    },'json');
}

function openAddCategoryModal(){ $('#categoryForm')[0].reset(); $('#categoryId').val(''); $('#categoryModal').modal('show'); }

$(function(){
    loadCategories();
    $('#saveCategoryBtn').click(function(){ var data=$('#categoryForm').serialize() + '&action=save'; $.post('ajax/test_category_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#categoryModal').modal('hide'); loadCategories(); } else toastr.error(resp.message||'Save failed'); }, 'json'); });

    $('#categoriesTable').on('click', '.edit-category', function(){ var id=$(this).data('id'); $.get('ajax/test_category_api.php',{action:'get',id:id}, function(resp){ if(resp.success){ var d=resp.data; $('#categoryId').val(d.id); $('#categoryName').val(d.name); $('#categoryDescription').val(d.description); $('#categoryModal').modal('show'); } else toastr.error('Category not found'); },'json'); });

    $('#categoriesTable').on('click', '.delete-category', function(){ if(!confirm('Delete category?')) return; var id=$(this).data('id'); $.post('ajax/test_category_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadCategories(); } else toastr.error(resp.message||'Delete failed'); }, 'json'); });
});
</script>