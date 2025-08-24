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
                    <h1>Patients</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Patients</li>
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
                            <h3 class="card-title">Patient Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#patientModal" onclick="openAddPatientModal()">
                                    <i class="fas fa-plus"></i> Add Patient
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
                            <table id="patientsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>UHID</th>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require_once 'inc/connection.php';
                                    
                                    $stmt = $conn->prepare("SELECT id, uhid, name, age, gender, phone, email FROM patients ORDER BY id DESC");
                                    $stmt->execute();
                                    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($patients as $row) {
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['uhid']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>";
                                        echo "<a href='#' class='btn btn-info btn-sm view-patient' data-id='" . $row['id'] . "' title='View'><i class='fas fa-eye'></i></a> ";
                                        echo "<a href='#' class='btn btn-warning btn-sm edit-patient' data-id='" . $row['id'] . "' title='Edit'><i class='fas fa-edit'></i></a> ";
                                        echo "<a href='#' class='btn btn-danger btn-sm delete-patient' data-id='" . $row['id'] . "' title='Delete'><i class='fas fa-trash'></i></a>";
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

<!-- Patient Modal -->
<div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="patientModalLabel">Add Patient</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="patientForm">
                    <input type="hidden" id="patientId" name="id">
                    <div class="form-group">
                        <label for="patientName">Name *</label>
                        <input type="text" class="form-control" id="patientName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="patientMobile">Mobile *</label>
                        <input type="text" class="form-control" id="patientMobile" name="mobile" required>
                    </div>
                    <div class="form-group">
                        <label for="patientFatherHusband">Father/Husband Name</label>
                        <input type="text" class="form-control" id="patientFatherHusband" name="father_husband">
                    </div>
                    <div class="form-group">
                        <label for="patientAddress">Address</label>
                        <textarea class="form-control" id="patientAddress" name="address" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="patientSex">Gender</label>
                        <select class="form-control" id="patientSex" name="sex">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="patientAge">Age</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" class="form-control" id="patientAge" name="age" min="0">
                            </div>
                            <div class="col-6">
                                <select class="form-control" id="patientAgeUnit" name="age_unit">
                                    <option value="Years">Years</option>
                                    <option value="Months">Months</option>
                                    <option value="Days">Days</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="patientUHID">UHID</label>
                        <input type="text" class="form-control" id="patientUHID" name="uhid">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="savePatientBtn">Save Patient</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>