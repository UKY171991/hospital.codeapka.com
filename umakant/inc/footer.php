<?php
// adminlte3/footer.php
?>
    </div> <!-- /.content-wrapper -->
    
    <!-- REQUIRED SCRIPTS -->
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- AdminLTE for demo purposes -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/demo.js"></script>
    
    <script>
    $(document).ready(function() {
        // AdminLTE initialization
        $('body').Layout();
        $('.nav-sidebar').Treeview('init');
        $('[data-widget="pushmenu"]').PushMenu();
        
        console.log('AdminLTE components initialized successfully');
    });
    </script>
</body>
</html>
