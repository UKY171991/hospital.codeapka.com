# Patient Management System Fix - Implementation Plan

- [x] 1. Fix API path and database column mapping issues


  - Create proper API endpoint routing to resolve path mismatch between frontend calls and actual API location
  - Implement gender to sex column mapping in both save and retrieve operations
  - Update API to handle all database columns including the unused contact field
  - _Requirements: 5.1, 5.2, 5.3, 5.5_



- [ ] 1.1 Create API endpoint symlink or redirect
  - Create ajax/patient_api.php file that properly routes to patho_api/patient.php
  - Ensure all API calls from frontend reach the correct backend endpoint


  - _Requirements: 5.5_

- [ ] 1.2 Fix gender to sex column mapping in API
  - Modify handleSave function to map gender field to sex database column


  - Update handleGet and handleList functions to map sex column back to gender for frontend
  - Ensure consistent data mapping throughout all CRUD operations
  - _Requirements: 5.1, 5.3_



- [ ] 1.3 Implement complete column handling
  - Add contact field to allowed_fields array in API configuration
  - Update frontend form to include contact field for secondary contact information
  - Ensure all database columns are properly handled in save and retrieve operations


  - _Requirements: 5.2, 5.4_

- [ ] 2. Fix frontend form validation and data handling
  - Implement proper client-side validation for required fields


  - Fix form population and submission to handle all patient data fields correctly
  - Update modal forms to display and edit all available patient information
  - _Requirements: 1.1, 1.4, 2.1, 2.5_



- [ ] 2.1 Enhance patient form with missing fields
  - Add contact field to the patient add/edit modal form
  - Implement proper form validation for name and mobile required fields


  - Update form reset and population functions to handle all fields
  - _Requirements: 1.1, 1.4, 2.1_

- [ ] 2.2 Fix form submission and data processing
  - Update form submission handler to include all form fields in API request

  - Implement proper error handling and user feedback for form operations
  - Fix gender field handling to work correctly with database sex column
  - _Requirements: 1.2, 1.3, 2.2, 2.3_

- [x] 2.3 Improve UHID generation and validation

  - Enhance UHID auto-generation to ensure uniqueness
  - Implement UHID preservation during edit operations
  - Add validation to prevent duplicate UHID values
  - _Requirements: 1.5, 2.4_


- [ ] 3. Enhance CRUD operations and error handling
  - Implement robust delete operation with dependency checking
  - Add comprehensive error handling for all CRUD operations
  - Improve data validation and constraint handling

  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [ ] 3.1 Implement safe delete operation
  - Add dependency checking before allowing patient deletion
  - Implement confirmation dialog with clear warning messages
  - Handle delete operation errors and provide appropriate user feedback

  - _Requirements: 3.1, 3.2, 3.3, 3.4_

- [ ] 3.2 Add comprehensive data validation
  - Implement server-side validation for all patient data fields
  - Add data type validation and constraint checking

  - Ensure data integrity across all CRUD operations
  - _Requirements: 5.4, 1.4, 2.5_

- [x] 3.3 Write unit tests for API operations

  - Create unit tests for each CRUD operation endpoint
  - Test data validation and error handling scenarios
  - Verify gender to sex column mapping functionality
  - _Requirements: 1.1, 1.2, 2.2, 3.4_

- [x] 4. Improve patient viewing and display functionality

  - Fix patient details modal to show all available information
  - Implement proper data formatting and display
  - Add print functionality for patient details
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_


- [ ] 4.1 Update patient details view modal
  - Modify renderPatientDetails function to display all patient fields including contact
  - Fix data formatting for age, gender, and other fields
  - Ensure proper handling of null or missing field values
  - _Requirements: 4.1, 4.2, 4.3, 4.5_


- [ ] 4.2 Implement print functionality
  - Add print button functionality to patient details modal
  - Create printable format for patient information


  - Ensure proper styling and layout for printed output
  - _Requirements: 4.4_

- [ ] 5. Enhance search and filtering capabilities
  - Improve DataTables search functionality across all relevant fields
  - Fix user filtering and ensure proper data loading

  - Add export functionality for patient data
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

- [ ] 5.1 Fix DataTables search and filtering
  - Update server-side search to include all searchable fields (name, uhid, mobile, contact)

  - Fix added_by filter functionality to work correctly with user selection
  - Ensure proper pagination and data loading for large datasets
  - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [x] 5.2 Implement export functionality

  - Fix CSV export functionality using DataTables buttons
  - Ensure exported data includes all relevant patient fields
  - Add proper formatting for exported data
  - _Requirements: 6.5_

- [ ] 5.3 Add integration tests for search and filter
  - Create tests for search functionality across multiple fields
  - Test filtering by added_by user
  - Verify export functionality works correctly
  - _Requirements: 6.1, 6.2, 6.5_

- [ ] 6. Final integration and testing
  - Test complete patient lifecycle (add, edit, view, delete)
  - Verify all database columns are handled correctly
  - Ensure proper error handling and user feedback throughout the system
  - _Requirements: All requirements_

- [ ] 6.1 Perform end-to-end testing
  - Test complete patient management workflow from add to delete
  - Verify all form fields save and display correctly
  - Test error scenarios and edge cases
  - _Requirements: All requirements_

- [ ] 6.2 Validate data consistency
  - Verify gender to sex mapping works in both directions
  - Ensure all patient fields are preserved during edit operations
  - Test data integrity across all CRUD operations
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ] 6.3 Performance and security testing
  - Test system performance with large patient datasets
  - Verify input validation and SQL injection prevention
  - Test authentication and authorization for all operations
  - _Requirements: 3.5, 5.4_