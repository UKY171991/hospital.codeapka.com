-- Add qr_code column to plans (stores relative path to uploaded QR image)
ALTER TABLE `plans`
  ADD COLUMN `qr_code` VARCHAR(255) DEFAULT NULL AFTER `end_date`;

-- Optional: create index (if you expect filtering by qr_code)
-- CREATE INDEX idx_plans_qr_code ON plans(qr_code);
