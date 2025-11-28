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
        `status` enum('Pending', 'Call Later', 'Interested', 'Not Interested', 'Converted', 'No Answer') NOT NULL DEFAULT 'Pending',
        `remarks` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `client_id` (`client_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function getFollowups() {
    global $pdo;
    ensureTableExists();
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    // Get total count
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM followups");
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    // Get records with client details
    $sql = "SELECT f.*, c.name as client_name, c.phone as client_phone, c.company as client_company 
            FROM followups f
            LEFT JOIN followup_clients c ON f.client_id = c.id
            ORDER BY f.created_at DESC 
            LIMIT :limit OFFSET :offset";
            
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
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
    
    $stmt = $pdo->prepare("SELECT * FROM followups WHERE id = :id");
    $stmt->execute([':id' => $id]);
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
    
    echo json_encode([
        'success' => true,
        'message' => 'Followup added successfully',
        'id' => $pdo->lastInsertId()
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
    
    echo json_encode([
        'success' => true,
        'message' => 'Followup updated successfully'
    ]);
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

function getClientsDropdown() {
    global $pdo;
    
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
    
    $sql = "SELECT id, name, company FROM followup_clients ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $clients
    ]);
}
?>
