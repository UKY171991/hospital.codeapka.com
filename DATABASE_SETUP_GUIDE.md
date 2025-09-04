# Database Setup Guide

## Issue: Database Connection Error

The hospital management system is showing "Network error" and "Failed to load data" messages because the database connection is failing.

## Root Cause
The application is configured to connect to a remote MySQL database (`u902379465_hospital`) but it's trying to connect to `localhost`, which suggests it was originally deployed on a shared hosting environment.

## Solutions

### Option 1: Set up Local MySQL Database (Recommended for Development)

1. **Install MySQL/MariaDB** (if not already installed):
   - Download and install MySQL Community Server
   - Or install XAMPP/WAMP/MAMP which includes MySQL

2. **Start MySQL Service**:
   - Windows: Start MySQL service from Services panel
   - Or start XAMPP/WAMP control panel and start MySQL

3. **Create Database and Import Data**:
   ```sql
   CREATE DATABASE hospital_local;
   ```
   Then import the SQL file:
   ```bash
   mysql -u root -p hospital_local < u902379465_hospital.sql
   ```

4. **Update Connection Settings**:
   Edit `umakant/inc/connection.php`:
   ```php
   $host = 'localhost';
   $db   = 'hospital_local';  // Changed from u902379465_hospital
   $user = 'root';            // Changed from u902379465_hospital
   $pass = '';                // Your MySQL root password
   ```

### Option 2: Use Environment Variables

Create a `.env` file in the project root:
```
DB_HOST=localhost
DB_NAME=hospital_local
DB_USER=root
DB_PASS=your_password
DB_PORT=3306
```

### Option 3: Remote Database Connection

If you have access to the remote database:
1. Ensure the database server allows remote connections
2. Update the host in `connection.php` to the actual server IP/domain
3. Ensure the credentials are correct

## Files Fixed

The following fixes have been applied to handle the current issues:

1. **Test API SQL Error**: Fixed query referencing non-existent columns
2. **Entry API Missing Stats**: Added stats action to entry API
3. **Patient API Field Mapping**: Fixed gender/sex field mapping
4. **JavaScript Errors**: Fixed syntax errors in patient.js
5. **Added Better Error Handling**: All APIs now show clearer error messages

## Testing the Fixes

Once the database is connected:
1. Visit the Patient Management page - should load patient data
2. Visit the Test Management page - should load without SQL errors
3. Visit the Entries page - should load entry statistics
4. Visit the Menu Plan page - should display properly

## Next Steps

1. Set up the local database connection
2. Import the SQL schema
3. Test each page to ensure all functionality works
4. Configure proper authentication if needed
