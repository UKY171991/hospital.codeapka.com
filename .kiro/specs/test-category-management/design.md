# Design Document

## Overview

The Test Category Management System provides a hierarchical organization structure for medical tests through a category-based classification system. The design implements a two-tier architecture where categories serve as parent containers for related tests, ensuring proper organization and easy navigation.

## Architecture

### Database Schema Design

#### Categories Table Structure
```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

#### Enhanced Tests Table Structure
```sql
ALTER TABLE tests ADD COLUMN category_id INT,
ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT;
```

### API Architecture

#### Category Management API (`ajax/category_api.php`)
- **GET /category_api.php?action=list** - Retrieve all categories with test counts
- **GET /category_api.php?action=get&id=X** - Get specific category details
- **POST /category_api.php?action=save** - Create/update category
- **POST /category_api.php?action=delete&id=X** - Delete category (with validation)
- **GET /category_api.php?action=simple_list** - Get categories for dropdowns

#### Enhanced Test API Updates
- Update `test_api.php` to include category information in all responses
- Add category validation in test creation/update operations
- Enhance test listing to include category names and allow category-based filtering

## Components and Interfaces

### Frontend Components

#### 1. Category Management Interface (`category-list.php`)
- **DataTable Integration**: Server-side processing for category listing
- **CRUD Modal Forms**: Create/edit category forms with validation
- **Test Count Display**: Show number of tests per category
- **Status Management**: Toggle active/inactive categories

#### 2. Enhanced Test Management Interface
- **Category Selection Dropdown**: Populated from categories API
- **Category-based Filtering**: Filter tests by selected category
- **Enhanced Test Display**: Show category information alongside test details

#### 3. Test Selection Enhancement (Entry Forms)
- **Grouped Test Dropdown**: Tests organized by category
- **Enhanced Search**: Search across test names and categories
- **Improved Display Format**: "Test Name (Category) [ID: X]"

### JavaScript Components

#### 1. CategoryManager Class (`category-list.js`)
```javascript
class CategoryManager {
    constructor() {
        this.categoriesTable = null;
        this.currentEditId = null;
    }
    
    // Core CRUD operations
    async loadCategories()
    async saveCategory(formData)
    async deleteCategory(id)
    
    // UI management
    openAddModal()
    openEditModal(id)
    validateForm()
}
```

#### 2. Enhanced EntryManager Updates
- Update test loading to include category information
- Enhance test option creation with category grouping
- Improve test search and filtering capabilities

## Data Models

### Category Model
```javascript
{
    id: number,
    name: string,
    description: string,
    is_active: boolean,
    test_count: number,
    created_at: string,
    updated_at: string,
    created_by: number,
    created_by_name: string
}
```

### Enhanced Test Model
```javascript
{
    id: number,
    name: string,
    category_id: number,
    category_name: string,
    // ... existing test fields
    price: number,
    unit: string,
    min: number,
    max: number,
    // ... demographic ranges
}
```

## Error Handling

### Database Constraint Violations
- **Foreign Key Violations**: Prevent test creation with invalid category_id
- **Cascade Restrictions**: Prevent category deletion when tests exist
- **Unique Constraints**: Handle duplicate category names gracefully

### API Error Responses
```javascript
{
    success: false,
    message: "User-friendly error message",
    error_code: "CATEGORY_HAS_TESTS",
    details: {
        category_id: 5,
        test_count: 12
    }
}
```

### Frontend Error Handling
- **Form Validation**: Client-side validation before API calls
- **Network Errors**: Graceful handling of connection issues
- **User Feedback**: Clear error messages with suggested actions

## Testing Strategy

### Database Testing
- Test foreign key constraints and referential integrity
- Validate cascade restrictions work correctly
- Test unique constraints on category names

### API Testing
- Unit tests for all CRUD operations
- Integration tests for category-test relationships
- Error handling tests for constraint violations

### Frontend Testing
- Component testing for CategoryManager class
- Integration testing for enhanced test selection
- User workflow testing for complete category-to-test creation flow

### Data Migration Testing
- Test migration of existing tests to new category structure
- Validate data integrity during schema updates
- Test rollback procedures if needed

## Security Considerations

### Access Control
- Restrict category management to admin users only
- Validate user permissions before allowing modifications
- Log all category and test management activities

### Data Validation
- Server-side validation for all category operations
- SQL injection prevention in all database queries
- Input sanitization for category names and descriptions

### Audit Trail
- Track all category creation, modification, and deletion
- Log test category assignments and changes
- Maintain user attribution for all operations

## Performance Considerations

### Database Optimization
- Index on category_id in tests table for fast lookups
- Index on category name for search operations
- Optimize JOIN queries for test-category relationships

### Caching Strategy
- Cache category list for dropdown population
- Cache test-category mappings for entry forms
- Implement cache invalidation on category updates

### Frontend Optimization
- Lazy loading of test data grouped by categories
- Debounced search functionality
- Efficient DOM updates for large test lists

## Migration Strategy

### Phase 1: Database Schema Updates
1. Create categories table with initial data
2. Add category_id column to tests table
3. Populate initial category assignments

### Phase 2: API Enhancements
1. Implement category management API
2. Update test API to include category information
3. Add validation and constraint handling

### Phase 3: Frontend Implementation
1. Create category management interface
2. Enhance test management with category features
3. Update entry forms with improved test selection

### Phase 4: Data Migration and Testing
1. Migrate existing test data to new structure
2. Comprehensive testing of all functionality
3. User training and documentation updates