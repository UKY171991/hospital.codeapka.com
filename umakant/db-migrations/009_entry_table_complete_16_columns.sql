-- =====================================================
-- ENTRY TABLE SCHEMA - COMPLETE 16 COLUMN STRUCTURE
-- =====================================================
-- This script creates/updates the entries table to match the exact
-- structure shown in phpMyAdmin with all 16 columns

-- =====================================================
-- 1. CREATE/UPDATE ENTRIES TABLE WITH ALL COLUMNS
-- =====================================================

-- Drop existing table if needed (uncomment if rebuilding)
-- DROP TABLE IF EXISTS `entries`;

CREATE TABLE IF NOT EXISTS `entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `entry_date` datetime NOT NULL,
  `result_value` text DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  `grouped` tinyint(1) DEFAULT 0,
  `tests_count` int(11) DEFAULT 1,
  `test_ids` longtext DEFAULT NULL,
  `test_names` longtext DEFAULT NULL,
  `test_results` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_doctor_id` (`doctor_id`),
  KEY `idx_test_id` (`test_id`),
  KEY `idx_entry_date` (`entry_date`),
  KEY `idx_status` (`status`),
  KEY `idx_added_by` (`added_by`),
  KEY `idx_grouped` (`grouped`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- 2. ADD MISSING COLUMNS IF THEY DON'T EXIST
-- =====================================================

-- Add grouped column if it doesn't exist
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `grouped` tinyint(1) DEFAULT 0 AFTER `created_at`;

-- Add tests_count column if it doesn't exist
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `tests_count` int(11) DEFAULT 1 AFTER `grouped`;

-- Add test_ids column if it doesn't exist
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `test_ids` longtext DEFAULT NULL AFTER `tests_count`;

-- Add test_names column if it doesn't exist
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `test_names` longtext DEFAULT NULL AFTER `test_ids`;

-- Add test_results column if it doesn't exist
ALTER TABLE `entries` 
ADD COLUMN IF NOT EXISTS `test_results` longtext DEFAULT NULL AFTER `test_names`;

-- =====================================================
-- 3. UPDATE COLUMN CONSTRAINTS AND DEFAULTS
-- =====================================================

-- Update status column to match enum values
ALTER TABLE `entries` 
MODIFY COLUMN `status` enum('pending','completed','failed') DEFAULT 'pending';

-- Update created_at column
ALTER TABLE `entries` 
MODIFY COLUMN `created_at` timestamp DEFAULT current_timestamp();

-- =====================================================
-- 4. SAMPLE DATA FOR TESTING
-- =====================================================

-- Insert sample entries with all fields
INSERT INTO `entries` (
    `patient_id`, `doctor_id`, `test_id`, `entry_date`, 
    `result_value`, `unit`, `remarks`, `status`, `added_by`,
    `grouped`, `tests_count`, `test_ids`, `test_names`, `test_results`
) VALUES
(1, 1, 1, '2025-01-15 10:30:00', '5.6', 'mg/dL', 'Normal glucose level', 'completed', 1, 0, 1, '[1]', '["Glucose Test"]', '["5.6"]'),
(2, 1, 2, '2025-01-15 11:00:00', '120/80', 'mmHg', 'Normal blood pressure', 'completed', 1, 0, 1, '[2]', '["Blood Pressure"]', '["120/80"]'),
(3, 2, 3, '2025-01-15 14:30:00', '7.2', 'mg/dL', 'Elevated glucose, follow-up needed', 'pending', 1, 0, 1, '[3]', '["Glucose Test"]', '["7.2"]'),
(4, 1, 1, '2025-01-16 09:15:00', '4.8', 'mg/dL', 'Good control', 'completed', 1, 0, 1, '[1]', '["Glucose Test"]', '["4.8"]'),
(5, 2, 4, '2025-01-16 10:45:00', '140/90', 'mmHg', 'Slightly elevated, monitor', 'pending', 1, 0, 1, '[4]', '["Blood Pressure"]', '["140/90"]'),
(6, 1, 5, '2025-01-16 14:20:00', '7500', 'cells/mcl', 'WBC count within normal range', 'completed', 1, 1, 3, '[5,6,7]', '["WBC Count","RBC Count","Platelet Count"]', '["7500","4.5","250000"]');

-- =====================================================
-- 5. VERIFICATION QUERIES
-- =====================================================

-- Check table structure
DESCRIBE `entries`;

-- Check all entries with enriched data
SELECT e.id,
       e.patient_id,
       e.doctor_id,
       e.test_id,
       e.entry_date,
       e.result_value,
       e.unit,
       e.remarks,
       e.status,
       e.added_by,
       e.created_at,
       e.grouped,
       e.tests_count,
       e.test_ids,
       e.test_names,
       e.test_results,
       p.name as patient_name,
       p.uhid as patient_uhid,
       d.name as doctor_name,
       t.name as test_name,
       u.username as added_by_username
FROM entries e 
LEFT JOIN patients p ON e.patient_id = p.id 
LEFT JOIN tests t ON e.test_id = t.id 
LEFT JOIN doctors d ON e.doctor_id = d.id 
LEFT JOIN users u ON e.added_by = u.id
ORDER BY e.entry_date DESC, e.id DESC;

-- =====================================================
-- 6. API TESTING QUERIES
-- =====================================================

-- Test list endpoint query (matches API)
SELECT e.id,
       e.patient_id,
       e.doctor_id,
       e.test_id,
       e.entry_date,
       e.result_value,
       e.unit,
       e.remarks,
       e.status,
       e.added_by,
       e.created_at,
       e.grouped,
       e.tests_count,
       e.test_ids,
       e.test_names,
       e.test_results,
       p.name as patient_name,
       p.uhid as patient_uhid,
       d.name as doctor_name,
       t.name as test_name,
       u.username as added_by_username
FROM entries e 
LEFT JOIN patients p ON e.patient_id = p.id 
LEFT JOIN tests t ON e.test_id = t.id 
LEFT JOIN doctors d ON e.doctor_id = d.id 
LEFT JOIN users u ON e.added_by = u.id
ORDER BY COALESCE(e.entry_date, e.created_at) DESC, e.id DESC;

-- Test get single entry query
SELECT e.id,
       e.patient_id,
       e.doctor_id,
       e.test_id,
       e.entry_date,
       e.result_value,
       e.unit,
       e.remarks,
       e.status,
       e.added_by,
       e.created_at,
       e.grouped,
       e.tests_count,
       e.test_ids,
       e.test_names,
       e.test_results,
       p.name as patient_name,
       p.uhid as patient_uhid,
       p.age,
       p.sex as gender,
       d.name as doctor_name,
       t.name as test_name,
       u.username as added_by_username
FROM entries e 
LEFT JOIN patients p ON e.patient_id = p.id 
LEFT JOIN tests t ON e.test_id = t.id 
LEFT JOIN doctors d ON e.doctor_id = d.id 
LEFT JOIN users u ON e.added_by = u.id
WHERE e.id = 1;

-- =====================================================
-- 7. STATISTICS QUERIES FOR DASHBOARD
-- =====================================================

-- Total entries count
SELECT COUNT(*) as total_entries FROM entries;

-- Pending entries count
SELECT COUNT(*) as pending_entries FROM entries WHERE status = 'pending';

-- Completed entries count
SELECT COUNT(*) as completed_entries FROM entries WHERE status = 'completed';

-- Failed entries count
SELECT COUNT(*) as failed_entries FROM entries WHERE status = 'failed';

-- Today's entries count
SELECT COUNT(*) as today_entries FROM entries WHERE DATE(entry_date) = CURDATE();

-- Grouped entries count
SELECT COUNT(*) as grouped_entries FROM entries WHERE grouped = 1;

-- =====================================================
-- 8. FOREIGN KEY CONSTRAINTS (Optional)
-- =====================================================

-- Note: Uncomment these if you want to add foreign key constraints
-- Make sure the referenced tables exist first

-- ALTER TABLE `entries` 
-- ADD CONSTRAINT `fk_entries_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
-- ADD CONSTRAINT `fk_entries_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
-- ADD CONSTRAINT `fk_entries_test` FOREIGN KEY (`test_id`) REFERENCES `tests`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
-- ADD CONSTRAINT `fk_entries_added_by` FOREIGN KEY (`added_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- =====================================================
-- END OF SCRIPT
-- =====================================================
