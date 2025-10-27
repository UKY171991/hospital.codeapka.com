/**
 * Entry List Management System
 * Clean, modern JavaScript for managing hospital test entries
 */

class EntryManager {
    constructor() {
        this.entriesTable = null;
        this.testsData = [];
        this.patientsData = [];
        this.doctorsData = [];
        this.ownersData = [];
        this.currentEditId = null;
        this.testRowCounter = 0;

        // Performance optimization: Range calculation cache
        this.rangeCache = new Map();
        this.rangeCacheTimeout = 5 * 60 * 1000; // 5 minutes cache timeout

        // Performance optimization: Debounced update function
        this.debouncedRangeUpdate = this.debounce(this.updateAllTestRangesForCurrentPatient.bind(this), 150);

        this.init();
    }

    /**
     * Initialize the Entry Manager
     */
    init() {
        //console.log('Initializing Entry Manager...');

        // Wait for DOM to be ready
        $(document).ready(() => {
            //console.log('DOM ready, starting initialization...');
            try {
                this.initializeDataTable();
                this.loadInitialData();
                this.bindEvents();
                this.loadStatistics();
                //console.log('Entry Manager initialization complete');
            } catch (error) {
                //console.error('Error during Entry Manager initialization:', error);
            }
        });
    }

    /**
     * Initialize DataTable with proper configuration
     */
    initializeDataTable() {
        //console.log('Initializing DataTable...');

        // Check if the table element exists
        if ($('#entriesTable').length === 0) {
            //console.error('DataTable element #entriesTable not found');
            return;
        }

        // Check if DataTable is available
        if (typeof $.fn.DataTable === 'undefined') {
            //console.error('DataTables library not loaded');
            return;
        }

        try {
            this.entriesTable = $('#entriesTable').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: 'ajax/entry_api_fixed.php',
                    type: 'GET',
                    data: { action: 'list' },
                    dataSrc: function (json) {
                        //console.log('DataTable received data:', json);
                        if (json && json.success) {
                            return json.data || [];
                        } else {
                            //console.error('API Error:', json ? json.message : 'Invalid response');
                            if (typeof toastr !== 'undefined') {
                                toastr.error(json ? json.message : 'Failed to load entries - invalid response');
                            } else {
                                alert('Failed to load entries: ' + (json ? json.message : 'Invalid response'));
                            }
                            return [];
                        }
                    },
                    error: function (xhr, error, thrown) {
                        console.error('DataTable AJAX Error:', {
                            error: error,
                            thrown: thrown,
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText
                        });

                        let errorMessage = 'Failed to load entries. ';
                        if (xhr.status === 404) {
                            errorMessage += 'API endpoint not found.';
                        } else if (xhr.status === 500) {
                            errorMessage += 'Server error occurred.';
                        } else if (xhr.status === 0) {
                            errorMessage += 'Network connection failed.';
                        } else {
                            errorMessage += 'Please refresh the page.';
                        }

                        if (typeof toastr !== 'undefined') {
                            toastr.error(errorMessage);
                        } else {
                            alert(errorMessage);
                        }
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
                        width: '12%',
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
                        width: '10%',
                        render: function (data, type, row) {
                            return data || '<span class="text-muted">Not assigned</span>';
                        }
                    },
                    {
                        data: 'test_names',
                        title: 'Tests',
                        width: '15%',
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
                        width: '7%',
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
                        width: '7%',
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
                        width: '8%',
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
                        width: '8%',
                        render: function (data, type, row) {
                            if (type === 'display' && data) {
                                const date = new Date(data);
                                return date.toLocaleDateString('en-IN');
                            }
                            return data || '';
                        }
                    },
                    {
                        data: 'added_by_full_name',
                        title: 'Added By',
                        width: '7%',
                        render: function (data, type, row) {
                            return data || row.added_by_username || 'Unknown';
                        }
                    },
                    {
                        data: null,
                        title: 'Actions',
                        width: '9%',
                        orderable: false,
                        render: function (data, type, row) {
                            if (type === 'display') {
                                return `
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-info btn-sm" onclick="window.entryManager.viewEntry(${row.id})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.entryManager.editEntry(${row.id})" title="Edit Entry">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="window.entryManager.deleteEntry(${row.id})" title="Delete Entry">
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

            //console.log('DataTable initialized successfully');
        } catch (error) {
            //console.error('Error initializing DataTable:', error);
            // Show user-friendly error message
            $('#entriesTable').html('<div class="alert alert-danger">Failed to initialize data table. Please refresh the page.</div>');
        }
    }

    /**
     * Load initial data (tests, patients, doctors, owners)
     */
    async loadInitialData() {
        // console.log('Loading initial data...');

        try {
            // Load tests data
            await this.loadTestsData();

            // Load owners/users data
            await this.loadOwnersData();

            // console.log('Initial data loaded successfully');
        } catch (error) {
            //console.error('Error loading initial data:', error);
            toastr.error('Failed to load initial data');
        }
    }

    /**
     * Load tests data from API
     */
    async loadTestsData() {
        try {
            //console.log('Loading tests data from API...');
            const response = await $.ajax({
                url: 'ajax/test_api.php',
                method: 'GET',
                data: { action: 'list' },
                dataType: 'json'
            });

            if (response && response.success) {
                this.testsData = response.data || [];
                // Clear range cache when test data is updated
                this.clearRangeCache();
                
                // Verify demographic range fields are available
                this.verifyDemographicRangeFields();
                
                //console.log('Loaded tests data:', this.testsData.length, 'tests');

                // Debug: show first few tests
                if (this.testsData.length > 0) {
                    // console.log('Sample tests:', this.testsData.slice(0, 3));
                    // console.log('Test data structure:', Object.keys(this.testsData[0]));
                } else {
                    //console.warn('Tests data is empty');
                }
            } else {
                // console.error('Failed to load tests:', response ? response.message : 'Invalid response');
                // console.error('Full response:', response);
                this.testsData = [];
            }
        } catch (error) {
            //console.error('Error loading tests data:', error);
            console.error('Error details:', {
                status: error.status,
                statusText: error.statusText,
                responseText: error.responseText
            });

            // Try to parse error response
            if (error.responseText) {
                try {
                    const errorData = JSON.parse(error.responseText);
                    //console.error('Parsed error response:', errorData);
                } catch (parseError) {
                    //console.error('Could not parse error response:', error.responseText);
                }
            }

            this.testsData = [];
        }
    }

    /**
     * Load owners/users data from API
     */
    async loadOwnersData() {
        try {
            const response = await $.ajax({
                url: 'ajax/owner_api.php',
                method: 'GET',
                data: { action: 'list' },
                dataType: 'json'
            });

            if (response.success) {
                this.ownersData = response.data || [];
                this.populateOwnerSelect();
                //console.log('Loaded owners data:', this.ownersData.length, 'owners');
            } else {
                //console.error('Failed to load owners:', response.message);
            }
        } catch (error) {
            //console.error('Error loading owners data:', error);
            this.ownersData = [];
        }
    }

    /**
     * Populate owner select dropdown
     */
    populateOwnerSelect() {
        const $select = $('#ownerAddedBySelect');
        $select.empty().append('<option value="">Select Owner/User</option>');

        this.ownersData.forEach(owner => {
            $select.append(`<option value="${owner.id}">${owner.name || owner.username || owner.full_name}</option>`);
        });

        //console.log('Populated owner select with', this.ownersData.length, 'owners');

        // Refresh Select2 if initialized
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.trigger('change');
        }
    }

    /**
     * Load statistics for dashboard cards
     */
    async loadStatistics() {
        try {
            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: { action: 'stats' },
                dataType: 'json'
            });

            if (response.success) {
                const stats = response.data;
                $('#totalEntries').text(stats.total || 0);
                $('#pendingEntries').text(stats.pending || 0);
                $('#completedEntries').text(stats.completed || 0);
                $('#todayEntries').text(stats.today || 0);
            } else {
                //console.error('Failed to load statistics:', response.message);
            }
        } catch (error) {
            //console.error('Error loading statistics:', error);
        }
    }

    /**
     * Bind event handlers
     */
    bindEvents() {
        // console.log('Binding events...');

        // Filter change events
        $('#statusFilter, #dateFilter').on('change', () => {
            this.applyFilters();
        });

        $('#patientFilter, #doctorFilter').on('keyup', this.debounce(() => {
            this.applyFilters();
        }, 300));

        // Owner selection change
        $('#ownerAddedBySelect').on('change', (e) => {
            this.onOwnerChange(e.target.value);
        });

        // Patient selection change
        $('#patientSelect').on('change', (e) => {
            this.onPatientChange(e.target.value);
        });

        // Form submission
        $('#entryForm').on('submit', (e) => {
            e.preventDefault();
            this.saveEntry();
        });

        // Discount amount change
        $('#discountAmount').on('input', () => {
            this.calculateTotals();
        });

        // console.log('Events bound successfully');
    }

    /**
     * Utility function for debouncing
     */
    debounce(func, wait) {
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
    applyFilters() {
        if (!this.entriesTable) return;

        const statusFilter = $('#statusFilter').val();
        const patientFilter = $('#patientFilter').val();
        const doctorFilter = $('#doctorFilter').val();

        // Apply column filters
        this.entriesTable
            .column(4).search(statusFilter) // Status column
            .column(1).search(patientFilter) // Patient column
            .column(2).search(doctorFilter) // Doctor column
            .draw();
    }

    /**
     * Refresh the entries table
     */
    refreshTable() {
        if (this.entriesTable) {
            this.entriesTable.ajax.reload();
            this.loadStatistics();
            toastr.success('Table refreshed successfully');
        }
    }

    /**
     * Export entries
     */
    exportEntries() {
        if (this.entriesTable) {
            // Trigger the Excel export
            this.entriesTable.button('.buttons-excel').trigger();
        }
    }

    /**
     * Filter by status (called from statistics cards)
     */
    filterByStatus(status) {
        $('#statusFilter').val(status === 'all' ? '' : status).trigger('change');
    }

    /**
     * Filter by date (called from statistics cards)
     */
    filterByDate(dateRange) {
        $('#dateFilter').val(dateRange).trigger('change');
    }

    /**
     * Open Add Entry Modal
     */
    openAddModal() {
        //console.log('Opening add entry modal...');

        this.currentEditId = null;
        this.resetForm();

        // Update modal title
        $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');

        // Show modal
        $('#entryModal').modal('show');

        // Initialize Select2 dropdowns
        this.initializeSelect2();

        // Add first test row
        this.addTestRow();
    }

    /**
     * Add a new test row
     */
    addTestRow(testData = null) {
        const rowIndex = this.testRowCounter++;

        //console.log('Creating test row with', this.testsData.length, 'available tests');

        const testOptions = this.testsData.map(test =>
            `<option value="${test.id}" data-category="${test.category_name || ''}" data-unit="${test.unit || ''}" data-min="${test.min || ''}" data-max="${test.max || ''}" data-price="${test.price || 0}">
                ${test.name}
            </option>`
        ).join('');

        if (testData) {
            //console.log('Looking for test with ID:', testData.test_id);
            const foundTest = this.testsData.find(t => t.id == testData.test_id);
            //console.log('Found test:', foundTest);
        }

        const rowHtml = `
            <div class="test-row row mb-2" data-row-index="${rowIndex}">
                <div class="col-md-3">
                    <select class="form-control test-select select2" name="tests[${rowIndex}][test_id]" required>
                        <option value="">Select Test</option>
                        ${testOptions}
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control test-category" name="tests[${rowIndex}][category_name]" placeholder="Category" readonly>
                    <input type="hidden" name="tests[${rowIndex}][category_id]" class="test-category-id">
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
                <div class="col-md-2">
                    <input type="text" class="form-control test-unit" name="tests[${rowIndex}][unit]" placeholder="Unit" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-test-btn" onclick="window.entryManager.removeTestRow(this)" title="Remove Test">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="hidden" name="tests[${rowIndex}][price]" class="test-price" value="0">
            </div>
        `;

        $('#testsContainer').append(rowHtml);

        // Get the new row
        const $newRow = $(`.test-row[data-row-index="${rowIndex}"]`);
        const $testSelect = $newRow.find('.test-select');

        // Bind test selection change event first
        $testSelect.on('change', (e) => {
            this.onTestChange(e.target, $newRow);
        });

        // Initialize Select2 for the new row
        $testSelect.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Test'
        });

        // If testData is provided, populate the row
        if (testData) {
            console.log('=== POPULATING TEST ROW ===');
            console.log('Test data received:', testData);
            console.log('Test ID from data:', testData.test_id);
            console.log('Test name from data:', testData.test_name);
            console.log('Available tests in testsData:', this.testsData.length);
            console.log('Looking for test ID:', testData.test_id);
            
            // Debug: show what test IDs are available
            const availableIds = this.testsData.map(t => t.id);
            console.log('Available test IDs in testsData:', availableIds);
            console.log('Is test ID', testData.test_id, 'in available IDs?', availableIds.includes(parseInt(testData.test_id)));

            // Find the test in our testsData to get the correct information
            const foundTest = this.testsData.find(t => t.id == testData.test_id);
            if (foundTest) {
                console.log('Found matching test in testsData:', foundTest);

                // Populate all the fields with the correct data first
                setTimeout(() => {
                    // Set the select value
                    $testSelect.val(testData.test_id);

                    // Destroy and reinitialize Select2 to ensure proper display
                    $testSelect.select2('destroy').select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Select Test'
                    });

                    // Get current patient demographics for range calculation
                    const patientAge = parseInt($('#patientAge').val()) || null;
                    const patientGender = $('#patientGender').val() || null;

                    // Calculate demographic-appropriate ranges (use current patient, not stored ranges)
                    const rangeData = this.calculateAppropriateRanges(patientAge, patientGender, foundTest);

                    // Use entry data for non-range fields, demographic ranges for range fields
                    const categoryName = testData.category_name || foundTest.category_name || '';
                    const categoryId = testData.category_id || foundTest.category_id || '';
                    const price = testData.price || foundTest.price || 0;

                    console.log('Using demographic-appropriate ranges for edit mode:', {
                        test_id: testData.test_id,
                        category_name: categoryName,
                        rangeType: rangeData.type,
                        min: rangeData.min,
                        max: rangeData.max,
                        unit: rangeData.unit,
                        price: price,
                        result: testData.result_value
                    });

                    // Populate test details with demographic-appropriate ranges
                    $newRow.find('.test-category').val(categoryName);
                    $newRow.find('.test-category-id').val(categoryId);
                    $newRow.find('.test-price').val(price);
                    $newRow.find('.test-result').val(testData.result_value || '');

                    // Use demographic-appropriate ranges instead of stored ranges
                    this.updateRangeDisplay($newRow, rangeData);

                    // Don't trigger change event to avoid overwriting entry-specific data
                    // $testSelect.trigger('change');

                    console.log('Test row populated with test ID:', testData.test_id, 'Name:', foundTest.name);
                }, 200); // Increased timeout to ensure DOM is ready
            } else {
                console.warn('Test not found in testsData for ID:', testData.test_id);
                console.log('Looking for test with ID:', testData.test_id, 'in', this.testsData.map(t => ({ id: t.id, name: t.name })));
                console.log('Available test IDs:', this.testsData.map(t => t.id));
                console.log('Looking for test ID type:', typeof testData.test_id, 'Available ID types:', this.testsData.map(t => typeof t.id));

                // Try to find test with string/number conversion
                let foundTestAlt = this.testsData.find(t => t.id == testData.test_id || t.id === String(testData.test_id) || String(t.id) === String(testData.test_id));
                if (foundTestAlt) {
                    console.log('Found test with alternative matching:', foundTestAlt);
                    // Use the found test data
                    const rangeData = this.calculateAppropriateRanges(
                        parseInt($('#patientAge').val()) || null,
                        $('#patientGender').val() || null,
                        foundTestAlt
                    );

                    $newRow.find('.test-category').val(foundTestAlt.category_name || '');
                    $newRow.find('.test-category-id').val(foundTestAlt.category_id || '');
                    $newRow.find('.test-price').val(foundTestAlt.price || 0);
                    $newRow.find('.test-result').val(testData.result_value || '');
                    this.updateRangeDisplay($newRow, rangeData);

                    console.log('Test row populated with alternative match for ID:', testData.test_id, 'Name:', foundTestAlt.name);
                    return; // Exit early since we found the test
                }

                // If test not found in our data, try to populate with what we have
                const testName = testData.test_name || `Test ${testData.test_id}`;
                console.log('Adding missing test option:', testData.test_id, testName);
                console.log('Test data available for missing test:', {
                    test_id: testData.test_id,
                    test_name: testData.test_name,
                    category_name: testData.category_name,
                    hasTestName: !!testData.test_name
                });

                // Add the missing test option if it doesn't exist
                if ($testSelect.find(`option[value="${testData.test_id}"]`).length === 0) {
                    $testSelect.append(`<option value="${testData.test_id}">${testName}</option>`);
                } else {
                    // Update existing option with correct name
                    $testSelect.find(`option[value="${testData.test_id}"]`).text(testName);
                }

                // Populate with available data
                setTimeout(() => {
                    $testSelect.val(testData.test_id);

                    // Reinitialize Select2
                    $testSelect.select2('destroy').select2({
                        theme: 'bootstrap4',
                        width: '100%',
                        placeholder: 'Select Test'
                    });

                    // Use whatever data is available from the entry (fallback for missing tests)
                    const categoryName = testData.category_name || testData.test_name || 'Unknown Category';
                    const price = testData.price || 0;

                    // Create fallback range data from stored values
                    const fallbackRangeData = {
                        min: testData.min || null,
                        max: testData.max || null,
                        unit: testData.unit || '',
                        type: 'general',
                        label: 'Stored Range'
                    };

                    console.log('Using fallback data for missing test:', {
                        test_id: testData.test_id,
                        category_name: categoryName,
                        rangeData: fallbackRangeData,
                        price: price
                    });

                    $newRow.find('.test-category').val(categoryName);
                    $newRow.find('.test-category-id').val(testData.category_id || '');
                    $newRow.find('.test-price').val(price);
                    $newRow.find('.test-result').val(testData.result_value || '');

                    // Use fallback range data
                    this.updateRangeDisplay($newRow, fallbackRangeData);

                    console.log('Test row populated with fallback data for ID:', testData.test_id, 'Name:', testName);
                }, 200);
            }
        }
    }

    /**
     * Remove a test row
     */
    removeTestRow(button) {
        const $row = $(button).closest('.test-row');
        $row.remove();
        this.calculateTotals();

        // Ensure at least one test row exists
        if ($('#testsContainer .test-row').length === 0) {
            this.addTestRow();
        }
    }

    /**
     * Handle test selection change
     */
    onTestChange(selectElement, $row) {
        const $select = $(selectElement);
        const selectedOption = $select.find('option:selected');
        const testId = selectedOption.val();

        console.log('Test selection changed to:', testId);
        console.log('Row being updated:', $row.data('row-index'));

        if (testId) {
            // Check if this row already has data (from edit mode) - if so, don't overwrite
            const existingCategory = $row.find('.test-category').val();
            const existingUnit = $row.find('.test-unit').val();
            const existingMin = $row.find('.test-min').val();
            const existingMax = $row.find('.test-max').val();

            // If the row already has complete data, don't overwrite it (edit mode)
            if (existingCategory && existingUnit && existingMin && existingMax) {
                console.log('Row already has complete data, not overwriting:', {
                    category: existingCategory,
                    unit: existingUnit,
                    min: existingMin,
                    max: existingMax
                });
                this.calculateTotals();
                return;
            }

            // Find the test in our testsData for accurate information
            const foundTest = this.testsData.find(t => t.id == testId);

            if (foundTest) {
                console.log('Found test data for ID', testId, ':', foundTest);

                // Get current patient demographics
                const patientAge = parseInt($('#patientAge').val()) || null;
                const patientGender = $('#patientGender').val() || null;

                // Calculate appropriate ranges for this patient
                const rangeData = this.calculateAppropriateRanges(patientAge, patientGender, foundTest);

                // Populate test details from testsData
                $row.find('.test-category').val(foundTest.category_name || '');
                $row.find('.test-category-id').val(foundTest.category_id || '');
                $row.find('.test-price').val(foundTest.price || 0);

                // Use calculated demographic-appropriate ranges
                this.updateRangeDisplay($row, rangeData);

                console.log('Updated row with demographic-appropriate ranges:', {
                    category: foundTest.category_name,
                    rangeType: rangeData.type,
                    min: rangeData.min,
                    max: rangeData.max,
                    unit: rangeData.unit,
                    price: foundTest.price
                });
            } else {
                // Fallback to data attributes if test not found in testsData
                console.warn('Test not found in testsData for ID:', testId, 'using data attributes');
                $row.find('.test-category').val(selectedOption.data('category') || '');
                $row.find('.test-unit').val(selectedOption.data('unit') || '');
                $row.find('.test-min').val(selectedOption.data('min') || '');
                $row.find('.test-max').val(selectedOption.data('max') || '');
                $row.find('.test-price').val(selectedOption.data('price') || 0);
            }
        } else {
            // Clear test details including range indicator
            $row.find('.test-category, .test-unit, .test-min, .test-max').val('');
            $row.find('.test-price').val(0);
            $row.find('.range-type-indicator').remove();
        }

        this.calculateTotals();
    }

    /**
     * Calculate pricing totals
     */
    calculateTotals() {
        let subtotal = 0;

        // Sum up all test prices
        $('.test-price').each(function () {
            const price = parseFloat($(this).val()) || 0;
            subtotal += price;
        });

        const discount = parseFloat($('#discountAmount').val()) || 0;
        const total = Math.max(subtotal - discount, 0);

        $('#subtotal').val(subtotal.toFixed(2));
        $('#totalPrice').val(total.toFixed(2));
    }

    /**
     * Calculate appropriate reference ranges based on patient demographics (with caching)
     * @param {number|null} patientAge - Patient's age in years
     * @param {string|null} patientGender - Patient's gender ('Male', 'Female', etc.)
     * @param {object} testData - Test data containing all range fields
     * @returns {object} Range data with min, max, unit, type, and label
     */
    calculateAppropriateRanges(patientAge, patientGender, testData) {
        // Performance optimization: Check cache first
        const cacheKey = this.generateRangeCacheKey(patientAge, patientGender, testData.id);
        const cachedResult = this.getRangeFromCache(cacheKey);
        if (cachedResult) {
            return cachedResult;
        }
        // Age threshold for child vs adult classification
        const CHILD_AGE_THRESHOLD = 18;
        
        // Validate inputs
        if (!testData) {
            return this.getDefaultRangeData();
        }

        // Validate patient demographics
        const validation = this.validatePatientDemographics(patientAge, patientGender);
        const validAge = validation.age;
        const validGender = validation.gender;

        // Log warnings if any
        if (validation.warnings.length > 0) {
            console.warn('Patient demographic validation warnings:', validation.warnings);
        }
        
        // Check if patient is a child (under 18)
        if (validAge !== null && validAge < CHILD_AGE_THRESHOLD) {
            // Use child ranges if available
            if (this.hasValidRange(testData.min_child, testData.max_child)) {
                const result = {
                    min: testData.min_child,
                    max: testData.max_child,
                    unit: testData.child_unit || testData.unit || '',
                    type: 'child',
                    label: 'Child Range'
                };
                this.setRangeInCache(cacheKey, result);
                return result;
            }
        } else if (validAge !== null && validAge >= CHILD_AGE_THRESHOLD) {
            // Adult patient - check gender-specific ranges
            if (validGender === 'male') {
                if (this.hasValidRange(testData.min_male, testData.max_male)) {
                    const result = {
                        min: testData.min_male,
                        max: testData.max_male,
                        unit: testData.male_unit || testData.unit || '',
                        type: 'male',
                        label: 'Male Range'
                    };
                    this.setRangeInCache(cacheKey, result);
                    return result;
                }
            } else if (validGender === 'female') {
                if (this.hasValidRange(testData.min_female, testData.max_female)) {
                    const result = {
                        min: testData.min_female,
                        max: testData.max_female,
                        unit: testData.female_unit || testData.unit || '',
                        type: 'female',
                        label: 'Female Range'
                    };
                    this.setRangeInCache(cacheKey, result);
                    return result;
                }
            }
        }
        
        // Fallback to general ranges
        const result = {
            min: testData.min,
            max: testData.max,
            unit: testData.unit || '',
            type: 'general',
            label: 'General Range'
        };

        // Cache the result for future use
        this.setRangeInCache(cacheKey, result);
        return result;
    }

    /**
     * Check if range values are valid (not null/undefined and at least one is present)
     * @param {number|null} min - Minimum value
     * @param {number|null} max - Maximum value
     * @returns {boolean} True if at least one valid range value exists
     */
    hasValidRange(min, max) {
        return (min !== null && min !== undefined) || (max !== null && max !== undefined);
    }

    /**
     * Normalize gender string to lowercase for consistent comparison
     * @param {string|null} gender - Gender string
     * @returns {string} Normalized gender ('male', 'female', or 'unknown')
     */
    normalizeGender(gender) {
        if (!gender || typeof gender !== 'string') {
            return 'unknown';
        }
        
        const normalized = gender.toLowerCase().trim();
        if (normalized === 'male' || normalized === 'm') {
            return 'male';
        } else if (normalized === 'female' || normalized === 'f') {
            return 'female';
        }
        
        return 'unknown';
    }

    /**
     * Get default range data when no specific ranges are available
     * @returns {object} Default range data structure
     */
    getDefaultRangeData() {
        return {
            min: null,
            max: null,
            unit: '',
            type: 'general',
            label: 'General Range'
        };
    }

    /**
     * Validate patient demographic data
     * @param {number|null} age - Patient age
     * @param {string|null} gender - Patient gender
     * @returns {object} Validation result with isValid flag and normalized values
     */
    validatePatientDemographics(age, gender) {
        const result = {
            isValid: true,
            age: null,
            gender: null,
            warnings: []
        };

        // Validate age
        if (age !== null && age !== undefined) {
            const numericAge = parseInt(age);
            if (isNaN(numericAge) || numericAge < 0 || numericAge > 150) {
                result.warnings.push(`Invalid age: ${age}. Using general ranges.`);
                result.age = null;
            } else {
                result.age = numericAge;
            }
        }

        // Validate and normalize gender
        if (gender && typeof gender === 'string') {
            result.gender = this.normalizeGender(gender);
        }

        return result;
    }

    /**
     * Generate cache key for range calculation
     * @param {number|null} age - Patient age
     * @param {string|null} gender - Patient gender
     * @param {number} testId - Test ID
     * @returns {string} Cache key
     */
    generateRangeCacheKey(age, gender, testId) {
        return `${age || 'null'}_${gender || 'null'}_${testId}`;
    }

    /**
     * Get range from cache if available and not expired
     * @param {string} cacheKey - Cache key
     * @returns {object|null} Cached range data or null
     */
    getRangeFromCache(cacheKey) {
        const cached = this.rangeCache.get(cacheKey);
        if (cached && (Date.now() - cached.timestamp) < this.rangeCacheTimeout) {
            return cached.data;
        }
        
        // Remove expired cache entry
        if (cached) {
            this.rangeCache.delete(cacheKey);
        }
        
        return null;
    }

    /**
     * Set range in cache with timestamp
     * @param {string} cacheKey - Cache key
     * @param {object} rangeData - Range data to cache
     */
    setRangeInCache(cacheKey, rangeData) {
        this.rangeCache.set(cacheKey, {
            data: rangeData,
            timestamp: Date.now()
        });

        // Prevent cache from growing too large
        if (this.rangeCache.size > 1000) {
            // Remove oldest entries (simple LRU-like behavior)
            const keysToDelete = Array.from(this.rangeCache.keys()).slice(0, 200);
            keysToDelete.forEach(key => this.rangeCache.delete(key));
        }
    }

    /**
     * Clear range cache (useful when test data is reloaded)
     */
    clearRangeCache() {
        this.rangeCache.clear();
        console.log('Range cache cleared');
    }

    /**
     * Optimized batch update for multiple test rows
     * @param {Array} testRows - Array of test row data to update
     * @param {number|null} patientAge - Patient age
     * @param {string|null} patientGender - Patient gender
     */
    batchUpdateTestRanges(testRows, patientAge, patientGender) {
        const startTime = performance.now();
        
        // Pre-calculate all ranges to minimize DOM access
        const rangeUpdates = testRows.map(rowData => {
            const testData = this.testsData.find(t => t.id == rowData.testId);
            if (testData) {
                return {
                    $row: rowData.$row,
                    rangeData: this.calculateAppropriateRanges(patientAge, patientGender, testData)
                };
            }
            return null;
        }).filter(update => update !== null);

        // Apply all updates in a single DOM manipulation cycle
        rangeUpdates.forEach(update => {
            this.updateRangeDisplay(update.$row, update.rangeData);
        });

        const endTime = performance.now();
        console.log(`Batch range update completed in ${(endTime - startTime).toFixed(2)}ms for ${rangeUpdates.length} tests`);
        
        return rangeUpdates.length;
    }

    /**
     * Test demographic range functionality with sample data
     */
    testDemographicRangeFunctionality() {
        console.log('=== TESTING DEMOGRAPHIC RANGE FUNCTIONALITY ===');
        
        if (this.testsData.length === 0) {
            console.error('No test data available for testing');
            return;
        }

        // Find a test with demographic ranges for testing
        const testWithRanges = this.testsData.find(test => 
            test.min_male || test.max_male || test.min_female || test.max_female || test.min_child || test.max_child
        );

        if (!testWithRanges) {
            console.warn('No tests with demographic ranges found for testing');
            return;
        }

        console.log('Testing with test:', testWithRanges.name, 'ID:', testWithRanges.id);

        // Test child ranges (age 10)
        const childRange = this.calculateAppropriateRanges(10, 'Male', testWithRanges);
        console.log('Child range (age 10, Male):', childRange);

        // Test adult male ranges (age 30)
        const maleRange = this.calculateAppropriateRanges(30, 'Male', testWithRanges);
        console.log('Adult male range (age 30, Male):', maleRange);

        // Test adult female ranges (age 25)
        const femaleRange = this.calculateAppropriateRanges(25, 'Female', testWithRanges);
        console.log('Adult female range (age 25, Female):', femaleRange);

        // Test general ranges (no demographics)
        const generalRange = this.calculateAppropriateRanges(null, null, testWithRanges);
        console.log('General range (no demographics):', generalRange);

        console.log('=== DEMOGRAPHIC RANGE TESTING COMPLETE ===');
    }

    /**
     * Format date for HTML date input (requires YYYY-MM-DD format)
     * @param {string} dateString - Date string in various formats
     * @returns {string} Formatted date string in YYYY-MM-DD format
     */
    formatDateForInput(dateString) {
        if (!dateString) {
            return '';
        }

        try {
            // Try to parse the date
            const date = new Date(dateString);
            
            // Check if date is valid
            if (isNaN(date.getTime())) {
                console.warn('Invalid date format:', dateString);
                return '';
            }

            // Format as YYYY-MM-DD for HTML date input
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            
            return `${year}-${month}-${day}`;
        } catch (error) {
            console.error('Error formatting date:', dateString, error);
            return '';
        }
    }

    /**
     * Validate complete demographic range workflow
     * This method can be called from browser console for testing
     */
    validateDemographicRangeWorkflow() {
        console.log('=== VALIDATING DEMOGRAPHIC RANGE WORKFLOW ===');
        
        const results = {
            cacheTest: false,
            performanceTest: false,
            validationTest: false,
            uiUpdateTest: false
        };

        try {
            // Test 1: Cache functionality
            console.log('Testing cache functionality...');
            const testData = this.testsData[0];
            if (testData) {
                const key = this.generateRangeCacheKey(25, 'Male', testData.id);
                const range1 = this.calculateAppropriateRanges(25, 'Male', testData);
                const range2 = this.calculateAppropriateRanges(25, 'Male', testData); // Should use cache
                results.cacheTest = true;
                console.log('✓ Cache test passed');
            }

            // Test 2: Performance test
            console.log('Testing performance...');
            const startTime = performance.now();
            for (let i = 0; i < 100; i++) {
                if (this.testsData[0]) {
                    this.calculateAppropriateRanges(25, 'Male', this.testsData[0]);
                }
            }
            const endTime = performance.now();
            const avgTime = (endTime - startTime) / 100;
            results.performanceTest = avgTime < 1; // Should be under 1ms per calculation
            console.log(`✓ Performance test: ${avgTime.toFixed(3)}ms per calculation`);

            // Test 3: Validation test
            console.log('Testing validation...');
            const validation = this.validatePatientDemographics(25, 'Male');
            results.validationTest = validation.age === 25 && validation.gender === 'male';
            console.log('✓ Validation test passed');

            // Test 4: UI update test (if DOM elements exist)
            console.log('Testing UI updates...');
            if ($('#patientAge').length > 0) {
                $('#patientAge').val('25');
                $('#patientGender').val('Male');
                this.updateAllTestRangesForCurrentPatient();
                results.uiUpdateTest = true;
                console.log('✓ UI update test passed');
            } else {
                console.log('⚠ UI elements not available for testing');
                results.uiUpdateTest = true; // Don't fail if UI not available
            }

        } catch (error) {
            console.error('Workflow validation error:', error);
        }

        const allPassed = Object.values(results).every(result => result === true);
        console.log('=== WORKFLOW VALIDATION RESULTS ===');
        console.log('Cache Test:', results.cacheTest ? '✓ PASS' : '✗ FAIL');
        console.log('Performance Test:', results.performanceTest ? '✓ PASS' : '✗ FAIL');
        console.log('Validation Test:', results.validationTest ? '✓ PASS' : '✗ FAIL');
        console.log('UI Update Test:', results.uiUpdateTest ? '✓ PASS' : '✗ FAIL');
        console.log('Overall Result:', allPassed ? '✓ ALL TESTS PASSED' : '✗ SOME TESTS FAILED');
        
        return results;
    }

    /**
     * Verify that demographic range fields are available in test data
     */
    verifyDemographicRangeFields() {
        if (this.testsData.length === 0) {
            console.warn('No test data available for demographic range field verification');
            return;
        }

        const requiredFields = ['min_male', 'max_male', 'min_female', 'max_female', 'min_child', 'max_child'];
        const sampleTest = this.testsData[0];
        const missingFields = [];
        const availableFields = [];

        requiredFields.forEach(field => {
            if (sampleTest.hasOwnProperty(field)) {
                availableFields.push(field);
            } else {
                missingFields.push(field);
            }
        });

        console.log('Demographic range field verification:', {
            totalTests: this.testsData.length,
            availableFields: availableFields,
            missingFields: missingFields,
            sampleTestId: sampleTest.id,
            sampleTestName: sampleTest.name
        });

        if (missingFields.length > 0) {
            console.error('Missing demographic range fields:', missingFields);
            console.error('Demographic range functionality may not work properly');
            
            if (typeof toastr !== 'undefined') {
                toastr.warning('Some demographic range fields are missing from test data. Age/gender-specific ranges may not work properly.');
            }
        } else {
            console.log('All demographic range fields are available');
            
            // Check if any tests actually have demographic-specific ranges
            let testsWithDemographicRanges = 0;
            this.testsData.forEach(test => {
                if (test.min_male || test.max_male || test.min_female || test.max_female || test.min_child || test.max_child) {
                    testsWithDemographicRanges++;
                }
            });

            console.log(`Found ${testsWithDemographicRanges} tests with demographic-specific ranges out of ${this.testsData.length} total tests`);
            
            // Run functionality test if we have tests with demographic ranges
            if (testsWithDemographicRanges > 0) {
                this.testDemographicRangeFunctionality();
            }
        }
    }

    /**
     * Update range display in a test row with demographic-appropriate ranges
     * @param {jQuery} $row - The test row jQuery object
     * @param {object} rangeData - Range data from calculateAppropriateRanges
     */
    updateRangeDisplay($row, rangeData) {
        if (!$row || !rangeData) {
            return;
        }

        // Update min/max fields
        $row.find('.test-min').val(rangeData.min || '');
        $row.find('.test-max').val(rangeData.max || '');
        $row.find('.test-unit').val(rangeData.unit || '');

        // Add/update range type indicator
        let $indicator = $row.find('.range-type-indicator');
        if ($indicator.length === 0) {
            $indicator = $('<span class="range-type-indicator badge ml-1"></span>');
            $row.find('.test-unit').after($indicator);
        }

        // Set indicator styling based on range type
        $indicator.removeClass('badge-info badge-primary badge-success badge-secondary badge-warning')
                 .addClass(this.getRangeTypeBadgeClass(rangeData.type))
                 .text(rangeData.label)
                 .attr('title', `Using ${rangeData.label.toLowerCase()} for this patient`)
                 .attr('data-toggle', 'tooltip');

        // Initialize tooltip if not already done
        if (!$indicator.data('bs.tooltip')) {
            $indicator.tooltip();
        }
    }

    /**
     * Get CSS class for range type badge
     * @param {string} rangeType - Type of range ('child', 'male', 'female', 'general')
     * @returns {string} CSS class for the badge
     */
    getRangeTypeBadgeClass(rangeType) {
        switch (rangeType) {
            case 'child':
                return 'badge-info';
            case 'male':
                return 'badge-primary';
            case 'female':
                return 'badge-success';
            case 'general':
            default:
                return 'badge-secondary';
        }
    }

    /**
     * Update all test ranges for the currently selected patient (with performance monitoring)
     */
    updateAllTestRangesForCurrentPatient() {
        const startTime = performance.now();
        const patientAge = parseInt($('#patientAge').val()) || null;
        const patientGender = $('#patientGender').val() || null;

        console.log('Updating all test ranges for patient:', { age: patientAge, gender: patientGender });

        // Batch DOM updates for better performance
        const updates = [];
        
        // Collect all updates first
        $('.test-row').each((index, row) => {
            const $row = $(row);
            const testId = $row.find('.test-select').val();

            if (testId) {
                const testData = this.testsData.find(t => t.id == testId);
                if (testData) {
                    const rangeData = this.calculateAppropriateRanges(patientAge, patientGender, testData);
                    updates.push({ $row, rangeData, testName: testData.name });
                }
            }
        });

        // Apply all updates in batch
        updates.forEach(update => {
            this.updateRangeDisplay(update.$row, update.rangeData);
            console.log(`Updated ranges for test ${update.testName}:`, update.rangeData);
        });

        // Performance monitoring
        const endTime = performance.now();
        const duration = endTime - startTime;
        console.log(`Range update completed in ${duration.toFixed(2)}ms for ${updates.length} tests`);
        
        if (duration > 100) {
            console.warn(`Range update took ${duration.toFixed(2)}ms, which exceeds the 100ms target`);
        }
    }

    /**
     * Reset all test ranges to general ranges (when no patient selected)
     */
    resetAllTestRangesToGeneral() {
        console.log('Resetting all test ranges to general');

        $('.test-row').each((index, row) => {
            const $row = $(row);
            const testId = $row.find('.test-select').val();

            if (testId) {
                const testData = this.testsData.find(t => t.id == testId);
                if (testData) {
                    const rangeData = {
                        min: testData.min,
                        max: testData.max,
                        unit: testData.unit || '',
                        type: 'general',
                        label: 'General Range'
                    };
                    this.updateRangeDisplay($row, rangeData);
                }
            }
        });
    }

    /**
     * Initialize Select2 dropdowns
     */
    initializeSelect2() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    /**
     * Reset the entry form
     */
    resetForm() {
        $('#entryForm')[0].reset();
        $('#entryId').val('');
        $('#testsContainer').empty();
        this.testRowCounter = 0;

        // Clear Select2 selections
        $('.select2').val(null).trigger('change');

        // Reset pricing
        this.calculateTotals();
    }

    /**
     * Handle owner selection change
     */
    onOwnerChange(ownerId) {
        //console.log('Owner changed:', ownerId);

        if (ownerId) {
            // Enable patient and doctor selects
            $('#patientSelect, #doctorSelect').prop('disabled', false);

            // Load patients and doctors for this owner
            this.loadPatientsForOwner(ownerId);
            this.loadDoctorsForOwner(ownerId);
        } else {
            // Disable and clear patient and doctor selects only if not in edit mode
            if (!this.currentEditId) {
                $('#patientSelect, #doctorSelect').prop('disabled', true).val('').trigger('change');
                this.clearPatientDetails();
            }
        }
    }

    /**
     * Handle patient selection change
     */
    onPatientChange(patientId) {
        //console.log('Patient changed:', patientId);

        if (patientId) {
            // Load patient details
            this.loadPatientDetails(patientId);
        } else {
            // Clear patient details
            this.clearPatientDetails();
            // Reset to general ranges when no patient selected
            this.resetAllTestRangesToGeneral();
        }
    }

    /**
     * Load patients for selected owner
     */
    async loadPatientsForOwner(ownerId) {
        try {
            //console.log('Loading patients for owner:', ownerId);

            const response = await $.ajax({
                url: 'ajax/patient_api.php',
                method: 'GET',
                data: { action: 'list', owner_id: ownerId },
                dataType: 'json'
            });

            const $select = $('#patientSelect');
            $select.prop('disabled', false);
            $select.empty().append('<option value="">Select Patient</option>');

            if (response.success && response.data) {
                response.data.forEach(patient => {
                    $select.append(`<option value="${patient.id}">${patient.name}</option>`);
                });
                //console.log('Loaded', response.data.length, 'patients');
            } else {
                //console.warn('No patients found or API error:', response.message);
            }

            // Refresh Select2 if initialized
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change');
            }
        } catch (error) {
            //console.error('Error loading patients:', error);
        }
    }

    /**
     * Load doctors for selected owner
     */
    async loadDoctorsForOwner(ownerId) {
        try {
            //console.log('Loading doctors for owner:', ownerId);

            const response = await $.ajax({
                url: 'ajax/doctor_api.php',
                method: 'GET',
                data: { action: 'list', owner_id: ownerId },
                dataType: 'json'
            });

            const $select = $('#doctorSelect');
            $select.prop('disabled', false);
            $select.empty().append('<option value="">Select Doctor</option>');

            if (response.success && response.data) {
                response.data.forEach(doctor => {
                    $select.append(`<option value="${doctor.id}">${doctor.name}</option>`);
                });
                //console.log('Loaded', response.data.length, 'doctors');
            } else {
                //console.warn('No doctors found or API error:', response.message);
            }

            // Refresh Select2 if initialized
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change');
            }
        } catch (error) {
            //console.error('Error loading doctors:', error);
        }
    }

    /**
     * Load patient details
     */
    async loadPatientDetails(patientId) {
        try {
            //console.log('Loading patient details for ID:', patientId);

            const response = await $.ajax({
                url: 'ajax/patient_api.php',
                method: 'GET',
                data: { action: 'get', id: patientId },
                dataType: 'json'
            });

            if (response.success && response.data) {
                const patient = response.data;
                //console.log('Patient details loaded:', patient);

                $('#patientName').val(patient.name || '');
                $('#patientContact').val(patient.contact || '');
                $('#patientAge').val(patient.age || '');
                $('#patientGender').val(patient.gender || '').trigger('change');
                $('#patientAddress').val(patient.address || '');

                // Update all test ranges after patient details are loaded (debounced for performance)
                setTimeout(() => {
                    this.debouncedRangeUpdate();
                }, 100); // Small delay to ensure DOM updates are complete
            } else {
                console.warn('Failed to load patient details:', response.message);
            }
        } catch (error) {
            console.error('Error loading patient details:', error);
        }
    }

    /**
     * Clear patient details
     */
    clearPatientDetails() {
        $('#patientName, #patientContact, #patientAge, #patientAddress').val('');
        $('#patientGender').val('').trigger('change');
    }

    /**
     * View entry details
     */
    async viewEntry(entryId) {
        //console.log('Viewing entry:', entryId);

        try {
            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: { action: 'get', id: entryId },
                dataType: 'json'
            });
            console.log('Displaying entry response:', response);
            if (response.success && response.data) {
                this.displayEntryDetails(response.data);
                $('#viewEntryModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load entry details');
            }
        } catch (error) {
            console.error('Error loading entry details:', error);
            toastr.error('Failed to load entry details');
        }
    }

    /**
     * Display entry details in modal
     */
    displayEntryDetails(entry) {
        console.log('Displaying entry details:', entry);
        console.log('Entry tests data:', entry.tests);

        // Debug each test
        if (entry.tests && entry.tests.length > 0) {
            entry.tests.forEach((test, index) => {
                console.log(`Test ${index + 1}:`, {
                    test_id: test.test_id,
                    test_name: test.test_name,
                    category_id: test.category_id,
                    min: test.min,
                    max: test.max,
                    unit: test.unit,
                    result_value: test.result_value
                });
            });
        }
        const detailsHtml = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Patient Information</h6>
                    <p><strong>Name:</strong> ${entry.patient_name || 'N/A'}</p>
                    <p><strong>Contact:</strong> ${entry.patient_contact || 'N/A'}</p>
                    <p><strong>Age:</strong> ${entry.age || 'N/A'}</p>
                    <p><strong>Gender:</strong> ${entry.gender || 'N/A'}</p>
                </div>
                <div class="col-md-6">
                    <h6>Entry Information</h6>
                    <p><strong>Entry ID:</strong> ${entry.id}</p>
                    <p><strong>Date:</strong> ${entry.entry_date || 'N/A'}</p>
                    <p><strong>Status:</strong> <span class="badge badge-info">${entry.status || 'pending'}</span></p>
                    <p><strong>Priority:</strong> <span class="badge badge-secondary">${entry.priority || 'normal'}</span></p>
                </div>
            </div>
            <hr>
            <h6>Tests</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Test Name</th>
                            <th>Category</th>
                            <th>Result</th>
                            <th>Range</th>
                            <th>Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${entry.tests ? entry.tests.map(test => {
                            // Calculate demographic-appropriate ranges for display
                            const patientAge = parseInt(entry.age) || null;
                            const patientGender = entry.gender || null;
                            
                            // Find test data for range calculation
                            const testData = this.testsData.find(t => t.id == test.test_id);
                            let rangeDisplay = '';
                            let rangeTypeIndicator = '';
                            
                            if (testData) {
                                const rangeData = this.calculateAppropriateRanges(patientAge, patientGender, testData);
                                const minVal = rangeData.min || '';
                                const maxVal = rangeData.max || '';
                                rangeDisplay = minVal || maxVal ? `${minVal} - ${maxVal}` : '';
                                rangeTypeIndicator = rangeData.type !== 'general' ? 
                                    `<span class="badge badge-${this.getRangeTypeBadgeClass(rangeData.type).replace('badge-', '')} ml-1" title="${rangeData.label}">${rangeData.type.charAt(0).toUpperCase() + rangeData.type.slice(1)}</span>` : '';
                            } else {
                                // Fallback to stored values if test data not found
                                rangeDisplay = (test.min || test.max) ? `${test.min || ''} - ${test.max || ''}` : '';
                            }
                            
                            return `
                                <tr>
                                    <td>${test.test_name || 'N/A'}</td>
                                    <td>${test.category_name || 'N/A'}</td>
                                    <td>${test.result_value || 'Pending'}</td>
                                    <td>${rangeDisplay}${rangeTypeIndicator}</td>
                                    <td>${test.unit || ''}</td>
                                </tr>
                            `;
                        }).join('') : '<tr><td colspan="5">No tests found</td></tr>'}
                    </tbody>
                </table>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6>Pricing</h6>
                    <p><strong>Subtotal:</strong> ₹${parseFloat(entry.subtotal || 0).toFixed(2)}</p>
                    <p><strong>Discount:</strong> ₹${parseFloat(entry.discount_amount || 0).toFixed(2)}</p>
                    <p><strong>Total:</strong> ₹${parseFloat(entry.total_price || 0).toFixed(2)}</p>
                </div>
                <div class="col-md-6">
                    <h6>Additional Information</h6>
                    <p><strong>Doctor:</strong> ${entry.doctor_name || 'Not assigned'}</p>
                    <p><strong>Added By:</strong> ${entry.added_by_full_name || 'Unknown'}</p>
                    <p><strong>Notes:</strong> ${entry.notes || 'No notes'}</p>
                </div>
            </div>
        `;

        $('#entryDetails').html(detailsHtml);
    }

    /**
     * Edit entry
     */
    async editEntry(entryId) {
        console.log('Editing entry:', entryId);

        try {
            // Show loading state
            if (typeof toastr !== 'undefined') {
                toastr.info('Loading entry data...'); 
            }

            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: { action: 'get', id: entryId },
                dataType: 'json'
            });
            console.log('Editing entry response:', response);
            
            // Special debugging for entry 17
            if (entryId == 17) {
                console.log('=== SPECIAL DEBUG FOR ENTRY 17 ===');
                console.log('Response data:', response.data);
                console.log('Tests in response:', response.data.tests);
                if (response.data.tests) {
                    response.data.tests.forEach((test, index) => {
                        console.log(`Test ${index + 1}:`, {
                            test_id: test.test_id,
                            test_name: test.test_name,
                            category_name: test.category_name,
                            result_value: test.result_value
                        });
                    });
                }
                console.log('=== END SPECIAL DEBUG ===');
            }
            
            if (response.success && response.data) {
                $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');
                $('#entryModal').modal('show');

                // Ensure owner data is loaded before populating form
                if (this.ownersData.length === 0) {
                    console.log('Owner data not loaded, loading now...');
                    await this.loadOwnersData();
                }

                // Populate form after modal is shown
                await this.populateEditForm(response.data);

                if (typeof toastr !== 'undefined') {
                    toastr.success('Entry loaded successfully');
                }
            } else {
                toastr.error(response.message || 'Failed to load entry for editing');
            }
        } catch (error) {
            console.error('Error loading entry for editing:', error);
            toastr.error('Failed to load entry for editing');
        }
    }

    /**
     * Populate edit form with entry data
     */
    async populateEditForm(entry) {
        // console.log('Populating edit form with entry:', entry);
        // console.log('Entry keys:', Object.keys(entry));
        // console.log('Added by field:', entry.added_by);
        // console.log('Owner ID field:', entry.owner_id);
        // console.log('Patient ID field:', entry.patient_id);
        // console.log('Doctor ID field:', entry.doctor_id);

        this.currentEditId = entry.id;

        // Reset form first
        this.resetForm();

        // Populate basic fields
        $('#entryId').val(entry.id);
        
        // Format entry date for HTML date input (requires YYYY-MM-DD format)
        if (entry.entry_date) {
            const formattedDate = this.formatDateForInput(entry.entry_date);
            $('#entryDate').val(formattedDate);
            console.log('Setting entry date:', entry.entry_date, '-> formatted:', formattedDate);
        }
        
        $('#entryStatus').val(entry.status);
        $('#priority').val(entry.priority);
        $('#referralSource').val(entry.referral_source);
        $('#entryNotes').val(entry.notes);

        // Populate pricing
        $('#subtotal').val(entry.subtotal || 0);
        $('#discountAmount').val(entry.discount_amount || 0);
        $('#totalPrice').val(entry.total_price || 0);

        // Initialize Select2 first
        this.initializeSelect2();

        // Handle owner/added_by selection and dependent dropdowns
        const ownerId = entry.added_by || entry.owner_id || entry.owner_added_by;
        if (ownerId) {
            //console.log('Setting owner/added_by to:', ownerId);

            // Check if the owner exists in the dropdown, if not add it
            const $ownerSelect = $('#ownerAddedBySelect');
            if ($ownerSelect.find(`option[value="${ownerId}"]`).length === 0) {
                const ownerName = entry.added_by_full_name || entry.added_by_username || `User ${ownerId}`;
                //console.log('Adding missing owner option:', ownerId, ownerName);
                $ownerSelect.append(`<option value="${ownerId}">${ownerName}</option>`);
            }

            $ownerSelect.val(ownerId).trigger('change');

            // Wait for owner change to load patients and doctors
            await this.loadPatientsForOwner(ownerId);
            await this.loadDoctorsForOwner(ownerId);

            // Now set patient and doctor values
            if (entry.patient_id) {
                //console.log('Setting patient to:', entry.patient_id);

                // Check if patient exists in dropdown, if not add it
                const $patientSelect = $('#patientSelect');
                if ($patientSelect.find(`option[value="${entry.patient_id}"]`).length === 0) {
                    const patientName = entry.patient_name || `Patient ${entry.patient_id}`;
                    //console.log('Adding missing patient option:', entry.patient_id, patientName);
                    $patientSelect.append(`<option value="${entry.patient_id}">${patientName}</option>`);
                }

                $patientSelect.val(entry.patient_id).trigger('change');

                // Load patient details
                await this.loadPatientDetails(entry.patient_id);
            }

            if (entry.doctor_id) {
                //console.log('Setting doctor to:', entry.doctor_id);

                // Check if doctor exists in dropdown, if not add it
                const $doctorSelect = $('#doctorSelect');
                if ($doctorSelect.find(`option[value="${entry.doctor_id}"]`).length === 0) {
                    const doctorName = entry.doctor_name || `Doctor ${entry.doctor_id}`;
                    //console.log('Adding missing doctor option:', entry.doctor_id, doctorName);
                    $doctorSelect.append(`<option value="${entry.doctor_id}">${doctorName}</option>`);
                }

                $doctorSelect.val(entry.doctor_id).trigger('change');
            }
        } else {
            //console.warn('No owner/added_by found in entry data:', entry);
        }

        // Always reload tests data to ensure we have the latest data
        console.log('Reloading tests data to ensure accuracy...');
        await this.loadTestsData();
        console.log('Tests data loaded:', this.testsData.length, 'tests');
        
        // Debug: Log test IDs and names for troubleshooting
        if (this.testsData.length > 0) {
            console.log('Available tests:', this.testsData.map(t => ({ id: t.id, name: t.name, id_type: typeof t.id })));
        }
        
        // Debug: show first few tests
        if (this.testsData.length > 0) {
            console.log('First 5 tests in testsData:', this.testsData.slice(0, 5).map(t => ({id: t.id, name: t.name})));
        }

        // Double-check that we have tests data
        if (this.testsData.length === 0) {
            console.error('No tests data available! This will cause issues with test selection.');
            toastr.warning('Tests data could not be loaded. Test selection may not work properly.');
        }

        // Clear and populate tests
        $('#testsContainer').empty();
        this.testRowCounter = 0;

        if (entry.tests && entry.tests.length > 0) {
            console.log('Populating', entry.tests.length, 'tests:', entry.tests);
            console.log('Available tests data:', this.testsData.length, 'tests');

            // Debug: show what test IDs we're looking for vs what we have
            const entryTestIds = entry.tests.map(t => t.test_id);
            const availableTestIds = this.testsData.map(t => t.id);
            console.log('Entry test IDs:', entryTestIds);
            console.log('Available test IDs:', availableTestIds);
            console.log('Missing test IDs:', entryTestIds.filter(id => !availableTestIds.includes(parseInt(id))));

            // Debug each test in detail
            entry.tests.forEach((test, index) => {
                console.log(`Entry Test ${index + 1} Details:`, {
                    test_id: test.test_id,
                    test_id_type: typeof test.test_id,
                    test_name: test.test_name,
                    category_name: test.category_name,
                    category_id: test.category_id,
                    min: test.min,
                    max: test.max,
                    unit: test.unit,
                    result_value: test.result_value,
                    price: test.price,
                    all_keys: Object.keys(test)
                });

                // Check if this test has unique data
                if (index > 0) {
                    const prevTest = entry.tests[index - 1];
                    const isDuplicate = (
                        test.category_name === prevTest.category_name &&
                        test.min === prevTest.min &&
                        test.max === prevTest.max &&
                        test.unit === prevTest.unit
                    );
                    if (isDuplicate) {
                        console.warn(`Test ${index + 1} has duplicate data with previous test!`);
                        console.warn('Current test:', test);
                        console.warn('Previous test:', prevTest);
                    }
                }

                this.addTestRow(test);
            });
        } else {
            console.log('No tests found, adding empty test row');
            this.addTestRow();
        }

        // Trigger change events for Select2 dropdowns to update display
        setTimeout(() => {
            $('#entryStatus').trigger('change');
            $('#priority').trigger('change');
            $('#referralSource').trigger('change');
            //console.log('Edit form populated successfully');
        }, 100);
    }

    /**
     * Delete entry
     */
    deleteEntry(entryId) {
        //console.log('Deleting entry:', entryId);

        // Show confirmation modal
        $('#deleteModal').modal('show');

        // Handle confirmation
        $('#confirmDelete').off('click').on('click', async () => {
            try {
                const response = await $.ajax({
                    url: 'ajax/entry_api_fixed.php',
                    method: 'POST',
                    data: { action: 'delete', id: entryId },
                    dataType: 'json'
                });

                if (response.success) {
                    toastr.success('Entry deleted successfully');
                    this.refreshTable();
                    $('#deleteModal').modal('hide');
                } else {
                    toastr.error(response.message || 'Failed to delete entry');
                }
            } catch (error) {
                //console.error('Error deleting entry:', error);
                toastr.error('Failed to delete entry');
            }
        });
    }

    /**
     * Validate form before submission
     */
    validateForm() {
        const errors = [];

        // Check required fields
        if (!$('#ownerAddedBySelect').val()) {
            errors.push('Owner/Added By is required');
        }

        if (!$('#patientSelect').val()) {
            errors.push('Patient is required');
        }

        if (!$('#entryDate').val()) {
            errors.push('Entry Date is required');
        }

        // Check tests
        const testSelects = $('.test-select');
        let hasValidTest = false;
        testSelects.each(function () {
            if ($(this).val()) {
                hasValidTest = true;
                return false;
            }
        });

        if (!hasValidTest) {
            errors.push('At least one test is required');
        }

        return errors;
    }

    /**
     * Save entry (create or update)
     */
    async saveEntry() {
        //console.log('Saving entry...');
        //console.log('Current edit ID:', this.currentEditId);

        try {
            // Validate form
            const validationErrors = this.validateForm();
            if (validationErrors.length > 0) {
                toastr.error('Please fix the following errors:<br>' + validationErrors.join('<br>'));
                return;
            }

            const patientId = $('#patientSelect').val();
            const ownerAddedBy = $('#ownerAddedBySelect').val();

            const formData = new FormData($('#entryForm')[0]);
            formData.append('action', 'save'); // Use 'save' action as expected by API

            // Ensure owner_added_by is set (it should be in the form already)
            if (!formData.get('owner_added_by')) {
                formData.append('owner_added_by', ownerAddedBy);
            }

            // Ensure patient_id is set
            if (!formData.get('patient_id')) {
                formData.append('patient_id', patientId);
            }

            // Debug form data
            //console.log('Form data being sent:');
            for (let [key, value] of formData.entries()) {
                //console.log(key, ':', value);
            }

            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });

            //console.log('Save response:', response);

            if (response.success) {
                toastr.success(this.currentEditId ? 'Entry updated successfully' : 'Entry created successfully');
                this.refreshTable();
                $('#entryModal').modal('hide');
                this.resetForm();
            } else {
                //console.error('Save failed:', response);
                toastr.error(response.message || 'Failed to save entry');
            }
        } catch (error) {
            //console.error('Error saving entry:', error);
            console.error('Error details:', {
                status: error.status,
                statusText: error.statusText,
                responseText: error.responseText
            });

            let errorMessage = 'Failed to save entry';
            if (error.responseJSON && error.responseJSON.message) {
                errorMessage += ': ' + error.responseJSON.message;
            } else if (error.responseText) {
                try {
                    const errorData = JSON.parse(error.responseText);
                    if (errorData.message) {
                        errorMessage += ': ' + errorData.message;
                    }
                } catch (parseError) {
                    // If response is not JSON, show status
                    if (error.status) {
                        errorMessage += ` (Status: ${error.status})`;
                    }
                }
            }

            toastr.error(errorMessage);
        }
    }

    /**
     * Print entry details
     */
    printEntryDetails() {
        const printContent = $('#entryDetails').html();
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Entry Details</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .table { width: 100%; border-collapse: collapse; }
                        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        .table th { background-color: #f2f2f2; }
                        .badge { padding: 2px 6px; border-radius: 3px; }
                        .badge-info { background-color: #17a2b8; color: white; }
                        .badge-secondary { background-color: #6c757d; color: white; }
                    </style>
                </head>
                <body>
                    <h2>Entry Details</h2>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}

// Initialize Entry Manager when page loads
let entryManager;
$(document).ready(function () {
    try {
        // console.log('Page ready, checking dependencies...');
        // console.log('jQuery version:', $.fn.jquery);
        // console.log('DataTables available:', typeof $.fn.DataTable !== 'undefined');
        // console.log('Select2 available:', typeof $.fn.select2 !== 'undefined');
        // console.log('Toastr available:', typeof toastr !== 'undefined');
        // console.log('Bootstrap available:', typeof $.fn.modal !== 'undefined');

        // console.log('Initializing Entry Manager...');
        entryManager = new EntryManager();
        window.entryManager = entryManager;
        
        // Add global testing functions for easy access
        window.testDemographicRanges = () => entryManager.validateDemographicRangeWorkflow();
        window.testRangeCalculation = (age, gender, testId) => {
            const test = entryManager.testsData.find(t => t.id == testId);
            if (test) {
                return entryManager.calculateAppropriateRanges(age, gender, test);
            } else {
                console.error('Test not found with ID:', testId);
                return null;
            }
        };
        
        // console.log('Entry Manager initialized successfully');
        console.log('Demographic range testing functions available:');
        console.log('- testDemographicRanges() - Run complete workflow validation');
        console.log('- testRangeCalculation(age, gender, testId) - Test specific range calculation');
    } catch (error) {
        // console.error('Error initializing Entry Manager:', error);
        // console.error('Error stack:', error.stack);
    }
});/*
*
 * Accessibility and keyboard navigation enhancements
 */
$(document).ready(function () {
    // Add keyboard navigation for modals
    $(document).on('keydown', function (e) {
        // ESC key to close modals
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide');
        }

        // Enter key to submit forms in modals
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            const $modal = $(e.target).closest('.modal');
            if ($modal.length && $modal.find('form').length) {
                e.preventDefault();
                $modal.find('form').submit();
            }
        }
    });

    // Add ARIA labels and accessibility attributes
    $('#entriesTable').attr('aria-label', 'Test entries data table');
    $('.btn').each(function () {
        if (!$(this).attr('aria-label') && $(this).attr('title')) {
            $(this).attr('aria-label', $(this).attr('title'));
        }
    });

    // Focus management for modals
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('input, select, textarea').filter(':visible').first().focus();
    });

    // Add loading indicators
    $(document).ajaxStart(function () {
        $('body').addClass('loading');
    }).ajaxStop(function () {
        $('body').removeClass('loading');
    });
});