<?php
// inc/connection_sqlite.php - SQLite connection for development
$db_path = __DIR__ . '/../hospital_dev.db';

// Ensure PHP uses India time (IST) globally
if (!ini_get('date.timezone')) {
    date_default_timezone_set('Asia/Kolkata');
}

try {
    $pdo = new PDO("sqlite:$db_path", null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    
    // Create tables if they don't exist
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS patients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            uhid VARCHAR(50) UNIQUE,
            name VARCHAR(255) NOT NULL,
            father_husband VARCHAR(255),
            mobile VARCHAR(20),
            email VARCHAR(255),
            age INTEGER,
            age_unit VARCHAR(10) DEFAULT "Years",
            gender VARCHAR(10),
            address TEXT,
            added_by VARCHAR(255),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    // Insert some sample data if the table is empty
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM patients");
    $count = $stmt->fetch()['count'];
    
    if ($count == 0) {
        $samplePatients = [
            ['P001', 'John Doe', 'Robert Doe', '9876543210', 'john@example.com', 30, 'Years', 'Male', '123 Main St, City'],
            ['P002', 'Jane Smith', 'Mike Smith', '9876543211', 'jane@example.com', 25, 'Years', 'Female', '456 Oak Ave, Town'],
            ['P003', 'Bob Johnson', 'Tom Johnson', '9876543212', 'bob@example.com', 45, 'Years', 'Male', '789 Pine Rd, Village'],
            ['P004', 'Alice Brown', 'David Brown', '9876543213', 'alice@example.com', 35, 'Years', 'Female', '321 Elm St, City'],
            ['P005', 'Charlie Wilson', 'Frank Wilson', '9876543214', 'charlie@example.com', 28, 'Years', 'Male', '654 Maple Dr, Town']
        ];
        
        $stmt = $pdo->prepare('
            INSERT INTO patients (uhid, name, father_husband, mobile, email, age, age_unit, gender, address, added_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        
        foreach ($samplePatients as $patient) {
            $stmt->execute([
                $patient[0], $patient[1], $patient[2], $patient[3], $patient[4],
                $patient[5], $patient[6], $patient[7], $patient[8], 'System'
            ]);
        }
    }
    
} catch (PDOException $e) {
    throw new PDOException("Database connection failed: " . $e->getMessage(), (int)$e->getCode());
}
?>
