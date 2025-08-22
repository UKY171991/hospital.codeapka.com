<?php
// adminlte3/navbar.php
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="dashboard.php" class="nav-link">Home</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- User Account Menu -->
    <li class="nav-item dropdown user-menu">
      <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle fa-lg mr-1"></i>
        <span class="d-none d-md-inline">
          <?php 
          // Display username if available
          echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; 
          ?>
        </span>
      </a>
      <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <!-- User image -->
        <li class="user-header bg-primary">
          <i class="fas fa-user-circle fa-6x text-light"></i>
          <p>
            <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>
            <small>Member since <?php echo isset($_SESSION['created_at']) ? date('M Y', strtotime($_SESSION['created_at'])) : date('M Y'); ?></small>
          </p>
        </li>
        <!-- Menu Footer-->
        <li class="user-footer">
          <a href="#" class="btn btn-default btn-flat" onclick="alert('Profile feature coming soon!')">Profile</a>
          <a href="logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
        </li>
      </ul>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
