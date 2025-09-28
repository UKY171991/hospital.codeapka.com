<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Owner Details</h1></div>
                <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Owner Details</li></ol></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Owners</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" id="addOwnerBtn"><i class="fas fa-plus"></i> Add Owner</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="ownersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>WhatsApp</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Link</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Owner Modal -->
<div class="modal fade" id="ownerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="ownerModalLabel">Add Owner</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <form id="ownerForm">
          <input type="hidden" name="id" id="ownerId">
          <div class="form-row">
            <div class="form-group col-md-6"><label for="ownerName">Name</label><input class="form-control" name="name" id="ownerName" required></div>
            <div class="form-group col-md-6"><label for="ownerEmail">Email</label><input class="form-control" name="email" id="ownerEmail" type="email"></div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6"><label for="ownerPhone">Phone</label><input class="form-control" name="phone" id="ownerPhone" type="text"></div>
            <div class="form-group col-md-6"><label for="ownerWhatsapp">WhatsApp</label><input class="form-control" name="whatsapp" id="ownerWhatsapp" type="text"></div>
          </div>
          <div class="form-group"><label for="ownerLink">Link (URL)</label><input class="form-control" name="link" id="ownerLink" type="url" placeholder="https://example.com"></div>
          <div class="form-group"><label for="ownerAddress">Address</label><textarea class="form-control" name="address" id="ownerAddress" rows="3"></textarea></div>
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button id="saveOwnerBtn" type="submit" form="ownerForm" class="btn btn-primary">Save Owner</button></div>
    </div>
  </div>
</div>

<?php // Include a dedicated, read-only view modal for owners
require_once __DIR__ . '/inc/owner_view_modal.php'; ?>

<?php require_once 'inc/footer.php'; ?>
