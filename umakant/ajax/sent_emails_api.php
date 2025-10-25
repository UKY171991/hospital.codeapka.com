<?php
// ajax/sent_emails_api.php - API for managing sent emails
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

$action = $_REQUEST['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            handleSentEmailsList();
            break;
        case 'scheduled':
            handleScheduledEmailsList();
            break;
        case 'stats':
            handleSentEmailsStats();
            break;
        case 'get':
            handleGetSentEmail();
            break;
        case 'delete':
            handleDeleteSentEmail();
            break;
        case 'bulk_delete':
            handleBulkDeleteSentEmails();
            break;
        case 'export':
            handleExportSentEmails();
            break;
        case 'cancel_scheduled':
            handleCancelScheduledEmail();
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    error_log("Sent Emails API Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
}

function handleSentEmailsList() {
    global $pdo;
    
    try {
        // Create sent emails table if it doesn't exist
        createSentEmailsTable();
        
        // Get sent emails for current user
        $stmt = $pdo->prepare("SELECT * FROM sent_emails 
                              WHERE user_id = ? 
                              ORDER BY sent_at DESC 
                              LIMIT 100");
        $stmt->execute([$_SESSION['user_id']]);
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'data' => $emails
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to load sent emails: ' . $e->getMessage()], 500);
    }
}

function handleScheduledEmailsList() {
    global $pdo;
    
    try {
        // Create scheduled emails table if it doesn't exist
        createScheduledEmailsTable();
        
        // Get scheduled emails for current user
        $stmt = $pdo->prepare("SELECT * FROM scheduled_emails 
                              WHERE user_id = ? 
                              ORDER BY schedule_date ASC");
        $stmt->execute([$_SESSION['user_id']]);
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true,
            'data' => $emails
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to load scheduled emails: ' . $e->getMessage()], 500);
    }
}

function handleSentEmailsStats() {
    global $pdo;
    
    try {
        createSentEmailsTable();
        createScheduledEmailsTable();
        
        $userId = $_SESSION['user_id'];
        
        // Total sent emails
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sent_emails WHERE user_id = ?");
        $stmt->execute([$userId]);
        $total = $stmt->fetchColumn();
        
        // Today's sent emails
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sent_emails 
                              WHERE user_id = ? AND DATE(sent_at) = CURDATE()");
        $stmt->execute([$userId]);
        $today = $stmt->fetchColumn();
        
        // This week's sent emails
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sent_emails 
                              WHERE user_id = ? AND sent_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute([$userId]);
        $week = $stmt->fetchColumn();
        
        // Scheduled emails
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM scheduled_emails 
                              WHERE user_id = ? AND status = 'pending'");
        $stmt->execute([$userId]);
        $scheduled = $stmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'today' => $today,
                'week' => $week,
                'scheduled' => $scheduled
            ]
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to load statistics: ' . $e->getMessage()], 500);
    }
}

function handleGetSentEmail() {
    global $pdo;
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Email ID is required'], 400);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM sent_emails 
                              WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $email = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$email) {
            json_response(['success' => false, 'message' => 'Email not found'], 404);
            return;
        }
        
        json_response([
            'success' => true,
            'data' => $email
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to get email: ' . $e->getMessage()], 500);
    }
}

function handleDeleteSentEmail() {
    global $pdo;
    
    $id = $_POST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Email ID is required'], 400);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM sent_emails 
                              WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$id, $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            json_response(['success' => true, 'message' => 'Email record deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Email not found or already deleted'], 404);
        }
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to delete email: ' . $e->getMessage()], 500);
    }
}

function handleBulkDeleteSentEmails() {
    global $pdo;
    
    $ids = $_POST['ids'] ?? [];
    if (empty($ids) || !is_array($ids)) {
        json_response(['success' => false, 'message' => 'Email IDs are required'], 400);
        return;
    }
    
    try {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $params = array_merge($ids, [$_SESSION['user_id']]);
        
        $stmt = $pdo->prepare("DELETE FROM sent_emails 
                              WHERE id IN ($placeholders) AND user_id = ?");
        $result = $stmt->execute($params);
        
        $deletedCount = $stmt->rowCount();
        
        json_response([
            'success' => true, 
            'message' => "Successfully deleted $deletedCount email record(s)",
            'deleted_count' => $deletedCount
        ]);
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to delete emails: ' . $e->getMessage()], 500);
    }
}

function handleExportSentEmails() {
    global $pdo;
    
    $ids = $_GET['ids'] ?? '';
    $idArray = array_filter(explode(',', $ids));
    
    if (empty($idArray)) {
        json_response(['success' => false, 'message' => 'No email IDs provided'], 400);
        return;
    }
    
    try {
        $placeholders = implode(',', array_fill(0, count($idArray), '?'));
        $params = array_merge($idArray, [$_SESSION['user_id']]);
        
        $stmt = $pdo->prepare("SELECT * FROM sent_emails 
                              WHERE id IN ($placeholders) AND user_id = ? 
                              ORDER BY sent_at DESC");
        $stmt->execute($params);
        $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Generate CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sent_emails_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['ID', 'To', 'CC', 'BCC', 'Subject', 'Priority', 'Sent Date', 'Body']);
        
        // CSV data
        foreach ($emails as $email) {
            fputcsv($output, [
                $email['id'],
                $email['to_email'],
                $email['cc_email'],
                $email['bcc_email'],
                $email['subject'],
                $email['priority'],
                $email['sent_at'],
                strip_tags($email['body'])
            ]);
        }
        
        fclose($output);
        exit;
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to export emails: ' . $e->getMessage()], 500);
    }
}

function handleCancelScheduledEmail() {
    global $pdo;
    
    $id = $_POST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Email ID is required'], 400);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE scheduled_emails 
                              SET status = 'cancelled' 
                              WHERE id = ? AND user_id = ? AND status = 'pending'");
        $result = $stmt->execute([$id, $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            json_response(['success' => true, 'message' => 'Scheduled email cancelled successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Scheduled email not found or cannot be cancelled'], 404);
        }
        
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Failed to cancel scheduled email: ' . $e->getMessage()], 500);
    }
}

function createSentEmailsTable() {
    global $pdo;
    
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
        INDEX idx_sent_at (sent_at),
        INDEX idx_priority (priority)
    )");
}

function createScheduledEmailsTable() {
    global $pdo;
    
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
        status ENUM('pending', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        sent_at TIMESTAMP NULL,
        error_message TEXT,
        INDEX idx_user_id (user_id),
        INDEX idx_schedule_date (schedule_date),
        INDEX idx_status (status)
    )");
}

// Helper function to add sample data for testing
function addSampleSentEmails() {
    global $pdo;
    
    try {
        createSentEmailsTable();
        
        // Check if sample data already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sent_emails WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            // Add sample sent emails
            $sampleEmails = [
                [
                    'to_email' => 'patient1@example.com',
                    'subject' => 'Appointment Reminder - Tomorrow at 10:00 AM',
                    'body' => 'Dear Patient, This is a reminder for your appointment tomorrow at 10:00 AM with Dr. Smith. Please arrive 15 minutes early.',
                    'priority' => 'high',
                    'sent_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
                ],
                [
                    'to_email' => 'patient2@example.com',
                    'cc_email' => 'nurse@hospital.com',
                    'subject' => 'Lab Results Available',
                    'body' => 'Dear Patient, Your lab results are now available. Please log into the patient portal to view them or contact us for more information.',
                    'priority' => 'normal',
                    'sent_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ],
                [
                    'to_email' => 'doctor@hospital.com',
                    'subject' => 'Patient Follow-up Required',
                    'body' => 'Dr. Johnson, Patient John Doe requires a follow-up appointment for his recent test results. Please schedule at your earliest convenience.',
                    'priority' => 'high',
                    'sent_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
                ],
                [
                    'to_email' => 'admin@hospital.com',
                    'subject' => 'Monthly Report Submission',
                    'body' => 'Please find attached the monthly pathology report for review. All tests have been completed successfully.',
                    'priority' => 'normal',
                    'sent_at' => date('Y-m-d H:i:s', strtotime('-1 week'))
                ]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO sent_emails (user_id, to_email, cc_email, subject, body, priority, sent_at) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($sampleEmails as $email) {
                $stmt->execute([
                    $_SESSION['user_id'],
                    $email['to_email'],
                    $email['cc_email'] ?? null,
                    $email['subject'],
                    $email['body'],
                    $email['priority'],
                    $email['sent_at']
                ]);
            }
        }
        
    } catch (Exception $e) {
        error_log("Failed to add sample sent emails: " . $e->getMessage());
    }
}

// Add sample data if this is a direct access for testing
if ($action === 'add_sample_data') {
    addSampleSentEmails();
    json_response(['success' => true, 'message' => 'Sample data added successfully']);
}
?>