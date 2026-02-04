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
                    <h1><i class="fas fa-x-ray mr-2"></i>X-Ray Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">X-Ray Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3 align-items-center">
                <div class="col-md-8">
                    <div class="text-muted small" id="xrayLastUpdated">Last updated: --</div>
                </div>
                <div class="col-md-4 text-md-right">
                    <a href="xray_scan.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-x-ray mr-1"></i>New Scan
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="xrayScansToday">0</h3>
                            <p>Scans Today</p>
                            <div class="stat-meta">Scheduled workflow</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-procedures"></i>
                        </div>
                        <a href="xray_scan.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="xrayCompletedReports">0</h3>
                            <p>Completed Reports</p>
                            <div class="stat-meta">Ready to print</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-medical-alt"></i>
                        </div>
                        <a href="xray_print.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="xrayPendingReports">0</h3>
                            <p>Pending Reports</p>
                            <div class="stat-meta">Awaiting review</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <a href="xray_print.php" class="small-box-footer">Review queue <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="xrayEquipmentStatus">Online</h3>
                            <p>Equipment Status</p>
                            <div class="stat-meta">All units ready</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <a href="xray_scan.php" class="small-box-footer">Run diagnostics <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-1"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="xray_scan.php" class="btn btn-app btn-block">
                                        <i class="fas fa-x-ray"></i>
                                        New Scan
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="xray_print.php" class="btn btn-app btn-block">
                                        <i class="fas fa-print"></i>
                                        Print Report
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="xray_scan.php" class="btn btn-app btn-block">
                                        <i class="fas fa-clipboard-check"></i>
                                        Verify Images
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="xray_print.php" class="btn btn-app btn-block">
                                        <i class="fas fa-file-medical"></i>
                                        Review Queue
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-1"></i>
                                Recent Scans
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Body Part</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4">
                                                <div class="empty-table-state">
                                                    <i class="fas fa-x-ray"></i>
                                                    <div>No scans recorded yet.</div>
                                                    <a href="xray_scan.php" class="btn btn-sm btn-outline-primary mt-2">Start a scan</a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-notes-medical mr-1"></i>
                                Workflow Tips
                            </h3>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-3">
                                    <strong>Prepare patient:</strong>
                                    confirm identifiers before scanning.
                                </li>
                                <li class="mb-3">
                                    <strong>Capture angles:</strong>
                                    take at least two views for accuracy.
                                </li>
                                <li>
                                    <strong>Review quickly:</strong>
                                    mark urgent findings for immediate print.
                                </li>
                            </ul>
                            <a href="xray_print.php" class="btn btn-outline-primary btn-block mt-3">Open Print Queue</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Page specific custom styles -->
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
.small-box-footer {
    display: block;
    padding: 3px 0;
    color: rgba(255,255,255,0.8);
    text-align: center;
    text-decoration: none;
    background: rgba(0,0,0,0.1);
}
.small-box-footer:hover {
    color: #fff;
    background: rgba(0,0,0,0.15);
}
.stat-meta {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.85);
}
.btn-app {
    border-radius: 3px;
    position: relative;
    padding: 15px 5px;
    margin: 0;
    min-width: 80px;
    height: 60px;
    text-align: center;
    color: #666;
    border: 1px solid #ddd;
    background-color: #f4f4f4;
    font-size: 12px;
}
.btn-app:hover {
    background: #f4f4f4;
    color: #444;
    border-color: #aaa;
}
.btn-app > .fa, .btn-app > .fas, .btn-app > .far, .btn-app > .fab, .btn-app > .ion {
    font-size: 20px;
    display: block;
}
.empty-table-state {
    padding: 2rem;
    text-align: center;
    color: #6c757d;
}
.empty-table-state i {
    font-size: 1.75rem;
    color: #adb5bd;
    margin-bottom: 0.5rem;
}
</style>

<script>
$(document).ready(function() {
    const now = new Date();
    const formatted = now.toLocaleString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    $('#xrayLastUpdated').text(`Last updated: ${formatted}`);
});
</script>

<?php require_once 'inc/footer.php'; ?>
