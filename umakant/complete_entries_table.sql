-- Complete SQL script to update entries table with all required columns
-- This script adds all the columns that the entry form expects

-- First, check if columns exist before adding them
-- Add priority column (for entry priority: normal, urgent, emergency)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `priority` VARCHAR(20) DEFAULT 'normal' 
COMMENT 'Entry priority: normal, urgent, emergency' 
AFTER `status`;

-- Add referral_source column (where the patient was referred from)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `referral_source` VARCHAR(255) DEFAULT NULL 
COMMENT 'Source of patient referral' 
AFTER `priority`;

-- Add patient_contact column (patient's contact number)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `patient_contact` VARCHAR(50) DEFAULT NULL 
COMMENT 'Patient contact number' 
AFTER `referral_source`;

-- Add patient_address column (patient's address)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `patient_address` TEXT DEFAULT NULL 
COMMENT 'Patient address' 
AFTER `patient_contact`;

-- Add gender column (patient's gender)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `gender` VARCHAR(10) DEFAULT NULL 
COMMENT 'Patient gender' 
AFTER `patient_address`;

-- Add owner_id column (link to owner/clinic)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `owner_id` INT(11) DEFAULT NULL 
COMMENT 'Owner/Clinic ID' 
AFTER `doctor_id`;

-- Add test_id column (primary test for backward compatibility)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `test_id` INT(11) DEFAULT NULL 
COMMENT 'Primary test ID for backward compatibility' 
AFTER `owner_id`;

-- Add test_ids column (comma-separated test IDs)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `test_ids` TEXT DEFAULT NULL 
COMMENT 'Comma-separated test IDs' 
AFTER `test_id`;

-- Add test_names column (comma-separated test names)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `test_names` TEXT DEFAULT NULL 
COMMENT 'Comma-separated test names' 
AFTER `test_ids`;

-- Add tests_count column (number of tests)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `tests_count` INT(11) DEFAULT 0 
COMMENT 'Number of tests in this entry' 
AFTER `test_names`;

-- Add grouped column (whether entry is grouped)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `grouped` TINYINT(1) DEFAULT 0 
COMMENT 'Whether entry is grouped' 
AFTER `tests_count`;

-- Add price column (total price)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `price` DECIMAL(10,2) DEFAULT 0.00 
COMMENT 'Total price' 
AFTER `grouped`;

-- Add remarks column (alternative to notes)
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `remarks` TEXT DEFAULT NULL 
COMMENT 'Entry remarks' 
AFTER `notes`;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_entries_priority` ON `entries` (`priority`);
CREATE INDEX IF NOT EXISTS `idx_entries_owner_id` ON `entries` (`owner_id`);
CREATE INDEX IF NOT EXISTS `idx_entries_added_by` ON `entries` (`added_by`);
CREATE INDEX IF NOT EXISTS `idx_entries_patient_id` ON `entries` (`patient_id`);
CREATE INDEX IF NOT EXISTS `idx_entries_doctor_id` ON `entries` (`doctor_id`);
CREATE INDEX IF NOT EXISTS `idx_entries_status` ON `entries` (`status`);
CREATE INDEX IF NOT EXISTS `idx_entries_entry_date` ON `entries` (`entry_date`);

-- Show the updated table structure
DESCRIBE `entries`;

-- Show table information
SHOW CREATE TABLE `entries`;
