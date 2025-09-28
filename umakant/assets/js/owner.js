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

  function loadOwners() {
    const $tbody = $('#ownersTable tbody');

    $.getJSON(API_URL, { action: 'list' })
      .done(function (resp) {
        if (ownersTableInstance) {
          ownersTableInstance.clear().destroy();
          ownersTableInstance = null;
        }

        if (resp && resp.success && Array.isArray(resp.data) && resp.data.length) {
          const rowsHtml = resp.data
            .map(function (owner, index) {
              return buildTableRow(owner, index);
            })
            .join('');
          $tbody.html(rowsHtml);
        } else {
          const message = resp && resp.message ? safeText(resp.message) : 'No owners found';
          $tbody.html('<tr><td colspan="10" class="text-center text-muted">' + message + '</td></tr>');
        }

        try {
          ownersTableInstance = initDataTable('#ownersTable');
        } catch (err) {
          console.error('Failed to initialize owners table DataTable', err);
        }
      })
      .fail(function (xhr) {
        console.error('Failed to load owners', xhr);
        $tbody.html('<tr><td colspan="10" class="text-center text-danger">Failed to load owner records.</td></tr>');
        if (window.toastr) toastr.error('Failed to load owner records');
      });
  }

  function buildTableRow(owner, index) {
    const linkHref = sanitizeUrl(owner.link);
    const linkHtml = linkHref ? '<a href="' + linkHref + '" target="_blank" rel="noopener">Open</a>' : '';
    const addedBy = owner.added_by_username || owner.added_by || '';

    return [
      '<tr>',
      '<td>', index + 1, '</td>',
      '<td>', safeText(owner.id || ''), '</td>',
      '<td>', safeText(owner.name || ''), '</td>',
      '<td>', safeText(owner.phone || ''), '</td>',
      '<td>', safeText(owner.whatsapp || ''), '</td>',
      '<td>', safeText(owner.email || ''), '</td>',
      '<td>', safeText(owner.address || ''), '</td>',
      '<td>', linkHtml, '</td>',
      '<td>', safeText(addedBy), '</td>',
      '<td class="text-nowrap">',
      '  <div class="btn-group btn-group-sm" role="group">',
      '    <button type="button" class="btn btn-info owner-view" data-id="', safeText(owner.id || ''), '"><i class="fas fa-eye"></i></button>',
      '    <button type="button" class="btn btn-warning owner-edit" data-id="', safeText(owner.id || ''), '"><i class="fas fa-edit"></i></button>',
      '    <button type="button" class="btn btn-danger owner-delete" data-id="', safeText(owner.id || ''), '"><i class="fas fa-trash"></i></button>',
      '  </div>',
      '</td>',
      '</tr>'
    ].join('');
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
