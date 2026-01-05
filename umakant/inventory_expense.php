<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-arrow-down mr-2"></i>Expense Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="inventory_dashboard.php">Inventory</a></li>
                        <li class="breadcrumb-item active">Expense</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Filter Row -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="expenseFilterForm" class="row align-items-end">
                        <div class="col-md-3">
                            <label>Year</label>
                            <select id="filterYear" class="form-control">
                                <option value="">All Years</option>
                                <?php
                                $startYear = 2020;
                                $currentYear = date('Y');
                                for ($y = $currentYear; $y >= $startYear; $y--) {
                                    echo "<option value='$y'>$y</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Month</label>
                            <select id="filterMonth" class="form-control">
                                <option value="">All Months</option>
                                <?php
                                for ($m = 1; $m <= 12; $m++) {
                                    $monthName = date('F', mktime(0, 0, 0, $m, 1));
                                    echo "<option value='$m'>$monthName</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="applyFilters" class="btn btn-primary btn-block">
                                <i class="fas fa-filter mr-1"></i>Apply
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                Expense Records
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-danger btn-sm" onclick="openExpenseModal()">
                                    <i class="fas fa-plus"></i> Add Expense
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="expenseTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Vendor</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="expenseTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Expense Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-arrow-down mr-2"></i>
                    <span id="expenseModalTitle">Add Expense</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="expenseForm">
                <div class="modal-body">
                    <input type="hidden" id="expenseId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expenseDate">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expenseDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expenseCategory">Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="expenseCategory" required>
                                    <option value="">Select Category</option>
                                    <option value="Medical Supplies">Medical Supplies</option>
                                    <option value="Equipment">Equipment</option>
                                    <option value="Utilities">Utilities</option>
                                    <option value="Salaries">Salaries</option>
                                    <option value="Rent">Rent</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Transportation">Transportation</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expenseVendor">Vendor/Supplier</label>
                        <input type="text" class="form-control" id="expenseVendor">
                    </div>

                    <div class="form-group">
                        <label for="expenseDescription">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="expenseDescription" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="expenseAmount">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="expenseAmount" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="expensePaymentMethod">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-control" id="expensePaymentMethod" required>
                                    <option value="">Select Method</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="expensePaymentStatus">Payment Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="expensePaymentStatus" required>
                                    <option value="Success">Success</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Failed">Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expenseInvoiceNumber">Invoice/Bill Number</label>
                        <input type="text" class="form-control" id="expenseInvoiceNumber">
                    </div>

                    <div class="form-group">
                        <label for="expenseNotes">Notes</label>
                        <textarea class="form-control" id="expenseNotes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save mr-1"></i> Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let expenseTable;

$(document).ready(function() {
    // Set today's date as default
    $('#expenseDate').val(new Date().toISOString().split('T')[0]);

    // Load expense records
    loadExpenseRecords();

    $('#applyFilters').click(function() {
        loadExpenseRecords();
    });

    // Form submit
    $('#expenseForm').on('submit', function(e) {
        e.preventDefault();
        saveExpense();
    });
});

function loadExpenseRecords() {
    const year = $('#filterYear').val();
    const month = $('#filterMonth').val();

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { 
            action: 'get_expense_records',
            year: year,
            month: month,
            _: new Date().getTime() // Cache buster
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayExpenseRecords(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading expenses:', error);
            toastr.error('Failed to load expense records');
        }
    });
}

function displayExpenseRecords(records) {
    // Destroy existing DataTable first
    if ($.fn.DataTable.isDataTable('#expenseTable')) {
        $('#expenseTable').DataTable().destroy();
    }
    
    const tbody = $('#expenseTableBody');
    tbody.empty();

    if (!records || records.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center">No expense records found</td></tr>');
        return;
    }

    // Sort records by date (descending - newest first)
    records.sort((a, b) => {
        const dateA = new Date(a.date);
        const dateB = new Date(b.date);
        return dateB - dateA; // Descending order
    });

    let srNo = 1;
    records.forEach(function(record) {
        // Default to 'Success' if payment_status is not set
        const paymentStatus = record.payment_status || 'Success';
        const statusBadge = paymentStatus === 'Success' ? 
            '<span class="badge badge-success">Success</span>' : 
            paymentStatus === 'Pending' ? 
            '<span class="badge badge-warning">Pending</span>' : 
            '<span class="badge badge-danger">Failed</span>';
        
        const row = `
            <tr>
                <td>${srNo++}</td>
                <td>${record.date}</td>
                <td>${record.category}</td>
                <td>${record.description}</td>
                <td>${record.vendor || '-'}</td>
                <td>₹${parseFloat(record.amount).toFixed(2)}</td>
                <td>${record.payment_method}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editExpense(${record.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteExpense(${record.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Initialize DataTable with fresh data
    expenseTable = $('#expenseTable').DataTable({
        responsive: true,
        order: [[1, 'desc']], // Sort by Date column (descending)
        destroy: true,
        columnDefs: [
            { orderable: false, targets: [0, 8] } // Disable sorting on Sr. No. and Actions
        ]
    });
}

function openExpenseModal() {
    $('#expenseId').val('');
    $('#expenseForm')[0].reset();
    $('#expenseDate').val(new Date().toISOString().split('T')[0]);
    $('#expenseModalTitle').text('Add Expense');
    $('#expenseModal').modal('show');
}

function editExpense(id) {
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { action: 'get_expense', id: id },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                $('#expenseId').val(data.id);
                $('#expenseDate').val(data.date);
                $('#expenseCategory').val(data.category);
                $('#expenseVendor').val(data.vendor);
                $('#expenseDescription').val(data.description);
                $('#expenseAmount').val(data.amount);
                $('#expensePaymentMethod').val(data.payment_method);
                $('#expensePaymentStatus').val(data.payment_status || 'Success');
                $('#expenseInvoiceNumber').val(data.invoice_number);
                $('#expenseNotes').val(data.notes);
                $('#expenseModalTitle').text('Edit Expense');
                $('#expenseModal').modal('show');
            }
        }
    });
}

function saveExpense() {
    const formData = {
        action: $('#expenseId').val() ? 'update_expense' : 'add_expense',
        id: $('#expenseId').val(),
        date: $('#expenseDate').val(),
        category: $('#expenseCategory').val(),
        vendor: $('#expenseVendor').val(),
        description: $('#expenseDescription').val(),
        amount: $('#expenseAmount').val(),
        payment_method: $('#expensePaymentMethod').val(),
        payment_status: $('#expensePaymentStatus').val(),
        invoice_number: $('#expenseInvoiceNumber').val(),
        notes: $('#expenseNotes').val()
    };

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'POST',
        data: formData,
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Expense saved successfully');
                $('#expenseModal').modal('hide');
                
                // Wait for modal to close, then reload data
                setTimeout(function() {
                    loadExpenseRecords();
                }, 300);
            } else {
                toastr.error(response.message || 'Failed to save expense');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving expense:', error);
            toastr.error('An error occurred while saving expense');
        }
    });
}

function deleteExpense(id) {
    if (!confirm('Are you sure you want to delete this expense record?')) {
        return;
    }

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'POST',
        data: { action: 'delete_expense', id: id },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Expense deleted successfully');
                // Reload data immediately
                setTimeout(function() {
                    loadExpenseRecords();
                }, 200);
            } else {
                toastr.error(response.message || 'Failed to delete expense');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error deleting expense:', error);
            toastr.error('An error occurred while deleting expense');
        }
    });
}
</script>

<?php require_once 'inc/footer.php'; ?>
