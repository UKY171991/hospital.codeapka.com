<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user-plus mr-2"></i>Add Client</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="client_list.php">Clients</a></li>
                        <li class="breadcrumb-item active">Add Client</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-user-plus mr-2"></i>
                                Client Information
                            </h3>
                        </div>
                        <form id="clientForm">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="clientName">Client Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="clientName" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="clientEmail">Email</label>
                                            <input type="email" class="form-control" id="clientEmail">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="clientPhone">Phone <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control" id="clientPhone" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="clientCompany">Company</label>
                                            <input type="text" class="form-control" id="clientCompany">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="clientAddress">Address</label>
                                    <textarea class="form-control" id="clientAddress" rows="3"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="clientCity">City</label>
                                            <input type="text" class="form-control" id="clientCity">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="clientState">State</label>
                                            <input type="text" class="form-control" id="clientState">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="clientZip">ZIP Code</label>
                                            <input type="text" class="form-control" id="clientZip">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="clientNotes">Notes</label>
                                    <textarea class="form-control" id="clientNotes" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save Client
                                </button>
                                <a href="client_list.php" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#clientForm').on('submit', function(e) {
        e.preventDefault();
        saveClient();
    });
});

function saveClient() {
    const formData = {
        action: 'add_client',
        name: $('#clientName').val(),
        email: $('#clientEmail').val(),
        phone: $('#clientPhone').val(),
        company: $('#clientCompany').val(),
        address: $('#clientAddress').val(),
        city: $('#clientCity').val(),
        state: $('#clientState').val(),
        zip: $('#clientZip').val(),
        notes: $('#clientNotes').val()
    };

    $.ajax({
        url: 'ajax/client_api.php',
        type: 'POST',
        data: formData,
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Client added successfully');
                setTimeout(function() {
                    window.location.href = 'client_list.php';
                }, 1000);
            } else {
                toastr.error(response.message || 'Failed to add client');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving client:', error);
            toastr.error('An error occurred while saving client');
        }
    });
}
</script>

<?php require_once 'inc/footer.php'; ?>
