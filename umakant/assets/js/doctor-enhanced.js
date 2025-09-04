// Enhanced Doctor Management with AJAX and Toaster Alerts
let doctorTableManager;
let selectedDoctors = new Set();

$(document).ready(function() {
    // Initialize enhanced table manager with server-side processing
    doctorTableManager = new EnhancedTableManager({
        tableSelector: '#doctorsTable',
        apiEndpoint: 'ajax/doctor_api.php',
        entityName: 'doctor',
        entityNamePlural: 'doctors',
        serverSide: false, // Use client-side for now to avoid server-side issues
        viewFields: ['id', 'name', 'qualification', 'specialization', 'hospital', 'contact_no', 'email', 'percent', 'registration_no', 'address', 'created_at']
    });
    
    loadDoctorStats();
    bindDoctorEvents();
});

function loadDoctorStats() {
    $.get('ajax/doctor_api.php?action=stats', function(response) {
        if (response.success) {
            $('#totalDoctors').text(response.data.total || 0);
            $('#activeDoctors').text(response.data.active || 0);
            $('#specializations').text(response.data.specializations || 0);
            $('#hospitals').text(response.data.hospitals || 0);
        }
    }).fail(function() {
        showError('Failed to load doctor statistics');
    });
}

function bindDoctorEvents() {
    // Individual selection
    $(document).on('change', '.doctor-checkbox', function() {
        const doctorId = $(this).val();
        const isChecked = $(this).is(':checked');
        
        if (isChecked) {
            selectedDoctors.add(doctorId);
            $(this).closest('tr').addClass('row-selected');
        } else {
            selectedDoctors.delete(doctorId);
            $(this).closest('tr').removeClass('row-selected');
        }
        
        updateBulkActions();
    });
    
    // Form submission
    $('#doctorForm').on('submit', function(e) {
        e.preventDefault();
        saveDoctor();
    });
    
    // Filter events
    $('#specializationFilter, #hospitalFilter').on('change', function() {
        applyFilters();
    });
    
    $('#doctorsSearch').on('input', function() {
        applyFilters();
    });
}

function selectAllDoctors() {
    $('.doctor-checkbox').prop('checked', true).trigger('change');
    showInfo('All doctors selected');
}

function deselectAllDoctors() {
    $('.doctor-checkbox').prop('checked', false).trigger('change');
    selectedDoctors.clear();
    $('.row-selected').removeClass('row-selected');
    updateBulkActions();
    showInfo('All doctors deselected');
}

function updateBulkActions() {
    const selectedCount = selectedDoctors.size;
    const bulkActions = $('.bulk-actions');
    
    if (selectedCount > 0) {
        bulkActions.addClass('show');
        bulkActions.find('.selected-count').text(selectedCount);
    } else {
        bulkActions.removeClass('show');
    }
}

function openAddDoctorModal() {
    $('#doctorForm')[0].reset();
    $('#doctorId').val('');
    $('#doctorModalLabel .modal-title-text').text('Add New Doctor');
    $('#doctorModal').modal('show');
}

function viewDoctor(id) {
    showLoading();
    
    $.get('ajax/doctor_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                displayDoctorDetails(response.data);
                $('#viewDoctorModal').modal('show');
                showSuccess('Doctor details loaded');
            } else {
                showError('Failed to load doctor details: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error loading doctor details');
        })
        .always(function() {
            hideLoading();
        });
}

function displayDoctorDetails(doctor) {
    const detailsHtml = `
        <div class="detail-item">
            <div class="detail-label">Doctor ID</div>
            <div class="detail-value">${doctor.id || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Full Name</div>
            <div class="detail-value">${doctor.name || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Qualification</div>
            <div class="detail-value">${doctor.qualification || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Specialization</div>
            <div class="detail-value">
                <span class="status-badge status-active">${doctor.specialization || 'N/A'}</span>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Hospital</div>
            <div class="detail-value">${doctor.hospital || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Contact Number</div>
            <div class="detail-value">
                ${doctor.contact ? `<a href="tel:${doctor.contact}">${doctor.contact}</a>` : 'N/A'}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Email</div>
            <div class="detail-value">
                ${doctor.email ? `<a href="mailto:${doctor.email}">${doctor.email}</a>` : 'N/A'}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Commission %</div>
            <div class="detail-value">${doctor.percent ? doctor.percent + '%' : 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Registration No</div>
            <div class="detail-value">${doctor.registration_no || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Address</div>
            <div class="detail-value">${doctor.address || 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Joined Date</div>
            <div class="detail-value">${doctor.created_at ? new Date(doctor.created_at).toLocaleDateString() : 'N/A'}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Added By</div>
            <div class="detail-value">${doctor.added_by_name || doctor.added_by || 'System'}</div>
        </div>
    `;
    
    $('#doctorViewDetails').html(detailsHtml);
    
    // Store doctor ID for edit function
    $('#viewDoctorModal').data('doctor-id', doctor.id);
}

function editDoctor(id) {
    showLoading();
    
    $.get('ajax/doctor_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                populateDoctorForm(response.data);
                $('#doctorModalLabel .modal-title-text').text('Edit Doctor');
                $('#doctorModal').modal('show');
                showSuccess('Doctor data loaded for editing');
            } else {
                showError('Failed to load doctor data: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error loading doctor data');
        })
        .always(function() {
            hideLoading();
        });
}

function editDoctorFromView() {
    const doctorId = $('#viewDoctorModal').data('doctor-id');
    $('#viewDoctorModal').modal('hide');
    setTimeout(() => editDoctor(doctorId), 300);
}

function populateDoctorForm(doctor) {
    $('#doctorId').val(doctor.id);
    $('#doctorName').val(doctor.name);
    $('#doctorQualification').val(doctor.qualification);
    $('#doctorSpecialization').val(doctor.specialization);
    $('#doctorHospital').val(doctor.hospital);
    $('#doctorContact').val(doctor.contact);
    $('#doctorEmail').val(doctor.email);
    $('#doctorPercent').val(doctor.percent);
    $('#doctorRegistration').val(doctor.registration_no);
    $('#doctorAddress').val(doctor.address);
}

function saveDoctor() {
    const formData = new FormData($('#doctorForm')[0]);
    formData.append('action', 'save');
    
    const isEdit = $('#doctorId').val() !== '';
    
    showLoading();
    
    $.ajax({
        url: 'ajax/doctor_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            $('#doctorModal').modal('hide');
            doctorTableManager.refreshData();
            loadDoctorStats();
            
            const message = isEdit ? 'Doctor updated successfully' : 'Doctor added successfully';
            showSuccess(message);
            
            // Reset form
            $('#doctorForm')[0].reset();
            $('#doctorId').val('');
        } else {
            showError('Failed to save doctor: ' + response.message);
        }
    })
    .fail(function() {
        showError('Error saving doctor');
    })
    .always(function() {
        hideLoading();
    });
}

function deleteDoctor(id) {
    showConfirmDialog(
        'Delete Doctor',
        'Are you sure you want to delete this doctor? This action cannot be undone.',
        'danger'
    ).then(function(confirmed) {
        if (confirmed) {
            showLoading();
            
            $.post('ajax/doctor_api.php', {action: 'delete', id: id})
                .done(function(response) {
                    if (response.success) {
                        doctorTableManager.refreshData();
                        loadDoctorStats();
                        showSuccess('Doctor deleted successfully');
                    } else {
                        showError('Failed to delete doctor: ' + response.message);
                    }
                })
                .fail(function() {
                    showError('Error deleting doctor');
                })
                .always(function() {
                    hideLoading();
                });
        }
    });
}

function bulkDeleteDoctors() {
    if (selectedDoctors.size === 0) {
        showWarning('Please select doctors to delete');
        return;
    }
    
    const selectedCount = selectedDoctors.size;
    showConfirmDialog(
        'Bulk Delete',
        `Are you sure you want to delete ${selectedCount} selected doctors? This action cannot be undone.`,
        'danger'
    ).then(function(confirmed) {
        if (confirmed) {
            const ids = Array.from(selectedDoctors);
            showLoading();
            
            $.post('ajax/doctor_api.php', {action: 'bulk_delete', ids: ids})
                .done(function(response) {
                    if (response.success) {
                        selectedDoctors.clear();
                        updateBulkActions();
                        doctorTableManager.refreshData();
                        loadDoctorStats();
                        showSuccess(`${selectedCount} doctors deleted successfully`);
                    } else {
                        showError('Failed to delete doctors: ' + response.message);
                    }
                })
                .fail(function() {
                    showError('Error deleting doctors');
                })
                .always(function() {
                    hideLoading();
                });
        }
    });
}

function bulkExportDoctors() {
    if (selectedDoctors.size === 0) {
        showWarning('Please select doctors to export');
        return;
    }
    
    const ids = Array.from(selectedDoctors);
    showLoading();
    
    $.get('ajax/doctor_api.php', {action: 'bulk_export', ids: ids})
        .done(function(response) {
            if (response.success) {
                downloadCSV(response.data, 'selected_doctors.csv');
                showSuccess('Doctors exported successfully');
            } else {
                showError('Failed to export doctors: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error exporting doctors');
        })
        .always(function() {
            hideLoading();
        });
}

function exportDoctors() {
    showLoading();
    
    $.get('ajax/doctor_api.php', {action: 'export'})
        .done(function(response) {
            if (response.success) {
                downloadCSV(response.data, 'all_doctors.csv');
                showSuccess('All doctors exported successfully');
            } else {
                showError('Failed to export doctors: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error exporting doctors');
        })
        .always(function() {
            hideLoading();
        });
}

function refreshDoctors() {
    doctorTableManager.refreshData();
    loadDoctorStats();
    showInfo('Doctor data refreshed');
}

function applyFilters() {
    const specialization = $('#specializationFilter').val();
    const hospital = $('#hospitalFilter').val();
    const search = $('#doctorsSearch').val();
    
    // Apply filters to DataTable
    doctorTableManager.dataTable
        .columns(4).search(specialization)
        .columns(5).search(hospital)
        .search(search)
        .draw();
}

function clearFilters() {
    $('#specializationFilter').val('');
    $('#hospitalFilter').val('');
    $('#doctorsSearch').val('');
    
    doctorTableManager.dataTable
        .search('')
        .columns().search('')
        .draw();
        
    showInfo('Filters cleared');
}

function printDoctorDetails() {
    const doctorId = $('#viewDoctorModal').data('doctor-id');
    if (doctorId) {
        window.open(`print_doctor.php?id=${doctorId}`, '_blank');
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
