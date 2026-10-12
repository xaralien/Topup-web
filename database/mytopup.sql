-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2026 at 08:46 AM
-- Server version: 8.4.3
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mytopup`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(30) NOT NULL,
  `name` varchar(80) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `name`, `password_hash`, `last_login_at`, `created_at`) VALUES
(1, 'admin', 'Admin', '$2y$10$ggNiW0xLaRzGuG/gF0IM8uFEk4SMbwKS..DY.2gS4NqseZO/wg5Ma', '2026-10-12 13:44:35', '2026-10-12 13:11:26');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int UNSIGNED NOT NULL,
  `slug` varchar(80) NOT NULL,
  `name` varchar(120) NOT NULL,
  `category` varchar(40) NOT NULL,
  `currency` varchar(60) NOT NULL,
  `color` varchar(9) NOT NULL DEFAULT '#E2E8F0',
  `image` varchar(255) DEFAULT NULL,
  `is_hot` tinyint(1) NOT NULL DEFAULT '0',
  `needs_zone` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `slug`, `name`, `category`, `currency`, `color`, `image`, `is_hot`, `needs_zone`, `is_active`, `sort_order`) VALUES
(1, 'mobile-legends', 'Mobile Legends', 'Game Mobile', 'Diamond', '#c7d2fe', 'assets/uploads/games/fb6e32a276c3394fc591d649.png', 1, 1, 1, 1),
(2, 'free-fire', 'Free Fire', 'Game Mobile', 'Diamond', '#FDE68A', NULL, 1, 0, 1, 2),
(3, 'genshin-impact', 'Genshin Impact', 'Game Mobile', 'Genesis Crystal', '#FBCFE8', NULL, 1, 0, 1, 3),
(4, 'pubg-mobile', 'PUBG Mobile', 'Game Mobile', 'UC', '#BAE6FD', NULL, 1, 0, 1, 4),
(5, 'roblox', 'Roblox', 'Game PC', 'Robux', '#BBF7D0', NULL, 1, 0, 1, 5),
(6, 'valorant', 'Valorant', 'Game PC', 'VP', '#DDD6FE', NULL, 0, 0, 1, 6),
(7, 'steam-wallet', 'Steam Wallet', 'Voucher', 'IDR Voucher', '#E2E8F0', NULL, 0, 0, 1, 7),
(8, 'google-play', 'Google Play', 'Voucher', 'Voucher', '#D1FAE5', NULL, 0, 0, 1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` bigint UNSIGNED NOT NULL,
  `ip` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `order_no` varchar(24) NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `game_id` int UNSIGNED NOT NULL,
  `pack_id` int UNSIGNED NOT NULL,
  `game_name` varchar(120) NOT NULL,
  `item_label` varchar(160) NOT NULL,
  `sku` varchar(60) DEFAULT NULL,
  `target_user_id` varchar(60) NOT NULL,
  `target_zone_id` varchar(20) DEFAULT NULL,
  `email` varchar(160) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `amount` int UNSIGNED NOT NULL,
  `payment_method` varchar(10) NOT NULL,
  `payment_name` varchar(60) NOT NULL,
  `status` enum('pending','paid','processing','success','failed','expired') NOT NULL DEFAULT 'pending',
  `duitku_ref` varchar(60) DEFAULT NULL,
  `payment_url` varchar(500) DEFAULT NULL,
  `va_number` varchar(60) DEFAULT NULL,
  `qr_string` text,
  `expires_at` datetime NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `fulfilled_at` datetime DEFAULT NULL,
  `last_checked_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packs`
--

CREATE TABLE `packs` (
  `id` int UNSIGNED NOT NULL,
  `game_id` int UNSIGNED NOT NULL,
  `qty` varchar(20) NOT NULL,
  `price` int UNSIGNED NOT NULL,
  `sku` varchar(60) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `packs`
--

INSERT INTO `packs` (`id`, `game_id`, `qty`, `price`, `sku`, `is_active`, `sort_order`) VALUES
(1, 2, '70', 10000, NULL, 1, 1),
(2, 2, '140', 20000, NULL, 1, 2),
(3, 2, '355', 50000, NULL, 1, 3),
(4, 2, '720', 100000, NULL, 1, 4),
(5, 3, '60', 16000, NULL, 1, 1),
(6, 3, '300', 79000, NULL, 1, 2),
(7, 3, '980', 249000, NULL, 1, 3),
(8, 3, '1980', 479000, NULL, 1, 4),
(9, 8, '20000', 22000, NULL, 1, 1),
(10, 8, '50000', 53000, NULL, 1, 2),
(11, 8, '100000', 104000, NULL, 1, 3),
(12, 1, '86', 20000, NULL, 1, 1),
(13, 1, '172', 39000, NULL, 1, 2),
(14, 1, '257', 58000, NULL, 1, 3),
(15, 1, '514', 115000, NULL, 1, 4),
(16, 1, '1050', 230000, NULL, 1, 5),
(17, 4, '60', 15000, NULL, 1, 1),
(18, 4, '325', 75000, NULL, 1, 2),
(19, 4, '660', 149000, NULL, 1, 3),
(20, 5, '80', 15000, NULL, 1, 1),
(21, 5, '400', 75000, NULL, 1, 2),
(22, 5, '800', 150000, NULL, 1, 3),
(23, 7, '12000', 14000, NULL, 1, 1),
(24, 7, '45000', 50000, NULL, 1, 2),
(25, 7, '120000', 130000, NULL, 1, 3),
(26, 6, '475', 59000, NULL, 1, 1),
(27, 6, '1000', 119000, NULL, 1, 2),
(28, 6, '2050', 239000, NULL, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `order_no` varchar(24) DEFAULT NULL,
  `type` varchar(30) NOT NULL,
  `payload` mediumtext,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `last_login_at`, `created_at`) VALUES
(1, 'Adrian', 'adrian@gmail.com', '$2y$10$VBoI14aDhgfH0twVcCe9EOMQP05ht0yxagOijEngaK8fS4dVdRGfq', NULL, '2026-10-12 13:45:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_games_cat` (`category`,`is_active`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_attempts` (`ip`,`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_no` (`order_no`),
  ADD KEY `fk_orders_game` (`game_id`),
  ADD KEY `fk_orders_pack` (`pack_id`),
  ADD KEY `idx_orders_status` (`status`,`expires_at`),
  ADD KEY `idx_orders_email` (`email`),
  ADD KEY `idx_orders_user` (`user_id`);

--
-- Indexes for table `packs`
--
ALTER TABLE `packs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_packs_game` (`game_id`,`is_active`);

--
-- Indexes for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_order` (`order_no`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packs`
--
ALTER TABLE `packs`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`),
  ADD CONSTRAINT `fk_orders_pack` FOREIGN KEY (`pack_id`) REFERENCES `packs` (`id`),
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `packs`
--
ALTER TABLE `packs`
  ADD CONSTRAINT `fk_packs_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
