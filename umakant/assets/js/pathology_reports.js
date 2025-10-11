'use strict';

(function () {
    let reportsTable = null;
    let searchPerformed = false;
    const testSelect = $('#filterTest');
    const doctorSelect = $('#filterDoctor');
    const statusSelect = $('#filterStatus');
    const dateFromInput = $('#filterFrom');
    const dateToInput = $('#filterTo');
    const summaryLabel = $('#reportSummary');
    const form = $('#pathologyReportFilters');

    const apiConfig = {
        listEndpoint: 'patho_api/entry.php',
        testEndpoint: 'ajax/test_api.php',
        doctorEndpoint: 'ajax/doctor_api.php'
    };

    $(document).ready(function () {
        initializeSelect2();
        loadTestOptions();
        loadDoctorOptions();
        initDataTable();
        bindEvents();
    });

    function initializeSelect2() {
        const select2Options = {
            width: '100%',
            allowClear: true,
            placeholder: function () {
                return $(this).data('placeholder') || 'Please select';
            }
        };
        testSelect.select2(select2Options);
        doctorSelect.select2(select2Options);
    }

    function bindEvents() {
        form.on('submit', function (event) {
            event.preventDefault();
            searchPerformed = true;
            reloadTable();
        });

        $('#resetReportFilters').on('click', function () {
            form.trigger('reset');
            testSelect.val('').trigger('change');
            doctorSelect.val('').trigger('change');
            searchPerformed = false;
            reloadTable();
        });
    }

    function loadTestOptions() {
        $.ajax({
            url: apiConfig.testEndpoint,
            method: 'GET',
            dataType: 'json',
            data: { action: 'simple_list' },
            success: function (response) {
                if (!response || !response.success || !Array.isArray(response.data)) {
                    console.warn('Unexpected tests response', response);
                    return;
                }
                populateSelect(testSelect, response.data.map(function (item) {
                    return { id: item.id, text: item.name };
                }));
            },
            error: function (xhr, status, error) {
                console.error('Failed to load tests', status, error, xhr.responseText);
                // Show user-friendly error message
                toastr.error('Failed to load test options. Please refresh the page or contact support.');
            }
        });
    }

    function loadDoctorOptions() {
        $.ajax({
            url: apiConfig.doctorEndpoint,
            method: 'GET',
            dataType: 'json',
            data: { action: 'simple_list' },
            success: function (response) {
                if (!response || !response.success || !Array.isArray(response.data)) {
                    console.warn('Unexpected doctors response', response);
                    return;
                }
                populateSelect(doctorSelect, response.data.map(function (item) {
                    const displayName = item.name || ('Doctor #' + item.id);
                    return { id: item.id, text: displayName };
                }));
            },
            error: function (xhr, status, error) {
                console.error('Failed to load doctors', status, error, xhr.responseText);
                // Show user-friendly error message
                toastr.error('Failed to load doctor options. Please refresh the page or contact support.');
            }
        });
    }

    function populateSelect($select, options) {
        const currentValue = $select.val();
        $select.empty();
        $select.append(new Option($select.data('placeholder') || 'All', '', true, false));
        options.forEach(function (opt) {
            $select.append(new Option(opt.text, opt.id, false, false));
        });
        if (currentValue) {
            $select.val(currentValue).trigger('change');
        }
    }

    function initDataTable() {
        reportsTable = $('#pathologyReportsTable').DataTable({
            processing: true,
            serverSide: false,
            deferRender: true,
            ajax: function (data, callback) {
                if (!searchPerformed) {
                    callback({ data: [] });
                    updateSummary(null);
                    return;
                }
                fetchReports(function (rows, meta) {
                    callback({
                        data: rows
                    });
                    updateSummary(meta);
                });
            },
            columns: [
                {
                    data: 'entry_id',
                    render: function (data) {
                        return '<span class="badge badge-primary">#' + data + '</span>';
                    }
                },
                { data: 'entry_date_formatted' },
                { data: 'patient_name' },
                {
                    data: 'doctor_name',
                    render: function (data) {
                        return data || '<span class="text-muted">—</span>';
                    }
                },
                { data: 'test_name' },
                {
                    data: 'result_display',
                    render: function (data) {
                        return data || '<span class="text-muted">Pending</span>';
                    }
                },
                {
                    data: 'entry_status',
                    render: function (data, type, row) {
                        const status = (data || '').toLowerCase();
                        const map = {
                            pending: 'warning',
                            completed: 'success',
                            cancelled: 'danger'
                        };
                        const badgeClass = map[status] || 'secondary';
                        return '<span class="badge badge-' + badgeClass + '">' + status + '</span>';
                    }
                },
                {
                    data: 'amount',
                    render: function (data) {
                        const amount = parseFloat(data || 0);
                        return '₹' + amount.toFixed(2);
                    }
                }
            ],
            order: [[1, 'desc']],
            language: {
                processing: 'Loading reports...',
                emptyTable: 'No data available - Please use the search filters above'
            }
        });
    }

    function reloadTable() {
        if (reportsTable) {
            reportsTable.ajax.reload();
        }
    }

    function fetchReports(onSuccess) {
        const payload = {
            action: 'report_list',
            test_id: testSelect.val(),
            doctor_id: doctorSelect.val(),
            status: statusSelect.val(),
            date_from: dateFromInput.val(),
            date_to: dateToInput.val()
        };

        $.ajax({
            url: apiConfig.listEndpoint,
            method: 'GET',
            data: payload,
            dataType: 'json',
            success: function (response) {
                if (!response || !response.success) {
                    console.warn('Report list returned error', response);
                    onSuccess([], { total_records: 0, total_amount_formatted: '0.00' });
                    return;
                }
                onSuccess(response.data || [], response.summary || {});
            },
            error: function (xhr, status, error) {
                console.error('Failed to fetch reports', status, error, xhr.responseText);
                // Show user-friendly error message
                toastr.error('Failed to fetch reports. Please check your connection and try again.');
                onSuccess([], { total_records: 0, total_amount_formatted: '0.00' });
            }
        });
    }

    function updateSummary(summary) {
        if (!searchPerformed) {
            summaryLabel.text('No search applied - Use filters above to search reports');
            return;
        }
        if (!summary) {
            summaryLabel.text('No records found');
            return;
        }
        const totalRecords = summary.total_records || 0;
        const totalAmount = summary.total_amount_formatted || '0.00';
        summaryLabel.text('Records: ' + totalRecords + ' | Total Amount: ₹' + totalAmount);
    }
})();
