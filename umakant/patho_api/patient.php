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

// Get action from request
$action = $_REQUEST['action'] ?? 'list';
if ($action === 'update') $action = 'save';

try {
    // Simple validation
    function validatePatientData($data) {
        if (empty(trim($data['name'] ?? ''))) {
            return ['Patient name is required'];
        }
        if (empty(trim($data['uhid'] ?? ''))) {
            return ['UHID is required'];
        }
        if (isset($data['age']) && ($data['age'] < 0 || $data['age'] > 150)) {
            return ['Age must be between 0 and 150'];
        }
        return [];
    }

    if ($action === 'list') {
        $auth = simpleAuthenticate($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        // Get user ID - prioritize provided user_id, then authenticated user
        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? $auth['user_id'];
        
        // DataTables parameters
        $draw = $_POST['draw'] ?? 1;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 25;
        $search = $_POST['search']['value'] ?? $_GET['search'] ?? '';
        $addedByFilter = $_POST['added_by'] ?? $_GET['added_by'] ?? '';
        
        // Build WHERE clause
        $whereClauses = ["p.added_by = ?"];
        $params = [$userId];
        
        // Search filter
        if (!empty($search)) {
            $whereClauses[] = "(p.name LIKE ? OR p.uhid LIKE ? OR p.mobile LIKE ? OR p.email LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        // Added by filter (overrides user filter if provided)
        if (!empty($addedByFilter)) {
            $whereClauses[0] = "p.added_by = ?";
            $params[0] = $addedByFilter;
        }
        
        $whereSql = " WHERE " . implode(" AND ", $whereClauses);
        
        // Get total and filtered counts
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM patients p");
        $totalRecords = $totalStmt->fetchColumn();
        
        $filteredStmt = $pdo->prepare("SELECT COUNT(*) FROM patients p LEFT JOIN users u ON p.added_by = u.id" . $whereSql);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();
        
        // Get patients data
        $query = "SELECT p.id, p.uhid, p.name, p.father_husband, p.mobile, p.email, p.age, p.age_unit, p.gender, p.address, p.added_by, p.created_at, u.username AS added_by_username 
                  FROM patients p 
                  LEFT JOIN users u ON p.added_by = u.id" . $whereSql . " 
                  ORDER BY p.id DESC LIMIT ?, ?";
        $stmt = $pdo->prepare($query);
        $params[] = $start;
        $params[] = $length;
        $stmt->execute($params);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        json_response([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'success' => true,
            'data' => $patients
        ]);
    }

    if ($action === 'get') {
        $auth = simpleAuthenticate($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT p.id, p.uhid, p.name, p.father_husband, p.mobile, p.email, p.age, p.age_unit, p.gender, p.address, p.added_by, p.created_at, u.username AS added_by_username 
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

    if ($action === 'save') {
        $auth = simpleAuthenticate($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = $_POST;
            
            // Validate input
        $errors = validatePatientData($input);
            if (!empty($errors)) {
                json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
            }
            
        // Check if this is an update (has ID)
        if (isset($input['id']) && !empty($input['id'])) {
            // Update existing patient
            $patientId = intval($input['id']);
            
            $stmt = $pdo->prepare('UPDATE patients SET uhid=?, name=?, father_husband=?, mobile=?, email=?, age=?, age_unit=?, gender=?, address=?, added_by=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([
                trim($input['uhid']),
                trim($input['name']),
                trim($input['father_husband'] ?? ''),
                trim($input['mobile'] ?? ''),
                trim($input['email'] ?? ''),
                intval($input['age'] ?? 0),
                trim($input['age_unit'] ?? 'Years'),
                trim($input['gender'] ?? ''),
                trim($input['address'] ?? ''),
                intval($input['added_by'] ?? $auth['user_id']),
                $patientId
            ]);
            
            $message = 'Patient updated successfully';
            $action = 'updated';
            $id = $patientId;
        } else {
            // Create new patient
            $stmt = $pdo->prepare('INSERT INTO patients (uhid, name, father_husband, mobile, email, age, age_unit, gender, address, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([
                trim($input['uhid']),
                trim($input['name']),
                trim($input['father_husband'] ?? ''),
                trim($input['mobile'] ?? ''),
                trim($input['email'] ?? ''),
                intval($input['age'] ?? 0),
                trim($input['age_unit'] ?? 'Years'),
                trim($input['gender'] ?? ''),
                trim($input['address'] ?? ''),
                intval($input['added_by'] ?? $auth['user_id'])
            ]);
            
            $message = 'Patient created successfully';
            $action = 'created';
            $id = $pdo->lastInsertId();
        }
        
        // Fetch the saved patient
        $stmt = $pdo->prepare('SELECT p.id, p.uhid, p.name, p.father_husband, p.mobile, p.email, p.age, p.age_unit, p.gender, p.added_by, p.created_at, u.username AS added_by_username 
                              FROM patients p 
                              LEFT JOIN users u ON p.added_by = u.id 
                              WHERE p.id = ?');
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        json_response(['success' => true, 'message' => $message, 'data' => $patient, 'action' => $action, 'id' => $id]);
    }

    if ($action === 'delete') {
        $auth = simpleAuthenticate($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
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

    if ($action === 'stats') {
        $auth = simpleAuthenticate($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        $userId = $_GET['user_id'] ?? $_POST['user_id'] ?? $auth['user_id'];
        
        // Get statistics for the user's patients
        $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ?');
        $totalStmt->execute([$userId]);
        $total = $totalStmt->fetchColumn();
        
        $todayStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ? AND DATE(created_at) = CURDATE()');
        $todayStmt->execute([$userId]);
        $today = $todayStmt->fetchColumn();
        
        $maleStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ? AND gender = "Male"');
        $maleStmt->execute([$userId]);
        $male = $maleStmt->fetchColumn();
        
        $femaleStmt = $pdo->prepare('SELECT COUNT(*) FROM patients WHERE added_by = ? AND gender = "Female"');
        $femaleStmt->execute([$userId]);
        $female = $femaleStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'today' => $today,
                'male' => $male,
                'female' => $female
            ]
        ]);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action: ' . $action], 400);

} catch (Exception $e) {
    error_log('Patient API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>