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
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="index.php" class="nav-link <?php echo ($activePage == 'index.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a>
        </li>

        <!-- <li class="nav-item">
          <a href="doctors.php" class="nav-link <?php echo ($activePage == 'doctors.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-user-md"></i><p>Doctors</p></a>
        </li> -->

        <!-- <li class="nav-item">
          <a href="patient.php" class="nav-link <?php echo ($activePage == 'patient.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-user"></i><p>Patients</p></a>
        </li> -->

        <li class="nav-item">
          <a href="owner.php" class="nav-link <?php echo ($activePage == 'owner.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-id-badge"></i><p>Owners</p></a>
        </li>

        <li class="nav-item">
          <a href="test-category.php" class="nav-link <?php echo ($activePage == 'test-category.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-th-list"></i><p>Test Categories</p></a>
        </li>

        <li class="nav-item">
          <a href="test.php" class="nav-link <?php echo ($activePage == 'test.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-vial"></i><p>Tests</p></a>
        </li>

        <li class="nav-item">
          <a href="upload_zip.php" class="nav-link <?php echo ($activePage == 'upload_zip.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-file-archive"></i><p>Upload ZIP</p></a>
        </li>

        <li class="nav-item">
          <a href="entry-list.php" class="nav-link <?php echo ($activePage == 'entry-list.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-file-medical"></i><p>Entries</p></a>
        </li>

        <li class="nav-item">
          <a href="plan.php" class="nav-link <?php echo ($activePage == 'plan.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-calendar-alt"></i><p>Menu Plan</p></a>
        </li>

        <li class="nav-item">
          <a href="notice.php" class="nav-link <?php echo ($activePage == 'notice.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-bell"></i><p>Notices</p></a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a href="user.php" class="nav-link <?php echo ($activePage == 'user.php') ? 'active' : ''; ?>"><i class="nav-icon fas fa-users"></i><p>Users</p></a>
        </li>
        <?php endif; ?>

  <!-- Admins menu removed -->

        <li class="nav-item mt-2">
          <a href="logout.php" class="nav-link"><i class="nav-icon fas fa-sign-out-alt"></i><p>Logout</p></a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
