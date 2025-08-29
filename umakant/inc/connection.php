<?php
// inc/connection.php
// Database connection settings (configured for remote phpMyAdmin DB)
$host = 'localhost';
$db   = 'u902379465_hospital';
$user = 'u902379465_hospital';
$pass = '8+B^YVnd';
$charset = 'utf8mb4';

// Ensure PHP uses India time (IST) globally
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Asia/Kolkata');
}

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // Ensure MySQL session uses IST (+05:30) so NOW()/CURRENT_TIMESTAMP align with PHP dates
    try {
        $pdo->exec("SET time_zone = '+05:30'");
    } catch (Throwable $tzErr) {
        // ignore if timezone cannot be set (missing timezone tables); PHP timezone is still set
    }
} catch (PDOException $e) {
    // In production you might want a cleaner error page
    header('Content-Type: text/plain; charset=utf-8', true, 500);
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
