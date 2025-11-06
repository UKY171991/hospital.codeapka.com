/**
 * Minimal Entry List Management - Clean version to fix console issues
 */

// Global variables
let entriesTable = null;
let testsData = [];
let categoriesData = [];
let patientsData = [];
let doctorsData = [];

/**
 * Initialize DataTable with minimal configuration
 */
function initializeDataTable() {
    console.log('Initializing minimal DataTable...');

    try {
        // Destroy existing table if it exists
        if ($.fn.DataTable.isDataTable('#entriesTable')) {
            $('#entriesTable').DataTable().destroy();
        }

        entriesTable = $('#entriesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: 'patho_api/entry.php',
                type: 'GET',
                data: { action: 'list' },
                dataSrc: function (json) {
                    console.log('DataTable response:', json);
                    if (json && json.success && json.data) {
                        return json.data;
                    } else if (json && Array.isArray(json)) {
                        return json;
                    } else {
                        console.error('Invalid response format:', json);
                        showError('Failed to load entries');
                        return [];
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error('DataTable AJAX error:', error, thrown);
                    console.error('Response:', xhr.responseText);
                    showError('Failed to load entries. Please refresh the page.');
                }
            },
            columns: [
                { data: 'id', title: 'ID', defaultContent: 'N/A' },
                { data: 'patient_name', title: 'Patient', defaultContent: 'N/A' },
                { data: 'doctor_name', title: 'Doctor', defaultContent: 'Not assigned' },
                { data: 'test_names', title: 'Tests', defaultContent: 'No tests' },
                { 
                    data: 'status', 
                    title: 'Status', 
                    defaultContent: 'pending',
                    render: function(data) {
                        const status = data || 'pending';
                        const badgeClass = status === 'completed' ? 'success' : status === 'cancelled' ? 'danger' : 'warning';
                        return `<span class="badge badge-${badgeClass}">${status}</span>`;
                    }
                },
                { 
                    data: 'priority', 
                    title: 'Priority', 
                    defaultContent: 'normal',
                    render: function(data) {
                        const priority = data || 'normal';
                        return `<span class="badge badge-info">${priority}</span>`;
                    }
                },
                { 
                    data: 'total_price', 
                    title: 'Amount', 
                    defaultContent: '0.00',
                    render: function(data) {
                        const amount = parseFloat(data) || 0;
                        return `₹${amount.toFixed(2)}`;
                    }
                },
                { 
                    data: 'entry_date', 
                    title: 'Date', 
                    defaultContent: 'N/A',
                    render: function(data) {
                        if (data) {
                            try {
                                return new Date(data).toLocaleDateString('en-IN');
                            } catch (e) {
                                return data;
                            }
                        }
                        return 'N/A';
                    }
                },
                {
                    data: null,
                    title: 'Actions',
                    orderable: false,
                    render: function (data, type, row) {
                        if (row && row.id) {
                            return `
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info btn-sm" onclick="viewEntry(${row.id})" title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm" onclick="editEntry(${row.id})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteEntry(${row.id})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                        return '<span class="text-muted">No actions</span>';
                    }
                }
            ],
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true,
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading entries...',
                emptyTable: 'No entries found. Click "Add Entry" to create your first entry.',
                zeroRecords: 'No matching entries found.',
                loadingRecords: 'Loading...'
            }
        });

        console.log('DataTable initialized successfully');
        return true;
    } catch (error) {
        console.error('Failed to initialize DataTable:', error);
        showError('Failed to initialize data table. Please refresh the page.');
        return false;
    }
}

/**
 * Simple message functions
 */
function showError(message) {
    if (typeof toastr !== 'undefined') {
        toastr.error(message);
    } else {
        alert('Error: ' + message);
    }
}

function showSuccess(message) {
    if (typeof toastr !== 'undefined') {
        toastr.success(message);
    } else {
        alert(message);
    }
}

function showInfo(message) {
    if (typeof toastr !== 'undefined') {
        toastr.info(message);
    } else {
        alert(message);
    }
}

/**
 * Placeholder functions for buttons
 */
function openAddModal() {
    alert('Add Entry feature is being loaded. Please refresh the page and try again.');
}

function refreshTable() {
    if (entriesTable) {
        entriesTable.ajax.reload();
        showSuccess('Table refreshed');
    }
}

function exportEntries() {
    alert('Export feature is being loaded. Please refresh the page and try again.');
}

function refreshTestAggregates() {
    alert('Refresh Test Aggregates feature is being loaded. Please refresh the page and try again.');
}

function diagnoseTestData() {
    alert('Diagnose Test Data feature is being loaded. Please refresh the page and try again.');
}

function addTestColumns() {
    alert('Add Test Columns feature is being loaded. Please refresh the page and try again.');
}

function viewEntry(id) {
    alert(`View Entry ${id} feature is being loaded. Please refresh the page and try again.`);
}

function editEntry(id) {
    alert(`Edit Entry ${id} feature is being loaded. Please refresh the page and try again.`);
}

function deleteEntry(id) {
    if (confirm(`Are you sure you want to delete entry ${id}?`)) {
        alert(`Delete Entry ${id} feature is being loaded. Please refresh the page and try again.`);
    }
}

/**
 * Initialize when document is ready
 */
$(document).ready(function () {
    console.log('Minimal Entry List Management - Initializing...');

    try {
        // Initialize DataTable
        initializeDataTable();

        // Bind basic filter events
        $('#statusFilter, #dateFilter').on('change', function () {
            if (entriesTable) {
                entriesTable.draw();
            }
        });

        $('#patientFilter, #doctorFilter').on('keyup', function () {
            if (entriesTable) {
                const searchTerm = $(this).val();
                entriesTable.search(searchTerm).draw();
            }
        });

        console.log('Minimal initialization completed successfully');

    } catch (error) {
        console.error('Error during minimal initialization:', error);
        showError('Failed to initialize the page. Please refresh and try again.');
    }
});

// Make functions available globally
window.initializeDataTable = initializeDataTable;
window.refreshTable = refreshTable;
window.openAddModal = openAddModal;
window.exportEntries = exportEntries;
window.refreshTestAggregates = refreshTestAggregates;
window.diagnoseTestData = diagnoseTestData;
window.addTestColumns = addTestColumns;
window.viewEntry = viewEntry;
window.editEntry = editEntry;
window.deleteEntry = deleteEntry;

console.log('Minimal Entry List Management loaded successfully');