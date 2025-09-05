// Enhanced Patient Management with AJAX and Toaster Alerts
let selectedPatients = new Set();
let patientsDataTable;

// Robust table reload helper: will try DataTable api, fallback to window.location.reload
function reloadPatientsTable() {
    try {
        if (patientsDataTable && typeof patientsDataTable.ajax === 'object' && typeof patientsDataTable.ajax.reload === 'function') {
            patientsDataTable.ajax.reload(null, false); // keep current paging
            return;
        }

        // If other table manager variable exists (patientsDataTable may be null), try global table manager
        if (typeof patientTableManager !== 'undefined' && patientTableManager && typeof patientTableManager.refreshData === 'function') {
            patientTableManager.refreshData();
            return;
        }

        // Fallback: reload the page to refresh data
        window.location.reload();
    } catch (err) {
        console.error('reloadPatientsTable error:', err);
        try { window.location.reload(); } catch(e) { /* last resort */ }
    }
}

// Utility function wrappers for backward compatibility
function showSuccess(message) {
    if (typeof toastr !== 'undefined') {
        toastr.success(message);
    } else if (window.utils) {
        utils.showSuccess(message);
    } else {
        alert(message);
    }
}

function showError(message) {
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else if (window.utils) {
        utils.showError(message);
    } else {
        alert('Error: ' + message);
    }
}

function showWarning(message) {
    if (typeof toastr !== 'undefined') {
        toastr.warning(message);
    } else if (window.utils) {
        utils.showWarning(message);
    } else {
        alert('Warning: ' + message);
    }
}

function showInfo(message) {
    if (typeof toastr !== 'undefined') {
        toastr.info(message);
    } else if (window.utils) {
        utils.showInfo(message);
    } else {
        alert('Info: ' + message);
    }
}

function showLoading() {
    if (window.utils) {
        utils.showLoading();
    } else {
        $('#loadingIndicator').show();
    }
}

function hideLoading() {
    if (window.utils) {
        utils.hideLoading();
    } else {
        $('#loadingIndicator').hide();
    }
}

function showConfirmDialog(title, message, type = 'danger') {
    return new Promise((resolve) => {
        if (window.utils && utils.confirm) {
            utils.confirm(message, title).then(resolve);
        } else {
            resolve(confirm(message));
        }
    });
}

$(document).ready(function() {
    // Add global loading indicator if it doesn't exist
    if ($('#loadingIndicator').length === 0) {
        $('body').append(`
            <div id="loadingIndicator" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; background: rgba(0,0,0,0.8); color: white; padding: 20px; border-radius: 10px;">
                <i class="fas fa-spinner fa-spin"></i> Loading...
            </div>
        `);
    }
    
    // Initialize DataTable properly
    initializePatientsTable();
    loadPatientStats();
    bindPatientEvents();
});

function initializePatientsTable() {
    patientsDataTable = $('#patientsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'ajax/patient_api.php',
            type: 'POST',
            data: function(d) {
                d.action = 'list';
                return d;
            },
            dataSrc: function(json) {
                if (json.success) {
                    return json.data || [];
                } else {
                    showError('Failed to load patients: ' + (json.message || 'Unknown error'));
                    return [];
                }
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                className: 'text-center',
                width: '40px',
                render: function(data, type, row) {
                    return `<input type="checkbox" class="patient-checkbox" value="${row.id}">`;
                }
            },
            {
                data: 'uhid',
                render: function(data, type, row) {
                    return data || 'N/A';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `<div class="patient-info">
                        <strong>${row.name || 'N/A'}</strong><br>
                        <small class="text-muted">${row.mobile || 'No mobile'}</small>
                    </div>`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `<div class="contact-info">
                        <small>${row.mobile || 'N/A'}<br>${row.email || 'No email'}</small>
                    </div>`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `${row.age || 'N/A'} ${row.age_unit || ''}<br>
                            <small class="text-muted">${row.sex || row.gender || 'N/A'}</small>`;
                }
            },
            {
                data: 'address',
                render: function(data, type, row) {
                    return data ? (data.length > 30 ? data.substring(0, 30) + '...' : data) : 'N/A';
                }
            },
            {
                data: 'created_at',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString() : 'N/A';
                }
            },
            {
                data: 'added_by',
                render: function(data, type, row) {
                    return row.added_by_name || data || 'System';
                }
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                width: '120px',
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info btn-sm" onclick="viewPatient(${row.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editPatient(${row.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deletePatient(${row.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading patients...</div>',
            emptyTable: 'No patients found',
            zeroRecords: 'No matching patients found'
        },
        order: [[1, 'desc']] // Order by UHID descending
    });
}

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
    
    // Select all checkbox
    $(document).on('change', '#selectAll', function() {
        const isChecked = $(this).is(':checked');
        $('.patient-checkbox').prop('checked', isChecked).trigger('change');
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
    
    // Bulk action buttons
    $('.bulk-export, .bulk-delete').on('click', function() {
        if ($(this).hasClass('bulk-export')) {
            bulkExportPatients();
        } else if ($(this).hasClass('bulk-delete')) {
            bulkDeletePatients();
        }
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
    $('#modalTitle').text('Add New Patient');
    $('#patientModal').modal('show');
    
    // Generate UHID for new patient
    generateUHID();
}

function generateUHID() {
    // Generate a random UHID
    const uhid = 'P' + Date.now().toString().slice(-8);
    $('#patientUHID').val(uhid);
}

function applyFilters() {
    if (patientsDataTable) {
        const searchValue = $('#patientsSearch').val();
        patientsDataTable.search(searchValue).draw();
    }
}

function clearFilters() {
    $('#patientsSearch').val('');
    $('#genderFilter').val('');
    $('#ageRangeFilter').val('');
    $('#dateFilter').val('');
    if (patientsDataTable) {
        patientsDataTable.search('').draw();
    }
}

function exportPatients() {
    showLoading();
    
    $.get('ajax/patient_api.php?action=export')
        .done(function(response) {
            if (response.success && response.data) {
                exportToCSV(response.data, 'patients_export.csv');
                showSuccess('Patients exported successfully');
            } else {
                showError('Failed to export patients: ' + (response.message || 'Unknown error'));
            }
        })
        .fail(function() {
            showError('Error exporting patients');
        })
        .always(function() {
            hideLoading();
        });
}

function exportToCSV(data, filename) {
    const csvRows = [];
    
    // Headers
    const headers = ['UHID', 'Name', 'Mobile', 'Email', 'Age', 'Age Unit', 'Gender', 'Father/Husband', 'Address', 'Registration Date'];
    csvRows.push(headers.join(','));
    
    // Data rows
    data.forEach(row => {
        const values = [
            row.uhid || '',
            row.name || '',
            row.mobile || '',
            row.email || '',
            row.age || '',
            row.age_unit || '',
            row.gender || '',
            row.father_husband || '',
            (row.address || '').replace(/,/g, ';'),
            row.created_at ? new Date(row.created_at).toLocaleDateString() : ''
        ];
        csvRows.push(values.map(val => `"${val}"`).join(','));
    });
    
    const csvContent = csvRows.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printPatientDetails() {
    const printContent = $('#patientViewDetails').html();
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Patient Details</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .detail-item { margin-bottom: 10px; }
                .detail-label { font-weight: bold; }
                .detail-value { margin-left: 10px; }
            </style>
        </head>
        <body>
            <h2>Patient Details</h2>
            ${printContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
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
                <span class="status-badge ${(patient.sex || patient.gender) ? 'status-active' : 'status-inactive'}">
                    ${patient.sex || patient.gender || 'Not specified'}
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
                $('#modalTitle').text('Edit Patient');
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
    $('#patientGender').val(patient.sex || patient.gender);
    $('#patientFatherHusband').val(patient.father_husband);
    $('#patientAddress').val(patient.address);
    $('#patientUHID').val(patient.uhid);
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
            reloadPatientsTable();
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
                        // Remove row immediately as a visual fallback
                        try {
                            const rowEl = $('#patientsTable').find(`input.patient-checkbox[value="${id}"]`).closest('tr');
                            if (rowEl.length && patientsDataTable && typeof patientsDataTable.row === 'function') {
                                patientsDataTable.row(rowEl).remove().draw(false);
                            }
                        } catch (e) { console.warn('row remove fallback failed', e); }

                        reloadPatientsTable();
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
                        // Remove rows immediately as a visual fallback
                        try {
                            const idsArr = ids.map(id => String(id));
                            $('#patientsTable').find('input.patient-checkbox').each(function() {
                                const val = $(this).val();
                                if (idsArr.indexOf(String(val)) !== -1) {
                                    const r = $(this).closest('tr');
                                    if (patientsDataTable && typeof patientsDataTable.row === 'function') {
                                        patientsDataTable.row(r).remove();
                                    } else {
                                        r.remove();
                                    }
                                }
                            });
                            if (patientsDataTable && typeof patientsDataTable.draw === 'function') patientsDataTable.draw(false);
                        } catch (e) { console.warn('bulk row remove fallback failed', e); }

                        selectedPatients.clear();
                        updateBulkActions();
                        reloadPatientsTable();
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
    patientsDataTable.ajax.reload();
    loadPatientStats();
    showInfo('Patient data refreshed');
}

function applyFilters() {
    const gender = $('#genderFilter').val();
    const ageRange = $('#ageRangeFilter').val();
    const date = $('#dateFilter').val();
    const search = $('#patientsSearch').val();
    
    // Apply search to DataTable
    patientsDataTable.search(search).draw();
    
    // Note: Advanced filtering would require server-side implementation
    // For now, just apply the search term
}

function clearFilters() {
    $('#genderFilter').val('');
    $('#ageRangeFilter').val('');
    $('#dateFilter').val('');
    $('#patientsSearch').val('');
    
    patientsDataTable.search('').draw();
        
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
