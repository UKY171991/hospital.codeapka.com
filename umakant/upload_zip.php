<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';

$uploadDir = __DIR__ . '/uploads';
$uploadUrl = 'uploads/';
$uploadError = '';
$uploadSuccess = '';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['zip_file'])) {
    $file = $_FILES['zip_file'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'zip') {
            $uploadError = 'Only ZIP files are allowed.';
        } else {
            $target = $uploadDir . '/' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $target)) {
                $uploadSuccess = 'File uploaded successfully: <a href="' . $uploadUrl . htmlspecialchars(basename($file['name'])) . '" target="_blank">' . htmlspecialchars($file['name']) . '</a>';
            } else {
                $uploadError = 'Failed to move uploaded file.';
            }
        }
    } else {
        $uploadError = 'Upload error: ' . $file['error'];
    }
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Upload ZIP File</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div id="uploadMessage"></div>
                            <form id="uploadForm" method="post" enctype="multipart/form-data" onsubmit="return false;">
                                <div class="form-group">
                                    <label for="file_input">Select ZIP or EXE file to upload</label>
                                    <input type="file" class="form-control-file" id="file_input" name="file" accept=".zip,.exe" required>
                                </div>
                                <div class="form-group">
                                    <div id="uploadProgressWrap" class="progress" style="height:20px; display:none;">
                                        <div id="uploadProgress" class="progress-bar" role="progressbar" style="width:0%">0%</div>
                                    </div>
                                    <small id="uploadProgressText" class="form-text text-muted" style="display:none;">&nbsp;</small>
                                </div>
                                <button id="startUpload" type="button" class="btn btn-primary">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Page specific CSS -->
<link rel="stylesheet" href="assets/css/upload.css">

<?php require_once 'inc/footer.php'; ?>

<!-- Page specific JavaScript -->
<script src="assets/js/upload.js"></script>
