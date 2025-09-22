# Authentication Fix Guide

## Problem Summary
The API endpoints in the `patho_api` folder are returning 401 Unauthorized errors because of authentication issues. The main problems are:

1. **Database Connection Issues**: The APIs are trying to connect to a remote MySQL database that's not accessible locally
2. **Header Handling Problems**: The `getallheaders()` function doesn't work in all environments
3. **Authentication Method Confusion**: Multiple authentication methods are supported but not all are working properly

## Root Cause Analysis

### 1. Database Connection
- Current connection: `inc/connection.php` tries to connect to remote MySQL
- Error: `SQLSTATE[HY000] [2002] No connection could be made because the target machine actively refused it`

### 2. Authentication Function Issues
- `authenticateApiUser()` in `inc/ajax_helpers.php` has header handling problems
- `getallheaders()` function is not available in all PHP environments
- Debug logging shows authentication methods are not working properly

## Solutions

### Solution 1: Use SQLite for Local Development (Recommended)

Replace the database connection in your API endpoints:

**Before:**
```php
require_once __DIR__ . '/../inc/connection.php';
```

**After:**
```php
require_once __DIR__ . '/../inc/connection_sqlite.php';
```

### Solution 2: Fix Authentication Function

Use the fixed authentication function:

**Before:**
```php
require_once __DIR__ . '/../inc/ajax_helpers.php';
$auth = authenticateApiUser($pdo);
```

**After:**
```php
require_once __DIR__ . '/../inc/ajax_helpers_fixed.php';
$auth = authenticateApiUserFixed($pdo);
```

### Solution 3: Authentication Methods

The API supports multiple authentication methods:

#### Method 1: Bearer Token (Recommended for API clients)
```javascript
fetch('/patho_api/patient.php', {
    headers: {
        'Authorization': 'Bearer YOUR_API_TOKEN',
        'Content-Type': 'application/json'
    }
})
```

#### Method 2: API Key Parameter
```
GET /patho_api/patient.php?api_key=YOUR_API_TOKEN
```

#### Method 3: Shared Secret Header (For server-to-server)
```javascript
fetch('/patho_api/patient.php', {
    headers: {
        'X-Api-Key': 'hospital-api-secret-2024',
        'Content-Type': 'application/json'
    }
})
```

#### Method 4: Shared Secret Parameter
```
GET /patho_api/patient.php?secret_key=hospital-api-secret-2024
```

## Testing the Fixes

### 1. Use the Test Files
- `test_fixed.php` - Shows working authentication
- `patient_fixed.php` - Fixed version of patient API
- `auth_test.html` - Web interface for testing

### 2. Test Authentication Methods

#### Get API Token
```bash
curl -X POST http://localhost/umakant/patho_api/test_fixed.php \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'
```

#### Use Token for API Calls
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost/umakant/patho_api/patient_fixed.php
```

#### Use Shared Secret
```bash
curl -H "X-Api-Key: hospital-api-secret-2024" \
  http://localhost/umakant/patho_api/patient_fixed.php
```

## Implementation Steps

### Step 1: Fix Database Connection
1. Copy `inc/connection_sqlite.php` to your project
2. Update API endpoints to use SQLite connection
3. The SQLite database will be created automatically at `umakant/hospital_dev.db`

### Step 2: Fix Authentication
1. Copy `inc/ajax_helpers_fixed.php` to your project
2. Update API endpoints to use the fixed authentication function
3. Test with the provided test files

### Step 3: Update Client Code
1. Ensure your client code sends proper authentication headers
2. Use Bearer tokens for API authentication
3. Handle 401 responses properly

### Step 4: Test All Endpoints
1. Use `auth_test.html` to test authentication methods
2. Test all API endpoints with proper authentication
3. Verify that 401 errors are resolved

## Example Fixed API Endpoint

Here's how to fix any API endpoint:

```php
<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-Key');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Use SQLite connection for local development
require_once __DIR__ . '/../inc/connection_sqlite.php';
require_once __DIR__ . '/../inc/ajax_helpers_fixed.php';
require_once __DIR__ . '/../inc/api_config.php';

try {
    // Authenticate user
    $auth = authenticateApiUserFixed($pdo);
    
    if (!$auth) {
        json_response([
            'success' => false,
            'message' => 'Authentication required',
            'debug_info' => [
                'available_auth_methods' => [
                    '1. Add Authorization: Bearer <token> header',
                    '2. Add api_key=<token> parameter',
                    '3. Add X-Api-Key: hospital-api-secret-2024 header',
                    '4. Add secret_key=hospital-api-secret-2024 parameter'
                ]
            ]
        ], 401);
    }
    
    // Check permissions
    if (!checkPermission($auth, 'list')) {
        json_response(['success' => false, 'message' => 'Permission denied'], 403);
    }
    
    // Your API logic here
    json_response(['success' => true, 'data' => []]);
    
} catch (Exception $e) {
    json_response(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
}
?>
```

## Troubleshooting

### Still Getting 401 Errors?
1. Check that you're using the fixed authentication function
2. Verify the database connection is working
3. Ensure proper headers are being sent
4. Check the debug output for authentication method details

### Database Issues?
1. Make sure SQLite extension is enabled in PHP
2. Check file permissions for the database file
3. Verify the connection file is being included correctly

### Header Issues?
1. Ensure CORS headers are set properly
2. Check that the client is sending the correct headers
3. Verify the authentication method being used

## Files Created/Modified

- `inc/ajax_helpers_fixed.php` - Fixed authentication function
- `patho_api/test_fixed.php` - Working authentication test
- `patho_api/patient_fixed.php` - Fixed patient API example
- `patho_api/auth_test.html` - Web testing interface
- `patho_api/AUTHENTICATION_FIX_GUIDE.md` - This guide

## Next Steps

1. Apply these fixes to all API endpoints
2. Update client code to use proper authentication
3. Test all functionality
4. Deploy with proper database configuration for production
