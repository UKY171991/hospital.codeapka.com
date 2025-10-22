# Patient Management System Fix - Design Document

## Overview

This design document outlines the technical approach to fix the patient management system. The solution addresses database schema mismatches, API path inconsistencies, incomplete column handling, and ensures all CRUD operations work correctly across all patient data fields.

## Architecture

The patient management system follows a three-tier architecture:

1. **Presentation Layer**: `patient.php` - Frontend interface with DataTables for listing and modals for forms
2. **API Layer**: `patho_api/patient.php` - RESTful API handling CRUD operations
3. **Data Layer**: MySQL `patients` table with proper schema mapping

### Key Components

- **Frontend Controller**: JavaScript (`patient.js`) managing UI interactions and API calls
- **Backend API**: PHP script handling HTTP requests and database operations
- **Database Schema**: MySQL table with proper column mapping and constraints

## Components and Interfaces

### 1. Database Schema Fixes

**Issue**: Mismatch between frontend `gender` field and database `sex` column, unused `contact` field

**Solution**: 
- Map `gender` form field to `sex` database column
- Utilize `contact` field as secondary contact information
- Ensure all columns are properly handled in CRUD operations

**Database Column Mapping**:
```
Frontend Field -> Database Column
name -> name
uhid -> uhid  
mobile -> mobile
email -> email
age -> age
age_unit -> age_unit
gender -> sex (mapping required)
father_husband -> father_husband
address -> address
added_by -> added_by
contact -> contact (utilize for secondary contact)
```

### 2. API Path Correction

**Issue**: JavaScript calls `ajax/patient_api.php` but actual API is at `patho_api/patient.php`

**Solution**: 
- Create a symlink or redirect from `ajax/patient_api.php` to `patho_api/patient.php`
- Alternatively, update all JavaScript API calls to use correct path
- Ensure consistent API endpoint usage across the application

### 3. Enhanced CRUD Operations

#### Create Operation
- Validate required fields (name, mobile)
- Auto-generate UHID if not provided
- Map gender to sex column
- Handle contact field appropriately
- Set created_at timestamp
- Associate with current user (added_by)

#### Read Operations
- List with server-side pagination via DataTables
- Search across name, uhid, mobile fields
- Filter by added_by user
- Individual patient retrieval for edit/view
- Map sex column back to gender for frontend

#### Update Operation
- Preserve original UHID
- Update all modifiable fields
- Handle gender to sex mapping
- Set updated_at timestamp
- Validate data integrity

#### Delete Operation
- Check for associated test entries
- Prevent deletion if dependencies exist
- Soft delete option for data retention
- Audit trail for deletions

### 4. Frontend Enhancements

#### Form Handling
- Proper field validation
- Gender dropdown with correct values
- Contact field integration
- Error message display
- Loading states for operations

#### DataTables Configuration
- Server-side processing for performance
- Proper column mapping
- Search and filter functionality
- Export capabilities
- Responsive design

#### Modal Management
- Add/Edit form modal
- View details modal
- Confirmation dialogs
- Print functionality

## Data Models

### Patient Model Structure
```php
class Patient {
    public $id;
    public $name;           // Required
    public $uhid;           // Auto-generated if empty
    public $mobile;         // Required
    public $email;
    public $age;
    public $age_unit;       // Years/Months/Days
    public $sex;            // Male/Female/Other (maps to gender)
    public $father_husband;
    public $address;
    public $contact;        // Secondary contact
    public $added_by;       // User ID
    public $created_at;
    public $updated_at;
}
```

### API Response Format
```json
{
    "success": true|false,
    "message": "Operation result message",
    "data": {
        // Patient object or array of patients
    },
    "draw": 1,              // For DataTables
    "recordsTotal": 100,    // For DataTables
    "recordsFiltered": 50   // For DataTables
}
```

## Error Handling

### Validation Errors
- Required field validation on both frontend and backend
- Data type validation (age as number, email format)
- Unique constraint handling (UHID uniqueness)
- Mobile number format validation

### Database Errors
- Connection failure handling
- Constraint violation handling
- Transaction rollback on failures
- Proper error logging

### API Errors
- HTTP status code mapping
- Structured error responses
- Authentication/authorization errors
- Rate limiting considerations

### Frontend Error Handling
- Toast notifications for user feedback
- Form validation messages
- Network error handling
- Graceful degradation

## Testing Strategy

### Unit Testing
- API endpoint testing for each CRUD operation
- Database operation testing
- Validation logic testing
- Error handling testing

### Integration Testing
- Frontend to API integration
- Database transaction testing
- File upload/export functionality
- Cross-browser compatibility

### User Acceptance Testing
- Complete patient lifecycle testing
- Permission and role-based testing
- Performance testing with large datasets
- Mobile responsiveness testing

## Security Considerations

### Input Validation
- SQL injection prevention using prepared statements
- XSS prevention with proper output encoding
- CSRF protection for form submissions
- File upload security (if applicable)

### Authentication & Authorization
- User session validation
- Role-based access control
- API key authentication
- Audit logging for sensitive operations

### Data Protection
- Sensitive data encryption
- Secure data transmission (HTTPS)
- Data retention policies
- GDPR compliance considerations

## Performance Optimizations

### Database Optimizations
- Proper indexing on search fields (name, uhid, mobile)
- Query optimization for large datasets
- Connection pooling
- Caching strategies

### Frontend Optimizations
- DataTables server-side processing
- Lazy loading for large lists
- Debounced search functionality
- Optimized asset loading

### API Optimizations
- Response compression
- Pagination for large datasets
- Efficient query design
- Caching for frequently accessed data

## Implementation Phases

### Phase 1: Core Fixes
1. Fix database column mapping (gender ↔ sex)
2. Correct API path references
3. Ensure all columns are handled in CRUD operations
4. Basic validation and error handling

### Phase 2: Enhanced Functionality
1. Improve search and filtering
2. Add export functionality
3. Enhance UI/UX with better error messages
4. Implement proper loading states

### Phase 3: Advanced Features
1. Audit logging
2. Advanced validation rules
3. Performance optimizations
4. Security enhancements

## Migration Strategy

### Database Changes
- No schema changes required (existing structure is adequate)
- Data migration for any inconsistent records
- Index creation for performance

### Code Changes
- Backward compatibility maintenance
- Gradual rollout of fixes
- Testing in staging environment
- Rollback plan preparation

## Monitoring and Maintenance

### Logging
- API request/response logging
- Error logging with stack traces
- Performance metrics logging
- User activity logging

### Monitoring
- Database performance monitoring
- API response time monitoring
- Error rate monitoring
- User experience monitoring

### Maintenance
- Regular database cleanup
- Log rotation policies
- Performance tuning
- Security updates