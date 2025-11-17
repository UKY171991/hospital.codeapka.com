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
                                        <th>ID</th>
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
                <td>${task.id}</td>
                <td>${task.title}</td>
                <td>${task.client_name || '-'}</td>
                <td>${priorityBadge}</td>
                <td>${statusBadge}</td>
                <td>${task.due_date || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="editTask(${task.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteTask(${task.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    taskTable = $('#taskTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        destroy: true
    });
}

function openTaskModal() {
    $('#taskId').val('');
    $('#taskForm')[0].reset();
    $('#taskClient').val('').trigger('change');
    $('#taskModalTitle').text('Add Task');
    $('#taskModal').modal('show');
}

function editTask(id) {
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
                $('#taskNotes').val(data.notes);
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

function saveTask() {
    const formData = {
        action: $('#taskId').val() ? 'update_task' : 'add_task',
        id: $('#taskId').val(),
        title: $('#taskTitle').val(),
        client_id: $('#taskClient').val(),
        description: $('#taskDescription').val(),
        priority: $('#taskPriority').val(),
        status: $('#taskStatus').val(),
        due_date: $('#taskDueDate').val(),
        notes: $('#taskNotes').val()
    };

    $.ajax({
        url: 'ajax/client_api.php',
        type: 'POST',
        data: formData,
        cache: false,
        dataType: 'json',
        success: function(response) {
            if (response && response.success) {
                toastr.success(response.message || 'Task saved successfully');
                $('#taskModal').modal('hide');
                
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
    if (!confirm('Are you sure you want to delete this task?')) {
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
</script>

<?php require_once 'inc/footer.php'; ?>
