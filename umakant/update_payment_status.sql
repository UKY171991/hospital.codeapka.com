-- Migration script to add payment_status column to existing tables
-- Run this script once to update your database

-- Add payment_status column to inventory_expense table if it doesn't exist
ALTER TABLE `inventory_expense` 
ADD COLUMN IF NOT EXISTS `payment_status` enum('Success','Pending','Failed') NOT NULL DEFAULT 'Success' AFTER `payment_method`;

-- Add payment_status column to inventory_income table if it doesn't exist
ALTER TABLE `inventory_income` 
ADD COLUMN IF NOT EXISTS `payment_status` enum('Success','Pending','Failed') NOT NULL DEFAULT 'Success' AFTER `payment_method`;

-- Update existing records to have 'Success' status by default
UPDATE `inventory_expense` SET `payment_status` = 'Success' WHERE `payment_status` IS NULL;
UPDATE `inventory_income` SET `payment_status` = 'Success' WHERE `payment_status` IS NULL;

-- For records with "pending" in description, set status to 'Pending'
UPDATE `inventory_expense` 
SET `payment_status` = 'Pending' 
WHERE LOWER(`description`) LIKE '%pending%' OR LOWER(`notes`) LIKE '%pending%';

UPDATE `inventory_income` 
SET `payment_status` = 'Pending' 
WHERE LOWER(`description`) LIKE '%pending%' OR LOWER(`notes`) LIKE '%pending%';
