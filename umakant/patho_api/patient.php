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

// Enhanced authentication with user filtering support
$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    json_response(['success' => false, 'message' => 'Authentication required', 'debug_info' => getAuthDebugInfo()], 401);
}

// Handle user-specific filtering
$current_user_id = null;
$current_user_role = $user_data['role'];

// Check if specific user is requested (for testing or admin access)
if (isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) {
    $requested_user_id = (int) $_REQUEST['user_id'];
    
    // Only allow if authenticated as master/admin or requesting own data
    if ($user_data['role'] === 'master' || $user_data['role'] === 'admin' || $user_data['user_id'] == $requested_user_id) {
        $current_user_id = $requested_user_id;
    } else {
        json_response(['success' => false, 'message' => 'Access denied: Cannot access other user data'], 403);
    }
} elseif (isset($_REQUEST['username']) && !empty($_REQUEST['username'])) {
    $requested_username = $_REQUEST['username'];
    
    // Get user ID from username
    try {
        $stmt = $pdo->prepare('SELECT id, role FROM users WHERE username = ?');
        $stmt->execute([$requested_username]);
        $requested_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($requested_user) {
            $requested_user_id = (int) $requested_user['id'];
            
            // Only allow if authenticated as master/admin or requesting own data
            if ($user_data['role'] === 'master' || $user_data['role'] === 'admin' || $user_data['user_id'] == $requested_user_id) {
                $current_user_id = $requested_user_id;
            } else {
                json_response(['success' => false, 'message' => 'Access denied: Cannot access other user data'], 403);
            }
        } else {
            json_response(['success' => false, 'message' => 'User not found'], 404);
        }
    } catch (Exception $e) {
        json_response(['success' => false, 'message' => 'Error looking up user'], 500);
    }
} else {
    // Use authenticated user's ID
    $current_user_id = $user_data['user_id'];
}

// If still no user ID and not master/admin, require user specification
if (!$current_user_id && $user_data['role'] !== 'master' && $user_data['role'] !== 'admin') {
    json_response([
        'success' => false, 
        'message' => 'User ID required. Add &user_id=1 or &username=admin parameter',
        'help' => 'For testing: add &user_id=1 to see user 1 data, or &username=doctor1 to see doctor1 data'
    ], 400);
}

// For master/admin without specific user, default to user 1 for testing
if (!$current_user_id && ($user_data['role'] === 'master' || $user_data['role'] === 'admin')) {
    $current_user_id = 1; // Default test user
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_REQUEST['action'] ?? $_GET['action'] ?? $_POST['action'] ?? $requestMethod;

// Handle specific actions based on request method and parameters
if ($requestMethod === 'GET' && isset($_GET['id']) && !isset($_GET['action'])) $action = 'get';
if ($requestMethod === 'GET' && !isset($_GET['id']) && !isset($_GET['action'])) $action = 'list';
if (($requestMethod === 'POST' || $requestMethod === 'PUT') && $action !== 'delete') $action = 'save';
if ($requestMethod === 'DELETE' || $action === 'delete') $action = 'delete';

try {
    // Log the action for debugging
    error_log("Patient API - Action: $action, Method: $requestMethod, ID: " . ($_REQUEST['id'] ?? 'none'));
    
    switch($action) {
        case 'list': handleList($pdo, $entity_config, $user_data); break;
        case 'get': handleGet($pdo, $entity_config, $user_data); break;
        case 'save': handleSave($pdo, $entity_config, $user_data); break;
        case 'delete': handleDelete($pdo, $entity_config, $user_data); break;
        case 'stats': handleStats($pdo, $user_data); break;
        default: 
            error_log("Patient API - Invalid action: $action");
            json_response(['success' => false, 'message' => "Invalid action specified: $action"], 400);
    }
} catch (Exception $e) {
    error_log("Patient API Error: " . $e->getMessage() . " - Trace: " . $e->getTraceAsString());
    json_response(['success' => false, 'message' => 'An internal server error occurred: ' . $e->getMessage()], 500);
}

function handleList($pdo, $config, $user_data) {
    global $current_user_id, $current_user_role;
    
    $draw = (int)($_REQUEST['draw'] ?? 1);
    $start = (int)($_REQUEST['start'] ?? 0);
    $length = (int)($_REQUEST['length'] ?? 25);
    $search = $_REQUEST['search']['value'] ?? '';

    // Filter by current user unless admin/master viewing all
    $whereClauses = [];
    $params = [];
    
    if ($current_user_role === 'master' || $current_user_role === 'admin') {
        // Admin can see all, but if user_id specified, filter by that user
        if ($current_user_id) {
            $whereClauses[] = "p.added_by = ?";
            $params[] = $current_user_id;
        }
    } else {
        // Regular users see only their data
        $whereClauses[] = "p.added_by = ?";
        $params[] = $current_user_id;
    }

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
    global $current_user_id, $current_user_role;
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
    }

    // Build query with user filtering
    $sql = "SELECT p.*, u.username as added_by_username FROM {$config['table_name']} p LEFT JOIN users u ON p.added_by = u.id WHERE p.id = ?";
    $params = [$id];
    
    // Add user filtering for non-admin users
    if ($current_user_role !== 'master' && $current_user_role !== 'admin') {
        $sql .= " AND p.added_by = ?";
        $params[] = $current_user_id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        json_response(['success' => false, 'message' => 'Patient not found or access denied'], 404);
    }

    // Map sex column to gender for frontend compatibility
    $patient['gender'] = $patient['sex'] ?? '';
    $patient['added_by_name'] = $patient['added_by_username'];

    json_response(['success' => true, 'data' => $patient]);
}

function handleSave($pdo, $config, $user_data) {
    global $current_user_id, $current_user_role;
    
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    $data = array_intersect_key($input, array_flip($config['allowed_fields']));
    $data['added_by'] = $current_user_id; // Always use current user ID
    
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
    global $current_user_id, $current_user_role;
    
    // Get ID from various sources
    $id = $_REQUEST['id'] ?? $_GET['id'] ?? $_POST['id'] ?? null;
    
    if (!$id) {
        json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
    }

    try {
        // First check if patient exists and user has access
        $checkSql = "SELECT id, added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?";
        $checkParams = [$id];
        
        // Add user filtering for non-admin users
        if ($current_user_role !== 'master' && $current_user_role !== 'admin') {
            $checkSql .= " AND added_by = ?";
            $checkParams[] = $current_user_id;
        }
        
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute($checkParams);
        $patient = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            json_response(['success' => false, 'message' => 'Patient not found or access denied'], 404);
        }

        // Check for associated test entries
        $entriesStmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE patient_id = ?');
        $entriesStmt->execute([$id]);
        $entryCount = $entriesStmt->fetchColumn();
        
        if ($entryCount > 0) {
            json_response(['success' => false, 'message' => "Cannot delete patient with {$entryCount} associated test entries"], 400);
        }

        // Perform the deletion with user filtering
        $deleteSql = "DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?";
        $deleteParams = [$id];
        
        // Add user filtering for non-admin users
        if ($current_user_role !== 'master' && $current_user_role !== 'admin') {
            $deleteSql .= " AND added_by = ?";
            $deleteParams[] = $current_user_id;
        }
        
        $deleteStmt = $pdo->prepare($deleteSql);
        $result = $deleteStmt->execute($deleteParams);
        
        if ($result && $deleteStmt->rowCount() > 0) {
            json_response([
                'success' => true, 
                'message' => 'Patient deleted successfully',
                'deleted_id' => $id,
                'user_id' => $current_user_id
            ]);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete patient - no rows affected or access denied'], 500);
        }
        
    } catch (Exception $e) {
        error_log("Delete patient error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Database error occurred while deleting patient: ' . $e->getMessage()], 500);
    }
}

function handleStats($pdo, $user_data) {
    global $current_user_id, $current_user_role;

    $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ?');
    $totalStmt->execute([$current_user_id]);
    $total = $totalStmt->fetchColumn();

    $todayStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ? AND DATE(created_at) = CURDATE()');
    $todayStmt->execute([$current_user_id]);
    $today = $todayStmt->fetchColumn();

    $thisWeekStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ? AND YEARWEEK(created_at) = YEARWEEK(CURDATE())');
    $thisWeekStmt->execute([$current_user_id]);
    $thisWeek = $thisWeekStmt->fetchColumn();

    $thisMonthStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ? AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())');
    $thisMonthStmt->execute([$current_user_id]);
    $thisMonth = $thisMonthStmt->fetchColumn();

    json_response([
        'success' => true,
        'data' => [
            'user_id' => $current_user_id,
            'user_role' => $current_user_role,
            'total' => (int) $total,
            'today' => (int) $today,
            'this_week' => (int) $thisWeek,
            'this_month' => (int) $thisMonth
        ]
    ]);
}
?>