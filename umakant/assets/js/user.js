/**
 * User Management JavaScript
 * Handles CRUD operations for user management interface
 */

// Global variables
let currentPage = 1;
let totalRecords = 0;
let recordsPerPage = 10;
let searchTimeout;

// Initialize page when document is ready
$(document).ready(function() {
    loadUsers();
    initializeEventListeners();
});

/**
 * Initialize all event listeners
 */
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
        html = '<tr><td colspan="8" class="text-center text-muted">No users found</td></tr>';
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
                    <td class="${expireDateClass}">${expireDateDisplay || '-'}</td>
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
 * View user (read-only)
 */
function viewUser(id) {
    $.get('ajax/user_api.php', { action: 'get', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const user = response.data;
                populateUserForm(user);
                
                // Disable all form inputs
                $('#userForm input, #userForm select').prop('disabled', true);
                $('#userModal .modal-title').text('View User');
                $('#saveUserBtn').hide();
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
