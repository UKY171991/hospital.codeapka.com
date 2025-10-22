# Patient Management System Fix - Requirements Document

## Introduction

This document outlines the requirements for fixing the patient management system in the hospital application. The system currently has issues with add/edit/delete operations, database schema mismatches, API path inconsistencies, and incomplete column handling. The goal is to create a fully functional CRUD system for patient management that handles all database columns correctly.

## Glossary

- **Patient_Management_System**: The web-based interface and API for managing patient records in the hospital application
- **CRUD_Operations**: Create, Read, Update, and Delete operations for patient data
- **Database_Schema**: The structure of the patients table in the MySQL database
- **API_Endpoint**: The server-side PHP script that handles patient data operations
- **Frontend_Interface**: The web page (patient.php) that provides the user interface for patient management

## Requirements

### Requirement 1

**User Story:** As a hospital staff member, I want to add new patients with all required information, so that I can maintain complete patient records in the system.

#### Acceptance Criteria

1. WHEN a user clicks the "Add New Patient" button, THE Patient_Management_System SHALL display a modal form with all patient fields
2. WHEN a user submits the add patient form with required fields, THE Patient_Management_System SHALL create a new patient record in the database
3. WHEN a patient is successfully added, THE Patient_Management_System SHALL display a success message and refresh the patient list
4. THE Patient_Management_System SHALL validate that name and mobile fields are provided before saving
5. THE Patient_Management_System SHALL auto-generate a unique UHID for new patients

### Requirement 2

**User Story:** As a hospital staff member, I want to edit existing patient information, so that I can keep patient records up to date.

#### Acceptance Criteria

1. WHEN a user clicks the edit button for a patient, THE Patient_Management_System SHALL populate the edit form with current patient data
2. WHEN a user submits the edit form, THE Patient_Management_System SHALL update the patient record with the new information
3. WHEN a patient is successfully updated, THE Patient_Management_System SHALL display a success message and refresh the patient list
4. THE Patient_Management_System SHALL preserve the original UHID when editing a patient
5. THE Patient_Management_System SHALL handle all database columns correctly during updates

### Requirement 3

**User Story:** As a hospital staff member, I want to delete patient records, so that I can remove incorrect or duplicate entries.

#### Acceptance Criteria

1. WHEN a user clicks the delete button for a patient, THE Patient_Management_System SHALL display a confirmation dialog
2. WHEN a user confirms deletion, THE Patient_Management_System SHALL remove the patient record from the database
3. IF a patient has associated test entries, THEN THE Patient_Management_System SHALL prevent deletion and display an appropriate error message
4. WHEN a patient is successfully deleted, THE Patient_Management_System SHALL display a success message and refresh the patient list
5. THE Patient_Management_System SHALL validate deletion permissions before removing records

### Requirement 4

**User Story:** As a hospital staff member, I want to view detailed patient information, so that I can access complete patient records.

#### Acceptance Criteria

1. WHEN a user clicks the view button for a patient, THE Patient_Management_System SHALL display a modal with complete patient details
2. THE Patient_Management_System SHALL format patient information in a readable layout with proper labels
3. THE Patient_Management_System SHALL display all available patient fields including contact information and registration details
4. THE Patient_Management_System SHALL provide a print option for patient details
5. THE Patient_Management_System SHALL handle missing or null field values gracefully

### Requirement 5

**User Story:** As a hospital staff member, I want the system to handle all patient data fields correctly, so that no information is lost or corrupted.

#### Acceptance Criteria

1. THE Patient_Management_System SHALL map the gender field correctly to the database sex column
2. THE Patient_Management_System SHALL handle the contact field appropriately in all operations
3. THE Patient_Management_System SHALL maintain data consistency between frontend forms and database schema
4. THE Patient_Management_System SHALL validate data types and constraints for all fields
5. THE Patient_Management_System SHALL use correct API endpoints for all operations

### Requirement 6

**User Story:** As a hospital staff member, I want to filter and search patient records efficiently, so that I can quickly find specific patients.

#### Acceptance Criteria

1. THE Patient_Management_System SHALL provide search functionality across patient name, UHID, and mobile number
2. THE Patient_Management_System SHALL allow filtering patients by the user who added them
3. THE Patient_Management_System SHALL display search results in real-time as the user types
4. THE Patient_Management_System SHALL maintain pagination for large patient lists
5. THE Patient_Management_System SHALL provide export functionality for filtered patient data