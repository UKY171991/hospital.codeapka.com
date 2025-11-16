-- SQL Script to Fix Wrong Income/Expense Records
-- Run this to identify and fix incorrectly categorized transactions

-- ============================================
-- STEP 1: Identify Wrong Records
-- ============================================

-- Find EXPENSE records that should be INCOME (contain "credit" keywords)
SELECT 'WRONG: Should be INCOME' as issue, id, date, description, amount, vendor
FROM inventory_expense
WHERE description LIKE '%credit%'
   OR description LIKE '%received%'
   OR description LIKE '%deposit%'
   OR description LIKE '%incoming%'
   OR notes LIKE '%Auto-imported%';

-- Find INCOME records that should be EXPENSE (contain "debit" keywords)
SELECT 'WRONG: Should be EXPENSE' as issue, id, date, description, amount
FROM inventory_income
WHERE description LIKE '%debit%'
   OR description LIKE '%paid%'
   OR description LIKE '%purchase%'
   OR description LIKE '%withdrawn%'
   OR notes LIKE '%Auto-imported%';

-- ============================================
-- STEP 2: Delete All Auto-Imported Records (Recommended)
-- ============================================

-- This will delete all auto-imported records so you can reprocess them correctly
-- UNCOMMENT the lines below to execute:

-- DELETE FROM inventory_income WHERE notes LIKE '%Auto-imported from email%';
-- DELETE FROM inventory_expense WHERE notes LIKE '%Auto-imported from email%';
-- TRUNCATE TABLE processed_emails;

-- After running above, go to: Inventory → Email Parser → Run Parser Now

-- ============================================
-- STEP 3: Move Specific Wrong Records (Alternative)
-- ============================================

-- If you want to keep some records and just move the wrong ones:

-- Move EXPENSE to INCOME (for records with IDs 7, 8 - adjust as needed)
-- UNCOMMENT and adjust IDs:

/*
INSERT INTO inventory_income (date, category, client_id, description, amount, payment_method, notes, created_at)
SELECT date, category, NULL, description, amount, payment_method, 
       CONCAT('Moved from expense | ', notes), created_at
FROM inventory_expense
WHERE id IN (7, 8);  -- Replace with actual IDs from STEP 1

DELETE FROM inventory_expense WHERE id IN (7, 8);  -- Replace with actual IDs
*/

-- Move INCOME to EXPENSE (for records with wrong IDs - adjust as needed)
-- UNCOMMENT and adjust IDs:

/*
INSERT INTO inventory_expense (date, category, vendor, description, amount, payment_method, notes, created_at)
SELECT date, category, '', description, amount, payment_method,
       CONCAT('Moved from income | ', notes), created_at
FROM inventory_income
WHERE id IN (1, 2);  -- Replace with actual IDs from STEP 1

DELETE FROM inventory_income WHERE id IN (1, 2);  -- Replace with actual IDs
*/

-- ============================================
-- STEP 4: Verify After Fix
-- ============================================

-- Check INCOME records (should only have credit/received keywords)
SELECT 'INCOME CHECK' as type, id, date, description, amount
FROM inventory_income
WHERE notes LIKE '%Auto-imported%'
ORDER BY id DESC
LIMIT 10;

-- Check EXPENSE records (should only have debit/paid keywords)
SELECT 'EXPENSE CHECK' as type, id, date, description, amount
FROM inventory_expense
WHERE notes LIKE '%Auto-imported%'
ORDER BY id DESC
LIMIT 10;

-- ============================================
-- STEP 5: Count Records
-- ============================================

SELECT 
    'INCOME' as type,
    COUNT(*) as total,
    SUM(amount) as total_amount
FROM inventory_income
WHERE notes LIKE '%Auto-imported%'

UNION ALL

SELECT 
    'EXPENSE' as type,
    COUNT(*) as total,
    SUM(amount) as total_amount
FROM inventory_expense
WHERE notes LIKE '%Auto-imported%';
