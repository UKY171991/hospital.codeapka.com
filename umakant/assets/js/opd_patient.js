// OPD Patient Management JavaScript
$(document).ready(function() {
    let currentPatientName = null;
    let allPatients = [];

    // Load all data
    function loadAllData() {
        loadStats();
        loadDoctors();
        loadPatients();
    }

    // Load doctors for filter
    function loadDoctors() {
        $.ajax({
            url: 'opd_api/doctors.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success) {
                    // Get full list of doctors
                    $.ajax({
                        url: 'opd_api/patients.php',
                        type: 'GET',
                        data: { action: 'get_doctors' },
                        success: function(doctorResponse) {
                            if (doctorResponse.success && doctorResponse.data) {
                                const doctorSelect = $('#filterDoctor');
                                doctorSelect.empty();
                                doctorSelect.append('<option value="">All Doctors</option>');
                                
                                doctorResponse.data.forEach(function(doctor) {
                                    let displayText = doctor.name;
                                    if (doctor.specialization) {
                                        displayText += ' - ' + doctor.specialization;
                                    }
                                    doctorSelect.append(`<option value="${doctor.name}">${displayText}</option>`);
                                });
                            }
                        }
                    });
                }
            }
        });
    }

    // Load statistics
    function loadStats() {
        $.ajax({
            url: 'opd_api/patients.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success && response.data) {
                    $('#totalPatients').text(response.data.total);
                    $('#todayPatients').text(response.data.today);
                    $('#weekPatients').text(response.data.week);
                    $('#monthPatients').text(response.data.month);
                }
            },
            error: function() {
                console.error('Error loading stats');
            }
        });
    }

    // Load patients list
    function loadPatients(doctorFilter = '') {
        const data = { action: 'list' };
        if (doctorFilter) {
            data.doctor = doctorFilter;
        }
        
        $.ajax({
            url: 'opd_api/patients.php',
            type: 'GET',
            data: data,
            success: function(response) {
                if (response.success && response.data) {
                    allPatients = response.data;
                    renderPatientsTable(response.data);
                }
            },
            error: function() {
                $('#patientTableBody').html('<tr><td colspan="9" class="text-center text-danger">Error loading patients</td></tr>');
            }
        });
    }

    // Filter by doctor
    $('#filterDoctor').on('change', function() {
        const doctorName = $(this).val();
        loadPatients(doctorName);
    });

    // Render patients table
    function renderPatientsTable(patients) {
        let html = '';
        
        if (patients.length === 0) {
            html = '<tr><td colspan="9" class="text-center text-muted">No patients found</td></tr>';
        } else {
            patients.forEach(function(patient, index) {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><strong>${patient.patient_name || 'N/A'}</strong></td>
                        <td>${patient.patient_phone || 'N/A'}</td>
                        <td>${patient.patient_age || 'N/A'}</td>
                        <td>${patient.patient_gender || 'N/A'}</td>
                        <td><span class="badge badge-primary">${patient.visit_count || 0}</span></td>
                        <td>${patient.first_visit ? new Date(patient.first_visit).toLocaleDateString() : 'N/A'}</td>
                        <td>${patient.last_visit ? new Date(patient.last_visit).toLocaleDateString() : 'N/A'}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info view-history-btn" data-name="${patient.patient_name}" title="View History">
                                    <i class="fas fa-history"></i>
                                </button>
                                <a href="opd_reports.php" class="btn btn-sm btn-success" title="Add Report">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="opd_billing.php" class="btn btn-sm btn-warning" title="Create Bill">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        
        $('#patientTableBody').html(html);
    }

    // Search patients
    $('#searchPatient').on('keyup', function() {
        const query = $(this).val();
        
        if (query.length >= 2) {
            $.ajax({
                url: 'opd_api/patients.php',
                type: 'GET',
                data: { action: 'search', query: query },
                success: function(response) {
                    if (response.success && response.data) {
                        renderPatientsTable(response.data);
                    }
                }
            });
        } else if (query.length === 0) {
            loadPatients();
        }
    });

    // View patient history
    $(document).on('click', '.view-history-btn', function() {
        const patientName = $(this).data('name');
        currentPatientName = patientName;
        
        $.ajax({
            url: 'opd_api/patients.php',
            type: 'GET',
            data: { action: 'history', name: patientName },
            success: function(response) {
                if (response.success && response.data) {
                    renderPatientHistory(patientName, response.data);
                    $('#viewPatientModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Error loading patient history');
            }
        });
    });

    // Render patient history
    function renderPatientHistory(patientName, data) {
        const reports = data.reports || [];
        const bills = data.bills || [];
        
        let html = `
            <div class="patient-card">
                <h5><i class="fas fa-user-injured mr-2"></i>${patientName}</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Total Visits:</strong> ${reports.length}</p>
                        <p><strong>Total Bills:</strong> ${bills.length}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>First Visit:</strong> ${reports.length > 0 ? new Date(reports[reports.length - 1].report_date).toLocaleDateString() : 'N/A'}</p>
                        <p><strong>Last Visit:</strong> ${reports.length > 0 ? new Date(reports[0].report_date).toLocaleDateString() : 'N/A'}</p>
                    </div>
                </div>
            </div>

            <div class="history-section">
                <h6><i class="fas fa-file-medical mr-2"></i>Medical Reports (${reports.length})</h6>
                ${reports.length === 0 ? '<p class="text-muted">No medical reports found</p>' : ''}
                ${reports.map(report => `
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Report ID:</strong> #${report.id}</p>
                                    <p><strong>Date:</strong> ${new Date(report.report_date).toLocaleDateString()}</p>
                                    <p><strong>Doctor:</strong> ${report.doctor_name || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Diagnosis:</strong> ${report.diagnosis || 'N/A'}</p>
                                    <p><strong>Symptoms:</strong> ${report.symptoms || 'N/A'}</p>
                                    ${report.follow_up_date ? `<p><strong>Follow-up:</strong> ${new Date(report.follow_up_date).toLocaleDateString()}</p>` : ''}
                                </div>
                            </div>
                            ${report.prescription ? `<p><strong>Prescription:</strong> ${report.prescription}</p>` : ''}
                        </div>
                    </div>
                `).join('')}
            </div>

            <div class="history-section">
                <h6><i class="fas fa-file-invoice-dollar mr-2"></i>Billing History (${bills.length})</h6>
                ${bills.length === 0 ? '<p class="text-muted">No billing records found</p>' : ''}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Bill ID</th>
                                <th>Date</th>
                                <th>Doctor</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Balance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${bills.map(bill => {
                                let statusClass = 'secondary';
                                if (bill.payment_status === 'Paid') statusClass = 'success';
                                else if (bill.payment_status === 'Unpaid') statusClass = 'danger';
                                else if (bill.payment_status === 'Partial') statusClass = 'warning';
                                
                                return `
                                    <tr>
                                        <td>#${bill.id}</td>
                                        <td>${new Date(bill.bill_date).toLocaleDateString()}</td>
                                        <td>${bill.doctor_name || 'N/A'}</td>
                                        <td>₹${parseFloat(bill.total_amount).toFixed(2)}</td>
                                        <td>₹${parseFloat(bill.paid_amount).toFixed(2)}</td>
                                        <td>₹${parseFloat(bill.balance_amount).toFixed(2)}</td>
                                        <td><span class="badge badge-${statusClass}">${bill.payment_status}</span></td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        $('#patientHistoryContent').html(html);
    }

    // Print patient history
    window.printPatientHistory = function() {
        window.print();
    };

    // Initialize
    loadAllData();
});
