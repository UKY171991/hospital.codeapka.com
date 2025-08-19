<?php require_once 'inc/auth.php'; ?>
<?php
// entry-list.php
// Dummy entry data for demonstration
$entries = [
    ["id" => 1, "patient" => "Rahul Sharma", "test" => "Complete Blood Count", "date" => "2025-08-18", "status" => "Completed"],
    ["id" => 2, "patient" => "Priya Verma", "test" => "Liver Function Test", "date" => "2025-08-19", "status" => "Pending"],
    ["id" => 3, "patient" => "Amit Patel", "test" => "Urine Routine", "date" => "2025-08-19", "status" => "Completed"],
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
                    <h1>Entry List</h1>
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
                                <th>Patient</th>
                                <th>Test</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td><?= htmlspecialchars($entry['id']) ?></td>
                                <td><?= htmlspecialchars($entry['patient']) ?></td>
                                <td><?= htmlspecialchars($entry['test']) ?></td>
                                <td><?= htmlspecialchars($entry['date']) ?></td>
                                <td><?= htmlspecialchars($entry['status']) ?></td>
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
