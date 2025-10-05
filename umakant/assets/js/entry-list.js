// entry-list.js - Handles test entry list functionality
$(document).ready(function() {
    initializeEntryList();
});

function initializeEntryList() {
    // Initialize DataTable
    const entriesTable = $('#entriesTable').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'pdf', 'print'],
        order: [[8, 'desc']], // Sort by date column descending
        language: {
            processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
        },
        ajax: {
            url: 'ajax/entry_api.php',
            type: 'GET',
            data: function(d) {
                d.action = 'list';
                // Add any additional filters
                return d;
            }
        },
        columns: [
            { data: 'id' },
            { data: 'patient_name' },
            { data: 'doctor_name' },
            { data: 'owner_name' },
            { data: 'tests' },
            { data: 'status' },
            { data: 'priority' },
            { data: 'amount' },
            { data: 'entry_date' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info" onclick="viewEntry(${row.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="editEntry(${row.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteEntry(${row.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Initialize Select2 for all select2 elements
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Load dropdowns data
    loadDropdowns();

    // Initialize form validation
    initializeFormValidation();
}

function loadDropdowns() {
    // Load patients
    $.get('ajax/patient_api.php', { action: 'list' })
        .done(function(response) {
            if (response.success && response.data) {
                populateSelect('#patient', response.data, 'Select Patient');
            }
        })
        .fail(function() {
            toastr.error('Failed to load patients list');
        });

    // Load doctors
    $.get('ajax/doctor_api.php', { action: 'list' })
        .done(function(response) {
            if (response.success && response.data) {
                populateSelect('#doctor', response.data, 'Select Doctor');
            }
        })
        .fail(function() {
            toastr.error('Failed to load doctors list');
        });
}

function populateSelect(selector, data, placeholder) {
    const $select = $(selector);
    $select.empty().append(`<option value="">${placeholder}</option>`);
    data.forEach(item => {
        $select.append(`<option value="${item.id}">${escapeHtml(item.name)}</option>`);
    });
    $select.trigger('change');
}

function initializeFormValidation() {
    $('#entryForm').on('submit', function(e) {
        e.preventDefault();
        if (this.checkValidity()) {
            submitEntryForm();
        }
        $(this).addClass('was-validated');
    });
}

function submitEntryForm() {
    const formData = new FormData($('#entryForm')[0]);
    
    $.ajax({
        url: 'ajax/entry_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                toastr.success('Entry saved successfully');
                $('#addEntryModal').modal('hide');
                $('#entriesTable').DataTable().ajax.reload();
            } else {
                toastr.error(response.message || 'Failed to save entry');
            }
        },
        error: function() {
            toastr.error('Failed to save entry. Please try again.');
        }
    });
}

// Utility Functions
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// CRUD Operations
function viewEntry(id) {
    // Implement view functionality
}

function editEntry(id) {
    // Implement edit functionality
}

function deleteEntry(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('ajax/entry_api.php', {
                action: 'delete',
                id: id
            })
            .done(function(response) {
                if (response.success) {
                    toastr.success('Entry deleted successfully');
                    $('#entriesTable').DataTable().ajax.reload();
                } else {
                    toastr.error(response.message || 'Failed to delete entry');
                }
            })
            .fail(function() {
                toastr.error('Failed to delete entry');
            });
        }
    });
}