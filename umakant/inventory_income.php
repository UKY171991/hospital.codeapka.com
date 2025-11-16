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
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                Income Records
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success btn-sm" onclick="openIncomeModal()">
                                    <i class="fas fa-plus"></i> Add Income
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="incomeTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Client</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="incomeTableBody">
                                    <tr>
                                        <td colspan="8" class="text-center">Loading...</td>
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
    <div class="modal-dialog modal-lg" role="document">
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
                                    <option value="Consultation">Consultation</option>
                                    <option value="Lab Tests">Lab Tests</option>
                                    <option value="Pharmacy">Pharmacy</option>
                                    <option value="Surgery">Surgery</option>
                                    <option value="Room Charges">Room Charges</option>
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
                        <label for="incomeDescription">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="incomeDescription" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="incomeAmount">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="incomeAmount" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
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
        data: { action: 'get_clients' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const select = $('#incomeClient');
                select.empty().append('<option value="">Select Client</option>');
                response.data.forEach(function(client) {
                    select.append(`<option value="${client.id}">${client.name}</option>`);
                });
            }
        }
    });
}

function loadIncomeRecords() {
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { action: 'get_income_records' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayIncomeRecords(response.data);
            }
        }
    });
}

function displayIncomeRecords(records) {
    const tbody = $('#incomeTableBody');
    tbody.empty();

    if (!records || records.length === 0) {
        tbody.append('<tr><td colspan="8" class="text-center">No income records found</td></tr>');
        return;
    }

    records.forEach(function(record) {
        const row = `
            <tr>
                <td>${record.id}</td>
                <td>${record.date}</td>
                <td>${record.category}</td>
                <td>${record.description}</td>
                <td>${record.client_name || '-'}</td>
                <td>₹${parseFloat(record.amount).toFixed(2)}</td>
                <td>${record.payment_method}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editIncome(${record.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteIncome(${record.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Initialize DataTable
    if ($.fn.DataTable.isDataTable('#incomeTable')) {
        $('#incomeTable').DataTable().destroy();
    }
    incomeTable = $('#incomeTable').DataTable({
        responsive: true,
        order: [[0, 'desc']]
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
                $('#incomeNotes').val(data.notes);
                $('#incomeModalTitle').text('Edit Income');
                $('#incomeModal').modal('show');
            }
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
        notes: $('#incomeNotes').val()
    };

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Income saved successfully');
                $('#incomeModal').modal('hide');
                loadIncomeRecords();
            } else {
                toastr.error(response.message || 'Failed to save income');
            }
        },
        error: function() {
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
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Income deleted successfully');
                loadIncomeRecords();
            } else {
                toastr.error(response.message || 'Failed to delete income');
            }
        }
    });
}
</script>

<?php require_once 'inc/footer.php'; ?>
