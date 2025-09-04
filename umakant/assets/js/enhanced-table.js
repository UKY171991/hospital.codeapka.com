// Enhanced Table Management Library
// Provides common functionality for all table pages

class EnhancedTableManager {
    constructor(config) {
        this.config = {
            tableSelector: '#dataTable',
            apiEndpoint: '',
            entityName: 'item',
            entityNamePlural: 'items',
            ...config
        };
        
        this.selectedRows = new Set();
        this.dataTable = null;
        this.init();
    }

    init() {
        this.initializeTable();
        this.bindEvents();
        this.loadData();
    }

    initializeTable() {
        const self = this;
        
        this.dataTable = $(this.config.tableSelector).DataTable({
            processing: true,
            serverSide: false,
            responsive: true,
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                {
                    text: '<i class="fas fa-download"></i> Export',
                    className: 'btn btn-info btn-sm',
                    action: function() { self.exportData(); }
                },
                {
                    text: '<i class="fas fa-sync"></i> Refresh',
                    className: 'btn btn-secondary btn-sm',
                    action: function() { self.refreshData(); }
                }
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return `<input type="checkbox" class="selection-checkbox" value="${row.id}">`;
                    }
                },
                {
                    targets: -1,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return self.renderActionButtons(row);
                    }
                }
            ],
            language: {
                processing: '<div class="spinner"></div>',
                emptyTable: `No ${this.config.entityNamePlural} found`,
                zeroRecords: `No matching ${this.config.entityNamePlural} found`
            },
            ...this.config.tableOptions
        });
    }

    bindEvents() {
        const self = this;

        // Selection events
        $(document).on('change', '.selection-checkbox', function() {
            const rowId = $(this).val();
            const isChecked = $(this).is(':checked');
            
            if (isChecked) {
                self.selectedRows.add(rowId);
                $(this).closest('tr').addClass('row-selected');
            } else {
                self.selectedRows.delete(rowId);
                $(this).closest('tr').removeClass('row-selected');
            }
            
            self.updateBulkActions();
        });

        // Select all checkbox
        $(document).on('change', '#selectAll', function() {
            const isChecked = $(this).is(':checked');
            $('.selection-checkbox').prop('checked', isChecked);
            
            if (isChecked) {
                $('.selection-checkbox').each(function() {
                    self.selectedRows.add($(this).val());
                    $(this).closest('tr').addClass('row-selected');
                });
            } else {
                self.selectedRows.clear();
                $('.row-selected').removeClass('row-selected');
            }
            
            self.updateBulkActions();
        });

        // Bulk action buttons
        $(document).on('click', '.bulk-delete', function() {
            self.bulkDelete();
        });

        $(document).on('click', '.bulk-export', function() {
            self.bulkExport();
        });

        // Individual action buttons
        $(document).on('click', '.btn-view', function() {
            const id = $(this).data('id');
            self.viewItem(id);
        });

        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            self.editItem(id);
        });

        $(document).on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            self.deleteItem(id);
        });
    }

    renderActionButtons(row) {
        return `
            <div class="action-buttons">
                <button class="btn btn-info btn-sm btn-view" data-id="${row.id}" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-warning btn-sm btn-edit" data-id="${row.id}" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm btn-delete" data-id="${row.id}" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    }

    updateBulkActions() {
        const selectedCount = this.selectedRows.size;
        const bulkActions = $('.bulk-actions');
        
        if (selectedCount > 0) {
            bulkActions.addClass('show');
            bulkActions.find('.selected-count').text(selectedCount);
        } else {
            bulkActions.removeClass('show');
        }
    }

    async loadData() {
        try {
            this.showLoading();
            const response = await this.apiRequest('GET', this.config.apiEndpoint);
            
            if (response.success) {
                this.dataTable.clear();
                this.dataTable.rows.add(response.data);
                this.dataTable.draw();
                this.showSuccess(`${this.config.entityNamePlural} loaded successfully`);
            } else {
                this.showError(`Failed to load ${this.config.entityNamePlural}: ${response.message}`);
            }
        } catch (error) {
            this.showError(`Error loading ${this.config.entityNamePlural}: ${error.message}`);
        } finally {
            this.hideLoading();
        }
    }

    async viewItem(id) {
        try {
            this.showLoading();
            const response = await this.apiRequest('GET', `${this.config.apiEndpoint}?action=get&id=${id}`);
            
            if (response.success) {
                this.showViewModal(response.data);
            } else {
                this.showError(`Failed to load ${this.config.entityName} details: ${response.message}`);
            }
        } catch (error) {
            this.showError(`Error loading ${this.config.entityName} details: ${error.message}`);
        } finally {
            this.hideLoading();
        }
    }

    async editItem(id) {
        try {
            this.showLoading();
            const response = await this.apiRequest('GET', `${this.config.apiEndpoint}?action=get&id=${id}`);
            
            if (response.success) {
                this.showEditModal(response.data);
            } else {
                this.showError(`Failed to load ${this.config.entityName} for editing: ${response.message}`);
            }
        } catch (error) {
            this.showError(`Error loading ${this.config.entityName} for editing: ${error.message}`);
        } finally {
            this.hideLoading();
        }
    }

    async deleteItem(id) {
        const confirmed = await this.showConfirmDialog(
            'Delete Confirmation',
            `Are you sure you want to delete this ${this.config.entityName}?`,
            'danger'
        );

        if (confirmed) {
            try {
                this.showLoading();
                const response = await this.apiRequest('POST', this.config.apiEndpoint, {
                    action: 'delete',
                    id: id
                });

                if (response.success) {
                    this.showSuccess(`${this.config.entityName} deleted successfully`);
                    this.refreshData();
                } else {
                    this.showError(`Failed to delete ${this.config.entityName}: ${response.message}`);
                }
            } catch (error) {
                this.showError(`Error deleting ${this.config.entityName}: ${error.message}`);
            } finally {
                this.hideLoading();
            }
        }
    }

    async bulkDelete() {
        const selectedCount = this.selectedRows.size;
        const confirmed = await this.showConfirmDialog(
            'Bulk Delete Confirmation',
            `Are you sure you want to delete ${selectedCount} selected ${this.config.entityNamePlural}?`,
            'danger'
        );

        if (confirmed) {
            try {
                this.showLoading();
                const ids = Array.from(this.selectedRows);
                const response = await this.apiRequest('POST', this.config.apiEndpoint, {
                    action: 'bulk_delete',
                    ids: ids
                });

                if (response.success) {
                    this.showSuccess(`${selectedCount} ${this.config.entityNamePlural} deleted successfully`);
                    this.selectedRows.clear();
                    this.updateBulkActions();
                    this.refreshData();
                } else {
                    this.showError(`Failed to delete ${this.config.entityNamePlural}: ${response.message}`);
                }
            } catch (error) {
                this.showError(`Error deleting ${this.config.entityNamePlural}: ${error.message}`);
            } finally {
                this.hideLoading();
            }
        }
    }

    showViewModal(data) {
        const modalHtml = this.generateViewModalHtml(data);
        
        // Remove existing modal
        $('#viewModal').remove();
        
        // Add modal to body
        $('body').append(modalHtml);
        
        // Show modal
        $('#viewModal').modal('show');
    }

    generateViewModalHtml(data) {
        const fields = this.config.viewFields || Object.keys(data);
        
        let detailsHtml = '';
        fields.forEach(field => {
            if (data.hasOwnProperty(field)) {
                const label = this.formatFieldLabel(field);
                const value = this.formatFieldValue(field, data[field]);
                
                detailsHtml += `
                    <div class="detail-item">
                        <div class="detail-label">${label}</div>
                        <div class="detail-value">${value}</div>
                    </div>
                `;
            }
        });

        return `
            <div class="modal fade view-modal modal-enhanced" id="viewModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-eye mr-2"></i>
                                View ${this.config.entityName}
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="view-details">
                                ${detailsHtml}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times"></i> Close
                            </button>
                            <button type="button" class="btn btn-warning" onclick="tableManager.editItem(${data.id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    formatFieldLabel(field) {
        return field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    formatFieldValue(field, value) {
        if (value === null || value === undefined || value === '') {
            return '<span class="text-muted">N/A</span>';
        }

        // Format specific field types
        if (field.toLowerCase().includes('date')) {
            return new Date(value).toLocaleDateString();
        }
        
        if (field.toLowerCase().includes('status')) {
            return `<span class="status-badge status-${value.toLowerCase()}">${value}</span>`;
        }
        
        if (field.toLowerCase().includes('email')) {
            return `<a href="mailto:${value}">${value}</a>`;
        }
        
        if (field.toLowerCase().includes('phone') || field.toLowerCase().includes('mobile')) {
            return `<a href="tel:${value}">${value}</a>`;
        }

        return String(value);
    }

    async apiRequest(method, url, data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            }
        };

        if (data) {
            if (method === 'GET') {
                const params = new URLSearchParams(data);
                url += (url.includes('?') ? '&' : '?') + params.toString();
            } else {
                options.body = JSON.stringify(data);
            }
        }

        const response = await fetch(url, options);
        return await response.json();
    }

    refreshData() {
        this.selectedRows.clear();
        this.updateBulkActions();
        $('.row-selected').removeClass('row-selected');
        $('#selectAll').prop('checked', false);
        this.loadData();
    }

    exportData() {
        // Export functionality
        const data = this.dataTable.data().toArray();
        this.downloadCSV(data, `${this.config.entityNamePlural}_export.csv`);
    }

    downloadCSV(data, filename) {
        if (data.length === 0) {
            this.showWarning('No data to export');
            return;
        }

        const headers = Object.keys(data[0]);
        let csv = headers.join(',') + '\n';
        
        data.forEach(row => {
            const values = headers.map(header => {
                const value = row[header] || '';
                return `"${String(value).replace(/"/g, '""')}"`;
            });
            csv += values.join(',') + '\n';
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    showLoading() {
        if ($('.loading-overlay').length === 0) {
            $('body').append('<div class="loading-overlay"><div class="spinner"></div></div>');
        }
    }

    hideLoading() {
        $('.loading-overlay').remove();
    }

    showSuccess(message) {
        toastr.success(message, 'Success', {
            timeOut: 3000,
            positionClass: 'toast-top-right',
            progressBar: true
        });
    }

    showError(message) {
        toastr.error(message, 'Error', {
            timeOut: 5000,
            positionClass: 'toast-top-right',
            progressBar: true
        });
    }

    showWarning(message) {
        toastr.warning(message, 'Warning', {
            timeOut: 4000,
            positionClass: 'toast-top-right',
            progressBar: true
        });
    }

    showInfo(message) {
        toastr.info(message, 'Info', {
            timeOut: 3000,
            positionClass: 'toast-top-right',
            progressBar: true
        });
    }

    async showConfirmDialog(title, message, type = 'warning') {
        return new Promise((resolve) => {
            const modalId = 'confirmModal_' + Date.now();
            const typeClass = type === 'danger' ? 'btn-danger' : 'btn-warning';
            const iconClass = type === 'danger' ? 'fa-exclamation-triangle' : 'fa-question-circle';
            
            const modalHtml = `
                <div class="modal fade" id="${modalId}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-${type} text-white">
                                <h5 class="modal-title">
                                    <i class="fas ${iconClass} mr-2"></i>${title}
                                </h5>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0">${message}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <button type="button" class="btn ${typeClass}" id="confirmBtn">
                                    <i class="fas fa-check"></i> Confirm
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            $(`#${modalId}`).modal('show');
            
            $(`#${modalId} #confirmBtn`).on('click', function() {
                $(`#${modalId}`).modal('hide');
                resolve(true);
            });
            
            $(`#${modalId}`).on('hidden.bs.modal', function() {
                $(this).remove();
                resolve(false);
            });
        });
    }
}

// Export for global use
window.EnhancedTableManager = EnhancedTableManager;
