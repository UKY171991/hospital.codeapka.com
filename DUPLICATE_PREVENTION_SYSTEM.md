# Comprehensive Duplicate Prevention System

## Overview
This system prevents duplicate data across all tables and APIs while implementing smart updates based on timestamps. If data is different and newer, it updates the existing record instead of creating duplicates.

## Key Features

### 1. Smart Upsert Logic
- **Prevents Duplicates**: Checks for existing records based on unique criteria
- **Timestamp Comparison**: Updates only if new data is newer (`updated_at` field)
- **Data Comparison**: Updates if data has actually changed
- **Flexible Criteria**: Different unique criteria for different entity types

### 2. Entity-Specific Unique Criteria

#### Users
- **Primary**: `username` (must be unique)
- **Secondary**: `email` (if provided, must be unique)

#### Patients
- **Primary**: `mobile` number
- **Secondary**: `uhid` (if provided)
- **Fallback**: `name` + `address` combination

#### Doctors
- **Primary**: `registration_no` (if provided)
- **Secondary**: `email` (if provided)
- **Fallback**: `name` + `hospital` combination

#### Tests
- **Primary**: `name` + `category_id` combination
- **Ensures**: No duplicate test names within same category

#### Categories
- **Primary**: `name` (category names must be unique)

#### Entries
- **Primary**: `patient_id` + `test_id` + `entry_date` combination
- **Ensures**: No duplicate test entries for same patient on same date

## Implementation

### 1. Smart Upsert Function (`umakant/inc/smart_upsert.php`)

```php
smartUpsert($pdo, $table, $uniqueWhere, $data, $options)
```

**Parameters:**
- `$pdo`: Database connection
- `$table`: Table name
- `$uniqueWhere`: Array of unique criteria
- `$data`: Data to insert/update
- `$options`: Configuration options

**Returns:**
```php
[
    'action' => 'created|updated|skipped',
    'id' => int,
    'message' => string
]
```

### 2. Updated APIs

#### Root APIs
- ✅ `user_api.php` - Username and email uniqueness
- ✅ `login_api.php` - Session-based, no duplicates needed

#### Patho APIs
- ✅ `umakant/patho_api/user.php` - Username and email uniqueness
- ✅ `umakant/patho_api/patient.php` - Mobile/UHID uniqueness with smart upsert
- ✅ `umakant/patho_api/doctor.php` - Registration/email uniqueness with smart upsert
- ✅ `umakant/patho_api/test.php` - Name+category uniqueness with smart upsert
- ✅ `umakant/patho_api/entry.php` - Smart upsert included

#### Ajax APIs
- ✅ `umakant/ajax/user_api.php` - Username and email uniqueness
- ✅ `umakant/ajax/patient_api.php` - Smart upsert included

## How It Works

### 1. Create Operation Flow
```
1. Receive new data
2. Determine unique criteria based on entity type
3. Check if record exists with same criteria
4. If exists:
   - Compare timestamps (if available)
   - Compare data for changes
   - Update if newer or different
   - Skip if same and not newer
5. If not exists:
   - Create new record
6. Return action taken and record ID
```

### 2. Update Operation Flow
```
1. Receive update data with ID
2. Check for duplicates (excluding current record)
3. If duplicate found: Return error
4. If no duplicate: Proceed with update
5. Always update timestamp
```

### 3. Timestamp Comparison Logic
```php
// If both records have timestamps
if (new_timestamp > existing_timestamp) {
    // Update the record
} else {
    // Skip update, existing is newer
}

// If no timestamp in new data
// Assume new data is current and update
```

## Error Handling

### Duplicate Prevention Errors
- **409 Conflict**: When trying to create/update with existing unique values
- **400 Bad Request**: When unique criteria cannot be determined
- **500 Server Error**: When database operations fail

### Error Messages
- `"Username already exists"`
- `"Email already exists"`
- `"Mobile number already exists"`
- `"Cannot determine unique criteria for duplicate prevention"`

## Benefits

### 1. Data Integrity
- **No Duplicates**: Prevents duplicate records across all tables
- **Consistent Data**: Ensures data consistency across the system
- **Referential Integrity**: Maintains proper relationships

### 2. Smart Updates
- **Timestamp-Based**: Only updates when data is actually newer
- **Change Detection**: Updates only when data has actually changed
- **Efficient**: Avoids unnecessary database operations

### 3. User Experience
- **Clear Errors**: Users get clear feedback about duplicates
- **Automatic Updates**: Newer data automatically updates existing records
- **Consistent Behavior**: All APIs behave the same way

### 4. Performance
- **Single Query**: Uses efficient upsert operations
- **Minimal Overhead**: Only checks for duplicates when necessary
- **Optimized**: Uses proper database indexes for unique constraints

## Database Constraints

### Recommended Unique Indexes
```sql
-- Users
ALTER TABLE users ADD CONSTRAINT unique_username UNIQUE (username);
ALTER TABLE users ADD CONSTRAINT unique_email UNIQUE (email);

-- Patients  
ALTER TABLE patients ADD CONSTRAINT unique_patient_mobile UNIQUE (mobile);
ALTER TABLE patients ADD CONSTRAINT unique_patient_uhid UNIQUE (uhid);

-- Doctors
ALTER TABLE doctors ADD CONSTRAINT unique_doctor_registration UNIQUE (registration_no);
ALTER TABLE doctors ADD CONSTRAINT unique_doctor_email UNIQUE (email);

-- Tests
ALTER TABLE tests ADD CONSTRAINT unique_test_name_category UNIQUE (name, category_id);

-- Categories
ALTER TABLE categories ADD CONSTRAINT unique_category_name UNIQUE (name);
```

## Testing

### Test Scenarios
1. **Create with existing data** → Should update if newer
2. **Create with same data** → Should skip if not newer  
3. **Create with new data** → Should create new record
4. **Update to existing values** → Should return duplicate error
5. **Update with new values** → Should update successfully

### Example Test Cases
```php
// Test 1: Create patient with existing mobile
$result = smartUpsert($pdo, 'patients', 
    ['mobile' => '9876543210'], 
    ['name' => 'Updated Name', 'mobile' => '9876543210']
);
// Expected: 'updated' if data changed, 'skipped' if same

// Test 2: Create user with existing username
$result = smartUpsert($pdo, 'users',
    ['username' => 'existing_user'],
    ['username' => 'existing_user', 'full_name' => 'New Name']
);
// Expected: 'updated' if newer, 'skipped' if same
```

## Migration Guide

### For Existing Code
1. Include `smart_upsert.php` in your API files
2. Replace manual duplicate checks with `smartUpsert()` calls
3. Update error handling to use returned action/message
4. Test thoroughly with existing data

### For New APIs
1. Include `smart_upsert.php`
2. Define unique criteria using `getUniqueWhere()`
3. Use `smartUpsert()` for create operations
4. Use standard duplicate checks for update operations

## Monitoring

### Log Actions
The system logs all upsert actions:
- `created`: New record created
- `updated`: Existing record updated  
- `skipped`: No changes needed
- `error`: Operation failed

### Metrics to Track
- Duplicate prevention rate
- Update vs create ratio
- Error frequency
- Performance impact

This comprehensive system ensures no duplicate data while maintaining data freshness through intelligent timestamp-based updates.
