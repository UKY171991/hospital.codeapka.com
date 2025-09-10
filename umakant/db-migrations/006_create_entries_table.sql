-- Entry table migration
-- Create entries table with proper foreign key relationships

DROP TABLE IF EXISTS `entries`;

CREATE TABLE `entries` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `patient_id` INT UNSIGNED NOT NULL,
  `test_id` INT UNSIGNED NOT NULL,
  `result_value` TEXT,
  `result_status` VARCHAR(50) DEFAULT 'normal',
  `remarks` TEXT DEFAULT NULL,
  `test_date` DATE DEFAULT NULL,
  `reported_date` DATETIME DEFAULT NULL,
  `doctor_id` INT UNSIGNED DEFAULT NULL,
  `status` VARCHAR(20) DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX (`patient_id`),
  INDEX (`test_id`),
  INDEX (`doctor_id`),
  CONSTRAINT `fk_entries_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_entries_test` FOREIGN KEY (`test_id`) REFERENCES `tests`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_entries_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data (optional)
INSERT INTO `entries` (patient_id, test_id, result_value, result_status, remarks, test_date, reported_date, doctor_id, status) VALUES
(1, 1, '5.6', 'normal', 'No issues detected', '2025-09-10', NOW(), 1, 'active'),
(1, 2, '120/80', 'normal', 'Blood pressure within normal range', '2025-09-10', NOW(), 1, 'active'),
(2, 1, '7.2', 'high', 'Slightly elevated, recommend follow-up', '2025-09-09', NOW(), 2, 'active');
