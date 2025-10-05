// Global initialization for Hospital Management System
window.HMS = window.HMS || {};

// Configuration
HMS.config = {
    debug: true,
    baseUrl: '/umakant/',
    apiUrl: '/umakant/ajax/',
    dateFormat: 'YYYY-MM-DD',
    timeFormat: 'HH:mm:ss',
    currency: '₹',
    defaultPageSize: 10
};

// Global state
HMS.state = {
    isLoading: false,
    currentUser: null,
    currentPage: null
};

// Global utilities
HMS.utils = {
    // Show loading state
    showLoading: function(message = 'Loading...') {
        HMS.state.isLoading = true;
        if ($.fn.LoadingOverlay) {
            $.LoadingOverlay("show", {
                text: message
            });
        }
    },

    // Hide loading state
    hideLoading: function() {
        HMS.state.isLoading = false;
        if ($.fn.LoadingOverlay) {
            $.LoadingOverlay("hide");
        }
    },

    // Show success message using toastr
    showSuccess: function(message) {
        if (window.toastr) {
            toastr.success(message);
        } else {
            alert(message);
        }
    },

    // Show error message using toastr
    showError: function(message) {
        if (window.toastr) {
            toastr.error(message);
        } else {
            alert('Error: ' + message);
        }
    },

    // Show warning message using toastr
    showWarning: function(message) {
        if (window.toastr) {
            toastr.warning(message);
        } else {
            alert('Warning: ' + message);
        }
    },

    // Format date
    formatDate: function(date) {
        if (!date) return '';
        return moment(date).format(HMS.config.dateFormat);
    },

    // Format currency
    formatCurrency: function(amount) {
        if (typeof amount !== 'number') return amount;
        return HMS.config.currency + amount.toFixed(2);
    },

    // Escape HTML to prevent XSS
    escapeHtml: function(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    },

    // Initialize DataTable with common settings
    initDataTable: function(selector, options = {}) {
        const defaultOptions = {
            processing: true,
            serverSide: false,
            responsive: true,
            dom: 'Bfrtip',
            pageLength: HMS.config.defaultPageSize,
            buttons: ['copy', 'excel', 'pdf', 'print'],
            language: {
                processing: '<i class="fa fa-spinner fa-spin"></i> Processing...'
            }
        };

        return $(selector).DataTable({
            ...defaultOptions,
            ...options
        });
    },

    // Initialize Select2 with common settings
    initSelect2: function(selector, options = {}) {
        const defaultOptions = {
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select an option'
        };

        return $(selector).select2({
            ...defaultOptions,
            ...options
        });
    }
};

// Initialize toastr settings
if (window.toastr) {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 5000
    };
}

// Global AJAX settings
$.ajaxSetup({
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    error: function(xhr, status, error) {
        if (xhr.status === 401) {
            HMS.utils.showError('Your session has expired. Please login again.');
            window.location.href = HMS.config.baseUrl + 'login.php';
        } else if (xhr.status === 403) {
            HMS.utils.showError('You do not have permission to perform this action.');
        } else {
            HMS.utils.showError('An error occurred: ' + error);
        }
    }
});