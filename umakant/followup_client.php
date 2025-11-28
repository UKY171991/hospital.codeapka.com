<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Followup Clients</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Followup Clients</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Add Client Form -->
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Client</h3>
                        </div>
                        <form id="addClientForm">
                            <input type="hidden" id="client_id" name="client_id">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone" required>
                                </div>
                                <div class="form-group">
                                    <label for="company">Company Name</label>
                                    <input type="text" class="form-control" id="company" name="company" placeholder="Enter company name">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Add Client</button>
                                <button type="button" class="btn btn-default float-right" id="cancelEdit" style="display: none;">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Client List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Client List</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap" id="clientsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Clients will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="overlay" id="loadingOverlay" style="display: none;">
                            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
$(document).ready(function() {
    loadClients();

    // Handle Form Submission
    $('#addClientForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const clientId = $('#client_id').val();
        
        // Determine action based on whether client_id is present
        if (clientId) {
            formData.append('action', 'update_client');
            formData.append('id', clientId);
        } else {
            formData.append('action', 'add_client');
        }

        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    resetForm();
                    loadClients();
                } else {
                    toastr.error(response.message || 'Error saving client');
                }
            },
            error: function() {
                toastr.error('Server error occurred');
            }
        });
    });

    // Edit Client
    $(document).on('click', '.edit-client', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'GET',
            data: { action: 'get_client', id: id },
            success: function(response) {
                if (response.success) {
                    const client = response.data;
                    $('#client_id').val(client.id);
                    $('#name').val(client.name);
                    $('#email').val(client.email);
                    $('#phone').val(client.phone);
                    $('#company').val(client.company);
                    
                    // Change UI to Edit Mode
                    $('.card-title').text('Edit Client');
                    $('button[type="submit"]').text('Update Client');
                    $('#cancelEdit').show();
                } else {
                    toastr.error(response.message || 'Error fetching client details');
                }
            },
            error: function() {
                toastr.error('Server error occurred');
            }
        });
    });

    // Cancel Edit
    $('#cancelEdit').on('click', function() {
        resetForm();
    });

    // Delete Client
    $(document).on('click', '.delete-client', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this client?')) {
            $.ajax({
                url: 'ajax/followup_client_api.php',
                type: 'POST',
                data: { action: 'delete_client', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        loadClients();
                    } else {
                        toastr.error(response.message || 'Error deleting client');
                    }
                },
                error: function() {
                    toastr.error('Server error occurred');
                }
            });
        }
    });
});

function resetForm() {
    $('#addClientForm')[0].reset();
    $('#client_id').val('');
    $('.card-title').text('Add New Client');
    $('button[type="submit"]').text('Add Client');
    $('#cancelEdit').hide();
}

function loadClients() {
    $('#loadingOverlay').show();
    $.ajax({
        url: 'ajax/followup_client_api.php',
        type: 'GET',
        data: { action: 'get_clients' },
        success: function(response) {
            $('#loadingOverlay').hide();
            if (response.success) {
                const tbody = $('#clientsTable tbody');
                tbody.empty();
                
                if (response.data.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center">No clients found</td></tr>');
                    return;
                }

                response.data.forEach(function(client) {
                    const row = `
                        <tr>
                            <td>${client.id}</td>
                            <td>${client.name}</td>
                            <td>${client.phone}</td>
                            <td>${client.email || '-'}</td>
                            <td>${client.company || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-info edit-client" data-id="${client.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-client" data-id="${client.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                toastr.error(response.message || 'Error loading clients');
            }
        },
        error: function() {
            $('#loadingOverlay').hide();
            toastr.error('Server error loading clients');
        }
    });
}
</script>
