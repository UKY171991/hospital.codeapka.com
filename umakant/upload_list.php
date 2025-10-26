<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6"><h1>Uploads</h1></div>
      </div>
    </div>
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-4">
          <div class="card" id="uploadCard">
            <div class="card-header bg-primary text-white">
              <h3 class="card-title mb-0"><i class="fas fa-file-upload mr-2"></i>Upload Files</h3>
            </div>
            <div class="card-body">
              <div id="uploadMessage" class="mb-3"></div>
              <form id="uploadForm" onsubmit="return false;" autocomplete="off">
                <div class="form-group">
                  <label for="file_input">Choose file</label>
                  <div class="upload-area" id="uploadArea">
                    <div class="upload-area__icon mb-2"><i class="fas fa-cloud-upload-alt fa-2x"></i></div>
                    <p class="mb-1">Drag &amp; drop any file here</p>
                    <p class="text-muted">or click below</p>
                    <input type="file" class="form-control-file" id="file_input" name="file" required>
                  </div>
                </div>
                <div class="form-group">
                  <div id="uploadProgressWrap" class="progress" style="height:20px; display:none;">
                    <div id="uploadProgress" class="progress-bar" role="progressbar" style="width:0%">0%</div>
                  </div>
                  <small id="uploadProgressText" class="form-text text-muted" style="display:none;">&nbsp;</small>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                  <button id="startUpload" type="button" class="btn btn-primary"><i class="fas fa-upload mr-1"></i>Upload</button>
                  <button id="cancelUpload" type="button" class="btn btn-outline-danger d-none"><i class="fas fa-stop mr-1"></i>Cancel</button>
                </div>
              </form>
            </div>
            <div class="card-footer text-muted">
              <small>All file types allowed • Max size 100MB</small>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header bg-info text-white">
              <h3 class="card-title mb-0"><i class="fas fa-folder-open mr-2"></i>Uploaded Files</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="uploadsTable" style="width:100%">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>File</th>
                      <th>Size</th>
                      <th>Uploaded</th>
                      <th>Uploaded By</th>
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
    </div>
  </section>

  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteLabel">Confirm Delete</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this uploaded file? This cannot be undone.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
      </div>
    </div>
  </div>

</div>

<link rel="stylesheet" href="assets/css/upload.css">

<?php require_once 'inc/footer.php'; ?>
