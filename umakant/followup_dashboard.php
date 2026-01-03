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
                    <h1><i class="fas fa-chart-line mr-2"></i>Followup Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Followup Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Quick Stats Row -->
            <div class="row">
                <!-- Total Clients -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalClients">0</h3>
                            <p>Total Clients</p>
                            <small class="text-white">Active Database</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="followup_client.php" class="small-box-footer">
                            View Clients <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Today's Followups -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="todayFollowups">0</h3>
                            <p>Today's Followups</p>
                            <small class="text-dark">Due Today</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <a href="followup_client.php?filter=today" class="small-box-footer" style="color: #1f2d3d !important;">
                            View List <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Overdue Followups -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="overdueFollowups">0</h3>
                            <p>Overdue Followups</p>
                            <small class="text-white">Requires Attention</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <a href="followup_client.php?filter=overdue" class="small-box-footer">
                            View List <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Templates -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalTemplates">0</h3>
                            <p>Message Templates</p>
                            <small class="text-white">Ready for Use</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <a href="followup_templates.php" class="small-box-footer">
                            Manage Templates <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Urgent & Recent Row -->
            <div class="row">
                <!-- Urgent Followups Table -->
                <div class="col-lg-7">
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clock mr-1"></i>
                                Urgent Followups (Overdue & Today)
                            </h3>
                            <div class="card-tools">
                                <a href="followup_client.php" class="btn btn-tool btn-sm">
                                    <i class="fas fa-bars"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="urgentFollowupsTable">
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-spinner fa-spin"></i> Loading...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity / Info Box -->
                <div class="col-lg-5">
                    <!-- Info Box -->
                    <div class="info-box bg-gradient-primary mb-3">
                        <span class="info-box-icon"><i class="far fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Upcoming (Next 7 Days)</span>
                            <span class="info-box-number" id="upcomingFollowups">0</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 100%"></div>
                            </div>
                            <span class="progress-description">
                                Scheduled for next week
                            </span>
                        </div>
                    </div>

                    <!-- Recently Updated Clients -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-1"></i>
                                Recently Updated
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="products-list product-list-in-card pl-2 pr-2" id="recentActivityList">
                                <!-- Loaded via JS -->
                                <div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading...</div>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <a href="followup_client.php" class="uppercase">View All Clients</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-1"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="followup_client.php?action=add" class="btn btn-app btn-block bg-light">
                                        <i class="fas fa-user-plus text-primary"></i>
                                        Add New Client
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="followup_templates.php" class="btn btn-app btn-block bg-light">
                                        <i class="fas fa-edit text-success"></i>
                                        Create Template
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <a href="followup_client.php" class="btn btn-app btn-block bg-light">
                                        <i class="fas fa-list text-info"></i>
                                        Client List
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <button class="btn btn-app btn-block bg-light" onclick="window.location.reload()">
                                        <i class="fas fa-sync-alt text-secondary"></i>
                                        Refresh Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Page specific JavaScript -->
<script src="assets/js/followup_dashboard.js?v=<?php echo time(); ?>"></script>

<style>
.small-box {
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    position: relative;
    display: block;
    overflow: hidden;
}
.small-box > .inner {
    padding: 20px;
}
.small-box h3 {
    font-size: 2.2rem;
    font-weight: 700;
    margin: 0 0 10px 0;
    white-space: nowrap;
    padding: 0;
}
.small-box p {
    font-size: 1rem;
    margin-bottom: 5px;
}
.small-box .icon {
    color: rgba(0,0,0,0.15);
    z-index: 0;
    top: 10px;
    right: 15px;
    font-size: 70px;
    position: absolute;
    transition: all .3s linear;
}
.small-box:hover .icon {
    transform: scale(1.1);
}
.small-box-footer {
    position: relative;
    text-align: center;
    padding: 8px 0;
    color: rgba(255, 255, 255, 0.8);
    display: block;
    z-index: 10;
    background: rgba(0, 0, 0, 0.1);
    text-decoration: none;
    transition: all 0.2s;
}
.small-box-footer:hover {
    color: #fff;
    background: rgba(0, 0, 0, 0.15);
}

.product-list-in-card > .item {
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
}
.product-list-in-card > .item:last-of-type {
    border-bottom: none;
}
.product-title {
    font-weight: 600;
}
.product-description {
    display: block;
    color: #6c757d;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
.btn-app {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin: 0;
    min-width: 80px;
    padding: 15px 5px;
    text-align: center;
    background-color: #f8f9fa;
    color: #444;
    border: 1px solid #e9ecef;
    font-size: 12px;
    transition: all 0.2s;
}
.btn-app:hover {
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
    border-color: #dee2e6;
}
</style>

<?php require_once 'inc/footer.php'; ?>
