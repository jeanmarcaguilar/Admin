-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2026 at 04:13 PM
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
-- Database: `administrative`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `created_at`, `updated_at`) VALUES
(1, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(2, '2025-10-18 11:52:59', '2025-10-18 11:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `type` varchar(255) DEFAULT NULL,
  `requested_by` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `lead_time` int(11) DEFAULT NULL,
  `requester_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `rejected_by` varchar(255) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approvals`
--

INSERT INTO `approvals` (`id`, `request_id`, `title`, `description`, `status`, `type`, `requested_by`, `date`, `lead_time`, `requester_id`, `approver_id`, `approved_by`, `rejected_by`, `approved_at`, `rejected_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(2, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `quantity` int(10) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `purpose` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `room_id` bigint(20) UNSIGNED DEFAULT NULL,
  `equipment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendees` int(11) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `code`, `type`, `name`, `date`, `start_time`, `end_time`, `return_date`, `quantity`, `status`, `purpose`, `created_at`, `updated_at`, `room_id`, `equipment_id`, `user_id`, `attendees`, `notes`) VALUES
(1, 'BK-2025-001', 'room', 'Conference Room', '2025-02-01', '09:00', '11:00', NULL, NULL, 'approved', 'Team meeting for case C-2025-001', '2025-10-18 11:52:59', '2025-10-18 11:52:59', NULL, NULL, NULL, 1, NULL),
(2, 'EQ-2025-001', 'equipment', 'Projector', '2025-02-05', '10:00', '16:00', NULL, 1, 'approved', 'Client presentation for ABC Corp merger', '2025-10-18 11:52:59', '2025-10-19 08:05:40', NULL, NULL, NULL, 1, NULL),
(3, 'BK-2025-002', 'room', 'Meeting Room', '2025-02-10', '14:00', '15:30', NULL, NULL, 'approved', 'Family law consultation', '2025-10-18 11:52:59', '2025-10-18 11:52:59', NULL, NULL, NULL, 1, NULL),
(4, 'EQ-2025-002', 'equipment', 'Laptop', '2025-02-15', '08:00', '17:00', NULL, 2, 'approved', 'Patent research and documentation', '2025-10-18 11:52:59', '2025-10-18 11:52:59', NULL, NULL, NULL, 1, NULL),
(5, 'BK-2025-003', 'room', 'Training Room', '2025-02-20', '10:00', '12:00', NULL, NULL, 'approved', 'Staff training session', '2025-10-18 11:52:59', '2025-10-19 19:17:50', NULL, NULL, NULL, 1, NULL),
(6, 'EQ-2025-003', 'equipment', 'Camera', '2025-02-25', '09:00', '15:00', NULL, 1, 'pending', 'Video conference setup', '2025-10-18 11:52:59', '2025-10-18 11:52:59', NULL, NULL, NULL, 1, NULL),
(7, 'BK-2025-9914', 'room', 'Meeting Room', '2025-10-20', '01:06', '13:07', NULL, NULL, 'pending', 'dwdwdwd', '2025-10-19 09:07:11', '2025-10-19 09:07:11', NULL, NULL, NULL, 1, NULL),
(8, 'EQ-2025-2206', 'equipment', 'Projector', '2025-10-20', '01:06', '13:07', NULL, 1, 'pending', 'dwdwdwd', '2025-10-19 09:07:11', '2025-10-19 09:07:11', NULL, NULL, NULL, 1, NULL),
(9, 'BK-2025-5295', 'room', 'Conference Room', '2025-10-20', '00:01', '01:18', NULL, NULL, 'pending', 'dwdwdwd', '2025-10-19 19:16:58', '2025-10-19 19:16:58', NULL, NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `budget_status_updates`
--

CREATE TABLE `budget_status_updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `budget_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `notes` text DEFAULT NULL,
  `action_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `budget_status_updates`
--

INSERT INTO `budget_status_updates` (`id`, `budget_id`, `title`, `department_name`, `status`, `notes`, `action_date`, `user_id`, `created_at`, `updated_at`) VALUES
(1, '188', 'sample3', 'Logistics 1', 'Approved', 'Budget approved by administrator', '2026-02-08 02:17:04', 1, '2026-02-08 02:17:04', '2026-02-08 02:17:04');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_files`
--

CREATE TABLE `case_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `number` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type_label` varchar(255) DEFAULT NULL,
  `type_badge` varchar(255) DEFAULT NULL,
  `client` varchar(255) DEFAULT NULL,
  `client_org` varchar(255) DEFAULT NULL,
  `client_initials` varchar(10) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `filed_date` date DEFAULT NULL,
  `hearing_date` date DEFAULT NULL,
  `hearing_time` varchar(20) DEFAULT NULL,
  `contract_type` enum('employee','employment','service','other') DEFAULT NULL,
  `contract_number` varchar(100) DEFAULT NULL,
  `contract_date` date DEFAULT NULL,
  `contract_expiration` date DEFAULT NULL,
  `contract_status` enum('active','expired','terminated','renewed') DEFAULT NULL,
  `contract_document_path` varchar(255) DEFAULT NULL,
  `contract_notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `case_files`
--

INSERT INTO `case_files` (`id`, `number`, `name`, `type_label`, `type_badge`, `client`, `client_org`, `client_initials`, `status`, `filed_date`, `hearing_date`, `hearing_time`, `contract_type`, `contract_number`, `contract_date`, `contract_expiration`, `contract_status`, `contract_document_path`, `contract_notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'C-2025-001', 'Smith vs. Johnson Contract Dispute', 'Civil', 'Contract', 'John Smith', 'Smith Enterprises', 'JS', 'active', NULL, '2025-11-15', '09:00 AM', 'employment', 'CN-2025-001', '2025-01-15', '2025-12-31', 'active', NULL, 'Standard employment contract', NULL, '2025-10-18 11:52:59', '2025-10-20 00:02:06'),
(2, 'C-2025-002', 'ABC Corp Employment Case', 'Labor', 'Employment', 'ABC Corporation', 'ABC Corp', 'AC', 'urgent', NULL, '2025-11-20', '02:00 PM', 'service', 'CN-2025-002', '2025-02-01', '2025-11-30', 'active', NULL, 'Service agreement for consulting', NULL, '2025-10-18 11:52:59', '2025-10-20 00:02:06'),
(3, 'C-2025-003', 'Property Rights Litigation', 'Property', 'Real Estate', 'Maria Garcia', 'Garcia Properties', 'MG', 'pending', NULL, '2025-12-05', '10:30 AM', 'employee', 'CN-2025-003', '2024-12-15', '2025-12-14', 'active', NULL, 'Employee contract for full-time position', NULL, '2025-10-18 11:52:59', '2025-10-20 00:02:06'),
(4, 'C-2025-004', 'Patent Infringement Case', 'Intellectual Property', 'Patent', 'Tech Innovations Ltd', 'Tech Innovations', 'TI', 'active', NULL, '2025-02-12', '11:00 AM', 'other', 'CN-2025-004', '2025-01-10', '2026-01-09', 'active', NULL, 'Special project contract', NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(5, 'C-2025-005', 'Family Law Matter', 'Family', 'Divorce', 'Robert Chen', 'Chen Family', 'RC', 'completed', NULL, '2025-01-30', '09:30 AM', 'employment', 'CN-2025-005', '2024-11-15', '2025-11-14', 'expired', NULL, 'Expired employment contract', NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(6, 'C-2025-006', 'Criminal Defense Case', 'Criminal', 'Defense', 'Lisa Martinez', 'Martinez Defense', 'LM', 'active', NULL, '2025-02-18', '03:00 PM', 'service', 'CN-2025-006', '2025-01-05', '2025-12-31', 'active', NULL, 'Legal services agreement', NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(7, 'C-2025-007', 'Corporate Merger', 'Corporate', 'Merger', 'Wilson & Associates', 'Wilson Corp', 'WA', 'urgent', NULL, '2025-02-08', '01:00 PM', 'other', 'CN-2025-007', '2025-01-20', '2026-01-19', 'active', NULL, 'Merger agreement', NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(8, 'C-2025-008', 'Personal Injury Claim', 'Personal Injury', 'Tort', 'James Wilson', 'Wilson Legal', 'JW', 'pending', NULL, '2025-02-25', '10:00 AM', 'service', 'CN-2025-008', '2025-02-01', '2025-11-30', 'active', NULL, 'Legal representation agreement', NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(9, 'C-2024-001', 'Smith vs. Johnson Contract Dispute', 'Civil', 'Civil', 'John Smith', 'Smith Enterprises', 'JS', 'Active', NULL, '2024-02-15', '10:00', 'employment', 'CN-2024-001', '2024-01-15', '2024-12-31', 'expired', NULL, 'Annual employment contract', NULL, '2025-10-19 23:54:00', '2025-10-19 23:54:00'),
(10, 'C-2024-002', 'State vs. Robert Davis', 'Criminal Defense', 'Criminal', 'Robert Davis', '', 'RD', 'Pending', NULL, '2024-02-20', '14:30', 'service', 'CN-2024-002', '2024-01-10', '2024-12-31', 'expired', NULL, 'Legal defense contract', NULL, '2025-10-19 23:54:00', '2025-10-19 23:54:00'),
(11, 'C-2024-003', 'Williams Divorce Proceedings', 'Family Law', 'Family', 'Sarah Williams', '', 'SW', 'Active', NULL, '2024-02-25', '09:00', 'other', 'CN-2024-003', '2024-01-20', '2024-12-31', 'expired', NULL, 'Divorce settlement agreement', NULL, '2025-10-19 23:54:00', '2025-10-19 23:54:00'),
(12, 'C-2024-004', 'TechCorp Merger Agreement', 'Corporate', 'Corporate', 'TechCorp Inc.', 'TechCorp Inc.', 'TC', 'Closed', NULL, NULL, NULL, 'other', 'CN-2024-004', '2024-01-05', '2025-01-04', 'active', NULL, 'Merger and acquisition agreement', NULL, '2025-10-19 23:54:00', '2025-10-19 23:54:00'),
(13, 'C-2024-005', 'Innovation Patent Dispute', 'Intellectual Property', 'IP', 'Innovation Labs', 'Innovation Labs LLC', 'IL', 'Active', NULL, '2024-03-01', '11:00', 'service', 'CN-2024-005', '2024-01-15', '2025-01-14', 'active', NULL, 'IP legal services contract', NULL, '2025-10-19 23:54:00', '2025-10-19 23:54:00');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `phone`, `company`, `address`, `city`, `state`, `postal_code`, `country`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Acme Corporation', 'info@acme.com', '123-456-7890', 'Acme Corp', '123 Business St', 'New York', 'NY', '10001', 'USA', 'Primary client with multiple cases', '2026-02-05 15:09:40', '2026-02-05 15:09:40', NULL),
(2, 'Globex Corporation', 'contact@globex.com', '234-567-8901', 'Globex', '456 Corporate Ave', 'San Francisco', 'CA', '94105', 'USA', 'Tech client with international presence', '2026-02-05 15:09:40', '2026-02-05 15:09:40', NULL),
(3, 'Initech', 'info@initech.com', '345-678-9012', 'Initech Inc', '789 Office Park', 'Austin', 'TX', '73301', 'USA', 'Software development company', '2026-02-05 15:09:40', '2026-02-05 15:09:40', NULL),
(4, 'Umbrella Corporation', 'contact@umbrella.com', '456-789-0123', 'Umbrella', '1 Research Park', 'Raccoon City', 'IL', '60007', 'USA', 'Pharmaceutical research', '2026-02-05 15:09:40', '2026-02-05 15:09:40', NULL),
(5, 'Stark Industries', 'tony@stark.com', '555-0123', 'Stark Industries', '200 Park Avenue', 'New York', 'NY', '10166', 'USA', 'Advanced technology and defense', '2026-02-05 15:09:40', '2026-02-05 15:09:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `compliance_tracking`
--

CREATE TABLE `compliance_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `due_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `responsible_person` varchar(255) DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `compliance_tracking`
--

INSERT INTO `compliance_tracking` (`id`, `code`, `title`, `type`, `status`, `due_date`, `description`, `responsible_person`, `priority`, `created_at`, `updated_at`) VALUES
(1, 'CPL-2023-045', 'Annual Financial Report', 'financial', 'active', '2023-12-31', 'SEC Compliance - Annual financial reporting requirement', 'Finance Team', 'high', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(2, 'CPL-2023-046', 'Employee Safety Training', 'safety', 'pending', '2023-11-15', 'Quarterly safety training for all employees', 'HR Department', 'medium', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(3, 'CPL-2023-047', 'Data Privacy Audit', 'legal', 'active', '2023-12-01', 'GDPR compliance audit and documentation', 'Legal Team', 'high', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(4, 'CPL-2023-048', 'Environmental Impact Assessment', 'environmental', 'overdue', '2023-10-30', 'Annual environmental impact assessment report', 'Operations Team', 'medium', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(5, 'CPL-2023-049', 'HR Policy Review', 'hr', 'completed', '2023-09-30', 'Annual review and update of HR policies', 'HR Department', 'low', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(6, 'CPL-2023-050', 'Tax Compliance Filing', 'financial', 'active', '2023-12-15', 'Quarterly tax compliance filing', 'Finance Team', 'critical', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(7, 'CPL-2023-051', 'Workplace Harassment Training', 'hr', 'pending', '2023-11-30', 'Mandatory harassment prevention training', 'HR Department', 'high', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(8, 'CPL-2023-052', 'Contract Renewal Review', 'legal', 'active', '2023-12-20', 'Annual contract renewal review process', 'Legal Team', 'medium', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(9, 'CPL-2025-002', 'dwdwdwd', 'legal', 'pending', '2025-10-22', 'dwdwdwdwd', 'dwdwdwdwd', 'high', '2025-10-20 04:05:49', '2025-10-20 04:05:49'),
(10, 'CPL-2025-003', 'dwdwddw', 'legal', 'pending', '2025-10-25', 'dwdwdwwdw', 'dwdwdwdw', 'high', '2025-10-20 04:06:13', '2025-10-20 04:06:13');

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `company` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`id`, `code`, `title`, `type`, `status`, `company`, `start_date`, `end_date`, `created_on`, `created_at`, `updated_at`) VALUES
(3, 'CTR-2025-0001', 'dwdd', 'service', 'pending', 'wdwdwdwd', '2025-10-20', '2026-10-20', '2025-10-20', '2025-10-20 08:25:33', '2025-10-20 08:38:21');

-- --------------------------------------------------------

--
-- Table structure for table `contract_types`
--

CREATE TABLE `contract_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_months` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract_types`
--

INSERT INTO `contract_types` (`id`, `name`, `description`, `duration_months`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Permanent', 'Regular employment with no fixed end date', NULL, 1, '2026-02-05 15:09:41', '2026-02-05 15:09:41'),
(2, 'Fixed Term', 'Employment for a specific period', 12, 1, '2026-02-05 15:09:41', '2026-02-05 15:09:41'),
(3, 'Probationary', 'Initial employment period for evaluation', 6, 1, '2026-02-05 15:09:41', '2026-02-05 15:09:41'),
(4, 'Project-based', 'Employment for a specific project duration', 6, 1, '2026-02-05 15:09:41', '2026-02-05 15:09:41'),
(5, 'Seasonal', 'Employment for seasonal work', 3, 1, '2026-02-05 15:09:41', '2026-02-05 15:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` enum('internal','payment','vendor','release_of_funds','purchase','disbursement','receipt') NOT NULL,
  `category` enum('financial','hr','legal','operations','contracts','utilities','projects','procurement','it','payroll') DEFAULT NULL,
  `size_label` varchar(255) DEFAULT NULL,
  `uploaded_on` date DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `version` varchar(32) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `is_shared` tinyint(1) DEFAULT 0,
  `visibility` enum('everyone','admin') DEFAULT 'everyone',
  `is_receipt` tinyint(1) DEFAULT 0,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `data_type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `code`, `name`, `type`, `category`, `size_label`, `uploaded_on`, `size`, `date`, `status`, `version`, `is_archived`, `is_shared`, `visibility`, `is_receipt`, `client_id`, `data_type`, `description`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 'DOC-2025-1001', 'Q1 Financial Report', 'internal', 'financial', NULL, NULL, '2.5 MB', '2025-10-15', 'Approved', NULL, 0, 0, 'admin', 0, NULL, 'financial_report', 'First quarter financial report for 2025', 'documents/financial/q1_report_2025.pdf', '2026-02-05 15:09:40', '2026-02-08 05:16:03'),
(2, 'DOC-2025-1002', 'Employee Handbook 2025', 'internal', 'hr', NULL, NULL, '1.8 MB', '2025-10-10', 'Rejected', NULL, 0, 0, 'everyone', 0, NULL, 'handbook', 'Updated employee handbook for 2025', 'documents/hr/employee_handbook_2025.pdf', '2026-02-05 15:09:40', '2026-02-08 05:17:34'),
(3, 'PAY-2025-1001', 'Vendor Payment - ABC Corp', 'payment', 'financial', NULL, NULL, '1.2 MB', '2025-10-12', 'Approved', NULL, 0, 1, 'admin', 0, 1, 'vendor_payment', 'Payment to ABC Corp for office supplies', 'documents/payments/abc_corp_payment_oct.pdf', '2026-02-05 15:09:40', '2026-02-09 09:09:54'),
(4, 'PAY-2025-1002', 'Utility Bill - October 2025', 'payment', 'utilities', NULL, NULL, '0.8 MB', '2025-10-05', 'Indexed', NULL, 0, 0, 'admin', 0, NULL, 'utility_bill', 'Monthly utility bill payment', 'documents/payments/utility_oct_2025.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40'),
(5, 'VEND-2025-1001', 'Vendor Contract - XYZ Supplies', 'vendor', 'contracts', NULL, NULL, '1.5 MB', '2025-09-28', 'Indexed', NULL, 0, 0, 'admin', 0, 2, 'vendor_contract', 'Annual supply contract with XYZ', 'documents/vendors/xyz_supply_contract.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40'),
(6, 'VEND-2025-1002', 'Vendor NDA - Tech Solutions Inc', 'vendor', 'legal', NULL, NULL, '1.1 MB', '2025-10-01', 'Indexed', NULL, 0, 1, 'admin', 0, 3, 'nda', 'Non-disclosure agreement with vendor', 'documents/vendors/tech_solutions_nda.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40'),
(7, 'ROF-2025-1001', 'Project Alpha - Phase 1 Release', 'release_of_funds', 'projects', NULL, NULL, '0.9 MB', '2025-10-18', 'Indexed', NULL, 0, 1, 'admin', 0, 4, 'fund_release', 'Approval for release of funds for Phase 1', 'documents/funds/project_alpha_phase1.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40'),
(8, 'ROF-2025-1002', 'Emergency Fund Release', 'release_of_funds', 'financial', NULL, NULL, '1.0 MB', '2025-10-08', 'Indexed', NULL, 0, 0, 'admin', 0, 5, 'emergency_fund', 'Approval for emergency fund release', 'documents/funds/emergency_release_oct.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40'),
(9, 'PO-2025-1001', 'Office Equipment Purchase', 'purchase', 'procurement', NULL, NULL, '2.1 MB', '2025-10-14', 'Indexed', NULL, 0, 0, 'admin', 0, NULL, 'purchase_order', 'Purchase order for new office computers', 'documents/purchases/office_equipment_po.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40'),
(10, 'PO-2025-1002', 'Software License Renewal', 'purchase', 'it', NULL, NULL, '0.7 MB', '2025-10-02', 'Indexed', NULL, 0, 1, 'admin', 0, 6, 'software_license', 'Annual software license renewal', 'documents/purchases/software_licenses_2025.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40'),
(11, 'DISB-2025-1001', 'October 2025 Payroll', 'disbursement', 'payroll', NULL, NULL, '2.8 MB', '2025-10-01', 'Indexed', NULL, 0, 0, 'admin', 0, NULL, 'payroll', 'Monthly payroll disbursement', 'documents/disbursements/payroll_oct_2025.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40'),
(12, 'DISB-2025-1002', 'Vendor Payments - October 2025', 'disbursement', 'financial', NULL, NULL, '1.9 MB', '2025-10-03', 'Approved', NULL, 0, 1, 'admin', 0, 7, 'vendor_payments', 'Monthly vendor payments report', 'documents/disbursements/vendor_payments_oct.pdf', '2026-02-05 15:09:40', '2026-02-09 04:48:24'),
(13, 'RCPT-2025-1001', 'Client Payment Receipt - #1001', 'receipt', 'financial', NULL, NULL, '0.5 MB', '2025-10-20', 'Indexed', NULL, 0, 1, 'everyone', 1, 1, 'payment_receipt', 'Payment receipt for client #1001', 'documents/receipts/client_1001_payment.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40'),
(14, 'RCPT-2025-1002', 'Office Supplies Receipt - Oct 2025', 'receipt', 'procurement', NULL, NULL, '0.6 MB', '2025-10-19', 'Indexed', NULL, 0, 0, 'admin', 1, NULL, 'supplies_receipt', 'Receipt for office supplies purchase', 'documents/receipts/office_supplies_oct.pdf', '2026-02-05 15:09:40', '2026-02-05 15:09:40');

-- --------------------------------------------------------

--
-- Table structure for table `employee_contracts`
--

CREATE TABLE `employee_contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `contract_type_id` bigint(20) UNSIGNED NOT NULL,
  `contract_number` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `job_title` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `salary` decimal(12,2) NOT NULL,
  `status` enum('active','expired','terminated','renewed') NOT NULL DEFAULT 'active',
  `renewal_reminder_date` date DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'audio',
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `type`, `description`, `location`, `available`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 'Projector', 'video', 'HD projector with HDMI connection', 'Storage Room A', 1, 3, '2026-02-10 07:48:53', '2026-02-10 07:48:53'),
(2, 'Laptop', 'computer', 'Windows laptop with office software', 'Storage Room B', 1, 5, '2026-02-10 07:48:53', '2026-02-10 07:48:53'),
(3, 'Audio System', 'audio', 'Portable audio system with microphone', 'Storage Room A', 1, 2, '2026-02-10 07:48:53', '2026-02-10 07:48:53');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `created_at`, `updated_at`, `date`) VALUES
(1, '2025-10-18 11:52:59', '2025-10-18 11:52:59', NULL),
(2, '2025-10-18 11:52:59', '2025-10-18 11:52:59', NULL),
(3, '2025-10-18 11:52:59', '2025-10-18 11:52:59', NULL);

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
-- Table structure for table `financial_proposal_status_overrides`
--

CREATE TABLE `financial_proposal_status_overrides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ref_no` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `financial_proposal_status_overrides`
--

INSERT INTO `financial_proposal_status_overrides` (`id`, `ref_no`, `status`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'PROP-196', 'Rejected', 1, '2026-02-10 07:59:49', '2026-02-10 07:59:49');

-- --------------------------------------------------------

--
-- Table structure for table `hearings`
--

CREATE TABLE `hearings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `case_number` varchar(255) DEFAULT NULL,
  `hearing_date` date NOT NULL,
  `hearing_time` varchar(20) DEFAULT NULL,
  `court_location` varchar(255) DEFAULT NULL,
  `judge` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'scheduled',
  `priority` varchar(20) DEFAULT 'medium',
  `description` text DEFAULT NULL,
  `responsible_lawyer` varchar(255) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `case_type` varchar(100) DEFAULT NULL,
  `reminder_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hearings`
--

INSERT INTO `hearings` (`id`, `title`, `type`, `case_number`, `hearing_date`, `hearing_time`, `court_location`, `judge`, `status`, `priority`, `description`, `responsible_lawyer`, `client_name`, `case_type`, `reminder_sent`, `created_at`, `updated_at`) VALUES
(1, 'Motion Hearing - Contract Dispute', 'Court Hearing', 'C-2025-001', '2025-02-15', '09:00 AM', 'Regional Trial Court', 'Hon. Maria Santos', 'scheduled', 'high', 'Motion for summary judgment hearing', 'Atty. John Smith', 'Smith Enterprises', 'Civil', 0, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(2, 'Preliminary Conference', 'Court Hearing', 'C-2025-002', '2025-02-10', '02:00 PM', 'Labor Relations Commission', 'Hon. Carlos Rodriguez', 'scheduled', 'urgent', 'Preliminary conference for employment dispute', 'Atty. Sarah Johnson', 'ABC Corporation', 'Labor', 1, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(3, 'Settlement Conference', 'Court Hearing', 'C-2025-003', '2025-02-20', '10:30 AM', 'Metropolitan Court', 'Hon. Ana Garcia', 'scheduled', 'medium', 'Settlement conference for property rights', 'Atty. Michael Brown', 'Garcia Properties', 'Property', 0, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(4, 'Patent Validity Hearing', 'Court Hearing', 'C-2025-004', '2025-02-12', '11:00 AM', 'Intellectual Property Court', 'Hon. David Kim', 'scheduled', 'high', 'Hearing on patent validity and infringement', 'Atty. Jennifer Lee', 'Tech Innovations Ltd', 'Intellectual Property', 0, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(5, 'Final Hearing', 'Court Hearing', 'C-2025-005', '2025-01-30', '09:30 AM', 'Family Court', 'Hon. Lisa Chen', 'completed', 'medium', 'Final hearing for divorce proceedings', 'Atty. Robert Chen', 'Chen Family', 'Family', 1, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(6, 'Bail Hearing', 'Court Hearing', 'C-2025-006', '2025-02-18', '03:00 PM', 'Criminal Court', 'Hon. James Wilson', 'scheduled', 'urgent', 'Bail hearing for criminal defense case', 'Atty. Lisa Martinez', 'Martinez Defense', 'Criminal', 0, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(7, 'Merger Approval Hearing', 'Court Hearing', 'C-2025-007', '2025-02-08', '01:00 PM', 'Corporate Court', 'Hon. Patricia Wilson', 'scheduled', 'high', 'Court approval for corporate merger', 'Atty. James Wilson', 'Wilson & Associates', 'Corporate', 1, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(8, 'Mediation Session', 'Mediation', 'C-2025-008', '2025-02-25', '10:00 AM', 'Mediation Center', 'Mediator Sarah Davis', 'scheduled', 'medium', 'Mediation session for personal injury claim', 'Atty. James Wilson', 'Wilson Legal', 'Personal Injury', 0, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(9, 'Document Filing Deadline', 'Filing Deadline', 'C-2025-001', '2025-02-12', '05:00 PM', 'Court Clerk Office', NULL, 'scheduled', 'high', 'Deadline for filing motion for summary judgment', 'Atty. John Smith', 'Smith Enterprises', 'Civil', 0, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(10, 'Discovery Deadline', 'Filing Deadline', 'C-2025-002', '2025-02-05', '05:00 PM', 'Court Clerk Office', NULL, 'scheduled', 'urgent', 'Deadline for completing discovery process', 'Atty. Sarah Johnson', 'ABC Corporation', 'Labor', 1, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(11, 'Appeal Filing Deadline', 'Filing Deadline', 'C-2025-005', '2025-02-15', '05:00 PM', 'Appeals Court', NULL, 'scheduled', 'medium', 'Deadline for filing notice of appeal', 'Atty. Robert Chen', 'Chen Family', 'Family', 0, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(12, 'Compliance Review Meeting', 'Meeting', 'COMP-2025-001', '2025-02-14', '02:00 PM', 'Office Conference Room', NULL, 'scheduled', 'medium', 'Monthly compliance review meeting', 'Legal Team', 'Internal', 'Compliance', 0, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(13, 'Client Consultation', 'Meeting', 'CONS-2025-001', '2025-02-16', '11:00 AM', 'Office Conference Room', NULL, 'scheduled', 'low', 'Client consultation for new case', 'Atty. Jennifer Lee', 'New Client', 'Consultation', 0, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(14, 'dwwdwd', 'court_hearing', 'dwdwwdw', '2025-10-21', NULL, NULL, NULL, 'scheduled', 'Medium', NULL, NULL, NULL, NULL, 0, '2025-10-20 21:46:54', '2025-10-20 21:46:54');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_29_023035_add_username_to_users_table', 1),
(5, '2025_10_02_181420_create_case_files_table', 1),
(6, '2025_10_02_181429_create_approvals_table', 1),
(7, '2025_10_02_181448_create_hearings_table', 1),
(8, '2025_10_02_181454_create_documents_table', 1),
(9, '2025_10_02_181634_create_activities_table', 1),
(10, '2025_10_02_181639_create_events_table', 1),
(11, '2025_10_14_000000_create_visitors_table', 1),
(12, '2025_10_14_000001_alter_case_files_add_columns', 1),
(13, '2025_10_14_000002_alter_documents_add_columns', 1),
(14, '2025_10_14_000003_alter_approvals_add_columns', 1),
(15, '2025_10_14_000004_create_archival_settings_table', 1),
(16, '2025_10_14_000005_create_bookings_table', 1),
(17, '2025_10_14_000004_add_approval_columns', 2),
(18, '2026_02_08_000000_create_budget_status_updates_table', 3),
(19, '2026_02_08_000000_increase_status_column_length', 4),
(20, '2024_01_31_000000_create_roles_table', 5),
(21, '2025_03_15_000000_add_contract_management_to_case_files', 6),
(22, '2025_10_02_181639_add_date_column_to_events_table', 6),
(23, '2025_10_14_000006_create_rooms_table', 6),
(24, '2025_10_14_000007_create_equipment_table', 6),
(25, '2025_10_14_000008_update_bookings_table_add_foreign_keys', 6),
(26, '2025_10_17_000006_add_missing_columns_to_documents_table', 6),
(27, '2025_10_18_000002_add_contract_fields_to_case_files', 7),
(28, '2025_10_18_000003_add_version_to_documents_table', 7),
(29, '2025_10_18_020939_create_compliance_tracking_table', 8),
(30, '2025_10_20_000000_add_document_fields', 9),
(31, '2026_02_10_000001_create_financial_proposal_status_overrides_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `notes` varchar(500) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `type`, `user_id`, `group_id`, `role`, `document_type`, `permissions`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 'user', 1, NULL, 'admin', 'all', '[\"view\", \"edit\", \"delete\", \"share\", \"download\", \"print\"]', 'Full access for Administrator', 'active', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(2, 'user', 2, NULL, 'editor', 'financial', '[\"view\", \"edit\", \"share\", \"download\", \"print\"]', 'Finance editor permissions', 'active', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(3, 'user', 3, NULL, 'viewer', 'hr', '[\"view\", \"download\", \"print\"]', 'HR viewer permissions', 'inactive', '2025-10-18 11:52:59', '2025-10-18 11:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'Full system access', '2026-02-10 07:43:55', '2026-02-10 07:43:55'),
(2, 'Manager', 'Department management access', '2026-02-10 07:43:55', '2026-02-10 07:43:55'),
(3, 'Employee', 'Basic access level', '2026-02-10 07:43:55', '2026-02-10 07:43:55'),
(4, 'Guest', 'Limited view-only access', '2026-02-10 07:43:55', '2026-02-10 07:43:55');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'meeting',
  `capacity` int(11) NOT NULL DEFAULT 10,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `type`, `capacity`, `description`, `location`, `available`, `created_at`, `updated_at`) VALUES
(1, 'Conference Room', 'conference', 20, 'Main conference room with projector and audio system', 'Floor 2', 1, '2026-02-10 07:48:53', '2026-02-10 07:48:53'),
(2, 'Meeting Room', 'meeting', 8, 'Small meeting room for team discussions', 'Floor 1', 1, '2026-02-10 07:48:53', '2026-02-10 07:48:53'),
(3, 'Training Room', 'training', 15, 'Training room with whiteboard and projector', 'Floor 3', 1, '2026-02-10 07:48:53', '2026-02-10 07:48:53');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('D066YUBuBHBq0dd6JEUAxTvVQXlp5H870Cx5MQwB', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoicG9tVnd5cWp2VTVYTUY2ZHRxTkxmWHdyaG5IRHFpZFBzN3dPWjFjQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hcGkvc2VydmVyLXRpbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTM6Imxhc3RfYWN0aXZpdHkiO2k6MTc3MDczNzE4MTt9', 1770737181),
('kMQwslka3SZJERWA5ke9IIirffrzFhPiGb3sVBW1', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiN1V4cnV4YjVLWlhjWlFpUVpEUTBaaHVUemVPbTh5T29hZmFHVHpxYSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL3NlcnZlci10aW1lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjEzOiJsYXN0X2FjdGl2aXR5IjtpOjE3NzA3MzkxOTM7fQ==', 1770739193);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` enum('Administrator','Manager','Employee','Guest') DEFAULT 'Employee',
  `department` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `email_verified_at`, `password`, `remember_token`, `role`, `department`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', 'jeanmarcaguilar829@gmail.com', '+63 917 123 4567', NULL, '$2y$12$MGGgIjrpjWZdRhTnVMTs4.v2wWgb/.lJqZMGOehHonN9mrmZrvU3i', NULL, 'Administrator', 'IT Department', '2025-10-18 11:52:59', '2025-10-18 11:52:59', '2025-10-18 03:57:25'),
(2, 'Alice Example', 'alice', 'janalbert11@gmail.com', '+63 927 234 5678', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'Manager', 'HR Department', '2025-10-17 14:30:00', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(3, 'Bob Example', 'bob', 'emmanuel.aguilar0830@gmail.com', '+63 938 345 6789', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'Employee', 'Finance Department', '2025-10-16 09:15:00', '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(4, 'Charlie Example', 'charlie', 'charlie@example.com', '+63 946 456 7890', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'Guest', 'External', NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59');

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `visitor_type` varchar(255) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `host_department` varchar(255) DEFAULT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_in_time` varchar(255) DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `check_out_time` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `qr_data` text DEFAULT NULL,
  `qr_generated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id`, `code`, `name`, `company`, `visitor_type`, `host`, `host_department`, `check_in_date`, `check_in_time`, `check_out_date`, `check_out_time`, `purpose`, `status`, `qr_code`, `qr_data`, `qr_generated_at`, `created_at`, `updated_at`) VALUES
(1, 'V-2025-001', 'Sarah Johnson', 'Smith Enterprises', 'client', 'Sarah Johnson', 'Procurement', '2025-01-28', '09:15', '2025-01-28', '11:30', 'meeting', 'checked_out', NULL, NULL, NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(2, 'V-2025-002', 'Michael Brown', 'ABC Corporation', 'client', 'Michael Brown', 'Sales', '2025-01-29', '14:00', '2025-01-29', '16:45', 'meeting', 'checked_out', NULL, NULL, NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(3, 'V-2025-003', 'Jennifer Lee', 'Tech Innovations Ltd', 'client', 'Jennifer Lee', 'Business Development', '2025-01-30', '10:30', NULL, NULL, 'meeting', 'checked_in', NULL, NULL, NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(4, 'V-2025-004', 'Robert Chen', 'Maintenance Solutions', 'contractor', 'Robert Chen', 'IT', '2025-02-01', '08:00', NULL, NULL, 'maintenance', 'scheduled', NULL, NULL, NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(5, 'V-2025-005', 'Lisa Martinez', 'Legal Associates', 'vendor', 'Lisa Martinez', 'Legal', '2025-01-27', '13:00', '2025-01-27', '17:00', 'delivery', 'checked_out', NULL, NULL, NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(6, 'V-2025-006', 'James Wilson', 'Wilson & Associates', 'client', 'James Wilson', 'Management', '2025-01-31', '11:00', NULL, NULL, 'interview', 'checked_in', NULL, NULL, NULL, '2025-10-18 11:52:59', '2025-10-18 11:52:59'),
(7, 'V-2025-3400', 'Jean Marc Aguilar', 'bwahahhah', 'client', 'dwdwd', 'dwdwdwdw', '2025-10-21', '03:05', NULL, NULL, 'delivery', 'scheduled', NULL, NULL, NULL, '2025-10-20 22:05:26', '2025-10-20 22:05:26'),
(8, 'V-2025-8182', 'dwdwd dwdwdw', 'dwdwdwdw', 'client', 'dwdwd', 'dwdwdw', '2025-10-21', '16:11', NULL, NULL, 'meeting', 'scheduled', NULL, NULL, NULL, '2025-10-20 22:12:03', '2025-10-20 22:12:03'),
(9, 'V-2025-5879', 'Jean Marc Aguilar', 'bwahahhah', 'contractor', 'dwdwd', 'dwdwdwdw', '2025-10-21', '14:12', NULL, NULL, 'meeting', 'scheduled', NULL, NULL, NULL, '2025-10-20 22:12:30', '2025-10-20 22:12:30'),
(10, 'V-2025-0808', 'Jean Marc Aguilar', 'bwahahhah', 'client', 'dwdwd', 'dwdwdwdw', '2025-10-21', '14:17', NULL, NULL, 'interview', 'scheduled', NULL, NULL, NULL, '2025-10-20 22:17:38', '2025-10-20 22:17:38'),
(11, 'V-2025-5302', 'Jean Marc Aguilar', 'bwahahhah', 'vendor', 'dwdwd', 'dwdwdwdw', '2025-10-21', '14:48', NULL, NULL, 'meeting', 'checked_in', NULL, NULL, NULL, '2025-10-20 22:21:26', '2025-10-20 22:48:07'),
(12, 'V-2025-0680', 'Jean Marc Aguilar', 'bwahahhah', 'client', 'dwdwd', 'dwdwdwdw', '2025-10-21', '14:25', NULL, NULL, 'meeting', 'checked_in', NULL, NULL, NULL, '2025-10-20 22:24:06', '2025-10-20 22:25:05'),
(13, 'VIS-UD4T0JIOOA', 'Jean Marc Aguilar', 'bestlink', 'vip', 'N/A', NULL, '2026-02-05', '15:11:15', NULL, NULL, 'mwodwodmwdm', 'pending', NULL, NULL, NULL, '2026-02-05 07:11:15', '2026-02-05 07:11:15'),
(14, 'VIS-3OUCEMJTDF', 'Jean Marc Aguilar', 'jeiaiadiwdiamd', 'standard', 'N/A', NULL, '2026-02-05', '15:15:11', NULL, NULL, 'nfiamidmaidmwad', 'pending', NULL, NULL, NULL, '2026-02-05 07:15:11', '2026-02-05 07:15:11'),
(15, 'VIS-YHOC1J9FZA', 'Jean Marc Aguilar', 'mdwamdi', 'standard', 'N/A', NULL, '2026-02-05', '15:51:55', NULL, NULL, 'miomi', 'pending', NULL, NULL, NULL, '2026-02-05 07:51:55', '2026-02-05 07:51:55'),
(16, 'VIS-JFITETIZNS', 'Jean Marc Aguilar', 'jdiamdiamwid', 'standard', 'N/A', NULL, '2026-02-05', '15:59:28', NULL, NULL, 'dmaidmiawmd', 'pending', NULL, NULL, NULL, '2026-02-05 07:59:28', '2026-02-05 07:59:28'),
(17, 'VIS-D6VXA6MOHS', 'Jean Marc Aguilar', 'tite', 'standard', 'N/A', NULL, '2026-02-05', '16:03:56', NULL, NULL, 'fmiafmaw', 'pending', NULL, NULL, NULL, '2026-02-05 08:03:56', '2026-02-05 08:03:56'),
(18, 'VIS-6MVZJZQWQX', 'Jean Marc Aguilar', 'tite', 'vip', 'N/A', NULL, '2026-02-05', '17:17:21', NULL, NULL, 'Meeting', 'checked_in', NULL, NULL, NULL, '2026-02-05 09:17:21', '2026-02-05 09:17:39'),
(19, 'VIS-SRZJITIZBE', 'Jean Marc Aguilar', 'wow', 'vip', 'N/A', NULL, '2026-02-05', '18:13:18', NULL, NULL, 'dmiwmdw', 'pending', NULL, NULL, NULL, '2026-02-05 10:13:18', '2026-02-05 10:13:18'),
(20, 'VIS-QLVEXMX9FJ', 'dwdwdwd dwdwdwd', 'dwdwdwdwd', 'standard', 'N/A', NULL, '2026-02-05', '18:36:31', NULL, NULL, 'dwdwdwd', 'pending', NULL, NULL, NULL, '2026-02-05 10:36:31', '2026-02-05 10:36:31'),
(21, 'VIS-SSV1VTJUET', 'dwdwd wdwdwd', 'dwdwdwd', 'standard', 'N/A', NULL, '2026-02-05', '18:40:39', NULL, NULL, 'dwdwdwd', 'pending', NULL, NULL, NULL, '2026-02-05 10:40:39', '2026-02-05 10:40:39'),
(22, 'VIS-UXCNRHEWXM', 'dwdwdw dwdwdwdwdwdw', 'fifmowmfw', 'vip', 'N/A', NULL, '2026-02-05', '19:04:34', NULL, NULL, 'dwdwdwdd', 'pending', NULL, NULL, NULL, '2026-02-05 11:04:34', '2026-02-05 11:04:34'),
(23, 'VIS-VTIMHABLHN', 'dwdww dwdw', 'tite', 'vip', 'N/A', NULL, '2026-02-06', '12:36:01', NULL, NULL, 'fefefefef', 'pending', NULL, NULL, NULL, '2026-02-06 04:36:01', '2026-02-06 04:36:01'),
(24, 'VIS-IWQYNMNX5E', 'Jean Marc Aguilar', 'SSS', 'vip', 'N/A', NULL, '2026-02-06', '22:12:38', NULL, NULL, 'wdwdwdwd', 'pending', NULL, NULL, NULL, '2026-02-06 14:12:38', '2026-02-06 14:12:38'),
(25, 'V-2026-9586', 'Arrianne Aguilar', 'STI', 'delivery', 'Jean Marc Aguilar', NULL, '2026-02-07', NULL, '2026-02-07', NULL, 'Jeanmarc', 'pending', NULL, NULL, NULL, '2026-02-06 22:28:24', '2026-02-06 22:28:24'),
(26, 'V-2026-2880', 'Daniel  Aguilar', 'Philhealth', 'business', 'dwdwd', NULL, '2026-02-07', NULL, '2026-02-07', NULL, 'wdwdwdwd', 'pending', NULL, NULL, NULL, '2026-02-06 22:30:48', '2026-02-06 22:30:48'),
(27, 'V-2026-4605', 'Jean Marc Aguilar', 'STI', 'interview', 'wdwdwd', NULL, '2026-02-07', NULL, '2026-02-07', NULL, 'wdwdwdwd', 'pending', NULL, NULL, NULL, '2026-02-07 00:05:49', '2026-02-07 00:05:49'),
(28, 'V-2026-9007', 'Jean Marc Aguilar', 'SSS', 'maintenance', 'dwdwdwd', NULL, '2026-02-07', NULL, '2026-02-07', NULL, 'wdwdwdw', 'pending', NULL, NULL, NULL, '2026-02-07 00:11:27', '2026-02-07 00:11:27'),
(29, 'V-2026-1396', 'Jean Marc  Aguilar', 'Human Resources', 'delivery', 'Jean Marc', NULL, '2026-02-07', NULL, '2026-02-07', NULL, 'dwdwdwdwd', 'pending', NULL, NULL, NULL, '2026-02-07 03:24:30', '2026-02-07 03:24:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approvals_status_index` (`status`),
  ADD KEY `approvals_request_id_index` (`request_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookings_code_unique` (`code`),
  ADD KEY `bookings_room_id_foreign` (`room_id`),
  ADD KEY `bookings_equipment_id_foreign` (`equipment_id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`);

--
-- Indexes for table `budget_status_updates`
--
ALTER TABLE `budget_status_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_status_updates_budget_id_status_index` (`budget_id`,`status`),
  ADD KEY `budget_status_updates_user_id_index` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `case_files`
--
ALTER TABLE `case_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_files_number_index` (`number`),
  ADD KEY `case_files_contract_type_index` (`contract_type`),
  ADD KEY `case_files_contract_status_index` (`contract_status`),
  ADD KEY `case_files_contract_expiration_index` (`contract_expiration`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_email_unique` (`email`);

--
-- Indexes for table `compliance_tracking`
--
ALTER TABLE `compliance_tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `compliance_tracking_code_unique` (`code`),
  ADD KEY `compliance_tracking_status_index` (`status`),
  ADD KEY `compliance_tracking_type_index` (`type`),
  ADD KEY `compliance_tracking_due_date_index` (`due_date`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contracts_code_unique` (`code`);

--
-- Indexes for table `contract_types`
--
ALTER TABLE `contract_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_code_index` (`code`),
  ADD KEY `documents_client_id_foreign` (`client_id`);

--
-- Indexes for table `employee_contracts`
--
ALTER TABLE `employee_contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_contracts_contract_number_unique` (`contract_number`),
  ADD KEY `employee_contracts_employee_id_foreign` (`employee_id`),
  ADD KEY `employee_contracts_contract_type_id_foreign` (`contract_type_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `financial_proposal_status_overrides`
--
ALTER TABLE `financial_proposal_status_overrides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `financial_proposal_status_overrides_ref_no_unique` (`ref_no`),
  ADD KEY `financial_proposal_status_overrides_status_index` (`status`);

--
-- Indexes for table `hearings`
--
ALTER TABLE `hearings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hearings_hearing_date_index` (`hearing_date`),
  ADD KEY `hearings_status_index` (`status`),
  ADD KEY `hearings_priority_index` (`priority`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissions_user_id_index` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `user_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitors_code_index` (`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `budget_status_updates`
--
ALTER TABLE `budget_status_updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `case_files`
--
ALTER TABLE `case_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `compliance_tracking`
--
ALTER TABLE `compliance_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `contract_types`
--
ALTER TABLE `contract_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `employee_contracts`
--
ALTER TABLE `employee_contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_proposal_status_overrides`
--
ALTER TABLE `financial_proposal_status_overrides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hearings`
--
ALTER TABLE `hearings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budget_status_updates`
--
ALTER TABLE `budget_status_updates`
  ADD CONSTRAINT `budget_status_updates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_contracts`
--
ALTER TABLE `employee_contracts`
  ADD CONSTRAINT `employee_contracts_contract_type_id_foreign` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_contracts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_role_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
