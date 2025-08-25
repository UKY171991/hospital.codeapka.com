<?php
// AdminLTE sidebar menu
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link">
    <span class="brand-text font-weight-light">Hospital Admin</span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="index.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a>
        </li>
        <li class="nav-item">
          <a href="doctors.php" class="nav-link"><i class="nav-icon fas fa-user-md"></i><p>Doctors</p></a>
        </li>
        <li class="nav-item">
          <a href="patient.php" class="nav-link"><i class="nav-icon fas fa-user"></i><p>Patients</p></a>
        </li>
        <li class="nav-item">
          <a href="test-category.php" class="nav-link"><i class="nav-icon fas fa-th-list"></i><p>Test Categories</p></a>
        </li>
        <li class="nav-item">
          <a href="test.php" class="nav-link"><i class="nav-icon fas fa-vial"></i><p>Tests</p></a>
        </li>
        <li class="nav-item">
          <a href="entry-list.php" class="nav-link"><i class="nav-icon fas fa-file-medical"></i><p>Entries</p></a>
        </li>
        <li class="nav-item">
          <a href="plan.php" class="nav-link"><i class="nav-icon fas fa-calendar-alt"></i><p>Menu Plan</p></a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
<?php
// Determine active page for menu highlighting
$activePage = basename($_SERVER['PHP_SELF']);
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <span class="brand-text font-weight-light">Pathology Lab</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($username); ?></a>
                <small class="text-muted"><?php echo htmlspecialchars($role); ?></small>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo ($activePage == 'index.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Doctors -->
                <li class="nav-item">
                    <a href="doctor.php" class="nav-link <?php echo ($activePage == 'doctor.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-md"></i>
                        <p>Doctors</p>
                    </a>
                </li>

                <!-- Patients -->
                <li class="nav-item">
                    <a href="patient.php" class="nav-link <?php echo ($activePage == 'patient.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-procedures"></i>
                        <p>Patients</p>
                    </a>
                </li>

                <!-- Test Categories -->
                <li class="nav-item">
                    <a href="test-category.php" class="nav-link <?php echo ($activePage == 'test-category.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-list"></i>
                        <p>Test Categories</p>
                    </a>
                </li>

                <!-- Tests -->
                <li class="nav-item">
                    <a href="test.php" class="nav-link <?php echo ($activePage == 'test.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-vial"></i>
                        <p>Tests</p>
                    </a>
                </li>

                <!-- Entries -->
                <li class="nav-item">
                    <a href="entry-list.php" class="nav-link <?php echo ($activePage == 'entry-list.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-file-medical"></i>
                        <p>Test Entries</p>
                    </a>
                </li>

                <!-- Users (Admin only) -->
                <?php if ($role == 'admin'): ?>
                <li class="nav-item">
                    <a href="user.php" class="nav-link <?php echo ($activePage == 'user.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>