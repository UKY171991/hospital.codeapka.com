<?php require_once 'inc/auth.php'; ?>
<?php
// doctor.php
// Dummy doctor data for demonstration
$doctors = [
    ["id" => 1, "name" => "Dr. A. Kumar", "specialty" => "Pathologist", "email" => "akumar@example.com"],
    ["id" => 2, "name" => "Dr. S. Mehta", "specialty" => "Microbiologist", "email" => "smehta@example.com"],
    ["id" => 3, "name" => "Dr. R. Singh", "specialty" => "Hematologist", "email" => "rsingh@example.com"],
];
?>
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
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Specialty</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($doctors as $doctor): ?>
                            <tr>
                                <td><?= htmlspecialchars($doctor['id']) ?></td>
                                <td><?= htmlspecialchars($doctor['name']) ?></td>
                                <td><?= htmlspecialchars($doctor['specialty']) ?></td>
                                <td><?= htmlspecialchars($doctor['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include 'inc/footer.php'; ?>
