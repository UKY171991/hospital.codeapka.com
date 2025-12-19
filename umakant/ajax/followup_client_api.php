<?php
// ajax/followup_client_api.php - Followup Client Management API
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
        case 'get_clients':
            getFollowupClients();
            break;
        case 'get_client':
            getFollowupClient();
            break;
        case 'add_client':
            addFollowupClient();
            break;
        case 'update_client':
            updateFollowupClient();
            break;
        case 'delete_client':
            deleteFollowupClient();
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
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `followup_clients` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(20) NOT NULL,
        `company` varchar(255) DEFAULT NULL,
        `followup_message` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Add followup_message column if it doesn't exist
    $pdo->exec("ALTER TABLE followup_clients ADD COLUMN IF NOT EXISTS `followup_message` text DEFAULT NULL AFTER `company`");
    
    // Add added_by column if it doesn't exist
    $pdo->exec("ALTER TABLE followup_clients ADD COLUMN IF NOT EXISTS `added_by` int(11) DEFAULT NULL AFTER `followup_message`");
}

function getFollowupClients() {
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
        $countStmt = $pdo->query("SELECT COUNT(*) as total FROM followup_clients");
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    } else {
        $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM followup_clients WHERE added_by = :user_id");
        $countStmt->execute([':user_id' => $userId]);
        $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    $totalPages = ceil($totalRecords / $limit);
    
    // Get records - filtered by role
    if ($userRole === 'master') {
        $sql = "SELECT * FROM followup_clients ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $sql = "SELECT * FROM followup_clients WHERE added_by = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $clients,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords
        ]
    ]);
}

function getFollowupClient() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    // Get user role and ID from session
    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;
    
    // Build query based on user role
    if ($userRole === 'master') {
        $stmt = $pdo->prepare("SELECT * FROM followup_clients WHERE id = :id");
        $stmt->execute([':id' => $id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM followup_clients WHERE id = :id AND added_by = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        throw new Exception('Client not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $client
    ]);
}

function updateFollowupClient() {
    global $pdo;
    ensureTableExists();
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        throw new Exception('Invalid client ID');
    }
    
    if (empty($_POST['name'])) {
        throw new Exception('Name is required');
    }
    
    if (empty($_POST['email']) && empty($_POST['phone'])) {
        throw new Exception('Either Email or Phone is required');
    }
    
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    // Check for duplicates
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $id]);
        if ($stmt->fetch()) {
            throw new Exception('Email already exists');
        }
    }
    
    if (!empty($phone)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE phone = :phone AND id != :id");
        $stmt->execute([':phone' => $phone, ':id' => $id]);
        if ($stmt->fetch()) {
            throw new Exception('Phone already exists');
        }
    }
    
    $sql = "UPDATE followup_clients 
            SET name = :name, email = :email, phone = :phone, company = :company, followup_message = :followup_message, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':name' => $_POST['name'],
        ':email' => $email,
        ':phone' => $phone,
        ':company' => $_POST['company'] ?? '',
        ':followup_message' => $_POST['followup_message'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client updated successfully'
    ]);
}

function addFollowupClient() {
    global $pdo;
    ensureTableExists();
    
    if (empty($_POST['name'])) {
        throw new Exception('Name is required');
    }
    
    if (empty($_POST['email']) && empty($_POST['phone'])) {
        throw new Exception('Either Email or Phone is required');
    }
    
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    // Check for duplicates
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            throw new Exception('Email already exists');
        }
    }
    
    if (!empty($phone)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE phone = :phone");
        $stmt->execute([':phone' => $phone]);
        if ($stmt->fetch()) {
            throw new Exception('Phone already exists');
        }
    }
    
    $sql = "INSERT INTO followup_clients (name, email, phone, company, followup_message, added_by, created_at)
            VALUES (:name, :email, :phone, :company, :followup_message, :added_by, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $_POST['name'],
        ':email' => $email,
        ':phone' => $phone,
        ':company' => $_POST['company'] ?? '',
        ':followup_message' => $_POST['followup_message'] ?? '',
        ':added_by' => $_SESSION['user_id'] ?? null
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client added successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

function deleteFollowupClient() {
    global $pdo;
    ensureTableExists();
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        throw new Exception('Invalid client ID');
    }
    
    $stmt = $pdo->prepare("DELETE FROM followup_clients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Client deleted successfully'
        ]);
    } else {
        throw new Exception('Client not found');
    }
}
?>
