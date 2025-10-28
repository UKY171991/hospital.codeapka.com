# Requirements Document

## Introduction

This feature addresses the critical issue in the hospital test entry system where test category selection and test filtering are not working correctly in the edit modal. Currently, when users select a category, the corresponding tests are not properly filtered, and during edit mode, the category-test relationships are not maintained correctly, leading to confusion and potential data integrity issues.

## Glossary

- **Entry_System**: The hospital test entry management system
- **Edit_Modal**: The modal dialog used for editing existing test entries
- **Category_Dropdown**: The dropdown selector for test categories in each test row
- **Test_Dropdown**: The dropdown selector for individual tests in each test row
- **Category_Filter**: The global category filter that affects all test rows
- **Test_Row**: Individual row containing category selector, test selector, and result fields

## Requirements

### Requirement 1

**User Story:** As a hospital staff member, I want the test dropdown to automatically filter and show only tests belonging to the selected category, so that I can quickly find the relevant tests without scrolling through unrelated options.

#### Acceptance Criteria

1. WHEN a category is selected in the Category_Dropdown, THE Entry_System SHALL filter the Test_Dropdown to show only tests belonging to that category
2. WHEN no category is selected in the Category_Dropdown, THE Entry_System SHALL show all available tests in the Test_Dropdown
3. WHEN a category selection changes in the Category_Dropdown, THE Entry_System SHALL immediately update the Test_Dropdown options
4. WHEN a test is selected that belongs to a different category, THE Entry_System SHALL automatically update the Category_Dropdown to match the test's category
5. WHEN the Category_Dropdown is populated, THE Entry_System SHALL group categories by main category for better organization

### Requirement 2

**User Story:** As a hospital staff member, I want the edit modal to correctly display and maintain the category-test relationships from the original entry, so that I can see the accurate data and make informed edits.

#### Acceptance Criteria

1. WHEN an entry is opened for editing, THE Entry_System SHALL populate each Test_Row with the correct category corresponding to each test
2. WHEN an entry is opened for editing, THE Entry_System SHALL ensure the Category_Dropdown shows the test's actual category, not a stored entry category
3. WHEN test data is loaded for editing, THE Entry_System SHALL use the test's current category information from the database
4. WHEN a test's category cannot be determined, THE Entry_System SHALL provide a clear indication and fallback behavior
5. WHEN category data is missing or unavailable, THE Entry_System SHALL disable category filtering gracefully

### Requirement 3

**User Story:** As a hospital staff member, I want consistent behavior between the global category filter and individual row category selectors, so that the interface behaves predictably and I can work efficiently.

#### Acceptance Criteria

1. WHEN the global Category_Filter is applied, THE Entry_System SHALL update all Test_Row dropdowns to show only tests from the selected category
2. WHEN the global Category_Filter is cleared, THE Entry_System SHALL restore all tests in all Test_Row dropdowns
3. WHEN a Test_Row category is selected, THE Entry_System SHALL not affect other Test_Row category selections
4. WHEN adding a new Test_Row, THE Entry_System SHALL respect the current global Category_Filter setting
5. WHEN the global Category_Filter is active, THE Entry_System SHALL indicate which filter is applied

### Requirement 4

**User Story:** As a hospital staff member, I want clear error handling and recovery options when category or test data fails to load, so that I can continue working even when there are temporary system issues.

#### Acceptance Criteria

1. WHEN category data fails to load, THE Entry_System SHALL display a clear error message and disable category filtering
2. WHEN test data fails to load, THE Entry_System SHALL provide a retry option and fallback functionality
3. WHEN a test's category information is missing, THE Entry_System SHALL place the test in an "Uncategorized" group
4. WHEN category filtering encounters an error, THE Entry_System SHALL automatically fall back to showing all tests
5. WHEN data loading fails, THE Entry_System SHALL log detailed error information for debugging purposes

### Requirement 5

**User Story:** As a hospital staff member, I want the category and test selection to work smoothly with good performance, so that I can work efficiently without delays or interface lag.

#### Acceptance Criteria

1. WHEN filtering tests by category, THE Entry_System SHALL complete the filtering within 200 milliseconds
2. WHEN populating category dropdowns, THE Entry_System SHALL cache category data to avoid repeated API calls
3. WHEN updating multiple Test_Row dropdowns, THE Entry_System SHALL batch DOM updates for optimal performance
4. WHEN loading edit data, THE Entry_System SHALL minimize the number of API requests required
5. WHEN category or test data changes, THE Entry_System SHALL update only the affected UI elements