<?php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list'){
    $stmt = $pdo->query('SELECT o.id, o.name, o.phone, o.whatsapp, o.email, o.address, o.link, o.added_by, u.username as added_by_username FROM owners o LEFT JOIN users u ON o.added_by = u.id ORDER BY o.id DESC');
    $rows = $stmt->fetchAll();
    json_response(['success'=>true,'data'=>$rows]);
}

if ($action === 'get' && isset($_GET['id'])){
    // Return owner details and resolve added_by to a friendly username when available
    $stmt = $pdo->prepare('SELECT o.*, u.username as added_by_username FROM owners o LEFT JOIN users u ON o.added_by = u.id WHERE o.id = ?');
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    json_response(['success'=>true,'data'=>$row]);
}

if ($action === 'save'){
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $link = trim($_POST['link'] ?? '');
    if ($name === '') json_response(['success'=>false,'message'=>'Name required'],400);
    try{
        if ($id){
            $stmt = $pdo->prepare('UPDATE owners SET name=?, phone=?, whatsapp=?, email=?, address=?, link=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name,$phone,$whatsapp,$email,$address,$link,$id]);
            json_response(['success'=>true,'message'=>'Owner updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            $stmt = $pdo->prepare('INSERT INTO owners (name,phone,whatsapp,email,address,link,added_by,created_at) VALUES (?,?,?,?,?,?,?,NOW())');
            $stmt->execute([$name,$phone,$whatsapp,$email,$address,$link,$added_by]);
            json_response(['success'=>true,'message'=>'Owner created']);
        }
    }catch(PDOException $e){ json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500); }
}

if ($action === 'delete' && isset($_POST['id'])){
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    try{ $stmt = $pdo->prepare('DELETE FROM owners WHERE id=?'); $stmt->execute([$_POST['id']]); json_response(['success'=>true,'message'=>'Owner deleted']); } catch(PDOException $e){ json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500); }
}

json_response(['success'=>false,'message'=>'Invalid action'],400);
