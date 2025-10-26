# Design Document

## Overview

The test display issue occurs because the test aggregation logic in the backend API is not properly collecting and formatting multiple tests for display in the frontend table. The system has the correct database structure with `entry_tests` table for storing multiple tests per entry, but the aggregation and display logic needs to be fixed.

## Architecture

The system follows a typical web application architecture:
- **Frontend**: JavaScript DataTable displaying entry list with test information
- **Backend API**: PHP script (`entry_api_fixed.php`) handling data retrieval and aggregation
- **Database**: MySQL with `entries` and `entry_tests` tables

## Root Cause Analysis

Based on code analysis, the issue stems from:

1. **Aggregation Logic**: The `build_entry_tests_aggregation_sql()` function may not be executing properly
2. **Data Flow**: Test data might not be flowing correctly from `entry_tests` to the aggregated fields
3. **Frontend Rendering**: The DataTable column configuration expects `test_names` and `tests_count` fields

## Components and Interfaces

### 1. Database Layer
- **entry_tests table**: Stores individual test records linked to entries
- **entries table**: Contains aggregated test information (`test_names`, `tests_count`)
- **tests table**: Master test definitions

### 2. Backend API Layer
- **entry_api_fixed.php**: Main API endpoint
- **build_entry_tests_aggregation_sql()**: Generates SQL for test aggregation
- **refresh_entry_aggregates()**: Updates aggregated data in entries table

### 3. Frontend Layer
- **entry-list.new.js**: DataTable configuration and rendering
- **Test column renderer**: Displays test count and names

## Data Models

### Entry Test Aggregation
```sql
SELECT et.entry_id,
       COUNT(*) as tests_count,
       GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') as test_names,
       GROUP_CONCAT(DISTINCT et.test_id ORDER BY et.test_id) as test_ids
FROM entry_tests et
LEFT JOIN tests t ON et.test_id = t.id
GROUP BY et.entry_id
```

### Frontend Data Structure
```javascript
{
    id: 123,
    patient_name: "John Doe",
    test_names: "Blood Test, X-Ray, ECG",
    tests_count: 3,
    // ... other fields
}
```

## Error Handling

### Database Errors
- Handle missing `entry_tests` table gracefully
- Provide fallback values when aggregation fails
- Log SQL errors for debugging

### Frontend Errors
- Display "No tests" when test data is missing
- Handle null/undefined test counts and names
- Provide user-friendly error messages

## Testing Strategy

### Backend Testing
1. **Database Verification**: Ensure `entry_tests` records are created correctly
2. **Aggregation Testing**: Verify GROUP_CONCAT produces correct results
3. **API Response Testing**: Confirm test data is included in API responses

### Frontend Testing
1. **DataTable Rendering**: Verify test column displays multiple tests
2. **Data Flow Testing**: Ensure backend data reaches frontend correctly
3. **User Interface Testing**: Confirm test information is readable and accurate

## Implementation Approach

### Phase 1: Diagnosis
1. Add comprehensive logging to identify where the data flow breaks
2. Verify database records in `entry_tests` table
3. Test aggregation SQL queries manually

### Phase 2: Backend Fix
1. Fix the test aggregation logic in `build_entry_tests_aggregation_sql()`
2. Ensure `refresh_entry_aggregates()` is called after saving tests
3. Verify API response includes correct test data

### Phase 3: Frontend Verification
1. Confirm DataTable receives proper test data
2. Test the rendering logic for multiple tests
3. Verify user interface displays tests correctly

### Phase 4: Testing and Validation
1. Create test entries with multiple tests
2. Verify the fix works across different scenarios
3. Ensure no regression in existing functionality

## Performance Considerations

- The GROUP_CONCAT operation should be efficient for typical test counts
- Consider indexing on `entry_tests.entry_id` for better aggregation performance
- Limit test name concatenation length to prevent display issues

## Security Considerations

- Ensure proper SQL parameter binding in aggregation queries
- Validate test data before aggregation
- Maintain existing authentication and authorization checks