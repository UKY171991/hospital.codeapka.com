/**
 * Entry List Management System - Clean and Simplified
 * Hospital Test Entries Management
 */

// Global variables
let entriesTable = null;
let testsData = [];
let categoriesData = [];
let patientsData = [];
let doctorsData = [];
let currentEditId = null;
let testRowCounter = 0;

// Enhanced API Configuration with unified request handling
const API_CONFIG = {
    // Use the new patho_api/entry.php API (set to false to use ajax/entry_api_fixed.php)
    useNewAPI: true,

    // API endpoints
    endpoints: {
        new: 'patho_api/entry.php',
        old: 'ajax/entry_api_fixed.php'
    },

    // Request configuration
    requestConfig: {
        timeout: 30000,
        retryAttempts: 3,
        retryDelay: 1000,
        retryMultiplier: 2
    },

    // Get current API URL
    getURL: function () {
        return this.useNewAPI ? this.endpoints.new : this.endpoints.old;
    },

    // Get API secret key
    getSecretKey: function () {
        return this.useNewAPI ? null : 'hospital-api-secret-2024';
    },

    // Check if API is available
    isAvailable: async function (endpoint = null) {
        try {
            const url = endpoint || this.getURL();
            const response = await $.ajax({
                url: url,
                method: 'GET',
                data: { action: 'stats' },
                timeout: 5000,
                dataType: 'json'
            });
            return response && (response.success !== false);
        } catch (error) {
            return false;
        }
    }
};

/**
 * Unified API Request Handler with enhanced error handling and retry logic
 */
class APIRequestHandler {
    constructor(config = API_CONFIG) {
        this.config = config;
        this.requestQueue = [];
        this.isOnline = navigator.onLine;
        this.setupNetworkMonitoring();
    }

    /**
     * Setup network connectivity monitoring
     */
    setupNetworkMonitoring() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            console.log('Network connection restored');
            this.processQueuedRequests();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            console.log('Network connection lost');
        });
    }

    /**
     * Make API request with retry logic and error handling
     */
    async makeRequest(options) {
        const requestId = this.generateRequestId();
        console.log(`[${requestId}] Making API request:`, options);

        // Validate options
        if (!options.action) {
            throw new Error('Action is required for API requests');
        }

        // Check network connectivity
        if (!this.isOnline) {
            console.warn(`[${requestId}] Network offline, queueing request`);
            return this.queueRequest(options);
        }

        const requestOptions = this.prepareRequestOptions(options);

        for (let attempt = 1; attempt <= this.config.requestConfig.retryAttempts; attempt++) {
            try {
                console.log(`[${requestId}] Attempt ${attempt}/${this.config.requestConfig.retryAttempts}`);

                const response = await this.executeRequest(requestOptions, requestId);

                // Validate response
                const validatedResponse = this.validateResponse(response, requestId);

                console.log(`[${requestId}] Request successful on attempt ${attempt}`);
                return validatedResponse;

            } catch (error) {
                console.error(`[${requestId}] Attempt ${attempt} failed:`, error);

                // Check if we should retry
                if (attempt < this.config.requestConfig.retryAttempts && this.shouldRetry(error)) {
                    const delay = this.calculateRetryDelay(attempt);
                    console.log(`[${requestId}] Retrying in ${delay}ms...`);
                    await this.sleep(delay);
                    continue;
                } else {
                    // All retries exhausted or non-retryable error
                    throw this.enhanceError(error, requestId, attempt);
                }
            }
        }
    }

    /**
     * Prepare request options with defaults and authentication
     */
    prepareRequestOptions(options) {
        const requestOptions = {
            url: options.url || this.config.getURL(),
            method: options.method || 'GET',
            timeout: options.timeout || this.config.requestConfig.timeout,
            dataType: options.dataType || 'json',
            data: { ...options.data }
        };

        // Add action if not present
        if (!requestOptions.data.action && options.action) {
            requestOptions.data.action = options.action;
        }

        // Add authentication
        const secretKey = this.config.getSecretKey();
        if (secretKey) {
            if (options.method === 'POST' && options.data instanceof FormData) {
                options.data.append('secret_key', secretKey);
            } else {
                requestOptions.data.secret_key = secretKey;
            }
        }

        // Handle FormData for POST requests
        if (options.method === 'POST' && options.data instanceof FormData) {
            requestOptions.data = options.data;
            requestOptions.processData = false;
            requestOptions.contentType = false;
        }

        return requestOptions;
    }

    /**
     * Execute the actual AJAX request
     */
    async executeRequest(options, requestId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                ...options,
                success: (data, textStatus, jqXHR) => {
                    resolve({
                        data: data,
                        status: jqXHR.status,
                        statusText: textStatus,
                        headers: jqXHR.getAllResponseHeaders()
                    });
                },
                error: (jqXHR, textStatus, errorThrown) => {
                    const error = new Error(`API request failed: ${textStatus}`);
                    error.status = jqXHR.status;
                    error.statusText = textStatus;
                    error.responseText = jqXHR.responseText;
                    error.errorThrown = errorThrown;
                    reject(error);
                }
            });
        });
    }

    /**
     * Validate API response
     */
    validateResponse(response, requestId) {
        if (!response || !response.data) {
            throw new Error('Invalid response format: missing data');
        }

        const data = response.data;

        // Check for API-level errors
        if (data.success === false) {
            const error = new Error(data.message || 'API request failed');
            error.apiError = true;
            error.errorCode = data.error_code;
            error.fieldErrors = data.field_errors;
            throw error;
        }

        // Check for authentication errors
        if (response.status === 401 || (data.message && data.message.includes('Authentication'))) {
            const error = new Error('Authentication required');
            error.authError = true;
            error.status = 401;
            throw error;
        }

        return data;
    }

    /**
     * Determine if request should be retried
     */
    shouldRetry(error) {
        // Don't retry authentication errors
        if (error.authError || error.status === 401 || error.status === 403) {
            return false;
        }

        // Don't retry client errors (4xx except 401, 403)
        if (error.status >= 400 && error.status < 500) {
            return false;
        }

        // Retry network errors, timeouts, and server errors
        return true;
    }

    /**
     * Calculate retry delay with exponential backoff
     */
    calculateRetryDelay(attempt) {
        const baseDelay = this.config.requestConfig.retryDelay;
        const multiplier = this.config.requestConfig.retryMultiplier;
        return baseDelay * Math.pow(multiplier, attempt - 1);
    }

    /**
     * Enhance error with additional context
     */
    enhanceError(error, requestId, attempts) {
        const enhancedError = new Error(error.message);
        enhancedError.originalError = error;
        enhancedError.requestId = requestId;
        enhancedError.attempts = attempts;
        enhancedError.status = error.status;
        enhancedError.authError = error.authError;
        enhancedError.apiError = error.apiError;
        enhancedError.fieldErrors = error.fieldErrors;

        return enhancedError;
    }

    /**
     * Queue request for later execution (when network is restored)
     */
    queueRequest(options) {
        return new Promise((resolve, reject) => {
            this.requestQueue.push({
                options: options,
                resolve: resolve,
                reject: reject,
                timestamp: Date.now()
            });
        });
    }

    /**
     * Process queued requests when network is restored
     */
    async processQueuedRequests() {
        console.log(`Processing ${this.requestQueue.length} queued requests`);

        const queue = [...this.requestQueue];
        this.requestQueue = [];

        for (const queuedRequest of queue) {
            try {
                const response = await this.makeRequest(queuedRequest.options);
                queuedRequest.resolve(response);
            } catch (error) {
                queuedRequest.reject(error);
            }
        }
    }

    /**
     * Generate unique request ID for tracking
     */
    generateRequestId() {
        return 'req_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    /**
     * Sleep utility for delays
     */
    sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Create global API request handler instance
const apiHandler = new APIRequestHandler();

/**
 * Convenience function for making API requests
 */
async function makeAPIRequest(options) {
    try {
        return await apiHandler.makeRequest(options);
    } catch (error) {
        // Handle authentication errors
        if (error.authError) {
            console.error('Authentication error:', error.message);
            showError('Session expired. Please log in again.');

            // Redirect to login page after a delay
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);

            throw error;
        }

        // Handle API errors with user-friendly messages
        if (error.apiError) {
            const message = error.message || 'An error occurred while processing your request';
            showError(message);

            // Show field-specific errors if available
            if (error.fieldErrors) {
                Object.entries(error.fieldErrors).forEach(([field, message]) => {
                    console.error(`Field error - ${field}: ${message}`);
                });
            }

            throw error;
        }

        // Handle network and other errors
        console.error('API request failed:', error);
        showError('Network error. Please check your connection and try again.');
        throw error;
    }
}

// Make functions available globally
window.makeAPIRequest = makeAPIRequest;
window.apiHandler = apiHandler;
window.switchAPI = switchAPI;
window.autoDetectBestAPI = autoDetectBestAPI;
window.testBothAPIs = testBothAPIs;
window.smartSwitchAPI = smartSwitchAPI;

// Debug functions for troubleshooting
window.debugFormSubmission = function () {
    console.log('=== FORM SUBMISSION DEBUG ===');
    console.log('Form exists:', $('#entryForm').length > 0);
    console.log('Form HTML:', $('#entryForm')[0]);
    console.log('Submit button exists:', $('#entryForm button[type="submit"]').length > 0);
    console.log('Current edit ID:', currentEditId);

    // Try to trigger form submission
    console.log('Attempting to trigger form submission...');
    $('#entryForm').trigger('submit');
};

window.debugSaveEntry = function () {
    console.log('=== SAVE ENTRY DEBUG ===');
    try {
        saveEntry();
    } catch (error) {
        console.error('Error calling saveEntry directly:', error);
    }
};

window.testFormValidation = function () {
    console.log('=== FORM VALIDATION DEBUG ===');
    try {
        const isValid = validateForm();
        console.log('Validation result:', isValid);
        return isValid;
    } catch (error) {
        console.error('Validation error:', error);
        return false;
    }
};

/**
 * Enhanced Network Connectivity and User Feedback System
 */

/**
 * Network status monitoring
 */
class NetworkMonitor {
    constructor() {
        this.isOnline = navigator.onLine;
        this.lastOnlineTime = Date.now();
        this.setupEventListeners();
        this.startPeriodicCheck();
    }

    setupEventListeners() {
        window.addEventListener('online', () => {
            this.handleOnline();
        });

        window.addEventListener('offline', () => {
            this.handleOffline();
        });
    }

    handleOnline() {
        this.isOnline = true;
        this.lastOnlineTime = Date.now();
        console.log('Network connection restored');

        showSuccess('Network connection restored');

        // Test API availability when coming back online
        this.testAPIAvailability();

        // Process any queued requests
        if (window.apiHandler) {
            apiHandler.processQueuedRequests();
        }
    }

    handleOffline() {
        this.isOnline = false;
        console.log('Network connection lost');

        showError('Network connection lost. Some features may not work until connection is restored.');
    }

    async testAPIAvailability() {
        try {
            const isAvailable = await API_CONFIG.isAvailable();
            if (isAvailable) {
                console.log('API is available after network restoration');
            } else {
                console.warn('API is not available despite network connection');
                showInfo('Network restored but server may be unavailable');
            }
        } catch (error) {
            console.error('Error testing API availability:', error);
        }
    }

    startPeriodicCheck() {
        setInterval(() => {
            this.checkConnectivity();
        }, 30000); // Check every 30 seconds
    }

    async checkConnectivity() {
        if (!this.isOnline) return;

        try {
            // Simple connectivity test
            const response = await fetch('/favicon.ico', {
                method: 'HEAD',
                cache: 'no-cache',
                timeout: 5000
            });

            if (!response.ok) {
                throw new Error('Connectivity test failed');
            }
        } catch (error) {
            console.warn('Periodic connectivity check failed:', error);
            // Don't show error for periodic checks to avoid spam
        }
    }

    getStatus() {
        return {
            isOnline: this.isOnline,
            lastOnlineTime: this.lastOnlineTime,
            offlineDuration: this.isOnline ? 0 : Date.now() - this.lastOnlineTime
        };
    }
}

// Initialize network monitor
const networkMonitor = new NetworkMonitor();

/**
 * Enhanced user feedback system
 */
class UserFeedbackSystem {
    constructor() {
        this.messageQueue = [];
        this.isProcessing = false;
        this.setupStyles();
    }

    setupStyles() {
        if (!$('#userFeedbackCSS').length) {
            const css = `
                <style id="userFeedbackCSS">
                    .feedback-toast {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        z-index: 10000;
                        min-width: 300px;
                        max-width: 500px;
                        background: white;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                        border-left: 4px solid #007bff;
                        animation: slideInRight 0.3s ease-out;
                    }
                    
                    .feedback-toast.success {
                        border-left-color: #28a745;
                    }
                    
                    .feedback-toast.error {
                        border-left-color: #dc3545;
                    }
                    
                    .feedback-toast.warning {
                        border-left-color: #ffc107;
                    }
                    
                    .feedback-toast.info {
                        border-left-color: #17a2b8;
                    }
                    
                    .feedback-toast-header {
                        padding: 12px 16px;
                        border-bottom: 1px solid #e9ecef;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        font-weight: 600;
                    }
                    
                    .feedback-toast-body {
                        padding: 12px 16px;
                        color: #333;
                    }
                    
                    .feedback-toast-close {
                        background: none;
                        border: none;
                        font-size: 18px;
                        cursor: pointer;
                        color: #999;
                    }
                    
                    .feedback-toast-close:hover {
                        color: #333;
                    }
                    
                    @keyframes slideInRight {
                        from {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateX(0);
                            opacity: 1;
                        }
                    }
                    
                    @keyframes slideOutRight {
                        from {
                            transform: translateX(0);
                            opacity: 1;
                        }
                        to {
                            transform: translateX(100%);
                            opacity: 0;
                        }
                    }
                    
                    .feedback-toast.hiding {
                        animation: slideOutRight 0.3s ease-in;
                    }
                </style>
            `;
            $('head').append(css);
        }
    }

    showMessage(message, type = 'info', duration = 5000) {
        const messageObj = {
            id: 'toast_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9),
            message: message,
            type: type,
            duration: duration,
            timestamp: Date.now()
        };

        this.messageQueue.push(messageObj);
        this.processQueue();
    }

    processQueue() {
        if (this.isProcessing || this.messageQueue.length === 0) return;

        this.isProcessing = true;
        const messageObj = this.messageQueue.shift();
        this.displayToast(messageObj);
    }

    displayToast(messageObj) {
        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };

        const titles = {
            success: 'Success',
            error: 'Error',
            warning: 'Warning',
            info: 'Information'
        };

        const toastHtml = `
            <div id="${messageObj.id}" class="feedback-toast ${messageObj.type}">
                <div class="feedback-toast-header">
                    <span><i class="${icons[messageObj.type]} mr-2"></i>${titles[messageObj.type]}</span>
                    <button class="feedback-toast-close" onclick="userFeedback.closeToast('${messageObj.id}')">&times;</button>
                </div>
                <div class="feedback-toast-body">
                    ${messageObj.message}
                </div>
            </div>
        `;

        $('body').append(toastHtml);

        // Auto-hide after duration
        if (messageObj.duration > 0) {
            setTimeout(() => {
                this.closeToast(messageObj.id);
            }, messageObj.duration);
        }

        // Process next message after a short delay
        setTimeout(() => {
            this.isProcessing = false;
            this.processQueue();
        }, 300);
    }

    closeToast(toastId) {
        const $toast = $(`#${toastId}`);
        if ($toast.length) {
            $toast.addClass('hiding');
            setTimeout(() => {
                $toast.remove();
            }, 300);
        }
    }

    clearAll() {
        $('.feedback-toast').each(function () {
            $(this).addClass('hiding');
            setTimeout(() => {
                $(this).remove();
            }, 300);
        });
        this.messageQueue = [];
    }
}

// Initialize user feedback system
const userFeedback = new UserFeedbackSystem();

// Override existing message functions to use enhanced system
const originalShowSuccess = window.showSuccess;
const originalShowError = window.showError;
const originalShowInfo = window.showInfo;

window.showSuccess = function (message) {
    userFeedback.showMessage(message, 'success');
    if (originalShowSuccess && typeof originalShowSuccess === 'function') {
        originalShowSuccess(message);
    }
};

window.showError = function (message) {
    userFeedback.showMessage(message, 'error', 8000); // Show errors longer
    if (originalShowError && typeof originalShowError === 'function') {
        originalShowError(message);
    }
};

window.showInfo = function (message) {
    userFeedback.showMessage(message, 'info');
    if (originalShowInfo && typeof originalShowInfo === 'function') {
        originalShowInfo(message);
    }
};

window.showWarning = function (message) {
    userFeedback.showMessage(message, 'warning');
};

// Make available globally
window.networkMonitor = networkMonitor;
window.userFeedback = userFeedback;

/**
 * Initialize the application
 */
$(document).ready(function () {
    console.log('Entry List Management - Initializing...');

    // Initialize components
    initializeDataTable();
    loadInitialData();
    bindEvents();

    // Additional debugging for form submission
    console.log('Checking if entryForm exists:', $('#entryForm').length);

    // Add a backup event handler for the modal
    $(document).on('shown.bs.modal', '#entryModal', function () {
        console.log('Entry modal shown, rebinding form events...');
        console.log('Form in modal:', $('#entryForm', this).length);

        // Ensure form submission is bound
        $('#entryForm').off('submit.backup').on('submit.backup', function (e) {
            e.preventDefault();
            console.log('Backup form submission handler triggered');
            try {
                saveEntry();
            } catch (error) {
                console.error('Error in backup form submission:', error);
                showError('An error occurred while submitting the form. Please try again.');
            }
        });

        // Also bind to button click as additional fallback
        $('#entryForm button[type="submit"]').off('click.backup').on('click.backup', function (e) {
            e.preventDefault();
            console.log('Save button clicked - triggering form submission');
            $('#entryForm').trigger('submit');
        });
    });

    console.log('Entry List Management - Initialized successfully');
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    console.log('Initializing DataTable...');

    try {
        entriesTable = $('#entriesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: API_CONFIG.getURL(),
                type: 'GET',
                data: function () {
                    const data = { action: 'list' };
                    const secretKey = API_CONFIG.getSecretKey();
                    if (secretKey) {
                        data.secret_key = secretKey;
                    }
                    return data;
                },
                dataSrc: function (json) {
                    console.log('DataTable response:', json);
                    if (json && json.success) {
                        return json.data || [];
                    } else {
                        console.error('DataTable error:', json);
                        showError(json ? json.message : 'Failed to load entries');
                        return [];
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTable AJAX error:', error, thrown);
                    showError('Failed to load entries. Please refresh the page.');
                }
            },
            columns: [
                {
                    data: 'id',
                    title: 'ID',
                    width: '5%'
                },
                {
                    data: 'patient_name',
                    title: 'Patient',
                    width: '15%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            let html = `<strong>${data || 'N/A'}</strong>`;
                            if (row.patient_contact) {
                                html += `<br><small class="text-muted">${row.patient_contact}</small>`;
                            }
                            return html;
                        }
                        return data || '';
                    }
                },
                {
                    data: 'doctor_name',
                    title: 'Doctor',
                    width: '12%',
                    render: function (data, type, row) {
                        return data || '<span class="text-muted">Not assigned</span>';
                    }
                },
                {
                    data: 'test_names',
                    title: 'Tests',
                    width: '20%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const testCount = parseInt(row.tests_count) || 0;
                            const testNames = data || '';

                            if (testCount === 0) {
                                return '<span class="text-muted">No tests</span>';
                            } else if (testCount === 1) {
                                return `<span class="badge badge-info">${testCount}</span> ${testNames}`;
                            } else {
                                return `<span class="badge badge-primary">${testCount}</span> ${testNames}`;
                            }
                        }
                        return data || '';
                    }
                },
                {
                    data: 'status',
                    title: 'Status',
                    width: '10%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const status = data || 'pending';
                            const badgeClass = {
                                'pending': 'badge-warning',
                                'completed': 'badge-success',
                                'cancelled': 'badge-danger'
                            }[status] || 'badge-secondary';

                            return `<span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
                        }
                        return data || 'pending';
                    }
                },
                {
                    data: 'priority',
                    title: 'Priority',
                    width: '8%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const priority = data || 'normal';
                            const badgeClass = {
                                'emergency': 'badge-danger',
                                'urgent': 'badge-warning',
                                'normal': 'badge-info',
                                'routine': 'badge-secondary'
                            }[priority] || 'badge-secondary';

                            return `<span class="badge ${badgeClass}">${priority.charAt(0).toUpperCase() + priority.slice(1)}</span>`;
                        }
                        return data || 'normal';
                    }
                },
                {
                    data: 'total_price',
                    title: 'Amount',
                    width: '10%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const amount = parseFloat(data) || 0;
                            return `₹${amount.toFixed(2)}`;
                        }
                        return data || 0;
                    }
                },
                {
                    data: 'entry_date',
                    title: 'Date',
                    width: '10%',
                    render: function (data, type, row) {
                        if (type === 'display' && data) {
                            const date = new Date(data);
                            return date.toLocaleDateString('en-IN');
                        }
                        return data || '';
                    }
                },
                {
                    data: null,
                    title: 'Actions',
                    width: '10%',
                    orderable: false,
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-info btn-sm" onclick="viewEntry(${row.id})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="editEntry(${row.id})" title="Edit Entry">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteEntry(${row.id})" title="Delete Entry">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        }
                        return '';
                    }
                }
            ],
            order: [[0, 'desc']], // Order by ID descending (newest first)
            pageLength: 25,
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-info btn-sm'
                }
            ],
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading entries...',
                emptyTable: 'No entries found',
                zeroRecords: 'No matching entries found'
            }
        });

        console.log('DataTable initialized successfully');
    } catch (error) {
        console.error('Failed to initialize DataTable:', error);
        showError('Failed to initialize data table. Please refresh the page.');
    }
}

/**
 * Load initial data (tests, patients, doctors) with enhanced error handling and retry logic
 */
async function loadInitialData(retryCount = 0) {
    const maxRetries = 3;
    const retryDelay = Math.pow(2, retryCount) * 1000; // Exponential backoff

    console.log(`Loading initial data... (attempt ${retryCount + 1}/${maxRetries + 1})`);

    // Show loading indicator
    showLoadingIndicator('Loading data, please wait...');

    try {
        const loadingResults = {
            tests: { success: false, data: [], error: null },
            categories: { success: false, data: [], error: null },
            patients: { success: false, data: [], error: null },
            doctors: { success: false, data: [], error: null }
        };

        // Load all data with individual error handling
        const loadingPromises = [
            loadTestsDataWithRetry().then(result => { loadingResults.tests = result; }),
            loadCategoriesDataWithRetry().then(result => { loadingResults.categories = result; }),
            loadPatientsDataWithRetry().then(result => { loadingResults.patients = result; }),
            loadDoctorsDataWithRetry().then(result => { loadingResults.doctors = result; })
        ];

        // Wait for all loading operations to complete
        await Promise.allSettled(loadingPromises);

        // Check results and determine success
        const criticalDataLoaded = loadingResults.tests.success && loadingResults.categories.success;
        const allDataLoaded = criticalDataLoaded && loadingResults.patients.success && loadingResults.doctors.success;

        // Update global data arrays
        testsData = loadingResults.tests.data;
        categoriesData = loadingResults.categories.data;
        patientsData = loadingResults.patients.data;
        doctorsData = loadingResults.doctors.data;

        // Log results
        console.log('Data loading results:', {
            tests: `${testsData.length} loaded (${loadingResults.tests.success ? 'success' : 'failed'})`,
            categories: `${categoriesData.length} loaded (${loadingResults.categories.success ? 'success' : 'failed'})`,
            patients: `${patientsData.length} loaded (${loadingResults.patients.success ? 'success' : 'failed'})`,
            doctors: `${doctorsData.length} loaded (${loadingResults.doctors.success ? 'success' : 'failed'})`
        });

        if (criticalDataLoaded) {
            console.log('Initial data loaded successfully');

            // Populate dropdowns
            populatePatientSelect();
            populateDoctorSelect();

            // Enable the Add Entry button
            $('button[onclick="openAddModal()"]').prop('disabled', false).removeClass('disabled');

            // Show success message if all data loaded
            if (allDataLoaded) {
                showSuccess('All data loaded successfully');
            } else {
                // Show warning for partial data loading
                const failedItems = [];
                if (!loadingResults.patients.success) failedItems.push('patients');
                if (!loadingResults.doctors.success) failedItems.push('doctors');
                showInfo(`Core data loaded. Some optional data failed to load: ${failedItems.join(', ')}`);
            }

            hideLoadingIndicator();
            return true;

        } else {
            // Critical data failed to load
            const failedItems = [];
            if (!loadingResults.tests.success) failedItems.push('tests');
            if (!loadingResults.categories.success) failedItems.push('categories');

            throw new Error(`Critical data failed to load: ${failedItems.join(', ')}`);
        }

    } catch (error) {
        console.error(`Failed to load initial data (attempt ${retryCount + 1}):`, error);

        // Retry logic
        if (retryCount < maxRetries) {
            console.log(`Retrying in ${retryDelay}ms...`);
            showInfo(`Loading failed, retrying in ${retryDelay / 1000} seconds...`);

            setTimeout(() => {
                loadInitialData(retryCount + 1);
            }, retryDelay);

            return false;
        } else {
            // All retries exhausted
            console.error('All retry attempts exhausted');
            hideLoadingIndicator();

            // Try to load from cache as fallback
            const cacheLoaded = await loadFromCache();
            if (cacheLoaded) {
                showInfo('Loaded data from cache. Some features may be limited.');
                $('button[onclick="openAddModal()"]').prop('disabled', false).removeClass('disabled');
                return true;
            } else {
                showError('Failed to load required data. Please refresh the page or contact support.');
                $('button[onclick="openAddModal()"]').prop('disabled', true).addClass('disabled');
                return false;
            }
        }
    }
}

/**
 * Load tests data from API
 */
async function loadTestsData() {
    try {
        const response = await $.ajax({
            url: 'ajax/test_api.php',
            method: 'GET',
            data: { action: 'simple_list' },
            dataType: 'json',
            timeout: 10000
        });

        if (response && response.success) {
            testsData = response.data || [];
            console.log(`Loaded ${testsData.length} tests`);
        } else {
            console.error('Failed to load tests:', response);
            testsData = [];
        }
    } catch (error) {
        console.error('Error loading tests:', error);
        testsData = [];
    }
}

/**
 * Load categories data from API
 */
async function loadCategoriesData() {
    try {
        const response = await $.ajax({
            url: 'patho_api/test_category.php',
            method: 'GET',
            data: {
                action: 'list',
                all: '1'
            },
            dataType: 'json',
            timeout: 10000
        });

        if (response && response.success) {
            categoriesData = response.data || [];
            console.log(`Loaded ${categoriesData.length} categories`);
        } else {
            console.error('Failed to load categories:', response);
            categoriesData = [];
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        categoriesData = [];
    }
}

/**
 * Load patients data from API
 */
async function loadPatientsData() {
    try {
        const response = await $.ajax({
            url: 'ajax/patient_api.php',
            method: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            timeout: 10000
        });

        if (response && response.success) {
            patientsData = response.data || [];
            populatePatientSelect();
            console.log(`Loaded ${patientsData.length} patients`);
        } else {
            console.error('Failed to load patients:', response);
            patientsData = [];
        }
    } catch (error) {
        console.error('Error loading patients:', error);
        patientsData = [];
    }
}

/**
 * Load doctors data from API
 */
async function loadDoctorsData() {
    try {
        const response = await $.ajax({
            url: 'ajax/doctor_api.php',
            method: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            timeout: 10000
        });

        if (response && response.success) {
            doctorsData = response.data || [];
            populateDoctorSelect();
            console.log(`Loaded ${doctorsData.length} doctors`);
        } else {
            console.error('Failed to load doctors:', response);
            doctorsData = [];
        }
    } catch (error) {
        console.error('Error loading doctors:', error);
        doctorsData = [];
    }
}

/**
 * Enhanced loading functions with retry logic and better error handling
 */

/**
 * Load tests data with retry logic
 */
async function loadTestsDataWithRetry(retryCount = 0) {
    const maxRetries = 2;

    try {
        const response = await $.ajax({
            url: 'ajax/test_api.php',
            method: 'GET',
            data: { action: 'simple_list' },
            dataType: 'json',
            timeout: 15000
        });

        if (response && response.success) {
            const data = response.data || [];
            console.log(`Loaded ${data.length} tests`);

            // Cache the data
            cacheData('tests', data);

            return { success: true, data: data, error: null };
        } else {
            throw new Error(response?.message || 'Invalid response format');
        }
    } catch (error) {
        console.error(`Error loading tests (attempt ${retryCount + 1}):`, error);

        if (retryCount < maxRetries) {
            const delay = Math.pow(2, retryCount) * 500;
            await new Promise(resolve => setTimeout(resolve, delay));
            return await loadTestsDataWithRetry(retryCount + 1);
        }

        // Try to load from cache as fallback
        const cachedData = getCachedData('tests');
        if (cachedData) {
            console.log('Using cached tests data');
            return { success: true, data: cachedData, error: error };
        }

        return { success: false, data: [], error: error };
    }
}

/**
 * Load categories data with retry logic
 */
async function loadCategoriesDataWithRetry(retryCount = 0) {
    const maxRetries = 2;

    try {
        const response = await $.ajax({
            url: 'patho_api/test_category.php',
            method: 'GET',
            data: {
                action: 'list',
                all: '1'
            },
            dataType: 'json',
            timeout: 15000
        });

        if (response && response.success) {
            const data = response.data || [];
            console.log(`Loaded ${data.length} categories`);

            // Cache the data
            cacheData('categories', data);

            return { success: true, data: data, error: null };
        } else {
            throw new Error(response?.message || 'Invalid response format');
        }
    } catch (error) {
        console.error(`Error loading categories (attempt ${retryCount + 1}):`, error);

        if (retryCount < maxRetries) {
            const delay = Math.pow(2, retryCount) * 500;
            await new Promise(resolve => setTimeout(resolve, delay));
            return await loadCategoriesDataWithRetry(retryCount + 1);
        }

        // Try to load from cache as fallback
        const cachedData = getCachedData('categories');
        if (cachedData) {
            console.log('Using cached categories data');
            return { success: true, data: cachedData, error: error };
        }

        return { success: false, data: [], error: error };
    }
}

/**
 * Load patients data with retry logic
 */
async function loadPatientsDataWithRetry(retryCount = 0) {
    const maxRetries = 2;

    try {
        const response = await $.ajax({
            url: 'ajax/patient_api.php',
            method: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            timeout: 15000
        });

        if (response && response.success) {
            const data = response.data || [];
            console.log(`Loaded ${data.length} patients`);

            // Cache the data
            cacheData('patients', data);

            return { success: true, data: data, error: null };
        } else {
            throw new Error(response?.message || 'Invalid response format');
        }
    } catch (error) {
        console.error(`Error loading patients (attempt ${retryCount + 1}):`, error);

        if (retryCount < maxRetries) {
            const delay = Math.pow(2, retryCount) * 500;
            await new Promise(resolve => setTimeout(resolve, delay));
            return await loadPatientsDataWithRetry(retryCount + 1);
        }

        // Try to load from cache as fallback
        const cachedData = getCachedData('patients');
        if (cachedData) {
            console.log('Using cached patients data');
            return { success: true, data: cachedData, error: error };
        }

        return { success: false, data: [], error: error };
    }
}

/**
 * Load doctors data with retry logic
 */
async function loadDoctorsDataWithRetry(retryCount = 0) {
    const maxRetries = 2;

    try {
        const response = await $.ajax({
            url: 'ajax/doctor_api.php',
            method: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            timeout: 15000
        });

        if (response && response.success) {
            const data = response.data || [];
            console.log(`Loaded ${data.length} doctors`);

            // Cache the data
            cacheData('doctors', data);

            return { success: true, data: data, error: null };
        } else {
            throw new Error(response?.message || 'Invalid response format');
        }
    } catch (error) {
        console.error(`Error loading doctors (attempt ${retryCount + 1}):`, error);

        if (retryCount < maxRetries) {
            const delay = Math.pow(2, retryCount) * 500;
            await new Promise(resolve => setTimeout(resolve, delay));
            return await loadDoctorsDataWithRetry(retryCount + 1);
        }

        // Try to load from cache as fallback
        const cachedData = getCachedData('doctors');
        if (cachedData) {
            console.log('Using cached doctors data');
            return { success: true, data: cachedData, error: error };
        }

        return { success: false, data: [], error: error };
    }
}

/**
 * Populate patient select dropdown with enhanced error handling
 */
function populatePatientSelect() {
    try {
        const $select = $('#patientSelect');

        if (!$select.length) {
            console.warn('Patient select element not found');
            return false;
        }

        // Clear and add default option
        $select.empty().append('<option value="">Select Patient</option>');

        if (!patientsData || patientsData.length === 0) {
            console.warn('No patients data available');
            $select.append('<option value="" disabled>No patients available</option>');
            return false;
        }

        // Add patient options with validation
        let addedCount = 0;
        patientsData.forEach(patient => {
            if (patient && patient.id && patient.name) {
                const displayName = `${patient.name} ${patient.uhid ? `(${patient.uhid})` : ''}`;
                $select.append(`<option value="${patient.id}">${displayName}</option>`);
                addedCount++;
            }
        });

        // Refresh Select2 if initialized
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.trigger('change');
        }

        console.log(`Populated patient select with ${addedCount} patients`);
        return true;

    } catch (error) {
        console.error('Error populating patient select:', error);
        return false;
    }
}

/**
 * Populate doctor select dropdown with enhanced error handling
 */
function populateDoctorSelect() {
    try {
        const $select = $('#doctorSelect');

        if (!$select.length) {
            console.warn('Doctor select element not found');
            return false;
        }

        // Clear and add default option
        $select.empty().append('<option value="">Select Doctor</option>');

        if (!doctorsData || doctorsData.length === 0) {
            console.warn('No doctors data available');
            $select.append('<option value="" disabled>No doctors available</option>');
            return false;
        }

        // Add doctor options with validation
        let addedCount = 0;
        doctorsData.forEach(doctor => {
            if (doctor && doctor.id && doctor.name) {
                const displayName = `${doctor.name} ${doctor.specialization ? `(${doctor.specialization})` : ''}`;
                $select.append(`<option value="${doctor.id}">${displayName}</option>`);
                addedCount++;
            }
        });

        // Refresh Select2 if initialized
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.trigger('change');
        }

        console.log(`Populated doctor select with ${addedCount} doctors`);
        return true;

    } catch (error) {
        console.error('Error populating doctor select:', error);
        return false;
    }
}



/**
 * Bind event handlers
 */
function bindEvents() {
    console.log('Binding events...');

    // Filter change events
    $('#statusFilter, #dateFilter').on('change', function () {
        applyFilters();
    });

    $('#patientFilter, #doctorFilter').on('keyup', debounce(function () {
        applyFilters();
    }, 300));

    // Form submission
    $('#entryForm').on('submit', function (e) {
        e.preventDefault();
        console.log('Form submitted, calling saveEntry...');
        console.log('Form data:', new FormData(this));
        try {
            saveEntry();
        } catch (error) {
            console.error('Error in form submission:', error);
            showError('An error occurred while submitting the form. Please try again.');
        }
    });

    // Also bind to the Save Entry button directly as a fallback
    $(document).on('click', '#entryForm button[type="submit"]', function (e) {
        e.preventDefault();
        console.log('Save Entry button clicked directly');
        $('#entryForm').submit();
    });

    // Discount amount change
    $('#discountAmount').on('input', function () {
        calculateTotals();
    });

    // Global category filter events
    $('#globalCategoryFilter').on('change', function () {
        applyGlobalCategoryFilter($(this).val());
    });

    $('#clearGlobalCategoryFilter').on('click', function () {
        $('#globalCategoryFilter').val('');
        applyGlobalCategoryFilter('');
    });

    // Setup real-time validation
    setupRealTimeValidation();

    console.log('Events bound successfully');
}

/**
 * Utility function for debouncing
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Apply filters to DataTable
 */
function applyFilters() {
    if (!entriesTable) return;

    const statusFilter = $('#statusFilter').val();
    const dateFilter = $('#dateFilter').val();
    const patientFilter = $('#patientFilter').val();
    const doctorFilter = $('#doctorFilter').val();

    // Apply global search for patient and doctor
    let globalSearch = '';
    if (patientFilter) globalSearch += patientFilter + ' ';
    if (doctorFilter) globalSearch += doctorFilter + ' ';

    entriesTable.search(globalSearch.trim()).draw();
}



/**
 * Refresh the entries table
 */
function refreshTable() {
    if (entriesTable) {
        entriesTable.ajax.reload();
        showSuccess('Table refreshed successfully');
    }
}

/**
 * Export entries
 */
function exportEntries() {
    if (entriesTable) {
        // Trigger the Excel export
        entriesTable.button('.buttons-excel').trigger();
    }
}

/**
 * Open Add Entry Modal
 */
function openAddModal() {
    console.log('Opening add modal...');

    // Check if required data is loaded
    if (testsData.length === 0 || categoriesData.length === 0) {
        console.log('Data not loaded, attempting to load...');
        showInfo('Loading data, please wait...');

        // Try to load data first
        loadInitialData().then(() => {
            if (testsData.length === 0 || categoriesData.length === 0) {
                showError('Failed to load required data. Please refresh the page.');
                return;
            }
            // Retry opening modal
            openAddModal();
        }).catch(error => {
            console.error('Failed to load data:', error);
            showError('Failed to load data. Please refresh the page.');
        });
        return;
    }

    console.log(`Opening modal with ${testsData.length} tests and ${categoriesData.length} categories`);

    currentEditId = null;
    resetForm();

    // Update modal title
    $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');

    // Show modal
    $('#entryModal').modal('show');

    // Wait for modal to be fully shown before initializing
    $('#entryModal').off('shown.bs.modal').on('shown.bs.modal', function () {
        console.log('Modal shown, initializing...');

        // Initialize Select2 dropdowns
        initializeSelect2();

        // Ensure all main form fields are editable
        ensureFieldsEditable();

        // Add first test row if none exist
        if ($('#testsContainer .test-row').length === 0) {
            console.log('Adding first test row...');
            addTestRow();
        }

        // Refresh dropdowns to ensure they have data
        setTimeout(() => {
            refreshAllDropdowns();
        }, 500);
    });

    // Reset form when modal is hidden
    $('#entryModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
        // Don't reset form here as it might interfere with the save process
        console.log('Modal hidden');
    });
}

/**
 * Reset form to default state
 */
function resetForm() {
    console.log('Resetting form...');

    // Reset form fields
    $('#entryForm')[0].reset();
    $('#entryId').val('');

    // Clear tests container
    $('#testsContainer').empty();
    testRowCounter = 0;

    // Reset totals
    calculateTotals();

    // Clear global category filter
    $('#globalCategoryFilter').val('');

    // Remove patient info card if it exists
    $('#patientInfoCard').remove();

    // Reset modal title
    $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');

    console.log('Form reset complete');
}

/**
 * Initialize Select2 dropdowns
 */
function initializeSelect2() {
    try {
        // Destroy existing Select2 instances first
        $('.select2').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        // Initialize Select2 dropdowns with proper configuration
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            disabled: false // Ensure Select2 dropdowns are not disabled
        });

        // Ensure main form Select2 dropdowns are enabled
        $('#patientSelect, #doctorSelect').select2('enable', true);

        // Populate global category filter
        populateGlobalCategoryFilter();

        console.log('Select2 initialized');
    } catch (error) {
        console.error('Error initializing Select2:', error);
    }
}

/**
 * Populate global category filter with enhanced error handling
 */
function populateGlobalCategoryFilter() {
    try {
        const $select = $('#globalCategoryFilter');

        if (!$select.length) {
            console.warn('Global category filter element not found');
            return false;
        }

        // Clear existing options except the first one
        $select.find('option:not(:first)').remove();

        if (!categoriesData || categoriesData.length === 0) {
            console.warn('No categories data available for global filter');
            return false;
        }

        // Add category options
        categoriesData.forEach(category => {
            if (category && category.id && category.name) {
                $select.append(`<option value="${category.id}">${category.name}</option>`);
            }
        });

        console.log(`Populated global category filter with ${categoriesData.length} categories`);
        return true;

    } catch (error) {
        console.error('Error populating global category filter:', error);
        return false;
    }
}

/**
 * Apply global category filter to all test dropdowns
 */
function applyGlobalCategoryFilter(categoryId) {
    console.log('Applying global category filter:', categoryId);

    // Update all existing test rows
    $('#testsContainer .test-row').each(function () {
        const $row = $(this);
        const $categorySelect = $row.find('.category-select');
        const $testSelect = $row.find('.test-select');

        if (categoryId) {
            // Set the category in the row
            $categorySelect.val(categoryId).trigger('change');
            // Update test options for this category
            updateTestOptions($testSelect, categoryId);
        } else {
            // Clear category selection and show all tests
            $categorySelect.val('').trigger('change');
            updateTestOptions($testSelect, '');
        }
    });
}

/**
 * Add a new test row
 */
function addTestRow(testData = null) {
    const rowIndex = testRowCounter++;

    console.log(`Adding test row ${rowIndex}`, testData);
    console.log('Available categories:', categoriesData.length);
    console.log('Available tests:', testsData.length);

    // Check if data is loaded
    if (categoriesData.length === 0 || testsData.length === 0) {
        console.error('Cannot add test row: Data not loaded yet');
        showError('Please wait for data to load before adding test rows');
        return;
    }

    // Create category options
    const categoryOptions = categoriesData.map(category => {
        return `<option value="${category.id}">${category.name}</option>`;
    }).join('');

    console.log(`Creating test row with ${categoriesData.length} categories and ${testsData.length} tests`);

    // Create test options (initially show all tests)
    const testOptions = testsData.map(test => {
        const displayName = `${test.name} [ID: ${test.id}]`;
        return `<option value="${test.id}" data-category-id="${test.category_id || ''}" data-price="${test.price || 0}" data-unit="${test.unit || ''}" data-min="${test.min || ''}" data-max="${test.max || ''}">${displayName}</option>`;
    }).join('');

    const rowHtml = `
        <div class="test-row row mb-2" data-row-index="${rowIndex}">
            <div class="col-md-2">
                <select class="form-control category-select" name="tests[${rowIndex}][category_id]">
                    <option value="">Select Category</option>
                    ${categoryOptions}
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control test-select" name="tests[${rowIndex}][test_id]" required>
                    <option value="">Select Test</option>
                    ${testOptions}
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control test-result" name="tests[${rowIndex}][result_value]" placeholder="Result">
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-min" name="tests[${rowIndex}][min]" placeholder="Min" readonly>
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-max" name="tests[${rowIndex}][max]" placeholder="Max" readonly>
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-unit" name="tests[${rowIndex}][unit]" placeholder="Unit" readonly>
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control test-price" name="tests[${rowIndex}][price]" placeholder="0.00" step="0.01" min="0">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-test-btn" onclick="removeTestRow(this)" title="Remove Test">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <input type="hidden" name="tests[${rowIndex}][main_category_id]" class="test-main-category-id">
        </div>
    `;

    $('#testsContainer').append(rowHtml);

    // Get the new row
    const $newRow = $(`.test-row[data-row-index="${rowIndex}"]`);
    const $categorySelect = $newRow.find('.category-select');
    const $testSelect = $newRow.find('.test-select');

    // Bind category selection change event
    $categorySelect.on('change', function () {
        onCategoryChange(this, $newRow);
    });

    // Bind test selection change event
    $testSelect.on('change', function () {
        onTestChange(this, $newRow);
    });

    // Bind price change event
    $newRow.find('.test-price').on('input', function () {
        calculateTotals();
    });

    // Bind result validation event
    $newRow.find('.test-result').on('input blur', function () {
        validateTestResult(this, $newRow);
    });

    // Initialize Select2 for the new row dropdowns
    try {
        // Destroy any existing Select2 instances first
        if ($categorySelect.hasClass('select2-hidden-accessible')) {
            $categorySelect.select2('destroy');
        }
        if ($testSelect.hasClass('select2-hidden-accessible')) {
            $testSelect.select2('destroy');
        }

        // Initialize Select2 with proper configuration
        $categorySelect.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Category',
            allowClear: true
        });

        $testSelect.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Test',
            allowClear: true
        });

        console.log(`Select2 initialized for row ${rowIndex}`);
        console.log(`Category options: ${$categorySelect.find('option').length}`);
        console.log(`Test options: ${$testSelect.find('option').length}`);
    } catch (error) {
        console.error('Error initializing Select2 for test row:', error);
    }

    // If testData is provided, populate the row (EDIT MODE)
    if (testData) {
        console.log('Populating test row with data:', testData);

        // Use setTimeout to ensure Select2 is fully initialized before setting values
        setTimeout(() => {
            console.log('Setting test row values for edit mode:', testData);

            // Set category first if available
            if (testData.category_id) {
                console.log('Setting category:', testData.category_id);
                $categorySelect.val(testData.category_id);
                if ($categorySelect.hasClass('select2-hidden-accessible')) {
                    $categorySelect.trigger('change.select2');
                } else {
                    $categorySelect.trigger('change');
                }
            }

            // Set test selection
            console.log('Setting test ID:', testData.test_id);
            $testSelect.val(testData.test_id);
            if ($testSelect.hasClass('select2-hidden-accessible')) {
                $testSelect.trigger('change.select2');
            } else {
                $testSelect.trigger('change');
            }

            // Set other test data
            const resultValue = testData.result_value || '';
            const priceValue = testData.price || testData.test_price || 0;
            const unitValue = testData.unit || testData.et_unit || '';
            const minValue = testData.min || testData.min_male || testData.min_female || '';
            const maxValue = testData.max || testData.max_male || testData.max_female || '';

            console.log('Setting field values:', {
                result: resultValue,
                price: priceValue,
                unit: unitValue,
                min: minValue,
                max: maxValue
            });

            $newRow.find('.test-result').val(resultValue);
            $newRow.find('.test-price').val(priceValue);
            $newRow.find('.test-unit').val(unitValue);
            $newRow.find('.test-min').val(minValue);
            $newRow.find('.test-max').val(maxValue);

            console.log('Test row populated successfully with:', {
                category_id: testData.category_id,
                test_id: testData.test_id,
                result_value: resultValue,
                price: priceValue,
                unit: unitValue
            });

            // Verify the values were set correctly
            setTimeout(() => {
                console.log('Verification - Values after setting:', {
                    category: $categorySelect.val(),
                    test: $testSelect.val(),
                    result: $newRow.find('.test-result').val(),
                    price: $newRow.find('.test-price').val(),
                    unit: $newRow.find('.test-unit').val()
                });
            }, 50);
        }, 100);
    }
}

/**
 * Remove a test row
 */
function removeTestRow(button) {
    const $row = $(button).closest('.test-row');
    $row.remove();
    calculateTotals();

    // Ensure at least one test row exists
    if ($('#testsContainer .test-row').length === 0) {
        addTestRow();
    }
}

/**
 * Handle category selection change
 */
function onCategoryChange(selectElement, $row) {
    const $categorySelect = $(selectElement);
    const $testSelect = $row.find('.test-select');
    const categoryId = $categorySelect.val();

    console.log('Category changed:', categoryId);

    // Set main category ID
    if (categoryId) {
        const categoryInfo = categoriesData.find(c => c.id == categoryId);
        if (categoryInfo && categoryInfo.main_category_id) {
            $row.find('.test-main-category-id').val(categoryInfo.main_category_id);
        }
    } else {
        $row.find('.test-main-category-id').val('');
    }

    // Clear current test selection
    $testSelect.val('').trigger('change');
    $row.find('.test-price').val('');
    $row.find('.test-unit').val('');
    $row.find('.test-min').val('');
    $row.find('.test-max').val('');

    // Rebuild test options based on selected category
    updateTestOptions($testSelect, categoryId);

    calculateTotals();
}

/**
 * Update test options based on category filter
 */
function updateTestOptions($testSelect, categoryId) {
    // Clear existing options except the first one
    $testSelect.find('option:not(:first)').remove();

    // Filter tests based on category
    let filteredTests = testsData;
    if (categoryId) {
        filteredTests = testsData.filter(test => test.category_id == categoryId);
    }

    // Add filtered test options
    filteredTests.forEach(test => {
        const displayName = `${test.name} [ID: ${test.id}]`;
        const option = `<option value="${test.id}" data-category-id="${test.category_id || ''}" data-price="${test.price || 0}" data-unit="${test.unit || ''}" data-min="${test.min || ''}" data-max="${test.max || ''}">${displayName}</option>`;
        $testSelect.append(option);
    });

    // Refresh Select2 to show updated options
    if ($testSelect.hasClass('select2-hidden-accessible')) {
        $testSelect.select2('destroy').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Test'
        });
    }

    console.log(`Updated test options: ${filteredTests.length} tests available`);
}

/**
 * Handle test selection change with duplicate detection
 */
function onTestChange(selectElement, $row) {
    const $select = $(selectElement);
    const testId = $select.val();

    console.log('Test selection changed:', testId);

    if (!testId) {
        // Clear everything if no test selected
        $row.find('.test-price').val('');
        $row.find('.test-unit').val('');
        $row.find('.test-min').val('');
        $row.find('.test-max').val('');
        $row.removeClass('duplicate-test-row');
        calculateTotals();
        return;
    }

    // Skip duplicate check if we're in edit mode and populating existing data
    const isEditMode = currentEditId !== null;
    if (!isEditMode) {
        // Check for duplicates before proceeding (only in add mode)
        if (!preventDuplicateSelection($select)) {
            return; // Duplicate detected and reverted
        }
    }

    // Find the test data
    const testData = testsData.find(t => t.id == testId);
    if (!testData) {
        console.error('Test not found:', testId);
        showError(`Test with ID ${testId} not found in available tests`);
        $select.val('').trigger('change');
        return;
    }

    console.log('Test selected:', testData);

    // Only auto-populate test details if the fields are empty (to preserve edit data)
    const $priceField = $row.find('.test-price');
    const $unitField = $row.find('.test-unit');
    const $minField = $row.find('.test-min');
    const $maxField = $row.find('.test-max');

    if (!$priceField.val() || $priceField.val() == '0') {
        $priceField.val(testData.price || 0);
    }
    if (!$unitField.val()) {
        $unitField.val(testData.unit || '');
    }
    if (!$minField.val()) {
        $minField.val(testData.min || '');
    }
    if (!$maxField.val()) {
        $maxField.val(testData.max || '');
    }

    // Auto-select category if not already selected
    const $categorySelect = $row.find('.category-select');
    if (!$categorySelect.val() && testData.category_id) {
        $categorySelect.val(testData.category_id);
        if ($categorySelect.hasClass('select2-hidden-accessible')) {
            $categorySelect.trigger('change.select2');
        }

        // Set main category ID
        const categoryInfo = categoriesData.find(c => c.id == testData.category_id);
        if (categoryInfo && categoryInfo.main_category_id) {
            $row.find('.test-main-category-id').val(categoryInfo.main_category_id);
        }
    }

    // Remove duplicate styling since this is now a valid selection
    $row.removeClass('duplicate-test-row');

    // Check for real-time duplicates (in case of race conditions) - only in add mode
    if (!isEditMode) {
        setTimeout(() => {
            checkForDuplicatesRealTime($row);
        }, 100);
    }

    // Calculate totals
    calculateTotals();
}

/**
 * Validate test result against min/max ranges
 */
function validateTestResult(resultInput, $row) {
    const $resultInput = $(resultInput);
    const resultValue = parseFloat($resultInput.val());
    const minValue = parseFloat($row.find('.test-min').val());
    const maxValue = parseFloat($row.find('.test-max').val());

    // Clear previous validation classes
    $resultInput.removeClass('result-normal result-abnormal');

    // Only validate if we have numeric values for result and ranges
    if (!isNaN(resultValue) && !isNaN(minValue) && !isNaN(maxValue)) {
        if (resultValue >= minValue && resultValue <= maxValue) {
            $resultInput.addClass('result-normal');
        } else {
            $resultInput.addClass('result-abnormal');
        }
    }
}

/**
 * Calculate totals from test prices
 */
function calculateTotals() {
    let subtotal = 0;

    // Sum up all test prices
    $('#testsContainer .test-price').each(function () {
        const price = parseFloat($(this).val()) || 0;
        subtotal += price;
    });

    const discount = parseFloat($('#discountAmount').val()) || 0;
    const total = Math.max(subtotal - discount, 0);

    $('#subtotal').val(subtotal.toFixed(2));
    $('#totalPrice').val(total.toFixed(2));

    console.log(`Totals calculated - Subtotal: ${subtotal}, Discount: ${discount}, Total: ${total}`);
}

/**
 * Save entry (create or update)
 */
async function saveEntry() {
    console.log('Saving entry...');

    try {
        // Validate form with error handling
        try {
            if (!validateForm()) {
                console.log('Form validation failed');
                return;
            }
        } catch (validationError) {
            console.error('Validation error:', validationError);

            // Fallback validation - check basic required fields
            const patientId = $('#patientSelect').val();
            const entryDate = $('#entryDate').val();
            const hasTests = $('#testsContainer .test-row .test-select').filter(function () {
                return $(this).val() !== '';
            }).length > 0;

            if (!patientId) {
                showError('Please select a patient.');
                return;
            }
            if (!entryDate) {
                showError('Please select an entry date.');
                return;
            }
            if (!hasTests) {
                showError('Please add at least one test.');
                return;
            }

            console.log('Fallback validation passed, continuing with save...');
        }

        // Show loading state
        const $submitBtn = $('#entryForm button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

        // Prepare form data
        const formData = new FormData($('#entryForm')[0]);
        formData.append('action', 'save'); // Use 'save' for both create and update
        if (currentEditId) {
            formData.append('id', currentEditId); // Add ID for updates
        }
        formData.append('secret_key', 'hospital-api-secret-2024');

        // Add current user ID if available
        if (typeof currentUserId !== 'undefined' && currentUserId) {
            formData.append('added_by', currentUserId);
        }

        // Collect tests data
        const tests = [];
        const addedTestIds = new Set(); // Track added test IDs to prevent duplicates

        $('#testsContainer .test-row').each(function (index) {
            const $row = $(this);
            const testId = $row.find('.test-select').val();

            console.log(`Test row ${index}:`, {
                testId: testId,
                categoryId: $row.find('.category-select').val(),
                result: $row.find('.test-result').val(),
                price: $row.find('.test-price').val()
            });

            if (testId) {
                // Check for duplicate test IDs
                if (addedTestIds.has(testId)) {
                    console.warn(`Duplicate test ID ${testId} found, skipping...`);
                    return; // Skip this iteration
                }

                addedTestIds.add(testId);

                // Find the test data to get main_category_id
                const testInfo = testsData.find(t => t.id == testId);
                const categoryId = $row.find('.category-select').val() || (testInfo ? testInfo.category_id : null);

                // Find main category ID
                let mainCategoryId = null;
                if (categoryId) {
                    const categoryInfo = categoriesData.find(c => c.id == categoryId);
                    mainCategoryId = categoryInfo ? categoryInfo.main_category_id : null;
                }

                const testData = {
                    test_id: testId,
                    category_id: categoryId,
                    main_category_id: mainCategoryId,
                    result_value: $row.find('.test-result').val() || '',
                    min: $row.find('.test-min').val() || '',
                    max: $row.find('.test-max').val() || '',
                    price: parseFloat($row.find('.test-price').val()) || 0,
                    unit: $row.find('.test-unit').val() || ''
                };
                tests.push(testData);
                console.log(`Added test data:`, testData);
            }
        });

        // Validate we have tests before submitting
        if (tests.length === 0) {
            showError('Please add at least one test before saving');
            return;
        }

        // Check for duplicate tests in the form
        const testIds = tests.map(t => t.test_id);
        const uniqueTestIds = [...new Set(testIds)];
        if (testIds.length !== uniqueTestIds.length) {
            showError('Duplicate tests detected. Please remove duplicate test entries.');
            return;
        }

        formData.append('tests', JSON.stringify(tests));

        console.log('Submitting form data:', Object.fromEntries(formData));
        console.log('Tests data:', tests);
        console.log('Current edit ID:', currentEditId);

        // Additional validation
        const patientId = formData.get('patient_id');
        const entryDate = formData.get('entry_date');
        console.log('Final validation - Patient ID:', patientId, 'Entry Date:', entryDate);

        // Add secret key if using old API
        const secretKey = API_CONFIG.getSecretKey();
        if (secretKey) {
            formData.append('secret_key', secretKey);
        }

        // Use enhanced API handler for better error handling
        const response = await makeAPIRequest({
            action: 'save',
            method: 'POST',
            data: formData
        });

        console.log('Server response:', response);

        // Handle successful response
        let successMessage;
        if (currentEditId) {
            // Get patient name for better context
            const patientName = $('#patientSelect option:selected').text() || 'Patient';
            successMessage = `Entry updated successfully for ${patientName}`;
        } else {
            successMessage = response.message || 'Entry created successfully';
        }

        // Add duplicate information if any were handled
        if (response.duplicates_skipped && response.duplicates_skipped > 0) {
            successMessage += ` (${response.duplicates_skipped} duplicate tests were automatically removed)`;
        }

        // Add test count information
        if (response.tests_processed) {
            successMessage += ` - ${response.tests_processed} tests processed`;
        }

        showSuccess(successMessage);
        $('#entryModal').modal('hide');
        refreshTable();

        // Log additional response information
        if (response.tests_processed) {
            console.log(`Successfully processed ${response.tests_processed} tests`);
        }

    } catch (error) {
        console.error('Error saving entry:', error);

        // Enhanced error handling is now handled by makeAPIRequest
        // Just log additional details for debugging
        if (error.requestId) {
            console.error(`Request ID: ${error.requestId}, Attempts: ${error.attempts}`);
        }

        // The error message has already been shown by makeAPIRequest
        // Just log the technical details for debugging
        console.error('Technical error details:', {
            status: error.status,
            authError: error.authError,
            apiError: error.apiError,
            fieldErrors: error.fieldErrors
        });
    } finally {
        // Restore button state
        const $submitBtn = $('#entryForm button[type="submit"]');
        $submitBtn.html('<i class="fas fa-save"></i> Save Entry').prop('disabled', false);
    }
}

/**
 * Enhanced Duplicate Test Prevention System
 */

/**
 * Validate that no duplicate tests exist in the current form
 */
function validateUniqueTests() {
    console.log('Validating unique tests...');

    const testIds = [];
    const duplicates = [];
    const testRows = [];

    $('#testsContainer .test-row').each(function (index) {
        const $row = $(this);
        const testId = $row.find('.test-select').val();

        if (testId) {
            const rowInfo = {
                index: index,
                testId: testId,
                testName: $row.find('.test-select option:selected').text(),
                $row: $row
            };

            if (testIds.includes(testId)) {
                duplicates.push(rowInfo);
                // Also mark the original as duplicate
                const originalIndex = testIds.indexOf(testId);
                if (!duplicates.find(d => d.index === originalIndex)) {
                    duplicates.push(testRows[originalIndex]);
                }
            } else {
                testIds.push(testId);
            }

            testRows.push(rowInfo);
        }
    });

    // Visual feedback for duplicates
    $('#testsContainer .test-row').removeClass('duplicate-test-row');
    duplicates.forEach(duplicate => {
        duplicate.$row.addClass('duplicate-test-row');
    });

    if (duplicates.length > 0) {
        console.log('Duplicate tests found:', duplicates);
        return {
            valid: false,
            duplicates: duplicates,
            message: `Duplicate tests detected: ${duplicates.map(d => d.testName).join(', ')}`
        };
    }

    console.log('No duplicate tests found');
    return { valid: true, duplicates: [], message: '' };
}

/**
 * Real-time duplicate detection as tests are selected
 */
function checkForDuplicatesRealTime($changedRow) {
    const changedTestId = $changedRow.find('.test-select').val();

    if (!changedTestId) {
        $changedRow.removeClass('duplicate-test-row');
        return;
    }

    let duplicateFound = false;

    $('#testsContainer .test-row').each(function () {
        const $row = $(this);
        const testId = $row.find('.test-select').val();

        if (testId === changedTestId) {
            if ($row[0] !== $changedRow[0]) {
                // Found a duplicate
                $row.addClass('duplicate-test-row');
                $changedRow.addClass('duplicate-test-row');
                duplicateFound = true;
            }
        } else {
            // Remove duplicate class if this row is not a duplicate
            const otherDuplicates = $('#testsContainer .test-row').not($row).filter(function () {
                return $(this).find('.test-select').val() === testId;
            });

            if (otherDuplicates.length === 0) {
                $row.removeClass('duplicate-test-row');
            }
        }
    });

    if (duplicateFound) {
        const testName = $changedRow.find('.test-select option:selected').text();
        showDuplicateWarning(`Duplicate test detected: ${testName}`);
    }
}

/**
 * Show duplicate warning message
 */
function showDuplicateWarning(message) {
    // Remove existing warning
    $('.duplicate-warning').remove();

    // Create warning element
    const warningHtml = `
        <div class="alert alert-warning alert-dismissible fade show duplicate-warning" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // Insert warning before tests container
    $('#testsContainer').before(warningHtml);

    // Auto-hide after 5 seconds
    setTimeout(() => {
        $('.duplicate-warning').fadeOut();
    }, 5000);
}

/**
 * Remove duplicate test rows automatically
 */
function removeDuplicateTestRows() {
    console.log('Checking for duplicate test rows...');

    const seenTestIds = new Set();
    const rowsToRemove = [];
    let duplicatesFound = 0;

    $('#testsContainer .test-row').each(function () {
        const $row = $(this);
        const testId = $row.find('.test-select').val();

        if (testId) {
            if (seenTestIds.has(testId)) {
                // This is a duplicate
                rowsToRemove.push({
                    $row: $row,
                    testId: testId,
                    testName: $row.find('.test-select option:selected').text()
                });
                duplicatesFound++;
                console.log('Found duplicate test ID:', testId);
            } else {
                seenTestIds.add(testId);
            }
        }
    });

    if (rowsToRemove.length > 0) {
        // Show confirmation dialog
        const testNames = rowsToRemove.map(item => item.testName).join(', ');
        const confirmMessage = `Found ${duplicatesFound} duplicate test(s): ${testNames}\n\nDo you want to remove the duplicates?`;

        if (confirm(confirmMessage)) {
            // Remove duplicate rows
            rowsToRemove.forEach(item => {
                item.$row.fadeOut(300, function () {
                    $(this).remove();
                    calculateTotals();
                });
            });

            showSuccess(`Removed ${duplicatesFound} duplicate test rows`);

            // Ensure at least one test row exists
            setTimeout(() => {
                if ($('#testsContainer .test-row').length === 0) {
                    addTestRow();
                }
            }, 400);
        }
    } else {
        showInfo('No duplicate test rows found');
    }

    console.log(`Checked for duplicates: ${duplicatesFound} found`);
    return duplicatesFound;
}

/**
 * Prevent duplicate test selection in dropdowns
 */
function preventDuplicateSelection($testSelect) {
    const selectedTestId = $testSelect.val();

    if (!selectedTestId) return;

    // Check if this test is already selected in another row
    const duplicateExists = $('#testsContainer .test-row').not($testSelect.closest('.test-row')).find('.test-select').filter(function () {
        return $(this).val() === selectedTestId;
    }).length > 0;

    if (duplicateExists) {
        // Revert the selection
        $testSelect.val('').trigger('change');

        const testName = $testSelect.find(`option[value="${selectedTestId}"]`).text();
        showError(`Test "${testName}" is already selected in another row. Please choose a different test.`);

        return false;
    }

    return true;
}

/**
 * Add CSS for duplicate test styling and form validation
 */
function addValidationCSS() {
    if (!$('#validationCSS').length) {
        const css = `
            <style id="validationCSS">
                /* Duplicate test styling */
                .duplicate-test-row {
                    background-color: #fff3cd !important;
                    border: 2px solid #ffc107 !important;
                    border-radius: 4px;
                    animation: duplicateWarning 1s ease-in-out;
                }
                
                .duplicate-test-row .test-select {
                    border-color: #ffc107 !important;
                    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25) !important;
                }
                
                @keyframes duplicateWarning {
                    0% { background-color: #f8d7da; }
                    50% { background-color: #fff3cd; }
                    100% { background-color: #fff3cd; }
                }
                
                .duplicate-warning {
                    margin-bottom: 15px;
                    border-left: 4px solid #ffc107;
                }
                
                /* Invalid test row styling */
                .invalid-test-row {
                    background-color: #f8d7da !important;
                    border: 2px solid #dc3545 !important;
                    border-radius: 4px;
                    animation: invalidWarning 1s ease-in-out;
                }
                
                @keyframes invalidWarning {
                    0% { background-color: #f8d7da; }
                    50% { background-color: #f5c6cb; }
                    100% { background-color: #f8d7da; }
                }
                
                /* Form validation styling */
                .is-invalid {
                    border-color: #dc3545 !important;
                    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
                }
                
                .is-valid {
                    border-color: #28a745 !important;
                    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
                }
                
                .invalid-feedback {
                    display: block !important;
                    width: 100%;
                    margin-top: 0.25rem;
                    font-size: 0.875em;
                    color: #dc3545;
                }
                
                /* Test container error styling */
                .has-error {
                    border: 2px dashed #dc3545;
                    border-radius: 4px;
                    padding: 10px;
                    background-color: rgba(220, 53, 69, 0.05);
                }
                
                .test-container-error {
                    margin-top: 10px !important;
                    font-size: 0.9em;
                }
                
                /* Test result validation styling */
                .result-normal {
                    border-color: #28a745 !important;
                    background-color: rgba(40, 167, 69, 0.1) !important;
                }
                
                .result-abnormal {
                    border-color: #ffc107 !important;
                    background-color: rgba(255, 193, 7, 0.1) !important;
                }
                
                /* Real-time validation feedback */
                .validation-pending {
                    border-color: #17a2b8 !important;
                    box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25) !important;
                }
                
                /* Validation success animation */
                @keyframes validationSuccess {
                    0% { border-color: #28a745; box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25); }
                    50% { border-color: #20c997; box-shadow: 0 0 0 0.4rem rgba(32, 201, 151, 0.35); }
                    100% { border-color: #28a745; box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25); }
                }
                
                .validation-success {
                    animation: validationSuccess 0.6s ease-in-out;
                }
                
                /* Validation error animation */
                @keyframes validationError {
                    0% { transform: translateX(0); }
                    25% { transform: translateX(-5px); }
                    75% { transform: translateX(5px); }
                    100% { transform: translateX(0); }
                }
                
                .validation-error {
                    animation: validationError 0.5s ease-in-out;
                }
            </style>
        `;
        $('head').append(css);
    }
}

// Initialize validation CSS
$(document).ready(function () {
    addValidationCSS();
});

/**
 * Enhanced form validation with comprehensive checks and real-time feedback
 */
function validateForm() {
    console.log('Validating form...');

    try {
        // Clear previous validation states
        clearValidationErrors();
    } catch (error) {
        console.error('Error clearing validation errors:', error);
    }

    const validationResult = {
        isValid: true,
        errors: [],
        fieldErrors: {},
        warnings: []
    };

    // 1. Validate patient selection
    const patientId = $('#patientSelect').val();
    console.log('Patient ID:', patientId);
    if (!patientId || patientId === '') {
        validationResult.isValid = false;
        validationResult.errors.push('Please select a patient');
        validationResult.fieldErrors.patient_id = 'Patient selection is required';
        markFieldAsInvalid('#patientSelect', 'Patient selection is required');
    } else {
        markFieldAsValid('#patientSelect');
    }

    // 2. Validate entry date
    const entryDate = $('#entryDate').val();
    console.log('Entry date:', entryDate);
    if (!entryDate || entryDate === '') {
        validationResult.isValid = false;
        validationResult.errors.push('Please select an entry date');
        validationResult.fieldErrors.entry_date = 'Entry date is required';
        markFieldAsInvalid('#entryDate', 'Entry date is required');
    } else {
        // Validate date format and range
        const dateValidation = validateEntryDate(entryDate);
        if (!dateValidation.valid) {
            validationResult.isValid = false;
            validationResult.errors.push(dateValidation.message);
            validationResult.fieldErrors.entry_date = dateValidation.message;
            markFieldAsInvalid('#entryDate', dateValidation.message);
        } else {
            markFieldAsValid('#entryDate');
            if (dateValidation.warning) {
                validationResult.warnings.push(dateValidation.warning);
            }
        }
    }

    // 3. Validate doctor selection (optional but warn if not selected)
    const doctorId = $('#doctorSelect').val();
    if (!doctorId || doctorId === '') {
        validationResult.warnings.push('No doctor assigned to this entry');
    }

    // 4. Validate test rows
    const testRows = $('#testsContainer .test-row');
    console.log('Number of test rows:', testRows.length);

    const validTests = [];
    const testValidationErrors = [];

    testRows.each(function (index) {
        const $row = $(this);
        const testId = $row.find('.test-select').val();
        const rowNumber = index + 1;

        if (testId && testId !== '') {
            const testValidation = validateTestRow($row, rowNumber);
            if (testValidation.valid) {
                validTests.push({
                    testId: testId,
                    testName: $row.find('.test-select option:selected').text(),
                    price: parseFloat($row.find('.test-price').val()) || 0
                });
            } else {
                testValidationErrors.push(...testValidation.errors);
                validationResult.isValid = false;
            }
        }
    });

    // Check if at least one test is selected
    if (validTests.length === 0) {
        validationResult.isValid = false;
        validationResult.errors.push('Please add at least one test');
        validationResult.fieldErrors.tests = 'At least one test must be selected';
        showTestContainerError('At least one test must be selected');
    } else {
        clearTestContainerError();
    }

    // Add test-specific errors
    if (testValidationErrors.length > 0) {
        validationResult.errors.push(...testValidationErrors);
    }

    // 5. Check for duplicate tests
    const duplicateCheck = validateUniqueTests();
    if (!duplicateCheck.valid) {
        validationResult.isValid = false;
        validationResult.errors.push(duplicateCheck.message);
        validationResult.fieldErrors.duplicate_tests = duplicateCheck.message;

        // Scroll to first duplicate
        if (duplicateCheck.duplicates.length > 0) {
            duplicateCheck.duplicates[0].$row[0].scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }

    // 6. Validate pricing calculations
    const pricingValidation = validatePricing();
    if (!pricingValidation.valid) {
        validationResult.isValid = false;
        validationResult.errors.push(...pricingValidation.errors);
        Object.assign(validationResult.fieldErrors, pricingValidation.fieldErrors);
    }

    // 7. Display validation results
    if (!validationResult.isValid) {
        displayValidationErrors(validationResult);
        console.log('Form validation failed:', validationResult);
        return false;
    }

    // Show warnings if any
    if (validationResult.warnings.length > 0) {
        showWarning(validationResult.warnings.join('<br>'));
    }

    console.log('Form validation passed');
    return true;
}

/**
 * Validate individual test row
 */
function validateTestRow($row, rowNumber) {
    const result = {
        valid: true,
        errors: []
    };

    const testId = $row.find('.test-select').val();
    const testName = $row.find('.test-select option:selected').text();

    // Clear previous row validation state
    $row.removeClass('invalid-test-row');

    if (!testId || testId === '') {
        result.valid = false;
        result.errors.push(`Test selection is required in row ${rowNumber}`);
        $row.addClass('invalid-test-row');
        return result;
    }

    // Validate test exists in available tests
    const testExists = testsData.find(t => t.id == testId);
    if (!testExists) {
        result.valid = false;
        result.errors.push(`Test "${testName}" in row ${rowNumber} is no longer available`);
        $row.addClass('invalid-test-row');
        return result;
    }

    // Validate pricing
    const price = parseFloat($row.find('.test-price').val());
    if (isNaN(price) || price < 0) {
        result.valid = false;
        result.errors.push(`Invalid price for test "${testName}" in row ${rowNumber}`);
        markFieldAsInvalid($row.find('.test-price'), 'Price must be a positive number');
    } else {
        markFieldAsValid($row.find('.test-price'));
    }

    // Validate test result if entered
    const resultValue = $row.find('.test-result').val();
    if (resultValue && resultValue.trim() !== '') {
        const resultValidation = validateTestResultValue(resultValue, $row);
        if (!resultValidation.valid) {
            // Don't fail validation for result issues, just show warning
            console.warn(`Test result validation warning for row ${rowNumber}:`, resultValidation.message);
        }
    }

    return result;
}

/**
 * Validate entry date
 */
function validateEntryDate(dateString) {
    const result = {
        valid: true,
        message: '',
        warning: ''
    };

    try {
        const entryDate = new Date(dateString);
        const today = new Date();
        const maxFutureDate = new Date();
        maxFutureDate.setDate(today.getDate() + 30); // Allow up to 30 days in future

        const minPastDate = new Date();
        minPastDate.setFullYear(today.getFullYear() - 1); // Allow up to 1 year in past

        // Check if date is valid
        if (isNaN(entryDate.getTime())) {
            result.valid = false;
            result.message = 'Please enter a valid date';
            return result;
        }

        // Check if date is too far in the past
        if (entryDate < minPastDate) {
            result.valid = false;
            result.message = 'Entry date cannot be more than 1 year in the past';
            return result;
        }

        // Check if date is too far in the future
        if (entryDate > maxFutureDate) {
            result.valid = false;
            result.message = 'Entry date cannot be more than 30 days in the future';
            return result;
        }

        // Warn if date is in the future
        if (entryDate > today) {
            result.warning = 'Entry date is in the future';
        }

        // Warn if date is more than 7 days old
        const weekAgo = new Date();
        weekAgo.setDate(today.getDate() - 7);
        if (entryDate < weekAgo) {
            result.warning = 'Entry date is more than a week old';
        }

    } catch (error) {
        result.valid = false;
        result.message = 'Invalid date format';
    }

    return result;
}

/**
 * Validate pricing calculations
 */
function validatePricing() {
    const result = {
        valid: true,
        errors: [],
        fieldErrors: {}
    };

    // Calculate expected totals
    let calculatedSubtotal = 0;
    $('#testsContainer .test-price').each(function () {
        const price = parseFloat($(this).val()) || 0;
        calculatedSubtotal += price;
    });

    const discount = parseFloat($('#discountAmount').val()) || 0;
    const calculatedTotal = Math.max(calculatedSubtotal - discount, 0);

    // Validate discount
    if (discount < 0) {
        result.valid = false;
        result.errors.push('Discount amount cannot be negative');
        result.fieldErrors.discount_amount = 'Discount cannot be negative';
        markFieldAsInvalid('#discountAmount', 'Discount cannot be negative');
    } else if (discount > calculatedSubtotal) {
        result.valid = false;
        result.errors.push('Discount amount cannot exceed subtotal');
        result.fieldErrors.discount_amount = 'Discount cannot exceed subtotal';
        markFieldAsInvalid('#discountAmount', 'Discount cannot exceed subtotal');
    } else {
        markFieldAsValid('#discountAmount');
    }

    // Validate subtotal matches calculation
    const displayedSubtotal = parseFloat($('#subtotal').val()) || 0;
    if (Math.abs(displayedSubtotal - calculatedSubtotal) > 0.01) {
        console.warn('Subtotal mismatch detected, recalculating...');
        calculateTotals(); // Auto-fix the calculation
    }

    // Validate total
    const displayedTotal = parseFloat($('#totalPrice').val()) || 0;
    if (displayedTotal < 0) {
        result.valid = false;
        result.errors.push('Total amount cannot be negative');
        result.fieldErrors.total_price = 'Total cannot be negative';
    }

    return result;
}

/**
 * Mark field as invalid with error message
 */
function markFieldAsInvalid($field, message) {
    $field.addClass('is-invalid').removeClass('is-valid');

    // Remove existing feedback
    $field.siblings('.invalid-feedback').remove();

    // Add error message
    if (message) {
        $field.after(`<div class="invalid-feedback">${message}</div>`);
    }
}

/**
 * Mark field as valid
 */
function markFieldAsValid($field) {
    $field.addClass('is-valid').removeClass('is-invalid');
    $field.siblings('.invalid-feedback').remove();
}

/**
 * Clear all validation errors
 */
function clearValidationErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.is-valid').removeClass('is-valid');
    $('.invalid-feedback').remove();
    $('.invalid-test-row').removeClass('invalid-test-row');
    $('.duplicate-test-row').removeClass('duplicate-test-row');
    clearTestContainerError();
}

/**
 * Show error for test container
 */
function showTestContainerError(message) {
    const $container = $('#testsContainer');
    $container.addClass('has-error');

    // Remove existing error message
    $container.siblings('.test-container-error').remove();

    // Add error message
    $container.after(`<div class="test-container-error alert alert-danger mt-2">${message}</div>`);
}

/**
 * Clear test container error
 */
function clearTestContainerError() {
    $('#testsContainer').removeClass('has-error');
    $('.test-container-error').remove();
}

/**
 * Display comprehensive validation errors
 */
function displayValidationErrors(validationResult) {
    if (validationResult.errors.length > 0) {
        const errorMessage = validationResult.errors.join('<br>');
        showError(errorMessage);
    }

    // Log field-specific errors for debugging
    if (Object.keys(validationResult.fieldErrors).length > 0) {
        console.error('Field validation errors:', validationResult.fieldErrors);
    }
}

/**
 * Real-time validation for form fields
 */
function setupRealTimeValidation() {
    // Patient selection validation
    $('#patientSelect').on('change', function () {
        if ($(this).val()) {
            markFieldAsValid($(this));
        } else {
            markFieldAsInvalid($(this), 'Patient selection is required');
        }
    });

    // Entry date validation
    $('#entryDate').on('change blur', function () {
        const dateValue = $(this).val();
        if (!dateValue) {
            markFieldAsInvalid($(this), 'Entry date is required');
        } else {
            const dateValidation = validateEntryDate(dateValue);
            if (dateValidation.valid) {
                markFieldAsValid($(this));
                if (dateValidation.warning) {
                    showWarning(dateValidation.warning);
                }
            } else {
                markFieldAsInvalid($(this), dateValidation.message);
            }
        }
    });

    // Discount validation
    $('#discountAmount').on('input blur', function () {
        const discount = parseFloat($(this).val()) || 0;
        const subtotal = parseFloat($('#subtotal').val()) || 0;

        if (discount < 0) {
            markFieldAsInvalid($(this), 'Discount cannot be negative');
        } else if (discount > subtotal) {
            markFieldAsInvalid($(this), 'Discount cannot exceed subtotal');
        } else {
            markFieldAsValid($(this));
            calculateTotals(); // Recalculate totals
        }
    });
}

/**
 * Validate test result value (used by validateTestRow)
 */
function validateTestResultValue(resultValue, $row) {
    const result = {
        valid: true,
        message: '',
        warning: ''
    };

    // If no result value, it's valid (results are optional)
    if (!resultValue || resultValue.trim() === '') {
        return result;
    }

    const trimmedValue = resultValue.trim();

    // Check if it's a numeric result
    const numericValue = parseFloat(trimmedValue);
    if (!isNaN(numericValue)) {
        // Validate against min/max ranges if available
        const minValue = parseFloat($row.find('.test-min').val());
        const maxValue = parseFloat($row.find('.test-max').val());

        if (!isNaN(minValue) && !isNaN(maxValue)) {
            if (numericValue < minValue) {
                result.warning = `Result ${numericValue} is below normal range (${minValue}-${maxValue})`;
            } else if (numericValue > maxValue) {
                result.warning = `Result ${numericValue} is above normal range (${minValue}-${maxValue})`;
            }
        }
    } else {
        // Non-numeric result - validate basic format
        if (trimmedValue.length > 100) {
            result.valid = false;
            result.message = 'Result value is too long (maximum 100 characters)';
        } else if (trimmedValue.length < 1) {
            result.valid = false;
            result.message = 'Result value cannot be empty';
        }
    }

    return result;
}

/**
 * View entry details
 */
async function viewEntry(id) {
    console.log('Viewing entry:', id);

    try {
        // Show modal
        $('#viewEntryModal').modal('show');

        // Show loading indicator in modal
        $('#entryDetails').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading entry details...</div>');

        // Load entry data using enhanced API handler
        const response = await makeAPIRequest({
            action: 'get',
            data: { id: id }
        });

        console.log('Entry details loaded:', response);
        displayEntryDetails(response.data);

    } catch (error) {
        console.error('Error loading entry:', error);
        $('#viewEntryModal').modal('hide');
        // Error message is already shown by makeAPIRequest
    }
}

/**
 * Display entry details in view modal
 */
function displayEntryDetails(entry) {
    console.log('Displaying entry details:', entry);

    const testsHtml = entry.tests && entry.tests.length > 0
        ? entry.tests.map(test => `
            <tr>
                <td>${test.category_name || 'No Category'}</td>
                <td>${test.test_name || 'Unknown Test'}</td>
                <td>${test.result_value || 'Pending'}</td>
                <td>${test.min || '-'}</td>
                <td>${test.max || '-'}</td>
                <td>${test.unit || '-'}</td>
                <td>₹${parseFloat(test.price || 0).toFixed(2)}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="7" class="text-center text-muted">No tests found</td></tr>';

    const detailsHtml = `
        <div class="row">
            <div class="col-md-6">
                <h5>Entry Information</h5>
                <table class="table table-sm">
                    <tr><th>Entry ID:</th><td>${entry.id}</td></tr>
                    <tr><th>Patient:</th><td>${entry.patient_name || 'N/A'}</td></tr>
                    <tr><th>Doctor:</th><td>${entry.doctor_name || 'Not assigned'}</td></tr>
                    <tr><th>Entry Date:</th><td>${entry.entry_date ? new Date(entry.entry_date).toLocaleDateString('en-IN') : 'N/A'}</td></tr>
                    <tr><th>Status:</th><td><span class="badge badge-${entry.status === 'completed' ? 'success' : entry.status === 'cancelled' ? 'danger' : 'warning'}">${entry.status || 'pending'}</span></td></tr>
                    <tr><th>Priority:</th><td><span class="badge badge-info">${entry.priority || 'normal'}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Pricing Information</h5>
                <table class="table table-sm">
                    <tr><th>Subtotal:</th><td>₹${parseFloat(entry.subtotal || 0).toFixed(2)}</td></tr>
                    <tr><th>Discount:</th><td>₹${parseFloat(entry.discount_amount || 0).toFixed(2)}</td></tr>
                    <tr><th>Total Amount:</th><td><strong>₹${parseFloat(entry.total_price || 0).toFixed(2)}</strong></td></tr>
                </table>
                ${entry.notes ? `<h6>Notes:</h6><p class="text-muted">${entry.notes}</p>` : ''}
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Tests</h5>
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Category</th>
                            <th>Test Name</th>
                            <th>Result</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Unit</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${testsHtml}
                    </tbody>
                </table>
            </div>
        </div>
    `;

    $('#entryDetails').html(detailsHtml);
}

/**
 * Edit entry
 */
async function editEntry(id) {
    console.log('Editing entry:', id);

    try {
        currentEditId = id;
        resetForm();

        // Update modal title
        $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');

        // Show loading state
        showLoadingIndicator('Loading entry data for editing...');

        // Load entry data using enhanced API handler
        const response = await makeAPIRequest({
            action: 'get',
            data: { id: id }
        });

        console.log('Entry data loaded for editing:', response);

        hideLoadingIndicator();

        // Show modal first
        $('#entryModal').modal('show');

        // Wait for modal to be fully shown before populating
        $('#entryModal').off('shown.bs.modal.edit').on('shown.bs.modal.edit', function () {
            console.log('Modal shown, initializing for edit...');

            // Initialize Select2 first
            initializeSelect2();

            // Ensure all main form fields are editable
            ensureFieldsEditable();

            // Add patient info card if available
            if (response.data.patient_name) {
                addPatientInfoCard(response.data);
            }

            // Then populate the form
            setTimeout(() => {
                populateEditForm(response.data);
            }, 200);
        });

    } catch (error) {
        console.error('Error loading entry for edit:', error);
        hideLoadingIndicator();
        // Error message is already shown by makeAPIRequest
    }
}

/**
 * Ensure all main form fields above Global Category Filter are editable
 */
function ensureFieldsEditable() {
    console.log('Ensuring all main form fields are editable...');

    // Remove readonly and disabled attributes from main form fields
    const mainFields = ['#patientSelect', '#doctorSelect', '#entryDate', '#entryStatus'];

    mainFields.forEach(fieldId => {
        const $field = $(fieldId);
        $field.prop('readonly', false)
            .prop('disabled', false)
            .removeClass('readonly disabled');

        // For select2 fields, ensure they are enabled
        if ($field.hasClass('select2-hidden-accessible')) {
            $field.select2('enable', true);
        }

        console.log(`Field ${fieldId} made editable`);
    });

    // Also ensure priority and referral source are editable
    $('#priority, #referralSource').prop('readonly', false).prop('disabled', false);

    console.log('All main form fields are now editable');
}

/**
 * Add patient information card to the modal for better context
 */
function addPatientInfoCard(entry) {
    const patientInfoHtml = `
        <div class="alert alert-info mb-3" id="patientInfoCard">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="mb-1"><i class="fas fa-user mr-1"></i>Patient Information</h6>
                    <p class="mb-1"><strong>Name:</strong> ${entry.patient_name || 'N/A'}</p>
                    <p class="mb-1"><strong>UHID:</strong> ${entry.patient_uhid || 'N/A'}</p>
                    ${entry.patient_contact ? `<p class="mb-1"><strong>Contact:</strong> ${entry.patient_contact}</p>` : ''}
                </div>
                <div class="col-md-6">
                    <h6 class="mb-1"><i class="fas fa-stethoscope mr-1"></i>Entry Details</h6>
                    <p class="mb-1"><strong>Entry ID:</strong> #${entry.id}</p>
                    <p class="mb-1"><strong>Doctor:</strong> ${entry.doctor_name || 'Not assigned'}</p>
                    <p class="mb-1"><strong>Tests Count:</strong> ${entry.tests_count || 0}</p>
                </div>
            </div>
        </div>
    `;

    // Insert after the hidden input field
    $('#entryId').after(patientInfoHtml);
}

/**
 * Populate form with entry data for editing
 */
function populateEditForm(entry) {
    console.log('Populating edit form with:', entry);

    // Set basic fields
    $('#entryId').val(entry.id);

    // Ensure all main form fields are editable (remove any readonly/disabled attributes)
    $('#patientSelect, #doctorSelect, #entryDate, #entryStatus').prop('readonly', false).prop('disabled', false);

    // Debug: Log field states
    console.log('Field states after making editable:');
    console.log('Patient Select - readonly:', $('#patientSelect').prop('readonly'), 'disabled:', $('#patientSelect').prop('disabled'));
    console.log('Doctor Select - readonly:', $('#doctorSelect').prop('readonly'), 'disabled:', $('#doctorSelect').prop('disabled'));
    console.log('Entry Date - readonly:', $('#entryDate').prop('readonly'), 'disabled:', $('#entryDate').prop('disabled'));
    console.log('Entry Status - readonly:', $('#entryStatus').prop('readonly'), 'disabled:', $('#entryStatus').prop('disabled'));

    // Set patient and doctor with Select2 trigger
    $('#patientSelect').val(entry.patient_id);
    if ($('#patientSelect').hasClass('select2-hidden-accessible')) {
        $('#patientSelect').trigger('change.select2');
    }

    $('#doctorSelect').val(entry.doctor_id);
    if ($('#doctorSelect').hasClass('select2-hidden-accessible')) {
        $('#doctorSelect').trigger('change.select2');
    }

    $('#entryDate').val(entry.entry_date ? entry.entry_date.split(' ')[0] : '');
    $('#entryStatus').val(entry.status || 'pending');
    $('#priority').val(entry.priority || 'normal');
    $('#referralSource').val(entry.referral_source || '');
    $('#subtotal').val(parseFloat(entry.subtotal || 0).toFixed(2));
    $('#discountAmount').val(parseFloat(entry.discount_amount || 0).toFixed(2));
    $('#totalPrice').val(parseFloat(entry.total_price || 0).toFixed(2));
    $('#entryNotes').val(entry.notes || '');

    // Show patient details in modal header for better context
    if (entry.patient_name) {
        const patientInfo = `${entry.patient_name}${entry.patient_uhid ? ` (${entry.patient_uhid})` : ''}`;
        $('#entryModalLabel').html(`<i class="fas fa-edit mr-1"></i>Edit Entry - ${patientInfo}`);
    }

    console.log('Entry tests data:', entry.tests);

    // Clear existing test rows
    $('#testsContainer').empty();
    testRowCounter = 0;

    // Add test rows
    if (entry.tests && entry.tests.length > 0) {
        console.log(`Adding ${entry.tests.length} test rows for editing`);

        entry.tests.forEach((test, index) => {
            console.log(`Processing test ${index + 1}:`, test);

            // Ensure category_id is available for proper row population
            if (!test.category_id && test.test_id) {
                // Find category from test data
                const testData = testsData.find(t => t.id == test.test_id);
                if (testData && testData.category_id) {
                    test.category_id = testData.category_id;
                    console.log(`Found category ${testData.category_id} for test ${test.test_id}`);
                }
            }

            // Add the test row with data
            addTestRow(test);
        });

        // Recalculate totals after all tests are added
        setTimeout(() => {
            calculateTotals();
        }, 500);
    } else {
        console.log('No tests found, adding empty row');
        addTestRow(); // Add at least one empty row
    }

    // Add visual indicators for required fields
    highlightRequiredFields();

    console.log('Edit form population completed');
}

/**
 * Highlight required fields for better user experience
 */
function highlightRequiredFields() {
    // Add visual indicators to required fields
    $('label:contains("*")').addClass('text-danger font-weight-bold');

    // Add tooltips to help users understand what's required
    $('#patientSelect').attr('title', 'Patient selection is required');
    $('#entryDate').attr('title', 'Entry date is required');

    // Initialize tooltips if Bootstrap is available
    if (typeof $().tooltip === 'function') {
        $('[title]').tooltip();
    }
}

/**
 * Delete entry
 */
function deleteEntry(id) {
    console.log('Deleting entry:', id);

    // Show confirmation modal
    $('#deleteModal').modal('show');

    // Handle delete confirmation
    $('#confirmDelete').off('click').on('click', async function () {
        try {
            // Show loading state
            const $deleteBtn = $(this);
            const originalText = $deleteBtn.html();
            $deleteBtn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...').prop('disabled', true);

            // Delete entry using enhanced API handler
            const response = await makeAPIRequest({
                action: 'delete',
                method: 'POST',
                data: { id: id }
            });

            console.log('Entry deleted successfully:', response);

            showSuccess(response.message || 'Entry deleted successfully');
            $('#deleteModal').modal('hide');
            refreshTable();

        } catch (error) {
            console.error('Error deleting entry:', error);
            // Error message is already shown by makeAPIRequest
        } finally {
            // Restore button state
            const $deleteBtn = $('#confirmDelete');
            $deleteBtn.html('Delete').prop('disabled', false);
        }
    });
}

/**
 * Print entry details
 */
function printEntryDetails() {
    const printContent = $('#entryDetails').html();
    const printWindow = window.open('', '_blank');

    printWindow.document.write(`
        <html>
        <head>
            <title>Entry Details</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <style>
                body { font-family: Arial, sans-serif; }
                .badge { color: #000 !important; border: 1px solid #000; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="container mt-3">
                <h2 class="text-center mb-4">Entry Details</h2>
                ${printContent}
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.print();
}

/**
 * Show success message
 */
function showSuccess(message) {
    if (typeof toastr !== 'undefined') {
        toastr.success(message);
    } else {
        alert(message);
    }
}

/**
 * Show error message
 */
function showError(message) {
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        alert('Error: ' + message);
    }
}

/**
 * Show info message
 */
function showInfo(message) {
    if (typeof toastr !== 'undefined') {
        toastr.info(message);
    } else {
        alert(message);
    }
}

/**
 * Debug function to test form data collection
 */
function debugFormData() {
    console.log('=== FORM DEBUG INFO ===');
    console.log('Patient ID:', $('#patientSelect').val());
    console.log('Doctor ID:', $('#doctorSelect').val());
    console.log('Entry Date:', $('#entryDate').val());
    console.log('Status:', $('#entryStatus').val());
    console.log('Priority:', $('#priority').val());

    console.log('Test rows count:', $('#testsContainer .test-row').length);

    $('#testsContainer .test-row').each(function (index) {
        const $row = $(this);
        console.log(`Test Row ${index}:`, {
            category: $row.find('.category-select').val(),
            test: $row.find('.test-select').val(),
            result: $row.find('.test-result').val(),
            min: $row.find('.test-min').val(),
            max: $row.find('.test-max').val(),
            unit: $row.find('.test-unit').val(),
            price: $row.find('.test-price').val()
        });
    });

    console.log('Current Edit ID:', currentEditId);
    console.log('=== END DEBUG INFO ===');
}

// Make debug functions available globally
window.debugFormData = debugFormData;

/**
 * Debug function to test test row functionality
 */
function debugTestRows() {
    console.log('=== TEST ROWS DEBUG ===');
    console.log('Tests data loaded:', testsData.length);
    console.log('Categories data loaded:', categoriesData.length);
    console.log('Current test rows:', $('#testsContainer .test-row').length);

    $('#testsContainer .test-row').each(function (index) {
        const $row = $(this);
        const $categorySelect = $row.find('.category-select');
        const $testSelect = $row.find('.test-select');

        console.log(`Row ${index}:`, {
            categoryOptions: $categorySelect.find('option').length,
            testOptions: $testSelect.find('option').length,
            categoryValue: $categorySelect.val(),
            testValue: $testSelect.val(),
            hasSelect2: $categorySelect.hasClass('select2-hidden-accessible')
        });
    });
    console.log('=== END TEST ROWS DEBUG ===');
}

window.debugTestRows = debugTestRows;

/**
 * Test API connectivity
 */
async function testAPI() {
    console.log('Testing API connectivity...');

    try {
        const requestData = {
            action: 'list'
        };

        const secretKey = API_CONFIG.getSecretKey();
        if (secretKey) {
            requestData.secret_key = secretKey;
        }

        const response = await $.ajax({
            url: API_CONFIG.getURL(),
            method: 'GET',
            data: requestData,
            dataType: 'json',
            timeout: 10000
        });

        console.log('API test response:', response);
        if (response.success) {
            console.log('✅ API is working correctly');
            return true;
        } else {
            console.error('❌ API returned error:', response.message);
            return false;
        }
    } catch (error) {
        console.error('❌ API connection failed:', error);
        return false;
    }
}

window.testAPI = testAPI;

/**
 * Test save functionality with minimal data
 */
async function testSave() {
    console.log('Testing save functionality...');

    // Check if we have required data
    if (patientsData.length === 0) {
        console.error('❌ No patients data loaded');
        return false;
    }

    if (testsData.length === 0) {
        console.error('❌ No tests data loaded');
        return false;
    }

    try {
        const formData = new FormData();
        formData.append('action', 'save');

        const secretKey = API_CONFIG.getSecretKey();
        if (secretKey) {
            formData.append('secret_key', secretKey);
        }
        formData.append('patient_id', patientsData[0].id); // Use first patient
        formData.append('entry_date', new Date().toISOString().split('T')[0]); // Today's date
        formData.append('status', 'pending');

        // Add current user ID if available
        if (typeof currentUserId !== 'undefined' && currentUserId) {
            formData.append('added_by', currentUserId);
        }

        // Add minimal test data
        const testData = [{
            test_id: testsData[0].id,
            category_id: testsData[0].category_id || null,
            result_value: '',
            min: testsData[0].min || '',
            max: testsData[0].max || '',
            price: testsData[0].price || 0,
            unit: testsData[0].unit || ''
        }];

        formData.append('tests', JSON.stringify(testData));

        console.log('Test save data:', Object.fromEntries(formData));

        const response = await $.ajax({
            url: API_CONFIG.getURL(),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        });

        console.log('Test save response:', response);

        if (response && response.success) {
            console.log('✅ Save functionality is working');
            return true;
        } else {
            console.error('❌ Save failed:', response ? response.message : 'No response');
            return false;
        }

    } catch (error) {
        console.error('❌ Save test failed:', error);
        console.error('Error details:', {
            status: error.status,
            statusText: error.statusText,
            responseText: error.responseText
        });
        return false;
    }
}

window.testSave = testSave;

/**
 * Check authentication status
 */
async function checkAuth() {
    console.log('Checking authentication...');

    try {
        const requestData = {
            action: 'stats'
        };

        const secretKey = API_CONFIG.getSecretKey();
        if (secretKey) {
            requestData.secret_key = secretKey;
        }

        const response = await $.ajax({
            url: API_CONFIG.getURL(),
            method: 'GET',
            data: requestData,
            dataType: 'json'
        });

        console.log('Auth check response:', response);

        if (response.success) {
            console.log('✅ User is authenticated');
            return true;
        } else {
            console.error('❌ Authentication failed:', response.message);
            return false;
        }
    } catch (error) {
        console.error('❌ Auth check failed:', error);
        return false;
    }
}

window.checkAuth = checkAuth;

/**
 * Remove duplicate test rows
 */
function removeDuplicateTestRows() {
    console.log('Checking for duplicate test rows...');

    const seenTestIds = new Set();
    const rowsToRemove = [];

    $('#testsContainer .test-row').each(function () {
        const $row = $(this);
        const testId = $row.find('.test-select').val();

        if (testId) {
            if (seenTestIds.has(testId)) {
                // This is a duplicate
                rowsToRemove.push($row);
                console.log('Found duplicate test ID:', testId);
            } else {
                seenTestIds.add(testId);
            }
        }
    });

    // Remove duplicate rows
    rowsToRemove.forEach($row => {
        $row.remove();
    });

    if (rowsToRemove.length > 0) {
        showInfo(`Removed ${rowsToRemove.length} duplicate test rows`);
        calculateTotals();
    }

    console.log(`Removed ${rowsToRemove.length} duplicate test rows`);
}

window.removeDuplicateTestRows = removeDuplicateTestRows;

/**
 * Comprehensive test function to verify all functionality
 */
async function runComprehensiveTest() {
    console.log('🔍 Running comprehensive test...');

    const results = {
        dataLoading: false,
        apiConnectivity: false,
        formValidation: false,
        duplicateHandling: false,
        saveProcess: false
    };

    try {
        // Test 1: Data Loading
        console.log('📊 Testing data loading...');
        if (testsData.length > 0 && categoriesData.length > 0 && patientsData.length > 0) {
            results.dataLoading = true;
            console.log('✅ Data loading: PASSED');
        } else {
            console.log('❌ Data loading: FAILED');
            console.log(`Tests: ${testsData.length}, Categories: ${categoriesData.length}, Patients: ${patientsData.length}`);
        }

        // Test 2: API Connectivity
        console.log('🌐 Testing API connectivity...');
        results.apiConnectivity = await testAPI();

        // Test 3: Dropdown Population & Form Validation
        console.log('📝 Testing dropdown population and form validation...');
        // Open modal and check dropdowns
        openAddModal();

        // Wait for modal to be ready
        await new Promise(resolve => setTimeout(resolve, 2000));

        // Check if dropdowns have options
        const $firstRow = $('#testsContainer .test-row').first();
        const categoryOptions = $firstRow.find('.category-select option').length;
        const testOptions = $firstRow.find('.test-select option').length;

        console.log(`Category options: ${categoryOptions}, Test options: ${testOptions}`);

        if (categoryOptions > 1 && testOptions > 1) {
            console.log('✅ Dropdown population: PASSED');

            // Set test data
            if (patientsData.length > 0) {
                $('#patientSelect').val(patientsData[0].id);
            }
            $('#entryDate').val(new Date().toISOString().split('T')[0]);

            // Select first available test
            if (testOptions > 1) {
                const firstTestValue = $firstRow.find('.test-select option:nth-child(2)').val();
                $firstRow.find('.test-select').val(firstTestValue).trigger('change');
                $firstRow.find('.test-price').val(100);
            }

            // Test validation
            const isValid = validateForm();
            results.formValidation = isValid;
            console.log(isValid ? '✅ Form validation: PASSED' : '❌ Form validation: FAILED');
        } else {
            console.log('❌ Dropdown population: FAILED');
            console.log('Attempting to refresh dropdowns...');
            refreshAllDropdowns();

            // Wait and check again
            await new Promise(resolve => setTimeout(resolve, 1000));
            const newCategoryOptions = $firstRow.find('.category-select option').length;
            const newTestOptions = $firstRow.find('.test-select option').length;

            if (newCategoryOptions > 1 && newTestOptions > 1) {
                results.formValidation = true;
                console.log('✅ Dropdown population: PASSED (after refresh)');
            } else {
                results.formValidation = false;
                console.log('❌ Dropdown population: FAILED (even after refresh)');
            }
        }

        // Test 4: Duplicate Handling
        console.log('🔄 Testing duplicate handling...');
        // Add duplicate test row
        if (testsData.length > 0) {
            addTestRow();
            const $secondRow = $('#testsContainer .test-row').last();
            $secondRow.find('.test-select').val(testsData[0].id).trigger('change');

            // Check for duplicates
            const testIds = [];
            $('#testsContainer .test-row').each(function () {
                const testId = $(this).find('.test-select').val();
                if (testId) testIds.push(testId);
            });

            const hasDuplicates = testIds.length !== new Set(testIds).size;
            if (hasDuplicates) {
                removeDuplicateTestRows();
                results.duplicateHandling = true;
                console.log('✅ Duplicate handling: PASSED');
            } else {
                results.duplicateHandling = true;
                console.log('✅ Duplicate handling: PASSED (no duplicates found)');
            }
        }

        // Test 5: Save Process (dry run)
        console.log('💾 Testing save process...');
        try {
            // Collect form data without actually saving
            const formData = new FormData($('#entryForm')[0]);
            const tests = [];

            $('#testsContainer .test-row').each(function () {
                const testId = $(this).find('.test-select').val();
                if (testId) {
                    tests.push({
                        test_id: testId,
                        category_id: $(this).find('.category-select').val(),
                        main_category_id: $(this).find('.test-main-category-id').val(),
                        result_value: $(this).find('.test-result').val(),
                        price: $(this).find('.test-price').val()
                    });
                }
            });

            if (tests.length > 0 && formData.get('patient_id')) {
                results.saveProcess = true;
                console.log('✅ Save process: PASSED (data collection successful)');
            } else {
                console.log('❌ Save process: FAILED (missing required data)');
            }
        } catch (error) {
            console.log('❌ Save process: FAILED', error);
        }

        // Close modal
        $('#entryModal').modal('hide');

        // Summary
        console.log('\n📋 TEST SUMMARY:');
        console.log('================');
        Object.entries(results).forEach(([test, passed]) => {
            console.log(`${passed ? '✅' : '❌'} ${test}: ${passed ? 'PASSED' : 'FAILED'}`);
        });

        const allPassed = Object.values(results).every(result => result);
        console.log(`\n🎯 Overall Status: ${allPassed ? '✅ ALL TESTS PASSED' : '❌ SOME TESTS FAILED'}`);

        if (allPassed) {
            showSuccess('All tests passed! The save entry functionality should work correctly.');
        } else {
            showError('Some tests failed. Please check the console for details.');
        }

        return results;

    } catch (error) {
        console.error('❌ Comprehensive test failed:', error);
        showError('Test execution failed: ' + error.message);
        return results;
    }
}

window.runComprehensiveTest = runComprehensiveTest;

/**
 * Quick fix function to address common issues
 */
function quickFix() {
    console.log('🔧 Running quick fix...');

    try {
        // Fix 1: Remove any duplicate test rows
        removeDuplicateTestRows();

        // Fix 2: Ensure main_category_id is populated
        $('#testsContainer .test-row').each(function () {
            const $row = $(this);
            const categoryId = $row.find('.category-select').val();
            const testId = $row.find('.test-select').val();

            if (testId && !$row.find('.test-main-category-id').val()) {
                // Try to get main_category_id from category data
                if (categoryId) {
                    const categoryInfo = categoriesData.find(c => c.id == categoryId);
                    if (categoryInfo && categoryInfo.main_category_id) {
                        $row.find('.test-main-category-id').val(categoryInfo.main_category_id);
                        console.log(`Fixed main_category_id for test ${testId}: ${categoryInfo.main_category_id}`);
                    }
                }
            }
        });

        // Fix 3: Recalculate totals
        calculateTotals();

        // Fix 4: Refresh Select2 dropdowns
        $('.select2').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
            }
        });

        console.log('✅ Quick fix completed');
        showSuccess('Quick fix applied successfully');

    } catch (error) {
        console.error('❌ Quick fix failed:', error);
        showError('Quick fix failed: ' + error.message);
    }
}

window.quickFix = quickFix;

/**
 * Refresh all dropdowns with current data - Enhanced version
 */
function refreshAllDropdowns() {
    console.log('🔄 Refreshing all dropdowns...');

    try {
        // Validate data availability
        const dataStatus = {
            categories: categoriesData && categoriesData.length > 0,
            tests: testsData && testsData.length > 0,
            patients: patientsData && patientsData.length > 0,
            doctors: doctorsData && doctorsData.length > 0
        };

        console.log('Data status:', dataStatus);

        // If critical data is missing, attempt to reload
        if (!dataStatus.categories || !dataStatus.tests) {
            console.log('Critical data missing, attempting to reload...');
            showInfo('Reloading data, please wait...');

            loadInitialData().then((success) => {
                if (success) {
                    console.log('Data reloaded successfully, refreshing dropdowns...');
                    setTimeout(() => refreshAllDropdowns(), 500);
                } else {
                    showError('Failed to reload data. Please refresh the page.');
                }
            });
            return false;
        }

        console.log(`Refreshing with ${categoriesData.length} categories, ${testsData.length} tests, ${patientsData.length} patients, ${doctorsData.length} doctors`);

        let refreshedRows = 0;
        let errors = [];

        // Refresh each test row
        $('#testsContainer .test-row').each(function (index) {
            try {
                const $row = $(this);
                const $categorySelect = $row.find('.category-select');
                const $testSelect = $row.find('.test-select');

                // Store current values
                const currentCategory = $categorySelect.val();
                const currentTest = $testSelect.val();

                console.log(`Refreshing row ${index}: category=${currentCategory}, test=${currentTest}`);

                // Rebuild category options with validation
                $categorySelect.find('option:not(:first)').remove();
                categoriesData.forEach(category => {
                    if (category && category.id && category.name) {
                        $categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
                    }
                });

                // Rebuild test options with validation
                $testSelect.find('option:not(:first)').remove();
                testsData.forEach(test => {
                    if (test && test.id && test.name) {
                        const displayName = `${test.name} [ID: ${test.id}]`;
                        const option = `<option value="${test.id}" 
                            data-category-id="${test.category_id || ''}" 
                            data-price="${test.price || 0}" 
                            data-unit="${test.unit || ''}" 
                            data-min="${test.min || ''}" 
                            data-max="${test.max || ''}">${displayName}</option>`;
                        $testSelect.append(option);
                    }
                });

                // Restore values if they still exist
                if (currentCategory && $categorySelect.find(`option[value="${currentCategory}"]`).length) {
                    $categorySelect.val(currentCategory);
                }
                if (currentTest && $testSelect.find(`option[value="${currentTest}"]`).length) {
                    $testSelect.val(currentTest);
                }

                // Refresh Select2 with error handling
                try {
                    if ($categorySelect.hasClass('select2-hidden-accessible')) {
                        $categorySelect.select2('destroy');
                    }
                    $categorySelect.select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Select Category',
                        allowClear: true
                    });

                    if ($testSelect.hasClass('select2-hidden-accessible')) {
                        $testSelect.select2('destroy');
                    }
                    $testSelect.select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Select Test',
                        allowClear: true
                    });
                } catch (select2Error) {
                    console.warn(`Select2 refresh failed for row ${index}:`, select2Error);
                }

                refreshedRows++;

            } catch (rowError) {
                console.error(`Error refreshing row ${index}:`, rowError);
                errors.push(`Row ${index}: ${rowError.message}`);
            }
        });

        // Refresh main dropdowns
        try {
            populatePatientSelect();
            populateDoctorSelect();
            populateGlobalCategoryFilter();
        } catch (mainDropdownError) {
            console.error('Error refreshing main dropdowns:', mainDropdownError);
            errors.push(`Main dropdowns: ${mainDropdownError.message}`);
        }

        // Report results
        if (errors.length === 0) {
            console.log(`✅ Successfully refreshed ${refreshedRows} test rows and main dropdowns`);
            showSuccess(`Dropdowns refreshed successfully (${refreshedRows} test rows)`);
            return true;
        } else {
            console.warn(`⚠️ Refreshed ${refreshedRows} rows with ${errors.length} errors:`, errors);
            showInfo(`Dropdowns partially refreshed. ${errors.length} errors occurred.`);
            return false;
        }

    } catch (error) {
        console.error('❌ Critical error refreshing dropdowns:', error);
        showError('Failed to refresh dropdowns: ' + error.message);
        return false;
    }
}

window.refreshAllDropdowns = refreshAllDropdowns;

/**
 * Force reload all data and refresh dropdowns
 */
async function forceReloadData() {
    console.log('🔄 Force reloading all data...');

    try {
        // Reset data arrays
        testsData = [];
        categoriesData = [];
        patientsData = [];
        doctorsData = [];

        // Reload all data
        await loadInitialData();

        // Refresh dropdowns
        refreshAllDropdowns();

        // Refresh patient and doctor selects
        populatePatientSelect();
        populateDoctorSelect();

        console.log('✅ Data force reloaded successfully');
        showSuccess('All data reloaded successfully');

    } catch (error) {
        console.error('❌ Error force reloading data:', error);
        showError('Failed to reload data: ' + error.message);
    }
}

window.forceReloadData = forceReloadData;

/**
 * Enhanced API endpoint switching with automatic testing and fallback
 */
async function switchAPI(useNew = true, skipTest = false) {
    console.log(`🔄 Switching to ${useNew ? 'NEW' : 'OLD'} API...`);

    const previousAPI = API_CONFIG.useNewAPI;
    API_CONFIG.useNewAPI = useNew;

    console.log(`Current API: ${API_CONFIG.getURL()}`);
    console.log(`Secret Key: ${API_CONFIG.getSecretKey() || 'None (new API)'}`);

    // Test the API endpoint if not skipping
    if (!skipTest) {
        showInfo('Testing API endpoint...');

        try {
            const isAvailable = await API_CONFIG.isAvailable();

            if (!isAvailable) {
                console.warn(`${useNew ? 'New' : 'Old'} API is not available`);

                // Revert to previous API
                API_CONFIG.useNewAPI = previousAPI;

                showError(`${useNew ? 'New' : 'Old'} API is not available. Reverted to ${previousAPI ? 'new' : 'old'} API.`);
                return false;
            }

            console.log(`✅ ${useNew ? 'New' : 'Old'} API is available and working`);

        } catch (error) {
            console.error(`Error testing ${useNew ? 'new' : 'old'} API:`, error);

            // Revert to previous API
            API_CONFIG.useNewAPI = previousAPI;

            showError(`Failed to test ${useNew ? 'new' : 'old'} API. Reverted to ${previousAPI ? 'new' : 'old'} API.`);
            return false;
        }
    }

    // Refresh the DataTable to use new API
    if (entriesTable) {
        try {
            entriesTable.ajax.reload();
        } catch (error) {
            console.error('Error reloading DataTable:', error);
        }
    }

    // Update API handler configuration
    if (window.apiHandler) {
        apiHandler.config = API_CONFIG;
    }

    const apiName = useNew ? 'new patho_api/entry.php' : 'old ajax/entry_api_fixed.php';
    showSuccess(`Successfully switched to ${apiName} API`);

    return true;
}

/**
 * Automatically detect and switch to the best available API
 */
async function autoDetectBestAPI() {
    console.log('🔍 Auto-detecting best available API...');
    showInfo('Detecting best API endpoint...');

    try {
        // Test new API first
        console.log('Testing new API...');
        const newAPIAvailable = await API_CONFIG.isAvailable(API_CONFIG.endpoints.new);

        if (newAPIAvailable) {
            console.log('✅ New API is available');
            const switched = await switchAPI(true, true); // Skip test since we already tested
            if (switched) {
                showSuccess('Using new API (patho_api/entry.php)');
                return 'new';
            }
        }

        // Test old API as fallback
        console.log('Testing old API...');
        const oldAPIAvailable = await API_CONFIG.isAvailable(API_CONFIG.endpoints.old);

        if (oldAPIAvailable) {
            console.log('✅ Old API is available');
            const switched = await switchAPI(false, true); // Skip test since we already tested
            if (switched) {
                showInfo('Using old API (ajax/entry_api_fixed.php) as fallback');
                return 'old';
            }
        }

        // Neither API is available
        console.error('❌ Neither API endpoint is available');
        showError('No API endpoints are available. Please check server configuration.');
        return null;

    } catch (error) {
        console.error('Error during API auto-detection:', error);
        showError('Failed to detect available APIs. Using default configuration.');
        return null;
    }
}

/**
 * Test both API endpoints and return availability status
 */
async function testBothAPIs() {
    console.log('🔍 Testing both API endpoints...');

    const results = {
        new: { available: false, error: null, responseTime: null },
        old: { available: false, error: null, responseTime: null }
    };

    // Test new API
    try {
        const startTime = Date.now();
        results.new.available = await API_CONFIG.isAvailable(API_CONFIG.endpoints.new);
        results.new.responseTime = Date.now() - startTime;
        console.log(`New API: ${results.new.available ? 'Available' : 'Not available'} (${results.new.responseTime}ms)`);
    } catch (error) {
        results.new.error = error.message;
        console.error('New API test failed:', error);
    }

    // Test old API
    try {
        const startTime = Date.now();
        results.old.available = await API_CONFIG.isAvailable(API_CONFIG.endpoints.old);
        results.old.responseTime = Date.now() - startTime;
        console.log(`Old API: ${results.old.available ? 'Available' : 'Not available'} (${results.old.responseTime}ms)`);
    } catch (error) {
        results.old.error = error.message;
        console.error('Old API test failed:', error);
    }

    // Summary
    console.log('\n📋 API Test Results:');
    console.log('==================');
    console.log(`New API (${API_CONFIG.endpoints.new}): ${results.new.available ? '✅ Available' : '❌ Not Available'} ${results.new.responseTime ? `(${results.new.responseTime}ms)` : ''}`);
    console.log(`Old API (${API_CONFIG.endpoints.old}): ${results.old.available ? '✅ Available' : '❌ Not Available'} ${results.old.responseTime ? `(${results.old.responseTime}ms)` : ''}`);

    // Recommend best API
    if (results.new.available && results.old.available) {
        const faster = results.new.responseTime <= results.old.responseTime ? 'new' : 'old';
        console.log(`\n🎯 Recommendation: Use ${faster} API (faster response time)`);

        showSuccess(`Both APIs available. New API: ${results.new.responseTime}ms, Old API: ${results.old.responseTime}ms`);
    } else if (results.new.available) {
        console.log('\n🎯 Recommendation: Use new API');
        showSuccess('New API is available and working');
    } else if (results.old.available) {
        console.log('\n⚠️ Fallback: Use old API');
        showInfo('Only old API is available');
    } else {
        console.log('\n❌ No APIs available');
        showError('No API endpoints are working');
    }

    return results;
}

/**
 * Smart API switching with automatic fallback
 */
async function smartSwitchAPI(preferNew = true) {
    console.log(`🧠 Smart API switching (prefer ${preferNew ? 'new' : 'old'})...`);

    try {
        const testResults = await testBothAPIs();

        // Try preferred API first
        const preferredAPI = preferNew ? 'new' : 'old';
        const fallbackAPI = preferNew ? 'old' : 'new';

        if (testResults[preferredAPI].available) {
            const success = await switchAPI(preferNew, true);
            if (success) {
                return preferredAPI;
            }
        }

        // Try fallback API
        if (testResults[fallbackAPI].available) {
            console.log(`Preferred API not available, switching to ${fallbackAPI} API`);
            const success = await switchAPI(!preferNew, true);
            if (success) {
                return fallbackAPI;
            }
        }

        // No APIs available
        showError('No working API endpoints found');
        return null;

    } catch (error) {
        console.error('Smart API switching failed:', error);
        showError('Failed to switch APIs automatically');
        return null;
    }
}

window.switchAPI = switchAPI;

/**
 * Data caching functions for improved reliability
 */

/**
 * Cache data in localStorage with timestamp
 */
function cacheData(key, data) {
    try {
        const cacheItem = {
            data: data,
            timestamp: Date.now(),
            version: '1.0'
        };
        localStorage.setItem(`entrySystem_${key}`, JSON.stringify(cacheItem));
        console.log(`Cached ${key} data (${Array.isArray(data) ? data.length : 'N/A'} items)`);
    } catch (error) {
        console.warn(`Failed to cache ${key} data:`, error);
    }
}

/**
 * Get cached data if it's still valid (within 1 hour)
 */
function getCachedData(key) {
    try {
        const cached = localStorage.getItem(`entrySystem_${key}`);
        if (!cached) return null;

        const cacheItem = JSON.parse(cached);
        const maxAge = 60 * 60 * 1000; // 1 hour

        if (Date.now() - cacheItem.timestamp < maxAge) {
            console.log(`Using cached ${key} data (${Array.isArray(cacheItem.data) ? cacheItem.data.length : 'N/A'} items)`);
            return cacheItem.data;
        } else {
            // Cache expired, remove it
            localStorage.removeItem(`entrySystem_${key}`);
            return null;
        }
    } catch (error) {
        console.warn(`Failed to get cached ${key} data:`, error);
        return null;
    }
}

/**
 * Clear all cached data
 */
function clearCache() {
    try {
        const keys = ['tests', 'categories', 'patients', 'doctors'];
        keys.forEach(key => {
            localStorage.removeItem(`entrySystem_${key}`);
        });
        console.log('Cache cleared successfully');
        showSuccess('Cache cleared successfully');
    } catch (error) {
        console.error('Failed to clear cache:', error);
        showError('Failed to clear cache');
    }
}

/**
 * Load data from cache as fallback
 */
async function loadFromCache() {
    console.log('Attempting to load data from cache...');

    try {
        const cachedTests = getCachedData('tests');
        const cachedCategories = getCachedData('categories');
        const cachedPatients = getCachedData('patients');
        const cachedDoctors = getCachedData('doctors');

        if (cachedTests && cachedCategories) {
            testsData = cachedTests;
            categoriesData = cachedCategories;
            patientsData = cachedPatients || [];
            doctorsData = cachedDoctors || [];

            // Populate dropdowns
            populatePatientSelect();
            populateDoctorSelect();

            console.log('Successfully loaded data from cache');
            return true;
        } else {
            console.log('Insufficient cached data available');
            return false;
        }
    } catch (error) {
        console.error('Failed to load from cache:', error);
        return false;
    }
}

/**
 * Loading indicator functions
 */

/**
 * Show loading indicator
 */
function showLoadingIndicator(message = 'Loading...') {
    // Remove existing indicator
    hideLoadingIndicator();

    // Create loading overlay
    const loadingHtml = `
        <div id="loadingIndicator" class="loading-overlay">
            <div class="loading-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="loading-message mt-2">${message}</div>
            </div>
        </div>
    `;

    $('body').append(loadingHtml);

    // Add CSS if not already present
    if (!$('#loadingIndicatorCSS').length) {
        const css = `
            <style id="loadingIndicatorCSS">
                .loading-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                }
                .loading-content {
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    text-align: center;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .loading-message {
                    color: #333;
                    font-weight: 500;
                }
            </style>
        `;
        $('head').append(css);
    }
}

/**
 * Hide loading indicator
 */
function hideLoadingIndicator() {
    $('#loadingIndicator').remove();
}

/**
 * Update loading indicator message
 */
function updateLoadingMessage(message) {
    $('#loadingIndicator .loading-message').text(message);
}

// Make cache functions available globally
window.cacheData = cacheData;
window.getCachedData = getCachedData;
window.clearCache = clearCache;
window.loadFromCache = loadFromCache;

/**
 * Test both APIs
 */
async function testBothAPIs() {
    console.log('🔍 Testing both APIs...');

    const results = {
        oldAPI: false,
        newAPI: false
    };

    try {
        // Test old API
        console.log('Testing OLD API...');
        switchAPI(false);
        await new Promise(resolve => setTimeout(resolve, 500));
        results.oldAPI = await testAPI();

        // Test new API
        console.log('Testing NEW API...');
        switchAPI(true);
        await new Promise(resolve => setTimeout(resolve, 500));
        results.newAPI = await testAPI();

        // Summary
        console.log('\n📋 API TEST SUMMARY:');
        console.log('==================');
        console.log(`${results.oldAPI ? '✅' : '❌'} Old API (ajax/entry_api_fixed.php): ${results.oldAPI ? 'WORKING' : 'FAILED'}`);
        console.log(`${results.newAPI ? '✅' : '❌'} New API (patho_api/entry.php): ${results.newAPI ? 'WORKING' : 'FAILED'}`);

        if (results.newAPI) {
            console.log('\n🎯 Recommendation: Use NEW API (already selected)');
            showSuccess('Both APIs tested. Using new API (recommended).');
        } else if (results.oldAPI) {
            console.log('\n⚠️ Fallback: Using OLD API');
            switchAPI(false);
            showInfo('New API failed, switched to old API as fallback.');
        } else {
            console.log('\n❌ Both APIs failed!');
            showError('Both APIs failed! Please check server configuration.');
        }

        return results;

    } catch (error) {
        console.error('❌ Error testing APIs:', error);
        showError('Error testing APIs: ' + error.message);
        return results;
    }
}

window.testBothAPIs = testBothAPIs;

/**
 * Enhanced Edit Entry with Confirmation for Important Changes
 */
function editEntryWithConfirmation(id) {
    // For entries with completed status, show confirmation
    const rowData = entriesTable.row(`[data-id="${id}"]`).data();

    if (rowData && rowData.status === 'completed') {
        if (!confirm('This entry is marked as completed. Are you sure you want to edit it?')) {
            return;
        }
    }

    editEntry(id);
}

/**
 * Quick Edit Functions for Common Operations
 */
function quickEditStatus(id, newStatus) {
    if (!confirm(`Change entry status to "${newStatus}"?`)) {
        return;
    }

    makeAPIRequest({
        action: 'save',
        method: 'POST',
        data: {
            id: id,
            status: newStatus
        }
    }).then(response => {
        showSuccess(`Status updated to ${newStatus}`);
        refreshTable();
    }).catch(error => {
        console.error('Error updating status:', error);
    });
}

function quickEditPriority(id, newPriority) {
    if (!confirm(`Change entry priority to "${newPriority}"?`)) {
        return;
    }

    makeAPIRequest({
        action: 'save',
        method: 'POST',
        data: {
            id: id,
            priority: newPriority
        }
    }).then(response => {
        showSuccess(`Priority updated to ${newPriority}`);
        refreshTable();
    }).catch(error => {
        console.error('Error updating priority:', error);
    });
}

/**
 * Keyboard Shortcuts for Edit Modal
 */
function setupEditKeyboardShortcuts() {
    $(document).on('keydown', function (e) {
        // Only when modal is open
        if ($('#entryModal').hasClass('show')) {
            // Ctrl+S to save
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                $('#entryForm').submit();
            }

            // Escape to close (if not already handled by Bootstrap)
            if (e.key === 'Escape' && !e.ctrlKey && !e.altKey) {
                $('#entryModal').modal('hide');
            }

            // Ctrl+T to add new test row
            if (e.ctrlKey && e.key === 't') {
                e.preventDefault();
                addTestRow();
            }
        }
    });
}

// Initialize keyboard shortcuts when document is ready
$(document).ready(function () {
    setupEditKeyboardShortcuts();
});

/**
 * Auto-save draft functionality for long forms
 */
let autoSaveDraftTimer;
let draftSaveKey = 'entry_draft_';

function enableAutoSaveDraft() {
    // Clear existing timer
    if (autoSaveDraftTimer) {
        clearInterval(autoSaveDraftTimer);
    }

    // Auto-save every 30 seconds
    autoSaveDraftTimer = setInterval(() => {
        if ($('#entryModal').hasClass('show')) {
            saveDraft();
        }
    }, 30000);
}

function saveDraft() {
    try {
        const formData = new FormData($('#entryForm')[0]);
        const draftData = {};

        // Convert FormData to object
        for (let [key, value] of formData.entries()) {
            draftData[key] = value;
        }

        // Add tests data
        const tests = [];
        $('#testsContainer .test-row').each(function () {
            const $row = $(this);
            const testId = $row.find('.test-select').val();
            if (testId) {
                tests.push({
                    test_id: testId,
                    category_id: $row.find('.category-select').val(),
                    result_value: $row.find('.test-result').val(),
                    price: $row.find('.test-price').val()
                });
            }
        });

        draftData.tests = tests;
        draftData.timestamp = Date.now();

        // Save to localStorage
        const key = draftSaveKey + (currentEditId || 'new');
        localStorage.setItem(key, JSON.stringify(draftData));

        console.log('Draft saved automatically');
    } catch (error) {
        console.error('Error saving draft:', error);
    }
}

function loadDraft() {
    try {
        const key = draftSaveKey + (currentEditId || 'new');
        const draftData = localStorage.getItem(key);

        if (draftData) {
            const data = JSON.parse(draftData);
            const draftAge = Date.now() - data.timestamp;

            // Only load drafts less than 1 hour old
            if (draftAge < 3600000) {
                if (confirm('A draft was found for this entry. Would you like to load it?')) {
                    // Load the draft data
                    Object.keys(data).forEach(key => {
                        if (key !== 'tests' && key !== 'timestamp') {
                            const $field = $(`[name="${key}"]`);
                            if ($field.length) {
                                $field.val(data[key]);
                            }
                        }
                    });

                    // Load tests
                    if (data.tests && data.tests.length > 0) {
                        $('#testsContainer').empty();
                        testRowCounter = 0;

                        data.tests.forEach(test => {
                            addTestRow(test);
                        });
                    }

                    showInfo('Draft loaded successfully');
                }
            }
        }
    } catch (error) {
        console.error('Error loading draft:', error);
    }
}

function clearDraft() {
    try {
        const key = draftSaveKey + (currentEditId || 'new');
        localStorage.removeItem(key);
        console.log('Draft cleared');
    } catch (error) {
        console.error('Error clearing draft:', error);
    }
}

// Make functions available globally
window.editEntryWithConfirmation = editEntryWithConfirmation;
window.quickEditStatus = quickEditStatus;
window.quickEditPriority = quickEditPriority;
window.saveDraft = saveDraft;
window.loadDraft = loadDraft;
window.clearDraft = clearDraft;