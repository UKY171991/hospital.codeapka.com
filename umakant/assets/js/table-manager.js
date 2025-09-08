/**
 * Comprehensive Table Manager
 * Handles all DataTable initialization with proper error handling and search functionality
 * Prevents double initialization and column mismatch issues
 */

// Global variables to track initialized tables
window.initializedTables = window.initializedTables || new Set();

$(document).ready(function() {
    // Prevent double initialization
    if (window.tablesInitialized) {
        return;
    }
    window.tablesInitialized = true;
    
    // Initialize all tables after DOM is ready
    setTimeout(function() {
        initializeAllTables();
    }, 100);
});

function initializeAllTables() {
    APP_LOG('Initializing all tables...');
    
    // Check each table type and initialize only once
    if ($('#patientsTable').length > 0 && !window.initializedTables.has('patientsTable')) {
        initializePatientTable();
    }
    
    if ($('#doctorsTable').length > 0 && !window.initializedTables.has('doctorsTable')) {
        initializeDoctorTable();
    }
    
    if ($('#testsTable').length > 0 && !window.initializedTables.has('testsTable')) {
        initializeTestTable();
    }
    
    if ($('#usersTable').length > 0 && !window.initializedTables.has('usersTable')) {
        initializeUserTable();
    }
    
    if ($('#testCategoriesTable').length > 0 && !window.initializedTables.has('testCategoriesTable')) {
        initializeTestCategoryTable();
    }
    
    // Initialize any other table with specific class
    $('.data-table').each(function() {
        const tableId = $(this).attr('id');
        if (tableId && !window.initializedTables.has(tableId)) {
            initializeGenericTable(tableId);
        }
    });
}

function destroyTableIfExists(tableId) {
    if ($.fn.DataTable.isDataTable('#' + tableId)) {
        $('#' + tableId).DataTable().destroy();
        APP_LOG('Destroyed existing DataTable:', tableId);
    }
}

function markTableAsInitialized(tableId) {
    window.initializedTables.add(tableId);
    APP_LOG('Table initialized:', tableId);
}

function getCommonTableConfig() {
    return {
        processing: true,
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-secondary btn-sm',
                text: '<i class="fas fa-copy"></i> Copy'
            },
            {
                extend: 'csv',
                className: 'btn btn-success btn-sm',
                text: '<i class="fas fa-file-csv"></i> CSV'
            },
            {
                extend: 'excel',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fas fa-file-excel"></i> Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm',
                text: '<i class="fas fa-file-pdf"></i> PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-info btn-sm',
                text: '<i class="fas fa-print"></i> Print'
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
        error: function(xhr, error, thrown) {
            console.error('DataTable Error:', error, thrown);
            showToast('error', 'Failed to load data: ' + error);
        }
    };
}

function initializePatientTable() {
    APP_LOG('Initializing Patient Table...');
    destroyTableIfExists('patientsTable');
    
    try {
        const config = $.extend(true, {}, getCommonTableConfig(), {
            serverSide: true,
            ajax: {
                url: 'ajax/patient_api.php',
                type: 'POST',
                data: function(d) {
                    d.action = 'list';
                    return d;
                },
                dataSrc: function(json) {
                    // Support multiple response formats:
                    // 1) DataTables server-side: { draw, recordsTotal, recordsFiltered, data }
                    // 2) Legacy API: { success: true, data: [...] }
                    if (!json) return [];

                    // DataTables format
                    if (typeof json.draw !== 'undefined') {
                        return json.data || [];
                    }

                    // Legacy format
                    if (typeof json.success !== 'undefined') {
                        if (json.success) return json.data || [];
                        console.error('Patient API Error:', json.message);
                        if (json.message) {
                            showToast('error', 'Failed to load patients: ' + json.message);
                        } else {
                            console.warn('Patient API returned success=false without message.');
                        }
                        return [];
                    }

                    // Fallback: try to return data array if present
                    return json.data || [];
                },
                error: function(xhr, error, thrown) {
                    console.error('Patient AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error,
                        thrown: thrown
                    });
                    // Try to extract server JSON error message if present
                    var msg = xhr.responseText || 'Failed to load patient data';
                    try {
                        var j = JSON.parse(xhr.responseText || '{}');
                        if (j.message) msg = j.message;
                        else if (j.debug && j.debug.sql_executed) msg = j.message || ('Server returned error; see console for SQL');
                    } catch (e) {}
                    if (xhr.status === 0 && navigator.onLine) {
                        console.warn('Suppressed toast for Patient AJAX status 0 while online:', msg);
                    } else {
                        showToast('error', msg);
                    }
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    width: '40px',
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="patient-checkbox" value="${row.id}">`;
                    }
                },
                { data: 'uhid', title: 'UHID' },
                {
                    data: null,
                    title: 'Patient Details',
                    render: function(data, type, row) {
                        return `<div><strong>${row.name || 'N/A'}</strong><br><small class="text-muted">${row.mobile || 'No mobile'}</small></div>`;
                    }
                },
                {
                    data: null,
                    title: 'Contact',
                    render: function(data, type, row) {
                        return `${row.mobile || 'N/A'}<br><small>${row.email || 'No email'}</small>`;
                    }
                },
                {
                    data: null,
                    title: 'Age/Gender',
                    render: function(data, type, row) {
                        const age = row.age ? `${row.age} ${row.age_unit || 'years'}` : 'N/A';
                        return `${age}<br><span class="badge badge-${row.gender === 'Male' ? 'primary' : (row.gender === 'Female' ? 'pink' : 'secondary')}">${row.gender || 'N/A'}</span>`;
                    }
                },
                { data: 'address', title: 'Address' },
                {
                    data: 'created_at',
                    title: 'Registration',
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString() : 'N/A';
                    }
                },
                { data: 'added_by_name', title: 'Added By' },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    width: '120px',
                    title: 'Actions',
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
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
        
        $('#patientsTable').DataTable(config);
        markTableAsInitialized('patientsTable');
        
        // Load stats
        loadPatientStats();
        showToast('success', 'Patient table loaded successfully');
        
    } catch (error) {
        console.error('Error initializing patient table:', error);
        showToast('error', 'Error initializing patient table');
    }
}

function initializeUserTable() {
    APP_LOG('Initializing User Table...');
    destroyTableIfExists('usersTable');
    
    try {
        const config = $.extend(true, {}, getCommonTableConfig(), {
            serverSide: true,
            ajax: {
                url: 'ajax/user_api.php',
                type: 'POST',
                data: function(d) {
                    d.action = 'list';
                    return d;
                },
                dataSrc: function(json) {
                    if (json.success) {
                        return json.data || [];
                    } else {
                        console.error('User API Error:', json.message);
                        showToast('error', 'Failed to load users: ' + (json.message || 'Unknown error'));
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                    console.error('User AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error,
                        thrown: thrown
                    });
                    showToast('error', 'Failed to load user data (see console)');
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    width: '40px',
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="user-checkbox" value="${row.id}">`;
                    }
                },
                { data: 'id', title: 'ID' },
                { data: 'username', title: 'Username' },
                { data: 'email', title: 'Email' },
                { data: 'full_name', title: 'Full Name' },
                { 
                    data: 'role', 
                    title: 'Role',
                    render: function(data, type, row) {
                        const badgeClass = data === 'admin' ? 'badge-danger' : (data === 'master' ? 'badge-success' : 'badge-info');
                        return `<span class="badge ${badgeClass}">${data}</span>`;
                    }
                },
                { data: 'expire_date', title: 'Expire Date' },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    width: '120px',
                    title: 'Actions',
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm" onclick="viewUser(${row.id})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="editUser(${row.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteUser(${row.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
        
        $('#usersTable').DataTable(config);
        markTableAsInitialized('usersTable');
        showToast('success', 'User table loaded successfully');
        
    } catch (error) {
        console.error('Error initializing user table:', error);
        showToast('error', 'Error initializing user table');
    }
}

function initializeTestTable() {
    APP_LOG('Initializing Test Table...');
    destroyTableIfExists('testsTable');
    
    try {
        const config = $.extend(true, {}, getCommonTableConfig(), {
            serverSide: true,
            ajax: {
                url: 'ajax/test_api.php',
                type: 'POST',
                data: function(d) {
                    d.action = 'list';
                    return d;
                },
                dataSrc: function(json) {
                    if (json.success) {
                        return json.data || [];
                    } else {
                        console.error('Test API Error:', json.message);
                        showToast('error', 'Failed to load tests: ' + (json.message || 'Unknown error'));
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                        console.error('Test AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error,
                            thrown: thrown
                        });
                        showToast('error', 'Failed to load test data (see console)');
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    width: '40px',
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="test-checkbox" value="${row.id}">`;
                    }
                },
                { data: 'id', title: 'ID' },
                { data: 'test_name', title: 'Test Name' },
                { data: 'category', title: 'Category' },
                { data: 'price', title: 'Price' },
                { data: 'gender', title: 'Gender' },
                { data: 'range', title: 'Range' },
                { data: 'unit', title: 'Unit' },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    width: '120px',
                    title: 'Actions',
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
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
        
        $('#testsTable').DataTable(config);
        markTableAsInitialized('testsTable');
        showToast('success', 'Test table loaded successfully');
        
    } catch (error) {
        console.error('Error initializing test table:', error);
        showToast('error', 'Error initializing test table');
    }
}

function initializeDoctorTable() {
    APP_LOG('Initializing Doctor Table...');
    destroyTableIfExists('doctorsTable');
    
    try {
        const config = $.extend(true, {}, getCommonTableConfig(), {
            serverSide: true,
            ajax: {
                url: 'ajax/doctor_api.php',
                type: 'POST',
                data: function(d) {
                    d.action = 'list';
                    return d;
                },
                dataSrc: function(json) {
                    if (json.success) {
                        return json.data || [];
                    } else {
                        console.error('Doctor API Error:', json.message);
                        showToast('error', 'Failed to load doctors: ' + (json.message || 'Unknown error'));
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                        console.error('Doctor AJAX Error:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            error: error,
                            thrown: thrown
                        });
                        showToast('error', 'Failed to load doctor data (see console)');
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    width: '40px',
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="doctor-checkbox" value="${row.id}">`;
                    }
                },
                { data: 'id', title: 'ID' },
                { data: 'name', title: 'Doctor Name' },
                { data: 'specialization', title: 'Specialization' },
                { data: 'phone', title: 'Phone' },
                { data: 'email', title: 'Email' },
                { data: 'hospital', title: 'Hospital' },
                {
                    data: 'status',
                    title: 'Status',
                    render: function(data, type, row) {
                        return `<span class="badge badge-${data === 'Active' ? 'success' : 'secondary'}">${data}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    width: '120px',
                    title: 'Actions',
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
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
        
        $('#doctorsTable').DataTable(config);
        markTableAsInitialized('doctorsTable');
        showToast('success', 'Doctor table loaded successfully');
        
    } catch (error) {
        console.error('Error initializing doctor table:', error);
        showToast('error', 'Error initializing doctor table');
    }
}

function initializeTestCategoryTable() {
    APP_LOG('Initializing Test Category Table...');
    destroyTableIfExists('testCategoriesTable');
    
    try {
        const config = $.extend(true, {}, getCommonTableConfig(), {
            ajax: {
                url: 'ajax/test_category_api.php',
                type: 'POST',
                data: function(d) {
                    d.action = 'list';
                    return d;
                },
                dataSrc: function(json) {
                    if (json.success) {
                        return json.data || [];
                    } else {
                        console.error('Test Category API Error:', json.message);
                        showToast('error', 'Failed to load test categories: ' + (json.message || 'Unknown error'));
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                    console.error('Test Category AJAX Error:', error, thrown);
                    showToast('error', 'Failed to load test category data');
                }
            },
            columns: [
                { data: 'sno', title: 'S.No.' },
                { data: 'id', title: 'ID' },
                { data: 'name', title: 'Name' },
                { data: 'description', title: 'Description' },
                { data: 'test_count', title: 'Test Count' },
                { data: 'added_by', title: 'Added by' },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    width: '120px',
                    title: 'Actions',
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-warning btn-sm" onclick="editCategory(${row.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteCategory(${row.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
        
        $('#testCategoriesTable').DataTable(config);
        markTableAsInitialized('testCategoriesTable');
        showToast('success', 'Test category table loaded successfully');
        
    } catch (error) {
        console.error('Error initializing test category table:', error);
        showToast('error', 'Error initializing test category table');
    }
}

function initializeGenericTable(tableId) {
    APP_LOG('Initializing Generic Table:', tableId);
    destroyTableIfExists(tableId);
    
    try {
        const config = getCommonTableConfig();
        $('#' + tableId).DataTable(config);
        markTableAsInitialized(tableId);
        showToast('success', `Table ${tableId} loaded successfully`);
        
    } catch (error) {
        console.error('Error initializing generic table:', tableId, error);
        showToast('error', `Error initializing ${tableId}`);
    }
}

function loadPatientStats() {
    $.ajax({
        url: 'ajax/patient_api.php',
        type: 'POST',
        data: { action: 'stats' },
        success: function(response) {
            if (response.success) {
                $('#totalPatients').text(response.data.total || 0);
                $('#todayPatients').text(response.data.today || 0);
                $('#malePatients').text(response.data.male || 0);
                $('#femalePatients').text(response.data.female || 0);
            }
        },
        error: function() {
            console.error('Failed to load patient statistics');
        }
    });
}

function showToast(type, message) {
    if (typeof toastr !== 'undefined') {
        toastr[type](message);
    } else {
    APP_LOG(`${type.toUpperCase()}: ${message}`);
    }
}

// Export functions for global use
window.initializeAllTables = initializeAllTables;
window.loadPatientStats = loadPatientStats;
