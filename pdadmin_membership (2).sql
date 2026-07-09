-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 09, 2026 at 08:26 AM
-- Server version: 11.4.12-MariaDB-log
-- PHP Version: 8.4.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pdadmin_membership`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `icon` varchar(20) DEFAULT NULL,
  `text_zh` varchar(255) NOT NULL,
  `text_en` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `icon`, `text_zh`, `text_en`, `created_at`) VALUES
(4, '🧾', '<b>test3</b> 上傳了消費收據等待審核', '<b>test3</b> uploaded a checkout receipt for review', '2026-07-08 05:24:43'),
(5, '🧾', '審核收據：拒絕了 <b>test3</b> 的收據申請', 'Receipt Rejected: Denied <b>test3</b>\'s receipt request', '2026-07-08 05:24:56'),
(6, '✉️', '管理員向 <b>test3</b> 發放了「test」優惠券', 'Admin issued \"test\" voucher to <b>test3</b>', '2026-07-08 06:39:52'),
(7, '✍️', '管理員手動調整了會員 <b>test3</b> 的積分/印花', 'Admin adjusted member <b>test3</b>\'s points/stamps manually', '2026-07-08 06:41:52'),
(8, '✍️', '管理員手動調整了會員 <b>test3</b> 的積分/印花', 'Admin adjusted member <b>test3</b>\'s points/stamps manually', '2026-07-08 06:42:07'),
(9, '🎁', '管理員掃描 <b>test3</b> 的專屬 QR，發放了 <b>+200 積分</b>', 'Admin scanned <b>test3</b>\'s QR, granted <b>+200 Points</b>', '2026-07-08 06:44:10'),
(10, '🎁', '管理員掃描 <b>test3</b> 的專屬 QR，發放了 <b>+200 積分</b>', 'Admin scanned <b>test3</b>\'s QR, granted <b>+200 Points</b>', '2026-07-08 06:44:18'),
(11, '✍️', '管理員手動調整了會員 <b>test3</b> 的積分/印花', 'Admin adjusted member <b>test3</b>\'s points/stamps manually', '2026-07-08 07:18:01'),
(12, '✍️', '管理員手動調整了會員 <b>test3</b> 的積分/印花', 'Admin adjusted member <b>test3</b>\'s points/stamps manually', '2026-07-08 07:18:38'),
(13, '✍️', '管理員手動調整了會員 <b>test3</b> 的積分/印花/抽獎次數', 'Admin adjusted member <b>test3</b>\'s points/stamps/spins manually', '2026-07-08 07:22:24'),
(14, '🎡', '<b>test3</b> 參與幸運輪盤，獲得了 +100 積分！', '<b>test3</b> spun the wheel and won +100 Points!', '2026-07-08 07:23:04'),
(15, '🎁', '<b>test3</b> 扣除 <b>100</b> 積分兌換了「test」', '<b>test3</b> spent <b>100</b> Points to redeem \"\"', '2026-07-08 07:23:26'),
(16, '🎁', '管理員掃描 <b>test3</b> 的專屬 QR，發放了 <b>+10 積分</b>', 'Admin scanned <b>test3</b>\'s QR, granted <b>+10 Points</b>', '2026-07-08 07:40:35'),
(17, '🎡', '<b>test3</b> 參與幸運輪盤，獲得了 +1 積分！', '<b>test3</b> spun the wheel and won +1 Points!', '2026-07-08 08:04:18'),
(18, '🎡', '<b>test3</b> 參與幸運輪盤，獲得了 +1 積分！', '<b>test3</b> spun the wheel and won +1 Points!', '2026-07-08 08:04:33'),
(19, '📌', '<b>test3</b> 提交了「分享家居照片」任務審核', '<b>test3</b> submitted 分享家居照片 task for review', '2026-07-08 08:05:13'),
(20, '👋', '新會員 <b>test01</b> 完成註冊', 'New member <b>test01</b> joined the system', '2026-07-08 08:10:27'),
(21, '✉️', '管理員向 <b>test01</b> 發放了「TESTTTTTTTT」優惠券', 'Admin issued \"TESTTTTTTTT\" voucher to <b>test01</b>', '2026-07-08 08:10:40'),
(22, '🎡', '<b>test01</b> 參與幸運輪盤，獲得了 +1 積分！', '<b>test01</b> spun the wheel and won +1 Points!', '2026-07-08 08:10:47'),
(23, '✅', '審核任務：<b>test3</b> 的「分享家居照片」任務已通過，獲得 <b>+2 積分</b>', 'Task Approved: <b>test3</b>\'s task is approved, earned <b>+2 Points</b>', '2026-07-08 08:26:53'),
(24, '👋', '新會員 <b>test005</b> 完成註冊', 'New member <b>test005</b> joined the system', '2026-07-08 23:46:40'),
(25, '👋', '新會員 <b>test00101</b> 完成註冊', 'New member <b>test00101</b> joined the system', '2026-07-09 01:37:16'),
(26, '✔️', '門市店員已核銷 <b>test3</b> 的優惠券「test」', 'Staff redeemed <b>test3</b>\'s voucher \"test\"', '2026-07-09 07:11:55'),
(27, '🏪', '門市店員為 <b>test3</b> 記錄消費 HK$200，發放 <b>+200 積分</b>', 'Staff logged HK$200 purchase for <b>test3</b>, issued <b>+200 Points</b>', '2026-07-09 07:12:22'),
(28, '👋', '新會員 <b>test666</b> 完成註冊', 'New member <b>test666</b> joined the system', '2026-07-09 07:13:16'),
(29, '👋', '新會員 <b>test0000001</b> 完成註冊', 'New member <b>test0000001</b> joined the system', '2026-07-09 07:34:47'),
(30, '🏪', '門市店員為 <b>test0000001</b> 記錄消費 HK$100，發放 <b>+100 積分</b>', 'Staff logged HK$100 purchase for <b>test0000001</b>, issued <b>+100 Points</b>', '2026-07-09 07:35:09'),
(31, '🏪', '門市店員為 <b>test0000001</b> 記錄消費 HK$100，發放 <b>+1 印花</b>', 'Staff logged HK$100 purchase for <b>test0000001</b>, issued <b>+1 Stamps</b>', '2026-07-09 07:35:51'),
(32, '🏪', '門市店員為 <b>test0000001</b> 記錄消費 HK$1000，發放 <b>+10 印花</b>', 'Staff logged HK$1000 purchase for <b>test0000001</b>, issued <b>+10 Stamps</b>', '2026-07-09 07:35:57'),
(33, '📌', '<b>test0000001</b> 提交了「test」任務審核', '<b>test0000001</b> submitted test task for review', '2026-07-09 07:36:17');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `upload_time` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending',
  `amount` decimal(10,2) DEFAULT NULL,
  `reward_granted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `user_id`, `image_path`, `upload_time`, `status`, `amount`, `reward_granted`) VALUES
(1, 7, 'uploads/receipt_7_1783488283.jpeg', '2026-07-08 05:24:43', 'rejected', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `id` int(11) NOT NULL,
  `icon` varchar(20) NOT NULL,
  `name_zh` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `cost` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`id`, `icon`, `name_zh`, `name_en`, `cost`) VALUES
(5, 'test', 'test', '', 100);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('bottom_bar', '[{\"id\":\"loyalty\",\"labelZh\":\"獎賞\",\"labelEn\":\"Rewards\",\"icon\":\"🎁\",\"visible\":true},{\"id\":\"membership\",\"labelZh\":\"會員\",\"labelEn\":\"Membership\",\"icon\":\"💳\",\"visible\":true},{\"id\":\"draw\",\"labelZh\":\"抽獎\",\"labelEn\":\"Lucky Draw\",\"icon\":\"🎡\",\"visible\":true},{\"id\":\"vouchers\",\"labelZh\":\"優惠券\",\"labelEn\":\"Vouchers\",\"icon\":\"🏷️\",\"visible\":true},{\"id\":\"profile\",\"labelZh\":\"個人\",\"labelEn\":\"Profile\",\"icon\":\"👤\",\"visible\":true}]'),
('extra_refer_reward', '2'),
('extra_share_reward', '2'),
('logo_image_url', ''),
('logo_text', 'CASA & CO.'),
('logo_type', 'text'),
('otp_enabled', '1'),
('otp_expiry_days', '30'),
('otp_method', 'phone'),
('system_mode', 'points');

-- --------------------------------------------------------

--
-- Table structure for table `staff_users`
--

CREATE TABLE `staff_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff_users`
--

INSERT INTO `staff_users` (`id`, `username`, `password`, `name`, `created_at`) VALUES
(1, 'staffA', '$2y$10$B59sDhfAFIoX1st3u9pCI.ZbnV2..oRX//2aSd9FZiVCHxeYrmuza', 'stadd', '2026-07-08 09:56:49'),
(2, 'staff111', '$2y$10$RRjGYEYu5M9/.0w6spGip.tdYlHZeRf96y5LSde7JeFc65GDiu2ye', 'staff111', '2026-07-09 07:33:35');

-- --------------------------------------------------------

--
-- Table structure for table `tasks_config`
--

CREATE TABLE `tasks_config` (
  `id` int(11) NOT NULL,
  `name_zh` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `desc_zh` text DEFAULT NULL,
  `desc_en` text DEFAULT NULL,
  `reward_type` varchar(50) NOT NULL,
  `reward_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tasks_config`
--

INSERT INTO `tasks_config` (`id`, `name_zh`, `name_en`, `desc_zh`, `desc_en`, `reward_type`, `reward_amount`) VALUES
(1, 'test', 'test', 'test', 'test', 'stamps', 2),
(2, 'test', 'test', 'test', 'test', 'points', 1000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gender` varchar(20) DEFAULT 'Prefer not to say',
  `points` int(11) DEFAULT 0,
  `stamps` int(11) DEFAULT 0,
  `spins` int(11) DEFAULT 0,
  `biometric_enabled` int(11) DEFAULT 0,
  `joined_date` datetime DEFAULT current_timestamp(),
  `qr_code` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT '',
  `last_login_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `email`, `gender`, `points`, `stamps`, `spins`, `biometric_enabled`, `joined_date`, `qr_code`, `phone`, `last_login_date`) VALUES
(5, 'test', '$2y$10$jAzPTj49GJtWIOCb3P05WOxq45eBO5js7ycwx/BoeqJHoZNumcfVG', 'test', 'test@gmail.com', 'Prefer not to say', 200, 3, 1, 1, '2026-07-08 04:51:41', 'USER_QR_F5CC47EB8E', '', '2026-07-09 01:13:13'),
(6, 'test2', '$2y$10$3NnIWdfqbiLGY/M8X9W3RuncLRNgsueGq/4YJVA.VKRslcrDbvXk.', 'test2', 'test2@gmail.com', 'Prefer not to say', 200, 1, 2, 0, '2026-07-08 05:17:00', 'USER_QR_9F991A2BBC', '', '2026-07-09 01:13:13'),
(7, 'test3', '$2y$10$DCNXj8cOuxpjiitGhcD2FOk62MBhT3NG5aI8uK7TnyVuBQ2hmZVEK', 'test3', 'test3@gmail.com', 'Prefer not to say', 2914, 1, 0, 1, '2026-07-08 05:17:17', 'USER_QR_D75C00B559', '', '2026-07-09 07:08:51'),
(8, 'test01', '$2y$10$KXe.uRt69NZbSW84njXghubw6EAjxCb7Vga/sOsqDhJ4KDn45Wzay', 'test01', 'test01@mail.com', 'Prefer not to say', 201, 1, 1, 1, '2026-07-08 08:10:27', 'USER_QR_E275D0BC29', '', '2026-07-09 01:13:13'),
(9, 'test002', '$2y$10$qyYb8Ov9lIktR90INpw3ueh5Myz7WYMA3IDMycvIsAmbVA5Xqyo2m', 'test002', 'test002@gmail.com', 'Prefer not to say', 0, 0, 2, 0, '2026-07-08 23:45:46', 'USER_QR_1170B6B83E', '', '2026-07-09 01:13:13'),
(10, 'test004', '$2y$10$k7FqYCy5g1Sm.I.uZ5QmlOPG5ts725ycavwvD1xKZRJ.LKudYJ1we', 'test004', 'test004@gmail.com', 'Prefer not to say', 0, 0, 2, 0, '2026-07-08 23:46:02', 'USER_QR_C746CC6D79', '', '2026-07-09 01:13:13'),
(11, 'test005', '$2y$10$D3PZCseXmcx5PcnIuIuVVevVy19.oXIouEA65qpu/3fdvM6W9Rpym', 'test005', 'test005@gmail.com', 'Prefer not to say', 0, 0, 2, 0, '2026-07-08 23:46:40', 'USER_QR_C843F7CED2', '', '2026-07-09 01:13:13'),
(12, 'test000', '$2y$10$ZmMEOc.cUGhucHXpje.3XOVVjua2anWU68X8NmMLYavgQDBCu/2X2', 'test000', 'test000@gmail.com', 'Prefer not to say', 0, 0, 2, 0, '2026-07-09 01:30:49', 'USER_QR_4DD7418CE5', '', '2026-07-09 01:30:49'),
(13, 'test0010', '$2y$10$ff8zoIEsKXkSZ.yfJzsXz.r4dbWfowYAu8QYUeHIjh1Mhrrq/DYaG', 'test0010', 'test0010@gmail.com', 'Prefer not to say', 0, 0, 2, 0, '2026-07-09 01:32:07', 'USER_QR_26AFA4C5C8', '', '2026-07-09 01:32:07'),
(14, 'test00101', '$2y$10$NEFxBEO8/xEWQqDCmjESyOrgMriPbId7nLMZTaeXOnor0VqH1bIDi', 'test00101', 'test00101@gmail.com', 'Prefer not to say', 0, 0, 2, 0, '2026-07-09 01:37:16', 'USER_QR_97AEF8A69E', '', '2026-07-09 01:37:16'),
(15, 'test666', '$2y$10$.Z.8HntyX5y4LFLNNapt4OCSMuMgp.2ZIY69VKHkYGp/fazGKY3fG', 'test666', 'test666@gmail.com', 'Prefer not to say', 0, 0, 2, 0, '2026-07-09 07:13:16', 'USER_QR_8C6E7F744C', '0167872285', '2026-07-09 07:13:16'),
(16, 'test0000001', '$2y$10$5yoHuB2U1GIW/rwmkTTXs.ozGS1SWhBI9PiWKBzldqIsHVVF3snGS', 'test0000001', '', 'Prefer not to say', 100, 10, 2, 1, '2026-07-09 07:34:47', 'USER_QR_FC81FF9F22', '0123456789', '2026-07-09 07:34:47');

-- --------------------------------------------------------

--
-- Table structure for table `user_tasks`
--

CREATE TABLE `user_tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `task_type` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_tasks`
--

INSERT INTO `user_tasks` (`id`, `user_id`, `task_type`, `status`, `created_at`) VALUES
(1, 7, 'share_photo', 'approved', '2026-07-08 08:05:13'),
(2, 16, '1', 'pending', '2026-07-09 07:36:17');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `expiry_date` date NOT NULL,
  `used` int(11) DEFAULT 0,
  `used_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `user_id`, `name`, `code`, `expiry_date`, `used`, `used_date`) VALUES
(4, 7, 'test', 'WTCHZR', '2026-08-07', 0, NULL),
(5, 7, 'test', 'X6YD98', '2026-08-07', 1, NULL),
(7, 8, 'TESTTTTTTTT', '11', '2026-08-07', 0, NULL),
(10, 11, '全單 9 折迎新優惠', 'WELCOME10', '2026-08-07', 0, NULL),
(13, 14, '全單 9 折迎新優惠', 'LC4JMN', '2026-08-08', 0, NULL),
(14, 15, '全單 9 折迎新優惠', '2KKD37', '2026-08-08', 0, NULL),
(15, 16, '全單 9 折迎新優惠', 'Q4BSAT', '2026-08-08', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wheel_prizes`
--

CREATE TABLE `wheel_prizes` (
  `id` int(11) NOT NULL,
  `name_zh` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `value` varchar(100) NOT NULL,
  `weight` int(11) NOT NULL,
  `color` varchar(20) DEFAULT '#788A6E'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wheel_prizes`
--

INSERT INTO `wheel_prizes` (`id`, `name_zh`, `name_en`, `type`, `value`, `weight`, `color`) VALUES
(9, 'test', '', 'points', '1', 100, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `staff_users`
--
ALTER TABLE `staff_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `tasks_config`
--
ALTER TABLE `tasks_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `qr_code` (`qr_code`);

--
-- Indexes for table `user_tasks`
--
ALTER TABLE `user_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `wheel_prizes`
--
ALTER TABLE `wheel_prizes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staff_users`
--
ALTER TABLE `staff_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tasks_config`
--
ALTER TABLE `tasks_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `user_tasks`
--
ALTER TABLE `user_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `wheel_prizes`
--
ALTER TABLE `wheel_prizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `vouchers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
