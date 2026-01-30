<?php
// umakant/video_stream.php
// Robust video streaming script with strict HTTP compliance for Range Requests

$file = $_GET['file'] ?? '';

// Sanitize filename: Allow alphanumeric, spaces, dots, dashes, underscores, parentheses
$file = basename($file);
$file = str_replace(array("\0", "..", "/", "\\"), "", $file);

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
$length = $size;         // Content length to send
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
    'avi' => 'video/x-msvideo',
    'mkv' => 'video/x-matroska'
];
$type = $mime_types[$extension] ?? 'video/mp4';

// Disable compression/buffering
@ini_set('zlib.output_compression', 'Off'); // Corrected this line
// Clean buffer
while (ob_get_level()) {
    ob_end_clean();
}

$size = filesize($path);
$mime = $mime_types[$extension] ?? 'application/octet-stream';
$filename = basename($path);

header("Content-Type: $mime");
header("Content-Disposition: inline; filename=\"$filename\"");
header("Accept-Ranges: bytes");
header("X-Content-Duration: $size"); // Hint for some players

if (isset($_SERVER['HTTP_RANGE'])) {
    list($unit, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
    
    if ($unit != 'bytes') {
        header("HTTP/1.1 416 Requested Range Not Satisfiable");
        header("Content-Range: bytes */$size");
        exit;
    }
    
    $ranges = explode(',', $range);
    $range = explode('-', $ranges[0]);
    
    $start = (int)$range[0];
    $end = isset($range[1]) && is_numeric($range[1]) ? (int)$range[1] : $size - 1;
    
    if ($start > $end || $start > $size - 1) {
        header("HTTP/1.1 416 Requested Range Not Satisfiable");
        header("Content-Range: bytes */$size");
        exit;
    }
    
    $length = $end - $start + 1;
    
    header("HTTP/1.1 206 Partial Content");
    header("Content-Range: bytes $start-$end/$size");
    header("Content-Length: $length");
    
    $fp = fopen($path, 'rb');
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
} else {
    // Serve full file
    header("HTTP/1.1 200 OK");
    header("Content-Length: $size");
    readfile($path);
}
exit;
?>
