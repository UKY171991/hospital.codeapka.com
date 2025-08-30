<?php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list'){
    $stmt = $pdo->query('SELECT p.id, p.name, p.description, p.price, p.upi, p.time_type, p.start_date, p.end_date, p.qr_code, p.added_by, u.username as added_by_username FROM plans p LEFT JOIN users u ON p.added_by = u.id ORDER BY p.id DESC');
    $rows = $stmt->fetchAll();
    json_response(['success'=>true,'data'=>$rows]);
}

if ($action === 'get' && isset($_GET['id'])){
    $stmt = $pdo->prepare('SELECT p.id, p.name, p.description, p.price, p.upi, p.time_type, p.start_date, p.end_date, p.qr_code, p.added_by, u.username as added_by_username FROM plans p LEFT JOIN users u ON p.added_by = u.id WHERE p.id = ?');
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    json_response(['success'=>true,'data'=>$row]);
}

if ($action === 'save'){
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $upi = trim($_POST['upi'] ?? '');
    $time_type = $_POST['time_type'] ?? 'monthly';
    // normalize time_type to 'monthly' or 'yearly' to match DB conventions
    $tt = strtolower(trim($time_type));
    if (strpos($tt, 'year') !== false) $time_type = 'yearly';
    else $time_type = 'monthly';
    $start = $_POST['start_date'] ?? null;
    $end = $_POST['end_date'] ?? null;
    // handle qr_code upload if provided
    $qr_path = null;
    if (!empty($_FILES['qr_code']) && $_FILES['qr_code']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/qr';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $orig = basename($_FILES['qr_code']['name']);
        $ext = pathinfo($orig, PATHINFO_EXTENSION);
        $safe = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $uploadDir . '/' . $safe;
        if (move_uploaded_file($_FILES['qr_code']['tmp_name'], $target)) {
            $qr_path = 'uploads/qr/' . $safe;
        }
    }
    if ($name === '') json_response(['success'=>false,'message'=>'Name required'],400);
    try{
        if ($id){
            if ($qr_path) {
                $stmt = $pdo->prepare('UPDATE plans SET name=?, description=?, price=?, upi=?, time_type=?, start_date=?, end_date=?, qr_code=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$name,$description,$price,$upi,$time_type,$start,$end,$qr_path,$id]);
            } else {
                $stmt = $pdo->prepare('UPDATE plans SET name=?, description=?, price=?, upi=?, time_type=?, start_date=?, end_date=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$name,$description,$price,$upi,$time_type,$start,$end,$id]);
            }
            json_response(['success'=>true,'message'=>'Plan updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            $stmt = $pdo->prepare('INSERT INTO plans (name,description,price,upi,time_type,start_date,end_date,qr_code,added_by,created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())');
            $stmt->execute([$name,$description,$price,$upi,$time_type,$start,$end,$qr_path,$added_by]);
            json_response(['success'=>true,'message'=>'Plan created']);
        }
    }catch(PDOException $e){ json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500); }
}

if ($action === 'delete' && isset($_POST['id'])){
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    try{ $stmt = $pdo->prepare('DELETE FROM plans WHERE id=?'); $stmt->execute([$_POST['id']]); json_response(['success'=>true,'message'=>'Plan deleted']); } catch(PDOException $e){ json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500); }
}

json_response(['success'=>false,'message'=>'Invalid action'],400);
