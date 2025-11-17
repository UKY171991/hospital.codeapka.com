-- Clean up ALL auto-imported records and reprocess
-- Run this in phpMyAdmin

-- Delete all auto-imported records
DELETE FROM inventory_income WHERE notes LIKE '%Auto-imported%';
DELETE FROM inventory_expense WHERE notes LIKE '%Auto-imported%';

-- Clear processed emails to allow reprocessing
TRUNCATE TABLE processed_emails;

-- After running this:
-- 1. Go to: Inventory → Email Parser
-- 2. Click "Run Parser Now"
-- 3. Check Income page - should only have CREDIT transactions
-- 4. Check Expense page - should only have DEBIT transactions
