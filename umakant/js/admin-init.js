/**
 * AdminLTE Component Initialization
 * 
 * This script properly initializes AdminLTE dropdown menus and toggle features
 */

$(function() {
    console.log('Initializing AdminLTE components with direct DOM methods...');
    
    // Ensure Bootstrap jQuery plugins are loaded
    if ($.fn.dropdown) {
        console.log('Bootstrap dropdown plugin loaded');
    } else {
        console.error('Bootstrap dropdown plugin not loaded!');
    }
    
    // Force initialize all dropdown toggles
    $('.dropdown-toggle').dropdown();
    
    // Simple direct handler for sidebar toggle button
    $('#sidebarToggle, [data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Sidebar toggle button clicked');
        $('body').toggleClass('sidebar-collapse');
        return false;
    });
    
    // Direct DOM handlers for top navbar dropdowns
    $('.navbar .nav-item.dropdown > .nav-link').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $dropdown = $(this).parent();
        var $menu = $dropdown.find('.dropdown-menu').first();
        
        // Close all other dropdowns
        $('.navbar .dropdown').not($dropdown).removeClass('show');
        $('.navbar .dropdown-menu').not($menu).removeClass('show');
        
        // Toggle this dropdown
        $dropdown.toggleClass('show');
        $menu.toggleClass('show');
        
        console.log('Navbar dropdown clicked:', $dropdown.hasClass('show') ? 'opened' : 'closed');
        return false;
    });
    
    // Close dropdowns when clicking elsewhere on the page
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown').removeClass('show');
            $('.dropdown-menu').removeClass('show');
        }
    });
    
    // Initialize AdminLTE sidebar menu
    if ($.fn.Treeview) {
        $('[data-widget="treeview"]').Treeview('init');
        console.log('AdminLTE Treeview initialized via plugin');
    }
    
    // Debug output
    console.log('Body classes:', $('body').attr('class'));
    console.log('AdminLTE component initialization complete');
});