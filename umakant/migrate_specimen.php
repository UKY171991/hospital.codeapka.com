<?php
require_once 'inc/connection.php';

try {
    // Check if specimen column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM tests LIKE 'specimen'");
    $columnExists = $stmt->rowCount() > 0;
    
    if ($columnExists) {
        echo "Specimen column exists, dropping it...\n";
        $pdo->exec("ALTER TABLE tests DROP COLUMN specimen");
        echo "Specimen column dropped successfully.\n";
    } else {
        echo "Specimen column does not exist, no action needed.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
