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
                    <a href="entry.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Entry</a>
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
                <button type="button" class="btn btn-primary" id="editEntryBtn">Edit Entry</button>
            </div>
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

function viewEntry(id) {
    $.get('ajax/entry_ajax.php', {action: 'get', id: id}, function(data) {
        if (data) {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>ID:</strong></td><td>${data.id}</td></tr>
                            <tr><td><strong>Patient Name:</strong></td><td>${data.patient_name || 'N/A'}</td></tr>
                            <tr><td><strong>Doctor Name:</strong></td><td>${data.doctor_name || 'N/A'}</td></tr>
                            <tr><td><strong>Test Name:</strong></td><td>${data.test_name || 'N/A'}</td></tr>
                            <tr><td><strong>Amount:</strong></td><td>₹${data.amount || 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><td><strong>Status:</strong></td><td><span class="badge badge-${data.status === 'completed' ? 'success' : 'warning'}">${data.status || 'N/A'}</span></td></tr>
                            <tr><td><strong>Added By:</strong></td><td>${data.added_by || 'N/A'}</td></tr>
                            <tr><td><strong>Created At:</strong></td><td>${data.created_at || 'N/A'}</td></tr>
                            <tr><td><strong>Updated At:</strong></td><td>${data.updated_at || 'N/A'}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Notes:</strong></h6>
                        <p>${data.notes || 'No notes available'}</p>
                    </div>
                </div>
            `;
            $('#entryModalBody').html(html);
            $('#entryModal').modal('show');
        }
    });
}

$(document).ready(function() {
    loadEntries();
    
    // Search functionality
    $('input[name="table_search"]').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('#entryTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Edit button in modal
    $('#editEntryBtn').click(function() {
        let entryId = $('#entryModalBody').find('td:first').text();
        window.location.href = 'entry.php?id=' + entryId;
    });
});
</script>

<?php include 'inc/footer.php'; ?>
