<?php
// opd_api/setup_tables.php - Create all required OPD tables
require_once __DIR__ . '/../inc/connection.php';
require_once __DIR__ . '/../inc/ajax_helpers.php';

try {
    $tables = [];

    // 1. opd_reports
    $tables['opd_reports'] = "CREATE TABLE IF NOT EXISTS opd_reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_name VARCHAR(255) NOT NULL,
        patient_phone VARCHAR(50),
        patient_age INT,
        patient_gender VARCHAR(20),
        doctor_name VARCHAR(255),
        report_date DATE,
        diagnosis TEXT,
        symptoms TEXT,
        test_results TEXT,
        prescription TEXT,
        follow_up_date DATE,
        notes TEXT,
        added_by INT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    // 2. opd_billing
    $tables['opd_billing'] = "CREATE TABLE IF NOT EXISTS opd_billing (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_name VARCHAR(255) NOT NULL,
        patient_phone VARCHAR(50),
        patient_age INT,
        patient_gender VARCHAR(20),
        doctor_name VARCHAR(255),
        consultation_fee DECIMAL(10,2) DEFAULT 0.00,
        medicine_charges DECIMAL(10,2) DEFAULT 0.00,
        lab_charges DECIMAL(10,2) DEFAULT 0.00,
        other_charges DECIMAL(10,2) DEFAULT 0.00,
        discount DECIMAL(10,2) DEFAULT 0.00,
        total_amount DECIMAL(10,2) DEFAULT 0.00,
        paid_amount DECIMAL(10,2) DEFAULT 0.00,
        balance_amount DECIMAL(10,2) DEFAULT 0.00,
        payment_method VARCHAR(50) DEFAULT 'Cash',
        payment_status VARCHAR(50) DEFAULT 'Unpaid',
        bill_date DATE,
        notes TEXT,
        added_by INT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    // 3. opd_patients
    $tables['opd_patients'] = "CREATE TABLE IF NOT EXISTS opd_patients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        age INT,
        gender VARCHAR(20),
        address TEXT,
        blood_group VARCHAR(10),
        known_allergies TEXT,
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY idx_name (name),
        KEY idx_phone (phone)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    // 4. opd_doctors (ensure it exists)
    $tables['opd_doctors'] = "CREATE TABLE IF NOT EXISTS opd_doctors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        qualification VARCHAR(255),
        specialization VARCHAR(255),
        hospital VARCHAR(255),
        contact_no VARCHAR(50),
        phone VARCHAR(50),
        email VARCHAR(100),
        address TEXT,
        registration_no VARCHAR(100),
        status VARCHAR(20) DEFAULT 'Active',
        is_active TINYINT(1) DEFAULT 1,
        added_by INT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $results = [];

    foreach ($tables as $name => $sql) {
        try {
            $pdo->exec($sql);
            $results[$name] = 'Created/Exists';
        } catch (PDOException $e) {
            $results[$name] = 'Error: ' . $e->getMessage();
        }
    }

    json_response([
        'success' => true,
        'message' => 'Table setup completed',
        'results' => $results
    ]);

} catch (Throwable $t) {
    json_response([
        'success' => false,
        'message' => 'Critical Error: ' . $t->getMessage()
    ], 500);
}
