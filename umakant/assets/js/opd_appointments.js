// OPD Appointments Management JavaScript
$(document).ready(function() {
    let appointmentsTable;

    // Initialize DataTable
    function initDataTable() {
        appointmentsTable = $('#opdAppointmentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'opd_api/appointments.php',
                type: 'POST',
                data: { action: 'list' }
            },
            columns: [
                { data: 'id' },
                { data: 'appointment_number' },
                { data: 'patient_name' },
                { data: 'doctor_name' },
                { data: 'department_name' },
                { data: 'appointment_date' },
                { data: 'appointment_time' },
                { data: 'type_name' },
                { 
                    data: 'status',
                    render: function(data) {
                        const badges = {
                            'scheduled': 'badge-warning',
                            'confirmed': 'badge-primary',
                            'in_progress': 'badge-info',
                            'completed': 'badge-success',
                            'cancelled': 'badge-danger',
                            'no_show': 'badge-secondary'
                        };
                        return `<span class="badge ${badges[data] || 'badge-secondary'}">${data}</span>`;
                    }
                },
                { data: 'fee' },
                { 
                    data: 'payment_status',
                    render: function(data) {
                        const badges = {
                            'pending': 'badge-warning',
                            'paid': 'badge-success',
                            'cancelled': 'badge-danger'
                        };
                        return `<span class="badge ${badges[data] || 'badge-secondary'}">${data}</span>`;
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
            url: 'opd_api/appointments.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success && response.data) {
                    $('#totalAppointments').text(response.data.total);
                    $('#scheduledAppointments').text(response.data.scheduled);
                    $('#confirmedAppointments').text(response.data.confirmed);
                    $('#completedAppointments').text(response.data.completed);
                    $('#cancelledAppointments').text(response.data.cancelled);
                    $('#todayAppointments').text(response.data.today);
                }
            }
        });
    }

    // Load dropdowns
    function loadDropdowns() {
        // Load patients
        $.ajax({
            url: 'opd_api/appointments.php',
            type: 'GET',
            data: { action: 'get_patients' },
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Patient</option>';
                    response.data.forEach(function(patient) {
                        options += `<option value="${patient.id}">${patient.name} - ${patient.phone}</option>`;
                    });
                    $('#patient_id').html(options);
                }
            }
        });

        // Load doctors
        $.ajax({
            url: 'opd_api/appointments.php',
            type: 'GET',
            data: { action: 'get_doctors' },
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Doctor</option>';
                    response.data.forEach(function(doctor) {
                        options += `<option value="${doctor.id}">${doctor.name} - ${doctor.specialization || 'General'}</option>`;
                    });
                    $('#doctor_id').html(options);
                }
            }
        });

        // Load departments
        $.ajax({
            url: 'opd_api/appointments.php',
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

        // Load appointment types
        $.ajax({
            url: 'opd_api/appointments.php',
            type: 'GET',
            data: { action: 'get_appointment_types' },
            success: function(response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Type</option>';
                    response.data.forEach(function(type) {
                        options += `<option value="${type.id}">${type.name} (${type.duration_minutes} min)</option>`;
                    });
                    $('#appointment_type_id').html(options);
                }
            }
        });
    }

    // Add appointment button
    $('#addAppointmentBtn').click(function() {
        $('#appointmentForm')[0].reset();
        $('#appointmentId').val('');
        $('#modalTitle').text('Add New Appointment');
        $('#appointmentModal').modal('show');
    });

    // Edit appointment
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'opd_api/appointments.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const apt = response.data;
                    $('#appointmentId').val(apt.id);
                    $('#patient_id').val(apt.patient_id);
                    $('#doctor_id').val(apt.doctor_id);
                    $('#department_id').val(apt.department_id);
                    $('#appointment_date').val(apt.appointment_date);
                    $('#appointment_time').val(apt.appointment_time);
                    $('#appointment_type_id').val(apt.appointment_type_id);
                    $('#status').val(apt.status);
                    $('#reason').val(apt.reason);
                    $('#notes').val(apt.notes);
                    $('#fee').val(apt.fee);
                    $('#payment_status').val(apt.payment_status);
                    $('#modalTitle').text('Edit Appointment');
                    $('#appointmentModal').modal('show');
                }
            }
        });
    });

    // Save appointment
    $('#appointmentForm').submit(function(e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save';
        
        $.ajax({
            url: 'opd_api/appointments.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#appointmentModal').modal('hide');
                    appointmentsTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Delete appointment
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this appointment?')) {
            $.ajax({
                url: 'opd_api/appointments.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        appointmentsTable.ajax.reload();
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
    loadDropdowns();
});
