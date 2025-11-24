// OPD Dashboard JavaScript
$(document).ready(function() {
    
    // Load all dashboard data
    function loadDashboardData() {
        loadStats();
        loadRecentReports();
        loadRecentBills();
        loadUpcomingFollowUps();
    }

    // Load statistics
    function loadStats() {
        $.ajax({
            url: 'opd_api/dashboard.php',
            type: 'GET',
            data: { action: 'stats' },
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    // Doctors
                    $('#totalDoctors').text(data.doctors.total);
                    $('#activeDoctors').text(data.doctors.active + ' Active');
                    
                    // Patients
                    $('#totalPatients').text(data.patients.total);
                    $('#todayPatients').text(data.patients.today + ' Today');
                    
                    // Reports
                    $('#totalReports').text(data.reports.total);
                    $('#weekReports').text(data.reports.week + ' This Week');
                    
                    // Billing
                    $('#totalBills').text(data.billing.total);
                    $('#totalRevenue').text('₹' + data.billing.revenue);
                    $('#todayRevenue').text('₹' + data.billing.todayRevenue + ' Today');
                    $('#pendingAmount').text('₹' + data.billing.pending);
                    
                    // Follow-ups
                    $('#upcomingFollowUps').text(data.followUps.upcoming);
                    $('#overdueFollowUps').text(data.followUps.overdue);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading stats:', error);
                toastr.error('Error loading dashboard statistics');
            }
        });
    }

    // Load recent reports
    function loadRecentReports() {
        $.ajax({
            url: 'opd_api/dashboard.php',
            type: 'GET',
            data: { action: 'recent_reports' },
            success: function(response) {
                if (response.success && response.data) {
                    const reports = response.data;
                    let html = '';
                    
                    if (reports.length === 0) {
                        html = '<tr><td colspan="4" class="text-center text-muted">No reports found</td></tr>';
                    } else {
                        reports.forEach(function(report) {
                            const diagnosis = report.diagnosis ? 
                                (report.diagnosis.length > 30 ? report.diagnosis.substring(0, 30) + '...' : report.diagnosis) : 
                                'N/A';
                            
                            html += `
                                <tr>
                                    <td>#${report.id}</td>
                                    <td>${report.patient_name || 'N/A'}</td>
                                    <td>${report.doctor_name || 'N/A'}</td>
                                    <td>${report.report_date ? new Date(report.report_date).toLocaleDateString() : 'N/A'}</td>
                                </tr>
                            `;
                        });
                    }
                    
                    $('#recentReportsTable').html(html);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading recent reports:', error);
                $('#recentReportsTable').html('<tr><td colspan="4" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    // Load recent bills
    function loadRecentBills() {
        $.ajax({
            url: 'opd_api/dashboard.php',
            type: 'GET',
            data: { action: 'recent_bills' },
            success: function(response) {
                if (response.success && response.data) {
                    const bills = response.data;
                    let html = '';
                    
                    if (bills.length === 0) {
                        html = '<tr><td colspan="4" class="text-center text-muted">No bills found</td></tr>';
                    } else {
                        bills.forEach(function(bill) {
                            let statusBadge = '';
                            if (bill.payment_status === 'Paid') {
                                statusBadge = '<span class="badge badge-success">Paid</span>';
                            } else if (bill.payment_status === 'Unpaid') {
                                statusBadge = '<span class="badge badge-danger">Unpaid</span>';
                            } else if (bill.payment_status === 'Partial') {
                                statusBadge = '<span class="badge badge-warning">Partial</span>';
                            }
                            
                            html += `
                                <tr>
                                    <td>#${bill.id}</td>
                                    <td>${bill.patient_name || 'N/A'}</td>
                                    <td>₹${parseFloat(bill.total_amount || 0).toFixed(2)}</td>
                                    <td>${statusBadge}</td>
                                </tr>
                            `;
                        });
                    }
                    
                    $('#recentBillsTable').html(html);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading recent bills:', error);
                $('#recentBillsTable').html('<tr><td colspan="4" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    // Load upcoming follow-ups
    function loadUpcomingFollowUps() {
        $.ajax({
            url: 'opd_api/dashboard.php',
            type: 'GET',
            data: { action: 'upcoming_followups' },
            success: function(response) {
                if (response.success && response.data) {
                    const followups = response.data;
                    let html = '';
                    
                    if (followups.length === 0) {
                        html = '<tr><td colspan="5" class="text-center text-muted">No upcoming follow-ups</td></tr>';
                    } else {
                        followups.forEach(function(followup) {
                            const followUpDate = new Date(followup.follow_up_date);
                            const today = new Date();
                            today.setHours(0, 0, 0, 0);
                            
                            let statusBadge = '';
                            if (followUpDate.toDateString() === today.toDateString()) {
                                statusBadge = '<span class="badge badge-warning">Today</span>';
                            } else if (followUpDate < today) {
                                statusBadge = '<span class="badge badge-danger">Overdue</span>';
                            } else {
                                const daysUntil = Math.ceil((followUpDate - today) / (1000 * 60 * 60 * 24));
                                statusBadge = `<span class="badge badge-success">In ${daysUntil} day${daysUntil > 1 ? 's' : ''}</span>`;
                            }
                            
                            html += `
                                <tr>
                                    <td>#${followup.id}</td>
                                    <td>${followup.patient_name || 'N/A'}</td>
                                    <td>${followup.doctor_name || 'N/A'}</td>
                                    <td>${followUpDate.toLocaleDateString()}</td>
                                    <td>${statusBadge}</td>
                                </tr>
                            `;
                        });
                    }
                    
                    $('#upcomingFollowUpsTable').html(html);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading follow-ups:', error);
                $('#upcomingFollowUpsTable').html('<tr><td colspan="5" class="text-center text-danger">Error loading data</td></tr>');
            }
        });
    }

    // Auto-refresh dashboard every 5 minutes
    setInterval(function() {
        loadDashboardData();
    }, 300000); // 5 minutes

    // Initial load
    loadDashboardData();
});
