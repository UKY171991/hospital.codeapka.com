<?php
// Define page-specific CSS
$pageSpecificCSS = '<link rel="stylesheet" href="assets/css/entry-list.css">';

// Include header
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

// Ensure variables are properly set for JavaScript
$currentUserId = $currentUserId ?: '';
$currentUserDisplayName = $currentUserDisplayName ?: 'Unknown User';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper entry-page-modal-override">
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
                                    <button type="button" class="btn btn-warning btn-sm" onclick="cleanupDuplicates()" title="Remove duplicate test entries">
                                        <i class="fas fa-broom"></i> Clean Duplicates
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
                                            <th width="12%">Patient</th>
                                            <th width="10%">Doctor</th>
                                            <th width="15%">Tests</th>
                                            <th width="7%">Status</th>
                                            <th width="7%">Priority</th>
                                            <th width="8%">Amount</th>
                                            <th width="8%">Date</th>
                                            <th width="7%">Added By</th>
                                            <th width="9%">Actions</th>
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
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
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
                    <input type="hidden" id="isNewPatient" name="is_new_patient" value="false">
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ownerAddedBySelect">Owner/Added By <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="ownerAddedBySelect" name="owner_added_by" required>
                                    <option value="">Select Owner/User</option>
                                </select>
                                <small class="form-text text-muted">Select the owner/user to filter patients and doctors</small>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="patientSelect">Patient <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="patientSelect" name="patient_id" required disabled>
                                    <option value="">Select Owner/User first to load patients</option>
                                </select>
                                <small class="form-text text-muted" id="patientHelpText">Select an owner/user above to load patients</small>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="doctorSelect">Doctor</label>
                                <select class="form-control select2" id="doctorSelect" name="doctor_id" disabled>
                                    <option value="">Select Owner/User first to load doctors</option>
                                </select>
                                <small class="form-text text-muted" id="doctorHelpText">Select an owner/user above to load doctors</small>
                            </div>
                        </div>
                    
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="entryDate">Entry Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="entryDate" name="entry_date" 
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="entryStatus">Status</label>
                                <select class="form-control select2" id="entryStatus" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Patient Information Section -->
                    <div class="card mt-3 mb-3 patient-info-card" id="patientInfoCard">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-user mr-1"></i>
                                Patient Information
                                <span id="patientModeIndicator" class="patient-info-mode-indicator new-patient">New Patient Mode</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="patientName">Patient Name</label>
                                        <input type="text" class="form-control patient-info-field" id="patientName" name="patient_name" 
                                               placeholder="Enter patient name...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="patientContact">Patient Contact</label>
                                        <input type="text" class="form-control patient-info-field" id="patientContact" name="patient_contact" 
                                               placeholder="Phone number or email...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="patientAge">Age</label>
                                        <input type="number" class="form-control patient-info-field" id="patientAge" name="age" 
                                               placeholder="Age" min="0" max="150">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="patientGender">Gender</label>
                                        <select class="form-control select2 patient-info-field" id="patientGender" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="patientAddress">Patient Address</label>
                                        <textarea class="form-control patient-info-field" id="patientAddress" name="patient_address" rows="2" 
                                                  placeholder="Patient address..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tests Section -->
                    <div class="form-group">
                        <label>Tests <span class="text-danger">*</span></label>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <small class="text-muted">Test Name</small>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted">Category</small>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted">Result</small>
                            </div>
                            <div class="col-md-1">
                                <small class="text-muted">Min</small>
                            </div>
                            <div class="col-md-1">
                                <small class="text-muted">Max</small>
                            </div>
                            <div class="col-md-2">
                                <small class="text-muted">Unit</small>
                            </div>
                            <div class="col-md-1">
                                <small class="text-muted">Action</small>
                            </div>
                        </div>
                        <div id="testsContainer">
                            <div class="test-row row mb-2">
                                <div class="col-md-3">
                                    <select class="form-control test-select select2" name="tests[0][test_id]" required>
                                        <option value="">Select Test</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control test-category" name="tests[0][category_name]" placeholder="Category" readonly>
                                    <input type="hidden" name="tests[0][category_id]" class="test-category-id">
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control test-result" name="tests[0][result_value]" placeholder="Result">
                                </div>
                                <div class="col-md-1">
                                    <input type="text" class="form-control test-min" name="tests[0][min]" placeholder="Min" readonly>
                                </div>
                                <div class="col-md-1">
                                    <input type="text" class="form-control test-max" name="tests[0][max]" placeholder="Max" readonly>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control test-unit" name="tests[0][unit]" placeholder="Unit" readonly>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeTestRow(this)" title="Remove Test">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-sm" onclick="addTestRow()">
                            <i class="fas fa-plus"></i> Add Test
                        </button>
                    </div>
                    
                    <!-- Additional Information Section -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="referralSource">Referral Source</label>
                                <select class="form-control select2" id="referralSource" name="referral_source">
                                    <option value="">Select Source</option>
                                    <option value="doctor">Doctor Referral</option>
                                    <option value="hospital">Hospital</option>
                                    <option value="walk_in">Walk-in</option>
                                    <option value="online">Online Booking</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <select class="form-control select2" id="priority" name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="emergency">Emergency</option>
                                    <option value="routine">Routine</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pricing Information Section -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-money-bill-wave mr-1"></i>
                                Pricing Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="subtotal">Subtotal (₹)</label>
                                        <input type="number" class="form-control" id="subtotal" name="subtotal" 
                                               placeholder="0.00" step="0.01" min="0" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="discountAmount">Discount Amount (₹)</label>
                                        <input type="number" class="form-control" id="discountAmount" name="discount_amount" 
                                               placeholder="0.00" step="0.01" min="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="totalPrice">Total Amount (₹)</label>
                                        <input type="number" class="form-control" id="totalPrice" name="total_price" 
                                               placeholder="0.00" step="0.01" min="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    <div class="modal-dialog modal-xl" role="document" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewEntryModalLabel">
                    <i class="fas fa-eye mr-2"></i>Entry Details - Complete Information
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="entryDetails" style="max-height: 70vh; overflow-y: auto;">
                <!-- Entry details will be loaded here -->
                <div class="text-center p-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
                    <p class="mt-3 text-muted">Loading entry details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printEntryDetails()">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
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

<script>
    console.log('Setting global variables...');
    const currentUserId = <?php echo json_encode($currentUserId); ?>;
    const currentUserDisplayName = <?php echo json_encode($currentUserDisplayName); ?>;
    console.log('Global variables set:', { currentUserId, currentUserDisplayName });
</script>
<script src="assets/js/entry-list.new.js?v=<?php echo time(); ?>"></script>
<script>
// Fallback error handling if main script fails to load
setTimeout(function() {
    if (typeof checkDependencies === 'undefined') {
        console.error('Main JavaScript file failed to load');
        alert('JavaScript files failed to load. Please refresh the page.');
    }
}, 1000);

// Debug function to test API response
function debugTestData() {
    console.log('Testing API response...');
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            console.log('API Response:', response);
            if (response.success && response.data) {
                console.log('First entry data:', response.data[0]);
                response.data.forEach(function(entry, index) {
                    if (entry.tests_count > 1) {
                        console.log(`Entry ${entry.id} has ${entry.tests_count} tests: ${entry.test_names}`);
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('API Error:', error);
        }
    });
}

// Function to refresh aggregates for a specific entry
function refreshAggregates(entryId) {
    if (!entryId) {
        entryId = prompt('Enter Entry ID to refresh aggregates:');
        if (!entryId) return;
    }
    
    console.log('Refreshing aggregates for entry:', entryId);
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'refresh_aggregates', entry_id: entryId },
        dataType: 'json',
        success: function(response) {
            console.log('Refresh response:', response);
            if (response.success) {
                alert('Aggregates refreshed for entry ' + entryId + '\nTests count: ' + (response.entry_data.tests_count || 0) + '\nTest names: ' + (response.entry_data.test_names || 'None'));
                // Refresh the table
                if (typeof entriesTable !== 'undefined' && entriesTable) {
                    entriesTable.ajax.reload();
                }
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Refresh error:', error);
            alert('Error refreshing aggregates: ' + error);
        }
    });
}

// Function to debug all entries with multiple tests
function debugAllEntries() {
    console.log('Debugging all entries with multiple tests...');
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'debug_all_entries' },
        dataType: 'json',
        success: function(response) {
            console.log('Debug all entries response:', response);
            if (response.success) {
                console.table(response.entries_with_multiple_tests);
                alert('Found ' + response.count + ' entries with multiple tests. Check console for details.');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Debug error:', error);
            alert('Error debugging entries: ' + error);
        }
    });
}

// Function to test aggregation SQL
function testAggregationSQL() {
    console.log('Testing aggregation SQL...');
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'test_aggregation_sql' },
        dataType: 'json',
        success: function(response) {
            console.log('Aggregation SQL test response:', response);
            if (response.success) {
                console.log('SQL:', response.sql);
                console.table(response.results);
                alert('Aggregation SQL test completed. Found ' + response.count + ' aggregated entries. Check console for details.');
            } else {
                alert('Error testing aggregation SQL: ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Aggregation SQL test error:', error);
            alert('Error testing aggregation SQL: ' + error);
        }
    });
}

// Function to refresh all aggregates
function refreshAllAggregates() {
    if (!confirm('This will refresh aggregates for all entries with tests. Continue?')) {
        return;
    }
    
    console.log('Refreshing all aggregates...');
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'refresh_all_aggregates' },
        dataType: 'json',
        success: function(response) {
            console.log('Refresh all aggregates response:', response);
            if (response.success) {
                alert('Successfully refreshed aggregates for ' + response.refreshed_count + ' entries.');
                // Refresh the table
                if (typeof entriesTable !== 'undefined' && entriesTable) {
                    entriesTable.ajax.reload();
                }
            } else {
                alert('Error refreshing all aggregates: ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('Refresh all aggregates error:', error);
            alert('Error refreshing all aggregates: ' + error);
        }
    });
}

// Function to test auto-population of test data
function testAutoPopulation() {
    console.log('Testing auto-population of test data...');
    
    // Open the add entry modal
    openAddEntryModal();
    
    // Wait a bit for the modal to open and tests to load
    setTimeout(function() {
        if (window.testsData && window.testsData.length > 0) {
            // Get the first test row
            const firstTestRow = $('.test-row').first();
            const testSelect = firstTestRow.find('.test-select');
            
            // Select the first available test
            const firstTest = window.testsData[0];
            testSelect.val(firstTest.id).trigger('change');
            
            console.log('Auto-populated test data for test:', firstTest.name);
            console.log('Category:', firstTestRow.find('.test-category').val());
            console.log('Unit:', firstTestRow.find('.test-unit').val());
            console.log('Min:', firstTestRow.find('.test-min').val());
            console.log('Max:', firstTestRow.find('.test-max').val());
            console.log('Price:', firstTestRow.find('.test-price').val());
            
            alert('Test auto-population completed. Check console for details.');
        } else {
            alert('No test data available for testing.');
        }
    }, 1000);
}

// Function to debug entry 17 specifically
function debugEntry17() {
    console.log('Debugging entry 17...');
    
    // First check the API response
    $.ajax({
        url: 'ajax/entry_api_fixed.php',
        method: 'GET',
        data: { action: 'get', id: 17 },
        dataType: 'json',
        success: function(response) {
            console.log('Entry 17 API response:', response);
            if (response.success && response.data.tests) {
                console.log('Entry 17 tests:', response.data.tests);
                response.data.tests.forEach(function(test, index) {
                    console.log(`Test ${index + 1}:`, {
                        test_id: test.test_id,
                        test_name: test.test_name,
                        category_name: test.category_name,
                        unit: test.unit,
                        min: test.min,
                        max: test.max,
                        price: test.price
                    });
                });
            }
            
            // Also check if testsData is loaded
            console.log('Available testsData:', window.testsData ? window.testsData.length + ' tests' : 'Not loaded');
            if (window.testsData) {
                console.log('First few tests:', window.testsData.slice(0, 3));
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading entry 17:', error);
        }
    });
}

// Add debug buttons to test
$(document).ready(function() {
    setTimeout(function() {
        if ($('.card-tools .btn-group').length > 0) {
            $('.card-tools .btn-group').append(
                '<button type="button" class="btn btn-secondary btn-sm" onclick="debugTestData()" title="Debug Test Data">' +
                '<i class="fas fa-bug"></i> Debug</button>' +
                '<button type="button" class="btn btn-warning btn-sm" onclick="refreshAggregates()" title="Refresh Aggregates">' +
                '<i class="fas fa-sync"></i> Refresh Agg</button>' +
                '<button type="button" class="btn btn-info btn-sm" onclick="debugAllEntries()" title="Debug All Entries">' +
                '<i class="fas fa-search"></i> Debug All</button>' +
                '<button type="button" class="btn btn-primary btn-sm" onclick="testAggregationSQL()" title="Test Aggregation SQL">' +
                '<i class="fas fa-database"></i> Test SQL</button>' +
                '<button type="button" class="btn btn-success btn-sm" onclick="refreshAllAggregates()" title="Refresh All Aggregates">' +
                '<i class="fas fa-sync-alt"></i> Refresh All</button>' +
                '<button type="button" class="btn btn-purple btn-sm" onclick="testAutoPopulation()" title="Test Auto-Population">' +
                '<i class="fas fa-magic"></i> Test Auto</button>' +
                '<button type="button" class="btn btn-dark btn-sm" onclick="debugEntry17()" title="Debug Entry 17">' +
                '<i class="fas fa-bug"></i> Debug 17</button>'
            );
        }
    }, 1000);
});
</script>
