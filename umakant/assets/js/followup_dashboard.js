$(document).ready(function () {
    loadDashboardStats();

    function loadDashboardStats() {
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'GET',
            data: { action: 'get_dashboard_stats' },
            success: function (response) {
                if (response.success) {
                    updateStats(response.data);
                } else {
                    toastr.error('Failed to load stats');
                }
            },
            error: function () {
                toastr.error('API Error');
            }
        });
    }

    function updateStats(data) {
        // High level counts
        $('#totalClients').text(data.total_clients || 0);
        $('#todayFollowups').text(data.today_followups || 0);
        $('#overdueFollowups').text(data.overdue_followups || 0);
        $('#totalTemplates').text(data.total_templates || 0);

        // Status Breakdown
        const breakdownRow = $('#statusBreakdownRow');
        breakdownRow.empty();
        if (data.status_stats && data.status_stats.length > 0) {
            data.status_stats.forEach(item => {
                const statusName = item.status || 'Uncategorized';
                const col = `
                    <div class="col-md-3 col-sm-6">
                        <div class="category-box text-center">
                            <h4 class="text-primary mb-1 font-weight-bold">${item.count}</h4>
                            <p class="mb-0 text-muted small font-weight-bold uppercase">${statusName}</p>
                        </div>
                    </div>
                `;
                breakdownRow.append(col);
            });
        } else {
            breakdownRow.append('<div class="col-12 text-center text-muted">No categorized data available</div>');
        }

        // Urgent Table
        const urgentTable = $('#urgentFollowupsTable');
        urgentTable.empty();
        if (data.urgent_followups && data.urgent_followups.length > 0) {
            data.urgent_followups.forEach(client => {
                const isOverdue = new Date(client.next_followup_date) < new Date().setHours(0, 0, 0, 0);
                const badgeClass = isOverdue ? 'badge-danger' : 'badge-warning';

                const row = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-3 text-muted"><i class="fas fa-user-circle fa-2x"></i></div>
                                <div>
                                    <div class="font-weight-bold text-dark">${client.name}</div>
                                    <div class="small text-muted"><i class="fas fa-phone-alt mr-1"></i>${client.phone}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-primary small font-weight-bold d-block">${client.followup_title || 'General Followup'}</span>
                            <span class="msg-snippet" title="${client.followup_message || ''}">${client.followup_message || '<i>No message configured</i>'}</span>
                        </td>
                        <td>
                            <span class="badge ${badgeClass} mb-1">${isOverdue ? 'Overdue' : 'Today'}</span>
                            <div class="small text-muted font-weight-bold">${new Date(client.next_followup_date).toLocaleDateString()}</div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary view-client" data-id="${client.id}" title="Log Followup">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-success whatsapp-click" data-phone="${client.phone}" data-name="${client.name}" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                urgentTable.append(row);
            });
        } else {
            urgentTable.append('<tr><td colspan="4" class="text-center py-5 text-muted">No urgent followups! All caught up.</td></tr>');
        }

        // Recent Responses (Chat style)
        const chatDiv = $('#recentResponsesDiv');
        chatDiv.empty();
        if (data.recent_responses && data.recent_responses.length > 0) {
            data.recent_responses.forEach(resp => {
                const chat = `
                    <div class="direct-chat-msg">
                        <div class="direct-chat-infos clearfix">
                            <span class="direct-chat-name float-left">${resp.client_name}</span>
                            <span class="direct-chat-timestamp float-right">${new Date(resp.created_at).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                        </div>
                        <div class="direct-chat-text shadow-xs">
                            ${resp.response_message}
                        </div>
                        <div class="small mt-1 text-muted">
                             <a href="#" class="text-info view-client" data-id="${resp.client_id}"><i class="fas fa-search mr-1"></i>View Profile</a>
                        </div>
                    </div>
                `;
                chatDiv.append(chat);
            });
        } else {
            chatDiv.append('<div class="text-center py-5 text-muted">No client responses logged yet.</div>');
        }

        // Recent Activity Table
        const recentBody = $('#recentActivityBody');
        recentBody.empty();
        if (data.recent_clients && data.recent_clients.length > 0) {
            data.recent_clients.forEach(client => {
                const updatedTime = client.updated_at || client.created_at;
                const row = `
                    <tr>
                        <td class="font-weight-bold">
                            <a href="#" class="view-client text-dark" data-id="${client.id}">${client.name}</a>
                        </td>
                        <td>
                            <div class="small text-muted font-weight-bold"><i class="fas fa-phone-alt text-success mr-1"></i>${client.phone}</div>
                            <div class="small text-muted"><i class="fas fa-envelope mr-1"></i>${client.email || 'N/A'}</div>
                        </td>
                        <td>
                            <span class="msg-snippet">${client.followup_message || '<i class="text-muted">None</i>'}</span>
                        </td>
                        <td>
                            <div class="small">${new Date(updatedTime).toLocaleDateString()}</div>
                            <div class="small text-muted">${timeAge(new Date(updatedTime))}</div>
                        </td>
                        <td>
                            <span class="status-pill bg-light border text-primary small font-weight-bold text-uppercase">${client.followup_title || 'Active'}</span>
                        </td>
                    </tr>
                `;
                recentBody.append(row);
            });
        } else {
            recentBody.append('<tr><td colspan="5" class="text-center">No activity found</td></tr>');
        }
    }

    // Modal & Response Logic
    $(document).on('click', '.view-client', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'GET',
            data: { action: 'get_client', id: id },
            success: function (response) {
                if (response.success) {
                    const client = response.data;
                    const modal = `
                        <div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                    <div class="modal-header bg-gradient-primary text-white py-3 border-0" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-user-circle mr-2"></i> Client Information & Feedback
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <div class="p-3 bg-light rounded shadow-xs h-100 border-left border-primary">
                                                    <small class="text-muted d-block text-uppercase font-weight-bold mb-1">Name & Contact</small>
                                                    <p class="mb-1"><strong>${client.name}</strong></p>
                                                    <p class="mb-0 text-secondary small"><i class="fas fa-phone-alt mr-1"></i> ${client.phone}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 bg-light rounded shadow-xs h-100 border-left border-warning">
                                                    <small class="text-muted d-block text-uppercase font-weight-bold mb-1">Company & Email</small>
                                                    <p class="mb-1">${client.company || 'N/A'}</p>
                                                    <p class="mb-0 text-secondary small"><i class="fas fa-envelope mr-1"></i> ${client.email || 'N/A'}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="p-3 bg-light rounded shadow-xs h-100 border-left border-success">
                                                    <small class="text-muted d-block text-uppercase font-weight-bold mb-1">Status & Next Date</small>
                                                    <p class="mb-1 text-success font-weight-bold"><i class="fas fa-tag mr-1"></i> ${client.followup_title || 'General'}</p>
                                                    <p class="mb-0 text-danger small font-weight-bold" id="modal_next_date"><i class="fas fa-calendar mr-1"></i> ${client.next_followup_date || 'Not Set'}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card bg-light border-0 mb-4 shadow-xs">
                                            <div class="card-body p-3">
                                                <h6 class="font-weight-bold text-primary"><i class="fas fa-plus-circle mr-2"></i> Log New Followup Response</h6>
                                                <input type="hidden" id="editing_response_id" value="">
                                                <textarea class="form-control border-0 mb-3" id="detail_response_message" rows="3" 
                                                    style="border-radius: 12px; font-size: 0.95rem;" placeholder="What did the client say?"></textarea>
                                                <div class="d-flex align-items-center">
                                                    <div class="input-group input-group-sm mr-auto" style="width: 200px;">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text bg-white border-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                                                        </div>
                                                        <input type="date" class="form-control border-0" id="detail_next_followup_date" value="${client.next_followup_date || ''}">
                                                    </div>
                                                    <button class="btn btn-primary px-4 btn-sm shadow-sm" id="saveResponseBtn" data-id="${client.id}" style="border-radius: 8px;">
                                                        <i class="fas fa-save mr-1"></i> Save Response
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="response-history">
                                            <h6 class="font-weight-bold mb-3"><i class="fas fa-history mr-2"></i> Response History</h6>
                                            <div id="modalResponseHistory" class="px-2" style="max-height: 200px; overflow-y: auto;">
                                               <p class="text-center text-muted py-3">Loading history...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#modalContainer').html(modal);
                    $('#viewClientModal').modal('show');
                    loadResponseHistory(client.id);
                }
            }
        });
    });

    function loadResponseHistory(clientId) {
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'GET',
            data: { action: 'get_responses', client_id: clientId },
            success: function (res) {
                if (res.success) {
                    const container = $('#modalResponseHistory');
                    container.empty();
                    if (res.data.length === 0) {
                        container.append('<p class="text-center text-muted py-3">No previous responses logged.</p>');
                        return;
                    }
                    res.data.forEach(item => {
                        const date = new Date(item.created_at).toLocaleString('en-US', {
                            month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                        });
                        container.append(`
                            <div class="border-bottom py-2 mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted font-weight-bold">${date}</small>
                                    <button class="btn btn-link btn-xs text-danger delete-response-btn" data-id="${item.id}" data-client-id="${clientId}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="small" style="white-space: pre-wrap;">${item.response_message}</div>
                            </div>
                        `);
                    });
                }
            }
        });
    }

    $(document).on('click', '#saveResponseBtn', function () {
        const clientId = $(this).data('id');
        const response = $('#detail_response_message').val();
        const nextDate = $('#detail_next_followup_date').val();
        const $btn = $(this);

        if (!response.trim() && !nextDate) {
            toastr.error('Message or Next Date is required');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'POST',
            data: {
                action: 'update_response',
                response_message: response,
                next_followup_date: nextDate,
                id: clientId
            },
            success: function (res) {
                if (res.success) {
                    toastr.success('Followup logged successfully');
                    $('#detail_response_message').val('');
                    loadResponseHistory(clientId);
                    loadDashboardStats(); // Refresh dashboard background
                    if (nextDate) {
                        $('#modal_next_date').html(`<i class="fas fa-calendar mr-1"></i> ${nextDate}`);
                    }
                } else {
                    toastr.error(res.message);
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save Response');
            }
        });
    });

    $(document).on('click', '.delete-response-btn', function () {
        if (!confirm('Delete this response record?')) return;
        const id = $(this).data('id');
        const clientId = $(this).data('client-id');
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'POST',
            data: { action: 'delete_response', id: id },
            success: function (res) {
                if (res.success) {
                    toastr.success('Record deleted');
                    loadResponseHistory(clientId);
                    loadDashboardStats();
                }
            }
        });
    });

    function timeAge(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " years ago";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " months ago";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " days ago";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " hours ago";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " mins ago";
        return Math.floor(seconds) + " seconds ago";
    }

    $(document).on('click', '.whatsapp-click', function (e) {
        e.preventDefault();
        const phone = $(this).data('phone');
        const name = $(this).data('name');
        if (phone) {
            const cleanPhone = phone.toString().replace(/\D/g, '');
            const text = `Hi ${name}, just wanted to follow up with you.`;
            window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`, '_blank');
        }
    });

});
