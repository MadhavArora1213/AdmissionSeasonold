<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'engagement';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Growth & Conversion Analytics | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .gro-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; overflow-x: auto; padding-bottom: 5px; }
        .gro-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; white-space: nowrap; }
        .gro-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        /* Funnel Styles */
        .funnel-container { display: flex; flex-direction: column; gap: 5px; margin-top: 2rem; }
        .funnel-step { display: flex; align-items: center; gap: 15px; }
        .funnel-bar { height: 40px; background: var(--accent-primary); border-radius: 4px; display: flex; align-items: center; padding: 0 15px; color: white; font-weight: 700; font-size: 0.85rem; transition: 0.5s; position: relative; }
        .funnel-drop { color: var(--danger); font-size: 0.75rem; font-weight: 700; width: 60px; }
        .funnel-label { width: 180px; font-size: 0.8rem; color: var(--text-secondary); text-align: right; }
        
        /* Cohort Table */
        .cohort-table { width: 100%; border-collapse: collapse; font-size: 0.75rem; }
        .cohort-table th, .cohort-table td { border: 1px solid var(--border-color); padding: 8px; text-align: center; }
        .cohort-cell { background: rgba(99, 102, 241, 0.1); }
        .cohort-high { background: rgba(99, 102, 241, 0.8); color: white; }
        .cohort-med { background: rgba(99, 102, 241, 0.4); }
        .cohort-low { background: rgba(99, 102, 241, 0.1); }
        
        .ab-test-card { background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1.2rem; border-radius: 12px; margin-bottom: 1rem; border-top: 4px solid var(--accent-primary); }
        .sig-badge { background: rgba(16, 185, 129, 0.1); color: var(--success); font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
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
                        <h1 class="page-title">Growth & Conversion Analytics</h1>
                        <p class="page-subtitle">Visualizing the student journey and optimizing product funnels for maximum ROI.</p>
                    </div>
                </div>

                <div class="gro-tabs">
                    <a href="?view=engagement" class="gro-tab <?php echo $view == 'engagement' ? 'active' : ''; ?>">Engagement Metrics</a>
                    <a href="?view=funnel" class="gro-tab <?php echo $view == 'funnel' ? 'active' : ''; ?>">Conversion Funnel</a>
                    <a href="?view=retention" class="gro-tab <?php echo $view == 'retention' ? 'active' : ''; ?>">Cohort Retention</a>
                    <a href="?view=content" class="gro-tab <?php echo $view == 'content' ? 'active' : ''; ?>">Content Perf</a>
                    <a href="?view=abtests" class="gro-tab <?php echo $view == 'abtests' ? 'active' : ''; ?>">A/B Test Manager</a>
                </div>

                <?php if ($view == 'engagement'): ?>
                <!-- Screen 9.1.1 — Core Engagement Metrics -->
                <div class="dashboard-grid">
                    <div class="widget w-full">
                        <div class="widget-header">
                            <h3 class="widget-title">DAU / WAU / MAU Trends (90 Days)</h3>
                        </div>
                        <canvas id="userTrendsChart" height="80"></canvas>
                    </div>

                    <div class="widget w-half">
                        <h3 class="widget-title">Bounce Rate by Page Type</h3>
                        <table class="data-table" style="margin-top: 1rem;">
                            <thead>
                                <tr>
                                    <th>Page Type</th>
                                    <th>Bounce Rate</th>
                                    <th>Change</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>AI Counselor</td>
                                    <td><strong>12.4%</strong></td>
                                    <td><i class="fas fa-caret-down" style="color: var(--success);"></i> 2%</td>
                                    <td><span class="status-badge status-approved">EXCELLENT</span></td>
                                </tr>
                                <tr>
                                    <td>College Profiles</td>
                                    <td><strong>48.2%</strong></td>
                                    <td><i class="fas fa-caret-up" style="color: var(--danger);"></i> 5%</td>
                                    <td><span class="status-badge status-pending">MODERATE</span></td>
                                </tr>
                                <tr>
                                    <td>Sitemap Listings</td>
                                    <td><strong>78.1%</strong></td>
                                    <td>-</td>
                                    <td><span class="status-badge status-rejected">POOR</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget w-half">
                        <h3 class="widget-title">Session Depth (Pages Viewed)</h3>
                        <canvas id="sessionDepthChart" height="150"></canvas>
                        <p style="text-align: center; font-size: 0.75rem; color: var(--text-secondary); margin-top: 10px;">Median: 4.8 Pages &bull; Target: > 4.0</p>
                    </div>
                </div>

                <?php elseif ($view == 'funnel'): ?>
                <!-- Screen 9.1.2 — Conversion Funnel -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Growth Funnel: Visit → Account Creation</h3>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.75rem;">Segment by Device</button>
                        </div>
                    </div>
                    
                    <div class="funnel-container">
                        <div class="funnel-step">
                            <div class="funnel-label">Total Visits</div>
                            <div class="funnel-bar" style="width: 100%;">100% (1.2M)</div>
                            <div class="funnel-drop"></div>
                        </div>
                        <div class="funnel-step">
                            <div class="funnel-label">Search Query Performed</div>
                            <div class="funnel-bar" style="width: 72%;">72% (864k)</div>
                            <div class="funnel-drop">-28%</div>
                        </div>
                        <div class="funnel-step">
                            <div class="funnel-label">College Profile Opened</div>
                            <div class="funnel-bar" style="width: 48%;">48% (576k)</div>
                            <div class="funnel-drop">-33%</div>
                        </div>
                        <div class="funnel-step">
                            <div class="funnel-label">'Enquire Now' Clicked</div>
                            <div class="funnel-bar" style="width: 12%; background: var(--warning);">12% (144k)</div>
                            <div class="funnel-drop" style="color: var(--danger); font-weight: 900;">-75% CRITICAL</div>
                        </div>
                        <div class="funnel-step">
                            <div class="funnel-label">Lead Submitted</div>
                            <div class="funnel-bar" style="width: 8%; background: var(--success);">8% (96k)</div>
                            <div class="funnel-drop">-33%</div>
                        </div>
                    </div>
                    <p style="margin-top: 2rem; font-size: 0.8rem; color: var(--text-secondary); text-align: center;">
                        <i class="fas fa-lightbulb" style="color: var(--warning);"></i> 75% drop-off at 'Enquire Now' click suggests friction in the modal or form complexity.
                    </p>
                </div>

                <?php elseif ($view == 'retention'): ?>
                <!-- Screen 9.1.3 — Cohort Retention Table -->
                <div class="widget w-full">
                    <h3 class="widget-title">Weekly Retention Cohorts (Registration Week)</h3>
                    <div style="margin-top: 1.5rem; overflow-x: auto;">
                        <table class="cohort-table">
                            <thead>
                                <tr>
                                    <th>Cohort Week</th>
                                    <th>Size</th>
                                    <th>Week 0</th>
                                    <th>Week 1</th>
                                    <th>Week 2</th>
                                    <th>Week 4</th>
                                    <th>Week 8</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Apr 01 - Apr 07</td>
                                    <td>12,402</td>
                                    <td class="cohort-high">100%</td>
                                    <td class="cohort-med">42%</td>
                                    <td class="cohort-med">31%</td>
                                    <td class="cohort-low">18%</td>
                                    <td class="cohort-low">12%</td>
                                </tr>
                                <tr>
                                    <td>Apr 08 - Apr 14</td>
                                    <td>14,180</td>
                                    <td class="cohort-high">100%</td>
                                    <td class="cohort-med">48%</td>
                                    <td class="cohort-med">35%</td>
                                    <td class="cohort-low">22%</td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>Apr 15 - Apr 21</td>
                                    <td>11,902</td>
                                    <td class="cohort-high">100%</td>
                                    <td class="cohort-high">52%</td>
                                    <td class="cohort-med">38%</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'content'): ?>
                <!-- Screen 9.2.1 — Content Performance Table -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">High-Traffic Page Performance</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Page / College</th>
                                <th>Visitors (Mo)</th>
                                <th>Avg Duration</th>
                                <th>Conv Rate (Leads)</th>
                                <th>Scroll Depth</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>/college/bits-pilani</strong></td>
                                <td>42,102</td>
                                <td>5m 12s</td>
                                <td><span style="color: var(--success); font-weight: 700;">12.4%</span></td>
                                <td>82%</td>
                                <td><span class="status-badge status-approved">TOP PERF</span></td>
                            </tr>
                            <tr>
                                <td><strong>/college/lpu-jalandhar</strong></td>
                                <td>84,102</td>
                                <td>2m 45s</td>
                                <td><span style="color: var(--warning); font-weight: 700;">4.2%</span></td>
                                <td>45%</td>
                                <td><button class="btn btn-primary" style="font-size: 0.65rem;">Optimize UX</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'abtests'): ?>
                <!-- Screen 9.2.2 — A/B Test Manager -->
                <div class="dashboard-grid">
                    <div class="widget w-two-thirds">
                        <div class="widget-header">
                            <h3 class="widget-title">Active Experiments</h3>
                            <button class="btn btn-primary" style="font-size: 0.75rem;">+ Create New Test</button>
                        </div>
                        
                        <div class="ab-test-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <div>
                                    <h4 style="color: white; font-size: 0.95rem;">CTA Button Color: Green vs Purple</h4>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">Target Page: College Profile &bull; Metric: Leads per Visit</div>
                                </div>
                                <span class="sig-badge"><i class="fas fa-check-circle"></i> 98% Significance</span>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div style="background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px;">
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Variant A (Control)</div>
                                    <div style="font-size: 1.2rem; font-weight: 700;">8.2% Conv</div>
                                </div>
                                <div style="background: rgba(16, 185, 129, 0.05); border: 1px solid var(--success); padding: 10px; border-radius: 8px;">
                                    <div style="font-size: 0.7rem; color: var(--success); font-weight: 700;">Variant B (Winner)</div>
                                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--success);">11.4% Conv (+39%)</div>
                                </div>
                            </div>
                            <button class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">Auto-Promote Variant B to 100% Traffic</button>
                        </div>
                    </div>

                    <div class="widget w-third">
                        <h3 class="widget-title">Experiment Logic</h3>
                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 1rem; line-height: 1.6;">
                            Our A/B engine uses **Bayesian Statistics** to calculate significance. We only promote winners when the "Probability of B beating A" exceeds 95%. This prevents false positives from sample noise.
                        </p>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script>
        if (document.getElementById('userTrendsChart')) {
            const ctx1 = document.getElementById('userTrendsChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: Array.from({length: 30}, (_, i) => i + 1),
                    datasets: [
                        { label: 'MAU', data: Array.from({length: 30}, () => 800000 + Math.random() * 50000), borderColor: '#6366f1', borderWidth: 2, fill: false },
                        { label: 'DAU', data: Array.from({length: 30}, () => 120000 + Math.random() * 20000), borderColor: '#10b981', borderWidth: 2, fill: false }
                    ]
                },
                options: { responsive: true, plugins: { legend: { labels: { color: '#94a3b8' } } } }
            });
        }

        if (document.getElementById('sessionDepthChart')) {
            const ctx2 = document.getElementById('sessionDepthChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: ['1 Page', '2-3', '4-5', '6-10', '10+'],
                    datasets: [{
                        label: '% of Sessions',
                        data: [15, 25, 35, 15, 10],
                        backgroundColor: '#6366f1',
                        borderRadius: 8
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });
        }
    </script>
</body>
</html>
