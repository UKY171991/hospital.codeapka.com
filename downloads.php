<?php $page = 'downloads'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Downloads — Advanced Pathology & Hospital Management System</title>
  <meta name="description" content="Download the latest software releases and updates for the Hospital Management System.">
  <meta name="keywords" content="hospital management download, healthcare software download, medical system updates, pathology lab software, hospital administration software download">
  
  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css?v=2.1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

  <style>
    .downloads-section {
      padding: 120px 0 80px;
      min-height: 80vh;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    .card-downloads {
      border: none;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      overflow: hidden;
    }
    .card-header-custom {
      background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
      padding: 24px;
      color: white;
      border-bottom: none;
    }
    .table thead th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #1e293b;
      border-bottom: 2px solid #e2e8f0;
    }
    .btn-download {
      background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
      color: white;
      border: none;
      padding: 6px 16px;
      border-radius: 8px;
      font-size: 0.875rem;
      transition: all 0.3s ease;
    }
    .btn-download:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
      color: white;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background: #4f46e5 !important;
      border-color: #4f46e5 !important;
      color: white !important;
      border-radius: 6px;
    }
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
      border-radius: 8px;
      border: 1px solid #e2e8f0;
      padding: 6px 12px;
    }
    .page-header {
      text-align: center;
      margin-bottom: 40px;
    }
    .page-title {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 16px;
    }
    .page-subtitle {
      color: #64748b;
      font-size: 1.1rem;
    }
  </style>
</head>
<body>
  <?php include_once __DIR__ . '/inc/header.php'; ?>

  <main>
    <section class="downloads-section">
      <div class="container">
        
        <div class="page-header animate-in">
          <h1 class="page-title">Resource Downloads</h1>
          <p class="page-subtitle">Access the latest software, updates, and documentation</p>
        </div>

        <div class="card card-downloads animate-in" style="animation-delay: 0.2s;">
          <div class="card-header-custom">
            <div class="d-flex align-items-center">
              <span class="fs-4 me-2">📥</span>
              <h4 class="mb-0">Available Files</h4>
            </div>
          </div>
          <div class="card-body p-4">
            <div class="table-responsive">
              <table id="downloadsTable" class="table table-hover w-100">
                <thead>
                  <tr>
                    <th width="5%">#</th>
                    <th width="40%">File Name</th>
                    <th width="15%">Size</th>
                    <th width="25%">Upload Date</th>
                    <th width="15%">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Populated via AJAX -->
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </section>
  </main>

  <?php include_once __DIR__ . '/inc/footer.php'; ?>
  
  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

  <script>
    $(document).ready(function() {
      $('#downloadsTable').DataTable({
        ajax: {
          url: 'umakant/ajax/upload_file.php?action=list',
          dataSrc: function(json) {
            return json.success ? json.data : [];
          }
        },
        columns: [
          { 
            data: null,
            render: function(data, type, row, meta) {
              return meta.row + 1;
            }
          },
          { 
            data: 'original_name',
            render: function(data, type, row) {
              const icon = getFileIcon(row.file_name);
              const path = row.relative_path ? 'umakant/' + row.relative_path : '#';
              return `<div class="d-flex align-items-center">
                        <span class="me-2">${icon}</span>
                        <a href="${path}" target="_blank" class="text-decoration-none fw-medium text-dark">${data}</a>
                      </div>`;
            }
          },
          { 
            data: 'file_size',
            render: function(data) {
              if (!data) return '';
              const kb = data / 1024;
              if (kb > 1024) return (kb / 1024).toFixed(2) + ' MB';
              return kb.toFixed(0) + ' KB';
            }
          },
          { 
            data: 'created_at',
            render: function(data) {
              if (!data) return '-';
              return new Date(data).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
              });
            }
          },
          {
            data: null,
            orderable: false,
            render: function(data, type, row) {
              const path = row.relative_path ? 'umakant/' + row.relative_path : '#';
              return `<a href="${path}" class="btn btn-download btn-sm" download target="_blank">
                        Download
                      </a>`;
            }
          }
        ],
        order: [[3, 'desc']], // Sort by date descending
        language: {
          emptyTable: "No downloads available at the moment."
        },
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>'
      });
    });

    function getFileIcon(filename) {
      if (!filename) return '📄';
      const ext = filename.split('.').pop().toLowerCase();
      switch(ext) {
        case 'zip': return '📦';
        case 'pdf': return '📕';
        case 'exe': return '💿';
        case 'doc': case 'docx': return '📘';
        case 'xls': case 'xlsx': return '📗';
        case 'jpg': case 'png': case 'jpeg': return '🖼️';
        default: return '📄';
      }
    }
  </script>
</body>
</html>
