// Enhanced Test Management with AJAX and Toaster Alerts
let testTableManager;
let selectedTests = new Set();

$(document).ready(function() {
    // Initialize enhanced table manager
    testTableManager = new EnhancedTableManager({
        tableSelector: '#testsTable',
        apiEndpoint: 'ajax/test_api.php',
        entityName: 'test',
        entityNamePlural: 'tests',
        viewFields: ['id', 'name', 'category_name', 'price', 'unit', 'method', 'min_male', 'max_male', 'min_female', 'max_female', 'added_by_username', 'created_at']
    });
    
    bindTestEvents();
    
    // Load categories after a short delay to ensure DOM is ready
    setTimeout(function() {
        loadCategories();
    }, 500);
});

function bindTestEvents() {
    // Individual selection
    $(document).on('change', '.test-checkbox', function() {
        const testId = $(this).val();
        const isChecked = $(this).is(':checked');
        
        if (isChecked) {
            selectedTests.add(testId);
            $(this).closest('tr').addClass('row-selected');
        } else {
            selectedTests.delete(testId);
            $(this).closest('tr').removeClass('row-selected');
        }
        
        updateBulkActions();
    });
    
    // Select all checkbox
    $('#selectAllTests').on('change', function() {
        if ($(this).is(':checked')) {
            selectAllTests();
        } else {
            deselectAllTests();
        }
    });
    
    // Form submission
    $('#testForm').on('submit', function(e) {
        e.preventDefault();
        saveTest();
    });
    
    // Filter handlers
    $('#categoryFilter, #genderFilter').on('change', function() {
        applyFilters();
    });
    
    $('#priceFilter').on('input', function() {
        applyFilters();
    });
}

function selectAllTests() {
    $('.test-checkbox').prop('checked', true).trigger('change');
    $('#selectAllTests').prop('checked', true);
    showInfo('All tests selected');
}

function deselectAllTests() {
    $('.test-checkbox').prop('checked', false).trigger('change');
    $('#selectAllTests').prop('checked', false);
    selectedTests.clear();
    $('.row-selected').removeClass('row-selected');
    updateBulkActions();
    showInfo('All tests deselected');
}

function updateBulkActions() {
    const selectedCount = selectedTests.size;
    const bulkActions = $('.bulk-actions');
    
    if (selectedCount > 0) {
        bulkActions.addClass('show');
        bulkActions.find('.selected-count').text(selectedCount);
    } else {
        bulkActions.removeClass('show');
    }
    
    // Update select all checkbox state
    const totalCheckboxes = $('.test-checkbox').length;
    const checkedCheckboxes = $('.test-checkbox:checked').length;
    
    if (checkedCheckboxes === 0) {
        $('#selectAllTests').prop('indeterminate', false).prop('checked', false);
    } else if (checkedCheckboxes === totalCheckboxes) {
        $('#selectAllTests').prop('indeterminate', false).prop('checked', true);
    } else {
        $('#selectAllTests').prop('indeterminate', true);
    }
}

function openAddTestModal() {
    $('#testForm')[0].reset();
    $('#testId').val('');
    $('#modalTitle').text('Add New Test');
    
    // Ensure categories are loaded
    loadCategories();
    
    $('#testModal').modal('show');
}

function viewTest(id) {
    showLoading();
    
    $.get('ajax/test_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                displayTestDetails(response.data);
                $('#viewTestModal').modal('show');
                showSuccess('Test details loaded');
            } else {
                showError('Failed to load test details: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error loading test details');
        })
        .always(function() {
            hideLoading();
        });
}

function displayTestDetails(test) {
    const detailsHtml = `
        <div class="row">
            <div class="col-md-8">
                <div class="detail-item">
                    <div class="detail-label">Test ID</div>
                    <div class="detail-value">${test.id || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Test Name</div>
                    <div class="detail-value"><strong>${test.name || 'N/A'}</strong></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Category</div>
                    <div class="detail-value">
                        <span class="status-badge status-active">${test.category_name || 'N/A'}</span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Price</div>
                    <div class="detail-value">
                        ${test.price ? `<span class="text-success font-weight-bold">₹${test.price}</span>` : 'N/A'}
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Unit</div>
                    <div class="detail-value">${test.unit || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Method</div>
                    <div class="detail-value">${test.method || 'N/A'}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="detail-item">
                    <div class="detail-label">Added By</div>
                    <div class="detail-value">${test.added_by_username || 'System'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Created Date</div>
                    <div class="detail-value">${test.created_at ? new Date(test.created_at).toLocaleDateString() : 'N/A'}</div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h6 class="text-primary border-bottom pb-2">
                <i class="fas fa-venus-mars mr-2"></i>Reference Ranges
            </h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white py-2">
                            <small><i class="fas fa-mars mr-1"></i>Male Range</small>
                        </div>
                        <div class="card-body py-2">
                            <div class="text-center">
                                ${test.male_min && test.male_max ? 
                                    `<strong>${test.male_min} - ${test.male_max}</strong><br>
                                     <small class="text-muted">${test.male_unit || test.unit || ''}</small>` 
                                    : '<span class="text-muted">Not specified</span>'}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white py-2">
                            <small><i class="fas fa-venus mr-1"></i>Female Range</small>
                        </div>
                        <div class="card-body py-2">
                            <div class="text-center">
                                ${test.female_min && test.female_max ? 
                                    `<strong>${test.female_min} - ${test.female_max}</strong><br>
                                     <small class="text-muted">${test.female_unit || test.unit || ''}</small>` 
                                    : '<span class="text-muted">Not specified</span>'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#testViewDetails').html(detailsHtml);
    
    // Store test ID for edit function
    $('#viewTestModal').data('test-id', test.id);
}

function editTest(id) {
    showLoading();
    
    $.get('ajax/test_api.php', {action: 'get', id: id})
        .done(function(response) {
            if (response.success) {
                populateTestForm(response.data);
                $('#testModalLabel').text('Edit Test');
                $('#testModal').modal('show');
                showSuccess('Test data loaded for editing');
            } else {
                showError('Failed to load test data: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error loading test data');
        })
        .always(function() {
            hideLoading();
        });
}

function editTestFromView() {
    const testId = $('#viewTestModal').data('test-id');
    $('#viewTestModal').modal('hide');
    setTimeout(() => editTest(testId), 300);
}

function populateTestForm(test) {
    $('#testId').val(test.id);
    $('#testName').val(test.name);
    $('#testCategory').val(test.category_id);
    $('#testPrice').val(test.price);
    $('#testUnit').val(test.unit);
    $('#testMethod').val(test.method);
    $('#maleMin').val(test.male_min);
    $('#maleMax').val(test.male_max);
    $('#maleUnit').val(test.male_unit);
    $('#femaleMin').val(test.female_min);
    $('#femaleMax').val(test.female_max);
    $('#femaleUnit').val(test.female_unit);
}

function saveTest() {
    const formData = new FormData($('#testForm')[0]);
    formData.append('action', 'save');
    
    const isEdit = $('#testId').val() !== '';
    
    showLoading();
    
    $.ajax({
        url: 'ajax/test_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(function(response) {
        if (response.success) {
            $('#testModal').modal('hide');
            testTableManager.refreshData();
            
            const message = isEdit ? 'Test updated successfully' : 'Test added successfully';
            showSuccess(message);
            
            // Reset form
            $('#testForm')[0].reset();
            $('#testId').val('');
        } else {
            showError('Failed to save test: ' + response.message);
        }
    })
    .fail(function() {
        showError('Error saving test');
    })
    .always(function() {
        hideLoading();
    });
}

function deleteTest(id) {
    showConfirmDialog(
        'Delete Test',
        'Are you sure you want to delete this test? This action cannot be undone.',
        'danger'
    ).then(function(confirmed) {
        if (confirmed) {
            showLoading();
            
            $.post('ajax/test_api.php', {action: 'delete', id: id})
                .done(function(response) {
                    if (response.success) {
                        testTableManager.refreshData();
                        showSuccess('Test deleted successfully');
                    } else {
                        showError('Failed to delete test: ' + response.message);
                    }
                })
                .fail(function() {
                    showError('Error deleting test');
                })
                .always(function() {
                    hideLoading();
                });
        }
    });
}

function bulkDeleteTests() {
    if (selectedTests.size === 0) {
        showWarning('Please select tests to delete');
        return;
    }
    
    const selectedCount = selectedTests.size;
    showConfirmDialog(
        'Bulk Delete',
        `Are you sure you want to delete ${selectedCount} selected tests? This action cannot be undone.`,
        'danger'
    ).then(function(confirmed) {
        if (confirmed) {
            const ids = Array.from(selectedTests);
            showLoading();
            
            $.post('ajax/test_api.php', {action: 'bulk_delete', ids: ids})
                .done(function(response) {
                    if (response.success) {
                        selectedTests.clear();
                        updateBulkActions();
                        testTableManager.refreshData();
                        showSuccess(`${selectedCount} tests deleted successfully`);
                    } else {
                        showError('Failed to delete tests: ' + response.message);
                    }
                })
                .fail(function() {
                    showError('Error deleting tests');
                })
                .always(function() {
                    hideLoading();
                });
        }
    });
}

function bulkExportTests() {
    if (selectedTests.size === 0) {
        showWarning('Please select tests to export');
        return;
    }
    
    const ids = Array.from(selectedTests);
    showLoading();
    
    $.get('ajax/test_api.php', {action: 'bulk_export', ids: ids})
        .done(function(response) {
            if (response.success) {
                downloadCSV(response.data, 'selected_tests.csv');
                showSuccess('Tests exported successfully');
            } else {
                showError('Failed to export tests: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error exporting tests');
        })
        .always(function() {
            hideLoading();
        });
}

function exportTests() {
    showLoading();
    
    $.get('ajax/test_api.php', {action: 'export'})
        .done(function(response) {
            if (response.success) {
                downloadCSV(response.data, 'all_tests.csv');
                showSuccess('All tests exported successfully');
            } else {
                showError('Failed to export tests: ' + response.message);
            }
        })
        .fail(function() {
            showError('Error exporting tests');
        })
        .always(function() {
            hideLoading();
        });
}

function refreshTests() {
    testTableManager.refreshData();
    selectedTests.clear();
    updateBulkActions();
    showInfo('Test data refreshed');
}

function loadCategories() {
    APP_LOG('Loading categories...');
    
    // Check if required elements exist
    if ($('#testCategory').length === 0) {
        console.error('testCategory element not found');
        return;
    }
    
    $.get('ajax/test_category_api.php', {action: 'list'})
        .done(function(response) {
            APP_LOG('Categories response:', response);
            if (response.success && response.data) {
                if (response.data.length === 0) {
                    APP_LOG('No categories found');
                    // Show message that no categories exist
                    $('#testCategory').html('<option value="">No categories available</option>');
                    if ($('#categoryFilter').length > 0) {
                        $('#categoryFilter').html('<option value="">No categories available</option>');
                    }
                } else {
                    // Populate filter dropdown if it exists
                    if ($('#categoryFilter').length > 0) {
                        let options = '<option value="">All Categories</option>';
                        response.data.forEach(function(category) {
                            options += `<option value="${category.id}">${category.name}</option>`;
                        });
                        $('#categoryFilter').html(options);
                    }
                    
                    // Populate test form category dropdown
                    let formOptions = '<option value="">Select Category</option>';
                    response.data.forEach(function(category) {
                        formOptions += `<option value="${category.id}">${category.name}</option>`;
                    });
                    $('#testCategory').html(formOptions);
                    APP_LOG('Categories loaded successfully:', response.data.length, 'categories');
                }
            } else {
                console.error('Categories response invalid:', response);
                showError('Invalid response format from categories API');
                // Fallback options
                $('#testCategory').html('<option value="">Error loading categories</option>');
                if ($('#categoryFilter').length > 0) {
                    $('#categoryFilter').html('<option value="">Error loading categories</option>');
                }
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Error loading categories:', xhr.responseText);
            showError('Error loading categories: ' + error);
            // Fallback options
            $('#testCategory').html('<option value="">Error loading categories</option>');
            if ($('#categoryFilter').length > 0) {
                $('#categoryFilter').html('<option value="">Error loading categories</option>');
            }
        });
}

function applyFilters() {
    const category = $('#categoryFilter').val();
    const gender = $('#genderFilter').val();
    const maxPrice = $('#priceFilter').val();
    
    let searchTerm = '';
    
    if (category) {
        searchTerm += ' category:' + category;
    }
    
    if (gender) {
        searchTerm += ' gender:' + gender;
    }
    
    if (maxPrice) {
        searchTerm += ' maxprice:' + maxPrice;
    }
    
    // Apply search to DataTable
    testTableManager.dataTable
        .search(searchTerm.trim())
        .draw();
}

function clearFilters() {
    $('#categoryFilter').val('');
    $('#genderFilter').val('');
    $('#priceFilter').val('');
    
    testTableManager.dataTable
        .search('')
        .draw();
        
    showInfo('Filters cleared');
}

function printTestDetails() {
    const testId = $('#viewTestModal').data('test-id');
    if (testId) {
        window.open(`print_test.php?id=${testId}`, '_blank');
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
