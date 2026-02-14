-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2026 at 04:52 PM
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
-- Database: `visitorqr`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2022_11_15_222448_create_visitors_table', 1),
(6, '2022_11_16_162102_drop_uuid_column_from_visitors_table', 1),
(7, '2022_11_16_162205_add_uuid_column_as_primary_key_on_visitors_table', 1),
(8, '2022_11_29_163251_drop_columns_on_visitors_table', 1),
(9, '2022_11_29_163414_add_columns_to_visitors_table', 1),
(10, '2026_02_08_005838_increase_ic_number_column_size_in_visitors_table', 2),
(11, '2026_02_08_011649_add_new_fields_to_visitors_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@visitorqr.com', '2026-02-07 16:40:01', '$2y$10$rCoW/d9hAgsMxPOIOHGEoOw/tCHlymPzy0AAFA4cVv4cuSu5GCvbu', 'Ju7i9EWfXS', '2026-02-07 16:40:01', '2026-02-07 16:40:01'),
(2, 'John Smith', 'john.smith@company.com', '2026-02-07 16:40:01', '$2y$10$qC5HQ2MRN01grvbJ2iL3KesMH5firCtdh2vDWuYyLCi62ruIZG1fW', 'OFwPRypBL1', '2026-02-07 16:40:01', '2026-02-07 16:40:01'),
(3, 'Jane Doe', 'jane.doe@company.com', '2026-02-07 16:40:01', '$2y$10$O2U/SzRpRHcVPRoXlsQaz.NBxQrA/PkeFIv/cMmLEGhygONeJ0RSW', '1XDzR04DR5', '2026-02-07 16:40:01', '2026-02-07 16:40:01'),
(4, 'Mike Johnson', 'mike.johnson@company.com', '2026-02-07 16:40:01', '$2y$10$hNxMImIypnaJnSIx1eTBROHytib/wMaZD136E8/yFvYQGB5lmgTu.', 'w6L1tRQX0I', '2026-02-07 16:40:01', '2026-02-07 16:40:01'),
(5, 'Sarah Wilson', 'sarah.wilson@company.com', '2026-02-07 16:40:01', '$2y$10$qe7pI8uYo3LuxXS2XWVfTullTgjyihB1lkXUaHKdF1PNowKdJ/BD2', '7LnpCqqhod', '2026-02-07 16:40:01', '2026-02-07 16:40:01');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `uuid` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ic_number` varchar(50) NOT NULL,
  `vehicle_plate_number` varchar(50) NOT NULL,
  `visit_datetime` datetime NOT NULL,
  `added_by` bigint(20) UNSIGNED DEFAULT NULL,
  `check_in_datetime` datetime DEFAULT NULL,
  `check_in_verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `check_out_datetime` datetime DEFAULT NULL,
  `check_out_verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `visitor_type` varchar(255) NOT NULL DEFAULT 'standard',
  `purpose` text DEFAULT NULL,
  `access_level` varchar(255) NOT NULL DEFAULT 'standard',
  `qr_code` varchar(255) DEFAULT NULL,
  `qr_data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`uuid`, `name`, `ic_number`, `vehicle_plate_number`, `visit_datetime`, `added_by`, `check_in_datetime`, `check_in_verified_by`, `check_out_datetime`, `check_out_verified_by`, `created_at`, `updated_at`, `first_name`, `last_name`, `email`, `phone`, `company`, `visitor_type`, `purpose`, `access_level`, `qr_code`, `qr_data`) VALUES
('0471c9b6-9cec-4857-9cfc-4aa3e58c70e7', 'Jean Marc Aguilar', 'N/A', 'N/A', '2026-02-11 04:51:00', 1, NULL, NULL, NULL, NULL, '2026-02-08 18:51:52', '2026-02-08 18:51:52', NULL, NULL, 'jeanmarcaguilar829@gmail.com', '09705667137', 'SSS', 'vip', 'Meeting', 'standard', NULL, NULL),
('1e894844-1df1-4ee1-8fb3-4a24e7007515', 'Jean Marc Aguilar', 'N/A', 'N/A', '2026-02-10 15:01:00', 1, NULL, NULL, NULL, NULL, '2026-02-08 05:38:06', '2026-02-08 05:38:06', NULL, NULL, 'jeanmarcaguilar829@gmail.com', '09705667137', 'SSS', 'vip', 'Meeting', 'standard', NULL, NULL),
('2ce37e82-1fcb-4899-be21-a1fc9e5d768f', 'Test Visitor', 'N/A', 'N/A', '2026-02-10 05:03:59', 1, NULL, NULL, NULL, NULL, '2026-02-08 21:03:59', '2026-02-08 21:03:59', NULL, NULL, 'test@example.com', '+1234567890', 'Test Company', 'vip', 'Test Purpose', 'standard', NULL, NULL),
('44de649e-295b-43f8-8267-584cf8882ca8', 'Luke Chiang', 'N/A', 'N/A', '2026-02-12 09:43:00', 1, '2026-02-09 08:42:46', 1, '2026-02-09 08:43:19', 1, '2026-02-09 00:42:19', '2026-02-09 00:43:19', NULL, NULL, 'lukaschiang@gmail.com', '0912345678', 'Starlink', 'vip', 'Meeting', 'standard', NULL, NULL),
('5ecb0be4-df37-4540-84a1-e65111286cea', 'Jean Marc Aguilar', 'N/A', 'N/A', '2026-02-17 06:02:00', 1, '2026-02-09 06:39:12', 1, NULL, NULL, '2026-02-08 21:01:12', '2026-02-08 22:39:12', NULL, NULL, 'jeanmarcaguilar829@gmail.com', '09705667137', 'SSS', 'vip', 'Meeting', 'standard', NULL, NULL),
('657007e4-1970-48f0-951f-bfc5888e2fdb', 'Jean Marc', 'N/A', 'N/A', '2026-02-12 05:42:00', 1, NULL, NULL, NULL, NULL, '2026-02-08 18:40:49', '2026-02-08 18:40:49', NULL, NULL, 'jeanmarcaguilar829@gmail.com', '09705667137', 'SSS', 'vip', 'Meeting', 'standard', NULL, NULL),
('85ac637f-0456-11f1-82fb-088fc386c9a6', 'Jean Marc Aguilar', '', '', '2026-02-10 00:00:00', 1, NULL, NULL, NULL, NULL, '2026-02-07 18:27:03', '2026-02-07 18:27:03', 'Jean Marc', 'Aguilar', 'jeanmarcaguilar829@gmail.com', '09705667137', 'SSS', 'vip', 'dwdawdw', 'vip', 'ZGF0YTppbWFnZS9wbmc7YmFzZTY0LGlWQk9SdzBLR2dvQUFBQU5TVWhFVWdBQUFVVUFBQUZGQ0FJQUFBRDBGbWdLQUFBQUNYQklXWE1BQUE3RUFBQU94QUdWS3c0YkFBQUtXRWxFUVZSNG5PM2R3VzRrT1E0RndQWmkvditYWis4RlF3MUJKS1Y2RTNIdHFzeDAyZzlDRXhUMTgrKy8vLzRCSXZ6djlnTUFaZVFaY3NnejVKQm55Q0hQa0VPZUl', '{\"id\":2,\"name\":\"Jean Marc Aguilar\",\"email\":\"jeanmarcaguilar829@gmail.com\",\"access\":\"vip\",\"date\":\"2026-02-10\",\"type\":\"VISITOR_QR\",\"version\":\"2.0\"}'),
('85adead3-0456-11f1-82fb-088fc386c9a6', 'Jean Marc Aguilar', '', '', '2026-02-08 00:00:00', 1, NULL, NULL, NULL, NULL, '2026-02-07 18:28:41', '2026-02-07 18:28:41', 'Jean Marc', 'Aguilar', 'jeanmarcaguilar829@gmail.com', '09705667137', 'SSS', 'professional', 'dwdwdwd', 'vip', 'ZGF0YTppbWFnZS9wbmc7YmFzZTY0LGlWQk9SdzBLR2dvQUFBQU5TVWhFVWdBQUFVVUFBQUZGQ0FJQUFBRDBGbWdLQUFBQUNYQklXWE1BQUE3RUFBQU94QUdWS3c0YkFBQUt0MGxFUVZSNG5PM2R3WTdkT2c0RndQVGcvZjh2Wi9aR29FQWdLU2tIVmR1KzEzYTdjeUNFb0tpZjM3OS8vd0lpL08vMkF3QnQ1Qmx5eURQa2tHZklJYytRUTU0aGh', '{\"id\":3,\"name\":\"Jean Marc Aguilar\",\"email\":\"jeanmarcaguilar829@gmail.com\",\"access\":\"vip\",\"date\":\"2026-02-08\",\"type\":\"VISITOR_QR\",\"version\":\"2.0\"}'),
('bdfee881-b616-4916-ad32-f5623f7c70c2', 'Jean Marc Aguilar', '21651651651651651651651651', '13[,[p,d[pa,d[pa,w', '2026-02-12 02:59:00', 1, '2026-02-08 01:03:21', 1, NULL, NULL, '2026-02-07 17:02:07', '2026-02-07 17:03:21', NULL, NULL, NULL, NULL, NULL, 'standard', NULL, 'standard', NULL, NULL),
('d9550db6-80d8-4675-a5f1-b9dfc29878f7', 'Jean Marc Aguilar', 'N/A', 'N/A', '2026-02-13 20:06:00', 1, NULL, NULL, NULL, NULL, '2026-02-08 19:00:42', '2026-02-08 19:00:42', NULL, NULL, 'jeanmarcaguilar829@gmail.com', '09705667137', 'SSS', 'vip', 'Meeting', 'standard', NULL, NULL),
('efd5c42e-0095-4c43-bbd7-c71eb069a1f9', 'dwddwd', 'wdwdwdw', 'dwdwddwdwd', '2026-02-12 17:22:00', 1, NULL, NULL, NULL, NULL, '2026-02-07 19:21:46', '2026-02-07 19:21:46', NULL, NULL, NULL, NULL, NULL, 'standard', NULL, 'standard', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `visitors_check_in_verified_by_foreign` (`check_in_verified_by`),
  ADD KEY `visitors_check_out_verified_by_foreign` (`check_out_verified_by`),
  ADD KEY `visitors_added_by_foreign` (`added_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `visitors`
--
ALTER TABLE `visitors`
  ADD CONSTRAINT `visitors_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visitors_check_in_verified_by_foreign` FOREIGN KEY (`check_in_verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visitors_check_out_verified_by_foreign` FOREIGN KEY (`check_out_verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
