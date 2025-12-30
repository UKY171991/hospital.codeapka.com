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
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone">
                                    <small class="text-muted">Either Email or Phone is required</small>
                                </div>
                                <div class="form-group">
                                    <label for="company">Company Name</label>
                                    <input type="text" class="form-control" id="company" name="company" placeholder="Enter company name">
                                </div>
                                <div class="form-group">
                                    <label for="followup_message">Followup Message</label>
                                    <textarea class="form-control" id="followup_message" name="followup_message" placeholder="Enter followup message" rows="3"></textarea>
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
                                        <th>Sr. No.</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Clients will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right" id="pagination">
                                <!-- Pagination links will be loaded here -->
                            </ul>
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
let currentPage = 1;
const limit = 10;

$(document).ready(function() {
    loadClients(currentPage);

    // Handle Form Submission
    $('#addClientForm').on('submit', function(e) {
        e.preventDefault();
        
        // Client-side validation
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        
        if (!email && !phone) {
            toastr.error('Either Email or Phone is required');
            return;
        }
        
        const formData = new FormData(this);
        const clientId = $('#client_id').val();
        
        // Determine action based on whether client_id is present
        if (clientId) {
            formData.append('action', 'update_client');
            formData.append('id', clientId);
        } else {
            formData.append('action', 'add_client');
        }

        const $btn = $(this).find('button[type="submit"]');
        const originalBtnText = $btn.text();
        $btn.prop('disabled', true).text('Processing...');

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
                    loadClients(currentPage);
                } else {
                    toastr.error(response.message || 'Error saving client');
                }
            },
            error: function() {
                toastr.error('Server error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false).text(originalBtnText);
            }
        });
    });

    // View Client
    $(document).on('click', '.view-client', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'GET',
            data: { action: 'get_client', id: id },
            success: function(response) {
                if (response.success) {
                    const client = response.data;
                    const modal = `
                        <div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Client Details</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Name:</strong> ${client.name}</p>
                                                <p><strong>Phone:</strong> ${client.phone}</p>
                                                <p><strong>Email:</strong> ${client.email || 'N/A'}</p>
                                                <p><strong>Company:</strong> ${client.company || 'N/A'}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Followup Message:</strong></p>
                                                <p>${client.followup_message || 'N/A'}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Remove existing modal if any
                    $('#viewClientModal').remove();
                    $('body').append(modal);
                    $('#viewClientModal').modal('show');
                } else {
                    toastr.error(response.message || 'Error fetching client details');
                }
            },
            error: function() {
                toastr.error('Server error occurred');
            }
        });
    });

    // WhatsApp Client
    $(document).on('click', '.whatsapp-client', function() {
        const phone = $(this).data('phone');
        if (!phone) {
            toastr.error('Phone number not available');
            return;
        }
        
        // Remove any non-digit characters
        const cleanPhone = phone.replace(/\D/g, '');
        const whatsappUrl = `https://wa.me/${cleanPhone}`;
        window.open(whatsappUrl, '_blank');
    });

    // Email Client
    $(document).on('click', '.email-client', function() {
        const email = $(this).data('email');
        if (!email) {
            toastr.error('Email address not available');
            return;
        }
        
        const modal = `
            <div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Send Email</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <form id="sendEmailForm">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="emailTo">To:</label>
                                    <input type="email" class="form-control" id="emailTo" name="to" value="${email}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="emailSubject">Subject:</label>
                                    <input type="text" class="form-control" id="emailSubject" name="subject" placeholder="Enter email subject" required>
                                </div>
                                <div class="form-group">
                                    <label for="emailMessage">Message:</label>
                                    <textarea class="form-control" id="emailMessage" name="message" placeholder="Enter your message" rows="6" required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Send Email</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#sendEmailModal').remove();
        $('body').append(modal);
        $('#sendEmailModal').modal('show');
    });

    // Handle Email Form Submission
    $(document).on('submit', '#sendEmailForm', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'send_email');
        
        $.ajax({
            url: 'ajax/send_email_api.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#sendEmailModal').modal('hide');
                } else {
                    toastr.error(response.message || 'Error sending email');
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
                    $('#followup_message').val(client.followup_message);
                    
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
                        loadClients(currentPage);
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
    
    // Pagination Click
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            loadClients(page);
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

function loadClients(page) {
    $('#loadingOverlay').show();
    $.ajax({
        url: 'ajax/followup_client_api.php',
        type: 'GET',
        data: { action: 'get_clients', page: page },
        success: function(response) {
            $('#loadingOverlay').hide();
            if (response.success) {
                const tbody = $('#clientsTable tbody');
                tbody.empty();
                
                if (response.data.length === 0) {
                    tbody.append('<tr><td colspan="5" class="text-center">No clients found</td></tr>');
                    $('#pagination').empty();
                    return;
                }

                response.data.forEach(function(client, index) {
                    const srNo = (page - 1) * limit + index + 1;
                    const row = `
                        <tr>
                            <td>${srNo}</td>
                            <td>${client.name}</td>
                            <td>${client.phone}</td>
                            <td>${client.email || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-info view-client" data-id="${client.id}" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success whatsapp-client" data-phone="${client.phone}" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                                <button class="btn btn-sm btn-warning email-client" data-email="${client.email}" title="Email">
                                    <i class="fas fa-envelope"></i>
                                </button>
                                <button class="btn btn-sm btn-primary edit-client" data-id="${client.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-client" data-id="${client.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                
                renderPagination(response.pagination);
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

function renderPagination(pagination) {
    const ul = $('#pagination');
    ul.empty();
    
    if (pagination.total_pages <= 1) return;
    
    // Previous
    const prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
    ul.append(`
        <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" data-page="${pagination.current_page - 1}">&laquo;</a>
        </li>
    `);
    
    // Pages
    for (let i = 1; i <= pagination.total_pages; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        ul.append(`
            <li class="page-item ${active}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `);
    }
    
    // Next
    const nextDisabled = pagination.current_page === pagination.total_pages ? 'disabled' : '';
    ul.append(`
        <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#" data-page="${pagination.current_page + 1}">&raquo;</a>
        </li>
    `);
}
</script>
