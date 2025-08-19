<?php
// patho_api/entry.php
header('Content-Type: application/json');
require_once '../inc/connection.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List all entries
    $stmt = $pdo->query('SELECT e.*, p.client_name AS patient_name, t.test_name, d.name AS doctor_name FROM entries e
        LEFT JOIN patients p ON e.patient_id = p.id
        LEFT JOIN tests t ON e.test_id = t.id
        LEFT JOIN doctors d ON e.doctor_id = d.id
        ORDER BY e.id DESC');
    $entries = $stmt->fetchAll();
    echo json_encode(['success' => true, 'entries' => $entries]);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $patient_id = (int)($data['patient_id'] ?? 0);
    $doctor_id = isset($data['doctor_id']) ? (int)$data['doctor_id'] : null;
    $test_id = (int)($data['test_id'] ?? 0);
    $entry_date = trim($data['entry_date'] ?? '');
    $result_value = trim($data['result_value'] ?? '');
    $unit = trim($data['unit'] ?? '');
    $remarks = trim($data['remarks'] ?? '');
    $status = trim($data['status'] ?? 'pending');
    $added_by = isset($data['added_by']) ? (int)$data['added_by'] : 0;
    if (!$patient_id || !$test_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Patient and Test are required.']);
        exit;
    }
    // Prevent duplicate entry for same patient, test, date, and user
    $dupStmt = $pdo->prepare('SELECT id FROM entries WHERE patient_id = ? AND test_id = ? AND entry_date = ? AND added_by = ?');
    $dupStmt->execute([$patient_id, $test_id, $entry_date, $added_by]);
    if ($dupStmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Entry already exists for this patient, test, and date.']);
        exit;
    }
    $stmt = $pdo->prepare('INSERT INTO entries (patient_id, doctor_id, test_id, entry_date, result_value, unit, remarks, status, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$patient_id, $doctor_id, $test_id, $entry_date, $result_value, $unit, $remarks, $status, $added_by]);
    echo json_encode(['success' => true, 'message' => 'Entry added successfully.']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
