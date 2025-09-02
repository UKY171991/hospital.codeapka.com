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
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0">Dashboard</h1></div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- First row: Doctors, Patients, Owners, Notices -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner"><h3><?php echo htmlspecialchars($counts['doctors']); ?></h3><p>Doctors</p></div>
              <div class="icon"><i class="fas fa-user-md"></i></div>
              <a href="doctors.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner"><h3><?php echo htmlspecialchars($counts['patients']); ?></h3><p>Patients</p></div>
              <div class="icon"><i class="fas fa-user"></i></div>
              <a href="patient.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner"><h3><?php echo htmlspecialchars($counts['owners']); ?></h3><p>Owners</p></div>
              <div class="icon"><i class="fas fa-id-badge"></i></div>
              <a href="owner.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner"><h3><?php echo htmlspecialchars($counts['notices']); ?></h3><p>Notices</p></div>
              <div class="icon"><i class="fas fa-bell"></i></div>
              <a href="notice.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Second row: Plans, Entries, Tests, Users -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
              <div class="inner"><h3><?php echo htmlspecialchars($counts['plans']); ?></h3><p>Plans</p></div>
              <div class="icon"><i class="fas fa-calendar-alt"></i></div>
              <a href="plan.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
              <div class="inner"><h3><?php echo htmlspecialchars($counts['entries']); ?></h3><p>Entries</p></div>
              <div class="icon"><i class="fas fa-file-medical"></i></div>
              <a href="entry-list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-light">
              <div class="inner"><h3><?php echo htmlspecialchars($counts['tests']); ?></h3><p>Tests</p></div>
              <div class="icon"><i class="fas fa-vial"></i></div>
              <a href="test.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-dark">
              <div class="inner"><h3><?php echo htmlspecialchars($counts['users']); ?></h3><p>Users</p></div>
              <div class="icon"><i class="fas fa-users"></i></div>
              <a href="user.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <div class="row">
          <!-- Third row: Uploaded Files -->
          <div class="col-lg-3 col-6">
            <div class="small-box bg-maroon">
              <div class="inner"><h3><?php echo htmlspecialchars($counts['uploads'] ?? '--'); ?></h3><p>Uploaded Files</p></div>
              <div class="icon"><i class="fas fa-folder-open"></i></div>
              <a href="upload_list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php include_once 'inc/footer.php'; ?>
