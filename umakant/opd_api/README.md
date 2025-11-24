# OPD API Documentation

Complete API endpoints for OPD (Outpatient Department) Management System.

## API Files

### 1. doctors.php
Manages OPD doctors data.

**Endpoints:**
- `?action=list` - Get paginated list of doctors (DataTables compatible)
- `?action=get&id=X` - Get single doctor details
- `?action=save` - Create or update doctor (POST)
- `?action=delete&id=X` - Delete doctor (POST)
- `?action=toggle_status&id=X` - Toggle doctor active/inactive status (POST)
- `?action=stats` - Get doctor statistics

**Fields:**
- name, qualification, specialization, hospital
- contact_no, phone, email, address
- registration_no, status, added_by

---

### 2. billing.php
Manages OPD billing and payments.

**Endpoints:**
- `?action=list` - Get paginated list of bills (DataTables compatible)
- `?action=get&id=X` - Get single bill details
- `?action=save` - Create or update bill (POST)
- `?action=delete&id=X` - Delete bill (POST)
- `?action=stats` - Get billing statistics
- `?action=get_doctors` - Get list of active doctors

**Fields:**
- patient_name, patient_phone, patient_age, patient_gender
- doctor_name, bill_date
- consultation_fee, medicine_charges, lab_charges, other_charges
- discount, total_amount, paid_amount, balance_amount
- payment_method, payment_status, notes

**Payment Status:** Paid, Partial, Unpaid

---

### 3. reports.php
Manages patient medical reports.

**Endpoints:**
- `?action=list` - Get paginated list of reports (DataTables compatible)
- `?action=get&id=X` - Get single report details
- `?action=save` - Create or update report (POST)
- `?action=delete&id=X` - Delete report (POST)
- `?action=stats` - Get report statistics
- `?action=get_doctors` - Get list of active doctors

**Fields:**
- patient_name, patient_phone, patient_age, patient_gender
- doctor_name, report_date, follow_up_date
- diagnosis, symptoms, test_results, prescription
- notes, added_by

---

### 4. patients.php
Manages patient data and history.

**Endpoints:**
- `?action=list` - Get list of all patients with visit summary
- `?action=history&name=X` - Get patient's complete history (reports + bills)
- `?action=search&query=X` - Search patients by name or phone
- `?action=stats` - Get patient statistics

**Returns:**
- Patient demographics
- Visit count and dates
- Medical reports history
- Billing history

---

### 5. dashboard.php
Provides dashboard statistics and recent activities.

**Endpoints:**
- `?action=stats` - Get comprehensive OPD statistics
- `?action=recent_reports` - Get 5 most recent reports
- `?action=recent_bills` - Get 5 most recent bills
- `?action=upcoming_followups` - Get upcoming follow-up appointments

**Statistics Include:**
- Doctors (total, active)
- Patients (total, today)
- Reports (total, this week)
- Billing (total, revenue, pending)
- Follow-ups (upcoming, overdue)

---

## Usage Examples

### JavaScript (jQuery)

```javascript
// Get doctors list
$.ajax({
    url: 'opd_api/doctors.php',
    type: 'GET',
    data: { action: 'list' },
    success: function(response) {
        console.log(response.data);
    }
});

// Save new bill
$.ajax({
    url: 'opd_api/billing.php',
    type: 'POST',
    data: {
        action: 'save',
        patient_name: 'John Doe',
        total_amount: 1000,
        paid_amount: 500
    },
    success: function(response) {
        console.log(response.message);
    }
});

// Get dashboard stats
$.ajax({
    url: 'opd_api/dashboard.php',
    type: 'GET',
    data: { action: 'stats' },
    success: function(response) {
        console.log(response.data);
    }
});
```

### PHP

```php
// Include the API file
require_once 'opd_api/doctors.php';

// Or make HTTP request
$response = file_get_contents('opd_api/doctors.php?action=stats');
$data = json_decode($response, true);
```

---

## Response Format

All APIs return JSON responses in this format:

**Success:**
```json
{
    "success": true,
    "data": { ... },
    "message": "Operation successful"
}
```

**Error:**
```json
{
    "success": false,
    "message": "Error description"
}
```

---

## Security

- All APIs use prepared statements to prevent SQL injection
- Session-based authentication required for write operations
- Input validation and sanitization
- Error handling with try-catch blocks

---

## Database Tables Required

1. `opd_doctors` - Doctor information
2. `opd_billing` - Billing records
3. `opd_reports` - Medical reports
4. `users` - User authentication

---

## Dependencies

- PHP 7.2+
- PDO MySQL extension
- `inc/connection.php` - Database connection
- `inc/ajax_helpers.php` - Helper functions (json_response)

---

## Notes

- All date fields use MySQL DATE format (YYYY-MM-DD)
- Monetary values stored as DECIMAL(10,2)
- Auto-calculation of totals and payment status in billing
- Supports DataTables server-side processing
- Includes search, pagination, and sorting
