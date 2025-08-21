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
                    <h1>Test Category List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary" id="addCatBtn"><i class="fas fa-plus"></i> Add Category</button>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="catTable">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Added By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Category rows will be loaded here by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal fade" id="catModal" tabindex="-1" role="dialog" aria-labelledby="catModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="catForm">
        <div class="modal-header">
          <h5 class="modal-title" id="catModalLabel">Add Category</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="catId">
          <div class="form-group">
            <label>Name</label>
            <input type="text" class="form-control" name="name" id="name" required>
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" name="description" id="description"></textarea>
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
function loadCats() {
    $.get('ajax/test_category_ajax.php', {action: 'list'}, function(data) {
        $('#catTable tbody').html(data);
    });
}

$(function() {
    loadCats();

    $('#addCatBtn').click(function() {
        $('#catForm')[0].reset();
        $('#catId').val('');
        $('#catModalLabel').text('Add Category');
        $('#catModal').modal('show');
    });

    $('#catTable').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.get('ajax/test_category_ajax.php', {action: 'get', id: id}, function(cat) {
            $('#catId').val(cat.id);
            $('#name').val(cat.name);
            $('#description').val(cat.description);
            $('#catModalLabel').text('Edit Category');
            $('#catModal').modal('show');
        }, 'json');
    });

    $('#catForm').submit(function(e) {
        e.preventDefault();
        $.post('ajax/test_category_ajax.php', $(this).serialize() + '&action=save', function(resp) {
            $('#catModal').modal('hide');
            loadCats();
        });
    });

    $('#catTable').on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this category?')) {
            var id = $(this).data('id');
            $.post('ajax/test_category_ajax.php', {action: 'delete', id: id}, function(resp) {
                loadCats();
            });
        }
    });
});
</script>
<?php include 'inc/footer.php'; ?>
