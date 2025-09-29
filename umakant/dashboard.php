<?php
require_once 'inc/connection.php';
@include_once 'inc/seed_admin.php';
include_once 'inc/header.php';
include_once 'inc/sidebar.php';

// Summary metrics for the master dashboard
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
  'uploads' => '--',
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

    try {
      $counts['test_categories'] = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    } catch (Throwable $inner) {
      $counts['test_categories'] = '--';
    }

    $counts['users'] = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

    try {
      $stmt = $pdo->query("SHOW TABLES LIKE 'zip_uploads'");
      $hasTable = (bool) $stmt->fetch();
      if ($hasTable) {
        $counts['uploads'] = (int) $pdo->query('SELECT COUNT(*) FROM zip_uploads')->fetchColumn();
      }
    } catch (Throwable $inner) {
      $counts['uploads'] = '--';
    }
  }
} catch (Throwable $e) {
  // Keep placeholder values -- no additional handling required for now
}

$showDebugCounts = (isset($_GET['debug_counts']) && $_GET['debug_counts'] == 1)
  || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

if ($counts['test_categories'] === '--') {
  error_log('Master Dashboard: test_categories count unavailable - verify categories table.');
}
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">
            <i class="fas fa-tachometer-alt mr-2 text-primary"></i>
            Master Dashboard
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

  <section class="content">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-12">
          <div class="card bg-gradient-primary text-white">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <h4 class="mb-1">Welcome back!</h4>
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

      <div class="row mb-4">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-info shadow">
            <div class="inner">
              <h3 id="md_doctorsCount"><?php echo htmlspecialchars($counts['doctors']); ?></h3>
              <p>Doctors</p>
            </div>
            <div class="icon"><i class="fas fa-user-md"></i></div>
            <a href="doctor.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-success shadow">
            <div class="inner">
              <h3 id="md_patientsCount"><?php echo htmlspecialchars($counts['patients']); ?></h3>
              <p>Patients</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="patient.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-warning shadow">
            <div class="inner">
              <h3 id="md_entriesCount"><?php echo htmlspecialchars($counts['entries']); ?></h3>
              <p>Test Entries</p>
            </div>
            <div class="icon"><i class="fas fa-clipboard-list"></i></div>
            <a href="entry-list.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-danger shadow">
            <div class="inner">
              <h3 id="md_testsCount"><?php echo htmlspecialchars($counts['tests']); ?></h3>
              <p>Tests</p>
            </div>
            <div class="icon"><i class="fas fa-vial"></i></div>
            <a href="test.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-teal shadow">
            <div class="inner">
              <h3 id="md_testCategoriesCount"><?php echo htmlspecialchars($counts['test_categories']); ?></h3>
              <p>Test Categories</p>
            </div>
            <div class="icon"><i class="fas fa-th-list"></i></div>
            <a href="test-category.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-primary shadow">
            <div class="inner">
              <h3 id="md_plansCount"><?php echo htmlspecialchars($counts['plans']); ?></h3>
              <p>Plans</p>
            </div>
            <div class="icon"><i class="fas fa-list-alt"></i></div>
            <a href="plan.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-secondary shadow">
            <div class="inner">
              <h3 id="md_usersCount"><?php echo htmlspecialchars($counts['users']); ?></h3>
              <p>Users</p>
            </div>
            <div class="icon"><i class="fas fa-user-cog"></i></div>
            <a href="user.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-dark shadow">
            <div class="inner">
              <h3 id="md_ownersCount"><?php echo htmlspecialchars($counts['owners']); ?></h3>
              <p>Owners</p>
            </div>
            <div class="icon"><i class="fas fa-id-badge"></i></div>
            <a href="owner.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-purple shadow">
            <div class="inner">
              <h3 id="md_noticesCount"><?php echo htmlspecialchars($counts['notices']); ?></h3>
              <p>Notices</p>
            </div>
            <div class="icon"><i class="fas fa-bell"></i></div>
            <a href="notice.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-gradient-maroon shadow">
            <div class="inner">
              <h3 id="md_uploadsCount"><?php echo htmlspecialchars($counts['uploads']); ?></h3>
              <p>Uploads</p>
            </div>
            <div class="icon"><i class="fas fa-upload"></i></div>
            <a href="upload_list.php" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-8">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i>System Overview</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="maximize"><i class="fas fa-expand"></i></button>
              </div>
            </div>
            <div class="card-body">
              <canvas id="masterDashboardChart" style="min-height:250px;height:250px;max-height:250px;max-width:100%;"></canvas>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card card-success card-outline">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-rocket mr-1"></i>Quick Actions</h3>
            </div>
            <div class="card-body">
              <div class="d-grid gap-2">
                <a href="patient.php" class="btn btn-primary btn-block"><i class="fas fa-user-plus mr-2"></i>Add New Patient</a>
                <a href="doctor.php" class="btn btn-info btn-block"><i class="fas fa-user-md mr-2"></i>Manage Doctors</a>
                <a href="test.php" class="btn btn-warning btn-block"><i class="fas fa-vial mr-2"></i>Manage Tests</a>
                <a href="entry-list.php" class="btn btn-success btn-block"><i class="fas fa-clipboard-list mr-2"></i>Test Entries</a>
                <a href="plan.php" class="btn btn-purple btn-block"><i class="fas fa-list-alt mr-2"></i>Manage Plans</a>
              </div>
            </div>
          </div>

          <div class="card card-info card-outline">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i>System Info</h3>
            </div>
            <div class="card-body">
              <div class="info-box-content">
                <div class="row">
                  <div class="col-12 mb-2">
                    <div class="d-flex justify-content-between"><span class="text-muted">PHP Version:</span><span class="font-weight-bold"><?php echo PHP_VERSION; ?></span></div>
                  </div>
                  <div class="col-12 mb-2">
                    <div class="d-flex justify-content-between"><span class="text-muted">Server:</span><span class="font-weight-bold"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span></div>
                  </div>
                  <div class="col-12 mb-2">
                    <div class="d-flex justify-content-between"><span class="text-muted">Database:</span><span class="font-weight-bold">MySQL/MariaDB</span></div>
                  </div>
                  <div class="col-12">
                    <div class="d-flex justify-content-between"><span class="text-muted">Status:</span><span class="badge badge-success">Online</span></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-history mr-1"></i>Recent Activity</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="refresh" onclick="loadMasterRecentActivity()"><i class="fas fa-sync"></i></button>
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
              </div>
            </div>
            <div class="card-body">
              <div id="masterRecentActivity">
                <div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading recent activity...</div>
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
              <h3 class="card-title"><i class="fas fa-bug mr-1"></i>Debug: Dashboard Counts</h3>
            </div>
            <div class="card-body">
              <pre><?php echo htmlspecialchars(print_r($counts, true)); ?></pre>
              <p class="text-muted">If any value shows <strong>--</strong>, the underlying query may have failed or the table is missing.</p>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    initializeMasterChart();
    loadMasterRecentActivity();
    setInterval(refreshMasterStats, 300000);
    try{ refreshMasterStats(); } catch(e){ console.warn('refreshMasterStats failed', e); }
  });

  function initializeMasterChart(){
    var canvas = document.getElementById('masterDashboardChart');
    if(!canvas) return;
    var ctx = canvas.getContext('2d');
    var chartData = {
      labels: ['Doctors', 'Patients', 'Tests', 'Entries', 'Plans', 'Users', 'Notices', 'Owners'],
      datasets: [{
        label: 'Count',
        data: [
          <?php echo (int) $counts['doctors']; ?>,
          <?php echo (int) $counts['patients']; ?>,
          <?php echo (int) $counts['tests']; ?>,
          <?php echo (int) $counts['entries']; ?>,
          <?php echo (int) $counts['plans']; ?>,
          <?php echo (int) $counts['users']; ?>,
          <?php echo (int) $counts['notices']; ?>,
          <?php echo (int) $counts['owners']; ?>
        ],
        backgroundColor: [
          'rgba(54, 162, 235, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(255, 99, 132, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)',
          'rgba(255, 99, 71, 0.2)',
          'rgba(70, 130, 180, 0.2)'
        ],
        borderColor: [
          'rgba(54, 162, 235, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(255, 99, 132, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)',
          'rgba(255, 99, 71, 1)',
          'rgba(70, 130, 180, 1)'
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
          legend: { position: 'bottom' },
          title: { display: true, text: 'System Data Distribution' }
        },
        animation: {
          animateScale: true,
          animateRotate: true
        }
      }
    });
  }

  function loadMasterRecentActivity(){
    var container = document.getElementById('masterRecentActivity');
    if(!container) return;
    container.innerHTML = '<div class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading recent activity...</div>';
    fetch('ajax/recent_activity.php', { cache: 'no-store' })
      .then(function(resp){ return resp.json(); })
      .then(function(data){
        if(!data || !data.success || !Array.isArray(data.items)){
          container.innerHTML = '<div class="text-center text-muted">No recent activity available.</div>';
          return;
        }
        var html = '<div class="timeline">';
        data.items.forEach(function(item){
          var icon = 'fas fa-info-circle';
          var color = 'text-secondary';
          if(item.type === 'patient'){ icon = 'fas fa-user-plus'; color = 'text-success'; }
          if(item.type === 'entry'){ icon = 'fas fa-vial'; color = 'text-info'; }
          if(item.type === 'notice'){ icon = 'fas fa-bell'; color = 'text-warning'; }
          if(item.type === 'upload'){ icon = 'fas fa-upload'; color = 'text-purple'; }

          var title = escapeHtml(item.title || '');
          var details = escapeHtml(item.details || '');
          var time = item.time ? escapeHtml(new Date(item.time).toLocaleString()) : '';

          html += '\n            <div class="timeline-item">\n              <div class="timeline-marker">\n                <i class="' + icon + ' ' + color + '"></i>\n              </div>\n              <div class="timeline-content">\n                <h6 class="timeline-title">' + title + '</h6>\n                <p class="timeline-text">' + details + '</p>\n                <small class="text-muted">' + time + '</small>\n              </div>\n            </div>\n          ';
        });
        html += '</div>';
        container.innerHTML = html;
      })
      .catch(function(){
        container.innerHTML = '<div class="text-center text-muted">Failed to load recent activity.</div>';
      });
  }

  function refreshMasterStats(){
    fetch('ajax/dashboard_counts.php', { cache: 'no-store' })
      .then(function(resp){ return resp.json(); })
      .then(function(data){
        if(!data || !data.success || !data.counts){
          console.warn('Master dashboard counts response invalid', data);
          return;
        }
        var c = data.counts;
        function setText(id, val){ var el = document.getElementById(id); if(el) el.textContent = val; }
        if('doctors' in c) setText('md_doctorsCount', c.doctors);
        if('patients' in c) setText('md_patientsCount', c.patients);
        if('entries' in c) setText('md_entriesCount', c.entries);
        if('tests' in c) setText('md_testsCount', c.tests);
        if('test_categories' in c) setText('md_testCategoriesCount', c.test_categories);
        if('plans' in c) setText('md_plansCount', c.plans);
        if('users' in c) setText('md_usersCount', c.users);
        if('notices' in c) setText('md_noticesCount', c.notices);
        if('owners' in c) setText('md_ownersCount', c.owners);
        if('uploads' in c) setText('md_uploadsCount', c.uploads);
      })
      .catch(function(){ console.warn('Failed to fetch master dashboard counts'); });
  }

  function escapeHtml(str){
    if(!str) return '';
    return String(str).replace(/[&<>"'`]/g, function(c){
      return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;', '`':'&#96;' }[c];
    });
  }
})();
</script>

<style>
.bg-gradient-maroon {
  background: linear-gradient(45deg, #d81b60, #8e24aa) !important;
}

.bg-gradient-purple {
  background: linear-gradient(45deg, #6f42c1, #9c27b0) !important;
}

.bg-gradient-teal {
  background: linear-gradient(45deg, #20c997, #17a2b8) !important;
}

.btn-purple {
  background: linear-gradient(45deg, #6f42c1, #9c27b0);
  border: none;
  color: #fff;
}

.btn-purple:hover {
  background: linear-gradient(45deg, #5a2d91, #7b1fa2);
  color: #fff;
}

.small-box {
  border-radius: 10px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.small-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.card {
  border-radius: 10px;
  border: none;
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
  background: #fff;
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
</style>

<?php include_once 'inc/footer.php'; ?>
