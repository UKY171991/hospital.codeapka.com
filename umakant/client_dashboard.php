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
            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalClients">0</h3>
                            <p>Total Clients</p>
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
});

function loadDashboardStats() {
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
                $('#totalClients').text(data.total_clients);
                $('#totalTasks').text(data.total_tasks);
                $('#pendingTasks').text(data.pending_tasks);
                $('#completedTasks').text(data.completed_tasks);
                
                // Create charts
                createTaskStatusChart(data.task_status);
                createTaskPriorityChart(data.task_priority);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading dashboard stats:', error);
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
            }
        }
    });
}

function displayRecentClients(clients) {
    const tbody = $('#recentClientsBody');
    tbody.empty();
    
    if (!clients || clients.length === 0) {
        tbody.append('<tr><td colspan="3" class="text-center">No clients found</td></tr>');
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
            }
        }
    });
}

function displayRecentTasks(tasks) {
    const tbody = $('#recentTasksBody');
    tbody.empty();
    
    if (!tasks || tasks.length === 0) {
        tbody.append('<tr><td colspan="3" class="text-center">No tasks found</td></tr>');
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
</script>

<?php require_once 'inc/footer.php'; ?>
