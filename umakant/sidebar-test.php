<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Sidebar Test Page</h1>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Sidebar Functionality Test</h3>
                        </div>
                        <div class="card-body">
                            <p>This page is for testing the sidebar functionality.</p>
                            <p>Check if:</p>
                            <ul>
                                <li>The sidebar menu expands/collapses properly</li>
                                <li>The active menu item is highlighted correctly</li>
                                <li>The "Pathology Management" menu opens automatically on related pages</li>
                                <li>The sidebar toggle button works correctly</li>
                            </ul>
                            
                            <h4>Current Page Information:</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Current File</th>
                                    <td><?php echo basename($_SERVER['PHP_SELF']); ?></td>
                                </tr>
                                <tr>
                                    <th>Is User List Page</th>
                                    <td><?php echo (basename($_SERVER['PHP_SELF']) == 'user-list.php') ? 'Yes' : 'No'; ?></td>
                                </tr>
                                <tr>
                                    <th>Should Menu Be Open</th>
                                    <td><?php echo (in_array(basename($_SERVER['PHP_SELF']), ['user-list.php', 'doctor-list.php', 'patient-list.php', 'test-list.php', 'test-category-list.php', 'entry-list.php'])) ? 'Yes' : 'No'; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'inc/footer.php'; ?>