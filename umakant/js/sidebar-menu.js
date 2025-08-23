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
        // Handle parent menu item clicks for toggle behavior
        $('.nav-sidebar .has-treeview > .nav-link, .nav-sidebar .nav-item > .nav-link').off('click').on('click', function(e) {
            var $navItem = $(this).parent('.nav-item');
            var $treeview = $navItem.find('.nav-treeview').first();
            
            // Only handle if this item has a submenu
            if ($treeview.length > 0) {
                e.preventDefault();
                console.log('Menu item with submenu clicked');
                
                // Toggle the menu-open class and slide animation
                if ($navItem.hasClass('menu-open')) {
                    if ($(e.target).closest('.nav-treeview').length === 0) {
                        $navItem.removeClass('menu-open');
                        $treeview.slideUp(300);
                        $(this).find('.right').removeClass('rotate-90');
                    }
                } else {
                    // Optionally close other open menus at the same level
                    $navItem.siblings('.menu-open').each(function() {
                        $(this).removeClass('menu-open');
                        $(this).find('.nav-treeview').first().slideUp(300);
                        $(this).find('.nav-link .right').removeClass('rotate-90');
                    });
                    
                    // Open this menu
                    $navItem.addClass('menu-open');
                    $treeview.slideDown(300);
                    $(this).find('.right').addClass('rotate-90');
                }
                
                return false;
            }
            
            // Allow default link behavior for items without submenu
        });
        
        // Ensure initially active menu is visible
        var $activeLinks = $('.nav-sidebar .nav-link.active');
        
        $activeLinks.each(function() {
            var $link = $(this);
            var $parentItem = $link.closest('.nav-item');
            var $parentTreeview = $link.closest('.nav-treeview');
            
            if ($parentTreeview.length > 0) {
                // This is a submenu item
                console.log('Active submenu item found');
                $parentTreeview.show();
                $parentTreeview.parents('.nav-item').addClass('menu-open');
                $parentTreeview.parents('.nav-item').children('.nav-link').addClass('active');
                $parentTreeview.parents('.nav-item').find('.right').addClass('rotate-90');
            }
        });
        
        console.log('Sidebar enhancements complete');
    }
});