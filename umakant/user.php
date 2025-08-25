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
                    <h1>Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">User Management</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#userModal" onclick="openAddUserModal()">
                                    <i class="fas fa-plus"></i> Add User
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="usersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="id">
                    <div class="form-group">
                        <label for="userUsername">Username *</label>
                        <input type="text" class="form-control" id="userUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="userPassword">Password *</label>
                        <input type="password" class="form-control" id="userPassword" name="password" required>
                        <small class="form-text text-muted">Leave blank to keep current password when editing</small>
                    </div>
                    <div class="form-group">
                        <label for="userFullName">Full Name *</label>
                        <input type="text" class="form-control" id="userFullName" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="userEmail">Email</label>
                        <input type="email" class="form-control" id="userEmail" name="email">
                    </div>
                    <div class="form-group">
                        <label for="userRole">Role *</label>
                        <select class="form-control" id="userRole" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="userIsActive">Status *</label>
                        <select class="form-control" id="userIsActive" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveUserBtn">Save User</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
function loadUsers(){
    $.get('ajax/user_api.php',{action:'list'},function(resp){
        if(resp.success){
            var t=''; resp.data.forEach(function(u){
                t += '<tr>'+
                         '<td>'+u.id+'</td>'+
                         '<td>'+ (u.username||'') +'</td>'+
                         '<td>'+ (u.email||'') +'</td>'+
                         '<td>'+ (u.full_name||'') +'</td>'+
                         '<td>'+ (u.role||'') +'</td>'+
                         '<td>'+ (u.is_active==1? 'Active':'Inactive') +'</td>'+
                         '<td>'+ (u.last_login||'Never') +'</td>'+
                         '<td><button class="btn btn-sm btn-info view-user" data-id="'+u.id+'">View</button> '+
                                    '<button class="btn btn-sm btn-warning edit-user" data-id="'+u.id+'">Edit</button> '+
                                    '<button class="btn btn-sm btn-danger delete-user" data-id="'+u.id+'">Delete</button></td>'+
                         '</tr>';
            });
            $('#usersTable tbody').html(t);
        } else toastr.error('Failed to load users');
    },'json');
}

function openAddUserModal(){
    $('#userForm')[0].reset(); $('#userId').val(''); $('#userPassword').attr('required',true); $('#userModal').modal('show');
}

$(function(){
    loadUsers();

    $('#saveUserBtn').click(function(){
        var data = $('#userForm').serialize() + '&action=save';
        $.post('ajax/user_api.php', data, function(resp){
            if(resp.success){ toastr.success(resp.message||'Saved'); $('#userModal').modal('hide'); loadUsers(); }
            else toastr.error(resp.message||'Save failed');
        },'json');
    });

    $('#usersTable').on('click', '.edit-user', function(){
        var id = $(this).data('id');
        $.get('ajax/user_api.php',{action:'get',id:id}, function(resp){
            if(resp.success){ var d=resp.data; $('#userId').val(d.id); $('#userUsername').val(d.username); $('#userFullName').val(d.full_name); $('#userEmail').val(d.email); $('#userRole').val(d.role); $('#userIsActive').val(d.is_active?1:0); $('#userPassword').attr('required',false); $('#userModal').modal('show'); }
            else toastr.error('User not found');
        },'json');
    });

    $('#usersTable').on('click', '.delete-user', function(){
        if(!confirm('Delete user?')) return; var id=$(this).data('id'); $.post('ajax/user_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadUsers(); } else toastr.error(resp.message||'Delete failed'); },'json');
    });
});
</script>