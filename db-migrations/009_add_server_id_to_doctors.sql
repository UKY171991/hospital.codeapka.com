-- Migration: Add server_id column to doctors table
-- Date: 2025-09-04
-- Purpose: Add server_id field to support multi-server synchronization

-- Add server_id column to doctors table
ALTER TABLE `doctors` 
ADD COLUMN `server_id` int(11) DEFAULT NULL AFTER `id`,
ADD KEY `idx_doctors_server_id` (`server_id`);

-- Add comment for the new column
ALTER TABLE `doctors` 
MODIFY COLUMN `server_id` int(11) DEFAULT NULL COMMENT 'External server identifier for synchronization';
