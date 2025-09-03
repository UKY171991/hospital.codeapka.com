<?php
// https://hospital.codeapka.com/umakant/patho_api/entry.php - public API for test entries (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $pdo->query("SELECT e.id, p.name AS patient_name, d.name AS doctor_name, t.name AS test_name, e.entry_date, e.result_value, e.unit, e.remarks, e.status, e.added_by FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id ORDER BY e.id DESC");
        $rows = $stmt->fetchAll();
        json_response(['success'=>true,'data'=>$rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM entries WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Entry not found'],404);
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save') {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $id = $_POST['id'] ?? '';
        $patient_id = $_POST['patient_id'] ?? null;
        $doctor_id = $_POST['doctor_id'] ?? null;
        $test_id = $_POST['test_id'] ?? null;
        $entry_date = $_POST['entry_date'] ?? null;
        $result_value = trim($_POST['result_value'] ?? '');
        $unit = trim($_POST['unit'] ?? '');
        $remarks = trim($_POST['remarks'] ?? '');
        $status = $_POST['status'] ?? 'pending';
        $added_by = $_SESSION['user_id'] ?? null;

        if ($id) {
            $stmt = $pdo->prepare('UPDATE entries SET patient_id=?, doctor_id=?, test_id=?, entry_date=?, result_value=?, unit=?, remarks=?, status=?, added_by=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $added_by, $id]);
            json_response(['success'=>true,'message'=>'Entry updated']);
        } else {
            $data = ['patient_id'=>$patient_id, 'doctor_id'=>$doctor_id, 'test_id'=>$test_id, 'entry_date'=>$entry_date, 'result_value'=>$result_value, 'unit'=>$unit, 'remarks'=>$remarks, 'status'=>$status, 'added_by'=>$added_by];
            $unique = ['patient_id'=>$patient_id, 'test_id'=>$test_id, 'entry_date'=>$entry_date];
            $res = upsert_or_skip($pdo, 'entries', $unique, $data);
            json_response(['success'=>true,'message'=>'Entry '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Entry deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
