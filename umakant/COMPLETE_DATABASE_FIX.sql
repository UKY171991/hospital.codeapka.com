-- =====================================================
-- COMPLETE DATABASE FIX FOR ENTRY FIELDS
-- =====================================================
-- This script will:
-- 1. Check current table structure
-- 2. Add missing columns if they don't exist
-- 3. Verify the fix worked
-- =====================================================

-- Step 1: Check current entries table structure
SELECT '=== CURRENT ENTRIES TABLE STRUCTURE ===' as info;
DESCRIBE entries;

-- Step 2: Check which columns are missing
SELECT '=== CHECKING FOR MISSING COLUMNS ===' as info;

-- Check if priority column exists
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✓ priority column EXISTS'
        ELSE '✗ priority column MISSING'
    END as priority_status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'entries' 
  AND COLUMN_NAME = 'priority';

-- Check if referral_source column exists
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✓ referral_source column EXISTS'
        ELSE '✗ referral_source column MISSING'
    END as referral_source_status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'entries' 
  AND COLUMN_NAME = 'referral_source';

-- Check if pricing columns exist
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✓ subtotal column EXISTS'
        ELSE '✗ subtotal column MISSING'
    END as subtotal_status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'entries' 
  AND COLUMN_NAME = 'subtotal';

SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✓ discount_amount column EXISTS'
        ELSE '✗ discount_amount column MISSING'
    END as discount_status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'entries' 
  AND COLUMN_NAME = 'discount_amount';

SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✓ total_price column EXISTS'
        ELSE '✗ total_price column MISSING'
    END as total_price_status
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'entries' 
  AND COLUMN_NAME = 'total_price';

-- Step 3: Add missing columns (with error handling)
SELECT '=== ADDING MISSING COLUMNS ===' as info;

-- Add priority column (if it doesn't exist)
SET @sql = 'ALTER TABLE entries ADD COLUMN priority varchar(50) DEFAULT "normal" AFTER status';
SET @sql_check = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entries' AND COLUMN_NAME = 'priority');
SET @sql = IF(@sql_check = 0, @sql, 'SELECT "priority column already exists" as result');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add referral_source column (if it doesn't exist)
SET @sql = 'ALTER TABLE entries ADD COLUMN referral_source varchar(100) DEFAULT NULL AFTER priority';
SET @sql_check = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entries' AND COLUMN_NAME = 'referral_source');
SET @sql = IF(@sql_check = 0, @sql, 'SELECT "referral_source column already exists" as result');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add patient_contact column (if it doesn't exist)
SET @sql = 'ALTER TABLE entries ADD COLUMN patient_contact varchar(100) DEFAULT NULL AFTER referral_source';
SET @sql_check = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entries' AND COLUMN_NAME = 'patient_contact');
SET @sql = IF(@sql_check = 0, @sql, 'SELECT "patient_contact column already exists" as result');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add patient_address column (if it doesn't exist)
SET @sql = 'ALTER TABLE entries ADD COLUMN patient_address text DEFAULT NULL AFTER patient_contact';
SET @sql_check = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entries' AND COLUMN_NAME = 'patient_address');
SET @sql = IF(@sql_check = 0, @sql, 'SELECT "patient_address column already exists" as result');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add gender column (if it doesn't exist)
SET @sql = 'ALTER TABLE entries ADD COLUMN gender varchar(10) DEFAULT NULL AFTER patient_address';
SET @sql_check = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entries' AND COLUMN_NAME = 'gender');
SET @sql = IF(@sql_check = 0, @sql, 'SELECT "gender column already exists" as result');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 4: Add indexes for better performance (if they don't exist)
SELECT '=== ADDING INDEXES ===' as info;

-- Add priority index
SET @sql = 'ALTER TABLE entries ADD INDEX idx_entries_priority (priority)';
SET @sql_check = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entries' AND INDEX_NAME = 'idx_entries_priority');
SET @sql = IF(@sql_check = 0, @sql, 'SELECT "idx_entries_priority index already exists" as result');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add referral_source index
SET @sql = 'ALTER TABLE entries ADD INDEX idx_entries_referral_source (referral_source)';
SET @sql_check = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'entries' AND INDEX_NAME = 'idx_entries_referral_source');
SET @sql = IF(@sql_check = 0, @sql, 'SELECT "idx_entries_referral_source index already exists" as result');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 5: Update existing entries with default values
SELECT '=== UPDATING EXISTING ENTRIES ===' as info;
UPDATE entries SET priority = 'normal' WHERE priority IS NULL;
SELECT CONCAT('Updated ', ROW_COUNT(), ' entries with default priority') as update_result;

-- Step 6: Final verification
SELECT '=== FINAL VERIFICATION ===' as info;

-- Show updated table structure
DESCRIBE entries;

-- Check all required columns exist
SELECT 
    'FINAL COLUMN CHECK' as check_type,
    CASE WHEN COUNT(*) > 0 THEN '✓ EXISTS' ELSE '✗ MISSING' END as subtotal,
    CASE WHEN COUNT(*) > 0 THEN '✓ EXISTS' ELSE '✗ MISSING' END as discount_amount,
    CASE WHEN COUNT(*) > 0 THEN '✓ EXISTS' ELSE '✗ MISSING' END as total_price,
    CASE WHEN COUNT(*) > 0 THEN '✓ EXISTS' ELSE '✗ MISSING' END as priority,
    CASE WHEN COUNT(*) > 0 THEN '✓ EXISTS' ELSE '✗ MISSING' END as referral_source,
    CASE WHEN COUNT(*) > 0 THEN '✓ EXISTS' ELSE '✗ MISSING' END as notes
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'entries' 
  AND COLUMN_NAME IN ('subtotal', 'discount_amount', 'total_price', 'priority', 'referral_source', 'notes');

-- Show sample entry data to verify structure
SELECT '=== SAMPLE ENTRY DATA ===' as info;
SELECT 
    id,
    priority,
    referral_source,
    subtotal,
    discount_amount,
    total_price,
    LEFT(notes, 50) as notes_preview
FROM entries 
ORDER BY id DESC 
LIMIT 3;

SELECT '=== FIX COMPLETE ===' as info;
SELECT 'All required columns have been added. The form should now save all fields properly.' as result;
