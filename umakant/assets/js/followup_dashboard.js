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
            const icons = ['fa-bullseye', 'fa-user-tag', 'fa-handshake', 'fa-clock', 'fa-check-double', 'fa-times-circle'];
            data.status_stats.forEach((item, index) => {
                const statusName = item.status || 'Uncategorized';
                const icon = icons[index % icons.length];
                const colors = ['primary', 'success', 'warning', 'info', 'danger', 'secondary'];
                const color = colors[index % colors.length];

                const col = `
                    <div class="col-md-3 col-6 mb-3">
                        <div class="category-box text-center shadow-sm border-0 border-top-4 border-${color}" style="background: #fff; padding: 25px 15px; border-radius: 15px; transition: all 0.3s ease;">
                            <div class="mb-2 text-${color}" style="font-size: 24px;">
                                <i class="fas ${icon}"></i>
                            </div>
                            <h4 class="mb-1 font-weight-bold" style="font-size: 1.8rem;">${item.count}</h4>
                            <p class="mb-0 text-muted small font-weight-bold text-uppercase" style="letter-spacing: 0.5px;">${statusName}</p>
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
                const clientMessage = client.followup_message || `Hi ${client.name}, just wanted to follow up with you.`;

                const row = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-3 text-muted"><i class="fas fa-user-circle fa-2x"></i></div>
                                <div>
                                    <div class="font-weight-bold text-dark">${client.name}</div>
                                    <div class="small text-muted"><i class="fas fa-phone-alt mr-1 text-success"></i>${client.phone}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-primary small font-weight-bold d-block mb-1"><i class="fas fa-tag mr-1"></i> ${client.followup_title || 'General'}</span>
                            <span class="msg-snippet text-muted" title="${client.followup_message || ''}">${client.followup_message || '<i>No message configured</i>'}</span>
                        </td>
                        <td>
                            <span class="badge ${badgeClass} mb-1 shadow-xs px-2 py-1">${isOverdue ? 'Overdue' : 'Today'}</span>
                            <div class="small text-muted font-weight-bold">${new Date(client.next_followup_date).toLocaleDateString()}</div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-xs rounded">
                                <button class="btn btn-sm btn-white view-client border-right" data-id="${client.id}" title="Log Followup">
                                    <i class="fas fa-eye text-primary"></i>
                                </button>
                                <button class="btn btn-sm btn-white whatsapp-click" data-phone="${client.phone}" data-name="${client.name}" data-message="${encodeURIComponent(clientMessage)}" title="WhatsApp">
                                    <i class="fab fa-whatsapp text-success"></i>
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
                            <div class="small text-muted"><i class="fas fa-envelope mr-1 text-primary"></i>${client.email || 'N/A'}</div>
                        </td>
                        <td>
                            <span class="msg-snippet">${client.followup_message || '<i class="text-muted">None</i>'}</span>
                        </td>
                        <td>
                            <div class="small font-weight-bold">${new Date(updatedTime).toLocaleDateString()}</div>
                            <div class="small text-muted">${timeAge(new Date(updatedTime))}</div>
                        </td>
                        <td>
                            <span class="status-pill bg-light border text-primary small font-weight-bold text-uppercase px-2 py-1" style="border-radius: 20px;">${client.followup_title || 'Active'}</span>
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
                    const nextDateStr = client.next_followup_date ? new Date(client.next_followup_date).toLocaleDateString() : 'Not Set';
                    const modal = `
                        <div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                                    <div class="modal-header bg-primary text-white py-3 border-0" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-user-edit mr-2"></i> Client Interaction Hub
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 1;">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <!-- Top Info Bar -->
                                        <div class="p-4 bg-light border-bottom">
                                            <div class="row align-items-center">
                                                <div class="col-md-5 mb-3 mb-md-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; font-size: 20px;">
                                                            ${client.name.charAt(0)}
                                                        </div>
                                                        <div>
                                                            <h5 class="mb-0 font-weight-bold">${client.name}</h5>
                                                            <small class="text-muted"><i class="fas fa-phone-alt text-success mr-1"></i> ${client.phone}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3 mb-md-0">
                                                    <div class="small text-muted font-weight-bold text-uppercase mb-1">Status & Next Due</div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-primary mr-2 px-2 py-1" style="border-radius: 20px;"><i class="fas fa-tag mr-1"></i> ${client.followup_title || 'General'}</span>
                                                        <span class="text-danger font-weight-bold" id="modal_next_date"><i class="fas fa-calendar-check mr-1"></i> ${nextDateStr}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="small text-muted font-weight-bold text-uppercase mb-1">Company</div>
                                                    <div class="font-weight-bold text-dark">${client.company || 'Individual'}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-4">
                                            <div class="row">
                                                <!-- Log Section -->
                                                <div class="col-md-7">
                                                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px; background: #fbfbfc;">
                                                        <div class="card-body p-4">
                                                            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-pen-alt mr-2 text-primary"></i> Log New Response</h6>
                                                            <div class="form-group mb-3">
                                                                <textarea class="form-control border-0 shadow-xs" id="detail_response_message" rows="4" 
                                                                    style="border-radius: 12px; font-size: 0.95rem; background: #fff; padding: 15px;" placeholder="What was the outcome of this followup?"></textarea>
                                                            </div>
                                                            <div class="form-group mb-4">
                                                                <label class="small text-muted font-weight-bold"><i class="fas fa-calendar-plus mr-1"></i> SCHEDULE NEXT FOLLOWUP</label>
                                                                <div class="input-group shadow-xs rounded" style="overflow: hidden;">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text bg-white border-0"><i class="fas fa-clock text-info"></i></span>
                                                                    </div>
                                                                    <input type="date" class="form-control border-0" id="detail_next_followup_date" value="${client.next_followup_date || ''}">
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-primary btn-block py-2 font-weight-bold shadow-sm" id="saveResponseBtn" data-id="${client.id}" style="border-radius: 10px;">
                                                                SAVE INTERACTION
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- History Section -->
                                                <div class="col-md-5 mt-4 mt-md-0">
                                                    <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-history mr-2 text-info"></i> Activity Log</h6>
                                                    <div id="modalResponseHistory" class="pr-2" style="max-height: 380px; overflow-y: auto;">
                                                       <p class="text-center text-muted py-5">Loading activity...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-0 py-3">
                                        <button type="button" class="btn btn-link text-muted font-weight-bold px-4" data-dismiss="modal">Close Window</button>
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
                        container.append(`
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-light mb-3"></i>
                                <p class="text-muted small">No interactions recorded yet.</p>
                            </div>
                        `);
                        return;
                    }
                    res.data.forEach(item => {
                        const date = new Date(item.created_at).toLocaleString('en-US', {
                            month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                        });
                        container.append(`
                            <div class="card border-0 shadow-xs mb-3" style="border-radius: 12px; background: #fff;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-primary font-weight-bold">${date}</small>
                                        <button class="btn btn-link btn-xs text-danger p-0 delete-response-btn" data-id="${item.id}" data-client-id="${clientId}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <div class="small text-dark" style="white-space: pre-wrap; line-height: 1.4;">${item.response_message}</div>
                                </div>
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
            toastr.error('Please enter a response or select a date');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin"></i> SAVING...');

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
                        const formatted = new Date(nextDate).toLocaleDateString();
                        $('#modal_next_date').html(`<i class="fas fa-calendar-check mr-1"></i> ${formatted}`);
                    }
                } else {
                    toastr.error(res.message);
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html('SAVE INTERACTION');
            }
        });
    });

    $(document).on('click', '.delete-response-btn', function () {
        if (!confirm('Are you sure? This action cannot be undone.')) return;
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
        const message = decodeURIComponent($(this).data('message') || '');

        if (phone) {
            const cleanPhone = phone.toString().replace(/\D/g, '');
            let text = message.replace(/{name}/g, name);
            if (!text.includes(name) && !message) {
                text = `Hi ${name}, just wanted to follow up with you.`;
            }
            window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`, '_blank');
        }
    });

});
