// umakant/assets/js/utils.js

var utils = {
    debounce: function(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    },
    initTooltips: function() {
        $('[data-toggle="tooltip"]').tooltip();
    },
    showLoading: function(selector) {
        $(selector).append('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');
    },
    hideLoading: function(selector) {
        $(selector).find('.overlay').remove();
    },
    showError: function(message) {
        showAlert(message, 'error'); // Assuming showAlert is global or defined in doctor.js
    },
    showSuccess: function(message) {
        showAlert(message, 'success'); // Assuming showAlert is global or defined in doctor.js
    },
    confirm: function(message, title) {
        return new Promise((resolve) => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    resolve(result.isConfirmed);
                });
            } else {
                resolve(confirm(message));
            }
        });
    },
    validateForm: function(formData, rules) {
        const errors = [];
        for (const field in rules) {
            const rule = rules[field];
            const value = formData[field];

            if (rule.required && (!value || value.trim() === '')) {
                errors.push(`${rule.label || field} is required.`);
            }
            if (value && rule.minLength && value.length < rule.minLength) {
                errors.push(`${rule.label || field} must be at least ${rule.minLength} characters long.`);
            }
            if (value && rule.type === 'email' && !/^[^
@]+@[^
@]+\.[^
@]+$/.test(value)) {
                errors.push(`Invalid ${rule.label || field} format.`);
            }
            // Add more validation types as needed
        }
        return errors;
    },
    generateAvatar: function(name, bgColorClass) {
        const initials = name ? name.charAt(0).toUpperCase() : '?';
        return `<div class="avatar-circle ${bgColorClass}">${initials}</div>`;
    }
};