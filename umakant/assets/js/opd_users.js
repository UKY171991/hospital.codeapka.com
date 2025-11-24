// OPD Users Management JavaScript
$(document).ready(function() {
    let usersTable;

    // Initialize DataTable
    function initDataTable() {
        usersTable = $('#opdUsersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'opd_api/users.php',
                type: 'POST',
                data: { action: 'list' }
            },
            columns: [
                { data: 'id' },
                { data: 'username' },
                { data: 'name' },
                { data: 'email' },
                { data: 'phone' },
                { 
                    data: 'role',
                    render: function(data) {
                        const badges = {
                            'admin': 'badge-danger',
                            'doctor': 'badge-primary',
                            'nurse': 'badge-info',
                            'receptionist': 'badge-warning',
                            'patient': 'badge-secondary'
                        };
                        return `<span class="badge ${badges[data] || 'badge-secondary'}">${data}</span>`;
                    }
                },
                { 
                    data: 'is_active',
                    render: function(data) {
                        return data == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                    }
                },
                { data: 'created_at' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-info view-btn" data-id="${row.id}"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}"><i class="fas fa-trash"></i></button>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']]
        });
    }

    // Load statistics
    function loadStats() {
        $.ajax({
            url: 'opd_api/users.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success && response.data) {
                    $('#totalUsers').text(response.data.total);
                    $('#activeUsers').text(response.data.active);
                    $('#adminUsers').text(response.data.admin);
                    $('#doctorUsers').text(response.data.doctor);
                    $('#nurseUsers').text(response.data.nurse);
                    $('#patientUsers').text(response.data.patient);
                }
            }
        });
    }

    // Add user button
    $('#addUserBtn').click(function() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#modalTitle').text('Add New User');
        $('#passwordRequired').show();
        $('#password').prop('required', true);
        $('#userModal').modal('show');
    });

    // Edit user
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'opd_api/users.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const user = response.data;
                    $('#userId').val(user.id);
                    $('#username').val(user.username);
                    $('#email').val(user.email);
                    $('#name').val(user.name);
                    $('#phone').val(user.phone);
                    $('#role').val(user.role);
                    $('#specialization').val(user.specialization);
                    $('#license_number').val(user.license_number);
                    $('#is_active').val(user.is_active);
                    $('#password').val('');
                    $('#password').prop('required', false);
                    $('#passwordRequired').hide();
                    $('#modalTitle').text('Edit User');
                    $('#userModal').modal('show');
                }
            }
        });
    });

    // Save user
    $('#userForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save';
        
        $.ajax({
            url: 'opd_api/users.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#userModal').modal('hide');
                    usersTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Delete user
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: 'opd_api/users.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        usersTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

    // Initialize
    initDataTable();
    loadStats();
});
