<?php
/**
 * Test API - Comprehensive CRUD operations for tests
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

// Entity Configuration for Tests (match DB schema)
$entity_config = [
    'table_name' => 'tests',
    'id_field' => 'id',
    'required_fields' => ['name', 'category_id'],
    'allowed_fields' => [
        'name', 'category_id', 'method', 'price', 'description',
        'min_male', 'max_male', 'min_female', 'max_female',
        'min', 'max', 'unit', 'default_result', 'reference_range',
        'test_code', 'shortcut', 'sub_heading', 'print_new_page', 'specimen', 'added_by'
    ],
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
    error_log("Test API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

function handleList($pdo, $config) {
    try {
    $sql = "SELECT t.*, c.name as category_name 
        FROM {$config['table_name']} t 
        LEFT JOIN categories c ON t.category_id = c.id 
        ORDER BY t.name";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $tests,
            'total' => count($tests)
        ]);
    } catch (Exception $e) {
        error_log("List tests error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch tests']);
    }
}

function handleGet($pdo, $config) {
    try {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Test ID is required']);
            return;
        }

    $sql = "SELECT t.*, c.name as category_name 
        FROM {$config['table_name']} t 
        LEFT JOIN categories c ON t.category_id = c.id 
        WHERE t.{$config['id_field']} = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$test) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Test not found']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $test]);
    } catch (Exception $e) {
        error_log("Get test error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch test']);
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

        // Additional validation for tests
        if (!is_numeric($input['category_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
            return;
        }

        // Check if category exists
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->execute([$input['category_id']]);
        if (!$stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Category does not exist']);
            return;
        }

        // Prepare data for saving
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

    // Set added_by if creating

        $id = $input['id'] ?? null;
        $is_update = !empty($id);

        if ($is_update) {
            // Update existing test
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
        } else {
            // Create new test
            $fields = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($fields) VALUES ($placeholders)";
            $values = array_values($data);
        }

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($values);

        if ($result) {
            $test_id = $is_update ? $id : $pdo->lastInsertId();
            
            // Fetch the saved test
            $stmt = $pdo->prepare("SELECT t.*, c.name as category_name 
                                   FROM {$config['table_name']} t 
                                   LEFT JOIN categories c ON t.category_id = c.id 
                                   WHERE t.{$config['id_field']} = ?");
            $stmt->execute([$test_id]);
            $saved_test = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'message' => $is_update ? 'Test updated successfully' : 'Test created successfully',
                'data' => $saved_test
            ]);
        } else {
            throw new Exception('Failed to save test');
        }

    } catch (Exception $e) {
        error_log("Save test error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save test']);
    }
}

function handleDelete($pdo, $config) {
    try {
        $id = $_REQUEST['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Test ID is required']);
            return;
        }

        // Check if test exists
        $stmt = $pdo->prepare("SELECT id FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Test not found']);
            return;
        }

        // Check if test is used in entries (prevent deletion if used)
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM entries WHERE test_id = ?");
        $stmt->execute([$id]);
        $usage = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usage['count'] > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot delete test that is used in test entries']);
            return;
        }

        // Delete the test
        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Test deleted successfully']);
        } else {
            throw new Exception('Failed to delete test');
        }

    } catch (Exception $e) {
        error_log("Delete test error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete test']);
    }
}
?>
