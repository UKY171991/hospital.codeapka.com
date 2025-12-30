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

<style>
.bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #117a8b); }
.bg-light-soft { background-color: #f8f9fa !important; border: 1px solid #e9ecef !important; }
#detail_response_message:focus { background-color: #fff !important; box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25) !important; border-color: #17a2b8 !important; }
.btn-link:hover { text-decoration: none; transform: scale(1.1); transition: 0.2s; }
.modal-content { border-radius: 12px; overflow: hidden; }
#responseHistoryTable thead th { border-top: 0; font-size: 0.85rem; letter-spacing: 0.5px; }
.history-count { vertical-align: middle; font-size: 0.75rem; padding: 0.35em 0.65em; }
</style>

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
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header bg-gradient-info text-white py-3">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-user-circle mr-2"></i> Client Information & Feedback
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <!-- Quick Info Cards -->
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="p-3 bg-light rounded shadow-sm h-100 border-left border-info">
                                                    <small class="text-muted d-block text-uppercase font-weight-bold mb-1">Name & Contact</small>
                                                    <p class="mb-1"><strong><i class="fas fa-user text-info mr-1"></i> ${client.name}</strong></p>
                                                    <p class="mb-0 text-secondary small"><i class="fas fa-phone-alt mr-1"></i> ${client.phone}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 bg-light rounded shadow-sm h-100 border-left border-warning">
                                                    <small class="text-muted d-block text-uppercase font-weight-bold mb-1">Company & Email</small>
                                                    <p class="mb-1"><i class="fas fa-building text-warning mr-1"></i> ${client.company || 'N/A'}</p>
                                                    <p class="mb-0 text-secondary small"><i class="fas fa-envelope mr-1"></i> ${client.email || 'N/A'}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 bg-light rounded shadow-sm h-100 border-left border-success">
                                                    <small class="text-muted d-block text-uppercase font-weight-bold mb-1">Current Followup</small>
                                                    <p class="mb-1 text-success font-weight-bold"><i class="fas fa-bullseye mr-1"></i> ${client.followup_title || 'No Title Set'}</p>
                                                    <p class="mb-0 text-secondary small"><i class="fas fa-clock mr-1"></i> Last Activity: ${client.updated_at ? new Date(client.updated_at).toLocaleDateString() : 'N/A'}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Response Submission Area -->
                                        <div class="card border-primary mb-4 shadow-sm">
                                            <div class="card-header bg-light py-2">
                                                <h6 class="mb-0 font-weight-bold text-primary"><i class="fas fa-comment-dots mr-2"></i> Log New Response</h6>
                                            </div>
                                            <div class="card-body p-3">
                                                <div class="form-group mb-0">
                                                    <input type="hidden" id="editing_response_id" value="">
                                                    <textarea class="form-control border-0 bg-light-soft" id="detail_response_message" rows="3" 
                                                        style="resize: none; border-radius: 8px; font-size: 0.95rem;" 
                                                        placeholder="Type the client's response or feedback here..."></textarea>
                                                    <div class="mt-3 d-flex justify-content-end">
                                                        <button class="btn btn-secondary btn-sm mr-2 d-none" id="cancelResponseEditBtn">
                                                            <i class="fas fa-times"></i> Cancel
                                                        </button>
                                                        <button class="btn btn-primary btn-sm px-4 shadow-sm" id="saveResponseBtn" data-id="${client.id}">
                                                            <i class="fas fa-save mr-1"></i> Save Response
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- History Section -->
                                        <div class="response-history-section">
                                            <h6 class="font-weight-bold mb-3 d-flex align-items-center">
                                                <i class="fas fa-history text-muted mr-2"></i> Response History
                                                <span class="badge badge-secondary ml-2 history-count"></span>
                                            </h6>
                                            <div class="table-responsive rounded border shadow-sm" style="max-height: 250px; overflow-y: auto;">
                                                <table class="table table-hover table-sm mb-0" id="responseHistoryTable">
                                                    <thead class="bg-dark text-white">
                                                        <tr>
                                                            <th class="py-2 pl-3" style="width: 25%">Date & Time</th>
                                                            <th class="py-2">Message</th>
                                                            <th class="py-2 pr-3 text-center" style="width: 15%">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr><td colspan="3" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i> Loading history...</td></tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light py-2">
                                        <button type="button" class="btn btn-secondary btn-sm px-4" data-dismiss="modal">Close</button>
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
                    $('.history-count').text(res.data.length);
                    tbody.empty();
                    if (res.data.length === 0) {
                        tbody.append('<tr><td colspan="3" class="text-center py-4 text-muted">No response history found for this client.</td></tr>');
                        return;
                    }
                    res.data.forEach(item => {
                        const date = new Date(item.created_at).toLocaleString('en-US', { 
                            month: 'short', day: 'numeric', year: 'numeric', 
                            hour: '2-digit', minute: '2-digit' 
                        });
                        tbody.append(`
                            <tr data-id="${item.id}" data-message="${item.response_message.replace(/"/g, '&quot;')}">
                                <td class="small text-muted pl-3 py-2 border-bottom">${date}</td>
                                <td class="py-2 border-bottom" style="white-space: pre-wrap; font-size: 0.9rem;">${item.response_message}</td>
                                <td class="text-center py-2 border-bottom pr-3">
                                    <div class="btn-group">
                                        <button class="btn btn-link btn-sm text-primary edit-detail-response px-2" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-link btn-sm text-danger delete-detail-response px-2" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                }
            }
        });
    }

    // Save/Update Response Message
    $(document).on('click', '#saveResponseBtn', function() {
        const clientId = $(this).data('id');
        const responseId = $('#editing_response_id').val();
        const response = $('#detail_response_message').val();
        const $btn = $(this);
        
        if (!response.trim()) {
            toastr.error('Please enter a response message');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        const action = responseId ? 'edit_response' : 'update_response';
        const data = { action: action, response_message: response };
        if (responseId) data.id = responseId; else data.id = clientId;

        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'POST',
            data: data,
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message);
                    $('#detail_response_message').val('');
                    $('#editing_response_id').val('');
                    $('#saveResponseBtn').html('<i class="fas fa-save"></i> Save Response');
                    $('#cancelResponseEditBtn').addClass('d-none');
                    loadResponseHistory(clientId);
                    loadClients(currentPage);
                } else {
                    toastr.error(res.message);
                }
            },
            complete: function() {
                $btn.prop('disabled', false);
                if (!$('#editing_response_id').val()) {
                    $btn.html('<i class="fas fa-save"></i> Save Response');
                } else {
                    $btn.html('<i class="fas fa-save"></i> Update Response');
                }
            }
        });
    });

    // Edit Response Detail Click
    $(document).on('click', '.edit-detail-response', function() {
        const tr = $(this).closest('tr');
        const id = tr.data('id');
        const message = tr.data('message');
        
        $('#editing_response_id').val(id);
        $('#detail_response_message').val(message).focus();
        $('#saveResponseBtn').html('<i class="fas fa-save"></i> Update Response');
        $('#cancelResponseEditBtn').removeClass('d-none');
    });

    // Cancel Edit Click
    $(document).on('click', '#cancelResponseEditBtn', function() {
        $('#editing_response_id').val('');
        $('#detail_response_message').val('');
        $('#saveResponseBtn').html('<i class="fas fa-save"></i> Save Response');
        $(this).addClass('d-none');
    });

    // Delete Response Detail Click
    $(document).on('click', '.delete-detail-response', function() {
        if (!confirm('Are you sure you want to delete this response message?')) return;
        
        const id = $(this).closest('tr').data('id');
        const clientId = $('#saveResponseBtn').data('id');
        
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'POST',
            data: { action: 'delete_response', id: id },
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message);
                    loadResponseHistory(clientId);
                }
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
