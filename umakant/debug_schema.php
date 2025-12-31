<?php
require_once 'inc/connection.php';
try {
    echo "ENTRIES TABLE:\n";
    $stmt = $pdo->query("DESCRIBE entries");
    while($row = $stmt->fetch()) {
        print_r($row);
    }
    echo "\nENTRY_TESTS TABLE:\n";
    $stmt = $pdo->query("DESCRIBE entry_tests");
    while($row = $stmt->fetch()) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
