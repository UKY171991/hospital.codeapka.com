<?php
$pageSpecificCSS = '<link rel="stylesheet" href="assets/css/opd-theme.css?v=' . time() . '">';
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper opd-page">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-file-invoice-dollar mr-2"></i>OPD Billing Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">OPD Billing</li>
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
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalBills">0</h3>
                            <p>Total Bills</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="paidBills">0</h3>
                            <p>Paid</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="unpaidBills">0</h3>
                            <p>Unpaid</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="partialBills">0</h3>
                            <p>Partial</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="totalRevenue">₹0</h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3 id="pendingAmount">₹0</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                Billing Records
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" id="addBillingBtn">
                                    <i class="fas fa-plus"></i> Add New Bill
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Billing Table -->
                            <div class="table-responsive">
                                <table id="opdBillingTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Bill ID</th>
                                            <th>Patient Name</th>
                                            <th>Doctor</th>
                                            <th>Bill Date</th>
                                            <th>Total Amount</th>
                                            <th>Balance</th>
                                            <th>Payment Status</th>
                                            <th>Payment Method</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
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

<!-- Add/Edit Billing Modal -->
<div class="modal fade" id="billingModal" tabindex="-1" role="dialog" aria-labelledby="billingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add New Bill</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="billingForm">
                <div class="modal-body">
                    <input type="hidden" id="billingId" name="id">
                    
                    <!-- Patient Information -->
                    <h5 class="mb-3"><i class="fas fa-user-injured mr-2"></i>Patient Information</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="patientName">
                                    Patient Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="patientName" name="patient_name" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="patientPhone">Phone Number</label>
                                <input type="text" class="form-control" id="patientPhone" name="patient_phone">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="patientAge">Age</label>
                                <input type="number" class="form-control" id="patientAge" name="patient_age" min="0" max="150">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="patientGender">Gender</label>
                                <select class="form-control" id="patientGender" name="patient_gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Doctor & Date Information -->
                    <h5 class="mb-3"><i class="fas fa-user-md mr-2"></i>Doctor & Date</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorName">Doctor Name</label>
                                <select class="form-control" id="doctorName" name="doctor_name">
                                    <option value="">Select Doctor</option>
                                    <!-- Doctors will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="billDate">Bill Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="billDate" name="bill_date" required>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Charges Information -->
                    <h5 class="mb-3"><i class="fas fa-calculator mr-2"></i>Charges</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="consultationFee">Consultation Fee</label>
                                <input type="number" class="form-control charge-input" id="consultationFee" name="consultation_fee" value="0" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="medicineCharges">Medicine Charges</label>
                                <input type="number" class="form-control charge-input" id="medicineCharges" name="medicine_charges" value="0" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="labCharges">Lab Charges</label>
                                <input type="number" class="form-control charge-input" id="labCharges" name="lab_charges" value="0" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="otherCharges">Other Charges</label>
                                <input type="number" class="form-control charge-input" id="otherCharges" name="other_charges" value="0" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="discount">Discount</label>
                                <input type="number" class="form-control charge-input" id="discount" name="discount" value="0" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="totalAmount">Total Amount</label>
                                <input type="text" class="form-control bg-light" id="totalAmount" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="paidAmount">Paid Amount</label>
                                <input type="number" class="form-control charge-input" id="paidAmount" name="paid_amount" value="0" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="balanceAmount">Balance Amount</label>
                                <input type="text" class="form-control bg-light" id="balanceAmount" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="paymentMethod">Payment Method</label>
                                <select class="form-control" id="paymentMethod" name="payment_method">
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Online">Online</option>
                                    <option value="Insurance">Insurance</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="paymentStatus">Payment Status</label>
                                <input type="text" class="form-control bg-light" id="paymentStatus" readonly>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Notes -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Bill
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Billing Modal -->
<div class="modal fade" id="viewBillingModal" tabindex="-1" role="dialog" aria-labelledby="viewBillingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>
                    Bill Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewBillingContent">
                <!-- Bill details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-warning" onclick="editBillingFromView()">
                    <i class="fas fa-edit"></i> Edit Bill
                </button>
                <button type="button" class="btn btn-info" onclick="printBillDetails()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- Page specific JavaScript -->
<script src="assets/js/opd_billing.js?v=<?php echo time(); ?>"></script>

<style>
.small-box {
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 0.25rem;
}

.small-box > .inner {
    padding: 10px;
}

.small-box .icon {
    transition: all .3s linear;
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 90px;
    color: rgba(0,0,0,0.15);
}

.table-responsive {
    border-radius: 0.375rem;
    overflow-x: auto;
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

#opdBillingTable {
    width: 100% !important;
    white-space: nowrap;
}

#opdBillingTable thead th {
    vertical-align: middle;
    white-space: nowrap;
    padding: 12px 8px;
    font-size: 14px;
}

#opdBillingTable tbody td {
    vertical-align: middle;
    padding: 8px;
    font-size: 13px;
}

#opdBillingTable_wrapper .dataTables_scroll {
    overflow-x: auto;
}

#billingModal .form-group {
    margin-bottom: 1rem;
}

#billingModal label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.modal-xl {
    max-width: 1200px;
}
</style>

<!-- Page specific JavaScript -->
<script src="assets/js/opd_billing.js?v=<?php echo time(); ?>"></script>

<?php require_once 'inc/footer.php'; ?>
