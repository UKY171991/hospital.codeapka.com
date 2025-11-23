// OPD Reports Management JavaScript
$(document).ready(function() {
    let opdReportsTable;
    let currentReportId = null;

    // Set today's date as default
    $('#reportDate').val(new Date().toISOString().split('T')[0]);

    // Load doctors list
    function loadDoctors(callback) {
        $.ajax({
            url: 'ajax/opd_reports_api.php',
            type: 'GET',
            data: { action: 'get_doctors' },
            success: function(response) {
                if (response.success && response.data) {
                    const doctorSelect = $('#doctorName');
                    doctorSelect.empty();
                    doctorSelect.append('<option value="">Select Doctor</option>');
                    
                    response.data.forEach(function(doctor) {
                        let displayText = doctor.name;
                        if (doctor.specialization) {
                            displayText += ' - ' + doctor.specialization;
                        }
                        if (doctor.hospital) {
                            displayText += ' (' + doctor.hospital + ')';
                        }
                        doctorSelect.append(`<option value="${doctor.name}">${displayText}</option>`);
                    });
                    
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            },
            error: function() {
                console.error('Error loading doctors');
                toastr.error('Error loading doctors list');
            }
        });
    }

    // Initialize DataTable
    function initDataTable() {
        opdReportsTable = $('#opdReportsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'ajax/opd_reports_api.php',
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
                    width: '70px'
                },
                { 
                    data: 'patient_name',
                    width: '150px'
                },
                { 
                    data: 'patient_phone',
                    width: '110px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'doctor_name',
                    width: '130px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'report_date',
                    width: '100px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : '';
                    }
                },
                { 
                    data: 'diagnosis',
                    width: '200px',
                    render: function(data) {
                        if (!data) return 'N/A';
                        return data.length > 50 ? data.substring(0, 50) + '...' : data;
                    },
                    defaultContent: 'N/A'
                },
                { 
                    data: 'follow_up_date',
                    width: '100px',
                    render: function(data) {
                        if (!data) return '<span class="badge badge-secondary">None</span>';
                        const followUpDate = new Date(data);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        
                        if (followUpDate < today) {
                            return '<span class="badge badge-danger">' + followUpDate.toLocaleDateString() + '</span>';
                        } else if (followUpDate.toDateString() === today.toDateString()) {
                            return '<span class="badge badge-warning">Today</span>';
                        } else {
                            return '<span class="badge badge-success">' + followUpDate.toLocaleDateString() + '</span>';
                        }
                    }
                },
                { 
                    data: 'added_by_username',
                    width: '100px',
                    defaultContent: 'N/A'
                },
                {
                    data: null,
                    orderable: false,
                    width: '120px',
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info view-btn" data-id="${row.id}" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}" title="Edit">
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
                { targets: [2, 4, 6], className: 'text-left' }
            ]
        });
    }

    // Load statistics
    function loadStats() {
        $.ajax({
            url: 'ajax/opd_reports_api.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success) {
                    $('#totalReports').text(response.data.total);
                    $('#todayReports').text(response.data.today);
                    $('#weekReports').text(response.data.week);
                    $('#monthReports').text(response.data.month);
                }
            },
            error: function() {
                console.error('Error loading stats');
            }
        });
    }

    // Add new report button
    $('#addReportBtn').click(function() {
        currentReportId = null;
        $('#reportForm')[0].reset();
        $('#reportId').val('');
        $('#reportDate').val(new Date().toISOString().split('T')[0]);
        $('#modalTitle').text('Add New Report');
        loadDoctors();
        $('#reportModal').modal('show');
    });

    // Form submission
    $('#reportForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize() + '&action=save';
        
        $.ajax({
            url: 'ajax/opd_reports_api.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#reportModal').modal('hide');
                    opdReportsTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message || 'Error saving report');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'Error saving report');
            }
        });
    });

    // View report
    $(document).on('click', '.view-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/opd_reports_api.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const report = response.data;
                    
                    let html = `
                        <div class="report-view p-3">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h4 class="text-center mb-3">Medical Report #${report.id}</h4>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user-injured mr-2"></i>Patient Information</h5>
                                    <p><strong>Name:</strong> ${report.patient_name || 'N/A'}</p>
                                    <p><strong>Phone:</strong> ${report.patient_phone || 'N/A'}</p>
                                    <p><strong>Age:</strong> ${report.patient_age || 'N/A'}</p>
                                    <p><strong>Gender:</strong> ${report.patient_gender || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-user-md mr-2"></i>Doctor & Dates</h5>
                                    <p><strong>Doctor:</strong> ${report.doctor_name || 'N/A'}</p>
                                    <p><strong>Report Date:</strong> ${report.report_date ? new Date(report.report_date).toLocaleDateString() : 'N/A'}</p>
                                    <p><strong>Follow-up Date:</strong> ${report.follow_up_date ? new Date(report.follow_up_date).toLocaleDateString() : 'Not scheduled'}</p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-notes-medical mr-2"></i>Symptoms</h5>
                                    <p>${report.symptoms || 'No symptoms recorded'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-diagnoses mr-2"></i>Diagnosis</h5>
                                    <p>${report.diagnosis || 'No diagnosis recorded'}</p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-flask mr-2"></i>Test Results</h5>
                                    <p>${report.test_results || 'No test results available'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-pills mr-2"></i>Prescription</h5>
                                    <p>${report.prescription || 'No prescription given'}</p>
                                </div>
                            </div>
                            
                            ${report.notes ? `
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5><i class="fas fa-sticky-note mr-2"></i>Additional Notes</h5>
                                    <p>${report.notes}</p>
                                </div>
                            </div>
                            ` : ''}
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-muted"><small>Added by: ${report.added_by_username || 'N/A'} on ${report.created_at ? new Date(report.created_at).toLocaleString() : 'N/A'}</small></p>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#viewReportContent').html(html);
                    currentReportId = id;
                    $('#viewReportModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Error loading report details');
            }
        });
    });

    // Edit report
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/opd_reports_api.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const report = response.data;
                    
                    loadDoctors(function() {
                        $('#reportId').val(report.id);
                        $('#patientName').val(report.patient_name);
                        $('#patientPhone').val(report.patient_phone);
                        $('#patientAge').val(report.patient_age);
                        $('#patientGender').val(report.patient_gender);
                        $('#doctorName').val(report.doctor_name);
                        $('#reportDate').val(report.report_date);
                        $('#followUpDate').val(report.follow_up_date);
                        $('#symptoms').val(report.symptoms);
                        $('#diagnosis').val(report.diagnosis);
                        $('#testResults').val(report.test_results);
                        $('#prescription').val(report.prescription);
                        $('#notes').val(report.notes);
                        $('#modalTitle').text('Edit Report');
                    });
                    
                    $('#reportModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Error loading report details');
            }
        });
    });

    // Edit from view modal
    window.editReportFromView = function() {
        if (currentReportId) {
            $('#viewReportModal').modal('hide');
            $('.edit-btn[data-id="' + currentReportId + '"]').click();
        }
    };

    // Delete report
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this report?')) {
            $.ajax({
                url: 'ajax/opd_reports_api.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        opdReportsTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message || 'Error deleting report');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Error deleting report');
                }
            });
        }
    });

    // Print report details
    window.printReportDetails = function() {
        window.print();
    };

    // Initialize
    initDataTable();
    loadStats();
    loadDoctors();
});
