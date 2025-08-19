<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>
?>
<div class="content-wrapper">
        <section class="content-header">
                <div class="container-fluid">
                        <div class="row mb-2">
                                <div class="col-sm-6">
                                        <h1>Doctor List</h1>
                                </div>
                                <div class="col-sm-6 text-right">
                                        <button class="btn btn-primary" id="addDoctorBtn"><i class="fas fa-plus"></i> Add Doctor</button>
                                </div>
                        </div>
                </div>
        </section>
        <section class="content">
                <div class="container-fluid">
                        <div class="card">
                                <div class="card-body">
                                        <table class="table table-bordered table-hover" id="doctorTable">
                                                <thead class="thead-light">
                                                        <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Specialization</th>
                                                                <th>Email</th>
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
        </section>
</div>

<!-- Add/Edit Doctor Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="doctorForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="doctorModalLabel">Add Doctor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="doctorId">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label>Qualification</label>
                        <input type="text" class="form-control" name="qualification" id="qualification">
                    </div>
                    <div class="form-group">
                        <label>Specialization</label>
                        <input type="text" class="form-control" name="specialization" id="specialization">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" id="phone">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" id="address"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Registration No</label>
                        <input type="text" class="form-control" name="registration_no" id="registration_no">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
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

$(function() {
        loadDoctors();

        $('#addDoctorBtn').click(function() {
                $('#doctorForm')[0].reset();
                $('#doctorId').val('');
                $('#doctorModalLabel').text('Add Doctor');
                $('#doctorModal').modal('show');
        });

        $('#doctorTable').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.get('ajax/doctor_ajax.php', {action: 'get', id: id}, function(doctor) {
                        $('#doctorId').val(doctor.id);
                        $('#name').val(doctor.name);
                        $('#qualification').val(doctor.qualification);
                        $('#specialization').val(doctor.specialization);
                        $('#phone').val(doctor.phone);
                        $('#email').val(doctor.email);
                        $('#address').val(doctor.address);
                        $('#registration_no').val(doctor.registration_no);
                        $('#doctorModalLabel').text('Edit Doctor');
                        $('#doctorModal').modal('show');
                }, 'json');
        });

        $('#doctorForm').submit(function(e) {
                e.preventDefault();
                $.post('ajax/doctor_ajax.php', $(this).serialize() + '&action=save', function(resp) {
                        $('#doctorModal').modal('hide');
                        loadDoctors();
                });
        });

        $('#doctorTable').on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this doctor?')) {
                        var id = $(this).data('id');
                        $.post('ajax/doctor_ajax.php', {action: 'delete', id: id}, function(resp) {
                                loadDoctors();
                        });
                }
        });
});
</script>
<?php include 'inc/footer.php'; ?>
