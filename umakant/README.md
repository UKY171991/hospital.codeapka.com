# Pathology Lab Management System

A web-based system designed to streamline pathology laboratory operations using PHP and AdminLTE 3.

## Features

- User Management (Admin and regular users)
- Doctor Management
- Patient Management with UHID tracking
- Test Categories
- Test Management with pricing and reference ranges
- Test Entry Management
- Role-based access control
- Responsive design using AdminLTE 3

## System Requirements

- Web Server (Apache/Nginx)
- PHP 7.4 or higher
- MySQL 5.7+/MariaDB 10.2+
- Modern web browser

## Installation

1. Clone or download the project to your web server directory
2. Run `init.php` to check system requirements
3. Run `create_database.php` in your browser to create the database
4. Run `setup_database.php` in your browser to initialize tables
5. Access the system via your web server URL
6. Login with default credentials:
   - Username: `admin`
   - Password: `admin123`

## Default User

The system comes with a default admin user:
- Username: `admin`
- Password: `admin123`

New users can register at `register.php`.

## Directory Structure

```
.
├── inc/                 # Include files (auth, connection, header, sidebar, footer)
├── ajax/                # AJAX handlers
├── js/                  # JavaScript files
├── css/                 # CSS files
├── patho_api/           # RESTful API endpoints
│   ├── api.txt          # API documentation
│   ├── index.html       # API endpoint reference
│   ├── db_connection.php # API database connection
│   ├── categories.php   # Categories API endpoints
│   ├── tests.php        # Tests API endpoints
│   ├── patients.php     # Patients API endpoints
│   ├── doctors.php      # Doctors API endpoints
│   ├── reports.php      # Reports API endpoints
│   ├── users.php        # Users API endpoints
│   └── entries.php      # Entries API endpoints
├── index.php            # Main dashboard
├── login.php            # Authentication page
├── logout.php           # Logout functionality
├── register.php         # User registration page
├── init.php             # Initialization script
├── user.php             # User management
├── doctor.php           # Doctor management
├── patient.php          # Patient management
├── test-category.php    # Test category management
├── test.php             # Test management
├── entry-list.php       # Test entry management
├── create_database.php  # Database creation script
├── setup_database.php   # Database initialization script
├── reset_db.php         # Database reset script
├── backup_db.php        # Database backup script
├── list_backups.php     # List database backups
├── download_backup.php  # Download database backups
├── README.md            # This file
```

## API Endpoints

The system includes a complete RESTful API for all entities:

- Categories API
- Tests API
- Patients API
- Doctors API
- Reports API
- Users API
- Entries API

See `patho_api/api.txt` for detailed documentation.

## Security Features

- Password hashing using PHP's `password_hash()`
- Prepared statements to prevent SQL injection
- Session-based authentication
- Role-based access control
- XSS protection using `htmlspecialchars()`

## Technologies Used

- **Frontend**: HTML, CSS, JavaScript, AdminLTE 3
- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **UI Framework**: AdminLTE 3
- **Icons**: Font Awesome 5

## License

This project is open source and available under the MIT License.