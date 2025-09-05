// Small helper for password show/hide and minor UX enhancements
document.addEventListener('DOMContentLoaded', function(){
    var toggle = document.querySelector('.toggle-pass');
    if(!toggle) return;
    var input = document.getElementById('password');
    var icon = toggle.querySelector('span');
    toggle.addEventListener('click', function(e){
        e.preventDefault();
        if (input.type === 'password'){
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            toggle.setAttribute('aria-label','Hide password');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            toggle.setAttribute('aria-label','Show password');
        }
    });
    // subtle animation for inputs
    var inputs = document.querySelectorAll('.form-control');
    inputs.forEach(function(i){
        i.addEventListener('focus', function(){ this.style.boxShadow='0 6px 18px rgba(60,141,188,0.08)'; });
        i.addEventListener('blur', function(){ this.style.boxShadow='none'; });
    });
});
