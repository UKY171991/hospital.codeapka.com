/**
 * Sidebar Menu Functionality
 * 
 * This script enhances AdminLTE's sidebar with custom behavior
 * - Handle menu open/close toggle properly
 * - Ensure proper styling for active menu items
 */

$(document).ready(function() {
    // Let AdminLTE handle the basic initialization
    // We'll just add our custom enhancements
    enhanceSidebar();
    
    function enhanceSidebar() {
        console.log('Enhancing sidebar menu...');
        
        // Handle menu item clicks for proper toggle behavior
        $('.nav-sidebar .nav-item > .nav-link').on('click', function(e) {
            var $parent = $(this).parent('.nav-item');
            var $treeview = $parent.find('.nav-treeview').first();
            
            // If this menu item has a submenu
            if ($treeview.length > 0) {
                e.preventDefault();
                
                // If this is the currently active menu, don't close it
                if ($parent.hasClass('menu-open')) {
                    // Allow closing if clicked directly on the parent menu item
                    if ($(e.target).closest('.nav-treeview').length === 0) {
                        $parent.removeClass('menu-open');
                        $treeview.slideUp();
                        $(this).find('.right').removeClass('rotate-90');
                    }
                } else {
                    // Close all other open menus at the same level
                    $parent.siblings('.menu-open').removeClass('menu-open').find('.nav-treeview').slideUp();
                    
                    // Open this menu
                    $parent.addClass('menu-open');
                    $treeview.slideDown();
                    $(this).find('.right').addClass('rotate-90');
                }
            }
        });
        
        // Ensure active menu is visible
        if ($('.nav-treeview .nav-link.active').length > 0) {
            $('.nav-treeview .nav-link.active').parents('.nav-item').addClass('menu-open');
            $('.nav-treeview .nav-link.active').parents('.nav-treeview').show();
            $('.nav-treeview .nav-link.active').parents('.nav-item').children('.nav-link').addClass('active');
        }
        
        console.log('Sidebar enhancements complete');
    }
});