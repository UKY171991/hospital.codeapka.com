-- SQL for OPD Billing Table
-- Run this in your phpMyAdmin to create the opd_billing table

CREATE TABLE IF NOT EXISTS `opd_billing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `patient_name` varchar(255) NOT NULL,
  `patient_phone` varchar(20) DEFAULT NULL,
  `patient_age` int(11) DEFAULT NULL,
  `patient_gender` enum('Male','Female','Other') DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `medicine_charges` decimal(10,2) DEFAULT 0.00,
  `lab_charges` decimal(10,2) DEFAULT 0.00,
  `other_charges` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `balance_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('Cash','Card','UPI','Online','Insurance') DEFAULT 'Cash',
  `payment_status` enum('Paid','Partial','Unpaid') DEFAULT 'Unpaid',
  `bill_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `added_by` (`added_by`),
  KEY `bill_date` (`bill_date`),
  KEY `payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
