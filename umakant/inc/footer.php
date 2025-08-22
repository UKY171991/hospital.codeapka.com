<?php
// adminlte3/footer.php
?>
    </div> <!-- /.content-wrapper -->
    
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

<script>
$(document).ready(function() {
    // AdminLTE initialization for sidebar
    $('[data-widget="pushmenu"]').PushMenu();
    $('[data-widget="treeview"]').Treeview('init');
    
    console.log('AdminLTE sidebar components initialized');
});
</script>
</body>
</html>