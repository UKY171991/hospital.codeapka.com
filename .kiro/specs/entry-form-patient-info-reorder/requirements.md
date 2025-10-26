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