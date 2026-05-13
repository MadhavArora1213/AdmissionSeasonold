<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'queue';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Moderation | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .mod-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .mod-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .mod-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .side-panel { position: fixed; top: 0; right: -500px; width: 500px; height: 100%; background: var(--sidebar-bg); border-left: 1px solid var(--accent-primary); z-index: 1100; transition: 0.3s; padding: 2rem; overflow-y: auto; box-shadow: -10px 0 30px rgba(0,0,0,0.5); }
        .side-panel.open { right: 0; }
        
        .ai-analysis { background: rgba(99, 102, 241, 0.05); border: 1px solid var(--accent-primary); padding: 1rem; border-radius: 12px; margin-top: 1.5rem; }
        .flagged-row { background: rgba(239, 68, 68, 0.05) !important; border-left: 4px solid var(--danger); }
        .priority-row { border-left: 4px solid var(--accent-secondary); }
        
        .rule-card { background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); padding: 1rem; border-radius: 12px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; }
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
                        <h1 class="page-title">Content Moderation Queue</h1>
                        <p class="page-subtitle">Ensuring 100% authentic student reviews via AI-assisted verification.</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <span class="status-badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); font-weight: 700; font-size: 0.8rem; padding: 10px 15px;">
                            <i class="fas fa-clock"></i> SLA: 12 Reviews Overdue (48h+)
                        </span>
                    </div>
                </div>

                <div class="mod-tabs">
                    <a href="?view=queue" class="mod-tab <?php echo $view == 'queue' ? 'active' : ''; ?>">Moderation Queue</a>
                    <a href="?view=rules" class="mod-tab <?php echo $view == 'rules' ? 'active' : ''; ?>">Auto-Mod Rules</a>
                    <a href="?view=analytics" class="mod-tab <?php echo $view == 'analytics' ? 'active' : ''; ?>">Moderator Analytics</a>
                </div>

                <?php if ($view == 'queue'): ?>
                <!-- Screen 3.1.1 — Moderation Queue -->
                <div class="widget w-full">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; align-items: center;">
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" id="bulk-approve" style="display: none;"><i class="fas fa-check"></i> Bulk Approve</button>
                            <button class="btn" id="bulk-reject" style="background: var(--danger); color: white; display: none;"><i class="fas fa-times"></i> Bulk Reject</button>
                            <div class="header-search" style="width: 250px;">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Filter by college...">
                            </div>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">
                            Sort by: <select class="btn" style="background: transparent; color: white; border: none; font-weight: 700;">
                                <option>Priority (Low Reviews First)</option>
                                <option>Newest First</option>
                                <option>AI Score (High First)</option>
                            </select>
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="select-all"></th>
                                <th>Student</th>
                                <th>College / Course</th>
                                <th>Verification</th>
                                <th>AI Score</th>
                                <th>Sentiment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="flagged-row">
                                <td><input type="checkbox" class="review-check"></td>
                                <td>
                                    <div style="font-weight: 700;">Rahul K. <i class="fas fa-exclamation-triangle" style="color: var(--danger); font-size: 0.7rem;" title="Duplicate IP Detected"></i></div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Joined 2h ago</div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">LPU Jalandhar</div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">MBA 2024 Batch</div>
                                </td>
                                <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">ID Upload</span></td>
                                <td><span class="status-badge status-rejected">1.4/10</span></td>
                                <td><span style="color: var(--danger); font-size: 0.75rem;"><i class="fas fa-frown"></i> Negative</span></td>
                                <td><span class="status-badge status-pending">Auto-Flagged</span></td>
                                <td><button class="action-btn view-detail" onclick="openDetail(1)"><i class="fas fa-eye"></i></button></td>
                            </tr>
                            <tr class="priority-row">
                                <td><input type="checkbox" class="review-check"></td>
                                <td>
                                    <div style="font-weight: 700;">Ananya M.</div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Joined 2 years ago</div>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">BITS Pilani</div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">B.Tech CSE 2022</div>
                                </td>
                                <td><span class="status-badge" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">Email Verified</span></td>
                                <td><span class="status-badge status-approved">9.2/10</span></td>
                                <td><span style="color: var(--success); font-size: 0.75rem;"><i class="fas fa-smile"></i> Positive</span></td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td><button class="action-btn view-detail" onclick="openDetail(2)"><i class="fas fa-eye"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'rules'): ?>
                <!-- Screen 3.1.3 — Auto-Moderation Rules -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Active Automation Rules</h3>
                        <button class="btn btn-primary" style="font-size: 0.75rem;">+ Create New Rule</button>
                    </div>
                    
                    <div class="rule-card">
                        <div>
                            <div style="font-weight: 700; margin-bottom: 5px;">High Quality Auto-Approve</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">IF AI Score >= 8 AND Email Verified AND Account Age > 30d → <span style="color: var(--success); font-weight: 700;">APPROVE</span></div>
                        </div>
                        <div style="display: flex; gap: 2rem; align-items: center;">
                            <div style="text-align: center;">
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Processed Today</div>
                                <div style="font-weight: 700;">42</div>
                            </div>
                            <div class="status-badge status-approved">ACTIVE</div>
                            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
                        </div>
                    </div>

                    <div class="rule-card" style="border: 1px dashed var(--warning);">
                        <div>
                            <div style="font-weight: 700; margin-bottom: 5px;">Promotional Spam Reject <span class="status-badge" style="background: var(--warning); color: black; font-size: 0.6rem;">SHADOW MODE</span></div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">IF Text contains "Helpline" OR Phone Pattern → <span style="color: var(--danger); font-weight: 700;">REJECT</span></div>
                        </div>
                        <div style="display: flex; gap: 2rem; align-items: center;">
                            <div style="text-align: center;">
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Simulated Actions</div>
                                <div style="font-weight: 700;">8</div>
                            </div>
                            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--warning); color: var(--warning); font-size: 0.7rem;">Activate Rule</button>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'analytics'): ?>
                <!-- Screen 3.1.4 — Moderation Analytics -->
                <div class="dashboard-grid">
                    <div class="widget w-two-thirds">
                        <h3 class="widget-title">Review Volume Trends (30 Days)</h3>
                        <canvas id="reviewTrendChart" height="200"></canvas>
                    </div>
                    <div class="widget w-third">
                        <h3 class="widget-title">Rejection Reasons</h3>
                        <canvas id="rejectionPie" height="200"></canvas>
                    </div>
                    <div class="widget w-full">
                        <h3 class="widget-title">Moderator Performance Leaderboard</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Moderator</th>
                                    <th>Processed Today</th>
                                    <th>Avg Time / Review</th>
                                    <th>SLA Compliance</th>
                                    <th>Accuracy (Overturns)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Arjun S.</strong></td>
                                    <td>142</td>
                                    <td>42s</td>
                                    <td><span style="color: var(--success); font-weight: 700;">98%</span></td>
                                    <td><span style="color: var(--success); font-weight: 700;">0.2%</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Priya V.</strong></td>
                                    <td>118</td>
                                    <td>58s</td>
                                    <td><span style="color: var(--warning); font-weight: 700;">92%</span></td>
                                    <td><span style="color: var(--success); font-weight: 700;">0.8%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Screen 3.1.2 — Review Detail Pane -->
    <div class="side-panel" id="detail-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 style="font-size: 1.2rem; color: var(--accent-primary);">Review Details</h2>
            <button class="action-btn" onclick="closeDetail()"><i class="fas fa-times"></i></button>
        </div>

        <div style="margin-bottom: 2rem;">
            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 5px;">Overall Verdict</div>
            <p style="font-size: 1rem; color: white; font-weight: 700; line-height: 1.4;">"Great campus life but faculty needs improvement. Placement cell is very active."</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
            <div style="background: rgba(16, 185, 129, 0.05); padding: 1rem; border-radius: 8px;">
                <div style="font-size: 0.7rem; color: var(--success); font-weight: 700; margin-bottom: 5px;">PROS</div>
                <div style="font-size: 0.8rem;">World-class labs, great canteen, strong alumni network.</div>
            </div>
            <div style="background: rgba(239, 68, 68, 0.05); padding: 1rem; border-radius: 8px;">
                <div style="font-size: 0.7rem; color: var(--danger); font-weight: 700; margin-bottom: 5px;">CONS</div>
                <div style="font-size: 0.8rem;">Very strict attendance policy (85% mandatory).</div>
            </div>
        </div>

        <div class="ai-analysis">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-primary);"><i class="fas fa-robot"></i> Llama AI Analysis</span>
                <span class="status-badge status-approved" style="font-size: 0.7rem;">Score: 8/10</span>
            </div>
            <p style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.5;">
                Detailed and balanced. Student mentions specific placement company names (TCS, Wipro) which is a strong authenticity signal. No promotional language detected. IP address matches student's registered city.
            </p>
        </div>

        <div style="margin-top: 2rem;">
            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 10px;">Rejection Reason (if applicable)</div>
            <select class="btn" style="width: 100%; background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); margin-bottom: 1rem;">
                <option>Insufficient detail</option>
                <option>Promotional content</option>
                <option>Unverifiable claims</option>
                <option>Suspected fake identity</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 2rem;">
            <button class="btn btn-primary" style="flex: 2;">Approve Review</button>
            <button class="btn" style="background: var(--danger); color: white; flex: 1;">Reject</button>
            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; flex: 1;">Escalate</button>
        </div>
    </div>

    <script>
        function openDetail(id) {
            document.getElementById('detail-panel').classList.add('open');
        }
        function closeDetail() {
            document.getElementById('detail-panel').classList.remove('open');
        }

        // Bulk selection logic
        const selectAll = document.getElementById('select-all');
        const checks = document.querySelectorAll('.review-check');
        const bulkApprove = document.getElementById('bulk-approve');
        const bulkReject = document.getElementById('bulk-reject');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checks.forEach(c => c.checked = this.checked);
                toggleBulkBtns();
            });
            checks.forEach(c => c.addEventListener('change', toggleBulkBtns));
        }

        function toggleBulkBtns() {
            const anyChecked = Array.from(checks).some(c => c.checked);
            bulkApprove.style.display = anyChecked ? 'inline-block' : 'none';
            bulkReject.style.display = anyChecked ? 'inline-block' : 'none';
        }

        // Analytics Charts
        if (document.getElementById('reviewTrendChart')) {
            const ctx1 = document.getElementById('reviewTrendChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: Array.from({length: 30}, (_, i) => i + 1),
                    datasets: [{
                        label: 'Reviews Submitted',
                        data: Array.from({length: 30}, () => Math.floor(Math.random() * 50) + 20),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } }
                }
            });

            const ctx2 = document.getElementById('rejectionPie').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Insufficient Detail', 'Promotional', 'Unverifiable', 'Abusive'],
                    datasets: [{
                        data: [60, 20, 15, 5],
                        backgroundColor: ['#6366f1', '#a855f7', '#f59e0b', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 10 } } } }
                }
            });
        }
    </script>
</body>
</html>
