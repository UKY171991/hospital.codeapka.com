-- Update entries table schema to match specifications
-- This migration updates the existing entries table structure

-- Add missing columns if they don't exist
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `unit` VARCHAR(50) DEFAULT NULL AFTER `result_value`,
ADD COLUMN IF NOT EXISTS `added_by` INT(11) DEFAULT NULL AFTER `status`,
ADD COLUMN IF NOT EXISTS `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `added_by`,
ADD COLUMN IF NOT EXISTS `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Update column defaults and constraints
ALTER TABLE `entries` 
MODIFY COLUMN `status` VARCHAR(20) DEFAULT 'pending',
MODIFY COLUMN `result_status` VARCHAR(50) DEFAULT 'normal';

-- Add indexes for better performance
ALTER TABLE `entries` 
ADD INDEX IF NOT EXISTS `idx_patient_id` (`patient_id`),
ADD INDEX IF NOT EXISTS `idx_test_id` (`test_id`),
ADD INDEX IF NOT EXISTS `idx_doctor_id` (`doctor_id`),
ADD INDEX IF NOT EXISTS `idx_entry_date` (`entry_date`),
ADD INDEX IF NOT EXISTS `idx_status` (`status`),
ADD INDEX IF NOT EXISTS `idx_added_by` (`added_by`);

-- Add foreign key constraints if they don't exist
-- Note: These will only work if the referenced tables exist
-- ALTER TABLE `entries` 
-- ADD CONSTRAINT `fk_entries_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
-- ADD CONSTRAINT `fk_entries_test` FOREIGN KEY (`test_id`) REFERENCES `tests`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
-- ADD CONSTRAINT `fk_entries_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
-- ADD CONSTRAINT `fk_entries_added_by` FOREIGN KEY (`added_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Sample data for testing (optional)
-- INSERT INTO `entries` (patient_id, test_id, doctor_id, entry_date, result_value, unit, remarks, status, added_by) VALUES
-- (1, 1, 1, '2025-01-15 10:30:00', '5.6', 'mg/dL', 'Normal glucose level', 'completed', 1),
-- (2, 2, 1, '2025-01-15 11:00:00', '120/80', 'mmHg', 'Normal blood pressure', 'completed', 1),
-- (3, 3, 2, '2025-01-15 14:30:00', '7.2', 'mg/dL', 'Elevated glucose, follow-up needed', 'pending', 1);
