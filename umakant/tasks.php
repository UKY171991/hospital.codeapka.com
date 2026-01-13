<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<style>
    /* Responsive styles for tasks page */
    @media (max-width: 768px) {
        .content-wrapper {
            margin-left: 0;
        }
        
        .card-tools .btn {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
        
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            padding: 1rem;
        }
        
        .btn-group .btn {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .table-responsive {
            border-radius: 0.25rem;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
        .card-img-top {
            border-radius: 0.25rem 0.25rem 0 0;
        }
        
        .modal-body {
            padding: 1rem;
        }
        
        .modal-footer {
            padding: 0.75rem 1rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .modal-footer .btn {
            flex: 1;
            min-width: 100px;
        }
        
        .select2-container--bootstrap4 .select2-selection {
            font-size: 0.875rem;
        }
        
        .form-control {
            font-size: 0.875rem;
        }
        
        .text-truncate {
            max-width: 100%;
        }
    }
    
    @media (max-width: 576px) {
        .content-header h1 {
            font-size: 1.5rem;
        }
        
        .breadcrumb {
            font-size: 0.875rem;
        }
        
        .card-header h3 {
            font-size: 1.1rem;
        }
        
        .btn-group .btn {
            padding: 0.25rem 0.375rem;
            font-size: 0.7rem;
        }
        
        .btn-group .btn i {
            font-size: 0.8rem;
        }
        
        .modal-header h5 {
            font-size: 1rem;
        }
        
        .form-group label {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
        
        .table th, .table td {
            font-size: 0.8rem;
            padding: 0.5rem 0.25rem;
        }
        
        .screenshot-preview .card {
            margin-bottom: 0.5rem;
        }
    }
    
    /* Improve screenshot preview layout */
    .screenshot-preview .card {
        transition: transform 0.2s ease-in-out;
    }
    
    .screenshot-preview .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    /* DataTables responsive improvements */
    .dtr-details {
        padding: 1rem !important;
    }
    
    .dtr-details li {
        padding: 0.25rem 0;
        border-bottom: 1px solid #eee;
    }
    
    .dtr-details li:last-child {
        border-bottom: none;
    }
    
    /* Button group improvements for mobile */
    @media (max-width: 768px) {
        .btn-group-vertical .btn {
            margin-bottom: 0.25rem;
        }
    }
    
    /* General modal scrolling fix */
    .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    
    .modal-dialog {
        max-height: calc(100vh - 100px);
        display: flex;
        flex-direction: column;
    }
    
    .modal-content {
        max-height: calc(100vh - 100px);
        display: flex;
        flex-direction: column;
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-tasks mr-2"></i>Task Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="client_dashboard.php">Client Dashboard</a></li>
                        <li class="breadcrumb-item active">Tasks</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list mr-2"></i>
                                All Tasks
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" onclick="openTaskModal()">
                                    <i class="fas fa-plus"></i> Add Task
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="taskTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th class="all">Sr. No.</th>
                                            <th class="min-tablet">Title</th>
                                            <th class="min-tablet">Client</th>
                                            <th class="min-tablet-p">Priority</th>
                                            <th class="min-tablet-p">Status</th>
                                            <th class="none">Due Date</th>
                                            <th class="all">Actions</th>
                                        </tr>
                                    </thead>
                                <tbody id="taskTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- View Task Modal -->
<div class="modal fade" id="viewTaskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tasks mr-2"></i>
                    Task Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <h6 class="text-primary"><i class="fas fa-info-circle mr-2"></i>Task Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Title:</strong></td>
                                <td id="viewTaskTitle">-</td>
                            </tr>
                            <tr>
                                <td><strong>Client:</strong></td>
                                <td id="viewTaskClient">-</td>
                            </tr>
                            <tr>
                                <td><strong>Priority:</strong></td>
                                <td id="viewTaskPriority">-</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td id="viewTaskStatus">-</td>
                            </tr>
                            <tr>
                                <td><strong>Due Date:</strong></td>
                                <td id="viewTaskDueDate">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <h6 class="text-primary"><i class="fas fa-align-left mr-2"></i>Description</h6>
                        <p id="viewTaskDescription" class="text-muted">-</p>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fas fa-link mr-2"></i>Website URLs</h6>
                        <div id="viewTaskUrls">
                            <p class="text-muted">No URLs provided</p>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fas fa-images mr-2"></i>Screenshots</h6>
                        <div id="viewTaskScreenshots" class="row">
                            <p class="text-muted col-12">No screenshots available</p>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-primary"><i class="fas fa-sticky-note mr-2"></i>Notes</h6>
                        <p id="viewTaskNotes" class="text-muted">No notes available</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="editTaskFromView()">
                    <i class="fas fa-edit mr-1"></i> Edit Task
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tasks mr-2"></i>
                    <span id="taskModalTitle">Add Task</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="taskForm">
                <div class="modal-body">
                    <input type="hidden" id="taskId">
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="taskTitle">Task Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="taskTitle" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="taskClient">Client <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="taskClient" required>
                                    <option value="">Select Client</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="display: none;">
                        <label for="taskDescription">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="taskDescription" rows="3" required></textarea>
                    </div>

                    <!-- TinyMCE Rich Text Editor for Description -->
                    <div class="form-group">
                        <label for="taskDescriptionRich">Rich Text Description</label>
                        <div id="taskDescriptionRich" style="height: 300px;"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="taskPriority">Priority <span class="text-danger">*</span></label>
                                <select class="form-control" id="taskPriority" required>
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="taskStatus">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="taskStatus" required>
                                    <option value="Pending" selected>Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <option value="On Hold">On Hold</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="taskDueDate">Due Date</label>
                                <input type="date" class="form-control" id="taskDueDate">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="taskWebsiteUrls">Website URLs</label>
                        <textarea class="form-control" id="taskWebsiteUrls" rows="3" placeholder="Enter URLs (one per line)&#10;https://example.com&#10;https://another-site.com"></textarea>
                        <small class="form-text text-muted">Enter one URL per line</small>
                    </div>

                    <div class="form-group">
                        <label for="taskScreenshots">Screenshots</label>
                        <input type="file" class="form-control-file" id="taskScreenshots" multiple accept="image/*">
                        <small class="form-text text-muted">You can select multiple images (JPG, PNG, GIF, WEBP)</small>
                        <div id="screenshotPreview" class="mt-2"></div>
                        <div id="existingScreenshots" class="mt-2"></div>
                    </div>

                    <div class="form-group">
                        <label for="taskNotes">Notes</label>
                        <textarea class="form-control" id="taskNotes" rows="2"></textarea>
                    </div>

                    <!-- TinyMCE Rich Text Editor for Notes -->
                    <div class="form-group">
                        <label for="taskNotesRich">Rich Text Notes</label>
                        <div id="taskNotesRich" style="height: 200px;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Save Task
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let taskTable;

let screenshotsToDelete = [];

$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4',
        dropdownParent: $('#taskModal')
    });

    // Initialize TinyMCE Rich Text Editor
    tinymce.init({
        selector: '#taskDescriptionRich',
        height: 300,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic backcolor | \
            alignleft aligncenter alignright alignjustify | \
            bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        setup: function (editor) {
            editor.on('change', function () {
                // Update the hidden textarea when content changes
                $('#taskDescription').val(editor.getContent());
            });
        }
    });
    
    // Initialize TinyMCE for Notes
    tinymce.init({
        selector: '#taskNotesRich',
        height: 200,
        plugins: [
            'advlist autolink lists link charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table paste code help wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic | \
            alignleft aligncenter alignright | \
            bullist numlist outdent indent | removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        setup: function (editor) {
            editor.on('change', function () {
                // Update the hidden textarea when content changes
                $('#taskNotes').val(editor.getContent());
            });
        }
    });

    loadClients();
    loadClients();
    
    // Initialize DataTable with server-side processing
    taskTable = $('#taskTable').DataTable({
        serverSide: true,
        processing: true,
        pageLength: 50,
        ajax: {
            url: 'ajax/client_api.php',
            data: function(d) {
                d.action = 'get_tasks';
            }
        },
        columns: [
            { 
                data: null, 
                orderable: false,
                className: 'text-center',
                render: function (data, type, row, meta) { 
                    return meta.row + meta.settings._iDisplayStart + 1; 
                } 
            },
            { data: 'title' },
            { data: 'client_name', defaultContent: '-' },
            { 
                data: 'priority',
                render: function(data) {
                    if (data === 'Urgent') return '<span class="badge badge-danger">Urgent</span>';
                    if (data === 'High') return '<span class="badge badge-warning">High</span>';
                    if (data === 'Medium') return '<span class="badge badge-info">Medium</span>';
                    return '<span class="badge badge-secondary">Low</span>';
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    if (data === 'Completed') return '<span class="badge badge-success">Completed</span>';
                    if (data === 'In Progress') return '<span class="badge badge-primary">In Progress</span>';
                    if (data === 'On Hold') return '<span class="badge badge-warning">On Hold</span>';
                    return '<span class="badge badge-secondary">Pending</span>';
                }
            },
            { data: 'due_date', defaultContent: '-' },
            { 
                data: 'id',
                orderable: false,
                className: 'text-center',
                render: function(data) {
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-primary" onclick="viewTask(${data})" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-info" onclick="editTask(${data})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTask(${data})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[4, 'asc']], // Default sort by Status
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRow,
                type: 'inline'
            }
        },
        columnDefs: [
            { responsivePriority: 1, targets: 0 }, // Always show Sr. No.
            { responsivePriority: 2, targets: 6 }, // Always show Actions
            { responsivePriority: 3, targets: 3 }, // Show Priority on tablet and up
            { responsivePriority: 4, targets: 4 }, // Show Status on tablet and up
            { responsivePriority: 5, targets: 1 }, // Show Title on tablet and up
            { responsivePriority: 6, targets: 2 }, // Show Client on tablet and up
            { responsivePriority: 7, targets: 5 }  // Due Date is lowest priority
        ]
    });

    $('#taskForm').on('submit', function(e) {
        e.preventDefault();
        saveTask();
    });

    // Preview screenshots before upload
    $('#taskScreenshots').on('change', function(e) {
        previewScreenshots(e.target.files);
    });
});

function openTaskModal() {
    $('#taskId').val('');
    $('#taskForm')[0].reset();
    $('#taskClient').val('').trigger('change');
    $('#screenshotPreview').empty();
    $('#existingScreenshots').empty();
    screenshotsToDelete = [];
    $('#taskModalTitle').text('Add Task');
    
    // Clear TinyMCE content
    if (tinymce.get('taskDescriptionRich')) {
        tinymce.get('taskDescriptionRich').setContent('');
        $('#taskDescription').val('');
    }
    if (tinymce.get('taskNotesRich')) {
        tinymce.get('taskNotesRich').setContent('');
        $('#taskNotes').val('');
    }
    
    $('#taskModal').modal('show');
}

function previewScreenshots(files) {
    const preview = $('#screenshotPreview');
    preview.empty();
    
    if (files.length === 0) return;
    
    preview.append('<div class="row">');
    
    Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.append(`
                <div class="col-md-3 col-sm-6 col-6 mb-2">
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted d-block text-truncate">${file.name}</small>
                        </div>
                    </div>
                </div>
            `);
        };
        reader.readAsDataURL(file);
    });
    
    preview.append('</div>');
}

function editTask(id) {
    screenshotsToDelete = [];
    
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { action: 'get_task', id: id },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                $('#taskId').val(data.id);
                $('#taskTitle').val(data.title);
                $('#taskClient').val(data.client_id).trigger('change');
                $('#taskDescription').val(data.description);
                $('#taskPriority').val(data.priority);
                $('#taskStatus').val(data.status);
                $('#taskDueDate').val(data.due_date);
                $('#taskWebsiteUrls').val(data.website_urls || '');
                $('#taskNotes').val(data.notes);
                
                // Set TinyMCE content
                if (tinymce.get('taskDescriptionRich')) {
                    tinymce.get('taskDescriptionRich').setContent(data.description || '');
                }
                if (tinymce.get('taskNotesRich')) {
                    tinymce.get('taskNotesRich').setContent(data.notes || '');
                }
                
                // Display existing screenshots
                displayExistingScreenshots(data.screenshots);
                
                $('#screenshotPreview').empty();
                $('#taskModalTitle').text('Edit Task');
                $('#taskModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading task:', error);
            toastr.error('Failed to load task');
        }
    });
}

function displayExistingScreenshots(screenshotsJson) {
    const container = $('#existingScreenshots');
    container.empty();
    
    if (!screenshotsJson) return;
    
    let screenshots = [];
    try {
        screenshots = JSON.parse(screenshotsJson);
    } catch (e) {
        screenshots = [];
    }
    
    if (screenshots.length === 0) return;
    
    container.append('<label>Existing Screenshots:</label><div class="row" id="existingScreenshotsRow"></div>');
    const row = $('#existingScreenshotsRow');
    
    screenshots.forEach((screenshot, index) => {
        row.append(`
            <div class="col-md-3 col-sm-6 col-6 mb-2" id="screenshot-${index}">
                <div class="card">
                    <img src="${screenshot}" class="card-img-top" style="height: 100px; object-fit: cover;">
                    <div class="card-body p-2">
                        <button type="button" class="btn btn-sm btn-danger btn-block" onclick="removeScreenshot('${screenshot}', ${index})">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        `);
    });
}

function removeScreenshot(screenshot, index) {
    screenshotsToDelete.push(screenshot);
    $(`#screenshot-${index}`).remove();
    toastr.info('Screenshot will be deleted when you save');
}

function saveTask() {
    // Update the hidden textarea with TinyMCE content before saving
    if (tinymce.get('taskDescriptionRich')) {
        $('#taskDescription').val(tinymce.get('taskDescriptionRich').getContent());
    }
    if (tinymce.get('taskNotesRich')) {
        $('#taskNotes').val(tinymce.get('taskNotesRich').getContent());
    }
    
    const formData = new FormData();
    
    formData.append('action', $('#taskId').val() ? 'update_task' : 'add_task');
    formData.append('id', $('#taskId').val());
    formData.append('title', $('#taskTitle').val());
    formData.append('client_id', $('#taskClient').val());
    formData.append('description', $('#taskDescription').val());
    formData.append('priority', $('#taskPriority').val());
    formData.append('status', $('#taskStatus').val());
    formData.append('due_date', $('#taskDueDate').val());
    formData.append('website_urls', $('#taskWebsiteUrls').val());
    formData.append('notes', $('#taskNotes').val());
    
    // Add screenshots
    const screenshotFiles = $('#taskScreenshots')[0].files;
    for (let i = 0; i < screenshotFiles.length; i++) {
        formData.append('screenshots[]', screenshotFiles[i]);
    }
    
    // Add screenshots to delete
    if (screenshotsToDelete.length > 0) {
        formData.append('delete_screenshots', JSON.stringify(screenshotsToDelete));
    }

    $.ajax({
        url: 'ajax/client_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Task saved successfully');
                $('#taskModal').modal('hide');
                screenshotsToDelete = [];
                
                setTimeout(function() {
                    taskTable.ajax.reload(null, false);
                }, 300);
            } else {
                toastr.error(response.message || 'Failed to save task');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving task:', error);
            toastr.error('An error occurred while saving task');
        }
    });
}

function deleteTask(id) {
    if (!confirm('Are you sure you want to delete this task? All associated screenshots will also be deleted.')) {
        return;
    }

    $.ajax({
        url: 'ajax/client_api.php',
        type: 'POST',
        data: { action: 'delete_task', id: id },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Task deleted successfully');
                setTimeout(function() {
                    taskTable.ajax.reload(null, false);
                }, 200);
            } else {
                toastr.error(response.message || 'Failed to delete task');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error deleting task:', error);
            toastr.error('An error occurred while deleting task');
        }
    });
}

let currentViewTaskId = null;

function viewTask(id) {
    currentViewTaskId = id;
    
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { action: 'get_task', id: id },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const task = response.data;
                
                // Basic information
                $('#viewTaskTitle').text(task.title || '-');
                $('#viewTaskClient').text(task.client_name || '-');
                $('#viewTaskDescription').html(task.description || '-');
                $('#viewTaskDueDate').text(task.due_date || '-');
                $('#viewTaskNotes').html(task.notes || 'No notes available');
                
                // Priority badge
                const priorityBadge = task.priority === 'Urgent' ? 
                    '<span class="badge badge-danger">Urgent</span>' : 
                    task.priority === 'High' ? 
                    '<span class="badge badge-warning">High</span>' : 
                    task.priority === 'Medium' ? 
                    '<span class="badge badge-info">Medium</span>' : 
                    '<span class="badge badge-secondary">Low</span>';
                $('#viewTaskPriority').html(priorityBadge);
                
                // Status badge
                const statusBadge = task.status === 'Completed' ? 
                    '<span class="badge badge-success">Completed</span>' : 
                    task.status === 'In Progress' ? 
                    '<span class="badge badge-primary">In Progress</span>' : 
                    task.status === 'On Hold' ? 
                    '<span class="badge badge-warning">On Hold</span>' : 
                    '<span class="badge badge-secondary">Pending</span>';
                $('#viewTaskStatus').html(statusBadge);
                
                // Website URLs
                if (task.website_urls && task.website_urls.trim()) {
                    const urls = task.website_urls.split('\n').filter(url => url.trim());
                    let urlsHtml = '<ul class="list-unstyled">';
                    urls.forEach(url => {
                        urlsHtml += `<li><a href="${url.trim()}" target="_blank"><i class="fas fa-external-link-alt mr-1"></i>${url.trim()}</a></li>`;
                    });
                    urlsHtml += '</ul>';
                    $('#viewTaskUrls').html(urlsHtml);
                } else {
                    $('#viewTaskUrls').html('<p class="text-muted">No URLs provided</p>');
                }
                
                // Screenshots
                let screenshots = [];
                try {
                    screenshots = JSON.parse(task.screenshots || '[]');
                } catch (e) {
                    screenshots = [];
                }
                
                if (screenshots.length > 0) {
                    let screenshotsHtml = '';
                    screenshots.forEach((screenshot, index) => {
                        screenshotsHtml += `
                            <div class="col-md-3 col-sm-6 col-6 mb-2" id="view-screenshot-${index}">
                                <div class="card">
                                    <a href="${screenshot}" target="_blank">
                                        <img src="${screenshot}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    </a>
                                    <div class="card-body p-2">
                                        <button type="button" class="btn btn-sm btn-danger btn-block" onclick="deleteSingleScreenshot(${id}, '${screenshot}', ${index})">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    $('#viewTaskScreenshots').html(screenshotsHtml);
                } else {
                    $('#viewTaskScreenshots').html('<p class="text-muted col-12">No screenshots available</p>');
                }
                
                $('#viewTaskModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading task:', error);
            toastr.error('Failed to load task details');
        }
    });
}

function editTaskFromView() {
    $('#viewTaskModal').modal('hide');
    
    setTimeout(function() {
        if (currentViewTaskId) {
            editTask(currentViewTaskId);
        }
    }, 300);
}

function deleteSingleScreenshot(taskId, screenshot, index) {
    if (!confirm('Are you sure you want to delete this screenshot?')) {
        return;
    }
    
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'POST',
        data: { 
            action: 'delete_single_screenshot',
            task_id: taskId,
            screenshot: screenshot
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success('Screenshot deleted successfully');
                $(`#view-screenshot-${index}`).fadeOut(300, function() {
                    $(this).remove();
                    
                    // Check if there are any screenshots left
                    if ($('#viewTaskScreenshots .col-md-3').length === 0) {
                        $('#viewTaskScreenshots').html('<p class="text-muted col-12">No screenshots available</p>');
                    }
                });
                
                // Reload tasks table
                setTimeout(function() {
                    taskTable.ajax.reload(null, false);
                }, 500);
            } else {
                toastr.error(response.message || 'Failed to delete screenshot');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error deleting screenshot:', error);
            toastr.error('An error occurred while deleting screenshot');
        }
    });
}
</script>

<?php require_once 'inc/footer.php'; ?>
