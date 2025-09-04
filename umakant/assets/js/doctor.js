// Enhanced Doctor Management JavaScript

$(document).ready(function() {
    initializeDoctorPage();
});

function initializeDoctorPage() {
    loadDoctors();
    loadStats();
    initializeDataTable();
    setupEventListeners();
    utils.initTooltips();
}

function setupEventListeners() {
    // Search functionality
    $('#searchDoctor').on('input', utils.debounce(function() {
        const searchTerm = $(this).val();
        filterDoctors(searchTerm);
    }, 300));

    // Specialization filter
    $('#specializationFilter').on('change', function() {
        const specialization = $(this).val();
        filterDoctorsBySpecialization(specialization);
    });

    // Export functionality
    $('#exportDoctors').on('click', function() {
        utils.exportTableToCSV('#doctorsTable', 'doctors-export.csv');
    });

    // Form submission
    $('#doctorForm').on('submit', function(e) {
        e.preventDefault();
        saveDoctorWithValidation();
    });
}

function initializeDataTable() {
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#doctorsTable')) {
        $('#doctorsTable').DataTable().destroy();
    }
    
    $('#doctorsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']],
        columnDefs: [
            {
                targets: [0, -1],
                orderable: false
            }
        ],
        language: {
            search: "Search doctors:",
            lengthMenu: "Show _MENU_ doctors per page",
            info: "Showing _START_ to _END_ of _TOTAL_ doctors",
            infoEmpty: "No doctors found",
            infoFiltered: "(filtered from _MAX_ total doctors)"
        },
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
    });
}

function loadDoctors() {
    utils.showLoading('#doctorsTableContainer');
    
    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateDoctorsTable(response.data || []);
                loadSpecializationFilter(response.data || []);
            } else {
                utils.showError('Failed to load doctors: ' + (response.message || 'Unknown error'));
                $('#doctorsTableContainer').html('<div class="alert alert-danger">Failed to load doctors</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading doctors:', error);
            utils.showError('Error loading doctors: ' + error);
            $('#doctorsTableContainer').html('<div class="alert alert-danger">Error loading doctors</div>');
        }
    });
}

function populateDoctorsTable(doctors) {
    let html = `
        <div class="table-responsive">
            <table id="doctorsTable" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Avatar</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Hospital</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Experience</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (doctors.length === 0) {
        html += `
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-user-md fa-3x mb-3 d-block"></i>
                            No doctors found. <a href="#" onclick="openAddDoctorModal()" class="text-primary">Add the first doctor</a>
                        </td>
                    </tr>
        `;
    } else {
        doctors.forEach(doctor => {
            const avatar = utils.generateAvatar(doctor.name, 'bg-info');
            const statusBadge = doctor.status === 'active' 
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-secondary">Inactive</span>';
            
            html += `
                <tr>
                    <td>${avatar}</td>
                    <td>
                        <strong>${doctor.name || 'N/A'}</strong>
                        ${doctor.designation ? `<br><small class="text-muted">${doctor.designation}</small>` : ''}
                    </td>
                    <td>
                        <span class="badge badge-primary">${doctor.specialization || 'General'}</span>
                    </td>
                    <td>${doctor.hospital || 'N/A'}</td>
                    <td>
                        ${doctor.phone ? `<i class="fas fa-phone text-success"></i> ${doctor.phone}<br>` : ''}
                        ${doctor.mobile ? `<i class="fas fa-mobile text-info"></i> ${doctor.mobile}` : ''}
                    </td>
                    <td>
                        ${doctor.email ? `<a href="mailto:${doctor.email}" class="text-primary">${doctor.email}</a>` : 'N/A'}
                    </td>
                    <td>
                        ${doctor.experience ? `${doctor.experience} years` : 'N/A'}
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-info btn-sm" onclick="viewDoctor(${doctor.id})" title="View Details" data-toggle="tooltip">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editDoctor(${doctor.id})" title="Edit" data-toggle="tooltip">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteDoctor(${doctor.id})" title="Delete" data-toggle="tooltip">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }

    html += `
                </tbody>
            </table>
        </div>
    `;

    $('#doctorsTableContainer').html(html);
    initializeDataTable();
    utils.initTooltips();
}

function loadSpecializationFilter(doctors) {
    const specializations = [...new Set(doctors.map(d => d.specialization).filter(s => s))];
    
    let options = '<option value="">All Specializations</option>';
    specializations.forEach(spec => {
        options += `<option value="${spec}">${spec}</option>`;
    });
    
    $('#specializationFilter').html(options);
}

function loadStats() {
    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'GET',
        data: { action: 'stats' },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const stats = response.data;
                $('#totalDoctors').text(stats.total || 0);
                $('#activeDoctors').text(stats.active || 0);
                $('#specializations').text(stats.specializations || 0);
                $('#hospitals').text(stats.hospitals || 0);
            }
        },
        error: function() {
            console.warn('Could not load doctor statistics');
        }
    });
}

function openAddDoctorModal() {
    $('#doctorModalLabel').text('Add New Doctor');
    $('#doctorForm')[0].reset();
    $('#doctorId').val('');
    $('#doctorModal').modal('show');
}

function editDoctor(id) {
    utils.showLoading('#doctorModal .modal-body');
    $('#doctorModal').modal('show');
    
    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'GET',
        data: { action: 'get', id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const doctor = response.data;
                $('#doctorModalLabel').text('Edit Doctor');
                $('#doctorId').val(doctor.id);
                $('#doctorName').val(doctor.name);
                $('#doctorSpecialization').val(doctor.specialization);
                $('#doctorDesignation').val(doctor.designation);
                $('#doctorHospital').val(doctor.hospital);
                $('#doctorPhone').val(doctor.phone);
                $('#doctorMobile').val(doctor.mobile);
                $('#doctorEmail').val(doctor.email);
                $('#doctorAddress').val(doctor.address);
                $('#doctorExperience').val(doctor.experience);
                $('#doctorQualification').val(doctor.qualification);
                $('#doctorStatus').val(doctor.status || 'active');
            } else {
                utils.showError('Failed to load doctor details');
                $('#doctorModal').modal('hide');
            }
        },
        error: function() {
            utils.showError('Error loading doctor details');
            $('#doctorModal').modal('hide');
        },
        complete: function() {
            utils.hideLoading('#doctorModal .modal-body');
        }
    });
}

function saveDoctorWithValidation() {
    const formData = {
        id: $('#doctorId').val(),
        name: $('#doctorName').val(),
        specialization: $('#doctorSpecialization').val(),
        designation: $('#doctorDesignation').val(),
        hospital: $('#doctorHospital').val(),
        phone: $('#doctorPhone').val(),
        mobile: $('#doctorMobile').val(),
        email: $('#doctorEmail').val(),
        address: $('#doctorAddress').val(),
        experience: $('#doctorExperience').val(),
        qualification: $('#doctorQualification').val(),
        status: $('#doctorStatus').val()
    };

    // Validation rules
    const rules = {
        name: { required: true, label: 'Doctor Name', minLength: 2 },
        specialization: { required: true, label: 'Specialization' },
        email: { type: 'email', label: 'Email Address' },
        phone: { type: 'phone', label: 'Phone Number' },
        mobile: { type: 'phone', label: 'Mobile Number' }
    };

    const errors = utils.validateForm(formData, rules);
    
    if (errors.length > 0) {
        utils.showError('Please fix the following errors:<br>• ' + errors.join('<br>• '));
        return;
    }

    saveDoctor(formData);
}

function saveDoctor(formData) {
    const action = formData.id ? 'update' : 'create';
    
    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'POST',
        data: {
            action: action,
            ...formData
        },
        dataType: 'json',
        beforeSend: function() {
            $('#saveDoctorBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        },
        success: function(response) {
            if (response.success) {
                utils.showSuccess(formData.id ? 'Doctor updated successfully!' : 'Doctor added successfully!');
                $('#doctorModal').modal('hide');
                loadDoctors();
                loadStats();
            } else {
                utils.showError('Failed to save doctor: ' + (response.message || 'Unknown error'));
            }
        },
        error: function(xhr, status, error) {
            utils.showError('Error saving doctor: ' + error);
        },
        complete: function() {
            $('#saveDoctorBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Doctor');
        }
    });
}

function deleteDoctor(id) {
    utils.confirm(
        'Are you sure you want to delete this doctor? This action cannot be undone.',
        'Delete Doctor'
    ).then(confirmed => {
        if (confirmed) {
            $.ajax({
                url: 'ajax/doctor_api.php',
                method: 'POST',
                data: { action: 'delete', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        utils.showSuccess('Doctor deleted successfully!');
                        loadDoctors();
                        loadStats();
                    } else {
                        utils.showError('Failed to delete doctor: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function() {
                    utils.showError('Error deleting doctor');
                }
            });
        }
    });
}

function viewDoctor(id) {
    // Implementation for viewing doctor details in a modal
    utils.showInfo('View doctor functionality will be implemented here');
}

function filterDoctors(searchTerm) {
    const table = $('#doctorsTable').DataTable();
    table.search(searchTerm).draw();
}

function filterDoctorsBySpecialization(specialization) {
    const table = $('#doctorsTable').DataTable();
    if (specialization) {
        table.column(2).search(specialization).draw();
    } else {
        table.column(2).search('').draw();
    }
}
        ...(specialization && { specialization: specialization }),
        ...(hospital && { hospital: hospital })
    });

    $.get(`patho_api/doctor.php?${params}`)
        .done(function(response) {
            if (response.status === 'success') {
                populateDoctorsTable(response.data);
                updatePagination(response.pagination);
                updateStats();
            } else {
                showAlert('Error loading doctors: ' + response.message, 'error');
            }
        })
        .fail(function() {
            // Fallback to legacy API
            $.get('ajax/doctor_api.php', {action: 'list'}, function(resp) {
                if (resp.success) {
                    populateLegacyDoctorsTable(resp.data);
                } else {
                    toastr.error('Failed to load doctors');
                }
            }, 'json');
        });
}

function populateDoctorsTable(doctors) {
    let html = '';
    
    if (doctors.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted">No doctors found</td></tr>';
    } else {
        doctors.forEach(doctor => {
            html += `
                <tr>
                    <td>${doctor.id}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary text-white mr-2">
                                ${doctor.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-weight-bold">${doctor.name}</div>
                                ${doctor.registration_no ? `<small class="text-muted">Reg: ${doctor.registration_no}</small>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${doctor.qualification || '-'}</td>
                    <td>
                        ${doctor.specialization ? `<span class="badge badge-info">${doctor.specialization}</span>` : '-'}
                    </td>
                    <td>${doctor.hospital || '-'}</td>
                    <td>
                        <div>
                            ${doctor.contact_no ? `<div><i class="fas fa-phone text-primary"></i> ${doctor.contact_no}</div>` : ''}
                            ${doctor.email ? `<div><i class="fas fa-envelope text-info"></i> ${doctor.email}</div>` : ''}
                        </div>
                    </td>
                    <td>
                        ${doctor.percent ? `<span class="badge badge-success">${doctor.percent}%</span>` : '-'}
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm" onclick="viewDoctor(${doctor.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editDoctor(${doctor.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteDoctor(${doctor.id}, '${doctor.name}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#doctorsTableBody').html(html);
}

function populateLegacyDoctorsTable(doctors) {
    let html = '';
    doctors.forEach(function(r) {
        html += '<tr>' +
            '<td>' + r.id + '</td>' +
            '<td>' + (r.name || '') + '</td>' +
            '<td>' + (r.qualification || '') + '</td>' +
            '<td>' + (r.specialization || '') + '</td>' +
            '<td>' + (r.hospital || '') + '</td>' +
            '<td>' + (r.contact_no || r.phone || '') + '</td>' +
            '<td>' + (r.percent || '') + '</td>' +
            '<td>' + (r.email || '') + '</td>' +
            '<td><button class="btn btn-sm btn-info view-doctor" data-id="' + r.id + '">View</button> ' +
            '<button class="btn btn-sm btn-warning edit-doctor" data-id="' + r.id + '">Edit</button> ' +
            '<button class="btn btn-sm btn-danger delete-doctor" data-id="' + r.id + '">Delete</button></td>' +
            '</tr>';
    });
    $('#doctorsTable tbody').html(html);
}

function updatePagination(pagination) {
    totalRecords = pagination.total;
    const totalPages = Math.ceil(totalRecords / recordsPerPage);
    
    // Update info
    const start = ((currentPage - 1) * recordsPerPage) + 1;
    const end = Math.min(currentPage * recordsPerPage, totalRecords);
    $('#doctorsInfo').html(`Showing ${start} to ${end} of ${totalRecords} entries`);
    
    // Update pagination
    let paginationHtml = '';
    if (totalPages > 1) {
        paginationHtml += `
            <ul class="pagination pagination-sm m-0 float-right">
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
                </li>
        `;
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        paginationHtml += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
                </li>
            </ul>
        `;
    }
    
    $('#doctorsPagination').html(paginationHtml);
}

function updateStats() {
    $.get('patho_api/doctor.php?action=stats')
        .done(function(response) {
            if (response.status === 'success') {
                $('#totalDoctors').text(response.data.total || 0);
                $('#activeDoctors').text(response.data.active || 0);
                $('#specializations').text(response.data.specializations || 0);
                $('#hospitals').text(response.data.hospitals || 0);
            }
        });
}

function loadFilters() {
    // Load specializations
    $.get('patho_api/doctor.php?action=specializations')
        .done(function(response) {
            if (response.status === 'success') {
                let options = '<option value="">All Specializations</option>';
                response.data.forEach(spec => {
                    options += `<option value="${spec}">${spec}</option>`;
                });
                $('#specializationFilter').html(options);
            }
        });

    // Load hospitals
    $.get('patho_api/doctor.php?action=hospitals')
        .done(function(response) {
            if (response.status === 'success') {
                let options = '<option value="">All Hospitals</option>';
                response.data.forEach(hospital => {
                    options += `<option value="${hospital}">${hospital}</option>`;
                });
                $('#hospitalFilter').html(options);
            }
        });
}

function changePage(page) {
    currentPage = page;
    loadDoctors();
}

function clearFilters() {
    $('#doctorsSearch').val('');
    $('#specializationFilter').val('');
    $('#hospitalFilter').val('');
    currentPage = 1;
    loadDoctors();
}

function openAddDoctorModal() {
    $('#doctorForm')[0].reset();
    $('#doctorId').val('');
    $('#modalTitle').text('Add New Doctor');
    $('#doctorModal').modal('show');
}

function editDoctor(id) {
    $.get(`patho_api/doctor.php?id=${id}`)
        .done(function(response) {
            if (response.status === 'success') {
                const doctor = response.data;
                populateDoctorForm(doctor);
                $('#modalTitle').text('Edit Doctor');
                $('#doctorModal').modal('show');
            } else {
                showAlert('Error loading doctor data: ' + response.message, 'error');
            }
        })
        .fail(function() {
            // Fallback to legacy API
            $.get('ajax/doctor_api.php', {action: 'get', id: id}, function(resp) {
                if (resp.success) {
                    populateDoctorForm(resp.data);
                    $('#doctorModal').modal('show');
                } else {
                    toastr.error('Doctor not found');
                }
            }, 'json');
        });
}

function populateDoctorForm(doctor) {
    $('#doctorId').val(doctor.id);
    $('#doctorName').val(doctor.name);
    $('#doctorQualification').val(doctor.qualification);
    $('#doctorSpecialization').val(doctor.specialization);
    $('#doctorHospital').val(doctor.hospital);
    $('#doctorContact').val(doctor.contact_no);
    $('#doctorPhone').val(doctor.phone);
    $('#doctorEmail').val(doctor.email);
    $('#doctorPercent').val(doctor.percent);
    $('#doctorRegistration').val(doctor.registration_no);
    $('#doctorAddress').val(doctor.address);
}

function viewDoctor(id) {
    $.get(`patho_api/doctor.php?id=${id}`)
        .done(function(response) {
            if (response.status === 'success') {
                const doctor = response.data;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td><strong>Name:</strong></td><td>${doctor.name}</td></tr>
                                <tr><td><strong>Qualification:</strong></td><td>${doctor.qualification || '-'}</td></tr>
                                <tr><td><strong>Specialization:</strong></td><td>${doctor.specialization || '-'}</td></tr>
                                <tr><td><strong>Hospital:</strong></td><td>${doctor.hospital || '-'}</td></tr>
                                <tr><td><strong>Registration No:</strong></td><td>${doctor.registration_no || '-'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td><strong>Contact:</strong></td><td>${doctor.contact_no || '-'}</td></tr>
                                <tr><td><strong>Phone:</strong></td><td>${doctor.phone || '-'}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${doctor.email || '-'}</td></tr>
                                <tr><td><strong>Commission:</strong></td><td>${doctor.percent ? doctor.percent + '%' : '-'}</td></tr>
                                <tr><td><strong>Created:</strong></td><td>${formatDateTime(doctor.created_at)}</td></tr>
                            </table>
                        </div>
                    </div>
                    ${doctor.address ? `<div class="row"><div class="col-12"><strong>Address:</strong><br>${doctor.address}</div></div>` : ''}
                `;
                $('#viewDoctorContent').html(content);
                $('#viewDoctorModal').modal('show');
            } else {
                showAlert('Error loading doctor data: ' + response.message, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load doctor data', 'error');
        });
}

function saveDoctorData() {
    const formData = new FormData($('#doctorForm')[0]);
    const id = $('#doctorId').val();
    const method = id ? 'PUT' : 'POST';
    
    // Add loading state
    const submitBtn = $('#doctorForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: 'patho_api/doctor.php',
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                showAlert(id ? 'Doctor updated successfully!' : 'Doctor added successfully!', 'success');
                $('#doctorModal').modal('hide');
                loadDoctors();
            } else {
                showAlert('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showAlert('Failed to save doctor data', 'error');
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function deleteDoctor(id, name) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete doctor "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                performDeleteDoctor(id);
            }
        });
    } else {
        // Fallback to confirm if SweetAlert is not available
        if (confirm(`Delete doctor "${name}"?`)) {
            performDeleteDoctor(id);
        }
    }
}

function performDeleteDoctor(id) {
    $.ajax({
        url: `patho_api/doctor.php?id=${id}`,
        type: 'DELETE',
        success: function(response) {
            if (response.status === 'success') {
                showAlert('Doctor deleted successfully!', 'success');
                loadDoctors();
            } else {
                showAlert('Error deleting doctor: ' + response.message, 'error');
            }
        },
        error: function() {
            // Fallback to legacy API
            $.post('ajax/doctor_api.php', {action: 'delete', id: id}, function(resp) {
                if (resp.success) {
                    toastr.success(resp.message);
                    loadDoctors();
                } else {
                    toastr.error(resp.message || 'Delete failed');
                }
            }, 'json');
        }
    });
}

function showAlert(message, type) {
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} mr-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top of content
    $('.content-wrapper .content').prepend(alert);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

// Legacy jQuery ready function for backward compatibility
$(function() {
    loadDoctors();
    
    $('#saveDoctorBtn').click(function() {
        var data = $('#doctorForm').serialize() + '&action=save';
        $.post('ajax/doctor_api.php', data, function(resp) {
            if (resp.success) {
                toastr.success(resp.message || 'Saved');
                $('#doctorModal').modal('hide');
                loadDoctors();
            } else {
                toastr.error(resp.message || 'Save failed');
            }
        }, 'json').fail(function(xhr) {
            var msg = xhr.responseText || 'Server error';
            try {
                var j = JSON.parse(xhr.responseText || '{}');
                if (j.message) msg = j.message;
            } catch (e) {}
            toastr.error(msg);
        });
    });

    $('#doctorsTable').on('click', '.edit-doctor', function() {
        var id = $(this).data('id');
        $.get('ajax/doctor_api.php', {action: 'get', id: id}, function(resp) {
            if (resp.success) {
                populateDoctorForm(resp.data);
                $('#doctorModal').modal('show');
            } else {
                toastr.error('Doctor not found');
            }
        }, 'json');
    });

    $('#doctorsTable').on('click', '.delete-doctor', function() {
        if (!confirm('Delete doctor?')) return;
        var id = $(this).data('id');
        $.post('ajax/doctor_api.php', {action: 'delete', id: id}, function(resp) {
            if (resp.success) {
                toastr.success(resp.message);
                loadDoctors();
            } else {
                toastr.error(resp.message || 'Delete failed');
            }
        }, 'json');
    });
});
