<?php
require_once 'inc/header.php';
require_once 'inc/sidebar.php';
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-chart-line mr-2"></i>Followup Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Followup Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Quick Stats Row -->
            <div class="row">
                <!-- Total Clients -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-sm">
                        <div class="inner">
                            <h3 id="totalClients">0</h3>
                            <p>Total Clients</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="followup_client.php" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Today's Followups -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning shadow-sm">
                        <div class="inner">
                            <h3 id="todayFollowups">0</h3>
                            <p>Today's Followups</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <a href="followup_client.php?filter=today" class="small-box-footer" style="color: #1f2d3d !important;">
                            View List <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Overdue Followups -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger shadow-sm">
                        <div class="inner">
                            <h3 id="overdueFollowups">0</h3>
                            <p>Overdue Followups</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <a href="followup_client.php?filter=overdue" class="small-box-footer">
                            View List <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Templates -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow-sm">
                        <div class="inner">
                            <h3 id="totalTemplates">0</h3>
                            <p>Active Templates</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <a href="followup_templates.php" class="small-box-footer">
                            Manage Templates <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Breakdown Section -->
            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title text-bold"><i class="fas fa-tags mr-2"></i>Followup Categories Breakdown</h3>
                </div>
                <div class="card-body">
                    <div class="row" id="statusBreakdownRow">
                        <div class="col-12 text-center text-muted py-3">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Analyzing categories...
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Urgent Followups Table -->
                <div class="col-lg-8">
                    <div class="card card-danger card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title text-bold">
                                <i class="fas fa-bell mr-2"></i>
                                Urgent Followups (Pending Attention)
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Client Details</th>
                                            <th>Followup Topic</th>
                                            <th>Date</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="urgentFollowupsTable">
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <i class="fas fa-sync-alt fa-spin fa-2x text-muted mb-3"></i>
                                                <p>Fetching urgent clients...</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Latest Client Responses -->
                <div class="col-lg-4">
                    <div class="card card-info card-outline shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title text-bold">
                                <i class="fas fa-comments mr-2"></i>
                                Recent Client Feedback
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="direct-chat-messages" id="recentResponsesDiv" style="height: auto; min-height: 380px;">
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-spinner fa-spin mb-2"></i><br>Loading feedback...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <!-- Recently Updated Clients -->
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header border-transparent">
                            <h3 class="card-title text-bold"><i class="fas fa-history mr-2"></i>Recently Added / Updated Clients</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover m-0">
                                    <thead>
                                        <tr>
                                            <th>Client Name</th>
                                            <th>Contact</th>
                                            <th>Last Message Sent</th>
                                            <th>Activity Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentActivityBody">
                                        <!-- Loaded via JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="followup_client.php" class="btn btn-sm btn-link font-weight-bold">VIEW ALL CLIENTS</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal Container for dynamic loading -->
<div id="modalContainer"></div>

<!-- Page specific JavaScript -->
<script src="assets/js/followup_dashboard.js?v=<?php echo time(); ?>"></script>

<style>
.content-wrapper { background-color: #f4f6f9; }
.small-box { border-radius: 15px; overflow: hidden; transition: all 0.3s ease; border: none; }
.small-box:hover { transform: translateY(-7px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
.small-box h3 { font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; }
.small-box .icon { top: 10px; right: 15px; font-size: 60px; opacity: 0.2; }

.card { border-radius: 15px; border: none; box-shadow: 0 0 15px rgba(0,0,0,0.05) !important; transition: all 0.3s ease; }
.card-header { border-top-left-radius: 15px !important; border-top-right-radius: 15px !important; background-color: #fff; padding: 1.2rem 1.5rem; }
.card-outline.card-primary { border-top: 4px solid #007bff; }
.card-outline.card-danger { border-top: 4px solid #dc3545; }
.card-outline.card-info { border-top: 4px solid #17a2b8; }

.table thead th { border-top: 0; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #888; border-bottom: 2px solid #f4f4f4; }
.table td { vertical-align: middle; padding: 1rem 1.2rem; }

.category-box { 
    background: #fff; 
    border-radius: 12px; 
    padding: 20px; 
    margin-bottom: 20px; 
    border: 1px solid rgba(0,0,0,0.05); 
    transition: all 0.3s ease; 
    box-shadow: 0 2px 5px rgba(0,0,0,0.02);
}
.category-box:hover { 
    background: #fff; 
    transform: scale(1.03); 
    box-shadow: 0 5px 15px rgba(0,0,0,0.08); 
    border-color: #007bff;
}

.direct-chat-msg { margin-bottom: 1.5rem; padding: 0 1.2rem; }
.direct-chat-text { 
    background: #f8f9fa; 
    border: 1px solid #e9ecef; 
    border-radius: 12px; 
    padding: 12px 18px; 
    font-size: 0.95rem; 
    line-height: 1.5; 
    color: #444; 
}
.direct-chat-text:after, .direct-chat-text:before { display: none; } /* Clear AdminLTE default chat arrow */

.msg-snippet { 
    display: block; 
    max-width: 300px; 
    overflow: hidden; 
    text-overflow: ellipsis; 
    white-space: nowrap; 
    color: #777; 
    font-size: 0.85rem; 
}

.bg-info { background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%) !important; }
.bg-warning { background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important; }
.bg-danger { background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%) !important; }
.bg-success { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important; }

/* Scrollbar styling */
.direct-chat-messages::-webkit-scrollbar { width: 5px; }
.direct-chat-messages::-webkit-scrollbar-track { background: #f1f1f1; }
.direct-chat-messages::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
</style>

<?php require_once 'inc/footer.php'; ?>
