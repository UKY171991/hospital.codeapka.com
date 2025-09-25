<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Custom CSS for Entry Table -->
<link rel="stylesheet" href="assets/css/entry-table.css">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clipboard-list mr-2"></i>Test Entries</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Test Entries</li>
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
                            <h3 id="totalEntries">0</h3>
                            <p>Total Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pendingEntries">0</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="completedEntries">0</h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="todayEntries">0</h3>
                            <p>Today's Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list-alt mr-1"></i>
                                Test Entry Management
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success btn-sm" onclick="exportEntries()">
                                    <i class="fas fa-download"></i> Export
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
                            <!-- Search and Filter Row -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="entriesSearch" class="form-control" placeholder="Search entries by patient, doctor, test, etc...">
                                        <div class="input-group-append">
                                            <button id="entriesSearchClear" class="btn btn-outline-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto text-right">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="entriesPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Advanced Filters -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <select id="statusFilter" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="doctorFilter" class="form-control">
                                        <option value="">All Doctors</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="testFilter" class="form-control">
                                        <option value="">All Tests</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" id="dateFromFilter" class="form-control" title="From Date">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" id="dateToFilter" class="form-control" title="To Date">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary btn-block" onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>

                            <!-- Entries DataTable -->
                            <div class="table-responsive">
                                <table id="entriesTable" class="table table-bordered table-striped table-hover entries-table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Entry ID</th>
                                            <th>Patient Name</th>
                                            <th>Tests</th>
                                            <th>Status</th>
                                            <th>Test Date</th>
                                            <th>Added By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated by JavaScript -->
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

<!-- Entry Modal -->
<div class="modal fade" id="entryModal" tabindex="-1" role="dialog" aria-labelledby="entryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="entryModalLabel">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    <span id="modalTitle">Add New Test Entry</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="entryForm">
                <div class="modal-body">
                    <input type="hidden" id="entryId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryPatient">
                                    <i class="fas fa-user mr-1"></i>
                                    Patient <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="entryPatient" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryDoctor">
                                    <i class="fas fa-user-md mr-1"></i>
                                    Doctor <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="entryDoctor" name="doctor_id" required>
                                    <option value="">Select Doctor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryTest">
                                    <i class="fas fa-vial mr-1"></i>
                                    Primary Test
                                </label>
                                <select class="form-control select2" id="entryTest" name="test_id">
                                    <option value="">Select Primary Test (Optional)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryDate">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Entry Date
                                </label>
                                <input type="date" class="form-control" id="entryDate" name="entry_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Multiple Tests Section -->
                    <div class="form-group">
                        <label>
                            <i class="fas fa-list mr-1"></i>
                            Tests <span class="text-danger">*</span>
                        </label>
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="mb-0">Selected Tests</h6>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addTestToEntry()">
                                            <i class="fas fa-plus"></i> Add Test
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="selectedTestsList">
                                    <p class="text-muted">No tests selected. Click "Add Test" to add tests to this entry.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entryResult">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Result
                                </label>
                                <input type="text" class="form-control" id="entryResult" name="result">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entryUnit">
                                    <i class="fas fa-ruler mr-1"></i>
                                    Unit
                                </label>
                                <input type="text" class="form-control" id="entryUnit" name="unit">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entryStatus">
                                    <i class="fas fa-flag mr-1"></i>
                                    Status
                                </label>
                                <select class="form-control" id="entryStatus" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryAmount">
                                    <i class="fas fa-rupee-sign mr-1"></i>
                                    Amount
                                </label>
                                <input type="number" class="form-control" id="entryAmount" name="amount" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryDiscount">
                                    <i class="fas fa-percentage mr-1"></i>
                                    Discount (%)
                                </label>
                                <input type="number" class="form-control" id="entryDiscount" name="discount" min="0" max="100" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="entryNotes">
                            <i class="fas fa-sticky-note mr-1"></i>
                            Notes
                        </label>
                        <textarea class="form-control" id="entryNotes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveEntryBtn">
                        <i class="fas fa-save"></i> Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Test Modal -->
<div class="modal fade" id="addTestModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus mr-1"></i>
                    Add Test to Entry
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="testSelect">Select Test <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="testSelect" required>
                        <option value="">Select Test</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="testResultValue">Result Value</label>
                            <input type="text" class="form-control" id="testResultValue" placeholder="Enter result value">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="testUnit">Unit</label>
                            <input type="text" class="form-control" id="testUnit" placeholder="Enter unit">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="testPrice">Price</label>
                            <input type="number" step="0.01" class="form-control" id="testPrice" placeholder="Enter price">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="testDiscount">Discount</label>
                            <input type="number" step="0.01" class="form-control" id="testDiscount" placeholder="Enter discount">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="testRemarks">Remarks</label>
                    <textarea class="form-control" id="testRemarks" rows="2" placeholder="Enter remarks"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmAddTest()">Add Test</button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let entriesTable;
let allEntries = [];
let currentPage = 1;
let entriesPerPage = 25;

// Initialize page
$(document).ready(function() {
    loadDropdownsForEntry();
    loadEntries();
    initializeEventListeners();
    loadStats();
});

function initializeEventListeners() {
    // Form submission
    $('#entryForm').on('submit', function(e) {
        e.preventDefault();
        saveEntryData();
    });
    
    // Search functionality
    $('#entriesSearch').on('input', function() {
        applyEntriesFilters();
    });

    // Clear search button
    $('#entriesSearchClear').click(function(e) {
        $('#entriesSearch').val('');
        applyEntriesFilters();
    });

    // Per page change
    $('#entriesPerPage').change(function() {
        entriesPerPage = parseInt($(this).val());
        currentPage = 1;
        applyEntriesFilters();
    });

    // Modal reset on hide
    $('#entryModal').on('hidden.bs.modal', function() {
        resetModalForm();
    });

    // Filter changes
    $('#statusFilter, #doctorFilter, #testFilter, #dateFromFilter, #dateToFilter').on('change', function() {
        currentPage = 1;
        applyEntriesFilters();
    });
}

function loadDropdownsForEntry() {
    // Initialize Select2 for entry form
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#entryModal')
    });

    // Load patients
    $.get('ajax/patient_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                let options = '<option value="">Select Patient</option>';
                response.data.forEach(patient => {
                    options += `<option value="${patient.id}">${patient.name || 'Unknown'}</option>`;
                });
                $('#entryPatient').html(options).trigger('change');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load patients: ' + errorMsg, 'error');
        });

    // Load doctors
    $.get('ajax/doctor_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                let options = '<option value="">Select Doctor</option>';
                let filterOptions = '<option value="">All Doctors</option>';
                response.data.forEach(doctor => {
                    options += `<option value="${doctor.id}">${doctor.name || 'Unknown'}</option>`;
                    filterOptions += `<option value="${doctor.id}">${doctor.name}</option>`;
                });
                $('#entryDoctor').html(options).trigger('change');
                $('#doctorFilter').html(filterOptions);
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load doctors: ' + errorMsg, 'error');
        });

    // Load tests
    $.get('ajax/test_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                let options = '<option value="">Select Test</option>';
                let filterOptions = '<option value="">All Tests</option>';
                response.data.forEach(test => {
                    options += `<option value="${test.id}">${test.name || 'Unknown'}</option>`;
                    filterOptions += `<option value="${test.id}">${test.name}</option>`;
                });
                $('#entryTest').html(options).trigger('change');
                $('#testFilter').html(filterOptions);
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load tests: ' + errorMsg, 'error');
        });
}

function loadEntries() {
    $.get('ajax/entry_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                allEntries = response.data;
                applyEntriesFilters();
            } else {
                showAlert('Error loading entries: ' + (response.message || 'Unknown error'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load entry data: ' + errorMsg, 'error');
        });
}

function applyEntriesFilters() {
    const searchTerm = $('#entriesSearch').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();
    const doctorFilter = $('#doctorFilter').val();
    const testFilter = $('#testFilter').val();
    const dateFrom = $('#dateFromFilter').val();
    const dateTo = $('#dateToFilter').val();
    
    let filteredEntries = allEntries.filter(entry => {
        // Search term filter
        if (searchTerm && 
            !((entry.patient_name || '').toLowerCase().includes(searchTerm)) &&
            !((entry.doctor_name || '').toLowerCase().includes(searchTerm)) &&
            !((entry.test_name || '').toLowerCase().includes(searchTerm)) &&
            !(entry.id.toString().includes(searchTerm))) {
            return false;
        }
        
        // Status filter
        if (statusFilter && entry.status !== statusFilter) {
            return false;
        }
        
        // Doctor filter
        if (doctorFilter && entry.doctor_id != doctorFilter) {
            return false;
        }
        
        // Test filter
        if (testFilter && entry.test_id != testFilter) {
            return false;
        }
        
        // Date range filter
        if (dateFrom || dateTo) {
            const entryDate = new Date(entry.entry_date || entry.created_at);
            if (dateFrom && entryDate < new Date(dateFrom)) {
                return false;
            }
            if (dateTo && entryDate > new Date(dateTo + 'T23:59:59')) {
                return false;
            }
        }
        
        return true;
    });
    
    renderEntriesTable(filteredEntries);
}

function renderEntriesTable(entries) {
    const tbody = $('#entriesTable tbody');
    tbody.empty();
    
    if (entries.length === 0) {
        tbody.append('<tr><td colspan="8" class="text-center">No entries found</td></tr>');
        return;
    }
    
    // Calculate pagination
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = Math.min(startIndex + entriesPerPage, entries.length);
    const pageEntries = entries.slice(startIndex, endIndex);
    
    // Render entries
    pageEntries.forEach((entry, index) => {
        const serialNo = startIndex + index + 1;
        const statusClass = getStatusBadgeClass(entry.status);
        const statusText = formatStatus(entry.status);
        const testDate = formatDate(entry.entry_date || entry.created_at);
        const addedBy = entry.added_by_username || 'Unknown';
        
        // Handle multiple tests display
        let testDisplay = '';
        if (entry.grouped && entry.tests_count > 1) {
            // Multiple tests entry
            testDisplay = `
                <div class="multiple-tests-display">
                    <span class="badge badge-info">${entry.tests_count} Tests</span>
                    <small class="text-muted d-block">${entry.test_names || 'Multiple Tests'}</small>
                </div>
            `;
        } else {
            // Single test entry
            testDisplay = `<span class="single-test-display">${entry.test_name || 'N/A'}</span>`;
        }
        
        const row = `
            <tr>
                <td class="sr-no-cell">${serialNo}</td>
                <td><span class="entry-id-badge">${entry.id}</span></td>
                <td><span class="patient-name-container">${entry.patient_name || 'N/A'}</span></td>
                <td class="test-name-cell">${testDisplay}</td>
                <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td class="test-date-cell">${testDate}</td>
                <td class="added-by-cell">${addedBy}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-info btn-sm" onclick="viewEntry(${entry.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editEntry(${entry.id})" title="Edit Entry">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-entry" data-id="${entry.id}" title="Delete Entry">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Bind delete button events
    $('.delete-entry').on('click', function() {
        const id = $(this).data('id');
        deleteEntry(id);
    });
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'completed': return 'badge-success';
        case 'pending': return 'badge-warning';
        case 'cancelled': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

function formatStatus(status) {
    switch (status) {
        case 'completed': return 'Completed';
        case 'pending': return 'Pending';
        case 'cancelled': return 'Cancelled';
        default: return status.charAt(0).toUpperCase() + status.slice(1);
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

function viewEntry(id) {
    $.get('ajax/entry_api.php', { action: 'get', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const entry = response.data;
                populateEntryForm(entry);
                
                // Load multiple tests if this is a grouped entry
                if (entry.grouped && entry.tests_count > 1) {
                    loadEntryTests(id);
                } else {
                    clearSelectedTests();
                }
                
                $('#modalTitle').text('View Test Entry');
                $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', true);
                $('#saveEntryBtn').hide();
                $('#entryModal').modal('show');
            } else {
                showAlert('Error loading entry data: ' + (response.message || 'Entry not found'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load entry data: ' + errorMsg, 'error');
        });
}

function editEntry(id) {
    $.get('ajax/entry_api.php', { action: 'get', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const entry = response.data;
                populateEntryForm(entry);
                
                // Load multiple tests if this is a grouped entry
                if (entry.grouped && entry.tests_count > 1) {
                    loadEntryTests(id);
                } else {
                    clearSelectedTests();
                }
                
                $('#modalTitle').text('Edit Test Entry');
                $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', false);
                $('#saveEntryBtn').show();
                $('#entryModal').modal('show');
            } else {
                showAlert('Error loading entry data: ' + (response.message || 'Entry not found'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load entry data: ' + errorMsg, 'error');
        });
}

function loadEntryTests(entryId) {
    $.get('patho_api/entry.php', { action: 'get_tests', entry_id: entryId })
        .done(function(response) {
            if (response.success) {
                selectedTests = response.data.map(test => ({
                    test_id: test.test_id,
                    test_name: test.test_name,
                    result_value: test.result_value,
                    unit: test.unit,
                    price: test.price,
                    discount_amount: test.discount_amount,
                    remarks: test.remarks
                }));
                updateSelectedTestsDisplay();
            } else {
                console.log('No tests found for entry:', entryId);
                clearSelectedTests();
            }
        })
        .fail(function(xhr) {
            console.log('Failed to load tests for entry:', entryId);
            clearSelectedTests();
        });
}

function populateEntryForm(entry) {
    $('#entryId').val(entry.id);
    $('#entryPatient').val(entry.patient_id).trigger('change');
    $('#entryDoctor').val(entry.doctor_id).trigger('change');
    $('#entryTest').val(entry.test_id).trigger('change');
    $('#entryDate').val(entry.entry_date ? entry.entry_date.split(' ')[0] : '');
    $('#entryResult').val(entry.result_value || '');
    $('#entryUnit').val(entry.unit || '');
    $('#entryStatus').val(entry.status || 'pending');
    $('#entryAmount').val(entry.amount || '');
    $('#entryDiscount').val(entry.discount || '');
    $('#entryNotes').val(entry.remarks || '');
}

function saveEntryData() {
    // Validate form first
    if (!validateModalForm('entryForm')) {
        return false;
    }
    
    const data = $('#entryForm').serialize() + '&action=save&ajax=1';
    const isEdit = $('#entryId').val();
    
    const submitBtn = $('#entryForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.post('ajax/entry_api.php', data)
        .done(function(response) {
            if (response.success) {
                toastr.success(isEdit ? 'Entry updated successfully!' : 'Entry added successfully!');
                $('#entryModal').modal('hide');
                loadEntries();
                loadStats();
            } else {
                toastr.error('Error: ' + (response.message || 'Save failed'));
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            toastr.error('Failed to save entry: ' + errorMsg);
        })
        .always(function() {
            submitBtn.html(originalText).prop('disabled', false);
        });
}

function deleteEntry(id) {
    if (!confirm('Are you sure you want to delete this entry?')) {
        return;
    }

    $.post('ajax/entry_api.php', { action: 'delete', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                toastr.success('Entry deleted successfully!');
                loadEntries();
                loadStats();
            } else {
                toastr.error('Error deleting entry: ' + (response.message || 'Delete failed'));
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            toastr.error('Failed to delete entry: ' + errorMsg);
        });
}

function clearFilters() {
    $('#statusFilter').val('');
    $('#doctorFilter').val('');
    $('#testFilter').val('');
    $('#dateFromFilter').val('');
    $('#dateToFilter').val('');
    $('#entriesSearch').val('');
    currentPage = 1;
    applyEntriesFilters();
}

function loadStats() {
    $.get('ajax/entry_api.php', { action: 'stats', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const stats = response.data;
                $('#totalEntries').text(stats.total || 0);
                $('#pendingEntries').text(stats.pending || 0);
                $('#completedEntries').text(stats.completed || 0);
                $('#todayEntries').text(stats.today || 0);
            }
        })
        .fail(function() {
            console.error('Failed to load entry statistics');
        });
}

function exportEntries() {
    // Simple CSV export of current filtered data
    const searchTerm = $('#entriesSearch').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();
    const doctorFilter = $('#doctorFilter').val();
    const testFilter = $('#testFilter').val();
    const dateFrom = $('#dateFromFilter').val();
    const dateTo = $('#dateToFilter').val();
    
    let filteredEntries = allEntries.filter(entry => {
        // Apply same filters as in applyEntriesFilters
        if (searchTerm && 
            !((entry.patient_name || '').toLowerCase().includes(searchTerm)) &&
            !((entry.doctor_name || '').toLowerCase().includes(searchTerm)) &&
            !((entry.test_name || '').toLowerCase().includes(searchTerm)) &&
            !(entry.id.toString().includes(searchTerm))) {
            return false;
        }
        
        if (statusFilter && entry.status !== statusFilter) {
            return false;
        }
        
        if (doctorFilter && entry.doctor_id != doctorFilter) {
            return false;
        }
        
        if (testFilter && entry.test_id != testFilter) {
            return false;
        }
        
        if (dateFrom || dateTo) {
            const entryDate = new Date(entry.entry_date || entry.created_at);
            if (dateFrom && entryDate < new Date(dateFrom)) {
                return false;
            }
            if (dateTo && entryDate > new Date(dateTo + 'T23:59:59')) {
                return false;
            }
        }
        
        return true;
    });
    
    if (filteredEntries.length === 0) {
        toastr.warning('No entries to export');
        return;
    }
    
    // Create CSV content
    const headers = ['ID', 'Patient Name', 'Doctor Name', 'Test Name', 'Status', 'Entry Date', 'Added By'];
    let csvContent = headers.join(',') + '\n';
    
    filteredEntries.forEach(entry => {
        const row = [
            entry.id,
            `"${(entry.patient_name || 'N/A').replace(/"/g, '""')}"`,
            `"${(entry.doctor_name || 'N/A').replace(/"/g, '""')}"`,
            `"${(entry.test_name || 'N/A').replace(/"/g, '""')}"`,
            entry.status,
            entry.entry_date || entry.created_at || 'N/A',
            entry.added_by_username || 'N/A'
        ];
        csvContent += row.join(',') + '\n';
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', `test_entries_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function validateModalForm(formId) {
    let isValid = true;
    const form = $('#' + formId);
    
    // Reset validation states
    form.find('.is-invalid').removeClass('is-invalid');
    
    // Check required fields
    form.find('[required]').each(function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
            isValid = false;
        }
    });
    
    if (!isValid) {
        toastr.error('Please fill in all required fields');
    }
    
    return isValid;
}

function resetModalForm() {
    $('#entryForm')[0].reset();
    $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', false);
    $('#entryForm .is-invalid').removeClass('is-invalid');
    $('#modalTitle').text('Add New Test Entry');
    $('#saveEntryBtn').show();
    $('.select2').trigger('change');
}

function getErrorMessage(xhr) {
    try {
        const response = JSON.parse(xhr.responseText);
        return response.message || response.error || xhr.statusText;
    } catch (e) {
        return xhr.statusText || 'Unknown error';
    }
}

// Event handlers using delegation
$(document).on('click', '.edit-entry', function() {
    const id = $(this).data('id');
    editEntry(id);
});

// Multiple Tests Management
let selectedTests = [];

function addTestToEntry() {
    // Load tests for the add test modal
    $.get('ajax/test_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                let options = '<option value="">Select Test</option>';
                response.data.forEach(test => {
                    // Check if test is already selected
                    const isSelected = selectedTests.some(t => t.test_id == test.id);
                    if (!isSelected) {
                        options += `<option value="${test.id}" data-price="${test.price || 0}" data-unit="${test.unit || ''}">${test.name || 'Unknown'}</option>`;
                    }
                });
                $('#testSelect').html(options).trigger('change');
                $('#addTestModal').modal('show');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load tests: ' + errorMsg, 'error');
        });
}

function confirmAddTest() {
    const testId = $('#testSelect').val();
    const testName = $('#testSelect option:selected').text();
    const resultValue = $('#testResultValue').val();
    const unit = $('#testUnit').val();
    const price = $('#testPrice').val();
    const discount = $('#testDiscount').val();
    const remarks = $('#testRemarks').val();
    
    if (!testId) {
        showAlert('Please select a test', 'error');
        return;
    }
    
    // Add test to selected tests array
    const testData = {
        test_id: testId,
        test_name: testName,
        result_value: resultValue,
        unit: unit,
        price: price,
        discount_amount: discount,
        remarks: remarks
    };
    
    selectedTests.push(testData);
    updateSelectedTestsDisplay();
    
    // Clear form and close modal
    $('#testSelect').val('').trigger('change');
    $('#testResultValue').val('');
    $('#testUnit').val('');
    $('#testPrice').val('');
    $('#testDiscount').val('');
    $('#testRemarks').val('');
    $('#addTestModal').modal('hide');
    
    showAlert('Test added successfully', 'success');
}

function removeTestFromEntry(testId) {
    selectedTests = selectedTests.filter(test => test.test_id != testId);
    updateSelectedTestsDisplay();
    showAlert('Test removed successfully', 'success');
}

function updateSelectedTestsDisplay() {
    const container = $('#selectedTestsList');
    
    if (selectedTests.length === 0) {
        container.html('<p class="text-muted">No tests selected. Click "Add Test" to add tests to this entry.</p>');
        return;
    }
    
    let html = '';
    selectedTests.forEach((test, index) => {
        html += `
            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <strong>${test.test_name}</strong>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Result: ${test.result_value || 'N/A'}</small>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Unit: ${test.unit || 'N/A'}</small>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Price: ₹${test.price || '0'}</small>
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeTestFromEntry(${test.test_id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function clearSelectedTests() {
    selectedTests = [];
    updateSelectedTestsDisplay();
}

// Override the saveEntryData function to handle multiple tests
function saveEntryData() {
    if (!validateModalForm('entryForm')) {
        return;
    }
    
    // Check if at least one test is selected
    if (selectedTests.length === 0) {
        showAlert('Please add at least one test to this entry', 'error');
        return;
    }
    
    const formData = new FormData($('#entryForm')[0]);
    
    // Add selected tests to form data
    formData.append('tests', JSON.stringify(selectedTests));
    
    const entryId = $('#entryId').val();
    const isEdit = entryId && entryId !== '';
    
    $.ajax({
        url: 'ajax/entry_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json'
    })
    .done(function(response) {
        if (response.success) {
            showAlert(response.message || 'Entry saved successfully', 'success');
            $('#entryModal').modal('hide');
            loadEntries();
            loadStats();
        } else {
            showAlert('Error saving entry: ' + (response.message || 'Unknown error'), 'error');
        }
    })
    .fail(function(xhr) {
        const errorMsg = getErrorMessage(xhr);
        showAlert('Failed to save entry: ' + errorMsg, 'error');
    });
}

// Override the resetModalForm function to clear selected tests
function resetModalForm() {
    $('#entryForm')[0].reset();
    $('#entryId').val('');
    $('#entryPatient').val('').trigger('change');
    $('#entryDoctor').val('').trigger('change');
    $('#entryTest').val('').trigger('change');
    $('#entryDate').val(new Date().toISOString().split('T')[0]);
    $('#entryStatus').val('pending');
    $('#entryAmount').val('');
    $('#entryDiscount').val('');
    $('#entryNotes').val('');
    
    // Clear selected tests
    clearSelectedTests();
    
    // Reset modal title and buttons
    $('#modalTitle').text('Add New Test Entry');
    $('#saveEntryBtn').show().text('Save Entry');
    $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', false);
}
</script>

<?php require_once 'inc/footer.php'; ?>