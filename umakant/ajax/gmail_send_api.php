<?php
// ajax/gmail_send_api.php - Gmail SMTP API for sending emails
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
    'smtp_server' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_encryption' => 'tls'
];

$action = $_REQUEST['action'] ?? 'send';

try {
    switch ($action) {
        case 'send':
            handleSendEmail();
            break;
        case 'save_draft':
            handleSaveDraft();
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    error_log("Gmail Send API Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
}

function handleSendEmail() {
    global $gmail_config;
    
    // Get stored password
    $password = getStoredGmailPassword();
    if (!$password) {
        json_response([
            'success' => false,
            'message' => 'Gmail credentials not configured. Please set up your Gmail credentials first.'
        ], 401);
        return;
    }
    
    // Validate required fields
    $to = $_POST['to'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';
    
    if (empty($to) || empty($subject) || empty($body)) {
        json_response(['success' => false, 'message' => 'To, Subject, and Body are required'], 400);
        return;
    }
    
    // Prepare email data
    $emailData = [
        'to' => $to,
        'cc' => $_POST['cc'] ?? '',
        'bcc' => $_POST['bcc'] ?? '',
        'subject' => $subject,
        'body' => $body,
        'priority' => $_POST['priority'] ?? 'normal',
        'read_receipt' => $_POST['read_receipt'] ?? '0',
        'send_copy' => $_POST['send_copy'] ?? '1',
        'schedule_date' => $_POST['schedule_date'] ?? ''
    ];
    
    // Handle attachments
    $attachments = [];
    if (isset($_FILES['attachments']) && is_array($_FILES['attachments']['name'])) {
        for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
            if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                $attachments[] = [
                    'name' => $_FILES['attachments']['name'][$i],
                    'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                    'type' => $_FILES['attachments']['type'][$i],
                    'size' => $_FILES['attachments']['size'][$i]
                ];
            }
        }
    }
    
    // Check if scheduled
    if (!empty($emailData['schedule_date'])) {
        $scheduleTime = strtotime($emailData['schedule_date']);
        if ($scheduleTime > time()) {
            // Save as scheduled email
            if (saveScheduledEmail($emailData, $attachments)) {
                json_response(['success' => true, 'message' => 'Email scheduled successfully']);
            } else {
                json_response(['success' => false, 'message' => 'Failed to schedule email'], 500);
            }
            return;
        }
    }
    
    // Send email immediately
    if (sendEmailViaSMTP($gmail_config, $password, $emailData, $attachments)) {
        // Log sent email
        logSentEmail($emailData);
        json_response(['success' => true, 'message' => 'Email sent successfully']);
    } else {
        json_response(['success' => false, 'message' => 'Failed to send email'], 500);
    }
}

function handleSaveDraft() {
    $draftData = [
        'to' => $_POST['to'] ?? '',
        'cc' => $_POST['cc'] ?? '',
        'bcc' => $_POST['bcc'] ?? '',
        'subject' => $_POST['subject'] ?? '',
        'body' => $_POST['body'] ?? '',
        'priority' => $_POST['priority'] ?? 'normal'
    ];
    
    if (saveDraft($draftData)) {
        json_response(['success' => true, 'message' => 'Draft saved successfully']);
    } else {
        json_response(['success' => false, 'message' => 'Failed to save draft'], 500);
    }
}

function sendEmailViaSMTP($config, $password, $emailData, $attachments = []) {
    // Use PHPMailer if available, otherwise use basic mail function
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return sendWithPHPMailer($config, $password, $emailData, $attachments);
    } else {
        return sendWithBasicSMTP($config, $password, $emailData, $attachments);
    }
}

function sendWithBasicSMTP($config, $password, $emailData, $attachments = []) {
    // Basic SMTP implementation
    $smtp_server = $config['smtp_server'];
    $smtp_port = $config['smtp_port'];
    $username = $config['email'];
    
    // Create socket connection
    $socket = fsockopen($smtp_server, $smtp_port, $errno, $errstr, 30);
    if (!$socket) {
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }
    
    try {
        // Read initial response
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '220') {
            throw new Exception("SMTP server not ready: $response");
        }
        
        // EHLO
        fwrite($socket, "EHLO " . $config['email'] . "\r\n");
        $response = fgets($socket, 512);
        
        // STARTTLS
        fwrite($socket, "STARTTLS\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '220') {
            throw new Exception("STARTTLS failed: $response");
        }
        
        // Enable crypto
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new Exception("Failed to enable TLS encryption");
        }
        
        // EHLO again after TLS
        fwrite($socket, "EHLO " . $config['email'] . "\r\n");
        $response = fgets($socket, 512);
        
        // AUTH LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '334') {
            throw new Exception("AUTH LOGIN failed: $response");
        }
        
        // Send username
        fwrite($socket, base64_encode($username) . "\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '334') {
            throw new Exception("Username authentication failed: $response");
        }
        
        // Send password
        fwrite($socket, base64_encode($password) . "\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '235') {
            throw new Exception("Password authentication failed: $response");
        }
        
        // MAIL FROM
        fwrite($socket, "MAIL FROM: <" . $username . ">\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '250') {
            throw new Exception("MAIL FROM failed: $response");
        }
        
        // RCPT TO
        $recipients = explode(',', $emailData['to']);
        foreach ($recipients as $recipient) {
            $recipient = trim($recipient);
            if (!empty($recipient)) {
                fwrite($socket, "RCPT TO: <$recipient>\r\n");
                $response = fgets($socket, 512);
                if (substr($response, 0, 3) != '250') {
                    throw new Exception("RCPT TO failed for $recipient: $response");
                }
            }
        }
        
        // Add CC recipients
        if (!empty($emailData['cc'])) {
            $cc_recipients = explode(',', $emailData['cc']);
            foreach ($cc_recipients as $recipient) {
                $recipient = trim($recipient);
                if (!empty($recipient)) {
                    fwrite($socket, "RCPT TO: <$recipient>\r\n");
                    $response = fgets($socket, 512);
                }
            }
        }
        
        // Add BCC recipients
        if (!empty($emailData['bcc'])) {
            $bcc_recipients = explode(',', $emailData['bcc']);
            foreach ($bcc_recipients as $recipient) {
                $recipient = trim($recipient);
                if (!empty($recipient)) {
                    fwrite($socket, "RCPT TO: <$recipient>\r\n");
                    $response = fgets($socket, 512);
                }
            }
        }
        
        // DATA
        fwrite($socket, "DATA\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '354') {
            throw new Exception("DATA command failed: $response");
        }
        
        // Email headers and body
        $email_content = buildEmailContent($config['email'], $emailData, $attachments);
        fwrite($socket, $email_content);
        fwrite($socket, "\r\n.\r\n");
        
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) != '250') {
            throw new Exception("Email sending failed: $response");
        }
        
        // QUIT
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        return true;
        
    } catch (Exception $e) {
        error_log("SMTP Error: " . $e->getMessage());
        fclose($socket);
        return false;
    }
}

function buildEmailContent($from, $emailData, $attachments = []) {
    $boundary = md5(time());
    
    $headers = [];
    $headers[] = "From: $from";
    $headers[] = "To: " . $emailData['to'];
    
    if (!empty($emailData['cc'])) {
        $headers[] = "Cc: " . $emailData['cc'];
    }
    
    $headers[] = "Subject: " . $emailData['subject'];
    $headers[] = "Date: " . date('r');
    $headers[] = "Message-ID: <" . md5(time()) . "@" . parse_url($from, PHP_URL_HOST) . ">";
    
    if ($emailData['priority'] === 'high') {
        $headers[] = "X-Priority: 1";
        $headers[] = "X-MSMail-Priority: High";
    } elseif ($emailData['priority'] === 'low') {
        $headers[] = "X-Priority: 5";
        $headers[] = "X-MSMail-Priority: Low";
    }
    
    if ($emailData['read_receipt'] === '1') {
        $headers[] = "Disposition-Notification-To: $from";
    }
    
    $headers[] = "MIME-Version: 1.0";
    
    if (!empty($attachments)) {
        $headers[] = "Content-Type: multipart/mixed; boundary=\"$boundary\"";
    } else {
        $headers[] = "Content-Type: text/html; charset=UTF-8";
    }
    
    $content = implode("\r\n", $headers) . "\r\n\r\n";
    
    if (!empty($attachments)) {
        // Multipart message with attachments
        $content .= "--$boundary\r\n";
        $content .= "Content-Type: text/html; charset=UTF-8\r\n";
        $content .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $content .= $emailData['body'] . "\r\n\r\n";
        
        // Add attachments
        foreach ($attachments as $attachment) {
            if (file_exists($attachment['tmp_name'])) {
                $file_content = base64_encode(file_get_contents($attachment['tmp_name']));
                $content .= "--$boundary\r\n";
                $content .= "Content-Type: " . $attachment['type'] . "; name=\"" . $attachment['name'] . "\"\r\n";
                $content .= "Content-Disposition: attachment; filename=\"" . $attachment['name'] . "\"\r\n";
                $content .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $content .= chunk_split($file_content) . "\r\n";
            }
        }
        
        $content .= "--$boundary--\r\n";
    } else {
        // Simple text/html message
        $content .= $emailData['body'];
    }
    
    return $content;
}

function logSentEmail($emailData) {
    global $pdo;
    
    try {
        // Create sent emails table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS sent_emails (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            to_email TEXT NOT NULL,
            cc_email TEXT,
            bcc_email TEXT,
            subject VARCHAR(500),
            body TEXT,
            priority VARCHAR(20) DEFAULT 'normal',
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_sent_at (sent_at)
        )");
        
        $stmt = $pdo->prepare("INSERT INTO sent_emails (user_id, to_email, cc_email, bcc_email, subject, body, priority) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $emailData['to'],
            $emailData['cc'],
            $emailData['bcc'],
            $emailData['subject'],
            $emailData['body'],
            $emailData['priority']
        ]);
        
    } catch (Exception $e) {
        error_log("Failed to log sent email: " . $e->getMessage());
    }
}

function saveDraft($draftData) {
    global $pdo;
    
    try {
        // Create drafts table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS email_drafts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            to_email TEXT,
            cc_email TEXT,
            bcc_email TEXT,
            subject VARCHAR(500),
            body TEXT,
            priority VARCHAR(20) DEFAULT 'normal',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id)
        )");
        
        $stmt = $pdo->prepare("INSERT INTO email_drafts (user_id, to_email, cc_email, bcc_email, subject, body, priority) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $_SESSION['user_id'],
            $draftData['to'],
            $draftData['cc'],
            $draftData['bcc'],
            $draftData['subject'],
            $draftData['body'],
            $draftData['priority']
        ]);
        
    } catch (Exception $e) {
        error_log("Failed to save draft: " . $e->getMessage());
        return false;
    }
}

function saveScheduledEmail($emailData, $attachments) {
    global $pdo;
    
    try {
        // Create scheduled emails table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS scheduled_emails (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            to_email TEXT NOT NULL,
            cc_email TEXT,
            bcc_email TEXT,
            subject VARCHAR(500),
            body TEXT,
            priority VARCHAR(20) DEFAULT 'normal',
            schedule_date DATETIME NOT NULL,
            attachments JSON,
            status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_schedule_date (schedule_date),
            INDEX idx_status (status)
        )");
        
        $stmt = $pdo->prepare("INSERT INTO scheduled_emails (user_id, to_email, cc_email, bcc_email, subject, body, priority, schedule_date, attachments) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $_SESSION['user_id'],
            $emailData['to'],
            $emailData['cc'],
            $emailData['bcc'],
            $emailData['subject'],
            $emailData['body'],
            $emailData['priority'],
            $emailData['schedule_date'],
            json_encode($attachments)
        ]);
        
    } catch (Exception $e) {
        error_log("Failed to save scheduled email: " . $e->getMessage());
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