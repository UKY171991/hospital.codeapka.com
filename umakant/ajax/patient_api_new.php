<?php
require_once '../inc/connection.php';
require_once '../inc/auth.php';

header('Content-Type: application/json');

// Local helper to check patients table columns
function patientHasColumn($col) {
    static $cols = null;
    global $pdo;
    if ($cols === null) {
        $stmt = $pdo->query("SHOW COLUMNS FROM patients");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    return in_array($col, $cols);
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    if (!isset($_POST['action']) && !isset($_GET['action'])) {
        throw new Exception('No action specified');
    }

    $action = $_POST['action'] ?? $_GET['action'];

    switch ($action) {
        case 'list':
            handleList();
            break;
        case 'get':
            handleGet();
            break;
        case 'save':
            handleSave();
            break;
        case 'delete':
            handleDelete();
            break;
        case 'bulk_delete':
            handleBulkDelete();
            break;
        case 'stats':
            handleStats();
            break;
        case 'export':
            handleExport();
            break;
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function handleList() {
    global $pdo;
    
    try {
        $page = max(1, (int)($_POST['page'] ?? 1));
        $limit = max(1, min(100, (int)($_POST['limit'] ?? 10)));
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $whereConditions = [];
        $params = [];
        
        // Search filter
        if (!empty($_POST['search'])) {
            $search = '%' . $_POST['search'] . '%';
            $whereConditions[] = "(patients.name LIKE ? OR patients.mobile LIKE ? OR patients.uhid LIKE ? OR patients.email LIKE ?)";
            $params = array_merge($params, [$search, $search, $search, $search]);
        }
        
        // Gender filter - check both 'gender' and 'sex' columns
        if (!empty($_POST['gender'])) {
            $whereConditions[] = "(gender = ? OR sex = ?)";
            $params[] = $_POST['gender'];
            $params[] = $_POST['gender'];
        }
        
        // Age range filter
        if (!empty($_POST['age_range'])) {
            $ageRange = $_POST['age_range'];
            switch ($ageRange) {
                case '0-18':
                    $whereConditions[] = "age BETWEEN 0 AND 18";
                    break;
                case '19-35':
                    $whereConditions[] = "age BETWEEN 19 AND 35";
                    break;
                case '36-60':
                    $whereConditions[] = "age BETWEEN 36 AND 60";
                    break;
                case '60+':
                    $whereConditions[] = "age > 60";
                    break;
            }
        }
        
        // Date filter
        if (!empty($_POST['date'])) {
            $whereConditions[] = "DATE(created_at) = ?";
            $params[] = $_POST['date'];
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Count total records
        $countSql = "SELECT COUNT(*) FROM patients $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalRecords = $countStmt->fetchColumn();
        
        // Get patients with pagination - choose gender expression safely
        if (patientHasColumn('gender') && patientHasColumn('sex')) {
            $genderSelect = "COALESCE(gender, sex) as gender";
        } elseif (patientHasColumn('gender')) {
            $genderSelect = "gender as gender";
        } elseif (patientHasColumn('sex')) {
            $genderSelect = "sex as gender";
        } else {
            $genderSelect = "NULL as gender";
        }

    $sql = "SELECT id, name, uhid, mobile, email, age, age_unit, 
                       $genderSelect,
                       father_husband, address, created_at, added_by 
        FROM patients 
        $whereClause 
        ORDER BY patients.created_at DESC 
                LIMIT $limit OFFSET $offset";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate pagination info
        $totalPages = ceil($totalRecords / $limit);
        
        echo json_encode([
            'success' => true,
            'data' => $patients,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'records_per_page' => $limit
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Failed to fetch patients: ' . $e->getMessage());
    }
}

function handleGet() {
    global $pdo;
    
    if (!isset($_POST['id']) && !isset($_GET['id'])) {
        throw new Exception('Patient ID is required');
    }
    
    $id = $_POST['id'] ?? $_GET['id'];
    
    try {
        // Build SELECT safely for single patient
        $select = '*';
        if (patientHasColumn('gender') && patientHasColumn('sex')) {
            $select = "*, COALESCE(gender, sex) as gender";
        } elseif (patientHasColumn('gender')) {
            $select = "*, gender as gender";
        } elseif (patientHasColumn('sex')) {
            $select = "*, sex as gender";
        }

        $stmt = $pdo->prepare("SELECT $select FROM patients WHERE id = ?");
        $stmt->execute([$id]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$patient) {
            throw new Exception('Patient not found');
        }
        
        echo json_encode([
            'success' => true,
            'data' => $patient
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Failed to fetch patient: ' . $e->getMessage());
    }
}

function handleSave() {
    global $pdo;
    
    try {
        // Validate required fields
        if (empty($_POST['name'])) {
            throw new Exception('Patient name is required');
        }
        
        if (empty($_POST['mobile'])) {
            throw new Exception('Mobile number is required');
        }
        
        // Validate mobile number
        if (!preg_match('/^[0-9]{10}$/', $_POST['mobile'])) {
            throw new Exception('Mobile number must be 10 digits');
        }
        
        $patientId = $_POST['id'] ?? null;
        $isEdit = !empty($patientId);
        
        // Check for duplicate mobile (excluding current patient if editing)
        $duplicateCheckSql = "SELECT id FROM patients WHERE mobile = ?";
        $duplicateParams = [$_POST['mobile']];
        
        if ($isEdit) {
            $duplicateCheckSql .= " AND id != ?";
            $duplicateParams[] = $patientId;
        }
        
        $stmt = $pdo->prepare($duplicateCheckSql);
        $stmt->execute($duplicateParams);
        
        if ($stmt->fetch()) {
            throw new Exception('Mobile number already exists');
        }
        
        // Check for duplicate UHID if provided
        if (!empty($_POST['uhid'])) {
            $uhidCheckSql = "SELECT id FROM patients WHERE uhid = ?";
            $uhidParams = [$_POST['uhid']];
            
            if ($isEdit) {
                $uhidCheckSql .= " AND id != ?";
                $uhidParams[] = $patientId;
            }
            
            $stmt = $pdo->prepare($uhidCheckSql);
            $stmt->execute($uhidParams);
            
            if ($stmt->fetch()) {
                throw new Exception('UHID already exists');
            }
        }
        
        $gender = $_POST['gender'] ?? $_POST['sex'] ?? null;
        
        $data = [
            'name' => trim($_POST['name']),
            'uhid' => !empty($_POST['uhid']) ? trim($_POST['uhid']) : null,
            'mobile' => trim($_POST['mobile']),
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'age' => !empty($_POST['age']) ? (int)$_POST['age'] : null,
            'age_unit' => $_POST['age_unit'] ?? 'Years',
            'gender' => $gender,
            'sex' => $gender, // Store in both columns for compatibility
            'father_husband' => !empty($_POST['father_husband']) ? trim($_POST['father_husband']) : null,
            'address' => !empty($_POST['address']) ? trim($_POST['address']) : null,
            // Use numeric session user_id for foreign-key column; NULL if not available
            'added_by' => isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null
        ];
        
        if ($isEdit) {
            // Update existing patient
            $sql = "UPDATE patients SET 
                    name = ?, uhid = ?, mobile = ?, email = ?, 
                    age = ?, age_unit = ?, gender = ?, sex = ?, 
                    father_husband = ?, address = ?, updated_at = NOW() 
                    WHERE id = ?";
            
            $params = [
                $data['name'], $data['uhid'], $data['mobile'], $data['email'],
                $data['age'], $data['age_unit'], $data['gender'], $data['sex'],
                $data['father_husband'], $data['address'], $patientId
            ];
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $message = 'Patient updated successfully';
            
        } else {
            // Insert new patient
            $sql = "INSERT INTO patients (name, uhid, mobile, email, age, age_unit, gender, sex, father_husband, address, added_by, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $params = [
                $data['name'], $data['uhid'], $data['mobile'], $data['email'],
                $data['age'], $data['age_unit'], $data['gender'], $data['sex'],
                $data['father_husband'], $data['address'], $data['added_by']
            ];
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $patientId = $pdo->lastInsertId();
            $message = 'Patient added successfully';
        }
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'patient_id' => $patientId
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Failed to save patient: ' . $e->getMessage());
    }
}

function handleDelete() {
    global $pdo;
    
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('Patient ID is required');
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Patient not found');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Patient deleted successfully'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Failed to delete patient: ' . $e->getMessage());
    }
}

function handleBulkDelete() {
    global $pdo;
    
    if (!isset($_POST['ids']) || empty($_POST['ids'])) {
        throw new Exception('Patient IDs are required');
    }
    
    try {
        $ids = explode(',', $_POST['ids']);
        $ids = array_filter(array_map('intval', $ids));
        
        if (empty($ids)) {
            throw new Exception('Valid patient IDs are required');
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "DELETE FROM patients WHERE id IN ($placeholders)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($ids);
        
        $deletedCount = $stmt->rowCount();
        
        echo json_encode([
            'success' => true,
            'message' => "$deletedCount patients deleted successfully",
            'deleted_count' => $deletedCount
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Failed to delete patients: ' . $e->getMessage());
    }
}

function handleStats() {
    global $pdo;
    
    try {
        // Total patients
        $stmt = $pdo->query("SELECT COUNT(*) FROM patients");
        $total = $stmt->fetchColumn();
        
        // Today's patients
        $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE DATE(created_at) = CURDATE()");
        $today = $stmt->fetchColumn();
        
        // Male patients - check both gender and sex columns
        $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE gender = 'Male' OR sex = 'Male'");
        $male = $stmt->fetchColumn();
        
        // Female patients - check both gender and sex columns
        $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE gender = 'Female' OR sex = 'Female'");
        $female = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total' => $total,
                'today' => $today,
                'male' => $male,
                'female' => $female
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Failed to fetch statistics: ' . $e->getMessage());
    }
}

function handleExport() {
    global $pdo;
    
    try {
        $whereConditions = [];
        $params = [];
        
        // Handle specific IDs for bulk export
        if (!empty($_POST['ids'])) {
            $ids = explode(',', $_POST['ids']);
            $ids = array_filter(array_map('intval', $ids));
            
            if (!empty($ids)) {
                $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                $whereConditions[] = "id IN ($placeholders)";
                $params = array_merge($params, $ids);
            }
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        if (patientHasColumn('gender') && patientHasColumn('sex')) {
            $genderSelect = "COALESCE(gender, sex) as gender";
        } elseif (patientHasColumn('gender')) {
            $genderSelect = "gender as gender";
        } elseif (patientHasColumn('sex')) {
            $genderSelect = "sex as gender";
        } else {
            $genderSelect = "NULL as gender";
        }

        $sql = "SELECT name, uhid, mobile, email, age, age_unit, 
                       $genderSelect,
                       father_husband, address, created_at, added_by 
                FROM patients 
                $whereClause 
                ORDER BY created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="patients_' . date('Y-m-d_H-i-s') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Name', 'UHID', 'Mobile', 'Email', 'Age', 'Age Unit', 
            'Gender', 'Father/Husband', 'Address', 'Registration Date', 'Added By'
        ]);
        
        // CSV data
        foreach ($patients as $patient) {
            fputcsv($output, [
                $patient['name'],
                $patient['uhid'],
                $patient['mobile'],
                $patient['email'],
                $patient['age'],
                $patient['age_unit'],
                $patient['gender'],
                $patient['father_husband'],
                $patient['address'],
                $patient['created_at'],
                $patient['added_by']
            ]);
        }
        
        fclose($output);
        exit;
        
    } catch (Exception $e) {
        // Return to regular JSON response if export fails
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to export: ' . $e->getMessage()
        ]);
    }
}
?>
