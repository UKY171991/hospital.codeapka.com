<?php
// ajax/entry_api.php - CRUD for entries
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list') {
    $stmt = $pdo->query("SELECT e.id, p.name AS patient_name, d.name AS doctor_name, t.name AS test_name, e.referring_doctor, e.entry_date, e.status FROM entries e LEFT JOIN patients p ON e.patient_id = p.id LEFT JOIN doctors d ON e.doctor_id = d.id LEFT JOIN tests t ON e.test_id = t.id ORDER BY e.id DESC");
    $rows = $stmt->fetchAll();
    json_response(['success'=>true,'data'=>$rows]);
}

if ($action === 'get' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM entries WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    json_response(['success'=>true,'data'=>$row]);
}

if ($action === 'save') {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $id = $_POST['id'] ?? '';
    $patient_id = $_POST['patient_id'] ?? null;
    $doctor_id = $_POST['doctor_id'] ?? null;
    $test_id = $_POST['test_id'] ?? null;
    $referring_doctor = trim($_POST['referring_doctor'] ?? '');
    $entry_date = $_POST['entry_date'] ?? null;
    $result_value = trim($_POST['result_value'] ?? '');
    $unit = trim($_POST['unit'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');
    $status = $_POST['status'] ?? 'pending';

    if ($id) {
        $stmt = $pdo->prepare('UPDATE entries SET patient_id=?, doctor_id=?, test_id=?, referring_doctor=?, entry_date=?, result_value=?, unit=?, remarks=?, status=? WHERE id=?');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $referring_doctor, $entry_date, $result_value, $unit, $remarks, $status, $id]);
        json_response(['success'=>true,'message'=>'Entry updated']);
    } else {
        $stmt = $pdo->prepare('INSERT INTO entries (patient_id, doctor_id, test_id, referring_doctor, entry_date, result_value, unit, remarks, status, created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())');
        $stmt->execute([$patient_id, $doctor_id, $test_id, $referring_doctor, $entry_date, $result_value, $unit, $remarks, $status]);
        json_response(['success'=>true,'message'=>'Entry created']);
    }
}

if ($action === 'delete' && isset($_POST['id'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
    $stmt->execute([$_POST['id']]);
    json_response(['success'=>true,'message'=>'Entry deleted']);
}

json_response(['success'=>false,'message'=>'Invalid action'],400);
