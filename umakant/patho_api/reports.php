<?php
/**
 * Reports API - Comprehensive CRUD operations for reports
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

    // Validate report data
    function validateReportData($data, $isUpdate = false) {
        $errors = [];
        
        if (!$isUpdate || isset($data['data'])) {
            if (empty(trim($data['data'] ?? ''))) {
                $errors[] = 'Report data is required';
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
        
        // Search functionality
        $search = $_GET['search'] ?? '';
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;
        $limit = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        
        // Check if user wants to see all (master only)
        $viewerRole = $_SESSION['role'] ?? 'user';
        
        $whereConditions = [];
        $params = [];
        
        if (isset($_GET['all']) && $_GET['all'] == '1' && $viewerRole === 'master') {
            // Master can see all reports
        } else if ($userId) {
            $whereConditions[] = 'r.added_by = ?';
            $params[] = $userId;
        } else {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }
        
        if (!empty($search)) {
            $whereConditions[] = 'r.data LIKE ?';
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
        }
        
        if ($dateFrom) {
            $whereConditions[] = 'DATE(r.created_at) >= ?';
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $whereConditions[] = 'DATE(r.created_at) <= ?';
            $params[] = $dateTo;
        }
        
        $whereClause = '';
        if (!empty($whereConditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        }
        
        // Count total records
        $countQuery = 'SELECT COUNT(*) as total FROM reports r ' . $whereClause;
        $stmt = $pdo->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Fetch reports
        $query = 'SELECT r.*, u.username AS added_by_username 
                 FROM reports r 
                 LEFT JOIN users u ON r.added_by = u.id 
                 ' . $whereClause . ' 
                 ORDER BY r.created_at DESC 
                 LIMIT ? OFFSET ?';
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'success' => true, 
            'data' => $reports, 
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    if ($action === 'get') {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Report ID is required'], 400);
        }
        
        $stmt = $pdo->prepare('SELECT r.*, u.username AS added_by_username 
                              FROM reports r 
                              LEFT JOIN users u ON r.added_by = u.id 
                              WHERE r.id = ?');
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$report) {
            json_response(['success' => false, 'message' => 'Report not found'], 404);
        }
        
        json_response(['success' => true, 'data' => $report]);
    }

    if ($action === 'create' || $action === 'save') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateReportData($input);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare data for insertion
        $data = [
            'data' => trim($input['data']),
            'added_by' => $authenticatedUserId
        ];

        // Insert report
        $fields = array_keys($data);
        $placeholders = ':' . implode(', :', $fields);
        $query = 'INSERT INTO reports (' . implode(', ', $fields) . ') VALUES (' . $placeholders . ')';
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($data);
        
        $reportId = $pdo->lastInsertId();
        
        // Fetch the created report
        $stmt = $pdo->prepare('SELECT r.*, u.username AS added_by_username 
                              FROM reports r 
                              LEFT JOIN users u ON r.added_by = u.id 
                              WHERE r.id = ?');
        $stmt->execute([$reportId]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Report created successfully', 'data' => $report]);
    }

    if ($action === 'update') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        // Get report ID from URL or input
        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Report ID is required'], 400);
        }

        // Check if report exists
        $stmt = $pdo->prepare('SELECT * FROM reports WHERE id = ?');
        $stmt->execute([$id]);
        $existingReport = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existingReport) {
            json_response(['success' => false, 'message' => 'Report not found'], 404);
        }

        // Get input data
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate input
        $errors = validateReportData($input, true);
        if (!empty($errors)) {
            json_response(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 400);
        }

        // Prepare update data
        $updateData = [];
        if (isset($input['data'])) {
            $updateData['data'] = trim($input['data']);
        }

        if (empty($updateData)) {
            json_response(['success' => false, 'message' => 'No valid fields to update'], 400);
        }

        // Build update query
        $setParts = [];
        foreach ($updateData as $field => $value) {
            $setParts[] = "$field = :$field";
        }
        
        $query = 'UPDATE reports SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        $updateData['id'] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($updateData);

        // Fetch updated report
        $stmt = $pdo->prepare('SELECT r.*, u.username AS added_by_username 
                              FROM reports r 
                              LEFT JOIN users u ON r.added_by = u.id 
                              WHERE r.id = ?');
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'message' => 'Report updated successfully', 'data' => $report]);
    }

    if ($action === 'delete') {
        $authenticatedUserId = authenticateUser($pdo);
        if (!$authenticatedUserId) {
            json_response(['success' => false, 'message' => 'Authentication required'], 401);
        }

        $id = $_GET['id'] ?? $_REQUEST['id'] ?? null;
        if (!$id) {
            json_response(['success' => false, 'message' => 'Report ID is required'], 400);
        }

        // Check if report exists
        $stmt = $pdo->prepare('SELECT * FROM reports WHERE id = ?');
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$report) {
            json_response(['success' => false, 'message' => 'Report not found'], 404);
        }

        // Delete report
        $stmt = $pdo->prepare('DELETE FROM reports WHERE id = ?');
        $stmt->execute([$id]);

        json_response(['success' => true, 'message' => 'Report deleted successfully']);
    }

    // Invalid action
    json_response(['success' => false, 'message' => 'Invalid action'], 400);

} catch (Exception $e) {
    error_log('Reports API Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Internal server error', 'error' => $e->getMessage()], 500);
}
?>
