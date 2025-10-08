-- SQL script to add missing columns to entries table
-- Run this script to add the columns that the form is trying to use

-- Add priority column
ALTER TABLE `entries` ADD COLUMN `priority` VARCHAR(20) DEFAULT 'normal' AFTER `status`;

-- Add referral_source column
ALTER TABLE `entries` ADD COLUMN `referral_source` VARCHAR(255) DEFAULT NULL AFTER `priority`;

-- Add patient_contact column
ALTER TABLE `entries` ADD COLUMN `patient_contact` VARCHAR(50) DEFAULT NULL AFTER `referral_source`;

-- Add patient_address column
ALTER TABLE `entries` ADD COLUMN `patient_address` TEXT DEFAULT NULL AFTER `patient_contact`;

-- Add gender column
ALTER TABLE `entries` ADD COLUMN `gender` VARCHAR(10) DEFAULT NULL AFTER `patient_address`;

-- Add owner_id column (if not exists)
ALTER TABLE `entries` ADD COLUMN `owner_id` INT(11) DEFAULT NULL AFTER `doctor_id`;

-- Optional: Add index for better performance
CREATE INDEX `idx_entries_priority` ON `entries` (`priority`);
CREATE INDEX `idx_entries_owner_id` ON `entries` (`owner_id`);
CREATE INDEX `idx_entries_added_by` ON `entries` (`added_by`);

-- Show the updated table structure
DESCRIBE `entries`;
