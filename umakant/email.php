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
</div>