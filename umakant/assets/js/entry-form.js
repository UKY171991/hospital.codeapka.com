// entry-form.js - Handles all entry form related functionality

// Initialize form when document is ready
$(document).ready(function() {
    initializeForm();
    setupValidation();
});

function initializeForm() {
    // Initialize select2 for better dropdown experience
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Load patients for dropdown
    $.ajax({
        url: 'ajax/patient_api.php',
        type: 'GET',
        data: { action: 'list' },
        success: function(response) {
            if (response.success && response.data) {
                populatePatientDropdown(response.data);
            } else {
                showError('Failed to load patients');
            }
        },
        error: function() {
            showError('Failed to load patients. Please try again.');
        }
    });

    // Load doctors for dropdown
    $.ajax({
        url: 'ajax/doctor_api.php',
        type: 'GET',
        data: { action: 'list' },
        success: function(response) {
            if (response.success && response.data) {
                populateDoctorDropdown(response.data);
            } else {
                showError('Failed to load doctors');
            }
        },
        error: function() {
            showError('Failed to load doctors. Please try again.');
        }
    });
}

function populatePatientDropdown(patients) {
    const $select = $('#patient');
    $select.empty().append('<option value="">Select Patient</option>');
    
    patients.forEach(patient => {
        $select.append(`<option value="${patient.id}">${escapeHtml(patient.name)}</option>`);
    });
}

function populateDoctorDropdown(doctors) {
    const $select = $('#doctor');
    $select.empty().append('<option value="">Select Doctor</option>');
    
    doctors.forEach(doctor => {
        $select.append(`<option value="${doctor.id}">${escapeHtml(doctor.name)}</option>`);
    });
}

function setupValidation() {
    // Add client-side validation
    $('#entryForm').on('submit', function(e) {
        e.preventDefault();
        
        // Reset previous errors
        clearErrors();
        
        // Validate required fields
        let isValid = true;
        
        if (!$('#patient').val()) {
            showFieldError('patient', 'Please select a patient');
            isValid = false;
        }
        
        if (!$('#doctor').val()) {
            showFieldError('doctor', 'Please select a doctor');
            isValid = false;
        }
        
        if (!$('#entryDate').val()) {
            showFieldError('entryDate', 'Please select an entry date');
            isValid = false;
        }
        
        if (!$('#status').val()) {
            showFieldError('status', 'Please select a status');
            isValid = false;
        }
        
        // Validate tests section
        const $testsTable = $('#testsTable tbody');
        if ($testsTable.find('tr').length === 0) {
            showError('Please add at least one test');
            isValid = false;
        }
        
        if (isValid) {
            submitForm();
        }
    });
}

function submitForm() {
    const formData = new FormData($('#entryForm')[0]);
    
    $.ajax({
        url: 'ajax/entry_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showSuccess('Entry saved successfully');
                $('#addEntryModal').modal('hide');
                refreshTable(); // Refresh the entries table
            } else {
                showError(response.message || 'Failed to save entry');
            }
        },
        error: function() {
            showError('Failed to save entry. Please try again.');
        }
    });
}

function showFieldError(fieldId, message) {
    const $field = $(`#${fieldId}`);
    $field.addClass('is-invalid');
    $field.after(`<div class="invalid-feedback">${escapeHtml(message)}</div>`);
}

function showError(message) {
    const $alert = $('<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">')
        .text(message)
        .append('<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>');
    
    $('#formMessages').empty().append($alert);
}

function showSuccess(message) {
    const $alert = $('<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">')
        .text(message)
        .append('<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>');
    
    $('#formMessages').empty().append($alert);
}

function clearErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    $('#formMessages').empty();
}