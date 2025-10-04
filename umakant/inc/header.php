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
  <!-- Select2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
  <!-- Global Improvements CSS -->
  
  <!-- Enhanced Tables CSS -->
  
  <!-- Comprehensive Table Styling -->
  
  <!-- Modal Layout Fixes -->
  
  <!-- AdminLTE Modal Compatibility -->
  
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
      var PATTERNS = [
        'the message port closed before a response was received',
        'unchecked runtime.lasterror',
        'message port closed',
        'could not establish connection',
        'messagenotsenterror',
        'messageportsenderror',
        'cookiemanager.injectclientscript',
        'registerclientlocalizationserror',
        'contentmanager.injectclientscript',
        'extension context invalidated'
      ];

      function collectStrings(args){
        var values = [];
        try{
          var arr = Array.prototype.slice.call(args || []);
          for(var i=0; i<arr.length; i++){
            var item = arr[i];
            if(item == null){ continue; }
            if(typeof item === 'string'){
              values.push(item);
            } else if(typeof item === 'object'){
              if(typeof item.message === 'string') values.push(item.message);
              if(typeof item.name === 'string') values.push(item.name);
              if(typeof item.stack === 'string') values.push(item.stack);
              try{
                values.push(JSON.stringify(item));
              }catch(jsonErr){ /* ignore */ }
            } else {
              values.push(String(item));
            }
          }
        }catch(collectErr){ /* ignore */ }
        return values;
      }

      function containsNoise(strings){
        for(var sIdx=0; sIdx<strings.length; sIdx++){
          var str = strings[sIdx];
          if(!str){ continue; }
          var lower = String(str).toLowerCase();
          for(var i=0; i<PATTERNS.length; i++){
            if(lower.indexOf(PATTERNS[i]) !== -1){
              return true;
            }
          }
        }
        return false;
      }

      function wrapConsoleMethod(method){
        if(!console || typeof console[method] !== 'function') return;
        var original = console[method].bind(console);
        console[method] = function(){
          var args = Array.prototype.slice.call(arguments);
          var toCheck = collectStrings(args);
          if(containsNoise(toCheck)){
            return;
          }
          try{
            original.apply(console, args);
          }catch(err){ /* swallow */ }
        };
      }

      wrapConsoleMethod('error');
      wrapConsoleMethod('warn');
      wrapConsoleMethod('info');

      window.addEventListener('error', function(ev){
        try{
          var toCheck = [];
          if(ev && typeof ev.message === 'string') {
            toCheck.push(ev.message);
          }
          if(ev && ev.error){
            toCheck = toCheck.concat(collectStrings([ev.error]));
          }
          if(containsNoise(toCheck)){
            ev.preventDefault();
            if(typeof ev.stopImmediatePropagation === 'function'){
              ev.stopImmediatePropagation();
            }
            return false;
          }
        }catch(handlerErr){ /* ignore */ }
      }, true);

      window.addEventListener('unhandledrejection', function(ev){
        try{
          var reason = ev && ev.reason;
          var toCheck = collectStrings([reason]);
          if(containsNoise(toCheck)){
            ev.preventDefault();
            if(typeof ev.stopImmediatePropagation === 'function'){
              ev.stopImmediatePropagation();
            }
            return;
          }
        }catch(e){ /* ignore */ }
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
        if(typeof window.registerClientLocalizations !== 'function'){
          window.registerClientLocalizations = function(){
            return Promise.resolve(window.ClientLocalizations);
          };
        }
        if(typeof window.ClientLocalizationManager === 'undefined'){
          window.ClientLocalizationManager = {
            injectClientScript: function(){ return Promise.resolve(); }
          };
        } else if(typeof window.ClientLocalizationManager.injectClientScript !== 'function'){
          window.ClientLocalizationManager.injectClientScript = function(){ return Promise.resolve(); };
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
