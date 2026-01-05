<?php
$host = 'localhost';
$db   = 'u902379465_hospital';
$user = 'u902379465_hospital';
$pass = '8+B^YVnd';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass);

$tables = ['doctors','patients','owners','notices','plans','entries','tests','users','categories', 'test_categories', 'zip_uploads', 'opd_doctors', 'opd_patients', 'opd_medical_records', 'opd_billing', 'opd_appointments', 'entry_list', 'test_category', 'clients', 'emails', 'inventory_income', 'inventory_expense'];

foreach($tables as $t) {
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$t` LIKE 'added_by'");
        if($stmt && $stmt->fetch()) {
            echo "$t\n";
        }
    } catch(Exception $e) {
        // Table might not exist
    }
}
