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
        // Accept JSON body as well as form-encoded
        $input = $_POST;
        if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) $input = array_merge($input, $json);
        }

        $name = trim($input['name'] ?? '');
        $qualification = trim($input['qualification'] ?? '');
        $specialization = trim($input['specialization'] ?? '');
        $hospital = trim($input['hospital'] ?? '');
        $contact_no = trim($input['contact_no'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $email = trim($input['email'] ?? '');
        $address = trim($input['address'] ?? '');
        $registration_no = trim($input['registration_no'] ?? '');
        $percent = isset($input['percent']) ? $input['percent'] : null;
        $added_by = $_SESSION['user_id'];

        if ($name === '') {
            json_response(['success'=>false,'message'=>'Name is required'],400);
        }
        $stmt = $pdo->prepare('INSERT INTO doctors (name, qualification, specialization, hospital, contact_no, phone, email, address, registration_no, percent, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $qualification, $specialization, $hospital, $contact_no, $phone, $email, $address, $registration_no, $percent, $added_by]);
        $newId = $pdo->lastInsertId();
        json_response(['success'=>true,'message'=>'Doctor added','id'=>$newId]);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Exception $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}
