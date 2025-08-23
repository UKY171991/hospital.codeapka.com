<?php
require_once 'inc/connection.php';

// Create tables
$tables = [];

// Users table
$tables[] = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    account_expires DATE
)";

// Doctors table
$tables[] = "CREATE TABLE IF NOT EXISTS doctors (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Patients table
$tables[] = "CREATE TABLE IF NOT EXISTS patients (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    uhid VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    age INT(3),
    gender ENUM('Male', 'Female', 'Other'),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Test categories table
$tables[] = "CREATE TABLE IF NOT EXISTS test_categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Tests table
$tables[] = "CREATE TABLE IF NOT EXISTS tests (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    category_id INT(11),
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) DEFAULT 0.00,
    normal_range VARCHAR(100),
    unit VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES test_categories(id)
)";

// Test entries table
$tables[] = "CREATE TABLE IF NOT EXISTS entries (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_id INT(11),
    doctor_id INT(11),
    test_id INT(11),
    referring_doctor VARCHAR(100),
    entry_date DATE,
    result TEXT,
    status ENUM('Pending', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id),
    FOREIGN KEY (test_id) REFERENCES tests(id)
)";

$success = 0;
$errors = [];

foreach ($tables as $sql) {
    if ($conn->query($sql) === TRUE) {
        $success++;
    } else {
        $errors[] = $conn->error;
    }
}

// Insert default admin user
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$adminQuery = "INSERT IGNORE INTO users (username, email, full_name, password, role, is_active, account_expires) 
               VALUES ('admin', 'admin@pathologylab.com', 'Administrator', '$adminPassword', 'admin', 1, DATE_ADD(NOW(), INTERVAL 365 DAY))";

if ($conn->query($adminQuery) === TRUE) {
    echo "Default admin user created successfully<br>";
} else {
    echo "Error creating admin user: " . $conn->error . "<br>";
}

$conn->close();

echo "<h2>Database Setup Results</h2>";
echo "<p>Successfully created $success tables</p>";

if (!empty($errors)) {
    echo "<h3>Errors:</h3>";
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
} else {
    echo "<p>All tables created successfully!</p>";
}

echo "<p><a href='login.php'>Go to Login</a></p>";
?>