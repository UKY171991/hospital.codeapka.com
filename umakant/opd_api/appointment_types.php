<?php
// opd_api/appointment_types.php - OPD Appointment Types API
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    // List appointment types
    if ($action === 'list') {
        $draw = (int)($_REQUEST['draw'] ?? 1);
        $start = max(0, (int)($_REQUEST['start'] ?? 0));
        $length = max(1, min(100, (int)($_REQUEST['length'] ?? 25)));
        $search = trim($_REQUEST['search']['value'] ?? '');
        
        $baseQuery = "FROM opd_appointment_types";
        $whereClause = " WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $whereClause .= " AND (name LIKE ? OR description LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm];
        }

        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointment_types");
        $totalRecords = $totalStmt->fetchColumn();

        $filteredStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $filteredStmt->execute($params);
        $filteredRecords = $filteredStmt->fetchColumn();

        $orderBy = " ORDER BY id DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT * " . $baseQuery . $whereClause . $orderBy . $limit;
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

    // Get single appointment type
    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM opd_appointment_types WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success' => true, 'data' => $row]);
    }

    // Save appointment type
    if ($action === 'save') {
        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $duration_minutes = (int)($_POST['duration_minutes'] ?? 30);
        $color = trim($_POST['color'] ?? '#007bff');
        $is_active = (int)($_POST['is_active'] ?? 1);

        if (empty($name)) {
            json_response(['success' => false, 'message' => 'Type name is required'], 400);
        }

        if ($id) {
            $stmt = $pdo->prepare('UPDATE opd_appointment_types SET name=?, description=?, duration_minutes=?, color=?, is_active=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $description, $duration_minutes, $color, $is_active, $id]);
            json_response(['success' => true, 'message' => 'Appointment type updated successfully']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO opd_appointment_types (name, description, duration_minutes, color, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$name, $description, $duration_minutes, $color, $is_active]);
            json_response(['success' => true, 'message' => 'Appointment type added successfully']);
        }
    }

    // Delete appointment type
    if ($action === 'delete' && isset($_POST['id'])) {
        $stmt = $pdo->prepare('DELETE FROM opd_appointment_types WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Appointment type deleted successfully']);
    }

    // Stats
    if ($action === 'stats') {
        $totalStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointment_types");
        $total = $totalStmt->fetchColumn();
        
        $activeStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointment_types WHERE is_active = 1");
        $active = $activeStmt->fetchColumn();
        
        $appointmentsStmt = $pdo->query("SELECT COUNT(*) FROM opd_appointments");
        $appointments = $appointmentsStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'appointments' => $appointments
            ]
        ]);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
