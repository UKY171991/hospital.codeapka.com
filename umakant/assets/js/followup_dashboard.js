$(document).ready(function() {
    loadDashboardStats();

    function loadDashboardStats() {
        $.ajax({
            url: 'ajax/followup_client_api.php',
            type: 'GET',
            data: { action: 'get_dashboard_stats' },
            success: function(response) {
                if (response.success) {
                    updateStats(response.data);
                } else {
                    console.error('Failed to load stats:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('API Error:', error);
            }
        });
    }

    function updateStats(data) {
        // Counts
        $('#totalClients').text(data.total_clients || 0);
        $('#todayFollowups').text(data.today_followups || 0);
        $('#overdueFollowups').text(data.overdue_followups || 0);
        $('#upcomingFollowups').text(data.upcoming_followups || 0);
        $('#totalTemplates').text(data.total_templates || 0);

        // Upcoming/Urgent Table
        const urgentTable = $('#urgentFollowupsTable');
        urgentTable.empty();
        
        if (data.urgent_followups && data.urgent_followups.length > 0) {
            data.urgent_followups.forEach(client => {
                let badgeClass = 'badge-warning';
                let statusText = 'Today';
                
                const today = new Date().setHours(0,0,0,0);
                const nextDate = new Date(client.next_followup_date).setHours(0,0,0,0);
                
                if (nextDate < today) {
                    badgeClass = 'badge-danger';
                    statusText = 'Overdue';
                }

                const row = `
                    <tr>
                        <td>
                            <a href="followup_client.php?search=${encodeURIComponent(client.phone)}" class="text-dark font-weight-bold">
                                ${client.name}
                            </a>
                        </td>
                        <td>${client.phone}</td>
                        <td><span class="badge ${badgeClass}">${statusText}</span></td>
                        <td>
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-1"></i>
                                ${new Date(client.next_followup_date).toLocaleDateString()}
                            </small>
                        </td>
                        <td>
                            <a href="followup_client.php?search=${encodeURIComponent(client.phone)}" class="btn btn-xs btn-primary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                             <a href="#" class="btn btn-xs btn-success whatsapp-click" data-phone="${client.phone}" data-name="${client.name}" title="WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </td>
                    </tr>
                `;
                urgentTable.append(row);
            });
        } else {
            urgentTable.append('<tr><td colspan="5" class="text-center text-muted">No urgent followups pending. Good job!</td></tr>');
        }

        // Recent Activity List
        const recentList = $('#recentActivityList');
        recentList.empty();
        
        if (data.recent_clients && data.recent_clients.length > 0) {
            data.recent_clients.forEach(client => {
                const updatedTime = client.updated_at || client.created_at;
                const timeString = new Date(updatedTime).toLocaleString();
                
                const item = `
                    <li class="item">
                        <div class="product-info ml-0">
                            <a href="followup_client.php?search=${encodeURIComponent(client.phone)}" class="product-title">
                                ${client.name}
                                <span class="badge badge-info float-right"><i class="fas fa-clock"></i> ${timeAge(new Date(updatedTime))}</span>
                            </a>
                            <span class="product-description">
                                ${client.followup_title || 'No title'} - ${client.phone}
                            </span>
                        </div>
                    </li>
                `;
                recentList.append(item);
            });
        } else {
            recentList.append('<li class="item text-center text-muted">No recent activity found.</li>');
        }
    }

    // Helper for time ago
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

    // WhatsApp Click Handler (Quick Action)
    $(document).on('click',('.whatsapp-click'), function(e) {
        e.preventDefault();
        const phone = $(this).data('phone');
        const name = $(this).data('name');
        if(phone) {
             const cleanPhone = phone.toString().replace(/\D/g, '');
             const text = `Hi ${name}, checking in regarding...`;
             window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(text)}`, '_blank');
        }
    });

});
