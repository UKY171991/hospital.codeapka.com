<?php
// opd_api/facilities.php - OPD Facilities API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List facilities
    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        $baseQuery = "FROM opd_facilities f LEFT JOIN opd_departments d ON f.department_id = d.id";
        $whereClause = " WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (f.name LIKE ? OR f.type LIKE ? OR f.location LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_facilities");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY f.id DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT f.*, d.name as department_name " . $baseQuery . $whereClause . $orderBy . $limit;
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

    // Get single facility
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT f.*, d.name as department_name FROM opd_facilities f LEFT JOIN opd_departments d ON f.department_id = d.id WHERE f.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    // Save facility
    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $capacity = $_POST['capacity'] ?? null;
        $department_id = $_POST['department_id'] ?? null;
        $is_available = (int)($_POST['is_available'] ?? 1);
        $is_active = (int)($_POST['is_active'] ?? 1);

        if (empty($name)) {
            json_response(['success' => false, 'message' => 'Facility name is required'], 400);
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE opd_facilities SET name=?, description=?, type=?, location=?, capacity=?, department_id=?, is_available=?, is_active=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $description, $type, $location, $capacity, $department_id ?: null, $is_available, $is_active, $id]);
            json_response(['success' => true, 'message' => 'Facility updated successfully']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO opd_facilities (name, description, type, location, capacity, department_id, is_available, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$name, $description, $type, $location, $capacity, $department_id ?: null, $is_available, $is_active]);
            json_response(['success' => true, 'message' => 'Facility added successfully']);
        }
    }

    // Delete facility
    if ($action === 'delete' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM opd_facilities WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Facility deleted successfully']);
    }

    // Get departments list for dropdown
    if ($action === 'get_departments') {
        $stmt = $pdo->query("SELECT id, name FROM opd_departments WHERE is_active = 1 ORDER BY name");
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $departments]);
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_facilities");
        $total = $totalStmt->fetchColumn();
        
        $availableStmt = $pdo->query("SELECT COUNT(*) FROM opd_facilities WHERE is_available = 1 AND is_active = 1");
        $available = $availableStmt->fetchColumn();
        
        $occupiedStmt = $pdo->query("SELECT COUNT(*) FROM opd_facilities WHERE is_available = 0 AND is_active = 1");
        $occupied = $occupiedStmt->fetchColumn();
        
        $capacityStmt = $pdo->query("SELECT SUM(capacity) FROM opd_facilities WHERE is_active = 1");
        $capacity = $capacityStmt->fetchColumn() ?? 0;
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'available' => $available,
                'occupied' => $occupied,
                'capacity' => $capacity
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
