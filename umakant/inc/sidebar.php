<?php
// adminlte3/sidebar.php
// Include the sidebar helper functions
require_once 'sidebar-helper.php';
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
        <i class="fas fa-microscope brand-image img-circle elevation-3" style="opacity: .8; margin-left: 10px;"></i>
        <span class="brand-text font-weight-light">Pathology Lab</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-light"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?php echo is_menu_active('dashboard.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <!-- Pathology Menu -->
                <li class="nav-item <?php echo $is_pathology_page ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo $is_pathology_page ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-flask"></i>
                        <p>
                            Pathology Management
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="user-list.php" class="nav-link <?php echo is_menu_active(['user-list.php', 'user.php']) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Users Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="doctor-list.php" class="nav-link <?php echo is_menu_active(['doctor-list.php', 'doctor.php']) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Doctors Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="patient-list.php" class="nav-link <?php echo is_menu_active(['patient-list.php', 'patient.php']) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Patients Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="test-list.php" class="nav-link <?php echo is_menu_active(['test-list.php', 'test.php']) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tests Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="test-category-list.php" class="nav-link <?php echo is_menu_active(['test-category-list.php', 'test-category.php']) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Test Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="entry-list.php" class="nav-link <?php echo is_menu_active(['entry-list.php', 'entry.php']) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Entry Management</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Reports Menu -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="data-export.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Data Export</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Test Reports</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Financial Reports</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Settings Menu -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            Settings
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>System Settings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>User Preferences</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Help Menu -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>Help & Support</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>