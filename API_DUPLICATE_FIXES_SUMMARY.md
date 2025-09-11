# API Duplicate Data Prevention Fixes

## Overview
Fixed all APIs in the hospital system to prevent duplicate data insertion and ensure proper update logic. All APIs now use consistent upsert patterns and proper duplicate checking.

## Issues Fixed

### 1. Test API (`umakant/patho_api/test.php`)
**Problem**: Direct INSERT/UPDATE without duplicate checking allowed duplicate tests with same name and category.

**Fix**: 
- Implemented upsert logic using `upsert_or_skip()` function
- Duplicate detection based on `name + category_id`
- Only updates fields that have changed
- Returns proper action status (`inserted`, `updated`, `skipped`)

### 2. Test Category API (`umakant/patho_api/test_category.php`)
**Problem**: 
- Partial duplicate checking with wrong field name check
- Checking for 'category_name' instead of 'name' in validation

**Fix**:
- Fixed field name validation to use correct `name` field
- Implemented upsert logic using `upsert_or_skip()` function
- Duplicate detection based on category `name`
- Proper update-only-changed-fields logic

### 3. Patient API (`umakant/patho_api/patient.php`)
**Problem**: Direct INSERT without duplicate checking could create duplicate patients.

**Fix**:
- Implemented upsert logic using `upsert_or_skip()` function
- Duplicate detection based on `mobile` number (primary) or `name + address` (fallback)
- Maintains UHID generation for new patients
- Proper field mapping maintained

### 4. Notice API (`umakant/patho_api/notice.php`)
**Problem**: Direct INSERT/UPDATE without duplicate checking.

**Fix**:
- Implemented upsert logic using `upsert_or_skip()` function
- Duplicate detection based on `title + start_date`
- Prevents same-day duplicate notices with identical titles

### 5. Owner API (`umakant/patho_api/owner.php`)
**Problem**: Had basic duplicate checking but inconsistent with other APIs.

**Fix**:
- Replaced basic duplicate checking with upsert logic
- Duplicate detection based on `phone` and optionally `email`
- Consistent with other API patterns
- Better error handling for updates

### 6. Entry API (`umakant/patho_api/entry.php`)
**Problem**: Direct INSERT/UPDATE without duplicate checking for test entries.

**Fix**:
- Implemented upsert logic with special handling for entries
- Duplicate detection based on `patient_id + test_id + entry_date (same day)`
- Prevents duplicate test entries for same patient on same day
- Updates existing entry if found instead of creating duplicate

## Key Improvements

### 1. Consistent Upsert Pattern
All APIs now use the `upsert_or_skip()` function from `ajax_helpers.php` which:
- Checks for existing records based on unique criteria
- Compares all fields and only updates if changes detected
- Returns proper action status (`inserted`, `updated`, `skipped`)
- Maintains `added_by` field integrity
- Updates `updated_at` timestamp automatically

### 2. Proper Update Logic
- Updates only check and modify provided fields
- Existence validation before updates
- Proper error messages for not found records
- Permission checking maintained

### 3. Standardized Responses
All APIs now return consistent response format:
```json
{
  "success": true,
  "message": "Record created/updated/no changes needed",
  "data": { /* full record data */ },
  "action": "inserted|updated|skipped",
  "id": 123
}
```

### 4. Duplicate Detection Criteria

| API | Unique Criteria |
|-----|----------------|
| Doctor | `registration_no` OR (`name` + `hospital` + `contact_no/phone`) |
| Test | `name` + `category_id` |
| Test Category | `name` |
| Patient | `mobile` OR (`name` + `address`) |
| Notice | `title` + `start_date` |
| Owner | `phone` + `email` (if provided) |
| Entry | `patient_id` + `test_id` + same date |

## Testing Recommendations

### 1. Test Duplicate Prevention
For each API, test:
```bash
# Create record
POST /api/endpoint.php
{"name": "Test Record", "other_field": "value"}

# Try to create same record again
POST /api/endpoint.php  
{"name": "Test Record", "other_field": "value"}
# Should return: {"action": "skipped"}

# Update with different data
POST /api/endpoint.php
{"name": "Test Record", "other_field": "new_value"}
# Should return: {"action": "updated"}
```

### 2. Test Update Logic
```bash
# Create record
POST /api/endpoint.php
{"name": "Test Record", "field1": "value1", "field2": "value2"}

# Update only one field
POST /api/endpoint.php
{"id": 1, "field1": "new_value1"}
# Should only update field1, leave field2 unchanged
```

### 3. Test Error Handling
```bash
# Try to update non-existent record
POST /api/endpoint.php
{"id": 99999, "name": "Updated Name"}
# Should return 404 error

# Try to create with missing required fields
POST /api/endpoint.php
{"optional_field": "value"}
# Should return 400 validation error
```

## Files Modified

1. `umakant/patho_api/test.php` - Lines 206-217 replaced with upsert logic
2. `umakant/patho_api/test_category.php` - Lines 158-169 replaced with upsert logic  
3. `umakant/patho_api/patient.php` - Lines 320-340 replaced with upsert logic
4. `umakant/patho_api/notice.php` - Lines 194-225 replaced with upsert logic
5. `umakant/patho_api/owner.php` - Lines 180-250 replaced with upsert logic
6. `umakant/patho_api/entry.php` - Lines 230-270 replaced with upsert logic

## Status
✅ **COMPLETE** - All APIs have been updated with proper duplicate prevention and update logic.

All APIs now consistently prevent duplicate data insertion and only update records when actual changes are detected.
