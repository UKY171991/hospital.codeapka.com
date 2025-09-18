-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 18, 2025 at 05:43 AM
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
(60, 'Basic Metabolic Panel (BMP) / Chem-7', '', 1, '2025-09-01 08:43:40', '2025-09-07 11:09:56'),
(65, 'Test Category 074617 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 05:46:17', '2025-09-16 05:46:19'),
(66, 'Test Category 075130 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 05:51:31', '2025-09-16 05:51:33'),
(67, 'Test Category 075211 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 05:52:12', '2025-09-16 05:52:14'),
(68, 'Test Category 075300 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 05:53:01', '2025-09-16 05:53:03'),
(69, 'Test Category 075604 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 05:56:05', '2025-09-16 05:56:06'),
(70, 'Test Category 075636 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 05:56:37', '2025-09-16 05:56:39'),
(71, 'Test Category 080120 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:01:21', '2025-09-16 06:01:23'),
(72, 'Test Category 081550 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:15:51', '2025-09-16 06:15:53'),
(73, 'Test Category 084328 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:43:29', '2025-09-16 06:43:31'),
(74, 'Test Category 084355 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:43:56', '2025-09-16 06:43:58'),
(75, 'Test Category 084423 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:44:23', '2025-09-16 06:44:25'),
(76, 'Test Category 084502 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:45:02', '2025-09-16 06:45:04'),
(77, 'Test Category 084539 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:45:40', '2025-09-16 06:45:42'),
(78, 'Test Category 084615 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:46:16', '2025-09-16 06:46:18'),
(80, 'Test Category 084805 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:48:06', '2025-09-16 06:48:08'),
(81, 'Test Category 084833 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:48:34', '2025-09-16 06:48:37'),
(86, 'Test Category 085138 UPDATED', 'Test category for duplicate prevention testing', 1, '2025-09-16 06:51:39', '2025-09-16 06:51:41');

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
(236, NULL, 'yy', '', '', 'sdfg', 'qa', '', '', 'q', '', 10.00, 2, '2025-09-11 08:04:29', '2025-09-11 08:04:29'),
(237, NULL, 'hhh', '', '', 'dfg', '4567', '', '', 'dfg', '', 60.00, 2, '2025-09-11 08:05:43', '2025-09-11 08:05:43'),
(243, NULL, 'Test1 Doctor', '', '', 'ttt', '56565656', '', '', 'ert', '', 30.00, 5, '2025-09-12 04:53:29', '2025-09-12 04:53:29'),
(244, NULL, 'hhh', '', '', 'sdfg', '23456', '', '', 'ertg', '', 50.00, 5, '2025-09-12 04:56:05', '2025-09-12 04:56:05'),
(245, NULL, 'Test d3', '', '', 'sdf', 'qw1we12e', '', '', 'qw', '', 40.00, 5, '2025-09-13 10:01:59', '2025-09-13 10:01:59'),
(246, NULL, 'Test d3', '', '', 'werf', '14567', '', '', 'f', '', 60.00, 2, '2025-09-13 10:03:14', '2025-09-13 10:03:14'),
(251, NULL, 'Dr. Test 074617 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543617', '', 'test074617@hospital.com', '', '', 0.00, 1, '2025-09-16 05:46:24', '2025-09-16 05:46:26'),
(252, NULL, 'Dr. Test 075130 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543130', '', 'test075130@hospital.com', '', '', 0.00, 1, '2025-09-16 05:51:38', '2025-09-16 05:51:40'),
(253, NULL, 'Dr. Test 075211 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543211', '', 'test075211@hospital.com', '', '', 0.00, 1, '2025-09-16 05:52:19', '2025-09-16 05:52:21'),
(254, NULL, 'Dr. Test 075300 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543300', '', 'test075300@hospital.com', '', '', 0.00, 1, '2025-09-16 05:53:08', '2025-09-16 05:53:10'),
(255, NULL, 'Dr. Test 075604 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543604', '', 'test075604@hospital.com', '', '', 0.00, 1, '2025-09-16 05:56:11', '2025-09-16 05:56:13'),
(256, NULL, 'Dr. Test 075636 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543636', '', 'test075636@hospital.com', '', '', 0.00, 1, '2025-09-16 05:56:44', '2025-09-16 05:56:46'),
(257, NULL, 'Dr. Test 080120 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543120', '', 'test080120@hospital.com', '', '', 0.00, 1, '2025-09-16 06:01:28', '2025-09-16 06:01:30'),
(258, NULL, 'Dr. Test 081550 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543550', '', 'test081550@hospital.com', '', '', 0.00, 1, '2025-09-16 06:15:58', '2025-09-16 06:16:00'),
(259, NULL, 'Dr. Test 084328 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543328', '', 'test084328@hospital.com', '', '', 0.00, 1, '2025-09-16 06:43:37', '2025-09-16 06:43:39'),
(260, NULL, 'Dr. Test 084355 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543355', '', 'test084355@hospital.com', '', '', 0.00, 1, '2025-09-16 06:44:04', '2025-09-16 06:44:06'),
(261, NULL, 'Dr. Test 084423 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543423', '', 'test084423@hospital.com', '', '', 0.00, 1, '2025-09-16 06:44:33', '2025-09-16 06:44:35'),
(262, NULL, 'Dr. Test 084502 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543502', '', 'test084502@hospital.com', '', '', 0.00, 1, '2025-09-16 06:45:11', '2025-09-16 06:45:13'),
(263, NULL, 'Dr. Test 084539 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543539', '', 'test084539@hospital.com', '', '', 0.00, 1, '2025-09-16 06:45:48', '2025-09-16 06:45:50'),
(264, NULL, 'Dr. Test 084615 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543615', '', 'test084615@hospital.com', '', '', 0.00, 1, '2025-09-16 06:46:25', '2025-09-16 06:46:26'),
(265, NULL, 'Dr. Test 084735 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543735', '', 'test084735@hospital.com', '', '', 0.00, 1, '2025-09-16 06:47:42', '2025-09-16 06:47:44'),
(266, NULL, 'Dr. Test 084805 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543805', '', 'test084805@hospital.com', '', '', 0.00, 1, '2025-09-16 06:48:13', '2025-09-16 06:48:15'),
(267, NULL, 'Dr. Test 084833 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543833', '', 'test084833@hospital.com', '', '', 0.00, 1, '2025-09-16 06:48:42', '2025-09-16 06:48:44'),
(268, NULL, 'Dr. Test 084901 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543901', '', 'test084901@hospital.com', '', '', 0.00, 1, '2025-09-16 06:49:08', '2025-09-16 06:49:10'),
(269, NULL, 'Dr. Test 084939 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543939', '', 'test084939@hospital.com', '', '', 0.00, 1, '2025-09-16 06:49:47', '2025-09-16 06:49:49'),
(270, NULL, 'Dr. Test 085020 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543020', '', 'test085020@hospital.com', '', '', 0.00, 1, '2025-09-16 06:50:27', '2025-09-16 06:50:29'),
(271, NULL, 'Dr. Test 085107 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543107', '', 'test085107@hospital.com', '', '', 0.00, 1, '2025-09-16 06:51:15', '2025-09-16 06:51:17'),
(272, NULL, 'Dr. Test 085138 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543138', '', 'test085138@hospital.com', '', '', 0.00, 1, '2025-09-16 06:51:46', '2025-09-16 06:51:48'),
(273, NULL, 'Dr. Test 085256 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543256', '', 'test085256@hospital.com', '', '', 0.00, 1, '2025-09-16 06:53:04', '2025-09-16 06:53:05'),
(274, NULL, 'Dr. Test 085326 UPDATED', 'MBBS', 'General Medicine', 'Test Hospital', '9876543326', '', 'test085326@hospital.com', '', '', 0.00, 1, '2025-09-16 06:53:34', '2025-09-16 06:53:36'),
(277, NULL, 'qwer', '', '', 'qw', 'qwe', '', '', 'qwe', '', 3.00, 3, '2025-09-16 18:01:55', '2025-09-18 00:58:37'),
(278, NULL, 'wedf', '', '', 'qs', 'qws', '', '', 'qs', '', 5.00, 3, '2025-09-16 18:02:26', '2025-09-18 00:58:39');

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
  `server_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `test_id` int(11) DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `result_value` text DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `added_by` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(6, 'Testing notice', 'Test content', '2025-09-02 23:40:00', '2025-10-11 23:40:00', 1, 1, '2025-09-02 23:40:36', '2025-09-11 15:30:50'),
(7, 'Test Notice 074617 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:46:29', NULL, 1, 1, '2025-09-16 11:16:27', '2025-09-16 11:16:29'),
(8, 'Test Notice 074617', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:46:28', NULL, 1, 1, '2025-09-16 11:16:28', NULL),
(9, 'Test Notice 075130 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:51:42', NULL, 1, 1, '2025-09-16 11:21:41', '2025-09-16 11:21:42'),
(10, 'Test Notice 075130', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:51:42', NULL, 1, 1, '2025-09-16 11:21:42', NULL),
(11, 'Test Notice 075211 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:52:24', NULL, 1, 1, '2025-09-16 11:22:22', '2025-09-16 11:22:24'),
(12, 'Test Notice 075211', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:52:23', NULL, 1, 1, '2025-09-16 11:22:23', NULL),
(13, 'Test Notice 075300 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:53:13', NULL, 1, 1, '2025-09-16 11:23:11', '2025-09-16 11:23:13'),
(14, 'Test Notice 075300', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:53:12', NULL, 1, 1, '2025-09-16 11:23:12', NULL),
(15, 'Test Notice 075604 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:56:16', NULL, 1, 1, '2025-09-16 11:26:14', '2025-09-16 11:26:16'),
(16, 'Test Notice 075604', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:56:15', NULL, 1, 1, '2025-09-16 11:26:15', NULL),
(17, 'Test Notice 075636 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:56:49', NULL, 1, 1, '2025-09-16 11:26:47', '2025-09-16 11:26:49'),
(18, 'Test Notice 075636', 'This is a test notice for duplicate prevention testing', '2025-09-16 05:56:48', NULL, 1, 1, '2025-09-16 11:26:48', NULL),
(19, 'Test Notice 080120 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:01:33', NULL, 1, 1, '2025-09-16 11:31:31', '2025-09-16 11:31:33'),
(20, 'Test Notice 080120', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:01:32', NULL, 1, 1, '2025-09-16 11:31:32', NULL),
(21, 'Test Notice 081550 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:16:03', NULL, 1, 1, '2025-09-16 11:46:01', '2025-09-16 11:46:03'),
(22, 'Test Notice 081550', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:16:02', NULL, 1, 1, '2025-09-16 11:46:02', NULL),
(24, 'Test Notice 084328', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:43:41', NULL, 1, 1, '2025-09-16 12:13:41', NULL),
(25, 'Test Notice 084355 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:44:09', NULL, 1, 1, '2025-09-16 12:14:07', '2025-09-16 12:14:09'),
(26, 'Test Notice 084355', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:44:08', NULL, 1, 1, '2025-09-16 12:14:08', NULL),
(27, 'Test Notice 084423 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:44:38', NULL, 1, 1, '2025-09-16 12:14:36', '2025-09-16 12:14:38'),
(28, 'Test Notice 084423', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:44:37', NULL, 1, 1, '2025-09-16 12:14:37', NULL),
(29, 'Test Notice 084502 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:16', NULL, 1, 1, '2025-09-16 12:15:14', '2025-09-16 12:15:16'),
(30, 'Test Notice 084502', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:15', NULL, 1, 1, '2025-09-16 12:15:15', NULL),
(31, 'Test Notice 084539 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:53', NULL, 1, 1, '2025-09-16 12:15:51', '2025-09-16 12:15:53');

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
(38, 'Test 1', NULL, '565656', NULL, 'wertyh', 'Male', 25, 'Yrs', '2025000039', '2025-09-10 14:14:18', 3, '2025-09-18 05:22:12'),
(39, 'P2', NULL, '5565665', NULL, '456yu', 'Male', 25, 'Years', 'qw', '2025-09-11 05:42:20', 3, '2025-09-17 03:17:18'),
(88, 'test alk', NULL, '45555', NULL, 'dc', 'Male', 25, 'Years', '2025000001', '2025-09-13 15:34:04', 2, '2025-09-13 10:04:04'),
(89, 'test 2', NULL, '4546', NULL, 'wer', 'Male', 25, 'Years', '2025000002', '2025-09-13 15:35:02', 2, '2025-09-13 10:05:02'),
(91, 'Test Patient for Entry', NULL, '1234567890', NULL, 'Test Address', 'Male', 25, NULL, '2025000003', '2025-09-14 13:15:53', 1, '2025-09-14 07:45:53'),
(101, 'Ramu', NULL, '5454545454', NULL, '', 'Male', 20, 'Yrs', '2025000004', '2025-09-14 15:23:14', 2, '2025-09-14 09:53:14'),
(106, 'Test Patient 075604 UPDATED', NULL, '9876543604', NULL, 'Test Address', 'Male', 30, NULL, '2025000014', '2025-09-16 11:26:08', 1, '2025-09-16 05:56:10'),
(107, 'Test Patient 075636 UPDATED', NULL, '9876543636', NULL, 'Test Address', 'Male', 30, NULL, '2025000016', '2025-09-16 11:26:41', 1, '2025-09-16 05:56:43'),
(108, 'Test Patient 080120 UPDATED', NULL, '9876543120', NULL, 'Test Address', 'Male', 30, NULL, '2025000018', '2025-09-16 11:31:25', 1, '2025-09-16 06:01:27'),
(109, 'Test Patient 081550 UPDATED', NULL, '9876543550', NULL, 'Test Address', 'Male', 30, NULL, '2025000020', '2025-09-16 11:45:55', 1, '2025-09-16 06:15:57'),
(110, 'Test Patient 084328 UPDATED', NULL, '9876543328', NULL, 'Test Address', 'Male', 30, NULL, '2025000022', '2025-09-16 12:13:35', 1, '2025-09-16 06:43:37'),
(111, 'Test Patient 084355 UPDATED', NULL, '9876543355', NULL, 'Test Address', 'Male', 30, NULL, '2025000024', '2025-09-16 12:14:02', 1, '2025-09-16 06:44:03'),
(112, 'Test Patient 084423 UPDATED', NULL, '9876543423', NULL, 'Test Address', 'Male', 30, NULL, '2025000026', '2025-09-16 12:14:29', 1, '2025-09-16 06:44:31'),
(113, 'Test Patient 084502 UPDATED', NULL, '9876543502', NULL, 'Test Address', 'Male', 30, NULL, '2025000028', '2025-09-16 12:15:08', 1, '2025-09-16 06:45:10'),
(114, 'Test Patient 084539 UPDATED', NULL, '9876543539', NULL, 'Test Address', 'Male', 30, NULL, '2025000030', '2025-09-16 12:15:45', 1, '2025-09-16 06:45:47'),
(115, 'Test Patient 084615 UPDATED', NULL, '9876543615', NULL, 'Test Address', 'Male', 30, NULL, '2025000032', '2025-09-16 12:16:22', 1, '2025-09-16 06:46:24'),
(116, 'Test Patient 084735 UPDATED', NULL, '9876543735', NULL, 'Test Address', 'Male', 30, NULL, '2025000034', '2025-09-16 12:17:39', 1, '2025-09-16 06:47:41'),
(117, 'Test Patient 084805 UPDATED', NULL, '9876543805', NULL, 'Test Address', 'Male', 30, NULL, '2025000036', '2025-09-16 12:18:10', 1, '2025-09-16 06:48:12'),
(118, 'Test Patient 084833 UPDATED', NULL, '9876543833', NULL, 'Test Address', 'Male', 30, NULL, '2025000038', '2025-09-16 12:18:39', 1, '2025-09-16 06:48:41');

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

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `data`, `created_at`, `added_by`) VALUES
(1, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758046444159\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-16T23:44:04.159907\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-16 23:44:05', 3),
(2, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758046444159\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-16T23:44:04.159907\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-16 23:44:06', 3),
(3, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758048383470\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-17T00:16:23.470397\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-17 00:16:25', 3),
(4, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758048383470\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-17T00:16:23.470397\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-17 00:16:26', 3),
(5, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758048502734\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-17T00:18:22.734328\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-17 00:18:24', 3),
(6, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758048502734\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-17T00:18:22.734328\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-17 00:18:25', 3),
(7, '{\"patient\":{\"name\":\"John Doe\",\"uhid\":\"UH123\",\"mobile\":\"9999999999\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-17T08:07:58.958312\",\"subtotal\":1000.0,\"original_total\":1200.0,\"discount_amount\":200.0,\"status\":\"Completed\"},\"tests\":[{\"name\":\"Test A\",\"price\":500}]}', '2025-09-17 08:08:00', 3),
(9, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758076678977\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-17T08:07:58.977324\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-17 08:08:01', 3),
(10, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758077849632\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-17T08:27:29.632959\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-17 08:27:31', 3),
(13, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758079090406\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-17T08:48:10.406471\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-17 08:48:13', 3),
(15, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758172337662\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-18T10:42:17.662705\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-18 10:42:18', 3),
(16, '{\"patient\":{\"name\":\"AutoTest\",\"uhid\":\"AT1758172337662\",\"mobile\":\"9111111111\"},\"meta\":{\"refBy\":\"Dr Test\",\"date\":\"2025-09-18T10:42:17.662705\",\"subtotal\":123.0,\"original_total\":123.0,\"discount_amount\":0.0,\"status\":\"Pending\"},\"tests\":[{\"name\":\"AutoTest\",\"price\":123}]}', '2025-09-18 10:42:19', 3);

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
(2, 'Blair Curry', 'Ut labore nesciunt', 59, 649.00, 'Dolorem amet aut no', '', '', 81.00, 100.00, 0, '', 'Tempor provident al', 1, '', 1, 24.00, 78.00, 60.00, 80.00, NULL),
(3, 'Giacomo Poole', 'Dolorum officia est', 68, 910.00, 'Animi modi minima d', '', '', 84.00, 99.00, 1, '', 'Unde fugiat quis con', 1, '', 1, 36.00, 79.00, 74.00, 99.00, NULL);

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
(1, 'umakant', '$2y$10$FviBcCP/ukXwL2B.a64gpuTRaOv7Rr9mreDi8maNoyKPw/FzdBISm', 'Umakant Yadav', 'umakant171991@gmail.com', 'master', '', 1, 1, '2025-08-25 09:47:40', '2025-09-16 15:36:38', NULL, '2025-09-09 00:51:43', '7ffe5020ef63cb74690cc1b5afd066db57e72bda4aaab33e59282c4ae58178fa'),
(2, 'alok', '$2y$10$Ip95EmIgT99MSwlk4wYrxOg8BFg4T.tIOEUtx953ITel5FLeN94zi', 'Alok Yadav', 'alok@gmail.com', 'user', 'Pathology', 1, 1, '2025-08-28 09:56:05', '2025-09-17 08:19:45', '2025-09-24 15:44:00', '2025-08-25 09:56:05', '7c412bf89e6b70fbf114c42961035d84a98b01b0cedd728db53400c47b7f423a'),
(3, 'uma', '$2y$10$JSbe8vwmhMXYDIzhZ11yZuiGbyfXx55hC1C4Q8GYlsAECYfKriX8K', 'Uma Yadav', 'umakant171991@gmail.com', 'user', 'Pathology', 1, 1, '2025-08-30 09:57:26', '2025-09-18 05:25:54', '2025-09-27 15:50:00', '2025-08-25 09:57:26', 'ec197b6d46257de56b80fe3b92ddd44b0d9467a6bc5d70fbfd767a4377e44c28'),
(4, 'ghayas', '$2y$12$FskrMh0zNSCOZ4zRpRBK6eWlv9rZfsoGJbEtV44xAjd6pXRAn7O2a', 'Ghayas', 'ghayasahmad522@gmail.com', 'admin', 'Pathology', 1, 1, '2025-09-02 07:04:53', '2025-09-16 16:12:50', NULL, '2025-09-02 07:04:53', NULL),
(5, 'Test', '$2y$10$P2tecM1j41r0mMQwtmVwUuG/20KMJSSvDDGaPXPY2lQ9.GihsOp/y', 'Harrison Mcpherson', 'hinew@mailinator.com', 'user', 'Pathology', 1, 1, '2025-09-08 10:54:11', NULL, '2025-09-27 11:14:00', '2025-09-08 10:54:11', '334d18c98ca59be415c5c3b7ee08c66e502975bbf7a7c0eeb3caeff478b1c360'),
(6, 'test1', '$2y$10$xg08.ykhBcH8rN0ju8xfm.6DhD8349Bq72CCddI4YGT3hM2c6L/ve', '', '', 'master', '', 1, 1, '2025-09-11 10:50:55', '2025-09-11 10:51:07', '2025-12-06 16:20:00', '2025-09-11 10:50:55', '28ac51567f73d2426f04a07657ca32ba2c762ce364afc1235538aa42221caddd'),
(7, 'hyguwuweke', '$2y$10$eki.QxC6C/0XxGNvB68deuQQOJdkGAP2.FDRZNO/brKtoiHL.7EVy', 'Ciara Schroeder', 'viju@mailinator.com', 'user', 'Pathology', 1, 1, '2025-09-14 04:11:11', NULL, '2025-10-14 09:41:00', '2025-09-14 04:11:11', NULL),
(8, 'user_admin', '$2y$12$UgToWYJq3BByvbzNvMRuFeH3OMgWFpFixmd4aL6ZQl.HgVourLfE2', 'ghayas', 'ghayas@gmail.com', 'user', 'Pathology', 4, 1, '2025-09-16 16:21:23', '2025-09-16 16:31:45', '2025-11-16 21:51:00', '2025-09-16 16:21:23', '9bfedcd475d559680bad74ff3b5bd62658554e0575aa422667199910795633c6');

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
  ADD KEY `idx_entries_patient` (`patient_id`),
  ADD KEY `idx_entries_doctor` (`doctor_id`),
  ADD KEY `idx_entries_test` (`test_id`),
  ADD KEY `idx_entries_added_by` (`added_by`),
  ADD KEY `idx_entries_entry_date` (`entry_date`),
  ADD KEY `idx_entries_status` (`status`),
  ADD KEY `idx_entries_server_id` (`server_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- Constraints for table `entries`
--
ALTER TABLE `entries`
  ADD CONSTRAINT `fk_entries_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_entries_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_entries_test` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_entries_user` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
