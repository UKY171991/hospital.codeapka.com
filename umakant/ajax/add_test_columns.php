<?php
// Add missing test aggregation columns to entries table
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
    $results = [];
    
    // Check which columns are missing
    $stmt = $pdo->query("DESCRIBE entries");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredColumns = [
        'tests_count' => 'INT DEFAULT 0',
        'test_names' => 'TEXT',
        'test_ids' => 'TEXT', 
        'grouped' => 'TINYINT(1) DEFAULT 0'
    ];
    
    $columnsAdded = [];
    $columnsSkipped = [];
    
    foreach ($requiredColumns as $column => $definition) {
        if (!in_array($column, $existingColumns)) {
            try {
                $sql = "ALTER TABLE entries ADD COLUMN `$column` $definition";
                $pdo->exec($sql);
                $columnsAdded[] = $column;
                $results[] = "Added column: $column";
            } catch (Exception $e) {
                $results[] = "Failed to add column $column: " . $e->getMessage();
            }
        } else {
            $columnsSkipped[] = $column;
            $results[] = "Column already exists: $column";
        }
    }
    
    // Now refresh aggregates for all entries
    if (!empty($columnsAdded)) {
        $results[] = "Refreshing aggregates for all entries...";
        
        // Get all entries
        $stmt = $pdo->query("SELECT id FROM entries ORDER BY id");
        $entries = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $updated = 0;
        
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
                        grouped = ?
                    WHERE id = ?
                ");
                
                $result = $updateStmt->execute([
                    $testsCount,
                    implode(',', $testIds),
                    implode(', ', $testNames),
                    $testsCount > 1 ? 1 : 0,
                    $entryId
                ]);
                
                if ($result) {
                    $updated++;
                }
                
            } catch (Exception $e) {
                $results[] = "Error updating entry $entryId: " . $e->getMessage();
            }
        }
        
        $results[] = "Updated aggregates for $updated entries";
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Database schema updated successfully',
        'results' => $results,
        'columns_added' => $columnsAdded,
        'columns_skipped' => $columnsSkipped
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating database schema: ' . $e->getMessage()
    ]);
}
?>