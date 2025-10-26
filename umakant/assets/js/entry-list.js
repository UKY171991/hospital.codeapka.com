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

                    // Use entry data if available, otherwise use testsData
                    const categoryName = testData.category_name || foundTest.category_name || '';
                    const categoryId = testData.category_id || foundTest.category_id || '';
                    const unit = testData.unit || foundTest.unit || '';
                    const min = testData.min || foundTest.min || '';
                    const max = testData.max || foundTest.max || '';
                    const price = testData.price || foundTest.price || 0;

                    console.log('Using test data for row:', {
                        test_id: testData.test_id,
                        category_name: categoryName,
                        unit: unit,
                        min: min,
                        max: max,
                        price: price,
                        source: testData.category_name ? 'entry_data' : 'tests_data'
                    });

                    // Populate test details
                    $newRow.find('.test-category').val(categoryName);
                    $newRow.find('.test-category-id').val(categoryId);
                    $newRow.find('.test-unit').val(unit);
                    $newRow.find('.test-min').val(min);
                    $newRow.find('.test-max').val(max);
                    $newRow.find('.test-price').val(price);
                    $newRow.find('.test-result').val(testData.result_value || '');

                    // Don't trigger change event to avoid overwriting entry-specific data
                    // $testSelect.trigger('change');

                    console.log('Test row populated with test ID:', testData.test_id, 'Name:', foundTest.name);
                }, 200); // Increased timeout to ensure DOM is ready
            } else {
                console.warn('Test not found in testsData for ID:', testData.test_id);
                console.log('Looking for test with ID:', testData.test_id, 'in', this.testsData.map(t => ({ id: t.id, name: t.name })));

                // If test not found in our data, try to populate with what we have
                const testName = testData.test_name || `Test ${testData.test_id}`;
                console.log('Adding missing test option:', testData.test_id, testName);

                // Add the missing test option if it doesn't exist
                if ($testSelect.find(`option[value="${testData.test_id}"]`).length === 0) {
                    $testSelect.append(`<option value="${testData.test_id}">${testName}</option>`);
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

                    // Use whatever data is available from the entry
                    const categoryName = testData.category_name || testData.test_name || 'Unknown Category';
                    const unit = testData.unit || '';
                    const min = testData.min || '';
                    const max = testData.max || '';
                    const price = testData.price || 0;

                    console.log('Using fallback data for missing test:', {
                        test_id: testData.test_id,
                        category_name: categoryName,
                        unit: unit,
                        min: min,
                        max: max,
                        price: price
                    });

                    $newRow.find('.test-category').val(categoryName);
                    $newRow.find('.test-category-id').val(testData.category_id || '');
                    $newRow.find('.test-unit').val(unit);
                    $newRow.find('.test-min').val(min);
                    $newRow.find('.test-max').val(max);
                    $newRow.find('.test-price').val(price);
                    $newRow.find('.test-result').val(testData.result_value || '');

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

                // Populate test details from testsData (more reliable than data attributes)
                $row.find('.test-category').val(foundTest.category_name || '');
                $row.find('.test-category-id').val(foundTest.category_id || '');
                $row.find('.test-unit').val(foundTest.unit || '');
                $row.find('.test-min').val(foundTest.min || '');
                $row.find('.test-max').val(foundTest.max || '');
                $row.find('.test-price').val(foundTest.price || 0);

                console.log('Updated row with test data:', {
                    category: foundTest.category_name,
                    unit: foundTest.unit,
                    min: foundTest.min,
                    max: foundTest.max,
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
            // Clear test details
            $row.find('.test-category, .test-unit, .test-min, .test-max').val('');
            $row.find('.test-price').val(0);
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
            console.log('Displaying entry response:', response.data);
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
                    category_name: test.category_name,
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
                        ${entry.tests ? entry.tests.map(test => `
                            <tr>
                                <td>${test.test_name || 'N/A'}</td>
                                <td>${test.category_name || 'N/A'}</td>
                                <td>${test.result_value || 'Pending'}</td>
                                <td>${test.min || ''} - ${test.max || ''}</td>
                                <td>${test.unit || ''}</td>
                            </tr>
                        `).join('') : '<tr><td colspan="5">No tests found</td></tr>'}
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
        $('#entryDate').val(entry.entry_date);
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
                    test_name: test.test_name,
                    category_name: test.category_name,
                    category_id: test.category_id,
                    min: test.min,
                    max: test.max,
                    unit: test.unit,
                    result_value: test.result_value,
                    price: test.price
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
        // console.log('Entry Manager initialized successfully');
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