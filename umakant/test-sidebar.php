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
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include 'inc/footer.php'; ?>