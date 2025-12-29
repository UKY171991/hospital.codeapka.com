<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Followup Templates</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Followup Templates</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Add/Edit Template Form -->
                <div class="col-md-4">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Add New Template</h3>
                        </div>
                        <form id="addTemplateForm">
                            <input type="hidden" id="template_id" name="template_id">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="template_name">Template Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="template_name" name="template_name" placeholder="E.g., Welcome Message" required>
                                </div>
                                <div class="form-group">
                                    <label for="content">Template Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="content" name="content" rows="6" placeholder="Enter template content" required></textarea>
                                    <small class="form-text text-muted">You can use this content in followups.</small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Save Template</button>
                                <button type="button" class="btn btn-default float-right" id="cancelEdit" style="display: none;">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Template List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Template List</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap" id="templatesTable">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Name</th>
                                        <th>Content Preview</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Templates will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right" id="pagination">
                                <!-- Pagination links will be loaded here -->
                            </ul>
                        </div>
                        <div class="overlay" id="loadingOverlay" style="display: none;">
                            <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'inc/footer.php'; ?>

<!-- Summernote -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
let currentPage = 1;
const limit = 10;

$(document).ready(function() {
    // Initialize Summernote
    $('#content').summernote({
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    loadTemplates(currentPage);

    // Handle Form Submission
    $('#addTemplateForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const templateId = $('#template_id').val();
        
        if (templateId) {
            formData.append('action', 'update_template');
            formData.append('id', templateId);
        } else {
            formData.append('action', 'add_template');
        }

        $.ajax({
            url: 'ajax/followup_templates_api.php',
            type: 'POST',
            data: formData,
            processData: false, // Important for FormData with Summernote content potentially? FormData handles it.
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    resetForm();
                    loadTemplates(currentPage);
                } else {
                    toastr.error(response.message || 'Error saving template');
                }
            },
            error: function() {
                toastr.error('Server error occurred');
            }
        });
    });

    // Edit Template
    $(document).on('click', '.edit-template', function() {
        const id = $(this).data('id');
        $.ajax({
            url: 'ajax/followup_templates_api.php',
            type: 'GET',
            data: { action: 'get_template', id: id },
            success: function(response) {
                if (response.success) {
                    const template = response.data;
                    $('#template_id').val(template.id);
                    $('#template_name').val(template.template_name);
                    $('#content').summernote('code', template.content);
                    
                    // Change UI to Edit Mode
                    $('.card-title').text('Edit Template');
                    $('button[type="submit"]').text('Update Template');
                    $('#cancelEdit').show();
                } else {
                    toastr.error(response.message || 'Error fetching template details');
                }
            },
            error: function() {
                toastr.error('Server error occurred');
            }
        });
    });

    // Cancel Edit
    $('#cancelEdit').on('click', function() {
        resetForm();
    });

    // Delete Template
    $(document).on('click', '.delete-template', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this template?')) {
            $.ajax({
                url: 'ajax/followup_templates_api.php',
                type: 'POST',
                data: { action: 'delete_template', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        loadTemplates(currentPage);
                    } else {
                        toastr.error(response.message || 'Error deleting template');
                    }
                },
                error: function() {
                    toastr.error('Server error occurred');
                }
            });
        }
    });
    
    // Pagination Click
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            currentPage = page;
            loadTemplates(page);
        }
    });
});

function resetForm() {
    $('#addTemplateForm')[0].reset();
    $('#template_id').val('');
    $('#content').summernote('reset');
    $('.card-title').text('Add New Template');
    $('button[type="submit"]').text('Save Template');
    $('#cancelEdit').hide();
}

function loadTemplates(page) {
    $('#loadingOverlay').show();
    $.ajax({
        url: 'ajax/followup_templates_api.php',
        type: 'GET',
        data: { action: 'get_templates', page: page },
        success: function(response) {
            $('#loadingOverlay').hide();
            if (response.success) {
                const tbody = $('#templatesTable tbody');
                tbody.empty();
                
                if (response.data.length === 0) {
                    tbody.append('<tr><td colspan="4" class="text-center">No templates found</td></tr>');
                    $('#pagination').empty();
                    return;
                }

                response.data.forEach(function(template, index) {
                    const srNo = (page - 1) * limit + index + 1;
                    
                    // Strip HTML for preview
                    const div = document.createElement("div");
                    div.innerHTML = template.content;
                    let text = div.textContent || div.innerText || "";
                    if (text.length > 50) text = text.substring(0, 50) + '...';
                    
                    const row = `
                        <tr>
                            <td>${srNo}</td>
                            <td>${template.template_name}</td>
                            <td>${text}</td>
                            <td>
                                <button class="btn btn-sm btn-info edit-template" data-id="${template.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-template" data-id="${template.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                
                renderPagination(response.pagination);
            } else {
                toastr.error(response.message || 'Error loading templates');
            }
        },
        error: function() {
            $('#loadingOverlay').hide();
            toastr.error('Server error loading templates');
        }
    });
}

function renderPagination(pagination) {
    const ul = $('#pagination');
    ul.empty();
    
    if (pagination.total_pages <= 1) return;
    
    // Previous
    const prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
    ul.append(`
        <li class="page-item ${prevDisabled}">
            <a class="page-link" href="#" data-page="${pagination.current_page - 1}">&laquo;</a>
        </li>
    `);
    
    // Pages
    for (let i = 1; i <= pagination.total_pages; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        ul.append(`
            <li class="page-item ${active}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `);
    }
    
    // Next
    const nextDisabled = pagination.current_page === pagination.total_pages ? 'disabled' : '';
    ul.append(`
        <li class="page-item ${nextDisabled}">
            <a class="page-link" href="#" data-page="${pagination.current_page + 1}">&raquo;</a>
        </li>
    `);
}
</script>
