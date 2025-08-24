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
                    <h1>Test Entries</h1>
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Test Entry Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#entryModal" onclick="openAddEntryModal()">
                                    <i class="fas fa-plus"></i> Add Entry
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="entriesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Test</th>
                                        <th>Referring Doctor</th>
                                        <th>Entry Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require_once 'inc/connection.php';
                                    
                                    $stmt = $conn->prepare("SELECT e.id, p.name as patient_name, d.name as doctor_name, t.name as test_name, 
                                            e.referring_doctor, e.entry_date, e.status
                                            FROM entries e
                                            LEFT JOIN patients p ON e.patient_id = p.id
                                            LEFT JOIN doctors d ON e.doctor_id = d.id
                                            LEFT JOIN tests t ON e.test_id = t.id
                                            ORDER BY e.id DESC");
                                    $stmt->execute();
                                    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($entries as $row) {
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['patient_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['doctor_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['test_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['referring_doctor']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['entry_date']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "<td>";
                                        echo "<a href='#' class='btn btn-info btn-sm view-entry' data-id='" . $row['id'] . "' title='View'><i class='fas fa-eye'></i></a> ";
                                        echo "<a href='#' class='btn btn-warning btn-sm edit-entry' data-id='" . $row['id'] . "' title='Edit'><i class='fas fa-edit'></i></a> ";
                                        echo "<a href='#' class='btn btn-danger btn-sm delete-entry' data-id='" . $row['id'] . "' title='Delete'><i class='fas fa-trash'></i></a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
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
                    <div class="form-group">
                        <label for="entryPatientId">Patient *</label>
                        <select class="form-control" id="entryPatientId" name="patient_id" required>
                            <option value="">Select Patient</option>
                            <?php
                            // Get patients for dropdown
                            $stmt = $conn->prepare("SELECT id, name FROM patients ORDER BY name");
                            $stmt->execute();
                            $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($patients as $patient) {
                                echo "<option value='" . $patient['id'] . "'>" . htmlspecialchars($patient['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="entryDoctorId">Doctor *</label>
                        <select class="form-control" id="entryDoctorId" name="doctor_id" required>
                            <option value="">Select Doctor</option>
                            <?php
                            // Get doctors for dropdown
                            $stmt = $conn->prepare("SELECT id, name FROM doctors ORDER BY name");
                            $stmt->execute();
                            $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($doctors as $doctor) {
                                echo "<option value='" . $doctor['id'] . "'>" . htmlspecialchars($doctor['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="entryTestId">Test *</label>
                        <select class="form-control" id="entryTestId" name="test_id" required>
                            <option value="">Select Test</option>
                            <?php
                            // Get tests for dropdown
                            $stmt = $conn->prepare("SELECT id, name FROM tests ORDER BY name");
                            $stmt->execute();
                            $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($tests as $test) {
                                echo "<option value='" . $test['id'] . "'>" . htmlspecialchars($test['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="entryReferringDoctor">Referring Doctor</label>
                        <input type="text" class="form-control" id="entryReferringDoctor" name="referring_doctor">
                    </div>
                    <div class="form-group">
                        <label for="entryEntryDate">Entry Date *</label>
                        <input type="date" class="form-control" id="entryEntryDate" name="entry_date" required>
                    </div>
                    <div class="form-group">
                        <label for="entryResultValue">Result Value</label>
                        <input type="text" class="form-control" id="entryResultValue" name="result_value">
                    </div>
                    <div class="form-group">
                        <label for="entryUnit">Unit</label>
                        <input type="text" class="form-control" id="entryUnit" name="unit">
                    </div>
                    <div class="form-group">
                        <label for="entryRemarks">Remarks</label>
                        <textarea class="form-control" id="entryRemarks" name="remarks" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="entryStatus">Status *</label>
                        <select class="form-control" id="entryStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
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