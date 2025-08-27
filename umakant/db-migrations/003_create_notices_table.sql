-- Create notices table
CREATE TABLE IF NOT EXISTS `notices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT,
  `start_date` DATETIME DEFAULT NULL,
  `end_date` DATETIME DEFAULT NULL,
  `active` TINYINT(1) DEFAULT 1,
  `added_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX (`added_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key if desired:
-- ALTER TABLE notices ADD CONSTRAINT fk_notices_added_by FOREIGN KEY (added_by) REFERENCES users(id);
