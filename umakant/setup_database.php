<?php
// setup_database.php
// Run this file once to set up the database tables

require_once 'inc/connection.php';

echo "<h2>Setting up database tables...</h2>";

try {
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        full_name VARCHAR(100),
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        added_by INT
    )");
    echo "<p>✓ Users table created/verified</p>";

    // Create doctors table
    $pdo->exec("CREATE TABLE IF NOT EXISTS doctors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        qualification VARCHAR(100),
        specialization VARCHAR(100),
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT,
        registration_no VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        added_by INT
    )");
    echo "<p>✓ Doctors table created/verified</p>";

    // Create patients table
    $pdo->exec("CREATE TABLE IF NOT EXISTS patients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(100) NOT NULL,
        mobile_number VARCHAR(20),
        father_or_husband VARCHAR(100),
        address TEXT,
        gender ENUM('Male', 'Female', 'Other'),
        age INT,
        age_unit ENUM('Years', 'Months') DEFAULT 'Years',
        uhid VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        added_by INT
    )");
    echo "<p>✓ Patients table created/verified</p>";

    // Create test_categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS test_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        added_by INT
    )");
    echo "<p>✓ Test categories table created/verified</p>";

    // Create tests table
    $pdo->exec("CREATE TABLE IF NOT EXISTS tests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        test_name VARCHAR(100) NOT NULL,
        category VARCHAR(100),
        description TEXT,
        price DECIMAL(10,2),
        unit VARCHAR(50),
        reference_range VARCHAR(100),
        min_value DECIMAL(10,2),
        max_value DECIMAL(10,2),
        method VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        added_by INT
    )");
    echo "<p>✓ Tests table created/verified</p>";

    // Create entries table
    $pdo->exec("CREATE TABLE IF NOT EXISTS entries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        doctor_id INT,
        test_id INT,
        entry_date DATETIME,
        result_value VARCHAR(100),
        unit VARCHAR(50),
        remarks TEXT,
        status ENUM('pending', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        added_by INT
    )");
    echo "<p>✓ Entries table created/verified</p>";

    // Insert default admin user if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, full_name, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@example.com', 'Administrator', $hash, 'admin']);
        echo "<p>✓ Default admin user created (username: admin, password: admin123)</p>";
    } else {
        echo "<p>✓ Admin user already exists</p>";
    }

    echo "<h3>Database setup completed successfully!</h3>";
    echo "<p><a href='login.php'>Go to Login</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error setting up database: " . $e->getMessage() . "</p>";
}
?>
