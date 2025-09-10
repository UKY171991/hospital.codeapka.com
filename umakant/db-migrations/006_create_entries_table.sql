-- Entry table migration (Compatible with existing database structure)
-- This will update the existing entries table or create if it doesn't exist

-- First, check if we need to add new columns to existing entries table
-- ALTER TABLE `entries` ADD COLUMN `result_status` VARCHAR(50) DEFAULT 'normal' AFTER `result_value`;
-- ALTER TABLE `entries` ADD COLUMN `test_date` DATE DEFAULT NULL AFTER `entry_date`;
-- ALTER TABLE `entries` ADD COLUMN `reported_date` DATETIME DEFAULT NULL AFTER `test_date`;
-- ALTER TABLE `entries` ADD COLUMN `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Or create new entries table if completely rebuilding
DROP TABLE IF EXISTS `entries`;

CREATE TABLE `entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `test_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `entry_date` datetime DEFAULT NULL,
  `test_date` date DEFAULT NULL,
  `reported_date` datetime DEFAULT NULL,
  `result_value` text DEFAULT NULL,
  `result_status` varchar(50) DEFAULT 'normal',
  `unit` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_patient_id` (`patient_id`),
  KEY `idx_test_id` (`test_id`),
  KEY `idx_doctor_id` (`doctor_id`),
  KEY `idx_entry_date` (`entry_date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (optional)
INSERT INTO `entries` (patient_id, test_id, doctor_id, entry_date, test_date, reported_date, result_value, result_status, unit, remarks, status, added_by) VALUES
(1, 35, 31, '2025-09-10 14:30:00', '2025-09-10', '2025-09-10 14:30:00', '7500', 'normal', 'cells/mcl', 'WBC count within normal range', 'completed', 1),
(1, 36, 31, '2025-09-10 14:35:00', '2025-09-10', '2025-09-10 14:35:00', '250000', 'normal', 'cells/mcl', 'Platelet count normal', 'completed', 1),
(1, 37, 31, '2025-09-10 14:40:00', '2025-09-10', '2025-09-10 14:40:00', '42', 'normal', '%', 'Hematocrit normal', 'completed', 1);
