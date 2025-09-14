-- Quick Fix ALTER SQL - Immediate Solution
-- This addresses the specific "test_date" column error

-- Check current table structure
DESCRIBE entries;

-- Ensure entry_date column exists (this is the correct column name)
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS entry_date DATETIME DEFAULT NULL;

-- Ensure created_at column exists
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Update any NULL entry_date values to use created_at
UPDATE entries 
SET entry_date = created_at 
WHERE entry_date IS NULL;

-- Add index on entry_date for better performance
ALTER TABLE entries 
ADD INDEX IF NOT EXISTS idx_entry_date (entry_date);

-- Test the query that was failing
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
