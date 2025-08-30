<?php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

// Detect if `qr_code` column exists in `plans` table to remain backward-compatible
$has_qr = false;
try{
    $colStmt = $pdo->prepare("SELECT COUNT(*) as c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plans' AND COLUMN_NAME = 'qr_code'");
    $colStmt->execute();
    $colRes = $colStmt->fetch();
    if ($colRes && isset($colRes['c']) && intval($colRes['c']) > 0) $has_qr = true;
}catch(Throwable $e){
    // ignore; default to false
}

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list'){
    $cols = 'p.id, p.name, p.description, p.price, p.upi, p.time_type, p.start_date, p.end_date, p.added_by, u.username as added_by_username';
    if($has_qr) $cols = str_replace('p.added_by', 'p.qr_code, p.added_by', $cols);
    $stmt = $pdo->query("SELECT $cols FROM plans p LEFT JOIN users u ON p.added_by = u.id ORDER BY p.id DESC");
    $rows = $stmt->fetchAll();
    json_response(['success'=>true,'data'=>$rows]);
}

if ($action === 'get' && isset($_GET['id'])){
    $cols = 'p.id, p.name, p.description, p.price, p.upi, p.time_type, p.start_date, p.end_date, p.added_by, u.username as added_by_username';
    if($has_qr) $cols = str_replace('p.added_by', 'p.qr_code, p.added_by', $cols);
    $stmt = $pdo->prepare("SELECT $cols FROM plans p LEFT JOIN users u ON p.added_by = u.id WHERE p.id = ?");
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
    // If no file was selected, PHP sets error = UPLOAD_ERR_NO_FILE (4). Treat that as "no file provided".
    if (isset($_FILES['qr_code']) && isset($_FILES['qr_code']['error']) && $_FILES['qr_code']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['qr_code']['error'] !== UPLOAD_ERR_OK) {
            json_response(['success'=>false,'message'=>'QR upload error: ' . $_FILES['qr_code']['error']],400);
        }
        // Basic validation: size (<2MB) and allow common image types
        $maxBytes = 2 * 1024 * 1024;
        if ($_FILES['qr_code']['size'] > $maxBytes) json_response(['success'=>false,'message'=>'QR image too large (max 2MB)'],400);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['qr_code']['tmp_name']);
        finfo_close($finfo);
        $allowed = ['image/png','image/jpeg','image/webp','image/gif'];
        if (!in_array($mime, $allowed)) json_response(['success'=>false,'message'=>'Invalid QR image type'],400);

        $uploadDir = __DIR__ . '/../uploads/qr';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                json_response(['success'=>false,'message'=>'Failed to create upload directory'],500);
            }
        }
        $orig = basename($_FILES['qr_code']['name']);
        $ext = pathinfo($orig, PATHINFO_EXTENSION);
        $safe = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $uploadDir . '/' . $safe;
        if (!move_uploaded_file($_FILES['qr_code']['tmp_name'], $target)) {
            json_response(['success'=>false,'message'=>'Failed to move uploaded QR file'],500);
        }
        $qr_path = 'uploads/qr/' . $safe;
    }
    if ($name === '') json_response(['success'=>false,'message'=>'Name required'],400);
    try{
        if ($id){
            if ($has_qr && $qr_path) {
                $stmt = $pdo->prepare('UPDATE plans SET name=?, description=?, price=?, upi=?, time_type=?, start_date=?, end_date=?, qr_code=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$name,$description,$price,$upi,$time_type,$start,$end,$qr_path,$id]);
            } else {
                $stmt = $pdo->prepare('UPDATE plans SET name=?, description=?, price=?, upi=?, time_type=?, start_date=?, end_date=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$name,$description,$price,$upi,$time_type,$start,$end,$id]);
            }
            json_response(['success'=>true,'message'=>'Plan updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            if($has_qr){
                $stmt = $pdo->prepare('INSERT INTO plans (name,description,price,upi,time_type,start_date,end_date,qr_code,added_by,created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())');
                $stmt->execute([$name,$description,$price,$upi,$time_type,$start,$end,$qr_path,$added_by]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO plans (name,description,price,upi,time_type,start_date,end_date,added_by,created_at) VALUES (?,?,?,?,?,?,?,?,NOW())');
                $stmt->execute([$name,$description,$price,$upi,$time_type,$start,$end,$added_by]);
            }
            json_response(['success'=>true,'message'=>'Plan created']);
        }
    }catch(PDOException $e){ json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500); }
}

if ($action === 'delete' && isset($_POST['id'])){
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    try{ $stmt = $pdo->prepare('DELETE FROM plans WHERE id=?'); $stmt->execute([$_POST['id']]); json_response(['success'=>true,'message'=>'Plan deleted']); } catch(PDOException $e){ json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500); }
}

json_response(['success'=>false,'message'=>'Invalid action'],400);
