# Design Document

## Overview

The test data retrieval issue stems from inconsistent JOIN operations and aggregation logic in the entry API. The system needs to be redesigned to ensure proper data flow from the entry_tests table through to the frontend display, with robust error handling and logging.

## Architecture

### Current Issues Identified

1. **Inconsistent Test Data Retrieval**: The `get` action in entry_api_fixed.php has complex JOIN logic that may not be retrieving the correct test data
2. **Aggregation Problems**: The `build_entry_tests_aggregation_sql()` function may be producing incorrect results
3. **Frontend Data Handling**: The JavaScript code expects specific data structure that may not match the API response
4. **Database Schema Variations**: The system tries to handle multiple schema variations which adds complexity

### Proposed Solution Architecture

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Frontend JS   │───▶│   Entry API      │───▶│   Database      │
│   entry-list.js │    │   entry_api.php  │    │   entry_tests   │
└─────────────────┘    └──────────────────┘    └─────────────────┘
        │                       │                       │
        │                       ▼                       │
        │              ┌──────────────────┐             │
        └──────────────│  Data Validator  │◀────────────┘
                       │  & Logger        │
                       └──────────────────┘
```

## Components and Interfaces

### 1. Database Query Layer

**Purpose**: Standardize and simplify database queries for test data retrieval

**Key Functions**:
- `getEntryTestsData($pdo, $entryId)`: Retrieve all tests for a specific entry
- `validateTestDataIntegrity($pdo, $entryId)`: Check for data consistency issues
- `getTestDetailsWithCategories($pdo, $testIds)`: Get complete test information

**Interface**:
```php
function getEntryTestsData($pdo, $entryId) {
    // Returns: array of test objects with all required fields
    // Fields: test_id, test_name, category_name, result_value, unit, min, max, price, etc.
}
```

### 2. Data Validation Layer

**Purpose**: Ensure data consistency and provide detailed logging

**Key Functions**:
- `validateEntryTestsConsistency($entryData, $testsData)`: Cross-validate entry and test data
- `logDataDiscrepancies($entryId, $issues)`: Log any data inconsistencies found
- `sanitizeTestData($rawTestData)`: Clean and format test data

### 3. API Response Formatter

**Purpose**: Standardize API responses and ensure frontend compatibility

**Key Functions**:
- `formatEntryResponse($entryData, $testsData)`: Create consistent API response structure
- `formatTestsForFrontend($testsData)`: Format test data for JavaScript consumption
- `addDebugInformation($response, $debugData)`: Include debug info when needed

## Data Models

### Entry Test Data Model
```php
class EntryTestData {
    public $id;              // entry_tests.id
    public $entry_id;        // entry_tests.entry_id
    public $test_id;         // entry_tests.test_id
    public $test_name;       // tests.name
    public $category_id;     // tests.category_id
    public $category_name;   // categories.name
    public $result_value;    // entry_tests.result_value
    public $unit;           // tests.unit
    public $min;            // tests.min
    public $max;            // tests.max
    public $price;          // entry_tests.price
    public $discount_amount; // entry_tests.discount_amount
    public $status;         // entry_tests.status
}
```

### API Response Model
```php
class EntryApiResponse {
    public $success;        // boolean
    public $data;          // Entry data with embedded tests array
    public $debug_info;    // Optional debug information
    public $message;       // Success/error message
}
```

## Error Handling

### 1. Database Connection Issues
- Graceful fallback when database is unavailable
- Clear error messages for connection problems
- Retry logic for transient failures

### 2. Data Inconsistency Issues
- Detect missing test records
- Handle orphaned entry_tests records
- Validate foreign key relationships

### 3. API Response Issues
- Validate response structure before sending
- Include debug information in development mode
- Log all API errors with context

## Testing Strategy

### 1. Unit Tests
- Test individual query functions with known data sets
- Validate data formatting functions
- Test error handling scenarios

### 2. Integration Tests
- Test complete API endpoints with real database
- Validate frontend-backend data flow
- Test with various entry configurations (single test, multiple tests, no tests)

### 3. Data Validation Tests
- Test with corrupted data scenarios
- Validate handling of missing foreign key references
- Test aggregation logic with edge cases

## Implementation Approach

### Phase 1: Database Query Standardization
1. Create dedicated functions for test data retrieval
2. Implement comprehensive logging
3. Add data validation checks

### Phase 2: API Response Standardization
1. Standardize the `get` action response format
2. Ensure consistent test data structure
3. Add debug information capabilities

### Phase 3: Frontend Compatibility
1. Update JavaScript to handle new response format
2. Add client-side validation
3. Improve error handling in the UI

### Phase 4: Testing and Validation
1. Comprehensive testing with various data scenarios
2. Performance optimization
3. Documentation updates

## Specific Fixes Required

### 1. Entry API Get Action
- Simplify the JOIN logic in the SQL query
- Use dedicated function for test data retrieval
- Add comprehensive logging of each step

### 2. Test Data Aggregation
- Fix the `build_entry_tests_aggregation_sql()` function
- Ensure proper GROUP BY handling
- Add validation of aggregated results

### 3. Frontend Data Handling
- Update `addTestRow()` function to handle new data structure
- Improve error handling in test data population
- Add client-side validation of test data

### 4. Logging and Debugging
- Add structured logging throughout the data flow
- Include SQL query logging with parameters
- Add debug endpoints for troubleshooting