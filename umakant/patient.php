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
        </div>
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

            <!-- Main Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-hospital-user mr-1"></i>
                                Patient Directory
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-success btn-sm" onclick="exportPatients()">
                                    <i class="fas fa-download"></i> Export All
                                </button>
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- Search and Filter Section -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Search Patients</label>
                                        <input type="text" id="patientsSearch" class="form-control" placeholder="Search by name, mobile, UHID...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select id="genderFilter" class="form-control">
                                            <option value="">All Genders</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Age Range</label>
                    <select id="ageRangeFilter" class="form-control">
                                            <option value="">All Ages</option>
                                            <option value="0-18">0-18 years</option>
                                            <option value="19-35">19-35 years</option>
                                            <option value="36-60">36-60 years</option>
                                            <option value="60+">60+ years</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Registration Date</label>
                                        <input type="date" id="dateFilter" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="filterAddedBy">Added By</label>
                                        <select id="filterAddedBy" class="form-control">
                                            <option value="">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                        <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-secondary btn-block" onclick="clearFilters()">
                                            <i class="fas fa-times"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Actions -->
                            <div id="bulkActions" class="alert alert-info bulk-actions" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="selected-count">0</span> patients selected
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button class="btn btn-sm btn-info bulk-export">
                                            <i class="fas fa-download"></i> Export Selected
                                        </button>
                                        <button class="btn btn-sm btn-danger bulk-delete">
                                            <i class="fas fa-trash"></i> Delete Selected
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Patients Table -->
                            <div class="table-responsive">
                                <table id="patientsTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="40px">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th>UHID</th>
                                            <th>Patient Details</th>
                                            <th>Contact</th>
                                            <th>Age/Gender</th>
                                            <th>Address</th>
                                            <th>Registration</th>
                                            <th>Added By</th>
                                            <th width="120px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patientsTableBody">
                                        <!-- Dynamic content will be loaded here -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Loading indicator -->
                            <div id="loadingIndicator" class="text-center" style="display: none;">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p>Loading patients...</p>
                            </div>

                            <!-- No data message -->
                            <div id="noDataMessage" class="text-center" style="display: none;">
                                <i class="fas fa-users fa-3x text-muted"></i>
                                <p class="text-muted">No patients found</p>
                            </div>
                        </div>

                        <!-- Card Footer with Pagination -->
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div id="paginationInfo"></div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <nav>
                                        <ul id="pagination" class="pagination pagination-sm m-0 float-right">
                                            <!-- Pagination will be inserted here -->
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- View Patient Modal -->
<div class="modal fade" id="viewPatientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>
                    Patient Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="patientViewDetails">
                <!-- Patient details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-info" onclick="printPatientDetails()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include custom CSS and JavaScript -->
<link rel="stylesheet" href="assets/css/patient.css">

<?php require_once 'inc/footer.php'; ?>