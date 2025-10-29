/**
 * Entry List Management System - Clean and Simplified
 * Hospital Test Entries Management
 */

// Global variables
let entriesTable = null;
let testsData = [];
let categoriesData = [];
let patientsData = [];
let doctorsData = [];
let currentEditId = null;
let testRowCounter = 0;

/**
 * Initialize the application
 */
$(document).ready(function () {
    console.log('Entry List Management - Initializing...');

    // Initialize components
    initializeDataTable();
    loadInitialData();
    bindEvents();

    console.log('Entry List Management - Initialized successfully');
});

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    console.log('Initializing DataTable...');

    try {
        entriesTable = $('#entriesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: 'ajax/entry_api_fixed.php',
                type: 'GET',
                data: {
                    action: 'list',
                    secret_key: 'hospital-api-secret-2024'
                },
                dataSrc: function (json) {
                    console.log('DataTable response:', json);
                    if (json && json.success) {
                        return json.data || [];
                    } else {
                        console.error('DataTable error:', json);
                        showError(json ? json.message : 'Failed to load entries');
                        return [];
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTable AJAX error:', error, thrown);
                    showError('Failed to load entries. Please refresh the page.');
                }
            },
            columns: [
                {
                    data: 'id',
                    title: 'ID',
                    width: '5%'
                },
                {
                    data: 'patient_name',
                    title: 'Patient',
                    width: '15%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            let html = `<strong>${data || 'N/A'}</strong>`;
                            if (row.patient_contact) {
                                html += `<br><small class="text-muted">${row.patient_contact}</small>`;
                            }
                            return html;
                        }
                        return data || '';
                    }
                },
                {
                    data: 'doctor_name',
                    title: 'Doctor',
                    width: '12%',
                    render: function (data, type, row) {
                        return data || '<span class="text-muted">Not assigned</span>';
                    }
                },
                {
                    data: 'test_names',
                    title: 'Tests',
                    width: '20%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const testCount = parseInt(row.tests_count) || 0;
                            const testNames = data || '';

                            if (testCount === 0) {
                                return '<span class="text-muted">No tests</span>';
                            } else if (testCount === 1) {
                                return `<span class="badge badge-info">${testCount}</span> ${testNames}`;
                            } else {
                                return `<span class="badge badge-primary">${testCount}</span> ${testNames}`;
                            }
                        }
                        return data || '';
                    }
                },
                {
                    data: 'status',
                    title: 'Status',
                    width: '10%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const status = data || 'pending';
                            const badgeClass = {
                                'pending': 'badge-warning',
                                'completed': 'badge-success',
                                'cancelled': 'badge-danger'
                            }[status] || 'badge-secondary';

                            return `<span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
                        }
                        return data || 'pending';
                    }
                },
                {
                    data: 'priority',
                    title: 'Priority',
                    width: '8%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const priority = data || 'normal';
                            const badgeClass = {
                                'emergency': 'badge-danger',
                                'urgent': 'badge-warning',
                                'normal': 'badge-info',
                                'routine': 'badge-secondary'
                            }[priority] || 'badge-secondary';

                            return `<span class="badge ${badgeClass}">${priority.charAt(0).toUpperCase() + priority.slice(1)}</span>`;
                        }
                        return data || 'normal';
                    }
                },
                {
                    data: 'total_price',
                    title: 'Amount',
                    width: '10%',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            const amount = parseFloat(data) || 0;
                            return `₹${amount.toFixed(2)}`;
                        }
                        return data || 0;
                    }
                },
                {
                    data: 'entry_date',
                    title: 'Date',
                    width: '10%',
                    render: function (data, type, row) {
                        if (type === 'display' && data) {
                            const date = new Date(data);
                            return date.toLocaleDateString('en-IN');
                        }
                        return data || '';
                    }
                },
                {
                    data: null,
                    title: 'Actions',
                    width: '10%',
                    orderable: false,
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-info btn-sm" onclick="viewEntry(${row.id})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="editEntry(${row.id})" title="Edit Entry">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteEntry(${row.id})" title="Delete Entry">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        }
                        return '';
                    }
                }
            ],
            order: [[0, 'desc']], // Order by ID descending (newest first)
            pageLength: 25,
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-info btn-sm'
                }
            ],
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading entries...',
                emptyTable: 'No entries found',
                zeroRecords: 'No matching entries found'
            }
        });

        console.log('DataTable initialized successfully');
    } catch (error) {
        console.error('Failed to initialize DataTable:', error);
        showError('Failed to initialize data table. Please refresh the page.');
    }
}

/**
 * Load initial data (tests, patients, doctors)
 */
async function loadInitialData() {
    console.log('Loading initial data...');

    try {
        // Load tests data
        await loadTestsData();

        // Load categories data
        await loadCategoriesData();

        // Load patients data
        await loadPatientsData();

        // Load doctors data
        await loadDoctorsData();

        console.log('Initial data loaded successfully');
    } catch (error) {
        console.error('Failed to load initial data:', error);
        showError('Failed to load initial data. Some features may not work properly.');
    }
}

/**
 * Load tests data from API
 */
async function loadTestsData() {
    try {
        const response = await $.ajax({
            url: 'ajax/test_api.php',
            method: 'GET',
            data: { action: 'simple_list' },
            dataType: 'json',
            timeout: 10000
        });

        if (response && response.success) {
            testsData = response.data || [];
            console.log(`Loaded ${testsData.length} tests`);
        } else {
            console.error('Failed to load tests:', response);
            testsData = [];
        }
    } catch (error) {
        console.error('Error loading tests:', error);
        testsData = [];
    }
}

/**
 * Load categories data from API
 */
async function loadCategoriesData() {
    try {
        const response = await $.ajax({
            url: 'patho_api/test_category.php',
            method: 'GET',
            data: {
                action: 'list',
                all: '1'
            },
            dataType: 'json',
            timeout: 10000
        });

        if (response && response.success) {
            categoriesData = response.data || [];
            console.log(`Loaded ${categoriesData.length} categories`);
        } else {
            console.error('Failed to load categories:', response);
            categoriesData = [];
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        categoriesData = [];
    }
}

/**
 * Load patients data from API
 */
async function loadPatientsData() {
    try {
        const response = await $.ajax({
            url: 'ajax/patient_api.php',
            method: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            timeout: 10000
        });

        if (response && response.success) {
            patientsData = response.data || [];
            populatePatientSelect();
            console.log(`Loaded ${patientsData.length} patients`);
        } else {
            console.error('Failed to load patients:', response);
            patientsData = [];
        }
    } catch (error) {
        console.error('Error loading patients:', error);
        patientsData = [];
    }
}

/**
 * Load doctors data from API
 */
async function loadDoctorsData() {
    try {
        const response = await $.ajax({
            url: 'ajax/doctor_api.php',
            method: 'GET',
            data: { action: 'list' },
            dataType: 'json',
            timeout: 10000
        });

        if (response && response.success) {
            doctorsData = response.data || [];
            populateDoctorSelect();
            console.log(`Loaded ${doctorsData.length} doctors`);
        } else {
            console.error('Failed to load doctors:', response);
            doctorsData = [];
        }
    } catch (error) {
        console.error('Error loading doctors:', error);
        doctorsData = [];
    }
}

/**
 * Populate patient select dropdown
 */
function populatePatientSelect() {
    const $select = $('#patientSelect');
    $select.empty().append('<option value="">Select Patient</option>');

    patientsData.forEach(patient => {
        const displayName = `${patient.name || 'Unknown'} ${patient.uhid ? `(${patient.uhid})` : ''}`;
        $select.append(`<option value="${patient.id}">${displayName}</option>`);
    });

    // Refresh Select2 if initialized
    if ($select.hasClass('select2-hidden-accessible')) {
        $select.trigger('change');
    }
}

/**
 * Populate doctor select dropdown
 */
function populateDoctorSelect() {
    const $select = $('#doctorSelect');
    $select.empty().append('<option value="">Select Doctor</option>');

    doctorsData.forEach(doctor => {
        const displayName = `${doctor.name || 'Unknown'} ${doctor.specialization ? `(${doctor.specialization})` : ''}`;
        $select.append(`<option value="${doctor.id}">${displayName}</option>`);
    });

    // Refresh Select2 if initialized
    if ($select.hasClass('select2-hidden-accessible')) {
        $select.trigger('change');
    }
}



/**
 * Bind event handlers
 */
function bindEvents() {
    console.log('Binding events...');

    // Filter change events
    $('#statusFilter, #dateFilter').on('change', function () {
        applyFilters();
    });

    $('#patientFilter, #doctorFilter').on('keyup', debounce(function () {
        applyFilters();
    }, 300));

    // Form submission
    $('#entryForm').on('submit', function (e) {
        e.preventDefault();
        saveEntry();
    });

    // Discount amount change
    $('#discountAmount').on('input', function () {
        calculateTotals();
    });

    // Global category filter events
    $('#globalCategoryFilter').on('change', function () {
        applyGlobalCategoryFilter($(this).val());
    });

    $('#clearGlobalCategoryFilter').on('click', function () {
        $('#globalCategoryFilter').val('');
        applyGlobalCategoryFilter('');
    });

    console.log('Events bound successfully');
}

/**
 * Utility function for debouncing
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Apply filters to DataTable
 */
function applyFilters() {
    if (!entriesTable) return;

    const statusFilter = $('#statusFilter').val();
    const dateFilter = $('#dateFilter').val();
    const patientFilter = $('#patientFilter').val();
    const doctorFilter = $('#doctorFilter').val();

    // Apply global search for patient and doctor
    let globalSearch = '';
    if (patientFilter) globalSearch += patientFilter + ' ';
    if (doctorFilter) globalSearch += doctorFilter + ' ';

    entriesTable.search(globalSearch.trim()).draw();
}



/**
 * Refresh the entries table
 */
function refreshTable() {
    if (entriesTable) {
        entriesTable.ajax.reload();
        showSuccess('Table refreshed successfully');
    }
}

/**
 * Export entries
 */
function exportEntries() {
    if (entriesTable) {
        // Trigger the Excel export
        entriesTable.button('.buttons-excel').trigger();
    }
}

/**
 * Open Add Entry Modal
 */
function openAddModal() {
    console.log('Opening add modal...');

    currentEditId = null;
    resetForm();

    // Update modal title
    $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');

    // Show modal
    $('#entryModal').modal('show');

    // Initialize Select2 dropdowns
    initializeSelect2();

    // Add first test row
    addTestRow();
}

/**
 * Reset form to default state
 */
function resetForm() {
    $('#entryForm')[0].reset();
    $('#entryId').val('');
    $('#testsContainer').empty();
    testRowCounter = 0;
    calculateTotals();
}

/**
 * Initialize Select2 dropdowns
 */
function initializeSelect2() {
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Populate global category filter
    populateGlobalCategoryFilter();
}

/**
 * Populate global category filter
 */
function populateGlobalCategoryFilter() {
    const $select = $('#globalCategoryFilter');
    $select.find('option:not(:first)').remove(); // Keep the "All Categories" option

    categoriesData.forEach(category => {
        $select.append(`<option value="${category.id}">${category.name}</option>`);
    });
}

/**
 * Apply global category filter to all test dropdowns
 */
function applyGlobalCategoryFilter(categoryId) {
    console.log('Applying global category filter:', categoryId);

    // Update all existing test rows
    $('#testsContainer .test-row').each(function () {
        const $row = $(this);
        const $categorySelect = $row.find('.category-select');
        const $testSelect = $row.find('.test-select');

        if (categoryId) {
            // Set the category in the row
            $categorySelect.val(categoryId);
            // Trigger the category change to filter tests
            $categorySelect.trigger('change');
        } else {
            // Clear category selection and show all tests
            $categorySelect.val('');
            $categorySelect.trigger('change');
        }
    });
}

/**
 * Add a new test row
 */
function addTestRow(testData = null) {
    const rowIndex = testRowCounter++;

    console.log(`Adding test row ${rowIndex}`, testData);

    // Create category options
    const categoryOptions = categoriesData.map(category => {
        return `<option value="${category.id}">${category.name}</option>`;
    }).join('');

    // Create test options (will be filtered by category)
    const testOptions = testsData.map(test => {
        const displayName = `${test.name} [ID: ${test.id}]`;
        return `<option value="${test.id}" data-category-id="${test.category_id || ''}" data-price="${test.price || 0}" data-unit="${test.unit || ''}" data-min="${test.min || ''}" data-max="${test.max || ''}">${displayName}</option>`;
    }).join('');

    const rowHtml = `
        <div class="test-row row mb-2" data-row-index="${rowIndex}">
            <div class="col-md-2">
                <select class="form-control category-select" name="tests[${rowIndex}][category_id]">
                    <option value="">Select Category</option>
                    ${categoryOptions}
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control test-select" name="tests[${rowIndex}][test_id]" required>
                    <option value="">Select Test</option>
                    ${testOptions}
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control test-result" name="tests[${rowIndex}][result_value]" placeholder="Result">
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-min" name="tests[${rowIndex}][min]" placeholder="Min" readonly>
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-max" name="tests[${rowIndex}][max]" placeholder="Max" readonly>
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-unit" name="tests[${rowIndex}][unit]" placeholder="Unit" readonly>
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control test-price" name="tests[${rowIndex}][price]" placeholder="0.00" step="0.01" min="0">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-test-btn" onclick="removeTestRow(this)" title="Remove Test">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;

    $('#testsContainer').append(rowHtml);

    // Get the new row
    const $newRow = $(`.test-row[data-row-index="${rowIndex}"]`);
    const $categorySelect = $newRow.find('.category-select');
    const $testSelect = $newRow.find('.test-select');

    // Bind category selection change event
    $categorySelect.on('change', function () {
        onCategoryChange(this, $newRow);
    });

    // Bind test selection change event
    $testSelect.on('change', function () {
        onTestChange(this, $newRow);
    });

    // Bind price change event
    $newRow.find('.test-price').on('input', function () {
        calculateTotals();
    });

    // Bind result validation event
    $newRow.find('.test-result').on('input blur', function () {
        validateTestResult(this, $newRow);
    });

    // If testData is provided, populate the row (EDIT MODE)
    if (testData) {
        console.log('Populating test row with data:', testData);

        // Set category first if available
        if (testData.category_id) {
            $categorySelect.val(testData.category_id);
            $categorySelect.trigger('change');
        }

        $testSelect.val(testData.test_id);
        $newRow.find('.test-result').val(testData.result_value || '');
        $newRow.find('.test-price').val(testData.price || 0);
        $newRow.find('.test-unit').val(testData.unit || '');
        $newRow.find('.test-min').val(testData.min || '');
        $newRow.find('.test-max').val(testData.max || '');

        // Trigger change to update unit and other fields
        $testSelect.trigger('change');
    }
}

/**
 * Remove a test row
 */
function removeTestRow(button) {
    const $row = $(button).closest('.test-row');
    $row.remove();
    calculateTotals();

    // Ensure at least one test row exists
    if ($('#testsContainer .test-row').length === 0) {
        addTestRow();
    }
}

/**
 * Handle category selection change
 */
function onCategoryChange(selectElement, $row) {
    const $categorySelect = $(selectElement);
    const $testSelect = $row.find('.test-select');
    const categoryId = $categorySelect.val();

    console.log('Category changed:', categoryId);

    // Clear current test selection
    $testSelect.val('');
    $row.find('.test-price').val('');
    $row.find('.test-unit').val('');

    // Filter tests by category
    const $testOptions = $testSelect.find('option');

    if (!categoryId) {
        // Show all tests if no category selected
        $testOptions.show();
    } else {
        // Hide all options first
        $testOptions.hide();

        // Show default option
        $testOptions.first().show();

        // Show tests that match the selected category
        $testOptions.each(function () {
            const $option = $(this);
            const testCategoryId = $option.data('category-id');

            if (testCategoryId == categoryId) {
                $option.show();
            }
        });
    }

    calculateTotals();
}

/**
 * Handle test selection change
 */
function onTestChange(selectElement, $row) {
    const $select = $(selectElement);
    const testId = $select.val();

    if (!testId) {
        // Clear everything if no test selected
        $row.find('.test-price').val('');
        $row.find('.test-unit').val('');
        $row.find('.test-min').val('');
        $row.find('.test-max').val('');
        calculateTotals();
        return;
    }

    // Find the test data
    const testData = testsData.find(t => t.id == testId);
    if (!testData) {
        console.error('Test not found:', testId);
        return;
    }

    console.log('Test selected:', testData);

    // Set test details
    $row.find('.test-price').val(testData.price || 0);
    $row.find('.test-unit').val(testData.unit || '');
    $row.find('.test-min').val(testData.min || '');
    $row.find('.test-max').val(testData.max || '');

    // Auto-select category if not already selected
    const $categorySelect = $row.find('.category-select');
    if (!$categorySelect.val() && testData.category_id) {
        $categorySelect.val(testData.category_id);
    }

    // Calculate totals
    calculateTotals();
}

/**
 * Validate test result against min/max ranges
 */
function validateTestResult(resultInput, $row) {
    const $resultInput = $(resultInput);
    const resultValue = parseFloat($resultInput.val());
    const minValue = parseFloat($row.find('.test-min').val());
    const maxValue = parseFloat($row.find('.test-max').val());

    // Clear previous validation classes
    $resultInput.removeClass('result-normal result-abnormal');

    // Only validate if we have numeric values for result and ranges
    if (!isNaN(resultValue) && !isNaN(minValue) && !isNaN(maxValue)) {
        if (resultValue >= minValue && resultValue <= maxValue) {
            $resultInput.addClass('result-normal');
        } else {
            $resultInput.addClass('result-abnormal');
        }
    }
}

/**
 * Calculate totals from test prices
 */
function calculateTotals() {
    let subtotal = 0;

    // Sum up all test prices
    $('#testsContainer .test-price').each(function () {
        const price = parseFloat($(this).val()) || 0;
        subtotal += price;
    });

    const discount = parseFloat($('#discountAmount').val()) || 0;
    const total = Math.max(subtotal - discount, 0);

    $('#subtotal').val(subtotal.toFixed(2));
    $('#totalPrice').val(total.toFixed(2));

    console.log(`Totals calculated - Subtotal: ${subtotal}, Discount: ${discount}, Total: ${total}`);
}

/**
 * Save entry (create or update)
 */
async function saveEntry() {
    console.log('Saving entry...');

    try {
        // Validate form
        if (!validateForm()) {
            return;
        }

        // Show loading state
        const $submitBtn = $('#entryForm button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

        // Prepare form data
        const formData = new FormData($('#entryForm')[0]);
        formData.append('action', currentEditId ? 'update' : 'save');
        formData.append('secret_key', 'hospital-api-secret-2024');

        // Collect tests data
        const tests = [];
        $('#testsContainer .test-row').each(function () {
            const $row = $(this);
            const testId = $row.find('.test-select').val();

            if (testId) {
                tests.push({
                    test_id: testId,
                    category_id: $row.find('.category-select').val() || null,
                    result_value: $row.find('.test-result').val(),
                    min: $row.find('.test-min').val(),
                    max: $row.find('.test-max').val(),
                    price: parseFloat($row.find('.test-price').val()) || 0,
                    unit: $row.find('.test-unit').val()
                });
            }
        });

        formData.append('tests', JSON.stringify(tests));

        console.log('Submitting form data:', Object.fromEntries(formData));

        const response = await $.ajax({
            url: 'ajax/entry_api_fixed.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        });

        if (response.success) {
            showSuccess(response.message || 'Entry saved successfully');
            $('#entryModal').modal('hide');
            refreshTable();
        } else {
            showError(response.message || 'Failed to save entry');
        }

    } catch (error) {
        console.error('Error saving entry:', error);
        showError('An error occurred while saving the entry');
    } finally {
        // Restore button state
        const $submitBtn = $('#entryForm button[type="submit"]');
        $submitBtn.html('<i class="fas fa-save"></i> Save Entry').prop('disabled', false);
    }
}

/**
 * Validate form before submission
 */
function validateForm() {
    // Check if patient is selected
    if (!$('#patientSelect').val()) {
        showError('Please select a patient');
        return false;
    }

    // Check if at least one test is selected
    const hasTests = $('#testsContainer .test-select').filter(function () {
        return $(this).val() !== '';
    }).length > 0;

    if (!hasTests) {
        showError('Please add at least one test');
        return false;
    }

    return true;
}

/**
 * View entry details
 */
function viewEntry(id) {
    console.log('Viewing entry:', id);

    // Show modal
    $('#viewEntryModal').modal('show');

    // Load entry data
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: {
            action: 'get',
            id: id,
            secret_key: 'hospital-api-secret-2024'
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                displayEntryDetails(response.data);
            } else {
                showError(response.message || 'Failed to load entry details');
                $('#viewEntryModal').modal('hide');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error loading entry:', error);
            showError('Failed to load entry details');
            $('#viewEntryModal').modal('hide');
        }
    });
}

/**
 * Display entry details in view modal
 */
function displayEntryDetails(entry) {
    console.log('Displaying entry details:', entry);

    const testsHtml = entry.tests && entry.tests.length > 0
        ? entry.tests.map(test => `
            <tr>
                <td>${test.category_name || 'No Category'}</td>
                <td>${test.test_name || 'Unknown Test'}</td>
                <td>${test.result_value || 'Pending'}</td>
                <td>${test.min || '-'}</td>
                <td>${test.max || '-'}</td>
                <td>${test.unit || '-'}</td>
                <td>₹${parseFloat(test.price || 0).toFixed(2)}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="7" class="text-center text-muted">No tests found</td></tr>';

    const detailsHtml = `
        <div class="row">
            <div class="col-md-6">
                <h5>Entry Information</h5>
                <table class="table table-sm">
                    <tr><th>Entry ID:</th><td>${entry.id}</td></tr>
                    <tr><th>Patient:</th><td>${entry.patient_name || 'N/A'}</td></tr>
                    <tr><th>Doctor:</th><td>${entry.doctor_name || 'Not assigned'}</td></tr>
                    <tr><th>Entry Date:</th><td>${entry.entry_date ? new Date(entry.entry_date).toLocaleDateString('en-IN') : 'N/A'}</td></tr>
                    <tr><th>Status:</th><td><span class="badge badge-${entry.status === 'completed' ? 'success' : entry.status === 'cancelled' ? 'danger' : 'warning'}">${entry.status || 'pending'}</span></td></tr>
                    <tr><th>Priority:</th><td><span class="badge badge-info">${entry.priority || 'normal'}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Pricing Information</h5>
                <table class="table table-sm">
                    <tr><th>Subtotal:</th><td>₹${parseFloat(entry.subtotal || 0).toFixed(2)}</td></tr>
                    <tr><th>Discount:</th><td>₹${parseFloat(entry.discount_amount || 0).toFixed(2)}</td></tr>
                    <tr><th>Total Amount:</th><td><strong>₹${parseFloat(entry.total_price || 0).toFixed(2)}</strong></td></tr>
                </table>
                ${entry.notes ? `<h6>Notes:</h6><p class="text-muted">${entry.notes}</p>` : ''}
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h5>Tests</h5>
                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Category</th>
                            <th>Test Name</th>
                            <th>Result</th>
                            <th>Min</th>
                            <th>Max</th>
                            <th>Unit</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${testsHtml}
                    </tbody>
                </table>
            </div>
        </div>
    `;

    $('#entryDetails').html(detailsHtml);
}

/**
 * Edit entry
 */
function editEntry(id) {
    console.log('Editing entry:', id);

    currentEditId = id;
    resetForm();

    // Update modal title
    $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');

    // Load entry data
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: {
            action: 'get',
            id: id,
            secret_key: 'hospital-api-secret-2024'
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                populateEditForm(response.data);
                $('#entryModal').modal('show');
                initializeSelect2();
            } else {
                showError(response.message || 'Failed to load entry for editing');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error loading entry for edit:', error);
            showError('Failed to load entry for editing');
        }
    });
}

/**
 * Populate form with entry data for editing
 */
function populateEditForm(entry) {
    console.log('Populating edit form with:', entry);

    // Set basic fields
    $('#entryId').val(entry.id);
    $('#patientSelect').val(entry.patient_id);
    $('#doctorSelect').val(entry.doctor_id);
    $('#entryDate').val(entry.entry_date ? entry.entry_date.split(' ')[0] : '');
    $('#entryStatus').val(entry.status || 'pending');
    $('#priority').val(entry.priority || 'normal');
    $('#referralSource').val(entry.referral_source || '');
    $('#subtotal').val(parseFloat(entry.subtotal || 0).toFixed(2));
    $('#discountAmount').val(parseFloat(entry.discount_amount || 0).toFixed(2));
    $('#totalPrice').val(parseFloat(entry.total_price || 0).toFixed(2));
    $('#entryNotes').val(entry.notes || '');

    // Add test rows
    if (entry.tests && entry.tests.length > 0) {
        entry.tests.forEach(test => {
            // Ensure category_id is available for proper row population
            if (!test.category_id && test.test_id) {
                // Find category from test data
                const testData = testsData.find(t => t.id == test.test_id);
                if (testData && testData.category_id) {
                    test.category_id = testData.category_id;
                }
            }
            addTestRow(test);
        });
    } else {
        addTestRow(); // Add at least one empty row
    }
}

/**
 * Delete entry
 */
function deleteEntry(id) {
    console.log('Deleting entry:', id);

    // Show confirmation modal
    $('#deleteModal').modal('show');

    // Handle delete confirmation
    $('#confirmDelete').off('click').on('click', function () {
        $.ajax({
            url: 'ajax/entry_api_fixed.php',
            method: 'POST',
            data: {
                action: 'delete',
                id: id,
                secret_key: 'hospital-api-secret-2024'
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showSuccess(response.message || 'Entry deleted successfully');
                    $('#deleteModal').modal('hide');
                    refreshTable();
                } else {
                    showError(response.message || 'Failed to delete entry');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error deleting entry:', error);
                showError('Failed to delete entry');
            }
        });
    });
}

/**
 * Print entry details
 */
function printEntryDetails() {
    const printContent = $('#entryDetails').html();
    const printWindow = window.open('', '_blank');

    printWindow.document.write(`
        <html>
        <head>
            <title>Entry Details</title>
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
            <style>
                body { font-family: Arial, sans-serif; }
                .badge { color: #000 !important; border: 1px solid #000; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="container mt-3">
                <h2 class="text-center mb-4">Entry Details</h2>
                ${printContent}
            </div>
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.print();
}

/**
 * Show success message
 */
function showSuccess(message) {
    if (typeof toastr !== 'undefined') {
        toastr.success(message);
    } else {
        alert(message);
    }
}

/**
 * Show error message
 */
function showError(message) {
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        alert('Error: ' + message);
    }
}

/**
 * Show info message
 */
function showInfo(message) {
    if (typeof toastr !== 'undefined') {
        toastr.info(message);
    } else {
        alert(message);
    }
}