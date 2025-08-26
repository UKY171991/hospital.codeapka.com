-- Create owners table
CREATE TABLE IF NOT EXISTS `owners` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `address` TEXT,
  `added_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX (`added_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optionally add foreign key if you want strict constraints:
-- ALTER TABLE owners ADD CONSTRAINT fk_owners_added_by FOREIGN KEY (added_by) REFERENCES users(id);
