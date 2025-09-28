(function ($) {
  'use strict';

  const API_URL = 'ajax/owner_api.php';
  let ownersTableInstance = null;

  function safeText(value) {
    if (value === null || value === undefined) return '';
    const str = String(value);
    if (typeof window.escapeHtml === 'function') {
      return window.escapeHtml(str);
    }
    return str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function sanitizeUrl(url) {
    if (!url) return '';
    try {
      const parsed = new URL(url, window.location.origin);
      if (!/^https?:$/i.test(parsed.protocol)) return '';
      return parsed.href;
    } catch (err) {
      console.warn('Invalid URL encountered for owner link:', url, err);
      return '';
    }
  }

  function initOwnerPage() {
    if (!$('#ownersTable').length) return;

    bindEvents();
    loadOwners();
  }

  function bindEvents() {
    $('#addOwnerBtn').on('click', function (event) {
      event.preventDefault();
      resetForm();
      $('#ownerModalLabel').text('Add Owner');
      $('#ownerModal').modal('show');
    });

    $('#ownerForm').on('submit', handleSaveOwner);

    $(document).on('click', '.owner-edit', handleEditClick);
    $(document).on('click', '.owner-delete', handleDeleteClick);
    $(document).on('click', '.owner-view', handleViewClick);

    $('#ownerModal').on('hidden.bs.modal', function () {
      resetForm();
    });
  }

  function ensureOwnerTable() {
    if (ownersTableInstance) return ownersTableInstance;

    if (!$.fn.DataTable) {
      console.error('DataTables library missing for owners table');
      return null;
    }

    ownersTableInstance = $('#ownersTable').DataTable({
      autoWidth: false,
      responsive: true,
      lengthMenu: [10, 25, 50, 100],
      language: {
        search: 'Search:',
        lengthMenu: 'Show _MENU_ entries',
        info: 'Showing _START_ to _END_ of _TOTAL_ owners',
        infoEmpty: 'No owners to show',
        zeroRecords: 'No matching owners found',
        emptyTable: 'No owners available'
      },
      columns: [
        {
          data: null,
          orderable: false,
          searchable: false,
          className: 'text-center',
          width: '60px',
          render: function (data, type, row, meta) {
            return meta.row + 1;
          }
        },
        { data: 'id', defaultContent: '' },
        {
          data: 'name',
          defaultContent: '',
          render: function (data) {
            return safeText(data || '');
          }
        },
        {
          data: 'phone',
          defaultContent: '',
          render: function (data) {
            return safeText(data || '');
          }
        },
        {
          data: 'whatsapp',
          defaultContent: '',
          render: function (data) {
            return safeText(data || '');
          }
        },
        {
          data: 'email',
          defaultContent: '',
          render: function (data) {
            return safeText(data || '');
          }
        },
        {
          data: 'address',
          defaultContent: '',
          render: function (data) {
            return safeText(data || '');
          }
        },
        {
          data: 'link',
          orderable: false,
          searchable: false,
          defaultContent: '',
          render: function (data) {
            const linkHref = sanitizeUrl(data);
            return linkHref ? '<a href="' + linkHref + '" target="_blank" rel="noopener">Open</a>' : '';
          }
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          render: function (data) {
            const added = data && (data.added_by_username || data.added_by);
            return safeText(added || '');
          }
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          className: 'text-nowrap text-center',
          render: function (data) {
            const id = safeText(data && data.id ? data.id : '');
            return (
              '<div class="btn-group btn-group-sm" role="group">' +
              '<button type="button" class="btn btn-info owner-view" data-id="' + id + '"><i class="fas fa-eye"></i></button>' +
              '<button type="button" class="btn btn-warning owner-edit" data-id="' + id + '"><i class="fas fa-edit"></i></button>' +
              '<button type="button" class="btn btn-danger owner-delete" data-id="' + id + '"><i class="fas fa-trash"></i></button>' +
              '</div>'
            );
          }
        }
      ]
    });

    return ownersTableInstance;
  }

  function loadOwners() {
    const table = ensureOwnerTable();
    if (!table) return;

    table.processing(true);

    $.getJSON(API_URL, { action: 'list' })
      .done(function (resp) {
        const rows = resp && resp.success && Array.isArray(resp.data) ? resp.data : [];
        table.clear();
        table.rows.add(rows);
        table.draw();

        if (!(resp && resp.success)) {
          const message = resp && resp.message ? resp.message : 'Failed to load owner records';
          if (window.toastr) toastr.warning(message);
        }
      })
      .fail(function (xhr) {
        console.error('Failed to load owners', xhr);
        table.clear().draw();
        if (window.toastr) toastr.error('Failed to load owner records');
      })
      .always(function () {
        table.processing(false);
      });
  }

  function handleSaveOwner(event) {
    event.preventDefault();
    const $form = $('#ownerForm');
    const $submitBtn = $('#saveOwnerBtn');
    const originalHtml = $submitBtn.html();

    const formData = $form.serializeArray();
    formData.push({ name: 'action', value: 'save' });

    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
      url: API_URL,
      method: 'POST',
      data: $.param(formData),
      dataType: 'json'
    })
      .done(function (resp) {
        if (resp && resp.success) {
          if (window.toastr) toastr.success(resp.message || 'Owner saved successfully');
          $('#ownerModal').modal('hide');
          loadOwners();
        } else {
          if (window.toastr) toastr.error((resp && resp.message) || 'Failed to save owner');
        }
      })
      .fail(function (xhr) {
        console.error('Failed to save owner', xhr);
        if (window.toastr) toastr.error('Failed to save owner');
      })
      .always(function () {
        $submitBtn.prop('disabled', false).html(originalHtml);
      });
  }

  function handleEditClick() {
    const id = $(this).data('id');
    if (!id) return;

    $.getJSON(API_URL, { action: 'get', id: id })
      .done(function (resp) {
        if (resp && resp.success && resp.data) {
          populateForm(resp.data);
          $('#ownerModalLabel').text('Edit Owner');
          $('#ownerModal').modal('show');
        } else {
          if (window.toastr) toastr.error((resp && resp.message) || 'Owner not found');
        }
      })
      .fail(function (xhr) {
        console.error('Failed to fetch owner details', xhr);
        if (window.toastr) toastr.error('Failed to load owner details');
      });
  }

  function handleDeleteClick() {
    const id = $(this).data('id');
    if (!id) return;

    const confirmDelete = function (cb) {
      if (window.Swal && Swal.fire) {
        Swal.fire({
          title: 'Are you sure?',
          text: 'This owner record will be permanently deleted.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!'
        }).then(function (result) {
          cb(result.isConfirmed);
        });
      } else {
        cb(window.confirm('Are you sure you want to delete this owner?'));
      }
    };

    confirmDelete(function (confirmed) {
      if (!confirmed) return;

      $.ajax({
        url: API_URL,
        method: 'POST',
        data: { action: 'delete', id: id },
        dataType: 'json'
      })
        .done(function (resp) {
          if (resp && resp.success) {
            if (window.toastr) toastr.success(resp.message || 'Owner deleted');
            loadOwners();
          } else {
            if (window.toastr) toastr.error((resp && resp.message) || 'Failed to delete owner');
          }
        })
        .fail(function (xhr) {
          console.error('Failed to delete owner', xhr);
          if (window.toastr) toastr.error('Failed to delete owner');
        });
    });
  }

  function handleViewClick() {
    const id = $(this).data('id');
    if (!id) return;

    $.getJSON(API_URL, { action: 'get', id: id })
      .done(function (resp) {
        if (resp && resp.success && resp.data) {
          renderOwnerDetails(resp.data);
          $('#ownerViewModal').modal('show');
        } else {
          if (window.toastr) toastr.error((resp && resp.message) || 'Owner not found');
        }
      })
      .fail(function (xhr) {
        console.error('Failed to fetch owner details', xhr);
        if (window.toastr) toastr.error('Failed to load owner details');
      });
  }

  function populateForm(owner) {
    $('#ownerId').val(owner.id || '');
    $('#ownerName').val(owner.name || '');
    $('#ownerEmail').val(owner.email || '');
    $('#ownerPhone').val(owner.phone || '');
    $('#ownerWhatsapp').val(owner.whatsapp || '');
    $('#ownerLink').val(owner.link || '');
    $('#ownerAddress').val(owner.address || '');
  }

  function resetForm() {
    const $form = $('#ownerForm');
    if ($form.length && $form[0]) {
      $form[0].reset();
    }
    $('#ownerId').val('');
    $('#ownerModalLabel').text('Add Owner');
    $('#saveOwnerBtn').prop('disabled', false).html('Save Owner');
  }

  function renderOwnerDetails(owner) {
    const linkHref = sanitizeUrl(owner.link);
    const linkHtml = linkHref ? '<a href="' + linkHref + '" target="_blank" rel="noopener">' + safeText(owner.link) + '</a>' : '—';
    const addedBy = owner.added_by_username || owner.added_by || '—';

    const html = [
      '<table class="table table-sm table-borderless">',
      '<tr><th>ID</th><td>', safeText(owner.id || ''), '</td></tr>',
      '<tr><th>Name</th><td>', safeText(owner.name || ''), '</td></tr>',
      '<tr><th>Phone</th><td>', safeText(owner.phone || ''), '</td></tr>',
      '<tr><th>WhatsApp</th><td>', safeText(owner.whatsapp || ''), '</td></tr>',
      '<tr><th>Email</th><td>', owner.email ? '<a href="mailto:' + safeText(owner.email) + '">' + safeText(owner.email) + '</a>' : '—', '</td></tr>',
      '<tr><th>Address</th><td>', safeText(owner.address || ''), '</td></tr>',
      '<tr><th>Link</th><td>', linkHtml, '</td></tr>',
      '<tr><th>Added By</th><td>', safeText(addedBy), '</td></tr>',
      '</table>'
    ].join('');

    $('#ownerViewModal .owner-view-content').html(html);
  }

  $(document).ready(initOwnerPage);
})(jQuery);
