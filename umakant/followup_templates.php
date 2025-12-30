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
                <!-- Template List -->
                <div class="col-md-12">
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header d-flex align-items-center">
                            <h3 class="card-title">Template List</h3>
                            <button type="button" class="btn btn-primary btn-sm ml-auto" id="openAddTemplateModal">
                                <i class="fas fa-plus mr-1"></i> Add New Template
                            </button>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap" id="templatesTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Name</th>
                                        <th>Content Preview</th>
                                        <th class="text-center">Action</th>
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
                            <i class="fas fa-2x fa-sync-alt fa-spin text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add/Edit Template Modal -->
    <div class="modal fade" id="templateModal" tabindex="-1" role="dialog" aria-labelledby="templateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold" id="templateModalLabel">Add New Template</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addTemplateForm">
                    <div class="modal-body p-4">
                        <input type="hidden" id="template_id" name="template_id">
                        <div class="form-group mb-4">
                            <label for="template_name" class="font-weight-bold">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg border-primary" id="template_name" name="template_name" placeholder="E.g., Welcome Message" required>
                        </div>
                        <div class="form-group mb-0">
                            <label for="content" class="font-weight-bold">Template Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="10" placeholder="Enter template content" required></textarea>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle mr-1"></i> You can use this content in followups. HTML and basic styling are supported.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 font-weight-bold" id="saveTemplateBtn">Save Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
        height: 250,
        placeholder: 'Write your template content here...',
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

    // Open Modal for Add
    $('#openAddTemplateModal').on('click', function() {
        resetForm();
        $('#templateModal').modal('show');
    });

    // Handle Form Submission
    $('#addTemplateForm').on('submit', function(e) {
        e.preventDefault();
        
        const $btn = $('#saveTemplateBtn');
        const originalBtnText = $btn.text();
        $btn.prop('disabled', true).text('Saving...');

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
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#templateModal').modal('hide');
                    resetForm();
                    loadTemplates(currentPage);
                } else {
                    toastr.error(response.message || 'Error saving template');
                }
            },
            error: function() {
                toastr.error('Server error occurred');
            },
            complete: function() {
                $btn.prop('disabled', false).text(originalBtnText);
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
                    resetForm();
                    $('#template_id').val(template.id);
                    $('#template_name').val(template.template_name);
                    $('#content').summernote('code', template.content);
                    
                    // Change UI to Edit Mode
                    $('#templateModalLabel').text('Edit Template');
                    $('#saveTemplateBtn').text('Update Template');
                    $('#templateModal').modal('show');
                } else {
                    toastr.error(response.message || 'Error fetching template details');
                }
            },
            error: function() {
                toastr.error('Server error occurred');
            }
        });
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
    $('#templateModalLabel').text('Add New Template');
    $('#saveTemplateBtn').text('Save Template');
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
                    
                    // Strip HTML for preview properly
                    const tempDiv = document.createElement("div");
                    tempDiv.innerHTML = template.content;
                    // Add spaces for block elements before stripping
                    const blocks = tempDiv.querySelectorAll('p, div, br, li');
                    blocks.forEach(b => b.after(' '));
                    let text = tempDiv.textContent || tempDiv.innerText || "";
                    if (text.length > 80) text = text.substring(0, 80) + '...';
                    
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
