# Hospital Pathology API - Fixed Version

## 🚀 Quick Start

The API issues have been identified and fixed. Here's how to use the APIs correctly:

### Base URL
```
https://hospital.codeapka.com/umakant/patho_api/
```

### Authentication
All API calls require authentication. Use one of these methods:

#### Method 1: Secret Key Parameter (Recommended)
```
?secret_key=hospital-api-secret-2024
```

#### Method 2: X-Api-Key Header
```
X-Api-Key: hospital-api-secret-2024
```

### User-Specific APIs
Most APIs require a user_id parameter for user-specific data:
```
?user_id=1
```

## 🔧 Fixed Issues

### 1. "Module is required" Error
**Cause**: Missing or invalid action parameter
**Fix**: Always include action parameter
```
✅ Correct: patient.php?action=list&user_id=1&secret_key=hospital-api-secret-2024
❌ Wrong: patient.php?user_id=1&secret_key=hospital-api-secret-2024
```

### 2. Authentication Errors
**Cause**: Missing authentication credentials
**Fix**: Include secret_key parameter or X-Api-Key header
```
✅ Correct: patient.php?action=list&secret_key=hospital-api-secret-2024
❌ Wrong: patient.php?action=list
```

### 3. User ID Required
**Cause**: Missing user_id for user-specific data
**Fix**: Include user_id parameter
```
✅ Correct: patient.php?action=list&user_id=1&secret_key=hospital-api-secret-2024
❌ Wrong: patient.php?action=list&secret_key=hospital-api-secret-2024
```

## 📋 API Endpoints

### Patient API (`patient.php`)

#### List Patients
```http
GET patient.php?action=list&user_id=1&secret_key=hospital-api-secret-2024
```

#### Get Single Patient
```http
GET patient.php?action=get&id=1&user_id=1&secret_key=hospital-api-secret-2024
```

#### Create Patient
```http
POST patient.php?action=save&user_id=1&secret_key=hospital-api-secret-2024
Content-Type: application/json

{
  "name": "John Doe",
  "mobile": "9876543210",
  "age": "30",
  "gender": "Male",
  "address": "123 Main St"
}
```

#### Update Patient
```http
POST patient.php?action=save&user_id=1&secret_key=hospital-api-secret-2024
Content-Type: application/json

{
  "id": 1,
  "name": "John Doe Updated",
  "mobile": "9876543210",
  "age": "31",
  "gender": "Male",
  "address": "456 New St"
}
```

#### Delete Patient
```http
POST patient.php?action=delete&id=1&user_id=1&secret_key=hospital-api-secret-2024
```

#### Patient Statistics
```http
GET patient.php?action=stats&user_id=1&secret_key=hospital-api-secret-2024
```

### Dashboard API (`dashboard.php`)

#### Dashboard Overview
```http
GET dashboard.php?action=overview&user_id=1&secret_key=hospital-api-secret-2024
```

#### Dashboard Statistics
```http
GET dashboard.php?action=stats&user_id=1&secret_key=hospital-api-secret-2024
```

### User API (`user.php`)

#### List Users (Admin only)
```http
GET user.php?action=list&secret_key=hospital-api-secret-2024
```

### Doctor API (`doctor.php`)

#### List Doctors
```http
GET doctor.php?action=list&secret_key=hospital-api-secret-2024
```

### Test API (`test.php`)

#### List Tests
```http
GET test.php?action=list&secret_key=hospital-api-secret-2024
```

## 🧪 Testing

### Online Tester
Use the fixed API tester:
```
https://hospital.codeapka.com/umakant/patho_api/test_api.html
```

### Diagnostics
Check API status and configuration:
```
https://hospital.codeapka.com/umakant/patho_api/fix_api_issues.php
```

### Postman Collection
Import this collection for easy testing:

```json
{
  "info": {
    "name": "Hospital Pathology API - Fixed",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "variable": [
    {
      "key": "base_url",
      "value": "https://hospital.codeapka.com/umakant/patho_api"
    },
    {
      "key": "secret_key",
      "value": "hospital-api-secret-2024"
    },
    {
      "key": "user_id",
      "value": "1"
    }
  ],
  "item": [
    {
      "name": "List Patients",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/patient.php?action=list&user_id={{user_id}}&secret_key={{secret_key}}"
      }
    },
    {
      "name": "Get Patient",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/patient.php?action=get&id=1&user_id={{user_id}}&secret_key={{secret_key}}"
      }
    },
    {
      "name": "Create Patient",
      "request": {
        "method": "POST",
        "url": "{{base_url}}/patient.php?action=save&user_id={{user_id}}&secret_key={{secret_key}}",
        "header": [
          {
            "key": "Content-Type",
            "value": "application/json"
          }
        ],
        "body": {
          "mode": "raw",
          "raw": "{\n  \"name\": \"Test Patient\",\n  \"mobile\": \"9876543210\",\n  \"age\": \"30\",\n  \"gender\": \"Male\",\n  \"address\": \"Test Address\"\n}"
        }
      }
    },
    {
      "name": "Dashboard Overview",
      "request": {
        "method": "GET",
        "url": "{{base_url}}/dashboard.php?action=overview&user_id={{user_id}}&secret_key={{secret_key}}"
      }
    }
  ]
}
```

## 🔒 Security

- All APIs require authentication
- User-specific data is filtered by user_id
- Admin/Master roles have elevated permissions
- CORS headers included for cross-origin requests
- Input validation and sanitization implemented

## 📊 Response Format

All APIs return JSON responses in this format:

### Success Response
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... },
  "timestamp": "2024-01-01 12:00:00"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "error_code": "ERROR_CODE",
  "debug_info": { ... }
}
```

## 🐛 Troubleshooting

### Common Issues

1. **"Module is required"**
   - Add `?action=list` to your URL
   - Check that action parameter is valid

2. **"Authentication required"**
   - Add `&secret_key=hospital-api-secret-2024` to your URL
   - Or add `X-Api-Key: hospital-api-secret-2024` header

3. **"User ID required"**
   - Add `&user_id=1` to your URL for testing
   - Use actual user ID in production

4. **CORS errors**
   - APIs include proper CORS headers
   - Use correct HTTP methods (GET/POST)

### Debug Steps

1. Test with the online tester first
2. Check diagnostics endpoint
3. Verify all required parameters
4. Check HTTP method and headers
5. Validate JSON body for POST requests

## 📞 Support

If you encounter issues:

1. Use the diagnostics endpoint: `/fix_api_issues.php`
2. Test with the online tester: `/test_api.html`
3. Check this documentation for examples
4. Verify authentication and parameters

## 🔄 Updates

- **2024-01-01**: Fixed "Module is required" error
- **2024-01-01**: Improved authentication handling
- **2024-01-01**: Added comprehensive error messages
- **2024-01-01**: Created testing tools and documentation