<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-calendar-check mr-2"></i>OPD Appointment Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">OPD Appointments</li>
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
                            <h3 id="totalAppointments">0</h3>
                            <p>Total Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pendingAppointments">0</h3>
                            <p>Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="confirmedAppointments">0</h3>
                            <p>Confirmed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="cancelledAppointments">0</h3>
                            <p>Cancelled</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
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
                                <i class="fas fa-list mr-1"></i>
                                OPD Appointments Directory
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" id="addAppointmentBtn">
                                    <i class="fas fa-plus"></i> Add New Appointment
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <!-- Appointments Table -->
                            <div class="table-responsive">
                                <table id="appointmentTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>ID</th>
                                            <th>Patient Name</th>
                                            <th>Doctor</th>
                                            <th>Department</th>
                                            <th>Appointment Date</th>
                                            <th>Time Slot</th>
                                            <th>Contact</th>
                                            <th>Status</th>
                                            <th>Added By</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add/Edit Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Add New OPD Appointment</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="appointmentForm">
                <div class="modal-body">
                    <input type="hidden" id="appointmentId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientName">
                                    <i class="fas fa-user mr-1"></i>
                                    Patient Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="patientName" name="patient_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientContact">
                                    <i class="fas fa-phone mr-1"></i>
                                    Contact Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="patientContact" name="patient_contact" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientEmail">
                                    <i class="fas fa-envelope mr-1"></i>
                                    Email
                                </label>
                                <input type="email" class="form-control" id="patientEmail" name="patient_email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="patientAge">
                                    <i class="fas fa-birthday-cake mr-1"></i>
                                    Age
                                </label>
                                <input type="number" class="form-control" id="patientAge" name="patient_age" min="0" max="150">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departmentName">
                                    <i class="fas fa-building mr-1"></i>
                                    Department <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="departmentName" name="department" required>
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctorName">
                                    <i class="fas fa-user-md mr-1"></i>
                                    Doctor <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="doctorName" name="doctor_name" required>
                                    <option value="">Select Doctor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointmentDate">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Appointment Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="timeSlot">
                                    <i class="fas fa-clock mr-1"></i>
                                    Time Slot <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="timeSlot" name="time_slot" required>
                                    <option value="">Select Time</option>
                                    <option value="09:00 AM - 10:00 AM">09:00 AM - 10:00 AM</option>
                                    <option value="10:00 AM - 11:00 AM">10:00 AM - 11:00 AM</option>
                                    <option value="11:00 AM - 12:00 PM">11:00 AM - 12:00 PM</option>
                                    <option value="12:00 PM - 01:00 PM">12:00 PM - 01:00 PM</option>
                                    <option value="02:00 PM - 03:00 PM">02:00 PM - 03:00 PM</option>
                                    <option value="03:00 PM - 04:00 PM">03:00 PM - 04:00 PM</option>
                                    <option value="04:00 PM - 05:00 PM">04:00 PM - 05:00 PM</option>
                                    <option value="05:00 PM - 06:00 PM">05:00 PM - 06:00 PM</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="appointmentReason">
                                    <i class="fas fa-notes-medical mr-1"></i>
                                    Reason for Visit
                                </label>
                                <textarea class="form-control" id="appointmentReason" name="reason" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointmentStatus">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="appointmentStatus" name="status" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Confirmed">Confirmed</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="appointmentNotes">
                                    <i class="fas fa-sticky-note mr-1"></i>
                                    Notes
                                </label>
                                <textarea class="form-control" id="appointmentNotes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Appointment Modal -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1" role="dialog" aria-labelledby="viewAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye mr-2"></i>
                    OPD Appointment Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewAppointmentContent">
                <!-- Appointment details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="btn btn-warning" onclick="editAppointmentFromView()">
                    <i class="fas fa-edit"></i> Edit Appointment
                </button>
                <button type="button" class="btn btn-info" onclick="printAppointment()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

<!-- Include DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<!-- Page specific JavaScript -->
<script src="assets/js/appointment.js"></script>

<style>
.small-box {
    position: relative;
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 0.25rem;
}

.small-box > .inner {
    padding: 10px;
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
    overflow-x: auto;
}

.card-outline.card-primary {
    border-top: 3px solid #007bff;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

#appointmentTable {
    width: 100% !important;
    white-space: nowrap;
}

#appointmentTable thead th {
    vertical-align: middle;
    white-space: nowrap;
    padding: 12px 8px;
    font-size: 14px;
}

#appointmentTable tbody td {
    vertical-align: middle;
    padding: 8px;
    font-size: 13px;
}

#appointmentTable_wrapper .dataTables_scroll {
    overflow-x: auto;
}

#appointmentModal .form-group {
    margin-bottom: 1rem;
    clear: both;
}

#appointmentModal label {
    display: block;
    margin-bottom: 0.5rem;
}
</style>

<script>
// Fix for duplicate field issue
$(document).ready(function() {
    $('#appointmentModal').on('show.bs.modal', function() {
        $('#appointmentForm .form-group').each(function() {
            var $labels = $(this).find('label');
            if ($labels.length > 1) {
                $labels.not(':first').remove();
            }
        });
    });
    
    $('#appointmentModal').on('hidden.bs.modal', function() {
        $('#appointmentForm')[0].reset();
    });
});
</script>

<?php require_once 'inc/footer.php'; ?>
