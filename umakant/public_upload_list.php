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
    echo '<div class="releases-empty">No releases available at this time.</div>';
    return;
}

echo '<div class="releases-table">';
echo '<div class="releases-table-header">';
echo '<div class="releases-table-row">';
echo '<div class="releases-table-cell header">File</div>';
echo '<div class="releases-table-cell header">Size</div>';
echo '<div class="releases-table-cell header">Uploaded</div>';
echo '</div>';
echo '</div>';

echo '<div class="releases-table-body">';
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
    
    echo '<div class="releases-table-row">';
    echo '<div class="releases-table-cell">';
    echo '<a href="' . htmlspecialchars($url) . '" target="_blank" class="release-link">' . htmlspecialchars($f['name']) . '</a>';
    echo '</div>';
    echo '<div class="releases-table-cell">' . $sizeMB . ' MB</div>';
    echo '<div class="releases-table-cell">' . htmlspecialchars($time) . '</div>';
    echo '</div>';
}
echo '</div>';
echo '</div>';
?>