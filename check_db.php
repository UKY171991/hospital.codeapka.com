<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=u902379465_hospital;charset=utf8mb4', 'u902379465_hospital', '8+B^YVnd');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Available tables: ' . implode(', ', $tables);
} catch (Exception $e) {
    echo 'Database error: ' . $e->getMessage();
}
?>
