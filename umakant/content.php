<?php
// adminlte3/content.php
require_once 'inc/connection.php';

// Get counts from database
try {
    $userCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    $doctorCount = $pdo->query('SELECT COUNT(*) FROM doctors')->fetchColumn();
    $patientCount = $pdo->query('SELECT COUNT(*) FROM patients')->fetchColumn();
    $testCount = $pdo->query('SELECT COUNT(*) FROM tests')->fetchColumn();
    $entryCount = $pdo->query('SELECT COUNT(*) FROM entries')->fetchColumn();
} catch (PDOException $e) {
    // Set default values if tables don't exist yet
    $userCount = 0;
    $doctorCount = 0;
    $patientCount = 0;
    $testCount = 0;
    $entryCount = 0;
}
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div>
        </div>
      </div>
    </div>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Dashboard Cards -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $userCount ?></h3>
                <p>Users</p>
              </div>
              <div class="icon">
                <i class="fas fa-users"></i>
              </div>
              <a href="user.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?= $doctorCount ?></h3>
                <p>Doctors</p>
              </div>
              <div class="icon">
                <i class="fas fa-user-md"></i>
              </div>
              <a href="doctor.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?= $patientCount ?></h3>
                <p>Patients</p>
              </div>
              <div class="icon">
                <i class="fas fa-procedures"></i>
              </div>
              <a href="patient.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?= $testCount ?></h3>
                <p>Tests</p>
              </div>
              <div class="icon">
                <i class="fas fa-vials"></i>
              </div>
              <a href="test.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
              <div class="inner">
                <h3><?= $entryCount ?></h3>
                <p>Entries</p>
              </div>
              <div class="icon">
                <i class="fas fa-list-alt"></i>
              </div>
              <a href="entry-list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>
</div>
