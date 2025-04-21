-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 21, 2025 at 05:18 PM
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
-- Database: `u555781181_Portal42025`
--
CREATE DATABASE IF NOT EXISTS `u555781181_Portal42025` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `u555781181_Portal42025`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Admin', 'admin@sot', '$2y$10$trpnpJfQeptQCaVheqv47uzr.FJkU/1VfuZKZl7NIxZjakF99FZua', '2025-04-18 12:08:43');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `emp_id` varchar(50) NOT NULL,
  `dob` date NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `offer_letter` varchar(255) DEFAULT NULL,
  `offer_accepted` tinyint(1) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `offer_status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `photo` varchar(255) DEFAULT NULL,
  `aadhar` varchar(255) DEFAULT NULL,
  `pan` varchar(255) DEFAULT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `relieving` varchar(255) DEFAULT NULL,
  `offer_response_date` datetime DEFAULT NULL,
  `reset_required` tinyint(4) DEFAULT 0,
  `password_raw` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `emp_id`, `dob`, `password`, `offer_letter`, `offer_accepted`, `last_login`, `created_at`, `offer_status`, `photo`, `aadhar`, `pan`, `resume`, `education`, `experience`, `relieving`, `offer_response_date`, `reset_required`, `password_raw`) VALUES
(1, 'Test Employee', 'test@example.com', 'ASOT0142', '1990-01-01', '$2y$10$knxUnvr461ytXRtAUEb7xurgxfl4rrRRq0dwBWoqRG49lO5sL6KxK', '../uploads/offer_letters/1744973323_Quest Global Employment Offer - K Sree Charan.pdf', 0, '2025-04-18 22:07:22', '2025-04-17 12:41:03', 'accepted', '../uploads/documents/photos/1744898099_dark-logo.png', '../uploads/documents/aadhar/1744898099_dark-logo.png', '../uploads/documents/pan/1744898099_dark-logo.png', '../uploads/documents/resumes/1744898099_Fullstack Development Notes.pdf', '../uploads/documents/education/1744898099_Exp_Letter_ITS45036.pdf', '../uploads/documents/experience/1744898099_Exp_Letter_ITS45036.pdf', '../uploads/documents/relieving/1744898099_Exp_Letter_ITS45036.pdf', NULL, 0, NULL),
(28, 'Pooja', 'pooja@gmail.com', 'ASOT0143', '2000-07-14', '$2y$10$EDm9zV1n6dNlfsMH7bXS5.obnPBfRIRMOy5VEPToI6pgToyO6ToxC', NULL, 0, NULL, '2025-04-19 16:10:11', 'pending', '../uploads/documents/photos/1745079091_dark-logo.png', '../uploads/documents/aadhar/1745079091_Exp_Letter_ITS45036.pdf', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `reset_by` int(11) NOT NULL,
  `new_password` varchar(255) DEFAULT NULL,
  `reset_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `emp_id` (`emp_id`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `reset_by` (`reset_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `employee_documents_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `password_resets_ibfk_2` FOREIGN KEY (`reset_by`) REFERENCES `admins` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
