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
                    <h1><i class="fas fa-tachometer-alt mr-2"></i>Hospital Management Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Module Navigation -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-th mr-2"></i>Quick Access</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-4 col-6 mb-3">
                                    <a href="opd_dashboard.php" class="btn btn-app btn-block bg-info">
                                        <i class="fas fa-user-md"></i>
                                        <span class="d-none d-sm-inline-block">OPD</span>
                                        <span class="d-sm-none">OPD</span>
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6 mb-3">
                                    <a href="pathology_reports.php" class="btn btn-app btn-block bg-success">
                                        <i class="fas fa-flask"></i>
                                        <span class="d-none d-sm-inline-block">Pathology</span>
                                        <span class="d-sm-none">Patho</span>
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6 mb-3">
                                    <a href="clients.php" class="btn btn-app btn-block bg-warning">
                                        <i class="fas fa-users"></i>
                                        <span class="d-none d-sm-inline-block">Clients</span>
                                        <span class="d-sm-none">Clients</span>
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6 mb-3">
                                    <a href="email_inbox.php" class="btn btn-app btn-block bg-danger">
                                        <i class="fas fa-envelope"></i>
                                        <span class="d-none d-sm-inline-block">Email</span>
                                        <span class="d-sm-none">Email</span>
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6 mb-3">
                                    <a href="inventory_dashboard.php" class="btn btn-app btn-block bg-primary">
                                        <i class="fas fa-boxes"></i>
                                        <span class="d-none d-sm-inline-block">Inventory</span>
                                        <span class="d-sm-none">Stock</span>
                                    </a>
                                </div>
                                <div class="col-md-2 col-sm-4 col-6 mb-3">
                                    <a href="user.php" class="btn btn-app btn-block bg-secondary">
                                        <i class="fas fa-user-cog"></i>
                                        <span class="d-none d-sm-inline-block">Users</span>
                                        <span class="d-sm-none">Users</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OPD Module Stats -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title"><i class="fas fa-user-md mr-2"></i>OPD Module</h3>
                            <div class="card-tools">
                                <a href="opd_dashboard.php" class="btn btn-sm btn-light">View Details</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3 id="opdDoctors">0</h3>
                                            <p>Doctors</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-user-md"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3 id="opdPatients">0</h3>
                                            <p>Patients</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-user-injured"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3 id="opdReports">0</h3>
                                            <p>Reports</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-file-medical"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3 id="opdRevenue">₹0</h3>
                                            <p>Revenue</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-rupee-sign"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pathology Module Stats -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title"><i class="fas fa-flask mr-2"></i>Pathology Module</h3>
                            <div class="card-tools">
                                <a href="pathology_reports.php" class="btn btn-sm btn-light">View Details</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3 id="pathoEntries">0</h3>
                                            <p>Test Entries</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-file-medical-alt"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-6">
                                    <div class="small-box bg-teal">
                                        <div class="inner">
                                            <h3 id="pathoTests">0</h3>
                                            <p>Available Tests</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-flask"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clients & Email Row -->
            <div class="row mb-4">
                <!-- Clients Module -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title"><i class="fas fa-users mr-2"></i>Clients Module</h3>
                            <div class="card-tools">
                                <a href="clients.php" class="btn btn-sm btn-light">View Details</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Clients</span>
                                            <span class="info-box-number" id="totalClients">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Module -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-danger">
                            <h3 class="card-title"><i class="fas fa-envelope mr-2"></i>Email Module</h3>
                            <div class="card-tools">
                                <a href="email_inbox.php" class="btn btn-sm btn-light">View Details</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-box bg-danger">
                                        <span class="info-box-icon"><i class="fas fa-inbox"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Inbox</span>
                                            <span class="info-box-number" id="emailInbox">0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="info-box bg-warning">
                                        <span class="info-box-icon"><i class="fas fa-envelope-open"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Unread</span>
                                            <span class="info-box-number" id="emailUnread">0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Module Stats -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Inventory Module</h3>
                            <div class="card-tools">
                                <a href="inventory_dashboard.php" class="btn btn-sm btn-light">View Details</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3 id="inventoryIncome">₹0</h3>
                                            <p>Total Income</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-arrow-up"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3 id="inventoryExpense">₹0</h3>
                                            <p>Total Expense</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-arrow-down"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-12">
                                    <div class="small-box bg-primary">
                                        <div class="inner">
                                            <h3 id="inventoryBalance">₹0</h3>
                                            <p>Balance</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-balance-scale"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </section>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Dashboard JavaScript -->
<script>
// Main Hospital Dashboard
$(document).ready(function() {
    loadDashboardData();
    
    // Auto-refresh every 5 minutes
    setInterval(loadDashboardData, 300000);
});

function loadDashboardData() {
    loadOverviewStats();
    loadTodayStats();
}

function loadOverviewStats() {
    $.ajax({
        url: 'ajax/main_dashboard_api.php',
        type: 'GET',
        data: { action: 'overview' },
        success: function(response) {
            if (response.success && response.data) {
                const data = response.data;
                
                // OPD Stats
                if (data.opd) {
                    $('#opdDoctors').text(data.opd.doctors || 0);
                    $('#opdPatients').text(data.opd.patients || 0);
                    $('#opdReports').text(data.opd.reports || 0);
                    $('#opdRevenue').text('₹' + parseFloat(data.opd.revenue || 0).toLocaleString());
                }
                
                // Pathology Stats
                if (data.pathology) {
                    $('#pathoEntries').text(data.pathology.entries || 0);
                    $('#pathoTests').text(data.pathology.tests || 0);
                }
                
                // Client Stats
                if (data.clients) {
                    $('#totalClients').text(data.clients.total || 0);
                }
                
                // Email Stats
                if (data.email) {
                    $('#emailInbox').text(data.email.inbox || 0);
                    $('#emailUnread').text(data.email.unread || 0);
                }
                
                // Inventory Stats
                if (data.inventory) {
                    $('#inventoryIncome').text('₹' + parseFloat(data.inventory.income || 0).toLocaleString());
                    $('#inventoryExpense').text('₹' + parseFloat(data.inventory.expense || 0).toLocaleString());
                    const balance = parseFloat(data.inventory.balance || 0);
                    $('#inventoryBalance').text('₹' + balance.toLocaleString());
                    
                    // Change color based on balance
                    const balanceBox = $('#inventoryBalance').closest('.small-box');
                    if (balance < 0) {
                        balanceBox.removeClass('bg-primary bg-success').addClass('bg-danger');
                    } else if (balance > 0) {
                        balanceBox.removeClass('bg-primary bg-danger').addClass('bg-success');
                    }
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading overview stats:', error);
            toastr.error('Error loading dashboard data');
        }
    });
}

function loadTodayStats() {
    $.ajax({
        url: 'ajax/main_dashboard_api.php',
        type: 'GET',
        data: { action: 'today_stats' },
        success: function(response) {
            if (response.success && response.data) {
                console.log('Today stats loaded:', response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading today stats:', error);
        }
    });
}

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

<!-- Custom Dashboard Styles -->
<style>
/* Responsive Design Improvements for Dashboard Page */

/* Mobile Responsive Styles */
@media (max-width: 576px) {
    /* Header Mobile Adjustments */
    .content-header {
        padding: 15px 0;
    }
    
    .content-header h1 {
        font-size: 1.4rem;
        line-height: 1.3;
    }
    
    /* Card Mobile Adjustments */
    .card {
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .card-header {
        padding: 12px 15px;
        border-radius: 8px 8px 0 0;
    }
    
    .card-header h3 {
        font-size: 1rem;
        line-height: 1.3;
    }
    
    .card-body {
        padding: 15px;
    }
    
    /* Quick Access Buttons */
    .btn-app {
        height: 60px;
        font-size: 0.8rem;
        padding: 8px 5px;
        margin-bottom: 10px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60px;
    }
    
    .btn-app i {
        font-size: 1.2rem;
        margin-bottom: 4px;
    }
    
    .btn-app span {
        font-size: 0.7rem;
        font-weight: 500;
    }
    
    /* Small Box Mobile Adjustments */
    .small-box {
        border-radius: 8px;
        margin-bottom: 15px;
        min-height: 90px;
        position: relative;
        overflow: hidden;
    }
    
    .small-box .inner {
        padding: 15px;
        padding-right: 60px;
    }
    
    .small-box .inner h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 5px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .small-box .inner p {
        font-size: 0.8rem;
        margin: 0;
        font-weight: 500;
    }
    
    .small-box .icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.8rem;
        opacity: 0.8;
    }
    
    /* Info Box Mobile Adjustments */
    .info-box {
        border-radius: 8px;
        margin-bottom: 15px;
        min-height: 80px;
        display: flex;
        align-items: center;
        padding: 15px;
    }
    
    .info-box-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 15px;
    }
    
    .info-box-content {
        flex: 1;
    }
    
    .info-box-text {
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 5px;
    }
    
    .info-box-number {
        font-size: 1.3rem;
        font-weight: 700;
        display: block;
    }
    
    /* Row Spacing */
    .row.mb-4 {
        margin-bottom: 25px;
    }
    
    /* Button Tools */
    .card-tools .btn {
        font-size: 0.8rem;
        padding: 5px 10px;
        min-height: auto;
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
    
    .btn-app {
        height: 70px;
        font-size: 0.9rem;
        padding: 10px 8px;
    }
    
    .btn-app i {
        font-size: 1.4rem;
        margin-bottom: 5px;
    }
    
    .btn-app span {
        font-size: 0.75rem;
    }
    
    .small-box .inner {
        padding: 20px;
        padding-right: 70px;
    }
    
    .small-box .inner h3 {
        font-size: 1.8rem;
    }
    
    .small-box .inner p {
        font-size: 0.9rem;
    }
    
    .small-box .icon {
        font-size: 2rem;
        right: 20px;
    }
}

@media (max-width: 992px) {
    /* Small Desktop Adjustments */
    .card-body {
        padding: 25px;
    }
    
    .small-box .inner h3 {
        font-size: 2rem;
    }
    
    .info-box-number {
        font-size: 1.5rem;
    }
}

/* Enhanced Hover Effects */
.small-box {
    transition: all 0.3s ease;
    cursor: pointer;
}

.small-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.info-box {
    transition: all 0.3s ease;
}

.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.12);
}

.btn-app {
    transition: all 0.3s ease;
    border: none;
    color: white;
    text-decoration: none;
}

.btn-app:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
    color: white;
    text-decoration: none;
}

.btn-app:active {
    transform: translateY(0);
}

/* Card Improvements */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.card:hover .card-header {
    background-color: rgba(0,0,0,0.05);
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
    
    .col-6 {
        padding-left: 7.5px;
        padding-right: 7.5px;
        margin-bottom: 15px;
    }
    
    .col-lg-3,
    .col-lg-4,
    .col-lg-6 {
        margin-bottom: 15px;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .small-box:hover {
        transform: none;
    }
    
    .btn-app:hover {
        transform: none;
    }
    
    .card:hover {
        transform: none;
    }
}

/* Original Styles */
.small-box {
    border-radius: 10px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    margin-bottom: 20px;
}

.info-box {
    border-radius: 10px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    margin-bottom: 20px;
}

.card {
    border-radius: 10px;
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
}

.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 4px;
    background: #dee2e6;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}

.timeline > div {
    position: relative;
    margin-right: 10px;
    margin-bottom: 15px;
}

.timeline > div > .timeline-item {
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 3px;
    margin-top: 0;
    background: #fff;
    color: #444;
    margin-left: 60px;
    margin-right: 15px;
    padding: 0;
    position: relative;
}

.timeline > div > .fas {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #f4f4f4;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}

.timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 10px 15px;
    font-size: 16px;
    line-height: 1.1;
}

.timeline-body {
    padding: 10px 15px;
}

.time-label > span {
    font-weight: 600;
    padding: 5px 10px;
    display: inline-block;
    background-color: #fff;
    border-radius: 4px;
}

#dashboard-loading {
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>

<?php require_once 'inc/footer.php'; ?>