<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-robot mr-2"></i>Email Parser Settings</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="inventory_dashboard.php">Inventory</a></li>
                        <li class="breadcrumb-item active">Email Parser</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalProcessed">0</h3>
                            <p>Total Processed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-envelope-open"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="incomeCreated">0</h3>
                            <p>Income Created</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="expenseCreated">0</h3>
                            <p>Expense Created</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="lastRun">Never</h3>
                            <p>Last Run</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Cron Job Setup -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog mr-2"></i>
                                Cron Job Setup
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Setup Instructions</h5>
                                <p>Add this line to your crontab to run the email parser every 5 minutes:</p>
                                <pre class="bg-dark text-white p-2 rounded">*/5 * * * * php <?php echo realpath(__DIR__); ?>/cron_email_parser.php</pre>
                                
                                <p class="mt-3">Or use the web URL (with secret key):</p>
                                <pre class="bg-dark text-white p-2 rounded">*/5 * * * * curl "<?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']); ?>/cron_email_parser.php?cron_key=your_secret_cron_key_12345"</pre>
                                
                                <p class="mt-3"><strong>Note:</strong> Change the secret key in cron_email_parser.php for security!</p>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-success btn-block" onclick="runParserNow()">
                                    <i class="fas fa-play mr-2"></i>
                                    Run Parser Now (Manual)
                                </button>
                            </div>

                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-block" onclick="testEmailParsing()">
                                    <i class="fas fa-vial mr-2"></i>
                                    Test Email Parsing
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Parser Configuration -->
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-sliders-h mr-2"></i>
                                Parser Configuration
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Gmail Password Status</label>
                                <div id="passwordStatus" class="alert alert-secondary">
                                    <i class="fas fa-spinner fa-spin"></i> Checking...
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="gmailPassword">Update Gmail App Password</label>
                                <input type="password" class="form-control" id="gmailPassword" placeholder="Enter Gmail App Password">
                                <small class="form-text text-muted">
                                    Get your App Password from: 
                                    <a href="https://myaccount.google.com/apppasswords" target="_blank">Google Account Settings</a>
                                </small>
                            </div>

                            <button type="button" class="btn btn-warning" onclick="saveGmailPassword()">
                                <i class="fas fa-save mr-2"></i>
                                Save Password
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Logs and History -->
                <div class="col-md-6">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-2"></i>
                                Processing Logs
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" onclick="refreshLogs()">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            <pre id="logContent" class="bg-dark text-white p-3 rounded" style="font-size: 12px;">
Loading logs...
                            </pre>
                        </div>
                    </div>

                    <!-- Processed Emails -->
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                Recently Processed Emails
                            </h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Message ID</th>
                                    </tr>
                                </thead>
                                <tbody id="processedEmailsTable">
                                    <tr>
                                        <td colspan="3" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    loadStats();
    checkPasswordStatus();
    loadProcessedEmails();
    refreshLogs();
    
    // Auto-refresh every 30 seconds
    setInterval(function() {
        loadStats();
        loadProcessedEmails();
    }, 30000);
});

function loadStats() {
    $.ajax({
        url: 'ajax/email_parser_api.php',
        type: 'GET',
        data: { action: 'get_stats' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                $('#totalProcessed').text(response.data.total_processed || 0);
                $('#incomeCreated').text(response.data.income_count || 0);
                $('#expenseCreated').text(response.data.expense_count || 0);
                $('#lastRun').text(response.data.last_run || 'Never');
            }
        }
    });
}

function checkPasswordStatus() {
    $.ajax({
        url: 'ajax/email_parser_api.php',
        type: 'GET',
        data: { action: 'check_password' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                if (response.data.configured) {
                    $('#passwordStatus').removeClass('alert-secondary alert-danger').addClass('alert-success')
                        .html('<i class="fas fa-check-circle"></i> Gmail password is configured');
                } else {
                    $('#passwordStatus').removeClass('alert-secondary alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-triangle"></i> Gmail password not configured');
                }
            }
        }
    });
}

function saveGmailPassword() {
    const password = $('#gmailPassword').val().trim();
    
    if (!password) {
        toastr.error('Please enter Gmail App Password');
        return;
    }
    
    $.ajax({
        url: 'ajax/email_parser_api.php',
        type: 'POST',
        data: { 
            action: 'save_password',
            password: password
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Gmail password saved successfully');
                $('#gmailPassword').val('');
                checkPasswordStatus();
            } else {
                toastr.error(response.message || 'Failed to save password');
            }
        },
        error: function() {
            toastr.error('An error occurred while saving password');
        }
    });
}

function runParserNow() {
    if (!confirm('This will manually run the email parser. Continue?')) {
        return;
    }
    
    toastr.info('Starting email parser... This may take a minute.');
    
    $.ajax({
        url: 'ajax/email_parser_api.php',
        type: 'POST',
        data: { action: 'run_parser' },
        dataType: 'json',
        timeout: 120000, // 2 minutes
        success: function(response) {
            if (response && response.success) {
                let message = response.message || 'Parser completed successfully';
                if (response.summary) {
                    message += '\n\n' + response.summary;
                }
                toastr.success(message);
                
                // Show detailed output in logs
                if (response.output) {
                    $('#logContent').text(response.output);
                }
                
                loadStats();
                loadProcessedEmails();
                refreshLogs();
            } else {
                toastr.error(response.message || 'Parser failed');
            }
        },
        error: function(xhr, status, error) {
            let errorMsg = 'An error occurred while running parser';
            if (xhr.responseText) {
                errorMsg += ': ' + xhr.responseText.substring(0, 200);
            }
            toastr.error(errorMsg);
        }
    });
}

function testEmailParsing() {
    toastr.info('Testing email parsing...');
    
    $.ajax({
        url: 'ajax/email_parser_api.php',
        type: 'POST',
        data: { action: 'test_parser' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                alert('Test Results:\n\n' + JSON.stringify(response.data, null, 2));
            } else {
                toastr.error(response.message || 'Test failed');
            }
        }
    });
}

function loadProcessedEmails() {
    $.ajax({
        url: 'ajax/email_parser_api.php',
        type: 'GET',
        data: { action: 'get_processed_emails', limit: 10 },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayProcessedEmails(response.data);
            }
        }
    });
}

function displayProcessedEmails(emails) {
    const tbody = $('#processedEmailsTable');
    tbody.empty();
    
    if (!emails || emails.length === 0) {
        tbody.append('<tr><td colspan="3" class="text-center">No processed emails</td></tr>');
        return;
    }
    
    emails.forEach(function(email) {
        const typeClass = email.transaction_type === 'income' ? 'badge-success' : 'badge-danger';
        const row = `
            <tr>
                <td>${email.processed_at}</td>
                <td><span class="badge ${typeClass}">${email.transaction_type.toUpperCase()}</span></td>
                <td><small>${email.message_id.substring(0, 30)}...</small></td>
            </tr>
        `;
        tbody.append(row);
    });
}

function refreshLogs() {
    $.ajax({
        url: 'ajax/email_parser_api.php',
        type: 'GET',
        data: { action: 'get_logs', lines: 50 },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                $('#logContent').text(response.data.logs || 'No logs available');
            }
        }
    });
}
</script>

<?php require_once 'inc/footer.php'; ?>
