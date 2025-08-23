<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Users List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary" onclick="addUser()"><i class="fas fa-plus"></i> Add New User</button>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Users</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search users...">
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
                        <table class="table table-bordered table-hover table-striped" id="userTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Password Hash</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Expire</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- User rows will be loaded here by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- User Details Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="userModalBody">
                <!-- User details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userFormModal" tabindex="-1" role="dialog" aria-labelledby="userFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="userForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="userFormModalLabel">Add New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select class="form-control" id="role" name="role">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password <span class="text-danger" id="passwordLabel">*</span></label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="form-text text-muted" id="passwordHelp">Leave blank to keep existing password when editing</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
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
function loadUsers() {
    $.get('ajax/user_ajax.php', {action: 'list'}, function(data) {
        $('#userTable tbody').html(data);
    });
}

function addUser() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#passwordLabel').text('*');
    $('#password').prop('required', true);
    $('#passwordHelp').text('Password is required for new users');
    $('#userFormModalLabel').text('Add New User');
    $('#userFormModal').modal('show');
}

function editUser(id) {
    $.get('ajax/user_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            $('#userId').val(data.id);
            $('#username').val(data.username);
            $('#email').val(data.email);
            $('#full_name').val(data.full_name);
            $('#role').val(data.role);
            $('#password').val('').prop('required', false);
            $('#passwordLabel').text('');
            $('#passwordHelp').text('Leave blank to keep existing password');
            
            $('#userFormModalLabel').text('Edit User');
            $('#userFormModal').modal('show');
        }
    });
}

function viewUser(id) {
    $.get('ajax/user_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Username:</strong></td><td>${data.username}</td></tr>
                            <tr><td><strong>Password Hash:</strong></td><td>${data.password_hash ? data.password_hash.substring(0, 20) + '...' : 'N/A'}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>${data.email || 'N/A'}</td></tr>
                            <tr><td><strong>Full Name:</strong></td><td>${data.full_name || 'N/A'}</td></tr>
                            <tr><td><strong>Role:</strong></td><td>${data.role}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Added By:</strong></td><td>${data.added_by || 'N/A'}</td></tr>
                            <tr><td><strong>Created At:</strong></td><td>${data.created_at || 'N/A'}</td></tr>
                            <tr><td><strong>Updated At:</strong></td><td>${data.updated_at || 'N/A'}</td></tr>
                            <tr><td><strong>Expire:</strong></td><td>${data.expire || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            $('#userModalBody').html(html);
            $('#userModal').modal('show');
        }
    });
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        $.post('ajax/user_ajax.php', {action: 'delete', id: id}, function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                loadUsers();
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
    loadUsers();
    
    // Search functionality
    $('input[name="table_search"]').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#userTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        $.post('ajax/user_ajax.php', $(this).serialize() + '&action=save', function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                $('#userFormModal').modal('hide');
                loadUsers();
            } else {
                showAlert('error', response.message);
            }
        }, 'json');
    });
});
</script>

<?php include 'inc/footer.php'; ?>