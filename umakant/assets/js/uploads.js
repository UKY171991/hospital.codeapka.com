/**
 * Uploads Page JavaScript
 * Handles upload list functionality and DataTable
 */

$(document).ready(function() {
    // Initialize uploads table
    if ($('#uploadsTable').length) {
        var uploadsTable = $('#uploadsTable').DataTable({
            ajax: {
                url: 'ajax/upload_file.php?action=list',
                type: 'GET',
                dataSrc: function(json) {
                    return json.success ? json.data : [];
                }
            },
            columns: [
                { 
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { 
                    data: 'original_name',
                    render: function(data, type, row) {
                        if (row.relative_path) {
                            return '<a href="' + row.relative_path + '" target="_blank">' + data + '</a>';
                        }
                        return data;
                    }
                },
                { 
                    data: 'file_size',
                    render: function(data) {
                        if (!data) return '';
                        var kb = Math.round(data / 1024);
                        if (kb > 1024) {
                            return Math.round(kb / 1024 * 100) / 100 + ' MB';
                        }
                        return kb + ' KB';
                    }
                },
                { 
                    data: 'created_at',
                    render: function(data) {
                        if (!data) return '';
                        return new Date(data).toLocaleString();
                    }
                },
                { 
                    data: 'uploaded_by_username',
                    defaultContent: 'Unknown'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        var actions = '';
                        if (row.relative_path) {
                            actions += '<a href="' + row.relative_path + '" class="btn btn-sm btn-info mr-1" target="_blank"><i class="fas fa-download"></i></a>';
                        }
                        actions += '<button class="btn btn-sm btn-danger delete-file" data-file="' + row.file_name + '"><i class="fas fa-trash"></i></button>';
                        return actions;
                    }
                }
            ],
            responsive: true,
            pageLength: 25,
            order: [[3, 'desc']] // Sort by upload date descending
        });
        
        // Handle file deletion
        $('#uploadsTable').on('click', '.delete-file', function() {
            var fileName = $(this).data('file');
            var row = $(this).closest('tr');
            
            $('#confirmDeleteModal').modal('show');
            
            $('#confirmDeleteBtn').off('click').on('click', function() {
                $.post('ajax/upload_file.php', {
                    action: 'delete',
                    file: fileName
                })
                .done(function(response) {
                    if (response.success) {
                        uploadsTable.ajax.reload();
                        toastr.success('File deleted successfully');
                    } else {
                        toastr.error(response.message || 'Failed to delete file');
                    }
                })
                .fail(function() {
                    toastr.error('Error deleting file');
                })
                .always(function() {
                    $('#confirmDeleteModal').modal('hide');
                });
            });
        });
        
        // Refresh table when upload completes
        $(document).on('uploadComplete', function() {
            uploadsTable.ajax.reload();
        });
    }
    
    // Initialize upload functionality
    if (typeof initializeUploadEvents === 'function') {
        initializeUploadEvents();
    }
});