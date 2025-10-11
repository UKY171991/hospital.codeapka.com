<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=u902379465_hospital;charset=utf8mb4', 'u902379465_hospital', '8+B^YVnd');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query('DESCRIBE users');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo 'Users table structure:' . PHP_EOL;
    foreach ($columns as $column) {
        echo $column['Field'] . ' - ' . $column['Type'] . ' - ' . ($column['Null'] == 'NO' ? 'NOT NULL' : 'NULL') . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
