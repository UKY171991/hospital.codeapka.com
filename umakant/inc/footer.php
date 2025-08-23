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
<!-- jQuery - already loaded in header -->
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<!-- Debug script to check what's loaded -->
<script>
    $(function() {
        console.log('jQuery version: ' + $.fn.jquery);
        console.log('Bootstrap dropdown plugin: ' + (typeof $.fn.dropdown === 'function' ? 'Loaded' : 'Not loaded'));
        console.log('AdminLTE version: ' + (typeof $.fn.Layout === 'function' ? 'Loaded' : 'Not available'));
    });
</script>

<!-- Custom Scripts -->
<script src="js/sidebar-menu.js"></script>
<script src="js/admin-init.js"></script>
</body>
</html>