$(function(){
  // Initialize DataTable (assuming it's already initialized in the PHP file)
  var table = window.doctorTable;

  // Handle click on Add New Doctor button
  $(document).on('click', '#addDoctorBtn', function(){
    $('#addDoctorForm')[0].reset(); // Clear form fields
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
});
