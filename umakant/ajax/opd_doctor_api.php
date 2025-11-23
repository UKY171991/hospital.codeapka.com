<?php
// ajax/opd_doctor_api.php - CRUD for OPD doctors
try {
    require_once __DIR__ . '/../inc/connection.php';
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error',
        'error' => $e->getMessage()
    ]);
    exit;
}

require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'save' : 'list');

    if ($action === 'update') {
        $action = 'save';
    }

    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        $baseQuery = "FROM opd_doctors d LEFT JOIN users u ON d.added_by = u.id";
        $whereClause = "";
        $params = [];
        
        if (!empty($search)) {
            $whereClause = " WHERE (d.name LIKE ? OR d.specialization LIKE ? OR d.contact_no LIKE ? OR d.hospital LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        try {
            if (empty($whereClause)) {
                $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
            } else {
                $totalStmt = $pdo->query("SELECT COUNT(*) " . $baseQuery);
            }
            $totalRecords = $totalStmt->fetchColumn();
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error counting records: ' . $e->getMessage()], 500);
        }

        try {
            $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
            $filteredStmt->execute($params);
            $filteredRecords = $filteredStmt->fetchColumn();
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error counting filtered records: ' . $e->getMessage()], 500);
        }

        $orderBy = " ORDER BY d.id DESC";
        if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {
            $orderColumn = (int)$_REQUEST['order'][0]['column'];
            $orderDir = $_REQUEST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';
            
            $columns = ['d.id', 'd.name', 'd.specialization', 'd.hospital', 'd.contact_no', 'd.created_at'];
            if (isset($columns[$orderColumn])) {
                $orderBy = " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;
            }
        }
        
        $limit = " LIMIT $start, $length";
        
        try {
            $dataQuery = "SELECT d.id, d.name, d.specialization, d.qualification, d.hospital, 
                         d.contact_no, d.phone, d.email, d.registration_no, d.address,
                         d.added_by, u.username as added_by_username, d.created_at, d.updated_at
                      " . $baseQuery . $whereClause . $orderBy . $limit;

            $dataStmt = $pdo->prepare($dataQuery);
            $dataStmt->execute($params);
            $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching data: ' . $e->getMessage()], 500);
        }
        
        json_response([
            'draw' => intval($draw),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($filteredRecords),
            'success' => true,
            'data' => $data
        ]);
    }
    
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
        $total = $totalStmt->fetchColumn();
        
        $activeStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors WHERE phone IS NOT NULL AND phone != ''");
        $active = $activeStmt->fetchColumn();
        
        $specializationsStmt = $pdo->query("SELECT COUNT(DISTINCT specialization) FROM opd_doctors WHERE specialization IS NOT NULL AND specialization != ''");
        $specializations = $specializationsStmt->fetchColumn();
        
        $hospitalsStmt = $pdo->query("SELECT COUNT(DISTINCT hospital) FROM opd_doctors WHERE hospital IS NOT NULL AND hospital != ''");
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
        $stmt = $pdo->prepare('SELECT d.*, u.username as added_by_username FROM opd_doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $qualification = trim($_POST['qualification'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $hospital = trim($_POST['hospital'] ?? '');
        $contact_no = trim($_POST['contact_no'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $registration_no = trim($_POST['registration_no'] ?? '');
        $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        if (empty($name)) {
            json_response(['success' => false, 'message' => 'Doctor name is required'], 400);
        }

        if ($id) {
            try {
                $stmt = $pdo->prepare('UPDATE opd_doctors SET name=?, qualification=?, specialization=?, hospital=?, contact_no=?, phone=?, email=?, address=?, registration_no=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $id]);
                json_response(['success' => true, 'message' => 'OPD Doctor updated successfully']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Error updating: ' . $e->getMessage()], 500);
            }
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO opd_doctors (name, qualification, specialization, hospital, contact_no, phone, email, address, registration_no, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $added_by]);
                json_response(['success' => true, 'message' => 'OPD Doctor added successfully']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Error adding: ' . $e->getMessage()], 500);
            }
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        try {
            $stmt = $pdo->prepare('DELETE FROM opd_doctors WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            json_response(['success' => true, 'message' => 'OPD Doctor deleted successfully']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error deleting: ' . $e->getMessage()], 500);
        }
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
