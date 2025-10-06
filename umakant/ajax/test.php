<?php
require_once __DIR__ . '/../inc/connection.php';

function db_table_exists($pdo, $table) {
    try {
        $stmt = $pdo->prepare('SHOW TABLES LIKE ?');
        $stmt->execute([$table]);
        return $stmt->fetch(PDO::FETCH_NUM) ? true : false;
    } catch (Exception $e) {
        return false;
    }
}

$tableExists = db_table_exists($pdo, 'entry_tests');

if ($tableExists) {
    echo "The 'entry_tests' table exists.";
} else {
    echo "The 'entry_tests' table does not exist.";
}
