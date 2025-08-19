<?php require_once 'inc/auth.php'; ?>
<?php include 'inc/header.php'; ?>
<?php include 'inc/navbar.php'; ?>
<?php include 'inc/sidebar.php'; ?>
?>
<div class="content-wrapper">
        <section class="content-header">
                <div class="container-fluid">
                        <div class="row mb-2">
                                <div class="col-sm-6">
                                        <h1>Test List</h1>
                                </div>
                                <div class="col-sm-6 text-right">
                                        <button class="btn btn-primary" id="addTestBtn"><i class="fas fa-plus"></i> Add Test</button>
                                </div>
                        </div>
                </div>
        </section>
        <section class="content">
                <div class="container-fluid">
                        <div class="card">
                                <div class="card-body">
                                        <table class="table table-bordered table-hover" id="testTable">
                                                <thead class="thead-light">
                                                        <tr>
                                                                <th>ID</th>
                                                                <th>Name</th>
                                                                <th>Category</th>
                                                                <th>Price (₹)</th>
                                                                <th>Actions</th>
                                                        </tr>
                                                </thead>
                                                <tbody>
                                                <!-- Test rows will be loaded here by AJAX -->
                                                </tbody>
                                        </table>
                                </div>
                        </div>
                </div>
        </section>
</div>

<!-- Add/Edit Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1" role="dialog" aria-labelledby="testModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="testForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="testModalLabel">Add Test</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="testId">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                                        <div class="form-group">
                                                <label>Category</label>
                                                <select class="form-control" name="category" id="category" required>
                                                        <option value="">Select Category</option>
                                                        <?php
                                                        require_once 'inc/connection.php';
                                                        $catStmt = $pdo->query('SELECT id, name FROM test_categories ORDER BY name');
                                                        while ($cat = $catStmt->fetch()) {
                                                                echo '<option value="' . htmlspecialchars($cat['id']) . '">' . htmlspecialchars($cat['name']) . '</option>';
                                                        }
                                                        ?>
                                                </select>
                                        </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" id="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" id="price" required>
                    </div>
                    <div class="form-group">
                        <label>Sample Type</label>
                        <input type="text" class="form-control" name="sample_type" id="sample_type">
                    </div>
                    <div class="form-group">
                        <label>Normal Range</label>
                        <input type="text" class="form-control" name="normal_range" id="normal_range">
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <input type="text" class="form-control" name="unit" id="unit">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function loadTests() {
        $.get('ajax/test_ajax.php', {action: 'list'}, function(data) {
                $('#testTable tbody').html(data);
        });
}

$(function() {
        loadTests();

        $('#addTestBtn').click(function() {
                $('#testForm')[0].reset();
                $('#testId').val('');
                $('#testModalLabel').text('Add Test');
                $('#testModal').modal('show');
        });

        $('#testTable').on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                $.get('ajax/test_ajax.php', {action: 'get', id: id}, function(test) {
                        $('#testId').val(test.id);
                        $('#name').val(test.name);
                        $('#category').val(test.category);
                        $('#description').val(test.description);
                        $('#price').val(test.price);
                        $('#sample_type').val(test.sample_type);
                        $('#normal_range').val(test.normal_range);
                        $('#unit').val(test.unit);
                        $('#testModalLabel').text('Edit Test');
                        $('#testModal').modal('show');
                }, 'json');
        });

        $('#testForm').submit(function(e) {
                e.preventDefault();
                $.post('ajax/test_ajax.php', $(this).serialize() + '&action=save', function(resp) {
                        $('#testModal').modal('hide');
                        loadTests();
                });
        });

        $('#testTable').on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this test?')) {
                        var id = $(this).data('id');
                        $.post('ajax/test_ajax.php', {action: 'delete', id: id}, function(resp) {
                                loadTests();
                        });
                }
        });
});
</script>
<?php include 'inc/footer.php'; ?>
