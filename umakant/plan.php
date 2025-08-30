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
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="planModalLabel">Add Plan</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <form id="planForm">
          <input type="hidden" name="id" id="planId">
          <div class="form-group"><label>Title</label><input class="form-control" name="name" id="planName" required></div>
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
          <div class="form-group"><label>Description</label><textarea class="form-control" name="description" id="planDescription"></textarea></div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>QR Code (upload)</label>
              <input type="file" class="form-control" name="qr_code" id="planQr" accept="image/*" />
            </div>
            <div class="form-group col-md-6">
              <label>Existing QR</label>
              <div id="existingQr" class="form-control-plaintext small">(none)</div>
            </div>
          </div>
          <!-- start/end dates removed per UI change -->
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button id="savePlanBtn" class="btn btn-primary">Save Plan</button></div>
    </div>
  </div>
</div>

<!-- Plan View Modal (read-only) -->
<div class="modal fade" id="planViewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">View Plan</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group"><label>Title</label><p id="viewPlanName" class="form-control-plaintext"></p></div>
        <div class="form-row">
          <div class="form-group col-md-4"><label>Price</label><p id="viewPlanPrice" class="form-control-plaintext"></p></div>
          <div class="form-group col-md-4"><label>UPI</label><p id="viewPlanUpi" class="form-control-plaintext"></p></div>
          <div class="form-group col-md-4"><label>Type</label><p id="viewPlanType" class="form-control-plaintext"></p></div>
        </div>
        <div class="form-group"><label>Description</label><p id="viewPlanDescription" class="form-control-plaintext"></p></div>
  <!-- start/end and Equivalent view fields removed -->
        <div class="form-group"><label>Added By</label><p id="viewPlanAddedBy" class="form-control-plaintext"></p></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Close</button></div>
    </div>
  </div>
</div>

<?php require_once 'inc/footer.php'; ?>

