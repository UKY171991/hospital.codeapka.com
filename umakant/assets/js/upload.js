/**
 * File Upload JavaScript
 * Handles file upload with progress tracking and validation
 */

// Global variables for upload management
let uploadXHR = null;
let indeterminateTimer = null;
let simTimer = null;
let simPct = 0;
let sawDeterminate = false;
let simStarterTimer = null;

// Initialize upload functionality when document is ready
$(document).ready(function() {
    initializeUploadEvents();
});

/**
 * Initialize all upload-related event listeners
 */
function initializeUploadEvents() {
    const startUploadBtn = document.getElementById('startUpload');
    if (startUploadBtn) {
        startUploadBtn.addEventListener('click', handleUploadClick);
    }

    // Initialize drag and drop if upload area exists
    initializeDragAndDrop();
}

/**
 * Handle upload button click
 */
function handleUploadClick() {
    const input = document.getElementById('file_input');
    const msg = document.getElementById('uploadMessage');
    
    // Validate file selection
    if (!input.files || !input.files.length) {
        showMessage('Please select a file.', 'danger');
        return;
    }
    
    const file = input.files[0];
    
    // Validate file type
    if (!validateFileType(file)) {
        showMessage('Invalid file. Please avoid uploading executable files.', 'danger');
        return;
    }
    
    // Start upload
    startFileUpload(file);
}

/**
 * Validate file type
 */
function validateFileType(file) {
    // Allow all file types - only check for basic security
    const filename = file.name;
    
    // Basic filename validation
    if (!filename || filename.length === 0) {
        return false;
    }
    
    // Check for potentially dangerous filenames
    const dangerousPatterns = ['.php', '.asp', '.jsp', '.cgi'];
    const lowerName = filename.toLowerCase();
    
    for (let pattern of dangerousPatterns) {
        if (lowerName.includes(pattern)) {
            return false;
        }
    }
    
    return true;
}

/**
 * Start the file upload process
 */
function startFileUpload(file) {
    const form = new FormData();
    form.append('file', file);
    form.append('action', 'upload');
    
    // Create new XMLHttpRequest
    uploadXHR = new XMLHttpRequest();
    uploadXHR.open('POST', 'ajax/upload_file.php', true);
    
    // Setup UI for upload
    setupUploadUI();
    
    // Setup event listeners
    setupUploadEventListeners(file);
    
    // Start upload
    uploadXHR.send(form);
    
    // Start progress feedback
    startProgressFeedback(file);
}

/**
 * Setup UI for upload process
 */
function setupUploadUI() {
    const msg = document.getElementById('uploadMessage');
    const progressWrap = document.getElementById('uploadProgressWrap');
    const bar = document.getElementById('uploadProgress');
    const startBtn = document.getElementById('startUpload');
    
    // Clear previous messages
    if (msg) msg.innerHTML = '';
    
    // Disable upload button
    if (startBtn) startBtn.disabled = true;
    
    // Show progress bar
    if (progressWrap) progressWrap.style.display = 'block';
    
    // Setup progress bar
    if (bar) {
        bar.classList.add('progress-bar-striped', 'bg-info');
        bar.style.width = '0%';
        bar.textContent = '0%';
    }
    
    // Reset state variables
    sawDeterminate = false;
    simPct = 0;
}

/**
 * Setup upload event listeners
 */
function setupUploadEventListeners(file) {
    // Upload progress
    uploadXHR.upload.addEventListener('progress', function(e) {
        handleUploadProgress(e, file);
    });
    
    // Upload complete
    uploadXHR.addEventListener('load', function() {
        handleUploadComplete();
    });
    
    // Upload error
    uploadXHR.addEventListener('error', function() {
        handleUploadError();
    });
    
    // Upload abort
    uploadXHR.addEventListener('abort', function() {
        handleUploadAbort();
    });
}

/**
 * Handle upload progress events
 */
function handleUploadProgress(e, file) {
    const bar = document.getElementById('uploadProgress');
    const progressText = document.getElementById('uploadProgressText');
    
    if (e.lengthComputable) {
        // Real progress available
        sawDeterminate = true;
        stopSimulation();
        stopIndeterminate();
        
        const pct = Math.round((e.loaded / e.total) * 100);
        
        if (bar) {
            bar.style.width = pct + '%';
            bar.textContent = pct + '%';
        }
        
        if (progressText) {
            const loadedKB = Math.round(e.loaded / 1024);
            const totalKB = Math.round(e.total / 1024);
            progressText.textContent = `${pct}% (${loadedKB} KB / ${totalKB} KB)`;
        }
    } else {
        // No real progress, use indeterminate
        startIndeterminate();
        if (bar) bar.textContent = 'Uploading...';
        if (!sawDeterminate) {
            startSimulation(file.size);
        }
    }
}

/**
 * Handle upload completion
 */
function handleUploadComplete() {
    cleanupTimers();
    
    try {
        const response = JSON.parse(uploadXHR.responseText || '{}');
        if (response.success) {
            const fileName = response.original_name || response.file_name || 'file';
            const filePath = response.relative_path || '';
            showMessage(`Upload successful: <a href="${filePath}" target="_blank">${fileName}</a>`, 'success');
        } else {
            showMessage(`Upload failed: ${response.message || 'Server error'}`, 'danger');
        }
    } catch (e) {
        showMessage('Unexpected server response', 'danger');
    }
    
    resetUploadUI();
}

/**
 * Handle upload error
 */
function handleUploadError() {
    cleanupTimers();
    showMessage('Upload failed due to a network error.', 'danger');
    resetUploadUI();
}

/**
 * Handle upload abort
 */
function handleUploadAbort() {
    cleanupTimers();
    showMessage('Upload canceled.', 'warning');
    resetUploadUI();
}

/**
 * Start indeterminate progress animation
 */
function startIndeterminate() {
    if (indeterminateTimer) return;
    
    const bar = document.getElementById('uploadProgress');
    const progressText = document.getElementById('uploadProgressText');
    
    if (bar) bar.classList.add('progress-bar-animated');
    
    indeterminateTimer = setInterval(function() {
        const width = 20 + Math.floor(Math.random() * 60);
        if (bar) bar.style.width = width + '%';
    }, 600);
    
    if (progressText) {
        progressText.style.display = 'block';
        progressText.textContent = 'Uploading...';
    }
}

/**
 * Stop indeterminate progress animation
 */
function stopIndeterminate() {
    if (indeterminateTimer) {
        clearInterval(indeterminateTimer);
        indeterminateTimer = null;
    }
    
    const bar = document.getElementById('uploadProgress');
    const progressText = document.getElementById('uploadProgressText');
    
    if (bar) bar.classList.remove('progress-bar-animated');
    if (progressText) progressText.style.display = 'none';
}

/**
 * Start simulated progress
 */
function startSimulation(bytes) {
    if (simTimer) return;
    
    simPct = 0;
    
    // Estimate duration based on file size
    const kb = Math.max(1, Math.round(bytes / 1024));
    const speedKbPerSec = 150; // Conservative estimate
    const estMs = Math.min(60000, Math.max(2000, Math.round((kb / speedKbPerSec) * 1000)));
    const stepMs = 500;
    const stepInc = (estMs > 0) ? (80 * stepMs / estMs) : 5;
    
    simTimer = setInterval(function() {
        simPct = Math.min(95, simPct + stepInc + Math.random() * 2);
        const bar = document.getElementById('uploadProgress');
        const progressText = document.getElementById('uploadProgressText');
        
        if (bar) {
            bar.style.width = Math.round(simPct) + '%';
            bar.textContent = Math.round(simPct) + '%';
        }
        
        if (progressText) {
            progressText.textContent = Math.round(simPct) + '% (estimating)';
        }
    }, stepMs);
}

/**
 * Stop simulated progress
 */
function stopSimulation() {
    if (simTimer) {
        clearInterval(simTimer);
        simTimer = null;
        simPct = 0;
    }
}

/**
 * Start progress feedback
 */
function startProgressFeedback(file) {
    // Start indeterminate animation immediately
    startIndeterminate();
    
    // Start simulation if no real progress events arrive quickly
    simStarterTimer = setTimeout(function() {
        if (!sawDeterminate) {
            startSimulation(file.size);
        }
    }, 300);
}

/**
 * Cleanup all timers
 */
function cleanupTimers() {
    if (simStarterTimer) {
        clearTimeout(simStarterTimer);
        simStarterTimer = null;
    }
    stopIndeterminate();
    stopSimulation();
}

/**
 * Reset upload UI to initial state
 */
function resetUploadUI() {
    const progressWrap = document.getElementById('uploadProgressWrap');
    const bar = document.getElementById('uploadProgress');
    const startBtn = document.getElementById('startUpload');
    const input = document.getElementById('file_input');
    
    stopIndeterminate();
    
    if (bar) {
        bar.classList.remove('progress-bar-striped');
        bar.style.width = '0%';
        bar.textContent = '0%';
    }
    
    if (progressWrap) progressWrap.style.display = 'none';
    if (startBtn) startBtn.disabled = false;
    if (input) input.value = '';
}

/**
 * Show message to user
 */
function showMessage(message, type) {
    const msg = document.getElementById('uploadMessage');
    if (!msg) return;
    
    const alertClass = `alert-${type}`;
    msg.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
}

/**
 * Initialize drag and drop functionality
 */
function initializeDragAndDrop() {
    const uploadArea = document.querySelector('.upload-area');
    const fileInput = document.getElementById('file_input');
    
    if (!uploadArea || !fileInput) return;
    
    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    // Highlight drop area when item is dragged over it
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });
    
    // Handle dropped files
    uploadArea.addEventListener('drop', handleDrop, false);
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function highlight(e) {
        uploadArea.classList.add('dragover');
    }
    
    function unhighlight(e) {
        uploadArea.classList.remove('dragover');
    }
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length > 0) {
            fileInput.files = files;
            // Trigger change event if needed
            const event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    }
}

/**
 * Cancel current upload
 */
function cancelUpload() {
    if (uploadXHR) {
        uploadXHR.abort();
    }
}

// Make functions available globally
window.handleUploadClick = handleUploadClick;
window.cancelUpload = cancelUpload;
