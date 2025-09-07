<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Owner Details</h1></div>
                <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Owner Details</li></ol></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Owners</h3>
                            <div class="card-tools">
                                <button class="btn btn-primary" onclick="openAddOwnerModal()"><i class="fas fa-plus"></i> Add Owner</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="ownersTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>WhatsApp</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Owner Modal -->
<div class="modal fade" id="ownerModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="ownerModalLabel">Add Owner</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <form id="ownerForm">
          <input type="hidden" name="id" id="ownerId">
          <div class="form-row">
            <div class="form-group col-md-6"><label for="ownerName">Name</label><input class="form-control" name="name" id="ownerName" required></div>
            <div class="form-group col-md-6"><label for="ownerEmail">Email</label><input class="form-control" name="email" id="ownerEmail" type="email"></div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6"><label for="ownerPhone">Phone</label><input class="form-control" name="phone" id="ownerPhone" type="text"></div>
            <div class="form-group col-md-6"><label for="ownerWhatsapp">WhatsApp</label><input class="form-control" name="whatsapp" id="ownerWhatsapp" type="text"></div>
          </div>
          <div class="form-group"><label for="ownerAddress">Address</label><textarea class="form-control" name="address" id="ownerAddress" rows="3"></textarea></div>
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button id="saveOwnerBtn" class="btn btn-primary">Save Owner</button></div>
    </div>
  </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<?php // Include a dedicated, read-only view modal for owners
require_once __DIR__ . '/inc/owner_view_modal.php'; ?>

<script>
function loadOwners(){
  $.get('ajax/owner_api.php',{action:'list'},function(resp){
   if(resp.success){ 
    // destroy existing DataTable instance if present to ensure clean re-init
    try{ if ($.fn.dataTable && $.fn.dataTable.isDataTable('#ownersTable')){ $('#ownersTable').DataTable().clear().destroy(); $('#ownersTable tbody').empty(); } }catch(e){}
    var t=''; resp.data.forEach(function(o,idx){
    t += '<tr>'+
    '<td>'+(idx+1)+'</td>'+
    '<td>'+o.id+'</td>'+
    '<td>'+(o.name||'')+'</td>'+
    '<td>'+(o.phone||'')+'</td>'+
    '<td>'+(o.whatsapp||'')+'</td>'+
    '<td>'+(o.email||'')+'</td>'+
    '<td>'+(o.address||'')+'</td>'+
    '<td>'+(o.added_by_username||'')+'</td>'+
    '<td><button class="btn btn-sm btn-info" onclick="viewOwner('+o.id+')">View</button> '
      +'<button class="btn btn-sm btn-warning edit-owner" data-id="'+o.id+'">Edit</button> '
      +'<button class="btn btn-sm btn-danger delete-owner" data-id="'+o.id+'">Delete</button></td>'+
    '</tr>';
    }); $('#ownersTable tbody').html(t); initDataTable('#ownersTable'); }
    else toastr.error(resp.message||'Failed to load');
  },'json');
}

function openAddOwnerModal(){ $('#ownerForm')[0].reset(); $('#ownerId').val(''); $('#ownerModal').modal('show'); }

$(function(){
  loadOwners();
  // Edit handler: load data and ensure form is editable
  $(document).on('click','.edit-owner', function(){
    var id=$(this).data('id');
    $.get('ajax/owner_api.php',{action:'get',id:id}, function(resp){
      if(resp.success){ var o=resp.data;
        $('#ownerForm').find('input,textarea,select').prop('disabled', false);
        $('#saveOwnerBtn').show();
        $('#ownerId').val(o.id); $('#ownerName').val(o.name); $('#ownerPhone').val(o.phone); $('#ownerWhatsapp').val(o.whatsapp||''); $('#ownerEmail').val(o.email); $('#ownerAddress').val(o.address);
        $('#ownerModal').modal('show');
      } else toastr.error('Not found');
    },'json');
  });

  // Delete via AJAX and refresh table (no page reload)
  $(document).on('click','.delete-owner', function(){
    if(!confirm('Delete?')) return;
    var id=$(this).data('id');
    $.post('ajax/owner_api.php',{action:'delete',id:id}, function(resp){
      if(resp.success){ toastr.success(resp.message); loadOwners(); }
      else toastr.error(resp.message||'Delete failed');
    },'json');
  });

  // Save via AJAX and refresh table (hide modal first, then reload table)
  $('#saveOwnerBtn').click(function(){
    var data=$('#ownerForm').serialize()+'&action=save';
    $.post('ajax/owner_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message); $('#ownerModal').modal('hide'); setTimeout(loadOwners, 250); } else toastr.error(resp.message||'Save failed'); },'json');
  });

  // When modal closes, reset form state so next open is editable
  $('#ownerModal').on('hidden.bs.modal', function(){
    $('#ownerForm').find('input,textarea,select').prop('disabled', false);
    $('#saveOwnerBtn').show();
  });
});

function viewOwner(id){
  $.get('ajax/owner_api.php',{action:'get',id:id}, function(resp){
    if(resp.success){
      var o = resp.data;
      var html = '<table class="table table-sm table-borderless">'+
        '<tr><th>ID</th><td>'+ (o.id||'') +'</td></tr>'+
        '<tr><th>Name</th><td>'+ (o.name||'') +'</td></tr>'+
        '<tr><th>Phone</th><td>'+ (o.phone||'') +'</td></tr>'+
        '<tr><th>WhatsApp</th><td>'+ (o.whatsapp||'') +'</td></tr>'+
        '<tr><th>Email</th><td>'+(o.email?('<a href="mailto:'+o.email+'">'+o.email+'</a>'):'N/A')+'</td></tr>'+
        '<tr><th>Address</th><td>'+ (o.address||'') +'</td></tr>'+
        '<tr><th>Added By</th><td>'+ (o.added_by_username||o.added_by||'') +'</td></tr>'+
        '</table>';
      $('#ownerViewModal .owner-view-content').html(html);
      $('#ownerViewModal').modal('show');
    } else toastr.error('Not found');
  },'json');
}
</script>
