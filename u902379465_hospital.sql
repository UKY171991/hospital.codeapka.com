-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 10, 2025 at 09:55 AM
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
(45, NULL, 'Ramu', '', '', 'test', '565666', '56565656', '', '', '', 30.00, 3, '2025-09-04 06:07:00', '2025-09-07 14:51:26'),
(46, NULL, 'Ramu kamal', '', '', 'test 1', '565666', '56565656', '', '', '', 40.00, 3, '2025-09-04 06:09:31', '2025-09-04 06:55:08'),
(233, NULL, 'Dr Test', '', '', 'Test Hospital', '9999999999', '', '', '', '', 20.00, 3, '2025-09-09 05:44:34', '2025-09-09 23:18:34');

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
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
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `entries`
--

INSERT INTO `entries` (`id`, `patient_id`, `test_id`, `doctor_id`, `entry_date`, `test_date`, `reported_date`, `result_value`, `result_status`, `unit`, `remarks`, `status`, `added_by`, `created_at`, `updated_at`) VALUES
(1, 1, 35, 31, '2025-09-10 14:30:00', '2025-09-10', '2025-09-10 14:30:00', '7500', 'normal', 'cells/mcl', 'WBC count within normal range', 'completed', 1, '2025-09-10 09:27:24', '2025-09-10 09:27:24'),
(2, 1, 36, 31, '2025-09-10 14:35:00', '2025-09-10', '2025-09-10 14:35:00', '250000', 'normal', 'cells/mcl', 'Platelet count normal', 'completed', 1, '2025-09-10 09:27:24', '2025-09-10 09:27:24'),
(3, 1, 37, 31, '2025-09-10 14:40:00', '2025-09-10', '2025-09-10 14:40:00', '42', 'normal', '%', 'Hematocrit normal', 'completed', 1, '2025-09-10 09:27:24', '2025-09-10 09:27:24');

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
(6, 'Testing notice', 'Test content', '2025-09-02 23:40:00', '2025-10-11 23:40:00', 1, 1, '2025-09-02 23:40:36', NULL);

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
(37, 'Test 1', NULL, '565656', NULL, 'wertyh', 'Male', 25, 'Years', 'qwe', '2025-09-10 14:09:46', 3, '2025-09-10 08:39:46'),
(38, 'Test 1', NULL, '565656', NULL, 'wertyh', 'Male', 25, 'Years', 'qwe', '2025-09-10 14:14:18', 3, '2025-09-10 08:44:18');

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
(35, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', '', '', 4500.00, 10000.00, 0, '', '', 0, '', 1, 4500.00, 10000.00, 4500.00, 10000.00, NULL),
(36, 'Platelets', '', 59, 0.00, '', '', '', 0.00, 0.00, 0, '', '', 0, '', 1, 0.00, 0.00, 0.00, 0.00, NULL),
(37, 'Hematocrit Test', '', 59, 0.00, '', '', '', 0.00, 0.00, 0, '', '', 0, '', 1, 0.00, 0.00, 0.00, 0.00, NULL),
(38, 'Hemoglobin Test', '', 59, 0.00, '', '', '', 0.00, 0.00, 0, '', '', 0, '', 1, 0.00, 0.00, 0.00, 0.00, NULL),
(39, 'RDW Blood Test', '', 59, 0.00, '', '', '', 0.00, 0.00, 0, '', '', 0, '', 1, 0.00, 0.00, 0.00, 0.00, NULL),
(40, 'MCV Blood Test', '', 59, 0.00, '', '', '', 0.00, 0.00, 0, '', '', 0, '', 1, 0.00, 0.00, 0.00, 0.00, NULL),
(41, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', '', '', 0.00, 0.00, 0, '', '', 0, '', 1, 4.70, 6.10, 4.20, 5.40, NULL),
(42, 'Hematocrit', '', 59, 0.00, '%', '', '', 40.70, 50.30, 0, '', '', 0, '', 1, 40.70, 50.30, 36.10, 44.30, NULL),
(43, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', '', '', 13.80, 17.20, 0, '', '', 0, '', 1, 13.80, 17.20, 12.10, 15.10, NULL),
(44, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', '', '', 150000.00, 400000.00, 0, '', '', 0, '', 1, 150000.00, 400000.00, 150000.00, 400000.00, NULL),
(46, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', '', '', 30.00, 62.00, 1, '', 'Id elit aperiam est', 1, '', 1, 25.00, 38.00, 12.00, 18.00, NULL),
(47, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(133, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(134, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(135, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(138, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(139, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(140, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(141, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(142, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(143, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(144, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(145, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(146, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(147, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(148, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(149, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(150, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(151, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(152, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(153, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(154, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(155, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(156, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(157, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(158, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(159, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(160, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(161, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(162, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(163, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(164, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(165, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(166, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(167, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(168, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(169, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(170, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(171, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(172, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(173, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(174, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(175, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(176, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(177, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(178, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(179, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(180, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(181, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(182, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(183, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(184, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(185, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(186, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(187, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(188, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(189, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(190, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(191, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(192, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(193, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(194, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(195, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(196, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(197, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(198, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(199, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(200, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(201, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(202, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(203, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(204, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(205, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(206, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(207, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(208, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(209, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(210, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(211, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(212, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(213, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(214, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(215, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(216, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(217, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(218, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(219, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(220, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(221, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(222, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(223, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(224, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(225, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(226, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(227, 'Hematocrit', '', 59, 0.00, '%', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(228, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(229, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(230, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(231, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(232, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(233, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(234, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(235, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(236, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(237, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(238, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(239, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(240, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(241, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(242, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(243, 'Hematocrit Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(244, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(245, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(246, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(247, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(248, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(249, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(250, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(251, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(252, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(253, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(254, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(255, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(256, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(257, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(258, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(259, 'Hemoglobin', '', 59, 0.00, 'grams/deciliter', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(260, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(261, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(262, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(263, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(264, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(265, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(266, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(267, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(268, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(269, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(270, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(271, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(272, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(273, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(274, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(275, 'Hemoglobin Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(276, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(277, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(278, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(279, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(280, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(281, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(282, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(283, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(284, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(285, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(286, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(287, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(288, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(289, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(290, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(291, 'Kirsten Garcia', 'Qui et dolor ab volu', 59, 169.00, 'Debitis occaecat et', NULL, NULL, NULL, NULL, 0, NULL, 'Id elit aperiam est', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(292, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(293, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(294, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(295, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(296, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(297, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(298, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(299, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(300, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(301, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(302, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(303, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(304, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(305, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(306, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(307, 'MCV Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(308, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(309, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(310, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(311, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(312, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(313, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(314, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(315, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(316, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(317, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(318, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(319, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(320, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(321, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(322, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(323, 'Platelet Count (Thrombocytes)', '', 59, 0.00, 'per mm3', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(324, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(325, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(326, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(327, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(328, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(329, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(330, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(331, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(332, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(333, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(334, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(335, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(336, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(337, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(338, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(339, 'Platelets', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(340, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(341, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(342, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(343, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(344, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(345, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(346, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(347, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(348, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(349, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(350, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(351, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(352, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(353, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(354, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(355, 'RDW Blood Test', '', 59, 0.00, '', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(356, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(357, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(358, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(359, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(360, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(361, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(362, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(363, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(364, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(365, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(366, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(367, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(368, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(369, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(370, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(371, 'Red Blood Cell Count (RBCs)', '', 59, 100.00, 'million cells/mcL', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(372, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(373, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(374, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(375, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(376, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(377, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(378, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(379, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(380, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(381, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(382, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(383, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(384, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(385, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(386, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(387, 'White Blood Cells(WBC)', '', 59, 0.00, 'cells/mcl', NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `api_token` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `added_by`, `is_active`, `created_at`, `last_login`, `expire_date`, `updated_at`, `api_token`) VALUES
(1, 'umakant', '$2y$10$FviBcCP/ukXwL2B.a64gpuTRaOv7Rr9mreDi8maNoyKPw/FzdBISm', 'Umakant Yadav', 'umakant171991@gmail.com', 'master', 1, 1, '2025-08-25 09:47:40', '2025-09-10 07:41:36', NULL, '2025-09-09 00:51:43', '7ffe5020ef63cb74690cc1b5afd066db57e72bda4aaab33e59282c4ae58178fa'),
(2, 'alok', '$2y$10$Ip95EmIgT99MSwlk4wYrxOg8BFg4T.tIOEUtx953ITel5FLeN94zi', 'Alok Yadav', 'alok@gmail.com', 'user', 1, 1, '2025-08-28 09:56:05', '2025-09-10 08:40:11', '2025-09-24 15:44:58', '2025-08-25 09:56:05', '7c412bf89e6b70fbf114c42961035d84a98b01b0cedd728db53400c47b7f423a'),
(3, 'uma', '$2y$10$xtu6M0jNNnPvbm9GTabuCO69DVfN6WuMBhMxSN1dYPvqw2j.uMC66', 'Uma Yadav', 'umakant171991@gmail.com', 'user', 1, 1, '2025-08-30 09:57:26', '2025-09-10 08:52:00', '2025-09-30 15:50:00', '2025-08-25 09:57:26', 'ec197b6d46257de56b80fe3b92ddd44b0d9467a6bc5d70fbfd767a4377e44c28'),
(4, 'ghayas', '$2y$10$2F4b19rus1Cdz57fQN5Hie5mje7vocZSmFy4sVyixwZ/1eXXBi7WG', 'Ghayas', 'ghayasahmad522@gmail.com', 'admin', 1, 1, '2025-09-02 07:04:53', NULL, NULL, '2025-09-02 07:04:53', NULL),
(5, 'sujezys', '$2y$10$37sQcPoVpG0Tl.MKABGvFOueHA1RL.9B0zgXnAYbaFaTkhslv37wK', 'Harrison Mcpherson', 'hinew@mailinator.com', 'user', 1, 1, '2025-09-08 10:54:11', NULL, '2011-02-05 11:14:00', '2025-09-08 10:54:11', NULL);

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
  ADD KEY `idx_test_id` (`test_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `idx_entry_date` (`entry_date`),
  ADD KEY `idx_status` (`status`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=388;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
