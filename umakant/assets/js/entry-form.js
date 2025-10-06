// entry-form.js - Handles all entry form related functionality

// Initialize form when document is ready
$(document).ready(function() {
    initializeForm();
    setupValidation();
});

function initializeForm() {
    // Initialize select2 for better dropdown experience
    // Initialize Select2 only for selects that are NOT inside a modal.
    // Modal-contained selects are initialized by modal-enhancements.js on modal show
    $('.select2').filter(function() {
        return $(this).closest('.modal').length === 0;
    }).select2({
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
    // Bind validation to both add and edit forms. We call the page's saveEntry() when valid
    // to ensure a single unified submission path (entry-list.php also binds submit handlers).
    $(document).on('submit', '#entryForm, #addEntryForm', function(e) {
        e.preventDefault();

        // Reset previous errors
        clearErrors();

        // Validate required fields (use fallbacks for different id/name variants)
        let isValid = true;

        const patientVal = $('#patient').val() || $('#patientSelect').val() || $('[name="patient_id"]').val();
        if (!patientVal) {
            // try to mark a visible field if present
            if ($('#patient').length) showFieldError('patient', 'Please select a patient');
            isValid = false;
        }

        const doctorVal = $('#doctor').val() || $('#doctorSelect').val() || $('[name="doctor_id"]').val();
        if (!doctorVal) {
            if ($('#doctor').length) showFieldError('doctor', 'Please select a doctor');
            isValid = false;
        }

        const entryDateVal = $('#entryDate').val() || $('[name="entry_date"]').val();
        if (!entryDateVal) {
            if ($('#entryDate').length) showFieldError('entryDate', 'Please select an entry date');
            isValid = false;
        }

        const statusVal = $('#status').val() || $('#entryStatus').val() || $('[name="status"]').val();
        if (!statusVal) {
            if ($('#status').length) showFieldError('status', 'Please select a status');
            isValid = false;
        }

        // Validate tests section: prefer #testsContainer .test-row
        const testsRows = $('#testsContainer .test-row');
        if (testsRows.length === 0) {
            const $testsTable = $('#testsTable tbody');
            if ($testsTable.length === 0 || $testsTable.find('tr').length === 0) {
                showError('Please add at least one test');
                isValid = false;
            }
        } else {
            let hasSelected = false;
            testsRows.each(function() {
                const sel = $(this).find('.test-select').val();
                if (sel && sel !== '') { hasSelected = true; return false; }
            });
            if (!hasSelected) {
                showError('Please select at least one test');
                isValid = false;
            }
        }

        if (!isValid) {
            return false;
        }

        // If a page-level saveEntry function exists (entry-list.php), call it and stop propagation
        if (typeof window.saveEntry === 'function') {
            try {
                saveEntry(this);
            } catch (err) {
                showError('Unable to submit form.');
            }
            return false;
        }

        // Fallback to submitForm (older handler)
        submitForm();
    });
}

function submitForm() {
    const formData = new FormData($('#entryForm')[0]);
    // Make sure API receives action=save
    formData.set('action', 'save');
    
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