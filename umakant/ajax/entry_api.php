<?php
// ajax/entry_api.php - CRUD for entries
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

header('Content-Type: application/json; charset=utf-8');

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'stats') {
    // Get statistics for dashboard
    $stats = [];
    
    // Total entries
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries");
    $stats['total'] = $stmt->fetchColumn();
    
    // Pending entries
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'pending'");
    $stats['pending'] = $stmt->fetchColumn();
    
    // Completed entries
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE status = 'completed'");
    $stats['completed'] = $stmt->fetchColumn();
    
    // Today's entries
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE DATE(created_at) = CURDATE()");
    $stats['today'] = $stmt->fetchColumn();
    
        json_response(['status' => 'success', 'data' => $stats]);
    }

    if ($action === 'list') {
    // Use test_date as entry date and take unit from tests table when available
    $stmt = $pdo->query("SELECT e.id, p.name AS patient_name, d.name AS doctor_name, t.name AS test_name, COALESCE(e.test_date, e.created_at) AS entry_date, e.result_value, COALESCE(t.unit, t.units, '') AS unit, e.remarks, e.status FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id ORDER BY COALESCE(e.test_date, e.created_at) DESC, e.id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        json_response(['success'=>true,'data'=>$rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
    // Return entry with related names and unit
    $stmt = $pdo->prepare('SELECT e.*, p.name AS patient_name, p.uhid, d.name AS doctor_name, t.name AS test_name, COALESCE(t.unit,t.units,"") AS unit FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id WHERE e.id = ?');
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $id = $_POST['id'] ?? '';
    $patient_id = $_POST['patient_id'] ?? null;
    $doctor_id = $_POST['doctor_id'] ?? null;
    $test_id = $_POST['test_id'] ?? null;
    // Map form fields to schema: entry_date -> test_date, accept 'result' or 'result_value'
    $test_date = $_POST['entry_date'] ?? $_POST['test_date'] ?? null;
    $reported_date = $_POST['reported_date'] ?? date('Y-m-d H:i:s');
    $result_value = trim($_POST['result'] ?? $_POST['result_value'] ?? '');
    $remarks = trim($_POST['remarks'] ?? $_POST['notes'] ?? '');
    $status = $_POST['status'] ?? 'pending';

    if ($id) {
        $stmt = $pdo->prepare('UPDATE entries SET patient_id=?, doctor_id=?, test_id=?, test_date=?, reported_date=?, result_value=?, remarks=?, status=? WHERE id=?');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $test_date, $reported_date, $result_value, $remarks, $status, $id]);
        json_response(['success'=>true,'message'=>'Entry updated']);
    } else {
        $stmt = $pdo->prepare('INSERT INTO entries (patient_id, doctor_id, test_id, test_date, reported_date, result_value, remarks, status, created_at) VALUES (?,?,?,?,?,?,?,?,NOW())');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $test_date, $reported_date, $result_value, $remarks, $status]);
        json_response(['success'=>true,'message'=>'Entry created']);
    }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
    $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Entry deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);

} catch (PDOException $e) {
    error_log('Entry API PDO error: ' . $e->getMessage());
    http_response_code(500);
    json_response(['success' => false, 'message' => 'Database error', 'error' => $e->getMessage()]);
} catch (Exception $e) {
    error_log('Entry API error: ' . $e->getMessage());
    http_response_code(500);
    json_response(['success' => false, 'message' => 'Server error', 'error' => $e->getMessage()]);
}
