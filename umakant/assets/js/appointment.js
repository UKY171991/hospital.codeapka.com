// OPD Appointment Management JavaScript
$(document).ready(function() {
    let appointmentTable;
    let currentAppointmentId = null;

    // Initialize DataTable
    function initDataTable() {
        appointmentTable = $('#appointmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'ajax/appointment_api.php',
                type: 'GET',
                data: function(d) {
                    d.action = 'list';
                },
                dataSrc: function(json) {
                    console.log('API Response:', json);
                    return json.data;
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable error:', error, thrown);
                    console.error('Response:', xhr.responseText);
                    toastr.error('Error loading data');
                }
            },
            columns: [
                { 
                    data: null,
                    render: function(data, type, row, meta) {
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
                    data: 'patient_name',
                    width: '150px'
                },
                { 
                    data: 'doctor_name',
                    width: '150px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'department',
                    width: '120px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'appointment_date',
                    width: '120px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : 'N/A';
                    }
                },
                { 
                    data: 'time_slot',
                    width: '130px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'patient_contact',
                    width: '120px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'status',
                    width: '100px',
                    render: function(data, type, row) {
                        if (!data) data = 'Pending';
                        let statusClass = 'secondary';
                        let statusIcon = 'clock';
                        
                        switch(data) {
                            case 'Confirmed':
                                statusClass = 'success';
                                statusIcon = 'check-circle';
                                break;
                            case 'Completed':
                                statusClass = 'info';
                                statusIcon = 'check-double';
                                break;
                            case 'Cancelled':
                                statusClass = 'danger';
                                statusIcon = 'times-circle';
                                break;
                            case 'Pending':
                                statusClass = 'warning';
                                statusIcon = 'clock';
                                break;
                        }
                        
                        return `<span class="badge badge-${statusClass}"><i class="fas fa-${statusIcon}"></i> ${data}</span>`;
                    },
                    defaultContent: '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>'
                },
                { 
                    data: 'added_by_username',
                    width: '100px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'created_at',
                    width: '100px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : '';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    width: '150px',
                    render: function(data, type, row) {
                        let statusBtn = '';
                        if (row.status === 'Pending') {
                            statusBtn = `<button class="btn btn-sm btn-success confirm-btn" data-id="${row.id}" title="Confirm"><i class="fas fa-check"></i></button>`;
                        } else if (row.status === 'Confirmed') {
                            statusBtn = `<button class="btn btn-sm btn-info complete-btn" data-id="${row.id}" title="Complete"><i class="fas fa-check-double"></i></button>`;
                        }
                        
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info view-btn" data-id="${row.id}" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${statusBtn}
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
                { targets: [2, 3, 4], className: 'text-left' }
            ]
        });
    }

    // Load statistics
    function loadStats() {
        $.ajax({
            url: 'ajax/appointment_api.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success) {
                    $('#totalAppointments').text(response.data.total);
                    $('#pendingAppointments').text(response.data.pending);
                    $('#confirmedAppointments').text(response.data.confirmed);
                    $('#cancelledAppointments').text(response.data.cancelled);
                }
            },
            error: function() {
                console.error('Error loading stats');
            }
        });
    }

    // Load departments
    function loadDepartments() {
        $.ajax({
            url: 'ajax/appointment_api.php',
            type: 'GET',
            data: { action: 'get_departments' },
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Department</option>';
                    response.data.forEach(function(dept) {
                        options += `<option value="${dept.name}">${dept.name}</option>`;
                    });
                    $('#departmentName').html(options);
                }
            }
        });
    }

    // Load doctors
    function loadDoctors() {
        $.ajax({
            url: 'ajax/appointment_api.php',
            type: 'GET',
            data: { action: 'get_doctors' },
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Doctor</option>';
                    response.data.forEach(function(doctor) {
                        options += `<option value="${doctor.name}">${doctor.name}</option>`;
                    });
                    $('#doctorName').html(options);
                }
            }
        });
    }

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#appointmentDate').attr('min', today);

    // Add new appointment button
    $('#addAppointmentBtn').click(function() {
        currentAppointmentId = null;
        $('#appointmentForm')[0].reset();
        $('#appointmentId').val('');
        $('#modalTitle').text('Add New OPD Appointment');
        $('#appointmentModal').modal('show');
    });

    // Form submission
    $('#appointmentForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize() + '&action=save';
        
        $.ajax({
            url: 'ajax/appointment_api.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#appointmentModal').modal('hide');
                    appointmentTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message || 'Error saving appointment');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'Error saving appointment');
            }
        });
    });

    // View appointment
    $(document).on('click', '.view-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/appointment_api.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const apt = response.data;
                    let statusClass = 'secondary';
                    
                    switch(apt.status) {
                        case 'Confirmed': statusClass = 'success'; break;
                        case 'Completed': statusClass = 'info'; break;
                        case 'Cancelled': statusClass = 'danger'; break;
                        case 'Pending': statusClass = 'warning'; break;
                    }
                    
                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary">Patient Information</h5>
                                <p><strong>Name:</strong> ${apt.patient_name || ''}</p>
                                <p><strong>Contact:</strong> ${apt.patient_contact || 'N/A'}</p>
                                <p><strong>Email:</strong> ${apt.patient_email || 'N/A'}</p>
                                <p><strong>Age:</strong> ${apt.patient_age || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-primary">Appointment Details</h5>
                                <p><strong>Doctor:</strong> ${apt.doctor_name || 'N/A'}</p>
                                <p><strong>Department:</strong> ${apt.department || 'N/A'}</p>
                                <p><strong>Date:</strong> ${apt.appointment_date ? new Date(apt.appointment_date).toLocaleDateString() : 'N/A'}</p>
                                <p><strong>Time:</strong> ${apt.time_slot || 'N/A'}</p>
                                <p><strong>Status:</strong> <span class="badge badge-${statusClass}">${apt.status || 'N/A'}</span></p>
                            </div>
                            <div class="col-md-12 mt-3">
                                <h5 class="text-primary">Additional Information</h5>
                                <p><strong>Reason:</strong> ${apt.reason || 'N/A'}</p>
                                <p><strong>Notes:</strong> ${apt.notes || 'N/A'}</p>
                                <p><strong>Added By:</strong> ${apt.added_by_username || 'N/A'}</p>
                                <p><strong>Created:</strong> ${apt.created_at ? new Date(apt.created_at).toLocaleString() : 'N/A'}</p>
                            </div>
                        </div>
                    `;
                    $('#viewAppointmentContent').html(html);
                    currentAppointmentId = id;
                    $('#viewAppointmentModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Error loading appointment details');
            }
        });
    });

    // Edit appointment
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/appointment_api.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const apt = response.data;
                    $('#appointmentId').val(apt.id);
                    $('#patientName').val(apt.patient_name);
                    $('#patientContact').val(apt.patient_contact);
                    $('#patientEmail').val(apt.patient_email);
                    $('#patientAge').val(apt.patient_age);
                    $('#departmentName').val(apt.department);
                    $('#doctorName').val(apt.doctor_name);
                    $('#appointmentDate').val(apt.appointment_date);
                    $('#timeSlot').val(apt.time_slot);
                    $('#appointmentReason').val(apt.reason);
                    $('#appointmentNotes').val(apt.notes);
                    $('#appointmentStatus').val(apt.status || 'Pending');
                    $('#modalTitle').text('Edit OPD Appointment');
                    $('#appointmentModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Error loading appointment details');
            }
        });
    });

    // Edit from view modal
    window.editAppointmentFromView = function() {
        if (currentAppointmentId) {
            $('#viewAppointmentModal').modal('hide');
            $('.edit-btn[data-id="' + currentAppointmentId + '"]').click();
        }
    };

    // Confirm appointment
    $(document).on('click', '.confirm-btn', function() {
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to confirm this appointment?')) {
            $.ajax({
                url: 'ajax/appointment_api.php',
                type: 'POST',
                data: { action: 'update_status', id: id, status: 'Confirmed' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        appointmentTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message || 'Error confirming appointment');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Error confirming appointment');
                }
            });
        }
    });

    // Complete appointment
    $(document).on('click', '.complete-btn', function() {
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to mark this appointment as completed?')) {
            $.ajax({
                url: 'ajax/appointment_api.php',
                type: 'POST',
                data: { action: 'update_status', id: id, status: 'Completed' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        appointmentTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message || 'Error completing appointment');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Error completing appointment');
                }
            });
        }
    });

    // Delete appointment
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this appointment?')) {
            $.ajax({
                url: 'ajax/appointment_api.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        appointmentTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message || 'Error deleting appointment');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Error deleting appointment');
                }
            });
        }
    });

    // Print appointment
    window.printAppointment = function() {
        window.print();
    };

    // Initialize
    initDataTable();
    loadStats();
    loadDepartments();
    loadDoctors();
});
