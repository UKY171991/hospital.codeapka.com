<?php
/**
 * Entry API - Comprehensive CRUD operations for test entries
 * Supports: CREATE, READ, UPDATE, DELETE operations
 * Authentication: Session-based or API token
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';

// Get action from request
$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map HTTP methods to actions
switch($requestMethod) {
    case 'GET':
        $action = isset($_GET['id']) ? 'get' : 'list';
        break;
    case 'POST':
        $action = $_REQUEST['action'] ?? 'create';
        break;
    case 'PUT':
        $action = 'update';
        break;
    case 'DELETE':
        $action = 'delete';
        break;
}

try {
    // Authenticate user
    function authenticateUser($pdo) {
        global $_SESSION;
        
        // Check session first
        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        
        // Check Authorization header
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $stmt = $pdo->prepare('SELECT id FROM users WHERE api_token = ? AND is_active = 1');
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            if ($user) return $user['id'];
        }
        
        return null;
    }

    // Validate entry data
    function validateEntryData($data, $pdo, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['patient_id'])) {
            if (empty($data['patient_id'])) {
                $errors[] = 'Patient ID is required';
            } else {
                // Verify patient exists
                $stmt = $pdo->prepare('SELECT id FROM patients WHERE id = ?');
                $stmt->execute([$data['patient_id']]);
                if (!$stmt->fetch()) {
                    $errors[] = 'Invalid patient ID';
                }
            }
        }
        
        if (!$isUpdate || isset($data['doctor_id'])) {
            if (empty($data['doctor_id'])) {
                $errors[] = 'Doctor ID is required';
            } else {
                // Verify doctor exists
                $stmt = $pdo->prepare('SELECT id FROM doctors WHERE id = ?');
                $stmt->execute([$data['doctor_id']]);
                if (!$stmt->fetch()) {
                    $errors[] = 'Invalid doctor ID';
                }
            }
        }
        
        if (!$isUpdate || isset($data['test_id'])) {
            if (empty($data['test_id'])) {
                $errors[] = 'Test ID is required';
            } else {
                // Verify test exists
                $stmt = $pdo->prepare('SELECT id FROM tests WHERE id = ?');
                $stmt->execute([$data['test_id']]);
                if (!$stmt->fetch()) {
                    $errors[] = 'Invalid test ID';
                }
            }
        }
        
        if (isset($data['status']) && !empty($data['status'])) {
            $validStatuses = ['pending', 'completed', 'cancelled', 'in_progress'];
            if (!in_array($data['status'], $validStatuses)) {
                $errors[] = 'Status must be pending, completed, cancelled, or in_progress';
            }
        }
        
        return $errors;
    }

    if ($action === 'list') {
        $userId = $_GET['user_id'] ?? null;
        $authenticatedUserId = authenticateUser($pdo);
        
        if (!$userId && $authenticatedUserId) {
            $userId = $authenticatedUserId;
        }
        
        // Search and filter functionality
        $search = $_GET['search'] ?? '';
        $patientId = $_GET['patient_id'] ?? null;
        $doctorId = $_GET['doctor_id'] ?? null;
        $testId = $_GET['test_id'] ?? null;
        $status = $_GET['status'] ?? null;
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        // Check if user wants to see all (master only)
        $viewerRole = $_SESSION['role'] ?? 'user';
        
        $whereClause = '';
        $params = [];
        $whereConditions = [];
        
        if (isset($_GET['all']) && $_GET['all'] == '1' && $viewerRole === 'master') {
            // Master can see all entries
        } else if ($userId) {
            $whereConditions[] = 'e.added_by = ?';
            $params[] = $userId;
        } else {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        // Add filters
        if (!empty($search)) {
            $whereConditions[] = '(p.name LIKE ? OR p.mobile LIKE ? OR d.name LIKE ? OR t.name LIKE ?)';
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }
        
        if ($patientId) {
            $whereConditions[] = 'e.patient_id = ?';
            $params[] = $patientId;
        }
        
        if ($doctorId) {
            $whereConditions[] = 'e.doctor_id = ?';
            $params[] = $doctorId;
        }
        
        if ($testId) {
            $whereConditions[] = 'e.test_id = ?';
            $params[] = $testId;
        }
        
        if ($status) {
            $whereConditions[] = 'e.status = ?';
            $params[] = $status;
        }
        
        if ($dateFrom) {
            $whereConditions[] = 'DATE(e.entry_date) >= ?';
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $whereConditions[] = 'DATE(e.entry_date) <= ?';
            $params[] = $dateTo;
        }
        
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total 
                       FROM entries e 
                       LEFT JOIN patients p ON e.patient_id = p.id
                       LEFT JOIN doctors d ON e.doctor_id = d.id
                       LEFT JOIN tests t ON e.test_id = t.id
                       ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch entries with related information
        $query = 'SELECT e.*, 
                        p.name as patient_name, p.mobile as patient_mobile, p.uhid,
                        d.name as doctor_name, d.hospital as doctor_hospital,
                        t.name as test_name, t.unit as test_unit,
                        u.username AS added_by_username
                 FROM entries e 
                 LEFT JOIN patients p ON e.patient_id = p.id
                 LEFT JOIN doctors d ON e.doctor_id = d.id
                 LEFT JOIN tests t ON e.test_id = t.id
                 LEFT JOIN users u ON e.added_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY e.entry_date DESC, e.id DESC
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $entries, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    if ($action === 'get') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT e.*, 
                                    p.name as patient_name, p.mobile as patient_mobile, p.uhid, p.sex as patient_sex, p.age as patient_age,
                                    d.name as doctor_name, d.hospital as doctor_hospital,
                                    t.name as test_name, t.unit as test_unit, t.reference_range, t.min, t.max, t.min_male, t.max_male, t.min_female, t.max_female,
                                    u.username AS added_by_username
                              FROM entries e 
                              LEFT JOIN patients p ON e.patient_id = p.id
                              LEFT JOIN doctors d ON e.doctor_id = d.id
                              LEFT JOIN tests t ON e.test_id = t.id
                              LEFT JOIN users u ON e.added_by = u.id 
                              WHERE e.id = ?');
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$entry) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $entry]);
    }

    if ($action === 'create' || $action === 'save') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateEntryData($input, $pdo);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare data for insertion
        $data = [
            'patient_id' => intval($input['patient_id']),
            'doctor_id' => intval($input['doctor_id']),
            'test_id' => intval($input['test_id']),
            'entry_date' => isset($input['entry_date']) && !empty($input['entry_date']) ? $input['entry_date'] : date('Y-m-d H:i:s'),
            'result_value' => trim($input['result_value'] ?? ''),
            'unit' => trim($input['unit'] ?? ''),
            'remarks' => trim($input['remarks'] ?? ''),
            'status' => $input['status'] ?? 'pending',
            'added_by' => $authenticatedUserId
        ];

        // Insert entry
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO entries (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $entryId = $pdo->lastInsertId();
        
        // Fetch the created entry
        $stmt = $pdo->prepare('SELECT e.*, 
                                    p.name as patient_name, p.mobile as patient_mobile, p.uhid,
                                    d.name as doctor_name, d.hospital as doctor_hospital,
                                    t.name as test_name, t.unit as test_unit,
                                    u.username AS added_by_username
                              FROM entries e 
                              LEFT JOIN patients p ON e.patient_id = p.id
                              LEFT JOIN doctors d ON e.doctor_id = d.id
                              LEFT JOIN tests t ON e.test_id = t.id
                              LEFT JOIN users u ON e.added_by = u.id 
                              WHERE e.id = ?');
        $stmt->execute([$entryId]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Entry created successfully', 'data' => $entry]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get entry ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
        }

        // Check if entry exists
        $stmt = $pdo->prepare('SELECT * FROM entries WHERE id = ?');
        $stmt->execute([$id]);
        $existingEntry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingEntry) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateEntryData($input, $pdo, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = ['patient_id', 'doctor_id', 'test_id', 'entry_date', 'result_value', 'unit', 'remarks', 'status'];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                if (in_array($field, ['patient_id', 'doctor_id', 'test_id'])) {
                    $updateData[$field] = intval($input[$field]);
                } else {
                    $updateData[$field] = trim($input[$field]);
                }
            }
        }

        if (empty($updateData)) {
            json_response(['success' => false, 'message' => 'No valid fields to update'], 400);
        }

        // Build update query
        $setParts = [];
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = :$field";
        }
        
        $query = 'UPDATE entries SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated entry
        $stmt = $pdo->prepare('SELECT e.*, 
                                    p.name as patient_name, p.mobile as patient_mobile, p.uhid,
                                    d.name as doctor_name, d.hospital as doctor_hospital,
                                    t.name as test_name, t.unit as test_unit,
                                    u.username AS added_by_username
                              FROM entries e 
                              LEFT JOIN patients p ON e.patient_id = p.id
                              LEFT JOIN doctors d ON e.doctor_id = d.id
                              LEFT JOIN tests t ON e.test_id = t.id
                              LEFT JOIN users u ON e.added_by = u.id 
                              WHERE e.id = ?');
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Entry updated successfully', 'data' => $entry]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
        }

        // Check if entry exists
        $stmt = $pdo->prepare('SELECT * FROM entries WHERE id = ?');
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$entry) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }

        // Delete entry
        $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Entry deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Entry API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
// https://hospital.codeapka.com/umakant/patho_api/entry.php - public API for test entries (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $pdo->query("SELECT e.id, p.name AS patient_name, d.name AS doctor_name, t.name AS test_name, e.entry_date, e.result_value, e.unit, e.remarks, e.status, e.added_by FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id ORDER BY e.id DESC");
        $rows = $stmt->fetchAll();
        json_response(['success'=>true,'data'=>$rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM entries WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Entry not found'],404);
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $id = $_POST['id'] ?? '';
        $patient_id = $_POST['patient_id'] ?? null;
        $doctor_id = $_POST['doctor_id'] ?? null;
        $test_id = $_POST['test_id'] ?? null;
        $entry_date = $_POST['entry_date'] ?? null;
        $result_value = trim($_POST['result_value'] ?? '');
        $unit = trim($_POST['unit'] ?? '');
        $remarks = trim($_POST['remarks'] ?? '');
        $status = $_POST['status'] ?? 'pending';
        $added_by = $_SESSION['user_id'] ?? null;
        if (empty($added_by) && !empty($PATHO_API_DEFAULT_USER_ID)) {
            $added_by = (int)$PATHO_API_DEFAULT_USER_ID;
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE entries SET patient_id=?, doctor_id=?, test_id=?, entry_date=?, result_value=?, unit=?, remarks=?, status=?, added_by=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $added_by, $id]);
            json_response(['success'=>true,'message'=>'Entry updated']);
        } else {
            $data = ['patient_id'=>$patient_id, 'doctor_id'=>$doctor_id, 'test_id'=>$test_id, 'entry_date'=>$entry_date, 'result_value'=>$result_value, 'unit'=>$unit, 'remarks'=>$remarks, 'status'=>$status, 'added_by'=>$added_by];
            $unique = ['patient_id'=>$patient_id, 'test_id'=>$test_id, 'entry_date'=>$entry_date];
            $res = upsert_or_skip($pdo, 'entries', $unique, $data);
            json_response(['success'=>true,'message'=>'Entry '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Entry deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
