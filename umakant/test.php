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
                            
                            <!-- Advanced Filters -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <select id="categoryFilter" class="form-control">
                                        <option value="">All Categories</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="genderFilter" class="form-control">
                                        <option value="">All Genders</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Both">Both</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" id="priceFilter" class="form-control" placeholder="Max Price">
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-outline-secondary btn-block" onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </button>
                                </div>
                            </div>

                            <!-- Tests DataTable -->
                            <div class="table-responsive">
                                <table id="testsTable" class="table table-bordered table-striped table-hover table-enhanced">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="40"><input type="checkbox" id="selectAllTests"></th>
                                            <th>ID</th>
                                            <th>Test Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Gender</th>
                                            <th>Range</th>
                                            <th>Unit</th>
                                            <th>Actions</th>
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
                                        <th>S.No.</th>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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
<div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="testModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="testName">
                                    <i class="fas fa-flask mr-1"></i>
                                    Test Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="testName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="testCategory">
                                    <i class="fas fa-tags mr-1"></i>
                                    Category <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="testCategory" name="category_id" required>
                                    <option value="">Select Category</option>
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
                            <!-- Male Range -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <label class="font-weight-bold text-primary">Male Range:</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="maleMin" name="male_min" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="maleMax" name="male_max" placeholder="Max" step="0.01">
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
                                    <input type="number" class="form-control" id="femaleMin" name="female_min" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="femaleMax" name="female_max" placeholder="Max" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="femaleUnit" name="female_unit" placeholder="Unit">
                                </div>
                            </div>

                            <!-- Child Range -->
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="font-weight-bold text-success">Child Range:</label>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="childMin" name="child_min" placeholder="Min" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" id="childMax" name="child_max" placeholder="Max" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="childUnit" name="child_unit" placeholder="Unit">
                                </div>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
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

<script>
// Global variables
let testsTable;
let categoriesTable;

// Initialize page
$(document).ready(function() {
    initializeDataTable();
    loadCategories();
    loadStats();
    initializeEventListeners();
});

function initializeDataTable() {
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
                if (json.success) {
                    return json.data || [];
                } else {
                    console.error('Failed to load tests:', json.message);
                    toastr.error('Failed to load tests: ' + (json.message || 'Unknown error'));
                    return [];
                }
            },
            error: function(xhr, error, thrown) {
                console.error('AJAX Error:', error, thrown);
                toastr.error('Failed to load tests data');
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                className: 'text-center',
                width: '40px',
                render: function(data, type, row) {
                    return `<input type="checkbox" class="test-checkbox" value="${row.id}">`;
                }
            },
            { 
                data: 'id',
                width: '60px'
            },
            { 
                data: 'name',
                render: function(data, type, row) {
                    return `<div class="font-weight-bold text-primary">${data || 'N/A'}</div>
                            ${row.description ? `<small class="text-muted">${row.description}</small>` : ''}`;
                }
            },
            { 
                data: 'category_name',
                render: function(data, type, row) {
                    return data ? `<span class="badge badge-info">${data}</span>` : '-';
                }
            },
            { 
                data: 'price',
                render: function(data, type, row) {
                    return data ? `₹${parseFloat(data).toFixed(2)}` : '-';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    let genders = [];
                    if (row.min_male !== null || row.max_male !== null) genders.push('<span class="badge badge-primary badge-sm">Male</span>');
                    if (row.min_female !== null || row.max_female !== null) genders.push('<span class="badge badge-danger badge-sm">Female</span>');
                    if (!genders.length && (row.min !== null || row.max !== null)) genders.push('<span class="badge badge-success badge-sm">Both</span>');
                    return genders.length > 0 ? genders.join(' ') : '-';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    let ranges = [];
                    if (row.min_male !== null || row.max_male !== null) {
                        ranges.push(`M: ${row.min_male || 0}-${row.max_male || '∞'}`);
                    }
                    if (row.min_female !== null || row.max_female !== null) {
                        ranges.push(`F: ${row.min_female || 0}-${row.max_female || '∞'}`);
                    }
                    if (!ranges.length && (row.min !== null || row.max !== null)) {
                        ranges.push(`${row.min || 0}-${row.max || '∞'}`);
                    }
                    return ranges.length > 0 ? ranges.join('<br>') : '-';
                }
            },
            { 
                data: 'unit',
                render: function(data, type, row) {
                    return data || '-';
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
                            <button class="btn btn-info btn-sm" onclick="viewTest(${data})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editTest(${data})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteTest(${data}, '${(row.name || '').replace(/'/g, '\\\'')}')" title="Delete">
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
    $('#selectAllTests').on('change', function() {
        $('.test-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkActions();
    });
    
    $(document).on('change', '.test-checkbox', function() {
        updateBulkActions();
    });
}

function applyFilters() {
    const category = $('#categoryFilter').val();
    const gender = $('#genderFilter').val();
    const maxPrice = $('#priceFilter').val();

    // Clear all filters first
    testsTable.search('');
    testsTable.columns().search('');
    
    // Apply category filter
    if (category) {
        testsTable.column(3).search(category, false, false);
    }
    
    // Apply gender filter
    if (gender) {
        testsTable.column(5).search(gender, false, false);
    }
    
    // Apply price filter
    if (maxPrice) {
        testsTable.column(4).search(function(settings, data, dataIndex) {
            const price = parseFloat(data[4].replace(/[₹,]/g, ''));
            return price <= parseFloat(maxPrice);
        });
    }
    
    testsTable.draw();
}

function clearFilters() {
    $('#categoryFilter').val('');
    $('#genderFilter').val('');
    $('#priceFilter').val('');
    testsTable.search('').columns().search('').draw();
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
    
    // Update select all checkbox
    const totalCheckboxes = $('.test-checkbox').length;
    if (selectedCount === 0) {
        $('#selectAllTests').prop('indeterminate', false).prop('checked', false);
    } else if (selectedCount === totalCheckboxes) {
        $('#selectAllTests').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#selectAllTests').prop('indeterminate', true);
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
    $('#genderFilter').val('');
    $('#priceFilter').val('');
    testsTable.search('').columns().search('').draw();
}

function loadCategories() {
    $.get('patho_api/test_category.php')
        .done(function(response) {
            if (response.status === 'success') {
                let options = '<option value="">Select Category</option>';
                let filterOptions = '<option value="">All Categories</option>';
                
                response.data.forEach(category => {
                    options += `<option value="${category.id}">${category.name}</option>`;
                    filterOptions += `<option value="${category.name}">${category.name}</option>`;
                });
                
                $('#testCategory').html(options);
                $('#categoryFilter').html(filterOptions);
                
                // Populate categories table
                populateCategoriesTable(response.data);
            }
        });
}

function populateCategoriesTable(categories) {
    let html = '';
    categories.forEach(category => {
        html += `
            <tr>
                <td>${category.id}</td>
                <td>${category.name}</td>
                <td><span class="badge badge-primary">${category.test_count || 0}</span></td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editCategory(${category.id}, '${category.name}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id}, '${category.name}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    $('#categoriesTableBody').html(html);
}

function loadStats() {
    $.get('patho_api/test.php?action=stats')
        .done(function(response) {
            if (response.status === 'success') {
                $('#totalTests').text(response.data.total || 0);
                $('#activeTests').text(response.data.active || 0);
                $('#totalCategories').text(response.data.categories || 0);
                $('#testEntries').text(response.data.entries || 0);
            }
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
    $.get(`patho_api/test.php?id=${id}`)
        .done(function(response) {
            if (response.status === 'success') {
                const test = response.data;
                $('#testId').val(test.id);
                $('#testName').val(test.name);
                $('#testCategory').val(test.category_id);
                $('#testPrice').val(test.price);
                $('#testUnit').val(test.unit);
                $('#testMethod').val(test.method);
                $('#maleMin').val(test.male_min);
                $('#maleMax').val(test.male_max);
                $('#maleUnit').val(test.male_unit);
                $('#femaleMin').val(test.female_min);
                $('#femaleMax').val(test.female_max);
                $('#femaleUnit').val(test.female_unit);
                $('#childMin').val(test.child_min);
                $('#childMax').val(test.child_max);
                $('#childUnit').val(test.child_unit);
                $('#testDescription').val(test.description);
                
                $('#modalTitle').text('Edit Test');
                $('#testModal').modal('show');
            } else {
                showAlert('Error loading test data: ' + response.message, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load test data', 'error');
        });
}

function saveTestData() {
    const formData = new FormData($('#testForm')[0]);
    const id = $('#testId').val();
    const method = id ? 'PUT' : 'POST';
    
    const submitBtn = $('#testForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: 'patho_api/test.php',
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                showAlert(id ? 'Test updated successfully!' : 'Test added successfully!', 'success');
                $('#testModal').modal('hide');
                testsTable.ajax.reload();
                loadStats();
            } else {
                showAlert('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showAlert('Failed to save test data', 'error');
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function deleteTest(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete test "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `patho_api/test.php?id=${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('Test deleted successfully!', 'success');
                        testsTable.ajax.reload();
                        loadStats();
                    } else {
                        showAlert('Error deleting test: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showAlert('Failed to delete test', 'error');
                }
            });
        }
    });
}

function addCategory() {
    const name = $('#newCategoryName').val().trim();
    if (!name) {
        showAlert('Please enter category name', 'error');
        return;
    }

    $.ajax({
        url: 'patho_api/test_category.php',
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
</style>

<?php require_once 'inc/footer.php'; ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="testName">Name *</label>
                                    <input type="text" class="form-control" id="testName" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="testDescription">Description</label>
                                    <textarea class="form-control" id="testDescription" name="description" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="testCategoryId">Category *</label>
                                    <select class="form-control" id="testCategoryId" name="category_id" required>
                                        <option value="">Select Category</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="testPrice">Price *</label>
                                    <input type="number" class="form-control" id="testPrice" name="price" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label for="testUnit">Unit</label>
                                    <input type="text" class="form-control" id="testUnit" name="unit">
                                </div>
                                <!-- specimen removed -->
                            </div>
                            <div class="col-md-6">
                                <!-- Default Result and Reference Range removed -->
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                            <label for="testMin">Min Value (General)</label>
                                            <input type="number" class="form-control" id="testMin" name="min" step="0.01">
                                        </div>
                                    <div class="form-group col-md-6">
                                        <label for="testMax">Max Value (General)</label>
                                        <input type="number" class="form-control" id="testMax" name="max" step="0.01">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="testMinMale">Min Value (Male)</label>
                                        <input type="number" class="form-control" id="testMinMale" name="min_male" step="0.01">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="testMaxMale">Max Value (Male)</label>
                                        <input type="number" class="form-control" id="testMaxMale" name="max_male" step="0.01">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="testMinFemale">Min Value (Female)</label>
                                        <input type="number" class="form-control" id="testMinFemale" name="min_female" step="0.01">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="testMaxFemale">Max Value (Female)</label>
                                        <input type="number" class="form-control" id="testMaxFemale" name="max_female" step="0.01">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="testSubHeading">Sub Heading</label>
                                    <select class="form-control" id="testSubHeading" name="sub_heading">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                
                                <!-- method removed -->
                                <div class="form-group">
                                    <label for="testPrintNewPage">Print on New Page</label>
                                    <select class="form-control" id="testPrintNewPage" name="print_new_page">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTestBtn">Save Test</button>
            </div>
        </div>
    </div>
</div>

<!-- View Test Modal -->
<div class="modal fade" id="viewTestModal" tabindex="-1" role="dialog" aria-labelledby="viewTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-enhanced" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewTestModalLabel">
                    <i class="fas fa-vial mr-2"></i>Test Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewTestBody">
                <div class="row">
                    <div class="col-12">
                        <div id="testViewDetails" class="details-container">
                            <!-- Details will be populated by JavaScript -->
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                                <p>Loading test details...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="editTestFromView()">
                    <i class="fas fa-edit"></i> Edit Test
                </button>
                <button type="button" class="btn btn-success" onclick="printTestDetails()">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
function addTestToTable(testData) {
    // Check if table is currently empty (has only one row with colspan)
    var isEmptyTable = $('#testsTable tbody tr').length === 1 && 
                      $('#testsTable tbody tr:first td').attr('colspan') === '7';
    
    // Add row to table
    var newRow = '<tr>' +
        '<td></td>' + // S.No. will be handled by DataTable
        '<td>' + testData.id + '</td>' +
        '<td>' + (testData.category_name || '') + '</td>' +
        '<td>' + (testData.name || '') + '</td>' +
        '<td>' + (testData.price || '') + '</td>' +
        '<td>' + (testData.added_by_username || '') + '</td>' +
        '<td><button class="btn btn-sm btn-info view-test" data-id="' + testData.id + '" onclick="viewTest(' + testData.id + ')">View</button> ' +
            '<button class="btn btn-sm btn-warning edit-test" data-id="' + testData.id + '">Edit</button> ' +
            '<button class="btn btn-sm btn-danger delete-test" data-id="' + testData.id + '">Delete</button></td>' +
        '</tr>';
    
    // If DataTable is present, use its API to add the row for better UX
    if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
        try {
            var table = $('#testsTable').DataTable();
            // Build row data array matching columns: S.No. (auto), ID, Category, Name, Price, Added By, Actions
            var actions = '<button class="btn btn-sm btn-info view-test" data-id="' + testData.id + '" onclick="viewTest(' + testData.id + ')">View</button> ' +
                          '<button class="btn btn-sm btn-warning edit-test" data-id="' + testData.id + '">Edit</button> ' +
                          '<button class="btn btn-sm btn-danger delete-test" data-id="' + testData.id + '">Delete</button>';
                // Add row without immediate draw to avoid inconsistent meta indexes
                var rowNode = table.row.add([
                    '', // placeholder for serial column (rendered by DataTable)
                    testData.id,
                    testData.category_name || '',
                    testData.name || '',
                    testData.price || '',
                    testData.added_by_username || '',
                    actions
                ]).node();

                // Ensure newest appears at top: order by ID desc and go to first page, then perform a full draw
                try{
                    table.order([[1, 'desc']]).page('first').draw();
                    // Recompute serial numbers for visible page to avoid meta inconsistencies
                    try{
                        var nodes = table.rows({ order: 'applied', page: 'current' }).nodes();
                        $(nodes).each(function(i, row){ $(row).find('td:first').text(i + 1); });
                    }catch(eNum){ /* ignore numbering issues */ }
                }catch(e){
                    // fallback to drawing table to recalc numbering
                    try{ table.draw(); }catch(e2){}
                }
            return;
        } catch (e) {
            console.warn('Failed to add row via DataTable API, falling back to DOM:', e);
        }
    }

    // Fallback when no DataTable exists: insert into DOM
    if (isEmptyTable) {
        // Replace the empty message row with the new row
        $('#testsTable tbody').html(newRow);
    } else {
        // Add new row to existing table
        $('#testsTable tbody').prepend(newRow);
    }
    // update serial numbers for regular table
    updateSerialNumbers();
}

function updateSerialNumbers() {
    $('#testsTable tbody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function loadCategoriesForTests(){
    $.get('ajax/test_category_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success){ var s=''; resp.data.forEach(function(c){ s += '<option value="'+c.id+'">'+(c.name||'')+'</option>'; }); $('#testCategoryId').append(s); }
        else toastr.error('Failed to load categories');
    },'json');
}

function loadTests(){
    $.get('ajax/test_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success && Array.isArray(resp.data)){
            var t=''; 
            if(resp.data.length === 0) {
                t = '<tr><td colspan="7" class="text-center py-4"><i class="fas fa-info-circle text-muted mr-2"></i>No tests found</td></tr>';
            } else {
                resp.data.forEach(function(x, idx){ t += '<tr>'+
                            '<td></td>'+ // S.No. - will be handled by DataTable
                            '<td>'+x.id+'</td>'+
                            '<td>'+ (x.category_name||'') +'</td>'+
                            '<td>'+ (x.name||'') +'</td>'+
                            '<td>'+ (x.price||'') +'</td>'+
                            '<td>'+ (x.added_by_username||'') +'</td>'+
                            '<td><button class="btn btn-sm btn-info view-test" data-id="'+x.id+'" onclick="viewTest('+x.id+')">View</button> '+
                                '<button class="btn btn-sm btn-warning edit-test" data-id="'+x.id+'">Edit</button> '+
                                '<button class="btn btn-sm btn-danger delete-test" data-id="'+x.id+'">Delete</button></td>'+
                        '</tr>'; });
            }
            
            // Always destroy and recreate DataTable to avoid conflicts
            if ($.fn.DataTable && $.fn.dataTable.isDataTable('#testsTable')) {
                $('#testsTable').DataTable().destroy();
            }
            
            // Set table content
            $('#testsTable tbody').html(t);
            
            // Initialize DataTable only if there are records
            if(resp.data.length > 0 && typeof initDataTable === 'function'){
                initDataTable('#testsTable', { order: [[1, 'desc']] });
            }
            // Don't initialize DataTable for empty tables - let it show the message naturally
        } else {
            // Clear table and show error message
            $('#testsTable tbody').html('<tr><td colspan="7" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mr-2"></i>Failed to load tests</td></tr>');
            toastr.error('Failed to load tests');
        }
    },'json').fail(function(xhr){ 
        $('#testsTable tbody').html('<tr><td colspan="7" class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading tests</td></tr>');
        var msg = xhr.responseText || 'Server error'; 
        try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} 
        toastr.error(msg); 
    });
}

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

function openAddTestModal(){ $('#testForm')[0].reset(); $('#testId').val(''); $('#testModal').modal('show'); }

$(function(){
    loadCategoriesForTests();
    loadTests();

    $('#saveTestBtn').click(function(){ 
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

        var isEdit = $('#testId').val() !== '';
        // ensure new gender fields are included
        var data = $('#testForm').serialize() + '&action=save&ajax=1'; 
        
        $.post('ajax/test_api.php', data, function(resp){ 
            if(resp.success){ 
                toastr.success(resp.message||'Saved'); 
                $('#testModal').modal('hide'); 
                
                if(resp.data && !isEdit) { 
                    // New record - add to table directly
                    addTestToTable(resp.data); 
                } else { 
                    // Update - reload table
                    loadTests(); 
                } 
                
                // Reset form after successful save
                $('#testForm')[0].reset(); 
                $('#testId').val(''); 
            } else { 
                toastr.error(resp.message||'Save failed'); 
            } 
        }, 'json').fail(function(xhr){ 
            var msg = xhr.responseText || 'Server error'; 
            try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} 
            toastr.error(msg); 
        }); 
    });

    // DataTables provides search and paging; removed custom filters

    // delegated edit handler
    $(document).on('click', '.edit-test', function(){
        try{
            console.debug('edit-test clicked', $(this).data('id'));
            var id=$(this).data('id');
            $.get('ajax/test_api.php',{action:'get',id:id,ajax:1}, function(resp){
                if(resp.success){
                    var d = resp.data || {};
                    // populate fields
                    $('#testId').val(d.id);
                    $('#testCategoryId').val(d.category_id);
                    $('#testName').val(d.name);
                    $('#testDescription').val(d.description);
                    $('#testPrice').val(d.price);
                    $('#testUnit').val(d.unit);
                    $('#testMin').val(d.min);
                    $('#testMax').val(d.max);
                    // gender-specific ranges
                    $('#testMinMale').val(d.min_male);
                    $('#testMaxMale').val(d.max_male);
                    $('#testMinFemale').val(d.min_female);
                    $('#testMaxFemale').val(d.max_female);
                    $('#testSubHeading').val(d.sub_heading);
                    $('#testPrintNewPage').val(d.print_new_page);
                    // ensure form inputs are enabled for editing and show save
                    $('#testForm').find('input,textarea,select').prop('disabled', false);
                    $('#saveTestBtn').show();
                    $('#testModal').modal('show');
                } else {
                    toastr.error('Test not found');
                }
            },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('edit-test handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // delegated delete handler
    $(document).on('click', '.delete-test', function(){
        try{
            var id = $(this).data('id');
            if(!confirm('Delete test?')) return;
            
            $.post('ajax/test_api.php', {
                action: 'delete',
                id: id,
                ajax: 1
            }, function(resp){
                if(resp.success){
                    toastr.success(resp.message || 'Test deleted successfully');
                    loadTests(); // Reload the table
                } else {
                    toastr.error(resp.message || 'Delete failed');
                }
            }, 'json').fail(function(xhr){
                var msg = xhr.responseText || 'Server error';
                try{ 
                    var j = JSON.parse(xhr.responseText || '{}'); 
                    if(j.message) msg = j.message;
                } catch(e){} 
                toastr.error('Delete failed: ' + msg);
            });
        } catch(err){ 
            console.error('delete-test handler error', err); 
            toastr.error('Error: ' + (err.message || err)); 
        }
    });

    // global view - show full details in dedicated view modal
    window.viewTest = function(id){
        try{
            $.get('ajax/test_api.php',{action:'get',id:id,ajax:1}, function(resp){
                if(resp.success){
                    var d = resp.data || {};
                    var html = '';
                    html += '<div class="container-fluid">';
                    html += '<div class="row">';
                    // Left column: main info
                    html += '<div class="col-md-7">';
                    html += '  <h4 class="mb-1">' + escapeHtml(d.name || '') + ' <small class="text-muted">#' + escapeHtml(d.id || '') + '</small></h4>';
                    if(d.description) html += '<p class="text-muted">' + escapeHtml(d.description) + '</p>';
                    html += '  <div class="row">';
                    html += '    <div class="col-sm-6"><strong>Category</strong><div>' + escapeHtml(d.category_name||'') + '</div></div>';
                    html += '    <div class="col-sm-6"><strong>Price</strong><div>' + escapeHtml(d.price||'') + '</div></div>';
                    html += '    <div class="col-sm-6 mt-2"><strong>Unit</strong><div>' + escapeHtml(d.unit||'') + '</div></div>';
                    html += '    <div class="col-sm-6 mt-2"><strong>Sub Heading</strong><div>' + (d.sub_heading? 'Yes':'No') + '</div></div>';
                    html += '  </div>'; // row
                    html += '</div>'; // col-md-7

                    // Right column: metadata and actions
                    html += '<div class="col-md-5">';
                    html += '  <div class="card border-0">';
                    html += '    <div class="card-body p-2">';
                    html += '      <p class="mb-1"><small class="text-muted">Added By</small><br><strong>' + escapeHtml(d.added_by_username||'') + '</strong></p>';
                    html += '      <p class="mb-1"><small class="text-muted">Print New Page</small><br><strong>' + (d.print_new_page? 'Yes':'No') + '</strong></p>';
                    html += '      <p class="mb-0"><small class="text-muted">Default Result</small><br>' + escapeHtml(d.default_result||'') + '</p>';
                    html += '    </div>'; 
                    html += '  </div>';
                    html += '</div>'; // col-md-5

                    html += '</div>'; // row

                    // Ranges table
                    html += '<hr/>';
                    html += '<h6 class="mb-2">Reference Ranges</h6>';
                    html += '<div class="table-responsive">';
                    html += '<table class="table table-sm table-bordered">';
                    html += '<thead class="thead-light"><tr><th>Scope</th><th>Min</th><th>Max</th></tr></thead>';
                    html += '<tbody>';
                    html += '<tr><td>General</td><td>' + escapeHtml(d.min||'') + '</td><td>' + escapeHtml(d.max||'') + '</td></tr>';
                    html += '<tr><td>Male</td><td>' + escapeHtml(d.min_male||'') + '</td><td>' + escapeHtml(d.max_male||'') + '</td></tr>';
                    html += '<tr><td>Female</td><td>' + escapeHtml(d.min_female||'') + '</td><td>' + escapeHtml(d.max_female||'') + '</td></tr>';
                    html += '</tbody></table></div>';

                    if(d.reference_range){ html += '<p class="mt-2"><strong>Reference Note:</strong> ' + escapeHtml(d.reference_range) + '</p>'; }

                    html += '</div>'; // container-fluid

                    $('#viewTestBody').html(html);
                    $('#viewTestModal').modal('show');
                } else {
                    toastr.error('Test not found');
                }
            }, 'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ toastr.error('Error: '+(err.message||err)); }
    }

    // restore modal state on close
    $('#testModal').on('hidden.bs.modal', function(){
        $('#testForm').find('input,textarea,select').prop('disabled', false);
        $('#saveTestBtn').show();
        $('#testModalLabel').text('Add Test');
    });
});
</script>