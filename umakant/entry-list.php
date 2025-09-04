<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

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
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#entryModal" onclick="openAddEntryModal()">
                                    <i class="fas fa-plus"></i> New Entry
                                </button>
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
                                <table id="entriesTable" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Patient</th>
                                            <th>Doctor</th>
                                            <th>Test</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated by DataTables AJAX -->
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
    initializeDataTable();
    loadDropdownData();
    loadStats();
    initializeEventListeners();
});

function initializeDataTable() {
    entriesTable = $('#entriesTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'patho_api/entry.php',
            type: 'GET',
            dataSrc: function(json) {
                if (json.status === 'success') {
                    return json.data;
                }
                return [];
            }
        },
        columns: [
            { data: 'id' },
            { 
                data: 'patient_name',
                render: function(data, type, row) {
                    return `<div class="font-weight-bold text-primary">${data}</div>
                            ${row.patient_uhid ? `<small class="text-muted">UHID: ${row.patient_uhid}</small>` : ''}`;
                }
            },
            { 
                data: 'doctor_name',
                render: function(data, type, row) {
                    return data ? `<span class="text-info">${data}</span>` : '-';
                }
            },
            { 
                data: 'test_name',
                render: function(data, type, row) {
                    return `<div class="font-weight-bold">${data}</div>
                            ${row.test_category ? `<small class="text-muted">${row.test_category}</small>` : ''}`;
                }
            },
            { 
                data: 'entry_date',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString() : '-';
                }
            },
            {
                data: 'status',
                render: function(data, type, row) {
                    const statusClass = {
                        'pending': 'warning',
                        'completed': 'success',
                        'cancelled': 'danger'
                    };
                    return `<span class="badge badge-${statusClass[data] || 'secondary'}">${data}</span>`;
                }
            },
            { 
                data: 'amount',
                render: function(data, type, row) {
                    if (data) {
                        const amount = parseFloat(data);
                        const discount = parseFloat(row.discount || 0);
                        const final = amount - (amount * discount / 100);
                        return `<div>₹${final.toFixed(2)}</div>
                                ${discount > 0 ? `<small class="text-muted">Disc: ${discount}%</small>` : ''}`;
                    }
                    return '-';
                }
            },
            {
                data: 'id',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm" onclick="viewEntry(${data})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editEntry(${data})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-success btn-sm" onclick="printReport(${data})" title="Print Report">
                                <i class="fas fa-print"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteEntry(${data})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading entries...'
        }
    });

    // Custom filters
    $('#statusFilter, #doctorFilter, #testFilter, #dateFromFilter, #dateToFilter').on('change', function() {
        applyFilters();
    });
}

function applyFilters() {
    const status = $('#statusFilter').val();
    const doctor = $('#doctorFilter').val();
    const test = $('#testFilter').val();
    const dateFrom = $('#dateFromFilter').val();
    const dateTo = $('#dateToFilter').val();

    // Apply filters
    entriesTable.column(5).search(status);
    entriesTable.column(2).search(doctor);
    entriesTable.column(3).search(test);
    
    entriesTable.draw();
}

function clearFilters() {
    $('#statusFilter').val('');
    $('#doctorFilter').val('');
    $('#testFilter').val('');
    $('#dateFromFilter').val('');
    $('#dateToFilter').val('');
    entriesTable.search('').columns().search('').draw();
}

function loadDropdownData() {
    // Load patients
    $.get('patho_api/patient.php')
        .done(function(response) {
            if (response.status === 'success') {
                let options = '<option value="">Select Patient</option>';
                response.data.forEach(patient => {
                    options += `<option value="${patient.id}">${patient.name} ${patient.uhid ? '(' + patient.uhid + ')' : ''}</option>`;
                });
                $('#entryPatient').html(options);
            }
        });

    // Load doctors
    $.get('patho_api/doctor.php')
        .done(function(response) {
            if (response.status === 'success') {
                let options = '<option value="">Select Doctor</option>';
                let filterOptions = '<option value="">All Doctors</option>';
                
                response.data.forEach(doctor => {
                    options += `<option value="${doctor.id}">${doctor.name}</option>`;
                    filterOptions += `<option value="${doctor.name}">${doctor.name}</option>`;
                });
                
                $('#entryDoctor').html(options);
                $('#doctorFilter').html(filterOptions);
            }
        });

    // Load tests
    $.get('patho_api/test.php')
        .done(function(response) {
            if (response.status === 'success') {
                let options = '<option value="">Select Test</option>';
                let filterOptions = '<option value="">All Tests</option>';
                
                response.data.forEach(test => {
                    options += `<option value="${test.id}" data-price="${test.price}">${test.name}</option>`;
                    filterOptions += `<option value="${test.name}">${test.name}</option>`;
                });
                
                $('#entryTest').html(options);
                $('#testFilter').html(filterOptions);
            }
        });
}

function loadStats() {
    $.get('ajax/entry_api.php?action=stats')
        .done(function(response) {
            if (response.status === 'success') {
                $('#totalEntries').text(response.data.total || 0);
                $('#pendingEntries').text(response.data.pending || 0);
                $('#completedEntries').text(response.data.completed || 0);
                $('#todayEntries').text(response.data.today || 0);
            }
        });
}

function initializeEventListeners() {
    $('#entryForm').on('submit', function(e) {
        e.preventDefault();
        saveEntryData();
    });

    // Auto-fill amount when test is selected
    $('#entryTest').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const price = selectedOption.data('price');
        if (price) {
            $('#entryAmount').val(price);
        }
    });

    // Initialize Select2
    $('.select2').select2({
        dropdownParent: $('#entryModal'),
        width: '100%'
    });
}

function openAddEntryModal() {
    $('#entryForm')[0].reset();
    $('#entryId').val('');
    $('#modalTitle').text('Add New Test Entry');
    $('#entryModal').modal('show');
}

function editEntry(id) {
    $.get(`patho_api/entry.php?id=${id}`)
        .done(function(response) {
            if (response.status === 'success') {
                const entry = response.data;
                $('#entryId').val(entry.id);
                $('#entryPatient').val(entry.patient_id).trigger('change');
                $('#entryDoctor').val(entry.doctor_id).trigger('change');
                $('#entryTest').val(entry.test_id).trigger('change');
                $('#entryDate').val(entry.entry_date);
                $('#entryResult').val(entry.result);
                $('#entryUnit').val(entry.unit);
                $('#entryStatus').val(entry.status);
                $('#entryAmount').val(entry.amount);
                $('#entryDiscount').val(entry.discount);
                $('#entryNotes').val(entry.notes);
                
                $('#modalTitle').text('Edit Test Entry');
                $('#entryModal').modal('show');
            } else {
                showAlert('Error loading entry data: ' + response.message, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load entry data', 'error');
        });
}

function saveEntryData() {
    const formData = new FormData($('#entryForm')[0]);
    const id = $('#entryId').val();
    const method = id ? 'PUT' : 'POST';
    
    const submitBtn = $('#entryForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: 'patho_api/entry.php',
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                showAlert(id ? 'Entry updated successfully!' : 'Entry added successfully!', 'success');
                $('#entryModal').modal('hide');
                entriesTable.ajax.reload();
                loadStats();
            } else {
                showAlert('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showAlert('Failed to save entry data', 'error');
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function deleteEntry(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this test entry?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `patho_api/entry.php?id=${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('Entry deleted successfully!', 'success');
                        entriesTable.ajax.reload();
                        loadStats();
                    } else {
                        showAlert('Error deleting entry: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showAlert('Failed to delete entry', 'error');
                }
            });
        }
    });
}

function printReport(id) {
    // Open print dialog for report
    window.open(`patho_api/entry.php?id=${id}&action=print`, '_blank');
}

function exportEntries() {
    // Trigger export functionality
    entriesTable.button('.buttons-excel').trigger();
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
</script>

<style>
.small-box {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.small-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
}
</style>

<?php require_once 'inc/footer.php'; ?>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entryModalLabel">Add Entry</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="entryForm">
                    <input type="hidden" id="entryId" name="id">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="entryPatientId">Patient *</label>
                                    <select class="form-control" id="entryPatientId" name="patient_id" required>
                                        <option value="">Select Patient</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="entryDoctorId">Doctor *</label>
                                    <select class="form-control" id="entryDoctorId" name="doctor_id" required>
                                        <option value="">Select Doctor</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="entryTestId">Test *</label>
                                    <select class="form-control" id="entryTestId" name="test_id" required>
                                        <option value="">Select Test</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="entryEntryDate">Entry Date *</label>
                                    <input type="datetime-local" class="form-control" id="entryEntryDate" name="entry_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="entryResultValue">Result Value</label>
                                    <input type="text" class="form-control" id="entryResultValue" name="result_value">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="entryUnit">Unit</label>
                                    <input type="text" class="form-control" id="entryUnit" name="unit">
                                </div>
                                <div class="form-group">
                                    <label for="entryRemarks">Remarks</label>
                                    <textarea class="form-control" id="entryRemarks" name="remarks" rows="4"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="entryStatus">Status *</label>
                                    <select class="form-control" id="entryStatus" name="status" required>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveEntryBtn">Save Entry</button>
            </div>
        </div>
    </div>
 </div>

<?php require_once 'inc/footer.php'; ?>

<script>
function loadDropdownsForEntry(){
    $('#entryPatientId, #entryDoctorId, #entryTestId').empty().append('<option value="">Select</option>');
    $.get('ajax/patient_api.php',{action:'list',ajax:1},function(r){ if(r.success){ var s=''; r.data.forEach(function(p){ s += '<option value="'+p.id+'">'+(p.name||'')+'</option>'; }); $('#entryPatientId').append(s);} },'json');
    $.get('ajax/doctor_api.php',{action:'list',ajax:1},function(r){ if(r.success){ var s=''; r.data.forEach(function(p){ s += '<option value="'+p.id+'">'+(p.name||'')+'</option>'; }); $('#entryDoctorId').append(s);} },'json');
    $.get('ajax/test_api.php',{action:'list',ajax:1},function(r){ if(r.success){ var s=''; r.data.forEach(function(p){ s += '<option value="'+p.id+'">'+(p.name||'')+'</option>'; }); $('#entryTestId').append(s);} },'json');
}

function loadEntries(){
    $.get('ajax/entry_api.php',{action:'list',ajax:1},function(resp){
        if(resp.success){
            var t=''; resp.data.forEach(function(e){
                t += '<tr>'+
                    '<td>'+e.id+'</td>'+
                    '<td>'+ (e.patient_name||'') +'</td>'+
                    '<td>'+ (e.doctor_name||'') +'</td>'+
                    '<td>'+ (e.test_name||'') +'</td>'+
                    '<td>'+ (e.entry_date||'') +'</td>'+
                    '<td>'+ (e.result_value||'') +'</td>'+
                    '<td>'+ (e.unit||'') +'</td>'+
                    '<td>'+ (e.status||'') +'</td>'+
                    '<td><button class="btn btn-sm btn-info view-entry" data-id="'+e.id+'" onclick="viewEntry('+e.id+')">View</button> '+
                        '<button class="btn btn-sm btn-warning edit-entry" data-id="'+e.id+'">Edit</button> '+
                        '<button class="btn btn-sm btn-danger delete-entry" data-id="'+e.id+'">Delete</button></td>'+
                    '</tr>';
            });
            $('#entriesTable tbody').html(t);
            applyEntriesFilters();
        } else toastr.error('Failed to load entries');
    },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
}

function openAddEntryModal(){
    $('#entryForm')[0].reset(); $('#entryId').val(''); $('#entryForm').find('input,textarea,select').prop('disabled', false); $('#saveEntryBtn').show(); $('#entryModalLabel').text('Add Entry'); $('#entryModal').modal('show');
}

// client-side filtering + per-page
function applyEntriesFilters(){
    var q = $('#entriesSearch').val().toLowerCase().trim();
    var per = parseInt($('#entriesPerPage').val()||10,10);
    var shown = 0;
    $('#entriesTable tbody tr').each(function(){
        var row = $(this);
        var text = row.text().toLowerCase();
        var matches = !q || text.indexOf(q) !== -1;
        if(matches && shown < per){ row.show(); shown++; } else { row.toggle(matches && shown < per); }
    });
}

$(function(){
    loadDropdownsForEntry();
    loadEntries();

    $('#saveEntryBtn').click(function(){
        var data = $('#entryForm').serialize() + '&action=save&ajax=1';
        $.post('ajax/entry_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#entryModal').modal('hide'); loadEntries(); } else toastr.error(resp.message||'Save failed'); },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
    });

    // client-side search
    $('#entriesSearch').on('input', function(){ applyEntriesFilters(); });
    $('#entriesSearchClear').click(function(e){ e.preventDefault(); $('#entriesSearch').val(''); applyEntriesFilters(); });
    $('#entriesPerPage').change(function(){ applyEntriesFilters(); });

    // delegated edit handler
    $(document).on('click', '.edit-entry', function(){
        try{
            var id = $(this).data('id');
            $.get('ajax/entry_api.php',{action:'get',id:id,ajax:1}, function(r){
                if(r.success){ var d=r.data;
                    $('#entryId').val(d.id);
                    $('#entryPatientId').val(d.patient_id);
                    $('#entryDoctorId').val(d.doctor_id);
                    $('#entryTestId').val(d.test_id);
                    // convert server 'YYYY-MM-DD HH:MM:SS' to datetime-local 'YYYY-MM-DDTHH:MM'
                    function toLocal(v){ if(!v) return ''; return v.replace(' ','T').slice(0,16); }
                    $('#entryEntryDate').val(toLocal(d.entry_date||''));
                    $('#entryResultValue').val(d.result_value);
                    $('#entryUnit').val(d.unit);
                    $('#entryRemarks').val(d.remarks);
                    $('#entryStatus').val(d.status);
                    $('#entryForm').find('input,textarea,select').prop('disabled', false);
                    $('#saveEntryBtn').show();
                    $('#entryModalLabel').text('Edit Entry');
                    $('#entryModal').modal('show');
                } else toastr.error('Entry not found');
            },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('edit-entry handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // delegated delete handler
    $(document).on('click', '.delete-entry', function(){
        try{
            if(!confirm('Delete entry?')) return;
            var id=$(this).data('id');
            $.post('ajax/entry_api.php',{action:'delete',id:id,ajax:1}, function(resp){ if(resp.success){ toastr.success(resp.message); loadEntries(); } else toastr.error(resp.message||'Delete failed'); },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
        }catch(err){ console.error('delete-entry handler error', err); toastr.error('Error: '+(err.message||err)); }
    });

    // restore modal state on hide
    $('#entryModal').on('hidden.bs.modal', function(){
        $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', false);
        $('#saveEntryBtn').show();
        $('#entryModalLabel').text('Add Entry');
    });
});

// global view fallback
window.viewEntry = function(id){
    try{
        $.get('ajax/entry_api.php',{action:'get',id:id,ajax:1}, function(r){
            if(r.success){ var d=r.data;
                $('#entryId').val(d.id);
                $('#entryPatientId').val(d.patient_id);
                $('#entryDoctorId').val(d.doctor_id);
                $('#entryTestId').val(d.test_id);
                function toLocal(v){ if(!v) return ''; return v.replace(' ','T').slice(0,16); }
                $('#entryEntryDate').val(toLocal(d.entry_date||''));
                $('#entryResultValue').val(d.result_value);
                $('#entryUnit').val(d.unit);
                $('#entryRemarks').val(d.remarks);
                $('#entryStatus').val(d.status);
                $('#entryModalLabel').text('View Entry');
                $('#entryForm').find('input,textarea,select').prop('disabled', true);
                $('#saveEntryBtn').hide();
                $('#entryModal').modal('show');
            } else toastr.error('Entry not found');
        },'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); });
    }catch(err){ console.error('viewEntry error', err); toastr.error('Error: '+(err.message||err)); }
}

</script>