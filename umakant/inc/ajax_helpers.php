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
            // Do not allow upsert to change the original creator of the row by default
            if ($col === 'added_by') continue;
            // Normalize null/empty strings
            $e = isset($existing[$col]) ? $existing[$col] : null;
            if ((string)$e !== (string)$val) $changed[$col] = $val;
        }

        // If no other fields changed, consider whether added_by should be set when missing
        if (empty($changed)) {
            if (isset($data['added_by']) && ($data['added_by'] !== null && $data['added_by'] !== '') && (empty($existing['added_by']) || $existing['added_by'] === null || $existing['added_by'] === '')) {
                // Set added_by where it was previously empty
                $up = $pdo->prepare("UPDATE $table SET added_by = ?, updated_at = NOW() WHERE id = ?");
                $up->execute([$data['added_by'], $existing['id']]);
                return ['action'=>'updated','id'=>(int)$existing['id']];
            }
            return ['action'=>'skipped','id'=>(int)$existing['id']];
        }
        // Build UPDATE statement for changed fields
        $setParts = [];
        $setParams = [];
        foreach ($changed as $col => $val) {
            $setParts[] = "$col = ?";
            $setParams[] = $val;
        }

        // If added_by is provided in data and existing added_by is empty, include it in update
        if (isset($data['added_by']) && ($data['added_by'] !== null && $data['added_by'] !== '') && (empty($existing['added_by']) || $existing['added_by'] === null || $existing['added_by'] === '')) {
            $setParts[] = "added_by = ?";
            $setParams[] = $data['added_by'];
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
