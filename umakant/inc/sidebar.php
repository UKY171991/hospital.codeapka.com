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
      'main-test-category.php','test-category.php','test.php','upload_zip.php','upload_list.php',
      'entry-list.php','plan.php','notice.php','pathology_reports.php'
    ];
    $isPathologyActive = in_array($activePage, $pathologyPages);
    // OPD menu and its pages - adjust filenames as needed
    $opdPages = ['opd_dashboard.php', 'opd_patient.php','opd_doctor.php','opd_departments.php','opd_specializations.php','opd_appointments.php','opd_appointment_types.php','opd_facilities.php','opd_medical_records.php','opd_prescriptions.php','opd_users.php', 'opd_billing.php', 'opd_reports.php'];
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
              <a href="opd_users.php" class="nav-link <?php echo ($activePage == 'opd_users.php') ? 'active' : ''; ?>">
                <i class="fas fa-users nav-icon"></i>
                <p>Users</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_patient.php" class="nav-link <?php echo ($activePage == 'opd_patient.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-injured nav-icon"></i>
                <p>Patients</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_doctor.php" class="nav-link <?php echo ($activePage == 'opd_doctor.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-md nav-icon"></i>
                <p>Doctors</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_departments.php" class="nav-link <?php echo ($activePage == 'opd_departments.php') ? 'active' : ''; ?>">
                <i class="fas fa-building nav-icon"></i>
                <p>Departments</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_specializations.php" class="nav-link <?php echo ($activePage == 'opd_specializations.php') ? 'active' : ''; ?>">
                <i class="fas fa-stethoscope nav-icon"></i>
                <p>Specializations</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_appointments.php" class="nav-link <?php echo ($activePage == 'opd_appointments.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check nav-icon"></i>
                <p>Appointments</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_appointment_types.php" class="nav-link <?php echo ($activePage == 'opd_appointment_types.php') ? 'active' : ''; ?>">
                <i class="fas fa-tags nav-icon"></i>
                <p>Appointment Types</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_facilities.php" class="nav-link <?php echo ($activePage == 'opd_facilities.php') ? 'active' : ''; ?>">
                <i class="fas fa-hospital nav-icon"></i>
                <p>Facilities</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_medical_records.php" class="nav-link <?php echo ($activePage == 'opd_medical_records.php') ? 'active' : ''; ?>">
                <i class="fas fa-file-medical nav-icon"></i>
                <p>Medical Records</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="opd_prescriptions.php" class="nav-link <?php echo ($activePage == 'opd_prescriptions.php') ? 'active' : ''; ?>">
                <i class="fas fa-prescription nav-icon"></i>
                <p>Prescriptions</p>
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
              <a href="main-test-category.php" class="nav-link <?php echo ($activePage == 'main-test-category.php') ? 'active' : ''; ?>">
                <i class="fas fa-th-large nav-icon"></i>
                <p>Main Test Categories</p>
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
              <a href="pathology_reports.php" class="nav-link <?php echo ($activePage == 'pathology_reports.php') ? 'active' : ''; ?>">
                <i class="fas fa-file-medical-alt nav-icon"></i>
                <p>Reports</p>
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

        <?php
        // Email menu pages
        $emailPages = ['email.php', 'email_compose.php', 'email_inbox.php', 'email_sent.php'];
        $isEmailActive = in_array($activePage, $emailPages);
        ?>
        <li class="nav-item has-treeview <?php echo $isEmailActive ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo $isEmailActive ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-envelope"></i>
            <p>
              Email
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="email_inbox.php" class="nav-link <?php echo ($activePage == 'email_inbox.php') ? 'active' : ''; ?>">
                <i class="fas fa-inbox nav-icon"></i>
                <p>Inbox</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="email_compose.php" class="nav-link <?php echo ($activePage == 'email_compose.php') ? 'active' : ''; ?>">
                <i class="fas fa-edit nav-icon"></i>
                <p>Compose</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="email_sent.php" class="nav-link <?php echo ($activePage == 'email_sent.php') ? 'active' : ''; ?>">
                <i class="fas fa-paper-plane nav-icon"></i>
                <p>Sent</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="email.php" class="nav-link <?php echo ($activePage == 'email.php') ? 'active' : ''; ?>">
                <i class="fas fa-cog nav-icon"></i>
                <p>Settings</p>
              </a>
            </li>
          </ul>
        </li>

        <?php
        // Inventory menu pages
        $inventoryPages = ['inventory_dashboard.php', 'inventory_income.php', 'inventory_expense.php', 'inventory_client.php', 'email_parser_settings.php'];
        $isInventoryActive = in_array($activePage, $inventoryPages);
        ?>
        <li class="nav-item has-treeview <?php echo $isInventoryActive ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo $isInventoryActive ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-boxes"></i>
            <p>
              Inventory
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="inventory_dashboard.php" class="nav-link <?php echo ($activePage == 'inventory_dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line nav-icon"></i>
                <p>Inventory Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="inventory_income.php" class="nav-link <?php echo ($activePage == 'inventory_income.php') ? 'active' : ''; ?>">
                <i class="fas fa-arrow-up nav-icon"></i>
                <p>Income</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="inventory_expense.php" class="nav-link <?php echo ($activePage == 'inventory_expense.php') ? 'active' : ''; ?>">
                <i class="fas fa-arrow-down nav-icon"></i>
                <p>Expense</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="inventory_client.php" class="nav-link <?php echo ($activePage == 'inventory_client.php') ? 'active' : ''; ?>">
                <i class="fas fa-users nav-icon"></i>
                <p>Client</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="email_parser_settings.php" class="nav-link <?php echo ($activePage == 'email_parser_settings.php') ? 'active' : ''; ?>">
                <i class="fas fa-robot nav-icon"></i>
                <p>Email Parser</p>
              </a>
            </li>
          </ul>
        </li>

        <?php
        // Clients menu pages
        $clientsPages = ['client_dashboard.php', 'clients.php', 'tasks.php'];
        $isClientsActive = in_array($activePage, $clientsPages);
        ?>
        <li class="nav-item has-treeview <?php echo $isClientsActive ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?php echo $isClientsActive ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-user-tie"></i>
            <p>
              Clients
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="client_dashboard.php" class="nav-link <?php echo ($activePage == 'client_dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-line nav-icon"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="clients.php" class="nav-link <?php echo ($activePage == 'clients.php') ? 'active' : ''; ?>">
                <i class="fas fa-users nav-icon"></i>
                <p>Clients</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="tasks.php" class="nav-link <?php echo ($activePage == 'tasks.php') ? 'active' : ''; ?>">
                <i class="fas fa-tasks nav-icon"></i>
                <p>Tasks</p>
              </a>
            </li>
          </ul>
        </li>

  <!-- Admins menu removed -->

  <!-- Logout moved to top navbar for easier access -->
      </ul>
    </nav>
  </div>
</aside>
