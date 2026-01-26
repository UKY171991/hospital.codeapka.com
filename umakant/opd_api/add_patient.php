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
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate required fields
    if (empty($patientName) || empty($patientPhone) || empty($patientAge) || empty($patientGender)) {
        json_response(['success' => false, 'message' => 'Missing required fields'], 400);
    }
    
    if (empty($username)) {
        json_response(['success' => false, 'message' => 'Username is required'], 400);
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
    
    // Check if username exists
    $userCheck = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $userCheck->execute([$username]);
    if ($userCheck->fetch()) {
         json_response(['success' => false, 'message' => 'Username already exists'], 400);
    }

    // Ensure user_id column exists
    $checkUserId = $pdo->query("SHOW COLUMNS FROM opd_patients LIKE 'user_id'");
    if ($checkUserId->rowCount() == 0) {
        $pdo->exec("ALTER TABLE opd_patients ADD COLUMN user_id INT NULL AFTER id");
    }

    $pdo->beginTransaction();
    
    try {
        // Create User Account
        $tempPass = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : password_hash('password123', PASSWORD_DEFAULT);
        $insertUser = $pdo->prepare("INSERT INTO users (username, password, full_name, role, email, is_active, created_at) VALUES (?, ?, ?, 'patient', ?, 1, NOW())");
        $insertUser->execute([$username, $tempPass, $patientName, $patientEmail]);
        $user_id = $pdo->lastInsertId();

        // Generate unique patient_id
        $patientId = 'PAT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

        // Normalize gender to lowercase
        $genderNormalized = strtolower($patientGender);

        // Insert new patient
        $insertStmt = $pdo->prepare("
            INSERT INTO opd_patients (user_id, patient_id, name, phone, dob, gender, email, address, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $insertStmt->execute([
            $user_id,
            $patientId,
            $patientName,
            $patientPhone,
            $dob,
            $genderNormalized,
            $patientEmail,
            $patientAddress
        ]);
        
        $newPatientId = $pdo->lastInsertId();
        
        $pdo->commit();
        
        json_response([
            'success' => true,
            'message' => 'Patient added successfully with login access',
            'data' => ['id' => $newPatientId, 'patient_id' => $patientId]
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
} catch (Throwable $t) {
    json_response(['success' => false, 'message' => 'Server error: ' . $t->getMessage()], 500);
}
