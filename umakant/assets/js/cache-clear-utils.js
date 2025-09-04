// Clear browser cache and reload utility
// This script helps ensure all new changes are loaded properly

$(document).ready(function() {
    // Clear any existing DataTable instances
    $.fn.dataTable.ext.errMode = 'none';
    
    // Show loading indicator for tables
    $('.table-enhanced').addClass('table-loading');
    
    // Add cache-busting to AJAX requests
    $.ajaxSetup({
        cache: false,
        beforeSend: function(xhr, settings) {
            if (settings.url.indexOf('?') === -1) {
                settings.url += '?v=' + Date.now();
            } else {
                settings.url += '&v=' + Date.now();
            }
        }
    });
    
    // Configure toastr for consistent notifications
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            preventDuplicates: true,
            onclick: null,
            showDuration: 300,
            hideDuration: 1000,
            timeOut: 5000,
            extendedTimeOut: 1000,
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };
    }
    
    // Global error handler for AJAX requests
    $(document).ajaxError(function(event, jqXHR, settings, thrownError) {
        console.error('AJAX Error:', settings.url, thrownError);
        if (typeof toastr !== 'undefined') {
            toastr.error('Network error: ' + thrownError);
        }
    });
    
    // Remove loading indicator after a short delay
    setTimeout(function() {
        $('.table-enhanced').removeClass('table-loading');
    }, 1000);
    
    console.log('Global utilities initialized successfully');
});

// Utility functions
window.clearCacheAndReload = function() {
    // Clear localStorage
    if (typeof Storage !== 'undefined') {
        localStorage.clear();
        sessionStorage.clear();
    }
    
    // Force reload with cache bust
    window.location.href = window.location.href + (window.location.href.indexOf('?') > -1 ? '&' : '?') + 'nocache=' + Date.now();
};

window.refreshAllTables = function() {
    $('.table-enhanced').each(function() {
        const tableId = $(this).attr('id');
        if (tableId && $.fn.DataTable.isDataTable('#' + tableId)) {
            $('#' + tableId).DataTable().ajax.reload(null, false);
        }
    });
};
