// assets/js/common.js
// Global DataTable initializer for all pages
// Requires jQuery and DataTables library to be loaded on the page

// Utility: escape HTML to safely insert untrusted strings into DOM
// Used by pages like `test.php` when rendering details into modals.
function escapeHtml(input) {
    if (input === null || input === undefined) return '';
    return String(input)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function initDataTable(selector, options) {
    if (!window.jQuery || !$.fn.DataTable) {
        console.error('DataTables library is not loaded.');
        return;
    }
    
    // Check if table has content
    var hasData = $(selector + ' tbody tr').length > 0 && 
                  !$(selector + ' tbody tr:first td').attr('colspan');
    
    // If table is already a DataTable, destroy it first to allow re-init
    try{
        if ($.fn.dataTable.isDataTable(selector)){
            try { $(selector).DataTable().clear().destroy(); } catch(e){ console.warn('failed to destroy existing DataTable', e); }
        }
    }catch(e){}

    var defaultOptions = {
        paging: hasData,
        searching: hasData,
        ordering: hasData,
        info: hasData,
        autoWidth: false,
        responsive: true,
        lengthMenu: [10, 25, 50, 100],
        // make last column (actions) non-orderable by default
        columnDefs: [ 
            { orderable: false, targets: -1 },
            { 
                targets: 0, // First column (S.No.)
                orderable: false,
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Row number starting from 1
                }
            }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries to show",
            zeroRecords: "No matching records found",
            emptyTable: "No data available in table"
        }
    };
    var dtOptions = $.extend(true, {}, defaultOptions, options || {});
    $(selector).DataTable(dtOptions);
}
