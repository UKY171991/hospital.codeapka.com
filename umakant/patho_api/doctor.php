<?php
/**
 * Doctor API - Comprehensive CRUD operations for doctors
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

// Entity Configuration for Doctors
$entity_config = [
    'table_name' => 'doctors',
    'id_field' => 'id',
    'required_fields' => ['name'],
    'allowed_fields' => [
        'server_id', 'name', 'qualification', 'specialization', 'hospital',
        'contact_no', 'phone', 'percent', 'email', 'address', 'registration_no', 'added_by'
    ],
    'list_fields' => 'd.id, d.name, d.hospital, d.contact_no, d.percent, d.added_by, u.username as added_by_username, d.created_at',
    'get_fields' => 'd.*, u.username as added_by_username'
];

// Authenticate user at the beginning of the script
$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
}

// Determine action
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? $requestMethod;

if ($requestMethod === 'GET' && isset($_GET['id'])) $action = 'get';
if ($requestMethod === 'GET' && !isset($_GET['id'])) $action = 'list';
if ($requestMethod === 'POST' || $requestMethod === 'PUT') $action = 'save';
if ($requestMethod === 'DELETE') $action = 'delete';

try {
    switch($action) {
        case 'list':
        case 'simple_list':
            handleList($pdo, $entity_config, $user_data, $action === 'simple_list');
            break;
        case 'get':
            handleGet($pdo, $entity_config, $user_data);
            break;
        case 'save':
            handleSave($pdo, $entity_config, $user_data);
            break;
        case 'specializations':
            handleSpecializations($pdo, $user_data);
            break;
        case 'hospitals':
            handleHospitals($pdo, $user_data);
            break;
        case 'delete':
            handleDelete($pdo, $entity_config, $user_data);
            break;
        case 'stats':
            handleStats($pdo, $user_data);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Doctor API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $user_data, $simpleList = false) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list doctors'], 403);
    }

    if ($simpleList) {
        $stmt = $pdo->query('SELECT id, name FROM doctors ORDER BY name ASC');
        json_response(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        return;
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    $where = '';
    $params = [];
    if (is_array($scopeIds)) {
        $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
        $where = ' WHERE d.added_by IN (' . $placeholders . ')';
        $params = $scopeIds;
    }

    $draw = $_POST['draw'] ?? 1;
    $start = $_POST['start'] ?? 0;
    $length = $_POST['length'] ?? 25;
    $search = $_POST['search']['value'] ?? '';

    $baseQuery = "FROM {$config['table_name']} d LEFT JOIN users u ON d.added_by = u.id";
    $whereClause = $where;

    if (!empty($search)) {
        $whereClause .= (empty($where) ? ' WHERE ' : ' AND ') . "(d.name LIKE ? OR d.specialization LIKE ? OR d.hospital LIKE ?)";
        $searchTerm = "%$search%";
        array_push($params, $searchTerm, $searchTerm, $searchTerm);
    }

    $totalStmt = $pdo->query("SELECT COUNT(*) FROM {$config['table_name']}");
    $totalRecords = $totalStmt->fetchColumn();

    $filteredStmt = $pdo->prepare("SELECT COUNT(*) $baseQuery $whereClause");
    $filteredStmt->execute($params);
    $filteredRecords = $filteredStmt->fetchColumn();

    $dataQuery = "SELECT {$config['list_fields']} $baseQuery $whereClause ORDER BY d.id DESC LIMIT $start, $length";
    $dataStmt = $pdo->prepare($dataQuery);
    $dataStmt->execute($params);
    $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    json_response([
        'draw' => intval($draw),
        'recordsTotal' => intval($totalRecords),
        'recordsFiltered' => intval($filteredRecords),
        'success' => true,
        'data' => $data
    ]);
}

function handleGet($pdo, $config, $user_data) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT {$config['get_fields']} FROM {$config['table_name']} d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        json_response(['success' => false, 'message' => 'Doctor not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds) && !in_array((int)$row['added_by'], $scopeIds, true)) {
        json_response(['success' => false, 'message' => 'Permission denied to view this doctor'], 403);
    }

    json_response(['success' => true, 'data' => $row]);
}

function handleSave($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'save')) {
        json_response(['success' => false, 'message' => 'Permission denied to save doctors'], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Doctor not found'], 404);
        }
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds) && !in_array((int)$existing['added_by'], $scopeIds, true)) {
            json_response(['success' => false, 'message' => 'Permission denied to update this doctor'], 403);
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
        $doctor_id = $id;
        $action_status = 'updated';
    } else {
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $doctor_id = $pdo->lastInsertId();
        $action_status = 'inserted';
    }

    $stmt = $pdo->prepare("SELECT {$config['get_fields']} FROM {$config['table_name']} d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?");
    $stmt->execute([$doctor_id]);
    $saved_doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    json_response([
        'success' => true,
        'message' => "Doctor {$action_status} successfully",
        'data' => $saved_doctor,
        'id' => $doctor_id
    ]);
}

function handleDelete($pdo, $config, $user_data) {
    if (!simpleCheckPermission($user_data, 'delete')) {
        json_response(['success' => false, 'message' => 'Permission denied to delete doctors'], 403);
    }

    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $stmt->execute([$id]);
    $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doctor) {
        json_response(['success' => false, 'message' => 'Doctor not found'], 404);
    }

    $scopeIds = getScopedUserIds($pdo, $user_data);
    if (is_array($scopeIds) && !in_array((int)$doctor['added_by'], $scopeIds, true)) {
        json_response(['success' => false, 'message' => 'Permission denied to delete this doctor'], 403);
    }

    $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $result = $stmt->execute([$id]);

    json_response(['success' => $result, 'message' => $result ? 'Doctor deleted successfully' : 'Failed to delete doctor']);
}

function handleStats($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
    }

    $stats = [];
    $stmt = $pdo->query("SELECT COUNT(*) FROM doctors");
    $stats['total'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM doctors WHERE phone IS NOT NULL AND phone != ''");
    $stats['active'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(DISTINCT specialization) FROM doctors WHERE specialization IS NOT NULL AND specialization != ''");
    $stats['specializations'] = (int) $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(DISTINCT hospital) FROM doctors WHERE hospital IS NOT NULL AND hospital != ''");
    $stats['hospitals'] = (int) $stmt->fetchColumn();

    json_response(['success' => true, 'data' => $stats]);
}

/**
 * Return a list of distinct specializations from doctors
 */
function handleSpecializations($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list specializations'], 403);
    }

    $stmt = $pdo->query("SELECT DISTINCT specialization FROM doctors WHERE specialization IS NOT NULL AND specialization != '' ORDER BY specialization");
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    // Normalize to simple array of strings
    $data = array_values(array_filter(array_map('trim', $rows), fn($v) => $v !== ''));
    json_response(['success' => true, 'data' => $data]);
}

/**
 * Return a list of distinct hospitals from doctors
 */
function handleHospitals($pdo, $user_data) {
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list hospitals'], 403);
    }

    $stmt = $pdo->query("SELECT DISTINCT hospital FROM doctors WHERE hospital IS NOT NULL AND hospital != '' ORDER BY hospital");
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $data = array_values(array_filter(array_map('trim', $rows), fn($v) => $v !== ''));
    json_response(['success' => true, 'data' => $data]);
}
?>