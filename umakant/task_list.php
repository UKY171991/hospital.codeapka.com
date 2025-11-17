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
                    <h1><i class="fas fa-clipboard-list mr-2"></i>Task List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Task List</li>
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
                                <i class="fas fa-tasks mr-2"></i>
                                All Tasks
                            </h3>
                            <div class="card-tools">
                                <a href="add_task.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Task
                                </a>
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

<script>
let taskTable;

$(document).ready(function() {
    loadTasks();
});

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
                    <button class="btn btn-sm btn-info" onclick="viewTask(${task.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="editTask(${task.id})">
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

function viewTask(id) {
    window.location.href = 'view_task.php?id=' + id;
}

function editTask(id) {
    window.location.href = 'edit_task.php?id=' + id;
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
