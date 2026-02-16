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
                    <h1><i class="fas fa-x-ray mr-2"></i>X-Ray Scan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="xray_dashboard.php">X-Ray</a></li>
                        <li class="breadcrumb-item active">Scan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">New X-Ray Scan</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Info</h5>
                        Select a patient and body part to start a safe, verified X-ray scan.
                    </div>

                    <div class="row">
                        <div class="col-lg-7">
                            <form id="xrayScanForm">
                                <div class="form-group">
                                    <label for="patientSelect">Select Patient <span class="text-danger">*</span></label>
                                    <select class="form-control" id="patientSelect" required>
                                        <option value="">Select Patient...</option>
                                        <!-- Populate with patients -->
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="bodyPart">Body Part <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="bodyPart" placeholder="e.g., Chest, Hand" required>
                                </div>
                                <div class="form-group">
                                    <label for="scanNotes">Notes</label>
                                    <textarea class="form-control" id="scanNotes" rows="3" placeholder="Include positioning instructions or clinical notes."></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="scanPriority">Priority</label>
                                        <select class="form-control" id="scanPriority">
                                            <option>Routine</option>
                                            <option>Urgent</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="scanTechnician">Technician</label>
                                        <input type="text" class="form-control" id="scanTechnician" placeholder="Assigned staff">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-play mr-1"></i>Start Scan
                                </button>
                                <span class="text-muted small ml-2" id="scanStatus"></span>
                            </form>
                        </div>
                        <div class="col-lg-5">
                            <div class="card card-outline card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-clipboard-check mr-1"></i>
                                        Scan Checklist
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <ul class="timeline mb-0">
                                        <li class="time-label">
                                            <span class="bg-info">Step 1</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-user bg-primary"></i>
                                            <div class="timeline-item">
                                                <h3 class="timeline-header">Confirm patient identity</h3>
                                                <div class="timeline-body">Verify name, age, and referral details.</div>
                                            </div>
                                        </li>
                                        <li class="time-label">
                                            <span class="bg-info">Step 2</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-camera bg-success"></i>
                                            <div class="timeline-item">
                                                <h3 class="timeline-header">Position &amp; capture</h3>
                                                <div class="timeline-body">Take at least two angles for clarity.</div>
                                            </div>
                                        </li>
                                        <li class="time-label">
                                            <span class="bg-info">Step 3</span>
                                        </li>
                                        <li>
                                            <i class="fas fa-file-medical bg-warning"></i>
                                            <div class="timeline-item">
                                                <h3 class="timeline-header">Review &amp; submit</h3>
                                                <div class="timeline-body">Flag urgent findings for the print queue.</div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .timeline {
        list-style: none;
        padding: 0;
    }
    .timeline .timeline-item {
        margin-left: 45px;
        padding: 10px 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    .timeline .time-label span {
        font-weight: 600;
    }
</style>

<script>
$(document).ready(function() {
    $('#xrayScanForm').on('submit', function(event) {
        event.preventDefault();
        const patient = $('#patientSelect').val();
        const bodyPart = $('#bodyPart').val();

        if (!patient || !bodyPart) {
            $('#scanStatus').text('Please complete the required fields.').addClass('text-danger');
            return;
        }

        $('#scanStatus')
            .removeClass('text-danger')
            .addClass('text-success')
            .text('Scan started. Continue with image capture.');
    });
});
</script>

<?php require_once 'inc/footer.php'; ?>
