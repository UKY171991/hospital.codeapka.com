<?php
// ajax/followup_api.php - Followup Management API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

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
        case 'get_followups':
            getFollowups();
            break;
        case 'get_followup':
            getFollowup();
            break;
        case 'add_followup':
            addFollowup();
            break;
        case 'update_followup':
            updateFollowup();
            break;
        case 'delete_followup':
            deleteFollowup();
            break;
        case 'send_email_notification':
            sendEmailNotification();
            break;
        case 'get_clients_dropdown':
            getClientsDropdown();
            break;
        default:
            throw new Exception('Invalid action specified');
    }
    
} catch (Exception $e) {
    http_response_code(200);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Ensure table exists
function ensureTableExists() {
    global $pdo;
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `followups` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `client_id` int(11) NOT NULL,
        `followup_date` date NOT NULL,
        `next_followup_date` date DEFAULT NULL,
        `status` varchar(50) NOT NULL DEFAULT 'Pending',
        `remarks` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `client_id` (`client_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Update status column to VARCHAR to support new statuses if it was ENUM
    try {
        $pdo->exec("ALTER TABLE `followups` MODIFY COLUMN `status` varchar(50) NOT NULL DEFAULT 'Pending'");
    } catch (Exception $e) {
        // Ignore if already varchar or other error
    }
}

function getStatusMessage($status, $clientName, $remarks, $nextDate) {
    // Clean HTML from remarks for WhatsApp (plain text)
    $cleanRemarks = cleanHtmlForWhatsApp($remarks);
    
    // Build professional WhatsApp message
    $message = "🏥 *Hospital Management System*\n\n";
    $message .= "Dear *{$clientName}*,\n\n";
    
    // Status-specific messages
    switch ($status) {
        case 'Proposal Sent':
            $message .= "📋 *Status Update: Proposal Sent*\n\n";
            $message .= "We have prepared and sent a detailed proposal for your website project. Please take your time to review it carefully.\n\n";
            break;
            
        case 'Quotation Sent':
            $message .= "💰 *Status Update: Quotation Sent*\n\n";
            $message .= "We have sent you a detailed quotation for your website project. Please review the pricing and scope of work.\n\n";
            break;
            
        case 'Negotiation':
            $message .= "🤝 *Status Update: Under Negotiation*\n\n";
            $message .= "Thank you for your valuable feedback. We are currently reviewing the terms and will get back to you shortly.\n\n";
            break;
            
        case 'Project Started':
            $message .= "🚀 *Status Update: Project Started*\n\n";
            $message .= "Great news! We have officially started working on your website project. Our team is excited to bring your vision to life.\n\n";
            break;
            
        case 'Completed':
            $message .= "✅ *Status Update: Project Completed*\n\n";
            $message .= "Congratulations! Your website project has been successfully completed. Thank you for choosing our services.\n\n";
            break;
            
        case 'Call Later':
            $message .= "📞 *Status Update: Call Later*\n\n";
            $message .= "As per our discussion, we will contact you at a more convenient time to discuss your website requirements.\n\n";
            break;
            
        case 'Interested':
            $message .= "👍 *Status Update: Interest Confirmed*\n\n";
            $message .= "Thank you for showing interest in our web development services. We are excited to work with you!\n\n";
            break;
            
        case 'Not Interested':
            $message .= "📝 *Status Update: Not Interested*\n\n";
            $message .= "Thank you for considering our services. We appreciate your time and wish you all the best.\n\n";
            break;
            
        case 'No Answer':
            $message .= "📱 *Status Update: No Answer*\n\n";
            $message .= "We tried to reach you but couldn't connect. Please feel free to call us back at your convenience.\n\n";
            break;
            
        case 'Pending':
            $message .= "⏳ *Status Update: Pending*\n\n";
            $message .= "Your inquiry is currently being reviewed by our team. We will get back to you soon.\n\n";
            break;
            
        default:
            $message .= "📌 *Status Update: {$status}*\n\n";
            break;
    }
    
    // Add remarks if available
    if (!empty($cleanRemarks)) {
        $message .= "📝 *Details:*\n";
        $message .= "{$cleanRemarks}\n\n";
    }
    
    // Add next followup date if available
    if (!empty($nextDate)) {
        $message .= "📅 *Next Followup:* {$nextDate}\n\n";
    }
    
    // Professional closing
    $message .= "---\n";
    $message .= "If you have any questions or concerns, please don't hesitate to reach out.\n\n";
    $message .= "Best Regards,\n";
    $message .= "*Hospital Management Team*\n";
    $message .= "📧 Email: info@hospital.codeapka.com\n";
    $message .= "🌐 Website: https://hospital.codeapka.com";
    
    return $message;
}

// Helper function to clean HTML for WhatsApp messages
function cleanHtmlForWhatsApp($html) {
    if (empty($html)) {
        return '';
    }
    
    // Convert common HTML tags to plain text equivalents
    $text = $html;
    
    // Convert line breaks to newlines
    $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
    $text = preg_replace('/<\/p>/i', "\n\n", $text);
    $text = preg_replace('/<\/div>/i', "\n", $text);
    $text = preg_replace('/<\/h[1-6]>/i', "\n\n", $text);
    $text = preg_replace('/<\/li>/i', "\n", $text);
    
    // Convert bold and italic to WhatsApp formatting
    $text = preg_replace('/<(strong|b)>(.*?)<\/(strong|b)>/i', '*$2*', $text);
    $text = preg_replace('/<(em|i)>(.*?)<\/(em|i)>/i', '_$2_', $text);
    
    // Remove all remaining HTML tags
    $text = strip_tags($text);
    
    // Decode HTML entities
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Remove excessive whitespace
    $text = preg_replace('/\n\s*\n\s*\n/', "\n\n", $text);
    $text = trim($text);
    
    return $text;
}

function getFollowups() {
    global $pdo;
    ensureTableExists();
    
    // Get user role and ID from session
    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    // Get total count - filtered by role
    if ($userRole === 'master') {
        $countStmt = $pdo->query("SELECT COUNT(*) as total FROM followups");
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM followups f 
                                   JOIN followup_clients c ON f.client_id = c.id 
                                   WHERE c.added_by = :user_id");
        $countStmt->execute([':user_id' => $userId]);
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    $totalPages = ceil($totalRecords / $limit);
    
    // Get records with client details - filtered by role
    if ($userRole === 'master') {
        $sql = "SELECT f.*, c.name as client_name, c.phone as client_phone, c.company as client_company 
                FROM followups f
                LEFT JOIN followup_clients c ON f.client_id = c.id
                ORDER BY f.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $sql = "SELECT f.*, c.name as client_name, c.phone as client_phone, c.company as client_company 
                FROM followups f
                LEFT JOIN followup_clients c ON f.client_id = c.id
                WHERE c.added_by = :user_id
                ORDER BY f.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $followups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $followups,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords
        ]
    ]);
}

function getFollowup() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    // Get user role and ID from session
    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;
    
    // Build query based on user role
    if ($userRole === 'master') {
        $stmt = $pdo->prepare("SELECT * FROM followups WHERE id = :id");
        $stmt->execute([':id' => $id]);
    } else {
        $stmt = $pdo->prepare("SELECT f.* FROM followups f 
                               JOIN followup_clients c ON f.client_id = c.id 
                               WHERE f.id = :id AND c.added_by = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }
    $followup = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$followup) {
        throw new Exception('Followup not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $followup
    ]);
}

function addFollowup() {
    global $pdo;
    ensureTableExists();
    
    if (empty($_POST['client_id'])) {
        throw new Exception('Client is required');
    }
    
    if (empty($_POST['followup_date'])) {
        throw new Exception('Followup Date is required');
    }
    
    $sql = "INSERT INTO followups (client_id, followup_date, next_followup_date, status, remarks, created_at)
            VALUES (:client_id, :followup_date, :next_followup_date, :status, :remarks, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':client_id' => $_POST['client_id'],
        ':followup_date' => $_POST['followup_date'],
        ':next_followup_date' => !empty($_POST['next_followup_date']) ? $_POST['next_followup_date'] : null,
        ':status' => $_POST['status'] ?? 'Pending',
        ':remarks' => $_POST['remarks'] ?? ''
    ]);
    
    $followupId = $pdo->lastInsertId();
    $message = 'Followup added successfully';
    $whatsappLink = '';
    
    // Handle Email Sending
    if (isset($_POST['send_email']) && $_POST['send_email'] == '1') {
        $client = getClientDetails($_POST['client_id']);
        if ($client && !empty($client['email'])) {
            $subject = "Update on your Website Project: " . ($_POST['status'] ?? 'Pending');
            // Use HTML line breaks for email body
            $emailBody = getStatusMessage($_POST['status'] ?? 'Pending', $client['name'], $_POST['remarks'] ?? '', $_POST['next_followup_date'] ?? '');
            $emailBody = nl2br($emailBody); // Convert newlines to <br> if any
            $emailBody .= "<br><br>Best Regards,<br>Hospital Management Team";
            
            $headers = "From: Hospital Admin <noreply@hospital.codeapka.com>\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            @mail($client['email'], $subject, $emailBody, $headers);
            $message .= ' and Email sent';
        }
    }
    
    // Handle WhatsApp
    if (isset($_POST['send_whatsapp']) && $_POST['send_whatsapp'] == '1') {
        $client = getClientDetails($_POST['client_id']);
        if ($client && !empty($client['phone'])) {
            $waMessage = getStatusMessage($_POST['status'] ?? 'Pending', $client['name'], $_POST['remarks'] ?? '', $_POST['next_followup_date'] ?? '');
            $whatsappLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $client['phone']) . "?text=" . urlencode($waMessage);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'id' => $followupId,
        'whatsapp_link' => $whatsappLink
    ]);
}

function updateFollowup() {
    global $pdo;
    ensureTableExists();
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        throw new Exception('Invalid followup ID');
    }
    
    if (empty($_POST['client_id'])) {
        throw new Exception('Client is required');
    }
    
    if (empty($_POST['followup_date'])) {
        throw new Exception('Followup Date is required');
    }
    
    $sql = "UPDATE followups 
            SET client_id = :client_id, followup_date = :followup_date, 
                next_followup_date = :next_followup_date, status = :status, 
                remarks = :remarks, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':client_id' => $_POST['client_id'],
        ':followup_date' => $_POST['followup_date'],
        ':next_followup_date' => !empty($_POST['next_followup_date']) ? $_POST['next_followup_date'] : null,
        ':status' => $_POST['status'] ?? 'Pending',
        ':remarks' => $_POST['remarks'] ?? ''
    ]);
    
    $message = 'Followup updated successfully';
    $whatsappLink = '';
    
    // Handle Email Sending
    if (isset($_POST['send_email']) && $_POST['send_email'] == '1') {
        $client = getClientDetails($_POST['client_id']);
        if ($client && !empty($client['email'])) {
            $subject = "Update on your Website Project: " . ($_POST['status'] ?? 'Pending');
            $emailBody = getStatusMessage($_POST['status'] ?? 'Pending', $client['name'], $_POST['remarks'] ?? '', $_POST['next_followup_date'] ?? '');
            $emailBody = nl2br($emailBody);
            $emailBody .= "<br><br>Best Regards,<br>Hospital Management Team";
            
            $headers = "From: Hospital Admin <noreply@hospital.codeapka.com>\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            @mail($client['email'], $subject, $emailBody, $headers);
            $message .= ' and Email sent';
        }
    }
    
    // Handle WhatsApp
    if (isset($_POST['send_whatsapp']) && $_POST['send_whatsapp'] == '1') {
        $client = getClientDetails($_POST['client_id']);
        if ($client && !empty($client['phone'])) {
            $waMessage = getStatusMessage($_POST['status'] ?? 'Pending', $client['name'], $_POST['remarks'] ?? '', $_POST['next_followup_date'] ?? '');
            $whatsappLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $client['phone']) . "?text=" . urlencode($waMessage);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'whatsapp_link' => $whatsappLink
    ]);
}

function getClientDetails($clientId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM followup_clients WHERE id = :id");
    $stmt->execute([':id' => $clientId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function deleteFollowup() {
    global $pdo;
    ensureTableExists();
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        throw new Exception('Invalid followup ID');
    }
    
    $stmt = $pdo->prepare("DELETE FROM followups WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Followup deleted successfully'
        ]);
    } else {
        throw new Exception('Followup not found');
    }
}

function sendEmailNotification() {
    global $pdo;
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        throw new Exception('Invalid followup ID');
    }
    
    // Fetch followup and client details
    $sql = "SELECT f.*, c.name as client_name, c.email as client_email 
            FROM followups f
            JOIN followup_clients c ON f.client_id = c.id
            WHERE f.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        throw new Exception('Followup record not found');
    }
    
    if (empty($data['client_email'])) {
        throw new Exception('Client does not have an email address');
    }
    
    $subject = "Update on your Website Project: " . ($data['status'] ?? 'Pending');
    $body = getStatusMessage($data['status'] ?? 'Pending', $data['client_name'], $data['remarks'] ?? '', $data['next_followup_date'] ?? '');
    $body = nl2br($body);
    $body .= "<br><br>Best Regards,<br>Hospital Management Team";
    
    $headers = "From: Hospital Admin <noreply@hospital.codeapka.com>\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    if (mail($data['client_email'], $subject, $body, $headers)) {
        echo json_encode([
            'success' => true,
            'message' => 'Email notification sent successfully'
        ]);
    } else {
        throw new Exception('Failed to send email');
    }
}

function getClientsDropdown() {
    global $pdo;
    
    // Get user role and ID from session
    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;
    
    // Ensure followup_clients table exists (it should, but good to be safe)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `followup_clients` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(20) NOT NULL,
        `company` varchar(255) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Build query based on user role
    if ($userRole === 'master') {
        $sql = "SELECT id, name, company FROM followup_clients ORDER BY name ASC";
        $stmt = $pdo->query($sql);
    } else {
        $sql = "SELECT id, name, company FROM followup_clients WHERE added_by = :user_id ORDER BY name ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
    }
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $clients
    ]);
}
?>
