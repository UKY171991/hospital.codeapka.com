// Enhanced User Management with AJAX and Toaster Alerts
let userTableManager;
let selectedUsers = new Set();

$(document).ready(function() {
    // Initialize enhanced table manager
    userTableManager = new EnhancedTableManager({
        tableSelector: '#usersTable',
        apiEndpoint: 'ajax/user_api.php',
        entityName: 'user',
        entityNamePlural: 'users',
        viewFields: ['id', 'username', 'email', 'full_name', 'role', 'is_active', 'expire_date', 'created_at']
    });
    
    bindUserEvents();
});

function bindUserEvents() {
    // Individual selection
    $(document).on('change', '.user-checkbox', function() {
        const userId = $(this).val();
        const isChecked = $(this).is(':checked');
        
        if (isChecked) {
            selectedUsers.add(userId);
            $(this).closest('tr').addClass('row-selected');
        } else {
            selectedUsers.delete(userId);
            $(this).closest('tr').removeClass('row-selected');
        }
        
        updateBulkActions();
    });
    
    // Form submission
    $('#saveUserBtn').on('click', function(e) {
        e.preventDefault();
        saveUser();
    });
    
    // Search functionality
    $('#usersSearch').on('input', function() {
        applyFilters();
    });
    
    $('#usersSearchClear').on('click', function() {
        $('#usersSearch').val('');
        applyFilters();
    });
}

function selectAllUsers() {
    $('.user-checkbox').prop('checked', true).trigger('change');
    showInfo('All users selected');
}

function deselectAllUsers() {
    $('.user-checkbox').prop('checked', false).trigger('change');
    selectedUsers.clear();
    $('.row-selected').removeClass('row-selected');
    updateBulkActions();
    showInfo('All users deselected');
}

function updateBulkActions() {
    const selectedCount = selectedUsers.size;
    const bulkActions = $('.bulk-actions');
    
    if (selectedCount > 0) {
        bulkActions.addClass('show');
        bulkActions.find('.selected-count').text(selectedCount);
    } else {
        bulkActions.removeClass('show');
    }
}

function openAddUserModal() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#userModalLabel').text('Add User');
    $('#userModal').modal('show');
}

function viewUser(id) {
    showLoading();
    
    $.get('ajax/user_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                displayUserDetails(response.data);
                $('#viewUserModal').modal('show');
                showSuccess('User details loaded');
            } else {
                showError('Failed to load user details: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error loading user details');
        })
        .always(function() {
            hideLoading();
        });
}

function displayUserDetails(user) {
    const detailsHtml = `
        <div class="detail-item">
            <div class="detail-label">User ID</div>
            <div class="detail-value">${user.id || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Username</div>
            <div class="detail-value">${user.username || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Full Name</div>
            <div class="detail-value">${user.full_name || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Email</div>
            <div class="detail-value">
                ${user.email ? `<a href="mailto:${user.email}">${user.email}</a>` : 'N/A'}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Role</div>
            <div class="detail-value">
                <span class="status-badge ${user.role === 'master' ? 'status-completed' : user.role === 'admin' ? 'status-active' : 'status-pending'}">
                    ${user.role || 'N/A'}
                </span>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Status</div>
            <div class="detail-value">
                <span class="status-badge ${user.is_active == 1 ? 'status-active' : 'status-inactive'}">
                    ${user.is_active == 1 ? 'Active' : 'Inactive'}
                </span>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Expire Date</div>
            <div class="detail-value">${user.expire_date ? new Date(user.expire_date).toLocaleDateString() : 'Never'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Created Date</div>
            <div class="detail-value">${user.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Last Login</div>
            <div class="detail-value">${user.last_login ? new Date(user.last_login).toLocaleDateString() : 'Never'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Added By</div>
            <div class="detail-value">${user.added_by_name || user.added_by || 'System'}</div>
        </div>
    `;
    
    $('#userViewDetails').html(detailsHtml);
    
    // Store user ID for edit function
    $('#viewUserModal').data('user-id', user.id);
}

function editUser(id) {
    showLoading();
    
    $.get('ajax/user_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                populateUserForm(response.data);
                $('#userModalLabel').text('Edit User');
                $('#userModal').modal('show');
                showSuccess('User data loaded for editing');
            } else {
                showError('Failed to load user data: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error loading user data');
        })
        .always(function() {
            hideLoading();
        });
}

function editUserFromView() {
    const userId = $('#viewUserModal').data('user-id');
    $('#viewUserModal').modal('hide');
    setTimeout(() => editUser(userId), 300);
}

function populateUserForm(user) {
    $('#userId').val(user.id);
    $('#userUsername').val(user.username);
    $('#userFullName').val(user.full_name);
    $('#userEmail').val(user.email);
    $('#userRole').val(user.role);
    $('#userIsActive').val(user.is_active);
    
    if (user.expire_date) {
        const expireDate = new Date(user.expire_date);
        const formattedDate = expireDate.toISOString().slice(0, 16);
        $('#userExpireDateDisplay').val(formattedDate);
        $('#userExpireDate').val(user.expire_date);
    }
    
    // Clear password field for editing
    $('#userPassword').val('').prop('required', false);
}

function saveUser() {
    const formData = new FormData($('#userForm')[0]);
    formData.append('action', 'save');
    
    // Convert expire date
    const expireDateDisplay = $('#userExpireDateDisplay').val();
    if (expireDateDisplay) {
        $('#userExpireDate').val(expireDateDisplay);
    }
    
    const isEdit = $('#userId').val() !== '';
    
    showLoading();
    
    $.ajax({
        url: 'ajax/user_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            $('#userModal').modal('hide');
            userTableManager.refreshData();
            
            const message = isEdit ? 'User updated successfully' : 'User added successfully';
            showSuccess(message);
            
            // Reset form
            $('#userForm')[0].reset();
            $('#userId').val('');
            $('#userPassword').prop('required', true);
        } else {
            showError('Failed to save user: ' + response.message);
        }
    })
    .fail(function() {
        showError('Error saving user');
    })
    .always(function() {
        hideLoading();
    });
}

function deleteUser(id) {
    showConfirmDialog(
        'Delete User',
        'Are you sure you want to delete this user? This action cannot be undone.',
        'danger'
    ).then(function(confirmed) {
        if (confirmed) {
            showLoading();
            
            $.post('ajax/user_api.php', {action: 'delete', id: id})
                .done(function(response) {
                    if (response.success) {
                        userTableManager.refreshData();
                        showSuccess('User deleted successfully');
                    } else {
                        showError('Failed to delete user: ' + response.message);
                    }
                })
                .fail(function() {
                    showError('Error deleting user');
                })
                .always(function() {
                    hideLoading();
                });
        }
    });
}

function bulkDeleteUsers() {
    if (selectedUsers.size === 0) {
        showWarning('Please select users to delete');
        return;
    }
    
    const selectedCount = selectedUsers.size;
    showConfirmDialog(
        'Bulk Delete',
        `Are you sure you want to delete ${selectedCount} selected users? This action cannot be undone.`,
        'danger'
    ).then(function(confirmed) {
        if (confirmed) {
            const ids = Array.from(selectedUsers);
            showLoading();
            
            $.post('ajax/user_api.php', {action: 'bulk_delete', ids: ids})
                .done(function(response) {
                    if (response.success) {
                        selectedUsers.clear();
                        updateBulkActions();
                        userTableManager.refreshData();
                        showSuccess(`${selectedCount} users deleted successfully`);
                    } else {
                        showError('Failed to delete users: ' + response.message);
                    }
                })
                .fail(function() {
                    showError('Error deleting users');
                })
                .always(function() {
                    hideLoading();
                });
        }
    });
}

function bulkExportUsers() {
    if (selectedUsers.size === 0) {
        showWarning('Please select users to export');
        return;
    }
    
    const ids = Array.from(selectedUsers);
    showLoading();
    
    $.get('ajax/user_api.php', {action: 'bulk_export', ids: ids})
        .done(function(response) {
            if (response.success) {
                downloadCSV(response.data, 'selected_users.csv');
                showSuccess('Users exported successfully');
            } else {
                showError('Failed to export users: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error exporting users');
        })
        .always(function() {
            hideLoading();
        });
}

function exportUsers() {
    showLoading();
    
    $.get('ajax/user_api.php', {action: 'export'})
        .done(function(response) {
            if (response.success) {
                downloadCSV(response.data, 'all_users.csv');
                showSuccess('All users exported successfully');
            } else {
                showError('Failed to export users: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error exporting users');
        })
        .always(function() {
            hideLoading();
        });
}

function refreshUsers() {
    userTableManager.refreshData();
    showInfo('User data refreshed');
}

function applyFilters() {
    const search = $('#usersSearch').val();
    
    // Apply search to DataTable
    userTableManager.dataTable
        .search(search)
        .draw();
}

function printUserDetails() {
    const userId = $('#viewUserModal').data('user-id');
    if (userId) {
        window.open(`print_user.php?id=${userId}`, '_blank');
    }
}

// Utility functions
function showLoading() {
    if ($('.loading-overlay').length === 0) {
        $('body').append('<div class="loading-overlay"><div class="spinner"></div></div>');
    }
}

function hideLoading() {
    $('.loading-overlay').remove();
}

function showSuccess(message) {
    toastr.success(message, 'Success', {
        timeOut: 3000,
        positionClass: 'toast-top-right',
        progressBar: true
    });
}

function showError(message) {
    toastr.error(message, 'Error', {
        timeOut: 5000,
        positionClass: 'toast-top-right',
        progressBar: true
    });
}

function showWarning(message) {
    toastr.warning(message, 'Warning', {
        timeOut: 4000,
        positionClass: 'toast-top-right',
        progressBar: true
    });
}

function showInfo(message) {
    toastr.info(message, 'Info', {
        timeOut: 3000,
        positionClass: 'toast-top-right',
        progressBar: true
    });
}

function showConfirmDialog(title, message, type = 'warning') {
    return new Promise((resolve) => {
        const modalId = 'confirmModal_' + Date.now();
        const typeClass = type === 'danger' ? 'btn-danger' : 'btn-warning';
        const iconClass = type === 'danger' ? 'fa-exclamation-triangle' : 'fa-question-circle';
        
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-${type} text-white">
                            <h5 class="modal-title">
                                <i class="fas ${iconClass} mr-2"></i>${title}
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn ${typeClass}" id="confirmBtn">
                                <i class="fas fa-check"></i> Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        $(`#${modalId}`).modal('show');
        
        $(`#${modalId} #confirmBtn`).on('click', function() {
            $(`#${modalId}`).modal('hide');
            resolve(true);
        });
        
        $(`#${modalId}`).on('hidden.bs.modal', function() {
            $(this).remove();
            resolve(false);
        });
    });
}

function downloadCSV(data, filename) {
    if (!data || data.length === 0) {
        showWarning('No data to export');
        return;
    }

    const headers = Object.keys(data[0]);
    let csv = headers.join(',') + '\n';
    
    data.forEach(row => {
        const values = headers.map(header => {
            const value = row[header] || '';
            return `"${String(value).replace(/"/g, '""')}"`;
        });
        csv += values.join(',') + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}
