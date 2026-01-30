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

// Clean buffer
while (ob_get_level()) {
    ob_end_clean();
}

header("Content-Type: $type");
header("Accept-Ranges: bytes");

if (isset($_SERVER['HTTP_RANGE'])) {
    $c_start = $start;
    $c_end = $end;

    list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
    
    // Handle multiple ranges (not supported by this simple script)
    if (strpos($range, ',') !== false) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes $start-$end/$size");
        exit;
    }
    
    // Handle suffix range (e.g., bytes=-500)
    if (substr($range, 0, 1) == '-') {
        $c_start = $size - substr($range, 1);
    } else {
        $range_parts = explode('-', $range);
        $c_start = $range_parts[0];
        $c_end = (isset($range_parts[1]) && is_numeric($range_parts[1])) ? $range_parts[1] : $size - 1;
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
    http_response_code(206); // Partial Content
    header("Content-Range: bytes $start-$end/$size");
} else {
    http_response_code(200); // OK
    // Do NOT send Content-Range for 200 OK
}

header("Content-Length: " . $length);

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
