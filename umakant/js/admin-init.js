/**
 * AdminLTE Component Initialization
 * 
 * This script properly initializes AdminLTE dropdown menus and toggle features
 */

$(function() {
    console.log('Initializing AdminLTE components...');
    
    // Direct initialization of AdminLTE components
    if ($.fn.AdminLTE) {
        console.log('Using AdminLTE native initialization');
    }
    
    // Initialize all dropdowns
    if ($.fn.dropdown) {
        console.log('Bootstrap dropdown plugin loaded');
        $('.dropdown-toggle').dropdown();
    } else {
        console.error('Bootstrap dropdown plugin not available');
    }
    
    // Initialize Layout and PushMenu
    if ($.fn.Layout) {
        $('body').Layout();
        console.log('AdminLTE Layout initialized');
    }
    
    if ($.fn.pushMenu) {
        $('[data-widget="pushmenu"]').pushMenu();
        console.log('AdminLTE PushMenu initialized');
    }
    
    // Initialize the sidebar toggle manually if AdminLTE's PushMenu is not available
    $('#sidebarToggle, [data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        console.log('Sidebar toggle clicked');
        $('body').toggleClass('sidebar-collapse');
        $('.main-sidebar').toggleClass('sidebar-closed sidebar-collapse');
        
        // Store state in localStorage
        var sidebarState = $('body').hasClass('sidebar-collapse') ? 'collapsed' : 'expanded';
        localStorage.setItem('sidebar-state', sidebarState);
        
        return false;
    });
    
    // Restore sidebar state from localStorage
    var savedSidebarState = localStorage.getItem('sidebar-state');
    if (savedSidebarState === 'collapsed') {
        $('body').addClass('sidebar-collapse');
        $('.main-sidebar').addClass('sidebar-closed sidebar-collapse');
    }
    
    // Handle all dropdowns manually
    $('.nav-item.dropdown').each(function() {
        var $dropdown = $(this);
        var $toggle = $dropdown.find('.dropdown-toggle');
        var $menu = $dropdown.find('.dropdown-menu');
        
        $toggle.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $dropdown.toggleClass('show');
            $menu.toggleClass('show');
        });
    });
    
    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown').removeClass('show');
            $('.dropdown-menu').removeClass('show');
        }
    });
    
    // Enable control sidebar
    $('[data-widget="control-sidebar"]').on('click', function(e) {
        e.preventDefault();
        $('body').toggleClass('control-sidebar-slide-open');
        $('.control-sidebar').toggleClass('control-sidebar-open');
    });
    
    // Initialize other AdminLTE components
    if ($.fn.Toasts) {
        $('.toasts-top-right').Toasts();
    }
    
    // Initialize treeview menus
    $('.nav-treeview').each(function() {
        var $treeview = $(this);
        var $parent = $treeview.closest('.nav-item');
        
        if ($parent.hasClass('menu-open') || $treeview.find('.nav-link.active').length > 0) {
            $treeview.show();
            $parent.addClass('menu-open');
        } else {
            $treeview.hide();
        }
    });
    
    console.log('AdminLTE component initialization complete');
});