<?php require_once 'inc/auth.php'; ?>
<?php
// patient.php
// Dummy patient data for demonstration
$patients = [
    ["id" => 1, "name" => "Rahul Sharma", "age" => 32, "gender" => "Male", "email" => "rahul.sharma@example.com"],
    ["id" => 2, "name" => "Priya Verma", "age" => 27, "gender" => "Female", "email" => "priya.verma@example.com"],
    ["id" => 3, "name" => "Amit Patel", "age" => 45, "gender" => "Male", "email" => "amit.patel@example.com"],
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
                    <h1>Patient List</h1>
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
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?= htmlspecialchars($patient['id']) ?></td>
                                <td><?= htmlspecialchars($patient['name']) ?></td>
                                <td><?= htmlspecialchars($patient['age']) ?></td>
                                <td><?= htmlspecialchars($patient['gender']) ?></td>
                                <td><?= htmlspecialchars($patient['email']) ?></td>
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
