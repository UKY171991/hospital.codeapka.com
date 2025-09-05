<?php
// Test script to debug patient API issues
require_once '../inc/connection.php';

header('Content-Type: application/json');

try {
    // Test database connection
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Check if patients table exists and get its structure
    $stmt = $pdo->query("DESCRIBE patients");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get basic patient count
    $stmt = $pdo->query("SELECT COUNT(*) FROM patients");
    $count = $stmt->fetchColumn();
    
    // Get sample patient data (first 2 records)
    $stmt = $pdo->query("SELECT * FROM patients LIMIT 2");
    $sampleData = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'database_connected' => true,
        'table_columns' => $columns,
        'patient_count' => $count,
        'sample_data' => $sampleData
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => __FILE__,
        'line' => __LINE__
    ]);
}
?>
