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
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary mb-2" id="addPatientBtn"><i class="fas fa-plus"></i> Add Patient</button>
                    <table class="table table-bordered table-hover" id="patientTable">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Gender</th>
                                <th>Age</th>
                                <th>UHID</th>
                                <th>Added By</th>
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
    </section>
</div>

<!-- Add/Edit Patient Modal -->
<div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="patientForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="patientModalLabel">Add Patient</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="patientId">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="client_name" id="client_name" required>
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="text" class="form-control" name="mobile_number" id="mobile_number" required>
                    </div>
                    <div class="form-group">
                        <label>Father/Husband</label>
                        <input type="text" class="form-control" name="father_or_husband" id="father_or_husband">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" id="address"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Gender</label>
                        <select class="form-control" name="gender" id="gender">
                            <option value="">Select</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Age</label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="age" id="age" min="0">
                            <select class="form-control" name="age_unit" id="age_unit" style="max-width:100px;">
                                <option value="Years">Years</option>
                                <option value="Months">Months</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>UHID</label>
                        <input type="text" class="form-control" name="uhid" id="uhid">
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

<script>
function loadPatients() {
        $.get('ajax/patient_ajax.php', {action: 'list'}, function(data) {
                $('#patientTable tbody').html(data);
        });
}

$(function() {
        loadPatients();

        $('#addPatientBtn').click(function() {
                $('#patientForm')[0].reset();
                $('#patientId').val('');
                $('#patientModalLabel').text('Add Patient');
                $('#patientModal').modal('show');
        });

        $('#patientTable').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.get('ajax/patient_ajax.php', {action: 'get', id: id}, function(patient) {
                        $('#patientId').val(patient.id);
                        $('#client_name').val(patient.client_name);
                        $('#mobile_number').val(patient.mobile_number);
                        $('#father_or_husband').val(patient.father_or_husband);
                        $('#address').val(patient.address);
                        $('#gender').val(patient.gender);
                        $('#age').val(patient.age);
                        $('#age_unit').val(patient.age_unit);
                        $('#uhid').val(patient.uhid);
                        $('#patientModalLabel').text('Edit Patient');
                        $('#patientModal').modal('show');
                }, 'json');
        });

        $('#patientForm').submit(function(e) {
                e.preventDefault();
                $.post('ajax/patient_ajax.php', $(this).serialize() + '&action=save', function(resp) {
                        $('#patientModal').modal('hide');
                        loadPatients();
                });
        });

        $('#patientTable').on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this patient?')) {
                        var id = $(this).data('id');
                        $.post('ajax/patient_ajax.php', {action: 'delete', id: id}, function(resp) {
                                loadPatients();
                        });
                }
        });
});
</script>
<?php include 'inc/footer.php'; ?>
