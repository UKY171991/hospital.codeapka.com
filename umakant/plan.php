<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Menu Plan</h1></div>
                <div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="index.php">Home</a></li><li class="breadcrumb-item active">Menu Plan</li></ol></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Plan Management</h3>
                            <div class="card-tools">
                                <button class="btn btn-primary" onclick="openAddPlanModal()"><i class="fas fa-plus"></i> Add Plan</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="plansTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>UPI</th>
                                        <th>Type</th>
                                        <th>Equivalent</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
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

<!-- Plan Modal -->
<div class="modal fade" id="planModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title" id="planModalLabel">Add Plan</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">
        <form id="planForm">
          <input type="hidden" name="id" id="planId">
          <div class="form-group"><label>Title</label><input class="form-control" name="name" id="planName" required></div>
          <div class="form-row">
            <div class="form-group col-md-4"><label>Price</label><input class="form-control" name="price" id="planPrice" type="number" step="0.01" required></div>
            <div class="form-group col-md-4"><label>UPI</label><input class="form-control" name="upi" id="planUpi" type="text"></div>
            <div class="form-group col-md-4"><label>Type</label><select class="form-control" name="time_type" id="planType"><option value="monthly">Monthly</option><option value="yearly">Yearly</option></select></div>
          </div>
          <div class="form-group"><label>Description</label><textarea class="form-control" name="description" id="planDescription"></textarea></div>
          <div class="form-row">
            <div class="form-group col-md-6"><label>Start Date</label><input type="date" class="form-control" name="start_date" id="planStart"></div>
            <div class="form-group col-md-6"><label>End Date</label><input type="date" class="form-control" name="end_date" id="planEnd"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button id="savePlanBtn" class="btn btn-primary">Save Plan</button></div>
    </div>
  </div>
</div>

<!-- Plan View Modal (read-only) -->
<div class="modal fade" id="planViewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">View Plan</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group"><label>Title</label><p id="viewPlanName" class="form-control-plaintext"></p></div>
        <div class="form-row">
          <div class="form-group col-md-4"><label>Price</label><p id="viewPlanPrice" class="form-control-plaintext"></p></div>
          <div class="form-group col-md-4"><label>UPI</label><p id="viewPlanUpi" class="form-control-plaintext"></p></div>
          <div class="form-group col-md-4"><label>Type</label><p id="viewPlanType" class="form-control-plaintext"></p></div>
        </div>
        <div class="form-group"><label>Description</label><p id="viewPlanDescription" class="form-control-plaintext"></p></div>
        <div class="form-row">
          <div class="form-group col-md-6"><label>Start Date</label><p id="viewPlanStart" class="form-control-plaintext"></p></div>
          <div class="form-group col-md-6"><label>End Date</label><p id="viewPlanEnd" class="form-control-plaintext"></p></div>
        </div>
        <div class="form-group"><label>Equivalent</label><p id="viewPlanEq" class="form-control-plaintext"></p></div>
        <div class="form-group"><label>Added By</label><p id="viewPlanAddedBy" class="form-control-plaintext"></p></div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-dismiss="modal">Close</button></div>
    </div>
  </div>
</div>

<?php require_once 'inc/footer.php'; ?>

<script>
function loadPlans(){
  $.get('ajax/plan_api.php',{action:'list'},function(resp){
    if(resp.success){ var t=''; resp.data.forEach(function(p,idx){
      var eq = '';
      if(p.time_type === 'monthly') eq = (parseFloat(p.price||0)*12).toFixed(2) + ' / year';
      else if(p.time_type === 'yearly') eq = (parseFloat(p.price||0)/12).toFixed(2) + ' / month';
    t += '<tr>'+
    '<td>'+(idx+1)+'</td>'+
    '<td>'+p.id+'</td>'+
    '<td>'+ (p.name||'') +'</td>'+
    '<td>'+ (p.price!=null?parseFloat(p.price).toFixed(2):'') +'</td>'+
    '<td>'+ (p.upi||'') +'</td>'+
    '<td>'+ (p.time_type||'') +'</td>'+
    '<td>'+ eq +'</td>'+
    '<td>'+ (p.start_date||'') +'</td>'+
    '<td>'+ (p.end_date||'') +'</td>'+
    '<td>'+ (p.added_by_username||'') +'</td>'+
    '<td><button class="btn btn-sm btn-info" onclick="viewPlan('+p.id+')">View</button> '+
      '<button class="btn btn-sm btn-warning edit-plan" data-id="'+p.id+'">Edit</button> '+
      '<button class="btn btn-sm btn-danger delete-plan" data-id="'+p.id+'">Delete</button></td>'+
    '</tr>'; }); $('#plansTable tbody').html(t); initDataTable('#plansTable'); }
    else toastr.error(resp.message||'Failed');
  },'json');
}

function openAddPlanModal(){
  $('#planForm')[0].reset();
  $('#planId').val('');
  // ensure form inputs are enabled and Save button visible
  $('#planForm').find('input,textarea,select').prop('disabled', false);
  $('#savePlanBtn').show();
  $('#planModalLabel').text('Add Plan');
  $('#planModal').modal('show');
}

  $(function(){ loadPlans();
  $(document).on('click','.edit-plan', function(){
    var id=$(this).data('id');
    $.get('ajax/plan_api.php',{action:'get',id:id}, function(resp){
      if(resp.success){
        var p=resp.data;
        $('#planId').val(p.id);
        $('#planName').val(p.name);
        $('#planDescription').val(p.description);
        $('#planPrice').val(p.price);
        $('#planUpi').val(p.upi||'');
        $('#planType').val(p.time_type||'monthly');
        $('#planStart').val(p.start_date);
        $('#planEnd').val(p.end_date);
        // ensure fields are editable and Save button visible for edit
        $('#planForm').find('input,textarea,select').prop('disabled', false);
        $('#savePlanBtn').show();
        $('#planModalLabel').text('Edit Plan');
        $('#planModal').modal('show');
      } else toastr.error('Not found');
    },'json');
  });

  $(document).on('click','.delete-plan', function(){ if(!confirm('Delete?')) return; var id=$(this).data('id'); $.post('ajax/plan_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); location.reload(); } else toastr.error(resp.message||'Delete failed'); },'json'); });

  $('#savePlanBtn').click(function(){ var data=$('#planForm').serialize()+'&action=save'; $.post('ajax/plan_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message); $('#planModal').modal('hide'); location.reload(); } else toastr.error(resp.message||'Save failed'); },'json'); });
});

function viewPlan(id){
  $.get('ajax/plan_api.php',{action:'get',id:id}, function(resp){
    if(resp.success){
      var p = resp.data;
      var eq = '';
      if(p.time_type === 'monthly') eq = (parseFloat(p.price||0)*12).toFixed(2) + ' / year';
      else if(p.time_type === 'yearly') eq = (parseFloat(p.price||0)/12).toFixed(2) + ' / month';

      $('#viewPlanName').text(p.name || '');
      $('#viewPlanDescription').text(p.description || '');
      $('#viewPlanPrice').text(p.price != null ? parseFloat(p.price).toFixed(2) : '');
      $('#viewPlanUpi').text(p.upi || '');
      $('#viewPlanType').text(p.time_type || '');
      $('#viewPlanEq').text(eq);
      $('#viewPlanStart').text(p.start_date || '');
      $('#viewPlanEnd').text(p.end_date || '');
      $('#viewPlanAddedBy').text(p.added_by_username || '');

      $('#planViewModal').modal('show');
    } else {
      toastr.error('Not found');
    }
  },'json');
}
</script>
