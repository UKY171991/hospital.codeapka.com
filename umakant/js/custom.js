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
        $('#doctorModal').modal('show');
    };
    
    // Save Doctor (Add or Edit)
    $('#saveDoctorBtn').on('click', function() {
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
                    $('#doctorId').val(doctor.id);
                    $('#doctorName').val(doctor.name);
                    $('#doctorSpecialization').val(doctor.specialization);
                    $('#doctorPhone').val(doctor.phone);
                    $('#doctorEmail').val(doctor.email);
                    $('#doctorAddress').val(doctor.address);
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
        $('#patientModal').modal('show');
    };
    
    // Save Patient (Add or Edit)
    $('#savePatientBtn').on('click', function() {
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
                    $('#patientId').val(patient.id);
                    $('#patientName').val(patient.name);
                    $('#patientMobile').val(patient.mobile);
                    $('#patientFatherHusband').val(patient.father_husband);
                    $('#patientAddress').val(patient.address);
                    $('#patientSex').val(patient.sex);
                    $('#patientAge').val(patient.age);
                    $('#patientAgeUnit').val(patient.age_unit);
                    $('#patientUHID').val(patient.uhid);
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
        $('#categoryModal').modal('show');
    };
    
    // Save Category (Add or Edit)
    $('#saveCategoryBtn').on('click', function() {
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
                    $('#categoryId').val(category.id);
                    $('#categoryName').val(category.name);
                    $('#categoryDescription').val(category.description);
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
                        alert('Error: ' . response.message);
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
        $('#testModal').modal('show');
    };
    
    // Save Test (Add or Edit)
    $('#saveTestBtn').on('click', function() {
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
                    $('#testId').val(test.id);
                    $('#testCategoryId').val(test.category_id);
                    $('#testName').val(test.name);
                    $('#testDescription').val(test.description);
                    $('#testPrice').val(test.price);
                    $('#testUnit').val(test.unit);
                    $('#testSpecimen').val(test.specimen);
                    $('#testDefaultResult').val(test.default_result);
                    $('#testReferenceRange').val(test.reference_range);
                    $('#testMin').val(test.min);
                    $('#testMax').val(test.max);
                    $('#testSubHeading').val(test.sub_heading);
                    $('#testCode').val(test.test_code);
                    $('#testMethod').val(test.method);
                    $('#testPrintNewPage').val(test.print_new_page);
                    $('#testShortcut').val(test.shortcut);
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
        $('#userModal').modal('show');
    };
    
    // Save User (Add or Edit)
    $('#saveUserBtn').on('click', function() {
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
                    $('#userId').val(user.id);
                    $('#userUsername').val(user.username);
                    $('#userPassword').val(''); // Clear password field for security
                    $('#userFullName').val(user.full_name);
                    $('#userEmail').val(user.email);
                    $('#userRole').val(user.role);
                    $('#userIsActive').val(user.is_active);
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
        $('#entryModal').modal('show');
    };
    
    // Save Entry (Add or Edit)
    $('#saveEntryBtn').on('click', function() {
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
                    $('#entryId').val(entry.id);
                    $('#entryPatientId').val(entry.patient_id);
                    $('#entryDoctorId').val(entry.doctor_id);
                    $('#entryTestId').val(entry.test_id);
                    $('#entryReferringDoctor').val(entry.referring_doctor);
                    $('#entryEntryDate').val(entry.entry_date);
                    $('#entryResultValue').val(entry.result_value);
                    $('#entryUnit').val(entry.unit);
                    $('#entryRemarks').val(entry.remarks);
                    $('#entryStatus').val(entry.status);
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