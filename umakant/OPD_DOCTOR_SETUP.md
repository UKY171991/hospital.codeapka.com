# OPD Doctor CRUD Setup Instructions

## Files Created

1. **umakant/ajax/opd_doctor_api.php** - Backend API for CRUD operations
2. **umakant/assets/js/opd_doctor.js** - Frontend JavaScript for handling AJAX requests
3. **umakant/opd_doctor.php** - Updated main page with complete UI
4. **umakant/sql/create_opd_doctors_table.sql** - Database table creation script

## Database Setup

Run the following SQL to create the `opd_doctors` table:

```sql
CREATE TABLE IF NOT EXISTS `opd_doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `hospital` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `registration_no` varchar(100) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_opd_doctors_name` (`name`),
  KEY `idx_opd_doctors_hospital` (`hospital`),
  KEY `idx_opd_doctors_added_by` (`added_by`),
  KEY `idx_opd_doctors_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Features Implemented

### 1. **Statistics Dashboard**
   - Total OPD Doctors
   - Active Doctors (with phone numbers)
   - Unique Specializations
   - Unique Hospitals

### 2. **CRUD Operations**
   - **Create**: Add new OPD doctors with all details
   - **Read**: View doctor list with DataTables (server-side processing)
   - **Update**: Edit existing doctor information
   - **Delete**: Remove doctors from the system

### 3. **DataTables Features**
   - Server-side processing for large datasets
   - Search functionality
   - Sorting on all columns
   - Pagination
   - Responsive design

### 4. **User Interface**
   - Modern Bootstrap 4 design
   - Modal forms for add/edit operations
   - View modal for detailed doctor information
   - Action buttons (View, Edit, Delete)
   - Toast notifications for user feedback
   - Print functionality

### 5. **Security**
   - Session-based authentication
   - Role-based access control (admin/master only for add/edit/delete)
   - SQL injection prevention using prepared statements
   - XSS protection

## API Endpoints

### GET Requests
- `?action=list` - Get paginated list of doctors (DataTables)
- `?action=stats` - Get statistics
- `?action=get&id={id}` - Get single doctor details

### POST Requests
- `action=save` - Create or update doctor
- `action=delete&id={id}` - Delete doctor

## Usage

1. Import the SQL file to create the table
2. Navigate to: `https://hospital.codeapka.com/umakant/opd_doctor.php`
3. Click "Add New Doctor" to create a new record
4. Use the action buttons to View, Edit, or Delete doctors
5. Use the search box to filter doctors

## Dependencies

- jQuery
- DataTables
- Bootstrap 4
- Font Awesome
- Toastr (for notifications)

All dependencies should already be included in your header/footer files.

## Notes

- The system uses session variables for user authentication
- Only users with 'admin' or 'master' roles can add/edit/delete doctors
- All users can view the doctor list
- The `added_by` field automatically captures the logged-in user ID
