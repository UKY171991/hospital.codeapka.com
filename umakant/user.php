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
                                <button type="button" class="btn btn-success btn-sm" onclick="exportUsers()">
                                    <i class="fas fa-download"></i> Export
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
                            
                            <!-- Group Actions -->
                            <div class="group-actions">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary" onclick="selectAllUsers()">
                                                <i class="fas fa-check-square"></i> Select All
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="deselectAllUsers()">
                                                <i class="fas fa-square"></i> Deselect All
                                            </button>
                                        </div>
                                        <div class="btn-group ml-2" role="group">
                                            <button type="button" class="btn btn-outline-info" onclick="bulkExportUsers()">
                                                <i class="fas fa-download"></i> Export Selected
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="bulkDeleteUsers()">
                                                <i class="fas fa-trash"></i> Delete Selected
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="text-muted">Select users to perform bulk actions</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Actions Alert -->
                            <div class="bulk-actions">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-info-circle"></i>
                                        <span class="selected-count">0</span> users selected
                                    </span>
                                    <div>
                                        <button class="btn btn-sm btn-info bulk-export">
                                            <i class="fas fa-download"></i> Export
                                        </button>
                                        <button class="btn btn-sm btn-danger bulk-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>

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
                            <table id="usersTable" class="table table-enhanced">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll" class="selection-checkbox">
                                        </th>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th>Expire Date</th>
                                        <th width="120">Actions</th>
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

<!-- View User Modal -->
<div class="modal fade view-modal modal-enhanced" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">
                    <i class="fas fa-eye mr-2"></i>
                    User Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewUserContent">
                <div class="view-details" id="userViewDetails">
                    <!-- User details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-warning" onclick="editUserFromView()">
                    <i class="fas fa-edit"></i> Edit User
                </button>
                <button type="button" class="btn btn-info" onclick="printUserDetails()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Page specific CSS -->
<link rel="stylesheet" href="assets/css/user.css">

<?php require_once 'inc/footer.php'; ?>

<!-- Page specific JavaScript -->
<script src="assets/js/user.js?v=<?php echo time(); ?>"></script>