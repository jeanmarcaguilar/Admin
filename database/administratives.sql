-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 06, 2026 at 03:44 PM
-- Server version: 10.11.14-MariaDB-ubu2204
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `admin_administratives`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` bigint(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `module` enum('facilities','documents','legal','visitors','departments','system') NOT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `module`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `created_at`) VALUES
(1, 1, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-24 09:50:24'),
(2, 1, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-24 10:15:37'),
(3, 1, 'visitors', 'APPROVE_PREREG', 'visitor_preregistrations', NULL, '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"visitor_code\":\"VIS-839357\"}', '::1', '2026-02-24 10:27:13'),
(4, 1, 'visitors', 'CHECK_OUT', 'visitor_logs', 3, NULL, NULL, '::1', '2026-02-24 10:38:31'),
(5, 1, 'visitors', 'CHECK_IN', 'visitor_logs', NULL, NULL, '{\"visit_code\":\"VL-2026-634834\",\"visitor_id\":16}', '::1', '2026-02-24 10:38:34'),
(6, 1, 'visitors', 'CHECK_OUT', 'visitor_logs', 16, NULL, NULL, '::1', '2026-02-24 10:38:37'),
(7, 1, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 6, NULL, '{\"reservation_code\":\"RES-2026-329536\",\"facility_id\":4,\"type\":\"vip\",\"auto_tagged\":1}', '::1', '2026-02-24 10:43:02'),
(8, 1, 'facilities', 'CREATE_MAINTENANCE', 'facility_maintenance', 1, NULL, '{\"ticket_number\":\"MNT-2026-912162\",\"facility_id\":\"5\",\"equipment_id\":6,\"issue_type\":\"equipment\"}', '::1', '2026-02-24 10:48:06'),
(9, 1, 'facilities', 'UPDATE_MAINTENANCE', 'facility_maintenance', 1, NULL, '{\"status\":\"in_progress\"}', '::1', '2026-02-24 10:48:18'),
(10, 1, 'facilities', 'UPDATE_MAINTENANCE', 'facility_maintenance', 1, NULL, '{\"status\":\"resolved\"}', '::1', '2026-02-24 10:48:27'),
(11, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-02-24 10:50:05'),
(12, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-24 13:57:17'),
(13, 1, 'documents', 'VERIFY_ARCHIVE_PIN', 'documents', NULL, NULL, '{\"verified\":true}', '::1', '2026-02-24 14:14:36'),
(14, 1, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 5, NULL, '{\"status\":\"approved\"}', '::1', '2026-02-24 18:36:09'),
(15, 1, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 3, NULL, '{\"status\":\"approved\"}', '::1', '2026-02-24 18:36:29'),
(16, 1, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 13, NULL, '{\"reservation_code\":\"RES-2026-735991\",\"facility_id\":9,\"type\":\"vip\",\"auto_tagged\":0}', '::1', '2026-02-24 19:55:49'),
(17, 1, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 11, NULL, '{\"status\":\"approved\"}', '::1', '2026-02-24 19:57:13'),
(18, 1, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 12, NULL, '{\"status\":\"approved\"}', '::1', '2026-02-24 19:58:36'),
(19, 1, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 10, NULL, '{\"status\":\"approved\"}', '::1', '2026-02-24 19:58:43'),
(20, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-02-24 20:38:15'),
(21, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-24 20:38:18'),
(22, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-24 20:39:53'),
(23, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-02-24 20:41:28'),
(24, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-25 07:50:57'),
(25, 1, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 13, NULL, '{\"status\":\"ongoing\"}', '::1', '2026-02-25 08:27:06'),
(26, 1, 'documents', 'VERIFY_CONFIDENTIAL_PIN', 'documents', NULL, NULL, '{\"verified\":true}', '::1', '2026-02-25 10:11:14'),
(27, 1, 'documents', 'VERIFY_ARCHIVE_PIN', 'documents', NULL, NULL, '{\"verified\":true}', '::1', '2026-02-25 10:12:42'),
(28, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-02-25 10:52:03'),
(29, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-25 10:52:07'),
(30, 1, 'documents', 'VERIFY_CONFIDENTIAL_PIN', 'documents', NULL, NULL, '{\"verified\":true}', '::1', '2026-02-25 10:53:59'),
(31, 1, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 14, NULL, '{\"reservation_code\":\"RES-2026-954307\",\"facility_id\":4,\"type\":\"vip\",\"auto_tagged\":1}', '::1', '2026-02-25 11:05:41'),
(32, 1, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 15, NULL, '{\"reservation_code\":\"RES-2026-906512\",\"facility_id\":5,\"type\":\"vip\",\"auto_tagged\":1}', '::1', '2026-02-25 11:07:21'),
(33, 1, 'facilities', 'COMPLETE_RESERVATION', 'facility_reservations', 15, NULL, '{\"completed\":true}', '::1', '2026-02-25 11:38:33'),
(34, 1, 'facilities', 'COMPLETE_RESERVATION', 'facility_reservations', 14, NULL, '{\"completed\":true}', '::1', '2026-02-25 11:38:39'),
(35, 1, 'documents', 'GRANT_ACCESS', 'document_access', 1, NULL, '{\"user_id\":1,\"permission\":\"download\"}', '::1', '2026-02-25 12:20:31'),
(36, 1, 'documents', 'REVOKE_ACCESS', 'document_access', 1, NULL, NULL, '::1', '2026-02-25 12:51:22'),
(37, 1, 'documents', 'FOLDER_ACCESS_DENIED', 'department_folders', NULL, NULL, '{\"folder\":\"Financial\",\"user_id\":1,\"role\":\"super_admin\",\"reason\":\"invalid_pin\"}', '::1', '2026-02-25 13:11:52'),
(38, 1, 'documents', 'FOLDER_ACCESS', 'department_folders', NULL, NULL, '{\"folder\":\"Financial\",\"user_id\":1,\"role\":\"super_admin\",\"access_method\":\"pin_verified\"}', '::1', '2026-02-25 13:12:06'),
(39, 1, 'documents', 'FOLDER_ACCESS', 'department_folders', NULL, NULL, '{\"folder\":\"HR 1\",\"user_id\":1,\"role\":\"super_admin\",\"access_method\":\"pin_verified\"}', '::1', '2026-02-25 13:12:34'),
(40, 1, 'documents', 'FOLDER_ACCESS_DENIED', 'department_folders', NULL, NULL, '{\"folder\":\"Legal\",\"user_id\":1,\"role\":\"super_admin\",\"reason\":\"invalid_pin\"}', '::1', '2026-02-25 13:15:57'),
(41, 1, 'documents', 'FOLDER_ACCESS_DENIED', 'department_folders', NULL, NULL, '{\"folder\":\"Legal\",\"user_id\":1,\"role\":\"super_admin\",\"reason\":\"invalid_pin\"}', '::1', '2026-02-25 13:16:06'),
(42, 1, 'documents', 'FOLDER_ACCESS', 'department_folders', NULL, NULL, '{\"folder\":\"Legal\",\"user_id\":1,\"role\":\"super_admin\",\"access_method\":\"pin_verified\"}', '::1', '2026-02-25 13:16:12'),
(43, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Legal\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-25 13:28:48'),
(44, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-25 13:36:09'),
(45, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-02-25 14:02:41'),
(46, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-02-25 14:02:41'),
(47, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-26 10:38:22'),
(48, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"HR 1\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 10:43:37'),
(49, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Legal\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 10:44:22'),
(50, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Financial\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 12:34:52'),
(51, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Legal\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 12:52:57'),
(52, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Legal\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 13:22:19'),
(53, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Core Transaction\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 13:31:39'),
(54, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Core Transaction\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 13:31:39'),
(55, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Core Transaction\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 13:31:39'),
(56, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Core Transaction\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 13:31:39'),
(57, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Credit\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 13:37:39'),
(58, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Credit\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 13:37:39'),
(59, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"IT\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 13:39:05'),
(60, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administration\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:07:47'),
(61, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administration\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:07:47'),
(62, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administration\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:07:47'),
(63, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administration\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:07:47'),
(64, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administration\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:07:47'),
(65, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administration\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:07:47'),
(66, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administration\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:07:47'),
(67, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administration\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:07:47'),
(68, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administration\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:07:47'),
(69, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Operations\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:08:10'),
(70, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"HR 1\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 14:13:39'),
(71, 1, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-26 14:21:54'),
(72, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Core 1\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(73, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Core 1\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(74, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Core 1\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(75, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(76, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(77, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(78, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(79, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(80, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(81, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(82, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(83, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:09:24'),
(84, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Log 1\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-02-26 15:18:11'),
(85, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-26 17:08:11'),
(86, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-02-26 17:10:06'),
(87, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-26 17:40:33'),
(88, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-02-26 17:48:18'),
(89, 1, 'visitors', 'APPROVE_PREREG', 'visitor_preregistrations', NULL, '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"visitor_code\":\"VIS-755805\"}', '::1', '2026-02-26 17:48:55'),
(90, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-02-26 17:49:52'),
(91, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-03-01 11:07:52'),
(92, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-03-01 11:09:20'),
(93, 1, 'documents', 'VERIFY_ARCHIVE_PIN', 'documents', NULL, NULL, '{\"verified\":true}', '::1', '2026-03-01 11:10:05'),
(94, 1, 'legal', 'VERIFY_LEGAL_OTP', 'legal_access', NULL, NULL, '{\"user_id\":1,\"verified\":true}', '::1', '2026-03-01 11:40:18'),
(95, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-03-02 16:25:54'),
(96, 1, 'legal', 'VERIFY_LEGAL_OTP', 'legal_access', NULL, NULL, '{\"user_id\":1,\"verified\":true}', '::1', '2026-03-02 16:32:10'),
(97, 1, 'visitors', 'PREREGISTER', 'visitor_preregistrations', NULL, NULL, '{\"prereg_code\":\"PR-2026-291867\",\"visitor_name\":\"jessel obina\"}', '::1', '2026-03-02 17:01:31'),
(98, 1, 'visitors', 'APPROVE_PREREG', 'visitor_preregistrations', NULL, '{\"status\":\"pending\"}', '{\"status\":\"approved\",\"visitor_code\":\"VIS-239662\"}', '::1', '2026-03-02 17:01:35'),
(99, 1, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 16, NULL, '{\"reservation_code\":\"RES-2026-341914\",\"facility_id\":1,\"type\":\"regular\",\"auto_tagged\":0}', '::1', '2026-03-02 17:47:58'),
(100, 1, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 16, NULL, '{\"status\":\"approved\"}', '::1', '2026-03-02 17:48:41'),
(101, 1, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 17, NULL, '{\"reservation_code\":\"RES-2026-838705\",\"facility_id\":5,\"type\":\"regular\",\"auto_tagged\":0}', '::1', '2026-03-02 17:50:53'),
(102, 1, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 17, NULL, '{\"status\":\"approved\"}', '::1', '2026-03-02 17:51:16'),
(103, 1, 'visitors', 'CHECK_IN', 'visitor_logs', NULL, NULL, '{\"visit_code\":\"VL-2026-510447\",\"visitor_id\":18}', '::1', '2026-03-02 17:56:02'),
(104, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-03-03 16:15:46'),
(105, 1, 'visitors', 'CHECK_OUT', 'visitor_logs', 17, NULL, NULL, '::1', '2026-03-03 16:24:36'),
(106, 1, 'visitors', 'CHECK_IN', 'visitor_logs', NULL, NULL, '{\"visit_code\":\"VL-2026-996461\",\"visitor_id\":18,\"visitor_type\":\"regular\"}', '::1', '2026-03-03 16:25:33'),
(107, 1, 'visitors', 'CHECK_OUT', 'visitor_logs', 18, NULL, NULL, '::1', '2026-03-03 16:25:52'),
(108, 1, 'visitors', 'CHECK_IN', 'visitor_logs', NULL, NULL, '{\"visit_code\":\"VL-2026-506476\",\"visitor_id\":17,\"visitor_type\":\"regular\"}', '::1', '2026-03-03 16:27:11'),
(109, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-03-03 16:31:14'),
(110, 1, 'legal', 'UPDATE_WORKFLOW', 'legal_cases', 1, NULL, '{\"workflow_step\":\"under_review\"}', '::1', '2026-03-03 16:39:53'),
(111, 1, 'legal', 'UPDATE_WORKFLOW', 'legal_cases', 1, NULL, '{\"workflow_step\":\"for_hearing\"}', '::1', '2026-03-03 16:40:09'),
(112, 1, 'legal', 'UPDATE_WORKFLOW', 'legal_cases', 1, NULL, '{\"workflow_step\":\"ongoing_investigation\"}', '::1', '2026-03-03 16:40:18'),
(113, 1, 'legal', 'RENDER_VERDICT', 'legal_cases', 1, NULL, '{\"verdict\":\"guilty_warning\"}', '::1', '2026-03-03 16:40:37'),
(114, 1, 'legal', 'VERIFY_LEGAL_PIN', 'legal', NULL, NULL, '{\"tab\":\"legal\",\"verified\":true}', '::1', '2026-03-03 17:21:09'),
(115, 1, 'visitors', 'CHECK_OUT', 'visitor_logs', 19, NULL, NULL, '::1', '2026-03-03 18:30:11'),
(116, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-03-03 18:35:28'),
(117, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-03-03 18:44:49'),
(118, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-03-03 19:03:10'),
(119, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-03-03 19:17:30'),
(120, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '::1', '2026-03-03 19:24:44'),
(121, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '::1', '2026-03-03 19:25:12'),
(123, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '::1', '2026-03-04 16:37:18'),
(124, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '2001:4451:4722:1f00:fd27:3a61:a675:b8c9', '2026-03-05 15:28:44'),
(125, NULL, 'system', 'LOGIN', 'users', 2, NULL, '{\"employee_id\":\"jeanmarc\"}', '120.28.161.186', '2026-03-05 15:32:16'),
(126, 1, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '2001:4451:4722:1f00:fd27:3a61:a675:b8c9', '2026-03-05 15:44:51'),
(127, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '2001:4451:4722:1f00:fd27:3a61:a675:b8c9', '2026-03-05 15:49:59'),
(128, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '2001:4451:4722:1f00:fd27:3a61:a675:b8c9', '2026-03-05 16:17:20'),
(129, 1, 'documents', 'VERIFY_FOLDER_PIN', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":1,\"role\":\"super_admin\",\"verified\":true}', '2001:4451:4722:1f00:fd27:3a61:a675:b8c9', '2026-03-05 16:18:17'),
(130, 1, 'legal', 'VERIFY_LEGAL_PIN', 'legal', NULL, NULL, '{\"tab\":\"legal\",\"verified\":true}', '2001:4451:4722:1f00:fd27:3a61:a675:b8c9', '2026-03-05 16:20:24'),
(131, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '2001:4451:4722:1f00:fd27:3a61:a675:b8c9', '2026-03-05 16:33:16'),
(132, NULL, 'system', 'LOGIN', 'users', 3, NULL, '{\"employee_id\":\"johnmark\"}', '175.158.203.143', '2026-03-05 19:01:05'),
(133, NULL, 'system', 'LOGIN', 'users', 3, NULL, '{\"employee_id\":\"johnmark\"}', '175.158.203.143', '2026-03-05 22:24:56'),
(134, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 18, NULL, '{\"reservation_code\":\"RES-2026-242497\",\"facility_id\":8,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-05 22:29:41'),
(135, 3, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 18, NULL, '{\"status\":\"approved\"}', '175.158.203.143', '2026-03-05 22:30:08'),
(136, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 19, NULL, '{\"reservation_code\":\"RES-2026-725118\",\"facility_id\":7,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-05 22:31:45'),
(137, 3, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 19, NULL, '{\"status\":\"approved\"}', '175.158.203.143', '2026-03-05 22:31:58'),
(138, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 20, NULL, '{\"reservation_code\":\"RES-2026-969079\",\"facility_id\":1,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-05 22:33:05'),
(139, 3, 'facilities', 'CANCEL_RESERVATION', 'facility_reservations', 19, NULL, '{\"reason\":\"\"}', '175.158.203.143', '2026-03-05 22:33:40'),
(140, 3, 'facilities', 'VALIDATE_RESERVATION', 'facility_reservations', 20, NULL, '{\"validated\":true}', '175.158.203.143', '2026-03-05 22:33:46'),
(141, 3, 'facilities', 'VALIDATE_RESERVATION', 'facility_reservations', 20, NULL, '{\"validated\":true}', '175.158.203.143', '2026-03-05 22:33:50'),
(142, 3, 'facilities', 'CANCEL_RESERVATION', 'facility_reservations', 20, NULL, '{\"reason\":\"\"}', '175.158.203.143', '2026-03-05 22:33:53'),
(143, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 21, NULL, '{\"reservation_code\":\"RES-2026-517541\",\"facility_id\":7,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-05 22:34:31'),
(144, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 22, NULL, '{\"reservation_code\":\"RES-2026-143825\",\"facility_id\":2,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-05 22:35:16'),
(145, 3, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 21, NULL, '{\"status\":\"approved\"}', '175.158.203.143', '2026-03-05 22:35:27'),
(146, 3, 'facilities', 'UPDATE_RESERVATION_STATUS', 'facility_reservations', 22, NULL, '{\"status\":\"approved\"}', '175.158.203.143', '2026-03-05 22:35:36'),
(147, NULL, 'system', 'LOGIN', 'users', 2, NULL, '{\"employee_id\":\"jeanmarc\"}', '45.64.120.238', '2026-03-06 01:02:07'),
(148, 2, 'documents', 'FOLDER_ACCESS_DENIED', 'department_folders', NULL, NULL, '{\"folder\":\"Administrative\",\"user_id\":2,\"role\":\"admin\",\"reason\":\"invalid_pin\"}', '45.64.120.238', '2026-03-06 01:04:00'),
(149, NULL, 'system', 'LOGIN', 'users', 2, NULL, '{\"employee_id\":\"jeanmarc\"}', '45.64.120.238', '2026-03-06 01:19:13'),
(150, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '58.97.176.19', '2026-03-06 08:05:50'),
(151, 1, 'system', 'LOGOUT', 'users', 1, NULL, NULL, '58.97.176.19', '2026-03-06 08:09:10'),
(152, NULL, 'system', 'LOGIN', 'users', 2, NULL, '{\"employee_id\":\"jeanmarc\"}', '136.158.39.92', '2026-03-06 12:06:43'),
(153, 2, 'system', 'LOGOUT', 'users', 2, NULL, NULL, '136.158.39.92', '2026-03-06 12:08:42'),
(154, NULL, 'system', 'LOGIN', 'users', 3, NULL, '{\"employee_id\":\"johnmark\"}', '175.158.203.143', '2026-03-06 13:22:35'),
(155, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 23, NULL, '{\"reservation_code\":\"RES-2026-405084\",\"facility_id\":8,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-06 14:13:53'),
(156, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 24, NULL, '{\"reservation_code\":\"RES-2026-781447\",\"facility_id\":1,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-06 14:14:47'),
(157, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 25, NULL, '{\"reservation_code\":\"RES-2026-499610\",\"facility_id\":3,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-06 14:16:04'),
(158, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 26, NULL, '{\"reservation_code\":\"RES-2026-175144\",\"facility_id\":4,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-06 14:17:25'),
(159, 3, 'facilities', 'CREATE_RESERVATION', 'facility_reservations', 27, NULL, '{\"reservation_code\":\"RES-2026-572812\",\"facility_id\":6,\"type\":\"regular\",\"auto_tagged\":0}', '175.158.203.143', '2026-03-06 14:24:07'),
(160, NULL, 'system', 'LOGIN', 'users', 1, NULL, '{\"employee_id\":\"admin\"}', '2001:4451:4722:1f00:50fc:1b45:a1a2:acc1', '2026-03-06 15:37:38');

-- --------------------------------------------------------

--
-- Table structure for table `board_resolutions`
--

CREATE TABLE `board_resolutions` (
  `resolution_id` int(11) NOT NULL,
  `resolution_code` varchar(30) NOT NULL,
  `title` varchar(300) NOT NULL,
  `resolution_type` enum('policy','financial','operational','appointment','amendment','dissolution','other') NOT NULL,
  `meeting_date` date NOT NULL,
  `meeting_type` enum('regular','special','emergency','annual') NOT NULL DEFAULT 'regular',
  `attendees` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of board member names' CHECK (json_valid(`attendees`)),
  `quorum_present` tinyint(1) NOT NULL DEFAULT 1,
  `resolution_text` longtext DEFAULT NULL,
  `minutes_text` longtext DEFAULT NULL COMMENT 'Meeting minutes',
  `votes_for` int(11) DEFAULT NULL,
  `votes_against` int(11) DEFAULT NULL,
  `votes_abstain` int(11) DEFAULT NULL,
  `passed` tinyint(1) NOT NULL DEFAULT 1,
  `effective_date` date DEFAULT NULL,
  `secretary_name` varchar(200) DEFAULT NULL,
  `chairman_name` varchar(200) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `status` enum('draft','approved','filed','superseded') NOT NULL DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `board_resolutions`
--

INSERT INTO `board_resolutions` (`resolution_id`, `resolution_code`, `title`, `resolution_type`, `meeting_date`, `meeting_type`, `attendees`, `quorum_present`, `resolution_text`, `minutes_text`, `votes_for`, `votes_against`, `votes_abstain`, `passed`, `effective_date`, `secretary_name`, `chairman_name`, `document_id`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'BR-2026-001', 'Approval of New Branch Expansion ΓÇö Taguig City', 'operational', '2026-01-15', 'special', '[\"Maria C. Santos (President)\", \"Pedro T. Reyes (VP - Operations)\", \"Ana M. Garcia (VP - Finance)\", \"Carlos L. Mendoza (Director)\", \"Elena R. Cruz (Director)\", \"Roberto F. Tan (Independent Director)\", \"Grace P. Lim (Corporate Secretary)\"]', 1, 'RESOLVED, as it is hereby resolved, that the Board of Directors of Microfinancial Management Corporation approves the establishment of a new branch office in Taguig City, with an initial capital outlay not exceeding Five Million Pesos (Γé▒5,000,000.00).\n\nRESOLVED FURTHER, that the President, Ms. Maria C. Santos, is hereby authorized to execute all documents necessary for the establishment of said branch.\n\nRESOLVED FINALLY, that the Corporate Secretary is hereby directed to file the necessary reports with the Bangko Sentral ng Pilipinas and the Securities and Exchange Commission.', NULL, 6, 0, 1, 1, '2026-02-01', 'Grace P. Lim', 'Maria C. Santos', NULL, 'approved', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'BR-2026-002', 'Adoption of Anti-Money Laundering Policy 2026', 'policy', '2026-01-15', 'special', '[\"Maria C. Santos (President)\", \"Pedro T. Reyes (VP - Operations)\", \"Ana M. Garcia (VP - Finance)\", \"Carlos L. Mendoza (Director)\", \"Elena R. Cruz (Director)\", \"Roberto F. Tan (Independent Director)\", \"Grace P. Lim (Corporate Secretary)\"]', 1, 'RESOLVED, that the Board adopts the updated Anti-Money Laundering and Counter-Terrorism Financing Policy for year 2026 in compliance with R.A. 9160 as amended.\n\nRESOLVED FURTHER, that the Compliance Officer shall ensure dissemination and training within thirty (30) days.', NULL, 7, 0, 0, 1, '2026-01-15', 'Grace P. Lim', 'Maria C. Santos', NULL, 'filed', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'BR-2026-003', 'Appointment of External Auditor for FY 2026', 'appointment', '2026-02-10', 'regular', '[\"Maria C. Santos (President)\", \"Pedro T. Reyes (VP - Operations)\", \"Ana M. Garcia (VP - Finance)\", \"Carlos L. Mendoza (Director)\", \"Grace P. Lim (Corporate Secretary)\"]', 1, 'RESOLVED, that the Board appoints Santos, Garcia & Co., CPAs as the external auditor for fiscal year 2026 with audit fees not exceeding Γé▒350,000.00.', NULL, 5, 0, 0, 1, '2026-02-10', 'Grace P. Lim', 'Maria C. Santos', NULL, 'approved', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'BR-2026-004', 'Amendment to Loan Interest Rate Policy', 'amendment', '2026-02-10', 'regular', '[\"Maria C. Santos (President)\", \"Pedro T. Reyes (VP - Operations)\", \"Ana M. Garcia (VP - Finance)\", \"Carlos L. Mendoza (Director)\", \"Grace P. Lim (Corporate Secretary)\"]', 1, 'RESOLVED, that effective March 1, 2026, the maximum allowable interest rate for unsecured microfinance loans shall be reduced from 3.00% to 2.50% per month in compliance with BSP Circular No. 1098.\n\nRESOLVED FURTHER, that existing loans with rates exceeding 2.50% shall be restructured upon request of the borrower.\n\nRESOLVED FINALLY, that the Finance Department shall prepare the revised rate schedule within fifteen (15) days.', NULL, 4, 1, 0, 1, '2026-03-01', 'Grace P. Lim', 'Maria C. Santos', NULL, 'approved', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `collateral_registry`
--

CREATE TABLE `collateral_registry` (
  `collateral_id` int(11) NOT NULL,
  `collateral_code` varchar(30) NOT NULL,
  `loan_doc_id` int(11) DEFAULT NULL,
  `borrower_name` varchar(300) NOT NULL,
  `collateral_type` enum('real_estate','vehicle','equipment','inventory','receivables','deposit','jewelry','other') NOT NULL,
  `description` varchar(500) NOT NULL,
  `serial_plate_no` varchar(100) DEFAULT NULL COMMENT 'For vehicles/equipment',
  `title_deed_no` varchar(100) DEFAULT NULL COMMENT 'For real estate',
  `location_address` varchar(500) DEFAULT NULL,
  `appraised_value` decimal(15,2) DEFAULT NULL,
  `appraisal_date` date DEFAULT NULL,
  `appraiser_name` varchar(200) DEFAULT NULL,
  `lien_status` enum('active','released','foreclosed','pending_release') NOT NULL DEFAULT 'active',
  `lien_recorded_date` date DEFAULT NULL,
  `lien_registry_no` varchar(100) DEFAULT NULL COMMENT 'Registry of Deeds annotation number',
  `insurance_policy` varchar(100) DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `release_authorized_by` int(11) DEFAULT NULL,
  `foreclosure_date` date DEFAULT NULL,
  `foreclosure_case_id` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collateral_registry`
--

INSERT INTO `collateral_registry` (`collateral_id`, `collateral_code`, `loan_doc_id`, `borrower_name`, `collateral_type`, `description`, `serial_plate_no`, `title_deed_no`, `location_address`, `appraised_value`, `appraisal_date`, `appraiser_name`, `lien_status`, `lien_recorded_date`, `lien_registry_no`, `insurance_policy`, `insurance_expiry`, `release_date`, `release_authorized_by`, `foreclosure_date`, `foreclosure_case_id`, `document_id`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'COL-2026-001', 1, 'Roberto A. Mendoza', 'vehicle', 'Toyota Hilux 2023 ΓÇö 4x2 G AT, White', 'ABC 1234', NULL, '123 Rizal Avenue, Makati City', 980000.00, '2026-01-08', 'Philippine Appraisal Corp.', 'active', '2026-01-10', 'CM-2026-MKT-001', 'INS-POL-2026-TY-001', '2027-01-10', NULL, NULL, NULL, NULL, NULL, 'Vehicle in good condition, no prior liens', 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'COL-2026-002', 3, 'ABC Corporation', 'real_estate', 'Commercial Lot ΓÇö 250 sqm with 2-storey building', NULL, 'TCT No. T-654321', '789 Commercial Drive, Taguig City', 8500000.00, '2026-01-28', 'Santos & Associates Appraisers', 'active', '2026-02-01', 'REM-2026-TGG-045', 'INS-POL-2026-RE-002', '2027-02-01', NULL, NULL, NULL, NULL, NULL, 'Property has existing tenants, lease disclosed', 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'COL-2026-003', 5, 'Michael B. Tan', 'equipment', 'HP Indigo 7900 Digital Press ΓÇö Brand New Industrial Printing Equipment', 'IND-2026-HP-001', NULL, '567 Bonifacio Avenue, Taguig City', 800000.00, '2026-02-03', 'Industrial Valuers Inc.', 'active', '2026-02-05', 'CM-2026-TGG-012', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Equipment to be delivered and inspected', 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'COL-2026-004', NULL, 'Patricia D. Reyes', 'jewelry', '18K Gold Necklace with Diamond Pendant (2.5 carats), Gold Bracelet', NULL, NULL, 'Pledged at Main Branch', 320000.00, '2025-11-15', 'GoldStar Appraisal Center', 'active', '2025-11-20', 'PLG-2025-MKT-089', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Items stored in vault, safe deposit box #12', 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(5, 'COL-2026-005', 1, 'Roberto A. Mendoza', 'deposit', 'Time Deposit Account ΓÇö Microfinancial Savings', NULL, NULL, NULL, 50000.00, '2026-01-10', NULL, 'active', '2026-01-10', 'HD-2026-TD-001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Hold-out on time deposit as additional security', 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(6, 'COL-2026-006', 7, 'Fernando C. Aguilar', 'real_estate', 'Residential Lot ΓÇö 120 sqm with single-storey bungalow, BF Homes Subdivision', NULL, 'TCT No. T-112233', '45 Mahogany Lane, BF Homes, Para├▒aque City', 3200000.00, '2026-02-10', 'Metro Manila Appraisal Corp.', 'pending_release', '2026-02-12', 'REM-2026-PNQ-078', 'INS-POL-2026-RE-003', '2027-02-12', NULL, NULL, NULL, NULL, NULL, 'Clean title, no prior encumbrances, flood-free zone', 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(7, 'COL-2026-007', NULL, 'Josefina R. Bautista', 'receivables', 'Assignment of Receivables ΓÇö Monthly rental income from 3 commercial stalls at Pasig Public Market', NULL, NULL, 'Pasig Public Market, Stalls 12A, 12B, 12C', 450000.00, '2026-01-20', NULL, 'active', '2026-01-25', 'AR-2026-PSG-015', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Monthly rental income of Γé▒37,500 assigned to Lender for 12 months', 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(8, 'COL-2026-008', NULL, 'Ricardo V. Santos', 'vehicle', 'Mitsubishi L300 2024 Exceed ΓÇö Delivery Van, Silver', 'DEF 5678', NULL, '234 Maharlika Highway, Cainta, Rizal', 650000.00, '2026-02-01', 'Philippine Appraisal Corp.', 'active', '2026-02-05', 'CM-2026-RZL-033', 'INS-POL-2026-VH-004', '2027-02-05', NULL, NULL, NULL, NULL, NULL, 'Vehicle in excellent condition, used for commercial delivery', 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `department_folders`
--

CREATE TABLE `department_folders` (
  `folder_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `folder_name` varchar(300) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(10) DEFAULT '📁',
  `color` varchar(10) DEFAULT '#6B7280',
  `bg_color` varchar(10) DEFAULT '#F3F4F6',
  `is_locked` tinyint(1) NOT NULL DEFAULT 1,
  `pin_hash` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department_folders`
--

INSERT INTO `department_folders` (`folder_id`, `department`, `folder_name`, `description`, `icon`, `color`, `bg_color`, `is_locked`, `pin_hash`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'HR 1', 'Talent Acquisition & Workforce Entry', 'Recruitment, onboarding, and employee entry documents', '👥', '#059669', '#D1FAE5', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00'),
(2, 'HR 2', 'Talent Development & Career Pathing', 'Training records, career development, and performance docs', '📝', '#2563EB', '#DBEAFE', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00'),
(3, 'HR 3', 'Workforce Operations & Time Management', 'Attendance, timekeeping, and workforce scheduling', '🎓', '#7C3AED', '#EDE9FE', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00'),
(4, 'HR 4', 'Compensation & HR Intelligence', 'Payroll, benefits, compensation, and HR analytics', '📋', '#DC2626', '#FEE2E2', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00'),
(5, 'Core 1', 'Client Services & Financial Transactions', 'Client-facing services, account management, and transactions', '🏦', '#D97706', '#FEF3C7', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00'),
(6, 'Core 2', 'Institutional Oversight & Financial Control', 'Internal audit, risk management, and financial controls', '📊', '#059669', '#D1FAE5', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00'),
(7, 'Log 1', 'Smart Supply Chain & Procurement Management', 'Procurement, vendor management, and supply chain', '🚚', '#0891B2', '#CFFAFE', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00'),
(8, 'Log 2', 'Fleet and Transportation Operations', 'Fleet management, vehicle tracking, and logistics', '📦', '#9333EA', '#F3E8FF', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00'),
(9, 'Financial', 'Financial Management', 'Budget, accounting, and financial reporting', '💵', '#16A34A', '#DCFCE7', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00'),
(10, 'Administrative', 'Administrative Services', 'General admin, office management, and support services', '⚖️', '#B91C1C', '#FEE2E2', 1, NULL, 1, '2026-02-24 08:00:00', '2026-02-24 08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `demand_letters`
--

CREATE TABLE `demand_letters` (
  `demand_id` int(11) NOT NULL,
  `demand_code` varchar(30) NOT NULL,
  `loan_doc_id` int(11) DEFAULT NULL,
  `case_id` int(11) DEFAULT NULL,
  `borrower_name` varchar(300) NOT NULL,
  `borrower_address` varchar(500) DEFAULT NULL,
  `amount_demanded` decimal(15,2) NOT NULL,
  `demand_type` enum('first_notice','second_notice','final_demand','notice_of_default','notice_of_foreclosure') NOT NULL DEFAULT 'first_notice',
  `letter_body` longtext DEFAULT NULL,
  `attorney_name` varchar(200) DEFAULT NULL,
  `sent_date` date DEFAULT NULL,
  `sent_via` enum('registered_mail','personal_service','email','courier') DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `response_deadline` date DEFAULT NULL,
  `borrower_responded` tinyint(1) NOT NULL DEFAULT 0,
  `response_summary` text DEFAULT NULL,
  `escalated_to_litigation` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('draft','sent','received','responded','expired','escalated') NOT NULL DEFAULT 'draft',
  `document_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `demand_letters`
--

INSERT INTO `demand_letters` (`demand_id`, `demand_code`, `loan_doc_id`, `case_id`, `borrower_name`, `borrower_address`, `amount_demanded`, `demand_type`, `letter_body`, `attorney_name`, `sent_date`, `sent_via`, `received_date`, `response_deadline`, `borrower_responded`, `response_summary`, `escalated_to_litigation`, `status`, `document_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'DL-2026-001', 5, NULL, 'Michael B. Tan', '567 Bonifacio Avenue, Taguig City', 535000.00, 'first_notice', 'DEMAND LETTER\n\nFebruary 10, 2026\n\nMR. MICHAEL B. TAN\n567 Bonifacio Avenue\nTaguig City\n\nDear Mr. Tan,\n\nGREETINGS!\n\nWe write on behalf of our client, MICROFINANCIAL MANAGEMENT CORPORATION, to formally demand payment of your outstanding obligation in the amount of FIVE HUNDRED THIRTY-FIVE THOUSAND PESOS (Γé▒535,000.00), inclusive of principal, interest, and penalties.\n\nRecords show that you have failed to pay your monthly amortizations due on January 5, 2026 and February 5, 2026 on your Chattel Mortgage Loan (LD-2026-005).\n\nWe hereby demand that you settle the aforementioned amount within FIFTEEN (15) days from receipt of this letter. Failure to comply shall constrain us to take the necessary legal action to protect our client\'s interests, including but not limited to foreclosure of the chattel mortgage.\n\nPlease govern yourself accordingly.\n\nVery truly yours,\n\nATTY. FRANCISCO J. DELA ROSA\nCounsel for Microfinancial Management Corp.', 'Atty. Francisco J. Dela Rosa', '2026-02-10', 'registered_mail', NULL, '2026-02-25', 0, NULL, 0, 'sent', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'DL-2026-002', NULL, 1, 'ABC Corporation', '789 Commercial Drive, Taguig City', 2650000.00, 'final_demand', 'FINAL DEMAND WITH NOTICE OF FORECLOSURE\n\nFebruary 1, 2026\n\nABC CORPORATION\nAttn: Mr. Pedro T. Lim, President\n789 Commercial Drive, Taguig City\n\nDear Mr. Lim,\n\nDespite previous demands, your corporation has failed to settle its outstanding obligation of TWO MILLION SIX HUNDRED FIFTY THOUSAND PESOS (Γé▒2,650,000.00).\n\nThis is your FINAL DEMAND. Should you fail to pay within TEN (10) days, we shall institute foreclosure proceedings on the mortgaged property covered by TCT No. T-654321.\n\nThis letter serves as the NOTICE OF DEFAULT required under Section 3 of your Loan Agreement.\n\nATTY. FRANCISCO J. DELA ROSA', 'Atty. Francisco J. Dela Rosa', '2026-02-01', 'personal_service', '2026-02-03', '2026-02-13', 1, NULL, 0, 'responded', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'DL-2026-003', 5, 5, 'Michael B. Tan', '567 Bonifacio Avenue, Taguig City', 545000.00, 'second_notice', 'SECOND DEMAND LETTER\n\nFebruary 15, 2026\n\nMR. MICHAEL B. TAN\n567 Bonifacio Avenue, Taguig City\n\nDear Mr. Tan,\n\nThis is a SECOND DEMAND further to our letter dated February 10, 2026 which remains unheeded.\n\nYour total outstanding obligation has now increased to FIVE HUNDRED FORTY-FIVE THOUSAND PESOS (Γé▒545,000.00) inclusive of accrued penalties.\n\nWe DEMAND payment within FIVE (5) days from receipt hereof, otherwise we shall be constrained to institute foreclosure proceedings on the mortgaged chattel and file the appropriate case in court.\n\nATTY. FRANCISCO J. DELA ROSA\nCounsel for Microfinancial Management Corp.', 'Atty. Francisco J. Dela Rosa', '2026-02-15', 'personal_service', NULL, '2026-02-20', 0, NULL, 0, 'sent', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'DL-2026-004', 6, NULL, 'Rosario M. Villanueva', '89 Sampaguita Street, Brgy. Holy Spirit, Quezon City', 195000.00, 'first_notice', 'DEMAND LETTER\n\nFebruary 14, 2026\n\nMS. ROSARIO M. VILLANUEVA\n89 Sampaguita St., Brgy. Holy Spirit, Quezon City\n\nDear Ms. Villanueva,\n\nWe write to remind you that your monthly amortization of Γé▒16,250.00 due on February 5, 2026 remains unpaid.\n\nKindly settle the overdue amount together with applicable penalties within FIFTEEN (15) days from receipt of this letter to avoid further legal action.\n\nPlease contact our office to discuss possible restructuring options if you are experiencing financial difficulties.\n\nATTY. MARIA TERESA R. SANTOS\nCounsel for Microfinancial Management Corp.', 'Atty. Maria Teresa R. Santos', '2026-02-14', 'registered_mail', NULL, '2026-03-01', 0, NULL, 0, 'sent', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `document_id` int(11) NOT NULL,
  `document_code` varchar(30) NOT NULL,
  `title` varchar(300) NOT NULL,
  `folder_name` varchar(200) DEFAULT NULL COMMENT 'Folder title for department organization',
  `category_id` int(11) DEFAULT NULL,
  `document_type` enum('memo','contract','report','policy','form','certificate','invoice','receipt','letter','other') NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_name` varchar(300) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT 1,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `qr_code_id` int(11) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `confidentiality` enum('public','internal','confidential','restricted') NOT NULL DEFAULT 'internal',
  `status` enum('draft','active','archived','retained') NOT NULL DEFAULT 'active',
  `source_system` varchar(50) DEFAULT NULL COMMENT 'Originating integrated system',
  `archived_at` datetime DEFAULT NULL,
  `retained_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`document_id`, `document_code`, `title`, `folder_name`, `category_id`, `document_type`, `description`, `file_path`, `file_name`, `file_size`, `file_type`, `version`, `tags`, `qr_code_id`, `uploaded_by`, `department`, `confidentiality`, `status`, `source_system`, `archived_at`, `retained_at`, `created_at`, `updated_at`) VALUES
(1, 'DOC-2026-00001', 'Q4 2025 Financial Statement', NULL, 1, 'report', 'Quarterly financial report for microfinancial operations', '/uploads/documents/fin_q4_2025.pdf', 'fin_q4_2025.pdf', 2456789, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'confidential', 'active', NULL, NULL, NULL, '2026-02-24 10:03:01', '2026-02-26 13:56:24'),
(2, 'DOC-2026-00002', 'Employee Handbook v3.2', NULL, 2, 'policy', 'Updated employee handbook with new policies', '/uploads/documents/emp_handbook_v3.pdf', 'emp_handbook_v3.pdf', 5123456, 'application/pdf', 1, NULL, NULL, 2, 'Administrative', 'internal', 'active', NULL, NULL, NULL, '2026-02-24 10:03:01', '2026-02-26 13:56:24'),
(3, 'DOC-2026-00003', 'Anti-Money Laundering Policy 2026', NULL, 3, 'policy', 'AML compliance policy for microfinancial institution', '/uploads/documents/aml_policy_2026.pdf', 'aml_policy_2026.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 3, 'Administrative', 'restricted', 'active', NULL, NULL, NULL, '2026-02-24 10:03:01', '2026-02-26 13:56:24'),
(4, 'DOC-2026-00004', 'Standard Operating Procedures - Loans', NULL, 4, 'form', 'SOP for processing microfinancial loan applications', '/uploads/documents/sop_loans.pdf', 'sop_loans.pdf', 3456789, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', NULL, NULL, NULL, '2026-02-24 10:03:01', '2026-02-26 13:56:24'),
(5, 'DOC-2026-00005', 'Board Resolution No. 2026-001', NULL, 8, 'certificate', 'Resolution approving new branch expansion', '/uploads/documents/br_2026_001.pdf', 'br_2026_001.pdf', 987654, 'application/pdf', 1, NULL, NULL, 1, 'Administrative', 'restricted', 'active', NULL, NULL, NULL, '2026-02-24 10:03:01', '2026-02-26 13:56:24'),
(6, 'DOC-2026-00006', 'Vendor Service Agreement - IT Support', NULL, 3, 'contract', 'Annual IT support contract with TechServ Inc.', '/uploads/documents/vendor_it_2026.pdf', 'vendor_it_2026.pdf', 2345678, 'application/pdf', 1, NULL, NULL, 1, 'Administrative', 'confidential', 'active', NULL, NULL, NULL, '2026-02-24 10:03:01', '2026-02-26 13:56:24'),
(7, 'DOC-2026-00007', 'Client Loan Application Form Template', NULL, 6, 'form', 'Standardized loan application form for microfinancial clients', '/uploads/documents/loan_app_form.pdf', 'loan_app_form.pdf', 567890, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'public', 'active', NULL, NULL, NULL, '2026-02-24 10:03:01', '2026-02-26 13:56:24'),
(8, 'DOC-2026-00008', 'Employee Master Data Export - January 2026', 'HR4 Employee Records', 2, 'report', 'Full employee master data export from HR4 HCM module', '/uploads/documents/hr4/emp_master_jan2026.xlsx', 'emp_master_jan2026.xlsx', 1845230, 'application/vnd.openxmlformats-officedocument.spre', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(9, 'DOC-2026-00009', 'Employee Onboarding Checklist Template', 'HR4 Employee Records', 2, 'form', 'Standard checklist for new employee onboarding process', '/uploads/documents/hr4/onboarding_checklist.pdf', 'onboarding_checklist.pdf', 234567, 'application/pdf', 1, NULL, NULL, 2, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(10, 'DOC-2026-00010', 'Employment Contract Template - Regular', 'HR4 Employee Records', 2, 'contract', 'Standard employment contract for regular full-time employees', '/uploads/documents/hr4/contract_template_regular.docx', 'contract_template_regular.docx', 345678, 'application/vnd.openxmlformats-officedocument.word', 1, NULL, NULL, 3, 'HR 4', 'confidential', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(11, 'DOC-2026-00011', 'Employment Contract Template - Probationary', 'HR4 Employee Records', 2, 'contract', 'Employment contract template for probationary period employees', '/uploads/documents/hr4/contract_template_probi.docx', 'contract_template_probi.docx', 312456, 'application/vnd.openxmlformats-officedocument.word', 1, NULL, NULL, 3, 'HR 4', 'confidential', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(12, 'DOC-2026-00012', 'Job Title & Position Classification Matrix', 'HR4 Employee Records', 2, 'report', 'Complete listing of all job titles, positions, and salary grades', '/uploads/documents/hr4/position_matrix_2026.pdf', 'position_matrix_2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(13, 'DOC-2026-00013', 'Employee Transfer Request Form', 'HR4 Employee Records', 2, 'form', 'Official form for inter-department or inter-branch employee transfers', '/uploads/documents/hr4/transfer_request_form.pdf', 'transfer_request_form.pdf', 189345, 'application/pdf', 1, NULL, NULL, 2, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(14, 'DOC-2026-00014', 'Employee Termination Clearance Form', 'HR4 Employee Records', 2, 'form', 'Clearance form required before processing employee separation', '/uploads/documents/hr4/termination_clearance.pdf', 'termination_clearance.pdf', 201456, 'application/pdf', 1, NULL, NULL, 2, 'HR 4', 'confidential', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(15, 'DOC-2026-00015', 'Department Organizational Chart 2026', 'HR4 Employee Records', 5, 'report', 'Updated organizational chart showing all departments and reporting lines', '/uploads/documents/hr4/org_chart_2026.pdf', 'org_chart_2026.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(16, 'DOC-2026-00016', 'Employee Status History Report - Q4 2025', 'HR4 Employee Records', 2, 'report', 'Quarterly report on employee status changes (promotions, transfers, terminations)', '/uploads/documents/hr4/emp_status_q4_2025.pdf', 'emp_status_q4_2025.pdf', 456789, 'application/pdf', 1, NULL, NULL, 2, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(17, 'DOC-2026-00017', 'Government ID Compliance Audit Report', 'HR4 Employee Records', 2, 'report', 'Audit report verifying completeness of employee government ID records (SSS, PhilHealth, Pag-IBIG, TIN)', '/uploads/documents/hr4/gov_id_audit_2026.pdf', 'gov_id_audit_2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(18, 'DOC-2026-00018', 'Payroll Summary Report - January 2026', 'HR4 Payroll', 9, 'report', 'Monthly payroll computation summary with net pay breakdown per employee', '/uploads/documents/hr4/payroll_jan2026.pdf', 'payroll_jan2026.pdf', 1567890, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(19, 'DOC-2026-00019', 'Payroll Summary Report - February 2026', 'HR4 Payroll', 9, 'report', 'Monthly payroll computation summary for February pay period', '/uploads/documents/hr4/payroll_feb2026.pdf', 'payroll_feb2026.pdf', 1623456, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(20, 'DOC-2026-00020', 'Payroll Disbursement Batch #2026-001', 'HR4 Payroll', 9, 'report', 'Bank disbursement batch file for January 2026 1st half payroll', '/uploads/documents/hr4/disbursement_batch_001.pdf', 'disbursement_batch_001.pdf', 345678, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(21, 'DOC-2026-00021', 'Payroll Disbursement Batch #2026-002', 'HR4 Payroll', 9, 'report', 'Bank disbursement batch file for January 2026 2nd half payroll', '/uploads/documents/hr4/disbursement_batch_002.pdf', 'disbursement_batch_002.pdf', 356789, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(22, 'DOC-2026-00022', 'Payslip Distribution Log - January 2026', 'HR4 Payroll', 9, 'report', 'Log of generated and distributed payslips for all employees', '/uploads/documents/hr4/payslip_log_jan2026.pdf', 'payslip_log_jan2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'confidential', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(23, 'DOC-2026-00023', 'Compensation & Allowance Schedule 2026', 'HR4 Payroll', 9, 'policy', 'Approved compensation structure including all allowance types and rates', '/uploads/documents/hr4/comp_schedule_2026.pdf', 'comp_schedule_2026.pdf', 456789, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(24, 'DOC-2026-00024', 'Mandatory Deduction Reference Table 2026', 'HR4 Payroll', 9, 'policy', 'Updated SSS, PhilHealth, Pag-IBIG, and withholding tax tables for 2026', '/uploads/documents/hr4/deduction_tables_2026.pdf', 'deduction_tables_2026.pdf', 234567, 'application/pdf', 1, NULL, NULL, 4, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(25, 'DOC-2026-00025', 'Salary Adjustment Memo - Annual Increase 2026', 'HR4 Payroll', 9, 'memo', 'Approved memo for annual salary adjustment effective March 2026', '/uploads/documents/hr4/salary_adj_memo_2026.pdf', 'salary_adj_memo_2026.pdf', 189456, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(26, 'DOC-2026-00026', '13th Month Pay Computation Report 2025', 'HR4 Payroll', 9, 'report', 'Year-end 13th month pay computation for all active employees', '/uploads/documents/hr4/13th_month_2025.pdf', '13th_month_2025.pdf', 1123456, 'application/pdf', 1, NULL, NULL, 4, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(27, 'DOC-2026-00027', 'HMO Benefits Plan Summary 2026', 'HR4 Benefits', 20, 'policy', 'Summary of all available HMO plans and coverage for employees', '/uploads/documents/hr4/hmo_plans_2026.pdf', 'hmo_plans_2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 2, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(28, 'DOC-2026-00028', 'HMO Enrollment Masterlist - January 2026', 'HR4 Benefits', 20, 'report', 'Complete list of employees enrolled in HMO benefits', '/uploads/documents/hr4/hmo_enrolled_jan2026.xlsx', 'hmo_enrolled_jan2026.xlsx', 890123, 'application/vnd.openxmlformats-officedocument.spre', 1, NULL, NULL, 2, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(29, 'DOC-2026-00029', 'Benefits Enrollment Form Template', 'HR4 Benefits', 20, 'form', 'Standard form for employee benefits enrollment and dependent declaration', '/uploads/documents/hr4/benefits_enrollment_form.pdf', 'benefits_enrollment_form.pdf', 156789, 'application/pdf', 1, NULL, NULL, 2, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(30, 'DOC-2026-00030', 'HMO Dependent Coverage Policy', 'HR4 Benefits', 20, 'policy', 'Policy guidelines on eligible dependents and coverage limits', '/uploads/documents/hr4/hmo_dependent_policy.pdf', 'hmo_dependent_policy.pdf', 234567, 'application/pdf', 1, NULL, NULL, 2, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(31, 'DOC-2026-00031', 'Benefits Termination & Off-boarding Procedure', 'HR4 Benefits', 20, 'policy', 'Procedure for terminating benefits upon employee separation', '/uploads/documents/hr4/benefits_offboard_proc.pdf', 'benefits_offboard_proc.pdf', 198765, 'application/pdf', 1, NULL, NULL, 2, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(32, 'DOC-2026-00032', 'Login History Audit Report - January 2026', 'HR4 Security', 5, 'report', 'Monthly audit of user login activities, failed attempts, and IP blocks', '/uploads/documents/hr4/login_audit_jan2026.pdf', 'login_audit_jan2026.pdf', 345678, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(33, 'DOC-2026-00033', 'Information Security Policy v2.0', 'HR4 Security', 3, 'policy', 'Updated information security policy including access control and data protection', '/uploads/documents/hr4/infosec_policy_v2.pdf', 'infosec_policy_v2.pdf', 678901, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'active', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(34, 'DOC-2026-00034', 'Competency Framework Manual 2026', 'HR2 Competency', 11, 'policy', 'Comprehensive competency framework defining all organizational competencies', '/uploads/documents/hr2/competency_framework_2026.pdf', 'competency_framework_2026.pdf', 2345678, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(35, 'DOC-2026-00035', 'Role-to-Competency Mapping Matrix', 'HR2 Competency', 11, 'report', 'Matrix mapping each job role to required competencies and proficiency levels', '/uploads/documents/hr2/role_competency_matrix.xlsx', 'role_competency_matrix.xlsx', 1456789, 'application/vnd.openxmlformats-officedocument.spre', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(36, 'DOC-2026-00036', 'Skill Gap Analysis Report - Q4 2025', 'HR2 Competency', 11, 'report', 'Organization-wide skill gap analysis identifying development needs', '/uploads/documents/hr2/gap_analysis_q4_2025.pdf', 'gap_analysis_q4_2025.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'confidential', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(37, 'DOC-2026-00037', 'Competency Assessment Guidelines', 'HR2 Competency', 11, 'policy', 'Standard guidelines for conducting competency assessments and scoring', '/uploads/documents/hr2/competency_assessment_guide.pdf', 'competency_assessment_guide.pdf', 567890, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(38, 'DOC-2026-00038', 'Individual Development Plan Template', 'HR2 Competency', 11, 'form', 'Template for creating employee individual development plans based on competency gaps', '/uploads/documents/hr2/idp_template.pdf', 'idp_template.pdf', 234567, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(39, 'DOC-2026-00039', 'Competency Definitions Catalog', 'HR2 Competency', 11, 'report', 'Complete catalog of all defined competencies with behavioral indicators', '/uploads/documents/hr2/competency_catalog.pdf', 'competency_catalog.pdf', 890123, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(40, 'DOC-2026-00040', 'Assessment Category Reference Guide', 'HR2 Learning', 10, 'policy', 'Reference guide for all assessment categories used in the LMS module', '/uploads/documents/hr2/assessment_categories_guide.pdf', 'assessment_categories_guide.pdf', 345678, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(41, 'DOC-2026-00041', 'Self-Assessment Survey Template - Leadership', 'HR2 Learning', 10, 'form', 'Self-assessment questionnaire for leadership competency evaluation', '/uploads/documents/hr2/self_assessment_leadership.pdf', 'self_assessment_leadership.pdf', 189456, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(42, 'DOC-2026-00042', 'Quiz Bank - Microfinance Fundamentals', 'HR2 Learning', 10, 'form', 'Question bank for microfinance fundamentals assessment quiz', '/uploads/documents/hr2/quiz_microfinance_fund.pdf', 'quiz_microfinance_fund.pdf', 456789, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(43, 'DOC-2026-00043', 'Quiz Bank - AML & Compliance', 'HR2 Learning', 10, 'form', 'Question bank for anti-money laundering compliance assessment', '/uploads/documents/hr2/quiz_aml_compliance.pdf', 'quiz_aml_compliance.pdf', 412345, 'application/pdf', 1, NULL, NULL, 3, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(44, 'DOC-2026-00044', 'Assessment Results Summary - January 2026', 'HR2 Learning', 10, 'report', 'Monthly summary of all completed assessments with pass/fail statistics', '/uploads/documents/hr2/assessment_results_jan2026.pdf', 'assessment_results_jan2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'confidential', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(45, 'DOC-2026-00045', 'AI Evaluation Accuracy Report - Q4 2025', 'HR2 Learning', 10, 'report', 'Analysis of AI-assisted evaluation accuracy compared to manual grading', '/uploads/documents/hr2/ai_eval_accuracy_q4_2025.pdf', 'ai_eval_accuracy_q4_2025.pdf', 567890, 'application/pdf', 1, NULL, NULL, 1, 'HR 2', 'confidential', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(46, 'DOC-2026-00046', 'LMS User Guide for Employees', 'HR2 Learning', 10, 'policy', 'Step-by-step user guide for employees accessing the Learning Management System', '/uploads/documents/hr2/lms_user_guide.pdf', 'lms_user_guide.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 1, 'HR 2', 'public', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(47, 'DOC-2026-00047', 'Training Catalog - First Semester 2026', 'HR2 Training', 10, 'report', 'Complete catalog of available training programs for H1 2026', '/uploads/documents/hr2/training_catalog_h1_2026.pdf', 'training_catalog_h1_2026.pdf', 1567890, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(48, 'DOC-2026-00048', 'Training Needs Assessment Report 2026', 'HR2 Training', 10, 'report', 'Annual training needs assessment based on competency gaps and business objectives', '/uploads/documents/hr2/tna_report_2026.pdf', 'tna_report_2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(49, 'DOC-2026-00049', 'Training Room Booking Policy', 'HR2 Training', 10, 'policy', 'Guidelines and procedures for booking training rooms and facilities', '/uploads/documents/hr2/training_room_policy.pdf', 'training_room_policy.pdf', 234567, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(50, 'DOC-2026-00050', 'Training Evaluation Form - Post-Activity', 'HR2 Training', 10, 'form', 'Standard post-training evaluation form for participant feedback', '/uploads/documents/hr2/training_eval_form.pdf', 'training_eval_form.pdf', 156789, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'public', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(51, 'DOC-2026-00051', 'Training Completion Report - January 2026', 'HR2 Training', 10, 'report', 'Monthly report of all completed training sessions with attendance and evaluation scores', '/uploads/documents/hr2/training_completion_jan2026.pdf', 'training_completion_jan2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(52, 'DOC-2026-00052', 'Grant Request Approval Memo - External Course', 'HR2 Training', 10, 'memo', 'Approved grant request for employees attending external professional development course', '/uploads/documents/hr2/grant_request_memo_001.pdf', 'grant_request_memo_001.pdf', 189456, 'application/pdf', 1, NULL, NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(53, 'DOC-2026-00053', 'Training Materials - Customer Service Excellence', 'HR2 Training', 10, 'other', 'Slide deck and handouts for customer service excellence training program', '/uploads/documents/hr2/tm_customer_service.pdf', 'tm_customer_service.pdf', 3456789, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(54, 'DOC-2026-00054', 'Training Materials - Loan Processing Masterclass', 'HR2 Training', 10, 'other', 'Comprehensive training materials for loan officers on processing procedures', '/uploads/documents/hr2/tm_loan_processing.pdf', 'tm_loan_processing.pdf', 4567890, 'application/pdf', 1, NULL, NULL, 5, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(55, 'DOC-2026-00055', 'Training Materials - Financial Literacy for Staff', 'HR2 Training', 10, 'other', 'Training handout on basic financial literacy for all employees', '/uploads/documents/hr2/tm_financial_literacy.pdf', 'tm_financial_literacy.pdf', 2345678, 'application/pdf', 1, NULL, NULL, 4, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(56, 'DOC-2026-00056', 'Succession Planning Policy & Framework', 'HR2 Succession', 11, 'policy', 'Organizational policy on succession planning including talent pool criteria', '/uploads/documents/hr2/succession_policy_2026.pdf', 'succession_policy_2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 1, 'HR 2', 'restricted', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(57, 'DOC-2026-00057', 'Talent Pool Registry - Q1 2026', 'HR2 Succession', 11, 'report', 'Register of all employees identified in the talent pool for key positions', '/uploads/documents/hr2/talent_pool_q1_2026.pdf', 'talent_pool_q1_2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'restricted', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(58, 'DOC-2026-00058', 'Potential Successors Report - Critical Roles', 'HR2 Succession', 11, 'report', 'Identified successors for all critical management and leadership positions', '/uploads/documents/hr2/successors_critical_roles.pdf', 'successors_critical_roles.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'restricted', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(59, 'DOC-2026-00059', 'Promotion Proposal - Branch Manager Candidates', 'HR2 Succession', 11, 'report', 'Evaluated candidates for branch manager promotion with competency scores', '/uploads/documents/hr2/promo_branch_mgr_2026.pdf', 'promo_branch_mgr_2026.pdf', 456789, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'restricted', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(60, 'DOC-2026-00060', 'Promotion Offer Letter Template', 'HR2 Succession', 11, 'letter', 'Standard template for employee promotion offer letters', '/uploads/documents/hr2/promo_offer_template.docx', 'promo_offer_template.docx', 189456, 'application/vnd.openxmlformats-officedocument.word', 1, NULL, NULL, 2, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(61, 'DOC-2026-00061', 'AI Job Recommendation Analysis Report', 'HR2 Succession', 11, 'report', 'AI-generated job recommendation analysis for internal talent mobility', '/uploads/documents/hr2/ai_job_recommendation.pdf', 'ai_job_recommendation.pdf', 678901, 'application/pdf', 1, NULL, NULL, 1, 'HR 2', 'confidential', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(62, 'DOC-2026-00062', 'Employee Self-Service Portal User Manual', 'HR2 ESS', 5, 'policy', 'Complete user guide for the Employee Self-Service portal', '/uploads/documents/hr2/ess_user_manual.pdf', 'ess_user_manual.pdf', 1567890, 'application/pdf', 1, NULL, NULL, 1, 'HR 3', 'public', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(63, 'DOC-2026-00063', 'Leave Management Policy & Procedures', 'HR2 ESS', 2, 'policy', 'Company leave policies including types, accrual, and approval workflows', '/uploads/documents/hr2/leave_policy_2026.pdf', 'leave_policy_2026.pdf', 345678, 'application/pdf', 1, NULL, NULL, 2, 'HR 3', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(64, 'DOC-2026-00064', 'Procurement Policy & Procedures Manual 2026', 'Logs1 Procurement', 12, 'policy', 'Updated procurement policy with sourcing guidelines and approval thresholds', '/uploads/documents/logs1/procurement_policy_2026.pdf', 'procurement_policy_2026.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(65, 'DOC-2026-00065', 'Purchase Requisition Form Template', 'Logs1 Procurement', 12, 'form', 'Standard form for submitting purchase requisitions to procurement', '/uploads/documents/logs1/purchase_req_form.pdf', 'purchase_req_form.pdf', 189456, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(66, 'DOC-2026-00066', 'Purchase Order Log - January 2026', 'Logs1 Procurement', 12, 'report', 'Monthly log of all issued purchase orders with vendor and amount details', '/uploads/documents/logs1/po_log_jan2026.pdf', 'po_log_jan2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(67, 'DOC-2026-00067', 'Purchase Order Log - February 2026', 'Logs1 Procurement', 12, 'report', 'Monthly log of all issued purchase orders for February 2026', '/uploads/documents/logs1/po_log_feb2026.pdf', 'po_log_feb2026.pdf', 712345, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(68, 'DOC-2026-00068', 'Vendor Accreditation Masterlist 2026', 'Logs1 Procurement', 12, 'report', 'List of all accredited vendors with contact details and product categories', '/uploads/documents/logs1/vendor_masterlist_2026.xlsx', 'vendor_masterlist_2026.xlsx', 456789, 'application/vnd.openxmlformats-officedocument.spre', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(69, 'DOC-2026-00069', 'Vendor Performance Evaluation - Q4 2025', 'Logs1 Procurement', 12, 'report', 'Quarterly vendor performance assessment with ratings and recommendations', '/uploads/documents/logs1/vendor_eval_q4_2025.pdf', 'vendor_eval_q4_2025.pdf', 890123, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'confidential', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(70, 'DOC-2026-00070', 'Vendor Quote Comparison Sheet - Office Supplies', 'Logs1 Procurement', 12, 'report', 'Comparative analysis of vendor quotes for office supplies procurement', '/uploads/documents/logs1/quote_compare_office.pdf', 'quote_compare_office.pdf', 345678, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(71, 'DOC-2026-00071', 'Vendor Quote Comparison Sheet - IT Equipment', 'Logs1 Procurement', 12, 'report', 'Comparative analysis of vendor quotes for IT equipment procurement', '/uploads/documents/logs1/quote_compare_it.pdf', 'quote_compare_it.pdf', 378901, 'application/pdf', 1, NULL, NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(72, 'DOC-2026-00072', 'Procurement Budget Allocation Report 2026', 'Logs1 Procurement', 12, 'report', 'Annual procurement budget allocation by department and category', '/uploads/documents/logs1/proc_budget_2026.pdf', 'proc_budget_2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 4, 'Log 1', 'confidential', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(73, 'DOC-2026-00073', 'Consolidated Procurement Report - January 2026', 'Logs1 Procurement', 12, 'report', 'Consolidated monthly procurement report with expenditure summaries', '/uploads/documents/logs1/consolidated_proc_jan2026.pdf', 'consolidated_proc_jan2026.pdf', 1123456, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(74, 'DOC-2026-00074', 'Warehouse Management Policy & Procedures', 'Logs1 Warehouse', 13, 'policy', 'Standard operating procedures for warehouse operations and inventory control', '/uploads/documents/logs1/warehouse_sop.pdf', 'warehouse_sop.pdf', 1456789, 'application/pdf', 1, NULL, NULL, 5, 'Log 2', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(75, 'DOC-2026-00075', 'Inventory Summary Report - January 2026', 'Logs1 Warehouse', 13, 'report', 'Monthly inventory levels, movements, and variance report', '/uploads/documents/logs1/inventory_jan2026.pdf', 'inventory_jan2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 5, 'Log 2', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(76, 'DOC-2026-00076', 'Inventory Snapshot - Physical Count February 2026', 'Logs1 Warehouse', 13, 'report', 'Physical inventory count results with discrepancy analysis', '/uploads/documents/logs1/physical_count_feb2026.pdf', 'physical_count_feb2026.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 5, 'Log 2', 'confidential', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(77, 'DOC-2026-00077', 'Warehouse Room Request Form Template', 'Logs1 Warehouse', 13, 'form', 'Form for requesting warehouse storage room allocation', '/uploads/documents/logs1/room_request_form.pdf', 'room_request_form.pdf', 156789, 'application/pdf', 1, NULL, NULL, 5, 'Log 2', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(78, 'DOC-2026-00078', 'Incoming Asset Receiving Report - January 2026', 'Logs1 Warehouse', 13, 'report', 'Report of all incoming assets received by the warehouse in January 2026', '/uploads/documents/logs1/incoming_assets_jan2026.pdf', 'incoming_assets_jan2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 5, 'Log 2', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(79, 'DOC-2026-00079', 'Item Category Classification Guide', 'Logs1 Warehouse', 13, 'policy', 'Guide for classifying items into warehouse categories and subcategories', '/uploads/documents/logs1/item_category_guide.pdf', 'item_category_guide.pdf', 234567, 'application/pdf', 1, NULL, NULL, 5, 'Log 2', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(80, 'DOC-2026-00080', 'Transaction Log Summary - January 2026', 'Logs1 Warehouse', 13, 'report', 'Summary of all warehouse transactions (in, out, transfer) for the month', '/uploads/documents/logs1/txn_log_jan2026.pdf', 'txn_log_jan2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 5, 'Log 2', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(81, 'DOC-2026-00081', 'Project Logistics Master Plan 2026', 'Logs1 Projects', 14, 'report', 'Annual project logistics plan including all scheduled projects and milestones', '/uploads/documents/logs1/project_masterplan_2026.pdf', 'project_masterplan_2026.pdf', 2345678, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(82, 'DOC-2026-00082', 'Project Status Report - Branch Expansion Cebu', 'Logs1 Projects', 14, 'report', 'Progress report on Cebu branch expansion project logistics', '/uploads/documents/logs1/proj_cebu_expansion.pdf', 'proj_cebu_expansion.pdf', 890123, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(83, 'DOC-2026-00083', 'Project Status Report - IT Infrastructure Upgrade', 'Logs1 Projects', 14, 'report', 'Progress report on IT infrastructure upgrade project logistics', '/uploads/documents/logs1/proj_it_upgrade.pdf', 'proj_it_upgrade.pdf', 912345, 'application/pdf', 1, NULL, NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(84, 'DOC-2026-00084', 'Dispatch Order Log - January 2026', 'Logs1 Projects', 14, 'report', 'Monthly log of all logistics dispatches with tracking information', '/uploads/documents/logs1/dispatch_log_jan2026.pdf', 'dispatch_log_jan2026.pdf', 456789, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(85, 'DOC-2026-00085', 'Resource Allocation Report - Q1 2026', 'Logs1 Projects', 14, 'report', 'Quarterly resource allocation across all active logistics projects', '/uploads/documents/logs1/resource_alloc_q1_2026.pdf', 'resource_alloc_q1_2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'confidential', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(86, 'DOC-2026-00086', 'Milestone Tracking Sheet - All Active Projects', 'Logs1 Projects', 14, 'report', 'Consolidated milestone tracker for all active projects with status indicators', '/uploads/documents/logs1/milestone_tracker.xlsx', 'milestone_tracker.xlsx', 1234567, 'application/vnd.openxmlformats-officedocument.spre', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(87, 'DOC-2026-00087', 'Asset Register - Complete Inventory 2026', 'Logs1 Assets', 15, 'report', 'Complete organizational asset register with acquisition details and current status', '/uploads/documents/logs1/asset_register_2026.xlsx', 'asset_register_2026.xlsx', 2345678, 'application/vnd.openxmlformats-officedocument.spre', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(88, 'DOC-2026-00088', 'Preventive Maintenance Schedule 2026', 'Logs1 Assets', 15, 'report', 'Annual preventive maintenance schedule for all organizational assets', '/uploads/documents/logs1/pm_schedule_2026.pdf', 'pm_schedule_2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(89, 'DOC-2026-00089', 'Maintenance Request Form Template', 'Logs1 Assets', 15, 'form', 'Standard form for submitting asset maintenance and repair requests', '/uploads/documents/logs1/maintenance_request_form.pdf', 'maintenance_request_form.pdf', 189456, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(90, 'DOC-2026-00090', 'Maintenance Completion Report - January 2026', 'Logs1 Assets', 15, 'report', 'Monthly report of all completed maintenance activities with cost summary', '/uploads/documents/logs1/maintenance_report_jan2026.pdf', 'maintenance_report_jan2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(91, 'DOC-2026-00091', 'Asset Disposal Policy & Procedures', 'Logs1 Assets', 15, 'policy', 'Policy governing the disposal of obsolete or damaged organizational assets', '/uploads/documents/logs1/asset_disposal_policy.pdf', 'asset_disposal_policy.pdf', 345678, 'application/pdf', 1, NULL, NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(92, 'DOC-2026-00092', 'Repair Personnel Registry & Certifications', 'Logs1 Assets', 15, 'report', 'Registry of authorized repair personnel with valid certifications', '/uploads/documents/logs1/repair_personnel.pdf', 'repair_personnel.pdf', 234567, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(93, 'DOC-2026-00093', 'Asset Depreciation Schedule 2026', 'Logs1 Assets', 15, 'report', 'Annual depreciation schedule for all capitalized assets', '/uploads/documents/logs1/asset_depreciation_2026.pdf', 'asset_depreciation_2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 4, 'Log 1', 'confidential', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(94, 'DOC-2026-00094', 'Document Tracking Procedures Manual', 'Logs1 Document Tracking', 4, 'policy', 'Standard procedures for tracking documents across logistics operations', '/uploads/documents/logs1/dtlr_procedures.pdf', 'dtlr_procedures.pdf', 456789, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(95, 'DOC-2026-00095', 'Logistics Audit Trail Report - January 2026', 'Logs1 Document Tracking', 4, 'report', 'Monthly audit trail of all logistics record changes and document movements', '/uploads/documents/logs1/logistics_audit_jan2026.pdf', 'logistics_audit_jan2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'confidential', 'active', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(96, 'DOC-2026-00096', 'Annual Budget Proposal FY 2026-2027', 'Finance Budget', 16, 'report', 'Consolidated annual budget proposal for all departments', '/uploads/documents/finance/budget_proposal_fy2026.pdf', 'budget_proposal_fy2026.pdf', 3456789, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(97, 'DOC-2026-00097', 'Budget Proposal - Human Resources Department', 'Finance Budget', 16, 'report', 'Departmental budget proposal from HR for FY 2026-2027', '/uploads/documents/finance/budget_hr_fy2026.pdf', 'budget_hr_fy2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 2, 'Financial', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(98, 'DOC-2026-00098', 'Budget Proposal - Operations Department', 'Finance Budget', 16, 'report', 'Departmental budget proposal from Operations for FY 2026-2027', '/uploads/documents/finance/budget_ops_fy2026.pdf', 'budget_ops_fy2026.pdf', 923456, 'application/pdf', 1, NULL, NULL, 5, 'Financial', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(99, 'DOC-2026-00099', 'Budget Proposal - IT Department', 'Finance Budget', 16, 'report', 'Departmental budget proposal from IT for FY 2026-2027', '/uploads/documents/finance/budget_it_fy2026.pdf', 'budget_it_fy2026.pdf', 856789, 'application/pdf', 1, NULL, NULL, 1, 'Financial', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(100, 'DOC-2026-00100', 'Budget Proposal - Legal Department', 'Finance Budget', 16, 'report', 'Departmental budget proposal from Legal for FY 2026-2027', '/uploads/documents/finance/budget_legal_fy2026.pdf', 'budget_legal_fy2026.pdf', 789012, 'application/pdf', 1, NULL, NULL, 3, 'Financial', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(101, 'DOC-2026-00101', 'Budget vs Actual Report - January 2026', 'Finance Budget', 16, 'report', 'Variance analysis comparing actual expenditures against budgeted amounts', '/uploads/documents/finance/bva_jan2026.pdf', 'bva_jan2026.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(102, 'DOC-2026-00102', 'Budget Forecast - Q2 2026', 'Finance Budget', 16, 'report', 'Rolling budget forecast for the second quarter of 2026', '/uploads/documents/finance/forecast_q2_2026.pdf', 'forecast_q2_2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(103, 'DOC-2026-00103', 'Disbursement Request Form Template', 'Finance Disbursement', 17, 'form', 'Standard form for submitting disbursement requests with approval workflow', '/uploads/documents/finance/disbursement_req_form.pdf', 'disbursement_req_form.pdf', 189456, 'application/pdf', 1, NULL, NULL, 4, 'Core 1', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(104, 'DOC-2026-00104', 'Approved Disbursements Report - January 2026', 'Finance Disbursement', 17, 'report', 'Monthly report of all approved disbursement requests with check/fund transfer details', '/uploads/documents/finance/approved_disb_jan2026.pdf', 'approved_disb_jan2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 4, 'Core 1', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(105, 'DOC-2026-00105', 'Pending Disbursements Status Report - February 2026', 'Finance Disbursement', 17, 'report', 'Current status of all pending disbursement requests awaiting approval', '/uploads/documents/finance/pending_disb_feb2026.pdf', 'pending_disb_feb2026.pdf', 456789, 'application/pdf', 1, NULL, NULL, 4, 'Core 1', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(106, 'DOC-2026-00106', 'Rejected Disbursements Log - Q4 2025', 'Finance Disbursement', 17, 'report', 'Log of rejected disbursement requests with reasons for denial', '/uploads/documents/finance/rejected_disb_q4_2025.pdf', 'rejected_disb_q4_2025.pdf', 345678, 'application/pdf', 1, NULL, NULL, 4, 'Core 1', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(107, 'DOC-2026-00107', 'Disbursement Approval Workflow Policy', 'Finance Disbursement', 17, 'policy', 'Policy defining disbursement approval hierarchy and authorization limits', '/uploads/documents/finance/disb_approval_policy.pdf', 'disb_approval_policy.pdf', 234567, 'application/pdf', 1, NULL, NULL, 1, 'Core 1', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(108, 'DOC-2026-00108', 'Chart of Accounts - MicroFinancial 2026', 'Finance Accounting', 18, 'report', 'Complete chart of accounts with account codes, types, and descriptions', '/uploads/documents/finance/coa_2026.pdf', 'coa_2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(109, 'DOC-2026-00109', 'Journal Entry Summary - January 2026', 'Finance Accounting', 18, 'report', 'Monthly summary of all journal entries posted to the general ledger', '/uploads/documents/finance/journal_jan2026.pdf', 'journal_jan2026.pdf', 1567890, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(110, 'DOC-2026-00110', 'General Ledger Report - January 2026', 'Finance Accounting', 18, 'report', 'Complete general ledger with all account balances for the month', '/uploads/documents/finance/gl_jan2026.pdf', 'gl_jan2026.pdf', 2345678, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(111, 'DOC-2026-00111', 'Balance Sheet - As of January 31 2026', 'Finance Accounting', 18, 'report', 'Statement of financial position as of month-end', '/uploads/documents/finance/balance_sheet_jan2026.pdf', 'balance_sheet_jan2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(112, 'DOC-2026-00112', 'Income Statement - January 2026', 'Finance Accounting', 18, 'report', 'Monthly statement of comprehensive income for microfinancial operations', '/uploads/documents/finance/income_stmt_jan2026.pdf', 'income_stmt_jan2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(113, 'DOC-2026-00113', 'Cash Flow Statement - January 2026', 'Finance Accounting', 18, 'report', 'Monthly cash flow statement showing operating, investing, and financing activities', '/uploads/documents/finance/cashflow_jan2026.pdf', 'cashflow_jan2026.pdf', 456789, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(114, 'DOC-2026-00114', 'Accounts Payable Aging Report - January 2026', 'Finance AP-AR', 19, 'report', 'Aging schedule of all outstanding payables by vendor and due date', '/uploads/documents/finance/ap_aging_jan2026.pdf', 'ap_aging_jan2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 4, 'Core 2', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(115, 'DOC-2026-00115', 'Accounts Receivable Aging Report - January 2026', 'Finance AP-AR', 19, 'report', 'Aging schedule of all outstanding receivables by client and due date', '/uploads/documents/finance/ar_aging_jan2026.pdf', 'ar_aging_jan2026.pdf', 912345, 'application/pdf', 1, NULL, NULL, 4, 'Core 2', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(116, 'DOC-2026-00116', 'Vendor & Customer Masterlist 2026', 'Finance AP-AR', 19, 'report', 'Complete listing of all vendors and customers with contact and bank details', '/uploads/documents/finance/vendor_customer_list.xlsx', 'vendor_customer_list.xlsx', 1234567, 'application/vnd.openxmlformats-officedocument.spre', 1, NULL, NULL, 4, 'Core 2', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(117, 'DOC-2026-00117', 'Outstanding Balances Report - January 2026', 'Finance AP-AR', 19, 'report', 'Summary of all outstanding balances for accounts payable and receivable', '/uploads/documents/finance/outstanding_bal_jan2026.pdf', 'outstanding_bal_jan2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 4, 'Core 2', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(118, 'DOC-2026-00118', 'Invoice Register - January 2026', 'Finance AP-AR', 19, 'report', 'Monthly register of all invoices issued and received', '/uploads/documents/finance/invoice_register_jan2026.pdf', 'invoice_register_jan2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 4, 'Core 2', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(119, 'DOC-2026-00119', 'Payment Voucher Template', 'Finance AP-AR', 19, 'form', 'Standard payment voucher form for processing vendor payments', '/uploads/documents/finance/payment_voucher_form.pdf', 'payment_voucher_form.pdf', 156789, 'application/pdf', 1, NULL, NULL, 4, 'Core 2', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(120, 'DOC-2026-00120', 'Collection Dashboard Summary - January 2026', 'Finance Collections', 19, 'report', 'Monthly collection performance dashboard with targets vs actuals', '/uploads/documents/finance/collection_dash_jan2026.pdf', 'collection_dash_jan2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 4, 'Core 1', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(121, 'DOC-2026-00121', 'Collection Report - Overdue Accounts January 2026', 'Finance Collections', 19, 'report', 'Detailed report on overdue accounts with collection action status', '/uploads/documents/finance/overdue_accounts_jan2026.pdf', 'overdue_accounts_jan2026.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 4, 'Core 1', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(122, 'DOC-2026-00122', 'Collection Receipt Template', 'Finance Collections', 19, 'receipt', 'Official receipt template for client payments and collections', '/uploads/documents/finance/collection_receipt_form.pdf', 'collection_receipt_form.pdf', 123456, 'application/pdf', 1, NULL, NULL, 4, 'Core 1', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(123, 'DOC-2026-00123', 'Collection Aging Schedule - Q4 2025', 'Finance Collections', 19, 'report', 'Quarterly aging schedule of all loan collections by maturity period', '/uploads/documents/finance/collection_aging_q4_2025.pdf', 'collection_aging_q4_2025.pdf', 567890, 'application/pdf', 1, NULL, NULL, 4, 'Core 1', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(124, 'DOC-2026-00124', 'Annual Financial Report FY 2025', 'Finance Reports', 1, 'report', 'Complete annual financial report for the fiscal year 2025', '/uploads/documents/finance/annual_report_fy2025.pdf', 'annual_report_fy2025.pdf', 5678901, 'application/pdf', 1, NULL, NULL, 4, 'Core 2', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(125, 'DOC-2026-00125', 'External Audit Report - FY 2025', 'Finance Reports', 1, 'report', 'Independent external audit report for the fiscal year 2025', '/uploads/documents/finance/external_audit_fy2025.pdf', 'external_audit_fy2025.pdf', 3456789, 'application/pdf', 1, NULL, NULL, 1, 'Core 2', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(126, 'DOC-2026-00126', 'BSP Compliance Report - Q4 2025', 'Finance Reports', 3, 'report', 'Bangko Sentral ng Pilipinas regulatory compliance report', '/uploads/documents/finance/bsp_compliance_q4_2025.pdf', 'bsp_compliance_q4_2025.pdf', 1234567, 'application/pdf', 1, NULL, NULL, 3, 'Core 2', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24');
INSERT INTO `documents` (`document_id`, `document_code`, `title`, `folder_name`, `category_id`, `document_type`, `description`, `file_path`, `file_name`, `file_size`, `file_type`, `version`, `tags`, `qr_code_id`, `uploaded_by`, `department`, `confidentiality`, `status`, `source_system`, `archived_at`, `retained_at`, `created_at`, `updated_at`) VALUES
(127, 'DOC-2026-00127', 'Payroll Import Reconciliation Report - January 2026', 'Finance Reports', 9, 'report', 'Reconciliation of HR4 payroll data imported into the finance system', '/uploads/documents/finance/payroll_recon_jan2026.pdf', 'payroll_recon_jan2026.pdf', 678901, 'application/pdf', 1, NULL, NULL, 4, 'Core 2', 'restricted', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(128, 'DOC-2026-00128', 'Logistics Expense Import Summary - January 2026', 'Finance Reports', 1, 'report', 'Summary of logistics expenses imported from Logs1 procurement module', '/uploads/documents/finance/logistics_import_jan2026.pdf', 'logistics_import_jan2026.pdf', 456789, 'application/pdf', 1, NULL, NULL, 4, 'Core 2', 'confidential', 'active', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(129, 'DOC-2026-00129', 'System Integration Architecture Document', 'Admin IT', 5, 'report', 'Technical architecture document for Admin-HR2-HR4-Logs1-Finance integration', '/uploads/documents/admin/system_architecture.pdf', 'system_architecture.pdf', 2345678, 'application/pdf', 1, NULL, NULL, 1, 'Administrative', 'restricted', 'active', 'Admin', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(130, 'DOC-2026-00130', 'Data Privacy Policy - NDPC Compliance 2026', 'Admin Compliance', 3, 'policy', 'Organizational data privacy policy compliant with National Data Privacy Commission', '/uploads/documents/admin/data_privacy_2026.pdf', 'data_privacy_2026.pdf', 890123, 'application/pdf', 1, NULL, NULL, 3, 'Administrative', 'internal', 'active', 'Admin', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(131, 'DOC-2026-00131', 'Administrative Circular No. 2026-001 - Office Hours', 'Admin Circulars', 5, 'memo', 'Circular updating official office hours effective March 2026', '/uploads/documents/admin/circular_2026_001.pdf', 'circular_2026_001.pdf', 123456, 'application/pdf', 1, NULL, NULL, 2, 'Administrative', 'public', 'active', 'Admin', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(132, 'DOC-2026-00132', 'Administrative Circular No. 2026-002 - Dress Code', 'Admin Circulars', 5, 'memo', 'Updated dress code policy for all employees', '/uploads/documents/admin/circular_2026_002.pdf', 'circular_2026_002.pdf', 134567, 'application/pdf', 1, NULL, NULL, 2, 'Administrative', 'public', 'active', 'Admin', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(133, 'DOC-2026-00133', 'Board Resolution No. 2026-002 - Annual Budget Approval', 'Board Resolutions', 8, 'certificate', 'Board resolution approving the FY 2026-2027 annual budget', '/uploads/documents/admin/br_2026_002.pdf', 'br_2026_002.pdf', 567890, 'application/pdf', 1, NULL, NULL, 1, 'Administrative', 'restricted', 'active', NULL, NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(134, 'DOC-2026-00134', 'Board Resolution No. 2026-003 - Dividend Declaration', 'Board Resolutions', 8, 'certificate', 'Board resolution declaring dividends for FY 2025', '/uploads/documents/admin/br_2026_003.pdf', 'br_2026_003.pdf', 490123, 'application/pdf', 1, NULL, NULL, 1, 'Administrative', 'restricted', 'active', NULL, NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(135, 'DOC-2026-00135', 'Board Meeting Minutes - January 15 2026', 'Board Resolutions', 8, 'report', 'Official minutes of the board of directors meeting held January 15, 2026', '/uploads/documents/admin/board_minutes_jan2026.pdf', 'board_minutes_jan2026.pdf', 789012, 'application/pdf', 1, NULL, NULL, 1, 'Administrative', 'restricted', 'active', NULL, NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(136, 'DOC-2026-00136', 'Document Management System User Guide', 'Admin IT', 5, 'policy', 'User manual for the centralized document management and archiving system', '/uploads/documents/admin/dms_user_guide.pdf', 'dms_user_guide.pdf', 1890123, 'application/pdf', 1, NULL, NULL, 1, 'Administrative', 'public', 'active', 'Admin', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(137, 'DOC-2026-00137', 'Cross-System Data Sync Report - January 2026', 'Admin IT', 5, 'report', 'Monthly report on data synchronization across HR2, HR4, Logs1, and Finance systems', '/uploads/documents/admin/data_sync_jan2026.pdf', 'data_sync_jan2026.pdf', 567890, 'application/pdf', 1, NULL, NULL, 1, 'Administrative', 'restricted', 'active', 'Admin', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(138, 'DOC-2026-00138', 'IT Infrastructure Inventory Report 2026', 'Admin IT', 5, 'report', 'Complete inventory of all servers, networking equipment, and software licenses', '/uploads/documents/admin/it_inventory_2026.xlsx', 'it_inventory_2026.xlsx', 1234567, 'application/vnd.openxmlformats-officedocument.spre', 1, NULL, NULL, 1, 'Administrative', 'confidential', 'active', 'Admin', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(139, 'DOC-2026-00139', 'Disaster Recovery Plan 2026', 'Admin IT', 3, 'policy', 'Updated disaster recovery and business continuity plan for all systems', '/uploads/documents/admin/disaster_recovery_2026.pdf', 'disaster_recovery_2026.pdf', 2345678, 'application/pdf', 1, NULL, NULL, 1, 'Administrative', 'restricted', 'active', 'Admin', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(140, 'DOC-2026-00140', 'Company Code of Conduct 2026', 'Admin Compliance', 3, 'policy', 'Updated company code of conduct and ethical standards for all employees', '/uploads/documents/admin/code_of_conduct_2026.pdf', 'code_of_conduct_2026.pdf', 1567890, 'application/pdf', 1, NULL, NULL, 3, 'Administrative', 'public', 'active', 'Admin', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(141, 'DOC-2025-00001', 'Q3 2025 Financial Statement', 'Finance Reports', 1, 'report', 'Quarterly financial report for Q3 2025', '/uploads/documents/archive/fin_q3_2025.pdf', 'fin_q3_2025.pdf', 2234567, 'application/pdf', 1, NULL, NULL, 4, 'Core 2', 'confidential', 'archived', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(142, 'DOC-2025-00002', 'Payroll Summary Report - December 2025', 'HR4 Payroll', 9, 'report', 'Monthly payroll summary for December 2025', '/uploads/documents/archive/payroll_dec2025.pdf', 'payroll_dec2025.pdf', 1456789, 'application/pdf', 1, NULL, NULL, 1, 'HR 4', 'restricted', 'archived', 'HR4', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(143, 'DOC-2025-00003', 'Training Completion Report - Q4 2025', 'HR2 Training', 10, 'report', 'Quarterly training completion report for Q4 2025', '/uploads/documents/archive/training_q4_2025.pdf', 'training_q4_2025.pdf', 890123, 'application/pdf', 1, NULL, NULL, 2, 'HR 2', 'internal', 'archived', 'HR2', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(144, 'DOC-2025-00004', 'Inventory Summary Report - December 2025', 'Logs1 Warehouse', 13, 'report', 'Monthly inventory summary for December 2025', '/uploads/documents/archive/inventory_dec2025.pdf', 'inventory_dec2025.pdf', 678901, 'application/pdf', 1, NULL, NULL, 5, 'Log 2', 'internal', 'archived', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(145, 'DOC-2025-00005', 'Budget vs Actual Report - Q4 2025', 'Finance Budget', 16, 'report', 'Quarterly variance analysis for Q4 2025', '/uploads/documents/archive/bva_q4_2025.pdf', 'bva_q4_2025.pdf', 1123456, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'confidential', 'archived', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(146, 'DOC-2025-00006', 'Employee Handbook v3.1', 'HR Policies', 2, 'policy', 'Previous version of the employee handbook (superseded by v3.2)', '/uploads/documents/archive/emp_handbook_v31.pdf', 'emp_handbook_v31.pdf', 4890123, 'application/pdf', 1, NULL, NULL, 2, 'HR 1', 'internal', 'archived', NULL, NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(147, 'DOC-2025-00007', 'Vendor Accreditation List 2025', 'Logs1 Procurement', 12, 'report', 'Previous year vendor accreditation masterlist', '/uploads/documents/archive/vendor_list_2025.pdf', 'vendor_list_2025.pdf', 345678, 'application/pdf', 1, NULL, NULL, 5, 'Log 1', 'internal', 'archived', 'Logs1', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(148, 'DOC-2025-00008', 'Balance Sheet - FY 2025 Year-End', 'Finance Accounting', 18, 'report', 'Year-end balance sheet for fiscal year 2025', '/uploads/documents/archive/balance_sheet_fy2025.pdf', 'balance_sheet_fy2025.pdf', 789012, 'application/pdf', 1, NULL, NULL, 4, 'Financial', 'restricted', 'archived', 'Finance', NULL, NULL, '2026-02-26 11:07:24', '2026-02-26 13:56:24'),
(304, 'INT-2026-00001', 'HR2 Employee Directory', 'hr2', 2, 'report', 'Complete employee directory retrieved from HR2 Talent Management system.', '/uploads/documents/hr2/live_hr2_employees.pdf', 'live_hr2_employees.pdf', 4474, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:50', '2026-02-26 15:43:37'),
(305, 'INT-2026-00002', 'HR2 Leave Requests Report', 'hr2', 2, 'report', 'All leave requests from the HR2 Leave Management module.', '/uploads/documents/hr2/live_hr2_leaves.pdf', 'live_hr2_leaves.pdf', 2664, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:51', '2026-02-26 15:43:37'),
(306, 'INT-2026-00003', 'HR2 Leave Types Reference', 'hr2', 2, 'report', 'Master list of all leave types configured in HR2.', '/uploads/documents/hr2/live_hr2_leave_types.pdf', 'live_hr2_leave_types.pdf', 3378, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:53', '2026-02-26 15:43:37'),
(307, 'INT-2026-00004', 'HR2 Leave Statistics Summary', 'hr2', 2, 'report', 'Leave usage summary statistics from HR2.', '/uploads/documents/hr2/live_hr2_leave_stats.pdf', 'live_hr2_leave_stats.pdf', 2173, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:53', '2026-02-26 15:43:37'),
(308, 'INT-2026-00005', 'HR2 Training Room Bookings', 'hr2', 10, 'report', 'All training room booking records from HR2 Training module.', '/uploads/documents/hr2/live_hr2_training_bookings.pdf', 'live_hr2_training_bookings.pdf', 3143, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:54', '2026-02-26 15:43:37'),
(309, 'INT-2026-00006', 'HR2 Training Statistics', 'hr2', 10, 'report', 'Training room booking statistics overview from HR2.', '/uploads/documents/hr2/live_hr2_training_stats.pdf', 'live_hr2_training_stats.pdf', 2175, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:55', '2026-02-26 15:43:37'),
(310, 'INT-2026-00007', 'HR2 Competency Assignments', 'hr2', 11, 'report', 'Employee competency assignments from HR2 Competency Management.', '/uploads/documents/hr2/live_hr2_competencies.pdf', 'live_hr2_competencies.pdf', 3147, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:56', '2026-02-26 15:43:37'),
(311, 'INT-2026-00008', 'HR2 Succession Planning Report', 'hr2', 11, 'report', 'Succession planning and talent pool data from HR2.', '/uploads/documents/hr2/live_hr2_successors.pdf', 'live_hr2_successors.pdf', 3261, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:57', '2026-02-26 15:43:37'),
(312, 'INT-2026-00009', 'HR2 Job Positions Report', 'hr2', 2, 'report', 'Job titles and positions directory from HR2.', '/uploads/documents/hr2/live_hr2_jobs.pdf', 'live_hr2_jobs.pdf', 4448, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:58', '2026-02-26 15:43:37'),
(313, 'INT-2026-00010', 'HR2 Dashboard Summary', 'hr2', 2, 'report', 'Real-time HR dashboard statistics from HR2.', '/uploads/documents/hr2/live_hr2_dashboard.pdf', 'live_hr2_dashboard.pdf', 2078, 'application/pdf', 1, '[\"live-data\",\"hr2\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 2', 'internal', 'active', 'HR2', NULL, NULL, '2026-02-26 12:27:58', '2026-02-26 15:43:37'),
(314, 'INT-2026-00011', 'HR4 Employee Master Data', 'hr4', 2, 'report', 'Complete employee master data from HR4 HCM module.', '/uploads/documents/hr4/live_hr4_employees.pdf', 'live_hr4_employees.pdf', 2088, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:27:59', '2026-02-26 15:43:37'),
(315, 'INT-2026-00012', 'HR4 Employment Contracts', 'hr4', 2, 'report', 'Employment contract records derived from HR4 employee data.', '/uploads/documents/hr4/live_hr4_contracts.pdf', 'live_hr4_contracts.pdf', 2097, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:00', '2026-02-26 15:43:37'),
(316, 'INT-2026-00013', 'HR4 Payroll Records', 'hr4', 9, 'report', 'All payslip records from HR4 Payroll module.', '/uploads/documents/hr4/live_hr4_payslips.pdf', 'live_hr4_payslips.pdf', 2077, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:01', '2026-02-26 15:43:37'),
(317, 'INT-2026-00014', 'HR4 Payroll Summary Report', 'hr4', 9, 'report', 'Payroll summary with totals from HR4.', '/uploads/documents/hr4/live_hr4_payslip_summary.pdf', 'live_hr4_payslip_summary.pdf', 2136, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:02', '2026-02-26 15:43:37'),
(318, 'INT-2026-00015', 'HR4 Job Positions', 'hr4', 2, 'report', 'All job positions with titles from HR4.', '/uploads/documents/hr4/live_hr4_positions.pdf', 'live_hr4_positions.pdf', 4113, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:03', '2026-02-26 15:43:37'),
(319, 'INT-2026-00016', 'HR4 Vacant Positions', 'hr4', 2, 'report', 'Currently open/vacant positions from HR4.', '/uploads/documents/hr4/live_hr4_vacant_positions.pdf', 'live_hr4_vacant_positions.pdf', 2863, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:04', '2026-02-26 15:43:37'),
(320, 'INT-2026-00017', 'HR4 Compensation & Benefits', 'hr4', 9, 'report', 'Employee compensation details including allowances and deductions.', '/uploads/documents/hr4/live_hr4_compensation.pdf', 'live_hr4_compensation.pdf', 2107, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:05', '2026-02-26 15:43:37'),
(321, 'INT-2026-00018', 'HR4 Government ID Records', 'hr4', 2, 'report', 'Government ID numbers (SSS, TIN, PhilHealth, Pag-IBIG) from HR4.', '/uploads/documents/hr4/live_hr4_government_ids.pdf', 'live_hr4_government_ids.pdf', 2105, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:05', '2026-02-26 15:43:37'),
(322, 'INT-2026-00019', 'HR4 Department Summary', 'hr4', 5, 'report', 'Department breakdown with employee counts from HR4.', '/uploads/documents/hr4/live_hr4_departments.pdf', 'live_hr4_departments.pdf', 2087, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:06', '2026-02-26 15:43:37'),
(323, 'INT-2026-00020', 'HR4 Termination Records', 'hr4', 2, 'report', 'Terminated/separated employee records from HR4.', '/uploads/documents/hr4/live_hr4_terminations.pdf', 'live_hr4_terminations.pdf', 2084, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:06', '2026-02-26 15:43:37'),
(324, 'INT-2026-00021', 'HR4 Latest Payroll Disbursement', 'hr4', 9, 'report', 'Latest payroll disbursement batch from HR4.', '/uploads/documents/hr4/live_hr4_disbursement.pdf', 'live_hr4_disbursement.pdf', 2088, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:07', '2026-02-26 15:43:37'),
(325, 'INT-2026-00022', 'HR4 Dashboard Summary', 'hr4', 2, 'report', 'Aggregate HR4 dashboard statistics.', '/uploads/documents/hr4/live_hr4_dashboard.pdf', 'live_hr4_dashboard.pdf', 2120, 'application/pdf', 1, '[\"live-data\",\"hr4\",\"transfer\",\"2026-02\"]', NULL, 1, 'HR 4', 'internal', 'active', 'HR4', NULL, NULL, '2026-02-26 12:28:09', '2026-02-26 15:43:37'),
(326, 'INT-2026-00023', 'Logs1 Purchase Requisitions', 'logs1', 12, 'report', 'Purchase requisitions from Logs1 Procurement module (PSM).', '/uploads/documents/logs1/live_logs1_purchases.pdf', 'live_logs1_purchases.pdf', 2102, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:10', '2026-02-26 15:43:37'),
(327, 'INT-2026-00024', 'Logs1 Procurement Budget Status', 'logs1', 12, 'report', 'Procurement budget allocation and spending from Logs1.', '/uploads/documents/logs1/live_logs1_psm_budget.pdf', 'live_logs1_psm_budget.pdf', 2100, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:11', '2026-02-26 15:43:37'),
(328, 'INT-2026-00025', 'Logs1 Budget Activity Logs', 'logs1', 12, 'report', 'Budget activity and transaction logs from Logs1.', '/uploads/documents/logs1/live_logs1_budget_logs.pdf', 'live_logs1_budget_logs.pdf', 2089, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:11', '2026-02-26 15:43:37'),
(329, 'INT-2026-00026', 'Logs1 Department Budget Requests', 'logs1', 12, 'report', 'Department budget requests from Logs1 PSM.', '/uploads/documents/logs1/live_logs1_budget_requests.pdf', 'live_logs1_budget_requests.pdf', 2089, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:12', '2026-02-26 15:43:37'),
(330, 'INT-2026-00027', 'Logs1 Vendor Directory', 'logs1', 12, 'report', 'Accredited vendor directory from Logs1 PSM.', '/uploads/documents/logs1/live_logs1_vendors.pdf', 'live_logs1_vendors.pdf', 2080, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:13', '2026-02-26 15:43:37'),
(331, 'INT-2026-00028', 'Logs1 Product Catalog', 'logs1', 12, 'report', 'Product catalog from Logs1 Procurement.', '/uploads/documents/logs1/live_logs1_products.pdf', 'live_logs1_products.pdf', 2075, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:14', '2026-02-26 15:43:37'),
(332, 'INT-2026-00029', 'Logs1 Inventory & Room Requests', 'logs1', 13, 'report', 'Inventory and room requests from Logs1 Smart Warehousing.', '/uploads/documents/logs1/live_logs1_inventory.pdf', 'live_logs1_inventory.pdf', 2103, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:14', '2026-02-26 15:43:37'),
(333, 'INT-2026-00030', 'Logs1 Project Tracking', 'logs1', 14, 'report', 'Project tracking from Logs1 Project Logistics Tracker.', '/uploads/documents/logs1/live_logs1_projects.pdf', 'live_logs1_projects.pdf', 2091, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:47', '2026-02-26 15:43:37'),
(334, 'INT-2026-00031', 'Logs1 Document Tracker Records', 'logs1', 5, 'report', 'Tracked documents from Logs1 DTLR module.', '/uploads/documents/logs1/live_logs1_documents.pdf', 'live_logs1_documents.pdf', 2086, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:48', '2026-02-26 15:43:37'),
(335, 'INT-2026-00032', 'Logs1 Asset Maintenance Records', 'logs1', 15, 'report', 'Asset maintenance records from Logs1 ALMS module.', '/uploads/documents/logs1/live_logs1_assets.pdf', 'live_logs1_assets.pdf', 2095, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:49', '2026-02-26 15:43:37'),
(336, 'INT-2026-00033', 'Logs1 Dashboard Summary', 'logs1', 12, 'report', 'Aggregate Logs1 dashboard statistics across all modules.', '/uploads/documents/logs1/live_logs1_dashboard.pdf', 'live_logs1_dashboard.pdf', 2201, 'application/pdf', 1, '[\"live-data\",\"logs1\",\"transfer\",\"2026-02\"]', NULL, 1, 'Log 1', 'internal', 'active', 'Logs1', NULL, NULL, '2026-02-26 12:28:52', '2026-02-26 15:43:37'),
(337, 'INT-2026-00034', 'Finance Budget Allocations', 'finance', 16, 'report', 'All budget proposal records from Finance system.', '/uploads/documents/finance/live_finance_budgets.pdf', 'live_finance_budgets.pdf', 2780, 'application/pdf', 1, '[\"live-data\",\"finance\",\"transfer\",\"2026-02\"]', NULL, 1, 'Financial', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 12:28:53', '2026-02-26 15:43:37'),
(338, 'INT-2026-00035', 'Finance Disbursement Records', 'finance', 17, 'report', 'All disbursement requests from Finance system.', '/uploads/documents/finance/live_finance_disbursements.pdf', 'live_finance_disbursements.pdf', 2089, 'application/pdf', 1, '[\"live-data\",\"finance\",\"transfer\",\"2026-02\"]', NULL, 1, 'Financial', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 12:28:56', '2026-02-26 15:43:37'),
(339, 'INT-2026-00036', 'Finance Budget Proposals', 'finance', 16, 'report', 'Public budget proposals with approval status from Finance.', '/uploads/documents/finance/live_finance_proposals.pdf', 'live_finance_proposals.pdf', 3864, 'application/pdf', 1, '[\"live-data\",\"finance\",\"transfer\",\"2026-02\"]', NULL, 1, 'Financial', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 12:28:57', '2026-02-26 15:43:37'),
(340, 'INT-2026-00037', 'Finance Admin Received Proposals', 'finance', 16, 'report', 'Proposals received from other departments for admin review.', '/uploads/documents/finance/live_finance_admin_proposals.pdf', 'live_finance_admin_proposals.pdf', 3873, 'application/pdf', 1, '[\"live-data\",\"finance\",\"transfer\",\"2026-02\"]', NULL, 1, 'Financial', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 12:28:57', '2026-02-26 15:43:37'),
(341, 'INT-2026-00038', 'Finance System Users', 'finance', 5, 'report', 'Finance module user accounts directory.', '/uploads/documents/finance/live_finance_users.pdf', 'live_finance_users.pdf', 2519, 'application/pdf', 1, '[\"live-data\",\"finance\",\"transfer\",\"2026-02\"]', NULL, 1, 'Financial', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 12:28:58', '2026-02-26 15:43:37'),
(342, 'INT-2026-00039', 'Finance Dashboard Summary', 'finance', 1, 'report', 'Aggregate financial dashboard statistics.', '/uploads/documents/finance/live_finance_dashboard.pdf', 'live_finance_dashboard.pdf', 2344, 'application/pdf', 1, '[\"live-data\",\"finance\",\"transfer\",\"2026-02\"]', NULL, 1, 'Financial', 'internal', 'active', 'Finance', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(343, 'INT-2026-00040', 'Legal Cases Report', 'legal', 3, 'report', 'All legal cases from the Legal Management module.', '/uploads/documents/legal/live_legal_cases.pdf', 'live_legal_cases.pdf', 3130, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(344, 'INT-2026-00041', 'Legal Contracts Register', 'legal', 3, 'report', 'All legal contracts and agreements.', '/uploads/documents/legal/live_legal_contracts.pdf', 'live_legal_contracts.pdf', 3245, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(345, 'INT-2026-00042', 'Legal Compliance Tracker', 'legal', 3, 'report', 'Compliance tracking records.', '/uploads/documents/legal/live_legal_compliance.pdf', 'live_legal_compliance.pdf', 3115, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(346, 'INT-2026-00043', 'Loan Documentation Report', 'legal', 7, 'report', 'Loan documentation records from Legal module.', '/uploads/documents/legal/live_legal_loans.pdf', 'live_legal_loans.pdf', 3256, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(347, 'INT-2026-00044', 'Collateral Registry Report', 'legal', 7, 'report', 'Collateral records linked to loan documentation.', '/uploads/documents/legal/live_legal_collaterals.pdf', 'live_legal_collaterals.pdf', 3260, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(348, 'INT-2026-00045', 'Demand Letters Report', 'legal', 3, 'report', 'Demand letter records from Legal.', '/uploads/documents/legal/live_legal_demands.pdf', 'live_legal_demands.pdf', 2642, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(349, 'INT-2026-00046', 'KYC Records Report', 'legal', 6, 'report', 'Know-Your-Customer verification records.', '/uploads/documents/legal/live_legal_kyc.pdf', 'live_legal_kyc.pdf', 3123, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(350, 'INT-2026-00047', 'Board Resolutions Register', 'legal', 8, 'report', 'Board resolution records.', '/uploads/documents/legal/live_legal_resolutions.pdf', 'live_legal_resolutions.pdf', 2639, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(351, 'INT-2026-00048', 'Power of Attorney Register', 'legal', 3, 'report', 'Power of Attorney records.', '/uploads/documents/legal/live_legal_poa.pdf', 'live_legal_poa.pdf', 2517, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(352, 'INT-2026-00049', 'Permits & Licenses Report', 'legal', 3, 'report', 'Permits and licenses tracking.', '/uploads/documents/legal/live_legal_permits.pdf', 'live_legal_permits.pdf', 3118, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37'),
(353, 'INT-2026-00050', 'Legal Dashboard Statistics', 'legal', 3, 'report', 'Aggregate legal module dashboard statistics.', '/uploads/documents/legal/live_legal_dashboard.pdf', 'live_legal_dashboard.pdf', 2965, 'application/pdf', 1, '[\"live-data\",\"legal\",\"transfer\",\"2026-02\"]', NULL, 1, 'Administrative', 'internal', 'active', 'Legal', NULL, NULL, '2026-02-26 12:29:01', '2026-02-26 15:43:37');

-- --------------------------------------------------------

--
-- Table structure for table `document_access`
--

CREATE TABLE `document_access` (
  `access_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission` enum('view','download','edit','admin') NOT NULL DEFAULT 'view',
  `granted_by` int(11) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_access`
--

INSERT INTO `document_access` (`access_id`, `document_id`, `user_id`, `permission`, `granted_by`, `expires_at`, `created_at`) VALUES
(1, 1, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(2, 2, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(3, 3, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(4, 4, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(5, 5, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(6, 6, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(7, 7, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(8, 8, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(9, 9, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(10, 10, 1, 'admin', NULL, NULL, '2026-02-26 11:08:56'),
(11, 2, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(12, 9, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(13, 10, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(14, 13, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(15, 14, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(16, 27, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(17, 28, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(18, 34, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(19, 35, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(20, 47, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(21, 56, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(22, 57, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(23, 63, 2, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(24, 3, 3, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(25, 33, 3, 'view', 1, NULL, '2026-02-26 11:08:56'),
(26, 43, 3, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(27, 126, 3, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(28, 130, 3, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(29, 140, 3, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(30, 1, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(31, 18, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(32, 19, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(33, 22, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(34, 24, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(35, 96, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(36, 101, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(37, 108, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(38, 109, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(39, 110, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(40, 111, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(41, 112, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(42, 113, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(43, 114, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(44, 120, 4, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(45, 4, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(46, 64, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(47, 65, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(48, 74, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(49, 75, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(50, 81, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(51, 84, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(52, 87, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(53, 88, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(54, 89, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(55, 90, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(56, 94, 5, 'edit', 1, NULL, '2026-02-26 11:08:56'),
(57, 1, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(58, 2, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(59, 3, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(60, 4, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(61, 5, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(62, 6, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(63, 7, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(64, 8, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(65, 9, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(66, 10, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(67, 11, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(68, 12, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(69, 13, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(70, 14, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(71, 15, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(72, 16, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(73, 17, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(74, 18, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(75, 19, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(76, 20, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(77, 21, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(78, 22, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(79, 23, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(80, 24, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(81, 25, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(82, 26, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(83, 27, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(84, 28, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(85, 29, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(86, 30, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(87, 31, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(88, 32, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(89, 33, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(90, 34, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(91, 35, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(92, 36, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(93, 37, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(94, 38, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(95, 39, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(96, 40, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(97, 41, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(98, 42, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(99, 43, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(100, 44, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(101, 45, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(102, 46, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(103, 47, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(104, 48, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(105, 49, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(106, 50, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(107, 51, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(108, 52, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(109, 53, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(110, 54, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(111, 55, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(112, 56, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(113, 57, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(114, 58, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(115, 59, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(116, 60, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(117, 61, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(118, 62, 18, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(119, 63, 18, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(120, 64, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(121, 65, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(122, 66, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(123, 67, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(124, 68, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(125, 69, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(126, 70, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(127, 71, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(128, 72, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(129, 73, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(130, 74, 23, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(131, 75, 23, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(132, 76, 23, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(133, 77, 23, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(134, 78, 23, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(135, 79, 23, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(136, 80, 23, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(137, 81, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(138, 82, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(139, 83, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(140, 84, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(141, 85, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(142, 86, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(143, 87, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(144, 88, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(145, 89, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(146, 90, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(147, 91, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(148, 92, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(149, 93, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(150, 94, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(151, 95, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(152, 96, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(153, 97, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(154, 98, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(155, 99, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(156, 100, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(157, 101, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(158, 102, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(159, 103, 20, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(160, 104, 20, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(161, 105, 20, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(162, 106, 20, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(163, 107, 20, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(164, 108, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(165, 109, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(166, 110, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(167, 111, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(168, 112, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(169, 113, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(170, 114, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(171, 115, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(172, 116, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(173, 117, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(174, 118, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(175, 119, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(176, 120, 20, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(177, 121, 20, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(178, 122, 20, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(179, 123, 20, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(180, 124, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(181, 125, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(182, 126, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(183, 127, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(184, 128, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(185, 129, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(186, 130, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(187, 131, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(188, 132, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(189, 133, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(190, 134, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(191, 135, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(192, 136, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(193, 137, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(194, 138, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(195, 139, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(196, 140, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(197, 141, 21, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(198, 142, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(199, 143, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(200, 144, 23, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(201, 145, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(202, 146, 16, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(203, 147, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(204, 148, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(205, 304, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(206, 305, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(207, 306, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(208, 307, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(209, 308, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(210, 309, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(211, 310, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(212, 311, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(213, 312, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(214, 313, 17, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(215, 314, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(216, 315, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(217, 316, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(218, 317, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(219, 318, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(220, 319, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(221, 320, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(222, 321, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(223, 322, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(224, 323, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(225, 324, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(226, 325, 19, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(227, 326, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(228, 327, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(229, 328, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(230, 329, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(231, 330, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(232, 331, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(233, 332, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(234, 333, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(235, 334, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(236, 335, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(237, 336, 22, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(238, 337, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(239, 338, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(240, 339, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(241, 340, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(242, 341, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(243, 342, 24, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(244, 343, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(245, 344, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(246, 345, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(247, 346, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(248, 347, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(249, 348, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(250, 349, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(251, 350, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(252, 351, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(253, 352, 25, 'admin', 1, NULL, '2026-02-26 13:56:24'),
(254, 353, 25, 'admin', 1, NULL, '2026-02-26 13:56:24');

-- --------------------------------------------------------

--
-- Table structure for table `document_access_requests`
--

CREATE TABLE `document_access_requests` (
  `request_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `document_code` varchar(50) DEFAULT NULL,
  `document_title` varchar(300) DEFAULT NULL,
  `requested_by` int(11) NOT NULL,
  `requester_name` varchar(200) DEFAULT NULL,
  `requester_role` varchar(50) DEFAULT NULL,
  `requester_dept` varchar(100) DEFAULT NULL,
  `permission_requested` varchar(30) DEFAULT 'view',
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewer_name` varchar(200) DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_categories`
--

CREATE TABLE `document_categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `code` varchar(20) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_categories`
--

INSERT INTO `document_categories` (`category_id`, `name`, `code`, `parent_id`, `description`, `icon`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Financial Documents', 'FIN', NULL, 'Microfinancial statements, reports, and records', NULL, 1, 1, '2026-02-24 10:03:01'),
(2, 'Human Resources', 'HR', NULL, 'Employee records, contracts, and HR policies', NULL, 2, 1, '2026-02-24 10:03:01'),
(3, 'Legal & Compliance', 'LEG', NULL, 'Legal documents, regulations, and compliance records', NULL, 3, 1, '2026-02-24 10:03:01'),
(4, 'Operations', 'OPS', NULL, 'Operational procedures, SOPs, and manuals', NULL, 4, 1, '2026-02-24 10:03:01'),
(5, 'Administrative', 'ADM', NULL, 'Administrative memos, circulars, and notices', NULL, 5, 1, '2026-02-24 10:03:01'),
(6, 'Client Records', 'CLT', NULL, 'Microfinancial client documents and applications', NULL, 6, 1, '2026-02-24 10:03:01'),
(7, 'Loan Documents', 'LN', NULL, 'Loan agreements, promissory notes, and amortization', NULL, 7, 1, '2026-02-24 10:03:01'),
(8, 'Board Resolutions', 'BRD', NULL, 'Board meeting minutes and resolutions', NULL, 8, 1, '2026-02-24 10:03:01'),
(9, 'Payroll & Compensation', 'PAY', NULL, 'Payroll runs, payslips, compensation adjustments', NULL, 9, 1, '2026-02-26 11:07:24'),
(10, 'Training & Development', 'TRN', NULL, 'Training materials, certifications, and evaluations', NULL, 10, 1, '2026-02-26 11:07:24'),
(11, 'Talent Management', 'TAL', NULL, 'Competency frameworks, succession plans, and talent pool records', NULL, 11, 1, '2026-02-26 11:07:24'),
(12, 'Procurement & Sourcing', 'PSM', NULL, 'Purchase orders, requisitions, and vendor documents', NULL, 12, 1, '2026-02-26 11:07:24'),
(13, 'Warehouse & Inventory', 'SWS', NULL, 'Inventory reports, warehouse logs, and asset receipts', NULL, 13, 1, '2026-02-26 11:07:24'),
(14, 'Project Logistics', 'PLT', NULL, 'Project plans, milestone reports, and dispatch records', NULL, 14, 1, '2026-02-26 11:07:24'),
(15, 'Asset Management', 'ALMS', NULL, 'Asset lifecycle records, maintenance logs, and repair documents', NULL, 15, 1, '2026-02-26 11:07:24'),
(16, 'Budget & Forecasting', 'BGT', NULL, 'Budget proposals, forecasts, and variance analysis', NULL, 16, 1, '2026-02-26 11:07:24'),
(17, 'Disbursement Records', 'DSB', NULL, 'Disbursement requests, approvals, and payment vouchers', NULL, 17, 1, '2026-02-26 11:07:24'),
(18, 'Accounting & GL', 'AGL', NULL, 'Journal entries, ledger reports, and chart of accounts', NULL, 18, 1, '2026-02-26 11:07:24'),
(19, 'Collections', 'COL', NULL, 'Collection reports, payment receipts, and aging schedules', NULL, 19, 1, '2026-02-26 11:07:24'),
(20, 'HMO & Benefits', 'HMO', NULL, 'Benefits enrollment, HMO plans, and dependent records', NULL, 20, 1, '2026-02-26 11:07:24');

-- --------------------------------------------------------

--
-- Table structure for table `document_versions`
--

CREATE TABLE `document_versions` (
  `version_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `version_number` int(11) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_name` varchar(300) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL,
  `change_notes` text DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_view_logs`
--

CREATE TABLE `document_view_logs` (
  `log_id` int(11) NOT NULL,
  `document_id` int(11) DEFAULT NULL,
  `document_code` varchar(50) DEFAULT NULL,
  `document_title` varchar(300) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `source_system` varchar(50) DEFAULT NULL,
  `action` varchar(30) NOT NULL DEFAULT 'view',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `user_name` varchar(200) DEFAULT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  `user_department` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `access_method` varchar(50) DEFAULT 'direct',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_view_logs`
--

INSERT INTO `document_view_logs` (`log_id`, `document_id`, `document_code`, `document_title`, `department`, `source_system`, `action`, `user_id`, `user_name`, `user_role`, `user_department`, `ip_address`, `user_agent`, `file_type`, `file_size`, `access_method`, `created_at`) VALUES
(1, 343, 'INT-2026-00040', 'Legal Cases Report', 'Administrative', NULL, 'view', 1, 'Admin', 'super_admin', 'IT Department', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'APPLICATION/PDF', 3130, 'role_based', '2026-03-03 19:03:13'),
(2, 343, 'INT-2026-00040', 'Legal Cases Report', 'Administrative', NULL, 'view', 1, 'Admin', 'super_admin', 'IT Department', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'APPLICATION/PDF', 3130, 'role_based', '2026-03-04 16:37:26'),
(3, 343, 'INT-2026-00040', 'Legal Cases Report', 'Administrative', NULL, 'view', 1, 'Admin', 'super_admin', 'IT Department', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'APPLICATION/PDF', 3130, 'role_based', '2026-03-04 16:45:16'),
(4, 345, 'INT-2026-00042', 'Legal Compliance Tracker', 'Administrative', NULL, 'view', 1, 'Admin', 'super_admin', 'IT Department', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'APPLICATION/PDF', 3115, 'role_based', '2026-03-04 16:48:38'),
(5, 345, 'INT-2026-00042', 'Legal Compliance Tracker', 'Administrative', NULL, 'download', 1, 'Admin', 'super_admin', 'IT Department', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'APPLICATION/PDF', 3115, 'role_based', '2026-03-04 16:48:44'),
(6, 343, 'INT-2026-00040', 'Legal Cases Report', 'Administrative', NULL, 'view', 1, 'Jessel Obina', 'super_admin', 'IT Department', '2001:4451:4722:1f00:fd27:3a61:a675:b8c9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'APPLICATION/PDF', 3130, 'role_based', '2026-03-05 16:18:21');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `facility_id` int(11) NOT NULL,
  `facility_code` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` enum('conference_room','meeting_room','training_hall','auditorium','parking','equipment','other') NOT NULL,
  `room_level` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Interview, 2=Training, 3=VIP/Meeting, 4=Emergency',
  `location` varchar(200) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `image_url` varchar(500) DEFAULT NULL,
  `status` enum('available','occupied','maintenance','retired') NOT NULL DEFAULT 'available',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`facility_id`, `facility_code`, `name`, `type`, `room_level`, `location`, `capacity`, `description`, `amenities`, `hourly_rate`, `image_url`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'FR-INT-A', 'Interview Room A', 'meeting_room', 1, 'Floor 1', 8, 'Applicant interviews and recruitment screenings', NULL, 0.00, NULL, 'available', NULL, '2026-03-05 00:00:00', '2026-03-05 00:00:00'),
(2, 'FR-INT-B', 'Interview Room B', 'meeting_room', 1, 'Floor 1', 6, 'Workforce entry assessments and onboarding interviews', NULL, 0.00, NULL, 'available', NULL, '2026-03-05 00:00:00', '2026-03-05 22:35:37'),
(3, 'FR-TRN-A', 'Training Hall A', 'training_hall', 2, 'Floor 2', 40, 'Learning management and competency training sessions', NULL, 0.00, NULL, 'available', NULL, '2026-03-05 00:00:00', '2026-03-05 00:00:00'),
(4, 'FR-TRN-B', 'Training Hall B', 'training_hall', 2, 'Floor 2', 30, 'Succession planning and career development programs', NULL, 0.00, NULL, 'available', NULL, '2026-03-05 00:00:00', '2026-03-05 00:00:00'),
(5, 'FR-TXN-A', 'Transaction Room A', 'conference_room', 2, 'Floor 3', 20, 'Loan processing and client services meetings', NULL, 0.00, NULL, 'available', NULL, '2026-03-05 00:00:00', '2026-03-05 00:00:00'),
(6, 'FR-TXN-B', 'Transaction Room B', 'conference_room', 2, 'Floor 3', 15, 'Savings management and portfolio review sessions', NULL, 0.00, NULL, 'available', NULL, '2026-03-05 00:00:00', '2026-03-05 00:00:00'),
(7, 'FR-FLT-A', 'Fleet Operations Room', 'meeting_room', 2, 'Floor 2', 10, 'Fleet dispatch, driver management and transport planning', NULL, 0.00, NULL, 'available', NULL, '2026-03-05 00:00:00', '2026-03-05 22:35:29'),
(8, 'FR-EXB-A', 'Executive Boardroom', 'conference_room', 3, 'Floor 5', 20, 'Executive board meetings, strategic planning and institutional oversight', NULL, 0.00, NULL, 'available', NULL, '2026-03-05 00:00:00', '2026-03-06 12:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `facility_equipment`
--

CREATE TABLE `facility_equipment` (
  `equipment_id` int(11) NOT NULL,
  `equipment_code` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `facility_id` int(11) DEFAULT NULL,
  `condition_status` enum('excellent','good','fair','needs_repair','retired') NOT NULL DEFAULT 'good',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `last_maintained` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facility_maintenance`
--

CREATE TABLE `facility_maintenance` (
  `maintenance_id` int(11) NOT NULL,
  `ticket_number` varchar(20) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `reported_by` int(11) NOT NULL,
  `issue_type` enum('electrical','plumbing','hvac','structural','equipment','cleaning','other') NOT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `description` text NOT NULL,
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `assigned_to` varchar(200) DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facility_reservations`
--

CREATE TABLE `facility_reservations` (
  `reservation_id` int(11) NOT NULL,
  `reservation_code` varchar(20) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `reserved_by` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `purpose` varchar(500) NOT NULL,
  `event_title` varchar(200) DEFAULT NULL,
  `reservation_type` enum('regular','vip','emergency') NOT NULL DEFAULT 'regular',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `attendees_count` int(11) DEFAULT NULL,
  `budget` decimal(12,2) DEFAULT 0.00,
  `equipment_needed` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of equipment names/ids requested' CHECK (json_valid(`equipment_needed`)),
  `special_requests` text DEFAULT NULL,
  `is_validated` tinyint(1) NOT NULL DEFAULT 0,
  `validated_by` int(11) DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled','completed') NOT NULL DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `qr_code_id` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `reschedule_reason` text DEFAULT NULL COMMENT 'Required for Level 2+ room rescheduling',
  `rescheduled_by` int(11) DEFAULT NULL,
  `rescheduled_at` datetime DEFAULT NULL,
  `original_start` datetime DEFAULT NULL COMMENT 'Original start time before reschedule',
  `original_end` datetime DEFAULT NULL COMMENT 'Original end time before reschedule',
  `cancelled_by` int(11) DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `is_auto_tagged` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 if VIP/Emergency was auto-tagged by system based on position_level',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility_reservations`
--

INSERT INTO `facility_reservations` (`reservation_id`, `reservation_code`, `facility_id`, `reserved_by`, `department`, `purpose`, `event_title`, `reservation_type`, `priority`, `start_datetime`, `end_datetime`, `attendees_count`, `budget`, `equipment_needed`, `special_requests`, `is_validated`, `validated_by`, `validated_at`, `status`, `approved_by`, `approved_at`, `qr_code_id`, `remarks`, `reschedule_reason`, `rescheduled_by`, `rescheduled_at`, `original_start`, `original_end`, `cancelled_by`, `cancelled_at`, `cancel_reason`, `is_auto_tagged`, `created_at`, `updated_at`) VALUES
(18, 'RES-2026-242497', 8, 3, 'Executive Office', '31231231', '12312', 'regular', 'urgent', '2026-03-06 06:29:00', '2026-03-06 10:29:00', 12, 100000.00, '[]', '123123', 0, NULL, NULL, 'completed', 3, '2026-03-06 06:30:08', NULL, 'Approved by admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-05 22:29:41', '2026-03-06 12:08:14'),
(19, 'RES-2026-725118', 7, 3, 'Executive Office', 'fadasdasd', '12312', 'regular', 'normal', '2026-03-06 06:31:00', '2026-03-06 11:31:00', 15, 10000.00, '[]', 'sadasfad', 0, NULL, NULL, 'cancelled', 3, '2026-03-06 06:31:58', NULL, 'Approved by admin\n[Cancelled: No reason provided]', NULL, NULL, NULL, NULL, NULL, 3, '2026-03-06 06:33:40', '', 0, '2026-03-05 22:31:45', '2026-03-05 22:33:40'),
(20, 'RES-2026-969079', 1, 3, 'Executive Office', '1231231', '2131231', 'regular', 'normal', '2026-03-06 06:32:00', '2026-03-06 11:32:00', 8, 123123.00, '[]', 'zdasgbfsdfasda', 1, 3, '2026-03-06 06:33:50', 'cancelled', NULL, NULL, NULL, '\n[Validated]\n[Validated]\n[Cancelled: No reason provided]', NULL, NULL, NULL, NULL, NULL, 3, '2026-03-06 06:33:53', '', 0, '2026-03-05 22:33:05', '2026-03-05 22:33:53'),
(21, 'RES-2026-517541', 7, 3, 'Executive Office', 'asdasd', '12312asdasd', 'regular', 'normal', '2026-03-07 06:34:00', '2026-03-07 10:34:00', 10, 123456.00, '[]', 'sdasfadas', 0, NULL, NULL, 'approved', 3, '2026-03-06 06:35:27', NULL, 'Approved by admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-05 22:34:31', '2026-03-05 22:35:27'),
(22, 'RES-2026-143825', 2, 3, 'Credit Department', 'asdasd', 'asdasd', 'regular', 'normal', '2026-03-08 07:34:00', '2026-03-08 12:34:00', 12, 123123.00, '[]', 'sdagsdasdas', 0, NULL, NULL, 'approved', 3, '2026-03-06 06:35:36', NULL, 'Approved by admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-05 22:35:16', '2026-03-05 22:35:36'),
(23, 'RES-2026-405084', 8, 3, 'Executive Office', 'asdadadasd', '🏫 Training: Customer Service Excellence', 'regular', 'normal', '2026-03-15 12:13:00', '2026-03-15 16:13:00', 15, 12353523.00, '[]', 'sadasdasdasd', 0, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-06 14:13:53', '2026-03-06 14:13:53'),
(24, 'RES-2026-781447', 1, 3, 'IT Department', 'asdasasd', 'adasdasd', 'regular', 'high', '2026-03-16 10:14:00', '2026-03-16 17:14:00', 5, 14123123.00, '[]', 'asdasfas', 0, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-06 14:14:47', '2026-03-06 14:14:47'),
(25, 'RES-2026-499610', 3, 3, 'IT Department', 'asdasdasdasd', 'asdasdasdad', 'regular', 'normal', '2026-03-17 07:00:00', '2026-03-17 12:00:00', 30, 120000.00, '[]', 'asdasda', 0, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-06 14:16:04', '2026-03-06 14:16:04'),
(26, 'RES-2026-175144', 4, 3, 'IT Department', 'asdasd', 'asdasdasdasd', 'regular', 'high', '2026-03-18 08:00:00', '2026-03-18 14:00:00', 30, 1231231231.00, '[]', 'aczxczxcasd', 0, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-06 14:17:25', '2026-03-06 14:17:25'),
(27, 'RES-2026-572812', 6, 3, 'IT Department', 'asdasdsadasd', 'asdasd', 'regular', 'high', '2026-03-14 12:23:00', '2026-03-14 17:23:00', 21, 123123.00, '[]', 'asdasdasd', 0, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-06 14:24:07', '2026-03-06 14:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `facility_room_usage_logs`
--

CREATE TABLE `facility_room_usage_logs` (
  `log_id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `reservation_code` varchar(50) DEFAULT NULL,
  `facility_id` int(11) DEFAULT NULL,
  `facility_name` varchar(200) DEFAULT NULL,
  `room_level` int(11) DEFAULT NULL,
  `reservation_type` varchar(50) DEFAULT NULL,
  `event_title` varchar(300) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `reserved_by` int(11) DEFAULT NULL,
  `reserved_by_name` varchar(200) DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `attendees_count` int(11) DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT 0.00,
  `equipment_used` text DEFAULT NULL,
  `status` enum('completed','cancelled','no_show','in_progress') DEFAULT 'completed',
  `completed_by` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facility_room_usage_logs`
--

INSERT INTO `facility_room_usage_logs` (`log_id`, `reservation_id`, `reservation_code`, `facility_id`, `facility_name`, `room_level`, `reservation_type`, `event_title`, `purpose`, `department`, `reserved_by`, `reserved_by_name`, `start_datetime`, `end_datetime`, `duration_minutes`, `attendees_count`, `budget`, `equipment_used`, `status`, `completed_by`, `remarks`, `logged_at`) VALUES
(1, 19, 'RES-2026-725118', 7, 'Fleet Operations Room', 2, 'regular', '12312', 'fadasdasd', 'Executive Office', 3, NULL, '2026-03-06 06:31:00', '2026-03-06 11:31:00', 300, 15, 10000.00, '[]', 'cancelled', 3, 'Approved by admin\n[Cancelled: No reason provided]', '2026-03-05 22:33:40'),
(2, 20, 'RES-2026-969079', 1, 'Interview Room A', 1, 'regular', '2131231', '1231231', 'Executive Office', 3, NULL, '2026-03-06 06:32:00', '2026-03-06 11:32:00', 300, 8, 123123.00, '[]', 'cancelled', 3, '\n[Validated]\n[Validated]\n[Cancelled: No reason provided]', '2026-03-05 22:33:53'),
(3, 18, 'RES-2026-242497', 8, 'Executive Boardroom', 3, 'regular', '12312', '31231231', 'Executive Office', 3, NULL, '2026-03-06 06:29:00', '2026-03-06 10:29:00', 240, 12, 100000.00, '[]', 'completed', NULL, 'Approved by admin', '2026-03-06 12:08:14');

-- --------------------------------------------------------

--
-- Table structure for table `kyc_records`
--

CREATE TABLE `kyc_records` (
  `kyc_id` int(11) NOT NULL,
  `kyc_code` varchar(30) NOT NULL,
  `client_name` varchar(300) NOT NULL,
  `client_type` enum('individual','corporate','partnership','sole_proprietor') NOT NULL DEFAULT 'individual',
  `id_type` varchar(100) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `id_expiry` date DEFAULT NULL,
  `tin` varchar(20) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `occupation` varchar(200) DEFAULT NULL,
  `source_of_funds` varchar(300) DEFAULT NULL,
  `risk_rating` enum('low','medium','high','pep') NOT NULL DEFAULT 'low',
  `verification_status` enum('pending','verified','rejected','expired','under_review') NOT NULL DEFAULT 'pending',
  `verified_by` int(11) DEFAULT NULL,
  `verified_date` date DEFAULT NULL,
  `aml_flag` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Anti-Money Laundering red flag',
  `aml_notes` text DEFAULT NULL,
  `sanctions_checked` tinyint(1) NOT NULL DEFAULT 0,
  `pep_checked` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Politically Exposed Person check',
  `next_review_date` date DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kyc_records`
--

INSERT INTO `kyc_records` (`kyc_id`, `kyc_code`, `client_name`, `client_type`, `id_type`, `id_number`, `id_expiry`, `tin`, `address`, `occupation`, `source_of_funds`, `risk_rating`, `verification_status`, `verified_by`, `verified_date`, `aml_flag`, `aml_notes`, `sanctions_checked`, `pep_checked`, `next_review_date`, `document_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'KYC-2026-001', 'Roberto A. Mendoza', 'individual', 'PhilSys ID (National ID)', 'PSA-2020-12345', '2031-06-15', '123-456-789-000', '123 Rizal Avenue, Makati City', 'Small Business Owner', 'Business Income ΓÇö Sari-sari Store', 'low', 'verified', 3, '2026-01-08', 0, NULL, 1, 1, '2027-01-08', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'KYC-2026-002', 'Elena G. Fernandez', 'individual', 'Driver\'s License', 'DL-N01-234567', '2028-03-20', '987-654-321-000', '456 Mabini Street, San Juan City', 'Public School Teacher', 'Employment Salary', 'low', 'verified', 3, '2026-01-22', 0, NULL, 1, 1, '2027-01-22', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'KYC-2026-003', 'ABC Corporation', 'corporate', 'SEC Registration', 'CS201912345', NULL, '555-123-456-000', '789 Commercial Drive, Taguig City', 'Retail & Distribution', 'Business Revenue', 'medium', 'verified', 3, '2026-01-28', 0, NULL, 1, 0, '2026-07-28', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'KYC-2026-004', 'Grace S. Aquino', 'individual', 'Passport', 'P1234567A', '2029-11-30', '444-888-222-000', '321 Luna Street, Quezon City', 'Freelance Consultant', 'Professional Fees', 'low', 'verified', 3, '2026-02-08', 0, NULL, 1, 1, '2027-02-08', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(5, 'KYC-2026-005', 'Michael B. Tan', 'sole_proprietor', 'Company ID / DTI Registration', 'DTI-NCR-2024-56789', NULL, '777-333-111-000', '567 Bonifacio Avenue, Taguig City', 'Printing Business Owner', 'Business Income', 'high', 'under_review', NULL, NULL, 0, NULL, 1, 0, NULL, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(6, 'KYC-2026-006', 'Rosario M. Villanueva', 'individual', 'PhilSys ID (National ID)', 'PSA-2021-78901', '2032-09-10', '222-333-444-000', '89 Sampaguita Street, Brgy. Holy Spirit, QC', 'Market Vendor / Entrepreneur', 'Business Income ΓÇö Food Cart', 'low', 'verified', 3, '2026-02-03', 0, NULL, 1, 1, '2027-02-03', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(7, 'KYC-2026-007', 'Fernando C. Aguilar', 'sole_proprietor', 'Driver\'s License', 'DL-N03-567890', '2029-04-18', '888-777-666-000', '45 Mahogany Lane, BF Homes, Para├▒aque City', 'Agricultural Supply Business Owner', 'Business Revenue ΓÇö AgriSupply Store', 'medium', 'verified', 3, '2026-02-10', 0, NULL, 1, 0, '2026-08-10', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `legal_cases`
--

CREATE TABLE `legal_cases` (
  `case_id` int(11) NOT NULL,
  `case_number` varchar(30) NOT NULL,
  `title` varchar(300) NOT NULL,
  `case_type` enum('litigation','arbitration','mediation','regulatory','compliance','internal_investigation','other','loan_default','fraud','theft','harassment','data_breach','forgery','contract_violation','policy_violation') DEFAULT 'other',
  `description` text NOT NULL,
  `priority` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `severity` enum('minor','moderate','major') DEFAULT 'moderate',
  `status` enum('open','in_progress','pending_review','resolved','closed','appealed') NOT NULL DEFAULT 'open',
  `workflow_step` enum('complaint_filed','under_review','for_hearing','ongoing_investigation','verdict','closed','dismissed') DEFAULT 'complaint_filed',
  `verdict` enum('not_guilty','guilty_warning','guilty_suspension','guilty_termination','filed_in_court','deduct_salary','dismissed') DEFAULT NULL,
  `penalty_details` text DEFAULT NULL,
  `legal_officer` varchar(200) DEFAULT NULL,
  `next_hearing` date DEFAULT NULL,
  `admin_decision` enum('dismiss','internal_discipline','escalate_legal','return_to_dept') DEFAULT NULL,
  `escalation_deadline` datetime DEFAULT NULL,
  `auto_escalated` tinyint(1) DEFAULT 0,
  `linked_loan_id` int(11) DEFAULT NULL,
  `penalty_amount` decimal(15,2) DEFAULT NULL,
  `filing_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `resolution_date` date DEFAULT NULL,
  `opposing_party` varchar(300) DEFAULT NULL,
  `complainant_name` varchar(300) DEFAULT NULL,
  `complainant_department` varchar(100) DEFAULT NULL,
  `accused_name` varchar(300) DEFAULT NULL,
  `accused_department` varchar(100) DEFAULT NULL,
  `accused_employee_id` varchar(20) DEFAULT NULL,
  `court_venue` varchar(300) DEFAULT NULL,
  `assigned_lawyer` varchar(200) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `financial_impact` decimal(15,2) DEFAULT NULL COMMENT 'Estimated financial impact in microfinancial operations',
  `resolution_summary` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `legal_cases`
--

INSERT INTO `legal_cases` (`case_id`, `case_number`, `title`, `case_type`, `description`, `priority`, `severity`, `status`, `workflow_step`, `verdict`, `penalty_details`, `legal_officer`, `next_hearing`, `admin_decision`, `escalation_deadline`, `auto_escalated`, `linked_loan_id`, `penalty_amount`, `filing_date`, `due_date`, `resolution_date`, `opposing_party`, `complainant_name`, `complainant_department`, `accused_name`, `accused_department`, `accused_employee_id`, `court_venue`, `assigned_lawyer`, `assigned_to`, `department`, `financial_impact`, `resolution_summary`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'LC-2026-001', 'Loan Default Recovery - ABC Corp', 'litigation', 'Recovery of defaulted microfinancial loan amounting to PHP 2.5M', 'high', 'moderate', 'pending_review', 'verdict', 'guilty_warning', NULL, NULL, NULL, NULL, '2026-03-10 17:40:18', 0, NULL, NULL, '2026-01-10', '2026-06-30', '2026-03-04', 'ABC Corporation', NULL, NULL, NULL, NULL, NULL, 'Regional Trial Court - Makati', 'Atty. Dela Rosa', 3, 'Legal Department', 2500000.00, NULL, 3, '2026-02-24 09:40:58', '2026-03-03 16:40:37'),
(2, 'LC-2026-002', 'Data Privacy Complaint', 'regulatory', 'NPC complaint regarding client data handling procedures', 'critical', 'moderate', 'open', 'complaint_filed', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-02-01', '2026-03-15', NULL, 'National Privacy Commission', NULL, NULL, NULL, NULL, NULL, 'NPC Office', 'Atty. Santos', 3, 'Legal Department', 500000.00, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'LC-2026-003', 'Employee Labor Dispute - Retrenchment', 'mediation', 'DOLE mediation for dispute on retrenchment benefits', 'medium', 'moderate', 'pending_review', 'complaint_filed', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-01-20', '2026-04-01', NULL, 'Former Employee Group', NULL, NULL, NULL, NULL, NULL, 'DOLE NCR Office', 'Atty. Cruz', 3, 'Legal Department', 800000.00, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'LC-2026-004', 'BSP Audit Compliance Review', 'compliance', 'Preparation for BSP scheduled audit on microfinancial operations', 'high', 'moderate', 'in_progress', 'complaint_filed', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-02-05', '2026-03-01', NULL, 'Bangko Sentral ng Pilipinas', NULL, NULL, NULL, NULL, NULL, 'BSP Main Office', 'Atty. Dela Rosa', 3, 'Legal Department', 0.00, NULL, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(5, 'LC-2026-005', 'Foreclosure Proceedings ΓÇö Tan Property', 'litigation', 'Judicial foreclosure of chattel mortgage on printing equipment due to loan default by Michael B. Tan (LD-2026-005)', 'high', 'moderate', 'open', 'complaint_filed', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-02-15', '2026-08-15', NULL, 'Michael B. Tan', NULL, NULL, NULL, NULL, NULL, 'Metropolitan Trial Court - Taguig', 'Atty. Francisco J. Dela Rosa', 3, 'Legal Department', 535000.00, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(6, 'LC-2026-006', 'Unfair Collection Practices Complaint', 'regulatory', 'Complaint filed by borrower alleging harassment by third-party collection agent', 'medium', 'moderate', 'pending_review', 'complaint_filed', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-02-08', '2026-05-08', NULL, 'Josefina R. Bautista', NULL, NULL, NULL, NULL, NULL, 'DTI-NCR Mediation Office', 'Atty. Maria Teresa R. Santos', 3, 'Legal Department', 150000.00, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(7, 'LC-2026-007', 'Internal Fraud Investigation ΓÇö Taguig Branch', 'internal_investigation', 'Investigation of suspected fraudulent loan approvals at the Taguig branch involving three fictitious borrower accounts', 'critical', 'moderate', 'in_progress', 'complaint_filed', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-02-12', '2026-04-12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Atty. Cruz', 3, 'Legal Department', 1200000.00, NULL, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `legal_case_documents`
--

CREATE TABLE `legal_case_documents` (
  `id` int(11) NOT NULL,
  `case_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  `compliance_id` int(11) DEFAULT NULL,
  `document_id` int(11) NOT NULL,
  `doc_label` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `legal_case_evidence`
--

CREATE TABLE `legal_case_evidence` (
  `evidence_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `evidence_type` enum('document','photo','video','audio','email','report','other') NOT NULL DEFAULT 'document',
  `file_name` varchar(300) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `legal_case_hearings`
--

CREATE TABLE `legal_case_hearings` (
  `hearing_id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `hearing_date` datetime NOT NULL,
  `hearing_type` enum('initial_review','admin_hearing','investigation','formal_hearing','verdict_hearing','follow_up') NOT NULL DEFAULT 'initial_review',
  `location` varchar(300) DEFAULT NULL,
  `officer_name` varchar(200) DEFAULT NULL,
  `attendees` text DEFAULT NULL,
  `witnesses` text DEFAULT NULL,
  `minutes` text DEFAULT NULL,
  `outcome` varchar(500) DEFAULT NULL,
  `next_action` varchar(500) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `legal_compliance`
--

CREATE TABLE `legal_compliance` (
  `compliance_id` int(11) NOT NULL,
  `reference_code` varchar(30) NOT NULL,
  `requirement` varchar(300) NOT NULL,
  `regulatory_body` varchar(200) DEFAULT NULL,
  `category` enum('banking_regulation','data_privacy','labor_law','tax','anti_money_laundering','consumer_protection','other') NOT NULL,
  `description` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('compliant','non_compliant','in_progress','pending_review','exempted') NOT NULL DEFAULT 'pending_review',
  `risk_level` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `evidence_document_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `last_reviewed` date DEFAULT NULL,
  `next_review_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `legal_compliance`
--

INSERT INTO `legal_compliance` (`compliance_id`, `reference_code`, `requirement`, `regulatory_body`, `category`, `description`, `deadline`, `status`, `risk_level`, `evidence_document_id`, `assigned_to`, `last_reviewed`, `next_review_date`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CMP-2026-001', 'BSP Quarterly Report Submission', 'Bangko Sentral ng Pilipinas', 'banking_regulation', 'Submit quarterly microfinancial operations report to BSP', '2026-03-31', 'in_progress', 'medium', NULL, 4, NULL, '2026-06-30', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'CMP-2026-002', 'Data Privacy Impact Assessment', 'National Privacy Commission', 'data_privacy', 'Annual DPIA for microfinancial client data processing', '2026-04-30', 'pending_review', 'high', NULL, 1, NULL, '2027-04-30', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'CMP-2026-003', 'AML/CTF Compliance Annual Review', 'AMLC', 'anti_money_laundering', 'Anti-money laundering review for microfinancial transactions', '2026-06-30', 'in_progress', 'critical', NULL, 3, NULL, '2027-06-30', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'CMP-2026-004', 'BIR Tax Compliance Filing', 'Bureau of Internal Revenue', 'tax', 'Annual tax filing for microfinancial institution', '2026-04-15', 'compliant', 'medium', NULL, 4, NULL, '2027-04-15', NULL, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(5, 'CMP-2026-005', 'DOLE Labor Standards Compliance', 'DOLE', 'labor_law', 'Annual compliance with labor standards and employee benefits', '2026-05-31', 'compliant', 'low', NULL, 2, NULL, '2027-05-31', NULL, 2, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(6, 'CMP-2026-006', 'Consumer Protection Compliance Audit', 'BSP Consumer Protection Department', 'consumer_protection', 'Annual review of fair lending practices, disclosure requirements, and complaint handling procedures', '2026-07-31', 'pending_review', 'high', NULL, 3, NULL, '2027-07-31', NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(7, 'CMP-2026-007', 'SEC Annual Reportorial Requirements', 'Securities and Exchange Commission', 'banking_regulation', 'Submission of General Information Sheet (GIS), Audited Financial Statements, and Annual Report', '2026-04-30', 'in_progress', 'medium', NULL, 4, NULL, '2027-04-30', NULL, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `legal_contracts`
--

CREATE TABLE `legal_contracts` (
  `contract_id` int(11) NOT NULL,
  `contract_number` varchar(30) NOT NULL,
  `title` varchar(300) NOT NULL,
  `contract_type` enum('employment','vendor','service','nda','lease','loan','partnership','other') NOT NULL,
  `party_name` varchar(300) NOT NULL,
  `party_contact` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `value` decimal(15,2) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'PHP',
  `status` enum('draft','active','expired','terminated','renewed','under_review') NOT NULL DEFAULT 'draft',
  `auto_renew` tinyint(1) NOT NULL DEFAULT 0,
  `renewal_notice_days` int(11) DEFAULT 30,
  `document_id` int(11) DEFAULT NULL COMMENT 'Link to archived document',
  `qr_code_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `legal_contracts`
--

INSERT INTO `legal_contracts` (`contract_id`, `contract_number`, `title`, `contract_type`, `party_name`, `party_contact`, `description`, `start_date`, `end_date`, `value`, `currency`, `status`, `auto_renew`, `renewal_notice_days`, `document_id`, `qr_code_id`, `assigned_to`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CON-2026-001', 'IT Infrastructure Support Agreement', 'service', 'TechServ Solutions Inc.', NULL, 'Annual IT support and maintenance for microfinancial systems', '2026-01-01', '2026-12-31', 1200000.00, 'PHP', 'active', 0, 30, NULL, NULL, 1, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'CON-2026-002', 'Office Space Lease - Main Branch', 'lease', 'Premier Realty Corp.', NULL, 'Lease agreement for main office space', '2025-06-01', '2028-05-31', 3600000.00, 'PHP', 'active', 0, 30, NULL, NULL, 2, 2, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'CON-2026-003', 'Security Services Contract', 'service', 'SafeGuard Security Agency', NULL, 'Building security and guard services', '2026-01-01', '2027-06-30', 2400000.00, 'PHP', 'active', 0, 30, NULL, NULL, 2, 2, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'CON-2026-004', 'Employee NDA Template', 'nda', 'All Employees', NULL, 'Standard non-disclosure agreement for microfinancial staff', '2026-01-01', NULL, 0.00, 'PHP', 'active', 0, 30, NULL, NULL, 3, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(5, 'CON-2026-005', 'Loan Partnership - Rural Bank of Taguig', 'partnership', 'Rural Bank of Taguig', NULL, 'Co-lending partnership for microfinancial loan disbursement', '2026-02-01', '2027-01-31', 5000000.00, 'PHP', 'under_review', 0, 30, NULL, NULL, 3, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(6, 'CON-2026-006', 'Janitorial Services Agreement', 'service', 'CleanPro Maintenance Corp.', NULL, 'Daily janitorial and sanitation services for all branch offices', '2026-01-01', '2026-12-31', 960000.00, 'PHP', 'active', 0, 30, NULL, NULL, 2, 2, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(7, 'CON-2026-007', 'Collection Agency Agreement', 'service', 'RecoverAll Collections Inc.', NULL, 'Third-party collection services for delinquent microfinance accounts exceeding 90 days past due', '2026-02-01', '2027-01-31', 0.00, 'PHP', 'active', 0, 30, NULL, NULL, 3, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(8, 'CON-2026-008', 'Employment Contract ΓÇö Branch Manager Taguig', 'employment', 'Ricardo V. Natividad', NULL, 'Employment contract for Taguig branch manager position with performance-based incentives', '2026-03-01', '2028-02-28', 720000.00, 'PHP', 'draft', 0, 30, NULL, NULL, 2, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `legal_decision_matrix`
--

CREATE TABLE `legal_decision_matrix` (
  `matrix_id` int(11) NOT NULL,
  `case_type` varchar(100) NOT NULL,
  `severity` enum('minor','moderate','major') NOT NULL,
  `recommended_action` varchar(300) NOT NULL,
  `days_threshold` int(11) DEFAULT NULL,
  `amount_threshold` decimal(15,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `legal_decision_matrix`
--

INSERT INTO `legal_decision_matrix` (`matrix_id`, `case_type`, `severity`, `recommended_action`, `days_threshold`, `amount_threshold`, `description`, `is_active`, `created_at`) VALUES
(1, 'Loan Default', 'minor', 'Reminder Notice', 15, 0.00, '1ΓÇô15 days overdue', 1, '2026-03-03 16:16:04'),
(2, 'Loan Default', 'minor', 'Warning Notice', 30, 0.00, '16ΓÇô30 days overdue', 1, '2026-03-03 16:16:04'),
(3, 'Loan Default', 'moderate', 'Final Demand Letter', 60, 5000.00, '31ΓÇô60 days overdue, balance > Γé▒5,000', 1, '2026-03-03 16:16:04'),
(4, 'Loan Default', 'major', 'Legal Endorsement / File Case', 60, 5000.00, '60+ days overdue, balance > Γé▒5,000', 1, '2026-03-03 16:16:04'),
(5, 'Fraud', 'major', 'Termination + Court Filing', NULL, NULL, 'Immediate legal action', 1, '2026-03-03 16:16:04'),
(6, 'Theft', 'major', 'Termination + Court Filing', NULL, NULL, 'Immediate legal action', 1, '2026-03-03 16:16:04'),
(7, 'Harassment', 'moderate', 'Suspension + Investigation', NULL, NULL, 'Internal investigation', 1, '2026-03-03 16:16:04'),
(8, 'Data Breach', 'major', 'Legal Case Filed', NULL, NULL, 'Data breach incidents', 1, '2026-03-03 16:16:04'),
(9, 'Forgery', 'major', 'Termination + Court Filing', NULL, NULL, 'Forgery cases', 1, '2026-03-03 16:16:04'),
(10, 'Contract Violation', 'moderate', 'Suspension', NULL, NULL, 'Contract violation', 1, '2026-03-03 16:16:04'),
(11, 'Policy Violation', 'minor', 'Written Warning', NULL, NULL, 'First offense', 1, '2026-03-03 16:16:04'),
(12, 'Policy Violation', 'moderate', 'Suspension', NULL, NULL, 'Repeated offense', 1, '2026-03-03 16:16:04'),
(13, 'Policy Violation', 'major', 'Legal Case Filed', NULL, NULL, 'Severe violation', 1, '2026-03-03 16:16:04');

-- --------------------------------------------------------

--
-- Table structure for table `legal_escalation_notices`
--

CREATE TABLE `legal_escalation_notices` (
  `notice_id` int(11) NOT NULL,
  `case_id` int(11) DEFAULT NULL,
  `loan_doc_id` int(11) DEFAULT NULL,
  `notice_type` enum('reminder','warning','final_demand','legal_endorsement','written_warning','suspension_notice','termination_notice') NOT NULL,
  `recipient_name` varchar(300) NOT NULL,
  `recipient_dept` varchar(100) DEFAULT NULL,
  `subject` varchar(500) NOT NULL,
  `body` text DEFAULT NULL,
  `severity` enum('minor','moderate','major') DEFAULT 'minor',
  `days_overdue` int(11) DEFAULT NULL,
  `amount_involved` decimal(15,2) DEFAULT NULL,
  `auto_generated` tinyint(1) DEFAULT 0,
  `sent_date` datetime DEFAULT NULL,
  `status` enum('draft','sent','acknowledged','expired') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_documentation`
--

CREATE TABLE `loan_documentation` (
  `loan_doc_id` int(11) NOT NULL,
  `loan_doc_code` varchar(30) NOT NULL,
  `borrower_name` varchar(300) NOT NULL,
  `borrower_address` varchar(500) DEFAULT NULL,
  `loan_amount` decimal(15,2) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `loan_term_months` int(11) NOT NULL DEFAULT 12,
  `repayment_schedule` enum('monthly','quarterly','semi_annual','annual','lump_sum') NOT NULL DEFAULT 'monthly',
  `purpose` varchar(500) DEFAULT NULL,
  `contract_body` longtext DEFAULT NULL COMMENT 'Full contract text with terms & conditions',
  `attorney_name` varchar(200) DEFAULT NULL,
  `attorney_prc` varchar(50) DEFAULT NULL COMMENT 'PRC license number',
  `attorney_ptr` varchar(50) DEFAULT NULL COMMENT 'PTR number',
  `attorney_ibp` varchar(50) DEFAULT NULL COMMENT 'IBP number',
  `attorney_roll` varchar(50) DEFAULT NULL COMMENT 'Roll of Attorneys number',
  `attorney_mcle` varchar(50) DEFAULT NULL COMMENT 'MCLE compliance number',
  `attorney_signature` text DEFAULT NULL COMMENT 'Base64 encoded signature image',
  `notary_name` varchar(200) DEFAULT NULL,
  `notary_commission` varchar(100) DEFAULT NULL,
  `doc_series_no` varchar(50) DEFAULT NULL,
  `doc_page_no` varchar(50) DEFAULT NULL,
  `doc_book_no` varchar(50) DEFAULT NULL,
  `penalty_rate` decimal(5,2) DEFAULT 3.00 COMMENT 'Penalty rate per month for late payment',
  `disclosure_statement` text DEFAULT NULL COMMENT 'Truth in Lending Act disclosure',
  `promissory_note` text DEFAULT NULL COMMENT 'Promissory note text',
  `security_type` enum('unsecured','chattel_mortgage','real_estate_mortgage','pledge','guarantor') NOT NULL DEFAULT 'unsecured',
  `digital_signature_hash` varchar(64) DEFAULT NULL COMMENT 'SHA-256 hash for digital signature verification',
  `signed_date` date DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `maturity_date` date DEFAULT NULL,
  `status` enum('draft','pending_signature','signed','active','defaulted','paid','cancelled') NOT NULL DEFAULT 'draft',
  `legal_status` enum('none','under_legal','filed_in_court') DEFAULT 'none',
  `penalty_amount` decimal(15,2) DEFAULT NULL,
  `days_overdue` int(11) DEFAULT NULL,
  `document_id` int(11) DEFAULT NULL COMMENT 'Link to document management',
  `case_id` int(11) DEFAULT NULL COMMENT 'Link to legal case if defaulted',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_documentation`
--

INSERT INTO `loan_documentation` (`loan_doc_id`, `loan_doc_code`, `borrower_name`, `borrower_address`, `loan_amount`, `interest_rate`, `loan_term_months`, `repayment_schedule`, `purpose`, `contract_body`, `attorney_name`, `attorney_prc`, `attorney_ptr`, `attorney_ibp`, `attorney_roll`, `attorney_mcle`, `attorney_signature`, `notary_name`, `notary_commission`, `doc_series_no`, `doc_page_no`, `doc_book_no`, `penalty_rate`, `disclosure_statement`, `promissory_note`, `security_type`, `digital_signature_hash`, `signed_date`, `effective_date`, `maturity_date`, `status`, `legal_status`, `penalty_amount`, `days_overdue`, `document_id`, `case_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'LD-2026-001', 'Roberto A. Mendoza', '123 Rizal Avenue, Brgy. Poblacion, Makati City', 250000.00, 2.50, 12, 'monthly', 'Working Capital for Small Business', 'LOAN AGREEMENT\n\nKNOW ALL MEN BY THESE PRESENTS:\n\nThis Loan Agreement (\"Agreement\") is entered into this 10th day of January 2026, by and between:\n\nMICROFINANCIAL MANAGEMENT CORPORATION, a corporation duly organized and existing under the laws of the Republic of the Philippines, with principal office at Unit 501, Finance Tower, Ayala Avenue, Makati City, represented herein by its President, MARIA C. SANTOS (hereinafter referred to as the \"LENDER\");\n\nΓÇö and ΓÇö\n\nROBERTO A. MENDOZA, of legal age, Filipino, single, with residence at 123 Rizal Avenue, Brgy. Poblacion, Makati City (hereinafter referred to as the \"BORROWER\");\n\nWITNESSETH:\n\nWHEREAS, the BORROWER has applied for and the LENDER has agreed to extend a loan facility subject to the following terms and conditions:\n\n1. LOAN AMOUNT: The LENDER agrees to lend the amount of TWO HUNDRED FIFTY THOUSAND PESOS (Γé▒250,000.00) to the BORROWER.\n\n2. INTEREST RATE: The loan shall bear interest at the rate of 2.50% per month, computed on the diminishing balance.\n\n3. LOAN TERM: The loan shall be payable within twelve (12) months from the date of release.\n\n4. REPAYMENT SCHEDULE: The BORROWER shall pay monthly amortizations as per the attached Schedule of Payments.\n\n5. PENALTY: A penalty of 3.00% per month shall be imposed on any unpaid amount past the due date.\n\n6. SECURITY: This loan is secured by a Chattel Mortgage over the borrower\'s commercial vehicle (Toyota Hilux 2023, Plate No. ABC 1234).\n\n7. DEFAULT: The BORROWER shall be considered in default upon failure to pay two (2) consecutive monthly amortizations.\n\n8. ACCELERATION CLAUSE: Upon default, the entire outstanding balance shall become immediately due and demandable.\n\n9. VENUE: Any legal action arising from this Agreement shall be filed exclusively in the courts of Makati City.\n\nIN WITNESS WHEREOF, the parties have hereunto affixed their signatures this 10th day of January 2026 at Makati City, Philippines.\n\n[Signed]\n________________________\nMARIA C. SANTOS\nPresident, Microfinancial Management Corp.\n\n[Signed]\n________________________\nROBERTO A. MENDOZA\nBorrower', 'Atty. Francisco J. Dela Rosa', 'PRC-0045678', 'PTR No. 2026-001234 / 01-05-2026 / Makati City', 'IBP No. 123456 / Makati Chapter / 01-03-2026', 'Roll No. 67890', 'MCLE Compliance No. VII-0012345 / 04-15-2025', NULL, 'Atty. Ernesto B. Villareal', 'Notary Public for Makati City / Commission No. 2026-089 / Until Dec 31, 2027', 'Series of 2026', 'Page No. 45', 'Book No. XII', 3.00, NULL, NULL, 'chattel_mortgage', NULL, '2026-01-10', '2026-01-10', '2027-01-10', 'active', 'none', NULL, NULL, NULL, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'LD-2026-002', 'Elena G. Fernandez', '456 Mabini Street, San Juan City', 150000.00, 2.00, 6, 'monthly', 'Educational Expenses', 'PROMISSORY NOTE\n\nDate: January 25, 2026\nPlace: Makati City, Philippines\n\nFOR VALUE RECEIVED, I, ELENA G. FERNANDEZ, of legal age, Filipino, residing at 456 Mabini Street, San Juan City, hereby promise to pay MICROFINANCIAL MANAGEMENT CORPORATION, or order, the sum of ONE HUNDRED FIFTY THOUSAND PESOS (Γé▒150,000.00), Philippine Currency, on or before July 25, 2026.\n\nThis note shall bear interest at the rate of 2.00% per month from date hereof until fully paid.\n\nIn case of non-payment at maturity, I agree to pay a penalty charge of 3% per month on the outstanding balance.\n\nDemand, presentment, notice of dishonor, and protest are hereby waived.\n\nDone this 25th day of January 2026 at Makati City.\n\n[Signed]\n________________________\nELENA G. FERNANDEZ\nBorrower\n\nWITNESSES:\n\n________________________\nJUAN P. DELA CRUZ\n\n________________________\nANA M. REYES', 'Atty. Maria Teresa R. Santos', 'PRC-0098765', 'PTR No. 2026-005678 / 01-08-2026 / Makati City', 'IBP No. 654321 / QC Chapter / 01-06-2026', 'Roll No. 34567', 'MCLE Compliance No. VII-0054321 / 06-20-2025', NULL, 'Atty. Ernesto B. Villareal', 'Notary Public for Makati City / Commission No. 2026-089 / Until Dec 31, 2027', 'Series of 2026', 'Page No. 78', 'Book No. XII', 3.00, NULL, NULL, 'unsecured', NULL, '2026-01-25', '2026-01-25', '2026-07-25', 'active', 'none', NULL, NULL, NULL, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'LD-2026-003', 'ABC Corporation (by: Pedro T. Lim, President)', '789 Commercial Drive, Taguig City', 2500000.00, 1.75, 24, 'monthly', 'Business Expansion - New Branch', 'LOAN AND MORTGAGE AGREEMENT\n\nThis Agreement made and executed on February 1, 2026 at Makati City by and between:\n\nMICROFINANCIAL MANAGEMENT CORPORATION (\"LENDER\")\nrepresented by MARIA C. SANTOS, President\n\nΓÇö and ΓÇö\n\nABC CORPORATION (\"BORROWER\")\nrepresented by PEDRO T. LIM, President\n\nTERMS:\n1. Loan Amount: Γé▒2,500,000.00\n2. Interest: 1.75% per month, diminishing balance\n3. Term: 24 months\n4. Security: Real Estate Mortgage over TCT No. T-654321\n5. Purpose: Business expansion and new branch establishment\n\nSPECIAL COVENANTS:\na) Borrower shall maintain insurance on the mortgaged property\nb) Borrower shall not sell, assign, or encumber the mortgaged property\nc) Borrower shall allow inspection of the mortgaged property\n\n[Signed by both parties]', 'Atty. Francisco J. Dela Rosa', 'PRC-0045678', 'PTR No. 2026-001234 / 01-05-2026 / Makati City', 'IBP No. 123456 / Makati Chapter / 01-03-2026', 'Roll No. 67890', 'MCLE Compliance No. VII-0012345 / 04-15-2025', NULL, 'Atty. Ernesto B. Villareal', 'Notary Public for Makati City / Commission No. 2026-089 / Until Dec 31, 2027', 'Series of 2026', 'Page No. 112', 'Book No. XII', 3.00, NULL, NULL, 'real_estate_mortgage', NULL, '2026-02-01', '2026-02-01', '2028-02-01', 'active', 'none', NULL, NULL, NULL, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'LD-2026-004', 'Grace S. Aquino', '321 Luna Street, Quezon City', 50000.00, 3.00, 6, 'monthly', 'Emergency Medical Expenses', 'DISCLOSURE STATEMENT\n(Required under R.A. 3765 ΓÇö Truth in Lending Act)\n\nDate: February 10, 2026\n\n1. Name of Creditor: Microfinancial Management Corporation\n2. Name of Borrower: Grace S. Aquino\n3. Address: 321 Luna Street, Quezon City\n\n4. Principal Loan: Γé▒50,000.00\n5. Net Proceeds: Γé▒48,500.00 (after documentary stamps & service charge)\n6. Monthly Interest Rate: 3.00%\n7. Total Interest: Γé▒9,000.00\n8. Penalties (if applicable): 3.00% per month on overdue amount\n9. Total Amount to be Paid: Γé▒59,000.00\n10. Monthly Amortization: Γé▒9,833.33\n\nI hereby acknowledge receipt of this Disclosure Statement and a copy of the Promissory Note.\n\n[Signed]\n________________________\nGRACE S. AQUINO\nBorrower', 'Atty. Maria Teresa R. Santos', 'PRC-0098765', 'PTR No. 2026-005678 / 01-08-2026 / Makati City', 'IBP No. 654321 / QC Chapter / 01-06-2026', 'Roll No. 34567', 'MCLE Compliance No. VII-0054321 / 06-20-2025', NULL, NULL, NULL, NULL, NULL, NULL, 3.00, NULL, NULL, 'unsecured', NULL, '2026-02-10', '2026-02-10', '2026-08-10', 'active', 'none', NULL, NULL, NULL, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(5, 'LD-2026-005', 'Michael B. Tan', '567 Bonifacio Avenue, Taguig City', 500000.00, 2.25, 18, 'monthly', 'Equipment Purchase', 'CHATTEL MORTGAGE AGREEMENT\n\nThis instrument executed on February 5, 2026 by MICHAEL B. TAN (\"MORTGAGOR\") in favor of MICROFINANCIAL MANAGEMENT CORPORATION (\"MORTGAGEE\").\n\nThe Mortgagor hereby mortgages the following personal property:\n- Brand New Industrial Printing Equipment\n- Model: HP Indigo 7900 Digital Press\n- Serial No.: IND-2026-HP-001\n- Location: 567 Bonifacio Avenue, Taguig City\n\nAppraised Value: Γé▒800,000.00\n\nThis mortgage secures the payment of Γé▒500,000.00 loan.\n\n[Signed by Mortgagor]', 'Atty. Francisco J. Dela Rosa', 'PRC-0045678', 'PTR No. 2026-001234 / 01-05-2026 / Makati City', 'IBP No. 123456 / Makati Chapter / 01-03-2026', 'Roll No. 67890', 'MCLE Compliance No. VII-0012345 / 04-15-2025', NULL, 'Atty. Ernesto B. Villareal', 'Notary Public for Makati City / Commission No. 2026-089 / Until Dec 31, 2027', 'Series of 2026', 'Page No. 95', 'Book No. XII', 3.00, NULL, NULL, 'chattel_mortgage', NULL, '2026-02-05', '2026-02-05', '2027-08-05', 'defaulted', 'none', NULL, NULL, NULL, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(6, 'LD-2026-006', 'Rosario M. Villanueva', '89 Sampaguita Street, Brgy. Holy Spirit, Quezon City', 180000.00, 2.00, 12, 'monthly', 'Micro-Enterprise ΓÇö Food Cart Business', 'LOAN AGREEMENT\n\nThis Agreement is entered into this 5th day of February 2026.\n\nBETWEEN:\nMICROFINANCIAL MANAGEMENT CORPORATION (\"LENDER\")\n\nAND:\nROSARIO M. VILLANUEVA (\"BORROWER\")\n\nTERMS:\n1. Loan Amount: Γé▒180,000.00\n2. Interest Rate: 2.00% per month (diminishing balance)\n3. Term: 12 months\n4. Repayment: Monthly amortization\n5. Purpose: Food cart business setup in QC area\n6. Security: Guarantor (spouse ΓÇö Eduardo P. Villanueva)\n\nThe BORROWER agrees to all terms and conditions set forth herein.\n\n[Signed by both parties]', 'Atty. Maria Teresa R. Santos', 'PRC-0098765', 'PTR No. 2026-005678 / 01-08-2026 / Makati City', 'IBP No. 654321 / QC Chapter / 01-06-2026', 'Roll No. 34567', 'MCLE Compliance No. VII-0054321 / 06-20-2025', NULL, 'Atty. Ernesto B. Villareal', 'Notary Public for Makati City / Commission No. 2026-089 / Until Dec 31, 2027', 'Series of 2026', 'Page No. 130', 'Book No. XIII', 3.00, NULL, NULL, 'guarantor', NULL, '2026-02-05', '2026-02-05', '2027-02-05', 'active', 'none', NULL, NULL, NULL, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(7, 'LD-2026-007', 'Fernando C. Aguilar', '45 Mahogany Lane, BF Homes, Para├▒aque City', 750000.00, 1.50, 24, 'monthly', 'Agricultural Supply Store Expansion', 'LOAN AND REAL ESTATE MORTGAGE AGREEMENT\n\nExecuted on February 12, 2026.\n\nMICROFINANCIAL MANAGEMENT CORPORATION (\"LENDER\")\nvs.\nFERNANDO C. AGUILAR (\"BORROWER\")\n\nThe LENDER grants a loan of Γé▒750,000.00 secured by Real Estate Mortgage over TCT No. T-112233, a residential lot located at BF Homes, Para├▒aque City.\n\nInterest: 1.50% monthly, diminishing balance\nTerm: 24 months\nPenalty: 3.00% per month on overdue amounts\n\nSPECIAL CONDITIONS:\n- Property insurance shall be maintained by the Borrower\n- No further encumbrance without written consent of Lender\n\n[Signed by both parties]', 'Atty. Francisco J. Dela Rosa', 'PRC-0045678', 'PTR No. 2026-001234 / 01-05-2026 / Makati City', 'IBP No. 123456 / Makati Chapter / 01-03-2026', 'Roll No. 67890', 'MCLE Compliance No. VII-0012345 / 04-15-2025', NULL, 'Atty. Ernesto B. Villareal', 'Notary Public for Makati City / Commission No. 2026-089 / Until Dec 31, 2027', 'Series of 2026', 'Page No. 145', 'Book No. XIII', 3.00, NULL, NULL, 'real_estate_mortgage', NULL, '2026-02-12', '2026-02-12', '2028-02-12', 'pending_signature', 'none', NULL, NULL, NULL, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(8, 'LD-2026-008', 'Carmen L. Soriano', '12 Acacia Road, Brgy. San Antonio, Pasig City', 100000.00, 2.75, 6, 'monthly', 'Emergency Home Repair After Typhoon', 'PROMISSORY NOTE\n\nDate: February 14, 2026\nPlace: Makati City\n\nI, CARMEN L. SORIANO, promise to pay MICROFINANCIAL MANAGEMENT CORPORATION the sum of ONE HUNDRED THOUSAND PESOS (Γé▒100,000.00) within six (6) months.\n\nInterest: 2.75% per month\nPenalty for late payment: 3.00% per month\n\nThis loan is UNSECURED and extended on the basis of the borrower\'s credit standing and employment record.\n\n[Signed]\nCARMEN L. SORIANO', 'Atty. Maria Teresa R. Santos', 'PRC-0098765', 'PTR No. 2026-005678 / 01-08-2026 / Makati City', 'IBP No. 654321 / QC Chapter / 01-06-2026', 'Roll No. 34567', 'MCLE Compliance No. VII-0054321 / 06-20-2025', NULL, NULL, NULL, NULL, NULL, NULL, 3.00, NULL, NULL, 'unsecured', NULL, '2026-02-14', '2026-02-14', '2026-08-14', 'signed', 'none', NULL, NULL, NULL, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module` enum('facilities','documents','legal','visitors','departments','system') NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `module`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'facilities', 'New Reservation Request', 'Training Hall reservation pending for AML Compliance Training on Feb 17.', '/modules/facilities/', 0, '2026-02-24 09:40:58'),
(2, 1, 'legal', 'Compliance Deadline Approaching', 'BSP Quarterly Report due by March 31, 2026. Current status: In Progress.', '/modules/legal/', 0, '2026-02-24 09:40:58'),
(3, 1, 'visitors', 'Pre-registration Pending Approval', 'BSP representative Mark Villanueva pre-registered for Feb 20 visit. Needs approval.', '/modules/visitors/', 0, '2026-02-24 09:40:58'),
(4, 2, 'documents', 'Document OCR Completed', 'Board Resolution No. 2026-001 has been queued for OCR processing.', '/modules/documents/', 0, '2026-02-24 09:40:58'),
(5, 3, 'legal', 'Case Update Required', 'Loan Default Recovery case LC-2026-001 hearing scheduled. Update case status.', '/modules/legal/', 0, '2026-02-24 09:40:58'),
(6, 4, 'visitors', 'Visitor Arriving Tomorrow', 'Elena Fernandez pre-registered for loan inquiry on Feb 16 at 10:00 AM.', '/modules/visitors/', 0, '2026-02-24 09:40:58'),
(7, 1, 'facilities', 'New Reservation Request', 'Training Hall reservation pending for AML Compliance Training on Feb 17.', '/modules/facilities/', 0, '2026-02-24 09:53:06'),
(8, 1, 'legal', 'Compliance Deadline Approaching', 'BSP Quarterly Report due by March 31, 2026. Current status: In Progress.', '/modules/legal/', 0, '2026-02-24 09:53:06'),
(9, 1, 'visitors', 'Pre-registration Pending Approval', 'BSP representative Mark Villanueva pre-registered for Feb 20 visit. Needs approval.', '/modules/visitors/', 0, '2026-02-24 09:53:06'),
(10, 2, 'documents', 'Document OCR Completed', 'Board Resolution No. 2026-001 has been queued for OCR processing.', '/modules/documents/', 0, '2026-02-24 09:53:06'),
(11, 3, 'legal', 'Case Update Required', 'Loan Default Recovery case LC-2026-001 hearing scheduled. Update case status.', '/modules/legal/', 0, '2026-02-24 09:53:06'),
(12, 4, 'visitors', 'Visitor Arriving Tomorrow', 'Elena Fernandez pre-registered for loan inquiry on Feb 16 at 10:00 AM.', '/modules/visitors/', 0, '2026-02-24 09:53:06'),
(13, 2, 'documents', 'Document OCR Completed', 'Board Resolution No. 2026-001 has been queued for OCR processing.', '/modules/documents/', 0, '2026-02-24 09:53:20'),
(14, 1, 'facilities', 'New Reservation Request', 'Training Hall reservation pending for AML Compliance Training on Feb 17.', '/modules/facilities/', 0, '2026-02-24 09:54:05'),
(15, 1, 'legal', 'Compliance Deadline Approaching', 'BSP Quarterly Report due by March 31, 2026. Current status: In Progress.', '/modules/legal/', 1, '2026-02-24 09:54:05'),
(16, 1, 'visitors', 'Pre-registration Pending Approval', 'BSP representative Mark Villanueva pre-registered for Feb 20 visit. Needs approval.', '/modules/visitors/', 1, '2026-02-24 09:54:05'),
(17, 2, 'documents', 'Document OCR Completed', 'Board Resolution No. 2026-001 has been queued for OCR processing.', '/modules/documents/', 0, '2026-02-24 09:54:05'),
(18, 3, 'legal', 'Case Update Required', 'Loan Default Recovery case LC-2026-001 hearing scheduled. Update case status.', '/modules/legal/', 0, '2026-02-24 09:54:05'),
(19, 4, 'visitors', 'Visitor Arriving Tomorrow', 'Elena Fernandez pre-registered for loan inquiry on Feb 16 at 10:00 AM.', '/modules/visitors/', 0, '2026-02-24 09:54:05');

-- --------------------------------------------------------

--
-- Table structure for table `ocr_queue`
--

CREATE TABLE `ocr_queue` (
  `queue_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `status` enum('queued','processing','completed','failed') NOT NULL DEFAULT 'queued',
  `attempts` int(11) NOT NULL DEFAULT 0,
  `error_message` text DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permits_licenses`
--

CREATE TABLE `permits_licenses` (
  `permit_id` int(11) NOT NULL,
  `permit_code` varchar(30) NOT NULL,
  `permit_name` varchar(300) NOT NULL,
  `issuing_body` varchar(200) NOT NULL,
  `permit_type` enum('business_permit','financial_license','sec_registration','cda_registration','bsp_license','fire_safety','occupancy','sanitary','other') NOT NULL,
  `permit_number` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `renewal_fee` decimal(12,2) DEFAULT NULL,
  `status` enum('active','expired','pending_renewal','suspended','revoked') NOT NULL DEFAULT 'active',
  `document_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permits_licenses`
--

INSERT INTO `permits_licenses` (`permit_id`, `permit_code`, `permit_name`, `issuing_body`, `permit_type`, `permit_number`, `issue_date`, `expiry_date`, `renewal_fee`, `status`, `document_id`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PL-2026-001', 'BSP Certificate of Authority', 'Bangko Sentral ng Pilipinas', 'bsp_license', 'BSP-CA-2020-1234', '2020-03-15', '2026-03-15', 25000.00, 'pending_renewal', NULL, 'Renewal application submitted Feb 2026', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'PL-2026-002', 'SEC Registration Certificate', 'Securities and Exchange Commission', 'sec_registration', 'SEC-REG-CS201912345', '2019-06-01', NULL, 0.00, 'active', NULL, 'Perpetual unless revoked', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'PL-2026-003', 'CDA Certificate of Registration', 'Cooperative Development Authority', 'cda_registration', 'CDA-REG-2020-5678', '2020-01-10', NULL, 0.00, 'active', NULL, 'Applicable if operating as cooperative', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'PL-2026-004', 'Business Permit ΓÇö Makati City', 'City of Makati ΓÇö Business Permits & Licensing Office', 'business_permit', 'BP-MKT-2026-09876', '2026-01-15', '2026-12-31', 45000.00, 'active', NULL, 'Renewed January 2026', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(5, 'PL-2026-005', 'Fire Safety Inspection Certificate', 'Bureau of Fire Protection ΓÇö Makati', 'fire_safety', 'FSIC-MKT-2026-0456', '2026-02-01', '2027-01-31', 5000.00, 'active', NULL, 'Annual inspection completed', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(6, 'PL-2026-006', 'NPC Registration Certificate', 'National Privacy Commission', 'other', 'NPC-REG-PIC-2021-0789', '2021-05-20', NULL, 0.00, 'active', NULL, 'Data Processing System registration', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(7, 'PL-2026-007', 'Occupancy Permit ΓÇö Taguig Branch', 'City of Taguig ΓÇö Building Official', 'occupancy', 'OP-TGG-2026-01234', '2026-02-01', '2031-01-31', 15000.00, 'active', NULL, 'Issued for new Taguig branch office space, 2nd Floor Unit 201-202, BGC Corporate Center', 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `power_of_attorney`
--

CREATE TABLE `power_of_attorney` (
  `poa_id` int(11) NOT NULL,
  `poa_code` varchar(30) NOT NULL,
  `principal_name` varchar(300) NOT NULL COMMENT 'Person granting authority',
  `principal_position` varchar(200) DEFAULT NULL,
  `agent_name` varchar(300) NOT NULL COMMENT 'Person receiving authority',
  `agent_position` varchar(200) DEFAULT NULL,
  `poa_type` enum('general','special','limited','durable') NOT NULL DEFAULT 'special',
  `scope` text NOT NULL COMMENT 'Scope of authority granted',
  `effective_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `notarized` tinyint(1) NOT NULL DEFAULT 0,
  `notary_name` varchar(200) DEFAULT NULL,
  `notary_date` date DEFAULT NULL,
  `resolution_id` int(11) DEFAULT NULL COMMENT 'Board resolution authorizing this POA',
  `document_id` int(11) DEFAULT NULL,
  `status` enum('active','expired','revoked','superseded') NOT NULL DEFAULT 'active',
  `revoked_date` date DEFAULT NULL,
  `revoked_reason` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `power_of_attorney`
--

INSERT INTO `power_of_attorney` (`poa_id`, `poa_code`, `principal_name`, `principal_position`, `agent_name`, `agent_position`, `poa_type`, `scope`, `effective_date`, `expiry_date`, `notarized`, `notary_name`, `notary_date`, `resolution_id`, `document_id`, `status`, `revoked_date`, `revoked_reason`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'POA-2026-001', 'Maria C. Santos', 'President & CEO', 'Pedro T. Reyes', 'VP - Operations', 'special', 'Authority to sign loan agreements, promissory notes, and disclosure statements on behalf of Microfinancial Management Corporation for loan amounts not exceeding Γé▒1,000,000.00', '2026-01-15', '2026-12-31', 1, 'Atty. Ernesto B. Villareal', '2026-01-15', 1, NULL, 'active', NULL, NULL, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'POA-2026-002', 'Maria C. Santos', 'President & CEO', 'Ana M. Garcia', 'VP - Finance', 'limited', 'Authority to represent Microfinancial Management Corporation in BSP quarterly reporting and compliance submissions for the year 2026', '2026-01-15', '2026-12-31', 1, 'Atty. Ernesto B. Villareal', '2026-01-15', NULL, NULL, 'active', NULL, NULL, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'POA-2026-003', 'Board of Directors', 'Governing Body', 'Atty. Francisco J. Dela Rosa', 'External Legal Counsel', 'special', 'Authority to institute legal action, file cases, and execute settlement agreements on behalf of the corporation for loan recovery cases', '2026-02-01', '2027-01-31', 1, 'Atty. Ernesto B. Villareal', '2026-02-01', 1, NULL, 'active', NULL, NULL, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `qr_codes`
--

CREATE TABLE `qr_codes` (
  `qr_id` int(11) NOT NULL,
  `qr_uuid` char(36) NOT NULL,
  `module` enum('facilities','documents','legal','visitors','departments','system') NOT NULL,
  `reference_table` varchar(100) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `qr_data` text NOT NULL,
  `qr_image_path` varchar(500) DEFAULT NULL,
  `scan_count` int(11) NOT NULL DEFAULT 0,
  `last_scanned` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservation_equipment`
--

CREATE TABLE `reservation_equipment` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','manager','head_department','staff') NOT NULL DEFAULT 'staff',
  `department` varchar(100) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `employee_id`, `first_name`, `last_name`, `email`, `password_hash`, `role`, `department`, `avatar_url`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Jessel', 'Obina', 'imjesselobina@gmail.com', '$2y$10$Av.MvbMhQRHX0hY0Zq111.BcVeGFZe.Z5K8XXfPJ/JJ8Lrca4SBDG', 'super_admin', 'IT Department', NULL, 1, '2026-03-06 23:37:38', '2026-02-24 10:03:01', '2026-03-06 15:37:38'),
(2, 'jeanmarc', 'Jean Marc', 'Aguilar', 'jeanmarcaguilar829@gmail.com', '$2y$10$JT21XwcwiqjGi.04ZPui0uK2FuL4vblwRGhmYZ4hYkzbYcRrM2.62', 'admin', 'Administration', NULL, 1, '2026-03-06 20:06:43', '2026-02-24 10:03:01', '2026-03-06 12:06:43'),
(3, 'johnmark', 'John Mark', 'Pacunio', 'johnmarkpacunio26@gmail.com', '$2y$10$ITMKAlMakIxdvZBGXm1moOakXbb.5ypoCFxOfkCND6267AhcCuFbK', 'admin', 'Administration', NULL, 1, '2026-03-06 21:22:35', '2026-02-24 10:03:01', '2026-03-06 13:22:35'),
(16, 'HD-HR1', 'Patricia', 'Mendoza', 'patricia.mendoza@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'HR 1', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57'),
(17, 'HD-HR2', 'Roberto', 'Villanueva', 'roberto.villanueva@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'HR 2', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57'),
(18, 'HD-HR3', 'Elena', 'Gutierrez', 'elena.gutierrez@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'HR 3', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57'),
(19, 'HD-HR4', 'Miguel', 'Fernandez', 'miguel.fernandez@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'HR 4', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57'),
(20, 'HD-CR1', 'Isabel', 'Castillo', 'isabel.castillo@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'Core 1', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57'),
(21, 'HD-CR2', 'Ricardo', 'Navarro', 'ricardo.navarro@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'Core 2', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57'),
(22, 'HD-LG1', 'Carmen', 'Ramos', 'carmen.ramos@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'Log 1', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57'),
(23, 'HD-LG2', 'Fernando', 'Diaz', 'fernando.diaz@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'Log 2', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57'),
(24, 'HD-FIN', 'Lucia', 'Torres', 'lucia.torres@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'Financial', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57'),
(25, 'HD-ADM', 'Antonio', 'Morales', 'antonio.morales@microfinancial.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'head_department', 'Administrative', NULL, 1, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `visitor_id` int(11) NOT NULL,
  `visitor_code` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `company` varchar(200) DEFAULT NULL,
  `visitor_type` enum('regular','vip','contractor','government_official') NOT NULL DEFAULT 'regular',
  `id_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `photo_url` varchar(500) DEFAULT NULL,
  `is_blacklisted` tinyint(1) NOT NULL DEFAULT 0,
  `blacklist_reason` text DEFAULT NULL,
  `visit_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`visitor_id`, `visitor_code`, `first_name`, `last_name`, `email`, `phone`, `company`, `visitor_type`, `id_type`, `id_number`, `photo_url`, `is_blacklisted`, `blacklist_reason`, `visit_count`, `created_at`, `updated_at`) VALUES
(1, 'VIS-001', 'Roberto', 'Mendoza', 'r.mendoza@abccorp.com', '09171234567', 'ABC Corporation', 'regular', 'government_id', 'PSA-2020-12345', NULL, 0, NULL, 3, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'VIS-002', 'Patricia', 'Lim', 'p.lim@techserv.com', '09182345678', 'TechServ Solutions Inc.', 'regular', 'company_id', 'TS-2024-089', NULL, 0, NULL, 5, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'VIS-003', 'Michael', 'Tan', 'm.tan@ruralbanktaguig.com', '09193456789', 'Rural Bank of Taguig', 'regular', 'company_id', 'RBT-2025-112', NULL, 0, NULL, 2, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(4, 'VIS-004', 'Elena', 'Fernandez', 'e.fernandez@gmail.com', '09204567890', NULL, 'regular', 'drivers_license', 'DL-N01-234567', NULL, 0, NULL, 1, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(5, 'VIS-005', 'David', 'Cruz', 'd.cruz@safeguard.com', '09215678901', 'SafeGuard Security Agency', 'regular', 'company_id', 'SG-2025-045', NULL, 0, NULL, 8, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(16, 'VIS-839357', 'Grace', 'Aquino', 'g.aquino@npc.gov.ph', '09237890123', 'National Privacy Commission', 'regular', NULL, NULL, NULL, 0, NULL, 1, '2026-02-24 10:27:13', '2026-02-24 10:38:34'),
(17, 'VIS-755805', 'Mark', 'Villanueva', 'm.villanueva@bsp.gov.ph', '09226789012', 'Bangko Sentral ng Pilipinas', 'regular', NULL, NULL, NULL, 0, NULL, 1, '2026-02-26 17:48:55', '2026-03-03 16:27:11'),
(18, 'VIS-239662', 'jessel', 'obina', 'imjesselobina@gmail.com', '0923498734', 'BCP FINANCE', 'regular', 'school_id', '22121890', NULL, 0, NULL, 2, '2026-03-02 17:01:35', '2026-03-03 16:25:33');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_daily_summary`
--

CREATE TABLE `visitor_daily_summary` (
  `summary_id` int(11) NOT NULL,
  `summary_date` date NOT NULL,
  `total_visitors` int(11) NOT NULL DEFAULT 0,
  `total_check_ins` int(11) NOT NULL DEFAULT 0,
  `total_check_outs` int(11) NOT NULL DEFAULT 0,
  `total_no_shows` int(11) NOT NULL DEFAULT 0,
  `peak_hour` tinyint(4) DEFAULT NULL,
  `avg_visit_duration_min` decimal(6,1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitor_logs`
--

CREATE TABLE `visitor_logs` (
  `log_id` int(11) NOT NULL,
  `visit_code` varchar(20) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `host_user_id` int(11) DEFAULT NULL,
  `host_name` varchar(200) DEFAULT NULL,
  `host_department` varchar(100) DEFAULT NULL,
  `purpose` enum('meeting','delivery','interview','inspection','consultation','maintenance','other') NOT NULL,
  `purpose_details` text DEFAULT NULL,
  `visitor_type` enum('regular','vip','contractor','government_official') NOT NULL DEFAULT 'regular',
  `security_level` enum('standard','elevated','high','executive') NOT NULL DEFAULT 'standard',
  `escort_required` tinyint(1) NOT NULL DEFAULT 0,
  `id_verified` tinyint(1) NOT NULL DEFAULT 0,
  `access_level` enum('lobby_only','general','executive_floor','all_access') NOT NULL DEFAULT 'general',
  `facility_id` int(11) DEFAULT NULL COMMENT 'Links to facility reservation if applicable',
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `qr_code_id` int(11) DEFAULT NULL,
  `badge_number` varchar(20) DEFAULT NULL,
  `items_brought` text DEFAULT NULL,
  `vehicle_plate` varchar(20) DEFAULT NULL,
  `status` enum('pre_registered','checked_in','checked_out','cancelled','no_show') NOT NULL DEFAULT 'pre_registered',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor_logs`
--

INSERT INTO `visitor_logs` (`log_id`, `visit_code`, `visitor_id`, `host_user_id`, `host_name`, `host_department`, `purpose`, `purpose_details`, `visitor_type`, `security_level`, `escort_required`, `id_verified`, `access_level`, `facility_id`, `check_in_time`, `check_out_time`, `qr_code_id`, `badge_number`, `items_brought`, `vehicle_plate`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'VL-2026-0001', 1, 3, 'Juan Dela Cruz', 'Legal Department', 'meeting', 'Discussion on loan default case LC-2026-001', 'regular', 'standard', 0, 0, 'general', NULL, '2026-02-15 09:30:00', '2026-02-15 11:45:00', NULL, 'B-001', NULL, NULL, 'checked_out', NULL, NULL, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'VL-2026-0002', 2, 1, 'System Administrator', 'IT Department', 'maintenance', 'Server maintenance and software updates', 'regular', 'standard', 0, 0, 'general', NULL, '2026-02-15 08:00:00', '2026-02-15 17:00:00', NULL, 'B-002', NULL, NULL, 'checked_out', NULL, NULL, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(3, 'VL-2026-0003', 3, 3, 'Juan Dela Cruz', 'Legal Department', 'consultation', 'Partnership agreement review discussion', 'regular', 'standard', 0, 0, 'general', NULL, '2026-02-15 14:00:00', '2026-02-24 18:38:31', NULL, 'B-003', NULL, NULL, 'checked_out', NULL, NULL, '2026-02-24 09:40:58', '2026-02-24 10:38:31'),
(4, 'VL-2026-0004', 4, 4, 'Ana Reyes', 'Finance', 'consultation', 'Personal loan application inquiry', 'regular', 'standard', 0, 0, 'general', NULL, '2026-02-16 10:00:00', NULL, NULL, NULL, NULL, NULL, 'pre_registered', NULL, NULL, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(5, 'VL-2026-0005', 5, 2, 'Maria Santos', 'Administration', 'inspection', 'Monthly security inspection rounds', 'regular', 'standard', 0, 0, 'general', NULL, '2026-02-15 07:00:00', '2026-02-15 08:30:00', NULL, 'B-004', NULL, NULL, 'checked_out', NULL, NULL, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(16, 'VL-2026-634834', 16, NULL, NULL, 'Executive Office', 'meeting', NULL, 'regular', 'standard', 0, 0, 'general', NULL, '2026-02-24 18:38:34', '2026-02-24 18:38:37', NULL, NULL, NULL, NULL, 'checked_out', NULL, 1, '2026-02-24 10:38:34', '2026-02-24 10:38:37'),
(17, 'VL-2026-510447', 18, NULL, 'Alex', 'IT Department', '', '[Educational] Institution: BCP FINANCE | Type: ojt | Notes: hahaha', 'regular', 'standard', 0, 0, 'general', NULL, '2026-03-03 01:56:02', '2026-03-04 00:24:36', NULL, 'B-002', NULL, NULL, 'checked_out', NULL, 1, '2026-03-02 17:56:02', '2026-03-03 16:24:36'),
(18, 'VL-2026-996461', 18, NULL, NULL, NULL, '', NULL, 'regular', 'standard', 0, 0, 'general', NULL, '2026-03-04 00:25:33', '2026-03-04 00:25:52', NULL, NULL, NULL, NULL, 'checked_out', NULL, 1, '2026-03-03 16:25:33', '2026-03-03 16:25:52'),
(19, 'VL-2026-506476', 17, NULL, NULL, 'Executive Office', 'meeting', NULL, 'regular', 'standard', 0, 0, 'lobby_only', NULL, '2026-03-04 00:27:11', '2026-03-04 02:30:11', NULL, NULL, NULL, NULL, 'checked_out', NULL, 1, '2026-03-03 16:27:11', '2026-03-03 18:30:11');

-- --------------------------------------------------------

--
-- Table structure for table `visitor_preregistrations`
--

CREATE TABLE `visitor_preregistrations` (
  `prereg_id` int(11) NOT NULL,
  `prereg_code` varchar(20) NOT NULL,
  `visitor_code` varchar(30) DEFAULT NULL,
  `visitor_name` varchar(200) NOT NULL,
  `visitor_email` varchar(150) DEFAULT NULL,
  `visitor_phone` varchar(20) DEFAULT NULL,
  `visitor_company` varchar(200) DEFAULT NULL,
  `visitor_type` enum('regular','vip','contractor','government_official') NOT NULL DEFAULT 'regular',
  `security_level` enum('standard','elevated','high','executive') NOT NULL DEFAULT 'standard',
  `parking_required` tinyint(1) NOT NULL DEFAULT 0,
  `escort_required` tinyint(1) NOT NULL DEFAULT 0,
  `id_type` varchar(50) DEFAULT NULL,
  `id_number` varchar(100) DEFAULT NULL,
  `host_user_id` int(11) NOT NULL,
  `purpose` varchar(300) NOT NULL,
  `expected_date` date NOT NULL,
  `expected_time` time DEFAULT NULL,
  `qr_code_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','checked_in','expired','cancelled') NOT NULL DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `visitor_id` int(11) DEFAULT NULL COMMENT 'Linked after check-in',
  `visit_log_id` int(11) DEFAULT NULL COMMENT 'Linked after check-in',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitor_preregistrations`
--

INSERT INTO `visitor_preregistrations` (`prereg_id`, `prereg_code`, `visitor_code`, `visitor_name`, `visitor_email`, `visitor_phone`, `visitor_company`, `visitor_type`, `security_level`, `parking_required`, `escort_required`, `id_type`, `id_number`, `host_user_id`, `purpose`, `expected_date`, `expected_time`, `qr_code_id`, `status`, `approved_by`, `visitor_id`, `visit_log_id`, `created_at`, `updated_at`) VALUES
(1, 'PR-2026-001', NULL, 'Elena Fernandez', 'e.fernandez@gmail.com', '09204567890', NULL, 'regular', 'standard', 0, 0, NULL, NULL, 4, 'Loan application inquiry', '2026-02-16', '10:00:00', NULL, 'approved', NULL, NULL, NULL, '2026-02-24 09:40:58', '2026-02-24 09:40:58'),
(2, 'PR-2026-002', NULL, 'Mark Villanueva', 'm.villanueva@bsp.gov.ph', '09226789012', 'Bangko Sentral ng Pilipinas', 'regular', 'standard', 0, 0, NULL, NULL, 1, 'BSP Audit preliminary visit', '2026-02-20', '09:00:00', NULL, 'approved', NULL, NULL, NULL, '2026-02-24 09:40:58', '2026-02-26 17:48:55'),
(3, 'PR-2026-003', NULL, 'Grace Aquino', 'g.aquino@npc.gov.ph', '09237890123', 'National Privacy Commission', 'regular', 'standard', 0, 0, NULL, NULL, 3, 'Data privacy compliance check', '2026-02-22', '13:00:00', NULL, 'approved', NULL, NULL, NULL, '2026-02-24 09:40:58', '2026-02-24 10:27:13'),
(8, 'PR-2026-291867', NULL, 'jessel obina', 'imjesselobina@gmail.com', '0923498734', 'BCP FINANCE', 'regular', 'standard', 0, 0, 'school_id', '22121890', 2, 'educational', '2026-03-03', '11:01:00', NULL, 'approved', NULL, NULL, NULL, '2026-03-02 17:01:31', '2026-03-02 17:01:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `board_resolutions`
--
ALTER TABLE `board_resolutions`
  ADD PRIMARY KEY (`resolution_id`),
  ADD UNIQUE KEY `resolution_code` (`resolution_code`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_type` (`resolution_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `collateral_registry`
--
ALTER TABLE `collateral_registry`
  ADD PRIMARY KEY (`collateral_id`),
  ADD UNIQUE KEY `collateral_code` (`collateral_code`),
  ADD KEY `loan_doc_id` (`loan_doc_id`),
  ADD KEY `release_authorized_by` (`release_authorized_by`),
  ADD KEY `foreclosure_case_id` (`foreclosure_case_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_lien_status` (`lien_status`),
  ADD KEY `idx_type` (`collateral_type`);

--
-- Indexes for table `department_folders`
--
ALTER TABLE `department_folders`
  ADD PRIMARY KEY (`folder_id`),
  ADD UNIQUE KEY `dept_unique` (`department`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `demand_letters`
--
ALTER TABLE `demand_letters`
  ADD PRIMARY KEY (`demand_id`),
  ADD UNIQUE KEY `demand_code` (`demand_code`),
  ADD KEY `loan_doc_id` (`loan_doc_id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`demand_type`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`document_id`),
  ADD UNIQUE KEY `document_code` (`document_code`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `qr_code_id` (`qr_code_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_confidentiality` (`confidentiality`),
  ADD KEY `idx_archived_at` (`archived_at`),
  ADD KEY `idx_retained_at` (`retained_at`);
ALTER TABLE `documents` ADD FULLTEXT KEY `idx_fulltext` (`title`,`description`);

--
-- Indexes for table `document_access`
--
ALTER TABLE `document_access`
  ADD PRIMARY KEY (`access_id`),
  ADD UNIQUE KEY `uk_doc_user` (`document_id`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `granted_by` (`granted_by`),
  ADD KEY `idx_access_expires` (`expires_at`);

--
-- Indexes for table `document_access_requests`
--
ALTER TABLE `document_access_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_doc_id` (`document_id`),
  ADD KEY `idx_requested_by` (`requested_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `document_categories`
--
ALTER TABLE `document_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD PRIMARY KEY (`version_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `document_view_logs`
--
ALTER TABLE `document_view_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_doc_id` (`document_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`facility_id`),
  ADD UNIQUE KEY `facility_code` (`facility_code`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `facility_equipment`
--
ALTER TABLE `facility_equipment`
  ADD PRIMARY KEY (`equipment_id`),
  ADD UNIQUE KEY `equipment_code` (`equipment_code`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `facility_maintenance`
--
ALTER TABLE `facility_maintenance`
  ADD PRIMARY KEY (`maintenance_id`),
  ADD UNIQUE KEY `ticket_number` (`ticket_number`),
  ADD KEY `facility_id` (`facility_id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `reported_by` (`reported_by`);

--
-- Indexes for table `facility_reservations`
--
ALTER TABLE `facility_reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD UNIQUE KEY `reservation_code` (`reservation_code`),
  ADD KEY `facility_id` (`facility_id`),
  ADD KEY `reserved_by` (`reserved_by`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `validated_by` (`validated_by`),
  ADD KEY `qr_code_id` (`qr_code_id`),
  ADD KEY `idx_date_range` (`start_datetime`,`end_datetime`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`reservation_type`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `rescheduled_by` (`rescheduled_by`),
  ADD KEY `cancelled_by` (`cancelled_by`);

--
-- Indexes for table `facility_room_usage_logs`
--
ALTER TABLE `facility_room_usage_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_reservation_id` (`reservation_id`),
  ADD KEY `idx_facility_id` (`facility_id`),
  ADD KEY `idx_room_level` (`room_level`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_logged_at` (`logged_at`);

--
-- Indexes for table `kyc_records`
--
ALTER TABLE `kyc_records`
  ADD PRIMARY KEY (`kyc_id`),
  ADD UNIQUE KEY `kyc_code` (`kyc_code`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_risk` (`risk_rating`),
  ADD KEY `idx_status` (`verification_status`);

--
-- Indexes for table `legal_cases`
--
ALTER TABLE `legal_cases`
  ADD PRIMARY KEY (`case_id`),
  ADD UNIQUE KEY `case_number` (`case_number`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`case_type`);

--
-- Indexes for table `legal_case_documents`
--
ALTER TABLE `legal_case_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `contract_id` (`contract_id`),
  ADD KEY `compliance_id` (`compliance_id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `legal_case_evidence`
--
ALTER TABLE `legal_case_evidence`
  ADD PRIMARY KEY (`evidence_id`);

--
-- Indexes for table `legal_case_hearings`
--
ALTER TABLE `legal_case_hearings`
  ADD PRIMARY KEY (`hearing_id`);

--
-- Indexes for table `legal_compliance`
--
ALTER TABLE `legal_compliance`
  ADD PRIMARY KEY (`compliance_id`),
  ADD UNIQUE KEY `reference_code` (`reference_code`),
  ADD KEY `evidence_document_id` (`evidence_document_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `legal_contracts`
--
ALTER TABLE `legal_contracts`
  ADD PRIMARY KEY (`contract_id`),
  ADD UNIQUE KEY `contract_number` (`contract_number`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `qr_code_id` (`qr_code_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_end_date` (`end_date`);

--
-- Indexes for table `legal_decision_matrix`
--
ALTER TABLE `legal_decision_matrix`
  ADD PRIMARY KEY (`matrix_id`);

--
-- Indexes for table `legal_escalation_notices`
--
ALTER TABLE `legal_escalation_notices`
  ADD PRIMARY KEY (`notice_id`);

--
-- Indexes for table `loan_documentation`
--
ALTER TABLE `loan_documentation`
  ADD PRIMARY KEY (`loan_doc_id`),
  ADD UNIQUE KEY `loan_doc_code` (`loan_doc_code`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_borrower` (`borrower_name`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user_read` (`user_id`,`is_read`);

--
-- Indexes for table `ocr_queue`
--
ALTER TABLE `ocr_queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD KEY `idx_ocr_doc` (`document_id`,`status`);

--
-- Indexes for table `permits_licenses`
--
ALTER TABLE `permits_licenses`
  ADD PRIMARY KEY (`permit_id`),
  ADD UNIQUE KEY `permit_code` (`permit_code`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expiry` (`expiry_date`);

--
-- Indexes for table `power_of_attorney`
--
ALTER TABLE `power_of_attorney`
  ADD PRIMARY KEY (`poa_id`),
  ADD UNIQUE KEY `poa_code` (`poa_code`),
  ADD KEY `resolution_id` (`resolution_id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`poa_type`);

--
-- Indexes for table `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD PRIMARY KEY (`qr_id`),
  ADD UNIQUE KEY `qr_uuid` (`qr_uuid`),
  ADD KEY `idx_module_ref` (`module`,`reference_table`,`reference_id`);

--
-- Indexes for table `reservation_equipment`
--
ALTER TABLE `reservation_equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_res_equip` (`reservation_id`,`equipment_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`visitor_id`),
  ADD UNIQUE KEY `visitor_code` (`visitor_code`),
  ADD KEY `idx_visitor_type` (`visitor_type`);

--
-- Indexes for table `visitor_daily_summary`
--
ALTER TABLE `visitor_daily_summary`
  ADD PRIMARY KEY (`summary_id`),
  ADD UNIQUE KEY `summary_date` (`summary_date`);

--
-- Indexes for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD UNIQUE KEY `visit_code` (`visit_code`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `host_user_id` (`host_user_id`),
  ADD KEY `facility_id` (`facility_id`),
  ADD KEY `qr_code_id` (`qr_code_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_date` (`check_in_time`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_log_visitor_type` (`visitor_type`);

--
-- Indexes for table `visitor_preregistrations`
--
ALTER TABLE `visitor_preregistrations`
  ADD PRIMARY KEY (`prereg_id`),
  ADD UNIQUE KEY `prereg_code` (`prereg_code`),
  ADD KEY `host_user_id` (`host_user_id`),
  ADD KEY `qr_code_id` (`qr_code_id`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `visit_log_id` (`visit_log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `board_resolutions`
--
ALTER TABLE `board_resolutions`
  MODIFY `resolution_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `collateral_registry`
--
ALTER TABLE `collateral_registry`
  MODIFY `collateral_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `department_folders`
--
ALTER TABLE `department_folders`
  MODIFY `folder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `demand_letters`
--
ALTER TABLE `demand_letters`
  MODIFY `demand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=354;

--
-- AUTO_INCREMENT for table `document_access`
--
ALTER TABLE `document_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `document_access_requests`
--
ALTER TABLE `document_access_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_categories`
--
ALTER TABLE `document_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `version_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_view_logs`
--
ALTER TABLE `document_view_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `facility_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `facility_equipment`
--
ALTER TABLE `facility_equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `facility_maintenance`
--
ALTER TABLE `facility_maintenance`
  MODIFY `maintenance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facility_reservations`
--
ALTER TABLE `facility_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `facility_room_usage_logs`
--
ALTER TABLE `facility_room_usage_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kyc_records`
--
ALTER TABLE `kyc_records`
  MODIFY `kyc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `legal_cases`
--
ALTER TABLE `legal_cases`
  MODIFY `case_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `legal_case_documents`
--
ALTER TABLE `legal_case_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `legal_case_evidence`
--
ALTER TABLE `legal_case_evidence`
  MODIFY `evidence_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `legal_case_hearings`
--
ALTER TABLE `legal_case_hearings`
  MODIFY `hearing_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `legal_compliance`
--
ALTER TABLE `legal_compliance`
  MODIFY `compliance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `legal_contracts`
--
ALTER TABLE `legal_contracts`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `legal_decision_matrix`
--
ALTER TABLE `legal_decision_matrix`
  MODIFY `matrix_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `legal_escalation_notices`
--
ALTER TABLE `legal_escalation_notices`
  MODIFY `notice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_documentation`
--
ALTER TABLE `loan_documentation`
  MODIFY `loan_doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ocr_queue`
--
ALTER TABLE `ocr_queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permits_licenses`
--
ALTER TABLE `permits_licenses`
  MODIFY `permit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `power_of_attorney`
--
ALTER TABLE `power_of_attorney`
  MODIFY `poa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `qr_codes`
--
ALTER TABLE `qr_codes`
  MODIFY `qr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservation_equipment`
--
ALTER TABLE `reservation_equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `visitor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `visitor_daily_summary`
--
ALTER TABLE `visitor_daily_summary`
  MODIFY `summary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `visitor_preregistrations`
--
ALTER TABLE `visitor_preregistrations`
  MODIFY `prereg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `board_resolutions`
--
ALTER TABLE `board_resolutions`
  ADD CONSTRAINT `board_resolutions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `board_resolutions_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `collateral_registry`
--
ALTER TABLE `collateral_registry`
  ADD CONSTRAINT `collateral_registry_ibfk_1` FOREIGN KEY (`loan_doc_id`) REFERENCES `loan_documentation` (`loan_doc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `collateral_registry_ibfk_2` FOREIGN KEY (`release_authorized_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `collateral_registry_ibfk_3` FOREIGN KEY (`foreclosure_case_id`) REFERENCES `legal_cases` (`case_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `collateral_registry_ibfk_4` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `collateral_registry_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `department_folders`
--
ALTER TABLE `department_folders`
  ADD CONSTRAINT `department_folders_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `demand_letters`
--
ALTER TABLE `demand_letters`
  ADD CONSTRAINT `demand_letters_ibfk_1` FOREIGN KEY (`loan_doc_id`) REFERENCES `loan_documentation` (`loan_doc_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `demand_letters_ibfk_2` FOREIGN KEY (`case_id`) REFERENCES `legal_cases` (`case_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `demand_letters_ibfk_3` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `demand_letters_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `facilities`
--
ALTER TABLE `facilities`
  ADD CONSTRAINT `facilities_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `facility_equipment`
--
ALTER TABLE `facility_equipment`
  ADD CONSTRAINT `facility_equipment_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`) ON DELETE SET NULL;

--
-- Constraints for table `facility_maintenance`
--
ALTER TABLE `facility_maintenance`
  ADD CONSTRAINT `facility_maintenance_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facility_maintenance_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `facility_equipment` (`equipment_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `facility_maintenance_ibfk_3` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `facility_reservations`
--
ALTER TABLE `facility_reservations`
  ADD CONSTRAINT `facility_reservations_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facility_reservations_ibfk_2` FOREIGN KEY (`reserved_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `facility_reservations_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `facility_reservations_ibfk_4` FOREIGN KEY (`validated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `facility_reservations_ibfk_5` FOREIGN KEY (`qr_code_id`) REFERENCES `qr_codes` (`qr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `facility_reservations_ibfk_6` FOREIGN KEY (`rescheduled_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `facility_reservations_ibfk_7` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `kyc_records`
--
ALTER TABLE `kyc_records`
  ADD CONSTRAINT `kyc_records_ibfk_1` FOREIGN KEY (`verified_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kyc_records_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kyc_records_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `legal_cases`
--
ALTER TABLE `legal_cases`
  ADD CONSTRAINT `legal_cases_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `legal_cases_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `legal_case_documents`
--
ALTER TABLE `legal_case_documents`
  ADD CONSTRAINT `legal_case_documents_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `legal_cases` (`case_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `legal_case_documents_ibfk_2` FOREIGN KEY (`contract_id`) REFERENCES `legal_contracts` (`contract_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `legal_case_documents_ibfk_3` FOREIGN KEY (`compliance_id`) REFERENCES `legal_compliance` (`compliance_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `legal_case_documents_ibfk_4` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE CASCADE;

--
-- Fix invalid assigned_to values in legal_compliance before adding constraints
--
UPDATE `legal_compliance` SET `assigned_to` = NULL WHERE `assigned_to` NOT IN (SELECT `user_id` FROM `users`);

--
-- Constraints for table `legal_compliance`
--
ALTER TABLE `legal_compliance`
  ADD CONSTRAINT `legal_compliance_ibfk_1` FOREIGN KEY (`evidence_document_id`) REFERENCES `documents` (`document_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `legal_compliance_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `legal_compliance_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `legal_contracts`
--
ALTER TABLE `legal_contracts`
  ADD CONSTRAINT `legal_contracts_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `legal_contracts_ibfk_2` FOREIGN KEY (`qr_code_id`) REFERENCES `qr_codes` (`qr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `legal_contracts_ibfk_3` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `legal_contracts_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_documentation`
--
ALTER TABLE `loan_documentation`
  ADD CONSTRAINT `loan_documentation_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_documentation_ibfk_2` FOREIGN KEY (`case_id`) REFERENCES `legal_cases` (`case_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `loan_documentation_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Fix invalid user_id values in notifications before adding constraints
--
UPDATE `notifications` SET `user_id` = NULL WHERE `user_id` NOT IN (SELECT `user_id` FROM `users`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `permits_licenses`
--
ALTER TABLE `permits_licenses`
  ADD CONSTRAINT `permits_licenses_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `permits_licenses_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `power_of_attorney`
--
ALTER TABLE `power_of_attorney`
  ADD CONSTRAINT `power_of_attorney_ibfk_1` FOREIGN KEY (`resolution_id`) REFERENCES `board_resolutions` (`resolution_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `power_of_attorney_ibfk_2` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `power_of_attorney_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reservation_equipment`
--
ALTER TABLE `reservation_equipment`
  ADD CONSTRAINT `reservation_equipment_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `facility_reservations` (`reservation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_equipment_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `facility_equipment` (`equipment_id`) ON DELETE CASCADE;

--
-- Constraints for table `visitor_logs`
--
ALTER TABLE `visitor_logs`
  ADD CONSTRAINT `visitor_logs_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`visitor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitor_logs_ibfk_2` FOREIGN KEY (`host_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visitor_logs_ibfk_3` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visitor_logs_ibfk_4` FOREIGN KEY (`qr_code_id`) REFERENCES `qr_codes` (`qr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visitor_logs_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `visitor_preregistrations`
--
ALTER TABLE `visitor_preregistrations`
  ADD CONSTRAINT `visitor_preregistrations_ibfk_1` FOREIGN KEY (`host_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitor_preregistrations_ibfk_2` FOREIGN KEY (`qr_code_id`) REFERENCES `qr_codes` (`qr_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visitor_preregistrations_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visitor_preregistrations_ibfk_4` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`visitor_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visitor_preregistrations_ibfk_5` FOREIGN KEY (`visit_log_id`) REFERENCES `visitor_logs` (`log_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
