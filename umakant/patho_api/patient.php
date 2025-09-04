<?php
/**
 * Patient API - Comprehensive CRUD operations for patients
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

    // Validate patient data
    function validatePatientData($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['name'])) {
            if (empty(trim($data['name'] ?? ''))) {
                $errors[] = 'Patient name is required';
            }
        }
        
        if (!$isUpdate || isset($data['mobile'])) {
            $mobile = trim($data['mobile'] ?? '');
            if (empty($mobile)) {
                $errors[] = 'Mobile number is required';
            } elseif (!preg_match('/^[0-9+\-\s()]+$/', $mobile)) {
                $errors[] = 'Invalid mobile number format';
            }
        }
        
        if (isset($data['sex']) && !empty($data['sex'])) {
            $validGenders = ['Male', 'Female', 'Other'];
            if (!in_array($data['sex'], $validGenders)) {
                $errors[] = 'Sex must be Male, Female, or Other';
            }
        }
        
        if (isset($data['age'])) {
            $age = intval($data['age']);
            if ($age < 0 || $age > 150) {
                $errors[] = 'Age must be between 0 and 150';
            }
        }
        
        if (isset($data['age_unit']) && !empty($data['age_unit'])) {
            $validUnits = ['Years', 'Months', 'Days'];
            if (!in_array($data['age_unit'], $validUnits)) {
                $errors[] = 'Age unit must be Years, Months, or Days';
            }
        }
        
        return $errors;
    }

    // Generate UHID
    function generateUHID($pdo) {
        $prefix = 'P' . date('Y');
        $stmt = $pdo->prepare('SELECT MAX(CAST(SUBSTRING(uhid, 6) AS UNSIGNED)) as max_num FROM patients WHERE uhid LIKE ?');
        $stmt->execute([$prefix . '%']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxNum = $result['max_num'] ?? 0;
        return $prefix . str_pad($maxNum + 1, 6, '0', STR_PAD_LEFT);
    }

    if ($action === 'list') {
        $userId = $_GET['user_id'] ?? null;
        $authenticatedUserId = authenticateUser($pdo);
        
        if (!$userId && $authenticatedUserId) {
            $userId = $authenticatedUserId;
        }
        
        // Search functionality
        $search = $_GET['search'] ?? '';
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        // Check if user wants to see all (master only)
        $viewerRole = $_SESSION['role'] ?? 'user';
        
        $whereClause = '';
        $params = [];
        
        if (isset($_GET['all']) && $_GET['all'] == '1' && $viewerRole === 'master') {
            // Master can see all patients
            if (!empty($search)) {
                $whereClause = 'WHERE p.name LIKE ? OR p.mobile LIKE ? OR p.uhid LIKE ?';
                $searchParam = '%' . $search . '%';
                $params = [$searchParam, $searchParam, $searchParam];
            }
        } else if ($userId) {
            $whereClause = 'WHERE p.added_by = ?';
            $params = [$userId];
            
            if (!empty($search)) {
                $whereClause .= ' AND (p.name LIKE ? OR p.mobile LIKE ? OR p.uhid LIKE ?)';
                $searchParam = '%' . $search . '%';
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            }
        } else {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total FROM patients p ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch patients
        $query = 'SELECT p.*, u.username AS added_by_username 
                 FROM patients p 
                 LEFT JOIN users u ON p.added_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY p.id DESC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $patients, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    if ($action === 'get') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT p.*, u.username AS added_by_username 
                              FROM patients p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            json_response(['success' => false, 'message' => 'Patient not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $patient]);
    }

    if ($action === 'create' || $action === 'save') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validatePatientData($input);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Check for duplicate mobile number
        $stmt = $pdo->prepare('SELECT id FROM patients WHERE mobile = ?');
        $stmt->execute([trim($input['mobile'])]);
        if ($stmt->fetch()) {
            json_response(['success' => false, 'message' => 'Patient with this mobile number already exists'], 400);
        }

        // Generate UHID if not provided
        $uhid = trim($input['uhid'] ?? '') ?: generateUHID($pdo);

        // Prepare data for insertion
        $data = [
            'name' => trim($input['name']),
            'mobile' => trim($input['mobile']),
            'father_husband' => trim($input['father_husband'] ?? ''),
            'address' => trim($input['address'] ?? ''),
            'sex' => trim($input['sex'] ?? ''),
            'age' => intval($input['age'] ?? 0),
            'age_unit' => trim($input['age_unit'] ?? 'Years'),
            'uhid' => $uhid,
            'added_by' => $authenticatedUserId
        ];

        // Insert patient
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO patients (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $patientId = $pdo->lastInsertId();
        
        // Fetch the created patient
        $stmt = $pdo->prepare('SELECT p.*, u.username AS added_by_username 
                              FROM patients p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$patientId]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Patient created successfully', 'data' => $patient]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get patient ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
        }

        // Check if patient exists
        $stmt = $pdo->prepare('SELECT * FROM patients WHERE id = ?');
        $stmt->execute([$id]);
        $existingPatient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingPatient) {
            json_response(['success' => false, 'message' => 'Patient not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validatePatientData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Check for duplicate mobile number (excluding current patient)
        if (isset($input['mobile'])) {
            $stmt = $pdo->prepare('SELECT id FROM patients WHERE mobile = ? AND id != ?');
            $stmt->execute([trim($input['mobile']), $id]);
            if ($stmt->fetch()) {
                json_response(['success' => false, 'message' => 'Patient with this mobile number already exists'], 400);
            }
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = ['name', 'mobile', 'father_husband', 'address', 'sex', 'age', 'age_unit', 'uhid'];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                if ($field === 'age') {
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
        
        $query = 'UPDATE patients SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated patient
        $stmt = $pdo->prepare('SELECT p.*, u.username AS added_by_username 
                              FROM patients p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Patient updated successfully', 'data' => $patient]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
        }

        // Check if patient exists
        $stmt = $pdo->prepare('SELECT * FROM patients WHERE id = ?');
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            json_response(['success' => false, 'message' => 'Patient not found'], 404);
        }

        // Check if patient has associated entries
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM entries WHERE patient_id = ?');
        $stmt->execute([$id]);
        $entryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($entryCount > 0) {
            json_response(['success' => false, 'message' => 'Cannot delete patient with associated test entries'], 400);
        }

        // Delete patient
        $stmt = $pdo->prepare('DELETE FROM patients WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Patient deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Patient API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
// patho_api/patient.php - public API for patients (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';
$viewerRole = $_SESSION['role'] ?? null;
$viewerId = $_SESSION['user_id'] ?? null;

try {
    if ($action === 'list') {
        // Public list: return basic patient info matching UI columns
        $stmt = $pdo->query('SELECT id, uhid, name, age, age_unit, sex as gender, mobile as phone, father_husband, address, created_at FROM patients ORDER BY id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT id, uhid, name, age, age_unit, sex as gender, mobile as phone, father_husband, address, created_at FROM patients WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success' => false, 'message' => 'Patient not found'], 404);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // requires authenticated admin/master role per ajax behavior
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $father_husband = trim($_POST['father_husband'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $sex = $_POST['sex'] ?? '';
        $age = $_POST['age'] ?? null;
        $age_unit = $_POST['age_unit'] ?? 'Years';
        $uhid = trim($_POST['uhid'] ?? '');

        if ($id) {
            $stmt = $pdo->prepare('UPDATE patients SET name=?, mobile=?, father_husband=?, address=?, sex=?, age=?, age_unit=?, uhid=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $mobile, $father_husband, $address, $sex, $age, $age_unit, $uhid, $id]);
            json_response(['success' => true, 'message' => 'Patient updated']);
        } else {
            $data = ['name'=>$name, 'mobile'=>$mobile, 'father_husband'=>$father_husband, 'address'=>$address, 'sex'=>$sex, 'age'=>$age, 'age_unit'=>$age_unit, 'uhid'=>$uhid];
            if ($uhid !== '') $unique = ['uhid'=>$uhid];
            else $unique = ['name'=>$name, 'mobile'=>$mobile];
            $res = upsert_or_skip($pdo, 'patients', $unique, $data);
            json_response(['success' => true, 'message' => 'Patient '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM patients WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Patient deleted']);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
