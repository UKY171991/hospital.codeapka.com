<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Complete Data Export</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                </div>
            </div>
        </div>
    </section>
    
    <section class="content">
        <div class="container-fluid">
            <!-- Users Data -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users Data</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-success" onclick="exportTable('usersTable', 'Users_Data')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="usersTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Expire</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once 'inc/connection.php';
                                $stmt = $pdo->query('SELECT * FROM users ORDER BY id');
                                while ($row = $stmt->fetch()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['email'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['full_name'] ?? 'N/A') . '</td>';
                                    echo '<td><span class="badge badge-' . ($row['role'] === 'admin' ? 'danger' : 'info') . '">' . htmlspecialchars($row['role']) . '</span></td>';
                                    echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['created_at'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['updated_at'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['expire'] ?? 'N/A') . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Doctors Data -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Doctors Data</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-success" onclick="exportTable('doctorsTable', 'Doctors_Data')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="doctorsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Qualification</th>
                                    <th>Specialization</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Registration No</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query('SELECT * FROM doctors ORDER BY id');
                                while ($row = $stmt->fetch()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['qualification'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['specialization'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['phone'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['email'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['address'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['registration_no'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['created_at'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['updated_at'] ?? 'N/A') . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Patients Data -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Patients Data</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-success" onclick="exportTable('patientsTable', 'Patients_Data')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="patientsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Client Name</th>
                                    <th>Mobile Number</th>
                                    <th>Father/Husband</th>
                                    <th>Address</th>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <th>Age Unit</th>
                                    <th>UHID</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query('SELECT * FROM patients ORDER BY id');
                                while ($row = $stmt->fetch()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['client_name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['mobile_number']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['father_or_husband'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['address'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['gender'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['age'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['age_unit'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['uhid'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['created_at'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['updated_at'] ?? 'N/A') . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tests Data -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tests Data</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-success" onclick="exportTable('testsTable', 'Tests_Data')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="testsTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Test Name</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Unit</th>
                                    <th>Reference Range</th>
                                    <th>Min Value</th>
                                    <th>Max Value</th>
                                    <th>Method</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query('SELECT * FROM tests ORDER BY id');
                                while ($row = $stmt->fetch()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['test_name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['category'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['description'] ?? 'N/A') . '</td>';
                                    echo '<td>₹' . htmlspecialchars($row['price']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['unit'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['reference_range'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['min_value'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['max_value'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['method'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['created_at'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['updated_at'] ?? 'N/A') . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Test Categories Data -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Test Categories Data</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-success" onclick="exportTable('categoriesTable', 'Test_Categories_Data')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="categoriesTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query('SELECT * FROM test_categories ORDER BY id');
                                while ($row = $stmt->fetch()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['description'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['created_at'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['updated_at'] ?? 'N/A') . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Entries Data -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Entries Data</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-success" onclick="exportTable('entriesTable', 'Entries_Data')">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="entriesTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Patient ID</th>
                                    <th>Doctor ID</th>
                                    <th>Test ID</th>
                                    <th>Entry Date</th>
                                    <th>Result Value</th>
                                    <th>Unit</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Added By</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query('SELECT * FROM entries ORDER BY id');
                                while ($row = $stmt->fetch()) {
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['patient_id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['doctor_id'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['test_id']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['entry_date'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['result_value'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['unit'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['remarks'] ?? 'N/A') . '</td>';
                                    echo '<td><span class="badge badge-' . ($row['status'] === 'completed' ? 'success' : 'warning') . '">' . htmlspecialchars($row['status'] ?? 'pending') . '</span></td>';
                                    echo '<td>' . htmlspecialchars($row['added_by']) . '</td>';
                                    echo '<td>' . htmlspecialchars($row['created_at'] ?? 'N/A') . '</td>';
                                    echo '<td>' . htmlspecialchars($row['updated_at'] ?? 'N/A') . '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function exportTable(tableId, filename) {
    let table = document.getElementById(tableId);
    let html = table.outerHTML;
    let url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
    let downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    downloadLink.href = url;
    downloadLink.download = filename + '.xls';
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Initialize DataTables for better functionality
$(document).ready(function() {
    // Add search functionality to all tables
    $('input[name="table_search"]').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        let tableId = $(this).closest('.card').find('table').attr('id');
        $('#' + tableId + ' tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>

<?php include 'inc/footer.php'; ?>
