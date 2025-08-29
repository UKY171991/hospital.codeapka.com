<?php
require_once 'inc/connection.php';
require_once 'inc/ajax_helpers.php';

try {
    $stmt = $pdo->query('SELECT COUNT(*) as count FROM tests');
    $result = $stmt->fetch();
    echo 'Total tests in database: ' . $result['count'] . PHP_EOL;
    
    $stmt = $pdo->query('SELECT id, name FROM tests ORDER BY id DESC LIMIT 3');
    $tests = $stmt->fetchAll();
    echo 'Latest 3 tests:' . PHP_EOL;
    foreach ($tests as $test) {
        echo 'ID: ' . $test['id'] . ', Name: ' . $test['name'] . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
