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
                    <h1>Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
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
                            <h3 class="card-title">User Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#userModal" onclick="openAddUserModal()">
                                    <i class="fas fa-plus"></i> Add User
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
                                        <input id="usersSearch" class="form-control" placeholder="Search users by username, email or name...">
                                        <div class="input-group-append">
                                            <button id="usersSearchClear" class="btn btn-outline-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto text-right">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="usersPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <table id="usersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Expire Date</th>
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

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
                <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="id">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="userUsername">Username *</label>
                                    <input type="text" class="form-control" id="userUsername" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="userPassword">Password *</label>
                                    <input type="password" class="form-control" id="userPassword" name="password" required>
                                    <small class="form-text text-muted">Leave blank to keep current password when editing</small>
                                </div>
                                <div class="form-group">
                                    <label for="userFullName">Full Name *</label>
                                    <input type="text" class="form-control" id="userFullName" name="full_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="userEmail">Email</label>
                                    <input type="email" class="form-control" id="userEmail" name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="userRole">Role *</label>
                                    <select class="form-control" id="userRole" name="role" required>
                                        <?php
                                        $curRole = $_SESSION['role'] ?? 'user';
                                        if ($curRole === 'master') {
                                            echo "<option value=\"master\">Master</option>";
                                            echo "<option value=\"admin\">Admin</option>";
                                            echo "<option value=\"user\">User</option>";
                                        } elseif ($curRole === 'admin') {
                                            echo "<option value=\"admin\">Admin</option>";
                                            echo "<option value=\"user\">User</option>";
                                        } else {
                                            echo "<option value=\"master\">Master</option>";
                                            echo "<option value=\"admin\">Admin</option>";
                                            echo "<option value=\"user\">User</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="userIsActive">Status *</label>
                                    <select class="form-control" id="userIsActive" name="is_active" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="userExpireDateDisplay">Expire Date</label>
                                    <input type="datetime-local" class="form-control" id="userExpireDateDisplay">
                                    <input type="hidden" id="userExpireDate" name="expire_date">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">Save User</button>
            </div>
        </div>
    </div>
</div>

<!-- Page specific CSS -->
<link rel="stylesheet" href="assets/css/user.css">

<?php require_once 'inc/footer.php'; ?>

<!-- Page specific JavaScript -->
<script src="assets/js/user.js"></script>