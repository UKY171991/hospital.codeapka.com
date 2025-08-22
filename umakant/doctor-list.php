<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Doctor List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary" onclick="addDoctor()"><i class="fas fa-plus"></i> Add New Doctor</button>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Doctors</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search doctors...">
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
                        <table class="table table-bordered table-hover" id="doctorTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Qualification</th>
                                    <th>Specialization</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Registration No</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Doctor rows will be loaded here by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Doctor Details Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="doctorModalLabel">Doctor Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="doctorModalBody">
                <!-- Doctor details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Doctor Modal -->
<div class="modal fade" id="doctorFormModal" tabindex="-1" role="dialog" aria-labelledby="doctorFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="doctorForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="doctorFormModalLabel">Add New Doctor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="doctorId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qualification">Qualification</label>
                                <input type="text" class="form-control" id="qualification" name="qualification">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="specialization">Specialization</label>
                                <input type="text" class="form-control" id="specialization" name="specialization">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="registration_no">Registration No</label>
                                <input type="text" class="form-control" id="registration_no" name="registration_no">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Doctor</button>
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

<script>
function loadDoctors() {
    $.get('ajax/doctor_ajax.php', {action: 'list'}, function(data) {
        $('#doctorTable tbody').html(data);
    });
}

function addDoctor() {
    $('#doctorForm')[0].reset();
    $('#doctorId').val('');
    $('#doctorFormModalLabel').text('Add New Doctor');
    $('#doctorFormModal').modal('show');
}

function editDoctor(id) {
    $.get('ajax/doctor_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            $('#doctorId').val(data.id);
            $('#name').val(data.name);
            $('#qualification').val(data.qualification);
            $('#specialization').val(data.specialization);
            $('#phone').val(data.phone);
            $('#email').val(data.email);
            $('#address').val(data.address);
            $('#registration_no').val(data.registration_no);
            
            $('#doctorFormModalLabel').text('Edit Doctor');
            $('#doctorFormModal').modal('show');
        }
    });
}

function viewDoctor(id) {
    $.get('ajax/doctor_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Name:</strong></td><td>${data.name || 'N/A'}</td></tr>
                            <tr><td><strong>Qualification:</strong></td><td>${data.qualification || 'N/A'}</td></tr>
                            <tr><td><strong>Specialization:</strong></td><td>${data.specialization || 'N/A'}</td></tr>
                            <tr><td><strong>Phone:</strong></td><td>${data.phone || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Email:</strong></td><td>${data.email || 'N/A'}</td></tr>
                            <tr><td><strong>Address:</strong></td><td>${data.address || 'N/A'}</td></tr>
                            <tr><td><strong>Registration No:</strong></td><td>${data.registration_no || 'N/A'}</td></tr>
                            <tr><td><strong>Added By:</strong></td><td>${data.added_by || 'N/A'}</td></tr>
                            <tr><td><strong>Created At:</strong></td><td>${data.created_at || 'N/A'}</td></tr>
                            <tr><td><strong>Updated At:</strong></td><td>${data.updated_at || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            $('#doctorModalBody').html(html);
            $('#doctorModal').modal('show');
        }
    });
}

function deleteDoctor(id) {
    if (confirm('Are you sure you want to delete this doctor?')) {
        $.post('ajax/doctor_ajax.php', {action: 'delete', id: id}, function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                loadDoctors();
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
    loadDoctors();
    
    // Search functionality
    $('input[name="table_search"]').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#doctorTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Form submission
    $('#doctorForm').on('submit', function(e) {
        e.preventDefault();
        
        $.post('ajax/doctor_ajax.php', $(this).serialize() + '&action=save', function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                $('#doctorFormModal').modal('hide');
                loadDoctors();
            } else {
                showAlert('error', response.message);
            }
        }, 'json');
    });
});
</script>

<?php include 'inc/footer.php'; ?>
