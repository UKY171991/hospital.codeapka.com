<?php require_once 'inc/auth.php'; ?>
<?php
// test.php
// Dummy test data for demonstration
$tests = [
    ["id" => 1, "name" => "Complete Blood Count", "category" => "Hematology", "price" => 350],
    ["id" => 2, "name" => "Liver Function Test", "category" => "Biochemistry", "price" => 600],
    ["id" => 3, "name" => "Urine Routine", "category" => "Urinalysis", "price" => 200],
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
                    <h1>Test List</h1>
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
                                <th>Category</th>
                                <th>Price (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($tests as $test): ?>
                            <tr>
                                <td><?= htmlspecialchars($test['id']) ?></td>
                                <td><?= htmlspecialchars($test['name']) ?></td>
                                <td><?= htmlspecialchars($test['category']) ?></td>
                                <td><?= htmlspecialchars($test['price']) ?></td>
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
