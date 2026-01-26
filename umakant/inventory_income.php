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
                    <h1><i class="fas fa-arrow-up mr-2"></i>Income Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="inventory_dashboard.php">Inventory</a></li>
                        <li class="breadcrumb-item active">Income</li>
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
                    <form id="incomeFilterForm" class="row align-items-end">
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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                Income Records
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success btn-sm d-none d-sm-inline-block" onclick="openIncomeModal()">
                                    <i class="fas fa-plus"></i> Add Income
                                </button>
                                <button type="button" class="btn btn-success btn-sm d-sm-none" onclick="openIncomeModal()" title="Add Income">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="incomeTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="all" style="width: 50px;">Sr. No.</th>
                                            <th class="min-tablet" style="min-width: 100px;">Date</th>
                                            <th class="min-tablet-p" style="min-width: 120px;">Category</th>
                                            <th class="none" style="min-width: 150px;">Description</th>
                                            <th class="none" style="min-width: 120px;">Client</th>
                                            <th class="min-tablet-p" style="min-width: 100px;">Amount</th>
                                            <th class="none" style="min-width: 120px;">Payment Method</th>
                                            <th class="min-tablet-p" style="min-width: 80px;">Status</th>
                                            <th class="all" style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                <tbody id="incomeTableBody">
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

<!-- Income Modal -->
<div class="modal fade" id="incomeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-arrow-up mr-2"></i>
                    <span id="incomeModalTitle">Add Income</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="incomeForm">
                <div class="modal-body">
                    <input type="hidden" id="incomeId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incomeDate">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="incomeDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incomeCategory">Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="incomeCategory" required>
                                    <option value="">Select Category</option>
                                    <option value="Web Development">Web Development</option>
                                    <option value="App Development">App Development</option>
                                    <option value="Software Development">Software Development</option>
                                    <option value="UI/UX Design">UI/UX Design</option>
                                    <option value="Maintenance & Support">Maintenance & Support</option>
                                    <option value="Digital Marketing">Digital Marketing</option>
                                    <option value="SEO Services">SEO Services</option>
                                    <option value="Domain & Hosting">Domain & Hosting</option>
                                    <option value="Consultation">Consultation</option>
                                    <option value="Other Services">Other Services</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="incomeClient">Client</label>
                        <select class="form-control select2" id="incomeClient">
                            <option value="">Select Client</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="incomeDescription">Description</label>
                        <textarea class="form-control" id="incomeDescription" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="incomeAmount">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="incomeAmount" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="incomePaymentMethod">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-control" id="incomePaymentMethod" required>
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
                                <label for="incomePaymentStatus">Payment Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="incomePaymentStatus" required>
                                    <option value="Success">Success</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Failed">Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="incomeNotes">Notes</label>
                        <textarea class="form-control" id="incomeNotes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i> Save Income
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let incomeTable;

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#incomeModal')
    });

    // Set today's date as default
    $('#incomeDate').val(new Date().toISOString().split('T')[0]);

    // Load clients
    loadClients();

    // Load income records
    loadIncomeRecords();

    $('#applyFilters').click(function() {
        loadIncomeRecords();
    });

    $('#resetFilters').click(function() {
        $('#filterYear').val('');
        $('#filterMonth').val('');
        loadIncomeRecords();
    });

    // Form submit
    $('#incomeForm').on('submit', function(e) {
        e.preventDefault();
        saveIncome();
    });
});

function loadClients() {
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { 
            action: 'get_clients',
            _: new Date().getTime()
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const select = $('#incomeClient');
                select.empty().append('<option value="">Select Client</option>');
                response.data.forEach(function(client) {
                    select.append(`<option value="${client.id}">${client.name}</option>`);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading clients:', error);
        }
    });
}

function loadIncomeRecords() {
    const year = $('#filterYear').val();
    const month = $('#filterMonth').val();

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { 
            action: 'get_income_records',
            year: year,
            month: month,
            _: new Date().getTime() // Cache buster
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayIncomeRecords(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading income records:', error);
            toastr.error('Failed to load income records');
        }
    });
}

function displayIncomeRecords(records) {
    // Destroy existing DataTable first
    if ($.fn.DataTable.isDataTable('#incomeTable')) {
        $('#incomeTable').DataTable().destroy();
    }
    
    const tbody = $('#incomeTableBody');
    tbody.empty();

    if (!records || records.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center">No income records found</td></tr>');
        return;
    }

    // Sort records by status first (Pending before Success), then by date (newest first)
    const statusOrder = {
        'Pending': 1,
        'Failed': 2,
        'Success': 3
    };
    
    records.sort((a, b) => {
        // First sort by status (Pending first, then Failed, then Success)
        const statusA = statusOrder[a.payment_status || 'Success'] || 3;
        const statusB = statusOrder[b.payment_status || 'Success'] || 3;
        const statusDiff = statusA - statusB;
        
        // If status is the same, sort by date (descending - newest first)
        if (statusDiff === 0) {
            const dateA = new Date(a.date);
            const dateB = new Date(b.date);
            return dateB - dateA;
        }
        
        return statusDiff;
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
                <td>${record.client_name || '-'}</td>
                <td>₹${parseFloat(record.amount).toFixed(2)}</td>
                <td>${record.payment_method}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-group-vertical btn-group-sm d-sm-none" role="group">
                        <button class="btn btn-info btn-sm" onclick="editIncome(${record.id})" title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteIncome(${record.id})" title="Delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                    <div class="btn-group btn-group-sm d-none d-sm-inline-flex" role="group">
                        <button class="btn btn-info btn-sm" onclick="editIncome(${record.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteIncome(${record.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Initialize DataTable with fresh data
    incomeTable = $('#incomeTable').DataTable({
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
        order: [[7, 'asc'], [1, 'desc']], // Sort by Status (asc - Pending first) then Date (desc)
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
            { responsivePriority: 8, targets: 4 }, // Client is lower priority
            { responsivePriority: 9, targets: 6 }  // Payment Method is lowest priority
        ],
        language: {
            search: "Search income:",
            lengthMenu: "Show _MENU_ income records",
            info: "Showing _START_ to _END_ of _TOTAL_ income records",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });
}

function openIncomeModal() {
    $('#incomeId').val('');
    $('#incomeForm')[0].reset();
    $('#incomeDate').val(new Date().toISOString().split('T')[0]);
    $('#incomeModalTitle').text('Add Income');
    $('#incomeModal').modal('show');
}

function editIncome(id) {
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { action: 'get_income', id: id },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                $('#incomeId').val(data.id);
                $('#incomeDate').val(data.date);
                $('#incomeCategory').val(data.category);
                $('#incomeClient').val(data.client_id).trigger('change');
                $('#incomeDescription').val(data.description);
                $('#incomeAmount').val(data.amount);
                $('#incomePaymentMethod').val(data.payment_method);
                $('#incomePaymentStatus').val(data.payment_status || 'Success');
                $('#incomeNotes').val(data.notes);
                $('#incomeModalTitle').text('Edit Income');
                $('#incomeModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading income:', error);
            toastr.error('Failed to load income record');
        }
    });
}

function saveIncome() {
    const formData = {
        action: $('#incomeId').val() ? 'update_income' : 'add_income',
        id: $('#incomeId').val(),
        date: $('#incomeDate').val(),
        category: $('#incomeCategory').val(),
        client_id: $('#incomeClient').val(),
        description: $('#incomeDescription').val(),
        amount: $('#incomeAmount').val(),
        payment_method: $('#incomePaymentMethod').val(),
        payment_status: $('#incomePaymentStatus').val(),
        notes: $('#incomeNotes').val()
    };

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'POST',
        data: formData,
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Income saved successfully');
                $('#incomeModal').modal('hide');
                
                // Wait for modal to close, then reload data
                setTimeout(function() {
                    loadIncomeRecords();
                }, 300);
            } else {
                toastr.error(response.message || 'Failed to save income');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving income:', error);
            toastr.error('An error occurred while saving income');
        }
    });
}

function deleteIncome(id) {
    if (!confirm('Are you sure you want to delete this income record?')) {
        return;
    }

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'POST',
        data: { action: 'delete_income', id: id },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Income deleted successfully');
                // Reload data immediately
                setTimeout(function() {
                    loadIncomeRecords();
                }, 200);
            } else {
                toastr.error(response.message || 'Failed to delete income');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error deleting income:', error);
            toastr.error('An error occurred while deleting income');
        }
    });
}
</script>

<style>
/* Responsive Design Improvements for Income Page */

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
    #incomeFilterForm .col-sm-6 {
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
    
    /* Select2 mobile adjustments */
    .select2-container--bootstrap4 .select2-selection {
        min-height: 38px;
    }
    
    .select2-container--bootstrap4 .select2-selection__rendered {
        line-height: 34px;
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
    #incomeFilterForm .col-sm-6 {
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
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
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
#incomeFilterForm .form-group {
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

/* Select2 responsive improvements */
@media (max-width: 768px) {
    .select2-container--bootstrap4 {
        width: 100% !important;
    }
    
    .select2-dropdown {
        font-size: 0.875rem;
    }
}

/* Income amount styling */
.amount-positive {
    color: #28a745;
    font-weight: 600;
}

/* Responsive form layouts */
@media (max-width: 576px) {
    .modal-body .row {
        margin: 0;
    }
    
    .modal-body .col-md-6,
    .modal-body .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
        padding: 0 0 15px 0;
    }
}

/* Enhanced focus states */
.form-control:focus,
select.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

/* Success theme consistency */
.bg-success {
    background-color: #28a745 !important;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}
</style>

<?php require_once 'inc/footer.php'; ?>
