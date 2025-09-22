<?php
/**
 * Entry API - Comprehensive CRUD operations for test entries
 * Supports: CREATE, READ, UPDATE, DELETE operations with statistics
 * Authentication: Multiple methods supported
 * Database Schema: Complete 16-column support with enriched data
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

// Entity Configuration for Entries
$entity_config = [
    'table_name' => 'entries',
    'id_field' => 'id',
    'required_fields' => ['patient_id', 'test_id'],
    'allowed_fields' => [
        // Exact columns per DB schema
        'server_id',
        'patient_id',
        'doctor_id',
        'test_id',
        'entry_date',
        'result_value',
        'unit',
        'remarks',
        'status',
        'added_by',
        'price',
        'discount_amount',
        'total_price',
        'created_at',
        'updated_at'
    ]
];

// Get action from request
$action = $_REQUEST['action'] ?? $_SERVER['REQUEST_METHOD'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map HTTP methods to actions
switch($requestMethod) {
    case 'GET':
        $action = isset($_GET['id']) ? 'get' : ($_GET['action'] ?? 'list');
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
    switch($action) {
        case 'list':
            handleList($pdo, $entity_config);
            break;
        case 'get':
            handleGet($pdo, $entity_config);
            break;
        case 'save':
            handleSave($pdo, $entity_config);
            break;
        case 'delete':
            handleDelete($pdo, $entity_config);
            break;
        case 'stats':
            handleStats($pdo);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Entry API Uncaught Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.'], 500);
}

function handleList($pdo, $config) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }
    if (!checkPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to list entries'], 403);
    }

    try {
        // Role-based scoping by added_by
        $scopeIds = getScopedUserIds($pdo, $user_data); // null => no restriction
        $where = '';
        $params = [];
        if (is_array($scopeIds)) {
            $placeholders = implode(',', array_fill(0, count($scopeIds), '?'));
            $where = ' WHERE e.added_by IN (' . $placeholders . ')';
            $params = $scopeIds;
        }

        $sql = "SELECT e.*, p.name as patient_name, p.uhid as patient_uhid, d.name as doctor_name, t.name as test_name, u.username as added_by_username
                FROM {$config['table_name']} e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN tests t ON e.test_id = t.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                LEFT JOIN users u ON e.added_by = u.id" .
                $where .
                " ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        json_response(['success' => true, 'data' => $entries, 'total' => count($entries)]);
    } catch (Exception $e) {
        error_log("List entries error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch entries'], 500);
    }
}

function handleGet($pdo, $config) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    try {
        $sql = "SELECT e.*, p.name as patient_name, p.uhid as patient_uhid, d.name as doctor_name, t.name as test_name, u.username as added_by_username
                FROM {$config['table_name']} e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN tests t ON e.test_id = t.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                LEFT JOIN users u ON e.added_by = u.id
                WHERE e.{$config['id_field']} = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$entry) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }

        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$entry['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to view this entry'], 403);
            }
        }

        json_response(['success' => true, 'data' => $entry]);
    } catch (Exception $e) {
        error_log("Get entry error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch entry'], 500);
    }
}

function handleSave($pdo, $config) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    if ($id) { // Update
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }
        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$existing['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to update this entry'], 403);
            }
        }
    } else { // Create
        // Any authenticated user can create; added_by will be current user
    }

    foreach ($config['required_fields'] as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    try {
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (array_key_exists($field, $input)) {
                $data[$field] = $input[$field];
            }
        }

        // Defaults and normalization
        $data['added_by'] = $data['added_by'] ?? $user_data['user_id'];
        if (isset($data['status']) && !in_array($data['status'], ['pending','completed','cancelled'], true)) {
            $data['status'] = 'pending';
        }
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }
        // Normalize entry_date to Y-m-d (accepts date or datetime)
        if (isset($data['entry_date']) && $data['entry_date'] !== '') {
            $ts = strtotime($data['entry_date']);
            if ($ts !== false) {
                $data['entry_date'] = date('Y-m-d', $ts);
            }
        }
        // Normalize decimals
        foreach (['price','discount_amount','total_price'] as $numField) {
            if (isset($data[$numField]) && $data[$numField] !== '') {
                $data[$numField] = number_format((float)$data[$numField], 2, '.', '');
            }
        }
        // Compute total_price if not provided but price/discount present
        if ((!isset($data['total_price']) || $data['total_price'] === '') && isset($data['price'])) {
            $price = (float)($data['price'] ?? 0);
            $disc = (float)($data['discount_amount'] ?? 0);
            $data['total_price'] = number_format($price - $disc, 2, '.', '');
        }

        if ($id) {
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            // Do not override DB-managed updated_at if client provided none
            if (!array_key_exists('updated_at', $data)) {
                $sql = "UPDATE {$config['table_name']} SET $set_clause, updated_at = NOW() WHERE {$config['id_field']} = ?";
            } else {
                $sql = "UPDATE {$config['table_name']} SET $set_clause WHERE {$config['id_field']} = ?";
            }
            $values = array_merge(array_values($data), [$id]);
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $entry_id = $id;
            $action = 'updated';
        } else {
            $cols = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($cols) VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array_values($data));
            $entry_id = $pdo->lastInsertId();
            $action = 'inserted';
        }

        $stmt = $pdo->prepare("SELECT e.*, p.name as patient_name, d.name as doctor_name, t.name as test_name FROM {$config['table_name']} e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id WHERE e.id = ?");
        $stmt->execute([$entry_id]);
        $saved_entry = $stmt->fetch(PDO::FETCH_ASSOC);

        json_response([
            'success' => true,
            'message' => "Entry {$action} successfully",
            'data' => $saved_entry,
            'id' => $entry_id
        ]);
    } catch (Exception $e) {
        error_log("Save entry error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to save entry'], 500);
    }
}

function handleDelete($pdo, $config) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare("SELECT added_by FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$entry) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }

        // Scoped visibility
        $scopeIds = getScopedUserIds($pdo, $user_data);
        if (is_array($scopeIds)) {
            if (!in_array((int)$entry['added_by'], $scopeIds, true)) {
                json_response(['success' => false, 'message' => 'Permission denied to delete this entry'], 403);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            json_response(['success' => true, 'message' => 'Entry deleted successfully']);
        } else {
            json_response(['success' => false, 'message' => 'Failed to delete entry'], 500);
        }
    } catch (Exception $e) {
        error_log("Delete entry error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete entry'], 500);
    }
}

function handleStats($pdo) {
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }
    if (!checkPermission($user_data, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied to view stats'], 403);
    }

    try {
        $stats = [];
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries");
        $stats['total'] = (int) $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'pending'");
        $stats['pending'] = (int) $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'completed'");
        $stats['completed'] = (int) $stmt->fetchColumn();
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE DATE(COALESCE(entry_date, created_at)) = CURDATE()");
        $stats['today'] = (int) $stmt->fetchColumn();
        
        json_response(['success' => true, 'data' => $stats]);
    } catch (Exception $e) {
        error_log("Stats error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch statistics'], 500);
    }
}
?>