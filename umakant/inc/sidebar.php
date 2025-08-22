<?php
// adminlte3/sidebar.php
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
      <span class="brand-text font-weight-light">Pathology Lab</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-flask"></i>
              <p>
                Patho
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="user-list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Users List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="doctor-list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Doctors List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="patient-list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Patients List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="test-list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Tests List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="test-category-list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Test Categories List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="entry-list.php" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Entry List</p>
                </a>
              </li>
            </ul>
          </li>



          <!-- Add more menu items here -->
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
