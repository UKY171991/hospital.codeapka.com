-- URGENT FIX - ENTRIES TABLE SCHEMA
-- This will fix the "test_date" column error immediately

-- Step 1: Check current structure
SELECT 'Current table structure:' as info;
DESCRIBE entries;

-- Step 2: Drop test_date column if it exists (it shouldn't exist)
ALTER TABLE entries DROP COLUMN IF EXISTS test_date;

-- Step 3: Ensure entry_date column exists
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS entry_date DATETIME DEFAULT NULL;

-- Step 4: Ensure created_at column exists
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Step 5: Ensure updated_at column exists
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Step 6: Update any NULL entry_date values
UPDATE entries 
SET entry_date = COALESCE(entry_date, created_at, NOW()) 
WHERE entry_date IS NULL;

-- Step 7: Add indexes for performance
ALTER TABLE entries 
ADD INDEX IF NOT EXISTS idx_entry_date (entry_date),
ADD INDEX IF NOT EXISTS idx_created_at (created_at),
ADD INDEX IF NOT EXISTS idx_status (status);

-- Step 8: Verify the fix
SELECT 'Fixed table structure:' as info;
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
    COALESCE(p.name, 'Unknown Patient') as patient_name,
    COALESCE(t.name, 'Unknown Test') as test_name,
    COALESCE(d.name, 'No Doctor') as doctor_name
FROM entries e 
LEFT JOIN patients p ON e.patient_id = p.id 
LEFT JOIN tests t ON e.test_id = t.id 
LEFT JOIN doctors d ON e.doctor_id = d.id
ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC
LIMIT 3;
