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

function determineTargetUserId(array $user_data): ?int {
    $role = $user_data['role'] ?? 'user';
    $requested = $_GET['user_id'] ?? $_POST['user_id'] ?? null;
    $requested = is_numeric($requested) ? (int)$requested : null;
    $includeAll = isset($_REQUEST['all']) && (string)$_REQUEST['all'] === '1';

    if (in_array($role, ['master', 'admin'], true)) {
        if ($includeAll) {
            return null; // master/admin can request all records
        }
        if ($requested !== null) {
            return $requested;
        }
        return (int)$user_data['user_id'];
    }

    return (int)$user_data['user_id'];
}

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';
require_once __DIR__ . '/../inc/simple_auth.php';

// Entity Configuration for Test Categories
$entity_config = [
    'table_name' => 'categories',
    'id_field' => 'id',
    'required_fields' => ['name'],
    'allowed_fields' => ['name', 'description', 'added_by']
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
    error_log("Test Category API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list categories'], 403);
    }

    try {
        $targetUserId = determineTargetUserId($user_data);
        $searchTerm = trim((string)($_GET['search'] ?? $_POST['search'] ?? ''));

        $filters = [];
        $params = [];

        if ($targetUserId !== null) {
            $filters[] = 'c.added_by = ?';
            $params[] = $targetUserId;
        }

        if ($searchTerm !== '') {
            $filters[] = '(c.name LIKE ? OR c.description LIKE ?)';
            $like = '%' . $searchTerm . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql = "SELECT c.*, COALESCE(u.username, '') AS added_by_username, COUNT(t.id) AS test_count
                FROM {$config['table_name']} c
                LEFT JOIN tests t ON c.id = t.category_id
                LEFT JOIN users u ON c.added_by = u.id";

        if (!empty($filters)) {
            $sql .= ' WHERE ' . implode(' AND ', $filters);
        }

        $sql .= ' GROUP BY c.id ORDER BY c.name';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        foreach ($categories as $idx => &$category) {
            $category['sno'] = $idx + 1;
            $category['test_count'] = (int)($category['test_count'] ?? 0);
            if (isset($category['added_by'])) {
                $category['added_by'] = $category['added_by'] !== null ? (int)$category['added_by'] : null;
            }
        }

        json_response([
            'success' => true,
            'data' => $categories,
            'total' => count($categories),
            'filters' => [
                'user_id' => $targetUserId,
                'search' => $searchTerm
            ]
        ]);
    } catch (Exception $e) {
        error_log('List test categories error: ' . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch test categories'], 500);
    }
}

function handleGet($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Category ID is required'], 400);
    }

    try {
        $sql = "SELECT c.*, COALESCE(u.username, '') AS added_by_username, COUNT(t.id) AS test_count
                FROM {$config['table_name']} c
                LEFT JOIN tests t ON c.id = t.category_id
                LEFT JOIN users u ON c.added_by = u.id
                WHERE c.{$config['id_field']} = ?
                GROUP BY c.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            json_response(['success' => false, 'message' => 'Test category not found'], 404);
        }

        if (!simpleCheckPermission($user_data, 'get', $category['added_by'] ?? null)) {
            json_response(['success' => false, 'message' => 'Permission denied to view this category'], 403);
        }

        $category['test_count'] = (int)($category['test_count'] ?? 0);

        json_response(['success' => true, 'data' => $category]);
    } catch (Exception $e) {
        error_log("Get test category error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch test category'], 500);
    }
}

function handleSave($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $input = array_map(fn($v) => is_string($v) ? trim($v) : $v, $input);
    $id = $input['id'] ?? null;

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Category not found'], 404);
        }
        if (!simpleCheckPermission($user_data, 'save', $existing['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to update this category'], 403);
        }
    } else { // Create
        if (!simpleCheckPermission($user_data, 'save')) {
            json_response(['success' => false, 'message' => 'Permission denied to create categories'], 403);
        }
    }

    foreach ($config['required_fields'] as $field) {
        if (!isset($input[$field]) || trim((string)$input[$field]) === '') {
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
        if (!empty($data['added_by'])) {
            $data['added_by'] = (int)$data['added_by'];
        }

        // Always default new records to the authenticated user
        if (!$id || empty($data['added_by'])) {
            $data['added_by'] = (int)$user_data['user_id'];
        }

        if ($id && !simpleCheckPermission($user_data, 'save', $data['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to update this category'], 403);
        }

        if ($id) {
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $category_id = $id;
            $action = 'updated';
        } else {
            $cols = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $category_id = $pdo->lastInsertId();
            $action = 'inserted';
        }

        $stmt = $pdo->prepare("SELECT c.id, c.name, c.description, c.added_by, COALESCE(u.username, '') AS added_by_username, COUNT(t.id) AS test_count
                                FROM {$config['table_name']} c
                                LEFT JOIN tests t ON c.id = t.category_id
                                LEFT JOIN users u ON c.added_by = u.id
                                WHERE c.id = ?
                                GROUP BY c.id");
        $stmt->execute([$category_id]);
        $saved_category = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($saved_category) {
            $saved_category['test_count'] = (int)($saved_category['test_count'] ?? 0);
        }

        json_response([
            'success' => true,
            'message' => "Category {$action} successfully",
            'data' => $saved_category,
            'id' => $category_id
        ]);
    } catch (Exception $e) {
        error_log("Save test category error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to save test category'], 500);
    }
}

function handleDelete($pdo, $config) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }

    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Category ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            json_response(['success' => false, 'message' => 'Test category not found'], 404);
        }

        if (!simpleCheckPermission($user_data, 'delete', $category['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to delete this category'], 403);
        }

        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            json_response(['success' => true, 'message' => 'Test category deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete test category'], 500);
        }
    } catch (Exception $e) {
        error_log("Delete test category error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete test category'], 500);
    }
}
?>