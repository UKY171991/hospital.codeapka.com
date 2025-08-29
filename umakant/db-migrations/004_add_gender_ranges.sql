-- Migration: add gender specific min/max columns to tests
ALTER TABLE `tests`
  ADD COLUMN `min_male` DECIMAL(10,2) DEFAULT NULL,
  ADD COLUMN `max_male` DECIMAL(10,2) DEFAULT NULL,
  ADD COLUMN `min_female` DECIMAL(10,2) DEFAULT NULL,
  ADD COLUMN `max_female` DECIMAL(10,2) DEFAULT NULL;

-- To apply: run this SQL against the database (phpMyAdmin or mysql client)
