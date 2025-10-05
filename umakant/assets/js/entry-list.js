// entry-list.js - Handles test entry list functionality
(function($) {
    'use strict';
    
    // Ensure HMS namespace exists
    window.HMS = window.HMS || {};
    
    // EntryList module
    HMS.entryList = {
        // Module state
        table: null,
        filters: {},
        
        // Initialize the module
        init: function() {
            this.initializeDataTable();
            this.initializeSelect2();
            this.loadDropdowns();
            this.initializeFormValidation();
            this.setupEventListeners();
        },
        
        initializeSelect2: function() {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        },
    
    initializeDataTable: function() {
        // Ensure the table element exists
        const tableElement = $('#entriesTable');
        if (!tableElement.length) {
            console.error('Entries table element not found');
            return;
        }

        // Initialize DataTable with Bootstrap 4 styling
        this.table = tableElement.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'print'],
            order: [[8, 'desc']], // Sort by date column descending
            language: {
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            ajax: {
                url: 'ajax/entry_api.php',
                type: 'POST',
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

        loadDropdowns: function() {
            var self = this;
            
            // Load patients
            $.get('ajax/patient_api.php', { action: 'list' })
                .done(function(response) {
                    if (response.success && response.data) {
                        self.populateSelect('#patient', response.data, 'Select Patient');
                    }
                })
                .fail(function() {
                    HMS.utils.showError('Failed to load patients list');
                });

            // Load doctors
            $.get('ajax/doctor_api.php', { action: 'list' })
                .done(function(response) {
                    if (response.success && response.data) {
                        self.populateSelect('#doctor', response.data, 'Select Doctor');
                    }
                })
                .fail(function() {
                    HMS.utils.showError('Failed to load doctors list');
                });
}

        populateSelect: function(selector, data, placeholder) {
            const $select = $(selector);
            $select.empty().append(`<option value="">${placeholder}</option>`);
            data.forEach(item => {
                $select.append(`<option value="${item.id}">${HMS.utils.escapeHtml(item.name)}</option>`);
            });
            $select.trigger('change');
        },

        initializeFormValidation: function() {
            var self = this;
            $('#entryForm').on('submit', function(e) {
                e.preventDefault();
                if (this.checkValidity()) {
                    self.submitEntryForm();
                }
                $(this).addClass('was-validated');
            });
}

        submitEntryForm: function() {
            var self = this;
            const formData = new FormData($('#entryForm')[0]);
            
            $.ajax({
                url: 'ajax/entry_api.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        HMS.utils.showSuccess('Entry saved successfully');
                        $('#addEntryModal').modal('hide');
                        self.table.ajax.reload();
                    } else {
                        HMS.utils.showError(response.message || 'Failed to save entry');
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
        viewEntry: function(id) {
            // Implement view functionality
            // TODO: Add view implementation
        },

        editEntry: function(id) {
            // Implement edit functionality
            // TODO: Add edit implementation
        },

        deleteEntry: function(id) {
            var self = this;
            
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
                            HMS.utils.showSuccess('Entry deleted successfully');
                            self.table.ajax.reload();
                        } else {
                            HMS.utils.showError(response.message || 'Failed to delete entry');
                        }
                    })
                    .fail(function() {
                        HMS.utils.showError('Failed to delete entry');
                    });
                }
            });
        },
        
        setupEventListeners: function() {
            var self = this;
            
            // Filter change handlers
            $('#testCategoryFilter').on('change', function() {
                self.filters.category_id = $(this).val();
                self.table.ajax.reload();
            });
        }
    }; // End of HMS.entryList object

    // Initialize when document is ready
    $(document).ready(function() {
        HMS.entryList.init();
    });

})(jQuery);
            });
        }
    });
}