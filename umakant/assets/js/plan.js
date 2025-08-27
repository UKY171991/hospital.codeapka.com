// assets/js/plan.js
(function($){
  // Helper: normalize time_type to 'monthly' or 'yearly'
  function normalizeType(val){
    var tt = (val||'monthly').toString().toLowerCase();
    if (tt.indexOf('year') !== -1) return 'yearly';
    return 'monthly';
  }

  function loadPlans(){
    $.get('ajax/plan_api.php',{action:'list'},function(resp){
      if(!resp.success){ toastr.error(resp.message||'Failed to load plans'); return; }

      var rows = resp.data || [];
      var t = '';
      rows.forEach(function(p, idx){
        var tt = normalizeType(p.time_type);
        var eq = tt === 'monthly' ? (parseFloat(p.price||0)*12).toFixed(2) + ' / year' : (parseFloat(p.price||0)/12).toFixed(2) + ' / month';
        t += '<tr>'+
             '<td>'+(idx+1)+'</td>'+
             '<td>'+ (p.id||'') +'</td>'+
             '<td>'+ (p.name||'') +'</td>'+
             '<td>'+ (p.price!=null?parseFloat(p.price).toFixed(2):'') +'</td>'+
             '<td>'+ (p.upi||'') +'</td>'+
             '<td>'+ (tt==='yearly' ? 'Yearly' : 'Monthly') +'</td>'+
             '<td>'+ eq +'</td>'+
             '<td>'+ (p.start_date||'') +'</td>'+
             '<td>'+ (p.end_date||'') +'</td>'+
             '<td>'+ (p.added_by_username||'') +'</td>'+
             '<td>'+
               '<button class="btn btn-sm btn-info view-plan" data-id="'+p.id+'">View</button> '+
               '<button class="btn btn-sm btn-warning edit-plan" data-id="'+p.id+'">Edit</button> '+
               '<button class="btn btn-sm btn-danger delete-plan" data-id="'+p.id+'">Delete</button>'+
             '</td>'+
           '</tr>';
      });

      $('#plansTable tbody').html(t);
      initDataTable('#plansTable');
    },'json');
  }

  function openAddPlanModal(){
    $('#planForm')[0].reset();
    $('#planId').val('');
    $('#planForm').find('input,textarea,select').prop('disabled', false);
    $('#savePlanBtn').show();
    $('#planModalLabel').text('Add Plan');
    $('#planModal').modal('show');
  }

  $(function(){
    loadPlans();

    // add button
    $('#addPlanBtn').on('click', openAddPlanModal);

    // delegated view
    $(document).on('click', '.view-plan', function(){
      var id = $(this).data('id');
      $.get('ajax/plan_api.php',{action:'get',id:id}, function(resp){
        if(!resp.success){ toastr.error(resp.message||'Not found'); return; }
        var p = resp.data || {};
        var tt = normalizeType(p.time_type);
        var eq = tt === 'monthly' ? (parseFloat(p.price||0)*12).toFixed(2) + ' / year' : (parseFloat(p.price||0)/12).toFixed(2) + ' / month';
        $('#viewPlanName').text(p.name || '');
        $('#viewPlanDescription').text(p.description || '');
        $('#viewPlanPrice').text(p.price != null ? parseFloat(p.price).toFixed(2) : '');
        $('#viewPlanUpi').text(p.upi || '');
        $('#viewPlanType').text(tt === 'yearly' ? 'Yearly' : 'Monthly');
        $('#viewPlanEq').text(eq);
        $('#viewPlanStart').text(p.start_date || '');
        $('#viewPlanEnd').text(p.end_date || '');
        $('#viewPlanAddedBy').text(p.added_by_username || '');
        $('#planViewModal').modal('show');
      },'json');
    });

    // delegated edit
    $(document).on('click', '.edit-plan', function(){
      var id = $(this).data('id');
      $.get('ajax/plan_api.php',{action:'get',id:id}, function(resp){
        if(!resp.success){ toastr.error(resp.message||'Not found'); return; }
        var p = resp.data || {};
        $('#planId').val(p.id || '');
        $('#planName').val(p.name || '');
        $('#planDescription').val(p.description || '');
        $('#planPrice').val(p.price != null ? p.price : '');
        $('#planUpi').val(p.upi || '');
        var chosenType = normalizeType(p.time_type);
        $('#planType').val(chosenType);
        if ($('#planType').val() !== chosenType){
          $('#planType option').each(function(){
            var opt = $(this);
            if(opt.text().toLowerCase().indexOf(chosenType.replace('ly','')) !== -1) opt.prop('selected',true);
          });
        }
        $('#planStart').val(p.start_date || '');
        $('#planEnd').val(p.end_date || '');
        $('#planForm').find('input,textarea,select').prop('disabled', false);
        $('#savePlanBtn').show();
        $('#planModalLabel').text('Edit Plan');
        $('#planModal').modal('show');
      },'json');
    });

    // delegated delete
    $(document).on('click', '.delete-plan', function(){
      if(!confirm('Delete this plan?')) return;
      var id = $(this).data('id');
      $.post('ajax/plan_api.php',{action:'delete',id:id}, function(resp){
        if(resp.success){ toastr.success(resp.message); loadPlans(); }
        else toastr.error(resp.message||'Delete failed');
      },'json');
    });

    // save (add/edit)
    $('#savePlanBtn').on('click', function(){
      var data = $('#planForm').serialize() + '&action=save';
      $.post('ajax/plan_api.php', data, function(resp){
        if(resp.success){
          toastr.success(resp.message);
          $('#planModal').modal('hide');
          loadPlans();
        } else {
          toastr.error(resp.message||'Save failed');
        }
      },'json');
    });
  });

})(jQuery);
