<?php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/smart_upsert.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging but send to log, not output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cached helper to check if a column exists in patients table.
 */
function patientHasColumn($col) {
    static $cols = null;
    global $pdo;
    if ($cols === null) {
        $stmt = $pdo->query("SHOW COLUMNS FROM patients");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    return in_array($col, $cols);
}

try {
    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    // If no explicit action is provided but this request looks like a DataTables server-side
    // request (it includes draw/start/length), treat it as a 'list' action to be forgiving
    // and avoid returning a 400 error for DataTables requests that don't set action.
    if (empty($action) && (isset($_POST['draw']) || isset($_GET['draw']))) {
        $action = 'list';
    }

    if (empty($action)) {
        throw new Exception('No action specified');
    }

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
        case 'bulk_export':
            // legacy client may call bulk_export; reuse same export handler
            handleExport();
            break;
        case 'stats':
            handleStats();
            break;
        case 'export':
            handleExport();
            break;
        default:
            throw new Exception('Invalid action: ' . $action);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'action' => $action ?? 'not set',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
            'post_data' => !empty($_POST) ? array_keys($_POST) : 'empty',
            'get_data' => !empty($_GET) ? array_keys($_GET) : 'empty'
        ]
    ]);
}

function handleList() {
    global $pdo;
    
    try {
        // Check if the database connection is working
        if (!$pdo) {
            throw new Exception('Database connection not available');
        }

        // Detect DataTables server-side params (draw, start, length)
        $isDataTables = isset($_POST['draw']) || isset($_GET['draw']);

        if ($isDataTables) {
            $draw = (int)($_POST['draw'] ?? $_GET['draw'] ?? 0);
            $start = (int)($_POST['start'] ?? $_GET['start'] ?? 0);
            $length = (int)($_POST['length'] ?? $_GET['length'] ?? 10);
            $limit = max(1, min(100, $length));
            $offset = max(0, $start);
            // For DataTables we will calculate current page for compatibility
            $page = (int)floor($offset / $limit) + 1;
        } else {
            $page = max(1, (int)($_POST['page'] ?? 1));
            $limit = max(1, min(100, (int)($_POST['limit'] ?? 10)));
            $offset = ($page - 1) * $limit;
        }
        
        // First, let's get a simple count of all patients
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM patients");
        $totalRecords = $totalStmt->fetchColumn() ?: 0;
        
        // If no patients exist, return empty result
        if ($totalRecords == 0) {
            echo json_encode([
                'success' => true,
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'total_pages' => 0,
                    'total_records' => 0,
                    'records_per_page' => $limit
                ],
                'message' => 'No patients found in database'
            ]);
            return;
        }
        
        // Check what columns exist in the patients table
        $stmt = $pdo->query("SHOW COLUMNS FROM patients");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Build basic WHERE clause for filtering
        $whereConditions = [];
        $params = [];
        
        // Extract search value (support DataTables: search[value]) from POST or GET
        $searchValue = null;
        if (!empty($_POST['search']) && is_array($_POST['search']) && isset($_POST['search']['value'])) {
            $searchValue = trim($_POST['search']['value']);
        } elseif (!empty($_GET['search']) && is_array($_GET['search']) && isset($_GET['search']['value'])) {
            $searchValue = trim($_GET['search']['value']);
        } elseif (isset($_POST['search']) && !is_array($_POST['search'])) {
            $searchValue = trim($_POST['search']);
        } elseif (isset($_GET['search']) && !is_array($_GET['search'])) {
            $searchValue = trim($_GET['search']);
        }

        // Search filter - only search in columns that exist
        if (!empty($searchValue)) {
            $search = '%' . $searchValue . '%';
            $searchConditions = [];
            
            if (in_array('name', $columns)) {
                $searchConditions[] = "patients.name LIKE ?";
                $params[] = $search;
            }
            if (in_array('mobile', $columns)) {
                $searchConditions[] = "patients.mobile LIKE ?";
                $params[] = $search;
            }
            if (in_array('uhid', $columns)) {
                $searchConditions[] = "patients.uhid LIKE ?";
                $params[] = $search;
            }
            if (in_array('email', $columns)) {
                $searchConditions[] = "patients.email LIKE ?";
                $params[] = $search;
            }
            
            if (!empty($searchConditions)) {
                $whereConditions[] = "(" . implode(" OR ", $searchConditions) . ")";
            }
        }
        
        // Gender filter (accept from POST or GET) - handle tables that use 'sex' instead of 'gender'
        $genderParam = $_POST['gender'] ?? $_GET['gender'] ?? null;
        // determine which column to use for gender data
        $genderColumn = null;
        if (in_array('gender', $columns)) {
            $genderColumn = 'patients.gender';
        } elseif (in_array('sex', $columns)) {
            $genderColumn = 'patients.sex';
        }

        if ($genderParam && $genderColumn) {
            // Use case-insensitive compare to be robust against stored variants
            $whereConditions[] = "LOWER(TRIM($genderColumn)) = LOWER(TRIM(?))";
            $params[] = $genderParam;
        }

        // Optional filter by added_by (accept from POST or GET). Only apply if column exists.
        $addedByParam = $_POST['added_by'] ?? $_GET['added_by'] ?? null;
        if ($addedByParam && in_array('added_by', $columns)) {
            $whereConditions[] = 'patients.added_by = ?';
            $params[] = intval($addedByParam);
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Count filtered records
        $countSql = "SELECT COUNT(*) FROM patients $whereClause";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $filteredRecords = $countStmt->fetchColumn() ?: 0;
        
        // Build SELECT fields based on available columns
        $selectFields = ['patients.id'];
        $joinUsers = '';

        // Add columns that commonly exist
        $commonFields = ['name', 'uhid', 'mobile', 'email', 'age', 'age_unit', 'father_husband', 'address', 'created_at', 'added_by'];
        foreach ($commonFields as $field) {
            if (in_array($field, $columns)) {
                $selectFields[] = "patients.$field";
            }
        }

        // If patients table has added_by, try to join users table to resolve a human-friendly name.
        if (in_array('added_by', $columns)) {
            // Add an aliased field for front-end display. We prefer user's full_name, then username, then raw added_by value.
            $selectFields[] = "COALESCE(u.full_name, u.username, patients.added_by) as added_by_name";
            // Left join users on either id match or username match to support mixed storage.
            $joinUsers = " LEFT JOIN users u ON (patients.added_by = u.id OR patients.added_by = u.username) ";
        }
        
        // Handle gender column: prefer 'gender' column if present, otherwise use 'sex'.
        // Normalize common variants into 'Male'/'Female'/'Other' for front-end.
        if ($genderColumn) {
            $selectFields[] = "(
                CASE
                    WHEN LOWER(TRIM($genderColumn)) IN ('male','m','1','true','yes') THEN 'Male'
                    WHEN LOWER(TRIM($genderColumn)) IN ('female','f','0','false','no') THEN 'Female'
                    WHEN LOWER(TRIM($genderColumn)) IN ('other','o','non-binary','nonbinary','nb') THEN 'Other'
                    ELSE NULL
                END
            ) as gender";
        }
        
        // Get patients with pagination
    $orderBy = in_array('created_at', $columns) ? "patients.created_at DESC" : "patients.id DESC";
    $sql = "SELECT " . implode(', ', $selectFields) . " 
        FROM patients" . $joinUsers . " 
        $whereClause 
        ORDER BY $orderBy
        LIMIT $limit OFFSET $offset";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate pagination info
    $totalPages = ceil($filteredRecords / $limit);
        
        // If request came from DataTables, return DataTables-compatible response
        if (!empty($isDataTables)) {
            // Return DataTables-compatible response and include success flag for enhanced clients
            echo json_encode([
                'success' => true,
                'draw' => $draw,
                'recordsTotal' => (int)$totalRecords,
                'recordsFiltered' => (int)$filteredRecords,
                'data' => $patients
            ]);
            return;
        }

        // Default response for non-DataTables clients
        echo json_encode([
            'success' => true,
            'data' => $patients,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => (int)$filteredRecords,
                'records_per_page' => $limit
            ],
            'debug' => [
                'total_in_db' => (int)$totalRecords,
                'filtered_count' => (int)$filteredRecords,
                'columns_available' => $columns,
                'sql_executed' => $sql,
                'where_conditions' => $whereConditions,
                'params_used' => $params
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch patients: ' . $e->getMessage(),
            'error_details' => [
                'file' => __FILE__,
                'line' => __LINE__,
                'trace' => $e->getTraceAsString()
            ]
        ]);
    }
}

function handleGet() {
    global $pdo;
    
    if (!isset($_POST['id']) && !isset($_GET['id'])) {
        throw new Exception('Patient ID is required');
    }
    
    $id = $_POST['id'] ?? $_GET['id'];
    
    try {
        // Build SELECT dynamically to avoid referencing missing columns
        $select = '*';
        if (patientHasColumn('gender') && patientHasColumn('sex')) {
            $select = "*, COALESCE(gender, sex) as gender";
        } elseif (patientHasColumn('gender')) {
            $select = "*, gender as gender";
        } elseif (patientHasColumn('sex')) {
            $select = "*, sex as gender";
        }

        $sql = "SELECT $select FROM patients WHERE id = ?";
        $stmt = $pdo->prepare($sql);
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

        error_log("DEBUG: handleSave - POST added_by: " . ($_POST['added_by'] ?? 'not set'));
        $addedByValue = !empty($_POST['added_by']) && is_numeric($_POST['added_by']) ? (int)$_POST['added_by'] : (isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null);
        error_log("DEBUG: handleSave - Final added_by value: " . $addedByValue);

        $data = [
            'name' => trim($_POST['name']),
            'uhid' => !empty($_POST['uhid']) ? trim($_POST['uhid']) : null,
            'mobile' => trim($_POST['mobile']),
            'email' => !empty($_POST['email']) ? trim($_POST['email']) : null,
            'age' => !empty($_POST['age']) ? (int)$_POST['age'] : null,
            'age_unit' => $_POST['age_unit'] ?? 'Years',
            'father_husband' => !empty($_POST['father_husband']) ? trim($_POST['father_husband']) : null,
            'address' => !empty($_POST['address']) ? trim($_POST['address']) : null,
            // Use numeric user id for foreign-key column if available; otherwise NULL to avoid FK violations
            'added_by' => $addedByValue
        ];

        // Attach gender/sex fields only if columns exist in the DB
        if (patientHasColumn('gender')) {
            $data['gender'] = $gender;
        }
        if (patientHasColumn('sex')) {
            $data['sex'] = $gender;
        }
        
        if ($isEdit) {
            // Build dynamic update list and params
            $updateFields = [
                'name', 'uhid', 'mobile', 'email', 'age', 'age_unit', 'father_husband', 'address'
            ];
            $setParts = [];
            $params = [];
            foreach ($updateFields as $f) {
                $setParts[] = "$f = ?";
                $params[] = $data[$f];
            }
            if (isset($data['gender'])) {
                $setParts[] = "gender = ?";
                $params[] = $data['gender'];
            }
            if (isset($data['sex'])) {
                $setParts[] = "sex = ?";
                $params[] = $data['sex'];
            }
            $setParts[] = "updated_at = NOW()";

            $sql = "UPDATE patients SET " . implode(', ', $setParts) . " WHERE id = ?";
            $params[] = $patientId;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $message = 'Patient updated successfully';

        } else {
            // Insert new patient - build column list dynamically
            $insertCols = ['name', 'uhid', 'mobile', 'email', 'age', 'age_unit', 'father_husband', 'address', 'added_by'];
            $placeholders = array_fill(0, count($insertCols), '?');
            $insertParams = [];
            foreach ($insertCols as $c) {
                $insertParams[] = $data[$c];
            }
            if (isset($data['gender'])) {
                $insertCols[] = 'gender';
                $placeholders[] = '?';
                $insertParams[] = $data['gender'];
            }
            if (isset($data['sex'])) {
                $insertCols[] = 'sex';
                $placeholders[] = '?';
                $insertParams[] = $data['sex'];
            }

            $sql = "INSERT INTO patients (" . implode(', ', $insertCols) . ", created_at) VALUES (" . implode(', ', $placeholders) . ", NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($insertParams);

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
        // Check if the database connection is working
        if (!$pdo) {
            throw new Exception('Database connection not available');
        }

        // First, check what columns exist in the patients table
        $stmt = $pdo->query("SHOW COLUMNS FROM patients");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $hasGender = in_array('gender', $columns);
        $hasSex = in_array('sex', $columns);
        $hasCreatedAt = in_array('created_at', $columns);
        
        // Total patients
        $stmt = $pdo->query("SELECT COUNT(*) FROM patients");
        $total = $stmt->fetchColumn() ?: 0;
        
        // Today's patients - handle if created_at column doesn't exist
        $today = 0;
        if ($hasCreatedAt) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE DATE(created_at) = CURDATE()");
            $today = $stmt->fetchColumn() ?: 0;
        }
        
        // Male patients - check available gender columns
        $male = 0;
        if ($hasGender && $hasSex) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE gender = 'Male' OR sex = 'Male'");
            $male = $stmt->fetchColumn() ?: 0;
        } elseif ($hasGender) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE gender = 'Male'");
            $male = $stmt->fetchColumn() ?: 0;
        } elseif ($hasSex) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE sex = 'Male'");
            $male = $stmt->fetchColumn() ?: 0;
        }
        
        // Female patients - check available gender columns
        $female = 0;
        if ($hasGender && $hasSex) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE gender = 'Female' OR sex = 'Female'");
            $female = $stmt->fetchColumn() ?: 0;
        } elseif ($hasGender) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE gender = 'Female'");
            $female = $stmt->fetchColumn() ?: 0;
        } elseif ($hasSex) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM patients WHERE sex = 'Female'");
            $female = $stmt->fetchColumn() ?: 0;
        }
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total' => (int)$total,
                'today' => (int)$today,
                'male' => (int)$male,
                'female' => (int)$female
            ],
            'debug' => [
                'columns_available' => $columns,
                'has_gender' => $hasGender,
                'has_sex' => $hasSex,
                'has_created_at' => $hasCreatedAt
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch statistics: ' . $e->getMessage(),
            'error_details' => [
                'file' => __FILE__,
                'line' => __LINE__,
                'trace' => $e->getTraceAsString()
            ]
        ]);
    }
}

function handleExport() {
    global $pdo;
    
    try {
        $whereConditions = [];
        $params = [];
        
        // Handle specific IDs for bulk export
        // Support ids passed via POST or GET as comma-separated string or as array
        $rawIds = null;
        if (isset($_POST['ids'])) $rawIds = $_POST['ids'];
        elseif (isset($_GET['ids'])) $rawIds = $_GET['ids'];

        if (!empty($rawIds)) {
            if (is_array($rawIds)) {
                $ids = array_filter(array_map('intval', $rawIds));
            } else {
                $ids = explode(',', $rawIds);
                $ids = array_filter(array_map('intval', $ids));
            }

            if (!empty($ids)) {
                $placeholders = str_repeat('?,', count($ids) - 1) . '?';
                $whereConditions[] = "p.id IN ($placeholders)";
                $params = array_merge($params, $ids);
            }
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
    // Try to resolve added_by to a friendly name using users table if available
    // Determine a safe gender select expression depending on available columns
    $genderSelect = '';
    if (patientHasColumn('gender') && patientHasColumn('sex')) {
        $genderSelect = "COALESCE(p.gender, p.sex) as gender";
    } elseif (patientHasColumn('gender')) {
        $genderSelect = "p.gender as gender";
    } elseif (patientHasColumn('sex')) {
        $genderSelect = "p.sex as gender";
    } else {
        $genderSelect = "NULL as gender";
    }

    $sql = "SELECT p.name, p.uhid, p.mobile, p.email, p.age, p.age_unit, 
               $genderSelect,
               p.father_husband, p.address, p.created_at, COALESCE(u.full_name, u.username, p.added_by) as added_by 
        FROM patients p LEFT JOIN users u ON (p.added_by = u.id OR p.added_by = u.username) 
        $whereClause 
        ORDER BY p.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If this is an AJAX request expecting JSON, return JSON data
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $patients
            ]);
            return;
        }

        // Otherwise send CSV download
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
