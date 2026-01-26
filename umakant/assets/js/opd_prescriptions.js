// OPD Prescriptions Management JavaScript
$(document).ready(function () {
    let prescriptionsTable;

    // Initialize DataTable
    function initDataTable() {
        prescriptionsTable = $('#opdPrescriptionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'opd_api/prescriptions.php',
                type: 'POST',
                cache: false,
                data: { action: 'list' }
            },
            columns: [
                { data: 'prescription_date' },
                { data: 'patient_name' },
                { data: 'doctor_name' },
                {
                    data: 'medications',
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
                            <button class="btn btn-sm btn-primary print-btn" data-id="${row.id}"><i class="fas fa-print"></i></button>
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
            url: 'opd_api/prescriptions.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function (response) {
                if (response.success && response.data) {
                    $('#totalPrescriptions').text(response.data.total);
                    $('#todayPrescriptions').text(response.data.today);
                    $('#weekPrescriptions').text(response.data.week);
                    $('#totalPatients').text(response.data.patients);
                }
            }
        });
    }

    // Load dropdowns
    function loadDropdowns() {
        // Load patients
        $.ajax({
            url: 'opd_api/prescriptions.php',
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
            url: 'opd_api/prescriptions.php',
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
                url: 'opd_api/prescriptions.php',
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

    // Add prescription button
    $('#addPrescriptionBtn').click(function () {
        $('#prescriptionForm')[0].reset();
        $('#prescriptionId').val('');
        $('#modalTitle').text('Add New Prescription');
        $('#printPrescriptionBtn').hide();
        $('#prescriptionModal').modal('show');
    });

    // Edit prescription
    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        $.ajax({
            url: 'opd_api/prescriptions.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success && response.data) {
                    const prescription = response.data;
                    $('#prescriptionId').val(prescription.id);
                    $('#patient_id').val(prescription.patient_id).trigger('change');
                    $('#doctor_id').val(prescription.doctor_id);
                    $('#appointment_id').val(prescription.appointment_id);
                    $('#prescription_date').val(prescription.prescription_date);
                    $('#medications').val(prescription.medications);
                    $('#dosage').val(prescription.dosage);
                    $('#instructions').val(prescription.instructions);
                    $('#duration').val(prescription.duration);
                    $('#notes').val(prescription.notes);
                    $('#modalTitle').text('Edit Prescription');
                    $('#printPrescriptionBtn').show();
                    $('#prescriptionModal').modal('show');
                }
            }
        });
    });

    // Save prescription
    $('#prescriptionForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save';

        $.ajax({
            url: 'opd_api/prescriptions.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#prescriptionModal').modal('hide');
                    prescriptionsTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Delete prescription
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this prescription?')) {
            $.ajax({
                url: 'opd_api/prescriptions.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        prescriptionsTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        }
    });

    // Print prescription
    $(document).on('click', '.print-btn', function () {
        const id = $(this).data('id');
        window.open('opd_api/print_prescription.php?id=' + id, '_blank');
    });

    // Initialize
    initDataTable();
    loadStats();
    loadDropdowns();
});
