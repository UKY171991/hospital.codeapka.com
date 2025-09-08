<?php
/**
 * Test Category API - Comprehensive CRUD operations for test categories
 * Supports: CREATE, READ, UPDATE, DELETE operations
 * Authentication: Multiple methods supported
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key, X-API-Secret');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';

// Entity Configuration for Test Categories
$entity_config = [
    'table_name' => 'categories',
    'id_field' => 'id',
    'required_fields' => ['name'],
    'allowed_fields' => ['name', 'description'],
    'permission_map' => [
        'list' => 'read',
        'get' => 'read', 
        'save' => 'write',
        'delete' => 'delete'
    ]
];

// Get action from request
$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map HTTP methods to actions
switch($requestMethod) {
    case 'GET':
        $action = isset($_GET['id']) ? 'get' : 'list';
        break;
    case 'POST':
        $action = $_REQUEST['action'] ?? 'save';
        break;
    case 'PUT':
        $action = 'save';
        break;
    case 'DELETE':
        $action = 'delete';
        break;
}

try {
    // Authenticate user
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
        exit;
    }

    // Check permissions
    $required_permission = $entity_config['permission_map'][$action] ?? 'read';
    if (!checkPermission($user_data, $required_permission)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
        exit;
    }

    switch($action) {
        case 'list':
            handleList($pdo, $entity_config);
            break;
            
        case 'get':
            handleGet($pdo, $entity_config);
            break;
            
        case 'save':
            handleSave($pdo, $entity_config, $user_data);
            break;
            
        case 'delete':
            handleDelete($pdo, $entity_config);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    error_log("Test Category API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

function handleList($pdo, $config) {
    try {
        $sql = "SELECT tc.*, 
                       COUNT(t.id) as test_count 
                FROM {$config['table_name']} tc 
                LEFT JOIN tests t ON tc.id = t.category_id 
                GROUP BY tc.id 
                ORDER BY tc.category_name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $categories,
            'total' => count($categories)
        ]);
    } catch (Exception $e) {
        error_log("List test categories error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch test categories']);
    }
}

function handleGet($pdo, $config) {
    try {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Category ID is required']);
            return;
        }

        $sql = "SELECT tc.*, 
                       COUNT(t.id) as test_count 
                FROM {$config['table_name']} tc 
                LEFT JOIN tests t ON tc.id = t.category_id 
                WHERE tc.{$config['id_field']} = ?
                GROUP BY tc.id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Test category not found']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $category]);
    } catch (Exception $e) {
        error_log("Get test category error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch test category']);
    }
}

function handleSave($pdo, $config, $user_data) {
    try {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate required fields
        foreach ($config['required_fields'] as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                return;
            }
        }

        // Check for duplicate category name
        $id = $input['id'] ?? null;
        $check_sql = "SELECT id FROM {$config['table_name']} WHERE category_name = ?";
        if ($id) {
            $check_sql .= " AND id != ?";
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['category_name'], $id]);
        } else {
            $stmt = $pdo->prepare($check_sql);
            $stmt->execute([$input['category_name']]);
        }
        
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Category name already exists']);
            return;
        }

        // Prepare data for saving
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        $is_update = !empty($id);

        if ($is_update) {
            // Update existing category
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
        } else {
            // Create new category
            $fields = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($fields) VALUES ($placeholders)";
            $values = array_values($data);
        }

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($values);

        if ($result) {
            $category_id = $is_update ? $id : $pdo->lastInsertId();
            
            // Fetch the saved category
            $stmt = $pdo->prepare("SELECT tc.*, 
                                           COUNT(t.id) as test_count 
                                   FROM {$config['table_name']} tc 
                                   LEFT JOIN tests t ON tc.id = t.category_id 
                                   WHERE tc.{$config['id_field']} = ?
                                   GROUP BY tc.id");
            $stmt->execute([$category_id]);
            $saved_category = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'message' => $is_update ? 'Test category updated successfully' : 'Test category created successfully',
                'data' => $saved_category
            ]);
        } else {
            throw new Exception('Failed to save test category');
        }

    } catch (Exception $e) {
        error_log("Save test category error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save test category']);
    }
}

function handleDelete($pdo, $config) {
    try {
        $id = $_REQUEST['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Category ID is required']);
            return;
        }

        // Check if category exists
        $stmt = $pdo->prepare("SELECT id FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Test category not found']);
            return;
        }

        // Check if category is used in tests (prevent deletion if used)
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tests WHERE category_id = ?");
        $stmt->execute([$id]);
        $usage = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usage['count'] > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot delete category that has associated tests']);
            return;
        }

        // Delete the category
        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Test category deleted successfully']);
        } else {
            throw new Exception('Failed to delete test category');
        }

    } catch (Exception $e) {
        error_log("Delete test category error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete test category']);
    }
}
?>
