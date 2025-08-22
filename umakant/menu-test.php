<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Menu Dropdown Test</h1>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sidebar Menu Dropdown Test</h3>
                </div>
                <div class="card-body">
                    <p>This page is used to test if the sidebar menu dropdown functionality is working correctly.</p>
                    <p>Try clicking on the "Pathology Management" menu item in the sidebar to see if it expands/collapses properly.</p>
                    
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Current Page Information:</h5>
                        <ul>
                            <li><strong>Current Page:</strong> <?php echo basename($_SERVER['PHP_SELF']); ?></li>
                            <li><strong>Is Pathology Page:</strong> <?php echo in_array(basename($_SERVER['PHP_SELF']), ['user-list.php', 'doctor-list.php', 'patient-list.php', 'test-list.php', 'test-category-list.php', 'entry-list.php', 'user.php', 'doctor.php', 'patient.php', 'test.php', 'test-category.php', 'entry.php']) ? 'Yes' : 'No'; ?></li>
                            <li><strong>Menu Should Be:</strong> <?php echo in_array(basename($_SERVER['PHP_SELF']), ['user-list.php', 'doctor-list.php', 'patient-list.php', 'test-list.php', 'test-category-list.php', 'entry-list.php', 'user.php', 'doctor.php', 'patient.php', 'test.php', 'test-category.php', 'entry.php']) ? 'Open' : 'Closed'; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'inc/footer.php'; ?>