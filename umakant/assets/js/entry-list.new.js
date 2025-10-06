// Global variables
let entriesTable;
let currentEntryId = null;
let testRowCount = 1;

// Initialize page when document is ready
$(document).ready(function() {
    initializePage();
});

// Initialize page components
function initializePage() {
    loadStatistics();
    initializeDataTable();
    loadTests();
    setupEventHandlers();
}

// Load statistics
function loadStatistics() {
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'stats' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalEntries').text(response.data.total || 0);
                $('#pendingEntries').text(response.data.pending || 0);
                $('#completedEntries').text(response.data.completed || 0);
                $('#todayEntries').text(response.data.today || 0);
            }
        },
        error: function() {
            console.error('Failed to load statistics');
        }
    });
}

// Initialize DataTable
function initializeDataTable() {
    // Avoid re-initializing DataTable if another script already initialized it.
    if (window._entriesTableInitialized || (typeof $.fn.dataTable !== 'undefined' && $.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable('#entriesTable'))) {
        try { entriesTable = $('#entriesTable').DataTable(); } catch(e) { /* fallback */ }
        window._entriesTableInitialized = true;
        return;
    }

    entriesTable = $('#entriesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'ajax/entry_api_fixed.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'list' },
            dataSrc: function(response) {
                try {
                    if (response && response.success) {
                        return response.data || [];
                    }
                    console.error('Entries list returned an error payload:', response);
                    return [];
                } catch (e) {
                    console.error('Failed to parse entries list response', e, response);
                    return [];
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                // Ignore aborted requests (status 0) which commonly happen on navigation or duplicate inits
                if (textStatus === 'abort' || xhr.status === 0) {
                    console.info('Entries list AJAX aborted (likely duplicate init or navigation)');
                    return;
                }
                console.error('Entries list AJAX error:', textStatus, errorThrown, 'HTTP status:', xhr.status, 'response:', xhr.responseText);
                try { toastr.error('Failed to load entries: ' + (xhr.status || textStatus)); } catch(e) {}
            },
            complete: function() {
                // You can add any UI cleanup here if needed
            }
        },
        columns: [
            { 
                data: 'id',
                render: function(data, type, row) {
                    return `<span class="badge badge-primary">#${data}</span>`;
                }
            },
            { 
                data: 'patient_name',
                render: function(data, type, row) {
                    return `<div>
                        <strong>${data || 'N/A'}</strong>
                        ${row.uhid ? `<br><small class="text-muted">UHID: ${row.uhid}</small>` : ''}
                        ${row.age ? `<br><small class="text-muted">Age: ${row.age} ${row.gender || ''}</small>` : ''}
                    </div>`;
                }
            },
            { 
                data: 'doctor_name',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">Not assigned</span>';
                }
            },
            { 
                data: 'owner_name',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">Not assigned</span>';
                }
            },
            { 
                data: 'test_name',
                render: function(data, type, row) {
                    if (row.tests_count > 1) {
                        return `<div>
                            <span class="badge badge-info">${row.tests_count} tests</span>
                            <br><small>${row.test_names}</small>
                        </div>`;
                    }
                    return data || '<span class="text-muted">No tests</span>';
                }
            },
            {
                data: 'status',
                render: function(data, type, row) {
                    const statusClass = {
                        'pending': 'warning',
                        'completed': 'success',
                        'cancelled': 'danger'
                    }[data] || 'secondary';
                    return `<span class="badge badge-${statusClass}">${data}</span>`;
                }
            },
            {
                data: 'priority',
                render: function(data, type, row) {
                    const priority = data || 'normal';
                    const priorityClass = {
                        'urgent': 'danger',
                        'emergency': 'warning',
                        'routine': 'info',
                        'normal': 'secondary'
                    }[priority] || 'secondary';
                    return `<span class="badge badge-${priorityClass}">${priority}</span>`;
                }
            },
            {
                data: 'final_amount',
                render: function(data, type, row) {
                    const amount = parseFloat(data || 0);
                    return `₹${amount.toFixed(2)}`;
                }
            },
            {
                data: 'entry_date',
                render: function(data, type, row) {
                    if (data) {
                        const date = new Date(data);
                        return date.toLocaleDateString('en-IN');
                    }
                    return '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `<div class="btn-group" role="group">
                        <button class="btn btn-info btn-sm" onclick="viewEntry(${row.id})" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editEntry(${row.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteEntry(${row.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>`;
                }
            }
        ],
        order: [[6, 'desc']], // Sort by date descending
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: true,
        language: {
            processing: "Loading entries...",
            emptyTable: "No entries found",
            zeroRecords: "No matching entries found"
        }
    });
    window._entriesTableInitialized = true;
}

// Load patients for dropdown
function loadPatients() {
    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const patientSelect = $('#patientSelect');
                patientSelect.empty().append('<option value="">Select Patient</option>');
                response.data.forEach(function(patient) {
                    // include gender, contact and address data attributes so we can auto-populate fields on selection/edit
                    const genderVal = patient.gender || patient.sex || '';
                    const contactVal = (patient.contact || patient.phone || patient.mobile || '');
                    const addressVal = (patient.address || patient.address_line || '');
                    patientSelect.append(`<option value="${patient.id}" data-gender="${genderVal}" data-contact="${contactVal}" data-address="${addressVal}">${patient.name} (${patient.uhid || 'No UHID'})</option>`);
                });
                // modal-enhancements will initialize Select2 on modal show
                patientSelect.addClass('select2');
            }
        }
    });
}

// Load doctors for dropdown
function loadDoctors() {
    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const doctorSelect = $('#doctorSelect');
                doctorSelect.empty().append('<option value="">Select Doctor</option>');
                response.data.forEach(function(doctor) {
                    doctorSelect.append(`<option value="${doctor.id}">Dr. ${doctor.name}</option>`);
                });
                // modal-enhancements will initialize Select2 on modal show
                doctorSelect.addClass('select2');
            }
        }
    });
}

// Load combined owners and users for dropdown
function loadUsersDropdown(callback) {
    const ownerUserSelect = $('#ownerAddedBySelect');
    ownerUserSelect.empty().append('<option value="">Select User</option>');

    // Load users
    $.ajax({
        url: 'ajax/user_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(userResponse) {
            console.log('User response:', userResponse);

            // Add users
            if (userResponse.success && userResponse.data && userResponse.data.length > 0) {
                userResponse.data.forEach(function(user) {
                    const displayName = user.full_name || user.username || user.email || `User ${user.id}`;
                    ownerUserSelect.append(`<option value="user_${user.id}" data-type="user" data-user-id="${user.id}">👤 ${displayName} (${user.role || 'user'})</option>`);
                });
            } else {
                // If no users, add a placeholder
                ownerUserSelect.append('<option value="" disabled>No users available</option>');
            }

            // modal-enhancements will initialize Select2 on modal show
            ownerUserSelect.addClass('select2');

            // Notify listeners that owner/user options are loaded
            try { ownerUserSelect.trigger('ownerUsers:loaded'); } catch(e) { /* ignore */ }

            // Set current user as default if not editing
            if (!currentEntryId && currentUserId) {
                setTimeout(function() {
                    ownerUserSelect.val(`user_${currentUserId}`).trigger('change');
                }, 100);
            }

            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading users:', error);
            ownerUserSelect.append('<option value="" disabled>Error loading users</option>');
        }
    });
}

// Load patients based on selected owner
function loadPatientsBy(id, type) {
    let data = { action: 'list' };
    if (type === 'owner') {
        data.owner_id = id;
    } else {
        data.added_by = id;
    }

    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'GET',
        data: data,
        dataType: 'json',
        success: function(response) {
            const patientSelect = $('#patientSelect');
            patientSelect.empty().append('<option value="">Select Patient</option>');
            
                if (response.success && response.data) {
                response.data.forEach(function(patient) {
                    const genderVal = patient.gender || patient.sex || '';
                    const contactVal = (patient.contact || patient.phone || patient.mobile || '');
                    const addressVal = (patient.address || patient.address_line || '');
                    patientSelect.append(`<option value="${patient.id}" data-gender="${genderVal}" data-contact="${contactVal}" data-address="${addressVal}">${patient.name} (${patient.uhid || 'No UHID'})</option>`);
                });
                $('#patientHelpText').text(`${response.data.length} patients available`);
            } else {
                $('#patientHelpText').text('No patients found for this owner/user');
            }
            
            patientSelect.addClass('select2');
            $(document).trigger('patients:loaded');
        },
        error: function() {
            $('#patientHelpText').text('Error loading patients');
        }
    });
}

// Load doctors based on selected owner
function loadDoctorsBy(id, type) {
    let data = { action: 'list' };
    if (type === 'owner') {
        data.owner_id = id;
    } else {
        data.added_by = id;
    }

    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'GET',
        data: data,
        dataType: 'json',
        success: function(response) {
            const doctorSelect = $('#doctorSelect');
            doctorSelect.empty().append('<option value="">Select Doctor</option>');
            
            if (response.success && response.data) {
                response.data.forEach(function(doctor) {
                    doctorSelect.append(`<option value="${doctor.id}">Dr. ${doctor.name}</option>`);
                });
                $('#doctorHelpText').text(`${response.data.length} doctors available`);
            } else {
                $('#doctorHelpText').text('No doctors found for this owner/user');
            }
            
            doctorSelect.addClass('select2');
            $(document).trigger('doctors:loaded');
        },
        error: function() {
            $('#doctorHelpText').text('Error loading doctors');
        }
    });
}

// Load tests for dropdown
function loadTests(callback) {
    $.ajax({
        url: 'ajax/test_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const testSelects = $('.test-select');
                testSelects.each(function() {
                    const $this = $(this);
                    const currentVal = $this.val();
                    $this.empty().append('<option value="">Select Test</option>');
                    response.data.forEach(function(test) {
                        // include category and unit as data attributes for easy population
                        const opt = $(`<option value="${test.id}" data-price="${test.price || 0}" data-unit="${(test.unit||'')}" data-category-id="${test.category_id||''}" data-category-name="${(test.category_name||'')}">${test.name} - ₹${test.price || 0}</option>`);
                        $this.append(opt);
                    });
                    // restore previously selected value if still present
                    if (currentVal) { $this.val(currentVal).trigger('change'); }
                });
            }
            if (typeof callback === 'function') {
                callback();
            }
        }
    });
}

// Setup event handlers
function setupEventHandlers() {
    // Note: form submit handling and validation is handled centrally in
    // umakant/assets/js/entry-form.js which performs validation then calls
    // the page-level saveEntry(form) function. Avoid double-binding here.
    
    $('#entryForm').on('submit', function(e) {
        e.preventDefault();
        saveEntry(this);
    });

    // Delete confirmation
    $('#confirmDelete').on('click', function() {
        if (currentEntryId) {
            performDelete(currentEntryId);
        }
    });
    
    // Test price auto-fill
    $(document).on('change', '.test-select', function() {
        const $opt = $(this).find('option:selected');
        const price = $opt.data('price');
        const unit = $opt.data('unit') || '';
        const categoryName = $opt.data('category-name') || '';
        const categoryId = $opt.data('category-id') || '';

        const $row = $(this).closest('.test-row');
        // sanitize incoming values
        const safePrice = (typeof price !== 'undefined' && price !== null) ? price : '';
        const safeUnit = unit || '';
        const safeCategoryName = categoryName || '';
        const safeCategoryId = categoryId || '';

        // set fields in explicit order matching layout
        if (safePrice !== '') {
            $row.find('input[name*="[price]"]').val(safePrice);
        }
        // Clear result when a new test is selected (user can enter new result)
        $row.find('.test-result').val('');
        $row.find('.test-unit').val(safeUnit);
        $row.find('.test-category').val(safeCategoryName);
        $row.find('.test-category-id').val(safeCategoryId);
    });
    
    // Owner/User selection change - filter patients and doctors
    $(document).on('change', '#ownerAddedBySelect', function() {
        const selectedValue = $(this).val();
        const selectedOption = $(this).find('option:selected');
        const selectedText = selectedOption.text();
        
        if (selectedValue) {
            const id = selectedOption.data('user-id');
            const type = 'user';
            
            if (id) {
                // Enable dropdowns and show loading
                $('#patientSelect, #doctorSelect').prop('disabled', false);
                $('#patientHelpText').text(`Loading patients for: ${selectedText}`);
                $('#doctorHelpText').text(`Loading doctors for: ${selectedText}`);
                
                // Clear current selections
                $('#patientSelect').val('').trigger('change');
                $('#doctorSelect').val('').trigger('change');
                
                // Show loading message
                $('#patientSelect').empty().append('<option value="" disabled>Loading patients...</option>');
                $('#doctorSelect').empty().append('<option value="" disabled>Loading doctors...</option>');
                
                // Load filtered data
                loadPatientsBy(id, type);
                loadDoctorsBy(id, type);
            }
        } else {
            // Disable dropdowns when no owner is selected
            $('#patientSelect, #doctorSelect').prop('disabled', true);
            $('#patientSelect').empty().append('<option value="">Select Owner/User first to load patients</option>');
            $('#doctorSelect').empty().append('<option value="">Select Owner/User first to load doctors</option>');
            $('#patientHelpText').text('Select an owner/user above to load patients');
            $('#doctorHelpText').text('Select an owner/user above to load doctors');
        }
    });

    // When patient selection changes, auto-fill gender, contact and address if available
    $(document).on('change', '#patientSelect', function() {
        const selected = $(this).find('option:selected');
        const gender = selected.data('gender') || '';
        const contact = selected.data('contact') || '';
        const address = selected.data('address') || '';
        try {
            if (gender) { $('#patientGender').val(gender).trigger('change'); } else { $('#patientGender').val(''); }
        } catch(e) { $('#patientGender').val(gender); }
        try { $('#patientContact').val(contact); } catch(e) { /* ignore */ }
        try { $('#patientAddress').val(address); } catch(e) { /* ignore */ }
    });
}

// Open add entry modal
function openAddEntryModal() {
    currentEntryId = null;
    $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');
    // Reset both forms to be safe
    try { if ($('#entryForm').length) { $('#entryForm')[0].reset(); } } catch(e) {}
    try { if ($('#addEntryForm').length) { $('#addEntryForm')[0].reset(); } } catch(e) {}
    $('#entryId').val('');
    $('#entryDate').val(new Date().toISOString().split('T')[0]);
    $('#priority').val('normal');
    // Reset gender
    try { $('#patientGender').val('').trigger('change'); } catch(e) { $('#patientGender').val(''); }
    
    // Reset select2 dropdowns; keep owner selection if already present so
    // patients/doctors can be loaded based on owner. Set default owner to
    // the current user if none is selected.
    $('#patientSelect').val('').trigger('change');
    $('#doctorSelect').val('').trigger('change');
    $('#entryStatus').val('pending').trigger('change');
    
    // Reset additional fields
    $('#patientContact').val('');
    $('#patientAddress').val('');
    $('#referralSource').val('');
    $('#entryNotes').val('');
    
    // Reset tests container
    $('#testsContainer').html(`
        <div class="test-row row mb-2">
                <div class="col-md-3">
                <select class="form-control test-select select2" name="tests[0][test_id]" required>
                    <option value="">Select Test</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control test-category" name="tests[0][category_name]" placeholder="Category" readonly>
                <input type="hidden" name="tests[0][category_id]" class="test-category-id">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control test-result" name="tests[0][result_value]" placeholder="Result">
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-unit" name="tests[0][unit]" placeholder="Unit" readonly>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="tests[0][price]" placeholder="0.00" step="0.01" min="0" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)" title="Remove Test">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `);
    testRowCount = 1;
    
    // Load dropdowns
    loadTests();
    loadUsersDropdown();
    
    // Select2 initialization for modal-contained selects is handled by modal-enhancements.js
    // on modal show. Avoid initializing here to prevent double-init and wrong dropdownParent.
    
    // Set current user as default
    setTimeout(function() {
        if (currentUserId) {
            $('#ownerAddedBySelect').val(`user_${currentUserId}`).trigger('change');
        }
    }, 800);
    
    // If no owner is selected, try to set current user as owner.
    const currentOwnerVal = $('#ownerAddedBySelect').val();
    if (!currentOwnerVal && currentUserId) {
        $('#ownerAddedBySelect').val(`user_${currentUserId}`);
    }

    // If owner options are not yet loaded, wait for the event, otherwise trigger immediately
    const ownerSelect = $('#ownerAddedBySelect');
    if (ownerSelect.find('option').length <= 1) {
        ownerSelect.one('ownerUsers:loaded', function() {
            ownerSelect.trigger('change');
        });
    } else {
        ownerSelect.trigger('change');
    }

    $('#entryModal').modal('show');
}

// Ensure result inputs are enabled when Add/Edit Entry modals are shown (handles initial and reopened modals)
$(document).on('shown.bs.modal', '#entryModal, #addEntryModal', function() {
    $('#testsContainer').find('.test-result').each(function() {
        $(this).prop('disabled', false).prop('readonly', false);
        $(this).removeClass('disabled');
    });
    // ensure units are readonly and not editable
    $('#testsContainer').find('.test-unit').each(function() {
        $(this).prop('readonly', true);
    });
});

// Fallback: on page ready ensure any test-result inputs are visible and enabled
$(function() {
    $('#testsContainer').find('.test-result').each(function() {
        $(this).show().css({ 'display': 'block', 'visibility': 'visible' });
        $(this).prop('disabled', false).prop('readonly', false);
    });
    // ensure units and categories keep readonly but visible
    $('#testsContainer').find('.test-unit, .test-category').each(function() {
        $(this).prop('readonly', true).show().css({ 'display': 'block', 'visibility': 'visible' });
    });
});

// Add test row
function addTestRow() {
    const newRow = `
        <div class="test-row row mb-2">
            <div class="col-md-3">
                <select class="form-control test-select select2" name="tests[${testRowCount}][test_id]" required>
                    <option value="">Select Test</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control test-category" name="tests[${testRowCount}][category_name]" placeholder="Category" readonly>
                <input type="hidden" name="tests[${testRowCount}][category_id]" class="test-category-id">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control test-result" name="tests[${testRowCount}][result_value]" placeholder="Result">
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-unit" name="tests[${testRowCount}][unit]" placeholder="Unit" readonly>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="tests[${testRowCount}][price]" placeholder="0.00" step="0.01" min="0" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)" title="Remove Test">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    $('#testsContainer').append(newRow);
    testRowCount++;
    loadTests();
    // Enable the result input for the newly added row
    setTimeout(function() {
        const $newRow = $('#testsContainer').find('.test-row').last();
        $newRow.find('.test-result').prop('readonly', false).prop('disabled', false);
        $newRow.find('.test-unit').prop('readonly', true);
    }, 50);
}

// Remove test row
function removeTestRow(button) {
    $(button).closest('.test-row').remove();
}

// Save entry
function saveEntry(formElement) {
    // Accept either a form element reference or default to #entryForm
    const $form = formElement ? $(formElement) : $('#entryForm');
    const formData = new FormData($form[0]);
    
    // Process owner/added by field - prefer form-local field, fallback to global selector
    let ownerAddedByValue = $form.find('[name="owner_added_by"]').val();
    if (!ownerAddedByValue) { ownerAddedByValue = $('#ownerAddedBySelect').val(); }
    if (ownerAddedByValue) {
        // Try to read the selected option from the form if present
        let $selectedOption = $form.find('[name="owner_added_by"]').find('option:selected');
        if (!$selectedOption || $selectedOption.length === 0) { $selectedOption = $('#ownerAddedBySelect').find('option:selected'); }
        const type = $selectedOption.data('type');
        if (type === 'owner') {
            const ownerId = $selectedOption.data('owner-id');
            formData.set('owner_id', ownerId);
            formData.set('added_by', ownerId);
        } else if (type === 'user') {
            const userId = $selectedOption.data('user-id');
            formData.set('added_by', userId);
            formData.set('owner_id', userId);
        }
    }
    
    // Convert tests data to JSON (only rows within this form)
    const tests = [];
    // gather test rows: prefer rows inside submitted form; fallback to global testsContainer
    let $testRows = $form.find('.test-row');
    if (!$testRows || $testRows.length === 0) { $testRows = $('#testsContainer').find('.test-row'); }
    $testRows.each(function() {
        const testId = $(this).find('.test-select').val();
    const price = $(this).find('input[name*="[price]"]').val();
    // discount column removed from UI; set to 0 by default
    const discount = 0;
        const resultVal = $(this).find('.test-result').val();
        const unitVal = $(this).find('.test-unit').val() || '';
        const categoryName = $(this).find('.test-category').val() || '';
        const categoryId = $(this).find('.test-category-id').val() || '';
        const testName = $(this).find('.test-select option:selected').text() || '';

        if (testId && price) {
            tests.push({
                test_id: testId,
                test_name: testName,
                price: parseFloat(price),
                discount_amount: parseFloat(discount || 0),
                result_value: resultVal || null,
                unit: unitVal,
                category_id: categoryId,
                category_name: categoryName
            });
        }
    });
    
    formData.set('tests', JSON.stringify(tests));

    // Ensure server executes save branch
    formData.set('action', 'save');

    // Debug: log the outgoing tests payload
    try { console.debug('Saving entry tests payload:', tests); } catch(e) {}
    // Prevent duplicate submissions
    if (window.entrySaving) {
        toastr.info('Save in progress, please wait...');
        return;
    }
    window.entrySaving = true;
    $('.btn-save-entry').prop('disabled', true).addClass('disabled');

    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                toastr.success(response.message || 'Entry saved successfully');
                $('#entryModal').modal('hide');
                refreshTable();
                loadStatistics();
            } else {
                toastr.error(response.message || 'Failed to save entry');
            }
        },
        error: function(xhr) {
            var msg = 'An error occurred while saving the entry';
            try { if (xhr && xhr.responseText) msg += ': ' + xhr.responseText; } catch(e) {}
            toastr.error(msg);
            try { console.error('Save entry error', xhr); } catch(e) {}
        },
        complete: function() {
            window.entrySaving = false;
            $('.btn-save-entry').prop('disabled', false).removeClass('disabled');
        }
    });
}

// View entry
function viewEntry(id) {
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayEntryDetails(response.data);
                $('#viewEntryModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load entry details');
            }
        }
    });
}

// Display entry details
function displayEntryDetails(entry) {
    const details = `
        <div class="row">
            <div class="col-md-6">
                <h6><strong>Entry Information</strong></h6>
                <p><strong>ID:</strong> #${entry.id}</p>
                <p><strong>Date:</strong> ${new Date(entry.entry_date).toLocaleDateString('en-IN')}</p>
                <p><strong>Status:</strong> <span class="badge badge-${entry.status === 'completed' ? 'success' : entry.status === 'pending' ? 'warning' : 'danger'}">${entry.status}</span></p>
                <p><strong>Priority:</strong> <span class="badge badge-${entry.priority === 'urgent' ? 'danger' : entry.priority === 'emergency' ? 'warning' : 'info'}">${entry.priority || 'normal'}</span></p>
                <p><strong>Tests Count:</strong> ${entry.tests_count || 0}</p>
                <p><strong>Added By:</strong> ${entry.added_by_username || 'N/A'}</p>
            </div>
            <div class="col-md-6">
                <h6><strong>Patient Information</strong></h6>
                <p><strong>Name:</strong> ${entry.patient_name || 'N/A'}</p>
                <p><strong>UHID:</strong> ${entry.uhid || 'N/A'}</p>
                <p><strong>Age/Gender:</strong> ${entry.age ? entry.age + ' ' + (entry.gender || '') : 'N/A'}</p>
                ${entry.patient_contact ? `<p><strong>Contact:</strong> ${entry.patient_contact}</p>` : ''}
                ${entry.patient_address ? `<p><strong>Address:</strong> ${entry.patient_address}</p>` : ''}
                <p><strong>Doctor:</strong> ${entry.doctor_name || 'Not assigned'}</p>
                ${entry.owner_name ? `<p><strong>Owner/Lab:</strong> ${entry.owner_name}</p>` : ''}
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6><strong>Tests & Pricing</strong></h6>
                <p><strong>Tests:</strong> ${entry.test_names || 'No tests'}</p>
                <p><strong>Total Amount:</strong> ₹${parseFloat(entry.final_amount || 0).toFixed(2)}</p>
            </div>
            <div class="col-md-6">
                <h6><strong>Additional Information</strong></h6>
                ${entry.referral_source ? `<p><strong>Referral Source:</strong> ${entry.referral_source}</p>` : ''}
                ${entry.notes ? `<p><strong>Notes:</strong> ${entry.notes}</p>` : ''}
                <p><strong>Created:</strong> ${new Date(entry.created_at).toLocaleString('en-IN')}</p>
            </div>
        </div>
    `;
    $('#entryDetails').html(details);
}

// Edit entry
function editEntry(id) {
    // Similar to view but populate form for editing
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateEditForm(response.data);
                $('#entryModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load entry for editing');
            }
        }
    });
}

// Populate edit form
function populateEditForm(entry) {
    currentEntryId = entry.id;
    $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');
    $('#entryId').val(entry.id);

    // Set up event handlers to catch when dropdowns are loaded
    $(document).one('patients:loaded', function() {
        $('#patientSelect').val(entry.patient_id).trigger('change');
        
        // Populate gender field from entry if present
        if (entry.gender) {
            try {
                $('#patientGender').val(entry.gender).trigger('change');
            } catch (e) {
                $('#patientGender').val(entry.gender);
            }
        }
    });

    $(document).one('doctors:loaded', function() {
        $('#doctorSelect').val(entry.doctor_id).trigger('change');
    });

    loadUsersDropdown(function() {
        // Set owner/added by first, then load patients and doctors
        let ownerAddedByValue = '';
        if (entry.added_by) {
            ownerAddedByValue = `user_${entry.added_by}`;
        }

        $('#ownerAddedBySelect').val(ownerAddedByValue).trigger('change');
    });

    // Set other fields
    if (entry.entry_date) {
        const date = new Date(entry.entry_date);
        const formattedDate = date.toISOString().split('T')[0];
        $('#entryDate').val(formattedDate);
    }
    $('#entryStatus').val(entry.status);
    $('#entryNotes').val(entry.notes || '');

    // Populate additional fields
    $('#patientContact').val(entry.patient_contact || '');
    $('#patientAddress').val(entry.patient_address || '');
    $('#referralSource').val(entry.referral_source || '');
    $('#priority').val(entry.priority || 'normal');

    

    // Populate tests section
    const testsContainer = $('#testsContainer');
    testsContainer.empty();
    testRowCount = 0;

    if (entry.tests && entry.tests.length > 0) {
        entry.tests.forEach(function(test, index) {
            const newRowHTML = `
                <div class="test-row row mb-2">
                    <div class="col-md-3">
                        <select class="form-control test-select select2" name="tests[${index}][test_id]" required>
                            <option value="">Select Test</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control test-category" name="tests[${index}][category_name]" placeholder="Category" readonly value="${test.category_name || ''}">
                        <input type="hidden" name="tests[${index}][category_id]" class="test-category-id" value="${test.category_id || ''}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control test-result" name="tests[${index}][result_value]" placeholder="Result" value="${test.result_value || ''}">
                    </div>
                    <div class="col-md-1">
                        <input type="text" class="form-control test-unit" name="tests[${index}][unit]" placeholder="Unit" readonly value="${test.unit || ''}">
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="tests[${index}][price]" placeholder="0.00" step="0.01" min="0" required value="${test.price || ''}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)" title="Remove Test">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            testsContainer.append(newRowHTML);
        });
        testRowCount = entry.tests.length;
    } else {
        addTestRow(); // Add a blank row if no tests
    }

    // Load dropdowns
    loadTests(function() {
        if (entry.tests && entry.tests.length > 0) {
            entry.tests.forEach(function(test, index) {
                const testRow = testsContainer.find('.test-row').eq(index);
                const testSelect = testRow.find('.test-select');
                if (test.test_id) {
                    testSelect.val(test.test_id).trigger('change');
                }
            });
        }
    });
}

// Delete entry
function deleteEntry(id) {
    currentEntryId = id;
    $('#deleteModal').modal('show');
}

// Perform delete
function performDelete(id) {
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'POST',
        data: { action: 'delete', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                toastr.success(response.message || 'Entry deleted successfully');
                $('#deleteModal').modal('hide');
                refreshTable();
                loadStatistics();
            } else {
                toastr.error(response.message || 'Failed to delete entry');
            }
        },
        error: function() {
            toastr.error('An error occurred while deleting the entry');
        }
    });
}

// Apply filters
function applyFilters() {
    const status = $('#statusFilter').val();
    const dateFilter = $('#dateFilter').val();
    const patient = $('#patientFilter').val();
    const doctor = $('#doctorFilter').val();
    
    // Add custom filtering logic here
    // For now, we'll use DataTables built-in search
    let searchTerm = '';
    if (patient) searchTerm += patient + ' ';
    if (doctor) searchTerm += doctor + ' ';
    
    entriesTable.search(searchTerm).draw();
}

// Filter by status
function filterByStatus(status) {
    $('#statusFilter').val(status).trigger('change');
    applyFilters();
}

// Filter by date
function filterByDate(dateFilter) {
    $('#dateFilter').val(dateFilter).trigger('change');
    applyFilters();
}

// Export entries
function exportEntries() {
    // Show export options modal
    const status = $('#statusFilter').val();
    const dateFilter = $('#dateFilter').val();
    
    const exportModal = `
        <div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title"><i class="fas fa-download mr-1"></i>Export Entries</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Export Format:</label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="exportFormat" value="csv" checked> CSV
                                </label>
                                <label class="btn btn-outline-success">
                                    <input type="radio" name="exportFormat" value="json"> JSON
                                </label>
                                <label class="btn btn-outline-info">
                                    <input type="radio" name="exportFormat" value="excel"> Excel
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Export Options:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeFilters" checked>
                                <label class="form-check-label" for="includeFilters">
                                    Include current filters
                                </label>
                            </div>
                        </div>
                        ${status || dateFilter ? `
                        <div class="alert alert-info">
                            <strong>Current Filters:</strong><br>
                            ${status ? `Status: ${status}<br>` : ''}
                            ${dateFilter ? `Date: ${dateFilter}<br>` : ''}
                        </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" onclick="performExport('${status}', '${dateFilter}')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#exportModal').remove();
    
    // Add modal to body
    $('body').append(exportModal);
    
    // Show modal
    $('#exportModal').modal('show');
}

// Perform export
function performExport(status, dateFilter) {
    const format = $('input[name="exportFormat"]:checked').val();
    const includeFilters = $('#includeFilters').is(':checked');
    
    let exportUrl = `ajax/entry_api_fixed.php?action=export&format=${format}`;
    
    if (includeFilters) {
        if (status) exportUrl += `&status=${status}`;
        if (dateFilter) exportUrl += `&date=${dateFilter}`;
    }
    
    // Close modal
    $('#exportModal').modal('hide');
    
    // Show loading
    toastr.info('Preparing export...', 'Export', {timeOut: 2000});
    
    if (format === 'csv') {
        // Direct download for CSV
        window.open(exportUrl, '_blank');
    } else {
        // Handle other formats
        $.ajax({
            url: exportUrl,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (format === 'json') {
                        // Download JSON file
                        const blob = new Blob([JSON.stringify(response.data, null, 2)], {type: 'application/json'});
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `entries_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.json`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                    } else if (format === 'excel') {
                        // Convert to Excel format (simplified - would need a proper Excel library for full functionality)
                        exportToExcel(response.data);
                    }
                    toastr.success(`Export completed successfully! ${response.total} entries exported.`);
                } else {
                    toastr.error(response.message || 'Export failed');
                }
            },
            error: function() {
                toastr.error('Export failed. Please try again.');
            }
        });
    }
}

// Export to Excel (simplified version)
function exportToExcel(data) {
    // Create HTML table for Excel
    let html = '<table border="1"><tr>';
    
    // Headers
    if (data.length > 0) {
        Object.keys(data[0]).forEach(key => {
            html += `<th>${key}</th>`;
        });
        html += '</tr>';
        
        // Data rows
        data.forEach(row => {
            html += '<tr>';
            Object.values(row).forEach(value => {
                html += `<td>${value}</td>`;
            });
            html += '</tr>';
        });
    }
    
    html += '</table>';
    
    // Create and download file
    const blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `entries_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Refresh table
function refreshTable() {
    entriesTable.ajax.reload();
}

// Add fallback for owner/users if API fails (kept separate from Select2 init)
setTimeout(function() {
    const ownerSelect = $('#ownerAddedBySelect');
    if (ownerSelect.find('option').length <= 1) {
        // If no options loaded, add current user as fallback
        if (currentUserId) {
            ownerSelect.append(`<option value="user_${currentUserId}" data-type="user" data-user-id="${currentUserId}">👤 ${currentUserDisplayName} (Current User)</option>`);
            ownerSelect.val(`user_${currentUserId}`).trigger('change');
        }
    }
}, 2000);
