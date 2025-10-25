<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-bug mr-2"></i>Email Debug & Test</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="email_inbox.php">Email</a></li>
                        <li class="breadcrumb-item active">Debug</li>
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
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Email Troubleshooting
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                This page will help diagnose email sending issues. Click the tests below to check different aspects of your email configuration.
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Quick Tests</h5>
                                        </div>
                                        <div class="card-body">
                                            <button class="btn btn-primary btn-block mb-2" onclick="testGmailCredentials()">
                                                <i class="fas fa-key mr-2"></i>Test Gmail Credentials
                                            </button>
                                            <button class="btn btn-info btn-block mb-2" onclick="testSMTPConnection()">
                                                <i class="fas fa-plug mr-2"></i>Test SMTP Connection
                                            </button>
                                            <button class="btn btn-success btn-block mb-2" onclick="sendTestEmail()">
                                                <i class="fas fa-paper-plane mr-2"></i>Send Test Email
                                            </button>
                                            <button class="btn btn-warning btn-block mb-2" onclick="checkServerConfig()">
                                                <i class="fas fa-server mr-2"></i>Check Server Config
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Test Results</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="testResults" style="min-height: 200px;">
                                                <p class="text-muted">Click a test button to see results here...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Common Solutions</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-shield-alt text-warning mr-2"></i>Gmail Security Issues</h6>
                                                    <ul>
                                                        <li>Make sure you're using an <strong>App Password</strong>, not your regular Gmail password</li>
                                                        <li>Enable <strong>2-Step Verification</strong> in your Google Account</li>
                                                        <li>Generate a new App Password if the current one isn't working</li>
                                                        <li>Check if "Less secure app access" is enabled (if not using App Password)</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-server text-danger mr-2"></i>Server Configuration Issues</h6>
                                                    <ul>
                                                        <li>Your hosting provider might block outbound SMTP connections</li>
                                                        <li>Port 587 (SMTP) might be blocked by firewall</li>
                                                        <li>PHP mail() function might be disabled</li>
                                                        <li>OpenSSL extension might not be installed</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-network-wired text-info mr-2"></i>Network Issues</h6>
                                                    <ul>
                                                        <li>Check if your server can reach smtp.gmail.com:587</li>
                                                        <li>Verify DNS resolution for Gmail servers</li>
                                                        <li>Test from command line: <code>telnet smtp.gmail.com 587</code></li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-tools text-success mr-2"></i>Alternative Solutions</h6>
                                                    <ul>
                                                        <li>Use a different SMTP service (SendGrid, Mailgun)</li>
                                                        <li>Configure local mail server (Postfix, Sendmail)</li>
                                                        <li>Use email API services instead of SMTP</li>
                                                        <li>Contact your hosting provider for SMTP support</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function testGmailCredentials() {
    updateResults('<i class="fas fa-spinner fa-spin mr-2"></i>Testing Gmail credentials...');
    
    $.ajax({
        url: 'ajax/gmail_api.php',
        type: 'GET',
        data: { action: 'status' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                updateResults('<div class="alert alert-success"><i class="fas fa-check mr-2"></i><strong>Gmail Credentials:</strong> ✅ Valid and configured</div>');
            } else {
                updateResults('<div class="alert alert-danger"><i class="fas fa-times mr-2"></i><strong>Gmail Credentials:</strong> ❌ Not configured or invalid<br><small>' + (response.message || 'Unknown error') + '</small></div>');
            }
        },
        error: function() {
            updateResults('<div class="alert alert-danger"><i class="fas fa-times mr-2"></i><strong>Gmail Credentials:</strong> ❌ Error checking credentials</div>');
        }
    });
}

function testSMTPConnection() {
    updateResults('<i class="fas fa-spinner fa-spin mr-2"></i>Testing SMTP connection...');
    
    $.ajax({
        url: 'ajax/email_debug_api.php',
        type: 'POST',
        data: { action: 'test_smtp' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                updateResults('<div class="alert alert-success"><i class="fas fa-check mr-2"></i><strong>SMTP Connection:</strong> ✅ Connection successful<br><small>' + response.message + '</small></div>');
            } else {
                updateResults('<div class="alert alert-danger"><i class="fas fa-times mr-2"></i><strong>SMTP Connection:</strong> ❌ Connection failed<br><small>' + (response.message || 'Unknown error') + '</small></div>');
            }
        },
        error: function() {
            updateResults('<div class="alert alert-danger"><i class="fas fa-times mr-2"></i><strong>SMTP Connection:</strong> ❌ Error testing connection</div>');
        }
    });
}

function sendTestEmail() {
    updateResults('<i class="fas fa-spinner fa-spin mr-2"></i>Sending test email...');
    
    $.ajax({
        url: 'ajax/gmail_send_api.php',
        type: 'POST',
        data: {
            action: 'send',
            to: 'umakant171991@gmail.com',
            subject: 'Test Email from Hospital System - ' + new Date().toLocaleString(),
            body: 'This is a test email to verify the email sending functionality.<br><br>Sent at: ' + new Date().toLocaleString(),
            priority: 'normal'
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                updateResults('<div class="alert alert-success"><i class="fas fa-check mr-2"></i><strong>Test Email:</strong> ✅ Email sent successfully!<br><small>Check your inbox for the test email.</small></div>');
            } else {
                updateResults('<div class="alert alert-danger"><i class="fas fa-times mr-2"></i><strong>Test Email:</strong> ❌ Failed to send<br><small>' + (response.message || 'Unknown error') + '</small></div>');
            }
        },
        error: function(xhr) {
            let errorMsg = 'Unknown error';
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                errorMsg = errorResponse.message || errorMsg;
            } catch (e) {
                errorMsg = xhr.statusText || errorMsg;
            }
            updateResults('<div class="alert alert-danger"><i class="fas fa-times mr-2"></i><strong>Test Email:</strong> ❌ Request failed<br><small>' + errorMsg + '</small></div>');
        }
    });
}

function checkServerConfig() {
    updateResults('<i class="fas fa-spinner fa-spin mr-2"></i>Checking server configuration...');
    
    $.ajax({
        url: 'ajax/email_debug_api.php',
        type: 'POST',
        data: { action: 'check_config' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                let html = '<div class="alert alert-info"><i class="fas fa-info mr-2"></i><strong>Server Configuration:</strong><br>';
                html += '<small>';
                if (response.data) {
                    for (let key in response.data) {
                        html += '<strong>' + key + ':</strong> ' + response.data[key] + '<br>';
                    }
                }
                html += '</small></div>';
                updateResults(html);
            } else {
                updateResults('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i><strong>Server Configuration:</strong> ⚠️ Could not retrieve all information<br><small>' + (response.message || 'Unknown error') + '</small></div>');
            }
        },
        error: function() {
            updateResults('<div class="alert alert-danger"><i class="fas fa-times mr-2"></i><strong>Server Configuration:</strong> ❌ Error checking configuration</div>');
        }
    });
}

function updateResults(html) {
    $('#testResults').html(html);
}
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

#testResults {
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
}
</style>

<?php require_once 'inc/footer.php'; ?>