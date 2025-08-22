/**
 * Sidebar Menu Functionality
 * 
 * This script handles the expanding/collapsing of sidebar menu items
 * and ensures proper menu state management.
 */

$(document).ready(function() {
    // Initialize sidebar menu
    initSidebar();
    
    function initSidebar() {
        console.log('Initializing sidebar menu...');
        
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
                
                console.log('Menu toggled: ' + $this.find('p').text());
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
    }
});