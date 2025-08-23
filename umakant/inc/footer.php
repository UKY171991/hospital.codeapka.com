<?php
// adminlte3/footer.php
?>
    </div> <!-- /.content-wrapper -->
    
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
        <div class="p-3">
            <h5>Customization</h5>
            <p>Customize the layout and theme options here.</p>
        </div>
    </aside>
    <!-- /.control-sidebar -->
    
    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2025 <a href="dashboard.php">Pathology Lab Management System</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>
</div> <!-- /.wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<!-- Custom Sidebar Menu Script -->
<script>
$(document).ready(function() {
    // Initialize sidebar menu
    
    // Click handler for parent menu items
    $('.nav-sidebar .nav-item > .nav-link').on('click', function(e) {
        var $this = $(this);
        var $parent = $this.parent('.nav-item');
        var $treeview = $parent.find('.nav-treeview').first();
        
        if ($treeview.length > 0) {
            e.preventDefault(); // Prevent navigation for parent menu items
            
            // Toggle menu-open class
            $parent.toggleClass('menu-open');
            
            // Toggle submenu visibility with animation
            if ($parent.hasClass('menu-open')) {
                $treeview.slideDown(300);
            } else {
                $treeview.slideUp(300);
            }
            
            console.log('Menu toggled');
        }
    });
    
    // Ensure sidebar toggle button works
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('sidebar-collapse');
        console.log('Sidebar toggle clicked');
    });
    
    // Make sure submenus of active menu items are visible
    $('.nav-sidebar .nav-item.menu-open > .nav-treeview').show();
    
    console.log('Sidebar initialization complete');
});
</script>
</body>
</html>