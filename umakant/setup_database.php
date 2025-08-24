<?php
// Database configuration
define('DB_PATH', __DIR__ . '/pathology_lab.db');

// Create connection
try {
    $conn = new PDO("sqlite:" . DB_PATH);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

echo "<h1>Setting up Pathology Lab Database</h1>";

// Enable foreign key constraints
$conn->exec("PRAGMA foreign_keys = ON");

// Create tables according to the provided database structure
$tables = [];

// Users table (Version 6)
$tables[] = "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    full_name TEXT NOT NULL,
    email TEXT,
    role TEXT NOT NULL DEFAULT 'user',
    is_active INTEGER NOT NULL DEFAULT 1,
    created_at TEXT DEFAULT (datetime('now')),
    last_login TEXT,
    expire_date TEXT
)";

// Create indexes for users table
$tables[] = "CREATE INDEX IF NOT EXISTS idx_users_username ON users (username)";
$tables[] = "CREATE INDEX IF NOT EXISTS idx_users_role ON users (role)";

// Categories table (Version 1)
$tables[] = "CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    added_by INTEGER
)";

// Doctors table (Version 4)
$tables[] = "CREATE TABLE IF NOT EXISTS doctors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    qualification TEXT,
    specialization TEXT,
    hospital TEXT,
    contact_no TEXT,
    phone TEXT,
    email TEXT,
    address TEXT,
    registration_no TEXT,
    percent REAL NOT NULL DEFAULT 0,
    added_by INTEGER
)";

// Create indexes for doctors table
$tables[] = "CREATE INDEX IF NOT EXISTS idx_doctors_name ON doctors (name)";
$tables[] = "CREATE INDEX IF NOT EXISTS idx_doctors_contact ON doctors (contact_no)";
$tables[] = "CREATE INDEX IF NOT EXISTS idx_doctors_email ON doctors (email)";

// Patients table (Version 3)
$tables[] = "CREATE TABLE IF NOT EXISTS patients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    mobile TEXT NOT NULL,
    father_husband TEXT,
    address TEXT,
    sex TEXT,
    age INTEGER,
    age_unit TEXT,
    uhid TEXT,
    created_at TEXT DEFAULT (datetime('now')),
    added_by INTEGER
)";

// Create indexes for patients table
$tables[] = "CREATE INDEX IF NOT EXISTS idx_patients_name ON patients (name)";
$tables[] = "CREATE INDEX IF NOT EXISTS idx_patients_mobile ON patients (mobile)";

// Tests table (Version 2)
$tables[] = "CREATE TABLE IF NOT EXISTS tests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category_id INTEGER,
    price REAL NOT NULL DEFAULT 0,
    unit TEXT,
    specimen TEXT,
    default_result TEXT,
    reference_range TEXT,
    min REAL,
    max REAL,
    sub_heading INTEGER NOT NULL DEFAULT 0,
    test_code TEXT,
    method TEXT,
    print_new_page INTEGER NOT NULL DEFAULT 0,
    shortcut TEXT,
    added_by INTEGER
)";

// Reports table (Version 5, rebuilt in Version 15)
$tables[] = "CREATE TABLE IF NOT EXISTS reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    data TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    added_by INTEGER
)";

// Create indexes for reports table
$tables[] = "CREATE INDEX IF NOT EXISTS idx_reports_created_at ON reports (created_at)";
$tables[] = "CREATE INDEX IF NOT EXISTS idx_reports_added_by ON reports (added_by)";

// Entries table (Version 11)
$tables[] = "CREATE TABLE IF NOT EXISTS entries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_id INTEGER,
    doctor_id INTEGER,
    test_id INTEGER,
    entry_date TEXT,
    result_value TEXT,
    unit TEXT,
    remarks TEXT,
    status TEXT DEFAULT 'pending',
    added_by INTEGER,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
)";

// Create indexes for entries table
$tables[] = "CREATE INDEX IF NOT EXISTS idx_entries_patient ON entries (patient_id)";
$tables[] = "CREATE INDEX IF NOT EXISTS idx_entries_doctor ON entries (doctor_id)";
$tables[] = "CREATE INDEX IF NOT EXISTS idx_entries_test ON entries (test_id)";
$tables[] = "CREATE INDEX IF NOT EXISTS idx_entries_added_by ON entries (added_by)";
$tables[] = "CREATE INDEX IF NOT EXISTS idx_entries_date ON entries (entry_date)";

$success = 0;
$errors = [];

foreach ($tables as $sql) {
    try {
        $conn->exec($sql);
        $success++;
    } catch (PDOException $e) {
        $errors[] = $e->getMessage();
    }
}

// Insert default admin user
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$adminQuery = "INSERT OR IGNORE INTO users (username, password, full_name, role, is_active, expire_date) 
               VALUES ('admin', '$adminPassword', 'Administrator', 'admin', 1, datetime('now', '+1 year'))";

try {
    $conn->exec($adminQuery);
    echo "Default admin user created successfully<br>";
} catch (PDOException $e) {
    echo "Error creating admin user: " . $e->getMessage() . "<br>";
}

$conn = null;

echo "<h2>Database Setup Results</h2>";
echo "<p>Successfully created $success tables and indexes</p>";

if (!empty($errors)) {
    echo "<h3>Errors:</h3>";
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
} else {
    echo "<p>All tables and indexes created successfully!</p>";
}

echo "<p><a href='login.php'>Go to Login</a></p>";
?>