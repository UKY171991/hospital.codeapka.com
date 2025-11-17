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
                        <li class="breadcrumb-item"><a href="client_dashboard.php">Client Dashboard</a></li>
                        <li class="breadcrumb-item active">Clients</li>
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
                                All Clients
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
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Company</th>
                                        <th>City</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="clientTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading...</td>
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

<!-- View Client Modal -->
<div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user mr-2"></i>
                    Client Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-info-circle mr-2"></i>Basic Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Name:</strong></td>
                                <td id="viewClientName">-</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td id="viewClientEmail">-</td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td id="viewClientPhone">-</td>
                            </tr>
                            <tr>
                                <td><strong>Company:</strong></td>
                                <td id="viewClientCompany">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="fas fa-map-marker-alt mr-2"></i>Address Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Address:</strong></td>
                                <td id="viewClientAddress">-</td>
                            </tr>
                            <tr>
                                <td><strong>City:</strong></td>
                                <td id="viewClientCity">-</td>
                            </tr>
                            <tr>
                                <td><strong>State:</strong></td>
                                <td id="viewClientState">-</td>
                            </tr>
                            <tr>
                                <td><strong>ZIP Code:</strong></td>
                                <td id="viewClientZip">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fas fa-sticky-note mr-2"></i>Notes</h6>
                        <p id="viewClientNotes" class="text-muted">No notes available</p>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fas fa-tasks mr-2"></i>Associated Tasks</h6>
                        <div id="viewClientTasks">
                            <p class="text-center text-muted">Loading tasks...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="editClientFromView()">
                    <i class="fas fa-edit mr-1"></i> Edit Client
                </button>
            </div>
        </div>
    </div>
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
                                <label for="clientEmail">Email</label>
                                <input type="email" class="form-control" id="clientEmail">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientPhone">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="clientPhone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientCompany">Company</label>
                                <input type="text" class="form-control" id="clientCompany">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="clientAddress">Address</label>
                        <textarea class="form-control" id="clientAddress" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="clientCity">City</label>
                                <input type="text" class="form-control" id="clientCity">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="clientState">State</label>
                                <input type="text" class="form-control" id="clientState">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="clientZip">ZIP Code</label>
                                <input type="text" class="form-control" id="clientZip">
                            </div>
                        </div>
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
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { 
            action: 'get_clients',
            _: new Date().getTime()
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayClients(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading clients:', error);
            toastr.error('Failed to load clients');
        }
    });
}

function displayClients(clients) {
    if ($.fn.DataTable.isDataTable('#clientTable')) {
        $('#clientTable').DataTable().destroy();
    }
    
    const tbody = $('#clientTableBody');
    tbody.empty();

    if (!clients || clients.length === 0) {
        tbody.append('<tr><td colspan="7" class="text-center">No clients found</td></tr>');
        return;
    }

    clients.forEach(function(client) {
        const row = `
            <tr>
                <td>${client.id}</td>
                <td>${client.name}</td>
                <td>${client.email || '-'}</td>
                <td>${client.phone}</td>
                <td>${client.company || '-'}</td>
                <td>${client.city || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-success" onclick="openWhatsApp('${client.phone}')" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="viewClient(${client.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-info" onclick="editClient(${client.id})" title="Edit">
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

    clientTable = $('#clientTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        destroy: true
    });
}

function openClientModal() {
    $('#clientId').val('');
    $('#clientForm')[0].reset();
    $('#clientModalTitle').text('Add Client');
    $('#clientModal').modal('show');
}

function editClient(id) {
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { action: 'get_client', id: id },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                $('#clientId').val(data.id);
                $('#clientName').val(data.name);
                $('#clientEmail').val(data.email);
                $('#clientPhone').val(data.phone);
                $('#clientCompany').val(data.company);
                $('#clientAddress').val(data.address);
                $('#clientCity').val(data.city);
                $('#clientState').val(data.state);
                $('#clientZip').val(data.zip);
                $('#clientNotes').val(data.notes);
                $('#clientModalTitle').text('Edit Client');
                $('#clientModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading client:', error);
            toastr.error('Failed to load client');
        }
    });
}

function saveClient() {
    const formData = {
        action: $('#clientId').val() ? 'update_client' : 'add_client',
        id: $('#clientId').val(),
        name: $('#clientName').val(),
        email: $('#clientEmail').val(),
        phone: $('#clientPhone').val(),
        company: $('#clientCompany').val(),
        address: $('#clientAddress').val(),
        city: $('#clientCity').val(),
        state: $('#clientState').val(),
        zip: $('#clientZip').val(),
        notes: $('#clientNotes').val()
    };

    $.ajax({
        url: 'ajax/client_api.php',
        type: 'POST',
        data: formData,
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Client saved successfully');
                $('#clientModal').modal('hide');
                
                setTimeout(function() {
                    loadClients();
                }, 300);
            } else {
                toastr.error(response.message || 'Failed to save client');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving client:', error);
            toastr.error('An error occurred while saving client');
        }
    });
}

function deleteClient(id) {
    if (!confirm('Are you sure you want to delete this client?')) {
        return;
    }

    $.ajax({
        url: 'ajax/client_api.php',
        type: 'POST',
        data: { action: 'delete_client', id: id },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Client deleted successfully');
                setTimeout(function() {
                    loadClients();
                }, 200);
            } else {
                toastr.error(response.message || 'Failed to delete client');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error deleting client:', error);
            toastr.error('An error occurred while deleting client');
        }
    });
}

let currentViewClientId = null;

function viewClient(id) {
    currentViewClientId = id;
    
    // Load client details
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { action: 'get_client', id: id },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const client = response.data;
                
                // Populate basic information
                $('#viewClientName').text(client.name || '-');
                $('#viewClientEmail').text(client.email || '-');
                $('#viewClientPhone').text(client.phone || '-');
                $('#viewClientCompany').text(client.company || '-');
                
                // Populate address information
                $('#viewClientAddress').text(client.address || '-');
                $('#viewClientCity').text(client.city || '-');
                $('#viewClientState').text(client.state || '-');
                $('#viewClientZip').text(client.zip || '-');
                
                // Populate notes
                $('#viewClientNotes').text(client.notes || 'No notes available');
                
                // Load client tasks
                loadClientTasks(id);
                
                // Show modal
                $('#viewClientModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading client:', error);
            toastr.error('Failed to load client details');
        }
    });
}

function loadClientTasks(clientId) {
    $('#viewClientTasks').html('<p class="text-center text-muted">Loading tasks...</p>');
    
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { action: 'get_tasks' },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                // Filter tasks for this client
                const clientTasks = response.data.filter(task => task.client_id == clientId);
                
                if (clientTasks.length === 0) {
                    $('#viewClientTasks').html('<p class="text-center text-muted">No tasks found for this client</p>');
                    return;
                }
                
                let tasksHtml = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                tasksHtml += '<thead><tr><th>Title</th><th>Priority</th><th>Status</th><th>Due Date</th></tr></thead><tbody>';
                
                clientTasks.forEach(function(task) {
                    const priorityBadge = task.priority === 'Urgent' ? 'badge-danger' :
                                        task.priority === 'High' ? 'badge-warning' :
                                        task.priority === 'Medium' ? 'badge-info' : 'badge-secondary';
                    
                    const statusBadge = task.status === 'Completed' ? 'badge-success' :
                                      task.status === 'In Progress' ? 'badge-primary' :
                                      task.status === 'On Hold' ? 'badge-warning' : 'badge-secondary';
                    
                    tasksHtml += `
                        <tr>
                            <td>${task.title}</td>
                            <td><span class="badge ${priorityBadge}">${task.priority}</span></td>
                            <td><span class="badge ${statusBadge}">${task.status}</span></td>
                            <td>${task.due_date || '-'}</td>
                        </tr>
                    `;
                });
                
                tasksHtml += '</tbody></table></div>';
                $('#viewClientTasks').html(tasksHtml);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading tasks:', error);
            $('#viewClientTasks').html('<p class="text-center text-danger">Failed to load tasks</p>');
        }
    });
}

function editClientFromView() {
    $('#viewClientModal').modal('hide');
    
    // Wait for modal to close, then open edit modal
    setTimeout(function() {
        if (currentViewClientId) {
            editClient(currentViewClientId);
        }
    }, 300);
}

function openWhatsApp(phone) {
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
    
    // Open WhatsApp
    const whatsappUrl = `https://wa.me/${cleanPhone}`;
    window.open(whatsappUrl, '_blank');
}
</script>

<?php require_once 'inc/footer.php'; ?>
