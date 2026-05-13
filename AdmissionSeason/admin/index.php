<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Fetch some real counts
$leads_today = $pdo->query("SELECT COUNT(*) FROM leads WHERE DATE(created_at) = CURRENT_DATE")->fetchColumn();
$pending_reviews = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'PENDING'")->fetchColumn();
$active_colleges = $pdo->query("SELECT COUNT(*) FROM colleges WHERE is_verified = 1")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// Mock revenue and DAU
$revenue_mtd = 142000;
$dau = 1240;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <div class="page-header">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                        <div>
                            <h1 class="page-title">Command Centre</h1>
                            <p class="page-subtitle">Welcome back, Super Admin. Here is what's happening today.</p>
                        </div>
                        <div class="header-btns" style="display: flex; gap: 10px;">
                            <div style="display: flex; align-items: center; background: rgba(0,0,0,0.2); padding: 5px 15px; border-radius: 8px; border: 1px solid var(--border-color);">
                                <span style="font-size: 0.75rem; color: var(--text-secondary); margin-right: 10px;">Role:</span>
                                <select id="role-switcher" style="background: transparent; border: none; color: white; font-size: 0.8rem; outline: none; cursor: pointer;">
                                    <option value="SUPER_ADMIN" selected>Super Admin</option>
                                    <option value="CONTENT_MODERATOR">Content Moderator</option>
                                    <option value="FINANCE_MANAGER">Finance Manager</option>
                                    <option value="SEO_MANAGER">SEO Manager</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" onclick="document.getElementById('threshold-modal').style.display='flex'"><i class="fas fa-cog"></i> Config Alerts</button>
                        </div>
                    </div>
                </div>

                <!-- Alert Threshold Modal -->
                <div id="threshold-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
                    <div class="widget" style="width: 400px; background: var(--sidebar-bg); border: 1px solid var(--accent-primary);">
                        <div class="widget-header">
                            <h3 class="widget-title">Personal Alert Thresholds</h3>
                            <button class="action-btn" onclick="document.getElementById('threshold-modal').style.display='none'"><i class="fas fa-times"></i></button>
                        </div>
                        <div style="padding: 1rem;">
                            <div style="margin-bottom: 1.5rem;">
                                <label style="font-size: 0.8rem; color: var(--text-secondary);">Notify when Pending Reviews ></label>
                                <input type="number" value="50" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; padding: 8px; border-radius: 4px; margin-top: 5px;">
                            </div>
                            <div style="margin-bottom: 1.5rem;">
                                <label style="font-size: 0.8rem; color: var(--text-secondary);">Notify when VPS RAM % ></label>
                                <input type="number" value="85" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; padding: 8px; border-radius: 4px; margin-top: 5px;">
                            </div>
                            <button class="btn btn-primary" style="width: 100%;" onclick="document.getElementById('threshold-modal').style.display='none'">Save Config</button>
                        </div>
                    </div>
                </div>
                
                <div class="kpi-strip" id="kpi-strip">
                    <div class="kpi-card" data-kpi="dau">
                        <div class="kpi-label">DAU (Students) <i class="fas fa-users text-primary"></i></div>
                        <div class="kpi-value"><?php echo number_format($dau); ?></div>
                        <div class="kpi-trend trend-up"><i class="fas fa-arrow-up"></i> 12% vs yesterday</div>
                    </div>
                    <div class="kpi-card" data-kpi="leads">
                        <div class="kpi-label">Leads Today <i class="fas fa-bullseye text-primary"></i></div>
                        <div class="kpi-value"><?php echo number_format($leads_today); ?></div>
                        <div class="kpi-trend trend-up"><i class="fas fa-arrow-up"></i> 8% vs yesterday</div>
                    </div>
                    <div class="kpi-card" data-kpi="reviews">
                        <div class="kpi-label">Pending Reviews <i class="fas fa-star text-primary"></i></div>
                        <div class="kpi-value"><?php echo number_format($pending_reviews); ?></div>
                        <div class="kpi-trend <?php echo $pending_reviews > 10 ? 'trend-down' : 'trend-up'; ?>">
                            <?php echo $pending_reviews > 20 ? 'Action required' : 'Manageable'; ?>
                        </div>
                    </div>
                    <div class="kpi-card" data-kpi="clients">
                        <div class="kpi-label">Active Clients <i class="fas fa-university text-primary"></i></div>
                        <div class="kpi-value"><?php echo number_format($active_colleges); ?></div>
                        <div class="kpi-trend trend-up">Growing steadily</div>
                    </div>
                    <div class="kpi-card" data-kpi="revenue">
                        <div class="kpi-label">Revenue MTD <i class="fas fa-rupee-sign text-primary"></i></div>
                        <div class="kpi-value">₹<?php echo number_format($revenue_mtd); ?></div>
                        <div class="kpi-trend trend-up"><i class="fas fa-arrow-up"></i> 24% vs last month</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">VPS Health <i class="fas fa-server text-primary"></i></div>
                        <div class="kpi-value" style="color: var(--success);">NORMAL</div>
                        <div class="kpi-trend">All systems operational</div>
                    </div>
                </div>

                <!-- Quick Actions Bar -->
                <div class="quick-actions-bar" style="display: flex; gap: 1rem; margin-bottom: 2rem; background: rgba(30, 41, 59, 0.4); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); align-self: center; margin-right: 10px; text-transform: uppercase;">Quick Actions:</span>
                    <a href="colleges.php?action=add" class="btn" style="background: rgba(255,255,255,0.05); font-size: 0.8rem;"><i class="fas fa-plus"></i> Add College</a>
                    <a href="reviews.php" class="btn" style="background: rgba(255,255,255,0.05); font-size: 0.8rem;"><i class="fas fa-check"></i> Approve Reviews (<?php echo $pending_reviews; ?>)</a>
                    <a href="billing.php?action=invoices" class="btn" style="background: rgba(255,255,255,0.05); font-size: 0.8rem;"><i class="fas fa-file-invoice"></i> Generate Invoices</a>
                    <a href="leads.php?filter=disputed" class="btn" style="background: rgba(255,255,255,0.05); font-size: 0.8rem;"><i class="fas fa-exclamation-triangle"></i> View Disputes</a>
                    <button class="btn" style="background: rgba(255,255,255,0.05); font-size: 0.8rem;"><i class="fas fa-search"></i> Trigger Re-index</button>
                </div>
                
                <div class="dashboard-grid" id="dashboard-grid">
                    <!-- Widget 1: Student Activity -->
                    <div class="widget w-half draggable" data-widget="student-activity">
                        <div class="widget-header">
                            <h3 class="widget-title"><i class="fas fa-chart-line"></i> Student Activity</h3>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <select class="time-range-toggle" style="background: transparent; border: none; color: var(--text-secondary); outline: none; font-size: 0.8rem;">
                                    <option value="7">Last 7 Days</option>
                                    <option value="30" selected>Last 30 Days</option>
                                    <option value="90">Last 90 Days</option>
                                </select>
                                <button class="action-btn export-csv" title="Export CSV"><i class="fas fa-download"></i></button>
                                <button class="action-btn collapse-widget"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="widget-content">
                            <canvas id="activityChart" height="200"></canvas>
                        </div>
                    </div>
                    
                    <!-- Widget 2: Lead Velocity -->
                    <div class="widget w-half draggable" data-widget="lead-velocity">
                        <div class="widget-header">
                            <h3 class="widget-title"><i class="fas fa-bolt"></i> Lead Velocity</h3>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <button class="action-btn export-csv" title="Export CSV"><i class="fas fa-download"></i></button>
                                <button class="action-btn collapse-widget"><i class="fas fa-minus"></i></button>
                                <i class="fas fa-ellipsis-v text-secondary"></i>
                            </div>
                        </div>
                        <div class="widget-content">
                            <canvas id="leadChart" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Widget 3: Revenue Trend -->
                    <div class="widget w-half draggable" data-widget="revenue-trend">
                        <div class="widget-header">
                            <h3 class="widget-title"><i class="fas fa-hand-holding-usd"></i> Revenue Trend</h3>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <button class="action-btn export-csv" title="Export CSV"><i class="fas fa-download"></i></button>
                                <button class="action-btn collapse-widget"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="widget-content">
                            <canvas id="revenueChart" height="200"></canvas>
                        </div>
                    </div>

                    <!-- Widget 4: College Client Map (Mock) -->
                    <div class="widget w-half draggable" data-widget="college-map">
                        <div class="widget-header">
                            <h3 class="widget-title"><i class="fas fa-map-marked-alt"></i> College Client Map</h3>
                            <button class="action-btn collapse-widget"><i class="fas fa-minus"></i></button>
                        </div>
                        <div class="widget-content" style="height: 200px; background: rgba(0,0,0,0.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                             <i class="fas fa-map" style="font-size: 5rem; opacity: 0.1;"></i>
                             <div style="position: absolute; color: var(--accent-primary); top: 30%; left: 40%;"><i class="fas fa-circle"></i></div>
                             <div style="position: absolute; color: var(--accent-secondary); top: 50%; left: 60%;"><i class="fas fa-circle"></i></div>
                             <div style="position: absolute; color: var(--success); top: 20%; left: 70%;"><i class="fas fa-circle"></i></div>
                             <p style="position: absolute; bottom: 10px; font-size: 0.7rem; color: var(--text-secondary);">Proportional dot size by leads generated</p>
                        </div>
                    </div>

                    <!-- Widget 5: Moderation Queue Status -->
                    <div class="widget w-third draggable" data-widget="moderation-status">
                        <div class="widget-header">
                            <h3 class="widget-title"><i class="fas fa-tasks"></i> Moderation Status</h3>
                            <button class="action-btn collapse-widget"><i class="fas fa-minus"></i></button>
                        </div>
                        <div class="widget-content">
                            <canvas id="moderationDonut" height="150"></canvas>
                        </div>
                    </div>

                    <!-- Widget 6: AI Counselor Usage -->
                    <div class="widget w-third draggable" data-widget="ai-usage">
                        <div class="widget-header">
                            <h3 class="widget-title"><i class="fas fa-robot"></i> AI Counselor Usage</h3>
                            <button class="action-btn collapse-widget"><i class="fas fa-minus"></i></button>
                        </div>
                        <div class="widget-content" style="font-size: 0.85rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="color: var(--text-secondary);">Ollama Resp Time</span>
                                <span style="font-weight: 700;">2.4s</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="color: var(--text-secondary);">Cache Hit Rate</span>
                                <span style="color: var(--success); font-weight: 700;">72%</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="color: var(--text-secondary);">Current Queue</span>
                                <span style="color: var(--warning); font-weight: 700;">3 sess</span>
                            </div>
                        </div>
                    </div>

                    <!-- Widget 7: Top Searched Colleges -->
                    <div class="widget w-third draggable" data-widget="top-search">
                        <div class="widget-header">
                            <h3 class="widget-title"><i class="fas fa-search"></i> Top Searched</h3>
                            <button class="action-btn collapse-widget"><i class="fas fa-minus"></i></button>
                        </div>
                        <div class="widget-content">
                            <ul style="list-style: none; font-size: 0.8rem;">
                                <li style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span>1. BITS Pilani</span>
                                    <span style="color: var(--accent-primary);">1.2k</span>
                                </li>
                                <li style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span>2. VIT Vellore</span>
                                    <span style="color: var(--accent-primary);">940</span>
                                </li>
                                <li style="display: flex; justify-content: space-between;">
                                    <span>3. IIT Bombay</span>
                                    <span style="color: var(--accent-primary);">810</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Widget 8: System Alerts Feed -->
                    <div class="widget w-full draggable" data-widget="system-alerts">
                        <div class="widget-header">
                            <h3 class="widget-title"><i class="fas fa-bell"></i> System Alerts Feed</h3>
                            <button class="action-btn collapse-widget"><i class="fas fa-minus"></i></button>
                        </div>
                        <div class="widget-content" style="max-height: 200px; overflow-y: auto; font-size: 0.8rem;">
                            <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color); display: flex; gap: 10px;">
                                <span style="color: var(--danger);"><i class="fas fa-exclamation-circle"></i></span>
                                <div>
                                    <strong>Payment Failure:</strong> College ID 4291 failed subscription renewal.
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">2 mins ago</div>
                                </div>
                            </div>
                            <div style="padding: 8px 0; border-bottom: 1px solid var(--border-color); display: flex; gap: 10px;">
                                <span style="color: var(--success);"><i class="fas fa-check-circle"></i></span>
                                <div>
                                    <strong>Scraper Success:</strong> NIRF Sync completed for 500 records.
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">15 mins ago</div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </main>
    </div>

    <script>
        // Real-time KPI polling simulator (simulating WebSocket)
        function refreshKPIs() {
            // In a real app, this would be a fetch() call to a real-time endpoint
            console.log("Refreshing KPI data...");
            const cards = document.querySelectorAll('.kpi-card .kpi-value');
            cards.forEach(card => {
                // Subtle visual flicker to show update
                card.style.opacity = '0.5';
                setTimeout(() => {
                    card.style.opacity = '1';
                    // We won't actually change the numbers here to avoid confusion, 
                    // but this is where the dynamic update happens.
                }, 100);
            });
        }
        setInterval(refreshKPIs, 30000); // 30 seconds as per spec

        // Activity Chart
        const ctx1 = document.getElementById('activityChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'DAU',
                    data: [1200, 1900, 1500, 2100, 2400, 1800, 1600],
                    borderColor: '#6366f1',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(99, 102, 241, 0.1)'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Lead Chart
        const ctx2 = document.getElementById('leadChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Leads',
                    data: [45, 62, 55, 78, 84, 32, 28],
                    backgroundColor: '#a855f7'
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Revenue Chart
        const ctx3 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx3, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Revenue',
                    data: [25000, 42000, 38000, 56000],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { display: false },
                    x: { grid: { display: false } }
                }
            }
        });

        // Moderation Donut
        const ctx4 = document.getElementById('moderationDonut').getContext('2d');
        new Chart(ctx4, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Approved', 'Rejected'],
                datasets: [{
                    data: [12, 45, 8],
                    backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 10 } } } },
                cutout: '70%'
            }
        });

        // Role-Based Views Logic
        const roleSwitcher = document.getElementById('role-switcher');
        const widgets = document.querySelectorAll('.widget');
        
        roleSwitcher.addEventListener('change', function() {
            const role = this.value;
            widgets.forEach(w => {
                const widgetName = w.getAttribute('data-widget');
                w.style.display = 'block'; // Reset
                
                if (role === 'CONTENT_MODERATOR') {
                    if (!['moderation-status', 'system-alerts'].includes(widgetName)) {
                        w.style.display = 'none';
                    }
                } else if (role === 'FINANCE_MANAGER') {
                    if (!['revenue-trend', 'lead-velocity', 'system-alerts'].includes(widgetName)) {
                        w.style.display = 'none';
                    }
                } else if (role === 'SEO_MANAGER') {
                    if (!['student-activity', 'top-search', 'system-alerts'].includes(widgetName)) {
                        w.style.display = 'none';
                    }
                }
            });
        });

        // CSV Export Simulation
        document.querySelectorAll('.export-csv').forEach(btn => {
            btn.addEventListener('click', function() {
                const widgetName = this.closest('.widget').getAttribute('data-widget');
                alert(`Exporting CSV data for: ${widgetName}...`);
                // In real app: window.location.href = `/api/export?widget=${widgetName}`;
            });
        });

        // Collapse logic
        document.querySelectorAll('.collapse-widget').forEach(btn => {
            btn.addEventListener('click', function() {
                const content = this.closest('.widget').querySelector('.widget-content');
                const icon = this.querySelector('i');
                if (content.style.display === 'none') {
                    content.style.display = 'block';
                    icon.className = 'fas fa-minus';
                } else {
                    content.style.display = 'none';
                    icon.className = 'fas fa-plus';
                }
            });
        });
    </script>
</body>
</html>