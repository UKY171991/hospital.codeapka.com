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
      $counts['test_categories'] = (int) $pdo->query('SELECT COUNT(*) FROM test_categories')->fetchColumn();
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
                  <div class="col-md-4 text-right d-none d-md-block">
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
          <div class="col-md-8">
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

          <div class="col-md-4">
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
                    <i class="fas fa-user-plus mr-2"></i>Add New Patient
                  </a>
                  <a href="doctor.php" class="btn btn-info btn-block">
                    <i class="fas fa-user-md mr-2"></i>Manage Doctors
                  </a>
                  <a href="test.php" class="btn btn-warning btn-block">
                    <i class="fas fa-vial mr-2"></i>Manage Tests
                  </a>
                  <a href="entry-list.php" class="btn btn-success btn-block">
                    <i class="fas fa-clipboard-list mr-2"></i>Test Entries
                  </a>
                  <a href="plan.php" class="btn btn-purple btn-block">
                    <i class="fas fa-list-alt mr-2"></i>Manage Plans
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
// Initialize dashboard
$(document).ready(function() {
    initializeChart();
    loadRecentActivity();
    
    // Auto-refresh stats every 5 minutes
    setInterval(refreshStats, 300000);
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
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'System Data Distribution'
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
}

function loadRecentActivity() {
    // Simulate loading recent activity
    const activities = [
        {
            icon: 'fas fa-user-plus',
            color: 'text-success',
            action: 'New patient registered',
            time: '2 minutes ago',
            details: 'John Doe (UHID: P123456)'
        },
        {
            icon: 'fas fa-vial',
            color: 'text-info',
            action: 'Test entry created',
            time: '15 minutes ago',
            details: 'Blood Test for Patient UHID: P789012'
        },
        {
            icon: 'fas fa-user-md',
            color: 'text-primary',
            action: 'Doctor profile updated',
            time: '1 hour ago',
            details: 'Dr. Smith updated specialization'
        },
        {
            icon: 'fas fa-bell',
            color: 'text-warning',
            action: 'New notice published',
            time: '2 hours ago',
            details: 'System maintenance scheduled'
        },
        {
            icon: 'fas fa-upload',
            color: 'text-purple',
            action: 'File uploaded',
            time: '3 hours ago',
            details: 'Lab reports uploaded to system'
        }
    ];

    let html = '<div class="timeline">';
    activities.forEach((activity, index) => {
        html += `
            <div class="timeline-item">
                <div class="timeline-marker">
                    <i class="${activity.icon} ${activity.color}"></i>
                </div>
                <div class="timeline-content">
                    <h6 class="timeline-title">${activity.action}</h6>
                    <p class="timeline-text">${activity.details}</p>
                    <small class="text-muted">${activity.time}</small>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    setTimeout(() => {
        $('#recentActivity').html(html);
    }, 1000);
}

function refreshStats() {
  // Fetch latest counts from server and update stat boxes
  $.getJSON('ajax/dashboard_counts.php').done(function(resp){
    if(resp && resp.success && resp.counts){
      var c = resp.counts;
      if(typeof c.doctors !== 'undefined') $('#doctorsCount').text(c.doctors);
      if(typeof c.patients !== 'undefined') $('#patientsCount').text(c.patients);
      if(typeof c.entries !== 'undefined') $('#entriesCount').text(c.entries);
      if(typeof c.tests !== 'undefined') $('#testsCount').text(c.tests);
      if(typeof c.test_categories !== 'undefined') $('#testCategoriesCount').text(c.test_categories);
      if(typeof c.plans !== 'undefined') $('#plansCount').text(c.plans);
      if(typeof c.users !== 'undefined') $('#usersCount').text(c.users);
      if(typeof c.notices !== 'undefined') $('#noticesCount').text(c.notices);
      if(typeof c.owners !== 'undefined') $('#ownersCount').text(c.owners);
      if(typeof c.uploads !== 'undefined') $('#uploadsCount').text(c.uploads);
    } else {
      console.warn('Dashboard counts response invalid', resp);
    }
  }).fail(function(){
    console.warn('Failed to fetch dashboard counts');
  });
}
</script>

<style>
.bg-gradient-purple {
    background: linear-gradient(45deg, #6f42c1, #9c27b0) !important;
}

.btn-purple {
    background: linear-gradient(45deg, #6f42c1, #9c27b0);
    border: none;
    color: white;
}

.btn-purple:hover {
    background: linear-gradient(45deg, #5a2d91, #7b1fa2);
    color: white;
}

.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: white;
    border: 2px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    color: #6c757d;
}

.small-box {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.card {
    border-radius: 10px;
    border: none;
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.card-outline.card-success {
    border-top: 3px solid #28a745;
}

.card-outline.card-info {
    border-top: 3px solid #17a2b8;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #6f42c1) !important;
}

.bg-gradient-teal {
  background: linear-gradient(45deg, #20c997, #17a2b8) !important;
}

.shadow {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.d-grid {
    display: grid !important;
}

.gap-2 {
    gap: 0.5rem !important;
}
</style>

<?php include_once 'inc/footer.php'; ?>
