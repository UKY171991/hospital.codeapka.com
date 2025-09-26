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
require_once __DIR__ . '/../inc/smart_upsert.php';

// Entity Configuration for Tests
$entity_config = [
    'table_name' => 'tests',
    'id_field' => 'id',
    'required_fields' => ['name', 'category_id'],
    'allowed_fields' => [
        'name', 'category_id', 'method', 'price', 'description',
        'min_male', 'max_male', 'min_female', 'max_female',
        'min', 'max', 'unit', 'default_result', 'reference_range',
        'test_code', 'shortcut', 'sub_heading', 'print_new_page', 'specimen', 'added_by'
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
    switch($action) {
        case 'list':
            handleList($pdo, $entity_config);
            break;
        case 'get':
            handleGet($pdo, $entity_config);
            break;
        case 'save':
            handleSave($pdo, $entity_config);
            break;
        case 'delete':
            handleDelete($pdo, $entity_config);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Test API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }
    if (!checkPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list tests'], 403);
    }

    try {
        $sql = "SELECT t.*, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id ORDER BY t.name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $tests, 'total' => count($tests)]);
    } catch (Exception $e) {
        error_log("List tests error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch tests'], 500);
    }
}

function handleGet($pdo, $config) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Test ID is required'], 400);
    }

    try {
        $sql = "SELECT t.*, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id WHERE t.{$config['id_field']} = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$test) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
        }

        if (!checkPermission($user_data, 'get', $test['added_by'] ?? null)) {
            json_response(['success' => false, 'message' => 'Permission denied to view this test'], 403);
        }

        json_response(['success' => true, 'data' => $test]);
    } catch (Exception $e) {
        error_log("Get test error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch test'], 500);
    }
}

function handleSave($pdo, $config) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
        }
        if (!checkPermission($user_data, 'save', $existing['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to update this test'], 403);
        }
    } else { // Create
        if (!checkPermission($user_data, 'save')) {
            json_response(['success' => false, 'message' => 'Permission denied to create tests'], 403);
        }
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    try {
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }
        $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];

        if ($id) {
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $test_id = $id;
            $action = 'updated';
        } else {
            // Use smart upsert to prevent duplicates
            $uniqueWhere = getUniqueWhere('test', $data);
            
            if (empty($uniqueWhere)) {
                json_response(['success' => false, 'message' => 'Cannot determine unique criteria for duplicate prevention'], 400);
            }

            // Use smart upsert function
            $result = smartUpsert($pdo, $config['table_name'], $uniqueWhere, $data, [
                'compare_timestamps' => true,
                'force_update' => false
            ]);
            
            if ($result['action'] === 'error') {
                json_response(['success' => false, 'message' => $result['message']], 500);
            }
            
            $test_id = $result['id'];
            $action = $result['action'];
        }

        $stmt = $pdo->prepare("SELECT t.*, c.name as category_name FROM {$config['table_name']} t LEFT JOIN categories c ON t.category_id = c.id WHERE t.id = ?");
        $stmt->execute([$test_id]);
        $saved_test = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response([
            'success' => true,
            'message' => "Test {$action} successfully",
            'data' => $saved_test,
            'id' => $test_id
        ]);
    } catch (Exception $e) {
        error_log("Save test error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to save test'], 500);
    }
}

function handleDelete($pdo, $config) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Test ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $test = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$test) {
            json_response(['success' => false, 'message' => 'Test not found'], 404);
        }

        if (!checkPermission($user_data, 'delete', $test['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to delete this test'], 403);
        }

        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            json_response(['success' => true, 'message' => 'Test deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete test'], 500);
        }
    } catch (Exception $e) {
        error_log("Delete test error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete test'], 500);
    }
}
?>