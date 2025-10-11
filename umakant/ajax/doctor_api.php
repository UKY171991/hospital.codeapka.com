<?php
// ajax/doctor_api.php - simple CRUD for doctors table (AJAX JSON)
try {
    require_once __DIR__ . '/../inc/connection.php';
} catch (Exception $e) {
    // If database connection fails, provide fallback response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error. Please ensure MySQL is running.',
        'error' => $e->getMessage()
    ]);
    exit;
}

require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

function resolveUserIdentifierValues($value, $pdo) {
    $identifiers = [];

    if ($value === null || $value === '') {
        return $identifiers;
    }

    if (is_numeric($value)) {
        $userId = (int)$value;
        $identifiers[] = $userId;
        try {
            $stmt = $pdo->prepare('SELECT username, full_name FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($userRow) {
                if (!empty($userRow['username'])) {
                    $identifiers[] = $userRow['username'];
                }
                if (!empty($userRow['full_name'])) {
                    $identifiers[] = $userRow['full_name'];
                }
            }
        } catch (Throwable $e) {
            // ignore lookup issues
        }
    } else {
        $provided = trim((string)$value);
        if ($provided !== '') {
            $identifiers[] = $provided;
            try {
                $stmt = $pdo->prepare('SELECT id, username, full_name FROM users WHERE username = ? OR full_name = ?');
                $stmt->execute([$provided, $provided]);
                $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($userRow) {
                    if (!empty($userRow['id'])) {
                        $identifiers[] = (int)$userRow['id'];
                    }
                    if (!empty($userRow['username'])) {
                        $identifiers[] = $userRow['username'];
                    }
                    if (!empty($userRow['full_name'])) {
                        $identifiers[] = $userRow['full_name'];
                    }
                }
            } catch (Throwable $e) {
                // ignore
            }
        }
    }

    $identifiers = array_filter($identifiers, function($item) {
        return $item !== null && $item !== '';
    });

    return array_values(array_unique($identifiers, SORT_REGULAR));
}

try {
    $action = $_REQUEST['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'save' : 'list');

    // If action is 'update', change it to 'save' to reuse existing logic
    if ($action === 'update') {
        $action = 'save';
    }

    if ($action === 'list' || $action === 'simple_list') {
        if ($action === 'simple_list') {
            $stmt = $pdo->query('SELECT id, name FROM doctors ORDER BY name ASC');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $rows]);
        }

        // Support DataTables server-side processing
        $draw = $_POST['draw'] ?? 1;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 25;
        $search = $_POST['search']['value'] ?? '';
        
        // Base query
        $baseQuery = "FROM doctors d LEFT JOIN users u ON d.added_by = u.id";
        $whereClause = "";
        $params = [];
        
        // Add search conditions
        if (!empty($search)) {
            $whereClause = " WHERE (d.name LIKE ? OR d.specialization LIKE ? OR d.hospital LIKE ? OR d.phone LIKE ? OR d.email LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        // Optional filter by added_by (from dropdown)
        if (isset($_REQUEST['added_by']) && $_REQUEST['added_by'] !== '') {
            $identifierValues = resolveUserIdentifierValues($_REQUEST['added_by'], $pdo);
            if (!empty($identifierValues)) {
                $placeholders = implode(',', array_fill(0, count($identifierValues), '?'));
                if ($whereClause === "") {
                    $whereClause = " WHERE d.added_by IN ($placeholders)";
                    $params = $identifierValues;
                } else {
                    $whereClause .= " AND d.added_by IN ($placeholders)";
                    $params = array_merge($params, $identifierValues);
                }
            } else {
                $whereClause = " WHERE 1 = 0";
                $params = [];
            }
        }

        // Optional filter by owner_id (accept from request). If doctors table has
        // an owner_id column, use it. Otherwise, allow 'added_by' identifier filtering
        // (e.g., user id, username) via the existing resolveUserIdentifierValues helper.
        if (isset($_REQUEST['owner_id']) && $_REQUEST['owner_id'] !== '') {
            $ownerIdParam = $_REQUEST['owner_id'];
            // Check schema for owner_id column
            $colsStmt = $pdo->query('SHOW COLUMNS FROM doctors');
            $docCols = $colsStmt->fetchAll(PDO::FETCH_COLUMN);
            if (in_array('owner_id', $docCols)) {
                $place = (strpos($whereClause, 'WHERE') === false) ? ' WHERE ' : ' AND ';
                $whereClause .= $place . 'd.owner_id = ?';
                $params[] = (int)$ownerIdParam;
            } else {
                $identifierValues = resolveUserIdentifierValues($ownerIdParam, $pdo);
                if (!empty($identifierValues)) {
                    $place = (strpos($whereClause, 'WHERE') === false) ? ' WHERE ' : ' AND ';
                    $placeholders = implode(',', array_fill(0, count($identifierValues), '?'));
                    $whereClause .= $place . "d.added_by IN ($placeholders)";
                    $params = array_merge($params, $identifierValues);
                } else {
                    $whereClause = (strpos($whereClause, 'WHERE') === false) ? ' WHERE 1 = 0' : $whereClause . ' AND 1 = 0';
                }
            }
        }
        
    // Get total records (no filters)
    $totalStmt = $pdo->query("SELECT COUNT(*) " . $baseQuery);
    $totalRecords = $totalStmt->fetchColumn();

    // Get filtered records (with current search filters and added_by)
    $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
    $filteredStmt->execute($params);
    $filteredRecords = $filteredStmt->fetchColumn();
        $orderBy = " ORDER BY d.id DESC";
        $limit = " LIMIT $start, $length";
        
    $dataQuery = "SELECT d.id,
                 d.name,
                 d.hospital,
                 d.contact_no,
                 d.percent,
                 d.added_by,
                 u.username as added_by_username,
                 d.created_at
              " . $baseQuery . $whereClause . $orderBy . $limit;

    $dataStmt = $pdo->prepare($dataQuery);
    $dataStmt->execute($params);
    $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Return DataTables format
        json_response([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'success' => true,
            'data' => $data
        ]);
    }
    
    if ($action === 'stats') {
        // Get doctor statistics
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM doctors");
        $total = $totalStmt->fetchColumn();
        
        $activeStmt = $pdo->query("SELECT COUNT(*) FROM doctors WHERE phone IS NOT NULL AND phone != ''");
        $active = $activeStmt->fetchColumn();
        
        $specializationsStmt = $pdo->query("SELECT COUNT(DISTINCT specialization) FROM doctors WHERE specialization IS NOT NULL AND specialization != ''");
        $specializations = $specializationsStmt->fetchColumn();
        
        $hospitalsStmt = $pdo->query("SELECT COUNT(DISTINCT hospital) FROM doctors WHERE hospital IS NOT NULL AND hospital != ''");
        $hospitals = $hospitalsStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'specializations' => $specializations,
                'hospitals' => $hospitals
            ]
        ]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT d.id,d.server_id,d.name,d.qualification,d.specialization,d.hospital,d.contact_no,d.phone,d.percent,d.email,d.address,d.registration_no,d.added_by,d.created_at,d.updated_at,u.username as added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // allow master and admin to save
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = $_POST['id'] ?? '';
        $server_id = isset($_POST['server_id']) && is_numeric($_POST['server_id']) ? (int)$_POST['server_id'] : null;
        $name = trim($_POST['name'] ?? '');
        $qualification = trim($_POST['qualification'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $hospital = trim($_POST['hospital'] ?? '');
        $contact_no = trim($_POST['contact_no'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $registration_no = trim($_POST['registration_no'] ?? '');
        $percent = isset($_POST['percent']) ? (float)$_POST['percent'] : 0.00;
        $added_by = isset($_POST['added_by']) ? (int)$_POST['added_by'] : (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

        if ($id) {
            try {
                $stmt = $pdo->prepare('UPDATE doctors SET name=?, hospital=?, contact_no=?, percent=?, address=?, added_by=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$name, $hospital, $contact_no, $percent, $address, $added_by, $id]);
                json_response(['success' => true, 'message' => 'Doctor updated']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
            }
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO doctors (name, hospital, contact_no, percent, address, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([$name, $hospital, $contact_no, $percent, $address, $added_by]);
                json_response(['success' => true, 'message' => 'Doctor added']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
            }
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        // allow master and admin to delete
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        try {
            $stmt = $pdo->prepare('DELETE FROM doctors WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            json_response(['success' => true, 'message' => 'Doctor deleted']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'stats') {
        // Get doctor statistics
        $totalStmt = $pdo->query('SELECT COUNT(*) FROM doctors');
        $total = $totalStmt->fetchColumn();
        
        $activeStmt = $pdo->query('SELECT COUNT(*) FROM doctors WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
        $active = $activeStmt->fetchColumn();
        
        $specializationStmt = $pdo->query('SELECT COUNT(DISTINCT specialization) FROM doctors WHERE specialization IS NOT NULL AND specialization != ""');
        $specializations = $specializationStmt->fetchColumn();
        
        $hospitalStmt = $pdo->query('SELECT COUNT(DISTINCT hospital) FROM doctors WHERE hospital IS NOT NULL AND hospital != ""');
        $hospitals = $hospitalStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'specializations' => $specializations,
                'hospitals' => $hospitals
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    // ensure any uncaught error returns JSON so client sees it
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
