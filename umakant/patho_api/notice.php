<?php
/**
 * Notice API - Comprehensive CRUD operations for notices
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
require_once __DIR__ . '/../inc/simple_auth.php';

$entity_config = [
    'table_name' => 'notices',
    'id_field' => 'id',
    'required_fields' => ['title'],
    'allowed_fields' => ['title', 'content', 'start_date', 'end_date', 'active', 'added_by']
];

$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? $requestMethod;

if ($requestMethod === 'GET' && isset($_GET['id'])) $action = 'get';
if ($requestMethod === 'GET' && !isset($_GET['id'])) $action = 'list';
if ($requestMethod === 'POST' || $requestMethod === 'PUT') $action = 'save';
if ($requestMethod === 'DELETE') $action = 'delete';

try {
    switch($action) {
        case 'list': handleList($pdo, $entity_config, $user_data); break;
        case 'get': handleGet($pdo, $entity_config, $user_data); break;
        case 'save': handleSave($pdo, $entity_config, $user_data); break;
        case 'delete': handleDelete($pdo, $entity_config, $user_data); break;
        default: json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Notice API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list notices'], 403);
    }

    $sql = "SELECT n.*, u.username as added_by_username FROM {$config['table_name']} n LEFT JOIN users u ON n.added_by = u.id ORDER BY n.start_date DESC, n.id DESC";
    $stmt = $pdo->query($sql);
    $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    json_response(['success' => true, 'data' => $notices, 'total' => count($notices)]);
}

function handleGet($pdo, $config, $user_data) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Notice ID is required'], 400);
    }

    $sql = "SELECT n.*, u.username as added_by_username FROM {$config['table_name']} n LEFT JOIN users u ON n.added_by = u.id WHERE n.{$config['id_field']} = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $notice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notice) {
        json_response(['success' => false, 'message' => 'Notice not found'], 404);
    }

    if (!simpleCheckPermission($user_data, 'get', $notice['added_by'])) {
        json_response(['success' => false, 'message' => 'Permission denied to view this notice'], 403);
    }

    json_response(['success' => true, 'data' => $notice]);
}

function handleSave($pdo, $config, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Notice not found'], 404);
        }
        if (!simpleCheckPermission($user_data, 'save', $existing['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to update this notice'], 403);
        }
    } else { // Create
        if (!simpleCheckPermission($user_data, 'save')) {
            json_response(['success' => false, 'message' => 'Permission denied to create notices'], 403);
        }
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    $data = array_intersect_key($input, array_flip($config['allowed_fields']));
    $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];

    if ($id) {
        $set_clause = implode(', ', array_map(fn($field) => "`$field` = ?", array_keys($data)));
        $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
        $values = array_merge(array_values($data), [$id]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $notice_id = $id;
        $action_status = 'updated';
    } else {
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $notice_id = $pdo->lastInsertId();
        $action_status = 'inserted';
    }

    $stmt = $pdo->prepare("SELECT n.*, u.username as added_by_username FROM {$config['table_name']} n LEFT JOIN users u ON n.added_by = u.id WHERE n.id = ?");
    $stmt->execute([$notice_id]);
    $saved_notice = $stmt->fetch(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'message' => "Notice {$action_status} successfully",
        'data' => $saved_notice,
        'id' => $notice_id
    ]);
}

function handleDelete($pdo, $config, $user_data) {
    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Notice ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $stmt->execute([$id]);
    $notice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$notice) {
        json_response(['success' => false, 'message' => 'Notice not found'], 404);
    }

    if (!simpleCheckPermission($user_data, 'delete', $notice['added_by'])) {
        json_response(['success' => false, 'message' => 'Permission denied to delete this notice'], 403);
    }

    $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $result = $stmt->execute([$id]);

    json_response(['success' => $result, 'message' => $result ? 'Notice deleted successfully' : 'Failed to delete notice']);
}
?>