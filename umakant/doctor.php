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
                                <table id="doctorsTable" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Qualification</th>
                                            <th>Specialization</th>
                                            <th>Hospital</th>
                                            <th>Contact</th>
                                            <th>Percentage</th>
                                            <th>Actions</th>
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
<div class="modal fade" id="viewDoctorModal" tabindex="-1" role="dialog" aria-labelledby="viewDoctorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewDoctorModalLabel">
                    <i class="fas fa-eye mr-2"></i>
                    Doctor Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewDoctorContent">
                <!-- Doctor details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
let currentPage = 1;
let totalRecords = 0;
let recordsPerPage = 10;
let searchTimeout;

// Initialize page
$(document).ready(function() {
    loadDoctors();
    loadFilters();
    initializeEventListeners();
});

function initializeEventListeners() {
    // Search functionality
    $('#doctorsSearch').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadDoctors();
        }, 300);
    });

    // Filter functionality
    $('#specializationFilter, #hospitalFilter').on('change', function() {
        currentPage = 1;
        loadDoctors();
    });

    // Form submission
    $('#doctorForm').on('submit', function(e) {
        e.preventDefault();
        saveDoctorData();
    });
}

function loadDoctors() {
    const searchTerm = $('#doctorsSearch').val();
    const specialization = $('#specializationFilter').val();
    const hospital = $('#hospitalFilter').val();

    // Show loading
    $('#doctorsTableBody').html('<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');

    const params = new URLSearchParams({
        page: currentPage,
        limit: recordsPerPage,
        ...(searchTerm && { search: searchTerm }),
        ...(specialization && { specialization: specialization }),
        ...(hospital && { hospital: hospital })
    });

    $.get(`patho_api/doctor.php?${params}`)
        .done(function(response) {
            if (response.status === 'success') {
                populateDoctorsTable(response.data);
                updatePagination(response.pagination);
                updateStats();
            } else {
                showAlert('Error loading doctors: ' + response.message, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load doctors', 'error');
            $('#doctorsTableBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load data</td></tr>');
        });
}

function populateDoctorsTable(doctors) {
    let html = '';
    
    if (doctors.length === 0) {
        html = '<tr><td colspan="8" class="text-center text-muted">No doctors found</td></tr>';
    } else {
        doctors.forEach(doctor => {
            html += `
                <tr>
                    <td>${doctor.id}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle bg-primary text-white mr-2">
                                ${doctor.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="font-weight-bold">${doctor.name}</div>
                                ${doctor.registration_no ? `<small class="text-muted">Reg: ${doctor.registration_no}</small>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${doctor.qualification || '-'}</td>
                    <td>
                        ${doctor.specialization ? `<span class="badge badge-info">${doctor.specialization}</span>` : '-'}
                    </td>
                    <td>${doctor.hospital || '-'}</td>
                    <td>
                        <div>
                            ${doctor.contact_no ? `<div><i class="fas fa-phone text-primary"></i> ${doctor.contact_no}</div>` : ''}
                            ${doctor.email ? `<div><i class="fas fa-envelope text-info"></i> ${doctor.email}</div>` : ''}
                        </div>
                    </td>
                    <td>
                        ${doctor.percent ? `<span class="badge badge-success">${doctor.percent}%</span>` : '-'}
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm" onclick="viewDoctor(${doctor.id})" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editDoctor(${doctor.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteDoctor(${doctor.id}, '${doctor.name}')" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    }
    
    $('#doctorsTableBody').html(html);
}

function updatePagination(pagination) {
    totalRecords = pagination.total;
    const totalPages = Math.ceil(totalRecords / recordsPerPage);
    
    // Update info
    const start = ((currentPage - 1) * recordsPerPage) + 1;
    const end = Math.min(currentPage * recordsPerPage, totalRecords);
    $('#doctorsInfo').html(`Showing ${start} to ${end} of ${totalRecords} entries`);
    
    // Update pagination
    let paginationHtml = '';
    if (totalPages > 1) {
        paginationHtml += `
            <ul class="pagination pagination-sm m-0 float-right">
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a>
                </li>
        `;
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                paginationHtml += `
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                    </li>
                `;
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        paginationHtml += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a>
                </li>
            </ul>
        `;
    }
    
    $('#doctorsPagination').html(paginationHtml);
}

function updateStats() {
    $.get('patho_api/doctor.php?action=stats')
        .done(function(response) {
            if (response.status === 'success') {
                $('#totalDoctors').text(response.data.total || 0);
                $('#activeDoctors').text(response.data.active || 0);
                $('#specializations').text(response.data.specializations || 0);
                $('#hospitals').text(response.data.hospitals || 0);
            }
        });
}

function loadFilters() {
    // Load specializations
    $.get('patho_api/doctor.php?action=specializations')
        .done(function(response) {
            if (response.status === 'success') {
                let options = '<option value="">All Specializations</option>';
                response.data.forEach(spec => {
                    options += `<option value="${spec}">${spec}</option>`;
                });
                $('#specializationFilter').html(options);
            }
        });

    // Load hospitals
    $.get('patho_api/doctor.php?action=hospitals')
        .done(function(response) {
            if (response.status === 'success') {
                let options = '<option value="">All Hospitals</option>';
                response.data.forEach(hospital => {
                    options += `<option value="${hospital}">${hospital}</option>`;
                });
                $('#hospitalFilter').html(options);
            }
        });
}

function changePage(page) {
    currentPage = page;
    loadDoctors();
}

function clearFilters() {
    $('#doctorsSearch').val('');
    $('#specializationFilter').val('');
    $('#hospitalFilter').val('');
    currentPage = 1;
    loadDoctors();
}

function openAddDoctorModal() {
    $('#doctorForm')[0].reset();
    $('#doctorId').val('');
    $('#modalTitle').text('Add New Doctor');
    $('#doctorModal').modal('show');
}

function editDoctor(id) {
    $.get(`patho_api/doctor.php?id=${id}`)
        .done(function(response) {
            if (response.status === 'success') {
                const doctor = response.data;
                $('#doctorId').val(doctor.id);
                $('#doctorName').val(doctor.name);
                $('#doctorQualification').val(doctor.qualification);
                $('#doctorSpecialization').val(doctor.specialization);
                $('#doctorHospital').val(doctor.hospital);
                $('#doctorContact').val(doctor.contact_no);
                $('#doctorPhone').val(doctor.phone);
                $('#doctorEmail').val(doctor.email);
                $('#doctorPercent').val(doctor.percent);
                $('#doctorRegistration').val(doctor.registration_no);
                $('#doctorAddress').val(doctor.address);
                
                $('#modalTitle').text('Edit Doctor');
                $('#doctorModal').modal('show');
            } else {
                showAlert('Error loading doctor data: ' + response.message, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load doctor data', 'error');
        });
}

function viewDoctor(id) {
    $.get(`patho_api/doctor.php?id=${id}`)
        .done(function(response) {
            if (response.status === 'success') {
                const doctor = response.data;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td><strong>Name:</strong></td><td>${doctor.name}</td></tr>
                                <tr><td><strong>Qualification:</strong></td><td>${doctor.qualification || '-'}</td></tr>
                                <tr><td><strong>Specialization:</strong></td><td>${doctor.specialization || '-'}</td></tr>
                                <tr><td><strong>Hospital:</strong></td><td>${doctor.hospital || '-'}</td></tr>
                                <tr><td><strong>Registration No:</strong></td><td>${doctor.registration_no || '-'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr><td><strong>Contact:</strong></td><td>${doctor.contact_no || '-'}</td></tr>
                                <tr><td><strong>Phone:</strong></td><td>${doctor.phone || '-'}</td></tr>
                                <tr><td><strong>Email:</strong></td><td>${doctor.email || '-'}</td></tr>
                                <tr><td><strong>Commission:</strong></td><td>${doctor.percent ? doctor.percent + '%' : '-'}</td></tr>
                                <tr><td><strong>Created:</strong></td><td>${formatDateTime(doctor.created_at)}</td></tr>
                            </table>
                        </div>
                    </div>
                    ${doctor.address ? `<div class="row"><div class="col-12"><strong>Address:</strong><br>${doctor.address}</div></div>` : ''}
                `;
                $('#viewDoctorContent').html(content);
                $('#viewDoctorModal').modal('show');
            } else {
                showAlert('Error loading doctor data: ' + response.message, 'error');
            }
        })
        .fail(function() {
            showAlert('Failed to load doctor data', 'error');
        });
}

function saveDoctorData() {
    const formData = new FormData($('#doctorForm')[0]);
    const id = $('#doctorId').val();
    const method = id ? 'PUT' : 'POST';
    
    // Add loading state
    const submitBtn = $('#doctorForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: 'patho_api/doctor.php',
        type: method,
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                showAlert(id ? 'Doctor updated successfully!' : 'Doctor added successfully!', 'success');
                $('#doctorModal').modal('hide');
                loadDoctors();
            } else {
                showAlert('Error: ' + response.message, 'error');
            }
        },
        error: function() {
            showAlert('Failed to save doctor data', 'error');
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function deleteDoctor(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete doctor "${name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `patho_api/doctor.php?id=${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.status === 'success') {
                        showAlert('Doctor deleted successfully!', 'success');
                        loadDoctors();
                    } else {
                        showAlert('Error deleting doctor: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showAlert('Failed to delete doctor', 'error');
                }
            });
        }
    });
}

function showAlert(message, type) {
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    
    const alert = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} mr-2"></i>${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top of content
    $('.content-wrapper .content').prepend(alert);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}
</script>

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

<script>
function loadDoctors(){
    $.get('ajax/doctor_api.php',{action:'list'},function(resp){
    if(resp.success){ var t=''; resp.data.forEach(function(r){ t += '<tr>'+
            '<td>'+r.id+'</td>'+
            '<td>'+ (r.name||'') +'</td>'+
            '<td>'+ (r.qualification||'') +'</td>'+
            '<td>'+ (r.specialization||'') +'</td>'+
            '<td>'+ (r.hospital||'') +'</td>'+
            '<td>'+ (r.contact_no||r.phone||'') +'</td>'+
            '<td>'+ (r.percent||'') +'</td>'+
            '<td>'+ (r.email||'') +'</td>'+
            '<td><button class="btn btn-sm btn-info view-doctor" data-id="'+r.id+'">View</button> '+
                '<button class="btn btn-sm btn-warning edit-doctor" data-id="'+r.id+'">Edit</button> '+
                '<button class="btn btn-sm btn-danger delete-doctor" data-id="'+r.id+'">Delete</button></td>'+
            '</tr>'; }); $('#doctorsTable tbody').html(t);} else toastr.error('Failed to load'); },'json');
}

function openAddDoctorModal(){ $('#doctorForm')[0].reset(); $('#doctorId').val(''); $('#doctorModal').modal('show'); }

$(function(){
    loadDoctors();
    $('#saveDoctorBtn').click(function(){ var data=$('#doctorForm').serialize() + '&action=save'; $.post('ajax/doctor_api.php', data, function(resp){ if(resp.success){ toastr.success(resp.message||'Saved'); $('#doctorModal').modal('hide'); loadDoctors(); } else toastr.error(resp.message||'Save failed'); }, 'json').fail(function(xhr){ var msg = xhr.responseText || 'Server error'; try{ var j=JSON.parse(xhr.responseText||'{}'); if(j.message) msg=j.message;}catch(e){} toastr.error(msg); }); });

    $('#doctorsTable').on('click', '.edit-doctor', function(){ var id=$(this).data('id'); $.get('ajax/doctor_api.php',{action:'get',id:id}, function(resp){ if(resp.success){ var d=resp.data; $('#doctorId').val(d.id); $('#doctorName').val(d.name); $('#doctorQualification').val(d.qualification); $('#doctorSpecialization').val(d.specialization); $('#doctorHospital').val(d.hospital); $('#doctorContact').val(d.contact_no); $('#doctorPhone').val(d.phone); $('#doctorPercent').val(d.percent); $('#doctorEmail').val(d.email); $('#doctorRegistration').val(d.registration_no); $('#doctorAddress').val(d.address); $('#doctorModal').modal('show'); } else toastr.error('Doctor not found'); },'json'); });

    $('#doctorsTable').on('click', '.delete-doctor', function(){ if(!confirm('Delete doctor?')) return; var id=$(this).data('id'); $.post('ajax/doctor_api.php',{action:'delete',id:id}, function(resp){ if(resp.success){ toastr.success(resp.message); loadDoctors(); } else toastr.error(resp.message||'Delete failed'); },'json'); });
});
</script>
