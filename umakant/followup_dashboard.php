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
.content-wrapper { background-color: #f8fafc; }
.small-box { border-radius: 20px; overflow: hidden; transition: all 0.3s ease; border: none; }
.small-box:hover { transform: translateY(-7px); box-shadow: 0 15px 30px rgba(0,0,0,0.12) !important; }
.small-box h3 { font-size: 2.8rem; font-weight: 800; letter-spacing: -1.5px; margin-bottom: 2px; }
.small-box p { font-size: 0.95rem; font-weight: 600; opacity: 0.9; }
.small-box .icon { top: 10px; right: 15px; font-size: 70px; opacity: 0.2; transition: all 0.3s ease; }
.small-box:hover .icon { transform: scale(1.1); opacity: 0.3; }

.card { border-radius: 20px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important; }
.card-header { border-top-left-radius: 20px !important; border-top-right-radius: 20px !important; background-color: #fff; padding: 1.5rem; border-bottom: 1px solid #f1f5f9; }
.card-title { font-size: 1.1rem; color: #1e293b; }

.table thead th { background: #f8fafc; color: #64748b; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; padding: 1.2rem; border-bottom: 2px solid #f1f5f9; }
.table td { vertical-align: middle; padding: 1.2rem; border-color: #f1f5f9; }

.category-box:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important; }
.border-top-4 { border-top-width: 4px !important; }

.shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important; }
.bg-light-soft { background-color: #f1f5f9; }

.direct-chat-msg { margin-bottom: 1.5rem; padding: 0 1.5rem; position: relative; }
.direct-chat-msg:after { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: #e2e8f0; border-radius: 3px; }
.direct-chat-text { 
    background: #fff; 
    border: 1px solid #e2e8f0; 
    border-radius: 12px; 
    padding: 15px; 
    font-size: 0.9rem; 
    line-height: 1.6; 
    color: #334155; 
    margin-left: 10px;
}
.direct-chat-text:before, .direct-chat-text:after { display: none; }

.msg-snippet { max-width: 280px; display: inline-block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.modal-content { border-radius: 20px !important; }
.bg-gradient-primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important; }
.modal-body { background: #fff; }

.btn-white { background: #fff; border: 1px solid #e2e8f0; color: #64748b; }
.btn-white:hover { background: #f8fafc; color: #1e293b; }

/* Gradients */
.bg-info { background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%) !important; }
.bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%) !important; }
.bg-danger { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important; }
.bg-success { background: linear-gradient(135deg, #10b981 0%, #047857 100%) !important; }

.direct-chat-messages::-webkit-scrollbar { width: 4px; }
.direct-chat-messages::-webkit-scrollbar-track { background: transparent; }
.direct-chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<?php require_once 'inc/footer.php'; ?>
