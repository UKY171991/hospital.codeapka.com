<?php
// opd_api/add_patient.php - Add new OPD patient
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';
session_start();

try {
    // Check if user is authenticated
    if (!isset($_SESSION['user_id'])) {
        json_response(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    // Get POST data
    $patientName = $_POST['patientName'] ?? '';
    $patientPhone = $_POST['patientPhone'] ?? '';
    $patientAge = $_POST['patientAge'] ?? '';
    $patientGender = $_POST['patientGender'] ?? '';
    $patientEmail = $_POST['patientEmail'] ?? '';
    $patientAddress = $_POST['patientAddress'] ?? '';

    // Validate required fields
    if (empty($patientName) || empty($patientPhone) || empty($patientAge) || empty($patientGender)) {
        json_response(['success' => false, 'message' => 'Missing required fields'], 400);
    }

    // Validate age is numeric
    if (!is_numeric($patientAge) || $patientAge < 0 || $patientAge > 150) {
        json_response(['success' => false, 'message' => 'Invalid age'], 400);
    }

    // Calculate DOB from age
    $dob = date('Y-m-d', strtotime("-$patientAge years"));

    // Check if patient already exists
    $checkStmt = $pdo->prepare("SELECT id FROM opd_patients WHERE phone = ?");
    $checkStmt->execute([$patientPhone]);
    if ($checkStmt->rowCount() > 0) {
        json_response(['success' => false, 'message' => 'Patient with this phone number already exists'], 400);
    }

    // Insert new patient
    $insertStmt = $pdo->prepare("
        INSERT INTO opd_patients (name, phone, dob, gender, email, address, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    $result = $insertStmt->execute([
        $patientName,
        $patientPhone,
        $dob,
        $patientGender,
        $patientEmail,
        $patientAddress
    ]);

    if ($result) {
        $patientId = $pdo->lastInsertId();
        json_response([
            'success' => true,
            'message' => 'Patient added successfully',
            'data' => ['id' => $patientId]
        ]);
    } else {
        json_response(['success' => false, 'message' => 'Failed to add patient'], 500);
    }

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
