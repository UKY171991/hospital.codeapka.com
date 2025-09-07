<?php
// inc/header.php - simple header with assets (Bootstrap + jQuery + Toastr)
?>
<!doctype html>
<html lang="en">
<head>
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital Admin</title>
  <!-- Favicon to avoid 404 in browser console -->
  <link rel="icon" href="/umakant/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="/umakant/favicon.ico" type="image/x-icon">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <!-- Global Improvements CSS -->
  <link rel="stylesheet" href="assets/css/global-improvements.css">
  <!-- Enhanced Tables CSS -->
  <link rel="stylesheet" href="assets/css/enhanced-tables.css">
  <!-- Comprehensive Table Styling -->
  <link rel="stylesheet" href="assets/css/comprehensive-tables.css?v=<?php echo time(); ?>">
  <style>body{background:#f4f6f9}</style>
</head>
<?php if(session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php include_once __DIR__ . '/auth.php'; ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="#">Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php" id="topLogout" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  <!-- Ensure jQuery is available early for inline scripts that run before footer -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    // Global debug flag - set to true to enable verbose logging
    window.APP_DEBUG = false;
    window.APP_LOG = function(){ if(window.APP_DEBUG && console && console.log){ console.log.apply(console, arguments); } };
  </script>
  <script>
    // Quiet known noisy extension message in console which is outside our control.
    // This filters console.error calls containing the specific runtime.lastError text
    (function(){
      try{
        if(console && console.error){
          var _origErr = console.error.bind(console);
          console.error = function(){
            try{
              var msg = Array.prototype.slice.call(arguments).join(' ');
              if(msg){
                var lower = msg.toLowerCase();
                var noisy = [
                  'the message port closed before a response was received',
                  'could not establish connection',
                  'messagenotsenterror',
                  'cookiemanager.injectclientscript',
                  'registerclientlocalizationserror'
                ];
                for(var i=0;i<noisy.length;i++){
                  if(lower.indexOf(noisy[i]) !== -1){
                    // ignore this noisy extension error
                    return;
                  }
                }
              }
            }catch(e){ }
            _origErr.apply(console, arguments);
          };
        }
      }catch(e){}
    })();
  </script>
  <script>
    // Suppress noisy unhandled promise rejection messages originating from extensions
    window.addEventListener('unhandledrejection', function(ev){
      try{
        var reason = ev && ev.reason;
        if(!reason) return;
        var msg = (reason && (reason.message || reason.toString && reason.toString())) || '';
        if(!msg) return;
        var m = msg.toLowerCase();
        var noisy = ['the message port closed before a response was received', 'could not establish connection', 'messagenotsenterror', 'registerclientlocalizationserror', 'cookiemanager.injectclientscript'];
        for(var i=0;i<noisy.length;i++){
          if(m.indexOf(noisy[i]) !== -1){
            ev.preventDefault(); // stop default logging
            return;
          }
        }
      }catch(e){ }
    }, true);
  </script>
  <script>
    // Defensive stubs: some browser extensions or injected scripts expect global
    // localization objects like `ClientLocalizations`/`clientLocalizations` with
    // a `translations` property. When absent they can throw and pollute the
    // console. Create minimal safe stubs to prevent "Cannot read properties of
    // undefined (reading 'translations')" errors originating outside our app.
    (function(){
      try{
        if(typeof window.ClientLocalizations === 'undefined'){
          window.ClientLocalizations = { translations: {} };
        }
        if(typeof window.clientLocalizations === 'undefined'){
          window.clientLocalizations = { translations: {} };
        }
        if(typeof window.CLIENT_LOCALIZATIONS === 'undefined'){
          window.CLIENT_LOCALIZATIONS = { translations: {} };
        }
      }catch(e){ /* noop */ }
    })();
  </script>
  <script>
    // Confirm logout click (small UX safeguard)
    document.addEventListener('DOMContentLoaded', function(){
      var el = document.getElementById('topLogout');
      if(!el) return;
      el.addEventListener('click', function(e){
        if(!confirm('Are you sure you want to log out?')){
          e.preventDefault();
        }
      });
    });
  </script>
