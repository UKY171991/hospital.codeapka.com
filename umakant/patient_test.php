<?php
// patient_test.php - Test version using SQLite
session_start();

// For testing purposes, set a mock session
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management - Test Version</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/patient.css" rel="stylesheet">
    <link href="assets/css/global-improvements.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-primary">
                <i class="fas fa-users"></i> Patient Management (Test Version)
            </h1>
            <button class="btn btn-success" data-toggle="modal" data-target="#patientModal" onclick="resetForm()">
                <i class="fas fa-plus"></i> Add New Patient
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0" id="totalPatients">0</h4>
                                <p class="card-text">Total Patients</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0" id="malePatients">0</h4>
                                <p class="card-text">Male Patients</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-male fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0" id="femalePatients">0</h4>
                                <p class="card-text">Female Patients</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-female fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title mb-0" id="recentPatients">0</h4>
                                <p class="card-text">Recent (7 days)</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> Patient List
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search patients...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="searchPatients()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Loading patients...</p>
                </div>

                <!-- Patients Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="patientsTable" style="display: none;">
                        <thead class="thead-light">
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>UHID</th>
                                <th>Patient Name</th>
                                <th>Contact</th>
                                <th>Age & Gender</th>
                                <th>Address</th>
                                <th>Created Date</th>
                                <th>Added By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTableBody">
                            <!-- Patient rows will be inserted here -->
                        </tbody>
                    </table>
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
                    <div class="col-md-6">
                        <div id="paginationInfo">
                            Showing 0 to 0 of 0 entries
                        </div>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="Patient pagination">
                            <ul class="pagination justify-content-end mb-0" id="pagination">
                                <!-- Pagination buttons will be inserted here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient Modal -->
    <div class="modal fade" id="patientModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Patient</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="patientForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="uhid">UHID</label>
                                    <input type="text" class="form-control" id="uhid" name="uhid" placeholder="Auto-generated if empty">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="father_husband">Father/Husband Name</label>
                                    <input type="text" class="form-control" id="father_husband" name="father_husband">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mobile">Mobile <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="mobile" name="mobile" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="number" class="form-control" id="age" name="age" min="0" max="150">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="age_unit">Age Unit</label>
                                    <select class="form-control" id="age_unit" name="age_unit">
                                        <option value="Years">Years</option>
                                        <option value="Months">Months</option>
                                        <option value="Days">Days</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Patient
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Override the API endpoint for testing
        const API_ENDPOINT = 'ajax/patient_api_sqlite.php';
        
        // Global variables
        let currentPage = 1;
        let recordsPerPage = 10;
        let totalPages = 1;
        let searchQuery = '';
        let isLoading = false;
        let editingPatientId = null;

        // Initialize the page
        $(document).ready(function() {
            loadStats();
            loadPatients();
            
            // Set up search input handler
            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    searchPatients();
                }
            });
            
            // Set up form submission
            $('#patientForm').on('submit', handleFormSubmit);
        });

        // Load statistics
        function loadStats() {
            $.ajax({
                url: API_ENDPOINT,
                type: 'GET',
                data: { action: 'stats' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const stats = response.data;
                        $('#totalPatients').text(stats.total_patients || 0);
                        $('#malePatients').text(stats.male_patients || 0);
                        $('#femalePatients').text(stats.female_patients || 0);
                        $('#recentPatients').text(stats.recent_patients || 0);
                    }
                },
                error: function() {
                    console.error('Failed to load statistics');
                }
            });
        }

        // Load patients
        function loadPatients() {
            if (isLoading) return;
            
            isLoading = true;
            showLoading();
            
            $.ajax({
                url: API_ENDPOINT,
                type: 'GET',
                data: {
                    action: 'list',
                    page: currentPage,
                    limit: recordsPerPage,
                    search: searchQuery
                },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    console.log('API Response:', response);
                    
                    if (response.success) {
                        renderPatientsTable(response.data);
                        updatePagination(response.pagination);
                    } else {
                        showError(response.message || 'Failed to load patients');
                        showNoData();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    showError('Failed to load patients: ' + error);
                    showNoData();
                },
                complete: function() {
                    isLoading = false;
                    hideLoading();
                }
            });
        }

        // Render patients table
        function renderPatientsTable(patients) {
            const tbody = $('#patientsTableBody');
            
            if (!patients || patients.length === 0) {
                showNoData();
                return;
            }

            let html = '';
            patients.forEach(function(patient) {
                html += `
                    <tr>
                        <td>
                            <input type="checkbox" class="patient-checkbox" value="${patient.id}">
                        </td>
                        <td>
                            <span class="badge badge-primary">${patient.uhid || 'N/A'}</span>
                        </td>
                        <td>
                            <div class="patient-info">
                                <strong>${patient.name}</strong>
                                ${patient.father_husband ? `<br><small class="text-muted">S/O: ${patient.father_husband}</small>` : ''}
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                <i class="fas fa-mobile-alt"></i> ${patient.mobile}
                                ${patient.email ? `<br><i class="fas fa-envelope"></i> ${patient.email}` : ''}
                            </div>
                        </td>
                        <td>
                            <span class="age-gender">
                                ${patient.age ? patient.age + ' ' + (patient.age_unit || 'Years') : 'N/A'}
                                ${patient.gender ? `<br><span class="badge badge-${getGenderBadgeClass(patient.gender)}">${patient.gender}</span>` : ''}
                            </span>
                        </td>
                        <td>
                            <div class="address-info">
                                ${patient.address ? patient.address.substring(0, 50) + (patient.address.length > 50 ? '...' : '') : 'N/A'}
                            </div>
                        </td>
                        <td>
                            <small class="text-muted">
                                ${formatDate(patient.created_at)}
                            </small>
                        </td>
                        <td>
                            <small class="text-muted">
                                ${patient.added_by || 'System'}
                            </small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-info" onclick="viewPatient(${patient.id})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editPatient(${patient.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deletePatient(${patient.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });

            tbody.html(html);
            $('#noDataMessage').hide();
            $('#patientsTable').show();
        }

        // Update pagination
        function updatePagination(pagination) {
            if (!pagination) return;

            totalPages = pagination.total_pages || 1;
            currentPage = pagination.current_page || 1;

            // Update pagination info
            const start = ((currentPage - 1) * recordsPerPage) + 1;
            const end = Math.min(currentPage * recordsPerPage, pagination.total_records);
            $('#paginationInfo').html(`Showing ${start} to ${end} of ${pagination.total_records} entries`);

            // Generate pagination buttons
            let paginationHtml = '';
            
            // Previous button
            if (currentPage > 1) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${currentPage - 1})">Previous</a></li>`;
            }

            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(1)">1</a></li>`;
                if (startPage > 2) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                const activeClass = i === currentPage ? 'active' : '';
                paginationHtml += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${totalPages})">${totalPages}</a></li>`;
            }

            // Next button
            if (currentPage < totalPages) {
                paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${currentPage + 1})">Next</a></li>`;
            }

            $('#pagination').html(paginationHtml);
        }

        // Show/hide loading
        function showLoading() {
            $('#loadingSpinner').show();
            $('#patientsTable').hide();
            $('#noDataMessage').hide();
        }

        function hideLoading() {
            $('#loadingSpinner').hide();
        }

        function showNoData() {
            $('#patientsTable').hide();
            $('#noDataMessage').show();
            $('#paginationInfo').html('Showing 0 to 0 of 0 entries');
            $('#pagination').html('');
        }

        function showError(message) {
            toastr.error(message);
        }

        function showSuccess(message) {
            toastr.success(message);
        }

        // Search patients
        function searchPatients() {
            searchQuery = $('#searchInput').val().trim();
            currentPage = 1;
            loadPatients();
        }

        // Change page
        function changePage(page) {
            if (page >= 1 && page <= totalPages && page !== currentPage) {
                currentPage = page;
                loadPatients();
            }
        }

        // Form handling
        function resetForm() {
            $('#patientForm')[0].reset();
            editingPatientId = null;
            $('#modalTitle').text('Add New Patient');
        }

        function handleFormSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            const url = editingPatientId ? 
                API_ENDPOINT + '?action=update&id=' + editingPatientId :
                API_ENDPOINT + '?action=create';
            
            $.ajax({
                url: url,
                type: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        $('#patientModal').modal('hide');
                        loadPatients();
                        loadStats();
                    } else {
                        showError(response.message);
                    }
                },
                error: function() {
                    showError('Failed to save patient');
                }
            });
        }

        // CRUD operations
        function viewPatient(id) {
            $.ajax({
                url: API_ENDPOINT,
                type: 'GET',
                data: { action: 'read', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const patient = response.data;
                        alert('Patient Details:\n' + 
                              'UHID: ' + patient.uhid + '\n' +
                              'Name: ' + patient.name + '\n' +
                              'Mobile: ' + patient.mobile + '\n' +
                              'Gender: ' + patient.gender);
                    }
                }
            });
        }

        function editPatient(id) {
            $.ajax({
                url: API_ENDPOINT,
                type: 'GET',
                data: { action: 'read', id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const patient = response.data;
                        editingPatientId = id;
                        $('#modalTitle').text('Edit Patient');
                        
                        // Populate form
                        $('#uhid').val(patient.uhid);
                        $('#name').val(patient.name);
                        $('#father_husband').val(patient.father_husband);
                        $('#mobile').val(patient.mobile);
                        $('#email').val(patient.email);
                        $('#age').val(patient.age);
                        $('#age_unit').val(patient.age_unit);
                        $('#gender').val(patient.gender);
                        $('#address').val(patient.address);
                        
                        $('#patientModal').modal('show');
                    }
                }
            });
        }

        function deletePatient(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: API_ENDPOINT,
                        type: 'POST',
                        data: JSON.stringify({ action: 'delete', id: id }),
                        contentType: 'application/json',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showSuccess(response.message);
                                loadPatients();
                                loadStats();
                            } else {
                                showError(response.message);
                            }
                        }
                    });
                }
            });
        }

        // Utility functions
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-IN', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function getGenderBadgeClass(gender) {
            switch (gender) {
                case 'Male': return 'primary';
                case 'Female': return 'success';
                default: return 'secondary';
            }
        }
    </script>
</body>
</html>
