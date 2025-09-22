<?php
/**
 * Fixed Patient API - Demonstrates proper authentication handling
 * This shows how to fix the 401 authentication issues in the original APIs
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection_sqlite.php'; // Use SQLite for local testing
require_once __DIR__ . '/../inc/ajax_helpers_fixed.php';
require_once __DIR__ . '/../inc/api_config.php';

// Create patients table if it doesn't exist
try {
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS patients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            uhid VARCHAR(50) UNIQUE,
            name VARCHAR(255) NOT NULL,
            father_husband VARCHAR(255),
            mobile VARCHAR(20),
            email VARCHAR(255),
            age INTEGER,
            age_unit VARCHAR(10) DEFAULT "Years",
            sex VARCHAR(10),
            address TEXT,
            added_by INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
} catch (Exception $e) {
    // Ignore setup errors
}

// Get action from request
$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map HTTP methods to actions
switch($requestMethod) {
    case 'GET':
        $action = isset($_GET['id']) ? 'get' : 'list';
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
    if ($action === 'list') {
        $auth = authenticateApiUserFixed($pdo);

        if (!$auth) {
            json_response([
                'success' => false, 
                'status' => 'error', 
                'message' => 'Authentication required',
                'debug_info' => [
                    'available_auth_methods' => [
                        '1. Add Authorization: Bearer <token> header',
                        '2. Add api_key=<token> parameter',
                        '3. Add X-Api-Key: hospital-api-secret-2024 header',
                        '4. Add secret_key=hospital-api-secret-2024 parameter'
                    ]
                ]
            ], 401);
        }

        if (!checkPermission($auth, 'list')) {
            json_response(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $userId = $_GET['user_id'] ?? $auth['user_id'];

        // Pagination params
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, intval($_GET['limit'] ?? 10));
        $offset = ($page - 1) * $limit;
        
        // Build base where clause
        $where = '';
        $params = [];

        // If user is master/admin, show all by default unless explicitly disabled by all=0
        if (in_array($auth['role'], ['master', 'admin'])) {
            $isAll = !isset($_GET['all']) || $_GET['all'] == '1';
        } else {
            $isAll = false;
        }

        // If not allowed to view all, restrict by added_by (normal users)
        if (!$isAll) {
            $where = 'WHERE added_by = ?';
            $params[] = $userId;
        }

        // Total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM patients " . ($where ? $where : '');
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->execute($params);
        $total = (int) ($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        // Fetch paginated rows
        $query = "SELECT * FROM patients " . ($where ? $where : '') . " ORDER BY id DESC LIMIT ? OFFSET ?";

        // bind params + limit/offset
        $stmt = $pdo->prepare($query);
        $execParams = $params;
        $execParams[] = $limit;
        $execParams[] = $offset;
        $stmt->execute($execParams);

        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        json_response([
            'success' => true,
            'status' => 'success',
            'data' => $patients,
            'pagination' => ['total' => $total, 'page' => $page, 'limit' => $limit]
        ]);
    }

    if ($action === 'get') {
        $auth = authenticateApiUserFixed($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
        }
        
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            json_response(['success' => false, 'status' => 'error', 'message' => 'Patient not found'], 404);
        }

        if (!checkPermission($auth, 'get', $patient['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied'], 403);
        }
        
        json_response(['success' => true, 'status' => 'success', 'data' => $patient]);
    }

    if ($action === 'save') {
        $auth = authenticateApiUserFixed($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Check if this is an update (has ID)
        $isUpdate = isset($input['id']) && !empty($input['id']);
        
        if ($isUpdate) {
            // Update existing patient
            $patientId = intval($input['id']);
            
            // Check if patient exists
            $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
            $stmt->execute([$patientId]);
            $existingPatient = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existingPatient) {
                json_response(['success' => false, 'message' => 'Patient not found'], 404);
            }
            
            // Check permissions
            if (!checkPermission($auth, 'update', $existingPatient['added_by'])) {
                json_response(['success' => false, 'message' => 'Permission denied'], 403);
            }
            
            // Prepare update data
            $updateData = [];
            $allowedFields = ['name', 'mobile', 'age', 'age_unit', 'sex', 'uhid', 'address', 'father_husband'];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $updateData[$field] = trim($input[$field]);
                }
            }
            
            if (empty($updateData)) {
                json_response(['success' => false, 'message' => 'No valid fields to update'], 400);
            }
            
            // Build update query
            $setParts = [];
            $params = [];
            foreach ($updateData as $field => $value) {
                $setParts[] = "$field = ?";
                $params[] = $value;
            }
            $params[] = $patientId;
            
            $query = "UPDATE patients SET " . implode(', ', $setParts) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            
            // Fetch updated patient
            $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
            $stmt->execute([$patientId]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);
            
            json_response(['success' => true, 'status' => 'success', 'message' => 'Patient updated successfully', 'data' => $patient, 'action' => 'updated', 'id' => $patientId]);
            
        } else {
            // Create new patient
            
            // Validate required fields
            if (empty(trim($input['name'] ?? ''))) {
                json_response(['success' => false, 'message' => 'Patient name is required'], 400);
            }
            
            if (empty(trim($input['mobile'] ?? ''))) {
                json_response(['success' => false, 'message' => 'Mobile number is required'], 400);
            }

            // Prepare data for insertion
            $data = [
                'name' => trim($input['name']),
                'mobile' => trim($input['mobile']),
                'age' => isset($input['age']) ? intval($input['age']) : null,
                'age_unit' => trim($input['age_unit'] ?? 'Years'),
                'sex' => trim($input['sex'] ?? ''),
                'uhid' => trim($input['uhid'] ?? ''),
                'address' => trim($input['address'] ?? ''),
                'father_husband' => trim($input['father_husband'] ?? ''),
                'added_by' => $auth['user_id']
            ];

            // Generate UHID if not provided
            if (empty($data['uhid'])) {
                $year = date('Y');
                $stmt = $pdo->prepare("SELECT MAX(CAST(SUBSTRING(uhid, 5) AS INTEGER)) as max_num FROM patients WHERE uhid LIKE ?");
                $stmt->execute([$year . '%']);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $nextNum = ($result['max_num'] ?? 0) + 1;
                $data['uhid'] = $year . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
            }

            // Use upsert logic to prevent duplicates
            $uniqueWhere = ['mobile' => $data['mobile']];

            // Use upsert function to handle duplicates properly
            $result_info = upsert_or_skip($pdo, 'patients', $uniqueWhere, $data);
            $patientId = $result_info['id'];
            $action_result = $result_info['action'];
            
            // Fetch the saved patient
            $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
            $stmt->execute([$patientId]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);

            $message = match($action_result) {
                'inserted' => 'Patient created successfully',
                'updated' => 'Patient updated successfully', 
                'skipped' => 'Patient already exists (no changes needed)',
                default => 'Patient saved successfully'
            };

            json_response(['success' => true, 'status' => 'success', 'message' => $message, 'data' => $patient, 'action' => $action_result, 'id' => $patientId]);
        }
    }

    if ($action === 'delete') {
        $auth = authenticateApiUserFixed($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Patient ID is required'], 400);
        }

        // Check if patient exists
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            json_response(['success' => false, 'message' => 'Patient not found'], 404);
        }

        // Check permissions
        if (!checkPermission($auth, 'delete', $patient['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied'], 403);
        }

        // Delete patient
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Patient deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action: ' . $action], 400);

} catch (Exception $e) {
    error_log('Patient API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>