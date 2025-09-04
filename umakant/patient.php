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
                    <h1><i class="fas fa-users mr-2"></i>Patient Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Patients</li>
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
                            <h3 id="totalPatients">0</h3>
                            <p>Total Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="todayPatients">0</h3>
                            <p>Today's Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="malePatients">0</h3>
                            <p>Male Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-male"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="femalePatients">0</h3>
                            <p>Female Patients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-female"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-hospital-user mr-1"></i>
                                Patient Directory
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#patientModal" onclick="openAddPatientModal()">
                                    <i class="fas fa-plus"></i> Add New Patient
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
                            <!-- Search and Filter Section -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input id="patientsSearch" class="form-control" placeholder="Search by name, mobile, UHID...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select id="genderFilter" class="form-control">
                                        <option value="">All Genders</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="ageRangeFilter" class="form-control">
                                        <option value="">All Ages</option>
                                        <option value="0-18">0-18 years</option>
                                        <option value="19-35">19-35 years</option>
                                        <option value="36-60">36-60 years</option>
                                        <option value="60+">60+ years</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" id="dateFilter" class="form-control" title="Filter by registration date">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary btn-block" onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>

                            <!-- Patients Table -->
                            <div class="table-responsive">
                                <table id="patientsTable" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>UHID</th>
                                            <th>Patient Details</th>
                                            <th>Contact</th>
                                            <th>Age/Gender</th>
                                            <th>Address</th>
                                            <th>Registration</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patientsTableBody">
                                        <!-- Dynamic content will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info" id="patientsInfo"></div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="patientsPagination">
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
<!-- Patient Modal -->
<div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="patientModalLabel">
                    <i class="fas fa-user-plus mr-2"></i>
                    <span id="modalTitle">Add New Patient</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="patientForm">
                <div class="modal-body">
                    <input type="hidden" id="patientId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientName">
                                    <i class="fas fa-user mr-1"></i>
                                    Full Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="patientName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientUHID">
                                    <i class="fas fa-id-card mr-1"></i>
                                    UHID
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="patientUHID" name="uhid" readonly>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" onclick="generateUHID()">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientMobile">
                                    <i class="fas fa-mobile-alt mr-1"></i>
                                    Mobile Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="patientMobile" name="mobile" required pattern="[0-9]{10}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientEmail">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control" id="patientEmail" name="email">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="patientAge">
                                    <i class="fas fa-birthday-cake mr-1"></i>
                                    Age
                                </label>
                                <input type="number" class="form-control" id="patientAge" name="age" min="0" max="150">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="patientAgeUnit">Age Unit</label>
                                <select class="form-control" id="patientAgeUnit" name="age_unit">
                                    <option value="Years">Years</option>
                                    <option value="Months">Months</option>
                                    <option value="Days">Days</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="patientGender">
                                    <i class="fas fa-venus-mars mr-1"></i>
                                    Gender
                                </label>
                                <select class="form-control" id="patientGender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="patientFatherHusband">
                            <i class="fas fa-user-friends mr-1"></i>
                            Father/Husband Name
                        </label>
                        <input type="text" class="form-control" id="patientFatherHusband" name="father_husband">
                    </div>

                    <div class="form-group">
                        <label for="patientAddress">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            Address
                        </label>
                        <textarea class="form-control" id="patientAddress" name="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Patient Modal -->
<div class="modal fade" id="viewPatientModal" tabindex="-1" role="dialog" aria-labelledby="viewPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewPatientModalLabel">
                    <i class="fas fa-eye mr-2"></i>
                    Patient Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewPatientContent">
                <!-- Patient details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Page specific CSS -->
<link rel="stylesheet" href="assets/css/patient.css">

<?php require_once 'inc/footer.php'; ?>