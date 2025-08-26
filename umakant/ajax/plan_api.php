<?php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

if ($action === 'list'){
    $stmt = $pdo->query('SELECT p.id, p.name, p.description, p.start_date, p.end_date, p.added_by, u.username as added_by_username FROM plans p LEFT JOIN users u ON p.added_by = u.id ORDER BY p.id DESC');
    $rows = $stmt->fetchAll();
    json_response(['success'=>true,'data'=>$rows]);
}

if ($action === 'get' && isset($_GET['id'])){
    $stmt = $pdo->prepare('SELECT * FROM plans WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch();
    json_response(['success'=>true,'data'=>$row]);
}

if ($action === 'save'){
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start = $_POST['start_date'] ?? null;
    $end = $_POST['end_date'] ?? null;
    if ($name === '') json_response(['success'=>false,'message'=>'Name required'],400);
    try{
        if ($id){
            $stmt = $pdo->prepare('UPDATE plans SET name=?, description=?, start_date=?, end_date=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$name,$description,$start,$end,$id]);
            json_response(['success'=>true,'message'=>'Plan updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            $stmt = $pdo->prepare('INSERT INTO plans (name,description,start_date,end_date,added_by,created_at) VALUES (?,?,?,?,?,NOW())');
            $stmt->execute([$name,$description,$start,$end,$added_by]);
            json_response(['success'=>true,'message'=>'Plan created']);
        }
    }catch(PDOException $e){ json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500); }
}

if ($action === 'delete' && isset($_POST['id'])){
    if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
    try{ $stmt = $pdo->prepare('DELETE FROM plans WHERE id=?'); $stmt->execute([$_POST['id']]); json_response(['success'=>true,'message'=>'Plan deleted']); } catch(PDOException $e){ json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500); }
}

json_response(['success'=>false,'message'=>'Invalid action'],400);
