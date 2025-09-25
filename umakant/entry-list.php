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
                        <div class="col-md-12">
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
                                    Total Amount (Auto-calculated)
                                </label>
                                <input type="number" class="form-control" id="entryAmount" name="amount" step="0.01" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryDiscount">
                                    <i class="fas fa-percentage mr-1"></i>
                                    Discount % (Auto-calculated)
                                </label>
                                <input type="number" class="form-control" id="entryDiscount" name="discount" min="0" max="100" step="0.01" readonly>
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
                    <label for="categorySelect">Select Test Category <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="categorySelect" required>
                        <option value="">Select Category First</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Select Tests <span class="text-danger">*</span></label>
                    <div id="testsContainer" class="border rounded p-3" style="max-height: 300px; overflow-y: auto; display: none;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllTests">
                            <label class="form-check-label font-weight-bold" for="selectAllTests">
                                Select All Tests
                            </label>
                        </div>
                        <hr>
                        <div id="testsList">
                            <!-- Tests will be loaded here -->
                        </div>
                    </div>
                    <div id="noTestsMessage" class="text-muted text-center py-3" style="display: none;">
                        <i class="fas fa-info-circle"></i> Select a category to see available tests
                    </div>
                </div>
                
                <!-- Individual Test Details Section -->
                <div id="testDetailsSection" class="form-group" style="display: none;">
                    <label>Test Details <span class="text-danger">*</span></label>
                    <div id="testDetailsContainer" class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                        <!-- Individual test details will be loaded here -->
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="testRemarks">Default Remarks</label>
                    <textarea class="form-control" id="testRemarks" rows="2" placeholder="Enter default remarks for all selected tests"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmAddTest()">
                    <i class="fas fa-plus"></i> Add Selected Tests
                </button>
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
                    category_id: test.category_id,
                    category_name: test.category_name || 'Unknown Category',
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
    $('#entryStatus').val(entry.status || 'pending');
    $('#entryNotes').val(entry.remarks || '');
    
    // Calculate and set auto-calculated fields
    calculateEntryTotals();
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
    // Clear previous selections
    $('#categorySelect').val('').trigger('change');
    $('#testRemarks').val('');
    $('#testsContainer').hide();
    $('#noTestsMessage').show();
    $('#testDetailsSection').hide();
    $('#testDetailsContainer').html('');
    
    // Load categories for the add test modal
    $.get('ajax/test_category_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                let options = '<option value="">Select Category First</option>';
                response.data.forEach(category => {
                    options += `<option value="${category.id}">${category.name || 'Unknown'}</option>`;
                });
                $('#categorySelect').html(options).trigger('change');
                
                $('#addTestModal').modal('show');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load categories: ' + errorMsg, 'error');
        });
}

// Handle category selection change
$(document).on('change', '#categorySelect', function() {
    const categoryId = $(this).val();
    
    if (categoryId) {
        // Load tests for selected category
        $.get('ajax/test_api.php', { action: 'list', category_id: categoryId, ajax: 1 })
            .done(function(response) {
                if (response.success && response.data.length > 0) {
                    let html = '';
                    response.data.forEach(test => {
                        // Check if test is already selected
                        const isSelected = selectedTests.some(t => t.test_id == test.id);
                        const disabledAttr = isSelected ? 'disabled' : '';
                        const checkedAttr = isSelected ? 'checked' : '';
                        
                        html += `
                            <div class="form-check mb-2">
                                <input class="form-check-input test-checkbox" type="checkbox" 
                                       id="test_${test.id}" 
                                       value="${test.id}" 
                                       data-name="${test.name}"
                                       data-price="${test.price || 0}"
                                       data-unit="${test.unit || ''}"
                                       ${disabledAttr}
                                       ${checkedAttr}>
                                <label class="form-check-label" for="test_${test.id}">
                                    <strong>${test.name || 'Unknown'}</strong>
                                    <small class="text-muted d-block">
                                        Default Price: ₹${test.price || '0'} | Unit: ${test.unit || 'N/A'}
                                        ${isSelected ? ' <span class="text-warning">(Already Added)</span>' : ''}
                                    </small>
                                </label>
                            </div>
                        `;
                    });
                    
                    $('#testsList').html(html);
                    $('#testsContainer').show();
                    $('#noTestsMessage').hide();
                    
                    // Update select all checkbox
                    updateSelectAllCheckbox();
                } else {
                    $('#testsContainer').hide();
                    $('#noTestsMessage').show().html('<i class="fas fa-info-circle"></i> No tests available in this category');
                }
            })
            .fail(function(xhr) {
                const errorMsg = getErrorMessage(xhr);
                showAlert('Failed to load tests: ' + errorMsg, 'error');
                $('#testsContainer').hide();
                $('#noTestsMessage').show().html('<i class="fas fa-exclamation-triangle"></i> Error loading tests');
            });
    } else {
        // Reset tests container
        $('#testsContainer').hide();
        $('#noTestsMessage').show().html('<i class="fas fa-info-circle"></i> Select a category to see available tests');
    }
});

// Handle select all checkbox
$(document).on('change', '#selectAllTests', function() {
    const isChecked = $(this).is(':checked');
    $('.test-checkbox:not(:disabled)').prop('checked', isChecked);
    updateTestDetailsSection();
});

// Handle individual test checkbox change
$(document).on('change', '.test-checkbox', function() {
    updateSelectAllCheckbox();
    updateTestDetailsSection();
});

function updateTestDetailsSection() {
    const checkedTests = $('.test-checkbox:checked');
    console.log('updateTestDetailsSection called, checked tests:', checkedTests.length);
    
    if (checkedTests.length === 0) {
        $('#testDetailsSection').hide();
        return;
    }
    
    let html = '';
    checkedTests.each(function() {
        const testId = $(this).val();
        const testName = $(this).data('name');
        const defaultPrice = $(this).data('price');
        const defaultUnit = $(this).data('unit');
        
        console.log('Processing test:', testName, 'ID:', testId, 'Price:', defaultPrice, 'Unit:', defaultUnit);
        
        html += `
            <div class="test-detail-card mb-3 p-3 border rounded">
                <h6 class="mb-3 text-primary">
                    <i class="fas fa-vial"></i> ${testName}
                </h6>
                <div class="row">
                    <div class="col-md-4">
                        <label for="result_${testId}" class="form-label">Result Value</label>
                        <input type="text" class="form-control form-control-sm" 
                               id="result_${testId}" 
                               placeholder="Enter result value">
                    </div>
                    <div class="col-md-4">
                        <label for="unit_${testId}" class="form-label">Unit</label>
                        <input type="text" class="form-control form-control-sm" 
                               id="unit_${testId}" 
                               value="${defaultUnit}"
                               placeholder="Enter unit">
                    </div>
                    <div class="col-md-4">
                        <label for="price_${testId}" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" 
                               id="price_${testId}" 
                               value="${defaultPrice}"
                               placeholder="Enter price">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <label for="remarks_${testId}" class="form-label">Remarks</label>
                        <textarea class="form-control form-control-sm" 
                                  id="remarks_${testId}" 
                                  rows="2" 
                                  placeholder="Enter remarks for this test"></textarea>
                    </div>
                </div>
            </div>
        `;
    });
    
    $('#testDetailsContainer').html(html);
    $('#testDetailsSection').show();
    console.log('Test details section updated and shown');
}

function updateSelectAllCheckbox() {
    const totalCheckboxes = $('.test-checkbox:not(:disabled)').length;
    const checkedCheckboxes = $('.test-checkbox:not(:disabled):checked').length;
    
    if (checkedCheckboxes === 0) {
        $('#selectAllTests').prop('indeterminate', false).prop('checked', false);
    } else if (checkedCheckboxes === totalCheckboxes) {
        $('#selectAllTests').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#selectAllTests').prop('indeterminate', true);
    }
}

function confirmAddTest() {
    console.log('confirmAddTest called');
    const categoryId = $('#categorySelect').val();
    const categoryName = $('#categorySelect option:selected').text();
    const defaultRemarks = $('#testRemarks').val();
    
    console.log('Category ID:', categoryId, 'Category Name:', categoryName);
    
    if (!categoryId) {
        showAlert('Please select a category', 'error');
        return;
    }
    
    // Get all checked tests
    const checkedTests = $('.test-checkbox:checked');
    console.log('Checked tests count:', checkedTests.length);
    
    if (checkedTests.length === 0) {
        showAlert('Please select at least one test', 'error');
        return;
    }
    
    let addedCount = 0;
    let hasValidationErrors = false;
    
    // Validate and collect individual test details
    checkedTests.each(function() {
        const testId = $(this).val();
        const testName = $(this).data('name');
        
        console.log('Processing test for addition:', testName, 'ID:', testId);
        
        // Get individual test details
        const resultValue = $(`#result_${testId}`).val();
        const unit = $(`#unit_${testId}`).val();
        const price = $(`#price_${testId}`).val();
        const remarks = $(`#remarks_${testId}`).val() || defaultRemarks;
        
        console.log('Test details:', { resultValue, unit, price, remarks });
        
        // Validate required fields
        if (!resultValue.trim()) {
            showAlert(`Please enter result value for ${testName}`, 'error');
            hasValidationErrors = true;
            return false;
        }
        
        if (!unit.trim()) {
            showAlert(`Please enter unit for ${testName}`, 'error');
            hasValidationErrors = true;
            return false;
        }
        
        if (!price || parseFloat(price) <= 0) {
            showAlert(`Please enter valid price for ${testName}`, 'error');
            hasValidationErrors = true;
            return false;
        }
        
        // Check if test is already added
        const isAlreadyAdded = selectedTests.some(t => t.test_id == testId);
        if (!isAlreadyAdded) {
            const testData = {
                category_id: categoryId,
                category_name: categoryName,
                test_id: testId,
                test_name: testName,
                result_value: resultValue,
                unit: unit,
                price: parseFloat(price),
                discount_amount: 0, // No discount at test level
                remarks: remarks
            };
            
            console.log('Adding test data:', testData);
            selectedTests.push(testData);
            addedCount++;
        } else {
            console.log('Test already added:', testName);
        }
    });
    
    console.log('Added count:', addedCount, 'Has validation errors:', hasValidationErrors);
    
    if (hasValidationErrors) {
        return;
    }
    
    if (addedCount > 0) {
        updateSelectedTestsDisplay();
        showAlert(`${addedCount} test(s) added successfully`, 'success');
        
        // Clear form and close modal
        $('#categorySelect').val('').trigger('change');
        $('#testRemarks').val('');
        $('#addTestModal').modal('hide');
    } else {
        showAlert('All selected tests are already added to this entry', 'warning');
    }
}

function removeTestFromEntry(testId) {
    selectedTests = selectedTests.filter(test => test.test_id != testId);
    updateSelectedTestsDisplay();
    showAlert('Test removed successfully', 'success');
}

function calculateEntryTotals() {
    let totalAmount = 0;
    let totalDiscount = 0;
    
    // Calculate from selected tests
    selectedTests.forEach(test => {
        totalAmount += parseFloat(test.price) || 0;
        totalDiscount += parseFloat(test.discount_amount) || 0;
    });
    
    // Set the calculated values
    $('#entryAmount').val(totalAmount.toFixed(2));
    
    // Calculate discount percentage
    const discountPercentage = totalAmount > 0 ? (totalDiscount / totalAmount) * 100 : 0;
    $('#entryDiscount').val(discountPercentage.toFixed(2));
    
    console.log('Calculated totals:', { totalAmount, totalDiscount, discountPercentage });
}

function updateSelectedTestsDisplay() {
    const container = $('#selectedTestsList');
    
    if (selectedTests.length === 0) {
        container.html('<p class="text-muted">No tests selected. Click "Add Test" to add tests to this entry.</p>');
        calculateEntryTotals(); // Update totals even when no tests
        return;
    }
    
    let html = '';
    selectedTests.forEach((test, index) => {
        html += `
            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <strong>${test.test_name}</strong>
                            <br><small class="text-info">${test.category_name}</small>
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
                        <div class="col-md-2">
                            <small class="text-muted">Remarks: ${test.remarks ? (test.remarks.length > 20 ? test.remarks.substring(0, 20) + '...' : test.remarks) : 'N/A'}</small>
                        </div>
                        <div class="col-md-1 text-right">
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
    
    // Update calculated totals
    calculateEntryTotals();
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