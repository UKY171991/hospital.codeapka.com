/**
 * Global Utilities
 * Common functions used across the application
 */

// Global namespace
window.HMS = window.HMS || {};
window.HMS.utils = window.HMS.utils || {};

// HTML escape function
window.HMS.utils.escapeHtml = function(text) {
    if (text == null) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
};

// Loading state management
window.isLoading = false;

// Pagination globals
window.currentPage = 1;
window.recordsPerPage = 25;

// Debug logging
window.APP_DEBUG = false;
window.APP_LOG = function() {
    if (window.APP_DEBUG && console && console.log) {
        console.log.apply(console, arguments);
    }
};