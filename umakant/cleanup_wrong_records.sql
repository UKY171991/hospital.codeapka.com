-- Clean up wrong records
-- Run this in phpMyAdmin to remove incorrectly categorized transactions

-- Delete INCOME records that contain DEBIT keywords (should be expense)
DELETE FROM inventory_income 
WHERE description LIKE '%debit%' 
   OR description LIKE '%paid%' 
   OR description LIKE '%purchase%'
   OR description LIKE '%withdrawn%';

-- Delete EXPENSE records that contain CREDIT keywords (should be income)
DELETE FROM inventory_expense 
WHERE description LIKE '%credit%' 
   OR description LIKE '%received%' 
   OR description LIKE '%deposit%';

-- Clear processed emails to allow reprocessing
TRUNCATE TABLE processed_emails;

-- After running this, go to: Inventory → Email Parser → Run Parser Now
