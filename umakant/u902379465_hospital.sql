-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 14, 2025 at 07:14 AM
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
(59, 'Complete blood count (CBC)', '', 1, '2025-09-01 06:34:18', '2025-09-01 06:35:42'),
(60, 'Basic Metabolic Panel (BMP) / Chem-7', '', 1, '2025-09-01 08:43:40', '2025-09-07 11:09:56');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `server_id` int(11) DEFAULT NULL COMMENT 'External server identifier for synchronization',
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

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `server_id`, `name`, `qualification`, `specialization`, `hospital`, `contact_no`, `phone`, `email`, `address`, `registration_no`, `percent`, `added_by`, `created_at`, `updated_at`) VALUES
(45, NULL, 'Ramu', '', '', 'test', '565666', '56565656', '', 'ggg', '', 30.00, 3, '2025-09-04 06:07:00', '2025-09-14 02:48:40'),
(46, NULL, 'Ramu kamal', '', '', 'test 1', '565666', '56565656', '', '', '', 40.00, 3, '2025-09-04 06:09:31', '2025-09-04 06:55:08'),
(235, NULL, 'yyy', '', '', 'wertg', '345678', '', '', 'dfg', '', 60.00, 3, '2025-09-11 05:04:00', '2025-09-11 05:04:00'),
(236, NULL, 'yy', '', '', 'sdfg', 'qa', '', '', 'q', '', 10.00, 2, '2025-09-11 08:04:29', '2025-09-11 08:04:29'),
(237, NULL, 'hhh', '', '', 'dfg', '4567', '', '', 'dfg', '', 60.00, 2, '2025-09-11 08:05:43', '2025-09-11 08:05:43'),
(243, NULL, 'Test1 Doctor', '', '', 'ttt', '56565656', '', '', 'ert', '', 30.00, 5, '2025-09-12 04:53:29', '2025-09-12 04:53:29'),
(244, NULL, 'hhh', '', '', 'sdfg', '23456', '', '', 'ertg', '', 50.00, 5, '2025-09-12 04:56:05', '2025-09-12 04:56:05'),
(245, NULL, 'Test d3', '', '', 'sdf', 'qw1we12e', '', '', 'qw', '', 40.00, 5, '2025-09-13 10:01:59', '2025-09-13 10:01:59'),
(246, NULL, 'Test d3', '', '', 'werf', '14567', '', '', 'f', '', 60.00, 2, '2025-09-13 10:03:14', '2025-09-13 10:03:14'),
(247, NULL, 'Dr Test', '', '', 'Test Hospital', '9999999999', '', '', '', '', 20.00, 4, '2025-09-14 02:28:05', '2025-09-14 02:28:05'),
(248, NULL, 'yyy', '', '', 'gghh', '5555', '', '', 'gg', '', 20.00, 3, '2025-09-14 02:47:18', '2025-09-14 02:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `entry_date` datetime NOT NULL,
  `result_value` text DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `grouped` tinyint(1) DEFAULT 0,
  `tests_count` int(11) DEFAULT 1,
  `test_ids` longtext DEFAULT NULL,
  `test_names` longtext DEFAULT NULL,
  `test_results` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `entries`
--

INSERT INTO `entries` (`id`, `patient_id`, `doctor_id`, `test_id`, `entry_date`, `result_value`, `unit`, `remarks`, `status`, `added_by`, `created_at`, `grouped`, `tests_count`, `test_ids`, `test_names`, `test_results`) VALUES
(1, 1, 1, 1, '2025-01-15 10:30:00', '5.6', 'mg/dL', 'Normal glucose level', 'completed', 1, '2025-09-14 06:38:59', 0, 1, '[1]', '[\"Glucose Test\"]', '[\"5.6\"]'),
(2, 2, 1, 2, '2025-01-15 11:00:00', '120/80', 'mmHg', 'Normal blood pressure', 'completed', 1, '2025-09-14 06:38:59', 0, 1, '[2]', '[\"Blood Pressure\"]', '[\"120/80\"]'),
(3, 3, 2, 3, '2025-01-15 14:30:00', '7.2', 'mg/dL', 'Elevated glucose, follow-up needed', 'pending', 1, '2025-09-14 06:38:59', 0, 1, '[3]', '[\"Glucose Test\"]', '[\"7.2\"]'),
(4, 4, 1, 1, '2025-01-16 09:15:00', '4.8', 'mg/dL', 'Good control', 'completed', 1, '2025-09-14 06:38:59', 0, 1, '[1]', '[\"Glucose Test\"]', '[\"4.8\"]'),
(5, 5, 2, 4, '2025-01-16 10:45:00', '140/90', 'mmHg', 'Slightly elevated, monitor', 'pending', 1, '2025-09-14 06:38:59', 0, 1, '[4]', '[\"Blood Pressure\"]', '[\"140/90\"]'),
(6, 6, 1, 5, '2025-01-16 14:20:00', '7500', 'cells/mcl', 'WBC count within normal range', 'completed', 1, '2025-09-14 06:38:59', 1, 3, '[5,6,7]', '[\"WBC Count\",\"RBC Count\",\"Platelet Count\"]', '[\"7500\",\"4.5\",\"250000\"]');

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
(5, 'Happy New Years', '', '2025-12-31 23:52:00', '2026-01-01 23:51:00', 1, 1, '2025-08-27 03:41:02', '2025-08-27 05:23:33'),
(6, 'Testing notice', 'Test content', '2025-09-02 23:40:00', '2025-10-11 23:40:00', 1, 1, '2025-09-02 23:40:36', '2025-09-11 15:30:50');

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
(3, 'Support (Umakant Yadav)', '+91-9453619260', '+91-9453619260', 'uky171991@gmail.com', '', 1, '2025-08-26 13:19:00', '2025-09-07 18:29:53');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(128) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(1, 1, '73ef1b644d56690eaf6a209ac296150eba3c2823d5bde5a9235794ed67a535b4', '2025-09-09 01:12:09', 1, '2025-09-09 06:12:09'),
(2, 1, '93e06dd7a2f4a458f45cbda44d46dacab1138da388673874481671eab001fd46', '2025-09-09 01:15:23', 1, '2025-09-09 06:15:23'),
(3, 1, '72a39c6b4b144b9ed1b0a60f00d52df2ea315dded871dfd4b5e45535a93b73a7', '2025-09-09 01:19:44', 1, '2025-09-09 06:19:44'),
(4, 1, '82a120a966b55ed082143d0fa18e5c1498644afbe5c90d626c1385cef30098a2', '2025-09-09 01:21:26', 1, '2025-09-09 06:21:26');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(20) NOT NULL,
  `father_husband` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `age_unit` varchar(20) DEFAULT NULL,
  `uhid` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `email`, `mobile`, `father_husband`, `address`, `sex`, `age`, `age_unit`, `uhid`, `created_at`, `added_by`, `updated_at`) VALUES
(28, 'Test allk', NULL, '34344334', NULL, 'sdfg', 'Male', 50, 'Years', 'rtyu', '2025-09-09 12:06:26', 2, '2025-09-10 08:40:56'),
(38, 'Test 1', NULL, '565656', NULL, 'wertyh', 'Male', 25, 'Years', 'qwe', '2025-09-10 14:14:18', 3, '2025-09-12 05:06:24'),
(39, 'P2', NULL, '5565665', NULL, '456yu', 'Male', 25, 'Years', 'qw', '2025-09-11 05:42:20', 3, '2025-09-11 00:12:20'),
(88, 'test alk', NULL, '45555', NULL, 'dc', 'Male', 25, 'Years', '2025000001', '2025-09-13 15:34:04', 2, '2025-09-13 10:04:04'),
(89, 'test 2', NULL, '4546', NULL, 'wer', 'Male', 25, 'Years', '2025000002', '2025-09-13 15:35:02', 2, '2025-09-13 10:05:02'),
(90, 'Test 3', NULL, '7676676767', NULL, 'rfg', 'Male', 25, 'Years', 'g', '2025-09-13 15:42:54', 3, '2025-09-13 12:01:51');

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
  `qr_code` varchar(255) DEFAULT NULL,
  `added_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `description`, `price`, `upi`, `time_type`, `start_date`, `end_date`, `qr_code`, `added_by`, `created_at`, `updated_at`) VALUES
(1, 'Yearly Plan', '', 2999.00, '8081674028@upi', 'yearly', NULL, NULL, 'uploads/qr/1756541847_ca675b2b7067.jpg', 1, '2025-08-27 05:25:32', '2025-08-30 13:47:27'),
(2, 'Basic Plan', '', 299.00, '8081674028@upi', 'monthly', NULL, NULL, 'uploads/qr/1756541839_7947cffc3422.jpg', 1, '2025-08-27 05:26:40', '2025-08-30 13:47:19');

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
  `max_female` decimal(10,2) DEFAULT NULL,
  `specimen` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `name`, `description`, `category_id`, `price`, `unit`, `default_result`, `reference_range`, `min`, `max`, `sub_heading`, `test_code`, `method`, `print_new_page`, `shortcut`, `added_by`, `min_male`, `max_male`, `min_female`, `max_female`, `specimen`) VALUES
(1, 'Igor Hull', 'Molestiae quia dolor', 59, 803.00, 'Maiores laboris moll', '', '', 35.00, 64.00, 0, '', 'Fugit deleniti et m', 1, '', 1, 30.00, 95.00, 72.00, 78.00, NULL),
(2, 'Blair Curry', 'Ut labore nesciunt', 59, 649.00, 'Dolorem amet aut no', '', '', 81.00, 100.00, 0, '', 'Tempor provident al', 1, '', 1, 24.00, 78.00, 60.00, 80.00, NULL);

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
  `user_type` text NOT NULL,
  `added_by` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `api_token` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `user_type`, `added_by`, `is_active`, `created_at`, `last_login`, `expire_date`, `updated_at`, `api_token`) VALUES
(1, 'umakant', '$2y$10$FviBcCP/ukXwL2B.a64gpuTRaOv7Rr9mreDi8maNoyKPw/FzdBISm', 'Umakant Yadav', 'umakant171991@gmail.com', 'master', '', 1, 1, '2025-08-25 09:47:40', '2025-09-14 04:11:18', NULL, '2025-09-09 00:51:43', '7ffe5020ef63cb74690cc1b5afd066db57e72bda4aaab33e59282c4ae58178fa'),
(2, 'alok', '$2y$10$Ip95EmIgT99MSwlk4wYrxOg8BFg4T.tIOEUtx953ITel5FLeN94zi', 'Alok Yadav', 'alok@gmail.com', 'user', 'Pathology', 1, 1, '2025-08-28 09:56:05', NULL, '2025-09-24 15:44:00', '2025-08-25 09:56:05', '7c412bf89e6b70fbf114c42961035d84a98b01b0cedd728db53400c47b7f423a'),
(3, 'uma', '$2y$10$JSbe8vwmhMXYDIzhZ11yZuiGbyfXx55hC1C4Q8GYlsAECYfKriX8K', 'Uma Yadav', 'umakant171991@gmail.com', 'user', 'Pathology', 1, 1, '2025-08-30 09:57:26', NULL, '2025-09-13 15:50:00', '2025-08-25 09:57:26', 'ec197b6d46257de56b80fe3b92ddd44b0d9467a6bc5d70fbfd767a4377e44c28'),
(4, 'ghayas', '$2y$10$2F4b19rus1Cdz57fQN5Hie5mje7vocZSmFy4sVyixwZ/1eXXBi7WG', 'Ghayas', 'ghayasahmad522@gmail.com', 'admin', '', 1, 1, '2025-09-02 07:04:53', NULL, NULL, '2025-09-02 07:04:53', NULL),
(5, 'Test', '$2y$10$P2tecM1j41r0mMQwtmVwUuG/20KMJSSvDDGaPXPY2lQ9.GihsOp/y', 'Harrison Mcpherson', 'hinew@mailinator.com', 'user', 'Pathology', 1, 1, '2025-09-08 10:54:11', NULL, '2025-09-27 11:14:00', '2025-09-08 10:54:11', '334d18c98ca59be415c5c3b7ee08c66e502975bbf7a7c0eeb3caeff478b1c360'),
(6, 'test1', '$2y$10$xg08.ykhBcH8rN0ju8xfm.6DhD8349Bq72CCddI4YGT3hM2c6L/ve', '', '', 'master', '', 1, 1, '2025-09-11 10:50:55', '2025-09-11 10:51:07', '2025-12-06 16:20:00', '2025-09-11 10:50:55', '28ac51567f73d2426f04a07657ca32ba2c762ce364afc1235538aa42221caddd'),
(7, 'hyguwuweke', '$2y$10$eki.QxC6C/0XxGNvB68deuQQOJdkGAP2.FDRZNO/brKtoiHL.7EVy', 'Ciara Schroeder', 'viju@mailinator.com', 'user', 'Pathology', 1, 1, '2025-09-14 04:11:11', NULL, '2025-10-14 09:41:00', '2025-09-14 04:11:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `zip_uploads`
--

CREATE TABLE `zip_uploads` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `relative_path` varchar(512) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'uploaded',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zip_uploads`
--

INSERT INTO `zip_uploads` (`id`, `file_name`, `original_name`, `relative_path`, `mime_type`, `file_size`, `uploaded_by`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(8, '1756474617_Pathology_Management_Software.exe', 'Pathology Management Software.exe', 'uploads/1756474617_Pathology_Management_Software.exe', 'application/x-dosexec', 12996041, 1, 'uploaded', NULL, '2025-08-29 19:06:57', '2025-08-29 19:06:57'),
(9, '1756632021_Pathology_Management_Software.exe', 'Pathology Management Software.exe', 'uploads/1756632021_Pathology_Management_Software.exe', 'application/x-dosexec', 12996041, 1, 'uploaded', NULL, '2025-08-31 14:50:21', '2025-08-31 14:50:21');

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
  ADD KEY `idx_doctors_email` (`email`),
  ADD KEY `idx_doctors_server_id` (`server_id`);

--
-- Indexes for table `entries`
--
ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `idx_test_id` (`test_id`),
  ADD KEY `idx_entry_date` (`entry_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_added_by` (`added_by`),
  ADD KEY `idx_grouped` (`grouped`);

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
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `token` (`token`);

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
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_api_token` (`api_token`);

--
-- Indexes for table `zip_uploads`
--
ALTER TABLE `zip_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `zip_uploads`
--
ALTER TABLE `zip_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
