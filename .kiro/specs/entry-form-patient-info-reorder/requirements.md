# Requirements Document

## Introduction

This feature involves reorganizing the entry form modal in the hospital management system to improve user experience by displaying patient information fields at the top of the form, before other sections like Tests. This change will make the form more intuitive by following a logical flow where patient details are captured first.

## Glossary

- **Entry Form Modal**: The modal dialog used for adding or editing test entries in the hospital system
- **Patient Information Section**: The card section containing patient contact, age, gender, and address fields
- **Tests Section**: The section containing test selection and result entry fields
- **Form Layout**: The visual arrangement and ordering of form sections and fields

## Requirements

### Requirement 1

**User Story:** As a hospital staff member, I want to see patient information fields at the top of the entry form, so that I can enter patient details first before proceeding to other information.

#### Acceptance Criteria

1. WHEN the entry form modal is opened, THE Entry Form Modal SHALL display the Patient Information Section before the Tests Section
2. WHEN the entry form modal is opened, THE Entry Form Modal SHALL maintain the current functionality of all existing fields
3. THE Entry Form Modal SHALL preserve the existing field validation and data binding behavior
4. THE Entry Form Modal SHALL maintain the current responsive design and styling
5. THE Entry Form Modal SHALL display sections in the following order: Owner/Patient/Doctor selection, Patient Information, Tests, Additional Information, Pricing Information

### Requirement 2

**User Story:** As a hospital staff member, I want the form layout to be logical and intuitive, so that I can efficiently enter patient data without confusion.

#### Acceptance Criteria

1. THE Entry Form Modal SHALL position the Patient Information Section immediately after the basic selection fields (Owner, Patient, Doctor, Date, Status)
2. THE Entry Form Modal SHALL maintain consistent spacing and visual hierarchy between sections
3. THE Entry Form Modal SHALL preserve all existing form field labels and help text
4. THE Entry Form Modal SHALL maintain the current card-based section styling for the Patient Information Section

### Requirement 3

**User Story:** As a hospital staff member, I want to either select an existing patient or add a new patient directly in the form, so that I can efficiently manage patient information without switching between pages.

#### Acceptance Criteria

1. WHEN a patient is selected from the dropdown, THE Entry Form Modal SHALL automatically populate the patient contact, age, gender, and address fields with the selected patient's data
2. THE Entry Form Modal SHALL provide an "Add New Patient" option or button within the patient selection area
3. WHEN "Add New Patient" is selected, THE Entry Form Modal SHALL enable manual editing of all patient information fields (contact, age, gender, address)
4. WHEN a patient is selected from dropdown, THE Entry Form Modal SHALL make patient information fields read-only to prevent accidental modification of existing patient data
5. THE Entry Form Modal SHALL maintain the existing patient selection functionality for owner-filtered patients
6. WHEN patient information fields are manually entered for new patient, THE Entry Form Modal SHALL preserve the entered data during form submission
7. THE Entry Form Modal SHALL clearly indicate whether patient data is from an existing patient or newly entered data

### Requirement 4

**User Story:** As a hospital staff member, I want the patient information fields to behave intelligently based on my selection, so that I can avoid data entry errors and work efficiently.

#### Acceptance Criteria

1. WHEN no patient is selected, THE Entry Form Modal SHALL allow manual entry in all patient information fields
2. WHEN switching from selected patient to "Add New Patient", THE Entry Form Modal SHALL clear all patient information fields and enable editing
3. WHEN switching from manual entry back to patient selection, THE Entry Form Modal SHALL populate fields with selected patient data and disable editing
4. THE Entry Form Modal SHALL validate that either a patient is selected OR all required patient information fields are manually filled
5. THE Entry Form Modal SHALL provide clear visual indicators for editable vs read-only patient information fields