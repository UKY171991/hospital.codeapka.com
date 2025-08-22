<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard - Pathology Lab Management System</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <span class="text-muted">Last Updated: <?php echo date('d M Y H:i:s'); ?></span>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <!-- Summary Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <?php
                            require_once 'inc/connection.php';
                            $stmt = $pdo->query('SELECT COUNT(*) as count FROM users');
                            $userCount = $stmt->fetch()['count'];
                            ?>
                            <h3><?php echo $userCount; ?></h3>
                            <p>Total Users</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="user-list.php" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <?php
                            $stmt = $pdo->query('SELECT COUNT(*) as count FROM doctors');
                            $doctorCount = $stmt->fetch()['count'];
                            ?>
                            <h3><?php echo $doctorCount; ?></h3>
                            <p>Total Doctors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <a href="doctor-list.php" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <?php
                            $stmt = $pdo->query('SELECT COUNT(*) as count FROM patients');
                            $patientCount = $stmt->fetch()['count'];
                            ?>
                            <h3><?php echo $patientCount; ?></h3>
                            <p>Total Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <a href="patient-list.php" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <?php
                            $stmt = $pdo->query('SELECT COUNT(*) as count FROM tests');
                            $testCount = $stmt->fetch()['count'];
                            ?>
                            <h3><?php echo $testCount; ?></h3>
                            <p>Total Tests</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <a href="test-list.php" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Data Tables Overview -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Database Tables Overview</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Table Name</th>
                                            <th>Description</th>
                                            <th>Total Records</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Users</strong></td>
                                            <td>System users and administrators</td>
                                            <td><span class="badge badge-info"><?php echo $userCount; ?></span></td>
                                            <td>
                                                <a href="user-list.php" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View All
                                                </a>
                                                <a href="user.php" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Add New
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Doctors</strong></td>
                                            <td>Medical practitioners and specialists</td>
                                            <td><span class="badge badge-success"><?php echo $doctorCount; ?></span></td>
                                            <td>
                                                <a href="doctor-list.php" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View All
                                                </a>
                                                <a href="doctor.php" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Add New
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Patients</strong></td>
                                            <td>Patient information and details</td>
                                            <td><span class="badge badge-warning"><?php echo $patientCount; ?></span></td>
                                            <td>
                                                <a href="patient-list.php" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View All
                                                </a>
                                                <a href="patient.php" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Add New
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tests</strong></td>
                                            <td>Laboratory tests and procedures</td>
                                            <td><span class="badge badge-danger"><?php echo $testCount; ?></span></td>
                                            <td>
                                                <a href="test-list.php" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View All
                                                </a>
                                                <a href="test.php" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Add New
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Test Categories</strong></td>
                                            <td>Categories and classifications of tests</td>
                                            <td>
                                                <?php
                                                $stmt = $pdo->query('SELECT COUNT(*) as count FROM test_categories');
                                                $categoryCount = $stmt->fetch()['count'];
                                                ?>
                                                <span class="badge badge-secondary"><?php echo $categoryCount; ?></span>
                                            </td>
                                            <td>
                                                <a href="test-category-list.php" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View All
                                                </a>
                                                <a href="test-category.php" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Add New
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Entries</strong></td>
                                            <td>Test orders and patient entries</td>
                                            <td>
                                                <?php
                                                $stmt = $pdo->query('SELECT COUNT(*) as count FROM entries');
                                                $entryCount = $stmt->fetch()['count'];
                                                ?>
                                                <span class="badge badge-primary"><?php echo $entryCount; ?></span>
                                            </td>
                                            <td>
                                                <a href="entry-list.php" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View All
                                                </a>
                                                <a href="entry.php" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus"></i> Add New
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Users</h3>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $pdo->query('SELECT username, full_name, role, created_at FROM users ORDER BY created_at DESC LIMIT 5');
                            $recentUsers = $stmt->fetchAll();
                            ?>
                            <div class="list-group">
                                <?php foreach ($recentUsers as $user): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($user['full_name']); ?> (<?php echo htmlspecialchars($user['role']); ?>)</small>
                                        </div>
                                        <small class="text-muted"><?php echo date('d M Y', strtotime($user['created_at'])); ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Patients</h3>
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $pdo->query('SELECT client_name, mobile_number, created_at FROM patients ORDER BY created_at DESC LIMIT 5');
                            $recentPatients = $stmt->fetchAll();
                            ?>
                            <div class="list-group">
                                <?php foreach ($recentPatients as $patient): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($patient['client_name']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($patient['mobile_number']); ?></small>
                                        </div>
                                        <small class="text-muted"><?php echo date('d M Y', strtotime($patient['created_at'])); ?></small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'inc/footer.php'; ?>
