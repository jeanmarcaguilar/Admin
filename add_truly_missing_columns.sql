-- Add only the truly missing columns to documents table
ALTER TABLE `documents` 
ADD COLUMN `file_type` VARCHAR(255) NULL AFTER `file_path`,
ADD COLUMN `uploaded_on` DATE NULL AFTER `status`;
