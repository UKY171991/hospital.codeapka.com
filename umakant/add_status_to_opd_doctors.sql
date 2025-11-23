-- Add status column to opd_doctors table
-- Run this SQL to add the status column if it doesn't exist

ALTER TABLE `opd_doctors` 
ADD COLUMN `status` ENUM('Active','Inactive') DEFAULT 'Active' AFTER `registration_no`;

-- Update existing records to Active status
UPDATE `opd_doctors` SET `status` = 'Active' WHERE `status` IS NULL;

-- Add index for better performance
ALTER TABLE `opd_doctors` ADD INDEX `idx_opd_doctors_status` (`status`);
