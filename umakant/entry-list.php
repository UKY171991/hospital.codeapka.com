<?php require_once 'inc/auth.php'; ?>
?>
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
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <button class="btn btn-primary mb-2" id="addEntryBtn"><i class="fas fa-plus"></i> Add Entry</button>
                    <table class="table table-bordered table-hover" id="entryTable">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Test</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Added By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Entry rows will be loaded here by AJAX -->
                        </tbody>
                    </table>
</div>

<!-- Add/Edit Entry Modal -->
<div class="modal fade" id="entryModal" tabindex="-1" role="dialog" aria-labelledby="entryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="entryForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="entryModalLabel">Add Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="entryId">
                    <div class="form-group">
                        <label>Patient</label>
                        <select class="form-control" name="patient_id" id="patient_id" required>
                            <option value="">Select Patient</option>
                            <?php
                            require_once 'inc/connection.php';
                            $stmt = $pdo->query('SELECT id, client_name FROM patients ORDER BY client_name');
                            while ($row = $stmt->fetch()) {
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['client_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Doctor</label>
                        <select class="form-control" name="doctor_id" id="doctor_id">
                            <option value="">Select Doctor</option>
                            <?php
                            $stmt = $pdo->query('SELECT id, name FROM doctors ORDER BY name');
                            while ($row = $stmt->fetch()) {
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Test</label>
                        <select class="form-control" name="test_id" id="test_id" required>
                            <option value="">Select Test</option>
                            <?php
                            $stmt = $pdo->query('SELECT id, test_name FROM tests ORDER BY test_name');
                            while ($row = $stmt->fetch()) {
                                echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['test_name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Entry Date</label>
                        <input type="datetime-local" class="form-control" name="entry_date" id="entry_date">
                    </div>
                    <div class="form-group">
                        <label>Result Value</label>
                        <input type="text" class="form-control" name="result_value" id="result_value">
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <input type="text" class="form-control" name="unit" id="unit">
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks" id="remarks"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" id="status">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                        </select>
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
function loadEntries() {
        $.get('ajax/entry_ajax.php', {action: 'list'}, function(data) {
                $('#entryTable tbody').html(data);
        });
}

$(function() {
        loadEntries();

        $('#addEntryBtn').click(function() {
                $('#entryForm')[0].reset();
                $('#entryId').val('');
                $('#entryModalLabel').text('Add Entry');
                $('#entryModal').modal('show');
        });

        $('#entryTable').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.get('ajax/entry_ajax.php', {action: 'get', id: id}, function(entry) {
                        $('#entryId').val(entry.id);
                        $('#patient_id').val(entry.patient_id);
                        $('#doctor_id').val(entry.doctor_id);
                        $('#test_id').val(entry.test_id);
                        $('#entry_date').val(entry.entry_date ? entry.entry_date.replace(' ', 'T') : '');
                        $('#result_value').val(entry.result_value);
                        $('#unit').val(entry.unit);
                        $('#remarks').val(entry.remarks);
                        $('#status').val(entry.status);
                        $('#entryModalLabel').text('Edit Entry');
                        $('#entryModal').modal('show');
                }, 'json');
        });

        $('#entryForm').submit(function(e) {
                e.preventDefault();
                $.post('ajax/entry_ajax.php', $(this).serialize() + '&action=save', function(resp) {
                        $('#entryModal').modal('hide');
                        loadEntries();
                });
        });

        $('#entryTable').on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this entry?')) {
                        var id = $(this).data('id');
                        $.post('ajax/entry_ajax.php', {action: 'delete', id: id}, function(resp) {
                                loadEntries();
                        });
                }
        });
});
</script>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include 'inc/footer.php'; ?>
