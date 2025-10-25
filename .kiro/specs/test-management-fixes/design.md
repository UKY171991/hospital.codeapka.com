# Design Document

## Overview

The test management system has several critical issues related to category loading, form population, data display, and table presentation. This design addresses these issues by fixing the JavaScript category loading logic, improving the edit modal data binding, enhancing the view modal data display, and ensuring the table shows all important columns.

## Architecture

The solution involves fixing both frontend JavaScript logic and backend API responses to ensure proper data flow:

1. **Frontend Fixes**: Improve category loading, form population, and data display
2. **Backend Verification**: Ensure APIs return complete data with proper joins
3. **Data Flow**: Fix the cascade loading of main categories → test categories
4. **UI Enhancement**: Improve table columns and modal displays

## Components and Interfaces

### 1. Category Loading System

**Problem**: Main categories and test categories are not loading properly in dropdowns.

**Solution**:
- Fix the `loadCategories()` function to properly handle main category loading
- Improve the `loadTestCategoriesByMain()` function to correctly filter by main_category_id
- Add proper error handling and loading states
- Ensure the category filter dropdown gets populated with all available categories

**Key Changes**:
- Verify API endpoints are working correctly
- Fix the main category to test category relationship loading
- Add debugging information to identify loading failures
- Implement proper async loading with callbacks

### 2. Edit Modal Form Population

**Problem**: Edit modal select fields are not showing correct selected values.

**Solution**:
- Fix the `editTest()` function to properly load categories before populating form
- Ensure `loadMainCategoriesForEdit()` and `loadTestCategoriesByMainForEdit()` work correctly
- Add proper sequencing to load main categories first, then test categories
- Implement value setting after categories are loaded

**Key Changes**:
- Load categories synchronously before setting values
- Add proper callbacks to ensure categories are loaded before setting selections
- Fix the timing issue where values are set before options are available
- Add verification to ensure correct values are selected

### 3. View Modal Data Display

**Problem**: View modal is missing field data and not showing complete information.

**Solution**:
- Enhance the `viewTest()` function to display all relevant fields
- Add missing fields like main_category_name, test_code, specimen, method
- Improve the reference ranges display to show all range types
- Add proper formatting for dates, prices, and boolean values

**Key Changes**:
- Include all test fields in the view modal HTML generation
- Add proper null/empty value handling
- Enhance the reference ranges table to show all range types
- Add metadata section with creation/modification information

### 4. Table Column Enhancement

**Problem**: Table is not showing all important columns.

**Solution**:
- Modify the `renderTable()` function to include all essential columns
- Add proper category display with both main category and test category
- Ensure price formatting is consistent
- Add proper action buttons for all operations

**Key Changes**:
- Include main_category_name in the category column display
- Add proper badge styling for category hierarchy
- Ensure all test data is displayed in the table
- Fix any missing column data issues

## Data Models

### Test Record Structure
```javascript
{
  id: number,
  name: string,
  category_id: number,
  main_category_id: number,
  category_name: string,
  main_category_name: string,
  price: decimal,
  unit: string,
  specimen: string,
  method: string,
  test_code: string,
  description: text,
  reference_range: string,
  min: decimal,
  max: decimal,
  min_male: decimal,
  max_male: decimal,
  min_female: decimal,
  max_female: decimal,
  min_child: decimal,
  max_child: decimal,
  child_unit: string,
  sub_heading: boolean,
  print_new_page: boolean,
  added_by: number,
  added_by_username: string,
  created_at: datetime,
  updated_at: datetime
}
```

### Category Relationship
```javascript
// Main Category
{
  id: number,
  name: string,
  description: string
}

// Test Category (linked to main category)
{
  id: number,
  name: string,
  main_category_id: number,
  description: string
}
```

## Error Handling

### Category Loading Errors
- Add proper error messages when categories fail to load
- Implement fallback options when API calls fail
- Add retry mechanisms for failed category loads
- Display user-friendly error messages

### Form Validation Errors
- Ensure required field validation works properly
- Add proper error display for invalid category selections
- Implement client-side validation before form submission

### Data Display Errors
- Handle null/empty values gracefully in view modal
- Add proper fallback text for missing data
- Ensure table renders correctly even with incomplete data

## Testing Strategy

### Unit Testing Focus
- Test category loading functions individually
- Verify form population with various data scenarios
- Test view modal rendering with complete and incomplete data
- Validate table rendering with different data sets

### Integration Testing
- Test the complete flow from category selection to test creation
- Verify edit functionality works end-to-end
- Test view modal with real database data
- Validate table display with actual test records

### User Acceptance Testing
- Verify categories load correctly on page load
- Test edit modal shows correct selected values
- Confirm view modal displays all relevant information
- Validate table shows all important columns clearly