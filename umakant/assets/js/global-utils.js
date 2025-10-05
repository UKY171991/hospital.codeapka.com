// Initialize global namespace for Hospital Management System
window.HMS = window.HMS || {};

// Configuration
HMS.config = {
    baseApiUrl: 'ajax/',
    currentPage: 1,
    recordsPerPage: 10,
    dateFormat: 'YYYY-MM-DD',
    currency: '₹'
};

// Global state
HMS.state = {
    isLoading: false,
    currentUser: null,
    permissions: {}
};

// Global utility functions
HMS.utils = {
    // Show loading spinner
    showLoading: function(selector = '.loading') {
        $(selector).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
    },

    // Hide loading spinner
    hideLoading: function(selector = '.loading') {
        $(selector).empty();
    },

    // Show success message
    showSuccess: function(message, duration = 3000) {
        this.showAlert(message, 'success', duration);
    },

    // Show error message
    showError: function(message, duration = 5000) {
        this.showAlert(message, 'danger', duration);
    },

    // Show warning message
    showWarning: function(message, duration = 4000) {
        this.showAlert(message, 'warning', duration);
    },

    // Show info message
    showInfo: function(message, duration = 3000) {
        this.showAlert(message, 'info', duration);
    },

    // Generic alert function
    showAlert: function(message, type = 'info', duration = 3000) {
        const alertId = 'alert-' + Date.now();
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999; min-width: 300px;">
                <strong>${type.charAt(0).toUpperCase() + type.slice(1)}!</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto-hide after duration
        setTimeout(() => {
            $(`#${alertId}`).alert('close');
        }, duration);
    },

    // Format date for display
    formatDate: function(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    },

    // Format datetime for display
    formatDateTime: function(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    // Generate avatar circle with initials
    generateAvatar: function(name, color = null) {
        if (!name) return '<div class="avatar-circle bg-secondary">?</div>';
        
        const initials = name.split(' ').map(word => word.charAt(0)).join('').substring(0, 2).toUpperCase();
        const colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
        const bgColor = color || colors[Math.floor(Math.random() * colors.length)];
        
        return `<div class="avatar-circle ${bgColor} text-white">${initials}</div>`;
    },

    // Confirm dialog
    confirm: function(message, title = 'Confirm Action') {
        return new Promise((resolve) => {
            const confirmId = 'confirm-' + Date.now();
            const modalHtml = `
                <div class="modal fade" id="${confirmId}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${title}</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                ${message}
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-danger confirm-yes">Yes, Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            const modal = $(`#${confirmId}`);
            
            modal.on('click', '.confirm-yes', function() {
                modal.modal('hide');
                resolve(true);
            });
            
            modal.on('hidden.bs.modal', function() {
                modal.remove();
                resolve(false);
            });
            
            modal.modal('show');
        });
    },

    // Validate form data
    validateForm: function(formData, rules) {
        const errors = [];
        
        for (const field in rules) {
            const value = formData[field];
            const rule = rules[field];
            
            if (rule.required && (!value || value.trim() === '')) {
                errors.push(`${rule.label || field} is required`);
                continue;
            }
            
            if (value && rule.minLength && value.length < rule.minLength) {
                errors.push(`${rule.label || field} must be at least ${rule.minLength} characters`);
            }
            
            if (value && rule.maxLength && value.length > rule.maxLength) {
                errors.push(`${rule.label || field} must not exceed ${rule.maxLength} characters`);
            }
            
            if (value && rule.pattern && !rule.pattern.test(value)) {
                errors.push(`${rule.label || field} format is invalid`);
            }
            
            if (value && rule.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                errors.push(`${rule.label || field} must be a valid email address`);
            }
            
            if (value && rule.type === 'phone' && !/^[\+]?[1-9][\d]{0,15}$/.test(value.replace(/\s/g, ''))) {
                errors.push(`${rule.label || field} must be a valid phone number`);
            }
        }
        
        return errors;
    },

    // API request wrapper
    apiRequest: function(url, method = 'GET', data = null) {
        const options = {
            url: url,
            method: method,
            dataType: 'json',
            beforeSend: function() {
                // Show loading indicator
                $('#loadingIndicator').show();
            },
            complete: function() {
                // Hide loading indicator
                $('#loadingIndicator').hide();
            }
        };
        
        if (data) {
            if (method === 'GET') {
                options.data = data;
            } else {
                options.data = JSON.stringify(data);
                options.contentType = 'application/json';
            }
        }
        
        return $.ajax(options);
    },

    // Parse jqXHR to produce a concise user-friendly error message
    parseAjaxError: function(jqxhr) {
        // jqxhr may be null in some failure modes
        if (!jqxhr) return 'Unknown network error';

        // Try to parse JSON response if present
        var respText = jqxhr.responseText || '';
        try {
            var json = JSON.parse(respText || '{}');
            if (json && json.message) return json.message;
        } catch (e) {
            // not JSON
        }

        // Fallback messages based on status
        if (jqxhr.status === 0) return 'Network error: Could not reach the server.';
        if (jqxhr.status >= 500) return 'Server error (' + jqxhr.status + '): ' + (jqxhr.statusText || 'Internal Server Error');
        if (jqxhr.status >= 400) return 'Request error (' + jqxhr.status + '): ' + (jqxhr.statusText || 'Bad request');

        // Last resort: include a short portion of responseText
        return (jqxhr.status ? ('Error ' + jqxhr.status + ': ') : '') + (jqxhr.statusText || '') + (respText ? ' - ' + respText.substring(0, 400) : '');
    },

    // Format currency
    formatCurrency: function(amount, currency = '₹') {
        if (!amount) return currency + '0.00';
        return currency + parseFloat(amount).toFixed(2);
    },

    // Debounce function for search inputs
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Initialize tooltips
    initTooltips: function() {
        $('[data-toggle="tooltip"]').tooltip();
    },

    // Initialize popovers
    initPopovers: function() {
        $('[data-toggle="popover"]').popover();
    },

    // Reset form
    resetForm: function(formSelector) {
        $(formSelector)[0].reset();
        $(formSelector).find('.is-invalid').removeClass('is-invalid');
        $(formSelector).find('.invalid-feedback').remove();
    },

    // Show form validation errors
    showFormErrors: function(formSelector, errors) {
        $(formSelector).find('.is-invalid').removeClass('is-invalid');
        $(formSelector).find('.invalid-feedback').remove();
        
        errors.forEach(error => {
            if (error.field) {
                const field = $(formSelector).find(`[name="${error.field}"]`);
                field.addClass('is-invalid');
                field.after(`<div class="invalid-feedback">${error.message}</div>`);
            }
        });
    },

    // Export table to CSV
    exportTableToCSV: function(tableSelector, filename = 'export.csv') {
        const csv = [];
        const table = $(tableSelector);
        
        // Get headers
        const headers = [];
        table.find('thead th').each(function() {
            headers.push($(this).text().trim());
        });
        csv.push(headers.join(','));
        
        // Get data rows
        table.find('tbody tr').each(function() {
            const row = [];
            $(this).find('td').each(function() {
                row.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
            });
            csv.push(row.join(','));
        });
        
        // Download
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }
};

// Global loading indicator
$(document).ready(function() {
    // Add global loading indicator
    if ($('#loadingIndicator').length === 0) {
        $('body').append(`
            <div id="loadingIndicator" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; background: rgba(0,0,0,0.8); color: white; padding: 20px; border-radius: 10px;">
                <i class="fas fa-spinner fa-spin"></i> Loading...
            </div>
        `);
    }
    
    // Initialize tooltips and popovers
    HMS.utils.initTooltips();
    HMS.utils.initPopovers();
    
    // Add smooth scrolling to anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = this.hash;
        if (target && $(target).length) {
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 300);
        }
    });
    
    // Auto-close alerts after 5 seconds
    $('.alert').each(function() {
        const alert = $(this);
        if (!alert.hasClass('alert-permanent')) {
            setTimeout(() => {
                alert.fadeOut();
            }, 5000);
        }
    });

    // Global jQuery AJAX error handler to show parsed server messages
    $(document).ajaxError(function(event, jqxhr, settings, thrown) {
        // Ignore OPTIONS preflight messages
        if (jqxhr && jqxhr.status === 0 && !navigator.onLine) {
            utils.showError('Network offline. Please check your internet connection.');
            return;
        }

        var msg = HMS.utils.parseAjaxError(jqxhr);
        HMS.utils.showError(msg, 7000);
    });
});
// Make HMS.utils available under window.utils for backward compatibility
window.utils = HMS.utils;

// Show content in the global view modal
HMS.utils.showViewModal = function(title, htmlContent){
    $('#globalViewModalLabel').text(title || 'Details');
    $('#globalViewModalBody').html(htmlContent || '<div class="text-muted">No details available</div>');
    $('#globalViewModal').modal('show');
};

// Notify other modules that HMS is ready
$(function(){
    try{ $(document).trigger('HMS:ready'); }catch(e){ /* ignore */ }
});
