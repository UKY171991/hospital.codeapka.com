<?php
// ajax/patient_api.php - CRUD for patients
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    $action = $_REQUEST['action'] ?? 'list';

    if ($action === 'list') {
        // removed non-existent `email` column to match schema
        $stmt = $pdo->query('SELECT id, uhid, name, age, sex as gender, mobile as phone FROM patients ORDER BY id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $rows]);
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
            json_response(['success' => true, 'message' => 'Patient created']);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM patients WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Patient deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    // return JSON error for debugging in browser
    json_response(['success' => false, 'message' => $e->getMessage()], 500);
}
