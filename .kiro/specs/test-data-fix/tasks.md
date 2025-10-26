# Implementation Plan

- [ ] 1. Create dedicated database query functions for test data retrieval
  - Create new helper functions in entry_api_fixed.php for standardized test data queries
  - Implement getEntryTestsData() function with proper JOIN logic
  - Add validateTestDataIntegrity() function for data consistency checks
  - _Requirements: 1.1, 1.3, 2.1, 2.3_

- [ ] 2. Fix the entry API get action test data retrieval
  - [ ] 2.1 Simplify and fix the SQL query in the get action
    - Replace complex JOIN logic with dedicated function calls
    - Ensure proper relationship between entry_tests and tests tables
    - Add comprehensive logging of SQL queries and parameters
    - _Requirements: 1.1, 2.1, 3.1_

  - [ ] 2.2 Implement robust test data formatting
    - Create formatTestsForFrontend() function to standardize test data structure
    - Ensure all required fields are present in the response
    - Handle missing or null values gracefully
    - _Requirements: 1.2, 1.4, 2.4_

  - [ ] 2.3 Add comprehensive error handling and logging
    - Log each step of the test data retrieval process
    - Add detailed error messages for debugging
    - Include debug information in API responses when needed
    - _Requirements: 3.1, 3.2, 3.4, 3.5_

- [ ] 3. Fix the test data aggregation logic
  - [ ] 3.1 Repair build_entry_tests_aggregation_sql() function
    - Fix GROUP BY clauses and JOIN conditions
    - Ensure proper handling of test categories and names
    - Add validation of aggregated results
    - _Requirements: 2.2, 2.3_

  - [ ] 3.2 Update refresh_entry_aggregates() function
    - Ensure aggregated data matches individual test records
    - Add logging of aggregation process
    - Handle edge cases like entries with no tests
    - _Requirements: 2.2, 2.5_

- [ ] 4. Update frontend JavaScript to handle corrected data structure
  - [ ] 4.1 Fix addTestRow() function in entry-list.js
    - Update test data population logic to handle new API response format
    - Improve error handling when test data is missing
    - Add client-side validation of test data structure
    - _Requirements: 1.2, 1.4_

  - [ ] 4.2 Enhance test data display and debugging
    - Add better console logging for test data debugging
    - Improve error messages when test data is inconsistent
    - Add fallback handling for missing test information
    - _Requirements: 3.3, 3.5_

- [ ] 5. Add comprehensive testing and validation
  - [ ] 5.1 Create unit tests for database query functions
    - Test getEntryTestsData() with various entry configurations
    - Test data validation functions with corrupted data
    - Test aggregation functions with edge cases
    - _Requirements: 2.1, 2.2, 2.3_

  - [ ] 5.2 Add integration tests for API endpoints
    - Test complete data flow from database to frontend
    - Validate API responses with real data scenarios
    - Test error handling with various failure conditions
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 6. Implement data consistency validation and cleanup
  - [ ] 6.1 Add data integrity validation functions
    - Create functions to detect orphaned entry_tests records
    - Implement validation of foreign key relationships
    - Add automated data consistency checks
    - _Requirements: 2.3, 2.5_

  - [ ] 6.2 Enhance existing cleanup and debug endpoints
    - Improve cleanup_duplicates action to handle more edge cases
    - Add comprehensive debug endpoints for troubleshooting
    - Implement data repair functions for common issues
    - _Requirements: 2.5, 3.4, 3.5_