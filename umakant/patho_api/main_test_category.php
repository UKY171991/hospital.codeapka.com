<?php
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

$entity_table = 'main_test_categories';

$user = simpleAuthenticate($pdo);
if (!$user) {
    json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
}

$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
if ($requestMethod === 'GET') {
    $action = isset($_GET['id']) ? 'get' : 'list';
}
if ($requestMethod === 'POST' || $requestMethod === 'PUT') $action = $_REQUEST['action'] ?? 'save';
if ($requestMethod === 'DELETE') $action = 'delete';

try {
    switch ($action) {
        case 'list':
            handleList($pdo, $entity_table, $user);
            break;
        case 'get':
            handleGet($pdo, $entity_table, $user);
            break;
        case 'save':
            handleSave($pdo, $entity_table, $user);
            break;
        case 'delete':
            handleDelete($pdo, $entity_table, $user);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log('Main Test Category API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error'], 500);
}

function handleList($pdo, $table, $user) {
    if (!simpleCheckPermission($user, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list categories'], 403);
    }

    $params = [];
    $filters = [];

    // Optional user_id filter
    $requestedUser = isset($_REQUEST['user_id']) && is_numeric($_REQUEST['user_id']) ? (int)$_REQUEST['user_id'] : null;
    // If not master/admin, force scope to current user
    if (!in_array($user['role'] ?? 'user', ['master','admin'], true)) {
        $filters[] = 'c.added_by = ?';
        $params[] = $user['user_id'];
    } elseif ($requestedUser !== null) {
        $filters[] = 'c.added_by = ?';
        $params[] = $requestedUser;
    }

    $sql = "SELECT c.id, c.name, c.description, c.added_by, COALESCE(u.username, '') as added_by_username,
            (SELECT COUNT(*) FROM categories t WHERE t.main_category_id = c.id) as test_count
            FROM {$table} c LEFT JOIN users u ON c.added_by = u.id";

    if (!empty($filters)) {
        $sql .= ' WHERE ' . implode(' AND ', $filters);
    }
    $sql .= ' ORDER BY c.id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    foreach ($rows as $i => &$r) { $r['sno'] = $i + 1; $r['test_count'] = (int)($r['test_count'] ?? 0); }

    json_response(['success' => true, 'data' => $rows, 'total' => count($rows)]);
}

function handleGet($pdo, $table, $user) {
    if (!simpleCheckPermission($user, 'get')) {
        json_response(['success' => false, 'message' => 'Permission denied'], 403);
    }
    $id = $_GET['id'] ?? null;
    if (!$id) json_response(['success' => false, 'message' => 'ID required'], 400);

    $stmt = $pdo->prepare("SELECT c.*, COALESCE(u.username,'') as added_by_username, (SELECT COUNT(*) FROM categories t WHERE t.main_category_id = c.id) as test_count FROM {$table} c LEFT JOIN users u ON c.added_by = u.id WHERE c.id = ? GROUP BY c.id");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) json_response(['success' => false, 'message' => 'Not found'], 404);
    $row['test_count'] = (int)($row['test_count'] ?? 0);
    json_response(['success' => true, 'data' => $row]);
}

function handleSave($pdo, $table, $user) {
    // Only admin/master can create or update (match ajax behavior)
    if (!in_array($user['role'] ?? 'user', ['master','admin'], true)) {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $input = $_POST + (array) json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    $name = trim($input['name'] ?? '');
    $description = trim($input['description'] ?? '');

    if ($name === '') json_response(['success' => false, 'message' => 'Name is required'], 400);

    if ($id) {
        $stmt = $pdo->prepare("UPDATE {$table} SET name = ?, description = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$name, $description, $id]);
        json_response(['success' => true, 'message' => 'Category updated']);
    } else {
        $added_by = $user['user_id'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO {$table} (name, description, added_by, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $description, $added_by]);
        json_response(['success' => true, 'message' => 'Category created']);
    }
}

function handleDelete($pdo, $table, $user) {
    if (!in_array($user['role'] ?? 'user', ['master','admin'], true)) {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }
    $id = $_POST['id'] ?? null;
    if (!$id) json_response(['success' => false, 'message' => 'ID required'], 400);
    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
    $stmt->execute([$id]);
    json_response(['success' => true, 'message' => 'Category deleted']);
}

?>
