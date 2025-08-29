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
                            <?php if ($uploadError): ?>
                                <div class="alert alert-danger"><?php echo $uploadError; ?></div>
                            <?php elseif ($uploadSuccess): ?>
                                <div class="alert alert-success"><?php echo $uploadSuccess; ?></div>
                            <?php endif; ?>
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="zip_file">Select ZIP file to upload</label>
                                    <input type="file" class="form-control-file" id="zip_file" name="zip_file" accept=".zip" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'inc/footer.php'; ?>
