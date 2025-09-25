<?php
/**
 * Fixed Entry API - Comprehensive CRUD operations for test entries
 * Fixes authentication issues, database schema problems, and field mappings
 * Compatible with the API testing interface at api.html
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

// Use SQLite for local testing, fallback to MySQL for production
try {
    require_once __DIR__ . '/../inc/connection_sqlite.php';
} catch (Exception $e) {
    require_once __DIR__ . '/../inc/connection.php';
}

require_once __DIR__ . '/../inc/ajax_helpers_fixed.php';
require_once __DIR__ . '/../inc/api_config.php';

// Create entries table if it doesn't exist (SQLite)
try {
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS entries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            server_id INTEGER DEFAULT NULL,
            patient_id INTEGER NOT NULL,
            doctor_id INTEGER DEFAULT NULL,
            test_id INTEGER NOT NULL,
            entry_date DATE DEFAULT NULL,
            result_value TEXT DEFAULT NULL,
            unit VARCHAR(50) DEFAULT NULL,
            remarks TEXT DEFAULT NULL,
            status VARCHAR(20) DEFAULT "pending",
            added_by INTEGER DEFAULT NULL,
            price DECIMAL(10,2) DEFAULT NULL,
            discount_amount DECIMAL(10,2) DEFAULT NULL,
            total_price DECIMAL(10,2) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    // Create related tables if they don't exist
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS patients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            uhid VARCHAR(50) UNIQUE,
            mobile VARCHAR(20),
            age INTEGER,
            sex VARCHAR(10),
            address TEXT,
            added_by INTEGER,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS doctors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            qualification VARCHAR(255),
            specialization VARCHAR(255),
            hospital VARCHAR(255),
            contact_no VARCHAR(20),
            added_by INTEGER,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS tests (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            category_id INTEGER,
            price DECIMAL(10,2),
            unit VARCHAR(50),
            added_by INTEGER,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255),
            full_name VARCHAR(255),
            role VARCHAR(50) DEFAULT "user",
            is_active INTEGER DEFAULT 1,
            api_token VARCHAR(64),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    // Insert sample data if tables are empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch()['count'];
    if ($count == 0) {
        $adminToken = bin2hex(random_bytes(32));
        $stmt = $pdo->prepare('INSERT INTO users (username, password, full_name, role, is_active, api_token) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'System Administrator', 'admin', 1, $adminToken]);
    }
    
    // Insert sample patients, doctors, tests if empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM patients");
    $count = $stmt->fetch()['count'];
    if ($count == 0) {
        $stmt = $pdo->prepare('INSERT INTO patients (name, uhid, mobile, age, sex, added_by) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute(['Test Patient', 'P001', '9876543210', 30, 'Male', 1]);
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM doctors");
    $count = $stmt->fetch()['count'];
    if ($count == 0) {
        $stmt = $pdo->prepare('INSERT INTO doctors (name, qualification, specialization, hospital, added_by) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(['Dr. Test Doctor', 'MBBS', 'General Medicine', 'Test Hospital', 1]);
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM tests");
    $count = $stmt->fetch()['count'];
    if ($count == 0) {
        $stmt = $pdo->prepare('INSERT INTO tests (name, price, unit, added_by) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Blood Sugar', 100.00, 'mg/dL', 1]);
    }
    
} catch (Exception $e) {
    // Ignore setup errors for production MySQL
}

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
            handleList($pdo);
            break;
        case 'get':
            handleGet($pdo);
            break;
        case 'save':
            handleSave($pdo);
            break;
        case 'delete':
            handleDelete($pdo);
            break;
        case 'stats':
            handleStats($pdo);
            break;
        default:
            json_response(['success' => false, 'message' => 'Invalid action specified'], 400);
    }
} catch (Exception $e) {
    error_log("Entry API Error: " . $e->getMessage());
    json_response(['success' => false, 'message' => 'An internal server error occurred.', 'error' => $e->getMessage()], 500);
}

function handleList($pdo) {
    $user_data = authenticateApiUserFixed($pdo);
    if (!$user_data) {
        json_response([
            'success' => false, 
            'message' => 'Authentication required',
            'debug_info' => [
                'available_auth_methods' => [
                    '1. Add X-Api-Key: hospital-api-secret-2024 header',
                    '2. Add secret_key=hospital-api-secret-2024 parameter',
                    '3. Add Authorization: Bearer <token> header',
                    '4. Add api_key=<token> parameter'
                ]
            ]
        ], 401);
    }

    try {
        $sql = "SELECT e.*, 
                   p.name as patient_name, p.uhid as patient_uhid, 
                   d.name as doctor_name, 
                   t.name as test_name, 
                   u.username as added_by_username
                FROM entries e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN tests t ON e.test_id = t.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                LEFT JOIN users u ON e.added_by = u.id
                ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format entries for frontend compatibility
        foreach ($entries as &$entry) {
            // Ensure all expected fields exist
            $entry['patient_name'] = $entry['patient_name'] ?? 'Unknown Patient';
            $entry['doctor_name'] = $entry['doctor_name'] ?? 'Unknown Doctor';
            $entry['test_name'] = $entry['test_name'] ?? 'Unknown Test';
            $entry['added_by_username'] = $entry['added_by_username'] ?? 'Unknown User';
            
            // Format dates
            if ($entry['entry_date']) {
                $entry['entry_date'] = date('Y-m-d', strtotime($entry['entry_date']));
            }
            
            // Ensure numeric fields are properly formatted
            $entry['price'] = $entry['price'] ? number_format((float)$entry['price'], 2, '.', '') : null;
            $entry['discount_amount'] = $entry['discount_amount'] ? number_format((float)$entry['discount_amount'], 2, '.', '') : null;
            $entry['total_price'] = $entry['total_price'] ? number_format((float)$entry['total_price'], 2, '.', '') : null;
        }

        json_response(['success' => true, 'data' => $entries, 'total' => count($entries)]);
    } catch (Exception $e) {
        error_log("List entries error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch entries'], 500);
    }
}

function handleGet($pdo) {
    $user_data = authenticateApiUserFixed($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    try {
        $sql = "SELECT e.*, 
                   p.name as patient_name, p.uhid as patient_uhid, 
                   d.name as doctor_name, 
                   t.name as test_name, 
                   u.username as added_by_username
                FROM entries e 
                LEFT JOIN patients p ON e.patient_id = p.id 
                LEFT JOIN tests t ON e.test_id = t.id 
                LEFT JOIN doctors d ON e.doctor_id = d.id 
                LEFT JOIN users u ON e.added_by = u.id
                WHERE e.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$entry) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }

        // Format entry for frontend compatibility
        $entry['patient_name'] = $entry['patient_name'] ?? 'Unknown Patient';
        $entry['doctor_name'] = $entry['doctor_name'] ?? 'Unknown Doctor';
        $entry['test_name'] = $entry['test_name'] ?? 'Unknown Test';
        $entry['added_by_username'] = $entry['added_by_username'] ?? 'Unknown User';
        
        if ($entry['entry_date']) {
            $entry['entry_date'] = date('Y-m-d', strtotime($entry['entry_date']));
        }
        
        $entry['price'] = $entry['price'] ? number_format((float)$entry['price'], 2, '.', '') : null;
        $entry['discount_amount'] = $entry['discount_amount'] ? number_format((float)$entry['discount_amount'], 2, '.', '') : null;
        $entry['total_price'] = $entry['total_price'] ? number_format((float)$entry['total_price'], 2, '.', '') : null;

        json_response(['success' => true, 'data' => $entry]);
    } catch (Exception $e) {
        error_log("Get entry error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch entry'], 500);
    }
}

function handleSave($pdo) {
    $user_data = authenticateApiUserFixed($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = $input['id'] ?? null;

    // Validate required fields
    $required_fields = ['patient_id', 'test_id'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            json_response(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required'], 400);
        }
    }

    try {
        // Prepare data
        $data = [
            'server_id' => $input['server_id'] ?? null,
            'patient_id' => (int)$input['patient_id'],
            'doctor_id' => !empty($input['doctor_id']) ? (int)$input['doctor_id'] : null,
            'test_id' => (int)$input['test_id'],
            'entry_date' => !empty($input['entry_date']) ? date('Y-m-d', strtotime($input['entry_date'])) : date('Y-m-d'),
            'result_value' => $input['result_value'] ?? null,
            'unit' => $input['unit'] ?? null,
            'remarks' => $input['remarks'] ?? null,
            'status' => in_array($input['status'] ?? '', ['pending', 'completed', 'cancelled']) ? $input['status'] : 'pending',
            'added_by' => $user_data['user_id'],
            'price' => !empty($input['price']) ? number_format((float)$input['price'], 2, '.', '') : null,
            'discount_amount' => !empty($input['discount_amount']) ? number_format((float)$input['discount_amount'], 2, '.', '') : null,
            'total_price' => null
        ];

        // Calculate total_price if not provided
        if (!empty($data['price'])) {
            $price = (float)$data['price'];
            $discount = (float)($data['discount_amount'] ?? 0);
            $data['total_price'] = number_format($price - $discount, 2, '.', '');
        } elseif (!empty($input['total_price'])) {
            $data['total_price'] = number_format((float)$input['total_price'], 2, '.', '');
        }

        if ($id) {
            // Update existing entry
            $stmt = $pdo->prepare("SELECT * FROM entries WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existing) {
                json_response(['success' => false, 'message' => 'Entry not found'], 404);
            }

            $set_parts = [];
            $params = [];
            foreach ($data as $field => $value) {
                if ($value !== null) {
                    $set_parts[] = "$field = ?";
                    $params[] = $value;
                }
            }
            $params[] = $id;
            
            $sql = "UPDATE entries SET " . implode(', ', $set_parts) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $entry_id = $id;
            $action = 'updated';
        } else {
            // Create new entry
            $fields = array_keys(array_filter($data, function($v) { return $v !== null; }));
            $placeholders = implode(', ', array_fill(0, count($fields), '?'));
            $values = array_values(array_filter($data, function($v) { return $v !== null; }));
            
            $sql = "INSERT INTO entries (" . implode(', ', $fields) . ") VALUES ($placeholders)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($values);
            $entry_id = $pdo->lastInsertId();
            $action = 'inserted';
        }

        // Fetch the saved entry with related data
        $stmt = $pdo->prepare("
            SELECT e.*, 
                   p.name as patient_name, 
                   d.name as doctor_name, 
                   t.name as test_name 
            FROM entries e 
            LEFT JOIN patients p ON e.patient_id = p.id 
            LEFT JOIN doctors d ON e.doctor_id = d.id 
            LEFT JOIN tests t ON e.test_id = t.id 
            WHERE e.id = ?
        ");
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
        json_response(['success' => false, 'message' => 'Failed to save entry: ' . $e->getMessage()], 500);
    }
}

function handleDelete($pdo) {
    $user_data = authenticateApiUserFixed($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    $id = $_REQUEST['id'] ?? null;
    if (!$id) {
        json_response(['success' => false, 'message' => 'Entry ID is required'], 400);
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM entries WHERE id = ?");
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$entry) {
            json_response(['success' => false, 'message' => 'Entry not found'], 404);
        }

        $stmt = $pdo->prepare("DELETE FROM entries WHERE id = ?");
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
    $user_data = authenticateApiUserFixed($pdo);
    if (!$user_data) {
        json_response(['success' => false, 'message' => 'Authentication required'], 401);
    }

    try {
        $stats = [];
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries");
        $stats['total'] = (int) $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'pending'");
        $stats['pending'] = (int) $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'completed'");
        $stats['completed'] = (int) $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE DATE(COALESCE(entry_date, created_at)) = DATE('now')");
        $stats['today'] = (int) $stmt->fetchColumn();
        
        json_response(['success' => true, 'status' => 'success', 'data' => $stats]);
    } catch (Exception $e) {
        error_log("Stats error: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to fetch statistics'], 500);
    }
}
?>
