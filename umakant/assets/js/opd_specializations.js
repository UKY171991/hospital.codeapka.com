// OPD Specializations Management JavaScript
$(document).ready(function() {
    let specializationsTable;

    // Initialize DataTable
    function initDataTable() {
        specializationsTable = $('#opdSpecializationsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'opd_api/specializations.php',
                type: 'POST',
                data: { action: 'list' }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'description' },
                { data: 'department_name' },
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
            url: 'opd_api/specializations.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success && response.data) {
                    $('#totalSpecializations').text(response.data.total);
                    $('#activeSpecializations').text(response.data.active);
                    $('#totalDoctors').text(response.data.doctors);
                }
            }
        });
    }

    // Load departments for dropdown
    function loadDepartments() {
        $.ajax({
            url: 'opd_api/specializations.php',
            type: 'GET',
            data: { action: 'get_departments' },
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Department</option>';
                    response.data.forEach(function(dept) {
                        options += `<option value="${dept.id}">${dept.name}</option>`;
                    });
                    $('#department_id').html(options);
                }
            }
        });
    }

    // Add specialization button
    $('#addSpecializationBtn').click(function() {
        $('#specializationForm')[0].reset();
        $('#specializationId').val('');
        $('#modalTitle').text('Add New Specialization');
        $('#specializationModal').modal('show');
    });

    // Edit specialization
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'opd_api/specializations.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const spec = response.data;
                    $('#specializationId').val(spec.id);
                    $('#name').val(spec.name);
                    $('#description').val(spec.description);
                    $('#department_id').val(spec.department_id);
                    $('#is_active').val(spec.is_active);
                    $('#modalTitle').text('Edit Specialization');
                    $('#specializationModal').modal('show');
                }
            }
        });
    });

    // Save specialization
    $('#specializationForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save';
        
        $.ajax({
            url: 'opd_api/specializations.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#specializationModal').modal('hide');
                    specializationsTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Delete specialization
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this specialization?')) {
            $.ajax({
                url: 'opd_api/specializations.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        specializationsTable.ajax.reload();
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
    loadDepartments();
});
