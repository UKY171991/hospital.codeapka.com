// assets/js/common.js
// Global DataTable initializer for all pages
// Requires jQuery and DataTables library to be loaded on the page

function initDataTable(selector, options) {
    if (!window.jQuery || !$.fn.DataTable) {
        console.error('DataTables library is not loaded.');
        return;
    }
    var defaultOptions = {
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        responsive: true,
        lengthMenu: [10, 25, 50, 100],
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
