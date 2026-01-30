<?php
// umakant/video_stream.php
// Robust video streaming script with support for Range requests (seeking)

$file = $_GET['file'] ?? '';

// Basic security: Allow only alphanumeric, dots, dashes, underscores
$file = preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file));

if (empty($file)) {
    http_response_code(400);
    exit('File not specified.');
}

$path = 'uploads/videos/' . $file;

if (!file_exists($path)) {
    http_response_code(404);
    exit('File not found.');
}

$fp = @fopen($path, 'rb');
$size = filesize($path); // File size
$length = $size;         // Content length
$start = 0;              // Start byte
$end = $size - 1;        // End byte

// Determine MIME type
$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mime_types = [
    'mp4' => 'video/mp4',
    'webm' => 'video/webm',
    'ogg' => 'video/ogg',
    'ogv' => 'video/ogg',
    'mov' => 'video/mp4',
    'avi' => 'video/x-msvideo'
];
$type = $mime_types[$extension] ?? 'video/mp4';

header("Content-Type: $type");
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
    
    fseek($fp, $start);
    header('HTTP/1.1 206 Partial Content');
}

header("Content-Range: bytes $start-$end/$size");
header("Content-Length: " . $length);

// Disable output buffering to ensure streaming works
while (ob_get_level()) {
    ob_end_clean();
}

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
