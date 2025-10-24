# Implementation Plan

- [ ] 1. Fix DataTables server-side processing configuration
  - Update doctors.php DataTables initialization to explicitly enable server-side processing
  - Ensure proper column configuration and AJAX settings
  - Verify proper parameter transmission to server
  - _Requirements: 1.1, 1.2, 1.3_

- [ ] 2. Fix server-side API parameter handling
  - [ ] 2.1 Fix DataTables parameter extraction in doctor_api.php
    - Properly extract draw, start, length parameters from $_POST
    - Handle search parameters correctly
    - Implement proper parameter validation and defaults
    - _Requirements: 1.1, 2.1, 2.3_
  
  - [ ] 2.2 Implement proper search functionality
    - Add search across doctor name, hospital, contact_no, and added_by_username columns
    - Use proper SQL LIKE queries with parameter binding
    - Handle empty search terms correctly
    - _Requirements: 3.1, 3.2, 3.3_

- [ ] 3. Fix SQL query structure and pagination
  - [ ] 3.1 Implement proper base query with JOINs
    - Create optimized base query joining doctors and users tables
    - Ensure proper column selection and aliasing
    - _Requirements: 2.1, 2.3_
  
  - [ ] 3.2 Implement correct record counting
    - Create separate queries for total records and filtered records
    - Ensure count queries match the data query conditions
    - Optimize count queries for performance
    - _Requirements: 1.5, 3.5_
  
  - [ ] 3.3 Fix pagination with LIMIT and ORDER BY
    - Implement proper LIMIT with start and length parameters
    - Add consistent ORDER BY clause for predictable pagination
    - Handle edge cases for last page and empty results
    - _Requirements: 1.1, 1.3, 1.4_

- [ ] 4. Fix API response format
  - [ ] 4.1 Ensure proper DataTables response structure
    - Return all required fields: draw, recordsTotal, recordsFiltered, data
    - Maintain backward compatibility with existing success field
    - Handle error responses properly
    - _Requirements: 1.1, 1.5, 2.1_
  
  - [ ] 4.2 Integrate filter functionality with pagination
    - Ensure "Added By" filter works with server-side processing
    - Apply filter conditions to both data and count queries
    - Maintain filter state during pagination navigation
    - _Requirements: 2.4, 2.5_

- [ ] 5. Add error handling and validation
  - Add comprehensive error handling for database operations
  - Implement parameter validation with appropriate defaults
  - Ensure graceful degradation on errors
  - _Requirements: 2.1, 2.2_

- [ ] 6. Performance optimization
  - Add database indexes if needed for pagination performance
  - Optimize queries for large datasets
  - Implement query result caching where appropriate
  - _Requirements: 2.1, 2.2_