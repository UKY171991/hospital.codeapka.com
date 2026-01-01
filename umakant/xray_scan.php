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
                    <!-- Placeholder for scan functionality -->
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Info</h5>
                        Select a patient to start text-ray scanning.
                    </div>
                    
                    <form>
                        <div class="form-group">
                            <label for="patientSelect">Select Patient</label>
                            <select class="form-control" id="patientSelect">
                                <option>Select Patient...</option>
                                <!-- Populate with patients -->
                            </select>
                        </div>
                         <div class="form-group">
                            <label for="bodyPart">Body Part</label>
                            <input type="text" class="form-control" id="bodyPart" placeholder="e.g., Chest, Hand">
                        </div>
                        <div class="form-group">
                            <label for="scanNotes">Notes</label>
                            <textarea class="form-control" id="scanNotes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Start Scan</button>
                    </form>

                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'inc/footer.php'; ?>
