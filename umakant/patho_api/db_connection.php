<?php
// Database configuration
define('DB_PATH', __DIR__ . '/../pathology_lab.db');

// Create connection
try {
    $conn = new PDO("sqlite:" . DB_PATH);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>