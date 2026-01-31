-- Create rooms table
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT 'meeting',
  `capacity` int(11) DEFAULT 10,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default rooms
INSERT INTO `rooms` (`name`, `type`, `capacity`, `description`, `location`, `available`, `created_at`, `updated_at`) VALUES
('Conference Room', 'conference', 20, 'Main conference room with projector and audio system', 'Floor 2', 1, NOW(), NOW()),
('Meeting Room', 'meeting', 8, 'Small meeting room for team discussions', 'Floor 1', 1, NOW(), NOW()),
('Training Room', 'training', 15, 'Training room with whiteboard and projector', 'Floor 3', 1, NOW(), NOW());

-- Create equipment table
CREATE TABLE IF NOT EXISTS `equipment` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT 'audio',
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default equipment
INSERT INTO `equipment` (`name`, `type`, `description`, `location`, `available`, `quantity`, `created_at`, `updated_at`) VALUES
('Projector', 'video', 'HD projector with HDMI connection', 'Storage Room A', 1, 3, NOW(), NOW()),
('Laptop', 'computer', 'Windows laptop with office software', 'Storage Room B', 1, 5, NOW(), NOW()),
('Audio System', 'audio', 'Portable audio system with microphone', 'Storage Room A', 1, 2, NOW(), NOW());

-- Add foreign key columns to bookings table
ALTER TABLE `bookings` 
ADD COLUMN `room_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
ADD COLUMN `equipment_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
ADD COLUMN `user_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
ADD COLUMN `attendees` int(11) DEFAULT 1,
ADD COLUMN `notes` text DEFAULT NULL;
