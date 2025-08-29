-- Migration: 005_drop_specimen_from_tests.sql
-- Drops the `specimen` column from `tests` table if it exists.
-- This script is idempotent and safe to re-run.

SET @sql = (
  SELECT IF(
    COUNT(*) > 0,
    'ALTER TABLE `tests` DROP COLUMN `specimen`;',
    'SELECT "specimen_column_not_present";'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tests'
    AND COLUMN_NAME = 'specimen'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- End of migration
