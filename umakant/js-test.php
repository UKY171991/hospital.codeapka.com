<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>JavaScript Test Page</h1>
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
                            <h3 class="card-title">AdminLTE JavaScript Components Test</h3>
                        </div>
                        <div class="card-body">
                            <p>This page tests if AdminLTE JavaScript components are working correctly.</p>
                            
                            <h4>Test Results:</h4>
                            <div id="test-results">
                                <p>Checking components...</p>
                            </div>
                            
                            <h4>Manual Tests:</h4>
                            <ul>
                                <li>Try clicking the sidebar toggle button (hamburger icon) in the navbar</li>
                                <li>Try clicking on the "Pathology Management" menu item to expand/collapse it</li>
                                <li>Check if the current page is highlighted in the sidebar</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var results = document.getElementById('test-results');
    var html = '<ul>';
    
    // Check if jQuery is loaded
    if (typeof jQuery !== 'undefined') {
        html += '<li style="color: green;">✓ jQuery is loaded</li>';
    } else {
        html += '<li style="color: red;">✗ jQuery is NOT loaded</li>';
    }
    
    // Check if AdminLTE is loaded
    if (typeof AdminLTE !== 'undefined') {
        html += '<li style="color: green;">✓ AdminLTE is loaded</li>';
    } else {
        html += '<li style="color: orange;">⚠ AdminLTE may not be fully loaded</li>';
    }
    
    // Check if sidebar elements exist
    if (document.querySelector('.main-sidebar')) {
        html += '<li style="color: green;">✓ Sidebar element found</li>';
    } else {
        html += '<li style="color: red;">✗ Sidebar element NOT found</li>';
    }
    
    // Check if treeview elements exist
    if (document.querySelector('.nav-treeview')) {
        html += '<li style="color: green;">✓ Treeview elements found</li>';
    } else {
        html += '<li style="color: red;">✗ Treeview elements NOT found</li>';
    }
    
    html += '</ul>';
    results.innerHTML = html;
});
</script>

<?php include 'inc/footer.php'; ?>