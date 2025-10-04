<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$currentUserId = $_SESSION['user_id'] ?? '';
$currentUserDisplayName = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Unknown User';
$currentUserRole = $_SESSION['role'] ?? 'user';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clipboard-list mr-2"></i>Test Entries Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active">Test Entries</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalEntries">-</h3>
                            <p>Total Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <a href="#" class="small-box-footer" onclick="filterByStatus('all')">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pendingEntries">-</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="#" class="small-box-footer" onclick="filterByStatus('pending')">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="completedEntries">-</h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" class="small-box-footer" onclick="filterByStatus('completed')">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="todayEntries">-</h3>
                            <p>Today's Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <a href="#" class="small-box-footer" onclick="filterByDate('today')">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list-alt mr-1"></i>
                                Test Entry Management
                            </h3>
                            <div class="card-tools">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="openAddEntryModal()">
                                        <i class="fas fa-plus"></i> Add Entry
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" onclick="exportEntries()">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="refreshTable()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Filters Row -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="statusFilter">Status Filter:</label>
                                        <select class="form-control form-control-sm" id="statusFilter" onchange="applyFilters()">
                                            <option value="">All Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="dateFilter">Date Range:</label>
                                        <select class="form-control form-control-sm" id="dateFilter" onchange="applyFilters()">
                                            <option value="">All Dates</option>
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="this_week">This Week</option>
                                            <option value="this_month">This Month</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="patientFilter">Patient:</label>
                                        <input type="text" class="form-control form-control-sm" id="patientFilter" 
                                               placeholder="Search by patient name..." onkeyup="applyFilters()">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="doctorFilter">Doctor:</label>
                                        <input type="text" class="form-control form-control-sm" id="doctorFilter" 
                                               placeholder="Search by doctor name..." onkeyup="applyFilters()">
                                    </div>
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="table-responsive">
                                <table id="entriesTable" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th width="15%">Patient</th>
                                            <th width="15%">Doctor</th>
                                            <th width="20%">Tests</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Amount</th>
                                            <th width="10%">Date</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="dataTables_info" id="entriesTable_info" role="status" aria-live="polite">
                                        Showing entries
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="entriesTable_paginate">
                                        <!-- Pagination will be added by DataTables -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add/Edit Entry Modal -->
<div class="modal fade" id="entryModal" tabindex="-1" role="dialog" aria-labelledby="entryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="entryModalLabel">
                    <i class="fas fa-plus mr-1"></i>Add New Entry
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="entryForm">
                <div class="modal-body">
                    <input type="hidden" id="entryId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientSelect">Patient <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="patientSelect" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorSelect">Doctor</label>
                                <select class="form-control select2" id="doctorSelect" name="doctor_id">
                                    <option value="">Select Doctor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryDate">Entry Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="entryDate" name="entry_date" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryStatus">Status</label>
                                <select class="form-control" id="entryStatus" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tests Section -->
                    <div class="form-group">
                        <label>Tests <span class="text-danger">*</span></label>
                        <div id="testsContainer">
                            <div class="test-row row mb-2">
                                <div class="col-md-5">
                                    <select class="form-control test-select" name="tests[0][test_id]" required>
                                        <option value="">Select Test</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" name="tests[0][price]" 
                                           placeholder="Price" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" name="tests[0][discount_amount]" 
                                           placeholder="Discount" step="0.01" min="0" value="0">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-sm" onclick="addTestRow()">
                            <i class="fas fa-plus"></i> Add Test
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label for="entryNotes">Notes</label>
                        <textarea class="form-control" id="entryNotes" name="notes" rows="3" 
                                  placeholder="Additional notes or remarks..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Entry Modal -->
<div class="modal fade" id="viewEntryModal" tabindex="-1" role="dialog" aria-labelledby="viewEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="viewEntryModalLabel">
                    <i class="fas fa-eye mr-1"></i>Entry Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="entryDetails">
                <!-- Entry details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Confirm Delete
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this entry?</p>
                <p class="text-muted"><strong>Note:</strong> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash"></i> Delete Entry
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include footer -->
<?php include 'inc/footer.php'; ?>

<!-- Page-specific CSS -->
<link rel="stylesheet" href="assets/css/entry-list.css">

<!-- Page-specific JavaScript -->
<script>
// Global variables
let entriesTable;
let currentEntryId = null;
let testRowCount = 1;

// Initialize page when document is ready
$(document).ready(function() {
    initializePage();
});

// Initialize page components
function initializePage() {
    loadStatistics();
    initializeDataTable();
    loadPatients();
    loadDoctors();
    loadTests();
    setupEventHandlers();
}

// Load statistics
function loadStatistics() {
    $.ajax({
        url: 'ajax/entry_api.php',
        method: 'GET',
        data: { action: 'stats' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#totalEntries').text(response.data.total || 0);
                $('#pendingEntries').text(response.data.pending || 0);
                $('#completedEntries').text(response.data.completed || 0);
                $('#todayEntries').text(response.data.today || 0);
            }
        },
        error: function() {
            console.error('Failed to load statistics');
        }
    });
}

// Initialize DataTable
function initializeDataTable() {
    entriesTable = $('#entriesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'ajax/entry_api.php',
            type: 'GET',
            data: { action: 'list' },
            dataSrc: function(response) {
                if (response.success) {
                    return response.data;
                }
                return [];
            }
        },
        columns: [
            { 
                data: 'id',
                render: function(data, type, row) {
                    return `<span class="badge badge-primary">#${data}</span>`;
                }
            },
            { 
                data: 'patient_name',
                render: function(data, type, row) {
                    return `<div>
                        <strong>${data || 'N/A'}</strong>
                        ${row.uhid ? `<br><small class="text-muted">UHID: ${row.uhid}</small>` : ''}
                        ${row.age ? `<br><small class="text-muted">Age: ${row.age} ${row.gender || ''}</small>` : ''}
                    </div>`;
                }
            },
            { 
                data: 'doctor_name',
                render: function(data, type, row) {
                    return data || '<span class="text-muted">Not assigned</span>';
                }
            },
            { 
                data: 'test_name',
                render: function(data, type, row) {
                    if (row.tests_count > 1) {
                        return `<div>
                            <span class="badge badge-info">${row.tests_count} tests</span>
                            <br><small>${row.test_names}</small>
                        </div>`;
                    }
                    return data || '<span class="text-muted">No tests</span>';
                }
            },
            { 
                data: 'status',
                render: function(data, type, row) {
                    const statusClass = {
                        'pending': 'warning',
                        'completed': 'success',
                        'cancelled': 'danger'
                    }[data] || 'secondary';
                    return `<span class="badge badge-${statusClass}">${data}</span>`;
                }
            },
            { 
                data: 'final_amount',
                render: function(data, type, row) {
                    const amount = parseFloat(data || 0);
                    return `₹${amount.toFixed(2)}`;
                }
            },
            { 
                data: 'entry_date',
                render: function(data, type, row) {
                    if (data) {
                        const date = new Date(data);
                        return date.toLocaleDateString('en-IN');
                    }
                    return '<span class="text-muted">N/A</span>';
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `<div class="btn-group" role="group">
                        <button class="btn btn-info btn-sm" onclick="viewEntry(${row.id})" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editEntry(${row.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteEntry(${row.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>`;
                }
            }
        ],
        order: [[6, 'desc']], // Sort by date descending
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: true,
        language: {
            processing: "Loading entries...",
            emptyTable: "No entries found",
            zeroRecords: "No matching entries found"
        }
    });
}

// Load patients for dropdown
function loadPatients() {
    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const patientSelect = $('#patientSelect');
                patientSelect.empty().append('<option value="">Select Patient</option>');
                response.data.forEach(function(patient) {
                    patientSelect.append(`<option value="${patient.id}">${patient.name} (${patient.uhid || 'No UHID'})</option>`);
                });
                patientSelect.select2({
                    placeholder: 'Select Patient',
                    allowClear: true
                });
            }
        }
    });
}

// Load doctors for dropdown
function loadDoctors() {
    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const doctorSelect = $('#doctorSelect');
                doctorSelect.empty().append('<option value="">Select Doctor</option>');
                response.data.forEach(function(doctor) {
                    doctorSelect.append(`<option value="${doctor.id}">Dr. ${doctor.name}</option>`);
                });
                doctorSelect.select2({
                    placeholder: 'Select Doctor',
                    allowClear: true
                });
            }
        }
    });
}

// Load tests for dropdown
function loadTests() {
    $.ajax({
        url: 'ajax/test_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const testSelects = $('.test-select');
                testSelects.each(function() {
                    const $this = $(this);
                    $this.empty().append('<option value="">Select Test</option>');
                    response.data.forEach(function(test) {
                        $this.append(`<option value="${test.id}" data-price="${test.price || 0}">${test.name} - ₹${test.price || 0}</option>`);
                    });
                });
            }
        }
    });
}

// Setup event handlers
function setupEventHandlers() {
    // Form submission
    $('#entryForm').on('submit', function(e) {
        e.preventDefault();
        saveEntry();
    });
    
    // Delete confirmation
    $('#confirmDelete').on('click', function() {
        if (currentEntryId) {
            performDelete(currentEntryId);
        }
    });
    
    // Test price auto-fill
    $(document).on('change', '.test-select', function() {
        const price = $(this).find('option:selected').data('price');
        if (price) {
            $(this).closest('.test-row').find('input[name*="[price]"]').val(price);
        }
    });
}

// Open add entry modal
function openAddEntryModal() {
    currentEntryId = null;
    $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');
    $('#entryForm')[0].reset();
    $('#entryId').val('');
    $('#entryDate').val(new Date().toISOString().split('T')[0]);
    
    // Reset tests container
    $('#testsContainer').html(`
        <div class="test-row row mb-2">
            <div class="col-md-5">
                <select class="form-control test-select" name="tests[0][test_id]" required>
                    <option value="">Select Test</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="tests[0][price]" placeholder="Price" step="0.01" min="0" required>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="tests[0][discount_amount]" placeholder="Discount" step="0.01" min="0" value="0">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `);
    testRowCount = 1;
    
    loadTests();
    $('#entryModal').modal('show');
}

// Add test row
function addTestRow() {
    const newRow = `
        <div class="test-row row mb-2">
            <div class="col-md-5">
                <select class="form-control test-select" name="tests[${testRowCount}][test_id]" required>
                    <option value="">Select Test</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="tests[${testRowCount}][price]" placeholder="Price" step="0.01" min="0" required>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="tests[${testRowCount}][discount_amount]" placeholder="Discount" step="0.01" min="0" value="0">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    $('#testsContainer').append(newRow);
    testRowCount++;
    loadTests();
}

// Remove test row
function removeTestRow(button) {
    $(button).closest('.test-row').remove();
}

// Save entry
function saveEntry() {
    const formData = new FormData($('#entryForm')[0]);
    
    // Convert tests data to JSON
    const tests = [];
    $('.test-row').each(function() {
        const testId = $(this).find('.test-select').val();
        const price = $(this).find('input[name*="[price]"]').val();
        const discount = $(this).find('input[name*="[discount_amount]"]').val();
        
        if (testId && price) {
            tests.push({
                test_id: testId,
                price: parseFloat(price),
                discount_amount: parseFloat(discount || 0)
            });
        }
    });
    
    formData.set('tests', JSON.stringify(tests));
    
    $.ajax({
        url: 'ajax/entry_api.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                toastr.success(response.message || 'Entry saved successfully');
                $('#entryModal').modal('hide');
                refreshTable();
                loadStatistics();
            } else {
                toastr.error(response.message || 'Failed to save entry');
            }
        },
        error: function() {
            toastr.error('An error occurred while saving the entry');
        }
    });
}

// View entry
function viewEntry(id) {
    $.ajax({
        url: 'ajax/entry_api.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayEntryDetails(response.data);
                $('#viewEntryModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load entry details');
            }
        }
    });
}

// Display entry details
function displayEntryDetails(entry) {
    const details = `
        <div class="row">
            <div class="col-md-6">
                <h6><strong>Entry Information</strong></h6>
                <p><strong>ID:</strong> #${entry.id}</p>
                <p><strong>Date:</strong> ${new Date(entry.entry_date).toLocaleDateString('en-IN')}</p>
                <p><strong>Status:</strong> <span class="badge badge-${entry.status === 'completed' ? 'success' : entry.status === 'pending' ? 'warning' : 'danger'}">${entry.status}</span></p>
                <p><strong>Tests Count:</strong> ${entry.tests_count || 0}</p>
            </div>
            <div class="col-md-6">
                <h6><strong>Patient Information</strong></h6>
                <p><strong>Name:</strong> ${entry.patient_name || 'N/A'}</p>
                <p><strong>UHID:</strong> ${entry.uhid || 'N/A'}</p>
                <p><strong>Age/Gender:</strong> ${entry.age ? entry.age + ' ' + (entry.gender || '') : 'N/A'}</p>
                <p><strong>Doctor:</strong> ${entry.doctor_name || 'Not assigned'}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <h6><strong>Tests & Pricing</strong></h6>
                <p><strong>Tests:</strong> ${entry.test_names || 'No tests'}</p>
                <p><strong>Total Amount:</strong> ₹${parseFloat(entry.final_amount || 0).toFixed(2)}</p>
                ${entry.notes ? `<p><strong>Notes:</strong> ${entry.notes}</p>` : ''}
            </div>
        </div>
    `;
    $('#entryDetails').html(details);
}

// Edit entry
function editEntry(id) {
    // Similar to view but populate form for editing
    $.ajax({
        url: 'ajax/entry_api.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateEditForm(response.data);
                $('#entryModal').modal('show');
            } else {
                toastr.error(response.message || 'Failed to load entry for editing');
            }
        }
    });
}

// Populate edit form
function populateEditForm(entry) {
    currentEntryId = entry.id;
    $('#entryModalLabel').html('<i class="fas fa-edit mr-1"></i>Edit Entry');
    $('#entryId').val(entry.id);
    $('#patientSelect').val(entry.patient_id).trigger('change');
    $('#doctorSelect').val(entry.doctor_id).trigger('change');
    $('#entryDate').val(entry.entry_date);
    $('#entryStatus').val(entry.status);
    $('#entryNotes').val(entry.notes || '');
    
    // For now, we'll show a simple edit form
    // In a full implementation, you'd populate the tests section
}

// Delete entry
function deleteEntry(id) {
    currentEntryId = id;
    $('#deleteModal').modal('show');
}

// Perform delete
function performDelete(id) {
    $.ajax({
        url: 'ajax/entry_api.php',
        method: 'POST',
        data: { action: 'delete', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                toastr.success(response.message || 'Entry deleted successfully');
                $('#deleteModal').modal('hide');
                refreshTable();
                loadStatistics();
            } else {
                toastr.error(response.message || 'Failed to delete entry');
            }
        },
        error: function() {
            toastr.error('An error occurred while deleting the entry');
        }
    });
}

// Apply filters
function applyFilters() {
    const status = $('#statusFilter').val();
    const dateFilter = $('#dateFilter').val();
    const patient = $('#patientFilter').val();
    const doctor = $('#doctorFilter').val();
    
    // Add custom filtering logic here
    // For now, we'll use DataTables built-in search
    let searchTerm = '';
    if (patient) searchTerm += patient + ' ';
    if (doctor) searchTerm += doctor + ' ';
    
    entriesTable.search(searchTerm).draw();
}

// Filter by status
function filterByStatus(status) {
    $('#statusFilter').val(status).trigger('change');
    applyFilters();
}

// Filter by date
function filterByDate(dateFilter) {
    $('#dateFilter').val(dateFilter).trigger('change');
    applyFilters();
}

// Export entries
function exportEntries() {
    // Show export options modal
    const status = $('#statusFilter').val();
    const dateFilter = $('#dateFilter').val();
    
    const exportModal = `
        <div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title"><i class="fas fa-download mr-1"></i>Export Entries</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Export Format:</label>
                            <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                <label class="btn btn-outline-primary active">
                                    <input type="radio" name="exportFormat" value="csv" checked> CSV
                                </label>
                                <label class="btn btn-outline-success">
                                    <input type="radio" name="exportFormat" value="json"> JSON
                                </label>
                                <label class="btn btn-outline-info">
                                    <input type="radio" name="exportFormat" value="excel"> Excel
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Export Options:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeFilters" checked>
                                <label class="form-check-label" for="includeFilters">
                                    Include current filters
                                </label>
                            </div>
                        </div>
                        ${status || dateFilter ? `
                        <div class="alert alert-info">
                            <strong>Current Filters:</strong><br>
                            ${status ? `Status: ${status}<br>` : ''}
                            ${dateFilter ? `Date: ${dateFilter}<br>` : ''}
                        </div>
                        ` : ''}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" onclick="performExport('${status}', '${dateFilter}')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    $('#exportModal').remove();
    
    // Add modal to body
    $('body').append(exportModal);
    
    // Show modal
    $('#exportModal').modal('show');
}

// Perform export
function performExport(status, dateFilter) {
    const format = $('input[name="exportFormat"]:checked').val();
    const includeFilters = $('#includeFilters').is(':checked');
    
    let exportUrl = `ajax/entry_api.php?action=export&format=${format}`;
    
    if (includeFilters) {
        if (status) exportUrl += `&status=${status}`;
        if (dateFilter) exportUrl += `&date=${dateFilter}`;
    }
    
    // Close modal
    $('#exportModal').modal('hide');
    
    // Show loading
    toastr.info('Preparing export...', 'Export', {timeOut: 2000});
    
    if (format === 'csv') {
        // Direct download for CSV
        window.open(exportUrl, '_blank');
    } else {
        // Handle other formats
        $.ajax({
            url: exportUrl,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (format === 'json') {
                        // Download JSON file
                        const blob = new Blob([JSON.stringify(response.data, null, 2)], {type: 'application/json'});
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `entries_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.json`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                    } else if (format === 'excel') {
                        // Convert to Excel format (simplified - would need a proper Excel library for full functionality)
                        exportToExcel(response.data);
                    }
                    toastr.success(`Export completed successfully! ${response.total} entries exported.`);
                } else {
                    toastr.error(response.message || 'Export failed');
                }
            },
            error: function() {
                toastr.error('Export failed. Please try again.');
            }
        });
    }
}

// Export to Excel (simplified version)
function exportToExcel(data) {
    // Create HTML table for Excel
    let html = '<table border="1"><tr>';
    
    // Headers
    if (data.length > 0) {
        Object.keys(data[0]).forEach(key => {
            html += `<th>${key}</th>`;
        });
        html += '</tr>';
        
        // Data rows
        data.forEach(row => {
            html += '<tr>';
            Object.values(row).forEach(value => {
                html += `<td>${value}</td>`;
            });
            html += '</tr>';
        });
    }
    
    html += '</table>';
    
    // Create and download file
    const blob = new Blob([html], {type: 'application/vnd.ms-excel'});
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `entries_${new Date().toISOString().slice(0,19).replace(/:/g, '-')}.xls`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Refresh table
function refreshTable() {
    entriesTable.ajax.reload();
}

// Initialize Select2 for modals
$(document).ready(function() {
    $('.select2').select2({
        dropdownParent: $('#entryModal')
    });
});
</script>

<?php include 'inc/footer.php'; ?>