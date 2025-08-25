// Shared app JS: ajax setup, toastr defaults, confirm helper
$(function(){
  if(window.toastr){
    toastr.options = {
      closeButton: true,
      progressBar: true,
      positionClass: 'toast-top-right',
      timeOut: 3000
    };
  }
  $.ajaxSetup({cache:false});
});

function confirmAction(message, cb){
  if(confirm(message)) cb();
}
