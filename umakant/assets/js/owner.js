/* owner.js - Owner management functionality */
$(document).ready(function () {
    // Initialize DataTable
    var table = $('#ownersTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "ajax": {
            "url": "ajax/owner_api.php?action=list",
            "dataSrc": function (json) {
                if (!json.success) {
                    toastr.error(json.message);
                    return [];
                }
                return json.data;
            }
        },
        "columns": [
            {
                "data": null,
                "render": function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { "data": "id" },
            { "data": "name" },
            { "data": "phone" },
            { "data": "whatsapp" },
            { "data": "email" },
            { "data": "address" },
            {
                "data": "link",
                "render": function (data) {
                    return data ? `<a href="${data}" target="_blank"><i class="fas fa-external-link-alt"></i> Link</a>` : '-';
                }
            },
            {
                "data": "added_by_username",
                "defaultContent": "Unknown"
            },
            {
                "data": null,
                "orderable": false,
                "className": "text-center",
                "render": function (data, type, row) {
                    return `
              <button class="btn btn-sm btn-info view-btn" data-id="${row.id}" title="View">
                <i class="fas fa-eye"></i>
              </button>
              <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}" title="Edit">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" title="Delete">
                <i class="fas fa-trash"></i>
              </button>
            `;
                }
            }
        ],
        "order": [[1, "desc"]]
    });

    // Add Owner Button
    $('#addOwnerBtn').click(function () {
        $('#ownerForm')[0].reset();
        $('#ownerId').val('');
        $('#ownerModalLabel').text('Add Owner');
        $('#ownerModal').modal('show');
    });

    // Save Owner
    $('#saveOwnerBtn').click(function () {
        var form = $('#ownerForm')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).text('Saving...');

        var formData = new FormData(form);
        $.ajax({
            url: 'ajax/owner_api.php?action=save',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#ownerModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error saving owner');
                }
            },
            error: function () {
                toastr.error('Server error');
            },
            complete: function () {
                btn.prop('disabled', false).text('Save Owner');
            }
        });
    });

    // Edit Owner
    $('#ownersTable').on('click', '.edit-btn', function () {
        var id = $(this).data('id');
        $.ajax({
            url: 'ajax/owner_api.php',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success) {
                    var data = response.data;
                    $('#ownerId').val(data.id);
                    $('#ownerName').val(data.name);
                    $('#ownerEmail').val(data.email);
                    $('#ownerPhone').val(data.phone);
                    $('#ownerWhatsapp').val(data.whatsapp);
                    $('#ownerAddress').val(data.address);
                    $('#ownerLink').val(data.link || '');

                    $('#ownerModalLabel').text('Edit Owner');
                    $('#ownerModal').modal('show');
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Delete Owner
    $('#ownersTable').on('click', '.delete-btn', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Delete Owner?',
            text: "This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/owner_api.php?action=delete',
                    type: 'POST',
                    data: { id: id },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            table.ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    }
                });
            }
        });
    });

    // View Owner (Using Global Modal or dedicated one)
    $('#ownersTable').on('click', '.view-btn', function () {
        var id = $(this).data('id');
        // Fetch fresh data or use row data
        $.ajax({
            url: 'ajax/owner_api.php',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success) {
                    var d = response.data;
                    var content = `
                        <table class="table table-striped">
                            <tr><th>ID</th><td>${d.id}</td></tr>
                            <tr><th>Name</th><td>${d.name}</td></tr>
                            <tr><th>Phone</th><td>${d.phone || '-'}</td></tr>
                            <tr><th>WhatsApp</th><td>${d.whatsapp || '-'}</td></tr>
                            <tr><th>Email</th><td>${d.email || '-'}</td></tr>
                            <tr><th>Address</th><td>${d.address || '-'}</td></tr>
                            <tr><th>Link</th><td>${d.link ? `<a href="${d.link}" target="_blank">${d.link}</a>` : '-'}</td></tr>
                            <tr><th>Added By</th><td>${d.added_by_username || 'Unknown'}</td></tr>
                        </table>
                    `;
                    // If you have a specific view modal, populate it. Here using a generic approach or alert for brevity if modal structure not confirmed
                    // Assuming 'globalViewModal' exists from footer
                    $('#globalViewModalBody').html(content);
                    $('#globalViewModalLabel').text('Owner Details');
                    $('#globalViewModal').modal('show');
                }
            }
        });
    });
});
