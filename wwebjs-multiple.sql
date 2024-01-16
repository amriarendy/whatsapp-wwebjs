-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 11, 2023 at 06:21 PM
-- Server version: 8.0.30
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wa_blast`
--

-- --------------------------------------------------------

--
-- Table structure for table `autoreplies`
--

CREATE TABLE `autoreplies` (
  `id` int NOT NULL,
  `keyword` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `response` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `media` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `user_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `category` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int NOT NULL,
  `contact_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_type` enum('PERSONAL','GROUP') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `contact_category` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `user_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `information`
--

CREATE TABLE `information` (
  `id` int NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `about` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `telephone` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `website` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `address` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `logo` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `owner` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `image` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `video` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `frame` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `information`
--

INSERT INTO `information` (`id`, `title`, `about`, `telephone`, `website`, `email`, `address`, `logo`, `owner`, `image`, `video`, `frame`, `updated_at`, `created_at`) VALUES
(1, 'Whatsapp Bot Gateway - By Goican Developers', 'WhatsApp Bot adalah program komputer yang menggunakan teknologi kecerdasan buatan (AI) dan otomatisasi untuk berinteraksi dengan pengguna melalui aplikasi WhatsApp. Bot ini dirancang untuk menjawab pertanyaan, memberikan informasi, dan melakukan berbagai tugas sesuai dengan permintaan pengguna. Ide di balik WhatsApp Bot adalah memberikan solusi cepat dan efisien untuk berbagai kebutuhan pengguna, tanpa perlu interaksi langsung dengan manusia.', '6282246686900', 'https://goican.id/', 'goican.id@gmail.com', 'Jl. Gedung Kesenian No.1, Ps. Baru Kec. Sawah Besar, Kota Jakarta Pusat, DKI Jakarta, Indonesia', 'http://127.0.0.1/wa-api/public/files/favicon.ico', 'amriarendy', NULL, NULL, NULL, '2023-08-11 18:20:54', '2023-08-11 18:20:54');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int NOT NULL,
  `media` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `user_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint NOT NULL,
  `message` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `message_categories` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `message_media` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `message_status` enum('success','failed','waiting') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'waiting',
  `send_to` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `message_schedule` datetime DEFAULT NULL,
  `user_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seo`
--

CREATE TABLE `seo` (
  `id` int NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `keyword` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `author` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `favicon` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `image` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `indexing` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `url` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `copyright` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `seo`
--

INSERT INTO `seo` (`id`, `title`, `description`, `keyword`, `author`, `favicon`, `image`, `indexing`, `url`, `copyright`, `updated_at`, `created_at`) VALUES
(1, 'Whatsapp Bot Gateway', 'WhatsApp Bot adalah program komputer yang menggunakan teknologi kecerdasan buatan (AI) dan otomatisasi untuk berinteraksi dengan pengguna melalui aplikasi WhatsApp. Bot ini dirancang untuk menjawab pertanyaan, memberikan informasi, dan melakukan berbagai tugas sesuai dengan permintaan pengguna. Ide di balik WhatsApp Bot adalah memberikan solusi cepat dan efisien untuk berbagai kebutuhan pengguna, tanpa perlu interaksi langsung dengan manusia.', 'whatsapp, whatsapp api, whatsapp bot, whatsapp gateway, whatsapp tools, whatsapp broadcast', 'amriarendy', 'http://127.0.0.1/wa-api/public/files/favicon.ico', 'http://127.0.0.1/wa-api/public/files/Whatsapp Bot Api.png', 'noindex/nofollow', 'https://goican.id/', 'Copyright Goican Developers. All Rights Reserved', NULL, '2023-08-11 18:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int NOT NULL,
  `connect_attempt` enum('Connected','Disconnected') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `token` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `url_gateway` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `callback` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `webhook` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `limited` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `connect_attempt`, `token`, `url_gateway`, `callback`, `webhook`, `limited`, `updated_at`, `created_at`) VALUES
(1, 'Disconnected', 'c08ab0ff99440cf5cf33246ce0e72eab', 'http://127.0.0.1:3000/', 'https://domain.com/callback.php', 'https://domain.com/webhook.php', 300, NULL, '2023-08-11 18:13:57');

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` int NOT NULL,
  `template_title` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `template_content` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `template_media` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `user_id` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `password` text CHARACTER SET latin1 COLLATE latin1_swedish_ci,
  `picture` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `gender` enum('male','female') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `phone` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `api_key` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `level` enum('admin','user') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'user',
  `session_attempt` enum('Connected','Disconnected') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Disconnected',
  `status` enum('actived','deactived') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'actived',
  `chunk` int DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `picture`, `gender`, `phone`, `api_key`, `level`, `session_attempt`, `status`, `chunk`, `updated_at`, `created_at`) VALUES
(1, 'Bot Administrator', 'bot@admin.com', '5791e2ae4c5aa539440442afd1417c0e3ee9a784', 'https://source.unsplash.com/360x200?games', 'male', '6282246686900', 'e5b528274baebacea9bf6b0766e2262f', 'admin', 'Disconnected', 'actived', 100, '2023-08-11 18:18:41', '2023-08-11 18:10:03'),
(2, 'Bot User', 'bot@user.com', '533a083a5b8c321e54f35fc1e80d4dc68fc1e047', 'https://source.unsplash.com/360x200?games', 'male', '6282246686900', 'c7a0cfaf35067d380c9b19c193d9b6ad', 'user', 'Disconnected', 'actived', 100, '2023-08-11 18:18:51', '2023-08-11 18:10:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `autoreplies`
--
ALTER TABLE `autoreplies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `information`
--
ALTER TABLE `information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seo`
--
ALTER TABLE `seo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
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
-- AUTO_INCREMENT for table `autoreplies`
--
ALTER TABLE `autoreplies`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `information`
--
ALTER TABLE `information`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seo`
--
ALTER TABLE `seo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
