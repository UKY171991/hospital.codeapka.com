// Global variables
let entriesTable;
let currentEntryId = null;
let testRowCount = 1;

// Safety check for required libraries
function checkDependencies() {
    const missing = [];
    
    if (typeof $ === 'undefined') missing.push('jQuery');
    if (typeof $.fn.dataTable === 'undefined') missing.push('DataTables');
    if (typeof $.fn.select2 === 'undefined') missing.push('Select2');
    if (typeof toastr === 'undefined') missing.push('Toastr');
    
    if (missing.length > 0) {
        console.error('Missing required libraries:', missing.join(', '));
        alert('Error: Missing required libraries. Please refresh the page.');
        return false;
    }
    return true;
}

// Initialize page when document is ready
$(document).ready(function() {
    // Check dependencies first
    if (!checkDependencies()) {
        return;
    }
    
    try {
        initializePage();
    } catch (error) {
        console.error('Error initializing page:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('Error initializing page. Please refresh and try again.');
        } else {
            alert('Error initializing page. Please refresh and try again.');
        }
    }
});

// Initialize page components
function initializePage() {
    try {
        console.log('Initializing entry list page...');
        loadStatistics();
        initializeDataTable();
        loadOwnerUsers();
        loadTests();
        setupEventHandlers();
        console.log('Entry list page initialized successfully');
    } catch (error) {
        console.error('Error in initializePage:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to initialize page components');
        }
    }
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
    try {
        // Check if table element exists
        if ($('#entriesTable').length === 0) {
            console.error('Entries table element not found');
            return;
        }
        
        // Avoid re-initializing DataTable if another script already initialized it.
        if (window._entriesTableInitialized || (typeof $.fn.dataTable !== 'undefined' && $.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable('#entriesTable'))) {
            try { 
                entriesTable = $('#entriesTable').DataTable(); 
                console.log('DataTable already initialized, reusing existing instance');
            } catch(e) { 
                console.warn('Failed to get existing DataTable instance:', e);
            }
            window._entriesTableInitialized = true;
            return;
        }
        
        console.log('Initializing new DataTable...');
    } catch (error) {
        console.error('Error in initializeDataTable setup:', error);
        return;
    }

    try {
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
                data: 'added_by_full_name',
                render: function(data, type, row) {
                    return data || row.added_by_username || '<span class="text-muted">Unknown</span>';
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
        order: [[6, 'desc']], // Sort by date descending (column 6 is Date)
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
        console.log('DataTable initialized successfully');
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        window._entriesTableInitialized = false;
        
        // Show user-friendly error
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to initialize data table. Please refresh the page.');
        } else {
            alert('Failed to initialize data table. Please refresh the page.');
        }
        
        // Try to show a basic table without DataTable features
        try {
            $('#entriesTable').show();
        } catch (e) {
            console.error('Failed to show basic table:', e);
        }
    }
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
function loadOwnerUsers() {
    const ownerUserSelect = $('#ownerAddedBySelect');
    ownerUserSelect.empty().append('<option value="">Select Owner/User</option>');
    
    // Load owners
    $.ajax({
        url: 'ajax/owner_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(ownerResponse) {
            console.log('Owner response:', ownerResponse);
            
            // Add owners first
            if (ownerResponse.success && ownerResponse.data && ownerResponse.data.length > 0) {
                ownerResponse.data.forEach(function(owner) {
                    ownerUserSelect.append(`<option value="owner_${owner.id}" data-type="owner" data-owner-id="${owner.id}">🏢 ${owner.name} (Owner)</option>`);
                });
            } else {
                // If no owners, add a placeholder
                ownerUserSelect.append(`<option value="" disabled>No owners available</option>`);
            }
            
            // Load users
            $.ajax({
                url: 'ajax/user_api.php',
                method: 'GET',
                data: { action: 'list_simple' },
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
                        ownerUserSelect.append(`<option value="" disabled>No users available</option>`);
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
                },
                error: function(xhr, status, error) {
                    console.error('Error loading users:', error);
                    ownerUserSelect.append(`<option value="" disabled>Error loading users</option>`);
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading owners:', error);
            ownerUserSelect.append(`<option value="" disabled>Error loading owners</option>`);
        }
    });
}

// Load patients based on selected owner
function loadPatientsByOwner(ownerId, callback) {
    console.log('Loading patients for owner ID:', ownerId);
    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'GET',
        data: { action: 'list', owner_id: ownerId },
        dataType: 'json',
        success: function(response) {
            const patientSelect = $('#patientSelect');
            patientSelect.empty().append('<option value="">Select Patient</option>');
            
                if (response.success && response.data) {
                response.data.forEach(function(patient) {
                    const genderVal = patient.gender || patient.sex || '';
                    const contactVal = (patient.contact || patient.phone || patient.mobile || '');
                    const addressVal = (patient.address || patient.address_line || '');
                    const ageVal = patient.age || '';
                    
                    // Create option with proper data attributes
                    const option = $('<option>', {
                        value: patient.id,
                        text: `${patient.name} (${patient.uhid || 'No UHID'})`,
                        'data-gender': genderVal,
                        'data-contact': contactVal,
                        'data-address': addressVal,
                        'data-age': ageVal
                    });
                    
                    patientSelect.append(option);
                });
                $('#patientHelpText').text(`${response.data.length} patients available`);
            } else {
                $('#patientHelpText').text('No patients found for this owner/user');
            }
            
            patientSelect.addClass('select2');
            
            // Execute callback if provided
            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function() {
            $('#patientHelpText').text('Error loading patients');
            
            // Execute callback even on error
            if (typeof callback === 'function') {
                callback();
            }
        }
    });
}

// Load doctors based on selected owner
function loadDoctorsByOwner(ownerId, callback) {
    console.log('Loading doctors for owner ID:', ownerId);
    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'GET',
        data: { action: 'list', added_by: ownerId },
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
            
            // Execute callback if provided
            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function() {
            $('#doctorHelpText').text('Error loading doctors');
            
            // Execute callback even on error
            if (typeof callback === 'function') {
                callback();
            }
        }
    });
}

// Load tests for dropdown
function loadTests(callback) {
    $.ajax({
        url: 'ajax/test_api.php',
        method: 'GET',
        data: { action: 'simple_list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log('Tests loaded successfully:', response.data.length, 'tests');
                if (response.data.length > 0) {
                    console.log('First test sample:', response.data[0]);
                    console.log('First test price:', response.data[0].price);
                    
                    // Check if ANY tests have prices
                    const testsWithPrices = response.data.filter(t => t.price && t.price > 0);
                    console.log('Tests with prices > 0:', testsWithPrices.length);
                    if (testsWithPrices.length === 0) {
                        console.error('⚠️ WARNING: NO TESTS HAVE PRICES! All tests have price = 0 or null');
                        alert('WARNING: Tests do not have prices set in the database. Please add prices to tests first.');
                    }
                }
                
                const testSelects = $('.test-select');
                testSelects.each(function() {
                    const $this = $(this);
                    const currentVal = $this.val();
                    $this.empty().append('<option value="">Select Test</option>');
                    response.data.forEach(function(test) {
                        // include category, unit, reference range, and min/max as data attributes for easy population
                        // Properly escape HTML attributes to handle special characters
                        const escapedUnit = (test.unit || '').replace(/"/g, '&quot;');
                        const escapedRefRange = (test.reference_range || '').replace(/"/g, '&quot;');
                        const escapedCategoryName = (test.category_name || '').replace(/"/g, '&quot;');
                        const escapedTestName = (test.name || '').replace(/"/g, '&quot;');
                        
                        const opt = $(`<option value="${test.id}" data-price="${test.price || 0}" data-unit="${escapedUnit}" data-reference-range="${escapedRefRange}" data-min="${test.min || ''}" data-max="${test.max || ''}" data-category-id="${test.category_id||''}" data-category-name="${escapedCategoryName}">${escapedTestName} - ₹${test.price || 0}</option>`);
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
        const min = $opt.data('min') || '';
        const max = $opt.data('max') || '';
        const categoryName = $opt.data('category-name') || '';
        const categoryId = $opt.data('category-id') || '';

        const $row = $(this).closest('.test-row');
        // sanitize incoming values
        const safePrice = (typeof price !== 'undefined' && price !== null) ? price : '';
        const safeUnit = unit || '';
        const safeMin = min || '';
        const safeMax = max || '';
        const safeCategoryName = categoryName || '';
        const safeCategoryId = categoryId || '';

        // Debug logging
        console.log('Test selection change:', {
            testId: $opt.val(),
            testName: $opt.text(),
            price: safePrice,
            unit: safeUnit,
            min: safeMin,
            max: safeMax,
            categoryName: safeCategoryName
        });

        // Only clear result if it's not already populated (avoid clearing during edit mode)
        const currentResult = $row.find('.test-result').val();
        if (!currentResult) {
            $row.find('.test-result').val('');
        }
        
        // Populate fields and ensure they're visible
        $row.find('.test-unit').val(safeUnit).prop('readonly', true).show().css({ 'display': 'block', 'visibility': 'visible' });
        $row.find('.test-min').val(safeMin).prop('readonly', true).show().css({ 'display': 'block', 'visibility': 'visible' });
        $row.find('.test-max').val(safeMax).prop('readonly', true).show().css({ 'display': 'block', 'visibility': 'visible' });
        $row.find('.test-category').val(safeCategoryName).prop('readonly', true).show().css({ 'display': 'block', 'visibility': 'visible' });
        $row.find('.test-category-id').val(safeCategoryId);
        
        // Enable result input
        $row.find('.test-result').prop('readonly', false).prop('disabled', false);
        
        // Update pricing fields
        updatePricingFields();
    });
    
    // Owner/User selection change - filter patients and doctors
    $(document).on('change', '#ownerAddedBySelect', function() {
        const selectedValue = $(this).val();
        const selectedOption = $(this).find('option:selected');
        const selectedText = selectedOption.text();
        
        if (selectedValue) {
            const type = selectedOption.data('type');
            let ownerId = null;
            
            if (type === 'owner') {
                ownerId = selectedOption.data('owner-id');
            } else if (type === 'user') {
                // For users, we might want to get their associated owner
                // For now, we'll use the user ID as owner ID
                ownerId = selectedOption.data('user-id');
            }
            
            if (ownerId) {
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
                loadPatientsByOwner(ownerId);
                loadDoctorsByOwner(ownerId);
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

    // When patient selection changes, auto-fill age, gender, contact and address from patient data
    $(document).on('change', '#patientSelect', function() {
        const selected = $(this).find('option:selected');
        const age = selected.data('age') || '';
        const gender = selected.data('gender') || '';
        const contact = selected.data('contact') || '';
        const address = selected.data('address') || '';
        
        // Populate age field
        $('#patientAge').val(age);
        
        // Populate gender field
        if (gender) {
            $('#patientGender').val(gender).trigger('change');
        } else {
            $('#patientGender').val('');
        }
        
        // Populate contact and address
        $('#patientContact').val(contact);
        $('#patientAddress').val(address);
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
    $('#patientAge').val('');
    $('#patientContact').val('');
    $('#patientAddress').val('');
    $('#referralSource').val('');
    $('#entryNotes').val('');
    
    // Reset pricing fields
    $('#subtotal').val('');
    $('#discountAmount').val('');
    $('#totalPrice').val('');
    
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
                <input type="text" class="form-control test-min" name="tests[0][min]" placeholder="Min" readonly>
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-max" name="tests[0][max]" placeholder="Max" readonly>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control test-unit" name="tests[0][unit]" placeholder="Unit" readonly>
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
    loadOwnerUsers();
    
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
    // ensure units, categories, and min/max are readonly and not editable
    $('#testsContainer').find('.test-unit, .test-category, .test-min, .test-max').each(function() {
        $(this).prop('readonly', true).show().css({ 'display': 'block', 'visibility': 'visible' });
    });
    
    // Debug: Check if pricing fields exist
    console.log('Modal shown - pricing fields check:', {
        subtotal: $('#subtotal').length,
        discountAmount: $('#discountAmount').length,
        totalPrice: $('#totalPrice').length,
        currentValues: {
            subtotal: $('#subtotal').val(),
            discount: $('#discountAmount').val(),
            total: $('#totalPrice').val()
        }
    });
    
    // Force calculate pricing when modal is shown
    setTimeout(function() {
        updatePricingFields();
        console.log('Pricing fields after modal shown:', {
            subtotal: $('#subtotal').val(),
            discount: $('#discountAmount').val(),
            total: $('#totalPrice').val()
        });
    }, 100);
});

// Fallback: on page ready ensure any test-result inputs are visible and enabled
$(function() {
    $('#testsContainer').find('.test-result').each(function() {
        $(this).show().css({ 'display': 'block', 'visibility': 'visible' });
        $(this).prop('disabled', false).prop('readonly', false);
    });
    // ensure units, categories, and min/max keep readonly but visible
    $('#testsContainer').find('.test-unit, .test-category, .test-min, .test-max').each(function() {
        $(this).prop('readonly', true).show().css({ 'display': 'block', 'visibility': 'visible' });
    });
});

// Duplicate function removed - using the complete implementation below

// Update pricing fields based on selected tests
function updatePricingFields() {
    let subtotal = 0;
    let discount = 0;
    
    // Calculate subtotal from test prices
    $('.test-select').each(function() {
        const $opt = $(this).find('option:selected');
        const price = parseFloat($opt.data('price') || 0);
        if (price > 0) {
            console.log('Test selected:', $opt.text(), 'Price:', price);
        }
        subtotal += price;
    });
    
    // Get discount amount
    discount = parseFloat($('#discountAmount').val() || 0);
    
    // Calculate total
    const total = Math.max(subtotal - discount, 0);
    
    console.log('Pricing calculated:', {
        subtotal: subtotal.toFixed(2),
        discount: discount.toFixed(2),
        total: total.toFixed(2)
    });
    
    // Update fields
    $('#subtotal').val(subtotal.toFixed(2));
    $('#totalPrice').val(total.toFixed(2));
    
    // Force trigger change event so any listeners know the values changed
    $('#subtotal, #totalPrice').trigger('change');
}

// Handle discount amount change
$(document).on('input', '#discountAmount', function() {
    updatePricingFields();
});

// Save entry
function saveEntry(formElement) {
    // Accept either a form element reference or default to #entryForm
    const $form = formElement ? $(formElement) : $('#entryForm');
    
    // IMPORTANT: Calculate pricing FIRST before creating FormData
    // This ensures the fields have values before we serialize the form
    updatePricingFields();
    
    // Give the DOM a moment to update the field values
    setTimeout(function() {
        continueWithSave($form);
    }, 50);
}

function continueWithSave($form) {
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
        const resultVal = $(this).find('.test-result').val();
        const unitVal = $(this).find('.test-unit').val() || '';
        const categoryName = $(this).find('.test-category').val() || '';
        const categoryId = $(this).find('.test-category-id').val() || '';
        const $selectedTest = $(this).find('.test-select option:selected');
        const testName = $selectedTest.text() || '';
        const testPrice = parseFloat($selectedTest.data('price') || 0);

        if (testId) {
            tests.push({
                test_id: testId,
                test_name: testName,
                result_value: resultVal || null,
                unit: unitVal,
                category_id: categoryId,
                category_name: categoryName,
                price: testPrice,
                discount_amount: 0 // Individual test discount is 0, we use global discount
            });
        }
    });
    
    formData.set('tests', JSON.stringify(tests));
    
    // FORCE read pricing field values directly from DOM to ensure we have the latest values
    const subtotalVal = parseFloat($('#subtotal').val() || 0).toFixed(2);
    const discountVal = parseFloat($('#discountAmount').val() || 0).toFixed(2);
    const totalVal = parseFloat($('#totalPrice').val() || 0).toFixed(2);
    
    console.log('=== SAVING ENTRY - PRICING DEBUG ===');
    console.log('Pricing field values from DOM:', {
        subtotal: subtotalVal,
        discount: discountVal,
        total: totalVal
    });
    
    // WARNING if pricing is 0
    if (parseFloat(subtotalVal) === 0 && tests.length > 0) {
        console.error('⚠️ WARNING: Saving entry with ZERO subtotal despite having tests!');
        console.error('Tests selected:', tests.length);
        console.error('Test prices:', tests.map(t => ({ id: t.test_id, name: t.test_name, price: t.price })));
        
        // Check if tests actually have prices
        const totalTestPrices = tests.reduce((sum, t) => sum + (parseFloat(t.price) || 0), 0);
        console.error('Sum of test prices:', totalTestPrices);
        
        if (totalTestPrices === 0) {
            alert('⚠️ ERROR: Tests do not have prices!\n\nThe tests you selected have price = 0 in the database.\nPlease set prices for tests before creating entries.');
            window.entrySaving = false;
            $('.btn-save-entry').prop('disabled', false).removeClass('disabled');
            return;
        }
    }
    
    // Ensure pricing fields are explicitly included with correct values
    formData.set('subtotal', subtotalVal);
    formData.set('discount_amount', discountVal);
    formData.set('total_price', totalVal);

    // Ensure server executes save branch
    formData.set('action', 'save');

    // Debug: log the outgoing payload
    console.log('Saving entry - tests count:', tests.length);
    console.log('FormData pricing being sent:');
    console.log('  subtotal:', formData.get('subtotal'));
    console.log('  discount_amount:', formData.get('discount_amount'));
    console.log('  total_price:', formData.get('total_price'));
    
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
                console.log('✅ Entry saved successfully!');
                console.log('Server response:', response);
                
                // Check if pricing was actually saved
                if (response.data && response.data.saved_pricing) {
                    const saved = response.data.saved_pricing;
                    console.log('💰 PRICING SAVED TO DATABASE:', {
                        subtotal: saved.subtotal,
                        discount: saved.discount_amount,
                        total: saved.total_price
                    });
                    
                    if (saved.subtotal == 0 && saved.total_price == 0) {
                        console.error('⚠️ WARNING: Database shows ZERO pricing despite saving!');
                        console.error('This means the data reached the server but was not saved correctly.');
                    } else {
                        console.log('✅ Pricing verified in database!');
                    }
                }
                
                toastr.success(response.message || 'Entry saved successfully');
                $('#entryModal').modal('hide');
                refreshTable();
                loadStatistics();
            } else {
                toastr.error(response.message || 'Failed to save entry');
                console.error('Save failed:', response);
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
    // Validate ID
    if (!id) {
        toastr.error('Invalid entry ID');
        return;
    }
    
    // Check if modal exists
    if ($('#viewEntryModal').length === 0) {
        console.error('View entry modal not found in DOM');
        toastr.error('Modal element not found. Please refresh the page.');
        return;
    }
    
    // Show loading indicator
    toastr.info('Loading entry details...', '', {timeOut: 1000});
    
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
        },
        error: function(xhr, status, error) {
            console.error('View entry error:', xhr, status, error);
            let errorMsg = 'Failed to load entry details. ';
            if (xhr.status === 401 || xhr.status === 403) {
                errorMsg += 'Please log in again.';
            } else if (xhr.status === 404) {
                errorMsg += 'Entry not found.';
            } else if (xhr.status === 500) {
                errorMsg += 'Server error occurred.';
            } else {
                errorMsg += 'Please try again.';
            }
            toastr.error(errorMsg);
        }
    });
}

// Display entry details
function displayEntryDetails(entry) {
    if (!entry) {
        toastr.error('No entry data received');
        return;
    }
    
    // Format date safely
    let entryDate = 'N/A';
    try {
        if (entry.entry_date) {
            entryDate = new Date(entry.entry_date).toLocaleDateString('en-IN', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    } catch (e) {
        console.error('Error formatting entry date:', e);
    }
    
    // Format created date safely
    let createdDate = 'N/A';
    try {
        if (entry.created_at) {
            createdDate = new Date(entry.created_at).toLocaleString('en-IN', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    } catch (e) {
        console.error('Error formatting created date:', e);
    }
    
    // Format updated date if available
    let updatedDate = '';
    try {
        if (entry.updated_at && entry.updated_at !== entry.created_at) {
            updatedDate = new Date(entry.updated_at).toLocaleString('en-IN', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    } catch (e) {
        console.error('Error formatting updated date:', e);
    }
    
    // Build individual tests table if available
    let testsTable = '';
    if (entry.tests && Array.isArray(entry.tests) && entry.tests.length > 0) {
        testsTable = `
            <div class="col-12 mt-3">
                <h6><strong><i class="fas fa-flask mr-2"></i>Test Details</strong></h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Test Name</th>
                                    <th>Category</th>
                                    <th>Result</th>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        <tbody>
        `;
        
            entry.tests.forEach(function(test) {
                const testStatus = test.status || 'pending';
                const statusBadge = testStatus === 'completed' ? 'success' : (testStatus === 'pending' ? 'warning' : 'secondary');
                
                // Get patient gender to determine which min/max values to use
                const patientGender = entry.gender || entry.sex;
                let minValue = test.min || '-';
                let maxValue = test.max || '-';
                
                // If patient has gender and gender-specific values exist, use them
                if (patientGender && patientGender.toLowerCase() === 'male' && test.min_male !== null && test.max_male !== null) {
                    minValue = test.min_male || '-';
                    maxValue = test.max_male || '-';
                } else if (patientGender && patientGender.toLowerCase() === 'female' && test.min_female !== null && test.max_female !== null) {
                    minValue = test.min_female || '-';
                    maxValue = test.max_female || '-';
                }
                
                testsTable += `
                    <tr>
                        <td>${test.test_name || 'N/A'}</td>
                        <td>${test.category_name || 'N/A'}</td>
                        <td>${test.result_value || '-'}</td>
                        <td>${minValue}</td>
                        <td>${maxValue}</td>
                        <td>${test.unit || '-'}</td>
                        <td><span class="badge badge-${statusBadge}">${testStatus}</span></td>
                    </tr>
                `;
            });
        
        testsTable += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    } else {
        // Fallback to showing test names as a list
        testsTable = `
            <div class="col-12 mt-3">
                <h6><strong><i class="fas fa-flask mr-2"></i>Tests</strong></h6>
                <p class="text-muted">${entry.test_names || 'No tests available'}</p>
            </div>
        `;
    }
    
    const details = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary"><strong><i class="fas fa-info-circle mr-2"></i>Entry Information</strong></h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Entry ID:</strong></td>
                        <td><span class="badge badge-primary">#${entry.id || 'N/A'}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Entry Date:</strong></td>
                        <td>${entryDate}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge badge-${entry.status === 'completed' ? 'success' : entry.status === 'pending' ? 'warning' : 'danger'}">${(entry.status || 'N/A').toUpperCase()}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Priority:</strong></td>
                        <td><span class="badge badge-${entry.priority === 'urgent' ? 'danger' : entry.priority === 'emergency' ? 'warning' : 'info'}">${(entry.priority || 'normal').toUpperCase()}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Tests Count:</strong></td>
                        <td><span class="badge badge-info">${entry.tests_count || 0} Test(s)</span></td>
                    </tr>
                    ${entry.referral_source ? `
                    <tr>
                        <td><strong>Referral Source:</strong></td>
                        <td>${entry.referral_source}</td>
                    </tr>
                    ` : ''}
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-success"><strong><i class="fas fa-user-injured mr-2"></i>Patient Information</strong></h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Patient Name:</strong></td>
                        <td>${entry.patient_name || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>UHID:</strong></td>
                        <td><span class="badge badge-secondary">${entry.uhid || 'N/A'}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Age/Gender:</strong></td>
                        <td>
                            ${(() => {
                                const age = entry.patient_age || entry.age;
                                const gender = entry.gender || entry.sex;
                                if (age && gender) {
                                    return `${age} years / ${gender}`;
                                } else if (age) {
                                    return `${age} years`;
                                } else if (gender) {
                                    return gender;
                                } else {
                                    return 'N/A';
                                }
                            })()}
                        </td>
                    </tr>
                    ${entry.patient_contact ? `
                    <tr>
                        <td><strong>Contact:</strong></td>
                        <td><i class="fas fa-phone mr-1"></i>${entry.patient_contact}</td>
                    </tr>
                    ` : ''}
                    ${entry.patient_address ? `
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td><i class="fas fa-map-marker-alt mr-1"></i>${entry.patient_address}</td>
                    </tr>
                    ` : ''}
                    <tr>
                        <td><strong>Doctor:</strong></td>
                        <td>${entry.doctor_name ? '<i class="fas fa-user-md mr-1"></i>Dr. ' + entry.doctor_name : '<span class="text-muted">Not assigned</span>'}</td>
                    </tr>
                    ${entry.owner_name ? `
                    <tr>
                        <td><strong>Owner/Lab:</strong></td>
                        <td><i class="fas fa-hospital mr-1"></i>${entry.owner_name}</td>
                    </tr>
                    ` : ''}
                </table>
            </div>
        </div>
        
        <hr class="my-3">
        
        <div class="row">
            ${testsTable}
        </div>
        
        <hr class="my-3">
        
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-info"><strong><i class="fas fa-money-bill-wave mr-2"></i>Pricing Information</strong></h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Subtotal:</strong></td>
                        <td>₹${parseFloat(entry.subtotal || entry.aggregated_total_price || 0).toFixed(2)}</td>
                    </tr>
                    ${entry.discount_amount || entry.aggregated_total_discount ? `
                    <tr>
                        <td><strong>Discount:</strong></td>
                        <td class="text-danger">- ₹${parseFloat(entry.discount_amount || entry.aggregated_total_discount || 0).toFixed(2)}</td>
                    </tr>
                    ` : ''}
                    <tr class="font-weight-bold">
                        <td><strong>Total Amount:</strong></td>
                        <td class="text-success"><strong>₹${Math.max(parseFloat(entry.subtotal || entry.aggregated_total_price || 0) - parseFloat(entry.discount_amount || entry.aggregated_total_discount || 0), 0).toFixed(2)}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-secondary"><strong><i class="fas fa-clipboard mr-2"></i>Additional Information</strong></h6>
                ${entry.notes || entry.remarks ? `
                <div class="alert alert-light" role="alert">
                    <strong>Notes:</strong><br>
                    ${entry.notes || entry.remarks}
                </div>
                ` : '<p class="text-muted">No additional notes</p>'}
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%"><strong>Added By:</strong></td>
                        <td>${entry.added_by_full_name || entry.added_by_username || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td><small>${createdDate}</small></td>
                    </tr>
                    ${updatedDate ? `
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td><small>${updatedDate}</small></td>
                    </tr>
                    ` : ''}
                </table>
            </div>
        </div>
    `;
    
    const detailsElement = $('#entryDetails');
    if (detailsElement.length) {
        detailsElement.html(details);
    } else {
        console.error('Entry details element not found');
        toastr.error('Unable to display entry details');
    }
}

// Edit entry
function editEntry(id) {
    console.log('=== EDIT ENTRY CALLED ===');
    console.log('Entry ID:', id);
    
    if (!id) {
        toastr.error('Invalid entry ID');
        return;
    }
    
    // Show loading
    toastr.info('Loading entry for editing...', '', {timeOut: 1000});
    
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            console.log('=== EDIT ENTRY API RESPONSE ===');
            console.log('Full response:', response);
            
            if (response.success) {
                console.log('Entry data received:', response.data);
                console.log('Tests in entry:', response.data.tests ? response.data.tests.length : 0);
                
                if (response.data.tests && response.data.tests.length > 0) {
                    console.log('Test details:', response.data.tests);
                }
                
                populateEditForm(response.data);
                $('#entryModal').modal('show');
            } else {
                console.error('Edit entry failed:', response.message);
                toastr.error(response.message || 'Failed to load entry for editing');
            }
        },
        error: function(xhr, status, error) {
            console.error('Edit entry AJAX error:', xhr, status, error);
            let errorMsg = 'Failed to load entry for editing. ';
            if (xhr.status === 401 || xhr.status === 403) {
                errorMsg += 'Permission denied.';
            } else if (xhr.status === 404) {
                errorMsg += 'Entry not found.';
            } else {
                errorMsg += 'Please try again.';
            }
            toastr.error(errorMsg);
        }
    });
}

// Populate edit form
function populateEditForm(entry) {
    console.log('=== POPULATING EDIT FORM ===');
    console.log('Entry data:', entry);
    
    currentEntryId = entry.id;
    $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');
    $('#entryId').val(entry.id);

    // Set basic fields first
    let entryDateValue = '';
    if (entry.entry_date) {
        entryDateValue = entry.entry_date.split(' ')[0];
    }
    $('#entryDate').val(entryDateValue);
    $('#entryStatus').val(entry.status || 'pending');
    $('#entryNotes').val(entry.notes || entry.remarks || '');
    $('#patientContact').val(entry.patient_contact || '');
    $('#patientAddress').val(entry.patient_address || '');
    $('#referralSource').val(entry.referral_source || '');
    $('#priority').val(entry.priority || 'normal');

    // Store entry data for later use
    window.editingEntry = entry;

    // Enable all dropdowns for editing
    $('#patientSelect, #doctorSelect').prop('disabled', false);

    // Step 1: Set owner/added by and wait for it to load patients/doctors
    let ownerAddedByValue = '';
    if (entry.owner_id) {
        ownerAddedByValue = `owner_${entry.owner_id}`;
    } else if (entry.added_by) {
        ownerAddedByValue = `user_${entry.added_by}`;
    }

    console.log('Setting owner/added by to:', ownerAddedByValue);

    // Check if owner option exists, if not, add it
    const ownerSelect = $('#ownerAddedBySelect');
    if (ownerAddedByValue && ownerSelect.find(`option[value="${ownerAddedByValue}"]`).length === 0) {
        // Add the option if it doesn't exist
        let optionText = '';
        if (entry.owner_name) {
            optionText = `🏢 ${entry.owner_name} (Owner)`;
        } else if (entry.added_by_full_name || entry.added_by_username) {
            optionText = `👤 ${entry.added_by_full_name || entry.added_by_username} (User)`;
        } else {
            optionText = `User ${entry.added_by}`;
        }
        
        const dataType = entry.owner_id ? 'owner' : 'user';
        const dataId = entry.owner_id || entry.added_by;
        
        ownerSelect.append(`<option value="${ownerAddedByValue}" data-type="${dataType}" data-${dataType}-id="${dataId}">${optionText}</option>`);
    }

    // Set the owner/added by value
    ownerSelect.val(ownerAddedByValue);

    // Step 2: Load patients and doctors for this owner, then set selections
    const ownerId = entry.owner_id || entry.added_by;
    if (ownerId) {
        console.log('Loading patients and doctors for owner ID:', ownerId);
        
        // Load patients first
        loadPatientsByOwner(ownerId, function() {
            console.log('Patients loaded, setting patient selection to:', entry.patient_id);
            
            // Check if patient option exists, if not add it
            const patientSelect = $('#patientSelect');
            if (entry.patient_id && patientSelect.find(`option[value="${entry.patient_id}"]`).length === 0) {
                const patientText = `${entry.patient_name || 'Unknown Patient'} (${entry.uhid || 'No UHID'})`;
                patientSelect.append(`<option value="${entry.patient_id}" data-gender="${entry.gender || ''}" data-contact="${entry.patient_contact || ''}" data-address="${entry.patient_address || ''}">${patientText}</option>`);
            }
            
            patientSelect.val(entry.patient_id).trigger('change');
            
            // Manually populate patient fields if auto-fill doesn't work
            setTimeout(function() {
                if (entry.patient_age || entry.age) {
                    $('#patientAge').val(entry.patient_age || entry.age);
                }
                if (entry.gender || entry.sex) {
                    $('#patientGender').val(entry.gender || entry.sex);
                }
            }, 100);
            
            // Load doctors
            loadDoctorsByOwner(ownerId, function() {
                console.log('Doctors loaded, setting doctor selection to:', entry.doctor_id);
                
                // Check if doctor option exists, if not add it
                const doctorSelect = $('#doctorSelect');
                if (entry.doctor_id && doctorSelect.find(`option[value="${entry.doctor_id}"]`).length === 0) {
                    const doctorText = `Dr. ${entry.doctor_name || 'Unknown Doctor'}`;
                    doctorSelect.append(`<option value="${entry.doctor_id}">${doctorText}</option>`);
                }
                
                doctorSelect.val(entry.doctor_id);
                
                console.log('All dropdowns populated successfully');
            });
        });
    } else {
        console.warn('No owner ID found, cannot load patients/doctors');
        
        // Fallback: try to add patient and doctor options directly if we have the data
        if (entry.patient_id && entry.patient_name) {
            const patientSelect = $('#patientSelect');
            patientSelect.empty().append('<option value="">Select Patient</option>');
            const patientText = `${entry.patient_name} (${entry.uhid || 'No UHID'})`;
            patientSelect.append(`<option value="${entry.patient_id}" data-gender="${entry.gender || ''}" data-contact="${entry.patient_contact || ''}" data-address="${entry.patient_address || ''}" selected>${patientText}</option>`);
            patientSelect.prop('disabled', false);
        }
        
        if (entry.doctor_id && entry.doctor_name) {
            const doctorSelect = $('#doctorSelect');
            doctorSelect.empty().append('<option value="">Select Doctor</option>');
            const doctorText = `Dr. ${entry.doctor_name}`;
            doctorSelect.append(`<option value="${entry.doctor_id}" selected>${doctorText}</option>`);
            doctorSelect.prop('disabled', false);
        }
    }
    
    // Populate pricing fields - handle both direct fields and aggregated fields
    console.log('=== EDIT ENTRY DEBUG ===');
    console.log('Complete entry object:', JSON.stringify(entry, null, 2));
    console.log('All entry keys:', Object.keys(entry));
    console.log('Entry pricing data:', {
        subtotal: entry.subtotal,
        discount_amount: entry.discount_amount,
        total_price: entry.total_price,
        aggregated_total_price: entry.aggregated_total_price,
        aggregated_total_discount: entry.aggregated_total_discount,
        final_amount: entry.final_amount,
        price: entry.price,
        agg_total_price: entry.agg_total_price,
        agg_total_discount: entry.agg_total_discount
    });
    
    // Use direct fields if available (check for null/undefined, not falsy), otherwise use aggregated fields
    // Check ALL possible field name variations from the API response
    const subtotalValue = (entry.subtotal !== null && entry.subtotal !== undefined) 
        ? parseFloat(entry.subtotal)
        : (entry.aggregated_total_price !== null && entry.aggregated_total_price !== undefined)
            ? parseFloat(entry.aggregated_total_price)
            : (entry.agg_total_price !== null && entry.agg_total_price !== undefined)
                ? parseFloat(entry.agg_total_price)
                : (entry.price !== null && entry.price !== undefined)
                    ? parseFloat(entry.price)
                    : 0;
    
    const discountValue = (entry.discount_amount !== null && entry.discount_amount !== undefined)
        ? parseFloat(entry.discount_amount)
        : (entry.aggregated_total_discount !== null && entry.aggregated_total_discount !== undefined)
            ? parseFloat(entry.aggregated_total_discount)
            : (entry.agg_total_discount !== null && entry.agg_total_discount !== undefined)
                ? parseFloat(entry.agg_total_discount)
                : (entry.total_discount !== null && entry.total_discount !== undefined)
                    ? parseFloat(entry.total_discount)
                    : 0;
    
    const totalValue = (entry.total_price !== null && entry.total_price !== undefined)
        ? parseFloat(entry.total_price)
        : (entry.final_amount !== null && entry.final_amount !== undefined)
            ? parseFloat(entry.final_amount)
            : Math.max(subtotalValue - discountValue, 0);
    
    console.log('Calculated pricing values:', {
        subtotalValue: subtotalValue,
        discountValue: discountValue,
        totalValue: totalValue
    });
    
    // Format pricing values properly - always show 2 decimal places
    const subtotal = parseFloat(subtotalValue).toFixed(2);
    const discountAmount = parseFloat(discountValue).toFixed(2);
    const totalPrice = parseFloat(totalValue).toFixed(2);
    
    console.log('Formatted pricing values:', {
        subtotal: subtotal,
        discountAmount: discountAmount,
        totalPrice: totalPrice
    });
    
    $('#subtotal').val(subtotal);
    $('#discountAmount').val(discountAmount);
    $('#totalPrice').val(totalPrice);
    
    console.log('Pricing fields populated:', {
        subtotal: $('#subtotal').val(),
        discountAmount: $('#discountAmount').val(),
        totalPrice: $('#totalPrice').val()
    });

    // Set patient and doctor after a delay to ensure owner selection is processed
    setTimeout(function() {
        $('#patientSelect').val(entry.patient_id).trigger('change');
        $('#doctorSelect').val(entry.doctor_id).trigger('change');
    }, 1000);

    // Populate tests section
    const testsContainer = $('#testsContainer');
    testsContainer.empty();
    testRowCount = 0;

    console.log('Populating tests section with', entry.tests ? entry.tests.length : 0, 'tests');

    if (entry.tests && entry.tests.length > 0) {
        entry.tests.forEach(function(test, index) {
            console.log(`Adding test row ${index}:`, test);
            
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
                        <input type="text" class="form-control test-min" name="tests[${index}][min]" placeholder="Min" readonly value="${test.min || ''}">
                    </div>
                    <div class="col-md-1">
                        <input type="text" class="form-control test-max" name="tests[${index}][max]" placeholder="Max" readonly value="${test.max || ''}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control test-unit" name="tests[${index}][unit]" placeholder="Unit" readonly value="${test.unit || ''}">
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
        console.log('Added', testRowCount, 'test rows to form');
    } else {
        console.log('No tests found, adding blank row');
        addTestRow(); // Add a blank row if no tests
    }

    // Load dropdowns
    loadTests(function() {
        console.log('Tests loaded, now populating test selections...');
        
        if (entry.tests && entry.tests.length > 0) {
            entry.tests.forEach(function(test, index) {
                console.log(`Setting test ${index}:`, test);
                
                const testRow = testsContainer.find('.test-row').eq(index);
                const testSelect = testRow.find('.test-select');
                
                if (test.test_id && testRow.length > 0) {
                    // Set the test selection
                    testSelect.val(test.test_id);
                    
                    console.log(`Test ${index} selection set to:`, test.test_id, 'Row found:', testRow.length > 0);
                    
                    // Get test data from the selected option
                    const $opt = testSelect.find('option:selected');
                    const unit = $opt.data('unit') || test.unit || '';
                    const min = $opt.data('min') || test.min || '';
                    const max = $opt.data('max') || test.max || '';
                    const categoryName = $opt.data('category-name') || test.category_name || '';
                    const categoryId = $opt.data('category-id') || test.category_id || '';
                    
                    // Populate fields directly with fallback to test data
                    testRow.find('.test-unit').val(unit).prop('readonly', true).show();
                    testRow.find('.test-min').val(min).prop('readonly', true).show();
                    testRow.find('.test-max').val(max).prop('readonly', true).show();
                    testRow.find('.test-category').val(categoryName).prop('readonly', true).show();
                    testRow.find('.test-category-id').val(categoryId);
                    testRow.find('.test-result').val(test.result_value || '').prop('readonly', false).prop('disabled', false);
                    
                    console.log(`Test ${index} fields populated:`, {
                        unit: unit,
                        min: min,
                        max: max,
                        category: categoryName,
                        result: test.result_value || ''
                    });
                } else {
                    console.warn(`Test ${index} missing test_id or row not found:`, {
                        test_id: test.test_id,
                        rowFound: testRow.length > 0
                    });
                }
            });
            
            console.log('All test selections completed');
        } else {
            console.log('No tests to populate');
        }
        
        // After tests are loaded, re-populate pricing fields to ensure they're visible
        // This handles cases where the fields might have been cleared or reset
        setTimeout(function() {
            // Recalculate subtotal from entry data (not from test selections)
            // Check ALL possible field name variations
            const finalSubtotal = (entry.subtotal !== null && entry.subtotal !== undefined) 
                ? parseFloat(entry.subtotal)
                : (entry.aggregated_total_price !== null && entry.aggregated_total_price !== undefined)
                    ? parseFloat(entry.aggregated_total_price)
                    : (entry.agg_total_price !== null && entry.agg_total_price !== undefined)
                        ? parseFloat(entry.agg_total_price)
                        : (entry.price !== null && entry.price !== undefined)
                            ? parseFloat(entry.price)
                            : 0;
            
            const finalDiscount = (entry.discount_amount !== null && entry.discount_amount !== undefined)
                ? parseFloat(entry.discount_amount)
                : (entry.aggregated_total_discount !== null && entry.aggregated_total_discount !== undefined)
                    ? parseFloat(entry.aggregated_total_discount)
                    : (entry.agg_total_discount !== null && entry.agg_total_discount !== undefined)
                        ? parseFloat(entry.agg_total_discount)
                        : (entry.total_discount !== null && entry.total_discount !== undefined)
                            ? parseFloat(entry.total_discount)
                            : 0;
            
            const finalTotal = (entry.total_price !== null && entry.total_price !== undefined)
                ? parseFloat(entry.total_price)
                : (entry.final_amount !== null && entry.final_amount !== undefined)
                    ? parseFloat(entry.final_amount)
                    : Math.max(finalSubtotal - finalDiscount, 0);
            
            $('#subtotal').val(parseFloat(finalSubtotal).toFixed(2));
            $('#discountAmount').val(parseFloat(finalDiscount).toFixed(2));
            $('#totalPrice').val(parseFloat(finalTotal).toFixed(2));
            
            console.log('Pricing fields re-populated after tests loaded:', {
                subtotal: $('#subtotal').val(),
                discountAmount: $('#discountAmount').val(),
                totalPrice: $('#totalPrice').val(),
                rawValues: {
                    finalSubtotal: finalSubtotal,
                    finalDiscount: finalDiscount,
                    finalTotal: finalTotal
                }
            });
        }, 200);
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

// Print entry details
function printEntryDetails() {
    const printContent = document.getElementById('entryDetails').innerHTML;
    const originalContent = document.body.innerHTML;
    
    // Create print-friendly version
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Entry Details - Print</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
            <style>
                body { padding: 20px; }
                @media print {
                    .no-print { display: none; }
                    body { padding: 0; }
                }
                .badge { padding: 5px 10px; }
                .table { page-break-inside: avoid; }
            </style>
        </head>
        <body>
            <div class="container-fluid">
                <div class="text-center mb-4">
                    <h3>Pathology Lab Management - Entry Details</h3>
                    <p class="text-muted">Printed on: ${new Date().toLocaleString('en-IN')}</p>
                </div>
                ${printContent}
            </div>
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(function() { window.close(); }, 100);
                }
            </script>
        </body>
        </html>
    `);
    printWindow.document.close();
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

// Add new test row
function addTestRow() {
    console.log('=== ADD TEST ROW CALLED ===');
    console.log('Current testRowCount:', testRowCount);
    
    const testsContainer = $('#testsContainer');
    console.log('Tests container found:', testsContainer.length > 0);
    
    if (testsContainer.length === 0) {
        console.error('Tests container not found!');
        if (typeof toastr !== 'undefined') {
            toastr.error('Tests container not found. Please refresh the page.');
        } else {
            alert('Tests container not found. Please refresh the page.');
        }
        return;
    }
    
    // Ensure testRowCount is a valid number
    if (typeof testRowCount !== 'number' || isNaN(testRowCount)) {
        console.warn('testRowCount is not a valid number, resetting to current row count');
        testRowCount = testsContainer.find('.test-row').length;
    }
    
    const newRowHTML = `
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
                <input type="text" class="form-control test-min" name="tests[${testRowCount}][min]" placeholder="Min" readonly>
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-max" name="tests[${testRowCount}][max]" placeholder="Max" readonly>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control test-unit" name="tests[${testRowCount}][unit]" placeholder="Unit" readonly>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)" title="Remove Test">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    testsContainer.append(newRowHTML);
    testRowCount++;
    
    // Load tests for the new row
    loadTests();
    
    // Initialize Select2 for the new row
    setTimeout(function() {
        const newRow = testsContainer.find('.test-row').last();
        const newSelect = newRow.find('.test-select');
        
        console.log('Initializing Select2 for new row:', newRow.length > 0);
        
        if (typeof $.fn.select2 !== 'undefined' && newSelect.length > 0) {
            try {
                // Initialize Select2 with proper parent
                if ($('#entryModal').hasClass('show') || $('#entryModal').is(':visible')) {
                    newSelect.select2({
                        dropdownParent: $('#entryModal'),
                        width: '100%'
                    });
                } else {
                    newSelect.select2({
                        width: '100%'
                    });
                }
                console.log('Select2 initialized successfully');
            } catch (e) {
                console.error('Error initializing Select2:', e);
            }
        }
        
        // Ensure the new row is visible and properly styled
        newRow.find('.test-unit, .test-category, .test-min, .test-max').prop('readonly', true).show();
        newRow.find('.test-result').prop('readonly', false).prop('disabled', false);
        
    }, 100);
    
    console.log('Added new test row, total rows:', testRowCount);
    
    // Show success message
    if (typeof toastr !== 'undefined') {
        toastr.success(`Test row added successfully! Total rows: ${testRowCount}`);
    }
}

// Make functions globally accessible
window.addTestRow = addTestRow;
window.removeTestRow = removeTestRow;
window.openAddEntryModal = openAddEntryModal;

// Remove test row
function removeTestRow(button) {
    const testRow = $(button).closest('.test-row');
    const testsContainer = $('#testsContainer');
    
    // Don't allow removing the last row
    if (testsContainer.find('.test-row').length > 1) {
        testRow.remove();
        
        // Recalculate pricing after removing test
        updatePricingFields();
        
        // Re-index remaining test rows
        testsContainer.find('.test-row').each(function(index) {
            $(this).find('select, input').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    const newName = name.replace(/tests\[\d+\]/, `tests[${index}]`);
                    $(this).attr('name', newName);
                }
            });
        });
        
        testRowCount = testsContainer.find('.test-row').length;
        console.log('Removed test row, remaining rows:', testRowCount);
    } else {
        toastr.warning('At least one test is required');
    }
}

// Update pricing fields based on selected tests
function updatePricingFields() {
    let subtotal = 0;
    
    $('#testsContainer .test-row').each(function() {
        const testSelect = $(this).find('.test-select');
        const selectedOption = testSelect.find('option:selected');
        const price = parseFloat(selectedOption.data('price') || 0);
        subtotal += price;
    });
    
    const discount = parseFloat($('#discountAmount').val() || 0);
    const total = Math.max(subtotal - discount, 0);
    
    $('#subtotal').val(subtotal.toFixed(2));
    $('#totalPrice').val(total.toFixed(2));
    
    console.log('Pricing updated:', {
        subtotal: subtotal.toFixed(2),
        discount: discount.toFixed(2),
        total: total.toFixed(2)
    });
}

// Handle discount amount changes
$(document).on('input', '#discountAmount', function() {
    updatePricingFields();
});

// Fix the populateEditForm function to properly handle multiple tests
function fixEditFormTestsDisplay() {
    // This function ensures all tests are properly displayed in edit mode
    const testsContainer = $('#testsContainer');
    const testRows = testsContainer.find('.test-row');
    
    console.log('Fixing edit form tests display, found rows:', testRows.length);
    
    // Ensure all test rows are visible and properly configured
    testRows.each(function(index) {
        const $row = $(this);
        
        // Make sure all fields are visible
        $row.find('.test-unit, .test-category, .test-min, .test-max').each(function() {
            $(this).prop('readonly', true).show().css({ 
                'display': 'block', 
                'visibility': 'visible' 
            });
        });
        
        // Enable result inputs
        $row.find('.test-result').prop('disabled', false).prop('readonly', false);
        
        // Ensure proper name attributes
        $row.find('select, input').each(function() {
            const name = $(this).attr('name');
            if (name && !name.includes(`[${index}]`)) {
                const newName = name.replace(/tests\[\d+\]/, `tests[${index}]`);
                $(this).attr('name', newName);
            }
        });
    });
    
    // Update the test row counter
    testRowCount = testRows.length;
    console.log('Fixed test rows, total count:', testRowCount);
}

// Enhanced modal show handler to fix tests display
$(document).on('shown.bs.modal', '#entryModal', function() {
    console.log('=== ENTRY MODAL SHOWN ===');
    
    // Check current state
    const testsContainer = $('#testsContainer');
    const testRows = testsContainer.find('.test-row');
    
    console.log('Tests container found:', testsContainer.length > 0);
    console.log('Test rows found:', testRows.length);
    
    testRows.each(function(index) {
        const $row = $(this);
        const testSelect = $row.find('.test-select');
        const selectedValue = testSelect.val();
        const resultValue = $row.find('.test-result').val();
        
        console.log(`Test row ${index}:`, {
            selectedTest: selectedValue,
            resultValue: resultValue,
            selectOptions: testSelect.find('option').length
        });
    });
    
    // Fix tests display
    fixEditFormTestsDisplay();
    
    // Ensure pricing fields are visible and calculated
    setTimeout(function() {
        updatePricingFields();
        console.log('Modal shown - pricing fields updated');
        
        // Final verification
        const finalTestRows = testsContainer.find('.test-row');
        console.log('Final test rows count:', finalTestRows.length);
        
        finalTestRows.each(function(index) {
            const $row = $(this);
            const testSelect = $row.find('.test-select');
            const selectedValue = testSelect.val();
            
            if (selectedValue) {
                console.log(`Test row ${index} has selection:`, selectedValue);
            } else {
                console.warn(`Test row ${index} has no selection`);
            }
        });
    }, 200);
});