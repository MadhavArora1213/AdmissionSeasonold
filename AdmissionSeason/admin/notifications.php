<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'email';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification & Comms Centre | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .not-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; overflow-x: auto; padding-bottom: 5px; }
        .not-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; white-space: nowrap; }
        .not-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .template-card { background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1.2rem; border-radius: 12px; margin-bottom: 1rem; transition: 0.2s; }
        .template-card:hover { border-color: var(--accent-primary); background: rgba(99, 102, 241, 0.05); }
        
        .token { background: rgba(249, 115, 22, 0.1); color: #f97316; padding: 2px 4px; border-radius: 4px; font-family: monospace; font-size: 0.8rem; }
        .sms-preview { background: #e2e8f0; color: #1e293b; padding: 15px; border-radius: 20px 20px 20px 5px; max-width: 250px; font-size: 0.85rem; line-height: 1.4; position: relative; margin-top: 1rem; }
        .dlt-status { font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
        
        .qr-placeholder { width: 180px; height: 180px; background: white; padding: 10px; border-radius: 8px; margin: 1rem auto; display: flex; align-items: center; justify-content: center; }
        .flow-node { background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; font-size: 0.75rem; text-align: center; position: relative; }
        .flow-line { height: 20px; width: 2px; background: var(--border-color); margin: 0 auto; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Notification & Communication Centre</h1>
                        <p class="page-subtitle">Managing omni-channel student outreach across Brevo (Email/SMS) and WhatsApp.</p>
                    </div>
                </div>

                <div class="not-tabs">
                    <a href="?view=email" class="not-tab <?php echo $view == 'email' ? 'active' : ''; ?>">Email Library</a>
                    <a href="?view=sms" class="not-tab <?php echo $view == 'sms' ? 'active' : ''; ?>">SMS Templates</a>
                    <a href="?view=campaigns" class="not-tab <?php echo $view == 'campaigns' ? 'active' : ''; ?>">Campaign Scheduler</a>
                    <a href="?view=whatsapp" class="not-tab <?php echo $view == 'whatsapp' ? 'active' : ''; ?>">WhatsApp Bot</a>
                </div>

                <?php if ($view == 'email'): ?>
                <!-- Screen 11.1.1 — Email Template Library -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Brevo Transactional Templates</h3>
                        <button class="btn btn-primary" style="font-size: 0.75rem;">+ New Template</button>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-top: 1.5rem;">
                        <div class="template-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <div>
                                    <h4 style="color: white; font-size: 0.95rem;">Lead Alert to College</h4>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Subject: New Student Enquiry for <span class="token">{{course_name}}</span></div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--success);">42.1% Open</div>
                                    <div style="font-size: 0.65rem; color: var(--text-secondary);">1.2k sent this mo</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-primary" style="font-size: 0.7rem;">Edit HTML</button>
                                <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.7rem;">Subject A/B Test</button>
                            </div>
                        </div>

                        <div class="template-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <div>
                                    <h4 style="color: white; font-size: 0.95rem;">Scholarship Deadline Reminder</h4>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Subject: Hurry! <span class="token">{{scholarship_name}}</span> expires in 7 days</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.8rem; font-weight: 700; color: var(--warning);">28.4% Open</div>
                                    <div style="font-size: 0.65rem; color: var(--text-secondary);">842 sent this mo</div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-primary" style="font-size: 0.7rem;">Edit HTML</button>
                                <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.7rem;">A/B Test Winner</button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'sms'): ?>
                <!-- Screen 11.1.2 — SMS Content Manager (DLT Focused) -->
                <div class="dashboard-grid">
                    <div class="widget w-two-thirds">
                        <div class="widget-header">
                            <h3 class="widget-title">TRAI DLT Registered SMS Templates</h3>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Template Name</th>
                                    <th>Message Text</th>
                                    <th>DLT ID</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>OTP Verification</strong></td>
                                    <td style="font-size: 0.75rem;">Your EduSearch OTP is <span class="token">{{otp}}</span>. Valid for 10 mins. - EDUSRCH</td>
                                    <td style="font-family: monospace;">120716...</td>
                                    <td><span class="dlt-status status-approved" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">APPROVED</span></td>
                                    <td><button class="action-btn"><i class="fas fa-eye"></i></button></td>
                                </tr>
                                <tr style="background: rgba(239, 68, 68, 0.05);">
                                    <td><strong>Lead Follow-up</strong></td>
                                    <td style="font-size: 0.75rem;">Hi <span class="token">{{name}}</span>, <span class="token">{{college}}</span> wants to talk to you. Call at <span class="token">{{phone}}</span>.</td>
                                    <td style="color: var(--danger);">MISSING</td>
                                    <td><span class="dlt-status status-rejected" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">REJECTED</span></td>
                                    <td><button class="btn" style="font-size: 0.6rem; background: var(--accent-primary);">Update DLT ID</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="widget w-third">
                        <h3 class="widget-title">Mobile Preview</h3>
                        <div class="sms-preview">
                            <strong>EDUSRCH</strong><br>
                            Hi Rahul, BITS Pilani wants to talk to you. Call at +91 98XXX XXX82.
                        </div>
                        <div style="margin-top: 1.5rem; font-size: 0.7rem; color: var(--text-secondary);">
                            Character Count: 78 / 160 (1 SMS Credit)<br>
                            Sender ID: <strong>EDUSRCH</strong>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'campaigns'): ?>
                <!-- Screen 11.1.3 — Campaign Scheduler -->
                <div class="dashboard-grid">
                    <div class="widget w-full">
                        <div class="widget-header">
                            <h3 class="widget-title">Targeted Outreach Campaigns</h3>
                            <button class="btn btn-primary" style="font-size: 0.75rem;">+ New Campaign</button>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Campaign Name</th>
                                    <th>Segment</th>
                                    <th>Schedule</th>
                                    <th>Performance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Maharashtra Engg Nudge</strong></td>
                                    <td><span class="token">Shortlisted but no Lead</span> (Engg, MH)</td>
                                    <td>Daily 8:30 PM</td>
                                    <td style="font-weight: 700; color: var(--success);">14.2% Conv</td>
                                    <td><button class="action-btn"><i class="fas fa-chart-bar"></i></button></td>
                                </tr>
                                <tr>
                                    <td><strong>Monthly Newsletter</strong></td>
                                    <td>All Registered Students</td>
                                    <td>1st of Month</td>
                                    <td>32% Open</td>
                                    <td><button class="action-btn"><i class="fas fa-pause"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <p style="margin-top: 1.5rem; font-size: 0.75rem; color: var(--warning); text-align: center;">
                            <i class="fas fa-clock"></i> Optimization Tip: Peak engagement window for students is <strong>8 PM - 11 PM</strong>. Schedule campaigns accordingly.
                        </p>
                    </div>
                </div>

                <?php elseif ($view == 'whatsapp'): ?>
                <!-- Screen 11.1.4 — WhatsApp Bot Manager -->
                <div class="dashboard-grid">
                    <div class="widget w-third" style="text-align: center;">
                        <h3 class="widget-title">Baileys Bot Status</h3>
                        <div style="margin: 1.5rem 0;">
                            <div style="color: var(--warning); font-weight: 700; font-size: 1.1rem;"><i class="fas fa-link-slash"></i> DISCONNECTED</div>
                            <div class="qr-placeholder">
                                <i class="fas fa-qrcode" style="font-size: 5rem; color: #1e293b;"></i>
                            </div>
                            <p style="font-size: 0.75rem; color: var(--text-secondary);">Scan QR with WhatsApp to reconnect bot.</p>
                        </div>
                    </div>
                    
                    <div class="widget w-two-thirds">
                        <h3 class="widget-title">Conversation Flow Nodes</h3>
                        <div style="margin-top: 2rem; display: flex; flex-direction: column; align-items: center;">
                            <div class="flow-node" style="width: 200px; border-color: var(--accent-primary);">
                                <strong>Node 1: Greet</strong><br>
                                "Hi! Looking for colleges?"
                                <div style="font-size: 0.6rem; color: var(--success); margin-top: 5px;">100% Completion</div>
                            </div>
                            <div class="flow-line"></div>
                            <div class="flow-node" style="width: 200px;">
                                <strong>Node 2: Stream Selection</strong><br>
                                "Choose Engineering or MBA"
                                <div style="font-size: 0.6rem; color: var(--success); margin-top: 5px;">82% Proceeded</div>
                            </div>
                            <div class="flow-line"></div>
                            <div class="flow-node" style="width: 200px; border-color: var(--danger);">
                                <strong>Node 3: Lead Form</strong><br>
                                "Share your phone number"
                                <div style="font-size: 0.6rem; color: var(--danger); margin-top: 5px;">24% Abandonment</div>
                            </div>
                        </div>
                        <button class="btn btn-primary" style="width: 100%; margin-top: 2rem;">Edit Visual Flow...</button>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html>
