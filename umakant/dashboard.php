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
                    <h1>Pathology Dashboard</h1>
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
            
            <!-- Loading Indicator -->
            <div id="dashboard-loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading dashboard data...</p>
            </div>

            <!-- Dashboard Content -->
            <div id="dashboard-content" style="display: none;">
                
                <!-- Quick Stats Row -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Today's Performance
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" onclick="refreshQuickStats()">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row" id="quick-stats-row">
                                    <!-- Quick stats will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Stats Cards -->
                <div class="row mb-4" id="main-stats-cards">
                    <!-- Main stats cards will be loaded here -->
                </div>

                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Patient Growth Chart -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-users mr-2"></i>
                                    Patient Growth
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="patientGrowthChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Chart -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-rupee-sign mr-2"></i>
                                    Revenue Trends
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Second Charts Row -->
                <div class="row mb-4">
                    <!-- Test Distribution -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-flask mr-2"></i>
                                    Test Distribution
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="testDistributionChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Doctor Performance -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-user-md mr-2"></i>
                                    Doctor Performance
                                </h3>
                            </div>
                            <div class="card-body">
                                <canvas id="doctorPerformanceChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Tables Row -->
                <div class="row mb-4">
                    <!-- Recent Activities -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-clock mr-2"></i>
                                    Recent Activities
                                </h3>
                            </div>
                            <div class="card-body">
                                <div id="recent-activities">
                                    <!-- Recent activities will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Tests -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-star mr-2"></i>
                                    Top Performing Tests
                                </h3>
                            </div>
                            <div class="card-body">
                                <div id="top-tests">
                                    <!-- Top tests will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Alerts -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    System Alerts
                                </h3>
                            </div>
                            <div class="card-body">
                                <div id="system-alerts">
                                    <!-- System alerts will be loaded here -->
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
class PathologyDashboard {
    constructor() {
        this.apiBase = 'patho_api/dashboard.php';
        this.secretKey = 'hospital-api-secret-2024';
        this.charts = {};
        this.refreshInterval = null;
        
        this.init();
    }
    
    async init() {
        try {
            await this.loadDashboardData();
            this.setupAutoRefresh();
            this.hideLoading();
        } catch (error) {
            console.error('Dashboard initialization failed:', error);
            this.showError('Failed to load dashboard data');
        }
    }
    
    async loadDashboardData() {
        // Load all dashboard components
        await Promise.all([
            this.loadOverview(),
            this.loadQuickStats(),
            this.loadChartsData(),
            this.loadRecentActivities(),
            this.loadTopTests(),
            this.loadSystemAlerts()
        ]);
    }
    
    async apiCall(action) {
        // Get user info from session or use test user
        let url = `${this.apiBase}?action=${action}&secret_key=${this.secretKey}`;
        
        // Add user authentication - in a real scenario, this would come from session
        // For testing, we'll use a default test user
        url += '&test_user_id=1'; // This should be replaced with actual session user ID
        
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`API call failed: ${response.statusText}`);
        }
        return await response.json();
    }
    
    async loadOverview() {
        try {
            const data = await this.apiCall('overview');
            if (data.success) {
                this.renderMainStatsCards(data.data.counts);
            }
        } catch (error) {
            console.error('Failed to load overview:', error);
        }
    }
    
    async loadQuickStats() {
        try {
            const data = await this.apiCall('quick_stats');
            if (data.success) {
                this.renderQuickStats(data.data);
            }
        } catch (error) {
            console.error('Failed to load quick stats:', error);
        }
    }
    
    async loadChartsData() {
        try {
            const data = await this.apiCall('charts_data');
            if (data.success) {
                this.renderCharts(data.data);
            }
        } catch (error) {
            console.error('Failed to load charts data:', error);
        }
    }
    
    async loadRecentActivities() {
        try {
            const data = await this.apiCall('recent_activities');
            if (data.success) {
                this.renderRecentActivities(data.data);
            }
        } catch (error) {
            console.error('Failed to load recent activities:', error);
        }
    }
    
    async loadTopTests() {
        try {
            const data = await this.apiCall('top_tests');
            if (data.success) {
                this.renderTopTests(data.data);
            }
        } catch (error) {
            console.error('Failed to load top tests:', error);
        }
    }
    
    async loadSystemAlerts() {
        try {
            const data = await this.apiCall('alerts');
            if (data.success) {
                this.renderSystemAlerts(data.data);
            }
        } catch (error) {
            console.error('Failed to load system alerts:', error);
        }
    }
    
    renderMainStatsCards(counts) {
        const container = document.getElementById('main-stats-cards');
        const cards = [
            { title: 'Total Patients', value: counts.patients, icon: 'fas fa-users', color: 'primary' },
            { title: 'Total Doctors', value: counts.doctors, icon: 'fas fa-user-md', color: 'success' },
            { title: 'Test Entries', value: counts.entries, icon: 'fas fa-file-medical', color: 'info' },
            { title: 'Available Tests', value: counts.tests, icon: 'fas fa-flask', color: 'warning' }
        ];
        
        container.innerHTML = cards.map(card => `
            <div class="col-lg-3 col-6">
                <div class="small-box bg-${card.color}">
                    <div class="inner">
                        <h3>${card.value.toLocaleString()}</h3>
                        <p>${card.title}</p>
                    </div>
                    <div class="icon">
                        <i class="${card.icon}"></i>
                    </div>
                </div>
            </div>
        `).join('');
    }
    
    renderQuickStats(stats) {
        const container = document.getElementById('quick-stats-row');
        const quickStats = [
            { 
                title: 'Today\'s Patients', 
                today: stats.today.patients, 
                yesterday: stats.yesterday.patients,
                growth: stats.growth.patients 
            },
            { 
                title: 'Today\'s Entries', 
                today: stats.today.entries, 
                yesterday: stats.yesterday.entries,
                growth: stats.growth.entries 
            },
            { 
                title: 'Today\'s Revenue', 
                today: `₹${stats.today.revenue.toLocaleString()}`, 
                yesterday: `₹${stats.yesterday.revenue.toLocaleString()}`,
                growth: stats.growth.revenue 
            }
        ];
        
        container.innerHTML = quickStats.map(stat => {
            const growthClass = stat.growth >= 0 ? 'text-success' : 'text-danger';
            const growthIcon = stat.growth >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';
            
            return `
                <div class="col-lg-4 col-md-6">
                    <div class="info-box">
                        <div class="info-box-content">
                            <span class="info-box-text">${stat.title}</span>
                            <span class="info-box-number">${stat.today}</span>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: 70%"></div>
                            </div>
                            <span class="progress-description">
                                <span class="${growthClass}">
                                    <i class="fas ${growthIcon}"></i> ${Math.abs(stat.growth)}%
                                </span>
                                vs yesterday (${stat.yesterday})
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    renderCharts(chartsData) {
        // Patient Growth Chart
        this.renderLineChart('patientGrowthChart', chartsData.patient_growth, 'Patient Growth');
        
        // Revenue Chart
        this.renderLineChart('revenueChart', chartsData.revenue_chart, 'Revenue Trends');
        
        // Test Distribution Chart
        this.renderDoughnutChart('testDistributionChart', chartsData.test_distribution, 'Test Distribution');
        
        // Doctor Performance Chart
        this.renderBarChart('doctorPerformanceChart', chartsData.doctor_performance, 'Doctor Performance');
    }
    
    renderLineChart(canvasId, data, title) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        
        if (this.charts[canvasId]) {
            this.charts[canvasId].destroy();
        }
        
        this.charts[canvasId] = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: title
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    renderBarChart(canvasId, data, title) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        
        if (this.charts[canvasId]) {
            this.charts[canvasId].destroy();
        }
        
        this.charts[canvasId] = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: title
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    renderDoughnutChart(canvasId, data, title) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        
        if (this.charts[canvasId]) {
            this.charts[canvasId].destroy();
        }
        
        this.charts[canvasId] = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: title
                    },
                    legend: {
                        display: true,
                        position: 'right'
                    }
                }
            }
        });
    }
    
    renderRecentActivities(activities) {
        const container = document.getElementById('recent-activities');
        
        if (!activities || activities.length === 0) {
            container.innerHTML = '<p class="text-muted">No recent activities found.</p>';
            return;
        }
        
        container.innerHTML = `
            <div class="timeline">
                ${activities.map(activity => `
                    <div class="time-label">
                        <span class="bg-${activity.color}">${this.formatDate(activity.timestamp)}</span>
                    </div>
                    <div>
                        <i class="fas fa-${activity.icon} bg-${activity.color}"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">${activity.title}</h3>
                            <div class="timeline-body">
                                ${activity.description}
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    renderTopTests(tests) {
        const container = document.getElementById('top-tests');
        
        if (!tests || tests.length === 0) {
            container.innerHTML = '<p class="text-muted">No test data available.</p>';
            return;
        }
        
        container.innerHTML = `
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Test Name</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tests.slice(0, 10).map(test => `
                            <tr>
                                <td>${test.name}</td>
                                <td><span class="badge badge-primary">${test.order_count || 0}</span></td>
                                <td>₹${(test.total_revenue || 0).toLocaleString()}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
    }
    
    renderSystemAlerts(alerts) {
        const container = document.getElementById('system-alerts');
        
        if (!alerts || alerts.length === 0) {
            container.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i>All systems are running normally.</div>';
            return;
        }
        
        container.innerHTML = alerts.map(alert => {
            const alertClass = alert.type === 'danger' ? 'alert-danger' : 
                              alert.type === 'warning' ? 'alert-warning' : 'alert-info';
            const icon = alert.type === 'danger' ? 'fa-exclamation-triangle' : 
                        alert.type === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle';
            
            return `
                <div class="alert ${alertClass}">
                    <i class="fas ${icon} mr-2"></i>
                    <strong>${alert.title}:</strong> ${alert.message}
                    ${alert.action ? `<br><small>Action: ${alert.action}</small>` : ''}
                </div>
            `;
        }).join('');
    }
    
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }
    
    setupAutoRefresh() {
        // Refresh dashboard every 5 minutes
        this.refreshInterval = setInterval(() => {
            this.loadQuickStats();
            this.loadRecentActivities();
            this.loadSystemAlerts();
        }, 5 * 60 * 1000);
    }
    
    hideLoading() {
        document.getElementById('dashboard-loading').style.display = 'none';
        document.getElementById('dashboard-content').style.display = 'block';
    }
    
    showError(message) {
        document.getElementById('dashboard-loading').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                ${message}
                <br>
                <button class="btn btn-primary mt-2" onclick="location.reload()">
                    <i class="fas fa-refresh mr-2"></i>Retry
                </button>
            </div>
        `;
    }
    
    destroy() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
        
        // Destroy all charts
        Object.values(this.charts).forEach(chart => {
            if (chart) chart.destroy();
        });
    }
}

// Global functions
function refreshQuickStats() {
    if (window.dashboard) {
        window.dashboard.loadQuickStats();
    }
}

// Initialize dashboard when page loads
document.addEventListener('DOMContentLoaded', function() {
    window.dashboard = new PathologyDashboard();
});

// Cleanup when page unloads
window.addEventListener('beforeunload', function() {
    if (window.dashboard) {
        window.dashboard.destroy();
    }
});
</script>

<!-- Custom Dashboard Styles -->
<style>
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