<?php
// ajax/patient_api.php - Patient API
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
        if (session_status() === PHP_SESSION_NONE) { 
        session_start();
    }

    require_once '../inc/connection.php';
    
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
        case 'get':
            handleRead();
            break;
        case 'update':
            handleUpdate();
            break;
        case 'save':
            handleSave();
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

// Helper to check if a column exists in patients table for MySQL
function patientHasColumn($col) {
    static $cols = null;
    global $pdo;
    if ($cols === null) {
        $stmt = $pdo->query("DESCRIBE patients");
        $info = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cols = array_map(function($r){ return $r['Field']; }, $info);
    }
    return in_array($col, $cols);
}

function normalizeGenderValue($value) {
    if ($value === null) {
        return '';
    }
    $normalized = strtolower(trim((string)$value));
    return match ($normalized) {
        'm', 'male' => 'Male',
        'f', 'female' => 'Female',
        'o', 'other' => 'Other',
        default => (string)$value
    };
}

function assignGenderParam(&$params, $value) {
    $params[':gender'] = normalizeGenderValue($value);
}

function handleList() {
    global $pdo;

    $isDataTables = isset($_POST['draw']);
    $draw   = intval($_POST['draw'] ?? 0);
    $start  = intval($_POST['start'] ?? 0);
    $length = intval($_POST['length'] ?? 25);

    if ($length <= 0) {
        $length = 25;
    }

    $search = '';
    if (isset($_POST['search']['value'])) {
        $search = trim($_POST['search']['value']);
    } else {
        $search = trim($_GET['search'] ?? '');
    }

    $addedBy = $_POST['added_by'] ?? $_GET['added_by'] ?? '';

    try {
        $whereClauses = [];
        $params = [];

        if ($search !== '') {
            $whereClauses[] = '(p.name LIKE :search OR p.uhid LIKE :search OR p.mobile LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($addedBy !== '') {
            $whereClauses[] = 'p.added_by = :added_by';
            $params[':added_by'] = $addedBy;
        }

        $whereSql = $whereClauses ? (' WHERE ' . implode(' AND ', $whereClauses)) : '';

        $totalSql = 'SELECT COUNT(*) FROM patients';
        $totalRecords = (int)$pdo->query($totalSql)->fetchColumn();

        $filteredSql = 'SELECT COUNT(*) FROM patients p' . $whereSql;
        $filteredStmt = $pdo->prepare($filteredSql);
        foreach ($params as $key => $value) {
            $filteredStmt->bindValue($key, $value);
        }
        $filteredStmt->execute();
        $filteredRecords = (int)$filteredStmt->fetchColumn();

        $dataSql = 'SELECT p.*, u.username AS added_by_name
                    FROM patients p
                    LEFT JOIN users u ON p.added_by = u.id'
                    . $whereSql .
                    ' ORDER BY p.id DESC
                      LIMIT :limit OFFSET :offset';

        $stmt = $pdo->prepare($dataSql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $length, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $start, PDO::PARAM_INT);
        $stmt->execute();
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($patients as &$patient) {
            // Map sex column to gender for frontend compatibility
            $rawGender = $patient['sex'] ?? ($patient['gender'] ?? null);
            $patient['gender'] = normalizeGenderValue($rawGender);
            // Ensure added_by_name is available for display
            if (!isset($patient['added_by_name']) && isset($patient['added_by_username'])) {
                $patient['added_by_name'] = $patient['added_by_username'];
            }
        }
        unset($patient);

        if ($isDataTables) {
            echo json_encode([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $patients,
                'success' => true
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'data' => $patients,
                'total' => $filteredRecords
            ]);
        }

    } catch (Exception $e) {
        throw new Exception('Failed to fetch patients: ' . $e->getMessage());
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
        if (patientHasColumn('gender')) {
            $stmt = $pdo->query("SELECT gender as g, COUNT(*) as count FROM patients WHERE gender IS NOT NULL GROUP BY gender");
            $genderStats = array_map(function($r){ return ['gender' => $r['g'], 'count' => $r['count']]; }, $stmt->fetchAll());
        } elseif (patientHasColumn('sex')) {
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
        $stmt = $pdo->query("SELECT COUNT(*) as recent FROM patients WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
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
            $stmt = $pdo->query("SELECT MAX(CAST(SUBSTR(uhid, 2) AS UNSIGNED)) as max_num FROM patients WHERE uhid LIKE 'P%'");
            $result = $stmt->fetch();
            $nextNum = ($result['max_num'] ?? 0) + 1;
            $input['uhid'] = 'P' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        }
        
        // Build insert depending on available columns
        $cols = ['uhid','name','father_husband','mobile','email','age','age_unit','address','added_by'];
        $placeholders = [':uhid',':name',':father_husband',':mobile',':email',':age',':age_unit',':address',':added_by'];
        if (patientHasColumn('gender')) {
            $cols[] = 'gender';
            $placeholders[] = ':gender';
        } elseif (patientHasColumn('sex')) {
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


    echo "Hello"; die;
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        throw new Exception('Patient ID is required');
    }
    
    try {
        $stmt = $pdo->prepare("SELECT p.*, u.username AS added_by_name, u.username AS added_by_username
                                FROM patients p
                                LEFT JOIN users u ON p.added_by = u.id
                                WHERE p.id = :id");
        $stmt->execute([':id' => $id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            throw new Exception('Patient not found');
        }
        
        // Map sex column to gender for frontend compatibility
        $rawGender = $patient['sex'] ?? ($patient['gender'] ?? null);
        $patient['gender'] = normalizeGenderValue($rawGender);
        
        echo json_encode([
            'success' => true,
            'data' => $patient
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Failed to fetch patient: " . $e->getMessage());
    }
}

function handleSave() {
    global $pdo;

    $id = intval($_POST['id'] ?? 0);
    $data = [];
    foreach ($_POST as $key => $value) {
        $data[$key] = is_string($value) ? trim($value) : $value;
    }

    if (empty($data['name'])) {
        throw new Exception("Patient name is required");
    }

    if (empty($data['mobile'])) {
        throw new Exception("Mobile number is required");
    }

    $addedBy = $data['added_by'] ?? ($_SESSION['user_id'] ?? null);

    try {
        // Always use 'sex' column as that's what exists in the database
        $genderColumn = 'sex';
        $normalizedGender = normalizeGenderValue($data['gender'] ?? '');
        $fatherName   = $data['father_husband'] ?? '';
        $email        = $data['email'] ?? '';
        $ageValue     = ($data['age'] ?? '') === '' ? null : intval($data['age']);
        $ageUnit      = $data['age_unit'] ?? 'Years';
        $addressValue = $data['address'] ?? '';
        $contactValue = $data['contact'] ?? '';

        if ($id > 0) {
            // Update existing patient
            $updateParts = [
                'name = :name',
                'father_husband = :father_husband',
                'mobile = :mobile',
                'email = :email',
                'age = :age',
                'age_unit = :age_unit',
                'address = :address',
                'contact = :contact',
                'sex = :gender'
            ];

            if ($addedBy !== null && $addedBy !== '') {
                $updateParts[] = 'added_by = :added_by';
            }

            $sql = 'UPDATE patients SET ' . implode(', ', $updateParts) . ', updated_at = NOW() WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $params = [
                ':id' => $id,
                ':name' => $data['name'],
                ':father_husband' => $fatherName,
                ':mobile' => $data['mobile'],
                ':email' => $email,
                ':age' => $ageValue,
                ':age_unit' => $ageUnit,
                ':address' => $addressValue,
                ':contact' => $contactValue,
                ':gender' => $normalizedGender
            ];

            if ($addedBy !== null && $addedBy !== '') {
                $params[':added_by'] = $addedBy;
            }

            $stmt->execute($params);

            // Return updated patient data
            $stmt = $pdo->prepare("SELECT p.*, u.username AS added_by_name FROM patients p LEFT JOIN users u ON p.added_by = u.id WHERE p.id = :id");
            $stmt->execute([':id' => $id]);
            $updatedPatient = $stmt->fetch(PDO::FETCH_ASSOC);
            $updatedPatient['gender'] = normalizeGenderValue($updatedPatient['sex']);

            echo json_encode([
                'success' => true,
                'message' => 'Patient updated successfully',
                'data' => $updatedPatient
            ]);
            return;
        }

        // Create new patient
        if (empty($data['uhid'])) {
            $stmt = $pdo->query("SELECT MAX(CAST(SUBSTR(uhid, 2) AS UNSIGNED)) FROM patients WHERE uhid LIKE 'P%'");
            $next = (int)$stmt->fetchColumn();
            $data['uhid'] = 'P' . str_pad($next + 1, 6, '0', STR_PAD_LEFT);
        }

        $columns = ['uhid','name','father_husband','mobile','email','age','age_unit','address','contact','sex'];
        $placeholders = [':uhid',':name',':father_husband',':mobile',':email',':age',':age_unit',':address',':contact',':gender'];

        if ($addedBy !== null && $addedBy !== '') {
            $columns[] = 'added_by';
            $placeholders[] = ':added_by';
        }

        $sql = 'INSERT INTO patients (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        $stmt = $pdo->prepare($sql);
        $params = [
            ':uhid' => $data['uhid'],
            ':name' => $data['name'],
            ':father_husband' => $fatherName,
            ':mobile' => $data['mobile'],
            ':email' => $email,
            ':age' => $ageValue,
            ':age_unit' => $ageUnit,
            ':address' => $addressValue,
            ':contact' => $contactValue,
            ':gender' => $normalizedGender
        ];

        if ($addedBy !== null && $addedBy !== '') {
            $params[':added_by'] = $addedBy;
        }

        $stmt->execute($params);
        $newId = $pdo->lastInsertId();

        // Return created patient data
        $stmt = $pdo->prepare("SELECT p.*, u.username AS added_by_name FROM patients p LEFT JOIN users u ON p.added_by = u.id WHERE p.id = :id");
        $stmt->execute([':id' => $newId]);
        $newPatient = $stmt->fetch(PDO::FETCH_ASSOC);
        $newPatient['gender'] = normalizeGenderValue($newPatient['sex']);

        echo json_encode([
            'success' => true,
            'message' => 'Patient created successfully',
            'data' => $newPatient,
            'id' => $newId
        ]);

    } catch (Exception $e) {
        throw new Exception('Failed to save patient: ' . $e->getMessage());
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
        $normalizedGender = normalizeGenderValue($data['gender'] ?? '');
        if (patientHasColumn('gender')) {
            $updateParts[] = 'gender = :gender';
        } elseif (patientHasColumn('sex')) {
            $updateParts[] = 'sex = :gender';
        }
        $sql = "UPDATE patients SET " . implode(', ', $updateParts) . ", updated_at = NOW() WHERE id = :id";
        
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
        // First check if patient exists
        $checkStmt = $pdo->prepare("SELECT id FROM patients WHERE id = :id");
        $checkStmt->execute([':id' => $id]);
        if (!$checkStmt->fetch()) {
            throw new Exception('Patient not found');
        }

        // Check for associated test entries
        $entriesStmt = $pdo->prepare('SELECT COUNT(*) FROM entries WHERE patient_id = :id');
        $entriesStmt->execute([':id' => $id]);
        if ($entriesStmt->fetchColumn() > 0) {
            throw new Exception('Cannot delete patient with associated test entries');
        }

        // Perform the deletion
        $deleteStmt = $pdo->prepare("DELETE FROM patients WHERE id = :id");
        $result = $deleteStmt->execute([':id' => $id]);
        
        if ($result && $deleteStmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Patient deleted successfully'
            ]);
        } else {
            throw new Exception('Failed to delete patient - no rows affected');
        }
        
    } catch (Exception $e) {
        throw new Exception("Failed to delete patient: " . $e->getMessage());
    }
}
?>