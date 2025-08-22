# Pathology Lab Management System

A comprehensive web-based system for managing pathology laboratory operations including patients, doctors, tests, and test results.

## Features

- **User Management**: Admin and regular user roles with secure authentication
- **Doctor Management**: Add, edit, and manage doctor information
- **Patient Management**: Comprehensive patient records with UHID tracking
- **Test Categories**: Organize tests into logical categories
- **Test Management**: Detailed test information with pricing and reference ranges
- **Entry Management**: Track test entries and results
- **Responsive Design**: Modern AdminLTE 3 interface that works on all devices

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher / MariaDB 10.2 or higher
- Web server (Apache/Nginx)
- Modern web browser

## Installation

1. **Clone or download** the project files to your web server directory

2. **Configure Database**:
   - Create a MySQL database
   - Update database credentials in `inc/connection.php`
   - Run `setup_database.php` in your browser to create tables

3. **Set Permissions**:
   - Ensure the web server can read/write to the project directory
   - Make sure PHP has PDO and MySQL extensions enabled

4. **Access the System**:
   - Navigate to your project URL
   - Default login: `admin` / `admin123`

## Database Setup

The system will automatically create the following tables:

- `users` - User accounts and authentication
- `doctors` - Doctor information and credentials
- `patients` - Patient records and demographics
- `test_categories` - Test classification
- `tests` - Individual test details and pricing
- `entries` - Test orders and results

## File Structure

```
├── ajax/                 # AJAX handlers for dynamic operations
├── inc/                  # Include files (auth, connection, UI components)
├── index.php            # Main dashboard
├── login.php            # Authentication page
├── user.php             # User management
├── doctor.php           # Doctor management
├── patient.php          # Patient management
├── test.php             # Test management
├── test-category.php    # Test category management
├── entry-list.php       # Test entry management
├── plan.php             # Subscription plans
├── setup_database.php   # Database initialization
└── README.md            # This file
```

## Security Features

- Session-based authentication
- Password hashing using PHP's built-in functions
- SQL injection prevention with prepared statements
- XSS protection with htmlspecialchars
- Role-based access control

## Usage

1. **Login**: Use admin credentials to access the system
2. **Dashboard**: View system overview and statistics
3. **Navigation**: Use the sidebar to access different modules
4. **Data Entry**: Add, edit, and delete records as needed
5. **Reports**: View and manage test entries and results

## Troubleshooting

- **Database Connection**: Check credentials in `inc/connection.php`
- **Tables Missing**: Run `setup_database.php` to create required tables
- **Permission Errors**: Ensure web server has proper file permissions
- **AJAX Issues**: Check browser console for JavaScript errors

## Support

For technical support or feature requests, please contact the development team.

## License

This project is proprietary software. All rights reserved.

---

**Note**: This is a production-ready system designed for pathology laboratories. Ensure proper backup procedures and data security measures are in place before deployment.
