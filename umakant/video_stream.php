<?php
// umakant/video_stream.php
// Handles video streaming with Byte-Range support to fix seeking on servers without static file Range support

$file = $_GET['file'] ?? '';

// Basic security sanitization
$file = basename($file);

// Clean up any path traversal attempts just in case basename isn't enough for some edge cases
$file = str_replace(['..', '/', '\\'], '', $file);

if (empty($file)) {
    header("HTTP/1.0 404 Not Found");
    exit('File not specified.');
}

$filePath = 'uploads/videos/' . $file;

if (!file_exists($filePath)) {
    header("HTTP/1.0 404 Not Found");
    exit('File not found.');
}

$size = filesize($filePath);
$length = $size;
$start = 0;
$end = $size - 1;

// Detect Mime Type
$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$mimeType = 'video/mp4'; // Default
if ($extension === 'webm') $mimeType = 'video/webm';
if ($extension === 'ogg') $mimeType = 'video/ogg';
if ($extension === 'mov') $mimeType = 'video/mp4';
if ($extension === 'avi') $mimeType = 'video/x-msvideo';

header("Content-Type: $mimeType");
header("Accept-Ranges: bytes");

if (isset($_SERVER['HTTP_RANGE'])) {
    $c_start = $start;
    $c_end = $end;

    list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
    if (strpos($range, ',') !== false) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes $start-$end/$size");
        exit;
    }
    
    if ($range == '-') {
        $c_start = $size - 1;
    } else {
        $range = explode('-', $range);
        $c_start = $range[0];
        $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size - 1;
    }
    
    $c_end = ($c_end > $end) ? $end : $c_end;
    
    if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes $start-$end/$size");
        exit;
    }
    
    $start = $c_start;
    $end = $c_end;
    $length = $end - $start + 1;
    
    header('HTTP/1.1 206 Partial Content');
}

header("Content-Range: bytes $start-$end/$size");
header("Content-Length: " . $length);

// Clear buffers to prevent memory issues
if (ob_get_level()) ob_end_clean();

$fp = fopen($filePath, 'rb');
fseek($fp, $start);

$buffer = 1024 * 8;
while (!feof($fp) && ($p = ftell($fp)) <= $end) {
    if ($p + $buffer > $end) {
        $buffer = $end - $p + 1;
    }
    set_time_limit(0);
    echo fread($fp, $buffer);
    flush();
}

fclose($fp);
exit;
?>
