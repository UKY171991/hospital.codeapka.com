<?php
// opd_api/debug_schema.php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

try {
    $tables = ['opd_patients', 'opd_doctors'];
    $schema = [];

    foreach ($tables as $table) {
        $stmt = $pdo->query("DESCRIBE $table");
        $schema[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    json_response(['success' => true, 'schema' => $schema]);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => $t->getMessage()]);
}
