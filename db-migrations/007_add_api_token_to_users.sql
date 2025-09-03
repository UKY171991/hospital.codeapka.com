-- Add api_token column to users for token-based API access
ALTER TABLE users
  ADD COLUMN api_token VARCHAR(128) DEFAULT NULL;

-- Optional: create an index for lookups
CREATE INDEX idx_users_api_token ON users(api_token);
