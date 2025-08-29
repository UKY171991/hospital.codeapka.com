<?php
// ajax/upload_file.php - handles AJAX file uploads (ZIP or EXE)
// return JSON and avoid accidental HTML from warnings
@ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../inc/connection.php';
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();

try{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid method');

    // delete action
    if(isset($_POST['action']) && $_POST['action'] === 'delete'){
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')) throw new Exception('Unauthorized');
        $file = $_POST['file'] ?? '';
        if(!$file) throw new Exception('No file specified');
        $safe = basename($file);
        $path = __DIR__ . '/../uploads/' . $safe;
        $deleted = false;
        if(is_file($path)){
            $deleted = unlink($path);
        }
        try{ $stmt = $pdo->prepare('DELETE FROM zip_uploads WHERE file_name = ? OR relative_path = ?'); $stmt->execute([$safe, 'uploads/'.$safe]); }catch(Throwable $e){}
    echo json_encode(['success'=> (bool)$deleted ]);
    ob_end_flush();
    exit;
    }

    if (empty($_FILES['file'])) throw new Exception('No file uploaded');

    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Upload error: ' . $file['error']);

    $allowed = ['zip','exe'];
    $name = $file['name'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) throw new Exception('Only ZIP or EXE files allowed');

    // size limit (example 100MB)
    $maxSize = 100 * 1024 * 1024;
    if ($file['size'] > $maxSize) throw new Exception('File too large (max 100MB)');

    $uploadDir = __DIR__ . '/../uploads';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // generate a unique filename to avoid collisions
    $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($name));
    $target = $uploadDir . '/' . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $target)) throw new Exception('Failed to move uploaded file');

    // optional: record in DB if you have zip_uploads table
    $relative = 'uploads/' . $safeName;
    $orig = $name;
    $size = $file['size'];
    $mime = mime_content_type($target) ?: '';
    $uploaded_by = $_SESSION['user_id'] ?? null;

    // attempt to insert metadata if table exists
    try{
        $stmt = $pdo->prepare('INSERT INTO zip_uploads (file_name, original_name, relative_path, mime_type, file_size, uploaded_by, status) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([$safeName, $orig, $relative, $mime, $size, $uploaded_by, 'uploaded']);
        $insertId = $pdo->lastInsertId();
    }catch(Throwable $e){
        // ignore DB errors; still return success for file upload
    }

    echo json_encode(['success'=>true,'file_name'=>$safeName,'original_name'=>$orig,'relative_path'=>$relative]);
    ob_end_flush();
    exit;
} catch(Throwable $e){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
    ob_end_flush();
    exit;
}
