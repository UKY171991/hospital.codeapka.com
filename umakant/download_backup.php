<?php
// Download backup file
if (isset($_GET['file'])) {
    $filename = basename($_GET['file']);
    $filepath = __DIR__ . '/backups/' . $filename;
    
    // Security check: ensure file exists and is in the backups directory
    if (file_exists($filepath) && strpos(realpath($filepath), realpath(__DIR__ . '/backups')) === 0) {
        // Set headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        
        // Output file contents
        readfile($filepath);
        exit;
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "File not found.";
        exit;
    }
} else {
    header("HTTP/1.0 400 Bad Request");
    echo "No file specified.";
    exit;
}
?>