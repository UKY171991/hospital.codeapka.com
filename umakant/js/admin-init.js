/**
 * AdminLTE Component Initialization
 * 
 * This script properly initializes AdminLTE dropdown menus and toggle features
 */

$(document).ready(function() {
    // Initialize all dropdowns
    $('.dropdown-toggle').dropdown();
    
    // Initialize the pushmenu toggle
    $('#sidebarToggle, [data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        console.log('Sidebar toggle clicked');
        $('body').toggleClass('sidebar-collapse');
        
        // Store state in localStorage
        var sidebarState = $('body').hasClass('sidebar-collapse') ? 'collapsed' : 'expanded';
        localStorage.setItem('sidebar-state', sidebarState);
        
        return false;
    });
    
    // Restore sidebar state from localStorage
    var savedSidebarState = localStorage.getItem('sidebar-state');
    if (savedSidebarState === 'collapsed') {
        $('body').addClass('sidebar-collapse');
    }
    
    // Fix user dropdown menu
    $('.user-menu .dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        $(this).parent().toggleClass('show');
        $(this).parent().find('.dropdown-menu').toggleClass('show');
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.user-menu').length) {
            $('.user-menu').removeClass('show');
            $('.user-menu .dropdown-menu').removeClass('show');
        }
    });
    
    // Initialize other AdminLTE components if needed
    if (typeof $.fn.Toasts === 'function') {
        $('.toasts-top-right').Toasts();
    }
    
    console.log('AdminLTE component initialization complete');
});