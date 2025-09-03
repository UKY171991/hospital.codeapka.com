<?php
// inc/ajax_helpers.php
function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Find existing row by unique criteria, compare with provided data, and either skip, update or insert.
 *
 * @param PDO $pdo
 * @param string $table
 * @param array $uniqueWhere associative column=>value used to find existing row
 * @param array $data associative column=>value to insert/update
 * @return array ['action'=>'skipped'|'updated'|'inserted', 'id'=>int|null]
 */
function upsert_or_skip($pdo, $table, $uniqueWhere, $data) {
    // Build WHERE clause and params
    $whereParts = [];
    $params = [];
    foreach ($uniqueWhere as $col => $val) {
        $whereParts[] = "$col = ?";
        $params[] = $val;
    }
    $whereSql = implode(' AND ', $whereParts);

    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $whereSql LIMIT 1");
    $stmt->execute($params);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // Compare fields - if all provided data fields are identical to existing, skip
        $changed = [];
        foreach ($data as $col => $val) {
            // Normalize null/empty strings
            $e = isset($existing[$col]) ? $existing[$col] : null;
            if ((string)$e !== (string)$val) $changed[$col] = $val;
        }
        if (empty($changed)) {
            return ['action'=>'skipped','id'=>(int)$existing['id']];
        }
        // Build UPDATE statement for changed fields
        $setParts = [];
        $setParams = [];
        foreach ($changed as $col => $val) {
            $setParts[] = "$col = ?";
            $setParams[] = $val;
        }
        $setSql = implode(', ', $setParts);
        $setParams[] = $existing['id'];
        $up = $pdo->prepare("UPDATE $table SET $setSql, updated_at = NOW() WHERE id = ?");
        $up->execute($setParams);
        return ['action'=>'updated','id'=>(int)$existing['id']];
    }

    // Insert new row - build columns and placeholders
    $cols = array_keys($data);
    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
    $colSql = implode(', ', $cols);
    $ins = $pdo->prepare("INSERT INTO $table ($colSql, created_at) VALUES ($placeholders, NOW())");
    $ins->execute(array_values($data));
    return ['action'=>'inserted','id'=> (int)$pdo->lastInsertId()];
}
