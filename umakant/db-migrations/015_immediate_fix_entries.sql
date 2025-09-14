-- IMMEDIATE FIX ALTER SQL - ENTRIES TABLE
-- Run this to fix the "test_date" column error immediately

-- 1. Ensure the entries table exists with correct structure
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

-- 2. Add missing columns if they don't exist
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS entry_date DATETIME DEFAULT NULL,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 3. Ensure proper data types
ALTER TABLE entries 
MODIFY COLUMN entry_date DATETIME DEFAULT NULL,
MODIFY COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- 4. Update NULL entry_date values
UPDATE entries 
SET entry_date = created_at 
WHERE entry_date IS NULL;

-- 5. Add essential indexes
ALTER TABLE entries 
ADD INDEX IF NOT EXISTS idx_entry_date (entry_date),
ADD INDEX IF NOT EXISTS idx_status (status);

-- 6. Verify table structure
DESCRIBE entries;

-- 7. Test the API query
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
