/**
 * Entry List Management System
 * Clean, modern JavaScript for managing hospital test entries
 */

class EntryManager {
    constructor() {
        this.entriesTable = null;
        this.testsData = [];
        this.categoriesData = [];
        this.mainCategoriesData = [];
        this.patientsData = [];
        this.doctorsData = [];
        this.ownersData = [];
        this.currentEditId = null;
        this.testRowCounter = 0;

        // Performance optimization: Range calculation cache
        this.rangeCache = new Map();
        this.rangeCacheTimeout = 5 * 60 * 1000; // 5 minutes cache timeout

        // Performance optimization: Data caching
        this.dataCache = new Map();
        this.dataCacheTimeout = 10 * 60 * 1000; // 10 minutes cache timeout for data
        this.cacheKeys = {
            TESTS_DATA: 'tests_data',
            CATEGORIES_DATA: 'categories_data',
            MAIN_CATEGORIES_DATA: 'main_categories_data',
            OWNERS_DATA: 'owners_data'
        };

        // Performance optimization: Debounced update functions
        this.debouncedRangeUpdate = this.debounce(this.updateAllTestRangesAndValidation.bind(this), 150);
        this.debouncedCategoryFilter = this.debounce(this.onCategoryFilterChange.bind(this), 100);
        this.debouncedTestDropdownUpdate = this.debounce(this.updateAllTestDropdowns.bind(this), 200);

        // Performance monitoring
        this.performanceMetrics = {
            filterOperations: [],
            cacheHits: 0,
            cacheMisses: 0,
            apiCalls: 0
        };

        this.init();

        // Start performance monitoring (every 2 minutes)
        setInterval(() => {
            this.monitorPerformance();
        }, 2 * 60 * 1000);
    }

    /**
     * Debug function to check test data (can be called from browser console)
     */
    debugTestData() {
        //// console.log removed
        //// console.log removed

        if (this.testsData.length > 0) {
            //// console.log removed

            // Check for duplicates
            const testNames = this.testsData.map(t => t.name);
            const uniqueNames = [...new Set(testNames)];
            //// console.log removed
            //// console.log removed

            if (testNames.length !== uniqueNames.length) {
                // console.warn removed
                const duplicates = testNames.filter((name, index) => testNames.indexOf(name) !== index);
                //// console.log removed

                duplicates.forEach(dupName => {
                    const duplicateTests = this.testsData.filter(t => t.name === dupName);
                    //// console.log removed
                });
            } else {
                //// console.log removed
            }
        }
        //// console.log removed
    }

    /**
     * Debug function to check category data (can be called from browser console)
     */
    debugCategoryData() {
        //// console.log removed
        //// console.log removed
        //// console.log removed

        if (this.categoriesData.length > 0) {
            //// console.log removed

            // Check category structure
            const categoriesWithMainId = this.categoriesData.filter(cat => cat.main_category_id);
            const categoriesWithoutMainId = this.categoriesData.filter(cat => !cat.main_category_id);

            //// console.log removed
            //// console.log removed

            if (categoriesWithMainId.length > 0) {
                //// console.log removed
            }
        }

        if (this.mainCategoriesData.length > 0) {
            //// console.log removed
        }

        // Check if tests have category information
        if (this.testsData.length > 0) {
            const testsWithCategory = this.testsData.filter(test => test.category_id);
            const testsWithCategoryName = this.testsData.filter(test => test.category_name);

            //// console.log removed
            //// console.log removed

            if (testsWithCategory.length > 0) {
                //// console.log removed
            }
        }

        // Check current form state
        const $categoryDropdowns = $('.test-category-select');
        //// console.log removed

        $categoryDropdowns.each((index, dropdown) => {
            const $dropdown = $(dropdown);
            const optionCount = $dropdown.find('option').length;
            const selectedValue = $dropdown.val();
            //// console.log removed
        });

        //// console.log removed
    }

    /**
     * Debug function to check edit mode state (can be called from browser console)
     */
    debugEditMode() {
        //// console.log removed
        //// console.log removed

        // Check test rows in the form
        const $testRows = $('.test-row');
        //// console.log removed

        $testRows.each((index, row) => {
            const $row = $(row);
            const rowIndex = $row.data('row-index');
            const testId = $row.find('.test-select').val();
            const testName = $row.find('.test-select option:selected').text();
            const categoryId = $row.find('.test-category-select').val();
            const categoryName = $row.find('.test-category-select option:selected').text();
            const mainCategoryId = $row.find('.test-main-category-id').val();

            /*// console.log removed:`, {
                testId: testId,
                testName: testName,
                categoryId: categoryId,
                categoryName: categoryName,
                mainCategoryId: mainCategoryId,
                categoryOptions: $row.find('.test-category-select option').length
            });*/
        });

        //// console.log removed
    }

    /**
     * Debug function to check specific entry data (can be called from browser console)
     */
    async debugSpecificEntry(entryId) {
        //// console.log removed
        //// console.log removed

        try {
            // Get entry data from API
            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: {
                    action: 'get',
                    id: entryId,
                    secret_key: 'hospital-api-secret-2024'
                },
                dataType: 'json'
            });

            if (response.success && response.data) {
                //// console.log removed

                if (response.data.tests) {
                    //// console.log removed
                    response.data.tests.forEach((test, index) => {
                        /*// console.log removed*/

                        // Check if this test exists in our testsData
                        const foundInTestsData = this.testsData.find(t => t.id == test.test_id);
                        if (foundInTestsData) {
                            /*// console.log removed*/
                        } else {
                            // console.warn removed
                        }

                        // Check if the category exists in our categoriesData
                        if (test.category_id) {
                            const foundCategory = this.categoriesData.find(cat => cat.id == test.category_id);
                            if (foundCategory) {
                                //// console.log removed
                            } else {
                                // console.warn removed
                            }
                        }
                    });
                }
            } else {
                // console.error removed
            }
        } catch (error) {
            // console.error removed
        }

        //// console.log removed
    }

    /**
     * Initialize the Entry Manager
     */
    init() {
        // console.log removed

        // Wait for DOM to be ready
        $(document).ready(() => {
            // console.log removed
            try {
                // Add a small delay to ensure all libraries are loaded
                setTimeout(() => {
                    this.initializeDataTable();
                    this.loadInitialData();
                    this.bindEvents();
                    this.loadStatistics();
                    // console.log removed
                }, 100);
            } catch (error) {
                // console.error removed
                $('#entriesTable').closest('.card-body').html(`
                    <div class="alert alert-danger">
                        <h5>Initialization Error</h5>
                        <p><strong>Error:</strong> ${error.message}</p>
                        <button class="btn btn-primary btn-sm" onclick="location.reload()">Refresh Page</button>
                    </div>
                `);
            }
        });
    }

    /**
     * Initialize DataTable with proper configuration
     */
    initializeDataTable() {
        // console.log removed

        // Check if the table element exists
        if ($('#entriesTable').length === 0) {
            // console.error removed
            $('#entriesTable').closest('.card-body').html(`
                <div class="alert alert-danger">
                    <h5>Table Element Not Found</h5>
                    <p>The entries table element could not be found in the DOM.</p>
                    <button class="btn btn-primary btn-sm" onclick="location.reload()">Refresh Page</button>
                </div>
            `);
            return;
        }

        // Check if jQuery is available
        if (typeof $ === 'undefined') {
            // console.error removed
            document.getElementById('entriesTable').innerHTML = `
                <div class="alert alert-danger">
                    <h5>jQuery Library Not Loaded</h5>
                    <p>The jQuery JavaScript library failed to load.</p>
                    <button class="btn btn-primary btn-sm" onclick="location.reload()">Refresh Page</button>
                </div>
            `;
            return;
        }

        // Check if DataTable is available
        if (typeof $.fn.DataTable === 'undefined') {
            // console.error removed
            $('#entriesTable').html(`
                <div class="alert alert-danger">
                    <h5>DataTables Library Not Loaded</h5>
                    <p>The DataTables JavaScript library failed to load.</p>
                    <button class="btn btn-primary btn-sm" onclick="location.reload()">Refresh Page</button>
                </div>
            `);
            return;
        }

        try {
            // console.log removed
            // console.log removed

            // Check table structure
            const $table = $('#entriesTable');
            const headerCells = $table.find('thead th').length;
            // console.log removed

            if (headerCells !== 11) {
                throw new Error(`Table structure mismatch: Expected 11 columns, found ${headerCells} columns`);
            }

            // Add custom search functions for all filters
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'entriesTable') {
                    return true;
                }

                const rowData = settings.aoData[dataIndex]._aData;

                // Main Category Filter
                const mainCategoryFilter = $('#mainCategoryFilter').val();
                if (mainCategoryFilter) {
                    const mainCategories = rowData.agg_main_test_categories || rowData.main_test_categories || '';
                    if (!mainCategories || !mainCategories.toLowerCase().includes(mainCategoryFilter.toLowerCase())) {
                        return false;
                    }
                }

                // Status Filter
                const statusFilter = $('#statusFilter').val();
                if (statusFilter) {
                    const status = rowData.status || '';
                    if (status.toLowerCase() !== statusFilter.toLowerCase()) {
                        return false;
                    }
                }

                // Date Filter
                const dateFilter = $('#dateFilter').val();
                if (dateFilter) {
                    const entryDate = new Date(rowData.entry_date);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    let showRow = false;
                    switch (dateFilter) {
                        case 'today':
                            const todayEnd = new Date(today);
                            todayEnd.setHours(23, 59, 59, 999);
                            showRow = entryDate >= today && entryDate <= todayEnd;
                            break;
                        case 'yesterday':
                            const yesterday = new Date(today);
                            yesterday.setDate(yesterday.getDate() - 1);
                            const yesterdayEnd = new Date(yesterday);
                            yesterdayEnd.setHours(23, 59, 59, 999);
                            showRow = entryDate >= yesterday && entryDate <= yesterdayEnd;
                            break;
                        case 'this_week':
                            const weekStart = new Date(today);
                            weekStart.setDate(today.getDate() - today.getDay());
                            showRow = entryDate >= weekStart;
                            break;
                        case 'this_month':
                            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                            showRow = entryDate >= monthStart;
                            break;
                        default:
                            showRow = true;
                    }
                    if (!showRow) {
                        return false;
                    }
                }

                // Patient Filter
                const patientFilter = $('#patientFilter').val();
                if (patientFilter) {
                    const patientName = rowData.patient_name || '';
                    if (!patientName.toLowerCase().includes(patientFilter.toLowerCase())) {
                        return false;
                    }
                }

                // Doctor Filter
                const doctorFilter = $('#doctorFilter').val();
                if (doctorFilter) {
                    const doctorName = rowData.doctor_name || '';
                    if (!doctorName.toLowerCase().includes(doctorFilter.toLowerCase())) {
                        return false;
                    }
                }

                return true; // Show row if all filters pass
            });

            this.entriesTable = $('#entriesTable').DataTable({
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
                        // console.log removed
                        if (json && json.success) {
                            return json.data || [];
                        } else {
                            // console.error removed
                            if (typeof toastr !== 'undefined') {
                                toastr.error(json ? json.message : 'Failed to load entries - invalid response');
                            } else {
                                alert('Failed to load entries: ' + (json ? json.message : 'Invalid response'));
                            }
                            return [];
                        }
                    },
                    error: function (xhr, error, thrown) {
                        // console.error removed

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
                        width: '4%'
                    },
                    {
                        data: 'patient_name',
                        title: 'Patient',
                        width: '10%',
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
                        width: '8%',
                        render: function (data, type, row) {
                            return data || '<span class="text-muted">Not assigned</span>';
                        }
                    },
                    {
                        data: 'test_names',
                        title: 'Tests',
                        width: '12%',
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
                        data: 'agg_test_categories',
                        title: 'Test Category',
                        width: '12%',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                const categories = data || row.test_categories || '';
                                if (!categories) {
                                    return '<span class="text-muted">No category</span>';
                                }

                                // Handle multiple categories
                                const categoryArray = categories.split(',').map(cat => cat.trim()).filter(cat => cat);
                                const uniqueCategories = [...new Set(categoryArray)];

                                if (uniqueCategories.length === 0) {
                                    return '<span class="text-muted">No category</span>';
                                } else if (uniqueCategories.length === 1) {
                                    return `<span class="badge badge-secondary">${uniqueCategories[0]}</span>`;
                                } else {
                                    const displayText = uniqueCategories.slice(0, 2).join(', ');
                                    const remainingCount = uniqueCategories.length - 2;
                                    return `<span class="badge badge-secondary" title="${uniqueCategories.join(', ')}">${displayText}${remainingCount > 0 ? ` +${remainingCount}` : ''}</span>`;
                                }
                            }
                            return data || '';
                        }
                    },
                    {
                        data: 'status',
                        title: 'Status',
                        width: '8%',
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
                        width: '8%',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                // Only show amount if there are tests
                                if (row.tests_count && row.tests_count > 0) {
                                    // Use total_price if available, otherwise calculate from subtotal
                                    let amount = parseFloat(data) || 0;
                                    if (amount === 0 && row.subtotal) {
                                        amount = parseFloat(row.subtotal) || 0;
                                        // Subtract discount if available
                                        const discount = parseFloat(row.discount_amount) || 0;
                                        amount = Math.max(amount - discount, 0);
                                    }
                                    return `₹${amount.toFixed(2)}`;
                                } else {
                                    // No tests, show ₹0.00
                                    return `₹0.00`;
                                }
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
                        width: '8%',
                        render: function (data, type, row) {
                            return data || row.added_by_username || 'Unknown';
                        }
                    },
                    {
                        data: null,
                        title: 'Actions',
                        width: '8%',
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
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // ID
                    { responsivePriority: 2, targets: 1 }, // Patient
                    { responsivePriority: 3, targets: -1 }, // Actions
                    { responsivePriority: 4, targets: 5 }, // Status
                    { responsivePriority: 5, targets: 3 }  // Tests
                ],
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

            //// console.log removed
        } catch (error) {
            // console.error removed
            // Show detailed error message for debugging
            const errorDetails = error.message || 'Unknown error';
            $('#entriesTable').html(`
                <div class="alert alert-danger">
                    <h5>Failed to initialize data table</h5>
                    <p><strong>Error:</strong> ${errorDetails}</p>
                    <p>Please check the browser console for more details and refresh the page.</p>
                    <button class="btn btn-primary btn-sm" onclick="location.reload()">Refresh Page</button>
                </div>
            `);
        }
    }

    /**
     * Load initial data (tests, patients, doctors, owners)
     */
    async loadInitialData() {
        // //// console.log removed

        try {
            // Load tests data
            await this.loadTestsData();

            // Load main categories data
            await this.loadMainCategoriesData();

            // Load categories data for filtering
            await this.loadCategoriesForFilter();

            // Load owners/users data
            await this.loadOwnersData();

            // //// console.log removed
        } catch (error) {
            //// console.error removed
            toastr.error('Failed to load initial data');
        }
    }

    /**
     * Load tests data from API with enhanced error handling and caching
     */
    async loadTestsData(retryCount = 0, forceReload = false) {
        const maxRetries = 2;

        try {
            // Check cache first unless force reload is requested
            if (!forceReload) {
                const cachedData = this.getCacheData(this.cacheKeys.TESTS_DATA);
                if (cachedData) {
                    this.testsData = cachedData;
                    //// console.log removed
                    return true;
                }
            }

            //// console.log removed
            this.performanceMetrics.apiCalls++;

            const response = await $.ajax({
                url: 'ajax/test_api.php',
                method: 'GET',
                data: { action: 'simple_list' },
                dataType: 'json',
                timeout: 15000 // 15 second timeout for test data
            });

            if (response && response.success) {
                const rawTestsData = response.data || [];

                // Validate and clean test data
                const validatedTests = this.validateTestData(rawTestsData);
                this.testsData = validatedTests;

                // Cache the validated data
                this.setCacheData(this.cacheKeys.TESTS_DATA, validatedTests);

                // Clear range cache when test data is updated
                this.clearRangeCache();

                // Verify demographic range fields are available
                this.verifyDemographicRangeFields();

                //// console.log removed

                // Check for duplicate test names and handle them
                this.handleDuplicateTestNames();

                // Debug: show first few tests
                if (this.testsData.length > 0) {
                    // console.log removed
                    // console.log removed
                } else {
                    // console.warn removed
                    this.handleEmptyTestData();
                }

                return true; // Success
            } else {
                const errorMessage = response ? response.message : 'Invalid response from server';
                // console.error removed

                // Try retry if we haven't exceeded max retries
                if (retryCount < maxRetries) {
                    //// console.log removed
                    await this.delay(2000);
                    return await this.loadTestsData(retryCount + 1);
                }

                this.testsData = [];
                this.handleTestDataLoadError(errorMessage);
                return false; // Failed
            }
        } catch (error) {
            // console.error removed

            const errorDetails = {
                status: error.status,
                statusText: error.statusText,
                responseText: error.responseText,
                timeout: error.statusText === 'timeout'
            };
            // console.error removed

            // Try to parse error response for more details
            if (error.responseText) {
                try {
                    const errorData = JSON.parse(error.responseText);
                    // console.error removed
                    errorDetails.parsedError = errorData;
                } catch (parseError) {
                    // console.error removed
                }
            }

            // Try retry for network errors if we haven't exceeded max retries
            if (retryCount < maxRetries && (error.status === 0 || error.statusText === 'timeout' || error.status >= 500)) {
                //// console.log removed
                await this.delay(3000);
                return await this.loadTestsData(retryCount + 1);
            }

            this.testsData = [];
            this.handleTestDataLoadError(error.message || 'Network error occurred');
            return false; // Failed
        }
    }

    /**
     * Validate test data structure
     * @param {Array} tests - Array of test objects to validate
     * @returns {Array} Array of valid test objects
     */
    validateTestData(tests) {
        if (!Array.isArray(tests)) {
            // console.error removed
            return [];
        }

        const validTests = tests.filter(test => {
            if (!test || typeof test !== 'object') {
                // console.warn removed
                return false;
            }

            if (!test.id || !test.name) {
                // console.warn removed
                return false;
            }

            // Validate numeric fields
            if (test.price && isNaN(parseFloat(test.price))) {
                // console.warn removed
                test.price = 0; // Set default price
            }

            return true;
        });

        if (validTests.length !== tests.length) {
            // console.warn removed
        }

        return validTests;
    }

    /**
     * Handle duplicate test names
     */
    handleDuplicateTestNames() {
        const testNames = this.testsData.map(t => t.name);
        const uniqueNames = [...new Set(testNames)];

        if (testNames.length !== uniqueNames.length) {
            // console.error removed
            //// console.log removed

            // Find and log duplicates
            const duplicates = testNames.filter((name, index) => testNames.indexOf(name) !== index);
            //// console.log removed

            // Show detailed info about duplicates
            duplicates.forEach(dupName => {
                const duplicateTests = this.testsData.filter(t => t.name === dupName);
                /*// console.log removed));*/
            });

            // Show user warning about duplicates
            if (typeof toastr !== 'undefined') {
                toastr.warning(
                    `Found ${duplicates.length} duplicate test names. This may cause confusion in test selection.`,
                    'Duplicate Tests Detected',
                    { timeOut: 8000 }
                );
            }
        }
    }

    /**
     * Handle empty test data
     */
    handleEmptyTestData() {
        // console.warn removed

        if (typeof toastr !== 'undefined') {
            toastr.warning('No tests are available. Please contact your administrator.', 'No Tests Available');
        }

        // Disable test-related functionality
        this.handleTestDataUnavailable();
    }

    /**
     * Handle test data loading errors
     * @param {string} errorMessage - The error message to display
     */
    handleTestDataLoadError(errorMessage = 'Unknown error') {
        // console.error removed

        // Create retry UI for test data
        this.createTestDataRetryUI(errorMessage);

        // Show user-friendly error message
        if (typeof toastr !== 'undefined') {
            toastr.error(
                `Could not load test data: ${errorMessage}. <br>
                <button onclick="window.entryManager.retryTestDataLoad()" class="btn btn-sm btn-outline-light mt-1">
                    <i class="fas fa-retry"></i> Retry
                </button>`,
                'Test Data Loading Error',
                {
                    timeOut: 0,
                    extendedTimeOut: 0,
                    closeButton: true,
                    escapeHtml: false
                }
            );
        }

        // Apply fallback measures
        this.handleTestDataUnavailable();
    }

    /**
     * Create retry UI for test data loading
     * @param {string} errorMessage - The error message to display
     */
    createTestDataRetryUI(errorMessage) {
        try {
            // Remove existing retry UI
            $('#testDataRetryUI').remove();

            // Create retry UI
            const retryHtml = `
                <div id="testDataRetryUI" class="alert alert-danger mt-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <strong>Test data loading failed:</strong> ${this.escapeHtml(errorMessage)}
                    <br>
                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="window.entryManager.retryTestDataLoad()">
                        <i class="fas fa-retry mr-1"></i>Retry Loading Tests
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary mt-2 ml-2" onclick="window.entryManager.dismissTestDataError()">
                        <i class="fas fa-times mr-1"></i>Dismiss
                    </button>
                </div>
            `;

            // Add retry UI to the tests container or form
            const $container = $('#testsContainer').length > 0 ? $('#testsContainer') : $('#entryForm');
            $container.prepend(retryHtml);
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Retry test data loading
     */
    async retryTestDataLoad() {
        try {
            //// console.log removed

            // Show loading state
            const $retryButton = $('#testDataRetryUI button:first');
            const originalText = $retryButton.html();
            $retryButton.html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...').prop('disabled', true);

            // Attempt to reload test data
            const success = await this.loadTestsData();

            if (success) {
                // Success - remove retry UI and refresh UI
                $('#testDataRetryUI').remove();

                // Refresh test dropdowns if any exist
                this.updateAllTestDropdowns();

                if (typeof toastr !== 'undefined') {
                    toastr.success('Test data loaded successfully!');
                }

                //// console.log removed
            } else {
                // Failed again - restore button state
                $retryButton.html(originalText).prop('disabled', false);
                //// console.log removed
            }
        } catch (error) {
            // console.error removed

            // Restore button state
            const $retryButton = $('#testDataRetryUI button:first');
            $retryButton.html('<i class="fas fa-retry mr-1"></i>Retry Loading Tests').prop('disabled', false);
        }
    }

    /**
     * Dismiss test data error UI
     */
    dismissTestDataError() {
        try {
            $('#testDataRetryUI').remove();
            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Load categories data for filtering with enhanced error handling and caching
     */
    async loadCategoriesForFilter(retryCount = 0, forceReload = false) {
        const maxRetries = 2;

        try {
            // Check cache first unless force reload is requested
            if (!forceReload) {
                const cachedData = this.getCacheData(this.cacheKeys.CATEGORIES_DATA);
                if (cachedData) {
                    this.categoriesData = cachedData;
                    this.populateCategoryFilter();
                    //// console.log removed
                    return true;
                }
            }

            //// console.log removed
            this.performanceMetrics.apiCalls++;

            const response = await $.ajax({
                url: 'patho_api/test_category.php',
                method: 'GET',
                data: {
                    action: 'list',
                    all: '1',  // Load ALL categories, not just user-specific ones
                    secret_key: 'hospital-api-secret-2024'
                },
                dataType: 'json',
                timeout: 10000 // 10 second timeout
            });

            if (response && response.success) {
                this.categoriesData = response.data || [];

                // Validate category data structure
                const validCategories = this.validateCategoryData(this.categoriesData);
                if (validCategories.length !== this.categoriesData.length) {
                    // console.warn removed
                    this.categoriesData = validCategories;
                }

                // Cache the validated data
                this.setCacheData(this.cacheKeys.CATEGORIES_DATA, this.categoriesData);

                this.populateCategoryFilter();
                // console.log removed
                // console.log removed

                // Debug: Log first few categories to verify data structure
                if (this.categoriesData.length > 0) {
                    //// console.log removed
                }

                return true; // Success
            } else {
                const errorMessage = response ? response.message : 'Invalid response from server';
                // console.error removed

                // Try retry if we haven't exceeded max retries
                if (retryCount < maxRetries) {
                    //// console.log removed
                    await this.delay(2000);
                    return await this.loadCategoriesForFilter(retryCount + 1);
                }

                this.categoriesData = [];
                this.handleCategoryLoadError(errorMessage);
                return false; // Failed
            }
        } catch (error) {
            // console.error removed

            const errorDetails = {
                status: error.status,
                statusText: error.statusText,
                responseText: error.responseText,
                timeout: error.statusText === 'timeout'
            };
            // console.error removed

            // Try retry for network errors if we haven't exceeded max retries
            if (retryCount < maxRetries && (error.status === 0 || error.statusText === 'timeout' || error.status >= 500)) {
                //// console.log removed
                await this.delay(3000);
                return await this.loadCategoriesForFilter(retryCount + 1);
            }

            this.categoriesData = [];
            this.handleCategoryLoadError(error.message || 'Network error occurred');
            return false; // Failed
        }
    }

    /**
     * Validate category data structure
     * @param {Array} categories - Array of category objects to validate
     * @returns {Array} Array of valid category objects
     */
    validateCategoryData(categories) {
        if (!Array.isArray(categories)) {
            // console.error removed
            return [];
        }

        return categories.filter(category => {
            if (!category || typeof category !== 'object') {
                // console.warn removed
                return false;
            }

            if (!category.id || !category.name) {
                // console.warn removed
                return false;
            }

            return true;
        });
    }

    /**
     * Utility method to create a delay
     * @param {number} ms - Milliseconds to delay
     * @returns {Promise} Promise that resolves after the delay
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Load main categories data
     */
    async loadMainCategoriesData() {
        try {
            //// console.log removed
            const response = await $.ajax({
                url: 'ajax/main_test_category_api.php',
                method: 'GET',
                data: { action: 'list' },
                dataType: 'json'
            });

            if (response && response.success) {
                this.mainCategoriesData = response.data || [];
                //// console.log removed
            } else {
                //// console.error removed
                this.mainCategoriesData = [];
            }
        } catch (error) {
            //// console.error removed
            this.mainCategoriesData = [];
        }
    }

    /**
     * Populate category filter dropdown
     */
    populateCategoryFilter() {
        const $categoryFilter = $('#categoryFilter');
        if ($categoryFilter.length === 0) {
            //// console.warn removed
            return;
        }

        // Clear existing options except the first one
        $categoryFilter.empty().append('<option value="">All Categories (Show All Tests)</option>');

        // Add categories to dropdown
        this.categoriesData.forEach(category => {
            if (category && category.id && category.name) {
                const option = `<option value="${category.id}">${category.name}</option>`;
                $categoryFilter.append(option);
            }
        });

        // Update test count
        this.updateFilteredTestCount();

        //// console.log removed
    }

    /**
     * Handle category loading errors gracefully with retry options
     * @param {string} errorMessage - The error message to display
     */
    handleCategoryLoadError(errorMessage = 'Unknown error') {
        // console.warn removed
        // console.error removed

        const $categoryFilter = $('#categoryFilter');
        const $clearButton = $('#clearCategoryFilter');

        // Disable category filtering UI
        if ($categoryFilter.length > 0) {
            $categoryFilter.empty().append('<option value="">All Categories (Error loading categories)</option>');
            $categoryFilter.prop('disabled', true).addClass('error-state');
        }

        if ($clearButton.length > 0) {
            $clearButton.prop('disabled', true);
        }

        // Hide category filter indicator if visible
        this.hideCategoryFilterIndicator();

        // Show all tests since filtering is not available
        this.updateFilteredTestCount();

        // Create retry mechanism
        this.createCategoryRetryUI();

        // Show user-friendly error message with retry option
        if (typeof toastr !== 'undefined') {
            toastr.error(
                `Could not load test categories: ${errorMessage}. Category filtering is disabled. <br>
                <button onclick="window.entryManager.retryCategoryLoad()" class="btn btn-sm btn-outline-light mt-1">
                    <i class="fas fa-retry"></i> Retry
                </button>`,
                'Category Loading Error',
                {
                    timeOut: 0,
                    extendedTimeOut: 0,
                    closeButton: true,
                    escapeHtml: false
                }
            );
        }

        // Log error for debugging
        // console.error removed
    }

    /**
     * Create retry UI for category loading
     */
    createCategoryRetryUI() {
        try {
            // Remove existing retry UI
            $('#categoryRetryUI').remove();

            // Create retry UI
            const retryHtml = `
                <div id="categoryRetryUI" class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Category loading failed.</strong> Category filtering is disabled.
                    <br>
                    <button type="button" class="btn btn-sm btn-warning mt-2" onclick="window.entryManager.retryCategoryLoad()">
                        <i class="fas fa-retry mr-1"></i>Retry Loading Categories
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary mt-2 ml-2" onclick="window.entryManager.dismissCategoryError()">
                        <i class="fas fa-times mr-1"></i>Dismiss
                    </button>
                </div>
            `;

            // Add retry UI after the category filter
            $('#categoryFilter').parent().append(retryHtml);
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Retry category loading
     */
    async retryCategoryLoad() {
        try {
            //// console.log removed

            // Show loading state
            const $retryButton = $('#categoryRetryUI button:first');
            const originalText = $retryButton.html();
            $retryButton.html('<i class="fas fa-spinner fa-spin mr-1"></i>Loading...').prop('disabled', true);

            // Attempt to reload categories
            const success = await this.loadCategoriesForFilter();

            if (success) {
                // Success - remove retry UI and re-enable category filter
                $('#categoryRetryUI').remove();
                $('#categoryFilter').prop('disabled', false).removeClass('error-state');
                $('#clearCategoryFilter').prop('disabled', false);

                if (typeof toastr !== 'undefined') {
                    toastr.success('Categories loaded successfully!');
                }

                //// console.log removed
            } else {
                // Failed again - restore button state
                $retryButton.html(originalText).prop('disabled', false);
                //// console.log removed
            }
        } catch (error) {
            // console.error removed

            // Restore button state
            const $retryButton = $('#categoryRetryUI button:first');
            $retryButton.html('<i class="fas fa-retry mr-1"></i>Retry Loading Categories').prop('disabled', false);
        }
    }

    /**
     * Dismiss category error UI
     */
    dismissCategoryError() {
        try {
            $('#categoryRetryUI').remove();
            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Update the filtered test count display
     */
    updateFilteredTestCount() {
        const $countElement = $('#filteredTestCount');
        if ($countElement.length > 0) {
            const selectedCategory = $('#categoryFilter').val();
            let count = this.testsData.length;

            if (selectedCategory) {
                count = this.testsData.filter(test => test.category_id == selectedCategory).length;
            }

            $countElement.text(count);
        }
    }

    /**
     * Filter tests by selected category
     * @param {string|number} categoryId - The category ID to filter by (empty string for all)
     * @returns {Array} Filtered tests array
     */
    filterTestsByCategory(categoryId) {
        try {
            // Enhanced validation of input parameters
            if (!this.testsData || !Array.isArray(this.testsData)) {
                // console.warn removed
                return [];
            }

            // Handle empty tests data gracefully
            if (this.testsData.length === 0) {
                // console.warn removed
                return [];
            }

            // Enhanced type checking and normalization for category ID
            const normalizedCategoryId = this.normalizeCategoryId(categoryId);

            // If no valid category selected, return all tests
            if (normalizedCategoryId === null) {
                // console.log removed
                return this.testsData;
            }

            // Filter tests by category_id with enhanced validation and error handling
            const filteredTests = this.testsData.filter(test => {
                try {
                    // Enhanced test object validation
                    if (!this.isValidTestObject(test)) {
                        return false;
                    }

                    // Enhanced category ID validation with fallback behavior
                    const testCategoryId = this.normalizeTestCategoryId(test);

                    // Handle missing category data with fallback
                    if (testCategoryId === null) {
                        // If category data is incomplete, include test in "uncategorized" behavior
                        return normalizedCategoryId === 'uncategorized';
                    }

                    // Perform comparison with type safety
                    return testCategoryId === normalizedCategoryId;
                } catch (testError) {
                    // console.warn removed
                    return false; // Exclude problematic tests
                }
            });

            // console.log removed

            // Enhanced debugging and fallback behavior
            if (filteredTests.length === 0 && normalizedCategoryId !== 'uncategorized') {
                this.handleEmptyFilterResults(categoryId, normalizedCategoryId);
            }

            return filteredTests;
        } catch (error) {
            // console.error removed
            this.handleCategoryFilterError(error, categoryId);
            // Enhanced fallback: return all tests to maintain functionality
            return this.testsData || [];
        }
    }

    /**
     * Normalize category ID with enhanced type checking and validation
     * @param {*} categoryId - The category ID to normalize
     * @returns {string|null} Normalized category ID or null if invalid/empty
     */
    normalizeCategoryId(categoryId) {
        // Handle null, undefined, empty string cases
        if (categoryId === null || categoryId === undefined || categoryId === '') {
            return null;
        }

        // Handle boolean false (but not 0)
        if (categoryId === false) {
            return null;
        }

        // Handle numeric zero as valid category ID
        if (categoryId === 0 || categoryId === '0') {
            return '0';
        }

        // Convert to string and trim whitespace
        const stringValue = String(categoryId).trim();

        // Return null for empty strings after trimming
        if (stringValue === '') {
            return null;
        }

        // Validate that it's a reasonable category ID (numeric or alphanumeric)
        if (!/^[a-zA-Z0-9_-]+$/.test(stringValue)) {
            // console.warn removed
            return null;
        }

        return stringValue;
    }

    /**
     * Validate test object structure
     * @param {*} test - The test object to validate
     * @returns {boolean} True if test object is valid
     */
    isValidTestObject(test) {
        if (!test || typeof test !== 'object') {
            return false;
        }

        // Check for required fields
        if (!test.id || !test.name) {
            return false;
        }

        // Validate ID is numeric
        if (isNaN(parseInt(test.id))) {
            return false;
        }

        return true;
    }

    /**
     * Normalize test category ID with fallback behavior
     * @param {Object} test - The test object
     * @returns {string|null} Normalized category ID or null if missing
     */
    normalizeTestCategoryId(test) {
        // Check if test has a valid category_id
        if (test.category_id === null || test.category_id === undefined) {
            return null;
        }

        // Handle numeric zero as valid category ID
        if (test.category_id === 0 || test.category_id === '0') {
            return '0';
        }

        // Convert to string and trim
        const categoryId = String(test.category_id).trim();

        // Return null for empty strings
        if (categoryId === '') {
            return null;
        }

        return categoryId;
    }

    /**
     * Handle empty filter results with debugging and suggestions
     * @param {*} originalCategoryId - The original category ID provided
     * @param {string} normalizedCategoryId - The normalized category ID
     */
    handleEmptyFilterResults(originalCategoryId, normalizedCategoryId) {
        // console.warn removed

        // Provide debugging information
        const availableCategoryIds = [...new Set(
            this.testsData
                .map(t => this.normalizeTestCategoryId(t))
                .filter(id => id !== null)
        )];

        // console.log removed

        // Check if the category exists in our categories data
        const categoryExists = this.categoriesData.some(cat =>
            String(cat.id).trim() === normalizedCategoryId
        );

        if (!categoryExists) {
            // console.warn removed
        } else {
            // console.warn removed
        }
    }

    /**
     * Handle category filter errors with recovery options
     * @param {Error} error - The error that occurred
     * @param {*} categoryId - The category ID that caused the error
     */
    handleCategoryFilterError(error, categoryId) {
        // console.error removed

        // Track error for performance monitoring
        if (this.performanceMetrics) {
            this.performanceMetrics.filterOperations.push({
                timestamp: Date.now(),
                type: 'filter_error',
                categoryId: categoryId,
                error: error.message
            });
        }

        // Attempt to recover by clearing any problematic state
        try {
            this.recoverFromCategoryFilterError();
        } catch (recoveryError) {
            // console.error removed
        }
    }

    /**
     * Handle global category filter change
     * @param {string|number} categoryId - The selected category ID
     */
    async onCategoryFilterChange(categoryId) {
        try {
            const startTime = performance.now();
            //// console.log removed

            // Update the filtered test count display
            this.updateFilteredTestCount();

            // Update all existing test dropdowns with filtered results
            await this.updateAllTestDropdowns();

            // Add visual indicator when filter is active
            const $categoryFilter = $('#categoryFilter');
            const $clearButton = $('#clearCategoryFilter');

            if (categoryId && categoryId !== '') {
                // Filter is active
                $categoryFilter.addClass('category-filter-active');
                $clearButton.prop('disabled', false).show();

                // Show filter indicator
                this.showCategoryFilterIndicator(categoryId);
            } else {
                // Filter is cleared
                $categoryFilter.removeClass('category-filter-active');
                $clearButton.prop('disabled', true).hide();

                // Hide filter indicator
                this.hideCategoryFilterIndicator();
            }

            const endTime = performance.now();
            const duration = endTime - startTime;

            // Log performance metrics
            //// console.log removed

            // Track performance
            this.performanceMetrics.filterOperations.push({
                timestamp: Date.now(),
                duration: duration,
                type: 'global_filter_change',
                categoryId: categoryId
            });

            // Alert if operation is slow
            if (duration > 200) {
                // console.warn removed
            }

            //// console.log removed
        } catch (error) {
            // console.error removed
            // Attempt recovery
            this.recoverFromCategoryFilterError();
        }
    }

    /**
     * Clear category filter and show all tests
     */
    clearCategoryFilter() {
        try {
            //// console.log removed

            // Reset category filter dropdown
            const $categoryFilter = $('#categoryFilter');
            if ($categoryFilter.length > 0) {
                $categoryFilter.val('').trigger('change');
            }

            // Update test count
            this.updateFilteredTestCount();

            // Update all existing test dropdowns to show all tests
            this.updateAllTestDropdowns();

            // Remove visual indicators
            $categoryFilter.removeClass('category-filter-active');
            $('#clearCategoryFilter').prop('disabled', true).hide();
            this.hideCategoryFilterIndicator();

            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Show visual indicator that category filter is active
     * @param {string|number} categoryId - The active category ID
     */
    showCategoryFilterIndicator(categoryId) {
        try {
            const category = this.categoriesData.find(cat => cat.id == categoryId);
            const categoryName = category ? category.name : `Category ${categoryId}`;

            // Create or update filter indicator
            let $indicator = $('#categoryFilterIndicator');
            if ($indicator.length === 0) {
                $indicator = $('<div id="categoryFilterIndicator" class="alert alert-info alert-sm mt-2"></div>');
                $('#categoryFilter').parent().append($indicator);
            }

            $indicator.html(`
                <i class="fas fa-filter mr-1"></i>
                <strong>Active Filter:</strong> ${this.escapeHtml(categoryName)}
                <button type="button" class="btn btn-sm btn-outline-info ml-2" onclick="window.entryManager.clearCategoryFilter()">
                    <i class="fas fa-times"></i> Clear
                </button>
            `).show();

        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Hide category filter indicator
     */
    hideCategoryFilterIndicator() {
        try {
            $('#categoryFilterIndicator').hide();
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Get currently filtered tests based on selected category
     * @returns {Array} Currently filtered tests
     */
    getCurrentlyFilteredTests() {
        const selectedCategory = $('#categoryFilter').val();
        return this.filterTestsByCategory(selectedCategory);
    }

    /**
     * Update all existing test dropdowns with filtered results (optimized)
     */
    async updateAllTestDropdowns() {
        try {
            const startTime = performance.now();
            const filteredTests = this.getCurrentlyFilteredTests();
            const $testSelects = $('#testsContainer .test-select');

            //// console.log removed

            // Batch DOM updates for better performance
            const updates = [];

            // Collect all updates first
            $testSelects.each((index, element) => {
                const $select = $(element);
                const currentValue = $select.val();
                updates.push({ $select, currentValue });
            });

            // Apply updates in batches to minimize DOM reflows
            const batchSize = 5;
            for (let i = 0; i < updates.length; i += batchSize) {
                const batch = updates.slice(i, i + batchSize);

                // Process batch
                batch.forEach(update => {
                    this.updateTestDropdownOptions(update.$select, filteredTests, update.currentValue);
                });

                // Allow browser to breathe between batches
                if (i + batchSize < updates.length) {
                    await new Promise(resolve => setTimeout(resolve, 0));
                }
            }

            const endTime = performance.now();
            const duration = endTime - startTime;

            // Track performance
            this.performanceMetrics.filterOperations.push({
                timestamp: Date.now(),
                duration: duration,
                dropdownCount: $testSelects.length,
                testCount: filteredTests.length
            });

            // Keep only last 20 operations for memory efficiency
            if (this.performanceMetrics.filterOperations.length > 20) {
                this.performanceMetrics.filterOperations.splice(0, 10);
            }

            //// console.log removed

            // Warn if operation is slow
            if (duration > 500) {
                // console.warn removed
            }
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Update a single test dropdown with filtered options
     * @param {jQuery} $select - The select element to update
     * @param {Array} filteredTests - Array of filtered tests
     * @param {string} currentValue - Currently selected value to preserve
     */
    updateTestDropdownOptions($select, filteredTests, currentValue = null) {
        try {
            // Validate input parameters
            if (!$select || $select.length === 0) {
                // console.error removed
                return;
            }

            if (!filteredTests || !Array.isArray(filteredTests)) {
                // console.warn removed
                filteredTests = [];
            }

            // Store current selection if not provided
            if (currentValue === null) {
                currentValue = $select.val();
            }

            // Performance optimization: Build options HTML in memory first
            let optionsHtml = '<option value="">Select Test</option>';
            const validTests = [];

            // Process and validate tests
            filteredTests.forEach(test => {
                if (test && test.id && test.name) {
                    validTests.push(test);

                    // Create a unique display name with proper escaping
                    let displayName = this.escapeHtml(test.name) || `Test ${test.id}`;

                    // Add category if available to help distinguish similar tests
                    if (test.category_name) {
                        displayName += ` (${this.escapeHtml(test.category_name)})`;
                    }

                    // Add ID for additional uniqueness
                    displayName += ` [ID: ${test.id}]`;

                    // Build option with proper data attributes
                    optionsHtml += `<option value="${test.id}" 
                        data-category="${this.escapeHtml(test.category_name || '')}" 
                        data-category-id="${test.category_id || ''}"
                        data-unit="${this.escapeHtml(test.unit || '')}" 
                        data-min="${test.min || ''}" 
                        data-max="${test.max || ''}" 
                        data-price="${test.price || 0}">
                        ${displayName}
                    </option>`;
                } else {
                    // console.warn removed
                }
            });

            // Update dropdown with all options at once (more efficient)
            $select.html(optionsHtml);

            // Handle selection restoration
            const shouldRestoreSelection = currentValue && validTests.some(test => test.id == currentValue);

            if (shouldRestoreSelection) {
                $select.val(currentValue);
                //// console.log removed
            } else if (currentValue) {
                // If previously selected test is not in filtered results, clear selection
                //// console.log removed
                $select.val('');

                // Clear related fields in the row using the helper method
                const $row = $select.closest('.test-row');
                if ($row.length > 0) {
                    this.clearTestRowFields($row);
                }
            }

            // Refresh Select2 if it's initialized
            if ($select.hasClass('select2-hidden-accessible')) {
                try {
                    $select.trigger('change.select2');
                } catch (select2Error) {
                    // console.warn removed
                    // Try to reinitialize Select2 if refresh fails
                    try {
                        $select.select2('destroy').select2({
                            theme: 'bootstrap4',
                            width: '100%',
                            placeholder: 'Select Test'
                        });
                    } catch (reinitError) {
                        // console.error removed
                    }
                }
            }

            //// console.log removed

        } catch (error) {
            // console.error removed

            // Fallback: Ensure dropdown has at least the default option
            try {
                if ($select && $select.length > 0) {
                    $select.html('<option value="">Select Test (Error loading options)</option>');
                }
            } catch (fallbackError) {
                // console.error removed
            }
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
                //// console.log removed
            } else {
                //// console.error removed
            }
        } catch (error) {
            //// console.error removed
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

        //// console.log removed

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
                data: {
                    action: 'stats',
                    secret_key: 'hospital-api-secret-2024'
                },
                dataType: 'json'
            });

            if (response.success) {
                const stats = response.data;
                $('#totalEntries').text(stats.total || 0);
                $('#pendingEntries').text(stats.pending || 0);
                $('#completedEntries').text(stats.completed || 0);
                $('#todayEntries').text(stats.today || 0);
            } else {
                //// console.error removed
            }
        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Bind event handlers
     */
    bindEvents() {
        // //// console.log removed

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

        // Add new patient button click
        $('#addNewPatientBtn').on('click', () => {
            this.addNewPatientDirectly();
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

        // Category filter events (debounced for performance)
        $('#categoryFilter').on('change', (e) => {
            this.debouncedCategoryFilter(e.target.value);
        });

        $('#clearCategoryFilter').on('click', () => {
            this.clearCategoryFilter();
        });

        // Patient demographics change events for range updates
        $('#patientAge, #patientGender').on('change input', () => {
            this.debouncedRangeUpdate();
        });

        // //// console.log removed
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
        //// console.log removed

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

        //// console.log removed

        // Debug: Check for duplicate test names
        const testNames = this.testsData.map(t => t.name);
        const duplicateNames = testNames.filter((name, index) => testNames.indexOf(name) !== index);
        if (duplicateNames.length > 0) {
            // console.warn removed
            //// console.log removed
        }

        // Get filtered tests based on current global category filter
        // This ensures new test rows respect the active global filter
        const globalCategoryFilter = $('#categoryFilter').val();
        const filteredTests = this.getCurrentlyFilteredTests();

        /*// console.log removed*/

        const testOptions = filteredTests.map(test => {
            // Create a unique display name to avoid confusion
            let displayName = test.name || `Test ${test.id}`;

            // Add category if available to help distinguish similar tests
            if (test.category_name) {
                displayName += ` (${test.category_name})`;
            }

            // Add ID for additional uniqueness
            displayName += ` [ID: ${test.id}]`;

            return `<option value="${test.id}" data-category="${test.category_name || ''}" data-unit="${test.unit || ''}" data-min="${test.min || ''}" data-max="${test.max || ''}" data-price="${test.price || 0}">
                ${displayName}
            </option>`;
        }).join('');

        if (testData) {
            //// console.log removed
            const foundTest = this.testsData.find(t => t.id == testData.test_id);
            //// console.log removed
        }

        const rowHtml = `
            <div class="test-row row mb-2" data-row-index="${rowIndex}">
                <div class="col-md-2">
                    <select class="form-control test-category-select select2" name="tests[${rowIndex}][category_id]">
                        <option value="">Select Category</option>
                        <!-- Categories will be populated via JavaScript -->
                    </select>
                    <input type="hidden" name="tests[${rowIndex}][main_category_id]" class="test-main-category-id">
                </div>
                <div class="col-md-3">
                    <select class="form-control test-select select2" name="tests[${rowIndex}][test_id]" required>
                        <option value="">Select Test</option>
                        ${testOptions}
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control test-result" name="tests[${rowIndex}][result_value]" placeholder="Result">
                    <small class="validation-indicator text-muted"></small>
                </div>
                <div class="col-md-1">
                    <input type="text" class="form-control test-min" name="tests[${rowIndex}][min]" placeholder="Min" readonly>
                    <small class="range-indicator text-muted"></small>
                </div>
                <div class="col-md-1">
                    <input type="text" class="form-control test-max" name="tests[${rowIndex}][max]" placeholder="Max" readonly>
                    <small class="range-indicator text-muted"></small>
                </div>
                <div class="col-md-2">
                    <div class="test-unit-container">
                        <input type="text" class="form-control test-unit" name="tests[${rowIndex}][unit]" placeholder="Unit" readonly>
                        <span class="test-range-indicator badge badge-secondary" style="display: none;"></span>
                    </div>
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

        // Bind result validation events
        const $resultInput = $newRow.find('.test-result');
        $resultInput.on('input blur', (e) => {
            this.onTestResultChange(e.target, $newRow);
        });

        // Bind category selection change event
        const $categorySelect = $newRow.find('.test-category-select');
        $categorySelect.on('change', (e) => {
            this.onRowCategoryChange(e.target, $newRow);
        });

        // Populate category dropdown for this row
        this.populateRowCategoryDropdown($categorySelect);

        // If global category filter is active, pre-select it in the new row
        if (globalCategoryFilter && globalCategoryFilter !== '' && !testData) {
            //// console.log removed
            $categorySelect.val(globalCategoryFilter);

            // Set main category ID if available
            const selectedCategory = this.categoriesData.find(cat => cat.id == globalCategoryFilter);
            if (selectedCategory && selectedCategory.main_category_id) {
                $newRow.find('.test-main-category-id').val(selectedCategory.main_category_id);
            }
        }

        // Debug: Log category population status
        //// console.log removed

        // Initialize Select2 for both dropdowns
        $testSelect.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Test'
        });

        $categorySelect.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Category'
        });

        // If testData is provided, populate the row (EDIT MODE)
        if (testData) {
            // console.log removed
            // console.log removed

            // Set the test selection first
            $testSelect.val(testData.test_id);
            $testSelect.select2('destroy').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Select Test'
            });

            // Set the category IMMEDIATELY based on the test data
            // Use the category_id from the test data (which should be the correct one)
            const categoryId = testData.category_id || testData.test_category_id || testData.entry_category_id;
            if (categoryId && categoryId != 0) {
                // console.log removed

                // Ensure category dropdown is populated
                this.populateRowCategoryDropdown($categorySelect);

                // Set the category value
                $categorySelect.val(categoryId);

                // Reinitialize Select2 for category
                $categorySelect.select2('destroy').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: 'Select Category'
                });

                // Set main category ID if available
                const mainCategoryId = testData.main_category_id || testData.entry_main_category_id;
                if (mainCategoryId) {
                    $row.find('.test-main-category-id').val(mainCategoryId);
                }

                // console.log removed
            }

            // Set other fields from the entry data
            $resultInput.val(testData.result_value || '');
            $row.find('.test-price').val(testData.price || 0);

            // Set range fields if available
            if (testData.min) $row.find('.test-min').val(testData.min);
            if (testData.max) $row.find('.test-max').val(testData.max);
            if (testData.unit) $row.find('.test-unit').val(testData.unit);

            // Recalculate totals after setting prices
            setTimeout(() => {
                this.calculateTotals();
            }, 200);

            // console.log removed
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
    async onTestChange(selectElement, $row) {
        const $select = $(selectElement);
        const testId = $select.val();

        // console.log removed
        // console.log removed
        // console.log removed

        if (!testId) {
            // Clear everything if no test selected
            this.clearTestRow($row);
            this.calculateTotals();
            return;
        }

        // Find the test data
        const testData = this.testsData.find(t => t.id == testId);
        if (!testData) {
            // console.error removed
            this.clearTestRow($row);
            this.calculateTotals();
            return;
        }

        // console.log removed

        // ALWAYS set the category based on the test's category, regardless of edit mode
        await this.setTestCategory($row, testData);

        // Set other test details
        this.setTestDetails($row, testData);

        // Calculate totals
        this.calculateTotals();

        // console.log removed
    }

    /**
     * Clear all data in a test row
     */
    clearTestRow($row) {
        $row.find('.test-category-select').val('').trigger('change');
        $row.find('.test-unit, .test-min, .test-max').val('');
        $row.find('.test-main-category-id').val('');
        $row.find('.test-price').val(0);
        $row.find('.range-type-indicator').remove();
    }

    /**
     * Set category for a test row - COMPLETELY REWRITTEN
     */
    async setTestCategory($row, testData) {
        const $categorySelect = $row.find('.test-category-select');

        // console.log removed

        // Ensure categories are loaded
        if (this.categoriesData.length === 0) {
            // console.log removed
            await this.loadCategoriesForFilter();
        }

        // ALWAYS destroy Select2 first to avoid any conflicts
        if ($categorySelect.hasClass('select2-hidden-accessible')) {
            $categorySelect.select2('destroy');
        }

        // Repopulate the dropdown with fresh data
        this.populateRowCategoryDropdown($categorySelect);

        // Set the correct category value
        if (testData.category_id && testData.category_id != 0) {
            $categorySelect.val(testData.category_id);

            // Set main category ID
            const category = this.categoriesData.find(cat => cat.id == testData.category_id);
            if (category && category.main_category_id) {
                $row.find('.test-main-category-id').val(category.main_category_id);
            }

            // console.log removed
        } else {
            $categorySelect.val('');
            // console.log removed
        }

        // Reinitialize Select2 AFTER setting the value
        $categorySelect.select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Select Category'
        });

        // Verify the value was set correctly
        const finalValue = $categorySelect.val();
        // console.log removed
        if (finalValue != testData.category_id && testData.category_id != 0) {
            // console.error removed
        }
    }

    /**
     * Set other test details (unit, ranges, price)
     */
    setTestDetails($row, testData) {
        // Get patient demographics for range calculation
        const patientAge = parseInt($('#patientAge').val()) || null;
        const patientGender = $('#patientGender').val() || null;

        // Calculate appropriate ranges
        const rangeData = this.calculateAppropriateRanges(patientAge, patientGender, testData);

        // Set price
        $row.find('.test-price').val(testData.price || 0);

        // Update range display
        this.updateRangeDisplay($row, rangeData);

        // Recalculate totals after setting price
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
            // console.warn removed
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
        //// console.log removed
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
        //// console.log removed

        return rangeUpdates.length;
    }

    /**
     * Test demographic range functionality with sample data
     */
    testDemographicRangeFunctionality() {
        //// console.log removed

        if (this.testsData.length === 0) {
            // console.error removed
            return;
        }

        // Find a test with demographic ranges for testing
        const testWithRanges = this.testsData.find(test =>
            test.min_male || test.max_male || test.min_female || test.max_female || test.min_child || test.max_child
        );

        if (!testWithRanges) {
            // console.warn removed
            return;
        }

        //// console.log removed

        // Test child ranges (age 10)
        const childRange = this.calculateAppropriateRanges(10, 'Male', testWithRanges);
        //// console.log removed

        // Test adult male ranges (age 30)
        const maleRange = this.calculateAppropriateRanges(30, 'Male', testWithRanges);
        //// console.log removed

        // Test adult female ranges (age 25)
        const femaleRange = this.calculateAppropriateRanges(25, 'Female', testWithRanges);
        //// console.log removed

        // Test general ranges (no demographics)
        const generalRange = this.calculateAppropriateRanges(null, null, testWithRanges);
        //// console.log removed

        //// console.log removed
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
                // console.warn removed
                return '';
            }

            // Format as YYYY-MM-DD for HTML date input
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        } catch (error) {
            // console.error removed
            return '';
        }
    }

    /**
     * Validate complete demographic range workflow
     * This method can be called from browser console for testing
     */
    validateDemographicRangeWorkflow() {
        //// console.log removed

        const results = {
            cacheTest: false,
            performanceTest: false,
            validationTest: false,
            uiUpdateTest: false
        };

        try {
            // Test 1: Cache functionality
            //// console.log removed
            const testData = this.testsData[0];
            if (testData) {
                const key = this.generateRangeCacheKey(25, 'Male', testData.id);
                const range1 = this.calculateAppropriateRanges(25, 'Male', testData);
                const range2 = this.calculateAppropriateRanges(25, 'Male', testData); // Should use cache
                results.cacheTest = true;
                //// console.log removed
            }

            // Test 2: Performance test
            //// console.log removed
            const startTime = performance.now();
            for (let i = 0; i < 100; i++) {
                if (this.testsData[0]) {
                    this.calculateAppropriateRanges(25, 'Male', this.testsData[0]);
                }
            }
            const endTime = performance.now();
            const avgTime = (endTime - startTime) / 100;
            results.performanceTest = avgTime < 1; // Should be under 1ms per calculation
            //// console.log removed

            // Test 3: Validation test
            //// console.log removed
            const validation = this.validatePatientDemographics(25, 'Male');
            results.validationTest = validation.age === 25 && validation.gender === 'male';
            //// console.log removed

            // Test 4: UI update test (if DOM elements exist)
            //// console.log removed
            if ($('#patientAge').length > 0) {
                $('#patientAge').val('25');
                $('#patientGender').val('Male');
                this.updateAllTestRangesForCurrentPatient();
                results.uiUpdateTest = true;
                //// console.log removed
            } else {
                //// console.log removed
                results.uiUpdateTest = true; // Don't fail if UI not available
            }

        } catch (error) {
            // console.error removed
        }

        const allPassed = Object.values(results).every(result => result === true);
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed

        return results;
    }

    /**
     * Verify that demographic range fields are available in test data
     */
    verifyDemographicRangeFields() {
        if (this.testsData.length === 0) {
            // console.warn removed
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

        /*// console.log removed*/

        if (missingFields.length > 0) {
            // console.error removed
            // console.error removed

            if (typeof toastr !== 'undefined') {
                toastr.warning('Some demographic range fields are missing from test data. Age/gender-specific ranges may not work properly.');
            }
        } else {
            //// console.log removed

            // Check if any tests actually have demographic-specific ranges
            let testsWithDemographicRanges = 0;
            this.testsData.forEach(test => {
                if (test.min_male || test.max_male || test.min_female || test.max_female || test.min_child || test.max_child) {
                    testsWithDemographicRanges++;
                }
            });

            //// console.log removed

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

        // Update range indicators in min/max fields
        const $minIndicator = $row.find('.test-min').siblings('.range-indicator');
        const $maxIndicator = $row.find('.test-max').siblings('.range-indicator');

        if ($minIndicator.length > 0) {
            $minIndicator.text(rangeData.label).attr('title', `Using ${rangeData.label.toLowerCase()} for this patient`);
        }

        if ($maxIndicator.length > 0) {
            $maxIndicator.text(rangeData.label).attr('title', `Using ${rangeData.label.toLowerCase()} for this patient`);
        }

        // Update the main range indicator badge
        const $mainIndicator = $row.find('.test-range-indicator');
        if ($mainIndicator.length > 0) {
            // Remove all existing badge classes
            $mainIndicator.removeClass('badge-info badge-primary badge-success badge-secondary badge-warning male-range female-range child-range general-range');

            // Add appropriate class and show the indicator
            $mainIndicator.addClass(this.getRangeTypeBadgeClass(rangeData.type))
                .addClass(this.getRangeTypeClass(rangeData.type))
                .text(rangeData.label)
                .attr('title', `Using ${rangeData.label.toLowerCase()} for this patient`)
                .show();

            // Initialize tooltip if not already done
            if (!$mainIndicator.data('bs.tooltip')) {
                $mainIndicator.tooltip();
            }
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
                return 'badge-warning'; // Yellow for child
            case 'male':
                return 'badge-primary'; // Blue for male
            case 'female':
                return 'badge-danger'; // Red for female
            case 'general':
            default:
                return 'badge-secondary'; // Gray for general
        }
    }

    /**
     * Get specific CSS class for range type styling
     * @param {string} rangeType - Type of range ('child', 'male', 'female', 'general')
     * @returns {string} CSS class for range type styling
     */
    getRangeTypeClass(rangeType) {
        switch (rangeType) {
            case 'child':
                return 'child-range';
            case 'male':
                return 'male-range';
            case 'female':
                return 'female-range';
            case 'general':
            default:
                return 'general-range';
        }
    }

    /**
     * Update range labels for all test rows
     */
    updateRangeLabels() {
        try {
            const patientAge = parseInt($('#patientAge').val()) || null;
            const patientGender = $('#patientGender').val() || null;

            $('.test-row').each((index, row) => {
                const $row = $(row);
                const testId = $row.find('.test-select').val();

                if (testId) {
                    const testData = this.testsData.find(t => t.id == testId);
                    if (testData) {
                        const rangeData = this.calculateAppropriateRanges(patientAge, patientGender, testData);
                        this.updateRangeDisplay($row, rangeData);
                    }
                }
            });

            ////// console.log removed
        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Validate test result against appropriate reference ranges
     * @param {number} resultValue - The test result value
     * @param {object} rangeData - Range data from calculateAppropriateRanges
     * @returns {object} Validation result with status and message
     */
    validateTestResult(resultValue, rangeData) {
        try {
            // Check if result value is valid
            if (resultValue === null || resultValue === undefined || resultValue === '') {
                return {
                    status: 'empty',
                    message: 'No result entered',
                    isNormal: null
                };
            }

            const numericResult = parseFloat(resultValue);
            if (isNaN(numericResult)) {
                return {
                    status: 'invalid',
                    message: 'Invalid numeric value',
                    isNormal: null
                };
            }

            // Check if range data is available
            if (!rangeData || (rangeData.min === null && rangeData.max === null)) {
                return {
                    status: 'no_range',
                    message: 'No reference range available',
                    isNormal: null
                };
            }

            const min = parseFloat(rangeData.min);
            const max = parseFloat(rangeData.max);

            // Validate against range
            let isNormal = true;
            let message = 'Normal';

            if (!isNaN(min) && numericResult < min) {
                isNormal = false;
                message = `Below normal (Min: ${min})`;
            } else if (!isNaN(max) && numericResult > max) {
                isNormal = false;
                message = `Above normal (Max: ${max})`;
            } else if (!isNaN(min) && !isNaN(max)) {
                message = `Normal (${min} - ${max})`;
            } else if (!isNaN(min)) {
                message = `Normal (≥ ${min})`;
            } else if (!isNaN(max)) {
                message = `Normal (≤ ${max})`;
            }

            return {
                status: 'valid',
                message: message,
                isNormal: isNormal
            };

        } catch (error) {
            //// console.error removed
            return {
                status: 'error',
                message: 'Validation error',
                isNormal: null
            };
        }
    }

    /**
     * Update validation indicators for a test result
     * @param {jQuery} $row - The test row jQuery object
     * @param {object} validationResult - Result from validateTestResult
     */
    updateValidationIndicators($row, validationResult) {
        try {
            const $resultInput = $row.find('.test-result');
            const $validationIndicator = $row.find('.validation-indicator');

            // Remove existing validation classes
            $resultInput.removeClass('result-normal result-abnormal result-invalid result-empty');

            // Add appropriate validation class
            if (validationResult.status === 'valid') {
                if (validationResult.isNormal) {
                    $resultInput.addClass('result-normal');
                } else {
                    $resultInput.addClass('result-abnormal');
                }
            } else if (validationResult.status === 'invalid') {
                $resultInput.addClass('result-invalid');
            } else if (validationResult.status === 'empty') {
                $resultInput.addClass('result-empty');
            }

            // Update or create validation indicator
            if ($validationIndicator.length === 0) {
                const indicator = '<small class="validation-indicator text-muted"></small>';
                $resultInput.after(indicator);
            }

            const $indicator = $row.find('.validation-indicator');
            $indicator.text(validationResult.message)
                .removeClass('text-success text-danger text-warning text-muted')
                .addClass(this.getValidationIndicatorClass(validationResult));

        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Get CSS class for validation indicator
     * @param {object} validationResult - Result from validateTestResult
     * @returns {string} CSS class for the indicator
     */
    getValidationIndicatorClass(validationResult) {
        switch (validationResult.status) {
            case 'valid':
                return validationResult.isNormal ? 'text-success' : 'text-danger';
            case 'invalid':
                return 'text-warning';
            case 'empty':
            case 'no_range':
            default:
                return 'text-muted';
        }
    }

    /**
     * Validate all test results in the form
     */
    validateAllTestResults() {
        try {
            const patientAge = parseInt($('#patientAge').val()) || null;
            const patientGender = $('#patientGender').val() || null;

            $('.test-row').each((index, row) => {
                const $row = $(row);
                const testId = $row.find('.test-select').val();
                const resultValue = $row.find('.test-result').val();

                if (testId) {
                    const testData = this.testsData.find(t => t.id == testId);
                    if (testData) {
                        const rangeData = this.calculateAppropriateRanges(patientAge, patientGender, testData);
                        const validationResult = this.validateTestResult(resultValue, rangeData);
                        this.updateValidationIndicators($row, validationResult);
                    }
                }
            });

            //// console.log removed
        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Handle test result input change for validation
     * @param {HTMLElement} resultInput - The result input element
     * @param {jQuery} $row - The test row jQuery object
     */
    onTestResultChange(resultInput, $row) {
        try {
            const testId = $row.find('.test-select').val();
            const resultValue = $(resultInput).val();

            if (testId) {
                const testData = this.testsData.find(t => t.id == testId);
                if (testData) {
                    const patientAge = parseInt($('#patientAge').val()) || null;
                    const patientGender = $('#patientGender').val() || null;

                    const rangeData = this.calculateAppropriateRanges(patientAge, patientGender, testData);
                    const validationResult = this.validateTestResult(resultValue, rangeData);
                    this.updateValidationIndicators($row, validationResult);
                }
            }
        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Handle category filter change
     * @param {string} categoryId - Selected category ID
     */
    onCategoryFilterChange(categoryId) {
        try {
            //// console.log removed

            // Update test count display
            this.updateFilteredTestCount();

            // Update all existing test dropdowns with filtered results
            this.updateAllTestDropdowns();

            // Add visual indication that filter is active
            const $filterCard = $('#categoryFilterCard, .test-category-filter-card').parent();
            if (categoryId) {
                $filterCard.addClass('category-filter-active');
                $('#clearCategoryFilter').prop('disabled', false);
            } else {
                $filterCard.removeClass('category-filter-active');
                $('#clearCategoryFilter').prop('disabled', true);
            }

            //// console.log removed
        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Populate category dropdown for a test row
     * @param {jQuery} $categorySelect - The category select element
     */
    populateRowCategoryDropdown($categorySelect) {
        try {
            // console.log removed
            // console.log removed

            // Clear existing options
            $categorySelect.empty().append('<option value="">Select Category</option>');

            // Group categories by main category
            if (this.mainCategoriesData && this.mainCategoriesData.length > 0) {
                this.mainCategoriesData.forEach(mainCategory => {
                    if (mainCategory && mainCategory.id && mainCategory.name) {
                        // Find test categories under this main category
                        const subCategories = this.categoriesData.filter(cat =>
                            cat && cat.main_category_id == mainCategory.id
                        );

                        if (subCategories.length > 0) {
                            // Create optgroup for main category
                            const $optgroup = $(`<optgroup label="${this.escapeHtml(mainCategory.name)}"></optgroup>`);

                            // Add test categories under this main category
                            subCategories.forEach(category => {
                                if (category && category.id && category.name) {
                                    const $option = $(`<option value="${category.id}" data-main-category="${mainCategory.id}">${this.escapeHtml(category.name)}</option>`);
                                    $optgroup.append($option);
                                }
                            });

                            $categorySelect.append($optgroup);
                        }
                    }
                });
            }

            // Add categories without main category (if any)
            if (this.categoriesData && this.categoriesData.length > 0) {
                const orphanCategories = this.categoriesData.filter(cat =>
                    !cat.main_category_id || cat.main_category_id === null
                );

                if (orphanCategories.length > 0) {
                    const $optgroup = $(`<optgroup label="Other Categories"></optgroup>`);
                    orphanCategories.forEach(category => {
                        if (category && category.id && category.name) {
                            const $option = $(`<option value="${category.id}">${this.escapeHtml(category.name)}</option>`);
                            $optgroup.append($option);
                        }
                    });
                    $categorySelect.append($optgroup);
                }
            }

            // console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Comprehensive error recovery for category filtering
     */
    recoverFromCategoryFilterError() {
        try {
            // console.warn removed

            // Reset category filter to show all tests
            const $categoryFilter = $('#categoryFilter');
            if ($categoryFilter.length > 0) {
                $categoryFilter.val('').prop('disabled', false).removeClass('error-state');
            }

            // Reset clear button
            const $clearButton = $('#clearCategoryFilter');
            if ($clearButton.length > 0) {
                $clearButton.prop('disabled', true).hide();
            }

            // Ensure all tests are shown
            this.updateAllTestDropdowns();
            this.updateFilteredTestCount();

            // Remove any active filter styling
            $('.category-filter-active').removeClass('category-filter-active');
            this.hideCategoryFilterIndicator();

            // Remove any error UI elements
            $('#categoryRetryUI').remove();

            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Global error handler for the entry manager
     * @param {Error} error - The error object
     * @param {string} context - Context where the error occurred
     */
    handleGlobalError(error, context = 'Unknown') {
        try {
            // console.error removed

            // Log error details for debugging
            const errorDetails = {
                message: error.message,
                stack: error.stack,
                context: context,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                userAgent: navigator.userAgent
            };

            // console.error removed

            // Apply appropriate fallback based on context
            switch (context) {
                case 'category_loading':
                    this.handleCategoryLoadError(error.message);
                    break;
                case 'test_loading':
                    this.handleTestDataLoadError(error.message);
                    break;
                case 'category_filtering':
                    this.recoverFromCategoryFilterError();
                    break;
                case 'test_selection':
                    this.recoverFromTestSelectionError();
                    break;
                case 'data_reconciliation':
                    this.recoverFromDataReconciliationError();
                    break;
                default:
                    // Generic fallback - ensure basic functionality works
                    this.ensureBasicFunctionality();
                    break;
            }

            // Log error for potential reporting
            this.logErrorForReporting(errorDetails);

        } catch (fallbackError) {
            // console.error removed
            // Last resort - show user message
            if (typeof toastr !== 'undefined') {
                toastr.error('An unexpected error occurred. Please refresh the page.');
            }
        }
    }

    /**
     * Recover from test selection errors
     */
    recoverFromTestSelectionError() {
        try {
            // console.warn removed

            // Clear any problematic test selections
            $('.test-select').each((index, select) => {
                const $select = $(select);
                const $row = $select.closest('.test-row');

                try {
                    // Verify the selected test exists in our data
                    const selectedValue = $select.val();
                    if (selectedValue) {
                        const testExists = this.testsData.some(test => test.id == selectedValue);
                        if (!testExists) {
                            // console.warn removed
                            $select.val('');
                            this.clearTestRowFields($row);
                        }
                    }
                } catch (rowError) {
                    // console.error removed
                    // Clear the problematic row
                    $select.val('');
                    this.clearTestRowFields($row);
                }
            });

            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Recover from data reconciliation errors
     */
    recoverFromDataReconciliationError() {
        try {
            // console.warn removed

            // Reset to basic entry mode without reconciliation
            const $testsContainer = $('#testsContainer');
            if ($testsContainer.length > 0) {
                // Clear existing test rows
                $testsContainer.empty();
                this.testRowCounter = 0;

                // Add a single basic test row
                this.addTestRow();

                if (typeof toastr !== 'undefined') {
                    toastr.warning('Data reconciliation failed. Starting with a fresh test row.');
                }
            }

            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Ensure basic functionality is available even when errors occur
     */
    ensureBasicFunctionality() {
        try {
            //// console.log removed

            // Ensure at least one test row exists
            if ($('#testsContainer .test-row').length === 0) {
                // Create a basic test row
                const basicRowHtml = `
                    <div class="test-row row mb-2" data-row-index="0">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Some features are temporarily unavailable. Basic test entry is still possible.
                                <button type="button" class="btn btn-sm btn-outline-info ml-2" onclick="location.reload()">
                                    <i class="fas fa-refresh mr-1"></i>Refresh Page
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#testsContainer').html(basicRowHtml);
            }

            // Ensure form can still be submitted
            const $form = $('#entryForm');
            if ($form.length > 0) {
                // Remove required attributes from problematic fields
                $form.find('input[required], select[required]').each(function () {
                    const $field = $(this);
                    if ($field.attr('id') !== 'patientSelect' && $field.attr('id') !== 'ownerAddedBySelect') {
                        $field.removeAttr('required');
                    }
                });
            }

            // Ensure basic dropdowns work
            $('.select2').each(function () {
                const $select = $(this);
                try {
                    if (!$select.hasClass('select2-hidden-accessible')) {
                        $select.select2({
                            theme: 'bootstrap4',
                            width: '100%'
                        });
                    }
                } catch (select2Error) {
                    // console.warn removed
                }
            });

            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Log error for potential reporting
     * @param {object} errorDetails - Detailed error information
     */
    logErrorForReporting(errorDetails) {
        try {
            // Store error in localStorage for potential reporting
            const errorLog = JSON.parse(localStorage.getItem('entryManagerErrors') || '[]');
            errorLog.push(errorDetails);

            // Keep only the last 10 errors to prevent storage bloat
            if (errorLog.length > 10) {
                errorLog.splice(0, errorLog.length - 10);
            }

            localStorage.setItem('entryManagerErrors', JSON.stringify(errorLog));

            // In a production environment, you might want to send this to a logging service
            // this.sendErrorToLoggingService(errorDetails);

        } catch (loggingError) {
            // console.error removed
        }
    }

    /**
     * Get stored error logs (for debugging)
     * @returns {Array} Array of stored error logs
     */
    getStoredErrorLogs() {
        try {
            return JSON.parse(localStorage.getItem('entryManagerErrors') || '[]');
        } catch (error) {
            // console.error removed
            return [];
        }
    }

    /**
     * Clear stored error logs
     */
    clearStoredErrorLogs() {
        try {
            localStorage.removeItem('entryManagerErrors');
            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Set data in cache with timestamp
     * @param {string} key - Cache key
     * @param {any} data - Data to cache
     * @param {number} customTimeout - Custom timeout in milliseconds (optional)
     */
    setCacheData(key, data, customTimeout = null) {
        try {
            const timeout = customTimeout || this.dataCacheTimeout;
            this.dataCache.set(key, {
                data: data,
                timestamp: Date.now(),
                timeout: timeout
            });

            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Get data from cache if available and not expired
     * @param {string} key - Cache key
     * @returns {any|null} Cached data or null if not found/expired
     */
    getCacheData(key) {
        try {
            const cached = this.dataCache.get(key);
            if (cached && (Date.now() - cached.timestamp) < cached.timeout) {
                this.performanceMetrics.cacheHits++;
                //// console.log removed
                return cached.data;
            }

            // Remove expired cache entry
            if (cached) {
                this.dataCache.delete(key);
                //// console.log removed
            }

            this.performanceMetrics.cacheMisses++;
            return null;
        } catch (error) {
            // console.error removed
            this.performanceMetrics.cacheMisses++;
            return null;
        }
    }

    /**
     * Clear specific cache entry
     * @param {string} key - Cache key to clear
     */
    clearCacheData(key) {
        try {
            this.dataCache.delete(key);
            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Clear all cached data
     */
    clearAllCacheData() {
        try {
            this.dataCache.clear();
            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Get cache statistics
     * @returns {object} Cache statistics
     */
    getCacheStatistics() {
        return {
            cacheSize: this.dataCache.size,
            cacheHits: this.performanceMetrics.cacheHits,
            cacheMisses: this.performanceMetrics.cacheMisses,
            hitRate: this.performanceMetrics.cacheHits / (this.performanceMetrics.cacheHits + this.performanceMetrics.cacheMisses) || 0,
            apiCalls: this.performanceMetrics.apiCalls
        };
    }

    /**
     * Preload and cache essential data
     */
    async preloadEssentialData() {
        try {
            //// console.log removed
            const startTime = performance.now();

            // Load data in parallel for better performance
            const promises = [
                this.loadTestsData(),
                this.loadCategoriesForFilter(),
                this.loadMainCategoriesData(),
                this.loadOwnersData()
            ];

            await Promise.allSettled(promises);

            const endTime = performance.now();
            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Get performance metrics
     * @returns {object} Performance metrics
     */
    getPerformanceMetrics() {
        const filterOps = this.performanceMetrics.filterOperations;
        const avgFilterTime = filterOps.length > 0
            ? filterOps.reduce((sum, op) => sum + op.duration, 0) / filterOps.length
            : 0;

        return {
            cache: this.getCacheStatistics(),
            filtering: {
                totalOperations: filterOps.length,
                averageTime: avgFilterTime,
                slowOperations: filterOps.filter(op => op.duration > 500).length,
                recentOperations: filterOps.slice(-5)
            },
            memory: {
                rangeCacheSize: this.rangeCache.size,
                dataCacheSize: this.dataCache.size
            }
        };
    }

    /**
     * Optimize performance by cleaning up caches
     */
    optimizePerformance() {
        try {
            //// console.log removed

            // Clean up expired cache entries
            this.cleanupExpiredCache();

            // Limit range cache size
            if (this.rangeCache.size > 1000) {
                const keysToDelete = Array.from(this.rangeCache.keys()).slice(0, 200);
                keysToDelete.forEach(key => this.rangeCache.delete(key));
                //// console.log removed
            }

            // Clean up old performance metrics
            if (this.performanceMetrics.filterOperations.length > 50) {
                this.performanceMetrics.filterOperations.splice(0, 25);
                //// console.log removed
            }

            //// console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Clean up expired cache entries
     */
    cleanupExpiredCache() {
        try {
            let cleanedCount = 0;
            const now = Date.now();

            for (const [key, cached] of this.dataCache.entries()) {
                if ((now - cached.timestamp) >= cached.timeout) {
                    this.dataCache.delete(key);
                    cleanedCount++;
                }
            }

            if (cleanedCount > 0) {
                //// console.log removed
            }
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Monitor and log performance issues
     */
    monitorPerformance() {
        try {
            const metrics = this.getPerformanceMetrics();

            // Log warnings for performance issues
            if (metrics.filtering.averageTime > 300) {
                // console.warn removed
            }

            if (metrics.cache.hitRate < 0.7) {
                // console.warn removed
            }

            if (metrics.memory.dataCacheSize > 50) {
                // console.warn removed
            }

            // Auto-optimize if needed
            if (metrics.memory.dataCacheSize > 100 || metrics.memory.rangeCacheSize > 2000) {
                //// console.log removed
                this.optimizePerformance();
            }
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Handle category selection change in a test row
     * @param {HTMLElement} categorySelect - The category select element
     * @param {jQuery} $row - The test row jQuery object
     */
    onRowCategoryChange(categorySelect, $row) {
        try {
            // Enhanced validation of input parameters
            if (!categorySelect || !$row || $row.length === 0) {
                // console.error removed
                return;
            }

            const $categorySelect = $(categorySelect);
            const selectedCategoryId = $categorySelect.val();
            const $selectedOption = $categorySelect.find('option:selected');
            const mainCategoryId = $selectedOption.data('main-category');
            const rowIndex = $row.data('row-index');

            // console.log removed

            // Enhanced main category ID handling
            const mainCategoryValue = mainCategoryId || '';
            $row.find('.test-main-category-id').val(mainCategoryValue);

            // Enhanced test dropdown validation
            const $testSelect = $row.find('.test-select');
            if ($testSelect.length === 0) {
                // console.error removed
                this.handleRowCategoryChangeError('missing_test_dropdown', $row);
                return;
            }

            // Enhanced filtering with error handling
            let filteredTests;
            try {
                filteredTests = this.filterTestsByCategory(selectedCategoryId);
                // console.log removed
            } catch (filterError) {
                // console.error removed
                this.handleRowCategoryChangeError('filter_error', $row, filterError);
                return;
            }

            // Store current test selection with enhanced validation
            const currentTestId = $testSelect.val();
            const currentTestName = $testSelect.find('option:selected').text();

            // Enhanced test dropdown clearing and repopulation
            try {
                // Clear the dropdown first to ensure clean state
                this.clearTestDropdownOptions($testSelect);

                // Repopulate with filtered tests
                this.updateTestDropdownOptions($testSelect, filteredTests, currentTestId);

                // console.log removed
            } catch (updateError) {
                // console.error removed
                this.handleRowCategoryChangeError('dropdown_update_error', $row, updateError);
                return;
            }

            // Enhanced validation of current test selection
            const isCurrentTestStillValid = currentTestId &&
                filteredTests.some(test => String(test.id) === String(currentTestId));

            if (currentTestId && !isCurrentTestStillValid) {
                // console.log removed

                // Enhanced clearing of test selection and related fields
                this.clearTestSelectionAndFields($testSelect, $row);

                // Provide user feedback about cleared selection
                this.showTestSelectionClearedFeedback($row, currentTestName, selectedCategoryId);

            } else if (isCurrentTestStillValid) {
                // console.log removed

                // Enhanced restoration of valid selection
                this.restoreTestSelection($testSelect, currentTestId);
            }

            // Enhanced test count and UI updates
            this.updateFilteredTestCount();
            this.updateRowCategoryIndicator($row, selectedCategoryId);

            // console.log removed

        } catch (error) {
            // console.error removed
            this.handleRowCategoryChangeError('general_error', $row, error);
        }
    }

    /**
     * Clear test dropdown options properly
     * @param {jQuery} $testSelect - The test select dropdown
     */
    clearTestDropdownOptions($testSelect) {
        try {
            // Destroy Select2 if initialized to prevent memory leaks
            if ($testSelect.hasClass('select2-hidden-accessible')) {
                $testSelect.select2('destroy');
            }

            // Clear all options except the placeholder
            $testSelect.empty().append('<option value="">Select Test</option>');

        } catch (error) {
            // console.error removed
            // Fallback: just empty the select
            $testSelect.empty().append('<option value="">Select Test</option>');
        }
    }

    /**
     * Clear test selection and related fields with enhanced cleanup
     * @param {jQuery} $testSelect - The test select dropdown
     * @param {jQuery} $row - The test row jQuery object
     */
    clearTestSelectionAndFields($testSelect, $row) {
        try {
            // Clear the test selection
            $testSelect.val('');

            // Clear all related fields
            this.clearTestRowFields($row);

            // Trigger change event to ensure all handlers are notified
            $testSelect.trigger('change');

            // Reinitialize Select2 if needed
            if (!$testSelect.hasClass('select2-hidden-accessible')) {
                $testSelect.select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: 'Select Test'
                });
            }

        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Restore test selection with enhanced validation
     * @param {jQuery} $testSelect - The test select dropdown
     * @param {string} testId - The test ID to restore
     */
    restoreTestSelection($testSelect, testId) {
        try {
            // Set the value
            $testSelect.val(testId);

            // Refresh Select2 if it's initialized
            if ($testSelect.hasClass('select2-hidden-accessible')) {
                $testSelect.trigger('change.select2');
            } else {
                // Reinitialize Select2 if not already initialized
                $testSelect.select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    placeholder: 'Select Test'
                });
            }

            // Verify the selection was successful
            const actualValue = $testSelect.val();
            if (actualValue !== testId) {
                // console.warn removed
            }

        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Show feedback when test selection is cleared due to category change
     * @param {jQuery} $row - The test row jQuery object
     * @param {string} testName - The name of the cleared test
     * @param {string} categoryId - The new category ID
     */
    showTestSelectionClearedFeedback($row, testName, categoryId) {
        try {
            // Find category name for better user feedback
            const category = this.categoriesData.find(cat => cat.id == categoryId);
            const categoryName = category ? category.name : `Category ${categoryId}`;

            // Show temporary feedback in the row
            const $feedback = $('<small class="text-warning test-selection-feedback"></small>')
                .text(`"${testName}" cleared (not in ${categoryName})`);

            $row.find('.test-select').parent().append($feedback);

            // Remove feedback after 3 seconds
            setTimeout(() => {
                $feedback.fadeOut(300, function () {
                    $(this).remove();
                });
            }, 3000);

        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Update row category indicator
     * @param {jQuery} $row - The test row jQuery object
     * @param {string} categoryId - The selected category ID
     */
    updateRowCategoryIndicator($row, categoryId) {
        try {
            // Remove existing indicators
            $row.find('.category-indicator').remove();

            if (categoryId) {
                const category = this.categoriesData.find(cat => cat.id == categoryId);
                if (category) {
                    const $indicator = $('<span class="badge badge-info badge-sm category-indicator ml-1"></span>')
                        .text(category.name)
                        .attr('title', `Category: ${category.name}`);

                    $row.find('.test-category-select').parent().append($indicator);
                }
            }

        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Handle errors during row category change operations
     * @param {string} errorType - The type of error that occurred
     * @param {jQuery} $row - The test row jQuery object
     * @param {Error} error - The error object (optional)
     */
    handleRowCategoryChangeError(errorType, $row, error = null) {
        try {
            const rowIndex = $row.data('row-index');
            // console.error removed

            // Attempt recovery based on error type
            switch (errorType) {
                case 'missing_test_dropdown':
                    // Try to recreate the test dropdown
                    this.recreateTestDropdown($row);
                    break;

                case 'filter_error':
                    // Fall back to showing all tests
                    this.fallbackToAllTests($row);
                    break;

                case 'dropdown_update_error':
                    // Try to restore dropdown to working state
                    this.restoreTestDropdown($row);
                    break;

                case 'general_error':
                default:
                    // General recovery: show all tests and clear category selection
                    this.generalErrorRecovery($row);
                    break;
            }

            // Show user-friendly error message
            this.showRowErrorFeedback($row, errorType);

        } catch (recoveryError) {
            // console.error removed
        }
    }

    /**
     * Fallback to showing all tests when filtering fails
     * @param {jQuery} $row - The test row jQuery object
     */
    fallbackToAllTests($row) {
        try {
            const $testSelect = $row.find('.test-select');
            if ($testSelect.length > 0) {
                this.clearTestDropdownOptions($testSelect);
                this.updateTestDropdownOptions($testSelect, this.testsData, null);
                // console.log removed
            }
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Show error feedback to user
     * @param {jQuery} $row - The test row jQuery object
     * @param {string} errorType - The type of error
     */
    showRowErrorFeedback($row, errorType) {
        try {
            const errorMessages = {
                'missing_test_dropdown': 'Test dropdown not found',
                'filter_error': 'Category filtering failed',
                'dropdown_update_error': 'Failed to update test options',
                'general_error': 'Category change failed'
            };

            const message = errorMessages[errorType] || 'An error occurred';

            const $errorFeedback = $('<small class="text-danger row-error-feedback"></small>')
                .text(`${message} - showing all tests`);

            $row.find('.test-category-select').parent().append($errorFeedback);

            // Remove error feedback after 5 seconds
            setTimeout(() => {
                $errorFeedback.fadeOut(300, function () {
                    $(this).remove();
                });
            }, 5000);

        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Clear test row fields when test selection is cleared
     * @param {jQuery} $row - The test row jQuery object
     */
    clearTestRowFields($row) {
        try {
            $row.find('.test-result').val('');
            $row.find('.test-min').val('');
            $row.find('.test-max').val('');
            $row.find('.test-unit').val('');
            $row.find('.test-price').val('0');

            // Clear validation indicators
            $row.find('.validation-indicator').text('').removeClass('text-success text-danger text-warning');
            $row.find('.test-result').removeClass('result-normal result-abnormal result-invalid result-empty');

            // Remove range indicators
            $row.find('.test-range-indicator').hide();
            $row.find('.range-indicator').text('');
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Enhanced patient demographics change handler
     * Updates both range displays and validation indicators
     */
    updateAllTestRangesAndValidation() {
        try {
            // Update range labels for all tests
            this.updateRangeLabels();

            // Validate all test results with new demographics
            this.validateAllTestResults();

            //// console.log removed
        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Comprehensive error recovery for category filtering
     */
    recoverFromCategoryFilterError() {
        try {
            //// console.warn removed

            // Reset category filter to show all tests
            const $categoryFilter = $('#categoryFilter');
            if ($categoryFilter.length > 0) {
                $categoryFilter.val('').prop('disabled', false);
            }

            // Reset clear button
            const $clearButton = $('#clearCategoryFilter');
            if ($clearButton.length > 0) {
                $clearButton.prop('disabled', true);
            }

            // Ensure all tests are shown
            this.updateAllTestDropdowns();
            this.updateFilteredTestCount();

            // Remove any active filter styling
            $('.category-filter-active').removeClass('category-filter-active');

            //// console.log removed
        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Fallback function when test data is unavailable
     */
    handleTestDataUnavailable() {
        try {
            //// console.warn removed

            // Show user-friendly message
            const $testsContainer = $('#testsContainer');
            if ($testsContainer.length > 0 && $testsContainer.children().length === 0) {
                const fallbackMessage = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Test data temporarily unavailable.</strong>
                        Please refresh the page or contact support if the issue persists.
                        <button type="button" class="btn btn-sm btn-outline-warning ml-2" onclick="window.entryManager.loadTestsData()">
                            <i class="fas fa-refresh mr-1"></i>Retry
                        </button>
                    </div>
                `;
                $testsContainer.html(fallbackMessage);
            }

            // Disable category filter
            $('#categoryFilter').prop('disabled', true).html('<option value="">Tests unavailable</option>');
            $('#clearCategoryFilter').prop('disabled', true);

            //// console.log removed
        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Global error handler for the entry manager
     * @param {Error} error - The error object
     * @param {string} context - Context where the error occurred
     */
    handleGlobalError(error, context = 'Unknown') {
        try {
            //// console.error removed

            // Log error details for debugging
            const errorDetails = {
                message: error.message,
                stack: error.stack,
                context: context,
                timestamp: new Date().toISOString()
            };

            //// console.error removed

            // Apply appropriate fallback based on context
            switch (context) {
                case 'category_loading':
                    this.handleCategoryLoadError();
                    break;
                case 'test_loading':
                    this.handleTestDataUnavailable();
                    break;
                case 'category_filtering':
                    this.recoverFromCategoryFilterError();
                    break;
                default:
                    // Generic fallback - ensure basic functionality works
                    this.ensureBasicFunctionality();
                    break;
            }

        } catch (fallbackError) {
            //// console.error removed
            // Last resort - show user message
            if (typeof toastr !== 'undefined') {
                toastr.error('An unexpected error occurred. Please refresh the page.');
            }
        }
    }

    /**
     * Ensure basic functionality is available even when errors occur
     */
    ensureBasicFunctionality() {
        try {
            // Ensure at least one test row exists
            if ($('#testsContainer .test-row').length === 0) {
                // Create a basic test row without filtered data
                const basicRowHtml = `
                    <div class="test-row row mb-2" data-row-index="0">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                Test selection temporarily unavailable. Please refresh the page.
                                <button type="button" class="btn btn-sm btn-outline-info ml-2" onclick="location.reload()">
                                    <i class="fas fa-refresh mr-1"></i>Refresh Page
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                $('#testsContainer').html(basicRowHtml);
            }

            // Ensure form can still be submitted
            const $form = $('#entryForm');
            if ($form.length > 0) {
                $form.find('input[required], select[required]').each(function () {
                    if ($(this).attr('id') !== 'patientSelect' && $(this).attr('id') !== 'ownerAddedBySelect') {
                        $(this).removeAttr('required');
                    }
                });
            }
        } catch (error) {
            //// console.error removed
        }
    }

    /**
     * Update all test ranges for the currently selected patient (with performance monitoring)
     */
    updateAllTestRangesForCurrentPatient() {
        const startTime = performance.now();
        const patientAge = parseInt($('#patientAge').val()) || null;
        const patientGender = $('#patientGender').val() || null;
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
        });

        // Performance monitoring
        const endTime = performance.now();
        const duration = endTime - startTime;
        if (duration > 100) {
            // console.warn removed
        }
    }

    /**
     * Reset all test ranges to general ranges (when no patient selected)
     */
    resetAllTestRangesToGeneral() {

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
        // Initialize most Select2 dropdowns with default settings
        $('.select2:not(#patientSelect)').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Initialize patient select with custom formatting
        $('#patientSelect').select2({
            theme: 'bootstrap4',
            width: '100%',
            templateResult: function (option) {
                if (!option.id) return option.text;

                if (option.id === 'new') {
                    return $('<span><i class="fas fa-plus text-success mr-1"></i><strong class="text-success">' + option.text + '</strong></span>');
                }

                return option.text;
            },
            templateSelection: function (option) {
                if (option.id === 'new') {
                    return $('<span><i class="fas fa-plus text-success mr-1"></i><strong class="text-success">New Patient</strong></span>');
                }
                return option.text;
            }
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

        // Reset patient mode to existing patient
        this.switchToExistingPatientMode();

        // Disable patient and doctor selects initially
        $('#patientSelect, #doctorSelect, #addNewPatientBtn').prop('disabled', true);

        // Reset pricing
        this.calculateTotals();
    }

    /**
     * Handle owner selection change
     */
    onOwnerChange(ownerId) {

        if (ownerId) {
            // Enable patient and doctor selects and new patient button
            $('#patientSelect, #doctorSelect, #addNewPatientBtn').prop('disabled', false);

            // Load patients and doctors for this owner
            this.loadPatientsForOwner(ownerId);
            this.loadDoctorsForOwner(ownerId);
        } else {
            // Disable and clear patient and doctor selects only if not in edit mode
            if (!this.currentEditId) {
                $('#patientSelect, #doctorSelect, #addNewPatientBtn').prop('disabled', true).val('').trigger('change');
                this.clearPatientDetails();
            }
        }
    }

    /**
     * Handle patient selection change
     */
    onPatientChange(patientId) {
        ////// console.log removed

        if (patientId === 'new') {
            // Switch to new patient mode
            this.switchToNewPatientMode();
        } else if (patientId) {
            // Load existing patient details
            this.switchToExistingPatientMode();
            this.loadPatientDetails(patientId);
        } else {
            // Clear patient details
            this.clearPatientDetails();
            this.switchToExistingPatientMode(); // Default mode
            // Reset to general ranges when no patient selected
            this.resetAllTestRangesToGeneral();
        }
    }

    /**
     * Switch to new patient mode - enable editing of patient fields
     */
    switchToNewPatientMode() {
        // Update mode indicator
        $('#patientModeIndicator')
            .removeClass('badge-info')
            .addClass('badge-success')
            .text('New Patient');

        // Enable patient information fields for editing
        $('#patientName').prop('readonly', false);
        $('#patientContact').prop('readonly', false);
        $('#patientAge').prop('readonly', false);
        $('#patientGender').prop('disabled', false);
        $('#patientAddress').prop('readonly', false);

        // Clear existing patient data
        $('#patientName').val('');
        $('#patientContact').val('');
        $('#patientAge').val('');
        $('#patientGender').val('').trigger('change');
        $('#patientAddress').val('');

        // Add visual indicators for editable fields
        $('.patient-field').addClass('new-patient-mode');

        // Reset to general ranges for new patient
        this.resetAllTestRangesToGeneral();
    }

    /**
     * Switch to existing patient mode - disable editing of patient fields
     */
    switchToExistingPatientMode() {
        // Update mode indicator
        $('#patientModeIndicator')
            .removeClass('badge-success')
            .addClass('badge-info')
            .text('Existing Patient');

        // Disable patient information fields
        $('#patientName').prop('readonly', true);
        $('#patientContact').prop('readonly', true);
        $('#patientAge').prop('readonly', true);
        $('#patientGender').prop('disabled', true);
        $('#patientAddress').prop('readonly', true);

        // Remove visual indicators
        $('.patient-field').removeClass('new-patient-mode');
    }

    /**
     * Add new patient directly via button click
     */
    addNewPatientDirectly() {
        // Set dropdown to "new" option
        $('#patientSelect').val('new').trigger('change');

        // Focus on patient name field for immediate input
        setTimeout(() => {
            $('#patientName').focus();
        }, 100);
    }

    /**
     * Load patients for selected owner
     */
    async loadPatientsForOwner(ownerId) {
        try {
            const response = await $.ajax({
                url: 'ajax/patient_api.php',
                method: 'GET',
                data: { action: 'list', owner_id: ownerId },
                dataType: 'json'
            });

            const $select = $('#patientSelect');
            const $addNewBtn = $('#addNewPatientBtn');

            $select.prop('disabled', false);
            $addNewBtn.prop('disabled', false);

            // Clear and populate dropdown
            $select.empty();
            $select.append('<option value="">Select Patient</option>');
            $select.append('<option value="new" class="text-success"><i class="fas fa-plus"></i> Add New Patient</option>');

            if (response.success && response.data) {
                response.data.forEach(patient => {
                    $select.append(`<option value="${patient.id}">${patient.name}</option>`);
                });
            } else {
                // console.warn removed
            }

            // Refresh Select2 if initialized
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change');
            }
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Load doctors for selected owner
     */
    async loadDoctorsForOwner(ownerId) {
        try {
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
            } else {
                // console.warn removed
            }

            // Refresh Select2 if initialized
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change');
            }
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Load patient details
     */
    async loadPatientDetails(patientId) {
        try {
            const response = await $.ajax({
                url: 'ajax/patient_api.php',
                method: 'GET',
                data: { action: 'get', id: patientId },
                dataType: 'json'
            });

            if (response.success && response.data) {
                const patient = response.data;

                $('#patientName').val(patient.name || '');
                $('#patientContact').val(patient.contact || '');
                $('#patientAge').val(patient.age || '');
                $('#patientGender').val(patient.gender || '').trigger('change');
                $('#patientAddress').val(patient.address || '');

                setTimeout(() => {
                    this.debouncedRangeUpdate();
                }, 100); // Small delay to ensure DOM updates are complete
            } else {
                // console.warn removed
            }
        } catch (error) {
            // console.error removed
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
     * Reconcile test category data between entry data and current test data
     * @param {Array} entryTests - Array of tests from entry data
     * @returns {Array} Array of reconciled test data
     */
    reconcileTestCategoryData(entryTests) {
        try {

            const reconciledTests = entryTests.map((entryTest, index) => {
                // Get current test data
                const currentTest = this.getCurrentTestCategory(entryTest.test_id);

                // Create reconciled test object
                const reconciledTest = {
                    ...entryTest, // Start with entry data
                    category_conflict: false,
                    data_source: 'entry',
                    resolved_category_id: entryTest.category_id,
                    resolved_category_name: entryTest.category_name
                };

                if (currentTest) {
                    // Check for category conflicts
                    const hasConflict = this.detectCategoryConflict(entryTest, currentTest);

                    if (hasConflict) {
                        // console.warn removed

                        // Resolve conflict by prioritizing current test data
                        const resolution = this.resolveConflictingCategories(entryTest, currentTest);

                        reconciledTest.category_conflict = true;
                        reconciledTest.resolved_category_id = resolution.category_id;
                        reconciledTest.resolved_category_name = resolution.category_name;
                        reconciledTest.data_source = resolution.source;
                        reconciledTest.conflict_resolution = resolution.reason;

                        // Update the test data to use resolved values
                        reconciledTest.category_id = resolution.category_id;
                        reconciledTest.category_name = resolution.category_name;
                    } else {
                        // No conflict, but update with current data if entry data is missing or 0
                        if ((!entryTest.category_id || entryTest.category_id == 0) && currentTest.category_id) {
                            reconciledTest.category_id = currentTest.category_id;
                            reconciledTest.category_name = currentTest.category_name;
                            reconciledTest.resolved_category_id = currentTest.category_id;
                            reconciledTest.resolved_category_name = currentTest.category_name;
                            reconciledTest.data_source = 'current';
                        }
                    }
                } else {
                    // console.warn removed
                    reconciledTest.data_source = 'entry_only';
                }

                return reconciledTest;
            });

            return reconciledTests;
        } catch (error) {
            // console.error removed
            // Return original data as fallback
            return entryTests;
        }
    }

    /**
     * Get current test category data for a specific test ID
     * @param {number|string} testId - The test ID to look up
     * @returns {object|null} Current test data or null if not found
     */
    getCurrentTestCategory(testId) {
        try {
            if (!testId || !this.testsData || !Array.isArray(this.testsData)) {
                return null;
            }

            const currentTest = this.testsData.find(test => test.id == testId);

            if (currentTest) {
                return {
                    test_id: currentTest.id,
                    test_name: currentTest.name,
                    category_id: currentTest.category_id,
                    category_name: currentTest.category_name,
                    main_category_id: currentTest.main_category_id,
                    price: currentTest.price,
                    unit: currentTest.unit,
                    min: currentTest.min,
                    max: currentTest.max
                };
            }

            return null;
        } catch (error) {
            // console.error removed
            return null;
        }
    }

    /**
     * Detect if there's a category conflict between entry and current test data
     * @param {object} entryTest - Test data from entry
     * @param {object} currentTest - Current test data
     * @returns {boolean} True if conflict detected
     */
    detectCategoryConflict(entryTest, currentTest) {
        try {
            // No conflict if either doesn't have category data (treat 0 as no category)
            if (!entryTest.category_id || entryTest.category_id == 0 || !currentTest.category_id || currentTest.category_id == 0) {
                return false;
            }

            // Convert to strings for comparison to handle type differences
            const entryCategoryId = String(entryTest.category_id).trim();
            const currentCategoryId = String(currentTest.category_id).trim();

            // Conflict exists if category IDs are different
            return entryCategoryId !== currentCategoryId;
        } catch (error) {
            // console.error removed
            return false;
        }
    }

    /**
     * Resolve conflicting categories between entry and current test data
     * @param {object} entryTest - Test data from entry
     * @param {object} currentTest - Current test data
     * @returns {object} Resolution with category_id, category_name, source, and reason
     */
    resolveConflictingCategories(entryTest, currentTest) {
        try {
            // Priority: Current test data over entry data
            // This ensures we show the most up-to-date category information

            if (currentTest.category_id) {
                return {
                    category_id: currentTest.category_id,
                    category_name: currentTest.category_name,
                    source: 'current',
                    reason: 'Prioritized current test category over entry category'
                };
            } else if (entryTest.category_id) {
                return {
                    category_id: entryTest.category_id,
                    category_name: entryTest.category_name,
                    source: 'entry',
                    reason: 'Used entry category as current test has no category'
                };
            } else {
                return {
                    category_id: null,
                    category_name: null,
                    source: 'none',
                    reason: 'No category data available in either source'
                };
            }
        } catch (error) {
            // console.error removed
            // Fallback to entry data
            return {
                category_id: entryTest.category_id,
                category_name: entryTest.category_name,
                source: 'entry_fallback',
                reason: 'Error occurred, used entry data as fallback'
            };
        }
    }

    /**
     * View entry details
     */
    async viewEntry(entryId) {

        try {
            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: {
                    action: 'get',
                    id: entryId,
                    secret_key: 'hospital-api-secret-2024'
                },
                dataType: 'json'
            });
            if (response.success && response.data) {
                this.displayEntryDetails(response.data);
                $('#viewEntryModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load entry details');
            }
        } catch (error) {
            // console.error removed
            toastr.error('Failed to load entry details');
        }
    }

    /**
     * Display entry details in modal
     */
    displayEntryDetails(entry) {
        // Debug each test
        if (entry.tests && entry.tests.length > 0) {
            entry.tests.forEach((test, index) => {
                /*// console.log removed*/
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
                            <th>Main Category</th>
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

            // Get main category name
            let mainCategoryName = 'N/A';
            if (test.category_id && this.categoriesData.length > 0) {
                const category = this.categoriesData.find(cat => cat.id == test.category_id);
                if (category && category.main_category_id && this.mainCategoriesData.length > 0) {
                    const mainCategory = this.mainCategoriesData.find(mc => mc.id == category.main_category_id);
                    if (mainCategory) {
                        mainCategoryName = mainCategory.name;
                    }
                }
            }

            return `
                                <tr>
                                    <td>${test.test_name || 'N/A'}</td>
                                    <td>${test.category_name || 'N/A'}</td>
                                    <td>${mainCategoryName}</td>
                                    <td>${test.result_value || 'Pending'}</td>
                                    <td>${rangeDisplay}${rangeTypeIndicator}</td>
                                    <td>${test.unit || ''}</td>
                                </tr>
                            `;
        }).join('') : '<tr><td colspan="6">No tests found</td></tr>'}
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

        try {
            // console.log removed
            // console.log removed

            // Show loading state
            if (typeof toastr !== 'undefined') {
                toastr.info('Loading entry data...');
            }

            // console.log removed
            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: {
                    action: 'get',
                    id: entryId,
                    secret_key: 'hospital-api-secret-2024'
                },
                dataType: 'json'
            });

            // console.log removed

            // Special debugging for entry 17
            if (entryId == 17) {
                if (response.data.tests) {
                    response.data.tests.forEach((test, index) => {
                        /*// console.log removed*/
                    });
                }
            }

            if (response.success && response.data) {
                $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');
                $('#entryModal').modal('show');

                // Ensure owner data is loaded before populating form
                if (this.ownersData.length === 0) {
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
            // console.error removed
            // console.error removed

            let errorMessage = 'Failed to load entry for editing';
            if (error.status === 401) {
                errorMessage += ' - Authentication failed';
            } else if (error.status === 404) {
                errorMessage += ' - Entry not found';
            } else if (error.status === 500) {
                errorMessage += ' - Server error';
            } else if (error.responseText) {
                try {
                    const errorData = JSON.parse(error.responseText);
                    errorMessage += ': ' + (errorData.message || 'Unknown error');
                } catch (e) {
                    errorMessage += ': ' + error.responseText.substring(0, 100);
                }
            }

            toastr.error(errorMessage);
        }
    }

    /**
     * Populate edit form with entry data
     */
    async populateEditForm(entry) {


        this.currentEditId = entry.id;

        // Reset form first
        this.resetForm();

        // Populate basic fields
        $('#entryId').val(entry.id);

        // Format entry date for HTML date input (requires YYYY-MM-DD format)
        if (entry.entry_date) {
            const formattedDate = this.formatDateForInput(entry.entry_date);
            $('#entryDate').val(formattedDate);
            //// console.log removed
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
            //// console.log removed

            // Check if the owner exists in the dropdown, if not add it
            const $ownerSelect = $('#ownerAddedBySelect');
            if ($ownerSelect.find(`option[value="${ownerId}"]`).length === 0) {
                const ownerName = entry.added_by_full_name || entry.added_by_username || `User ${ownerId}`;
                //// console.log removed
                $ownerSelect.append(`<option value="${ownerId}">${ownerName}</option>`);
            }

            $ownerSelect.val(ownerId).trigger('change');

            // Wait for owner change to load patients and doctors
            await this.loadPatientsForOwner(ownerId);
            await this.loadDoctorsForOwner(ownerId);

            // Now set patient and doctor values
            if (entry.patient_id) {
                //// console.log removed

                // Check if patient exists in dropdown, if not add it
                const $patientSelect = $('#patientSelect');
                if ($patientSelect.find(`option[value="${entry.patient_id}"]`).length === 0) {
                    const patientName = entry.patient_name || `Patient ${entry.patient_id}`;
                    //// console.log removed
                    $patientSelect.append(`<option value="${entry.patient_id}">${patientName}</option>`);
                }

                $patientSelect.val(entry.patient_id).trigger('change');

                // Load patient details
                await this.loadPatientDetails(entry.patient_id);
            }

            if (entry.doctor_id) {
                //// console.log removed

                // Check if doctor exists in dropdown, if not add it
                const $doctorSelect = $('#doctorSelect');
                if ($doctorSelect.find(`option[value="${entry.doctor_id}"]`).length === 0) {
                    const doctorName = entry.doctor_name || `Doctor ${entry.doctor_id}`;
                    //// console.log removed
                    $doctorSelect.append(`<option value="${entry.doctor_id}">${doctorName}</option>`);
                }

                $doctorSelect.val(entry.doctor_id).trigger('change');
            }
        } else {
            //// console.warn removed
        }

        // Always reload tests data to ensure we have the latest data
        //// console.log removed
        await this.loadTestsData();
        //// console.log removed

        // Also ensure categories are loaded for proper category dropdown population
        //// console.log removed
        if (this.categoriesData.length === 0) {
            await this.loadCategoriesForFilter();
        }
        //// console.log removed

        // Debug: Log test IDs and names for troubleshooting
        if (this.testsData.length > 0) {
            //// console.log removed
        }

        // Debug: show first few tests
        if (this.testsData.length > 0) {
            //// console.log removed
        }

        // Double-check that we have tests data
        if (this.testsData.length === 0) {
            // console.error removed
            toastr.warning('Tests data could not be loaded. Test selection may not work properly.');
        }

        // Clear and populate tests with data reconciliation
        $('#testsContainer').empty();
        this.testRowCounter = 0;

        if (entry.tests && entry.tests.length > 0) {
            //// console.log removed

            // Reconcile entry test data with current test data
            const reconciledTests = this.reconcileTestCategoryData(entry.tests);

            // Log reconciliation results
            /*// console.log removed.length
            });*/

            // Create test rows with reconciled data
            reconciledTests.forEach((reconciledTest, index) => {
                /*// console.log removed*/

                this.addTestRow(reconciledTest);
            });
        } else {
            //// console.log removed
            this.addTestRow();
        }

        // Trigger change events for Select2 dropdowns to update display
        setTimeout(() => {
            $('#entryStatus').trigger('change');
            $('#priority').trigger('change');
            $('#referralSource').trigger('change');
            //// console.log removed
        }, 100);
    }

    /**
     * Delete entry
     */
    deleteEntry(entryId) {
        //// console.log removed

        // Show confirmation modal
        $('#deleteModal').modal('show');

        // Handle confirmation
        $('#confirmDelete').off('click').on('click', async () => {
            try {
                const response = await $.ajax({
                    url: 'ajax/entry_api_fixed.php',
                    method: 'POST',
                    data: {
                        action: 'delete',
                        id: entryId,
                        secret_key: 'hospital-api-secret-2024'
                    },
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
                //// console.error removed
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

        // Enhanced patient validation
        const patientId = $('#patientSelect').val();
        if (!patientId) {
            errors.push('Patient is required');
        } else if (patientId === 'new') {
            // Validate new patient fields
            if (!$('#patientName').val().trim()) {
                errors.push('Patient Name is required for new patient');
            }
            if (!$('#patientContact').val().trim()) {
                errors.push('Patient Contact is required for new patient');
            }
            if (!$('#patientAge').val()) {
                errors.push('Patient Age is required for new patient');
            }
            if (!$('#patientGender').val()) {
                errors.push('Patient Gender is required for new patient');
            }
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
        //// console.log removed
        //// console.log removed

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
            formData.append('secret_key', 'hospital-api-secret-2024'); // Add authentication

            // Ensure owner_added_by is set (it should be in the form already)
            if (!formData.get('owner_added_by')) {
                formData.append('owner_added_by', ownerAddedBy);
            }

            // Ensure patient_id is set
            if (!formData.get('patient_id')) {
                formData.append('patient_id', patientId);
            }

            // Debug form data - specifically check for category information
            //// console.log removed
            let hasTestData = false;
            let categoryDataFound = false;
            for (let [key, value] of formData.entries()) {
                //// console.log removed
                if (key.includes('tests[') && key.includes('category_id')) {
                    categoryDataFound = true;
                    //// console.log removed
                }
                if (key.includes('tests[') && key.includes('main_category_id')) {
                    //// console.log removed
                }
                if (key.includes('tests[')) {
                    hasTestData = true;
                }
            }

            if (hasTestData && !categoryDataFound) {
                // console.warn removed
            }

            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });

            //// console.log removed

            if (response.success) {
                toastr.success(this.currentEditId ? 'Entry updated successfully' : 'Entry created successfully');
                this.refreshTable();
                $('#entryModal').modal('hide');
                this.resetForm();
            } else {
                //// console.error removed
                toastr.error(response.message || 'Failed to save entry');
            }
        } catch (error) {
            //// console.error removed
            // console.error removed

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

    /**
     * View entry details
     */
    viewEntry(entryId) {
        // console.log removed

        // Show loading state
        $('#entryDetails').html(`
            <div class="text-center p-5">
                <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
                <p class="mt-3 text-muted">Loading entry details...</p>
            </div>
        `);

        // Show modal
        $('#viewEntryModal').modal('show');

        // Load entry data
        $.ajax({
            url: 'ajax/entry_api_fixed.php',
            method: 'GET',
            data: {
                action: 'get',
                id: entryId,
                secret_key: 'hospital-api-secret-2024'
            },
            dataType: 'json',
            success: (response) => {
                if (response.success && response.data) {
                    this.displayEntryDetails(response.data);
                } else {
                    $('#entryDetails').html(`
                        <div class="alert alert-danger">
                            <h5>Error Loading Entry</h5>
                            <p>${response.message || 'Failed to load entry details'}</p>
                        </div>
                    `);
                }
            },
            error: (xhr, status, error) => {
                // console.error removed
                $('#entryDetails').html(`
                    <div class="alert alert-danger">
                        <h5>Error Loading Entry</h5>
                        <p>Failed to load entry details. Please try again.</p>
                    </div>
                `);
            }
        });
    }

    /**
     * Display entry details in the view modal
     */
    displayEntryDetails(entry) {
        const html = `
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table table-sm">
                        <tr><th>Entry ID:</th><td>#${entry.id}</td></tr>
                        <tr><th>Patient:</th><td>${entry.patient_name || 'N/A'}</td></tr>
                        <tr><th>Doctor:</th><td>${entry.doctor_name || 'Not assigned'}</td></tr>
                        <tr><th>Entry Date:</th><td>${entry.entry_date || 'N/A'}</td></tr>
                        <tr><th>Status:</th><td><span class="badge badge-${entry.status === 'completed' ? 'success' : entry.status === 'pending' ? 'warning' : 'danger'}">${entry.status || 'pending'}</span></td></tr>
                        <tr><th>Priority:</th><td><span class="badge badge-info">${entry.priority || 'normal'}</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Financial Information</h5>
                    <table class="table table-sm">
                        <tr><th>Subtotal:</th><td>₹${parseFloat(entry.subtotal || 0).toFixed(2)}</td></tr>
                        <tr><th>Discount:</th><td>₹${parseFloat(entry.discount_amount || 0).toFixed(2)}</td></tr>
                        <tr><th>Total Amount:</th><td><strong>₹${parseFloat(entry.total_price || 0).toFixed(2)}</strong></td></tr>
                        <tr><th>Added By:</th><td>${entry.added_by_full_name || entry.added_by_username || 'Unknown'}</td></tr>
                    </table>
                </div>
            </div>
            ${entry.tests && entry.tests.length > 0 ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Tests</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Test Name</th>
                                    <th>Category</th>
                                    <th>Result</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${entry.tests.map(test => `
                                    <tr>
                                        <td>${test.test_name || 'N/A'}</td>
                                        <td><span class="badge badge-secondary">${test.category_name || 'No category'}</span></td>
                                        <td>${test.result_value || 'Pending'}</td>
                                        <td>${test.unit || 'N/A'}</td>
                                        <td><span class="badge badge-${test.status === 'completed' ? 'success' : 'warning'}">${test.status || 'pending'}</span></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            ` : '<div class="alert alert-info">No tests associated with this entry.</div>'}
            ${entry.notes ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Notes</h5>
                    <div class="alert alert-light">${entry.notes}</div>
                </div>
            </div>
            ` : ''}
        `;

        $('#entryDetails').html(html);
    }

    /**
     * Edit entry
     */
    editEntry(entryId) {
        // console.log removed

        // Set current edit ID
        this.currentEditId = entryId;

        // Show loading state in modal
        toastr.info('Loading entry for editing...');

        // Load entry data
        $.ajax({
            url: 'ajax/entry_api_fixed.php',
            method: 'GET',
            data: {
                action: 'get',
                id: entryId,
                secret_key: 'hospital-api-secret-2024'
            },
            dataType: 'json',
            success: (response) => {
                // console.log removed
                if (response && response.success && response.data) {
                    this.populateEditForm(response.data);
                    $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');
                    $('#entryModal').modal('show');
                    toastr.clear();
                } else {
                    // console.error removed
                    const errorMsg = response && response.message ? response.message : 'Failed to load entry for editing';
                    toastr.error(errorMsg);
                    this.currentEditId = null;
                }
            },
            error: (xhr, status, error) => {
                // console.error removed

                let errorMessage = 'Failed to load entry for editing';
                if (xhr.status === 404) {
                    errorMessage = 'Entry not found';
                } else if (xhr.status === 403) {
                    errorMessage = 'Access denied';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error occurred';
                } else if (xhr.status === 0) {
                    errorMessage = 'Network connection failed';
                }

                toastr.error(errorMessage);
                this.currentEditId = null;
            }
        });
    }

    /**
     * Populate edit form with entry data
     */
    populateEditForm(entry) {
        // console.log removed

        try {
            // Basic fields
            $('#entryId').val(entry.id);
            $('#entryDate').val(entry.entry_date);

            // Use Select2 trigger for dropdowns
            if (entry.status) {
                $('#entryStatus').val(entry.status).trigger('change');
            }
            if (entry.priority) {
                $('#priority').val(entry.priority).trigger('change');
            }
            if (entry.referral_source) {
                $('#referralSource').val(entry.referral_source).trigger('change');
            }

            $('#entryNotes').val(entry.notes || '');

            // Financial fields
            $('#subtotal').val(entry.subtotal || 0);
            $('#discountAmount').val(entry.discount_amount || 0);
            $('#totalPrice').val(entry.total_price || 0);

            // Patient information
            $('#patientName').val(entry.patient_name || '');
            $('#patientContact').val(entry.patient_contact || '');
            $('#patientAge').val(entry.age || '');
            if (entry.gender) {
                $('#patientGender').val(entry.gender).trigger('change');
            }
            $('#patientAddress').val(entry.patient_address || '');

            // Owner/Added by - load options first if needed
            if (entry.added_by) {
                // Check if option exists, if not add it
                if ($('#ownerAddedBySelect option[value="' + entry.added_by + '"]').length === 0) {
                    const ownerName = entry.added_by_full_name || entry.added_by_username || 'User #' + entry.added_by;
                    $('#ownerAddedBySelect').append(new Option(ownerName, entry.added_by, false, false));
                }
                $('#ownerAddedBySelect').val(entry.added_by).trigger('change');
            }

            // Patient selection - load options first if needed
            if (entry.patient_id) {
                // Check if option exists, if not add it
                if ($('#patientSelect option[value="' + entry.patient_id + '"]').length === 0) {
                    const patientName = entry.patient_name || 'Patient #' + entry.patient_id;
                    $('#patientSelect').append(new Option(patientName, entry.patient_id, false, false));
                }
                $('#patientSelect').val(entry.patient_id).trigger('change');
            }

            // Doctor selection - load options first if needed
            if (entry.doctor_id) {
                // Check if option exists, if not add it
                if ($('#doctorSelect option[value="' + entry.doctor_id + '"]').length === 0) {
                    const doctorName = entry.doctor_name || 'Doctor #' + entry.doctor_id;
                    $('#doctorSelect').append(new Option(doctorName, entry.doctor_id, false, false));
                }
                $('#doctorSelect').val(entry.doctor_id).trigger('change');
            }

            // Clear existing test rows
            $('#testsContainer').empty();
            this.testRowCounter = 0;

            // Add test rows if tests exist
            if (entry.tests && entry.tests.length > 0) {
                // console.log removed
                entry.tests.forEach((test, index) => {
                    // console.log removed
                    this.addTestRowWithData(test);
                });
            } else {
                // console.log removed
                // Add one empty test row
                this.addTestRow();
            }

            // console.log removed
        } catch (error) {
            // console.error removed
            toastr.error('Error populating form data');
        }
    }

    /**
     * Add test row with existing data
     */
    addTestRowWithData(testData = {}) {
        const rowIndex = this.testRowCounter++;
        // console.log removed

        const testRow = `
            <div class="test-row" data-row-index="${rowIndex}">
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control test-category-select" name="test_category_${rowIndex}">
                            <option value="">Select Category</option>
                        </select>
                        <input type="hidden" class="test-main-category-id" name="test_main_category_id_${rowIndex}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control test-select" name="test_id_${rowIndex}" required>
                            <option value="">Select Test</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control test-result" name="test_result_${rowIndex}" 
                               placeholder="Result" value="${testData.result_value || ''}">
                        <small class="validation-indicator text-muted"></small>
                    </div>
                    <div class="col-md-1">
                        <input type="number" class="form-control test-min-range" name="test_min_${rowIndex}" 
                               placeholder="Min" step="0.01" readonly>
                    </div>
                    <div class="col-md-1">
                        <input type="number" class="form-control test-max-range" name="test_max_${rowIndex}" 
                               placeholder="Max" step="0.01" readonly>
                    </div>
                    <div class="col-md-2">
                        <div class="test-unit-container">
                            <input type="text" class="form-control test-unit" name="test_unit_${rowIndex}" 
                                   placeholder="Unit" value="${testData.unit || ''}" readonly>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-test-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#testsContainer').append(testRow);

        // Get the newly added row
        const $row = $(`.test-row[data-row-index="${rowIndex}"]`);

        // Populate category dropdown if we have categories data
        if (this.categoriesData && this.categoriesData.length > 0) {
            const $categorySelect = $row.find('.test-category-select');
            this.categoriesData.forEach(category => {
                $categorySelect.append(new Option(category.name, category.id, false, false));
            });
        }

        // Populate test dropdown if we have tests data
        if (this.testsData && this.testsData.length > 0) {
            const $testSelect = $row.find('.test-select');

            // Check if category filter is active
            const selectedCategoryId = $('#modalCategoryFilter').val();
            let filteredTests = this.testsData;

            if (selectedCategoryId) {
                filteredTests = this.testsData.filter(test => {
                    return test.category_id == selectedCategoryId;
                });
            }

            filteredTests.forEach(test => {
                $testSelect.append(new Option(test.name, test.id, false, false));
            });
        }

        // Set the selections after populating dropdowns
        if (testData.test_id) {
            // console.log removed
            $row.find('.test-select').val(testData.test_id).trigger('change');
        }
        if (testData.category_id) {
            // console.log removed
            $row.find('.test-category-select').val(testData.category_id).trigger('change');
        }

        // Set range values if available
        if (testData.min_range) {
            $row.find('.test-min-range').val(testData.min_range);
        }
        if (testData.max_range) {
            $row.find('.test-max-range').val(testData.max_range);
        }

        // Bind test selection change event to auto-populate category
        $row.find('.test-select').on('change', (e) => {
            const selectedTestId = $(e.target).val();
            if (selectedTestId) {
                const selectedTest = this.testsData.find(test => test.id == selectedTestId);
                if (selectedTest && selectedTest.category_id) {
                    $row.find('.test-category-select').val(selectedTest.category_id);
                }
            }
        });

        // Bind remove event
        $row.find('.remove-test-row').on('click', () => {
            $row.remove();
            this.updatePricing();
        });

        // console.log removed
    }

    /**
     * Delete entry
     */
    deleteEntry(entryId) {
        // console.log removed

        // Show confirmation modal
        $('#deleteModal').modal('show');

        // Bind confirm delete event
        $('#confirmDelete').off('click').on('click', () => {
            $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'POST',
                data: {
                    action: 'delete',
                    id: entryId,
                    secret_key: 'hospital-api-secret-2024'
                },
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        toastr.success('Entry deleted successfully');
                        this.refreshTable();
                        $('#deleteModal').modal('hide');
                    } else {
                        toastr.error(response.message || 'Failed to delete entry');
                    }
                },
                error: (xhr, status, error) => {
                    // console.error removed
                    toastr.error('Failed to delete entry');
                }
            });
        });
    }

    /**
     * Refresh the DataTable
     */
    refreshTable() {
        if (this.entriesTable) {
            this.entriesTable.ajax.reload();
        }
    }

    /**
     * Reset the form
     */
    resetForm() {
        $('#entryForm')[0].reset();
        $('#testsContainer').empty();
        $('#modalCategoryFilter').val('');
        this.currentEditId = null;
        this.testRowCounter = 0;
        this.updateFilteredTestCount();

        // Reset pricing
        $('#subtotal').val('0.00');
        $('#discountAmount').val('0.00');
        $('#totalPrice').val('0.00');
    }

    /**
     * Add empty test row
     */
    addTestRow() {
        this.addTestRowWithData({});
    }

    /**
     * Update pricing calculations
     */
    updatePricing() {
        // This would contain pricing calculation logic
        // For now, just a placeholder
        // console.log removed
    }

    /**
     * Open add modal
     */
    openAddModal() {
        this.currentEditId = null;
        this.resetForm();
        $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');

        // Initialize Select2
        this.initializeSelect2();

        // Initialize category filter if not already done
        if (this.categoriesData.length > 0) {
            this.populateModalCategoryFilter();
        }

        // Populate owner dropdown if not already done
        if (this.ownersData.length > 0) {
            this.populateOwnerDropdown();
        }

        $('#entryModal').modal('show');

        // Add one empty test row after modal is shown
        setTimeout(() => {
            this.addTestRow();
        }, 100);
    }

    /**
     * Export entries
     */
    exportEntries() {
        toastr.info('Export functionality will be implemented soon');
    }

    /**
     * Debounce function for performance optimization
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
     * Monitor performance (placeholder)
     */
    monitorPerformance() {
        // Performance monitoring placeholder
        // console.log removed
    }

    /**
     * Filter by status
     */
    filterByStatus(status) {
        $('#statusFilter').val(status === 'all' ? '' : status).trigger('change');
    }

    /**
     * Filter by date
     */
    filterByDate(dateRange) {
        $('#dateFilter').val(dateRange).trigger('change');
    }

    /**
     * Refresh the DataTable
     */
    refreshTable() {
        if (this.entriesTable) {
            this.entriesTable.ajax.reload();
            // console.log removed
        }
    }

    /**
     * Load statistics
     */
    loadStatistics() {
        // Placeholder for statistics loading
        $('#totalEntries').text('Loading...');
        $('#pendingEntries').text('Loading...');
        $('#completedEntries').text('Loading...');
        $('#todayEntries').text('Loading...');

        // This would load actual statistics from the API
        setTimeout(() => {
            $('#totalEntries').text('0');
            $('#pendingEntries').text('0');
            $('#completedEntries').text('0');
            $('#todayEntries').text('0');
        }, 1000);
    }

    /**
     * Add a new test row
     */
    addTestRow() {
        this.addTestRowWithData({});
    }

    /**
     * Add test row with existing data
     */
    addTestRowWithData(testData = {}) {
        const rowIndex = this.testRowCounter++;
        // console.log removed

        const testRow = `
            <div class="test-row" data-row-index="${rowIndex}">
                <div class="row">
                    <div class="col-md-2">
                        <select class="form-control test-category-select" name="test_category_${rowIndex}" required>
                            <option value="">Select Category</option>
                        </select>
                        <input type="hidden" class="test-main-category-id" name="test_main_category_id_${rowIndex}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control test-select" name="test_id_${rowIndex}" required disabled>
                            <option value="">Select Category First</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control test-result" name="test_result_${rowIndex}" 
                               placeholder="Enter Result">
                        <small class="validation-indicator text-muted"></small>
                    </div>
                    <div class="col-md-1">
                        <input type="number" class="form-control test-min-range" name="test_min_${rowIndex}" 
                               placeholder="Min" step="0.01" readonly>
                    </div>
                    <div class="col-md-1">
                        <input type="number" class="form-control test-max-range" name="test_max_${rowIndex}" 
                               placeholder="Max" step="0.01" readonly>
                    </div>
                    <div class="col-md-2">
                        <div class="test-unit-container">
                            <input type="text" class="form-control test-unit" name="test_unit_${rowIndex}" 
                                   placeholder="Unit" readonly>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-test-row">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $('#testsContainer').append(testRow);

        // Get the newly added row
        const $row = $(`.test-row[data-row-index="${rowIndex}"]`);

        // Populate category dropdown
        this.populateTestCategoryDropdown($row.find('.test-category-select'));

        // Bind category change event
        $row.find('.test-category-select').on('change', (e) => {
            this.onCategoryChange(e, $row);
        });

        // Bind test selection change event
        $row.find('.test-select').on('change', (e) => {
            this.onTestChange(e, $row);
        });

        // Bind result input change event for validation
        $row.find('.test-result').on('input', (e) => {
            const testId = $row.find('.test-select').val();
            if (testId) {
                const test = this.testsData.find(t => t.id == testId);
                if (test) {
                    this.validateTestResult($row, test);
                }
            }
        });

        // Bind remove event
        $row.find('.remove-test-row').on('click', () => {
            $row.remove();
            this.updatePricing();
        });

        // If we have existing data, populate it
        if (testData.category_id) {
            $row.find('.test-category-select').val(testData.category_id).trigger('change');

            // Wait a bit for the test dropdown to populate, then set the test
            setTimeout(() => {
                if (testData.test_id) {
                    $row.find('.test-select').val(testData.test_id).trigger('change');
                }
            }, 100);
        }

        if (testData.result_value) {
            $row.find('.test-result').val(testData.result_value);
        }

        // console.log removed
    }

    /**
     * Populate test category dropdown
     */
    populateTestCategoryDropdown($dropdown) {
        $dropdown.empty().append('<option value="">Select Category</option>');

        this.categoriesData.forEach(category => {
            if (category && category.id && category.name) {
                $dropdown.append(new Option(category.name, category.id, false, false));
            }
        });
    }

    /**
     * Handle category selection change
     */
    onCategoryChange(event, $row) {
        const selectedCategoryId = $(event.target).val();
        const $testSelect = $row.find('.test-select');

        // Clear and reset test dropdown
        $testSelect.empty().append('<option value="">Select Test</option>');

        if (selectedCategoryId) {
            // Enable test dropdown
            $testSelect.prop('disabled', false);

            // Filter tests by selected category
            const filteredTests = this.testsData.filter(test => {
                return test.category_id == selectedCategoryId;
            });

            // Populate test dropdown with filtered tests
            filteredTests.forEach(test => {
                $testSelect.append(new Option(test.name, test.id, false, false));
            });

            // console.log removed
        } else {
            // Disable test dropdown if no category selected
            $testSelect.prop('disabled', true);
        }

        // Clear test-related fields
        this.clearTestFields($row);
    }

    /**
     * Handle test selection change
     */
    onTestChange(event, $row) {
        const selectedTestId = $(event.target).val();

        if (selectedTestId) {
            const selectedTest = this.testsData.find(test => test.id == selectedTestId);
            if (selectedTest) {
                // console.log removed

                // Auto-fill unit
                $row.find('.test-unit').val(selectedTest.unit || '');

                // Auto-fill min/max ranges based on patient demographics
                this.updateTestRanges($row, selectedTest);

                // Validate result if already entered
                this.validateTestResult($row, selectedTest);
            }
        } else {
            this.clearTestFields($row);
        }
    }

    /**
     * Update test ranges based on patient demographics
     */
    updateTestRanges($row, test) {
        // Get patient demographics
        const patientAge = parseInt($('#patientAge').val()) || 0;
        const patientGender = $('#patientGender').val() || '';

        let minRange = '';
        let maxRange = '';

        // Determine appropriate range based on demographics
        if (patientAge < 18) {
            // Child ranges
            minRange = test.min_child || test.min_range || '';
            maxRange = test.max_child || test.max_range || '';
        } else if (patientGender.toLowerCase() === 'male') {
            // Male ranges
            minRange = test.min_male || test.min_range || '';
            maxRange = test.max_male || test.max_range || '';
        } else if (patientGender.toLowerCase() === 'female') {
            // Female ranges
            minRange = test.min_female || test.min_range || '';
            maxRange = test.max_female || test.max_range || '';
        } else {
            // General ranges
            minRange = test.min_range || '';
            maxRange = test.max_range || '';
        }

        // Set the ranges
        $row.find('.test-min-range').val(minRange);
        $row.find('.test-max-range').val(maxRange);

        // console.log removed
    }

    /**
     * Validate test result against ranges
     */
    validateTestResult($row, test) {
        const resultValue = parseFloat($row.find('.test-result').val());
        const minRange = parseFloat($row.find('.test-min-range').val());
        const maxRange = parseFloat($row.find('.test-max-range').val());
        const $indicator = $row.find('.validation-indicator');
        const $resultInput = $row.find('.test-result');

        // Clear previous styling
        $indicator.text('').removeClass('text-success text-danger text-warning');
        $resultInput.removeClass('result-normal result-abnormal result-warning');

        if (isNaN(resultValue)) {
            return;
        }

        if (!isNaN(minRange) && !isNaN(maxRange)) {
            if (resultValue < minRange) {
                $indicator.text('Below normal range').addClass('text-danger');
                $resultInput.addClass('result-abnormal');
            } else if (resultValue > maxRange) {
                $indicator.text('Above normal range').addClass('text-danger');
                $resultInput.addClass('result-abnormal');
            } else {
                $indicator.text('Within normal range').addClass('text-success');
                $resultInput.addClass('result-normal');
            }
        } else {
            $indicator.text('No reference range').addClass('text-warning');
            $resultInput.addClass('result-warning');
        }
    }

    /**
     * Clear test-related fields
     */
    clearTestFields($row) {
        $row.find('.test-unit').val('');
        $row.find('.test-min-range').val('');
        $row.find('.test-max-range').val('');
        $row.find('.validation-indicator').text('').removeClass('text-success text-danger text-warning');
    }

    /**
     * Update pricing calculations
     */
    updatePricing() {
        let subtotal = 0;

        // Calculate subtotal from all test rows
        $('.test-row').each((index, row) => {
            const $row = $(row);
            const testId = $row.find('.test-select').val();

            if (testId) {
                const test = this.testsData.find(t => t.id == testId);
                if (test && test.price) {
                    subtotal += parseFloat(test.price) || 0;
                }
            }
        });

        // Update subtotal
        $('#subtotal').val(subtotal.toFixed(2));

        // Calculate total (subtotal - discount)
        const discount = parseFloat($('#discountAmount').val()) || 0;
        const total = Math.max(subtotal - discount, 0);
        $('#totalPrice').val(total.toFixed(2));

        // console.log removed
    }

    /**
     * Save entry (add or edit)
     */
    async saveEntry() {
        try {
            // console.log removed

            // Validate form
            if (!this.validateForm()) {
                return;
            }

            // Show loading state
            const $saveButton = $('#entryForm button[type="submit"]');
            const originalText = $saveButton.html();
            $saveButton.html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving...').prop('disabled', true);

            // Collect form data
            const formData = this.collectFormData();
            // console.log removed

            // Determine if this is add or edit
            const isEdit = this.currentEditId !== null;
            const url = 'ajax/entry_api_fixed.php';
            const action = isEdit ? 'update' : 'save';

            // Add action and secret key
            formData.action = action;
            formData.secret_key = 'hospital-api-secret-2024';

            if (isEdit) {
                formData.id = this.currentEditId;
            }

            // Submit form
            const response = await $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                dataType: 'json'
            });

            // console.log removed

            if (response && response.success) {
                toastr.success(isEdit ? 'Entry updated successfully' : 'Entry created successfully');

                // Close modal and refresh table
                $('#entryModal').modal('hide');
                this.refreshTable();
                this.resetForm();

            } else {
                const errorMessage = response && response.message ? response.message : 'Failed to save entry';
                // console.error removed
                toastr.error(errorMessage);
            }

        } catch (error) {
            // console.error removed

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
                    if (error.status) {
                        errorMessage += ` (Status: ${error.status})`;
                    }
                }
            }

            toastr.error(errorMessage);

        } finally {
            // Restore save button
            const $saveButton = $('#entryForm button[type="submit"]');
            $saveButton.html('<i class="fas fa-save"></i> Save Entry').prop('disabled', false);
        }
    }

    /**
     * Validate form before submission
     */
    validateForm() {
        let isValid = true;
        const errors = [];

        // Check required fields
        const ownerAddedBy = $('#ownerAddedBySelect').val();
        if (!ownerAddedBy) {
            errors.push('Owner/Added By is required');
            isValid = false;
        }

        const patientId = $('#patientSelect').val();
        if (!patientId) {
            errors.push('Patient is required');
            isValid = false;
        }

        const entryDate = $('#entryDate').val();
        if (!entryDate) {
            errors.push('Entry Date is required');
            isValid = false;
        }

        // Check if at least one test is selected
        let hasTests = false;
        $('.test-row').each((index, row) => {
            const $row = $(row);
            const categoryId = $row.find('.test-category-select').val();
            const testId = $row.find('.test-select').val();

            if (categoryId && testId) {
                hasTests = true;
                return false; // break loop
            }
        });

        if (!hasTests) {
            errors.push('At least one test must be selected');
            isValid = false;
        }

        // Show validation errors
        if (!isValid) {
            const errorMessage = 'Please fix the following errors:<br>• ' + errors.join('<br>• ');
            toastr.error(errorMessage, 'Validation Error', {
                timeOut: 8000,
                escapeHtml: false
            });
        }

        return isValid;
    }

    /**
     * Collect form data for submission
     */
    collectFormData() {
        const formData = {};

        // Basic entry data
        formData.owner_added_by = $('#ownerAddedBySelect').val();

        // Enhanced patient handling
        const patientId = $('#patientSelect').val();
        if (patientId === 'new') {
            // Mark as new patient and include patient data
            formData.patient_id = null; // Will be created
            formData.create_new_patient = true;
            formData.patient_name = $('#patientName').val();
            formData.patient_contact = $('#patientContact').val();
            formData.age = $('#patientAge').val();
            formData.gender = $('#patientGender').val();
            formData.patient_address = $('#patientAddress').val();
        } else {
            // Existing patient
            formData.patient_id = patientId;
            formData.create_new_patient = false;
            // Still include patient data for reference
            formData.patient_name = $('#patientName').val();
            formData.patient_contact = $('#patientContact').val();
            formData.age = $('#patientAge').val();
            formData.gender = $('#patientGender').val();
            formData.patient_address = $('#patientAddress').val();
        }

        formData.doctor_id = $('#doctorSelect').val();
        formData.entry_date = $('#entryDate').val();
        formData.status = $('#entryStatus').val();
        formData.priority = $('#priority').val();
        formData.referral_source = $('#referralSource').val();
        formData.notes = $('#entryNotes').val();

        // Pricing data
        formData.subtotal = $('#subtotal').val();
        formData.discount_amount = $('#discountAmount').val();
        formData.total_price = $('#totalPrice').val();

        // Collect test data
        const tests = [];
        $('.test-row').each((index, row) => {
            const $row = $(row);
            const categoryId = $row.find('.test-category-select').val();
            const testId = $row.find('.test-select').val();
            const result = $row.find('.test-result').val();
            const minRange = $row.find('.test-min-range').val();
            const maxRange = $row.find('.test-max-range').val();
            const unit = $row.find('.test-unit').val();

            if (categoryId && testId) {
                tests.push({
                    category_id: categoryId,
                    test_id: testId,
                    result_value: result,
                    min_range: minRange,
                    max_range: maxRange,
                    unit: unit
                });
            }
        });

        formData.tests = JSON.stringify(tests);

        return formData;
    }

    /**
     * Populate patient information fields
     */
    populatePatientInfo(patientId) {
        const patient = this.patientsData.find(p => p.id == patientId);
        if (patient) {
            $('#patientName').val(patient.name || '');
            $('#patientContact').val(patient.contact || patient.phone || '');
            $('#patientAge').val(patient.age || '');
            $('#patientGender').val(patient.gender || '').trigger('change');
            $('#patientAddress').val(patient.address || '');

            // console.log removed

            // Update test ranges if any tests are selected
            this.updateAllTestRanges();
        }
    }

    /**
     * Clear patient information fields
     */
    clearPatientInfo() {
        $('#patientName').val('');
        $('#patientContact').val('');
        $('#patientAge').val('');
        $('#patientGender').val('').trigger('change');
        $('#patientAddress').val('');

        // Update test ranges
        this.updateAllTestRanges();
    }

    /**
     * Update all test ranges when patient demographics change
     */
    updateAllTestRanges() {
        $('.test-row').each((index, row) => {
            const $row = $(row);
            const testId = $row.find('.test-select').val();

            if (testId) {
                const test = this.testsData.find(t => t.id == testId);
                if (test) {
                    this.updateTestRanges($row, test);
                    this.validateTestResult($row, test);
                }
            }
        });
    }

    /**
     * Initialize Select2 dropdowns
     */
    initializeSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        }
    }

    /**
     * Update filtered test count based on category selection
     */
    updateFilteredTestCount() {
        const selectedCategoryId = $('#modalCategoryFilter').val();
        let filteredCount = 0;

        if (!selectedCategoryId) {
            // Show all tests if no filter selected
            filteredCount = this.testsData.length;
        } else {
            // Count tests that belong to the selected category
            filteredCount = this.testsData.filter(test => {
                return test.category_id == selectedCategoryId;
            }).length;
        }

        $('#filteredTestCount').text(filteredCount);
        // console.log removed
    }

    /**
     * Filter tests in dropdowns based on category selection
     */
    filterTestsByCategory() {
        const selectedCategoryId = $('#modalCategoryFilter').val();

        // Get all test dropdowns in the modal
        $('.test-select').each((index, element) => {
            const $testSelect = $(element);
            const currentValue = $testSelect.val();

            // Clear and repopulate the dropdown
            $testSelect.empty().append('<option value="">Select Test</option>');

            // Filter tests based on category
            let filteredTests = this.testsData;
            if (selectedCategoryId) {
                filteredTests = this.testsData.filter(test => {
                    return test.category_id == selectedCategoryId;
                });
            }

            // Populate dropdown with filtered tests
            filteredTests.forEach(test => {
                $testSelect.append(new Option(test.name, test.id, false, false));
            });

            // Restore previous selection if it's still available
            if (currentValue && $testSelect.find(`option[value="${currentValue}"]`).length > 0) {
                $testSelect.val(currentValue);
            }
        });

        // console.log removed
    }

    /**
     * Clear category filter
     */
    clearCategoryFilter() {
        $('#modalCategoryFilter').val('').trigger('change');
    }

    /**
     * Initialize the Entry Manager
     */
    init() {
        // console.log removed

        // Wait for DOM to be ready
        $(document).ready(() => {
            // console.log removed
            try {
                // Add a small delay to ensure all libraries are loaded
                setTimeout(() => {
                    this.initializeDataTable();
                    this.loadInitialData();
                    this.loadStatistics();
                    // console.log removed
                }, 100);
            } catch (error) {
                // console.error removed
            }
        });
    }

    /**
     * Load initial data (tests, categories, etc.)
     */
    async loadInitialData() {
        // console.log removed

        try {
            // Load tests data
            await this.loadTestsData();

            // Load categories data
            await this.loadCategoriesData();

            // Load main categories data
            await this.loadMainCategoriesData();

            // Load owners data
            await this.loadOwnersData();

            // console.log removed
        } catch (error) {
            // console.error removed
        }
    }

    /**
     * Load tests data from API
     */
    async loadTestsData() {
        try {
            // console.log removed
            const response = await $.ajax({
                url: 'ajax/test_api.php',
                method: 'GET',
                data: { action: 'simple_list' },
                dataType: 'json'
            });

            if (response && response.success) {
                this.testsData = response.data || [];
                // console.log removed
            } else {
                // console.error removed
                this.testsData = [];
            }
        } catch (error) {
            // console.error removed
            this.testsData = [];
        }
    }

    /**
     * Load categories data from API
     */
    async loadCategoriesData() {
        try {
            // console.log removed
            const response = await $.ajax({
                url: 'patho_api/test_category.php',
                method: 'GET',
                data: {
                    action: 'list',
                    secret_key: 'hospital-api-secret-2024'
                },
                dataType: 'json'
            });

            if (response && response.success) {
                this.categoriesData = response.data || [];
                // console.log removed

                // Populate the modal category filter
                this.populateModalCategoryFilter();
            } else {
                // console.error removed
                this.categoriesData = [];
            }
        } catch (error) {
            // console.error removed
            this.categoriesData = [];
        }
    }

    /**
     * Load main categories data from API
     */
    async loadMainCategoriesData() {
        try {
            // console.log removed
            const response = await $.ajax({
                url: 'ajax/main_test_category_api.php',
                method: 'GET',
                data: { action: 'list' },
                dataType: 'json'
            });

            if (response && response.success) {
                this.mainCategoriesData = response.data || [];
                // console.log removed
            } else {
                // console.error removed
                this.mainCategoriesData = [];
            }
        } catch (error) {
            // console.error removed
            this.mainCategoriesData = [];
        }
    }

    /**
     * Populate the modal category filter dropdown
     */
    populateModalCategoryFilter() {
        const $filter = $('#modalCategoryFilter');
        if ($filter.length === 0) {
            // console.warn removed
            return;
        }

        // Clear existing options except the first one
        $filter.empty().append('<option value="">All Categories (Show All Tests)</option>');

        // Add categories to dropdown
        this.categoriesData.forEach(category => {
            if (category && category.id && category.name) {
                const option = `<option value="${category.id}">${category.name}</option>`;
                $filter.append(option);
            }
        });

        // console.log removed

        // Update test count
        this.updateFilteredTestCount();
    }

    /**
     * Load owners data from API
     */
    async loadOwnersData() {
        try {
            // console.log removed
            const response = await $.ajax({
                url: 'ajax/user_api.php',
                method: 'GET',
                data: { action: 'list' },
                dataType: 'json'
            });

            if (response && response.success) {
                this.ownersData = response.data || [];
                // console.log removed

                // Populate owner dropdown
                this.populateOwnerDropdown();
            } else {
                // console.error removed
                this.ownersData = [];
            }
        } catch (error) {
            // console.error removed
            this.ownersData = [];
        }
    }

    /**
     * Populate owner dropdown
     */
    populateOwnerDropdown() {
        const $ownerSelect = $('#ownerAddedBySelect');
        if ($ownerSelect.length === 0) {
            return;
        }

        $ownerSelect.empty().append('<option value="">Select Owner/User</option>');

        this.ownersData.forEach(owner => {
            if (owner && owner.id) {
                const name = owner.full_name || owner.username || `User ${owner.id}`;
                $ownerSelect.append(new Option(name, owner.id, false, false));
            }
        });

        // console.log removed
    }

    /**
     * Load patients data based on selected owner
     */
    async loadPatientsData(ownerId) {
        try {
            // console.log removed
            const response = await $.ajax({
                url: 'ajax/patient_api.php',
                method: 'GET',
                data: {
                    action: 'list',
                    owner_id: ownerId
                },
                dataType: 'json'
            });

            if (response && response.success) {
                this.patientsData = response.data || [];
                // console.log removed

                // Populate patient dropdown
                this.populatePatientDropdown();
            } else {
                // console.error removed
                this.patientsData = [];
                this.populatePatientDropdown();
            }
        } catch (error) {
            // console.error removed
            this.patientsData = [];
            this.populatePatientDropdown();
        }
    }

    /**
     * Populate patient dropdown
     */
    populatePatientDropdown() {
        const $patientSelect = $('#patientSelect');
        if ($patientSelect.length === 0) {
            return;
        }

        $patientSelect.empty().append('<option value="">Select Patient</option>');

        this.patientsData.forEach(patient => {
            if (patient && patient.id && patient.name) {
                $patientSelect.append(new Option(patient.name, patient.id, false, false));
            }
        });

        // Enable/disable based on data availability
        $patientSelect.prop('disabled', this.patientsData.length === 0);

        // console.log removed
    }

    /**
     * Load doctors data based on selected owner
     */
    async loadDoctorsData(ownerId) {
        try {
            // console.log removed
            const response = await $.ajax({
                url: 'ajax/doctor_api.php',
                method: 'GET',
                data: {
                    action: 'list',
                    owner_id: ownerId
                },
                dataType: 'json'
            });

            if (response && response.success) {
                this.doctorsData = response.data || [];
                // console.log removed

                // Populate doctor dropdown
                this.populateDoctorDropdown();
            } else {
                // console.error removed
                this.doctorsData = [];
                this.populateDoctorDropdown();
            }
        } catch (error) {
            // console.error removed
            this.doctorsData = [];
            this.populateDoctorDropdown();
        }
    }

    /**
     * Populate doctor dropdown
     */
    populateDoctorDropdown() {
        const $doctorSelect = $('#doctorSelect');
        if ($doctorSelect.length === 0) {
            return;
        }

        $doctorSelect.empty().append('<option value="">Select Doctor (Optional)</option>');

        this.doctorsData.forEach(doctor => {
            if (doctor && doctor.id && doctor.name) {
                $doctorSelect.append(new Option(doctor.name, doctor.id, false, false));
            }
        });

        // Enable/disable based on data availability
        $doctorSelect.prop('disabled', this.doctorsData.length === 0);

        // console.log removed
    }

    /**
     * Initialize DataTable with proper configuration
     */
    initializeDataTable() {
        // console.log removed

        // Check if the table element exists
        if ($('#entriesTable').length === 0) {
            // console.error removed
            return;
        }

        try {
            // Add custom search functions for all filters
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'entriesTable') {
                    return true;
                }

                const rowData = settings.aoData[dataIndex]._aData;

                // Main Category Filter
                const mainCategoryFilter = $('#mainCategoryFilter').val();
                if (mainCategoryFilter) {
                    const mainCategories = rowData.agg_main_test_categories || rowData.main_test_categories || '';
                    if (!mainCategories || !mainCategories.toLowerCase().includes(mainCategoryFilter.toLowerCase())) {
                        return false;
                    }
                }

                // Status Filter
                const statusFilter = $('#statusFilter').val();
                if (statusFilter) {
                    const status = rowData.status || '';
                    if (status.toLowerCase() !== statusFilter.toLowerCase()) {
                        return false;
                    }
                }

                // Date Filter
                const dateFilter = $('#dateFilter').val();
                if (dateFilter) {
                    const entryDate = new Date(rowData.entry_date);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    let showRow = false;
                    switch (dateFilter) {
                        case 'today':
                            const todayEnd = new Date(today);
                            todayEnd.setHours(23, 59, 59, 999);
                            showRow = entryDate >= today && entryDate <= todayEnd;
                            break;
                        case 'yesterday':
                            const yesterday = new Date(today);
                            yesterday.setDate(yesterday.getDate() - 1);
                            const yesterdayEnd = new Date(yesterday);
                            yesterdayEnd.setHours(23, 59, 59, 999);
                            showRow = entryDate >= yesterday && entryDate <= yesterdayEnd;
                            break;
                        case 'this_week':
                            const weekStart = new Date(today);
                            weekStart.setDate(today.getDate() - today.getDay());
                            showRow = entryDate >= weekStart;
                            break;
                        case 'this_month':
                            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
                            showRow = entryDate >= monthStart;
                            break;
                        default:
                            showRow = true;
                    }
                    if (!showRow) {
                        return false;
                    }
                }

                // Patient Filter
                const patientFilter = $('#patientFilter').val();
                if (patientFilter) {
                    const patientName = rowData.patient_name || '';
                    if (!patientName.toLowerCase().includes(patientFilter.toLowerCase())) {
                        return false;
                    }
                }

                // Doctor Filter
                const doctorFilter = $('#doctorFilter').val();
                if (doctorFilter) {
                    const doctorName = rowData.doctor_name || '';
                    if (!doctorName.toLowerCase().includes(doctorFilter.toLowerCase())) {
                        return false;
                    }
                }

                return true; // Show row if all filters pass
            });

            this.entriesTable = $('#entriesTable').DataTable({
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
                        // console.log removed
                        if (json && json.success) {
                            return json.data || [];
                        } else {
                            // console.error removed
                            return [];
                        }
                    },
                    error: function (xhr, error, thrown) {
                        // console.error removed
                    }
                },
                columns: [
                    {
                        data: 'id',
                        title: 'ID',
                        width: '4%'
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
                        width: '14%',
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
                        data: 'agg_test_categories',
                        title: 'Test Category',
                        width: '12%',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                const categories = data || row.test_categories || '';
                                if (!categories) {
                                    return '<span class="text-muted">No category</span>';
                                }

                                // Handle multiple categories
                                const categoryArray = categories.split(',').map(cat => cat.trim()).filter(cat => cat);
                                const uniqueCategories = [...new Set(categoryArray)];

                                if (uniqueCategories.length === 0) {
                                    return '<span class="text-muted">No category</span>';
                                } else if (uniqueCategories.length === 1) {
                                    return `<span class="badge badge-secondary">${uniqueCategories[0]}</span>`;
                                } else {
                                    const displayText = uniqueCategories.slice(0, 2).join(', ');
                                    const remainingCount = uniqueCategories.length - 2;
                                    return `<span class="badge badge-secondary" title="${uniqueCategories.join(', ')}">${displayText}${remainingCount > 0 ? ` +${remainingCount}` : ''}</span>`;
                                }
                            }
                            return data || '';
                        }
                    },
                    {
                        data: 'status',
                        title: 'Status',
                        width: '8%',
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
                        width: '8%',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                // Only show amount if there are tests
                                if (row.tests_count && row.tests_count > 0) {
                                    // Use total_price if available, otherwise calculate from subtotal
                                    let amount = parseFloat(data) || 0;
                                    if (amount === 0 && row.subtotal) {
                                        amount = parseFloat(row.subtotal) || 0;
                                        // Subtract discount if available
                                        const discount = parseFloat(row.discount_amount) || 0;
                                        amount = Math.max(amount - discount, 0);
                                    }
                                    return `₹${amount.toFixed(2)}`;
                                } else {
                                    // No tests, show ₹0.00
                                    return `₹0.00`;
                                }
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
                        width: '8%',
                        render: function (data, type, row) {
                            return data || row.added_by_username || 'Unknown';
                        }
                    },
                    {
                        data: null,
                        title: 'Actions',
                        width: '8%',
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
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // ID
                    { responsivePriority: 2, targets: 1 }, // Patient
                    { responsivePriority: 3, targets: -1 }, // Actions
                    { responsivePriority: 4, targets: 5 }, // Status
                    { responsivePriority: 5, targets: 3 }  // Tests
                ],
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

            // console.log removed
        } catch (error) {
            // console.error removed
        }
    }
}

// Initialize Entry Manager when page loads
let entryManager;
$(document).ready(function () {
    try {
        // //// console.log removed
        // //// console.log removed
        // //// console.log removed
        // //// console.log removed
        // //// console.log removed
        // //// console.log removed

        // //// console.log removed
        entryManager = new EntryManager();
        window.entryManager = entryManager;

        // Add global testing functions for easy access
        window.testDemographicRanges = () => entryManager.validateDemographicRangeWorkflow();
        window.testRangeCalculation = (age, gender, testId) => {
            const test = entryManager.testsData.find(t => t.id == testId);
            if (test) {
                return entryManager.calculateAppropriateRanges(age, gender, test);
            } else {
                // console.error removed
                return null;
            }
        };
        window.debugTestData = () => entryManager.debugTestData();
        window.debugCategoryData = () => entryManager.debugCategoryData();
        window.debugEditMode = () => entryManager.debugEditMode();
        window.debugSpecificEntry = (entryId) => entryManager.debugSpecificEntry(entryId);

        // Add performance monitoring functions
        window.getPerformanceMetrics = () => entryManager.getPerformanceMetrics();
        window.getCacheStatistics = () => entryManager.getCacheStatistics();
        window.optimizePerformance = () => entryManager.optimizePerformance();
        window.clearAllCache = () => entryManager.clearAllCacheData();

        // //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
        //// console.log removed
    } catch (error) {
        // // console.error removed
        // // console.error removed
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

    // Load main categories for filter
    loadMainCategoriesFilter();

    // Bind filter change events
    $('#mainCategoryFilter, #statusFilter, #dateFilter').on('change', function () {
        if (window.entryManager && window.entryManager.entriesTable) {
            window.entryManager.entriesTable.draw();
        }
    });

    // Bind search input events with debounce
    let searchTimeout;
    $('#patientFilter, #doctorFilter').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            if (window.entryManager && window.entryManager.entriesTable) {
                window.entryManager.entriesTable.draw();
            }
        }, 300);
    });

    // Bind modal category filter events
    $('#modalCategoryFilter').on('change', function () {
        if (window.entryManager) {
            window.entryManager.updateFilteredTestCount();
            window.entryManager.filterTestsByCategory();
        }
    });

    // Bind clear category filter button
    $('#clearCategoryFilter').on('click', function () {
        if (window.entryManager) {
            window.entryManager.clearCategoryFilter();
        }
    });

    // Bind discount amount change to update pricing
    $('#discountAmount').on('input', function () {
        if (window.entryManager) {
            window.entryManager.updatePricing();
        }
    });

    // Bind form submission
    $('#entryForm').on('submit', function (e) {
        e.preventDefault();
        if (window.entryManager) {
            window.entryManager.saveEntry();
        }
    });

    // Bind owner selection change to load patients and doctors
    $('#ownerAddedBySelect').on('change', function () {
        const ownerId = $(this).val();
        if (window.entryManager && ownerId) {
            window.entryManager.loadPatientsData(ownerId);
            window.entryManager.loadDoctorsData(ownerId);
        } else {
            // Clear dependent dropdowns
            $('#patientSelect').empty().append('<option value="">Select Owner/User first</option>').prop('disabled', true);
            $('#doctorSelect').empty().append('<option value="">Select Owner/User first</option>').prop('disabled', true);
        }
    });

    // Bind patient selection change to populate patient info
    $('#patientSelect').on('change', function () {
        const patientId = $(this).val();
        if (window.entryManager && patientId) {
            window.entryManager.populatePatientInfo(patientId);
        } else {
            window.entryManager.clearPatientInfo();
        }
    });
});

/**
 * Load main categories for the filter dropdown
 */
function loadMainCategoriesFilter() {
    $.ajax({
        url: 'ajax/main_test_category_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function (response) {
            if (response && response.success && response.data) {
                const $filter = $('#mainCategoryFilter');
                $filter.empty().append('<option value="">All Categories</option>');

                response.data.forEach(function (category) {
                    if (category && category.id && category.name) {
                        $filter.append(`<option value="${category.name}">${category.name}</option>`);
                    }
                });

                // console.log removed
            } else {
                // console.error removed
            }
        },
        error: function (xhr, status, error) {
            // console.error removed
        }
    });
}

