-- =====================================================
-- COMPLETE ADMINISTRATIVE SYSTEM DATABASE
-- Import this file into phpMyAdmin (XAMPP)
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS `administrative`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `administrative`;

-- =====================================================
-- 1. CORE LARAVEL TABLES
-- =====================================================

-- Users table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache tables
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Job tables
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. APPLICATION TABLES
-- =====================================================

-- Case Files table (basic structure from main file)
DROP TABLE IF EXISTS `case_files`;
CREATE TABLE `case_files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `case_files_number_index` (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Documents table (aligned with application fields)
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `size_label` varchar(255) DEFAULT NULL,
  `uploaded_on` date DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_code_index` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contracts table
DROP TABLE IF EXISTS `contracts`;
CREATE TABLE `contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `company` varchar(255) DEFAULT NULL,
  `created_on` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contracts_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookings table
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(255) NOT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `quantity` int unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `purpose` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bookings_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Visitors table
DROP TABLE IF EXISTS `visitors`;
CREATE TABLE `visitors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visitors_code_index` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Additional application tables
DROP TABLE IF EXISTS `approvals`;
CREATE TABLE `approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hearings`;
CREATE TABLE `hearings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hearings_hearing_date_index` (`hearing_date`),
  KEY `hearings_status_index` (`status`),
  KEY `hearings_priority_index` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `activities`;
CREATE TABLE `activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permissions table (for Access Control & Permissions page)
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL, -- user | group | department
  `user_id` bigint unsigned DEFAULT NULL,
  `group_id` bigint unsigned DEFAULT NULL,
  `role` varchar(50) NOT NULL, -- admin | editor | viewer | custom
  `document_type` varchar(50) NOT NULL, -- all | financial | hr | legal | other
  `permissions` json DEFAULT NULL, -- [view, edit, delete, share, download, print]
  `notes` varchar(500) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permissions_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Compliance Tracking table
DROP TABLE IF EXISTS `compliance_tracking`;
CREATE TABLE `compliance_tracking` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL, -- legal | financial | hr | safety | environmental | other
  `status` varchar(50) NOT NULL DEFAULT 'active', -- active | pending | overdue | completed
  `due_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `responsible_person` varchar(255) DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'medium', -- low | medium | high | critical
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compliance_tracking_code_unique` (`code`),
  KEY `compliance_tracking_status_index` (`status`),
  KEY `compliance_tracking_type_index` (`type`),
  KEY `compliance_tracking_due_date_index` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. SAMPLE DATA
-- =====================================================

-- Insert users from main file (password is 'password')
DELETE FROM `users`;
INSERT INTO `users` (`name`, `username`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`)
VALUES
('Administrator', 'admin', 'jeanmarcaguilar829@gmail.com', NULL,
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
NULL, NOW(), NOW()),
('Alice Example', 'alice', 'janalbert11@gmail.com', NULL,
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
NULL, NOW(), NOW()),
('Bob Example', 'bob', 'emmanuel.aguilar0830@gmail.com', NULL,
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
NULL, NOW(), NOW()),
('Charlie Example', 'charlie', 'charlie@example.com', NULL,
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
NULL, NOW(), NOW());

-- Insert cases from main file with hearing data
DELETE FROM `case_files`;
INSERT INTO `case_files` (`number`, `name`, `client`, `client_org`, `client_initials`, `type_label`, `type_badge`, `status`, `hearing_date`, `hearing_time`, `created_at`, `updated_at`) VALUES
  ('C-2025-001', 'Smith vs. Johnson Contract Dispute', 'John Smith', 'Smith Enterprises', 'JS', 'Civil', 'Contract', 'active', '2025-02-15', '09:00 AM', NOW(), NOW()),
  ('C-2025-002', 'ABC Corp Employment Case', 'ABC Corporation', 'ABC Corp', 'AC', 'Labor', 'Employment', 'urgent', '2025-02-10', '02:00 PM', NOW(), NOW()),
  ('C-2025-003', 'Property Rights Litigation', 'Maria Garcia', 'Garcia Properties', 'MG', 'Property', 'Real Estate', 'pending', '2025-02-20', '10:30 AM', NOW(), NOW()),
  ('C-2025-004', 'Patent Infringement Case', 'Tech Innovations Ltd', 'Tech Innovations', 'TI', 'Intellectual Property', 'Patent', 'active', '2025-02-12', '11:00 AM', NOW(), NOW()),
  ('C-2025-005', 'Family Law Matter', 'Robert Chen', 'Chen Family', 'RC', 'Family', 'Divorce', 'completed', '2025-01-30', '09:30 AM', NOW(), NOW()),
  ('C-2025-006', 'Criminal Defense Case', 'Lisa Martinez', 'Martinez Defense', 'LM', 'Criminal', 'Defense', 'active', '2025-02-18', '03:00 PM', NOW(), NOW()),
  ('C-2025-007', 'Corporate Merger', 'Wilson & Associates', 'Wilson Corp', 'WA', 'Corporate', 'Merger', 'urgent', '2025-02-08', '01:00 PM', NOW(), NOW()),
  ('C-2025-008', 'Personal Injury Claim', 'James Wilson', 'Wilson Legal', 'JW', 'Personal Injury', 'Tort', 'pending', '2025-02-25', '10:00 AM', NOW(), NOW());

-- Seed sample documents (optional)
DELETE FROM `documents`;
INSERT INTO `documents` (`code`, `name`, `type`, `category`, `size_label`, `uploaded_on`, `status`, `is_archived`, `created_at`, `updated_at`) VALUES
  ('DOC-2025-0001', 'Q3-Report-2025.pdf', 'PDF', 'financial', '1.2 MB', '2025-10-01', 'Indexed', 0, NOW(), NOW()),
  ('DOC-2025-0002', 'Meeting-Minutes.docx', 'Word', 'hr', '0.3 MB', '2025-10-05', 'Indexed', 0, NOW(), NOW()),
  ('DOC-2025-0003', 'Budget-2025.xlsx', 'Excel', 'legal', '0.9 MB', '2025-10-10', 'Indexed', 0, NOW(), NOW()),
  ('DOC-2025-0004', 'Strategy.pptx', 'PowerPoint', 'operations', '2.5 MB', '2025-10-12', 'Indexed', 0, NOW(), NOW());

-- Seed sample contracts
DELETE FROM `contracts`;
INSERT INTO `contracts` (`code`, `title`, `type`, `status`, `company`, `created_on`, `created_at`, `updated_at`) VALUES
  ('CT-2023-045', 'Service Agreement', 'service', 'active', 'Acme Corp', '2023-10-01', NOW(), NOW()),
  ('CT-2023-044', 'NDA', 'nda', 'pending', 'TechStart Inc', '2023-09-28', NOW(), NOW());

-- Insert sample bookings
INSERT INTO `bookings` (`code`, `type`, `name`, `date`, `start_time`, `end_time`, `return_date`, `quantity`, `status`, `purpose`, `created_at`, `updated_at`) VALUES
('BK-2025-001', 'room', 'Conference Room', '2025-02-01', '09:00', '11:00', NULL, NULL, 'approved', 'Team meeting for case C-2025-001', NOW(), NOW()),
('EQ-2025-001', 'equipment', 'Projector', '2025-02-05', '10:00', '16:00', NULL, 1, 'pending', 'Client presentation for ABC Corp merger', NOW(), NOW()),
('BK-2025-002', 'room', 'Meeting Room', '2025-02-10', '14:00', '15:30', NULL, NULL, 'approved', 'Family law consultation', NOW(), NOW()),
('EQ-2025-002', 'equipment', 'Laptop', '2025-02-15', '08:00', '17:00', NULL, 2, 'approved', 'Patent research and documentation', NOW(), NOW()),
('BK-2025-003', 'room', 'Training Room', '2025-02-20', '10:00', '12:00', NULL, NULL, 'pending', 'Staff training session', NOW(), NOW()),
('EQ-2025-003', 'equipment', 'Camera', '2025-02-25', '09:00', '15:00', NULL, 1, 'pending', 'Video conference setup', NOW(), NOW());

-- Insert sample visitors
INSERT INTO `visitors` (`code`, `name`, `company`, `visitor_type`, `host`, `host_department`, `check_in_date`, `check_in_time`, `check_out_date`, `check_out_time`, `purpose`, `status`, `created_at`, `updated_at`) VALUES
('V-2025-001', 'Sarah Johnson', 'Smith Enterprises', 'client', 'Sarah Johnson', 'Procurement', '2025-01-28', '09:15', '2025-01-28', '11:30', 'meeting', 'checked_out', NOW(), NOW()),
('V-2025-002', 'Michael Brown', 'ABC Corporation', 'client', 'Michael Brown', 'Sales', '2025-01-29', '14:00', '2025-01-29', '16:45', 'meeting', 'checked_out', NOW(), NOW()),
('V-2025-003', 'Jennifer Lee', 'Tech Innovations Ltd', 'client', 'Jennifer Lee', 'Business Development', '2025-01-30', '10:30', NULL, NULL, 'meeting', 'checked_in', NOW(), NOW()),
('V-2025-004', 'Robert Chen', 'Maintenance Solutions', 'contractor', 'Robert Chen', 'IT', '2025-02-01', '08:00', NULL, NULL, 'maintenance', 'scheduled', NOW(), NOW()),
('V-2025-005', 'Lisa Martinez', 'Legal Associates', 'vendor', 'Lisa Martinez', 'Legal', '2025-01-27', '13:00', '2025-01-27', '17:00', 'delivery', 'checked_out', NOW(), NOW()),
('V-2025-006', 'James Wilson', 'Wilson & Associates', 'client', 'James Wilson', 'Management', '2025-01-31', '11:00', NULL, NULL, 'interview', 'checked_in', NOW(), NOW());

-- Insert approvals from main file
DELETE FROM `approvals`;
INSERT INTO `approvals` (`created_at`, `updated_at`) VALUES
  (NOW(), NOW()),
  (NOW(), NOW());

-- Insert hearings from main file with detailed data
DELETE FROM `hearings`;
INSERT INTO `hearings` (`title`, `type`, `case_number`, `hearing_date`, `hearing_time`, `court_location`, `judge`, `status`, `priority`, `description`, `responsible_lawyer`, `client_name`, `case_type`, `reminder_sent`, `created_at`, `updated_at`) VALUES
  ('Motion Hearing - Contract Dispute', 'Court Hearing', 'C-2025-001', '2025-02-15', '09:00 AM', 'Regional Trial Court', 'Hon. Maria Santos', 'scheduled', 'high', 'Motion for summary judgment hearing', 'Atty. John Smith', 'Smith Enterprises', 'Civil', 0, NOW(), NOW()),
  ('Preliminary Conference', 'Court Hearing', 'C-2025-002', '2025-02-10', '02:00 PM', 'Labor Relations Commission', 'Hon. Carlos Rodriguez', 'scheduled', 'urgent', 'Preliminary conference for employment dispute', 'Atty. Sarah Johnson', 'ABC Corporation', 'Labor', 1, NOW(), NOW()),
  ('Settlement Conference', 'Court Hearing', 'C-2025-003', '2025-02-20', '10:30 AM', 'Metropolitan Court', 'Hon. Ana Garcia', 'scheduled', 'medium', 'Settlement conference for property rights', 'Atty. Michael Brown', 'Garcia Properties', 'Property', 0, NOW(), NOW()),
  ('Patent Validity Hearing', 'Court Hearing', 'C-2025-004', '2025-02-12', '11:00 AM', 'Intellectual Property Court', 'Hon. David Kim', 'scheduled', 'high', 'Hearing on patent validity and infringement', 'Atty. Jennifer Lee', 'Tech Innovations Ltd', 'Intellectual Property', 0, NOW(), NOW()),
  ('Final Hearing', 'Court Hearing', 'C-2025-005', '2025-01-30', '09:30 AM', 'Family Court', 'Hon. Lisa Chen', 'completed', 'medium', 'Final hearing for divorce proceedings', 'Atty. Robert Chen', 'Chen Family', 'Family', 1, NOW(), NOW()),
  ('Bail Hearing', 'Court Hearing', 'C-2025-006', '2025-02-18', '03:00 PM', 'Criminal Court', 'Hon. James Wilson', 'scheduled', 'urgent', 'Bail hearing for criminal defense case', 'Atty. Lisa Martinez', 'Martinez Defense', 'Criminal', 0, NOW(), NOW()),
  ('Merger Approval Hearing', 'Court Hearing', 'C-2025-007', '2025-02-08', '01:00 PM', 'Corporate Court', 'Hon. Patricia Wilson', 'scheduled', 'high', 'Court approval for corporate merger', 'Atty. James Wilson', 'Wilson & Associates', 'Corporate', 1, NOW(), NOW()),
  ('Mediation Session', 'Mediation', 'C-2025-008', '2025-02-25', '10:00 AM', 'Mediation Center', 'Mediator Sarah Davis', 'scheduled', 'medium', 'Mediation session for personal injury claim', 'Atty. James Wilson', 'Wilson Legal', 'Personal Injury', 0, NOW(), NOW()),
  ('Document Filing Deadline', 'Filing Deadline', 'C-2025-001', '2025-02-12', '05:00 PM', 'Court Clerk Office', NULL, 'scheduled', 'high', 'Deadline for filing motion for summary judgment', 'Atty. John Smith', 'Smith Enterprises', 'Civil', 0, NOW(), NOW()),
  ('Discovery Deadline', 'Filing Deadline', 'C-2025-002', '2025-02-05', '05:00 PM', 'Court Clerk Office', NULL, 'scheduled', 'urgent', 'Deadline for completing discovery process', 'Atty. Sarah Johnson', 'ABC Corporation', 'Labor', 1, NOW(), NOW()),
  ('Appeal Filing Deadline', 'Filing Deadline', 'C-2025-005', '2025-02-15', '05:00 PM', 'Appeals Court', NULL, 'scheduled', 'medium', 'Deadline for filing notice of appeal', 'Atty. Robert Chen', 'Chen Family', 'Family', 0, NOW(), NOW()),
  ('Compliance Review Meeting', 'Meeting', 'COMP-2025-001', '2025-02-14', '02:00 PM', 'Office Conference Room', NULL, 'scheduled', 'medium', 'Monthly compliance review meeting', 'Legal Team', 'Internal', 'Compliance', 0, NOW(), NOW()),
  ('Client Consultation', 'Meeting', 'CONS-2025-001', '2025-02-16', '11:00 AM', 'Office Conference Room', NULL, 'scheduled', 'low', 'Client consultation for new case', 'Atty. Jennifer Lee', 'New Client', 'Consultation', 0, NOW(), NOW());

-- Insert activities from main file
DELETE FROM `activities`;
INSERT INTO `activities` (`created_at`, `updated_at`) VALUES
  (NOW(), NOW()),
  (NOW(), NOW());

-- Insert events from main file
DELETE FROM `events`;
INSERT INTO `events` (`created_at`, `updated_at`) VALUES
  (NOW(), NOW()),
  (NOW(), NOW()),
  (NOW(), NOW());

-- Seed permissions for Access Control view
DELETE FROM `permissions`;
INSERT INTO `permissions` (`type`, `user_id`, `group_id`, `role`, `document_type`, `permissions`, `notes`, `status`, `created_at`, `updated_at`) VALUES
('user', 1, NULL, 'admin', 'all', JSON_ARRAY('view','edit','delete','share','download','print'), 'Full access for Administrator', 'active', NOW(), NOW()),
('user', 2, NULL, 'editor', 'financial', JSON_ARRAY('view','edit','share','download','print'), 'Finance editor permissions', 'active', NOW(), NOW()),
('user', 3, NULL, 'viewer', 'hr', JSON_ARRAY('view','download','print'), 'HR viewer permissions', 'inactive', NOW(), NOW());

-- Seed compliance tracking data
DELETE FROM `compliance_tracking`;
INSERT INTO `compliance_tracking` (`code`, `title`, `type`, `status`, `due_date`, `description`, `responsible_person`, `priority`, `created_at`, `updated_at`) VALUES
('CPL-2023-045', 'Annual Financial Report', 'financial', 'active', '2023-12-31', 'SEC Compliance - Annual financial reporting requirement', 'Finance Team', 'high', NOW(), NOW()),
('CPL-2023-046', 'Employee Safety Training', 'safety', 'pending', '2023-11-15', 'Quarterly safety training for all employees', 'HR Department', 'medium', NOW(), NOW()),
('CPL-2023-047', 'Data Privacy Audit', 'legal', 'active', '2023-12-01', 'GDPR compliance audit and documentation', 'Legal Team', 'high', NOW(), NOW()),
('CPL-2023-048', 'Environmental Impact Assessment', 'environmental', 'overdue', '2023-10-30', 'Annual environmental impact assessment report', 'Operations Team', 'medium', NOW(), NOW()),
('CPL-2023-049', 'HR Policy Review', 'hr', 'completed', '2023-09-30', 'Annual review and update of HR policies', 'HR Department', 'low', NOW(), NOW()),
('CPL-2023-050', 'Tax Compliance Filing', 'financial', 'active', '2023-12-15', 'Quarterly tax compliance filing', 'Finance Team', 'critical', NOW(), NOW()),
('CPL-2023-051', 'Workplace Harassment Training', 'hr', 'pending', '2023-11-30', 'Mandatory harassment prevention training', 'HR Department', 'high', NOW(), NOW()),
('CPL-2023-052', 'Contract Renewal Review', 'legal', 'active', '2023-12-20', 'Annual contract renewal review process', 'Legal Team', 'medium', NOW(), NOW());

-- =====================================================
-- 4. MIGRATION TRACKING
-- =====================================================

-- Create migrations table to track Laravel migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mark migrations as completed
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_09_29_023035_add_username_to_users_table', 1),
('2025_10_02_181420_create_case_files_table', 1),
('2025_10_02_181429_create_approvals_table', 1),
('2025_10_02_181448_create_hearings_table', 1),
('2025_10_02_181454_create_documents_table', 1),
('2025_10_02_181634_create_activities_table', 1),
('2025_10_02_181639_create_events_table', 1),
('2025_10_14_000000_create_visitors_table', 1),
('2025_10_14_000001_alter_case_files_add_columns', 1),
('2025_10_14_000002_alter_documents_add_columns', 1),
('2025_10_14_000003_alter_approvals_add_columns', 1),
('2025_10_14_000004_create_archival_settings_table', 1),
('2025_10_14_000005_create_bookings_table', 1);

-- Optional: clear sessions and cache tables (from main file)
TRUNCATE TABLE `sessions`;
TRUNCATE TABLE `cache`;
TRUNCATE TABLE `cache_locks`;

-- =====================================================
-- IMPORT INSTRUCTIONS FOR XAMPP
-- =====================================================
-- 1. Open XAMPP Control Panel
-- 2. Start Apache and MySQL services
-- 3. Open phpMyAdmin (http://localhost/phpmyadmin)
-- 4. Click "Import" tab
-- 5. Choose this SQL file (administrative_complete.sql)
-- 6. Click "Go" to import
-- 7. Database 'administrative' will be created with all tables and data
-- 
-- LOGIN CREDENTIALS (from your main file):
-- Username: admin
-- Password: password
-- Email: jeanmarcaguilar829@gmail.com
-- 
-- Alternative users:
-- alice/password (janalbert11@gmail.com)
-- bob/password (emmanuel.aguilar0830@gmail.com)
-- charlie/password (charlie@example.com)
-- =====================================================
