-- Manual SQL commands to add missing columns to entries table
-- Copy and paste these commands into phpMyAdmin or MySQL command line

-- 1. Add priority column
ALTER TABLE `entries` ADD COLUMN `priority` VARCHAR(20) DEFAULT 'normal' AFTER `status`;

-- 2. Add referral_source column  
ALTER TABLE `entries` ADD COLUMN `referral_source` VARCHAR(255) DEFAULT NULL AFTER `priority`;

-- 3. Add patient_contact column
ALTER TABLE `entries` ADD COLUMN `patient_contact` VARCHAR(50) DEFAULT NULL AFTER `referral_source`;

-- 4. Add patient_address column
ALTER TABLE `entries` ADD COLUMN `patient_address` TEXT DEFAULT NULL AFTER `patient_contact`;

-- 5. Add gender column
ALTER TABLE `entries` ADD COLUMN `gender` VARCHAR(10) DEFAULT NULL AFTER `patient_address`;

-- 6. Add owner_id column (if not exists)
ALTER TABLE `entries` ADD COLUMN `owner_id` INT(11) DEFAULT NULL AFTER `doctor_id`;

-- 7. Verify the changes
DESCRIBE `entries`;

-- 8. Optional: Add indexes for better performance
CREATE INDEX `idx_entries_priority` ON `entries` (`priority`);
CREATE INDEX `idx_entries_owner_id` ON `entries` (`owner_id`);
CREATE INDEX `idx_entries_added_by` ON `entries` (`added_by`);
