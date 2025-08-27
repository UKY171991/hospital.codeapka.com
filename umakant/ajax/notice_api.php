<?php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

try{
    if ($action === 'list'){
        $stmt = $pdo->query('SELECT n.*, u.username as added_by_username FROM notices n LEFT JOIN users u ON n.added_by = u.id ORDER BY n.id DESC');
        $rows = $stmt->fetchAll();
        json_response(['success'=>true,'data'=>$rows]);
    }

    if ($action === 'get' && isset($_GET['id'])){
        $stmt = $pdo->prepare('SELECT n.*, u.username as added_by_username FROM notices n LEFT JOIN users u ON n.added_by = u.id WHERE n.id = ?');
        $stmt->execute([$_GET['id']]);
        $row = $stmt->fetch();
        if (!$row) json_response(['success'=>false,'message'=>'Not found'],404);
        json_response(['success'=>true,'data'=>$row]);
    }

    if ($action === 'save'){
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $id = $_POST['id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $start = $_POST['start_date'] ?? null;
        $end = $_POST['end_date'] ?? null;
        $active = isset($_POST['active']) ? (int)$_POST['active'] : 0;
        if ($title === '') json_response(['success'=>false,'message'=>'Title required'],400);
        if ($id){
            $stmt = $pdo->prepare('UPDATE notices SET title=?, content=?, start_date=?, end_date=?, active=?, updated_at=NOW() WHERE id=?');
            $stmt->execute([$title,$content,$start,$end,$active,$id]);
            json_response(['success'=>true,'message'=>'Notice updated']);
        } else {
            $added_by = $_SESSION['user_id'] ?? null;
            $stmt = $pdo->prepare('INSERT INTO notices (title,content,start_date,end_date,active,added_by,created_at) VALUES (?,?,?,?,?,?,NOW())');
            $stmt->execute([$title,$content,$start,$end,$active,$added_by]);
            json_response(['success'=>true,'message'=>'Notice created']);
        }
    }

    if ($action === 'delete' && isset($_POST['id'])){
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) json_response(['success'=>false,'message'=>'Unauthorized'],401);
        $stmt = $pdo->prepare('DELETE FROM notices WHERE id = ?');
        $stmt->execute([$_POST['id']]);
        json_response(['success'=>true,'message'=>'Notice deleted']);
    }

    json_response(['success'=>false,'message'=>'Invalid action'],400);
}catch(Throwable $e){ json_response(['success'=>false,'message'=>'Server error: '.$e->getMessage()],500); }
