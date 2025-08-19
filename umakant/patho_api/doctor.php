<?php
// patho_api/doctor.php
header('Content-Type: application/json');
require_once '../inc/connection.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List all doctors
    $stmt = $pdo->query('SELECT id, name, qualification, specialization, phone, email, address FROM doctors ORDER BY name');
    $doctors = $stmt->fetchAll();
    echo json_encode(['success' => true, 'doctors' => $doctors]);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $qualification = trim($data['qualification'] ?? '');
    $specialization = trim($data['specialization'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $email = trim($data['email'] ?? '');
    $address = trim($data['address'] ?? '');
    $registration_no = trim($data['registration_no'] ?? '');
    $added_by = isset($data['added_by']) ? (int)$data['added_by'] : 0;
    if (!$name) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Name is required.']);
        exit;
    }
    $stmt = $pdo->prepare('INSERT INTO doctors (name, qualification, specialization, phone, email, address, registration_no, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $qualification, $specialization, $phone, $email, $address, $registration_no, $added_by]);
    echo json_encode(['success' => true, 'message' => 'Doctor added successfully.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
