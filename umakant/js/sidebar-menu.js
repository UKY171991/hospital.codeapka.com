/**
 * Sidebar Menu Functionality
 * 
 * This script enhances AdminLTE's sidebar with custom behavior
 * - Handle menu open/close toggle properly
 * - Ensure proper styling for active menu items
 */

$(function() {
    console.log('Enhancing sidebar menu...');
    enhanceSidebar();
    
    function enhanceSidebar() {
        // Simple click handler for all parent menu items
        $('.nav-sidebar .nav-item > .nav-link').on('click', function(e) {
            var $navItem = $(this).parent('.nav-item');
            var $treeview = $navItem.children('.nav-treeview').first();
            
            // Only handle toggle if this item has a submenu
            if ($treeview.length > 0) {
                e.preventDefault();
                e.stopPropagation(); // Prevent event bubbling
                console.log('Menu item with submenu clicked:', $navItem.find('p').first().text());
                
                $navItem.toggleClass('menu-open');
                
                if ($navItem.hasClass('menu-open')) {
                    $treeview.slideDown(300);
                    $(this).find('.right').addClass('rotate-90');
                } else {
                    $treeview.slideUp(300);
                    $(this).find('.right').removeClass('rotate-90');
                }
                
                return false;
            }
        });
        
        // Force all active menu paths to be visible on page load
        $('.nav-treeview .nav-link.active').each(function() {
            var $link = $(this);
            var $parentItem = $link.closest('.nav-item').parent('.nav-treeview').parent('.nav-item');
            
            // Make sure parent menu is open
            if ($parentItem.length > 0) {
                console.log('Setting parent menu open:', $parentItem.find('p').first().text());
                $parentItem.addClass('menu-open');
                $parentItem.children('.nav-treeview').show();
                $parentItem.children('.nav-link').find('.right').addClass('rotate-90');
            }
        });
        
        // Debug logging to check what's active
        console.log('Active menu items:', $('.nav-link.active').length);
        console.log('Menu items with treeview:', $('.nav-item').has('.nav-treeview').length);
        console.log('Sidebar enhancements complete');
    }
});