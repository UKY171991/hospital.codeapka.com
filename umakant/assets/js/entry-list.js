/**
 * Entry List Management System
 * Clean, modern JavaScript for managing hospital test entries
 */

class EntryManager {
    constructor() {
        this.entriesTable = null;
        this.testsData = [];
        this.patientsData = [];
        this.doctorsData = [];
        this.ownersData = [];
        this.currentEditId = null;
        this.testRowCounter = 0;
        
        this.init();
    }

    /**
     * Initialize the Entry Manager
     */
    init() {
        // console.log('Initializing Entry Manager...');
        
        // Wait for DOM to be ready
        $(document).ready(() => {
            this.initializeDataTable();
            this.loadInitialData();
            this.bindEvents();
            this.loadStatistics();
        });
    }

    /**
     * Initialize DataTable with proper configuration
     */
    initializeDataTable() {
        // console.log('Initializing DataTable...');
        
        this.entriesTable = $('#entriesTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: 'ajax/entry_api_fixed.php',
                type: 'GET',
                data: { action: 'list' },
                dataSrc: function(json) {
                    console.log('DataTable received data:', json);
                    if (json.success) {
                        return json.data || [];
                    } else {
                        console.error('API Error:', json.message);
                        toastr.error(json.message || 'Failed to load entries');
                        return [];
                    }
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable AJAX Error:', error, thrown);
                    toastr.error('Failed to load entries. Please refresh the page.');
                }
            },
            columns: [
                { 
                    data: 'id',
                    title: 'ID',
                    width: '5%'
                },
                { 
                    data: 'patient_name',
                    title: 'Patient',
                    width: '12%',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let html = `<strong>${data || 'N/A'}</strong>`;
                            if (row.patient_contact) {
                                html += `<br><small class="text-muted">${row.patient_contact}</small>`;
                            }
                            return html;
                        }
                        return data || '';
                    }
                },
                { 
                    data: 'doctor_name',
                    title: 'Doctor',
                    width: '10%',
                    render: function(data, type, row) {
                        return data || '<span class="text-muted">Not assigned</span>';
                    }
                },
                { 
                    data: 'test_names',
                    title: 'Tests',
                    width: '15%',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const testCount = parseInt(row.tests_count) || 0;
                            const testNames = data || '';
                            
                            if (testCount === 0) {
                                return '<span class="text-muted">No tests</span>';
                            } else if (testCount === 1) {
                                return `<span class="badge badge-info">${testCount}</span> ${testNames}`;
                            } else {
                                return `<span class="badge badge-primary">${testCount}</span> ${testNames}`;
                            }
                        }
                        return data || '';
                    }
                },
                { 
                    data: 'status',
                    title: 'Status',
                    width: '7%',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const status = data || 'pending';
                            const badgeClass = {
                                'pending': 'badge-warning',
                                'completed': 'badge-success',
                                'cancelled': 'badge-danger'
                            }[status] || 'badge-secondary';
                            
                            return `<span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
                        }
                        return data || 'pending';
                    }
                },
                { 
                    data: 'priority',
                    title: 'Priority',
                    width: '7%',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const priority = data || 'normal';
                            const badgeClass = {
                                'emergency': 'badge-danger',
                                'urgent': 'badge-warning',
                                'normal': 'badge-info',
                                'routine': 'badge-secondary'
                            }[priority] || 'badge-secondary';
                            
                            return `<span class="badge ${badgeClass}">${priority.charAt(0).toUpperCase() + priority.slice(1)}</span>`;
                        }
                        return data || 'normal';
                    }
                },
                { 
                    data: 'total_price',
                    title: 'Amount',
                    width: '8%',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            const amount = parseFloat(data) || 0;
                            return `₹${amount.toFixed(2)}`;
                        }
                        return data || 0;
                    }
                },
                { 
                    data: 'entry_date',
                    title: 'Date',
                    width: '8%',
                    render: function(data, type, row) {
                        if (type === 'display' && data) {
                            const date = new Date(data);
                            return date.toLocaleDateString('en-IN');
                        }
                        return data || '';
                    }
                },
                { 
                    data: 'added_by_full_name',
                    title: 'Added By',
                    width: '7%',
                    render: function(data, type, row) {
                        return data || row.added_by_username || 'Unknown';
                    }
                },
                { 
                    data: null,
                    title: 'Actions',
                    width: '9%',
                    orderable: false,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return `
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-info btn-sm" onclick="window.entryManager.viewEntry(${row.id})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.entryManager.editEntry(${row.id})" title="Edit Entry">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="window.entryManager.deleteEntry(${row.id})" title="Delete Entry">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `;
                        }
                        return '';
                    }
                }
            ],
            order: [[0, 'desc']], // Order by ID descending (newest first)
            pageLength: 25,
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-info btn-sm'
                }
            ],
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading entries...',
                emptyTable: 'No entries found',
                zeroRecords: 'No matching entries found'
            }
        });

        // console.log('DataTable initialized successfully');
    }

    /**
     * Load initial data (tests, patients, doctors, owners)
     */
    async loadInitialData() {
        // console.log('Loading initial data...');
        
        try {
            // Load tests data
            await this.loadTestsData();
            
            // Load owners/users data
            await this.loadOwnersData();
            
            // console.log('Initial data loaded successfully');
        } catch (error) {
            console.error('Error loading initial data:', error);
            toastr.error('Failed to load initial data');
        }
    }

    /**
     * Load tests data from API
     */
    async loadTestsData() {
        try {
            const response = await $.ajax({
                url: 'ajax/test_api.php',
                method: 'GET',
                data: { action: 'list' },
                dataType: 'json'
            });

            if (response.success) {
                this.testsData = response.data || [];
                console.log('Loaded tests data:', this.testsData.length, 'tests');
            } else {
                console.error('Failed to load tests:', response.message);
            }
        } catch (error) {
            console.error('Error loading tests data:', error);
            this.testsData = [];
        }
    }

    /**
     * Load owners/users data from API
     */
    async loadOwnersData() {
        try {
            const response = await $.ajax({
                url: 'ajax/owner_api.php',
                method: 'GET',
                data: { action: 'list' },
                dataType: 'json'
            });

            if (response.success) {
                this.ownersData = response.data || [];
                this.populateOwnerSelect();
                console.log('Loaded owners data:', this.ownersData.length, 'owners');
            } else {
                console.error('Failed to load owners:', response.message);
            }
        } catch (error) {
            console.error('Error loading owners data:', error);
            this.ownersData = [];
        }
    }

    /**
     * Populate owner select dropdown
     */
    populateOwnerSelect() {
        const $select = $('#ownerAddedBySelect');
        $select.empty().append('<option value="">Select Owner/User</option>');
        
        this.ownersData.forEach(owner => {
            $select.append(`<option value="${owner.id}">${owner.name}</option>`);
        });
        
        // Refresh Select2 if initialized
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.trigger('change');
        }
    }

    /**
     * Load statistics for dashboard cards
     */
    async loadStatistics() {
        try {
            const response = await $.ajax({
                url: 'ajax/entry_api_fixed.php',
                method: 'GET',
                data: { action: 'stats' },
                dataType: 'json'
            });

            if (response.success) {
                const stats = response.data;
                $('#totalEntries').text(stats.total || 0);
                $('#pendingEntries').text(stats.pending || 0);
                $('#completedEntries').text(stats.completed || 0);
                $('#todayEntries').text(stats.today || 0);
            } else {
                console.error('Failed to load statistics:', response.message);
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    /**
     * Bind event handlers
     */
    bindEvents() {
        // console.log('Binding events...');
        
        // Filter change events
        $('#statusFilter, #dateFilter').on('change', () => {
            this.applyFilters();
        });
        
        $('#patientFilter, #doctorFilter').on('keyup', this.debounce(() => {
            this.applyFilters();
        }, 300));
        
        // Owner selection change
        $('#ownerAddedBySelect').on('change', (e) => {
            this.onOwnerChange(e.target.value);
        });
        
        // Patient selection change
        $('#patientSelect').on('change', (e) => {
            this.onPatientChange(e.target.value);
        });
        
        // Form submission
        $('#entryForm').on('submit', (e) => {
            e.preventDefault();
            this.saveEntry();
        });
        
        // Discount amount change
        $('#discountAmount').on('input', () => {
            this.calculateTotals();
        });
        
        // console.log('Events bound successfully');
    }

    /**
     * Utility function for debouncing
     */
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Apply filters to DataTable
     */
    applyFilters() {
        if (!this.entriesTable) return;
        
        const statusFilter = $('#statusFilter').val();
        const dateFilter = $('#dateFilter').val();
        const patientFilter = $('#patientFilter').val();
        const doctorFilter = $('#doctorFilter').val();
        
        // Apply column filters
        this.entriesTable
            .column(4).search(statusFilter) // Status column
            .column(1).search(patientFilter) // Patient column
            .column(2).search(doctorFilter) // Doctor column
            .draw();
    }

    /**
     * Refresh the entries table
     */
    refreshTable() {
        if (this.entriesTable) {
            this.entriesTable.ajax.reload();
            this.loadStatistics();
            toastr.success('Table refreshed successfully');
        }
    }

    /**
     * Export entries
     */
    exportEntries() {
        if (this.entriesTable) {
            // Trigger the Excel export
            this.entriesTable.button('.buttons-excel').trigger();
        }
    }

    /**
     * Filter by status (called from statistics cards)
     */
    filterByStatus(status) {
        $('#statusFilter').val(status === 'all' ? '' : status).trigger('change');
    }

    /**
     * Filter by date (called from statistics cards)
     */
    filterByDate(dateRange) {
        $('#dateFilter').val(dateRange).trigger('change');
    }

    /**
     * Open Add Entry Modal
     */
    openAddModal() {
        console.log('Opening add entry modal...');
        
        this.currentEditId = null;
        this.resetForm();
        
        // Update modal title
        $('#entryModalLabel').html('<i class="fas fa-plus mr-1"></i>Add New Entry');
        
        // Show modal
        $('#entryModal').modal('show');
        
        // Initialize Select2 dropdowns
        this.initializeSelect2();
        
        // Add first test row
        this.addTestRow();
    }

    /**
     * Add a new test row
     */
    addTestRow(testData = null) {
        const rowIndex = this.testRowCounter++;
        
        const testOptions = this.testsData.map(test => 
            `<option value="${test.id}" data-category="${test.category_name || ''}" data-unit="${test.unit || ''}" data-min="${test.min || ''}" data-max="${test.max || ''}" data-price="${test.price || 0}">
                ${test.name}
            </option>`
        ).join('');

        const rowHtml = `
            <div class="test-row row mb-2" data-row-index="${rowIndex}">
                <div class="col-md-3">
                    <select class="form-control test-select select2" name="tests[${rowIndex}][test_id]" required>
                        <option value="">Select Test</option>
                        ${testOptions}
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control test-category" name="tests[${rowIndex}][category_name]" placeholder="Category" readonly>
                    <input type="hidden" name="tests[${rowIndex}][category_id]" class="test-category-id">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control test-result" name="tests[${rowIndex}][result_value]" placeholder="Result">
                </div>
                <div class="col-md-1">
                    <input type="text" class="form-control test-min" name="tests[${rowIndex}][min]" placeholder="Min" readonly>
                </div>
                <div class="col-md-1">
                    <input type="text" class="form-control test-max" name="tests[${rowIndex}][max]" placeholder="Max" readonly>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control test-unit" name="tests[${rowIndex}][unit]" placeholder="Unit" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-test-btn" onclick="window.entryManager.removeTestRow(this)" title="Remove Test">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <input type="hidden" name="tests[${rowIndex}][price]" class="test-price" value="0">
            </div>
        `;

        $('#testsContainer').append(rowHtml);
        
        // Initialize Select2 for the new row
        const $newRow = $(`.test-row[data-row-index="${rowIndex}"]`);
        $newRow.find('.test-select').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
        
        // Bind test selection change event
        $newRow.find('.test-select').on('change', (e) => {
            this.onTestChange(e.target, $newRow);
        });
        
        // If testData is provided, populate the row
        if (testData) {
            setTimeout(() => {
                $newRow.find('.test-select').val(testData.test_id).trigger('change');
                $newRow.find('.test-result').val(testData.result_value || '');
            }, 100);
        }
    }

    /**
     * Remove a test row
     */
    removeTestRow(button) {
        const $row = $(button).closest('.test-row');
        $row.remove();
        this.calculateTotals();
        
        // Ensure at least one test row exists
        if ($('#testsContainer .test-row').length === 0) {
            this.addTestRow();
        }
    }

    /**
     * Handle test selection change
     */
    onTestChange(selectElement, $row) {
        const $select = $(selectElement);
        const selectedOption = $select.find('option:selected');
        
        if (selectedOption.val()) {
            // Populate test details
            $row.find('.test-category').val(selectedOption.data('category') || '');
            $row.find('.test-unit').val(selectedOption.data('unit') || '');
            $row.find('.test-min').val(selectedOption.data('min') || '');
            $row.find('.test-max').val(selectedOption.data('max') || '');
            $row.find('.test-price').val(selectedOption.data('price') || 0);
        } else {
            // Clear test details
            $row.find('.test-category, .test-unit, .test-min, .test-max').val('');
            $row.find('.test-price').val(0);
        }
        
        this.calculateTotals();
    }

    /**
     * Calculate pricing totals
     */
    calculateTotals() {
        let subtotal = 0;
        
        // Sum up all test prices
        $('.test-price').each(function() {
            const price = parseFloat($(this).val()) || 0;
            subtotal += price;
        });
        
        const discount = parseFloat($('#discountAmount').val()) || 0;
        const total = Math.max(subtotal - discount, 0);
        
        $('#subtotal').val(subtotal.toFixed(2));
        $('#totalPrice').val(total.toFixed(2));
    }

    /**
     * Initialize Select2 dropdowns
     */
    initializeSelect2() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    /**
     * Reset the entry form
     */
    resetForm() {
        $('#entryForm')[0].reset();
        $('#entryId').val('');
        $('#testsContainer').empty();
        this.testRowCounter = 0;
        
        // Clear Select2 selections
        $('.select2').val(null).trigger('change');
        
        // Reset pricing
        this.calculateTotals();
    }
}

// Initialize Entry Manager when page loads
let entryManager;
$(document).ready(function() {
    entryManager = new EntryManager();
    window.entryManager = entryManager;
});/*
*
 * Accessibility and keyboard navigation enhancements
 */
$(document).ready(function() {
    // Add keyboard navigation for modals
    $(document).on('keydown', function(e) {
        // ESC key to close modals
        if (e.key === 'Escape') {
            $('.modal.show').modal('hide');
        }
        
        // Enter key to submit forms in modals
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            const $modal = $(e.target).closest('.modal');
            if ($modal.length && $modal.find('form').length) {
                e.preventDefault();
                $modal.find('form').submit();
            }
        }
    });
    
    // Add ARIA labels and accessibility attributes
    $('#entriesTable').attr('aria-label', 'Test entries data table');
    $('.btn').each(function() {
        if (!$(this).attr('aria-label') && $(this).attr('title')) {
            $(this).attr('aria-label', $(this).attr('title'));
        }
    });
    
    // Focus management for modals
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('input, select, textarea').filter(':visible').first().focus();
    });
    
    // Add loading indicators
    $(document).ajaxStart(function() {
        $('body').addClass('loading');
    }).ajaxStop(function() {
        $('body').removeClass('loading');
    });
});