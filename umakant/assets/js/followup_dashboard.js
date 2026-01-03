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
                                <a href="followup_client.php?search=${encodeURIComponent(client.phone)}" class="btn btn-sm btn-outline-primary" title="Details">
                                    <i class="fas fa-eye"></i>
                                </a>
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
                             <a href="followup_client.php?search=${encodeURIComponent(resp.client_phone)}" class="text-info"><i class="fas fa-search mr-1"></i>View Profile</a>
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
                        <td class="font-weight-bold">${client.name}</td>
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
