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
        echo json_encode(['debug' => 'handleSave: after json_decode', 'input' => $input]);
        
        // Validate required fields
        echo json_encode(['debug' => 'handleSave: before required fields validation']);
        foreach ($config['required_fields'] as $field) {
            if (empty($input[$field])) {
                echo json_encode(['debug' => 'handleSave: required field empty', 'field' => $field]);
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                return;
            }
        }
        echo json_encode(['debug' => 'handleSave: after required fields validation']);

        // Additional validation for tests
        echo json_encode(['debug' => 'handleSave: before category_id numeric check']);
        if (!is_numeric($input['category_id'])) {
            echo json_encode(['debug' => 'handleSave: category_id not numeric', 'category_id' => $input['category_id']]);
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
            return;
        }
        echo json_encode(['debug' => 'handleSave: after category_id numeric check']);

        // Check if category exists
        echo json_encode(['debug' => 'handleSave: before category exists check']);
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
        $stmt->execute([$input['category_id']]);
        if (!$stmt->fetch()) {
            echo json_encode(['debug' => 'handleSave: category does not exist', 'category_id' => $input['category_id']]);
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Category does not exist']);
            return;
        }
        echo json_encode(['debug' => 'handleSave: after category exists check']);

        // Prepare data for saving
        echo json_encode(['debug' => 'handleSave: before data array init']);
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }
        echo json_encode(['debug' => 'handleSave: after data array init', 'data' => $data]);

        // Set added_by for new records
        $id = $input['id'] ?? null;
        $is_update = !empty($id);
        
        echo json_encode(['debug' => 'handleSave: before added_by check', 'is_update' => $is_update, 'data_added_by_isset' => isset($data['added_by'])] );
        if (!$is_update && !isset($data['added_by'])) {
            $data['added_by'] = $user_data['user_id'] ?? ($user_data['id'] ?? null);
        }
        echo json_encode(['debug' => 'handleSave: after added_by check', 'data_added_by' => ($data['added_by'] ?? 'N/A')]);

        if ($is_update) {
            // Update existing test - only update provided fields
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
                return;
            }
            
            // Check if test exists
            $stmt = $pdo->prepare("SELECT * FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Test not found']);
                return;
            }
            
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($values);
            $test_id = $id;
            $action = 'updated';
        } else {
            // Create new test using upsert logic to prevent duplicates
            
            // Define unique criteria for duplicate detection
            $uniqueWhere = [
                'name' => $data['name'],
                'category_id' => $data['category_id']
            ];
            echo json_encode(['debug' => 'handleSave: before upsert_or_skip', 'uniqueWhere' => $uniqueWhere, 'data' => $data]);
            
            // Use upsert function to handle duplicates properly
            $result_info = upsert_or_skip($pdo, $config['table_name'], $uniqueWhere, $data);
            echo json_encode(['debug' => 'handleSave: after upsert_or_skip', 'result_info' => $result_info]);
            $test_id = $result_info['id'];
            $action = $result_info['action'];
            $result = true;
        }
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        // Set added_by for new records
        $id = $input['id'] ?? null;
        $is_update = !empty($id);
        
        if (!$is_update && !isset($data['added_by'])) {
            $data['added_by'] = $user_data['user_id'] ?? ($user_data['id'] ?? null);
        }

        if ($is_update) {
            // Update existing test - only update provided fields
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
                return;
            }
            
            // Check if test exists
            $stmt = $pdo->prepare("SELECT * FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existing) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Test not found']);
                return;
            }
            
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($values);
            $test_id = $id;
            $action = 'updated';
        } else {
            // Create new test using upsert logic to prevent duplicates
            
            // Define unique criteria for duplicate detection
            $uniqueWhere = [
                'name' => $data['name'],
                'category_id' => $data['category_id']
            ];
            
            // Use upsert function to handle duplicates properly
            $result_info = upsert_or_skip($pdo, $config['table_name'], $uniqueWhere, $data);
            $test_id = $result_info['id'];
            $action = $result_info['action'];
            $result = true;
        }

        if ($result) {            
            // Fetch the saved test
            $stmt = $pdo->prepare("SELECT t.*, c.name as category_name 
                                   FROM {$config['table_name']} t 
                                   LEFT JOIN categories c ON t.category_id = c.id 
                                   WHERE t.{$config['id_field']} = ?");
            $stmt->execute([$test_id]);
            $saved_test = $stmt->fetch(PDO::FETCH_ASSOC);

            $message = match($action) {
                'inserted' => 'Test created successfully',
                'updated' => 'Test updated successfully', 
                'skipped' => 'Test already exists (no changes needed)',
                default => 'Test saved successfully'
            };

            echo json_encode([
                'success' => true,
                'message' => $message,
                'data' => $saved_test,
                'action' => $action,
                'id' => $test_id
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