// OPD Departments Management JavaScript
$(document).ready(function() {
    let departmentsTable;

    // Initialize DataTable
    function initDataTable() {
        departmentsTable = $('#opdDepartmentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'opd_api/departments.php',
                type: 'POST',
                data: { action: 'list' }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'description' },
                { data: 'head_doctor_name' },
                { data: 'location' },
                { data: 'phone' },
                { data: 'email' },
                { 
                    data: 'is_active',
                    render: function(data) {
                        return data == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
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
            url: 'opd_api/departments.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success && response.data) {
                    $('#totalDepartments').text(response.data.total);
                    $('#activeDepartments').text(response.data.active);
                    $('#totalDoctors').text(response.data.doctors);
                    $('#totalSpecializations').text(response.data.specializations);
                }
            }
        });
    }

    // Load doctors for dropdown
    function loadDoctors() {
        $.ajax({
            url: 'opd_api/departments.php',
            type: 'GET',
            data: { action: 'get_doctors' },
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Doctor</option>';
                    response.data.forEach(function(doctor) {
                        options += `<option value="${doctor.id}">${doctor.name}</option>`;
                    });
                    $('#head_doctor_id').html(options);
                }
            }
        });
    }

    // Add department button
    $('#addDepartmentBtn').click(function() {
        $('#departmentForm')[0].reset();
        $('#departmentId').val('');
        $('#modalTitle').text('Add New Department');
        $('#departmentModal').modal('show');
    });

    // Edit department
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'opd_api/departments.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const dept = response.data;
                    $('#departmentId').val(dept.id);
                    $('#name').val(dept.name);
                    $('#description').val(dept.description);
                    $('#head_doctor_id').val(dept.head_doctor_id);
                    $('#location').val(dept.location);
                    $('#phone').val(dept.phone);
                    $('#email').val(dept.email);
                    $('#is_active').val(dept.is_active);
                    $('#modalTitle').text('Edit Department');
                    $('#departmentModal').modal('show');
                }
            }
        });
    });

    // Save department
    $('#departmentForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save';
        
        $.ajax({
            url: 'opd_api/departments.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#departmentModal').modal('hide');
                    departmentsTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Delete department
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this department?')) {
            $.ajax({
                url: 'opd_api/departments.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        departmentsTable.ajax.reload();
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
    loadDoctors();
});
