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
                                <table id="testsTable" class="table table-striped table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="30"><input type="checkbox" id="selectAll"></th>
                                            <th width="50">ID</th>
                                            <th>Test Name</th>
                                            <th>Category</th>
                                            <th width="100">Price (₹)</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
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
                                <select class="form-control" id="mainCategorySelect" required>
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
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="testMin" name="min" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="testMax" name="max" placeholder="Max" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="generalUnit" name="general_unit" placeholder="Unit">
                                </div>
                            </div>

                            <!-- Male Range -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="font-weight-bold text-primary">Male Range:</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="testMinMale" name="min_male" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="testMaxMale" name="max_male" placeholder="Max" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="maleUnit" name="male_unit" placeholder="Unit">
                                </div>
                            </div>

                            <!-- Female Range -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="font-weight-bold text-danger">Female Range:</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="testMinFemale" name="min_female" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="testMaxFemale" name="max_female" placeholder="Max" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="femaleUnit" name="female_unit" placeholder="Unit">
                                </div>
                            </div>

                            <!-- Child Range -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="font-weight-bold text-warning">Child Range:</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="testMinChild" name="min_child" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="testMaxChild" name="max_child" placeholder="Max" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="childUnit" name="child_unit" placeholder="Unit">
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
    
    try {
        loadCategories();
        loadStats();
        initializeTable();
        setupEventHandlers();
        
        console.log('Test Management page initialized successfully');
    } catch (error) {
        console.error('Error initializing Test Management page:', error);
        toastr.error('Error initializing page: ' + error.message);
    }
});

// Initialize simple table without DataTables
function initializeTable() {
    loadTests();
}

// Load tests data
function loadTests() {
    console.log('Loading tests...');
    
    $.ajax({
        url: 'ajax/test_api.php?action=list',
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            console.log('Tests loaded successfully:', response);
            
            if (response && response.success && Array.isArray(response.data)) {
                testsData = response.data;
                renderTable(testsData);
                toastr.success('Tests loaded successfully');
            } else {
                console.error('Invalid response:', response);
                showTableError('Invalid data received from server');
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to load tests:', {xhr, status, error});
            showTableError('Failed to load test data. Please check your connection and try again.');
        }
    });
}

// Render table with data
function renderTable(data) {
    let html = '';
    
    if (!data || data.length === 0) {
        html = '<tr><td colspan="6" class="text-center py-4"><i class="fas fa-info-circle text-muted mr-2"></i>No tests found</td></tr>';
    } else {
        data.forEach(function(test) {
            let categoryHtml = '';
            if (test.main_category_name) {
                categoryHtml += `<span class="badge badge-secondary badge-sm">${test.main_category_name}</span><br>`;
            }
            if (test.category_name) {
                categoryHtml += `<span class="badge badge-info">${test.category_name}</span>`;
            } else {
                categoryHtml += '<span class="text-muted">No Category</span>';
            }
            
            html += `
                <tr>
                    <td class="text-center"><input type="checkbox" class="test-checkbox" value="${test.id}"></td>
                    <td class="text-center">${test.id}</td>
                    <td>
                        <strong class="text-primary">${test.name || 'N/A'}</strong>
                        ${test.description ? `<br><small class="text-muted">${test.description.substring(0, 50)}${test.description.length > 50 ? '...' : ''}</small>` : ''}
                    </td>
                    <td>${categoryHtml}</td>
                    <td class="text-right"><strong class="text-success">₹${test.price || '0'}</strong></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-info btn-sm" onclick="viewTest(${test.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning btn-sm" onclick="editTest(${test.id})" title="Edit Test">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteTest(${test.id}, '${(test.name || '').replace(/'/g, '\\\'')}')" title="Delete Test">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#testsTable tbody').html(html);
    setupCheckboxEvents();
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
    // Load main categories
    $.getJSON('ajax/main_test_category_api.php', { action: 'list' }, function(response) {
        if (response && response.success) {
            let modalOptions = '<option value="">Select Main Category</option>';
            
            (response.data || []).forEach(category => {
                modalOptions += `<option value="${category.id}">${category.name}</option>`;
            });
            
            $('#mainCategorySelect').html(modalOptions);
        }
    });

    // Load test categories for filter
    $.getJSON(TEST_CATEGORY_API + 'list', function(response) {
        if (response && response.success) {
            let filterOptions = '<option value="">All Categories</option>';

            (response.data || []).forEach(category => {
                filterOptions += `<option value="${category.name}">${category.name}</option>`;
            });

            $('#categoryFilter').html(filterOptions);
        }
    });
}

// Load test categories by main category
function loadTestCategoriesByMain(mainCategoryId) {
    if (!mainCategoryId) {
        $('#testCategoryId').html('<option value="">Select Test Category</option>');
        return;
    }

    $.getJSON(TEST_CATEGORY_API + 'list', function(response) {
        if (response && response.success) {
            let options = '<option value="">Select Test Category</option>';
            
            (response.data || []).forEach(category => {
                if (category.main_category_id == mainCategoryId) {
                    options += `<option value="${category.id}">${category.name}</option>`;
                }
            });

            $('#testCategoryId').html(options);
        }
    });
}

// Load stats
function loadStats() {
    $.get('ajax/test_api.php?action=stats')
        .done(function(response) {
            if (response.success) {
                $('#totalTests').text(response.data.total || 0);
                $('#activeTests').text(response.data.active || 0);
                $('#totalCategories').text(response.data.categories || 0);
                $('#testEntries').text(response.data.entries || 0);
            }
        })
        .fail(function() {
            $('#totalTests').text('0');
            $('#activeTests').text('0');
            $('#totalCategories').text('0');
            $('#testEntries').text('0');
        });
}

// Open add test modal
function openAddTestModal() {
    $('#testForm')[0].reset();
    $('#testId').val('');
    $('#modalTitle').text('Add New Test');
    $('#testModal').modal('show');
}

// Edit test
function editTest(id) {
    $.get('ajax/test_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                const test = response.data;
                
                // Populate form fields
                $('#testId').val(test.id);
                $('#testName').val(test.name);
                $('#testPrice').val(test.price);
                $('#testUnit').val(test.unit);
                $('#testMethod').val(test.method);
                
                // Set main category and load test categories
                if (test.main_category_id) {
                    $('#mainCategorySelect').val(test.main_category_id);
                    loadTestCategoriesByMain(test.main_category_id);
                    
                    // Set test category after a delay
                    setTimeout(function() {
                        $('#testCategoryId').val(test.category_id);
                    }, 500);
                }
                
                // Set range values
                $('#testMin').val(test.min);
                $('#testMax').val(test.max);
                $('#testMinMale').val(test.min_male);
                $('#testMaxMale').val(test.max_male);
                $('#testMinFemale').val(test.min_female);
                $('#testMaxFemale').val(test.max_female);
                $('#testMinChild').val(test.min_child);
                $('#testMaxChild').val(test.max_child);
                
                // Set units
                $('#generalUnit').val(test.unit);
                $('#maleUnit').val(test.male_unit || test.unit);
                $('#femaleUnit').val(test.female_unit || test.unit);
                $('#childUnit').val(test.child_unit || test.unit);
                
                // Set other fields
                $('#testSubHeading').val(test.sub_heading || 0);
                $('#testPrintNewPage').val(test.print_new_page || 0);
                $('#testDescription').val(test.description);
                $('#testReferenceRange').val(test.reference_range);
                
                $('#modalTitle').text('Edit Test');
                $('#testModal').modal('show');
            } else {
                toastr.error('Error loading test data: ' + (response.message || 'Unknown error'));
            }
        })
        .fail(function() {
            toastr.error('Failed to load test data');
        });
}

// Save test data
function saveTestData() {
    const formData = new FormData($('#testForm')[0]);
    const id = $('#testId').val();
    formData.append('action', 'save');
    
    const submitBtn = $('#saveTestBtn');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: 'ajax/test_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                toastr.success(id ? 'Test updated successfully!' : 'Test added successfully!');
                $('#testModal').modal('hide');
                loadTests(); // Reload table
                loadStats(); // Reload stats
            } else {
                toastr.error('Error: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            toastr.error('Failed to save test');
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
    $('#testsTable tbody').html(`
        <tr>
            <td colspan="6" class="text-center text-danger py-4">
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

#testsTable {
    font-size: 0.9rem;
}

#testsTable thead th {
    background-color: #343a40;
    color: white;
    border-color: #454d55;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 12px 8px;
}

#testsTable tbody td {
    vertical-align: middle;
    padding: 10px 8px;
    border-color: #dee2e6;
}

#testsTable tbody tr:hover {
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
    #testsTable {
        font-size: 0.8rem;
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