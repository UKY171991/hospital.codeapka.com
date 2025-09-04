<?php
/**
 * Doctor API - Comprehensive CRUD operations for doctors
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
        $userId = $_GET['user_id'] ?? null;
        $authenticatedUserId = authenticateUser($pdo);
        
        if (!$userId && $authenticatedUserId) {
            $userId = $authenticatedUserId;
        }
        
        // Check if user wants to see all (master only)
        $viewerRole = $_SESSION['role'] ?? 'user';
        if (isset($_GET['all']) && $_GET['all'] == '1' && $viewerRole === 'master') {
            $query = 'SELECT d.*, u.username AS added_by_username 
                     FROM doctors d 
                     LEFT JOIN users u ON d.added_by = u.id 
                     ORDER BY d.id DESC';
            $stmt = $pdo->query($query);
        } else if ($userId) {
            $query = 'SELECT d.*, u.username AS added_by_username 
                     FROM doctors d 
                     LEFT JOIN users u ON d.added_by = u.id 
                     WHERE d.added_by = ? 
                     ORDER BY d.id DESC';
            $stmt = $pdo->prepare($query);
            $stmt->execute([$userId]);
        } else {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $doctors, 'total' => count($doctors)]);
    }

    if ($action === 'get') {
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

    if ($action === 'create' || $action === 'save') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateDoctorData($input);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare data for insertion
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
            'percent' => floatval($input['percent'] ?? 0),
            'added_by' => $authenticatedUserId
        ];

        if (isset($input['server_id'])) {
            $data['server_id'] = intval($input['server_id']);
        }

        // Insert doctor
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO doctors (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $doctorId = $pdo->lastInsertId();
        
        // Fetch the created doctor
        $stmt = $pdo->prepare('SELECT d.*, u.username AS added_by_username 
                              FROM doctors d 
                              LEFT JOIN users u ON d.added_by = u.id 
                              WHERE d.id = ?');
        $stmt->execute([$doctorId]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Doctor created successfully', 'data' => $doctor]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get doctor ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Doctor ID is required'], 400);
        }

        // Check if doctor exists
        $stmt = $pdo->prepare('SELECT * FROM doctors WHERE id = ?');
        $stmt->execute([$id]);
        $existingDoctor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingDoctor) {
            json_response(['success' => false, 'message' => 'Doctor not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateDoctorData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare update data
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

        // Add updated_at timestamp
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        // Build update query
        $setParts = [];
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = :$field";
        }
        
        $query = 'UPDATE doctors SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated doctor
        $stmt = $pdo->prepare('SELECT d.*, u.username AS added_by_username 
                              FROM doctors d 
                              LEFT JOIN users u ON d.added_by = u.id 
                              WHERE d.id = ?');
        $stmt->execute([$id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Doctor updated successfully', 'data' => $doctor]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
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
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Doctor API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
            if (is_array($tmp)) $bodyJson = $tmp;
        }

        // 1) Session-based
        if (isset($_SESSION['user_id'])) {
            $authenticatedUserId = $_SESSION['user_id'];
        }

        // 2) Token-based: Authorization: Bearer <token> or api_key param
        if (!$authenticatedUserId) {
            $token = null;
            // Check Authorization header
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
            if ($authHeader && preg_match('/Bearer\s+(\S+)/', $authHeader, $m)) {
                $token = $m[1];
            }
            if (!$token && isset($_REQUEST['api_key'])) $token = $_REQUEST['api_key'];

            if ($token) {
                $tstmt = $pdo->prepare('SELECT id FROM users WHERE api_token = ? AND is_active = 1 LIMIT 1');
                $tstmt->execute([$token]);
                $u = $tstmt->fetch();
                if ($u) $authenticatedUserId = $u['id'];
            }
        }

        // 3) Credentials in request: accept username & password in the POST (form-data/json)
        if (!$authenticatedUserId) {
            // Look for username/password in request (POST/form-data or query)
            $reqUsername = $_REQUEST['username'] ?? ($bodyJson['username'] ?? null);
            $reqPassword = $_REQUEST['password'] ?? ($bodyJson['password'] ?? null);
            if ($reqUsername && $reqPassword) {
                // Fetch user row and verify password using same rules as login.php
                $ustmt = $pdo->prepare('SELECT id, password, is_active FROM users WHERE username = ? LIMIT 1');
                $ustmt->execute([$reqUsername]);
                $urow = $ustmt->fetch();
                if ($urow && $urow['is_active']) {
                    $stored = $urow['password'] ?? '';
                    $passOk = false;
                    if (is_string($stored) && (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0 || strpos($stored, '$argon') === 0 || password_needs_rehash($stored, PASSWORD_DEFAULT) || password_verify($reqPassword, $stored))) {
                        if (password_verify($reqPassword, $stored)) $passOk = true;
                    }
                    if (!$passOk && is_string($stored)) {
                        $len = strlen($stored);
                        if ($len === 32) { // MD5
                            if (hash_equals($stored, md5($reqPassword))) $passOk = true;
                        } elseif ($len === 40) { // SHA1
                            if (hash_equals($stored, sha1($reqPassword))) $passOk = true;
                        }
                    }
                    if (!$passOk) {
                        if (hash_equals((string)$stored, (string)$reqPassword)) $passOk = true;
                    }
                    if ($passOk) $authenticatedUserId = $urow['id'];
                }
            }
        }

                // 4) Secret-based direct insert (server-to-server): X-Api-Key header or secret_key param
                if (!$authenticatedUserId && !empty($PATHO_API_SECRET)) {
                    $reqSecret = $_SERVER['HTTP_X_API_KEY'] ?? $_REQUEST['secret_key'] ?? ($bodyJson['secret_key'] ?? null);
                    if ($reqSecret && hash_equals($PATHO_API_SECRET, $reqSecret)) {
                        // Use configured default user id for added_by
                        $authenticatedUserId = $PATHO_API_DEFAULT_USER_ID ?: null;
                    }
                }

        // Allow unauthenticated inserts: if there's no authenticated user, we will insert with added_by = NULL
    // (Keep other auth flows like delete unchanged.)

    // If we have an authenticated user id, fetch their role so we can allow privileged fields like added_by
    $authenticatedUserRole = null;
    if ($authenticatedUserId) {
        $rstmt = $pdo->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
        $rstmt->execute([$authenticatedUserId]);
        $rrow = $rstmt->fetch();
        if ($rrow && isset($rrow['role'])) $authenticatedUserRole = $rrow['role'];
    }
    // Accept JSON body as well as form-encoded
    $input = $_POST;
        // If we already decoded JSON earlier into $bodyJson, reuse it; avoids reading php://input twice
        if (is_array($bodyJson)) {
            $input = array_merge($input, $bodyJson);
        } elseif (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) $input = array_merge($input, $json);
        }

    // Prevent importing client-supplied id values — server controls id. added_by is handled below based on role.
    if (isset($input['id'])) unset($input['id']);

    $name = trim($input['name'] ?? '');
        $qualification = trim($input['qualification'] ?? '');
        $specialization = trim($input['specialization'] ?? '');
        $hospital = trim($input['hospital'] ?? '');
        $contact_no = trim($input['contact_no'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $email = trim($input['email'] ?? '');
        $address = trim($input['address'] ?? '');
        $registration_no = trim($input['registration_no'] ?? '');
    $percent = isset($input['percent']) ? $input['percent'] : null;
    if ($percent === '') $percent = null;
    if ($percent !== null) $percent = (float)$percent;
            // If an admin/master explicitly provided added_by in the input, allow it. Otherwise use authenticated user id (may be null).
            $added_by = $authenticatedUserId;
            if (isset($input['added_by']) && is_numeric($input['added_by']) && in_array($authenticatedUserRole, ['master','admin'])) {
                $added_by = (int)$input['added_by'];
            }
            // If no authenticated user and a default API user id is configured, use it so records created via secret/anonymous flows
            // still have a sensible added_by value instead of NULL. This helps when clients insert without session cookies.
            if (empty($added_by) && !empty($PATHO_API_DEFAULT_USER_ID)) {
                $added_by = (int)$PATHO_API_DEFAULT_USER_ID;
            }

        if ($name === '') {
            json_response(['success'=>false,'message'=>'Name is required'],400);
        }

        // If id provided in input treat as update (id must be integer) — client cannot change added_by
        $updateId = isset($input['id']) && is_numeric($input['id']) ? (int)$input['id'] : null;
        if ($updateId) {
            // update existing (preserve added_by)
            $stmt = $pdo->prepare('UPDATE doctors SET server_id = ?, name=?, qualification=?, specialization=?, hospital=?, contact_no=?, phone=?, email=?, address=?, registration_no=?, percent=?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$input['server_id'] ?? null, $name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $percent, $updateId]);
            json_response(['success'=>true,'message'=>'Doctor updated','id'=>$updateId]);
        }

        // Prepare data for insert/update
        // Include server_id if client provides one (useful for sync)
        $server_id = isset($input['server_id']) ? (is_numeric($input['server_id']) ? (int)$input['server_id'] : null) : null;
        $data = [
            'server_id' => $server_id,
            'name'=>$name,
            'qualification'=>$qualification,
            'specialization'=>$specialization,
            'hospital'=>$hospital,
            'contact_no'=>$contact_no,
            'phone'=>$phone,
            'email'=>$email,
            'address'=>$address,
            'registration_no'=>$registration_no,
            'percent'=>$percent,
            'added_by'=>$added_by
        ];

        // Determine unique criteria: prefer registration_no if provided, else name+phone+hospital
        if ($registration_no !== '') {
            $unique = ['registration_no'=>$registration_no];
        } else {
            // prefer contact_no, fallback to phone
            $contactKey = $contact_no !== '' ? $contact_no : $phone;
            $unique = ['name'=>$name, 'contact_no'=>$contactKey, 'hospital'=>$hospital];
        }

        $res = upsert_or_skip($pdo, 'doctors', $unique, $data);
        json_response(['success'=>true,'message'=>'Doctor '.$res['action'],'id'=>$res['id']]);
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['user_id'])) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $toDelete = $_POST['id'];
        // fetch row to check ownership
        $stmt = $pdo->prepare('SELECT added_by FROM doctors WHERE id = ?');
        $stmt->execute([$toDelete]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Not found'],404);
        $owner = $row['added_by'];
        $viewerRole = $_SESSION['role'] ?? 'user';
        $viewerId = $_SESSION['user_id'];
        // allow delete if master/admin or owner
        if ($viewerRole !== 'master' && $viewerRole !== 'admin' && $owner != $viewerId) {
            json_response(['success'=>false,'message'=>'Unauthorized'],403);
        }
        $del = $pdo->prepare('DELETE FROM doctors WHERE id = ?');
        $del->execute([$toDelete]);
        json_response(['success'=>true,'message'=>'Doctor deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Exception $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
