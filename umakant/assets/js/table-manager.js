/**
 * Table Manager
 * Handles DataTables initialization and management
 */

window.TableManager = {
    init: function(selector, options) {
        var defaultOptions = {
            responsive: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        };
        
        var finalOptions = $.extend({}, defaultOptions, options || {});
        
        if ($(selector).length) {
            return $(selector).DataTable(finalOptions);
        }
        
        return null;
    },
    
    refresh: function(table) {
        if (table && typeof table.ajax === 'object') {
            table.ajax.reload();
        }
    }
};