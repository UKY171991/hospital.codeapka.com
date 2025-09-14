-- ALTER SQL Commands to Fix Entries Table Schema
-- This script will modify the existing entries table to have the correct structure

-- First, let's check the current table structure
DESCRIBE entries;

-- Add missing columns if they don't exist
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS reported_date DATETIME DEFAULT NULL AFTER entry_date,
ADD COLUMN IF NOT EXISTS result_status VARCHAR(20) DEFAULT 'normal' AFTER result_value;

-- Ensure entry_date column exists and is properly configured
ALTER TABLE entries 
MODIFY COLUMN entry_date DATETIME DEFAULT NULL;

-- Ensure created_at column exists and is properly configured
ALTER TABLE entries 
MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Add updated_at column if it doesn't exist
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Ensure all required columns exist with proper data types
ALTER TABLE entries 
MODIFY COLUMN id INT AUTO_INCREMENT PRIMARY KEY,
MODIFY COLUMN patient_id INT NOT NULL,
MODIFY COLUMN doctor_id INT DEFAULT NULL,
MODIFY COLUMN test_id INT NOT NULL,
MODIFY COLUMN result_value TEXT DEFAULT NULL,
MODIFY COLUMN unit VARCHAR(50) DEFAULT NULL,
MODIFY COLUMN remarks TEXT DEFAULT NULL,
MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending',
MODIFY COLUMN added_by INT DEFAULT NULL;

-- Add foreign key constraints if they don't exist
ALTER TABLE entries 
ADD CONSTRAINT IF NOT EXISTS fk_entries_patient 
FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE;

ALTER TABLE entries 
ADD CONSTRAINT IF NOT EXISTS fk_entries_doctor 
FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL;

ALTER TABLE entries 
ADD CONSTRAINT IF NOT EXISTS fk_entries_test 
FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE;

ALTER TABLE entries 
ADD CONSTRAINT IF NOT EXISTS fk_entries_user 
FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL;

-- Add indexes for better performance
ALTER TABLE entries 
ADD INDEX IF NOT EXISTS idx_patient_id (patient_id),
ADD INDEX IF NOT EXISTS idx_test_id (test_id),
ADD INDEX IF NOT EXISTS idx_doctor_id (doctor_id),
ADD INDEX IF NOT EXISTS idx_entry_date (entry_date),
ADD INDEX IF NOT EXISTS idx_status (status),
ADD INDEX IF NOT EXISTS idx_created_at (created_at);

-- Update any NULL entry_date values to use created_at
UPDATE entries 
SET entry_date = created_at 
WHERE entry_date IS NULL AND created_at IS NOT NULL;

-- Set default entry_date for any remaining NULL values
UPDATE entries 
SET entry_date = NOW() 
WHERE entry_date IS NULL;

-- Verify the final table structure
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

-- Test the corrected query that the API will use
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
    p.name as patient_name,
    t.name as test_name,
    d.name as doctor_name
FROM entries e 
LEFT JOIN patients p ON e.patient_id = p.id 
LEFT JOIN tests t ON e.test_id = t.id 
LEFT JOIN doctors d ON e.doctor_id = d.id
ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC
LIMIT 5;
