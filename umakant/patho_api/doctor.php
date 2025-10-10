<?php
/**
 * Doctor API - Lightweight CRUD for doctors.php page
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
    function validateDoctorData($data) {
        if (empty(trim($data['name'] ?? ''))) {
            return ['Doctor name is required'];
        }
        if (isset($data['percent']) && ($data['percent'] < 0 || $data['percent'] > 100)) {
            return ['Percentage must be between 0 and 100'];
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
        $whereClauses = ["d.added_by = ?"];
        $params = [$userId];
        
        // Search filter
        if (!empty($search)) {
            $whereClauses[] = "(d.name LIKE ? OR d.hospital LIKE ? OR d.contact_no LIKE ?)";
            $searchTerm = "%" . $search . "%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        // Added by filter (overrides user filter if provided)
        if (!empty($addedByFilter)) {
            $whereClauses[0] = "d.added_by = ?";
            $params[0] = $addedByFilter;
        }
        
        $whereSql = " WHERE " . implode(" AND ", $whereClauses);
        
        // Get total and filtered counts
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM doctors d");
        $totalRecords = $totalStmt->fetchColumn();
        
        $filteredStmt = $pdo->prepare("SELECT COUNT(*) FROM doctors d LEFT JOIN users u ON d.added_by = u.id" . $whereSql);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();
        
        // Get doctors data
        $query = "SELECT d.id, d.name, d.hospital, d.contact_no, d.percent, d.added_by, d.created_at, u.username AS added_by_username 
                  FROM doctors d 
                  LEFT JOIN users u ON d.added_by = u.id" . $whereSql . " 
                  ORDER BY d.id DESC LIMIT ?, ?";
        $stmt = $pdo->prepare($query);
        $params[] = $start;
        $params[] = $length;
        $stmt->execute($params);
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'success' => true,
            'data' => $doctors
        ]);
    }

    if ($action === 'get') {
        $auth = simpleAuthenticate($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT d.id, d.name, d.hospital, d.contact_no, d.percent, d.address, d.added_by, d.created_at, u.username AS added_by_username 
                              FROM doctors d 
                              LEFT JOIN users u ON d.added_by = u.id 
                              WHERE d.id = ?');
        $stmt->execute([$id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            json_response(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        json_response(['success' => true, 'data' => $doctor]);
    }

    if ($action === 'save') {
        $auth = simpleAuthenticate($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = $_POST;
        
        // Validate input
        $errors = validateDoctorData($input);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }
        
        // Check if this is an update (has ID)
        if (isset($input['id']) && !empty($input['id'])) {
            // Update existing doctor
            $doctorId = intval($input['id']);
            
            $stmt = $pdo->prepare('UPDATE doctors SET name=?, hospital=?, contact_no=?, percent=?, address=?, added_by=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([
                trim($input['name']),
                trim($input['hospital'] ?? ''),
                trim($input['contact_no'] ?? ''),
                floatval($input['percent'] ?? 0),
                trim($input['address'] ?? ''),
                intval($input['added_by'] ?? $auth['user_id']),
                $doctorId
            ]);
            
            $message = 'Doctor updated successfully';
            $action = 'updated';
            $id = $doctorId;
        } else {
            // Create new doctor
            $stmt = $pdo->prepare('INSERT INTO doctors (name, hospital, contact_no, percent, address, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([
                trim($input['name']),
                trim($input['hospital'] ?? ''),
                trim($input['contact_no'] ?? ''),
                floatval($input['percent'] ?? 0),
                trim($input['address'] ?? ''),
                intval($input['added_by'] ?? $auth['user_id'])
            ]);
            
            $message = 'Doctor created successfully';
            $action = 'created';
            $id = $pdo->lastInsertId();
        }
        
        // Fetch the saved doctor
        $stmt = $pdo->prepare('SELECT d.id, d.name, d.hospital, d.contact_no, d.percent, d.added_by, d.created_at, u.username AS added_by_username 
                              FROM doctors d 
                              LEFT JOIN users u ON d.added_by = u.id 
                              WHERE d.id = ?');
        $stmt->execute([$id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        json_response(['success' => true, 'message' => $message, 'data' => $doctor, 'action' => $action, 'id' => $id]);
    }

    if ($action === 'delete') {
        $auth = simpleAuthenticate($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
        }

        // Check if doctor has associated entries
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM entries WHERE doctor_id = ?');
        $stmt->execute([$id]);
        $entryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($entryCount > 0) {
            json_response(['success' => false, 'message' => 'Cannot delete doctor with associated test entries'], 400);
        }

        // Delete doctor
        $stmt = $pdo->prepare('DELETE FROM doctors WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Doctor deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action: ' . $action], 400);

} catch (Exception $e) {
    error_log('Doctor API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>