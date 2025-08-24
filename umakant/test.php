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
                    <h1>Tests</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Tests</li>
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
                            <h3 class="card-title">Test Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#testModal" onclick="openAddTestModal()">
                                    <i class="fas fa-plus"></i> Add Test
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
                            <table id="testsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Normal Range</th>
                                        <th>Unit</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    require_once 'inc/connection.php';
                                    
                                    $stmt = $conn->prepare("SELECT t.id, tc.name as category_name, t.name, t.description, t.price, t.normal_range, t.unit 
                                            FROM tests t 
                                            LEFT JOIN categories tc ON t.category_id = tc.id 
                                            ORDER BY t.id DESC");
                                    $stmt->execute();
                                    $tests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($tests as $row) {
                                        echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['normal_range']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
                                        echo "<td>";
                                        echo "<a href='#' class='btn btn-info btn-sm view-test' data-id='" . $row['id'] . "' title='View'><i class='fas fa-eye'></i></a> ";
                                        echo "<a href='#' class='btn btn-warning btn-sm edit-test' data-id='" . $row['id'] . "' title='Edit'><i class='fas fa-edit'></i></a> ";
                                        echo "<a href='#' class='btn btn-danger btn-sm delete-test' data-id='" . $row['id'] . "' title='Delete'><i class='fas fa-trash'></i></a>";
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

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="testModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="testModalLabel">Add Test</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="testForm">
                    <input type="hidden" id="testId" name="id">
                    <div class="form-group">
                        <label for="testCategoryId">Category *</label>
                        <select class="form-control" id="testCategoryId" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php
                            // Get categories for dropdown
                            $stmt = $conn->prepare("SELECT id, name FROM categories ORDER BY name");
                            $stmt->execute();
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category) {
                                echo "<option value='" . $category['id'] . "'>" . htmlspecialchars($category['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="testName">Name *</label>
                        <input type="text" class="form-control" id="testName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="testDescription">Description</label>
                        <textarea class="form-control" id="testDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="testPrice">Price *</label>
                        <input type="number" class="form-control" id="testPrice" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="testUnit">Unit</label>
                        <input type="text" class="form-control" id="testUnit" name="unit">
                    </div>
                    <div class="form-group">
                        <label for="testSpecimen">Specimen</label>
                        <input type="text" class="form-control" id="testSpecimen" name="specimen">
                    </div>
                    <div class="form-group">
                        <label for="testDefaultResult">Default Result</label>
                        <input type="text" class="form-control" id="testDefaultResult" name="default_result">
                    </div>
                    <div class="form-group">
                        <label for="testReferenceRange">Reference Range</label>
                        <input type="text" class="form-control" id="testReferenceRange" name="reference_range">
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="testMin">Min Value</label>
                            <input type="number" class="form-control" id="testMin" name="min" step="0.01">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="testMax">Max Value</label>
                            <input type="number" class="form-control" id="testMax" name="max" step="0.01">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="testSubHeading">Sub Heading</label>
                        <select class="form-control" id="testSubHeading" name="sub_heading">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="testCode">Test Code</label>
                        <input type="text" class="form-control" id="testCode" name="test_code">
                    </div>
                    <div class="form-group">
                        <label for="testMethod">Method</label>
                        <input type="text" class="form-control" id="testMethod" name="method">
                    </div>
                    <div class="form-group">
                        <label for="testPrintNewPage">Print on New Page</label>
                        <select class="form-control" id="testPrintNewPage" name="print_new_page">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="testShortcut">Shortcut</label>
                        <input type="text" class="form-control" id="testShortcut" name="shortcut">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTestBtn">Save Test</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>