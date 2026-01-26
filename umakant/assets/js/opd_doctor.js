// OPD Doctor Management JavaScript
$(document).ready(function () {
    let opdDoctorTable;
    let currentDoctorId = null;

    // Attach event handlers (called after table redraw)
    function attachEventHandlers() {
        // Event handlers are already attached via $(document).on() so they persist
    }

    // Initialize DataTable
    function initDataTable() {
        opdDoctorTable = $('#opdDoctorTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'opd_api/doctors.php',
                type: 'POST',
                cache: false,
                data: function (d) {
                    d.action = 'list';
                },
                dataSrc: function (json) {
                    console.log('API Response:', json);
                    return json.data;
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTable error:', error, thrown);
                    console.error('Response:', xhr.responseText);
                    toastr.error('Error loading data');
                }
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    width: '50px'
                },
                {
                    data: 'id',
                    width: '60px'
                },
                {
                    data: 'name',
                    width: '150px'
                },
                {
                    data: 'qualification',
                    width: '120px',
                    defaultContent: 'N/A'
                },
                {
                    data: 'specialization',
                    width: '120px',
                    defaultContent: 'N/A'
                },
                {
                    data: 'hospital',
                    width: '120px',
                    defaultContent: 'N/A'
                },
                {
                    data: 'contact_no',
                    width: '110px',
                    defaultContent: 'N/A'
                },
                {
                    data: 'phone',
                    width: '110px',
                    defaultContent: 'N/A'
                },
                {
                    data: 'email',
                    width: '150px',
                    defaultContent: 'N/A'
                },
                {
                    data: 'registration_no',
                    width: '100px',
                    defaultContent: 'N/A'
                },
                {
                    data: 'status',
                    width: '120px',
                    render: function (data, type, row) {
                        if (!data) data = 'Active';
                        if (data === 'Active') {
                            return `<button class="btn btn-success btn-sm toggle-status-btn" data-id="${row.id}" title="Click to Deactivate">
                                        <i class="fas fa-toggle-on"></i> Active
                                    </button>`;
                        } else {
                            return `<button class="btn btn-secondary btn-sm toggle-status-btn" data-id="${row.id}" title="Click to Activate">
                                        <i class="fas fa-toggle-off"></i> Inactive
                                    </button>`;
                        }
                    },
                    defaultContent: '<button class="btn btn-success btn-sm toggle-status-btn" title="Click to Deactivate"><i class="fas fa-toggle-on"></i> Active</button>'
                },
                {
                    data: 'added_by_username',
                    width: '100px',
                    defaultContent: 'N/A'
                },
                {
                    data: 'created_at',
                    width: '100px',
                    render: function (data) {
                        return data ? new Date(data).toLocaleDateString() : '';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    width: '120px',
                    render: function (data, type, row) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info view-btn" data-id="${row.id}" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, 'desc']],
            pageLength: 25,
            scrollX: true,
            autoWidth: false,
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading...'
            },
            columnDefs: [
                { targets: '_all', className: 'text-center' },
                { targets: [2], className: 'text-left' },
                { targets: [8], className: 'text-left' }
            ],
            drawCallback: function () {
                // Re-attach event handlers after table redraw
                attachEventHandlers();
            }
        });
    }

    // Load statistics
    function loadStats() {
        $.ajax({
            url: 'opd_api/doctors.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function (response) {
                if (response.success) {
                    $('#totalDoctors').text(response.data.total);
                    $('#activeDoctors').text(response.data.active);
                    $('#inactiveDoctors').text(response.data.inactive);
                    $('#specializations').text(response.data.specializations);
                    $('#hospitals').text(response.data.hospitals);
                }
            },
            error: function () {
                console.error('Error loading stats');
            }
        });
    }

    // Add new doctor button
    $('#addDoctorBtn').click(function () {
        currentDoctorId = null;
        $('#doctorForm')[0].reset();
        $('#doctorId').val('');
        $('#modalTitle').text('Add New OPD Doctor');
        $('#doctorModal').modal('show');
    });

    // Form submission
    $('#doctorForm').submit(function (e) {
        e.preventDefault();

        const formData = $(this).serialize() + '&action=save';

        $.ajax({
            url: 'opd_api/doctors.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#doctorModal').modal('hide');
                    opdDoctorTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message || 'Error saving doctor');
                }
            },
            error: function (xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'Error saving doctor');
            }
        });
    });

    // View doctor
    $(document).on('click', '.view-btn', function () {
        const id = $(this).data('id');

        $.ajax({
            url: 'opd_api/doctors.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success && response.data) {
                    const doctor = response.data;
                    const statusClass = doctor.status === 'Active' ? 'success' : 'danger';
                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> ${doctor.name || ''}</p>
                                <p><strong>Qualification:</strong> ${doctor.qualification || 'N/A'}</p>
                                <p><strong>Specialization:</strong> ${doctor.specialization || 'N/A'}</p>
                                <p><strong>Hospital:</strong> ${doctor.hospital || 'N/A'}</p>
                                <p><strong>Contact No:</strong> ${doctor.contact_no || 'N/A'}</p>
                                <p><strong>Status:</strong> <span class="badge badge-${statusClass}">${doctor.status || 'N/A'}</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Phone:</strong> ${doctor.phone || 'N/A'}</p>
                                <p><strong>Email:</strong> ${doctor.email || 'N/A'}</p>
                                <p><strong>Registration No:</strong> ${doctor.registration_no || 'N/A'}</p>
                                <p><strong>Address:</strong> ${doctor.address || 'N/A'}</p>
                                <p><strong>Added By:</strong> ${doctor.added_by_username || 'N/A'}</p>
                            </div>
                        </div>
                    `;
                    $('#viewDoctorContent').html(html);
                    currentDoctorId = id;
                    $('#viewDoctorModal').modal('show');
                }
            },
            error: function () {
                toastr.error('Error loading doctor details');
            }
        });
    });

    // Edit doctor
    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');

        $.ajax({
            url: 'opd_api/doctors.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success && response.data) {
                    const doctor = response.data;
                    $('#doctorId').val(doctor.id);
                    $('#doctorName').val(doctor.name);
                    $('#doctorQualification').val(doctor.qualification);
                    $('#doctorSpecialization').val(doctor.specialization);
                    $('#doctorHospital').val(doctor.hospital);
                    $('#doctorContact').val(doctor.contact_no);
                    $('#doctorPhone').val(doctor.phone);
                    $('#doctorEmail').val(doctor.email);
                    $('#doctorRegistration').val(doctor.registration_no);
                    $('#doctorAddress').val(doctor.address);
                    $('#doctorStatus').val(doctor.status || 'Active');
                    $('#modalTitle').text('Edit OPD Doctor');
                    $('#doctorModal').modal('show');
                }
            },
            error: function () {
                toastr.error('Error loading doctor details');
            }
        });
    });

    // Edit from view modal
    window.editDoctorFromView = function () {
        if (currentDoctorId) {
            $('#viewDoctorModal').modal('hide');
            $('.edit-btn[data-id="' + currentDoctorId + '"]').click();
        }
    };

    // Delete doctor
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');

        if (confirm('Are you sure you want to delete this OPD doctor?')) {
            $.ajax({
                url: 'opd_api/doctors.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        opdDoctorTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message || 'Error deleting doctor');
                    }
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Error deleting doctor');
                }
            });
        }
    });

    // Toggle status
    $(document).on('click', '.toggle-status-btn', function () {
        const id = $(this).data('id');
        const $btn = $(this);

        if (confirm('Are you sure you want to change the status of this doctor?')) {
            // Disable button during request
            $btn.prop('disabled', true);

            $.ajax({
                url: 'opd_api/doctors.php',
                type: 'POST',
                data: { action: 'toggle_status', id: id },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        // Force page reload to refresh the table
                        setTimeout(function () {
                            location.reload();
                        }, 500);
                    } else {
                        toastr.error(response.message || 'Error updating status');
                        $btn.prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Error updating status');
                    $btn.prop('disabled', false);
                }
            });
        }
    });

    // Print doctor details
    window.printDoctorDetails = function () {
        window.print();
    };

    // Initialize
    initDataTable();
    loadStats();
});
