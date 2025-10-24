# Design Document

## Overview

The pagination issue on the doctors.php page stems from improper server-side processing implementation in the DataTables configuration. The current implementation has inconsistencies in parameter handling and response formatting that prevent proper pagination functionality. This design addresses these issues by fixing the server-side API and ensuring proper DataTables configuration.

## Architecture

The solution maintains the existing three-tier architecture:

1. **Presentation Layer**: doctors.php with DataTables frontend
2. **API Layer**: ajax/doctor_api.php handling server-side processing
3. **Data Layer**: MySQL database with doctors table

The fix focuses on the API layer to properly handle DataTables server-side processing parameters and ensure consistent response formatting.

## Components and Interfaces

### 1. DataTables Configuration (Frontend)
- **Location**: doctors.php inline script
- **Responsibility**: Configure DataTables with proper server-side processing parameters
- **Key Changes**: 
  - Ensure `serverSide: true` is explicitly set
  - Verify proper column mapping
  - Configure proper AJAX data transmission

### 2. Server-side Processing API (Backend)
- **Location**: ajax/doctor_api.php
- **Responsibility**: Handle DataTables server-side processing requests
- **Key Changes**:
  - Fix parameter extraction for `draw`, `start`, `length`
  - Implement proper search functionality across relevant columns
  - Ensure correct response format with required DataTables fields
  - Fix SQL query construction for pagination and filtering

### 3. Database Query Optimization
- **Responsibility**: Efficient data retrieval with proper LIMIT and OFFSET
- **Key Changes**:
  - Optimize base query with proper JOINs
  - Implement efficient counting for total and filtered records
  - Ensure proper ORDER BY for consistent pagination

## Data Models

### DataTables Request Parameters
```javascript
{
  draw: integer,           // Draw counter for request tracking
  start: integer,          // Starting record index
  length: integer,         // Number of records to return
  search: {
    value: string,         // Global search term
    regex: boolean         // Whether search is regex
  },
  order: [{
    column: integer,       // Column index to sort by
    dir: string           // Sort direction (asc/desc)
  }],
  columns: [{
    data: string,          // Column data source
    searchable: boolean,   // Whether column is searchable
    orderable: boolean     // Whether column is sortable
  }]
}
```

### DataTables Response Format
```javascript
{
  draw: integer,           // Echo of draw parameter
  recordsTotal: integer,   // Total records in dataset
  recordsFiltered: integer, // Total records after filtering
  data: array,            // Array of row data objects
  success: boolean        // Custom success indicator
}
```

## Error Handling

### 1. Database Connection Errors
- Return proper JSON error response with 500 status code
- Log errors for debugging while providing user-friendly messages
- Ensure DataTables receives valid JSON even on errors

### 2. Invalid Parameters
- Validate and sanitize all input parameters
- Provide default values for missing parameters
- Return appropriate error responses for malformed requests

### 3. Query Execution Errors
- Wrap database operations in try-catch blocks
- Return meaningful error messages
- Maintain transaction integrity

## Testing Strategy

### 1. Functional Testing
- Test pagination with various dataset sizes (small, medium, large)
- Verify search functionality across all searchable columns
- Test filtering by "Added By" dropdown with pagination
- Verify sorting functionality on all sortable columns

### 2. Performance Testing
- Test response times with large datasets (1000+ records)
- Verify memory usage remains reasonable
- Test concurrent user access to pagination

### 3. Integration Testing
- Test DataTables frontend integration with server-side API
- Verify proper parameter passing and response handling
- Test error scenarios and graceful degradation

## Implementation Details

### Key Fixes Required

1. **Server-side Processing Flag**: Ensure DataTables is configured with `serverSide: true`

2. **Parameter Handling**: Fix extraction of DataTables parameters in PHP:
   - `$_POST['draw']` for request tracking
   - `$_POST['start']` for pagination offset
   - `$_POST['length']` for page size
   - `$_POST['search']['value']` for search terms

3. **Response Format**: Ensure API returns all required DataTables fields:
   - `draw` (echoed from request)
   - `recordsTotal` (total records without filtering)
   - `recordsFiltered` (total records with current filters)
   - `data` (array of record objects)

4. **SQL Query Structure**: Implement proper query structure:
   ```sql
   -- Base query for data retrieval
   SELECT columns FROM doctors d LEFT JOIN users u ON d.added_by = u.id
   WHERE search_conditions AND filter_conditions
   ORDER BY sort_column sort_direction
   LIMIT start, length
   
   -- Count query for total records
   SELECT COUNT(*) FROM doctors
   
   -- Count query for filtered records
   SELECT COUNT(*) FROM doctors d LEFT JOIN users u ON d.added_by = u.id
   WHERE search_conditions AND filter_conditions
   ```

5. **Search Implementation**: Implement proper search across relevant columns:
   - Doctor name
   - Hospital name
   - Contact number
   - Added by username

6. **Filter Integration**: Ensure "Added By" filter works with pagination:
   - Apply filter conditions to both data and count queries
   - Maintain filter state during pagination navigation

## Security Considerations

- Sanitize all input parameters to prevent SQL injection
- Use prepared statements for all database queries
- Validate user permissions for data access
- Implement proper error handling without exposing sensitive information