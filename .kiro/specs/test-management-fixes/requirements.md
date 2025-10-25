# Requirements Document

## Introduction

This specification addresses critical issues in the hospital test management system where main categories and test categories are not displaying properly, edit modal select fields are not showing correct values, view modal is missing field data, and the table is not showing all important columns.

## Glossary

- **Test_Management_System**: The web application module for managing laboratory tests
- **Main_Category**: Primary classification of test categories (stored in main_test_categories table)
- **Test_Category**: Secondary classification of tests (stored in categories table with main_category_id reference)
- **Edit_Modal**: The popup form used to modify existing test records
- **View_Modal**: The popup display showing detailed test information
- **Test_Table**: The data table displaying all tests with their associated information

## Requirements

### Requirement 1

**User Story:** As a laboratory administrator, I want to see main categories and test categories properly loaded in dropdowns, so that I can correctly categorize tests.

#### Acceptance Criteria

1. WHEN the test management page loads, THE Test_Management_System SHALL populate the main category dropdown with all available main categories from main_test_categories table
2. WHEN a main category is selected, THE Test_Management_System SHALL load and display corresponding test categories from categories table where main_category_id matches the selected main category
3. WHEN the category filter is used, THE Test_Management_System SHALL populate the filter dropdown with all available test category names
4. IF no categories are found, THEN THE Test_Management_System SHALL display "No categories found" message in the dropdown

### Requirement 2

**User Story:** As a laboratory administrator, I want the edit modal to show the correct selected values for categories, so that I can accurately modify test information.

#### Acceptance Criteria

1. WHEN editing an existing test, THE Test_Management_System SHALL pre-select the correct main category in the main category dropdown
2. WHEN the main category is loaded for editing, THE Test_Management_System SHALL automatically load and pre-select the correct test category
3. WHEN the edit modal opens, THE Test_Management_System SHALL ensure all form fields display the current test values
4. IF a test has no category assigned, THEN THE Test_Management_System SHALL show empty selection options

### Requirement 3

**User Story:** As a laboratory administrator, I want the view modal to display all relevant test information, so that I can review complete test details.

#### Acceptance Criteria

1. WHEN viewing a test, THE Test_Management_System SHALL display the test name, main category name, and test category name
2. WHEN viewing a test, THE Test_Management_System SHALL show all reference ranges including general, male, female, and child ranges with their respective units
3. WHEN viewing a test, THE Test_Management_System SHALL display test metadata including price, unit, method, specimen, and description
4. WHEN viewing a test, THE Test_Management_System SHALL show creation and modification timestamps with user information

### Requirement 4

**User Story:** As a laboratory administrator, I want the test table to show all important columns, so that I can quickly identify and manage tests.

#### Acceptance Criteria

1. THE Test_Management_System SHALL display test ID, name, main category, test category, and price in the main table
2. THE Test_Management_System SHALL show category information as badges with main category and test category clearly distinguished
3. THE Test_Management_System SHALL include action buttons for view, edit, and delete operations for each test
4. WHEN no category is assigned to a test, THEN THE Test_Management_System SHALL display "No Category" placeholder text