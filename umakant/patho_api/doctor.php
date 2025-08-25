<?php
// patho_api/doctor.php - API to create and list doctors per user
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';
try {
    if ($action === 'list') {
        // If user_id is provided, return doctors added by that user
        $userId = $_GET['user_id'] ?? null;
        if (!$userId && isset($_SESSION['user_id'])) $userId = $_SESSION['user_id'];
        if ($userId) {
            $stmt = $pdo->prepare('SELECT d.*, u.username AS added_by_username FROM doctors d LEFT JOIN users u ON d.added_by = u.id WHERE d.added_by = ? ORDER BY d.id DESC');
            $stmt->execute([$userId]);
            $rows = $stmt->fetchAll();
            json_response(['success'=>true,'data'=>$rows]);
        } else {
            json_response(['success'=>false,'message'=>'user_id required or authenticated'],400);
        }
    }

    if ($action === 'save') {
        // allow authenticated users to create doctors; you can restrict to admin/master if needed
        if (!isset($_SESSION['user_id'])) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $name = trim($_POST['name'] ?? '');
        $qualification = trim($_POST['qualification'] ?? '');
        $specialization = trim($_POST['specialization'] ?? '');
        $hospital = trim($_POST['hospital'] ?? '');
        $contact_no = trim($_POST['contact_no'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $registration_no = trim($_POST['registration_no'] ?? '');
        $percent = $_POST['percent'] ?? null;
        $added_by = $_SESSION['user_id'];

        $stmt = $pdo->prepare('INSERT INTO doctors (name, qualification, specialization, hospital, contact_no, phone, email, address, registration_no, percent, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $percent, $added_by]);
        json_response(['success'=>true,'message'=>'Doctor added']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Exception $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
