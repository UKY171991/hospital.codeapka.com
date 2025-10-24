<?php
/**
 * Database Migration: Add added_by columns to all tables
 * Run this once to add missing added_by columns
 */

require_once __DIR__ . '/inc/connection.php';

header('Content-Type: application/json');

$tables_to_update = [
    'patients' => 'Patient records',
    'doctors' => 'Doctor records', 
    'tests' => 'Test records',
    'entries' => 'Test entry records',
    'notices' => 'Notice records',
    'owners' => 'Owner records',
    'plans' => 'Plan records'
];

$results = [];

try {
    foreach ($tables_to_update as $table => $description) {
        try {
            // Check if table exists
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() == 0) {
                $results[$table] = [
                    'status' => 'skipped',
                    'message' => 'Table does not exist'
                ];
                continue;
            }
            
            // Check if added_by column exists
            $stmt = $pdo->query("SHOW COLUMNS FROM $table LIKE 'added_by'");
            if ($stmt->rowCount() > 0) {
                $results[$table] = [
                    'status' => 'exists',
                    'message' => 'added_by column already exists'
                ];
                continue;
            }
            
            // Add the column
            $sql = "ALTER TABLE $table ADD COLUMN added_by INT(11) DEFAULT 1 COMMENT 'User who added this record'";
            $pdo->exec($sql);
            
            $results[$table] = [
                'status' => 'added',
                'message' => 'added_by column added successfully'
            ];
            
        } catch (Exception $e) {
            $results[$table] = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database migration completed',
        'results' => $results
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>