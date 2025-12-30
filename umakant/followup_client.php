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
                <!-- Client List (Full Width) -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Client List</h3>
                            <div class="card-tools d-flex" style="gap: 10px;">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="text" id="tableSearch" class="form-control float-right" placeholder="Search name/phone/email...">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" id="openAddClientModal">
                                    <i class="fas fa-plus"></i> Add New Client
                                </button>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap" id="clientsTable">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Followup Title</th>
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

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addClientForm">
                <div class="modal-body">
                    <input type="hidden" id="client_id" name="client_id">
                    <div class="row">
                        <div class="col-md-6">
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
                            <div class="form-group">
                                <label for="followup_title">Followup Title</label>
                                <input type="text" class="form-control" id="followup_title" name="followup_title" placeholder="Enter message title">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="templateSelector">Select Followup Template (Optional)</label>
                                <select class="form-control" id="templateSelector">
                                    <option value="">-- Select Template --</option>
                                    <!-- Templates will be loaded here -->
                                </select>
                            </div>
                            <div class="form-group d-none">
                                <label for="followup_message">Followup Message</label>
                                <textarea class="form-control" id="followup_message" name="followup_message" placeholder="Enter followup message" rows="8"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveClientBtn">Add Client</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
let currentPage = 1;
let currentSearch = '';
const limit = 10;
let templates = [];

$(document).ready(function() {
    loadClients(currentPage);
    loadTemplates();

    // Open Modal for Add
    $('#openAddClientModal').on('click', function() {
        resetForm();
        $('#addClientModal').modal('show');
    });

    // Handle Search
    let searchTimer;
    $('#tableSearch').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            currentSearch = $(this).val();
            currentPage = 1;
            loadClients(currentPage);
        }, 500);
    });

    // Load Templates
    function loadTemplates() {
        $.ajax({
            url: 'ajax/followup_templates_api.php',
            type: 'GET',
            data: { action: 'get_templates', limit: 100 },
            success: function(response) {
                if (response.success) {
                    templates = response.data;
                    const selector = $('#templateSelector');
                    selector.empty().append('<option value="">-- Select Template --</option>');
                    templates.forEach(tpl => {
                        selector.append(`<option value="${tpl.id}">${tpl.template_name}</option>`);
                    });
                }
            }
        });
    }

    // Apply Template
    $('#templateSelector').on('change', function() {
        const id = $(this).val();
        if (id) {
            const template = templates.find(t => t.id == id);
            if (template) {
                // Set Title automatically
                $('#followup_title').val(template.template_name);
                // Remove HTML tags for plain textarea
                const cleanContent = template.content.replace(/<[^>]*>?/gm, '');
                $('#followup_message').val(cleanContent);
            }
        }
    });

    // Handle Form Submission
    $('#addClientForm').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val().trim();
        const phone = $('#phone').val().trim();
        
        if (!email && !phone) {
            toastr.error('Either Email or Phone is required');
            return;
        }
        
        const $btn = $('#saveClientBtn');
        const originalBtnText = $btn.text();
        $btn.prop('disabled', true).text('Processing...');

        const formData = new FormData(this);
        const clientId = $('#client_id').val();
        
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
                    $('#addClientModal').modal('hide');
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
                                    <div class="modal-header bg-info">
                                        <h5 class="modal-title">Client Details</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <p><strong>Name:</strong> ${client.name}</p>
                                                <p><strong>Phone:</strong> ${client.phone}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Email:</strong> ${client.email || 'N/A'}</p>
                                                <p><strong>Company:</strong> ${client.company || 'N/A'}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <p><strong>Followup Title:</strong> ${client.followup_title || 'N/A'}</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h6><strong>Response Message:</strong></h6>
                                                <div class="form-group">
                                                    <textarea class="form-control mb-2" id="detail_response_message" rows="3" placeholder="Enter response from client..."></textarea>
                                                    <button class="btn btn-sm btn-success float-right" id="saveResponseBtn" data-id="${client.id}">
                                                        <i class="fas fa-save"></i> Save Response
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h6><strong>Response History:</strong></h6>
                                                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                                    <table class="table table-sm table-bordered" id="responseHistoryTable">
                                                        <thead class="bg-light">
                                                            <tr>
                                                                <th style="width: 25%">Date & Time</th>
                                                                <th>Message</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr><td colspan="2" class="text-center">Loading history...</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
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
                    $('#viewClientModal').remove();
                    $('body').append(modal);
                    $('#viewClientModal').modal('show');
                    loadResponseHistory(client.id);
                }
            }
        });
    });

    function loadResponseHistory(clientId) {
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'GET',
            data: { action: 'get_responses', client_id: clientId },
            success: function(res) {
                if (res.success) {
                    const tbody = $('#responseHistoryTable tbody');
                    tbody.empty();
                    if (res.data.length === 0) {
                        tbody.append('<tr><td colspan="2" class="text-center text-muted">No response history found</td></tr>');
                        return;
                    }
                    res.data.forEach(item => {
                        const date = new Date(item.created_at).toLocaleString();
                        tbody.append(`
                            <tr>
                                <td class="small">${date}</td>
                                <td style="white-space: pre-wrap;">${item.response_message}</td>
                            </tr>
                        `);
                    });
                }
            }
        });
    }

    // Save Response Message
    $(document).on('click', '#saveResponseBtn', function() {
        const id = $(this).data('id');
        const response = $('#detail_response_message').val();
        const $btn = $(this);
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'POST',
            data: { action: 'update_response', id: id, response_message: response },
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message);
                    $('#detail_response_message').val(''); // Clear input
                    loadResponseHistory(id); // Refresh history
                    loadClients(currentPage); // Update main table too
                } else {
                    toastr.error(res.message);
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Response');
            }
        });
    });

    // WhatsApp Client
    $(document).on('click', '.whatsapp-client', function() {
        const phone = $(this).data('phone');
        const message = $(this).closest('tr').data('message');
        if (!phone) {
            toastr.error('Phone number not available');
            return;
        }
        const cleanPhone = phone.replace(/\D/g, '');
        const whatsappUrl = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message || '')}`;
        window.open(whatsappUrl, '_blank');
    });

    // Email Client
    $(document).on('click', '.email-client', function() {
        const email = $(this).data('email');
        const message = $(this).closest('tr').data('message');
        if (!email) {
            toastr.error('Email address not available');
            return;
        }
        
        const modal = `
            <div class="modal fade" id="sendEmailModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
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
                                    <textarea class="form-control" id="emailMessage" name="message" placeholder="Enter your message" rows="6" required>${message || ''}</textarea>
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
        $('#sendEmailModal').remove();
        $('body').append(modal);
        $('#sendEmailModal').modal('show');
    });

    // Handle Email Form Submission
    $(document).on('submit', '#sendEmailForm', function(e) {
        e.preventDefault();
        const $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true).text('Sending...');
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
            complete: function() {
                $btn.prop('disabled', false).text('Send Email');
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
                    resetForm();
                    $('#client_id').val(client.id);
                    $('#name').val(client.name);
                    $('#email').val(client.email);
                    $('#phone').val(client.phone);
                    $('#company').val(client.company);
                    $('#followup_title').val(client.followup_title);
                    $('#followup_message').val(client.followup_message);
                    
                    // Try to select the template automatically based on title
                    if (client.followup_title && templates.length > 0) {
                        const tpl = templates.find(t => t.template_name === client.followup_title);
                        if (tpl) $('#templateSelector').val(tpl.id);
                    }
                    
                    $('#addClientModalLabel').text('Edit Client');
                    $('#saveClientBtn').text('Update Client');
                    $('#addClientModal').modal('show');
                }
            }
        });
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
                    }
                }
            });
        }
    });

    // Copy Message
    $(document).on('click', '.copy-msg', function() {
        const message = $(this).closest('tr').data('message');
        if (message) {
            const el = document.createElement('textarea');
            el.value = message;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            toastr.info('Message copied to clipboard');
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
    $('#templateSelector').val('');
    $('#addClientModalLabel').text('Add New Client');
    $('#saveClientBtn').text('Add Client');
}

function loadClients(page) {
    $('#loadingOverlay').show();
    $.ajax({
        url: 'ajax/followup_client_api.php',
        type: 'GET',
        data: { action: 'get_clients', page: page, search: currentSearch },
        success: function(response) {
            $('#loadingOverlay').hide();
            if (response.success) {
                const tbody = $('#clientsTable tbody');
                tbody.empty();
                
                if (response.data.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center">No clients found</td></tr>');
                    $('#pagination').empty();
                    return;
                }

                response.data.forEach(function(client, index) {
                    const srNo = (page - 1) * limit + index + 1;
                    const truncatedMessage = client.followup_message ? (client.followup_message.length > 50 ? client.followup_message.substring(0, 50) + '...' : client.followup_message) : '-';
                    const row = `
                        <tr data-message="${client.followup_message || ''}">
                            <td>${srNo}</td>
                            <td>${client.name}</td>
                            <td>${client.phone}</td>
                            <td>${client.email || '-'}</td>
                            <td>${client.company || '-'}</td>
                            <td>${client.followup_title || '-'}</td>
                            <td>
                                <button class="btn btn-xs btn-info view-client" data-id="${client.id}" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-xs btn-success whatsapp-client" data-phone="${client.phone}" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                                <button class="btn btn-xs btn-warning email-client" data-email="${client.email}" title="Email">
                                    <i class="fas fa-envelope"></i>
                                </button>
                                <button class="btn btn-xs btn-primary edit-client" data-id="${client.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-xs btn-danger delete-client" data-id="${client.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                renderPagination(response.pagination);
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
    const prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
    ul.append(`<li class="page-item ${prevDisabled}"><a class="page-link" href="#" data-page="${pagination.current_page - 1}">&laquo;</a></li>`);
    for (let i = 1; i <= pagination.total_pages; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        ul.append(`<li class="page-item ${active}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`);
    }
    const nextDisabled = pagination.current_page === pagination.total_pages ? 'disabled' : '';
    ul.append(`<li class="page-item ${nextDisabled}"><a class="page-link" href="#" data-page="${pagination.current_page + 1}">&raquo;</a></li>`);
}
</script>
