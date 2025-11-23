// OPD Billing Management JavaScript
$(document).ready(function() {
    let opdBillingTable;
    let currentBillingId = null;

    // Set today's date as default
    $('#billDate').val(new Date().toISOString().split('T')[0]);

    // Initialize DataTable
    function initDataTable() {
        opdBillingTable = $('#opdBillingTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'ajax/opd_billing_api.php',
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
                    width: '70px'
                },
                { 
                    data: 'patient_name',
                    width: '150px'
                },
                { 
                    data: 'patient_phone',
                    width: '110px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'doctor_name',
                    width: '130px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'bill_date',
                    width: '100px',
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : '';
                    }
                },
                { 
                    data: 'total_amount',
                    width: '100px',
                    render: function(data) {
                        return '₹' + parseFloat(data).toFixed(2);
                    }
                },
                { 
                    data: 'paid_amount',
                    width: '100px',
                    render: function(data) {
                        return '₹' + parseFloat(data).toFixed(2);
                    }
                },
                { 
                    data: 'balance_amount',
                    width: '100px',
                    render: function(data) {
                        return '₹' + parseFloat(data).toFixed(2);
                    }
                },
                { 
                    data: 'payment_status',
                    width: '90px',
                    render: function(data, type, row) {
                        let statusClass = 'secondary';
                        let statusIcon = 'question-circle';
                        
                        if (data === 'Paid') {
                            statusClass = 'success';
                            statusIcon = 'check-circle';
                        } else if (data === 'Unpaid') {
                            statusClass = 'danger';
                            statusIcon = 'times-circle';
                        } else if (data === 'Partial') {
                            statusClass = 'warning';
                            statusIcon = 'exclamation-circle';
                        }
                        
                        return `<span class="badge badge-${statusClass}"><i class="fas fa-${statusIcon}"></i> ${data}</span>`;
                    }
                },
                { 
                    data: 'payment_method',
                    width: '90px',
                    defaultContent: 'N/A'
                },
                { 
                    data: 'added_by_username',
                    width: '100px',
                    defaultContent: 'N/A'
                },
                {
                    data: null,
                    orderable: false,
                    width: '120px',
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info view-btn" data-id="${row.id}" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
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
                { targets: [2, 4], className: 'text-left' }
            ]
        });
    }

    // Load statistics
    function loadStats() {
        $.ajax({
            url: 'ajax/opd_billing_api.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success) {
                    $('#totalBills').text(response.data.total);
                    $('#paidBills').text(response.data.paid);
                    $('#unpaidBills').text(response.data.unpaid);
                    $('#partialBills').text(response.data.partial);
                    $('#totalRevenue').text('₹' + response.data.totalRevenue);
                    $('#pendingAmount').text('₹' + response.data.pending);
                }
            },
            error: function() {
                console.error('Error loading stats');
            }
        });
    }

    // Calculate totals
    function calculateTotals() {
        const consultationFee = parseFloat($('#consultationFee').val()) || 0;
        const medicineCharges = parseFloat($('#medicineCharges').val()) || 0;
        const labCharges = parseFloat($('#labCharges').val()) || 0;
        const otherCharges = parseFloat($('#otherCharges').val()) || 0;
        const discount = parseFloat($('#discount').val()) || 0;
        const paidAmount = parseFloat($('#paidAmount').val()) || 0;

        const totalAmount = consultationFee + medicineCharges + labCharges + otherCharges - discount;
        const balanceAmount = totalAmount - paidAmount;

        $('#totalAmount').val('₹' + totalAmount.toFixed(2));
        $('#balanceAmount').val('₹' + balanceAmount.toFixed(2));

        // Update payment status
        let paymentStatus = 'Unpaid';
        if (paidAmount >= totalAmount && totalAmount > 0) {
            paymentStatus = 'Paid';
        } else if (paidAmount > 0) {
            paymentStatus = 'Partial';
        }
        $('#paymentStatus').val(paymentStatus);
    }

    // Bind calculation to charge inputs
    $('.charge-input').on('input', calculateTotals);

    // Add new billing button
    $('#addBillingBtn').click(function() {
        currentBillingId = null;
        $('#billingForm')[0].reset();
        $('#billingId').val('');
        $('#billDate').val(new Date().toISOString().split('T')[0]);
        $('#modalTitle').text('Add New Bill');
        calculateTotals();
        $('#billingModal').modal('show');
    });

    // Form submission
    $('#billingForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize() + '&action=save';
        
        $.ajax({
            url: 'ajax/opd_billing_api.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#billingModal').modal('hide');
                    opdBillingTable.ajax.reload();
                    loadStats();
                } else {
                    toastr.error(response.message || 'Error saving billing record');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response?.message || 'Error saving billing record');
            }
        });
    });

    // View billing
    $(document).on('click', '.view-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/opd_billing_api.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const bill = response.data;
                    let statusClass = 'secondary';
                    
                    if (bill.payment_status === 'Paid') {
                        statusClass = 'success';
                    } else if (bill.payment_status === 'Unpaid') {
                        statusClass = 'danger';
                    } else if (bill.payment_status === 'Partial') {
                        statusClass = 'warning';
                    }
                    
                    let html = `
                        <div class="invoice p-3">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h4 class="text-center mb-3">Bill #${bill.id}</h4>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5>Patient Information</h5>
                                    <p><strong>Name:</strong> ${bill.patient_name || 'N/A'}</p>
                                    <p><strong>Phone:</strong> ${bill.patient_phone || 'N/A'}</p>
                                    <p><strong>Age:</strong> ${bill.patient_age || 'N/A'}</p>
                                    <p><strong>Gender:</strong> ${bill.patient_gender || 'N/A'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Bill Information</h5>
                                    <p><strong>Doctor:</strong> ${bill.doctor_name || 'N/A'}</p>
                                    <p><strong>Bill Date:</strong> ${bill.bill_date ? new Date(bill.bill_date).toLocaleDateString() : 'N/A'}</p>
                                    <p><strong>Payment Method:</strong> ${bill.payment_method || 'N/A'}</p>
                                    <p><strong>Status:</strong> <span class="badge badge-${statusClass}">${bill.payment_status || 'N/A'}</span></p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h5>Charges Breakdown</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Consultation Fee:</td>
                                            <td class="text-right">₹${parseFloat(bill.consultation_fee || 0).toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td>Medicine Charges:</td>
                                            <td class="text-right">₹${parseFloat(bill.medicine_charges || 0).toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td>Lab Charges:</td>
                                            <td class="text-right">₹${parseFloat(bill.lab_charges || 0).toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td>Other Charges:</td>
                                            <td class="text-right">₹${parseFloat(bill.other_charges || 0).toFixed(2)}</td>
                                        </tr>
                                        <tr>
                                            <td>Discount:</td>
                                            <td class="text-right text-danger">-₹${parseFloat(bill.discount || 0).toFixed(2)}</td>
                                        </tr>
                                        <tr class="font-weight-bold">
                                            <td>Total Amount:</td>
                                            <td class="text-right">₹${parseFloat(bill.total_amount || 0).toFixed(2)}</td>
                                        </tr>
                                        <tr class="text-success">
                                            <td>Paid Amount:</td>
                                            <td class="text-right">₹${parseFloat(bill.paid_amount || 0).toFixed(2)}</td>
                                        </tr>
                                        <tr class="font-weight-bold ${parseFloat(bill.balance_amount) > 0 ? 'text-danger' : 'text-success'}">
                                            <td>Balance Amount:</td>
                                            <td class="text-right">₹${parseFloat(bill.balance_amount || 0).toFixed(2)}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            ${bill.notes ? `
                            <div class="row">
                                <div class="col-12">
                                    <h5>Notes</h5>
                                    <p>${bill.notes}</p>
                                </div>
                            </div>
                            ` : ''}
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-muted"><small>Added by: ${bill.added_by_username || 'N/A'} on ${bill.created_at ? new Date(bill.created_at).toLocaleString() : 'N/A'}</small></p>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#viewBillingContent').html(html);
                    currentBillingId = id;
                    $('#viewBillingModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Error loading billing details');
            }
        });
    });

    // Edit billing
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'ajax/opd_billing_api.php',
            type: 'GET',
            data: { action: 'get', id: id },
            success: function(response) {
                if (response.success && response.data) {
                    const bill = response.data;
                    $('#billingId').val(bill.id);
                    $('#patientName').val(bill.patient_name);
                    $('#patientPhone').val(bill.patient_phone);
                    $('#patientAge').val(bill.patient_age);
                    $('#patientGender').val(bill.patient_gender);
                    $('#doctorName').val(bill.doctor_name);
                    $('#billDate').val(bill.bill_date);
                    $('#consultationFee').val(bill.consultation_fee);
                    $('#medicineCharges').val(bill.medicine_charges);
                    $('#labCharges').val(bill.lab_charges);
                    $('#otherCharges').val(bill.other_charges);
                    $('#discount').val(bill.discount);
                    $('#paidAmount').val(bill.paid_amount);
                    $('#paymentMethod').val(bill.payment_method);
                    $('#notes').val(bill.notes);
                    $('#modalTitle').text('Edit Bill');
                    calculateTotals();
                    $('#billingModal').modal('show');
                }
            },
            error: function() {
                toastr.error('Error loading billing details');
            }
        });
    });

    // Edit from view modal
    window.editBillingFromView = function() {
        if (currentBillingId) {
            $('#viewBillingModal').modal('hide');
            $('.edit-btn[data-id="' + currentBillingId + '"]').click();
        }
    };

    // Delete billing
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this billing record?')) {
            $.ajax({
                url: 'ajax/opd_billing_api.php',
                type: 'POST',
                data: { action: 'delete', id: id },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        opdBillingTable.ajax.reload();
                        loadStats();
                    } else {
                        toastr.error(response.message || 'Error deleting billing record');
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    toastr.error(response?.message || 'Error deleting billing record');
                }
            });
        }
    });

    // Print bill details
    window.printBillDetails = function() {
        window.print();
    };

    // Initialize
    initDataTable();
    loadStats();
});
