<?php
/**
 * Zip Uploads API - Comprehensive CRUD operations for file uploads
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

// Support unified 'save' action: choose create vs update by presence of id
if ($action === 'save') {
    $raw = file_get_contents('php://input');
    $input = json_decode($raw, true);
    if (!is_array($input) || empty($input)) { $input = $_POST; }
    $id = $_GET['id'] ?? $_REQUEST['id'] ?? ($input['id'] ?? null);
    if ($id) {
        $_REQUEST['id'] = $id;
        $action = 'update';
    } else {
        $action = 'create';
    }
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

    // Check if user has permission to manage uploads
    function checkUploadPermission($pdo, $userId) {
        $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && in_array($user['role'], ['master', 'admin']);
    }

    // Validate upload data
    function validateUploadData($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['file_name'])) {
            if (empty(trim($data['file_name'] ?? ''))) {
                $errors[] = 'File name is required';
            }
        }
        
        if (!$isUpdate || isset($data['relative_path'])) {
            if (empty(trim($data['relative_path'] ?? ''))) {
                $errors[] = 'Relative path is required';
            }
        }
        
        if (isset($data['file_size'])) {
            $fileSize = intval($data['file_size']);
            if ($fileSize < 0) {
                $errors[] = 'File size cannot be negative';
            }
        }
        
        if (isset($data['status'])) {
            $validStatuses = ['uploaded', 'processing', 'completed', 'failed', 'deleted'];
            if (!in_array($data['status'], $validStatuses)) {
                $errors[] = 'Status must be uploaded, processing, completed, failed, or deleted';
            }
        }
        
        return $errors;
    }

    if ($action === 'list') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkUploadPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }
        
        // Search functionality
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        $whereConditions = [];
        $params = [];
        
        if (!empty($search)) {
            $whereConditions[] = '(file_name LIKE ? OR original_name LIKE ? OR relative_path LIKE ?)';
            $searchParam = '%' . $search . '%';
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if (!empty($status)) {
            $whereConditions[] = 'status = ?';
            $params[] = $status;
        }
        
        if ($dateFrom) {
            $whereConditions[] = 'DATE(created_at) >= ?';
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $whereConditions[] = 'DATE(created_at) <= ?';
            $params[] = $dateTo;
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total FROM zip_uploads ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch uploads
        $query = 'SELECT z.*, u.username AS uploaded_by_username 
                 FROM zip_uploads z 
                 LEFT JOIN users u ON z.uploaded_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY z.created_at DESC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $uploads, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    if ($action === 'get') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Upload ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT z.*, u.username AS uploaded_by_username 
                              FROM zip_uploads z 
                              LEFT JOIN users u ON z.uploaded_by = u.id 
                              WHERE z.id = ?');
        $stmt->execute([$id]);
        $upload = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$upload) {
            json_response(['success' => false, 'message' => 'Upload not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $upload]);
    }

    if ($action === 'create') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkUploadPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateUploadData($input);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare data for insertion
        $data = [
            'file_name' => trim($input['file_name']),
            'original_name' => trim($input['original_name'] ?? ''),
            'relative_path' => trim($input['relative_path']),
            'mime_type' => trim($input['mime_type'] ?? ''),
            'file_size' => isset($input['file_size']) ? intval($input['file_size']) : null,
            'uploaded_by' => $authenticatedUserId,
            'status' => $input['status'] ?? 'uploaded',
            'notes' => trim($input['notes'] ?? '')
        ];

        // Insert upload record
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO zip_uploads (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $uploadId = $pdo->lastInsertId();
        
        // Fetch the created upload
        $stmt = $pdo->prepare('SELECT z.*, u.username AS uploaded_by_username 
                              FROM zip_uploads z 
                              LEFT JOIN users u ON z.uploaded_by = u.id 
                              WHERE z.id = ?');
        $stmt->execute([$uploadId]);
        $upload = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Upload record created successfully', 'data' => $upload]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkUploadPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        // Get upload ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Upload ID is required'], 400);
        }

        // Check if upload exists
        $stmt = $pdo->prepare('SELECT * FROM zip_uploads WHERE id = ?');
        $stmt->execute([$id]);
        $existingUpload = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingUpload) {
            json_response(['success' => false, 'message' => 'Upload not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateUploadData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare update data
        $updateData = [];
        $allowedFields = ['file_name', 'original_name', 'relative_path', 'mime_type', 'file_size', 'status', 'notes'];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                if ($field === 'file_size') {
                    $updateData[$field] = isset($input[$field]) ? intval($input[$field]) : null;
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
        
        $query = 'UPDATE zip_uploads SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated upload
        $stmt = $pdo->prepare('SELECT z.*, u.username AS uploaded_by_username 
                              FROM zip_uploads z 
                              LEFT JOIN users u ON z.uploaded_by = u.id 
                              WHERE z.id = ?');
        $stmt->execute([$id]);
        $upload = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Upload updated successfully', 'data' => $upload]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Check permission
        if (!checkUploadPermission($pdo, $authenticatedUserId)) {
            json_response(['success' => false, 'message' => 'Insufficient permissions'], 403);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Upload ID is required'], 400);
        }

        // Check if upload exists
        $stmt = $pdo->prepare('SELECT * FROM zip_uploads WHERE id = ?');
        $stmt->execute([$id]);
        $upload = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$upload) {
            json_response(['success' => false, 'message' => 'Upload not found'], 404);
        }

        // Delete upload record
        $stmt = $pdo->prepare('DELETE FROM zip_uploads WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Upload record deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Zip Uploads API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
