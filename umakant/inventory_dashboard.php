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
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalIncome">₹0</h3>
                            <p>Total Income</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <a href="inventory_income.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="totalExpense">₹0</h3>
                            <p>Total Expense</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <a href="inventory_expense.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="netProfit">₹0</h3>
                            <p>Net Profit</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="totalClients">0</h3>
                            <p>Total Clients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="inventory_client.php" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Period Statistics -->
            <div class="row">
                <div class="col-md-4">
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

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title">This Month</h3>
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

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title">This Year</h3>
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
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Monthly Income vs Expense
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="incomeExpenseChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-2"></i>
                                Expense Categories
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="expenseCategoryChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>
                                Recent Transactions
                            </h3>
                        </div>
                        <div class="card-body">
                            <table id="recentTransactionsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Client</th>
                                        <th>Amount</th>
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
$(document).ready(function() {
    loadDashboardData();
    initializeCharts();
});

function loadDashboardData() {
    // Load summary statistics
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { action: 'get_dashboard_stats' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                $('#totalIncome').text('₹' + formatNumber(data.total_income || 0));
                $('#totalExpense').text('₹' + formatNumber(data.total_expense || 0));
                $('#netProfit').text('₹' + formatNumber(data.net_profit || 0));
                $('#totalClients').text(data.total_clients || 0);
                
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
            } else {
                console.error('Failed to load dashboard stats:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Dashboard stats error:', error);
            $('#totalIncome').text('₹0');
            $('#totalExpense').text('₹0');
            $('#netProfit').text('₹0');
            $('#totalClients').text('0');
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
            console.error('Recent transactions error:', error);
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

function initializeCharts() {
    // Income vs Expense Chart
    const ctx1 = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Income',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                backgroundColor: 'rgba(40, 167, 69, 0.7)'
            }, {
                label: 'Expense',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                backgroundColor: 'rgba(220, 53, 69, 0.7)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Expense Category Chart
    const ctx2 = document.getElementById('expenseCategoryChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ['Medical Supplies', 'Equipment', 'Utilities', 'Salaries', 'Others'],
            datasets: [{
                data: [0, 0, 0, 0, 0],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function formatNumber(num) {
    return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
</script>

<?php require_once 'inc/footer.php'; ?>
