<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user-md mr-2"></i>OPD Doctor Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">OPD Doctors</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row mb-4">
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalDoctors">0</h3>
                            <p>Total Doctors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="activeDoctors">0</h3>
                            <p>Active</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="inactiveDoctors">0</h3>
                            <p>Inactive</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="specializations">0</h3>
                            <p>Specializations</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3 id="hospitals">0</h3>
                            <p>Hospitals</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users mr-1"></i>
                                OPD Doctors Directory
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" id="addDoctorBtn">
                                    <i class="fas fa-plus"></i> Add New Doctor
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Doctors Table -->
                            <div class="table-responsive">
                                <table id="opdDoctorTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Name</th>
                                            <th>Specialization</th>
                                            <th>Hospital</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add/Edit Doctor Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add New OPD Doctor</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="doctorForm">
                <div class="modal-body">
                    <input type="hidden" id="doctorId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorName">
                                    <i class="fas fa-user mr-1"></i>
                                    Doctor Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="doctorName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorQualification">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    Qualification
                                </label>
                                <input type="text" class="form-control" id="doctorQualification" name="qualification">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorUsername">
                                    <i class="fas fa-user-circle mr-1"></i>
                                    Username <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="doctorUsername" name="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorPassword">
                                    <i class="fas fa-lock mr-1"></i>
                                    Password
                                </label>
                                <input type="password" class="form-control" id="doctorPassword" name="password">
                                <small class="form-text text-muted">Leave blank to keep current password (for edit)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorSpecialization">
                                    <i class="fas fa-stethoscope mr-1"></i>
                                    Specialization
                                </label>
                                <input type="text" class="form-control" id="doctorSpecialization" name="specialization">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorHospital">
                                    <i class="fas fa-hospital mr-1"></i>
                                    Hospital
                                </label>
                                <input type="text" class="form-control" id="doctorHospital" name="hospital">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorContact">
                                    <i class="fas fa-phone mr-1"></i>
                                    Contact Number
                                </label>
                                <input type="text" class="form-control" id="doctorContact" name="contact_no">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorPhone">
                                    <i class="fas fa-mobile-alt mr-1"></i>
                                    Phone
                                </label>
                                <input type="text" class="form-control" id="doctorPhone" name="phone">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorEmail">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control" id="doctorEmail" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorRegistration">
                                    <i class="fas fa-id-card mr-1"></i>
                                    Registration Number
                                </label>
                                <input type="text" class="form-control" id="doctorRegistration" name="registration_no">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorAddress">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    Address
                                </label>
                                <textarea class="form-control" id="doctorAddress" name="address" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorStatus">
                                    <i class="fas fa-toggle-on mr-1"></i>
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="doctorStatus" name="status" required>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Doctor Modal -->
<div class="modal fade" id="viewDoctorModal" tabindex="-1" role="dialog" aria-labelledby="viewDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>
                    OPD Doctor Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewDoctorContent">
                <!-- Doctor details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-warning" onclick="editDoctorFromView()">
                    <i class="fas fa-edit"></i> Edit Doctor
                </button>
                <button type="button" class="btn btn-info" onclick="printDoctorDetails()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- Page specific JavaScript -->
<script src="assets/js/opd_doctor.js?v=<?php echo time(); ?>"></script>

<script>
// Fix for duplicate status field issue
$(document).ready(function() {
    // Clean up any duplicate elements when modal is shown
    /*
    $('#doctorModal').on('show.bs.modal', function() {
        // Remove any duplicate labels within form groups
        $('#doctorForm .form-group').each(function() {
            var $labels = $(this).find('label');
            if ($labels.length > 1) {
                console.log('Found duplicate labels, removing extras');
                $labels.not(':first').remove();
            }
        });
        
        // Remove any duplicate select elements
        $('#doctorForm .form-group').each(function() {
            var $selects = $(this).find('select');
            if ($selects.length > 1) {
                console.log('Found duplicate selects, removing extras');
                $selects.not(':first').remove();
            }
        });
    });
    */
    
    // Ensure form is properly reset when modal is hidden
    $('#doctorModal').on('hidden.bs.modal', function() {
        $('#doctorForm')[0].reset();
    });
    
    // Additional cleanup on page load
    setTimeout(function() {
        $('#doctorForm .form-group').each(function() {
            var $labels = $(this).find('label');
            if ($labels.length > 1) {
                $labels.not(':first').remove();
            }
        });
    }, 100);
});
</script>

<style>
.small-box {
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 0.25rem;
}

.small-box > .inner {
    padding: 10px;
}

.small-box .icon {
    transition: all .3s linear;
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 90px;
    color: rgba(0,0,0,0.15);
}

.table-responsive {
    border-radius: 0.375rem;
    overflow-x: auto;
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

#opdDoctorTable {
    width: 100% !important;
    white-space: nowrap;
}

#opdDoctorTable thead th {
    vertical-align: middle;
    white-space: nowrap;
    padding: 12px 8px;
    font-size: 14px;
}

#opdDoctorTable tbody td {
    vertical-align: middle;
    padding: 8px;
    font-size: 13px;
}

#opdDoctorTable_wrapper .dataTables_scroll {
    overflow-x: auto;
}

/* Ensure proper form rendering */
#doctorModal .form-group {
    margin-bottom: 1rem;
    clear: both;
}

#doctorModal label {
    display: block;
    margin-bottom: 0.5rem;
}

/* Enhanced Status Badge Styling */
.badge-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    color: #fff !important;
    padding: 0.5rem 0.75rem !important;
    border-radius: 20px !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.badge-success i {
    font-size: 0.9rem !important;
}

.badge-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%) !important;
    color: #fff !important;
    padding: 0.5rem 0.75rem !important;
    border-radius: 20px !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.badge-danger i {
    font-size: 0.9rem !important;
}

/* Hover effects for status badges */
.badge-success:hover {
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4) !important;
    transform: translateY(-1px) !important;
}

.badge-danger:hover {
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4) !important;
    transform: translateY(-1px) !important;
}

/* Status toggle buttons styling */
.toggle-status-btn {
    transition: all 0.3s ease !important;
}

.toggle-status-btn:hover {
    transform: scale(1.1) !important;
}

/* Active status - Green theme */
.badge-success {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%) !important;
}

/* Inactive status - Red theme */
.badge-danger {
    background: linear-gradient(135deg, #ef4444 0%, #f87171 100%) !important;
}
</style>

<?php require_once 'inc/footer.php'; ?>
