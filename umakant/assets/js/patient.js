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
    
    // Store patient ID for view function
    $('#viewPatientModal').data('patient-id', patient.id);
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

function populatePatientsTable(patients) {
    const tbody = $('#patientsTableBody');
    tbody.empty();

    if (patients.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="9" class="text-center py-4">
                    <i class="fas fa-users fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">No patients found</p>
                </td>
            </tr>
        `);
        return;
    }

    patients.forEach(patient => {
        const row = `
            <tr data-id="${patient.id}">
                <td class="text-center">
                    <input type="checkbox" class="patient-checkbox" value="${patient.id}">
                </td>
                <td>${patient.uhid || 'N/A'}</td>
                <td>
                    <div class="font-weight-bold">${patient.name || 'N/A'}</div>
                    <small class="text-muted">${patient.father_husband || ''}</small>
                </td>
                <td>
                    <div>${patient.mobile || 'N/A'}</div>
                    <small class="text-muted">${patient.email || ''}</small>
                </td>
                <td>
                    ${patient.age ? `${patient.age} ${patient.age_unit || 'years'}` : 'N/A'} 
                    <span class="badge ${patient.gender === 'Male' ? 'bg-primary' : patient.gender === 'Female' ? 'bg-pink' : 'bg-secondary'}">
                        ${patient.gender || 'N/A'}
                    </span>
                </td>
                <td class="text-truncate" style="max-width: 200px;" title="${patient.address || ''}">
                    ${patient.address || 'N/A'}
                </td>
                <td>${formatDateTime(patient.created_at) || 'N/A'}</td>
                <td>${patient.added_by_username || 'System'}</td>
                <td class="text-nowrap">
                    <button class="btn btn-xs btn-info view-patient" data-id="${patient.id}" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-xs btn-danger delete-patient" data-id="${patient.id}" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// Event delegation for view buttons
$(document).on('click', '.view-patient', function() {
    const patientId = $(this).data('id');
    if (patientId) {
        viewPatient(patientId);
    }
});

// Event delegation for delete buttons
$(document).on('click', '.delete-patient', function() {
    const patientId = $(this).data('id');
    if (patientId) {
        deletePatient(patientId);
    }
});

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

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}
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