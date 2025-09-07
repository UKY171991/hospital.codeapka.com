$(function(){
  // Nothing heavy here — keep page-specific behavior in this file if needed.
  // The DataTable initialization is kept inline in the PHP file because it
  // references server-rendered variables and modal IDs directly. If you want
  // I can fully move the initialization as well and use a small init function.

  // Example helper: ensure buttons in action column don't collapse when table is redrawn.
  $('#doctorTable').on('draw.dt', function(){
    // No-op for now; placeholder for page-specific JS hooks.
  });
});
