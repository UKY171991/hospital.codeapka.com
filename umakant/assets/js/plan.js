// assets/js/plan.js
(function($){
  // Helper: normalize time_type to 'monthly' or 'yearly'
  function normalizeType(val){
    var tt = (val||'monthly').toString().toLowerCase();
    if (tt.indexOf('year') !== -1) return 'yearly';
    return 'monthly';
  }

  // Helper: parse price string into a safe number (remove commas/currency)
  function parsePrice(v){
    if (v === null || v === undefined || v === '') return 0;
    var s = v.toString();
    // remove any non-digit except dot and minus
    s = s.replace(/[^0-9.\-]+/g, '');
    var n = parseFloat(s);
    return isNaN(n) ? 0 : n;
  }

  function loadPlans(){
    $.get('ajax/plan_api.php',{action:'list'},function(resp){
      if(!resp.success){ toastr.error(resp.message||'Failed to load plans'); return; }

      var rows = resp.data || [];
      var t = '';
      // expected column count from thead
      var thCount = $('#plansTable thead th').length || 0;
      rows.forEach(function(p, idx){
        var tt = normalizeType(p.time_type);
        var priceNum = parsePrice(p.price);
        var cells = [];
        cells.push('<td>'+(idx+1)+'</td>');
        cells.push('<td>'+ (p.id||'') +'</td>');
        cells.push('<td>'+ (p.name||'') +'</td>');
        cells.push('<td>'+ (p.price!=null?priceNum.toFixed(2):'') +'</td>');
        cells.push('<td>'+ (p.upi||'') +'</td>');
        cells.push('<td>'+ (tt==='yearly' ? 'Yearly' : 'Monthly') +'</td>');
        cells.push('<td>'+ (p.added_by_username||'') +'</td>');
        cells.push('<td>'+
               '<button class="btn btn-sm btn-info view-plan" data-id="'+p.id+'">View</button> '+
               '<button class="btn btn-sm btn-warning edit-plan" data-id="'+p.id+'">Edit</button> '+
               '<button class="btn btn-sm btn-danger delete-plan" data-id="'+p.id+'">Delete</button>'+
             '</td>');
        // pad cells to match header count
        while(cells.length < thCount) cells.push('<td></td>');
        // if there are more cells than headers, slice to header count
        if (cells.length > thCount) cells = cells.slice(0, thCount);
        t += '<tr>' + cells.join('') + '</tr>';
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
  var priceNum = parsePrice(p.price);
  $('#viewPlanName').text(p.name || '');
  $('#viewPlanDescription').text(p.description || '');
  $('#viewPlanPrice').text(p.price != null ? priceNum.toFixed(2) : '');
  $('#viewPlanUpi').text(p.upi || '');
  $('#viewPlanType').text(tt === 'yearly' ? 'Yearly' : 'Monthly');
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
        if (p.qr_code) {
          $('#existingQr').html('<a href="' + p.qr_code + '" target="_blank">View QR</a>');
        } else {
          $('#existingQr').text('(none)');
        }
        var chosenType = normalizeType(p.time_type);
        $('#planType').val(chosenType);
        if ($('#planType').val() !== chosenType){
          $('#planType option').each(function(){
            var opt = $(this);
            if(opt.text().toLowerCase().indexOf(chosenType.replace('ly','')) !== -1) opt.prop('selected',true);
          });
        }
  // start/end inputs removed from form
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
      // use FormData to allow file upload (qr_code)
      var form = document.getElementById('planForm');
      var fd = new FormData(form);
      fd.append('action','save');

      // disable button to prevent double submits
      var btn = this; btn.disabled = true;

      fetch('ajax/plan_api.php', { method: 'POST', body: fd }).then(function(r){ return r.json(); }).then(function(resp){
        if(resp.success){
          toastr.success(resp.message);
          $('#planModal').modal('hide');
          loadPlans();
        } else {
          toastr.error(resp.message||'Save failed');
        }
      }).catch(function(){ toastr.error('Network error'); }).finally(function(){ btn.disabled = false; });
    });
  });

})(jQuery);
