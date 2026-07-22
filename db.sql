-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Jul 22, 2026 at 12:38 PM
-- Server version: 10.11.14-MariaDB-ubu2204-log
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$HQaIyqopUzjIkdMfnp6ZjO9pNwk/fDXhpLq0GktuxeTdzfmKYocyq', '2026-07-21 09:51:47');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `is_probation` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `is_probation`) VALUES
(1, 'Front end', 0),
(2, 'Back end', 0),
(3, 'HR', 0),
(4, 'QA tester', 0),
(5, 'App development', 1),
(6, 'Game development', 1),
(7, 'DEVOPS', 1),
(8, 'Architecturer', 1),
(9, 'Digital marketting', 0);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `department_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `joining_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_name`, `email`, `department_id`, `password`, `joining_date`, `created_at`) VALUES
(1, 'Aarav Patel', 'aarav.patel@example.com', 1, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-01-10', '2026-07-22 10:46:50'),
(2, 'Vivaan Shah', 'vivaan.shah@example.com', 2, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-02-15', '2026-07-22 10:46:50'),
(3, 'Aditya Mehta', 'aditya.mehta@example.com', 3, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-03-20', '2026-07-22 10:46:50'),
(4, 'Krish Desai', 'krish.desai@example.com', 4, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-04-12', '2026-07-22 10:46:50'),
(5, 'Dhruv Joshi', 'dhruv.joshi@example.com', 5, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-05-08', '2026-07-22 10:46:50'),
(6, 'Aryan Trivedi', 'aryan.trivedi@example.com', 6, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-06-18', '2026-07-22 10:46:50'),
(7, 'Kabir Bhatt', 'kabir.bhatt@example.com', 7, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-07-22', '2026-07-22 10:46:50'),
(8, 'Rohan Dave', 'rohan.dave@example.com', 8, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-08-05', '2026-07-22 10:46:50'),
(9, 'Yash Modi', 'yash.modi@example.com', 9, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-09-14', '2026-07-22 10:46:50'),
(10, 'Harsh Pandya', 'harsh.pandya@example.com', 1, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-10-01', '2026-07-22 10:46:50'),
(11, 'Nisarg Rana', 'nisarg.rana@example.com', 2, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-11-11', '2026-07-22 10:46:50'),
(12, 'Meet Vyas', 'meet.vyas@example.com', 3, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2023-12-03', '2026-07-22 10:46:50'),
(13, 'Parth Soni', 'parth.soni@example.com', 4, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2024-01-16', '2026-07-22 10:46:50'),
(14, 'Jay Gohil', 'jay.gohil@example.com', 5, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2024-02-09', '2026-07-22 10:46:50'),
(15, 'Karan Solanki', 'karan.solanki@example.com', 6, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2024-03-21', '2026-07-22 10:46:50'),
(16, 'Manav Chauhan', 'manav.chauhan@example.com', 7, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2024-04-17', '2026-07-22 10:46:50'),
(17, 'Tanish Patel', 'tanish.patel@example.com', 8, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2024-05-29', '2026-07-22 10:46:50'),
(18, 'Om Thakkar', 'om.thakkar@example.com', 9, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2024-06-13', '2026-07-22 10:46:50'),
(19, 'Dev Parmar', 'dev.parmar@example.com', 1, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2024-07-04', '2026-07-22 10:46:50'),
(20, 'Rahul Makwana', 'rahul.makwana@example.com', 2, '$2y$10$0Kcw6/HBua5d33ggmeIPquCBdSy5a3gpRQHqrdUCeOLY3p.jcdzc.', '2024-08-20', '2026-07-22 10:46:50');

-- --------------------------------------------------------

--
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type` enum('Sick','Casual','Paid') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaves`
--

INSERT INTO `leaves` (`id`, `employee_id`, `leave_type`, `start_date`, `end_date`, `reason`, `status`, `rejection_reason`, `created_at`) VALUES
(1, 1, 'Sick', '2026-08-01', '2026-08-01', 'Medical appointment', 'Approved', NULL, '2026-07-22 10:52:26'),
(2, 1, 'Casual', '2026-08-02', '2026-08-02', 'Personal work', 'Rejected', 'work remianing', '2026-07-22 10:52:26'),
(3, 1, 'Sick', '2026-08-03', '2026-08-03', 'Fever', 'Approved', NULL, '2026-07-22 10:52:26'),
(4, 1, 'Paid', '2026-08-04', '2026-08-04', 'Family function', 'Approved', NULL, '2026-07-22 10:52:26'),
(5, 1, 'Casual', '2026-08-05', '2026-08-05', 'Personal work', 'Approved', NULL, '2026-07-22 10:52:26'),
(6, 1, 'Sick', '2026-08-06', '2026-08-06', 'Health checkup', 'Approved', NULL, '2026-07-22 10:52:26'),
(7, 1, 'Paid', '2026-08-07', '2026-08-07', 'Vacation', 'Approved', NULL, '2026-07-22 10:52:26'),
(8, 1, 'Casual', '2026-08-08', '2026-08-08', 'Personal work', 'Approved', NULL, '2026-07-22 10:52:26'),
(9, 1, 'Sick', '2026-08-09', '2026-08-09', 'Migraine', 'Approved', NULL, '2026-07-22 10:52:26'),
(10, 1, 'Paid', '2026-08-10', '2026-08-10', 'Festival', 'Approved', NULL, '2026-07-22 10:52:26'),
(11, 1, 'Casual', '2026-08-11', '2026-08-11', 'Urgent work', 'Approved', NULL, '2026-07-22 10:52:26'),
(12, 2, 'Casual', '2026-08-01', '2026-08-01', 'Personal work', 'Pending', NULL, '2026-07-22 10:52:26'),
(13, 2, 'Sick', '2026-08-02', '2026-08-02', 'Fever', 'Pending', NULL, '2026-07-22 10:52:26'),
(14, 2, 'Paid', '2026-08-03', '2026-08-03', 'Family event', 'Pending', NULL, '2026-07-22 10:52:26'),
(15, 2, 'Casual', '2026-08-04', '2026-08-04', 'Bank work', 'Pending', NULL, '2026-07-22 10:52:26'),
(16, 2, 'Sick', '2026-08-05', '2026-08-05', 'Cold', 'Pending', NULL, '2026-07-22 10:52:26'),
(17, 1, 'Casual', '2026-07-22', '2026-07-31', 'sick', 'Approved', NULL, '2026-07-22 10:59:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_department_id` (`department_id`);

--
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_employees_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_department_id` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `fk_employees_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
