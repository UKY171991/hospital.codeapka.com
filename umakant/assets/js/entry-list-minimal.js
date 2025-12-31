/**
 * Minimal Entry List Management - Clean version to fix console issues
 */

// Global variables
let entriesTable = null;
let testsData = [];
let categoriesData = [];
let mainCategoriesData = [];
let patientsData = [];
let doctorsData = [];

/**
 * Initialize DataTable with minimal configuration
 */
function initializeDataTable() {
    console.log('Initializing minimal DataTable...');

    try {
        if ($.fn.DataTable.isDataTable('#entriesTable')) {
            $('#entriesTable').DataTable().destroy();
        }

        entriesTable = $('#entriesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'patho_api/entry.php',
                type: 'GET',
                data: function (d) {
                    d.action = 'list';
                    d.status = $('#filterStatus').val();
                    d.date_from = $('#filterFrom').val();
                    d.date_to = $('#filterTo').val();
                    d.patient_id = $('#patientFilter').val();
                    d.doctor_id = $('#doctorFilter').val();
                    d.secret_key = 'hospital-api-secret-2024';
                }
            },
            columns: [
                { data: 'id', title: 'ID', defaultContent: 'N/A' },
                { data: 'patient_name', title: 'Patient', defaultContent: 'N/A' },
                { data: 'doctor_name', title: 'Doctor', defaultContent: 'Not assigned' },
                { data: 'test_names', title: 'Tests', defaultContent: 'No tests' },
                {
                    data: 'status',
                    title: 'Status',
                    defaultContent: 'pending',
                    render: function (data) {
                        const status = data || 'pending';
                        const badgeClass = status === 'completed' ? 'success' : status === 'cancelled' ? 'danger' : 'warning';
                        return `<span class="badge badge-${badgeClass}">${status}</span>`;
                    }
                },
                {
                    data: 'priority',
                    title: 'Priority',
                    defaultContent: 'normal',
                    render: function (data) {
                        return `<span class="badge badge-info">${data || 'normal'}</span>`;
                    }
                },
                {
                    data: 'total_price',
                    title: 'Amount',
                    defaultContent: '0.00',
                    render: function (data) {
                        return `₹${(parseFloat(data) || 0).toFixed(2)}`;
                    }
                },
                {
                    data: 'entry_date',
                    title: 'Date',
                    defaultContent: 'N/A',
                    render: function (data) {
                        if (data) return new Date(data).toLocaleDateString('en-IN');
                        return 'N/A';
                    }
                },
                {
                    data: null,
                    title: 'Actions',
                    orderable: false,
                    render: function (data, type, row) {
                        if (row && row.id) {
                            return `
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-primary btn-sm" onclick="printEntryReport(${row.id})" title="Print Report"><i class="fas fa-print"></i></button>
                                    <button class="btn btn-info btn-sm" onclick="viewEntry(${row.id})" title="View"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-warning btn-sm" onclick="editEntry(${row.id})" title="Edit"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteEntry(${row.id})" title="Delete"><i class="fas fa-trash"></i></button>
                                </div>
                            `;
                        }
                        return '';
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true
        });
        return true;
    } catch (error) {
        console.error('Failed to initialize DataTable:', error);
        return false;
    }
}

function showError(message) {
    if (typeof toastr !== 'undefined') toastr.error(message);
    else alert(message);
}

function showSuccess(message) {
    if (typeof toastr !== 'undefined') toastr.success(message);
    else console.log(message);
}

function openAddModal() {
    resetForm();
    $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');
    $('#entryModal').modal('show');
    loadModalData();
}

function resetForm() {
    $('#entryForm')[0].reset();
    $('#entryId').val('');
    $('#testsContainer').empty();
    $('#subtotal').val('0.00');
    $('#discountAmount').val('0.00');
    $('#totalPrice').val('0.00');
    $('#entryDate').val(new Date().toISOString().split('T')[0]);
    $('#dateSlot').val('');
    $('#serviceLocation').val('');
    $('#collectionAddress').val('');
}

function loadModalData() {
    loadPatients();
    loadDoctors();
    loadTestsAndCategories();
    setTimeout(() => { addTestRow(); }, 500);
}

function loadPatients() {
    $.ajax({
        url: 'ajax/patient_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function (response) {
            if (response && response.success && response.data) {
                populatePatientSelect(response.data);
            }
        }
    });
}

function loadDoctors() {
    $.ajax({
        url: 'ajax/doctor_api.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function (response) {
            if (response && response.success && response.data) {
                populateDoctorSelect(response.data);
            }
        }
    });
}

function loadTestsAndCategories() {
    // Load tests
    $.ajax({
        url: 'ajax/test_api.php',
        method: 'GET',
        data: { action: 'simple_list' },
        dataType: 'json',
        success: function (response) {
            if (response && response.success && response.data) {
                testsData = response.data;
            }
        }
    });

    // Load categories
    $.ajax({
        url: 'patho_api/test_category.php',
        method: 'GET',
        data: { action: 'list', all: '1' },
        dataType: 'json',
        success: function (response) {
            if (response && response.success && response.data) {
                categoriesData = response.data;
                populateGlobalCategoryFilter();
            }
        }
    });

    // Load main categories
    $.ajax({
        url: 'patho_api/main_test_category.php',
        method: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function (response) {
            if (response && response.success && response.data) {
                mainCategoriesData = response.data;
            }
        }
    });
}

function populatePatientSelect(patients) {
    const $select = $('#patientSelect');
    $select.empty().append('<option value="">Select Patient</option>');
    patients.forEach(patient => {
        const displayName = `${patient.name}${patient.uhid ? ` (${patient.uhid})` : ''}`;
        $select.append(`<option value="${patient.id}">${displayName}</option>`);
    });
    if (typeof $.fn.select2 !== 'undefined') $select.select2({ theme: 'bootstrap4', width: '100%' });
}

function populateDoctorSelect(doctors) {
    const $select = $('#doctorSelect');
    $select.empty().append('<option value="">Select Doctor</option>');
    doctors.forEach(doctor => {
        const displayName = `${doctor.name}${doctor.specialization ? ` (${doctor.specialization})` : ''}`;
        $select.append(`<option value="${doctor.id}">${displayName}</option>`);
    });
    if (typeof $.fn.select2 !== 'undefined') $select.select2({ theme: 'bootstrap4', width: '100%' });
}

function populateGlobalCategoryFilter() {
    const $select = $('#globalCategoryFilter');
    $select.find('option:not(:first)').remove();
    categoriesData.forEach(category => {
        $select.append(`<option value="${category.id}">${category.name}</option>`);
    });
}

let testRowCounter = 0;

function addTestRow() {
    const rowIndex = testRowCounter++;

    let mainCategoryOptions = '<option value="">Select Main Category</option>';
    if (mainCategoriesData) {
        mainCategoriesData.forEach(cat => {
            mainCategoryOptions += `<option value="${cat.id}">${cat.name}</option>`;
        });
    }

    let categoryOptions = '<option value="">Select Sub Category</option>';
    if (categoriesData) {
        categoriesData.forEach(category => {
            categoryOptions += `<option value="${category.id}" data-main-cat="${category.main_category_id || ''}">${category.name}</option>`;
        });
    }

    let testOptions = '<option value="">Select Test</option>';
    if (testsData) {
        testsData.forEach(test => {
            testOptions += `<option value="${test.id}">${test.name}</option>`;
        });
    }

    const rowHtml = `
        <div class="test-row row mb-2" data-row-index="${rowIndex}">
            <div class="col-md-2">
                <select class="form-control main-category-select" name="tests[${rowIndex}][main_category_id]">${mainCategoryOptions}</select>
            </div>
            <div class="col-md-2">
                <select class="form-control category-select" name="tests[${rowIndex}][category_id]">${categoryOptions}</select>
            </div>
            <div class="col-md-2">
                <select class="form-control test-select" name="tests[${rowIndex}][test_id]" required>${testOptions}</select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control test-result" name="tests[${rowIndex}][result_value]" placeholder="Result">
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-range" placeholder="Min - Max" readonly>
                <input type="hidden" class="test-min" name="tests[${rowIndex}][min]">
                <input type="hidden" class="test-max" name="tests[${rowIndex}][max]">
            </div>
            <div class="col-md-1">
                <input type="text" class="form-control test-unit" name="tests[${rowIndex}][unit]" placeholder="Unit" readonly>
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control test-price" name="tests[${rowIndex}][price]" placeholder="0.00" step="0.01" min="0">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-test-btn" onclick="removeTestRow(this)"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    `;

    $('#testsContainer').append(rowHtml);

    const $newRow = $(`.test-row[data-row-index="${rowIndex}"]`);

    $newRow.find('.main-category-select').on('change', function () {
        const mainCatId = $(this).val();
        const $subCatSelect = $newRow.find('.category-select');
        $subCatSelect.empty().append('<option value="">Select Sub Category</option>');
        if (categoriesData) {
            categoriesData.forEach(cat => {
                if (!mainCatId || (cat.main_category_id && cat.main_category_id == mainCatId)) {
                    $subCatSelect.append(`<option value="${cat.id}" data-main-cat="${cat.main_category_id || ''}">${cat.name}</option>`);
                }
            });
        }
        $subCatSelect.val('').trigger('change');
    });

    $newRow.find('.category-select').on('change', function () {
        const categoryId = $(this).val();
        const $testSelect = $newRow.find('.test-select');
        $testSelect.val('');
        updateTestOptions($testSelect, categoryId);
        $newRow.find('.test-price, .test-unit, .test-range, .test-min, .test-max').val('');
        calculateTotals();
    });

    $newRow.find('.test-select').on('change', function () {
        const testId = $(this).val();
        if (testId && testsData) {
            const test = testsData.find(t => String(t.id) === String(testId));
            if (test) {
                const $categorySelect = $newRow.find('.category-select');
                if (!$categorySelect.val() && test.category_id) {
                    $categorySelect.val(test.category_id);
                    if (test.main_category_id) {
                        $newRow.find('.main-category-select').val(test.main_category_id);
                    }
                }
                $newRow.find('.test-price').val(test.price || 0);
                $newRow.find('.test-unit').val(test.unit || '');
                const min = test.min || '';
                const max = test.max || '';
                $newRow.find('.test-min').val(min);
                $newRow.find('.test-max').val(max);
                $newRow.find('.test-range').val(min || max ? `${min} - ${max}` : '');
                calculateTotals();
            }
        }
    });

    $newRow.find('.test-price').on('input', calculateTotals);
}

function removeTestRow(button) {
    $(button).closest('.test-row').remove();
    calculateTotals();
    if ($('#testsContainer .test-row').length === 0) addTestRow();
}

function calculateTotals() {
    let subtotal = 0;
    $('#testsContainer .test-price').each(function () {
        subtotal += parseFloat($(this).val()) || 0;
    });
    const discount = parseFloat($('#discountAmount').val()) || 0;
    const total = Math.max(subtotal - discount, 0);
    $('#subtotal').val(subtotal.toFixed(2));
    $('#totalPrice').val(total.toFixed(2));
}

$(document).on('submit', '#entryForm', function (e) {
    e.preventDefault();
    saveEntry();
});

$(document).on('input', '#discountAmount', calculateTotals);

function saveEntry() {
    const patientId = $('#patientSelect').val();
    const entryDate = $('#entryDate').val();

    if (!patientId || !entryDate) {
        showError('Please select patient and date.');
        return;
    }

    const formData = new FormData($('#entryForm')[0]);
    formData.append('action', 'save');
    formData.append('secret_key', 'hospital-api-secret-2024');
    if (typeof currentUserId !== 'undefined') formData.append('added_by', currentUserId);

    const tests = [];
    $('#testsContainer .test-row').each(function () {
        const $row = $(this);
        const testId = $row.find('.test-select').val();
        if (testId) {
            tests.push({
                test_id: testId,
                category_id: $row.find('.category-select').val() || 0,
                main_category_id: $row.find('.main-category-select').val() || 0,
                result_value: $row.find('.test-result').val() || '',
                price: parseFloat($row.find('.test-price').val()) || 0,
                unit: $row.find('.test-unit').val() || '',
                status: 'pending'
            });
        }
    });

    formData.append('tests', JSON.stringify(tests));

    $.ajax({
        url: 'patho_api/entry.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            if (response && response.success) {
                showSuccess('Entry saved successfully');
                $('#entryModal').modal('hide');
                refreshTable();
            } else {
                showError(response ? response.message : 'Failed to save');
            }
        },
        error: function () { showError('Failed to save entry'); }
    });
}

function refreshTable() {
    if (entriesTable) entriesTable.ajax.reload();
}

function viewEntry(id) {
    $('#viewEntryModal').modal('show');
    $('#entryDetails').html('Loading...');

    $.ajax({
        url: 'patho_api/entry.php',
        method: 'GET',
        data: { action: 'get', id: id, secret_key: 'hospital-api-secret-2024' },
        dataType: 'json',
        success: function (response) {
            if (response && response.success) displayEntryDetails(response.data);
        }
    });
}

function displayEntryDetails(entry) {
    const statusClass = entry.status === 'completed' ? 'success' : entry.status === 'cancelled' ? 'danger' : 'warning';
    const priorityClass = entry.priority === 'urgent' || entry.priority === 'emergency' ? 'danger' : 'info';

    const testsHtml = entry.tests ? entry.tests.map(test => `
        <tr class="${test.result_value && isValueAbnormal(test) ? 'table-danger' : ''}">
            <td class="font-weight-bold">${test.test_name}</td>
            <td class="text-center font-weight-bold text-primary">${test.result_value || '<span class="text-muted italic">Pending</span>'}</td>
            <td class="text-center">${test.test_ref_range || (test.test_min !== undefined ? `${test.test_min} - ${test.test_max}` : 'N/A')}</td>
            <td class="text-center">${test.unit || test.test_unit || '-'}</td>
            <td class="text-right">₹${parseFloat(test.price).toFixed(2)}</td>
        </tr>
        ${test.remarks ? `<tr><td colspan="5" class="small text-muted py-0"><em>Note: ${test.remarks}</em></td></tr>` : ''}
    `).join('') : '<tr><td colspan="5" class="text-center">No tests recorded</td></tr>';

    const html = `
        <div class="container-fluid px-0">
            <!-- Header Info Card -->
            <div class="row mb-4">
                <div class="col-md-7">
                    <div class="card card-outline card-primary shadow-sm h-100">
                        <div class="card-body p-3">
                            <h5 class="text-primary font-weight-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-user-circle mr-2"></i>Patient Information
                            </h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1"><span class="text-muted small uppercase block">Name</span><strong>${entry.patient_name}</strong></p>
                                    <p class="mb-1"><span class="text-muted small uppercase block">UHID</span><strong>${entry.patient_uhid || 'N/A'}</strong></p>
                                    <p class="mb-1"><span class="text-muted small uppercase block">Age / Sex</span><strong>${entry.age || 'N/A'} ${entry.age_unit || ''} / ${entry.sex || 'N/A'}</strong></p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1"><span class="text-muted small uppercase block">Phone</span><strong>${entry.patient_mobile || 'N/A'}</strong></p>
                                    <p class="mb-0"><span class="text-muted small uppercase block">Address</span><small>${entry.patient_address || 'N/A'}</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card card-outline card-info shadow-sm h-100">
                        <div class="card-body p-3">
                            <h5 class="text-info font-weight-bold mb-3 border-bottom pb-2">
                                <i class="fas fa-notes-medical mr-2"></i>Entry Metadata
                            </h5>
                            <div class="row">
                                <div class="col-sm-6">
                                    <p class="mb-1"><span class="text-muted small uppercase block">Date</span><strong>${new Date(entry.entry_date).toLocaleDateString('en-IN')}</strong></p>
                                    <p class="mb-1"><span class="text-muted small uppercase block">Referrer</span><strong>Dr. ${entry.doctor_name || 'Self'}</strong></p>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <div class="mb-2"><span class="badge badge-${statusClass} px-3 py-2 text-md">${entry.status.toUpperCase()}</span></div>
                                    <div><span class="badge badge-${priorityClass} px-3 py-1">${entry.priority.toUpperCase()}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tests Table Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 font-weight-bold">
                        <i class="fas fa-flask mr-2 text-secondary"></i>Investigation Details
                    </h6>
                    <span class="badge badge-secondary">${entry.tests ? entry.tests.length : 0} Tests</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0 border-bottom">
                        <thead class="bg-gray-light">
                            <tr>
                                <th class="border-top-0">Test Description</th>
                                <th class="border-top-0 text-center">Value</th>
                                <th class="border-top-0 text-center">Range</th>
                                <th class="border-top-0 text-center">Unit</th>
                                <th class="border-top-0 text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>${testsHtml}</tbody>
                    </table>
                </div>
            </div>

            <!-- Summary and totals -->
            <div class="row">
                <div class="col-md-7">
                    <div class="card card-outline card-secondary shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="font-weight-bold border-bottom pb-2 mb-2">Notes / Interpretation</h6>
                            <p class="text-muted small mb-0 font-italic">
                                ${entry.notes || 'No clinical notes provided for this entry.'}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card shadow-sm bg-light">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span>₹${parseFloat(entry.subtotal).toFixed(2)}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Discount:</span>
                                <span class="text-danger">-₹${parseFloat(entry.discount_amount).toFixed(2)}</span>
                            </div>
                            <div class="d-flex justify-content-bold justify-content-between border-top pt-2">
                                <h5 class="font-weight-bold mb-0">Total Amount:</h5>
                                <h5 class="font-weight-bold text-primary mb-0">₹${parseFloat(entry.total_price).toFixed(2)}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#entryDetails').html(html);
}

function isValueAbnormal(test) {
    if (!test.result_value || isNaN(test.result_value)) return false;
    const val = parseFloat(test.result_value);
    const min = parseFloat(test.test_min);
    const max = parseFloat(test.test_max);
    if (!isNaN(min) && val < min) return true;
    if (!isNaN(max) && val > max) return true;
    return false;
}

function editEntry(id) {
    $.ajax({
        url: 'patho_api/entry.php',
        method: 'GET',
        data: { action: 'get', id: id, secret_key: 'hospital-api-secret-2024' },
        dataType: 'json',
        success: function (response) {
            if (response && response.success) {
                populateEditForm(response.data);
                $('#entryModal').modal('show');
            }
        }
    });
}

function populateEditForm(entry) {
    loadModalData();
    setTimeout(() => {
        $('#entryId').val(entry.id);
        $('#patientSelect').val(entry.patient_id).trigger('change');
        $('#doctorSelect').val(entry.doctor_id).trigger('change');
        $('#entryDate').val(entry.entry_date.split(' ')[0]);
        $('#subtotal').val(entry.subtotal);
        $('#discountAmount').val(entry.discount_amount);
        $('#totalPrice').val(entry.total_price);

        $('#testsContainer').empty();
        testRowCounter = 0;
        if (entry.tests && entry.tests.length) {
            entry.tests.forEach(test => addTestRowWithData(test));
        } else {
            addTestRow();
        }
    }, 1000);
}

function addTestRowWithData(testData) {
    addTestRow();
    const $lastRow = $('#testsContainer .test-row').last();
    setTimeout(() => {
        if (testData.main_category_id) $lastRow.find('.main-category-select').val(testData.main_category_id).trigger('change');
        if (testData.category_id) $lastRow.find('.category-select').val(testData.category_id).trigger('change');
        $lastRow.find('.test-select').val(testData.test_id).trigger('change');
        $lastRow.find('.test-result').val(testData.result_value);
        $lastRow.find('.test-price').val(testData.price);
    }, 100);
}

function deleteEntry(id) {
    if (confirm('Delete this entry?')) {
        $.ajax({
            url: 'patho_api/entry.php',
            method: 'POST',
            data: { action: 'delete', id: id, secret_key: 'hospital-api-secret-2024' },
            success: function (response) {
                if (response.success) refreshTable();
            }
        });
    }
}

function updateTestOptions($testSelect, categoryId) {
    $testSelect.find('option:not(:first)').remove();
    let filtered = testsData;
    if (categoryId) filtered = testsData.filter(t => t.category_id == categoryId);
    filtered.forEach(test => {
        $testSelect.append(`<option value="${test.id}">${test.name}</option>`);
    });
}

$(document).ready(function () {
    initializeDataTable();
    $('#globalCategoryFilter').on('change', function () {
        const catId = $(this).val();
        $('.test-row').each(function () {
            $(this).find('.category-select').val(catId).trigger('change');
        });
    });

    // Handle filters
    $('#filterStatus, #patientFilter, #doctorFilter, #filterFrom, #filterTo').on('change', function () {
        refreshTable();
    });

    $('#resetFilters').on('click', function () {
        $('#filterStatus, #patientFilter, #doctorFilter, #filterFrom, #filterTo').val('').trigger('change');
        refreshTable();
    });
});

// Exports
window.refreshTable = refreshTable;
window.openAddModal = openAddModal;
window.viewEntry = viewEntry;
window.editEntry = editEntry;
window.deleteEntry = deleteEntry;
window.addTestRow = addTestRow;
window.removeTestRow = removeTestRow;

window.printEntryReport = function (id) {
    if (!id) return;
    const url = `print-entry.php?id=${id}`;
    window.open(url, '_blank', 'width=900,height=800');
};

window.printEntryDetails = function () {
    const entryId = $('#entryId').val() || currentViewEntryId;
    if (entryId) {
        window.printEntryReport(entryId);
    } else {
        window.print();
    }
};

let currentViewEntryId = null;
const originalViewEntry = window.viewEntry;
window.viewEntry = function (id) {
    currentViewEntryId = id;
    originalViewEntry(id);
};

window.exportEntries = function () {
    if (entriesTable) {
        // Find the export button in DataTables if exists, or use manual export
        try {
            // Check if DataTables buttons extension is active
            if (entriesTable.button('.buttons-csv').length) {
                entriesTable.button('.buttons-csv').trigger();
            } else {
                // Manual CSV export as fallback
                const data = entriesTable.rows({ search: 'applied' }).data().toArray();
                if (data.length === 0) {
                    showError('No data to export');
                    return;
                }

                let csv = 'ID,Patient,Doctor,Tests,Status,Priority,Amount,Date\n';
                data.forEach(row => {
                    const tests = (row.test_names || '').replace(/,/g, ';');
                    csv += `${row.id},"${row.patient_name}","${row.doctor_name || ''}","${tests}",${row.status},${row.priority},${row.total_price},${row.entry_date}\n`;
                });

                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', `test_entries_${new Date().toISOString().split('T')[0]}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        } catch (e) {
            console.error('Export failed:', e);
            alert('Export failed. See console for details.');
        }
    }
};

window.refreshTestAggregates = function () {
    $.ajax({
        url: 'patho_api/entry.php',
        method: 'POST',
        data: {
            action: 'refresh_aggregates',
            secret_key: 'hospital-api-secret-2024'
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showSuccess(response.message || 'Aggregates refreshed successfully');
                refreshTable();
            } else {
                showError(response.message || 'Failed to refresh aggregates');
            }
        },
        error: function () {
            showError('Failed to refresh aggregates');
        }
    });
};

window.diagnoseTestData = function () {
    $.ajax({
        url: 'patho_api/entry.php',
        method: 'GET',
        data: {
            action: 'diagnose',
            secret_key: 'hospital-api-secret-2024'
        },
        dataType: 'json',
        success: function (response) {
            console.log('Diagnosis:', response);
            alert(JSON.stringify(response, null, 2));
        },
        error: function () {
            showError('Failed to run diagnosis');
        }
    });
};

window.addTestColumns = function () {
    if (!confirm('This will attempt to fix the database schema by adding missing columns. Continue?')) {
        return;
    }

    $.ajax({
        url: 'patho_api/entry.php',
        method: 'POST',
        data: {
            action: 'fix_schema',
            secret_key: 'hospital-api-secret-2024'
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showSuccess(response.message || 'Schema fixed successfully');
            } else {
                showError(response.message || 'Failed to fix schema');
            }
        },
        error: function (xhr, status, error) {
            console.error('Schema fix error:', error);
            showError('An error occurred while fixing the schema');
        }
    });
};