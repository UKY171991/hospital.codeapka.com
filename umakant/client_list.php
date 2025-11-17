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
                    <h1><i class="fas fa-list mr-2"></i>Client List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Client List</li>
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
                                <i class="fas fa-users mr-2"></i>
                                All Clients
                            </h3>
                            <div class="card-tools">
                                <a href="add_client.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Client
                                </a>
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

<script>
let clientTable;

$(document).ready(function() {
    loadClients();
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
                    <button class="btn btn-sm btn-info" onclick="viewClient(${client.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="editClient(${client.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteClient(${client.id})">
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

function viewClient(id) {
    window.location.href = 'view_client.php?id=' + id;
}

function editClient(id) {
    window.location.href = 'edit_client.php?id=' + id;
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
</script>

<?php require_once 'inc/footer.php'; ?>
