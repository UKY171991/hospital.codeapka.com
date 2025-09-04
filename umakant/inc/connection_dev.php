<?php
// inc/connection_dev.php - Development database configuration
// Use this file for local development

// Check if environment variables are set for database config
$host = $_ENV['DB_HOST'] ?? 'localhost';
$db   = $_ENV['DB_NAME'] ?? 'hospital_local';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$port = $_ENV['DB_PORT'] ?? 3306;
$charset = 'utf8mb4';

// Ensure PHP uses India time (IST) globally
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Asia/Kolkata');
}

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
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
    echo "✓ Development database connected successfully\n";
} catch (PDOException $e) {
    // More detailed error information for development
    header('Content-Type: text/plain; charset=utf-8', true, 500);
    echo "Development Database Connection Failed:\n";
    echo "Host: $host:$port\n";
    echo "Database: $db\n";
    echo "User: $user\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "To fix this issue:\n";
    echo "1. Make sure MySQL/MariaDB is running\n";
    echo "2. Create a database named '$db'\n";
    echo "3. Update the credentials in this file or set environment variables\n";
    echo "4. Import the SQL file: u902379465_hospital.sql\n";
    exit;
}
?>
