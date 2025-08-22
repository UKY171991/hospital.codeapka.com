<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Doctor List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="doctor.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Doctor</a>
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Doctors</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search doctors...">
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
                        <table class="table table-bordered table-hover" id="doctorTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Qualification</th>
                                    <th>Specialization</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Registration No</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Doctor rows will be loaded here by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Doctor Details Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="doctorModalLabel">Doctor Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="doctorModalBody">
                <!-- Doctor details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editDoctorBtn">Edit Doctor</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadDoctors() {
    $.get('ajax/doctor_ajax.php', {action: 'list'}, function(data) {
        $('#doctorTable tbody').html(data);
    });
}

function viewDoctor(id) {
    $.get('ajax/doctor_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Name:</strong></td><td>${data.name || 'N/A'}</td></tr>
                            <tr><td><strong>Qualification:</strong></td><td>${data.qualification || 'N/A'}</td></tr>
                            <tr><td><strong>Specialization:</strong></td><td>${data.specialization || 'N/A'}</td></tr>
                            <tr><td><strong>Phone:</strong></td><td>${data.phone || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Email:</strong></td><td>${data.email || 'N/A'}</td></tr>
                            <tr><td><strong>Address:</strong></td><td>${data.address || 'N/A'}</td></tr>
                            <tr><td><strong>Registration No:</strong></td><td>${data.registration_no || 'N/A'}</td></tr>
                            <tr><td><strong>Added By:</strong></td><td>${data.added_by || 'N/A'}</td></tr>
                            <tr><td><strong>Created At:</strong></td><td>${data.created_at || 'N/A'}</td></tr>
                            <tr><td><strong>Updated At:</strong></td><td>${data.updated_at || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
            `;
            $('#doctorModalBody').html(html);
            $('#doctorModal').modal('show');
        }
    });
}

$(document).ready(function() {
    loadDoctors();
    
    // Search functionality
    $('input[name="table_search"]').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#doctorTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Edit button in modal
    $('#editDoctorBtn').click(function() {
        let doctorId = $('#doctorModalBody').find('td:first').text();
        window.location.href = 'doctor.php?id=' + doctorId;
    });
});
</script>

<?php include 'inc/footer.php'; ?>
