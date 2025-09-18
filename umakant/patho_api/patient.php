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
$REQUIRED_FIELDS = ['name', 'mobile']; // keep DB column 'mobile' required but accept 'phone' input
// include 'added_by' so admin can provide it and it's recognized consistently
$ALLOWED_FIELDS = ['name', 'mobile', 'age', 'age_unit', 'sex', 'uhid', 'address', 'father_husband', 'added_by']; // DB columns (mobile remains the column)

// Field mapping for form to database
function mapFormToDb($data) {
    if (isset($data['gender'])) {
        $data['sex'] = $data['gender'];
        unset($data['gender']);
    }
    // Accept 'phone' from external clients and map to DB column 'mobile'
    if (isset($data['phone']) && !isset($data['mobile'])) {
        $data['mobile'] = $data['phone'];
        unset($data['phone']);
    }
    // Normalize added_by if provided from frontend (admin override)
    if (isset($data['added_by'])) {
        $data['added_by'] = intval($data['added_by']);
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
    // Ensure updated_at exists in response to avoid frontend undefined access
    if (!isset($data['updated_at'])) {
        $data['updated_at'] = null;
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
                    $errors[] = 'Invalid phone number format';
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

        if (!checkPermission($auth, 'list')) {
            json_response(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $userId = $_GET['user_id'] ?? $auth['user_id'];

        // Pagination params (frontend sends page & limit)
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

        if (!checkPermission($auth, 'get', $entity['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied'], 403);
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

            // Use upsert logic to prevent duplicates
            $uniqueWhere = [];
            
            // Define unique criteria based on entity type
            if ($ENTITY_NAME === 'Patient') {
                // For patients, use mobile as unique identifier
                if (!empty($data['mobile'])) {
                    $uniqueWhere['mobile'] = $data['mobile'];
                } else {
                    // Fallback to name + address if mobile not provided
                    $uniqueWhere['name'] = $data['name'];
                    if (!empty($data['address'])) {
                        $uniqueWhere['address'] = $data['address'];
                    }
                }
            } else {
                // Generic fallback - use name
                $uniqueWhere['name'] = $data['name'];
            }

            // Use upsert function to handle duplicates properly
            $result_info = upsert_or_skip($pdo, $ENTITY_TABLE, $uniqueWhere, $data);
            $entityId = $result_info['id'];
            $action = $result_info['action'];
            
            // Fetch the saved entity
            $stmt = $pdo->prepare("SELECT e.*, u.username AS added_by_username 
                                  FROM {$ENTITY_TABLE} e 
                                  LEFT JOIN users u ON e.added_by = u.id 
                                  WHERE e.id = ?");
            $stmt->execute([$entityId]);
            $entity = $stmt->fetch(PDO::FETCH_ASSOC);

            // Apply field mapping
            $entity = mapDbToResponse($entity);

            $message = match($action) {
                'inserted' => $ENTITY_NAME . ' created successfully',
                'updated' => $ENTITY_NAME . ' updated successfully', 
                'skipped' => $ENTITY_NAME . ' already exists (no changes needed)',
                default => $ENTITY_NAME . ' saved successfully'
            };

            json_response(['success' => true, 'status' => 'success', 'message' => $message, 'data' => $entity, 'action' => $action, 'id' => $entityId]);
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
