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
                    <h1>Doctors</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Doctors</li>
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
                            <h3 class="card-title">Doctor Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#doctorModal" onclick="openAddDoctorModal()">
                                    <i class="fas fa-plus"></i> Add Doctor
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
                            <table id="doctorsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Specialization</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require_once 'inc/connection.php';
                                    
                                    $stmt = $conn->prepare("SELECT id, name, specialization, phone, email FROM doctors ORDER BY id DESC");
                                    $stmt->execute();
                                    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($doctors as $row) {
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['specialization']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>";
                                        echo "<a href='#' class='btn btn-info btn-sm view-doctor' data-id='" . $row['id'] . "' title='View'><i class='fas fa-eye'></i></a> ";
                                        echo "<a href='#' class='btn btn-warning btn-sm edit-doctor' data-id='" . $row['id'] . "' title='Edit'><i class='fas fa-edit'></i></a> ";
                                        echo "<a href='#' class='btn btn-danger btn-sm delete-doctor' data-id='" . $row['id'] . "' title='Delete'><i class='fas fa-trash'></i></a>";
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

<!-- Doctor Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="doctorModalLabel">Add Doctor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="doctorForm">
                    <input type="hidden" id="doctorId" name="id">
                    <div class="form-group">
                        <label for="doctorName">Name *</label>
                        <input type="text" class="form-control" id="doctorName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="doctorSpecialization">Specialization</label>
                        <input type="text" class="form-control" id="doctorSpecialization" name="specialization">
                    </div>
                    <div class="form-group">
                        <label for="doctorPhone">Phone</label>
                        <input type="text" class="form-control" id="doctorPhone" name="phone">
                    </div>
                    <div class="form-group">
                        <label for="doctorEmail">Email</label>
                        <input type="email" class="form-control" id="doctorEmail" name="email">
                    </div>
                    <div class="form-group">
                        <label for="doctorAddress">Address</label>
                        <textarea class="form-control" id="doctorAddress" name="address" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDoctorBtn">Save Doctor</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>
