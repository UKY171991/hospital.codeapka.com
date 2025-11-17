<?php
// ajax/client_api.php - Client and Task Management API
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
        // Client operations
        case 'get_clients':
            getClients();
            break;
        case 'get_client':
            getClient();
            break;
        case 'add_client':
            addClient();
            break;
        case 'update_client':
            updateClient();
            break;
        case 'delete_client':
            deleteClient();
            break;
            
        // Task operations
        case 'get_tasks':
            getTasks();
            break;
        case 'get_task':
            getTask();
            break;
        case 'add_task':
            addTask();
            break;
        case 'update_task':
            updateTask();
            break;
        case 'delete_task':
            deleteTask();
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

// Ensure tables exist
function ensureTablesExist() {
    global $pdo;
    
    // Create clients table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `clients` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(255) DEFAULT NULL,
        `phone` varchar(20) NOT NULL,
        `company` varchar(255) DEFAULT NULL,
        `address` text DEFAULT NULL,
        `city` varchar(100) DEFAULT NULL,
        `state` varchar(100) DEFAULT NULL,
        `zip` varchar(10) DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Create tasks table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `tasks` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `client_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text NOT NULL,
        `priority` enum('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
        `status` enum('Pending','In Progress','Completed','On Hold') NOT NULL DEFAULT 'Pending',
        `due_date` date DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `client_id` (`client_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

// Client Functions
function getClients() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "SELECT * FROM clients ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $clients
    ]);
}

function getClient() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        throw new Exception('Client not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $client
    ]);
}

function addClient() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "INSERT INTO clients (name, email, phone, company, address, city, state, zip, notes, created_at)
            VALUES (:name, :email, :phone, :company, :address, :city, :state, :zip, :notes, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $_POST['name'],
        ':email' => $_POST['email'] ?? '',
        ':phone' => $_POST['phone'],
        ':company' => $_POST['company'] ?? '',
        ':address' => $_POST['address'] ?? '',
        ':city' => $_POST['city'] ?? '',
        ':state' => $_POST['state'] ?? '',
        ':zip' => $_POST['zip'] ?? '',
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client added successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

function updateClient() {
    global $pdo;
    
    $sql = "UPDATE clients 
            SET name = :name, email = :email, phone = :phone, company = :company,
                address = :address, city = :city, state = :state, zip = :zip, 
                notes = :notes, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $_POST['id'],
        ':name' => $_POST['name'],
        ':email' => $_POST['email'] ?? '',
        ':phone' => $_POST['phone'],
        ':company' => $_POST['company'] ?? '',
        ':address' => $_POST['address'] ?? '',
        ':city' => $_POST['city'] ?? '',
        ':state' => $_POST['state'] ?? '',
        ':zip' => $_POST['zip'] ?? '',
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client updated successfully'
    ]);
}

function deleteClient() {
    global $pdo;
    
    $id = intval($_POST['id'] ?? 0);
    
    // Check if client has tasks
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tasks WHERE client_id = :id");
    $stmt->execute([':id' => $id]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($count > 0) {
        throw new Exception('Cannot delete client with existing tasks');
    }
    
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Client deleted successfully'
    ]);
}

// Task Functions
function getTasks() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "SELECT t.*, c.name as client_name 
            FROM tasks t
            LEFT JOIN clients c ON t.client_id = c.id
            ORDER BY t.created_at DESC";
    
    $stmt = $pdo->query($sql);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $tasks
    ]);
}

function getTask() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    $stmt = $pdo->prepare("SELECT t.*, c.name as client_name 
                           FROM tasks t
                           LEFT JOIN clients c ON t.client_id = c.id
                           WHERE t.id = :id");
    $stmt->execute([':id' => $id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$task) {
        throw new Exception('Task not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $task
    ]);
}

function addTask() {
    global $pdo;
    ensureTablesExist();
    
    $sql = "INSERT INTO tasks (client_id, title, description, priority, status, due_date, notes, created_at)
            VALUES (:client_id, :title, :description, :priority, :status, :due_date, :notes, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':client_id' => $_POST['client_id'],
        ':title' => $_POST['title'],
        ':description' => $_POST['description'],
        ':priority' => $_POST['priority'],
        ':status' => $_POST['status'],
        ':due_date' => $_POST['due_date'] ?: null,
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Task added successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

function updateTask() {
    global $pdo;
    
    $sql = "UPDATE tasks 
            SET client_id = :client_id, title = :title, description = :description,
                priority = :priority, status = :status, due_date = :due_date, 
                notes = :notes, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $_POST['id'],
        ':client_id' => $_POST['client_id'],
        ':title' => $_POST['title'],
        ':description' => $_POST['description'],
        ':priority' => $_POST['priority'],
        ':status' => $_POST['status'],
        ':due_date' => $_POST['due_date'] ?: null,
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Task updated successfully'
    ]);
}

function deleteTask() {
    global $pdo;
    
    $id = intval($_POST['id'] ?? 0);
    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Task deleted successfully'
    ]);
}
?>
