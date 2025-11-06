<?php
// Diagnose test data issues
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/simple_auth.php';

header('Content-Type: application/json; charset=utf-8');

// Start session and authenticate
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_data = simpleAuthenticate($pdo);
if (!$user_data) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

try {
    $diagnosis = [];
    
    // Check tables existence
    $tables = ['entries', 'entry_tests', 'tests', 'categories'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $diagnosis['tables'][$table] = $stmt->fetch() ? 'exists' : 'missing';
    }
    
    // Check entries table structure
    if ($diagnosis['tables']['entries'] === 'exists') {
        $stmt = $pdo->query("DESCRIBE entries");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $diagnosis['entries_columns'] = $columns;
        
        // Check for test-related columns
        $testColumns = ['tests_count', 'test_names', 'test_ids', 'grouped'];
        foreach ($testColumns as $col) {
            $diagnosis['entries_has'][$col] = in_array($col, $columns);
        }
    }
    
    // Count records
    if ($diagnosis['tables']['entries'] === 'exists') {
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries");
        $diagnosis['counts']['entries'] = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE tests_count > 0");
        $diagnosis['counts']['entries_with_tests'] = $stmt->fetchColumn();
    }
    
    if ($diagnosis['tables']['entry_tests'] === 'exists') {
        $stmt = $pdo->query("SELECT COUNT(*) FROM entry_tests");
        $diagnosis['counts']['entry_tests'] = $stmt->fetchColumn();
    }
    
    if ($diagnosis['tables']['tests'] === 'exists') {
        $stmt = $pdo->query("SELECT COUNT(*) FROM tests");
        $diagnosis['counts']['tests'] = $stmt->fetchColumn();
    }
    
    // Sample data
    if ($diagnosis['tables']['entries'] === 'exists') {
        $stmt = $pdo->query("SELECT id, patient_id, tests_count, test_names, test_ids FROM entries ORDER BY id DESC LIMIT 5");
        $diagnosis['sample_entries'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    if ($diagnosis['tables']['entry_tests'] === 'exists' && $diagnosis['counts']['entry_tests'] > 0) {
        $stmt = $pdo->query("SELECT * FROM entry_tests LIMIT 5");
        $diagnosis['sample_entry_tests'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Check for orphaned data
    if ($diagnosis['tables']['entry_tests'] === 'exists' && $diagnosis['tables']['entries'] === 'exists') {
        $stmt = $pdo->query("
            SELECT COUNT(*) 
            FROM entry_tests et 
            LEFT JOIN entries e ON et.entry_id = e.id 
            WHERE e.id IS NULL
        ");
        $diagnosis['orphaned_entry_tests'] = $stmt->fetchColumn();
    }
    
    echo json_encode([
        'success' => true,
        'diagnosis' => $diagnosis
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error during diagnosis: ' . $e->getMessage()
    ]);
}
?>