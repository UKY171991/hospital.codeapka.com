<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-briefcase mr-2 text-primary"></i>Business & CRM Hub</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Business Hub</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Summary Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary shadow">
                        <div class="inner">
                            <h3 id="stat-total-clients">0</h3>
                            <p>Combined Total Clients</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="clients.php" class="small-box-footer">Go to CRM <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow">
                        <div class="inner">
                            <h3 id="stat-active-tasks">0</h3>
                            <p>Active Tasks</p>
                        </div>
                        <div class="icon"><i class="fas fa-tasks"></i></div>
                        <a href="tasks.php" class="small-box-footer">Manage Tasks <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning shadow">
                        <div class="inner">
                            <h3 id="stat-today-followups">0</h3>
                            <p>Today's Followups</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar-check"></i></div>
                        <a href="followup_client.php" class="small-box-footer">View Schedule <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow">
                        <div class="inner">
                            <h3 id="stat-monthly-profit">₹0</h3>
                            <p>Monthly Profit (Inv)</p>
                        </div>
                        <div class="icon"><i class="fas fa-wallet"></i></div>
                        <a href="inventory_dashboard.php" class="small-box-footer">Finance Details <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Breakdown Card -->
                <div class="col-md-7">
                    <div class="card card-outline card-primary shadow">
                        <div class="card-header border-0">
                            <h3 class="card-title text-bold"><i class="fas fa-layer-group mr-2"></i>Infrastructure Breakdown</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-valign-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Resource Group</th>
                                        <th>Total Count</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><i class="fas fa-user-tie text-muted mr-2"></i> Main Clients (Task Based)</td>
                                        <td id="count-main-clients">0</td>
                                        <td><a href="clients.php" class="text-primary"><i class="fas fa-external-link-alt"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-users-cog text-muted mr-2"></i> Inventory Clients (Finance)</td>
                                        <td id="count-inventory-clients">0</td>
                                        <td><a href="inventory_client.php" class="text-primary"><i class="fas fa-external-link-alt"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td><i class="fas fa-user-clock text-muted mr-2"></i> Followup Clients (CRM)</td>
                                        <td id="count-followup-clients">0</td>
                                        <td><a href="followup_client.php" class="text-primary"><i class="fas fa-external-link-alt"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Task Distribution -->
                    <div class="card card-outline card-success shadow mt-4">
                        <div class="card-header">
                            <h3 class="card-title text-bold"><i class="fas fa-clipboard-check mr-2"></i>Critical Task Status</h3>
                        </div>
                        <div class="card-body">
                           <div class="alert alert-warning mb-0" id="urgent-task-alert" style="display:none;">
                               <i class="fas fa-exclamation-triangle mr-2"></i> There are <strong id="urgent-task-count">0</strong> Urgent tasks that need your immediate attention!
                               <a href="tasks.php" class="ml-2 text-dark font-weight-bold">View Now</a>
                           </div>
                           <p id="no-urgent-tasks" class="text-muted mb-0">No urgent tasks at the moment. Keep it up!</p>
                        </div>
                    </div>

                    <!-- Duplicate Detection Card -->
                    <div class="card card-outline card-danger shadow mt-4" id="duplicate-card" style="display:none;">
                        <div class="card-header">
                            <h3 class="card-title text-bold"><i class="fas fa-copy mr-2"></i>Duplicate Client Detection</h3>
                        </div>
                        <div class="card-body">
                            <p class="text-danger mb-2 font-weight-bold"><i class="fas fa-exclamation-circle mr-1"></i> We found <span id="duplicate-phone-count">0</span> phone numbers used across multiple modules.</p>
                            <button class="btn btn-danger btn-sm" onclick="viewDuplicatesModal()">View & Resolve Duplicates</button>
                        </div>
                    </div>
                </div>

                <!-- Financial Snapshot -->
                <div class="col-md-5">
                    <div class="card bg-gradient-navy shadow">
                        <div class="card-header border-0">
                            <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>This Month's Finance (Inventory)</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-light">Current Income:</span>
                                <h4 class="mb-0 text-success" id="inv-income">₹0</h4>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-light">Current Expense:</span>
                                <h4 class="mb-0 text-danger" id="inv-expense">₹0</h4>
                            </div>
                            <div class="progress mb-2" style="height: 5px;">
                                <div class="progress-bar bg-success" id="profit-bar" style="width: 0%"></div>
                            </div>
                            <div class="d-flex justify-content-between small text-light">
                                <span id="profit-label">Calculated Profit</span>
                                <span id="profit-percent">0%</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-center">
                            <a href="inventory_dashboard.php" class="btn btn-outline-light btn-sm">Full Inventory Dashboard</a>
                        </div>
                    </div>

                    <!-- Followup Alerts -->
                    <div class="card card-outline card-warning shadow mt-4">
                        <div class="card-header">
                            <h3 class="card-title text-bold text-dark"><i class="fas fa-bell mr-2"></i>CRM Alerts</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger text-white rounded-circle p-2 mr-3" style="width: 40px; height: 40px; text-align: center;">
                                    <i class="fas fa-times"></i>
                                </div>
                                <div>
                                    <p class="mb-0 font-weight-bold" id="overdue-count">0 Overdue Followups</p>
                                    <small class="text-muted">These clients missed their scheduled interaction.</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-warning text-dark rounded-circle p-2 mr-3" style="width: 40px; height: 40px; text-align: center;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <p class="mb-0 font-weight-bold" id="today-count">0 Pending for Today</p>
                                    <small class="text-muted">Interactions planned for current date.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'inc/footer.php'; ?>

<!-- Duplicates Modal -->
<div class="modal fade" id="duplicatesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Duplicate Clients Found</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>The following clients appear in multiple modules (matched by phone number):</p>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Source Module</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="duplicates-table-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadBusinessStats();
});

function loadBusinessStats() {
    $.getJSON('ajax/business_dashboard_api.php?action=get_unified_stats', function(response) {
        if (response.success) {
            const d = response.data;
            
            // Top Stats
            $('#stat-total-clients').text(d.main_clients + d.inventory_clients + d.followup_clients);
            $('#stat-active-tasks').text(d.active_tasks);
            $('#stat-today-followups').text(d.today_followups);
            $('#stat-monthly-profit').text('₹' + d.month_profit.toLocaleString());

            // Breakdown Table
            $('#count-main-clients').text(d.main_clients);
            $('#count-inventory-clients').text(d.inventory_clients);
            $('#count-followup-clients').text(d.followup_clients);

            // Tasks
            if(d.urgent_tasks > 0) {
                $('#urgent-task-alert').show();
                $('#urgent-task-count').text(d.urgent_tasks);
                $('#no-urgent-tasks').hide();
            }

            // Duplicates
            if(d.duplicate_phones > 0) {
                $('#duplicate-card').show();
                $('#duplicate-phone-count').text(d.duplicate_phones);
            }

            // Finance
            $('#inv-income').text('₹' + d.month_income.toLocaleString());
            $('#inv-expense').text('₹' + d.month_expense.toLocaleString());
            
            if(d.month_income > 0) {
                const profitPct = Math.min(100, Math.max(0, (d.month_profit / d.month_income) * 100));
                $('#profit-bar').css('width', profitPct + '%');
                $('#profit-percent').text(profitPct.toFixed(1) + '%');
            }

            // Followups
            $('#overdue-count').text(d.overdue_followups + ' Overdue Followups');
            $('#today-count').text(d.today_followups + ' Pending for Today');
        }
    });
}

function viewDuplicatesModal() {
    $.getJSON('ajax/business_dashboard_api.php?action=get_duplicates', function(response) {
        if(response.success) {
            const tbody = $('#duplicates-table-body');
            tbody.empty();
            response.data.forEach(item => {
                let link = '';
                if(item.source == 'Task Client') link = 'clients.php';
                if(item.source == 'Inventory Client') link = 'inventory_client.php';
                if(item.source == 'Followup Client') link = 'followup_client.php';

                tbody.append(`
                    <tr>
                        <td>${item.name}</td>
                        <td class="font-weight-bold">${item.phone}</td>
                        <td><span class="badge badge-info">${item.source}</span></td>
                        <td><a href="${link}" class="btn btn-xs btn-primary">Go to List</a></td>
                    </tr>
                `);
            });
            $('#duplicatesModal').modal('show');
        }
    });
}
</script>
