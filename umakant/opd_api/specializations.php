<?php
// opd_api/specializations.php - OPD Specializations API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List specializations
    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        $baseQuery = "FROM opd_specializations s LEFT JOIN opd_departments d ON s.department_id = d.id";
        $whereClause = " WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (s.name LIKE ? OR s.description LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm];
        }

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_specializations");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY s.id DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT s.*, d.name as department_name " . $baseQuery . $whereClause . $orderBy . $limit;
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

    // Get single specialization
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT s.*, d.name as department_name FROM opd_specializations s LEFT JOIN opd_departments d ON s.department_id = d.id WHERE s.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    // Save specialization
    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $department_id = $_POST['department_id'] ?? null;
        $is_active = (int)($_POST['is_active'] ?? 1);

        if (empty($name)) {
            json_response(['success' => false, 'message' => 'Specialization name is required'], 400);
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE opd_specializations SET name=?, description=?, department_id=?, is_active=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $description, $department_id ?: null, $is_active, $id]);
            json_response(['success' => true, 'message' => 'Specialization updated successfully']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO opd_specializations (name, description, department_id, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$name, $description, $department_id ?: null, $is_active]);
            json_response(['success' => true, 'message' => 'Specialization added successfully']);
        }
    }

    // Delete specialization
    if ($action === 'delete' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM opd_specializations WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Specialization deleted successfully']);
    }

    // Get departments list for dropdown
    if ($action === 'get_departments') {
        $stmt = $pdo->query("SELECT id, name FROM opd_departments WHERE is_active = 1 ORDER BY name");
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $departments]);
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_specializations");
        $total = $totalStmt->fetchColumn();
        
        $activeStmt = $pdo->query("SELECT COUNT(*) FROM opd_specializations WHERE is_active = 1");
        $active = $activeStmt->fetchColumn();
        
        $doctorsStmt = $pdo->query("SELECT COUNT(*) FROM opd_doctors");
        $doctors = $doctorsStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'doctors' => $doctors
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
