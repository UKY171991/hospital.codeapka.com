// Enhanced Doctor Management JavaScript

// Global DataTable instance
let doctorsDataTable;

$(document).ready(function() {
    initializeDoctorPage();
});

function initializeDoctorPage() {
    initializeDataTable(); // Initialize DataTables first
    loadStats();
    loadFilters(); // Load specializations and hospitals for filters
    setupEventListeners();
    utils.initTooltips();
}

function setupEventListeners() {
    // Search functionality
    $('#doctorsSearch').on('input', utils.debounce(function() {
        doctorsDataTable.search($(this).val()).draw();
    }, 300));

    // Specialization filter
    $('#specializationFilter').on('change', function() {
        doctorsDataTable.ajax.reload(); // Reload data with new filter
    });

    // Hospital filter
    $('#hospitalFilter').on('change', function() {
        doctorsDataTable.ajax.reload(); // Reload data with new filter
    });

    // Records per page change - DataTables handles this automatically with lengthMenu
    // We just need to ensure the select element is present in HTML and DataTables picks it up.

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

function initializeDataTable() {
    if ($.fn.DataTable.isDataTable('#doctorsTable')) {
        $('#doctorsTable').DataTable().destroy();
        $('#doctorsTableBody').empty(); // Clear old tbody content
    }

    doctorsDataTable = $('#doctorsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false, // Added to prevent potential auto-width issues
        pageLength: 10, // Default records per page
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[0, 'desc']], // Order by ID descending by default
        ajax: {
            url: 'patho_api/doctor.php',
            type: 'GET',
            dataType: 'json',
            data: function(d) {
                // DataTables sends its own parameters, we add our custom filters
                d.action = 'list';
                d.page = (d.start / d.length) + 1; // Calculate page number
                d.limit = d.length; // Records per page
                d.search = d.search.value; // DataTables global search
                d.specialization = $('#specializationFilter').val();
                d.hospital = $('#hospitalFilter').val();
            },
            dataSrc: function(json) {
                console.log("API Response (dataSrc):", json); // Log entire JSON response
                // Map API response to DataTables format
                if (!json.success) {
                    console.error("API returned an error:", json.message);
                    showAlert('Error loading doctors: ' + (json.message || 'Unknown API error'), 'error');
                    return []; // Return empty array to prevent DataTables error
                }
                if (!json.data) {
                    console.warn("API returned no data array.");
                    return [];
                }
                json.recordsTotal = json.pagination.total;
                json.recordsFiltered = json.pagination.total; // Assuming server-side filtering is applied
                return json.data;
            },
            error: function(xhr, error, thrown) {
                console.error("DataTables AJAX error:", xhr, error, thrown);
                let errorMessage = 'Error loading doctors data. ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += xhr.responseJSON.message;
                } else {
                    errorMessage += thrown ? thrown : 'Unknown error.';
                }
                showAlert(errorMessage, 'error');
            }
        },
        columns: [
            {
                data: null, // Use null for Sr. No. as it's not directly from data
                orderable: false,
                render: function (data, type, row, meta) {
                    console.log("Rendering Sr. No. for row:", row); // Debugging
                    // Calculate serial number based on current page and row index
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'id' },
            {
                data: 'name',
                render: function(data, type, row) {
                    console.log("Rendering Name for row:", row); // Debugging
                    const avatar = utils.generateAvatar(row.name || '', 'bg-info'); // Ensure name is not null/undefined
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary text-white mr-2">
                                ${row.name ? row.name.charAt(0).toUpperCase() : '?'}
                            </div>
                            <div>
                                <div class="font-weight-bold">${row.name || '-'}</div>
                                ${row.registration_no ? `<small class="text-muted">Reg: ${row.registration_no}</small>` : ''}
                            </div>
                        </div>
                    `;
                }
            },
            { data: 'qualification', defaultContent: '-' },
            {
                data: 'specialization',
                render: function(data, type, row) { // Added row parameter for consistency
                    console.log("Rendering Specialization for row:", row); // Debugging
                    return data ? `<span class="badge badge-info">${data}</span>` : '-';
                }
            },
            { data: 'hospital', defaultContent: '-' },
            { data: 'contact_no', defaultContent: '-' },
            { data: 'phone', defaultContent: '-' },
            { data: 'email', defaultContent: '-' },
            { data: 'registration_no', defaultContent: '-' },
            { data: 'percent', defaultContent: '-' },
            { data: 'added_by_username', defaultContent: '-' },
            { 
                data: 'created_at',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString() : '-';
                }
            },
            { 
                data: 'updated_at',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString() : '-';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    console.log("Rendering Actions for row:", row); // Debugging
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm" onclick="viewDoctor(${row.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editDoctor(${row.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteDoctor(${row.id}, '${row.name || ''}')" title="Delete"> // Ensure name is not null/undefined
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            search: "Search doctors:",
            lengthMenu: "Show _MENU_ doctors per page",
            info: "Showing _START_ to _END_ of _TOTAL_ doctors",
            infoEmpty: "No doctors found",
            infoFiltered: "(filtered from _MAX_ total doctors)",
            zeroRecords: "No matching doctors found"
        },
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
    });
}

// loadDoctors, populateDoctorsTable, updatePagination, changePage are no longer needed
// as DataTables handles these.

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

function clearFilters() {
    $('#doctorsSearch').val('');
    $('#specializationFilter').val('');
    $('#hospitalFilter').val('');
    doctorsDataTable.search('').columns().search('').draw(); // Clear all DataTables searches
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
                                <tr><td><strong>ID:</strong></td><td>${doctor.id || '-'}</td></tr>
                                <tr><td><strong>Name:</strong></td><td>${doctor.name || '-'}</td></tr>
                                <tr><td><strong>Qualification:</strong></td><td>${doctor.qualification || '-'}</td></tr>
                                <tr><td><strong>Specialization:</strong></td><td>${doctor.specialization || '-'}</td></tr>
                                <tr><td><strong>Hospital:</strong></td><td>${doctor.hospital || '-'}</td></tr>
                                <tr><td><strong>Registration No:</strong></td><td>${doctor.registration_no || '-'}</td></tr>
                                <tr><td><strong>Contact:</strong></td><td>${doctor.contact_no || '-'}</td></tr>
                                <tr><td><strong>Phone:</strong></td><td>${doctor.phone || '-'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td><strong>Email:</strong></td><td>${doctor.email || '-'}</td></tr>
                                <tr><td><strong>Commission:</strong></td><td>${doctor.percent ? doctor.percent + '%' : '-'}</td></tr>
                                <tr><td><strong>Added By:</strong></td><td>${doctor.added_by_username || doctor.added_by || '-'}</td></tr>
                                <tr><td><strong>Server ID:</strong></td><td>${doctor.server_id || '-'}</td></tr>
                                <tr><td><strong>Created:</strong></td><td>${formatDateTime(doctor.created_at)}</td></tr>
                                <tr><td><strong>Updated:</strong></td><td>${formatDateTime(doctor.updated_at)}</td></tr>
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
                doctorsDataTable.ajax.reload(); // Reload DataTables after save
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
                doctorsDataTable.ajax.reload(); // Reload DataTables after delete
                loadStats(); // Update stats after delete
            }
            else {
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