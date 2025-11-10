-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 10, 2025 at 12:24 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `security`
--

-- --------------------------------------------------------

--
-- Table structure for table `items_safe`
--

CREATE TABLE `items_safe` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `items_safe`
--

INSERT INTO `items_safe` (`id`, `uuid`, `token_hash`, `token_expires_at`, `user_id`, `title`, `content`, `created_at`) VALUES
(2, 'eb9d5c09-1ce4-49c4-91bd-700f20076f49', '2d40f1c2e188b0d31cbdf682fe024ddac154dfec0ef1337d2dae328591bca6c3', NULL, 5, 'tessss', 'okeeeeeeee', '2025-11-09 17:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `items_vuln`
--

CREATE TABLE `items_vuln` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(4, 'admin', '$2y$10$H4qGmOMAF8gUpUKDxYy0fOep8m9fKk41t91M3RxFfw5wcZirAA/xO', 'admin', '2025-11-09 17:20:15'),
(5, 'test_user', '$2y$10$UzzgYq2pX5PDzP/v6U1A4uE2bqDODBBmZnkhktN9KJXcM1ssHsDjq', 'user', '2025-11-09 17:20:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items_safe`
--
ALTER TABLE `items_safe`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `items_vuln`
--
ALTER TABLE `items_vuln`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items_safe`
--
ALTER TABLE `items_safe`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items_vuln`
--
ALTER TABLE `items_vuln`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items_safe`
--
ALTER TABLE `items_safe`
  ADD CONSTRAINT `items_safe_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `items_vuln`
--
ALTER TABLE `items_vuln`
  ADD CONSTRAINT `items_vuln_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
