<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';

// Get parameters for reply/forward
$replyUid = $_GET['reply'] ?? null;
$forwardUid = $_GET['forward'] ?? null;
$action = $replyUid ? 'reply' : ($forwardUid ? 'forward' : 'compose');

// Gmail configuration
$gmail_config = [
    'email' => 'umakant171991@gmail.com',
    'smtp_server' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_encryption' => 'tls'
];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <i class="fas fa-edit mr-2"></i>
                        <?php 
                        if ($action === 'reply') echo 'Reply Email';
                        elseif ($action === 'forward') echo 'Forward Email';
                        else echo 'Compose Email';
                        ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="email_inbox.php">Email</a></li>
                        <li class="breadcrumb-item active">Compose</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-envelope mr-1"></i>
                                <?php echo ucfirst($action); ?> Email
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='email_inbox.php'">
                                    <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Back to Inbox</span>
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="saveDraft()">
                                    <i class="fas fa-save"></i> <span class="d-none d-sm-inline">Save Draft</span>
                                </button>
                            </div>
                        </div>
                        
                        <form id="emailForm">
                            <div class="card-body">
                                <!-- Connection Status Alert -->
                                <div id="connectionAlert" class="alert alert-warning" style="display: none;">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span id="connectionMessage">Gmail connection not configured. Please set up your Gmail credentials in the inbox.</span>
                                </div>

                                <!-- Email Form Fields -->
                                <div class="form-group">
                                    <label for="fromEmail">From</label>
                                    <input type="email" class="form-control" id="fromEmail" value="<?php echo htmlspecialchars($gmail_config['email']); ?>" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="toEmail">To <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="toEmail" name="to" placeholder="recipient@example.com" required multiple>
                                    <small class="form-text text-muted">Separate multiple emails with commas</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="ccEmail">CC</label>
                                            <input type="email" class="form-control" id="ccEmail" name="cc" placeholder="cc@example.com" multiple>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="bccEmail">BCC</label>
                                            <input type="email" class="form-control" id="bccEmail" name="bcc" placeholder="bcc@example.com" multiple>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="emailSubject">Subject <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="emailSubject" name="subject" placeholder="Email subject" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="emailPriority">Priority</label>
                                            <select class="form-control" id="emailPriority" name="priority">
                                                <option value="normal">Normal</option>
                                                <option value="high">High</option>
                                                <option value="low">Low</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="emailBody">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="emailBody" name="body" rows="15" placeholder="Type your message here..." required></textarea>
                                </div>

                                <!-- Attachments -->
                                <div class="form-group">
                                    <label for="emailAttachments">Attachments</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="emailAttachments" name="attachments[]" multiple>
                                        <label class="custom-file-label" for="emailAttachments">Choose files...</label>
                                    </div>
                                    <small class="form-text text-muted">Maximum file size: 25MB per file</small>
                                    <div id="attachmentsList" class="mt-2"></div>
                                </div>

                                <!-- Email Options -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-cog mr-2"></i>
                                            Email Options
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="requestReadReceipt" name="read_receipt">
                                                    <label class="custom-control-label" for="requestReadReceipt">
                                                        Request read receipt
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="sendCopy" name="send_copy" checked>
                                                    <label class="custom-control-label" for="sendCopy">
                                                        Save copy to Sent folder
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <label for="scheduleDate">Schedule Send (Optional)</label>
                                                <input type="datetime-local" class="form-control" id="scheduleDate" name="schedule_date">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="emailTemplate">Use Template</label>
                                                <select class="form-control" id="emailTemplate">
                                                    <option value="">Select template...</option>
                                                    <option value="appointment">Appointment Reminder</option>
                                                    <option value="report">Lab Report Ready</option>
                                                    <option value="follow_up">Follow-up</option>
                                                    <option value="custom">Custom Template</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Original Email (for reply/forward) -->
                                <div id="originalEmailSection" class="card mt-3" style="display: none;">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-reply mr-2"></i>
                                            <span id="originalEmailTitle">Original Email</span>
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="originalEmailContent">
                                            <!-- Original email content will be loaded here -->
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-secondary" onclick="window.location.href='email_inbox.php'">
                                            <i class="fas fa-times mr-1"></i> Cancel
                                        </button>
                                        <button type="button" class="btn btn-info ml-2" onclick="saveDraft()">
                                            <i class="fas fa-save mr-1"></i> Save Draft
                                        </button>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="submit" id="sendEmailBtn" class="btn btn-primary">
                                            <i class="fas fa-paper-plane mr-1"></i> Send Email
                                        </button>
                                        <button type="button" class="btn btn-warning ml-2" onclick="scheduleEmail()">
                                            <i class="fas fa-clock mr-1"></i> Schedule Send
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
// Global variables
let originalEmail = null;
let attachedFiles = [];

// Initialize page
$(document).ready(function() {
    console.log('Initializing Email Compose...');
    
    // Setup event handlers
    setupEventHandlers();
    
    // Check Gmail connection
    checkGmailConnection();
    
    // Load original email if reply/forward
    const urlParams = new URLSearchParams(window.location.search);
    const replyUid = urlParams.get('reply');
    const forwardUid = urlParams.get('forward');
    
    if (replyUid) {
        loadOriginalEmail(replyUid, 'reply');
    } else if (forwardUid) {
        loadOriginalEmail(forwardUid, 'forward');
    }
    
    // Initialize rich text editor
    initializeEditor();
});

// Setup event handlers
function setupEventHandlers() {
    // Form submission
    $('#emailForm').on('submit', function(e) {
        e.preventDefault();
        sendEmail();
    });

    // File attachment handler
    $('#emailAttachments').on('change', function() {
        handleFileSelection(this.files);
    });

    // Template selection
    $('#emailTemplate').on('change', function() {
        loadEmailTemplate($(this).val());
    });

    // Custom file input label update
    $('.custom-file-input').on('change', function() {
        const files = this.files;
        let fileNames = [];
        for (let i = 0; i < files.length; i++) {
            fileNames.push(files[i].name);
        }
        $(this).next('.custom-file-label').text(fileNames.join(', ') || 'Choose files...');
    });
}

// Initialize rich text editor
function initializeEditor() {
    // Simple rich text functionality
    $('#emailBody').on('keydown', function(e) {
        // Ctrl+B for bold
        if (e.ctrlKey && e.keyCode === 66) {
            e.preventDefault();
            document.execCommand('bold');
        }
        // Ctrl+I for italic
        if (e.ctrlKey && e.keyCode === 73) {
            e.preventDefault();
            document.execCommand('italic');
        }
    });
}

// Check Gmail connection
function checkGmailConnection() {
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'GET',
        data: { action: 'status' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                $('#connectionAlert').hide();
            } else {
                $('#connectionAlert').show();
                $('#connectionMessage').text(response.message || 'Gmail connection not configured');
            }
        },
        error: function() {
            $('#connectionAlert').show();
            $('#connectionMessage').text('Unable to check Gmail connection status');
        }
    });
}

// Load original email for reply/forward
function loadOriginalEmail(uid, action) {
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'GET',
        data: { action: 'get', uid: uid },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                originalEmail = response.data;
                populateReplyForward(originalEmail, action);
            } else {
                toastr.error('Failed to load original email');
            }
        },
        error: function() {
            toastr.error('Failed to load original email');
        }
    });
}

// Populate form for reply/forward
function populateReplyForward(email, action) {
    if (action === 'reply') {
        $('#toEmail').val(email.from);
        $('#emailSubject').val('Re: ' + (email.subject || '').replace(/^Re:\s*/i, ''));
        
        // Add original email content
        const originalContent = `\n\n--- Original Message ---\nFrom: ${email.from}\nDate: ${email.date}\nSubject: ${email.subject}\n\n${email.body || ''}`;
        $('#emailBody').val(originalContent);
        
        $('#originalEmailTitle').text('Original Email (Reply)');
    } else if (action === 'forward') {
        $('#emailSubject').val('Fwd: ' + (email.subject || '').replace(/^Fwd:\s*/i, ''));
        
        // Add forwarded email content
        const forwardContent = `\n\n--- Forwarded Message ---\nFrom: ${email.from}\nTo: ${email.to}\nDate: ${email.date}\nSubject: ${email.subject}\n\n${email.body || ''}`;
        $('#emailBody').val(forwardContent);
        
        $('#originalEmailTitle').text('Forwarded Email');
    }
    
    // Show original email section
    $('#originalEmailSection').show();
    $('#originalEmailContent').html(`
        <div class="email-header mb-2">
            <strong>From:</strong> ${escapeHtml(email.from)}<br>
            <strong>Date:</strong> ${formatEmailDate(email.date)}<br>
            <strong>Subject:</strong> ${escapeHtml(email.subject || '(No Subject)')}
        </div>
        <hr>
        <div class="email-content" style="max-height: 200px; overflow-y: auto;">
            ${email.body || '<em>No content</em>'}
        </div>
    `);
}

// Handle file selection
function handleFileSelection(files) {
    attachedFiles = [];
    let html = '';
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        attachedFiles.push(file);
        
        html += `
            <div class="attachment-item d-flex justify-content-between align-items-center p-2 border rounded mb-2">
                <span>
                    <i class="fas fa-file mr-2"></i>
                    ${escapeHtml(file.name)} (${formatFileSize(file.size)})
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAttachment(${i})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }
    
    $('#attachmentsList').html(html);
}

// Remove attachment
function removeAttachment(index) {
    attachedFiles.splice(index, 1);
    
    // Update file input
    const dt = new DataTransfer();
    for (let file of attachedFiles) {
        dt.items.add(file);
    }
    document.getElementById('emailAttachments').files = dt.files;
    
    // Update display
    handleFileSelection(attachedFiles);
}

// Load email template
function loadEmailTemplate(templateType) {
    if (!templateType) return;
    
    const templates = {
        appointment: {
            subject: 'Appointment Reminder - Hospital',
            body: 'Dear Patient,\n\nThis is a reminder for your upcoming appointment.\n\nAppointment Details:\nDate: [DATE]\nTime: [TIME]\nDoctor: [DOCTOR]\n\nPlease arrive 15 minutes early.\n\nBest regards,\nHospital Team'
        },
        report: {
            subject: 'Lab Report Ready - Hospital',
            body: 'Dear Patient,\n\nYour lab report is ready for collection.\n\nReport Details:\nTest Date: [DATE]\nReport ID: [REPORT_ID]\n\nYou can collect your report from the reception or download it from our patient portal.\n\nBest regards,\nLab Department'
        },
        follow_up: {
            subject: 'Follow-up Appointment - Hospital',
            body: 'Dear Patient,\n\nWe hope you are feeling better after your recent visit.\n\nAs discussed, please schedule a follow-up appointment for:\n[FOLLOW_UP_DETAILS]\n\nPlease contact us to schedule your appointment.\n\nBest regards,\nHospital Team'
        }
    };
    
    const template = templates[templateType];
    if (template) {
        $('#emailSubject').val(template.subject);
        $('#emailBody').val(template.body);
    }
}

// Send email
function sendEmail() {
    console.log('Sending email...');
    
    // Validate form
    if (!validateForm()) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'send');
    formData.append('to', $('#toEmail').val());
    formData.append('cc', $('#ccEmail').val());
    formData.append('bcc', $('#bccEmail').val());
    formData.append('subject', $('#emailSubject').val());
    formData.append('body', $('#emailBody').val());
    formData.append('priority', $('#emailPriority').val());
    formData.append('read_receipt', $('#requestReadReceipt').is(':checked') ? '1' : '0');
    formData.append('send_copy', $('#sendCopy').is(':checked') ? '1' : '0');
    formData.append('schedule_date', $('#scheduleDate').val());
    
    // Add attachments
    for (let i = 0; i < attachedFiles.length; i++) {
        formData.append('attachments[]', attachedFiles[i]);
    }
    
    const submitBtn = $('#sendEmailBtn');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Sending...').prop('disabled', true);
    
    $.ajax({
        url: 'ajax/gmail_send_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        timeout: 60000,
        success: function(response) {
            console.log('Send response:', response);
            
            if (response && response.success) {
                toastr.success('Email sent successfully!');
                
                // Redirect to inbox after short delay
                setTimeout(function() {
                    window.location.href = 'email_inbox.php';
                }, 2000);
            } else {
                toastr.error(response.message || 'Failed to send email');
            }
        },
        error: function(xhr, status, error) {
            console.error('Send error:', {xhr, status, error});
            
            let errorMessage = 'Failed to send email';
            
            if (xhr.status === 401) {
                errorMessage = 'Gmail authentication failed. Please check your credentials.';
            } else if (xhr.status === 413) {
                errorMessage = 'Email too large. Please reduce attachment sizes.';
            } else if (status === 'timeout') {
                errorMessage = 'Email sending timed out. Please try again.';
            }
            
            toastr.error(errorMessage);
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

// Save draft
function saveDraft() {
    console.log('Saving draft...');
    
    const draftData = {
        action: 'save_draft',
        to: $('#toEmail').val(),
        cc: $('#ccEmail').val(),
        bcc: $('#bccEmail').val(),
        subject: $('#emailSubject').val(),
        body: $('#emailBody').val(),
        priority: $('#emailPriority').val()
    };
    
    $.ajax({
        url: 'ajax/gmail_send_api.php',
        type: 'POST',
        data: draftData,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Draft saved successfully!');
            } else {
                toastr.error(response.message || 'Failed to save draft');
            }
        },
        error: function() {
            toastr.error('Failed to save draft');
        }
    });
}

// Schedule email
function scheduleEmail() {
    const scheduleDate = $('#scheduleDate').val();
    
    if (!scheduleDate) {
        toastr.error('Please select a date and time to schedule the email');
        return;
    }
    
    const scheduledTime = new Date(scheduleDate);
    const now = new Date();
    
    if (scheduledTime <= now) {
        toastr.error('Scheduled time must be in the future');
        return;
    }
    
    // Set a flag and send the email
    $('#emailForm').data('scheduled', true);
    sendEmail();
}

// Validate form
function validateForm() {
    const to = $('#toEmail').val().trim();
    const subject = $('#emailSubject').val().trim();
    const body = $('#emailBody').val().trim();
    
    if (!to) {
        toastr.error('Please enter recipient email address');
        $('#toEmail').focus();
        return false;
    }
    
    if (!subject) {
        toastr.error('Please enter email subject');
        $('#emailSubject').focus();
        return false;
    }
    
    if (!body) {
        toastr.error('Please enter email message');
        $('#emailBody').focus();
        return false;
    }
    
    // Validate email addresses
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const toEmails = to.split(',').map(e => e.trim());
    
    for (let email of toEmails) {
        if (email && !emailRegex.test(email)) {
            toastr.error(`Invalid email address: ${email}`);
            $('#toEmail').focus();
            return false;
        }
    }
    
    return true;
}

// Utility functions
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatEmailDate(dateString) {
    if (!dateString) return 'Unknown';
    
    try {
        const date = new Date(dateString);
        return date.toLocaleString();
    } catch (error) {
        return 'Invalid Date';
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}
</script>

<style>
.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.attachment-item {
    background-color: #f8f9fa;
}

.email-header {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}

.email-content {
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    background-color: #fff;
}

#emailBody {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.5;
}

.form-group label {
    font-weight: 600;
    color: #495057;
}

.text-danger {
    color: #dc3545 !important;
}

@media (max-width: 768px) {
    .card-footer .row {
        flex-direction: column;
    }
    
    .card-footer .col-md-6 {
        margin-bottom: 10px;
    }
    
    .card-footer .text-right {
        text-align: left !important;
    }
}
</style>

<?php require_once 'inc/footer.php'; ?>