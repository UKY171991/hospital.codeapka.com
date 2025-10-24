-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 24, 2025 at 07:09 AM
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
(3, 'asd', 'qw', NULL, 1, '2025-09-27 10:14:02', '2025-10-15 11:02:41'),
(5, 'erfg  hh', 'qws', 1, 1, '2025-09-27 10:30:03', '2025-10-15 11:19:16'),
(6, 'Sydnee Levine hh', 'Eos et in lorem non', 1, 1, '2025-09-28 10:02:38', '2025-10-15 11:19:10'),
(8, 'ttt', '', 1, 1, '2025-10-15 11:19:03', '2025-10-15 11:19:03');

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
(15, NULL, 'ASD', NULL, NULL, 'QW', 'Q', NULL, NULL, 'Q', NULL, 4.00, 1, '2025-09-28 11:44:10', '2025-10-17 16:38:21'),
(16, NULL, 'Dr. API Test 1760266881895', 'MBBS', 'General Medicine', 'Test Hospital', '9876543210', NULL, 'test@example.com', NULL, NULL, 10.00, 1, NULL, NULL),
(17, NULL, 'Dr. API Test 1760266881895', NULL, NULL, 'Test Hospital', '9876543210', NULL, NULL, NULL, NULL, 10.00, 1, NULL, NULL),
(18, NULL, 'ASD', NULL, NULL, 'QW', 'Q', NULL, NULL, NULL, NULL, 4.00, 1, NULL, NULL),
(19, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(20, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(21, NULL, 'Alma Cooke', NULL, NULL, 'Quia et rerum totam', 'Cupiditate sunt et', NULL, NULL, NULL, NULL, 96.00, 1, NULL, NULL),
(22, NULL, 'Candace Lowe', NULL, NULL, 'Sunt cum expedita l', 'Rerum qui id fuga E', NULL, NULL, NULL, NULL, 83.00, 1, NULL, NULL),
(23, NULL, 'Malcolm Callahan', NULL, NULL, 'Vero qui esse omnis', 'Quis consectetur si', NULL, NULL, NULL, NULL, 34.00, 1, NULL, NULL),
(24, NULL, 'sdef', NULL, NULL, 'qw', 'qw', NULL, NULL, NULL, NULL, 4.00, 1, NULL, NULL),
(25, NULL, 'Sylvester Harmon', NULL, NULL, 'Quia et repellendus', 'Cum occaecat dicta u', NULL, NULL, NULL, NULL, 24.00, 1, NULL, NULL),
(26, NULL, 'Constance Conrad', NULL, NULL, 'Nostrud obcaecati co', 'Ipsa iusto totam oc', NULL, NULL, NULL, NULL, 60.00, 1, NULL, NULL),
(27, NULL, 'Wade Solomon', NULL, NULL, 'Quas nostrud quibusd', 'Assumenda et suscipi', NULL, NULL, NULL, NULL, 57.00, 1, NULL, NULL),
(28, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(29, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(30, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(31, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(32, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(33, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(34, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(35, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(36, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(37, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(38, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(39, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(40, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(41, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(42, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(43, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(44, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(45, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(46, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(47, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(48, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(49, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(51, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(52, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(53, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(54, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(55, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(56, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(57, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(58, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(59, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(60, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(61, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(62, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(63, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(64, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(65, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(66, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(67, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(68, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(69, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(70, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(71, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(72, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(73, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, 'Ad beatae rerum sint', NULL, 9.00, 2, '2025-10-17 16:37:52', '2025-10-17 16:37:52'),
(74, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, 'Nemo sit quod ea sun', NULL, 16.00, 1, '2025-10-17 16:38:41', '2025-10-17 16:38:41'),
(75, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, 'Amet cupiditate ut', NULL, 77.00, 1, '2025-10-17 16:39:35', '2025-10-17 16:39:35'),
(76, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(77, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(78, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(79, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(80, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(81, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(82, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(83, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(84, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(85, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(86, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(87, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(88, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(89, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(90, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(91, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(92, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(93, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(94, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(95, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(96, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(97, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(98, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(99, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(100, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(101, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(102, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(103, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(104, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(105, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(106, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(107, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(108, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(109, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(110, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(111, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(112, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(113, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(114, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(115, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(116, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(117, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(118, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(119, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(120, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(121, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(122, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(123, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(124, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(125, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(126, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(127, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(128, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(129, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(130, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(131, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(132, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(133, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(134, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(135, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(136, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(137, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(138, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(139, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(140, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(141, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(142, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(143, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(144, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(145, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(146, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(147, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(148, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(149, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(150, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(151, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(152, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(153, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(154, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(155, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(156, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(157, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(158, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(159, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(160, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(161, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(162, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(163, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(164, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(165, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(166, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(167, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(168, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(169, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(170, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(171, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(172, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(173, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(174, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(175, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(176, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(177, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(178, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(179, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(180, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(181, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(182, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(183, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(184, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(185, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(186, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(187, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(188, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(189, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(190, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(191, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(192, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(193, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(194, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(195, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(196, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(197, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(198, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(199, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(200, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(201, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(202, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(203, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(204, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(205, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(206, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(207, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(208, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(209, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(210, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(211, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(212, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(213, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(214, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(215, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(216, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(217, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(218, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(219, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(220, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(221, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(222, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(223, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(224, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(225, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(226, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(227, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(228, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(229, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(230, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(231, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(232, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(233, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(234, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(235, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(236, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(237, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(238, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(239, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(240, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(241, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(242, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(243, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(244, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(245, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(246, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(247, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(248, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(249, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(250, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(251, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(252, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(253, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(254, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(255, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(256, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(257, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(258, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(259, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(260, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(261, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(262, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(263, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(264, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(265, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(266, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(267, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(268, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(269, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(270, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(271, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(272, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(273, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(274, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(275, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(276, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(277, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(278, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(279, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(280, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(281, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(282, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(283, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(284, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(285, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(286, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(287, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(288, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(289, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(290, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(291, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(292, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(293, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(294, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(295, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(296, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(297, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(298, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(299, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(300, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(301, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(302, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(303, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(304, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(305, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(306, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(307, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(308, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(309, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(310, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(311, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(312, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(313, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(314, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(315, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(316, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(317, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(318, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(319, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(320, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(321, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(322, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(323, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(324, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(325, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(326, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(327, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(328, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(329, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(330, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(331, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(332, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(333, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(334, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(335, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(336, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(337, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(338, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(339, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(340, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(341, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(342, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(343, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(344, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(345, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(346, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, '', NULL, 9.00, 1, NULL, '2025-10-24 07:41:56'),
(347, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(348, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(352, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, '', NULL, 44.00, 1, NULL, '2025-10-24 07:39:18'),
(353, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(354, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(355, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(356, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(357, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(358, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(359, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(360, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(361, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(362, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(363, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(364, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(365, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(366, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(367, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(368, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(369, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(370, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(374, NULL, 'uuufgns', NULL, 'wertg', NULL, NULL, '45545454', '', '', NULL, 0.00, 1, NULL, '2025-10-23 12:50:30'),
(375, NULL, 'AAAAA', NULL, 'BBBB jj', 'BBBB jj', '5454545454', '5454545454', '', '', NULL, 10.00, 2, NULL, '2025-10-24 11:07:06');

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
(1, 'qwer', '', 1, '2025-10-15 11:18:44', '2025-10-15 11:18:44'),
(2, 'Second main', '', 1, '2025-10-17 11:15:22', '2025-10-17 11:15:22'),
(3, 'sdfv', 'sdf', 1, '2025-10-23 07:22:34', '2025-10-23 07:22:34');

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
(2, NULL, 'Magna sint est vel', 'Qui praesentium eos', '1988-01-10 01:37:00', '1979-10-28 00:26:00', 1, 1, '2025-10-10 19:14:26', NULL),
(3, NULL, 'API Test Notice 1760266896536', 'This is a test notice created by the API test.', '2025-10-12 00:00:00', '2025-10-19 00:00:00', 1, 1, NULL, NULL),
(4, NULL, 'Test Notice 084539 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:53', NULL, 0, 1, NULL, NULL),
(5, NULL, 'Test Notice 084539 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:53', NULL, 0, 1, NULL, NULL),
(6, NULL, 'Test Notice 084539 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:53', NULL, 0, 1, NULL, NULL);

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
(4, NULL, 'Support (Umakant Yadav)', '9453619260', '9453619260', '', '', 'https://hospital.codeapka.com/', 1, NULL, NULL),
(5, NULL, 'API Test Owner 1760266902285', '9876543210', '9876543210', 'testowner@example.com', 'Test Owner Address', '', 1, NULL, NULL);

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
(2, NULL, 'James Sears', 'ciwi@mailinator.com', '5656565656', 'Arthur Oneal', 'Vel nobis error corr', 'female', 10, '', 'Years', 'P642622065', '2025-09-28 12:27:50', '2025-10-24 08:06:29', 2),
(3, NULL, 'Indigo Cortez', 'feloz@mailinator.com', '5454545454', 'Odette Villarreal', 'newada Jaunpur', 'Female', 82, '', 'Days', 'P483791824', '2025-09-28 13:31:49', '2025-10-23 12:33:00', 1),
(7, NULL, 'gfgfgf', '', '6556565656', 'wertg', '', NULL, 43, '', 'Years', 'P642622066', NULL, '2025-10-23 12:32:54', 1),
(9, NULL, 'sdfgb', '', '4554545454', 'werty', '', NULL, 44, '', 'Years', 'P642622068', NULL, NULL, 1),
(10, NULL, 'fgghnfds', '', '6767676767', 'fe', '', NULL, 66, '', 'Years', 'P642622069', NULL, NULL, 1),
(11, NULL, 'drftgh', '', '6556565656', 'qwertyhj', '', NULL, 34, '', 'Years', 'P642622070', NULL, NULL, 1),
(12, NULL, 'erty', '', '6767676767', 'rt', '', NULL, 6, '', 'Years', 'P642622071', NULL, NULL, 1),
(13, NULL, 'ertyu', '', '6767676767', 'qwertg', '', NULL, 55, '', 'Years', 'P642622072', NULL, '2025-10-23 15:57:43', 1),
(14, NULL, 'Karen Puckett', 'rejotocoha@mailinator.com', '5454545454', 'Rooney Hopkins', 'Culpa ratione nostru', 'Other', 8, 'Odit dolor elit asp', 'Years', 'P909868058', NULL, '2025-10-23 17:16:08', 1),
(15, NULL, 'ttt', '', '566565565656', 'ggg', '', NULL, 4, '', 'Years', 'P909868059', NULL, NULL, 1),
(17, NULL, 'wert', '', '5656565656', 'werty', '', NULL, 54, '', 'Years', 'P909868060', NULL, NULL, 1),
(18, NULL, 'erfedf', '', '555445455454', 'sdf', '', NULL, 4, '', 'Years', 'P909868061', NULL, NULL, 1),
(19, NULL, 'werf', '', '5665565656', 'werf', '', NULL, 44, '', 'Years', 'P909868062', NULL, NULL, 1),
(21, NULL, 'sdfg', '', '5545454545445', 'dfv', '', 'male', 44, '', 'Years', 'P909868063', NULL, NULL, 2),
(22, NULL, 'asdf', '', '545454545454', 'werfg', '', 'male', 4, '', 'Years', 'P909868064', NULL, NULL, 2),
(23, NULL, 'wertg', '', '4545545454', 'wedfg', '', 'male', 23, '', 'Years', 'P909868065', NULL, NULL, 2),
(24, NULL, 'werfg', '', '4554545454', 'esdfgb', '', 'male', 4, '', 'Years', 'P909868066', NULL, NULL, 2),
(25, NULL, 'sdfgq', '', '5656534565', 'edfg', '', 'male', 43, '', 'Years', 'P909868067', NULL, NULL, 2),
(26, NULL, 'wertgh', '', '655656565656', 'qwedfg', '', 'male', 5, '', 'Years', 'P909868068', NULL, NULL, 2),
(27, NULL, 'aerfg', '', '5454545445', 'er', '', 'male', 54, '', 'Years', 'P909868069', NULL, NULL, 2);

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
  `child_unit` varchar(50) DEFAULT NULL,
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

INSERT INTO `tests` (`id`, `name`, `category_id`, `main_category_id`, `price`, `unit`, `specimen`, `default_result`, `reference_range`, `min`, `max`, `description`, `min_male`, `max_male`, `min_female`, `max_female`, `min_child`, `max_child`, `child_unit`, `sub_heading`, `test_code`, `method`, `print_new_page`, `shortcut`, `added_by`, `created_at`, `updated_at`) VALUES
(1, 'Aubrey Reyes g', 3, 0, 980.00, 'abc', NULL, '', '', 20.00, 24.00, 'Aliquid labore place', 30.00, 31.00, 72.00, 78.00, NULL, NULL, NULL, 1, '', '', 1, '', 1, NULL, NULL),
(2, 'New test', 6, 0, 100.00, 'etc', NULL, '', '', 10.00, 20.00, '', 11.00, 21.00, 12.00, 22.00, 5.00, 8.00, 'etc', 0, '', '', 0, '', 1, NULL, NULL),
(3, 'New test', 6, 0, 100.00, 'etc', NULL, '', '', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, 1, NULL, NULL),
(4, 'Aubrey Reyes g', 3, 0, 980.00, 'abc', NULL, '', '', NULL, NULL, 'Aliquid labore place', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '', 0, NULL, 1, NULL, NULL);

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
(1, 'umakant', '$2y$12$8RovPoAOxY30weFvoSKJD.aabD27dV8cHbqON2XTQ04x1fs/Tw1da', 'Umakant Yadav', 'umakant171991@gmail.com', 'master', 1, 'Pathology', '2025-09-26 10:12:24', '2025-10-24 06:32:55', '2025-10-26 10:12:00', '', '0000-00-00 00:00:00', '2025-09-26 04:42:48'),
(2, 'uma', '$2y$12$auavnwTI5hbfkyqCoavijO/i3diGYgDZqt58EzkY6ZkB6M9jTmD9e', 'Uma Yadav', 'umakant171992@gmail.com', 'user', 1, 'Pathology', '2025-09-26 10:13:58', NULL, '2025-12-30 23:59:00', '6dad141b199e8c2ae3021462459c23135244d408778939a4c33c4a969726fde7', '0000-00-00 00:00:00', '2025-10-18 05:36:53');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;

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
-- AUTO_INCREMENT for table `main_test_categories`
--
ALTER TABLE `main_test_categories`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
