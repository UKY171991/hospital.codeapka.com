$(function(){
  // Initialize DataTable (assuming it's already initialized in the PHP file)
  var table = window.doctorTable;

  // Function to load users into the "Added By" dropdowns
  function loadAddedByUsers(selectedUserId = null) {
    $.get('ajax/user_api.php', { action: 'list' }, function(r){
      if(r && r.success && r.data){
        var options = '<option value="">Select User</option>';
        $.each(r.data, function(i, user){
          options += '<option value="' + user.id + '"' + (selectedUserId == user.id ? ' selected' : '') + '>' + user.username + '</option>';
        });
        $('#filterAddedBy, #addDoctorAddedBy, #editDoctorAddedBy').html(options);
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

  // Handle click on Add New Doctor button
  $(document).on('click', '#addDoctorBtn', function(){
    $('#addDoctorForm')[0].reset(); // Clear form fields
    loadAddedByUsers(); // Reload users for add form
    $('#addDoctorModal').modal('show');
  });

  // Handle Save Changes button click in the add modal
  $(document).on('click', '#createDoctor', function(e){
    e.preventDefault();
    var formData = $('#addDoctorForm').serializeArray();
    formData.push({name: 'action', value: 'save'});

    $.post('ajax/doctor_api.php', formData, function(r){
      if(r && r.success){
        toastr.success(r.message || 'Doctor added successfully!');
        $('#addDoctorModal').modal('hide');
        table.ajax.reload(null, false);
      } else {
        console.error('Failed to add doctor:', r && r.message);
        toastr.error((r && r.message) || 'Failed to add doctor');
      }
    }, 'json').fail(function(xhr){
      console.error('Ajax error adding doctor', xhr);
      toastr.error('Server error adding doctor');
    });
  });

  // Handle click on Edit button
  $(document).on('click', '#doctorTable .edit-btn', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    if(!id){ console.warn('edit-btn clicked but data-id missing'); return; }

    // Fetch doctor data
    $.get('ajax/doctor_api.php', { action: 'get', id: id }, function(r){
      if(r && r.success){
        var d = r.data;
        // Populate the form fields
        $('#editDoctorId').val(d.id);
        $('#editDoctorName').val(d.name);
        $('#editDoctorHospital').val(d.hospital);
        $('#editDoctorContactNo').val(d.contact_no);
        $('#editDoctorPercent').val(d.percent);
        $('#editDoctorAddress').val(d.address);
        loadAddedByUsers(d.added_by); // Load users and pre-select the doctor's added_by user

        // Show the modal
        $('#editDoctorModal').modal('show');
      } else {
        console.error('Failed to load doctor for editing:', r && r.message);
        toastr.error((r && r.message) || 'Failed to load doctor for editing');
      }
    }, 'json').fail(function(xhr){
      console.error('Ajax error fetching doctor for editing', xhr);
      toastr.error('Server error fetching doctor details');
    });
  });

  // Handle Save Changes button click in the edit modal
  $(document).on('click', '#saveDoctorChanges', function(e){
    e.preventDefault();
    var formData = $('#editDoctorForm').serializeArray();
    formData.push({name: 'action', value: 'update'});

    $.post('ajax/doctor_api.php', formData, function(r){
      if(r && r.success){
        toastr.success(r.message || 'Doctor updated successfully!');
        $('#editDoctorModal').modal('hide');
        // Reload the DataTable
        table.ajax.reload(null, false);
      } else {
        console.error('Failed to update doctor:', r && r.message);
        toastr.error((r && r.message) || 'Failed to update doctor');
      }
    }, 'json').fail(function(xhr){
      console.error('Ajax error updating doctor', xhr);
      toastr.error('Server error updating doctor');
    });
  });

  // Handle click on View button
  $(document).on('click', '#doctorTable .view-btn', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    if(!id){ console.warn('view-btn clicked but data-id missing'); return; }

    $.get('ajax/doctor_api.php', { action: 'get', id: id }, function(r){
      if(r && r.success){
        var d = r.data;
        $('#viewDoctorId').text(d.id);
        $('#viewDoctorName').text(d.name);
        $('#viewDoctorHospital').text(d.hospital);
        $('#viewDoctorContactNo').text(d.contact_no);
        $('#viewDoctorPercent').text(d.percent);
        $('#viewDoctorAddress').text(d.address);
        $('#viewDoctorAddedBy').text(d.added_by_username);
        $('#viewDoctorCreatedAt').text(d.created_at);
        $('#viewDoctorModal').modal('show');
      } else {
        console.error('Failed to load doctor for viewing:', r && r.message);
        toastr.error((r && r.message) || 'Failed to load doctor for viewing');
      }
    }, 'json').fail(function(xhr){
      console.error('Ajax error fetching doctor for viewing', xhr);
      toastr.error('Server error fetching doctor details');
    });
  });

  // Handle click on Delete button
  $(document).on('click', '#doctorTable .del-btn', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    if(!id){ console.warn('del-btn clicked but data-id missing'); return; }

    if(confirm('Are you sure you want to delete this doctor?')){
      $.post('ajax/doctor_api.php', { action: 'delete', id: id }, function(r){
        if(r && r.success){
          toastr.success(r.message || 'Doctor deleted successfully!');
          table.ajax.reload(null, false);
        } else {
          console.error('Failed to delete doctor:', r && r.message);
          toastr.error((r && r.message) || 'Failed to delete doctor');
        }
      }, 'json').fail(function(xhr){
        console.error('Ajax error deleting doctor', xhr);
        toastr.error('Server error deleting doctor');
      });
    }
  });

  // Handle change event on the "Filter: Added By" dropdown
  $(document).on('change', '#filterAddedBy', function() {
    table.ajax.reload(); // Reload the DataTable when the filter changes
  });
});