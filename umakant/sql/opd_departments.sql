-- Create OPD Departments table
CREATE TABLE IF NOT EXISTS `opd_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `head_of_department` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_added_by` (`added_by`),
  KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO `opd_departments` (`name`, `description`, `head_of_department`, `contact_number`, `email`, `location`, `status`, `added_by`) VALUES
('Cardiology', 'Heart and cardiovascular system treatment', 'Dr. John Smith', '+1 (555) 123-4567', 'cardiology@hospital.com', 'Floor 3, Wing A', 'Active', 1),
('Neurology', 'Brain and nervous system disorders', 'Dr. Sarah Johnson', '+1 (555) 234-5678', 'neurology@hospital.com', 'Floor 4, Wing B', 'Active', 1),
('Orthopedics', 'Bone, joint, and muscle treatment', 'Dr. Michael Brown', '+1 (555) 345-6789', 'orthopedics@hospital.com', 'Floor 2, Wing C', 'Active', 1),
('Pediatrics', 'Child healthcare and treatment', 'Dr. Emily Davis', '+1 (555) 456-7890', 'pediatrics@hospital.com', 'Floor 1, Wing A', 'Active', 1),
('General Medicine', 'General health checkups and treatment', 'Dr. Robert Wilson', '+1 (555) 567-8901', 'general@hospital.com', 'Floor 1, Wing B', 'Active', 1);
