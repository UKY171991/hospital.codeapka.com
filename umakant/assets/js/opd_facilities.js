// OPD Facilities Management JavaScript
$(document).ready(function () {
    let facilitiesTable;

    // Initialize DataTable
    function initDataTable() {
        facilitiesTable = $('#opdFacilitiesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'opd_api/facilities.php',
                type: 'POST',
                cache: false,
                data: { action: 'list' }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'type' },
                { data: 'location' },
                { data: 'department_name' },
                { data: 'capacity' },
                {
                    data: 'is_available',
                    render: function (data) {
                        return data == 1 ? '<span class="badge badge-success">Available</span>' : '<span class="badge badge-warning">Occupied</span>';
                    }
                },
                {
                    data: 'is_active',
                    render: function (data) {
                        return data == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                    }
                },
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
            url: 'opd_api/facilities.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function (response) {
                if (response.success && response.data) {
                    $('#totalFacilities').text(response.data.total);
                    $('#availableFacilities').text(response.data.available);
                    $('#occupiedFacilities').text(response.data.occupied);
                    $('#totalCapacity').text(response.data.capacity);
                }
            }
        });
    }

    // Load departments for dropdown
    function loadDepartments() {
        $.ajax({
            url: 'opd_api/facilities.php',
            type: 'GET',
            data: { action: 'get_departments' },
            success: function (response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Department</option>';
                    response.data.forEach(function (dept) {
                        options += `<option value="${dept.id}">${dept.name}</option>`;
                    });
                    $('#department_id').html(options);
                }
            }
        });
    }

    // Add facility button
    $('#addFacilityBtn').click(function () {
        $('#facilityForm')[0].reset();
        $('#facilityId').val('');
        $('#modalTitle').text('Add New Facility');
        $('#facilityModal').modal('show');
    });

    // Edit facility
    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        $.ajax({
            url: 'opd_api/facilities.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success && response.data) {
                    const facility = response.data;
                    $('#facilityId').val(facility.id);
                    $('#name').val(facility.name);
                    $('#description').val(facility.description);
                    $('#type').val(facility.type);
                    $('#location').val(facility.location);
                    $('#capacity').val(facility.capacity);
                    $('#department_id').val(facility.department_id);
                    $('#is_available').val(facility.is_available);
                    $('#is_active').val(facility.is_active);
                    $('#modalTitle').text('Edit Facility');
                    $('#facilityModal').modal('show');
                }
            }
        });
    });

    // Save facility
    $('#facilityForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save';

        $.ajax({
            url: 'opd_api/facilities.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#facilityModal').modal('hide');
                    facilitiesTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Delete facility
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this facility?')) {
            $.ajax({
                url: 'opd_api/facilities.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        facilitiesTable.ajax.reload();
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
