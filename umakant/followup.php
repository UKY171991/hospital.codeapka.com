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
                    <h1>Followups</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Followups</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Add/Edit Followup Form -->
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Followup</h3>
                        </div>
                        <form id="addFollowupForm">
                            <input type="hidden" id="followup_id" name="followup_id">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="client_id">Client <span class="text-danger">*</span></label>
                                    <select class="form-control select2" id="client_id" name="client_id" style="width: 100%;" required>
                                        <option value="">Select Client</option>
                                        <!-- Clients will be loaded here -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="followup_date">Followup Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="followup_date" name="followup_date" required value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="next_followup_date">Next Followup Date</label>
                                    <input type="date" class="form-control" id="next_followup_date" name="next_followup_date">
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Pending">Pending</option>
                                        <option value="Call Later">Call Later</option>
                                        <option value="Interested">Interested</option>
                                        <option value="Not Interested">Not Interested</option>
                                        <option value="Converted">Converted</option>
                                        <option value="No Answer">No Answer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter remarks"></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="send_email" name="send_email" value="1" checked>
                                        <label class="custom-control-label" for="send_email">Send Email Notification</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input" id="send_whatsapp" name="send_whatsapp" value="1" checked>
                                        <label class="custom-control-label" for="send_whatsapp">Send WhatsApp Message</label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Add Followup</button>
                                <button type="button" class="btn btn-default float-right" id="cancelEdit" style="display: none;">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Followup List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Followup List</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap" id="followupsTable">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Next Date</th>
                                        <th>Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Followups will be loaded here -->
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

<!-- Select2 -->
<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<script src="assets/plugins/select2/js/select2.full.min.js"></script>

<script>
let currentPage = 1;
const limit = 10;

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    loadClientsDropdown();
    loadFollowups(currentPage);

    // Handle Form Submission
    $('#addFollowupForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const followupId = $('#followup_id').val();
        
        // Determine action based on whether followup_id is present
        if (followupId) {
            formData.append('action', 'update_followup');
            formData.append('id', followupId);
        } else {
            formData.append('action', 'add_followup');
        }

        $.ajax({
            url: 'ajax/followup_api.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    
                    // Open WhatsApp if link is provided
                    if (response.whatsapp_link) {
                        window.open(response.whatsapp_link, '_blank');
                    }
                    
                    resetForm();
                    loadFollowups(currentPage);
                } else {
                    toastr.error(response.message || 'Error saving followup');
                }
            },
            error: function() {
                toastr.error('Server error occurred');
            }
        });
    });

    // Edit Followup
    $(document).on('click', '.edit-followup', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'ajax/followup_api.php',
            type: 'GET',
            data: { action: 'get_followup', id: id },
            success: function(response) {
                if (response.success) {
                    const followup = response.data;
                    $('#followup_id').val(followup.id);
                    $('#client_id').val(followup.client_id).trigger('change');
                    $('#followup_date').val(followup.followup_date);
                    $('#next_followup_date').val(followup.next_followup_date);
                    $('#status').val(followup.status);
                    $('#remarks').val(followup.remarks);
                    
                    // Change UI to Edit Mode
                    $('.card-title').text('Edit Followup');
                    $('button[type="submit"]').text('Update Followup');
                    $('#cancelEdit').show();
                } else {
                    toastr.error(response.message || 'Error fetching followup details');
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

    // Delete Followup
    $(document).on('click', '.delete-followup', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this followup?')) {
            $.ajax({
                url: 'ajax/followup_api.php',
                type: 'POST',
                data: { action: 'delete_followup', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        loadFollowups(currentPage);
                    } else {
                        toastr.error(response.message || 'Error deleting followup');
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
            loadFollowups(page);
        }
    });
});

function resetForm() {
    $('#addFollowupForm')[0].reset();
    $('#followup_id').val('');
    $('#client_id').val('').trigger('change');
    $('#followup_date').val('<?php echo date('Y-m-d'); ?>');
    $('.card-title').text('Add New Followup');
    $('button[type="submit"]').text('Add Followup');
    $('#cancelEdit').hide();
}

function loadClientsDropdown() {
    $.ajax({
        url: 'ajax/followup_api.php',
        type: 'GET',
        data: { action: 'get_clients_dropdown' },
        success: function(response) {
            if (response.success) {
                const select = $('#client_id');
                // Keep the first option
                select.find('option:not(:first)').remove();
                
                response.data.forEach(function(client) {
                    const companyText = client.company ? ` (${client.company})` : '';
                    select.append(`<option value="${client.id}">${client.name}${companyText}</option>`);
                });
            }
        }
    });
}

function loadFollowups(page) {
    $('#loadingOverlay').show();
    $.ajax({
        url: 'ajax/followup_api.php',
        type: 'GET',
        data: { action: 'get_followups', page: page },
        success: function(response) {
            $('#loadingOverlay').hide();
            if (response.success) {
                const tbody = $('#followupsTable tbody');
                tbody.empty();
                
                if (response.data.length === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center">No followups found</td></tr>');
                    $('#pagination').empty();
                    return;
                }

                response.data.forEach(function(followup, index) {
                    const srNo = (page - 1) * limit + index + 1;
                    
                    // Status badge color
                    let badgeClass = 'badge-secondary';
                    if (followup.status === 'Interested') badgeClass = 'badge-success';
                    else if (followup.status === 'Not Interested') badgeClass = 'badge-danger';
                    else if (followup.status === 'Call Later') badgeClass = 'badge-warning';
                    else if (followup.status === 'Converted') badgeClass = 'badge-primary';
                    
                    // WhatsApp Link Generation
                    let whatsappBtn = '';
                    if (followup.client_phone) {
                        const cleanPhone = followup.client_phone.replace(/[^0-9]/g, '');
                        const waMessage = `Dear ${followup.client_name}, Followup Update: ${followup.status}. Remarks: ${followup.remarks || ''}. Next Followup: ${followup.next_followup_date || 'Not scheduled'}`;
                        const waLink = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(waMessage)}`;
                        whatsappBtn = `
                            <a href="${waLink}" target="_blank" class="btn btn-sm btn-success" title="Send WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        `;
                    }

                    // Email Button
                    let emailBtn = '';
                    // We don't have client_email in the current get_followups response, let's assume we can try to send it anyway
                    // or we should update get_followups to return email. 
                    // For now, let's show the button and let the server validate.
                    emailBtn = `
                        <button class="btn btn-sm btn-warning send-email" data-id="${followup.id}" title="Send Email">
                            <i class="fas fa-envelope"></i>
                        </button>
                    `;

                    const row = `
                        <tr>
                            <td>${srNo}</td>
                            <td>
                                <strong>${followup.client_name}</strong><br>
                                <small class="text-muted">${followup.client_company || ''}</small>
                            </td>
                            <td>${followup.followup_date}</td>
                            <td><span class="badge ${badgeClass}">${followup.status}</span></td>
                            <td>${followup.next_followup_date || '-'}</td>
                            <td><small>${followup.remarks || '-'}</small></td>
                            <td>
                                ${whatsappBtn}
                                ${emailBtn}
                                <button class="btn btn-sm btn-info edit-followup" data-id="${followup.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-followup" data-id="${followup.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                
                renderPagination(response.pagination);
            } else {
                toastr.error(response.message || 'Error loading followups');
            }
        },
        error: function() {
            $('#loadingOverlay').hide();
            toastr.error('Server error loading followups');
        }
    });
}

// Send Email Handler
$(document).on('click', '.send-email', function() {
    const id = $(this).data('id');
    const btn = $(this);
    
    if (confirm('Send email notification to client?')) {
        btn.prop('disabled', true);
        $.ajax({
            url: 'ajax/followup_api.php',
            type: 'POST',
            data: { action: 'send_email_notification', id: id },
            success: function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message || 'Error sending email');
                }
            },
            error: function() {
                btn.prop('disabled', false);
                toastr.error('Server error occurred');
            }
        });
    }
});

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
