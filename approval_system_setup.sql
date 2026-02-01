-- Add required columns to approvals table
ALTER TABLE `approvals` 
ADD COLUMN `request_id` VARCHAR(50) NULL AFTER `id`,
ADD COLUMN `title` VARCHAR(255) NULL AFTER `request_id`,
ADD COLUMN `description` TEXT NULL AFTER `title`,
ADD COLUMN `status` VARCHAR(50) DEFAULT 'pending' AFTER `description`,
ADD COLUMN `type` VARCHAR(50) NULL AFTER `status`,
ADD COLUMN `requested_by` VARCHAR(255) NULL AFTER `type`,
ADD COLUMN `date` DATE NULL AFTER `requested_by`,
ADD COLUMN `lead_time` INT NULL AFTER `date`,
ADD COLUMN `requester_id` BIGINT UNSIGNED NULL AFTER `lead_time`,
ADD COLUMN `approver_id` BIGINT UNSIGNED NULL AFTER `requester_id`,
ADD COLUMN `approved_by` VARCHAR(255) NULL AFTER `approver_id`,
ADD COLUMN `rejected_by` VARCHAR(255) NULL AFTER `approved_by`,
ADD COLUMN `approved_at` TIMESTAMP NULL AFTER `rejected_by`,
ADD COLUMN `rejected_at` TIMESTAMP NULL AFTER `approved_at`;

-- Add indexes for better performance
ALTER TABLE `approvals` 
ADD INDEX `idx_status` (`status`),
ADD INDEX `idx_request_id` (`request_id`);

-- Insert sample approval requests
INSERT INTO `approvals` (`request_id`, `title`, `type`, `requested_by`, `date`, `status`, `lead_time`, `description`, `requester_id`, `created_at`, `updated_at`) VALUES
('REQ-001', 'Meeting Room Booking', 'room', 'John Smith', '2025-01-25', 'pending', 3, 'Quarterly team meeting for Q1 planning', 1, NOW(), NOW()),
('REQ-002', 'Projector Request', 'equipment', 'Sarah Johnson', '2025-01-26', 'pending', 2, 'Need projector for client presentation', 1, NOW(), NOW()),
('REQ-003', 'Training Room Setup', 'room', 'Mike Wilson', '2025-01-28', 'approved', 7, 'New employee training session', 1, NOW(), NOW()),
('REQ-004', 'Audio System', 'equipment', 'Emily Davis', '2025-01-30', 'pending', 1, 'Audio system for company event', 1, NOW(), NOW()),
('REQ-005', 'Conference Room', 'room', 'David Brown', '2025-02-02', 'rejected', 5, 'Board meeting with investors', 1, NOW(), NOW());

-- Update approved and rejected records with proper metadata
UPDATE `approvals` 
SET `approved_by` = 'Admin User', 
    `approved_at` = NOW() 
WHERE `request_id` = 'REQ-003';

UPDATE `approvals` 
SET `rejected_by` = 'Admin User', 
    `rejected_at` = NOW(),
    `description` = CONCAT(`description`, '\n\nRejection reason: Budget constraints for this quarter.') 
WHERE `request_id` = 'REQ-005';

-- Show the results
SELECT * FROM `approvals`;
