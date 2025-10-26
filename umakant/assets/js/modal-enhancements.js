/**
 * Modal Enhancements
 * Provides enhanced modal functionality
 */

$(document).ready(function() {
    // Modal enhancement functionality
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('input:first').focus();
    });
    
    // Auto-focus first input in modals
    $('.modal').on('show.bs.modal', function() {
        var modal = $(this);
        setTimeout(function() {
            modal.find('input:visible:first').focus();
        }, 100);
    });
});