/**
 * Modal Enhancement Script for Hospital Management System
 * Handles modal initialization, Select2 integration, and form validation
 */

$(document).ready(function() {
    // Global modal event handlers
    $('.modal').on('shown.bs.modal', function() {
        // Fix for Select2 in modals
        $(this).find('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $(this)
        });
        
        // Focus on first input field
        $(this).find('input:not([type=hidden]):first').focus();
    });

    $('.modal').on('hidden.bs.modal', function() {
        // Clean up Select2 instances
        $(this).find('.select2').select2('destroy');
        
        // Clear form validation states
        $(this).find('.form-control').removeClass('is-invalid is-valid');
        $(this).find('.invalid-feedback, .valid-feedback').remove();
        
        // Reset form
        $(this).find('form')[0]?.reset();
    });

    // Form validation helper
    window.validateModalForm = function(formId) {
        const form = document.getElementById(formId);
        let isValid = true;

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
