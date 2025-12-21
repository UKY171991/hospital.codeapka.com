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
        doctorsDataTable.ajax.reload(null, false); // Reload data with new filter without resetting pagination
    });

    // Hospital filter
    $('#hospitalFilter').on('change', function() {
        doctorsDataTable.ajax.reload(null, false); // Reload data with new filter without resetting pagination
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
            cache: false, // Disable caching to ensure fresh data
            data: function(d) {
                // DataTables sends its own parameters, we add our custom filters
                d.action = 'list';
                d.page = (d.start / d.length) + 1; // Calculate page number
                d.limit = d.length; // Records per page
                d.search = d.search.value; // DataTables global search
                d.specialization = $('#specializationFilter').val();
                d.hospital = $('#hospitalFilter').val();
                // Add cache-busting parameter
                d._t = new Date().getTime();
            },
            dataSrc: function(json) {
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
                // Fix: Check if pagination exists before accessing total
                if (json.pagination && json.pagination.total) {
                    json.recordsTotal = json.pagination.total;
                    json.recordsFiltered = json.pagination.total;
                } else {
                    // Fallback if pagination is not provided
                    json.recordsTotal = json.data ? json.data.length : 0;
                    json.recordsFiltered = json.data ? json.data.length : 0;
                }
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
                    // Calculate serial number based on current page and row index
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'id' },
            {
                data: 'name',
                render: function(data, type, row) {
                    const avatar = utils.generateAvatar(row.name || '', 'bg-info'); // Ensure name is not null/undefined
                    return `
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary text-white mr-2">
                                ${row.name ? row.name.charAt(0).toUpperCase() : '?'}
                            </div>
                            <div>
                                <div class="font-weight-bold">${row.name || '-'}</div>
                            </div>
                        </div>
                    `;
                }
            },
            { data: 'hospital', defaultContent: '-' },
            { data: 'contact_no', defaultContent: '-' },
            { data: 'phone', defaultContent: '-' },
            { data: 'email', defaultContent: '-' },
            { data: 'percent', defaultContent: '-' },
            { data: 'added_by_username', defaultContent: '-' },
            { 
                data: 'created_at',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString() : '-';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm" onclick="viewDoctor(${row.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editDoctor(${row.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteDoctor(${row.id}, '${row.name || ''}')" title="Delete">
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
                // Store doctor ID in modal data for editDoctorFromView function
                $('#viewDoctorModal').data('doctor-id', doctor.id);
                
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
    // Prevent multiple submissions
    if (window.isSubmitting) {
        console.log('Form already submitting, preventing duplicate submission');
        return;
    }
    
    window.isSubmitting = true;
    
    const formData = new FormData($('#doctorForm')[0]);
    const id = $('#doctorId').val();
    const method = id ? 'POST' : 'POST'; // patho_api uses POST for both create and update (upsert)

    // Clear any existing loading states first
    clearAllLoadingStates();
    
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
                
                // Comprehensive cleanup of all loading states
                clearAllLoadingStates();
                
                console.log('Update successful, reloading table...');
                console.log('API Response:', response);
                
                // Force table reload with proper approach
                setTimeout(() => {
                    if (typeof doctorsDataTable !== 'undefined' && doctorsDataTable) {
                        console.log('Reloading DataTable...');
                        console.log('Current DataTable state:', doctorsDataTable);
                        
                        // Clear any existing data first
                        doctorsDataTable.clear().draw();
                        
                        // Force reload with cache busting
                        doctorsDataTable.ajax.reload(function(json) {
                            console.log('Table reload completed');
                            console.log('Data returned:', json);
                            
                            // Check if data was returned
                            if (json && json.data && json.data.length > 0) {
                                console.log('Data rows returned:', json.data.length);
                            } else {
                                console.warn('No data returned from API');
                            }
                            
                            // Force redraw to ensure data is displayed
                            doctorsDataTable.draw();
                            
                            // Clear any remaining loading states
                            clearAllLoadingStates();
                            
                            // Additional safety check - if table still empty, try reinitialization
                            setTimeout(() => {
                                if ($('#doctorsTable tbody tr').length === 0) {
                                    console.warn('Table still empty, attempting reinitialization...');
                                    doctorsDataTable.destroy();
                                    initializeDataTable();
                                }
                            }, 1000);
                            
                        }, false);
                        
                    } else {
                        console.log('DataTable not found, initializing...');
                        initializeDataTable();
                    }
                }, 500); // Wait for modal to fully hide
                
            } else {
                showAlert('Error: ' + (response.message || 'Unknown error'), 'error');
                clearAllLoadingStates();
            }
        },
        error: function(xhr, status, error) {
            showAlert('Failed to save doctor data. ' + (xhr.responseJSON?.message || error), 'error');
            // Clear all loading states on error
            clearAllLoadingStates();
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
            // Reset submission flag
            window.isSubmitting = false;
            // Ensure all loading states are cleared when request completes
            setTimeout(() => {
                clearAllLoadingStates();
            }, 100);
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
                    clearAllLoadingStates(); // Clear any loading states
                    doctorsDataTable.ajax.reload(null, false); // Reload DataTables after delete without resetting pagination
                    loadStats(); // Update stats after delete
                }
                else {
                    showAlert('Error deleting doctor: ' + (response.message || 'Unknown error'), 'error');
                    clearAllLoadingStates(); // Clear loading states on error
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

// Bulk selection functions
function selectAllDoctors() {
    const checkboxes = $('#doctorsTable tbody input[type="checkbox"]');
    checkboxes.prop('checked', true);
    updateSelectedCount();
}

function deselectAllDoctors() {
    const checkboxes = $('#doctorsTable tbody input[type="checkbox"]');
    checkboxes.prop('checked', false);
    updateSelectedCount();
}

function updateSelectedCount() {
    const selected = $('#doctorsTable tbody input[type="checkbox"]:checked').length;
    $('.selected-count').text(selected);
}

function bulkExportDoctors() {
    const selectedIds = [];
    $('#doctorsTable tbody input[type="checkbox"]:checked').each(function() {
        selectedIds.push($(this).data('id'));
    });
    
    if (selectedIds.length === 0) {
        showAlert('Please select at least one doctor to export.', 'error');
        return;
    }
    
    // Implement bulk export logic here
    // Export selectedIds array
}

function bulkDeleteDoctors() {
    const selectedIds = [];
    $('#doctorsTable tbody input[type="checkbox"]:checked').each(function() {
        selectedIds.push($(this).data('id'));
    });
    
    if (selectedIds.length === 0) {
        showAlert('Please select at least one doctor to delete.', 'error');
        return;
    }
    
    // Implement bulk delete logic here
    // Delete selectedIds array
}

function exportDoctors() {
    // Implement export all doctors logic here
    // Export all doctors data
}

// Helper function to clear all loading states
function clearAllLoadingStates() {
    // Remove all types of loading indicators
    $('.overlay').remove();
    $('.loading').remove();
    $('.spinner-border').remove();
    $('.fa-spinner').remove();
    $('.fa-spin').removeClass('fa-spin');
    
    // Clear DataTables processing indicators
    $('#doctorsTable_processing').remove();
    $('.dataTables_processing').remove();
    
    // Clear any loading states in table cells
    $('#doctorsTable td').removeClass('loading');
    $('#doctorsTable td .fa-spinner').remove();
    $('#doctorsTable td .spinner-border').remove();
    
    // Clear loading states in modals
    utils.hideLoading('#doctorModal .modal-body');
    utils.hideLoading('#viewDoctorModal .modal-body');
    
    // Clear any global loading states
    $('body').removeClass('loading');
    
    // Aggressive cleanup - remove any elements with loading classes
    $('[class*="loading"]').remove();
    $('[class*="spinner"]').remove();
    
    // Reset submit buttons if they're stuck
    $('button[type="submit"]').each(function() {
        if ($(this).find('.fa-spinner').length > 0) {
            $(this).find('.fa-spinner').remove();
            $(this).prop('disabled', false);
        }
    });
}

// View modal functions
function editDoctorFromView() {
    // Get the current doctor ID from the view modal content
    const doctorId = $('#viewDoctorModal').data('doctor-id');
    if (doctorId) {
        // Hide view modal first
        $('#viewDoctorModal').modal('hide');
        
        // Wait for modal to fully hide before showing edit modal
        $('#viewDoctorModal').on('hidden.bs.modal', function () {
            // Clear any loading states from view modal
            utils.hideLoading('#viewDoctorModal .modal-body');
            // Open edit modal
            editDoctor(doctorId);
            // Remove the event listener to prevent multiple calls
            $(this).off('hidden.bs.modal');
        });
    } else {
        showAlert('Unable to determine doctor ID for editing', 'error');
    }
}

function printDoctorDetails() {
    // Print the doctor details from the view modal
    const printContent = $('#viewDoctorContent').html();
    if (printContent) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Doctor Details</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        td { padding: 8px; border-bottom: 1px solid #ddd; }
                        .font-weight-bold { font-weight: bold; }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}

// Helper function to generate avatar (assuming utils.js provides this)
// If utils.js is not available or doesn't have this, you might need to implement it here.
// For now, assuming utils.generateAvatar exists.

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
//                 if (value && rule.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
//                     errors.push(`Invalid ${rule.label || field} format.`);
//                 }
//                 // Add more validation types as needed
//             }
//             return errors;
//         }
//     };
// }