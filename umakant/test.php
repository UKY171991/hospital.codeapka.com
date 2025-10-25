<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-vial mr-2"></i>Test Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Tests</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalTests">0</h3>
                            <p>Total Tests</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-vial"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="activeTests">0</h3>
                            <p>Active Tests</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="totalCategories">0</h3>
                            <p>Categories</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tags"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="testEntries">0</h3>
                            <p>Test Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-flask mr-1"></i>
                                Laboratory Tests
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" onclick="openAddTestModal()">
                                    <i class="fas fa-plus"></i> Add New Test
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="refreshTests()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="testCategoryAPIs()" title="Debug: Test Category APIs">
                                    <i class="fas fa-bug"></i> Debug
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-filter mr-2"></i>
                                        Filters
                                        <button class="btn btn-sm btn-outline-secondary float-right" onclick="clearFilters()">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">Category</label>
                                            <select id="categoryFilter" class="form-control">
                                                <option value="">All Categories</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Max Price (₹)</label>
                                            <input type="number" id="priceFilter" class="form-control" placeholder="Max Price">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Quick Search</label>
                                            <input type="text" id="quickSearch" class="form-control" placeholder="Search tests...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tests Table -->
                            <div class="table-responsive">
                                <table id="testManagementTable" class="table table-striped table-bordered table-hover" data-no-datatables="true">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="30"><input type="checkbox" id="selectAll"></th>
                                            <th width="50">ID</th>
                                            <th>Test Name</th>
                                            <th>Category</th>
                                            <th width="100">Price (₹)</th>
                                            <th width="120">Added By</th>
                                            <th width="120">Created At</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-spinner fa-spin mr-2"></i>Loading tests...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="testModalLabel">
                    <i class="fas fa-vial mr-2"></i>
                    <span id="modalTitle">Add New Test</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="testForm">
                <div class="modal-body">
                    <input type="hidden" id="testId" name="id">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="testName">
                                    <i class="fas fa-flask mr-1"></i>
                                    Test Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="testName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mainCategorySelect">
                                    <i class="fas fa-layer-group mr-1"></i>
                                    Main Category <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="mainCategorySelect" name="main_category_id" required>
                                    <option value="">Select Main Category</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="testCategoryId">
                                    <i class="fas fa-tags mr-1"></i>
                                    Test Category <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="testCategoryId" name="category_id" required>
                                    <option value="">Select Test Category</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="testPrice">
                                    <i class="fas fa-rupee-sign mr-1"></i>
                                    Price
                                </label>
                                <input type="number" class="form-control" id="testPrice" name="price" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="testUnit">
                                    <i class="fas fa-ruler mr-1"></i>
                                    Unit
                                </label>
                                <input type="text" class="form-control" id="testUnit" name="unit" placeholder="mg/dL, IU/L, etc.">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="testMethod">
                                    <i class="fas fa-cogs mr-1"></i>
                                    Method
                                </label>
                                <input type="text" class="form-control" id="testMethod" name="method">
                            </div>
                        </div>
                    </div>

                    <!-- Reference Ranges -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-venus-mars mr-1"></i>
                                Reference Ranges
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- General Range -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="font-weight-bold text-info">General Range:</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" id="testMin" name="min" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" id="testMax" name="max" placeholder="Max" step="0.01">
                                </div>
                            </div>

                            <!-- Male Range -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="font-weight-bold text-primary">Male Range:</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" id="testMinMale" name="min_male" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" id="testMaxMale" name="max_male" placeholder="Max" step="0.01">
                                </div>
                            </div>

                            <!-- Female Range -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="font-weight-bold text-danger">Female Range:</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" id="testMinFemale" name="min_female" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" id="testMaxFemale" name="max_female" placeholder="Max" step="0.01">
                                </div>
                            </div>

                            <!-- Child Range -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="font-weight-bold text-warning">Child Range:</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" id="testMinChild" name="min_child" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" class="form-control" id="testMaxChild" name="max_child" placeholder="Max" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Settings -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="testSubHeading">
                                    <i class="fas fa-heading mr-1"></i>
                                    Sub Heading
                                </label>
                                <select class="form-control" id="testSubHeading" name="sub_heading">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="testPrintNewPage">
                                    <i class="fas fa-print mr-1"></i>
                                    Print New Page
                                </label>
                                <select class="form-control" id="testPrintNewPage" name="print_new_page">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="testDescription">
                            <i class="fas fa-info-circle mr-1"></i>
                            Description
                        </label>
                        <textarea class="form-control" id="testDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group mt-3">
                        <label for="testReferenceRange">
                            <i class="fas fa-clipboard-list mr-1"></i>
                            Reference Range Note
                        </label>
                        <textarea class="form-control" id="testReferenceRange" name="reference_range" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" id="saveTestBtn" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Test
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Test Modal -->
<div class="modal fade" id="viewTestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewTestModalLabel">
                    <i class="fas fa-eye mr-2"></i>
                    Test Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewTestBody">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="editTestFromView()">
                    <i class="fas fa-edit mr-1"></i> Edit Test
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Constants
const TEST_CATEGORY_API = 'patho_api/test_category.php?action=';
const CURRENT_USER_ID = <?php echo (int)($_SESSION['user_id'] ?? 0); ?>;

// Global variables
let testsTable;
let testsData = [];

// Initialize page
$(document).ready(function() {
    console.log('Initializing Test Management page...');
    
    // Check if required libraries are loaded
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    
    // Ensure toastr is available with fallback
    if (typeof toastr === 'undefined') {
        console.warn('Toastr is not loaded, using alert fallback');
        window.toastr = {
            success: function(msg) { console.log('SUCCESS:', msg); },
            error: function(msg) { console.error('ERROR:', msg); },
            warning: function(msg) { console.warn('WARNING:', msg); },
            info: function(msg) { console.info('INFO:', msg); }
        };
    }
    
    // Prevent any undefined function errors
    window.onerror = function(msg, url, lineNo, columnNo, error) {
        console.error('JavaScript Error:', {
            message: msg,
            source: url,
            line: lineNo,
            column: columnNo,
            error: error
        });
        return false; // Don't suppress default browser error handling
    };
    
    try {
        // Initialize components in order
        console.log('Loading categories...');
        loadCategories();
        
        console.log('Loading statistics...');
        loadStats();
        
        console.log('Initializing table...');
        initializeTable();
        
        console.log('Setting up event handlers...');
        setupEventHandlers();
        
        console.log('Test Management page initialized successfully');
    } catch (error) {
        console.error('Error initializing Test Management page:', error);
        if (window.toastr) {
            toastr.error('Error initializing page: ' + error.message);
        } else {
            alert('Error initializing page: ' + error.message);
        }
    }
});

// Initialize simple table without DataTables
function initializeTable() {
    console.log('Initializing table...');
    
    // Check if table has correct structure
    const tableHeaders = $('#testManagementTable thead th').length;
    console.log('Table headers count:', tableHeaders);
    
    if (tableHeaders !== 8) {
        console.warn('Table structure mismatch, fixing...');
        fixTableStructure();
    }
    
    loadTests();
}

// Fix table structure if needed
function fixTableStructure() {
    console.log('Fixing table structure...');
    
    const correctHeaders = `
        <thead class="thead-dark">
            <tr>
                <th width="30"><input type="checkbox" id="selectAll"></th>
                <th width="50">ID</th>
                <th>Test Name</th>
                <th>Category</th>
                <th width="100">Price (₹)</th>
                <th width="120">Added By</th>
                <th width="120">Created At</th>
                <th width="120">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin mr-2"></i>Loading tests...
                </td>
            </tr>
        </tbody>
    `;
    
    $('#testManagementTable').html(correctHeaders);
    console.log('Table structure fixed');
}

// Load tests data
function loadTests() {
    console.log('Loading tests...');
    
    // Show loading state
    $('#testManagementTable tbody').html('<tr><td colspan="8" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading tests...</td></tr>');
    
    // Ensure we have a valid table element
    if (!$('#testManagementTable').length) {
        console.error('Test management table not found');
        return;
    }
    
    $.ajax({
        url: 'ajax/test_api.php?action=list',
        type: 'GET',
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            console.log('Tests API response:', response);
            
            try {
                if (response && response.success === true && Array.isArray(response.data)) {
                    testsData = response.data;
                    renderTable(testsData);
                    console.log('Tests loaded successfully, count:', testsData.length);
                } else if (response && response.success === false) {
                    console.error('API returned error:', response.message);
                    showTableError('Server error: ' + (response.message || 'Unknown error'));
                } else {
                    console.error('Invalid response format:', response);
                    showTableError('Invalid data format received from server');
                }
            } catch (e) {
                console.error('Error processing response:', e);
                showTableError('Error processing server response');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error Details:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error,
                readyState: xhr.readyState
            });
            
            let errorMessage = 'Failed to load test data';
            
            if (xhr.status === 0) {
                errorMessage = 'Network connection error. Please check your internet connection.';
            } else if (xhr.status === 404) {
                errorMessage = 'API endpoint not found. Please contact administrator.';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error occurred. Please try again later.';
            } else if (xhr.status === 403) {
                errorMessage = 'Access denied. Please check your permissions.';
            } else if (status === 'timeout') {
                errorMessage = 'Request timed out. Please try again.';
            } else if (xhr.responseText) {
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch (e) {
                    // Response is not JSON, use default message
                }
            }
            
            showTableError(errorMessage);
        }
    });
}

// Render table with data
function renderTable(data) {
    console.log('Rendering table with data:', data);
    
    let html = '';
    
    try {
        if (!data || !Array.isArray(data) || data.length === 0) {
            html = '<tr><td colspan="8" class="text-center py-4"><i class="fas fa-info-circle text-muted mr-2"></i>No tests found</td></tr>';
        } else {
            data.forEach(function(test, index) {
                if (!test || !test.id) {
                    console.warn('Invalid test data at index', index, test);
                    return;
                }
                
                let categoryHtml = '';
                if (test.main_category_name) {
                    categoryHtml += `<span class="badge badge-secondary badge-sm">${escapeHtml(test.main_category_name)}</span><br>`;
                }
                if (test.category_name) {
                    categoryHtml += `<span class="badge badge-info">${escapeHtml(test.category_name)}</span>`;
                } else {
                    categoryHtml += '<span class="text-muted">No Category</span>';
                }
                
                const testName = escapeHtml(test.name || 'N/A');
                const testDescription = test.description ? escapeHtml(test.description.substring(0, 50)) + (test.description.length > 50 ? '...' : '') : '';
                const testPrice = test.price ? parseFloat(test.price).toFixed(0) : '0';
                const safeName = (test.name || '').replace(/'/g, '\\\'');
                
                // Format added by and created at
                const addedBy = test.added_by_username ? escapeHtml(test.added_by_username) : '<span class="text-muted">Unknown</span>';
                const createdAt = test.created_at ? formatDateTime(test.created_at) : '<span class="text-muted">N/A</span>';
                
                html += `
                    <tr data-test-id="${test.id}">
                        <td class="text-center"><input type="checkbox" class="test-checkbox" value="${test.id}"></td>
                        <td class="text-center">${test.id}</td>
                        <td>
                            <strong class="text-primary">${testName}</strong>
                            ${testDescription ? `<br><small class="text-muted">${testDescription}</small>` : ''}
                        </td>
                        <td>${categoryHtml}</td>
                        <td class="text-right"><strong class="text-success">₹${testPrice}</strong></td>
                        <td class="text-center">${addedBy}</td>
                        <td class="text-center">${createdAt}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-info btn-sm" onclick="viewTest(${test.id})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning btn-sm" onclick="editTest(${test.id})" title="Edit Test">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteTest(${test.id}, '${safeName}')" title="Delete Test">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#testManagementTable tbody').html(html);
        setupCheckboxEvents();
        console.log('Table rendered successfully with', data ? data.length : 0, 'rows');
        
    } catch (error) {
        console.error('Error rendering table:', error);
        $('#testManagementTable tbody').html('<tr><td colspan="8" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mr-2"></i>Error rendering table data</td></tr>');
    }
}

// Setup event handlers
function setupEventHandlers() {
    // Form submission
    $('#testForm').on('submit', function(e) {
        e.preventDefault();
        saveTestData();
    });
    
    // Main category change
    $('#mainCategorySelect').on('change', function() {
        const mainCategoryId = $(this).val();
        loadTestCategoriesByMain(mainCategoryId);
    });

    // Quick search
    $('#quickSearch').on('keyup', function() {
        applyFilters();
    });

    // Filter changes
    $('#categoryFilter, #priceFilter').on('change keyup', function() {
        applyFilters();
    });
}

// Setup checkbox events
function setupCheckboxEvents() {
    try {
        // Remove existing handlers
        $('#selectAll').off('change.testTable');
        $(document).off('change.testTable', '.test-checkbox');
        
        // Select all handler
        $('#selectAll').on('change.testTable', function() {
            $('.test-checkbox').prop('checked', $(this).is(':checked'));
        });
        
        // Individual checkbox handler
        $(document).on('change.testTable', '.test-checkbox', function() {
            const totalCheckboxes = $('.test-checkbox').length;
            const checkedCheckboxes = $('.test-checkbox:checked').length;
            
            if (checkedCheckboxes === 0) {
                $('#selectAll').prop('indeterminate', false).prop('checked', false);
            } else if (checkedCheckboxes === totalCheckboxes) {
                $('#selectAll').prop('indeterminate', false).prop('checked', true);
            } else {
                $('#selectAll').prop('indeterminate', true);
            }
        });
    } catch (error) {
        console.error('Error setting up checkbox events:', error);
    }
}

// Apply filters
function applyFilters() {
    const categoryFilter = $('#categoryFilter').val().toLowerCase();
    const priceFilter = parseFloat($('#priceFilter').val()) || 0;
    const searchFilter = $('#quickSearch').val().toLowerCase();
    
    let filteredData = testsData.filter(function(test) {
        // Category filter
        if (categoryFilter && test.category_name && !test.category_name.toLowerCase().includes(categoryFilter)) {
            return false;
        }
        
        // Price filter
        if (priceFilter > 0 && parseFloat(test.price || 0) > priceFilter) {
            return false;
        }
        
        // Search filter
        if (searchFilter) {
            const searchText = (test.name || '').toLowerCase() + ' ' + (test.description || '').toLowerCase();
            if (!searchText.includes(searchFilter)) {
                return false;
            }
        }
        
        return true;
    });
    
    renderTable(filteredData);
}

// Clear filters
function clearFilters() {
    $('#categoryFilter').val('');
    $('#priceFilter').val('');
    $('#quickSearch').val('');
    renderTable(testsData);
}

// Load categories
function loadCategories() {
    console.log('Loading main categories...');
    
    // Ensure required elements exist
    if (!$('#mainCategorySelect').length) {
        console.warn('Main category select element not found, will retry when modal opens');
    }
    if (!$('#categoryFilter').length) {
        console.warn('Category filter element not found');
    }
    
    // Load main categories
    $.ajax({
        url: 'ajax/main_test_category_api.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Main categories response:', response);
            
            if (response && response.success && Array.isArray(response.data)) {
                let modalOptions = '<option value="">Select Main Category</option>';
                
                response.data.forEach(category => {
                    console.log('Main category found:', category.name, 'ID:', category.id);
                    if (category && category.id && category.name) {
                        modalOptions += `<option value="${category.id}">${escapeHtml(category.name)}</option>`;
                    }
                });
                
                if ($('#mainCategorySelect').length) {
                    $('#mainCategorySelect').html(modalOptions);
                    console.log('Main categories loaded successfully');
                } else {
                    console.warn('Main category select element not found when trying to populate');
                }
            } else {
                console.warn('Invalid main categories response:', response);
                $('#mainCategorySelect').html('<option value="">Error loading categories</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load main categories:', {xhr, status, error});
            $('#mainCategorySelect').html('<option value="">Error loading categories</option>');
        }
    });

    console.log('Loading test categories...');
    
    // Load test categories for filter
    $.ajax({
        url: TEST_CATEGORY_API + 'list&secret_key=hospital-api-secret-2024',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Test categories response:', response);
            
            if (response && response.success && Array.isArray(response.data)) {
                let filterOptions = '<option value="">All Categories</option>';

                response.data.forEach(category => {
                    if (category && category.name) {
                        filterOptions += `<option value="${escapeHtml(category.name)}">${escapeHtml(category.name)}</option>`;
                    }
                });

                if ($('#categoryFilter').length) {
                    $('#categoryFilter').html(filterOptions);
                    console.log('Test categories loaded successfully');
                } else {
                    console.warn('Category filter element not found when trying to populate');
                }
            } else {
                console.warn('Invalid test categories response:', response);
                $('#categoryFilter').html('<option value="">Error loading categories</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load test categories:', {xhr, status, error});
            $('#categoryFilter').html('<option value="">Error loading categories</option>');
        }
    });
}

// Load test categories by main category
function loadTestCategoriesByMain(mainCategoryId) {
    console.log('Loading test categories for main category:', mainCategoryId);
    
    if (!mainCategoryId) {
        $('#testCategoryId').html('<option value="">Select Test Category</option>');
        return;
    }

    // Show loading state
    $('#testCategoryId').html('<option value="">Loading categories...</option>');

    $.ajax({
        url: TEST_CATEGORY_API + 'list&secret_key=hospital-api-secret-2024',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Test categories by main category response:', response);
            
            if (response && response.success && Array.isArray(response.data)) {
                let options = '<option value="">Select Test Category</option>';
                let foundCategories = 0;
                
                response.data.forEach(category => {
                    console.log('Checking category:', category.name, 'main_category_id:', category.main_category_id, 'vs selected:', mainCategoryId);
                    if (category && category.main_category_id == mainCategoryId && category.id && category.name) {
                        options += `<option value="${category.id}">${escapeHtml(category.name)}</option>`;
                        foundCategories++;
                        console.log('Added category:', category.name);
                    }
                });

                if ($('#testCategoryId').length) {
                    $('#testCategoryId').html(options);
                    console.log(`Found ${foundCategories} categories for main category ${mainCategoryId}`);
                } else {
                    console.warn('Test category select element not found when trying to populate');
                }
                
                if (foundCategories === 0) {
                    $('#testCategoryId').html('<option value="">No categories found</option>');
                }
            } else {
                console.warn('Invalid response for test categories:', response);
                $('#testCategoryId').html('<option value="">Error loading categories</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load test categories by main category:', {xhr, status, error});
            $('#testCategoryId').html('<option value="">Error loading categories</option>');
        }
    });
}

// Load main categories for edit (with callback to set selected values)
function loadMainCategoriesForEdit(selectedMainCategoryId, selectedCategoryId) {
    console.log('Loading main categories for edit - main:', selectedMainCategoryId, 'category:', selectedCategoryId);
    
    $.ajax({
        url: 'ajax/main_test_category_api.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Main categories for edit response:', response);
            
            if (response && response.success && Array.isArray(response.data)) {
                let modalOptions = '<option value="">Select Main Category</option>';
                let mainCategoryFound = false;
                
                response.data.forEach(category => {
                    if (category && category.id && category.name) {
                        const isSelected = selectedMainCategoryId && category.id == selectedMainCategoryId;
                        modalOptions += `<option value="${category.id}"${isSelected ? ' selected' : ''}>${escapeHtml(category.name)}</option>`;
                        if (isSelected) {
                            mainCategoryFound = true;
                        }
                    }
                });
                
                $('#mainCategorySelect').html(modalOptions);
                
                // If we have a selected main category ID but didn't find it, try setting it anyway
                if (selectedMainCategoryId && !mainCategoryFound) {
                    $('#mainCategorySelect').val(selectedMainCategoryId);
                }
                
                console.log('Main categories loaded for edit, found:', mainCategoryFound);
                
                // Now load test categories if we have a main category
                if (selectedMainCategoryId) {
                    loadTestCategoriesByMainForEdit(selectedMainCategoryId, selectedCategoryId);
                }
            } else {
                console.warn('Invalid main categories response:', response);
                $('#mainCategorySelect').html('<option value="">Error loading categories</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load main categories for edit:', {xhr, status, error});
            $('#mainCategorySelect').html('<option value="">Error loading categories</option>');
        }
    });
}

// Load test categories synchronously for edit
function loadTestCategoriesForEditSync(mainCategoryId, selectedCategoryId) {
    console.log('Loading test categories synchronously - main:', mainCategoryId, 'selected:', selectedCategoryId);
    
    if (!mainCategoryId) {
        $('#testCategoryId').html('<option value="">Select Test Category</option>');
        return;
    }

    // Show loading state
    $('#testCategoryId').html('<option value="">Loading categories...</option>');

    $.ajax({
        url: TEST_CATEGORY_API + 'list&secret_key=hospital-api-secret-2024',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Test categories sync response:', response);
            
            if (response && response.success && Array.isArray(response.data)) {
                let options = '<option value="">Select Test Category</option>';
                let foundCategories = 0;
                let categoryFound = false;
                
                response.data.forEach(category => {
                    if (category && category.main_category_id == mainCategoryId && category.id && category.name) {
                        const isSelected = selectedCategoryId && category.id == selectedCategoryId;
                        options += `<option value="${category.id}"${isSelected ? ' selected' : ''}>${escapeHtml(category.name)}</option>`;
                        foundCategories++;
                        if (isSelected) {
                            categoryFound = true;
                        }
                    }
                });

                $('#testCategoryId').html(options);
                
                // Set the selected value if not already set by the selected attribute
                if (selectedCategoryId && !categoryFound) {
                    $('#testCategoryId').val(selectedCategoryId);
                }
                
                console.log(`Sync loaded ${foundCategories} categories, selected: ${selectedCategoryId}, found: ${categoryFound}`);
                console.log('Test category value is now:', $('#testCategoryId').val());
                
                if (foundCategories === 0) {
                    $('#testCategoryId').html('<option value="">No categories found</option>');
                }
            } else {
                console.warn('Invalid response for test categories:', response);
                $('#testCategoryId').html('<option value="">Error loading categories</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load test categories sync:', {xhr, status, error});
            $('#testCategoryId').html('<option value="">Error loading categories</option>');
        }
    });
}

// Load test categories by main category for edit (with callback to set selected value)
function loadTestCategoriesByMainForEdit(mainCategoryId, selectedCategoryId) {
    console.log('Loading test categories for edit - main category:', mainCategoryId, 'selected:', selectedCategoryId);
    
    if (!mainCategoryId) {
        $('#testCategoryId').html('<option value="">Select Test Category</option>');
        return;
    }

    // Show loading state
    $('#testCategoryId').html('<option value="">Loading categories...</option>');

    $.ajax({
        url: TEST_CATEGORY_API + 'list&secret_key=hospital-api-secret-2024',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Test categories for edit response:', response);
            
            if (response && response.success && Array.isArray(response.data)) {
                let options = '<option value="">Select Test Category</option>';
                let foundCategories = 0;
                let categoryFound = false;
                
                response.data.forEach(category => {
                    if (category && category.main_category_id == mainCategoryId && category.id && category.name) {
                        const isSelected = selectedCategoryId && category.id == selectedCategoryId;
                        options += `<option value="${category.id}"${isSelected ? ' selected' : ''}>${escapeHtml(category.name)}</option>`;
                        foundCategories++;
                        if (isSelected) {
                            categoryFound = true;
                        }
                    }
                });

                $('#testCategoryId').html(options);
                
                // If we have a selected category ID but didn't find it, try setting it anyway
                if (selectedCategoryId && !categoryFound) {
                    $('#testCategoryId').val(selectedCategoryId);
                }
                
                console.log(`Found ${foundCategories} categories for main category ${mainCategoryId}, selected: ${selectedCategoryId}, found: ${categoryFound}`);
                
                if (foundCategories === 0) {
                    $('#testCategoryId').html('<option value="">No categories found</option>');
                }
            } else {
                console.warn('Invalid response for test categories:', response);
                $('#testCategoryId').html('<option value="">Error loading categories</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load test categories for edit:', {xhr, status, error});
            $('#testCategoryId').html('<option value="">Error loading categories</option>');
        }
    });
}

// Load stats
function loadStats() {
    console.log('Loading statistics...');
    
    // Ensure stats elements exist
    if (!$('#totalTests').length || !$('#activeTests').length || !$('#totalCategories').length || !$('#testEntries').length) {
        console.error('Required stats elements not found');
        return;
    }
    
    $.ajax({
        url: 'ajax/test_api.php?action=stats',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Stats response:', response);
            
            try {
                if (response && response.success && response.data) {
                    $('#totalTests').text(response.data.total || 0);
                    $('#activeTests').text(response.data.active || 0);
                    $('#totalCategories').text(response.data.categories || 0);
                    $('#testEntries').text(response.data.entries || 0);
                    console.log('Statistics loaded successfully');
                } else {
                    console.warn('Invalid stats response:', response);
                    setDefaultStats();
                }
            } catch (e) {
                console.error('Error processing stats:', e);
                setDefaultStats();
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load stats:', {xhr, status, error});
            setDefaultStats();
        }
    });
}

// Set default stats values
function setDefaultStats() {
    $('#totalTests').text('0');
    $('#activeTests').text('0');
    $('#totalCategories').text('0');
    $('#testEntries').text('0');
}

// Open add test modal
function openAddTestModal() {
    $('#testForm')[0].reset();
    $('#testId').val('');
    $('#modalTitle').text('Add New Test');
    
    // Ensure categories are loaded before showing modal
    console.log('Loading categories for new test modal...');
    loadCategories();
    
    $('#testModal').modal('show');
}

// Edit test
function editTest(id) {
    console.log('Editing test with ID:', id);
    
    if (!id) {
        toastr.error('Invalid test ID');
        return;
    }
    
    // First, ensure categories are loaded, then get test data
    ensureCategoriesLoadedThenEdit(id);
}

// Ensure categories are loaded before editing
function ensureCategoriesLoadedThenEdit(testId) {
    console.log('Ensuring categories are loaded before editing test:', testId);
    
    // Load main categories first
    $.ajax({
        url: 'ajax/main_test_category_api.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        timeout: 10000,
        success: function(categoryResponse) {
            console.log('Categories loaded for edit:', categoryResponse);
            
            if (categoryResponse && categoryResponse.success && Array.isArray(categoryResponse.data)) {
                // Populate main categories
                let modalOptions = '<option value="">Select Main Category</option>';
                categoryResponse.data.forEach(category => {
                    if (category && category.id && category.name) {
                        modalOptions += `<option value="${category.id}">${escapeHtml(category.name)}</option>`;
                    }
                });
                $('#mainCategorySelect').html(modalOptions);
                
                // Now get the test data
                $.ajax({
                    url: 'ajax/test_api.php',
                    type: 'GET',
                    data: {action: 'get', id: testId},
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        console.log('Edit test response:', response);
                        
                        if (response && response.success && response.data) {
                            const test = response.data;
                            
                            // Reset form first
                            $('#testForm')[0].reset();
                            
                            // Populate basic form fields
                            $('#testId').val(test.id);
                            $('#testName').val(test.name || '');
                            $('#testPrice').val(test.price || '');
                            $('#testUnit').val(test.unit || '');
                            $('#testMethod').val(test.method || '');
                            
                            console.log('Setting main category to:', test.main_category_id);
                            
                            // Set main category (categories are now loaded)
                            if (test.main_category_id) {
                                $('#mainCategorySelect').val(test.main_category_id);
                                console.log('Main category set, value is now:', $('#mainCategorySelect').val());
                                
                                // Load and set test categories
                                loadTestCategoriesForEditSync(test.main_category_id, test.category_id);
                            } else {
                                $('#mainCategorySelect').val('');
                                $('#testCategoryId').html('<option value="">Select Test Category</option>');
                            }
                            
                            // Set range values
                            $('#testMin').val(test.min || '');
                            $('#testMax').val(test.max || '');
                            $('#testMinMale').val(test.min_male || '');
                            $('#testMaxMale').val(test.max_male || '');
                            $('#testMinFemale').val(test.min_female || '');
                            $('#testMaxFemale').val(test.max_female || '');
                            $('#testMinChild').val(test.min_child || '');
                            $('#testMaxChild').val(test.max_child || '');
                            
                            // Unit is set in the main unit field at the top
                            
                            // Set other fields with proper type conversion
                            $('#testSubHeading').val(String(test.sub_heading || 0));
                            $('#testPrintNewPage').val(String(test.print_new_page || 0));
                            $('#testDescription').val(test.description || '');
                            $('#testReferenceRange').val(test.reference_range || '');
                            
                            // Show modal
                            $('#modalTitle').text('Edit Test');
                            $('#testModal').modal('show');
                            
                            // Final verification after modal is shown
                            setTimeout(function() {
                                console.log('Final verification - select field values:', {
                                    mainCategory: $('#mainCategorySelect').val(),
                                    testCategory: $('#testCategoryId').val(),
                                    expectedMain: test.main_category_id,
                                    expectedTest: test.category_id
                                });
                                
                                // Force set values one more time if they're not correct
                                if (test.main_category_id && $('#mainCategorySelect').val() != test.main_category_id) {
                                    console.log('Force setting main category');
                                    $('#mainCategorySelect').val(test.main_category_id);
                                }
                                
                                if (test.category_id && $('#testCategoryId').val() != test.category_id) {
                                    console.log('Force setting test category');
                                    $('#testCategoryId').val(test.category_id);
                                }
                            }, 200);
                            
                            console.log('Edit form populated and modal shown');
                            
                        } else {
                            console.error('Invalid edit response:', response);
                            toastr.error('Error loading test data: ' + (response.message || 'Invalid response'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to load test for editing:', {xhr, status, error});
                        toastr.error('Failed to load test data. Please try again.');
                    }
                });
            } else {
                console.error('Failed to load categories for edit');
                toastr.error('Failed to load categories. Please try again.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load categories for edit:', {xhr, status, error});
            toastr.error('Failed to load categories. Please try again.');
        }
    });
}

// Save test data
function saveTestData() {
    console.log('Saving test data...');
    
    // Validate required fields
    const testName = $('#testName').val().trim();
    const mainCategory = $('#mainCategorySelect').val();
    const testCategory = $('#testCategoryId').val();
    
    if (!testName) {
        toastr.error('Test name is required');
        $('#testName').focus();
        return;
    }
    
    if (!mainCategory || mainCategory === '' || mainCategory === '0') {
        toastr.error('Main category is required');
        $('#mainCategorySelect').focus();
        return;
    }
    
    if (!testCategory) {
        toastr.error('Test category is required');
        $('#testCategoryId').focus();
        return;
    }
    
    const formData = new FormData($('#testForm')[0]);
    const id = $('#testId').val();
    formData.append('action', 'save');
    
    // Debug: Log form data being sent
    console.log('Form data being sent:', {
        testName: testName,
        mainCategory: mainCategory,
        testCategory: testCategory,
        id: id
    });
    
    // Debug: Log all form data entries
    for (let [key, value] of formData.entries()) {
        console.log('FormData:', key, '=', value);
    }
    
    const submitBtn = $('#saveTestBtn');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: 'ajax/test_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        timeout: 15000,
        success: function(response) {
            console.log('Save response:', response);
            
            try {
                if (response && response.success) {
                    toastr.success(id ? 'Test updated successfully!' : 'Test added successfully!');
                    $('#testModal').modal('hide');
                    loadTests(); // Reload table
                    loadStats(); // Reload stats
                    console.log('Test saved successfully');
                } else {
                    console.error('Save failed:', response);
                    toastr.error('Error: ' + (response.message || 'Unknown error occurred'));
                }
            } catch (error) {
                console.error('Error processing save response:', error);
                toastr.error('Error processing server response');
            }
        },
        error: function(xhr, status, error) {
            console.error('Save request failed:', {xhr, status, error});
            
            let errorMessage = 'Failed to save test';
            
            if (xhr.status === 0) {
                errorMessage = 'Network connection error';
            } else if (xhr.status === 500) {
                errorMessage = 'Server error occurred';
            } else if (status === 'timeout') {
                errorMessage = 'Request timed out';
            } else if (xhr.responseText) {
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            toastr.error(errorMessage);
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

// Delete test
function deleteTest(id, name) {
    if (confirm(`Are you sure you want to delete test "${name || 'this test'}"?`)) {
        $.ajax({
            url: 'ajax/test_api.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                if (response.success) {
                    toastr.success('Test deleted successfully!');
                    loadTests(); // Reload table
                    loadStats(); // Reload stats
                } else {
                    toastr.error('Error deleting test: ' + (response.message || 'Unknown error'));
                }
            },
            error: function() {
                toastr.error('Failed to delete test');
            }
        });
    }
}

// View test
function viewTest(id) {
    $.get('ajax/test_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                const d = response.data;
                let html = '';
                
                html += '<div class="container-fluid p-0">';
                
                // Test Header
                html += '<div class="row mb-3">';
                html += '<div class="col-12">';
                html += `  <h4 class="mb-1 text-primary">${d.name || ''} <small class="text-muted">#${d.id || ''}</small></h4>`;
                if(d.description) html += `<p class="text-muted mb-0">${d.description}</p>`;
                html += '</div>';
                html += '</div>';
                
                // Main Information Cards
                html += '<div class="row">';
                
                // Left column: Test Information
                html += '<div class="col-md-6 mb-3">';
                html += '  <div class="card h-100">';
                html += '    <div class="card-header bg-primary text-white py-2">';
                html += '      <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Test Information</h6>';
                html += '    </div>';
                html += '    <div class="card-body">';
                html += '      <table class="table table-sm table-borderless mb-0">';
                html += `        <tr><td class="font-weight-bold" width="40%">Main Category:</td><td><span class="badge badge-secondary">${d.main_category_name||'N/A'}</span></td></tr>`;
                html += `        <tr><td class="font-weight-bold">Test Category:</td><td><span class="badge badge-info">${d.category_name||'N/A'}</span></td></tr>`;
                html += `        <tr><td class="font-weight-bold">Price:</td><td><span class="text-success font-weight-bold">₹${d.price||'0'}</span></td></tr>`;
                html += `        <tr><td class="font-weight-bold">Unit:</td><td><code>${d.unit||'N/A'}</code></td></tr>`;
                html += `        <tr><td class="font-weight-bold">Method:</td><td>${d.method||'N/A'}</td></tr>`;
                html += `        <tr><td class="font-weight-bold">Test Code:</td><td><code>${d.test_code||'N/A'}</code></td></tr>`;
                html += '      </table>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';

                // Right column: Settings & Metadata
                html += '<div class="col-md-6 mb-3">';
                html += '  <div class="card h-100">';
                html += '    <div class="card-header bg-secondary text-white py-2">';
                html += '      <h6 class="mb-0"><i class="fas fa-cogs mr-2"></i>Settings & Metadata</h6>';
                html += '    </div>';
                html += '    <div class="card-body">';
                html += '      <table class="table table-sm table-borderless mb-0">';
                html += `        <tr><td class="font-weight-bold" width="40%">Sub Heading:</td><td><span class="badge ${d.sub_heading ? 'badge-success">Yes' : 'badge-secondary">No'}</span></td></tr>`;
                html += `        <tr><td class="font-weight-bold">Print New Page:</td><td><span class="badge ${d.print_new_page ? 'badge-success">Yes' : 'badge-secondary">No'}</span></td></tr>`;
                html += `        <tr><td class="font-weight-bold">Added By:</td><td>${d.added_by_username||'N/A'}</td></tr>`;
                html += `        <tr><td class="font-weight-bold">Created:</td><td>${d.created_at||'N/A'}</td></tr>`;
                html += `        <tr><td class="font-weight-bold">Updated:</td><td>${d.updated_at||'N/A'}</td></tr>`;
                html += '        <tr><td class="font-weight-bold">Status:</td><td><span class="badge badge-success">Active</span></td></tr>';
                html += '      </table>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';

                html += '</div>'; // row

                // Reference Ranges
                html += '<div class="row">';
                html += '<div class="col-12">';
                html += '  <div class="card">';
                html += '    <div class="card-header bg-info text-white py-2">';
                html += '      <h6 class="mb-0"><i class="fas fa-chart-line mr-2"></i>Reference Ranges</h6>';
                html += '    </div>';
                html += '    <div class="card-body p-0">';
                html += '      <div class="table-responsive">';
                html += '      <table class="table table-sm table-hover mb-0">';
                html += '        <thead class="thead-light">';
                html += '          <tr><th>Scope</th><th>Min Value</th><th>Max Value</th><th>Unit</th></tr>';
                html += '        </thead>';
                html += '        <tbody>';
                
                let hasRanges = false;
                
                // General range
                if (d.min !== null || d.max !== null) {
                    html += `          <tr><td><strong>General</strong></td><td>${d.min||'-'}</td><td>${d.max||'-'}</td><td>${d.unit||'-'}</td></tr>`;
                    hasRanges = true;
                }
                
                // Male range
                if (d.min_male !== null || d.max_male !== null) {
                    html += `          <tr><td><strong class="text-primary"><i class="fas fa-mars mr-1"></i>Male</strong></td><td>${d.min_male||'-'}</td><td>${d.max_male||'-'}</td><td>${d.male_unit||d.unit||'-'}</td></tr>`;
                    hasRanges = true;
                }
                
                // Female range
                if (d.min_female !== null || d.max_female !== null) {
                    html += `          <tr><td><strong class="text-danger"><i class="fas fa-venus mr-1"></i>Female</strong></td><td>${d.min_female||'-'}</td><td>${d.max_female||'-'}</td><td>${d.female_unit||d.unit||'-'}</td></tr>`;
                    hasRanges = true;
                }
                
                // Child range
                if (d.min_child !== null || d.max_child !== null) {
                    html += `          <tr><td><strong class="text-warning"><i class="fas fa-child mr-1"></i>Child</strong></td><td>${d.min_child||'-'}</td><td>${d.max_child||'-'}</td><td>${d.child_unit||d.unit||'-'}</td></tr>`;
                    hasRanges = true;
                }
                
                // If no ranges defined
                if (!hasRanges) {
                    html += '          <tr><td colspan="4" class="text-center text-muted py-3"><i class="fas fa-info-circle mr-2"></i>No reference ranges defined</td></tr>';
                }
                
                html += '        </tbody>';
                html += '      </table>';
                html += '      </div>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';
                html += '</div>';

                // Reference Notes (if available)
                if(d.reference_range){ 
                    html += '<div class="row mt-3">';
                    html += '<div class="col-12">';
                    html += '  <div class="card">';
                    html += '    <div class="card-header bg-warning text-dark py-2">';
                    html += '      <h6 class="mb-0"><i class="fas fa-sticky-note mr-2"></i>Reference Notes</h6>';
                    html += '    </div>';
                    html += '    <div class="card-body">';
                    html += `      <p class="mb-0">${d.reference_range}</p>`;
                    html += '    </div>';
                    html += '  </div>';
                    html += '</div>';
                    html += '</div>';
                }

                html += '</div>'; // container-fluid

                $('#viewTestBody').html(html);
                $('#viewTestModalLabel').text(`Test Details: ${d.name || ''}`);
                
                // Store test ID for edit function
                $('#viewTestModal').data('test-id', id);
                $('#viewTestModal').modal('show');
            } else {
                toastr.error('Test not found');
            }
        })
        .fail(function() {
            toastr.error('Failed to load test details');
        });
}

// Edit test from view modal
function editTestFromView() {
    const testId = $('#viewTestModal').data('test-id');
    if (testId) {
        $('#viewTestModal').modal('hide');
        editTest(testId);
    }
}

// Refresh tests
function refreshTests() {
    loadTests();
}

// Show table error
function showTableError(message) {
    $('#testManagementTable tbody').html(`
        <tr>
            <td colspan="8" class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>${message}
                <br><br>
                <button class="btn btn-primary btn-sm" onclick="refreshTests()">
                    <i class="fas fa-refresh mr-1"></i>Try Again
                </button>
            </td>
        </tr>
    `);
}

// HTML escape function
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    var s = String(text);
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return s.replace(/[&<>\"']/g, function(m) { return map[m]; });
}

// Format datetime function
function formatDateTime(dateTimeString) {
    if (!dateTimeString) return 'N/A';
    
    try {
        const date = new Date(dateTimeString);
        if (isNaN(date.getTime())) return 'Invalid Date';
        
        const options = {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        };
        
        return date.toLocaleDateString('en-US', options);
    } catch (error) {
        console.error('Error formatting date:', error);
        return 'Invalid Date';
    }
}

// Debug function to test API calls manually
function testCategoryAPIs() {
    console.log('=== Testing Category APIs ===');
    
    // Test main categories
    $.ajax({
        url: 'ajax/main_test_category_api.php?action=list',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Main Categories API Response:', response);
        },
        error: function(xhr, status, error) {
            console.error('Main Categories API Error:', {xhr, status, error});
        }
    });
    
    // Test test categories
    $.ajax({
        url: 'patho_api/test_category.php?action=list&secret_key=hospital-api-secret-2024',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Test Categories API Response:', response);
        },
        error: function(xhr, status, error) {
            console.error('Test Categories API Error:', {xhr, status, error});
        }
    });
}

// Debug function to manually populate dropdowns
function testPopulateDropdowns() {
    console.log('=== Testing Dropdown Population ===');
    
    // Check if elements exist
    console.log('Main category select exists:', $('#mainCategorySelect').length > 0);
    console.log('Test category select exists:', $('#testCategoryId').length > 0);
    console.log('Category filter exists:', $('#categoryFilter').length > 0);
    
    // Try to populate with test data
    $('#mainCategorySelect').html('<option value="">Select Main Category</option><option value="1">Test Main Category</option>');
    $('#testCategoryId').html('<option value="">Select Test Category</option><option value="1">Test Category</option>');
    
    console.log('Test data populated');
}
</script>

<style>
.small-box {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.badge-sm {
    font-size: 0.7em;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

#testManagementTable {
    font-size: 0.9rem;
}

#testManagementTable thead th {
    background-color: #343a40;
    color: white;
    border-color: #454d55;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 12px 8px;
}

#testManagementTable tbody td {
    vertical-align: middle;
    padding: 10px 8px;
    border-color: #dee2e6;
}

#testManagementTable tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.btn-outline-info:hover {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.btn-outline-warning:hover {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
}

.table-responsive {
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.badge {
    font-size: 0.75em;
    padding: 0.375em 0.75em;
}

code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

#viewTestModal .modal-dialog {
    max-width: 900px;
}

#viewTestModal .card {
    border: 1px solid #dee2e6;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

#viewTestModal .card-header {
    font-weight: 600;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

#viewTestModal .table td {
    padding: 0.5rem 0.75rem;
    border: none;
    border-bottom: 1px solid #f8f9fa;
}

#viewTestModal .table tr:last-child td {
    border-bottom: none;
}

@media (max-width: 768px) {
    #testManagementTable {
        font-size: 0.8rem;
    }
    
    #testManagementTable th:nth-child(6),
    #testManagementTable td:nth-child(6),
    #testManagementTable th:nth-child(7),
    #testManagementTable td:nth-child(7) {
        display: none;
    }
    
    .btn-group-sm > .btn, .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .card-header h3 {
        font-size: 1.1rem;
    }
    
    .small-box .inner h3 {
        font-size: 1.5rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group .btn {
        margin-bottom: 2px;
        margin-right: 0;
    }
}
</style>

<?php require_once 'inc/footer.php'; ?>