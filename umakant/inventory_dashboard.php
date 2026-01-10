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
                    <h1><i class="fas fa-chart-line mr-2"></i>Inventory Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Inventory Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Filter Row -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="filterForm" class="row align-items-end">
                        <div class="col-md-3 col-sm-6">
                            <label for="filterYear">Year</label>
                            <select id="filterYear" class="form-control">
                                <?php
                                $startYear = 2020;
                                $currentYear = date('Y');
                                for ($y = $currentYear; $y >= $startYear; $y--) {
                                    echo "<option value='$y'>$y</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label for="filterMonth">Month (Optional)</label>
                            <select id="filterMonth" class="form-control">
                                <option value="">Full Year</option>
                                <?php
                                for ($m = 1; $m <= 12; $m++) {
                                    $monthName = date('F', mktime(0, 0, 0, $m, 1));
                                    echo "<option value='$m'>$monthName</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <button type="button" id="applyFilters" class="btn btn-primary btn-block">
                                <i class="fas fa-filter mr-1"></i> <span class="d-none d-sm-inline">Apply</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <button type="button" id="resetFilters" class="btn btn-secondary btn-block">
                                <i class="fas fa-undo mr-1"></i> <span class="d-none d-sm-inline">Reset</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalIncome">₹0</h3>
                            <p>Total Income</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <a href="inventory_income.php" class="small-box-footer">
                            <span class="d-none d-sm-inline">More info</span> <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="totalExpense">₹0</h3>
                            <p>Total Expense</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <a href="inventory_expense.php" class="small-box-footer">
                            <span class="d-none d-sm-inline">More info</span> <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="netProfit">₹0</h3>
                            <p>Net Profit</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            <span class="d-none d-sm-inline">More info</span> <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="totalClients">0</h3>
                            <p>Total Clients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="inventory_client.php" class="small-box-footer">
                            <span class="d-none d-sm-inline">More info</span> <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Amount Card -->
            <div class="row" id="pendingIncomeCard" style="display: none;">
                <div class="col-lg-12">
                    <div class="card bg-warning">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <i class="fas fa-clock fa-4x text-white"></i>
                                </div>
                                <div class="col-md-8">
                                    <h3 class="text-white mb-0">Pending Income Amount</h3>
                                    <h1 class="text-white font-weight-bold" id="pendingAmount">₹0</h1>
                                    <p class="text-white mb-0">Total pending income awaiting payment</p>
                                </div>
                                <div class="col-md-2 text-right">
                                    <a href="inventory_income.php" class="btn btn-light btn-lg">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Period Statistics -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Today</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <small>Income</small>
                                    <h4 class="text-success" id="todayIncome">₹0</h4>
                                </div>
                                <div class="col-6">
                                    <small>Expense</small>
                                    <h4 class="text-danger" id="todayExpense">₹0</h4>
                                </div>
                            </div>
                            <hr>
                            <small>Net: <strong id="todayNet">₹0</strong></small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title" id="monthStatsTitle">This Month</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <small>Income</small>
                                    <h4 class="text-success" id="monthIncome">₹0</h4>
                                    <small class="text-muted">Target: <span id="monthIncomeTarget">₹0</span></small>
                                </div>
                                <div class="col-6">
                                    <small>Expense</small>
                                    <h4 class="text-danger" id="monthExpense">₹0</h4>
                                    <small class="text-muted">Target: <span id="monthExpenseTarget">₹0</span></small>
                                </div>
                            </div>
                            <hr>
                            <small>Net: <strong id="monthNet">₹0</strong></small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title" id="yearStatsTitle">This Year</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <small>Income</small>
                                    <h4 class="text-success" id="yearIncome">₹0</h4>
                                    <small class="text-muted">Target: <span id="yearIncomeTarget">₹0</span></small>
                                </div>
                                <div class="col-6">
                                    <small>Expense</small>
                                    <h4 class="text-danger" id="yearExpense">₹0</h4>
                                    <small class="text-muted">Target: <span id="yearExpenseTarget">₹0</span></small>
                                </div>
                            </div>
                            <hr>
                            <small>Net: <strong id="yearNet">₹0</strong></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Monthly Income vs Expense
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="incomeExpenseChart" style="min-height: 300px; height: 300px; max-height: 400px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>
                                Recent Transactions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="recentTransactionsTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="all" style="width: 80px;">Date</th>
                                            <th class="min-tablet" style="min-width: 80px;">Type</th>
                                            <th class="min-tablet-p" style="min-width: 100px;">Category</th>
                                            <th class="none" style="min-width: 150px;">Description</th>
                                            <th class="none" style="min-width: 120px;">Client</th>
                                            <th class="min-tablet-p" style="min-width: 100px;">Amount</th>
                                        </tr>
                                    </thead>
                                <tbody id="recentTransactionsBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
let incomeExpenseChart = null;

$(document).ready(function() {
    loadDashboardData();
    
    $('#applyFilters').click(function() {
        loadDashboardData();
    });

    $('#resetFilters').click(function() {
        $('#filterYear').val(new Date().getFullYear());
        $('#filterMonth').val('');
        loadDashboardData();
    });
});

function loadDashboardData() {
    const year = $('#filterYear').val();
    const month = $('#filterMonth').val();
    
    // Update titles
    if (month) {
        const monthName = $("#filterMonth option:selected").text();
        $('#monthStatsTitle').text(monthName + ' ' + year);
    } else {
        $('#monthStatsTitle').text('Monthly Average (' + year + ')');
    }
    $('#yearStatsTitle').text('Full Year ' + year);

    // Load summary statistics
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { 
            action: 'get_dashboard_stats',
            year: year,
            month: month
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                $('#totalIncome').text('₹' + formatNumber(data.total_income || 0));
                $('#totalExpense').text('₹' + formatNumber(data.total_expense || 0));
                $('#netProfit').text('₹' + formatNumber(data.net_profit || 0));
                $('#totalClients').text(data.total_clients || 0);
                
                // Show/hide pending income card based on amount
                const pendingAmount = parseFloat(data.pending_amount || 0);
                if (pendingAmount > 0) {
                    $('#pendingAmount').text('₹' + formatNumber(pendingAmount));
                    $('#pendingIncomeCard').show();
                } else {
                    $('#pendingIncomeCard').hide();
                }
                
                // Today
                $('#todayIncome').text('₹' + formatNumber(data.today_income || 0));
                $('#todayExpense').text('₹' + formatNumber(data.today_expense || 0));
                $('#todayNet').text('₹' + formatNumber((data.today_income || 0) - (data.today_expense || 0)));
                
                // Month
                $('#monthIncome').text('₹' + formatNumber(data.month_income || 0));
                $('#monthExpense').text('₹' + formatNumber(data.month_expense || 0));
                $('#monthNet').text('₹' + formatNumber((data.month_income || 0) - (data.month_expense || 0)));
                $('#monthIncomeTarget').text('₹' + formatNumber(data.month_income_target || 100000));
                $('#monthExpenseTarget').text('₹' + formatNumber(data.month_expense_target || 80000));
                
                // Year
                $('#yearIncome').text('₹' + formatNumber(data.year_income || 0));
                $('#yearExpense').text('₹' + formatNumber(data.year_expense || 0));
                $('#yearNet').text('₹' + formatNumber((data.year_income || 0) - (data.year_expense || 0)));
                $('#yearIncomeTarget').text('₹' + formatNumber(data.year_income_target || 1200000));
                $('#yearExpenseTarget').text('₹' + formatNumber(data.year_expense_target || 1000000));

                if (data.chart_data) {
                    updateChart(data.chart_data);
                }
            } else {
                console.error('Failed to load dashboard stats:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Dashboard stats error:', error);
        }
    });

    // Load recent transactions
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { action: 'get_recent_transactions', limit: 10 },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayRecentTransactions(response.data);
            } else {
                displayRecentTransactions([]);
            }
        },
        error: function(xhr, status, error) {
            displayRecentTransactions([]);
        }
    });
}

function displayRecentTransactions(transactions) {
    const tbody = $('#recentTransactionsBody');
    tbody.empty();

    if (!transactions || transactions.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">No transactions found</td></tr>');
        return;
    }

    transactions.forEach(function(trans) {
        const typeClass = trans.type === 'income' ? 'badge-success' : 'badge-danger';
        const row = `
            <tr>
                <td>${trans.date}</td>
                <td><span class="badge ${typeClass}">${trans.type.toUpperCase()}</span></td>
                <td>${trans.category}</td>
                <td>${trans.description}</td>
                <td>${trans.client_name || '-'}</td>
                <td>₹${formatNumber(trans.amount)}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

function updateChart(data) {
    const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
    
    if (incomeExpenseChart) {
        incomeExpenseChart.destroy();
    }
    
    incomeExpenseChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Income',
                data: data.income,
                backgroundColor: 'rgba(40, 167, 69, 0.7)'
            }, {
                label: 'Expense',
                data: data.expense,
                backgroundColor: 'rgba(220, 53, 69, 0.7)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    },
                    grid: {
                        display: window.innerWidth > 768
                    }
                },
                x: {
                    grid: {
                        display: window.innerWidth > 768
                    }
                }
            },
            plugins: {
                legend: {
                    position: window.innerWidth < 768 ? 'bottom' : 'top',
                    labels: {
                        boxWidth: 12,
                        padding: window.innerWidth < 768 ? 10 : 20,
                        font: {
                            size: window.innerWidth < 768 ? 11 : 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '₹' + context.parsed.y.toLocaleString();
                            return label;
                        }
                    }
                }
            }
        }
    });
}

function formatNumber(num) {
    return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
</script>

<style>
/* Responsive Design Improvements for Inventory Dashboard */

/* Mobile Responsive Styles */
@media (max-width: 576px) {
    /* Filter form mobile adjustments */
    .card-body {
        padding: 15px;
    }
    
    #filterForm .col-sm-6 {
        margin-bottom: 10px;
    }
    
    /* Summary cards mobile adjustments */
    .small-box {
        margin-bottom: 15px;
        border-radius: 8px;
    }
    
    .small-box .inner {
        padding: 15px;
    }
    
    .small-box h3 {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }
    
    .small-box p {
        font-size: 0.8rem;
        margin-bottom: 0;
    }
    
    .small-box .icon {
        top: 10px;
        right: 10px;
        font-size: 1.2rem;
        opacity: 0.8;
    }
    
    .small-box-footer {
        padding: 8px 15px;
        font-size: 0.75rem;
    }
    
    /* Pending income card mobile adjustments */
    #pendingIncomeCard .card-body {
        padding: 20px;
    }
    
    #pendingIncomeCard .col-md-2 {
        margin-bottom: 15px;
    }
    
    #pendingIncomeCard .col-md-8 {
        margin-bottom: 15px;
    }
    
    #pendingIncomeCard h3 {
        font-size: 1.25rem;
    }
    
    #pendingIncomeCard h1 {
        font-size: 1.75rem;
    }
    
    #pendingIncomeCard p {
        font-size: 0.8rem;
    }
    
    /* Statistics cards mobile adjustments */
    .col-md-6 {
        margin-bottom: 20px;
    }
    
    .card {
        margin-bottom: 15px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    .card-header h3 {
        font-size: 1rem;
    }
    
    .card-body h4 {
        font-size: 1.1rem;
    }
    
    .card-body small {
        font-size: 0.75rem;
    }
    
    /* Chart mobile adjustments */
    .chart-container {
        margin: 10px 0;
    }
    
    #incomeExpenseChart {
        max-height: 300px !important;
    }
    
    /* Recent transactions table mobile adjustments */
    .table-responsive {
        font-size: 0.8rem;
    }
    
    /* Card mobile adjustments */
    .card-header {
        padding: 12px 15px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    /* Button mobile improvements */
    .btn {
        min-height: 44px;
        font-size: 0.9rem;
    }
    
    .btn-lg {
        min-height: 48px;
        font-size: 0.9rem;
    }
    
    /* Content header mobile */
    .content-header {
        padding: 15px 0;
    }
    
    .content-header h1 {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    /* Tablet adjustments */
    .small-box {
        margin-bottom: 20px;
    }
    
    .small-box h3 {
        font-size: 1.75rem;
    }
    
    .small-box .icon {
        font-size: 1.4rem;
    }
    
    .card-body {
        padding: 20px;
    }
    
    #pendingIncomeCard .card-body {
        padding: 25px;
    }
    
    /* Statistics cards tablet adjustments */
    .col-md-6 {
        margin-bottom: 25px;
    }
    
    .card-header h3 {
        font-size: 1.1rem;
    }
    
    .card-body h4 {
        font-size: 1.2rem;
    }
    
    /* Chart tablet adjustments */
    .chart-container {
        margin: 15px 0;
    }
    
    #incomeExpenseChart {
        max-height: 400px !important;
    }
}

@media (max-width: 992px) {
    /* Small desktop adjustments */
    .card-body {
        padding: 20px;
    }
    
    #incomeExpenseChart {
        max-height: 450px !important;
    }
}

/* Enhanced Hover Effects */
.small-box {
    transition: all 0.3s ease;
    cursor: pointer;
}

.small-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.small-box:hover .icon {
    opacity: 1;
    transform: scale(1.1);
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

/* Card Improvements */
.card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
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

/* Amount styling */
.text-success {
    color: #28a745;
    font-weight: 600;
}

.text-danger {
    color: #dc3545;
    font-weight: 600;
}

/* Pending income card improvements */
.bg-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800) !important;
}

/* Chart container improvements */
.chart-container {
    position: relative;
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* Responsive Grid Improvements */
@media (max-width: 576px) {
    .row.mb-4 {
        margin-bottom: 20px;
    }
    
    .col-sm-6 {
        padding-left: 7.5px;
        padding-right: 7.5px;
    }
}

/* Loading State */
.fa-spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Typography */
@media (max-width: 576px) {
    h1 {
        font-size: 1.5rem;
    }
    
    h3 {
        font-size: 1.2rem;
    }
    
    h4 {
        font-size: 1.1rem;
    }
    
    h6 {
        font-size: 0.95rem;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .small-box:hover {
        transform: none;
    }
    
    .btn:hover {
        transform: none;
    }
    
    .card:hover {
        transform: none;
    }
}

/* Badge improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.375em 0.75em;
    border-radius: 6px;
}

/* Responsive table improvements */
@media (max-width: 576px) {
    .table td, .table th {
        padding: 0.5rem;
        vertical-align: middle;
    }
    
    .table-responsive {
        border-radius: 0.25rem;
    }
}

/* Form focus improvements */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Pending income card improvements */
#pendingIncomeCard .btn-light {
    background-color: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    color: #fff;
}

#pendingIncomeCard .btn-light:hover {
    background-color: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.7);
    color: #fff;
}

/* Statistics card improvements */
.card-header.bg-primary {
    background: linear-gradient(45deg, #007bff, #0056b3) !important;
}

.card-header.bg-info {
    background: linear-gradient(45deg, #17a2b8, #138496) !important;
}

.card-header.bg-success {
    background: linear-gradient(45deg, #28a745, #1e7e34) !important;
}
</style>

<?php require_once 'inc/footer.php'; ?>
