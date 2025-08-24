<?php
// Database restore script
if (isset($_GET['file'])) {
    $backupFile = basename($_GET['file']);
    $backupPath = __DIR__ . '/backups/' . $backupFile;
    $dbPath = __DIR__ . '/pathology_lab.db';
    
    echo "<h1>Database Restore</h1>";
    
    // Security check: ensure file exists and is in the backups directory
    if (file_exists($backupPath) && strpos(realpath($backupPath), realpath(__DIR__ . '/backups')) === 0) {
        // Create a backup of the current database before restoring
        $currentBackup = __DIR__ . '/backups/pathology_lab_current_backup_' . date('Y-m-d_H-i-s') . '.db';
        if (copy($dbPath, $currentBackup)) {
            echo "<p>✓ Current database backed up to: " . basename($currentBackup) . "</p>";
        }
        
        // Restore the database
        if (copy($backupPath, $dbPath)) {
            echo "<p>✓ Database restored successfully from: " . htmlspecialchars($backupFile) . "</p>";
        } else {
            echo "<p>✗ Failed to restore database</p>";
        }
    } else {
        echo "<p>✗ Backup file not found or invalid</p>";
    }
} else {
    echo "<h1>Database Restore</h1>";
    echo "<p>No backup file specified. Please use the <a href='list_backups.php'>backup list</a> to select a file to restore.</p>";
}

echo "<p><a href='index.php'>Go to Dashboard</a> | <a href='list_backups.php'>List Backups</a></p>";
?>