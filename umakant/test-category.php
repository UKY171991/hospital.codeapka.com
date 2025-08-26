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
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="categoriesSearch" class="form-control" placeholder="Search categories by name or description...">
                                        <div class="input-group-append">
                                            <button id="categoriesSearchClear" class="btn btn-outline-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto text-right">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="categoriesPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <table id="categoriesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
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
        '<td>'+ ((c.added_by_username && c.added_by_username!='')?c.added_by_username:(c.added_by||'')) +'</td>'+
        '<td><button class="btn btn-sm btn-info view-category" data-id="'+c.id+'" onclick="viewCategory('+c.id+')">View</button> '+
            '<button class="btn btn-sm btn-warning edit-category" data-id="'+c.id+'">Edit</button> '+
            '<button class="btn btn-sm btn-danger delete-category" data-id="'+c.id+'">Delete</button></td>'+
        '</tr>'; }); $('#categoriesTable tbody').html(t);
        } else toastr.error('Failed to load categories');
    },'json');
}

function openAddCategoryModal(){ $('#categoryForm')[0].reset(); $('#categoryId').val(''); $('#categoryModal').modal('show'); }

// global fallback used by inline onclick on View buttons
function viewCategory(id){
    try{
        console.debug('viewCategory() called', id);
        $.get('ajax/test_category_api.php',{action:'get',id:id}, function(resp){ if(resp.success){ var d=resp.data; $('#categoryId').val(d.id); $('#categoryName').val(d.name); $('#categoryDescription').val(d.description); $('#categoryModalLabel').text('View Category'); $('#categoryForm').find('input,textarea,select').prop('disabled', true); $('#saveCategoryBtn').hide(); $('#categoryModal').modal('show'); } else toastr.error('Category not found'); },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
    }catch(err){ console.error('viewCategory error', err); toastr.error('Error: '+(err.message||err)); }
}

$(function(){
    loadCategories();

    // search/filter UI
    $('#categoriesSearch').on('input', function(){
        var q = $(this).val().toLowerCase().trim();
        if(!q){ $('#categoriesTable tbody tr').show(); return; }
        $('#categoriesTable tbody tr').each(function(){ var row=$(this); var text=row.text().toLowerCase(); row.toggle(text.indexOf(q) !== -1); });
    });
    $('#categoriesSearchClear').click(function(e){ e.preventDefault(); $('#categoriesSearch').val(''); $('#categoriesSearch').trigger('input'); });

    $('#saveCategoryBtn').click(function(){ var data=$('#categoryForm').serialize() + '&action=save'; $.post('ajax/test_category_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#categoryModal').modal('hide'); loadCategories(); } else toastr.error(resp.message||'Save failed'); }, 'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); }); });

    // delegated edit handler
    $(document).on('click', '.edit-category', function(){
        try{
            console.debug('edit-category clicked', $(this).data('id'));
            var id=$(this).data('id');
            $.get('ajax/test_category_api.php',{action:'get',id:id}, function(resp){
                if(resp.success){ var d=resp.data; $('#categoryId').val(d.id); $('#categoryName').val(d.name); $('#categoryDescription').val(d.description); $('#categoryModalLabel').text('Edit Category'); $('#saveCategoryBtn').show(); $('#categoryForm').find('input,textarea,select').prop('disabled', false); $('#categoryModal').modal('show'); } else toastr.error('Category not found');
            },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('edit-category handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // delegated delete handler
    $(document).on('click', '.delete-category', function(){
        try{
            if(!confirm('Delete category?')) return; var id=$(this).data('id'); $.post('ajax/test_category_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadCategories(); } else toastr.error(resp.message||'Delete failed'); }, 'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('delete-category handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // delegated view handler fallback is provided by global function viewCategory(id)
    
    // restore modal state on close
    $('#categoryModal').on('hidden.bs.modal', function(){
        $('#categoryForm').find('input,textarea,select').prop('disabled', false);
        $('#saveCategoryBtn').show();
        $('#categoryModalLabel').text('Add Category');
    });
});
</script>