<?php
// patho_api/test_category.php
header('Content-Type: application/json');
require_once '../inc/connection.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List all test categories
    $stmt = $pdo->query('SELECT id, name, description FROM test_categories ORDER BY name');
    $categories = $stmt->fetchAll();
    echo json_encode(['success' => true, 'categories' => $categories]);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $description = trim($data['description'] ?? '');
    $added_by = isset($data['added_by']) ? (int)$data['added_by'] : 0;
    if (!$name) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Name is required.']);
        exit;
    }
    $stmt = $pdo->prepare('INSERT INTO test_categories (name, description, added_by) VALUES (?, ?, ?)');
    $stmt->execute([$name, $description, $added_by]);
    echo json_encode(['success' => true, 'message' => 'Test category added successfully.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
