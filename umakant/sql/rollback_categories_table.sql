-- Rollback script for categories table migration
-- WARNING: This will delete all category data and relationships

-- Drop the category summary view
DROP VIEW IF EXISTS category_summary;

-- Remove foreign key constraint from tests table if it exists
-- (This will be handled in the next task, but included here for completeness)
SET FOREIGN_KEY_CHECKS = 0;

-- Drop the categories table
DROP TABLE IF EXISTS categories;

SET FOREIGN_KEY_CHECKS = 1;

-- Note: If tests table has been modified to include category_id,
-- you may need to run additional commands to remove that column:
-- ALTER TABLE tests DROP COLUMN IF EXISTS category_id;