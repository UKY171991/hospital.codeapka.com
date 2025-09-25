-- Migration: Support Multiple Tests Per Entry
-- This creates a proper many-to-many relationship between entries and tests
-- Run this after the main entries table is created

-- Create entry_tests junction table for many-to-many relationship
CREATE TABLE IF NOT EXISTS entry_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entry_id INT NOT NULL,
    test_id INT NOT NULL,
    result_value TEXT DEFAULT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    remarks TEXT DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    price DECIMAL(10,2) DEFAULT NULL,
    discount_amount DECIMAL(10,2) DEFAULT NULL,
    total_price DECIMAL(10,2) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (entry_id) REFERENCES entries(id) ON DELETE CASCADE,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE,
    
    -- Unique constraint to prevent duplicate test assignments
    UNIQUE KEY unique_entry_test (entry_id, test_id),
    
    -- Indexes for performance
    INDEX idx_entry_tests_entry_id (entry_id),
    INDEX idx_entry_tests_test_id (test_id),
    INDEX idx_entry_tests_status (status),
    INDEX idx_entry_tests_created_at (created_at)
);

-- Modify entries table to support multiple tests
ALTER TABLE entries 
ADD COLUMN IF NOT EXISTS grouped TINYINT(1) DEFAULT 0 COMMENT 'Whether this entry contains multiple tests',
ADD COLUMN IF NOT EXISTS tests_count INT DEFAULT 1 COMMENT 'Number of tests in this entry',
ADD COLUMN IF NOT EXISTS test_ids TEXT DEFAULT NULL COMMENT 'Comma-separated list of test IDs for quick reference',
ADD COLUMN IF NOT EXISTS test_names TEXT DEFAULT NULL COMMENT 'Comma-separated list of test names for quick reference',
ADD COLUMN IF NOT EXISTS test_results TEXT DEFAULT NULL COMMENT 'JSON string of test results for quick reference';

-- Make test_id nullable for backward compatibility
ALTER TABLE entries MODIFY COLUMN test_id INT DEFAULT NULL COMMENT 'Primary test ID (for backward compatibility)';

-- Create a view for easy querying of entries with their tests
CREATE OR REPLACE VIEW entries_with_tests AS
SELECT 
    e.id as entry_id,
    e.server_id,
    e.patient_id,
    e.doctor_id,
    e.entry_date,
    e.status as entry_status,
    e.added_by,
    e.price as entry_total_price,
    e.discount_amount as entry_discount,
    e.total_price as entry_final_price,
    e.grouped,
    e.tests_count,
    e.test_ids,
    e.test_names,
    e.created_at as entry_created_at,
    e.updated_at as entry_updated_at,
    
    -- Patient information
    p.name as patient_name,
    p.uhid as patient_uhid,
    p.mobile as patient_mobile,
    p.age as patient_age,
    p.sex as patient_sex,
    
    -- Doctor information
    d.name as doctor_name,
    d.qualification as doctor_qualification,
    d.specialization as doctor_specialization,
    d.hospital as doctor_hospital,
    
    -- User information
    u.username as added_by_username,
    u.full_name as added_by_full_name,
    
    -- Test details (for single test entries)
    t.name as primary_test_name,
    t.category_id as primary_test_category,
    t.price as primary_test_price,
    t.unit as primary_test_unit,
    
    -- Aggregated test information (for multiple test entries)
    GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as all_test_ids,
    GROUP_CONCAT(DISTINCT t2.name ORDER BY et.test_id SEPARATOR ', ') as all_test_names,
    GROUP_CONCAT(DISTINCT et.status ORDER BY et.test_id SEPARATOR ', ') as all_test_statuses,
    SUM(et.price) as total_tests_price,
    SUM(et.discount_amount) as total_tests_discount,
    SUM(et.total_price) as total_tests_final_price,
    COUNT(et.id) as actual_tests_count
    
FROM entries e
LEFT JOIN patients p ON e.patient_id = p.id
LEFT JOIN doctors d ON e.doctor_id = d.id
LEFT JOIN users u ON e.added_by = u.id
LEFT JOIN tests t ON e.test_id = t.id
LEFT JOIN entry_tests et ON e.id = et.entry_id
LEFT JOIN tests t2 ON et.test_id = t2.id
GROUP BY e.id;

-- Create a view for individual test results within entries
CREATE OR REPLACE VIEW entry_test_results AS
SELECT 
    et.id as entry_test_id,
    et.entry_id,
    et.test_id,
    et.result_value,
    et.unit,
    et.remarks,
    et.status as test_status,
    et.price,
    et.discount_amount,
    et.total_price,
    et.created_at,
    et.updated_at,
    
    -- Entry information
    e.entry_date,
    e.status as entry_status,
    e.added_by,
    
    -- Patient information
    p.name as patient_name,
    p.uhid as patient_uhid,
    
    -- Doctor information
    d.name as doctor_name,
    
    -- Test information
    t.name as test_name,
    t.category_id,
    t.normal_value_male,
    t.normal_value_female,
    t.min_range_male,
    t.max_range_male,
    t.min_range_female,
    t.max_range_female,
    t.unit as test_unit,
    
    -- User information
    u.username as added_by_username
    
FROM entry_tests et
LEFT JOIN entries e ON et.entry_id = e.id
LEFT JOIN patients p ON e.patient_id = p.id
LEFT JOIN doctors d ON e.doctor_id = d.id
LEFT JOIN tests t ON et.test_id = t.id
LEFT JOIN users u ON e.added_by = u.id;

-- Insert sample data for testing multiple tests per entry
INSERT IGNORE INTO entry_tests (entry_id, test_id, result_value, unit, status, price, total_price) 
SELECT 
    e.id as entry_id,
    t.id as test_id,
    CASE 
        WHEN t.name LIKE '%Blood%' THEN '5.6'
        WHEN t.name LIKE '%Sugar%' THEN '95.5'
        WHEN t.name LIKE '%Count%' THEN '4.5'
        ELSE 'Normal'
    END as result_value,
    t.unit,
    'completed' as status,
    t.price,
    t.price as total_price
FROM entries e
CROSS JOIN tests t
WHERE e.id = 1 AND t.id IN (1, 2, 3)
LIMIT 3;

-- Update entries table to reflect multiple tests
UPDATE entries 
SET 
    grouped = 1,
    tests_count = (SELECT COUNT(*) FROM entry_tests WHERE entry_id = entries.id),
    test_ids = (SELECT GROUP_CONCAT(test_id ORDER BY test_id) FROM entry_tests WHERE entry_id = entries.id),
    test_names = (SELECT GROUP_CONCAT(t.name ORDER BY et.test_id SEPARATOR ', ') 
                  FROM entry_tests et 
                  JOIN tests t ON et.test_id = t.id 
                  WHERE et.entry_id = entries.id)
WHERE id IN (SELECT DISTINCT entry_id FROM entry_tests);

-- Create stored procedures for common operations

-- Procedure to add a test to an entry
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS AddTestToEntry(
    IN p_entry_id INT,
    IN p_test_id INT,
    IN p_result_value TEXT,
    IN p_unit VARCHAR(50),
    IN p_remarks TEXT,
    IN p_price DECIMAL(10,2),
    IN p_discount_amount DECIMAL(10,2)
)
BEGIN
    DECLARE v_total_price DECIMAL(10,2);
    DECLARE v_entry_tests_count INT;
    
    -- Calculate total price
    SET v_total_price = COALESCE(p_price, 0) - COALESCE(p_discount_amount, 0);
    
    -- Insert the test into the entry
    INSERT INTO entry_tests (entry_id, test_id, result_value, unit, remarks, price, discount_amount, total_price)
    VALUES (p_entry_id, p_test_id, p_result_value, p_unit, p_remarks, p_price, p_discount_amount, v_total_price)
    ON DUPLICATE KEY UPDATE
        result_value = VALUES(result_value),
        unit = VALUES(unit),
        remarks = VALUES(remarks),
        price = VALUES(price),
        discount_amount = VALUES(discount_amount),
        total_price = VALUES(total_price),
        updated_at = CURRENT_TIMESTAMP;
    
    -- Update the entry's test count and aggregated data
    SELECT COUNT(*) INTO v_entry_tests_count FROM entry_tests WHERE entry_id = p_entry_id;
    
    UPDATE entries 
    SET 
        grouped = CASE WHEN v_entry_tests_count > 1 THEN 1 ELSE 0 END,
        tests_count = v_entry_tests_count,
        test_ids = (SELECT GROUP_CONCAT(test_id ORDER BY test_id) FROM entry_tests WHERE entry_id = p_entry_id),
        test_names = (SELECT GROUP_CONCAT(t.name ORDER BY et.test_id SEPARATOR ', ') 
                      FROM entry_tests et 
                      JOIN tests t ON et.test_id = t.id 
                      WHERE et.entry_id = p_entry_id),
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_entry_id;
    
    SELECT 'Test added successfully' as message, v_entry_tests_count as tests_count;
END //
DELIMITER ;

-- Procedure to remove a test from an entry
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS RemoveTestFromEntry(
    IN p_entry_id INT,
    IN p_test_id INT
)
BEGIN
    DECLARE v_entry_tests_count INT;
    
    -- Remove the test from the entry
    DELETE FROM entry_tests WHERE entry_id = p_entry_id AND test_id = p_test_id;
    
    -- Update the entry's test count and aggregated data
    SELECT COUNT(*) INTO v_entry_tests_count FROM entry_tests WHERE entry_id = p_entry_id;
    
    IF v_entry_tests_count = 0 THEN
        -- If no tests left, delete the entry
        DELETE FROM entries WHERE id = p_entry_id;
        SELECT 'Entry deleted (no tests remaining)' as message;
    ELSE
        -- Update the entry's aggregated data
        UPDATE entries 
        SET 
            grouped = CASE WHEN v_entry_tests_count > 1 THEN 1 ELSE 0 END,
            tests_count = v_entry_tests_count,
            test_ids = (SELECT GROUP_CONCAT(test_id ORDER BY test_id) FROM entry_tests WHERE entry_id = p_entry_id),
            test_names = (SELECT GROUP_CONCAT(t.name ORDER BY et.test_id SEPARATOR ', ') 
                          FROM entry_tests et 
                          JOIN tests t ON et.test_id = t.id 
                          WHERE et.entry_id = p_entry_id),
            updated_at = CURRENT_TIMESTAMP
        WHERE id = p_entry_id;
        
        SELECT 'Test removed successfully' as message, v_entry_tests_count as tests_count;
    END IF;
END //
DELIMITER ;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_entries_grouped ON entries(grouped);
CREATE INDEX IF NOT EXISTS idx_entries_tests_count ON entries(tests_count);
CREATE INDEX IF NOT EXISTS idx_entry_tests_entry_status ON entry_tests(entry_id, status);

-- Add comments to tables
ALTER TABLE entries COMMENT = 'Main entries table - can contain single or multiple tests';
ALTER TABLE entry_tests COMMENT = 'Junction table for many-to-many relationship between entries and tests';

-- Sample queries for testing:

-- Get all entries with their tests
-- SELECT * FROM entries_with_tests ORDER BY entry_created_at DESC;

-- Get individual test results for an entry
-- SELECT * FROM entry_test_results WHERE entry_id = 1 ORDER BY test_name;

-- Add a test to an entry
-- CALL AddTestToEntry(1, 2, 'Normal', 'mg/dL', 'Within normal range', 150.00, 10.00);

-- Remove a test from an entry
-- CALL RemoveTestFromEntry(1, 2);