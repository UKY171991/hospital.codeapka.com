<?php
require_once 'connection.php';

try {
    // Check opd_doctors for user_id
    $checkDoctors = $pdo->query("SHOW COLUMNS FROM opd_doctors LIKE 'user_id'");
    if ($checkDoctors->rowCount() == 0) {
        $pdo->exec("ALTER TABLE opd_doctors ADD COLUMN user_id INT NULL AFTER id");
        echo "Added user_id to opd_doctors.<br>";
    } else {
        echo "user_id already exists in opd_doctors.<br>";
    }

    // Check opd_patients for user_id
    $checkPatients = $pdo->query("SHOW COLUMNS FROM opd_patients LIKE 'user_id'");
    if ($checkPatients->rowCount() == 0) {
        $pdo->exec("ALTER TABLE opd_patients ADD COLUMN user_id INT NULL AFTER id");
        echo "Added user_id to opd_patients.<br>";
    } else {
        echo "user_id already exists in opd_patients.<br>";
    }

    echo "Schema update complete.";

} catch (PDOException $e) {
    die("Error updating schema: " . $e->getMessage());
}
?>
