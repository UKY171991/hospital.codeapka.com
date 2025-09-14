-- Essential ALTER SQL Commands for Entries Table
-- Run these commands to fix the database schema issues

-- 1. Ensure entry_date column exists and is properly configured
ALTER TABLE entries 
MODIFY COLUMN entry_date DATETIME DEFAULT NULL;

-- 2. Ensure created_at column exists and is properly configured  
ALTER TABLE entries 
MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- 3. Add updated_at column if it doesn't exist
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 4. Add missing columns that might be referenced
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS reported_date DATETIME DEFAULT NULL AFTER entry_date,
ADD COLUMN IF NOT EXISTS result_status VARCHAR(20) DEFAULT 'normal' AFTER result_value;

-- 5. Ensure all columns have proper data types
ALTER TABLE entries 
MODIFY COLUMN id INT AUTO_INCREMENT,
MODIFY COLUMN patient_id INT NOT NULL,
MODIFY COLUMN doctor_id INT DEFAULT NULL,
MODIFY COLUMN test_id INT NOT NULL,
MODIFY COLUMN result_value TEXT DEFAULT NULL,
MODIFY COLUMN unit VARCHAR(50) DEFAULT NULL,
MODIFY COLUMN remarks TEXT DEFAULT NULL,
MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending',
MODIFY COLUMN added_by INT DEFAULT NULL;

-- 6. Update any NULL entry_date values
UPDATE entries 
SET entry_date = COALESCE(entry_date, created_at, NOW()) 
WHERE entry_date IS NULL;

-- 7. Add essential indexes
ALTER TABLE entries 
ADD INDEX IF NOT EXISTS idx_entry_date (entry_date),
ADD INDEX IF NOT EXISTS idx_status (status),
ADD INDEX IF NOT EXISTS idx_patient_id (patient_id),
ADD INDEX IF NOT EXISTS idx_test_id (test_id);

-- 8. Verify the table structure
DESCRIBE entries;
