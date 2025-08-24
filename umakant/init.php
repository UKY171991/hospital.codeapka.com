<?php
// Initialization script for Pathology Lab Management System

echo "<h1>Pathology Lab Management System - Initialization</h1>";

echo "<h2>Step 1: Checking PHP Version</h2>";
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    echo "<p style='color: green;'>PHP version " . PHP_VERSION . " is compatible.</p>";
} else {
    echo "<p style='color: red;'>PHP version " . PHP_VERSION . " is not compatible. Please upgrade to PHP 7.4 or higher.</p>";
    exit;
}

echo "<h2>Step 2: Checking Required Extensions</h2>";
$required_extensions = ['pdo', 'pdo_sqlite', 'json', 'session'];
$missing_extensions = [];

foreach ($required_extensions as $extension) {
    if (!extension_loaded($extension)) {
        $missing_extensions[] = $extension;
    }
}

if (empty($missing_extensions)) {
    echo "<p style='color: green;'>All required extensions are loaded.</p>";
} else {
    echo "<p style='color: red;'>Missing extensions: " . implode(', ', $missing_extensions) . "</p>";
    exit;
}

echo "<h2>Step 3: Checking File Permissions</h2>";
$writable_dirs = ['inc', 'ajax', 'js', 'css', 'patho_api'];
$not_writable = [];

foreach ($writable_dirs as $dir) {
    if (!is_writable($dir)) {
        $not_writable[] = $dir;
    }
}

if (empty($not_writable)) {
    echo "<p style='color: green;'>All required directories are writable.</p>";
} else {
    echo "<p style='color: red;'>The following directories are not writable: " . implode(', ', $not_writable) . "</p>";
    echo "<p>Please ensure the web server has write permissions to these directories.</p>";
}

echo "<h2>Step 4: Database Configuration</h2>";
echo "<p>The system uses SQLite database which will be created automatically.</p>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Run <a href='create_database.php'>create_database.php</a> to create the database</li>";
echo "<li>Run <a href='setup_database.php'>setup_database.php</a> to create tables and default data</li>";
echo "<li>Access the system via <a href='login.php'>login.php</a> (Default: admin / admin123)</li>";
echo "</ol>";

echo "<p><a href='index.php'>Go to Dashboard</a></p>";
?>