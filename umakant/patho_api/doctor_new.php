<?php
/**
 * Doctor API - Comprehensive CRUD operations for doctors
 * Supports: CREATE, READ, UPDATE, DELETE operations
 * Authentication: Multiple methods supported (session, API token, shared secret, etc.)
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
    // Validate doctor data
    function validateDoctorData($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['name'])) {
            if (empty(trim($data['name'] ?? ''))) {
                $errors[] = 'Doctor name is required';
            }
        }
        
        if (isset($data['email']) && !empty($data['email'])) {
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
        }
        
        if (isset($data['percent'])) {
            $percent = floatval($data['percent']);
            if ($percent < 0 || $percent > 100) {
                $errors[] = 'Percentage must be between 0 and 100';
            }
        }
        
        return $errors;
    }

    if ($action === 'list') {
        $auth = authenticateApiUser($pdo);
        
        // For listing, we need some form of authentication
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        $userId = $_GET['user_id'] ?? $auth['user_id'];
        
        // Check if user wants to see all (master/admin only)
        if (isset($_GET['all']) && $_GET['all'] == '1' && in_array($auth['role'], ['master', 'admin'])) {
            $query = 'SELECT d.*, u.username AS added_by_username 
                     FROM doctors d 
                     LEFT JOIN users u ON d.added_by = u.id 
                     ORDER BY d.id DESC';
            $stmt = $pdo->query($query);
        } else {
            $query = 'SELECT d.*, u.username AS added_by_username 
                     FROM doctors d 
                     LEFT JOIN users u ON d.added_by = u.id 
                     WHERE d.added_by = ? 
                     ORDER BY d.id DESC';
            $stmt = $pdo->prepare($query);
            $stmt->execute([$userId]);
        }
        
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $doctors, 'total' => count($doctors)]);
    }

    if ($action === 'get') {
        $auth = authenticateApiUser($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT d.*, u.username AS added_by_username 
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
        $auth = authenticateApiUser($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Check if this is an update (has ID)
        $isUpdate = isset($input['id']) && !empty($input['id']);
        
        if ($isUpdate) {
            // Update existing doctor
            $doctorId = intval($input['id']);
            
            // Check if doctor exists and get current data
            $stmt = $pdo->prepare('SELECT * FROM doctors WHERE id = ?');
            $stmt->execute([$doctorId]);
            $existingDoctor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existingDoctor) {
                json_response(['success' => false, 'message' => 'Doctor not found'], 404);
            }
            
            // Check permissions
            if (!checkPermission($auth, 'update', $existingDoctor['added_by'])) {
                json_response(['success' => false, 'message' => 'Permission denied'], 403);
            }
            
            // Validate input
            $errors = validateDoctorData($input, true);
            if (!empty($errors)) {
                json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
            }
            
            // Prepare update data (only include fields that are provided)
            $updateData = [];
            $allowedFields = ['name', 'qualification', 'specialization', 'hospital', 'contact_no', 'phone', 'email', 'address', 'registration_no', 'percent', 'server_id'];
            
            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    if ($field === 'percent') {
                        $updateData[$field] = floatval($input[$field]);
                    } elseif ($field === 'server_id') {
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
            $params = [];
            foreach ($updateData as $field => $value) {
                $setParts[] = "$field = ?";
                $params[] = $value;
            }
            $params[] = $doctorId;
            
            $query = 'UPDATE doctors SET ' . implode(', ', $setParts) . ', updated_at = NOW() WHERE id = ?';
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            
            // Fetch updated doctor
            $stmt = $pdo->prepare('SELECT d.*, u.username AS added_by_username 
                                  FROM doctors d 
                                  LEFT JOIN users u ON d.added_by = u.id 
                                  WHERE d.id = ?');
            $stmt->execute([$doctorId]);
            $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            json_response(['success' => true, 'message' => 'Doctor updated successfully', 'data' => $doctor, 'action' => 'updated', 'id' => $doctorId]);
            
        } else {
            // Create new doctor or upsert
            
            // Validate input
            $errors = validateDoctorData($input);
            if (!empty($errors)) {
                json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
            }

            // Prepare data for insertion/upsert
            $data = [
                'name' => trim($input['name']),
                'qualification' => trim($input['qualification'] ?? ''),
                'specialization' => trim($input['specialization'] ?? ''),
                'hospital' => trim($input['hospital'] ?? ''),
                'contact_no' => trim($input['contact_no'] ?? ''),
                'phone' => trim($input['phone'] ?? ''),
                'email' => trim($input['email'] ?? ''),
                'address' => trim($input['address'] ?? ''),
                'registration_no' => trim($input['registration_no'] ?? ''),
                'percent' => isset($input['percent']) ? floatval($input['percent']) : null,
                'added_by' => $auth['user_id']
            ];

            if (isset($input['server_id'])) {
                $data['server_id'] = intval($input['server_id']);
            }
            
            // Remove null percent if empty
            if ($data['percent'] === null || $data['percent'] === '') {
                unset($data['percent']);
            }

            // Determine unique criteria for upsert
            $uniqueWhere = [];
            if (!empty($data['registration_no'])) {
                $uniqueWhere['registration_no'] = $data['registration_no'];
            } else {
                $uniqueWhere['name'] = $data['name'];
                $uniqueWhere['hospital'] = $data['hospital'];
                // Use contact_no if available, otherwise phone
                $contactField = !empty($data['contact_no']) ? 'contact_no' : 'phone';
                $contactValue = !empty($data['contact_no']) ? $data['contact_no'] : $data['phone'];
                if (!empty($contactValue)) {
                    $uniqueWhere[$contactField] = $contactValue;
                }
            }

            // Perform upsert
            $result = upsert_or_skip($pdo, 'doctors', $uniqueWhere, $data);
            
            // Fetch the doctor record
            $stmt = $pdo->prepare('SELECT d.*, u.username AS added_by_username 
                                  FROM doctors d 
                                  LEFT JOIN users u ON d.added_by = u.id 
                                  WHERE d.id = ?');
            $stmt->execute([$result['id']]);
            $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

            $message = ($result['action'] === 'inserted') ? 'Doctor created successfully' : 
                      (($result['action'] === 'updated') ? 'Doctor updated successfully' : 'Doctor already exists (no changes needed)');
            
            json_response(['success' => true, 'message' => $message, 'data' => $doctor, 'action' => $result['action'], 'id' => $result['id']]);
        }
    }

    if ($action === 'delete') {
        $auth = authenticateApiUser($pdo);
        if (!$auth) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
        }

        // Check if doctor exists
        $stmt = $pdo->prepare('SELECT * FROM doctors WHERE id = ?');
        $stmt->execute([$id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$doctor) {
            json_response(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        // Check permissions
        if (!checkPermission($auth, 'delete', $doctor['added_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied'], 403);
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
