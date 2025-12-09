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
                                <button type="button" class="btn btn-primary btn-sm" onclick="openClientModal()">
                                    <i class="fas fa-plus"></i> Add Client
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="clientTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="clientTableBody">
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

<!-- Client Modal -->
<div class="modal fade" id="clientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle mr-2"></i>
                    Client Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="clientDetailsBody">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> Loading...
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let clientTable;

$(document).ready(function() {
    loadClients();

    $('#clientForm').on('submit', function(e) {
        e.preventDefault();
        saveClient();
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
                displayClients(response.data);
            }
        }
    });
}

function displayClients(clients) {
    const tbody = $('#clientTableBody');
    tbody.empty();

    if (!clients || clients.length === 0) {
        tbody.append('<tr><td colspan="8" class="text-center">No clients found</td></tr>');
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
                <td>${client.email || '-'}</td>
                <td>${client.phone}</td>
                <td>${client.address || '-'}</td>
                <td>${client.type}</td>
                <td><span class="badge ${statusClass}">${client.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="openWhatsAppChat('${client.phone}', '${client.name.replace(/'/g, "\\'")}')">
                        <i class="fab fa-whatsapp"></i>
                    </button>
                    <button class="btn btn-sm btn-info" onclick="viewClientDetails(${client.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="editClient(${client.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteClient(${client.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
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
        responsive: true,
        order: [[1, 'asc']], // Sort by Name column (ascending)
        columnDefs: [
            { orderable: false, targets: [0, 7] } // Disable sorting on Sr. No. and Actions
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
    $('#clientDetailsBody').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
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
                
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Client Information</h5>
                            <table class="table table-sm">
                                <tr><th>Name:</th><td>${client.name}</td></tr>
                                <tr><th>Type:</th><td>${client.type}</td></tr>
                                <tr><th>Email:</th><td>${client.email || '-'}</td></tr>
                                <tr><th>Phone:</th><td>${client.phone}</td></tr>
                                <tr><th>Address:</th><td>${client.address || '-'}</td></tr>
                                <tr><th>City:</th><td>${client.city || '-'}</td></tr>
                                <tr><th>State:</th><td>${client.state || '-'}</td></tr>
                                <tr><th>GST:</th><td>${client.gst_number || '-'}</td></tr>
                                <tr><th>Status:</th><td><span class="badge badge-${client.status === 'Active' ? 'success' : 'secondary'}">${client.status}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Transaction Summary</h5>
                            <table class="table table-sm">
                                <tr><th>Total Transactions:</th><td>${transactions.length}</td></tr>
                                <tr><th>Total Amount:</th><td>₹${response.data.total_amount || 0}</td></tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5>All Transactions</h5>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm table-bordered table-hover">
                                    <thead class="thead-light" style="position: sticky; top: 0; background: #f8f9fa; z-index: 1;">
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                `;

                if (transactions.length > 0) {
                    let srNo = 1;
                    transactions.forEach(function(trans) {
                        const statusBadge = trans.status === 'Pending' 
                            ? '<span class="badge badge-warning">Pending</span>' 
                            : '<span class="badge badge-success">Completed</span>';
                        
                        html += `
                            <tr>
                                <td>${srNo++}</td>
                                <td>${trans.date}</td>
                                <td>${trans.type}</td>
                                <td>${trans.description || '-'}</td>
                                <td><strong>₹${trans.amount}</strong></td>
                                <td>${statusBadge}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += '<tr><td colspan="6" class="text-center text-muted">No transactions found</td></tr>';
                }

                html += `
                                    </tbody>
                                </table>
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
</script>

<?php require_once 'inc/footer.php'; ?>
