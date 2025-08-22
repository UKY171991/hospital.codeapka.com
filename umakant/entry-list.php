<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Entry List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary" onclick="addEntry()"><i class="fas fa-plus"></i> Add New Entry</button>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Entries</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search entries...">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="entryTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Patient Name</th>
                                    <th>Doctor Name</th>
                                    <th>Test Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Entry rows will be loaded here by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Entry Details Modal -->
<div class="modal fade" id="entryModal" tabindex="-1" role="dialog" aria-labelledby="entryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="entryModalLabel">Entry Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="entryModalBody">
                <!-- Entry details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Entry Modal -->
<div class="modal fade" id="entryFormModal" tabindex="-1" role="dialog" aria-labelledby="entryFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="entryForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="entryFormModalLabel">Add New Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="entryId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patient_id">Patient <span class="text-danger">*</span></label>
                                <select class="form-control" id="patient_id" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctor_id">Doctor</label>
                                <select class="form-control" id="doctor_id" name="doctor_id">
                                    <option value="">Select Doctor</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="test_id">Test <span class="text-danger">*</span></label>
                                <select class="form-control" id="test_id" name="test_id" required>
                                    <option value="">Select Test</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entry_date">Entry Date</label>
                                <input type="date" class="form-control" id="entry_date" name="entry_date">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
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
                                <label for="result_value">Result Value</label>
                                <input type="text" class="form-control" id="result_value" name="result_value">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit">Unit</label>
                                <input type="text" class="form-control" id="unit" name="unit">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success/Error Alert -->
<div class="alert alert-success alert-dismissible fade" id="successAlert" style="display: none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span id="successMessage"></span>
</div>

<div class="alert alert-danger alert-dismissible fade" id="errorAlert" style="display: none;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <span id="errorMessage"></span>
</div>

<script>
function loadEntries() {
    $.get('ajax/entry_ajax.php', {action: 'list'}, function(data) {
        $('#entryTable tbody').html(data);
    });
}

function loadDropdowns() {
    // Load patients
    $.get('ajax/entry_ajax.php', {action: 'get_patients'}, function(data) {
        let options = '<option value="">Select Patient</option>';
        data.forEach(function(patient) {
            options += '<option value="' + patient.id + '">' + patient.client_name + '</option>';
        });
        $('#patient_id').html(options);
    }, 'json');
    
    // Load doctors
    $.get('ajax/entry_ajax.php', {action: 'get_doctors'}, function(data) {
        let options = '<option value="">Select Doctor</option>';
        data.forEach(function(doctor) {
            options += '<option value="' + doctor.id + '">' + doctor.name + '</option>';
        });
        $('#doctor_id').html(options);
    }, 'json');
    
    // Load tests
    $.get('ajax/entry_ajax.php', {action: 'get_tests'}, function(data) {
        let options = '<option value="">Select Test</option>';
        data.forEach(function(test) {
            options += '<option value="' + test.id + '">' + test.test_name + ' (₹' + test.price + ')</option>';
        });
        $('#test_id').html(options);
    }, 'json');
}

function addEntry() {
    $('#entryForm')[0].reset();
    $('#entryId').val('');
    $('#entryFormModalLabel').text('Add New Entry');
    $('#entryFormModal').modal('show');
}

function editEntry(id) {
    $.get('ajax/entry_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            $('#entryId').val(data.id);
            $('#patient_id').val(data.patient_id);
            $('#doctor_id').val(data.doctor_id);
            $('#test_id').val(data.test_id);
            $('#entry_date').val(data.entry_date);
            $('#result_value').val(data.result_value);
            $('#unit').val(data.unit);
            $('#remarks').val(data.remarks);
            $('#status').val(data.status);
            $('#amount').val(data.amount);
            
            $('#entryFormModalLabel').text('Edit Entry');
            $('#entryFormModal').modal('show');
        }
    });
}

function viewEntry(id) {
    $.get('ajax/entry_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Patient:</strong></td><td>${data.patient_name || 'N/A'}</td></tr>
                            <tr><td><strong>Doctor:</strong></td><td>${data.doctor_name || 'N/A'}</td></tr>
                            <tr><td><strong>Test:</strong></td><td>${data.test_name || 'N/A'}</td></tr>
                            <tr><td><strong>Amount:</strong></td><td>₹${data.amount || '0'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Status:</strong></td><td><span class="badge badge-${data.status === 'completed' ? 'success' : 'warning'}">${data.status || 'pending'}</span></td></tr>
                            <tr><td><strong>Entry Date:</strong></td><td>${data.entry_date || 'N/A'}</td></tr>
                            <tr><td><strong>Result Value:</strong></td><td>${data.result_value || 'N/A'}</td></tr>
                            <tr><td><strong>Unit:</strong></td><td>${data.unit || 'N/A'}</td></tr>
                            <tr><td><strong>Added By:</strong></td><td>${data.added_by || 'N/A'}</td></tr>
                            <tr><td><strong>Created At:</strong></td><td>${data.created_at || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Remarks:</strong></h6>
                        <p>${data.remarks || 'No remarks available'}</p>
                    </div>
                </div>
            `;
            $('#entryModalBody').html(html);
            $('#entryModal').modal('show');
        }
    });
}

function deleteEntry(id) {
    if (confirm('Are you sure you want to delete this entry?')) {
        $.post('ajax/entry_ajax.php', {action: 'delete', id: id}, function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                loadEntries();
            } else {
                showAlert('error', response.message);
            }
        }, 'json');
    }
}

function showAlert(type, message) {
    if (type === 'success') {
        $('#successMessage').text(message);
        $('#successAlert').show().addClass('show');
        setTimeout(function() {
            $('#successAlert').hide().removeClass('show');
        }, 3000);
    } else {
        $('#errorMessage').text(message);
        $('#errorAlert').show().addClass('show');
        setTimeout(function() {
            $('#errorAlert').hide().removeClass('show');
        }, 3000);
    }
}

$(document).ready(function() {
    loadEntries();
    loadDropdowns();
    
    // Search functionality
    $('input[name="table_search"]').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#entryTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Form submission
    $('#entryForm').on('submit', function(e) {
        e.preventDefault();
        
        $.post('ajax/entry_ajax.php', $(this).serialize() + '&action=save', function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                $('#entryFormModal').modal('hide');
                loadEntries();
            } else {
                showAlert('error', response.message);
            }
        }, 'json');
    });
});
</script>

<?php include 'inc/footer.php'; ?>
