// entry-list.js - Handles test entry list functionality
(function($){
    'use strict';

    window.HMS = window.HMS || {};

    HMS.entryList = (function(){
        var table = null;
        var filters = {};

        function init(){
            // Ensure utils exists
            if(!window.HMS || !HMS.utils){
                console.error('HMS.utils is not available yet');
                return;
            }

            initSelect2();
            initDataTable();
            loadDropdowns();
            initFormValidation();
            setupEventListeners();
        }

        function initSelect2(){
            // Initialize Select2 only for selects that are NOT inside a modal
            $('.select2').filter(function(){
                return $(this).closest('.modal').length === 0;
            }).select2({ theme: 'bootstrap4', width: '100%' });
        }

        function initDataTable(){
            var $table = $('#entriesTable');
            if(!$table.length) return;

            // Avoid re-initializing if another script already created the DataTable
            if (window._entriesTableInitialized || (typeof $.fn.dataTable !== 'undefined' && $.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable('#entriesTable'))) {
                try { table = $table.DataTable(); window.entriesTable = table; } catch(e) { /* fallback */ }
                window._entriesTableInitialized = true;
                return;
            }

            table = $table.DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                dom: 'Bfrtip',
                buttons: ['copy','excel','pdf','print'],
                order: [[8,'desc']],
                ajax: {
                    url: 'ajax/entry_api.php',
                    type: 'POST',
                    data: function(d){
                        d.action = 'list';
                        return $.extend({}, d, filters);
                    }
                },
                columns: [
                    {data:'id'},{data:'patient_name'},{data:'doctor_name'},{data:'owner_name'},{data:'tests'},{data:'status'},{data:'priority'},{data:'amount'},{data:'entry_date'},
                    {data:null, orderable:false, searchable:false, render:function(data,type,row){
                        return '<div class="btn-group">'
                            + '<button class="btn btn-sm btn-info view-entry" data-id="'+row.id+'"><i class="fas fa-eye"></i></button>'
                            + '<button class="btn btn-sm btn-primary edit-entry" data-id="'+row.id+'"><i class="fas fa-edit"></i></button>'
                            + '<button class="btn btn-sm btn-danger delete-entry" data-id="'+row.id+'"><i class="fas fa-trash"></i></button>'
                            + '</div>';
                    }}
                ]
            });
            // Expose to global so inline scripts can access and reload
            try { window.entriesTable = table; } catch(e) {}
        }

        function loadDropdowns(){
            // patients
            $.get('ajax/patient_api.php',{action:'list'})
                .done(function(res){ if(res && res.success){ populateSelect('#patient', res.data, 'Select Patient'); } })
                .fail(function(){ HMS.utils.showError('Failed to load patients list'); });

            // doctors
            $.get('ajax/doctor_api.php',{action:'list'})
                .done(function(res){ if(res && res.success){ populateSelect('#doctor', res.data, 'Select Doctor'); } })
                .fail(function(){ HMS.utils.showError('Failed to load doctors list'); });
        }

        function populateSelect(selector, data, placeholder){
            var $s = $(selector);
            if(!$s.length) return;
            $s.empty().append('<option value="">'+placeholder+'</option>');
            (data||[]).forEach(function(it){
                $s.append('<option value="'+it.id+'">'+HMS.utils.escapeHtml(it.name||it.label||it.title||'')+'</option>');
            });
            $s.trigger('change');
        }

        function initFormValidation(){
            $('#entryForm').on('submit', function(e){
                e.preventDefault();
                if(this.checkValidity()){
                    submitEntryForm();
                }
                $(this).addClass('was-validated');
            });
        }

        function submitEntryForm(){
            var fd = new FormData($('#entryForm')[0]);
            $.ajax({
                url:'ajax/entry_api.php',
                type:'POST',
                data:fd,
                processData:false,
                contentType:false
            }).done(function(res){
                if(res && res.success){ HMS.utils.showSuccess('Entry saved successfully'); $('#addEntryModal').modal('hide'); if(table) table.ajax.reload(); }
                else { HMS.utils.showError(res && res.message ? res.message : 'Failed to save entry'); }
            }).fail(function(){ HMS.utils.showError('Failed to save entry'); });
        }

        function setupEventListeners(){
            // delegation for action buttons
            $(document).on('click','.view-entry', function(){ var id=$(this).data('id'); viewEntry(id); });
            $(document).on('click','.edit-entry', function(){ var id=$(this).data('id'); editEntry(id); });
            $(document).on('click','.delete-entry', function(){ var id=$(this).data('id'); deleteEntry(id); });

            // filters
            $('#testCategoryFilter').on('change', function(){ filters.category_id = $(this).val(); if(table) table.ajax.reload(); });
        }

        // CRUD helpers
        function viewEntry(id){
            // TODO: implement modal or redirect
            window.location.href = 'view_entry.php?id='+encodeURIComponent(id);
        }

        function editEntry(id){
            window.location.href = 'edit_entry.php?id='+encodeURIComponent(id);
        }

        function deleteEntry(id){
            if(!id) return;
            Swal.fire({title:'Are you sure?', text:"You won't be able to revert this!", icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete it!'}).then(function(r){
                if(r && r.isConfirmed){
                    $.post('ajax/entry_api.php',{action:'delete', id:id}).done(function(res){ if(res && res.success){ HMS.utils.showSuccess('Entry deleted'); if(table) table.ajax.reload(); } else { HMS.utils.showError(res && res.message ? res.message : 'Delete failed'); } }).fail(function(){ HMS.utils.showError('Delete failed'); });
                }
            });
        }

        return { init:init };
    })();

    // Expose init to global and initialize on DOM ready if utils already present
    $(function(){
        // Prevent double-initialization if this script or the inline page script already initialized the page
        if (window._entriesPageInitialized) return;

        if(window.HMS && HMS.utils){
            window._entriesPageInitialized = true;
            HMS.entryList.init();
        } else { // wait for global-utils to initialize
            $(document).on('HMS:ready', function(){
                if (window._entriesPageInitialized) return;
                window._entriesPageInitialized = true;
                HMS.entryList.init();
            });
        }
    });

})(jQuery);