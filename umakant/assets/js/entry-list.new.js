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
                        console.log('DataTable received response:', response);
                        if (response.data && response.data.length > 0) {
                            console.log('First entry data:', response.data[0]);
                            // Log entries with multiple tests
                            response.data.forEach(function(entry, index) {
                                if (entry.tests_count > 1) {
                                    console.log(`Entry ${entry.id} has ${entry.tests_count} tests: "${entry.test_names}"`);
                                }
                            });
                        }
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
                        
                        // Debug logging for this specific row
                        if (testsCount > 1) {
                            console.log(`Rendering tests for entry ${row.id}: count=${testsCount}, names="${testNames}"`);
                        }

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

// Helper function to populate a single test select dropdown - SIMPLIFIED
function populateTestSelect($testSelect, testsData, currentVal) {
    $testSelect.empty().append('<option value="">Select Test</option>');
    
    testsData.forEach(function (test) {
        const testName = test.name || 'Test #' + test.id;
        const testPrice = test.price || 0;
        
        const $option = $('<option></option>')
            .attr('value', test.id)
            .text(testName + ' - ₹' + testPrice)
            .data('price', testPrice)
            .data('category', test.category_name || '')
            .data('category-id', test.category_id || '')
            .data('unit', test.unit || '')
            .data('min', test.min || '')
            .data('max', test.max || '')
            .data('min-male', test.min_male || '')
            .data('max-male', test.max_male || '')
            .data('min-female', test.min_female || '')
            .data('max-female', test.max_female || '')
            .data('reference-range', test.reference_range || '');
        
        $testSelect.append($option);
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

    // Test selection change handler - allows multiple instances of same test
    $(document).on('change', '.test-select', function () {
        const $currentSelect = $(this);
        const selectedTestId = $currentSelect.val();
        const $row = $currentSelect.closest('.test-row');

        console.log('Test selection changed to:', selectedTestId);

        // Auto-fill test information (no duplicate prevention - users can select same test multiple times)
        if (selectedTestId) {
            const $opt = $currentSelect.find('option:selected');
            const price = parseFloat($opt.data('price') || 0);
            const category = $opt.data('category') || '';
            const categoryId = $opt.data('category-id') || '';
            const unit = $opt.data('unit') || '';
            const min = $opt.data('min') || '';
            const max = $opt.data('max') || '';
            const minMale = $opt.data('min-male') || '';
            const maxMale = $opt.data('max-male') || '';
            const minFemale = $opt.data('min-female') || '';
            const maxFemale = $opt.data('max-female') || '';
            const referenceRange = $opt.data('reference-range') || '';
            
            console.log('Selected option data:', {
                price: price,
                category: category,
                categoryId: categoryId,
                unit: unit,
                min: min,
                max: max
            });
            
            // Set test information fields
            $row.find('.test-category').val(category);
            $row.find('.test-category-id').val(categoryId);
            $row.find('.test-unit').val(unit);
            $row.find('.test-min').val(min);
            $row.find('.test-max').val(max);
            
            // Set price information
            $row.find('.test-price').val(price);
            $row.find('.test-discount').val(0);
            $row.find('.test-total').val(price);
            
            console.log('Auto-filled test data for test ID:', selectedTestId, {
                category: category,
                unit: unit,
                min: min,
                max: max,
                price: price
            });
        } else {
            // Clear all test information
            $row.find('.test-category').val('');
            $row.find('.test-category-id').val('');
            $row.find('.test-unit').val('');
            $row.find('.test-min').val('');
            $row.find('.test-max').val('');
            $row.find('.test-price').val(0);
            $row.find('.test-discount').val(0);
            $row.find('.test-total').val(0);
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
        '<input type="hidden" name="tests[' + newIndex + '][category_id]" class="test-category-id">' +
        '<input type="hidden" class="test-price" name="tests[' + newIndex + '][price]" value="0">' +
        '<input type="hidden" class="test-discount" name="tests[' + newIndex + '][discount_amount]" value="0">' +
        '<input type="hidden" class="test-total" name="tests[' + newIndex + '][total_price]" value="0">' +
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
        populateTestSelect(newRow.find('.test-select'), window.testsData, null);
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
    
    // Ensure tests are loaded first, then load entry data
    if (!window.testsData) {
        console.log('Loading tests data before viewing entry...');
        loadTests(function() {
            loadAndViewEntry(id);
        });
    } else {
        loadAndViewEntry(id);
    }
}

// Helper function to load and view entry
function loadAndViewEntry(id) {
    // Load entry data
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log('Loaded entry data for viewing:', response.data);
                console.log('Entry tests data for viewing:', response.data.tests);
                if (response.data.tests && response.data.tests.length > 0) {
                    response.data.tests.forEach(function(test, index) {
                        console.log(`View Test ${index + 1}:`, {
                            test_id: test.test_id,
                            test_name: test.test_name,
                            price: test.price,
                            result_value: test.result_value
                        });
                    });
                }
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
    
    // Ensure tests are loaded first, then load entry data
    if (!window.testsData) {
        console.log('Loading tests data before editing entry...');
        loadTests(function() {
            loadAndEditEntry(id);
        });
    } else {
        loadAndEditEntry(id);
    }
}

// Helper function to load and edit entry
function loadAndEditEntry(id) {
    // Load entry data
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log('Loaded entry data for editing:', response.data);
                console.log('Entry tests data:', response.data.tests);
                if (response.data.tests && response.data.tests.length > 0) {
                    response.data.tests.forEach(function(test, index) {
                        console.log(`Test ${index + 1}:`, {
                            test_id: test.test_id,
                            test_name: test.test_name,
                            price: test.price,
                            result_value: test.result_value
                        });
                    });
                }
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
    
    // Validate required fields
    const patientId = $('#patientSelect').val();
    const ownerAddedBy = $('#ownerAddedBySelect').val();
    
    if (!ownerAddedBy) {
        if (typeof toastr !== 'undefined') {
            toastr.error('Please select an Owner/User');
        } else {
            alert('Please select an Owner/User');
        }
        return;
    }
    
    if (!patientId) {
        if (typeof toastr !== 'undefined') {
            toastr.error('Please select a patient');
        } else {
            alert('Please select a patient');
        }
        return;
    }
    
    // Check if at least one test is selected
    let hasTests = false;
    $('.test-row').each(function() {
        if ($(this).find('.test-select').val()) {
            hasTests = true;
            return false;
        }
    });
    
    if (!hasTests) {
        if (typeof toastr !== 'undefined') {
            toastr.error('Please select at least one test');
        } else {
            alert('Please select at least one test');
        }
        return;
    }
    
    const formData = new FormData(form);
    formData.append('action', 'save');
    
    // Extract owner/user information
    if (ownerAddedBy.startsWith('user_')) {
        formData.append('added_by', ownerAddedBy.replace('user_', ''));
    } else if (ownerAddedBy.startsWith('owner_')) {
        formData.append('owner_id', ownerAddedBy.replace('owner_', ''));
    }
    
    const tests = [];
    $('.test-row').each(function() {
        const $row = $(this);
        const testId = $row.find('.test-select').val();
        
        if (testId) {
            tests.push({
                test_id: testId,
                result_value: $row.find('.test-result').val() || '',
                unit: $row.find('.test-unit').val() || '',
                price: parseFloat($row.find('.test-price').val() || 0),
                discount_amount: parseFloat($row.find('.test-discount').val() || 0),
                status: 'pending'
            });
        }
    });
    
    formData.append('tests', JSON.stringify(tests));
    
    console.log('Submitting form with tests:', tests);
    
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
                resetEntryForm();
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response.message || 'Failed to save entry');
                } else {
                    alert(response.message || 'Failed to save entry');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Save error:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to save entry: ' + error);
            } else {
                alert('Failed to save entry: ' + error);
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
    showAddEntryModal();
}

// Refresh table
function refreshTable() {
    console.log('Refreshing entries table...');
    if (entriesTable) {
        entriesTable.ajax.reload(null, false); // false = don't reset paging
        console.log('Table refreshed successfully');
    } else {
        console.error('Entries table not initialized');
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

// Populate entry form with data - COMPLETELY REWRITTEN
function populateEntryForm(data, viewMode = false) {
    console.log('=== FRESH POPULATE ENTRY FORM ===');
    console.log('Entry data received:', data);
    
    // Set basic form fields
    $('#entryId').val(data.id || '');
    $('#entryDate').val(data.entry_date || '');
    $('#entryStatus').val(data.status || 'pending').trigger('change');
    
    // Set patient information
    $('#patientName').val(data.patient_name || '');
    $('#patientContact').val(data.patient_contact || '');
    $('#patientAge').val(data.age || '');
    $('#patientGender').val(data.gender || '').trigger('change');
    $('#patientAddress').val(data.patient_address || '');
    
    // Set pricing information
    $('#subtotal').val(data.subtotal || 0);
    $('#discountAmount').val(data.total_discount || data.discount_amount || 0);
    $('#totalPrice').val(data.final_amount || data.total_price || 0);
    
    // Set additional fields
    $('#referralSource').val(data.referral_source || '').trigger('change');
    $('#priority').val(data.priority || 'normal').trigger('change');
    $('#entryNotes').val(data.notes || '');
    
    // Set owner/user selection
    if (data.added_by) {
        $('#ownerAddedBySelect').val('user_' + data.added_by).trigger('change');
    }
    
    // Set patient selection
    if (data.patient_id) {
        const patientSelect = $('#patientSelect');
        if (patientSelect.find('option[value="' + data.patient_id + '"]').length === 0) {
            patientSelect.append('<option value="' + data.patient_id + '">' + (data.patient_name || 'Patient #' + data.patient_id) + '</option>');
        }
        patientSelect.val(data.patient_id).trigger('change');
    }
    
    // Set doctor selection
    if (data.doctor_id) {
        const doctorSelect = $('#doctorSelect');
        if (doctorSelect.find('option[value="' + data.doctor_id + '"]').length === 0) {
            doctorSelect.append('<option value="' + data.doctor_id + '">Dr. ' + (data.doctor_name || 'Doctor #' + data.doctor_id) + '</option>');
        }
        doctorSelect.val(data.doctor_id).trigger('change');
    }
    
    // FRESH TEST HANDLING - Clear and rebuild
    $('#testsContainer').empty();
    
    if (data.tests && data.tests.length > 0) {
        console.log('Processing ' + data.tests.length + ' tests...');
        
        data.tests.forEach(function(testData, index) {
            console.log('=== Processing Test ' + (index + 1) + ' ===');
            console.log('Test ID:', testData.test_id);
            console.log('Test Name:', testData.test_name);
            console.log('Test Price:', testData.price);
            console.log('Test Category:', testData.category_name);
            
            // Create fresh test row HTML
            const testRowHtml = 
                '<div class="test-row row mb-2">' +
                    '<div class="col-md-3">' +
                        '<select class="form-control test-select" name="tests[' + index + '][test_id]" required>' +
                        '</select>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                        '<input type="text" class="form-control test-category" name="tests[' + index + '][category_name]" placeholder="Category" readonly>' +
                        '<input type="hidden" name="tests[' + index + '][category_id]" class="test-category-id">' +
                        '<input type="hidden" class="test-price" name="tests[' + index + '][price]">' +
                        '<input type="hidden" class="test-discount" name="tests[' + index + '][discount_amount]">' +
                        '<input type="hidden" class="test-total" name="tests[' + index + '][total_price]">' +
                    '</div>' +
                    '<div class="col-md-2">' +
                        '<input type="text" class="form-control test-result" name="tests[' + index + '][result_value]" placeholder="Result">' +
                    '</div>' +
                    '<div class="col-md-1">' +
                        '<input type="text" class="form-control test-min" name="tests[' + index + '][min]" placeholder="Min" readonly>' +
                    '</div>' +
                    '<div class="col-md-1">' +
                        '<input type="text" class="form-control test-max" name="tests[' + index + '][max]" placeholder="Max" readonly>' +
                    '</div>' +
                    '<div class="col-md-2">' +
                        '<input type="text" class="form-control test-unit" name="tests[' + index + '][unit]" placeholder="Unit" readonly>' +
                    '</div>' +
                    '<div class="col-md-1">' +
                        '<button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)" title="Remove Test">' +
                            '<i class="fas fa-trash"></i>' +
                        '</button>' +
                    '</div>' +
                '</div>';
            
            // Add the row to container
            const $newRow = $(testRowHtml);
            $('#testsContainer').append($newRow);
            
            // Get the select element for this row
            const $testSelect = $newRow.find('.test-select');
            
            // FRESH APPROACH: Create the option directly from test data
            const testName = testData.test_name || 'Unknown Test';
            const testPrice = testData.price || 0;
            const testId = testData.test_id;
            
            // Add the "Select Test" default option
            $testSelect.append('<option value="">Select Test</option>');
            
            // Add the current test as the selected option
            const optionText = testName + ' - ₹' + testPrice;
            const $currentOption = $('<option></option>')
                .attr('value', testId)
                .attr('selected', 'selected')
                .text(optionText)
                .data('price', testPrice)
                .data('category', testData.category_name || '')
                .data('category-id', testData.category_id || '')
                .data('unit', testData.unit || '')
                .data('min', testData.min || '')
                .data('max', testData.max || '');
            
            $testSelect.append($currentOption);
            
            // Set the select value
            $testSelect.val(testId);
            
            // Fill in the other fields
            $newRow.find('.test-category').val(testData.category_name || '');
            $newRow.find('.test-category-id').val(testData.category_id || '');
            $newRow.find('.test-result').val(testData.result_value || '');
            $newRow.find('.test-min').val(testData.min || '');
            $newRow.find('.test-max').val(testData.max || '');
            $newRow.find('.test-unit').val(testData.unit || '');
            $newRow.find('.test-price').val(testPrice);
            $newRow.find('.test-discount').val(testData.discount_amount || 0);
            $newRow.find('.test-total').val(testData.total_price || testPrice);
            
            console.log('✅ Test ' + (index + 1) + ' populated successfully');
            console.log('Selected value:', $testSelect.val());
            console.log('Option text:', $testSelect.find('option:selected').text());
        });
        
        console.log('=== All tests processed successfully ===');
    } else {
        // Add one empty row if no tests
        addTestRow();
    }
    
    // Set form mode
    if (viewMode) {
        $('#entryForm input, #entryForm select, #entryForm textarea').prop('disabled', true);
        $('#entryForm button[type="submit"]').hide();
        $('.btn-danger').hide();
        $('#entryForm .btn-success').hide();
    } else {
        $('#entryForm input, #entryForm select, #entryForm textarea').prop('disabled', false);
        $('#entryForm button[type="submit"]').show();
        $('.btn-danger').show();
        $('#entryForm .btn-success').show();
    }
    
    // Recalculate totals
    setTimeout(calculateTotals, 100);
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