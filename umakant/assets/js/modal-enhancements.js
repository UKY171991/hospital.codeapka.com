/**
 * Modal Enhancement Script for Hospital Management System
 * Handles modal initialization, Select2 integration, and form validation
 */

$(document).ready(function() {
    // Fix for AdminLTE sidebar toggle interaction with modals
    $(document).on('click', '[data-widget="pushmenu"]', function(e) {
        if ($('.modal.show').length > 0) {
            e.preventDefault();
            e.stopPropagation();

            $('body').toggleClass('sidebar-collapse sidebar-open');

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

        if ($.fn.select2) {
            $modal.find('.select2').each(function() {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible')) {
                    return;
                }

                try {
                    $el.select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        dropdownParent: $modal
                    });
                } catch (err) {
                    // Ignore initialization errors from third-party plugins
                    console.warn('Select2 initialization skipped:', err);
                }
            });
        }

        // Focus on first input field
        $modal.find('input:not([type=hidden]):first').focus();

        // Ensure modal stays on top
        $modal.css('z-index', '1060');
        $('.modal-backdrop').css('z-index', '1055');

        // Prevent modal from closing on sidebar interactions
        $modal.off('click.sidebar-fix').on('click.sidebar-fix', function(event) {
            if ($(event.target).closest('.main-sidebar').length > 0) {
                event.stopPropagation();
                return false;
            }
        });
    });

    $('.modal').on('hidden.bs.modal', function() {
        const $modal = $(this);

        if ($.fn.select2) {
            $modal.find('.select2').each(function() {
                const $el = $(this);
                if ($el.hasClass('select2-hidden-accessible') && $el.data('select2')) {
                    try {
                        $el.select2('destroy');
                    } catch (err) {
                        console.warn('Select2 cleanup skipped:', err);
                    }
                }
            });
        }

        // Clear form validation states
        $modal.find('.form-control').removeClass('is-invalid is-valid');
        $modal.find('.invalid-feedback, .valid-feedback').remove();

        // Reset forms within the modal
        $modal.find('form').each(function() {
            if (typeof this.reset === 'function') {
                this.reset();
            }
        });
    });

    // Override Bootstrap modal backdrop click behavior
    $(document).on('click', '.modal-backdrop', function(e) {
        if ($(e.target).closest('.main-sidebar, .main-header, .control-sidebar').length > 0) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });

    // Prevent modal from closing on body clicks when sidebar is involved
    $(document).on('click', 'body', function(e) {
        if ($('.modal.show').length > 0) {
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

    // Enhanced toastr settings for better UX
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        preventDuplicates: false,
        onclick: null,
        showDuration: '300',
        hideDuration: '1000',
        timeOut: '5000',
        extendedTimeOut: '1000',
        showEasing: 'swing',
        hideEasing: 'linear',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
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
        } catch (error) {
            // Keep default message
        }

        toastr.error(message);
        return message;
    };
});

// Global utility functions for modal management
window.ModalUtils = {
    openModal: function(modalId, title, data = {}) {
        const modal = $('#' + modalId);

        if (title) {
            modal.find('.modal-title, #modalTitle').text(title);
        }

        if (Object.keys(data).length > 0) {
            Object.keys(data).forEach(function(key) {
                const selector = `[name="${key}"], #${key}`;
                const field = modal.find(selector);
                if (!field.length) {
                    return;
                }

                if (field.hasClass('select2')) {
                    field.val(data[key]).trigger('change');
                } else {
                    field.val(data[key]);
                }
            });
        }

        modal.modal('show');
    },

    closeModal: function(modalId) {
        $('#' + modalId).modal('hide');
    },

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
        }).then(function(result) {
            if (result.isConfirmed && typeof callback === 'function') {
                callback();
            }
        });
    }
};
