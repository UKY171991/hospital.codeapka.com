$(document).ready(function () {
    // Initialize DataTable
    var table = $('#plansTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "ajax": {
            "url": "ajax/plan_api.php?action=list",
            "dataSrc": "data"
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
            {
                "data": "price",
                "render": function (data) {
                    return parseFloat(data).toFixed(2);
                }
            },
            { "data": "upi" },
            {
                "data": "time_type",
                "render": function (data) {
                    return data.charAt(0).toUpperCase() + data.slice(1);
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

    // Open Add Modal
    $('#addPlanBtn').click(function () {
        $('#planForm')[0].reset();
        $('#planId').val('');
        $('#planModalLabel').text('Add Plan');
        $('#qrPreview').hide().attr('src', '');
        $('#existingQr').text('(none)').show();
        $('#planModal').modal('show');
    });

    // Handle Form Submission
    $('#savePlanBtn').click(function () {
        var form = $('#planForm')[0];
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var formData = new FormData(form);
        // Add logic to include save action if not implied by URL, 
        // but here we post to ?action=save

        // Disable button to prevent double submit
        var btn = $(this);
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'ajax/plan_api.php?action=save',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#planModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(response.message || 'Error saving plan');
                }
            },
            error: function (xhr) {
                var msg = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
            },
            complete: function () {
                btn.prop('disabled', false).text('Save Plan');
            }
        });
    });

    // Handle Edit
    $('#plansTable').on('click', '.edit-btn', function () {
        var id = $(this).data('id');
        $.ajax({
            url: 'ajax/plan_api.php',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success) {
                    var data = response.data;
                    $('#planId').val(data.id);
                    $('#planName').val(data.name);
                    $('#planPrice').val(data.price);
                    $('#planUpi').val(data.upi);
                    $('#planType').val(data.time_type); // ensure value matches option value (lowercase)
                    $('#planDescription').val(data.description);

                    // QR Code handling
                    if (data.qr_code) {
                        $('#qrPreview').attr('src', data.qr_code).show();
                        $('#existingQr').hide();
                    } else {
                        $('#qrPreview').hide();
                        $('#existingQr').text('(none)').show();
                    }

                    $('#planModalLabel').text('Edit Plan');
                    $('#planModal').modal('show');
                } else {
                    toastr.error('Failed to fetch plan details');
                }
            },
            error: function () {
                toastr.error('Error fetching plan details');
            }
        });
    });

    // Handle View
    $('#plansTable').on('click', '.view-btn', function () {
        var id = $(this).data('id');
        $.ajax({
            url: 'ajax/plan_api.php',
            data: { action: 'get', id: id },
            success: function (response) {
                if (response.success) {
                    var data = response.data;
                    $('#viewPlanName').text(data.name);
                    $('#viewPlanDescription').text(data.description || 'No description');
                    $('#viewPlanPrice').text(parseFloat(data.price).toFixed(2));
                    $('#viewPlanUpi').text(data.upi || '-');
                    $('#viewPlanType').text(data.time_type.charAt(0).toUpperCase() + data.time_type.slice(1));
                    $('#viewPlanAddedBy').text(data.added_by_username || 'Unknown');

                    if (data.qr_code) {
                        $('#viewQrImg').attr('src', data.qr_code).show();
                        $('#viewQrNone').hide();
                    } else {
                        $('#viewQrImg').hide();
                        $('#viewQrNone').show();
                    }

                    $('#planViewModal').modal('show');
                }
            }
        });
    });

    // Handle Delete
    $('#plansTable').on('click', '.delete-btn', function () {
        var id = $(this).data('id');
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
                $.ajax({
                    url: 'ajax/plan_api.php?action=delete',
                    type: 'POST',
                    data: { id: id },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            table.ajax.reload();
                        } else {
                            toastr.error(response.message || 'Failed to delete');
                        }
                    },
                    error: function () {
                        toastr.error('Server error');
                    }
                });
            }
        });
    });

    // File input preview (optional enhancement)
    $('#planQr').change(function () {
        var file = this.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) {
                toastr.warning('File too large (max 2MB)');
                this.value = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#qrPreview').attr('src', e.target.result).show();
                $('#existingQr').hide();
            }
            reader.readAsDataURL(file);
        }
    });

});
