// Enhanced Doctor Management JavaScript

// Global variables for pagination and filtering
let currentPage = 1;
let recordsPerPage = 10; // Default records per page
let totalRecords = 0; // To be updated by API response

$(document).ready(function() {
    initializeDoctorPage();
});

function initializeDoctorPage() {
    loadDoctors();
    loadStats();
    loadFilters(); // Load specializations and hospitals for filters
    setupEventListeners();
    utils.initTooltips();
}

function setupEventListeners() {
    // Search functionality
    $('#doctorsSearch').on('input', utils.debounce(function() {
        currentPage = 1; // Reset page on search
        loadDoctors();
    }, 300));

    // Specialization filter
    $('#specializationFilter').on('change', function() {
        currentPage = 1; // Reset page on filter change
        loadDoctors();
    });

    // Hospital filter
    $('#hospitalFilter').on('change', function() {
        currentPage = 1; // Reset page on filter change
        loadDoctors();
    });

    // Records per page change
    $('#doctorsPerPage').on('change', function() {
        recordsPerPage = parseInt($(this).val());
        currentPage = 1; // Reset page on records per page change
        loadDoctors();
    });

    // Clear filters button
    $('#doctorsSearchClear').on('click', function() {
        clearFilters();
    });

    // Form submission for Add/Edit Doctor Modal
    $('#doctorForm').on('submit', function(e) {
        e.preventDefault();
        saveDoctorData();
    });

    // Click handler for "Add New Doctor" button
    $('.card-header').on('click', 'button[data-target="#doctorModal"]', function() {
        openAddDoctorModal();
    });
}

// Removed initializeDataTable as the table is now dynamically populated and managed by populateDoctorsTable and updatePagination.
// If DataTables functionality is still desired, it would need to be re-initialized *after* populateDoctorsTable has rendered the table,
// and its server-server-side processing features would need to be integrated with the patho_api/doctor.php's pagination and filtering.
// For now, I've removed the DataTables initialization to simplify and remove potential conflicts.

function loadDoctors() {
    utils.showLoading('#doctorsTableBody');

    const searchTerm = $('#doctorsSearch').val();
    const specialization = $('#specializationFilter').val();
    const hospital = $('#hospitalFilter').val();

    const params = new URLSearchParams({
        action: 'list',
        page: currentPage,
        limit: recordsPerPage,
        ...(searchTerm && { search: searchTerm }),
        ...(specialization && { specialization: specialization }),
        ...(hospital && { hospital: hospital })
    });

    $.get(`patho_api/doctor.php?${params}`)
        .done(function(response) {
            if (response.success) {
                populateDoctorsTable(response.data);
                updatePagination(response.pagination);
            } else {
                showAlert('Error loading doctors: ' + response.message, 'error');
                $('#doctorsTableBody').html('<tr><td colspan="8" class="text-center text-muted">Failed to load doctors.</td></tr>');
            }
        })
        .fail(function() {
            showAlert('Failed to load doctors data from API.', 'error');
            $('#doctorsTableBody').html('<tr><td colspan="8" class="text-center text-muted">Failed to load doctors.</td></tr>');
        })
        .always(function() {
            utils.hideLoading('#doctorsTableBody');
        });
}

function populateDoctorsTable(doctors) {
    let html = '';

    if (doctors.length === 0) {
        html = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-user-md fa-3x mb-3 d-block"></i>
                    No doctors found. <a href="#" onclick="openAddDoctorModal()" class="text-primary">Add the first doctor</a>
                </td>
            </tr>
        `;
    } else {
        doctors.forEach(doctor => {
            const avatar = utils.generateAvatar(doctor.name, 'bg-info');
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

function loadStats() {
    $.get('patho_api/doctor.php?action=stats')
        .done(function(response) {
            if (response.success) {
                const stats = response.data;
                $('#totalDoctors').text(stats.total || 0);
                $('#activeDoctors').text(stats.active || 0);
                $('#specializations').text(stats.specializations || 0);
                $('#hospitals').text(stats.hospitals || 0);
            }
        })
        .fail(function() {
            console.warn('Could not load doctor statistics');
        });
}

function loadFilters() {
    // Load specializations
    $.get('patho_api/doctor.php?action=specializations')
        .done(function(response) {
            if (response.success) {
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
            if (response.success) {
                let options = '<option value="">All Hospitals</option>';
                response.data.forEach(hospital => {
                    options += `<option value="${hospital}">${hospital}</option>`;
                });
                $('#hospitalFilter').html(options);
            }
        });
}

function changePage(page) {
    if (page < 1) page = 1;
    const totalPages = Math.ceil(totalRecords / recordsPerPage); // totalRecords needs to be global or passed
    if (page > totalPages) page = totalPages;
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
    utils.showLoading('#doctorModal .modal-body');
    $('#doctorModal').modal('show');

    $.get(`patho_api/doctor.php?action=get&id=${id}`)
        .done(function(response) {
            if (response.success) {
                const doctor = response.data;
                populateDoctorForm(doctor);
                $('#modalTitle').text('Edit Doctor');
            }
            else {
                showAlert('Error loading doctor data: ' + response.message, 'error');
                $('#doctorModal').modal('hide');
            }
        })
        .fail(function() {
            showAlert('Failed to load doctor data for editing.', 'error');
            $('#doctorModal').modal('hide');
        })
        .always(function() {
            utils.hideLoading('#doctorModal .modal-body');
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
    utils.showLoading('#viewDoctorModal .modal-body');
    $('#viewDoctorModal').modal('show');

    $.get(`patho_api/doctor.php?action=get&id=${id}`)
        .done(function(response) {
            if (response.success) {
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
            }
            else {
                showAlert('Error loading doctor data: ' + response.message, 'error');
                $('#viewDoctorModal').modal('hide');
            }
        })
        .fail(function() {
            showAlert('Failed to load doctor data for viewing.', 'error');
            $('#viewDoctorModal').modal('hide');
        })
        .always(function() {
            utils.hideLoading('#viewDoctorModal .modal-body');
        });
}

function saveDoctorData() {
    const formData = new FormData($('#doctorForm')[0]);
    const id = $('#doctorId').val();
    const method = id ? 'POST' : 'POST'; // patho_api uses POST for both create and update (upsert)

    // Convert FormData to a plain object for JSON payload if needed,
    // but patho_api/doctor.php handles both JSON and form data.
    // For simplicity and consistency with existing form submission, we'll stick to FormData.

    const submitBtn = $('#doctorForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: 'patho_api/doctor.php',
        type: method,
        data: formData,
        processData: false,
        contentType: false, // Important for FormData
        success: function(response) {
            if (response.success) {
                showAlert(id ? 'Doctor updated successfully!' : 'Doctor added successfully!', 'success');
                $('#doctorModal').modal('hide');
                loadDoctors();
                loadStats(); // Update stats after save
            } else {
                showAlert('Error: ' + (response.message || 'Unknown error'), 'error');
            }
        },
        error: function() {
            showAlert('Failed to save doctor data.', 'error');
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
            if (response.success) {
                showAlert('Doctor deleted successfully!', 'success');
                loadDoctors();
                loadStats(); // Update stats after delete
            } else {
                showAlert('Error deleting doctor: ' + (response.message || 'Unknown error'), 'error');
            }
        },
        error: function() {
            showAlert('Failed to delete doctor.', 'error');
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

// Helper function to generate avatar (assuming utils.js provides this)
// If utils.js is not available or doesn't have this, you might need to implement it here.
// For now, assuming utils.generateAvatar exists.
// function generateAvatar(name, bgColorClass) {
//     const initials = name ? name.charAt(0).toUpperCase() : '?';
//     return `<div class="avatar-circle ${bgColorClass}">${initials}</div>`;
// }

// Placeholder for utils.debounce and utils.initTooltips if not defined elsewhere
// if (typeof utils === 'undefined') {
//     var utils = {
//         debounce: function(func, delay) {
//             let timeout;
//             return function(...args) {
//                 const context = this;
//                 clearTimeout(timeout);
//                 timeout = setTimeout(() => func.apply(context, args), delay);
//             };
//         },
//         initTooltips: function() {
//             $('[data-toggle="tooltip"]').tooltip();
//         },
//         showLoading: function(selector) {
//             $(selector).append('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');
//         },
//         hideLoading: function(selector) {
//             $(selector).find('.overlay').remove();
//         },
//         showError: function(message) {
//             showAlert(message, 'error');
//         },
//         showSuccess: function(message) {
//             showAlert(message, 'success');
//         },
//         confirm: function(message, title) {
//             return new Promise((resolve) => {
//                 if (typeof Swal !== 'undefined') {
//                     Swal.fire({
//                         title: title,
//                         text: message,
//                         icon: 'warning',
//                         showCancelButton: true,
//                         confirmButtonColor: '#3085d6',
//                         cancelButtonColor: '#d33',
//                         confirmButtonText: 'Yes'
//                     }).then((result) => {
//                         resolve(result.isConfirmed);
//                     });
//                 } else {
//                     resolve(confirm(message));
//                 }
//             });
//         },
//         validateForm: function(formData, rules) {
//             const errors = [];
//             for (const field in rules) {
//                 const rule = rules[field];
//                 const value = formData[field];

//                 if (rule.required && (!value || value.trim() === '')) {
//                     errors.push(`${rule.label || field} is required.`);
//                 }
//                 if (value && rule.minLength && value.length < rule.minLength) {
//                     errors.push(`${rule.label || field} must be at least ${rule.minLength} characters long.`);
//                 }
//                 if (value && rule.type === 'email' && !/^[^
// @]+@[^
// @]+\.[^
// @]+$/.test(value)) {
//                     errors.push(`Invalid ${rule.label || field} format.`);
//                 }
//                 // Add more validation types as needed
//             }
//             return errors;
//         }
//     };
// }