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
                                    <div class="progress" style="height:20px; display:none;">
                                        <div id="uploadProgress" class="progress-bar" role="progressbar" style="width:0%">0%</div>
                                    </div>
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

<?php require_once 'inc/footer.php'; ?>
<script>
document.getElementById('startUpload').addEventListener('click', function(){
    var input = document.getElementById('file_input');
    var msg = document.getElementById('uploadMessage');
    if(!input.files || !input.files.length){
        msg.innerHTML = '<div class="alert alert-danger">Please select a file.</div>';
        return;
    }
    var file = input.files[0];
    var allowed = ['zip','exe'];
    var ext = file.name.split('.').pop().toLowerCase();
    if(allowed.indexOf(ext) === -1){
        msg.innerHTML = '<div class="alert alert-danger">Only ZIP or EXE files allowed.</div>';
        return;
    }

    var form = new FormData();
    form.append('file', file);
    form.append('action','upload');

    var xhr = new XMLHttpRequest();
    xhr.open('POST','ajax/upload_file.php',true);

    var progressWrap = document.querySelector('.progress');
    var bar = document.getElementById('uploadProgress');
    var startBtn = document.getElementById('startUpload');
    // prepare UI
    msg.innerHTML = '';
    startBtn.disabled = true;
    progressWrap.style.display = 'block';
    bar.classList.add('progress-bar-striped','progress-bar-animated');
    bar.style.width = '100%';
    bar.textContent = 'Uploading...';

    xhr.upload.addEventListener('progress', function(e){
        if(e.lengthComputable){
            var pct = Math.round((e.loaded / e.total) * 100);
            // switch to determinate mode
            bar.classList.remove('progress-bar-animated');
            bar.style.width = pct + '%';
            bar.textContent = pct + '%';
        } else {
            // indeterminate: keep animated full-width bar and message
            bar.classList.add('progress-bar-animated');
            bar.style.width = '100%';
            bar.textContent = 'Uploading...';
        }
    });

    xhr.addEventListener('load', function(){
        // request finished (may be success or error)
        try{
            var res = JSON.parse(xhr.responseText || '{}');
            if(res.success){
                msg.innerHTML = '<div class="alert alert-success">Upload successful: <a href="'+(res.relative_path||'')+'" target="_blank">'+(res.original_name||res.file_name||'file')+'</a></div>';
            } else {
                msg.innerHTML = '<div class="alert alert-danger">Upload failed: '+(res.message||'Server error')+'</div>';
            }
        }catch(e){
            msg.innerHTML = '<div class="alert alert-danger">Unexpected server response</div>';
        }
        // reset UI
        bar.classList.remove('progress-bar-animated','progress-bar-striped');
        bar.style.width = '0%'; bar.textContent = '0%';
        progressWrap.style.display = 'none';
        startBtn.disabled = false;
        // clear file input
        input.value = '';
    });

    xhr.addEventListener('error', function(){
        msg.innerHTML = '<div class="alert alert-danger">Upload failed due to a network error.</div>';
        bar.classList.remove('progress-bar-animated','progress-bar-striped');
        bar.style.width = '0%'; bar.textContent = '0%';
        progressWrap.style.display = 'none';
        startBtn.disabled = false;
    });

    xhr.addEventListener('abort', function(){
        msg.innerHTML = '<div class="alert alert-warning">Upload canceled.</div>';
        bar.classList.remove('progress-bar-animated','progress-bar-striped');
        bar.style.width = '0%'; bar.textContent = '0%';
        progressWrap.style.display = 'none';
        startBtn.disabled = false;
    });

    xhr.send(form);
});
</script>
