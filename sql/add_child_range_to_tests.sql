ALTER TABLE `tests` 
ADD `min_child` DECIMAL(10,2) NULL DEFAULT NULL AFTER `max_female`,
ADD `max_child` DECIMAL(10,2) NULL DEFAULT NULL AFTER `min_child`,
ADD `child_unit` VARCHAR(50) NULL DEFAULT NULL AFTER `max_child`;
