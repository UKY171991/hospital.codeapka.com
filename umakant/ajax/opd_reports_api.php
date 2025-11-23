<?php
// ajax/opd_reports_api.php - CRUD for OPD Reports
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
        
        $baseQuery = "FROM opd_reports r LEFT JOIN users u ON r.added_by = u.id";
        $whereClause = "";
        $params = [];
        
        if (!empty($search)) {
            $whereClause = " WHERE (r.patient_name LIKE ? OR r.patient_phone LIKE ? OR r.doctor_name LIKE ? OR r.diagnosis LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        try {
            if (empty($whereClause)) {
                $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports");
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

        $orderBy = " ORDER BY r.id DESC";
        if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {
            $orderColumn = (int)$_REQUEST['order'][0]['column'];
            $orderDir = $_REQUEST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';
            
            $columns = ['r.id', 'r.patient_name', 'r.doctor_name', 'r.report_date', 'r.diagnosis'];
            if (isset($columns[$orderColumn])) {
                $orderBy = " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;
            }
        }
        
        $limit = " LIMIT $start, $length";
        
        try {
            $dataQuery = "SELECT r.*, u.username as added_by_username
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
        try {
            $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports");
            $total = $totalStmt->fetchColumn();
            
            $todayStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports WHERE DATE(report_date) = CURDATE()");
            $today = $todayStmt->fetchColumn();
            
            $weekStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports WHERE report_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
            $week = $weekStmt->fetchColumn();
            
            $monthStmt = $pdo->query("SELECT COUNT(*) FROM opd_reports WHERE report_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
            $month = $monthStmt->fetchColumn();
            
            json_response([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'today' => $today,
                    'week' => $week,
                    'month' => $month
                ]
            ]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching stats: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT r.*, u.username as added_by_username FROM opd_reports r LEFT JOIN users u ON r.added_by = u.id WHERE r.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'get_doctors') {
        try {
            $checkColumn = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'status'");
            $statusExists = $checkColumn->rowCount() > 0;
            
            if ($statusExists) {
                $stmt = $pdo->query("SELECT id, name, specialization, hospital FROM opd_doctors WHERE status = 'Active' OR status IS NULL ORDER BY name ASC");
            } else {
                $stmt = $pdo->query("SELECT id, name, specialization, hospital FROM opd_doctors ORDER BY name ASC");
            }
            
            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $doctors]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching doctors: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = $_POST['id'] ?? '';
        $patient_name = trim($_POST['patient_name'] ?? '');
        $patient_phone = trim($_POST['patient_phone'] ?? '');
        $patient_age = !empty($_POST['patient_age']) ? (int)$_POST['patient_age'] : null;
        $patient_gender = trim($_POST['patient_gender'] ?? '');
        $doctor_name = trim($_POST['doctor_name'] ?? '');
        $report_date = trim($_POST['report_date'] ?? date('Y-m-d'));
        $diagnosis = trim($_POST['diagnosis'] ?? '');
        $symptoms = trim($_POST['symptoms'] ?? '');
        $test_results = trim($_POST['test_results'] ?? '');
        $prescription = trim($_POST['prescription'] ?? '');
        $follow_up_date = !empty($_POST['follow_up_date']) ? trim($_POST['follow_up_date']) : null;
        $notes = trim($_POST['notes'] ?? '');
        $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        if (empty($patient_name)) {
            json_response(['success' => false, 'message' => 'Patient name is required'], 400);
        }

        if ($id) {
            try {
                $stmt = $pdo->prepare('UPDATE opd_reports SET patient_name=?, patient_phone=?, patient_age=?, patient_gender=?, doctor_name=?, report_date=?, diagnosis=?, symptoms=?, test_results=?, prescription=?, follow_up_date=?, notes=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$patient_name, $patient_phone, $patient_age, $patient_gender, $doctor_name, $report_date, $diagnosis, $symptoms, $test_results, $prescription, $follow_up_date, $notes, $id]);
                json_response(['success' => true, 'message' => 'Report updated successfully']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Error updating: ' . $e->getMessage()], 500);
            }
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO opd_reports (patient_name, patient_phone, patient_age, patient_gender, doctor_name, report_date, diagnosis, symptoms, test_results, prescription, follow_up_date, notes, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([$patient_name, $patient_phone, $patient_age, $patient_gender, $doctor_name, $report_date, $diagnosis, $symptoms, $test_results, $prescription, $follow_up_date, $notes, $added_by]);
                json_response(['success' => true, 'message' => 'Report added successfully']);
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
            $stmt = $pdo->prepare('DELETE FROM opd_reports WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            json_response(['success' => true, 'message' => 'Report deleted successfully']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error deleting: ' . $e->getMessage()], 500);
        }
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
