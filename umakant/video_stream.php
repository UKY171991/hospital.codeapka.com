<?php
// umakant/video_stream.php
// Robust chunk-based video streamer for PHP
// Handles partial content (Range requests) reliably on all hosting environments

ini_set('memory_limit', '512M');
@ini_set('zlib.output_compression', 'Off');
@set_time_limit(0);

$file = $_GET['file'] ?? '';
$file = basename($file); // Security: allow only filename, no paths
$file = str_replace(array("\0", "..", "/", "\\"), "", $file); // Extra sanitization

if (empty($file)) {
    die('No file specified');
}

$path = 'uploads/videos/' . $file;

if (!file_exists($path)) {
    header("HTTP/1.1 404 Not Found");
    die('File not found');
}

$fp = @fopen($path, 'rb');
$size = filesize($path);
$length = $size;
$start = 0;
$end = $size - 1;

// Determine Mime Type
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mime_types = [
    'mp4' => 'video/mp4', 'webm' => 'video/webm', 'ogg' => 'video/ogg',
    'ogv' => 'video/ogg', 'mov' => 'video/mp4', 'avi' => 'video/x-msvideo',
    'mkv' => 'video/x-matroska'
];
$mime = $mime_types[$ext] ?? 'application/octet-stream';

// Hande Range Header
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

// Clean any previous output
while (ob_get_level()) ob_end_clean();

// Send Headers
header("Content-Type: $mime");
header("Cache-Control: public, must-revalidate, max-age=0");
header("Accept-Ranges: bytes");
header("Content-Length: $length");
header("Content-Range: bytes $start-$end/$size");
header("Content-Disposition: inline; filename=\"$file\"");
header("Content-Transfer-Encoding: binary");
header("Connection: keep-alive");

// Stream content
$buffer = 1024 * 8;
while (!feof($fp) && ($p = ftell($fp)) <= $end) {
    if ($p + $buffer > $end) {
        $buffer = $end - $p + 1;
    }
    echo fread($fp, $buffer);
    flush();
}

fclose($fp);
exit;
?>
