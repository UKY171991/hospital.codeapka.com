-- Add UPI column to plans table if missing
ALTER TABLE plans
  ADD COLUMN IF NOT EXISTS upi VARCHAR(255) DEFAULT NULL;

-- Also ensure price and time_type columns exist (used by the app):
-- ALTER TABLE plans ADD COLUMN IF NOT EXISTS price DECIMAL(10,2) DEFAULT 0;
-- ALTER TABLE plans ADD COLUMN IF NOT EXISTS time_type ENUM('monthly','yearly') DEFAULT 'monthly';

-- Run this SQL in your MySQL/MariaDB client.
