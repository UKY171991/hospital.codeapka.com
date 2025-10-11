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

// Robust: always return JSON for unhandled errors
set_exception_handler(function($ex){
    json_response(['success' => false, 'message' => 'Server error', 'error' => $ex->getMessage()], 500);
});
set_error_handler(function($severity, $message, $file, $line){
    throw new ErrorException($message, 0, $severity, $file, $line);
});
register_shutdown_function(function(){
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Fatal error', 'error' => $e['message']]);
    }
});

require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';
require_once __DIR__ . '/../inc/smart_upsert.php';
require_once __DIR__ . '/../inc/simple_auth.php';

function resolveUserIdentifierValues($value, $pdo) {
    $identifiers = [];

    if ($value === null || $value === '') {
        return $identifiers;
    }

    if (is_numeric($value)) {
        $userId = (int)$value;
        $identifiers[] = $userId;
        try {
            $stmt = $pdo->prepare('SELECT username, full_name FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($userRow) {
                if (!empty($userRow['username'])) {
                    $identifiers[] = $userRow['username'];
                }
                if (!empty($userRow['full_name'])) {
                    $identifiers[] = $userRow['full_name'];
                }
            }
        } catch (Throwable $e) {
            // ignore lookup issues
        }
    } else {
        $provided = trim((string)$value);
        if ($provided !== '') {
            $identifiers[] = $provided;
            try {
                $stmt = $pdo->prepare('SELECT id, username, full_name FROM users WHERE username = ? OR full_name = ?');
                $stmt->execute([$provided, $provided]);
                $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($userRow) {
                    if (!empty($userRow['id'])) {
                        $identifiers[] = (int)$userRow['id'];
                    }
                    if (!empty($userRow['username'])) {
                        $identifiers[] = $userRow['username'];
                    }
                    if (!empty($userRow['full_name'])) {
                        $identifiers[] = $userRow['full_name'];
                    }
                }
            } catch (Throwable $e) {
                // ignore
            }
        }
    }

    $identifiers = array_filter($identifiers, function($item) {
        return $item !== null && $item !== '';
    });

    return array_values(array_unique($identifiers, SORT_REGULAR));
}

// Entity Configuration for Doctors
$entity_config = [
    'table_name' => 'doctors',
    'id_field' => 'id',
    'required_fields' => ['name'],
    'allowed_fields' => [
        'server_id',
        'name',
        'qualification',
        'specialization',
        'hospital',
        'contact_no',
        'phone',
        'percent',
        'email',
        'address',
        'registration_no',
        'added_by',
        'created_at',
        'updated_at'
    ]
];

// Get action from request
$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map HTTP methods to actions
switch($requestMethod) {
    case 'GET':
        $action = isset($_GET['id']) ? 'get' : ($_GET['action'] ?? 'list');
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
        case 'simple_list':
            handleList($pdo, $entity_config, $action === 'simple_list');
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
        case 'stats':
            handleStats($pdo);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Doctor API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config, $simpleList = false) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list doctors'], 403);
    }

    if ($simpleList) {
        try {
            $stmt = $pdo->query('SELECT id, name FROM doctors ORDER BY name ASC');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            json_response(['success' => false, 'message' => 'Failed to fetch doctors', 'error' => $e->getMessage()], 500);
        }
        return;
    }

    try {
        // Role-based scoping by added_by
        $scopeIds = getScopedUserIds($pdo, $user_data);
        $where = '';
        $params = [];
        if (is_array($scopeIds)) {
            $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
            $where = ' WHERE d.added_by IN (' . $placeholders . ')';
            $params = $scopeIds;
        }

        // Support DataTables server-side processing
        $draw = $_POST['draw'] ?? 1;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 25;
        $search = $_POST['search']['value'] ?? '';

        // Base query
        $baseQuery = "FROM doctors d LEFT JOIN users u ON d.added_by = u.id";
        $whereClause = $where;
        $searchParams = [];

        // Add search conditions
        if (!empty($search)) {
            $whereClause .= (strpos($whereClause, 'WHERE') === false) ? ' WHERE ' : ' AND ';
            $whereClause .= "(d.name LIKE ? OR d.specialization LIKE ? OR d.hospital LIKE ? OR d.phone LIKE ? OR d.email LIKE ?)";
            $searchTerm = "%$search%";
            $searchParams = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        // Optional filter by added_by (from dropdown)
        if (isset($_REQUEST['added_by']) && $_REQUEST['added_by'] !== '') {
            $identifierValues = resolveUserIdentifierValues($_REQUEST['added_by'], $pdo);
            if (!empty($identifierValues)) {
                $placeholders = implode(',', array_fill(0, count($identifierValues), '?'));
                $whereClause .= (strpos($whereClause, 'WHERE') === false) ? ' WHERE ' : ' AND ';
                $whereClause .= "d.added_by IN ($placeholders)";
                $params = array_merge($params, $identifierValues);
            } else {
                $whereClause = " WHERE 1 = 0";
                $params = [];
            }
        }

        // Get total records (no filters)
        $totalStmt = $pdo->query("SELECT COUNT(*) " . $baseQuery);
        $totalRecords = $totalStmt->fetchColumn();

        // Get filtered records (with current search filters and added_by)
        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY d.id DESC";
        $limit = " LIMIT $start, $length";

        $dataQuery = "SELECT d.id,
                     d.name,
                     d.hospital,
                     d.contact_no,
                     d.percent,
                     d.added_by,
                     u.username as added_by_username,
                     d.created_at
                  " . $baseQuery . $whereClause . $orderBy . $limit;

        $dataStmt = $pdo->prepare($dataQuery);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

        // Return DataTables format
        json_response([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'success' => true,
            'data' => $data
        ]);
    } catch (Exception $e) {
        error_log("List doctors error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch doctors', 'error' => $e->getMessage()], 500);
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
        json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT d.id,d.server_id,d.name,d.qualification,d.specialization,d.hospital,d.contact_no,d.phone,d.percent,d.email,d.address,d.registration_no,d.added_by,d.created_at,d.updated_at,u.username as added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            json_response(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$row['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to view this doctor'], 403);
            }
        }

        json_response(['success' => true, 'data' => $row]);
    } catch (Exception $e) {
        error_log("Get doctor error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch doctor'], 500);
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

    // Check if user has permission to save (admin or master)
    if (!isset($user_data['role']) || !in_array($user_data['role'], ['admin', 'master'])) {
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
        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$existing['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to update this doctor'], 403);
            }
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
            if (array_key_exists($field, $input)) {
                $data[$field] = $input[$field];
            }
        }

        // Defaults and normalization
        $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];

        if ($id) {
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $doctor_id = $id;
            $action = 'updated';
        } else {
            $cols = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $doctor_id = $pdo->lastInsertId();
            $action = 'inserted';
        }

        $stmt = $pdo->prepare("SELECT d.*, u.username as added_by_username FROM {$config['table_name']} d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?");
        $stmt->execute([$doctor_id]);
        $saved_doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response([
            'success' => true,
            'message' => "Doctor {$action} successfully",
            'data' => $saved_doctor,
            'id' => $doctor_id
        ]);
    } catch (Exception $e) {
        error_log("Save doctor error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to save doctor'], 500);
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

    // Check if user has permission to delete (admin or master)
    if (!isset($user_data['role']) || !in_array($user_data['role'], ['admin', 'master'])) {
        json_response(['success' => false, 'message' => 'Permission denied to delete doctors'], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doctor) {
            json_response(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$doctor['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to delete this doctor'], 403);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            json_response(['success' => true, 'message' => 'Doctor deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete doctor'], 500);
        }
    } catch (Exception $e) {
        error_log("Delete doctor error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete doctor'], 500);
    }
}

function handleStats($pdo) {
    $user_data = simpleAuthenticate($pdo);
    if (!$user_data) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => getAuthDebugInfo()
        ], 401);
    }
    if (!simpleCheckPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
    }

    try {
        $stats = [];

        // Total doctors
        $stmt = $pdo->query("SELECT COUNT(*) FROM doctors");
        $stats['total'] = (int) $stmt->fetchColumn();

        // Active doctors (with phone)
        $stmt = $pdo->query("SELECT COUNT(*) FROM doctors WHERE phone IS NOT NULL AND phone != ''");
        $stats['active'] = (int) $stmt->fetchColumn();

        // Specializations count
        $stmt = $pdo->query("SELECT COUNT(DISTINCT specialization) FROM doctors WHERE specialization IS NOT NULL AND specialization != ''");
        $stats['specializations'] = (int) $stmt->fetchColumn();

        // Hospitals count
        $stmt = $pdo->query("SELECT COUNT(DISTINCT hospital) FROM doctors WHERE hospital IS NOT NULL AND hospital != ''");
        $stats['hospitals'] = (int) $stmt->fetchColumn();

        json_response(['success' => true, 'data' => $stats]);
    } catch (Exception $e) {
        error_log("Stats error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch statistics'], 500);
    }
}
?>