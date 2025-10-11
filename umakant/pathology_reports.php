<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-file-medical-alt mr-2"></i>Pathology Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active">Pathology Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-search mr-1"></i>Search Filters</h3>
                </div>
                <div class="card-body">
                    <form id="pathologyReportFilters" class="row g-3">
                        <div class="col-md-3">
                            <label for="filterTest" class="form-label">Test</label>
                            <select id="filterTest" name="test_id" class="form-control select2" data-placeholder="All Tests">
                                <option value="">All Tests</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterDoctor" class="form-label">Doctor</label>
                            <select id="filterDoctor" name="doctor_id" class="form-control select2" data-placeholder="All Doctors">
                                <option value="">All Doctors</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterStatus" class="form-label">Status</label>
                            <select id="filterStatus" name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filterFrom" class="form-label">From Date</label>
                            <input type="date" id="filterFrom" name="date_from" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="filterTo" class="form-label">To Date</label>
                            <input type="date" id="filterTo" name="date_to" class="form-control">
                        </div>
                        <div class="col-md-12 mt-3 d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search mr-1"></i>Search</button>
                            <button type="button" id="resetReportFilters" class="btn btn-secondary"><i class="fas fa-undo mr-1"></i>Reset</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-outline card-secondary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-table mr-1"></i>Report Results</h3>
                    <small class="text-muted" id="reportSummary">Showing latest records</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pathologyReportsTable" class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Entry #</th>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Test</th>
                                    <th>Result</th>
                                    <th>Status</th>
                                    <th>Amount (₹)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'inc/footer.php'; ?>
<script>
    const pathologyReportsConfig = {
        currentUserId: <?php echo json_encode($_SESSION['user_id'] ?? null); ?>,
        currentUserRole: <?php echo json_encode($_SESSION['role'] ?? 'user'); ?>
    };
</script>
<script src="assets/js/pathology_reports.js"></script>
