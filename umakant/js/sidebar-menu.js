/**
 * Sidebar Menu Functionality
 * 
 * This script enhances AdminLTE's sidebar with custom behavior
 * - Always keep submenus of menu-open items visible
 * - Ensure proper styling for active menu items
 */

$(document).ready(function() {
    // Let AdminLTE handle the basic initialization
    // We'll just add our custom enhancements
    enhanceSidebar();
    
    function enhanceSidebar() {
        console.log('Enhancing sidebar menu...');
        
        // Make sure all menu-open items have their submenus visible
        $('.nav-sidebar .nav-item.menu-open > .nav-treeview').show();
        
        // Add proper active class to parent menu items when a child is active
        if ($('.nav-treeview .nav-link.active').length > 0) {
            $('.nav-treeview .nav-link.active').parents('.nav-item').addClass('menu-open');
            $('.nav-treeview .nav-link.active').parents('.nav-treeview').show();
        }
        
        // Remove default click behavior for sidebar items with submenus
        // to keep them always expanded
        $('.nav-sidebar .nav-item.has-treeview > .nav-link').off('click');
        
        console.log('Sidebar enhancements complete');
    }
});