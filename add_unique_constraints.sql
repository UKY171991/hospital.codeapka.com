-- Add unique constraints to prevent duplicate user data
-- Run this SQL script to ensure database integrity

-- Add unique constraint on username (if not already exists)
ALTER TABLE users ADD CONSTRAINT unique_username UNIQUE (username);

-- Add unique constraint on email (if not already exists and email is not null)
-- Note: This allows multiple NULL emails but prevents duplicate non-null emails
ALTER TABLE users ADD CONSTRAINT unique_email UNIQUE (email);

-- Optional: Add unique constraint on mobile for patients (if not already exists)
ALTER TABLE patients ADD CONSTRAINT unique_patient_mobile UNIQUE (mobile);

-- Optional: Add unique constraint on uhid for patients (if not already exists)
ALTER TABLE patients ADD CONSTRAINT unique_patient_uhid UNIQUE (uhid);

-- Optional: Add unique constraint on registration_no for doctors (if not already exists)
ALTER TABLE doctors ADD CONSTRAINT unique_doctor_registration UNIQUE (registration_no);

-- Note: If constraints already exist, these commands will fail with an error.
-- This is expected and safe - it means the constraints are already in place.
