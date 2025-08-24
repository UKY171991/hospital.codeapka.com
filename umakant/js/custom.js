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
});