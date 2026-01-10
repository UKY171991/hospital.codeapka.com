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
                    <h1><i class="fas fa-users mr-2"></i>Client Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="inventory_dashboard.php">Inventory</a></li>
                        <li class="breadcrumb-item active">Client</li>
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
                                Client List
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm d-none d-sm-inline-block" onclick="openClientModal()">
                                    <i class="fas fa-plus"></i> Add Client
                                </button>
                                <button type="button" class="btn btn-primary btn-sm d-sm-none" onclick="openClientModal()" title="Add Client">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="clientTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="all" style="width: 50px;">Sr. No.</th>
                                            <th class="min-tablet" style="min-width: 150px;">Name</th>
                                            <th class="min-tablet-p" style="min-width: 120px;">Phone</th>
                                            <th class="none" style="min-width: 100px;">Type</th>
                                            <th class="min-tablet-p" style="min-width: 80px;">Status</th>
                                            <th class="all" style="width: 200px;">Actions</th>
                                        </tr>
                                    </thead>
                                <tbody id="clientTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center">Loading...</td>
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

<!-- Client Modal -->
<div class="modal fade" id="clientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user mr-2"></i>
                    <span id="clientModalTitle">Add Client</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="clientForm">
                <div class="modal-body">
                    <input type="hidden" id="clientId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientName">Client Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="clientName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientType">Client Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="clientType" required>
                                    <option value="">Select Type</option>
                                    <option value="Individual">Individual</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Insurance">Insurance Company</option>
                                    <option value="Government">Government</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientEmail">Email</label>
                                <input type="email" class="form-control" id="clientEmail">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientPhone">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="clientPhone" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="clientAddress">Address</label>
                        <textarea class="form-control" id="clientAddress" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientCity">City</label>
                                <input type="text" class="form-control" id="clientCity">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientState">State</label>
                                <input type="text" class="form-control" id="clientState">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientPincode">Pincode</label>
                                <input type="text" class="form-control" id="clientPincode">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientGST">GST Number</label>
                                <input type="text" class="form-control" id="clientGST">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="clientStatus">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="clientStatus" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="clientNotes">Notes</label>
                        <textarea class="form-control" id="clientNotes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Client Details Modal -->
<div class="modal fade" id="clientDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 20px 30px;">
                <h5 class="modal-title text-white" style="font-weight: 600; font-size: 1.4rem;">
                    <i class="fas fa-user-circle mr-2"></i>
                    Client Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 1; text-shadow: none;">
                    <span style="font-size: 2rem;">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="clientDetailsBody" style="padding: 30px; background: #f8f9fa;">
                <div class="text-center" style="padding: 40px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i>
                    <p class="mt-3" style="color: #6c757d;">Loading client details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; padding: 20px 30px;">
                <h5 class="modal-title text-white" style="font-weight: 600;">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Transaction
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 1; text-shadow: none;">
                    <span style="font-size: 2rem;">&times;</span>
                </button>
            </div>
            <form id="editTransactionForm">
                <div class="modal-body" style="padding: 30px;">
                    <input type="hidden" id="editTransactionId">
                    <input type="hidden" id="editTransactionType">
                    <input type="hidden" id="editClientId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editDate">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="editDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editAmount">Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="editAmount" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editCategory">Category <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editCategory" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editDescription">Description</label>
                        <textarea class="form-control" id="editDescription" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editPaymentMethod">Payment Method <span class="text-danger">*</span></label>
                                <select class="form-control" id="editPaymentMethod" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editPaymentStatus">Payment Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="editPaymentStatus" required>
                                    <option value="Success">Completed</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Failed">Failed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editNotes">Notes</label>
                        <textarea class="form-control" id="editNotes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 15px 30px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.client-info-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}

.client-info-card h5 {
    color: #667eea;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.info-table {
    margin-bottom: 0;
}

.info-table tr {
    border-bottom: 1px solid #e9ecef;
}

.info-table tr:last-child {
    border-bottom: none;
}

.info-table th {
    font-weight: 600;
    color: #495057;
    padding: 12px 15px;
    width: 35%;
    background: #f8f9fa;
}

.info-table td {
    padding: 12px 15px;
    color: #6c757d;
}

.summary-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 20px;
    color: white;
    text-align: center;
    margin-bottom: 10px;
}

.summary-card h6 {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 5px;
}

.summary-card .value {
    font-size: 1.8rem;
    font-weight: 700;
}

.transaction-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.transaction-section h5 {
    color: #667eea;
    font-weight: 600;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #667eea;
}

.transaction-table {
    border-radius: 8px;
    overflow: hidden;
}

.transaction-table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    padding: 15px 12px;
    border: none;
}

.transaction-table tbody tr {
    transition: all 0.3s ease;
}

.transaction-table tbody tr:hover {
    background: #f8f9fa;
    transform: scale(1.01);
}

.transaction-table tbody td {
    padding: 12px;
    vertical-align: middle;
    border-color: #e9ecef;
}

.badge-pending {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
}

.badge-completed {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
}

.amount-cell {
    font-weight: 700;
    color: #667eea;
    font-size: 1.05rem;
}

.no-transactions {
    padding: 40px;
    text-align: center;
    color: #6c757d;
}

.no-transactions i {
    font-size: 3rem;
    color: #dee2e6;
    margin-bottom: 15px;
}

.transaction-table .btn-sm {
    padding: 4px 8px;
    font-size: 0.85rem;
    margin: 0 2px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.transaction-table .btn-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border: none;
    color: white;
}

.transaction-table .btn-warning:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(240, 147, 251, 0.4);
}

.transaction-table .btn-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    border: none;
}

.transaction-table .btn-danger:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(255, 107, 107, 0.4);
}
</style>

<script>
let clientTable;

$(document).ready(function() {
    loadClients();

    $('#clientForm').on('submit', function(e) {
        e.preventDefault();
        saveClient();
    });
    
    $('#editTransactionForm').on('submit', function(e) {
        e.preventDefault();
        saveTransaction();
    });
});

// Format date to DD-MM-YYYY
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

// Format amount with commas
function formatAmount(amount) {
    if (!amount) return '0.00';
    return parseFloat(amount).toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function loadClients() {
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { action: 'get_clients' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayClients(response.data);
            }
        }
    });
}

function displayClients(clients) {
    const tbody = $('#clientTableBody');
    tbody.empty();

    if (!clients || clients.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">No clients found</td></tr>');
        return;
    }

    // Sort clients by name in ascending order
    clients.sort((a, b) => {
        const nameA = (a.name || '').toLowerCase();
        const nameB = (b.name || '').toLowerCase();
        return nameA.localeCompare(nameB);
    });

    let srNo = 1;
    clients.forEach(function(client) {
        const statusClass = client.status === 'Active' ? 'badge-success' : 'badge-secondary';
        const row = `
            <tr>
                <td>${srNo++}</td>
                <td>${client.name}</td>
                <td>${client.phone}</td>
                <td><span class="badge badge-primary">${client.type}</span></td>
                <td><span class="badge ${statusClass}">${client.status}</span></td>
                <td>
                    <div class="btn-group-vertical btn-group-sm d-sm-none" role="group">
                        <button class="btn btn-success btn-sm" onclick="openWhatsAppChat('${client.phone}', '${client.name.replace(/'/g, "\\'")}')" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                        <button class="btn btn-info btn-sm" onclick="viewClientDetails(${client.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editClient(${client.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteClient(${client.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="btn-group btn-group-sm d-none d-sm-inline-flex" role="group">
                        <button class="btn btn-success btn-sm" onclick="openWhatsAppChat('${client.phone}', '${client.name.replace(/'/g, "\\'")}')" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                        <button class="btn btn-info btn-sm" onclick="viewClientDetails(${client.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editClient(${client.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteClient(${client.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Initialize DataTable
    if ($.fn.DataTable.isDataTable('#clientTable')) {
        $('#clientTable').DataTable().destroy();
    }
    clientTable = $('#clientTable').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRow,
                type: 'inline'
            }
        },
        order: [[1, 'asc']], // Sort by Name column (ascending)
        destroy: true,
        columnDefs: [
            { orderable: false, targets: [0, 5] }, // Disable sorting on Sr. No. and Actions
            { className: 'text-center', targets: [0, 5] }, // Center align Sr. No. and Actions
            { responsivePriority: 1, targets: 0 }, // Always show Sr. No.
            { responsivePriority: 2, targets: 5 }, // Always show Actions
            { responsivePriority: 3, targets: 1 }, // Show Name on tablet and up
            { responsivePriority: 4, targets: 3 }, // Show Phone on tablet and up
            { responsivePriority: 5, targets: 4 }, // Show Status on tablet and up
            { responsivePriority: 6, targets: 2 }  // Type is lowest priority
        ]
    });
}

function openClientModal() {
    $('#clientId').val('');
    $('#clientForm')[0].reset();
    $('#clientStatus').val('Active');
    $('#clientModalTitle').text('Add Client');
    $('#clientModal').modal('show');
}

function editClient(id) {
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { action: 'get_client', id: id },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                $('#clientId').val(data.id);
                $('#clientName').val(data.name);
                $('#clientType').val(data.type);
                $('#clientEmail').val(data.email);
                $('#clientPhone').val(data.phone);
                $('#clientAddress').val(data.address);
                $('#clientCity').val(data.city);
                $('#clientState').val(data.state);
                $('#clientPincode').val(data.pincode);
                $('#clientGST').val(data.gst_number);
                $('#clientStatus').val(data.status);
                $('#clientNotes').val(data.notes);
                $('#clientModalTitle').text('Edit Client');
                $('#clientModal').modal('show');
            }
        }
    });
}

function viewClientDetails(id) {
    // Store client ID for later use
    $('#editClientId').val(id);
    
    $('#clientDetailsBody').html('<div class="text-center" style="padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #667eea;"></i><p class="mt-3" style="color: #6c757d;">Loading client details...</p></div>');
    $('#clientDetailsModal').modal('show');

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { action: 'get_client_details', id: id },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const client = response.data.client;
                let transactions = response.data.transactions || [];
                
                // Sort transactions: pending first, then completed
                transactions.sort((a, b) => {
                    const statusOrder = { 'Pending': 0, 'Completed': 1 };
                    const statusA = statusOrder[a.status] !== undefined ? statusOrder[a.status] : 2;
                    const statusB = statusOrder[b.status] !== undefined ? statusOrder[b.status] : 2;
                    
                    if (statusA !== statusB) {
                        return statusA - statusB;
                    }
                    // If same status, sort by date (newest first)
                    return new Date(b.date) - new Date(a.date);
                });
                
                // Count pending and completed
                const pendingCount = transactions.filter(t => t.status === 'Pending').length;
                const completedCount = transactions.filter(t => t.status === 'Completed').length;
                
                let html = `
                    <div class="row">
                        <div class="col-md-7">
                            <div class="client-info-card">
                                <h5><i class="fas fa-user mr-2"></i>Client Information</h5>
                                <table class="info-table table table-borderless">
                                    <tr><th><i class="fas fa-id-card mr-2"></i>Name:</th><td>${client.name}</td></tr>
                                    <tr><th><i class="fas fa-tag mr-2"></i>Type:</th><td><span class="badge badge-primary">${client.type}</span></td></tr>
                                    <tr><th><i class="fas fa-envelope mr-2"></i>Email:</th><td>${client.email || '-'}</td></tr>
                                    <tr><th><i class="fas fa-phone mr-2"></i>Phone:</th><td>${client.phone}</td></tr>
                                    <tr><th><i class="fas fa-map-marker-alt mr-2"></i>Address:</th><td>${client.address || '-'}</td></tr>
                                    <tr><th><i class="fas fa-city mr-2"></i>City:</th><td>${client.city || '-'}</td></tr>
                                    <tr><th><i class="fas fa-map mr-2"></i>State:</th><td>${client.state || '-'}</td></tr>
                                    <tr><th><i class="fas fa-file-invoice mr-2"></i>GST:</th><td>${client.gst_number || '-'}</td></tr>
                                    <tr><th><i class="fas fa-toggle-on mr-2"></i>Status:</th><td><span class="badge badge-${client.status === 'Active' ? 'success' : 'secondary'}">${client.status}</span></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="summary-card">
                                <h6><i class="fas fa-list-alt mr-2"></i>Total Transactions</h6>
                                <div class="value">${transactions.length}</div>
                            </div>
                            <div class="summary-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <h6><i class="fas fa-clock mr-2"></i>Pending</h6>
                                <div class="value">${pendingCount}</div>
                            </div>
                            <div class="summary-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <h6><i class="fas fa-check-circle mr-2"></i>Completed</h6>
                                <div class="value">${completedCount}</div>
                            </div>
                            <div class="summary-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <h6><i class="fas fa-rupee-sign mr-2"></i>Total Amount</h6>
                                <div class="value">₹${formatAmount(response.data.total_amount)}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="transaction-section">
                                <h5><i class="fas fa-exchange-alt mr-2"></i>All Transactions</h5>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="transaction-table table table-hover">
                                        <thead style="position: sticky; top: 0; z-index: 10;">
                                            <tr>
                                                <th style="width: 60px;">Sr.</th>
                                                <th style="width: 120px;">Date</th>
                                                <th style="width: 100px;">Type</th>
                                                <th>Description</th>
                                                <th style="width: 130px;">Amount</th>
                                                <th style="width: 120px;">Status</th>
                                                <th style="width: 120px;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                `;

                if (transactions.length > 0) {
                    let srNo = 1;
                    transactions.forEach(function(trans) {
                        const statusBadge = trans.status === 'Pending' 
                            ? '<span class="badge-pending"><i class="fas fa-clock mr-1"></i>Pending</span>' 
                            : '<span class="badge-completed"><i class="fas fa-check-circle mr-1"></i>Completed</span>';
                        
                        const typeIcon = trans.type === 'income' ? '<i class="fas fa-arrow-down text-success mr-1"></i>' : '<i class="fas fa-arrow-up text-danger mr-1"></i>';
                        
                        html += `
                            <tr>
                                <td class="text-center"><strong>${srNo++}</strong></td>
                                <td>${formatDate(trans.date)}</td>
                                <td>${typeIcon}${trans.type}</td>
                                <td>${trans.description || '-'}</td>
                                <td class="amount-cell">₹${formatAmount(trans.amount)}</td>
                                <td class="text-center">${statusBadge}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning" onclick="editTransaction(${trans.id}, '${trans.type}')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteTransaction(${trans.id}, '${trans.type}', ${client.id})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="7" class="no-transactions">
                                <i class="fas fa-inbox"></i>
                                <p class="mb-0">No transactions found</p>
                            </td>
                        </tr>
                    `;
                }

                html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#clientDetailsBody').html(html);
            }
        }
    });
}

function saveClient() {
    const formData = {
        action: $('#clientId').val() ? 'update_client' : 'add_client',
        id: $('#clientId').val(),
        name: $('#clientName').val(),
        type: $('#clientType').val(),
        email: $('#clientEmail').val(),
        phone: $('#clientPhone').val(),
        address: $('#clientAddress').val(),
        city: $('#clientCity').val(),
        state: $('#clientState').val(),
        pincode: $('#clientPincode').val(),
        gst_number: $('#clientGST').val(),
        status: $('#clientStatus').val(),
        notes: $('#clientNotes').val()
    };

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Client saved successfully');
                $('#clientModal').modal('hide');
                loadClients();
            } else {
                toastr.error(response.message || 'Failed to save client');
            }
        },
        error: function() {
            toastr.error('An error occurred while saving client');
        }
    });
}

function deleteClient(id) {
    if (!confirm('Are you sure you want to delete this client?')) {
        return;
    }

    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'POST',
        data: { action: 'delete_client', id: id },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Client deleted successfully');
                loadClients();
            } else {
                toastr.error(response.message || 'Failed to delete client');
            }
        }
    });
}

function openWhatsAppChat(phone, clientName) {
    if (!phone || phone === '-') {
        toastr.error('No phone number available for this client');
        return;
    }
    
    // Remove all non-numeric characters except +
    let cleanPhone = phone.replace(/[^\d+]/g, '');
    
    // Remove leading zeros and add country code if not present
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '91' + cleanPhone.substring(1); // Assuming India (+91)
    } else if (!cleanPhone.startsWith('+') && !cleanPhone.startsWith('91')) {
        cleanPhone = '91' + cleanPhone;
    }
    
    // Remove + if present
    cleanPhone = cleanPhone.replace('+', '');
    
    // Optional: Add a greeting message
    const greeting = `Hello ${clientName}, `;
    const encodedMessage = encodeURIComponent(greeting);
    
    // Open WhatsApp with greeting message
    const whatsappUrl = `https://wa.me/${cleanPhone}?text=${encodedMessage}`;
    window.open(whatsappUrl, '_blank');
}

function editTransaction(id, type) {
    // Store the transaction details for later use
    $('#editTransactionId').val(id);
    $('#editTransactionType').val(type);
    
    // Get transaction details
    const action = type === 'income' ? 'get_income' : 'get_expense';
    
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'GET',
        data: { action: action, id: id },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                
                // Populate the form
                $('#editDate').val(data.date);
                $('#editAmount').val(data.amount);
                $('#editCategory').val(data.category);
                $('#editDescription').val(data.description || '');
                $('#editPaymentMethod').val(data.payment_method || 'Cash');
                $('#editPaymentStatus').val(data.payment_status || 'Success');
                $('#editNotes').val(data.notes || '');
                
                // Show the edit modal
                $('#editTransactionModal').modal('show');
            } else {
                toastr.error('Failed to load transaction details');
            }
        },
        error: function() {
            toastr.error('An error occurred while loading transaction details');
        }
    });
}

function saveTransaction() {
    const id = $('#editTransactionId').val();
    const type = $('#editTransactionType').val();
    const action = type === 'income' ? 'update_income' : 'update_expense';
    
    const formData = {
        action: action,
        id: id,
        date: $('#editDate').val(),
        amount: $('#editAmount').val(),
        category: $('#editCategory').val(),
        description: $('#editDescription').val(),
        payment_method: $('#editPaymentMethod').val(),
        payment_status: $('#editPaymentStatus').val(),
        notes: $('#editNotes').val()
    };
    
    // Add client_id for income transactions
    if (type === 'income') {
        formData.client_id = $('#editClientId').val();
    } else {
        formData.vendor = ''; // For expense transactions
    }
    
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Transaction updated successfully');
                $('#editTransactionModal').modal('hide');
                
                // Reload the client details to show updated data
                const clientId = $('#editClientId').val();
                if (clientId) {
                    viewClientDetails(clientId);
                }
            } else {
                toastr.error(response.message || 'Failed to update transaction');
            }
        },
        error: function() {
            toastr.error('An error occurred while updating transaction');
        }
    });
}

function deleteTransaction(id, type, clientId) {
    if (!confirm('Are you sure you want to delete this transaction?')) {
        return;
    }
    
    const action = type === 'income' ? 'delete_income' : 'delete_expense';
    
    $.ajax({
        url: 'ajax/inventory_api.php',
        type: 'POST',
        data: { action: action, id: id },
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Transaction deleted successfully');
                // Reload the client details
                viewClientDetails(clientId);
            } else {
                toastr.error(response.message || 'Failed to delete transaction');
            }
        },
        error: function() {
            toastr.error('An error occurred while deleting transaction');
        }
    });
}
</script>

<?php require_once 'inc/footer.php'; ?>
