-- SQL Fix for Entry API - Database Schema Correction
-- This script ensures the entries table has the correct column structure

-- Check if entries table exists and has correct columns
DESCRIBE entries;

-- If the table doesn't exist, create it with the correct structure
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes for better performance
    INDEX idx_patient_id (patient_id),
    INDEX idx_test_id (test_id),
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_entry_date (entry_date),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Add missing columns if they don't exist
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS reported_date DATETIME DEFAULT NULL AFTER entry_date,
ADD COLUMN IF NOT EXISTS result_status VARCHAR(20) DEFAULT 'normal' AFTER result_value;

-- Update any NULL entry_date values to use created_at
UPDATE entries 
SET entry_date = created_at 
WHERE entry_date IS NULL;

-- Sample data insertion (only if table is empty)
INSERT IGNORE INTO entries (patient_id, test_id, doctor_id, entry_date, result_value, unit, remarks, status, added_by)
SELECT 
    (SELECT id FROM patients LIMIT 1) as patient_id,
    (SELECT id FROM tests LIMIT 1) as test_id,
    (SELECT id FROM doctors LIMIT 1) as doctor_id,
    NOW() as entry_date,
    '95.5' as result_value,
    'mg/dL' as unit,
    'Sample test entry' as remarks,
    'completed' as status,
    1 as added_by
WHERE NOT EXISTS (SELECT 1 FROM entries LIMIT 1);

-- Verify the table structure
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'entries' 
AND TABLE_SCHEMA = DATABASE()
ORDER BY ORDINAL_POSITION;

-- Test query to ensure the API will work
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
