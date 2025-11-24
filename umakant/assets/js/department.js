// OPD Department Management JavaScript
$(document).ready(function() {
    let departmentTable;
    let currentDepartmentId = null;

    // Initialize DataTable
    function initDataTable() {
        departmentTable = $('#departmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'ajax/department_api.php',
                type: 'GET',
                data: function(d) {
                    d.action = 'list';
                },
                dataSrc: function(json) {
                    console.log('API Response:', json);
                    return json.data;
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable error:', error, thrown);
                    console.error('Response:', xhr.responseText);
                    toastr.error('Error loading data');
                }
            },
            columns: [
                { 
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false,
                    width: '50px'
                },
                { 
                    data: 'id',
                    width: '60px'
                },
                { 
                    data: 'name',
                    width: '150px'
                },
                { 
                    data: 'description',
                    width: '200px',
                    defaultContent: 'N/A',
                    render: function(data) {
                        if (!data) return 'N/A';
                        return data.length > 50 ? data.substring(0, 50) + '...' : data;
                    }
                },
                { 
                    data: 'head_of_department',
                    width: '150px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'contact_number',
                    width: '120px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'email',
                    width: '150px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'status',
                    width: '80px',
                    render: function(data, type, row) {
                        if (!data) data = 'Active';
                        const statusClass = data === 'Active' ? 'success' : 'danger';
                        const statusIcon = data === 'Active' ? 'check-circle' : 'times-circle';
                        return `<span class="badge badge-${statusClass}"><i class="fas fa-${statusIcon}"></i> ${data}</span>`;
                    },
                    defaultContent: '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Active</span>'
                },
                { 
                    data: 'added_by_username',
                    width: '100px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'created_at',
                    width: '100px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : '';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    width: '150px',
                    render: function(data, type, row) {
                        const statusBtn = row.status === 'Active' 
                            ? `<button class="btn btn-sm btn-secondary toggle-status-btn" data-id="${row.id}" title="Deactivate"><i class="fas fa-toggle-on"></i></button>`
                            : `<button class="btn btn-sm btn-success toggle-status-btn" data-id="${row.id}" title="Activate"><i class="fas fa-toggle-off"></i></button>`;
                        
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info view-btn" data-id="${row.id}" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${statusBtn}
                                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[1, 'desc']],
            pageLength: 25,
            scrollX: true,
            autoWidth: false,
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading...'
            },
            columnDefs: [
                { targets: '_all', className: 'text-center' },
                { targets: [2, 3], className: 'text-left' }
            ]
        });
    }

    // Load statistics
    function loadStats() {
        $.ajax({
            url: 'ajax/department_api.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success) {
                    $('#totalDepartments').text(response.data.total);
                    $('#activeDepartments').text(response.data.active);
                    $('#inactiveDepartments').text(response.data.inactive);
                    $('#totalDoctors').text(response.data.total_doctors || 0);
                }
            },
            error: function() {
                console.error('Error loading stats');
            }
        });
    }

    // Load doctors for dropdown
    function loadDoctors(callback) {
        $.ajax({
            url: 'opd_api/doctors.php',
            type: 'GET',
            data: { action: 'list', length: 1000 },
            success: function(response) {
                if (response.success && response.data) {
                    const doctorSelect = $('#departmentHead');
                    doctorSelect.empty();
                    doctorSelect.append('<option value="">Select Head of Department</option>');
                    
                    response.data.forEach(function(doctor) {
                        let displayText = doctor.name;
                        if (doctor.specialization) {
                            displayText += ' - ' + doctor.specialization;
                        }
                        doctorSelect.append(`<option value="${doctor.name}">${displayText}</option>`);
                    });
                    
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            },
            error: function() {
                console.error('Error loading doctors');
            }
        });
    }

    // Add new department button
    $('#addDepartmentBtn').click(function() {
        currentDepartmentId = null;
        $('#departmentForm')[0].reset();
        $('#departmentId').val('');
        $('#modalTitle').text('Add New OPD Department');
        loadDoctors();
        $('#departmentModal').modal('show');
    });

    // Form submission
    $('#departmentForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize() + '&action=save';
        
        $.ajax({
            url: 'ajax/department_api.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#departmentModal').modal('hide');
                    departmentTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message || 'Error saving department');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'Error saving department');
            }
        });
    });

    // View department
    $(document).on('click', '.view-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/department_api.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const dept = response.data;
                    const statusClass = dept.status === 'Active' ? 'success' : 'danger';
                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Department Name:</strong> ${dept.name || ''}</p>
                                <p><strong>Head of Department:</strong> ${dept.head_of_department || 'N/A'}</p>
                                <p><strong>Contact Number:</strong> ${dept.contact_number || 'N/A'}</p>
                                <p><strong>Email:</strong> ${dept.email || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Location:</strong> ${dept.location || 'N/A'}</p>
                                <p><strong>Status:</strong> <span class="badge badge-${statusClass}">${dept.status || 'N/A'}</span></p>
                                <p><strong>Added By:</strong> ${dept.added_by_username || 'N/A'}</p>
                                <p><strong>Created:</strong> ${dept.created_at ? new Date(dept.created_at).toLocaleString() : 'N/A'}</p>
                            </div>
                            <div class="col-md-12 mt-2">
                                <p><strong>Description:</strong></p>
                                <p>${dept.description || 'N/A'}</p>
                            </div>
                        </div>
                    `;
                    $('#viewDepartmentContent').html(html);
                    currentDepartmentId = id;
                    $('#viewDepartmentModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Error loading department details');
            }
        });
    });

    // Edit department
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/department_api.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const dept = response.data;
                    
                    // Load doctors first, then populate form
                    loadDoctors(function() {
                        $('#departmentId').val(dept.id);
                        $('#departmentName').val(dept.name);
                        $('#departmentDescription').val(dept.description);
                        $('#departmentHead').val(dept.head_of_department);
                        $('#departmentContact').val(dept.contact_number);
                        $('#departmentEmail').val(dept.email);
                        $('#departmentLocation').val(dept.location);
                        $('#departmentStatus').val(dept.status || 'Active');
                        $('#modalTitle').text('Edit OPD Department');
                    });
                    
                    $('#departmentModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Error loading department details');
            }
        });
    });

    // Edit from view modal
    window.editDepartmentFromView = function() {
        if (currentDepartmentId) {
            $('#viewDepartmentModal').modal('hide');
            $('.edit-btn[data-id="' + currentDepartmentId + '"]').click();
        }
    };

    // Delete department
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this department?')) {
            $.ajax({
                url: 'ajax/department_api.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        departmentTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message || 'Error deleting department');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Error deleting department');
                }
            });
        }
    });

    // Toggle status
    $(document).on('click', '.toggle-status-btn', function() {
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to change the status of this department?')) {
            $.ajax({
                url: 'ajax/department_api.php',
                type: 'POST',
                data: { action: 'toggle_status', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        departmentTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message || 'Error updating status');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Error updating status');
                }
            });
        }
    });

    // Initialize
    initDataTable();
    loadStats();
    loadDoctors();
});
