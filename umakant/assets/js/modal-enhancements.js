/**
 * Modal Enhancement Script for Hospital Management System
 * Handles modal initialization, Select2 integration, and form validation
 */

$(document).ready(function() {
    // Fix for AdminLTE sidebar toggle interaction with modals
    $(document).on('click', '[data-widget="pushmenu"]', function(e) {
        // If a modal is open, prevent the default pushmenu behavior temporarily
        if ($('.modal.show').length > 0) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle sidebar classes manually
            $('body').toggleClass('sidebar-collapse sidebar-open');
            
            // Ensure modal stays visible
            setTimeout(function() {
                $('.modal.show').css('z-index', '1060');
                $('.modal-backdrop').css('z-index', '1055');
            }, 100);
            
            return false;
        }
    });
    
    // Prevent modal from closing when clicking on sidebar
    $(document).on('click', '.main-sidebar, .main-sidebar *', function(e) {
        if ($('.modal.show').length > 0) {
            e.stopPropagation();
        }
    });
    
    // Global modal event handlers
    $('.modal').on('shown.bs.modal', function() {
        const $modal = $(this);

        // Fix for Select2 in modals
        $modal.find('.select2').each(function() {
            const $el = $(this);
            if ($el.hasClass('select2-hidden-accessible')) {
                return;
            }
            $el.select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $modal
            });
        });
        
        // Focus on first input field
        $(this).find('input:not([type=hidden]):first').focus();
        
        // Ensure modal stays on top
        $(this).css('z-index', '1060');
        $('.modal-backdrop').css('z-index', '1055');
        
        // Prevent modal from closing on sidebar interactions
        const modal = $(this);
        modal.off('click.sidebar-fix').on('click.sidebar-fix', function(e) {
            if ($(e.target).closest('.main-sidebar').length > 0) {
                e.stopPropagation();

    $('.modal').on('hidden.bs.modal', function() {
        const $modal = $(this);

        // Clean up Select2 instances
        $modal.find('.select2').each(function() {
            const $el = $(this);
            if ($el.hasClass('select2-hidden-accessible') && $.fn.select2 && $el.data('select2')) {
                $el.select2('destroy');

        // Clear previous validation
        $(form).find('.form-control').removeClass('is-invalid is-valid');
        $(form).find('.invalid-feedback, .valid-feedback').remove();

        // Check required fields
        $(form).find('[required]').each(function() {
            const field = $(this);
            const value = field.val();

            if (!value || value === '') {
                field.addClass('is-invalid');
                field.after('<div class="invalid-feedback">This field is required.</div>');
                isValid = false;
            } else {
                field.addClass('is-valid');
            }
        });

        // Email validation
        $(form).find('input[type="email"]').each(function() {
            const field = $(this);
            const value = field.val();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (value && !emailRegex.test(value)) {
                field.addClass('is-invalid');
                field.after('<div class="invalid-feedback">Please enter a valid email address.</div>');
                isValid = false;
            } else if (value) {
                field.addClass('is-valid');
            }
        });

        return isValid;
    };

    // Enhanced toastr settings for better UX
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };
    
    // Override Bootstrap modal backdrop click behavior
    $(document).on('click', '.modal-backdrop', function(e) {
        // Check if click is on sidebar or header elements
        if ($(e.target).closest('.main-sidebar, .main-header, .control-sidebar').length > 0) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });
    
    // Prevent modal from closing on body clicks when sidebar is involved
    $(document).on('click', 'body', function(e) {
        if ($('.modal.show').length > 0) {
            // If click is on sidebar or its children, don't close modal
            if ($(e.target).closest('.main-sidebar, .main-header, .control-sidebar, [data-widget="pushmenu"]').length > 0) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
    });

    // Loading state helper
    window.setModalLoading = function(modalId, loading) {
        const modal = $('#' + modalId);
        const modalBody = modal.find('.modal-body');
        
        if (loading) {
            modalBody.addClass('loading');
            modal.find('.btn[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');
        } else {
            modalBody.removeClass('loading');
            modal.find('.btn[type="submit"]').prop('disabled', false).html('<i class="fas fa-save mr-2"></i>Save');
        }
    };

    // Enhanced AJAX error handler
    window.handleAjaxError = function(xhr, action = 'perform action') {
        let message = 'Failed to ' + action;
        
        if (xhr.status === 422) {
            message = 'Validation failed. Please check your input.';
        } else if (xhr.status === 404) {
            message = 'Resource not found.';
        } else if (xhr.status === 500) {
            message = 'Server error occurred.';
        } else if (xhr.status === 0) {
            message = 'Network error. Please check your connection.';
        }

        try {
            const response = JSON.parse(xhr.responseText);
            if (response.message) {
                message = response.message;
            }
        } catch (e) {
            // Keep the default message
        }

        toastr.error(message);
        return message;
    };
});

// Global utility functions for modal management
window.ModalUtils = {
    // Open modal with data pre-population
    openModal: function(modalId, title, data = {}) {
        const modal = $('#' + modalId);
        
        if (title) {
            modal.find('.modal-title, #modalTitle').text(title);
        }
        
        // Populate form fields if data provided
        if (Object.keys(data).length > 0) {
            Object.keys(data).forEach(key => {
                const field = modal.find(`[name="${key}"], #${key}`);
                if (field.length) {
                    if (field.hasClass('select2')) {
                        field.val(data[key]).trigger('change');
                    } else {
                        field.val(data[key]);
                    }
                }
            });
        }
        
        modal.modal('show');
    },

    // Close modal with cleanup
    closeModal: function(modalId) {
        $('#' + modalId).modal('hide');
    },

    // Show confirmation dialog
    confirm: function(title, message, callback) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    }
};
