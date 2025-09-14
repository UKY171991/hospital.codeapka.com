-- Migration: add user_type column to users and ensure expire_date exists
-- Safe alter: add column only if it does not exist
-- NOTE: run this against your MySQL database. This file follows the project's migration conventions.

-- Add user_type column (varchar) to classify accounts (Pathology, Hospital, School)
ALTER TABLE `users` 
  ADD COLUMN IF NOT EXISTS `user_type` varchar(50) DEFAULT 'Pathology' AFTER `role`;

-- Ensure expire_date column exists (older dumps may already have it)
ALTER TABLE `users` 
  ADD COLUMN IF NOT EXISTS `expire_date` datetime DEFAULT NULL AFTER `last_login`;

-- If your MySQL/MariaDB version doesn't support IF NOT EXISTS for ADD COLUMN,
-- run equivalent conditional checks in your migration runner or manually verify before applying.
