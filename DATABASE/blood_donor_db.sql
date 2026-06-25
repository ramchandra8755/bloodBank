-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 09, 2025 at 04:51 AM
-- Server version: 8.0.40
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blood_donor_db`
--
DROP PROCEDURE IF EXISTS update_donor_rewards;
DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_donor_rewards` (IN `donor_id` INT)   BEGIN
    DECLARE total_donations INT;
    DECLARE total_points INT;
    DECLARE current_level VARCHAR(20);
    
    -- Get total donations
    SELECT COUNT(*) INTO total_donations
    FROM donation_history
    WHERE donor_id = donor_id;
    
    -- Calculate base points (100 per donation)
    SET total_points = total_donations * 100;
    
    -- Determine level
    SET current_level = CASE
        WHEN total_points >= 5000 THEN 'Diamond'
        WHEN total_points >= 2000 THEN 'Platinum'
        WHEN total_points >= 1000 THEN 'Gold'
        WHEN total_points >= 500 THEN 'Silver'
        ELSE 'Bronze'
    END;
    
    -- Update or insert rewards
    INSERT INTO donor_rewards (donor_id, points, level, total_donations, last_updated)
    VALUES (donor_id, total_points, current_level, total_donations, NOW())
    ON DUPLICATE KEY UPDATE
        points = total_points,
        level = current_level,
        total_donations = total_donations,
        last_updated = NOW();
        
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int NOT NULL,
  `donor_id` int DEFAULT NULL,
  `blood_bank_id` int DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `time_slot` time DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled','Missed') DEFAULT 'Scheduled',
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `donor_id`, `blood_bank_id`, `appointment_date`, `time_slot`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 2, '2025-04-10', '09:30:00', 'Scheduled', 'i dont have much time pls do fast', '2025-01-02 15:40:58', '2025-01-02 15:40:58'),
(2, 3, 5, '2025-01-10', '09:00:00', 'Scheduled', '', '2025-01-02 15:54:37', '2025-01-02 16:27:13'),
(6, 6, 5, '2025-01-02', '16:30:00', 'Completed', 'do', '2025-01-02 16:29:36', '2025-01-02 16:32:30'),
(7, 7, 5, '2025-01-02', '16:30:00', 'Completed', 'hello', '2025-01-02 16:35:23', '2025-01-02 16:35:50'),
(8, 8, 5, '2025-01-02', '16:30:00', 'Completed', 'pls do at 7pm', '2025-01-02 16:45:18', '2025-01-02 16:45:39'),
(9, 9, 5, '2025-01-02', '13:30:00', 'Missed', '123', '2025-01-02 16:47:11', '2025-01-02 16:47:22'),
(10, 9, 5, '2025-01-03', '10:30:00', 'Scheduled', '', '2025-01-02 16:48:02', '2025-01-02 16:48:02'),
(11, 10, 5, '2025-01-03', '09:00:00', 'Cancelled', 'hello', '2025-01-02 19:12:29', '2025-01-02 19:12:36'),
(12, 10, 5, '2025-01-03', '09:30:00', 'Completed', 'time changed', '2025-01-02 19:12:54', '2025-01-02 19:13:17'),
(13, 13, 5, '2025-01-20', '10:00:00', 'Completed', '', '2025-01-18 07:42:36', '2025-01-18 07:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `auth_tokens`
--

INSERT INTO `auth_tokens` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(1, 2, '895a9cab9d30cdd33b552fc29ddcfd35c4c73c030292a91c119ff8f9fd66bdf8', '2025-02-01 07:14:26', '2025-01-02 12:44:26'),
(2, 2, '563ba9de5ff4b6f181abd41aeb5a151d68638522c03a91f8c659d6e2d2ba62b6', '2025-02-01 07:17:18', '2025-01-02 12:47:18'),
(3, 2, '0689151479aaca989d6916afdd3b8f5e73f5a04a0327026dc0119dec8d91a8bf', '2025-02-01 07:57:10', '2025-01-02 13:27:10'),
(4, 2, '158c24056e1db2e56d9e8ae786ba04283e5d23106697ed9de742e482046f2ec3', '2025-02-01 08:07:19', '2025-01-02 13:37:19'),
(5, 2, '7df0088cae759fa96d60289095341a0641bfa2a2db64b998e2b0554a1a525f80', '2025-02-01 08:07:58', '2025-01-02 13:37:58'),
(6, 2, 'e75b02520063ff7dc6cd290253f4610e78810bf410a31dd020b85a84d4396384', '2025-02-01 08:08:03', '2025-01-02 13:38:03'),
(7, 2, '04ca52be0803fcb039f003ef3ad78468dda071ccdfa5a0490dee0c1ea43604bf', '2025-02-01 08:08:18', '2025-01-02 13:38:18'),
(8, 2, 'dfa730fc30d9bc60f04661d5e25c880064595e58da96ca5add3f671f6e668c8b', '2025-02-01 08:08:42', '2025-01-02 13:38:42'),
(9, 2, '9307fb4b1f19ddafbaa29a46da57a7c3ed8ac52c289ef3cb55f8d13938097832', '2025-02-01 08:08:57', '2025-01-02 13:38:57'),
(10, 2, '4f20dc4341de0f2ec2fc6fb877695694e2a11435587e5d10a1b2fffec04a3ac8', '2025-02-01 08:15:16', '2025-01-02 13:45:16'),
(11, 2, '339b656d994bbe4b81da5460466c5db4c736171e8ba7981d66161eceb50a8ba9', '2025-02-01 10:21:52', '2025-01-02 15:51:52'),
(12, 3, 'af94c3482c4a5e1529f376cc67a09da6f6a00fb9ee375bf20890150ed8ca5cf2', '2025-02-01 10:22:42', '2025-01-02 15:52:42'),
(13, 2, '234717f824d124bda1e255b75e11aae97892baa028fbe9da9f6c60982ca5ba0c', '2025-02-01 10:26:33', '2025-01-02 15:56:33'),
(14, 6, '4e417803694f6cc65052d530866f4cd680a9139acb7b9786f7aa3068e9f71784', '2025-02-01 10:59:17', '2025-01-02 16:29:17'),
(15, 7, 'eb0ad7db23cd35381fd446d6948fcc9c7f4e5a6ff6e6b6d586fb770950d50765', '2025-02-01 11:04:53', '2025-01-02 16:34:53'),
(16, 8, 'c63464a09f960386e77f61f20078ac6544cbfc4c17cc96ddf077e5391bc3d62f', '2025-02-01 11:14:53', '2025-01-02 16:44:53'),
(17, 9, 'e18f4454cbaa8f277595029518bc0d8f5f3498e6e06a69311d3cf724399f3e9a', '2025-02-01 11:17:00', '2025-01-02 16:47:00'),
(18, 2, '4ace8817360c8ae1895b775e5e63fb345e00a008bb843d5d9c3c0fc3e3b815c9', '2025-02-01 11:56:22', '2025-01-02 17:26:22'),
(19, 2, '912259f503abda564fbabd5b33cd3bfcba20d12da35008368732dd802e718054', '2025-02-01 12:04:45', '2025-01-02 17:34:45'),
(20, 3, '59732d2ce72a2343c57ea13705c478104d636dfc7f6ca1fe4d79a7eb2301e78e', '2025-02-01 12:53:47', '2025-01-02 18:23:47'),
(21, 10, '1d52e45ddf9c1e45019d92e8811fae5aa5b08b493200802f7943ae6cc5292e05', '2025-02-01 13:22:59', '2025-01-02 18:52:59'),
(22, 2, '2bac41a723d2374aab460d7234ac2091ecbb7e087ce0c6b396278e16200c891d', '2025-02-01 13:49:12', '2025-01-02 19:19:12'),
(23, 2, '428b77aa4e63c8c0e7935864a598de73380cb451d0814d48550c41a3c6c887b0', '2025-02-01 13:52:42', '2025-01-02 19:22:42'),
(24, 2, 'ab7c8ca9bb23979cfd3a71b7cac0b13382f415d19e9dafcb30aa4d03850da77d', '2025-02-01 14:01:20', '2025-01-02 19:31:20'),
(25, 2, '388bf0f852a1f166c786735bff25e8670c3fe5de6008864327b1eead9e60f6d6', '2025-02-01 14:03:10', '2025-01-02 19:33:10'),
(26, 2, '0d6f8cfb1b8d86ed58efe3eb5de24fc50ffd72f55d4e800caba3ccf9a9732269', '2025-02-01 14:08:41', '2025-01-02 19:38:41'),
(27, 11, '5986bbe0056c5c1d78351699e7d219c8e10e2fb9fab7f3d31c2fb433d7fae8e9', '2025-02-01 14:15:24', '2025-01-02 19:45:24'),
(28, 2, 'c6414cf72cee15d9a59e30db192d0a0ebc1f9ffa4b6421246bdd5c867546b718', '2025-02-01 14:33:28', '2025-01-02 20:03:28'),
(29, 2, '8b5d428ed4c9206e30efc6cf9b78c090a9a7733fa7f17da280887816411fb0f0', '2025-02-01 14:41:33', '2025-01-02 20:11:33'),
(30, 2, '61d994526383479e484c33b5859fa19c8967130d931104f6293bdd17ce180243', '2025-02-01 14:44:29', '2025-01-02 20:14:29'),
(31, 2, '07fda0263c57de5ec9ee37467f9625210df14066fb1560f8c422c8b052adda5b', '2025-02-01 14:45:15', '2025-01-02 20:15:15'),
(32, 2, 'c65c4934259ef8ce3c1f222facefa91b3174a5db18a76c5e2064c2c238af276f', '2025-02-01 14:47:28', '2025-01-02 20:17:28'),
(33, 2, '0846a9aa6265b465a761fac2a8ff49a2c0ef2973f4167c3081c2d05844519467', '2025-02-03 09:33:04', '2025-01-04 15:03:04'),
(34, 12, '3f6ef42732a785978e8b52f3205e2f669d5ac76a37956be65991c16381c371ed', '2025-02-03 09:37:21', '2025-01-04 15:07:21'),
(35, 2, '6d632241656c316108fc714c94eb8379eef228b3a5d96b8adc1e3045080b7796', '2025-02-03 23:35:52', '2025-01-05 05:05:52'),
(36, 2, 'be5e13280aaf9ab1d66056206abc9b00641d4ad523bfc7206247cf01e929c86c', '2025-02-04 03:04:29', '2025-01-05 08:34:29'),
(37, 2, '9776b3857627cd0dbbc8a1fac4ff5e2f2892692ffbb2257e532bac7017a8e554', '2025-02-04 03:20:38', '2025-01-05 08:50:38'),
(38, 13, '3e53c767d8437471b2028739d3a0c928201db8856ea9d95bd2ce1aee93b46dcc', '2025-02-17 01:48:40', '2025-01-18 07:18:40'),
(39, 13, 'c03535e7805ba2e5cd52a40b4aedc5d08a330f0c41e2decffda78d4c05a7705c', '2025-02-17 02:28:21', '2025-01-18 07:58:21');

-- --------------------------------------------------------

--
-- Table structure for table `available_slots`
--

CREATE TABLE `available_slots` (
  `id` int NOT NULL,
  `blood_bank_id` int DEFAULT NULL,
  `day_of_week` int DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `slot_duration` int DEFAULT NULL,
  `max_appointments` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `available_slots`
--

INSERT INTO `available_slots` (`id`, `blood_bank_id`, `day_of_week`, `start_time`, `end_time`, `slot_duration`, `max_appointments`) VALUES
(1, 1, 2, '09:00:00', '17:00:00', 30, 2),
(2, 1, 3, '09:00:00', '17:00:00', 30, 2),
(3, 1, 4, '09:00:00', '17:00:00', 30, 2),
(4, 1, 5, '09:00:00', '17:00:00', 30, 2),
(5, 1, 6, '09:00:00', '16:00:00', 30, 2),
(6, 2, 2, '08:00:00', '18:00:00', 30, 3),
(7, 2, 3, '08:00:00', '18:00:00', 30, 3),
(8, 2, 4, '08:00:00', '18:00:00', 30, 3),
(9, 2, 5, '08:00:00', '18:00:00', 30, 3),
(10, 2, 6, '08:00:00', '16:00:00', 30, 3),
(11, 2, 7, '09:00:00', '14:00:00', 30, 2),
(12, 3, 2, '10:00:00', '19:00:00', 30, 2),
(13, 3, 3, '10:00:00', '19:00:00', 30, 2),
(14, 3, 4, '10:00:00', '19:00:00', 30, 2),
(15, 3, 5, '10:00:00', '19:00:00', 30, 2),
(16, 3, 6, '10:00:00', '17:00:00', 30, 2),
(17, 4, 1, '09:00:00', '17:00:00', 30, 2),
(18, 4, 2, '09:00:00', '17:00:00', 30, 2),
(19, 4, 3, '09:00:00', '17:00:00', 30, 2),
(20, 4, 4, '09:00:00', '17:00:00', 30, 2),
(21, 4, 5, '09:00:00', '17:00:00', 30, 2),
(22, 5, 1, '09:00:00', '17:00:00', 30, 2),
(23, 5, 2, '09:00:00', '17:00:00', 30, 2),
(24, 5, 3, '09:00:00', '17:00:00', 30, 2),
(25, 5, 4, '09:00:00', '17:00:00', 30, 2),
(26, 5, 5, '09:00:00', '17:00:00', 30, 2),
(27, 5, 6, '09:00:00', '17:00:00', 30, 2),
(28, 6, 1, '09:00:00', '19:00:00', 30, 2),
(29, 6, 2, '09:00:00', '17:00:00', 30, 2),
(30, 6, 3, '09:00:00', '17:00:00', 30, 2),
(31, 6, 4, '09:00:00', '17:00:00', 30, 2),
(32, 6, 5, '09:00:00', '17:00:00', 30, 2),
(33, 6, 6, '09:00:00', '17:00:00', 30, 2),
(34, 6, 7, '09:00:00', '17:00:00', 30, 2);

-- --------------------------------------------------------

--
-- Table structure for table `blood_banks`
--

CREATE TABLE `blood_banks` (
  `id` int NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` text,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `operating_hours` text,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blood_banks`
--

INSERT INTO `blood_banks` (`id`, `name`, `address`, `contact_number`, `email`, `operating_hours`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'City General Hospital Blood Bank', '123 Main St, City Center', '555-0123', 'blood@citygeneral.com', NULL, NULL, NULL, '2025-01-02 15:39:26'),
(2, 'Red Cross Blood Center', '456 Health Ave, Downtown', '555-0124', 'donate@redcross.org', NULL, NULL, NULL, '2025-01-02 15:39:26'),
(3, 'Community Blood Services', '789 Care Lane, Uptown', '555-0125', 'info@communityblood.org', NULL, NULL, NULL, '2025-01-02 15:39:26'),
(4, 'kamaldeep', 'narnaul', '7015277872', NULL, NULL, 28.23423400, 74.24532453, '2025-01-02 16:03:28'),
(5, 'vanshika blood doner', 'narnaul', '7015277875', NULL, NULL, 25.20003000, 23.23423400, '2025-01-02 16:20:39'),
(6, 'Govt Hospital narnaul', 'Govt Hospital narnaul', '7015277870', NULL, NULL, 25.00000000, 78.00000000, '2025-01-02 19:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `blood_bank_admins`
--

CREATE TABLE `blood_bank_admins` (
  `id` int NOT NULL,
  `blood_bank_id` int DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `blood_bank_admins`
--

INSERT INTO `blood_bank_admins` (`id`, `blood_bank_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 4, 'kamaldeep', 'kamaldeep@gmail.com', '$2y$10$EYcrkTgsbC3yhve/nGgFdOqi8Gv/4jQ8DRG00M8GB40JiTqR6Z0zm', 'admin', '2025-01-02 16:03:28'),
(2, 5, 'vanshika', 'vanshika@gmail.com', '$2y$10$K22P2myjNTzlf.FeE5GytODt7urDUvXfIdlfZRNzcevYAOTsbA1/m', 'admin', '2025-01-02 16:20:40'),
(3, 6, 'admin', 'admin@gmail.com', '$2y$10$hY283cW6Yt.eys11a3451OrGm1qd2vTmaV/8NNPCBGpqm5mGihsEG', 'admin', '2025-01-02 19:55:34');

-- --------------------------------------------------------

--
-- Table structure for table `donation_history`
--

CREATE TABLE `donation_history` (
  `id` int NOT NULL,
  `donor_id` int DEFAULT NULL,
  `donation_date` date DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `units` int DEFAULT NULL,
  `hospital_name` varchar(255) DEFAULT NULL,
  `certificate_number` varchar(50) DEFAULT NULL,
  `next_eligible_date` date DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `donation_history`
--

INSERT INTO `donation_history` (`id`, `donor_id`, `donation_date`, `blood_group`, `units`, `hospital_name`, `certificate_number`, `next_eligible_date`, `notes`, `created_at`) VALUES
(1, 2, '2024-10-10', 'B+', 2, 'govt narnaul', '123231', '2025-01-10', 'test', '2025-01-02 15:12:33'),
(2, 2, '2025-01-01', 'B+', 1, 'govt narnaul', '1232355', '2025-04-01', 'test', '2025-01-02 15:13:15'),
(3, 8, '2025-01-02', 'B+', 1, 'vanshika blood doner', NULL, '2025-04-02', NULL, '2025-01-02 16:45:39'),
(16, 3, '2023-10-12', 'B-', 1, 'govt narnaul', '32423', '2024-01-12', 'sdfsdf', '2025-01-02 18:29:26'),
(17, 3, '2024-01-25', 'B-', 2, 'govt narnaul', '23423', '2024-04-25', '234234', '2025-01-02 18:29:56'),
(18, 3, '2024-07-17', 'B-', 2, 'govt narnaul', '234234', '2024-10-17', '2342', '2025-01-02 18:30:22'),
(19, 3, '2024-10-18', 'B-', 2, 'govt narnaul', '234234', '2025-01-18', '2342', '2025-01-02 18:41:55'),
(20, 3, '2024-12-28', 'B-', 2, 'govt narnaul', '23423', '2025-03-28', '234', '2025-01-02 18:42:12'),
(21, 3, '2025-01-01', 'B-', 2, 'govt narnaul', '123123', '2025-04-01', '123', '2025-01-02 18:43:43'),
(22, 10, '2022-11-02', 'B+', 2, 'govt narnaul', 'qweqwe21', '2023-02-02', '123123', '2025-01-02 18:53:36'),
(23, 10, '2023-05-10', 'B+', 1, 'govt narnaul', '21312', '2023-08-10', '12312', '2025-01-02 18:53:57'),
(24, 10, '2023-11-29', 'B+', 2, 'govt narnaul', '1232', '2024-02-29', '123', '2025-01-02 19:07:09'),
(25, 10, '2025-01-03', 'B+', 1, 'vanshika blood doner', NULL, '2025-04-03', NULL, '2025-01-02 19:13:17'),
(26, 13, '2024-09-12', 'B+', 2, 'Govt narnaul', '12312234', '2024-12-12', 'very good', '2025-01-18 07:21:20'),
(27, 13, '2025-01-18', 'B+', 1, 'vanshika blood doner', NULL, '2025-04-18', NULL, '2025-01-18 07:45:30');

--
-- Triggers `donation_history`
--
DELIMITER $$
CREATE TRIGGER `after_donation_insert` AFTER INSERT ON `donation_history` FOR EACH ROW BEGIN
    CALL update_donor_rewards(NEW.donor_id);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `donor_rewards`
--

CREATE TABLE `donor_rewards` (
  `id` int NOT NULL,
  `donor_id` int DEFAULT NULL,
  `points` int DEFAULT '0',
  `level` varchar(20) DEFAULT 'Bronze',
  `total_donations` int DEFAULT '0',
  `badges` json DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `donor_rewards`
--

INSERT INTO `donor_rewards` (`id`, `donor_id`, `points`, `level`, `total_donations`, `badges`, `last_updated`) VALUES
(1, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:13:04'),
(2, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:14:25'),
(3, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:15:45'),
(4, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:15:52'),
(5, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:15:54'),
(6, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:15:56'),
(7, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:15:57'),
(8, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:16:49'),
(9, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:19:45'),
(10, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:19:47'),
(11, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:19:47'),
(12, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:19:48'),
(13, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:19:51'),
(14, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:19:55'),
(16, 3, 400, 'Bronze', 4, NULL, '2025-01-02 18:29:26'),
(17, 3, 150, 'Bronze', 1, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:29:26'),
(18, 3, 500, 'Silver', 5, NULL, '2025-01-02 18:29:56'),
(19, 3, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:29:56'),
(20, 3, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:30:00'),
(21, 3, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:30:01'),
(22, 3, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:30:02'),
(23, 3, 600, 'Silver', 6, NULL, '2025-01-02 18:30:22'),
(24, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:30:22'),
(25, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:30:31'),
(26, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:30:32'),
(27, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:30:38'),
(28, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:32:25'),
(29, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:32:28'),
(30, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:32:31'),
(31, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:32:33'),
(32, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:32:34'),
(33, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:35:17'),
(34, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:35:31'),
(35, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:35:37'),
(36, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:38:19'),
(37, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:38:22'),
(38, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:38:23'),
(39, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:41:20'),
(40, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:41:21'),
(41, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:41:22'),
(42, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:41:23'),
(43, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:41:23'),
(44, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:41:23'),
(45, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:41:23'),
(46, 3, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:41:27'),
(47, 3, 700, 'Silver', 7, NULL, '2025-01-02 18:41:55'),
(48, 3, 750, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:41:55'),
(49, 3, 800, 'Silver', 8, NULL, '2025-01-02 18:42:12'),
(50, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:42:12'),
(51, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:42:18'),
(52, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:42:19'),
(53, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:42:24'),
(54, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:01'),
(55, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:01'),
(56, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:02'),
(57, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:02'),
(58, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:02'),
(59, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:03'),
(60, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:03'),
(61, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:03'),
(62, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:04'),
(63, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:05'),
(64, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:05'),
(65, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:06'),
(66, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:06'),
(67, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:06'),
(68, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:07'),
(69, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:08'),
(70, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:09'),
(71, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:09'),
(72, 3, 950, 'Silver', 5, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:09'),
(73, 3, 900, 'Silver', 9, NULL, '2025-01-02 18:43:43'),
(74, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:43'),
(75, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:49'),
(76, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:43:50'),
(77, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:44:04'),
(78, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:44:05'),
(79, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:44:05'),
(80, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:44:05'),
(81, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:44:06'),
(82, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:44:06'),
(83, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:44:07'),
(84, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:44:08'),
(85, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:45:42'),
(86, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:49:04'),
(87, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:49:07'),
(88, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:52:11'),
(89, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:52:12'),
(90, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:52:13'),
(91, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:52:14'),
(92, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:52:15'),
(93, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:52:15'),
(94, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:52:16'),
(95, 3, 1150, 'Gold', 6, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}, {\"icon\": \"donation-5\", \"name\": \"5 Donations\", \"type\": \"achievement\", \"description\": \"Completed 5 blood donations\"}]', '2025-01-02 18:52:16'),
(96, 10, 0, 'Bronze', 0, '[]', '2025-01-02 18:52:59'),
(97, 10, 0, 'Bronze', 0, '[]', '2025-01-02 18:53:01'),
(98, 10, 0, 'Bronze', 0, '[]', '2025-01-02 18:53:05'),
(99, 10, 0, 'Bronze', 0, '[]', '2025-01-02 18:53:10'),
(100, 10, 1000, 'Gold', 10, NULL, '2025-01-02 18:53:36'),
(101, 10, 200, 'Bronze', 1, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:53:36'),
(102, 10, 1100, 'Gold', 11, NULL, '2025-01-02 18:53:57'),
(103, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:53:57'),
(104, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:54:01'),
(105, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:54:02'),
(106, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:54:05'),
(107, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:54:14'),
(108, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:58:10'),
(109, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:58:15'),
(110, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:58:19'),
(111, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:58:28'),
(112, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:58:45'),
(113, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:59:09'),
(114, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:59:12'),
(115, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 18:59:24'),
(116, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:13'),
(117, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:19'),
(118, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:48'),
(119, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:49'),
(120, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:51'),
(121, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:52'),
(122, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:53'),
(123, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:53'),
(124, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:54'),
(125, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:54'),
(126, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:54'),
(127, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:56'),
(128, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:57'),
(129, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:57'),
(130, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:59'),
(131, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:02:59'),
(132, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:01'),
(133, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:02'),
(134, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:05'),
(135, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:48'),
(136, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:50'),
(137, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:51'),
(138, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:51'),
(139, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:51'),
(140, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:52'),
(141, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:52'),
(142, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:03:53'),
(143, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:06'),
(144, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:07'),
(145, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:07'),
(146, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:07'),
(147, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:07'),
(148, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:36'),
(149, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:38'),
(150, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:39'),
(151, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:41'),
(152, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:50'),
(153, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:52'),
(154, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:52'),
(155, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:53'),
(156, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:53'),
(157, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:54'),
(158, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:04:54'),
(159, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:35'),
(160, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:37'),
(161, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:38'),
(162, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:39'),
(163, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:39'),
(164, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:40'),
(165, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:40'),
(166, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:40'),
(167, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:41'),
(168, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:41'),
(169, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:41'),
(170, 10, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:06:42'),
(171, 10, 1200, 'Gold', 12, NULL, '2025-01-02 19:07:09'),
(172, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:09'),
(173, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:14'),
(174, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:15'),
(175, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:17'),
(176, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:21'),
(177, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:22'),
(178, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:22'),
(179, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:22'),
(180, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:23'),
(181, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:24'),
(182, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:24'),
(183, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:25'),
(184, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:25'),
(185, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:26'),
(186, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:26'),
(187, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:27'),
(188, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:27'),
(189, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:27'),
(190, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:27'),
(191, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:29'),
(192, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:29'),
(193, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:29'),
(194, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:30'),
(195, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:30'),
(196, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:30'),
(197, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:31'),
(198, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:31'),
(199, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:31'),
(200, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:32'),
(201, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:32'),
(202, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:46'),
(203, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:07:47'),
(204, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:06'),
(205, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:08'),
(206, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:09'),
(207, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:09'),
(208, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:09'),
(209, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:10'),
(210, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:11'),
(211, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:11'),
(212, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:13'),
(213, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:14'),
(214, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:16'),
(215, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:19'),
(216, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:20'),
(217, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:21'),
(218, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:09:22'),
(219, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:10:02'),
(220, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:10:03'),
(221, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:10:19'),
(222, 10, 550, 'Silver', 3, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:10:23'),
(223, 10, 1300, 'Gold', 13, NULL, '2025-01-02 19:13:17'),
(224, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:13:33'),
(225, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:13:34'),
(226, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:13:36'),
(227, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:13:41'),
(228, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:13:45'),
(229, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:13:55'),
(230, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:13:58'),
(231, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:14:01'),
(232, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:14:02'),
(233, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:14:04'),
(234, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:14:58'),
(235, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:15:01'),
(236, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:15:01'),
(237, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:15:02'),
(238, 10, 700, 'Silver', 4, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:15:02'),
(239, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:19:12'),
(240, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:19:13'),
(241, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:19:14'),
(242, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:19:15'),
(243, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:21:07'),
(244, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:21:08'),
(245, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:22:42'),
(246, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:31:20'),
(247, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:31:28'),
(248, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:33:10');
INSERT INTO `donor_rewards` (`id`, `donor_id`, `points`, `level`, `total_donations`, `badges`, `last_updated`) VALUES
(249, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:33:12'),
(250, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:33:13'),
(251, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:33:13'),
(252, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:33:14'),
(253, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:34:42'),
(254, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:34:44'),
(255, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:34:45'),
(256, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:34:45'),
(257, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:38:41'),
(258, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:38:44'),
(259, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:38:45'),
(260, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:38:45'),
(261, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:38:45'),
(262, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:38:49'),
(263, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:38:50'),
(264, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 19:38:53'),
(265, 11, 0, 'Bronze', 0, '[]', '2025-01-02 19:45:24'),
(266, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:03:28'),
(267, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:03:29'),
(268, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:03:31'),
(269, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:03:33'),
(270, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:03:37'),
(271, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:03:38'),
(272, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:06:06'),
(273, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:06:07'),
(274, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:06:10'),
(275, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:06:10'),
(276, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:07:26'),
(277, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:07:27'),
(278, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:07:42'),
(279, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:07:43'),
(280, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:07:45'),
(281, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:07:46'),
(282, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:09:58'),
(283, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:09:59'),
(284, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:10:04'),
(285, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:10:05'),
(286, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:11:18'),
(287, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:11:19'),
(288, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:11:20'),
(289, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:11:23'),
(290, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:11:33'),
(291, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:11:35'),
(292, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:11:43'),
(293, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:12:27'),
(294, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:12:31'),
(295, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:12:32'),
(296, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:12:33'),
(297, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:12:34'),
(298, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:12:37'),
(299, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:12:39'),
(300, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:13:59'),
(301, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:14:01'),
(302, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:14:15'),
(303, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:14:29'),
(304, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:15:04'),
(305, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:15:15'),
(306, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:15:16'),
(307, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:17:29'),
(308, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:19:02'),
(309, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-02 20:19:29'),
(310, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-04 15:03:04'),
(311, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-04 15:03:09'),
(312, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-04 15:03:09'),
(313, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-04 15:03:10'),
(314, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-04 15:03:10'),
(315, 12, 0, 'Bronze', 0, '[]', '2025-01-04 15:07:21'),
(316, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 05:05:52'),
(317, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 05:06:13'),
(318, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 05:06:22'),
(319, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 05:07:05'),
(320, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 05:08:47'),
(321, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 05:10:29'),
(322, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 05:10:38'),
(323, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 05:10:39'),
(324, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 08:34:29'),
(325, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 08:34:38'),
(326, 2, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-05 08:50:38'),
(327, 13, 0, 'Bronze', 0, '[]', '2025-01-18 07:18:40'),
(328, 13, 1400, 'Gold', 14, NULL, '2025-01-18 07:21:20'),
(329, 13, 200, 'Bronze', 1, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-18 07:21:20'),
(330, 13, 1500, 'Gold', 15, NULL, '2025-01-18 07:45:30'),
(331, 13, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-18 07:48:05'),
(332, 13, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-18 07:48:58'),
(333, 13, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-18 07:50:28'),
(334, 13, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-18 07:51:46'),
(335, 13, 350, 'Bronze', 2, '[{\"icon\": \"donation-1\", \"name\": \"1 Donations\", \"type\": \"achievement\", \"description\": \"Completed 1 blood donations\"}]', '2025-01-18 07:58:21');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_requests`
--

CREATE TABLE `emergency_requests` (
  `id` int NOT NULL,
  `requester_id` int DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `blood_group` varchar(5) DEFAULT NULL,
  `units_needed` int DEFAULT NULL,
  `hospital_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `urgency_level` enum('High','Medium','Low') DEFAULT NULL,
  `status` enum('Active','Fulfilled','Expired') DEFAULT 'Active',
  `additional_notes` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `emergency_requests`
--

INSERT INTO `emergency_requests` (`id`, `requester_id`, `patient_name`, `blood_group`, `units_needed`, `hospital_name`, `location`, `contact_number`, `urgency_level`, `status`, `additional_notes`, `created_at`, `updated_at`) VALUES
(1, 2, 'kamaldeep', 'B+', 1, 'govt narnaul', 'narnaul', '7015277872', 'Medium', 'Fulfilled', 'pls contact me at 7015277872', '2025-01-02 13:46:25', '2025-01-02 13:47:01'),
(2, 2, 'kamaldeep saini', 'B+', 2, 'govt narnaul', 'narnaul', '7015277872', 'High', 'Fulfilled', 'pls help', '2025-01-02 13:47:26', '2025-01-05 05:06:05'),
(3, 9, 'hari', 'B-', 1, 'govt narnaul', 'narnaul', '7015277870', 'Medium', 'Active', 'pls share details as posible as fast', '2025-01-02 16:49:58', '2025-01-02 16:49:58'),
(4, 10, 'moon', 'AB-', 2, 'govt narnaul', 'narnaul', '7015277870', 'High', 'Active', 'pls ', '2025-01-02 19:11:51', '2025-01-02 19:11:51');

-- --------------------------------------------------------

--
-- Table structure for table `points_history`
--

CREATE TABLE `points_history` (
  `id` int NOT NULL,
  `donor_id` int NOT NULL,
  `points` int NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` enum('donation','streak','achievement') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `points_history`
--

INSERT INTO `points_history` (`id`, `donor_id`, `points`, `description`, `type`, `created_at`) VALUES
(24, 10, 100, 'Blood donation completed', 'donation', '2023-05-09 18:30:00'),
(25, 10, 50, 'Bonus points for units donated', 'donation', '2023-05-09 18:30:00'),
(26, 10, 100, 'Blood donation completed', 'donation', '2022-11-01 18:30:00'),
(27, 10, 100, 'Blood donation completed', 'donation', '2023-11-28 18:30:00'),
(28, 10, 100, 'Blood donation completed', 'donation', '2025-01-02 18:30:00'),
(29, 10, 50, 'Bonus points for units donated', 'donation', '2025-01-02 18:30:00'),
(30, 2, 100, 'Blood donation completed', 'donation', '2024-12-31 18:30:00'),
(31, 2, 50, 'Bonus points for units donated', 'donation', '2024-12-31 18:30:00'),
(32, 2, 100, 'Blood donation completed', 'donation', '2024-10-09 18:30:00'),
(33, 13, 100, 'Blood donation completed', 'donation', '2024-09-11 18:30:00'),
(34, 13, 100, 'Blood donation completed', 'donation', '2025-01-17 18:30:00'),
(35, 13, 50, 'Bonus points for units donated', 'donation', '2025-01-17 18:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `reward_badges`
--

CREATE TABLE `reward_badges` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `points_required` int NOT NULL,
  `badge_type` varchar(20) DEFAULT 'achievement',
  `icon_class` varchar(50) DEFAULT 'bi-award',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reward_badges`
--

INSERT INTO `reward_badges` (`id`, `name`, `description`, `points_required`, `badge_type`, `icon_class`, `created_at`) VALUES
(1, 'First Blood', 'Completed your first blood donation', 100, 'achievement', 'bi-droplet', '2025-01-02 17:46:46'),
(2, 'Regular Donor', 'Completed 3 blood donations', 300, 'achievement', 'bi-heart', '2025-01-02 17:46:46'),
(3, 'Silver Savior', 'Completed 5 blood donations', 500, 'rank', 'bi-shield', '2025-01-02 17:46:46'),
(4, 'Golden Heart', 'Completed 10 blood donations', 1000, 'rank', 'bi-star', '2025-01-02 17:46:46'),
(5, 'Platinum Hero', 'Completed 20 blood donations', 2000, 'rank', 'bi-trophy', '2025-01-02 17:46:46'),
(6, 'Emergency Responder', 'Donated blood during an emergency', 250, 'special', 'bi-lightning', '2025-01-02 17:46:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT '1',
  `share_contact` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `age`, `gender`, `blood_group`, `location`, `latitude`, `longitude`, `is_available`, `share_contact`, `created_at`, `updated_at`, `profile_picture`) VALUES
(1, 'Test User', 'test@example.com', '$2y$10$4mwoWm9UeC94FgTNZqhdde2Ypf96HZ0BmH0.ug9lbU6J30Brr5Qau', NULL, NULL, NULL, 'B+', NULL, NULL, NULL, 1, 1, '2025-01-02 12:44:00', '2025-01-02 12:47:52', NULL),
(2, 'kamaldeep', 'kamaldeep@kringle.ai', '$2y$10$kNTC36upEVSGZWUXPcMMo.iq0V5/gcPV4t8JaZEL2S/d7M8kDezyO', '7015277872', 25, 'male', 'B+', 'narnaul', NULL, NULL, 1, 1, '2025-01-02 12:44:18', '2025-01-02 13:01:24', '2_1735822884_unnamed.jpg'),
(3, 'Ankit Mishra', 'test@kringle.ai', '$2y$10$FdRZBGoXTXBeMBnGLuNU3uTHR32zXgIOIjf0gU0n04Enwz75qJaHS', '7015277875', 30, 'male', 'B-', 'narnaul', NULL, NULL, 1, 1, '2025-01-02 15:52:35', '2025-01-02 15:54:22', '3_1735833249_s.jpg'),
(6, 'uclub', 'test2@kringle.ai', '$2y$10$RoL91p/JIUxVAlu3C4nvkOg98OOr92EVjSFgS69KTap6W.Ipddjuq', '7015277877', NULL, NULL, 'B+', NULL, NULL, NULL, 1, 1, '2025-01-02 16:29:02', '2025-01-02 16:29:02', NULL),
(7, 'github', 'test3@kringle.ai', '$2y$10$oe6My2ko7LaWEGZnaHXNNemDIMYp4KqRAbExXGsYYOU8R6TtUrPCK', '7015277873', NULL, NULL, 'B+', NULL, NULL, NULL, 1, 1, '2025-01-02 16:34:42', '2025-01-02 16:34:42', NULL),
(8, 'hansika', 'test4@kringle.ai', '$2y$10$g1vd2XkWYP3ZoP.FoHkea.jtni7kTpvuZqbVLZn3Kg0FiuHigPOGy', '7015277876', NULL, NULL, 'B+', NULL, NULL, NULL, 1, 1, '2025-01-02 16:44:36', '2025-01-02 16:44:36', NULL),
(9, 'Ankit ', 'ankit@kringle.ai', '$2y$10$LabVrOLfaji5sZQ3JLLEJOs5YrKhhQgz84ZMXbD4A8fXARaQy4w7G', '7015277870', NULL, NULL, 'A-', NULL, NULL, NULL, 1, 1, '2025-01-02 16:46:49', '2025-01-02 16:46:49', NULL),
(10, 'we', 'test6@kringle.ai', '$2y$10$4QHXURPa8BdM4ld4w8x6p.yUTDccz/hBWZwZxsqpKAKMy6ytJmchG', '7015277812', NULL, NULL, 'B+', NULL, NULL, NULL, 1, 1, '2025-01-02 18:52:49', '2025-01-02 18:52:49', NULL),
(11, 'guru', 'test7@kringle.ai', '$2y$10$zapbe8yOG/voT5uJGciFU.d5cL8KngWrxikyjPTNw7IV2Oc31cTs6', '7015277809', 29, 'male', 'A+', 'narnaul', NULL, NULL, 1, 1, '2025-01-02 19:45:15', '2025-01-02 19:45:42', NULL),
(12, 'raju', 'raju@gmail.com', '$2y$10$F8RqG8GfdidTkHkBt9D3f.v8kGwI6ZEldOMDkJvnRI/6JTx3wQAGW', 'raju', NULL, NULL, 'O+', NULL, NULL, NULL, 1, 1, '2025-01-04 15:06:58', '2025-01-04 15:06:58', NULL),
(13, 'panu', 'panu@gmail.com', '$2y$10$K22P2myjNTzlf.FeE5GytODt7urDUvXfIdlfZRNzcevYAOTsbA1/m', 'Panu@123', NULL, NULL, 'B+', NULL, NULL, NULL, 1, 1, '2025-01-18 07:18:28', '2025-01-18 07:18:28', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`),
  ADD KEY `blood_bank_id` (`blood_bank_id`);

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `available_slots`
--
ALTER TABLE `available_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blood_bank_id` (`blood_bank_id`);

--
-- Indexes for table `blood_banks`
--
ALTER TABLE `blood_banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blood_bank_admins`
--
ALTER TABLE `blood_bank_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `blood_bank_id` (`blood_bank_id`);

--
-- Indexes for table `donation_history`
--
ALTER TABLE `donation_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_donor_date` (`donor_id`,`donation_date`);

--
-- Indexes for table `donor_rewards`
--
ALTER TABLE `donor_rewards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_donor_points` (`donor_id`,`points`);

--
-- Indexes for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requester_id` (`requester_id`);

--
-- Indexes for table `points_history`
--
ALTER TABLE `points_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_donor_history` (`donor_id`,`created_at`),
  ADD KEY `idx_donation_history` (`donor_id`,`created_at`);

--
-- Indexes for table `reward_badges`
--
ALTER TABLE `reward_badges`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `available_slots`
--
ALTER TABLE `available_slots`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `blood_banks`
--
ALTER TABLE `blood_banks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `blood_bank_admins`
--
ALTER TABLE `blood_bank_admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `donation_history`
--
ALTER TABLE `donation_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `donor_rewards`
--
ALTER TABLE `donor_rewards`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=336;

--
-- AUTO_INCREMENT for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `points_history`
--
ALTER TABLE `points_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `reward_badges`
--
ALTER TABLE `reward_badges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`blood_bank_id`) REFERENCES `blood_banks` (`id`);

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `available_slots`
--
ALTER TABLE `available_slots`
  ADD CONSTRAINT `available_slots_ibfk_1` FOREIGN KEY (`blood_bank_id`) REFERENCES `blood_banks` (`id`);

--
-- Constraints for table `blood_bank_admins`
--
ALTER TABLE `blood_bank_admins`
  ADD CONSTRAINT `blood_bank_admins_ibfk_1` FOREIGN KEY (`blood_bank_id`) REFERENCES `blood_banks` (`id`);

--
-- Constraints for table `donation_history`
--
ALTER TABLE `donation_history`
  ADD CONSTRAINT `donation_history_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `donor_rewards`
--
ALTER TABLE `donor_rewards`
  ADD CONSTRAINT `donor_rewards_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `emergency_requests`
--
ALTER TABLE `emergency_requests`
  ADD CONSTRAINT `emergency_requests_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `points_history`
--
ALTER TABLE `points_history`
  ADD CONSTRAINT `points_history_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
