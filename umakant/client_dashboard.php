<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<style>
    /* Responsive styles for client dashboard */
    @media (max-width: 768px) {
        .content-wrapper {
            margin-left: 0;
        }
        
        .content-header h1 {
            font-size: 1.75rem;
        }
        
        .breadcrumb {
            font-size: 0.875rem;
        }
        
        /* Statistics boxes */
        .small-box {
            margin-bottom: 1rem;
            min-height: 120px;
        }
        
        .small-box .inner h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .small-box .inner p {
            font-size: 0.9rem;
        }
        
        .small-box .icon {
            font-size: 2.5rem;
            top: 1rem;
            right: 1rem;
        }
        
        .small-box-footer {
            font-size: 0.875rem;
            padding: 0.5rem;
        }
        
        /* Charts */
        .card-body {
            padding: 1rem;
        }
        
        .card-header h3 {
            font-size: 1.1rem;
        }
        
        /* Tables */
        .table-responsive {
            border-radius: 0.25rem;
            margin-bottom: 0;
        }
        
        .table th, .table td {
            font-size: 0.875rem;
            padding: 0.75rem 0.5rem;
        }
        
        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.5rem;
        }
        
        .card-footer {
            padding: 0.75rem;
            font-size: 0.875rem;
        }
    }
    
    @media (max-width: 576px) {
        .content-header h1 {
            font-size: 1.5rem;
        }
        
        /* Statistics boxes for small mobile */
        .small-box {
            margin-bottom: 0.75rem;
            min-height: 100px;
        }
        
        .small-box .inner h3 {
            font-size: 1.75rem;
        }
        
        .small-box .inner p {
            font-size: 0.8rem;
        }
        
        .small-box .icon {
            font-size: 2rem;
            top: 0.75rem;
            right: 0.75rem;
        }
        
        .small-box-footer {
            font-size: 0.8rem;
            padding: 0.4rem;
        }
        
        /* Charts for mobile */
        .card-body {
            padding: 0.75rem;
        }
        
        #taskStatusChart, #taskPriorityChart {
            max-height: 200px !important;
        }
        
        /* Tables for mobile */
        .table th, .table td {
            font-size: 0.8rem;
            padding: 0.5rem 0.25rem;
        }
        
        .card-header h3 {
            font-size: 1rem;
        }
        
        .card-footer {
            padding: 0.5rem;
            font-size: 0.8rem;
        }
        
        /* Hide certain columns on very small screens */
        .d-none.d-sm-table-cell {
            display: none !important;
        }
    }
    
    @media (max-width: 480px) {
        /* Extra small mobile adjustments */
        .small-box .inner h3 {
            font-size: 1.5rem;
        }
        
        .small-box .icon {
            font-size: 1.75rem;
        }
        
        #taskStatusChart, #taskPriorityChart {
            max-height: 180px !important;
        }
        
        .card-body {
            padding: 0.5rem;
        }
        
        .table th, .table td {
            font-size: 0.75rem;
            padding: 0.4rem 0.2rem;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.4rem;
        }
    }
    
    /* Chart specific responsive improvements */
    @media (max-width: 768px) {
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }
    }

    .dashboard-actions .btn {
        border-radius: 999px;
        font-weight: 600;
        padding: 0.5rem 1rem;
    }

    .dashboard-actions .btn i {
        margin-right: 0.4rem;
    }

    .dashboard-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .stat-meta {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.85);
    }

    .stat-progress {
        height: 6px;
        border-radius: 999px;
        overflow: hidden;
        background: rgba(255,255,255,0.2);
        margin-top: 0.5rem;
    }

    .stat-progress .progress-bar {
        background: rgba(255,255,255,0.85);
    }

    .quick-action-card .card-body {
        padding: 1rem 1.5rem 1.5rem;
    }

    .quick-action-card .btn {
        width: 100%;
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .empty-state {
        padding: 1.5rem;
        text-align: center;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        color: #adb5bd;
    }

    .dashboard-alert {
        display: none;
    }
    
    /* Improve table responsiveness */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Small box hover effects */
    .small-box {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .small-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* Card improvements */
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,0.125);
        transition: box-shadow 0.15s ease-in-out;
    }
    
    .card:hover {
        box-shadow: 0 0 8px rgba(0,0,0,0.15);
    }
    
    /* Ensure proper spacing on mobile */
    @media (max-width: 768px) {
        .row {
            margin-bottom: 1rem;
        }
        
        .col-md-6 {
            margin-bottom: 1rem;
        }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-chart-line mr-2"></i>Client Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Client Dashboard</li>
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
                    <div class="dashboard-meta">
                        <span id="dashboardLastUpdated">Last updated: --</span>
                    </div>
                </div>
                <div class="col-md-4 text-md-right dashboard-actions mt-2 mt-md-0">
                    <button class="btn btn-outline-primary btn-sm mr-2" type="button" id="refreshDashboard">
                        <i class="fas fa-sync-alt"></i>Refresh
                    </button>
                    <a href="clients.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-user-plus"></i>Add Client
                    </a>
                </div>
            </div>

            <div class="alert alert-warning dashboard-alert" role="alert" id="dashboardAlert"></div>

            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalClients">0</h3>
                            <p>Total Clients</p>
                            <div class="stat-meta">Active accounts</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="clients.php" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalTasks">0</h3>
                            <p>Total Tasks</p>
                            <div class="stat-meta">Across all clients</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="tasks.php" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pendingTasks">0</h3>
                            <p>Pending Tasks</p>
                            <div class="stat-meta">Needs attention</div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="tasks.php" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="completedTasks">0</h3>
                            <p>Completed Tasks</p>
                            <div class="stat-meta">Closed successfully</div>
                            <div class="stat-progress">
                                <div class="progress-bar" id="completionProgress" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="tasks.php" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card quick-action-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-2"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <a href="clients.php" class="btn btn-outline-primary">
                                <i class="fas fa-user-plus"></i>Add New Client
                            </a>
                            <a href="tasks.php" class="btn btn-outline-success">
                                <i class="fas fa-tasks"></i>Create Task
                            </a>
                            <a href="tasks.php" class="btn btn-outline-info mb-0">
                                <i class="fas fa-list"></i>Review Tasks
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-2"></i>
                                Productivity Insights
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <h4 class="text-primary mb-1" id="activeTasks">0</h4>
                                    <span class="text-muted">Active Tasks</span>
                                </div>
                                <div class="col-sm-4 mb-3 mb-sm-0">
                                    <h4 class="text-success mb-1" id="completionRate">0%</h4>
                                    <span class="text-muted">Completion Rate</span>
                                </div>
                                <div class="col-sm-4">
                                    <h4 class="text-info mb-1" id="avgTasksPerClient">0</h4>
                                    <span class="text-muted">Tasks per Client</span>
                                </div>
                            </div>
                            <div class="mt-3 text-muted small">
                                Keep momentum by closing pending tasks and creating follow-ups for high priority items.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Activity -->
            <div class="row">
                <!-- Task Status Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-2"></i>
                                Task Status Overview
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="taskStatusChart" style="height: 250px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Task Priority Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Task Priority Distribution
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="taskPriorityChart" style="height: 250px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Clients and Tasks -->
            <div class="row">
                <!-- Recent Clients -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-plus mr-2"></i>
                                Recent Clients
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th class="d-none d-sm-table-cell">Phone</th>
                                            <th class="d-none d-md-table-cell">Company</th>
                                        </tr>
                                    </thead>
                                <tbody id="recentClientsBody">
                                    <tr>
                                        <td colspan="3" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center">
                            <a href="clients.php">View All Clients</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tasks mr-2"></i>
                                Recent Tasks
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th class="d-none d-sm-table-cell">Client</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                <tbody id="recentTasksBody">
                                    <tr>
                                        <td colspan="3" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center">
                            <a href="tasks.php">View All Tasks</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
let taskStatusChart, taskPriorityChart;

$(document).ready(function() {
    loadDashboardStats();
    loadRecentClients();
    loadRecentTasks();

    $('#refreshDashboard').on('click', function() {
        loadDashboardStats();
        loadRecentClients();
        loadRecentTasks();
    });
});

function loadDashboardStats() {
    setDashboardAlert('');
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { 
            action: 'get_dashboard_stats',
            _: new Date().getTime()
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                const totalClients = Number(data.total_clients || 0);
                const totalTasks = Number(data.total_tasks || 0);
                const pendingTasks = Number(data.pending_tasks || 0);
                const completedTasks = Number(data.completed_tasks || 0);
                const inProgress = Number(data.task_status?.in_progress || 0);
                const onHold = Number(data.task_status?.on_hold || 0);

                $('#totalClients').text(totalClients);
                $('#totalTasks').text(totalTasks);
                $('#pendingTasks').text(pendingTasks);
                $('#completedTasks').text(completedTasks);

                const activeTasks = pendingTasks + inProgress + onHold;
                const completionRate = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
                const avgTasksPerClient = totalClients > 0 ? (totalTasks / totalClients).toFixed(1) : 0;

                $('#activeTasks').text(activeTasks);
                $('#completionRate').text(`${completionRate}%`);
                $('#avgTasksPerClient').text(avgTasksPerClient);
                $('#completionProgress').css('width', `${completionRate}%`);

                updateDashboardTimestamp();
                
                // Create charts
                createTaskStatusChart(data.task_status);
                createTaskPriorityChart(data.task_priority);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading dashboard stats:', error);
            setDashboardAlert('Unable to load dashboard statistics. Please refresh.');
        }
    });
}

function createTaskStatusChart(data) {
    const ctx = document.getElementById('taskStatusChart').getContext('2d');
    
    if (taskStatusChart) {
        taskStatusChart.destroy();
    }
    
    taskStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Completed', 'On Hold'],
            datasets: [{
                data: [
                    data.pending || 0,
                    data.in_progress || 0,
                    data.completed || 0,
                    data.on_hold || 0
                ],
                backgroundColor: ['#ffc107', '#007bff', '#28a745', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 12
                        },
                        padding: 10
                    }
                }
            },
            onResize: function(chart) {
                // Adjust chart size on window resize
                if (window.innerWidth < 768) {
                    chart.options.plugins.legend.labels.font.size = 10;
                } else {
                    chart.options.plugins.legend.labels.font.size = 12;
                }
                chart.update();
            }
        }
    });
}

function createTaskPriorityChart(data) {
    const ctx = document.getElementById('taskPriorityChart').getContext('2d');
    
    if (taskPriorityChart) {
        taskPriorityChart.destroy();
    }
    
    taskPriorityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Low', 'Medium', 'High', 'Urgent'],
            datasets: [{
                label: 'Tasks',
                data: [
                    data.low || 0,
                    data.medium || 0,
                    data.high || 0,
                    data.urgent || 0
                ],
                backgroundColor: ['#6c757d', '#17a2b8', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    titleFont: {
                        size: 12
                    },
                    bodyFont: {
                        size: 11
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: true,
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            onResize: function(chart) {
                // Adjust chart size on window resize
                if (window.innerWidth < 768) {
                    chart.options.scales.y.ticks.font.size = 10;
                    chart.options.scales.x.ticks.font.size = 10;
                    chart.options.plugins.tooltip.titleFont.size = 10;
                    chart.options.plugins.tooltip.bodyFont.size = 9;
                } else {
                    chart.options.scales.y.ticks.font.size = 11;
                    chart.options.scales.x.ticks.font.size = 11;
                    chart.options.plugins.tooltip.titleFont.size = 12;
                    chart.options.plugins.tooltip.bodyFont.size = 11;
                }
                chart.update();
            }
        }
    });
}

function loadRecentClients() {
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { 
            action: 'get_recent_clients',
            limit: 5,
            _: new Date().getTime()
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayRecentClients(response.data);
            } else {
                displayRecentClients([]);
            }
        },
        error: function() {
            displayRecentClients([]);
        }
    });
}

function displayRecentClients(clients) {
    const tbody = $('#recentClientsBody');
    tbody.empty();
    
    if (!clients || clients.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="3">
                    <div class="empty-state">
                        <i class="fas fa-user-plus"></i>
                        <div>No recent clients yet.</div>
                        <a href="clients.php" class="btn btn-sm btn-outline-primary mt-2">Add Client</a>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    clients.forEach(function(client) {
        tbody.append(`
                                    <tr>
                                        <td>${client.name}</td>
                                        <td class="d-none d-sm-table-cell">${client.phone}</td>
                                        <td class="d-none d-md-table-cell">${client.company || '-'}</td>
                                    </tr>
        `);
    });
}

function loadRecentTasks() {
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { 
            action: 'get_recent_tasks',
            limit: 5,
            _: new Date().getTime()
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayRecentTasks(response.data);
            } else {
                displayRecentTasks([]);
            }
        },
        error: function() {
            displayRecentTasks([]);
        }
    });
}

function displayRecentTasks(tasks) {
    const tbody = $('#recentTasksBody');
    tbody.empty();
    
    if (!tasks || tasks.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="3">
                    <div class="empty-state">
                        <i class="fas fa-tasks"></i>
                        <div>No tasks available.</div>
                        <a href="tasks.php" class="btn btn-sm btn-outline-success mt-2">Create Task</a>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    tasks.forEach(function(task) {
        const statusBadge = task.status === 'Completed' ? 
            '<span class="badge badge-success">Completed</span>' : 
            task.status === 'In Progress' ? 
            '<span class="badge badge-primary">In Progress</span>' : 
            task.status === 'On Hold' ? 
            '<span class="badge badge-warning">On Hold</span>' : 
            '<span class="badge badge-secondary">Pending</span>';
        
        tbody.append(`
                                    <tr>
                                        <td>${task.title}</td>
                                        <td class="d-none d-sm-table-cell">${task.client_name || '-'}</td>
                                        <td>${statusBadge}</td>
                                    </tr>
        `);
    });
}

function updateDashboardTimestamp() {
    const now = new Date();
    const formatted = now.toLocaleString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    $('#dashboardLastUpdated').text(`Last updated: ${formatted}`);
}

function setDashboardAlert(message) {
    const alert = $('#dashboardAlert');
    if (message) {
        alert.text(message).show();
        return;
    }
    alert.hide();
}
</script>

<?php require_once 'inc/footer.php'; ?>
