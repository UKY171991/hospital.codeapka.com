<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

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
                            <table id="taskTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Title</th>
                                        <th>Client</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
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
                    <div class="col-md-6">
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
                    <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="taskTitle">Task Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="taskTitle" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="taskClient">Client <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="taskClient" required>
                                    <option value="">Select Client</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="taskDescription">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="taskDescription" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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

    loadClients();
    loadTasks();

    $('#taskForm').on('submit', function(e) {
        e.preventDefault();
        saveTask();
    });

    // Preview screenshots before upload
    $('#taskScreenshots').on('change', function(e) {
        previewScreenshots(e.target.files);
    });
});

function loadClients() {
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { 
            action: 'get_clients',
            _: new Date().getTime()
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                const select = $('#taskClient');
                select.empty().append('<option value="">Select Client</option>');
                response.data.forEach(function(client) {
                    select.append(`<option value="${client.id}">${client.name}</option>`);
                });
            }
        }
    });
}

function loadTasks() {
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { 
            action: 'get_tasks',
            _: new Date().getTime()
        },
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                displayTasks(response.data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading tasks:', error);
            toastr.error('Failed to load tasks');
        }
    });
}

function displayTasks(tasks) {
    if ($.fn.DataTable.isDataTable('#taskTable')) {
        $('#taskTable').DataTable().destroy();
    }
    
    const tbody = $('#taskTableBody');
    tbody.empty();

    if (!tasks || tasks.length === 0) {
        tbody.append('<tr><td colspan="7" class="text-center">No tasks found</td></tr>');
        return;
    }

    // Sort tasks by status priority: Pending > In Progress > On Hold > Completed
    const statusOrder = {
        'Pending': 1,
        'In Progress': 2,
        'On Hold': 3,
        'Completed': 4
    };
    
    tasks.sort((a, b) => {
        const statusA = statusOrder[a.status] || 5;
        const statusB = statusOrder[b.status] || 5;
        return statusA - statusB;
    });

    let srNo = 1;
    tasks.forEach(function(task) {
        const priorityBadge = task.priority === 'Urgent' ? 
            '<span class="badge badge-danger">Urgent</span>' : 
            task.priority === 'High' ? 
            '<span class="badge badge-warning">High</span>' : 
            task.priority === 'Medium' ? 
            '<span class="badge badge-info">Medium</span>' : 
            '<span class="badge badge-secondary">Low</span>';
        
        const statusBadge = task.status === 'Completed' ? 
            '<span class="badge badge-success">Completed</span>' : 
            task.status === 'In Progress' ? 
            '<span class="badge badge-primary">In Progress</span>' : 
            task.status === 'On Hold' ? 
            '<span class="badge badge-warning">On Hold</span>' : 
            '<span class="badge badge-secondary">Pending</span>';
        
        const row = `
            <tr>
                <td>${srNo++}</td>
                <td>${task.title}</td>
                <td>${task.client_name || '-'}</td>
                <td>${priorityBadge}</td>
                <td>${statusBadge}</td>
                <td>${task.due_date || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewTask(${task.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-info" onclick="editTask(${task.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteTask(${task.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    taskTable = $('#taskTable').DataTable({
        responsive: true,
        order: [[4, 'asc']], // Sort by Status column
        destroy: true,
        columnDefs: [
            { orderable: false, targets: [0, 6] } // Disable sorting on Sr. No. and Actions
        ]
    });
}

function openTaskModal() {
    $('#taskId').val('');
    $('#taskForm')[0].reset();
    $('#taskClient').val('').trigger('change');
    $('#screenshotPreview').empty();
    $('#existingScreenshots').empty();
    screenshotsToDelete = [];
    $('#taskModalTitle').text('Add Task');
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
                <div class="col-md-3 mb-2">
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                        <div class="card-body p-2">
                            <small class="text-muted">${file.name}</small>
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
            <div class="col-md-3 mb-2" id="screenshot-${index}">
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
                    loadTasks();
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
                    loadTasks();
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
                $('#viewTaskDescription').text(task.description || '-');
                $('#viewTaskDueDate').text(task.due_date || '-');
                $('#viewTaskNotes').text(task.notes || 'No notes available');
                
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
                            <div class="col-md-3 mb-2" id="view-screenshot-${index}">
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
                    loadTasks();
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
