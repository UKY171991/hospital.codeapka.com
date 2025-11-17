-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 17, 2025 at 12:34 PM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `company`, `address`, `city`, `state`, `zip`, `notes`, `created_at`, `updated_at`) VALUES
(3, 'Amzad', 'info@no.com', '+91 95400 52228', '', '', 'Delhi', 'Delhi', '', '', '2025-11-17 12:17:49', '2025-11-17 12:19:14');

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
(166, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(168, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(216, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(218, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(229, NULL, 'Elijah Mayo', NULL, NULL, 'Eius veritatis ullam', 'Sed sed eiusmod sequ', NULL, NULL, NULL, NULL, 77.00, 1, NULL, NULL),
(231, NULL, 'Laura Bond', NULL, NULL, 'Aliqua Nulla ipsum', 'Officia magni ipsum', NULL, NULL, NULL, NULL, 16.00, 1, NULL, NULL),
(232, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
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
(333, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(337, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(376, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(377, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(378, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(379, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(380, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(381, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(399, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(400, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(401, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(402, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(403, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(404, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(405, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(406, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(407, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(408, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(409, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(410, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(411, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(412, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(413, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(414, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(415, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(416, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(417, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(418, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(419, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(420, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(421, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(422, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(423, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(424, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(425, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(426, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(427, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(428, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(429, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(430, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(431, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(432, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(433, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(434, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(435, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(436, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(437, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(438, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(439, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(440, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(441, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(442, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(443, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(444, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(445, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(446, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(447, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(448, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(449, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(450, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(451, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(452, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(453, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(454, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(455, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(456, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(457, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(458, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(459, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(460, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(461, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(462, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(463, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(464, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(465, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(466, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(467, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(468, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(469, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(470, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(471, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(472, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(473, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(474, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(475, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(476, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(477, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(478, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(479, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(480, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(481, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(482, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(483, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(484, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(485, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(486, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(487, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(488, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(489, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(490, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(491, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(492, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(493, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(494, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(495, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(496, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(497, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(498, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(499, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(500, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(501, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(502, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(503, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(504, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(505, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(506, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(507, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(508, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(509, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(510, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(511, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(512, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(513, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(514, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(515, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(516, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(517, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(518, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(519, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(520, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(521, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(522, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(523, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(524, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(525, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(526, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(527, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(528, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(529, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(530, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(531, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(532, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(533, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(534, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(535, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(536, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(537, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(538, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(539, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(540, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(541, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(542, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(543, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(544, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(545, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(546, NULL, 'wed', NULL, NULL, 'awsdef', 'awedf', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(547, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(548, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(549, NULL, 'wed', NULL, NULL, 'awsdef', 'awedf', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(550, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(551, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(552, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(553, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(554, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(555, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(556, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(557, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(558, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(559, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(560, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(561, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(562, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(563, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(564, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(565, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(566, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(567, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(568, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(569, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(570, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(571, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(572, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(573, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(574, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(575, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(576, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(577, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(578, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(579, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(580, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(581, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(582, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(583, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(584, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(585, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(586, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(587, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(588, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(589, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(590, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(591, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(592, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(593, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(594, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(595, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(596, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(597, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(598, NULL, 'wed', NULL, NULL, 'awsdef', 'awedf', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(599, NULL, 'wed', NULL, NULL, 'awsdef', 'awedf', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(600, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(601, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(602, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(603, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(604, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(605, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(606, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(607, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(608, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(609, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(610, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(611, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(612, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(613, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(614, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(615, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(616, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(617, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(618, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(619, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(620, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(621, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(622, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(623, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(624, NULL, 'df', NULL, NULL, 'df', '4554545454', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(625, NULL, 'df', NULL, NULL, 'df', '4554545454', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(626, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(627, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(628, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(629, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(630, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(631, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(632, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(633, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(634, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(635, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(636, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(637, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(638, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(639, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(640, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(641, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(642, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(643, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(644, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(645, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(646, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(647, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(648, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(649, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(650, NULL, 'df', NULL, NULL, 'sdfg', '6565655656', NULL, NULL, NULL, NULL, 20.00, 2, NULL, NULL),
(651, NULL, 'df', NULL, NULL, 'sdfg', '6565655656', NULL, NULL, NULL, NULL, 20.00, 2, NULL, NULL),
(652, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(653, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(654, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(655, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(656, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(657, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(658, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(659, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(660, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(661, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(662, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(663, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(664, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(665, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(666, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(667, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(668, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(669, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(670, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(671, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(672, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(673, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(674, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(675, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(676, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(677, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(678, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(679, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(680, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(681, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(682, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(683, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(684, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(685, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(686, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(687, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(688, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(689, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(690, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(691, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(692, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(693, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(694, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(695, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(696, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(697, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(698, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(699, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL);
INSERT INTO `doctors` (`id`, `server_id`, `name`, `qualification`, `specialization`, `hospital`, `contact_no`, `phone`, `email`, `address`, `registration_no`, `percent`, `added_by`, `created_at`, `updated_at`) VALUES
(700, NULL, 'df', NULL, NULL, 'sdfg', '6565655656', NULL, NULL, NULL, NULL, 20.00, 2, NULL, NULL),
(701, NULL, 'df', NULL, NULL, 'sdfg', '6565655656', NULL, NULL, NULL, NULL, 20.00, 2, NULL, NULL),
(702, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(703, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(704, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(705, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(706, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(707, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(708, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(709, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(710, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(711, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(712, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(713, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(714, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(715, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(716, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(717, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(718, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(719, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(720, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(721, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(722, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(723, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(724, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(725, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(726, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(727, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(728, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(729, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(730, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(731, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(732, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(733, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(734, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(735, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(736, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(737, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(738, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(739, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(740, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(741, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(742, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(743, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(744, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(745, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(746, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(747, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(748, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(749, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(750, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(751, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(752, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(753, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(754, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(755, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(756, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(757, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(758, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(759, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(760, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(761, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(762, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(763, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(764, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(765, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(766, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(767, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(768, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(769, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(770, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(771, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(772, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(773, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(774, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(775, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(776, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(777, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(778, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(779, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(780, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(781, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(782, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(783, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(784, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(785, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(786, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(787, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(788, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(789, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(790, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(791, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(792, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(793, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(794, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(795, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(796, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(797, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(798, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(799, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(800, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(801, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(802, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(803, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(804, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(805, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(806, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(807, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(808, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(809, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(810, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(811, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(812, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(813, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(814, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(815, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(816, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(817, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(818, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(819, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(820, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(821, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(822, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(823, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(824, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(825, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(826, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(827, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(828, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(829, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(830, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(831, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(832, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(833, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(834, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(835, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(836, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(837, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(838, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(839, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(840, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(841, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(842, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(843, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(844, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(845, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(846, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(847, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(848, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(849, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(850, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(851, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(852, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(853, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(854, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(855, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(856, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(857, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(858, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(859, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(860, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(861, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(862, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(863, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(864, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(865, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(866, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(867, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(868, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(869, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(870, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(871, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(872, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(873, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(874, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(875, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(876, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(877, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(878, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(879, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(880, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(881, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(882, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(883, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(884, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(885, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(886, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(887, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(888, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(889, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(890, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(891, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(892, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(893, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(894, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(895, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(896, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(897, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(898, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(899, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(900, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(901, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(902, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(903, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(904, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(905, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(906, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(907, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(908, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(909, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(910, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(911, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(912, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(913, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(914, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(915, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(916, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(917, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(918, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(919, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(920, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(921, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(922, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(923, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(924, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(925, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(926, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(927, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(928, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(929, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(930, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(931, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(932, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(933, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(934, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(935, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(936, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(937, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(938, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(939, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(940, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(941, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(942, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(943, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(944, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(945, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(946, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(947, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(948, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(949, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(950, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(951, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(952, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(953, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(954, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(955, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(956, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(957, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(958, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(959, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(960, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(961, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(962, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(963, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(964, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(965, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(966, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(967, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(968, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(969, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(970, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(971, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(972, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(973, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(974, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(975, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(976, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(977, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(978, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(979, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(980, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(981, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(982, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(983, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(984, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(985, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(986, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(987, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(988, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(989, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(990, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(991, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(992, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(993, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(994, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(995, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(996, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(997, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(998, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(999, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1000, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1001, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1002, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1003, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1004, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1005, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1006, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1007, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1008, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1009, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1010, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1011, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1012, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1013, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1014, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1015, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1016, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1017, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1018, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1019, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1020, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1021, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1022, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1023, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1024, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1025, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1026, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1027, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1028, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1029, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1030, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1031, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1032, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1033, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1034, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1035, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1036, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1037, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1038, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1039, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1040, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1041, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1042, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1043, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1044, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1045, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1046, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1047, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1048, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1049, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1050, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1051, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1052, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1053, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1054, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1055, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1056, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1057, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1058, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1059, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1060, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1061, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1062, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1063, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1064, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1065, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1066, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1067, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1068, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1069, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1070, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1071, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1072, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1073, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1074, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1075, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1076, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1077, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1078, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1079, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1080, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1081, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1082, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1083, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1084, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1085, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1086, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1087, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1088, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1089, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1090, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1091, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1092, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1093, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL);
INSERT INTO `doctors` (`id`, `server_id`, `name`, `qualification`, `specialization`, `hospital`, `contact_no`, `phone`, `email`, `address`, `registration_no`, `percent`, `added_by`, `created_at`, `updated_at`) VALUES
(1094, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1095, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1096, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1097, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1098, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1099, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1100, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1101, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1102, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1103, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1104, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1105, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1106, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1107, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1108, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1109, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1110, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1111, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1112, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1113, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1114, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1115, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1116, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1117, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1118, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1119, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1120, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1121, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1122, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1123, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1124, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1125, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1126, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1127, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1128, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1129, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1130, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1131, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1132, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1133, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1134, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1135, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1136, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1137, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1138, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1139, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1140, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1141, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1142, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1143, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1144, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1145, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1146, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1147, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1148, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1149, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1150, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1151, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1152, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1153, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1154, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1155, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1156, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1157, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1158, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1159, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1160, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1161, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1162, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1163, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1164, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1165, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1166, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1167, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1168, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1169, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1170, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1171, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1172, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1173, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1174, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1175, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1176, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1177, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1178, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1179, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1180, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1181, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1182, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1183, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1184, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1185, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1186, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1187, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1188, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1189, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1190, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1191, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1192, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1193, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1194, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1195, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1196, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1197, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1198, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1199, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1200, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1201, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1202, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1203, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1204, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1205, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1206, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1207, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1208, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1209, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1210, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1211, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1212, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1213, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1214, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1215, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1216, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1217, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1218, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1219, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1220, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1221, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1222, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1223, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1224, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1225, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1226, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1227, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1228, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1229, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1230, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1231, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1232, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1233, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1234, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1235, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1236, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1237, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1238, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1239, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1240, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1241, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1242, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1243, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1244, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1245, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1246, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1247, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1248, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1249, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1250, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1251, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1252, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1253, NULL, 'Leslie Frazier', NULL, NULL, 'Tempor ut ut ullam n', 'Accusamus sed quam s', NULL, NULL, NULL, NULL, 41.00, 2, NULL, NULL),
(1254, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1255, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1256, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1257, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1258, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1259, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1260, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1261, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1262, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1263, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1264, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1265, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1266, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1267, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1268, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1269, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1270, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1271, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1272, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1273, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1274, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1275, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1276, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1277, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1278, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1279, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1280, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1281, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1282, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1283, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1284, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1285, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1286, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1287, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1288, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1289, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1290, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1291, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1292, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1293, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1294, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1295, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1296, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1297, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1298, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1299, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1300, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1301, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1302, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1303, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1304, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1305, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1306, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1307, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1308, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1309, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1310, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1311, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1312, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1313, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1314, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1315, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1316, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1317, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1318, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1319, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1320, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1321, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1322, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1323, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1324, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1325, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1326, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1327, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1328, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1329, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1330, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1331, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1332, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1333, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1334, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1335, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1336, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1337, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1338, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1339, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1340, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1341, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1342, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1343, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1344, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1345, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1346, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1347, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1348, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1349, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1350, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1351, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1352, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1353, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1354, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1355, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1356, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1357, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1358, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1359, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1360, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1361, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1362, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1363, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1364, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1365, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1366, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1367, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1368, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1369, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1370, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1371, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1372, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1373, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1374, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1375, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1376, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1377, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1378, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1379, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1380, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1381, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1382, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1383, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1384, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1385, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1386, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1387, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1388, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1389, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1390, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1391, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1392, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1393, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1394, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1395, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1396, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1397, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1398, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1399, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1400, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1401, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1402, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1403, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1404, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1405, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1406, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1407, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1408, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1409, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1410, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1411, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1412, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1413, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1414, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1415, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1416, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1417, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1418, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1419, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1420, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1421, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1422, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1423, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1424, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1425, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1426, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1427, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1428, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1429, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1430, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1431, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1432, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1433, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1434, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1435, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1436, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1437, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1438, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1439, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1440, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1441, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1442, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1443, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1444, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1445, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1446, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1447, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1448, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1449, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1450, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1451, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1452, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1453, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1454, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1455, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1456, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1457, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1458, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1459, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1460, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1461, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1462, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1463, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1464, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1465, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1466, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1467, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1468, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1469, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1470, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1471, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1472, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1473, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1474, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1475, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1476, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1477, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1478, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1479, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1480, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1481, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1482, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1483, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1484, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1485, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1486, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1487, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL);
INSERT INTO `doctors` (`id`, `server_id`, `name`, `qualification`, `specialization`, `hospital`, `contact_no`, `phone`, `email`, `address`, `registration_no`, `percent`, `added_by`, `created_at`, `updated_at`) VALUES
(1488, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1489, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1490, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1491, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1492, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1493, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1494, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1495, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1496, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1497, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1498, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1499, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1500, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1501, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1502, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1503, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1504, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1505, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1506, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1507, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1508, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1509, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1510, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1511, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1512, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1513, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1514, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1515, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1516, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1517, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1518, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1519, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1520, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1521, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1522, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1523, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1524, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1525, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1526, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1527, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1528, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1529, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1530, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1531, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1532, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1533, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1534, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1535, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1536, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1537, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1538, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1539, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1540, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1541, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1542, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1543, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1544, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1545, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1546, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1547, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1548, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1549, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1550, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1551, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1552, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1553, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1554, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1555, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1556, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1557, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1558, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1559, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1560, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1561, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1562, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1563, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1564, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1565, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1566, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1567, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1568, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1569, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1570, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1571, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1572, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1573, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1574, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1575, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1576, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1577, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1578, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1579, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1580, NULL, 'df', NULL, NULL, 'df', '54545445', NULL, NULL, NULL, NULL, 40.00, 2, NULL, NULL),
(1581, NULL, 'tt', NULL, NULL, 'ert', '6556565656', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1582, NULL, 'wed', NULL, NULL, 'awsdef', '5656565656', NULL, NULL, NULL, NULL, 12.00, 2, NULL, NULL),
(1583, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1584, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1585, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1586, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1587, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1588, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1589, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1590, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1591, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1592, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1593, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1594, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1595, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1596, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1597, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1598, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1599, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1600, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1601, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1602, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1603, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1604, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1605, NULL, '{{doctor_name}}', NULL, '{{specialization}}', '{{hospital_name}}', NULL, '{{doctor_phone}}', '{{doctor_email}}', NULL, NULL, 0.00, 1, NULL, NULL),
(1606, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1607, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1608, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1609, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1610, NULL, '{{doctor_name}}', NULL, NULL, '{{hospital_name}}', NULL, NULL, NULL, NULL, NULL, 0.00, 1, NULL, NULL),
(1611, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1612, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1613, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1614, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1615, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1616, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1617, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1618, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1619, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1620, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1621, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1622, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1623, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1624, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1625, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1626, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1627, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1628, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1629, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1630, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1631, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1632, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1633, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1634, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1635, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1636, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1637, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1638, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1639, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1640, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1641, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1642, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1643, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1644, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1645, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1646, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1647, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1648, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1649, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1650, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1651, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1652, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1653, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1654, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1655, NULL, '{{doctor_name}}', NULL, NULL, '{{hospital_name}}', NULL, NULL, NULL, NULL, NULL, 0.00, 1, NULL, NULL),
(1656, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1657, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1658, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1659, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1660, NULL, '{{doctor_name}}', NULL, NULL, '{{hospital_name}}', NULL, NULL, NULL, NULL, NULL, 0.00, 1, NULL, NULL),
(1661, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1662, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1663, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1664, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1665, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1666, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1667, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1668, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1669, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1670, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1671, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1672, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1673, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1674, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1675, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1676, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1677, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1678, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1679, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1680, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1681, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1682, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1683, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1684, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1685, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1686, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1687, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1688, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1689, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1690, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1691, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1692, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1693, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1694, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1695, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1696, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1697, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1698, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1699, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1700, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1701, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1702, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1703, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1704, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1705, NULL, '{{doctor_name}}', NULL, NULL, '{{hospital_name}}', NULL, NULL, NULL, NULL, NULL, 0.00, 1, NULL, NULL),
(1706, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1707, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1708, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1709, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1710, NULL, '{{doctor_name}}', NULL, NULL, '{{hospital_name}}', NULL, NULL, NULL, NULL, NULL, 0.00, 1, NULL, NULL),
(1711, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1712, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1713, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1714, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1715, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1716, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1717, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1718, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1719, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1720, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1721, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1722, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1723, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1724, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1725, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1726, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1727, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1728, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1729, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1730, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1731, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1732, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1733, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1734, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1735, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1736, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1737, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1738, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1739, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1740, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1741, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1742, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1743, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1744, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1745, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1746, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1747, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1748, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1749, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1750, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1751, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1752, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1753, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1754, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1755, NULL, '{{doctor_name}}', NULL, NULL, '{{hospital_name}}', NULL, NULL, NULL, NULL, NULL, 0.00, 1, NULL, NULL),
(1756, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1757, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1758, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1759, NULL, 'edf', NULL, NULL, 'werf', '545455454', NULL, NULL, NULL, NULL, 30.00, 2, NULL, NULL),
(1760, NULL, '{{doctor_name}}', NULL, NULL, '{{hospital_name}}', NULL, NULL, NULL, NULL, NULL, 0.00, 1, NULL, NULL),
(1761, NULL, 'Sarah Mcmahon', NULL, NULL, 'Rerum aspernatur dol', 'Ad deserunt ad est s', NULL, NULL, NULL, NULL, 9.00, 2, NULL, NULL),
(1762, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1763, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1764, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1765, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1766, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1767, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1768, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1769, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1770, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1771, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1772, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1773, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1774, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1775, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1776, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1777, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1778, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1779, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1780, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL),
(1781, NULL, 'df', NULL, NULL, 'sdf', '5656565656', NULL, NULL, NULL, NULL, 50.00, 2, NULL, NULL),
(1782, NULL, 'Carl Whitaker', NULL, NULL, 'Recusandae Sint ex', 'Qui non dolor amet', NULL, NULL, NULL, NULL, 44.00, 2, NULL, NULL);

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
  `owner_id` int(11) DEFAULT NULL,
  `server_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `entry_date` datetime DEFAULT NULL,
  `date_slot` varchar(50) DEFAULT NULL COMMENT 'Time slot: morning/afternoon/evening/night',
  `service_location` varchar(100) DEFAULT NULL COMMENT 'Service location: lab/home/hospital/clinic/other',
  `collection_address` text DEFAULT NULL COMMENT 'Address for home collection',
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

INSERT INTO `entries` (`id`, `owner_id`, `server_id`, `patient_id`, `doctor_id`, `entry_date`, `date_slot`, `service_location`, `collection_address`, `status`, `priority`, `referral_source`, `subtotal`, `discount_amount`, `total_price`, `payment_status`, `notes`, `added_by`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, NULL, '2025-10-08 00:00:00', NULL, NULL, NULL, 'pending', 'normal', NULL, 0.00, 0.00, 0.00, 'pending', NULL, 1, '2025-11-16 06:54:49', NULL),
(2, NULL, NULL, NULL, NULL, '2025-10-08 00:00:00', NULL, NULL, NULL, 'pending', 'normal', NULL, 0.00, 0.00, 0.00, 'pending', NULL, 1, '2025-11-16 06:54:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `entry_tests`
--

CREATE TABLE `entry_tests` (
  `id` int(10) UNSIGNED NOT NULL,
  `entry_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL,
  `main_category_id` int(11) NOT NULL,
  `result_value` varchar(255) DEFAULT NULL,
  `unit` varchar(64) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `price` decimal(10,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_clients`
--

INSERT INTO `inventory_clients` (`id`, `name`, `type`, `email`, `phone`, `address`, `city`, `state`, `pincode`, `gst_number`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'Individual', 'john@example.com', '9876543210', '123 Main St', 'Mumbai', 'Maharashtra', NULL, NULL, 'Active', NULL, '2025-11-16 05:05:49', NULL),
(2, 'ABC Corporation', 'Corporate', 'contact@abc.com', '9876543211', '456 Business Park', 'Delhi', 'Delhi', NULL, NULL, 'Active', NULL, '2025-11-16 05:05:49', NULL),
(3, 'Ravi', 'Individual', 'info@xyz.com', '0000000000', '789 Insurance Tower', 'Bangalore', 'Bombay', '', '', 'Active', '', '2025-11-16 05:05:49', '2025-11-17 05:14:04'),
(4, 'Vishal ', 'Individual', 'john@example.com', '08427722958', '123 Main St', 'Mumbai', 'Punjab', '', '', 'Active', '', '2025-11-16 07:47:46', '2025-11-17 01:01:45'),
(5, 'Gyas', 'Individual', 'contact@abc.com', '72755 51625', '456 Business Park', 'Delhi', 'Delhi', '', '', 'Active', '', '2025-11-16 07:47:46', '2025-11-17 00:54:05'),
(6, 'Amzad', 'Individual', 'info@xyz.com', '+91 95400 52228', '789 Insurance Tower', 'Bangalore', 'Delhi', '', '', 'Active', '', '2025-11-16 07:47:46', '2025-11-17 00:55:01');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `inventory_expense`
--

INSERT INTO `inventory_expense` (`id`, `date`, `category`, `vendor`, `description`, `amount`, `payment_method`, `payment_status`, `invoice_number`, `notes`, `created_at`, `updated_at`) VALUES
(11, '2025-11-13', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 4500.00, 'UPI', 'Success', NULL, 'Auto-imported from email', '2025-11-17 06:24:27', NULL),
(12, '2025-11-15', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 1100.00, 'UPI', 'Success', NULL, 'Auto-imported from email', '2025-11-17 06:24:31', NULL),
(13, '2025-11-16', 'Other', 'canarabank@canarabank.com', 'ATM/IMPS/UPI Transaction Alert', 1000.00, 'UPI', 'Success', NULL, 'Auto-imported from email', '2025-11-17 06:24:31', NULL),
(14, '2025-11-17', 'Medical Supplies', 'Dr Tahsheel', 'Sarthak and  saksham madicine', 100.00, 'UPI', 'Success', '', '', '2025-11-17 07:27:26', '2025-11-17 07:38:30'),
(15, '2025-11-17', 'Other', 'Surylal Maurya', 'Biskit purchage  ', 70.00, 'UPI', 'Success', '', '', '2025-11-17 12:09:19', NULL);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
(1, 'C-Reactive protein test', 'The CRP test analyzes the level of C-reactive protein in the bloodstream. The specific protein is made by the liver only. Any heightened levels of the same ...', 1, '2025-10-15 11:18:44', '2025-11-14 04:38:10'),
(2, 'Complete blood count', 'A complete blood count, also known as a full blood count or full haemogram, is a set of medical laboratory tests that provide information about the cells in a person\'s blood. The CBC indicates the counts of white blood cells, red blood cells and platelets, the concentration of hemoglobin, and the hematocrit.', 1, '2025-10-17 11:15:22', '2025-11-14 04:38:15'),
(3, 'Lipid panel', 'A lipid profile or lipid panel is a panel of blood tests used to find abnormalities in blood lipid concentrations. The results of this test can identify certain genetic diseases and can determine approximate risks for cardiovascular disease, certain forms of pancreatitis, and other diseases.', 1, '2025-10-23 07:22:34', '2025-11-14 04:38:22'),
(4, 'Thyroid function tests', 'Thyroid function tests is a collective term for blood tests used to check the function of the thyroid. TFTs may be requested if a patient is thought to suffer from hyperthyroidism or hypothyroidism, or to monitor the effectiveness of either thyroid-suppression or hormone replacement therapy.', 1, '2025-10-28 01:45:27', '2025-11-14 04:38:27'),
(5, 'Liver panel', 'Liver function tests, also referred to as a hepatic panel or liver panel, are groups of blood tests that provide information about the state of a patient\'s liver. These tests include prothrombin time, activated partial thromboplastin time, albumin, bilirubin, and others.', 1, '2025-10-28 01:45:53', '2025-11-14 04:38:32'),
(6, 'Basic metabolic panel', 'A basic metabolic panel is a blood test consisting of a set of seven or eight biochemical tests and is one of the most common lab tests ordered by health care providers', 1, '2025-10-28 01:46:19', '2025-11-14 04:38:38'),
(7, 'Basic metabolic panel', 'A basic metabolic panel is a blood test consisting of a set of seven or eight biochemical tests and is one of the most common lab tests ordered by health care providers.', 1, '2025-10-28 01:47:04', '2025-10-28 01:47:04'),
(8, 'Erythrocyte sedimentation rate', 'The erythrocyte sedimentation rate is the rate at which red blood cells in anticoagulated whole blood descend in a standardized tube over a period of one hour. It is a common hematology test, and is a non-specific measure of inflammation.', 1, '2025-10-28 01:47:31', '2025-10-28 01:47:31'),
(9, 'HbA1c test', 'Glycated hemoglobin, also called glycohemoglobin, is a form of hemoglobin that is chemically linked to a sugar. Most monosaccharides, including glucose, galactose, and fructose, spontaneously bond with hemoglobin when they are present in the bloodstream.', 1, '2025-10-28 01:48:10', '2025-10-28 01:48:10'),
(10, 'Cardiac biomarkers', 'Cardiac biomarkers. Abnormal levels of enzymes indicate a wide range of issues that may need further testing.', 1, '2025-10-28 01:48:38', '2025-10-28 01:48:38'),
(11, 'Coagulogram', 'Blood clotting tests are the tests used for diagnostics of the hemostasis system. Coagulometer is the medical laboratory analyzer used for testing of the hemostasis system. Modern coagulometers realize different methods of activation and observation of development of blood clots in blood or in blood plasma.', 1, '2025-10-28 02:02:08', '2025-10-28 02:02:08'),
(12, 'Blood glucose test', 'Many types of glucose tests exist and they can be used to estimate blood sugar levels at a given time or, over a longer period of time, to obtain average levels or to see how fast the body is able to normalize changed glucose levels. Eating food for example leads to elevated blood sugar levels.', 1, '2025-10-28 02:02:43', '2025-10-28 02:02:43'),
(13, 'Calcium blood test', 'Calcium blood test. The calcium blood test is conducted to assess the calcium levels in the bloodstream. Since calcium is a key mineral needed for healthy ...', 1, '2025-10-28 02:03:18', '2025-10-28 02:03:18'),
(14, 'Electrolyte panel', 'Electrolyte panel. This blood test helps measure the levels of different minerals in your body. Any imbalance in these levels may indicate problems with ...', 1, '2025-10-28 02:04:03', '2025-10-28 02:04:03'),
(15, 'Hematocrit', 'The hematocrit, also known by several other names, is the volume percentage of red blood cells in blood, measured as part of a blood test. The measurement depends on the number and size of red blood cells. It is normally 40.7–50.3% for males and 36.1–44.3% for females', 1, '2025-10-28 02:04:31', '2025-11-14 04:37:51'),
(16, 'KFT test', 'Book a lab/blood test online with PharmEasy Labs and get the convenience of home sample collection at affordable price with fast and accurate results.', 1, '2025-10-28 02:04:59', '2025-11-14 04:37:42'),
(17, 'Platelet', 'Platelets or thrombocytes are a part of blood whose function is to react to bleeding from blood vessel injury by clumping to form a blood clot. Platelets have no cell nucleus; they are fragments of cytoplasm from megakaryocytes which reside in bone marrow or lung tissue, and then enter the circulation.', 1, '2025-10-28 02:05:42', '2025-11-14 04:37:39'),
(18, 'Thyroid-stimulating hormone', 'Thyroid-stimulating hormone is a pituitary hormone that stimulates the thyroid gland to produce thyroxine, and then triiodothyronine which stimulates the metabolism of almost every tissue in the body.', 1, '2025-10-28 02:06:09', '2025-11-14 04:37:35'),
(19, 'Creatinine', '', 1, '2025-10-28 02:06:35', '2025-11-14 04:37:32'),
(20, 'D-dimer test', 'D-dimer test. The D-dimer test is a blood test that checks for, or monitors, blood-clotting problems. Find out what a positive result means for you.', 1, '2025-10-28 02:07:02', '2025-11-14 04:37:28'),
(21, 'Electrolytes', 'Other electrolytes and metabolites. edit · Electrolytes and metabolites: For iron and copper, some related proteins are also included. Test, Patient type, Lower ...', 1, '2025-10-28 02:07:25', '2025-11-14 04:37:25'),
(22, 'Folate test', 'Folate test. Folate is an important nutrient for making normal red blood cells. The folate test checks whether you have enough folate in your blood.', 1, '2025-10-28 02:07:53', '2025-11-14 04:37:14'),
(23, 'Gonorrhea test', 'Your doctor will perform follow-up tests to confirm a diagnosis. The Lifeforce Diagnostic is an at-home blood test designed to gather data on 40+ biomarkers ...', 1, '2025-10-28 02:08:24', '2025-11-14 04:37:58'),
(24, 'Hemoglobin', 'Hemoglobin is an iron-rich protein in red blood cells that carries oxygen. Hematocrit levels that are too high might mean you\'re dehydrated. Low hematocrit ...', 1, '2025-10-28 02:08:47', '2025-11-14 04:37:54'),
(25, 'MCV', 'The mean corpuscular volume, or mean cell volume, is a measure of the average volume of a red blood corpuscle. The measure is obtained by multiplying a volume of blood by the proportion of blood that is cellular, and dividing that product by the number of erythrocytes in that volume.', 1, '2025-10-28 02:09:17', '2025-11-14 04:37:05');

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
(2, NULL, 'Magna sint est vel', 'Qui praesentium eos', '1988-01-10 01:37:00', '1979-10-28 00:26:00', 1, 1, '2025-10-10 19:14:26', NULL),
(3, NULL, 'API Test Notice 1760266896536', 'This is a test notice created by the API test.', '2025-10-12 00:00:00', '2025-10-19 00:00:00', 1, 1, NULL, NULL),
(4, NULL, 'Test Notice 084539 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:53', NULL, 0, 1, NULL, NULL),
(5, NULL, 'Test Notice 084539 UPDATED', 'This is a test notice for duplicate prevention testing', '2025-09-16 06:45:53', NULL, 0, 1, NULL, NULL),
(6, NULL, 'Umakant', 'Test desc', '2025-09-16 06:45:53', '2025-11-29 05:48:00', 1, 1, NULL, '2025-11-16 06:07:02');

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
(426, NULL, 'Umakant Yadav', 'admin@iaem.net', '9453619260', 'df', 'Jaunpur Rd', 'Female', 33, 'Odit dolor elit asp', 'Years', 'P251116647321', NULL, NULL, 2),
(427, NULL, 'Umakant Yadav  yy', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(446, NULL, 'Umakant Yadav  sss', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(447, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(448, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(449, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(450, NULL, 'Umakant Yadav  hhh', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(451, NULL, 'ttt', '', '454554545454', NULL, 'derfg', NULL, 0, '', 'Years', '3433', NULL, NULL, 1),
(452, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(453, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(454, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(455, NULL, 'dfg', '', '5656565656', NULL, 'werf', NULL, 33, '', 'Years', '234567', NULL, NULL, 1),
(456, NULL, 'dfg', '', '5656565656', NULL, 'werf', NULL, 33, '', 'Years', '234567', NULL, NULL, 1),
(457, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(458, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(462, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(463, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(464, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(465, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(466, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(467, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(468, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(469, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(470, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(471, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(472, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(473, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(474, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(475, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(476, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(477, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(478, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(479, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(480, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(481, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(482, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(483, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(484, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(485, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(486, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(487, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(488, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(489, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1),
(490, NULL, 'James Sears', '', '5656565656', NULL, 'Vel nobis error corr', NULL, 10, '', 'Years', 'P642622065', NULL, NULL, 1),
(491, NULL, 'Naida Marquez', '', '5454455454', NULL, 'Voluptate quibusdam', NULL, 45, '', 'Years', 'P033679005', NULL, NULL, 1),
(492, NULL, 'Umakant Yadav', '', '9453619260', NULL, 'Jaunpur Rd', NULL, 33, '', 'Years', 'P251116647321', NULL, NULL, 1);

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
(4, '<-59381021.1571670.1763268037904@DCLACBSHOSTPRDAP03>', 'expense', '2025-11-17 06:24:31');

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
(2, 3, 'Website not working', 'Menu issue ', 'Medium', 'Pending', NULL, 'https://allcurepharmacys.com/', '[\"uploads\\/screenshots\\/1763382631_0_Screenshot 2025-11-17 180004.png\",\"uploads\\/screenshots\\/1763382631_1_WhatsApp Image 2025-11-17 at 13.11.52_96a0c113.jpg\",\"uploads\\/screenshots\\/1763382660_0_WhatsApp Image 2025-11-17 at 13.11.52_c70534d2.jpg\"]', '', '2025-11-17 12:21:35', '2025-11-17 12:31:00');

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
(1, 'umakant', '$2y$12$8RovPoAOxY30weFvoSKJD.aabD27dV8cHbqON2XTQ04x1fs/Tw1da', 'Umakant Yadav', 'umakant171991@gmail.com', 'master', 1, 'Pathology', '2025-09-26 10:12:24', '2025-11-17 17:36:58', '2025-10-26 10:12:00', '', '0000-00-00 00:00:00', '2025-09-26 04:42:48'),
(2, 'uma', '$2y$12$auavnwTI5hbfkyqCoavijO/i3diGYgDZqt58EzkY6ZkB6M9jTmD9e', 'Uma Yadav', 'umakant171992@gmail.com', 'user', 1, 'Pathology', '2025-09-26 10:13:58', '2025-11-17 14:02:35', '2025-12-30 23:59:00', '6dad141b199e8c2ae3021462459c23135244d408778939a4c33c4a969726fde7', '0000-00-00 00:00:00', '2025-10-18 05:36:53');

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
(2, 1, 'email_default_priority', 'normal', NULL, '2025-11-17 01:04:10', '2025-11-17 01:04:10'),
(3, 1, 'email_emails_per_page', '100', NULL, '2025-11-17 01:04:10', '2025-11-17 01:04:10'),
(4, 1, 'email_enable_imap', '1', NULL, '2025-11-17 01:04:10', '2025-11-17 01:04:10'),
(5, 1, 'email_enable_smtp', '1', NULL, '2025-11-17 01:04:10', '2025-11-17 01:04:10'),
(6, 1, 'email_auto_refresh', '1', NULL, '2025-11-17 01:04:10', '2025-11-17 01:04:10'),
(7, 1, 'email_mark_as_read', '0', NULL, '2025-11-17 01:04:10', '2025-11-17 01:04:10'),
(8, 1, 'email_show_notifications', '1', NULL, '2025-11-17 01:04:10', '2025-11-17 01:04:10'),
(9, 1, 'email_save_sent_copy', '1', NULL, '2025-11-17 01:04:10', '2025-11-17 01:04:10');

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
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1783;

--
-- AUTO_INCREMENT for table `email_signatures`
--
ALTER TABLE `email_signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `entry_tests`
--
ALTER TABLE `entry_tests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_clients`
--
ALTER TABLE `inventory_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inventory_expense`
--
ALTER TABLE `inventory_expense`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `inventory_income`
--
ALTER TABLE `inventory_income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `main_test_categories`
--
ALTER TABLE `main_test_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=493;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `processed_emails`
--
ALTER TABLE `processed_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
