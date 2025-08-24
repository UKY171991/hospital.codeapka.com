<?php
// List database backups
$backupDir = __DIR__ . '/backups';

echo "<h1>Database Backups</h1>";

if (is_dir($backupDir)) {
    $backups = scandir($backupDir);
    $backups = array_diff($backups, array('.', '..'));
    $backups = array_reverse($backups); // Show newest first
    
    if (count($backups) > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0'>";
        echo "<tr><th>Backup File</th><th>Size</th><th>Date</th><th>Actions</th></tr>";
        
        foreach ($backups as $backup) {
            $filePath = $backupDir . '/' . $backup;
            $size = round(filesize($filePath) / 1024, 2);
            $date = date('Y-m-d H:i:s', filemtime($filePath));
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($backup) . "</td>";
            echo "<td>" . $size . " KB</td>";
            echo "<td>" . $date . "</td>";
            echo "<td><a href='download_backup.php?file=" . urlencode($backup) . "'>Download</a></td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No backups found.</p>";
    }
} else {
    echo "<p>Backups directory does not exist. Run <a href='backup_db.php'>backup_db.php</a> to create a backup.</p>";
}

echo "<p><a href='index.php'>Go to Dashboard</a> | <a href='backup_db.php'>Create Backup</a></p>";
?>