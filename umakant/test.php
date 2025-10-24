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
        </div><!-- /.container-fluid -->
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
                                <!-- Group Action Buttons -->
                                <div class="btn-group mr-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllTests()">
                                        <i class="fas fa-check-square"></i> Select All
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAllTests()">
                                        <i class="fas fa-square"></i> Deselect All
                                    </button>
                                </div>
                                <div class="btn-group mr-2">
                                    <button type="button" class="btn btn-success btn-sm" onclick="exportTests()">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="refreshTests()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#testModal" onclick="openAddTestModal()">
                                    <i class="fas fa-plus"></i> Add New Test
                                </button>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#categoryModal" onclick="openAddCategoryModal()">
                                    <i class="fas fa-tags"></i> Manage Categories
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- Bulk Actions Alert -->
                            <div class="alert alert-info bulk-actions" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <span class="selected-count">0</span> tests selected
                                    </span>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkExportTests()">
                                            <i class="fas fa-download"></i> Export Selected
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkDeleteTests()">
                                            <i class="fas fa-trash"></i> Delete Selected
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Enhanced Filters -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-filter mr-2"></i>
                                        Advanced Filters
                                        <button class="btn btn-sm btn-outline-secondary float-right" onclick="clearFilters()">
                                            <i class="fas fa-times"></i> Clear All
                                        </button>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Category</label>
                                            <select id="categoryFilter" class="form-control">
                                                <option value="">All Categories</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Gender</label>
                                            <select id="genderFilter" class="form-control">
                                                <option value="">All Genders</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Both">Both</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Max Price (₹)</label>
                                            <input type="number" id="priceFilter" class="form-control" placeholder="Max Price">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Quick Search</label>
                                            <input type="text" id="quickSearch" class="form-control" placeholder="Search tests...">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">&nbsp;</label>
                                            <button class="btn btn-outline-secondary btn-block" onclick="clearFilters()">
                                                <i class="fas fa-times"></i> Clear
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tests DataTable -->
                            <div class="table-responsive">
                                <table id="testsTable" class="table table-striped table-bordered table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="30"><input type="checkbox" id="selectAll"></th>
                                            <th width="50">ID</th>
                                            <th>Test Name</th>
                                            <th>Category</th>
                                            <th>Price (₹)</th>
                                            <th>Gender</th>
                                            <th>Range</th>
                                            <th>Unit</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated by DataTables AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="testModalLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 90%; margin: 20px auto;">
        <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="testModalLabel">
                    <i class="fas fa-vial mr-2"></i>
                    <span id="modalTitle">Add New Test</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
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

                    <!-- Gender-specific ranges -->
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
                        <textarea class="form-control" id="testReferenceRange" name="reference_range" rows="2" placeholder="e.g., Normal range: 70-100 mg/dL, Critical values: <50 or >200 mg/dL"></textarea>
                        <small class="form-text text-muted">Additional notes or description about the reference ranges</small>
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

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="categoryModalLabel">
                    <i class="fas fa-tags mr-2"></i>
                    Test Categories
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <input type="text" class="form-control" id="newCategoryName" placeholder="Enter category name">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success btn-block" onclick="addCategory()">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="categoriesTable" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Tests Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoriesTableBody">
                            <!-- Categories will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Test Modal -->
<div class="modal fade" id="viewTestModal" tabindex="-1" role="dialog" aria-labelledby="viewTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewTestModalLabel">
                    <i class="fas fa-eye mr-2"></i>
                    Test Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
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
const TEST_CATEGORY_API = 'patho_api/test_category.php?action=';
const CURRENT_USER_ID = <?php echo (int)($_SESSION['user_id'] ?? 0); ?>;

// Global variables
let testsTable;
let categoriesTable;

// Simple DataTable initialization function
function initDataTable(selector, options = {}) {
    if ($.fn.DataTable && !$.fn.dataTable.isDataTable(selector)) {
        $(selector).DataTable({
            responsive: true,
            pageLength: 25,
            ...options
        });
    }
}

// Initialize page
$(document).ready(function() {
    console.log('Initializing Test Management page...');
    
    try {
        initializeDataTable();
        loadCategories();
        loadStats();
        initializeEventListeners();
        
        // Additional initialization
        loadCategoriesForTests();
        
        // Event handlers
        setupEventHandlers();
        
        console.log('Test Management page initialized successfully');
    } catch (error) {
        console.error('Error initializing Test Management page:', error);
        toastr.error('Error initializing page: ' + error.message);
    }
});

function setupEventHandlers() {
    // Form submission
    $('#testForm').on('submit', function(e) {
        e.preventDefault();
        saveTestData();
    });
    
    // Save button click (backup handler)
    $('#saveTestBtn').off('click').on('click', function(e) {
        e.preventDefault();
        saveTestData();
    });
    
    // Delegated edit handler
    $(document).on('click', '.edit-test', function(){
        try{
            var id = $(this).data('id');
            editTest(id);
        } catch(err) { 
            console.error('edit-test handler error', err); 
            toastr.error('Error: '+(err.message||err)); 
        }
    });
    
    // Delegated delete handler
    $(document).on('click', '.delete-test', function(){
        try{
            var id = $(this).data('id');
            var name = $(this).closest('tr').find('td:nth-child(3)').text() || 'this test';
            deleteTest(id, name);
        } catch(err) { 
            console.error('delete-test handler error', err); 
            toastr.error('Error: '+(err.message||err)); 
        }
    });
    
    // Modal event handlers
    $('#testModal').on('hidden.bs.modal', function(){
        $('#testForm').find('input,textarea,select').prop('disabled', false);
        $('#saveTestBtn').show();
        $('#modalTitle').text('Add New Test');
        // Clear any previous aria-hidden
        $(this).removeAttr('aria-hidden');
    });
    
    $('#testModal').on('show.bs.modal', function(){
        // Remove aria-hidden when showing
        $(this).removeAttr('aria-hidden');
    });
    
    $('#testModal').on('shown.bs.modal', function(){
        var $modal = $(this);
        // Ensure aria-hidden is not set when modal is visible
        $modal.removeAttr('aria-hidden');
        
        // Focus first input with a small delay
        setTimeout(function(){
            try{
                var $input = $('#testName');
                if ($input.length && $input.is(':visible')) {
                    $input.trigger('focus');
                }
            }catch(e){
                console.warn('Could not focus input:', e);
            }
        }, 100);
    });
    
    // Checkbox handlers
    $('#selectAllTests').on('change', function() {
        $('.test-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });
    
    $(document).on('change', '.test-checkbox', function() {
        updateBulkActions();
    });

    // Main category selection handler
    $('#mainCategorySelect').on('change', function() {
        const mainCategoryId = $(this).val();
        loadTestCategoriesByMain(mainCategoryId);
    });

    // Quick search handler
    $('#quickSearch').on('keyup', function() {
        if (testsTable) {
            testsTable.search($(this).val()).draw();
        }
    });

    // Main category filter handler
    $('#mainCategoryFilter').on('change', function() {
        applyFilters();
    });
}

function initializeDataTable() {
    try {
        console.log('Initializing DataTable...');
        
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
            $('#testsTable').DataTable().destroy();
        }
        
        testsTable = $('#testsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: 'ajax/test_api.php?action=list',
                type: 'GET',
                dataSrc: function(json) {
                    console.log('DataTable AJAX response:', json);
                    if (json.success) {
                        return json.data || [];
                    } else {
                        console.error('Failed to load tests:', json.message);
                        toastr.error('Failed to load tests: ' + (json.message || 'Unknown error'));
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable AJAX Error:', error, thrown);
                    toastr.error('Failed to load tests data');
                }
            },
        columns: [
            {
                data: null,
                orderable: false,
                className: 'text-center',
                width: '30px',
                render: function(data, type, row) {
                    return `<input type="checkbox" class="test-checkbox" value="${row.id}">`;
                }
            },
            { 
                data: 'id',
                width: '50px',
                className: 'text-center'
            },
            { 
                data: 'name',
                render: function(data, type, row) {
                    return `<strong>${data || 'N/A'}</strong>`;
                }
            },
            { 
                data: 'category_name',
                render: function(data, type, row) {
                    return data ? `<span class="badge badge-info">${data}</span>` : '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'price',
                className: 'text-right',
                render: function(data, type, row) {
                    return data ? `₹${parseFloat(data).toFixed(0)}` : '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                className: 'text-center',
                render: function(data, type, row) {
                    let genders = [];
                    if (row.min_male !== null || row.max_male !== null) genders.push('Male');
                    if (row.min_female !== null || row.max_female !== null) genders.push('Female');
                    if (!genders.length && (row.min !== null || row.max !== null)) genders.push('Both');
                    return genders.length > 0 ? genders.join(', ') : '<span class="text-muted">-</span>';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    let ranges = [];
                    if (row.min !== null && row.max !== null) {
                        ranges.push(`${row.min}-${row.max}`);
                    }
                    if (row.min_male !== null && row.max_male !== null) {
                        ranges.push(`M: ${row.min_male}-${row.max_male}`);
                    }
                    if (row.min_female !== null && row.max_female !== null) {
                        ranges.push(`F: ${row.min_female}-${row.max_female}`);
                    }
                    return ranges.length > 0 ? ranges.join('<br>') : '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'unit',
                className: 'text-center',
                render: function(data, type, row) {
                    return data ? `<code>${data}</code>` : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'id',
                orderable: false,
                className: 'text-center',
                width: '120px',
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-info btn-sm" onclick="viewTest(${data})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning btn-sm" onclick="editTest(${data})" title="Edit Test">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteTest(${data}, '${(row.name || '').replace(/'/g, '\\\'')}')" title="Delete Test">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'csv',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'excel',
                className: 'btn btn-primary btn-sm'
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                className: 'btn btn-info btn-sm'
            }
        ],
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading tests...',
            emptyTable: 'No tests found',
            zeroRecords: 'No matching tests found',
            search: 'Search tests:',
            lengthMenu: 'Show _MENU_ tests per page',
            info: 'Showing _START_ to _END_ of _TOTAL_ tests',
            infoEmpty: 'No tests available',
            infoFiltered: '(filtered from _MAX_ total tests)'
        },
        columnDefs: [
            {
                targets: [0, -1],
                orderable: false
            }
        ]
    });

    // Custom filters
    $('#categoryFilter, #genderFilter, #priceFilter').on('change keyup', function() {
        applyFilters();
    });
    
    // Checkbox selection handlers
    $('#selectAll').on('change', function() {
        $('.test-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkActions();
    });
    
    $(document).on('change', '.test-checkbox', function() {
        updateBulkActions();
        // Update select all checkbox state
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

    // After DataTable ajax completes, refresh the stat cards so they reflect current data
    try{
        $('#testsTable').on('xhr.dt', function(e, settings, json, xhr){
            try{ if(typeof loadStats === 'function') loadStats(); }catch(e){}
        });
    }catch(e){}

    console.log('DataTable initialized successfully');
    
    } catch (error) {
        console.error('Error initializing DataTable:', error);
        toastr.error('Error initializing data table: ' + error.message);
        
        // Fallback: show a simple message in the table
        $('#testsTable tbody').html('<tr><td colspan="9" class="text-center text-warning py-4"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading table. Please refresh the page.</td></tr>');
    }
}

function applyFilters() {
    if (!testsTable) return;
    
    const mainCategory = $('#mainCategoryFilter').val();
    const category = $('#categoryFilter').val();
    const gender = $('#genderFilter').val();
    const maxPrice = $('#priceFilter').val();

    // Clear all filters first
    testsTable.search('');
    testsTable.columns().search('');
    
    // Apply category filter (column 3)
    if (category) {
        testsTable.column(3).search(category, false, false);
    }
    
    // Apply gender filter (column 5)
    if (gender) {
        testsTable.column(5).search(gender, false, false);
    }
    
    // Apply price filter (column 4)
    if (maxPrice) {
        testsTable.column(4).search(function(settings, data, dataIndex) {
            const priceText = data[4] || '';
            const price = parseFloat(priceText.replace(/[₹,]/g, ''));
            return !isNaN(price) && price <= parseFloat(maxPrice);
        });
    }
    
    testsTable.draw();
}

function clearFilters() {
    $('#categoryFilter').val('');
    $('#genderFilter').val('');
    $('#priceFilter').val('');
    $('#quickSearch').val('');
    if (testsTable) {
        testsTable.search('').columns().search('').draw();
    }
}

function updateBulkActions() {
    const checkedBoxes = $('.test-checkbox:checked');
    const bulkActionsDiv = $('.bulk-actions');
    const selectedCount = checkedBoxes.length;
    
    if (selectedCount > 0) {
        bulkActionsDiv.show();
        $('.selected-count').text(selectedCount);
    } else {
        bulkActionsDiv.hide();
    }
}

function bulkExportTests() {
    const selectedIds = $('.test-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        toastr.warning('Please select tests to export');
        return;
    }
    
    // Export functionality - for now just show selected IDs
    toastr.info(`Exporting ${selectedIds.length} selected tests...`);
    console.log('Selected test IDs:', selectedIds);
}

function bulkDeleteTests() {
    const selectedIds = $('.test-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        toastr.warning('Please select tests to delete');
        return;
    }
    
    if (confirm(`Are you sure you want to delete ${selectedIds.length} selected tests?`)) {
        // Bulk delete functionality
        toastr.info(`Deleting ${selectedIds.length} selected tests...`);
        console.log('Deleting test IDs:', selectedIds);
    }
}

// Small helper stubs to avoid ReferenceError from inline onclick handlers
function selectAllTests() {
    $('.test-checkbox').prop('checked', true);
    updateBulkActions();
}

function deselectAllTests() {
    $('.test-checkbox').prop('checked', false);
    updateBulkActions();
}

function exportTests() {
    // Prefer DataTables export buttons if available
    try {
        if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
            var table = $('#testsTable').DataTable();
            table.button('.buttons-csv').trigger();
            return;
        }
    } catch(e){}
    toastr.info('Export not available');
}

function refreshTests() {
    // Reload table data
    if (typeof initializeDataTable === 'function') {
        initializeDataTable();
    } else if (typeof loadTests === 'function') {
        loadTests();
    }
}


function loadCategories() {
    // Load main categories for modal
    $.getJSON('ajax/main_test_category_api.php', { action: 'list' }, function(response) {
        if (response && response.success) {
            let modalOptions = '<option value="">Select Main Category</option>';
            
            (response.data || []).forEach(category => {
                modalOptions += `<option value="${category.id}">${category.name}</option>`;
            });
            
            $('#mainCategorySelect').html(modalOptions);
        } else {
            console.warn('Failed to load main categories', response && response.message);
        }
    }).fail(function(xhr){ console.warn('Failed to load main categories', xhr.status); });

    // Load test categories for filter (all categories)
    const params = { action: 'list' };
    if (CURRENT_USER_ID) {
        params.user_id = CURRENT_USER_ID;
    }

    $.getJSON(TEST_CATEGORY_API, params, function(response) {
        if (response && response.success) {
            let filterOptions = '<option value="">All Categories</option>';

            (response.data || []).forEach(category => {
                filterOptions += `<option value="${category.name}">${category.name}</option>`;
            });

            $('#categoryFilter').html(filterOptions);

            // Populate categories table
            populateCategoriesTable(response.data || []);
        } else {
            console.warn('Failed to load categories', response && response.message);
        }
    }).fail(function(xhr){ console.warn('Failed to load categories', xhr.status); });
}

// Handle main category selection to load corresponding test categories
function loadTestCategoriesByMain(mainCategoryId) {
    if (!mainCategoryId) {
        $('#testCategoryId').html('<option value="">Select Test Category</option>');
        return;
    }

    $.getJSON(TEST_CATEGORY_API, { action: 'list' }, function(response) {
        if (response && response.success) {
            let options = '<option value="">Select Test Category</option>';
            
            (response.data || []).forEach(category => {
                if (category.main_category_id == mainCategoryId) {
                    options += `<option value="${category.id}">${category.name}</option>`;
                }
            });

            $('#testCategoryId').html(options);
        } else {
            console.warn('Failed to load test categories', response && response.message);
            $('#testCategoryId').html('<option value="">Error loading categories</option>');
        }
    }).fail(function(xhr){ 
        console.warn('Failed to load test categories', xhr.status);
        $('#testCategoryId').html('<option value="">Error loading categories</option>');
    });
}

function populateCategoriesTable(categories = []) {
    let html = '';

    categories.forEach(category => {
        const safeName = escapeHtml(category.name || '');
        html += `
            <tr>
                <td>${category.id}</td>
                <td>${safeName}</td>
                <td><span class="badge badge-primary">${category.test_count ?? 0}</span></td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editCategory(${category.id}, '${safeName}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id}, '${safeName}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    $('#categoriesTableBody').html(html);
}

function loadStats() {
    $.get('ajax/test_api.php?action=stats')
        .done(function(response) {
            if (response.success) {
                $('#totalTests').text(response.data.total || 0);
                $('#activeTests').text(response.data.active || 0);
                $('#totalCategories').text(response.data.categories || 0);
                $('#testEntries').text(response.data.entries || 0);
            } else {
                console.warn('Failed to load stats:', response.message);
                // Set default values if stats fail
                $('#totalTests').text('0');
                $('#activeTests').text('0');
                $('#totalCategories').text('0');
                $('#testEntries').text('0');
            }
        })
        .fail(function(xhr) {
            console.error('Stats AJAX error:', xhr.status, xhr.responseText);
            // Set default values if AJAX fails
            $('#totalTests').text('0');
            $('#activeTests').text('0');
            $('#totalCategories').text('0');
            $('#testEntries').text('0');
        });
}

function initializeEventListeners() {
    $('#testForm').on('submit', function(e) {
        e.preventDefault();
        saveTestData();
    });
}

function openAddTestModal() {
    $('#testForm')[0].reset();
    $('#testId').val('');
    $('#modalTitle').text('Add New Test');
    $('#testModal').modal('show');
}

function openAddCategoryModal() {
    $('#categoryModal').modal('show');
}

function editTest(id) {
    $.get('ajax/test_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                const test = response.data;
                $('#testId').val(test.id);
                $('#testName').val(test.name);
                $('#testCategoryId').val(test.category_id);
                $('#testPrice').val(test.price);
                $('#testUnit').val(test.unit);
                // Populate range-specific unit fields (server stores a single unit column)
                $('#generalUnit').val(test.unit);
                $('#maleUnit').val(test.unit);
                $('#femaleUnit').val(test.unit);
                $('#testMin').val(test.min);
                $('#testMax').val(test.max);
                $('#testMinMale').val(test.min_male);
                $('#testMaxMale').val(test.max_male);
                $('#testMinFemale').val(test.min_female);
                $('#testMaxFemale').val(test.max_female);
                $('#testMinChild').val(test.min_child);
                $('#testMaxChild').val(test.max_child);
                $('#childUnit').val(test.child_unit);
                $('#testSubHeading').val(test.sub_heading);
                $('#testPrintNewPage').val(test.print_new_page);
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

function saveTestData() {
    // Validate min/max ranges before submitting
    function validateTestRanges(){
        var pairs = [
            {min:'#testMin', max:'#testMax', label:'General'},
            {min:'#testMinMale', max:'#testMaxMale', label:'Male'},
            {min:'#testMinFemale', max:'#testMaxFemale', label:'Female'}
        ];
        for(var i=0;i<pairs.length;i++){
            var p = pairs[i];
            var vMin = $(p.min).val().trim();
            var vMax = $(p.max).val().trim();
            if(vMin === '' || vMax === '') continue; // nothing to validate
            var nMin = parseFloat(vMin);
            var nMax = parseFloat(vMax);
            if(isNaN(nMin) || isNaN(nMax)){
                toastr.error(p.label + ' range must be numeric');
                $(p.min).focus();
                return false;
            }
            if(nMax < nMin){
                toastr.error('Max Value ('+p.label+') cannot be less than Min Value ('+p.label+').');
                $(p.max).focus();
                return false;
            }
        }
        return true;
    }

    if(!validateTestRanges()) return; // abort save if invalid

    const formData = new FormData($('#testForm')[0]);
    const id = $('#testId').val();
    formData.append('action', 'save');
    formData.append('ajax', '1');
    
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
                
                // Always reload the DataTable to keep column alignment stable
                if (testsTable && typeof testsTable.ajax === 'function') {
                    testsTable.ajax.reload(null, false);
                } else if (typeof reloadDataTable === 'function') {
                    reloadDataTable('testsTable', false);
                } else if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
                    try { $('#testsTable').DataTable().ajax.reload(null, false); } catch(e){}
                } else if (typeof initializeDataTable === 'function') {
                    initializeDataTable();
                }
                
                loadStats();
                
                // Reset form after successful save
                $('#testForm')[0].reset(); 
                $('#testId').val(''); 
            } else {
                toastr.error('Error: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr) {
            var msg = xhr.responseText || 'Server error'; 
            try{ 
                var j=JSON.parse(xhr.responseText||'{}'); 
                if(j.message) msg=j.message;
            }catch(e){} 
            toastr.error('Failed to save test: ' + msg);
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function deleteTest(id, name) {
    if (confirm(`Are you sure you want to delete test "${name || 'this test'}"?`)) {
        $.ajax({
            url: 'ajax/test_api.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            success: function(response) {
                if (response.success) {
                    toastr.success('Test deleted successfully!');
                    if (typeof reloadDataTable === 'function') {
                        reloadDataTable('testsTable', false);
                    } else if (typeof reloadTestsDebounced === 'function') {
                        reloadTestsDebounced();
                    } else if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
                        try { $('#testsTable').DataTable().ajax.reload(null,false); } catch(e){}
                    } else if (typeof loadTests === 'function') { loadTests(); }
                    loadStats();
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

function addCategory() {
    const name = $('#newCategoryName').val().trim();
    if (!name) {
        showAlert('Please enter category name', 'error');
        return;
    }

    $.ajax({
    url: 'ajax/test_category_api.php',
        type: 'POST',
        data: { name: name },
        success: function(response) {
            if (response.status === 'success') {
                showAlert('Category added successfully!', 'success');
                $('#newCategoryName').val('');
                loadCategories();
            } else {
                showAlert('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showAlert('Failed to add category', 'error');
        }
    });
}

function showAlert(message, type) {
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} mr-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    $('.alert').remove();
    $('.content-wrapper .content').prepend(alert);
    
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
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

/* Enhanced table styling */
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

/* Filter card styling */
.card-body .row {
    margin-bottom: 0;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

/* Bulk actions styling */
.bulk-actions {
    border-radius: 0.375rem;
    border: 1px solid #b8daff;
    background-color: #d1ecf1;
}

/* DataTables custom styling */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
}

.dataTables_wrapper .dataTables_info {
    padding-top: 0.75rem;
}

.dataTables_wrapper .dataTables_paginate {
    padding-top: 0.75rem;
}

/* Button styling improvements */
.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
}

/* Modal improvements */
.modal-header {
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    border-top: 1px solid #dee2e6;
}

/* Responsive improvements */
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
    
    .table-responsive {
        border: 0;
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

<script>
// Removed addTestToTable manual DOM manipulations to avoid column mismatch.

function updateSerialNumbers() {
    $('#testsTable tbody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function loadCategoriesForTests(){
    $.get('ajax/test_category_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success){ 
            var s=''; 
            resp.data.forEach(function(c){ 
                s += '<option value="'+c.id+'">'+(c.name||'')+'</option>'; 
            }); 
            $('#testCategoryId').html(s); 
        } else {
            toastr.error('Failed to load categories');
        }
    },'json');
}

function loadTests(){
    // Prefer DataTable AJAX reload when DataTable is initialized
    if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
        try {
            $('#testsTable').DataTable().ajax.reload(null, false);
            return;
        } catch (e) {
            console.warn('DataTable reload failed, falling back to manual load', e);
        }
    }

    // Fallback: fetch and render rows
    $.get('ajax/test_api.php',{action:'list',ajax:1},function(resp){
        if(resp && resp.success && Array.isArray(resp.data)){
            var t=''; 
            if(resp.data.length === 0) {
                t = '<tr><td colspan="10" class="text-center py-4"><i class="fas fa-info-circle text-muted mr-2"></i>No tests found</td></tr>';
            } else {
                resp.data.forEach(function(x, idx){ t += '<tr>'+
                            '<td><input type="checkbox" class="test-checkbox" value="'+x.id+'"></td>'+
                            '<td>'+x.id+'</td>'+
                            '<td>'+ (x.name||'') +'</td>'+
                            '<td>'+ (x.main_category_name||'') +'</td>'+
                            '<td>'+ (x.category_name||'') +'</td>'+
                            '<td>'+ (x.price||'') +'</td>'+
                            '<td>'+ (x.gender||'') +'</td>'+
                            '<td>'+ (x.range||'') +'</td>'+
                            '<td>'+ (x.unit||'') +'</td>'+
                            '<td><button class="btn btn-sm btn-info view-test" data-id="'+x.id+'" onclick="viewTest('+x.id+')">View</button> '+
                                '<button class="btn btn-sm btn-warning edit-test" data-id="'+x.id+'">Edit</button> '+
                                '<button class="btn btn-sm btn-danger delete-test" data-id="'+x.id+'">Delete</button></td>'+
                        '</tr>'; });
            }

            $('#testsTable tbody').html(t);
        } else {
            $('#testsTable tbody').html('<tr><td colspan="10" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mr-2"></i>Failed to load tests</td></tr>');
            toastr.error('Failed to load tests');
        }
    },'json').fail(function(xhr){ 
        $('#testsTable tbody').html('<tr><td colspan="10" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading tests</td></tr>');
        var msg = xhr.responseText || 'Server error'; 
        try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} 
        toastr.error(msg); 
    });
}

// Debounced reload to avoid concurrent AJAX requests causing aborts
var _reloadTimer = null;
function reloadTestsDebounced(delay){
    delay = typeof delay === 'number' ? delay : 200;
    if (_reloadTimer) clearTimeout(_reloadTimer);
    _reloadTimer = setTimeout(function(){
        if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
            try { $('#testsTable').DataTable().ajax.reload(null, false); return; } catch(e){}
        }
        loadTests();
    }, delay);
}

// Unified simple reload alias used across CRUD handlers
window.reloadTests = function(){
    if (typeof reloadDataTable === 'function') {
        reloadDataTable('testsTable', false);
    } else if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
        try { $('#testsTable').DataTable().ajax.reload(null,false); } catch(e){}
    } else if (typeof reloadTestsDebounced === 'function') {
        reloadTestsDebounced();
    } else if (typeof loadTests === 'function') { loadTests(); }
};

function applyTestsFilters(){
    var q = $('#testsSearch').val().toLowerCase().trim();
    var per = parseInt($('#testsPerPage').val()||10,10);
    var shown = 0;
    $('#testsTable tbody tr').each(function(){
        var row = $(this);
        var text = row.text().toLowerCase();
        var matches = !q || text.indexOf(q) !== -1;
        if(matches && shown < per){ row.show(); shown++; } else { row.toggle(matches && shown < per); }
    });
}

function openAddTestModal(){ 
    $('#testForm')[0].reset(); 
    $('#testId').val(''); 
    $('#modalTitle').text('Add New Test');
    $('#testModal').modal('show'); 
}

function loadCategoriesForTests(){
    $.get('ajax/test_category_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success){ 
            var s='<option value="">Select Category</option>'; 
            resp.data.forEach(function(c){ 
                s += '<option value="'+c.id+'">'+(c.name||'')+'</option>'; 
            }); 
            $('#testCategory').html(s); 
        } else {
            toastr.error('Failed to load categories');
        }
    },'json');
}

// HTML escape function
function escapeHtml(text) {
    // Return empty string for null/undefined explicitly
    if (text === null || text === undefined) return '';
    // Coerce to string safely (handles numbers, booleans, objects with toString)
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

// Global view test function
window.viewTest = function(id){
    try{
        $.get('ajax/test_api.php',{action:'get',id:id,ajax:1}, function(resp){
            if(resp.success){
                var d = resp.data || {};
                var html = '';
                html += '<div class="container-fluid">';
                html += '<div class="row">';
                
                // Left column: main info
                html += '<div class="col-md-6">';
                html += '  <h4 class="mb-3 text-primary">' + escapeHtml(d.name || '') + ' <small class="text-muted">#' + escapeHtml(d.id || '') + '</small></h4>';
                if(d.description) html += '<p class="text-muted mb-3">' + escapeHtml(d.description) + '</p>';
                
                html += '  <div class="card border-0 bg-light">';
                html += '    <div class="card-body p-3">';
                html += '      <h6 class="card-title mb-3"><i class="fas fa-info-circle mr-2"></i>Test Information</h6>';
                html += '      <div class="row">';
                html += '        <div class="col-sm-6 mb-2"><strong>Category:</strong><br><span class="badge badge-info">' + escapeHtml(d.category_name||'N/A') + '</span></div>';
                html += '        <div class="col-sm-6 mb-2"><strong>Price:</strong><br><span class="text-success font-weight-bold">₹' + escapeHtml(d.price||'0') + '</span></div>';
                html += '        <div class="col-sm-6 mb-2"><strong>Unit:</strong><br><code>' + escapeHtml(d.unit||'N/A') + '</code></div>';
                html += '        <div class="col-sm-6 mb-2"><strong>Method:</strong><br>' + escapeHtml(d.method||'N/A') + '</div>';
                html += '      </div>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>'; // col-md-6

                // Right column: metadata and settings
                html += '<div class="col-md-6">';
                html += '  <div class="card border-0 bg-light">';
                html += '    <div class="card-body p-3">';
                html += '      <h6 class="card-title mb-3"><i class="fas fa-cogs mr-2"></i>Settings & Metadata</h6>';
                html += '      <div class="row">';
                html += '        <div class="col-sm-6 mb-2"><strong>Sub Heading:</strong><br><span class="badge ' + (d.sub_heading ? 'badge-success">Yes' : 'badge-secondary">No') + '</span></div>';
                html += '        <div class="col-sm-6 mb-2"><strong>Print New Page:</strong><br><span class="badge ' + (d.print_new_page ? 'badge-success">Yes' : 'badge-secondary">No') + '</span></div>';
                html += '        <div class="col-sm-6 mb-2"><strong>Added By:</strong><br>' + escapeHtml(d.added_by_username||'N/A') + '</div>';
                html += '        <div class="col-sm-6 mb-2"><strong>Test Code:</strong><br><code>' + escapeHtml(d.test_code||'N/A') + '</code></div>';
                html += '      </div>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>'; // col-md-6

                html += '</div>'; // row

                // Reference Ranges
                html += '<div class="row mt-3">';
                html += '<div class="col-12">';
                html += '  <h6 class="mb-3"><i class="fas fa-chart-line mr-2"></i>Reference Ranges</h6>';
                html += '  <div class="table-responsive">';
                html += '  <table class="table table-sm table-bordered">';
                html += '    <thead class="thead-light">';
                html += '      <tr><th>Scope</th><th>Min Value</th><th>Max Value</th><th>Unit</th></tr>';
                html += '    </thead>';
                html += '    <tbody>';
                html += '      <tr><td><strong>General</strong></td><td>' + escapeHtml(d.min||'-') + '</td><td>' + escapeHtml(d.max||'-') + '</td><td>' + escapeHtml(d.unit||'-') + '</td></tr>';
                html += '      <tr><td><strong class="text-primary">Male</strong></td><td>' + escapeHtml(d.min_male||'-') + '</td><td>' + escapeHtml(d.max_male||'-') + '</td><td>' + escapeHtml(d.male_unit||d.unit||'-') + '</td></tr>';
                html += '      <tr><td><strong class="text-danger">Female</strong></td><td>' + escapeHtml(d.min_female||'-') + '</td><td>' + escapeHtml(d.max_female||'-') + '</td><td>' + escapeHtml(d.female_unit||d.unit||'-') + '</td></tr>';
                html += '      <tr><td><strong class="text-warning">Child</strong></td><td>' + escapeHtml(d.min_child||'-') + '</td><td>' + escapeHtml(d.max_child||'-') + '</td><td>' + escapeHtml(d.child_unit||d.unit||'-') + '</td></tr>';
                html += '    </tbody>';
                html += '  </table>';
                html += '  </div>';

                if(d.reference_range){ 
                    html += '<div class="alert alert-info mt-3">';
                    html += '  <h6 class="alert-heading"><i class="fas fa-info-circle mr-2"></i>Reference Notes</h6>';
                    html += '  <p class="mb-0">' + escapeHtml(d.reference_range) + '</p>';
                    html += '</div>';
                }

                html += '</div>'; // col-12
                html += '</div>'; // row
                html += '</div>'; // container-fluid

                $('#viewTestBody').html(html);
                $('#viewTestModalLabel').text('Test Details: ' + escapeHtml(d.name || ''));
                
                // Store test ID for edit function
                $('#viewTestModal').data('test-id', id);
                $('#viewTestModal').modal('show');
            } else {
                toastr.error('Test not found');
            }
        }, 'json').fail(function(xhr){ 
            var msg = xhr.responseText || 'Server error'; 
            try{ 
                var j=JSON.parse(xhr.responseText||'{}'); 
                if(j.message) msg=j.message;
            }catch(e){} 
            toastr.error(msg); 
        });
    }catch(err){ 
        toastr.error('Error: '+(err.message||err)); 
    }
};

// Function to edit test from view modal
window.editTestFromView = function() {
    const testId = $('#viewTestModal').data('test-id');
    if (testId) {
        $('#viewTestModal').modal('hide');
        editTest(testId);
    }
};
</script>

<!-- Enhanced table scripts commented out to avoid conflicts -->
<!-- <script src="assets/js/enhanced-table.js?v=<?php echo time(); ?>"></script>
<script src="assets/js/test-enhanced.js?v=<?php echo time(); ?>"></script> -->