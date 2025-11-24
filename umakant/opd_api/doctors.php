<?php
// opd_api/doctors.php - OPD Doctors API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List doctors
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

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY d.id DESC";
        $limit = " LIMIT $start, $length";
        
        $checkColumn = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'status'");
        $statusExists = $checkColumn->rowCount() > 0;
        
        if ($statusExists) {
            $dataQuery = "SELECT d.*, COALESCE(d.status, 'Active') as status, u.username as added_by_username " . $baseQuery . $whereClause . $orderBy . $limit;
        } else {
            $dataQuery = "SELECT d.*, 'Active' as status, u.username as added_by_username " . $baseQuery . $whereClause . $orderBy . $limit;
        }

        $dataStmt = $pdo->prepare($dataQuery);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);
        
        json_response([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'success' => true,
            'data' => $data
        ]);
    }

    // Get single doctor
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT d.*, u.username as added_by_username FROM opd_doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    // Save doctor
    if ($action === 'save') {
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
        $status = trim($_POST['status'] ?? 'Active');
        $added_by = $_SESSION['user_id'] ?? null;

        if (empty($name)) {
            json_response(['success' => false, 'message' => 'Doctor name is required'], 400);
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE opd_doctors SET name=?, qualification=?, specialization=?, hospital=?, contact_no=?, phone=?, email=?, address=?, registration_no=?, status=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $status, $id]);
            json_response(['success' => true, 'message' => 'Doctor updated successfully']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO opd_doctors (name, qualification, specialization, hospital, contact_no, phone, email, address, registration_no, status, added_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $status, $added_by]);
            json_response(['success' => true, 'message' => 'Doctor added successfully']);
        }
    }

    // Delete doctor
    if ($action === 'delete' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM opd_doctors WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Doctor deleted successfully']);
    }

    // Toggle status
    if ($action === 'toggle_status' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('UPDATE opd_doctors SET status = IF(status = "Active", "Inactive", "Active"), updated_at = NOW() WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        
        $getStmt = $pdo->prepare('SELECT status FROM opd_doctors WHERE id = ?');
        $getStmt->execute([$_POST['id']]);
        $newStatus = $getStmt->fetchColumn();
        
        json_response(['success' => true, 'message' => 'Status updated successfully', 'status' => $newStatus]);
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
        $total = $totalStmt->fetchColumn();
        
        $checkColumn = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'status'");
        $statusExists = $checkColumn->rowCount() > 0;
        
        if ($statusExists) {
            $activeStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors WHERE status = 'Active' OR status IS NULL");
            $active = $activeStmt->fetchColumn();
            $inactiveStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors WHERE status = 'Inactive'");
            $inactive = $inactiveStmt->fetchColumn();
        } else {
            $active = $total;
            $inactive = 0;
        }
        
        $specializationsStmt = $pdo->query("SELECT COUNT(DISTINCT specialization) FROM opd_doctors WHERE specialization IS NOT NULL AND specialization != ''");
        $specializations = $specializationsStmt->fetchColumn();
        
        $hospitalsStmt = $pdo->query("SELECT COUNT(DISTINCT hospital) FROM opd_doctors WHERE hospital IS NOT NULL AND hospital != ''");
        $hospitals = $hospitalsStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'specializations' => $specializations,
                'hospitals' => $hospitals
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
