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
                                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Add User</span>
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="exportUsers()">
                                    <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Export</span>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            
                            <!-- Group Actions -->
                            <div class="group-actions">
                                <div class="row align-items-center">
                                    <div class="col-md-6 col-sm-12 col-lg-6">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary" onclick="selectAllUsers()">
                                                <i class="fas fa-check-square"></i> <span class="d-none d-sm-inline">Select All</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="deselectAllUsers()">
                                                <i class="fas fa-square"></i> <span class="d-none d-sm-inline">Deselect All</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-lg-6">
                                        <div class="btn-group ml-2" role="group">
                                            <button type="button" class="btn btn-outline-info" onclick="bulkExportUsers()">
                                                <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Export Selected</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="bulkDeleteUsers()">
                                                <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Delete Selected</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-right">
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
                                            <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Export</span>
                                        </button>
                                        <button class="btn btn-sm btn-danger bulk-delete">
                                            <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6 col-sm-12">
                                    <div class="input-group">
                                        <input id="usersSearch" class="form-control" placeholder="Search users by username, email or name...">
                                        <div class="input-group-append">
                                            <button id="usersSearchClear" class="btn btn-outline-secondary"><span class="d-none d-sm-inline">Clear</span></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="usersPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6 text-right">
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-sm btn-info bulk-export">
                                            <i class="fas fa-download"></i> <span class="d-none d-sm-inline">Export</span>
                                        </button>
                                        <button class="btn btn-sm btn-danger bulk-delete">
                                            <i class="fas fa-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                                        </button>
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
                                        <th>User Type</th>
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
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
                                    <label for="userType">User Type *</label>
                                    <select class="form-control" id="userType" name="user_type" required>
                                        <option value="Pathology">Pathology</option>
                                        <option value="Hospital">Hospital</option>
                                        <option value="School">School</option>
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
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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

<style>
/* Responsive Design Improvements for User Management Page */

/* Mobile Responsive Styles */
@media (max-width: 576px) {
    /* Header Mobile Adjustments */
    .content-header {
        padding: 15px 0;
    }
    
    .content-header h1 {
        font-size: 1.5rem;
    }
    
    /* Card Mobile Adjustments */
    .card {
        margin-bottom: 20px;
        border-radius: 8px;
    }
    
    .card-header {
        padding: 12px 15px;
    }
    
    .card-header h3 {
        font-size: 1rem;
    }
    
    .card-body {
        padding: 15px;
    }
    
    /* Button Mobile Improvements */
    .btn {
        min-height: 44px;
        font-size: 0.9rem;
        margin-bottom: 10px;
    }
    
    .btn-sm {
        min-height: 38px;
        font-size: 0.8rem;
    }
    
    .btn-group {
        margin-bottom: 10px;
    }
    
    .card-tools {
        margin-bottom: 10px;
    }
    
    /* Form Mobile Adjustments */
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-label {
        font-size: 0.8rem;
        margin-bottom: 5px;
    }
    
    .form-control {
        font-size: 0.9rem;
        min-height: 44px;
    }
    
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    /* Table Mobile Adjustments */
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .table th {
        font-size: 0.75rem;
        padding: 0.5rem;
    }
    
    .table td {
        padding: 0.5rem;
        vertical-align: middle;
    }
    
    /* Group Actions Mobile Adjustments */
    .group-actions {
        margin-bottom: 15px;
    }
    
    .bulk-actions {
        margin-bottom: 15px;
    }
    
    .selected-count {
        font-size: 0.8rem;
    }
    
    /* Input Group Mobile Adjustments */
    .input-group {
        margin-bottom: 15px;
    }
    
    .input-group-append .btn {
        min-height: 44px;
        font-size: 0.8rem;
    }
    
    /* Modal Mobile Adjustments */
    .modal-dialog {
        margin: 10px;
        max-width: calc(100% - 20px);
    }
    
    .modal-content {
        border-radius: 8px;
    }
    
    .modal-header {
        padding: 15px;
        border-radius: 8px 8px 0 0;
    }
    
    .modal-body {
        padding: 15px;
    }
    
    .modal-footer {
        padding: 10px 15px;
    }
    
    /* View Details Mobile Adjustments */
    .view-details {
        font-size: 0.8rem;
        line-height: 1.4;
    }
}

@media (max-width: 768px) {
    /* Tablet Adjustments */
    .card {
        margin-bottom: 25px;
    }
    
    .card-header {
        padding: 15px 20px;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        font-size: 0.85rem;
    }
    
    .form-control {
        font-size: 0.95rem;
    }
    
    .btn {
        margin-bottom: 15px;
    }
    
    .btn-sm {
        min-height: 40px;
        font-size: 0.85rem;
    }
    
    .btn-group {
        margin-bottom: 15px;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .table th {
        font-size: 0.8rem;
    }
    
    .table td {
        padding: 0.75rem;
    }
    
    .modal-dialog {
        max-width: 95%;
        margin: 15px auto;
    }
    
    .modal-content {
        border-radius: 8px;
    }
    
    .modal-header {
        padding: 20px;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-footer {
        padding: 15px 20px;
    }
    
    .view-details {
        font-size: 0.85rem;
        line-height: 1.4;
    }
}

@media (max-width: 992px) {
    /* Small Desktop Adjustments */
    .card-body {
        padding: 25px;
    }
    
    .form-control {
        font-size: 1rem;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .table th {
        font-size: 0.8rem;
    }
    
    .table td {
        padding: 1rem;
    }
    
    .modal-body {
        padding: 25px;
    }
    
    .view-details {
        font-size: 0.9rem;
        line-height: 1.4;
    }
}

/* Enhanced Hover Effects */
.card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.card:hover .card-header {
    background-color: rgba(0,0,0,0.05);
}

/* Button Improvements */
.btn {
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Table Improvements */
.table {
    margin-bottom: 0;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-top: none;
}

.table tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* Form Input Focus Effects */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Modal Improvements */
.modal-content {
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.modal-header {
    border-radius: 8px 8px 0 0;
}

/* Alert Improvements */
.alert {
    border-radius: 6px;
    border-left: 4px solid transparent;
}

/* Badge Improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.375em 0.75em;
    border-radius: 6px;
}

/* Loading State */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Grid Improvements */
@media (max-width: 576px) {
    .row {
        margin-bottom: 20px;
    }
    
    .col-sm-6 {
        padding-left: 7.5px;
        padding-right: 7.5px;
        margin-bottom: 15px;
    }
    
    .col-md-6 {
        margin-bottom: 15px;
    }
    
    .col-md-12 {
        margin-bottom: 15px;
    }
    
    .col-md-3 {
        margin-bottom: 15px;
    }
}

/* Responsive Typography */
@media (max-width: 576px) {
    h1 {
        font-size: 1.5rem;
    }
    
    h3 {
        font-size: 1.2rem;
    }
    
    h5 {
        font-size: 1.1rem;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .card:hover {
        transform: none;
    }
    
    .btn:hover {
        transform: none;
    }
    
    .btn-group .btn {
        margin-bottom: 8px;
    }
}
</style>

<?php require_once 'inc/footer.php'; ?>

<!-- Page specific JavaScript -->
<script src="assets/js/user.js?v=<?php echo time(); ?>"></script>

<script>
// Fallback sidebar toggle functionality
$(document).ready(function() {
    // Handle sidebar toggle button
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const body = $('body');
        const sidebar = $('.main-sidebar');
        
        if (body.hasClass('sidebar-collapse')) {
            body.removeClass('sidebar-collapse').addClass('sidebar-open');
            sidebar.removeClass('sidebar-collapse').addClass('sidebar-open');
        } else {
            body.removeClass('sidebar-open').addClass('sidebar-collapse');
            sidebar.removeClass('sidebar-open').addClass('sidebar-collapse');
        }
        
        console.log('Sidebar toggle clicked');
    });
    
    // Handle window resize for responsive sidebar
    $(window).on('resize', function() {
        const width = $(window).width();
        const body = $('body');
        
        if (width <= 991) {
            body.addClass('sidebar-collapse');
        } else {
            body.removeClass('sidebar-collapse');
        }
    });
});
</script>