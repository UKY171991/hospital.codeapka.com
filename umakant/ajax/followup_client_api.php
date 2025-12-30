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
        case 'update_response':
            updateResponse();
            break;
        case 'get_responses':
            getResponses();
            break;
        case 'edit_response':
            editResponse();
            break;
        case 'delete_response':
            deleteResponse();
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
    
    // Add response_message column if it doesn't exist
    $pdo->exec("ALTER TABLE followup_clients ADD COLUMN IF NOT EXISTS `response_message` text DEFAULT NULL AFTER `followup_title`");

    // Create client_responses table for history
    $pdo->exec("CREATE TABLE IF NOT EXISTS `client_responses` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `client_id` int(11) NOT NULL,
        `response_message` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `client_id` (`client_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function getFollowupClients() {
    global $pdo;
    ensureTableExists();
    
    // Get user role and ID from session
    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search = trim($_GET['search'] ?? '');
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    $whereClauses = [];
    $params = [];
    
    if ($userRole !== 'master') {
        $whereClauses[] = "added_by = :user_id";
        $params[':user_id'] = $userId;
    }
    
    if (!empty($search)) {
        $whereClauses[] = "(name LIKE :search_name OR phone LIKE :search_phone OR email LIKE :search_email OR company LIKE :search_company)";
        $params[':search_name'] = "%$search%";
        $params[':search_phone'] = "%$search%";
        $params[':search_email'] = "%$search%";
        $params[':search_company'] = "%$search%";
    }
    
    $whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM followup_clients $whereSql";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    // Get records
    $sql = "SELECT * FROM followup_clients $whereSql ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
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
    
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = preg_replace('/[^\d+]/', '', $_POST['phone'] ?? ''); // Clean phone: allow digits and +
    $name = trim($_POST['name'] ?? '');
    
    // Check for duplicates
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE email = :email AND id != :id");
        $stmt->execute([':email' => $email, ':id' => $id]);
        if ($stmt->fetch()) {
            throw new Exception('A client with this email already exists');
        }
    }
    
    if (!empty($phone)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE phone = :phone AND id != :id");
        $stmt->execute([':phone' => $phone, ':id' => $id]);
        if ($stmt->fetch()) {
            throw new Exception('A client with this phone number already exists');
        }
    }

    // Optional: Check for duplicate Name + Phone combination if strict uniqueness is needed
    // But since phone is already checked globally, this is redundant unless we allow same phone for different names.
    
    $sql = "UPDATE followup_clients 
            SET name = :name, email = :email, phone = :phone, company = :company, 
                followup_message = :followup_message, followup_title = :followup_title, 
                response_message = :response_message, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':company' => $_POST['company'] ?? '',
        ':followup_message' => $_POST['followup_message'] ?? '',
        ':followup_title' => $_POST['followup_title'] ?? '',
        ':response_message' => $_POST['response_message'] ?? ''
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
    
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = preg_replace('/[^\d+]/', '', $_POST['phone'] ?? ''); // Clean phone: allow digits and +
    $name = trim($_POST['name'] ?? '');
    
    // Check for duplicates
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            throw new Exception('A client with this email already exists');
        }
    }
    
    if (!empty($phone)) {
        $stmt = $pdo->prepare("SELECT id FROM followup_clients WHERE phone = :phone");
        $stmt->execute([':phone' => $phone]);
        if ($stmt->fetch()) {
            throw new Exception('A client with this phone number already exists');
        }
    }

    // Check if a client with the same name and phone/email already exists
    // (This is mostly covered by the above, but good for clarity)
    
    $sql = "INSERT INTO followup_clients (name, email, phone, company, followup_message, followup_title, added_by, created_at)
            VALUES (:name, :email, :phone, :company, :followup_message, :followup_title, :added_by, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':company' => $_POST['company'] ?? '',
        ':followup_message' => $_POST['followup_message'] ?? '',
        ':followup_title' => $_POST['followup_title'] ?? '',
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
function updateResponse() {
    global $pdo;
    $id = intval($_POST['id'] ?? 0);
    $response = $_POST['response_message'] ?? '';
    if ($id <= 0) throw new Exception('Invalid ID');
    if (empty($response)) throw new Exception('Response message cannot be empty');
    
    // Insert into history table
    $stmt = $pdo->prepare("INSERT INTO client_responses (client_id, response_message, created_at) VALUES (:client_id, :response, NOW())");
    $stmt->execute([':client_id' => $id, ':response' => $response]);
    
    // Also update last response in main table for reference
    $stmt = $pdo->prepare("UPDATE followup_clients SET response_message = :response, updated_at = NOW() WHERE id = :id");
    $stmt->execute([':response' => $response, ':id' => $id]);
    
    echo json_encode(['success' => true, 'message' => 'Response added successfully']);
}

function getResponses() {
    global $pdo;
    $client_id = intval($_GET['client_id'] ?? 0);
    if ($client_id <= 0) throw new Exception('Invalid Client ID');
    
    $stmt = $pdo->prepare("SELECT * FROM client_responses WHERE client_id = :client_id ORDER BY created_at DESC");
    $stmt->execute([':client_id' => $client_id]);
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $responses]);
}

function editResponse() {
    global $pdo;
    $id = intval($_POST['id'] ?? 0);
    $response = $_POST['response_message'] ?? '';
    if ($id <= 0) throw new Exception('Invalid ID');
    if (empty($response)) throw new Exception('Response message cannot be empty');
    
    $stmt = $pdo->prepare("UPDATE client_responses SET response_message = :response WHERE id = :id");
    $stmt->execute([':response' => $response, ':id' => $id]);
    
    echo json_encode(['success' => true, 'message' => 'Response updated successfully']);
}

function deleteResponse() {
    global $pdo;
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) throw new Exception('Invalid ID');
    
    $stmt = $pdo->prepare("DELETE FROM client_responses WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    echo json_encode(['success' => true, 'message' => 'Response deleted successfully']);
}
?>
