<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';

// Gmail configuration
$gmail_config = [
    'email' => 'umakant171991@gmail.com',
    'smtp_server' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'imap_server' => 'imap.gmail.com',
    'imap_port' => 993
];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-cog mr-2"></i>Email Settings</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="email_inbox.php">Email</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Connection Status Row -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="connectionStatus">Checking...</h3>
                            <p>Gmail Connection</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="emailTemplates">0</h3>
                            <p>Email Templates</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="emailSignatures">0</h3>
                            <p>Signatures</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-signature"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="autoReplies">0</h3>
                            <p>Auto Replies</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-robot"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Gmail Configuration -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-envelope mr-1"></i>
                                Gmail Configuration
                            </h3>
                        </div>
                        <form id="gmailConfigForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="gmailAddress">Gmail Address</label>
                                    <input type="email" class="form-control" id="gmailAddress" value="<?php echo htmlspecialchars($gmail_config['email']); ?>" readonly>
                                    <small class="form-text text-muted">Your Gmail account for sending and receiving emails</small>
                                </div>

                                <div class="form-group">
                                    <label for="authType">Authentication Type</label>
                                    <select class="form-control" id="authType">
                                        <option value="app_password">App Password (Recommended)</option>
                                        <option value="oauth2">OAuth2 (Advanced)</option>
                                        <option value="regular">Regular Password (Less Secure)</option>
                                    </select>
                                </div>

                                <div class="form-group" id="passwordGroup">
                                    <label for="gmailPassword">Password/App Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="gmailPassword" placeholder="Enter your Gmail password">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
                                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted" id="passwordHelp">Enter your Gmail App Password for secure access</small>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="enableIMAP" checked>
                                        <label class="custom-control-label" for="enableIMAP">
                                            Enable IMAP (for receiving emails)
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="enableSMTP" checked>
                                        <label class="custom-control-label" for="enableSMTP">
                                            Enable SMTP (for sending emails)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save Configuration
                                </button>
                                <button type="button" class="btn btn-info ml-2" onclick="testConnection()">
                                    <i class="fas fa-plug mr-1"></i> Test Connection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>         
       <!-- Email Preferences -->
                <div class="col-md-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-sliders-h mr-1"></i>
                                Email Preferences
                            </h3>
                        </div>
                        <form id="emailPreferencesForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="defaultPriority">Default Email Priority</label>
                                    <select class="form-control" id="defaultPriority">
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="emailsPerPage">Emails Per Page</label>
                                    <select class="form-control" id="emailsPerPage">
                                        <option value="25">25</option>
                                        <option value="50" selected>50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="autoRefresh" checked>
                                        <label class="custom-control-label" for="autoRefresh">
                                            Auto-refresh inbox every 5 minutes
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="markAsRead">
                                        <label class="custom-control-label" for="markAsRead">
                                            Mark emails as read when viewed
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="showNotifications" checked>
                                        <label class="custom-control-label" for="showNotifications">
                                            Show desktop notifications for new emails
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="saveSentCopy" checked>
                                        <label class="custom-control-label" for="saveSentCopy">
                                            Save copy of sent emails
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save mr-1"></i> Save Preferences
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Email Templates -->
                <div class="col-md-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-alt mr-1"></i>
                                Email Templates
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-warning btn-sm" onclick="addNewTemplate()">
                                    <i class="fas fa-plus"></i> Add Template
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="templatesList">
                                <div class="text-center py-3">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading templates...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Signatures -->
                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-signature mr-1"></i>
                                Email Signatures
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-info btn-sm" onclick="addNewSignature()">
                                    <i class="fas fa-plus"></i> Add Signature
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="signaturesList">
                                <div class="text-center py-3">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading signatures...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Auto Reply Settings -->
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-robot mr-1"></i>
                                Auto Reply Settings
                            </h3>
                        </div>
                        <form id="autoReplyForm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="enableAutoReply">
                                                <label class="custom-control-label" for="enableAutoReply">
                                                    Enable Auto Reply (Out of Office)
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="autoReplySubject">Auto Reply Subject</label>
                                            <input type="text" class="form-control" id="autoReplySubject" placeholder="Out of Office - Auto Reply">
                                        </div>

                                        <div class="form-group">
                                            <label for="autoReplyStartDate">Start Date</label>
                                            <input type="datetime-local" class="form-control" id="autoReplyStartDate">
                                        </div>

                                        <div class="form-group">
                                            <label for="autoReplyEndDate">End Date</label>
                                            <input type="datetime-local" class="form-control" id="autoReplyEndDate">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="autoReplyMessage">Auto Reply Message</label>
                                            <textarea class="form-control" id="autoReplyMessage" rows="8" placeholder="Thank you for your email. I am currently out of office and will respond to your message when I return."></textarea>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="replyOnlyOnce">
                                                <label class="custom-control-label" for="replyOnlyOnce">
                                                    Send auto reply only once per sender
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="replyToKnownOnly">
                                                <label class="custom-control-label" for="replyToKnownOnly">
                                                    Reply only to known contacts
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-save mr-1"></i> Save Auto Reply Settings
                                </button>
                                <button type="button" class="btn btn-info ml-2" onclick="testAutoReply()">
                                    <i class="fas fa-paper-plane mr-1"></i> Send Test Auto Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div><
!-- Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="templateModalLabel">
                    <i class="fas fa-file-alt mr-2"></i>
                    <span id="templateModalTitle">Add Email Template</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="templateForm">
                <div class="modal-body">
                    <input type="hidden" id="templateId">
                    
                    <div class="form-group">
                        <label for="templateName">Template Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="templateName" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="templateCategory">Category</label>
                        <select class="form-control" id="templateCategory">
                            <option value="general">General</option>
                            <option value="appointment">Appointment</option>
                            <option value="lab_report">Lab Report</option>
                            <option value="follow_up">Follow Up</option>
                            <option value="billing">Billing</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="templateSubject">Subject <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="templateSubject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="templateBody">Message Body <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="templateBody" rows="8" required></textarea>
                        <small class="form-text text-muted">You can use placeholders like [PATIENT_NAME], [DATE], [TIME], [DOCTOR_NAME]</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save mr-1"></i> Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Signature Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="signatureModalLabel">
                    <i class="fas fa-signature mr-2"></i>
                    <span id="signatureModalTitle">Add Email Signature</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="signatureForm">
                <div class="modal-body">
                    <input type="hidden" id="signatureId">
                    
                    <div class="form-group">
                        <label for="signatureName">Signature Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signatureName" required>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="isDefaultSignature">
                            <label class="custom-control-label" for="isDefaultSignature">
                                Set as default signature
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="signatureContent">Signature Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="signatureContent" rows="6" required></textarea>
                        <small class="form-text text-muted">HTML formatting is supported</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Preview:</label>
                        <div id="signaturePreview" class="border p-3 bg-light">
                            <em>Signature preview will appear here...</em>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save mr-1"></i> Save Signature
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global variables
let templates = [];
let signatures = [];

// Initialize page
$(document).ready(function() {
    console.log('Initializing Email Settings...');
    
    // Setup event handlers
    setupEventHandlers();
    
    // Load current settings
    loadEmailSettings();
    
    // Check Gmail connection status
    checkConnectionStatus();
    
    // Load templates and signatures
    loadTemplates();
    loadSignatures();
});

// Setup event handlers
function setupEventHandlers() {
    // Gmail configuration form
    $('#gmailConfigForm').on('submit', function(e) {
        e.preventDefault();
        saveGmailConfiguration();
    });

    // Email preferences form
    $('#emailPreferencesForm').on('submit', function(e) {
        e.preventDefault();
        saveEmailPreferences();
    });

    // Auto reply form
    $('#autoReplyForm').on('submit', function(e) {
        e.preventDefault();
        saveAutoReplySettings();
    });

    // Template form
    $('#templateForm').on('submit', function(e) {
        e.preventDefault();
        saveTemplate();
    });

    // Signature form
    $('#signatureForm').on('submit', function(e) {
        e.preventDefault();
        saveSignature();
    });

    // Auth type change
    $('#authType').on('change', function() {
        updatePasswordHelp();
    });

    // Signature content change for preview
    $('#signatureContent').on('keyup', function() {
        updateSignaturePreview();
    });
}

// Load email settings
function loadEmailSettings() {
    $.ajax({
        url: 'ajax/email_settings_api.php',
        type: 'GET',
        data: { action: 'get_settings' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                populateSettings(response.data);
            }
        },
        error: function() {
            console.error('Failed to load email settings');
        }
    });
}

// Populate settings form
function populateSettings(settings) {
    if (settings.auth_type) $('#authType').val(settings.auth_type);
    if (settings.default_priority) $('#defaultPriority').val(settings.default_priority);
    if (settings.emails_per_page) $('#emailsPerPage').val(settings.emails_per_page);
    
    $('#enableIMAP').prop('checked', settings.enable_imap !== false);
    $('#enableSMTP').prop('checked', settings.enable_smtp !== false);
    $('#autoRefresh').prop('checked', settings.auto_refresh !== false);
    $('#markAsRead').prop('checked', settings.mark_as_read === true);
    $('#showNotifications').prop('checked', settings.show_notifications !== false);
    $('#saveSentCopy').prop('checked', settings.save_sent_copy !== false);
    
    // Auto reply settings
    $('#enableAutoReply').prop('checked', settings.enable_auto_reply === true);
    if (settings.auto_reply_subject) $('#autoReplySubject').val(settings.auto_reply_subject);
    if (settings.auto_reply_message) $('#autoReplyMessage').val(settings.auto_reply_message);
    if (settings.auto_reply_start_date) $('#autoReplyStartDate').val(settings.auto_reply_start_date);
    if (settings.auto_reply_end_date) $('#autoReplyEndDate').val(settings.auto_reply_end_date);
    $('#replyOnlyOnce').prop('checked', settings.reply_only_once === true);
    $('#replyToKnownOnly').prop('checked', settings.reply_to_known_only === true);
    
    updatePasswordHelp();
}

// Check connection status
function checkConnectionStatus() {
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'GET',
        data: { action: 'status' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                $('#connectionStatus').text('Connected').parent().removeClass('bg-info').addClass('bg-success');
            } else {
                $('#connectionStatus').text('Disconnected').parent().removeClass('bg-success').addClass('bg-danger');
            }
        },
        error: function() {
            $('#connectionStatus').text('Error').parent().removeClass('bg-success').addClass('bg-danger');
        }
    });
}

// Save Gmail configuration
function saveGmailConfiguration() {
    const password = $('#gmailPassword').val().trim();
    const authType = $('#authType').val();
    
    if (!password) {
        toastr.error('Please enter your Gmail password');
        return;
    }
    
    const submitBtn = $('#gmailConfigForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...').prop('disabled', true);
    
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'POST',
        data: {
            action: 'setup',
            password: password,
            password_type: authType,
            test_connection: true
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Gmail configuration saved successfully!');
                $('#gmailPassword').val('');
                checkConnectionStatus();
            } else {
                toastr.error(response.message || 'Failed to save Gmail configuration');
            }
        },
        error: function() {
            toastr.error('Failed to save Gmail configuration');
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

// Save email preferences
function saveEmailPreferences() {
    const preferences = {
        action: 'save_preferences',
        default_priority: $('#defaultPriority').val(),
        emails_per_page: $('#emailsPerPage').val(),
        enable_imap: $('#enableIMAP').is(':checked'),
        enable_smtp: $('#enableSMTP').is(':checked'),
        auto_refresh: $('#autoRefresh').is(':checked'),
        mark_as_read: $('#markAsRead').is(':checked'),
        show_notifications: $('#showNotifications').is(':checked'),
        save_sent_copy: $('#saveSentCopy').is(':checked')
    };
    
    $.ajax({
        url: 'ajax/email_settings_api.php',
        type: 'POST',
        data: preferences,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Email preferences saved successfully!');
            } else {
                toastr.error(response.message || 'Failed to save preferences');
            }
        },
        error: function() {
            toastr.error('Failed to save email preferences');
        }
    });
}

// Save auto reply settings
function saveAutoReplySettings() {
    const autoReplyData = {
        action: 'save_auto_reply',
        enable_auto_reply: $('#enableAutoReply').is(':checked'),
        auto_reply_subject: $('#autoReplySubject').val(),
        auto_reply_message: $('#autoReplyMessage').val(),
        auto_reply_start_date: $('#autoReplyStartDate').val(),
        auto_reply_end_date: $('#autoReplyEndDate').val(),
        reply_only_once: $('#replyOnlyOnce').is(':checked'),
        reply_to_known_only: $('#replyToKnownOnly').is(':checked')
    };
    
    $.ajax({
        url: 'ajax/email_settings_api.php',
        type: 'POST',
        data: autoReplyData,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Auto reply settings saved successfully!');
            } else {
                toastr.error(response.message || 'Failed to save auto reply settings');
            }
        },
        error: function() {
            toastr.error('Failed to save auto reply settings');
        }
    });
}

// Test connection
function testConnection() {
    const password = $('#gmailPassword').val().trim();
    
    if (!password) {
        toastr.error('Please enter your Gmail password first');
        return;
    }
    
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'POST',
        data: {
            action: 'setup',
            password: password,
            password_type: $('#authType').val(),
            test_connection: true
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Connection test successful!');
                checkConnectionStatus();
            } else {
                toastr.error(response.message || 'Connection test failed');
            }
        },
        error: function() {
            toastr.error('Connection test failed');
        }
    });
}

// Load templates
function loadTemplates() {
    $.ajax({
        url: 'ajax/email_settings_api.php',
        type: 'GET',
        data: { action: 'get_templates' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                templates = response.data || [];
                renderTemplates();
                $('#emailTemplates').text(templates.length);
            }
        },
        error: function() {
            $('#templatesList').html('<div class="text-center text-danger py-3">Failed to load templates</div>');
        }
    });
}

// Load signatures
function loadSignatures() {
    $.ajax({
        url: 'ajax/email_settings_api.php',
        type: 'GET',
        data: { action: 'get_signatures' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                signatures = response.data || [];
                renderSignatures();
                $('#emailSignatures').text(signatures.length);
            }
        },
        error: function() {
            $('#signaturesList').html('<div class="text-center text-danger py-3">Failed to load signatures</div>');
        }
    });
}

// Render templates
function renderTemplates() {
    let html = '';
    
    if (templates.length === 0) {
        html = '<div class="text-center text-muted py-3">No templates found. <a href="#" onclick="addNewTemplate()">Add your first template</a></div>';
    } else {
        templates.forEach(function(template) {
            html += `
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${escapeHtml(template.name)}</h6>
                            <small class="text-muted">${escapeHtml(template.category)}</small>
                            <p class="mb-1 mt-2"><strong>Subject:</strong> ${escapeHtml(template.subject)}</p>
                            <p class="mb-0 text-muted">${escapeHtml(template.body.substring(0, 100))}${template.body.length > 100 ? '...' : ''}</p>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editTemplate(${template.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteTemplate(${template.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#templatesList').html(html);
}

// Render signatures
function renderSignatures() {
    let html = '';
    
    if (signatures.length === 0) {
        html = '<div class="text-center text-muted py-3">No signatures found. <a href="#" onclick="addNewSignature()">Add your first signature</a></div>';
    } else {
        signatures.forEach(function(signature) {
            const isDefault = signature.is_default ? '<span class="badge badge-success ml-2">Default</span>' : '';
            
            html += `
                <div class="border rounded p-3 mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${escapeHtml(signature.name)}${isDefault}</h6>
                            <div class="signature-preview" style="max-height: 100px; overflow: hidden;">
                                ${signature.content}
                            </div>
                        </div>
                        <div class="btn-group btn-group-sm ml-2">
                            <button class="btn btn-outline-primary" onclick="editSignature(${signature.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteSignature(${signature.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#signaturesList').html(html);
}

// Utility functions
function togglePasswordVisibility() {
    const passwordField = $('#gmailPassword');
    const toggleIcon = $('#passwordToggleIcon');
    
    if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        toggleIcon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        passwordField.attr('type', 'password');
        toggleIcon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

function updatePasswordHelp() {
    const authType = $('#authType').val();
    const helpText = $('#passwordHelp');
    
    switch (authType) {
        case 'app_password':
            helpText.text('Enter your Gmail App Password (16 characters, recommended for security)');
            break;
        case 'oauth2':
            helpText.text('OAuth2 authentication will be configured automatically');
            $('#passwordGroup').hide();
            return;
        case 'regular':
            helpText.text('Enter your regular Gmail password (requires "Less secure app access" enabled)');
            break;
    }
    
    $('#passwordGroup').show();
}

function updateSignaturePreview() {
    const content = $('#signatureContent').val();
    $('#signaturePreview').html(content || '<em>Signature preview will appear here...</em>');
}

function addNewTemplate() {
    $('#templateId').val('');
    $('#templateName').val('');
    $('#templateCategory').val('general');
    $('#templateSubject').val('');
    $('#templateBody').val('');
    $('#templateModalTitle').text('Add Email Template');
    $('#templateModal').modal('show');
}

function addNewSignature() {
    $('#signatureId').val('');
    $('#signatureName').val('');
    $('#signatureContent').val('');
    $('#isDefaultSignature').prop('checked', false);
    $('#signatureModalTitle').text('Add Email Signature');
    updateSignaturePreview();
    $('#signatureModal').modal('show');
}

function saveTemplate() {
    const templateData = {
        action: 'save_template',
        id: $('#templateId').val(),
        name: $('#templateName').val(),
        category: $('#templateCategory').val(),
        subject: $('#templateSubject').val(),
        body: $('#templateBody').val()
    };
    
    $.ajax({
        url: 'ajax/email_settings_api.php',
        type: 'POST',
        data: templateData,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Template saved successfully!');
                $('#templateModal').modal('hide');
                loadTemplates();
            } else {
                toastr.error(response.message || 'Failed to save template');
            }
        },
        error: function() {
            toastr.error('Failed to save template');
        }
    });
}

function saveSignature() {
    const signatureData = {
        action: 'save_signature',
        id: $('#signatureId').val(),
        name: $('#signatureName').val(),
        content: $('#signatureContent').val(),
        is_default: $('#isDefaultSignature').is(':checked')
    };
    
    $.ajax({
        url: 'ajax/email_settings_api.php',
        type: 'POST',
        data: signatureData,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Signature saved successfully!');
                $('#signatureModal').modal('hide');
                loadSignatures();
            } else {
                toastr.error(response.message || 'Failed to save signature');
            }
        },
        error: function() {
            toastr.error('Failed to save signature');
        }
    });
}

function editTemplate(id) {
    const template = templates.find(t => t.id === id);
    if (template) {
        $('#templateId').val(template.id);
        $('#templateName').val(template.name);
        $('#templateCategory').val(template.category);
        $('#templateSubject').val(template.subject);
        $('#templateBody').val(template.body);
        $('#templateModalTitle').text('Edit Email Template');
        $('#templateModal').modal('show');
    }
}

function editSignature(id) {
    const signature = signatures.find(s => s.id === id);
    if (signature) {
        $('#signatureId').val(signature.id);
        $('#signatureName').val(signature.name);
        $('#signatureContent').val(signature.content);
        $('#isDefaultSignature').prop('checked', signature.is_default);
        $('#signatureModalTitle').text('Edit Email Signature');
        updateSignaturePreview();
        $('#signatureModal').modal('show');
    }
}

function deleteTemplate(id) {
    if (confirm('Are you sure you want to delete this template?')) {
        $.ajax({
            url: 'ajax/email_settings_api.php',
            type: 'POST',
            data: { action: 'delete_template', id: id },
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    toastr.success('Template deleted successfully!');
                    loadTemplates();
                } else {
                    toastr.error(response.message || 'Failed to delete template');
                }
            },
            error: function() {
                toastr.error('Failed to delete template');
            }
        });
    }
}

function deleteSignature(id) {
    if (confirm('Are you sure you want to delete this signature?')) {
        $.ajax({
            url: 'ajax/email_settings_api.php',
            type: 'POST',
            data: { action: 'delete_signature', id: id },
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    toastr.success('Signature deleted successfully!');
                    loadSignatures();
                } else {
                    toastr.error(response.message || 'Failed to delete signature');
                }
            },
            error: function() {
                toastr.error('Failed to delete signature');
            }
        });
    }
}

function testAutoReply() {
    const message = $('#autoReplyMessage').val();
    const subject = $('#autoReplySubject').val();
    
    if (!message || !subject) {
        toastr.error('Please enter auto reply subject and message first');
        return;
    }
    
    $.ajax({
        url: 'ajax/email_settings_api.php',
        type: 'POST',
        data: {
            action: 'test_auto_reply',
            subject: subject,
            message: message
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Test auto reply sent successfully!');
            } else {
                toastr.error(response.message || 'Failed to send test auto reply');
            }
        },
        error: function() {
            toastr.error('Failed to send test auto reply');
        }
    });
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<style>
.small-box {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.signature-preview {
    font-size: 0.9em;
    line-height: 1.4;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-group label {
    font-weight: 600;
    color: #495057;
}

@media (max-width: 768px) {
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
}
</style>

<?php require_once 'inc/footer.php'; ?>