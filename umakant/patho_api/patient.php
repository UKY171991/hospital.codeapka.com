<?php
/**
 * Universal API Template - Can be adapted for any entity
 * Just replace the entity-specific parts
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
require_once __DIR__ . '/../inc/api_config.php';

// CONFIGURATION - Update these for each entity
$ENTITY_TABLE = 'patients'; // Change this for each API
$ENTITY_NAME = 'Patient'; // Change this for each API
$REQUIRED_FIELDS = ['name', 'mobile']; // Change this for each API
$ALLOWED_FIELDS = ['name', 'mobile', 'age', 'age_unit', 'sex', 'uhid', 'address', 'father_husband']; // Change this for each API

// Field mapping for form to database
function mapFormToDb($data) {
    if (isset($data['gender'])) {
        $data['sex'] = $data['gender'];
        unset($data['gender']);
    }
    return $data;
}

// Field mapping for database to response
function mapDbToResponse($data) {
    if (isset($data['sex'])) {
        $data['gender'] = $data['sex']; // Also include gender for frontend compatibility
    }
    // Ensure added_by is present and normalized, keep added_by_username if available
    if (isset($data['added_by'])) {
        $data['added_by'] = intval($data['added_by']);
    } else {
        $data['added_by'] = null;
    }
    if (!isset($data['added_by_username'])) {
        $data['added_by_username'] = null;
    }
    return $data;
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
    // Entity-specific validation function
    function validateEntityData($data, $isUpdate = false) {
        global $ENTITY_NAME, $REQUIRED_FIELDS;
        $errors = [];
        
        foreach ($REQUIRED_FIELDS as $field) {
            if (!$isUpdate || isset($data[$field])) {
                if (empty(trim($data[$field] ?? ''))) {
                    $errors[] = ucfirst($field) . ' is required';
                }
            }
        }
        
        // Add entity-specific validation here
        if ($ENTITY_NAME === 'Patient') {
            if (isset($data['mobile']) && !empty($data['mobile'])) {
                if (!preg_match('/^[0-9+\-\s()]+$/', $data['mobile'])) {
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
        }
        
        return $errors;
    }

    if ($action === 'list') {
        $auth = authenticateApiUser($pdo);

        if (!$auth) {
            json_response(['success' => false, 'status' => 'error', 'message' => 'Authentication required'], 401);
        }

        $userId = $_GET['user_id'] ?? $auth['user_id'];

        // Pagination params (frontend sends page & limit)
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = max(1, intval($_GET['limit'] ?? 10));
        $offset = ($page - 1) * $limit;

        // Build base where clause
        $where = '';
        $params = [];

        // If not master/admin and not asking for all, restrict by added_by
        $isAll = (isset($_GET['all']) && $_GET['all'] == '1' && in_array($auth['role'], ['master', 'admin']));
        if (!$isAll) {
            $where = 'WHERE e.added_by = ?';
            $params[] = $userId;
        }

        // Total count for pagination
        $countQuery = "SELECT COUNT(*) as total FROM {$ENTITY_TABLE} e " . ($where ? $where : '');
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->execute($params);
        $total = (int) ($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        // Fetch paginated rows with join
        $query = "SELECT e.*, u.username AS added_by_username 
                  FROM {$ENTITY_TABLE} e 
                  LEFT JOIN users u ON e.added_by = u.id " . ($where ? $where : '') . " 
                  ORDER BY e.id DESC 
                  LIMIT ? OFFSET ?";

        // bind params + limit/offset
        $stmt = $pdo->prepare($query);
        $execParams = $params;
        $execParams[] = $limit;
        $execParams[] = $offset;
        $stmt->execute($execParams);

        $entities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Apply field mapping for each entity
        $entities = array_map('mapDbToResponse', $entities);

        json_response([
            'success' => true,
            'status' => 'success',
            'data' => $entities,
            'pagination' => ['total' => $total, 'page' => $page, 'limit' => $limit]
        ]);
    }

    if ($action === 'get') {
        $auth = authenticateApiUser($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => $ENTITY_NAME . ' ID is required'], 400);
        }
        
        $stmt = $pdo->prepare("SELECT e.*, u.username AS added_by_username 
                              FROM {$ENTITY_TABLE} e 
                              LEFT JOIN users u ON e.added_by = u.id 
                              WHERE e.id = ?");
        $stmt->execute([$id]);
        $entity = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$entity) {
            json_response(['success' => false, 'status' => 'error', 'message' => $ENTITY_NAME . ' not found'], 404);
        }
        
        // Apply field mapping
        $entity = mapDbToResponse($entity);
        
        json_response(['success' => true, 'status' => 'success', 'data' => $entity]);
    }

    if ($action === 'save') {
        $auth = authenticateApiUser($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Apply field mapping for form to database
        $input = mapFormToDb($input);
        
        // Check if this is an update (has ID)
        $isUpdate = isset($input['id']) && !empty($input['id']);
        
        if ($isUpdate) {
            // Update existing entity
            $entityId = intval($input['id']);
            
            // Check if entity exists
            $stmt = $pdo->prepare("SELECT * FROM {$ENTITY_TABLE} WHERE id = ?");
            $stmt->execute([$entityId]);
            $existingEntity = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existingEntity) {
                json_response(['success' => false, 'message' => $ENTITY_NAME . ' not found'], 404);
            }
            
            // Check permissions
            if (!checkPermission($auth, 'update', $existingEntity['added_by'])) {
                json_response(['success' => false, 'message' => 'Permission denied'], 403);
            }
            
            // Validate input
            $errors = validateEntityData($input, true);
            if (!empty($errors)) {
                json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
            }
            
            // Prepare update data
            $updateData = [];
            foreach ($ALLOWED_FIELDS as $field) {
                if (isset($input[$field])) {
                    $updateData[$field] = trim($input[$field]);
                }
            }

            // Allow admin/master to update added_by explicitly
            if (isset($input['added_by']) && in_array($auth['role'], ['master', 'admin'])) {
                $updateData['added_by'] = intval($input['added_by']);
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
            $params[] = $entityId;
            
            $query = "UPDATE {$ENTITY_TABLE} SET " . implode(', ', $setParts) . ", updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            
            // Fetch updated entity
            $stmt = $pdo->prepare("SELECT e.*, u.username AS added_by_username 
                                  FROM {$ENTITY_TABLE} e 
                                  LEFT JOIN users u ON e.added_by = u.id 
                                  WHERE e.id = ?");
            $stmt->execute([$entityId]);
            $entity = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Apply field mapping
            $entity = mapDbToResponse($entity);
            
            json_response(['success' => true, 'status' => 'success', 'message' => $ENTITY_NAME . ' updated successfully', 'data' => $entity, 'action' => 'updated', 'id' => $entityId]);
            
        } else {
            // Create new entity
            
            // Validate input
            $errors = validateEntityData($input);
            if (!empty($errors)) {
                json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
            }

            // Prepare data for insertion - set added_by from session by default
            $data = ['added_by' => $auth['user_id']];

            // Allow master/admin to set added_by explicitly when provided
            if (isset($input['added_by']) && in_array($auth['role'], ['master', 'admin'])) {
                $data['added_by'] = intval($input['added_by']);
            }
            
            foreach ($ALLOWED_FIELDS as $field) {
                if (isset($input[$field])) {
                    $data[$field] = trim($input[$field]);
                }
            }

            // Entity-specific processing
            if ($ENTITY_NAME === 'Patient' && empty($data['uhid'])) {
                // Generate UHID for patients
                $year = date('Y');
                $stmt = $pdo->prepare("SELECT MAX(CAST(SUBSTRING(uhid, 5) AS UNSIGNED)) as max_num FROM {$ENTITY_TABLE} WHERE uhid LIKE ?");
                $stmt->execute([$year . '%']);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $nextNum = ($result['max_num'] ?? 0) + 1;
                $data['uhid'] = $year . str_pad($nextNum, 6, '0', STR_PAD_LEFT);
            }

            // Insert entity
            $fields = array_keys($data);
            $placeholders = implode(', ', array_fill(0, count($fields), '?'));
            $query = "INSERT INTO {$ENTITY_TABLE} (" . implode(', ', $fields) . ", created_at) VALUES (" . $placeholders . ", NOW())";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute(array_values($data));
            
            $entityId = $pdo->lastInsertId();
            
            // Fetch the created entity
            $stmt = $pdo->prepare("SELECT e.*, u.username AS added_by_username 
                                  FROM {$ENTITY_TABLE} e 
                                  LEFT JOIN users u ON e.added_by = u.id 
                                  WHERE e.id = ?");
            $stmt->execute([$entityId]);
            $entity = $stmt->fetch(PDO::FETCH_ASSOC);

            // Apply field mapping
            $entity = mapDbToResponse($entity);

            json_response(['success' => true, 'status' => 'success', 'message' => $ENTITY_NAME . ' created successfully', 'data' => $entity, 'action' => 'inserted', 'id' => $entityId]);
        }
    }

    if ($action === 'delete') {
        $auth = authenticateApiUser($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => $ENTITY_NAME . ' ID is required'], 400);
        }

        // Check if entity exists
        $stmt = $pdo->prepare("SELECT * FROM {$ENTITY_TABLE} WHERE id = ?");
        $stmt->execute([$id]);
        $entity = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$entity) {
            json_response(['success' => false, 'message' => $ENTITY_NAME . ' not found'], 404);
        }

        // Check permissions
        if (!checkPermission($auth, 'delete', $entity['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied'], 403);
        }

        // Entity-specific deletion checks
        if ($ENTITY_NAME === 'Patient') {
            // Check if patient has associated entries
            $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM entries WHERE patient_id = ?');
            $stmt->execute([$id]);
            $entryCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            if ($entryCount > 0) {
                json_response(['success' => false, 'message' => 'Cannot delete patient with associated test entries'], 400);
            }
        }

        // Delete entity
        $stmt = $pdo->prepare("DELETE FROM {$ENTITY_TABLE} WHERE id = ?");
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => $ENTITY_NAME . ' deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action: ' . $action], 400);

} catch (Exception $e) {
    error_log($ENTITY_NAME . ' API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
