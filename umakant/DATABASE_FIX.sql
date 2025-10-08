-- =====================================================
-- DATABASE FIX: Add Missing Columns to Entries Table
-- =====================================================
-- This script fixes the issue where referral_source, priority, 
-- and other fields are not being saved to the database.
--
-- Run this script in phpMyAdmin or your database management tool
-- =====================================================

-- Add priority column (for Normal, Urgent, Emergency, Routine)
ALTER TABLE `entries` ADD COLUMN `priority` varchar(50) DEFAULT 'normal' AFTER `status`;

-- Add referral_source column (for Doctor Referral, Hospital, Walk-in, etc.)
ALTER TABLE `entries` ADD COLUMN `referral_source` varchar(100) DEFAULT NULL AFTER `priority`;

-- Add patient_contact column (for storing patient contact info in entry)
ALTER TABLE `entries` ADD COLUMN `patient_contact` varchar(100) DEFAULT NULL AFTER `referral_source`;

-- Add patient_address column (for storing patient address in entry)
ALTER TABLE `entries` ADD COLUMN `patient_address` text DEFAULT NULL AFTER `patient_contact`;

-- Add gender column (for storing patient gender in entry)
ALTER TABLE `entries` ADD COLUMN `gender` varchar(10) DEFAULT NULL AFTER `patient_address`;

-- Add indexes for better performance
ALTER TABLE `entries` ADD INDEX `idx_entries_priority` (`priority`);
ALTER TABLE `entries` ADD INDEX `idx_entries_referral_source` (`referral_source`);

-- Update existing entries to have default priority
UPDATE `entries` SET `priority` = 'normal' WHERE `priority` IS NULL;

-- Show the updated table structure
DESCRIBE `entries`;

-- =====================================================
-- VERIFICATION: Check that columns were added
-- =====================================================
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'entries' 
  AND COLUMN_NAME IN ('priority', 'referral_source', 'patient_contact', 'patient_address', 'gender')
ORDER BY ORDINAL_POSITION;
