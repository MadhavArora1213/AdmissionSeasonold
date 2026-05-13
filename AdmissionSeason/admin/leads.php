<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'monitor';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Management & Revenue Audit | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .lead-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .lead-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .lead-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .sla-card { background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 12px; border-top: 4px solid var(--accent-primary); text-align: center; }
        .heatmap-grid { display: grid; grid-template-columns: repeat(24, 1fr); gap: 2px; margin-top: 1rem; }
        .heat-cell { aspect-ratio: 1; background: rgba(99, 102, 241, 0.1); border-radius: 2px; }
        
        .failed-lead { color: var(--danger); font-weight: 700; cursor: pointer; text-decoration: underline; }
        .dedup-badge { background: rgba(16, 185, 129, 0.1); color: var(--success); font-size: 0.6rem; padding: 2px 5px; border-radius: 4px; margin-left: 5px; }
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
                        <h1 class="page-title">Lead Management & Audit</h1>
                        <p class="page-subtitle">Real-time revenue integrity layer and delivery performance monitoring.</p>
                    </div>
                </div>

                <div class="lead-tabs">
                    <a href="?view=monitor" class="lead-tab <?php echo $view == 'monitor' ? 'active' : ''; ?>">Live Monitor</a>
                    <a href="?view=disputes" class="lead-tab <?php echo $view == 'disputes' ? 'active' : ''; ?>">Dispute Workflow</a>
                    <a href="?view=performance" class="lead-tab <?php echo $view == 'performance' ? 'active' : ''; ?>">SLA & Performance</a>
                    <a href="?view=attribution" class="lead-tab <?php echo $view == 'attribution' ? 'active' : ''; ?>">Source Attribution</a>
                    <a href="?view=blacklist" class="lead-tab <?php echo $view == 'blacklist' ? 'active' : ''; ?>">Blacklist</a>
                </div>

                <?php if ($view == 'monitor'): ?>
                <!-- Screen 4.1.1 — Live Lead Monitor -->
                <div class="dashboard-grid">
                    <div class="widget w-full">
                        <div class="widget-header">
                            <h3 class="widget-title">Real-time Lead Flow (Today)</h3>
                            <div style="color: var(--danger); font-size: 0.75rem; font-weight: 700;">
                                <i class="fas fa-exclamation-circle"></i> Spike Detected: +42% Volume in last 60 mins
                            </div>
                        </div>
                        <canvas id="liveLeadChart" height="80"></canvas>
                    </div>

                    <div class="widget w-full">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>College / Course</th>
                                    <th>Quality</th>
                                    <th>Delivery Status</th>
                                    <th>Latency</th>
                                    <th>Audit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Rahul Sharma</strong> <span class="dedup-badge">MERGED</span></td>
                                    <td>BITS Pilani (B.Tech)</td>
                                    <td><span class="status-badge status-approved">9.2</span></td>
                                    <td><span class="status-badge status-approved"><i class="fas fa-check"></i> Delivered</span></td>
                                    <td>12s</td>
                                    <td><button class="action-btn"><i class="fas fa-history"></i></button></td>
                                </tr>
                                <tr>
                                    <td><strong>Test User</strong></td>
                                    <td>VIT Vellore (MBA)</td>
                                    <td><span class="status-badge status-rejected">1.4</span></td>
                                    <td><span class="failed-lead" onclick="alert('Brevo Error: SMS Blocked by Carrier (NDNC)')"><i class="fas fa-times-circle"></i> Failed</span></td>
                                    <td>-</td>
                                    <td><button class="action-btn" style="color: var(--accent-primary);">Retry</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'disputes'): ?>
                <!-- Screen 4.1.2 — Invalid Lead Dispute Workflow -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Active Dispute Queue</h3>
                        <div style="display: flex; gap: 10px;">
                            <span class="status-badge status-rejected">5 Overdue (SLA > 72h)</span>
                        </div>
                    </div>
                    
                    <div style="background: rgba(30, 41, 59, 0.4); padding: 1.5rem; border-radius: 12px; border-left: 4px solid var(--danger); margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.2rem;">
                            <div>
                                <h4 style="color: white; margin-bottom: 5px;">Dispute ID: #DSP-8291</h4>
                                <p style="font-size: 0.8rem; color: var(--text-secondary);">College: <strong>Manipal University</strong> &bull; 12 Leads disputed &bull; Reason: <strong>Wrong Number</strong></p>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.7rem; color: var(--text-secondary); margin-bottom: 5px;">Submitted 4 days ago</div>
                                <span class="status-badge status-rejected">SLA BREACHED</span>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                            <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px;">
                                <h5 style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 10px;">Investigation Tools</h5>
                                <div style="display: flex; gap: 10px;">
                                    <button class="btn" style="background: var(--sidebar-bg); color: white; font-size: 0.7rem;"><i class="fas fa-phone-alt"></i> Call (Masked)</button>
                                    <button class="btn" style="background: var(--sidebar-bg); color: white; font-size: 0.7rem;"><i class="fas fa-search"></i> Truecaller API</button>
                                </div>
                            </div>
                            <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px;">
                                <h5 style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 5px;">Internal Decision Note</h5>
                                <textarea placeholder="Reason for approval/rejection..." style="width: 100%; background: transparent; border: 1px solid var(--border-color); color: white; font-size: 0.75rem; padding: 5px; border-radius: 4px;"></textarea>
                            </div>
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.8rem;">Approve & Issue Credits (₹1,800)</button>
                            <button class="btn" style="background: var(--danger); color: white; font-size: 0.8rem;">Reject Dispute</button>
                            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.8rem;">Partial Approval...</button>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'performance'): ?>
                <!-- Screen 4.1.3 — Lead SLA & Performance Dashboard -->
                <div class="dashboard-grid">
                    <div class="widget w-full">
                        <h3 class="widget-title">Delivery Latency (Submission → SMS Received)</h3>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-top: 1.5rem;">
                            <div class="sla-card">
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Average Latency</div>
                                <div style="font-size: 1.8rem; font-weight: 700; color: var(--success);">14s</div>
                            </div>
                            <div class="sla-card">
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">P95 Latency</div>
                                <div style="font-size: 1.8rem; font-weight: 700; color: var(--success);">42s</div>
                            </div>
                            <div class="sla-card" style="border-top-color: var(--danger);">
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">P99 Latency</div>
                                <div style="font-size: 1.8rem; font-weight: 700; color: var(--danger);">8.2m</div>
                                <div style="font-size: 0.6rem; color: var(--danger);">Queue Backup Detected</div>
                            </div>
                            <div class="sla-card">
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Delivery Rate</div>
                                <div style="font-size: 1.8rem; font-weight: 700; color: var(--success);">99.4%</div>
                            </div>
                        </div>
                    </div>

                    <div class="widget w-half">
                        <h3 class="widget-title">College Conversion ROI (Lead → Enrolled)</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>College</th>
                                    <th>Conv Rate</th>
                                    <th>ROI Rank</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>LPU Jalandhar</td>
                                    <td><span style="font-weight: 700; color: var(--success);">12.4%</span></td>
                                    <td><span class="status-badge status-approved">#1</span></td>
                                </tr>
                                <tr>
                                    <td>Amity Univ</td>
                                    <td><span style="font-weight: 700; color: var(--accent-primary);">8.1%</span></td>
                                    <td><span class="status-badge status-approved">#2</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'attribution'): ?>
                <!-- Screen 4.1.4 — Lead Source Attribution -->
                <div class="dashboard-grid">
                    <div class="widget w-two-thirds">
                        <h3 class="widget-title">Channel Attribution</h3>
                        <canvas id="attributionChart" height="150"></canvas>
                    </div>
                    <div class="widget w-third">
                        <h3 class="widget-title">Hourly Heatmap (Submission Time)</h3>
                        <div class="heatmap-grid">
                            <?php for($i=0; $i<24; $i++): 
                                $opacity = ($i >= 20 || $i <= 2) ? 0.9 : (($i >= 12 && $i <= 14) ? 0.5 : 0.1);
                            ?>
                                <div class="heat-cell" style="opacity: <?php echo $opacity; ?>;" title="Hour: <?php echo $i; ?>:00"></div>
                            <?php endfor; ?>
                        </div>
                        <p style="font-size: 0.65rem; color: var(--text-secondary); margin-top: 10px; text-align: center;">Peaks observed between 8 PM - 11 PM</p>
                    </div>
                </div>

                <?php elseif ($view == 'blacklist'): ?>
                <!-- Blacklist Management -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Revenue Protection Blacklist</h3>
                        <button class="btn btn-primary" style="font-size: 0.75rem;">+ Add Block Identifier</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Identifier (Email/Phone)</th>
                                <th>Reason</th>
                                <th>Block Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>spam.bot@competitor.com</strong></td>
                                <td>Mass form submissions (422 in 1h)</td>
                                <td>12 May 2026</td>
                                <td><button class="action-btn" style="color: var(--danger);"><i class="fas fa-trash"></i> Unblock</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script>
        // Charts
        if (document.getElementById('liveLeadChart')) {
            const ctx1 = document.getElementById('liveLeadChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: ['10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm'],
                    datasets: [{
                        label: 'Leads / Hour',
                        data: [120, 150, 180, 420, 190, 160, 140],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4,
                        segment: { borderColor: ctx => ctx.p1.parsed.y > 400 ? '#ef4444' : '#6366f1' }
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { display: false } } }
            });
        }

        if (document.getElementById('attributionChart')) {
            const ctx2 = document.getElementById('attributionChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: ['SEO', 'Direct', 'AI Counselor', 'WhatsApp', 'Social'],
                    datasets: [{
                        label: 'Leads',
                        data: [420, 210, 180, 92, 45],
                        backgroundColor: '#6366f1',
                        borderRadius: 8
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } }, indexAxis: 'y' }
            });
        }
    </script>
</body>
</html>
