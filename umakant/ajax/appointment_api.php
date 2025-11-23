<?php
// ajax/appointment_api.php - CRUD for OPD appointments
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
        
        $baseQuery = "FROM opd_appointments a LEFT JOIN users u ON a.added_by = u.id";
        $whereClause = "";
        $params = [];
        
        if (!empty($search)) {
            $whereClause = " WHERE (a.patient_name LIKE ? OR a.doctor_name LIKE ? OR a.department LIKE ? OR a.patient_contact LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }

        try {
            if (empty($whereClause)) {
                $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments");
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

        $orderBy = " ORDER BY a.id DESC";
        if (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && count($_REQUEST['order']) > 0) {
            $orderColumn = (int)$_REQUEST['order'][0]['column'];
            $orderDir = $_REQUEST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';
            
            $columns = ['a.id', 'a.patient_name', 'a.doctor_name', 'a.department', 'a.appointment_date', 'a.created_at'];
            if (isset($columns[$orderColumn])) {
                $orderBy = " ORDER BY " . $columns[$orderColumn] . " " . $orderDir;
            }
        }
        
        $limit = " LIMIT $start, $length";
        
        try {
            $dataQuery = "SELECT a.id, a.patient_name, a.patient_contact, a.patient_email, a.patient_age,
                         a.doctor_name, a.department, a.appointment_date, a.time_slot, a.reason, a.notes,
                         COALESCE(a.status, 'Pending') as status,
                         a.added_by, u.username as added_by_username, a.created_at, a.updated_at
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
            $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments");
            $total = $totalStmt->fetchColumn();
            
            $pendingStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE status = 'Pending' OR status IS NULL");
            $pending = $pendingStmt->fetchColumn();
            
            $confirmedStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE status = 'Confirmed'");
            $confirmed = $confirmedStmt->fetchColumn();
            
            $cancelledStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments WHERE status = 'Cancelled'");
            $cancelled = $cancelledStmt->fetchColumn();
            
            json_response([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'pending' => $pending,
                    'confirmed' => $confirmed,
                    'cancelled' => $cancelled
                ]
            ]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching stats: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'get_departments') {
        try {
            $stmt = $pdo->query("SELECT DISTINCT name FROM opd_departments WHERE status = 'Active' ORDER BY name");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $data]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching departments: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'get_doctors') {
        try {
            $stmt = $pdo->query("SELECT DISTINCT name FROM opd_doctors WHERE status = 'Active' ORDER BY name");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            json_response(['success' => true, 'data' => $data]);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error fetching doctors: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT a.*, u.username as added_by_username FROM opd_appointments a LEFT JOIN users u ON a.added_by = u.id WHERE a.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = $_POST['id'] ?? '';
        $patient_name = trim($_POST['patient_name'] ?? '');
        $patient_contact = trim($_POST['patient_contact'] ?? '');
        $patient_email = trim($_POST['patient_email'] ?? '');
        $patient_age = trim($_POST['patient_age'] ?? '');
        $doctor_name = trim($_POST['doctor_name'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $appointment_date = trim($_POST['appointment_date'] ?? '');
        $time_slot = trim($_POST['time_slot'] ?? '');
        $reason = trim($_POST['reason'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $status = trim($_POST['status'] ?? 'Pending');
        $added_by = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        if (empty($patient_name)) {
            json_response(['success' => false, 'message' => 'Patient name is required'], 400);
        }

        if (empty($patient_contact)) {
            json_response(['success' => false, 'message' => 'Patient contact is required'], 400);
        }

        if (empty($appointment_date)) {
            json_response(['success' => false, 'message' => 'Appointment date is required'], 400);
        }

        if ($id) {
            try {
                $stmt = $pdo->prepare('UPDATE opd_appointments SET patient_name=?, patient_contact=?, patient_email=?, patient_age=?, doctor_name=?, department=?, appointment_date=?, time_slot=?, reason=?, notes=?, status=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$patient_name, $patient_contact, $patient_email, $patient_age, $doctor_name, $department, $appointment_date, $time_slot, $reason, $notes, $status, $id]);
                json_response(['success' => true, 'message' => 'Appointment updated successfully']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Error updating: ' . $e->getMessage()], 500);
            }
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO opd_appointments (patient_name, patient_contact, patient_email, patient_age, doctor_name, department, appointment_date, time_slot, reason, notes, status, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
                $stmt->execute([$patient_name, $patient_contact, $patient_email, $patient_age, $doctor_name, $department, $appointment_date, $time_slot, $reason, $notes, $status, $added_by]);
                json_response(['success' => true, 'message' => 'Appointment added successfully']);
            } catch (PDOException $e) {
                json_response(['success' => false, 'message' => 'Error adding: ' . $e->getMessage()], 500);
            }
        }
    }

    if ($action === 'update_status' && isset($_POST['id']) && isset($_POST['status'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        try {
            $stmt = $pdo->prepare('UPDATE opd_appointments SET status = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$_POST['status'], $_POST['id']]);
            
            json_response(['success' => true, 'message' => 'Status updated successfully']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()], 500);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) {
            json_response(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        try {
            $stmt = $pdo->prepare('DELETE FROM opd_appointments WHERE id = ?');
            $stmt->execute([$_POST['id']]);
            json_response(['success' => true, 'message' => 'Appointment deleted successfully']);
        } catch (PDOException $e) {
            json_response(['success' => false, 'message' => 'Error deleting: ' . $e->getMessage()], 500);
        }
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
