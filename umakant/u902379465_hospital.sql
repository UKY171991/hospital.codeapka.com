-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 29, 2025 at 05:37 AM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u902379465_hospital`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `added_by`, `created_at`, `updated_at`) VALUES
(3, 'Complete Blood Count (CBC)', '', 1, '2025-08-26 06:20:33', '2025-08-26 08:39:10'),
(4, 'Lipid panel', '', 1, '2025-08-26 06:21:14', '2025-08-26 08:30:38'),
(5, 'Basic Metabolic Panel (BMP)', '', 1, '2025-08-26 08:00:49', '2025-08-26 08:39:32'),
(6, 'C-reactive protein (crp)', '', 1, '2025-08-26 08:00:55', '2025-08-26 08:31:39'),
(7, 'Thyroid Function Test (T3, T4, TSH)', '', 1, '2025-08-26 08:32:15', '2025-08-26 08:42:38'),
(8, 'Glucose test', '', 1, '2025-08-26 08:32:33', '2025-08-26 08:32:33'),
(9, 'Sedimentation rate', '', 1, '2025-08-26 08:32:52', '2025-08-26 08:32:52'),
(10, 'Blood smear', '', 1, '2025-08-26 08:33:06', '2025-08-26 08:33:06'),
(11, 'Calcium', '', 1, '2025-08-26 08:33:47', '2025-08-26 08:33:47'),
(12, 'Coagulation panel', '', 1, '2025-08-26 08:34:02', '2025-08-26 08:34:02'),
(13, 'Electrolyte panel', '', 1, '2025-08-26 08:34:15', '2025-08-26 08:34:15'),
(14, 'Hematocrit', '', 1, '2025-08-26 08:34:30', '2025-08-27 05:21:13'),
(15, 'Hemoglobin', '', 1, '2025-08-26 08:34:49', '2025-08-26 08:34:49'),
(16, 'Liver blood tests', '', 1, '2025-08-26 08:35:12', '2025-08-26 08:35:12'),
(17, 'Platelets', '', 1, '2025-08-26 08:35:29', '2025-08-26 08:35:29'),
(18, 'Red blood cell', '', 1, '2025-08-26 08:35:51', '2025-08-26 08:35:51'),
(19, 'White blood cell', '', 1, '2025-08-26 08:36:01', '2025-08-26 08:36:01'),
(20, 'Allergy testing', '', 1, '2025-08-26 08:36:13', '2025-08-26 08:36:13'),
(21, 'Antinuclear antibody', '', 1, '2025-08-26 08:36:28', '2025-08-26 08:36:28'),
(22, 'Blood compatibility testing', '', 1, '2025-08-26 08:36:43', '2025-08-26 08:36:43'),
(23, 'Blood urea nitrogen (bun)', '', 1, '2025-08-26 08:37:03', '2025-08-26 08:37:03'),
(24, 'Cardiac biomarkers', '', 1, '2025-08-26 08:37:20', '2025-08-26 08:37:20'),
(25, 'Chlamydia test', '', 1, '2025-08-26 08:37:34', '2025-08-26 08:37:34'),
(26, 'Chloride', '', 1, '2025-08-26 08:37:47', '2025-08-26 08:37:47'),
(27, 'Comprehensive Metabolic Panel (CMP)', '', 1, '2025-08-26 08:39:51', '2025-08-26 08:39:51'),
(28, 'Lipid Profile (Cholesterol Test)', '', 1, '2025-08-26 08:40:16', '2025-08-26 08:40:16'),
(29, 'Blood Glucose (Fasting, PP, HbA1c)', '', 1, '2025-08-26 08:41:08', '2025-08-26 08:41:08'),
(30, 'Liver Function Test (LFT)', '', 1, '2025-08-26 08:41:30', '2025-08-26 08:41:30'),
(31, 'Kidney Function Test (KFT) / Renal Profile', '', 1, '2025-08-26 08:41:56', '2025-08-26 08:41:56'),
(32, 'HIV Test', '', 1, '2025-08-26 08:44:16', '2025-08-26 08:44:16'),
(33, 'Hepatitis Panel (Hepatitis A, B, C)', '', 1, '2025-08-26 08:44:35', '2025-08-26 08:44:35'),
(34, 'Dengue, Malaria, Typhoid Tests', '', 1, '2025-08-26 08:44:55', '2025-08-26 08:44:55'),
(35, 'Erythrocyte Sedimentation Rate (ESR )', '', 1, '2025-08-26 08:46:11', '2025-08-26 08:46:11'),
(36, 'Vitamin D Test', '', 1, '2025-08-26 08:46:31', '2025-08-26 08:46:31'),
(37, 'Vitamin B12 Test', '', 1, '2025-08-26 08:46:46', '2025-08-26 08:46:46'),
(38, 'Calcium, Iron, Ferritin Levels', '', 1, '2025-08-26 08:47:00', '2025-08-26 08:47:00'),
(39, 'Testosterone, Estrogen, Progesterone', '', 1, '2025-08-26 08:47:26', '2025-08-26 08:47:26'),
(40, 'FSH, LH, Prolactin', '', 1, '2025-08-26 08:47:49', '2025-08-26 08:47:49'),
(41, 'Beta-hCG (Pregnancy Test)', '', 1, '2025-08-26 08:48:33', '2025-08-26 08:48:33'),
(42, 'Prothrombin Time (PT)', '', 1, '2025-08-26 08:49:13', '2025-08-26 08:49:13'),
(43, 'International Normalized Ratio (INR)', '', 1, '2025-08-26 08:49:48', '2025-08-26 08:49:48'),
(44, 'Activated Partial Thromboplastin Time (APTT)', '', 1, '2025-08-26 08:50:10', '2025-08-26 08:50:10'),
(45, 'Prostate Specific Antigen (PSA)', '', 1, '2025-08-26 08:50:43', '2025-08-26 08:50:43'),
(46, 'CA-125 (Ovarian Cancer Marker)', '', 1, '2025-08-26 08:50:56', '2025-08-26 08:50:56'),
(47, 'AFP (Alpha-Fetoprotein)', '', 1, '2025-08-26 08:51:14', '2025-08-26 08:51:14');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `hospital` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `registration_no` varchar(100) DEFAULT NULL,
  `percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `test_id` int(11) DEFAULT NULL,
  `entry_date` datetime DEFAULT NULL,
  `result_value` text DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `title`, `content`, `start_date`, `end_date`, `active`, `added_by`, `created_at`, `updated_at`) VALUES
(5, 'Happy New Years', '', '2025-12-31 23:52:00', '2026-01-01 23:51:00', 1, 1, '2025-08-27 03:41:02', '2025-08-27 05:23:33');

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id`, `name`, `phone`, `whatsapp`, `email`, `address`, `added_by`, `created_at`, `updated_at`) VALUES
(3, 'Support (Umakant Yadav)', '+91-9453619260', '+91-9453619260', 'uky171991@gmail.com', 'codeapka.com', 1, '2025-08-26 13:19:00', '2025-08-28 11:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `father_husband` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `age_unit` varchar(20) DEFAULT NULL,
  `uhid` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `upi` varchar(191) DEFAULT NULL,
  `time_type` enum('monthly','yearly') NOT NULL DEFAULT 'monthly',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `added_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `description`, `price`, `upi`, `time_type`, `start_date`, `end_date`, `added_by`, `created_at`, `updated_at`) VALUES
(1, 'Yearly Plan', 'Accusantium a error', 3333.00, '8081674028@upi', 'yearly', '1974-10-02', '1989-05-02', 1, '2025-08-27 05:25:32', '2025-08-27 06:24:03'),
(2, 'Basic Plan', 'Hic magna pariatur', 300.00, '8081674028@upi', 'monthly', '2021-04-13', '2016-11-14', 1, '2025-08-27 05:26:40', '2025-08-27 06:23:49');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `data` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(50) DEFAULT NULL,
  `specimen` varchar(100) DEFAULT NULL,
  `default_result` text DEFAULT NULL,
  `reference_range` varchar(255) DEFAULT NULL,
  `min` decimal(10,2) DEFAULT NULL,
  `max` decimal(10,2) DEFAULT NULL,
  `sub_heading` tinyint(1) NOT NULL DEFAULT 0,
  `test_code` varchar(50) DEFAULT NULL,
  `method` varchar(100) DEFAULT NULL,
  `print_new_page` tinyint(1) NOT NULL DEFAULT 0,
  `shortcut` varchar(50) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `min_male` decimal(10,2) DEFAULT NULL,
  `max_male` decimal(10,2) DEFAULT NULL,
  `min_female` decimal(10,2) DEFAULT NULL,
  `max_female` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `name`, `description`, `category_id`, `price`, `unit`, `specimen`, `default_result`, `reference_range`, `min`, `max`, `sub_heading`, `test_code`, `method`, `print_new_page`, `shortcut`, `added_by`, `min_male`, `max_male`, `min_female`, `max_female`) VALUES
(19, 'Graham Sherman', 'Quibusdam est dolore', 45, 619.00, 'Laboris odio adipisi', '', '', '', 100.00, 110.00, 1, '', '', 0, '', 1, 74.00, 80.00, 74.00, 87.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `added_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `added_by`, `is_active`, `created_at`, `last_login`, `expire_date`, `updated_at`) VALUES
(1, 'umakant', '$2y$10$oRGQF5fROySm7UyX.qeFAO26K5I3nKzTPJPjctZYHimYidVx1uvBm', 'Umakant Yadav', 'umakant171991@gmail.com', 'master', NULL, 1, '2025-08-25 09:47:40', '2025-08-29 01:27:38', NULL, '2025-08-25 09:47:40'),
(2, 'alok', '$2y$10$Ip95EmIgT99MSwlk4wYrxOg8BFg4T.tIOEUtx953ITel5FLeN94zi', 'Alok Yadav', 'alok@gmail.com', 'user', 1, 1, '2025-08-28 09:56:05', NULL, '2025-09-24 15:44:58', '2025-08-25 09:56:05'),
(3, 'uma', '$2y$10$rFFtK96Kr8ssYvoL/0UfP.h.G7uLDkn37ZO32mqeLHbgHWcc4Wgv.', 'Umakant Yadav', 'umakant171991@gmail.com', 'user', 1, 1, '2025-08-30 09:57:26', NULL, '2025-08-31 15:44:00', '2025-08-25 09:57:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `idx_doctors_name` (`name`),
  ADD KEY `idx_doctors_contact` (`contact_no`),
  ADD KEY `idx_doctors_email` (`email`);

--
-- Indexes for table `entries`
--
ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entries_patient` (`patient_id`),
  ADD KEY `idx_entries_doctor` (`doctor_id`),
  ADD KEY `idx_entries_test` (`test_id`),
  ADD KEY `idx_entries_added_by` (`added_by`),
  ADD KEY `idx_entries_date` (`entry_date`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `idx_patients_name` (`name`),
  ADD KEY `idx_patients_mobile` (`mobile`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`),
  ADD KEY `time_type` (`time_type`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reports_created_at` (`created_at`),
  ADD KEY `idx_reports_added_by` (`added_by`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_users_username` (`username`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `entries`
--
ALTER TABLE `entries`
  ADD CONSTRAINT `entries_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `entries_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`),
  ADD CONSTRAINT `entries_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`),
  ADD CONSTRAINT `entries_ibfk_4` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `tests_ibfk_2` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
