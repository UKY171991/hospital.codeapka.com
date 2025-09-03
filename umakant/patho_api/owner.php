<?php
// patho_api/owner.php - public API for owners (JSON)
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

$action = $_REQUEST['action'] ?? 'list';

try {
    if ($action === 'list') {
        $stmt = $pdo->query('SELECT o.id, o.name, o.phone, o.whatsapp, o.email, o.address, o.added_by, u.username as added_by_username FROM owners o LEFT JOIN users u ON o.added_by = u.id ORDER BY o.id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success' => true, 'data' => $rows]);
    }

    if ($action === 'get' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT o.*, u.username as added_by_username FROM owners o LEFT JOIN users u ON o.added_by = u.id WHERE o.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Owner not found'],404);
        json_response(['success' => true, 'data' => $row]);
    }

    if ($action === 'save') {
        // only admin/master may write
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);

        $id = $_POST['id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($name === '') json_response(['success'=>false,'message'=>'Name is required'],400);

        if ($id) {
            $stmt = $pdo->prepare('UPDATE owners SET name=?, phone=?, whatsapp=?, email=?, address=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name, $phone, $whatsapp, $email, $address, $id]);
            json_response(['success' => true, 'message' => 'Owner updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            if (empty($added_by) && !empty($PATHO_API_DEFAULT_USER_ID)) {
                $added_by = (int)$PATHO_API_DEFAULT_USER_ID;
            }
            $data = ['name'=>$name, 'phone'=>$phone, 'whatsapp'=>$whatsapp, 'email'=>$email, 'address'=>$address, 'added_by'=>$added_by];
            // Unique by phone if present, else email, else name
            if ($phone !== '') $unique = ['phone'=>$phone];
            elseif ($email !== '') $unique = ['email'=>$email];
            else $unique = ['name'=>$name];
            $res = upsert_or_skip($pdo, 'owners', $unique, $data);
            json_response(['success'=>true,'message'=>'Owner '.$res['action'],'id'=>$res['id']]);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM owners WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success' => true, 'message' => 'Owner deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
} catch (Throwable $e) {
    json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500);
}

