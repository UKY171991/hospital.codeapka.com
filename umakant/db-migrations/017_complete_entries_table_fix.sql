-- COMPREHENSIVE ALTER SQL - ENTRIES TABLE
-- Based on the SQL file structure from u902379465_hospital.sql
-- This will ensure the entries table matches the exact structure from the SQL file

-- Step 1: Check current table structure
SELECT 'Current table structure:' as info;
DESCRIBE entries;

-- Step 2: Drop test_date column if it exists (it shouldn't exist)
ALTER TABLE entries DROP COLUMN IF EXISTS test_date;

-- Step 3: Ensure all columns exist with correct structure
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS id INT(11) NOT NULL AUTO_INCREMENT,
ADD COLUMN IF NOT EXISTS patient_id INT(11) NOT NULL,
ADD COLUMN IF NOT EXISTS doctor_id INT(11) NOT NULL,
ADD COLUMN IF NOT EXISTS test_id INT(11) NOT NULL,
ADD COLUMN IF NOT EXISTS entry_date DATETIME NOT NULL,
ADD COLUMN IF NOT EXISTS result_value TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS unit VARCHAR(50) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS remarks TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS status ENUM('pending','completed','failed') DEFAULT 'pending',
ADD COLUMN IF NOT EXISTS added_by INT(11) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS grouped TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS tests_count INT(11) DEFAULT 1,
ADD COLUMN IF NOT EXISTS test_ids LONGTEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS test_names LONGTEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS test_results LONGTEXT DEFAULT NULL;

-- Step 4: Ensure proper data types and constraints
ALTER TABLE entries 
MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT,
MODIFY COLUMN patient_id INT(11) NOT NULL,
MODIFY COLUMN doctor_id INT(11) NOT NULL,
MODIFY COLUMN test_id INT(11) NOT NULL,
MODIFY COLUMN entry_date DATETIME NOT NULL,
MODIFY COLUMN result_value TEXT DEFAULT NULL,
MODIFY COLUMN unit VARCHAR(50) DEFAULT NULL,
MODIFY COLUMN remarks TEXT DEFAULT NULL,
MODIFY COLUMN status ENUM('pending','completed','failed') DEFAULT 'pending',
MODIFY COLUMN added_by INT(11) DEFAULT NULL,
MODIFY COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
MODIFY COLUMN grouped TINYINT(1) DEFAULT 0,
MODIFY COLUMN tests_count INT(11) DEFAULT 1,
MODIFY COLUMN test_ids LONGTEXT DEFAULT NULL,
MODIFY COLUMN test_names LONGTEXT DEFAULT NULL,
MODIFY COLUMN test_results LONGTEXT DEFAULT NULL;

-- Step 5: Update any NULL entry_date values
UPDATE entries 
SET entry_date = COALESCE(entry_date, created_at, NOW()) 
WHERE entry_date IS NULL;

-- Step 6: Add essential indexes
ALTER TABLE entries 
ADD INDEX IF NOT EXISTS idx_patient_id (patient_id),
ADD INDEX IF NOT EXISTS idx_doctor_id (doctor_id),
ADD INDEX IF NOT EXISTS idx_test_id (test_id),
ADD INDEX IF NOT EXISTS idx_entry_date (entry_date),
ADD INDEX IF NOT EXISTS idx_status (status),
ADD INDEX IF NOT EXISTS idx_created_at (created_at),
ADD INDEX IF NOT EXISTS idx_added_by (added_by);

-- Step 7: Add primary key if it doesn't exist
ALTER TABLE entries 
ADD PRIMARY KEY IF NOT EXISTS (id);

-- Step 8: Verify the final table structure
SELECT 'Final table structure:' as info;
DESCRIBE entries;

-- Step 9: Test the API query
SELECT 'Testing API query:' as info;
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
    e.grouped,
    e.tests_count,
    e.test_ids,
    e.test_names,
    e.test_results,
    COALESCE(p.name, 'Unknown Patient') as patient_name,
    COALESCE(t.name, 'Unknown Test') as test_name,
    COALESCE(d.name, 'No Doctor') as doctor_name
FROM entries e 
LEFT JOIN patients p ON e.patient_id = p.id 
LEFT JOIN tests t ON e.test_id = t.id 
LEFT JOIN doctors d ON e.doctor_id = d.id
ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC
LIMIT 3;
