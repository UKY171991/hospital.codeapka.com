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
            
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>0</h3>
                            <p>Scans Today</p>
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
                            <h3>0</h3>
                            <p>Completed Reports</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-medical-alt"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                            </div>
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
</style>

<?php require_once 'inc/footer.php'; ?>
