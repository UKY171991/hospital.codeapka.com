-- COMPREHENSIVE ALTER SQL - FIX ENTRIES TABLE SCHEMA
-- This script will completely fix the database schema issues

-- Step 1: Check current table structure
DESCRIBE entries;

-- Step 2: Create entries table if it doesn't exist
CREATE TABLE IF NOT EXISTS entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT DEFAULT NULL,
    test_id INT NOT NULL,
    entry_date DATETIME DEFAULT NULL,
    result_value TEXT DEFAULT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    remarks TEXT DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    added_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Step 3: Add missing columns if they don't exist
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS reported_date DATETIME DEFAULT NULL AFTER entry_date,
ADD COLUMN IF NOT EXISTS result_status VARCHAR(20) DEFAULT 'normal' AFTER result_value;

-- Step 4: Ensure all columns have correct data types
ALTER TABLE entries 
MODIFY COLUMN id INT AUTO_INCREMENT,
MODIFY COLUMN patient_id INT NOT NULL,
MODIFY COLUMN doctor_id INT DEFAULT NULL,
MODIFY COLUMN test_id INT NOT NULL,
MODIFY COLUMN entry_date DATETIME DEFAULT NULL,
MODIFY COLUMN result_value TEXT DEFAULT NULL,
MODIFY COLUMN unit VARCHAR(50) DEFAULT NULL,
MODIFY COLUMN remarks TEXT DEFAULT NULL,
MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending',
MODIFY COLUMN added_by INT DEFAULT NULL,
MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
MODIFY COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Step 5: Add essential indexes
ALTER TABLE entries 
ADD INDEX IF NOT EXISTS idx_patient_id (patient_id),
ADD INDEX IF NOT EXISTS idx_test_id (test_id),
ADD INDEX IF NOT EXISTS idx_doctor_id (doctor_id),
ADD INDEX IF NOT EXISTS idx_entry_date (entry_date),
ADD INDEX IF NOT EXISTS idx_status (status),
ADD INDEX IF NOT EXISTS idx_created_at (created_at);

-- Step 6: Update any NULL entry_date values
UPDATE entries 
SET entry_date = COALESCE(entry_date, created_at, NOW()) 
WHERE entry_date IS NULL;

-- Step 7: Add foreign key constraints (if referenced tables exist)
-- Only add these if the referenced tables exist
SET @patients_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'patients' AND table_schema = DATABASE());
SET @tests_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'tests' AND table_schema = DATABASE());
SET @doctors_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'doctors' AND table_schema = DATABASE());
SET @users_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'users' AND table_schema = DATABASE());

-- Add foreign keys only if referenced tables exist
SET @sql = IF(@patients_exists > 0, 
    'ALTER TABLE entries ADD CONSTRAINT IF NOT EXISTS fk_entries_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE', 
    'SELECT "Patients table does not exist, skipping foreign key" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(@tests_exists > 0, 
    'ALTER TABLE entries ADD CONSTRAINT IF NOT EXISTS fk_entries_test FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE', 
    'SELECT "Tests table does not exist, skipping foreign key" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(@doctors_exists > 0, 
    'ALTER TABLE entries ADD CONSTRAINT IF NOT EXISTS fk_entries_doctor FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL', 
    'SELECT "Doctors table does not exist, skipping foreign key" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = IF(@users_exists > 0, 
    'ALTER TABLE entries ADD CONSTRAINT IF NOT EXISTS fk_entries_user FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL', 
    'SELECT "Users table does not exist, skipping foreign key" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 8: Insert sample data if table is empty
INSERT IGNORE INTO entries (patient_id, test_id, doctor_id, entry_date, result_value, unit, remarks, status, added_by)
SELECT 
    COALESCE((SELECT id FROM patients LIMIT 1), 1) as patient_id,
    COALESCE((SELECT id FROM tests LIMIT 1), 1) as test_id,
    COALESCE((SELECT id FROM doctors LIMIT 1), NULL) as doctor_id,
    NOW() as entry_date,
    '95.5' as result_value,
    'mg/dL' as unit,
    'Sample test entry' as remarks,
    'completed' as status,
    1 as added_by
WHERE NOT EXISTS (SELECT 1 FROM entries LIMIT 1);

-- Step 9: Verify the final table structure
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    EXTRA
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'entries' 
AND TABLE_SCHEMA = DATABASE()
ORDER BY ORDINAL_POSITION;

-- Step 10: Test the corrected query
SELECT 
    e.id,
    e.patient_id,
    e.doctor_id,
    e.test_id,
    e.entry_date,
    e.result_value,
    e.unit,
    e.remarks,
    e.status,
    e.added_by,
    e.created_at,
    COALESCE(p.name, 'Unknown Patient') as patient_name,
    COALESCE(t.name, 'Unknown Test') as test_name,
    COALESCE(d.name, 'No Doctor') as doctor_name
FROM entries e 
LEFT JOIN patients p ON e.patient_id = p.id 
LEFT JOIN tests t ON e.test_id = t.id 
LEFT JOIN doctors d ON e.doctor_id = d.id
ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC
LIMIT 5;
