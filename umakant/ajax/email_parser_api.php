<?php
// ajax/email_parser_api.php - Email Parser Management API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (session_status() === PHP_SESSION_NONE) { 
        session_start();
    }

    require_once '../inc/connection.php';
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get_stats':
            getStats();
            break;
        case 'check_password':
            checkPassword();
            break;
        case 'save_password':
            savePassword();
            break;
        case 'run_parser':
            runParser();
            break;
        case 'test_parser':
            testParser();
            break;
        case 'get_processed_emails':
            getProcessedEmails();
            break;
        case 'get_logs':
            getLogs();
            break;
        default:
            throw new Exception('Invalid action specified');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

function getStats() {
    global $pdo;
    
    try {
        // Create table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `processed_emails` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `message_id` varchar(255) NOT NULL,
            `transaction_type` enum('income','expense') NOT NULL,
            `processed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_message_id` (`message_id`),
            KEY `idx_processed_at` (`processed_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Total processed
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM processed_emails");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Income count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM processed_emails WHERE transaction_type = 'income'");
        $income = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Expense count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM processed_emails WHERE transaction_type = 'expense'");
        $expense = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Last run
        $stmt = $pdo->query("SELECT MAX(processed_at) as last_run FROM processed_emails");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $last_run = $result['last_run'] ? date('Y-m-d H:i:s', strtotime($result['last_run'])) : 'Never';
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_processed' => $total,
                'income_count' => $income,
                'expense_count' => $expense,
                'last_run' => $last_run
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error getting stats: ' . $e->getMessage()
        ]);
    }
}

function checkPassword() {
    global $pdo;
    
    try {
        // Create table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `system_config` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `config_key` varchar(100) NOT NULL,
            `config_value` text NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_config_key` (`config_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM system_config WHERE config_key = 'gmail_password' AND config_value != ''");
        $configured = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        echo json_encode([
            'success' => true,
            'data' => [
                'configured' => $configured
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error checking password: ' . $e->getMessage()
        ]);
    }
}

function savePassword() {
    global $pdo;
    
    $password = $_POST['password'] ?? '';
    
    if (empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Password is required'
        ]);
        return;
    }
    
    try {
        // Create table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `system_config` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `config_key` varchar(100) NOT NULL,
            `config_value` text NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_config_key` (`config_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Check if exists
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM system_config WHERE config_key = 'gmail_password'");
        $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        if ($exists) {
            $stmt = $pdo->prepare("UPDATE system_config SET config_value = :password, updated_at = NOW() WHERE config_key = 'gmail_password'");
        } else {
            $stmt = $pdo->prepare("INSERT INTO system_config (config_key, config_value, created_at) VALUES ('gmail_password', :password, NOW())");
        }
        
        $stmt->execute([':password' => $password]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Gmail password saved successfully'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save password: ' . $e->getMessage()
        ]);
    }
}

function runParser() {
    // Execute the cron script
    $script_path = realpath(__DIR__ . '/../cron_email_parser.php');
    
    if (!file_exists($script_path)) {
        throw new Exception('Cron script not found');
    }
    
    // Run in background
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows
        pclose(popen("start /B php \"$script_path\"", "r"));
    } else {
        // Linux/Unix
        exec("php \"$script_path\" > /dev/null 2>&1 &");
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Email parser started. Check logs for progress.'
    ]);
}

function testParser() {
    // Test email parsing with sample data
    $test_emails = [
        [
            'subject' => 'Payment Received - Rs. 1500.00',
            'body' => 'Dear Customer, Your payment of Rs. 1500.00 has been credited to your account via UPI. Transaction ID: 123456789',
            'from' => 'bank@example.com'
        ],
        [
            'subject' => 'Bill Payment Successful',
            'body' => 'Your electricity bill payment of INR 2500.00 has been debited from your account.',
            'from' => 'utility@example.com'
        ]
    ];
    
    $results = [];
    
    foreach ($test_emails as $email) {
        $transaction = parseTransactionEmail(
            $email['subject'], 
            $email['body'], 
            $email['from'], 
            date('Y-m-d')
        );
        
        $results[] = [
            'email' => $email,
            'parsed' => $transaction
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $results
    ]);
}

function parseTransactionEmail($subject, $body, $from, $date) {
    $subject_lower = strtolower($subject);
    $body_lower = strtolower($body);
    $combined = $subject_lower . ' ' . $body_lower;
    
    // Payment keywords for income
    $income_keywords = [
        'payment received', 'payment credited', 'money received', 'credited to',
        'payment successful', 'transaction successful', 'amount credited',
        'upi credit', 'imps credit', 'neft credit', 'rtgs credit'
    ];
    
    // Payment keywords for expense
    $expense_keywords = [
        'payment debited', 'amount debited', 'payment made', 'transaction debited',
        'purchase', 'bill payment', 'recharge', 'subscription',
        'upi debit', 'imps debit', 'neft debit', 'rtgs debit'
    ];
    
    $is_income = false;
    $is_expense = false;
    
    foreach ($income_keywords as $keyword) {
        if (strpos($combined, $keyword) !== false) {
            $is_income = true;
            break;
        }
    }
    
    if (!$is_income) {
        foreach ($expense_keywords as $keyword) {
            if (strpos($combined, $keyword) !== false) {
                $is_expense = true;
                break;
            }
        }
    }
    
    if (!$is_income && !$is_expense) {
        return null;
    }
    
    // Extract amount
    $patterns = [
        '/(?:rs\.?|inr|₹)\s*([0-9,]+(?:\.[0-9]{2})?)/i',
        '/([0-9,]+(?:\.[0-9]{2})?)\s*(?:rs\.?|inr|₹)/i',
        '/amount[:\s]+(?:rs\.?|inr|₹)?\s*([0-9,]+(?:\.[0-9]{2})?)/i'
    ];
    
    $amount = null;
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $combined, $matches)) {
            $amount = str_replace(',', '', $matches[1]);
            $amount = floatval($amount);
            break;
        }
    }
    
    if (!$amount) {
        return null;
    }
    
    // Extract payment method
    $payment_method = 'Bank Transfer';
    if (strpos($combined, 'upi') !== false) $payment_method = 'UPI';
    if (strpos($combined, 'card') !== false) $payment_method = 'Card';
    if (strpos($combined, 'cash') !== false) $payment_method = 'Cash';
    
    return [
        'type' => $is_income ? 'income' : 'expense',
        'date' => $date,
        'amount' => $amount,
        'payment_method' => $payment_method,
        'description' => substr($subject, 0, 200),
        'from' => $from
    ];
}

function getProcessedEmails() {
    global $pdo;
    
    try {
        // Create table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `processed_emails` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `message_id` varchar(255) NOT NULL,
            `transaction_type` enum('income','expense') NOT NULL,
            `processed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_message_id` (`message_id`),
            KEY `idx_processed_at` (`processed_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $limit = intval($_GET['limit'] ?? 10);
        
        $stmt = $pdo->prepare("SELECT * FROM processed_emails ORDER BY processed_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $emails
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => true,
            'data' => []
        ]);
    }
}

function getLogs() {
    $log_file = realpath(__DIR__ . '/../logs/email_parser.log');
    $lines = intval($_GET['lines'] ?? 50);
    
    if (!file_exists($log_file)) {
        echo json_encode([
            'success' => true,
            'data' => [
                'logs' => 'No logs available yet. Run the parser to generate logs.'
            ]
        ]);
        return;
    }
    
    // Read last N lines
    $file = new SplFileObject($log_file, 'r');
    $file->seek(PHP_INT_MAX);
    $last_line = $file->key();
    $start_line = max(0, $last_line - $lines);
    
    $log_content = '';
    $file->seek($start_line);
    while (!$file->eof()) {
        $log_content .= $file->current();
        $file->next();
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'logs' => $log_content
        ]
    ]);
}
?>
