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
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
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
                        <a href="#" class="small-box-footer" onclick="window.entryManager.filterByStatus('all')">
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
                        <a href="#" class="small-box-footer" onclick="window.entryManager.filterByStatus('pending')">
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
                        <a href="#" class="small-box-footer" onclick="window.entryManager.filterByStatus('completed')">
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
                        <a href="#" class="small-box-footer" onclick="window.entryManager.filterByDate('today')">
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
                                    <button type="button" class="btn btn-primary btn-sm" onclick="window.entryManager.openAddModal()">
                                        <i class="fas fa-plus"></i> Add Entry
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm" onclick="window.entryManager.exportEntries()">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="window.entryManager.refreshTable()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Filters Row -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="statusFilter">Status Filter:</label>
                                        <select class="form-control form-control-sm" id="statusFilter">
                                            <option value="">All Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="mainCategoryFilter">Main Category:</label>
                                        <select class="form-control form-control-sm" id="mainCategoryFilter">
                                            <option value="">All Categories</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="dateFilter">Date Range:</label>
                                        <select class="form-control form-control-sm" id="dateFilter">
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
                                               placeholder="Search by patient name...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="doctorFilter">Doctor:</label>
                                        <input type="text" class="form-control form-control-sm" id="doctorFilter" 
                                               placeholder="Search by doctor name...">
                                    </div>
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="table-responsive">
                                <table id="entriesTable" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="4%">ID</th>
                                            <th width="12%">Patient</th>
                                            <th width="10%">Doctor</th>
                                            <th width="14%">Tests</th>
                                            <th width="12%">Test Category</th>
                                            <th width="8%">Status</th>
                                            <th width="8%">Priority</th>
                                            <th width="8%">Amount</th>
                                            <th width="8%">Date</th>
                                            <th width="8%">Added By</th>
                                            <th width="8%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
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
                    
                    <!-- Basic Information Row -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ownerAddedBySelect">Owner/Added By <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="ownerAddedBySelect" name="owner_added_by" required>
                                    <option value="">Select Owner/User</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="patientSelect">Patient <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="patientSelect" name="patient_id" required disabled>
                                    <option value="">Select Owner/User first</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="doctorSelect">Doctor</label>
                                <select class="form-control select2" id="doctorSelect" name="doctor_id" disabled>
                                    <option value="">Select Owner/User first</option>
                                </select>
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
                    <div class="card mt-3 mb-3" id="patientInfoCard">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-user mr-1"></i>
                                Patient Information
                                <span id="patientModeIndicator" class="badge badge-info ml-2">Existing Patient</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="patientName">Patient Name</label>
                                        <input type="text" class="form-control" id="patientName" name="patient_name" 
                                               placeholder="Enter patient name..." readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="patientContact">Patient Contact</label>
                                        <input type="text" class="form-control" id="patientContact" name="patient_contact" 
                                               placeholder="Phone number or email..." readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="patientAge">Age</label>
                                        <input type="number" class="form-control" id="patientAge" name="age" 
                                               placeholder="Age" min="0" max="150" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="patientGender">Gender</label>
                                        <select class="form-control select2" id="patientGender" name="gender" disabled>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="patientAddress">Address</label>
                                        <textarea class="form-control" id="patientAddress" name="patient_address" rows="2" 
                                                  placeholder="Patient address..." readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test Category Filter Section -->
                    <div class="card mt-3 mb-3" style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%); color: white;">
                        <div class="card-body py-2">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <h6 class="mb-0 text-white">
                                        <i class="fas fa-filter mr-1"></i> Test Category Filter
                                    </h6>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-white-50">Filter Tests by Category</small>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control form-control-sm" id="categoryFilter" style="background: white; border: none;">
                                        <option value="">All Categories (Show All Tests)</option>
                                        <!-- Categories will be populated via JavaScript -->
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-light btn-sm" id="clearCategoryFilter">
                                        <i class="fas fa-times mr-1"></i> Clear Filter
                                    </button>
                                </div>
                                <div class="col-md-1">
                                    <small class="text-white-50">
                                        <span id="filteredTestCount">0</span> tests available
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tests Section -->
                    <div class="form-group">
                        <label>Tests <span class="text-danger">*</span></label>
                        <div class="row mb-2">
                            <div class="col-md-2">
                                <small class="text-muted">Category</small>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Test Name</small>
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
                            <!-- Test rows will be added dynamically -->
                        </div>
                        <button type="button" class="btn btn-success btn-sm" onclick="window.entryManager.addTestRow()">
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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewEntryModalLabel">
                    <i class="fas fa-eye mr-2"></i>Entry Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="entryDetails">
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
                <button type="button" class="btn btn-primary" onclick="window.entryManager.printEntryDetails()">
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

<!-- Page-specific JavaScript -->
<script>
    // Set global variables for JavaScript
    const currentUserId = <?php echo json_encode($currentUserId); ?>;
    const currentUserDisplayName = <?php echo json_encode($currentUserDisplayName); ?>;
    const currentUserRole = <?php echo json_encode($currentUserRole); ?>;
</script>
<script src="assets/js/entry-list.js?v=<?php echo time(); ?>"></script>