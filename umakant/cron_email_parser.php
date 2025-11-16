<?php
/**
 * Email Parser Cron Job
 * This script reads emails and automatically creates inventory income/expense records
 * Run this via cron every 5 minutes
 */

// Prevent direct browser access (optional security)
if (php_sapi_name() !== 'cli' && !isset($_GET['cron_key'])) {
    // Allow web access with secret key
    $secret_key = 'hospital_parser_2024_secure'; // Your custom secret key
    if (!isset($_GET['cron_key']) || $_GET['cron_key'] !== $secret_key) {
        die('Access denied. This script should be run via cron job.');
    }
}

// Set execution time limit
set_time_limit(300); // 5 minutes

// Include required files
require_once __DIR__ . '/inc/connection.php';

// Log file
$log_file = __DIR__ . '/logs/email_parser.log';
$log_dir = dirname($log_file);
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
    echo "[$timestamp] $message\n";
}

writeLog("=== Email Parser Cron Job Started ===");

// Gmail configuration
$gmail_config = [
    'email' => 'umakant171991@gmail.com',
    'imap_server' => 'imap.gmail.com',
    'imap_port' => 993
];

try {
    // Create system_config table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `system_config` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `config_key` varchar(100) NOT NULL,
        `config_value` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `idx_config_key` (`config_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Create processed_emails table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS `processed_emails` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `message_id` varchar(255) NOT NULL,
        `transaction_type` enum('income','expense') NOT NULL,
        `processed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `idx_message_id` (`message_id`),
        KEY `idx_processed_at` (`processed_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    // Get stored Gmail password
    $stmt = $pdo->query("SELECT config_value FROM system_config WHERE config_key = 'gmail_password' LIMIT 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result || empty($result['config_value'])) {
        writeLog("ERROR: Gmail password not configured in database");
        writeLog("Please configure Gmail App Password in: Inventory → Email Parser Settings");
        exit(1);
    }
    
    $password = $result['config_value'];
    
    // Connect to Gmail
    writeLog("Connecting to Gmail IMAP...");
    $mailbox = '{' . $gmail_config['imap_server'] . ':' . $gmail_config['imap_port'] . '/imap/ssl}INBOX';
    $connection = @imap_open($mailbox, $gmail_config['email'], $password);
    
    if (!$connection) {
        writeLog("ERROR: Failed to connect to Gmail: " . imap_last_error());
        exit(1);
    }
    
    writeLog("Successfully connected to Gmail");
    
    // Get unprocessed emails (last 24 hours)
    $since_date = date('d-M-Y', strtotime('-24 hours'));
    $emails = imap_search($connection, "SINCE \"$since_date\"");
    
    if (!$emails) {
        writeLog("No new emails found in the last 24 hours");
        imap_close($connection);
        exit(0);
    }
    
    writeLog("Found " . count($emails) . " emails to process");
    
    $processed_count = 0;
    $income_count = 0;
    $expense_count = 0;
    $skipped_count = 0;
    
    foreach ($emails as $email_number) {
        try {
            // Get email header
            $header = imap_headerinfo($connection, $email_number);
            $subject = isset($header->subject) ? imap_utf8($header->subject) : '';
            $from = isset($header->from[0]) ? $header->from[0]->mailbox . '@' . $header->from[0]->host : '';
            $date = date('Y-m-d', strtotime($header->date));
            
            writeLog("Processing email: $subject (from: $from)");
            
            // Get email body
            $body = getEmailBody($connection, $email_number);
            
            // Check if already processed
            $message_id = isset($header->message_id) ? $header->message_id : '';
            if ($message_id && isEmailProcessed($pdo, $message_id)) {
                writeLog("SKIP: Already processed");
                $skipped_count++;
                continue;
            }
            
            // Parse email for transaction data
            $transaction = parseTransactionEmail($subject, $body, $from, $date);
            
            if ($transaction) {
                // Insert into database
                if ($transaction['type'] === 'income') {
                    insertIncome($pdo, $transaction);
                    $income_count++;
                    writeLog("Created INCOME record: {$transaction['description']} - ₹{$transaction['amount']}");
                } elseif ($transaction['type'] === 'expense') {
                    insertExpense($pdo, $transaction);
                    $expense_count++;
                    writeLog("Created EXPENSE record: {$transaction['description']} - ₹{$transaction['amount']}");
                }
                
                // Mark as processed
                if ($message_id) {
                    markEmailAsProcessed($pdo, $message_id, $transaction['type']);
                }
                
                $processed_count++;
            } else {
                $skipped_count++;
            }
            
        } catch (Exception $e) {
            writeLog("ERROR processing email #$email_number: " . $e->getMessage());
        }
    }
    
    imap_close($connection);
    
    writeLog("=== Processing Complete ===");
    writeLog("Total Emails: " . count($emails));
    writeLog("Processed: $processed_count");
    writeLog("Income Records: $income_count");
    writeLog("Expense Records: $expense_count");
    writeLog("Skipped: $skipped_count");
    
} catch (Exception $e) {
    writeLog("FATAL ERROR: " . $e->getMessage());
    exit(1);
}

/**
 * Get email body content
 */
function getEmailBody($connection, $email_number) {
    $body = '';
    
    // Try to get HTML body first
    $structure = imap_fetchstructure($connection, $email_number);
    
    if (isset($structure->parts) && count($structure->parts)) {
        foreach ($structure->parts as $partNum => $part) {
            if ($part->subtype === 'HTML') {
                $body = imap_fetchbody($connection, $email_number, $partNum + 1);
                $body = quoted_printable_decode($body);
                break;
            } elseif ($part->subtype === 'PLAIN') {
                $body = imap_fetchbody($connection, $email_number, $partNum + 1);
            }
        }
    } else {
        $body = imap_body($connection, $email_number);
    }
    
    // Strip HTML tags
    $body = strip_tags($body);
    
    return $body;
}

/**
 * Parse email for transaction information
 */
function parseTransactionEmail($subject, $body, $from, $date) {
    global $log_file;
    
    $subject_lower = strtolower($subject);
    $body_lower = strtolower($body);
    $combined = $subject_lower . ' ' . $body_lower;
    
    // Payment keywords for income (expanded list)
    $income_keywords = [
        'payment received', 'payment credited', 'money received', 'credited to',
        'payment successful', 'transaction successful', 'amount credited',
        'upi credit', 'imps credit', 'neft credit', 'rtgs credit',
        'paytm payment', 'phonepe payment', 'gpay payment', 'google pay',
        'credited', 'deposit', 'received', 'incoming',
        'credit alert', 'account credited', 'money added',
        'payment confirmation', 'transfer received'
    ];
    
    // Payment keywords for expense (expanded list)
    $expense_keywords = [
        'payment debited', 'amount debited', 'payment made', 'transaction debited',
        'purchase', 'bill payment', 'recharge', 'subscription',
        'upi debit', 'imps debit', 'neft debit', 'rtgs debit',
        'debited', 'withdrawn', 'spent', 'paid',
        'debit alert', 'account debited', 'money deducted',
        'payment processed', 'transfer made', 'order placed'
    ];
    
    $is_income = false;
    $is_expense = false;
    $matched_keyword = '';
    
    foreach ($income_keywords as $keyword) {
        if (strpos($combined, $keyword) !== false) {
            $is_income = true;
            $matched_keyword = $keyword;
            break;
        }
    }
    
    if (!$is_income) {
        foreach ($expense_keywords as $keyword) {
            if (strpos($combined, $keyword) !== false) {
                $is_expense = true;
                $matched_keyword = $keyword;
                break;
            }
        }
    }
    
    if (!$is_income && !$is_expense) {
        writeLog("SKIP: No transaction keywords found in: $subject");
        return null; // Not a transaction email
    }
    
    // Extract amount
    $amount = extractAmount($combined);
    if (!$amount) {
        writeLog("SKIP: No amount found in: $subject (matched keyword: $matched_keyword)");
        return null; // No amount found
    }
    
    writeLog("DETECTED: " . ($is_income ? 'INCOME' : 'EXPENSE') . " - Amount: ₹$amount - Keyword: $matched_keyword");
    
    // Extract payment method
    $payment_method = extractPaymentMethod($combined);
    
    // Extract description
    $description = extractDescription($subject, $body);
    
    // Determine category
    $category = determineCategory($combined, $is_income);
    
    return [
        'type' => $is_income ? 'income' : 'expense',
        'date' => $date,
        'category' => $category,
        'description' => $description,
        'amount' => $amount,
        'payment_method' => $payment_method,
        'source_email' => $from,
        'notes' => 'Auto-imported from email'
    ];
}

/**
 * Extract amount from text
 */
function extractAmount($text) {
    // Enhanced patterns for Indian currency (₹ or Rs or INR)
    $patterns = [
        // Rs. 1500, Rs 1500, Rs.1500
        '/(?:rs\.?|inr|₹)\s*([0-9,]+(?:\.[0-9]{1,2})?)/i',
        // 1500 Rs, 1500 INR, 1500₹
        '/([0-9,]+(?:\.[0-9]{1,2})?)\s*(?:rs\.?|inr|₹)/i',
        // Amount: Rs. 1500, Amount Rs 1500
        '/amount[:\s]+(?:rs\.?|inr|₹)?\s*([0-9,]+(?:\.[0-9]{1,2})?)/i',
        // Credited/Debited Rs. 1500
        '/(?:credited|debited|received|paid|sent)[:\s]+(?:rs\.?|inr|₹)?\s*([0-9,]+(?:\.[0-9]{1,2})?)/i',
        // of Rs. 1500, for Rs. 1500
        '/(?:of|for)[:\s]+(?:rs\.?|inr|₹)?\s*([0-9,]+(?:\.[0-9]{1,2})?)/i',
        // Just numbers with currency nearby (within 10 chars)
        '/(?:rs\.?|inr|₹).{0,10}?([0-9,]+(?:\.[0-9]{1,2})?)/i',
        '/([0-9,]+(?:\.[0-9]{1,2})?)(?:.{0,10}?)(?:rs\.?|inr|₹)/i'
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $matches)) {
            $amount = str_replace(',', '', $matches[1]);
            $amount = floatval($amount);
            // Validate amount is reasonable (between 1 and 10,000,000)
            if ($amount >= 1 && $amount <= 10000000) {
                return $amount;
            }
        }
    }
    
    return null;
}

/**
 * Extract payment method
 */
function extractPaymentMethod($text) {
    if (strpos($text, 'upi') !== false) return 'UPI';
    if (strpos($text, 'card') !== false || strpos($text, 'debit') !== false || strpos($text, 'credit') !== false) return 'Card';
    if (strpos($text, 'neft') !== false) return 'Bank Transfer';
    if (strpos($text, 'rtgs') !== false) return 'Bank Transfer';
    if (strpos($text, 'imps') !== false) return 'Bank Transfer';
    if (strpos($text, 'cheque') !== false || strpos($text, 'check') !== false) return 'Cheque';
    if (strpos($text, 'cash') !== false) return 'Cash';
    
    return 'Bank Transfer'; // Default
}

/**
 * Extract description
 */
function extractDescription($subject, $body) {
    // Clean subject
    $description = trim($subject);
    
    // Limit length
    if (strlen($description) > 200) {
        $description = substr($description, 0, 197) . '...';
    }
    
    return $description;
}

/**
 * Determine category based on keywords
 */
function determineCategory($text, $is_income) {
    if ($is_income) {
        if (strpos($text, 'consultation') !== false) return 'Consultation';
        if (strpos($text, 'lab') !== false || strpos($text, 'test') !== false) return 'Lab Tests';
        if (strpos($text, 'pharmacy') !== false || strpos($text, 'medicine') !== false) return 'Pharmacy';
        if (strpos($text, 'surgery') !== false || strpos($text, 'operation') !== false) return 'Surgery';
        if (strpos($text, 'room') !== false || strpos($text, 'bed') !== false) return 'Room Charges';
        return 'Other Services';
    } else {
        if (strpos($text, 'medical supply') !== false || strpos($text, 'supplies') !== false) return 'Medical Supplies';
        if (strpos($text, 'equipment') !== false) return 'Equipment';
        if (strpos($text, 'electricity') !== false || strpos($text, 'water') !== false || strpos($text, 'utility') !== false) return 'Utilities';
        if (strpos($text, 'salary') !== false || strpos($text, 'wage') !== false) return 'Salaries';
        if (strpos($text, 'rent') !== false) return 'Rent';
        if (strpos($text, 'maintenance') !== false || strpos($text, 'repair') !== false) return 'Maintenance';
        if (strpos($text, 'marketing') !== false || strpos($text, 'advertisement') !== false) return 'Marketing';
        if (strpos($text, 'transport') !== false || strpos($text, 'fuel') !== false) return 'Transportation';
        return 'Other';
    }
}

/**
 * Check if email already processed
 */
function isEmailProcessed($pdo, $message_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM processed_emails WHERE message_id = :message_id");
    $stmt->execute([':message_id' => $message_id]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Mark email as processed
 */
function markEmailAsProcessed($pdo, $message_id, $type) {
    $stmt = $pdo->prepare("INSERT INTO processed_emails (message_id, transaction_type, processed_at) VALUES (:message_id, :type, NOW())");
    $stmt->execute([
        ':message_id' => $message_id,
        ':type' => $type
    ]);
}

/**
 * Insert income record
 */
function insertIncome($pdo, $transaction) {
    $sql = "INSERT INTO inventory_income (date, category, description, amount, payment_method, notes, created_at)
            VALUES (:date, :category, :description, :amount, :payment_method, :notes, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':date' => $transaction['date'],
        ':category' => $transaction['category'],
        ':description' => $transaction['description'],
        ':amount' => $transaction['amount'],
        ':payment_method' => $transaction['payment_method'],
        ':notes' => $transaction['notes'] . ' | From: ' . $transaction['source_email']
    ]);
}

/**
 * Insert expense record
 */
function insertExpense($pdo, $transaction) {
    $sql = "INSERT INTO inventory_expense (date, category, vendor, description, amount, payment_method, notes, created_at)
            VALUES (:date, :category, :vendor, :description, :amount, :payment_method, :notes, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':date' => $transaction['date'],
        ':category' => $transaction['category'],
        ':vendor' => $transaction['source_email'],
        ':description' => $transaction['description'],
        ':amount' => $transaction['amount'],
        ':payment_method' => $transaction['payment_method'],
        ':notes' => $transaction['notes']
    ]);
}

writeLog("=== Email Parser Cron Job Completed ===");
?>
