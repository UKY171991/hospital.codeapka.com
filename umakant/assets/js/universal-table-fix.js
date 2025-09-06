// Universal Table Fix - Apply to all table pages
// This script ensures all DataTables are properly initialized with search functionality

$(document).ready(function() {
    // Global DataTable configuration
    $.extend(true, $.fn.dataTable.defaults, {
        processing: true,
        responsive: true,
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'csv',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'excel', 
                className: 'btn btn-primary btn-sm'
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                className: 'btn btn-info btn-sm'
            }
        ],
        language: {
            processing: '<div class="d-flex justify-content-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>',
            emptyTable: 'No data available in table',
            zeroRecords: 'No matching records found',
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries per page',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'No entries available',
            infoFiltered: '(filtered from _MAX_ total entries)',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }
        },
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                className: 'text-center',
                width: '40px'
            },
            {
                targets: -1,
                orderable: false,
                className: 'text-center',
                width: '120px'
            }
        ]
    });

    // Auto-fix common table issues
    fixTableIssues();
    
    // Initialize search functionality for all tables
    initializeUniversalSearch();
});

function fixTableIssues() {
    // Fix patient table if it exists
    if ($('#patientsTable').length > 0) {
        fixPatientTable();
    }
    
    // Fix doctor table if it exists  
    if ($('#doctorsTable').length > 0) {
        fixDoctorTable();
    }
    
    // Fix test table if it exists
    if ($('#testsTable').length > 0) {
        fixTestTable();
    }
    
    // Fix any other DataTables
    $('table[id*="Table"]').each(function() {
        const tableId = $(this).attr('id');
        if (!$.fn.DataTable.isDataTable('#' + tableId)) {
            fixGenericTable('#' + tableId);
        }
    });
}

function fixPatientTable() {
    if ($.fn.DataTable.isDataTable('#patientsTable')) {
        $('#patientsTable').DataTable().destroy();
    }
    
    try {
        $('#patientsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'ajax/patient_api.php',
                type: 'POST',
                data: function(d) {
                    d.action = 'list';
                    return d;
                },
                dataSrc: function(json) {
                    if (json.success) {
                        return json.data || [];
                    } else {
                        console.error('Patient API Error:', json.message);
                        toastr.error('Failed to load patients: ' + (json.message || 'Unknown error'));
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                    // Log more details to help diagnose 400/500 errors from server
                    console.error('Patient DataTable AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error,
                        thrown: thrown
                    });
                    toastr.error('Failed to load patient data (see console for details)');
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="patient-checkbox" value="${row.id}">`;
                    }
                },
                { data: 'uhid' },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<div><strong>${row.name || 'N/A'}</strong><br><small class="text-muted">${row.mobile || 'No mobile'}</small></div>`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `${row.mobile || 'N/A'}<br>${row.email || 'No email'}`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `${row.age || 'N/A'} ${row.age_unit || ''}<br><small>${row.gender || 'N/A'}</small>`;
                    }
                },
                { 
                    data: 'address',
                    render: function(data) {
                        return data ? (data.length > 30 ? data.substring(0, 30) + '...' : data) : 'N/A';
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : 'N/A';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return row.added_by_name || row.added_by || 'System';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-info btn-sm" onclick="viewPatient(${row.id})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="editPatient(${row.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deletePatient(${row.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
                        APP_LOG('Patient table initialized successfully');
    } catch (error) {
        console.error('Error initializing patient table:', error);
        toastr.error('Failed to initialize patient table');
    }
}

function fixDoctorTable() {
    if ($.fn.DataTable.isDataTable('#doctorsTable')) {
        $('#doctorsTable').DataTable().destroy();
    }
    
    try {
        $('#doctorsTable').DataTable({
            processing: true,
            ajax: {
                url: 'ajax/doctor_api.php?action=list',
                dataSrc: function(json) {
                    if (json.success) {
                        return json.data || [];
                    } else {
                        console.error('Doctor API Error:', json.message);
                        toastr.error('Failed to load doctors: ' + (json.message || 'Unknown error'));
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                    console.error('Doctor DataTable AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error,
                        thrown: thrown
                    });
                    toastr.error('Failed to load doctor data (see console for details)');
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="doctor-checkbox" value="${row.id}">`;
                    }
                },
                { data: 'id' },
                { data: 'name' },
                { data: 'qualification' },
                { data: 'specialization' },
                { data: 'hospital' },
                { data: 'contact_no' },
                { data: 'percent' },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-info btn-sm" onclick="viewDoctor(${row.id})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="editDoctor(${row.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteDoctor(${row.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
    APP_LOG('Doctor table initialized successfully');
    } catch (error) {
        console.error('Error initializing doctor table:', error);
        toastr.error('Failed to initialize doctor table');
    }
}

function fixTestTable() {
    if ($.fn.DataTable.isDataTable('#testsTable')) {
        $('#testsTable').DataTable().destroy();
    }
    
    try {
        $('#testsTable').DataTable({
            processing: true,
            ajax: {
                url: 'ajax/test_api.php?action=list',
                dataSrc: function(json) {
                    if (json.success) {
                        return json.data || [];
                    } else {
                        console.error('Test API Error:', json.message);
                        toastr.error('Failed to load tests: ' + (json.message || 'Unknown error'));
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                    console.error('Test DataTable AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error,
                        thrown: thrown
                    });
                    toastr.error('Failed to load test data (see console for details)');
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="test-checkbox" value="${row.id}">`;
                    }
                },
                { data: 'id' },
                { 
                    data: 'name',
                    render: function(data, type, row) {
                        return `<div class="font-weight-bold text-primary">${data || 'N/A'}</div>
                                ${row.description ? `<small class="text-muted">${row.description}</small>` : ''}`;
                    }
                },
                { 
                    data: 'category_name',
                    render: function(data) {
                        return data ? `<span class="badge badge-info">${data}</span>` : '-';
                    }
                },
                { 
                    data: 'price',
                    render: function(data) {
                        return data ? `₹${parseFloat(data).toFixed(2)}` : '-';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        let genders = [];
                        if (row.min_male !== null || row.max_male !== null) genders.push('<span class="badge badge-primary badge-sm">Male</span>');
                        if (row.min_female !== null || row.max_female !== null) genders.push('<span class="badge badge-danger badge-sm">Female</span>');
                        if (!genders.length && (row.min !== null || row.max !== null)) genders.push('<span class="badge badge-success badge-sm">Both</span>');
                        return genders.length > 0 ? genders.join(' ') : '-';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        let ranges = [];
                        if (row.min_male !== null || row.max_male !== null) {
                            ranges.push(`M: ${row.min_male || 0}-${row.max_male || '∞'}`);
                        }
                        if (row.min_female !== null || row.max_female !== null) {
                            ranges.push(`F: ${row.min_female || 0}-${row.max_female || '∞'}`);
                        }
                        if (!ranges.length && (row.min !== null || row.max !== null)) {
                            ranges.push(`${row.min || 0}-${row.max || '∞'}`);
                        }
                        return ranges.length > 0 ? ranges.join('<br>') : '-';
                    }
                },
                { 
                    data: 'unit',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-info btn-sm" onclick="viewTest(${row.id})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="editTest(${row.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteTest(${row.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
    APP_LOG('Test table initialized successfully');
    } catch (error) {
        console.error('Error initializing test table:', error);
        toastr.error('Failed to initialize test table');
    }
}

function fixGenericTable(tableSelector) {
    try {
        $(tableSelector).DataTable({
            processing: true,
            responsive: true,
            pageLength: 25
        });
    APP_LOG(`Generic table ${tableSelector} initialized successfully`);
    } catch (error) {
        console.error(`Error initializing generic table ${tableSelector}:`, error);
    }
}

function initializeUniversalSearch() {
    // Universal search functionality for all tables
    $(document).on('input', '[id*="Search"], .table-search-input, .dataTables_filter input', function() {
        const searchTerm = $(this).val();
        const tableId = $(this).closest('.card, .container, .content').find('table[id*="Table"]').attr('id');
        
        if (tableId && $.fn.DataTable.isDataTable('#' + tableId)) {
            $('#' + tableId).DataTable().search(searchTerm).draw();
        }
    });
    
    // Universal checkbox selection
    $(document).on('change', '#selectAll, [id*="selectAll"]', function() {
        const isChecked = $(this).is(':checked');
        const checkboxClass = $(this).closest('.card, .container, .content').find('input[type="checkbox"][class*="checkbox"]').attr('class');
        
        if (checkboxClass) {
            $('.' + checkboxClass.split(' ')[0]).prop('checked', isChecked);
        }
    });
    
    // Show success message when tables are properly loaded
    setTimeout(function() {
        const tablesCount = $('table[id*="Table"]').length;
        const initializedCount = $('table[id*="Table"]').filter(function() {
            return $.fn.DataTable.isDataTable(this);
        }).length;
        
        if (tablesCount > 0 && initializedCount > 0) {
            toastr.success(`${initializedCount} table(s) loaded successfully with search functionality`);
        }
    }, 2000);
}

// Global utility functions
function showToast(message, type = 'info') {
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
    APP_LOG(`${type.toUpperCase()}: ${message}`);
    }
}

// Export function
window.universalTableFix = {
    fixTableIssues,
    fixPatientTable,
    fixDoctorTable,
    fixTestTable,
    initializeUniversalSearch
};
