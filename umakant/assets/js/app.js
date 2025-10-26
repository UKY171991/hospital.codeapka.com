/**
 * Main Application JavaScript
 * Core application functionality
 */

$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-toggle="popover"]').popover();
    
    // Handle AJAX errors globally
    $(document).ajaxError(function(event, xhr, settings, error) {
        if (xhr.status === 401) {
            window.location.href = 'login.php';
        } else if (xhr.status >= 500) {
            console.error('Server error:', error);
        }
    });
    
    // Global loading state
    $(document).ajaxStart(function() {
        window.isLoading = true;
    }).ajaxStop(function() {
        window.isLoading = false;
    });
});