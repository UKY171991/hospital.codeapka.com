<?php
// inc/footer.php - common scripts and toast helper
?>
    <!-- Bootstrap and core plugins -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- DataTables and Extensions -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Notification and Alert Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <!-- Global utilities -->
    <script src="assets/js/global-utils.js"></script>
    <!-- Modal enhancements -->
    <script src="assets/js/modal-enhancements.js?v=<?php echo time(); ?>"></script>
    <!-- Cache clear utilities -->
    <script src="assets/js/cache-clear-utils.js?v=<?php echo time(); ?>"></script>
    <!-- New Comprehensive Table Manager - Handles all table initialization -->
    <script src="assets/js/table-manager.js?v=<?php echo time(); ?>"></script>
    <!-- Local scripts -->
    <script src="assets/js/common.js"></script>
    <script src="assets/js/app.js"></script>
    <!-- Page-specific scripts -->
    <?php
    // Load patient-enhanced script on patient.php (it provides enhanced table/export handlers).
    $currentPage = basename($_SERVER['SCRIPT_NAME']);
    if ($currentPage === 'patient.php') {
        echo '<script src="assets/js/patient.js?v=' . time() . '"></script>';
    } else {
        echo '<script src="assets/js/patient-new.js?v=' . time() . '"></script>';
    }
    if ($currentPage === 'owner.php') {
        echo '<script src="assets/js/owner.js?v=' . time() . '"></script>';
    }
    if ($currentPage === 'upload_list.php') {
        echo '<script src="assets/js/uploads.js?v=' . time() . '"></script>';
    }
    // Load plan page script when viewing plan.php
    if ($currentPage === 'plan.php') {
        echo '<script src="assets/js/plan.js?v=' . time() . '"></script>';
    }
    ?>
        <!-- Global View Modal (reusable across pages) -->
        <div class="modal fade" id="globalViewModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="globalViewModalLabel">Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="globalViewModalBody">
                        <!-- content filled by JS -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>
