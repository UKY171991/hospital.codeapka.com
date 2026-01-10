<?php
/**
 * Pending Tasks Email Cron Job
 * This script sends a digest of all pending tasks to uky171991@gmail.com
 * Recommended to run once every 24 hours
 */

// Set execution time limit
set_time_limit(300); // 5 minutes

// Include database connection
require_once __DIR__ . '/inc/connection.php';

// Security: Prevent direct unauthorized access via web
if (php_sapi_name() !== 'cli') {
    $secret_key = 'hospital_tasks_cron_2024'; // Custom secret key
    if (!isset($_GET['cron_key']) || $_GET['cron_key'] !== $secret_key) {
        die('Access denied. This script should be run via cron job or with a valid cron_key.');
    }
}

/**
 * SMTP Email Sending Helper (Minimal standalone SMTP implementation)
 */
function sendEmailViaSTMP($to, $subject, $body, $fromName = 'Hospital System', $fromEmail = 'info@codeapka.com') {
    global $pdo;
    
    // Get Gmail App Password from system_config (configured in Email Parser Settings)
    $stmt = $pdo->query("SELECT config_value FROM system_config WHERE config_key = 'gmail_password' LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result || empty($result['config_value'])) {
        return ['success' => false, 'message' => 'Sender password not configured in system_config.'];
    }
    
    $password = $result['config_value']; // This is likely already plain text if from system_config based on cron_email_parser.php logic
    $smtp_server = 'smtp.gmail.com';
    $smtp_port = 587;
    $username = 'umakant171991@gmail.com'; // Gmail user for authentication

    // Connect
    $socket = fsockopen($smtp_server, $smtp_port, $errno, $errstr, 30);
    if (!$socket) return ['success' => false, 'message' => "Socket error: $errstr ($errno)"];

    try {
        fgets($socket, 512);
        fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n"); fgets($socket, 512);
        fwrite($socket, "STARTTLS\r\n"); $resp = fgets($socket, 512);
        if (substr($resp, 0, 3) != '220') throw new Exception("STARTTLS failed");
        
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        fwrite($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n"); fgets($socket, 512);
        
        fwrite($socket, "AUTH LOGIN\r\n"); fgets($socket, 512);
        fwrite($socket, base64_encode($username) . "\r\n"); fgets($socket, 512);
        fwrite($socket, base64_encode($password) . "\r\n"); $resp = fgets($socket, 512);
        if (substr($resp, 0, 3) != '235') throw new Exception("Auth failed: " . $resp);
        
        fwrite($socket, "MAIL FROM: <$username>\r\n"); fgets($socket, 512);
        fwrite($socket, "RCPT TO: <$to>\r\n"); fgets($socket, 512);
        fwrite($socket, "DATA\r\n"); fgets($socket, 512);
        
        $boundary = md5(time());
        $headers = "From: $fromName <$fromEmail>\r\n";
        $headers .= "Reply-To: $fromEmail\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "X-Mailer: Hospital Management System\r\n";
        
        fwrite($socket, $headers . "\r\n" . $body . "\r\n.\r\n");
        $resp = fgets($socket, 512);
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        return substr($resp, 0, 3) == '250' ? ['success' => true] : ['success' => false, 'message' => "Send failed: $resp"];
    } catch (Exception $e) {
        fclose($socket);
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

try {
    // Ensure system_config table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `system_config` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `config_key` varchar(100) NOT NULL,
        `config_value` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `idx_config_key` (`config_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Check if already sent in last 23 hours
    $stmt = $pdo->prepare("SELECT config_value FROM system_config WHERE config_key = 'last_pending_tasks_email_sent' LIMIT 1");
    $stmt->execute();
    $last_sent = $stmt->fetchColumn();

    if ($last_sent && (time() - strtotime($last_sent)) < (6 * 3600)) {
        if (!isset($_GET['force'])) {
            die('Email already sent within the last 24 hours. Use &force=1 to override.');
        }
    }

    // Fetch all 'Pending' tasks
    $sql = "SELECT t.*, c.name as client_name 
            FROM tasks t
            LEFT JOIN clients c ON t.client_id = c.id
            WHERE t.status = 'Pending'
            ORDER BY t.priority = 'Urgent' DESC, t.priority = 'High' DESC, t.priority = 'Medium' DESC, t.due_date ASC";
    
    $stmt = $pdo->query($sql);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tasks)) {
        echo "No pending tasks found. No email sent.";
        exit;
    }

    // Recipient
    $to = 'uky171991@gmail.com';
    $subject = 'Daily Pending Tasks Digest - ' . date('d M Y');

    // Build HTML message
    $message = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 20px; }
            .header { background-color: #0d6efd; color: #fff; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
            th { background-color: #f8f9fa; font-weight: bold; }
            .priority { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
            .priority-Urgent { background-color: #dc3545; color: #fff; }
            .priority-High { background-color: #ffc107; color: #000; }
            .priority-Medium { background-color: #17a2b8; color: #fff; }
            .priority-Low { background-color: #6c757d; color: #fff; }
            .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #777; }
            .btn { display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: #ffffff !important; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1 style="margin:0;">Pending Tasks Report</h1>
            <p style="margin:5px 0 0 0;">Generated on ' . date('l, d F Y, h:i A') . '</p>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>You have <strong>' . count($tasks) . '</strong> pending tasks that require attention.</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Client</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>';

    foreach ($tasks as $task) {
        $priority_class = 'priority-' . $task['priority'];
        $due_date = $task['due_date'] ? date('d M Y', strtotime($task['due_date'])) : '-';
        
        $message .= '
                    <tr>
                        <td>
                            <strong>' . htmlspecialchars($task['title']) . '</strong><br>
                            <small style="color: #666;">' . htmlspecialchars(substr($task['description'], 0, 100)) . (strlen($task['description']) > 100 ? '...' : '') . '</small>
                        </td>
                        <td>' . htmlspecialchars($task['client_name'] ?? 'N/A') . '</td>
                        <td><span class="priority ' . $priority_class . '">' . htmlspecialchars($task['priority']) . '</span></td>
                        <td>' . htmlspecialchars($due_date) . '</td>
                    </tr>';
    }

    $message .= '
                </tbody>
            </table>
            
            <div style="text-align: center;">
                <a href="https://hospital.codeapka.com/umakant/tasks.php" class="btn">Manage All Tasks</a>
            </div>
            
            <p>Thank you!</p>
        </div>
        <div class="footer">
            <p>This is an automated report generated by Hospital Management System.<br>
            Sender: info@codeapka.com</p>
        </div>
    </body>
    </html>';

    // Send Email using SMTP
    $sendResult = sendEmailViaSTMP($to, $subject, $message, 'Hospital System', 'info@codeapka.com');

    if ($sendResult['success']) {
        // Record the last sent timestamp
        $stmt = $pdo->prepare("INSERT INTO system_config (config_key, config_value) VALUES ('last_pending_tasks_email_sent', NOW()) 
                               ON DUPLICATE KEY UPDATE config_value = NOW()");
        $stmt->execute();
        
        echo "Success: Email with " . count($tasks) . " tasks sent successfully to $to via SMTP (info@codeapka.com).";
    } else {
        echo "Error: Failed to send email via SMTP. " . $sendResult['message'];
        
        // Fallback to mail() if SMTP fails? (Optional: The user specifically wants to avoid spam, so fallback might be bad)
        /*
        $headers = "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n" . "From: info@codeapka.com" . "\r\n";
        if (mail($to, $subject, $message, $headers)) { echo "\nFallback: Sent via standard mail()."; }
        */
    }

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}
?>
