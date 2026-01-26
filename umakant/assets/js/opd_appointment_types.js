// OPD Appointment Types Management JavaScript
$(document).ready(function () {
    let typesTable;

    // Initialize DataTable
    function initDataTable() {
        typesTable = $('#opdAppointmentTypesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'opd_api/appointment_types.php',
                type: 'POST',
                cache: false,
                data: { action: 'list' }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'description' },
                { data: 'duration_minutes' },
                {
                    data: 'color',
                    render: function (data) {
                        return `<span style="display:inline-block;width:30px;height:20px;background-color:${data};border:1px solid #ccc;"></span> ${data}`;
                    }
                },
                {
                    data: 'is_active',
                    render: function (data) {
                        return data == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                    }
                },
                { data: 'created_at' },
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
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
            url: 'opd_api/appointment_types.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function (response) {
                if (response.success && response.data) {
                    $('#totalTypes').text(response.data.total);
                    $('#activeTypes').text(response.data.active);
                    $('#totalAppointments').text(response.data.appointments);
                }
            }
        });
    }

    // Add type button
    $('#addTypeBtn').click(function () {
        $('#typeForm')[0].reset();
        $('#typeId').val('');
        $('#modalTitle').text('Add New Appointment Type');
        $('#typeModal').modal('show');
    });

    // Edit type
    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        $.ajax({
            url: 'opd_api/appointment_types.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success && response.data) {
                    const type = response.data;
                    $('#typeId').val(type.id);
                    $('#name').val(type.name);
                    $('#description').val(type.description);
                    $('#duration_minutes').val(type.duration_minutes);
                    $('#color').val(type.color);
                    $('#is_active').val(type.is_active);
                    $('#modalTitle').text('Edit Appointment Type');
                    $('#typeModal').modal('show');
                }
            }
        });
    });

    // Save type
    $('#typeForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save';

        $.ajax({
            url: 'opd_api/appointment_types.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#typeModal').modal('hide');
                    typesTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Delete type
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this appointment type?')) {
            $.ajax({
                url: 'opd_api/appointment_types.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        typesTable.ajax.reload();
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
