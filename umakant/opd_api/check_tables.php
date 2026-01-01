<?php
// opd_api/check_tables.php - Check if OPD tables exist
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

try {
    $requiredTables = [
        'opd_users',
        'opd_patients',
        'opd_doctors',
        'opd_departments',
        'opd_specializations',
        'opd_appointments',
        'opd_appointment_types',
        'opd_facilities',
        'opd_medical_records',
        'opd_prescriptions',
        'opd_reports',
        'opd_billing'
    ];
    
    $existingTables = [];
    $missingTables = [];
    
    foreach ($requiredTables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $existingTables[] = $table;
        } else {
            $missingTables[] = $table;
        }
    }
    
    json_response([
        'success' => true,
        'existing_tables' => $existingTables,
        'missing_tables' => $missingTables,
        'all_tables_exist' => empty($missingTables),
        'message' => empty($missingTables) ? 
            'All required tables exist' : 
            'Missing tables: ' . implode(', ', $missingTables) . '. Please run the SQL schema to create them.'
    ]);
} catch (Throwable $t) {
    json_response([
        'success' => false,
        'message' => 'Error checking tables: ' . $t->getMessage()
    ], 500);
}
