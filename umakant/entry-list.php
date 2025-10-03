<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';

$currentUserId = $_SESSION['user_id'] ?? '';
$currentUserDisplayName = $_SESSION['full_name']
    ?? $_SESSION['username']
    ?? 'Unknown User';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-clipboard-list mr-2"></i>Test Entries</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Test Entries</li>
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
                            <h3 id="totalEntries">0</h3>
                            <p>Total Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="pendingEntries">0</h3>
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
                            <h3 id="completedEntries">0</h3>
                            <p>Completed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3 id="todayEntries">0</h3>
                            <p>Today's Entries</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list-alt mr-1"></i>
                                Test Entry Management
                            </h3>
                            <div class="card-tools d-flex align-items-center">
                                <button type="button" class="btn btn-primary btn-sm mr-2" onclick="openAddEntryModal()">
                                    <i class="fas fa-plus"></i> Add Entry
                                </button>
                                <button type="button" class="btn btn-success btn-sm mr-2" onclick="exportEntries()">
                                    <i class="fas fa-download"></i> Export
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
                            <!-- Search and Filter Row -->
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="entriesSearch" class="form-control" placeholder="Search entries by patient, doctor, test, etc...">
                                        <div class="input-group-append">
                                            <button id="entriesSearchClear" class="btn btn-outline-secondary">Clear</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 ml-auto text-right">
                                    <div class="form-inline float-right">
                                        <label class="mr-2">Per page</label>
                                        <select id="entriesPerPage" class="form-control">
                                            <option value="10">10</option>
                                            <option value="25" selected>25</option>
                                            <option value="50">50</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Advanced Filters -->
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <select id="statusFilter" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="doctorFilter" class="form-control">
                                        <option value="">All Doctors</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="testFilter" class="form-control">
                                        <option value="">All Tests</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" id="dateFromFilter" class="form-control" title="From Date">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" id="dateToFilter" class="form-control" title="To Date">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary btn-block" onclick="clearFilters()">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>

                            <!-- Entries DataTable -->
                            <div class="table-responsive">
                                <table id="entriesTable" class="table table-bordered table-striped table-hover entries-table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Entry ID</th>
                                            <th>Patient Name</th>
                                            <th>Doctor / Added By</th>
                                            <th>Tests</th>
                                            <th>Status</th>
                                            <th>Test Date</th>
                                            <th>Added By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
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

<!-- Entry Modal -->
<div class="modal fade" id="entryModal" tabindex="-1" role="dialog" aria-labelledby="entryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="entryModalLabel">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    <span id="modalTitle">Add New Test Entry</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="entryForm">
                <div class="modal-body">
                    <input type="hidden" id="entryId" name="id">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-user-check mr-1"></i>
                                    Added By
                                </label>
                                <input type="hidden" id="entryAddedBy" name="added_by" value="<?= htmlspecialchars((string)$currentUserId, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="text" class="form-control" id="entryAddedByDisplay" value="<?= htmlspecialchars($currentUserDisplayName, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entryPatient">
                                    <i class="fas fa-user mr-1"></i>
                                    Patient <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="entryPatient" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entryDoctor">
                                    <i class="fas fa-user-md mr-1"></i>
                                    Doctor <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="entryDoctor" name="doctor_id" required>
                                    <option value="">Select Doctor</option>
                                </select>
                                <small id="doctorAddedByInfo" class="form-text text-muted" style="display: none;"></small>
                            </div>
                        </div>
                    </div>


                    <!-- Inline Test Management Section -->
                    <div class="form-group">
                        <label>
                            <i class="fas fa-list mr-1"></i>
                            Tests <span class="text-danger">*</span>
                        </label>
                        
                        <!-- Test Selection Controls -->
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="showAddTestInterface()">
                                    <i class="fas fa-plus mr-1"></i>
                                    Add Test
                                </button>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="badge badge-info" id="selectedTestsCount">0 tests selected</span>
                            </div>
                        </div>
                        
                        <!-- Add Test Interface -->
                        <div id="addTestInterface" class="card mb-3" style="display: none;">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h6 class="mb-0">
                                            <i class="fas fa-plus mr-1"></i>
                                            Add Test to Entry
                                        </h6>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="hideAddTestInterface()">
                                            <i class="fas fa-times"></i>
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Single Line Test Selection -->
                                <div class="row">
                                    <div class="col-md-5">
                                        <label class="form-label">
                                            <i class="fas fa-folder mr-1"></i>
                                            Category
                                        </label>
                                        <select class="form-control" id="testCategorySelect" onchange="loadTestsForCategory()">
                                            <option value="">Choose category...</option>
                                        </select>
                                </div>
                                    <div class="col-md-5">
                                        <label class="form-label">
                                            <i class="fas fa-vial mr-1"></i>
                                            Test
                                        </label>
                                        <select class="form-control" id="testSelect">
                                            <option value="">Choose category first...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-success btn-block" onclick="addSelectedTest()">
                                            <i class="fas fa-plus"></i>
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Selected Tests Display -->
                        <div id="testsByCategoryContainer">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                No tests selected. Click "Add Test" to choose tests for this entry.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="entryStatus">
                                    <i class="fas fa-flag mr-1"></i>
                                    Status
                                </label>
                                <select class="form-control" id="entryStatus" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryAmount">
                                    <i class="fas fa-rupee-sign mr-1"></i>
                                    Total Amount (Auto-calculated)
                                </label>
                                <input type="number" class="form-control" id="entryAmount" name="amount" step="0.01" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="entryDiscount">
                                    <i class="fas fa-percentage mr-1"></i>
                                    Discount % (Auto-calculated)
                                </label>
                                <input type="number" class="form-control" id="entryDiscount" name="discount" min="0" max="100" step="0.01" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="entryNotes">
                            <i class="fas fa-sticky-note mr-1"></i>
                            Notes
                        </label>
                        <textarea class="form-control" id="entryNotes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="saveEntryBtn">
                        <i class="fas fa-save"></i> Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- View Entry Modal -->
<div class="modal fade" id="viewEntryModal" tabindex="-1" role="dialog" aria-labelledby="viewEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewEntryModalLabel">
                    <i class="fas fa-eye mr-2"></i>
                    View Entry Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                <div class="form-group">
                            <label><strong>Patient:</strong></label>
                            <p id="viewPatientName" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Doctor:</strong></label>
                            <p id="viewDoctorName" class="form-control-plaintext"></p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                <div class="form-group">
                            <label><strong>Entry Date:</strong></label>
                            <p id="viewEntryDate" class="form-control-plaintext"></p>
                        </div>
                        </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Status:</strong></label>
                            <p id="viewEntryStatus" class="form-control-plaintext"></p>
                    </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><strong>Tests:</strong></label>
                    <div id="viewSelectedTests">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            No tests found for this entry.
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><strong>Notes:</strong></label>
                    <p id="viewEntryNotes" class="form-control-plaintext"></p>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Total Price:</strong></label>
                            <p id="viewTotalPrice" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Discount:</strong></label>
                            <p id="viewDiscount" class="form-control-plaintext"></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label><strong>Final Amount:</strong></label>
                            <p id="viewFinalAmount" class="form-control-plaintext"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="editFromViewBtn">
                    <i class="fas fa-edit mr-1"></i>
                    Edit Entry
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Ensure showAlert exists for uniform messaging
if (typeof window.showAlert !== 'function') {
    window.showAlert = function(message, type) {
        if (window.toastr && typeof window.toastr[type] === 'function') {
            window.toastr[type](message);
        } else if (window.toastr && typeof window.toastr.error === 'function') {
            window.toastr.error(message);
        } else {
            console[(type === 'error' ? 'error' : 'log')](message);
        }
    };
}

// Global variables
let entriesTable;
let allEntries = [];
let entriesCurrentPage = 1;
let entriesPerPageCount = 25;
let doctorDirectory = {};
const currentUserId = <?= json_encode($currentUserId, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const currentUserDisplayName = <?= json_encode($currentUserDisplayName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
let cachedPatientsByIdentifiers = {};
let cachedDoctorsByIdentifiers = {};

function normalizeIdentifierValue(value) {
    if (value === null || value === undefined) return null;
    const str = String(value).trim();
    if (!str) return null;
    if (/^-?\d+$/.test(str)) {
        return String(parseInt(str, 10));
    }
    return str.toLowerCase();
}

function getAddedByIdentifierSet(explicitUserId) {
    const identifiers = new Set();
    const hiddenField = $('#entryAddedBy');
    const displayField = $('#entryAddedByDisplay');

    const addValue = (value) => {
        const normalized = normalizeIdentifierValue(value);
        if (normalized) {
            identifiers.add(normalized);
        }
    };

    if (explicitUserId !== undefined && explicitUserId !== null && explicitUserId !== '') {
        addValue(explicitUserId);
    }

    const hiddenValue = hiddenField.val();
    addValue(hiddenValue);

    const formGroup = hiddenField.closest('.form-group');
    addValue(displayField && displayField.length ? displayField.val() : null);
    addValue(formGroup.data('username'));
    addValue(formGroup.data('full-name'));

    return identifiers;
}

function extractFieldValue(obj, path) {
    if (!obj) return null;
    if (!path) return null;
    if (path.indexOf('.') === -1) {
        return obj[path];
    }
    return path.split('.').reduce((acc, key) => (acc && acc[key] !== undefined ? acc[key] : null), obj);
}

function recordMatchesAddedBy(record, identifiersSet, candidateFields = []) {
    if (!record || !identifiersSet || identifiersSet.size === 0) {
        return false;
    }
    return candidateFields.some(field => {
        const value = extractFieldValue(record, field);
        return identifiersSet.has(normalizeIdentifierValue(value));
    });
}

function renderPatientOptions(patients, selectedId) {
    const select = $('#entryPatient');
    select.empty();
    select.append($('<option>', { value: '', text: 'Select Patient' }));

    patients.forEach(function(patient) {
        select.append(
            $('<option>', {
                value: patient.id,
                text: patient.label
            })
        );
    });

    if (selectedId !== undefined && selectedId !== null && selectedId !== '') {
        select.val(String(selectedId));
    } else {
        select.val('');
    }

    select.trigger('change.select2');
}

function renderDoctorOptions(doctors, selectedId) {
    const entrySelect = $('#entryDoctor');
    const filterSelect = $('#doctorFilter');

    entrySelect.empty();
    filterSelect.empty();

    entrySelect.append($('<option>', { value: '', text: 'Select Doctor' }));
    filterSelect.append($('<option>', { value: '', text: 'All Doctors' }));

    const directory = {};

    doctors.forEach(function(doctor) {
        const optionLabel = `${doctor.name}${doctor.addedByUsername ? ' (Added by ' + doctor.addedByUsername + ')' : ''}`;
        entrySelect.append(
            $('<option>', {
                value: doctor.id,
                text: optionLabel
            }).attr('data-added-by', doctor.addedByUsername || '')
        );

        const filterLabel = `${doctor.name}${doctor.addedByUsername ? ' (' + doctor.addedByUsername + ')' : ''}`;
        filterSelect.append(
            $('<option>', {
                value: doctor.id,
                text: filterLabel
            })
        );

        directory[doctor.id] = {
            name: doctor.name,
            addedByUsername: doctor.addedByUsername || ''
        };
    });

    doctorDirectory = directory;

    if (selectedId !== undefined && selectedId !== null && selectedId !== '') {
        entrySelect.val(String(selectedId));
    } else {
        entrySelect.val('');
    }

    entrySelect.trigger('change.select2');
    updateDoctorAddedByDisplay();
}


function populateAddedByDisplay(userId) {
    const hiddenField = $('#entryAddedBy');
    const displayField = $('#entryAddedByDisplay');
    const formGroup = hiddenField.closest('.form-group');

    const targetId = userId !== undefined && userId !== null && userId !== '' ? userId : currentUserId;
    hiddenField.val(targetId);

    $.get('patho_api/user.php', { action: 'get', id: targetId, ajax: 1 })
        .done(function(response) {
            if (response.success && response.data) {
                const user = response.data;
                const displayName = user.full_name && user.full_name.trim() !== ''
                    ? user.full_name
                    : (user.username || ('User #' + user.id));
                displayField.val(displayName);
                formGroup.data('username', user.username || '');
                formGroup.data('full-name', user.full_name || '');
            } else {
                displayField.val('Unknown User');
                formGroup.removeData('username');
                formGroup.removeData('full-name');
            }
        })
        .fail(function() {
            displayField.val('Unknown User');
            formGroup.removeData('username');
            formGroup.removeData('full-name');
        });
}

function getIdentifiersCacheKey(identifiersSet) {
    if (!identifiersSet || identifiersSet.size === 0) {
        return '';
    }
    return Array.from(identifiersSet).sort().join('|');
}

function loadPatientsForUser(userId, selectedPatientId) {
    const select = $('#entryPatient');
    const identifiersSet = getAddedByIdentifierSet(userId);
    const cacheKey = getIdentifiersCacheKey(identifiersSet);

    if (!cacheKey) {
        select.html('<option value="">Select Patient</option>');
        select.trigger('change.select2');
        return;
    }

    if (cachedPatientsByIdentifiers[cacheKey]) {
        renderPatientOptions(cachedPatientsByIdentifiers[cacheKey], selectedPatientId);
        return;
    }

    select.prop('disabled', true);
    select.html('<option value="">Loading patients...</option>');

    const requestIdentifier = Array.from(identifiersSet)[0];

    $.get('ajax/patient_api.php', { action: 'list', ajax: 1, added_by: requestIdentifier })
        .done(function(response) {
            if (!response.success || !Array.isArray(response.data)) {
                throw new Error(response.message || 'Unexpected response');
            }

            const filteredPatients = response.data.filter(function(patient) {
                return recordMatchesAddedBy(patient, identifiersSet, [
                    'added_by',
                    'added_by_name',
                    'added_by_username',
                    'addedBy',
                    'addedByName'
                ]);
            }).map(function(patient) {
                const label = patient.name || patient.full_name || patient.uhid || `Patient #${patient.id}`;
                return {
                    id: patient.id,
                    label: label
                };
            });

            cachedPatientsByIdentifiers[cacheKey] = filteredPatients;
            renderPatientOptions(filteredPatients, selectedPatientId);
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            select.html(`<option value="">Failed to load patients (${errorMsg})</option>`);
        })
        .always(function() {
            select.prop('disabled', false);
        });
}

function loadDoctorsForUser(userId, selectedDoctorId) {
    const select = $('#entryDoctor');
    const identifiersSet = getAddedByIdentifierSet(userId);
    const cacheKey = getIdentifiersCacheKey(identifiersSet);

    if (!cacheKey) {
        select.html('<option value="">Select Doctor</option>');
        $('#doctorFilter').html('<option value="">All Doctors</option>');
        return;
    }

    if (cachedDoctorsByIdentifiers[cacheKey]) {
        const { doctors, directory } = cachedDoctorsByIdentifiers[cacheKey];
        doctorDirectory = directory;
        renderDoctorOptions(doctors, selectedDoctorId);
        return;
    }

    select.prop('disabled', true);
    select.html('<option value="">Loading doctors...</option>');

    const requestIdentifier = Array.from(identifiersSet)[0];

    $.get('ajax/doctor_api.php', { action: 'list', ajax: 1, added_by: requestIdentifier })
        .done(function(response) {
            if (!response.success || !Array.isArray(response.data)) {
                throw new Error(response.message || 'Unexpected response');
            }
            const filteredDoctors = response.data.filter(function(doctor) {
                return recordMatchesAddedBy(doctor, identifiersSet, [
                    'added_by',
                    'added_by_username',
                    'addedBy',
                    'addedByUsername'
                ]);
            }).map(function(doctor) {
                return {
                    id: doctor.id,
                    name: doctor.name || 'Unknown',
                    addedByUsername: doctor.added_by_username || doctor.addedByUsername || ''
                };
            });

            const directory = {};
            filteredDoctors.forEach(function(doctor) {
                directory[doctor.id] = {
                    name: doctor.name,
                    addedByUsername: doctor.addedByUsername || ''
                };
            });

            cachedDoctorsByIdentifiers[cacheKey] = { doctors: filteredDoctors, directory };
            doctorDirectory = directory;
            renderDoctorOptions(filteredDoctors, selectedDoctorId);
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            select.html(`<option value="">Failed to load doctors (${errorMsg})</option>`);
        })
        .always(function() {
            select.prop('disabled', false);
        });
}

// Utility to wait for Select2 availability before initializing
function waitForSelect2(callback, attempt = 0) {
    if (typeof jQuery !== 'undefined' && $.fn && typeof $.fn.select2 === 'function') {
        callback();
        return;
    }
    if (attempt >= 20) {
        console.warn('Select2 plugin did not load in time');
        return;
    }
    setTimeout(function() {
        waitForSelect2(callback, attempt + 1);
    }, 100);
}

function initializeEntrySelect2Fields() {
    const $selects = $('.select2');
    if (!$selects.length) {
        return;
    }

    $selects.each(function() {
        const $el = $(this);
        if (typeof $el.data('select2') !== 'undefined') {
            $el.select2('destroy');
        }
    });

    $selects.select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#entryModal')
    });
}

// Initialize page
$(document).ready(function() {
    loadDropdownsForEntry();
    loadEntries();
    initializeEventListeners();
    loadStats();
});

function initializeEventListeners() {
    // Form submission
    $('#entryForm').on('submit', function(e) {
        e.preventDefault();
        saveEntryData();
    });

    $('#entryDoctor').on('change', updateDoctorAddedByDisplay);
    
    // Search functionality
    $('#entriesSearch').on('input', function() {
        applyEntriesFilters();
    });

    // Clear search button
    $('#entriesSearchClear').click(function(e) {
        $('#entriesSearch').val('');
        applyEntriesFilters();
    });

    // Per page change
    $('#entriesPerPage').change(function() {
        entriesPerPageCount = parseInt($(this).val());
        entriesCurrentPage = 1;
        applyEntriesFilters();
    });

    // Modal reset on hide
    $('#entryModal').on('hidden.bs.modal', function() {
        resetModalForm();
    });

    $('#entryModal').on('shown.bs.modal', function() {
        populateAddedBySelect($('#entryAddedBy').data('prefill'));
    });

    // Filter changes
    $('#statusFilter, #doctorFilter, #testFilter, #dateFromFilter, #dateToFilter').on('change', function() {
        entriesCurrentPage = 1;
        applyEntriesFilters();
    });
}

function updateDoctorAddedByDisplay() {
    const selected = $('#entryDoctor option:selected');
    const info = $('#doctorAddedByInfo');
    if (!selected || !selected.val()) {
        info.text('').hide();
        return;
    }
    const addedBy = selected.data('added-by');
    if (addedBy) {
        info.text(`Doctor added by: ${addedBy}`).show();
    } else {
        const doctorId = selected.val();
        const fallback = doctorDirectory[doctorId] && doctorDirectory[doctorId].addedByUsername;
        if (fallback) {
            info.text(`Doctor added by: ${fallback}`).show();
        } else {
            info.text('').hide();
        }
    }
}

function loadDropdownsForEntry() {
    // Initialize Select2 for entry form once plugin is ready
    waitForSelect2(initializeEntrySelect2Fields);

    // Load tests for filter only
    $.get('ajax/test_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                let filterOptions = '<option value="">All Tests</option>';
                response.data.forEach(test => {
                    filterOptions += `<option value="${test.id}">${test.name}</option>`;
                });
                $('#testFilter').html(filterOptions);
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            if (typeof showAlert === 'function') {
                showAlert('Failed to load tests: ' + errorMsg, 'error');
            } else {
                console.error('Failed to load tests:', errorMsg);
            }
        });

    // Load users for Added By select on page load so data is ready
    $('#entryAddedBy').data('forceReload', true);
    populateAddedBySelect(currentUserId);
    loadPatientsForUser(currentUserId);
    loadDoctorsForUser(currentUserId);
}

function loadEntries() {
    $.get('ajax/entry_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                allEntries = response.data;
                applyEntriesFilters();
            } else {
                showAlert('Error loading entries: ' + (response.message || 'Unknown error'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load entry data: ' + errorMsg, 'error');
        });
}

function openAddEntryModal() {
    resetModalForm();
    $('#selectedTestsCount').text('0 tests selected');
    $('#testsByCategoryContainer').html('<div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>No tests selected. Click "Add Test" to choose tests for this entry.</div>');
    $('#entryModal').modal('show');
}

function applyEntriesFilters() {
    const searchTerm = $('#entriesSearch').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();
    const doctorFilter = $('#doctorFilter').val();
    const testFilter = $('#testFilter').val();
    const dateFrom = $('#dateFromFilter').val();
    const dateTo = $('#dateToFilter').val();
    
    let filteredEntries = allEntries.filter(entry => {
        // Search term filter
        if (searchTerm && 
            !((entry.patient_name || '').toLowerCase().includes(searchTerm)) &&
            !((entry.doctor_name || '').toLowerCase().includes(searchTerm)) &&
            !((entry.test_name || '').toLowerCase().includes(searchTerm)) &&
            !(entry.id.toString().includes(searchTerm))) {
            return false;
        }
        
        // Status filter
        if (statusFilter && entry.status !== statusFilter) {
            return false;
        }
        
        // Doctor filter
        if (doctorFilter && entry.doctor_id != doctorFilter) {
            return false;
        }
        
        // Test filter
        if (testFilter && entry.test_id != testFilter) {
            return false;
        }
        
        // Date range filter
        if (dateFrom || dateTo) {
            const entryDate = new Date(entry.entry_date || entry.created_at);
            if (dateFrom && entryDate < new Date(dateFrom)) {
                return false;
            }
            if (dateTo && entryDate > new Date(dateTo + 'T23:59:59')) {
                return false;
            }
        }
        
        return true;
    });
    
    renderEntriesTable(filteredEntries);
}

function renderEntriesTable(entries) {
    const tbody = $('#entriesTable tbody');
    tbody.empty();
    
    if (entries.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center">No entries found</td></tr>');
        return;
    }
    
    // Calculate pagination
    const startIndex = (entriesCurrentPage - 1) * entriesPerPageCount;
    const endIndex = Math.min(startIndex + entriesPerPageCount, entries.length);
    const pageEntries = entries.slice(startIndex, endIndex);
    
    // Render entries
    pageEntries.forEach((entry, index) => {
        const serialNo = startIndex + index + 1;
        const statusClass = getStatusBadgeClass(entry.status);
        const statusText = formatStatus(entry.status);
        const testDate = formatDate(entry.entry_date || entry.created_at);
        const addedBy = entry.added_by_username || 'Unknown';
        const doctorMeta = entry.doctor_name
            ? `${entry.doctor_name}${entry.doctor_added_by_username ? `<br><small class="text-muted">Added by: ${entry.doctor_added_by_username}</small>` : ''}`
            : 'N/A';
        
        // Handle multiple tests display
        let testDisplay = '';
        if (entry.grouped && entry.tests_count > 1) {
            // Multiple tests entry
            testDisplay = `
                <div class="multiple-tests-display">
                    <span class="badge badge-info">${entry.tests_count} Tests</span>
                    <small class="text-muted d-block">${entry.test_names || 'Multiple Tests'}</small>
                </div>
            `;
        } else {
            // Single test entry
            testDisplay = `<span class="single-test-display">${entry.test_name || 'N/A'}</span>`;
        }
        
        const row = `
            <tr>
                <td class="sr-no-cell">${serialNo}</td>
                <td><span class="entry-id-badge">${entry.id}</span></td>
                <td><span class="patient-name-container">${entry.patient_name || 'N/A'}</span></td>
                <td class="doctor-cell">${doctorMeta}</td>
                <td class="test-name-cell">${testDisplay}</td>
                <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                <td class="test-date-cell">${testDate}</td>
                <td class="added-by-cell">${addedBy}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-info btn-sm" onclick="viewEntry(${entry.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editEntry(${entry.id})" title="Edit Entry">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-entry" data-id="${entry.id}" title="Delete Entry">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
    
    // Bind delete button events
    $('.delete-entry').on('click', function() {
        const id = $(this).data('id');
        deleteEntry(id);
    });
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'completed': return 'badge-success';
        case 'pending': return 'badge-warning';
        case 'cancelled': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

function formatStatus(status) {
    switch (status) {
        case 'completed': return 'Completed';
        case 'pending': return 'Pending';
        case 'cancelled': return 'Cancelled';
        default: return status.charAt(0).toUpperCase() + status.slice(1);
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString();
}

function viewEntry(id) {
    $.get('ajax/entry_api.php', { action: 'get', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const entry = response.data;
                populateViewModal(entry);
                
                // Load multiple tests if this is a grouped entry
                if (entry.grouped && entry.tests_count > 1) {
                    loadEntryTestsForView(id);
                }
                
                $('#viewEntryModal').modal('show');
            } else {
                showAlert('Error loading entry data: ' + (response.message || 'Entry not found'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load entry data: ' + errorMsg, 'error');
        });
}

function editEntry(id) {
    $.get('ajax/entry_api.php', { action: 'get', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const entry = response.data;
                populateEntryForm(entry);
                $('#saveEntryBtn').text('Update Entry');
                $('#testsByCategoryContainer').html('<div class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i>Loading tests...</div>');
                $('#selectedTestsCount').text('Loading tests...');

                // Load tests for editing, fallback to entry data if needed
                loadEntryTests(id, entry);
                
                $('#modalTitle').text('Edit Test Entry');
                $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', false);
                $('#saveEntryBtn').show();
                $('#entryModal').modal('show');
            } else {
                showAlert('Error loading entry data: ' + (response.message || 'Entry not found'), 'error');
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load entry data: ' + errorMsg, 'error');
        });
}

function loadEntryTests(entryId, fallbackEntry = null) {
    $.get('patho_api/entry.php', { action: 'get_tests', entry_id: entryId })
        .done(function(response) {
            if (response.success && response.data.length > 0) {
                selectedTests = response.data.map(test => ({
                    category_id: test.category_id,
                    category_name: test.category_name || 'Unknown Category',
                    test_id: test.test_id,
                    test_name: test.test_name,
                    result_value: test.result_value,
                    unit: test.unit || test.test_unit,
                    price: test.price,
                    discount_amount: test.discount_amount,
                    remarks: test.remarks,
                    min: test.min || test.min_range_male || test.min_range_female || null,
                    max: test.max || test.max_range_male || test.max_range_female || null
                }));
                updateSelectedTestsDisplay();
            } else if (!populateSelectedTestsFromFallback(fallbackEntry)) {
                console.log('No detailed tests found for entry:', entryId);
                clearSelectedTests();
            }
        })
        .fail(function(xhr) {
            console.log('Failed to load tests for entry:', entryId);
            if (!populateSelectedTestsFromFallback(fallbackEntry)) {
                clearSelectedTests();
            }
        });
}

function populateSelectedTestsFromFallback(entry) {
    if (!entry || !entry.test_id) {
        return false;
    }

    selectedTests = [{
        category_id: entry.category_id || null,
        category_name: entry.category_name || 'Unknown Category',
        test_id: entry.test_id,
        test_name: entry.test_name || 'Selected Test',
        result_value: entry.result_value || '',
        unit: entry.unit || '',
        price: parseFloat(entry.price || entry.total_price || 0),
        discount_amount: parseFloat(entry.discount_amount || 0),
        remarks: entry.remarks || '',
        min: entry.min || null,
        max: entry.max || null
    }];

    updateSelectedTestsDisplay();
    return true;
}

function populateEntryForm(entry) {
    $('#entryId').val(entry.id);
    $('#entryPatient').val(entry.patient_id).trigger('change');
    $('#entryDoctor').val(entry.doctor_id).trigger('change');
    $('#entryAddedBy').data('prefill', entry.added_by || currentUserId);
    $('#entryAddedBy').data('forceReload', true);
    populateAddedBySelect(entry.added_by || currentUserId);
    loadPatientsForUser(entry.added_by || currentUserId, entry.patient_id);
    loadDoctorsForUser(entry.added_by || currentUserId, entry.doctor_id);
    $('#entryStatus').val(entry.status || 'pending');
    $('#entryNotes').val(entry.remarks || '');
    updateDoctorAddedByDisplay();
    
    // Calculate and set auto-calculated fields
    calculateEntryTotals();
    
    // Update selected tests display
    updateSelectedTestsDisplay();
}

function populateViewModal(entry) {
    $('#viewPatientName').text(entry.patient_name || 'N/A');
    if (entry.doctor_name) {
        const doctorInfo = `${entry.doctor_name}${entry.doctor_added_by_username ? ' (Added by ' + entry.doctor_added_by_username + ')' : ''}`;
        $('#viewDoctorName').text(doctorInfo);
    } else {
        $('#viewDoctorName').text('N/A');
    }
    $('#viewEntryDate').text(formatDate(entry.entry_date || entry.created_at));
    $('#viewEntryStatus').text(entry.status || 'pending');
    $('#viewEntryNotes').text(entry.remarks || 'N/A');
    
    // Format currency values
    const totalPrice = parseFloat(entry.price || 0);
    const discount = parseFloat(entry.discount_amount || 0);
    const finalAmount = totalPrice - discount;
    
    $('#viewTotalPrice').text('₹' + totalPrice.toFixed(2));
    $('#viewDiscount').text('₹' + discount.toFixed(2));
    $('#viewFinalAmount').text('₹' + finalAmount.toFixed(2));
    
    // Show primary test if available
    if (entry.test_name) {
        $('#viewSelectedTests').html(`
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">${entry.test_name}</h6>
                    <p class="card-text">Primary Test</p>
                </div>
            </div>
        `);
    } else {
        $('#viewSelectedTests').html(`
            <div class="alert alert-info">
                <i class="fas fa-info-circle mr-2"></i>
                No primary test found for this entry.
            </div>
        `);
    }
    
    // Store entry ID for edit functionality
    $('#editFromViewBtn').data('entry-id', entry.id);
}

function loadEntryTestsForView(entryId) {
    $.get('patho_api/entry.php', { action: 'get_tests', entry_id: entryId })
        .done(function(response) {
            if (response.success && response.data.length > 0) {
                let testsHtml = '<div class="row">';
                response.data.forEach(test => {
                    testsHtml += `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">${test.test_name}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            Category: ${test.category_name || 'N/A'}<br>
                                            Result: ${test.result_value || 'Pending'}<br>
                                            Unit: ${test.unit || 'N/A'}<br>
                                            Price: ₹${parseFloat(test.price || 0).toFixed(2)}
                                        </small>
                                    </p>
                                    ${test.remarks ? `<p class="card-text"><small><strong>Remarks:</strong> ${test.remarks}</small></p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
                testsHtml += '</div>';
                $('#viewSelectedTests').html(testsHtml);
            }
        })
        .fail(function(xhr) {
            console.log('Failed to load tests for view:', xhr);
        });
}

function saveEntryData() {
    // Validate form first
    if (!validateModalForm('entryForm')) {
        return false;
    }
    
    const data = $('#entryForm').serialize() + '&action=save&ajax=1';
    const isEdit = $('#entryId').val();
    
    const submitBtn = $('#entryForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.post('ajax/entry_api.php', data)
        .done(function(response) {
            if (response.success) {
                toastr.success(isEdit ? 'Entry updated successfully!' : 'Entry added successfully!');
                $('#entryModal').modal('hide');
                loadEntries();
                loadStats();
            } else {
                toastr.error('Error: ' + (response.message || 'Save failed'));
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            toastr.error('Failed to save entry: ' + errorMsg);
        })
        .always(function() {
            submitBtn.html(originalText).prop('disabled', false);
        });
}

function deleteEntry(id) {
    if (!confirm('Are you sure you want to delete this entry?')) {
        return;
    }

    $.post('ajax/entry_api.php', { action: 'delete', id: id, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                toastr.success('Entry deleted successfully!');
                loadEntries();
                loadStats();
            } else {
                toastr.error('Error deleting entry: ' + (response.message || 'Delete failed'));
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            toastr.error('Failed to delete entry: ' + errorMsg);
        });
}

function clearFilters() {
    $('#statusFilter').val('');
    $('#doctorFilter').val('');
    $('#testFilter').val('');
    $('#dateFromFilter').val('');
    $('#dateToFilter').val('');
    $('#entriesSearch').val('');
    entriesCurrentPage = 1;
    applyEntriesFilters();
}

function loadStats() {
    $.get('ajax/entry_api.php', { action: 'stats', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                const stats = response.data;
                $('#totalEntries').text(stats.total || 0);
                $('#pendingEntries').text(stats.pending || 0);
                $('#completedEntries').text(stats.completed || 0);
                $('#todayEntries').text(stats.today || 0);
            }
        })
        .fail(function() {
            console.error('Failed to load entry statistics');
        });
}

function exportEntries() {
    // Simple CSV export of current filtered data
    const searchTerm = $('#entriesSearch').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();
    const doctorFilter = $('#doctorFilter').val();
    const testFilter = $('#testFilter').val();
    const dateFrom = $('#dateFromFilter').val();
    const dateTo = $('#dateToFilter').val();
    
    let filteredEntries = allEntries.filter(entry => {
        // Apply same filters as in applyEntriesFilters
        if (searchTerm && 
            !((entry.patient_name || '').toLowerCase().includes(searchTerm)) &&
            !((entry.doctor_name || '').toLowerCase().includes(searchTerm)) &&
            !((entry.test_name || '').toLowerCase().includes(searchTerm)) &&
            !(entry.id.toString().includes(searchTerm))) {
            return false;
        }
        
        if (statusFilter && entry.status !== statusFilter) {
            return false;
        }
        
        if (doctorFilter && entry.doctor_id != doctorFilter) {
            return false;
        }
        
        if (testFilter && entry.test_id != testFilter) {
            return false;
        }
        
        if (dateFrom || dateTo) {
            const entryDate = new Date(entry.entry_date || entry.created_at);
            if (dateFrom && entryDate < new Date(dateFrom)) {
                return false;
            }
            if (dateTo && entryDate > new Date(dateTo + 'T23:59:59')) {
                return false;
            }
        }
        
        return true;
    });
    
    if (filteredEntries.length === 0) {
        toastr.warning('No entries to export');
        return;
    }
    
    // Create CSV content
    const headers = ['ID', 'Patient Name', 'Doctor Name', 'Test Name', 'Status', 'Entry Date', 'Added By'];
    let csvContent = headers.join(',') + '\n';
    
    filteredEntries.forEach(entry => {
        const row = [
            entry.id,
            `"${(entry.patient_name || 'N/A').replace(/"/g, '""')}"`,
            `"${(entry.doctor_name || 'N/A').replace(/"/g, '""')}"`,
            `"${(entry.test_name || 'N/A').replace(/"/g, '""')}"`,
            entry.status,
            entry.entry_date || entry.created_at || 'N/A',
            entry.added_by_username || 'N/A'
        ];
        csvContent += row.join(',') + '\n';
    });
    
    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', `test_entries_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function validateModalForm(formId) {
    let isValid = true;
    const form = $('#' + formId);
    
    // Reset validation states
    form.find('.is-invalid').removeClass('is-invalid');
    
    // Check required fields
    form.find('[required]').each(function() {
        if (!$(this).val()) {
            $(this).addClass('is-invalid');
            isValid = false;
        }
    });
    
    if (!isValid) {
        toastr.error('Please fill in all required fields');
    }
    
    return isValid;
}

function resetModalForm() {
    $('#entryForm')[0].reset();
    $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', false);
    $('#entryForm .is-invalid').removeClass('is-invalid');
    $('#modalTitle').text('Add New Test Entry');
    $('#saveEntryBtn').show();
    $('.select2').trigger('change');
    $('#entryAddedBy').data('prefill', currentUserId);
    $('#entryAddedBy').data('forceReload', true);
    populateAddedBySelect(currentUserId);
    loadPatientsForUser(currentUserId);
    loadDoctorsForUser(currentUserId);
    updateDoctorAddedByDisplay();
}

function getErrorMessage(xhr) {
    try {
        const response = JSON.parse(xhr.responseText);
        return response.message || response.error || xhr.statusText;
    } catch (e) {
        return xhr.statusText || 'Unknown error';
    }
}

// Event handlers using delegation
$(document).on('click', '.edit-entry', function() {
    const id = $(this).data('id');
    editEntry(id);
});

$(document).on('click', '#editFromViewBtn', function() {
    const entryId = $(this).data('entry-id');
    if (entryId) {
        $('#viewEntryModal').modal('hide');
        editEntry(entryId);
    }
});

// Multiple Tests Management
let selectedTests = [];

function updateSelectedTestsCount() {
    const count = selectedTests.length;
    const label = count === 0 ? '0 tests selected' : `${count} test${count === 1 ? '' : 's'} selected`;
    $('#selectedTestsCount').text(label);
}

// Add Test Functions
function showAddTestInterface() {
    $('#addTestInterface').show();
    loadCategories();
    // Reset the interface
    $('#testCategorySelect').val('');
    $('#testSelect').val('').html('<option value="">Choose category first...</option>');
}

function hideAddTestInterface() {
    $('#addTestInterface').hide();
    // Clear the interface
    $('#testCategorySelect').val('');
    $('#testSelect').val('').html('<option value="">Choose category first...</option>');
}

function loadCategories() {
    $.get('ajax/test_category_api.php', { action: 'list', ajax: 1 })
        .done(function(response) {
            if (response.success) {
                let options = '<option value="">Choose a category...</option>';
                response.data.forEach(category => {
                    options += `<option value="${category.id}">${category.name || 'Unknown'}</option>`;
                });
                $('#testCategorySelect').html(options);
            }
        })
        .fail(function(xhr) {
            const errorMsg = getErrorMessage(xhr);
            showAlert('Failed to load categories: ' + errorMsg, 'error');
        });
}

function loadTestsForCategory() {
    const categoryId = $('#testCategorySelect').val();
    
    if (!categoryId) {
        $('#testSelect').val('').html('<option value="">Choose category first...</option>');
        return;
    }
    
    $('#testSelect').html('<option value="">Loading tests...</option>');
    
    $.get('ajax/test_api.php', { action: 'list', category_id: categoryId, ajax: 1 })
        .done(function(response) {
            if (response.success) {
                populateTestSelect(response.data);
                } else {
                $('#testSelect').html('<option value="">Error loading tests</option>');
                }
            })
            .fail(function(xhr) {
            $('#testSelect').html('<option value="">Error loading tests</option>');
        });
}

function populateTestSelect(tests) {
    if (tests.length === 0) {
        $('#testSelect').html('<option value="">No tests in this category</option>');
        return;
    }
    
    let options = '<option value="">Select a test...</option>';
    tests.forEach(test => {
        const isSelected = selectedTests.some(t => t.test_id == test.id);
        const disabledAttr = isSelected ? ' disabled' : '';
        const selectedText = isSelected ? ' (Already Added)' : '';
        
        options += `<option value="${test.id}" data-test='${JSON.stringify(test)}'${disabledAttr}>${test.name} - ₹${parseFloat(test.price || 0).toFixed(2)}${selectedText}</option>`;
    });
    
    $('#testSelect').html(options);
}


function addSelectedTest() {
    const testId = $('#testSelect').val();
    const selectedOption = $('#testSelect option:selected');
    
    if (!testId) {
        showAlert('Please select a test', 'error');
        return;
    }
    
    const testData = JSON.parse(selectedOption.data('test'));
    
    // Check if test is already selected
    const isAlreadyAdded = selectedTests.some(t => t.test_id == testId);
    if (isAlreadyAdded) {
        showAlert('This test is already added to the entry', 'warning');
        return;
    }
    
    // Add the test
    selectedTests.push({
        category_id: testData.category_id,
        category_name: testData.category_name,
        test_id: testData.id,
        test_name: testData.name,
        result_value: '',
        unit: testData.unit,
        price: testData.price,
        discount_amount: 0,
        remarks: '',
        min: testData.min || null,
        max: testData.max || null
    });
    
    updateSelectedTestsDisplay();
    showAlert('Test added successfully', 'success');
    
    // Refresh the test dropdown to show updated status
    loadTestsForCategory();
    
    // Clear the selection
    $('#testSelect').val('');
}






function calculateEntryTotals() {
    let totalAmount = 0;
    let totalDiscount = 0;
    
    // Calculate from selected tests
    selectedTests.forEach(test => {
        totalAmount += parseFloat(test.price) || 0;
        totalDiscount += parseFloat(test.discount_amount) || 0;
    });
    
    // Set the calculated values
    $('#entryAmount').val(totalAmount.toFixed(2));
    
    // Calculate discount percentage
    const discountPercentage = totalAmount > 0 ? (totalDiscount / totalAmount) * 100 : 0;
    $('#entryDiscount').val(discountPercentage.toFixed(2));
    
    console.log('Calculated totals:', { totalAmount, totalDiscount, discountPercentage });
}

function updateSelectedTestsDisplay() {
    const container = $('#testsByCategoryContainer');
    console.log('updateSelectedTestsDisplay called, selectedTests:', selectedTests.length);
    updateSelectedTestsCount();
    
    if (selectedTests.length === 0) {
        container.html('<div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>No tests selected. Click "Add Test" to choose tests for this entry.</div>');
        calculateEntryTotals(); // Update totals even when no tests
        return;
    }
    
    let html = '';
    selectedTests.forEach((test, index) => {
        // Format test range if available
        const testRange = test.min && test.max ? `${test.min}-${test.max}` : 'N/A';
        const testValue = test.result_value || 'Pending';
        const testUnit = test.unit || 'N/A';
        
        // Single line format: Value | Range | Unit
        const valueRangeUnit = `${testValue} | ${testRange} | ${testUnit}`;
        
        html += `
            <div class="card mb-2">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <strong>${test.test_name}</strong>
                            <br><small class="text-info">${test.category_name}</small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-primary">
                                <strong>Value:</strong> ${testValue} | 
                                <strong>Range:</strong> ${testRange} | 
                                <strong>Unit:</strong> ${testUnit}
                            </small>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Price: ₹${parseFloat(test.price || 0).toFixed(2)}</small>
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="button" class="btn btn-sm btn-outline-primary mr-1" onclick="editTestDetails(${test.test_id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeTestFromEntry(${test.test_id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
    
    // Update calculated totals
    calculateEntryTotals();
}

function clearSelectedTests() {
    selectedTests = [];
    updateSelectedTestsDisplay();
}

function editTestDetails(testId) {
    const test = selectedTests.find(t => t.test_id == testId);
    if (!test) return;
    
    const newResultValue = prompt(`Enter result value for ${test.test_name}:`, test.result_value || '');
    if (newResultValue !== null) {
        test.result_value = newResultValue;
        updateSelectedTestsDisplay();
        showAlert('Test details updated', 'success');
    }
}

function removeTestFromEntry(testId) {
    if (confirm('Are you sure you want to remove this test from the entry?')) {
        selectedTests = selectedTests.filter(test => test.test_id != testId);
        updateSelectedTestsDisplay();
        showAlert('Test removed successfully', 'success');
    }
}


// Override the saveEntryData function to handle multiple tests
function saveEntryData() {
    if (!validateModalForm('entryForm')) {
        return;
    }
    
    // Check if at least one test is selected
    if (selectedTests.length === 0) {
        showAlert('Please add at least one test to this entry', 'error');
        return;
    }
    
    const formData = new FormData($('#entryForm')[0]);
    
    // Add selected tests to form data
    formData.append('tests', JSON.stringify(selectedTests));
    formData.append('action', 'save');
    formData.append('ajax', 1);
    
    const entryId = $('#entryId').val();
    const isEdit = entryId && entryId !== '';
    const submitBtn = $('#saveEntryBtn');
    const originalText = submitBtn.html();
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    
    $.ajax({
        url: 'ajax/entry_api.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json'
    })
    .done(function(response) {
        if (response.success) {
            showAlert(response.message || 'Entry saved successfully', 'success');
            $('#entryModal').modal('hide');
            loadEntries();
            loadStats();
        } else {
            showAlert('Error saving entry: ' + (response.message || 'Unknown error'), 'error');
        }
    })
    .fail(function(xhr) {
        const errorMsg = getErrorMessage(xhr);
        showAlert('Failed to save entry: ' + errorMsg, 'error');
    })
    .always(function() {
        submitBtn.prop('disabled', false).html(originalText);
    });
}

// Override the resetModalForm function to clear selected tests
function resetModalForm() {
    $('#entryForm')[0].reset();
    $('#entryId').val('');
    $('#entryPatient').val('').trigger('change');
    $('#entryDoctor').val('').trigger('change');
    $('#entryStatus').val('pending');
    $('#entryAmount').val('');
    $('#entryDiscount').val('');
    $('#entryNotes').val('');
    $('#entryAddedBy').data('prefill', currentUserId);
    $('#entryAddedBy').data('forceReload', true);
    populateAddedBySelect(currentUserId);

    // Clear selected tests
    clearSelectedTests();
    
    // Clear add test interface
    hideAddTestInterface();
    
    // Reset modal title and buttons
    $('#modalTitle').text('Add New Test Entry');
    $('#entryForm input, #entryForm textarea, #entryForm select').prop('disabled', false);
}
</script>

<?php require_once 'inc/footer.php'; ?>