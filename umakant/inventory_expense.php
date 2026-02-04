<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper inventory-section">
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
                        <div class="col-md-3 col-sm-6">
                            <label for="filterYear">Year</label>
                            <select id="filterYear" class="form-control">
                                <option value="">All Years</option>
                                <?php
                                $startYear = 2020;
                                $currentYear = date('Y');
                                for ($y = $currentYear; $y >= $startYear; $y--) {
                                    $selected = ($y == $currentYear) ? 'selected' : '';
                                    echo "<option value='$y' $selected>$y</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label for="filterMonth">Month</label>
                            <select id="filterMonth" class="form-control">
                                <option value="">All Months</option>
                                <?php
                                $currentMonth = date('n');
                                for ($m = 1; $m <= 12; $m++) {
                                    $monthName = date('F', mktime(0, 0, 0, $m, 1));
                                    $selected = ($m == $currentMonth) ? 'selected' : '';
                                    echo "<option value='$m' $selected>$monthName</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <button type="button" id="applyFilters" class="btn btn-primary btn-block">
                                <i class="fas fa-filter mr-1"></i> <span class="d-none d-sm-inline">Apply</span>
                            </button>
                        </div>
                        <div class="col-md-2 col-sm-6">
                            <button type="button" id="resetFilters" class="btn btn-secondary btn-block">
                                <i class="fas fa-redo mr-1"></i> <span class="d-none d-sm-inline">Reset</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="info-box bg-white">
                        <span class="info-box-icon bg-danger"><i class="fas fa-rupee-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Expense</span>
                            <span class="info-box-number" id="expenseTotalAmount">₹0.00</span>
                            <small class="text-muted" id="expenseRecordCount">0 records</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="info-box bg-white">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending Amount</span>
                            <span class="info-box-number" id="expensePendingAmount">₹0.00</span>
                            <small class="text-muted" id="expensePendingCount">0 pending</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="info-box bg-info">
                        <span class="info-box-icon bg-info"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Successful</span>
                            <span class="info-box-number" id="expenseSuccessCount">0</span>
                            <small class="text-muted">Completed payments</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="info-box bg-white">
                        <span class="info-box-icon bg-secondary"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Failed</span>
                            <span class="info-box-number" id="expenseFailedCount">0</span>
                            <small class="text-muted">Failed payments</small>
                        </div>
                    </div>
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
                                <button type="button" class="btn btn-danger btn-sm d-none d-sm-inline-block" onclick="openExpenseModal()">
                                    <i class="fas fa-plus"></i> Add Expense
                                </button>
                                <button type="button" class="btn btn-danger btn-sm d-sm-none" onclick="openExpenseModal()" title="Add Expense">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="expenseTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="all" style="width: 50px;">Sr. No.</th>
                                            <th class="min-tablet" style="min-width: 100px;">Date</th>
                                            <th class="min-tablet-p" style="min-width: 120px;">Category</th>
                                            <th class="none" style="min-width: 150px;">Description</th>
                                            <th class="none" style="min-width: 120px;">Vendor</th>
                                            <th class="min-tablet-p" style="min-width: 100px;">Amount</th>
                                            <th class="none" style="min-width: 120px;">Payment Method</th>
                                            <th class="min-tablet-p" style="min-width: 80px;">Status</th>
                                            <th class="all" style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                <tbody id="expenseTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>Loading expense records...
                                        </td>
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
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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

    $('#resetFilters').click(function() {
        $('#filterYear').val('');
        $('#filterMonth').val('');
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
    $('#expenseTableBody').html('<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin mr-2"></i>Loading expense records...</td></tr>');

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
            } else {
                displayExpenseRecords([]);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading expenses:', error);
            toastr.error('Failed to load expense records');
            displayExpenseRecords([]);
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
        tbody.append('<tr><td colspan="9" class="text-center text-muted">No expense records found</td></tr>');
        updateExpenseStats([]);
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
            '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Success</span>' : 
            paymentStatus === 'Pending' ? 
            '<span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>Pending</span>' : 
            '<span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i>Failed</span>';
        
        const row = `
            <tr>
                <td>${srNo++}</td>
                <td>${record.date}</td>
                <td>${record.category}</td>
                <td>${record.description}</td>
                <td>${record.vendor || '-'}</td>
                <td class="text-right font-weight-bold text-danger">${formatCurrency(record.amount)}</td>
                <td>${record.payment_method}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-group-vertical btn-group-sm d-sm-none" role="group">
                        <button class="btn btn-info btn-sm" onclick="editExpense(${record.id})" title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteExpense(${record.id})" title="Delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                    <div class="btn-group btn-group-sm d-none d-sm-inline-flex" role="group">
                        <button class="btn btn-info btn-sm" onclick="editExpense(${record.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteExpense(${record.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    updateExpenseStats(records);

    // Initialize DataTable with fresh data
    expenseTable = $('#expenseTable').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRow,
                type: 'inline',
                renderer: function (api, rowIdx, columns) {
                    const data = $.map(columns, function (col, i) {
                        return col.hidden ?
                            '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                            '<td><strong>' + col.title + ':</strong></td> ' +
                            '<td>' + col.data + '</td>' +
                            '</tr>' :
                            '';
                    }).join('');
                    return data ?
                        $('<table/>').append(data) :
                        false;
                }
            }
        },
        order: [[1, 'desc']], // Sort by Date column (descending)
        destroy: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        columnDefs: [
            { orderable: false, targets: [0, 8] }, // Disable sorting on Sr. No. and Actions
            { className: 'text-center', targets: [0, 8] }, // Center align Sr. No. and Actions
            { responsivePriority: 1, targets: 0 }, // Always show Sr. No.
            { responsivePriority: 2, targets: 8 }, // Always show Actions
            { responsivePriority: 3, targets: 1 }, // Show Date on tablet and up
            { responsivePriority: 4, targets: 5 }, // Show Amount on tablet and up
            { responsivePriority: 5, targets: 2 }, // Show Category on tablet and up
            { responsivePriority: 6, targets: 7 }, // Show Status on tablet and up
            { responsivePriority: 7, targets: 3 }, // Description is lower priority
            { responsivePriority: 8, targets: 4 }, // Vendor is lowest priority
            { responsivePriority: 9, targets: 6 }  // Payment Method is lowest priority
        ],
        language: {
            search: "Search expenses:",
            lengthMenu: "Show _MENU_ expenses",
            info: "Showing _START_ to _END_ of _TOTAL_ expenses",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
}

function updateExpenseStats(records) {
    let totalAmount = 0;
    let pendingAmount = 0;
    let pendingCount = 0;
    let successCount = 0;
    let failedCount = 0;

    records.forEach(function(record) {
        const amount = parseFloat(record.amount || 0);
        totalAmount += amount;
        const status = record.payment_status || 'Success';
        if (status === 'Pending') {
            pendingAmount += amount;
            pendingCount += 1;
        } else if (status === 'Failed') {
            failedCount += 1;
        } else {
            successCount += 1;
        }
    });

    $('#expenseTotalAmount').text(formatCurrency(totalAmount));
    $('#expensePendingAmount').text(formatCurrency(pendingAmount));
    $('#expensePendingCount').text(pendingCount + ' pending');
    $('#expenseSuccessCount').text(successCount);
    $('#expenseFailedCount').text(failedCount);
    $('#expenseRecordCount').text(records.length + ' records');
}

function formatCurrency(amount) {
    const value = parseFloat(amount || 0);
    return '₹' + value.toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
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

<style>
/* Responsive Design Improvements for Expense Page */

/* Mobile Responsive Styles */
@media (max-width: 576px) {
    /* Modal adjustments for mobile */
    .modal-dialog {
        margin: 10px;
        max-width: calc(100% - 20px);
    }
    
    .modal-body {
        padding: 15px;
    }
    
    .modal-header {
        padding: 15px;
    }
    
    .modal-footer {
        padding: 10px 15px;
    }
    
    /* Form adjustments for mobile */
    .modal-body .row > div {
        margin-bottom: 15px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    /* Filter form mobile adjustments */
    .card-body {
        padding: 15px;
    }
    
    /* Table adjustments for mobile */
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 2px;
        text-align: left;
    }
    
    /* Card adjustments */
    .card {
        margin-bottom: 15px;
    }
    
    .card-body {
        padding: 15px;
    }
    
    /* Filter form improvements */
    #expenseFilterForm .col-sm-6 {
        margin-bottom: 10px;
    }
    
    /* Button improvements for touch */
    .btn {
        min-height: 44px;
        font-size: 0.9rem;
    }
    
    .btn-sm {
        min-height: 38px;
        font-size: 0.8rem;
    }
}

@media (max-width: 768px) {
    /* Tablet adjustments */
    .modal-dialog.modal-lg {
        max-width: 95%;
        margin: 15px auto;
    }
    
    .card-body {
        padding: 20px;
    }
    
    /* Filter form adjustments */
    #expenseFilterForm .col-sm-6 {
        margin-bottom: 15px;
    }
}

@media (max-width: 992px) {
    /* Small desktop adjustments */
    .modal-dialog.modal-lg {
        max-width: 90%;
    }
}

/* DataTables responsive improvements */
@media (max-width: 768px) {
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        text-align: center;
        margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_info {
        text-align: center;
        margin-top: 10px;
    }
    
    .dataTables_wrapper .dataTables_paginate {
        text-align: center;
        margin-top: 10px;
    }
    
    .dataTables_wrapper .dataTables_length select {
        width: auto;
    }
}

/* Button hover effects */
.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Form input focus effects */
.form-control:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Badge improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.375em 0.75em;
}

/* Card improvements */
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

/* Filter card improvements */
#expenseFilterForm .form-group {
    margin-bottom: 1rem;
}

/* Table improvements */
.table {
    margin-bottom: 0;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
    border-top: none;
}

/* Amount column styling */
.text-right {
    text-align: right;
}

/* Status badge improvements */
.badge-success {
    background-color: #28a745;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-danger {
    background-color: #dc3545;
}

/* Responsive table cell improvements */
@media (max-width: 576px) {
    .table td, .table th {
        padding: 0.5rem;
        vertical-align: middle;
    }
    
    .table-responsive {
        border-radius: 0.25rem;
    }
}

/* Form validation improvements */
.was-validated .form-control:valid {
    border-color: #28a745;
}

.was-validated .form-control:invalid {
    border-color: #dc3545;
}

/* Loading state improvements */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Action button improvements */
.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

/* Mobile-specific action buttons */
@media (max-width: 576px) {
    .btn-group-vertical .btn {
        border-radius: 0.25rem;
        margin-bottom: 5px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
}
</style>

<?php require_once 'inc/footer.php'; ?>
