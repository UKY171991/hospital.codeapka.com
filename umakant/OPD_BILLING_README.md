# OPD Billing CRUD System

Complete CRUD (Create, Read, Update, Delete) system for OPD Billing management.

## Files Created

1. **opd_billing_table.sql** - Database table structure
2. **opd_billing.php** - Main page with UI
3. **ajax/opd_billing_api.php** - Backend API for CRUD operations
4. **assets/js/opd_billing.js** - Frontend JavaScript for interactions

## Installation Steps

### 1. Create Database Table

Run the SQL file in your phpMyAdmin:
- Open phpMyAdmin
- Select your database: `u902379465_hospital`
- Go to SQL tab
- Copy and paste the contents of `opd_billing_table.sql`
- Click "Go" to execute

### 2. Verify File Placement

Ensure all files are in the correct locations:
```
umakant/
├── opd_billing.php (updated)
├── ajax/
│   └── opd_billing_api.php (new)
└── assets/
    └── js/
        └── opd_billing.js (new)
```

### 3. Access the Page

Navigate to: `https://hospital.codeapka.com/umakant/opd_billing.php`

## Features

### Dashboard Statistics
- Total Bills
- Paid Bills
- Unpaid Bills
- Partial Payment Bills
- Total Revenue (₹)
- Pending Amount (₹)

### CRUD Operations

#### Create (Add New Bill)
- Patient Information (Name, Phone, Age, Gender)
- Doctor & Date
- Charges (Consultation, Medicine, Lab, Other)
- Discount
- Payment Details (Amount, Method)
- Auto-calculation of totals and balance
- Notes field

#### Read (View Bills)
- DataTables with server-side processing
- Search functionality
- Pagination
- Sorting
- Detailed view modal with invoice format

#### Update (Edit Bill)
- Edit all bill details
- Recalculate totals automatically
- Update payment status

#### Delete
- Delete billing records with confirmation

### Additional Features

1. **Auto-Calculations**
   - Total Amount = Consultation + Medicine + Lab + Other - Discount
   - Balance = Total - Paid Amount
   - Payment Status (Paid/Partial/Unpaid) auto-updates

2. **Payment Status Badges**
   - Paid (Green)
   - Unpaid (Red)
   - Partial (Yellow)

3. **Payment Methods**
   - Cash
   - Card
   - UPI
   - Online
   - Insurance

4. **Print Functionality**
   - Print bill details from view modal

5. **Responsive Design**
   - Works on desktop, tablet, and mobile
   - Bootstrap 4 based UI
   - DataTables for advanced table features

## Database Schema

```sql
opd_billing
├── id (Primary Key)
├── patient_id
├── patient_name
├── patient_phone
├── patient_age
├── patient_gender
├── doctor_id
├── doctor_name
├── consultation_fee
├── medicine_charges
├── lab_charges
├── other_charges
├── discount
├── total_amount
├── paid_amount
├── balance_amount
├── payment_method
├── payment_status
├── bill_date
├── notes
├── added_by
├── created_at
└── updated_at
```

## API Endpoints

### GET Requests
- `?action=list` - Get paginated list of bills (DataTables)
- `?action=stats` - Get dashboard statistics
- `?action=get&id=X` - Get single bill details

### POST Requests
- `action=save` - Create or update bill
- `action=delete&id=X` - Delete bill

## Security Features

- Session-based authentication
- Role-based access control (admin/master only for write operations)
- SQL injection prevention (PDO prepared statements)
- XSS protection
- CSRF protection via session validation

## Dependencies

- jQuery
- Bootstrap 4
- DataTables
- Font Awesome
- Toastr (for notifications)

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Troubleshooting

### Table doesn't load
- Check browser console for errors
- Verify database connection in `inc/connection.php`
- Ensure `opd_billing` table exists

### Can't add/edit bills
- Verify you're logged in as admin or master
- Check session is active
- Verify file permissions on ajax folder

### Calculations not working
- Clear browser cache
- Check if `opd_billing.js` is loaded
- Verify jQuery is loaded before the script

## Future Enhancements

Possible additions:
- PDF invoice generation
- Email bill to patient
- SMS notifications
- Payment history tracking
- Integration with patient and doctor modules
- Bulk billing
- Reports and analytics
- Export to Excel/CSV

## Support

For issues or questions, contact the development team.
