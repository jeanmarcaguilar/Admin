-- Create roles table
CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add primary key to roles table
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

-- Set auto increment for roles table
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- Create role_user pivot table
CREATE TABLE `role_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add primary key and foreign keys for role_user table
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

-- Add foreign key constraints
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- Insert default roles
INSERT INTO `roles` (`name`, `description`, `created_at`, `updated_at`) VALUES
('Administrator', 'Full system access', NOW(), NOW()),
('Manager', 'Department management access', NOW(), NOW()),
('Employee', 'Basic access level', NOW(), NOW()),
('Guest', 'Limited view-only access', NOW(), NOW());

-- Add department and last_login_at columns to users table if they don't exist
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `department` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `last_login_at` timestamp NULL DEFAULT NULL;
