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
      'entry-list.php','plan.php','notice.php','user.php'
    ];
    $isPathologyActive = in_array($activePage, $pathologyPages);
    ?>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
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
                <i class="fas fa-tachometer-alt nav-icon"></i>
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
              <a href="owner.php" class="nav-link <?php echo ($activePage == 'owner.php') ? 'active' : ''; ?>">
                <i class="fas fa-id-badge nav-icon"></i>
                <p>Owners</p>
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
              <a href="upload_zip.php" class="nav-link <?php echo ($activePage == 'upload_zip.php') ? 'active' : ''; ?>">
                <i class="fas fa-file-archive nav-icon"></i>
                <p>Upload ZIP</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="upload_list.php" class="nav-link <?php echo ($activePage == 'upload_list.php') ? 'active' : ''; ?>">
                <i class="fas fa-folder-open nav-icon"></i>
                <p>Uploaded Files</p>
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

            <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a href="user.php" class="nav-link <?php echo ($activePage == 'user.php') ? 'active' : ''; ?>">
                <i class="fas fa-users nav-icon"></i>
                <p>Users</p>
              </a>
            </li>
            <?php endif; ?>

          </ul>
        </li>

  <!-- Admins menu removed -->

  <!-- Logout moved to top navbar for easier access -->
      </ul>
    </nav>
  </div>
</aside>
