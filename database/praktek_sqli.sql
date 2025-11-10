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
-- Database: `praktek_sqli`
--

-- --------------------------------------------------------

--
-- Table structure for table `users_safe`
--

CREATE TABLE `users_safe` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_safe`
--

INSERT INTO `users_safe` (`id`, `username`, `password_hash`, `full_name`, `created_at`) VALUES
(1, 'aurellia01', '$2y$10$J8hK6QwQYdQxFfF89x7e6eEoV3mQ7VQ1vWbF3b2qZ9tX5t9aPzZu', 'Aurellia Salsabila', '2025-11-09 10:55:48'),
(2, 'admin_unimus', '$2y$10$E6j3T8lYhV3dTnP5wWbF8mEoV4rN6aK9qT6aF2zC8sU4rV1pN3r9Gq', 'Admin Sistem', '2025-11-09 10:55:48'),
(3, 'user_broadcast', '$2y$10$Q3rK2sN9eT6mWfD7pB1xR9yEoU4hV6aC8tZ3nR5lJ7mV2uL1bN8qC', 'Sava Aurellia', '2025-11-09 10:55:48'),
(4, 'user_01', '$2y$10$S5/8X2UbEXBkmVSlURmMB.U3vIdmFODUHZfgcPTTIl.Be4RbUm5I6', 'Sava Aurellia', '2025-11-09 11:11:34');

-- --------------------------------------------------------

--
-- Table structure for table `users_vul`
--

CREATE TABLE `users_vul` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users_vul`
--

INSERT INTO `users_vul` (`id`, `username`, `password`, `full_name`, `created_at`) VALUES
(1, 'danang', 'admin123', 'danang sadewa', '2025-10-07 07:16:51'),
(2, 'wijayato', 'rita 123', 'rita wijayato', '2025-10-07 07:17:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users_safe`
--
ALTER TABLE `users_safe`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users_vul`
--
ALTER TABLE `users_vul`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users_safe`
--
ALTER TABLE `users_safe`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users_vul`
--
ALTER TABLE `users_vul`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
