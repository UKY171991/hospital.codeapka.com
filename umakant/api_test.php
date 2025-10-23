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
                    <h1>API Testing Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">API Test</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- API Test Cards -->
            <div class="row">
                
                <!-- Dashboard API Test -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard API Test
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" onclick="testDashboardAPI()">
                                    <i class="fas fa-play"></i> Test All
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="dashboard-api-results">
                                <p class="text-muted">Click "Test All" to run dashboard API tests</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other APIs Test -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs mr-2"></i>
                                Other APIs Test
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success btn-sm" onclick="testOtherAPIs()">
                                    <i class="fas fa-play"></i> Test All
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="other-apis-results">
                                <p class="text-muted">Click "Test All" to run other API tests</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- API Documentation Link -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-book mr-2"></i>
                                API Documentation
                            </h3>
                        </div>
                        <div class="card-body">
                            <p>For complete API documentation and interactive testing, visit:</p>
                            <a href="patho_api/api.html" class="btn btn-info" target="_blank">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Open API Documentation
                            </a>
                            
                            <div class="mt-3">
                                <h5>Available API Endpoints:</h5>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Dashboard API
                                        <span class="badge badge-primary badge-pill">12 endpoints</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Patient API
                                        <span class="badge badge-success badge-pill">5 endpoints</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Doctor API
                                        <span class="badge badge-info badge-pill">6 endpoints</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Test API
                                        <span class="badge badge-warning badge-pill">5 endpoints</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Entry API
                                        <span class="badge badge-danger badge-pill">9 endpoints</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<script>
const API_SECRET = 'hospital-api-secret-2024';

async function testDashboardAPI() {
    const resultsDiv = document.getElementById('dashboard-api-results');
    resultsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Testing Dashboard APIs with User ID 1...</div>';
    
    const dashboardEndpoints = [
        'overview',
        'stats', 
        'recent_activities',
        'charts_data',
        'quick_stats',
        'revenue_stats',
        'test_performance',
        'patient_demographics',
        'monthly_trends',
        'top_tests',
        'doctor_performance',
        'alerts'
    ];
    
    let results = '<div class="alert alert-info"><strong>Note:</strong> Testing with test_user_id=1 (Admin user)</div>';
    results += '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Endpoint</th><th>Status</th><th>Response Time</th><th>User Data</th></tr></thead><tbody>';
    
    for (const endpoint of dashboardEndpoints) {
        const startTime = Date.now();
        try {
            const response = await fetch(`patho_api/dashboard.php?action=${endpoint}&secret_key=${API_SECRET}&test_user_id=1`);
            const endTime = Date.now();
            const responseTime = endTime - startTime;
            
            if (response.ok) {
                const data = await response.json();
                const status = data.success ? 
                    '<span class="badge badge-success">Success</span>' : 
                    '<span class="badge badge-warning">Failed</span>';
                
                let userInfo = '';
                if (data.success && data.data && data.data.user_info) {
                    userInfo = `User: ${data.data.user_info.username} (${data.data.user_info.role})`;
                } else if (data.success && endpoint === 'overview') {
                    userInfo = 'User-filtered data';
                } else {
                    userInfo = data.message || 'No user info';
                }
                
                results += `<tr><td>${endpoint}</td><td>${status}</td><td>${responseTime}ms</td><td><small>${userInfo}</small></td></tr>`;
            } else {
                results += `<tr><td>${endpoint}</td><td><span class="badge badge-danger">Error ${response.status}</span></td><td>${responseTime}ms</td><td>-</td></tr>`;
            }
        } catch (error) {
            results += `<tr><td>${endpoint}</td><td><span class="badge badge-danger">Network Error</span></td><td>-</td><td>-</td></tr>`;
        }
    }
    
    results += '</tbody></table></div>';
    results += '<div class="mt-3"><small class="text-muted">💡 Tip: Add &test_user_id=2 or &test_username=doctor1 to test with different users</small></div>';
    resultsDiv.innerHTML = results;
}

async function testOtherAPIs() {
    const resultsDiv = document.getElementById('other-apis-results');
    resultsDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Testing Other APIs...</div>';
    
    const otherAPIs = [
        { name: 'Patient API', file: 'patient.php', action: 'list' },
        { name: 'Doctor API', file: 'doctor.php', action: 'list' },
        { name: 'Test API', file: 'test.php', action: 'list' },
        { name: 'Entry API', file: 'entry.php', action: 'list' },
        { name: 'Test Category API', file: 'test_category.php', action: 'list' },
        { name: 'Notice API', file: 'notice.php', action: 'list' },
        { name: 'Owner API', file: 'owner.php', action: 'list' },
        { name: 'User API', file: 'user.php', action: 'list' }
    ];
    
    let results = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>API</th><th>Status</th><th>Response Time</th></tr></thead><tbody>';
    
    for (const api of otherAPIs) {
        const startTime = Date.now();
        try {
            const response = await fetch(`patho_api/${api.file}?action=${api.action}&secret_key=${API_SECRET}`);
            const endTime = Date.now();
            const responseTime = endTime - startTime;
            
            if (response.ok) {
                const data = await response.json();
                const status = data.success ? 
                    '<span class="badge badge-success">Success</span>' : 
                    '<span class="badge badge-warning">Failed</span>';
                results += `<tr><td>${api.name}</td><td>${status}</td><td>${responseTime}ms</td></tr>`;
            } else {
                results += `<tr><td>${api.name}</td><td><span class="badge badge-danger">Error ${response.status}</span></td><td>${responseTime}ms</td></tr>`;
            }
        } catch (error) {
            results += `<tr><td>${api.name}</td><td><span class="badge badge-danger">Network Error</span></td><td>-</td></tr>`;
        }
    }
    
    results += '</tbody></table></div>';
    resultsDiv.innerHTML = results;
}

// Auto-test on page load
document.addEventListener('DOMContentLoaded', function() {
    // Uncomment to auto-test on page load
    // testDashboardAPI();
    // setTimeout(testOtherAPIs, 2000);
});
</script>

<?php require_once 'inc/footer.php'; ?>