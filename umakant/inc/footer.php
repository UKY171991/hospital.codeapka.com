<?php
// adminlte3/footer.php
?>
    </div> <!-- /.wrapper -->
    
    <!-- REQUIRED SCRIPTS -->
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- Custom Script for Sidebar -->
    <script>
    $(document).ready(function() {
        // Initialize sidebar menu
        $('.nav-sidebar a').on('click', function(e) {
            var $this = $(this);
            var target = $this.attr('href');
            
            // Handle dropdown menus
            if (target === '#' || target === 'javascript:;') {
                e.preventDefault();
                
                var $parent = $this.parent('.nav-item');
                var $treeview = $parent.find('.nav-treeview');
                
                if ($treeview.length > 0) {
                    // Toggle the menu
                    if ($parent.hasClass('menu-open')) {
                        $parent.removeClass('menu-open');
                        $treeview.slideUp(300);
                        $this.find('.fa-angle-left').removeClass('fa-angle-down');
                    } else {
                        // Close other open menus
                        $('.nav-item.menu-open').removeClass('menu-open').find('.nav-treeview').slideUp(300);
                        $('.nav-item.menu-open .fa-angle-left').removeClass('fa-angle-down');
                        
                        // Open this menu
                        $parent.addClass('menu-open');
                        $treeview.slideDown(300);
                        $this.find('.fa-angle-left').addClass('fa-angle-down');
                    }
                }
            }
        });
        
        // Set active menu item based on current page
        var currentPage = window.location.pathname.split('/').pop();
        $('.nav-sidebar .nav-link').each(function() {
            var href = $(this).attr('href');
            if (href && href.indexOf(currentPage) !== -1 && currentPage !== '') {
                $(this).addClass('active');
                // If it's a submenu item, open the parent menu
                var $parent = $(this).closest('.nav-item').parent().closest('.nav-item');
                if ($parent.length > 0) {
                    $parent.addClass('menu-open');
                    $parent.find('.nav-treeview').show();
                    $parent.find('.fa-angle-left').addClass('fa-angle-down');
                }
            }
        });
        
        // Sidebar toggle functionality
        $('[data-widget="pushmenu"]').on('click', function(e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-collapse');
        });
        
        console.log('AdminLTE sidebar initialized successfully');
    });
    </script>
</body>
</html>
