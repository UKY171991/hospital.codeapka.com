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
                    <h1><i class="fas fa-file-medical mr-2"></i>OPD Reports Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">OPD Reports</li>
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
                            <h3 id="totalReports">0</h3>
                            <p>Total Reports</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="todayReports">0</h3>
                            <p>Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="weekReports">0</h3>
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
                            <h3 id="monthReports">0</h3>
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
                                Patient Reports
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" id="addReportBtn">
                                    <i class="fas fa-plus"></i> Add New Report
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Reports Table -->
                            <div class="table-responsive">
                                <table id="opdReportsTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Report ID</th>
                                            <th>Patient Name</th>
                                            <th>Phone</th>
                                            <th>Doctor</th>
                                            <th>Report Date</th>
                                            <th>Diagnosis</th>
                                            <th>Follow-up</th>
                                            <th>Added By</th>
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

<!-- Add/Edit Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add New Report</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="reportForm">
                <div class="modal-body">
                    <input type="hidden" id="reportId" name="id">
                    
                    <!-- Patient Information -->
                    <h5 class="mb-3"><i class="fas fa-user-injured mr-2"></i>Patient Information</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="patientName">
                                    Patient Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="patientName" name="patient_name" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="patientPhone">Phone Number</label>
                                <input type="text" class="form-control" id="patientPhone" name="patient_phone">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="patientAge">Age</label>
                                <input type="number" class="form-control" id="patientAge" name="patient_age" min="0" max="150">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="patientGender">Gender</label>
                                <select class="form-control" id="patientGender" name="patient_gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Doctor & Date Information -->
                    <h5 class="mb-3"><i class="fas fa-user-md mr-2"></i>Doctor & Date</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorName">Doctor Name</label>
                                <select class="form-control" id="doctorName" name="doctor_name">
                                    <option value="">Select Doctor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="reportDate">Report Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="reportDate" name="report_date" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="followUpDate">Follow-up Date</label>
                                <input type="date" class="form-control" id="followUpDate" name="follow_up_date">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Medical Information -->
                    <h5 class="mb-3"><i class="fas fa-notes-medical mr-2"></i>Medical Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="symptoms">Symptoms</label>
                                <textarea class="form-control" id="symptoms" name="symptoms" rows="3" placeholder="Patient's symptoms..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="diagnosis">Diagnosis</label>
                                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" placeholder="Doctor's diagnosis..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="testResults">Test Results</label>
                                <textarea class="form-control" id="testResults" name="test_results" rows="3" placeholder="Lab test results..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="prescription">Prescription</label>
                                <textarea class="form-control" id="prescription" name="prescription" rows="3" placeholder="Prescribed medications..."></textarea>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Notes -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Additional Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Report Modal -->
<div class="modal fade" id="viewReportModal" tabindex="-1" role="dialog" aria-labelledby="viewReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>
                    Report Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewReportContent">
                <!-- Report details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-warning" onclick="editReportFromView()">
                    <i class="fas fa-edit"></i> Edit Report
                </button>
                <button type="button" class="btn btn-info" onclick="printReportDetails()">
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
<script src="assets/js/opd_reports.js"></script>

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

#opdReportsTable {
    width: 100% !important;
    white-space: nowrap;
}

#opdReportsTable thead th {
    vertical-align: middle;
    white-space: nowrap;
    padding: 12px 8px;
    font-size: 14px;
}

#opdReportsTable tbody td {
    vertical-align: middle;
    padding: 8px;
    font-size: 13px;
}

#opdReportsTable_wrapper .dataTables_scroll {
    overflow-x: auto;
}

#reportModal .form-group {
    margin-bottom: 1rem;
}

#reportModal label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.modal-xl {
    max-width: 1200px;
}
</style>

<!-- Page specific JavaScript -->
<script src="assets/js/opd_reports.js?v=<?php echo time(); ?>"></script>

<?php require_once 'inc/footer.php'; ?>
