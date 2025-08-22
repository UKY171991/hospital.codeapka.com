<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Test List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary" onclick="addTest()"><i class="fas fa-plus"></i> Add New Test</button>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Tests</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search tests...">
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
                        <table class="table table-bordered table-hover" id="testTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Test Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Unit</th>
                                    <th>Reference Range</th>
                                    <th>Min Value</th>
                                    <th>Max Value</th>
                                    <th>Method</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Test rows will be loaded here by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Test Details Modal -->
<div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="testModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testModalLabel">Test Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="testModalBody">
                <!-- Test details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Test Modal -->
<div class="modal fade" id="testFormModal" tabindex="-1" role="dialog" aria-labelledby="testFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="testForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="testFormModalLabel">Add New Test</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="testId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_name">Test Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="test_name" name="test_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" class="form-control" id="category" name="category">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit">Unit</label>
                                <input type="text" class="form-control" id="unit" name="unit">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference_range">Reference Range</label>
                                <input type="text" class="form-control" id="reference_range" name="reference_range">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="method">Method</label>
                                <input type="text" class="form-control" id="method" name="method">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_value">Min Value</label>
                                <input type="number" class="form-control" id="min_value" name="min_value" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="max_value">Max Value</label>
                                <input type="number" class="form-control" id="max_value" name="max_value" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Test</button>
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
function loadTests() {
    $.get('ajax/test_ajax.php', {action: 'list'}, function(data) {
        $('#testTable tbody').html(data);
    });
}

function addTest() {
    $('#testForm')[0].reset();
    $('#testId').val('');
    $('#testFormModalLabel').text('Add New Test');
    $('#testFormModal').modal('show');
}

function editTest(id) {
    $.get('ajax/test_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            $('#testId').val(data.id);
            $('#test_name').val(data.test_name);
            $('#category').val(data.category);
            $('#description').val(data.description);
            $('#price').val(data.price);
            $('#unit').val(data.unit);
            $('#reference_range').val(data.reference_range);
            $('#min_value').val(data.min_value);
            $('#max_value').val(data.max_value);
            $('#method').val(data.method);
            
            $('#testFormModalLabel').text('Edit Test');
            $('#testFormModal').modal('show');
        }
    });
}

function viewTest(id) {
    $.get('ajax/test_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Test Name:</strong></td><td>${data.test_name || 'N/A'}</td></tr>
                            <tr><td><strong>Category:</strong></td><td>${data.category || 'N/A'}</td></tr>
                            <tr><td><strong>Price:</strong></td><td>₹${data.price || 'N/A'}</td></tr>
                            <tr><td><strong>Unit:</strong></td><td>${data.unit || 'N/A'}</td></tr>
                            <tr><td><strong>Method:</strong></td><td>${data.method || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Reference Range:</strong></td><td>${data.reference_range || 'N/A'}</td></tr>
                            <tr><td><strong>Min Value:</strong></td><td>${data.min_value || 'N/A'}</td></tr>
                            <tr><td><strong>Max Value:</strong></td><td>${data.max_value || 'N/A'}</td></tr>
                            <tr><td><strong>Added By:</strong></td><td>${data.added_by || 'N/A'}</td></tr>
                            <tr><td><strong>Created At:</strong></td><td>${data.created_at || 'N/A'}</td></tr>
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
            $('#testModalBody').html(html);
            $('#testModal').modal('show');
        }
    });
}

function deleteTest(id) {
    if (confirm('Are you sure you want to delete this test?')) {
        $.post('ajax/test_ajax.php', {action: 'delete', id: id}, function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                loadTests();
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
    loadTests();
    
    // Search functionality
    $('input[name="table_search"]').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#testTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Form submission
    $('#testForm').on('submit', function(e) {
        e.preventDefault();
        
        $.post('ajax/test_ajax.php', $(this).serialize() + '&action=save', function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                $('#testFormModal').modal('hide');
                loadTests();
            } else {
                showAlert('error', response.message);
            }
        }, 'json');
    });
});
</script>

<?php include 'inc/footer.php'; ?>
