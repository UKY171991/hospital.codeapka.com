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
            const colors = ['primary', 'success', 'warning', 'info', 'danger', 'secondary', 'purple'];

            data.status_stats.forEach((item, index) => {
                const statusName = item.status || 'Uncategorized';
                const icon = icons[index % icons.length];
                const color = colors[index % colors.length];

                const col = `
                    <div class="col-lg-2 col-md-4 col-6 mb-4">
                        <div class="premium-stat-card shadow-sm h-100 text-center border-0 p-3" style="background: #fff; border-radius: 20px; border-bottom: 5px solid var(--${color}); transition: all 0.3s ease;">
                            <div class="icon-circle mb-3 mx-auto shadow-xs bg-light text-${color}" style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                <i class="fas ${icon}"></i>
                            </div>
                            <h3 class="font-weight-bold mb-0" style="color: #334155; font-size: 1.6rem;">${item.count}</h3>
                            <p class="text-muted small font-weight-bold text-uppercase mb-0 mt-1" style="font-size: 0.7rem; letter-spacing: 0.8px;">${statusName}</p>
                        </div>
                    </div>
                `;
                breakdownRow.append(col);
            });
        } else {
            breakdownRow.append('<div class="col-12 text-center text-muted py-5 text-uppercase font-weight-bold small">No activity tracking data available</div>');
        }

        // Urgent Table
        const urgentTable = $('#urgentFollowupsTable');
        urgentTable.empty();
        if (data.urgent_followups && data.urgent_followups.length > 0) {
            data.urgent_followups.forEach(client => {
                const isOverdue = new Date(client.next_followup_date) < new Date().setHours(0, 0, 0, 0);
                const badgeClass = isOverdue ? 'badge-danger' : 'badge-warning';
                const clientMessage = client.followup_message || '';

                const row = `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light-soft text-primary rounded-circle mr-3 border d-flex align-items-center justify-content-center shadow-xs" style="width: 45px; height: 45px; font-size: 18px; font-weight: 800;">
                                    ${client.name.charAt(0)}
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark mb-0 view-client cursor-pointer" style="font-size: 0.95rem;" data-id="${client.id}">${client.name}</div>
                                    <div class="small text-muted font-weight-bold"><i class="fas fa-phone-alt mr-1 text-success"></i>${client.phone}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-pill badge-light border text-primary font-weight-bold d-inline-flex align-items-center mb-1 px-2 py-1" style="font-size: 0.75rem;">
                                <i class="fas fa-tag mr-1 x-small"></i> ${client.followup_title || 'General'}
                            </span>
                            <div class="msg-snippet text-muted small" title="${client.followup_message || ''}">${client.followup_message || '<i class="text-light">No personalized message</i>'}</div>
                        </td>
                        <td>
                            <div class="badge ${badgeClass} mb-1 shadow-xs px-2 py-1">${isOverdue ? 'Overdue' : 'Due Today'}</div>
                            <div class="small text-muted font-weight-bold">${new Date(client.next_followup_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-xs">
                                <button class="btn btn-sm btn-white view-client" data-id="${client.id}" title="Deep Details">
                                    <i class="fas fa-eye text-primary"></i>
                                </button>
                                <button class="btn btn-sm btn-white whatsapp-click" data-phone="${client.phone}" data-name="${client.name}" data-message="${encodeURIComponent(clientMessage)}" title="Contact via WhatsApp">
                                    <i class="fab fa-whatsapp text-success"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                urgentTable.append(row);
            });
        } else {
            urgentTable.append('<tr><td colspan="4" class="text-center py-5 text-muted"><i class="fas fa-check-circle text-success mb-2 d-block fa-2x"></i> Great job! No urgent followups pending.</td></tr>');
        }

        // Recent Responses (Chat style)
        const chatDiv = $('#recentResponsesDiv');
        chatDiv.empty();
        if (data.recent_responses && data.recent_responses.length > 0) {
            data.recent_responses.forEach(resp => {
                const chat = `
                    <div class="direct-chat-msg mb-4">
                        <div class="direct-chat-infos clearfix mb-1">
                            <span class="direct-chat-name float-left font-weight-bold text-primary">${resp.client_name}</span>
                            <small class="direct-chat-timestamp float-right text-muted">${new Date(resp.created_at).toLocaleString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</small>
                        </div>
                        <div class="direct-chat-text shadow-xs p-3" style="border-radius: 12px; border-left: 4px solid #ced4da; background: #fff;">
                            ${resp.response_message}
                            <div class="mt-2 text-right">
                                <a href="#" class="btn btn-xs btn-link text-info view-client p-0" data-id="${resp.client_id}">
                                    <i class="fas fa-external-link-alt mr-1"></i>Explore Profiling
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                chatDiv.append(chat);
            });
        } else {
            chatDiv.append('<div class="text-center py-5 text-muted small"><i class="fas fa-comments-slash mb-2 d-block fa-lg"></i> No feedback yet</div>');
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
                            <a href="#" class="view-client d-flex align-items-center text-dark" data-id="${client.id}">
                                <div class="bg-primary text-white p-1 rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width:24px; height:24px; font-size:10px;">${client.name.charAt(0)}</div>
                                ${client.name}
                            </a>
                        </td>
                        <td>
                            <div class="small text-muted font-weight-bold"><i class="fas fa-phone-alt text-success mr-1"></i>${client.phone}</div>
                        </td>
                        <td>
                            <span class="msg-snippet text-muted">${client.followup_message || '<i class="text-light">No content</i>'}</span>
                        </td>
                        <td>
                            <div class="small font-weight-bold text-dark">${new Date(updatedTime).toLocaleDateString()}</div>
                            <div class="small text-muted">${timeAge(new Date(updatedTime))}</div>
                        </td>
                        <td>
                            <span class="badge badge-pill badge-light border text-info small font-weight-bold text-uppercase px-2" style="font-size: 0.65rem;">${client.followup_title || 'Active'}</span>
                        </td>
                    </tr>
                `;
                recentBody.append(row);
            });
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
                    const nextDateStr = client.next_followup_date ? new Date(client.next_followup_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : 'Not Scheduled';

                    const modal = `
                        <div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 25px;">
                                    <div class="modal-header d-block p-4 border-0" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle mr-3 bg-primary text-white shadow-lg d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: 800; border: 3px solid rgba(255,255,255,0.1);">
                                                    ${client.name.charAt(0)}
                                                </div>
                                                <div>
                                                    <h3 class="mb-0 text-white font-weight-bold">${client.name}</h3>
                                                    <div class="text-light opacity-75 small">
                                                        <i class="fas fa-map-marker-alt mr-1"></i> ${client.company || 'Corporate Client'} 
                                                        <span class="mx-2 text-primary font-weight-bold">|</span>
                                                        <i class="fas fa-phone-alt mr-1"></i> ${client.phone}
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="close text-white opacity-1" data-dismiss="modal" style="font-size: 30px;">&times;</button>
                                        </div>
                                        
                                        <div class="d-flex align-items-center mt-4">
                                            <div class="glass-pill mr-3">
                                                <i class="fas fa-tag mr-2 text-primary"></i>
                                                <span class="text-white font-weight-bold">${client.followup_title || 'General'}</span>
                                            </div>
                                            <div class="glass-pill">
                                                <i class="fas fa-calendar-check mr-2 text-warning"></i>
                                                <span class="text-white font-weight-bold" id="modal_next_date">Next Due: ${nextDateStr}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-body p-0" style="background: #f8fafc;">
                                        <div class="p-4">
                                            <div class="row">
                                                <!-- Action Form -->
                                                <div class="col-md-7">
                                                    <div class="card h-100 border-0 shadow-xs mb-0" style="border-radius: 20px;">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex align-items-center mb-4">
                                                                <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-feather-alt mr-2 text-primary"></i> New Activity Note</h5>
                                                            </div>
                                                            <div class="form-group mb-4">
                                                                <textarea class="form-control border-0 bg-light p-3" id="detail_response_message" rows="5" 
                                                                    style="border-radius: 15px; font-size: 1rem; resize: none;" placeholder="Record the outcome of your latest interaction..."></textarea>
                                                            </div>
                                                            <div class="form-group mb-4">
                                                                <label class="small text-muted font-weight-bold mb-2 ml-1">RE-SCHEDULE NEXT TOUCHPOINT</label>
                                                                <div class="input-group input-group-lg bg-light rounded" style="overflow: hidden;">
                                                                    <div class="input-group-prepend border-0">
                                                                        <span class="input-group-text border-0 bg-transparent text-primary px-3"><i class="fas fa-calendar-alt"></i></span>
                                                                    </div>
                                                                    <input type="date" class="form-control border-0 bg-transparent font-weight-bold text-primary pl-0" id="detail_next_followup_date" value="${client.next_followup_date || ''}">
                                                                </div>
                                                            </div>
                                                            <button class="btn btn-primary btn-lg btn-block shadow-sm py-3 font-weight-bold" id="saveResponseBtn" data-id="${client.id}" style="border-radius: 15px; letter-spacing: 0.5px;">
                                                                LOG INTERACTION
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- History Feed -->
                                                <div class="col-md-5 mt-4 mt-md-0">
                                                    <div class="d-flex align-items-center justify-content-between mb-3 px-3">
                                                        <h6 class="mb-0 font-weight-bold text-dark text-uppercase small letter-spacing-1">Interaction Feed</h6>
                                                        <i class="fas fa-history text-muted"></i>
                                                    </div>
                                                    <div id="modalResponseHistory" class="px-2 pb-4" style="max-height: 480px; overflow-y: auto;">
                                                       <div class="text-center py-5">
                                                           <i class="fas fa-circle-notch fa-spin text-primary fa-2x"></i>
                                                       </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="modal-footer bg-white border-0 p-3 text-center d-block">
                                        <small class="text-muted font-weight-bold">Created on ${new Date(client.created_at).toLocaleDateString()} by System Manager</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <style>
                            .glass-pill { background: rgba(255,255,255,0.1); backdrop-filter: blur(5px); border-radius: 12px; padding: 10px 18px; display: inline-flex; align-items: center; border: 1px solid rgba(255,255,255,0.05); }
                            .x-small { font-size: 0.65rem; }
                            .letter-spacing-1 { letter-spacing: 1px; }
                        </style>
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
                            <div class="text-center py-5 bg-white shadow-xs rounded" style="border-radius: 15px;">
                                <i class="fas fa-layer-group fa-3x text-light mb-3"></i>
                                <p class="text-muted small font-weight-bold">History is clean.</p>
                            </div>
                        `);
                        return;
                    }
                    res.data.forEach(item => {
                        const date = new Date(item.created_at).toLocaleString('en-US', {
                            month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                        });
                        container.append(`
                            <div class="card border-0 shadow-xs mb-3 interaction-card" style="border-radius: 15px; background: #fff;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="small font-weight-bold text-primary px-2 py-1 rounded bg-light">${date}</div>
                                        <button class="btn btn-link btn-xs text-danger p-0 delete-response-btn" data-id="${item.id}" data-client-id="${clientId}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    <div class="small text-dark interaction-text" style="white-space: pre-wrap; line-height: 1.5;">${item.response_message}</div>
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
            toastr.error('Provide a note or re-schedule date');
            return;
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> RECORDING...');

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
                    toastr.success('Interaction logged');
                    $('#detail_response_message').val('');
                    loadResponseHistory(clientId);
                    loadDashboardStats(); // Refresh dashboard background
                    if (nextDate) {
                        const formatted = new Date(nextDate).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                        $('#modal_next_date').html(`Next Due: ${formatted}`);
                    }
                } else {
                    toastr.error(res.message);
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html('LOG INTERACTION');
            }
        });
    });

    $(document).on('click', '.delete-response-btn', function () {
        if (!confirm('Permanent delete? This removes the activity history.')) return;
        const id = $(this).data('id');
        const clientId = $(this).data('client-id');
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'POST',
            data: { action: 'delete_response', id: id },
            success: function (res) {
                if (res.success) {
                    toastr.success('Note removed');
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
            // Replace {name} placeholder with actual name if exists
            let text = `Dear ${name},\n\n` + message.replace(/{name}/g, name);

            // If the template message is completely empty, use a soft default
            if (!message.trim()) {
                text = `Dear ${name},\n\nHi, I am following up regarding our previous conversation.`;
            }

            window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`, '_blank');
        }
    });

});
