/**
 * Patient Management System - Complete AJAX Implementation
 * All functions work without page refresh
 */

$(document).ready(function() {
    initializePatientPage();
});

// Global Variables - Using HMS namespace
window.HMS = window.HMS || {};
HMS.patient = {
    totalPages: 1,
    currentFilters: {},
    searchTimeout: null,
    isLoading: false,
    currentViewPatientId: null
};

/**
 * Initialize Patient Page
 */
function initializePatientPage() {
    APP_LOG('Initializing Patient Management System...');
    
    // Initialize event listeners
    initializeEventListeners();
    
    // Load initial data
    loadPatients();
    loadStats();
    loadAddedByUsers(); // Load users for the "Added By" filter
    
    // Auto-generate UHID for new patients
    generateUHID();
}

/**
 * Initialize all event listeners
 */
function initializeEventListeners() {
    // Search input with debounce
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            currentPage = 1;
            loadPatients();
        }, 500);
    });

    // Filter change events
    $('#genderFilter, #ageFilter, #dateFilter, #filterAddedBy').on('change', function() {
        currentPage = 1;
        loadPatients();
    });

    // Form submission
    $('#patientForm').on('submit', function(e) {
        e.preventDefault();
        savePatient();
    });

    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('.patient-checkbox').prop('checked', this.checked);
        updateBulkActions();
    });

    // Modal events
    $('#patientModal').on('hidden.bs.modal', function() {
        resetForm();
    });

    // Bulk action events
    $(document).on('change', '.patient-checkbox', function() {
        updateBulkActions();
    });
}

/**
 * Load patients with pagination and filters
 */
function loadPatients() {
    if (isLoading) return;
    
    isLoading = true;
    showLoading();

    const searchTerm = $('#searchInput').val() || '';
    const gender = $('#genderFilter').val() || '';
    const ageRange = $('#ageFilter').val() || '';
    const date = $('#dateFilter').val() || '';
    const addedBy = $('#filterAddedBy').val() || '';

    const filters = {
        search: searchTerm,
        gender: gender,
        age_range: ageRange,
        date: date,
        added_by: addedBy,
        page: currentPage,
        limit: recordsPerPage
    };

    APP_LOG('Loading patients with filters:', filters);

    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'POST',
        data: {
            action: 'list',
            ...filters
        },
        dataType: 'json',
        timeout: 30000,
        success: function(response) {
            APP_LOG('Patient list response:', response);
            
            if (response.success) {
                renderPatientsTable(response.data || []);
                updatePagination(response.pagination || {});
                hideLoading();
            } else {
                console.error('Failed to load patients:', response);
                showError('Failed to load patients: ' + (response.message || 'Unknown error'));
                showNoData();
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error details:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });

            let errorMessage = 'Failed to load patients. ';
            if (xhr.status === 0) {
                // status 0 can mean network offline or a request aborted by extensions/devtools.
                if (!navigator.onLine) {
                    errorMessage += 'Network connection error.';
                    showError(errorMessage);
                } else {
                    // Likely a transient/extension/devtools abort - don't show a noisy toast.
                    console.warn('XHR status 0 while online - suppressing toast (possible extension or abort).');
                }
            } else if (xhr.status === 404) {
                errorMessage += 'API endpoint not found.';
                showError(errorMessage);
            } else if (xhr.status >= 500) {
                errorMessage += 'Server error.';
                showError(errorMessage);
            } else {
                errorMessage += 'Please check your connection.';
                showError(errorMessage);
            }

            showNoData();
        },
        complete: function() {
            isLoading = false;
            hideLoading();
        }
    });
}

/**
 * Render patients table
 */
function renderPatientsTable(patients) {
    const tbody = $('#patientsTableBody');
    
    if (!patients || patients.length === 0) {
        showNoData();
        return;
    }

    let html = '';
    patients.forEach(function(patient) {
        html += `
            <tr>
                <td>
                    <input type="checkbox" class="patient-checkbox" value="${patient.id}">
                </td>
                <td>
                    <span class="badge badge-primary">${patient.uhid || 'N/A'}</span>
                </td>
                <td>
                    <div class="patient-info">
                        <strong>${patient.name}</strong>
                        ${patient.father_husband ? `<br><small class="text-muted">S/O: ${patient.father_husband}</small>` : ''}
                    </div>
                </td>
                <td>
                    <div class="contact-info">
                        <i class="fas fa-mobile-alt"></i> ${patient.mobile}
                        ${patient.email ? `<br><i class="fas fa-envelope"></i> ${patient.email}` : ''}
                    </div>
                </td>
                <td>
                    <span class="age-gender">
                        ${patient.age ? patient.age + ' ' + (patient.age_unit || 'Years') : 'N/A'}
                        ${patient.gender ? `<br><span class="badge badge-${getGenderBadgeClass(patient.gender)}">${patient.gender}</span>` : ''}
                    </span>
                </td>
                <td>
                    <div class="address-info">
                        ${patient.address ? patient.address.substring(0, 50) + (patient.address.length > 50 ? '...' : '') : 'N/A'}
                    </div>
                </td>
                <td>
                    <small class="text-muted">
                        ${formatDate(patient.created_at)}
                    </small>
                </td>
                <td>
                    <small class="text-muted">
                        ${patient.added_by || 'System'}
                    </small>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info" onclick="viewPatient(${patient.id})" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="editPatient(${patient.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deletePatient(${patient.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.html(html);
    $('#noDataMessage').hide();
    $('#patientsTable').show();
}

/**
 * Update pagination
 */
function updatePagination(pagination) {
    if (!pagination) return;

    totalPages = pagination.total_pages || 1;
    currentPage = pagination.current_page || 1;

    // Update pagination info
    const start = ((currentPage - 1) * recordsPerPage) + 1;
    const end = Math.min(currentPage * recordsPerPage, pagination.total_records);
    $('#paginationInfo').html(`Showing ${start} to ${end} of ${pagination.total_records} entries`);

    // Generate pagination buttons
    let paginationHtml = '';
    
    // Previous button
    if (currentPage > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a></li>`;
    }

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(1)">1</a></li>`;
        if (startPage > 2) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === currentPage ? 'active' : '';
        paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${totalPages})">${totalPages}</a></li>`;
    }

    // Next button
    if (currentPage < totalPages) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a></li>`;
    }

    $('#pagination').html(paginationHtml);
}

/**
 * Change page
 */
function changePage(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    currentPage = page;
    loadPatients();
}

/**
 * Load statistics
 */
function loadStats() {
    APP_LOG('Loading statistics...');
    
    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'POST',
        data: { action: 'stats' },
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            APP_LOG('Stats response:', response);
            
            if (response.success && response.data) {
                $('#totalPatients').text(response.data.total || 0);
                $('#todayPatients').text(response.data.today || 0);
                $('#malePatients').text(response.data.male || 0);
                $('#femalePatients').text(response.data.female || 0);
            } else {
                console.error('Failed to load statistics:', response);
                $('#totalPatients').text('0');
                $('#todayPatients').text('0');
                $('#malePatients').text('0');
                $('#femalePatients').text('0');
            }
        },
        error: function(xhr, status, error) {
            console.error('Stats AJAX Error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            $('#totalPatients').text('0');
            $('#todayPatients').text('0');
            $('#malePatients').text('0');
            $('#femalePatients').text('0');
        }
    });
}

/**
 * Function to load users into the "Added By" dropdowns
 */
function loadAddedByUsers(selectedUserId = null) {
    $.get('ajax/user_api.php', { action: 'list' }, function(r){
      if(r && r.success && r.data){
        var options = '<option value="">All</option>';
        $.each(r.data, function(i, user){
          options += '<option value="' + user.id + '"' + (selectedUserId == user.id ? ' selected' : '') + '>' + user.username + '</option>';
        });
        $('#filterAddedBy, #patientAddedBy').html(options); // Also populate the hidden added_by in the form
      } else {
        console.error('Failed to load users:', r && r.message);
        showError((r && r.message) || 'Failed to load users for "Added By" dropdown.');
      }
    }, 'json').fail(function(xhr){
      console.error('Ajax error fetching users', xhr);
      showError('Server error fetching users for "Added By" dropdown.');
    });
}

/**
 * Open add patient modal
 */
function openAddPatientModal() {
    resetForm();
    generateUHID();
    $('#modalTitle').text('Add New Patient');
    $('#patientModal').modal('show');
}

/**
 * Edit patient
 */
function editPatient(id) {
    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'POST',
        data: {
            action: 'get',
            id: id
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                populateForm(response.data);
                $('#modalTitle').text('Edit Patient');
                $('#patientModal').modal('show');
            } else {
                showError('Failed to load patient data');
            }
        },
        error: function() {
            showError('Failed to load patient data');
        }
    });
}

/**
 * View patient
 */
function viewPatient(id) {
    currentViewPatientId = id;
    
    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'POST',
        data: {
            action: 'get',
            id: id
        },
        dataType: 'json',
        success: function(response) {
            try {
                if (response && response.success) {
                    renderPatientDetails(response.data);
                    $('#viewPatientModal').modal('show');
                } else {
                    var msg = (response && response.message) ? response.message : 'Failed to load patient details';
                    showError(msg);
                }
            } catch (e) {
                showError('Failed to load patient details');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // Try to extract a JSON message from the response body
            var msg = 'Failed to load patient details';
            try {
                var j = JSON.parse(jqXHR.responseText || '{}');
                if (j.message) msg = j.message;
                else if (j.error) msg = j.error;
            } catch (e) {
                // fallback to status/text
                if (textStatus || errorThrown) msg = (textStatus || '') + ' ' + (errorThrown || '');
            }
            showError(msg);
        }
    });
}

/**
 * Render patient details in view modal
 */
function renderPatientDetails(patient) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>UHID:</strong></td>
                                <td><span class="badge badge-primary">${patient.uhid || 'N/A'}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>${patient.name}</td>
                            </tr>
                            <tr>
                                <td><strong>Father/Husband:</strong></td>
                                <td>${patient.father_husband || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Age:</strong></td>
                                <td>${patient.age ? patient.age + ' ' + (patient.age_unit || 'Years') : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Gender:</strong></td>
                                <td>${patient.gender ? `<span class="badge badge-${getGenderBadgeClass(patient.gender)}">${patient.gender}</span>` : 'N/A'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Mobile:</strong></td>
                                <td><i class="fas fa-mobile-alt"></i> ${patient.mobile}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>${patient.email ? `<i class="fas fa-envelope"></i> ${patient.email}` : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td>${patient.address || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Registration:</strong></td>
                                <td>${formatDate(patient.created_at)}</td>
                            </tr>
                            <tr>
                                <td><strong>Added By:</strong></td>
                                <td>${patient.added_by || 'System'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#patientViewContent').html(html);
}

/**
 * Delete patient
 */
function deletePatient(id) {
    // Prefer SweetAlert2 if available, otherwise use the built-in modal confirm
    if (typeof Swal !== 'undefined' && Swal.fire) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                performDeletePatient(id);
            }
        });
    } else {
        // Fallback to custom confirm modal
        showConfirmDialog('Delete Patient', 'Are you sure you want to delete this patient? This action cannot be undone.', 'danger')
            .then(function(confirmed) {
                if (confirmed) performDeletePatient(id);
            });
    }
}

function performDeletePatient(id) {
    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'POST',
        data: { action: 'delete', id: id },
        dataType: 'json'
    }).done(function(response) {
            if (response.success) {
                // Refresh data using best available method
                try {
                    if (typeof patientsDataTable !== 'undefined' && patientsDataTable && patientsDataTable.ajax && typeof patientsDataTable.ajax.reload === 'function') {
                        patientsDataTable.ajax.reload(null, false);
                    } else if (typeof patientTableManager !== 'undefined' && patientTableManager && typeof patientTableManager.refreshData === 'function') {
                        patientTableManager.refreshData();
                    } else if (typeof loadPatients === 'function') {
                        loadPatients();
                    } else {
                        window.location.reload();
                    }
                } catch (e) { console.warn('Error refreshing table after delete:', e); window.location.reload(); }

                showSuccess('Patient deleted successfully');
                loadStats();
            } else {
                showError(response.message || 'Failed to delete patient');
            }
    }).fail(function() {
        showError('Failed to delete patient');
    });
}

/**
 * Save patient (add or edit)
 */
function savePatient() {
    const formData = new FormData(document.getElementById('patientForm'));
    formData.append('action', 'save');

    // Show loading on button
    const submitBtn = $('#patientForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSuccess(response.message || 'Patient saved successfully');
                $('#patientModal').modal('hide');
                loadPatients();
                loadStats();
            } else {
                showError(response.message || 'Failed to save patient');
            }
        },
        error: function(xhr) {
            // Provide more detailed error information to help debugging server-side issues
            let errorMessage = 'Failed to save patient';
            console.error('Save patient XHR error:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText
            });
            try {
                const response = JSON.parse(xhr.responseText || '{}');
                if (response && response.message) {
                    errorMessage = response.message;
                } else if (response && response.debug) {
                    // If debug information is available, append for visibility
                    errorMessage = (response.message || errorMessage) + ' — Debug: ' + JSON.stringify(response.debug);
                }
            } catch (e) {
                // Not JSON, include raw text
                if (xhr.responseText) errorMessage = xhr.responseText;
            }
            showError(errorMessage);
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

/**
 * Generate UHID
 */
function generateUHID() {
    const timestamp = Date.now().toString().slice(-6);
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    const uhid = 'P' + timestamp + random;
    $('#patientUHID').val(uhid);
}

/**
 * Clear all filters
 */
function clearAllFilters() {
    $('#searchInput').val('');
    $('#genderFilter').val('');
    $('#ageFilter').val('');
    $('#dateFilter').val('');
    currentPage = 1;
    loadPatients();
}

/**
 * Export all patients
 */
function exportAllPatients() {
    window.open('ajax/patient_api.php?action=export&type=all', '_blank');
}

/**
 * Bulk operations
 */
function updateBulkActions() {
    const selectedBoxes = $('.patient-checkbox:checked');
    const count = selectedBoxes.length;
    
    if (count > 0) {
        $('#bulkActions').show();
        $('#selectedCount').text(count);
    } else {
        $('#bulkActions').hide();
    }
    
    // Update select all checkbox
    const totalBoxes = $('.patient-checkbox').length;
    if (count === 0) {
        $('#selectAll').prop('indeterminate', false).prop('checked', false);
    } else if (count === totalBoxes) {
        $('#selectAll').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#selectAll').prop('indeterminate', true);
    }
}

/**
 * Bulk export
 */
function bulkExport() {
    const selectedIds = $('.patient-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selectedIds.length === 0) {
        showError('Please select patients to export');
        return;
    }
    
    const form = $('<form>', {
        method: 'POST',
        action: 'ajax/patient_api.php',
        target: '_blank'
    });
    
    form.append($('<input>', {
        type: 'hidden',
        name: 'action',
        value: 'export'
    }));
    
    form.append($('<input>', {
        type: 'hidden',
        name: 'ids',
        value: selectedIds.join(',')
    }));
    
    $('body').append(form);
    form.submit();
    form.remove();
}

/**
 * Bulk delete
 */
function bulkDelete() {
    const selectedIds = $('.patient-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    if (selectedIds.length === 0) {
        showError('Please select patients to delete');
        return;
    }
    
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete ${selectedIds.length} patients. This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/patient_api.php',
                method: 'POST',
                data: {
                    action: 'bulk_delete',
                    ids: selectedIds.join(',')
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccess(`${selectedIds.length} patients deleted successfully`);
                        loadPatients();
                        loadStats();
                        $('#bulkActions').hide();
                    } else {
                        showError(response.message || 'Failed to delete patients');
                    }
                },
                error: function() {
                    showError('Failed to delete patients');
                }
            });
        }
    });
}

/**
 * Edit from view modal
 */
function editFromView() {
    if (currentViewPatientId) {
        $('#viewPatientModal').modal('hide');
        editPatient(currentViewPatientId);
    }
}

/**
 * Print patient details
 */
function printPatientDetails() {
    const content = $('#patientViewContent').html();
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Patient Details</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
                <style>
                    @media print {
                        .no-print { display: none; }
                        body { font-size: 12px; }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2>Patient Details</h2>
                    ${content}
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

/**
 * Utility Functions
 */

function resetForm() {
    $('#patientForm')[0].reset();
    $('#patientId').val('');
    generateUHID();
}

function populateForm(patient) {
    $('#patientId').val(patient.id);
    $('#patientName').val(patient.name);
    $('#patientUHID').val(patient.uhid);
    $('#patientMobile').val(patient.mobile);
    $('#patientEmail').val(patient.email);
    $('#patientAge').val(patient.age);
    $('#patientAgeUnit').val(patient.age_unit);
    $('#patientGender').val(patient.gender);
    $('#patientFatherHusband').val(patient.father_husband);
    $('#patientAddress').val(patient.address);
}

function showLoading() {
    $('#loadingIndicator').show();
    $('#patientsTable').hide();
    $('#noDataMessage').hide();
}

function hideLoading() {
    $('#loadingIndicator').hide();
}

function showNoData() {
    $('#patientsTable').hide();
    $('#noDataMessage').show();
    $('#paginationInfo').html('Showing 0 to 0 of 0 entries');
    $('#pagination').html('');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function getGenderBadgeClass(gender) {
    switch (gender) {
        case 'Male': return 'primary';
        case 'Female': return 'pink';
        case 'Other': return 'secondary';
        default: return 'secondary';
    }
}

function showSuccess(message) {
    if (typeof toastr !== 'undefined') {
        toastr.success(message);
    } else {
        alert(message);
    }
}

function showError(message) {
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        alert(message);
    }
}

function showInfo(message) {
    if (typeof toastr !== 'undefined') {
        toastr.info(message);
    } else {
        alert(message);
    }
}
