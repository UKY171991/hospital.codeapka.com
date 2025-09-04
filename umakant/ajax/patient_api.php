<?php
// ajax/patient_api.php - CRUD for patients
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    if ($action === 'list') {
        // Support DataTables server-side processing
        $draw = $_POST['draw'] ?? 1;
        $start = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 25;
        $search = $_POST['search']['value'] ?? '';
        
        // Base query
        $baseQuery = "FROM patients p LEFT JOIN users u ON p.added_by = u.id";
        $whereClause = "";
        $params = [];
        
        // Add search conditions
        if (!empty($search)) {
            $whereClause = " WHERE (p.name LIKE ? OR p.mobile LIKE ? OR p.uhid LIKE ? OR p.father_husband LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
        }
        
        // Get total records
        $totalStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery . $whereClause);
        $totalStmt->execute($params);
        $totalRecords = $totalStmt->fetchColumn();
        
        // Get filtered records
        $orderBy = " ORDER BY p.id DESC";
        $limit = " LIMIT $start, $length";
        
        $dataQuery = "SELECT p.id, p.uhid, p.name, p.mobile, p.email, p.age, p.age_unit, 
                      p.sex as gender, p.father_husband, p.address, p.created_at, 
                      p.added_by, u.username as added_by_name " . 
                      $baseQuery . $whereClause . $orderBy . $limit;
        
        $dataStmt = $pdo->prepare($dataQuery);
        $dataStmt->execute($params);
        $data = $dataStmt->fetchAll();
        
        // Return DataTables format
        json_response([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
            'success' => true
        ]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM patients WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $mobile = trim($_POST['mobile'] ?? '');
        $father_husband = trim($_POST['father_husband'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $sex = $_POST['sex'] ?? '';
        $age = $_POST['age'] ?? null;
        $age_unit = $_POST['age_unit'] ?? 'Years';
        $uhid = trim($_POST['uhid'] ?? '');

        if ($id) {
            $stmt = $pdo->prepare('UPDATE patients SET name=?, mobile=?, father_husband=?, address=?, sex=?, age=?, age_unit=?, uhid=? WHERE id=?');
            $stmt->execute([$name, $mobile, $father_husband, $address, $sex, $age, $age_unit, $uhid, $id]);
            json_response(['success' => true, 'message' => 'Patient updated']);
        } else {
            $stmt = $pdo->prepare('INSERT INTO patients (name, mobile, father_husband, address, sex, age, age_unit, uhid, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $mobile, $father_husband, $address, $sex, $age, $age_unit, $uhid]);
            
            // Get the newly inserted record
            $newId = $pdo->lastInsertId();
            $stmt = $pdo->prepare('SELECT id, uhid, name, age, sex as gender, mobile as phone FROM patients WHERE id = ?');
            $stmt->execute([$newId]);
            $newRecord = $stmt->fetch();
            
            json_response(['success' => true, 'message' => 'Patient created', 'data' => $newRecord]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM patients WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Patient deleted']);
    }

    if ($action === 'stats') {
        // Get patient statistics
        $stats = [];
        
        // Total patients
        $stmt = $pdo->query('SELECT COUNT(*) FROM patients');
        $stats['total'] = $stmt->fetchColumn();
        
        // Today's patients
        $stmt = $pdo->query('SELECT COUNT(*) FROM patients WHERE DATE(created_at) = CURDATE()');
        $stats['today'] = $stmt->fetchColumn();
        
        // Male patients
        $stmt = $pdo->query('SELECT COUNT(*) FROM patients WHERE sex = "Male"');
        $stats['male'] = $stmt->fetchColumn();
        
        // Female patients
        $stmt = $pdo->query('SELECT COUNT(*) FROM patients WHERE sex = "Female"');
        $stats['female'] = $stmt->fetchColumn();
        
        json_response(['success' => true, 'data' => $stats]);
    }

    if ($action === 'bulk_delete' && isset($_POST['ids'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        
        $ids = $_POST['ids'];
        if (is_array($ids) && count($ids) > 0) {
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $pdo->prepare("DELETE FROM patients WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            json_response(['success' => true, 'message' => count($ids) . ' patients deleted']);
        } else {
            json_response(['success' => false, 'message' => 'No patient IDs provided']);
        }
    }

    if ($action === 'bulk_export' && isset($_REQUEST['ids'])) {
        $ids = $_REQUEST['ids'];
        if (is_array($ids) && count($ids) > 0) {
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            $stmt = $pdo->prepare("SELECT id, uhid, name, mobile, email, age, age_unit, sex as gender, father_husband, address, created_at FROM patients WHERE id IN ($placeholders)");
            $stmt->execute($ids);
            $data = $stmt->fetchAll();
            json_response(['success' => true, 'data' => $data]);
        } else {
            json_response(['success' => false, 'message' => 'No patient IDs provided']);
        }
    }

    if ($action === 'export') {
        $stmt = $pdo->query('SELECT id, uhid, name, mobile, email, age, age_unit, sex as gender, father_husband, address, created_at FROM patients ORDER BY id DESC');
        $data = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $data]);
    }

    if ($action === 'stats') {
        // Get patient statistics
        $totalStmt = $pdo->query('SELECT COUNT(*) FROM patients');
        $total = $totalStmt->fetchColumn();
        
        $todayStmt = $pdo->query('SELECT COUNT(*) FROM patients WHERE DATE(created_at) = CURDATE()');
        $today = $todayStmt->fetchColumn();
        
        $maleStmt = $pdo->query('SELECT COUNT(*) FROM patients WHERE sex = "Male"');
        $male = $maleStmt->fetchColumn();
        
        $femaleStmt = $pdo->query('SELECT COUNT(*) FROM patients WHERE sex = "Female"');
        $female = $femaleStmt->fetchColumn();
        
        json_response([
            'success' => true,
            'data' => [
                'total' => $total,
                'today' => $today,
                'male' => $male,
                'female' => $female
            ]
        ]);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    // return JSON error for debugging in browser
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
