<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';

// Gmail configuration
$gmail_config = [
    'email' => 'umakant171991@gmail.com'
];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-paper-plane mr-2"></i>Sent Emails</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="email_inbox.php">Email</a></li>
                        <li class="breadcrumb-item active">Sent</li>
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
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="totalSent">0</h3>
                            <p>Total Sent</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="todaySent">0</h3>
                            <p>Sent Today</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="thisWeekSent">0</h3>
                            <p>This Week</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="scheduledEmails">0</h3>
                            <p>Scheduled</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-paper-plane mr-1"></i>
                                Sent Emails - <?php echo htmlspecialchars($gmail_config['email']); ?>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" onclick="window.location.href='email_compose.php'">
                                    <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Compose New</span>
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="refreshSentEmails()">
                                    <i class="fas fa-sync-alt"></i> <span class="d-none d-sm-inline">Refresh</span>
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="window.location.href='email_inbox.php'">
                                    <i class="fas fa-inbox"></i> <span class="d-none d-sm-inline">Inbox</span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
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
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label">Date Range</label>
                                            <select id="dateFilter" class="form-control">
                                                <option value="">All Time</option>
                                                <option value="today">Today</option>
                                                <option value="week">This Week</option>
                                                <option value="month">This Month</option>
                                                <option value="custom">Custom Range</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label">Priority</label>
                                            <select id="priorityFilter" class="form-control">
                                                <option value="">All Priorities</option>
                                                <option value="high">High Priority</option>
                                                <option value="normal">Normal Priority</option>
                                                <option value="low">Low Priority</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-sm-12">
                                            <label class="form-label">Search</label>
                                            <input type="text" id="emailSearch" class="form-control" placeholder="Search recipients, subject, or content...">
                                        </div>
                                    </div>
                                    <div class="row mt-2" id="customDateRange" style="display: none;">
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label">From Date</label>
                                            <input type="date" id="fromDate" class="form-control">
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <label class="form-label">To Date</label>
                                            <input type="date" id="toDate" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sent Emails Table -->
                            <div class="table-responsive">
                                <table id="sentEmailsTable" class="table table-striped table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="30"><input type="checkbox" id="selectAll"></th>
                                            <th width="200">To</th>
                                            <th>Subject</th>
                                            <th width="100">Priority</th>
                                            <th width="150">Sent Date</th>
                                            <th width="100">Status</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading sent emails...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Bulk Actions -->
                            <div class="card mt-3" id="bulkActionsCard" style="display: none;">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-tasks mr-2"></i>
                                        Bulk Actions
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedEmails()">
                                        <i class="fas fa-trash mr-1"></i> Delete Selected
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="exportSelectedEmails()">
                                        <i class="fas fa-download mr-1"></i> Export Selected
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scheduled Emails Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clock mr-1"></i>
                                Scheduled Emails
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-warning btn-sm" onclick="refreshScheduledEmails()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="scheduledEmailsTable" class="table table-striped table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="200">To</th>
                                            <th>Subject</th>
                                            <th width="150">Scheduled For</th>
                                            <th width="100">Status</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading scheduled emails...
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
<div class="modal fade" id="emailViewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="emailViewModalLabel">
                    <i class="fas fa-envelope mr-2"></i>
                    <span id="viewEmailSubject">Email Details</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="emailViewBody">
                <!-- Email content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="forwardSentEmail()">
                    <i class="fas fa-share mr-1"></i> Forward Again
                </button>
                <button type="button" class="btn btn-info" onclick="resendEmail()">
                    <i class="fas fa-redo mr-1"></i> Send Again
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let sentEmailsData = [];
let scheduledEmailsData = [];
let currentViewEmail = null;

// Initialize page
$(document).ready(function() {
    console.log('Initializing Sent Emails page...');
    
    // Initialize AdminLTE components
    if (typeof $.AdminLTE !== 'undefined') {
        console.log('AdminLTE detected, initializing components...');
    } else {
        console.log('AdminLTE not found, checking for alternative initialization...');
    }
    
    // Fallback sidebar toggle functionality
    initializeSidebarToggle();
    
    // Setup event handlers
    setupEventHandlers();
    
    // Load sent emails
    loadSentEmails();
    
    // Load scheduled emails
    loadScheduledEmails();
    
    // Load statistics
    loadSentEmailStats();
});

// Fallback sidebar toggle functionality
function initializeSidebarToggle() {
    // Handle sidebar toggle button
    $('[data-widget="pushmenu"]').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const body = $('body');
        const sidebar = $('.main-sidebar');
        
        if (body.hasClass('sidebar-collapse')) {
            body.removeClass('sidebar-collapse').addClass('sidebar-open');
            sidebar.removeClass('sidebar-collapse').addClass('sidebar-open');
        } else {
            body.removeClass('sidebar-open').addClass('sidebar-collapse');
            sidebar.removeClass('sidebar-open').addClass('sidebar-collapse');
        }
        
        console.log('Sidebar toggle clicked');
    });
    
    // Handle window resize for responsive sidebar
    $(window).on('resize', function() {
        const width = $(window).width();
        const body = $('body');
        
        if (width <= 991) {
            body.addClass('sidebar-collapse');
        } else {
            body.removeClass('sidebar-collapse');
        }
    });
}

// Setup event handlers
function setupEventHandlers() {
    // Search functionality
    $('#emailSearch').on('keyup', function() {
        applyFilters();
    });

    // Filter changes
    $('#dateFilter, #priorityFilter').on('change', function() {
        if ($('#dateFilter').val() === 'custom') {
            $('#customDateRange').show();
        } else {
            $('#customDateRange').hide();
        }
        applyFilters();
    });

    // Custom date range
    $('#fromDate, #toDate').on('change', function() {
        applyFilters();
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.email-checkbox').prop('checked', $(this).is(':checked'));
        toggleBulkActions();
    });

    // Individual checkboxes
    $(document).on('change', '.email-checkbox', function() {
        const totalCheckboxes = $('.email-checkbox').length;
        const checkedCheckboxes = $('.email-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#selectAll').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#selectAll').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#selectAll').prop('indeterminate', true);
        }
        
        toggleBulkActions();
    });
}

// Load sent emails
function loadSentEmails() {
    console.log('Loading sent emails...');
    
    // Show loading state
    $('#sentEmailsTable tbody').html('<tr><td colspan="7" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading sent emails...</td></tr>');
    
    $.ajax({
        url: 'ajax/sent_emails_api.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            console.log('Sent emails response:', response);
            
            if (response && response.success === true && Array.isArray(response.data)) {
                sentEmailsData = response.data;
                renderSentEmailsTable(sentEmailsData);
                console.log('Sent emails loaded successfully, count:', sentEmailsData.length);
            } else {
                console.error('Invalid response format:', response);
                showTableError('sentEmailsTable', response.message || 'Failed to load sent emails');
            }
        },
        error: function(xhr, status, error) {
            console.error('Sent emails API Error:', {xhr, status, error});
            
            let errorMessage = 'Failed to load sent emails';
            
            if (xhr.status === 0) {
                errorMessage = 'Network connection error. Please check your internet connection.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error occurred. Please try again later.';
            } else if (status === 'timeout') {
                errorMessage = 'Request timed out. Please try again.';
            }
            
            showTableError('sentEmailsTable', errorMessage);
        }
    });
}

// Load scheduled emails
function loadScheduledEmails() {
    console.log('Loading scheduled emails...');
    
    // Show loading state
    $('#scheduledEmailsTable tbody').html('<tr><td colspan="5" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading scheduled emails...</td></tr>');
    
    $.ajax({
        url: 'ajax/sent_emails_api.php',
        type: 'GET',
        data: { action: 'scheduled' },
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            console.log('Scheduled emails response:', response);
            
            if (response && response.success === true && Array.isArray(response.data)) {
                scheduledEmailsData = response.data;
                renderScheduledEmailsTable(scheduledEmailsData);
                console.log('Scheduled emails loaded successfully, count:', scheduledEmailsData.length);
            } else {
                console.error('Invalid response format:', response);
                showTableError('scheduledEmailsTable', response.message || 'Failed to load scheduled emails');
            }
        },
        error: function(xhr, status, error) {
            console.error('Scheduled emails API Error:', {xhr, status, error});
            showTableError('scheduledEmailsTable', 'Failed to load scheduled emails');
        }
    });
}

// Render sent emails table
function renderSentEmailsTable(emails) {
    let html = '';
    
    if (!emails || emails.length === 0) {
        html = '<tr><td colspan="7" class="text-center py-4"><i class="fas fa-info-circle text-muted mr-2"></i>No sent emails found</td></tr>';
    } else {
        emails.forEach(function(email, index) {
            const priorityBadge = getPriorityBadge(email.priority);
            const statusBadge = '<span class="badge badge-success">Sent</span>';
            
            html += `
                <tr data-email-id="${email.id}">
                    <td class="text-center"><input type="checkbox" class="email-checkbox" value="${email.id}"></td>
                    <td>
                        <div class="text-truncate" style="max-width: 180px;" title="${escapeHtml(email.to_email)}">
                            ${escapeHtml(email.to_email)}
                        </div>
                        ${email.cc_email ? `<small class="text-muted">CC: ${escapeHtml(email.cc_email.substring(0, 30))}${email.cc_email.length > 30 ? '...' : ''}</small>` : ''}
                    </td>
                    <td>
                        <a href="#" onclick="viewSentEmail(${email.id})" class="text-decoration-none">
                            <strong>${escapeHtml(email.subject || '(No Subject)')}</strong>
                        </a>
                        <br><small class="text-muted">${escapeHtml((email.body || '').substring(0, 50))}${(email.body || '').length > 50 ? '...' : ''}</small>
                    </td>
                    <td class="text-center">${priorityBadge}</td>
                    <td class="text-center">${formatEmailDate(email.sent_at)}</td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info btn-sm" onclick="viewSentEmail(${email.id})" title="View Email">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="resendEmail(${email.id})" title="Send Again">
                                <i class="fas fa-redo"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteSentEmail(${email.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#sentEmailsTable tbody').html(html);
}

// Render scheduled emails table
function renderScheduledEmailsTable(emails) {
    let html = '';
    
    if (!emails || emails.length === 0) {
        html = '<tr><td colspan="5" class="text-center py-4"><i class="fas fa-info-circle text-muted mr-2"></i>No scheduled emails found</td></tr>';
    } else {
        emails.forEach(function(email, index) {
            const statusBadge = getScheduledStatusBadge(email.status, email.schedule_date);
            
            html += `
                <tr data-email-id="${email.id}">
                    <td>
                        <div class="text-truncate" style="max-width: 180px;" title="${escapeHtml(email.to_email)}">
                            ${escapeHtml(email.to_email)}
                        </div>
                    </td>
                    <td>
                        <strong>${escapeHtml(email.subject || '(No Subject)')}</strong>
                        <br><small class="text-muted">${escapeHtml((email.body || '').substring(0, 50))}${(email.body || '').length > 50 ? '...' : ''}</small>
                    </td>
                    <td class="text-center">${formatEmailDate(email.schedule_date)}</td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info btn-sm" onclick="viewScheduledEmail(${email.id})" title="View Email">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${email.status === 'pending' ? `
                                <button class="btn btn-outline-warning btn-sm" onclick="editScheduledEmail(${email.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="cancelScheduledEmail(${email.id})" title="Cancel">
                                    <i class="fas fa-times"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#scheduledEmailsTable tbody').html(html);
}

// Load sent email statistics
function loadSentEmailStats() {
    $.ajax({
        url: 'ajax/sent_emails_api.php',
        type: 'GET',
        data: { action: 'stats' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                $('#totalSent').text(response.data.total || 0);
                $('#todaySent').text(response.data.today || 0);
                $('#thisWeekSent').text(response.data.week || 0);
                $('#scheduledEmails').text(response.data.scheduled || 0);
            }
        },
        error: function() {
            console.error('Failed to load sent email statistics');
        }
    });
}

// View sent email
function viewSentEmail(id) {
    const email = sentEmailsData.find(e => e.id === id);
    if (!email) {
        toastr.error('Email not found');
        return;
    }
    
    currentViewEmail = email;
    
    $('#viewEmailSubject').text(email.subject || '(No Subject)');
    
    let bodyHtml = `
        <div class="email-header mb-3">
            <div class="row">
                <div class="col-md-6">
                    <strong>To:</strong> ${escapeHtml(email.to_email)}<br>
                    ${email.cc_email ? `<strong>CC:</strong> ${escapeHtml(email.cc_email)}<br>` : ''}
                    ${email.bcc_email ? `<strong>BCC:</strong> ${escapeHtml(email.bcc_email)}<br>` : ''}
                </div>
                <div class="col-md-6 text-right">
                    <strong>Sent:</strong> ${formatEmailDate(email.sent_at)}<br>
                    <strong>Priority:</strong> ${getPriorityBadge(email.priority)}
                </div>
            </div>
        </div>
        <hr>
        <div class="email-content">
            ${email.body || '<em>No content available</em>'}
        </div>
    `;
    
    $('#emailViewBody').html(bodyHtml);
    $('#emailViewModal').modal('show');
}

// Apply filters
function applyFilters() {
    const dateFilter = $('#dateFilter').val();
    const priorityFilter = $('#priorityFilter').val();
    const searchFilter = $('#emailSearch').val().toLowerCase();
    const fromDate = $('#fromDate').val();
    const toDate = $('#toDate').val();
    
    let filteredEmails = sentEmailsData.filter(function(email) {
        // Date filter
        if (dateFilter) {
            const emailDate = new Date(email.sent_at);
            const now = new Date();
            
            if (dateFilter === 'today') {
                if (emailDate.toDateString() !== now.toDateString()) return false;
            } else if (dateFilter === 'week') {
                const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                if (emailDate < weekAgo) return false;
            } else if (dateFilter === 'month') {
                const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                if (emailDate < monthAgo) return false;
            } else if (dateFilter === 'custom') {
                if (fromDate && emailDate < new Date(fromDate)) return false;
                if (toDate && emailDate > new Date(toDate + ' 23:59:59')) return false;
            }
        }
        
        // Priority filter
        if (priorityFilter && email.priority !== priorityFilter) return false;
        
        // Search filter
        if (searchFilter) {
            const searchText = (email.to_email + ' ' + email.subject + ' ' + (email.body || '')).toLowerCase();
            if (!searchText.includes(searchFilter)) return false;
        }
        
        return true;
    });
    
    renderSentEmailsTable(filteredEmails);
}

// Clear filters
function clearFilters() {
    $('#dateFilter').val('');
    $('#priorityFilter').val('');
    $('#emailSearch').val('');
    $('#fromDate').val('');
    $('#toDate').val('');
    $('#customDateRange').hide();
    renderSentEmailsTable(sentEmailsData);
}

// Toggle bulk actions
function toggleBulkActions() {
    const checkedCount = $('.email-checkbox:checked').length;
    if (checkedCount > 0) {
        $('#bulkActionsCard').show();
    } else {
        $('#bulkActionsCard').hide();
    }
}

// Refresh functions
function refreshSentEmails() {
    loadSentEmails();
    loadSentEmailStats();
}

function refreshScheduledEmails() {
    loadScheduledEmails();
}

// Utility functions
function getPriorityBadge(priority) {
    switch (priority) {
        case 'high':
            return '<span class="badge badge-danger">High</span>';
        case 'low':
            return '<span class="badge badge-secondary">Low</span>';
        default:
            return '<span class="badge badge-primary">Normal</span>';
    }
}

function getScheduledStatusBadge(status, scheduleDate) {
    const now = new Date();
    const scheduled = new Date(scheduleDate);
    
    switch (status) {
        case 'sent':
            return '<span class="badge badge-success">Sent</span>';
        case 'failed':
            return '<span class="badge badge-danger">Failed</span>';
        case 'pending':
            if (scheduled <= now) {
                return '<span class="badge badge-warning">Processing</span>';
            } else {
                return '<span class="badge badge-info">Scheduled</span>';
            }
        default:
            return '<span class="badge badge-secondary">Unknown</span>';
    }
}

function showTableError(tableId, message) {
    const colspan = tableId === 'sentEmailsTable' ? 7 : 5;
    $(`#${tableId} tbody`).html(`
        <tr>
            <td colspan="${colspan}" class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>${message}
                <br><br>
                <button class="btn btn-primary btn-sm" onclick="${tableId === 'sentEmailsTable' ? 'refreshSentEmails' : 'refreshScheduledEmails'}()">
                    <i class="fas fa-refresh mr-1"></i>Try Again
                </button>
            </td>
        </tr>
    `);
}

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

// Action functions (to be implemented)
function resendEmail(id) {
    const email = sentEmailsData.find(e => e.id === id);
    if (email) {
        const params = new URLSearchParams({
            to: email.to_email,
            subject: email.subject,
            body: email.body
        });
        window.location.href = `email_compose.php?${params.toString()}`;
    }
}

function deleteSentEmail(id) {
    if (confirm('Are you sure you want to delete this sent email record?')) {
        $.ajax({
            url: 'ajax/sent_emails_api.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                if (response && response.success) {
                    toastr.success('Email record deleted successfully');
                    refreshSentEmails();
                } else {
                    toastr.error(response.message || 'Failed to delete email record');
                }
            },
            error: function() {
                toastr.error('Failed to delete email record');
            }
        });
    }
}

function forwardSentEmail() {
    if (currentViewEmail) {
        const params = new URLSearchParams({
            subject: 'Fwd: ' + currentViewEmail.subject,
            body: `\n\n--- Forwarded Message ---\nTo: ${currentViewEmail.to_email}\nSent: ${currentViewEmail.sent_at}\nSubject: ${currentViewEmail.subject}\n\n${currentViewEmail.body}`
        });
        window.location.href = `email_compose.php?${params.toString()}`;
    }
}

function deleteSelectedEmails() {
    const selectedIds = $('.email-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        toastr.warning('Please select emails to delete');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedIds.length} email record(s)?`)) {
        $.ajax({
            url: 'ajax/sent_emails_api.php',
            type: 'POST',
            data: { action: 'bulk_delete', ids: selectedIds },
            success: function(response) {
                if (response && response.success) {
                    toastr.success('Selected email records deleted successfully');
                    refreshSentEmails();
                } else {
                    toastr.error(response.message || 'Failed to delete email records');
                }
            },
            error: function() {
                toastr.error('Failed to delete email records');
            }
        });
    }
}

function exportSelectedEmails() {
    const selectedIds = $('.email-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        toastr.warning('Please select emails to export');
        return;
    }
    
    window.open(`ajax/sent_emails_api.php?action=export&ids=${selectedIds.join(',')}`);
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

.card-outline.card-success {
    border-top: 3px solid #28a745;
}

.card-outline.card-warning {
    border-top: 3px solid #ffc107;
}

#sentEmailsTable, #scheduledEmailsTable {
    font-size: 0.9rem;
}

#sentEmailsTable thead th, #scheduledEmailsTable thead th {
    background-color: #343a40;
    color: white;
    border-color: #454d55;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 12px 8px;
}

#sentEmailsTable tbody td, #scheduledEmailsTable tbody td {
    vertical-align: middle;
    padding: 10px 8px;
    border-color: #dee2e6;
}

#sentEmailsTable tbody tr:hover, #scheduledEmailsTable tbody tr:hover {
    background-color: rgba(40,167,69,0.05);
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

@media (max-width: 768px) {
    #sentEmailsTable, #scheduledEmailsTable {
        font-size: 0.8rem;
    }
    
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
}

<?php require_once 'inc/footer.php'; ?>