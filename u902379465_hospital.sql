-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 01, 2026 at 02:47 AM
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
  `main_category_id` int(11) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `main_category_id`, `added_by`, `created_at`, `updated_at`) VALUES
(9, 'Complete Blood Count', '', 30, 1, '2025-12-05 09:40:54', '2025-12-05 09:40:54'),
(10, 'Differential Count', '', 30, 1, '2025-12-05 11:09:27', '2025-12-05 11:09:27'),
(11, 'Platelets', '', 30, 1, '2025-12-06 00:16:31', '2025-12-06 00:16:31'),
(12, 'RBC Indices', '', 30, 1, '2025-12-06 00:18:04', '2025-12-06 00:18:04'),
(13, 'Coagulation', '', 30, 1, '2025-12-06 00:20:30', '2025-12-06 00:20:30'),
(14, 'Renal Function', '', 31, 1, '2025-12-06 00:35:18', '2025-12-06 00:35:18'),
(15, 'Liver Function', '', 31, 1, '2025-12-06 00:40:17', '2025-12-06 00:40:17'),
(16, 'Lipid Profile', '', 31, 1, '2025-12-06 00:45:19', '2025-12-06 00:45:19'),
(17, 'Electrolytes', '', 31, 1, '2025-12-06 00:53:31', '2025-12-06 00:53:31'),
(18, 'Thyroid Function', '', 32, 1, '2025-12-06 00:57:01', '2025-12-06 00:57:01'),
(19, 'Vitamins', '', 33, 1, '2025-12-06 00:59:36', '2025-12-06 00:59:36'),
(20, 'Inflammatory Markers', '', 34, 1, '2025-12-06 01:02:41', '2025-12-06 01:02:41'),
(21, 'Autoimmune', '', 34, 1, '2025-12-06 01:05:43', '2025-12-06 01:05:43'),
(22, 'Viral Markers', '', 35, 1, '2025-12-06 01:07:13', '2025-12-06 01:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `company`, `address`, `city`, `state`, `zip`, `notes`, `added_by`, `created_at`, `updated_at`) VALUES
(3, 'Amzad', '', '+91 95400 52228', '', '', 'Delhi', 'Delhi', '', '', NULL, '2025-11-17 12:17:49', '2025-11-18 06:12:46'),
(4, 'Vishal ', '', '08427722958', '', '', '', 'Punjab', '', '', NULL, '2025-11-17 12:44:15', NULL),
(5, 'Raman', '', '+917087071220', '', '', 'Gurugram', '', '', '', NULL, '2025-11-17 15:43:14', NULL),
(6, 'Dharmendra Bachheriya', '', '+91 83064 02805', '', '', '', '', '', '', NULL, '2025-11-17 15:51:53', NULL),
(7, 'Ezaz From Ireland', '', '+353 87 355 5012', '', '', '', '', '', '', NULL, '2025-11-17 15:54:22', NULL),
(8, 'Pankaj Freelancer', '', '98187 49023', '', '', '', '', '', '', NULL, '2025-11-17 15:57:18', NULL),
(9, 'Brendan Australia', 'info@sketchfurniture.com.au', '+61 414 739 495', '', 'https://sketchfurniture.com.au/', '', '', '', '', NULL, '2025-11-17 16:04:20', NULL),
(10, 'Ravi T', '', '+91 9860900484', '', '', 'Bombay', 'Bombay', '', '', NULL, '2025-11-18 06:06:52', NULL),
(11, 'Gyas', '', '97114 47614', '', '', 'Lucknow', 'UP', '', '', NULL, '2025-11-18 07:05:39', '2025-11-22 02:21:10'),
(12, 'Umakant (Selef)', 'umakant171991@gmail.com', '09453619260', 'Umakant Yadav', 'Jaunpur Rd', 'Jaunpur', 'Uttar Pradesh', '222001', '', NULL, '2025-12-06 19:55:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `client_responses`
--

CREATE TABLE `client_responses` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `response_message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `client_responses`
--

INSERT INTO `client_responses` (`id`, `client_id`, `response_message`, `created_at`) VALUES
(3, 67, 'In valid Whatsapp', '2025-12-30 04:32:50'),
(4, 22, 'Sent  a Whatsapp message  but  he is asking  me  for  plood test for me', '2025-12-30 04:55:46'),
(5, 23, 'Sent whatsapp message  not  respond', '2025-12-30 04:57:18'),
(6, 52, 'Invalid  whatsapp no.', '2025-12-30 05:09:31'),
(7, 51, 'Sent demo on whatsapp not respond now', '2025-12-30 05:13:04'),
(8, 50, 'Sent Whatsapp not respond now', '2025-12-30 05:15:22'),
(9, 48, 'Sent whatsapp not respond now', '2025-12-30 05:21:56'),
(10, 46, 'Sent  first message on whatsapp', '2025-12-30 05:41:40'),
(11, 42, 'Sent whatsapp  message  not repond now', '2025-12-30 05:59:25'),
(12, 54, 'Invalide  whatsapp no.', '2025-12-30 06:03:01'),
(13, 39, 'Sent  message  on whatsapp not respond now', '2025-12-30 06:04:07'),
(14, 43, 'Sent  massage  on whatsapp not respond now', '2025-12-30 06:05:48'),
(15, 29, 'Invalid whatsapp no.', '2025-12-30 06:06:35'),
(16, 33, 'Invalid whatsapp no.', '2025-12-30 06:07:59'),
(17, 44, 'Not valid Whatsapp no.', '2025-12-30 06:08:56'),
(18, 62, 'not valid whatsapp', '2025-12-30 06:09:37'),
(19, 45, 'Sent whatsapp not respond  now', '2025-12-30 06:10:30'),
(20, 41, 'not valid whatsapp', '2025-12-30 06:12:14'),
(21, 31, 'Sent whatsapp message not respond now', '2025-12-30 06:13:03'),
(22, 52, 'Not valid whatsapp', '2025-12-30 06:13:40'),
(23, 49, 'Sent Whatsapp message not respond now', '2025-12-30 06:16:02'),
(24, 47, 'Sent whatsapp message  not respond now', '2025-12-30 06:16:59'),
(25, 37, 'not valis whatsapp no.', '2025-12-30 06:17:40'),
(26, 57, 'not valid whatsapp no.', '2025-12-30 06:19:16'),
(27, 26, 'not valid whatsapp', '2025-12-30 06:20:02'),
(28, 61, 'Not valid whatsapp', '2025-12-30 06:20:43'),
(29, 60, 'Not valid whatsapp', '2025-12-30 06:21:19'),
(30, 59, 'Not valid whatsapp no.', '2025-12-30 06:21:55'),
(31, 58, 'Sent whatsapp message not respond now', '2025-12-30 06:22:46'),
(32, 63, 'Not valid whatsapp', '2025-12-30 06:23:17'),
(33, 56, 'Not valid whstasapp', '2025-12-30 06:23:46'),
(34, 55, 'Not valid whatsapp', '2025-12-30 06:26:39'),
(35, 53, 'not valid whatsapp', '2025-12-30 06:27:16'),
(36, 38, 'Sent  whatsapp message  not respond  now', '2025-12-30 06:29:04'),
(37, 69, 'Sent  whatsapp message  not respond  now', '2025-12-30 06:29:55'),
(38, 28, 'Not valid whatsapp no.', '2025-12-30 06:31:33'),
(39, 40, 'Not valid whatsapp no.', '2025-12-30 06:32:06'),
(40, 65, 'Sent whatsapp message not respond now', '2025-12-30 06:34:11'),
(41, 64, 'Not valid whatsapp', '2025-12-30 06:34:56'),
(42, 23, 'Sent whatsapp messge not respond now', '2025-12-30 06:35:36'),
(43, 23, 'Responded he  have already software no need to  send fallowup message.', '2025-12-30 06:39:43'),
(44, 34, 'Sent whatsapp message  not respond  now', '2025-12-30 06:42:19'),
(45, 35, 'Sent Whatsapp message  not respond  now', '2025-12-30 06:43:30'),
(46, 36, 'Sent whatsapp message  not respond  now', '2025-12-30 06:44:16'),
(47, 32, 'Not Valid whatsapp', '2025-12-30 06:45:31'),
(48, 27, 'Not valid whatsapp', '2025-12-30 06:46:12'),
(49, 30, 'Not valid  Whatsapp', '2025-12-30 06:51:58'),
(50, 25, 'Sent whatsapp message  not respond  now', '2025-12-30 07:00:55'),
(51, 24, 'NOt valid whatsapp', '2025-12-30 07:05:01'),
(52, 66, 'not  valid whatsapp', '2025-12-30 07:08:34'),
(54, 67, 'NOt valid whatsapp', '2025-12-30 07:10:40'),
(56, 2, 'test', '2025-12-30 07:13:22'),
(57, 35, 'He have already software', '2025-12-30 09:28:57'),
(58, 71, 'Sent whatsapp message  not respond', '2025-12-30 09:58:44'),
(59, 84, 'Sent  whatsapp message not respond now', '2025-12-30 09:59:49'),
(60, 82, 'Not valid whatsapp no.', '2025-12-30 10:00:34'),
(61, 81, 'Not valid whatsapp no.', '2025-12-30 10:02:58'),
(62, 85, 'Sent whatsapp message  not respond  now', '2025-12-30 10:06:57'),
(63, 80, 'Not valid Whatsapp no.', '2025-12-30 10:10:53'),
(64, 83, 'Sent message  on whatsapp not respond  now', '2025-12-30 10:12:21'),
(65, 79, 'Sent whats message not respond now', '2025-12-30 10:24:36'),
(66, 78, 'Not valid whatsapp', '2025-12-30 10:25:55'),
(67, 77, 'Not valid whatsapp', '2025-12-30 10:27:03'),
(68, 76, 'Not valid whatsapp', '2025-12-30 10:27:57'),
(69, 75, 'Not valid whatsapp', '2025-12-30 10:28:40'),
(70, 73, 'Not valid whatsapp', '2025-12-30 10:29:26'),
(71, 74, 'Not valid whatsapp', '2025-12-30 10:30:02'),
(72, 72, 'Sent whatsapp message not respnde now', '2025-12-30 10:31:03'),
(73, 70, 'Not valid whatsapp', '2025-12-30 10:31:44'),
(74, 86, 'Sent  Whatsapp message  not respond  now', '2025-12-31 02:07:27'),
(75, 90, 'Sent  whatsapp message  not respond  now', '2025-12-31 03:05:35'),
(76, 247, 'Sent whatsapp message not respond now', '2025-12-31 03:07:54'),
(77, 263, 'Sent Whatsapp message not respond', '2025-12-31 03:32:43'),
(78, 262, 'Sent whatsapp on message not respond now', '2025-12-31 03:34:08'),
(79, 261, 'Not valid whatsapp', '2025-12-31 03:35:28'),
(80, 260, 'Sent Whatsapp message not respond now', '2025-12-31 03:36:38'),
(81, 259, 'Sent  whatsapp message Not respond now', '2025-12-31 03:37:58'),
(82, 258, 'Not valid whatsapp', '2025-12-31 03:38:43'),
(83, 257, 'Sent  whatsapp message not respond now', '2025-12-31 03:40:01'),
(84, 253, 'Not valid whatsapp', '2025-12-31 03:40:47'),
(85, 252, 'Sent  Whatsapp message not respond  now', '2025-12-31 03:41:38'),
(86, 251, 'Not valid whatsapp', '2025-12-31 03:42:21'),
(87, 250, 'Not valid  whatsapp', '2025-12-31 03:43:18'),
(88, 249, 'Sent whatsapp message  not respond now', '2025-12-31 03:44:15'),
(89, 248, 'Send whatsapp message not respond  now', '2025-12-31 03:45:11'),
(90, 242, 'Not valid whatsapp', '2025-12-31 03:46:05'),
(91, 246, 'Not valid whatsapp', '2025-12-31 03:46:57'),
(92, 245, 'Not valid whatsapp', '2025-12-31 03:47:43'),
(93, 244, 'Sent whatsapp message not  respond now', '2025-12-31 03:48:36'),
(94, 243, 'Sent whatsapp message not respond now', '2025-12-31 03:49:49'),
(95, 241, 'Sent  whatsapp message not respond  now', '2025-12-31 03:50:37'),
(96, 233, 'Sent Whatsapp message not respond now', '2025-12-31 03:51:58'),
(97, 240, 'Sent whatsapp message not respond now', '2025-12-31 03:53:25'),
(98, 239, 'not valid whatsapp', '2025-12-31 03:54:21'),
(99, 238, 'not valid whatsapp', '2025-12-31 03:55:10'),
(100, 237, 'Sent whatsapp message not respond now', '2025-12-31 03:56:08'),
(101, 236, 'Sent Whatsapp message not respond now', '2025-12-31 03:57:02'),
(102, 235, 'Sent whatsapp message not respond now', '2025-12-31 03:58:07'),
(103, 234, 'Sent whatsapp message not respond now', '2025-12-31 03:58:52'),
(104, 223, 'Sent Whatsapp message message not respond now', '2025-12-31 03:59:58'),
(105, 232, 'Not valid whatsapp', '2025-12-31 04:00:40'),
(106, 231, 'Not valid Whatsapp', '2025-12-31 04:01:59'),
(107, 230, 'Sent whatsapp message not respond now', '2025-12-31 04:03:06'),
(108, 229, 'Sent Whatsapp message not respond now', '2025-12-31 04:04:29'),
(109, 228, 'Sent Whatsapp message not respond now', '2025-12-31 04:05:39'),
(110, 227, 'Not valid Whatsapp', '2025-12-31 04:07:35'),
(111, 226, 'Not valid Whatsapp', '2025-12-31 04:08:33'),
(112, 225, 'Sent Whatsapp message not respond now', '2025-12-31 04:09:45'),
(113, 224, 'Sent Whatsapp message not respond now', '2025-12-31 04:11:51'),
(114, 222, 'Sent whatsapp message not respond now', '2025-12-31 04:13:01'),
(115, 221, 'Sent Whatsapp message not respond now', '2025-12-31 04:13:54'),
(116, 219, 'Sent Whatsapp message not respond now', '2025-12-31 04:14:53'),
(117, 220, 'Sent Whatsapp message not respond now', '2025-12-31 04:16:10'),
(118, 218, 'Not valid Whatsapp', '2025-12-31 04:16:53'),
(119, 215, 'Sent Whatsapp message not respond now', '2025-12-31 04:17:40'),
(120, 217, 'Sent Whatsapp message not respond now', '2025-12-31 04:18:26'),
(121, 216, 'Sent Whatsapp message not respond now', '2025-12-31 04:19:29'),
(122, 213, 'Sent Whatsapp message not respond now', '2025-12-31 04:20:31'),
(123, 214, 'Sent Whatsapp message not respond now', '2025-12-31 04:28:48'),
(124, 212, 'Sent Whatsapp message  not respond  now', '2025-12-31 04:33:12'),
(125, 211, 'Not valid Whatsapp', '2025-12-31 04:43:09'),
(126, 210, 'Sent  Whatsapp message  not  respond  now', '2025-12-31 04:50:00'),
(127, 223, 'Intrested  asking for demo sent demo with demo user', '2025-12-31 05:01:59'),
(128, 209, 'Sent  Whatsapp message not  respond  now', '2025-12-31 08:47:03'),
(129, 205, 'Not valid Whatsapp', '2026-01-01 00:10:48'),
(130, 208, 'Sent Whatsapp message not respond now', '2026-01-01 00:11:51'),
(131, 207, 'Not valid Whatsapp', '2026-01-01 00:32:16'),
(132, 206, 'Sent WhatsApp message not respond now', '2026-01-01 00:33:06'),
(133, 264, 'Not valid Whatsapp', '2026-01-01 00:34:28'),
(134, 274, 'Not valid Whatsapp', '2026-01-01 00:35:15'),
(135, 202, 'Not Valid Whatsapp', '2026-01-01 00:35:59'),
(136, 204, 'Sent Whatsapp message not respond now', '2026-01-01 00:36:53'),
(137, 203, 'Sent Whatsapp message not respond now', '2026-01-01 00:37:47'),
(138, 197, 'Sent Whatsapp message not respond now', '2026-01-01 00:39:27'),
(139, 201, 'Not valid Whatsapp', '2026-01-01 00:40:36'),
(140, 200, 'Sent  Whatsapp message  but  not  respond  now', '2026-01-01 01:21:40'),
(141, 199, 'Sent  Whatsapp message  not respond  now', '2026-01-01 01:22:49'),
(142, 198, 'Sent  Whatsapp message not  respond now', '2026-01-01 01:23:40'),
(143, 196, 'Not valid Whatsapp', '2026-01-01 01:24:17'),
(144, 195, 'Sent  Whatsapp message not respond  now', '2026-01-01 01:26:01'),
(145, 194, 'Not valid Whatsapp', '2026-01-01 01:26:40'),
(146, 193, 'Sent Whatsapp message not respond  now', '2026-01-01 01:27:28'),
(147, 192, 'Sent  Whatsapp message  not respond  now', '2026-01-01 01:38:27'),
(148, 256, 'not valid Whatsapp', '2026-01-01 01:50:54'),
(149, 255, 'Not valid Whatsapp', '2026-01-01 01:51:30'),
(150, 254, 'Sent  Whatsapp message  not respond  now', '2026-01-01 01:52:27'),
(151, 182, 'Not valid Whatsapp', '2026-01-01 01:54:08'),
(152, 191, 'Sent Whatsapp message not respond now', '2026-01-01 01:57:39'),
(153, 190, 'Sent Whatsapp not respond now', '2026-01-01 02:20:18'),
(154, 189, 'Not valid Whatsapp', '2026-01-01 02:21:30');

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
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `server_id`, `name`, `qualification`, `specialization`, `hospital`, `contact_no`, `phone`, `email`, `address`, `registration_no`, `percent`, `added_by`, `created_at`, `updated_at`) VALUES
(1987, NULL, 'D1_gyas', '', '', '', '5656666556', '', '', '', '', 0.00, 7, NULL, '2025-12-21 20:25:48'),
(1988, NULL, 'D1_gyas', '', '', '', '99999', '', '', '', '', 0.00, 7, NULL, '2025-12-21 19:48:25'),
(1989, NULL, 'D1_gyas', '', '', 'hhhh', '4554', '5656565656', 'company@alexsol.tk', '', '', 0.00, 7, NULL, '2025-12-21 19:38:21'),
(1990, NULL, 'D1_gyas', NULL, NULL, 'hhhh', '4554', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-21 19:59:23', '2025-12-21 19:59:23'),
(1991, NULL, 'D1_gyas', NULL, NULL, '', '99999', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-21 19:59:23', '2025-12-21 19:59:23'),
(1992, NULL, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-21 19:59:24', '2025-12-21 19:59:24'),
(1994, NULL, 'Kalia Knox', NULL, NULL, 'Maxime enim enim vol', 'Irure fuga Laborum', NULL, NULL, 'Veritatis duis qui n', NULL, 52.00, 2, '2025-12-21 20:08:23', '2025-12-21 20:14:30'),
(1997, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-21 20:18:53', '2025-12-21 20:18:53'),
(1998, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-21 20:28:53', '2025-12-21 20:28:53'),
(1999, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 10:36:15', '2025-12-31 10:36:15'),
(2000, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 10:45:40', '2025-12-31 10:45:40'),
(2001, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 11:00:16', '2025-12-31 11:00:16'),
(2002, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 11:04:56', '2025-12-31 11:04:56'),
(2003, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 11:15:02', '2025-12-31 11:15:02'),
(2004, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 13:42:31', '2025-12-31 13:42:31'),
(2005, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 14:00:47', '2025-12-31 14:00:47'),
(2006, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 14:07:11', '2025-12-31 14:07:11'),
(2007, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 14:11:35', '2025-12-31 14:11:35'),
(2008, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 14:37:15', '2025-12-31 14:37:15'),
(2009, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 14:46:45', '2025-12-31 14:46:45'),
(2010, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 14:48:54', '2025-12-31 14:48:54'),
(2011, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2025-12-31 14:56:04', '2025-12-31 14:56:04'),
(2012, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2026-01-01 05:07:32', '2026-01-01 05:07:32'),
(2013, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2026-01-01 05:17:02', '2026-01-01 05:17:02'),
(2014, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2026-01-01 05:27:08', '2026-01-01 05:27:08'),
(2015, 1996, 'D1_gyas', NULL, NULL, '', '5656666556', NULL, NULL, NULL, NULL, 0.00, 7, '2026-01-01 05:36:30', '2026-01-01 05:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `email_signatures`
--

CREATE TABLE `email_signatures` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_signatures`
--

INSERT INTO `email_signatures` (`id`, `user_id`, `name`, `content`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'Umakant Yadav', 'Umakant Yadav\n\nThanks Regards', 0, '2025-11-22 02:48:31', '2025-11-22 02:48:31');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT 'general',
  `subject` varchar(500) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `reg_no` varchar(50) DEFAULT NULL,
  `entry_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `payment_status` enum('pending','paid','partial') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `impression` text DEFAULT NULL,
  `date_slot` varchar(20) DEFAULT NULL,
  `service_location` varchar(100) NOT NULL,
  `collection_address` varchar(100) NOT NULL,
  `priority` varchar(100) NOT NULL,
  `referral_source` varchar(100) NOT NULL,
  `added_by` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entry_tests`
--

CREATE TABLE `entry_tests` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `result_value` varchar(255) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `date_slot` varchar(20) NOT NULL,
  `main_category_id` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followups`
--

CREATE TABLE `followups` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `followup_date` date NOT NULL,
  `next_followup_date` date DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `followups`
--

INSERT INTO `followups` (`id`, `client_id`, `followup_date`, `next_followup_date`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 2, '2025-11-28', '2025-11-29', 'Call Later', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-11-28 08:10:45', '2025-12-29 05:41:14'),
(5, 51, '2025-12-29', '2026-01-10', 'Call Later', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 05:35:01', '2025-12-29 11:19:12'),
(7, 33, '2025-12-29', NULL, 'Pending', '<p>Followup: Request for detailed meeting to review PathoLab Pro capabilities and understand your specific laboratory requirements. Available for discussion on your preferred date and time.</p>', '2025-12-29 05:44:58', NULL),
(9, 51, '2025-12-29', NULL, 'Pending', '<p><br></p>', '2025-12-29 11:19:51', NULL),
(10, 39, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:20:14', NULL),
(11, 35, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:21:11', NULL),
(12, 32, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:21:49', NULL),
(13, 53, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:22:25', NULL),
(14, 53, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:22:51', NULL),
(15, 23, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:23:26', NULL),
(16, 38, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:24:11', NULL),
(17, 28, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:24:54', NULL),
(18, 29, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:25:30', NULL),
(19, 41, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:25:55', NULL);
INSERT INTO `followups` (`id`, `client_id`, `followup_date`, `next_followup_date`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(20, 42, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:26:33', NULL),
(21, 24, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:27:22', NULL),
(22, 43, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:27:54', NULL),
(23, 31, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:28:30', NULL),
(24, 52, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:29:05', NULL),
(25, 27, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:29:31', NULL),
(26, 36, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:29:59', NULL),
(27, 50, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:30:33', NULL),
(28, 48, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:31:18', NULL),
(29, 47, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:32:14', NULL),
(30, 49, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:32:50', NULL),
(31, 46, '2025-12-29', NULL, 'Pending', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: https://wa.me/919453619260</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 11:33:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `followup_clients`
--

CREATE TABLE `followup_clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `followup_message` text DEFAULT NULL,
  `followup_title` varchar(255) DEFAULT NULL,
  `response_message` text DEFAULT NULL,
  `next_followup_date` date DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `followup_clients`
--

INSERT INTO `followup_clients` (`id`, `name`, `email`, `phone`, `company`, `followup_message`, `followup_title`, `response_message`, `next_followup_date`, `added_by`, `created_at`, `updated_at`) VALUES
(2, 'Umakant Yadav', 'umakant171991@gmail.com', '919453619260', 'Umakant Yadav', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'test', '2026-01-10', 0, '2025-11-28 07:47:56', '2025-12-30 07:13:22'),
(22, 'Redcliffe Labs (Saket Nagar)', '', '+918988988787', 'Redcliffe Labs', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'no need update', '2026-01-10', 1, '2025-12-29 05:05:20', '2025-12-30 07:13:02'),
(23, 'Karauli Diagnostics', '', '+917068970689', '', 'बहुत बढ़िया सर \r\nबस एक छोटा‑सा सवाल था – क्या कभी लगा कि:\r\nReport format और बेहतर हो सकता है\r\n\r\nSpeed या support में दिक्कत आती है\r\n\r\nNew tests add कराने में टाइम लगता है\r\n\r\nअगर कभी future में alternative देखना चाहें तो मैं 10 मिनट का free demo दे सकता हूँ, बिना किसी charge या ज़बरदस्ती के।\r\nजरूरत पड़े तो बस एक message कर दीजिएगा, सर।', 'Pathology अगर बोले – already software है', 'Responde he  have already software no need to  send fallowup message.', '2026-10-10', 1, '2025-12-29 05:07:53', '2025-12-30 06:39:43'),
(24, 'Neuberg Diagnostic', '', '+919731700865', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'NOt valid whatsapp', '2026-01-10', 1, '2025-12-29 05:09:49', '2025-12-30 07:05:01'),
(25, 'Apollo Diagnostics', 'contact@apollodiagnostics.com', '+919876543210', 'Apollo Diagnostics Ltd', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-29 05:11:04', '2025-12-30 07:00:55'),
(26, 'SRL Diagnostics', 'info@srldiagnostics.com', '+918765432109', 'SRL Diagnostics Pvt Ltd', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not valid whatsapp', '2026-01-10', 1, '2025-12-29 05:11:27', '2025-12-30 06:20:02'),
(27, 'PathCare Diagnostics', 'sales@pathcarediagnostics.com', '+917654321098', 'PathCare Diagnostics India', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-29 05:11:52', '2025-12-30 06:46:12'),
(28, 'LabCorp India', 'contact@labcorpindia.com', '+916543210987', 'LabCorp India Limited', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp no.', '2026-01-10', 1, '2025-12-29 05:12:18', '2025-12-30 06:31:33'),
(29, 'Metropolis Healthcare', 'info@metropolishealth.com', '+915432109876', 'Metropolis Healthcare Ltd', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Invalid whatsapp no.', '2026-01-10', 1, '2025-12-29 05:12:50', '2025-12-30 06:06:35'),
(30, 'Dr Lal Path Labs', 'sales@drlalpathlabs.com', '+914321098765', 'Dr Lal Path Labs India', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid  Whatsapp', '2026-01-10', 1, '2025-12-29 05:13:19', '2025-12-30 06:51:58'),
(31, 'Parul Pathology Clinic', 'parul@pathologyclinic.com', '+919792012345', 'Parul Pathology Clinic Varanasi', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-29 05:14:35', '2025-12-30 06:13:03'),
(32, 'DGChem Labs Delhi', 'info@dgchemlabs.com', '+919876543220', 'DGChem Labs South Delhi', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not Valid whatsapp', '2026-01-10', 1, '2025-12-29 05:15:08', '2025-12-30 06:45:31'),
(33, 'Agilus Diagnostics Prayagraj', 'prayagraj@agilusdiagnostics.com', '+918037839114', 'Agilus Diagnostics Allahabad', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Invalid whatsapp no.', '2026-01-10', 1, '2025-12-29 05:15:46', '2025-12-30 06:07:59'),
(34, 'Tyagi Pathology Centre', 'tyagi@pathologycentre.com', '+919690556690', 'Tyagi Pathology Centre Saharanpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-29 05:16:28', '2025-12-30 06:42:19'),
(35, 'Charak Pathology Clinic', 'charak.pathology@gmail.com', '+919719600445', 'Charak Pathology Clinic Dehradun', 'Thank you सर, अच्छा लगा सुनकर कि आप already software use कर रहे हैं।\r\nमैं बस ये समझना चाहता था – आपके current software में आपको सबसे ज़्यादा दिक्कत किस चीज़ में आती है?\r\nSpeed / report बनाने में time\r\n\r\nSupport / problem आने पर help न मिलना\r\n\r\nReport format या test add/update कराने में दिक्कत\r\n\r\nअगर आप चाहें तो मैं आपके current काम के हिसाब से एक छोटा‑सा demo दिखा सकता हूँ,\r\nताकि आप compare कर पाएं – better लगे तभी सोचिएगा .', 'Pathology अगर बोले – already software है', 'He have already software', '2026-12-12', 1, '2025-12-29 05:17:03', '2025-12-30 09:28:57'),
(36, 'Pathkind Labs Badlapur Jaunpur', 'badlapur@pathkindlabs.com', '+919044131326', 'Pathkind Labs Badlapur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-29 05:18:46', '2025-12-30 06:44:16'),
(37, 'Shivam Pathology Jaunpur', 'shivam@pathology.com', '+918869993492', 'Shivam Pathology Jaunpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not valis whatsapp no.', '2026-01-10', 1, '2025-12-29 05:19:25', '2025-12-30 06:17:40'),
(38, 'KG Diagnostic Centre Jaunpur', 'kg@diagnosticcentre.com', '+919721453786', 'KG Diagnostic Centre', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-29 05:20:06', '2025-12-30 06:29:04'),
(39, 'Charak Diagnostic Centre Wazidpur', 'charak@diagnosticjaunpur.com', '+919335941433', 'Charak Diagnostic Wazidpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  message  on whatsapp not respond now', '2026-01-10', 1, '2025-12-29 05:20:44', '2025-12-30 06:04:07'),
(40, 'Dr Lal PathLabs Siddikpur', 'siddikpur@drlalpathabs.com', '+918071318169', 'Dr Lal PathLabs Jaunpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp no.', '2026-01-10', 1, '2025-12-29 05:21:26', '2025-12-30 06:32:06'),
(41, 'Metropolis Healthcare Ruhatta', 'ruhatta@metropolishealth.com', '+919321272713', 'Metropolis Healthcare Jaunpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not valid whatsapp', '2026-01-10', 1, '2025-12-29 05:22:53', '2025-12-30 06:12:14'),
(42, 'NeoHealth Pathology Labs', 'info@neohealthlabs.com', '+919532532111', 'NeoHealth Pathology Labs Jaunpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp  message  not repond now', '2026-01-10', 1, '2025-12-29 05:23:32', '2025-12-30 05:59:25'),
(43, 'Nidan Pathology Center Jaunpur', 'nidan@pathologylab.com', '+918528262386', 'Nidan Pathology Center Husainabad', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  massage  on whatsapp not respond now', '2026-01-10', 1, '2025-12-29 05:24:12', '2025-12-30 06:05:48'),
(44, 'Dr Lal PathLabs Naiganj', 'naiganj@drlalpathabs.com', '+918071015836', 'Dr Lal PathLabs Jaunpur Naiganj', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp no.', '2026-01-10', 1, '2025-12-29 05:24:56', '2025-12-30 06:08:56'),
(45, 'Smart Diagnostic Centre Umarpur', 'smart@diagnosticcentre.com', '+919975891424', 'Smart Diagnostic Centre Jaunpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp not respond  now', '2026-01-10', 1, '2025-12-29 05:26:43', '2025-12-30 06:10:30'),
(46, 'Pathkind Labs Shahganj', 'shahganj@pathkindlabs.com', '+917905588088', 'Pathkind Labs Shahganj Jaunpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  first message on whatsapp', '2026-01-10', 1, '2025-12-29 05:27:20', '2025-12-30 05:41:40'),
(47, 'Pathkind Labs Mungra Badshahpur', 'mungra@pathkindlabs.com', '+919125127802', 'Pathkind Labs Badshahpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message  not respond now', '2026-01-10', 1, '2025-12-29 05:28:01', '2025-12-30 06:16:59'),
(48, 'Pathkind Labs Machhalishahar', 'machhali@pathkindlabs.com', '+916387771881', 'Pathkind Labs Machhalishahar', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', '', '2026-01-10', 1, '2025-12-29 05:28:43', '2025-12-30 05:37:53'),
(49, 'Pathkind Labs Olandganj', 'olandganj@pathkindlabs.com', '+919695027941', 'Pathkind Labs Olandganj Jaunpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-29 05:30:22', '2025-12-30 06:16:02'),
(50, 'Pathkind Labs Gaurabashahpur', 'gaura@pathkindlabs.com', '+917007931552', 'Pathkind Labs Gaurabashahpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', '', '2026-01-10', 1, '2025-12-29 05:31:04', '2025-12-30 05:38:09'),
(51, 'Agilus Diagnostics Jeevan Raksha', 'agilus@jeevanraksha.com', '+919876543221', 'Agilus Diagnostics Mariahu', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', '', '2026-01-10', 1, '2025-12-29 05:31:51', '2025-12-30 05:37:59'),
(52, 'Parul Pathology Clinic Varanasi', 'parul@pathologylanka.com', '+919792012346', 'Parul Pathology Lanka Varanasi', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-29 05:34:04', '2025-12-30 06:13:40'),
(53, 'Dr Lal PathLabs Mahmoor Ganj', 'mahmoor@drlalpathlabs.com', '+918071889367', 'Dr Lal PathLabs Varanasi', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not valid whatsapp', '2026-01-10', 1, '2025-12-29 05:34:51', '2025-12-30 06:27:16'),
(54, 'Agilus Diagnostics Saket Nagar', 'saket@agilusdiagnostics.com', '+918037840056', 'Agilus Diagnostics Varanasi', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Invalide  whatsapp no.', '2026-01-10', 1, '2025-12-29 05:35:38', '2025-12-30 06:03:01'),
(55, 'Metropolis Healthcare Lanka', 'lanka@metropolishealth.com', '+911144740086', 'Metropolis Healthcare Varanasi', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-29 05:36:31', '2025-12-30 06:26:39'),
(56, 'Pathkind Labs Sultanpur Civil Lines', '', '+919513445295', 'Pathkind Labs', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whstasapp', '2026-01-10', 1, '2025-12-29 11:34:58', '2025-12-30 06:23:46'),
(57, 'Krsnaa Diagnostics Amhat Sultanpur', '', '+918810006004', 'Krsnaa Diagnostics', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not valid whatsapp no.', '2026-01-10', 1, '2025-12-29 11:35:19', '2025-12-30 06:19:16'),
(58, 'Modern Pathology Sultanpur', '', '+918318892747', 'Modern Pathology', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-29 11:35:37', '2025-12-30 06:22:46'),
(59, 'Global Pathology and ECG Centre Sultanpur', '', '+919795651818', 'Global Pathology and ECG Centre', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp no.', '2026-01-10', 1, '2025-12-29 11:35:54', '2025-12-30 06:21:55'),
(60, 'Agilus Diagnostics Sultanpur Civil Line', '', '+919889555559', 'Agilus Diagnostics', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-29 11:36:14', '2025-12-30 06:21:19'),
(61, 'Verma Diagnostic Center Sultanpur', '', '+919918888500', 'Verma Diagnostic Center', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-29 11:36:34', '2025-12-30 06:20:43'),
(62, 'Charak Diagnostic Centre Sultanpur', '', '+918957667788', 'Charak Diagnostic Centre', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not valid whatsapp', '2026-01-10', 1, '2025-12-29 11:36:54', '2025-12-30 06:09:37'),
(63, 'Eureka Diagnostic Center Sultanpur', '', '+919561009488', 'Eureka Diagnostic Center', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-29 11:37:15', '2025-12-30 06:23:17'),
(64, 'Aman Pathology Sultanpur', '', '+918958852233', 'Aman Pathology', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-29 11:37:42', '2025-12-30 06:34:56'),
(65, 'Anas Pathology Sultanpur', '', '+919795432166', 'Anas Pathology', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-29 11:38:09', '2025-12-30 06:34:11'),
(66, 'Parakh Diagnostic Centre Sultanpur', '', '+918839660660', 'Parakh Diagnostic Centre', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not  valid whatsapp', '2026-01-10', 1, '2025-12-29 11:38:38', '2025-12-30 07:08:34'),
(67, 'Thyrocare Aarogyam Centre Sultanpur', '', '+919576889999', 'Thyrocare Aarogyam Centre', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'NOt valid whatsapp', '2026-01-10', 1, '2025-12-29 11:39:07', '2025-12-30 07:10:40'),
(69, 'Advanced Pathology Labs', 'contact@advancedpathology.com', '+919012345678', 'Advanced Pathology Labs Pvt Ltd', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-30 05:02:04', '2025-12-30 06:29:55'),
(70, 'Shree Labs Pathology', 'contact@shreelabs.com', '+919876543211', 'Shree Labs Pathology Indore', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-30 09:42:52', '2025-12-30 10:31:44'),
(71, 'Vimta Labs', 'contact@vimtalabs.com', '+919876543212', 'Vimta Labs Hyderabad', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message  not respond', '2026-01-10', 1, '2025-12-30 09:43:51', '2025-12-30 09:58:50'),
(72, 'Sonshine Diagnostic Centre', 'info@sonshinelabs.com', '+919876543213', 'Sonshine Diagnostic Centre Gwalior', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respnde now', '2026-01-10', 1, '2025-12-30 09:44:17', '2025-12-30 10:31:03'),
(73, 'Global Diagnostics', 'sales@globaldiags.com', '+919876543214', 'Global Diagnostics Bhopal', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-30 09:44:44', '2025-12-30 10:29:26'),
(74, 'Healthspire Diagnostics', 'contact@healthspire.com', '+919876543215', 'Healthspire Diagnostics Lucknow', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-30 09:45:13', '2025-12-30 10:30:02'),
(75, 'Lifecare Pathology', 'info@lifecarepatho.com', '+919876543216', 'Lifecare Pathology Nagpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-30 09:45:42', '2025-12-30 10:28:40'),
(76, 'Precision Pathology Lab', 'contact@precisionpath.com', '+919876543217', 'Precision Pathology Pune', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-30 09:46:14', '2025-12-30 10:27:57'),
(77, 'CareStart Labs', 'info@carestart.com', '+919876543218', 'CareStart Labs Ahmedabad', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-30 09:46:42', '2025-12-30 10:27:03'),
(78, 'Wellness Diagnostics', 'contact@wellness.com', '+919876543219', 'Wellness Diagnostics Surat', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-30 09:47:15', '2025-12-30 10:25:55'),
(79, 'Quickcare Pathology', 'info@quickcare.com', '+919876543225', 'Quickcare Pathology Vadodara', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whats message not respond now', '2026-01-10', 1, '2025-12-30 09:48:12', '2025-12-30 10:24:36'),
(80, 'Nucleus Diagnostics', 'info@nucleus.com', '+919876543226', 'Nucleus Diagnostics Bangalore', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp no.', '2026-01-10', 1, '2025-12-30 09:48:40', '2025-12-30 10:10:53'),
(81, 'TrueCare Pathology', 'info@truecare.com', '+919876543227', 'TrueCare Pathology Chandigarh', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp no.', '2026-01-10', 1, '2025-12-30 09:49:14', '2025-12-30 10:02:58'),
(82, 'Medwell Diagnostics', 'info@medwell.com', '+919876543228', 'Medwell Diagnostics Delhi', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp no.', '2026-01-10', 1, '2025-12-30 09:49:52', '2025-12-30 10:00:34'),
(83, 'Diamond Diagnostics', 'info@diamond.com', '+919876543229', 'Diamond Diagnostics Jaipur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent message  on whatsapp not respond  now', '2026-01-10', 1, '2025-12-30 09:50:39', '2025-12-30 10:12:21'),
(84, 'Sterling Labs', 'info@sterling.com', '+919876543230', 'Sterling Labs Kanpur', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  whatsapp message not respond now', '2026-01-10', 1, '2025-12-30 09:51:11', '2025-12-30 09:59:49'),
(85, 'Premier Pathology', 'info@premier.com', '+919876543231', 'Premier Pathology Lucknow', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-30 09:51:45', '2025-12-30 10:06:57'),
(86, 'Rama Pathology center', '', '+917209393435', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-31 01:16:18', '2025-12-31 02:07:27'),
(87, 'Max Healthcare Pathology Lab Mumbai', '', '+919876543301', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:11:01', '2025-12-31 02:25:05'),
(88, 'Indraprastha Pathology Center Delhi', '', '+919876543302', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:11:23', '2025-12-31 02:25:21'),
(89, 'Medanta Pathology Lab Gurgaon', '', '+919876543303', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:11:36', '2025-12-31 02:25:27'),
(90, 'Fortis Pathology Center Mumbai', '', '+919876543304', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-31 02:11:47', '2025-12-31 03:05:35'),
(91, 'Spark Diagnostics Bangalore', '', '+919876543305', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:11:56', '2025-12-31 02:25:32'),
(92, 'Thyrocare Labs Hyderabad', '', '+919876543306', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:12:05', '2025-12-31 02:25:47'),
(93, 'Metropolis Labs Chennai', '', '+919876543307', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:12:15', '2025-12-31 02:25:54'),
(94, 'Core Pathology Pune', '', '+919876543308', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:12:24', '2025-12-31 02:26:00'),
(95, 'Redcare Labs Kolkata', '', '+919876543309', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:12:33', '2025-12-31 02:26:08'),
(96, 'Chitra Labs Jaipur', '', '+919876543310', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:12:43', '2025-12-31 02:26:17'),
(97, 'Sigma Diagnostics Lucknow', '', '+919876543311', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:12:52', '2025-12-31 02:26:24'),
(98, 'Aditya Pathology Lab Kanpur', '', '+919876543312', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:13:02', '2025-12-31 02:26:30'),
(99, 'Vimta Labs Hyderabad', '', '+919876543313', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:13:11', '2025-12-31 02:26:37'),
(100, 'Kansai Pathology Nagpur', '', '+919876543314', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:13:21', '2025-12-31 02:26:43'),
(101, 'Dilon Pathology Indore', '', '+919876543315', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:13:31', '2025-12-31 02:27:16'),
(102, 'Vijaya Diagnostics Kochi', '', '+919876543316', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:13:41', '2025-12-31 02:27:28'),
(103, 'Ganesh Labs Surat', '', '+919876543317', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:13:50', '2025-12-31 02:26:56'),
(104, 'Bharat Pathology Vadodara', '', '+919876543318', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:14:00', '2025-12-31 02:26:50'),
(105, 'Unique Labs Ahmedabad', '', '+919876543319', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:14:09', '2025-12-31 02:27:21'),
(106, 'Sri Kamakshi Labs Ranchi', '', '+919876543320', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:14:19', '2025-12-31 02:27:02'),
(107, 'Shree Pathology Bhubaneswar', '', '+919876543321', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:14:29', '2025-12-31 02:27:07'),
(108, 'Health Point Labs Patna', '', '+919876543322', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:14:38', '2025-12-31 02:27:38'),
(109, 'Noble Pathology Ranipet', '', '+919876543323', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:14:48', '2025-12-31 02:27:53'),
(110, 'Star Labs Ghaziabad', '', '+919876543324', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:14:58', '2025-12-31 02:27:32'),
(111, 'Crown Pathology Meerut', '', '+919876543325', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:15:08', '2025-12-31 02:27:44'),
(112, 'Reliance Pathology Panipat', '', '+919876543326', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:15:24', '2025-12-31 02:28:01'),
(113, 'Victory Labs Faridabad', '', '+919876543327', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:15:34', '2025-12-31 02:28:22'),
(114, 'Rathi Labs Hisar', '', '+919876543328', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:15:47', '2025-12-31 02:28:27'),
(115, 'Prime Lab Rohtak', '', '+919876543329', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:15:58', '2025-12-31 02:28:36'),
(116, 'Neptune Lab Belgaum', 'test@test.com', '+919876543334', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:16:39', '2025-12-31 02:28:10'),
(117, 'Ascent Lab Channapatna', '', '+919876543341', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:17:29', '2025-12-31 02:28:17'),
(118, 'Infinity Lab Ramnagar', '', '+919876543342', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:17:41', '2025-12-31 02:28:31'),
(119, 'Titan Lab Hoskote', '', '+919876543343', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:17:54', '2025-12-31 02:29:13'),
(120, 'Pinnacle Lab Kengeri', '', '+919876543344', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:17:58', '2025-12-31 02:28:40'),
(121, 'Vision Lab Anekal', '', '+919876543345', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:18:03', '2025-12-31 02:32:42'),
(122, 'Impact Lab Whitefield', '', '+919876543346', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:18:17', '2025-12-31 02:30:09'),
(123, 'Genesis Lab Sarjapur', '', '+919876543347', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:18:21', '2025-12-31 02:28:45'),
(124, 'Eclipse Lab Marathahalli', '', '+919876543348', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:18:25', '2025-12-31 02:28:51'),
(125, 'Prism Lab Indiranagar', '', '+919876543349', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:18:29', '2025-12-31 02:28:55'),
(126, 'Compass Lab Koramangala', '', '+919876543350', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:18:42', '2025-12-31 02:29:01'),
(127, 'Beacon Lab JP Nagar', '', '+919876543351', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:18:46', '2025-12-31 02:29:05'),
(128, 'Atlas Lab Domlur', '', '+919876543352', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:18:51', '2025-12-31 02:30:05'),
(129, 'Summit Lab Malleswaram', '', '+919876543353', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:18:55', '2025-12-31 02:29:19'),
(130, 'Clarity Lab Basavanagudi', '', '+919988776655', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:19:08', '2025-12-31 02:29:24'),
(131, 'Harmony Lab Jayanagar', '', '+919988776656', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:19:12', '2025-12-31 02:29:29'),
(132, 'Luminous Lab Richmond Town', '', '+919988776657', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:19:17', '2025-12-31 02:29:34'),
(133, 'Horizon Lab Benson Town', '', '+919988776658', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:19:21', '2025-12-31 02:29:39'),
(134, 'Stellar Lab Seshadripuram', '', '+919988776659', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:20:04', '2025-12-31 02:29:43'),
(135, 'Radiant Lab Basaveshwaranagar', '', '+919988776660', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:20:09', '2025-12-31 02:29:49'),
(136, 'Zenith Lab Gavipuram', '', '+919988776661', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:20:14', '2025-12-31 02:29:54'),
(137, 'Quantum Lab Yeshwantpur', '', '+919988776662', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:20:27', '2025-12-31 02:30:00'),
(138, 'Pulse Lab Nagarbhavi', '', '+919988776663', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:20:31', '2025-12-31 02:30:14'),
(139, 'Echo Lab Tumkur', '', '+919988776664', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:20:35', '2025-12-31 02:30:18'),
(140, 'Wave Lab Doddaballapur', '', '+919988776665', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:20:40', '2025-12-31 02:30:51'),
(141, 'Stream Lab Renigunta', '', '+919988776666', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:20:54', '2025-12-31 02:30:41'),
(142, 'Flow Lab Tirupati', '', '+919988776667', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:20:58', '2025-12-31 02:30:23'),
(143, 'Rise Lab Nellore', '', '+919988776668', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:02', '2025-12-31 02:30:27'),
(144, 'Glow Lab Ongole', '', '+919988776669', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:06', '2025-12-31 02:30:32'),
(145, 'Spark Lab Vijayawada', '', '+919988776670', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:10', '2025-12-31 02:30:37'),
(146, 'Swift Lab Kakinada', '', '+919988776671', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:24', '2025-12-31 02:32:47'),
(147, 'Bright Lab Rajahmundry', '', '+919988776672', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:28', '2025-12-31 02:30:46'),
(148, 'Lucky Lab Eluru', '', '+919988776673', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:32', '2025-12-31 02:30:56'),
(149, 'Grace Lab Guntur', '', '+919988776674', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:36', '2025-12-31 02:31:28'),
(150, 'Trend Lab Vizag', '', '+919988776675', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:40', '2025-12-31 02:31:00'),
(151, 'Prime Lab Vijayawada', '', '+919988776676', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:55', '2025-12-31 02:31:05'),
(152, 'Crown Lab Hyderabad', '', '+919988776677', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:21:59', '2025-12-31 02:31:09'),
(153, 'Royal Lab Secunderabad', '', '+919988776678', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:22:04', '2025-12-31 02:31:15'),
(154, 'Expert Lab Warangal', '', '+919988776679', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:22:08', '2025-12-31 02:31:19'),
(155, 'Noble Lab Karimnagar', '', '+919988776680', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:22:12', '2025-12-31 02:31:24'),
(156, 'Medic Lab Nizamabad', '', '+919988776681', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:22:27', '2025-12-31 02:32:52'),
(157, 'Health Lab Khammam', '', '+919988776682', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:22:32', '2025-12-31 02:31:56'),
(158, 'Care Lab Mahbubnagar', '', '+919988776683', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:22:36', '2025-12-31 02:32:00'),
(159, 'Trust Lab Adilabad', '', '+919988776684', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:22:40', '2025-12-31 02:32:05'),
(160, 'Hope Lab Jagtial', '', '+919988776685', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:22:44', '2025-12-31 02:32:10'),
(161, 'Cure Lab Bodhan', '', '+919988776686', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:22:59', '2025-12-31 02:32:14'),
(162, 'Cure Lab Bhainsa', '', '+919988776687', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:23:03', '2025-12-31 02:32:19'),
(163, 'Cure Lab Tandur', '', '+919988776688', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:23:07', '2025-12-31 02:32:23'),
(164, 'Cure Lab Tandberg', '', '+919988776689', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:23:11', '2025-12-31 02:32:27'),
(165, 'Cure Lab Tandel', '', '+919988776690', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:23:16', '2025-12-31 02:32:34'),
(166, 'Cure Lab Tandel Plus', '', '+919988776691', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:23:31', '2025-12-31 02:33:03'),
(167, 'Cure Lab Talegaon Plus', '', '+919988776692', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:23:35', '2025-12-31 02:32:57'),
(168, 'Cure Lab Tansi', '', '+919988776693', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:23:39', '2025-12-31 02:33:41'),
(169, 'Cure Lab Tanu', '', '+919988776694', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:23:43', '2025-12-31 02:33:51'),
(170, 'Cure Lab Tannery', '', '+919988776695', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:23:47', '2025-12-31 02:33:57'),
(171, 'Cure Lab Temple', '', '+919988776696', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:24:03', '2025-12-31 02:33:15'),
(172, 'Cure Lab Tentpole', '', '+919988776697', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:24:07', '2025-12-31 02:33:20'),
(173, 'Cure Lab Tenali', '', '+919988776698', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:24:11', '2025-12-31 02:33:26'),
(174, 'Cure Lab Tenanga', '', '+919988776699', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:24:16', '2025-12-31 02:33:32'),
(175, 'Cure Lab Tendar', '', '+919988776700', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:24:20', '2025-12-31 02:34:02'),
(176, 'NewPath Lab Tandil', '', '+919988776701', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:39:03', '2025-12-31 02:49:17'),
(177, 'Tech Lab Tandore', '', '+919988776702', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:39:07', '2025-12-31 02:49:23'),
(178, 'Advanced Lab Tanduk', '', '+919988776703', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:39:11', '2025-12-31 02:49:32'),
(179, 'Future Lab Tandulas', '', '+919988776704', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:39:15', '2025-12-31 02:52:16'),
(180, 'Smart Lab Tandume', '', '+919988776705', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:39:19', '2025-12-31 02:52:26'),
(181, 'Prime Lab Tandun', '', '+919988776706', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:39:34', '2025-12-31 02:53:28'),
(182, 'Best Lab Tanduo', '', '+919988776707', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:39:39', '2026-01-01 01:54:08'),
(183, 'Elite Lab Tandup', '', '+919988776708', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:39:43', '2025-12-31 02:53:34'),
(184, 'Quality Lab Tanduq', '', '+919988776709', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:39:47', '2025-12-31 02:53:40'),
(185, 'Professional Lab Tandur', '', '+919988776710', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:39:52', '2025-12-31 02:53:45'),
(186, 'Trusted Lab Tandus', '', '+919988776711', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:40:07', '2025-12-31 02:53:51'),
(187, 'Reliable Lab Tandut', '', '+919988776712', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:40:11', '2025-12-31 02:53:58'),
(188, 'Fast Lab Tanduv', '', '+919988776713', '', '', 'Pathology पहला संपर्क', '', '2025-12-31', 1, '2025-12-31 02:40:16', '2025-12-31 02:54:05'),
(189, 'Quick Lab Tanduw', '', '+919988776714', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:40:20', '2026-01-01 02:21:30'),
(190, 'Expert Lab Tandux', '', '+919988776715', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp not respond now', '2026-01-10', 1, '2025-12-31 02:40:24', '2026-01-01 02:20:18'),
(191, 'Master Lab Tanduy', '', '+919988776716', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:40:40', '2026-01-01 01:57:39'),
(192, 'Advanced Lab Tanduz', '', '+919988776717', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-31 02:40:44', '2026-01-01 01:38:27'),
(193, 'Superior Lab Tandva', '', '+919988776718', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond  now', '2026-01-10', 1, '2025-12-31 02:40:48', '2026-01-01 01:27:28'),
(194, 'Leading Lab Tandvb', '', '+919988776719', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:40:53', '2026-01-01 01:26:40'),
(195, 'Premier Lab Tandvd', '', '+919988776721', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message not respond  now', '2026-01-10', 1, '2025-12-31 02:42:09', '2026-01-01 01:26:01'),
(196, 'Ultimate Lab Tandvf', '', '+919988776723', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:42:16', '2026-01-01 01:24:17'),
(197, 'Premium Lab Tandvg', '', '+919988776724', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:42:20', '2026-01-01 00:39:27'),
(198, 'Brilliant Lab Tandvh', '', '+919988776725', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message not  respond now', '2026-01-10', 1, '2025-12-31 02:42:25', '2026-01-01 01:23:40'),
(199, 'Outstanding Lab Tandvi', '', '+919988776726', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-31 02:42:43', '2026-01-01 01:22:49'),
(200, 'Exceptional Lab Tandvj', '', '+919988776727', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message  but  not  respond  now', '2026-01-10', 1, '2025-12-31 02:42:48', '2026-01-01 01:21:40'),
(201, 'Remarkable Lab Tandvk', '', '+919988776728', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:42:52', '2026-01-01 00:40:36'),
(202, 'Superb Lab Tandvl', '', '+919988776729', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not Valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:42:56', '2026-01-01 00:35:59'),
(203, 'Fabulous Lab Tandvm', '', '+919988776730', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:43:00', '2026-01-01 00:37:47'),
(204, 'Wonderful Lab Tandvn', '', '+919988776731', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:43:04', '2026-01-01 00:36:53'),
(205, 'Amazing Lab Tandvo', '', '+919988776732', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:43:09', '2026-01-01 00:10:48'),
(206, 'Fantastic Lab Tandvp', '', '+919988776733', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent WhatsApp message not respond now', '2026-01-10', 1, '2025-12-31 02:43:12', '2026-01-01 00:33:06'),
(207, 'Gorgeous Lab Tandvq', '', '+919988776734', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:43:34', '2026-01-01 00:32:16'),
(208, 'Marvelous Lab Tandvr', '', '+919988776735', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:43:38', '2026-01-01 00:11:51'),
(209, 'Splendid Lab Tandvs', '', '+919988776736', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message not  respond  now', '2026-01-10', 1, '2025-12-31 02:43:43', '2025-12-31 08:47:03'),
(210, 'Glorious Lab Tandvt', '', '+919988776737', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message  not  respond  now', '2026-01-10', 1, '2025-12-31 02:43:47', '2025-12-31 04:50:00'),
(211, 'Magnificent Lab Tandvu', '', '+919988776738', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:43:51', '2025-12-31 04:43:09'),
(212, 'Impressive Lab Tandvv', '', '+919988776739', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-31 02:43:56', '2025-12-31 04:33:12'),
(213, 'Notable Lab Tandvw', '', '+919988776740', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:44:00', '2025-12-31 04:20:31'),
(214, 'Incredible Lab Tandvx', '', '+919988776741', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:44:04', '2025-12-31 04:28:48'),
(215, 'Astounding Lab Tandvy', '', '+919988776742', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:44:22', '2025-12-31 04:17:40'),
(216, 'Perfect Lab Tandvz', '', '+919988776743', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:44:26', '2025-12-31 04:19:29'),
(217, 'Unique Lab Tandwa', '', '+919988776744', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:44:31', '2025-12-31 04:18:26');
INSERT INTO `followup_clients` (`id`, `name`, `email`, `phone`, `company`, `followup_message`, `followup_title`, `response_message`, `next_followup_date`, `added_by`, `created_at`, `updated_at`) VALUES
(218, 'Elegant Lab Tandwb', '', '+919988776745', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:44:35', '2025-12-31 04:16:53'),
(219, 'Sublime Lab Tandwc', '', '+919988776746', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:44:39', '2025-12-31 04:14:53'),
(220, 'Superb Lab Tandwd', '', '+919988776747', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:44:43', '2025-12-31 04:16:10'),
(221, 'Vivid Lab Tandwe', '', '+919988776748', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:44:47', '2025-12-31 04:13:54'),
(222, 'Brilliant Lab Tandwf', '', '+919988776749', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:44:51', '2025-12-31 04:13:01'),
(223, 'Radiant Lab Tandwg', '', '+919988776750', '', 'Namaste सर,\r\nlast time मैंने आपसे आपके lab के लिए software के बारे में message किया था।\r\nबस ये पूछना था – क्या manual system में आपको ये problems आती हैं?\r\nReports लिखने में ज़्यादा time लगना\r\n\r\nDue payments का हिसाब रखना\r\n\r\nपुराने reports ढूँढने में दिक्कत\r\n\r\nअगर इनमें से कुछ issue है, तो मैं आपको 10 मिनट का free demo दिखा सकता हूँ।\r\nअगर अभी जरूरत नहीं है तो बस “NO” लिखकर बता दीजिए, फिर मैं disturb नहीं करूँगा।', 'Pthology  जिनको पहले message कर चुके हो – follow‑up', 'Intrested  asking for demo sent demo with demo user', '2026-01-10', 1, '2025-12-31 02:45:09', '2025-12-31 05:01:59'),
(224, 'Dazzling Lab Tandwh', '', '+919988776751', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:45:13', '2025-12-31 04:11:51'),
(225, 'Stellar Lab Tandwi', '', '+919988776752', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:45:18', '2025-12-31 04:09:45'),
(226, 'Glowing Lab Tandwj', '', '+919988776753', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:45:22', '2025-12-31 04:08:33'),
(227, 'Sparkling Lab Tandwk', '', '+919988776754', '', '', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:45:26', '2025-12-31 04:07:35'),
(228, 'Gleaming Lab Tandwl', '', '+919988776755', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:45:30', '2025-12-31 04:05:39'),
(229, 'Shimmering Lab Tandwm', '', '+919988776756', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:45:34', '2025-12-31 04:04:29'),
(230, 'Luminous Lab Tandwn', '', '+919988776757', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:45:39', '2025-12-31 04:03:06'),
(231, 'Glimmering Lab Tandwo', '', '+919988776758', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:45:56', '2025-12-31 04:01:59'),
(232, 'Resplendent Lab Tandwp', '', '+919988776759', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:46:00', '2025-12-31 04:00:40'),
(233, 'Sparking Lab Tandwq', '', '+919988776760', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:46:04', '2025-12-31 03:51:58'),
(234, 'Radiance Lab Tandwr', '', '+919988776761', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:46:08', '2025-12-31 03:58:52'),
(235, 'Excellence Lab Tandws', '', '+919988776762', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:46:12', '2025-12-31 03:58:07'),
(236, 'Perfection Lab Tandwt', '', '+919988776763', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:46:16', '2025-12-31 03:57:02'),
(237, 'Harmony Lab Tandwu', '', '+919988776764', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:46:21', '2025-12-31 03:56:08'),
(238, 'Triumph Lab Tandwv', '', '+919988776765', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:46:25', '2025-12-31 03:55:10'),
(239, 'Victory Lab Tandww', '', '+919988776766', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:46:41', '2025-12-31 03:54:21'),
(240, 'Success Lab Tandwx', '', '+919988776767', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:46:45', '2025-12-31 03:53:25'),
(241, 'Prestige Lab Tandwy', '', '+919988776768', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  whatsapp message not respond  now', '2026-01-10', 1, '2025-12-31 02:46:49', '2025-12-31 03:50:37'),
(242, 'Integrity Lab Tandwz', '', '+919988776769', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:46:53', '2025-12-31 03:46:05'),
(243, 'Liberty Lab Tandxa', '', '+919988776770', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:46:57', '2025-12-31 03:49:49'),
(244, 'Promise Lab Tandxb', '', '+919988776771', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not  respond now', '2026-01-10', 1, '2025-12-31 02:47:02', '2025-12-31 03:48:36'),
(245, 'Legacy Lab Tandxc', '', '+919988776772', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:47:06', '2025-12-31 03:47:43'),
(246, 'Heritage Lab Tandxd', '', '+919988776773', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:47:10', '2025-12-31 03:46:57'),
(247, 'Landmark Lab Tandxe', '', '+919988776774', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:47:26', '2025-12-31 03:07:54'),
(248, 'Monument Lab Tandxf', '', '+919988776775', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Send whatsapp message not respond  now', '2026-01-10', 1, '2025-12-31 02:47:31', '2025-12-31 03:45:11'),
(249, 'Zenith Lab Tandxg', '', '+919988776776', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp message  not respond now', '2026-01-10', 1, '2025-12-31 02:47:35', '2025-12-31 03:44:15'),
(250, 'Apex Lab Tandxh', '', '+919988776777', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid  whatsapp', '2026-01-10', 1, '2025-12-31 02:47:39', '2025-12-31 03:43:18'),
(251, 'Summit Lab Tandxi', '', '+919988776778', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:47:43', '2025-12-31 03:42:21'),
(252, 'Peak Lab Tandxj', '', '+919988776779', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message not respond  now', '2026-01-10', 1, '2025-12-31 02:47:47', '2025-12-31 03:41:38'),
(253, 'Crown Lab Tandxk', '', '+919988776780', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:47:51', '2025-12-31 03:40:47'),
(254, 'Royal Lab Tandxl', '', '+919988776781', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  Whatsapp message  not respond  now', '2026-01-10', 1, '2025-12-31 02:47:55', '2026-01-01 01:52:27'),
(255, 'Prince Lab Tandxm', '', '+919988776782', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:48:13', '2026-01-01 01:51:30'),
(256, 'Supreme Lab Tandxn', '', '+919988776783', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'not valid Whatsapp', '2026-01-10', 1, '2025-12-31 02:48:17', '2026-01-01 01:50:54'),
(257, 'Imperial Lab Tandxo', '', '+919988776784', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:48:21', '2025-12-31 03:40:01'),
(258, 'Majestic Lab Tandxp', '', '+919988776785', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:48:25', '2025-12-31 03:38:43'),
(259, 'Sovereign Lab Tandxq', '', '+919988776786', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent  whatsapp message Not respond now', '2026-01-10', 1, '2025-12-31 02:48:29', '2025-12-31 03:37:58'),
(260, 'Empress Lab Tandxr', '', '+919988776787', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond now', '2026-01-10', 1, '2025-12-31 02:48:33', '2025-12-31 03:36:38'),
(261, 'Dynasty Lab Tandxs', '', '+919988776788', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Not valid whatsapp', '2026-01-10', 1, '2025-12-31 02:48:38', '2025-12-31 03:35:28'),
(262, 'Eternal Lab Tandxt', '', '+919988776789', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent whatsapp on message not respond now', '2026-01-10', 1, '2025-12-31 02:48:42', '2025-12-31 03:34:08'),
(263, 'Infinite Lab Tandxu', '', '+919988776790', '', 'Namaste सर,\r\nमैं  Umakant Yadav, Jaunpur से बोल रहा हूँ।\r\nआपकी lab के लिए अभी reports और billing register पर करते हैं या software use करते हैं?', 'Pathology पहला संपर्क', 'Sent Whatsapp message not respond', '2026-01-10', 1, '2025-12-31 02:48:46', '2025-12-31 03:32:43'),
(264, 'Dr. Lal PathLabs - Delhi', 'delhi@lalpathlabs.com', '+919811012345', 'Dr. Lal PathLabs', '', 'Pathology Services', 'Not valid Whatsapp', '2026-02-14', 1, '2025-12-31 23:41:48', '2026-01-01 00:34:28'),
(265, 'Metropolis Healthcare - Mumbai', 'contact@metropolis.co.in', '+919820123456', 'Metropolis Healthcare', '', 'Diagnostic Laboratory', NULL, '2026-01-01', 1, '2025-12-31 23:42:08', NULL),
(266, 'Vijaya Diagnostic Centre - Bangalore', 'info@vijaya.co.in', '+919900234567', 'Vijaya Diagnostics', '', 'Pathology Center', NULL, '2026-01-01', 1, '2025-12-31 23:42:26', NULL),
(267, 'Redcliffe Labs - Hyderabad', 'redcliffe@labs.com', '+918765432101', 'Redcliffe', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:43:22', NULL),
(268, 'Healthians Diagnostic Labs', 'contact@healthians.com', '+918765432102', 'Healthians', '', 'Diagnostic Services', NULL, '2026-01-01', 1, '2025-12-31 23:43:42', NULL),
(269, 'SRL Diagnostics Centre - Mumbai', 'mumbai@srl.com', '+918765432103', 'SRL Diagnostics', '', 'Pathology Lab', NULL, '2026-01-01', 1, '2025-12-31 23:43:59', NULL),
(270, 'Apollo Diagnostics - Delhi', 'delhi@apollo.co.in', '+918765432104', 'Apollo', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:44:14', NULL),
(271, 'Dr Vimal Pathology - Bengaluru', 'bengaluru@vimal.com', '+918765432105', 'Vimal', '', 'Pathology', NULL, '2026-01-01', 1, '2025-12-31 23:44:25', NULL),
(272, 'Aster Diagnostics - Pune', 'pune@aster.co.in', '+918765432106', 'Aster', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:44:39', NULL),
(273, 'Suburban Diagnostics - Mumbai', 'contact@suburban.co.in', '+918765432107', 'Suburban', '', 'Laboratory', NULL, '2026-01-01', 1, '2025-12-31 23:44:50', NULL),
(274, 'Pathcare Diagnostics - Kolkata', 'kolkata@pathcare.com', '+918765432108', 'Pathcare', '', 'Diagnostics', 'Not valid Whatsapp', '2026-01-17', 1, '2025-12-31 23:45:01', '2026-01-01 00:35:15'),
(275, 'Ganesh Diagnostic Centre - Chennai', 'chennai@ganesh.com', '+918765432110', 'Ganesh', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:45:26', NULL),
(276, 'Diagnostic Solutions - Hyderabad', 'hyd@diagsol.com', '+918765432111', 'DiagSol', '', 'Pathology', NULL, '2026-01-01', 1, '2025-12-31 23:45:40', NULL),
(277, 'Matrix Diagnostics - Ahmedabad', 'ahmd@matrix.com', '+918765432112', 'Matrix', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:45:51', NULL),
(278, 'Care Diagnostics - Surat', 'surat@care.com', '+918765432113', 'Care', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:46:02', NULL),
(279, 'ProCare Lab - Jaipur', 'jaipur@procare.com', '+918765432114', 'ProCare', '', 'Laboratory', NULL, '2026-01-01', 1, '2025-12-31 23:46:13', NULL),
(280, 'Lakshya Diagnostics - Lucknow', 'lucknow@lakshya.com', '+918765432115', 'Lakshya', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:46:24', NULL),
(281, 'NextGen Labs - Indore', 'indore@nextgen.com', '+918765432116', 'NextGen', '', 'Laboratory', NULL, '2026-01-01', 1, '2025-12-31 23:46:35', NULL),
(282, 'Spark Diagnostics - Nagpur', 'nagpur@spark.com', '+918765432117', 'Spark', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:46:47', NULL),
(283, 'Sunrise Labs - Bhopal', 'bhopal@sunrise.com', '+918765432118', 'Sunrise', '', 'Laboratory', NULL, '2026-01-01', 1, '2025-12-31 23:46:59', NULL),
(284, 'Vikram Diagnostics - Vadodara', 'vadodara@vikram.com', '+918765432119', 'Vikram', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:49:58', NULL),
(285, 'Unity Labs - Nashik', 'nashik@unity.com', '+918765432120', 'Unity', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:50:16', NULL),
(286, 'Raj Pathology - Raipur', 'raipur@raj.com', '+918765432121', 'Raj', '', 'Pathology', NULL, '2026-01-01', 1, '2025-12-31 23:50:33', NULL),
(287, 'Raj Pathology - Raipur', 'raipur123@raj.com', '+918765432150', 'Raj', '', 'Pathology', NULL, '2026-01-01', 1, '2025-12-31 23:52:46', NULL),
(288, 'Elite Labs - Visakhapatnam', 'viz@elite.com', '+919765432122', 'Elite', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:53:14', NULL),
(289, 'Precision Labs - Kochi', 'kochi@precision.com', '+919765432123', 'Precision', '', 'Lab', NULL, '2026-01-01', 1, '2025-12-31 23:53:27', NULL),
(290, 'MediCare Lab - Amritsar', 'amritsar@medicare.com', '+919765432124', 'MediCare', '', 'Lab', NULL, '2026-01-01', 1, '2025-12-31 23:53:42', NULL),
(291, 'Miracle Labs - Guwahati', 'guwahati@miracle.com', '+919765432125', 'Miracle', '', 'Diagnostics', NULL, '2026-01-01', 1, '2025-12-31 23:53:55', NULL),
(292, 'Dr. Sharma PathLab - Lucknow', 'lucknow1@sharma.com', '+917500000001', 'Dr Sharma', '', 'Pathology UP', NULL, '2026-01-01', 1, '2026-01-01 00:03:14', NULL),
(293, 'Perfect Diagnostics - Kanpur', 'kanpur@perfect.com', '+917500000002', 'Perfect', '', 'Diagnostics', NULL, '2026-01-01', 1, '2026-01-01 00:03:31', NULL),
(294, 'Shanti PathLab - Agra', 'agra@shanti.com', '+917500000003', 'Shanti', '', 'PathLab', NULL, '2026-01-01', 1, '2026-01-01 00:03:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `followup_templates`
--

CREATE TABLE `followup_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `followup_templates`
--

INSERT INTO `followup_templates` (`id`, `template_name`, `content`, `created_at`, `updated_at`, `created_by`) VALUES
(12, 'Approach  Pathology', '<p><span style=\"letter-spacing: 0.08px; color: oklch(0.3039 0.04 213.68); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-size: 1rem;\">PathoLab Pro आपके pathology lab के लिए तैयार है! v1.0.4 में latest security &amp; compliance updates।</span></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">आज खरीदें तो 20% डिस्काउंट + फ्री सेटअप!&nbsp;</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">+91-9453619260 (Umakant Yadav)</span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">WhatsApp: </span></font><a href=\"https://wa.me/919453619260\" target=\"_blank\">https://wa.me/919453619260</a></p><p><br></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">https://hospital.codeapka.com/contact.php</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">demo login details</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">user&nbsp; demo</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">password demo@12345</span></font></p><p><font color=\"#003913\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\"><br></span></font></p><p><font color=\"oklch(0.3039 0.04 213.68)\" face=\"fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji, Hiragino Sans, PingFang SC, Apple SD Gothic Neo, Yu Gothic, Microsoft YaHei, Microsoft JhengHei, Meiryo\"><span style=\"letter-spacing: 0.08px;\">अवसर मत छोड़ें - transforming healthcare starts now!&nbsp;</span></font></p>', '2025-12-29 04:52:54', '2025-12-30 05:20:44', 1),
(13, 'Pathology पहला संपर्क', '<p><span style=\"color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">Namaste सर,</span><br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\"><span style=\"color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">मैं&nbsp; Umakant Yadav</span><span style=\"color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">,&nbsp;</span><span style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: oklch(0.3039 0.04 213.68 / 0.75); display: inline-block; padding-bottom: 0.5rem; font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">Jaunpur</span><span style=\"color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">&nbsp;से बोल रहा हूँ।</span><br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\"><span style=\"color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">आपकी lab के लिए अभी reports और billing&nbsp;</span><span style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">register पर करते हैं या software use करते हैं?</span></p>', '2025-12-30 05:30:31', '2025-12-30 05:31:44', 1),
(14, 'Pathology  अगर उनके पास software नहीं है / manual काम है', '<p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-right: 0px; margin-left: 0px; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">Thank you सर reply के लिए।<br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ;\">मैं labs के लिए एक छोटा लेकिन powerful&nbsp;<span style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: inherit;\">pathology lab software</span>&nbsp;देता हूँ, जिसमें:</p><ul class=\"marker:text-quiet list-disc\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; list-style-position: initial; list-style-image: initial; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; padding-inline-start: 1.625em; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\"><li class=\"py-0 my-0 prose-p:pt-0 prose-p:mb-2 prose-p:my-0 [&amp;&gt;p]:pt-0 [&amp;&gt;p]:mb-2 [&amp;&gt;p]:my-0\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; padding-inline-start: 0.375em;\"><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding-top: 0px;\">Patient registration + report ready बस 1–2 clicks में</p></li><li class=\"py-0 my-0 prose-p:pt-0 prose-p:mb-2 prose-p:my-0 [&amp;&gt;p]:pt-0 [&amp;&gt;p]:mb-2 [&amp;&gt;p]:my-0\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; padding-inline-start: 0.375em;\"><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding-top: 0px;\">Due payments का पूरा हिसाब अपने आप</p></li><li class=\"py-0 my-0 prose-p:pt-0 prose-p:mb-2 prose-p:my-0 [&amp;&gt;p]:pt-0 [&amp;&gt;p]:mb-2 [&amp;&gt;p]:my-0\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; padding-inline-start: 0.375em;\"><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding-top: 0px;\">Old reports seconds में search हो जाती हैं</p></li></ul><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-right: 0px; margin-left: 0px; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">मैं आपको&nbsp;<span style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: inherit; display: inline-block; padding-bottom: 0.5rem;\">10 मिनट का free demo</span>&nbsp;दे सकता हूँ (online या visit), अगर आपको पसंद आए तो ही आगे बात करेंगे।<br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ;\">आपके लिए कब ठीक रहेगा –&nbsp;<span style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: inherit;\">आज शाम</span>&nbsp;या&nbsp;<span style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: inherit;\">कल सुबह</span>?</p>', '2025-12-30 05:32:48', NULL, 1);
INSERT INTO `followup_templates` (`id`, `template_name`, `content`, `created_at`, `updated_at`, `created_by`) VALUES
(15, 'Pathology अगर बोले – already software है', '<p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-right: 0px; margin-left: 0px; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">Thank you सर, अच्छा लगा सुनकर कि आप already software use कर रहे हैं।<br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ;\">मैं बस ये समझना चाहता था – आपके current software में आपको सबसे ज़्यादा दिक्कत किस चीज़ में आती है?</p><ul class=\"marker:text-quiet list-disc\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; list-style-position: initial; list-style-image: initial; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; padding-inline-start: 1.625em; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\"><li class=\"py-0 my-0 prose-p:pt-0 prose-p:mb-2 prose-p:my-0 [&amp;&gt;p]:pt-0 [&amp;&gt;p]:mb-2 [&amp;&gt;p]:my-0\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; padding-inline-start: 0.375em;\"><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding-top: 0px;\">Speed / report बनाने में time</p></li><li class=\"py-0 my-0 prose-p:pt-0 prose-p:mb-2 prose-p:my-0 [&amp;&gt;p]:pt-0 [&amp;&gt;p]:mb-2 [&amp;&gt;p]:my-0\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; padding-inline-start: 0.375em;\"><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding-top: 0px;\">Support / problem आने पर help न मिलना</p></li><li class=\"py-0 my-0 prose-p:pt-0 prose-p:mb-2 prose-p:my-0 [&amp;&gt;p]:pt-0 [&amp;&gt;p]:mb-2 [&amp;&gt;p]:my-0\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; padding-inline-start: 0.375em;\"><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding-top: 0px;\">Report format या test add/update कराने में दिक्कत</p></li></ul><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-right: 0px; margin-left: 0px; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">अगर आप चाहें तो मैं&nbsp;<span style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: inherit; display: inline-block; padding-bottom: 0.5rem;\">आपके current काम के हिसाब से</span>&nbsp;एक छोटा‑सा demo दिखा सकता हूँ,<br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ;\">ताकि आप compare कर पाएं – better लगे तभी सोचिएगा .</p>', '2025-12-30 05:34:28', '2025-12-30 06:57:28', 1),
(16, 'Pthology  जिनको पहले message कर चुके हो – follow‑up', '<p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-right: 0px; margin-left: 0px; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">Namaste सर,<br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ;\">last time मैंने आपसे आपके lab के लिए software के बारे में message किया था।<br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ;\">बस ये पूछना था – क्या manual system में आपको ये problems आती हैं?</p><ul class=\"marker:text-quiet list-disc\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; list-style-position: initial; list-style-image: initial; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding: 0px; padding-inline-start: 1.625em; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\"><li class=\"py-0 my-0 prose-p:pt-0 prose-p:mb-2 prose-p:my-0 [&amp;&gt;p]:pt-0 [&amp;&gt;p]:mb-2 [&amp;&gt;p]:my-0\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; padding-inline-start: 0.375em;\"><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding-top: 0px;\">Reports लिखने में ज़्यादा time लगना</p></li><li class=\"py-0 my-0 prose-p:pt-0 prose-p:mb-2 prose-p:my-0 [&amp;&gt;p]:pt-0 [&amp;&gt;p]:mb-2 [&amp;&gt;p]:my-0\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; padding-inline-start: 0.375em;\"><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding-top: 0px;\">Due payments का हिसाब रखना</p></li><li class=\"py-0 my-0 prose-p:pt-0 prose-p:mb-2 prose-p:my-0 [&amp;&gt;p]:pt-0 [&amp;&gt;p]:mb-2 [&amp;&gt;p]:my-0\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; padding-inline-start: 0.375em;\"><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-top: 0px; margin-right: 0px; margin-left: 0px; padding-top: 0px;\">पुराने reports ढूँढने में दिक्कत</p></li></ul><p class=\"my-2 [&amp;+p]:mt-4 [&amp;_strong:has(+br)]:inline-block [&amp;_strong:has(+br)]:pb-2\" style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; margin-right: 0px; margin-left: 0px; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">अगर इनमें से कुछ issue है, तो मैं आपको&nbsp;<span style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: inherit; display: inline-block; padding-bottom: 0.5rem;\">10 मिनट का free demo</span>&nbsp;दिखा सकता हूँ।<br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ;\">अगर अभी जरूरत नहीं है तो बस “NO” लिखकर बता दीजिए, फिर मैं disturb नहीं करूँगा।&nbsp;</p>', '2025-12-30 05:36:10', NULL, 1);
INSERT INTO `followup_templates` (`id`, `template_name`, `content`, `created_at`, `updated_at`, `created_by`) VALUES
(17, 'Pathology जिनको पहले message कर चुके हो – follow‑up 2', '<p><span style=\"color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">Namaste सर,</span><br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\"><span style=\"color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">ये मेरा final message है ताकि आपको बार‑बार disturb न करूँ।</span><br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\"><span style=\"color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">अगर future में कभी lab software की जरूरत पड़े तो मेरा contact save कर सकते हैं:</span><br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\"><span style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: oklch(0.3039 0.04 213.68 / 0.75); display: inline-block; padding-bottom: 0.5rem; font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">Umakant Yadav– +91-9453619260</span><br style=\"border-width: 0px; border-style: solid; border-color: oklch(0.3039 0.04 213.68 / 0.16); scrollbar-color: initial; scrollbar-width: initial; --tw-border-spacing-x: 0; --tw-border-spacing-y: 0; --tw-translate-x: 0; --tw-translate-y: 0; --tw-rotate: 0; --tw-skew-x: 0; --tw-skew-y: 0; --tw-scale-x: 1; --tw-scale-y: 1; --tw-pan-x: ; --tw-pan-y: ; --tw-pinch-zoom: ; --tw-scroll-snap-strictness: proximity; --tw-gradient-from-position: ; --tw-gradient-via-position: ; --tw-gradient-to-position: ; --tw-ordinal: ; --tw-slashed-zero: ; --tw-numeric-figure: ; --tw-numeric-spacing: ; --tw-numeric-fraction: ; --tw-ring-inset: ; --tw-ring-offset-width: 0px; --tw-ring-offset-color: #fff; --tw-ring-color: rgb(59 130 246 / .5); --tw-ring-offset-shadow: 0 0 #0000; --tw-ring-shadow: 0 0 #0000; --tw-shadow: 0 0 #0000; --tw-shadow-colored: 0 0 #0000; --tw-blur: ; --tw-brightness: ; --tw-contrast: ; --tw-grayscale: ; --tw-hue-rotate: ; --tw-invert: ; --tw-saturate: ; --tw-sepia: ; --tw-drop-shadow: ; --tw-backdrop-blur: ; --tw-backdrop-brightness: ; --tw-backdrop-contrast: ; --tw-backdrop-grayscale: ; --tw-backdrop-hue-rotate: ; --tw-backdrop-invert: ; --tw-backdrop-opacity: ; --tw-backdrop-saturate: ; --tw-backdrop-sepia: ; --tw-contain-size: ; --tw-contain-layout: ; --tw-contain-paint: ; --tw-contain-style: ; color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\"><span style=\"color: oklch(0.3039 0.04 213.68 / 0.75); font-family: fkGroteskNeue, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Roboto, &quot;Helvetica Neue&quot;, Arial, &quot;Noto Sans&quot;, sans-serif, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, &quot;Segoe UI Symbol&quot;, &quot;Noto Color Emoji&quot;, &quot;Hiragino Sans&quot;, &quot;PingFang SC&quot;, &quot;Apple SD Gothic Neo&quot;, &quot;Yu Gothic&quot;, &quot;Microsoft YaHei&quot;, &quot;Microsoft JhengHei&quot;, Meiryo; font-style: italic; letter-spacing: 0.08px; background-color: oklch(0.9902 0.004 106.47);\">आपका time देने के लिए धन्यवाद, सर।&nbsp;</span></p>', '2025-12-30 05:37:12', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_clients`
--

CREATE TABLE `inventory_clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Individual','Corporate','Insurance','Government') NOT NULL DEFAULT 'Individual',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `gst_number` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `notes` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_clients`
--

INSERT INTO `inventory_clients` (`id`, `name`, `type`, `email`, `phone`, `address`, `city`, `state`, `pincode`, `gst_number`, `status`, `notes`, `added_by`, `created_at`, `updated_at`) VALUES
(3, 'Ravi', 'Individual', 'info@xyz.com', '0000000000', '789 Insurance Tower', 'Bangalore', 'Bombay', '', '', 'Active', '', NULL, '2025-11-16 05:05:49', '2025-11-17 05:14:04'),
(4, 'Vishal ', 'Individual', 'john@example.com', '08427722958', '123 Main St', 'Mumbai', 'Punjab', '', '', 'Active', '', NULL, '2025-11-16 07:47:46', '2025-11-17 01:01:45'),
(5, 'Gyas', 'Individual', 'contact@abc.com', '72755 51625', '456 Business Park', 'Delhi', 'Delhi', '', '', 'Active', '', NULL, '2025-11-16 07:47:46', '2025-11-17 00:54:05'),
(6, 'Amzad', 'Individual', 'info@xyz.com', '+91 95400 52228', '', 'Delhi', 'Delhi', '', '', 'Active', '', NULL, '2025-11-16 07:47:46', '2025-12-07 11:53:43'),
(7, 'Raman', 'Individual', '', '+917087071220', '', '', '', '', '', 'Active', '', NULL, '2025-11-17 15:43:46', NULL),
(8, 'Dharmendra Bachheriya', 'Individual', '', '+91 83064 02805', '', '', '', '', '', 'Active', '', NULL, '2025-11-17 15:51:48', NULL),
(9, 'Ezaz From Ireland', 'Individual', '', '+353 87 355 5012', '', '', '', '', '', 'Active', '', NULL, '2025-11-17 15:54:16', NULL),
(10, 'Pankaj Freelancer', 'Individual', '', '98187 49023', '', '', '', '', '', 'Active', '', NULL, '2025-11-17 15:57:23', NULL),
(11, 'Brendan Australia', 'Individual', 'info@sketchfurniture.com.au', '+61 414 739 495', 'https://sketchfurniture.com.au/', '', '', '', '', 'Active', '', NULL, '2025-11-17 16:04:24', NULL),
(12, 'CRM', 'Individual', '', '+64 22 317 3318', 'New Zealand', '', '', '', '', 'Active', '', NULL, '2025-11-24 06:38:24', NULL),
(13, 'Rajesh', 'Individual', '', '09453619260', 'Jaunpur Rd', 'Jaunpur', 'Uttar Pradesh', '222001', '', 'Active', '', 1, '2025-12-24 02:23:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_expense`
--

CREATE TABLE `inventory_expense` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `category` varchar(100) NOT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Card','UPI','Bank Transfer','Cheque') NOT NULL DEFAULT 'Cash',
  `payment_status` enum('Success','Pending','Failed') NOT NULL DEFAULT 'Success',
  `invoice_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `inventory_expense`
--

INSERT INTO `inventory_expense` (`id`, `date`, `category`, `vendor`, `description`, `amount`, `payment_method`, `payment_status`, `invoice_number`, `notes`, `added_by`, `created_at`, `updated_at`) VALUES
(11, '2025-11-13', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 4500.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-11-17 06:24:27', NULL),
(12, '2025-11-15', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 1100.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-11-17 06:24:31', NULL),
(13, '2025-11-16', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 1000.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-11-17 06:24:31', NULL),
(14, '2025-11-17', 'Medical Supplies', 'Dr Tahsheel', 'Sarthak and  saksham madicine', 100.00, 'UPI', 'Success', '', '', NULL, '2025-11-17 07:27:26', '2025-11-17 07:38:30'),
(15, '2025-11-17', 'Other', 'Surylal Maurya', 'Biskit purchage  ', 70.00, 'UPI', 'Success', '', '', NULL, '2025-11-17 12:09:19', NULL),
(17, '2025-11-21', 'Other', 'Pay by Airtel bank', 'Pay by Airtel bank', 1000.00, 'UPI', 'Success', '', '', NULL, '2025-11-21 00:55:00', NULL),
(18, '2025-11-24', 'Other', 'Expense', 'sd', 1000.00, 'UPI', 'Success', '', '', NULL, '2025-11-24 06:36:50', NULL),
(20, '2025-11-25', 'Other', 'd', 'd', 500.00, 'UPI', 'Success', '', '', NULL, '2025-11-25 15:06:04', NULL),
(30, '2025-12-04', 'Utilities', 'Sagar', 'For cloth', 550.00, 'UPI', 'Pending', '', '', NULL, '2025-12-04 10:12:31', NULL),
(31, '2025-12-07', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 1000.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-07 12:00:30', NULL),
(32, '2025-12-08', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 300.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-09 00:00:23', NULL),
(33, '2025-12-15', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 100.00, 'Bank Transfer', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-16 00:00:19', NULL),
(34, '2025-12-17', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 100.00, 'Bank Transfer', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-17 12:00:21', NULL),
(35, '2025-12-18', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 706.82, 'Bank Transfer', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-18 12:00:21', NULL),
(36, '2025-12-18', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 100.00, 'Bank Transfer', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-18 12:00:22', NULL),
(37, '2025-12-19', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 10.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-19 12:00:17', NULL),
(38, '2025-12-20', 'Utilities', 'Milk', 'Milk', 600.00, 'Cash', 'Success', '', '', 1, '2025-12-21 00:57:10', NULL),
(39, '2025-12-22', 'Utilities', 'Amazon', 'Amazon', 2200.00, 'UPI', 'Success', '', '', 1, '2025-12-24 02:25:40', NULL),
(40, '2025-12-24', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 10.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-24 12:00:19', NULL),
(41, '2025-12-25', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 10.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-25 12:00:23', NULL),
(42, '2025-12-26', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 10.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-27 00:00:18', NULL),
(43, '2025-12-26', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 1.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-27 00:00:19', NULL),
(44, '2025-12-25', 'Other', 'Fiber Recharge', 'Recharge', 706.00, 'UPI', 'Success', '', '', 1, '2025-12-27 03:13:04', NULL),
(45, '2025-12-27', 'Other', 'Group d Exam', 'Group d Exam', 2000.00, 'Cash', 'Success', '', '', 1, '2025-12-27 03:14:00', NULL),
(46, '2025-12-27', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 10.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-27 12:00:21', NULL),
(47, '2025-12-27', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 10.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-27 12:00:22', NULL),
(48, '2025-12-28', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 1400.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-28 12:00:21', NULL),
(49, '2025-12-28', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 10.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-28 12:00:21', NULL),
(50, '2025-12-28', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 10.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-28 12:00:23', NULL),
(51, '2025-12-29', 'Other', 'no-reply@amazonpay.in', 'Payment Reminder', 10.00, 'UPI', 'Success', NULL, 'Auto-imported from email', NULL, '2025-12-30 00:00:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_income`
--

CREATE TABLE `inventory_income` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `category` varchar(100) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Card','UPI','Bank Transfer','Cheque') NOT NULL DEFAULT 'Cash',
  `payment_status` enum('Success','Pending','Failed') NOT NULL DEFAULT 'Success',
  `notes` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `inventory_income`
--

INSERT INTO `inventory_income` (`id`, `date`, `category`, `client_id`, `description`, `amount`, `payment_method`, `payment_status`, `notes`, `added_by`, `created_at`, `updated_at`) VALUES
(11, '2025-11-07', 'Other Services', 4, 'sallary', 1925.00, 'UPI', 'Success', '', NULL, '2025-11-17 12:45:29', '2025-12-12 02:46:04'),
(12, '2025-11-01', 'Other Services', 3, 'Sallary', 2000.00, 'UPI', 'Success', '', NULL, '2025-11-17 12:46:10', NULL),
(13, '2025-11-15', 'Other Services', 5, 'Sall', 1079.00, 'UPI', 'Success', '', NULL, '2025-11-17 12:47:09', '2025-11-25 15:08:06'),
(14, '2025-12-18', 'Other Services', 3, 'for https://bombaybeatsstudios.com/', 1000.00, 'UPI', 'Pending', '', NULL, '2025-11-21 01:18:24', '2025-12-19 03:23:50'),
(15, '2025-12-08', 'Other Services', 4, 'sallary', 1450.00, 'UPI', 'Success', '', NULL, '2025-11-21 01:32:25', '2025-12-12 02:46:52'),
(16, '2025-11-22', 'Other Services', 10, 'Computer software devlopment', 5000.00, 'UPI', 'Pending', '', NULL, '2025-11-22 02:22:58', NULL),
(17, '2025-11-22', 'Other Services', 3, '', 1000.00, 'UPI', 'Success', '', NULL, '2025-11-22 09:26:35', NULL),
(18, '2025-11-25', 'Other Services', 5, '', 500.00, 'UPI', 'Success', '', NULL, '2025-11-25 15:09:25', '2025-11-26 01:36:37'),
(20, '2025-12-23', 'Other Services', 6, '', 1000.00, 'UPI', 'Success', ' for  https://allcurepharmacys.com/', NULL, '2025-12-04 10:11:32', '2025-12-23 07:02:53'),
(21, '2025-12-07', 'Other Services', 6, '', 1000.00, 'UPI', 'Success', 'given for  https://allcurepharmacys.com/', NULL, '2025-12-07 11:47:33', NULL),
(22, '2025-12-10', 'Other Services', 8, '', 400.00, 'UPI', 'Success', '', 1, '2025-12-10 05:19:48', '2025-12-11 14:09:52'),
(23, '2025-12-11', 'Other Services', 3, 'for https://bombaybeatsstudios.com/', 1000.00, 'UPI', 'Success', '', 1, '2025-12-10 23:11:20', '2025-12-12 02:48:29'),
(24, '2025-12-18', 'Other Services', 3, 'for https://bombaybeatsstudios.com/', 1000.00, 'UPI', 'Success', '', 1, '2025-12-18 13:10:05', '2025-12-18 13:10:32'),
(25, '2025-12-19', 'Other Services', 3, 'for https://bombaybeatsstudios.com/', 1000.00, 'UPI', 'Success', '', 1, '2025-12-19 03:23:09', NULL),
(26, '2025-12-24', 'Other Services', 13, 'Advance payment 1000', 1000.00, 'UPI', 'Success', '', 1, '2025-12-24 02:24:05', NULL),
(27, '2025-12-30', 'Other Services', 8, '', 800.00, 'UPI', 'Success', 'Given for  website changes', 1, '2025-12-30 06:58:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `main_test_categories`
--

CREATE TABLE `main_test_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `main_test_categories`
--

INSERT INTO `main_test_categories` (`id`, `name`, `description`, `added_by`, `created_at`, `updated_at`) VALUES
(30, 'Hematology', '', 1, '2025-12-05 09:40:25', '2025-12-05 09:40:25'),
(31, 'Biochemistry', '', 1, '2025-12-06 00:34:34', '2025-12-06 00:34:34'),
(32, 'Endocrine', '', 1, '2025-12-06 00:56:29', '2025-12-06 00:56:29'),
(33, 'Vitamins', '', 1, '2025-12-06 00:59:26', '2025-12-06 00:59:26'),
(34, 'Immunology', '', 1, '2025-12-06 01:02:22', '2025-12-06 01:02:22'),
(35, 'Infectious Disease', '', 1, '2025-12-06 01:06:51', '2025-12-06 01:06:51'),
(37, 'Widal', '', 1, '2025-12-16 15:36:52', '2025-12-16 15:36:52');

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
(4, NULL, 'Test Notice 084539 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:53', NULL, 0, 1, NULL, NULL),
(5, NULL, 'Test Notice 084539 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:53', NULL, 0, 1, NULL, NULL),
(7, NULL, 'Happy New Years', 'Happy New Years', '2026-01-01 07:53:00', '2026-01-10 07:53:00', 1, 1, '2026-01-01 07:53:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `opd_appointments`
--

CREATE TABLE `opd_appointments` (
  `id` int(11) NOT NULL,
  `appointment_id` varchar(50) DEFAULT NULL,
  `appointment_number` varchar(50) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `doctor_id` int(11) NOT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `appointment_type` varchar(50) DEFAULT NULL,
  `appointment_type_id` int(11) DEFAULT NULL,
  `status` enum('scheduled','confirmed','in_progress','inprogress','completed','cancelled','no_show','noshow') DEFAULT 'scheduled',
  `reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `consultation_notes` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT 0.00,
  `payment_status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opd_appointment_types`
--

CREATE TABLE `opd_appointment_types` (
  `id` int(11) NOT NULL,
  `type_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 30,
  `color` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opd_appointment_types`
--

INSERT INTO `opd_appointment_types` (`id`, `type_id`, `name`, `type_name`, `description`, `duration_minutes`, `color`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Consultation', NULL, 'General consultation', 30, NULL, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(2, NULL, 'Follow-up', NULL, 'Follow-up visit', 20, NULL, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(3, NULL, 'Emergency', NULL, 'Emergency consultation', 15, NULL, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(4, NULL, 'Checkup', NULL, 'Regular health checkup', 45, '#cf1717', 1, 'active', '2025-11-24 07:06:20', '2025-12-11 11:52:30'),
(5, NULL, 'Procedure', NULL, 'Medical procedure', 60, '#000000', 1, 'active', '2025-11-24 07:06:20', '2025-12-11 11:52:21');

-- --------------------------------------------------------

--
-- Table structure for table `opd_billing`
--

CREATE TABLE `opd_billing` (
  `id` int(11) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `patient_phone` varchar(50) DEFAULT NULL,
  `patient_age` int(11) DEFAULT NULL,
  `patient_gender` varchar(20) DEFAULT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `medicine_charges` decimal(10,2) DEFAULT 0.00,
  `lab_charges` decimal(10,2) DEFAULT 0.00,
  `other_charges` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `balance_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `payment_status` varchar(50) DEFAULT 'Unpaid',
  `bill_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opd_departments`
--

CREATE TABLE `opd_departments` (
  `id` int(11) NOT NULL,
  `department_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `department_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `head_doctor_id` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opd_departments`
--

INSERT INTO `opd_departments` (`id`, `department_id`, `name`, `department_name`, `description`, `head_doctor_id`, `location`, `phone`, `email`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Cardiology', NULL, 'Heart and cardiovascular system', NULL, NULL, NULL, NULL, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(2, NULL, 'Pediatrics', NULL, 'Child healthcare', NULL, NULL, NULL, NULL, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(3, NULL, 'Orthopedics', NULL, 'Bone and joint care', NULL, NULL, NULL, NULL, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(4, NULL, 'Neurology', NULL, 'Brain and nervous system', NULL, NULL, NULL, NULL, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(5, NULL, 'General Medicine', NULL, 'General healthcare', NULL, NULL, NULL, NULL, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `opd_doctors`
--

CREATE TABLE `opd_doctors` (
  `id` int(11) NOT NULL,
  `doctor_id` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `specialization_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `qualification` text DEFAULT NULL,
  `experience_years` int(11) DEFAULT 0,
  `license_number` varchar(50) DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `followup_fee` decimal(10,2) DEFAULT 0.00,
  `bio` text DEFAULT NULL,
  `profile_image_url` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_reviews` int(11) DEFAULT 0,
  `hospital` text NOT NULL,
  `contact_no` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `registration_no` varchar(100) NOT NULL,
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `is_active` tinyint(1) DEFAULT 1,
  `added_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opd_doctors`
--

INSERT INTO `opd_doctors` (`id`, `doctor_id`, `user_id`, `name`, `doctor_name`, `email`, `phone`, `mobile`, `specialization`, `specialization_id`, `department_id`, `qualification`, `experience_years`, `license_number`, `consultation_fee`, `followup_fee`, `bio`, `profile_image_url`, `rating`, `total_reviews`, `hospital`, `contact_no`, `address`, `registration_no`, `status`, `is_active`, `added_by`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'Brennan Hill', NULL, 'fovet@mailinator.com', '+1 (327) 134-5633', NULL, 'Incidunt repudianda', NULL, NULL, 'Proident enim debit', 0, NULL, 0.00, 0.00, NULL, NULL, 0.00, 0, 'Cum ut soluta ipsa', '+1 (195) 382-7446', 'Placeat amet ea vo', '578', 'active', 1, 1, '2026-01-01 02:47:00', '2026-01-01 02:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `opd_facilities`
--

CREATE TABLE `opd_facilities` (
  `id` int(11) NOT NULL,
  `facility_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `facility_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opd_facilities`
--

INSERT INTO `opd_facilities` (`id`, `facility_id`, `name`, `facility_name`, `description`, `type`, `location`, `capacity`, `department_id`, `is_available`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'OPD Room 1', NULL, 'Outpatient consultation room', 'Consultation', NULL, 1, NULL, 1, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(2, NULL, 'OPD Room 2', NULL, 'Outpatient consultation room', 'Consultation', NULL, 1, NULL, 1, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(3, NULL, 'Emergency Room', NULL, 'Emergency treatment room', 'Emergency', NULL, 5, NULL, 1, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(4, NULL, 'X-Ray Room', NULL, 'Radiology imaging', 'Diagnostic', NULL, 2, NULL, 1, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(5, NULL, 'Laboratory', NULL, 'Medical testing facility', 'Lab', NULL, 10, NULL, 1, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `opd_medical_records`
--

CREATE TABLE `opd_medical_records` (
  `id` int(11) NOT NULL,
  `record_id` varchar(50) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `record_date` date NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `attachments` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opd_patients`
--

CREATE TABLE `opd_patients` (
  `id` int(11) NOT NULL,
  `patient_id` varchar(50) NOT NULL,
  `patient_number` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `dob` date NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `address` text DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `profile_image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opd_patients`
--

INSERT INTO `opd_patients` (`id`, `patient_id`, `patient_number`, `name`, `full_name`, `patient_name`, `email`, `phone`, `mobile`, `contact`, `dob`, `date_of_birth`, `gender`, `address`, `blood_group`, `emergency_contact`, `emergency_contact_name`, `emergency_contact_phone`, `medical_history`, `allergies`, `current_medications`, `profile_image_url`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(1, '', NULL, 'test p', NULL, NULL, '', '6767676', NULL, NULL, '1971-01-01', NULL, 'male', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'active', '2026-01-01 02:17:27', '2026-01-01 02:17:27'),
(4, 'PAT-20260101-B85626', NULL, 'P2', NULL, NULL, '', '7667766767', NULL, NULL, '1981-01-01', NULL, 'male', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'active', '2026-01-01 02:27:11', '2026-01-01 02:27:11');

-- --------------------------------------------------------

--
-- Table structure for table `opd_prescriptions`
--

CREATE TABLE `opd_prescriptions` (
  `id` int(11) NOT NULL,
  `prescription_id` varchar(50) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `prescription_date` date NOT NULL,
  `medications` text NOT NULL,
  `dosage` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opd_reports`
--

CREATE TABLE `opd_reports` (
  `id` int(11) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `patient_phone` varchar(50) DEFAULT NULL,
  `patient_age` int(11) DEFAULT NULL,
  `patient_gender` varchar(20) DEFAULT NULL,
  `doctor_name` varchar(255) DEFAULT NULL,
  `report_date` date DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `symptoms` text DEFAULT NULL,
  `test_results` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opd_specializations`
--

CREATE TABLE `opd_specializations` (
  `id` int(11) NOT NULL,
  `specialization_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `specialization_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opd_specializations`
--

INSERT INTO `opd_specializations` (`id`, `specialization_id`, `name`, `specialization_name`, `description`, `department_id`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Interventional Cardiology', NULL, 'Heart procedures and interventions', 1, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(2, NULL, 'Pediatric Surgery', NULL, 'Surgical care for children', 2, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(3, NULL, 'Sports Medicine', NULL, 'Sports injury treatment', 3, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(4, NULL, 'Neurosurgery', NULL, 'Brain and spine surgery', 4, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20'),
(5, NULL, 'Internal Medicine', NULL, 'Adult general medicine', 5, 1, 'active', '2025-11-24 07:06:20', '2025-11-24 07:06:20');

-- --------------------------------------------------------

--
-- Table structure for table `opd_users`
--

CREATE TABLE `opd_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `role` enum('admin','doctor','nurse','receptionist','patient') DEFAULT 'patient',
  `user_type` varchar(20) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opd_users`
--

INSERT INTO `opd_users` (`id`, `username`, `email`, `password`, `name`, `full_name`, `phone`, `mobile`, `role`, `user_type`, `specialization`, `license_number`, `is_active`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@hospital.com', '$2y$12$HPL7R9H5Rr3a/n7TcAR0nOqIiM0GCqIE0WWd1rKhuWgafOuUuWSXa', 'Administrator', NULL, '', NULL, 'admin', NULL, '', '', 1, 'active', '2025-11-24 07:06:20', '2025-12-05 06:23:30'),
(2, 'doctor', 'doc@gmail.com', '$2y$12$yeplk3EgwtDqZMbgjUlqtuzL9g/lZqCsIj1ikEK1PLvrvMCYFKFeq', 'doctor', NULL, '+919876543210', NULL, 'doctor', NULL, '', '', 1, 'active', '2025-12-16 09:00:17', '2026-01-01 01:36:23');

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
(4, NULL, 'Support (Umakant Yadav)', '+91-9453619260', '+91-9453619260', 'uky171991@gmail.com', '', 'https://codeapka.com/', 1, NULL, '2025-12-25 09:33:29'),
(5, NULL, 'Support  (Ghayas Ahmad)', '+91-9876543210', '+91-9876543210', 'testowner@example.com', 'Test Owner Address', '', 1, NULL, '2025-12-21 19:52:17');

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
(876, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(877, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(878, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(879, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(880, NULL, 'Ramu', '', '9453619260', NULL, NULL, NULL, 32, '', 'Years', 'P000001', NULL, NULL, 1),
(881, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(882, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(883, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(884, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(885, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(886, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(887, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(888, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(889, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(890, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(891, NULL, 'Umat', '', '9453619260', NULL, 'gg', NULL, 40, '', 'years', 'P000002', NULL, NULL, 1),
(892, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(893, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(894, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(895, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1),
(896, NULL, 'ertg', '', '5656565656', NULL, NULL, NULL, 5, '', 'Years', 'qw', NULL, NULL, 1);

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
(2, 'Basic Plan', '', 299.00, '8081674028@upi', 'monthly', NULL, NULL, 'uploads/qr/1756541839_7947cffc3422.jpg', 1, '2025-08-27 05:26:40', '2025-12-07 01:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `processed_emails`
--

CREATE TABLE `processed_emails` (
  `id` int(11) NOT NULL,
  `message_id` varchar(255) NOT NULL,
  `transaction_type` enum('income','expense') NOT NULL,
  `processed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `processed_emails`
--

INSERT INTO `processed_emails` (`id`, `message_id`, `transaction_type`, `processed_at`) VALUES
(1, '<0109019a76607135-e7e745e7-7908-4788-b806-263ebf1b2e7a-000000@ap-south-1.amazonses.com>', 'expense', '2025-11-17 06:24:20'),
(2, '<1344695856.1716094.1763074775385@DCLACBSHOSTPRDAP03>', 'expense', '2025-11-17 06:24:27'),
(3, '<1292300670.40894.1763249893546@DCLACBSHOSTPRDAP03>', 'expense', '2025-11-17 06:24:31'),
(4, '<-59381021.1571670.1763268037904@DCLACBSHOSTPRDAP03>', 'expense', '2025-11-17 06:24:31'),
(5, '<0109019a9fda55ba-cd7b0fd1-5694-4b08-9aab-c50fb6e9fe7c-000000@ap-south-1.amazonses.com>', 'expense', '2025-11-20 12:00:28'),
(6, '<0109019abad7ece6-15c5085e-92a7-4e7f-8e17-0bd936512983-000000@ap-south-1.amazonses.com>', 'expense', '2025-11-25 12:00:31'),
(7, '<feopkj17641821025234409@communications.sbi.co.in>', 'income', '2025-11-27 12:00:22'),
(8, '<0109019ac53f9b73-c578c3f2-d6f9-4c5a-8cc2-f1e9a074a590-000000@ap-south-1.amazonses.com>', 'expense', '2025-11-28 00:00:24'),
(9, '<0109019acdeca3cf-d116ce85-d096-47b9-9082-fead3fb0f1c4-000000@ap-south-1.amazonses.com>', 'expense', '2025-11-29 12:00:25'),
(10, '<0109019acef15074-9382cd51-b78c-4854-b6a1-4d28ce2fae44-000000@ap-south-1.amazonses.com>', 'expense', '2025-11-29 12:00:27'),
(11, '<0109019ad424ddcf-abf4b986-a0ab-491a-ad38-d58f79b800a6-000000@ap-south-1.amazonses.com>', 'expense', '2025-11-30 12:00:32'),
(12, '<0109019ad4b5bb31-88cabc7e-fabd-4cff-a360-d9aa641e734d-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-01 00:00:35'),
(13, '<0109019ad93f9d4d-159cee8f-9549-42f2-b637-2c04688830f9-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-01 12:00:37'),
(14, '<0109019ada4b1ebc-aaa8d05f-4837-4fbc-8894-392adfb5d719-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-02 00:00:30'),
(15, '<0109019adede5559-0d4209b7-66c8-4311-b762-0741d0c215f5-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-02 12:00:43'),
(16, '<0109019ae4a86b8b-88df1ffd-8588-4977-8781-7cefc924fb30-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-04 00:00:37'),
(17, '<1116663553.11192.1765102682921@DCLACBSHOSTPRDAP07>', 'expense', '2025-12-07 12:00:30'),
(18, '<-1142419656.7192074.1765210425363@DCLACBSHOSTPRDAP07>', 'expense', '2025-12-09 00:00:23'),
(19, '<0109019b22289bf0-d63c66ae-5e9d-4365-9197-d81490b5d995-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-16 00:00:19'),
(20, '<0109019b2b766db0-703e769c-dc3d-41b9-9a2e-444a8bf64888-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-17 12:00:21'),
(21, '<0109019b2f5bd5a6-078ffa41-8c91-4d64-aa2e-8e8d7452d4c6-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-18 12:00:21'),
(22, '<0109019b2fd527cd-31bc6cb2-3e92-47f0-a671-b3a19fc2441c-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-18 12:00:22'),
(23, '<0109019b347ad65b-03a5665a-0df2-4d46-811c-13b7d4520746-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-19 12:00:17'),
(24, '<0109019b4e7060b3-36b59592-af70-4c0f-9a20-0335365befc0-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-24 12:00:19'),
(25, '<0109019b54b4d44d-3bc8f0cc-339f-4a9e-b7bf-dbab930a536a-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-25 12:00:23'),
(26, '<0109019b5b173d58-965ed88d-c8e4-468a-a6e5-aec06e0dbc79-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-27 00:00:18'),
(27, '<-1718232826.6243790.1766777178551@DCLACBSHOSTPRDAP07>', 'expense', '2025-12-27 00:00:19'),
(28, '<0109019b5ddbf739-fdf4478a-2380-47c6-bba9-1334b5dcb5ad-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-27 12:00:21'),
(29, '<0109019b5f20f19c-e2a0f147-d29b-43eb-a9aa-1cdafcde2a07-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-27 12:00:22'),
(30, '<546989204.10984325.1766883000548@DCLACBSHOSTPRDAP07>', 'expense', '2025-12-28 12:00:21'),
(31, '<0109019b62f30bf3-5a48785b-9bd4-4a21-a547-0ca520fcb77f-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-28 12:00:21'),
(32, '<0109019b63b33829-e0d08211-60f1-4bae-bea6-2286eb3fee64-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-28 12:00:23'),
(33, '<0109019b6a4de577-4233c704-953c-417b-b465-d44434eac6e9-000000@ap-south-1.amazonses.com>', 'expense', '2025-12-30 00:00:21');

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
-- Table structure for table `scheduled_emails`
--

CREATE TABLE `scheduled_emails` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `to_email` text NOT NULL,
  `cc_email` text DEFAULT NULL,
  `bcc_email` text DEFAULT NULL,
  `subject` varchar(500) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'normal',
  `schedule_date` datetime NOT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `status` enum('pending','sent','failed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `sent_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sent_emails`
--

CREATE TABLE `sent_emails` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `to_email` text NOT NULL,
  `cc_email` text DEFAULT NULL,
  `bcc_email` text DEFAULT NULL,
  `subject` varchar(500) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'normal',
  `sent_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sent_emails`
--

INSERT INTO `sent_emails` (`id`, `user_id`, `to_email`, `cc_email`, `bcc_email`, `subject`, `body`, `priority`, `sent_at`) VALUES
(1, 1, 'mailer@angelbroking.in', '', '', 'Re: 🚀 Uma Kant Yadav, Nifty Holds Strong, Infy, TCS & HCL Shine, Oil Climbs', '\r\n\r\n--- Original Message ---\r\nFrom: mailer@angelbroking.in\r\nDate: Thu, 23 Oct 2025 12:26:28 +0000\r\nSubject: 🚀 Uma Kant Yadav, Nifty Holds Strong, Infy, TCS & HCL Shine, Oil Climbs\r\n\r\n<!DOCTYPE html> <html lang=\"en\"> <head> <meta charset=\"UTF-8\" /> <meta http-equiv=\"Content-Type\" content=\"text/html charset=UTF-8\" /> </head> <body> <table style=\"width: 100%; max-width: 800px; margin-left: auto; margin-right: auto;\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"> <tbody> <tr> <td colspan=\"100\" style=\"font-family: sans-serif; font-size: 12px; text-align: center; padding-bottom: 5px; color: #1b2559; font-weight: 600;\">23 October, 2025</td> </tr> <tr> <td align=\"center\" style=\"font-family: sans-serif; font-size: 11px; text-align: center; padding-bottom: 25px; height: 40px;\"> <img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/MarketCharchaLogo.png\" style=\"height: 30px;\" /> </td> </tr> <tr> <td colspan=\"100\" style=\"background: #f9fbfe; padding: 10px; border-radius: 8px; font-weight: 600;\"> <img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/Key_Takeaway.png\" style=\"width: 24px; height: 24px;\" /> Key Takeaways </td> </tr> <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-right: 10px; font-size: 14px; color: #5c7597; width: 100%;\"> <p>• <span style=\"font-weight: 600;\">Sensex and Nifty</span> : Nifty opened nearly 200 points higher and maintained momentum to reach an intraday peak of 26104. However, profit-taking occurred in the latter half of the trading session, which subsequently exacerbated the situation, resulting in the reduction of most intraday gains. Ultimately, the Nifty50 index concluded the day near 25900.</p> <p>• <span style=\"font-weight: 600;\">Sector Performance</span> : Nifty IT witnessed broad-based buying, with momentum sustained throughout the session. Considering the current price structure, the positive trend is likely to continue in this space.</p> <p>• <span style=\"font-weight: 600;\">Global Market Impact</span> : Global cues remained mixed, with no clear directional trend visible across either Asian or European markets.</p> </td> </tr> <tr> <td colspan=\"100\"> <table width=\"100%\" cellspacing=\"10\" cellpadding=\"0\" border=\"0\" style=\"margin-bottom: 20px;\"> <tbody> <tr> <td style=\"padding: 10px; border-radius: 8px; background: #2FAA491A; width: 33%;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%2Fmarkets%3Findices=SENSEX/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/n4bzbsdecpzWXx4dbGFLTYWINtA=230\" style=\"color: inherit; text-decoration: none;\"> <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"> <tbody> <tr> <td style=\"width: 100%; font-weight: 600; color: #343434; font-family: sans-serif;\">Sensex</td> </tr> <tr> <td style=\"font-size: 11px;\">84556.40</td> <td style=\"font-size: 11px;\"> <div style=\"font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500; color: #258D52;\">&#9650;&nbsp;0.15%</div> </td> </tr> </tbody> </table> </a> </td> <td style=\"padding: 10px; border-radius: 8px; background: #2FAA491A; width: 33%;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%2Fmarkets%3Findices=NIFTY/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/291SBJXSXER5SuHslxrDHwPQJA0=230\" style=\"color: inherit; text-decoration: none;\"> <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"> <tbody> <tr> <td style=\"width: 100%; font-weight: 600; color: #343434; font-family: sans-serif;\">Nifty</td> </tr> <tr> <td style=\"font-size: 11px;\">25891.40</td> <td style=\"font-size: 11px;\"> <div style=\"font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500; color: #258D52;\">&#9650;&nbsp;0.09%</div> </td> </tr> </tbody> </table> </a> </td> <td style=\"padding: 10px; border-radius: 8px; background: #2FAA491A; width: 33%;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%2Fmarkets%3Findices=BANKNIFTY/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/gkI4FWYHNxUIKwzZ_9LMosTcIuE=230\" style=\"color: inherit; text-decoration: none;\"> <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"> <tbody> <tr> <td style=\"width: 100%; font-weight: 600; color: #343434; font-family: sans-serif;\">Banknifty</td> </tr> <tr> <td style=\"font-size: 11px;\">58078.05</td> <td style=\"font-size: 11px;\"> <div style=\"font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500; color: #258D52;\">&#9650;&nbsp;0.12%</div> </td> </tr> </tbody> </table> </a> </td> </tr> </tbody> <tbody></tbody> </table> </td> </tr> <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-right: 10px; font-weight: 600; width: 100%; font-size: 14px; font-family: sans-serif; padding-bottom: 5px;\">NIFTY 50</td> </tr> <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-right: 10px; font-size: 16px; text-align: left;\"> 25891.40 <span style=\"color: #258D52; font-size: 12px;\">+22.80 </span> <span style=\"color: #5c7597; font-size: 12px;\"> [1-day] </span> </td> </tr> <tr> <td colspan=\"100\" style=\"width: 100%; padding-top: 20px; padding-bottom: 20px;\"><img class=\"w-full\" src=\"https://d14kmd90dujuk0.cloudfront.net/market-charcha/mc_oct232025.png\" style=\"max-width: 100%; border-radius: 10px;\" /></td> </tr> <tr> <td colspan=\"100\" style=\"background: #f9fbfe; padding: 10px; border-radius: 8px; font-weight: 600;\"> <img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/Pie_Chart.png\" style=\"width: 24px; height: 24px;\" /> Chart Insight </td> </tr> <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-right: 10px; font-size: 14px; color: #5c7597; width: 100%;\"> <p><p>&bull; Sharp Reversal & Bearish Candle: The Nifty index suffered a significant midday reversal, plummeting from a high of 26,104 to close lower at 25,891, resulting in a bearish candle formation.</p><p>&bull; Sectoral Divide: The market saw high divergence, with the IT sector closing as the top gainer with over 2% growth, while the Infrastructure sector emerged as the session\'s biggest loser.</p><p>&bull; Key Stock Performance: Infosys, HCLTech, and TCS were the leading gainers in the Nifty 50, contrasting sharply with top losers like IndiGo.</p><p>&bull; Technical (RSI) View: Despite the late sell-off, the index\'s RSI is above 72, suggesting a mild overbought condition but confirming the general underlying strength of the trend.</p><p>&bull; Derivatives Positioning: The options data shows a slightly cautious sentiment with the Put-Call Ratio (PCR) at 0.87, while the Max Pain point at 25,900 indicated a high concentration of options contracts near the closing level.</p></p> </td> </tr> <tr> <td colspan=\"100\" style=\"background: #f9fbfe; padding: 10px; border-radius: 8px; font-weight: 600;\"> <img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/Technical_Output.png\" style=\"width: 24px; height: 24px;\" /> Technical Outlook </td> </tr> <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-right: 10px; font-size: 14px; color: #5c7597; width: 100%;\"> <p>• As far as levels are concerned, the runaway gap around 25800-25750 is expected to cushion the upcoming blips, while the sacrosanct support is placed around 25600-25500. On the flip side, the intermediate high of 26100-26150 is likely to be seen as a potential hurdle, followed by the lifetime high zone of 26277 in the comparable period.</p> </td> </tr> <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-right: 10px;\"> <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"background: rgba(47, 170, 73, 0.1); border-radius: 8px; font-family: sans-serif;\"> <tbody> <tr> <td colspan=\"100\" style=\"text-align: left; padding-top: 20px; font-weight: 600;\"> <img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/Top_Gainers.png\" style=\"margin-left: 25px;\" /> Top Gainers </td> </tr> <tr> <td colspan=\"100\" style=\"padding-top: 20px; text-align: center; padding: 20px;\"> <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"background: #fff; border-radius: 8px;\"> <thead style=\"background-color: #f7f8f8;\"> <tr> <th style=\"padding: 10px 12px; font-size: 14px; font-weight: 700; color: #4f5157; text-align: left; width: 60%;\">Company</th> <th style=\"padding: 10px 12px; font-size: 14px; font-weight: 700; color: #4f5157; text-align: left; width: 20%;\">Price</th> <th style=\"padding: 10px 12px; font-size: 14px; font-weight: 700; color: #4f5157; text-align: left; width: 20%;\">Gains</th> </tr> </thead> <tbody> <tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">INFY</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">1528.50</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #258d52;\">&#9650;&nbsp;3.81%</td></tr><tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">HCLTECH</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">1523.90</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #258d52;\">&#9650;&nbsp;2.55%</td></tr><tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">TCS</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">3073.20</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #258d52;\">&#9650;&nbsp;2.21%</td></tr><tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">SHRIRAMFIN</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">709.65</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #258d52;\">&#9650;&nbsp;2.07%</td></tr><tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">AXISBANK</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">1258.80</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #258d52;\">&#9650;&nbsp;1.74%</td></tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr> <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-right: 10px; padding-top: 20px; padding-bottom: 20px;\"> <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"background: rgba(237, 26, 77, 0.09); border-radius: 8px; font-family: sans-serif;\"> <tbody> <tr> <td colspan=\"100\" style=\"text-align: left; padding-top: 20px; font-weight: 600;\"> <img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/Top_Losers.png\" style=\"margin-left: 25px;\" /> Top Losers </td> </tr> <tr> <td colspan=\"100\" style=\"padding-top: 20px; text-align: center; padding: 20px;\"> <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"background: #fff; border-radius: 8px;\"> <thead style=\"background-color: #f7f8f8;\"> <tr> <th style=\"padding: 10px 12px; font-size: 14px; font-weight: 700; color: #4f5157; text-align: left; width: 60%;\">Company</th> <th style=\"padding: 10px 12px; font-size: 14px; font-weight: 700; color: #4f5157; text-align: left; width: 20%;\">Price</th> <th style=\"padding: 10px 12px; font-size: 14px; font-weight: 700; color: #4f5157; text-align: left; width: 20%;\">Loss</th> </tr> </thead> <tbody> <tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">ETERNAL</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">328.35</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #eb4336;\">&#9660;&nbsp;2.88%</td></tr><tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">INDIGO</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">5789.00</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #eb4336;\">&#9660;&nbsp;2.10%</td></tr><tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">EICHERMOT</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">6884.50</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #eb4336;\">&#9660;&nbsp;1.91%</td></tr><tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">BHARTIARTL</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">2007.90</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #eb4336;\">&#9660;&nbsp;1.74%</td></tr><tr style=\"border-top: 1px solid #f7f8f8;\"><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">ULTRACEMCO</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #4f5157;\">12145.00</td><td style=\"text-align: left; padding: 10px 12px; font-size: 11px; font-weight: 500; color: #eb4336;\">&#9660;&nbsp;1.60%</td></tr> </tbody> </table> </td> </tr> </tbody> </table> </td> </tr>   <tr> <td colspan=\"100\" style=\"padding: 10px; border-radius: 8px; font-weight: 600; padding-bottom: 20px; background: #f9fbfe;\"> <img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/FNO_Logo.png\" alt=\"Hot Or Not\" /> Hot Or Not </td> </tr> <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-top: 10px;\"> <table width=\"100%\" cellspacing=\"8\" cellpadding=\"0\" border=\"1px solid;\" style=\"padding: 8px; border-collapse: collapse; border-color: #5c7597;\"> <thead style=\"background-color: #5a9fd7; color: #ffffff; border-style: hidden;\"> <tr> <th style=\"border-style: hidden; padding-top: 8px; padding-bottom: 8px; font-size: 15px; width: 25%;\"> Stock </th> <th style=\"border-style: hidden; padding-top: 8px; padding-bottom: 8px; font-size: 14px; width: 15%;\"> % Gain / Loss </th> <th style=\"border-style: hidden; padding-top: 8px; padding-bottom: 8px; font-size: 14px; width: 60%;\"> What\'s Happening </th> </tr> </thead> <tbody> <tr border=\"1px solid #5C7597;\"><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 25%;\">BHARAT FORGE</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 15%;\">4.56%</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 60%;\">Bags a ₹2,770 crore contract from the Indian Army to supply carbines, in partnership with PLR Systems.&nbsp;<a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_xp=custom%26pid=overview%26c=overview%26tokenID=422%26symbolName=BHARATFORG%26expirydate=01Jan1980%26strkPrice%26regularLot=1%26instrumenttype=CASH%26priceTick=0.001%26segmentID=1%26SecurityDesc=BHARATFORGELTD%26nseCashToken=422%26bseCashToken=%26curFutToken=%26isin=INE465A01025%26isCashWithFno=false%26instrumentName=%26tradeSymbol=BHARATFORG-EQ/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/QrECqN51OyaeJDAwy0sgfpXDtus=230\" style=\"text-decoration: none;\"><div style=\"color: #2D7969; font-size: 11px; font-family: sans-serif; font-weight: 500; text-decoration: underline; word-wrap: break-word; display: inline;\">Learn More</div> &nbsp;</a></td> </tr><tr border=\"1px solid #5C7597;\"><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 25%;\">RELIANCE</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 15%;\">-1.15%</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 60%;\">Shares trade flat to weak amid concerns that US sanctions on Russian oil could disrupt Rosneft supplies and raise import costs.&nbsp;<a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_xp=custom%26pid=overview%26c=overview%26tokenID=2885%26symbolName=RELIANCE%26expirydate=01Jan1980%26strkPrice%26regularLot=1%26instrumenttype=CASH%26priceTick=0.001%26segmentID=1%26SecurityDesc=RELIANCEINDUSTRIESLTD%26nseCashToken=2885%26bseCashToken=%26curFutToken=%26isin=INE002A01018%26isCashWithFno=false%26instrumentName=%26tradeSymbol=RELIANCE-EQ/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/YXTJEL_RW6Vs4zFMRchp1ygZt2g=230\" style=\"text-decoration: none;\"><div style=\"color: #9B2F26; font-size: 11px; font-family: sans-serif; font-weight: 500; text-decoration: underline; word-wrap: break-word; display: inline;\">Learn More</div> &nbsp;</a></td> </tr><tr border=\"1px solid #5C7597;\"><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 25%;\">KPIL</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 15%;\">-0.54%</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 60%;\">Wins ₹2,332 crore worth of new T&D and building contracts, including overseas orders.&nbsp;<a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_xp=custom%26pid=overview%26c=overview%26tokenID=1814%26symbolName=KPIL%26expirydate=01Jan1980%26strkPrice%26regularLot=1%26instrumenttype=CASH%26priceTick=0.0005%26segmentID=1%26SecurityDesc=KALPATARUPROJECTINTLTD%26nseCashToken=1814%26bseCashToken=%26curFutToken=%26isin=INE220B01022%26isCashWithFno=false%26instrumentName=%26tradeSymbol=KPIL-EQ/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/9z8cn2rbq2YhcurFmgw0meHGdlQ=230\" style=\"text-decoration: none;\"><div style=\"color: #9B2F26; font-size: 11px; font-family: sans-serif; font-weight: 500; text-decoration: underline; word-wrap: break-word; display: inline;\">Learn More</div> &nbsp;</a></td> </tr><tr border=\"1px solid #5C7597;\"><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 25%;\">TORRENTPHARMA</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 15%;\">0.65%</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 60%;\">Gets CCI nod to acquire a controlling stake in J B Chemicals for about $3 billion.&nbsp;<a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_xp=custom%26pid=overview%26c=overview%26tokenID=3518%26symbolName=TORNTPHARM%26expirydate=01Jan1980%26strkPrice%26regularLot=1%26instrumenttype=CASH%26priceTick=0.001%26segmentID=1%26SecurityDesc=TORRENTPHARMACEUTICALSL%26nseCashToken=3518%26bseCashToken=%26curFutToken=%26isin=INE685A01028%26isCashWithFno=false%26instrumentName=%26tradeSymbol=TORNTPHARM-EQ/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/rH7MfcJd82pqUT76jgkiRpL8HyM=230\" style=\"text-decoration: none;\"><div style=\"color: #2D7969; font-size: 11px; font-family: sans-serif; font-weight: 500; text-decoration: underline; word-wrap: break-word; display: inline;\">Learn More</div> &nbsp;</a></td> </tr><tr border=\"1px solid #5C7597;\"><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 25%;\">BEL</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 15%;\">0.23%</td><td style=\"padding: 10px 10px; font-size: 14px; color: #5c7597; text-align: left; width: 60%;\">Secures a ₹633 crore order from Cochin Shipyard for sensors, weapons, and communication systems.&nbsp;<a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_xp=custom%26pid=overview%26c=overview%26tokenID=383%26symbolName=BEL%26expirydate=01Jan1980%26strkPrice%26regularLot=1%26instrumenttype=CASH%26priceTick=0.0005%26segmentID=1%26SecurityDesc=BHARATELECTRONICSLTD%26nseCashToken=383%26bseCashToken=%26curFutToken=%26isin=INE263A01024%26isCashWithFno=false%26instrumentName=%26tradeSymbol=BEL-EQ/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/6ioqkK94w0MSUzLfl1GQ6jhhpdY=230\" style=\"text-decoration: none;\"><div style=\"color: #2D7969; font-size: 11px; font-family: sans-serif; font-weight: 500; text-decoration: underline; word-wrap: break-word; display: inline;\">Learn More</div> &nbsp;</a></td> </tr> </tbody> </table> </td> </tr> <tr> <td style=\"padding-top: 20px;\" ;></td> </tr>    <tr> <td colspan=\"100\" style=\"background: #f9fbfe; padding: 10px; border-radius: 8px; font-weight: 600;\"> <img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/News_And_Update.png\" style=\"width: 24px; height: 24px;\" /> News & Updates </td> </tr> <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-right: 10px; padding-bottom: 10px; font-size: 14px; color: #5c7597; width: 100%;\"><p>&bull; Laurus Labs: Q2 profit jumped 875% YoY to ₹195 cr on strong CDMO and generics growth; revenue up 35% to ₹1,653 cr.</p><a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_xp=custom%26pid=overview%26c=overview%26tokenID=19234%26symbolName=LAURUSLABS%26expirydate=01Jan1980%26strkPrice%26regularLot=1%26instrumenttype=CASH%26priceTick=0.0005%26segmentID=1%26SecurityDesc=LAURUSLABSLIMITED%26nseCashToken=19234%26bseCashToken=%26curFutToken=%26isin=INE947Q01028%26isCashWithFno=false%26instrumentName=%26tradeSymbol=LAURUSLABS-EQ/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/xwWoDf4jeZGLNjxpWVtLsQYTE_E=230\" style=\"text-decoration: none;\"><div style=\"height: 100%; padding-left: 1px; padding-right: 4px; padding-top: 1.60px; padding-bottom: 1.60px; background: #EAF6ED; border-radius: 13px; border: 1px #D1EDDF solid; justify-content: center; align-items: center; gap: 6.41px; display: inline-flex\"> <div style=\"justify-content: center; align-items: flex-end; gap: 6.43px; display: flex\"> <div style=\"color: #035A44; font-size: 12px; font-family: sans-serif; font-weight: 500; word-wrap: break-word; padding-top: 3px;\"> &nbsp;&nbsp;LAURUSLABS-EQ</div> <div style=\"justify-content: flex-start; align-items: center; gap: 6px; display: flex\"> <div style=\"color: #2D7969; font-size: 11px; font-family: sans-serif; font-weight: 500; word-wrap: break-word; padding-top: 3px;\">&nbsp;&nbsp;+2.07%&nbsp;&nbsp;</div> </div> </div> <div style=\"color: #2D7969; font-size: 11px; font-family: sans-serif; font-weight: 500; text-decoration: underline; word-wrap: break-word; padding-top: 3px;\">Learn More</div>  &nbsp;</div> &nbsp;</a><p>&bull; Epack Prefab Tech: Shares soared 20% after Q2 profit doubled to ₹29.5 cr.</p><a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_xp=custom%26pid=overview%26c=overview%26tokenID=22463%26symbolName=EPACK%26expirydate=01Jan1980%26strkPrice%26regularLot=1%26instrumenttype=CASH%26priceTick=0.0005%26segmentID=1%26SecurityDesc=EPACKDURABLELIMITED%26nseCashToken=22463%26bseCashToken=%26curFutToken=%26isin=INE0G5901015%26isCashWithFno=false%26instrumentName=%26tradeSymbol=EPACK-EQ/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/xYS_onPHE3uY5OrVzCvGtWgOMqU=230\" style=\"text-decoration: none;\"><div style=\"height: 100%; padding-left: 1px; padding-right: 4px; padding-top: 1.60px; padding-bottom: 1.60px; background: #EAF6ED; border-radius: 13px; border: 1px #D1EDDF solid; justify-content: center; align-items: center; gap: 6.41px; display: inline-flex\"> <div style=\"justify-content: center; align-items: flex-end; gap: 6.43px; display: flex\"> <div style=\"color: #035A44; font-size: 12px; font-family: sans-serif; font-weight: 500; word-wrap: break-word; padding-top: 3px;\"> &nbsp;&nbsp;EPACK-EQ</div> <div style=\"justify-content: flex-start; align-items: center; gap: 6px; display: flex\"> <div style=\"color: #2D7969; font-size: 11px; font-family: sans-serif; font-weight: 500; word-wrap: break-word; padding-top: 3px;\">&nbsp;&nbsp;+13.01%&nbsp;&nbsp;</div> </div> </div> <div style=\"color: #2D7969; font-size: 11px; font-family: sans-serif; font-weight: 500; text-decoration: underline; word-wrap: break-word; padding-top: 3px;\">Learn More</div>  &nbsp;</div> &nbsp;</a><p>&bull; HUL: Stock gained 3% as Q2 profit rose 4% YoY to ₹2,694 cr, aided by a one-off tax gain; revenue flat amid GST transition.</p><a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_xp=custom%26pid=overview%26c=overview%26tokenID=1394%26symbolName=HINDUNILVR%26expirydate=01Jan1980%26strkPrice%26regularLot=1%26instrumenttype=CASH%26priceTick=0.001%26segmentID=1%26SecurityDesc=HINDUSTANUNILEVERLTD.%26nseCashToken=1394%26bseCashToken=%26curFutToken=%26isin=INE030A01027%26isCashWithFno=false%26instrumentName=%26tradeSymbol=HINDUNILVR-EQ/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/0lxQqZs6w3EWm5acVs8Fz733yQ4=230\" style=\"text-decoration: none;\"><div style=\"height: 100%; padding-left: 1px; padding-right: 4px; padding-top: 1.60px; padding-bottom: 1.60px; background: #EAF6ED; border-radius: 13px; border: 1px #D1EDDF solid; justify-content: center; align-items: center; gap: 6.41px; display: inline-flex\"> <div style=\"justify-content: center; align-items: flex-end; gap: 6.43px; display: flex\"> <div style=\"color: #035A44; font-size: 12px; font-family: sans-serif; font-weight: 500; word-wrap: break-word; padding-top: 3px;\"> &nbsp;&nbsp;HINDUNILVR-EQ</div> <div style=\"justify-content: flex-start; align-items: center; gap: 6px; display: flex\"> <div style=\"color: #2D7969; font-size: 11px; font-family: sans-serif; font-weight: 500; word-wrap: break-word; padding-top: 3px;\">&nbsp;&nbsp;+0.36%&nbsp;&nbsp;</div> </div> </div> <div style=\"color: #2D7969; font-size: 11px; font-family: sans-serif; font-weight: 500; text-decoration: underline; word-wrap: break-word; padding-top: 3px;\">Learn More</div>  &nbsp;</div> &nbsp;</a><p>&bull; Hindalco: $125 mn AluChem deal delayed due to US govt shutdown, awaiting approval.</p><p>&bull; Caplin Point: Subsidiary gets USFDA nod for Nicardipine Hydrochloride injection, tapping a $68 mn U.S. market.</p><p>&bull; Bondada Engineering: Bags a ₹1,050 cr order from Adani Group to build a 650 MW solar project in Gujarat.</p><p>&bull; Vikram Solar: Wins major order from Sunsure Energy to supply 148.9 MW of high-efficiency solar modules, strengthening its role in India’s renewable energy expansion.</p><p>&bull; Eternal (Zomato parent): Receives ₹128.4 crore GST demand & penalty from UP authorities for Apr 2023–Mar 2024; company plans to appeal, citing a strong case.</p><p>&bull; Oil prices surged over 4% as the U.S. imposed sanctions on Russian oil majors Rosneft and Lukoil, tightening global supply and pushing Brent above $65 a barrel.</p></td> </tr> <tr> <td align=\"center\" ; style=\"padding: 10px; text-align: center; background: #f9fbfe;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_web_dp=https:%2F%2Fwww.angelone.in%2Ftwa-news-app%2F%26af_xp=custom%26pid=twaRedirection%26c=twaRedirection/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/GH4hlldey_Qy-JRx0WvWDEGy-eE=230\" style=\"color: #0060ff; font-size: 14px; font-family: sans-serif; font-weight: 600; line-height: 15.54px; text-decoration: none;\" > Explore Full Update > </a> </td> </tr> <tr> <td style=\"padding-top: 20px;\"></td> </tr>  <tr> <td colspan=\"100\" style=\"padding-left: 10px; padding-right: 10px; padding-top: 20px; padding-bottom: 20px;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%3Faf_xp=custom%26pid=topCommodity%26c=topCommodity%26TradeSymbol=Overview%26widget=CommodityWidget/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/VU3-kGHCNQM1G1GZ9Ke_tiV7mx4=230\" style=\"color: inherit; text-decoration: none;\"> <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"background: #f9fbfe; border-radius: 8px; font-family: sans-serif;\"> <tbody> <tr> <td colspan=\"100\" style=\"text-align: left; padding-top: 20px; padding-left: 20px; font-weight: 600;\"> <img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/Commodity_Market_Watch.png\" style=\"width: 24px; height: 24px;\" /> Commodity Market Watch <span style=\"font-weight: 400; font-size: 12px; color: #5c7597;\"> 1 Day Change</span> </td> </tr> <tr> <td colspan=\"100\" style=\"padding-top: 20px; text-align: center; padding: 20px;\"> <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"background: white; border-radius: 11px; padding: 14px;\"> <tr> <td style=\"padding-bottom: 11px; border-bottom: 1px dotted #d2d2d2;\"> <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse;\"> <tr> <td style=\"text-align: left; color: #1b2559; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 600; width: 65%;\">Crude Oil</td> <td style=\"text-align: left; color: #5c7597; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500; margin-right: 12px; width: 15%;\">5442.00</td> <td style=\"text-align: right; width: 20%;\"> <span style=\"color: #258D52; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500;\">&#9650;</span> <span style=\"color: #258D52; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 600; margin-left: 5px;\">5.69%</span> </td> </tr> </table> </td> </tr> <tr> <td style=\"padding-bottom: 11px; border-bottom: 1px dotted #d2d2d2;\"> <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse;\"> <tr> <td style=\"text-align: left; color: #1b2559; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 600; width: 65%;\">Natural Gas</td> <td style=\"text-align: left; color: #5c7597; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500; margin-right: 11px; width: 15%;\">304.70</td> <td style=\"text-align: right; width: 20%;\"> <span style=\"color: #EB4336;; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500;\">&#9660;</span> <span style=\"color: #EB4336;; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 600; margin-left: 5px;\">0.26%</span> </td> </tr> </table> </td> </tr> <tr> <td style=\"padding-bottom: 11px; border-bottom: 1px dotted #d2d2d2;\"> <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse;\"> <tr> <td style=\"text-align: left; color: #1b2559; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 600; width: 65%;\">Gold</td> <td style=\"text-align: left; color: #5c7597; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500; margin-right: 12px; width: 15%;\">122663.00</td> <td style=\"text-align: right; width: 20%;\"> <span style=\"color: #258D52; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500;\">&#9650;</span> <span style=\"color: #258D52; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 600; margin-left: 5px;\">1.30%</span> </td> </tr> </table> </td> </tr> <tr> <td> <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse;\"> <tr> <td style=\"text-align: left; color: #1b2559; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 600; width: 65%;\">Silver</td> <td style=\"text-align: left; color: #5c7597; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500; margin-right: 11px; width: 15%;\">150192.00</td> <td style=\"text-align: right; width: 20%;\"> <span style=\"color: #258D52; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 500;\">&#9650;</span> <span style=\"color: #258D52; font-size: 11px; font-family: Barlow, sans-serif; font-weight: 600; margin-left: 5px;\">1.77%</span> </td> </tr> </table> </td> </tr> </table> </td> </tr> <tr> <td colspan=\"100\" style=\"font-size: 12px; padding: 10px;\"> Note: The commodity market is open until 11:30 PM. The prices shown above reflect the upcoming futures expiry of the respective commodities as of 6 PM today </td> </tr> </tbody> </table> </a> </td> </tr> <tr><td style=\"padding: 0 15px;\"><table style=\"width: 100%; background: #f9fbfe; border-radius: 8px; margin-bottom: 20px;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td style=\"font-family: Arial, sans-serif; padding: 15px 20px; font-weight: 600; color: #1b2559; font-size: 16px;\"><img src=\"https://mf.angelmf.com/Content/MarketSnapshot/images/Quiz_Icon.png\" alt=\"Quiz\" style=\"height: 20px; vertical-align: middle; margin-right: 8px;\"/>Quiz of the Day</td></tr></table></td></tr><tr><td style=\"padding: 0 15px;\"><table class=\"question-table\" style=\"width: 100%; margin-bottom: 25px; border-radius: 8px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td style=\"background: #f9fbfe; font-family: Arial, sans-serif; padding: 18px 20px; font-weight: 600; color: #1b2559; font-size: 15px; border-bottom: 1px solid #e2e8f0;\">1. What does “index rebalancing” mean?</td></tr><tr> <td style=\"padding: 15px;\">  <table style=\"width: 100%; margin-bottom: 10px;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"> <tr> <td class=\"option-cell\" style=\"width: 48%; padding-right: 2%; vertical-align: top;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fmf.angelmf.com%2FCustomerComplaints%2FSaveClientResponseForMCQuiz%3Fid=QUFBTzQ5NTQ2Nw==%26quizId=388%26response=A/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/XSxLrxZuXnKVgeBb85Vga5BydDc=230\" target=\"_blank\" class=\"option-button button-hover\" style=\"display: block; width: 100%; padding: 12px 16px; background-color: #ffffff; border: 2px solid #cbd5e1; border-radius: 8px; color: #475569; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px; text-align: center; font-weight: 500; min-height: 20px; box-sizing: border-box;\"> Stock Change </a> </td> <td class=\"option-cell\" style=\"width: 48%; padding-left: 2%; vertical-align: top;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fmf.angelmf.com%2FCustomerComplaints%2FSaveClientResponseForMCQuiz%3Fid=QUFBTzQ5NTQ2Nw==%26quizId=388%26response=B/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/fvA2iWrnbEhNy4etjht4i7MlD3o=230\" target=\"_blank\" class=\"option-button button-hover\" style=\"display: block; width: 100%; padding: 12px 16px; background-color: #ffffff; border: 2px solid #cbd5e1; border-radius: 8px; color: #475569; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px; text-align: center; font-weight: 500; min-height: 20px; box-sizing: border-box;\"> Dividend Day </a> </td> </tr> </table>  <table style=\"width: 100%;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"> <tr> <td class=\"option-cell\" style=\"width: 48%; padding-right: 2%; vertical-align: top;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fmf.angelmf.com%2FCustomerComplaints%2FSaveClientResponseForMCQuiz%3Fid=QUFBTzQ5NTQ2Nw==%26quizId=388%26response=C/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/s4Az9_qKTtYNtDCJfItTl34Z36U=230\" target=\"_blank\" class=\"option-button button-hover\" style=\"display: block; width: 100%; padding: 12px 16px; background-color: #ffffff; border: 2px solid #cbd5e1; border-radius: 8px; color: #475569; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px; text-align: center; font-weight: 500; min-height: 20px; box-sizing: border-box;\"> IPO Listing </a> </td> <td class=\"option-cell\" style=\"width: 48%; padding-left: 2%; vertical-align: top;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fmf.angelmf.com%2FCustomerComplaints%2FSaveClientResponseForMCQuiz%3Fid=QUFBTzQ5NTQ2Nw==%26quizId=388%26response=D/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/A1kN-iIe20x8j5IFjUrfFvALiFY=230\" target=\"_blank\" class=\"option-button button-hover\" style=\"display: block; width: 100%; padding: 12px 16px; background-color: #ffffff; border: 2px solid #cbd5e1; border-radius: 8px; color: #475569; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px; text-align: center; font-weight: 500; min-height: 20px; box-sizing: border-box;\"> Market Holiday </a> </td> </tr> </table> </td> </tr> </table><table class=\"question-table\" style=\"width: 100%; margin-bottom: 25px; border-radius: 8px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td style=\"background: #f9fbfe; font-family: Arial, sans-serif; padding: 18px 20px; font-weight: 600; color: #1b2559; font-size: 15px; border-bottom: 1px solid #e2e8f0;\">2. Global oil prices surged over 4% after the U.S. imposed sanctions on which Russian oil companies?</td></tr><tr> <td style=\"padding: 15px;\">  <table style=\"width: 100%; margin-bottom: 10px;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"> <tr> <td class=\"option-cell\" style=\"width: 48%; padding-right: 2%; vertical-align: top;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fmf.angelmf.com%2FCustomerComplaints%2FSaveClientResponseForMCQuiz%3Fid=QUFBTzQ5NTQ2Nw==%26quizId=389%26response=A/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/oJFoQAyOIc_dNY8GyMYIoQKu7Ms=230\" target=\"_blank\" class=\"option-button button-hover\" style=\"display: block; width: 100%; padding: 12px 16px; background-color: #ffffff; border: 2px solid #cbd5e1; border-radius: 8px; color: #475569; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px; text-align: center; font-weight: 500; min-height: 20px; box-sizing: border-box;\"> Gazprom & Rosneft </a> </td> <td class=\"option-cell\" style=\"width: 48%; padding-left: 2%; vertical-align: top;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fmf.angelmf.com%2FCustomerComplaints%2FSaveClientResponseForMCQuiz%3Fid=QUFBTzQ5NTQ2Nw==%26quizId=389%26response=B/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/cossloYULJ_bn-IOjMZrMbhRhys=230\" target=\"_blank\" class=\"option-button button-hover\" style=\"display: block; width: 100%; padding: 12px 16px; background-color: #ffffff; border: 2px solid #cbd5e1; border-radius: 8px; color: #475569; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px; text-align: center; font-weight: 500; min-height: 20px; box-sizing: border-box;\"> Rosneft & Lukoil </a> </td> </tr> </table>  <table style=\"width: 100%;\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"> <tr> <td class=\"option-cell\" style=\"width: 48%; padding-right: 2%; vertical-align: top;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fmf.angelmf.com%2FCustomerComplaints%2FSaveClientResponseForMCQuiz%3Fid=QUFBTzQ5NTQ2Nw==%26quizId=389%26response=C/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/iSSQzkZKUoLW1Ny-ioLB-vzyM-8=230\" target=\"_blank\" class=\"option-button button-hover\" style=\"display: block; width: 100%; padding: 12px 16px; background-color: #ffffff; border: 2px solid #cbd5e1; border-radius: 8px; color: #475569; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px; text-align: center; font-weight: 500; min-height: 20px; box-sizing: border-box;\"> Transneft & Surgutneftegas </a> </td> <td class=\"option-cell\" style=\"width: 48%; padding-left: 2%; vertical-align: top;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fmf.angelmf.com%2FCustomerComplaints%2FSaveClientResponseForMCQuiz%3Fid=QUFBTzQ5NTQ2Nw==%26quizId=389%26response=D/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/7z00844XlNDNMr1pBsgqAPf-C2A=230\" target=\"_blank\" class=\"option-button button-hover\" style=\"display: block; width: 100%; padding: 12px 16px; background-color: #ffffff; border: 2px solid #cbd5e1; border-radius: 8px; color: #475569; text-decoration: none; font-family: Arial, sans-serif; font-size: 14px; text-align: center; font-weight: 500; min-height: 20px; box-sizing: border-box;\"> Bashneft & Tatneft </a> </td> </tr> </table> </td> </tr> </table></td> </tr> <tr> <td style=\"text-align: center; color: #5C7597; font-size: 16px; font-weight: 700; word-wrap: break-word; font-family: \'Barlow\', sans-serif;\"> We would love to hear your feedback! </td> </tr> <tr> <td style=\"padding-top: 20px;\"></td> </tr> <tr> <td style=\"width: 100%; text-align: center; font-family: \'Barlow\', sans-serif;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fmf.angelmf.com%2FCustomerComplaints%2FGetPRPMailerClientsSuggestion%3Fid=QUFBTzQ5NTQ2Nw==%26Tag=23102025_MCNormalMailer/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/yLOrPXY8zkXOR8yaygTxYS4HurA=230\" style=\"color: #2E51FF; font-size: 14px; font-weight: 500; text-decoration: underline; line-height: 21.60px; word-wrap: break-word\"> Click here </a> <span style=\"color: #40505F; font-size: 14px; font-weight: 500; line-height: 21.60px; word-wrap: break-word;font-family: \'Barlow\', sans-serif;\"> to share your suggestions.</span> </td> </tr> <tr> <td style=\"padding-top: 20px;\"></td> </tr> <tr> <td colspan=\"100\" style=\"text-align: center;\"> <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fangel-one.onelink.me%2FJAAb%2FadvisoryForYou/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/ykO3OjyLKa31xEodrwM1IVgrmCs=230\" style=\"display: inline-block; text-decoration: none; background: #2e51ff; color: white; padding-left: 24px; padding-right: 24px; padding-top: 12px; padding-bottom: 12px; border-radius: 8px;\" > AngelOne Research </a> </td> </tr> <tr> <td colspan=\"100\" style=\"text-align: center; font-size: 11px; padding-top: 20px;\"> <span>Disclaimer</span> - Investments in securities market are subject to market risks, read all related documents carefully before investing. <a href=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/L0/https:%2F%2Fwww.angelone.in%2Fresearch-disclaimer/1/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/5qk5NcP0zdNJPUuX3ks4sZ01KnY=230\"> Read More </a> <p>Quizzes are for engagement purpose only.</p> <p> You\'re getting this email because you\'re a Angel One user and we believe you deserve great finance content and the latest updates from us.</a> </p> <p>News summaries are generated by an LLM. Please report discrepancies to help us improve.</p> </td> </tr> </tbody> </table> <img alt=\"\" src=\"https://vk9ebk5e.r.ap-south-1.awstrack.me/I0/0109019a1108eade-3d9d8644-1587-4726-a788-4bdbb6bb2c30-000000/YBKGL4qmFVuR53aGjw60skaw3zM=230\" style=\"display: none; width: 1px; height: 1px;\">\r\n</body> </html>\r\nhi', 'normal', '2025-10-25 10:07:14'),
(2, 1, 'uky171991@gmail.com', '', '', 'Lab Report Ready - Hospital', 'Dear Patient,\r\n\r\nYour lab report is ready for collection.\r\n\r\nReport Details:\r\nTest Date: [DATE]\r\nReport ID: [REPORT_ID]\r\n\r\nYou can collect your report from the reception or download it from our patient portal.\r\n\r\nBest regards,\r\nLab Department', 'normal', '2025-10-25 10:08:03'),
(3, 1, 'uky171991@gmail.com', '', '', 'Appointment Reminder - Hospital', 'Dear Patient,\r\n\r\nThis is a reminder for your upcoming appointment.\r\n\r\nAppointment Details:\r\nDate: [DATE]\r\nTime: [TIME]\r\nDoctor: [DOCTOR]\r\n\r\nPlease arrive 15 minutes early.\r\n\r\nBest regards,\r\nHospital Team', 'high', '2025-10-25 10:10:31');

-- --------------------------------------------------------

--
-- Table structure for table `system_config`
--

CREATE TABLE `system_config` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_config`
--

INSERT INTO `system_config` (`id`, `config_key`, `config_value`, `created_at`, `updated_at`) VALUES
(1, 'gmail_password', 'puvo pavn vtij xcnl', '2025-11-16 07:38:47', '2025-11-17 01:32:04');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
  `status` enum('Pending','In Progress','Completed','On Hold') NOT NULL DEFAULT 'Pending',
  `due_date` date DEFAULT NULL,
  `website_urls` text DEFAULT NULL,
  `screenshots` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `client_id`, `title`, `description`, `priority`, `status`, `due_date`, `website_urls`, `screenshots`, `notes`, `created_at`, `updated_at`) VALUES
(2, 3, 'Website not working', 'Menu issue ', 'Medium', 'Completed', '2025-11-20', 'https://allcurepharmacys.com/', '[\"uploads\\/screenshots\\/1763382631_0_Screenshot 2025-11-17 180004.png\"]', '', '2025-11-17 12:21:35', '2025-12-04 07:28:29'),
(4, 8, 'Resume builders', 'http://resume.devloper.space/', 'Medium', 'In Progress', '2025-11-29', 'http://resume.devloper.space/', '[]', '', '2025-11-18 05:53:38', '2025-12-04 10:05:31'),
(5, 10, 'Booking Website', 'Studio booking', 'Medium', 'Completed', '2025-11-30', 'https://bombay.devloper.space/\r\nhttps://bombaybeatsstudios.com/', '[]', '', '2025-11-18 06:11:15', '2025-11-21 01:17:06'),
(6, 10, 'Booking  app', 'Booking app', 'Medium', 'On Hold', '2025-11-30', '', '[\"uploads\\/screenshots\\/1764833546_0_Untitled.png\"]', '', '2025-11-18 06:11:55', '2025-12-04 07:32:26'),
(7, 11, 'Issue  fixed', 'Marksheet page  issue, Progress card page  issue, result page  issue', 'Medium', 'Completed', '2025-11-15', 'https://iqrapublicschool.com/welcome.html', '[]', '', '2025-11-18 07:06:53', '2025-11-18 07:10:12'),
(8, 3, 'Website menu overwrite issue ', 'https://allcurepharmacys.com/', 'Medium', 'Completed', '2025-12-07', 'https://allcurepharmacys.com/', '[\"uploads\\/screenshots\\/1764833471_0_Untitled.png\",\"uploads\\/screenshots\\/1764833502_0_Untitled.png\"]', '', '2025-12-04 07:31:11', '2025-12-06 19:54:27'),
(9, 12, 'OPD App', 'OPD  for  hospitals', 'Medium', 'Pending', '2026-01-10', '', '[]', '', '2025-12-06 19:56:35', NULL),
(10, 12, 'Pathology Software', 'Pathology for computer software', 'Medium', 'Completed', '2025-12-07', '', '[]', '', '2025-12-06 19:57:18', NULL),
(11, 12, 'School website', 'School website ', 'Medium', 'Pending', '2025-12-31', '', '[]', '', '2025-12-06 19:57:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `main_category_id` int(11) NOT NULL,
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
  `min_child` decimal(10,2) DEFAULT NULL,
  `max_child` decimal(10,2) DEFAULT NULL,
  `sub_heading` tinyint(1) NOT NULL DEFAULT 0,
  `test_code` varchar(100) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `print_new_page` tinyint(1) NOT NULL DEFAULT 0,
  `shortcut` varchar(50) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `name`, `category_id`, `main_category_id`, `price`, `unit`, `specimen`, `default_result`, `reference_range`, `min`, `max`, `description`, `min_male`, `max_male`, `min_female`, `max_female`, `min_child`, `max_child`, `sub_heading`, `test_code`, `method`, `print_new_page`, `shortcut`, `added_by`, `created_at`, `updated_at`) VALUES
(4, 'Hemoglobin', 9, 30, 0.00, 'g/dL', '', '', '', 13.00, 17.00, '', 13.00, 17.00, 12.00, 15.50, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-05 15:12:52', '2025-12-05 15:12:52'),
(5, 'RBC', 9, 30, 0.00, 'million/µL', '', '', '', 4.30, 5.90, '', 4.30, 5.90, 3.50, 5.50, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-05 16:34:40', '2025-12-06 07:09:02'),
(6, 'Hematocrit', 9, 30, 0.00, '%', '', '', '', 0.00, 0.00, '', 41.00, 53.00, 36.00, 46.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-05 16:36:49', '2025-12-19 06:16:48'),
(7, 'WBC', 9, 30, 0.00, '×10³/µL', '', '', '', 4.00, 11.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-05 16:37:38', '2025-12-05 16:37:38'),
(8, 'Neutrophils', 10, 30, 0.00, '% of WBC', '', '', '', 40.00, 70.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-05 16:43:29', '2025-12-05 16:43:29'),
(9, 'Lymphocytes', 10, 30, 0.00, '% of WBC', '', '', '', 20.00, 40.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-05 16:45:13', '2025-12-05 16:45:13'),
(10, 'Monocytes', 10, 30, 0.00, '% of WBC', '', '', '', 2.00, 8.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-05 16:46:37', '2025-12-05 16:46:37'),
(11, 'Eosinophils', 10, 30, 0.00, '% of WBC', '', '', '', 1.00, 6.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-05 16:47:50', '2025-12-05 16:47:50'),
(12, 'Platelet Count', 11, 30, 0.00, '×10³/µL', '', '', '', 150.00, 400.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 05:47:27', '2025-12-06 05:47:27'),
(13, 'MCV', 12, 30, 0.00, 'fL', '', '', '', 80.00, 100.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 05:48:40', '2025-12-06 05:48:40'),
(14, 'MCH', 12, 30, 0.00, 'pg', '', '', '', 27.00, 33.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 05:49:13', '2025-12-06 05:49:13'),
(15, 'MCHC', 12, 30, 0.00, 'g/dL', '', '', '', 32.00, 36.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 05:50:06', '2025-12-06 05:50:06'),
(16, 'PT (INR)', 13, 30, 0.00, '-', '', '', '', 0.80, 1.20, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:02:06', '2025-12-06 06:02:06'),
(17, 'aPTT', 13, 30, 0.00, 'Second', '', '', '', 25.00, 35.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:02:49', '2025-12-06 06:02:49'),
(18, 'Blood Urea Nitrogen (BUN)', 14, 31, 0.00, 'mg/dL', '', '', '', 8.00, 23.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:06:20', '2025-12-06 06:06:20'),
(19, 'Serum Creatinine', 14, 31, 0.00, 'mg/dL', '', '', '', 0.70, 1.30, '', 0.70, 1.30, 0.60, 1.10, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:07:42', '2025-12-06 06:07:42'),
(20, 'Uric Acid', 14, 31, 0.00, 'mg/dL', '', '', '', 3.50, 7.20, '', 3.50, 7.20, 2.60, 6.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:09:19', '2025-12-06 06:09:19'),
(21, 'Total Bilirubin', 15, 31, 0.00, 'mg/dL', '', '', '', 0.30, 1.20, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:11:21', '2025-12-06 06:11:21'),
(22, 'Direct Bilirubin', 15, 31, 0.00, 'mg/dL', '', '', '', 0.10, 0.30, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:12:07', '2025-12-06 06:12:07'),
(23, 'ALT (SGPT)', 15, 31, 0.00, 'U/L', '', '', '', 10.00, 40.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:12:51', '2025-12-06 06:12:51'),
(24, 'AST (SGOT)', 15, 31, 0.00, 'U/L', '', '', '', 10.00, 40.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:13:29', '2025-12-06 06:13:29'),
(25, 'Alkaline Phosphatase', 15, 31, 0.00, 'U/L', '', '', '', 40.00, 120.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:14:07', '2025-12-06 06:14:07'),
(26, 'Albumin', 15, 31, 0.00, 'g/dL', '', '', '', 3.50, 5.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:14:55', '2025-12-06 06:14:55'),
(27, 'Total Cholesterol', 16, 31, 0.00, 'mg/dL', '', '', '', 0.00, 200.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:16:03', '2025-12-06 06:16:03'),
(28, 'LDL Cholesterol', 16, 31, 0.00, 'mg/dL', '', '', '', 0.00, 100.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:16:55', '2025-12-06 06:16:55'),
(29, 'HDL', 16, 31, 0.00, 'mg/dL', '', '', 'Male 40, Female 50', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:19:03', '2025-12-06 06:56:19'),
(30, 'Triglycerides', 16, 31, 0.00, 'mg/dL', '', '', '', 0.00, 150.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:22:33', '2025-12-06 06:22:33'),
(31, 'Sodium', 17, 31, 0.00, 'mEq/L', '', '', '', 135.00, 145.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:24:02', '2025-12-06 06:24:02'),
(32, 'Potassium', 17, 31, 0.00, 'mEq/L', '', '', '', 3.50, 5.10, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:24:40', '2025-12-06 06:24:40'),
(33, 'Chloride', 17, 31, 0.00, 'mEq/L', '', '', '', 98.00, 106.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:25:21', '2025-12-06 06:25:21'),
(34, 'TSH', 18, 32, 0.00, 'mIU/L', '', '', '', 0.40, 4.50, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:27:44', '2025-12-06 06:27:44'),
(35, 'Free T4', 18, 32, 0.00, 'ng/dL', '', '', '', 0.80, 1.80, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:28:21', '2025-12-06 06:28:21'),
(36, 'Free T3', 18, 32, 400.00, 'pg/mL', '', '', '', 2.30, 4.20, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:29:05', '2025-12-31 13:59:54'),
(37, 'Vitamin B12', 19, 33, 0.00, 'pg/mL', '', '', '', 200.00, 900.00, '', 200.00, 900.00, 200.00, 900.00, 200.00, 900.00, 0, '', '', 0, '', 1, '2025-12-06 06:30:20', '2025-12-19 06:14:32'),
(38, '25(OH) Vitamin D', 19, 33, 100.00, 'ng/mL', '', '', '', 30.00, 100.00, '', 30.00, 100.00, 30.00, 100.00, 30.00, 100.00, 0, '', '', 0, '', 1, '2025-12-06 06:30:51', '2025-12-19 06:12:55'),
(39, 'ESR', 20, 34, 0.00, 'mm/hr', '', '', '', 0.00, 15.00, '', 0.00, 15.00, 0.00, 20.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:34:40', '2025-12-06 06:35:24'),
(40, 'Rheumatoid Factor', 21, 34, 0.00, 'IU/mL', '', '', '', 0.00, 20.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:36:17', '2025-12-06 06:36:17'),
(41, 'HBsAg', 22, 35, 0.00, '', '', '', 'Negative', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:37:46', '2025-12-06 06:37:46'),
(42, 'HIV 1 & 2 Antibodies', 22, 35, 0.00, '', '', '', 'Negative', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:38:15', '2025-12-06 06:38:15'),
(43, 'HCV Antibody', 22, 35, 0.00, '', '', '', 'Negative', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-06 06:38:43', '2025-12-06 06:38:43'),
(44, 'Widal', NULL, 37, 0.00, '', '', '', '', 0.00, 0.00, '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, '', '', 0, '', 1, '2025-12-16 21:08:14', '2025-12-16 21:28:04');

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
  `added_by` int(11) NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `is_active`, `user_type`, `created_at`, `last_login`, `expire_date`, `api_token`, `added_by`, `updated_at`) VALUES
(1, 'umakant', '$2y$12$8RovPoAOxY30weFvoSKJD.aabD27dV8cHbqON2XTQ04x1fs/Tw1da', 'Umakant Yadav', 'umakant171991@gmail.com', 'master', 1, 'Pathology', '2025-09-26 10:12:24', '2026-01-01 05:10:37', '2025-12-03 21:58:00', '9ba25cbde33572ba77d12075e1eb96178260ea6c3effb6847d0f51b8e943c14d', 1, '2025-09-26 04:42:48'),
(2, 'uma', '$2y$12$auavnwTI5hbfkyqCoavijO/i3diGYgDZqt58EzkY6ZkB6M9jTmD9e', 'Uma Yadav', 'umakant171992@gmail.com', 'user', 1, 'Pathology', '2025-09-26 10:13:58', '2026-01-01 05:44:11', '2025-12-30 23:59:00', '6dad141b199e8c2ae3021462459c23135244d408778939a4c33c4a969726fde7', 1, '2025-10-18 05:36:53'),
(5, 'ghayas', '$2y$12$xGaV5GD7MjnYKyil2ZWdCuFfhFFlNz15MhoqcCfpIHRH4GNPgg8n2', 'ghayas ahmad', 'ghayasahmad522@gmail.com', 'user', 1, 'Hospital', '2025-12-02 10:34:10', '2025-12-17 11:54:17', '2026-01-02 10:34:10', '24aa51be2a90dbfc0a1e1fa02fcb8428e3ba6de8c5e4487b272ae06b9afe8f91', 1, '2025-12-02 05:04:10'),
(7, 'ghayas_m', '$2y$12$HxUjV9xpSg7soP1/M6E7j.p0mQsFCkAJSKM4pmOUbIa7CYXEQjzBq', 'ghayas ahmad', 'gha@gmail.com', 'admin', 1, 'Pathology', '2025-12-03 21:53:18', '2025-12-20 19:27:15', '2026-01-03 21:53:00', '68b3b1e0379106601e1454febff7085d76f36593f0288f6fc664d64ba9653e10', 1, '2025-12-03 16:23:18'),
(8, 'alok', '$2y$12$PbEu3wQ9yROIEZWJ6OBYbu3NvWBEyuBcuY7goSCIBSVhPqzAyekWC', '', '', 'user', 1, 'Pathology', '2025-12-07 01:39:22', NULL, '2026-03-31 01:39:00', '227f391382161b24b1ebd503fc99a9e0136e55307116db07acf30f5c652e4dab', 1, '2025-12-06 20:09:22'),
(9, 'sidhant', '$2y$12$nGbCzlSmY6bmIpVkZYv2l.ULcFKfmS2HhNJQv8j4JHm0Na7Ec5NSq', 'Sidhant', 'Sidhant@gmail.com', 'user', 1, 'Pathology', '2025-12-16 18:09:15', '2025-12-20 19:45:03', NULL, '4407d27dca7ef8cfad110648189859ad647be16fd3b08ba6d1701d8a0d36cff9', 7, '2025-12-16 12:39:15'),
(10, 'demo', '$2y$12$LlrwEAWuSGyp2aiMbMCadu6g69S2cnGjxQJT0UC9PLOrrRijtAO.6', 'Demo auser', 'admin@example.com', 'user', 1, 'Pathology', '2025-12-19 05:29:25', '2025-12-31 10:59:43', '2028-02-05 05:28:00', '58d731cd1fd88f5cbd3f13723b1ac943d8d7645ee2dc48be34d5f02150f8fdc6', 1, '2025-12-18 23:59:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `setting_key`, `setting_value`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, 'gmail_password', 'am5pbWl1aXluam5vcHZrdA==', '{\"type\":\"app\",\"created\":\"2025-10-25 09:22:07\"}', '2025-10-25 09:22:07', '2025-10-25 09:22:07'),
(2, 1, 'email_default_priority', 'normal', NULL, '2025-11-17 01:04:10', '2025-11-22 02:47:55'),
(3, 1, 'email_emails_per_page', '100', NULL, '2025-11-17 01:04:10', '2025-11-22 02:47:55'),
(4, 1, 'email_enable_imap', '1', NULL, '2025-11-17 01:04:10', '2025-11-22 02:47:55'),
(5, 1, 'email_enable_smtp', '1', NULL, '2025-11-17 01:04:10', '2025-11-22 02:47:55'),
(6, 1, 'email_auto_refresh', '1', NULL, '2025-11-17 01:04:10', '2025-11-22 02:47:55'),
(7, 1, 'email_mark_as_read', '1', NULL, '2025-11-17 01:04:10', '2025-11-22 02:47:55'),
(8, 1, 'email_show_notifications', '1', NULL, '2025-11-17 01:04:10', '2025-11-22 02:47:55'),
(9, 1, 'email_save_sent_copy', '1', NULL, '2025-11-17 01:04:10', '2025-11-22 02:47:55');

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
(18, '1765861091_PathoLab_Pro_Setup_v1.0.0.exe', 'PathoLab_Pro_Setup_v1.0.0.exe', 'uploads/1765861091_PathoLab_Pro_Setup_v1.0.0.exe', 'application/vnd.microsoft.portable-executable', 28648106, 1, 'uploaded', NULL, '2025-12-16 10:28:11', '2025-12-16 10:28:11'),
(23, '1765897651_PathoLab_Pro_Setup_v1.0.1.exe', 'PathoLab_Pro_Setup_v1.0.1.exe', 'uploads/1765897651_PathoLab_Pro_Setup_v1.0.1.exe', 'application/vnd.microsoft.portable-executable', 38169540, 1, 'uploaded', NULL, '2025-12-16 20:37:31', '2025-12-16 20:37:31'),
(24, '1765899181_PathoLab_Pro_Setup_v1.0.2.exe', 'PathoLab_Pro_Setup_v1.0.2.exe', 'uploads/1765899181_PathoLab_Pro_Setup_v1.0.2.exe', 'application/vnd.microsoft.portable-executable', 37874968, 1, 'uploaded', NULL, '2025-12-16 21:03:01', '2025-12-16 21:03:01'),
(25, '1765900428_PathoLab_Pro_Setup_v1.0.3.exe', 'PathoLab_Pro_Setup_v1.0.3.exe', 'uploads/1765900428_PathoLab_Pro_Setup_v1.0.3.exe', 'application/vnd.microsoft.portable-executable', 37879395, 1, 'uploaded', NULL, '2025-12-16 21:23:49', '2025-12-16 21:23:49'),
(26, '1766298181_PathoLab_Pro_Setup_v1.0.4.exe', 'PathoLab_Pro_Setup_v1.0.4.exe', 'uploads/1766298181_PathoLab_Pro_Setup_v1.0.4.exe', 'application/vnd.microsoft.portable-executable', 52088175, 1, 'uploaded', NULL, '2025-12-21 11:53:01', '2025-12-21 11:53:01'),
(30, '1767226582_PathoLab_Pro_Setup_v1.0.6.exe', 'PathoLab_Pro_Setup_v1.0.6.exe', 'uploads/1767226582_PathoLab_Pro_Setup_v1.0.6.exe', 'application/vnd.microsoft.portable-executable', 52931243, 1, 'uploaded', NULL, '2026-01-01 05:46:23', '2026-01-01 05:46:23');

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
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `client_responses`
--
ALTER TABLE `client_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

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
-- Indexes for table `email_signatures`
--
ALTER TABLE `email_signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_default` (`is_default`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `entries`
--
ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reg_no` (`reg_no`);

--
-- Indexes for table `entry_tests`
--
ALTER TABLE `entry_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_id` (`entry_id`);

--
-- Indexes for table `followups`
--
ALTER TABLE `followups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `followup_clients`
--
ALTER TABLE `followup_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `followup_templates`
--
ALTER TABLE `followup_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_clients`
--
ALTER TABLE `inventory_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `inventory_expense`
--
ALTER TABLE `inventory_expense`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_income`
--
ALTER TABLE `inventory_income`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `main_test_categories`
--
ALTER TABLE `main_test_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notices_active` (`active`),
  ADD KEY `idx_notices_added_by` (`added_by`);

--
-- Indexes for table `opd_appointments`
--
ALTER TABLE `opd_appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointment_number` (`appointment_number`),
  ADD UNIQUE KEY `unique_appointment_number` (`appointment_number`),
  ADD UNIQUE KEY `appointment_id` (`appointment_id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `idx_appointment_date` (`appointment_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `appointment_type_id` (`appointment_type_id`),
  ADD KEY `idx_appointments_date_status` (`appointment_date`,`status`);

--
-- Indexes for table `opd_appointment_types`
--
ALTER TABLE `opd_appointment_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`),
  ADD UNIQUE KEY `type_id` (`type_id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `opd_billing`
--
ALTER TABLE `opd_billing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opd_departments`
--
ALTER TABLE `opd_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`),
  ADD UNIQUE KEY `department_id` (`department_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `fk_opd_departments_head_doctor` (`head_doctor_id`);

--
-- Indexes for table `opd_doctors`
--
ALTER TABLE `opd_doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `doctor_id` (`doctor_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_specialization` (`specialization`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `specialization_id` (`specialization_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `idx_doctors_active` (`is_active`);

--
-- Indexes for table `opd_facilities`
--
ALTER TABLE `opd_facilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `facility_id` (`facility_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `opd_medical_records`
--
ALTER TABLE `opd_medical_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `record_id` (`record_id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `idx_record_date` (`record_date`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `opd_patients`
--
ALTER TABLE `opd_patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_id` (`patient_id`),
  ADD UNIQUE KEY `unique_patient_id` (`patient_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_patients_active` (`is_active`);

--
-- Indexes for table `opd_prescriptions`
--
ALTER TABLE `opd_prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prescription_id` (`prescription_id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `opd_reports`
--
ALTER TABLE `opd_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opd_specializations`
--
ALTER TABLE `opd_specializations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_name` (`name`),
  ADD UNIQUE KEY `specialization_id` (`specialization_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `opd_users`
--
ALTER TABLE `opd_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_users_role_active` (`role`,`is_active`);

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
-- Indexes for table `processed_emails`
--
ALTER TABLE `processed_emails`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_message_id` (`message_id`),
  ADD KEY `idx_processed_at` (`processed_at`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reports_created_at` (`created_at`),
  ADD KEY `idx_reports_added_by` (`added_by`);

--
-- Indexes for table `scheduled_emails`
--
ALTER TABLE `scheduled_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_schedule_date` (`schedule_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `sent_emails`
--
ALTER TABLE `sent_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `idx_priority` (`priority`);

--
-- Indexes for table `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_config_key` (`config_key`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

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
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_setting` (`user_id`,`setting_key`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `client_responses`
--
ALTER TABLE `client_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2016;

--
-- AUTO_INCREMENT for table `email_signatures`
--
ALTER TABLE `email_signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `entry_tests`
--
ALTER TABLE `entry_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followups`
--
ALTER TABLE `followups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `followup_clients`
--
ALTER TABLE `followup_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT for table `followup_templates`
--
ALTER TABLE `followup_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `inventory_clients`
--
ALTER TABLE `inventory_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `inventory_expense`
--
ALTER TABLE `inventory_expense`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `inventory_income`
--
ALTER TABLE `inventory_income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `main_test_categories`
--
ALTER TABLE `main_test_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `opd_appointments`
--
ALTER TABLE `opd_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_appointment_types`
--
ALTER TABLE `opd_appointment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `opd_billing`
--
ALTER TABLE `opd_billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_departments`
--
ALTER TABLE `opd_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `opd_doctors`
--
ALTER TABLE `opd_doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `opd_facilities`
--
ALTER TABLE `opd_facilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `opd_medical_records`
--
ALTER TABLE `opd_medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_patients`
--
ALTER TABLE `opd_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `opd_prescriptions`
--
ALTER TABLE `opd_prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_reports`
--
ALTER TABLE `opd_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_specializations`
--
ALTER TABLE `opd_specializations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `opd_users`
--
ALTER TABLE `opd_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=897;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `processed_emails`
--
ALTER TABLE `processed_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scheduled_emails`
--
ALTER TABLE `scheduled_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sent_emails`
--
ALTER TABLE `sent_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_config`
--
ALTER TABLE `system_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `zip_uploads`
--
ALTER TABLE `zip_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
-- Constraints for table `entry_tests`
--
ALTER TABLE `entry_tests`
  ADD CONSTRAINT `entry_tests_ibfk_1` FOREIGN KEY (`entry_id`) REFERENCES `entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `fk_notices_user` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `opd_appointments`
--
ALTER TABLE `opd_appointments`
  ADD CONSTRAINT `opd_appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `opd_patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opd_appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `opd_doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opd_appointments_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `opd_departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `opd_appointments_ibfk_4` FOREIGN KEY (`appointment_type_id`) REFERENCES `opd_appointment_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `opd_departments`
--
ALTER TABLE `opd_departments`
  ADD CONSTRAINT `fk_opd_departments_head_doctor` FOREIGN KEY (`head_doctor_id`) REFERENCES `opd_doctors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `opd_doctors`
--
ALTER TABLE `opd_doctors`
  ADD CONSTRAINT `opd_doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `opd_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `opd_doctors_ibfk_2` FOREIGN KEY (`specialization_id`) REFERENCES `opd_specializations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `opd_doctors_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `opd_departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `opd_facilities`
--
ALTER TABLE `opd_facilities`
  ADD CONSTRAINT `opd_facilities_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `opd_departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `opd_medical_records`
--
ALTER TABLE `opd_medical_records`
  ADD CONSTRAINT `opd_medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `opd_patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opd_medical_records_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `opd_doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opd_medical_records_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `opd_appointments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `opd_prescriptions`
--
ALTER TABLE `opd_prescriptions`
  ADD CONSTRAINT `opd_prescriptions_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `opd_patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opd_prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `opd_doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opd_prescriptions_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `opd_appointments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `opd_specializations`
--
ALTER TABLE `opd_specializations`
  ADD CONSTRAINT `opd_specializations_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `opd_departments` (`id`) ON DELETE SET NULL;

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
