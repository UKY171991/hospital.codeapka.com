<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user-injured mr-2"></i>OPD Patient Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">OPD Patients</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalPatients">0</h3>
                            <p>Total Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="todayPatients">0</h3>
                            <p>Today's Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="weekPatients">0</h3>
                            <p>This Week</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="monthPatients">0</h3>
                            <p>This Month</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-1"></i>
                                Patient Directory
                            </h3>
                            <div class="card-tools">
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-primary btn-sm mr-2" onclick="openAddPatientModal()">
                                        <i class="fas fa-plus mr-1"></i>Add Patient
                                    </button>
                                    <select id="filterDoctor" class="form-control form-control-sm mr-2" style="width: 200px;">
                                        <option value="">All Doctors</option>
                                    </select>
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <input type="text" id="searchPatient" class="form-control" placeholder="Search patient...">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Patients Table -->
                            <div class="table-responsive">
                                <table id="opdPatientTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Patient Name</th>
                                            <th>Phone</th>
                                            <th>Age</th>
                                            <th>Gender</th>
                                            <th>Total Visits</th>
                                            <th>First Visit</th>
                                            <th>Last Visit</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patientTableBody">
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <i class="fas fa-spinner fa-spin"></i> Loading...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Patient Modal -->
<div class="modal fade" id="addPatientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus mr-2"></i>
                    Add New Patient
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addPatientForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientName">Patient Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="patientName" name="patientName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientPhone">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="patientPhone" name="patientPhone" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientAge">Age <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="patientAge" name="patientAge" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientGender">Gender <span class="text-danger">*</span></label>
                                <select class="form-control" id="patientGender" name="patientGender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="patientEmail">Email</label>
                        <input type="email" class="form-control" id="patientEmail" name="patientEmail">
                    </div>
                    <div class="form-group">
                        <label for="patientAddress">Address</label>
                        <textarea class="form-control" id="patientAddress" name="patientAddress" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveNewPatient()">
                    <i class="fas fa-save"></i> Save Patient
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Patient History Modal -->
<div class="modal fade" id="viewPatientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-injured mr-2"></i>
                    Patient History
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="patientHistoryContent">
                    <!-- Patient history will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-info" onclick="printPatientHistory()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Page specific JavaScript -->
<script src="assets/js/opd_patient.js?v=<?php echo time(); ?>"></script>

<script>
function openAddPatientModal() {
    document.getElementById('addPatientForm').reset();
    
    // Ensure all form inputs are enabled
    const form = document.getElementById('addPatientForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.disabled = false;
        input.removeAttribute('readonly');
    });
    
    $('#addPatientModal').modal('show');
}

function saveNewPatient() {
    const form = document.getElementById('addPatientForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    
    fetch('opd_api/add_patient.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Patient added successfully!');
            $('#addPatientModal').modal('hide');
            form.reset();
            if (typeof loadPatients === 'function') {
                loadPatients(); // Reload the patient list
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to add patient'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the patient. Please check the console for details.');
    });
}
</script>

<style>
.small-box {
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 0.25rem;
}

.small-box > .inner {
    padding: 10px;
}

.small-box .icon {
    transition: all .3s linear;
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 90px;
    color: rgba(0,0,0,0.15);
}

.table-responsive {
    border-radius: 0.375rem;
    overflow-x: auto;
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

#opdPatientTable {
    width: 100% !important;
}

#opdPatientTable thead th {
    vertical-align: middle;
    padding: 12px 8px;
    font-size: 14px;
}

#opdPatientTable tbody td {
    vertical-align: middle;
    padding: 8px;
    font-size: 13px;
}

.patient-card {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
}

.patient-card h5 {
    margin-bottom: 10px;
    color: #007bff;
}

.history-section {
    margin-top: 20px;
}

.history-section h6 {
    background: #f8f9fa;
    padding: 10px;
    border-left: 4px solid #007bff;
    margin-bottom: 15px;
}

/* Modal Form Fixes */
#addPatientModal .form-control,
#addPatientModal .form-control:focus,
#addPatientModal input[type="text"],
#addPatientModal input[type="tel"],
#addPatientModal input[type="email"],
#addPatientModal input[type="number"],
#addPatientModal textarea,
#addPatientModal select {
    background-color: #fff !important;
    color: #333 !important;
    border: 1px solid #ced4da !important;
    padding: 0.375rem 0.75rem !important;
    cursor: text !important;
    pointer-events: auto !important;
}

#addPatientModal .form-control:focus {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
}

#addPatientModal .form-group label {
    color: #333 !important;
    font-weight: 500 !important;
}

#addPatientModal .modal-body {
    background-color: #fff !important;
}

#addPatientModal .form-control:disabled,
#addPatientModal .form-control[disabled] {
    background-color: #e9ecef !important;
    opacity: 1 !important;
}
</style>

<?php require_once 'inc/footer.php'; ?>
