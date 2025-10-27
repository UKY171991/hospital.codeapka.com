# Implementation Plan

- [x] 1. Implement core demographic range calculation logic


  - Add `calculateAppropriateRanges()` method to EntryManager class
  - Implement age threshold logic (18 years for child vs adult)
  - Add gender-specific range selection for adult patients
  - Add fallback logic to general ranges when demographic ranges unavailable
  - _Requirements: 1.1, 1.2, 1.3, 2.1, 2.2, 2.4, 5.1, 5.2, 5.3_

- [x] 2. Create range display management system

  - Add `updateRangeDisplay()` method to update UI elements with selected ranges
  - Implement `getRangeTypeBadgeClass()` for visual range type indicators
  - Create range type indicator badges (Child, Male, Female, General)
  - Add tooltips to explain which range type is being used
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [x] 3. Enhance patient selection change handling

  - Extend existing `onPatientChange()` method to trigger range updates
  - Add `updateAllTestRangesForCurrentPatient()` method
  - Add `resetAllTestRangesToGeneral()` method for when no patient selected
  - Ensure range updates happen after patient details are fully loaded
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 4. Enhance test selection change handling

  - Extend existing `onTestChange()` method to apply demographic ranges
  - Integrate demographic range calculation into test selection flow
  - Ensure ranges update when tests are added or changed
  - Preserve existing test result values during range updates
  - _Requirements: 6.1, 6.2, 3.2_

- [x] 5. Add comprehensive error handling and edge cases

  - Handle missing patient demographic data gracefully
  - Implement fallback logic for incomplete range data
  - Add validation for age and gender values
  - Ensure system continues working when demographic ranges are unavailable
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [x] 6. Integrate with edit mode functionality


  - Ensure demographic ranges work correctly in edit mode
  - Update `populateEditForm()` to apply current demographic ranges
  - Ensure view mode shows appropriate ranges for current patient demographics
  - Test range updates when editing existing entries
  - _Requirements: 6.2, 6.3, 6.4_

- [x] 7. Add performance optimizations


  - Implement debouncing for rapid patient selection changes
  - Add caching for calculated ranges to avoid recalculation
  - Optimize batch updates for multiple test rows
  - Ensure range updates complete within 100ms performance requirement
  - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

- [ ] 8. Add comprehensive unit tests
  - Write tests for `calculateAppropriateRanges()` function
  - Test child range selection logic
  - Test adult male and female range selection
  - Test fallback to general ranges
  - Test edge cases with missing demographic data
  - _Requirements: All requirements validation_

- [ ] 9. Add integration tests
  - Test complete patient selection to range update flow
  - Test test addition with demographic range application
  - Test edit mode range display functionality
  - Test multiple test scenarios with different range availability
  - Test error scenarios and recovery
  - _Requirements: All requirements end-to-end validation_

- [x] 10. Update CSS styling for range indicators

  - Add styles for range type badges (child, male, female, general)
  - Ensure badges are visually distinct and accessible
  - Add hover effects and tooltips for range indicators
  - Ensure responsive design works with new indicators
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

- [x] 11. Verify database schema compatibility


  - Confirm all demographic range fields are available in test data
  - Verify test API returns all required demographic range fields
  - Test with actual database data to ensure field availability
  - Document any missing fields that need to be added
  - _Requirements: 1.1, 2.1, 2.2, 5.1_

- [x] 12. Final integration and testing



  - Test complete workflow with child patients (under 18)
  - Test complete workflow with adult male patients
  - Test complete workflow with adult female patients
  - Test edge cases with missing or invalid demographic data
  - Verify performance meets requirements under normal load
  - _Requirements: All requirements comprehensive validation_