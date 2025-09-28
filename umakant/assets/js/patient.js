$(function(){
  // Initialize DataTable
  window.patientTable = $('#patientsTable').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: {
      url: 'ajax/patient_api.php',
      type: 'POST', // DataTables sends POST for server-side processing
      data: function(d){
        d.action = 'list';
        var addedBy = $('#filterAddedBy').val();
        if (addedBy) d.added_by = addedBy;
      },
      dataSrc: 'data',
      cache: false // Prevent browser caching
    },
    columns: [
      { data: 'id', render: function(data, type, row, meta){ return meta.row + meta.settings._iDisplayStart + 1; } }, // Auto-incrementing ID
      { data: 'uhid', defaultContent: 'N/A' },
      { data: 'name', className: 'text-truncate', defaultContent: '' },
      { data: 'mobile', defaultContent: '' },
      { 
        data: null, 
        render: function(data, type, row){
          let age = row.age ? row.age + ' ' + (row.age_unit || 'Years') : 'N/A';
          let gender = row.gender ? `<span class="badge badge-${getGenderBadgeClass(row.gender)}">${row.gender}</span>` : '';
          return `${age}<br>${gender}`;
        },
        defaultContent: ''
      },
      { data: 'address', className: 'text-truncate', defaultContent: '' },
      { data: 'created_at', render: function(data){ return formatDate(data); } },
      { data: 'added_by_name', defaultContent: 'System' },
      { 
        data: null, 
        orderable: false, 
        searchable: false, 
        render: function(data, type, row){
          return `<div class="btn-group btn-group-sm action-buttons" role="group" aria-label="Actions" style="white-space:nowrap;">
                    <button class="btn btn-primary btn-action view-btn" data-id="${row.id}" title="View"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-info btn-action edit-btn" data-id="${row.id}" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger del-btn" data-id="${row.id}" title="Delete"><i class="fas fa-trash"></i></button>
                  </div>`;
        } 
      }
    ],
    order: [[0, 'desc']], // Order by ID column descending
    dom: 'Bfrtip',
    buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
    pageLength: 25
  });

  var table = window.patientTable; // Reference to the DataTable instance

  // Function to load users into the "Added By" dropdowns
  function loadAddedByUsers(selectedUserId = null) {
    $.get('ajax/user_api.php', { action: 'list' }, function(r){
      if(r && r.success && r.data){
        var options = '<option value="">All</option>';
        $.each(r.data, function(i, user){
          options += `<option value="${user.id}" ${selectedUserId == user.id ? 'selected' : ''}>${user.username}</option>`;
        });
        $('#filterAddedBy, #patientAddedBy').html(options); // Populate filter and modal dropdown
      } else {
        console.error('Failed to load users:', r && r.message);
        toastr.error((r && r.message) || 'Failed to load users for "Added By" dropdown.');
      }
    }, 'json').fail(function(xhr){
      console.error('Ajax error fetching users', xhr);
      toastr.error('Server error fetching users for "Added By" dropdown.');
    });
  }

  // Load users when the page loads
  loadAddedByUsers();

  // Handle click on Add New Patient button
  $(document).on('click', '#addPatientBtn', function() {
    resetForm();
    generateUHID();
    loadAddedByUsers(); // Reload users for add form
    $('#modalTitle').text('Add New Patient');
    $('#patientModal').modal('show');
  });

  // Handle Save Patient button click in the add/edit modal
  $(document).on('submit', '#patientForm', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('action', 'save');

    // Show loading on button
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
      url: 'ajax/patient_api.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(r) {
        if (r && r.success) {
          toastr.success(r.message || 'Patient saved successfully!');
          $('#patientModal').modal('hide');
          table.ajax.reload(null, false); // Reload the DataTable
        } else {
          console.error('Failed to save patient:', r && r.message);
          toastr.error((r && r.message) || 'Failed to save patient');
        }
      },
      error: function(xhr) {
        let errorMessage = 'Failed to save patient';
        try {
          const response = JSON.parse(xhr.responseText || '{}');
          if (response && response.message) {
            errorMessage = response.message;
          } else if (response && response.debug) {
            errorMessage = (response.message || errorMessage) + ' — Debug: ' + JSON.stringify(response.debug);
          }
        } catch (e) {
          if (xhr.responseText) errorMessage = xhr.responseText;
        }
        toastr.error(errorMessage);
      },
      complete: function() {
        submitBtn.html(originalText).prop('disabled', false);
      }
    });
  });

  // Handle click on Edit button
  $(document).on('click', '#patientsTable .edit-btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    if (!id) { console.warn('edit-btn clicked but data-id missing'); return; }

    $.get('ajax/patient_api.php', { action: 'get', id: id }, function(r) {
      if (r && r.success) {
        populateForm(r.data);
        loadAddedByUsers(r.data.added_by); // Load users and pre-select the patient's added_by user
        $('#modalTitle').text('Edit Patient');
        $('#patientModal').modal('show');
      } else {
        console.error('Failed to load patient for editing:', r && r.message);
        toastr.error((r && r.message) || 'Failed to load patient for editing');
      }
    }, 'json').fail(function(xhr) {
      console.error('Ajax error fetching patient for editing', xhr);
      toastr.error('Server error fetching patient details');
    });
  });

  // Handle click on Delete button
  $(document).on('click', '#patientsTable .del-btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    if (!id) { console.warn('del-btn clicked but data-id missing'); return; }

    if (confirm('Are you sure you want to delete this patient?')) {
      $.post('ajax/patient_api.php', { action: 'delete', id: id }, function(r) {
        if (r && r.success) {
          toastr.success(r.message || 'Patient deleted successfully!');
          table.ajax.reload(null, false); // Reload the DataTable
        } else {
          console.error('Failed to delete patient:', r && r.message);
          toastr.error((r && r.message) || 'Failed to delete patient');
        }
      }, 'json').fail(function(xhr) {
        console.error('Ajax error deleting patient', xhr);
        toastr.error('Server error deleting patient');
      });
    }
  });

  // Handle click on View button
  $(document).on('click', '#patientsTable .view-btn', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    if (!id) { console.warn('view-btn clicked but data-id missing'); return; }

    $.get('ajax/patient_api.php', { action: 'get', id: id }, function(r) {
      if (r && r.success) {
        renderPatientDetails(r.data);
        $('#viewPatientModal').modal('show');
      } else {
        console.error('Failed to load patient for viewing:', r && r.message);
        toastr.error((r && r.message) || 'Failed to load patient for viewing');
      }
    }, 'json').fail(function(xhr) {
      console.error('Ajax error fetching patient for viewing', xhr);
      toastr.error('Server error fetching patient details');
    });
  });

  // Handle change event on the "Filter: Added By" dropdown
  $(document).on('change', '#filterAddedBy', function() {
    table.ajax.reload(); // Reload the DataTable when the filter changes
  });

  // Utility Functions
  function resetForm() {
    $('#patientForm')[0].reset();
    $('#patientId').val('');
    generateUHID();
  }

  function populateForm(patient) {
    $('#patientId').val(patient.id);
    $('#patientName').val(patient.name);
    $('#patientUHID').val(patient.uhid);
    $('#patientMobile').val(patient.mobile);
    $('#patientEmail').val(patient.email);
    $('#patientAge').val(patient.age);
    $('#patientAgeUnit').val(patient.age_unit);
    $('#patientGender').val(patient.gender);
    $('#patientFatherHusband').val(patient.father_husband);
    $('#patientAddress').val(patient.address);
    // Assuming patient.added_by contains the ID of the user who added the patient
    $('#patientAddedBy').val(patient.added_by); 
  }

  function generateUHID() {
    const timestamp = Date.now().toString().slice(-6);
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    const uhid = 'P' + timestamp + random;
    $('#patientUHID').val(uhid);
  }

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
      case 'Female': return 'pink';
      case 'Other': return 'secondary';
      default: return 'secondary';
    }
  }

  function renderPatientDetails(patient) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>UHID:</strong></td>
                                <td><span class="badge badge-primary">${patient.uhid || 'N/A'}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>${patient.name}</td>
                            </tr>
                            <tr>
                                <td><strong>Father/Husband:</strong></td>
                                <td>${patient.father_husband || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Age:</strong></td>
                                <td>${patient.age ? patient.age + ' ' + (patient.age_unit || 'Years') : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Gender:</strong></td>
                                <td>${patient.gender ? `<span class="badge badge-${getGenderBadgeClass(patient.gender)}">${patient.gender}</span>` : 'N/A'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Mobile:</strong></td>
                                <td><i class="fas fa-mobile-alt"></i> ${patient.mobile}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>${patient.email ? `<i class="fas fa-envelope"></i> ${patient.email}` : 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td>${patient.address || 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Registration:</strong></td>
                                <td>${formatDate(patient.created_at)}</td>
                            </tr>
                            <tr>
                                <td><strong>Added By:</strong></td>
                                <td>${patient.added_by_name || 'System'}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#patientViewDetails').html(html);
  }
});

// Global functions for direct calls from HTML (e.g., onclick)
function openAddPatientModal() {
  $(document).trigger('click', '#addPatientBtn'); // Trigger the click event on the add button
}

// The exportPatients function was in patient.php, but now we're using DataTables buttons for export.
// If a separate "Export All" button is still desired outside DataTables, it would need to be re-implemented.
// For now, relying on DataTables' built-in export buttons.
