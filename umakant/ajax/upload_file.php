<?php
// ajax/upload_file.php - handles AJAX file uploads (ZIP or EXE)
// return JSON and avoid accidental HTML from warnings
@ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../inc/connection.php';
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();

try{
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $action = $_REQUEST['action'] ?? ($method === 'GET' ? 'list' : 'upload');

    // LIST action (supports DataTables / client fetch)
    if ($action === 'list') {
        $uploadsDir = realpath(__DIR__ . '/../uploads');
        $rows = [];

        $hasTable = false;
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE 'zip_uploads'");
            $hasTable = $stmt->fetch() ? true : false;
        } catch (Throwable $e) {
            $hasTable = false;
        }

        if ($hasTable) {
            $stmt = $pdo->query("SELECT z.id, z.file_name, z.original_name, z.relative_path, z.mime_type, z.file_size, z.uploaded_by, z.status, z.created_at, u.username AS uploaded_by_username FROM zip_uploads z LEFT JOIN users u ON z.uploaded_by = u.id ORDER BY z.created_at DESC, z.id DESC");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            if ($uploadsDir && is_dir($uploadsDir)) {
                $dir = new DirectoryIterator($uploadsDir);
                foreach ($dir as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        $rows[] = [
                            'id' => null,
                            'file_name' => $fileinfo->getFilename(),
                            'original_name' => $fileinfo->getFilename(),
                            'relative_path' => 'uploads/' . $fileinfo->getFilename(),
                            'mime_type' => mime_content_type($fileinfo->getPathname()) ?: '',
                            'file_size' => $fileinfo->getSize(),
                            'uploaded_by' => null,
                            'uploaded_by_username' => null,
                            'status' => 'uploaded',
                            'created_at' => date('Y-m-d H:i:s', $fileinfo->getMTime())
                        ];
                    }
                }
                // sort newest first
                usort($rows, function($a, $b){
                    return strtotime($b['created_at']) <=> strtotime($a['created_at']);
                });
            }
        }

        echo json_encode([
            'success' => true,
            'data' => $rows
        ]);
        ob_end_flush();
        exit;
    }

    if ($method !== 'POST') throw new Exception('Invalid method');

    // delete action
    if($action === 'delete'){
        if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'master')){
            echo json_encode(['success'=>false,'message'=>'Unauthorized']); ob_end_flush(); exit;
        }
        $file = $_POST['file'] ?? '';
        if(!$file){ echo json_encode(['success'=>false,'message'=>'No file specified']); ob_end_flush(); exit; }
        $safe = basename($file);
        $path = __DIR__ . '/../uploads/' . $safe;
        $deleted = false;
        if(is_file($path)){
            $deleted = unlink($path);
            if(!$deleted){ echo json_encode(['success'=>false,'message'=>'Failed to delete file']); ob_end_flush(); exit; }
        } else {
            // file not present on disk but try DB cleanup and return success=false with message
            try{ $stmt = $pdo->prepare('DELETE FROM zip_uploads WHERE file_name = ? OR relative_path = ?'); $stmt->execute([$safe, 'uploads/'.$safe]); }catch(Throwable $e){}
            echo json_encode(['success'=>false,'message'=>'File not found on server']); ob_end_flush(); exit;
        }
        try{ $stmt = $pdo->prepare('DELETE FROM zip_uploads WHERE file_name = ? OR relative_path = ?'); $stmt->execute([$safe, 'uploads/'.$safe]); }catch(Throwable $e){}
        echo json_encode(['success'=> (bool)$deleted ]);
        ob_end_flush();
        exit;
    }

    if ($action !== 'upload') throw new Exception('Invalid action');

    if (empty($_FILES['file'])) throw new Exception('No file uploaded');

    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Upload error: ' . $file['error']);

    // Basic security validation - block potentially dangerous files
    $name = $file['name'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    
    // Block executable and script files for security
    $blocked = ['php', 'asp', 'jsp', 'cgi', 'pl', 'py', 'rb', 'sh', 'bat', 'cmd', 'com', 'scr', 'vbs', 'js'];
    if (in_array($ext, $blocked)) throw new Exception('File type not allowed for security reasons');
    
    // Additional security check for double extensions
    $nameParts = explode('.', $name);
    if (count($nameParts) > 2) {
        $secondExt = strtolower($nameParts[count($nameParts) - 2]);
        if (in_array($secondExt, $blocked)) throw new Exception('File type not allowed for security reasons');
    }

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

    // Send email notification to all users about new software upload
    try{
        sendUploadNotificationEmails($pdo, $orig, $safeName, $uploaded_by);
    }catch(Throwable $e){
        // ignore email errors; still return success for file upload
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
