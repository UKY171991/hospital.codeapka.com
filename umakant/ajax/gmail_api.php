<?php
// ajax/gmail_api.php - Gmail IMAP API for retrieving emails
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../inc/connection.php';
    require_once __DIR__ . '/../inc/ajax_helpers.php';
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => 'Database connection error: ' . $e->getMessage()
    ], 500);
    exit;
}

session_start();

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    json_response(['success' => false, 'message' => 'Authentication required'], 401);
    exit;
}

// Gmail configuration
$gmail_config = [
    'email' => 'umakant171991@gmail.com',
    'imap_server' => 'imap.gmail.com',
    'imap_port' => 993,
    'imap_encryption' => 'ssl'
];

$action = $_REQUEST['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            handleEmailList();
            break;
        case 'get':
            handleGetEmail();
            break;
        case 'setup':
            handleSetupCredentials();
            break;
        case 'status':
            handleConnectionStatus();
            break;
        case 'mark_read':
            handleMarkAsRead();
            break;
        case 'delete':
            handleDeleteEmail();
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    error_log("Gmail API Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
}

function handleEmailList() {
    global $gmail_config;
    
    // Get stored password
    $password = getStoredGmailPassword();
    if (!$password) {
        json_response([
            'success' => false,
            'message' => 'Gmail credentials not configured. Please set up your Gmail App Password.',
            'data' => [],
            'stats' => ['total' => 0, 'unread' => 0, 'today' => 0]
        ]);
        return;
    }
    
    // Connect to Gmail IMAP
    $connection = connectToGmail($gmail_config['email'], $password);
    if (!$connection) {
        json_response([
            'success' => false,
            'message' => 'Failed to connect to Gmail. Please check your credentials.',
            'data' => [],
            'stats' => ['total' => 0, 'unread' => 0, 'today' => 0]
        ]);
        return;
    }
    
    try {
        // Select INBOX
        if (!imap_reopen($connection, '{' . $gmail_config['imap_server'] . ':' . $gmail_config['imap_port'] . '/imap/ssl}INBOX')) {
            throw new Exception('Failed to select INBOX');
        }
        
        // Get email count
        $mailbox_info = imap_mailboxmsginfo($connection);
        $total_emails = $mailbox_info->Nmsgs;
        $unread_emails = $mailbox_info->Unread;
        
        // Get recent emails (last 50)
        $limit = 50;
        $start = max(1, $total_emails - $limit + 1);
        $end = $total_emails;
        
        $emails = [];
        $today_count = 0;
        $today = date('Y-m-d');
        
        if ($total_emails > 0) {
            // Get email headers
            $headers = imap_fetch_overview($connection, "$start:$end", 0);
            
            // Reverse to show newest first
            $headers = array_reverse($headers);
            
            foreach ($headers as $header) {
                $email_date = date('Y-m-d', strtotime($header->date));
                if ($email_date === $today) {
                    $today_count++;
                }
                
                $emails[] = [
                    'uid' => $header->uid,
                    'msgno' => $header->msgno,
                    'from' => isset($header->from) ? $header->from : 'Unknown',
                    'to' => isset($header->to) ? $header->to : '',
                    'subject' => isset($header->subject) ? imap_utf8($header->subject) : '(No Subject)',
                    'date' => $header->date,
                    'size' => $header->size,
                    'seen' => $header->seen,
                    'flagged' => $header->flagged,
                    'answered' => $header->answered,
                    'hasAttachments' => false // Will be determined when viewing individual email
                ];
            }
        }
        
        imap_close($connection);
        
        json_response([
            'success' => true,
            'data' => $emails,
            'stats' => [
                'total' => $total_emails,
                'unread' => $unread_emails,
                'today' => $today_count
            ]
        ]);
        
    } catch (Exception $e) {
        imap_close($connection);
        throw $e;
    }
}

function handleGetEmail() {
    global $gmail_config;
    
    $uid = $_GET['uid'] ?? null;
    if (!$uid) {
        json_response(['success' => false, 'message' => 'Email UID is required'], 400);
        return;
    }
    
    $password = getStoredGmailPassword();
    if (!$password) {
        json_response(['success' => false, 'message' => 'Gmail credentials not configured'], 401);
        return;
    }
    
    $connection = connectToGmail($gmail_config['email'], $password);
    if (!$connection) {
        json_response(['success' => false, 'message' => 'Failed to connect to Gmail'], 500);
        return;
    }
    
    try {
        // Select INBOX
        if (!imap_reopen($connection, '{' . $gmail_config['imap_server'] . ':' . $gmail_config['imap_port'] . '/imap/ssl}INBOX')) {
            throw new Exception('Failed to select INBOX');
        }
        
        // Get message number from UID
        $msgno = imap_msgno($connection, $uid);
        if (!$msgno) {
            throw new Exception('Email not found');
        }
        
        // Get email header
        $header = imap_headerinfo($connection, $msgno);
        
        // Get email body
        $body = getEmailBody($connection, $msgno);
        
        // Get attachments
        $attachments = getEmailAttachments($connection, $msgno);
        
        $email = [
            'uid' => $uid,
            'msgno' => $msgno,
            'from' => isset($header->from[0]) ? $header->from[0]->mailbox . '@' . $header->from[0]->host : 'Unknown',
            'to' => isset($header->to[0]) ? $header->to[0]->mailbox . '@' . $header->to[0]->host : '',
            'cc' => isset($header->cc) ? formatAddresses($header->cc) : '',
            'subject' => isset($header->subject) ? imap_utf8($header->subject) : '(No Subject)',
            'date' => $header->date,
            'size' => $header->Size,
            'seen' => $header->Unseen == '' ? true : false,
            'body' => $body,
            'attachments' => $attachments
        ];
        
        imap_close($connection);
        
        json_response([
            'success' => true,
            'data' => $email
        ]);
        
    } catch (Exception $e) {
        imap_close($connection);
        throw $e;
    }
}

function handleSetupCredentials() {
    global $gmail_config;
    
    $password = $_POST['password'] ?? '';
    if (!$password) {
        json_response(['success' => false, 'message' => 'Password is required'], 400);
        return;
    }
    
    // Test connection
    $connection = connectToGmail($gmail_config['email'], $password);
    if (!$connection) {
        json_response(['success' => false, 'message' => 'Failed to connect to Gmail. Please check your App Password.'], 401);
        return;
    }
    
    imap_close($connection);
    
    // Store password securely
    if (storeGmailPassword($password)) {
        json_response(['success' => true, 'message' => 'Gmail credentials saved successfully']);
    } else {
        json_response(['success' => false, 'message' => 'Failed to save credentials'], 500);
    }
}

function handleConnectionStatus() {
    $password = getStoredGmailPassword();
    if (!$password) {
        json_response(['success' => false, 'message' => 'Gmail credentials not configured']);
        return;
    }
    
    json_response(['success' => true, 'message' => 'Gmail connection configured']);
}

function handleMarkAsRead() {
    global $gmail_config;
    
    $uid = $_POST['uid'] ?? null;
    if (!$uid) {
        json_response(['success' => false, 'message' => 'Email UID is required'], 400);
        return;
    }
    
    $password = getStoredGmailPassword();
    if (!$password) {
        json_response(['success' => false, 'message' => 'Gmail credentials not configured'], 401);
        return;
    }
    
    $connection = connectToGmail($gmail_config['email'], $password);
    if (!$connection) {
        json_response(['success' => false, 'message' => 'Failed to connect to Gmail'], 500);
        return;
    }
    
    try {
        // Select INBOX
        if (!imap_reopen($connection, '{' . $gmail_config['imap_server'] . ':' . $gmail_config['imap_port'] . '/imap/ssl}INBOX')) {
            throw new Exception('Failed to select INBOX');
        }
        
        $msgno = imap_msgno($connection, $uid);
        if ($msgno) {
            imap_setflag_full($connection, $msgno, "\\Seen");
        }
        
        imap_close($connection);
        
        json_response(['success' => true, 'message' => 'Email marked as read']);
        
    } catch (Exception $e) {
        imap_close($connection);
        throw $e;
    }
}

function handleDeleteEmail() {
    global $gmail_config;
    
    $uid = $_POST['uid'] ?? null;
    if (!$uid) {
        json_response(['success' => false, 'message' => 'Email UID is required'], 400);
        return;
    }
    
    $password = getStoredGmailPassword();
    if (!$password) {
        json_response(['success' => false, 'message' => 'Gmail credentials not configured'], 401);
        return;
    }
    
    $connection = connectToGmail($gmail_config['email'], $password);
    if (!$connection) {
        json_response(['success' => false, 'message' => 'Failed to connect to Gmail'], 500);
        return;
    }
    
    try {
        // Select INBOX
        if (!imap_reopen($connection, '{' . $gmail_config['imap_server'] . ':' . $gmail_config['imap_port'] . '/imap/ssl}INBOX')) {
            throw new Exception('Failed to select INBOX');
        }
        
        $msgno = imap_msgno($connection, $uid);
        if ($msgno) {
            imap_delete($connection, $msgno);
            imap_expunge($connection);
        }
        
        imap_close($connection);
        
        json_response(['success' => true, 'message' => 'Email deleted successfully']);
        
    } catch (Exception $e) {
        imap_close($connection);
        throw $e;
    }
}

// Helper functions
function connectToGmail($email, $password) {
    global $gmail_config;
    
    $mailbox = '{' . $gmail_config['imap_server'] . ':' . $gmail_config['imap_port'] . '/imap/ssl}INBOX';
    
    // Suppress IMAP warnings
    $connection = @imap_open($mailbox, $email, $password);
    
    if (!$connection) {
        error_log("Gmail IMAP connection failed: " . imap_last_error());
        return false;
    }
    
    return $connection;
}

function getEmailBody($connection, $msgno) {
    $body = '';
    
    // Try to get HTML body first
    $structure = imap_fetchstructure($connection, $msgno);
    
    if (isset($structure->parts)) {
        // Multipart message
        foreach ($structure->parts as $partno => $part) {
            $data = imap_fetchbody($connection, $msgno, $partno + 1);
            
            // Decode if needed
            if ($part->encoding == 3) { // Base64
                $data = base64_decode($data);
            } elseif ($part->encoding == 4) { // Quoted-printable
                $data = quoted_printable_decode($data);
            }
            
            // Check if this is HTML or plain text
            if (strtolower($part->subtype) == 'html') {
                $body = $data;
                break;
            } elseif (strtolower($part->subtype) == 'plain' && empty($body)) {
                $body = nl2br(htmlspecialchars($data));
            }
        }
    } else {
        // Single part message
        $body = imap_fetchbody($connection, $msgno, 1);
        
        // Decode if needed
        if ($structure->encoding == 3) { // Base64
            $body = base64_decode($body);
        } elseif ($structure->encoding == 4) { // Quoted-printable
            $body = quoted_printable_decode($body);
        }
        
        // If it's plain text, convert to HTML
        if (strtolower($structure->subtype) == 'plain') {
            $body = nl2br(htmlspecialchars($body));
        }
    }
    
    return $body;
}

function getEmailAttachments($connection, $msgno) {
    $attachments = [];
    $structure = imap_fetchstructure($connection, $msgno);
    
    if (isset($structure->parts)) {
        foreach ($structure->parts as $partno => $part) {
            if (isset($part->disposition) && strtolower($part->disposition) == 'attachment') {
                $filename = '';
                
                if (isset($part->dparameters)) {
                    foreach ($part->dparameters as $param) {
                        if (strtolower($param->attribute) == 'filename') {
                            $filename = $param->value;
                            break;
                        }
                    }
                }
                
                if (empty($filename) && isset($part->parameters)) {
                    foreach ($part->parameters as $param) {
                        if (strtolower($param->attribute) == 'name') {
                            $filename = $param->value;
                            break;
                        }
                    }
                }
                
                if (!empty($filename)) {
                    $attachments[] = [
                        'filename' => imap_utf8($filename),
                        'size' => $part->bytes ?? 0,
                        'type' => $part->subtype ?? 'unknown'
                    ];
                }
            }
        }
    }
    
    return $attachments;
}

function formatAddresses($addresses) {
    $formatted = [];
    foreach ($addresses as $address) {
        $formatted[] = $address->mailbox . '@' . $address->host;
    }
    return implode(', ', $formatted);
}

function storeGmailPassword($password) {
    global $pdo;
    
    try {
        // Create settings table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS user_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            setting_key VARCHAR(255) NOT NULL,
            setting_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_setting (user_id, setting_key)
        )");
        
        // Encrypt password (basic encryption - in production use proper encryption)
        $encrypted_password = base64_encode($password);
        
        $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, setting_key, setting_value) 
                              VALUES (?, 'gmail_password', ?) 
                              ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
        
        return $stmt->execute([$_SESSION['user_id'], $encrypted_password, $encrypted_password]);
        
    } catch (Exception $e) {
        error_log("Failed to store Gmail password: " . $e->getMessage());
        return false;
    }
}

function getStoredGmailPassword() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM user_settings 
                              WHERE user_id = ? AND setting_key = 'gmail_password'");
        $stmt->execute([$_SESSION['user_id']]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            // Decrypt password (basic decryption - in production use proper decryption)
            return base64_decode($result['setting_value']);
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log("Failed to get Gmail password: " . $e->getMessage());
        return null;
    }
}
?>