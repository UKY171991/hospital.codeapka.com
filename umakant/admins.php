<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Admins</h1></div>
                <div class="col-sm-6 text-right"><button id="addAdminBtn" class="btn btn-primary">Add Admin</button></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <table id="adminsTable" class="table table-bordered table-striped">
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
            </div>
        </div>
    </section>
</div>

<!-- Admin Modal (reuses user modal fields) -->
<div class="modal fade" id="adminModal" tabindex="-1" role="dialog" aria-labelledby="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminModalLabel">Add Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="adminForm">
                    <input type="hidden" id="adminId" name="id">
                    <div class="form-group">
                        <label>Username *</label>
                        <input type="text" class="form-control" id="adminUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" class="form-control" id="adminPassword" name="password">
                        <small class="form-text text-muted">Leave blank to keep current password when editing</small>
                    </div>
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" class="form-control" id="adminFullName" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="adminEmail" name="email">
                    </div>
                    <input type="hidden" name="role" value="admin">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="adminIsActive" name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAdminBtn">Save Admin</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
function loadAdmins(){
    $.get('ajax/user_api.php',{action:'list', role:'admin'},function(resp){
        if(resp.success){ var t=''; resp.data.forEach(function(u){ t += '<tr>'+
                '<td>'+u.id+'</td>'+
                '<td>'+ (u.username||'') +'</td>'+
                '<td>'+ (u.email||'') +'</td>'+
                '<td>'+ (u.full_name||'') +'</td>'+
                '<td>'+ (u.role||'') +'</td>'+
                '<td>'+ (u.is_active==1? 'Active':'Inactive') +'</td>'+
                '<td>'+ (u.last_login||'Never') +'</td>'+
                '<td><button class="btn btn-sm btn-warning edit-admin" data-id="'+u.id+'">Edit</button> '+
                           '<button class="btn btn-sm btn-danger delete-admin" data-id="'+u.id+'">Delete</button></td>'+
                '</tr>'; }); $('#adminsTable tbody').html(t);
        } else toastr.error('Failed to load admins');
    },'json');
}

$(function(){
    loadAdmins();
    $('#addAdminBtn').click(function(){ $('#adminForm')[0].reset(); $('#adminId').val(''); $('#adminModal').modal('show'); });
    $('#saveAdminBtn').click(function(){ var data = $('#adminForm').serialize() + '&action=save'; $.post('ajax/user_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#adminModal').modal('hide'); loadAdmins(); } else toastr.error(resp.message||'Save failed'); },'json'); });

    $('#adminsTable').on('click', '.edit-admin', function(){ var id=$(this).data('id'); $.get('ajax/user_api.php',{action:'get',id:id}, function(resp){ if(resp.success){ var d=resp.data; $('#adminId').val(d.id); $('#adminUsername').val(d.username); $('#adminFullName').val(d.full_name); $('#adminEmail').val(d.email); $('#adminIsActive').val(d.is_active?1:0); $('#adminPassword').attr('required',false); $('#adminModal').modal('show'); } else toastr.error('Admin not found'); },'json'); });

    $('#adminsTable').on('click', '.delete-admin', function(){ if(!confirm('Delete admin?')) return; var id=$(this).data('id'); $.post('ajax/user_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadAdmins(); } else toastr.error(resp.message||'Delete failed'); },'json'); });
});
</script>
