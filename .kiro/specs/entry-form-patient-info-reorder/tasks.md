# Implementation Plan

- [ ] 1. Backup and analyze current form structure
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

- [ ] 4. Test form functionality and data flow
  - Test opening the entry form modal (Add Entry button)
  - Verify all form fields are accessible and functional
  - Test patient selection auto-population of patient information fields
  - Confirm form submission works correctly with reordered sections
  - Test entry editing functionality with the new layout
  - _Requirements: 1.2, 1.3_

- [ ] 5. Cross-browser compatibility testing
  - Test form layout in Chrome, Firefox, Safari, and Edge browsers
  - Verify consistent appearance and functionality across browsers
  - Document any browser-specific issues if found
  - _Requirements: 1.4, 2.2_