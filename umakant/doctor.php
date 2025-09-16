<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-user-md mr-2"></i>Doctor Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Doctors</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Stats Row -->
            <div class="row mb-4">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalDoctors">0</h3>
                            <p>Total Doctors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="activeDoctors">0</h3>
                            <p>Active Doctors</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="specializations">0</h3>
                            <p>Specializations</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="hospitals">0</h3>
                            <p>Hospitals</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-users mr-1"></i>
                                Doctors Directory
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#doctorModal" onclick="openAddDoctorModal()">
                                    <i class="fas fa-plus"></i> Add New Doctor
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="exportDoctors()">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            
                            <!-- Group Actions -->
                            <div class="group-actions">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-primary" onclick="selectAllDoctors()">
                                                <i class="fas fa-check-square"></i> Select All
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="deselectAllDoctors()">
                                                <i class="fas fa-square"></i> Deselect All
                                            </button>
                                        </div>
                                        <div class="btn-group ml-2" role="group">
                                            <button type="button" class="btn btn-outline-info" onclick="bulkExportDoctors()">
                                                <i class="fas fa-download"></i> Export Selected
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" onclick="bulkDeleteDoctors()">
                                                <i class="fas fa-trash"></i> Delete Selected
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <small class="text-muted">Select doctors to perform bulk actions</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Actions Alert -->
                            <div class="bulk-actions">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-info-circle"></i>
                                        <span class="selected-count">0</span> doctors selected
                                    </span>
                                    <div>
                                        <button class="btn btn-sm btn-info bulk-export">
                                            <i class="fas fa-download"></i> Export
                                        </button>
                                        <button class="btn btn-sm btn-danger bulk-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Search and Filter Section -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input id="doctorsSearch" class="form-control" placeholder="Search doctors...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select id="specializationFilter" class="form-control">
                                        <option value="">All Specializations</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select id="hospitalFilter" class="form-control">
                                        <option value="">All Hospitals</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary btn-block" onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>

                            <!-- Doctors Table -->
                            <div class="table-responsive">
                                <table id="doctorsTable" class="table table-enhanced">
                                    <thead>
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="selectAll" class="selection-checkbox">
                                            </th>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Qualification</th>
                                            <th>Specialization</th>
                                            <th>Hospital</th>
                                            <th>Contact</th>
                                            <th>Percentage</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="doctorsTableBody">
                                        <!-- Dynamic content will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info" id="doctorsInfo"></div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="doctorsPagination">
                                        <!-- Pagination will be inserted here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Doctor Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="doctorModalLabel">
                    <i class="fas fa-user-md mr-2"></i>
                    <span id="modalTitle">Add New Doctor</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="doctorForm">
                <div class="modal-body">
                    <input type="hidden" id="doctorId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorName">
                                    <i class="fas fa-user mr-1"></i>
                                    Doctor Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="doctorName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorQualification">
                                    <i class="fas fa-graduation-cap mr-1"></i>
                                    Qualification
                                </label>
                                <input type="text" class="form-control" id="doctorQualification" name="qualification">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorSpecialization">
                                    <i class="fas fa-stethoscope mr-1"></i>
                                    Specialization
                                </label>
                                <input type="text" class="form-control" id="doctorSpecialization" name="specialization">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorHospital">
                                    <i class="fas fa-hospital mr-1"></i>
                                    Hospital
                                </label>
                                <input type="text" class="form-control" id="doctorHospital" name="hospital">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorContact">
                                    <i class="fas fa-phone mr-1"></i>
                                    Contact Number
                                </label>
                                <input type="text" class="form-control" id="doctorContact" name="contact_no">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorPhone">
                                    <i class="fas fa-mobile-alt mr-1"></i>
                                    Phone
                                </label>
                                <input type="text" class="form-control" id="doctorPhone" name="phone">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorEmail">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control" id="doctorEmail" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorPercent">
                                    <i class="fas fa-percentage mr-1"></i>
                                    Commission Percentage
                                </label>
                                <input type="number" class="form-control" id="doctorPercent" name="percent" min="0" max="100" step="0.01">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorRegistration">
                                    <i class="fas fa-id-card mr-1"></i>
                                    Registration Number
                                </label>
                                <input type="text" class="form-control" id="doctorRegistration" name="registration_no">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="doctorAddress">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            Address
                        </label>
                        <textarea class="form-control" id="doctorAddress" name="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Doctor Modal -->
<div class="modal fade view-modal modal-enhanced" id="viewDoctorModal" tabindex="-1" role="dialog" aria-labelledby="viewDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDoctorModalLabel">
                    <i class="fas fa-eye mr-2"></i>
                    Doctor Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewDoctorContent">
                <div class="view-details" id="doctorViewDetails">
                    <!-- Doctor details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-warning" onclick="editDoctorFromView()">
                    <i class="fas fa-edit"></i> Edit Doctor
                </button>
                <button type="button" class="btn btn-info" onclick="printDoctorDetails()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>
            

<!-- Page specific CSS -->
<link rel="stylesheet" href="assets/css/doctor.css">

<!-- Page specific JavaScript -->
<script src="assets/js/doctor.js"></script>

<style>
.avatar-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.small-box {
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.small-box > .inner {
    padding: 10px;
}

.small-box > .small-box-footer {
    position: relative;
    text-align: center;
    padding: 3px 0;
    color: #fff;
    color: rgba(255,255,255,0.8);
    display: block;
    z-index: 10;
    background: rgba(0,0,0,0.1);
    text-decoration: none;
}

.small-box .icon {
    transition: all .3s linear;
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 90px;
    color: rgba(0,0,0,0.15);
}

.table-responsive {
    border-radius: 0.375rem;
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}
</style>

<?php require_once 'inc/footer.php'; ?>
                                        <div class="input-group-append">
                                            <button id="doctorsSearchClear" class="btn btn-outline-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto text-right">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="doctorsPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <table id="doctorsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Qualification</th>
                                        <th>Specialization</th>
                                        <th>Hospital</th>
                                        <th>Contact</th>
                                        <th>Percent</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Doctor Modal -->
<div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="doctorModalLabel">Add Doctor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="doctorForm">
                    <input type="hidden" id="doctorId" name="id">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doctorName">Name *</label>
                                    <input type="text" class="form-control" id="doctorName" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="doctorQualification">Qualification</label>
                                    <input type="text" class="form-control" id="doctorQualification" name="qualification">
                                </div>
                                <div class="form-group">
                                    <label for="doctorHospital">Hospital</label>
                                    <input type="text" class="form-control" id="doctorHospital" name="hospital">
                                </div>
                                <div class="form-group">
                                    <label for="doctorPhone">Phone</label>
                                    <input type="text" class="form-control" id="doctorPhone" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="doctorSpecialization">Specialization</label>
                                    <input type="text" class="form-control" id="doctorSpecialization" name="specialization">
                                </div>
                                <div class="form-group">
                                    <label for="doctorContact">Contact No</label>
                                    <input type="text" class="form-control" id="doctorContact" name="contact_no">
                                </div>
                                <div class="form-group">
                                    <label for="doctorPercent">Percent</label>
                                    <input type="number" step="0.01" class="form-control" id="doctorPercent" name="percent" value="0.00">
                                </div>
                                <div class="form-group">
                                    <label for="doctorEmail">Email</label>
                                    <input type="email" class="form-control" id="doctorEmail" name="email">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="doctorRegistration">Registration No</label>
                                    <input type="text" class="form-control" id="doctorRegistration" name="registration_no">
                                </div>
                                <div class="form-group">
                                    <label for="doctorAddress">Address</label>
                                    <textarea class="form-control" id="doctorAddress" name="address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDoctorBtn">Save Doctor</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'inc/footer.php'; ?>
