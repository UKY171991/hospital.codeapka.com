/**
 * Minimal Entry List Management - Clean version to fix console issues
 */

// Global variables
let entriesTable = null;
let testsData = [];
let categoriesData = [];
let patientsData = [];
let doctorsData = [];

/**
 * Initialize DataTable with minimal configuration
 */
function initializeDataTable() {
    console.log('Initializing minimal DataTable...');

    try {
        // Destroy existing table if it exists
        if ($.fn.DataTable.isDataTable('#entriesTable')) {
            $('#entriesTable').DataTable().destroy();
        }

        entriesTable = $('#entriesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: 'patho_api/entry.php',
                type: 'GET',
                data: { 
                    action: 'list',
                    secret_key: 'hospital-api-secret-2024'
                },
                dataSrc: function (json) {
                    console.log('DataTable response:', json);
                    if (json && json.success && json.data) {
                        return json.data;
                    } else if (json && Array.isArray(json)) {
                        return json;
                    } else {
                        console.error('Invalid response format:', json);
                        showError('Failed to load entries');
                        return [];
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTable AJAX error:', error, thrown);
                    console.error('Response:', xhr.responseText);
                    showError('Failed to load entries. Please refresh the page.');
                }
            },
            columns: [
                { data: 'id', title: 'ID', defaultContent: 'N/A' },
                { data: 'patient_name', title: 'Patient', defaultContent: 'N/A' },
                { data: 'doctor_name', title: 'Doctor', defaultContent: 'Not assigned' },
                { data: 'test_names', title: 'Tests', defaultContent: 'No tests' },
                { 
                    data: 'status', 
                    title: 'Status', 
                    defaultContent: 'pending',
                    render: function(data) {
                        const status = data || 'pending';
                        const badgeClass = status === 'completed' ? 'success' : status === 'cancelled' ? 'danger' : 'warning';
                        return `<span class="badge badge-${badgeClass}">${status}</span>`;
                    }
                },
                { 
                    data: 'priority', 
                    title: 'Priority', 
                    defaultContent: 'normal',
                    render: function(data) {
                        const priority = data || 'normal';
                        return `<span class="badge badge-info">${priority}</span>`;
                    }
                },
                { 
                    data: 'total_price', 
                    title: 'Amount', 
                    defaultContent: '0.00',
                    render: function(data) {
                        const amount = parseFloat(data) || 0;
                        return `₹${amount.toFixed(2)}`;
                    }
                },
                { 
                    data: 'entry_date', 
                    title: 'Date', 
                    defaultContent: 'N/A',
                    render: function(data) {
                        if (data) {
                            try {
                                return new Date(data).toLocaleDateString('en-IN');
                            } catch (e) {
                                return data;
                            }
                        }
                        return 'N/A';
                    }
                },
                {
                    data: null,
                    title: 'Actions',
                    orderable: false,
                    render: function (data, type, row) {
                        if (row && row.id) {
                            return `
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info btn-sm" onclick="viewEntry(${row.id})" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm" onclick="editEntry(${row.id})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteEntry(${row.id})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                        return '<span class="text-muted">No actions</span>';
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true,
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading entries...',
                emptyTable: 'No entries found. Click "Add Entry" to create your first entry.',
                zeroRecords: 'No matching entries found.',
                loadingRecords: 'Loading...'
            }
        });

        console.log('DataTable initialized successfully');
        return true;
    } catch (error) {
        console.error('Failed to initialize DataTable:', error);
        showError('Failed to initialize data table. Please refresh the page.');
        return false;
    }
}

/**
 * Enhanced message functions with better notifications
 */
function showError(message) {
    console.error('Error:', message);
    
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        // Create custom notification
        showCustomNotification(message, 'error');
    }
}

function showSuccess(message) {
    console.log('Success:', message);
    
    if (typeof toastr !== 'undefined') {
        toastr.success(message);
    } else {
        // Create custom notification
        showCustomNotification(message, 'success');
    }
}

function showInfo(message) {
    console.info('Info:', message);
    
    if (typeof toastr !== 'undefined') {
        toastr.info(message);
    } else {
        // Create custom notification
        showCustomNotification(message, 'info');
    }
}

function showCustomNotification(message, type = 'info') {
    // Remove existing notifications
    $('.custom-notification').remove();
    
    const typeClasses = {
        'error': 'alert-danger',
        'success': 'alert-success',
        'info': 'alert-info',
        'warning': 'alert-warning'
    };
    
    const icons = {
        'error': 'fas fa-exclamation-circle',
        'success': 'fas fa-check-circle',
        'info': 'fas fa-info-circle',
        'warning': 'fas fa-exclamation-triangle'
    };
    
    const alertClass = typeClasses[type] || 'alert-info';
    const icon = icons[type] || 'fas fa-info-circle';
    
    const notificationHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show custom-notification" 
             style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;">
            <i class="${icon} mr-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    $('body').append(notificationHtml);
    
    // Auto-hide after 5 seconds for success/info, 8 seconds for errors
    const duration = type === 'error' ? 8000 : 5000;
    setTimeout(() => {
        $('.custom-notification').fadeOut();
    }, duration);
}

/**
 * Modal and form functions
 */
function openAddModal() {
    console.log('Opening Add Entry modal...');
    
    // Reset form
    resetForm();
    
    // Update modal title
    $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');
    
    // Show modal
    $('#entryModal').modal('show');
    
    // Load initial data when modal is shown
    $('#entryModal').off('shown.bs.modal').on('shown.bs.modal', function () {
        console.log('Modal shown, loading data...');
        loadModalData();
    });
}

function resetForm() {
    console.log('Resetting form...');
    
    // Reset form fields
    $('#entryForm')[0].reset();
    $('#entryId').val('');
    
    // Clear tests container
    $('#testsContainer').empty();
    
    // Reset totals
    $('#subtotal').val('0.00');
    $('#discountAmount').val('0.00');
    $('#totalPrice').val('0.00');
    
    // Set default date
    $('#entryDate').val(new Date().toISOString().split('T')[0]);
    
    // Reset date slot and service location
    $('#dateSlot').val('');
    $('#serviceLocation').val('');
    $('#collectionAddress').val('');
}

function loadModalData() {
    console.log('Loading modal data...');
    
    // Load patients
    loadPatients();
    
    // Load doctors  
    loadDoctors();
    
    // Load tests and categories
    loadTestsAndCategories();
    
    // Add initial test row
    setTimeout(() => {
        addTestRow();
    }, 500);
}

function loadPatients() {
    console.log('Loading patients...');
    
    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                populatePatientSelect(response.data);
            } else {
                console.error('Failed to load patients:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading patients:', error);
        }
    });
}

function loadDoctors() {
    console.log('Loading doctors...');
    
    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                populateDoctorSelect(response.data);
            } else {
                console.error('Failed to load doctors:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading doctors:', error);
        }
    });
}

function loadTestsAndCategories() {
    console.log('Loading tests and categories...');
    
    // Load tests
    $.ajax({
        url: 'ajax/test_api.php',
        method: 'GET',
        data: { action: 'simple_list' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                testsData = response.data;
                console.log('Loaded tests:', testsData.length);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading tests:', error);
        }
    });
    
    // Load categories
    $.ajax({
        url: 'patho_api/test_category.php',
        method: 'GET',
        data: { action: 'list', all: '1' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                categoriesData = response.data;
                console.log('Loaded categories:', categoriesData.length);
                populateGlobalCategoryFilter();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading categories:', error);
        }
    });
}

function populatePatientSelect(patients) {
    const $select = $('#patientSelect');
    $select.empty().append('<option value="">Select Patient</option>');
    
    patients.forEach(patient => {
        if (patient && patient.id && patient.name) {
            const displayName = `${patient.name}${patient.uhid ? ` (${patient.uhid})` : ''}`;
            $select.append(`<option value="${patient.id}">${displayName}</option>`);
        }
    });
    
    // Initialize Select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $select.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Patient'
        });
    }
}

function populateDoctorSelect(doctors) {
    const $select = $('#doctorSelect');
    $select.empty().append('<option value="">Select Doctor</option>');
    
    doctors.forEach(doctor => {
        if (doctor && doctor.id && doctor.name) {
            const displayName = `${doctor.name}${doctor.specialization ? ` (${doctor.specialization})` : ''}`;
            $select.append(`<option value="${doctor.id}">${displayName}</option>`);
        }
    });
    
    // Initialize Select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $select.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Doctor'
        });
    }
}

function populateGlobalCategoryFilter() {
    const $select = $('#globalCategoryFilter');
    $select.find('option:not(:first)').remove();
    
    categoriesData.forEach(category => {
        if (category && category.id && category.name) {
            $select.append(`<option value="${category.id}">${category.name}</option>`);
        }
    });
}

let testRowCounter = 0;

function addTestRow() {
    console.log('Adding test row...');
    
    const rowIndex = testRowCounter++;
    
    // Create category options
    let categoryOptions = '<option value="">Select Category</option>';
    if (categoriesData && categoriesData.length > 0) {
        categoriesData.forEach(category => {
            categoryOptions += `<option value="${category.id}">${category.name}</option>`;
        });
    }
    
    // Create test options
    let testOptions = '<option value="">Select Test</option>';
    if (testsData && testsData.length > 0) {
        testsData.forEach(test => {
            const displayName = `${test.name} [ID: ${test.id}]`;
            testOptions += `<option value="${test.id}" data-price="${test.price || 0}" data-unit="${test.unit || ''}">${displayName}</option>`;
        });
    }
    
    const rowHtml = `
        <div class="test-row row mb-2" data-row-index="${rowIndex}">
            <div class="col-md-2">
                <select class="form-control category-select" name="tests[${rowIndex}][category_id]">
                    ${categoryOptions}
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control test-select" name="tests[${rowIndex}][test_id]" required>
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
    
    // Bind events for the new row
    const $newRow = $(`.test-row[data-row-index="${rowIndex}"]`);
    
    // Category selection change
    $newRow.find('.category-select').on('change', function() {
        const categoryId = $(this).val();
        const $testSelect = $newRow.find('.test-select');
        
        console.log('Category selected:', categoryId);
        
        // Clear current test selection
        $testSelect.val('');
        
        // Update test options based on selected category
        updateTestOptions($testSelect, categoryId);
        
        // Clear test details
        $newRow.find('.test-price').val('');
        $newRow.find('.test-unit').val('');
        $newRow.find('.test-min').val('');
        $newRow.find('.test-max').val('');
        
        calculateTotals();
    });
    
    // Test selection change
    $newRow.find('.test-select').on('change', function() {
        const testId = $(this).val();
        console.log('Test selected:', testId);
        
        if (testId && testsData) {
            const test = testsData.find(t => String(t.id) === String(testId));
            console.log('Found test data:', test);
            
            if (test) {
                // Auto-select category if not already selected
                const $categorySelect = $newRow.find('.category-select');
                if (!$categorySelect.val() && test.category_id) {
                    $categorySelect.val(test.category_id);
                    console.log('Auto-selected category:', test.category_id);
                }
                
                // Populate test details
                $newRow.find('.test-price').val(test.price || 0);
                $newRow.find('.test-unit').val(test.unit || '');
                $newRow.find('.test-min').val(test.min || '');
                $newRow.find('.test-max').val(test.max || '');
                
                console.log('Populated test details:', {
                    price: test.price,
                    unit: test.unit,
                    min: test.min,
                    max: test.max
                });
                
                calculateTotals();
            }
        } else {
            // Clear fields if no test selected
            $newRow.find('.test-price').val('');
            $newRow.find('.test-unit').val('');
            $newRow.find('.test-min').val('');
            $newRow.find('.test-max').val('');
            calculateTotals();
        }
    });
    
    // Price change
    $newRow.find('.test-price').on('input', calculateTotals);
}

function removeTestRow(button) {
    $(button).closest('.test-row').remove();
    calculateTotals();
    
    // Ensure at least one test row exists
    if ($('#testsContainer .test-row').length === 0) {
        addTestRow();
    }
}

function calculateTotals() {
    let subtotal = 0;
    
    $('#testsContainer .test-price').each(function() {
        const price = parseFloat($(this).val()) || 0;
        subtotal += price;
    });
    
    const discount = parseFloat($('#discountAmount').val()) || 0;
    const total = Math.max(subtotal - discount, 0);
    
    $('#subtotal').val(subtotal.toFixed(2));
    $('#totalPrice').val(total.toFixed(2));
}

// Bind form submission
$(document).on('submit', '#entryForm', function(e) {
    e.preventDefault();
    saveEntry();
});

// Bind discount change
$(document).on('input', '#discountAmount', calculateTotals);

function saveEntry() {
    console.log('Saving entry...');
    
    // Basic validation
    const patientId = $('#patientSelect').val();
    const entryDate = $('#entryDate').val();
    const hasTests = $('#testsContainer .test-row .test-select').filter(function() {
        return $(this).val() !== '';
    }).length > 0;
    
    console.log('Validation check:', {
        patientId: patientId,
        entryDate: entryDate,
        hasTests: hasTests
    });
    
    if (!patientId) {
        showError('Please select a patient.');
        return;
    }
    
    if (!entryDate) {
        showError('Please select an entry date.');
        return;
    }
    
    if (!hasTests) {
        showError('Please add at least one test.');
        return;
    }
    
    // Show loading
    const $submitBtn = $('#entryForm button[type="submit"]');
    const originalText = $submitBtn.html();
    $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    // Prepare form data
    const formData = new FormData($('#entryForm')[0]);
    formData.append('action', 'save');
    
    // Add authentication
    formData.append('secret_key', 'hospital-api-secret-2024');
    
    // Add current user ID if available
    if (typeof currentUserId !== 'undefined' && currentUserId) {
        formData.append('added_by', currentUserId);
    }
    
    // Collect tests data
    const tests = [];
    $('#testsContainer .test-row').each(function() {
        const $row = $(this);
        const testId = $row.find('.test-select').val();
        const categoryId = $row.find('.category-select').val();
        
        if (testId) {
            // Find category_id from test data if not selected in dropdown
            let finalCategoryId = categoryId;
            if (!finalCategoryId && testsData) {
                const testInfo = testsData.find(t => t.id == testId);
                if (testInfo && testInfo.category_id) {
                    finalCategoryId = testInfo.category_id;
                }
            }
            
            tests.push({
                test_id: testId,
                category_id: finalCategoryId || null,
                result_value: $row.find('.test-result').val() || '',
                price: parseFloat($row.find('.test-price').val()) || 0,
                unit: $row.find('.test-unit').val() || '',
                min: $row.find('.test-min').val() || '',
                max: $row.find('.test-max').val() || ''
            });
        }
    });
    
    formData.append('tests', JSON.stringify(tests));
    
    console.log('Submitting data:', {
        action: 'save',
        patient_id: patientId,
        entry_date: entryDate,
        tests_count: tests.length,
        tests: tests
    });
    
    // Submit to API
    $.ajax({
        url: 'patho_api/entry.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log('Save response:', response);
            
            if (response && response.success) {
                showSuccess(response.message || 'Entry saved successfully');
                $('#entryModal').modal('hide');
                refreshTable();
            } else {
                console.error('API returned error:', response);
                const errorMessage = response ? (response.message || 'Unknown error occurred') : 'Failed to save entry';
                showError(errorMessage);
                
                // Show detailed error if available
                if (response && response.errors) {
                    console.error('Detailed errors:', response.errors);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Save error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            let errorMessage = 'Failed to save entry. ';
            
            if (xhr.status === 0) {
                errorMessage += 'Network connection error.';
            } else if (xhr.status === 404) {
                errorMessage += 'API endpoint not found.';
            } else if (xhr.status === 500) {
                errorMessage += 'Server error.';
            } else if (xhr.status === 403) {
                errorMessage += 'Access denied.';
            } else {
                errorMessage += `Server returned ${xhr.status}: ${xhr.statusText}`;
            }
            
            // Try to parse error response
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                if (errorResponse && errorResponse.message) {
                    errorMessage = errorResponse.message;
                }
            } catch (e) {
                // Response is not JSON, use default message
            }
            
            showError(errorMessage);
        },
        complete: function() {
            // Restore button
            $submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function refreshTable() {
    if (entriesTable) {
        entriesTable.ajax.reload();
        showSuccess('Table refreshed');
    }
}

function exportEntries() {
    alert('Export feature is being loaded. Please refresh the page and try again.');
}

function refreshTestAggregates() {
    alert('Refresh Test Aggregates feature is being loaded. Please refresh the page and try again.');
}

function diagnoseTestData() {
    alert('Diagnose Test Data feature is being loaded. Please refresh the page and try again.');
}

function addTestColumns() {
    alert('Add Test Columns feature is being loaded. Please refresh the page and try again.');
}

function viewEntry(id) {
    console.log('Viewing entry:', id);
    
    // Show modal
    $('#viewEntryModal').modal('show');
    
    // Load entry data
    $('#entryDetails').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading entry details...</div>');
    
    $.ajax({
        url: 'patho_api/entry.php',
        method: 'GET',
        data: { 
            action: 'get', 
            id: id,
            secret_key: 'hospital-api-secret-2024'
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                displayEntryDetails(response.data);
            } else {
                $('#entryDetails').html('<div class="alert alert-danger">Failed to load entry details.</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading entry:', error);
            $('#entryDetails').html('<div class="alert alert-danger">Error loading entry details.</div>');
        }
    });
}

function displayEntryDetails(entry) {
    const testsHtml = entry.tests && entry.tests.length > 0
        ? entry.tests.map(test => `
            <tr>
                <td>${test.test_name || 'Unknown Test'}</td>
                <td>${test.result_value || 'Pending'}</td>
                <td>${test.unit || '-'}</td>
                <td>₹${parseFloat(test.price || 0).toFixed(2)}</td>
            </tr>
        `).join('')
        : '<tr><td colspan="4" class="text-center text-muted">No tests found</td></tr>';

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
                    ${entry.date_slot ? `<tr><th><i class="fas fa-calendar-alt mr-1"></i>Date Slot:</th><td>${entry.date_slot}</td></tr>` : ''}
                    ${entry.service_location ? `<tr><th><i class="fas fa-map-marker-alt mr-1"></i>Service Location:</th><td>${entry.service_location}</td></tr>` : ''}
                    ${entry.collection_address ? `<tr><th><i class="fas fa-home mr-1"></i>Collection Address:</th><td>${entry.collection_address}</td></tr>` : ''}
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
                            <th>Test Name</th>
                            <th>Result</th>
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

function editEntry(id) {
    console.log('Editing entry:', id);
    
    // Load entry data first
    $.ajax({
        url: 'patho_api/entry.php',
        method: 'GET',
        data: { 
            action: 'get', 
            id: id,
            secret_key: 'hospital-api-secret-2024'
        },
        dataType: 'json',
        success: function(response) {
            if (response && response.success && response.data) {
                populateEditForm(response.data);
                $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');
                $('#entryModal').modal('show');
            } else {
                showError('Failed to load entry for editing');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading entry for edit:', error);
            showError('Error loading entry for editing');
        }
    });
}

function populateEditForm(entry) {
    console.log('Populating edit form with:', entry);
    
    // Load modal data first
    loadModalData();
    
    // Wait a bit for data to load, then populate
    setTimeout(() => {
        // Set basic fields
        $('#entryId').val(entry.id);
        $('#patientSelect').val(entry.patient_id);
        $('#doctorSelect').val(entry.doctor_id);
        $('#entryDate').val(entry.entry_date ? entry.entry_date.split(' ')[0] : '');
        $('#entryStatus').val(entry.status || 'pending');
        $('#priority').val(entry.priority || 'normal');
        $('#dateSlot').val(entry.date_slot || '');
        $('#serviceLocation').val(entry.service_location || '');
        $('#collectionAddress').val(entry.collection_address || '');
        $('#subtotal').val(parseFloat(entry.subtotal || 0).toFixed(2));
        $('#discountAmount').val(parseFloat(entry.discount_amount || 0).toFixed(2));
        $('#totalPrice').val(parseFloat(entry.total_price || 0).toFixed(2));
        $('#entryNotes').val(entry.notes || '');
        
        // Trigger Select2 updates if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('#patientSelect, #doctorSelect').trigger('change');
        }
        
        // Clear and populate test rows
        $('#testsContainer').empty();
        testRowCounter = 0;
        
        if (entry.tests && entry.tests.length > 0) {
            entry.tests.forEach(test => {
                addTestRowWithData(test);
            });
        } else {
            addTestRow();
        }
    }, 1000);
}

function addTestRowWithData(testData) {
    addTestRow();
    
    // Get the last added row
    const $lastRow = $('#testsContainer .test-row').last();
    
    // Populate with data
    setTimeout(() => {
        // Set category first if available
        if (testData.category_id) {
            $lastRow.find('.category-select').val(testData.category_id).trigger('change');
        }
        
        // Then set test (this will auto-populate other fields)
        $lastRow.find('.test-select').val(testData.test_id).trigger('change');
        
        // Override with specific data from the entry
        $lastRow.find('.test-result').val(testData.result_value || '');
        $lastRow.find('.test-price').val(testData.price || testData.test_price || 0);
        $lastRow.find('.test-unit').val(testData.unit || testData.et_unit || '');
        $lastRow.find('.test-min').val(testData.min || testData.min_male || testData.min_female || '');
        $lastRow.find('.test-max').val(testData.max || testData.max_male || testData.max_female || '');
    }, 100);
}

function deleteEntry(id) {
    if (confirm(`Are you sure you want to delete entry ${id}? This action cannot be undone.`)) {
        console.log('Deleting entry:', id);
        
        $.ajax({
            url: 'patho_api/entry.php',
            method: 'POST',
            data: { 
                action: 'delete', 
                id: id,
                secret_key: 'hospital-api-secret-2024'
            },
            dataType: 'json',
            success: function(response) {
                if (response && response.success) {
                    showSuccess(response.message || 'Entry deleted successfully');
                    refreshTable();
                } else {
                    showError(response ? response.message : 'Failed to delete entry');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error deleting entry:', error);
                showError('Error deleting entry');
            }
        });
    }
}

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
                @media print { .no-print { display: none; } }
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
 * Initialize when document is ready
 */
$(document).ready(function () {
    console.log('Minimal Entry List Management - Initializing...');

    try {
        // Initialize DataTable
        initializeDataTable();

        // Bind basic filter events
        $('#statusFilter, #dateFilter').on('change', function () {
            if (entriesTable) {
                entriesTable.draw();
            }
        });

        $('#patientFilter, #doctorFilter').on('keyup', function () {
            if (entriesTable) {
                const searchTerm = $(this).val();
                entriesTable.search(searchTerm).draw();
            }
        });

        // Global category filter events
        $('#globalCategoryFilter').on('change', function () {
            const categoryId = $(this).val();
            applyGlobalCategoryFilter(categoryId);
        });

        $('#clearGlobalCategoryFilter').on('click', function () {
            $('#globalCategoryFilter').val('');
            applyGlobalCategoryFilter('');
        });

        // Service location change event
        $(document).on('change', '#serviceLocation', function () {
            const location = $(this).val();
            const $addressField = $('#collectionAddress').closest('.form-group');
            
            if (location === 'home') {
                $addressField.show();
                $('#collectionAddress').attr('required', true);
            } else {
                $addressField.hide();
                $('#collectionAddress').attr('required', false);
            }
        });

        console.log('Minimal initialization completed successfully');

    } catch (error) {
        console.error('Error during minimal initialization:', error);
        showError('Failed to initialize the page. Please refresh and try again.');
    }
});

function applyGlobalCategoryFilter(categoryId) {
    console.log('Applying global category filter:', categoryId);
    
    // Update all existing test rows
    $('#testsContainer .test-row').each(function () {
        const $row = $(this);
        const $testSelect = $row.find('.test-select');
        
        // Clear current selection
        $testSelect.val('').trigger('change');
        
        // Update test options based on category
        updateTestOptions($testSelect, categoryId);
    });
}

function updateTestOptions($testSelect, categoryId) {
    // Clear existing options except the first one
    $testSelect.find('option:not(:first)').remove();
    
    // Filter tests based on category
    let filteredTests = testsData || [];
    if (categoryId && testsData) {
        // Convert both to strings for comparison to handle type mismatches
        filteredTests = testsData.filter(test => String(test.category_id) === String(categoryId));
        console.log(`Filtering tests by category ${categoryId}: found ${filteredTests.length} tests`);
    }
    
    // Add filtered test options
    filteredTests.forEach(test => {
        const displayName = `${test.name} [ID: ${test.id}]`;
        const option = `<option value="${test.id}" data-price="${test.price || 0}" data-unit="${test.unit || ''}" data-min="${test.min || ''}" data-max="${test.max || ''}">${displayName}</option>`;
        $testSelect.append(option);
    });
    
    console.log(`Updated test options: ${filteredTests.length} tests available for category ${categoryId || 'all'}`);
}

// Make functions available globally
window.initializeDataTable = initializeDataTable;
window.refreshTable = refreshTable;
window.openAddModal = openAddModal;
window.exportEntries = exportEntries;
window.refreshTestAggregates = refreshTestAggregates;
window.diagnoseTestData = diagnoseTestData;
window.addTestColumns = addTestColumns;
window.viewEntry = viewEntry;
window.editEntry = editEntry;
window.deleteEntry = deleteEntry;
window.addTestRow = addTestRow;
window.removeTestRow = removeTestRow;
window.printEntryDetails = printEntryDetails;

console.log('Minimal Entry List Management loaded successfully');/**

 * Debug and troubleshooting functions
 */
function debugModal() {
    console.log('=== MODAL DEBUG ===');
    console.log('Modal element exists:', $('#entryModal').length > 0);
    console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
    console.log('Form element exists:', $('#entryForm').length > 0);
    console.log('Patient select exists:', $('#patientSelect').length > 0);
    console.log('Tests container exists:', $('#testsContainer').length > 0);
    console.log('Tests data loaded:', testsData.length);
    console.log('Categories data loaded:', categoriesData.length);
    console.log('=== END DEBUG ===');
}

function testModal() {
    console.log('Testing modal functionality...');
    
    // Test basic modal show
    try {
        $('#entryModal').modal('show');
        console.log('Modal show: SUCCESS');
        
        setTimeout(() => {
            $('#entryModal').modal('hide');
            console.log('Modal hide: SUCCESS');
        }, 2000);
        
    } catch (error) {
        console.error('Modal test failed:', error);
    }
}

// Make debug functions available globally
window.debugModal = debugModal;
window.testModal = testModal;

console.log('Entry List Management - Minimal version loaded with modal functionality');/**

 * Enhanced debug and troubleshooting functions
 */
function testAPI() {
    console.log('Testing API connection...');
    
    // Test basic API connectivity
    $.ajax({
        url: 'patho_api/entry.php',
        method: 'GET',
        data: { 
            action: 'stats',
            secret_key: 'hospital-api-secret-2024'
        },
        dataType: 'json',
        success: function(response) {
            console.log('API Test - SUCCESS:', response);
            showSuccess('API connection is working');
        },
        error: function(xhr, status, error) {
            console.error('API Test - FAILED:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            showError('API connection failed: ' + error);
        }
    });
}

function debugSaveForm() {
    console.log('=== SAVE FORM DEBUG ===');
    
    // Check form data
    const formData = new FormData($('#entryForm')[0]);
    console.log('Form data entries:');
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    
    // Check validation
    const patientId = $('#patientSelect').val();
    const entryDate = $('#entryDate').val();
    const hasTests = $('#testsContainer .test-row .test-select').filter(function() {
        return $(this).val() !== '';
    }).length > 0;
    
    console.log('Validation status:', {
        patientId: patientId,
        entryDate: entryDate,
        hasTests: hasTests,
        testRowsCount: $('#testsContainer .test-row').length
    });
    
    // Check tests data
    const tests = [];
    $('#testsContainer .test-row').each(function(index) {
        const $row = $(this);
        const testId = $row.find('.test-select').val();
        const categoryId = $row.find('.category-select').val();
        
        console.log(`Test row ${index}:`, {
            testId: testId,
            categoryId: categoryId,
            result: $row.find('.test-result').val(),
            price: $row.find('.test-price').val(),
            unit: $row.find('.test-unit').val()
        });
        
        if (testId) {
            // Find category_id from test data if not selected in dropdown
            let finalCategoryId = categoryId;
            if (!finalCategoryId && testsData) {
                const testInfo = testsData.find(t => t.id == testId);
                if (testInfo && testInfo.category_id) {
                    finalCategoryId = testInfo.category_id;
                }
            }
            
            tests.push({
                test_id: testId,
                category_id: finalCategoryId || null,
                result_value: $row.find('.test-result').val() || '',
                price: parseFloat($row.find('.test-price').val()) || 0,
                unit: $row.find('.test-unit').val() || ''
            });
        }
    });
    
    console.log('Tests to be saved:', tests);
    console.log('=== END DEBUG ===');
}

function testSaveWithDebug() {
    console.log('Testing save with debug...');
    debugSaveForm();
    
    // Try to save
    if (confirm('Do you want to proceed with the save test?')) {
        saveEntry();
    }
}

// Make additional debug functions available globally
window.testAPI = testAPI;
window.debugSaveForm = debugSaveForm;
window.testSaveWithDebug = testSaveWithDebug;