<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
$current_user_id = $_SESSION['user_id'] ?? null;
$current_user_role = $_SESSION['role'] ?? 'user';
$current_username = $_SESSION['username'] ?? 'You';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="mb-1">Laboratory Tests</h1>
                    <span class="text-muted">Manage and organize laboratory tests with categories.</span>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Tests</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-flask"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Tests</span>
                            <span class="info-box-number" id="testCount">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-tags"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Categories</span>
                            <span class="info-box-number" id="categoryCount">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-th-large"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Main Categories</span>
                            <span class="info-box-number" id="mainCategoryCount">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-user"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Current User</span>
                            <span class="info-box-number" style="font-size: 16px;"><?php echo htmlspecialchars($current_username); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="fas fa-vials mr-2"></i>Test Directory</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm mr-2" onclick="openAddTestModal()">
                                    <i class="fas fa-plus mr-1"></i> Add Test
                                </button>
                                <button type="button" class="btn btn-default btn-sm" id="refreshTests">
                                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="testsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">#</th>
                                            <th>ID</th>
                                            <th>Test Name</th>
                                            <th>Main Category</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Unit</th>
                                            <th>Specimen</th>
                                            <th>Added By</th>
                                            <th style="width:150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Test Modal -->
<div class="modal fade" id="testModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="testModalTitle"><i class="fas fa-plus-circle mr-2"></i>Add Test</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="testForm">
                    <input type="hidden" id="testId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="testName">Test Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="testName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="testCode">Test Code</label>
                                <input type="text" class="form-control" id="testCode" name="test_code">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="shortcut">Shortcut</label>
                                <input type="text" class="form-control" id="shortcut" name="shortcut">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mainCategory">Main Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="mainCategory" name="main_category_id" required>
                                    <option value="">Select Main Category</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select class="form-control" id="category" name="category_id">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="unit">Unit</label>
                                <input type="text" class="form-control" id="unit" name="unit">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="specimen">Specimen</label>
                                <input type="text" class="form-control" id="specimen" name="specimen">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="method">Method</label>
                                <input type="text" class="form-control" id="method" name="method">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="defaultResult">Default Result</label>
                                <input type="text" class="form-control" id="defaultResult" name="default_result">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="referenceRange">Reference Range</label>
                                <input type="text" class="form-control" id="referenceRange" name="reference_range">
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-3 mb-3"><i class="fas fa-ruler mr-2"></i>Normal Range Values</h6>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="min">Min</label>
                                <input type="text" class="form-control" id="min" name="min">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="max">Max</label>
                                <input type="text" class="form-control" id="max" name="max">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="minMale">Min Male</label>
                                <input type="text" class="form-control" id="minMale" name="min_male">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="maxMale">Max Male</label>
                                <input type="text" class="form-control" id="maxMale" name="max_male">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="minFemale">Min Female</label>
                                <input type="text" class="form-control" id="minFemale" name="min_female">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="maxFemale">Max Female</label>
                                <input type="text" class="form-control" id="maxFemale" name="max_female">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="minChild">Min Child</label>
                                <input type="text" class="form-control" id="minChild" name="min_child">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="maxChild">Max Child</label>
                                <input type="text" class="form-control" id="maxChild" name="max_child">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="subHeading">Sub Heading</label>
                                <input type="text" class="form-control" id="subHeading" name="sub_heading">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="printNewPage">Print New Page</label>
                                <select class="form-control" id="printNewPage" name="print_new_page">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTestBtn">
                    <i class="fas fa-save mr-1"></i>Save Test
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Test Modal -->
<div class="modal fade" id="viewTestModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye mr-2"></i>View Test Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="viewTestContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/common.js"></script>

<script>
const TEST_API = 'patho_api/test.php';
const CURRENT_USER_ID = <?php echo (int)($current_user_id ?? 0); ?>;
const CURRENT_USER_ROLE = <?php echo json_encode($current_user_role); ?>;

function loadTests() {
    $.getJSON(TEST_API, { action: 'simple_list' }, function(resp) {
        if (resp.success) {
            var $table = $('#testsTable');
            if ($.fn.DataTable.isDataTable($table)) {
                $table.DataTable().destroy();
            }

            var t = '';
            resp.data.forEach(function(test, idx) {
                var price = test.price ? '₹' + parseFloat(test.price).toFixed(2) : '-';
                var addedBy = test.added_by_username || test.added_by || '';
                
                t += '<tr>' +
                    '<td class="text-center">' + (idx + 1) + '</td>' +
                    '<td><span class="badge badge-light border">#' + test.id + '</span></td>' +
                    '<td><strong>' + (test.name || '') + '</strong></td>' +
                    '<td>' + (test.main_category_name || '-') + '</td>' +
                    '<td>' + (test.category_name || '-') + '</td>' +
                    '<td>' + price + '</td>' +
                    '<td>' + (test.unit || '-') + '</td>' +
                    '<td>' + (test.specimen || '-') + '</td>' +
                    '<td><span class="text-muted"><i class="fas fa-user mr-1"></i>' + addedBy + '</span></td>' +
                    '<td class="text-nowrap">' +
                        '<button class="btn btn-sm btn-outline-primary mr-1" onclick="viewTest(' + test.id + ')"><i class="fas fa-eye"></i></button>' +
                        '<button class="btn btn-sm btn-outline-info mr-1 edit-test" data-id="' + test.id + '"><i class="fas fa-edit"></i></button>' +
                        '<button class="btn btn-sm btn-outline-danger delete-test" data-id="' + test.id + '"><i class="fas fa-trash"></i></button>' +
                    '</td>' +
                    '</tr>';
            });
            
            $('#testsTable tbody').html(t);
            window.testTable = initDataTable('#testsTable', {
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                order: [[1, 'desc']]
            });
            
            $('#testCount').text(resp.data.length);
        } else {
            toastr.error('Failed to load tests');
        }
    }).fail(function(xhr) {
        toastr.error('Error loading tests');
    });
}

function loadStats() {
    $.getJSON(TEST_API, { action: 'stats' }, function(resp) {
        if (resp.success) {
            $('#testCount').text(resp.data.total || 0);
            $('#categoryCount').text(resp.data.test_categories || 0);
            $('#mainCategoryCount').text(resp.data.main_categories || 0);
        }
    });
}

function loadMainCategories() {
    $.getJSON(TEST_API, { action: 'main_categories' }, function(resp) {
        if (resp.success) {
            var options = '<option value="">Select Main Category</option>';
            resp.data.forEach(function(cat) {
                options += '<option value="' + cat.id + '">' + cat.name + '</option>';
            });
            $('#mainCategory').html(options);
        }
    });
}

function loadCategories(mainCategoryId) {
    if (!mainCategoryId) {
        $('#category').html('<option value="">Select Category</option>');
        return;
    }
    
    $.getJSON(TEST_API, { action: 'categories_by_main', main_category_id: mainCategoryId }, function(resp) {
        if (resp.success) {
            var options = '<option value="">Select Category</option>';
            resp.data.forEach(function(cat) {
                options += '<option value="' + cat.id + '">' + cat.name + '</option>';
            });
            $('#category').html(options);
        }
    });
}

function openAddTestModal() {
    $('#testForm')[0].reset();
    $('#testId').val('');
    $('#testModalTitle').html('<i class="fas fa-plus-circle mr-2"></i>Add Test');
    $('#testModal').modal('show');
}

function viewTest(id) {
    $.getJSON(TEST_API, { action: 'get', id: id }, function(resp) {
        if (resp.success) {
            var d = resp.data;
            var html = '<table class="table table-sm table-borderless">' +
                '<tr><th width="200">ID</th><td>#' + (d.id || '') + '</td></tr>' +
                '<tr><th>Test Name</th><td><strong>' + (d.name || '') + '</strong></td></tr>' +
                '<tr><th>Test Code</th><td>' + (d.test_code || '-') + '</td></tr>' +
                '<tr><th>Main Category</th><td>' + (d.main_category_name || '-') + '</td></tr>' +
                '<tr><th>Category</th><td>' + (d.category_name || '-') + '</td></tr>' +
                '<tr><th>Price</th><td>₹' + (d.price || '0') + '</td></tr>' +
                '<tr><th>Unit</th><td>' + (d.unit || '-') + '</td></tr>' +
                '<tr><th>Specimen</th><td>' + (d.specimen || '-') + '</td></tr>' +
                '<tr><th>Method</th><td>' + (d.method || '-') + '</td></tr>' +
                '<tr><th>Description</th><td>' + (d.description || '-') + '</td></tr>' +
                '<tr><th>Default Result</th><td>' + (d.default_result || '-') + '</td></tr>' +
                '<tr><th>Reference Range</th><td>' + (d.reference_range || '-') + '</td></tr>' +
                '<tr><th>Normal Range</th><td>Min: ' + (d.min || '-') + ', Max: ' + (d.max || '-') + '</td></tr>' +
                '<tr><th>Male Range</th><td>Min: ' + (d.min_male || '-') + ', Max: ' + (d.max_male || '-') + '</td></tr>' +
                '<tr><th>Female Range</th><td>Min: ' + (d.min_female || '-') + ', Max: ' + (d.max_female || '-') + '</td></tr>' +
                '<tr><th>Child Range</th><td>Min: ' + (d.min_child || '-') + ', Max: ' + (d.max_child || '-') + '</td></tr>' +
                '<tr><th>Added By</th><td>' + (d.added_by_username || d.added_by || '-') + '</td></tr>' +
                '</table>';
            $('#viewTestContent').html(html);
            $('#viewTestModal').modal('show');
        } else {
            toastr.error('Test not found');
        }
    }).fail(function() {
        toastr.error('Error loading test details');
    });
}

$(function() {
    loadTests();
    loadStats();
    loadMainCategories();

    $('#mainCategory').change(function() {
        loadCategories($(this).val());
    });

    $('#refreshTests').click(function() {
        loadTests();
        loadStats();
    });

    $('#saveTestBtn').click(function() {
        var mainCategoryId = $('#mainCategory').val();
        if (!mainCategoryId) {
            toastr.error('Please select a Main Category');
            $('#mainCategory').focus();
            return;
        }

        var data = $('#testForm').serialize() + '&action=save';
        $.post(TEST_API, data, function(resp) {
            if (resp.success) {
                toastr.success(resp.message || 'Test saved successfully');
                $('#testModal').modal('hide');
                loadTests();
                loadStats();
            } else {
                toastr.error(resp.message || 'Failed to save test');
            }
        }, 'json').fail(function(xhr) {
            var msg = 'Server error';
            try {
                var j = JSON.parse(xhr.responseText || '{}');
                if (j.message) msg = j.message;
            } catch (e) {}
            toastr.error(msg);
        });
    });

    $(document).on('click', '.edit-test', function() {
        var id = $(this).data('id');
        $.getJSON(TEST_API, { action: 'get', id: id }, function(resp) {
            if (resp.success) {
                var d = resp.data;
                $('#testId').val(d.id);
                $('#testName').val(d.name);
                $('#testCode').val(d.test_code);
                $('#shortcut').val(d.shortcut);
                $('#mainCategory').val(d.main_category_id);
                
                loadCategories(d.main_category_id);
                setTimeout(function() {
                    $('#category').val(d.category_id);
                }, 300);
                
                $('#price').val(d.price);
                $('#unit').val(d.unit);
                $('#specimen').val(d.specimen);
                $('#method').val(d.method);
                $('#description').val(d.description);
                $('#defaultResult').val(d.default_result);
                $('#referenceRange').val(d.reference_range);
                $('#min').val(d.min);
                $('#max').val(d.max);
                $('#minMale').val(d.min_male);
                $('#maxMale').val(d.max_male);
                $('#minFemale').val(d.min_female);
                $('#maxFemale').val(d.max_female);
                $('#minChild').val(d.min_child);
                $('#maxChild').val(d.max_child);
                $('#subHeading').val(d.sub_heading);
                $('#printNewPage').val(d.print_new_page || '0');
                
                $('#testModalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Test');
                $('#testModal').modal('show');
            } else {
                toastr.error('Test not found');
            }
        }).fail(function() {
            toastr.error('Error loading test');
        });
    });

    $(document).on('click', '.delete-test', function() {
        if (!confirm('Are you sure you want to delete this test?')) return;
        
        var id = $(this).data('id');
        $.post(TEST_API, { action: 'delete', id: id }, function(resp) {
            if (resp.success) {
                toastr.success(resp.message || 'Test deleted successfully');
                loadTests();
                loadStats();
            } else {
                toastr.error(resp.message || 'Failed to delete test');
            }
        }, 'json').fail(function(xhr) {
            var msg = 'Server error';
            try {
                var j = JSON.parse(xhr.responseText || '{}');
                if (j.message) msg = j.message;
            } catch (e) {}
            toastr.error(msg);
        });
    });
});
</script>

<?php require_once 'inc/footer.php'; ?>
