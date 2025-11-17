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
                    <h1><i class="fas fa-tasks mr-2"></i>Add Task</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="task_list.php">Tasks</a></li>
                        <li class="breadcrumb-item active">Add Task</li>
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
                                Task Information
                            </h3>
                        </div>
                        <form id="taskForm">
                            <div class="card-body">
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
                                    <textarea class="form-control" id="taskDescription" rows="4" required></textarea>
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
                                    <textarea class="form-control" id="taskNotes" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save Task
                                </button>
                                <a href="task_list.php" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    loadClients();

    $('#taskForm').on('submit', function(e) {
        e.preventDefault();
        saveTask();
    });
});

function loadClients() {
    $.ajax({
        url: 'ajax/client_api.php',
        type: 'GET',
        data: { action: 'get_clients' },
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

function saveTask() {
    const formData = {
        action: 'add_task',
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
                toastr.success(response.message || 'Task added successfully');
                setTimeout(function() {
                    window.location.href = 'task_list.php';
                }, 1000);
            } else {
                toastr.error(response.message || 'Failed to add task');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error saving task:', error);
            toastr.error('An error occurred while saving task');
        }
    });
}
</script>

<?php require_once 'inc/footer.php'; ?>
