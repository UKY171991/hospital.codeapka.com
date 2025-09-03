<?php
// patho_api/patient.php - public API for patients (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';
$viewerRole = $_SESSION['role'] ?? null;
$viewerId = $_SESSION['user_id'] ?? null;

try {
    if ($action === 'list') {
        // Public list: return basic patient info matching UI columns
        $stmt = $pdo->query('SELECT id, uhid, name, age, age_unit, sex as gender, mobile as phone, father_husband, address, created_at FROM patients ORDER BY id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT id, uhid, name, age, age_unit, sex as gender, mobile as phone, father_husband, address, created_at FROM patients WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success' => false, 'message' => 'Patient not found'], 404);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // requires authenticated admin/master role per ajax behavior
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
            $stmt = $pdo->prepare('UPDATE patients SET name=?, mobile=?, father_husband=?, address=?, sex=?, age=?, age_unit=?, uhid=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $mobile, $father_husband, $address, $sex, $age, $age_unit, $uhid, $id]);
            json_response(['success' => true, 'message' => 'Patient updated']);
        } else {
            $data = ['name'=>$name, 'mobile'=>$mobile, 'father_husband'=>$father_husband, 'address'=>$address, 'sex'=>$sex, 'age'=>$age, 'age_unit'=>$age_unit, 'uhid'=>$uhid];
            if ($uhid !== '') $unique = ['uhid'=>$uhid];
            else $unique = ['name'=>$name, 'mobile'=>$mobile];
            $res = upsert_or_skip($pdo, 'patients', $unique, $data);
            json_response(['success' => true, 'message' => 'Patient '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM patients WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Patient deleted']);
    }

    json_response(['success' => false, 'message' => 'Invalid action'], 400);
} catch (Throwable $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
