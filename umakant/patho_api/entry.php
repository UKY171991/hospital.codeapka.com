<?php
/**
 * Entry API - Comprehensive CRUD operations for test entries
 * Supports: CREATE, READ, UPDATE, DELETE operations with statistics
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
    'table_name' => 'entries',
    'id_field' => 'id',
    'required_fields' => ['patient_id'],
    'allowed_fields' => [
        'server_id', 'patient_id', 'doctor_id', 'test_id', 'entry_date', 'result_value',
        'unit', 'remarks', 'status', 'added_by', 'price', 'discount_amount', 'total_price',
        'reported_date', 'result_status', 'grouped', 'tests_count', 'test_ids', 'test_names', 'test_results'
    ]
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
        case 'stats': handleStats($pdo, $user_data); break;
        case 'add_test': handleAddTest($pdo, $user_data); break;
        case 'remove_test': handleRemoveTest($pdo, $user_data); break;
        case 'get_tests': handleGetTests($pdo, $user_data); break;
        case 'update_test_result': handleUpdateTestResult($pdo, $user_data); break;
        case 'report_list': handleReportList($pdo, $user_data); break;
        default: json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Entry API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list entries'], 403);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    $where = '';
    $params = [];
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where = ' WHERE e.added_by IN (' . $placeholders . ')';
        $params = $scopeIds;
    }

    $sql = "SELECT e.*, p.name as patient_name, p.uhid as patient_uhid, d.name as doctor_name, u.username as added_by_username
            FROM {$config['table_name']} e
            LEFT JOIN patients p ON e.patient_id = p.id
            LEFT JOIN doctors d ON e.doctor_id = d.id
            LEFT JOIN users u ON e.added_by = u.id{$where} ORDER BY e.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response(['success' => true, 'data' => $entries, 'total' => count($entries)]);
}

function handleGet($pdo, $config, $user_data) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    $sql = "SELECT e.*, p.name as patient_name, p.uhid as patient_uhid, d.name as doctor_name, u.username as added_by_username
            FROM {$config['table_name']} e
            LEFT JOIN patients p ON e.patient_id = p.id
            LEFT JOIN doctors d ON e.doctor_id = d.id
            LEFT JOIN users u ON e.added_by = u.id
            WHERE e.{$config['id_field']} = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entry) {
        json_response(['success' => false, 'message' => 'Entry not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds) && !in_array((int)$entry['added_by'], $scopeIds, true)) {
        json_response(['success' => false, 'message' => 'Permission denied to view this entry'], 403);
    }

    $testsStmt = $pdo->prepare("SELECT et.*, t.name as test_name FROM entry_tests et LEFT JOIN tests t ON et.test_id = t.id WHERE et.entry_id = ?");
    $testsStmt->execute([$id]);
    $entry['tests'] = $testsStmt->fetchAll(PDO::FETCH_ASSOC);

    json_response(['success' => true, 'data' => $entry]);
}

function handleSave($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'save')) {
        json_response(['success' => false, 'message' => 'Permission denied to save entries'], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds) && !in_array((int)$existing['added_by'], $scopeIds, true)) {
            json_response(['success' => false, 'message' => 'Permission denied to update this entry'], 403);
        }
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    $data = array_intersect_key($input, array_flip($config['allowed_fields']));
    $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];
    $data['entry_date'] = empty($data['entry_date']) ? date('Y-m-d') : date('Y-m-d', strtotime($data['entry_date']));

    if ($id) {
        $set_clause = implode(', ', array_map(fn($field) => "`$field` = ?", array_keys($data)));
        $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
        $values = array_merge(array_values($data), [$id]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $entry_id = $id;
        $action_status = 'updated';
    } else {
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $entry_id = $pdo->lastInsertId();
        $action_status = 'inserted';
    }

    $stmt = $pdo->prepare("SELECT * FROM {$config['table_name']} WHERE id = ?");
    $stmt->execute([$entry_id]);
    $saved_entry = $stmt->fetch(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'message' => "Entry {$action_status} successfully",
        'data' => $saved_entry,
        'id' => $entry_id
    ]);
}

function handleDelete($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'delete')) {
        json_response(['success' => false, 'message' => 'Permission denied to delete entries'], 403);
    }

    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $stmt->execute([$id]);
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entry) {
        json_response(['success' => false, 'message' => 'Entry not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds) && !in_array((int)$entry['added_by'], $scopeIds, true)) {
        json_response(['success' => false, 'message' => 'Permission denied to delete this entry'], 403);
    }

    $pdo->prepare("DELETE FROM entry_tests WHERE entry_id = ?")->execute([$id]);
    $result = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?")->execute([$id]);

    json_response(['success' => $result, 'message' => $result ? 'Entry deleted successfully' : 'Failed to delete entry']);
}

function handleStats($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
    }

    $stats = [];
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries");
    $stats['total'] = (int) $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'pending'");
    $stats['pending'] = (int) $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'completed'");
    $stats['completed'] = (int) $stmt->fetchColumn();
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE DATE(COALESCE(entry_date, created_at)) = CURDATE()");
    $stats['today'] = (int) $stmt->fetchColumn();

    json_response(['success' => true, 'data' => $stats]);
}

function handleAddTest($pdo, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $entry_id = $input['entry_id'] ?? null;
    $test_id = $input['test_id'] ?? null;

    if (!$entry_id || !$test_id) {
        json_response(['success' => false, 'message' => 'Entry ID and Test ID are required'], 400);
    }

    $stmt = $pdo->prepare("INSERT INTO entry_tests (entry_id, test_id) VALUES (?, ?)");
    $result = $stmt->execute([$entry_id, $test_id]);

    json_response(['success' => $result, 'message' => $result ? 'Test added successfully' : 'Failed to add test']);
}

function handleRemoveTest($pdo, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $entry_test_id = $input['entry_test_id'] ?? null;

    if (!$entry_test_id) {
        json_response(['success' => false, 'message' => 'Entry Test ID is required'], 400);
    }

    $stmt = $pdo->prepare("DELETE FROM entry_tests WHERE id = ?");
    $result = $stmt->execute([$entry_test_id]);

    json_response(['success' => $result, 'message' => $result ? 'Test removed successfully' : 'Failed to remove test']);
}

function handleGetTests($pdo, $user_data) {
    $entry_id = $_GET['entry_id'] ?? null;
    if (!$entry_id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT et.*, t.name as test_name FROM entry_tests et LEFT JOIN tests t ON et.test_id = t.id WHERE et.entry_id = ?");
    $stmt->execute([$entry_id]);
    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response(['success' => true, 'data' => $tests]);
}

function handleUpdateTestResult($pdo, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $entry_test_id = $input['entry_test_id'] ?? null;

    if (!$entry_test_id) {
        json_response(['success' => false, 'message' => 'Entry Test ID is required'], 400);
    }

    $data = array_intersect_key($input, array_flip(['result_value', 'unit', 'remarks', 'status', 'price', 'discount_amount']));
    if (empty($data)) {
        json_response(['success' => false, 'message' => 'No fields to update'], 400);
    }

    $set_clause = implode(', ', array_map(fn($field) => "`$field` = ?", array_keys($data)));
    $sql = "UPDATE entry_tests SET $set_clause, updated_at = NOW() WHERE id = ?";
    $values = array_merge(array_values($data), [$entry_test_id]);

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($values);

    json_response(['success' => $result, 'message' => $result ? 'Test result updated successfully' : 'Failed to update test result']);
}

function handleReportList($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list reports'], 403);
    }

    $params = [];
    $where = [];

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where[] = 'e.added_by IN (' . $placeholders . ')';
        $params = $scopeIds;
    }

    if (!empty($_GET['test_id'])) {
        $where[] = 'et.test_id = ?';
        $params[] = $_GET['test_id'];
    }
    if (!empty($_GET['doctor_id'])) {
        $where[] = 'e.doctor_id = ?';
        $params[] = $_GET['doctor_id'];
    }
    if (!empty($_GET['status'])) {
        $where[] = 'et.status = ?';
        $params[] = $_GET['status'];
    }
    if (!empty($_GET['date_from'])) {
        $where[] = 'DATE(e.entry_date) >= ?';
        $params[] = $_GET['date_from'];
    }
    if (!empty($_GET['date_to'])) {
        $where[] = 'DATE(e.entry_date) <= ?';
        $params[] = $_GET['date_to'];
    }

    $sql = "SELECT e.id as entry_id, e.entry_date, p.name as patient_name, d.name as doctor_name, t.name as test_name, et.result_value, et.status
            FROM entries e
            LEFT JOIN patients p ON e.patient_id = p.id
            LEFT JOIN doctors d ON e.doctor_id = d.id
            LEFT JOIN entry_tests et ON e.id = et.entry_id
            LEFT JOIN tests t ON et.test_id = t.id";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

    json_response(['success' => true, 'data' => $reports, 'total' => count($reports)]);
}
?>