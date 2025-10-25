<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';

// Gmail IMAP configuration
$gmail_config = [
    'email' => 'umakant171991@gmail.com',
    'password' => 'jnim iuiy njno pvkt', // This should be set via environment variable or config file
    'imap_server' => 'imap.gmail.com',
    'imap_port' => 993,
    'imap_encryption' => 'ssl'
];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-inbox mr-2"></i>Email Inbox</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Email</a></li>
                        <li class="breadcrumb-item active">Inbox</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Email Stats Row -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalEmails">0</h3>
                            <p>Total Emails</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="unreadEmails">0</h3>
                            <p>Unread Emails</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-envelope-open"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="todayEmails">0</h3>
                            <p>Today's Emails</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="connectionStatus">Disconnected</h3>
                            <p>Gmail Status</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-inbox mr-1"></i>
                                Gmail Inbox - <?php echo htmlspecialchars($gmail_config['email']); ?>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" onclick="refreshEmails()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="window.location.href='email_compose.php'">
                                    <i class="fas fa-plus"></i> Compose
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="setupGmailConnection()">
                                    <i class="fas fa-cog"></i> Setup
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Connection Status Alert -->
                            <div id="connectionAlert" class="alert alert-warning" style="display: none;">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span id="connectionMessage">Gmail connection not configured. Please set up your Gmail credentials.</span>
                            </div>

                            <!-- Email Filters -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-filter mr-2"></i>
                                        Filters
                                        <button class="btn btn-sm btn-outline-secondary float-right" onclick="clearFilters()">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Status</label>
                                            <select id="statusFilter" class="form-control">
                                                <option value="">All Emails</option>
                                                <option value="unread">Unread Only</option>
                                                <option value="read">Read Only</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Date Range</label>
                                            <select id="dateFilter" class="form-control">
                                                <option value="">All Time</option>
                                                <option value="today">Today</option>
                                                <option value="week">This Week</option>
                                                <option value="month">This Month</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Search</label>
                                            <input type="text" id="emailSearch" class="form-control" placeholder="Search emails...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Emails Table -->
                            <div class="table-responsive">
                                <table id="emailsTable" class="table table-striped table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="30"><input type="checkbox" id="selectAll"></th>
                                            <th width="50">Status</th>
                                            <th width="200">From</th>
                                            <th>Subject</th>
                                            <th width="150">Date</th>
                                            <th width="100">Size</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading emails...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Email View Modal -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="emailModalLabel">
                    <i class="fas fa-envelope mr-2"></i>
                    <span id="emailSubject">Email Subject</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="emailBody">
                <!-- Email content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="replyToEmail()">
                    <i class="fas fa-reply mr-1"></i> Reply
                </button>
                <button type="button" class="btn btn-info" onclick="forwardEmail()">
                    <i class="fas fa-share mr-1"></i> Forward
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Gmail Setup Modal -->
<div class="modal fade" id="setupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-cog mr-2"></i>
                    Gmail Setup
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="setupForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Setup Options:</strong><br>
                        1. <strong>App Password (Recommended):</strong> Create an App Password in Google Account Security<br>
                        2. <strong>Regular Password:</strong> Enable "Less secure app access" in Google Account Security<br>
                        3. <strong>OAuth2:</strong> Contact admin for OAuth2 setup
                    </div>
                    
                    <div class="form-group">
                        <label for="gmailEmail">Gmail Address</label>
                        <input type="email" class="form-control" id="gmailEmail" value="<?php echo htmlspecialchars($gmail_config['email']); ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="passwordType">Authentication Type</label>
                        <select class="form-control" id="passwordType">
                            <option value="app">App Password (16 characters, no spaces)</option>
                            <option value="regular">Regular Gmail Password</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="gmailPassword">Password</label>
                        <input type="password" class="form-control" id="gmailPassword" placeholder="Enter your Gmail password">
                        <small class="form-text text-muted">
                            <span id="passwordHelp">Enter your App Password (16 characters without spaces)</span>
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="testConnection">
                            <label class="custom-control-label" for="testConnection">
                                Test connection before saving
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save & Test Connection
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global variables
let emailsData = [];
let currentEmail = null;

// Initialize page
$(document).ready(function() {
    console.log('Initializing Email Inbox...');
    
    // Setup event handlers
    setupEventHandlers();
    
    // Load emails
    loadEmails();
    
    // Check connection status
    checkGmailConnection();
});

// Setup event handlers
function setupEventHandlers() {
    // Search functionality
    $('#emailSearch').on('keyup', function() {
        applyFilters();
    });

    // Filter changes
    $('#statusFilter, #dateFilter').on('change', function() {
        applyFilters();
    });

    // Setup form submission
    $('#setupForm').on('submit', function(e) {
        e.preventDefault();
        saveGmailCredentials();
    });

    // Password type change handler
    $('#passwordType').on('change', function() {
        const type = $(this).val();
        if (type === 'app') {
            $('#passwordHelp').text('Enter your App Password (16 characters without spaces)');
            $('#gmailPassword').attr('placeholder', 'Enter your Gmail App Password');
        } else {
            $('#passwordHelp').text('Enter your regular Gmail password (requires "Less secure app access" enabled)');
            $('#gmailPassword').attr('placeholder', 'Enter your regular Gmail password');
        }
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.email-checkbox').prop('checked', $(this).is(':checked'));
    });
}

// Load emails from Gmail
function loadEmails() {
    console.log('Loading emails from Gmail...');
    
    // Show loading state
    $('#emailsTable tbody').html('<tr><td colspan="7" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading emails...</td></tr>');
    
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        timeout: 30000,
        success: function(response) {
            console.log('Gmail API response:', response);
            
            if (response && response.success === true && Array.isArray(response.data)) {
                emailsData = response.data;
                renderEmailsTable(emailsData);
                updateStats(response.stats || {});
                console.log('Emails loaded successfully, count:', emailsData.length);
            } else {
                console.error('Invalid response format:', response);
                showTableError(response.message || 'Failed to load emails');
            }
        },
        error: function(xhr, status, error) {
            console.error('Gmail API Error:', {xhr, status, error});
            
            let errorMessage = 'Failed to load emails';
            
            if (xhr.status === 0) {
                errorMessage = 'Network connection error. Please check your internet connection.';
            } else if (xhr.status === 401) {
                errorMessage = 'Gmail authentication failed. Please check your credentials.';
                $('#connectionAlert').show();
                $('#connectionMessage').text('Gmail authentication failed. Please update your credentials.');
            } else if (xhr.status === 500) {
                errorMessage = 'Server error occurred. Please try again later.';
            } else if (status === 'timeout') {
                errorMessage = 'Request timed out. Gmail server may be slow.';
            }
            
            showTableError(errorMessage);
        }
    });
}

// Render emails table
function renderEmailsTable(emails) {
    let html = '';
    
    if (!emails || emails.length === 0) {
        html = '<tr><td colspan="7" class="text-center py-4"><i class="fas fa-info-circle text-muted mr-2"></i>No emails found</td></tr>';
    } else {
        emails.forEach(function(email, index) {
            const isUnread = !email.seen;
            const statusIcon = isUnread ? '<i class="fas fa-envelope text-primary" title="Unread"></i>' : '<i class="fas fa-envelope-open text-muted" title="Read"></i>';
            const rowClass = isUnread ? 'font-weight-bold' : '';
            
            html += `
                <tr class="${rowClass}" data-email-id="${email.uid}">
                    <td class="text-center"><input type="checkbox" class="email-checkbox" value="${email.uid}"></td>
                    <td class="text-center">${statusIcon}</td>
                    <td>${escapeHtml(email.from || 'Unknown')}</td>
                    <td>
                        <a href="#" onclick="viewEmail('${email.uid}')" class="text-decoration-none">
                            ${escapeHtml(email.subject || '(No Subject)')}
                        </a>
                        ${email.hasAttachments ? '<i class="fas fa-paperclip ml-2 text-muted" title="Has Attachments"></i>' : ''}
                    </td>
                    <td>${formatEmailDate(email.date)}</td>
                    <td>${formatFileSize(email.size || 0)}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info btn-sm" onclick="viewEmail('${email.uid}')" title="View Email">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="replyToEmail('${email.uid}')" title="Reply">
                                <i class="fas fa-reply"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteEmail('${email.uid}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#emailsTable tbody').html(html);
}

// View email details
function viewEmail(uid) {
    console.log('Viewing email:', uid);
    
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'GET',
        data: { action: 'get', uid: uid },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                const email = response.data;
                currentEmail = email;
                
                $('#emailSubject').text(email.subject || '(No Subject)');
                
                let bodyHtml = `
                    <div class="email-header mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>From:</strong> ${escapeHtml(email.from || 'Unknown')}<br>
                                <strong>To:</strong> ${escapeHtml(email.to || 'Unknown')}<br>
                                ${email.cc ? `<strong>CC:</strong> ${escapeHtml(email.cc)}<br>` : ''}
                            </div>
                            <div class="col-md-6 text-right">
                                <strong>Date:</strong> ${formatEmailDate(email.date)}<br>
                                <strong>Size:</strong> ${formatFileSize(email.size || 0)}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="email-content">
                        ${email.body || '<em>No content available</em>'}
                    </div>
                `;
                
                if (email.attachments && email.attachments.length > 0) {
                    bodyHtml += `
                        <hr>
                        <div class="email-attachments">
                            <h6><i class="fas fa-paperclip mr-2"></i>Attachments</h6>
                            <div class="list-group">
                    `;
                    
                    email.attachments.forEach(function(attachment) {
                        bodyHtml += `
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-file mr-2"></i>
                                    ${escapeHtml(attachment.filename)}
                                </span>
                                <span class="badge badge-primary badge-pill">${formatFileSize(attachment.size)}</span>
                            </div>
                        `;
                    });
                    
                    bodyHtml += `
                            </div>
                        </div>
                    `;
                }
                
                $('#emailBody').html(bodyHtml);
                $('#emailModal').modal('show');
                
                // Mark as read if it was unread
                if (!email.seen) {
                    markAsRead(uid);
                }
            } else {
                toastr.error('Failed to load email details');
            }
        },
        error: function() {
            toastr.error('Failed to load email details');
        }
    });
}

// Update statistics
function updateStats(stats) {
    $('#totalEmails').text(stats.total || 0);
    $('#unreadEmails').text(stats.unread || 0);
    $('#todayEmails').text(stats.today || 0);
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
                $('#connectionStatus').text('Connected').removeClass('bg-danger').addClass('bg-success');
                $('#connectionAlert').hide();
            } else {
                $('#connectionStatus').text('Disconnected').removeClass('bg-success').addClass('bg-danger');
                $('#connectionAlert').show();
                $('#connectionMessage').text(response.message || 'Gmail connection not configured');
            }
        },
        error: function() {
            $('#connectionStatus').text('Error').removeClass('bg-success').addClass('bg-danger');
            $('#connectionAlert').show();
            $('#connectionMessage').text('Unable to check Gmail connection status');
        }
    });
}

// Setup Gmail connection
function setupGmailConnection() {
    $('#setupModal').modal('show');
}

// Save Gmail credentials
function saveGmailCredentials() {
    const password = $('#gmailPassword').val().trim();
    const passwordType = $('#passwordType').val();
    const testConnection = $('#testConnection').is(':checked');
    
    if (!password) {
        toastr.error('Please enter your Gmail password');
        return;
    }
    
    // Validate App Password format
    if (passwordType === 'app') {
        const cleanPassword = password.replace(/\s/g, '');
        if (cleanPassword.length !== 16) {
            toastr.error('App Password should be 16 characters long');
            return;
        }
    }
    
    const submitBtn = $('#setupForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Testing...').prop('disabled', true);
    
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'POST',
        data: {
            action: 'setup',
            password: password,
            password_type: passwordType,
            test_connection: testConnection
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Gmail connection configured successfully!');
                $('#setupModal').modal('hide');
                $('#gmailPassword').val('');
                checkGmailConnection();
                loadEmails();
            } else {
                let errorMsg = response.message || 'Failed to configure Gmail connection';
                
                // Provide specific help based on error
                if (errorMsg.includes('authentication') || errorMsg.includes('login')) {
                    if (passwordType === 'regular') {
                        errorMsg += '<br><br><strong>Try this:</strong><br>1. Enable "Less secure app access" in Google Account Security<br>2. Or use an App Password instead';
                    } else {
                        errorMsg += '<br><br><strong>Try this:</strong><br>1. Generate a new App Password<br>2. Or try your regular password with "Less secure app access" enabled';
                    }
                }
                
                toastr.error(errorMsg, '', {
                    allowHtml: true,
                    timeOut: 10000
                });
            }
        },
        error: function(xhr) {
            let errorMsg = 'Failed to test Gmail connection';
            
            if (xhr.status === 401) {
                errorMsg = 'Authentication failed. Please check your password and Gmail security settings.';
            }
            
            toastr.error(errorMsg);
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

// Refresh emails
function refreshEmails() {
    loadEmails();
}

// Apply filters
function applyFilters() {
    const statusFilter = $('#statusFilter').val();
    const dateFilter = $('#dateFilter').val();
    const searchFilter = $('#emailSearch').val().toLowerCase();
    
    let filteredEmails = emailsData.filter(function(email) {
        // Status filter
        if (statusFilter === 'unread' && email.seen) return false;
        if (statusFilter === 'read' && !email.seen) return false;
        
        // Date filter
        if (dateFilter) {
            const emailDate = new Date(email.date);
            const now = new Date();
            
            if (dateFilter === 'today') {
                if (emailDate.toDateString() !== now.toDateString()) return false;
            } else if (dateFilter === 'week') {
                const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                if (emailDate < weekAgo) return false;
            } else if (dateFilter === 'month') {
                const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                if (emailDate < monthAgo) return false;
            }
        }
        
        // Search filter
        if (searchFilter) {
            const searchText = (email.from + ' ' + email.subject + ' ' + (email.body || '')).toLowerCase();
            if (!searchText.includes(searchFilter)) return false;
        }
        
        return true;
    });
    
    renderEmailsTable(filteredEmails);
}

// Clear filters
function clearFilters() {
    $('#statusFilter').val('');
    $('#dateFilter').val('');
    $('#emailSearch').val('');
    renderEmailsTable(emailsData);
}

// Mark email as read
function markAsRead(uid) {
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'POST',
        data: {
            action: 'mark_read',
            uid: uid
        },
        success: function(response) {
            if (response && response.success) {
                // Update the email in local data
                const emailIndex = emailsData.findIndex(e => e.uid === uid);
                if (emailIndex !== -1) {
                    emailsData[emailIndex].seen = true;
                    renderEmailsTable(emailsData);
                }
            }
        }
    });
}

// Reply to email
function replyToEmail(uid) {
    if (uid) {
        window.location.href = `email_compose.php?reply=${uid}`;
    } else if (currentEmail) {
        window.location.href = `email_compose.php?reply=${currentEmail.uid}`;
    }
}

// Forward email
function forwardEmail() {
    if (currentEmail) {
        window.location.href = `email_compose.php?forward=${currentEmail.uid}`;
    }
}

// Delete email
function deleteEmail(uid) {
    if (confirm('Are you sure you want to delete this email?')) {
        $.ajax({
            url: 'ajax/gmail_api.php',
            type: 'POST',
            data: {
                action: 'delete',
                uid: uid
            },
            success: function(response) {
                if (response && response.success) {
                    toastr.success('Email deleted successfully');
                    loadEmails();
                } else {
                    toastr.error(response.message || 'Failed to delete email');
                }
            },
            error: function() {
                toastr.error('Failed to delete email');
            }
        });
    }
}

// Show table error
function showTableError(message) {
    $('#emailsTable tbody').html(`
        <tr>
            <td colspan="7" class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>${message}
                <br><br>
                <button class="btn btn-primary btn-sm" onclick="refreshEmails()">
                    <i class="fas fa-refresh mr-1"></i>Try Again
                </button>
            </td>
        </tr>
    `);
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
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) {
            return 'Today ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        } else if (diffDays === 2) {
            return 'Yesterday ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        } else if (diffDays <= 7) {
            return date.toLocaleDateString([], {weekday: 'short'}) + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        } else {
            return date.toLocaleDateString();
        }
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
.small-box {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

#emailsTable {
    font-size: 0.9rem;
}

#emailsTable thead th {
    background-color: #343a40;
    color: white;
    border-color: #454d55;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 12px 8px;
}

#emailsTable tbody td {
    vertical-align: middle;
    padding: 10px 8px;
    border-color: #dee2e6;
}

#emailsTable tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.email-header {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
}

.email-content {
    max-height: 400px;
    overflow-y: auto;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 5px;
}

.email-attachments {
    margin-top: 15px;
}

@media (max-width: 768px) {
    #emailsTable {
        font-size: 0.8rem;
    }
    
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
}
</style>

<?php require_once 'inc/footer.php'; ?>