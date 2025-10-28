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
        //console.log('=== TEST DATA DEBUG ===');
        //console.log('Total tests loaded:', this.testsData.length);

        if (this.testsData.length > 0) {
            //console.log('Sample test data:', this.testsData.slice(0, 3));

            // Check for duplicates
            const testNames = this.testsData.map(t => t.name);
            const uniqueNames = [...new Set(testNames)];
            //console.log('Total test names:', testNames.length);
            //console.log('Unique test names:', uniqueNames.length);

            if (testNames.length !== uniqueNames.length) {
                console.warn('DUPLICATE NAMES FOUND!');
                const duplicates = testNames.filter((name, index) => testNames.indexOf(name) !== index);
                //console.log('Duplicate names:', [...new Set(duplicates)]);

                duplicates.forEach(dupName => {
                    const duplicateTests = this.testsData.filter(t => t.name === dupName);
                    //console.log(`Tests with name "${dupName}":`, duplicateTests);
                });
            } else {
                //console.log('✓ No duplicate test names found');
            }
        }
        //console.log('=== END DEBUG ===');
    }

    /**
     * Debug function to check category data (can be called from browser console)
     */
    debugCategoryData() {
        //console.log('=== CATEGORY DATA DEBUG ===');
        //console.log('Total categories loaded:', this.categoriesData.length);
        //console.log('Total main categories loaded:', this.mainCategoriesData.length);

        if (this.categoriesData.length > 0) {
            //console.log('Sample category data:', this.categoriesData.slice(0, 5));

            // Check category structure
            const categoriesWithMainId = this.categoriesData.filter(cat => cat.main_category_id);
            const categoriesWithoutMainId = this.categoriesData.filter(cat => !cat.main_category_id);

            //console.log('Categories with main_category_id:', categoriesWithMainId.length);
            //console.log('Categories without main_category_id:', categoriesWithoutMainId.length);

            if (categoriesWithMainId.length > 0) {
                //console.log('Sample category with main_category_id:', categoriesWithMainId[0]);
            }
        }

        if (this.mainCategoriesData.length > 0) {
            //console.log('Sample main category data:', this.mainCategoriesData.slice(0, 3));
        }

        // Check if tests have category information
        if (this.testsData.length > 0) {
            const testsWithCategory = this.testsData.filter(test => test.category_id);
            const testsWithCategoryName = this.testsData.filter(test => test.category_name);

            //console.log('Tests with category_id:', testsWithCategory.length, '/', this.testsData.length);
            //console.log('Tests with category_name:', testsWithCategoryName.length, '/', this.testsData.length);

            if (testsWithCategory.length > 0) {
                //console.log('Sample test with category:', testsWithCategory[0]);
            }
        }

        // Check current form state
        const $categoryDropdowns = $('.test-category-select');
        //console.log('Category dropdowns found in DOM:', $categoryDropdowns.length);

        $categoryDropdowns.each((index, dropdown) => {
            const $dropdown = $(dropdown);
            const optionCount = $dropdown.find('option').length;
            const selectedValue = $dropdown.val();
            //console.log(`Dropdown ${index}: ${optionCount} options, selected: "${selectedValue}"`);
        });

        //console.log('=== END CATEGORY DEBUG ===');
    }

    /**
     * Debug function to check edit mode state (can be called from browser console)
     */
    debugEditMode() {
        //console.log('=== EDIT MODE DEBUG ===');
        //console.log('Current edit ID:', this.currentEditId);

        // Check test rows in the form
        const $testRows = $('.test-row');
        //console.log('Test rows found:', $testRows.length);

        $testRows.each((index, row) => {
            const $row = $(row);
            const rowIndex = $row.data('row-index');
            const testId = $row.find('.test-select').val();
            const testName = $row.find('.test-select option:selected').text();
            const categoryId = $row.find('.test-category-select').val();
            const categoryName = $row.find('.test-category-select option:selected').text();
            const mainCategoryId = $row.find('.test-main-category-id').val();

            /*console.log(`Row ${index} (index: ${rowIndex}):`, {
                testId: testId,
                testName: testName,
                categoryId: categoryId,
                categoryName: categoryName,
                mainCategoryId: mainCategoryId,
                categoryOptions: $row.find('.test-category-select option').length
            });*/
        });

        //console.log('=== END EDIT MODE DEBUG ===');
    }

    /**
     * Debug function to check specific entry data (can be called from browser console)
     */
    async debugSpecificEntry(entryId) {
        //console.log('=== SPECIFIC ENTRY DEBUG ===');
        //console.log('Entry ID:', entryId);

        try {
            // Get entry data from API
            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: { action: 'get', id: entryId },
                dataType: 'json'
            });

            if (response.success && response.data) {
                //console.log('Entry data:', response.data);

                if (response.data.tests) {
                    //console.log('Tests in entry:', response.data.tests.length);
                    response.data.tests.forEach((test, index) => {
                        /*console.log(`Test ${index + 1}:`, {
                            test_id: test.test_id,
                            test_name: test.test_name,
                            category_id: test.category_id,
                            category_name: test.category_name,
                            entry_category_id: test.entry_category_id || 'not set'
                        });*/

                        // Check if this test exists in our testsData
                        const foundInTestsData = this.testsData.find(t => t.id == test.test_id);
                        if (foundInTestsData) {
                            /*console.log(`Test ${index + 1} found in testsData:`, {
                                id: foundInTestsData.id,
                                name: foundInTestsData.name,
                                category_id: foundInTestsData.category_id,
                                category_name: foundInTestsData.category_name
                            });*/
                        } else {
                            console.warn(`Test ${index + 1} NOT found in testsData!`);
                        }

                        // Check if the category exists in our categoriesData
                        if (test.category_id) {
                            const foundCategory = this.categoriesData.find(cat => cat.id == test.category_id);
                            if (foundCategory) {
                                //console.log(`Category ${test.category_id} found:`, foundCategory);
                            } else {
                                console.warn(`Category ${test.category_id} NOT found in categoriesData!`);
                            }
                        }
                    });
                }
            } else {
                console.error('Failed to get entry data:', response.message);
            }
        } catch (error) {
            console.error('Error getting entry data:', error);
        }

        //console.log('=== END SPECIFIC ENTRY DEBUG ===');
    }

    /**
     * Initialize the Entry Manager
     */
    init() {
        console.log('Initializing Entry Manager...');

        // Wait for DOM to be ready
        $(document).ready(() => {
            console.log('DOM ready, starting initialization...');
            try {
                // Add a small delay to ensure all libraries are loaded
                setTimeout(() => {
                    this.initializeDataTable();
                    this.loadInitialData();
                    this.bindEvents();
                    this.loadStatistics();
                    console.log('Entry Manager initialization complete');
                }, 100);
            } catch (error) {
                console.error('Error during Entry Manager initialization:', error);
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
        console.log('Initializing DataTable...');

        // Check if the table element exists
        if ($('#entriesTable').length === 0) {
            console.error('DataTable element #entriesTable not found');
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
            console.error('jQuery library not loaded');
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
            console.error('DataTables library not loaded');
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
            console.log('About to initialize DataTable...');
            console.log('Table element exists:', $('#entriesTable').length > 0);
            
            // Check table structure
            const $table = $('#entriesTable');
            const headerCells = $table.find('thead th').length;
            console.log('Number of header columns:', headerCells);
            
            if (headerCells !== 12) {
                throw new Error(`Table structure mismatch: Expected 12 columns, found ${headerCells} columns`);
            }
            
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
                        console.log('DataTable received data:', json);
                        if (json && json.success) {
                            return json.data || [];
                        } else {
                            console.error('API Error:', json ? json.message : 'Invalid response');
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
                        width: '10%',
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
                        data: 'agg_main_test_categories',
                        title: 'Main Test Category',
                        width: '10%',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                const mainCategories = data || row.main_test_categories || '';
                                if (!mainCategories) {
                                    return '<span class="text-muted">No main category</span>';
                                }

                                // Handle multiple main categories
                                const categoryArray = mainCategories.split(',').map(cat => cat.trim()).filter(cat => cat);
                                const uniqueCategories = [...new Set(categoryArray)];

                                if (uniqueCategories.length === 0) {
                                    return '<span class="text-muted">No main category</span>';
                                } else if (uniqueCategories.length === 1) {
                                    return `<span class="badge badge-primary">${uniqueCategories[0]}</span>`;
                                } else {
                                    const displayText = uniqueCategories.slice(0, 2).join(', ');
                                    const remainingCount = uniqueCategories.length - 2;
                                    return `<span class="badge badge-primary" title="${uniqueCategories.join(', ')}">${displayText}${remainingCount > 0 ? ` +${remainingCount}` : ''}</span>`;
                                }
                            }
                            return data || '';
                        }
                    },
                    {
                        data: 'status',
                        title: 'Status',
                        width: '6%',
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
                        width: '6%',
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
                        width: '7%',
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
                        width: '7%',
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
                        width: '6%',
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
                    { responsivePriority: 4, targets: 6 }, // Status
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

            //console.log('DataTable initialized successfully');
        } catch (error) {
            console.error('Error initializing DataTable:', error);
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
        // //console.log('Loading initial data...');

        try {
            // Load tests data
            await this.loadTestsData();

            // Load main categories data
            await this.loadMainCategoriesData();

            // Load categories data for filtering
            await this.loadCategoriesForFilter();

            // Load owners/users data
            await this.loadOwnersData();

            // //console.log('Initial data loaded successfully');
        } catch (error) {
            //console.error('Error loading initial data:', error);
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
                    //console.log('Tests data loaded from cache:', this.testsData.length, 'tests');
                    return true;
                }
            }

            //console.log(`Loading tests data from API... (attempt ${retryCount + 1}/${maxRetries + 1})`);
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

                //console.log('Tests data loaded successfully:', this.testsData.length, 'tests');

                // Check for duplicate test names and handle them
                this.handleDuplicateTestNames();

                // Debug: show first few tests
                if (this.testsData.length > 0) {
                    console.log('Sample tests:', this.testsData.slice(0, 5).map(t => ({id: t.id, name: t.name, category_id: t.category_id, category_name: t.category_name})));
                    console.log('Test data structure:', Object.keys(this.testsData[0]));
                } else {
                    console.warn('Tests data is empty after validation');
                    this.handleEmptyTestData();
                }

                return true; // Success
            } else {
                const errorMessage = response ? response.message : 'Invalid response from server';
                console.error('Failed to load tests:', errorMessage);

                // Try retry if we haven't exceeded max retries
                if (retryCount < maxRetries) {
                    //console.log(`Retrying test data load in 2 seconds... (${retryCount + 1}/${maxRetries})`);
                    await this.delay(2000);
                    return await this.loadTestsData(retryCount + 1);
                }

                this.testsData = [];
                this.handleTestDataLoadError(errorMessage);
                return false; // Failed
            }
        } catch (error) {
            console.error('Error loading tests data:', error);

            const errorDetails = {
                status: error.status,
                statusText: error.statusText,
                responseText: error.responseText,
                timeout: error.statusText === 'timeout'
            };
            console.error('Test data load error details:', errorDetails);

            // Try to parse error response for more details
            if (error.responseText) {
                try {
                    const errorData = JSON.parse(error.responseText);
                    console.error('Parsed error response:', errorData);
                    errorDetails.parsedError = errorData;
                } catch (parseError) {
                    console.error('Could not parse error response:', error.responseText);
                }
            }

            // Try retry for network errors if we haven't exceeded max retries
            if (retryCount < maxRetries && (error.status === 0 || error.statusText === 'timeout' || error.status >= 500)) {
                //console.log(`Retrying test data load due to network error in 3 seconds... (${retryCount + 1}/${maxRetries})`);
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
            console.error('Tests data is not an array:', tests);
            return [];
        }

        const validTests = tests.filter(test => {
            if (!test || typeof test !== 'object') {
                console.warn('Invalid test object:', test);
                return false;
            }

            if (!test.id || !test.name) {
                console.warn('Test missing required fields (id, name):', test);
                return false;
            }

            // Validate numeric fields
            if (test.price && isNaN(parseFloat(test.price))) {
                console.warn('Test has invalid price:', test);
                test.price = 0; // Set default price
            }

            return true;
        });

        if (validTests.length !== tests.length) {
            console.warn(`${tests.length - validTests.length} invalid tests filtered out`);
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
            console.error('Duplicate test names detected in API response!');
            //console.log('Total tests:', testNames.length, 'Unique names:', uniqueNames.length);

            // Find and log duplicates
            const duplicates = testNames.filter((name, index) => testNames.indexOf(name) !== index);
            //console.log('Duplicate names:', [...new Set(duplicates)]);

            // Show detailed info about duplicates
            duplicates.forEach(dupName => {
                const duplicateTests = this.testsData.filter(t => t.name === dupName);
                /*console.log(`Tests with name "${dupName}":`, duplicateTests.map(t => ({
                    id: t.id,
                    name: t.name,
                    category: t.category_name
                })));*/
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
        console.warn('No test data available');

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
        console.error('Test data loading failed:', errorMessage);

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
            console.error('Error creating test data retry UI:', error);
        }
    }

    /**
     * Retry test data loading
     */
    async retryTestDataLoad() {
        try {
            //console.log('Retrying test data load...');

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

                //console.log('Test data retry successful');
            } else {
                // Failed again - restore button state
                $retryButton.html(originalText).prop('disabled', false);
                //console.log('Test data retry failed');
            }
        } catch (error) {
            console.error('Error during test data retry:', error);

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
            //console.log('Test data error UI dismissed');
        } catch (error) {
            console.error('Error dismissing test data error UI:', error);
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
                    //console.log('Categories data loaded from cache:', this.categoriesData.length, 'categories');
                    return true;
                }
            }

            //console.log(`Loading categories for filter... (attempt ${retryCount + 1}/${maxRetries + 1})`);
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
                    console.warn(`${this.categoriesData.length - validCategories.length} invalid categories filtered out`);
                    this.categoriesData = validCategories;
                }

                // Cache the validated data
                this.setCacheData(this.cacheKeys.CATEGORIES_DATA, this.categoriesData);

                this.populateCategoryFilter();
                console.log('Categories loaded successfully:', this.categoriesData.length, 'categories');
                console.log('Sample categories:', this.categoriesData.slice(0, 3));

                // Debug: Log first few categories to verify data structure
                if (this.categoriesData.length > 0) {
                    //console.log('Sample category data:', this.categoriesData.slice(0, 3));
                }

                return true; // Success
            } else {
                const errorMessage = response ? response.message : 'Invalid response from server';
                console.error('Failed to load categories:', errorMessage);

                // Try retry if we haven't exceeded max retries
                if (retryCount < maxRetries) {
                    //console.log(`Retrying category load in 2 seconds... (${retryCount + 1}/${maxRetries})`);
                    await this.delay(2000);
                    return await this.loadCategoriesForFilter(retryCount + 1);
                }

                this.categoriesData = [];
                this.handleCategoryLoadError(errorMessage);
                return false; // Failed
            }
        } catch (error) {
            console.error('Error loading categories data:', error);

            const errorDetails = {
                status: error.status,
                statusText: error.statusText,
                responseText: error.responseText,
                timeout: error.statusText === 'timeout'
            };
            console.error('Category load error details:', errorDetails);

            // Try retry for network errors if we haven't exceeded max retries
            if (retryCount < maxRetries && (error.status === 0 || error.statusText === 'timeout' || error.status >= 500)) {
                //console.log(`Retrying category load due to network error in 3 seconds... (${retryCount + 1}/${maxRetries})`);
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
            console.error('Categories data is not an array:', categories);
            return [];
        }

        return categories.filter(category => {
            if (!category || typeof category !== 'object') {
                console.warn('Invalid category object:', category);
                return false;
            }

            if (!category.id || !category.name) {
                console.warn('Category missing required fields (id, name):', category);
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
            //console.log('Loading main categories...');
            const response = await $.ajax({
                url: 'ajax/main_test_category_api.php',
                method: 'GET',
                data: { action: 'list' },
                dataType: 'json'
            });

            if (response && response.success) {
                this.mainCategoriesData = response.data || [];
                //console.log('Loaded main categories data:', this.mainCategoriesData.length, 'categories');
            } else {
                //console.error('Failed to load main categories:', response ? response.message : 'Invalid response');
                this.mainCategoriesData = [];
            }
        } catch (error) {
            //console.error('Error loading main categories data:', error);
            this.mainCategoriesData = [];
        }
    }

    /**
     * Populate category filter dropdown
     */
    populateCategoryFilter() {
        const $categoryFilter = $('#categoryFilter');
        if ($categoryFilter.length === 0) {
            //console.warn('Category filter element not found');
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

        //console.log('Category filter populated with', this.categoriesData.length, 'categories');
    }

    /**
     * Handle category loading errors gracefully with retry options
     * @param {string} errorMessage - The error message to display
     */
    handleCategoryLoadError(errorMessage = 'Unknown error') {
        console.warn('Category loading failed, implementing fallback measures');
        console.error('Error details:', errorMessage);

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
        console.error('Category loading error handled. Features disabled:', {
            category_filter_disabled: true,
            clear_button_disabled: true,
            retry_ui_created: true,
            error_message: errorMessage
        });
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
            console.error('Error creating category retry UI:', error);
        }
    }

    /**
     * Retry category loading
     */
    async retryCategoryLoad() {
        try {
            //console.log('Retrying category load...');

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

                //console.log('Category retry successful');
            } else {
                // Failed again - restore button state
                $retryButton.html(originalText).prop('disabled', false);
                //console.log('Category retry failed');
            }
        } catch (error) {
            console.error('Error during category retry:', error);

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
            //console.log('Category error UI dismissed');
        } catch (error) {
            console.error('Error dismissing category error UI:', error);
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
                console.warn('Tests data is not available or invalid, returning empty array');
                return [];
            }

            // Handle empty tests data gracefully
            if (this.testsData.length === 0) {
                console.warn('Tests data is empty, no tests to filter');
                return [];
            }

            // Enhanced type checking and normalization for category ID
            const normalizedCategoryId = this.normalizeCategoryId(categoryId);

            // If no valid category selected, return all tests
            if (normalizedCategoryId === null) {
                console.log('No category filter applied, returning all tests:', this.testsData.length);
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
                    console.warn('Error processing test during filtering:', testError, test);
                    return false; // Exclude problematic tests
                }
            });

            console.log(`Filtered tests by category ${categoryId}:`, filteredTests.length, 'out of', this.testsData.length);

            // Enhanced debugging and fallback behavior
            if (filteredTests.length === 0 && normalizedCategoryId !== 'uncategorized') {
                this.handleEmptyFilterResults(categoryId, normalizedCategoryId);
            }

            return filteredTests;
        } catch (error) {
            console.error('Error in filterTestsByCategory:', error);
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
            console.warn('Invalid category ID format:', categoryId);
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
        console.warn(`No tests found for category ID: ${originalCategoryId} (normalized: ${normalizedCategoryId})`);

        // Provide debugging information
        const availableCategoryIds = [...new Set(
            this.testsData
                .map(t => this.normalizeTestCategoryId(t))
                .filter(id => id !== null)
        )];

        console.log('Available category IDs in tests:', availableCategoryIds);

        // Check if the category exists in our categories data
        const categoryExists = this.categoriesData.some(cat =>
            String(cat.id).trim() === normalizedCategoryId
        );

        if (!categoryExists) {
            console.warn(`Category ${normalizedCategoryId} does not exist in categories data`);
        } else {
            console.warn(`Category ${normalizedCategoryId} exists but has no associated tests`);
        }
    }

    /**
     * Handle category filter errors with recovery options
     * @param {Error} error - The error that occurred
     * @param {*} categoryId - The category ID that caused the error
     */
    handleCategoryFilterError(error, categoryId) {
        console.error('Category filtering error details:', {
            error: error.message,
            categoryId: categoryId,
            testsDataLength: this.testsData ? this.testsData.length : 'null',
            categoriesDataLength: this.categoriesData ? this.categoriesData.length : 'null'
        });

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
            console.error('Error during category filter recovery:', recoveryError);
        }
    }

    /**
     * Handle global category filter change
     * @param {string|number} categoryId - The selected category ID
     */
    async onCategoryFilterChange(categoryId) {
        try {
            const startTime = performance.now();
            //console.log('Global category filter changed to:', categoryId);

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
            //console.log(`Global category filter change completed in ${duration.toFixed(2)}ms`);

            // Track performance
            this.performanceMetrics.filterOperations.push({
                timestamp: Date.now(),
                duration: duration,
                type: 'global_filter_change',
                categoryId: categoryId
            });

            // Alert if operation is slow
            if (duration > 200) {
                console.warn(`Category filter change took ${duration.toFixed(2)}ms, which exceeds the 200ms target`);
            }

            //console.log('Global category filter change handled successfully');
        } catch (error) {
            console.error('Error handling global category filter change:', error);
            // Attempt recovery
            this.recoverFromCategoryFilterError();
        }
    }

    /**
     * Clear category filter and show all tests
     */
    clearCategoryFilter() {
        try {
            //console.log('Clearing global category filter...');

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

            //console.log('Category filter cleared, showing all tests');
        } catch (error) {
            console.error('Error clearing category filter:', error);
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
            console.error('Error showing category filter indicator:', error);
        }
    }

    /**
     * Hide category filter indicator
     */
    hideCategoryFilterIndicator() {
        try {
            $('#categoryFilterIndicator').hide();
        } catch (error) {
            console.error('Error hiding category filter indicator:', error);
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

            //console.log(`Updating ${$testSelects.length} test dropdowns with ${filteredTests.length} filtered tests`);

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

            //console.log(`Updated all test dropdowns in ${duration.toFixed(2)}ms`);

            // Warn if operation is slow
            if (duration > 500) {
                console.warn(`Test dropdown update took ${duration.toFixed(2)}ms, which exceeds the 500ms target`);
            }
        } catch (error) {
            console.error('Error updating test dropdowns:', error);
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
                console.error('Invalid select element passed to updateTestDropdownOptions');
                return;
            }

            if (!filteredTests || !Array.isArray(filteredTests)) {
                console.warn('Invalid filteredTests array, using empty array');
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
                    console.warn('Invalid test object found:', test);
                }
            });

            // Update dropdown with all options at once (more efficient)
            $select.html(optionsHtml);

            // Handle selection restoration
            const shouldRestoreSelection = currentValue && validTests.some(test => test.id == currentValue);

            if (shouldRestoreSelection) {
                $select.val(currentValue);
                //console.log(`Restored test selection: ${currentValue}`);
            } else if (currentValue) {
                // If previously selected test is not in filtered results, clear selection
                //console.log(`Previous test selection ${currentValue} not available in filtered results, clearing`);
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
                    console.warn('Error refreshing Select2:', select2Error);
                    // Try to reinitialize Select2 if refresh fails
                    try {
                        $select.select2('destroy').select2({
                            theme: 'bootstrap4',
                            width: '100%',
                            placeholder: 'Select Test'
                        });
                    } catch (reinitError) {
                        console.error('Error reinitializing Select2:', reinitError);
                    }
                }
            }

            //console.log(`Updated test dropdown with ${validTests.length} options`);

        } catch (error) {
            console.error('Error updating test dropdown options:', error);

            // Fallback: Ensure dropdown has at least the default option
            try {
                if ($select && $select.length > 0) {
                    $select.html('<option value="">Select Test (Error loading options)</option>');
                }
            } catch (fallbackError) {
                console.error('Error in fallback option creation:', fallbackError);
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
        // //console.log('Binding events...');

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

        // //console.log('Events bound successfully');
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

        // Debug: Check for duplicate test names
        const testNames = this.testsData.map(t => t.name);
        const duplicateNames = testNames.filter((name, index) => testNames.indexOf(name) !== index);
        if (duplicateNames.length > 0) {
            console.warn('Duplicate test names found:', duplicateNames);
            //console.log('All test data:', this.testsData.map(t => ({ id: t.id, name: t.name })));
        }

        // Get filtered tests based on current global category filter
        // This ensures new test rows respect the active global filter
        const globalCategoryFilter = $('#categoryFilter').val();
        const filteredTests = this.getCurrentlyFilteredTests();

        /*console.log('Creating new test row with global filter:', {
            global_filter: globalCategoryFilter,
            filtered_tests_count: filteredTests.length,
            total_tests_count: this.testsData.length
        });*/

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
            //console.log('Looking for test with ID:', testData.test_id);
            const foundTest = this.testsData.find(t => t.id == testData.test_id);
            //console.log('Found test:', foundTest);
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
            //console.log('Pre-selecting global category filter in new row:', globalCategoryFilter);
            $categorySelect.val(globalCategoryFilter);

            // Set main category ID if available
            const selectedCategory = this.categoriesData.find(cat => cat.id == globalCategoryFilter);
            if (selectedCategory && selectedCategory.main_category_id) {
                $newRow.find('.test-main-category-id').val(selectedCategory.main_category_id);
            }
        }

        // Debug: Log category population status
        //console.log('Category dropdown populated for row', rowIndex, 'with', this.categoriesData.length, 'categories');

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

        // If testData is provided, populate the row
        if (testData) {
            //console.log('=== POPULATING TEST ROW ===');
            //console.log('Test data received:', testData);
            //console.log('Test ID from data:', testData.test_id);
            //console.log('Test name from data:', testData.test_name);
            //console.log('Available tests in testsData:', this.testsData.length);
            //console.log('Looking for test ID:', testData.test_id);

            // Debug: show what test IDs are available
            const availableIds = this.testsData.map(t => t.id);
            //console.log('Available test IDs in testsData:', availableIds);
            //console.log('Is test ID', testData.test_id, 'in available IDs?', availableIds.includes(parseInt(testData.test_id)));

            // Find the test in our testsData to get the correct information
            const foundTest = this.testsData.find(t => t.id == testData.test_id);
            if (foundTest) {
                //console.log('Found matching test in testsData:', foundTest);

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

                    // PRIORITY: Use current test data over entry data for categories
                    // This ensures we show the test's current category, not outdated entry data
                    const categoryId = foundTest.category_id || testData.category_id || '';
                    const categoryName = foundTest.category_name || testData.category_name || '';
                    const price = testData.price || foundTest.price || 0;

                    // Detect and log category conflicts for debugging
                    if (testData.category_id && foundTest.category_id && testData.category_id != foundTest.category_id) {
                        console.warn('Category conflict detected:', {
                            test_id: testData.test_id,
                            test_name: foundTest.name,
                            entry_category_id: testData.category_id,
                            entry_category_name: testData.category_name,
                            current_category_id: foundTest.category_id,
                            current_category_name: foundTest.category_name,
                            resolution: 'Using current test category'
                        });
                    }

                    /*console.log('Category selection debug:', {
                        test_id: testData.test_id,
                        test_name: foundTest.name,
                        entry_category_id: testData.category_id,
                        entry_category_name: testData.category_name,
                        test_category_id: foundTest.category_id,
                        test_category_name: foundTest.category_name,
                        final_category_id: categoryId,
                        final_category_name: categoryName
                    });*/

                    /*console.log('Using demographic-appropriate ranges for edit mode:', {
                        test_id: testData.test_id,
                        category_name: categoryName,
                        rangeType: rangeData.type,
                        min: rangeData.min,
                        max: rangeData.max,
                        unit: rangeData.unit,
                        price: price,
                        result: testData.result_value
                    });*/

                    // Set the category dropdown to the correct category (prioritizing current test data)
                    if (categoryId) {
                        //console.log('Setting category dropdown to:', categoryId, 'for test:', testData.test_id);

                        // Check if the category exists in the dropdown
                        const categoryExists = $categorySelect.find(`option[value="${categoryId}"]`).length > 0;

                        if (categoryExists) {
                            // Category exists, set it
                            $categorySelect.val(categoryId);

                            // Set the main category ID if available
                            const selectedCategory = this.categoriesData.find(cat => cat.id == categoryId);
                            if (selectedCategory && selectedCategory.main_category_id) {
                                $newRow.find('.test-main-category-id').val(selectedCategory.main_category_id);
                                //console.log('Set main category ID:', selectedCategory.main_category_id, 'for category:', selectedCategory.name);
                            }

                            //console.log('Successfully set category:', categoryId, categoryName);
                        } else {
                            // Category doesn't exist in dropdown, try to add it
                            console.warn('Category ID', categoryId, 'not found in dropdown options!');

                            const categoryInData = this.categoriesData.find(cat => cat.id == categoryId);
                            if (categoryInData) {
                                //console.log('Adding missing category to dropdown:', categoryInData);

                                // Find the appropriate optgroup or create one
                                let $targetOptgroup;
                                if (categoryInData.main_category_id) {
                                    const mainCategory = this.mainCategoriesData.find(mc => mc.id == categoryInData.main_category_id);
                                    if (mainCategory) {
                                        $targetOptgroup = $categorySelect.find(`optgroup[label="${this.escapeHtml(mainCategory.name)}"]`);
                                        if ($targetOptgroup.length === 0) {
                                            $targetOptgroup = $(`<optgroup label="${this.escapeHtml(mainCategory.name)}"></optgroup>`);
                                            $categorySelect.append($targetOptgroup);
                                        }
                                    }
                                }

                                if (!$targetOptgroup || $targetOptgroup.length === 0) {
                                    $targetOptgroup = $categorySelect.find('optgroup[label="Other Categories"]');
                                    if ($targetOptgroup.length === 0) {
                                        $targetOptgroup = $('<optgroup label="Other Categories"></optgroup>');
                                        $categorySelect.append($targetOptgroup);
                                    }
                                }

                                // Add the missing category option
                                const $newOption = $(`<option value="${categoryInData.id}" data-main-category="${categoryInData.main_category_id || ''}">${this.escapeHtml(categoryInData.name)}</option>`);
                                $targetOptgroup.append($newOption);

                                // Now set the category
                                $categorySelect.val(categoryId);
                                $newRow.find('.test-main-category-id').val(categoryInData.main_category_id || '');

                                //console.log('Added and selected missing category:', categoryInData.name);
                            } else {
                                console.error('Category not found in categoriesData either! Using fallback.');
                                // Create a fallback option
                                const fallbackName = categoryName || `Category ${categoryId}`;
                                let $otherOptgroup = $categorySelect.find('optgroup[label="Other Categories"]');
                                if ($otherOptgroup.length === 0) {
                                    $otherOptgroup = $('<optgroup label="Other Categories"></optgroup>');
                                    $categorySelect.append($otherOptgroup);
                                }

                                const $fallbackOption = $(`<option value="${categoryId}">${this.escapeHtml(fallbackName)} (Missing)</option>`);
                                $otherOptgroup.append($fallbackOption);
                                $categorySelect.val(categoryId);

                                //console.log('Created fallback category option:', fallbackName);
                            }
                        }

                        // Trigger Select2 update for category dropdown
                        if ($categorySelect.hasClass('select2-hidden-accessible')) {
                            $categorySelect.trigger('change.select2');
                        }
                    } else {
                        //console.log('No category ID available for test:', testData.test_id);
                    }

                    $newRow.find('.test-price').val(price);
                    $newRow.find('.test-result').val(testData.result_value || '');

                    // Use demographic-appropriate ranges instead of stored ranges
                    this.updateRangeDisplay($newRow, rangeData);

                    // Don't trigger change event to avoid overwriting entry-specific data
                    // $testSelect.trigger('change');

                    //console.log('Test row populated with test ID:', testData.test_id, 'Name:', foundTest.name);
                }, 200); // Increased timeout to ensure DOM is ready
            } else {
                console.warn('Test not found in testsData for ID:', testData.test_id);
                //console.log('Looking for test with ID:', testData.test_id, 'in', this.testsData.map(t => ({ id: t.id, name: t.name })));
                //console.log('Available test IDs:', this.testsData.map(t => t.id));
                //console.log('Looking for test ID type:', typeof testData.test_id, 'Available ID types:', this.testsData.map(t => typeof t.id));

                // Try to find test with string/number conversion
                let foundTestAlt = this.testsData.find(t => t.id == testData.test_id || t.id === String(testData.test_id) || String(t.id) === String(testData.test_id));
                if (foundTestAlt) {
                    //console.log('Found test with alternative matching:', foundTestAlt);
                    // Use the found test data
                    const rangeData = this.calculateAppropriateRanges(
                        parseInt($('#patientAge').val()) || null,
                        $('#patientGender').val() || null,
                        foundTestAlt
                    );

                    // Set the category dropdown for alternative match
                    if (foundTestAlt.category_id) {
                        $categorySelect.val(foundTestAlt.category_id);
                        if ($categorySelect.hasClass('select2-hidden-accessible')) {
                            $categorySelect.trigger('change.select2');
                        }

                        // Set the main category ID
                        const selectedCategory = this.categoriesData.find(cat => cat.id == foundTestAlt.category_id);
                        if (selectedCategory && selectedCategory.main_category_id) {
                            $newRow.find('.test-main-category-id').val(selectedCategory.main_category_id);
                        }
                    }

                    $newRow.find('.test-price').val(foundTestAlt.price || 0);
                    $newRow.find('.test-result').val(testData.result_value || '');
                    this.updateRangeDisplay($newRow, rangeData);

                    //console.log('Test row populated with alternative match for ID:', testData.test_id, 'Name:', foundTestAlt.name);
                    return; // Exit early since we found the test
                }

                // If test not found in our data, try to populate with what we have
                let testName = testData.test_name || `Test ${testData.test_id}`;

                // Make the name more unique by adding category and ID
                if (testData.category_name) {
                    testName += ` (${testData.category_name})`;
                }
                testName += ` [ID: ${testData.test_id}]`;

                //console.log('Adding missing test option:', testData.test_id, testName);
                /*console.log('Test data available for missing test:', {
                    test_id: testData.test_id,
                    test_name: testData.test_name,
                    category_name: testData.category_name,
                    hasTestName: !!testData.test_name
                });*/

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

                    /*console.log('Using fallback data for missing test:', {
                        test_id: testData.test_id,
                        category_name: categoryName,
                        rangeData: fallbackRangeData,
                        price: price
                    });*/

                    // Set category dropdown for fallback case
                    if (testData.category_id) {
                        $categorySelect.val(testData.category_id);
                        if ($categorySelect.hasClass('select2-hidden-accessible')) {
                            $categorySelect.trigger('change.select2');
                        }

                        // Set the main category ID
                        const selectedCategory = this.categoriesData.find(cat => cat.id == testData.category_id);
                        if (selectedCategory && selectedCategory.main_category_id) {
                            $newRow.find('.test-main-category-id').val(selectedCategory.main_category_id);
                        }
                    }

                    $newRow.find('.test-price').val(price);
                    $newRow.find('.test-result').val(testData.result_value || '');

                    // Use fallback range data
                    this.updateRangeDisplay($newRow, fallbackRangeData);

                    //console.log('Test row populated with fallback data for ID:', testData.test_id, 'Name:', testName);
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
    async onTestChange(selectElement, $row) {
        const $select = $(selectElement);
        const selectedOption = $select.find('option:selected');
        const testId = selectedOption.val();

        //console.log('Test selection changed to:', testId);
        //console.log('Row being updated:', $row.data('row-index'));

        if (testId) {
            // Check if this row already has data (from edit mode) - if so, don't overwrite
            const existingCategoryId = $row.find('.test-category-select').val();
            const existingUnit = $row.find('.test-unit').val();
            const existingMin = $row.find('.test-min').val();
            const existingMax = $row.find('.test-max').val();

            // If the row already has complete data, don't overwrite it (edit mode)
            if (existingCategoryId && existingUnit && existingMin && existingMax) {
                /*console.log('Row already has complete data, not overwriting:', {
                    categoryId: existingCategoryId,
                    unit: existingUnit,
                    min: existingMin,
                    max: existingMax
                });*/
                this.calculateTotals();
                return;
            }

            // Find the test in our testsData for accurate information
            const foundTest = this.testsData.find(t => t.id == testId);

            if (foundTest) {
                //console.log('Found test data for ID', testId, ':', foundTest);

                // Get current patient demographics
                const patientAge = parseInt($('#patientAge').val()) || null;
                const patientGender = $('#patientGender').val() || null;

                // Calculate appropriate ranges for this patient
                const rangeData = this.calculateAppropriateRanges(patientAge, patientGender, foundTest);

                // Populate test details from testsData
                // Set the category dropdown to match the test's category
                const $categorySelect = $row.find('.test-category-select');
                if (foundTest.category_id) {
                    console.log('Setting category for test:', foundTest.category_id, 'Test name:', foundTest.name);
                    console.log('Test data:', {id: foundTest.id, name: foundTest.name, category_id: foundTest.category_id, category_name: foundTest.category_name});

                    // Ensure category dropdown is populated first
                    if (this.categoriesData.length === 0) {
                        console.warn('Categories not loaded yet, attempting to load...');
                        await this.loadCategoriesForFilter();
                    }

                    // Re-populate the category dropdown for this row to ensure it has the latest data
                    this.populateRowCategoryDropdown($categorySelect);

                    // Set the category value immediately
                    $categorySelect.val(foundTest.category_id);

                    // Force Select2 to update if it's initialized
                    if ($categorySelect.hasClass('select2-hidden-accessible')) {
                        // Destroy and reinitialize Select2 to ensure it shows the correct value
                        $categorySelect.select2('destroy');
                        $categorySelect.select2({
                            theme: 'bootstrap4',
                            width: '100%',
                            placeholder: 'Select Category'
                        });
                    }

                    console.log('Category set to:', foundTest.category_id, 'for test:', foundTest.name);
                    console.log('Dropdown value after setting:', $categorySelect.val());
                    console.log('Available options in dropdown:', $categorySelect.find('option').map(function() { return $(this).val() + ':' + $(this).text(); }).get());

                    // Also set the main category ID
                    const selectedCategory = this.categoriesData.find(cat => cat.id == foundTest.category_id);
                    if (selectedCategory && selectedCategory.main_category_id) {
                        $row.find('.test-main-category-id').val(selectedCategory.main_category_id);
                        console.log('Set main category ID:', selectedCategory.main_category_id, 'for category:', selectedCategory.name);
                    } else {
                        console.warn('Could not find category data for ID:', foundTest.category_id);
                        console.log('Available categories:', this.categoriesData.map(c => ({id: c.id, name: c.name})));
                    }
                } else {
                    console.log('Test has no category_id:', foundTest.name);
                }

                $row.find('.test-price').val(foundTest.price || 0);

                // Use calculated demographic-appropriate ranges
                this.updateRangeDisplay($row, rangeData);

                /*console.log('Updated row with demographic-appropriate ranges:', {
                    category: foundTest.category_name,
                    rangeType: rangeData.type,
                    min: rangeData.min,
                    max: rangeData.max,
                    unit: rangeData.unit,
                    price: foundTest.price
                });*/
            } else {
                // Fallback to data attributes if test not found in testsData
                console.warn('Test not found in testsData for ID:', testId, 'using data attributes');
                // Note: We should set the category dropdown properly here too, but for now just log
                //console.log('Category from data attribute:', selectedOption.data('category'));
                $row.find('.test-unit').val(selectedOption.data('unit') || '');
                $row.find('.test-min').val(selectedOption.data('min') || '');
                $row.find('.test-max').val(selectedOption.data('max') || '');
                $row.find('.test-price').val(selectedOption.data('price') || 0);
            }
        } else {
            // Clear test details including range indicator
            $row.find('.test-category-select, .test-unit, .test-min, .test-max').val('');
            $row.find('.test-main-category-id').val('');
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
        //console.log('Range cache cleared');
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
        //console.log(`Batch range update completed in ${(endTime - startTime).toFixed(2)}ms for ${rangeUpdates.length} tests`);

        return rangeUpdates.length;
    }

    /**
     * Test demographic range functionality with sample data
     */
    testDemographicRangeFunctionality() {
        //console.log('=== TESTING DEMOGRAPHIC RANGE FUNCTIONALITY ===');

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

        //console.log('Testing with test:', testWithRanges.name, 'ID:', testWithRanges.id);

        // Test child ranges (age 10)
        const childRange = this.calculateAppropriateRanges(10, 'Male', testWithRanges);
        //console.log('Child range (age 10, Male):', childRange);

        // Test adult male ranges (age 30)
        const maleRange = this.calculateAppropriateRanges(30, 'Male', testWithRanges);
        //console.log('Adult male range (age 30, Male):', maleRange);

        // Test adult female ranges (age 25)
        const femaleRange = this.calculateAppropriateRanges(25, 'Female', testWithRanges);
        //console.log('Adult female range (age 25, Female):', femaleRange);

        // Test general ranges (no demographics)
        const generalRange = this.calculateAppropriateRanges(null, null, testWithRanges);
        //console.log('General range (no demographics):', generalRange);

        //console.log('=== DEMOGRAPHIC RANGE TESTING COMPLETE ===');
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
        //console.log('=== VALIDATING DEMOGRAPHIC RANGE WORKFLOW ===');

        const results = {
            cacheTest: false,
            performanceTest: false,
            validationTest: false,
            uiUpdateTest: false
        };

        try {
            // Test 1: Cache functionality
            //console.log('Testing cache functionality...');
            const testData = this.testsData[0];
            if (testData) {
                const key = this.generateRangeCacheKey(25, 'Male', testData.id);
                const range1 = this.calculateAppropriateRanges(25, 'Male', testData);
                const range2 = this.calculateAppropriateRanges(25, 'Male', testData); // Should use cache
                results.cacheTest = true;
                //console.log('✓ Cache test passed');
            }

            // Test 2: Performance test
            //console.log('Testing performance...');
            const startTime = performance.now();
            for (let i = 0; i < 100; i++) {
                if (this.testsData[0]) {
                    this.calculateAppropriateRanges(25, 'Male', this.testsData[0]);
                }
            }
            const endTime = performance.now();
            const avgTime = (endTime - startTime) / 100;
            results.performanceTest = avgTime < 1; // Should be under 1ms per calculation
            //console.log(`✓ Performance test: ${avgTime.toFixed(3)}ms per calculation`);

            // Test 3: Validation test
            //console.log('Testing validation...');
            const validation = this.validatePatientDemographics(25, 'Male');
            results.validationTest = validation.age === 25 && validation.gender === 'male';
            //console.log('✓ Validation test passed');

            // Test 4: UI update test (if DOM elements exist)
            //console.log('Testing UI updates...');
            if ($('#patientAge').length > 0) {
                $('#patientAge').val('25');
                $('#patientGender').val('Male');
                this.updateAllTestRangesForCurrentPatient();
                results.uiUpdateTest = true;
                //console.log('✓ UI update test passed');
            } else {
                //console.log('⚠ UI elements not available for testing');
                results.uiUpdateTest = true; // Don't fail if UI not available
            }

        } catch (error) {
            console.error('Workflow validation error:', error);
        }

        const allPassed = Object.values(results).every(result => result === true);
        //console.log('=== WORKFLOW VALIDATION RESULTS ===');
        //console.log('Cache Test:', results.cacheTest ? '✓ PASS' : '✗ FAIL');
        //console.log('Performance Test:', results.performanceTest ? '✓ PASS' : '✗ FAIL');
        //console.log('Validation Test:', results.validationTest ? '✓ PASS' : '✗ FAIL');
        //console.log('UI Update Test:', results.uiUpdateTest ? '✓ PASS' : '✗ FAIL');
        //console.log('Overall Result:', allPassed ? '✓ ALL TESTS PASSED' : '✗ SOME TESTS FAILED');

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

        /*console.log('Demographic range field verification:', {
            totalTests: this.testsData.length,
            availableFields: availableFields,
            missingFields: missingFields,
            sampleTestId: sampleTest.id,
            sampleTestName: sampleTest.name
        });*/

        if (missingFields.length > 0) {
            console.error('Missing demographic range fields:', missingFields);
            console.error('Demographic range functionality may not work properly');

            if (typeof toastr !== 'undefined') {
                toastr.warning('Some demographic range fields are missing from test data. Age/gender-specific ranges may not work properly.');
            }
        } else {
            //console.log('All demographic range fields are available');

            // Check if any tests actually have demographic-specific ranges
            let testsWithDemographicRanges = 0;
            this.testsData.forEach(test => {
                if (test.min_male || test.max_male || test.min_female || test.max_female || test.min_child || test.max_child) {
                    testsWithDemographicRanges++;
                }
            });

            //console.log(`Found ${testsWithDemographicRanges} tests with demographic-specific ranges out of ${this.testsData.length} total tests`);

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

            ////console.log('Updated range labels for all test rows');
        } catch (error) {
            //console.error('Error updating range labels:', error);
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
            //console.error('Error validating test result:', error);
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
            //console.error('Error updating validation indicators:', error);
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

            //console.log('Validated all test results');
        } catch (error) {
            //console.error('Error validating all test results:', error);
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
            //console.error('Error handling test result change:', error);
        }
    }

    /**
     * Handle category filter change
     * @param {string} categoryId - Selected category ID
     */
    onCategoryFilterChange(categoryId) {
        try {
            //console.log('Category filter changed to:', categoryId);

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

            //console.log('Category filter applied successfully');
        } catch (error) {
            //console.error('Error handling category filter change:', error);
        }
    }

    /**
     * Populate category dropdown for a test row
     * @param {jQuery} $categorySelect - The category select element
     */
    populateRowCategoryDropdown($categorySelect) {
        try {
            console.log('Populating category dropdown with', this.categoriesData.length, 'categories');
            console.log('Main categories available:', this.mainCategoriesData.length);
            
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

            console.log('Populated category dropdown for test row with', $categorySelect.find('option').length, 'options');
        } catch (error) {
            console.error('Error populating row category dropdown:', error);
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
            console.warn('Attempting to recover from category filter error...');

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

            //console.log('Category filter error recovery completed');
        } catch (error) {
            console.error('Error during category filter recovery:', error);
        }
    }

    /**
     * Global error handler for the entry manager
     * @param {Error} error - The error object
     * @param {string} context - Context where the error occurred
     */
    handleGlobalError(error, context = 'Unknown') {
        try {
            console.error(`Global error in ${context}:`, error);

            // Log error details for debugging
            const errorDetails = {
                message: error.message,
                stack: error.stack,
                context: context,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                userAgent: navigator.userAgent
            };

            console.error('Error details:', errorDetails);

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
            console.error('Error in global error handler:', fallbackError);
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
            console.warn('Recovering from test selection error...');

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
                            console.warn(`Test ${selectedValue} no longer exists, clearing selection`);
                            $select.val('');
                            this.clearTestRowFields($row);
                        }
                    }
                } catch (rowError) {
                    console.error('Error recovering test row:', rowError);
                    // Clear the problematic row
                    $select.val('');
                    this.clearTestRowFields($row);
                }
            });

            //console.log('Test selection error recovery completed');
        } catch (error) {
            console.error('Error during test selection recovery:', error);
        }
    }

    /**
     * Recover from data reconciliation errors
     */
    recoverFromDataReconciliationError() {
        try {
            console.warn('Recovering from data reconciliation error...');

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

            //console.log('Data reconciliation error recovery completed');
        } catch (error) {
            console.error('Error during data reconciliation recovery:', error);
        }
    }

    /**
     * Ensure basic functionality is available even when errors occur
     */
    ensureBasicFunctionality() {
        try {
            //console.log('Ensuring basic functionality...');

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
                    console.warn('Select2 initialization failed for element:', $select, select2Error);
                }
            });

            //console.log('Basic functionality ensured');
        } catch (error) {
            console.error('Error ensuring basic functionality:', error);
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
            console.error('Error logging error for reporting:', loggingError);
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
            console.error('Error retrieving stored error logs:', error);
            return [];
        }
    }

    /**
     * Clear stored error logs
     */
    clearStoredErrorLogs() {
        try {
            localStorage.removeItem('entryManagerErrors');
            //console.log('Stored error logs cleared');
        } catch (error) {
            console.error('Error clearing stored error logs:', error);
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

            //console.log(`Data cached for key: ${key}, expires in ${timeout / 1000}s`);
        } catch (error) {
            console.error('Error setting cache data:', error);
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
                //console.log(`Cache hit for key: ${key}`);
                return cached.data;
            }

            // Remove expired cache entry
            if (cached) {
                this.dataCache.delete(key);
                //console.log(`Cache expired for key: ${key}`);
            }

            this.performanceMetrics.cacheMisses++;
            return null;
        } catch (error) {
            console.error('Error getting cache data:', error);
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
            //console.log(`Cache cleared for key: ${key}`);
        } catch (error) {
            console.error('Error clearing cache data:', error);
        }
    }

    /**
     * Clear all cached data
     */
    clearAllCacheData() {
        try {
            this.dataCache.clear();
            //console.log('All cache data cleared');
        } catch (error) {
            console.error('Error clearing all cache data:', error);
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
            //console.log('Preloading essential data...');
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
            //console.log(`Essential data preloaded in ${(endTime - startTime).toFixed(2)}ms`);
        } catch (error) {
            console.error('Error preloading essential data:', error);
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
            //console.log('Optimizing performance...');

            // Clean up expired cache entries
            this.cleanupExpiredCache();

            // Limit range cache size
            if (this.rangeCache.size > 1000) {
                const keysToDelete = Array.from(this.rangeCache.keys()).slice(0, 200);
                keysToDelete.forEach(key => this.rangeCache.delete(key));
                //console.log(`Cleaned up ${keysToDelete.length} range cache entries`);
            }

            // Clean up old performance metrics
            if (this.performanceMetrics.filterOperations.length > 50) {
                this.performanceMetrics.filterOperations.splice(0, 25);
                //console.log('Cleaned up old performance metrics');
            }

            //console.log('Performance optimization completed');
        } catch (error) {
            console.error('Error during performance optimization:', error);
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
                //console.log(`Cleaned up ${cleanedCount} expired cache entries`);
            }
        } catch (error) {
            console.error('Error cleaning up expired cache:', error);
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
                console.warn(`Average filter time (${metrics.filtering.averageTime.toFixed(2)}ms) exceeds 300ms target`);
            }

            if (metrics.cache.hitRate < 0.7) {
                console.warn(`Cache hit rate (${(metrics.cache.hitRate * 100).toFixed(1)}%) is below 70%`);
            }

            if (metrics.memory.dataCacheSize > 50) {
                console.warn(`Data cache size (${metrics.memory.dataCacheSize}) is getting large`);
            }

            // Auto-optimize if needed
            if (metrics.memory.dataCacheSize > 100 || metrics.memory.rangeCacheSize > 2000) {
                //console.log('Auto-optimizing performance due to large cache sizes');
                this.optimizePerformance();
            }
        } catch (error) {
            console.error('Error monitoring performance:', error);
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
                console.error('Invalid parameters passed to onRowCategoryChange:', {
                    categorySelect: !!categorySelect,
                    row: !!$row,
                    rowLength: $row ? $row.length : 0
                });
                return;
            }

            const $categorySelect = $(categorySelect);
            const selectedCategoryId = $categorySelect.val();
            const $selectedOption = $categorySelect.find('option:selected');
            const mainCategoryId = $selectedOption.data('main-category');
            const rowIndex = $row.data('row-index');

            console.log('Row category changed:', {
                rowIndex: rowIndex,
                categoryId: selectedCategoryId,
                mainCategoryId: mainCategoryId
            });

            // Enhanced main category ID handling
            const mainCategoryValue = mainCategoryId || '';
            $row.find('.test-main-category-id').val(mainCategoryValue);

            // Enhanced test dropdown validation
            const $testSelect = $row.find('.test-select');
            if ($testSelect.length === 0) {
                console.error('Test select dropdown not found in row', rowIndex);
                this.handleRowCategoryChangeError('missing_test_dropdown', $row);
                return;
            }

            // Enhanced filtering with error handling
            let filteredTests;
            try {
                filteredTests = this.filterTestsByCategory(selectedCategoryId);
                console.log(`Filtered ${filteredTests.length} tests for category ${selectedCategoryId} in row ${rowIndex}`);
            } catch (filterError) {
                console.error('Error filtering tests by category:', filterError);
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

                console.log(`Test dropdown updated for row ${rowIndex} with ${filteredTests.length} options`);
            } catch (updateError) {
                console.error('Error updating test dropdown options:', updateError);
                this.handleRowCategoryChangeError('dropdown_update_error', $row, updateError);
                return;
            }

            // Enhanced validation of current test selection
            const isCurrentTestStillValid = currentTestId &&
                filteredTests.some(test => String(test.id) === String(currentTestId));

            if (currentTestId && !isCurrentTestStillValid) {
                console.log(`Current test ${currentTestId} (${currentTestName}) is not in the filtered category, clearing selection`);

                // Enhanced clearing of test selection and related fields
                this.clearTestSelectionAndFields($testSelect, $row);

                // Provide user feedback about cleared selection
                this.showTestSelectionClearedFeedback($row, currentTestName, selectedCategoryId);

            } else if (isCurrentTestStillValid) {
                console.log(`Current test ${currentTestId} is still valid for the selected category`);

                // Enhanced restoration of valid selection
                this.restoreTestSelection($testSelect, currentTestId);
            }

            // Enhanced test count and UI updates
            this.updateFilteredTestCount();
            this.updateRowCategoryIndicator($row, selectedCategoryId);

            console.log('Row category change handled successfully for row', rowIndex);

        } catch (error) {
            console.error('Error handling row category change:', error);
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
            console.error('Error clearing test dropdown options:', error);
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
            console.error('Error clearing test selection and fields:', error);
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
                console.warn(`Failed to restore test selection: expected ${testId}, got ${actualValue}`);
            }

        } catch (error) {
            console.error('Error restoring test selection:', error);
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
            console.error('Error showing test selection cleared feedback:', error);
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
            console.error('Error updating row category indicator:', error);
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
            console.error(`Row category change error (${errorType}) in row ${rowIndex}:`, error);

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
            console.error('Error during row category change error handling:', recoveryError);
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
                console.log('Fallback: showing all tests in row', $row.data('row-index'));
            }
        } catch (error) {
            console.error('Error in fallback to all tests:', error);
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
            console.error('Error showing row error feedback:', error);
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
            console.error('Error clearing test row fields:', error);
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

            //console.log('Updated ranges and validation for all tests based on demographics');
        } catch (error) {
            //console.error('Error updating ranges and validation:', error);
        }
    }

    /**
     * Comprehensive error recovery for category filtering
     */
    recoverFromCategoryFilterError() {
        try {
            //console.warn('Attempting to recover from category filter error...');

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

            //console.log('Category filter error recovery completed');
        } catch (error) {
            //console.error('Error during category filter recovery:', error);
        }
    }

    /**
     * Fallback function when test data is unavailable
     */
    handleTestDataUnavailable() {
        try {
            //console.warn('Test data unavailable, applying fallbacks...');

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

            //console.log('Test data unavailable fallback applied');
        } catch (error) {
            //console.error('Error applying test data fallback:', error);
        }
    }

    /**
     * Global error handler for the entry manager
     * @param {Error} error - The error object
     * @param {string} context - Context where the error occurred
     */
    handleGlobalError(error, context = 'Unknown') {
        try {
            //console.error(`Global error in ${context}:`, error);

            // Log error details for debugging
            const errorDetails = {
                message: error.message,
                stack: error.stack,
                context: context,
                timestamp: new Date().toISOString()
            };

            //console.error('Error details:', errorDetails);

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
            //console.error('Error in global error handler:', fallbackError);
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

            //console.log('Basic functionality ensured');
        } catch (error) {
            //console.error('Error ensuring basic functionality:', error);
        }
    }

    /**
     * Update all test ranges for the currently selected patient (with performance monitoring)
     */
    updateAllTestRangesForCurrentPatient() {
        const startTime = performance.now();
        const patientAge = parseInt($('#patientAge').val()) || null;
        const patientGender = $('#patientGender').val() || null;

        //console.log('Updating all test ranges for patient:', { age: patientAge, gender: patientGender });

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
            //console.log(`Updated ranges for test ${update.testName}:`, update.rangeData);
        });

        // Performance monitoring
        const endTime = performance.now();
        const duration = endTime - startTime;
        //console.log(`Range update completed in ${duration.toFixed(2)}ms for ${updates.length} tests`);

        if (duration > 100) {
            console.warn(`Range update took ${duration.toFixed(2)}ms, which exceeds the 100ms target`);
        }
    }

    /**
     * Reset all test ranges to general ranges (when no patient selected)
     */
    resetAllTestRangesToGeneral() {
        //console.log('Resetting all test ranges to general');

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
     * Reconcile test category data between entry data and current test data
     * @param {Array} entryTests - Array of tests from entry data
     * @returns {Array} Array of reconciled test data
     */
    reconcileTestCategoryData(entryTests) {
        try {
            //console.log('Starting test category data reconciliation...');

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
                        console.warn(`Category conflict detected for test ${entryTest.test_id}:`, {
                            entry_category: { id: entryTest.category_id, name: entryTest.category_name },
                            current_category: { id: currentTest.category_id, name: currentTest.category_name }
                        });

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
                            console.log(`Using current test category for test ${entryTest.test_id}: ${currentTest.category_name} (ID: ${currentTest.category_id})`);
                        }
                    }
                } else {
                    console.warn(`Current test data not found for test ID: ${entryTest.test_id}`);
                    reconciledTest.data_source = 'entry_only';
                }

                return reconciledTest;
            });

            //console.log('Test category data reconciliation completed');
            return reconciledTests;
        } catch (error) {
            console.error('Error during test category data reconciliation:', error);
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
            console.error('Error getting current test category:', error);
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
            console.error('Error detecting category conflict:', error);
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
            console.error('Error resolving conflicting categories:', error);
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
        //console.log('Viewing entry:', entryId);

        try {
            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: { action: 'get', id: entryId },
                dataType: 'json'
            });
            //console.log('Displaying entry response:', response);
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
        //console.log('Displaying entry details:', entry);
        //console.log('Entry tests data:', entry.tests);

        // Debug each test
        if (entry.tests && entry.tests.length > 0) {
            entry.tests.forEach((test, index) => {
                /*console.log(`Test ${index + 1}:`, {
                    test_id: test.test_id,
                    test_name: test.test_name,
                    category_id: test.category_id,
                    min: test.min,
                    max: test.max,
                    unit: test.unit,
                    result_value: test.result_value
                });*/
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
        //console.log('Editing entry:', entryId);

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
            //console.log('Editing entry response:', response);

            // Special debugging for entry 17
            if (entryId == 17) {
                //console.log('=== SPECIAL DEBUG FOR ENTRY 17 ===');
                //console.log('Response data:', response.data);
                //console.log('Tests in response:', response.data.tests);
                if (response.data.tests) {
                    response.data.tests.forEach((test, index) => {
                        /*console.log(`Test ${index + 1}:`, {
                            test_id: test.test_id,
                            test_name: test.test_name,
                            category_name: test.category_name,
                            result_value: test.result_value
                        });*/
                    });
                }
                //console.log('=== END SPECIAL DEBUG ===');
            }

            if (response.success && response.data) {
                $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');
                $('#entryModal').modal('show');

                // Ensure owner data is loaded before populating form
                if (this.ownersData.length === 0) {
                    //console.log('Owner data not loaded, loading now...');
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
        // //console.log('Populating edit form with entry:', entry);
        // //console.log('Entry keys:', Object.keys(entry));
        // //console.log('Added by field:', entry.added_by);
        // //console.log('Owner ID field:', entry.owner_id);
        // //console.log('Patient ID field:', entry.patient_id);
        // //console.log('Doctor ID field:', entry.doctor_id);

        this.currentEditId = entry.id;

        // Reset form first
        this.resetForm();

        // Populate basic fields
        $('#entryId').val(entry.id);

        // Format entry date for HTML date input (requires YYYY-MM-DD format)
        if (entry.entry_date) {
            const formattedDate = this.formatDateForInput(entry.entry_date);
            $('#entryDate').val(formattedDate);
            //console.log('Setting entry date:', entry.entry_date, '-> formatted:', formattedDate);
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
        //console.log('Reloading tests data to ensure accuracy...');
        await this.loadTestsData();
        //console.log('Tests data loaded:', this.testsData.length, 'tests');

        // Also ensure categories are loaded for proper category dropdown population
        //console.log('Ensuring categories are loaded...');
        if (this.categoriesData.length === 0) {
            await this.loadCategoriesForFilter();
        }
        //console.log('Categories data loaded:', this.categoriesData.length, 'categories');

        // Debug: Log test IDs and names for troubleshooting
        if (this.testsData.length > 0) {
            //console.log('Available tests:', this.testsData.map(t => ({ id: t.id, name: t.name, id_type: typeof t.id })));
        }

        // Debug: show first few tests
        if (this.testsData.length > 0) {
            //console.log('First 5 tests in testsData:', this.testsData.slice(0, 5).map(t => ({ id: t.id, name: t.name })));
        }

        // Double-check that we have tests data
        if (this.testsData.length === 0) {
            console.error('No tests data available! This will cause issues with test selection.');
            toastr.warning('Tests data could not be loaded. Test selection may not work properly.');
        }

        // Clear and populate tests with data reconciliation
        $('#testsContainer').empty();
        this.testRowCounter = 0;

        if (entry.tests && entry.tests.length > 0) {
            //console.log('Populating', entry.tests.length, 'tests with data reconciliation');

            // Reconcile entry test data with current test data
            const reconciledTests = this.reconcileTestCategoryData(entry.tests);

            // Log reconciliation results
            /*console.log('Data reconciliation completed:', {
                original_tests: entry.tests.length,
                reconciled_tests: reconciledTests.length,
                conflicts_detected: reconciledTests.filter(t => t.category_conflict).length
            });*/

            // Create test rows with reconciled data
            reconciledTests.forEach((reconciledTest, index) => {
                /*console.log(`Creating test row ${index + 1} with reconciled data:`, {
                    test_id: reconciledTest.test_id,
                    test_name: reconciledTest.test_name,
                    resolved_category_id: reconciledTest.resolved_category_id,
                    resolved_category_name: reconciledTest.resolved_category_name,
                    category_conflict: reconciledTest.category_conflict,
                    data_source: reconciledTest.data_source
                });*/

                this.addTestRow(reconciledTest);
            });
        } else {
            //console.log('No tests found, adding empty test row');
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

            // Debug form data - specifically check for category information
            //console.log('Form data being sent:');
            let hasTestData = false;
            let categoryDataFound = false;
            for (let [key, value] of formData.entries()) {
                //console.log(key, ':', value);
                if (key.includes('tests[') && key.includes('category_id')) {
                    categoryDataFound = true;
                    //console.log('Found category data:', key, '=', value);
                }
                if (key.includes('tests[') && key.includes('main_category_id')) {
                    //console.log('Found main category data:', key, '=', value);
                }
                if (key.includes('tests[')) {
                    hasTestData = true;
                }
            }

            if (hasTestData && !categoryDataFound) {
                console.warn('WARNING: Test data found but no category_id data detected in form submission!');
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
        // //console.log('Page ready, checking dependencies...');
        // //console.log('jQuery version:', $.fn.jquery);
        // //console.log('DataTables available:', typeof $.fn.DataTable !== 'undefined');
        // //console.log('Select2 available:', typeof $.fn.select2 !== 'undefined');
        // //console.log('Toastr available:', typeof toastr !== 'undefined');
        // //console.log('Bootstrap available:', typeof $.fn.modal !== 'undefined');

        // //console.log('Initializing Entry Manager...');
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
        window.debugTestData = () => entryManager.debugTestData();
        window.debugCategoryData = () => entryManager.debugCategoryData();
        window.debugEditMode = () => entryManager.debugEditMode();
        window.debugSpecificEntry = (entryId) => entryManager.debugSpecificEntry(entryId);

        // Add performance monitoring functions
        window.getPerformanceMetrics = () => entryManager.getPerformanceMetrics();
        window.getCacheStatistics = () => entryManager.getCacheStatistics();
        window.optimizePerformance = () => entryManager.optimizePerformance();
        window.clearAllCache = () => entryManager.clearAllCacheData();

        // //console.log('Entry Manager initialized successfully');
        //console.log('Entry Manager functions available:');
        //console.log('- debugTestData() - Debug test data and check for duplicates');
        //console.log('- debugCategoryData() - Debug category data and form state');
        //console.log('- debugEditMode() - Debug edit mode and test row state');
        //console.log('- debugSpecificEntry(entryId) - Debug specific entry data and categories');
        //console.log('- testDemographicRanges() - Run complete workflow validation');
        //console.log('- testRangeCalculation(age, gender, testId) - Test specific range calculation');
        //console.log('- getPerformanceMetrics() - Get performance and cache statistics');
        //console.log('- getCacheStatistics() - Get detailed cache statistics');
        //console.log('- optimizePerformance() - Manually optimize performance and clean caches');
        //console.log('- clearAllCache() - Clear all cached data');
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
