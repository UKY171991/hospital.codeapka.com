                            <div class="action-buttons">
                            <button class="btn btn-info btn-sm" onclick="viewEntry(${entry.id})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-entry" data-id="${entry.id}" title="Delete Entry">
                                <i class="fas fa-trash"></i>
                            </button>
                            </div>
<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Custom CSS for Entry Table -->
<link rel="stylesheet" href="assets/css/entry-table.css">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clipboard-list mr-2"></i>Test Entries</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Test Entries</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalEntries">0</h3>
                            <p>Total Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pendingEntries">0</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="completedEntries">0</h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="todayEntries">0</h3>
                            <p>Today's Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list-alt mr-1"></i>
                                Test Entry Management
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success btn-sm" onclick="exportEntries()">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <!-- Search and Filter Row -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="entriesSearch" class="form-control" placeholder="Search entries by patient, doctor, test, etc...">
                                        <div class="input-group-append">
                                            <button id="entriesSearchClear" class="btn btn-outline-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto text-right">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="entriesPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Advanced Filters -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <select id="statusFilter" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="doctorFilter" class="form-control">
                                        <option value="">All Doctors</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="testFilter" class="form-control">
                                        <option value="">All Tests</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" id="dateFromFilter" class="form-control" title="From Date">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" id="dateToFilter" class="form-control" title="To Date">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary btn-block" onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>

                            <!-- Entries DataTable -->
                            <div class="table-responsive">
                                <table id="entriesTable" class="table table-bordered table-striped table-hover entries-table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Entry ID</th>
                                            <th>Patient Name</th>
                                            <th>Status</th>
                                            <th>Test Date</th>
                                            <th>Added By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Entry Modal -->
<div class="modal fade" id="entryModal" tabindex="-1" role="dialog" aria-labelledby="entryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="entryModalLabel">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    <span id="modalTitle">Add New Test Entry</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="entryForm">
                <div class="modal-body">
                    <input type="hidden" id="entryId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryPatient">
                                    <i class="fas fa-user mr-1"></i>
                                    Patient <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="entryPatient" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryDoctor">
                                    <i class="fas fa-user-md mr-1"></i>
                                    Doctor <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="entryDoctor" name="doctor_id" required>
                                    <option value="">Select Doctor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryTest">
                                    <i class="fas fa-vial mr-1"></i>
                                    Test <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="entryTest" name="test_id" required>
                                    <option value="">Select Test</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryDate">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Entry Date
                                </label>
                                <input type="date" class="form-control" id="entryDate" name="entry_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entryResult">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    Result
                                </label>
                                <input type="text" class="form-control" id="entryResult" name="result">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entryUnit">
                                    <i class="fas fa-ruler mr-1"></i>
                                    Unit
                                </label>
                                <input type="text" class="form-control" id="entryUnit" name="unit">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entryStatus">
                                    <i class="fas fa-flag mr-1"></i>
                                    Status
                                </label>
                                <select class="form-control" id="entryStatus" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryAmount">
                                    <i class="fas fa-rupee-sign mr-1"></i>
                                    Amount
                                </label>
                                <input type="number" class="form-control" id="entryAmount" name="amount" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryDiscount">
                                    <i class="fas fa-percentage mr-1"></i>
                                    Discount (%)
                                </label>
                                <input type="number" class="form-control" id="entryDiscount" name="discount" min="0" max="100" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="entryNotes">
                            <i class="fas fa-sticky-note mr-1"></i>
                            Notes
                        </label>
                        <textarea class="form-control" id="entryNotes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global variables
let entriesTable;

// Initialize page
$(document).ready(function() {
    loadDropdownsForEntry();
    loadEntries();
    initializeEventListeners();
    loadStats();
});

function initializeEventListeners() {
    // Form submission
    $('#entryForm').on('submit', function(e) {
        e.preventDefault();
        saveEntryData();
    });
    
    // Search functionality
    $('#entriesSearch').on('input', function() {
        applyEntriesFilters();
    });

    // Clear search button
    $('#entriesSearchClear').click(function(e) {
        $('#entriesTable tbody').html(html);
        // Hide action buttons row if there are no entries
        if (!entries || entries.length === 0) {
            $('.action-buttons').remove();
        }
        applyEntriesFilters();
        applyEntriesFilters();
    });

    // Per page change
    $('#entriesPerPage').change(function() {
        applyEntriesFilters();
    });

    // Modal reset on hide
    $('#entryModal').on('hidden.bs.modal', function() {
        resetModalForm();
    });

    // Filter changes
    $('#statusFilter, #doctorFilter, #testFilter, #dateFromFilter, #dateToFilter').on('change', function() {
        applyEntriesFilters();
    });
}

function loadDropdownsForEntry() {
    // Initialize Select2 for entry form
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#entryModal')
    });

    // Load patients
    $.get('ajax/patient_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                let options = '<option value="">Select Patient</option>';
                response.data.forEach(patient => {
                    options += `<option value="${patient.id}">${patient.name || 'Unknown'}</option>`;
                });
                $('#entryPatient').html(options).trigger('change');
            }
        });

    // Load doctors
    $.get('ajax/doctor_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                let options = '<option value="">Select Doctor</option>';
                let filterOptions = '<option value="">All Doctors</option>';
                response.data.forEach(doctor => {
                    options += `<option value="${doctor.id}">${doctor.name || 'Unknown'}</option>`;
                    filterOptions += `<option value="${doctor.name}">${doctor.name}</option>`;
                });
                $('#entryDoctor').html(options).trigger('change');
                $('#doctorFilter').html(filterOptions);
            }
        });

    // Load tests
    $.get('ajax/test_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load entry data: ' + errorMsg, 'error');
        });
}

function viewEntry(id) {
    $.get('ajax/entry_api.php', { action: 'get', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const entry = response.data;
                populateEntryForm(entry);
                $('#modalTitle').text('View Test Entry');
                $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', true);
                $('#saveEntryBtn').hide();
                $('#entryModal').modal('show');
            } else {
                showAlert('Error loading entry data: ' + (response.message || 'Entry not found'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load entry data: ' + errorMsg, 'error');
        });
}

function populateEntryForm(entry) {
    $('#entryId').val(entry.id);
    $('#entryPatient').val(entry.patient_id);
    $('#entryDoctor').val(entry.doctor_id);
    $('#entryTest').val(entry.test_id);
    
    // Convert server datetime to datetime-local format
    const entryDate = entry.entry_date || '';
    if (entryDate) {
        const localDateTime = entryDate.replace(' ', 'T').slice(0, 16);
        $('#entryDate').val(localDateTime);
    } else {
        $('#entryDate').val('');
    }
    
    $('#entryResult').val(entry.result_value || '');
    $('#entryUnit').val(entry.unit || '');
    $('#entryStatus').val(entry.status || 'pending');
    $('#entryNotes').val(entry.remarks || '');
}

function saveEntryData() {
    // Validate form first
    if (!validateModalForm('entryForm')) {
        return false;
    }
    
    const data = $('#entryForm').serialize() + '&action=save&ajax=1';
    const isEdit = $('#entryId').val();
    
    const submitBtn = $('#entryForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.post('ajax/entry_api.php', data)
        .done(function(response) {
            if (response.success) {
                toastr.success(isEdit ? 'Entry updated successfully!' : 'Entry added successfully!');
                $('#entryModal').modal('hide');
                loadEntries();
                loadStats();
            } else {
                toastr.error('Error: ' + (response.message || 'Save failed'));
            }
        })
        .fail(function(xhr) {
            handleAjaxError(xhr, 'save entry');
        })
        .always(function() {
            submitBtn.html(originalText).prop('disabled', false);
        });
}

function deleteEntry(id) {
    if (!confirm('Are you sure you want to delete this entry?')) {
        return;
    }

    $.post('ajax/entry_api.php', { action: 'delete', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                showAlert('Entry deleted successfully!', 'success');
                loadEntries();
                loadStats();
            } else {
                showAlert('Error deleting entry: ' + (response.message || 'Delete failed'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to delete entry: ' + errorMsg, 'error');
        });
}

// Event handlers using delegation
$(document).on('click', '.edit-entry', function() {

$(document).on('click', '.delete-entry', function() {
    const id = $(this).data('id');
    deleteEntry(id);
});

function applyEntriesFilters() {
    const query = $('#entriesSearch').val().toLowerCase().trim();
    const status = $('#statusFilter').val().toLowerCase();
    const doctor = $('#doctorFilter').val().toLowerCase();
    const test = $('#testFilter').val().toLowerCase();
    const dateFrom = $('#dateFromFilter').val();
    const dateTo = $('#dateToFilter').val();
    const perPage = parseInt($('#entriesPerPage').val() || 10, 10);
    
    let shown = 0;
    
    $('#entriesTable tbody tr').each(function() {
        const row = $(this);
        const text = row.text().toLowerCase();
        let matches = true;
        
        // Text search
        if (query && text.indexOf(query) === -1) {
            matches = false;
        }
        
        // Status filter
        if (status && text.indexOf(status) === -1) {
            matches = false;
        }
        
        // Doctor filter
        if (doctor && text.indexOf(doctor) === -1) {
            matches = false;
        }
        
        // Test filter
        if (test && text.indexOf(test) === -1) {
            matches = false;
        }
        
        // Show/hide based on matches and pagination
        if (matches && shown < perPage) {
            row.show();
            shown++;
        } else {
            row.hide();
        }
    });
}

function clearFilters() {
    $('#statusFilter').val('');
    $('#doctorFilter').val('');
    $('#testFilter').val('');
    $('#dateFromFilter').val('');
    $('#dateToFilter').val('');
    $('#entriesSearch').val('');
    applyEntriesFilters();
}

function resetModalForm() {
    $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', false);
    $('#saveEntryBtn').show();
    $('#modalTitle').text('Add New Test Entry');
}

function exportEntries() {
    // Simple export functionality
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Sr No.,Entry ID,Patient Name,Test Name,Status,Test Date,Added By\n";
    
    $('#entriesTable tbody tr:visible').each(function() {
        const cells = $(this).find('td');
        if (cells.length > 6) {
            const row = [
                cells.eq(0).text(), // Sr No.
                cells.eq(1).text().replace('#', ''), // Entry ID without badge
                cells.eq(2).text(), // Patient Name
                cells.eq(3).text(), // Test Name
                cells.eq(4).find('.badge').text(), // Status
                cells.eq(5).text(), // Test Date
                cells.eq(6).text() // Added By
            ].join(',');
            csvContent += row + "\n";
        }
    });
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "entries_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
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
    
    $('.alert').remove();
    $('.content-wrapper .content').prepend(alert);
    
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

function getErrorMessage(xhr) {
    let message = xhr.responseText || 'Server error';
    try {
        const jsonResponse = JSON.parse(xhr.responseText || '{}');
        if (jsonResponse.message) {
            message = jsonResponse.message;
        }
    } catch (e) {
        // Keep the original message
    }
    return message;
}

// Make functions globally available
window.openAddEntryModal = openAddEntryModal;
window.viewEntry = viewEntry;
window.editEntry = editEntry;
window.deleteEntry = deleteEntry;
window.exportEntries = exportEntries;
window.clearFilters = clearFilters;
</script>

<?php require_once 'inc/footer.php'; ?>