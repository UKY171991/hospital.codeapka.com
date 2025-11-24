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
                    <h1><i class="fas fa-user-injured mr-2"></i>OPD Patient Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">OPD Patients</li>
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
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalPatients">0</h3>
                            <p>Total Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="todayPatients">0</h3>
                            <p>Today's Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="weekPatients">0</h3>
                            <p>This Week</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="monthPatients">0</h3>
                            <p>This Month</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
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
                                <i class="fas fa-list mr-1"></i>
                                Patient Directory
                            </h3>
                            <div class="card-tools">
                                <div class="d-flex">
                                    <select id="filterDoctor" class="form-control form-control-sm mr-2" style="width: 200px;">
                                        <option value="">All Doctors</option>
                                    </select>
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <input type="text" id="searchPatient" class="form-control" placeholder="Search patient...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Patients Table -->
                            <div class="table-responsive">
                                <table id="opdPatientTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Patient Name</th>
                                            <th>Phone</th>
                                            <th>Age</th>
                                            <th>Gender</th>
                                            <th>Total Visits</th>
                                            <th>First Visit</th>
                                            <th>Last Visit</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patientTableBody">
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <i class="fas fa-spinner fa-spin"></i> Loading...
                                            </td>
                                        </tr>
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

<!-- View Patient History Modal -->
<div class="modal fade" id="viewPatientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-injured mr-2"></i>
                    Patient History
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="patientHistoryContent">
                    <!-- Patient history will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-info" onclick="printPatientHistory()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Page specific JavaScript -->
<script src="assets/js/opd_patient.js?v=<?php echo time(); ?>"></script>

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

#opdPatientTable {
    width: 100% !important;
}

#opdPatientTable thead th {
    vertical-align: middle;
    padding: 12px 8px;
    font-size: 14px;
}

#opdPatientTable tbody td {
    vertical-align: middle;
    padding: 8px;
    font-size: 13px;
}

.patient-card {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
}

.patient-card h5 {
    margin-bottom: 10px;
    color: #007bff;
}

.history-section {
    margin-top: 20px;
}

.history-section h6 {
    background: #f8f9fa;
    padding: 10px;
    border-left: 4px solid #007bff;
    margin-bottom: 15px;
}
</style>

<?php require_once 'inc/footer.php'; ?>
