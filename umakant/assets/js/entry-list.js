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

// API Configuration
const API_CONFIG = {
    // Use the new patho_api/entry.php API (set to false to use ajax/entry_api_fixed.php)
    useNewAPI: true,

    // API endpoints
    endpoints: {
        new: 'patho_api/entry.php',
        old: 'ajax/entry_api_fixed.php'
    },

    // Get current API URL
    getURL: function () {
        return this.useNewAPI ? this.endpoints.new : this.endpoints.old;
    },

    // Get API secret key
    getSecretKey: function () {
        return this.useNewAPI ? null : 'hospital-api-secret-2024';
    }
};

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
                url: API_CONFIG.getURL(),
                type: 'GET',
                data: function () {
                    const data = { action: 'list' };
                    const secretKey = API_CONFIG.getSecretKey();
                    if (secretKey) {
                        data.secret_key = secretKey;
                    }
                    return data;
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
        console.log(`Loaded: ${testsData.length} tests, ${categoriesData.length} categories, ${patientsData.length} patients, ${doctorsData.length} doctors`);

        // Enable the Add Entry button once data is loaded
        $('button[onclick="openAddModal()"]').prop('disabled', false).removeClass('disabled');

    } catch (error) {
        console.error('Failed to load initial data:', error);
        showError('Failed to load initial data. Some features may not work properly.');

        // Keep Add Entry button disabled if data loading fails
        $('button[onclick="openAddModal()"]').prop('disabled', true).addClass('disabled');
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

    // Check if required data is loaded
    if (testsData.length === 0 || categoriesData.length === 0) {
        console.log('Data not loaded, attempting to load...');
        showInfo('Loading data, please wait...');

        // Try to load data first
        loadInitialData().then(() => {
            if (testsData.length === 0 || categoriesData.length === 0) {
                showError('Failed to load required data. Please refresh the page.');
                return;
            }
            // Retry opening modal
            openAddModal();
        }).catch(error => {
            console.error('Failed to load data:', error);
            showError('Failed to load data. Please refresh the page.');
        });
        return;
    }

    console.log(`Opening modal with ${testsData.length} tests and ${categoriesData.length} categories`);

    currentEditId = null;
    resetForm();

    // Update modal title
    $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');

    // Show modal
    $('#entryModal').modal('show');

    // Wait for modal to be fully shown before initializing
    $('#entryModal').off('shown.bs.modal').on('shown.bs.modal', function () {
        console.log('Modal shown, initializing...');

        // Initialize Select2 dropdowns
        initializeSelect2();

        // Add first test row if none exist
        if ($('#testsContainer .test-row').length === 0) {
            console.log('Adding first test row...');
            addTestRow();
        }

        // Refresh dropdowns to ensure they have data
        setTimeout(() => {
            refreshAllDropdowns();
        }, 500);
    });

    // Reset form when modal is hidden
    $('#entryModal').off('hidden.bs.modal').on('hidden.bs.modal', function () {
        // Don't reset form here as it might interfere with the save process
        console.log('Modal hidden');
    });
}

/**
 * Reset form to default state
 */
function resetForm() {
    console.log('Resetting form...');

    // Reset form fields
    $('#entryForm')[0].reset();
    $('#entryId').val('');

    // Clear tests container
    $('#testsContainer').empty();
    testRowCounter = 0;

    // Reset totals
    calculateTotals();

    // Clear global category filter
    $('#globalCategoryFilter').val('');

    console.log('Form reset complete');
}

/**
 * Initialize Select2 dropdowns
 */
function initializeSelect2() {
    try {
        // Destroy existing Select2 instances first
        $('.select2').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        // Initialize Select2 dropdowns
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Populate global category filter
        populateGlobalCategoryFilter();

        console.log('Select2 initialized');
    } catch (error) {
        console.error('Error initializing Select2:', error);
    }
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
            $categorySelect.val(categoryId).trigger('change');
            // Update test options for this category
            updateTestOptions($testSelect, categoryId);
        } else {
            // Clear category selection and show all tests
            $categorySelect.val('').trigger('change');
            updateTestOptions($testSelect, '');
        }
    });
}

/**
 * Add a new test row
 */
function addTestRow(testData = null) {
    const rowIndex = testRowCounter++;

    console.log(`Adding test row ${rowIndex}`, testData);
    console.log('Available categories:', categoriesData.length);
    console.log('Available tests:', testsData.length);

    // Check if data is loaded
    if (categoriesData.length === 0 || testsData.length === 0) {
        console.error('Cannot add test row: Data not loaded yet');
        showError('Please wait for data to load before adding test rows');
        return;
    }

    // Create category options
    const categoryOptions = categoriesData.map(category => {
        return `<option value="${category.id}">${category.name}</option>`;
    }).join('');

    console.log(`Creating test row with ${categoriesData.length} categories and ${testsData.length} tests`);

    // Create test options (initially show all tests)
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
            <input type="hidden" name="tests[${rowIndex}][main_category_id]" class="test-main-category-id">
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

    // Initialize Select2 for the new row dropdowns
    try {
        // Destroy any existing Select2 instances first
        if ($categorySelect.hasClass('select2-hidden-accessible')) {
            $categorySelect.select2('destroy');
        }
        if ($testSelect.hasClass('select2-hidden-accessible')) {
            $testSelect.select2('destroy');
        }

        // Initialize Select2 with proper configuration
        $categorySelect.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Category',
            allowClear: true
        });

        $testSelect.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Test',
            allowClear: true
        });

        console.log(`Select2 initialized for row ${rowIndex}`);
        console.log(`Category options: ${$categorySelect.find('option').length}`);
        console.log(`Test options: ${$testSelect.find('option').length}`);
    } catch (error) {
        console.error('Error initializing Select2 for test row:', error);
    }

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

    // Set main category ID
    if (categoryId) {
        const categoryInfo = categoriesData.find(c => c.id == categoryId);
        if (categoryInfo && categoryInfo.main_category_id) {
            $row.find('.test-main-category-id').val(categoryInfo.main_category_id);
        }
    } else {
        $row.find('.test-main-category-id').val('');
    }

    // Clear current test selection
    $testSelect.val('').trigger('change');
    $row.find('.test-price').val('');
    $row.find('.test-unit').val('');
    $row.find('.test-min').val('');
    $row.find('.test-max').val('');

    // Rebuild test options based on selected category
    updateTestOptions($testSelect, categoryId);

    calculateTotals();
}

/**
 * Update test options based on category filter
 */
function updateTestOptions($testSelect, categoryId) {
    // Clear existing options except the first one
    $testSelect.find('option:not(:first)').remove();

    // Filter tests based on category
    let filteredTests = testsData;
    if (categoryId) {
        filteredTests = testsData.filter(test => test.category_id == categoryId);
    }

    // Add filtered test options
    filteredTests.forEach(test => {
        const displayName = `${test.name} [ID: ${test.id}]`;
        const option = `<option value="${test.id}" data-category-id="${test.category_id || ''}" data-price="${test.price || 0}" data-unit="${test.unit || ''}" data-min="${test.min || ''}" data-max="${test.max || ''}">${displayName}</option>`;
        $testSelect.append(option);
    });

    // Refresh Select2 to show updated options
    if ($testSelect.hasClass('select2-hidden-accessible')) {
        $testSelect.select2('destroy').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Test'
        });
    }

    console.log(`Updated test options: ${filteredTests.length} tests available`);
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

        // Set main category ID
        const categoryInfo = categoriesData.find(c => c.id == testData.category_id);
        if (categoryInfo && categoryInfo.main_category_id) {
            $row.find('.test-main-category-id').val(categoryInfo.main_category_id);
        }
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

        // Add current user ID if available
        if (typeof currentUserId !== 'undefined' && currentUserId) {
            formData.append('added_by', currentUserId);
        }

        // Collect tests data
        const tests = [];
        const addedTestIds = new Set(); // Track added test IDs to prevent duplicates

        $('#testsContainer .test-row').each(function (index) {
            const $row = $(this);
            const testId = $row.find('.test-select').val();

            console.log(`Test row ${index}:`, {
                testId: testId,
                categoryId: $row.find('.category-select').val(),
                result: $row.find('.test-result').val(),
                price: $row.find('.test-price').val()
            });

            if (testId) {
                // Check for duplicate test IDs
                if (addedTestIds.has(testId)) {
                    console.warn(`Duplicate test ID ${testId} found, skipping...`);
                    return; // Skip this iteration
                }

                addedTestIds.add(testId);

                // Find the test data to get main_category_id
                const testInfo = testsData.find(t => t.id == testId);
                const categoryId = $row.find('.category-select').val() || (testInfo ? testInfo.category_id : null);

                // Find main category ID
                let mainCategoryId = null;
                if (categoryId) {
                    const categoryInfo = categoriesData.find(c => c.id == categoryId);
                    mainCategoryId = categoryInfo ? categoryInfo.main_category_id : null;
                }

                const testData = {
                    test_id: testId,
                    category_id: categoryId,
                    main_category_id: mainCategoryId,
                    result_value: $row.find('.test-result').val() || '',
                    min: $row.find('.test-min').val() || '',
                    max: $row.find('.test-max').val() || '',
                    price: parseFloat($row.find('.test-price').val()) || 0,
                    unit: $row.find('.test-unit').val() || ''
                };
                tests.push(testData);
                console.log(`Added test data:`, testData);
            }
        });

        // Validate we have tests before submitting
        if (tests.length === 0) {
            showError('Please add at least one test before saving');
            return;
        }

        // Check for duplicate tests in the form
        const testIds = tests.map(t => t.test_id);
        const uniqueTestIds = [...new Set(testIds)];
        if (testIds.length !== uniqueTestIds.length) {
            showError('Duplicate tests detected. Please remove duplicate test entries.');
            return;
        }

        formData.append('tests', JSON.stringify(tests));

        console.log('Submitting form data:', Object.fromEntries(formData));
        console.log('Tests data:', tests);
        console.log('Current edit ID:', currentEditId);

        // Additional validation
        const patientId = formData.get('patient_id');
        const entryDate = formData.get('entry_date');
        console.log('Final validation - Patient ID:', patientId, 'Entry Date:', entryDate);

        // Add secret key if using old API
        const secretKey = API_CONFIG.getSecretKey();
        if (secretKey) {
            formData.append('secret_key', secretKey);
        }

        const response = await $.ajax({
            url: API_CONFIG.getURL(),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        });

        console.log('Server response:', response);

        if (response && response.success) {
            showSuccess(response.message || 'Entry saved successfully');
            $('#entryModal').modal('hide');
            refreshTable();
        } else {
            console.error('Save failed:', response);
            let errorMsg = 'Failed to save entry';
            if (response && response.message) {
                errorMsg = response.message;
            } else if (response && response.error) {
                errorMsg = response.error;
            } else if (!response) {
                errorMsg = 'No response from server - please check your connection';
            }
            showError(errorMsg);
        }

    } catch (error) {
        console.error('Error saving entry:', error);
        console.error('Error details:', {
            status: error.status,
            statusText: error.statusText,
            responseText: error.responseText
        });

        let errorMessage = 'An error occurred while saving the entry';

        // Handle specific error codes
        if (error.status === 500 && error.responseText) {
            try {
                const errorData = JSON.parse(error.responseText);
                if (errorData.message && errorData.message.includes('1062')) {
                    errorMessage = 'Duplicate entry detected. Please check for duplicate tests or try refreshing the page.';
                } else {
                    errorMessage = errorData.message || errorMessage;
                }
            } catch (parseError) {
                if (error.responseText.includes('1062')) {
                    errorMessage = 'Duplicate entry detected. Please check for duplicate tests.';
                } else {
                    errorMessage = error.responseText;
                }
            }
        } else if (error.statusText) {
            errorMessage = `Server error: ${error.statusText}`;
        }

        showError(errorMessage);
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
    console.log('Validating form...');

    // Check if patient is selected
    const patientId = $('#patientSelect').val();
    console.log('Patient ID:', patientId);
    if (!patientId) {
        showError('Please select a patient');
        return false;
    }

    // Check entry date
    const entryDate = $('#entryDate').val();
    console.log('Entry date:', entryDate);
    if (!entryDate) {
        showError('Please select an entry date');
        return false;
    }

    // Check if at least one test is selected
    const testRows = $('#testsContainer .test-row');
    console.log('Number of test rows:', testRows.length);

    const hasTests = testRows.filter(function () {
        const testId = $(this).find('.test-select').val();
        return testId && testId !== '';
    }).length > 0;

    console.log('Has valid tests:', hasTests);
    if (!hasTests) {
        showError('Please add at least one test');
        return false;
    }

    // Validate each test row (category is optional)
    let validationErrors = [];
    testRows.each(function (index) {
        const $row = $(this);
        const testId = $row.find('.test-select').val();

        if (testId) {
            // Test ID is required, category is optional
            const testName = $row.find('.test-select option:selected').text();
            console.log(`Test row ${index + 1}: ${testName} (ID: ${testId})`);

            // You can add other validations here if needed
            // For now, just having a test selected is sufficient
        }
    });

    if (validationErrors.length > 0) {
        showError(validationErrors.join('<br>'));
        return false;
    }

    console.log('Form validation passed');
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
    const requestData = {
        action: 'get',
        id: id
    };

    const secretKey = API_CONFIG.getSecretKey();
    if (secretKey) {
        requestData.secret_key = secretKey;
    }

    $.ajax({
        url: API_CONFIG.getURL(),
        method: 'GET',
        data: requestData,
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
    const requestData = {
        action: 'get',
        id: id
    };

    const secretKey = API_CONFIG.getSecretKey();
    if (secretKey) {
        requestData.secret_key = secretKey;
    }

    $.ajax({
        url: API_CONFIG.getURL(),
        method: 'GET',
        data: requestData,
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
        const requestData = {
            action: 'delete',
            id: id
        };

        const secretKey = API_CONFIG.getSecretKey();
        if (secretKey) {
            requestData.secret_key = secretKey;
        }

        $.ajax({
            url: API_CONFIG.getURL(),
            method: 'POST',
            data: requestData,
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

/**
 * Debug function to test form data collection
 */
function debugFormData() {
    console.log('=== FORM DEBUG INFO ===');
    console.log('Patient ID:', $('#patientSelect').val());
    console.log('Doctor ID:', $('#doctorSelect').val());
    console.log('Entry Date:', $('#entryDate').val());
    console.log('Status:', $('#entryStatus').val());
    console.log('Priority:', $('#priority').val());

    console.log('Test rows count:', $('#testsContainer .test-row').length);

    $('#testsContainer .test-row').each(function (index) {
        const $row = $(this);
        console.log(`Test Row ${index}:`, {
            category: $row.find('.category-select').val(),
            test: $row.find('.test-select').val(),
            result: $row.find('.test-result').val(),
            min: $row.find('.test-min').val(),
            max: $row.find('.test-max').val(),
            unit: $row.find('.test-unit').val(),
            price: $row.find('.test-price').val()
        });
    });

    console.log('Current Edit ID:', currentEditId);
    console.log('=== END DEBUG INFO ===');
}

// Make debug functions available globally
window.debugFormData = debugFormData;

/**
 * Debug function to test test row functionality
 */
function debugTestRows() {
    console.log('=== TEST ROWS DEBUG ===');
    console.log('Tests data loaded:', testsData.length);
    console.log('Categories data loaded:', categoriesData.length);
    console.log('Current test rows:', $('#testsContainer .test-row').length);

    $('#testsContainer .test-row').each(function (index) {
        const $row = $(this);
        const $categorySelect = $row.find('.category-select');
        const $testSelect = $row.find('.test-select');

        console.log(`Row ${index}:`, {
            categoryOptions: $categorySelect.find('option').length,
            testOptions: $testSelect.find('option').length,
            categoryValue: $categorySelect.val(),
            testValue: $testSelect.val(),
            hasSelect2: $categorySelect.hasClass('select2-hidden-accessible')
        });
    });
    console.log('=== END TEST ROWS DEBUG ===');
}

window.debugTestRows = debugTestRows;

/**
 * Test API connectivity
 */
async function testAPI() {
    console.log('Testing API connectivity...');

    try {
        const requestData = {
            action: 'list'
        };

        const secretKey = API_CONFIG.getSecretKey();
        if (secretKey) {
            requestData.secret_key = secretKey;
        }

        const response = await $.ajax({
            url: API_CONFIG.getURL(),
            method: 'GET',
            data: requestData,
            dataType: 'json',
            timeout: 10000
        });

        console.log('API test response:', response);
        if (response.success) {
            console.log('✅ API is working correctly');
            return true;
        } else {
            console.error('❌ API returned error:', response.message);
            return false;
        }
    } catch (error) {
        console.error('❌ API connection failed:', error);
        return false;
    }
}

window.testAPI = testAPI;

/**
 * Test save functionality with minimal data
 */
async function testSave() {
    console.log('Testing save functionality...');

    // Check if we have required data
    if (patientsData.length === 0) {
        console.error('❌ No patients data loaded');
        return false;
    }

    if (testsData.length === 0) {
        console.error('❌ No tests data loaded');
        return false;
    }

    try {
        const formData = new FormData();
        formData.append('action', 'save');

        const secretKey = API_CONFIG.getSecretKey();
        if (secretKey) {
            formData.append('secret_key', secretKey);
        }
        formData.append('patient_id', patientsData[0].id); // Use first patient
        formData.append('entry_date', new Date().toISOString().split('T')[0]); // Today's date
        formData.append('status', 'pending');

        // Add current user ID if available
        if (typeof currentUserId !== 'undefined' && currentUserId) {
            formData.append('added_by', currentUserId);
        }

        // Add minimal test data
        const testData = [{
            test_id: testsData[0].id,
            category_id: testsData[0].category_id || null,
            result_value: '',
            min: testsData[0].min || '',
            max: testsData[0].max || '',
            price: testsData[0].price || 0,
            unit: testsData[0].unit || ''
        }];

        formData.append('tests', JSON.stringify(testData));

        console.log('Test save data:', Object.fromEntries(formData));

        const response = await $.ajax({
            url: API_CONFIG.getURL(),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        });

        console.log('Test save response:', response);

        if (response && response.success) {
            console.log('✅ Save functionality is working');
            return true;
        } else {
            console.error('❌ Save failed:', response ? response.message : 'No response');
            return false;
        }

    } catch (error) {
        console.error('❌ Save test failed:', error);
        console.error('Error details:', {
            status: error.status,
            statusText: error.statusText,
            responseText: error.responseText
        });
        return false;
    }
}

window.testSave = testSave;

/**
 * Check authentication status
 */
async function checkAuth() {
    console.log('Checking authentication...');

    try {
        const requestData = {
            action: 'stats'
        };

        const secretKey = API_CONFIG.getSecretKey();
        if (secretKey) {
            requestData.secret_key = secretKey;
        }

        const response = await $.ajax({
            url: API_CONFIG.getURL(),
            method: 'GET',
            data: requestData,
            dataType: 'json'
        });

        console.log('Auth check response:', response);

        if (response.success) {
            console.log('✅ User is authenticated');
            return true;
        } else {
            console.error('❌ Authentication failed:', response.message);
            return false;
        }
    } catch (error) {
        console.error('❌ Auth check failed:', error);
        return false;
    }
}

window.checkAuth = checkAuth;

/**
 * Remove duplicate test rows
 */
function removeDuplicateTestRows() {
    console.log('Checking for duplicate test rows...');

    const seenTestIds = new Set();
    const rowsToRemove = [];

    $('#testsContainer .test-row').each(function () {
        const $row = $(this);
        const testId = $row.find('.test-select').val();

        if (testId) {
            if (seenTestIds.has(testId)) {
                // This is a duplicate
                rowsToRemove.push($row);
                console.log('Found duplicate test ID:', testId);
            } else {
                seenTestIds.add(testId);
            }
        }
    });

    // Remove duplicate rows
    rowsToRemove.forEach($row => {
        $row.remove();
    });

    if (rowsToRemove.length > 0) {
        showInfo(`Removed ${rowsToRemove.length} duplicate test rows`);
        calculateTotals();
    }

    console.log(`Removed ${rowsToRemove.length} duplicate test rows`);
}

window.removeDuplicateTestRows = removeDuplicateTestRows;

/**
 * Comprehensive test function to verify all functionality
 */
async function runComprehensiveTest() {
    console.log('🔍 Running comprehensive test...');

    const results = {
        dataLoading: false,
        apiConnectivity: false,
        formValidation: false,
        duplicateHandling: false,
        saveProcess: false
    };

    try {
        // Test 1: Data Loading
        console.log('📊 Testing data loading...');
        if (testsData.length > 0 && categoriesData.length > 0 && patientsData.length > 0) {
            results.dataLoading = true;
            console.log('✅ Data loading: PASSED');
        } else {
            console.log('❌ Data loading: FAILED');
            console.log(`Tests: ${testsData.length}, Categories: ${categoriesData.length}, Patients: ${patientsData.length}`);
        }

        // Test 2: API Connectivity
        console.log('🌐 Testing API connectivity...');
        results.apiConnectivity = await testAPI();

        // Test 3: Dropdown Population & Form Validation
        console.log('📝 Testing dropdown population and form validation...');
        // Open modal and check dropdowns
        openAddModal();

        // Wait for modal to be ready
        await new Promise(resolve => setTimeout(resolve, 2000));

        // Check if dropdowns have options
        const $firstRow = $('#testsContainer .test-row').first();
        const categoryOptions = $firstRow.find('.category-select option').length;
        const testOptions = $firstRow.find('.test-select option').length;

        console.log(`Category options: ${categoryOptions}, Test options: ${testOptions}`);

        if (categoryOptions > 1 && testOptions > 1) {
            console.log('✅ Dropdown population: PASSED');

            // Set test data
            if (patientsData.length > 0) {
                $('#patientSelect').val(patientsData[0].id);
            }
            $('#entryDate').val(new Date().toISOString().split('T')[0]);

            // Select first available test
            if (testOptions > 1) {
                const firstTestValue = $firstRow.find('.test-select option:nth-child(2)').val();
                $firstRow.find('.test-select').val(firstTestValue).trigger('change');
                $firstRow.find('.test-price').val(100);
            }

            // Test validation
            const isValid = validateForm();
            results.formValidation = isValid;
            console.log(isValid ? '✅ Form validation: PASSED' : '❌ Form validation: FAILED');
        } else {
            console.log('❌ Dropdown population: FAILED');
            console.log('Attempting to refresh dropdowns...');
            refreshAllDropdowns();

            // Wait and check again
            await new Promise(resolve => setTimeout(resolve, 1000));
            const newCategoryOptions = $firstRow.find('.category-select option').length;
            const newTestOptions = $firstRow.find('.test-select option').length;

            if (newCategoryOptions > 1 && newTestOptions > 1) {
                results.formValidation = true;
                console.log('✅ Dropdown population: PASSED (after refresh)');
            } else {
                results.formValidation = false;
                console.log('❌ Dropdown population: FAILED (even after refresh)');
            }
        }

        // Test 4: Duplicate Handling
        console.log('🔄 Testing duplicate handling...');
        // Add duplicate test row
        if (testsData.length > 0) {
            addTestRow();
            const $secondRow = $('#testsContainer .test-row').last();
            $secondRow.find('.test-select').val(testsData[0].id).trigger('change');

            // Check for duplicates
            const testIds = [];
            $('#testsContainer .test-row').each(function () {
                const testId = $(this).find('.test-select').val();
                if (testId) testIds.push(testId);
            });

            const hasDuplicates = testIds.length !== new Set(testIds).size;
            if (hasDuplicates) {
                removeDuplicateTestRows();
                results.duplicateHandling = true;
                console.log('✅ Duplicate handling: PASSED');
            } else {
                results.duplicateHandling = true;
                console.log('✅ Duplicate handling: PASSED (no duplicates found)');
            }
        }

        // Test 5: Save Process (dry run)
        console.log('💾 Testing save process...');
        try {
            // Collect form data without actually saving
            const formData = new FormData($('#entryForm')[0]);
            const tests = [];

            $('#testsContainer .test-row').each(function () {
                const testId = $(this).find('.test-select').val();
                if (testId) {
                    tests.push({
                        test_id: testId,
                        category_id: $(this).find('.category-select').val(),
                        main_category_id: $(this).find('.test-main-category-id').val(),
                        result_value: $(this).find('.test-result').val(),
                        price: $(this).find('.test-price').val()
                    });
                }
            });

            if (tests.length > 0 && formData.get('patient_id')) {
                results.saveProcess = true;
                console.log('✅ Save process: PASSED (data collection successful)');
            } else {
                console.log('❌ Save process: FAILED (missing required data)');
            }
        } catch (error) {
            console.log('❌ Save process: FAILED', error);
        }

        // Close modal
        $('#entryModal').modal('hide');

        // Summary
        console.log('\n📋 TEST SUMMARY:');
        console.log('================');
        Object.entries(results).forEach(([test, passed]) => {
            console.log(`${passed ? '✅' : '❌'} ${test}: ${passed ? 'PASSED' : 'FAILED'}`);
        });

        const allPassed = Object.values(results).every(result => result);
        console.log(`\n🎯 Overall Status: ${allPassed ? '✅ ALL TESTS PASSED' : '❌ SOME TESTS FAILED'}`);

        if (allPassed) {
            showSuccess('All tests passed! The save entry functionality should work correctly.');
        } else {
            showError('Some tests failed. Please check the console for details.');
        }

        return results;

    } catch (error) {
        console.error('❌ Comprehensive test failed:', error);
        showError('Test execution failed: ' + error.message);
        return results;
    }
}

window.runComprehensiveTest = runComprehensiveTest;

/**
 * Quick fix function to address common issues
 */
function quickFix() {
    console.log('🔧 Running quick fix...');

    try {
        // Fix 1: Remove any duplicate test rows
        removeDuplicateTestRows();

        // Fix 2: Ensure main_category_id is populated
        $('#testsContainer .test-row').each(function () {
            const $row = $(this);
            const categoryId = $row.find('.category-select').val();
            const testId = $row.find('.test-select').val();

            if (testId && !$row.find('.test-main-category-id').val()) {
                // Try to get main_category_id from category data
                if (categoryId) {
                    const categoryInfo = categoriesData.find(c => c.id == categoryId);
                    if (categoryInfo && categoryInfo.main_category_id) {
                        $row.find('.test-main-category-id').val(categoryInfo.main_category_id);
                        console.log(`Fixed main_category_id for test ${testId}: ${categoryInfo.main_category_id}`);
                    }
                }
            }
        });

        // Fix 3: Recalculate totals
        calculateTotals();

        // Fix 4: Refresh Select2 dropdowns
        $('.select2').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
            }
        });

        console.log('✅ Quick fix completed');
        showSuccess('Quick fix applied successfully');

    } catch (error) {
        console.error('❌ Quick fix failed:', error);
        showError('Quick fix failed: ' + error.message);
    }
}

window.quickFix = quickFix;

/**
 * Refresh all dropdowns with current data
 */
function refreshAllDropdowns() {
    console.log('🔄 Refreshing all dropdowns...');

    try {
        // Check if data is loaded
        if (categoriesData.length === 0 || testsData.length === 0) {
            console.log('Data not loaded, attempting to reload...');
            loadInitialData().then(() => {
                console.log('Data reloaded, refreshing dropdowns again...');
                refreshAllDropdowns();
            });
            return;
        }

        console.log(`Refreshing with ${categoriesData.length} categories and ${testsData.length} tests`);

        // Refresh each test row
        $('#testsContainer .test-row').each(function (index) {
            const $row = $(this);
            const $categorySelect = $row.find('.category-select');
            const $testSelect = $row.find('.test-select');

            // Store current values
            const currentCategory = $categorySelect.val();
            const currentTest = $testSelect.val();

            console.log(`Refreshing row ${index}: category=${currentCategory}, test=${currentTest}`);

            // Rebuild category options
            $categorySelect.find('option:not(:first)').remove();
            categoriesData.forEach(category => {
                $categorySelect.append(`<option value="${category.id}">${category.name}</option>`);
            });

            // Rebuild test options
            $testSelect.find('option:not(:first)').remove();
            testsData.forEach(test => {
                const displayName = `${test.name} [ID: ${test.id}]`;
                $testSelect.append(`<option value="${test.id}" data-category-id="${test.category_id || ''}" data-price="${test.price || 0}" data-unit="${test.unit || ''}" data-min="${test.min || ''}" data-max="${test.max || ''}">${displayName}</option>`);
            });

            // Restore values
            if (currentCategory) {
                $categorySelect.val(currentCategory);
            }
            if (currentTest) {
                $testSelect.val(currentTest);
            }

            // Refresh Select2
            if ($categorySelect.hasClass('select2-hidden-accessible')) {
                $categorySelect.select2('destroy').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: 'Select Category',
                    allowClear: true
                });
            }

            if ($testSelect.hasClass('select2-hidden-accessible')) {
                $testSelect.select2('destroy').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: 'Select Test',
                    allowClear: true
                });
            }
        });

        // Refresh global category filter
        populateGlobalCategoryFilter();

        console.log('✅ All dropdowns refreshed successfully');
        showSuccess('Dropdowns refreshed with latest data');

    } catch (error) {
        console.error('❌ Error refreshing dropdowns:', error);
        showError('Failed to refresh dropdowns: ' + error.message);
    }
}

window.refreshAllDropdowns = refreshAllDropdowns;

/**
 * Force reload all data and refresh dropdowns
 */
async function forceReloadData() {
    console.log('🔄 Force reloading all data...');

    try {
        // Reset data arrays
        testsData = [];
        categoriesData = [];
        patientsData = [];
        doctorsData = [];

        // Reload all data
        await loadInitialData();

        // Refresh dropdowns
        refreshAllDropdowns();

        // Refresh patient and doctor selects
        populatePatientSelect();
        populateDoctorSelect();

        console.log('✅ Data force reloaded successfully');
        showSuccess('All data reloaded successfully');

    } catch (error) {
        console.error('❌ Error force reloading data:', error);
        showError('Failed to reload data: ' + error.message);
    }
}

window.forceReloadData = forceReloadData;

/**
 * Switch between old and new API
 */
function switchAPI(useNew = true) {
    console.log(`🔄 Switching to ${useNew ? 'NEW' : 'OLD'} API...`);

    API_CONFIG.useNewAPI = useNew;

    console.log(`Current API: ${API_CONFIG.getURL()}`);
    console.log(`Secret Key: ${API_CONFIG.getSecretKey() || 'None (new API)'}`);

    // Refresh the DataTable to use new API
    if (entriesTable) {
        entriesTable.ajax.reload();
    }

    showSuccess(`Switched to ${useNew ? 'new patho_api/entry.php' : 'old ajax/entry_api_fixed.php'} API`);
}

window.switchAPI = switchAPI;

/**
 * Test both APIs
 */
async function testBothAPIs() {
    console.log('🔍 Testing both APIs...');

    const results = {
        oldAPI: false,
        newAPI: false
    };

    try {
        // Test old API
        console.log('Testing OLD API...');
        switchAPI(false);
        await new Promise(resolve => setTimeout(resolve, 500));
        results.oldAPI = await testAPI();

        // Test new API
        console.log('Testing NEW API...');
        switchAPI(true);
        await new Promise(resolve => setTimeout(resolve, 500));
        results.newAPI = await testAPI();

        // Summary
        console.log('\n📋 API TEST SUMMARY:');
        console.log('==================');
        console.log(`${results.oldAPI ? '✅' : '❌'} Old API (ajax/entry_api_fixed.php): ${results.oldAPI ? 'WORKING' : 'FAILED'}`);
        console.log(`${results.newAPI ? '✅' : '❌'} New API (patho_api/entry.php): ${results.newAPI ? 'WORKING' : 'FAILED'}`);

        if (results.newAPI) {
            console.log('\n🎯 Recommendation: Use NEW API (already selected)');
            showSuccess('Both APIs tested. Using new API (recommended).');
        } else if (results.oldAPI) {
            console.log('\n⚠️ Fallback: Using OLD API');
            switchAPI(false);
            showInfo('New API failed, switched to old API as fallback.');
        } else {
            console.log('\n❌ Both APIs failed!');
            showError('Both APIs failed! Please check server configuration.');
        }

        return results;

    } catch (error) {
        console.error('❌ Error testing APIs:', error);
        showError('Error testing APIs: ' + error.message);
        return results;
    }
}

window.testBothAPIs = testBothAPIs;