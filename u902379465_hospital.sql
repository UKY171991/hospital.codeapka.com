-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 27, 2025 at 10:49 AM
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
(3, 'AAAA', 'qw', 2, 1, '2025-09-27 10:14:02', '2025-10-25 23:20:42'),
(5, 'erfg  hh', 'qws', 1, 1, '2025-09-27 10:30:03', '2025-10-15 11:19:16'),
(6, 'Sydnee Levine hh', 'Eos et in lorem non', 1, 1, '2025-09-28 10:02:38', '2025-10-15 11:19:10'),
(8, 'ttt', '', 1, 1, '2025-10-15 11:19:03', '2025-10-15 11:19:03'),
(9, 'sdfgfgb', 'derfg', 3, 1, '2025-10-24 07:21:52', '2025-10-25 23:20:31'),
(10, 'gggg', '', 3, 1, '2025-10-25 10:16:00', '2025-10-25 23:20:23');

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
(362, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL);

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

INSERT INTO `entries` (`id`, `owner_id`, `server_id`, `patient_id`, `doctor_id`, `entry_date`, `status`, `priority`, `referral_source`, `subtotal`, `discount_amount`, `total_price`, `payment_status`, `notes`, `added_by`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 3, NULL, '2025-10-06 00:00:00', 'pending', 'normal', NULL, 100.00, 20.00, 100.00, 'pending', NULL, 1, '2025-10-06 14:13:29', NULL),
(2, 2, NULL, 2, 15, '2025-10-08 00:00:00', 'pending', 'normal', NULL, 100.00, 0.00, 60.00, 'pending', NULL, 2, '2025-10-06 15:53:42', NULL),
(5, 1, NULL, 3, NULL, '2025-10-05 00:00:00', 'completed', 'normal', NULL, 200.00, 0.00, 300.00, 'pending', NULL, 1, '2025-10-06 16:21:47', NULL),
(6, 1, NULL, 3, NULL, '2025-10-05 00:00:00', 'completed', 'normal', NULL, 1080.00, 100.00, 980.00, 'pending', NULL, 1, '2025-10-06 16:40:50', NULL),
(7, 1, NULL, 3, NULL, '2025-10-05 00:00:00', 'completed', 'normal', NULL, 400.00, 0.00, 300.00, 'pending', NULL, 1, '2025-10-06 17:12:12', NULL),
(9, 2, NULL, 2, 14, '2025-10-08 00:00:00', 'pending', 'normal', NULL, 500.00, 0.00, 300.00, 'pending', NULL, 2, '2025-10-08 11:21:38', NULL),
(10, 1, NULL, 3, 12, '2025-10-26 00:00:00', 'pending', 'normal', NULL, 600.00, 0.00, 340.00, 'pending', NULL, 1, '2025-10-08 17:09:55', '2025-10-26 08:41:20'),
(15, 1, NULL, 3, 12, '2025-10-26 00:00:00', 'pending', 'normal', NULL, 1080.00, 0.00, 1080.00, 'pending', NULL, 1, '2025-10-09 08:16:19', '2025-10-26 06:13:36'),
(17, 1, NULL, 27, 362, '2025-10-27 00:00:00', 'pending', 'normal', '', 1380.00, 20.00, 1360.00, 'pending', '', 1, '2025-10-09 08:30:36', '2025-10-27 10:19:22');

-- --------------------------------------------------------

--
-- Table structure for table `entry_tests`
--

CREATE TABLE `entry_tests` (
  `id` int(10) UNSIGNED NOT NULL,
  `entry_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL,
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

INSERT INTO `entry_tests` (`id`, `entry_id`, `test_id`, `category_id`, `result_value`, `unit`, `remarks`, `status`, `price`, `discount_amount`, `total_price`, `created_at`) VALUES
(54, 15, 4, 0, '', 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-26 11:43:36'),
(55, 15, 5, 0, '', 'abc', NULL, 'pending', 0.00, 0.00, 0.00, '2025-10-26 11:43:36'),
(61, 10, 4, 0, '', 'abc', NULL, 'pending', 980.00, 0.00, 980.00, '2025-10-26 14:11:20'),
(62, 10, 5, 0, '', 'aaa', NULL, 'pending', 100.00, 0.00, 100.00, '2025-10-26 14:11:20'),
(83, 17, 6, 0, '40', 'ttt', NULL, 'pending', 100.00, 0.00, 100.00, '2025-10-27 15:49:22'),
(84, 17, 4, 0, '', 'abc', NULL, 'pending', 100.00, 0.00, 100.00, '2025-10-27 15:49:22'),
(85, 17, 7, 0, '', 'abc', NULL, 'pending', 100.00, 0.00, 100.00, '2025-10-27 15:49:22');

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
(3, 'wwwww', 'sdf', 1, '2025-10-23 07:22:34', '2025-10-24 11:29:38');

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
(10, NULL, 'fgghnfds', '', '6767676767', 'fe', '', NULL, 66, '', 'Years', 'P642622069', NULL, NULL, 1),
(11, NULL, 'drftgh', '', '6556565656', 'qwertyhj', '', NULL, 34, '', 'Years', 'P642622070', NULL, NULL, 1),
(14, NULL, 'Karen Puckett', 'rejotocoha@mailinator.com', '5454545454', 'Rooney Hopkins', 'Culpa ratione nostru', 'Male', 8, '', 'Years', 'P909868058', NULL, '2025-10-26 05:56:19', 1),
(15, NULL, 'ttt', '', '566565565656', 'ggg', '', NULL, 4, '', 'Years', 'P909868059', NULL, NULL, 1),
(17, NULL, 'wert', '', '5656565656', 'werty', '', NULL, 54, '', 'Years', 'P909868060', NULL, NULL, 1),
(19, NULL, 'werf', '', '5665565656', 'werf', '', 'Male', 44, '', 'Years', 'P909868062', NULL, '2025-10-26 05:56:10', 1),
(21, NULL, 'sdfg', '', '5545454545445', 'dfv', '', 'male', 44, '', 'Years', 'P909868063', NULL, NULL, 2),
(22, NULL, 'asdf', '', '545454545454', 'werfg', '', 'male', 4, '', 'Years', 'P909868064', NULL, NULL, 2),
(23, NULL, 'wertg', '', '4545545454', 'wedfg', '', 'male', 23, '', 'Years', 'P909868065', NULL, NULL, 2),
(24, NULL, 'werfg', '', '4554545454', 'esdfgb', '', 'male', 4, '', 'Years', 'P909868066', NULL, NULL, 2),
(25, NULL, 'sdfgq', '', '5656534565', 'edfg', '', 'male', 43, '', 'Years', 'P909868067', NULL, NULL, 2),
(26, NULL, 'wertgh', '', '655656565656', 'qwedfg', '', 'male', 5, '', 'Years', 'P909868068', NULL, NULL, 2),
(27, NULL, 'aerfg gt', '', '5454545445', 'er', '', 'Male', 54, '', 'Years', 'P909868069', NULL, '2025-10-26 09:34:04', 2);

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
(2, 'New test', 6, 1, 100.00, 'etc', NULL, '', '', 10.00, 20.00, '', 11.00, 21.00, 12.00, 22.00, 5.00, 8.00, 0, '', '', 1, '', 1, NULL, NULL),
(4, 'Aubrey Reyes g', 5, 1, 980.00, 'abc', '', '', '', 100.00, 200.00, 'Aliquid labore place', 80.00, 190.00, 50.00, 60.00, 5.00, 30.00, 0, '', '', 0, '', 1, NULL, '2025-10-26 04:54:19'),
(5, 'sdf', 6, 1, 100.00, 'aaa', '', '', '', 45.00, 50.00, '', 45.00, 50.00, 14.00, 20.00, 22.00, 25.00, 0, '', '', 0, '', 2, NULL, '2025-10-25 15:10:33'),
(6, 'yyy', 5, 1, 300.00, 'ttt', '', '', '', 30.00, 50.00, '', 44.00, 55.00, 66.00, 69.00, 33.00, 44.00, 0, '', '', 0, '', 2, '2025-10-25 15:28:26', '2025-10-25 15:28:26'),
(7, 'Test new', 10, 3, 300.00, 'ggrr', '', '', '', 10.00, 20.00, '', 20.00, 30.00, 22.00, 44.00, 10.00, 14.00, 0, '', '', 0, '', 1, '2025-10-26 04:53:13', '2025-10-26 05:13:40');

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
(1, 'umakant', '$2y$12$8RovPoAOxY30weFvoSKJD.aabD27dV8cHbqON2XTQ04x1fs/Tw1da', 'Umakant Yadav', 'umakant171991@gmail.com', 'master', 1, 'Pathology', '2025-09-26 10:12:24', '2025-10-27 15:06:46', '2025-10-26 10:12:00', '', '0000-00-00 00:00:00', '2025-09-26 04:42:48'),
(2, 'uma', '$2y$12$auavnwTI5hbfkyqCoavijO/i3diGYgDZqt58EzkY6ZkB6M9jTmD9e', 'Uma Yadav', 'umakant171992@gmail.com', 'user', 1, 'Pathology', '2025-09-26 10:13:58', NULL, '2025-12-30 23:59:00', '6dad141b199e8c2ae3021462459c23135244d408778939a4c33c4a969726fde7', '0000-00-00 00:00:00', '2025-10-18 05:36:53');

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
(1, 1, 'gmail_password', 'am5pbWl1aXluam5vcHZrdA==', '{\"type\":\"app\",\"created\":\"2025-10-25 09:22:07\"}', '2025-10-25 09:22:07', '2025-10-25 09:22:07');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

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
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `zip_uploads`
--
ALTER TABLE `zip_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
