// entry-list.js - Handles test entry list functionality
(function($) {
    'use strict';

    // Ensure HMS namespace exists
    window.HMS = window.HMS || {};
    
    // EntryList module
    HMS.entryList = {
        // Module state
        state: {
            isLoading: false,
            table: null,
            filters: {}
        },

        // Initialize module
        init: function() {
            if (!HMS.utils) {
                console.error('HMS.utils is required but not loaded');
                return;
            }
            
            this.initializeDataTable();
            this.initializeSelect2();
            this.loadDropdowns();
            this.initializeFormValidation();
        },
        
        // Initialize DataTable
        initializeDataTable: function() {
            const self = this;
            const tableElement = $('#entriesTable');
            
            if (!tableElement.length) {
                console.error('Entries table element not found');
                return;
            }

            try {
                this.state.table = tableElement.DataTable({
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
                        url: HMS.config.baseApiUrl + 'entry_api.php',
                        type: 'GET',
                        data: function(d) {
                            d.action = 'list';
                            return d;
                        },
                        beforeSend: function() {
                            self.state.isLoading = true;
                        },
                        complete: function() {
                            self.state.isLoading = false;
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
                                        <button type="button" class="btn btn-sm btn-info" onclick="HMS.entryList.viewEntry(${row.id})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="HMS.entryList.editEntry(${row.id})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="HMS.entryList.deleteEntry(${row.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ]
                });
            } catch (error) {
                console.error('Failed to initialize DataTable:', error);
                HMS.utils.showError('Failed to initialize table. Please refresh the page.');
            }
        },

        // Initialize Select2 dropdowns
        initializeSelect2: function() {
            try {
                $('.select2').select2({
                    theme: 'bootstrap4',
                    width: '100%'
                });
            } catch (error) {
                console.error('Failed to initialize Select2:', error);
            }
        },

        // Load dropdown data
        loadDropdowns: function() {
            const self = this;
            
            // Load patients
            $.get(HMS.config.baseApiUrl + 'patient_api.php', { action: 'list' })
                .done(function(response) {
                    if (response.success && response.data) {
                        self.populateSelect('#patient', response.data, 'Select Patient');
                    }
                })
                .fail(function() {
                    HMS.utils.showError('Failed to load patients list');
                });

            // Load doctors
            $.get(HMS.config.baseApiUrl + 'doctor_api.php', { action: 'list' })
                .done(function(response) {
                    if (response.success && response.data) {
                        self.populateSelect('#doctor', response.data, 'Select Doctor');
                    }
                })
                .fail(function() {
                    HMS.utils.showError('Failed to load doctors list');
                });
        },

        // Populate select dropdown
        populateSelect: function(selector, data, placeholder) {
            const $select = $(selector);
            if (!$select.length) return;

            try {
                $select.empty().append(`<option value="">${HMS.utils.escapeHtml(placeholder)}</option>`);
                data.forEach(item => {
                    $select.append(`<option value="${item.id}">${HMS.utils.escapeHtml(item.name)}</option>`);
                });
                $select.trigger('change');
            } catch (error) {
                console.error(`Failed to populate select ${selector}:`, error);
            }
        },

        // Initialize form validation
        initializeFormValidation: function() {
            const self = this;
            $('#entryForm').on('submit', function(e) {
                e.preventDefault();
                if (this.checkValidity()) {
                    self.submitForm();
                }
                $(this).addClass('was-validated');
            });
        },

        // Submit form data
        submitForm: function() {
            const self = this;
            const formData = new FormData($('#entryForm')[0]);
            
            if (self.state.isLoading) return;
            self.state.isLoading = true;
            
            $.ajax({
                url: HMS.config.baseApiUrl + 'entry_api.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        HMS.utils.showSuccess('Entry saved successfully');
                        $('#addEntryModal').modal('hide');
                        self.state.table.ajax.reload();
                    } else {
                        HMS.utils.showError(response.message || 'Failed to save entry');
                    }
                },
                error: function() {
                    HMS.utils.showError('Failed to save entry. Please try again.');
                },
                complete: function() {
                    self.state.isLoading = false;
                }
            });
        },

        // View entry details
        viewEntry: function(id) {
            if (!id) return;
            // Implement view functionality
        },

        // Edit entry
        editEntry: function(id) {
            if (!id) return;
            // Implement edit functionality
        },

        // Delete entry
        deleteEntry: function(id) {
            if (!id) return;
            
            const self = this;
            
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
                    $.post(HMS.config.baseApiUrl + 'entry_api.php', {
                        action: 'delete',
                        id: id
                    })
                    .done(function(response) {
                        if (response.success) {
                            HMS.utils.showSuccess('Entry deleted successfully');
                            self.state.table.ajax.reload();
                        } else {
                            HMS.utils.showError(response.message || 'Failed to delete entry');
                        }
                    })
                    .fail(function() {
                        HMS.utils.showError('Failed to delete entry');
                    });
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        HMS.entryList.init();
    });

})(jQuery);