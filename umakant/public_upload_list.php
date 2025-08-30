<?php
// Public upload list - outputs an HTML fragment (no login required)
$uploadsDir = __DIR__ . '/uploads';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'hospital.codeapka.com') . '/umakant/uploads/';

$files = [];
if (is_dir($uploadsDir)) {
    $it = new DirectoryIterator($uploadsDir);
    foreach ($it as $fileinfo) {
        if ($fileinfo->isDot() || !$fileinfo->isFile()) continue;
        $files[] = [
            'name' => $fileinfo->getFilename(),
            'size' => $fileinfo->getSize(),
            'mtime' => $fileinfo->getMTime(),
        ];
    }
    usort($files, function($a,$b){ return $b['mtime'] <=> $a['mtime']; });
}

if (empty($files)) {
    echo '<div class="small">No releases available.</div>';
    return;
}

echo '<div class="upload-list">';
echo '<table style="width:100%;border-collapse:collapse">';
echo '<thead><tr><th style="text-align:left;padding:8px;border-bottom:1px solid #eee">File</th><th style="padding:8px;border-bottom:1px solid #eee">Size</th><th style="padding:8px;border-bottom:1px solid #eee">Uploaded</th></tr></thead>';
echo '<tbody>';
foreach ($files as $f) {
    $url = $baseUrl . rawurlencode($f['name']);
    $sizeMB = number_format($f['size'] / (1024*1024), 2);
    // Format modification time in Asia/Kolkata (IST) so displayed times match local expectations
    try {
        $dt = new DateTime('@' . $f['mtime']);
        $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
        $time = $dt->format('Y-m-d H:i') . ' IST';
    } catch (Exception $e) {
        $time = date('Y-m-d H:i', $f['mtime']);
    }
    echo '<tr>';
    echo '<td style="padding:8px;border-bottom:1px solid #f1f1f1"><a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($f['name']) . '</a></td>';
    echo '<td style="padding:8px;border-bottom:1px solid #f1f1f1">' . $sizeMB . ' MB</td>';
    echo '<td style="padding:8px;border-bottom:1px solid #f1f1f1">' . htmlspecialchars($time) . '</td>';
    echo '</tr>';
}
echo '</tbody></table>';
echo '</div>';
