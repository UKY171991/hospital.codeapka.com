// OPD Medical Records Management JavaScript
$(document).ready(function () {
    let recordsTable;

    // Initialize DataTable
    function initDataTable() {
        recordsTable = $('#opdMedicalRecordsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'opd_api/medical_records.php',
                type: 'POST',
                cache: false,
                data: { action: 'list' }
            },
            columns: [
                { data: 'record_date' },
                { data: 'patient_name' },
                { data: 'doctor_name' },
                {
                    data: 'diagnosis',
                    render: function (data) {
                        return data ? (data.length > 50 ? data.substring(0, 50) + '...' : data) : 'N/A';
                    }
                },

                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
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
            url: 'opd_api/medical_records.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function (response) {
                if (response.success && response.data) {
                    $('#totalRecords').text(response.data.total);
                    $('#todayRecords').text(response.data.today);
                    $('#weekRecords').text(response.data.week);
                    $('#totalPatients').text(response.data.patients);
                }
            }
        });
    }

    // Load dropdowns
    function loadDropdowns() {
        // Load patients
        $.ajax({
            url: 'opd_api/medical_records.php',
            type: 'GET',
            data: { action: 'get_patients' },
            success: function (response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Patient</option>';
                    response.data.forEach(function (patient) {
                        options += `<option value="${patient.id}">${patient.name} - ${patient.phone}</option>`;
                    });
                    $('#patient_id').html(options);
                }
            }
        });

        // Load doctors
        $.ajax({
            url: 'opd_api/medical_records.php',
            type: 'GET',
            data: { action: 'get_doctors' },
            success: function (response) {
                if (response.success && response.data) {
                    let options = '<option value="">Select Doctor</option>';
                    response.data.forEach(function (doctor) {
                        options += `<option value="${doctor.id}">${doctor.name} - ${doctor.specialization || 'General'}</option>`;
                    });
                    $('#doctor_id').html(options);
                }
            }
        });
    }

    // Load appointments when patient is selected
    $('#patient_id').change(function () {
        const patientId = $(this).val();
        if (patientId) {
            $.ajax({
                url: 'opd_api/medical_records.php',
                type: 'GET',
                data: { action: 'get_appointments', patient_id: patientId },
                success: function (response) {
                    if (response.success && response.data) {
                        let options = '<option value="">Select Appointment</option>';
                        response.data.forEach(function (apt) {
                            options += `<option value="${apt.id}">${apt.appointment_number} - ${apt.appointment_date}</option>`;
                        });
                        $('#appointment_id').html(options);
                    }
                }
            });
        }
    });

    // Add record button
    $('#addRecordBtn').click(function () {
        $('#recordForm')[0].reset();
        $('#recordId').val('');
        $('#modalTitle').text('Add New Medical Record');
        $('#recordModal').modal('show');
    });

    // Edit record
    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        $.ajax({
            url: 'opd_api/medical_records.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success && response.data) {
                    const record = response.data;
                    $('#recordId').val(record.id);
                    $('#patient_id').val(record.patient_id).trigger('change');
                    $('#doctor_id').val(record.doctor_id);
                    $('#appointment_id').val(record.appointment_id);
                    $('#record_date').val(record.record_date);
                    $('#symptoms').val(record.symptoms);
                    $('#diagnosis').val(record.diagnosis);
                    $('#treatment').val(record.treatment);
                    $('#prescription').val(record.prescription);
                    $('#notes').val(record.notes);
                    $('#modalTitle').text('Edit Medical Record');
                    $('#recordModal').modal('show');
                }
            }
        });
    });

    // Save record
    $('#recordForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save';

        $.ajax({
            url: 'opd_api/medical_records.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#recordModal').modal('hide');
                    recordsTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Delete record
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this medical record?')) {
            $.ajax({
                url: 'opd_api/medical_records.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        recordsTable.ajax.reload();
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
