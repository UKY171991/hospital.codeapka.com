<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user = 'u902379465_hospital';
$pass = '8+B^YVnd';
$db   = 'u902379465_hospital';
$charset = 'utf8mb4';

$hosts = ['localhost', '127.0.0.1', '::1'];
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

echo "Starting DB Connection Tests...\n";

foreach ($hosts as $test_host) {
    echo "Testing host: '$test_host' ... ";
    try {
        $dsn = "mysql:host=$test_host;dbname=$db;charset=$charset";
        $pdo = new PDO($dsn, $user, $pass, $options);
        echo "SUCCESS!\n";
        
        // Attempt a simple query
        $stmt = $pdo->query("SELECT 1");
        echo "  Query test: " . ($stmt ? "OK" : "Failed") . "\n";
        
    } catch (PDOException $e) {
        echo "FAILED. Error: " . $e->getMessage() . "\n";
    }
}
echo "Tests completed.\n";
?>
