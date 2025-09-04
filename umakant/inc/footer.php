<?php
// inc/footer.php - common scripts and toast helper
?>
    <!-- Core JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <!-- Global utilities -->
    <script src="assets/js/global-utils.js"></script>
    <!-- Universal Table Fix - Load before other table scripts -->
    <script src="assets/js/universal-table-fix.js"></script>
    <!-- Enhanced Table Manager -->
    <script src="assets/js/enhanced-table.js"></script>
    <!-- Local scripts -->
    <script src="assets/js/common.js"></script>
    <script src="assets/js/app.js"></script>
    <!-- Page scripts -->
    <?php
    // Cache-bust local scripts using file modification time to ensure clients load latest versions
    $assetBase = __DIR__ . '/../assets/js/';
    $scripts = ['plan.js', 'patient-enhanced.js', 'doctor-enhanced.js', 'user-enhanced.js', 'test-enhanced.js'];
    foreach ($scripts as $s) {
        $path = $assetBase . $s;
        $ver = file_exists($path) ? filemtime($path) : time();
        echo "    <script src=\"assets/js/{$s}?v={$ver}\"></script>\n";
    }
    ?>
    </div>
    </body>
    </html>
