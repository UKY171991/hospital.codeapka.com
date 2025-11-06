<?php
// Refresh test aggregates for all entries
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
    // Check if entry_tests table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'entry_tests'");
    $entryTestsExists = $stmt->fetch() ? true : false;
    
    if (!$entryTestsExists) {
        echo json_encode([
            'success' => false, 
            'message' => 'entry_tests table does not exist. Test data cannot be aggregated.',
            'debug' => [
                'entry_tests_exists' => false
            ]
        ]);
        exit;
    }
    
    // Get all entries
    $stmt = $pdo->query("SELECT id FROM entries ORDER BY id");
    $entries = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $updated = 0;
    $errors = [];
    
    foreach ($entries as $entryId) {
        try {
            // Get test data for this entry
            $testStmt = $pdo->prepare("
                SELECT et.test_id, et.price, t.name as test_name 
                FROM entry_tests et 
                LEFT JOIN tests t ON et.test_id = t.id 
                WHERE et.entry_id = ? 
                ORDER BY t.name
            ");
            $testStmt->execute([$entryId]);
            $tests = $testStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $testsCount = count($tests);
            $testIds = array_column($tests, 'test_id');
            $testNames = array_filter(array_column($tests, 'test_name'));
            $totalPrice = array_sum(array_column($tests, 'price'));
            
            // Update entry with aggregated data
            $updateStmt = $pdo->prepare("
                UPDATE entries 
                SET tests_count = ?, 
                    test_ids = ?, 
                    test_names = ?, 
                    total_price = ?,
                    grouped = ?
                WHERE id = ?
            ");
            
            $result = $updateStmt->execute([
                $testsCount,
                implode(',', $testIds),
                implode(', ', $testNames),
                $totalPrice,
                $testsCount > 1 ? 1 : 0,
                $entryId
            ]);
            
            if ($result) {
                $updated++;
            }
            
        } catch (Exception $e) {
            $errors[] = "Entry $entryId: " . $e->getMessage();
        }
    }
    
    // Get summary statistics
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries");
    $totalEntries = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM entries WHERE tests_count > 0");
    $entriesWithTests = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM entry_tests");
    $totalEntryTests = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'message' => "Successfully refreshed aggregates for $updated entries",
        'stats' => [
            'total_entries' => $totalEntries,
            'entries_with_tests' => $entriesWithTests,
            'total_entry_tests' => $totalEntryTests,
            'updated_entries' => $updated,
            'errors_count' => count($errors)
        ],
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error refreshing aggregates: ' . $e->getMessage()
    ]);
}
?>