// assets/js/common.js
// Global DataTable initializer for all pages
// Requires jQuery and DataTables library to be loaded on the page

function initDataTable(selector, options) {
    if (!window.jQuery || !$.fn.DataTable) {
        console.error('DataTables library is not loaded.');
        return;
    }
    // If table is already a DataTable, destroy it first to allow re-init
    try{
        if ($.fn.dataTable.isDataTable(selector)){
            try { $(selector).DataTable().clear().destroy(); } catch(e){ console.warn('failed to destroy existing DataTable', e); }
        }
    }catch(e){}

    var defaultOptions = {
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        lengthMenu: [10, 25, 50, 100],
        // make last column (actions) non-orderable by default
        columnDefs: [ { orderable: false, targets: -1 } ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries to show",
            zeroRecords: "No matching records found"
        }
    };
    var dtOptions = $.extend(true, {}, defaultOptions, options || {});
    $(selector).DataTable(dtOptions);
}
