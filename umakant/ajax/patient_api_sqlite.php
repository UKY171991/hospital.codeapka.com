<?php
// ajax/patient_api_sqlite.php - Patient API with SQLite for testing
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once '../inc/connection_sqlite.php';
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'list':
            handleList();
            break;
        case 'stats':
            handleStats();
            break;
        case 'create':
            handleCreate();
            break;
        case 'read':
            handleRead();
            break;
        case 'update':
            handleUpdate();
            break;
        case 'delete':
            handleDelete();
            break;
        default:
            throw new Exception('Invalid action specified');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'debug' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}

// Helper to check if a column exists in patients table for SQLite
function patientHasColumnSqlite($col) {
    static $cols = null;
    global $pdo;
    if ($cols === null) {
        $stmt = $pdo->query("PRAGMA table_info('patients')");
        $info = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cols = array_map(function($r){ return $r['name']; }, $info);
    }
    return in_array($col, $cols);
}

function handleList() {
    global $pdo;
    
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = max(1, min(100, intval($_GET['limit'] ?? 10)));
    $search = trim($_GET['search'] ?? '');
    $offset = ($page - 1) * $limit;
    
    try {
        // Build the WHERE clause for search
        $whereClause = '';
        $params = [];
        
        if (!empty($search)) {
            $whereClause = ' WHERE name LIKE :search OR uhid LIKE :search OR mobile LIKE :search';
            $params[':search'] = "%$search%";
        }
        
        // Count total records
        $countSql = "SELECT COUNT(*) as total FROM patients" . $whereClause;
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = $countStmt->fetch()['total'];
        
        if ($totalRecords == 0) {
            echo json_encode([
                'success' => true,
                'data' => [],
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => 0,
                    'total_records' => 0,
                    'per_page' => $limit
                ],
                'message' => 'No patients found'
            ]);
            return;
        }
        
        // Get patients with pagination
        $sql = "SELECT * FROM patients" . $whereClause . " ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        
        // Bind search parameters if they exist
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $patients = $stmt->fetchAll();
        $totalPages = ceil($totalRecords / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $patients,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'per_page' => $limit
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Failed to fetch patients: " . $e->getMessage());
    }
}

function handleStats() {
    global $pdo;
    
    try {
        // Total patients
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM patients");
        $total = $stmt->fetch()['total'];
        
        // Gender statistics - choose column if available
        $genderStats = [];
        if (patientHasColumnSqlite('gender')) {
            $stmt = $pdo->query("SELECT gender as g, COUNT(*) as count FROM patients WHERE gender IS NOT NULL GROUP BY gender");
            $genderStats = array_map(function($r){ return ['gender' => $r['g'], 'count' => $r['count']]; }, $stmt->fetchAll());
        } elseif (patientHasColumnSqlite('sex')) {
            $stmt = $pdo->query("SELECT sex as g, COUNT(*) as count FROM patients WHERE sex IS NOT NULL GROUP BY sex");
            $genderStats = array_map(function($r){ return ['gender' => $r['g'], 'count' => $r['count']]; }, $stmt->fetchAll());
        }
        
        $maleCount = 0;
        $femaleCount = 0;
        
        foreach ($genderStats as $stat) {
            if (strtolower($stat['gender']) === 'male') {
                $maleCount = $stat['count'];
            } elseif (strtolower($stat['gender']) === 'female') {
                $femaleCount = $stat['count'];
            }
        }
        
        // Recent patients (last 7 days)
        $stmt = $pdo->query("SELECT COUNT(*) as recent FROM patients WHERE created_at >= datetime('now', '-7 days')");
        $recent = $stmt->fetch()['recent'];
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total_patients' => $total,
                'male_patients' => $maleCount,
                'female_patients' => $femaleCount,
                'recent_patients' => $recent
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Failed to fetch statistics: " . $e->getMessage());
    }
}

function handleCreate() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $required = ['name', 'mobile'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }
    
    try {
        // Generate UHID if not provided
        if (empty($input['uhid'])) {
            $stmt = $pdo->query("SELECT MAX(CAST(SUBSTR(uhid, 2) AS INTEGER)) as max_num FROM patients WHERE uhid LIKE 'P%'");
            $result = $stmt->fetch();
            $nextNum = ($result['max_num'] ?? 0) + 1;
            $input['uhid'] = 'P' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        }
        
        // Build insert depending on available columns
        $cols = ['uhid','name','father_husband','mobile','email','age','age_unit','address','added_by'];
        $placeholders = [':uhid',':name',':father_husband',':mobile',':email',':age',':age_unit',':address',':added_by'];
        if (patientHasColumnSqlite('gender')) {
            $cols[] = 'gender';
            $placeholders[] = ':gender';
        } elseif (patientHasColumnSqlite('sex')) {
            $cols[] = 'sex';
            $placeholders[] = ':gender';
        }
        $sql = "INSERT INTO patients (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uhid' => $input['uhid'],
            ':name' => $input['name'],
            ':father_husband' => $input['father_husband'] ?? '',
            ':mobile' => $input['mobile'],
            ':email' => $input['email'] ?? '',
            ':age' => $input['age'] ?? null,
            ':age_unit' => $input['age_unit'] ?? 'Years',
            ':gender' => $input['gender'] ?? '',
            ':address' => $input['address'] ?? '',
            ':added_by' => 'System'
        ]);
        
        $id = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Patient created successfully',
            'data' => ['id' => $id]
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Failed to create patient: " . $e->getMessage());
    }
}

function handleRead() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        throw new Exception('Patient ID is required');
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $patient = $stmt->fetch();
        
        if (!$patient) {
            throw new Exception('Patient not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $patient
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Failed to fetch patient: " . $e->getMessage());
    }
}

function handleUpdate() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Patient ID is required');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    try {
        // Build update depending on available columns
        $updateParts = [
            'name = :name', 'father_husband = :father_husband', 'mobile = :mobile', 'email = :email',
            'age = :age', 'age_unit = :age_unit', 'address = :address'
        ];
        if (patientHasColumnSqlite('gender')) {
            $updateParts[] = 'gender = :gender';
        } elseif (patientHasColumnSqlite('sex')) {
            $updateParts[] = 'sex = :gender';
        }
        $sql = "UPDATE patients SET " . implode(', ', $updateParts) . ", updated_at = datetime('now') WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':name' => $input['name'],
            ':father_husband' => $input['father_husband'] ?? '',
            ':mobile' => $input['mobile'],
            ':email' => $input['email'] ?? '',
            ':age' => $input['age'] ?? null,
            ':age_unit' => $input['age_unit'] ?? 'Years',
            ':gender' => $input['gender'] ?? '',
            ':address' => $input['address'] ?? ''
        ]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Patient not found or no changes made');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Patient updated successfully'
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Failed to update patient: " . $e->getMessage());
    }
}

function handleDelete() {
    global $pdo;
    
    $id = intval($_GET['id'] ?? $_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception('Patient ID is required');
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Patient not found');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Patient deleted successfully'
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Failed to delete patient: " . $e->getMessage());
    }
}
?>
