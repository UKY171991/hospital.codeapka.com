<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Test Categories List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary" onclick="addCategory()"><i class="fas fa-plus"></i> Add New Category</button>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Test Categories</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search categories...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="categoryTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Category rows will be loaded here by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Category Details Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Test Category Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="categoryModalBody">
                <!-- Category details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="categoryFormModal" tabindex="-1" role="dialog" aria-labelledby="categoryFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="categoryForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryFormModalLabel">Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="categoryId">
                    <div class="form-group">
                        <label for="name">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success/Error Alert -->
<div class="alert alert-success alert-dismissible fade" id="successAlert" style="display: none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span id="successMessage"></span>
</div>

<div class="alert alert-danger alert-dismissible fade" id="errorAlert" style="display: none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span id="errorMessage"></span>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadCategories() {
    $.get('ajax/test_category_ajax.php', {action: 'list'}, function(data) {
        $('#categoryTable tbody').html(data);
    });
}

function addCategory() {
    $('#categoryForm')[0].reset();
    $('#categoryId').val('');
    $('#categoryFormModalLabel').text('Add New Category');
    $('#categoryFormModal').modal('show');
}

function editCategory(id) {
    $.get('ajax/test_category_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            $('#categoryId').val(data.id);
            $('#name').val(data.name);
            $('#description').val(data.description);
            
            $('#categoryFormModalLabel').text('Edit Category');
            $('#categoryFormModal').modal('show');
        }
    });
}

function viewCategory(id) {
    $.get('ajax/test_category_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Name:</strong></td><td>${data.name || 'N/A'}</td></tr>
                            <tr><td><strong>Added By:</strong></td><td>${data.added_by || 'N/A'}</td></tr>
                            <tr><td><strong>Created At:</strong></td><td>${data.created_at || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Updated At:</strong></td><td>${data.updated_at || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Description:</strong></h6>
                        <p>${data.description || 'No description available'}</p>
                    </div>
                </div>
            `;
            $('#categoryModalBody').html(html);
            $('#categoryModal').modal('show');
        }
    });
}

function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this test category?')) {
        $.post('ajax/test_category_ajax.php', {action: 'delete', id: id}, function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                loadCategories();
            } else {
                showAlert('error', response.message);
            }
        }, 'json');
    }
}

function showAlert(type, message) {
    if (type === 'success') {
        $('#successMessage').text(message);
        $('#successAlert').show().addClass('show');
        setTimeout(function() {
            $('#successAlert').hide().removeClass('show');
        }, 3000);
    } else {
        $('#errorMessage').text(message);
        $('#errorAlert').show().addClass('show');
        setTimeout(function() {
            $('#errorAlert').hide().removeClass('show');
        }, 3000);
    }
}

$(document).ready(function() {
    loadCategories();
    
    // Search functionality
    $('input[name="table_search"]').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#categoryTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Form submission
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        
        $.post('ajax/test_category_ajax.php', $(this).serialize() + '&action=save', function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                $('#categoryFormModal').modal('hide');
                loadCategories();
            } else {
                showAlert('error', response.message);
            }
        }, 'json');
    });
});
</script>

<?php include 'inc/footer.php'; ?>
