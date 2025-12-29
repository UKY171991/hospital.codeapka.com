<?php
// ajax/followup_templates_api.php - Followup Templates Management API
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
        case 'get_templates':
            getTemplates();
            break;
        case 'get_template':
            getTemplate();
            break;
        case 'add_template':
            addTemplate();
            break;
        case 'update_template':
            updateTemplate();
            break;
        case 'delete_template':
            deleteTemplate();
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
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS `followup_templates` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `template_name` varchar(255) NOT NULL,
        `content` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        `created_by` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function getTemplates() {
    global $pdo;
    ensureTableExists();
    
    // Get user role and ID from session
    $userRole = $_SESSION['role'] ?? 'user';
    $userId = $_SESSION['user_id'] ?? null;
    
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    // Build query based on user role (everyone can see all templates for now, or filter by user?)
    // For simplicity, let's allow everyone to see all templates, or maybe filter by created_by if needed.
    // Let's assume shared templates for now.
    
    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM followup_templates");
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalRecords / $limit);
    
    $sql = "SELECT * FROM followup_templates ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $templates,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalRecords
        ]
    ]);
}

function getTemplate() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    
    $stmt = $pdo->prepare("SELECT * FROM followup_templates WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$template) {
        throw new Exception('Template not found');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $template
    ]);
}

function addTemplate() {
    global $pdo;
    ensureTableExists();
    
    if (empty($_POST['template_name'])) {
        throw new Exception('Template Name is required');
    }
    
    if (empty($_POST['content'])) {
        throw new Exception('Template Content is required');
    }
    
    $userId = $_SESSION['user_id'] ?? null;
    
    $sql = "INSERT INTO followup_templates (template_name, content, created_by, created_at)
            VALUES (:template_name, :content, :created_by, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':template_name' => $_POST['template_name'],
        ':content' => $_POST['content'],
        ':created_by' => $userId
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Template added successfully',
        'id' => $pdo->lastInsertId()
    ]);
}

function updateTemplate() {
    global $pdo;
    ensureTableExists();
    
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        throw new Exception('Invalid template ID');
    }
    
    if (empty($_POST['template_name'])) {
        throw new Exception('Template Name is required');
    }
    
    if (empty($_POST['content'])) {
        throw new Exception('Template Content is required');
    }
    
    $sql = "UPDATE followup_templates 
            SET template_name = :template_name, content = :content, updated_at = NOW()
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':template_name' => $_POST['template_name'],
        ':content' => $_POST['content']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Template updated successfully'
    ]);
}

function deleteTemplate() {
    global $pdo;
    ensureTableExists();
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        throw new Exception('Invalid template ID');
    }
    
    $stmt = $pdo->prepare("DELETE FROM followup_templates WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Template deleted successfully'
        ]);
    } else {
        throw new Exception('Template not found');
    }
}
?>
