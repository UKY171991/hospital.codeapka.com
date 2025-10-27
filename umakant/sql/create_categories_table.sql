-- Create categories table for test category management
-- This script creates the categories table with proper constraints and indexes

CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    INDEX idx_categories_name (name),
    INDEX idx_categories_active (is_active),
    INDEX idx_categories_created_by (created_by),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert some initial categories for common medical tests
INSERT INTO categories (name, description, is_active, created_by) VALUES
('Hematology', 'Blood-related tests including CBC, blood counts, and coagulation studies', TRUE, 1),
('Biochemistry', 'Chemical analysis tests including glucose, lipids, liver function, kidney function', TRUE, 1),
('Microbiology', 'Tests for detecting bacteria, viruses, fungi and other microorganisms', TRUE, 1),
('Immunology', 'Tests related to immune system function and antibody detection', TRUE, 1),
('Endocrinology', 'Hormone-related tests including thyroid, diabetes, reproductive hormones', TRUE, 1),
('Cardiology', 'Heart-related diagnostic tests and cardiac markers', TRUE, 1),
('Urology', 'Urine analysis and kidney function tests', TRUE, 1),
('Radiology', 'Imaging tests and radiological examinations', TRUE, 1),
('Pathology', 'Tissue and cellular examination tests', TRUE, 1),
('General', 'General medical tests that do not fit into specific categories', TRUE, 1);

-- Create a view for easy category management with test counts
CREATE OR REPLACE VIEW category_summary AS
SELECT 
    c.id,
    c.name,
    c.description,
    c.is_active,
    c.created_at,
    c.updated_at,
    c.created_by,
    u.username as created_by_username,
    u.full_name as created_by_name,
    COALESCE(test_counts.test_count, 0) as test_count
FROM categories c
LEFT JOIN users u ON c.created_by = u.id
LEFT JOIN (
    SELECT category_id, COUNT(*) as test_count 
    FROM tests 
    WHERE category_id IS NOT NULL 
    GROUP BY category_id
) test_counts ON c.id = test_counts.category_id
ORDER BY c.name;