# Pathology Lab Management System - API

This directory contains the RESTful API endpoints for the Pathology Lab Management System.

## Overview

The API provides programmatic access to all system entities:
- Categories
- Tests
- Patients
- Doctors
- Reports
- Users
- Entries

## Base URL

```
https://hospital.codeapka.com/umakant/patho_api/
```

## Authentication

The API does not currently implement authentication. In a production environment, you should add authentication mechanisms such as API keys or OAuth.

## Endpoints

Each entity has standard CRUD operations:

### Categories
- `GET /categories.php` - Get all categories
- `GET /categories.php?id={id}` - Get category by ID
- `POST /categories.php` - Create category
- `PUT /categories.php` - Update category
- `DELETE /categories.php?id={id}` - Delete category

### Tests
- `GET /tests.php` - Get all tests
- `GET /tests.php?id={id}` - Get test by ID
- `POST /tests.php` - Create test
- `PUT /tests.php` - Update test
- `DELETE /tests.php?id={id}` - Delete test

### Patients
- `GET /patients.php` - Get all patients
- `GET /patients.php?id={id}` - Get patient by ID
- `POST /patients.php` - Create patient
- `PUT /patients.php` - Update patient
- `DELETE /patients.php?id={id}` - Delete patient

### Doctors
- `GET /doctors.php` - Get all doctors
- `GET /doctors.php?id={id}` - Get doctor by ID
- `POST /doctors.php` - Create doctor
- `PUT /doctors.php` - Update doctor
- `DELETE /doctors.php?id={id}` - Delete doctor

### Reports
- `GET /reports.php` - Get all reports
- `GET /reports.php?id={id}` - Get report by ID
- `POST /reports.php` - Create report
- `PUT /reports.php` - Update report
- `DELETE /reports.php?id={id}` - Delete report

### Users
- `GET /users.php` - Get all users
- `GET /users.php?id={id}` - Get user by ID
- `POST /users.php` - Create user
- `PUT /users.php` - Update user
- `DELETE /users.php?id={id}` - Delete user

### Entries
- `GET /entries.php` - Get all entries
- `GET /entries.php?id={id}` - Get entry by ID
- `POST /entries.php` - Create entry
- `PUT /entries.php` - Update entry
- `DELETE /entries.php?id={id}` - Delete entry

## Request/Response Format

All requests and responses use JSON format.

### Example Request
```json
POST /categories.php
Content-Type: application/json

{
  "name": "Blood Tests",
  "description": "Tests related to blood analysis"
}
```

### Example Response
```json
{
  "status": "success",
  "message": "Category created successfully",
  "id": 1
}
```

## Error Handling

Errors are returned in a consistent format:

```json
{
  "status": "error",
  "message": "Error description"
}
```

## Testing

You can test the API endpoints using:
1. The [test.html](test.html) file in this directory
2. The [test_all.php](test_all.php) script
3. Any REST client like Postman or curl

## Documentation

See [api.txt](api.txt) for detailed API documentation with all parameters.