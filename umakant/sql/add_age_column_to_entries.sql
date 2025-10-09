-- ALTER SQL statements to add missing age column to entries table
-- Run these statements to fix the age and gender data saving issue

-- Add age column to entries table if it doesn't exist
ALTER TABLE `entries` 
ADD COLUMN `age` INT(11) DEFAULT NULL 
COMMENT 'Patient age at time of entry' 
AFTER `gender`;

-- Verify the column was added
-- You can run this to check: DESCRIBE entries;

-- Optional: Add index for better performance on age queries
-- ALTER TABLE `entries` ADD INDEX `idx_age` (`age`);

-- Optional: Add index for better performance on gender queries  
-- ALTER TABLE `entries` ADD INDEX `idx_gender` (`gender`);

-- Note: The gender column already exists in the entries table
-- This script only adds the missing age column
