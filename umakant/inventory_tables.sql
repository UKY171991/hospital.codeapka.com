-- Inventory Management Tables

-- Table for tracking processed emails (to avoid duplicates)
CREATE TABLE IF NOT EXISTS `processed_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` varchar(255) NOT NULL,
  `transaction_type` enum('income','expense') NOT NULL,
  `processed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_message_id` (`message_id`),
  KEY `idx_processed_at` (`processed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for system configuration (to store Gmail password)
CREATE TABLE IF NOT EXISTS `system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config_key` varchar(100) NOT NULL,
  `config_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Clients
CREATE TABLE IF NOT EXISTS `inventory_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('Individual','Corporate','Insurance','Government') NOT NULL DEFAULT 'Individual',
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `gst_number` varchar(50) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Income
CREATE TABLE IF NOT EXISTS `inventory_income` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `category` varchar(100) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Card','UPI','Bank Transfer','Cheque') NOT NULL DEFAULT 'Cash',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date`),
  KEY `idx_category` (`category`),
  KEY `idx_client_id` (`client_id`),
  CONSTRAINT `fk_income_client` FOREIGN KEY (`client_id`) REFERENCES `inventory_clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Expense
CREATE TABLE IF NOT EXISTS `inventory_expense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `category` varchar(100) NOT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Card','UPI','Bank Transfer','Cheque') NOT NULL DEFAULT 'Cash',
  `invoice_number` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date`),
  KEY `idx_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing

-- Sample Clients
INSERT INTO `inventory_clients` (`name`, `type`, `email`, `phone`, `address`, `city`, `state`, `status`) VALUES
('John Doe', 'Individual', 'john@example.com', '9876543210', '123 Main St', 'Mumbai', 'Maharashtra', 'Active'),
('ABC Corporation', 'Corporate', 'contact@abc.com', '9876543211', '456 Business Park', 'Delhi', 'Delhi', 'Active'),
('XYZ Insurance', 'Insurance', 'info@xyz.com', '9876543212', '789 Insurance Tower', 'Bangalore', 'Karnataka', 'Active');

-- Sample Income
INSERT INTO `inventory_income` (`date`, `category`, `client_id`, `description`, `amount`, `payment_method`) VALUES
(CURDATE(), 'Consultation', 1, 'General consultation fee', 500.00, 'Cash'),
(CURDATE(), 'Lab Tests', 2, 'Blood test package', 1500.00, 'Card'),
(DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Pharmacy', 1, 'Medicine purchase', 800.00, 'UPI');

-- Sample Expense
INSERT INTO `inventory_expense` (`date`, `category`, `vendor`, `description`, `amount`, `payment_method`) VALUES
(CURDATE(), 'Medical Supplies', 'MedSupply Co', 'Surgical gloves and masks', 2000.00, 'Bank Transfer'),
(CURDATE(), 'Utilities', 'Power Company', 'Electricity bill', 5000.00, 'Cheque'),
(DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Salaries', NULL, 'Staff salary payment', 50000.00, 'Bank Transfer');
