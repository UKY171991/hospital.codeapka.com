<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-print mr-2"></i>X-Ray Print</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="xray_dashboard.php">X-Ray</a></li>
                        <li class="breadcrumb-item active">Print</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="text-muted small">Review and print finalized X-ray reports.</div>
                </div>
                <div class="col-md-4 text-md-right">
                    <button type="button" class="btn btn-primary btn-sm" id="printAllBtn">
                        <i class="fas fa-print mr-1"></i>Print All Ready
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="filter-row">
                        <div class="form-group mb-0">
                            <label for="printSearch" class="mb-1">Search</label>
                            <input type="text" class="form-control form-control-sm" id="printSearch" placeholder="Search by patient or scan ID">
                        </div>
                        <div class="form-group mb-0">
                            <label for="statusFilter" class="mb-1">Status</label>
                            <select class="form-control form-control-sm" id="statusFilter">
                                <option value="">All</option>
                                <option value="Ready">Ready</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label for="dateFilter" class="mb-1">Date</label>
                            <input type="date" class="form-control form-control-sm" id="dateFilter">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pending Prints</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped" id="printQueueTable">
                        <thead>
                            <tr>
                                <th>Scan ID</th>
                                <th>Patient Name</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-table-state">
                                        <i class="fas fa-print"></i>
                                        <div>No pending prints found.</div>
                                        <a href="xray_scan.php" class="btn btn-sm btn-outline-primary mt-2">Start a scan</a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .filter-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .empty-table-state {
        padding: 2rem;
        text-align: center;
        color: #6c757d;
    }

    .empty-table-state i {
        font-size: 1.75rem;
        color: #adb5bd;
        margin-bottom: 0.5rem;
    }
</style>

<script>
$(document).ready(function() {
    const filterTable = () => {
        const search = $('#printSearch').val().toLowerCase();
        const status = $('#statusFilter').val();
        const date = $('#dateFilter').val();

        $('#printQueueTable tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            const rowStatus = $(this).find('td:nth-child(4)').text().trim();
            const rowDate = $(this).find('td:nth-child(3)').text().trim();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatus = !status || rowStatus === status;
            const matchesDate = !date || rowDate === date;

            $(this).toggle(matchesSearch && matchesStatus && matchesDate);
        });
    };

    $('#printSearch, #statusFilter, #dateFilter').on('input change', filterTable);

    $('#printAllBtn').on('click', function() {
        toastr.info('Printing all ready reports...');
    });
});
</script>

<?php require_once 'inc/footer.php'; ?>
