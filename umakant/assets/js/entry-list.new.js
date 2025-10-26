// Global variables
let entriesTable;
let currentEntryId = null;
let testRowCount = 1;

// Debug: Log that the file is loaded
console.log('entry-list.new.js loaded successfully');

// Safety check for required libraries
function checkDependencies() {
    const missing = [];

    if (typeof $ === 'undefined') missing.push('jQuery');
    if (typeof $.fn.dataTable === 'undefined') missing.push('DataTables');
    if (typeof $.fn.select2 === 'undefined') missing.push('Select2');
    if (typeof toastr === 'undefined') missing.push('Toastr');

    if (missing.length > 0) {
        console.error('Missing required libraries:', missing.join(', '));
        return false;
    }
    console.log('All required libraries are loaded successfully');
    return true;
}

// Initialize page when document is ready
$(document).ready(function () {
    console.log('Document ready - starting initialization');
    
    // Add a small delay to ensure all libraries are fully loaded
    setTimeout(function() {
        // Check dependencies first
        if (!checkDependencies()) {
            console.error('Dependencies check failed');
            return;
        }

        try {
            console.log('Dependencies OK, initializing page...');
            initializePage();
        } catch (error) {
            console.error('Error initializing page:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Error initializing page. Please refresh and try again.');
            }
        }
    }, 100);
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
        success: function (response) {
            if (response.success) {
                $('#totalEntries').text(response.data.total || 0);
                $('#pendingEntries').text(response.data.pending || 0);
                $('#completedEntries').text(response.data.completed || 0);
                $('#todayEntries').text(response.data.today || 0);
            } else {
                console.error('Statistics API returned error:', response);
            }
        },
        error: function (xhr, status, error) {
            console.error('Failed to load statistics:', error);
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

        // Avoid re-initializing DataTable
        if (window._entriesTableInitialized) {
            console.log('DataTable already initialized');
            return;
        }

        console.log('Initializing new DataTable...');
        
        entriesTable = $('#entriesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: 'ajax/entry_api_fixed.php',
                type: 'GET',
                dataType: 'json',
                data: { action: 'list' },
                dataSrc: function (response) {
                    if (response && response.success) {
                        return response.data || [];
                    }
                    console.error('Entries list returned an error:', response);
                    return [];
                },
                error: function (xhr, textStatus, errorThrown) {
                    if (textStatus === 'abort' || xhr.status === 0) {
                        return;
                    }
                    console.error('Entries list AJAX error:', errorThrown);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load entries');
                    }
                }
            },
            columns: [
                {
                    data: 'id',
                    render: function (data) {
                        return '<span class="badge badge-primary">#' + data + '</span>';
                    }
                },
                {
                    data: 'patient_name',
                    render: function (data, type, row) {
                        let html = '<div><strong>' + (data || 'N/A') + '</strong>';
                        if (row.uhid) {
                            html += '<br><small class="text-muted">UHID: ' + row.uhid + '</small>';
                        }
                        if (row.age) {
                            html += '<br><small class="text-muted">Age: ' + row.age + ' ' + (row.gender || '') + '</small>';
                        }
                        html += '</div>';
                        return html;
                    }
                },
                {
                    data: 'doctor_name',
                    render: function (data) {
                        return data || '<span class="text-muted">Not assigned</span>';
                    }
                },
                {
                    data: 'test_names',
                    render: function (data, type, row) {
                        const testsCount = parseInt(row.tests_count || 0);
                        const testNames = data || '';

                        if (testsCount === 0) {
                            return '<span class="text-muted">No tests</span>';
                        } else if (testsCount === 1) {
                            return '<div class="test-info"><span class="badge badge-info">' + testsCount + ' test</span><br><small class="text-muted">' + testNames + '</small></div>';
                        } else {
                            const truncatedNames = testNames.length > 50 ? testNames.substring(0, 50) + '...' : testNames;
                            return '<div class="test-info"><span class="badge badge-success">' + testsCount + ' tests</span><br><small class="text-muted" title="' + testNames + '">' + truncatedNames + '</small></div>';
                        }
                    }
                },
                {
                    data: 'status',
                    render: function (data) {
                        const statusClass = {
                            'pending': 'warning',
                            'completed': 'success',
                            'cancelled': 'danger'
                        }[data] || 'secondary';
                        return '<span class="badge badge-' + statusClass + '">' + data + '</span>';
                    }
                },
                {
                    data: 'priority',
                    render: function (data) {
                        const priority = data || 'normal';
                        const priorityClass = {
                            'urgent': 'danger',
                            'emergency': 'warning',
                            'routine': 'info',
                            'normal': 'secondary'
                        }[priority] || 'secondary';
                        return '<span class="badge badge-' + priorityClass + '">' + priority + '</span>';
                    }
                },
                {
                    data: 'final_amount',
                    render: function (data) {
                        const amount = parseFloat(data || 0);
                        return '₹' + amount.toFixed(2);
                    }
                },
                {
                    data: 'entry_date',
                    render: function (data) {
                        if (data) {
                            const date = new Date(data);
                            return date.toLocaleDateString('en-IN');
                        }
                        return '<span class="text-muted">N/A</span>';
                    }
                },
                {
                    data: 'added_by_full_name',
                    render: function (data, type, row) {
                        return data || row.added_by_username || '<span class="text-muted">Unknown</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return '<div class="btn-group" role="group">' +
                            '<button class="btn btn-info btn-sm" onclick="viewEntry(' + row.id + ')" title="View">' +
                            '<i class="fas fa-eye"></i></button>' +
                            '<button class="btn btn-warning btn-sm" onclick="editEntry(' + row.id + ')" title="Edit">' +
                            '<i class="fas fa-edit"></i></button>' +
                            '<button class="btn btn-danger btn-sm" onclick="deleteEntry(' + row.id + ')" title="Delete">' +
                            '<i class="fas fa-trash"></i></button></div>';
                    }
                }
            ],
            order: [[7, 'desc']],
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
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to initialize data table. Please refresh the page.');
        }
    }
}

// Load combined owners and users for dropdown
function loadOwnerUsers() {
    const ownerUserSelect = $('#ownerAddedBySelect');
    if (ownerUserSelect.length === 0) return;
    
    ownerUserSelect.empty().append('<option value="">Select Owner/User</option>');

    // Load owners first, then users
    $.ajax({
        url: 'ajax/owner_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function (ownerResponse) {
            if (ownerResponse.success && ownerResponse.data && ownerResponse.data.length > 0) {
                ownerResponse.data.forEach(function (owner) {
                    ownerUserSelect.append('<option value="owner_' + owner.id + '" data-type="owner" data-owner-id="' + owner.id + '">🏢 ' + owner.name + ' (Owner)</option>');
                });
            }

            // Load users
            $.ajax({
                url: 'ajax/user_api.php',
                method: 'GET',
                data: { action: 'list_simple' },
                dataType: 'json',
                success: function (userResponse) {
                    if (userResponse.success && userResponse.data && userResponse.data.length > 0) {
                        userResponse.data.forEach(function (user) {
                            const displayName = user.full_name || user.username || user.email || 'User ' + user.id;
                            ownerUserSelect.append('<option value="user_' + user.id + '" data-type="user" data-user-id="' + user.id + '">👤 ' + displayName + ' (' + (user.role || 'user') + ')</option>');
                        });
                    }
                    ownerUserSelect.addClass('select2');
                },
                error: function () {
                    console.error('Error loading users');
                }
            });
        },
        error: function () {
            console.error('Error loading owners');
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
        success: function (response) {
            if (response.success) {
                console.log('Tests loaded successfully:', response.data.length, 'tests');
                window.testsData = response.data;
                
                const testSelects = $('.test-select');
                testSelects.each(function () {
                    const $this = $(this);
                    const currentVal = $this.val();
                    populateTestSelect($this, response.data, currentVal);
                });
            }
            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function () {
            console.error('Error loading tests');
        }
    });
}

// Helper function to populate a single test select dropdown
function populateTestSelect($testSelect, testsData, currentVal) {
    $testSelect.empty().append('<option value="">Select Test</option>');
    testsData.forEach(function (test) {
        const escapedTestName = (test.name || '').replace(/"/g, '&quot;');
        const opt = $('<option value="' + test.id + '" data-price="' + (test.price || 0) + '">' + escapedTestName + ' - ₹' + (test.price || 0) + '</option>');
        $testSelect.append(opt);
    });
    
    if (currentVal) { 
        $testSelect.val(currentVal).trigger('change'); 
    }
}

// Setup event handlers
function setupEventHandlers() {
    $('#entryForm').on('submit', function (e) {
        e.preventDefault();
        saveEntry(this);
    });

    // Test selection change handler with duplicate prevention
    $(document).on('change', '.test-select', function () {
        const $currentSelect = $(this);
        const selectedTestId = $currentSelect.val();
        const $row = $currentSelect.closest('.test-row');

        // Check for duplicate test selection
        if (selectedTestId) {
            let isDuplicate = false;
            
            $('.test-select').not($currentSelect).each(function () {
                if ($(this).val() === selectedTestId) {
                    isDuplicate = true;
                    return false;
                }
            });

            if (isDuplicate) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('This test is already selected in another row. Please choose a different test.');
                } else {
                    alert('This test is already selected in another row. Please choose a different test.');
                }
                
                $currentSelect.val('').trigger('change');
                return;
            }
        }

        // Auto-fill test information
        if (selectedTestId) {
            const $opt = $currentSelect.find('option:selected');
            const price = parseFloat($opt.data('price') || 0);
            
            if ($row.find('.test-price').length) {
                $row.find('.test-price').val(price.toFixed(2));
                $row.find('.test-discount').val('0.00');
                $row.find('.test-total').val(price.toFixed(2));
            }
        }
        
        calculateTotals();
    });
}

// Add new test row
function addTestRow() {
    const container = $('#testsContainer');
    const currentRows = container.find('.test-row').length;
    const newIndex = currentRows;
    
    const newRow = $('<div class="test-row row mb-2">' +
        '<div class="col-md-3">' +
        '<select class="form-control test-select select2" name="tests[' + newIndex + '][test_id]" required>' +
        '<option value="">Select Test</option>' +
        '</select></div>' +
        '<div class="col-md-2">' +
        '<input type="text" class="form-control test-category" name="tests[' + newIndex + '][category_name]" placeholder="Category" readonly>' +
        '</div>' +
        '<div class="col-md-2">' +
        '<input type="text" class="form-control test-result" name="tests[' + newIndex + '][result_value]" placeholder="Result">' +
        '</div>' +
        '<div class="col-md-1">' +
        '<input type="text" class="form-control test-min" name="tests[' + newIndex + '][min]" placeholder="Min" readonly>' +
        '</div>' +
        '<div class="col-md-1">' +
        '<input type="text" class="form-control test-max" name="tests[' + newIndex + '][max]" placeholder="Max" readonly>' +
        '</div>' +
        '<div class="col-md-2">' +
        '<input type="text" class="form-control test-unit" name="tests[' + newIndex + '][unit]" placeholder="Unit" readonly>' +
        '</div>' +
        '<div class="col-md-1">' +
        '<button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)" title="Remove Test">' +
        '<i class="fas fa-trash"></i></button></div></div>');
    
    container.append(newRow);
    
    // Load tests for the new row
    if (window.testsData) {
        populateTestSelect(newRow.find('.test-select'), window.testsData);
    }
    
    console.log('Added new test row with index:', newIndex);
}

// Remove test row
function removeTestRow(button) {
    const $row = $(button).closest('.test-row');
    const container = $('#testsContainer');
    
    if (container.find('.test-row').length <= 1) {
        if (typeof toastr !== 'undefined') {
            toastr.warning('At least one test row is required.');
        } else {
            alert('At least one test row is required.');
        }
        return;
    }
    
    $row.remove();
    calculateTotals();
    console.log('Removed test row');
}

// Calculate totals
function calculateTotals() {
    let subtotal = 0;
    let totalDiscount = 0;
    
    $('.test-row').each(function() {
        const $row = $(this);
        const price = parseFloat($row.find('.test-price').val() || 0);
        const discount = parseFloat($row.find('.test-discount').val() || 0);
        
        subtotal += price;
        totalDiscount += discount;
        
        const rowTotal = price - discount;
        $row.find('.test-total').val(rowTotal.toFixed(2));
    });
    
    const finalAmount = subtotal - totalDiscount;
    
    $('#subtotal').val(subtotal.toFixed(2));
    $('#discountAmount').val(totalDiscount.toFixed(2));
    $('#totalPrice').val(finalAmount.toFixed(2));
}

// View entry details
function viewEntry(id) {
    currentEntryId = id;
    
    // Load entry data
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateEntryForm(response.data, true); // true = view mode
                $('#entryModalLabel').html('<i class="fas fa-eye mr-1"></i>View Entry #' + id);
                $('#entryModal').modal('show');
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message || 'Failed to load entry details');
                } else {
                    alert(response.message || 'Failed to load entry details');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading entry:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to load entry details');
            } else {
                alert('Failed to load entry details');
            }
        }
    });
}

// Edit entry
function editEntry(id) {
    currentEntryId = id;
    
    // Load entry data
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateEntryForm(response.data, false); // false = edit mode
                $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry #' + id);
                $('#entryModal').modal('show');
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message || 'Failed to load entry details');
                } else {
                    alert(response.message || 'Failed to load entry details');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading entry:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to load entry details');
            } else {
                alert('Failed to load entry details');
            }
        }
    });
}

// Delete entry
function deleteEntry(id) {
    if (confirm('Are you sure you want to delete this entry? This action cannot be undone.')) {
        performDelete(id);
    }
}

// Perform delete operation
function performDelete(id) {
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'POST',
        data: { action: 'delete', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (typeof toastr !== 'undefined') {
                    toastr.success('Entry deleted successfully');
                } else {
                    alert('Entry deleted successfully');
                }
                
                if (entriesTable) {
                    entriesTable.ajax.reload();
                }
                loadStatistics();
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message || 'Failed to delete entry');
                } else {
                    alert(response.message || 'Failed to delete entry');
                }
            }
        },
        error: function() {
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to delete entry');
            } else {
                alert('Failed to delete entry');
            }
        }
    });
}

// Save entry
function saveEntry(form) {
    console.log('Saving entry...');
    
    const formData = new FormData(form);
    formData.append('action', 'save');
    
    const tests = [];
    $('.test-row').each(function() {
        const $row = $(this);
        const testId = $row.find('.test-select').val();
        
        if (testId) {
            tests.push({
                test_id: testId,
                result_value: $row.find('.test-result').val() || '',
                price: parseFloat($row.find('.test-price').val() || 0),
                discount_amount: parseFloat($row.find('.test-discount').val() || 0),
                status: 'pending'
            });
        }
    });
    
    formData.append('tests', JSON.stringify(tests));
    
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message || 'Entry saved successfully');
                } else {
                    alert(response.message || 'Entry saved successfully');
                }
                
                $('#entryModal').modal('hide');
                
                if (entriesTable) {
                    entriesTable.ajax.reload();
                }
                loadStatistics();
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message || 'Failed to save entry');
                } else {
                    alert(response.message || 'Failed to save entry');
                }
            }
        },
        error: function() {
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to save entry');
            } else {
                alert('Failed to save entry');
            }
        }
    });
}

// Clean up duplicate test entries
function cleanupDuplicates() {
    if (confirm('This will remove duplicate test entries from the database. Are you sure you want to continue?')) {
        $.ajax({
            url: 'ajax/entry_api_fixed.php',
            method: 'POST',
            data: { action: 'cleanup_duplicates' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    
                    if (entriesTable) {
                        entriesTable.ajax.reload();
                    }
                    loadStatistics();
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Failed to clean duplicates');
                    } else {
                        alert(response.message || 'Failed to clean duplicates');
                    }
                }
            },
            error: function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to clean duplicates');
                } else {
                    alert('Failed to clean duplicates');
                }
            }
        });
    }
}

// Open add entry modal
function openAddEntryModal() {
    $('#entryModal').modal('show');
}

// Refresh table
function refreshTable() {
    if (entriesTable) {
        entriesTable.ajax.reload();
    }
    loadStatistics();
}

// Export entries (placeholder)
function exportEntries() {
    if (typeof toastr !== 'undefined') {
        toastr.info('Export functionality coming soon');
    } else {
        alert('Export functionality coming soon');
    }
}

// Apply filters (placeholder)
function applyFilters() {
    console.log('Applying filters...');
}

// Filter by date (placeholder)
function filterByDate(period) {
    console.log('Filtering by date:', period);
}

// Filter by status (placeholder)
function filterByStatus(status) {
    console.log('Filtering by status:', status);
}

// Populate entry form with data
function populateEntryForm(data, viewMode = false) {
    console.log('Populating form with data:', data);
    
    // Set form fields
    $('#entryId').val(data.id || '');
    $('#entryDate').val(data.entry_date || '');
    $('#entryStatus').val(data.status || 'pending').trigger('change');
    
    // Set patient information
    $('#patientName').val(data.patient_name || '');
    $('#patientContact').val(data.patient_contact || '');
    $('#patientAge').val(data.age || '');
    $('#patientGender').val(data.gender || '').trigger('change');
    $('#patientAddress').val(data.patient_address || '');
    
    // Set owner/user selection if available
    if (data.added_by) {
        $('#ownerAddedBySelect').val('user_' + data.added_by).trigger('change');
    }
    
    // Set patient selection if available
    if (data.patient_id) {
        // Add the current patient as an option if not already present
        const patientSelect = $('#patientSelect');
        if (patientSelect.find('option[value="' + data.patient_id + '"]').length === 0) {
            patientSelect.append('<option value="' + data.patient_id + '">' + (data.patient_name || 'Patient #' + data.patient_id) + '</option>');
        }
        patientSelect.val(data.patient_id).trigger('change');
    }
    
    // Set doctor selection if available
    if (data.doctor_id) {
        const doctorSelect = $('#doctorSelect');
        if (doctorSelect.find('option[value="' + data.doctor_id + '"]').length === 0) {
            doctorSelect.append('<option value="' + data.doctor_id + '">Dr. ' + (data.doctor_name || 'Doctor #' + data.doctor_id) + '</option>');
        }
        doctorSelect.val(data.doctor_id).trigger('change');
    }
    
    // Clear existing test rows
    $('#testsContainer').empty();
    
    // Add test rows
    if (data.tests && data.tests.length > 0) {
        data.tests.forEach(function(test, index) {
            const newRow = $('<div class="test-row row mb-2">' +
                '<div class="col-md-3">' +
                '<select class="form-control test-select select2" name="tests[' + index + '][test_id]" required>' +
                '<option value="">Select Test</option>' +
                '</select></div>' +
                '<div class="col-md-2">' +
                '<input type="text" class="form-control test-category" name="tests[' + index + '][category_name]" placeholder="Category" readonly value="' + (test.category_name || '') + '">' +
                '</div>' +
                '<div class="col-md-2">' +
                '<input type="text" class="form-control test-result" name="tests[' + index + '][result_value]" placeholder="Result" value="' + (test.result_value || '') + '">' +
                '</div>' +
                '<div class="col-md-1">' +
                '<input type="text" class="form-control test-min" name="tests[' + index + '][min]" placeholder="Min" readonly value="' + (test.min || '') + '">' +
                '</div>' +
                '<div class="col-md-1">' +
                '<input type="text" class="form-control test-max" name="tests[' + index + '][max]" placeholder="Max" readonly value="' + (test.max || '') + '">' +
                '</div>' +
                '<div class="col-md-2">' +
                '<input type="text" class="form-control test-unit" name="tests[' + index + '][unit]" placeholder="Unit" readonly value="' + (test.unit || '') + '">' +
                '</div>' +
                '<div class="col-md-1">' +
                '<button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)" title="Remove Test">' +
                '<i class="fas fa-trash"></i></button></div></div>');
            
            $('#testsContainer').append(newRow);
            
            // Populate test select with current test
            const testSelect = newRow.find('.test-select');
            if (window.testsData) {
                populateTestSelect(testSelect, window.testsData);
            }
            
            // Set the test selection
            if (test.test_id) {
                // Add the current test as an option if not already present
                if (testSelect.find('option[value="' + test.test_id + '"]').length === 0) {
                    testSelect.append('<option value="' + test.test_id + '">' + (test.test_name || 'Test #' + test.test_id) + '</option>');
                }
                testSelect.val(test.test_id).trigger('change');
            }
        });
    } else {
        // Add at least one empty row
        addTestRow();
    }
    
    // Set form to view mode if requested
    if (viewMode) {
        $('#entryForm input, #entryForm select, #entryForm textarea').prop('disabled', true);
        $('#entryForm button[type="submit"]').hide();
        $('.btn-danger').hide(); // Hide remove buttons
        $('#entryForm .btn-success').hide(); // Hide add test button
    } else {
        $('#entryForm input, #entryForm select, #entryForm textarea').prop('disabled', false);
        $('#entryForm button[type="submit"]').show();
        $('.btn-danger').show(); // Show remove buttons
        $('#entryForm .btn-success').show(); // Show add test button
    }
}

// Reset entry form
function resetEntryForm() {
    currentEntryId = null;
    $('#entryForm')[0].reset();
    $('#testsContainer').empty();
    addTestRow(); // Add one empty test row
    $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');
    
    // Re-enable all form elements
    $('#entryForm input, #entryForm select, #entryForm textarea').prop('disabled', false);
    $('#entryForm button[type="submit"]').show();
    $('.btn-danger').show();
    $('#entryForm .btn-success').show();
}

// Show add entry modal
function showAddEntryModal() {
    resetEntryForm();
    $('#entryModal').modal('show');
}