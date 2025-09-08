/**
 * User Management JavaScript
 * Handles CRUD operations for user management interface
 */
(
function(){

    // Prevent the script from executing multiple times if it's accidentally included twice.
    // This avoids "Identifier 'currentPage' has already been declared" and similar issues.
    if (window.__user_js_loaded) {
        // Already loaded — skip re-initialization.
        return;
    }
    window.__user_js_loaded = true;

// Global variables (use var to avoid redeclaration errors if script is included twice)
var currentPage = 1;
var totalRecords = 0;
var recordsPerPage = 10;
var searchTimeout;

// Initialize page when document is ready
$(document).ready(function() {
    loadUsers();
    initializeEventListeners();
});

function initializeEventListeners() {
    // Save user button click
    $('#saveUserBtn').click(function() {
        saveUserData();
    });

    // Search functionality
    $('#usersSearch').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            performSearch();
        }, 300);
    });

    // Clear search button
    $('#usersSearchClear').click(function(e) {
        e.preventDefault();
        $('#usersSearch').val('');
        $('#usersSearch').trigger('input');
    });

    // Edit user button click
    $('#usersTable').on('click', '.edit-user', function() {
        const id = $(this).data('id');
        editUser(id);
    });

    // Delete user button click
    $('#usersTable').on('click', '.delete-user', function() {
        const id = $(this).data('id');
        deleteUser(id);
    });

    // View user button click
    $('#usersTable').on('click', '.view-user', function() {
        const id = $(this).data('id');
        viewUser(id);
    });

    // Modal hidden event
    $('#userModal').on('hidden.bs.modal', function() {
        resetModalForm();
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.user-checkbox').prop('checked', isChecked);
        updateSelectionUI();
    });

    // Individual checkboxes
    $('#usersTable').on('change', '.user-checkbox', function() {
        updateSelectionUI();
        
        // Update select all checkbox
        const totalCheckboxes = $('.user-checkbox').length;
        const checkedCheckboxes = $('.user-checkbox:checked').length;
        
        if (checkedCheckboxes === totalCheckboxes) {
            $('#selectAll').prop('checked', true).prop('indeterminate', false);
        } else if (checkedCheckboxes > 0) {
            $('#selectAll').prop('checked', false).prop('indeterminate', true);
        } else {
            $('#selectAll').prop('checked', false).prop('indeterminate', false);
        }
    });

    // Bulk actions
    $('.bulk-export').on('click', function() {
        bulkExportUsers();
    });

    $('.bulk-delete').on('click', function() {
        bulkDeleteUsers();
    });
}

/**
 * Update selection UI based on checked items
 */
function updateSelectionUI() {
    const selectedCount = $('.user-checkbox:checked').length;
    $('.selected-count').text(selectedCount);
    
    if (selectedCount > 0) {
        $('.bulk-actions').addClass('show');
    } else {
        $('.bulk-actions').removeClass('show');
    }
}

/**
 * Select all users
 */
function selectAllUsers() {
    $('.user-checkbox').prop('checked', true);
    $('#selectAll').prop('checked', true);
    updateSelectionUI();
}

/**
 * Deselect all users
 */
function deselectAllUsers() {
    $('.user-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    updateSelectionUI();
}

/**
 * Bulk export selected users
 */
function bulkExportUsers() {
    const selectedIds = $('.user-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        showAlert('Please select users to export', 'error');
        return;
    }
    
    // Simple CSV export
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "ID,Username,Email,Full Name,Role,Status\n";
    
    selectedIds.forEach(id => {
        const row = $(`input[value="${id}"]`).closest('tr');
        const cells = row.find('td');
        if (cells.length > 1) {
            csvContent += `${cells.eq(1).text()},${cells.eq(2).find('.font-weight-bold').text()},${cells.eq(3).text()},${cells.eq(4).text()},${cells.eq(5).find('.badge').text()},${cells.eq(6).text()}\n`;
        }
    });
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "selected_users_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showAlert(`Exported ${selectedIds.length} users successfully!`, 'success');
}

/**
 * Bulk delete selected users
 */
function bulkDeleteUsers() {
    const selectedIds = $('.user-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selectedIds.length === 0) {
        showAlert('Please select users to delete', 'error');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete ${selectedIds.length} selected users? This action cannot be undone.`)) {
        return;
    }
    
    let completedRequests = 0;
    let successCount = 0;
    let errorCount = 0;
    
    selectedIds.forEach(id => {
        $.post('ajax/user_api.php', { action: 'delete', id: id, ajax: 1 })
            .done(function(response) {
                if (response.success) {
                    successCount++;
                } else {
                    errorCount++;
                }
            })
            .fail(function() {
                errorCount++;
            })
            .always(function() {
                completedRequests++;
                
                if (completedRequests === selectedIds.length) {
                    // All requests completed
                    if (successCount > 0) {
                        showAlert(`Successfully deleted ${successCount} users`, 'success');
                        loadUsers();
                        deselectAllUsers();
                    }
                    
                    if (errorCount > 0) {
                        showAlert(`Failed to delete ${errorCount} users`, 'error');
                    }
                }
            });
    });
}

/**
 * Load users from the server
 */
function loadUsers() {
    // Show loading indicator
    $('#usersTable tbody').html('<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');

    $.get('ajax/user_api.php', { action: 'list' })
        .done(function(response) {
            if (response.success) {
                populateUsersTable(response.data);
                updateStats();
            } else {
                showAlert('Failed to load users: ' + (response.message || 'Unknown error'), 'error');
                $('#usersTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load data</td></tr>');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load users: ' + errorMsg, 'error');
            $('#usersTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load data</td></tr>');
        });
}

/**
 * Populate the users table with data
 */
function populateUsersTable(users) {
    let html = '';
    
    if (users.length === 0) {
        html = '<tr><td colspan="9" class="text-center text-muted">No users found</td></tr>';
    } else {
        users.forEach(user => {
            // Handle expire date styling
            const expireDate = user.expire_date || '';
            let expireDateClass = '';
            let expireDateDisplay = '';
            
            if (expireDate) {
                const date = new Date(expireDate.replace(' ', 'T'));
                const now = new Date();
                const diffDays = (date - now) / (1000 * 60 * 60 * 24);
                
                if (date < now) {
                    expireDateClass = 'text-danger';
                } else if (diffDays <= 7) {
                    expireDateClass = 'text-warning';
                } else {
                    expireDateClass = 'text-success';
                }
                
                try {
                    expireDateDisplay = date.toLocaleString();
                } catch (e) {
                    expireDateDisplay = expireDate;
                }
            }
            
            const statusClass = (user.is_active == 1) ? 'text-success' : 'text-danger';
            const statusText = (user.is_active == 1) ? 'Active' : 'Inactive';
            
            html += `
                <tr>
                    <td>
                        <input type="checkbox" class="user-checkbox" value="${user.id}">
                    </td>
                    <td>${user.id}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary text-white mr-2">
                                ${user.username.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-weight-bold">${user.username}</div>
                                ${user.role ? `<small class="text-muted">${user.role}</small>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${user.email || '-'}</td>
                    <td>${user.full_name || '-'}</td>
                    <td>
                        ${user.role ? `<span class="badge badge-info">${user.role}</span>` : '-'}
                    </td>
                    <td class="${statusClass}">${statusText}</td>
                    <td class="${expireDateClass}">${formatDateTime(expireDate) || '-'}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm view-user" data-id="${user.id}" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm edit-user" data-id="${user.id}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-user" data-id="${user.id}" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#usersTable tbody').html(html);
}

/**
 * Update dashboard statistics
 */
function updateStats() {
    // This could be enhanced to fetch actual stats from the server
    const totalUsers = $('#usersTable tbody tr').length;
    $('#totalUsers').text(totalUsers);
}

/**
 * Perform client-side search
 */
function performSearch() {
    const query = $('#usersSearch').val().toLowerCase().trim();
    
    if (!query) {
        $('#usersTable tbody tr').show();
        return;
    }
    
    $('#usersTable tbody tr').each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        row.toggle(text.indexOf(query) !== -1);
    });
}

/**
 * Open modal to add new user
 */
function openAddUserModal() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#userPassword').attr('required', true);
    $('#userModal .modal-title').text('Add User');
    $('#saveUserBtn').show();
    $('#userModal').modal('show');
}

/**
 * Edit user
 */
function editUser(id) {
    $.get('ajax/user_api.php', { action: 'get', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const user = response.data;
                populateUserForm(user);
                $('#userPassword').attr('required', false);
                $('#userModal .modal-title').text('Edit User');
                $('#saveUserBtn').show();
                $('#userModal').modal('show');
            } else {
                showAlert('Error loading user data: ' + (response.message || 'User not found'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load user data: ' + errorMsg, 'error');
        });
}

/**
 * View user (enhanced view)
 */
function viewUser(id) {
    $.get('ajax/user_api.php', { action: 'get', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const user = response.data;
                populateViewUserModal(user);
                $('#viewUserModal').modal('show');
            } else {
                showAlert('Error loading user data: ' + (response.message || 'User not found'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load user data: ' + errorMsg, 'error');
        });
}

/**
 * Populate the view user modal with data
 */
function populateViewUserModal(user) {
    const statusClass = (user.is_active == 1) ? 'success' : 'danger';
    const statusText = (user.is_active == 1) ? 'Active' : 'Inactive';
    
    const expireDate = user.expire_date || '';
    let expireDateDisplay = expireDate ? new Date(expireDate.replace(' ', 'T')).toLocaleString() : 'Not set';
    
    const roleColor = {
        'master': 'danger',
        'admin': 'warning', 
        'user': 'info'
    };
    
    const lastLogin = user.last_login ? new Date(user.last_login.replace(' ', 'T')).toLocaleString() : 'Never logged in';
    
    const viewContent = `
        <div class="user-profile-header text-center mb-4">
            <div class="avatar-large bg-primary text-white mx-auto mb-3">
                ${user.username.charAt(0).toUpperCase()}
            </div>
            <h4 class="mb-1">${user.full_name || user.username}</h4>
            <span class="badge badge-${roleColor[user.role] || 'secondary'} mb-2">${user.role || 'Unknown'}</span>
            <div>
                <span class="badge badge-${statusClass}">${statusText}</span>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">
                        <i class="fas fa-user mr-2"></i>Username
                    </label>
                    <div class="info-value">${user.username}</div>
                </div>
                
                <div class="info-group">
                    <label class="info-label">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </label>
                    <div class="info-value">${user.email || 'Not provided'}</div>
                </div>
                
                <div class="info-group">
                    <label class="info-label">
                        <i class="fas fa-id-card mr-2"></i>Full Name
                    </label>
                    <div class="info-value">${user.full_name || 'Not provided'}</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-group">
                    <label class="info-label">
                        <i class="fas fa-shield-alt mr-2"></i>Role
                    </label>
                    <div class="info-value">
                        <span class="badge badge-${roleColor[user.role] || 'secondary'}">${user.role || 'Unknown'}</span>
                    </div>
                </div>
                
                <div class="info-group">
                    <label class="info-label">
                        <i class="fas fa-clock mr-2"></i>Last Login
                    </label>
                    <div class="info-value">${lastLogin}</div>
                </div>
                
                <div class="info-group">
                    <label class="info-label">
                        <i class="fas fa-calendar-times mr-2"></i>Expire Date
                    </label>
                    <div class="info-value">${expireDateDisplay}</div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="info-group">
                <label class="info-label">
                    <i class="fas fa-info-circle mr-2"></i>Account Details
                </label>
                <div class="info-value">
                    <small class="text-muted">
                        User ID: ${user.id} | 
                        Added by: ${user.added_by || 'System'} | 
                        Status: <span class="badge badge-${statusClass} badge-sm">${statusText}</span>
                    </small>
                </div>
            </div>
        </div>
    `;
    
    $('#userViewDetails').html(viewContent);
    
    // Store user ID for edit functionality
    $('#viewUserModal').data('user-id', user.id);
}

/**
 * Edit user from view modal
 */
function editUserFromView() {
    const userId = $('#viewUserModal').data('user-id');
    $('#viewUserModal').modal('hide');
    setTimeout(() => {
        editUser(userId);
    }, 300);
}

/**
 * Print user details
 */
function printUserDetails() {
    const printContent = document.getElementById('userViewDetails').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>User Details</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .avatar-large { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 36px; }
                    .info-group { margin-bottom: 15px; }
                    .info-label { font-weight: bold; display: block; margin-bottom: 5px; }
                    .info-value { padding: 8px; background: #f8f9fa; border-radius: 4px; }
                    .badge { padding: 4px 8px; border-radius: 4px; color: white; }
                    .badge-success { background: #28a745; }
                    .badge-danger { background: #dc3545; }
                    .badge-warning { background: #ffc107; color: #212529; }
                    .badge-info { background: #17a2b8; }
                    .text-center { text-align: center; }
                    .row { display: flex; }
                    .col-md-6 { flex: 1; padding: 0 15px; }
                </style>
            </head>
            <body>
                <h1>User Details</h1>
                ${printContent}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

/**
 * Populate user form with data
 */
function populateUserForm(user) {
    $('#userId').val(user.id);
    $('#userUsername').val(user.username);
    $('#userFullName').val(user.full_name);
    $('#userEmail').val(user.email);
    $('#userRole').val(user.role);
    $('#userIsActive').val(user.is_active ? 1 : 0);
    
    // Convert server datetime to datetime-local format
    const expireDate = user.expire_date || '';
    if (expireDate) {
        const localDateTime = expireDate.replace(' ', 'T').slice(0, 16);
        $('#userExpireDateDisplay').val(localDateTime);
    } else {
        $('#userExpireDateDisplay').val('');
    }
    $('#userExpireDate').val(expireDate);
}

/**
 * Save user data
 */
function saveUserData() {
    // Convert datetime-local value to server format
    const expireVal = $('#userExpireDateDisplay').val();
    const sqlDateTime = expireVal ? expireVal.replace('T', ' ') + ':00' : '';
    $('#userExpireDate').val(sqlDateTime);

    const data = $('#userForm').serialize() + '&action=save&ajax=1';
    
    // Add loading state
    const submitBtn = $('#saveUserBtn');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.post('ajax/user_api.php', data)
        .done(function(response) {
            if (response.success) {
                const isEdit = $('#userId').val();
                showAlert(isEdit ? 'User updated successfully!' : 'User added successfully!', 'success');
                $('#userModal').modal('hide');
                loadUsers();
            } else {
                showAlert('Error: ' + (response.message || 'Save failed'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to save user: ' + errorMsg, 'error');
        })
        .always(function() {
            submitBtn.html(originalText).prop('disabled', false);
        });
}

/**
 * Delete user
 */
function deleteUser(id) {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }

    $.post('ajax/user_api.php', { action: 'delete', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                showAlert('User deleted successfully!', 'success');
                loadUsers();
            } else {
                showAlert('Error deleting user: ' + (response.message || 'Delete failed'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to delete user: ' + errorMsg, 'error');
        });
}

/**
 * Reset modal form to default state
 */
function resetModalForm() {
    $('#userForm input, #userForm select').prop('disabled', false);
    $('#saveUserBtn').show();
    $('#userModal .modal-title').text('Add User');
    $('#userPassword').attr('required', true);
}

/**
 * Show alert message
 */
function showAlert(message, type) {
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} mr-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top of content
    $('.content-wrapper .content').prepend(alert);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

/**
 * Extract error message from XHR object
 */
function getErrorMessage(xhr) {
    let message = xhr.responseText || 'Server error';
    try {
        const jsonResponse = JSON.parse(xhr.responseText || '{}');
        if (jsonResponse.message) {
            message = jsonResponse.message;
        }
    } catch (e) {
        // Keep the original message
    }
    return message;
}

/**
 * Format datetime for display
 */
function formatDateTime(dateString) {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    } catch (e) {
        return dateString;
    }
}

// Make functions available globally for onclick handlers
window.openAddUserModal = openAddUserModal;
window.editUserFromView = editUserFromView;
window.printUserDetails = printUserDetails;
window.selectAllUsers = selectAllUsers;
window.deselectAllUsers = deselectAllUsers;
window.bulkExportUsers = bulkExportUsers;
window.bulkDeleteUsers = bulkDeleteUsers;

// Ensure inline HTML onclick handlers can access these functions
window.viewUser = viewUser;
window.editUser = editUser;
window.deleteUser = deleteUser;

})();
