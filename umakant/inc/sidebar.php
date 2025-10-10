<?php
// Unified AdminLTE sidebar
if (session_status() === PHP_SESSION_NONE) session_start();
$activePage = basename($_SERVER['PHP_SELF']);
$username = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Guest';
$role = $_SESSION['role'] ?? 'user';
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link">
    <span class="brand-text font-weight-light">Hospital Admin</span>
  </a>
  <div class="sidebar">
    <!-- User panel -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="info">
        <a href="#" class="d-block"><?php echo htmlspecialchars($username); ?></a>
        <small class="text-muted"><?php echo htmlspecialchars(ucfirst($role)); ?></small>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <?php
    // Pages to include under Pathology
    $pathologyPages = [
      'index.php','doctors.php','patient.php','owner.php',
      'test-category.php','test.php','upload_zip.php','upload_list.php',
      'entry-list.php','plan.php','notice.php'
    ];
    $isPathologyActive = in_array($activePage, $pathologyPages);
    // OPD menu and its pages - adjust filenames as needed
    $opdPages = ['opd_dashboard.php', 'opd_patient.php','opd_doctor.php','department.php','appointment.php', 'opd_billing.php', 'opd_reports.php'];
    $isOpdActive = in_array($activePage, $opdPages);
    ?>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="dashboard.php" class="nav-link <?php echo ($activePage == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard Main</p>
          </a>
        </li>

        <li class="nav-item has-treeview <?php echo $isOpdActive ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo $isOpdActive ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-clinic-medical"></i>
            <p>
              OPD
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="opd_dashboard.php" class="nav-link <?php echo ($activePage == 'opd_dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt nav-icon"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_patient.php" class="nav-link <?php echo ($activePage == 'opd_patient.php') ? 'active' : ''; ?>">
                <i class="fas fa-user nav-icon"></i>
                <p>Patient</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_doctor.php" class="nav-link <?php echo ($activePage == 'opd_doctor.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-md nav-icon"></i>
                <p>Doctor</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="department.php" class="nav-link <?php echo ($activePage == 'department.php') ? 'active' : ''; ?>">
                <i class="fas fa-building nav-icon"></i>
                <p>Department</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="appointment.php" class="nav-link <?php echo ($activePage == 'appointment.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check nav-icon"></i>
                <p>User Appointment</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_billing.php" class="nav-link <?php echo ($activePage == 'opd_billing.php') ? 'active' : ''; ?>">
                <i class="fas fa-file-invoice-dollar nav-icon"></i>
                <p>Billing</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_reports.php" class="nav-link <?php echo ($activePage == 'opd_reports.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line nav-icon"></i>
                <p>Reports</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item has-treeview <?php echo $isPathologyActive ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo $isPathologyActive ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-flask"></i>
            <p>
              Pathology
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="index.php" class="nav-link <?php echo ($activePage == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie nav-icon"></i>
                <p>Dashboard</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="doctors.php" class="nav-link <?php echo ($activePage == 'doctors.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-md nav-icon"></i>
                <p>Doctors</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="patient.php" class="nav-link <?php echo ($activePage == 'patient.php') ? 'active' : ''; ?>">
                <i class="fas fa-user nav-icon"></i>
                <p>Patients</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="test-category.php" class="nav-link <?php echo ($activePage == 'test-category.php') ? 'active' : ''; ?>">
                <i class="fas fa-th-list nav-icon"></i>
                <p>Test Categories</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="test.php" class="nav-link <?php echo ($activePage == 'test.php') ? 'active' : ''; ?>">
                <i class="fas fa-vial nav-icon"></i>
                <p>Tests</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="entry-list.php" class="nav-link <?php echo ($activePage == 'entry-list.php') ? 'active' : ''; ?>">
                <i class="fas fa-file-medical nav-icon"></i>
                <p>Entries</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="plan.php" class="nav-link <?php echo ($activePage == 'plan.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt nav-icon"></i>
                <p>Menu Plan</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="notice.php" class="nav-link <?php echo ($activePage == 'notice.php') ? 'active' : ''; ?>">
                <i class="fas fa-bell nav-icon"></i>
                <p>Notices</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="upload_list.php" class="nav-link <?php echo ($activePage == 'upload_list.php') ? 'active' : ''; ?>">
                <i class="fas fa-folder-open nav-icon"></i>
                <p>Uploads</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="owner.php" class="nav-link <?php echo ($activePage == 'owner.php') ? 'active' : ''; ?>">
                <i class="fas fa-id-badge nav-icon"></i>
                <p>Owners</p>
              </a>
            </li>

          </ul>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a href="user.php" class="nav-link <?php echo ($activePage == 'user.php') ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Users</p>
          </a>
        </li>
        <?php endif; ?>

  <!-- Admins menu removed -->

  <!-- Logout moved to top navbar for easier access -->
      </ul>
    </nav>
  </div>
</aside>
