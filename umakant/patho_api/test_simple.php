<?php
// Simple test to debug the issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    require_once __DIR__ . '/../inc/connection.php';
    echo json_encode(['success' => true, 'message' => 'Database connection OK']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>