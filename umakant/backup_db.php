<?php
// Database backup script
$dbPath = __DIR__ . '/pathology_lab.db';
$backupPath = __DIR__ . '/backups/pathology_lab_backup_' . date('Y-m-d_H-i-s') . '.db';

echo "<h1>Database Backup</h1>";

// Create backups directory if it doesn't exist
if (!is_dir(__DIR__ . '/backups')) {
    if (mkdir(__DIR__ . '/backups', 0755, true)) {
        echo "<p>✓ Backups directory created</p>";
    } else {
        echo "<p>✗ Failed to create backups directory</p>";
        exit;
    }
}

if (file_exists($dbPath)) {
    if (copy($dbPath, $backupPath)) {
        echo "<p>✓ Database backed up successfully</p>";
        echo "<p>Backup location: " . $backupPath . "</p>";
        echo "<p>Backup size: " . round(filesize($backupPath) / 1024, 2) . " KB</p>";
    } else {
        echo "<p>✗ Failed to backup database</p>";
    }
} else {
    echo "<p>✗ Database file not found</p>";
}

echo "<p><a href='index.php'>Go to Dashboard</a></p>";
?>