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

// Entity Configuration for Test Categories (match DB categories table)
$entity_config = [
    'table_name' => 'categories',
    'id_field' => 'id',
    'required_fields' => ['name'],
    'allowed_fields' => ['name', 'description', 'added_by'],
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
    $sql = "SELECT c.*, 
               COUNT(t.id) as test_count 
        FROM {$config['table_name']} c 
        LEFT JOIN tests t ON c.id = t.category_id 
        GROUP BY c.id 
        ORDER BY c.name";
        
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

    $sql = "SELECT c.*, 
               COUNT(t.id) as test_count 
        FROM {$config['table_name']} c 
        LEFT JOIN tests t ON c.id = t.category_id 
        WHERE c.{$config['id_field']} = ?
        GROUP BY c.id";
        
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

        // Prepare data for saving
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        $id = $input['id'] ?? null;
        $is_update = !empty($id);
        
        // Set added_by for new records
        if (!$is_update && !isset($data['added_by'])) {
            $data['added_by'] = $user_data['user_id'] ?? ($user_data['id'] ?? null);
        }

        if ($is_update) {
            // Update existing category - only update provided fields
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
                return;
            }
            
            // Check if category exists
            $stmt = $pdo->prepare("SELECT * FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Category not found']);
                return;
            }
            
            // Check for duplicate name if name is being updated
            if (isset($data['name']) && $data['name'] !== $existing['name']) {
                $stmt = $pdo->prepare("SELECT id FROM {$config['table_name']} WHERE name = ? AND id != ?");
                $stmt->execute([$data['name'], $id]);
                if ($stmt->fetch()) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Category name already exists']);
                    return;
                }
            }
            
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($values);
            $category_id = $id;
            $action = 'updated';
        } else {
            // Create new category using upsert logic to prevent duplicates
            
            // Define unique criteria for duplicate detection
            $uniqueWhere = [
                'name' => $data['name']
            ];
            
            // Use upsert function to handle duplicates properly
            $result_info = upsert_or_skip($pdo, $config['table_name'], $uniqueWhere, $data);
            $category_id = $result_info['id'];
            $action = $result_info['action'];
            $result = true;
        }

        if ($result) {            
            // Fetch the saved category
            $stmt = $pdo->prepare("SELECT c.*, 
                           COUNT(t.id) as test_count 
                       FROM {$config['table_name']} c 
                       LEFT JOIN tests t ON c.id = t.category_id 
                       WHERE c.{$config['id_field']} = ?
                       GROUP BY c.id");
            $stmt->execute([$category_id]);
            $saved_category = $stmt->fetch(PDO::FETCH_ASSOC);

            $message = match($action) {
                'inserted' => 'Test category created successfully',
                'updated' => 'Test category updated successfully', 
                'skipped' => 'Test category already exists (no changes needed)',
                default => 'Test category saved successfully'
            };

            echo json_encode([
                'success' => true,
                'message' => $message,
                'data' => $saved_category,
                'action' => $action,
                'id' => $category_id
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
