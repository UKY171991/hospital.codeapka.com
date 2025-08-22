<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Patient List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="patient.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Patient</a>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Patients</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search patients...">
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
                        <table class="table table-bordered table-hover" id="patientTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Client Name</th>
                                    <th>Mobile Number</th>
                                    <th>Father/Husband</th>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <th>UHID</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Patient rows will be loaded here by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Patient Details Modal -->
<div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="patientModalLabel">Patient Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="patientModalBody">
                <!-- Patient details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editPatientBtn">Edit Patient</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadPatients() {
    $.get('ajax/patient_ajax.php', {action: 'list'}, function(data) {
        $('#patientTable tbody').html(data);
    });
}

function viewPatient(id) {
    $.get('ajax/patient_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Client Name:</strong></td><td>${data.client_name || 'N/A'}</td></tr>
                            <tr><td><strong>Mobile Number:</strong></td><td>${data.mobile_number || 'N/A'}</td></tr>
                            <tr><td><strong>Father/Husband:</strong></td><td>${data.father_or_husband || 'N/A'}</td></tr>
                            <tr><td><strong>Gender:</strong></td><td>${data.gender || 'N/A'}</td></tr>
                            <tr><td><strong>Age:</strong></td><td>${data.age || 'N/A'} ${data.age_unit || ''}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>UHID:</strong></td><td>${data.uhid || 'N/A'}</td></tr>
                            <tr><td><strong>Address:</strong></td><td>${data.address || 'N/A'}</td></tr>
                            <tr><td><strong>Added By:</strong></td><td>${data.added_by || 'N/A'}</td></tr>
                            <tr><td><strong>Created At:</strong></td><td>${data.created_at || 'N/A'}</td></tr>
                            <tr><td><strong>Updated At:</strong></td><td>${data.updated_at || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            $('#patientModalBody').html(html);
            $('#patientModal').modal('show');
        }
    });
}

$(document).ready(function() {
    loadPatients();
    
    // Search functionality
    $('input[name="table_search"]').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#patientTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Edit button in modal
    $('#editPatientBtn').click(function() {
        let patientId = $('#patientModalBody').find('td:first').text();
        window.location.href = 'patient.php?id=' + patientId;
    });
});
</script>

<?php include 'inc/footer.php'; ?>
