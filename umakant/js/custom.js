// Custom JavaScript for Pathology Lab Management System

// Initialize tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// Initialize popovers
$(function () {
    $('[data-toggle="popover"]').popover();
});

// Confirm before delete
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this record?');
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Document ready function
$(document).ready(function() {
    // Initialize DataTables if available
    if ($.fn.DataTable) {
        $('#usersTable, #doctorsTable, #patientsTable, #categoriesTable, #testsTable, #entriesTable').DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
    
    // Doctor Management Functions
    // Open Add Doctor Modal
    window.openAddDoctorModal = function() {
        $('#doctorModalLabel').text('Add Doctor');
        $('#doctorForm')[0].reset();
        $('#doctorId').val('');
        // Reset to form view
        let formContent = '<form id="doctorForm">';
        formContent += '<input type="hidden" id="doctorId" name="id">';
        formContent += '<div class="form-group">';
        formContent += '<label for="doctorName">Name *</label>';
        formContent += '<input type="text" class="form-control" id="doctorName" name="name" required>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="doctorSpecialization">Specialization</label>';
        formContent += '<input type="text" class="form-control" id="doctorSpecialization" name="specialization">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="doctorPhone">Phone</label>';
        formContent += '<input type="text" class="form-control" id="doctorPhone" name="phone">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="doctorEmail">Email</label>';
        formContent += '<input type="email" class="form-control" id="doctorEmail" name="email">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="doctorAddress">Address</label>';
        formContent += '<textarea class="form-control" id="doctorAddress" name="address" rows="3"></textarea>';
        formContent += '</div>';
        formContent += '</form>';
        $('#doctorModal .modal-body').html(formContent);
        $('#doctorModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveDoctorBtn">Save Doctor</button>');
        $('#doctorModal').modal('show');
    };
    
    // View Doctor
    $(document).on('click', '.view-doctor', function(e) {
        e.preventDefault();
        const doctorId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/doctor_ajax.php',
            type: 'POST',
            data: { id: doctorId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const doctor = response.data;
                    // Create view-only modal content
                    let viewContent = '<div class="row">';
                    viewContent += '<div class="col-md-12">';
                    viewContent += '<table class="table table-bordered">';
                    viewContent += '<tr><th>ID</th><td>' + doctor.id + '</td></tr>';
                    viewContent += '<tr><th>Name</th><td>' + (doctor.name || '') + '</td></tr>';
                    viewContent += '<tr><th>Specialization</th><td>' + (doctor.specialization || '') + '</td></tr>';
                    viewContent += '<tr><th>Phone</th><td>' + (doctor.phone || '') + '</td></tr>';
                    viewContent += '<tr><th>Email</th><td>' + (doctor.email || '') + '</td></tr>';
                    viewContent += '<tr><th>Address</th><td>' + (doctor.address || '') + '</td></tr>';
                    viewContent += '</table>';
                    viewContent += '</div>';
                    viewContent += '</div>';
                    
                    $('#doctorModalLabel').text('View Doctor');
                    $('#doctorModal .modal-body').html(viewContent);
                    $('#doctorModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#doctorModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching doctor data.');
            }
        });
    });
    
    // Edit Doctor
    $(document).on('click', '.edit-doctor', function(e) {
        e.preventDefault();
        const doctorId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/doctor_ajax.php',
            type: 'POST',
            data: { id: doctorId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const doctor = response.data;
                    $('#doctorModalLabel').text('Edit Doctor');
                    // Create form content
                    let formContent = '<form id="doctorForm">';
                    formContent += '<input type="hidden" id="doctorId" name="id" value="' + (doctor.id || '') + '">';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="doctorName">Name *</label>';
                    formContent += '<input type="text" class="form-control" id="doctorName" name="name" value="' + (doctor.name || '') + '" required>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="doctorSpecialization">Specialization</label>';
                    formContent += '<input type="text" class="form-control" id="doctorSpecialization" name="specialization" value="' + (doctor.specialization || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="doctorPhone">Phone</label>';
                    formContent += '<input type="text" class="form-control" id="doctorPhone" name="phone" value="' + (doctor.phone || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="doctorEmail">Email</label>';
                    formContent += '<input type="email" class="form-control" id="doctorEmail" name="email" value="' + (doctor.email || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="doctorAddress">Address</label>';
                    formContent += '<textarea class="form-control" id="doctorAddress" name="address" rows="3">' + (doctor.address || '') + '</textarea>';
                    formContent += '</div>';
                    formContent += '</form>';
                    
                    $('#doctorModal .modal-body').html(formContent);
                    $('#doctorModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveDoctorBtn">Save Doctor</button>');
                    $('#doctorModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching doctor data.');
            }
        });
    });
    
    // Save Doctor (Add or Edit)
    $(document).off('click', '#saveDoctorBtn').on('click', '#saveDoctorBtn', function() {
        const formData = $('#doctorForm').serialize();
        const doctorId = $('#doctorId').val();
        const action = doctorId ? 'edit' : 'add';
        
        $.ajax({
            url: 'ajax/doctor_ajax.php',
            type: 'POST',
            data: formData + '&action=' + action,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#doctorModal').modal('hide');
                    alert(response.message);
                    location.reload(); // Reload the page to show updated data
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    });
    
    // Delete Doctor
    $(document).on('click', '.delete-doctor', function(e) {
        e.preventDefault();
        const doctorId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this doctor?')) {
            $.ajax({
                url: 'ajax/doctor_ajax.php',
                type: 'POST',
                data: { id: doctorId, action: 'delete' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Reload the page to show updated data
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the doctor.');
                }
            });
        }
    });
    
    // Patient Management Functions
    // Open Add Patient Modal
    window.openAddPatientModal = function() {
        $('#patientModalLabel').text('Add Patient');
        $('#patientForm')[0].reset();
        $('#patientId').val('');
        // Reset to form view
        let formContent = '<form id="patientForm">';
        formContent += '<input type="hidden" id="patientId" name="id">';
        formContent += '<div class="form-group">';
        formContent += '<label for="patientName">Name *</label>';
        formContent += '<input type="text" class="form-control" id="patientName" name="name" required>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="patientMobile">Mobile *</label>';
        formContent += '<input type="text" class="form-control" id="patientMobile" name="mobile" required>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="patientFatherHusband">Father/Husband Name</label>';
        formContent += '<input type="text" class="form-control" id="patientFatherHusband" name="father_husband">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="patientAddress">Address</label>';
        formContent += '<textarea class="form-control" id="patientAddress" name="address" rows="3"></textarea>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="patientSex">Gender</label>';
        formContent += '<select class="form-control" id="patientSex" name="sex">';
        formContent += '<option value="">Select Gender</option>';
        formContent += '<option value="Male">Male</option>';
        formContent += '<option value="Female">Female</option>';
        formContent += '<option value="Other">Other</option>';
        formContent += '</select>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="patientAge">Age</label>';
        formContent += '<div class="row">';
        formContent += '<div class="col-6">';
        formContent += '<input type="number" class="form-control" id="patientAge" name="age" min="0">';
        formContent += '</div>';
        formContent += '<div class="col-6">';
        formContent += '<select class="form-control" id="patientAgeUnit" name="age_unit">';
        formContent += '<option value="Years">Years</option>';
        formContent += '<option value="Months">Months</option>';
        formContent += '<option value="Days">Days</option>';
        formContent += '</select>';
        formContent += '</div>';
        formContent += '</div>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="patientUHID">UHID</label>';
        formContent += '<input type="text" class="form-control" id="patientUHID" name="uhid">';
        formContent += '</div>';
        formContent += '</form>';
        $('#patientModal .modal-body').html(formContent);
        $('#patientModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="savePatientBtn">Save Patient</button>');
        $('#patientModal').modal('show');
    };
    
    // View Patient
    $(document).on('click', '.view-patient', function(e) {
        e.preventDefault();
        const patientId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/patient_ajax.php',
            type: 'POST',
            data: { id: patientId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const patient = response.data;
                    // Create view-only modal content
                    let viewContent = '<div class="row">';
                    viewContent += '<div class="col-md-12">';
                    viewContent += '<table class="table table-bordered">';
                    viewContent += '<tr><th>ID</th><td>' + patient.id + '</td></tr>';
                    viewContent += '<tr><th>Name</th><td>' + (patient.name || '') + '</td></tr>';
                    viewContent += '<tr><th>Mobile</th><td>' + (patient.mobile || '') + '</td></tr>';
                    viewContent += '<tr><th>Father/Husband</th><td>' + (patient.father_husband || '') + '</td></tr>';
                    viewContent += '<tr><th>Address</th><td>' + (patient.address || '') + '</td></tr>';
                    viewContent += '<tr><th>Gender</th><td>' + (patient.sex || '') + '</td></tr>';
                    viewContent += '<tr><th>Age</th><td>' + (patient.age || '') + ' ' + (patient.age_unit || '') + '</td></tr>';
                    viewContent += '<tr><th>UHID</th><td>' + (patient.uhid || '') + '</td></tr>';
                    viewContent += '</table>';
                    viewContent += '</div>';
                    viewContent += '</div>';
                    
                    $('#patientModalLabel').text('View Patient');
                    $('#patientModal .modal-body').html(viewContent);
                    $('#patientModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#patientModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching patient data.');
            }
        });
    });
    
    // Edit Patient
    $(document).on('click', '.edit-patient', function(e) {
        e.preventDefault();
        const patientId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/patient_ajax.php',
            type: 'POST',
            data: { id: patientId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const patient = response.data;
                    $('#patientModalLabel').text('Edit Patient');
                    // Create form content
                    let formContent = '<form id="patientForm">';
                    formContent += '<input type="hidden" id="patientId" name="id" value="' + (patient.id || '') + '">';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="patientName">Name *</label>';
                    formContent += '<input type="text" class="form-control" id="patientName" name="name" value="' + (patient.name || '') + '" required>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="patientMobile">Mobile *</label>';
                    formContent += '<input type="text" class="form-control" id="patientMobile" name="mobile" value="' + (patient.mobile || '') + '" required>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="patientFatherHusband">Father/Husband Name</label>';
                    formContent += '<input type="text" class="form-control" id="patientFatherHusband" name="father_husband" value="' + (patient.father_husband || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="patientAddress">Address</label>';
                    formContent += '<textarea class="form-control" id="patientAddress" name="address" rows="3">' + (patient.address || '') + '</textarea>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="patientSex">Gender</label>';
                    formContent += '<select class="form-control" id="patientSex" name="sex">';
                    formContent += '<option value="">Select Gender</option>';
                    formContent += '<option value="Male"' + (patient.sex === 'Male' ? ' selected' : '') + '>Male</option>';
                    formContent += '<option value="Female"' + (patient.sex === 'Female' ? ' selected' : '') + '>Female</option>';
                    formContent += '<option value="Other"' + (patient.sex === 'Other' ? ' selected' : '') + '>Other</option>';
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="patientAge">Age</label>';
                    formContent += '<div class="row">';
                    formContent += '<div class="col-6">';
                    formContent += '<input type="number" class="form-control" id="patientAge" name="age" min="0" value="' + (patient.age || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="col-6">';
                    formContent += '<select class="form-control" id="patientAgeUnit" name="age_unit">';
                    formContent += '<option value="Years"' + (patient.age_unit === 'Years' ? ' selected' : '') + '>Years</option>';
                    formContent += '<option value="Months"' + (patient.age_unit === 'Months' ? ' selected' : '') + '>Months</option>';
                    formContent += '<option value="Days"' + (patient.age_unit === 'Days' ? ' selected' : '') + '>Days</option>';
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '</div>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="patientUHID">UHID</label>';
                    formContent += '<input type="text" class="form-control" id="patientUHID" name="uhid" value="' + (patient.uhid || '') + '">';
                    formContent += '</div>';
                    formContent += '</form>';
                    
                    $('#patientModal .modal-body').html(formContent);
                    $('#patientModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="savePatientBtn">Save Patient</button>');
                    $('#patientModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching patient data.');
            }
        });
    });
    
    // Save Patient (Add or Edit)
    $(document).off('click', '#savePatientBtn').on('click', '#savePatientBtn', function() {
        const formData = $('#patientForm').serialize();
        const patientId = $('#patientId').val();
        const action = patientId ? 'edit' : 'add';
        
        $.ajax({
            url: 'ajax/patient_ajax.php',
            type: 'POST',
            data: formData + '&action=' + action,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#patientModal').modal('hide');
                    alert(response.message);
                    location.reload(); // Reload the page to show updated data
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    });
    
    // Delete Patient
    $(document).on('click', '.delete-patient', function(e) {
        e.preventDefault();
        const patientId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this patient?')) {
            $.ajax({
                url: 'ajax/patient_ajax.php',
                type: 'POST',
                data: { id: patientId, action: 'delete' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Reload the page to show updated data
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the patient.');
                }
            });
        }
    });
    
    // Category Management Functions
    // Open Add Category Modal
    window.openAddCategoryModal = function() {
        $('#categoryModalLabel').text('Add Category');
        $('#categoryForm')[0].reset();
        $('#categoryId').val('');
        // Reset to form view
        let formContent = '<form id="categoryForm">';
        formContent += '<input type="hidden" id="categoryId" name="id">';
        formContent += '<div class="form-group">';
        formContent += '<label for="categoryName">Name *</label>';
        formContent += '<input type="text" class="form-control" id="categoryName" name="name" required>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="categoryDescription">Description</label>';
        formContent += '<textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>';
        formContent += '</div>';
        formContent += '</form>';
        $('#categoryModal .modal-body').html(formContent);
        $('#categoryModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveCategoryBtn">Save Category</button>');
        $('#categoryModal').modal('show');
    };
    
    // View Category
    $(document).on('click', '.view-category', function(e) {
        e.preventDefault();
        const categoryId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/category_ajax.php',
            type: 'POST',
            data: { id: categoryId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const category = response.data;
                    // Create view-only modal content
                    let viewContent = '<div class="row">';
                    viewContent += '<div class="col-md-12">';
                    viewContent += '<table class="table table-bordered">';
                    viewContent += '<tr><th>ID</th><td>' + category.id + '</td></tr>';
                    viewContent += '<tr><th>Name</th><td>' + (category.name || '') + '</td></tr>';
                    viewContent += '<tr><th>Description</th><td>' + (category.description || '') + '</td></tr>';
                    viewContent += '<tr><th>Created At</th><td>' + (category.created_at || '') + '</td></tr>';
                    viewContent += '</table>';
                    viewContent += '</div>';
                    viewContent += '</div>';
                    
                    $('#categoryModalLabel').text('View Category');
                    $('#categoryModal .modal-body').html(viewContent);
                    $('#categoryModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#categoryModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching category data.');
            }
        });
    });
    
    // Edit Category
    $(document).on('click', '.edit-category', function(e) {
        e.preventDefault();
        const categoryId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/category_ajax.php',
            type: 'POST',
            data: { id: categoryId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const category = response.data;
                    $('#categoryModalLabel').text('Edit Category');
                    // Create form content
                    let formContent = '<form id="categoryForm">';
                    formContent += '<input type="hidden" id="categoryId" name="id" value="' + (category.id || '') + '">';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="categoryName">Name *</label>';
                    formContent += '<input type="text" class="form-control" id="categoryName" name="name" value="' + (category.name || '') + '" required>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="categoryDescription">Description</label>';
                    formContent += '<textarea class="form-control" id="categoryDescription" name="description" rows="3">' + (category.description || '') + '</textarea>';
                    formContent += '</div>';
                    formContent += '</form>';
                    
                    $('#categoryModal .modal-body').html(formContent);
                    $('#categoryModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveCategoryBtn">Save Category</button>');
                    $('#categoryModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching category data.');
            }
        });
    });
    
    // Save Category (Add or Edit)
    $(document).off('click', '#saveCategoryBtn').on('click', '#saveCategoryBtn', function() {
        const formData = $('#categoryForm').serialize();
        const categoryId = $('#categoryId').val();
        const action = categoryId ? 'edit' : 'add';
        
        $.ajax({
            url: 'ajax/category_ajax.php',
            type: 'POST',
            data: formData + '&action=' + action,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#categoryModal').modal('hide');
                    alert(response.message);
                    location.reload(); // Reload the page to show updated data
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    });
    
    // Delete Category
    $(document).on('click', '.delete-category', function(e) {
        e.preventDefault();
        const categoryId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this category?')) {
            $.ajax({
                url: 'ajax/category_ajax.php',
                type: 'POST',
                data: { id: categoryId, action: 'delete' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Reload the page to show updated data
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the category.');
                }
            });
        }
    });
    
    // Test Management Functions
    // Open Add Test Modal
    window.openAddTestModal = function() {
        $('#testModalLabel').text('Add Test');
        $('#testForm')[0].reset();
        $('#testId').val('');
        // Reset to form view
        let formContent = '<form id="testForm">';
        formContent += '<input type="hidden" id="testId" name="id">';
        formContent += '<div class="form-group">';
        formContent += '<label for="testCategoryId">Category *</label>';
        formContent += '<select class="form-control" id="testCategoryId" name="category_id" required>';
        formContent += '<option value="">Select Category</option>';
        // We'll need to populate categories dynamically
        formContent += '</select>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testName">Name *</label>';
        formContent += '<input type="text" class="form-control" id="testName" name="name" required>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testDescription">Description</label>';
        formContent += '<textarea class="form-control" id="testDescription" name="description" rows="3"></textarea>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testPrice">Price *</label>';
        formContent += '<input type="number" class="form-control" id="testPrice" name="price" step="0.01" required>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testUnit">Unit</label>';
        formContent += '<input type="text" class="form-control" id="testUnit" name="unit">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testSpecimen">Specimen</label>';
        formContent += '<input type="text" class="form-control" id="testSpecimen" name="specimen">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testDefaultResult">Default Result</label>';
        formContent += '<input type="text" class="form-control" id="testDefaultResult" name="default_result">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testReferenceRange">Reference Range</label>';
        formContent += '<input type="text" class="form-control" id="testReferenceRange" name="reference_range">';
        formContent += '</div>';
        formContent += '<div class="form-row">';
        formContent += '<div class="form-group col-md-6">';
        formContent += '<label for="testMin">Min Value</label>';
        formContent += '<input type="number" class="form-control" id="testMin" name="min" step="0.01">';
        formContent += '</div>';
        formContent += '<div class="form-group col-md-6">';
        formContent += '<label for="testMax">Max Value</label>';
        formContent += '<input type="number" class="form-control" id="testMax" name="max" step="0.01">';
        formContent += '</div>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testSubHeading">Sub Heading</label>';
        formContent += '<select class="form-control" id="testSubHeading" name="sub_heading">';
        formContent += '<option value="0">No</option>';
        formContent += '<option value="1">Yes</option>';
        formContent += '</select>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testCode">Test Code</label>';
        formContent += '<input type="text" class="form-control" id="testCode" name="test_code">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testMethod">Method</label>';
        formContent += '<input type="text" class="form-control" id="testMethod" name="method">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testPrintNewPage">Print on New Page</label>';
        formContent += '<select class="form-control" id="testPrintNewPage" name="print_new_page">';
        formContent += '<option value="0">No</option>';
        formContent += '<option value="1">Yes</option>';
        formContent += '</select>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="testShortcut">Shortcut</label>';
        formContent += '<input type="text" class="form-control" id="testShortcut" name="shortcut">';
        formContent += '</div>';
        formContent += '</form>';
        $('#testModal .modal-body').html(formContent);
        $('#testModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveTestBtn">Save Test</button>');
        $('#testModal').modal('show');
    };
    
    // View Test
    $(document).on('click', '.view-test', function(e) {
        e.preventDefault();
        const testId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/test_ajax.php',
            type: 'POST',
            data: { id: testId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const test = response.data;
                    // Create view-only modal content
                    let viewContent = '<div class="row">';
                    viewContent += '<div class="col-md-12">';
                    viewContent += '<table class="table table-bordered">';
                    viewContent += '<tr><th>ID</th><td>' + test.id + '</td></tr>';
                    viewContent += '<tr><th>Category ID</th><td>' + (test.category_id || '') + '</td></tr>';
                    viewContent += '<tr><th>Name</th><td>' + (test.name || '') + '</td></tr>';
                    viewContent += '<tr><th>Description</th><td>' + (test.description || '') + '</td></tr>';
                    viewContent += '<tr><th>Price</th><td>' + (test.price || '') + '</td></tr>';
                    viewContent += '<tr><th>Unit</th><td>' + (test.unit || '') + '</td></tr>';
                    viewContent += '<tr><th>Specimen</th><td>' + (test.specimen || '') + '</td></tr>';
                    viewContent += '<tr><th>Default Result</th><td>' + (test.default_result || '') + '</td></tr>';
                    viewContent += '<tr><th>Reference Range</th><td>' + (test.reference_range || '') + '</td></tr>';
                    viewContent += '<tr><th>Min Value</th><td>' + (test.min || '') + '</td></tr>';
                    viewContent += '<tr><th>Max Value</th><td>' + (test.max || '') + '</td></tr>';
                    viewContent += '<tr><th>Sub Heading</th><td>' + (test.sub_heading || '') + '</td></tr>';
                    viewContent += '<tr><th>Test Code</th><td>' + (test.test_code || '') + '</td></tr>';
                    viewContent += '<tr><th>Method</th><td>' + (test.method || '') + '</td></tr>';
                    viewContent += '<tr><th>Print on New Page</th><td>' + (test.print_new_page || '') + '</td></tr>';
                    viewContent += '<tr><th>Shortcut</th><td>' + (test.shortcut || '') + '</td></tr>';
                    viewContent += '</table>';
                    viewContent += '</div>';
                    viewContent += '</div>';
                    
                    $('#testModalLabel').text('View Test');
                    $('#testModal .modal-body').html(viewContent);
                    $('#testModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#testModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching test data.');
            }
        });
    });
    
    // Edit Test
    $(document).on('click', '.edit-test', function(e) {
        e.preventDefault();
        const testId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/test_ajax.php',
            type: 'POST',
            data: { id: testId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const test = response.data;
                    $('#testModalLabel').text('Edit Test');
                    // Create form content
                    let formContent = '<form id="testForm">';
                    formContent += '<input type="hidden" id="testId" name="id" value="' + (test.id || '') + '">';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testCategoryId">Category *</label>';
                    formContent += '<select class="form-control" id="testCategoryId" name="category_id" required>';
                    formContent += '<option value="">Select Category</option>';
                    // We'll need to populate categories dynamically
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testName">Name *</label>';
                    formContent += '<input type="text" class="form-control" id="testName" name="name" value="' + (test.name || '') + '" required>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testDescription">Description</label>';
                    formContent += '<textarea class="form-control" id="testDescription" name="description" rows="3">' + (test.description || '') + '</textarea>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testPrice">Price *</label>';
                    formContent += '<input type="number" class="form-control" id="testPrice" name="price" step="0.01" value="' + (test.price || '') + '" required>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testUnit">Unit</label>';
                    formContent += '<input type="text" class="form-control" id="testUnit" name="unit" value="' + (test.unit || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testSpecimen">Specimen</label>';
                    formContent += '<input type="text" class="form-control" id="testSpecimen" name="specimen" value="' + (test.specimen || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testDefaultResult">Default Result</label>';
                    formContent += '<input type="text" class="form-control" id="testDefaultResult" name="default_result" value="' + (test.default_result || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testReferenceRange">Reference Range</label>';
                    formContent += '<input type="text" class="form-control" id="testReferenceRange" name="reference_range" value="' + (test.reference_range || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-row">';
                    formContent += '<div class="form-group col-md-6">';
                    formContent += '<label for="testMin">Min Value</label>';
                    formContent += '<input type="number" class="form-control" id="testMin" name="min" step="0.01" value="' + (test.min || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group col-md-6">';
                    formContent += '<label for="testMax">Max Value</label>';
                    formContent += '<input type="number" class="form-control" id="testMax" name="max" step="0.01" value="' + (test.max || '') + '">';
                    formContent += '</div>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testSubHeading">Sub Heading</label>';
                    formContent += '<select class="form-control" id="testSubHeading" name="sub_heading">';
                    formContent += '<option value="0"' + (test.sub_heading === '0' ? ' selected' : '') + '>No</option>';
                    formContent += '<option value="1"' + (test.sub_heading === '1' ? ' selected' : '') + '>Yes</option>';
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testCode">Test Code</label>';
                    formContent += '<input type="text" class="form-control" id="testCode" name="test_code" value="' + (test.test_code || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testMethod">Method</label>';
                    formContent += '<input type="text" class="form-control" id="testMethod" name="method" value="' + (test.method || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testPrintNewPage">Print on New Page</label>';
                    formContent += '<select class="form-control" id="testPrintNewPage" name="print_new_page">';
                    formContent += '<option value="0"' + (test.print_new_page === '0' ? ' selected' : '') + '>No</option>';
                    formContent += '<option value="1"' + (test.print_new_page === '1' ? ' selected' : '') + '>Yes</option>';
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="testShortcut">Shortcut</label>';
                    formContent += '<input type="text" class="form-control" id="testShortcut" name="shortcut" value="' + (test.shortcut || '') + '">';
                    formContent += '</div>';
                    formContent += '</form>';
                    
                    $('#testModal .modal-body').html(formContent);
                    $('#testModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveTestBtn">Save Test</button>');
                    $('#testModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching test data.');
            }
        });
    });
    
    // Save Test (Add or Edit)
    $(document).off('click', '#saveTestBtn').on('click', '#saveTestBtn', function() {
        const formData = $('#testForm').serialize();
        const testId = $('#testId').val();
        const action = testId ? 'edit' : 'add';
        
        $.ajax({
            url: 'ajax/test_ajax.php',
            type: 'POST',
            data: formData + '&action=' + action,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#testModal').modal('hide');
                    alert(response.message);
                    location.reload(); // Reload the page to show updated data
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    });
    
    // Delete Test
    $(document).on('click', '.delete-test', function(e) {
        e.preventDefault();
        const testId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this test?')) {
            $.ajax({
                url: 'ajax/test_ajax.php',
                type: 'POST',
                data: { id: testId, action: 'delete' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Reload the page to show updated data
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the test.');
                }
            });
        }
    });
    
    // User Management Functions
    // Open Add User Modal
    window.openAddUserModal = function() {
        $('#userModalLabel').text('Add User');
        $('#userForm')[0].reset();
        $('#userId').val('');
        // Reset to form view
        let formContent = '<form id="userForm">';
        formContent += '<input type="hidden" id="userId" name="id">';
        formContent += '<div class="form-group">';
        formContent += '<label for="userUsername">Username *</label>';
        formContent += '<input type="text" class="form-control" id="userUsername" name="username" required>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="userPassword">Password *</label>';
        formContent += '<input type="password" class="form-control" id="userPassword" name="password" required>';
        formContent += '<small class="form-text text-muted">Leave blank to keep current password when editing</small>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="userFullName">Full Name *</label>';
        formContent += '<input type="text" class="form-control" id="userFullName" name="full_name" required>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="userEmail">Email</label>';
        formContent += '<input type="email" class="form-control" id="userEmail" name="email">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="userRole">Role *</label>';
        formContent += '<select class="form-control" id="userRole" name="role" required>';
        formContent += '<option value="admin">Admin</option>';
        formContent += '<option value="user">User</option>';
        formContent += '</select>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="userIsActive">Status *</label>';
        formContent += '<select class="form-control" id="userIsActive" name="is_active" required>';
        formContent += '<option value="1">Active</option>';
        formContent += '<option value="0">Inactive</option>';
        formContent += '</select>';
        formContent += '</div>';
        formContent += '</form>';
        $('#userModal .modal-body').html(formContent);
        $('#userModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveUserBtn">Save User</button>');
        $('#userModal').modal('show');
    };
    
    // View User
    $(document).on('click', '.view-user', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/user_ajax.php',
            type: 'POST',
            data: { id: userId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const user = response.data;
                    // Create view-only modal content
                    let viewContent = '<div class="row">';
                    viewContent += '<div class="col-md-12">';
                    viewContent += '<table class="table table-bordered">';
                    viewContent += '<tr><th>ID</th><td>' + user.id + '</td></tr>';
                    viewContent += '<tr><th>Username</th><td>' + (user.username || '') + '</td></tr>';
                    viewContent += '<tr><th>Full Name</th><td>' + (user.full_name || '') + '</td></tr>';
                    viewContent += '<tr><th>Email</th><td>' + (user.email || '') + '</td></tr>';
                    viewContent += '<tr><th>Role</th><td>' + (user.role || '') + '</td></tr>';
                    viewContent += '<tr><th>Status</th><td>' + (user.is_active == 1 ? 'Active' : 'Inactive') + '</td></tr>';
                    viewContent += '<tr><th>Last Login</th><td>' + (user.last_login || 'Never') + '</td></tr>';
                    viewContent += '</table>';
                    viewContent += '</div>';
                    viewContent += '</div>';
                    
                    $('#userModalLabel').text('View User');
                    $('#userModal .modal-body').html(viewContent);
                    $('#userModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#userModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching user data.');
            }
        });
    });
    
    // Edit User
    $(document).on('click', '.edit-user', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/user_ajax.php',
            type: 'POST',
            data: { id: userId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const user = response.data;
                    $('#userModalLabel').text('Edit User');
                    // Create form content
                    let formContent = '<form id="userForm">';
                    formContent += '<input type="hidden" id="userId" name="id" value="' + (user.id || '') + '">';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="userUsername">Username *</label>';
                    formContent += '<input type="text" class="form-control" id="userUsername" name="username" value="' + (user.username || '') + '" required>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="userPassword">Password *</label>';
                    formContent += '<input type="password" class="form-control" id="userPassword" name="password">';
                    formContent += '<small class="form-text text-muted">Leave blank to keep current password when editing</small>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="userFullName">Full Name *</label>';
                    formContent += '<input type="text" class="form-control" id="userFullName" name="full_name" value="' + (user.full_name || '') + '" required>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="userEmail">Email</label>';
                    formContent += '<input type="email" class="form-control" id="userEmail" name="email" value="' + (user.email || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="userRole">Role *</label>';
                    formContent += '<select class="form-control" id="userRole" name="role" required>';
                    formContent += '<option value="admin"' + (user.role === 'admin' ? ' selected' : '') + '>Admin</option>';
                    formContent += '<option value="user"' + (user.role === 'user' ? ' selected' : '') + '>User</option>';
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="userIsActive">Status *</label>';
                    formContent += '<select class="form-control" id="userIsActive" name="is_active" required>';
                    formContent += '<option value="1"' + (user.is_active == 1 ? ' selected' : '') + '>Active</option>';
                    formContent += '<option value="0"' + (user.is_active == 0 ? ' selected' : '') + '>Inactive</option>';
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '</form>';
                    
                    $('#userModal .modal-body').html(formContent);
                    $('#userModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveUserBtn">Save User</button>');
                    $('#userModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching user data.');
            }
        });
    });
    
    // Save User (Add or Edit)
    $(document).off('click', '#saveUserBtn').on('click', '#saveUserBtn', function() {
        const formData = $('#userForm').serialize();
        const userId = $('#userId').val();
        const action = userId ? 'edit' : 'add';
        
        // If editing and password is empty, remove it from the form data
        let dataToSend = formData + '&action=' + action;
        if (userId && $('#userPassword').val() === '') {
            dataToSend = dataToSend.replace('&password=', '&password_skip=');
        }
        
        $.ajax({
            url: 'ajax/user_ajax.php',
            type: 'POST',
            data: dataToSend,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#userModal').modal('hide');
                    alert(response.message);
                    location.reload(); // Reload the page to show updated data
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    });
    
    // Delete User
    $(document).on('click', '.delete-user', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: 'ajax/user_ajax.php',
                type: 'POST',
                data: { id: userId, action: 'delete' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Reload the page to show updated data
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the user.');
                }
            });
        }
    });
    
    // Entry Management Functions
    // Open Add Entry Modal
    window.openAddEntryModal = function() {
        $('#entryModalLabel').text('Add Entry');
        $('#entryForm')[0].reset();
        $('#entryId').val('');
        // Reset to form view
        let formContent = '<form id="entryForm">';
        formContent += '<input type="hidden" id="entryId" name="id">';
        formContent += '<div class="form-group">';
        formContent += '<label for="entryPatientId">Patient *</label>';
        formContent += '<select class="form-control" id="entryPatientId" name="patient_id" required>';
        formContent += '<option value="">Select Patient</option>';
        // We'll need to populate patients dynamically
        formContent += '</select>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="entryDoctorId">Doctor *</label>';
        formContent += '<select class="form-control" id="entryDoctorId" name="doctor_id" required>';
        formContent += '<option value="">Select Doctor</option>';
        // We'll need to populate doctors dynamically
        formContent += '</select>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="entryTestId">Test *</label>';
        formContent += '<select class="form-control" id="entryTestId" name="test_id" required>';
        formContent += '<option value="">Select Test</option>';
        // We'll need to populate tests dynamically
        formContent += '</select>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="entryReferringDoctor">Referring Doctor</label>';
        formContent += '<input type="text" class="form-control" id="entryReferringDoctor" name="referring_doctor">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="entryEntryDate">Entry Date *</label>';
        formContent += '<input type="date" class="form-control" id="entryEntryDate" name="entry_date" required>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="entryResultValue">Result Value</label>';
        formContent += '<input type="text" class="form-control" id="entryResultValue" name="result_value">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="entryUnit">Unit</label>';
        formContent += '<input type="text" class="form-control" id="entryUnit" name="unit">';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="entryRemarks">Remarks</label>';
        formContent += '<textarea class="form-control" id="entryRemarks" name="remarks" rows="3"></textarea>';
        formContent += '</div>';
        formContent += '<div class="form-group">';
        formContent += '<label for="entryStatus">Status *</label>';
        formContent += '<select class="form-control" id="entryStatus" name="status" required>';
        formContent += '<option value="pending">Pending</option>';
        formContent += '<option value="completed">Completed</option>';
        formContent += '<option value="cancelled">Cancelled</option>';
        formContent += '</select>';
        formContent += '</div>';
        formContent += '</form>';
        $('#entryModal .modal-body').html(formContent);
        $('#entryModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveEntryBtn">Save Entry</button>');
        $('#entryModal').modal('show');
    };
    
    // View Entry
    $(document).on('click', '.view-entry', function(e) {
        e.preventDefault();
        const entryId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/entry_ajax.php',
            type: 'POST',
            data: { id: entryId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const entry = response.data;
                    // Create view-only modal content
                    let viewContent = '<div class="row">';
                    viewContent += '<div class="col-md-12">';
                    viewContent += '<table class="table table-bordered">';
                    viewContent += '<tr><th>ID</th><td>' + entry.id + '</td></tr>';
                    viewContent += '<tr><th>Patient ID</th><td>' + (entry.patient_id || '') + '</td></tr>';
                    viewContent += '<tr><th>Doctor ID</th><td>' + (entry.doctor_id || '') + '</td></tr>';
                    viewContent += '<tr><th>Test ID</th><td>' + (entry.test_id || '') + '</td></tr>';
                    viewContent += '<tr><th>Referring Doctor</th><td>' + (entry.referring_doctor || '') + '</td></tr>';
                    viewContent += '<tr><th>Entry Date</th><td>' + (entry.entry_date || '') + '</td></tr>';
                    viewContent += '<tr><th>Result Value</th><td>' + (entry.result_value || '') + '</td></tr>';
                    viewContent += '<tr><th>Unit</th><td>' + (entry.unit || '') + '</td></tr>';
                    viewContent += '<tr><th>Remarks</th><td>' + (entry.remarks || '') + '</td></tr>';
                    viewContent += '<tr><th>Status</th><td>' + (entry.status || '') + '</td></tr>';
                    viewContent += '</table>';
                    viewContent += '</div>';
                    viewContent += '</div>';
                    
                    $('#entryModalLabel').text('View Entry');
                    $('#entryModal .modal-body').html(viewContent);
                    $('#entryModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
                    $('#entryModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching entry data.');
            }
        });
    });
    
    // Edit Entry
    $(document).on('click', '.edit-entry', function(e) {
        e.preventDefault();
        const entryId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/entry_ajax.php',
            type: 'POST',
            data: { id: entryId, action: 'get' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const entry = response.data;
                    $('#entryModalLabel').text('Edit Entry');
                    // Create form content
                    let formContent = '<form id="entryForm">';
                    formContent += '<input type="hidden" id="entryId" name="id" value="' + (entry.id || '') + '">';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="entryPatientId">Patient *</label>';
                    formContent += '<select class="form-control" id="entryPatientId" name="patient_id" required>';
                    formContent += '<option value="">Select Patient</option>';
                    // We'll need to populate patients dynamically
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="entryDoctorId">Doctor *</label>';
                    formContent += '<select class="form-control" id="entryDoctorId" name="doctor_id" required>';
                    formContent += '<option value="">Select Doctor</option>';
                    // We'll need to populate doctors dynamically
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="entryTestId">Test *</label>';
                    formContent += '<select class="form-control" id="entryTestId" name="test_id" required>';
                    formContent += '<option value="">Select Test</option>';
                    // We'll need to populate tests dynamically
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="entryReferringDoctor">Referring Doctor</label>';
                    formContent += '<input type="text" class="form-control" id="entryReferringDoctor" name="referring_doctor" value="' + (entry.referring_doctor || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="entryEntryDate">Entry Date *</label>';
                    formContent += '<input type="date" class="form-control" id="entryEntryDate" name="entry_date" value="' + (entry.entry_date || '') + '" required>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="entryResultValue">Result Value</label>';
                    formContent += '<input type="text" class="form-control" id="entryResultValue" name="result_value" value="' + (entry.result_value || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="entryUnit">Unit</label>';
                    formContent += '<input type="text" class="form-control" id="entryUnit" name="unit" value="' + (entry.unit || '') + '">';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="entryRemarks">Remarks</label>';
                    formContent += '<textarea class="form-control" id="entryRemarks" name="remarks" rows="3">' + (entry.remarks || '') + '</textarea>';
                    formContent += '</div>';
                    formContent += '<div class="form-group">';
                    formContent += '<label for="entryStatus">Status *</label>';
                    formContent += '<select class="form-control" id="entryStatus" name="status" required>';
                    formContent += '<option value="pending"' + (entry.status === 'pending' ? ' selected' : '') + '>Pending</option>';
                    formContent += '<option value="completed"' + (entry.status === 'completed' ? ' selected' : '') + '>Completed</option>';
                    formContent += '<option value="cancelled"' + (entry.status === 'cancelled' ? ' selected' : '') + '>Cancelled</option>';
                    formContent += '</select>';
                    formContent += '</div>';
                    formContent += '</form>';
                    
                    $('#entryModal .modal-body').html(formContent);
                    $('#entryModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" id="saveEntryBtn">Save Entry</button>');
                    $('#entryModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while fetching entry data.');
            }
        });
    });
    
    // Save Entry (Add or Edit)
    $(document).off('click', '#saveEntryBtn').on('click', '#saveEntryBtn', function() {
        const formData = $('#entryForm').serialize();
        const entryId = $('#entryId').val();
        const action = entryId ? 'edit' : 'add';
        
        $.ajax({
            url: 'ajax/entry_ajax.php',
            type: 'POST',
            data: formData + '&action=' + action,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#entryModal').modal('hide');
                    alert(response.message);
                    location.reload(); // Reload the page to show updated data
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while processing your request.');
            }
        });
    });
    
    // Delete Entry
    $(document).on('click', '.delete-entry', function(e) {
        e.preventDefault();
        const entryId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this entry?')) {
            $.ajax({
                url: 'ajax/entry_ajax.php',
                type: 'POST',
                data: { id: entryId, action: 'delete' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        location.reload(); // Reload the page to show updated data
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the entry.');
                }
            });
        }
    });
});