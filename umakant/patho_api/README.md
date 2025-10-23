# Pathology API Documentation

This directory contains comprehensive API endpoints for the pathology management system.

## 🚀 New Dashboard API

### **dashboard.php** - Comprehensive Dashboard API
The most advanced API providing complete dashboard functionality:

#### Available Endpoints:
- `overview` - Complete dashboard overview with counts, stats, and metrics
- `stats` - Detailed statistics (daily, weekly, monthly, yearly)
- `recent_activities` - Recent activities and events
- `charts_data` - Data for dashboard charts and graphs
- `quick_stats` - Quick statistics for today vs yesterday
- `revenue_stats` - Comprehensive revenue statistics
- `test_performance` - Test performance metrics and analytics
- `patient_demographics` - Patient demographic breakdowns
- `monthly_trends` - Monthly trend analysis
- `top_tests` - Most popular and profitable tests
- `doctor_performance` - Doctor performance metrics
- `alerts` - System alerts and notifications

#### Example Usage:
```bash
# Get dashboard overview for current user (requires session or user parameters)
GET /umakant/patho_api/dashboard.php?action=overview&secret_key=hospital-api-secret-2024

# Get dashboard overview for specific user (testing)
GET /umakant/patho_api/dashboard.php?action=overview&secret_key=hospital-api-secret-2024&test_user_id=1

# Get quick stats for specific username (testing)
GET /umakant/patho_api/dashboard.php?action=quick_stats&secret_key=hospital-api-secret-2024&test_username=admin

# Get charts data (user-filtered)
GET /umakant/patho_api/dashboard.php?action=charts_data&secret_key=hospital-api-secret-2024&test_user_id=2
```

## 📊 Other Available APIs

- `patient.php` - Patient management (list, get, save, delete, stats) - **User-filtered**
- `doctor.php` - Doctor management (list, get, save, delete, specializations, hospitals)
- `test.php` - Test management (list, get, save, delete, stats)
- `entry.php` - Test entry management (list, get, save, delete, stats, add_test, remove_test, get_tests, update_test_result)
- `test_category.php` - Test category management (list, get, save, delete)
- `main_test_category.php` - Main test category management (list, get, save, delete)
- `notice.php` - Notice management (list, get, save, delete)
- `owner.php` - Owner management (list, get, save, delete)
- `user.php` - User management (list, get, save, delete)

## 🔐 Authentication & User Filtering

All APIs require a secret key parameter: `secret_key=hospital-api-secret-2024`

### User-Specific Data Filtering

The Dashboard API now shows **only the current user's data** based on authentication:

- **Session-based**: Uses `$_SESSION['user_id']` and `$_SESSION['role']` from active session
- **Parameter-based** (for testing): Use `test_user_id=1` or `test_username=admin`
- **Role-based access**: 
  - **Admin/Super Admin**: See all system data
  - **Regular Users**: See only their own patients, entries, and activities

### User Authentication Methods:
1. **Active Session** (Production): User must be logged in
2. **Test Parameters** (Development): Add `&test_user_id=1` or `&test_username=admin`
3. **API Parameters**: Pass `user_id`, `username`, and `role` parameters

## 📖 Interactive Documentation

Visit the interactive API documentation and testing interface:
- **Local**: `/umakant/patho_api/api.html`
- **Live**: `https://hospital.codeapka.com/umakant/patho_api/api.html`

## 🎯 Dashboard Integration

The new dashboard API is integrated with:
- **Main Dashboard**: `/umakant/dashboard.php` - Visual dashboard using the API
- **API Testing**: `/umakant/api_test.php` - Test all APIs functionality

## 📈 Features

### Dashboard API Features:
- **Real-time Statistics** - Live data from database
- **Chart Data** - Ready-to-use data for Chart.js
- **Performance Metrics** - Doctor and test performance analytics
- **Revenue Analytics** - Comprehensive financial reporting
- **System Health** - Database status and alerts
- **Activity Tracking** - Recent activities and events
- **Demographic Analysis** - Patient demographic breakdowns
- **Trend Analysis** - Monthly and yearly trends

### Response Format:
```json
{
  "success": true,
  "data": {
    // API-specific data structure
  },
  "message": "Optional message",
  "timestamp": "2024-01-01 12:00:00"
}
```

### Error Handling:
```json
{
  "success": false,
  "message": "Error description",
  "error_code": "ERROR_CODE"
}
```

## 🔧 Technical Details

- **Language**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Security**: Secret key authentication
- **CORS**: Enabled for cross-origin requests
- **Response Format**: JSON
- **Error Handling**: Comprehensive error responses
- **Performance**: Optimized queries with error handling

## 📱 Usage Examples

### JavaScript (Fetch API):
```javascript
// Dashboard API (user-filtered)
const response = await fetch('/umakant/patho_api/dashboard.php?action=overview&secret_key=hospital-api-secret-2024&test_user_id=1');
const data = await response.json();
console.log(data);

// Patient API (user-filtered)
const patientResponse = await fetch('/umakant/patho_api/patient.php?action=list&secret_key=hospital-api-secret-2024&user_id=1');
const patientData = await patientResponse.json();
console.log('User 1 Patients:', patientData.data);
```

### JavaScript (Axios):
```javascript
const response = await axios.get('/umakant/patho_api/dashboard.php', {
  params: {
    action: 'overview',
    secret_key: 'hospital-api-secret-2024'
  }
});
console.log(response.data);
```

### PHP (cURL):
```php
$url = 'https://hospital.codeapka.com/umakant/patho_api/dashboard.php?action=overview&secret_key=hospital-api-secret-2024';
$response = file_get_contents($url);
$data = json_decode($response, true);
print_r($data);
```

### Python (Requests):
```python
import requests

url = 'https://hospital.codeapka.com/umakant/patho_api/dashboard.php'
params = {
    'action': 'overview',
    'secret_key': 'hospital-api-secret-2024'
}
response = requests.get(url, params=params)
data = response.json()
print(data)
```

## 🚀 Getting Started

1. **Access the API Documentation**: Visit `/umakant/patho_api/api.html`
2. **Test the APIs**: Use `/umakant/api_test.php` for quick testing
3. **View Dashboard**: Check `/umakant/dashboard.php` for visual representation
4. **Integration**: Use the APIs in your applications with the secret key

## 📞 Support

For API support and questions:
- Check the interactive documentation at `api.html`
- Use the API test page at `api_test.php`
- Review the dashboard implementation at `dashboard.php`