-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 08, 2025 at 06:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `budget_tracker_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `amount`, `category`, `description`, `transaction_date`, `created_at`, `is_deleted`) VALUES
(2, 1, 'income', 240000.00, '', 'Salary', '0000-00-00', '2025-05-05 07:01:57', 0),
(4, 1, 'expense', 1500.00, '', 'Travelling Fee', '0000-00-00', '2025-05-05 07:17:56', 1),
(6, 1, 'expense', 80000.00, '', 'Domestics', '0000-00-00', '2025-05-05 07:51:10', 1),
(7, 1, 'expense', 70000.00, '', 'Domestics', '0000-00-00', '2025-05-05 08:33:35', 1),
(8, 1, 'expense', 80000.00, '', 'Domestics', '0000-00-00', '2025-05-05 08:36:34', 0),
(9, 1, 'expense', 1500.00, '', 'Travelling Fee', '0000-00-00', '2025-05-05 08:37:38', 0),
(10, 3, 'expense', 10000.00, '', 'Lunch', '0000-00-00', '2025-05-08 14:50:11', 0),
(11, 3, 'income', 500000.00, '', 'Salary', '0000-00-00', '2025-05-08 14:50:34', 0),
(12, 4, 'income', 175757.00, '', 'Domestics', '0000-00-00', '2025-05-11 10:39:07', 0),
(13, 4, 'expense', 534536.00, '', 'Travelling Fee', '0000-00-00', '2025-05-11 10:39:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Chris', 'chris@gmail.com', '$2y$10$CsW/UW6epAGr1KneeUSZcuGv6u4mENnrr8QumzkCNg26DBcUh4AMm', 'user', '2025-05-05 06:46:51'),
(2, 'Layla', 'layla@gmail.com', '$2y$10$0ggrEUYoxnum0aHrCcZdgeEWRMPfEkaNr6qK2pE4VMvTd30eGfzEm', 'user', '2025-05-05 07:03:02'),
(3, 'Seint Lae Kyi', 'seintlaekyi@gmail.com', '$2y$10$OT4rSeB5csmsaUTF5FVDO.d6VXBhHjAOgl7FUKTEXQlHd6DhJ3DKG', 'user', '2025-05-08 14:49:25'),
(4, 'ccc', 'ccc@gmail.com', '$2y$10$YJkOBG5gAXzbb2PwPbizRe.XpbBuf4DRhzKJ8gBl84C1K3kbFdWV.', 'user', '2025-05-11 10:38:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
