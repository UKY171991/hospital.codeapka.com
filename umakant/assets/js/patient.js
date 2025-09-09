// Enhanced Patient Management with AJAX and Toaster Alerts
let patientTableManager;
let selectedPatients = new Set();

$(document).ready(function() {
    // Initialize enhanced table manager
    patientTableManager = new EnhancedTableManager({
        tableSelector: '#patientsTable',
        apiEndpoint: 'ajax/patient_api.php',
        entityName: 'patient',
        entityNamePlural: 'patients',
        viewFields: ['uhid', 'name', 'mobile', 'email', 'age', 'age_unit', 'gender', 'father_husband', 'address', 'added_by', 'created_at']
    });
    
    loadPatientStats();
    bindPatientEvents();
});

function loadPatientStats() {
    $.get('ajax/patient_api.php?action=stats', function(response) {
        if (response.success) {
            $('#totalPatients').text(response.data.total || 0);
            $('#todayPatients').text(response.data.today || 0);
            $('#malePatients').text(response.data.male || 0);
            $('#femalePatients').text(response.data.female || 0);
        }
    }).fail(function() {
        showError('Failed to load patient statistics');
    });
}

function loadPatients() {
    if (patientTableManager) {
        patientTableManager.loadData();
    } else {
        console.error('Patient table manager not initialized');
    }
}

function bindPatientEvents() {
    // Individual selection
    $(document).on('change', '.patient-checkbox', function() {
        const patientId = $(this).val();
        const isChecked = $(this).is(':checked');
        
        if (isChecked) {
            selectedPatients.add(patientId);
            $(this).closest('tr').addClass('row-selected');
        } else {
            selectedPatients.delete(patientId);
            $(this).closest('tr').removeClass('row-selected');
        }
        
        updateBulkActions();
    });
    
    // Form submission
    $('#patientForm').on('submit', function(e) {
        e.preventDefault();
        savePatient();
    });
    
    // Filter events
    $('#genderFilter, #ageRangeFilter, #dateFilter').on('change', function() {
        applyFilters();
    });
    
    $('#patientsSearch').on('input', function() {
        applyFilters();
    });

    // Populate Added By dropdown and reload when changed
    loadAddedByOptions();
    $(document).on('change', '#filterAddedBy', function() {
        applyFilters();
    });
}

function selectAllPatients() {
    $('.patient-checkbox').prop('checked', true).trigger('change');
    showInfo('All patients selected');
}

function deselectAllPatients() {
    $('.patient-checkbox').prop('checked', false).trigger('change');
    selectedPatients.clear();
    $('.row-selected').removeClass('row-selected');
    updateBulkActions();
    showInfo('All patients deselected');
}

function updateBulkActions() {
    const selectedCount = selectedPatients.size;
    const bulkActions = $('.bulk-actions');
    
    if (selectedCount > 0) {
        bulkActions.addClass('show');
        bulkActions.find('.selected-count').text(selectedCount);
    } else {
        bulkActions.removeClass('show');
    }
}

function openAddPatientModal() {
    $('#patientForm')[0].reset();
    $('#patientId').val('');
    $('#patientModalLabel .modal-title-text').text('Add New Patient');
    $('#patientModal').modal('show');
}

function viewPatient(id) {
    showLoading();
    
    $.get('ajax/patient_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                displayPatientDetails(response.data);
                $('#viewPatientModal').modal('show');
                showSuccess('Patient details loaded');
            } else {
                showError('Failed to load patient details: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error loading patient details');
        })
        .always(function() {
            hideLoading();
        });
}

function displayPatientDetails(patient) {
    const detailsHtml = `
        <div class="detail-item">
            <div class="detail-label">UHID</div>
            <div class="detail-value">${patient.uhid || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Full Name</div>
            <div class="detail-value">${patient.name || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Mobile Number</div>
            <div class="detail-value">
                ${patient.mobile ? `<a href="tel:${patient.mobile}">${patient.mobile}</a>` : 'N/A'}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Email</div>
            <div class="detail-value">
                ${patient.email ? `<a href="mailto:${patient.email}">${patient.email}</a>` : 'N/A'}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Age</div>
            <div class="detail-value">${patient.age ? `${patient.age} ${patient.age_unit || 'Years'}` : 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Gender</div>
            <div class="detail-value">
                <span class="status-badge ${patient.gender ? 'status-active' : 'status-inactive'}">
                    ${patient.gender || 'Not specified'}
                </span>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Father/Husband</div>
            <div class="detail-value">${patient.father_husband || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Address</div>
            <div class="detail-value">${patient.address || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Registration Date</div>
            <div class="detail-value">${patient.created_at ? new Date(patient.created_at).toLocaleDateString() : 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Added By</div>
            <div class="detail-value">${patient.added_by_name || patient.added_by || 'System'}</div>
        </div>
    `;
    
    $('#patientViewDetails').html(detailsHtml);
    
    // Store patient ID for edit function
    $('#viewPatientModal').data('patient-id', patient.id);
}

function editPatient(id) {
    showLoading();
    
    $.get('ajax/patient_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                populatePatientForm(response.data);
                $('#patientModalLabel .modal-title-text').text('Edit Patient');
                $('#patientModal').modal('show');
                showSuccess('Patient data loaded for editing');
            } else {
                showError('Failed to load patient data: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error loading patient data');
        })
        .always(function() {
            hideLoading();
        });
}

function editPatientFromView() {
    const patientId = $('#viewPatientModal').data('patient-id');
    $('#viewPatientModal').modal('hide');
    setTimeout(() => editPatient(patientId), 300);
}

function populatePatientForm(patient) {
    $('#patientId').val(patient.id);
    $('#patientName').val(patient.name);
    $('#patientMobile').val(patient.mobile);
    $('#patientEmail').val(patient.email);
    $('#patientAge').val(patient.age);
    $('#patientAgeUnit').val(patient.age_unit);
    $('#patientGender').val(patient.gender);
    $('#patientFatherHusband').val(patient.father_husband);
    $('#patientAddress').val(patient.address);
}

function savePatient() {
    const formData = new FormData($('#patientForm')[0]);
    formData.append('action', 'save');
    
    const isEdit = $('#patientId').val() !== '';
    
    showLoading();
    
    $.ajax({
        url: 'ajax/patient_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            $('#patientModal').modal('hide');
            patientTableManager.refreshData();
            loadPatientStats();
            
            const message = isEdit ? 'Patient updated successfully' : 'Patient added successfully';
            showSuccess(message);
            
            // Reset form
            $('#patientForm')[0].reset();
            $('#patientId').val('');
        } else {
            showError('Failed to save patient: ' + response.message);
        }
    })
    .fail(function() {
        showError('Error saving patient');
    })
    .always(function() {
        hideLoading();
    });
}

function deletePatient(id) {
    showConfirmDialog(
        'Delete Patient',
        'Are you sure you want to delete this patient? This action cannot be undone.',
        'danger'
    ).then(function(confirmed) {
        if (confirmed) {
            showLoading();
            
            $.post('ajax/patient_api.php', {action: 'delete', id: id})
                .done(function(response) {
                    if (response.success) {
                        // Refresh table data robustly and clear selection UI
                        try {
                            $('.selection-checkbox, #selectAll').prop('checked', false);
                            $('.bulk-actions').hide();
                            if (patientTableManager && patientTableManager.dataTable && patientTableManager.dataTable.ajax && typeof patientTableManager.dataTable.ajax.reload === 'function') {
                                patientTableManager.dataTable.ajax.reload(null, false);
                            } else if (patientTableManager && typeof patientTableManager.refreshData === 'function') {
                                patientTableManager.refreshData();
                            } else {
                                // fallback to loadData
                                patientTableManager.loadData && patientTableManager.loadData();
                            }
                        } catch (e) {
                            console.warn('Error refreshing table after delete:', e);
                            try { window.location.reload(); } catch(e){}
                        }

                        loadPatientStats();
                        showSuccess('Patient deleted successfully');
                    } else {
                        showError('Failed to delete patient: ' + response.message);
                    }
                })
                .fail(function() {
                    showError('Error deleting patient');
                })
                .always(function() {
                    hideLoading();
                });
        }
    });
}

function bulkDeletePatients() {
    if (selectedPatients.size === 0) {
        showWarning('Please select patients to delete');
        return;
    }
    
    const selectedCount = selectedPatients.size;
    showConfirmDialog(
        'Bulk Delete',
        `Are you sure you want to delete ${selectedCount} selected patients? This action cannot be undone.`,
        'danger'
    ).then(function(confirmed) {
        if (confirmed) {
            const ids = Array.from(selectedPatients);
            showLoading();
            
            $.post('ajax/patient_api.php', {action: 'bulk_delete', ids: ids})
                .done(function(response) {
                    if (response.success) {
                        selectedPatients.clear();
                        updateBulkActions();
                        patientTableManager.refreshData();
                        loadPatientStats();
                        showSuccess(`${selectedCount} patients deleted successfully`);
                    } else {
                        showError('Failed to delete patients: ' + response.message);
                    }
                })
                .fail(function() {
                    showError('Error deleting patients');
                })
                .always(function() {
                    hideLoading();
                });
        }
    });
}

function bulkExportPatients() {
    if (selectedPatients.size === 0) {
        showWarning('Please select patients to export');
        return;
    }
    
    const ids = Array.from(selectedPatients);
    showLoading();
    
    $.get('ajax/patient_api.php', {action: 'bulk_export', ids: ids})
        .done(function(response) {
            if (response.success) {
                downloadCSV(response.data, 'selected_patients.csv');
                showSuccess('Patients exported successfully');
            } else {
                showError('Failed to export patients: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error exporting patients');
        })
        .always(function() {
            hideLoading();
        });
}

function exportPatients() {
    showLoading();
    
    $.get('ajax/patient_api.php', {action: 'export'})
        .done(function(response) {
            if (response.success) {
                downloadCSV(response.data, 'all_patients.csv');
                showSuccess('All patients exported successfully');
            } else {
                showError('Failed to export patients: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error exporting patients');
        })
        .always(function() {
            hideLoading();
        });
}

function refreshPatients() {
    patientTableManager.refreshData();
    loadPatientStats();
    showInfo('Patient data refreshed');
}

function applyFilters() {
    const gender = $('#genderFilter').val();
    const ageRange = $('#ageRangeFilter').val();
    const date = $('#dateFilter').val();
    const search = $('#patientsSearch').val();
    
    // Apply filters to DataTable
    patientTableManager.dataTable
        .columns(4).search(gender)
        .columns(5).search(date)
        .search(search)
        .draw();
}

function clearFilters() {
    $('#genderFilter').val('');
    $('#ageRangeFilter').val('');
    $('#dateFilter').val('');
    $('#patientsSearch').val('');
    
    patientTableManager.dataTable
        .search('')
        .columns().search('')
        .draw();
        
    showInfo('Filters cleared');
}

function printPatientDetails() {
    const patientId = $('#viewPatientModal').data('patient-id');
    if (patientId) {
        window.open(`print_patient.php?id=${patientId}`, '_blank');
    }
}

// Utility functions
function showLoading() {
    if ($('.loading-overlay').length === 0) {
        $('body').append('<div class="loading-overlay"><div class="spinner"></div></div>');
    }
}

function hideLoading() {
    $('.loading-overlay').remove();
}

function showSuccess(message) {
    toastr.success(message, 'Success', {
        timeOut: 3000,
        positionClass: 'toast-top-right',
        progressBar: true
    });
}

function showError(message) {
    toastr.error(message, 'Error', {
        timeOut: 5000,
        positionClass: 'toast-top-right',
        progressBar: true
    });
}

function showWarning(message) {
    toastr.warning(message, 'Warning', {
        timeOut: 4000,
        positionClass: 'toast-top-right',
        progressBar: true
    });
}

function showInfo(message) {
    toastr.info(message, 'Info', {
        timeOut: 3000,
        positionClass: 'toast-top-right',
        progressBar: true
    });
}

function showConfirmDialog(title, message, type = 'warning') {
    return new Promise((resolve) => {
        const modalId = 'confirmModal_' + Date.now();
        const typeClass = type === 'danger' ? 'btn-danger' : 'btn-warning';
        const iconClass = type === 'danger' ? 'fa-exclamation-triangle' : 'fa-question-circle';
        
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-${type} text-white">
                            <h5 class="modal-title">
                                <i class="fas ${iconClass} mr-2"></i>${title}
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                            <button type="button" class="btn ${typeClass}" id="confirmBtn">
                                <i class="fas fa-check"></i> Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        $(`#${modalId}`).modal('show');
        
        $(`#${modalId} #confirmBtn`).on('click', function() {
            $(`#${modalId}`).modal('hide');
            resolve(true);
        });
        
        $(`#${modalId}`).on('hidden.bs.modal', function() {
            $(this).remove();
            resolve(false);
        });
    });
}

function downloadCSV(data, filename) {
    if (!data || data.length === 0) {
        showWarning('No data to export');
        return;
    }

    const headers = Object.keys(data[0]);
    let csv = headers.join(',') + '\n';
    
    data.forEach(row => {
        const values = headers.map(header => {
            const value = row[header] || '';
            return `"${String(value).replace(/"/g, '""')}"`;
        });
        csv += values.join(',') + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    a.click();
    window.URL.revokeObjectURL(url);
}

function applyFilters() {
    const searchTerm = $('#patientsSearch').val().trim();
    const gender = $('#genderFilter').val();
    const ageRange = $('#ageRangeFilter').val();
    const date = $('#dateFilter').val();
    
    // Build search parameters
    const params = new URLSearchParams({
        page: currentPage,
        limit: recordsPerPage
    });
    
    if (searchTerm) params.append('search', searchTerm);
    if (gender) params.append('gender', gender);
    if (ageRange) params.append('age_range', ageRange);
    if (date) params.append('date', date);

    // Use the table manager to apply filters if available
    if (patientTableManager) {
    // set custom params on the manager then reload
    const addedBy = $('#filterAddedBy').val();
    patientTableManager.extraParams = patientTableManager.extraParams || {};
    if (addedBy) patientTableManager.extraParams.added_by = addedBy; else delete patientTableManager.extraParams.added_by;
    patientTableManager.loadData();
    } else {
        // Fallback: load data manually
        $.get(`ajax/patient_api.php?action=list&${params}`)
            .done(function(response) {
                if (response.success) {
                    // Handle response data
                    APP_LOG('Patients loaded:', response.data);
                } else {
                    showError('Error loading patients: ' + response.message);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                if (jqXHR && jqXHR.status === 0 && navigator.onLine) {
                    console.warn('Suppressed patient load toast for XHR status 0 while online. Likely aborted by extension or network probe.');
                } else {
                    showError('Failed to load patients');
                }
            });
    }
}

// Load users to populate the Added By dropdown
function loadAddedByOptions(){
    // Request a larger page to ensure we get all users for the dropdown; log response for debugging
    $.ajax({
        url: 'ajax/user_api.php',
        method: 'GET',
        dataType: 'json',
        data: { action: 'list', start: 0, length: 1000 },
        cache: false,
        timeout: 8000
    }).done(function(r){
        console.debug('loadAddedByOptions response:', r);
        const sel = $('#filterAddedBy');
        sel.find('option:not(:first)').remove();

        if (r && r.success && Array.isArray(r.data) && r.data.length > 0) {
            r.data.forEach(function(u){
                const label = u.full_name || u.username || u.email || ('user-'+u.id);
                sel.append($('<option>').val(u.id).text(label));
            });
        } else {
            console.warn('No users returned for Added By dropdown', r);
            // leave the default 'All' option but still provide a visible console hint
        }
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.warn('Failed to load Added By options', textStatus, errorThrown);
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
    showLoading();
    $.ajax({
        url: 'ajax/patient_api.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'delete', id: id },
        success: function(response) {
            if (response && response.success) {
                showAlert('Patient deleted successfully!', 'success');
                // Refresh table via manager if available, otherwise fallback
                try {
                    if (patientTableManager && patientTableManager.dataTable && patientTableManager.dataTable.ajax && typeof patientTableManager.dataTable.ajax.reload === 'function') {
                        patientTableManager.dataTable.ajax.reload(null, false);
                    } else if (patientTableManager && typeof patientTableManager.refreshData === 'function') {
                        patientTableManager.refreshData();
                    } else if (typeof loadPatients === 'function') {
                        loadPatients();
                    }
                } catch (e) {
                    console.warn('Error refreshing list after delete:', e);
                }
                try { updateStats(); } catch(e){}
            } else {
                const msg = (response && response.message) ? response.message : 'Unknown error';
                showAlert('Error deleting patient: ' + msg, 'error');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Delete AJAX error', textStatus, errorThrown, jqXHR.responseText);
            showAlert('Failed to delete patient: ' + (errorThrown || textStatus), 'error');
        },
        complete: function() {
            hideLoading();
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