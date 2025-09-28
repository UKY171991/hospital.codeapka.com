(function ($) {
  'use strict';

  const API_URL = 'ajax/upload_file.php';
  const $tableSelector = $('#uploadsTable');
  let uploadsTable = null;
  let uploadXHR = null;
  let simTimer = null;
  let simPercent = 0;
  let sawDeterminate = false;
  let simStarterTimer = null;

  function initPage() {
    if (!$tableSelector.length) return;

    initDataTable();
    bindUploadEvents();
    bindDeleteEvents();
  }

  function initDataTable() {
    if (!$.fn.DataTable) {
      console.error('DataTables plugin missing for uploads table');
      return;
    }

    uploadsTable = $tableSelector.DataTable({
      autoWidth: false,
      processing: true,
      ajax: {
        url: API_URL,
        method: 'GET',
        data: { action: 'list' },
        dataSrc: function (json) {
          if (!json || json.success !== true) {
            const message = json && json.message ? json.message : 'Failed to load uploads';
            if (window.toastr) toastr.error(message);
            return [];
          }
          return Array.isArray(json.data) ? json.data : [];
        }
      },
      order: [[3, 'desc']],
      columns: [
        {
          data: null,
          className: 'text-center align-middle',
          orderable: false,
          width: '50px',
          render: function (_data, _type, _row, meta) {
            return meta.row + 1;
          }
        },
        {
          data: null,
          render: function (row) {
            const name = row.original_name || row.file_name || 'download';
            const link = row.relative_path ? sanitizeUrl(row.relative_path) : '';
            const button = link
              ? '<a class="btn btn-sm btn-outline-primary mr-2" href="' + link + '" target="_blank" rel="noopener"><i class="fas fa-download"></i></a>'
              : '';
            return button + '<span class="text-break">' + escapeHtml(name) + '</span>';
          }
        },
        {
          data: 'file_size',
          className: 'text-nowrap',
          render: function (value) {
            const size = Number(value) || 0;
            if (!size) return '—';
            const mb = size / (1024 * 1024);
            return mb >= 1 ? mb.toFixed(2) + ' MB' : (size / 1024).toFixed(1) + ' KB';
          }
        },
        {
          data: 'created_at',
          render: function (value) {
            if (!value) return '—';
            const date = parseUploadDate(value);
            return date ? date : escapeHtml(value);
          }
        },
        {
          data: null,
          render: function (row) {
            return escapeHtml(row.uploaded_by_username || row.uploaded_by || '—');
          }
        },
        {
          data: null,
          orderable: false,
          className: 'text-center',
          render: function (row) {
            const fileName = row.file_name || row.original_name;
            if (!fileName) return '—';
            const safe = escapeAttr(fileName);
            return (
              '<button type="button" class="btn btn-sm btn-danger delete-upload" data-file="' + safe + '"><i class="fas fa-trash"></i></button>'
            );
          }
        }
      ],
      language: {
        emptyTable: 'No uploads yet',
        zeroRecords: 'No matching uploads found',
        search: 'Search uploads:'
      }
    });
  }

  function reloadTable() {
    if (uploadsTable && typeof uploadsTable.ajax.reload === 'function') {
      uploadsTable.ajax.reload(null, false);
    }
  }

  // ----------------- Upload logic -----------------

  function bindUploadEvents() {
    $('#startUpload').on('click', startUploadHandler);
    $('#cancelUpload').on('click', cancelUploadHandler);
    initDragAndDrop();
  }

  function startUploadHandler() {
    const input = document.getElementById('file_input');
    if (!input || !input.files || !input.files.length) {
      showMessage('Please select a file to upload.', 'danger');
      return;
    }

    const file = input.files[0];
    if (!validateFileType(file)) {
      showMessage('Only ZIP or EXE files are allowed.', 'danger');
      return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('action', 'upload');

    uploadXHR = new XMLHttpRequest();
    uploadXHR.open('POST', API_URL, true);

    setupUploadUI();
    attachUploadEvents(uploadXHR, file);
    uploadXHR.send(formData);
    startProgressFeedback(file);
  }

  function cancelUploadHandler() {
    if (uploadXHR) {
      uploadXHR.abort();
    }
  }

  function attachUploadEvents(xhr, file) {
    xhr.upload.addEventListener('progress', function (event) {
      handleUploadProgress(event, file);
    });

    xhr.addEventListener('load', function () {
      completeUpload();
    });

    xhr.addEventListener('error', function () {
      failUpload('Upload failed due to a network error.');
    });

    xhr.addEventListener('abort', function () {
      failUpload('Upload cancelled.');
    });
  }

  function setupUploadUI() {
    sawDeterminate = false;
    simPercent = 0;

    $('#uploadMessage').empty();
    $('#startUpload').prop('disabled', true);
    $('#cancelUpload').removeClass('d-none');
    $('#uploadProgressWrap').show();

    const $bar = $('#uploadProgress');
    $bar.removeClass('bg-success bg-danger').addClass('bg-info progress-bar-striped');
    $bar.css('width', '0%').text('0%');
    $('#uploadProgressText').hide();
  }

  function resetUploadUI() {
    $('#startUpload').prop('disabled', false);
    $('#cancelUpload').addClass('d-none');
    $('#uploadProgressWrap').hide();
    $('#uploadProgress').removeClass('progress-bar-striped progress-bar-animated bg-info').css('width', '0%').text('0%');
    $('#uploadProgressText').hide();
    $('#file_input').val('');
  }

  function handleUploadProgress(event, file) {
    const $bar = $('#uploadProgress');
    const $text = $('#uploadProgressText');

    if (event.lengthComputable) {
      sawDeterminate = true;
      stopSimulation();
      stopIndeterminate();

      const percent = Math.round((event.loaded / event.total) * 100);
      $bar.css('width', percent + '%').text(percent + '%');

      const loadedKB = Math.round(event.loaded / 1024);
      const totalKB = Math.round(event.total / 1024);
      $text.text(percent + '% (' + loadedKB + ' KB / ' + totalKB + ' KB)').show();
    } else {
      startIndeterminate();
      if (!sawDeterminate) startSimulation(file.size);
    }
  }

  function completeUpload() {
    cleanupTimers();

    let response = {};
    try {
      response = JSON.parse(uploadXHR.responseText || '{}');
    } catch (error) {
      failUpload('Unexpected server response.');
      resetUploadUI();
      return;
    }

    if (response.success) {
      showMessage('Upload successful: <a href="' + sanitizeUrl(response.relative_path) + '" target="_blank" rel="noopener">' + escapeHtml(response.original_name || response.file_name || 'download') + '</a>', 'success');
      $('#uploadProgress').removeClass('bg-info').addClass('bg-success').text('100%').css('width', '100%');
      reloadTable();
    } else {
      failUpload(response.message || 'Upload failed.');
    }

    resetUploadUI();
  }

  function failUpload(message) {
    cleanupTimers();
    showMessage(message, 'danger');
    $('#uploadProgress').removeClass('bg-info').addClass('bg-danger');
    resetUploadUI();
  }

  function startIndeterminate() {
    const $bar = $('#uploadProgress');
    const $text = $('#uploadProgressText');
    $bar.addClass('progress-bar-striped progress-bar-animated').text('Uploading...');
    $text.text('Uploading...').show();
  }

  function stopIndeterminate() {
    $('#uploadProgress').removeClass('progress-bar-animated');
  }

  function startSimulation(bytes) {
    if (simTimer) return;
    simPercent = 0;

    const estimatedDuration = Math.min(60000, Math.max(2000, Math.round((Math.max(bytes, 1) / 1024 / 150) * 1000)));
    const stepMs = 400;
    const increment = 80 * stepMs / estimatedDuration;

    simTimer = setInterval(function () {
      simPercent = Math.min(95, simPercent + increment + Math.random() * 2);
      $('#uploadProgress').css('width', Math.round(simPercent) + '%').text(Math.round(simPercent) + '%');
      $('#uploadProgressText').text(Math.round(simPercent) + '% (estimating)');
    }, stepMs);
  }

  function stopSimulation() {
    if (simTimer) {
      clearInterval(simTimer);
      simTimer = null;
      simPercent = 0;
    }
  }

  function startProgressFeedback(file) {
    startIndeterminate();
    simStarterTimer = setTimeout(function () {
      if (!sawDeterminate) startSimulation(file.size);
    }, 300);
  }

  function cleanupTimers() {
    if (simStarterTimer) {
      clearTimeout(simStarterTimer);
      simStarterTimer = null;
    }
    stopSimulation();
    stopIndeterminate();
  }

  function showMessage(message, type) {
    const $container = $('#uploadMessage');
    if (!$container.length) return;
    $container.html('<div class="alert alert-' + type + ' mb-2">' + message + '</div>');
  }

  function validateFileType(file) {
    const allowed = ['zip', 'exe'];
    const ext = file.name.split('.').pop().toLowerCase();
    return allowed.indexOf(ext) !== -1;
  }

  function initDragAndDrop() {
    const uploadArea = document.getElementById('uploadArea');
    const input = document.getElementById('file_input');
    if (!uploadArea || !input) return;

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (eventName) {
      uploadArea.addEventListener(eventName, preventDefaults, false);
      document.body.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(function (eventName) {
      uploadArea.addEventListener(eventName, function () { uploadArea.classList.add('dragover'); });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
      uploadArea.addEventListener(eventName, function () { uploadArea.classList.remove('dragover'); });
    });

    uploadArea.addEventListener('drop', function (event) {
      const files = event.dataTransfer.files;
      if (files && files.length) {
        input.files = files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
  }

  function preventDefaults(event) {
    event.preventDefault();
    event.stopPropagation();
  }

  // ----------------- Delete logic -----------------

  function bindDeleteEvents() {
    $(document).on('click', '.delete-upload', function () {
      const file = $(this).data('file');
      if (!file) {
        if (window.toastr) toastr.error('No file specified');
        return;
      }

      const $modal = $('#confirmDeleteModal');
      $modal.data('target-file', file).modal('show');
    });

    $('#confirmDeleteBtn').on('click', function () {
      const $modal = $('#confirmDeleteModal');
      const file = $modal.data('target-file');
      if (!file) {
        if (window.toastr) toastr.error('No file selected');
        return;
      }

      $(this).prop('disabled', true).text('Deleting...');

      $.ajax({
        url: API_URL,
        method: 'POST',
        dataType: 'json',
        data: { action: 'delete', file: file }
      })
        .done(function (resp) {
          if (resp && resp.success) {
            if (window.toastr) toastr.success('File deleted successfully');
            reloadTable();
          } else {
            const message = resp && resp.message ? resp.message : 'Delete failed';
            if (window.toastr) toastr.error(message);
          }
        })
        .fail(function (xhr) {
          console.error('Delete upload failed', xhr);
          if (window.toastr) toastr.error('Server error while deleting file');
        })
        .always(function () {
          $('#confirmDeleteBtn').prop('disabled', false).text('Delete');
          $('#confirmDeleteModal').modal('hide');
        });
    });
  }

  // ----------------- Helpers -----------------

  function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return String(text)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function escapeAttr(text) {
    return escapeHtml(text).replace(/"/g, '&quot;');
  }

  function sanitizeUrl(url) {
    if (!url) return '';
    try {
      const full = new URL(url, window.location.origin);
      if (!/^https?:$/i.test(full.protocol)) return '';
      return full.href;
    } catch (error) {
      return '';
    }
  }

  function parseUploadDate(value) {
    const date = new Date(value.replace(' ', 'T'));
    if (isNaN(date.getTime())) return null;
    return date.toLocaleString();
  }

  $(document).ready(initPage);
})(jQuery);
