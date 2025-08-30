<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Menu Plan</h1></div>
                <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Menu Plan</li></ol></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Plan Management</h3>
                            <div class="card-tools">
                                <button class="btn btn-primary" id="addPlanBtn"><i class="fas fa-plus"></i> Add Plan</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="plansTable" class="table table-bordered table-striped">
                                <thead>
                  <tr>
                    <th>S.No.</th>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>UPI</th>
                    <th>Type</th>
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

<!-- Plan Form Modal (used for Add/Edit) -->
<div class="modal fade" id="planModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white"><h5 class="modal-title" id="planModalLabel">Add Plan</h5><button type="button" class="close text-white" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <form id="planForm" enctype="multipart/form-data">
          <input type="hidden" name="id" id="planId">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group"><label>Title</label><input class="form-control form-control-lg" name="name" id="planName" required></div>
              <div class="form-row">
                <div class="form-group col-md-4"><label>Price</label><input class="form-control" name="price" id="planPrice" type="number" step="0.01" required></div>
                <div class="form-group col-md-4"><label>UPI</label><input class="form-control" name="upi" id="planUpi" type="text"></div>
                <div class="form-group col-md-4"><label>Type</label>
                  <select class="form-control" name="time_type" id="planType">
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                  </select>
                </div>
              </div>
              <div class="form-group"><label>Description</label><textarea class="form-control" name="description" id="planDescription" rows="4"></textarea></div>
            </div>
            <div class="col-md-4">
              <div class="card shadow-sm">
                <div class="card-body text-center">
                  <label class="d-block">QR Code</label>
                  <div id="qrPreviewWrap" class="mb-2">
                    <img id="qrPreview" src="" alt="QR preview" class="img-fluid rounded border" style="max-height:200px; display:none;" />
                    <div id="existingQr" class="small text-muted">(none)</div>
                  </div>
                  <div class="form-group">
                    <input type="file" class="form-control-file" name="qr_code" id="planQr" accept="image/*" />
                  </div>
                  <small class="text-muted">Accepted: PNG, JPG, WEBP, GIF. Max 2MB.</small>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
        <button id="savePlanBtn" class="btn btn-primary btn-lg">Save Plan</button>
      </div>
    </div>
  </div>
</div>

<!-- Plan View Modal (read-only) -->
<div class="modal fade" id="planViewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Plan Details</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-8">
            <h4 id="viewPlanName" class="mb-2"></h4>
            <p id="viewPlanDescription" class="text-muted"></p>
            <dl class="row">
              <dt class="col-sm-4">Price</dt><dd class="col-sm-8" id="viewPlanPrice"></dd>
              <dt class="col-sm-4">UPI</dt><dd class="col-sm-8" id="viewPlanUpi"></dd>
              <dt class="col-sm-4">Type</dt><dd class="col-sm-8" id="viewPlanType"></dd>
              <dt class="col-sm-4">Added By</dt><dd class="col-sm-8" id="viewPlanAddedBy"></dd>
            </dl>
          </div>
          <div class="col-md-4 text-center">
            <div class="card">
              <div class="card-body">
                <img id="viewQrImg" src="" alt="QR" class="img-fluid mb-2" style="max-height:250px; display:none;" />
                <div id="viewQrNone" class="text-muted small">No QR available</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Close</button></div>
    </div>
  </div>
</div>

<!-- Page local styles -->
<link rel="stylesheet" href="assets/css/plan.css">

<?php require_once 'inc/footer.php'; ?>

