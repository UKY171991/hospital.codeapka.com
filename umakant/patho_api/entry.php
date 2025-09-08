<?php
/**
 * Entry API - Comprehensive CRUD operations for test entries
 * Supports: CREATE, READ, UPDATE, DELETE operation        $sql = "SELECT e.*, 
                       p.name as patient_name, p.uhid,
                       t.name as test_name, t.u        // Set default values
        if (!isset($data['entry_date'])) {
            $data['entry_date'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }units, t.min_male as normal_value_male, t.max_male as normal_value_male_max, t.min_female as normal_value_female, t.max_female as normal_value_female_max, t.min as normal_value_child, t.max as normal_value_child_max,
                       d.name as doctor_name
                FROM {$config['table_name']} e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN tests t ON e.test_id = t.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                ORDER BY e.entry_date DESC, e.id DESC";hentication: Multiple methods supported
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

// Helper to detect if patients table has a given column (cached)
function patientHasColumnPatho($pdo, $col) {
    static $cols = null;
    if ($cols === null) {
        $stmt = $pdo->query("SHOW COLUMNS FROM patients");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    return in_array($col, $cols);
}

// Entity Configuration for Entries
$entity_config = [
    'table_name' => 'entries',
    'id_field' => 'id',
    'required_fields' => ['patient_id', 'test_id', 'result_value'],
    'allowed_fields' => [
        'patient_id', 'test_id', 'result_value', 'status', 'remarks',
        'entry_date', 'doctor_id', 'unit'
    ],
    'permission_map' => [
        'list' => 'read',
        'get' => 'read', 
        'save' => 'write',
        'delete' => 'delete'
    ]
];

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
    // Authenticate user
    $user_data = authenticateApiUser($pdo);
    if (!$user_data) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
        exit;
    }

    // Check permissions
    $required_permission = $entity_config['permission_map'][$action] ?? 'read';
    if (!checkPermission($user_data, $required_permission)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
        exit;
    }

    switch($action) {
        case 'list':
            handleList($pdo, $entity_config);
            break;
            
        case 'get':
            handleGet($pdo, $entity_config);
            break;
            
        case 'save':
            handleSave($pdo, $entity_config, $user_data);
            break;
            
        case 'delete':
            handleDelete($pdo, $entity_config);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    error_log("Entry API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

function handleList($pdo, $config) {
    try {
        // Choose gender select safely
        if (patientHasColumnPatho($pdo, 'gender') && patientHasColumnPatho($pdo, 'sex')) {
            $genderSelect = "COALESCE(p.gender, p.sex) as gender";
        } elseif (patientHasColumnPatho($pdo, 'gender')) {
            $genderSelect = "p.gender as gender";
        } elseif (patientHasColumnPatho($pdo, 'sex')) {
            $genderSelect = "p.sex as gender";
        } else {
            $genderSelect = "NULL as gender";
        }

        $sql = "SELECT e.*, 
                       p.patient_name, p.uhid, $genderSelect,
                       t.name as test_name, t.unit as units, t.min_male as normal_value_male, t.max_male as normal_value_male_max, t.min_female as normal_value_female, t.max_female as normal_value_female_max, t.min as normal_value_child, t.max as normal_value_child_max,
                       d.doctor_name
                FROM {$config['table_name']} e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN tests t ON e.test_id = t.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                ORDER BY e.test_date DESC, e.id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $entries,
            'total' => count($entries)
        ]);
    } catch (Exception $e) {
        error_log("List entries error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch entries']);
    }
}

function handleGet($pdo, $config) {
    try {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Entry ID is required']);
            return;
        }

        // Safe gender select for single entry
        if (patientHasColumnPatho($pdo, 'gender') && patientHasColumnPatho($pdo, 'sex')) {
            $genderSelect = "COALESCE(p.gender, p.sex) as gender";
        } elseif (patientHasColumnPatho($pdo, 'gender')) {
            $genderSelect = "p.gender as gender";
        } elseif (patientHasColumnPatho($pdo, 'sex')) {
            $genderSelect = "p.sex as gender";
        } else {
            $genderSelect = "NULL as gender";
        }

        $sql = "SELECT e.*, 
                       p.name as patient_name, p.uhid, p.age, p.sex as gender,
                       t.name as test_name, t.unit as units, t.min_male as normal_value_male, t.max_male as normal_value_male_max, t.min_female as normal_value_female, t.max_female as normal_value_female_max, t.min as normal_value_child, t.max as normal_value_child_max,
                       d.name as doctor_name
                FROM {$config['table_name']} e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN tests t ON e.test_id = t.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                WHERE e.{$config['id_field']} = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$entry) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Entry not found']);
            return;
        }

        echo json_encode(['success' => true, 'data' => $entry]);
    } catch (Exception $e) {
        error_log("Get entry error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch entry']);
    }
}

function handleSave($pdo, $config, $user_data) {
    try {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate required fields
        foreach ($config['required_fields'] as $field) {
            if (empty($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
                return;
            }
        }

        // Additional validation for entries
        if (!is_numeric($input['patient_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid patient ID']);
            return;
        }

        if (!is_numeric($input['test_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid test ID']);
            return;
        }

        // Check if patient exists
        $stmt = $pdo->prepare("SELECT id FROM patients WHERE id = ?");
        $stmt->execute([$input['patient_id']]);
        if (!$stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Patient does not exist']);
            return;
        }

        // Check if test exists
        $stmt = $pdo->prepare("SELECT id FROM tests WHERE id = ?");
        $stmt->execute([$input['test_id']]);
        if (!$stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Test does not exist']);
            return;
        }

        // Prepare data for saving
        $data = [];
        foreach ($config['allowed_fields'] as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        // Set default values
        if (!isset($data['test_date'])) {
            $data['test_date'] = date('Y-m-d');
        }
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        if (!isset($data['result_status'])) {
            $data['result_status'] = 'normal';
        }

        $id = $input['id'] ?? null;
        $is_update = !empty($id);

        if ($is_update) {
            // Update existing entry
            $set_clause = implode(', ', array_map(fn($field) => "$field = ?", array_keys($data)));
            $sql = "UPDATE {$config['table_name']} SET $set_clause WHERE {$config['id_field']} = ?";
            $values = array_merge(array_values($data), [$id]);
        } else {
            // Create new entry
            $fields = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$config['table_name']} ($fields) VALUES ($placeholders)";
            $values = array_values($data);
        }

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($values);

        if ($result) {
            $entry_id = $is_update ? $id : $pdo->lastInsertId();
            
            // Fetch the saved entry with related data
            $stmt = $pdo->prepare("SELECT e.*, 
                                           p.patient_name, p.uhid,
                                           t.name as test_name, t.unit as units,
                                           d.doctor_name
                                   FROM {$config['table_name']} e 
                                   LEFT JOIN patients p ON e.patient_id = p.id 
                                   LEFT JOIN tests t ON e.test_id = t.id 
                                   LEFT JOIN doctors d ON e.doctor_id = d.id 
                                   WHERE e.{$config['id_field']} = ?");
            $stmt->execute([$entry_id]);
            $saved_entry = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'message' => $is_update ? 'Entry updated successfully' : 'Entry created successfully',
                'data' => $saved_entry
            ]);
        } else {
            throw new Exception('Failed to save entry');
        }

    } catch (Exception $e) {
        error_log("Save entry error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save entry']);
    }
}

function handleDelete($pdo, $config) {
    try {
        $id = $_REQUEST['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Entry ID is required']);
            return;
        }

        // Check if entry exists
        $stmt = $pdo->prepare("SELECT id FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Entry not found']);
            return;
        }

        // Delete the entry
        $stmt = $pdo->prepare("DELETE FROM {$config['table_name']} WHERE {$config['id_field']} = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Entry deleted successfully']);
        } else {
            throw new Exception('Failed to delete entry');
        }

    } catch (Exception $e) {
        error_log("Delete entry error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete entry']);
    }
}
?>
