-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: May 27, 2024 at 05:37 PM
-- Server version: 8.0.32
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agri_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `middle_name` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `role_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `first_name`, `middle_name`, `last_name`, `email`, `role_id`) VALUES
(1, 'billyjay', 'billy123', 'Billy', NULL, 'Menguito', 'billy.watapampa@sample.com', 2),
(2, 'boloybay', 'boloy123', 'Boloy', '', 'Bayo', 'boloybay@sample.com', 2),
(3, 'kumalon', 'kum123', 'kumlay', '', 'kumlon', 'kumalon@sample.com', 2),
(4, 'jaero', 'naruto123', 'jaero', '', 'cabayot', 'jaerocabayot@sample.com', 2),
(5, 'ally123', 'ally123', 'Ally', '', 'Parcon', 'allyparcon@sample.com', 2),
(6, 'dannn', 'dan123', 'Dan', 'dan', 'dan', 'dan@example.com', 2);

-- --------------------------------------------------------

--
-- Table structure for table `barangay`
--

CREATE TABLE `barangay` (
  `id` int NOT NULL,
  `barangay_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay`
--

INSERT INTO `barangay` (`id`, `barangay_name`) VALUES
(1, 'Lagao'),
(2, 'San Isidro'),
(3, 'Bula'),
(4, 'Apopong'),
(5, 'Baluan'),
(6, 'Bula'),
(7, 'City Heights'),
(8, 'Conel'),
(9, 'Dadiangas East'),
(10, 'Dadiangas West'),
(11, 'Dadiangas South'),
(12, 'Fatima'),
(13, 'Katangawan'),
(14, 'Lagao'),
(15, 'Ligaya'),
(16, 'Mabuhay'),
(17, 'Olympog'),
(18, 'San Isidro'),
(19, 'San Jose'),
(20, 'Tinagacan');

-- --------------------------------------------------------

--
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` int NOT NULL,
  `crop_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`id`, `crop_name`) VALUES
(1, 'Corn'),
(2, 'Rice');

-- --------------------------------------------------------

--
-- Table structure for table `crop_variance`
--

CREATE TABLE `crop_variance` (
  `id` int NOT NULL,
  `variance_name` varchar(255) NOT NULL,
  `crop_id` int NOT NULL,
  `price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `crop_variance`
--

INSERT INTO `crop_variance` (`id`, `variance_name`, `crop_id`, `price`) VALUES
(1, '480', 2, 35),
(2, '402', 2, 35),
(3, '350', 2, 35),
(4, '216', 2, 35),
(5, '222', 2, 35),
(6, '160', 2, 35),
(7, '440', 2, 35),
(8, '506', 2, 35),
(9, '400', 2, 35),
(10, '442', 2, 35),
(11, 'yellow', 1, 35),
(12, 'white', 1, 35),
(13, 'sweet corn', 1, 35),
(14, 'Glutinous', 1, 35);

-- --------------------------------------------------------

--
-- Table structure for table `farmers`
--

CREATE TABLE `farmers` (
  `id` int NOT NULL,
  `rsbsa_num` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `middle_name` varchar(25) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `area` double(5,1) NOT NULL,
  `crop_id` int NOT NULL,
  `barangay_id` int DEFAULT NULL,
  `role_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `farmers`
--

INSERT INTO `farmers` (`id`, `rsbsa_num`, `password`, `first_name`, `middle_name`, `last_name`, `contact_number`, `area`, `crop_id`, `barangay_id`, `role_id`) VALUES
(1, '000_0000_001', 'billy123', 'Billy Joe', NULL, 'Mengits', '092255336871', 2.1, 1, 1, 1),
(2, '000_0000_002', 'baymax123', 'Bay', NULL, 'Max', '092255336871', 5.0, 2, 2, 1),
(5, '000_0000_003', 'emman123', 'Emmanuel', '', 'Malagamba', '12345678910', 2.1, 1, 2, 1),
(6, '000_0000_004', 'yot123', 'Yot', '', 'Bay', '12345678910', 4.5, 2, 3, 1),
(7, '000_0000_005', '123', 'jaero', '', 'cabayot', '04040400404', 4.6, 2, 3, 1),
(8, '000_0000_006', '123', 'gemuel', '', 'catubig', '09070605121', 6.7, 2, 1, 1),
(9, '000_0000_007', '123', 'Allysa', '', 'Parcon', '09090909090', 9.9, 2, 2, 1),
(10, '000_0000_008', '123', 'lagkit', '', 'makapilit', '09870007851', 6.8, 1, 1, 1),
(11, '000_0000_009', '123', 'ungkoy', '', 'curacotdacot', '09125162393', 6.9, 2, 2, 1),
(12, '000_0000_010', '123', 'miling', '', 'pastil', '09601325637', 9.1, 1, 3, 1),
(13, '000_0000_011', '123', 'sheesh', '', 'falbon', '09837561907', 2.8, 2, 1, 1),
(14, '000_0000_012', '123', 'king', '', 'yasmin', '09005738415', 3.8, 1, 2, 1),
(15, '000_0000_013', '123', 'baby', '', 'sugar', '09561745859', 7.1, 2, 3, 1),
(16, '000_0000_014', '123', 'arji', '', 'dane', '09571827586', 1.1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `farmer_planted`
--

CREATE TABLE `farmer_planted` (
  `id` int NOT NULL,
  `farmer_id` int NOT NULL,
  `date_planted` date NOT NULL,
  `isHarvested` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `farmer_planted`
--

INSERT INTO `farmer_planted` (`id`, `farmer_id`, `date_planted`, `isHarvested`) VALUES
(1, 1, '2024-05-27', 1);

-- --------------------------------------------------------

--
-- Table structure for table `farmer_tokens`
--

CREATE TABLE `farmer_tokens` (
  `id` int NOT NULL,
  `farmer_id` int NOT NULL,
  `remember_token` text NOT NULL,
  `date_login` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `farmer_tokens`
--

INSERT INTO `farmer_tokens` (`id`, `farmer_id`, `remember_token`, `date_login`) VALUES
(4, 1, '3dbdda1729e489a69041dac4e06db40e', '2024-05-27 14:01:14');

-- --------------------------------------------------------

--
-- Table structure for table `farmer_variance`
--

CREATE TABLE `farmer_variance` (
  `id` int NOT NULL,
  `farmer_id` int NOT NULL,
  `variance_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `harvests`
--

CREATE TABLE `harvests` (
  `id` int NOT NULL,
  `farmer_id` int DEFAULT NULL,
  `crop_id` int DEFAULT NULL,
  `date_harvested` datetime DEFAULT NULL,
  `estimated_produce` double NOT NULL,
  `estimated_income` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `harvests`
--

INSERT INTO `harvests` (`id`, `farmer_id`, `crop_id`, `date_harvested`, `estimated_produce`, `estimated_income`) VALUES
(1, 1, 1, '2023-04-03 23:49:38', 0, 0),
(2, 1, 1, '2023-05-10 23:49:38', 500, 0),
(3, 1, 1, '2023-05-08 23:49:38', 0, 0),
(4, 2, 2, '2024-01-02 23:49:38', 0, 0),
(5, 1, 2, '2024-01-04 23:49:38', 0, 0),
(6, 2, 2, '2024-02-13 23:49:38', 0, 0),
(7, 1, 1, '2024-02-12 23:49:38', 0, 0),
(8, 1, 1, '2024-02-29 23:49:38', 0, 0),
(9, 1, 1, '2024-02-29 23:49:38', 0, 0),
(10, 2, 2, '2024-03-20 23:49:38', 50000, 0),
(11, 1, 1, '2024-06-12 01:13:54', 0, 0),
(12, 2, 2, '2023-03-08 01:22:23', 0, 0),
(13, 7, 2, '2023-09-20 09:43:13', 0, 0),
(14, 8, 2, '2024-10-15 09:49:50', 0, 0),
(16, 1, 1, '2024-05-27 15:36:38', 10500, 378000);

-- --------------------------------------------------------

--
-- Table structure for table `pests_reports`
--

CREATE TABLE `pests_reports` (
  `id` int NOT NULL,
  `farmer_id` int NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `pest_name` varchar(255) NOT NULL,
  `date_reported` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pests_reports`
--

INSERT INTO `pests_reports` (`id`, `farmer_id`, `img_path`, `pest_name`, `date_reported`) VALUES
(1, 1, 'pest_images/000_0000_001/f65dd24074e9792da2525257c290e252.jpg', 'Pesting Yawa', '2024-05-27 14:44:09'),
(2, 1, 'pest_images/000_0000_001/d7317a08f0ed8068459b2566a989ea49.jpg', 'Billy Pest', '2024-05-27 14:44:50'),
(3, 1, 'pest_images/000_0000_001/7bf1d390c87c986ec927c5e4c4f8fce5.jpg', 'Billy Pest', '2024-05-27 16:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_levels`
--

CREATE TABLE `user_levels` (
  `id` int NOT NULL,
  `role` varchar(25) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_levels`
--

INSERT INTO `user_levels` (`id`, `role`) VALUES
(1, 'Farmer'),
(2, 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role` (`role_id`);

--
-- Indexes for table `barangay`
--
ALTER TABLE `barangay`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crop_variance`
--
ALTER TABLE `crop_variance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_type` (`crop_id`);

--
-- Indexes for table `farmers`
--
ALTER TABLE `farmers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rsbsa_num` (`rsbsa_num`),
  ADD KEY `barangay` (`barangay_id`),
  ADD KEY `role` (`role_id`),
  ADD KEY `crops` (`crop_id`);

--
-- Indexes for table `farmer_planted`
--
ALTER TABLE `farmer_planted`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `farmer_tokens`
--
ALTER TABLE `farmer_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `harvests`
--
ALTER TABLE `harvests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer` (`farmer_id`),
  ADD KEY `crop` (`crop_id`);

--
-- Indexes for table `pests_reports`
--
ALTER TABLE `pests_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `farmer_id` (`farmer_id`);

--
-- Indexes for table `user_levels`
--
ALTER TABLE `user_levels`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `barangay`
--
ALTER TABLE `barangay`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `crop_variance`
--
ALTER TABLE `crop_variance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `farmers`
--
ALTER TABLE `farmers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `farmer_planted`
--
ALTER TABLE `farmer_planted`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `farmer_tokens`
--
ALTER TABLE `farmer_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `harvests`
--
ALTER TABLE `harvests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pests_reports`
--
ALTER TABLE `pests_reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_levels`
--
ALTER TABLE `user_levels`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `user_levels` (`id`);

--
-- Constraints for table `crop_variance`
--
ALTER TABLE `crop_variance`
  ADD CONSTRAINT `crop_variance_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `farmers`
--
ALTER TABLE `farmers`
  ADD CONSTRAINT `farmers_ibfk_1` FOREIGN KEY (`barangay_id`) REFERENCES `barangay` (`id`),
  ADD CONSTRAINT `farmers_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `user_levels` (`id`),
  ADD CONSTRAINT `farmers_ibfk_3` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`);

--
-- Constraints for table `farmer_planted`
--
ALTER TABLE `farmer_planted`
  ADD CONSTRAINT `farmer_planted_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `harvests`
--
ALTER TABLE `harvests`
  ADD CONSTRAINT `harvests_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`),
  ADD CONSTRAINT `harvests_ibfk_2` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`);

--
-- Constraints for table `pests_reports`
--
ALTER TABLE `pests_reports`
  ADD CONSTRAINT `pests_reports_ibfk_1` FOREIGN KEY (`farmer_id`) REFERENCES `farmers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
