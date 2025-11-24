<?php
// opd_api/departments.php - OPD Departments API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List departments
    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        $baseQuery = "FROM opd_departments d LEFT JOIN opd_doctors doc ON d.head_doctor_id = doc.id";
        $whereClause = " WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (d.name LIKE ? OR d.description LIKE ? OR d.location LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_departments");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY d.id DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT d.*, doc.name as head_doctor_name " . $baseQuery . $whereClause . $orderBy . $limit;
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

    // Get single department
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT d.*, doc.name as head_doctor_name FROM opd_departments d LEFT JOIN opd_doctors doc ON d.head_doctor_id = doc.id WHERE d.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    // Save department
    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $head_doctor_id = $_POST['head_doctor_id'] ?? null;
        $location = trim($_POST['location'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $is_active = (int)($_POST['is_active'] ?? 1);

        if (empty($name)) {
            json_response(['success' => false, 'message' => 'Department name is required'], 400);
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE opd_departments SET name=?, description=?, head_doctor_id=?, location=?, phone=?, email=?, is_active=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $description, $head_doctor_id ?: null, $location, $phone, $email, $is_active, $id]);
            json_response(['success' => true, 'message' => 'Department updated successfully']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO opd_departments (name, description, head_doctor_id, location, phone, email, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$name, $description, $head_doctor_id ?: null, $location, $phone, $email, $is_active]);
            json_response(['success' => true, 'message' => 'Department added successfully']);
        }
    }

    // Delete department
    if ($action === 'delete' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM opd_departments WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Department deleted successfully']);
    }

    // Get doctors list for dropdown
    if ($action === 'get_doctors') {
        $stmt = $pdo->query("SELECT id, name FROM opd_doctors WHERE is_active = 1 ORDER BY name");
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $doctors]);
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_departments");
        $total = $totalStmt->fetchColumn();
        
        $activeStmt = $pdo->query("SELECT COUNT(*) FROM opd_departments WHERE is_active = 1");
        $active = $activeStmt->fetchColumn();
        
        $doctorsStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
        $doctors = $doctorsStmt->fetchColumn();
        
        $specializationsStmt = $pdo->query("SELECT COUNT(*) FROM opd_specializations");
        $specializations = $specializationsStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'doctors' => $doctors,
                'specializations' => $specializations
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
