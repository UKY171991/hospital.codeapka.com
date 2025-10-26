# Implementation Plan

- [x] 1. Backup and analyze current form structure


  - Create backup of current entry-list.php file
  - Document current HTML section positions and line numbers
  - Identify the exact Patient Information Section HTML block to be moved
  - _Requirements: 1.2, 1.3_



- [ ] 2. Reorganize entry form modal HTML structure
  - Locate the Patient Information Section in the modal body
  - Cut the Patient Information Section HTML block from its current position
  - Paste the Patient Information Section immediately after the basic selection fields row

  - Ensure proper indentation and HTML structure is maintained
  - _Requirements: 1.1, 1.5, 2.1_

- [ ] 3. Verify form layout and styling
  - Test that the Patient Information Section displays correctly in its new position
  - Confirm Bootstrap card styling is preserved


  - Verify responsive behavior on different screen sizes
  - Check that section spacing and visual hierarchy remain consistent
  - _Requirements: 1.4, 2.2, 2.3, 2.4_

- [x] 4. Add "Add New Patient" option to patient dropdown


  - Add "Add New Patient" option to the patient selection dropdown
  - Implement JavaScript logic to detect when "Add New Patient" is selected
  - Create function to toggle patient information fields between read-only and editable states
  - Add visual indicators (styling) to show when fields are editable vs read-only
  - _Requirements: 3.2, 3.3, 4.5_



- [ ] 5. Implement patient data mode switching functionality
  - Create JavaScript function to handle switching between selected patient and new patient modes
  - Implement logic to clear patient information fields when switching to "Add New Patient"
  - Implement logic to populate and lock fields when selecting existing patient
  - Add validation to ensure either patient is selected OR all required patient fields are filled manually
  - Track patient data source (selected vs manual) for proper form submission
  - _Requirements: 3.4, 4.1, 4.2, 4.3, 4.4_


- [ ] 6. Test enhanced patient selection functionality
  - Test selecting existing patient from dropdown and verify auto-population of patient fields
  - Test selecting "Add New Patient" option and verify fields become editable
  - Test switching between selected patient and "Add New Patient" modes
  - Test form validation with selected patient data vs manually entered patient data
  - Verify visual indicators clearly show field editability status
  - Test that patient data source is properly tracked for form submission

  - _Requirements: 3.1, 3.3, 3.7, 4.1, 4.2, 4.3, 4.5_

- [ ] 7. Test complete form functionality and data flow
  - Test opening the entry form modal (Add Entry button)
  - Verify all form fields are accessible and functional in new layout
  - Test complete form submission with selected patient data
  - Test complete form submission with manually entered patient data
  - Test entry editing functionality with the new layout and enhanced patient selection
  - Verify data persistence and proper saving of both patient selection modes
  - _Requirements: 1.2, 1.3, 3.6_

- [ ] 8. Cross-browser compatibility testing
  - Test form layout in Chrome, Firefox, Safari, and Edge browsers
  - Verify consistent appearance and functionality across browsers
  - Document any browser-specific issues if found
  - _Requirements: 1.4, 2.2_