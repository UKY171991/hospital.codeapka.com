<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>
?>
<div class="content-wrapper">
        <section class="content-header">
                <div class="container-fluid">
                        <div class="row mb-2">
                                <div class="col-sm-6">
                                        <h1>User List</h1>
                                </div>
                                <div class="col-sm-6 text-right">
                                        <button class="btn btn-primary" id="addUserBtn"><i class="fas fa-plus"></i> Add User</button>
                                </div>
                        </div>
                </div>
        </section>
        <section class="content">
                <div class="container-fluid">
                        <div class="card">
                                <div class="card-body">
                                        <table class="table table-bordered table-hover" id="userTable">
                                                <thead class="thead-light">
                                                        <tr>
                                                                <th>ID</th>
                                                                <th>Username</th>
                                                                <th>Email</th>
                                                                <th>Role</th>
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
        </section>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="userForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" name="full_name" id="full_name">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" id="password">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select class="form-control" name="role" id="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadUsers() {
                $.get('ajax/user_ajax.php', {action: 'list'}, function(data) {
                        // Add Delete button to each row
                        data = data.replace(/(<\/td>\s*<\/tr>)/g, function(match, p1, offset, string) {
                                var idMatch = string.substring(0, offset).match(/data-id=\"(\d+)\"/);
                                var id = idMatch ? idMatch[1] : '';
                                if (id) {
                                        return '<button class="btn btn-sm btn-danger delete-btn" data-id="' + id + '"><i class="fas fa-trash"></i> Delete</button>' + p1;
                                }
                                return p1;
                        });
                        $('#userTable tbody').html(data);
                });
}

$(function() {
        loadUsers();

        $('#addUserBtn').click(function() {
                $('#userForm')[0].reset();
                $('#userId').val('');
                $('#userModalLabel').text('Add User');
                $('#userModal').modal('show');
        });

        $('#userTable').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.get('ajax/user_ajax.php', {action: 'get', id: id}, function(user) {
                        $('#userId').val(user.id);
                        $('#username').val(user.username);
                        $('#email').val(user.email);
                        $('#full_name').val(user.full_name);
                        $('#role').val(user.role);
                        $('#password').val('');
                        $('#userModalLabel').text('Edit User');
                        $('#userModal').modal('show');
                }, 'json');
        });

                $('#userForm').submit(function(e) {
                        e.preventDefault();
                        $.post('ajax/user_ajax.php', $(this).serialize() + '&action=save', function(resp) {
                                $('#userModal').modal('hide');
                                loadUsers();
                        });
                });

                        $('#userTable').on('click', '.delete-btn', function() {
                                if (confirm('Are you sure you want to delete this user?')) {
                                        var id = $(this).data('id');
                                        $.ajax({
                                                url: 'ajax/user_ajax.php',
                                                type: 'POST',
                                                data: {action: 'delete', id: id},
                                                success: function(resp) {
                                                        loadUsers();
                                                        alert('User deleted successfully!');
                                                },
                                                error: function(xhr, status, error) {
                                                        alert('Delete failed: ' + error);
                                                }
                                        });
                                }
                        });
});
</script>
<?php include 'inc/footer.php'; ?>
