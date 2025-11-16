# Inventory Management Module

## Overview
A complete inventory management system has been added to the Hospital Admin panel with Income, Expense, and Client management features.

## Files Created

### 1. Menu Integration
- **umakant/inc/sidebar.php** - Updated with Inventory menu after Email menu

### 2. Page Files
- **umakant/inventory_dashboard.php** - Dashboard with statistics and charts
- **umakant/inventory_income.php** - Income management page
- **umakant/inventory_expense.php** - Expense management page
- **umakant/inventory_client.php** - Client management page

### 3. API File
- **umakant/ajax/inventory_api.php** - Backend API for all inventory operations

### 4. Database
- **umakant/inventory_tables.sql** - SQL file to create required tables

## Installation Steps

1. **Import Database Tables**
   - Open phpMyAdmin or your MySQL client
   - Select your database (u902379465_hospital)
   - Import the file: `umakant/inventory_tables.sql`
   - This will create 3 tables:
     - `inventory_clients` - Client information
     - `inventory_income` - Income records
     - `inventory_expense` - Expense records

2. **Access the Module**
   - Login to the admin panel
   - Navigate to the "Inventory" menu in the sidebar
   - You'll see 4 submenus:
     - Inventory Dashboard
     - Income
     - Expense
     - Client

## Features

### Inventory Dashboard
- Total Income summary
- Total Expense summary
- Net Profit calculation
- Total Clients count
- Income vs Expense chart
- Expense category pie chart
- Recent transactions table

### Income Management
- Add/Edit/Delete income records
- Categories: Consultation, Lab Tests, Pharmacy, Surgery, Room Charges, Other Services
- Link income to clients
- Payment methods: Cash, Card, UPI, Bank Transfer, Cheque
- DataTables with search and export features

### Expense Management
- Add/Edit/Delete expense records
- Categories: Medical Supplies, Equipment, Utilities, Salaries, Rent, Maintenance, Marketing, Transportation, Other
- Track vendor/supplier information
- Invoice number tracking
- Payment methods: Cash, Card, UPI, Bank Transfer, Cheque
- DataTables with search and export features

### Client Management
- Add/Edit/Delete clients
- Client types: Individual, Corporate, Insurance Company, Government
- Complete contact information
- GST number tracking
- View client transaction history
- Client status management (Active/Inactive)
- DataTables with search and export features

## Sample Data
The SQL file includes sample data for testing:
- 3 sample clients
- 3 sample income records
- 3 sample expense records

## Technologies Used
- PHP 7.4+
- MySQL/MariaDB
- Bootstrap 4
- AdminLTE 3
- jQuery
- DataTables
- Chart.js (for dashboard charts)
- Select2 (for client dropdown)
- Toastr (for notifications)

## Notes
- All pages follow the existing design pattern
- Responsive design for mobile devices
- Form validation included
- AJAX-based operations for smooth UX
- Proper error handling
- Foreign key constraints for data integrity
