<?php
/**
 * Zip Uploads API - Comprehensive CRUD operations for uploaded files
 * Supports: CREATE, READ, UPDATE, DELETE operations
 * Authentication: Session-based or API token
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key, X-API-Secret');

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
    switch($action) {
        case 'list':
            handleList($pdo);
            break;
        case 'get':
            handleGet($pdo);
            break;
        case 'create':
            handleCreate($pdo);
            break;
        case 'update':
            handleUpdate($pdo);
            break;
        case 'delete':
            handleDelete($pdo);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Zip Uploads API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function validateUploadData($data, $isUpdate = false) {
    $errors = [];
    if (!$isUpdate && empty($data['file_name'])) {
        $errors[] = 'File name is required';
    }
    if (!$isUpdate && empty($data['relative_path'])) {
        $errors[] = 'Relative path is required';
    }
    return $errors;
}

function handleList($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }
    if (!checkPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list uploads'], 403);
    }

    try {
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
        
        $countQuery = 'SELECT COUNT(*) as total FROM zip_uploads ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
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
    } catch (Exception $e) {
        error_log("List uploads error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch uploads'], 500);
    }
}

function handleGet($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Upload ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT z.*, u.username AS uploaded_by_username 
                              FROM zip_uploads z 
                              LEFT JOIN users u ON z.uploaded_by = u.id 
                              WHERE z.id = ?');
        $stmt->execute([$id]);
        $upload = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$upload) {
            json_response(['success' => false, 'message' => 'Upload not found'], 404);
        }

        if (!checkPermission($user_data, 'get', $upload['uploaded_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to view this upload'], 403);
        }
        
        json_response(['success' => true, 'data' => $upload]);
    } catch (Exception $e) {
        error_log("Get upload error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch upload'], 500);
    }
}

function handleCreate($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    if (!checkPermission($user_data, 'create')) {
        json_response(['success' => false, 'message' => 'Permission denied to create uploads'], 403);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    
    $errors = validateUploadData($input);
    if (!empty($errors)) {
        json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
    }

    try {
        $data = [
            'file_name' => trim($input['file_name']),
            'original_name' => trim($input['original_name'] ?? ''),
            'relative_path' => trim($input['relative_path']),
            'mime_type' => trim($input['mime_type'] ?? ''),
            'file_size' => isset($input['file_size']) ? intval($input['file_size']) : null,
            'uploaded_by' => $user_data['user_id'],
            'status' => $input['status'] ?? 'uploaded',
            'notes' => trim($input['notes'] ?? '')
        ];

        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO zip_uploads (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $uploadId = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare('SELECT z.*, u.username AS uploaded_by_username 
                              FROM zip_uploads z 
                              LEFT JOIN users u ON z.uploaded_by = u.id 
                              WHERE z.id = ?');
        $stmt->execute([$uploadId]);
        $upload = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Upload record created successfully', 'data' => $upload]);
    } catch (Exception $e) {
        error_log("Create upload error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to create upload'], 500);
    }
}

function handleUpdate($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Upload ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT uploaded_by FROM zip_uploads WHERE id = ?');
        $stmt->execute([$id]);
        $existingUpload = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingUpload) {
            json_response(['success' => false, 'message' => 'Upload not found'], 404);
        }

        if (!checkPermission($user_data, 'update', $existingUpload['uploaded_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to update this upload'], 403);
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        $errors = validateUploadData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

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

        $setParts = [];
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = :$field";
        }
        
        $query = 'UPDATE zip_uploads SET ' . implode(', ', $setParts) . ', updated_at = NOW() WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        $stmt = $pdo->prepare('SELECT z.*, u.username AS uploaded_by_username 
                              FROM zip_uploads z 
                              LEFT JOIN users u ON z.uploaded_by = u.id 
                              WHERE z.id = ?');
        $stmt->execute([$id]);
        $upload = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Upload updated successfully', 'data' => $upload]);
    } catch (Exception $e) {
        error_log("Update upload error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to update upload'], 500);
    }
}

function handleDelete($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Upload ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare('SELECT uploaded_by FROM zip_uploads WHERE id = ?');
        $stmt->execute([$id]);
        $upload = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$upload) {
            json_response(['success' => false, 'message' => 'Upload not found'], 404);
        }

        if (!checkPermission($user_data, 'delete', $upload['uploaded_by'])) {
            json_response(['success' => false, 'message' => 'Permission denied to delete this upload'], 403);
        }

        $stmt = $pdo->prepare('DELETE FROM zip_uploads WHERE id = ?');
        $result = $stmt->execute([$id]);

        if ($result) {
            json_response(['success' => true, 'message' => 'Upload deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete upload'], 500);
        }
    } catch (Exception $e) {
        error_log("Delete upload error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete upload'], 500);
    }
}
?>