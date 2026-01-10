<?php
require_once 'inc/connection.php';
@include_once 'inc/seed_admin.php';
include_once 'inc/header.php';
include_once 'inc/sidebar.php';
?>

<?php
// Fetch summary counts for dashboard
$counts = [
  'doctors' => '--',
  'patients' => '--',
  'owners' => '--',
  'notices' => '--',
  'plans' => '--',
  'entries' => '--',
  'tests' => '--',
  'test_categories' => '--',
  'users' => '--',
];
try {
  if (isset($pdo)) {
    $counts['doctors'] = (int) $pdo->query('SELECT COUNT(*) FROM doctors')->fetchColumn();
    $counts['patients'] = (int) $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
    $counts['owners'] = (int) $pdo->query('SELECT COUNT(*) FROM owners')->fetchColumn();
    $counts['notices'] = (int) $pdo->query('SELECT COUNT(*) FROM notices')->fetchColumn();
    $counts['plans'] = (int) $pdo->query('SELECT COUNT(*) FROM plans')->fetchColumn();
    $counts['entries'] = (int) $pdo->query('SELECT COUNT(*) FROM entries')->fetchColumn();
    $counts['tests'] = (int) $pdo->query('SELECT COUNT(*) FROM tests')->fetchColumn();
    // test categories count (used for dashboard card)
    try {
      $counts['test_categories'] = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    } catch (Throwable $e) {
      $counts['test_categories'] = '--';
    }
    $counts['users'] = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    // uploaded files: prefer zip_uploads table if available
    try {
      $stmt = $pdo->query("SHOW TABLES LIKE 'zip_uploads'");
      $has = $stmt->fetch() ? true : false;
      if ($has) {
        $counts['uploads'] = (int) $pdo->query('SELECT COUNT(*) FROM zip_uploads')->fetchColumn();
      } else {
        $counts['uploads'] = '--';
      }
    } catch (Throwable $inner) {
      $counts['uploads'] = '--';
    }
  }
} catch (Throwable $e) {
  // keep placeholders if query fails; leave counts as '--'
}

// Debug visibility: admins or explicit query parameter
$showDebugCounts = (isset($_GET['debug_counts']) && $_GET['debug_counts'] == 1) || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// Log if test_categories count wasn't retrieved (helps diagnose missing table or DB error)
if (isset($counts['test_categories']) && $counts['test_categories'] === '--') {
  error_log('Dashboard: test_categories count unavailable - check table presence or query errors');
}
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">
              <i class="fas fa-tachometer-alt mr-2 text-primary"></i>
              Dashboard
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <!-- Welcome Card -->
        <div class="row mb-4">
          <div class="col-12">
            <div class="card bg-gradient-primary text-white">
              <div class="card-body">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h4 class="mb-1">Welcome to Hospital Management System</h4>
                    <p class="mb-0 opacity-75">
                      <i class="far fa-clock mr-1"></i>
                      Today is <?php echo date('l, F j, Y'); ?>
                    </p>
                  </div>
                  <div class="col-md-4">
                    <div class="text-right d-none d-md-block mb-3">
                      <a href="dashboard.php" class="btn btn-light btn-sm mr-2">
                        <i class="fas fa-chart-line mr-1"></i>
                        <span class="d-none d-lg-inline">Pathology Dashboard</span>
                        <span class="d-lg-none">Dashboard</span>
                      </a>
                      <a href="api_test.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-cogs mr-1"></i>
                        <span class="d-none d-lg-inline">API Test</span>
                        <span class="d-lg-none">API</span>
                      </a>
                    </div>
                    <div class="text-center d-md-none">
                      <a href="dashboard.php" class="btn btn-light btn-sm mr-2 mb-2">
                        <i class="fas fa-chart-line mr-1"></i>
                        Dashboard
                      </a>
                      <a href="api_test.php" class="btn btn-outline-light btn-sm mb-2">
                        <i class="fas fa-cogs mr-1"></i>
                        API Test
                      </a>
                    </div>
                  </div>
                    <i class="fas fa-hospital fa-3x opacity-50"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats Row 1 -->
        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info shadow">
              <div class="inner">
                <h3 id="doctorsCount"><?php echo htmlspecialchars($counts['doctors']); ?></h3>
                <p>Doctors</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-md"></i>
              </div>
              <a href="doctor.php" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success shadow">
              <div class="inner">
                <h3 id="patientsCount"><?php echo htmlspecialchars($counts['patients']); ?></h3>
                <p>Patients</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <a href="patient.php" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-warning shadow">
              <div class="inner">
                <h3 id="entriesCount"><?php echo htmlspecialchars($counts['entries']); ?></h3>
                <p>Test Entries</p>
              </div>
              <div class="icon">
                <i class="fas fa-clipboard-list"></i>
              </div>
              <a href="entry-list.php" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-danger shadow">
              <div class="inner">
                <h3 id="testsCount"><?php echo htmlspecialchars($counts['tests']); ?></h3>
                <p>Tests</p>
              </div>
              <div class="icon">
                <i class="fas fa-vial"></i>
              </div>
              <a href="test.php" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-teal shadow">
              <div class="inner">
                <h3 id="testCategoriesCount"><?php echo htmlspecialchars($counts['test_categories']); ?></h3>
                <p>Test Categories</p>
              </div>
              <div class="icon">
                <i class="fas fa-th-list"></i>
              </div>
              <a href="test-category.php" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Stats Row 2 -->
        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-primary shadow">
              <div class="inner">
                <h3 id="plansCount"><?php echo htmlspecialchars($counts['plans']); ?></h3>
                <p>Plans</p>
              </div>
              <div class="icon">
                <i class="fas fa-list-alt"></i>
              </div>
              <a href="plan.php" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-secondary shadow">
              <div class="inner">
                <h3 id="usersCount"><?php echo htmlspecialchars($counts['users']); ?></h3>
                <p>Users</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-cog"></i>
              </div>
              <a href="user.php" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-purple shadow">
              <div class="inner">
                <h3 id="noticesCount"><?php echo htmlspecialchars($counts['notices']); ?></h3>
                <p>Notices</p>
              </div>
              <div class="icon">
                <i class="fas fa-bell"></i>
              </div>
              <a href="notice.php" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-dark shadow">
              <div class="inner">
                <h3 id="ownersCount"><?php echo htmlspecialchars($counts['owners']); ?></h3>
                <p>Owners</p>
              </div>
              <div class="icon">
                <i class="fas fa-id-badge"></i>
              </div>
              <a href="owner.php" class="small-box-footer">
                View Details <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Quick Actions & Charts Row -->
        <div class="row">
          <div class="col-lg-8 col-md-12">
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-line mr-1"></i>
                  System Overview
                </h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="systemChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-12">
            <div class="card card-success card-outline">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-rocket mr-1"></i>
                  Quick Actions
                </h3>
              </div>
              <div class="card-body">
                <div class="d-grid gap-2">
                  <a href="patient.php" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus mr-2"></i><span class="d-none d-sm-inline">Add New</span> Patient
                  </a>
                  <a href="doctor.php" class="btn btn-info btn-block">
                    <i class="fas fa-user-md mr-2"></i><span class="d-none d-sm-inline">Manage</span> Doctors
                  </a>
                  <a href="test.php" class="btn btn-warning btn-block">
                    <i class="fas fa-vial mr-2"></i><span class="d-none d-sm-inline">Manage</span> Tests
                  </a>
                  <a href="entry-list.php" class="btn btn-success btn-block">
                    <i class="fas fa-clipboard-list mr-2"></i><span class="d-none d-sm-inline">Test</span> Entries
                  </a>
                  <a href="plan.php" class="btn btn-purple btn-block">
                    <i class="fas fa-list-alt mr-2"></i><span class="d-none d-sm-inline">Manage</span> Plans
                  </a>
                </div>
              </div>
            </div>

            <!-- System Info Card -->
            <div class="card card-info card-outline">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-info-circle mr-1"></i>
                  System Info
                </h3>
              </div>
              <div class="card-body">
                <div class="info-box-content">
                  <div class="row">
                    <div class="col-12 mb-2">
                      <div class="d-flex justify-content-between">
                        <span class="text-muted">PHP Version:</span>
                        <span class="font-weight-bold"><?php echo PHP_VERSION; ?></span>
                      </div>
                    </div>
                    <div class="col-12 mb-2">
                      <div class="d-flex justify-content-between">
                        <span class="text-muted">Server:</span>
                        <span class="font-weight-bold"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span>
                      </div>
                    </div>
                    <div class="col-12 mb-2">
                      <div class="d-flex justify-content-between">
                        <span class="text-muted">Database:</span>
                        <span class="font-weight-bold">MySQL/MariaDB</span>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="d-flex justify-content-between">
                        <span class="text-muted">Status:</span>
                        <span class="badge badge-success">Online</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity Row -->
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-history mr-1"></i>
                  Recent Activity
                </h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="refresh" onclick="loadRecentActivity()">
                    <i class="fas fa-sync"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div id="recentActivity">
                  <div class="text-center text-muted">
                    <i class="fas fa-spinner fa-spin"></i> Loading recent activity...
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php if (!empty($showDebugCounts)): ?>
        <div class="row mt-3">
          <div class="col-12">
            <div class="card card-outline card-danger">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bug mr-1"></i> Debug: Dashboard Counts</h3>
              </div>
              <div class="card-body">
                <pre><?php echo htmlspecialchars(print_r($counts, true)); ?></pre>
                <p class="text-muted">If any value shows <strong>--</strong>, the corresponding DB query may have failed or the table is missing.</p>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize dashboard using vanilla JS to avoid jQuery dependency timing issues
document.addEventListener('DOMContentLoaded', function(){
  initializeChart();
  loadRecentActivity();

  // Auto-refresh stats every 5 minutes
  setInterval(refreshStats, 300000);
  // Fetch counts immediately so the UI shows up-to-date numbers on first render
  try{ refreshStats(); } catch(e){ console.warn('refreshStats failed', e); }
});

function initializeChart() {
    const ctx = document.getElementById('systemChart').getContext('2d');
    const chartData = {
        labels: ['Doctors', 'Patients', 'Tests', 'Entries', 'Plans', 'Users'],
        datasets: [{
            label: 'Count',
            data: [
                <?php echo $counts['doctors']; ?>,
                <?php echo $counts['patients']; ?>,
                <?php echo $counts['tests']; ?>,
                <?php echo $counts['entries']; ?>,
                <?php echo $counts['plans']; ?>,
                <?php echo $counts['users']; ?>
            ],
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(255, 99, 132, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 2
        }]
    };

    new Chart(ctx, {
        type: 'doughnut',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: window.innerWidth < 768 ? 'bottom' : 'right',
                    labels: {
                        boxWidth: 12,
                        padding: window.innerWidth < 768 ? 10 : 20,
                        font: {
                            size: window.innerWidth < 768 ? 11 : 12
                        }
                    }
                },
                title: {
                    display: true,
                    text: 'System Data Distribution',
                    font: {
                        size: window.innerWidth < 768 ? 14 : 16
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed;
                            return label;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            },
            cutout: window.innerWidth < 768 ? '50%' : '60%'
        }
    });
}

function loadRecentActivity() {
  var el = document.getElementById('recentActivity');
  if(!el) return;
  el.innerHTML = '<div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading recent activity...</div>';

  fetch('ajax/recent_activity.php', {cache: 'no-store'})
    .then(function(r){ return r.json(); })
    .then(function(resp){
      if(!resp || !resp.success || !Array.isArray(resp.items)){
        el.innerHTML = '<div class="text-center text-muted">No recent activity available.</div>';
        return;
      }
      var html = '<div class="timeline">';
      resp.items.forEach(function(item){
        var icon = 'fas fa-info-circle';
        var color = 'text-secondary';
        var title = item.title || '';
        var details = item.details || '';
        var time = item.time ? new Date(item.time).toLocaleString() : '';
        if(item.type === 'patient'){ icon = 'fas fa-user-plus'; color = 'text-success'; }
        if(item.type === 'entry'){ icon = 'fas fa-vial'; color = 'text-info'; }
        if(item.type === 'notice'){ icon = 'fas fa-bell'; color = 'text-warning'; }
        if(item.type === 'upload'){ icon = 'fas fa-upload'; color = 'text-purple'; }

        html += '\n            <div class="timeline-item">\n                <div class="timeline-marker">\n                    <i class="'+icon+' '+color+'"></i>\n                </div>\n                <div class="timeline-content">\n                    <div class="d-flex justify-content-between align-items-start mb-1">\n                        <h6 class="timeline-title mb-0">'+escapeHtml(title)+'</h6>\n                        <small class="text-muted flex-shrink-0 ml-2">'+escapeHtml(time)+'</small>\n                    </div>\n                    <p class="timeline-text mb-0">'+escapeHtml(details)+'</p>\n                </div>\n            </div>\n        ';
      });
      html += '</div>';
      el.innerHTML = html;
    }).catch(function(){
      el.innerHTML = '<div class="text-center text-muted">Failed to load recent activity.</div>';
    });
}

function escapeHtml(s){ if(!s) return ''; return String(s).replace(/[&<>"'`]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[c]; }); }

function refreshStats() {
  // Fetch latest counts from server and update stat boxes using fetch API
  fetch('ajax/dashboard_counts.php', {cache: 'no-store'})
    .then(function(r){ return r.json(); })
    .then(function(resp){
      if(resp && resp.success && resp.counts){
        var c = resp.counts;
        function setText(id, val){ var el = document.getElementById(id); if(el) el.textContent = val; }
        if(typeof c.doctors !== 'undefined') setText('doctorsCount', c.doctors);
        if(typeof c.patients !== 'undefined') setText('patientsCount', c.patients);
        if(typeof c.entries !== 'undefined') setText('entriesCount', c.entries);
        if(typeof c.tests !== 'undefined') setText('testsCount', c.tests);
        if(typeof c.test_categories !== 'undefined') setText('testCategoriesCount', c.test_categories);
        if(typeof c.plans !== 'undefined') setText('plansCount', c.plans);
        if(typeof c.users !== 'undefined') setText('usersCount', c.users);
        if(typeof c.notices !== 'undefined') setText('noticesCount', c.notices);
        if(typeof c.owners !== 'undefined') setText('ownersCount', c.owners);
        if(typeof c.uploads !== 'undefined') setText('uploadsCount', c.uploads);
      } else {
        console.warn('Dashboard counts response invalid', resp);
      }
    }).catch(function(){
      console.warn('Failed to fetch dashboard counts');
    });
}
</script>

<style>
/* Responsive Design Improvements for Dashboard */

/* Mobile Responsive Styles */
@media (max-width: 576px) {
    /* Welcome Card Mobile Adjustments */
    .bg-gradient-primary .card-body {
        padding: 20px 15px;
    }
    
    .bg-gradient-primary h4 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    .bg-gradient-primary p {
        font-size: 0.875rem;
    }
    
    /* Stats Cards Mobile Adjustments */
    .small-box {
        margin-bottom: 15px;
        border-radius: 8px;
    }
    
    .small-box .inner {
        padding: 15px;
    }
    
    .small-box h3 {
        font-size: 1.75rem;
        margin-bottom: 5px;
    }
    
    .small-box p {
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    .small-box .icon {
        top: 10px;
        right: 10px;
        font-size: 1.5rem;
        opacity: 0.7;
    }
    
    .small-box-footer {
        padding: 8px 15px;
        font-size: 0.8rem;
    }
    
    /* Chart Mobile Adjustments */
    .chart {
        margin: 10px 0;
    }
    
    #systemChart {
        max-height: 200px !important;
    }
    
    /* Quick Actions Mobile Adjustments */
    .d-grid {
        gap: 0.5rem !important;
    }
    
    .btn-block {
        padding: 12px 15px;
        font-size: 0.875rem;
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* System Info Mobile Adjustments */
    .info-box-content {
        font-size: 0.875rem;
    }
    
    .info-box-content .d-flex {
        padding: 8px 0;
    }
    
    /* Timeline Mobile Adjustments */
    .timeline {
        padding-left: 25px;
    }
    
    .timeline-marker {
        width: 20px;
        height: 20px;
        left: -20px;
        font-size: 10px;
    }
    
    .timeline-content {
        padding: 12px;
        margin-left: 5px;
    }
    
    .timeline-title {
        font-size: 0.9rem;
        line-height: 1.3;
    }
    
    .timeline-text {
        font-size: 0.8rem;
        line-height: 1.4;
    }
    
    /* Card Mobile Adjustments */
    .card {
        margin-bottom: 15px;
        border-radius: 8px;
    }
    
    .card-header {
        padding: 12px 15px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    .card-title {
        font-size: 1rem;
    }
    
    /* Button Mobile Improvements */
    .btn {
        min-height: 44px;
        border-radius: 6px;
    }
    
    .btn-sm {
        min-height: 38px;
        font-size: 0.8rem;
    }
    
    /* Content Header Mobile */
    .content-header {
        padding: 15px 0;
    }
    
    .content-header h1 {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    /* Tablet Adjustments */
    .small-box {
        margin-bottom: 20px;
    }
    
    .small-box h3 {
        font-size: 2rem;
    }
    
    #systemChart {
        max-height: 250px !important;
    }
    
    .timeline {
        padding-left: 30px;
    }
    
    .timeline-marker {
        width: 25px;
        height: 25px;
        left: -27px;
        font-size: 11px;
    }
    
    .timeline-content {
        padding: 15px;
    }
    
    /* Quick Actions Tablet */
    .btn-block {
        padding: 10px 15px;
        font-size: 0.9rem;
    }
}

@media (max-width: 992px) {
    /* Small Desktop Adjustments */
    .card-header h3 {
        font-size: 1.1rem;
    }
    
    #systemChart {
        max-height: 300px !important;
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

/* Timeline Improvements */
.timeline-item {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
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

/* Responsive Grid Improvements */
@media (max-width: 576px) {
    .row.mb-4 {
        margin-bottom: 20px;
    }
    
    .col-6 {
        padding-left: 7.5px;
        padding-right: 7.5px;
    }
}

/* Chart Container Improvements */
.chart {
    position: relative;
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* System Info Improvements */
.info-box-content .d-flex {
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.3s ease;
}

.info-box-content .d-flex:hover {
    background-color: #f8f9fa;
}

.info-box-content .d-flex:last-child {
    border-bottom: none;
}

/* Badge Improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.375em 0.75em;
    border-radius: 6px;
}

/* Welcome Card Icon Fix */
.bg-gradient-primary .fa-hospital {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.1;
}

/* Mobile-specific welcome card layout */
@media (max-width: 768px) {
    .bg-gradient-primary .fa-hospital {
        display: none;
    }
}

/* Quick Actions Grid Improvements */
.d-grid {
    display: grid !important;
}

@media (max-width: 576px) {
    .d-grid {
        grid-template-columns: 1fr;
    }
}

@media (min-width: 577px) and (max-width: 768px) {
    .d-grid {
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem !important;
    }
}

/* Responsive Typography */
@media (max-width: 576px) {
    h1 {
        font-size: 1.5rem;
    }
    
    h4 {
        font-size: 1.25rem;
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
</style>

<?php include_once 'inc/footer.php'; ?>
