/* Patient Management JavaScript */

// Global variables
let currentPage = 1;
let totalRecords = 0;
let recordsPerPage = 10;
let searchTimeout;

// Initialize page
$(document).ready(function() {
    loadPatients();
    updateStats();
    initializeEventListeners();
    generateUHID(); // Generate initial UHID for new patients
});

function initializeEventListeners() {
    // Search functionality
    $('#patientsSearch').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadPatients();
        }, 300);
    });

    // Filter functionality
    $('#genderFilter, #ageRangeFilter, #dateFilter').on('change', function() {
        currentPage = 1;
        loadPatients();
    });

    // Form submission
    $('#patientForm').on('submit', function(e) {
        e.preventDefault();
        savePatientData();
    });
}

function loadPatients() {
    const searchTerm = $('#patientsSearch').val();
    const gender = $('#genderFilter').val();
    const ageRange = $('#ageRangeFilter').val();
    const date = $('#dateFilter').val();

    // Show loading
    $('#patientsTableBody').html('<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');

    const params = new URLSearchParams({
        page: currentPage,
        limit: recordsPerPage,
        ...(searchTerm && { search: searchTerm }),
        ...(gender && { gender: gender }),
        ...(ageRange && { age_range: ageRange }),
        ...(date && { date: date })
    });

    // if frontend knows the user is admin/master, request all records
    try {
        if (window.AppUser && (window.AppUser.role === 'master' || window.AppUser.role === 'admin')) {
            params.append('all', '1');
        }
    } catch (e) { /* ignore */ }

    $.get(`patho_api/patient.php?${params}`)
        .done(function(response) {
            if (response.status === 'success') {
                populatePatientsTable(response.data);
                updatePagination(response.pagination);
            } else {
                showAlert('Error loading patients: ' + response.message, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load patients', 'error');
                $('#patientsTableBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load data</td></tr>');
        });
}

function populatePatientsTable(patients) {
    let html = '';
    
    if (patients.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted">No patients found</td></tr>';
    } else {
        patients.forEach(patient => {
            const ageDisplay = patient.age ? `${patient.age} ${patient.age_unit || 'Years'}` : '-';
            const gender = patient.gender || patient.sex; // Use gender if available, fallback to sex
            const genderBadge = gender ? 
                `<span class="badge badge-${gender === 'Male' ? 'primary' : gender === 'Female' ? 'danger' : 'secondary'}">${gender}</span>` : '-';
            
            html += `
                <tr>
                    <td>
                        <div class="font-weight-bold text-primary">${patient.uhid || 'N/A'}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-info text-white mr-2">
                                ${patient.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-weight-bold">${patient.name}</div>
                                ${patient.father_husband ? `<small class="text-muted">S/D/W of ${patient.father_husband}</small>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            ${patient.mobile ? `<div><i class="fas fa-mobile-alt text-primary"></i> ${patient.mobile}</div>` : ''}
                            ${patient.email ? `<div><i class="fas fa-envelope text-info"></i> ${patient.email}</div>` : ''}
                        </div>
                    </td>
                    <td>
                        <div>${ageDisplay}</div>
                        <div>${genderBadge}</div>
                    </td>
                    <td>
                        <div class="text-truncate" style="max-width: 150px;" title="${patient.address || ''}">
                            ${patient.address || '-'}
                        </div>
                    </td>
                    <td>
                        <small class="text-muted">${formatDateTime(patient.created_at)}</small>
                    </td>
                    <td>
                        <small class="text-muted">${patient.added_by_username || '—'}</small>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm" onclick="viewPatient(${patient.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editPatient(${patient.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deletePatient(${patient.id}, '${patient.name}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#patientsTableBody').html(html);
}

function updatePagination(pagination) {
    totalRecords = pagination.total;
    const totalPages = Math.ceil(totalRecords / recordsPerPage);
    
    // Update info
    const start = ((currentPage - 1) * recordsPerPage) + 1;
    const end = Math.min(currentPage * recordsPerPage, totalRecords);
    $('#patientsInfo').html(`Showing ${start} to ${end} of ${totalRecords} entries`);
    
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
    
    $('#patientsPagination').html(paginationHtml);
}

function updateStats() {
    $.get('patho_api/patient.php?action=stats')
        .done(function(response) {
            if (response.status === 'success') {
                $('#totalPatients').text(response.data.total || 0);
                $('#todayPatients').text(response.data.today || 0);
                $('#malePatients').text(response.data.male || 0);
                $('#femalePatients').text(response.data.female || 0);
            }
        });
}

function changePage(page) {
    currentPage = page;
    loadPatients();
}

function clearFilters() {
    $('#patientsSearch').val('');
    $('#genderFilter').val('');
    $('#ageRangeFilter').val('');
    $('#dateFilter').val('');
    currentPage = 1;
    loadPatients();
}

function openAddPatientModal() {
    $('#patientForm')[0].reset();
    $('#patientId').val('');
    $('#modalTitle').text('Add New Patient');
    generateUHID();
    $('#patientModal').modal('show');
}

function generateUHID() {
    const timestamp = Date.now().toString();
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    const uhid = 'P' + timestamp.slice(-6) + random;
    $('#patientUHID').val(uhid);
}

function editPatient(id) {
    $.get(`patho_api/patient.php?id=${id}`)
        .done(function(response) {
            if (response.status === 'success') {
                const patient = response.data;
                $('#patientId').val(patient.id);
                $('#patientName').val(patient.name);
                $('#patientUHID').val(patient.uhid);
                $('#patientMobile').val(patient.mobile);
                $('#patientEmail').val(patient.email);
                $('#patientAge').val(patient.age);
                $('#patientAgeUnit').val(patient.age_unit);
                $('#patientGender').val(patient.gender || patient.sex); // Use gender if available, fallback to sex
                $('#patientFatherHusband').val(patient.father_husband);
                $('#patientAddress').val(patient.address);
                    // Fill added_by if available (admin override)
                    if (typeof patient.added_by !== 'undefined' && patient.added_by !== null) {
                        $('#patientAddedBy').val(patient.added_by);
                    } else {
                        $('#patientAddedBy').val('');
                    }
                
                $('#modalTitle').text('Edit Patient');
                $('#patientModal').modal('show');
            } else {
                showAlert('Error loading patient data: ' + response.message, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load patient data', 'error');
        });
}

// Fallback global function used by inline onclick on View buttons
function viewPatient(id) {
    try {
        console.debug('viewPatient() called', id);
        $.get('ajax/patient_api.php', {action: 'get', id: id}, function(resp) {
            if (resp.success) {
                var d = resp.data;
                $('#patientId').val(d.id);
                $('#patientName').val(d.name);
                $('#patientMobile').val(d.mobile);
                $('#patientFatherHusband').val(d.father_husband);
                $('#patientAddress').val(d.address);
                $('#patientSex').val(d.sex);
                $('#patientAge').val(d.age);
                $('#patientAgeUnit').val(d.age_unit || 'Years');
                $('#patientUHID').val(d.uhid);
                $('#patientModalLabel').text('View Patient');
                $('#patientForm').find('input,textarea,select').prop('disabled', true);
                $('#savePatientBtn').hide();
                $('#patientModal').modal('show');
            } else {
                toastr.error('Patient not found');
            }
        }, 'json').fail(function(xhr) {
            var msg = xhr.responseText || 'Server error';
            try {
                var j = JSON.parse(xhr.responseText || '{}');
                if (j.message) msg = j.message;
            } catch (e) {}
            toastr.error(msg);
        });
    } catch (err) {
        console.error('viewPatient error', err);
        toastr.error('Error: ' + (err.message || err));
    }
}

function savePatientData() {
    const formData = new FormData($('#patientForm')[0]);
    const id = $('#patientId').val();
    const method = id ? 'PUT' : 'POST';
    
    // Add loading state
    const submitBtn = $('#patientForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: 'patho_api/patient.php',
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                showAlert(id ? 'Patient updated successfully!' : 'Patient added successfully!', 'success');
                $('#patientModal').modal('hide');
                loadPatients();
                updateStats();
            } else {
                showAlert('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showAlert('Failed to save patient data', 'error');
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function deletePatient(id, name) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Are you sure?',
            text: `You want to delete patient "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                performDelete(id);
            }
        });
    } else {
        // Fallback to confirm if SweetAlert is not available
        if (confirm(`Delete patient "${name}"?`)) {
            performDelete(id);
        }
    }
}

function performDelete(id) {
    $.ajax({
        url: `patho_api/patient.php?id=${id}`,
        type: 'DELETE',
        success: function(response) {
            if (response.status === 'success') {
                showAlert('Patient deleted successfully!', 'success');
                loadPatients();
                updateStats();
            } else {
                showAlert('Error deleting patient: ' + response.message, 'error');
            }
        },
        error: function() {
            showAlert('Failed to delete patient', 'error');
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
    loadPatients();

    // Search/filter UI
    $('#patientsSearch').on('input', function() {
        var q = $(this).val().toLowerCase().trim();
        if (!q) {
            $('#patientsTable tbody tr').show();
            return;
        }
        $('#patientsTable tbody tr').each(function() {
            var row = $(this);
            var text = row.text().toLowerCase();
            row.toggle(text.indexOf(q) !== -1);
        });
    });

    $('#patientsSearchClear').click(function(e) {
        e.preventDefault();
        $('#patientsSearch').val('');
        $('#patientsSearch').trigger('input');
    });

    $('#savePatientBtn').click(function() {
        var data = $('#patientForm').serialize() + '&action=save';
        $.post('ajax/patient_api.php', data, function(resp) {
            if (resp.success) {
                toastr.success(resp.message || 'Saved');
                $('#patientModal').modal('hide');
                if (resp.data && !$('#patientId').val()) {
                    // New record - add to table directly
                    addPatientToTable(resp.data);
                } else {
                    // Update - reload table
                    loadPatients();
                }
                $('#patientForm')[0].reset();
                $('#patientId').val('');
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

    // Edit - use document delegation to be robust against dynamic table rebuilds
    $(document).on('click', '.edit-patient', function() {
        try {
            console.debug('edit-patient clicked', this, $(this).data('id'));
            var id = $(this).data('id');
            $.get('ajax/patient_api.php', {action: 'get', id: id}, function(resp) {
                if (resp.success) {
                    var d = resp.data;
                    $('#patientId').val(d.id);
                    $('#patientName').val(d.name);
                    $('#patientMobile').val(d.mobile);
                    $('#patientFatherHusband').val(d.father_husband);
                    $('#patientAddress').val(d.address);
                    $('#patientSex').val(d.sex);
                    $('#patientAge').val(d.age);
                    $('#patientAgeUnit').val(d.age_unit || 'Years');
                    $('#patientUHID').val(d.uhid);
                    // Make editable for edit
                    $('#patientModalLabel').text('Edit Patient');
                    $('#patientForm').find('input,textarea,select').prop('disabled', false);
                    $('#savePatientBtn').show();
                    $('#patientModal').modal('show');
                } else {
                    toastr.error('Patient not found');
                }
            }, 'json').fail(function(xhr) {
                var msg = xhr.responseText || 'Server error';
                try {
                    var j = JSON.parse(xhr.responseText || '{}');
                    if (j.message) msg = j.message;
                } catch (e) {}
                toastr.error(msg);
            });
        } catch (err) {
            console.error('edit handler error', err);
            toastr.error('Error: ' + (err.message || err));
        }
    });

    // View handler - opens modal in read-only mode; attach to document for robustness
    $(document).on('click', '.view-patient', function() {
        try {
            console.debug('view-patient clicked', this, $(this).data('id'));
            var id = $(this).data('id');
            $.get('ajax/patient_api.php', {action: 'get', id: id}, function(resp) {
                if (resp.success) {
                    var d = resp.data;
                    $('#patientId').val(d.id);
                    $('#patientName').val(d.name);
                    $('#patientMobile').val(d.mobile);
                    $('#patientFatherHusband').val(d.father_husband);
                    $('#patientAddress').val(d.address);
                    $('#patientSex').val(d.sex);
                    $('#patientAge').val(d.age);
                    $('#patientAgeUnit').val(d.age_unit || 'Years');
                    $('#patientUHID').val(d.uhid);
                    // Make read-only for view
                    $('#patientModalLabel').text('View Patient');
                    $('#patientForm').find('input,textarea,select').prop('disabled', true);
                    $('#savePatientBtn').hide();
                    $('#patientModal').modal('show');
                } else {
                    toastr.error('Patient not found');
                }
            }, 'json').fail(function(xhr) {
                var msg = xhr.responseText || 'Server error';
                try {
                    var j = JSON.parse(xhr.responseText || '{}');
                    if (j.message) msg = j.message;
                } catch (e) {}
                toastr.error(msg);
            });
        } catch (err) {
            console.error('view handler error', err);
            toastr.error('Error: ' + (err.message || err));
        }
    });

    $(document).on('click', '.delete-patient', function() {
        try {
            if (!confirm('Delete patient?')) return;
            var id = $(this).data('id');
            $.post('ajax/patient_api.php', {action: 'delete', id: id}, function(resp) {
                if (resp.success) {
                    toastr.success(resp.message);
                    loadPatients();
                } else {
                    toastr.error(resp.message || 'Delete failed');
                }
            }, 'json').fail(function(xhr) {
                var msg = xhr.responseText || 'Server error';
                try {
                    var j = JSON.parse(xhr.responseText || '{}');
                    if (j.message) msg = j.message;
                } catch (e) {}
                toastr.error(msg);
            });
        } catch (err) {
            console.error('delete handler error', err);
            toastr.error('Error: ' + (err.message || err));
        }
    });

    // Restore modal to editable default when closed
    $('#patientModal').on('hidden.bs.modal', function() {
        $('#patientForm').find('input,textarea,select').prop('disabled', false);
        $('#savePatientBtn').show();
        $('#patientModalLabel').text('Add Patient');
    });
});