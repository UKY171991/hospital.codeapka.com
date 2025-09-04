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
                        patientTableManager.refreshData();
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
