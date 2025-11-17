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
        // Dashboard
        case 'get_dashboard_stats':
            getDashboardStats();
            break;
        case 'get_recent_clients':
            getRecentClients();
            break;
        case 'get_recent_tasks':
            getRecentTasks();
            break;
            
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
    http_response_code(200); // Keep 200 to allow AJAX to process the response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
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
        `website_urls` text DEFAULT NULL,
        `screenshots` text DEFAULT NULL,
        `notes` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `client_id` (`client_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Add website_urls and screenshots columns if they don't exist
    try {
        $pdo->exec("ALTER TABLE `tasks` ADD COLUMN `website_urls` text DEFAULT NULL AFTER `due_date`");
    } catch (Exception $e) {
        // Column already exists
    }
    try {
        $pdo->exec("ALTER TABLE `tasks` ADD COLUMN `screenshots` text DEFAULT NULL AFTER `website_urls`");
    } catch (Exception $e) {
        // Column already exists
    }
}

// Dashboard Functions
function getDashboardStats() {
    global $pdo;
    ensureTablesExist();
    
    // Total clients
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clients");
    $totalClients = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total tasks
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tasks");
    $totalTasks = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Task status counts
    $stmt = $pdo->query("SELECT 
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'On Hold' THEN 1 ELSE 0 END) as on_hold
        FROM tasks");
    $taskStatus = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Task priority counts
    $stmt = $pdo->query("SELECT 
        SUM(CASE WHEN priority = 'Low' THEN 1 ELSE 0 END) as low,
        SUM(CASE WHEN priority = 'Medium' THEN 1 ELSE 0 END) as medium,
        SUM(CASE WHEN priority = 'High' THEN 1 ELSE 0 END) as high,
        SUM(CASE WHEN priority = 'Urgent' THEN 1 ELSE 0 END) as urgent
        FROM tasks");
    $taskPriority = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_clients' => $totalClients,
            'total_tasks' => $totalTasks,
            'pending_tasks' => $taskStatus['pending'] ?? 0,
            'completed_tasks' => $taskStatus['completed'] ?? 0,
            'task_status' => $taskStatus,
            'task_priority' => $taskPriority
        ]
    ]);
}

function getRecentClients() {
    global $pdo;
    ensureTablesExist();
    
    $limit = intval($_GET['limit'] ?? 5);
    
    $stmt = $pdo->prepare("SELECT * FROM clients ORDER BY created_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $clients
    ]);
}

function getRecentTasks() {
    global $pdo;
    ensureTablesExist();
    
    $limit = intval($_GET['limit'] ?? 5);
    
    $stmt = $pdo->prepare("SELECT t.*, c.name as client_name 
                           FROM tasks t
                           LEFT JOIN clients c ON t.client_id = c.id
                           ORDER BY t.created_at DESC 
                           LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $tasks
    ]);
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
    ensureTablesExist();
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid client ID'
        ]);
        return;
    }
    
    try {
        // Check if client has tasks
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tasks WHERE client_id = :id");
        $stmt->execute([':id' => $id]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Cannot delete client with existing tasks. Please delete or reassign the tasks first.'
            ]);
            return;
        }
        
        // Delete the client
        $stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Client deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Client not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
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
    
    // Handle file uploads
    $screenshots = [];
    if (isset($_FILES['screenshots'])) {
        $screenshots = handleScreenshotUploads($_FILES['screenshots']);
    }
    
    $sql = "INSERT INTO tasks (client_id, title, description, priority, status, due_date, website_urls, screenshots, notes, created_at)
            VALUES (:client_id, :title, :description, :priority, :status, :due_date, :website_urls, :screenshots, :notes, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':client_id' => $_POST['client_id'],
        ':title' => $_POST['title'],
        ':description' => $_POST['description'],
        ':priority' => $_POST['priority'],
        ':status' => $_POST['status'],
        ':due_date' => $_POST['due_date'] ?: null,
        ':website_urls' => $_POST['website_urls'] ?? '',
        ':screenshots' => json_encode($screenshots),
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
    
    // Get existing screenshots
    $stmt = $pdo->prepare("SELECT screenshots FROM tasks WHERE id = :id");
    $stmt->execute([':id' => $_POST['id']]);
    $existingTask = $stmt->fetch(PDO::FETCH_ASSOC);
    $existingScreenshots = json_decode($existingTask['screenshots'] ?? '[]', true);
    
    // Handle new file uploads
    $newScreenshots = [];
    if (isset($_FILES['screenshots'])) {
        $newScreenshots = handleScreenshotUploads($_FILES['screenshots']);
    }
    
    // Merge existing and new screenshots
    $allScreenshots = array_merge($existingScreenshots, $newScreenshots);
    
    // Handle screenshot deletions
    if (isset($_POST['delete_screenshots'])) {
        $deleteScreenshots = json_decode($_POST['delete_screenshots'], true);
        foreach ($deleteScreenshots as $screenshot) {
            // Remove from array
            $allScreenshots = array_filter($allScreenshots, function($s) use ($screenshot) {
                return $s !== $screenshot;
            });
            // Delete file
            $filePath = '../uploads/screenshots/' . basename($screenshot);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $allScreenshots = array_values($allScreenshots); // Re-index array
    }
    
    $sql = "UPDATE tasks 
            SET client_id = :client_id, title = :title, description = :description,
                priority = :priority, status = :status, due_date = :due_date, 
                website_urls = :website_urls, screenshots = :screenshots, notes = :notes, updated_at = NOW()
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
        ':website_urls' => $_POST['website_urls'] ?? '',
        ':screenshots' => json_encode($allScreenshots),
        ':notes' => $_POST['notes'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Task updated successfully'
    ]);
}

function deleteTask() {
    global $pdo;
    ensureTablesExist();
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid task ID'
        ]);
        return;
    }
    
    try {
        // Get task screenshots before deleting
        $stmt = $pdo->prepare("SELECT screenshots FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($task) {
            // Delete screenshot files
            $screenshots = json_decode($task['screenshots'] ?? '[]', true);
            foreach ($screenshots as $screenshot) {
                $filePath = '../uploads/screenshots/' . basename($screenshot);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
        
        // Delete task
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Task and associated screenshots deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Task not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

function handleScreenshotUploads($files) {
    $uploadedFiles = [];
    $uploadDir = '../uploads/screenshots/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Handle multiple file uploads
    if (is_array($files['name'])) {
        $fileCount = count($files['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = time() . '_' . $i . '_' . basename($files['name'][$i]);
                $targetPath = $uploadDir . $fileName;
                
                // Validate file type
                $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                        $uploadedFiles[] = 'uploads/screenshots/' . $fileName;
                    }
                }
            }
        }
    } else {
        // Single file upload
        if ($files['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . '_' . basename($files['name']);
            $targetPath = $uploadDir . $fileName;
            
            $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($files['tmp_name'], $targetPath)) {
                    $uploadedFiles[] = 'uploads/screenshots/' . $fileName;
                }
            }
        }
    }
    
    return $uploadedFiles;
}
?>
