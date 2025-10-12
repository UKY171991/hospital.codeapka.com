-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 12, 2025 at 04:38 AM
-- Server version: 11.8.3-MariaDB-log
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `added_by`, `created_at`, `updated_at`) VALUES
(3, 'asd', 'qw', 1, '2025-09-27 10:14:02', '2025-10-10 13:12:26'),
(5, 'erfg  hh', 'qws', 1, '2025-09-27 10:30:03', '2025-09-27 10:30:11'),
(6, 'Sydnee Levine hh', 'Eos et in lorem non', 1, '2025-09-28 10:02:38', '2025-09-28 10:02:50');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `server_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `hospital` varchar(255) DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `registration_no` varchar(100) DEFAULT NULL,
  `percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `server_id`, `name`, `qualification`, `specialization`, `hospital`, `contact_no`, `phone`, `email`, `address`, `registration_no`, `percent`, `added_by`, `created_at`, `updated_at`) VALUES
(6, NULL, 'Wade Solomon', '', '', 'Quas nostrud quibusd', 'Assumenda et suscipi', '', '', 'Ipsum labore eaque a', '', 57.00, 1, '2025-09-27 17:08:37', '2025-10-10 12:38:34'),
(7, NULL, 'Constance Conrad', NULL, NULL, 'Nostrud obcaecati co', 'Ipsa iusto totam oc', NULL, NULL, 'Porro eum irure odio', NULL, 60.00, 1, '2025-09-27 17:10:52', '2025-09-27 17:10:52'),
(8, NULL, 'Sylvester Harmon', NULL, NULL, 'Quia et repellendus', 'Cum occaecat dicta u', NULL, NULL, 'Fugiat quas reiciend', NULL, 24.00, 1, '2025-09-27 17:13:41', '2025-09-27 17:13:41'),
(9, NULL, 'sdef', NULL, NULL, 'qw', 'qw', NULL, NULL, 'qw', NULL, 4.00, 1, '2025-09-27 17:18:42', '2025-09-27 17:18:42'),
(10, NULL, 'Malcolm Callahan', NULL, NULL, 'Vero qui esse omnis', 'Quis consectetur si', NULL, NULL, 'Adipisicing nobis te', NULL, 34.00, 1, '2025-09-27 17:19:16', '2025-09-27 17:19:16'),
(11, NULL, 'Candace Lowe', NULL, NULL, 'Sunt cum expedita l', 'Rerum qui id fuga E', NULL, NULL, 'Cumque soluta sint', NULL, 83.00, 1, '2025-09-27 17:26:52', '2025-09-27 17:26:52'),
(12, NULL, 'Alma Cooke', NULL, NULL, 'Quia et rerum totam', 'Cupiditate sunt et', NULL, NULL, 'Dolor soluta quibusd', NULL, 96.00, 1, '2025-09-27 17:27:21', '2025-09-27 17:27:21'),
(13, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, 'Ea vero eos et aut t', NULL, 44.00, 2, '2025-09-28 11:32:44', '2025-09-28 11:32:44'),
(14, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, 'Tempore proident s', NULL, 41.00, 2, '2025-09-28 11:41:15', '2025-09-28 11:41:15'),
(15, NULL, 'ASD', NULL, NULL, 'QW', 'Q', NULL, NULL, 'Q', NULL, 4.00, 1, '2025-09-28 11:44:10', '2025-10-11 16:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `server_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `entry_date` datetime DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `priority` varchar(50) DEFAULT 'normal',
  `referral_source` varchar(100) DEFAULT NULL,
  `patient_contact` varchar(100) DEFAULT NULL,
  `patient_address` text DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `age` int(11) DEFAULT NULL COMMENT 'Patient age at time of entry',
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `payment_status` varchar(50) DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `entries`
--

INSERT INTO `entries` (`id`, `owner_id`, `server_id`, `patient_id`, `doctor_id`, `entry_date`, `status`, `priority`, `referral_source`, `patient_contact`, `patient_address`, `gender`, `age`, `subtotal`, `discount_amount`, `total_price`, `payment_status`, `notes`, `added_by`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 3, NULL, '2025-10-06 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 100.00, 20.00, 100.00, 'pending', NULL, 1, '2025-10-06 14:13:29', NULL),
(2, 2, NULL, 2, 15, '2025-10-08 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 100.00, 0.00, 60.00, 'pending', NULL, 2, '2025-10-06 15:53:42', NULL),
(5, 1, NULL, 3, NULL, '2025-10-05 00:00:00', 'completed', 'normal', NULL, NULL, NULL, NULL, NULL, 200.00, 0.00, 300.00, 'pending', NULL, 1, '2025-10-06 16:21:47', NULL),
(6, 1, NULL, 3, NULL, '2025-10-05 00:00:00', 'completed', 'normal', NULL, NULL, NULL, NULL, NULL, 1080.00, 100.00, 980.00, 'pending', NULL, 1, '2025-10-06 16:40:50', NULL),
(7, 1, NULL, 3, NULL, '2025-10-05 00:00:00', 'completed', 'normal', NULL, NULL, NULL, NULL, NULL, 400.00, 0.00, 300.00, 'pending', NULL, 1, '2025-10-06 17:12:12', NULL),
(9, 2, NULL, 2, 14, '2025-10-08 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 500.00, 0.00, 300.00, 'pending', NULL, 2, '2025-10-08 11:21:38', NULL),
(10, 1, NULL, 3, 12, '2025-10-08 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 600.00, 0.00, 340.00, 'pending', NULL, 1, '2025-10-08 17:09:55', NULL),
(11, 1, NULL, NULL, 12, '2025-10-08 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 300.00, 0.00, 300.00, 'pending', NULL, 1, '2025-10-08 17:51:17', NULL),
(12, 1, NULL, 3, 12, '2025-10-08 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'pending', NULL, 1, '2025-10-08 18:00:34', NULL),
(13, 1, NULL, NULL, 12, '2025-10-09 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'pending', NULL, 1, '2025-10-09 08:15:03', NULL),
(14, 1, NULL, NULL, 12, '2025-10-09 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 'pending', NULL, 1, '2025-10-09 08:15:35', NULL),
(15, 1, NULL, 3, 12, '2025-10-09 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 1080.00, 0.00, 1080.00, 'pending', NULL, 1, '2025-10-09 08:16:19', NULL),
(16, 1, NULL, NULL, 12, '2025-10-09 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 1080.00, 50.00, 1030.00, 'pending', NULL, 1, '2025-10-09 08:21:40', NULL),
(17, 1, NULL, 3, 12, '2025-10-09 00:00:00', 'pending', 'normal', NULL, NULL, NULL, NULL, NULL, 100.00, 20.00, 80.00, 'pending', NULL, 1, '2025-10-09 08:30:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `entry_tests`
--

CREATE TABLE `entry_tests` (
  `id` int(10) UNSIGNED NOT NULL,
  `entry_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `result_value` varchar(255) DEFAULT NULL,
  `unit` varchar(64) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `price` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `entry_tests`
--

INSERT INTO `entry_tests` (`id`, `entry_id`, `test_id`, `result_value`, `unit`, `remarks`, `status`, `price`, `discount_amount`, `total_price`, `created_at`) VALUES
(1, 1, 2, '40', 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-06 14:13:29'),
(8, 5, 2, NULL, 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-06 16:21:47'),
(9, 5, 1, NULL, 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-06 16:21:47'),
(12, 7, 2, '40', 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-06 17:12:12'),
(13, 7, 1, '50', 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-06 17:12:12'),
(14, 8, 2, '40', 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-07 07:20:16'),
(15, 8, 1, '50', 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-07 07:20:16'),
(18, 2, 2, '40', 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-08 11:09:43'),
(19, 2, 1, '50', 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-08 11:09:43'),
(20, 9, 1, NULL, 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-08 11:21:38'),
(21, 9, 2, NULL, 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-08 11:21:38'),
(34, 10, 1, NULL, 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-08 17:50:21'),
(35, 10, 2, NULL, 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-08 17:50:21'),
(56, 12, 1, NULL, 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-09 04:53:46'),
(57, 12, 2, NULL, 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-09 04:53:46'),
(58, 11, 1, NULL, 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-09 08:00:55'),
(59, 11, 2, NULL, 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-09 08:00:55'),
(60, 13, 1, NULL, 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-09 08:15:03'),
(61, 14, 1, NULL, 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-09 08:15:35'),
(62, 14, 2, NULL, 'etc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-09 08:15:35'),
(70, 15, 1, NULL, 'abc', NULL, 'pending', 0.00, 0.00, 980.00, '2025-10-09 15:01:48'),
(71, 15, 2, NULL, 'etc', NULL, 'pending', 0.00, 0.00, 100.00, '2025-10-09 15:01:48'),
(73, 16, 2, '40', 'etc', NULL, 'pending', 0.00, 0.00, 100.00, '2025-10-09 15:18:15'),
(74, 16, 1, '50', 'abc', NULL, 'pending', 0.00, 0.00, 980.00, '2025-10-09 15:18:15'),
(78, 17, 2, '40', 'etc', NULL, 'pending', 0.00, 0.00, 100.00, '2025-10-09 16:01:41'),
(79, 6, 1, NULL, 'abc', NULL, 'pending', 0.00, 0.00, 980.00, '2025-10-10 06:44:42'),
(80, 6, 2, NULL, 'etc', NULL, 'pending', 0.00, 0.00, 100.00, '2025-10-10 06:44:42');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `server_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `server_id`, `title`, `content`, `start_date`, `end_date`, `active`, `added_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Dolores irure non qu', 'Temporibus itaque re', '1990-03-11 14:49:00', '2017-12-22 21:06:00', 1, 1, '2025-10-10 19:14:13', NULL),
(2, NULL, 'Magna sint est vel', 'Qui praesentium eos', '1988-01-10 01:37:00', '1979-10-28 00:26:00', 1, 1, '2025-10-10 19:14:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `server_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `link` text NOT NULL,
  `added_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id`, `server_id`, `name`, `phone`, `whatsapp`, `email`, `address`, `link`, `added_by`, `created_at`, `updated_at`) VALUES
(4, NULL, 'Support (Umakant Yadav)', '9453619260', '9453619260', '', '', 'https://hospital.codeapka.com/', 1, NULL, NULL);

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
  `server_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `father_husband` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `contact` varchar(100) NOT NULL,
  `age_unit` varchar(10) DEFAULT 'Years',
  `uhid` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `server_id`, `name`, `email`, `mobile`, `father_husband`, `address`, `sex`, `age`, `contact`, `age_unit`, `uhid`, `created_at`, `updated_at`, `added_by`) VALUES
(1, NULL, 'Naida Marquez', 'jejasiduw@mailinator.com', '5454455454', 'Indigo Holman', 'Voluptate quibusdam', 'Male', 45, '', 'Months', 'P033679005', '2025-09-28 12:02:58', '2025-09-28 13:52:46', 2),
(2, NULL, 'James Sears', 'ciwi@mailinator.com', '5656565656', 'Arthur Oneal', 'Vel nobis error corr', 'Female', 10, '', 'Years', 'P642622065', '2025-09-28 12:27:50', '2025-09-28 13:52:29', 2),
(3, NULL, 'Indigo Cortez', 'feloz@mailinator.com', '5454545454', 'Odette Villarreal', 'Et quaerat voluptati', NULL, 86, '', 'Days', 'P483791824', '2025-09-28 13:31:49', '2025-10-10 15:46:54', 1);

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
(2, 'Basic Plan', '', 299.00, '8081674028@upi', 'monthly', NULL, NULL, 'uploads/qr/1756541839_7947cffc3422.jpg', 1, '2025-08-27 05:26:40', '2025-10-11 05:03:23');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(50) DEFAULT NULL,
  `specimen` varchar(255) DEFAULT NULL,
  `default_result` text DEFAULT NULL,
  `reference_range` varchar(255) DEFAULT NULL,
  `min` decimal(10,2) DEFAULT NULL,
  `max` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `min_male` decimal(10,2) DEFAULT NULL,
  `max_male` decimal(10,2) DEFAULT NULL,
  `min_female` decimal(10,2) DEFAULT NULL,
  `max_female` decimal(10,2) DEFAULT NULL,
  `sub_heading` tinyint(1) NOT NULL DEFAULT 0,
  `test_code` varchar(100) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `print_new_page` tinyint(1) NOT NULL DEFAULT 0,
  `shortcut` varchar(50) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `name`, `category_id`, `price`, `unit`, `specimen`, `default_result`, `reference_range`, `min`, `max`, `description`, `min_male`, `max_male`, `min_female`, `max_female`, `sub_heading`, `test_code`, `method`, `print_new_page`, `shortcut`, `added_by`, `created_at`, `updated_at`) VALUES
(1, 'Aubrey Reyes g', 3, 980.00, 'abc', NULL, '', '', 20.00, 24.00, 'Aliquid labore place', 30.00, 31.00, 72.00, 78.00, 1, '', '', 1, '', 1, NULL, NULL),
(2, 'New test', 6, 100.00, 'etc', NULL, '', '', 10.00, 20.00, '', 11.00, 21.00, 12.00, 22.00, 0, '', 'none', 0, '', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `user_type` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  `api_token` text NOT NULL,
  `added_by` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `is_active`, `user_type`, `created_at`, `last_login`, `expire_date`, `api_token`, `added_by`, `updated_at`) VALUES
(1, 'umakant', '$2y$12$8RovPoAOxY30weFvoSKJD.aabD27dV8cHbqON2XTQ04x1fs/Tw1da', 'Umakant Yadav', 'umakant171991@gmail.com', 'master', 1, 'Pathology', '2025-09-26 10:12:24', '2025-10-12 09:37:05', '2025-10-26 10:12:00', '', '0000-00-00 00:00:00', '2025-09-26 04:42:48'),
(2, 'uma', '$2y$12$yBaDoENR.9MOXDLizW.UYunvNev1XOICwYC.WNCRmPEd1fQ5TS85q', 'Uma Yadav', 'umakant171992@gmail.com', 'user', 1, 'Pathology', '2025-09-26 10:13:58', NULL, '2025-10-11 10:13:00', '', '0000-00-00 00:00:00', '2025-09-26 04:43:58');

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
  ADD UNIQUE KEY `ux_categories_name` (`name`),
  ADD KEY `idx_categories_added_by` (`added_by`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctors_name` (`name`),
  ADD KEY `idx_doctors_contact` (`contact_no`),
  ADD KEY `idx_doctors_email` (`email`),
  ADD KEY `fk_doctors_user` (`added_by`);

--
-- Indexes for table `entries`
--
ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entries_patient` (`patient_id`),
  ADD KEY `idx_entries_doctor` (`doctor_id`),
  ADD KEY `idx_entries_added_by` (`added_by`),
  ADD KEY `idx_entries_entry_date` (`entry_date`),
  ADD KEY `idx_entries_status` (`status`),
  ADD KEY `idx_entries_owner_id` (`owner_id`),
  ADD KEY `idx_entries_patient_id` (`patient_id`),
  ADD KEY `idx_entries_doctor_id` (`doctor_id`),
  ADD KEY `idx_entries_priority` (`priority`),
  ADD KEY `idx_entries_referral_source` (`referral_source`);

--
-- Indexes for table `entry_tests`
--
ALTER TABLE `entry_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_entry_id` (`entry_id`),
  ADD KEY `idx_test_id` (`test_id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notices_active` (`active`),
  ADD KEY `idx_notices_added_by` (`added_by`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_owners_name` (`name`),
  ADD KEY `idx_owners_phone` (`phone`);

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
  ADD KEY `idx_patients_name` (`name`),
  ADD KEY `idx_patients_mobile` (`mobile`),
  ADD KEY `idx_patients_uhid` (`uhid`),
  ADD KEY `idx_patients_added_by` (`added_by`);

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
  ADD KEY `idx_tests_name` (`name`),
  ADD KEY `idx_tests_category` (`category_id`),
  ADD KEY `idx_tests_added_by` (`added_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `entry_tests`
--
ALTER TABLE `entry_tests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `zip_uploads`
--
ALTER TABLE `zip_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_user` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `fk_doctors_user` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `entries`
--
ALTER TABLE `entries`
  ADD CONSTRAINT `fk_entries_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_entries_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_entries_user` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `fk_notices_user` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_user` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_user` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `fk_tests_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tests_user` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
