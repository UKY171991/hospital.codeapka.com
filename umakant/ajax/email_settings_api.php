<?php
// ajax/email_settings_api.php - API for managing email settings, templates, and signatures
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

$action = $_REQUEST['action'] ?? 'get_settings';

try {
    switch ($action) {
        case 'get_settings':
            handleGetSettings();
            break;
        case 'save_preferences':
            handleSavePreferences();
            break;
        case 'save_auto_reply':
            handleSaveAutoReply();
            break;
        case 'get_templates':
            handleGetTemplates();
            break;
        case 'save_template':
            handleSaveTemplate();
            break;
        case 'delete_template':
            handleDeleteTemplate();
            break;
        case 'get_signatures':
            handleGetSignatures();
            break;
        case 'save_signature':
            handleSaveSignature();
            break;
        case 'delete_signature':
            handleDeleteSignature();
            break;
        case 'test_auto_reply':
            handleTestAutoReply();
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    error_log("Email Settings API Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
}

function handleGetSettings() {
    global $pdo;
    
    try {
        createSettingsTables();
        
        $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM user_settings 
                              WHERE user_id = ? AND setting_key LIKE 'email_%'");
        $stmt->execute([$_SESSION['user_id']]);
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Convert to more readable format
        $emailSettings = [];
        foreach ($settings as $key => $value) {
            $cleanKey = str_replace('email_', '', $key);
            $emailSettings[$cleanKey] = $value;
        }
        
        json_response([
            'success' => true,
            'data' => $emailSettings
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to load settings: ' . $e->getMessage()], 500);
    }
}

function handleSavePreferences() {
    global $pdo;
    
    try {
        createSettingsTables();
        
        $preferences = [
            'email_default_priority' => $_POST['default_priority'] ?? 'normal',
            'email_emails_per_page' => $_POST['emails_per_page'] ?? '50',
            'email_enable_imap' => $_POST['enable_imap'] === 'true' ? '1' : '0',
            'email_enable_smtp' => $_POST['enable_smtp'] === 'true' ? '1' : '0',
            'email_auto_refresh' => $_POST['auto_refresh'] === 'true' ? '1' : '0',
            'email_mark_as_read' => $_POST['mark_as_read'] === 'true' ? '1' : '0',
            'email_show_notifications' => $_POST['show_notifications'] === 'true' ? '1' : '0',
            'email_save_sent_copy' => $_POST['save_sent_copy'] === 'true' ? '1' : '0'
        ];
        
        foreach ($preferences as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, setting_key, setting_value) 
                                  VALUES (?, ?, ?) 
                                  ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
            $stmt->execute([$_SESSION['user_id'], $key, $value, $value]);
        }
        
        json_response(['success' => true, 'message' => 'Preferences saved successfully']);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to save preferences: ' . $e->getMessage()], 500);
    }
}

function handleSaveAutoReply() {
    global $pdo;
    
    try {
        createSettingsTables();
        
        $autoReplySettings = [
            'email_enable_auto_reply' => $_POST['enable_auto_reply'] === 'true' ? '1' : '0',
            'email_auto_reply_subject' => $_POST['auto_reply_subject'] ?? '',
            'email_auto_reply_message' => $_POST['auto_reply_message'] ?? '',
            'email_auto_reply_start_date' => $_POST['auto_reply_start_date'] ?? '',
            'email_auto_reply_end_date' => $_POST['auto_reply_end_date'] ?? '',
            'email_reply_only_once' => $_POST['reply_only_once'] === 'true' ? '1' : '0',
            'email_reply_to_known_only' => $_POST['reply_to_known_only'] === 'true' ? '1' : '0'
        ];
        
        foreach ($autoReplySettings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO user_settings (user_id, setting_key, setting_value) 
                                  VALUES (?, ?, ?) 
                                  ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
            $stmt->execute([$_SESSION['user_id'], $key, $value, $value]);
        }
        
        json_response(['success' => true, 'message' => 'Auto reply settings saved successfully']);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to save auto reply settings: ' . $e->getMessage()], 500);
    }
}

function handleGetTemplates() {
    global $pdo;
    
    try {
        createEmailTemplatesTable();
        
        $stmt = $pdo->prepare("SELECT * FROM email_templates 
                              WHERE user_id = ? 
                              ORDER BY category, name");
        $stmt->execute([$_SESSION['user_id']]);
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'data' => $templates
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to load templates: ' . $e->getMessage()], 500);
    }
}

function handleSaveTemplate() {
    global $pdo;
    
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $category = $_POST['category'] ?? 'general';
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');
    
    if (empty($name) || empty($subject) || empty($body)) {
        json_response(['success' => false, 'message' => 'Name, subject, and body are required'], 400);
        return;
    }
    
    try {
        createEmailTemplatesTable();
        
        if ($id) {
            // Update existing template
            $stmt = $pdo->prepare("UPDATE email_templates 
                                  SET name = ?, category = ?, subject = ?, body = ?, updated_at = NOW() 
                                  WHERE id = ? AND user_id = ?");
            $stmt->execute([$name, $category, $subject, $body, $id, $_SESSION['user_id']]);
        } else {
            // Create new template
            $stmt = $pdo->prepare("INSERT INTO email_templates (user_id, name, category, subject, body) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $name, $category, $subject, $body]);
        }
        
        json_response(['success' => true, 'message' => 'Template saved successfully']);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to save template: ' . $e->getMessage()], 500);
    }
}

function handleDeleteTemplate() {
    global $pdo;
    
    $id = $_POST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Template ID is required'], 400);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM email_templates WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            json_response(['success' => true, 'message' => 'Template deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Template not found'], 404);
        }
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to delete template: ' . $e->getMessage()], 500);
    }
}

function handleGetSignatures() {
    global $pdo;
    
    try {
        createEmailSignaturesTable();
        
        $stmt = $pdo->prepare("SELECT * FROM email_signatures 
                              WHERE user_id = ? 
                              ORDER BY is_default DESC, name");
        $stmt->execute([$_SESSION['user_id']]);
        $signatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'data' => $signatures
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to load signatures: ' . $e->getMessage()], 500);
    }
}

function handleSaveSignature() {
    global $pdo;
    
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $isDefault = $_POST['is_default'] === 'true';
    
    if (empty($name) || empty($content)) {
        json_response(['success' => false, 'message' => 'Name and content are required'], 400);
        return;
    }
    
    try {
        createEmailSignaturesTable();
        
        // If this is being set as default, unset other defaults
        if ($isDefault) {
            $stmt = $pdo->prepare("UPDATE email_signatures SET is_default = 0 WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        }
        
        if ($id) {
            // Update existing signature
            $stmt = $pdo->prepare("UPDATE email_signatures 
                                  SET name = ?, content = ?, is_default = ?, updated_at = NOW() 
                                  WHERE id = ? AND user_id = ?");
            $stmt->execute([$name, $content, $isDefault ? 1 : 0, $id, $_SESSION['user_id']]);
        } else {
            // Create new signature
            $stmt = $pdo->prepare("INSERT INTO email_signatures (user_id, name, content, is_default) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $name, $content, $isDefault ? 1 : 0]);
        }
        
        json_response(['success' => true, 'message' => 'Signature saved successfully']);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to save signature: ' . $e->getMessage()], 500);
    }
}

function handleDeleteSignature() {
    global $pdo;
    
    $id = $_POST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Signature ID is required'], 400);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM email_signatures WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            json_response(['success' => true, 'message' => 'Signature deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Signature not found'], 404);
        }
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to delete signature: ' . $e->getMessage()], 500);
    }
}

function handleTestAutoReply() {
    global $pdo;
    
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($subject) || empty($message)) {
        json_response(['success' => false, 'message' => 'Subject and message are required'], 400);
        return;
    }
    
    try {
        // Get Gmail credentials
        $password = getStoredGmailPassword();
        if (!$password) {
            json_response(['success' => false, 'message' => 'Gmail credentials not configured'], 401);
            return;
        }
        
        // Send test auto reply to user's own email
        $testEmailData = [
            'to' => 'umakant171991@gmail.com', // Send to self for testing
            'subject' => '[TEST] ' . $subject,
            'body' => $message . "\n\n---\nThis is a test auto reply message.",
            'priority' => 'normal'
        ];
        
        // Use the same sending mechanism as compose
        require_once __DIR__ . '/gmail_send_api.php';
        
        if (sendEmailViaSMTP(['email' => 'umakant171991@gmail.com', 'smtp_server' => 'smtp.gmail.com', 'smtp_port' => 587], $password, $testEmailData)) {
            json_response(['success' => true, 'message' => 'Test auto reply sent successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to send test auto reply'], 500);
        }
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to send test auto reply: ' . $e->getMessage()], 500);
    }
}

// Helper functions
function createSettingsTables() {
    global $pdo;
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        setting_key VARCHAR(255) NOT NULL,
        setting_value TEXT,
        metadata JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_setting (user_id, setting_key),
        INDEX idx_user_id (user_id),
        INDEX idx_setting_key (setting_key)
    )");
}

function createEmailTemplatesTable() {
    global $pdo;
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS email_templates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        category VARCHAR(100) DEFAULT 'general',
        subject VARCHAR(500) NOT NULL,
        body TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_category (category)
    )");
}

function createEmailSignaturesTable() {
    global $pdo;
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS email_signatures (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        is_default BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_is_default (is_default)
    )");
}

function getStoredGmailPassword() {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM user_settings 
                              WHERE user_id = ? AND setting_key = 'gmail_password'");
        $stmt->execute([$_SESSION['user_id']]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return base64_decode($result['setting_value']);
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log("Failed to get Gmail password: " . $e->getMessage());
        return null;
    }
}
?>