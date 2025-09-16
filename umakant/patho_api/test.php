<?php
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
require_once __DIR__ . '/../inc/api_config.php';

echo json_encode(['success' => true, 'message' => 'Test file loaded successfully.']);
exit;
?>