<?php
/**
 * Patient API - Lightweight CRUD for patient.php page
 * Supports: CREATE, READ, UPDATE, DELETE operations with user filtering
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Api-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/simple_auth.php';

$entity_config = [
    'table_name' => 'patients',
    'id_field' => 'id',
    'required_fields' => ['name', 'mobile'],
    'allowed_fields' => ['name', 'uhid', 'father_husband', 'mobile', 'email', 'age', 'age_unit', 'gender', 'address', 'contact', 'added_by']
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
        default: json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Patient API Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $user_data) {
    $userId = $_REQUEST['user_id'] ?? $user_data['user_id'];
    $draw = (int)($_REQUEST['draw'] ?? 1);
    $start = (int)($_REQUEST['start'] ?? 0);
    $length = (int)($_REQUEST['length'] ?? 25);
    $search = $_REQUEST['search']['value'] ?? '';

    $whereClauses = ["p.added_by = ?"];
    $params = [$userId];

    if (!empty($search)) {
        $whereClauses[] = "(p.name LIKE ? OR p.uhid LIKE ? OR p.mobile LIKE ?)";
        $searchTerm = "%$search%";
        array_push($params, $searchTerm, $searchTerm, $searchTerm);
    }

    $whereSql = " WHERE " . implode(" AND ", $whereClauses);

    $totalStmt = $pdo->query("SELECT COUNT(*) FROM {$config['table_name']}");
    $totalRecords = $totalStmt->fetchColumn();

    $filteredStmt = $pdo->prepare("SELECT COUNT(*) FROM {$config['table_name']} p $whereSql");
    $filteredStmt->execute($params);
    $filteredRecords = $filteredStmt->fetchColumn();

    $query = "SELECT p.*, u.username as added_by_username FROM {$config['table_name']} p LEFT JOIN users u ON p.added_by = u.id $whereSql ORDER BY p.id DESC LIMIT $start, $length";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map sex column to gender for frontend compatibility
    foreach ($patients as &$patient) {
        $patient['gender'] = $patient['sex'] ?? '';
        $patient['added_by_name'] = $patient['added_by_username'];
    }
    unset($patient);

    json_response([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'success' => true,
        'data' => $patients
    ]);
}

function handleGet($pdo, $config, $user_data) {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
    }

    $stmt = $pdo->prepare("SELECT p.*, u.username as added_by_username FROM {$config['table_name']} p LEFT JOIN users u ON p.added_by = u.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        json_response(['success' => false, 'message' => 'Patient not found'], 404);
    }

    // Map sex column to gender for frontend compatibility
    $patient['gender'] = $patient['sex'] ?? '';
    $patient['added_by_name'] = $patient['added_by_username'];

    json_response(['success' => true, 'data' => $patient]);
}

function handleSave($pdo, $config, $user_data) {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    $data = array_intersect_key($input, array_flip($config['allowed_fields']));
    $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];
    
    // Map gender to sex column for database storage
    if (isset($data['gender'])) {
        $data['sex'] = $data['gender'];
        unset($data['gender']);
    }
    
    // Generate UHID if not provided for new patients
    if (!$id && empty($data['uhid'])) {
        $stmt = $pdo->query("SELECT MAX(CAST(SUBSTR(uhid, 2) AS UNSIGNED)) FROM patients WHERE uhid LIKE 'P%'");
        $next = (int)$stmt->fetchColumn();
        $data['uhid'] = 'P' . str_pad($next + 1, 6, '0', STR_PAD_LEFT);
    }

    if ($id) {
        $set_clause = implode(', ', array_map(fn($field) => "`$field` = ?", array_keys($data)));
        $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
        $values = array_merge(array_values($data), [$id]);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $patient_id = $id;
        $action_status = 'updated';
    } else {
        $cols = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $patient_id = $pdo->lastInsertId();
        $action_status = 'inserted';
    }

    $stmt = $pdo->prepare("SELECT p.*, u.username as added_by_username FROM {$config['table_name']} p LEFT JOIN users u ON p.added_by = u.id WHERE p.id = ?");
    $stmt->execute([$patient_id]);
    $saved_patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Map sex back to gender for frontend
    $saved_patient['gender'] = $saved_patient['sex'] ?? '';
    $saved_patient['added_by_name'] = $saved_patient['added_by_username'];

    json_response([
        'success' => true,
        'message' => "Patient {$action_status} successfully",
        'data' => $saved_patient,
        'id' => $patient_id
    ]);
}

function handleDelete($pdo, $config, $user_data) {
    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE patient_id = ?');
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        json_response(['success' => false, 'message' => 'Cannot delete patient with associated test entries'], 400);
    }

    $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
    $result = $stmt->execute([$id]);

    json_response(['success' => $result, 'message' => $result ? 'Patient deleted successfully' : 'Failed to delete patient']);
}

function handleStats($pdo, $user_data) {
    $userId = $_REQUEST['user_id'] ?? $user_data['user_id'];

    $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ?');
    $totalStmt->execute([$userId]);
    $total = $totalStmt->fetchColumn();

    $todayStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ? AND DATE(created_at) = CURDATE()');
    $todayStmt->execute([$userId]);
    $today = $todayStmt->fetchColumn();

    json_response([
        'success' => true,
        'data' => [
            'total' => $total,
            'today' => $today
        ]
    ]);
}
?>