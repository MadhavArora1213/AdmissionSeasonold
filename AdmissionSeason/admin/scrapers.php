<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'jobs';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scraper & Data Pipeline | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .scr-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .scr-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .scr-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .health-indicator { width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 5px; }
        .health-good { background: var(--success); box-shadow: 0 0 10px var(--success); }
        .health-warning { background: var(--warning); box-shadow: 0 0 10px var(--warning); }
        .health-critical { background: var(--danger); box-shadow: 0 0 10px var(--danger); }
        
        .diff-row { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr; gap: 15px; padding: 12px; border-bottom: 1px solid var(--border-color); align-items: center; }
        .diff-label { font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 2px; }
        .diff-old { color: var(--danger); text-decoration: line-through; opacity: 0.6; }
        .diff-new { color: var(--success); font-weight: 700; }
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
                        <h1 class="page-title">Scraper & Data Pipeline Dashboard</h1>
                        <p class="page-subtitle">Ensuring 100% accurate institution data via automated intelligence and human verification.</p>
                    </div>
                </div>

                <div class="scr-tabs">
                    <a href="?view=jobs" class="scr-tab <?php echo $view == 'jobs' ? 'active' : ''; ?>">Scraper Job Manager</a>
                    <a href="?view=diff" class="scr-tab <?php echo $view == 'diff' ? 'active' : ''; ?>">Data Diff Queue</a>
                    <a href="?view=conflicts" class="scr-tab <?php echo $view == 'conflicts' ? 'active' : ''; ?>">Conflict Resolver</a>
                    <a href="?view=errors" class="scr-tab <?php echo $view == 'errors' ? 'active' : ''; ?>">Error Logs</a>
                </div>

                <?php if ($view == 'jobs'): ?>
                <!-- Screen 6.1.1 — Scraper Job Manager -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Active Data Scrapers</h3>
                        <button class="btn btn-primary" style="font-size: 0.75rem;">+ Create New Scraper</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Job Name</th>
                                <th>Target Source</th>
                                <th>Schedule</th>
                                <th>Health</th>
                                <th>Last Run</th>
                                <th>Success Rate</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>NIRF Rank Scraper</strong></td>
                                <td style="font-size: 0.8rem;">nirfindia.org (Official)</td>
                                <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">Weekly (Mon 03:00)</span></td>
                                <td><span class="health-indicator health-good"></span> <span style="font-size: 0.75rem;">Healthy</span></td>
                                <td>
                                    <div style="font-size: 0.8rem;">12 May 2026</div>
                                    <div style="font-size: 0.65rem; color: var(--success);">+142 records updated</div>
                                </td>
                                <td><span style="font-weight: 700; color: var(--success);">98.2%</span></td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <button class="action-btn" title="Run Now"><i class="fas fa-play"></i></button>
                                        <button class="action-btn" title="Pause"><i class="fas fa-pause"></i></button>
                                        <button class="action-btn"><i class="fas fa-cog"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>AmbitionBox Salary Data</strong></td>
                                <td style="font-size: 0.8rem;">ambitionbox.com</td>
                                <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">Monthly</span></td>
                                <td><span class="health-indicator health-warning"></span> <span style="font-size: 0.75rem;">Rate Limited</span></td>
                                <td>
                                    <div style="font-size: 0.8rem;">01 May 2026</div>
                                    <div style="font-size: 0.65rem; color: var(--warning);">Timeout on 12% requests</div>
                                </td>
                                <td><span style="font-weight: 700; color: var(--warning);">74.1%</span></td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <button class="action-btn"><i class="fas fa-play"></i></button>
                                        <button class="action-btn"><i class="fas fa-pause"></i></button>
                                        <button class="action-btn"><i class="fas fa-cog"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'diff'): ?>
                <!-- Screen 6.1.2 — Data Diff Viewer (Scraper Output) -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Pending Data Diff Queue</h3>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.75rem;">Bulk Approve High Confidence</button>
                        </div>
                    </div>
                    
                    <div class="diff-view-header" style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr; gap: 15px; padding: 12px; border-bottom: 2px solid var(--border-color); font-weight: 700; font-size: 0.8rem; color: var(--text-secondary);">
                        <div>College & Field</div>
                        <div>Current (Live)</div>
                        <div>New (Scraped)</div>
                        <div>Confidence</div>
                        <div>Action</div>
                    </div>

                    <div class="diff-row">
                        <div>
                            <div style="font-weight: 700;">BITS Pilani</div>
                            <div class="diff-label">NIRF Engineering Rank 2026</div>
                        </div>
                        <div class="diff-old">#25</div>
                        <div class="diff-new">#21</div>
                        <td><span class="status-badge status-approved">High (NIRF.org)</span></td>
                        <td><button class="btn btn-primary" style="font-size: 0.7rem; padding: 5px 10px;">Apply Change</button></td>
                    </tr>
                    <div class="diff-row">
                        <div>
                            <div style="font-weight: 700;">LPU Jalandhar</div>
                            <div class="diff-label">Hostel Fee (AC Double)</div>
                        </div>
                        <div class="diff-old">₹ 1,15,000</div>
                        <div class="diff-new">₹ 1,28,000</div>
                        <td><span class="status-badge" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);">Medium (Official Site)</span></td>
                        <td><button class="btn btn-primary" style="font-size: 0.7rem; padding: 5px 10px;">Apply Change</button></td>
                    </tr>
                </div>

                <?php elseif ($view == 'conflicts'): ?>
                <!-- Screen 6.1.3 — Conflict Resolver -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Scraper vs College Conflicts</h3>
                        <span class="status-badge status-rejected">4 High Impact Conflicts</span>
                    </div>
                    
                    <div style="background: rgba(30, 41, 59, 0.4); padding: 1.5rem; border-radius: 12px; margin-top: 1.5rem; border-left: 4px solid var(--danger);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <div>
                                <h4 style="color: white; margin-bottom: 5px;">College: VIT Vellore</h4>
                                <div style="font-size: 0.75rem; color: var(--danger); font-weight: 700;">Field: NAAC Grading</div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 0.7rem; color: var(--text-secondary); margin-bottom: 5px;">Detected 12h ago</div>
                                <span class="status-badge status-rejected">ESCALATED TO SUPER ADMIN</span>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                            <div style="background: rgba(239, 68, 68, 0.05); padding: 1rem; border-radius: 8px;">
                                <div style="font-size: 0.7rem; color: var(--danger); font-weight: 700; margin-bottom: 10px;">SCRAPED VALUE (Official NAAC Portal)</div>
                                <div style="font-size: 1.5rem; font-weight: 700;">A++</div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 5px;"><a href="#" style="color: var(--accent-primary);">Source URL: naac.gov.in/verify/vit</a></div>
                            </div>
                            <div style="background: rgba(16, 185, 129, 0.05); padding: 1rem; border-radius: 8px;">
                                <div style="font-size: 0.7rem; color: var(--success); font-weight: 700; margin-bottom: 10px;">COLLEGE SUBMITTED VALUE</div>
                                <div style="font-size: 1.5rem; font-weight: 700;">A (Tier 1)</div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 5px;">Via B2B Portal (Mar 12, 2026)</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.8rem;">Use Scraped Value (A++)</button>
                            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.8rem;">Use College Value (A)</button>
                            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.8rem;">Mark Under Verification</button>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'errors'): ?>
                <!-- Screen 6.1.4 — Scraper Error Log -->
                <div class="dashboard-grid">
                    <div class="widget w-two-thirds">
                        <h3 class="widget-title">Error Trend (Last 30 Days)</h3>
                        <canvas id="errorTrendChart" height="150"></canvas>
                    </div>
                    <div class="widget w-third">
                        <h3 class="widget-title">Anti-Block Status</h3>
                        <div style="margin-top: 1rem;">
                            <div style="padding: 10px; background: rgba(0,0,0,0.2); border-radius: 8px; margin-bottom: 10px;">
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">IP Blocked By</div>
                                <div style="font-weight: 700;">NIRF Portal (182.XX.XX.XX)</div>
                                <button class="btn" style="font-size: 0.6rem; background: var(--accent-primary); margin-top: 5px;">Rotate Proxy</button>
                            </div>
                            <div style="padding: 10px; background: rgba(0,0,0,0.2); border-radius: 8px;">
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">HTTP 429 (Rate Limited)</div>
                                <div style="font-weight: 700;">3 Sources Detected</div>
                                <button class="btn" style="font-size: 0.6rem; background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; margin-top: 5px;">Adjust Frequency</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script>
        if (document.getElementById('errorTrendChart')) {
            const ctx = document.getElementById('errorTrendChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array.from({length: 30}, (_, i) => i + 1),
                    datasets: [{
                        label: 'Error Rate %',
                        data: Array.from({length: 30}, () => Math.random() * 5),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, max: 10 } }
                }
            });
        }
    </script>
</body>
</html>
