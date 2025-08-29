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

    var progressWrap = document.getElementById('uploadProgressWrap');
    var bar = document.getElementById('uploadProgress');
    var progressText = document.getElementById('uploadProgressText');
    var startBtn = document.getElementById('startUpload');
        // prepare UI
        msg.innerHTML = '';
        startBtn.disabled = true;
        progressWrap.style.display = 'block';
        // start from 0% so percent is meaningful when available
        // ensure visible color and striping
        bar.classList.add('progress-bar-striped','bg-info');
        bar.style.width = '0%';
        bar.textContent = '0%';
        // indeterminate animation fallback (cycles width) until we get lengthComputable
        var indeterminateTimer = null;
        var simTimer = null;
        var simPct = 0;
        var sawDeterminate = false;
        var simStarterTimer = null;
            function startIndeterminate(){
            if(indeterminateTimer) return;
            bar.classList.add('progress-bar-animated');
            indeterminateTimer = setInterval(function(){
                // oscillate between 20% and 80%
                var w = 20 + Math.floor(Math.random() * 60);
                bar.style.width = w + '%';
                }, 600);
                if(progressText) { progressText.style.display = 'block'; progressText.textContent = 'Uploading...'; }
        }
        function stopIndeterminate(){
            if(indeterminateTimer){ clearInterval(indeterminateTimer); indeterminateTimer = null; }
            bar.classList.remove('progress-bar-animated');
                if(progressText) { progressText.style.display = 'none'; }
        }

            function startSimulation(bytes){
                // only start if real progress isn't available
                if(simTimer) return;
                simPct = 0;
                // estimate duration based on size at ~150 KB/s, clamp 2s..60s
                var kb = Math.max(1, Math.round(bytes / 1024));
                var speedKbPerSec = 150; // conservative
                var estMs = Math.min(60000, Math.max(2000, Math.round((kb / speedKbPerSec) * 1000)));
                var stepMs = 500;
                var stepInc = (estMs > 0) ? (80 * stepMs / estMs) : 5; // aim to reach ~80%
                simTimer = setInterval(function(){
                    simPct = Math.min(95, simPct + stepInc + Math.random()*2);
                    bar.style.width = Math.round(simPct) + '%';
                    bar.textContent = Math.round(simPct) + '%';
                    if(progressText) progressText.textContent = Math.round(simPct) + '% (estimating)';
                }, stepMs);
            }

            function stopSimulation(){ if(simTimer){ clearInterval(simTimer); simTimer = null; simPct = 0; } }
        // start the indeterminate animation immediately to give feedback
        startIndeterminate();

    xhr.upload.addEventListener('progress', function(e){
        if(e.lengthComputable){
            sawDeterminate = true;
            stopSimulation();
            stopIndeterminate();
            var pct = Math.round((e.loaded / e.total) * 100);
            // switch to determinate mode
            bar.style.width = pct + '%';
            bar.textContent = pct + '%';
            if(progressText) progressText.textContent = pct + '% (' + Math.round(e.loaded/1024) + ' KB / ' + Math.round(e.total/1024) + ' KB)';
        } else {
            // keep indeterminate animation running, but also start a simulated percent for better UX
            startIndeterminate();
            bar.textContent = 'Uploading...';
            if(!sawDeterminate){ startSimulation(file.size); }
        }
    });

    xhr.addEventListener('load', function(){
        if(simStarterTimer){ clearTimeout(simStarterTimer); simStarterTimer = null; }
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
    stopIndeterminate();
    bar.classList.remove('progress-bar-striped');
    bar.style.width = '0%'; bar.textContent = '0%';
    progressWrap.style.display = 'none';
    startBtn.disabled = false;
    // clear file input
    input.value = '';
    });

    xhr.addEventListener('error', function(){
        if(simStarterTimer){ clearTimeout(simStarterTimer); simStarterTimer = null; }
    msg.innerHTML = '<div class="alert alert-danger">Upload failed due to a network error.</div>';
    stopIndeterminate();
    bar.classList.remove('progress-bar-striped');
    bar.style.width = '0%'; bar.textContent = '0%';
    progressWrap.style.display = 'none';
    startBtn.disabled = false;
    });

    xhr.addEventListener('abort', function(){
        if(simStarterTimer){ clearTimeout(simStarterTimer); simStarterTimer = null; }
    msg.innerHTML = '<div class="alert alert-warning">Upload canceled.</div>';
    stopIndeterminate();
    bar.classList.remove('progress-bar-striped');
    bar.style.width = '0%'; bar.textContent = '0%';
    progressWrap.style.display = 'none';
    startBtn.disabled = false;
    });

    xhr.send(form);
    // If no progress events arrive quickly, start the simulation so percent becomes visible
    simStarterTimer = setTimeout(function(){ if(!sawDeterminate){ startSimulation(file.size); } }, 300);
});
</script>
