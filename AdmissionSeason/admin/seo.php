<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'bulk';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Control Centre | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .seo-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; overflow-x: auto; padding-bottom: 5px; }
        .seo-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; white-space: nowrap; }
        .seo-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .meta-cell { 
            cursor: pointer; 
            transition: 0.2s; 
            border: 1px solid transparent; 
            padding: 12px; 
            border-radius: 8px; 
            white-space: normal; 
            word-break: break-word; 
            line-height: 1.6; 
        }
        .meta-cell:hover { border-color: var(--accent-primary); background: rgba(99, 102, 241, 0.05); }
        .char-over { 
            background: rgba(239, 68, 68, 0.05); 
            color: var(--danger); 
            font-weight: 700; 
            border-color: rgba(239, 68, 68, 0.3) !important; 
        }
        
        .cwv-card { background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 12px; border-top: 4px solid var(--accent-primary); }
        .cwv-good { color: var(--success); }
        .cwv-need-improvement { color: var(--warning); }
        .cwv-poor { color: var(--danger); }
        
        .link-bubble { display: inline-block; padding: 4px 8px; background: rgba(255,255,255,0.05); border-radius: 15px; font-size: 0.7rem; margin-right: 5px; margin-bottom: 5px; border: 1px solid var(--border-color); }
        
        .bulk-table { table-layout: fixed; width: 100%; }
        .bulk-table th:nth-child(1) { width: 40px; }
        .bulk-table th:nth-child(2) { width: 15%; }
        .bulk-table th:nth-child(3) { width: 25%; }
        .bulk-table th:nth-child(4) { width: 45%; }
        .bulk-table th:nth-child(5) { width: 100px; }
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
                        <h1 class="page-title">SEO Control Centre</h1>
                        <p class="page-subtitle">Command centre for 90,000+ programmatic pages and search performance optimization.</p>
                    </div>
                </div>

                <div class="seo-tabs">
                    <a href="?view=bulk" class="seo-tab <?php echo $view == 'bulk' ? 'active' : ''; ?>">Bulk Meta Editor</a>
                    <a href="?view=health" class="seo-tab <?php echo $view == 'health' ? 'active' : ''; ?>">Index Health</a>
                    <a href="?view=sitemap" class="seo-tab <?php echo $view == 'sitemap' ? 'active' : ''; ?>">Sitemap Manager</a>
                    <a href="?view=cwv" class="seo-tab <?php echo $view == 'cwv' ? 'active' : ''; ?>">Core Web Vitals</a>
                    <a href="?view=schema" class="seo-tab <?php echo $view == 'schema' ? 'active' : ''; ?>">Schema Validator</a>
                    <a href="?view=links" class="seo-tab <?php echo $view == 'links' ? 'active' : ''; ?>">Internal Links</a>
                    <a href="?view=redirects" class="seo-tab <?php echo $view == 'redirects' ? 'active' : ''; ?>">301 Redirects</a>
                    <a href="?view=gaps" class="seo-tab <?php echo $view == 'gaps' ? 'active' : ''; ?>">Content Gaps</a>
                </div>

                <?php if ($view == 'bulk'): ?>
                <!-- Screen 7.1.1 — Metadata Bulk Editor -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <div style="display: flex; gap: 10px;">
                            <select class="btn btn-secondary" style="font-size: 0.8rem;">
                                <option>College Profile Pages</option>
                                <option>Exam Detail Pages</option>
                                <option>Course Combo Pages</option>
                            </select>
                            <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleSEOAction('bulk_apply', 'selected_pages')">Apply Template to Selected</button>
                        </div>
                        <div class="header-search" style="width: 250px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search URLs...">
                        </div>
                    </div>
                    <table class="data-table bulk-table">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox"></th>
                                <th>Page URL / Type</th>
                                <th>Meta Title (Max 60)</th>
                                <th>Meta Description (Max 160)</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>
                                    <div style="font-size: 0.8rem; font-weight: 700;">/college/bits-pilani</div>
                                    <div style="font-size: 0.65rem; color: var(--text-secondary);">College Profile</div>
                                </td>
                                <td><div class="meta-cell">BITS Pilani Admissions 2026 — Fees, Placements & Ranking</div></td>
                                <td><div class="meta-cell">Explore BITS Pilani admission process, fee structure for B.Tech, placement records, and NIRF rankings for 2026. Apply now!</div></td>
                                <td style="font-size: 0.75rem;">2 days ago</td>
                            </tr>
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>
                                    <div style="font-size: 0.8rem; font-weight: 700;">/exam/jee-main</div>
                                    <div style="font-size: 0.65rem; color: var(--text-secondary);">Exam Page</div>
                                </td>
                                <td><div class="meta-cell char-over">JEE Main 2026 Exam Dates, Syllabus, Pattern and Previous Year Question Papers with Solutions PDF Download</div></td>
                                <td><div class="meta-cell">Complete guide to JEE Main 2026...</div></td>
                                <td style="font-size: 0.75rem;">1 week ago</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'health'): ?>
                <!-- Screen 7.1.2 — Programmatic Page Health Monitor -->
                <div class="dashboard-grid">
                    <div class="widget w-third glass-card">
                        <h3 class="widget-title">Indexing Status (GSC)</h3>
                        <canvas id="indexStatusChart" height="250"></canvas>
                    </div>
                    <div class="widget w-two-thirds glass-card">
                        <h3 class="widget-title">Top Indexing Issues (by Impact)</h3>
                        <table class="data-table" style="margin-top: 1rem;">
                            <thead>
                                <tr>
                                    <th>Status Issue</th>
                                    <th>Affected URLs</th>
                                    <th>Traffic Loss Est.</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span style="color: var(--danger);">404 Not Found</span></td>
                                    <td>1,242</td>
                                    <td>8,200 visits/mo</td>
                                    <td><button class="btn btn-primary" style="font-size: 0.7rem;" onclick="handleSEOAction('fix_404', '1,242 URLs')">Fix & Redirect</button></td>
                                </tr>
                                <tr>
                                    <td>Discovered - Not Crawled</td>
                                    <td>18,402</td>
                                    <td>42,000 visits/mo</td>
                                    <td><button class="btn btn-primary" style="font-size: 0.7rem;" onclick="handleSEOAction('submit_api', '18,402 URLs')">Submit to API</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'cwv'): ?>
                <!-- Screen 7.2.1 — Core Web Vitals Tracker -->
                <div class="dashboard-grid">
                    <div class="widget w-full glass-card">
                        <div class="widget-header">
                            <h3 class="widget-title">Google Field Data (Last 28 Days)</h3>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 1.5rem;">
                            <div class="cwv-card">
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Largest Contentful Paint (LCP)</div>
                                <div style="font-size: 2rem; font-weight: 700;" class="cwv-good">1.8s</div>
                                <div style="font-size: 0.7rem; color: var(--success);">Target < 2.5s</div>
                            </div>
                            <div class="cwv-card">
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Interaction to Next Paint (INP)</div>
                                <div style="font-size: 2rem; font-weight: 700;" class="cwv-need-improvement">242ms</div>
                                <div style="font-size: 0.7rem; color: var(--warning);">Needs Improvement (> 200ms)</div>
                            </div>
                            <div class="cwv-card">
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Cumulative Layout Shift (CLS)</div>
                                <div style="font-size: 2rem; font-weight: 700;" class="cwv-good">0.04</div>
                                <div style="font-size: 0.7rem; color: var(--success);">Target < 0.1</div>
                            </div>
                        </div>
                    </div>
                    <div class="widget w-full glass-card">
                        <h3 class="widget-title">Pages Failing CWV Thresholds</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Page Type</th>
                                    <th>P90 LCP</th>
                                    <th>P90 INP</th>
                                    <th>P90 CLS</th>
                                    <th>Trend</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>College Profile (/college/*)</td>
                                    <td class="cwv-good">2.1s</td>
                                    <td class="cwv-need-improvement">280ms</td>
                                    <td class="cwv-good">0.02</td>
                                    <td><i class="fas fa-arrow-up" style="color: var(--success);"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'schema'): ?>
                <!-- Screen 7.2.2 — Structured Data Validator -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <h3 class="widget-title">Live Schema Validator</h3>
                    </div>
                    <div style="display: flex; gap: 10px; margin-bottom: 2rem;">
                        <input type="text" id="schema-url" placeholder="Enter Page URL (e.g., https://edusearch.com/college/iit-delhi)" style="flex: 1; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px;">
                        <button class="btn btn-primary" onclick="handleSEOAction('validate_schema', document.getElementById('schema-url').value)">Validate JSON-LD</button>
                    </div>
                    
                    <div style="background: rgba(16, 185, 129, 0.05); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--success);">
                        <h4 style="color: var(--success); margin-bottom: 1rem;"><i class="fas fa-check-circle"></i> EducationalOrganization Schema Detected</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                            <div style="font-family: monospace; font-size: 0.75rem; color: var(--text-secondary);">
                                {<br>
                                &nbsp;&nbsp;"@context": "https://schema.org",<br>
                                &nbsp;&nbsp;"@type": "EducationalOrganization",<br>
                                &nbsp;&nbsp;"name": "IIT Delhi",<br>
                                &nbsp;&nbsp;"address": { ... }<br>
                                }
                            </div>
                            <div style="font-size: 0.8rem;">
                                <div style="margin-bottom: 10px;"><i class="fas fa-check" style="color: var(--success);"></i> Required property 'name' present</div>
                                <div style="margin-bottom: 10px;"><i class="fas fa-check" style="color: var(--success);"></i> Required property 'address' present</div>
                                <div style="color: var(--warning);"><i class="fas fa-exclamation-triangle"></i> Optional property 'description' missing</div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'links'): ?>
                <!-- Screen 7.2.3 — Internal Link Manager -->
                <div class="dashboard-grid">
                    <div class="widget w-half glass-card">
                        <h3 class="widget-title">Link Opportunity Finder</h3>
                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1.5rem;">Suggesting internal links for SEO authority distribution.</p>
                        <div class="activity-item">
                            <div style="font-weight: 700;">Target: /college/bits-pilani</div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 5px;">Suggesting links from:</div>
                            <div style="margin-top: 5px;">
                                <span class="link-bubble">Engineering Colleges in Rajasthan</span>
                                <span class="link-bubble">Private Colleges Ranking</span>
                            </div>
                        </div>
                    </div>
                    <div class="widget w-half glass-card">
                        <h3 class="widget-title">Orphan Page Detector</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Orphan URL</th>
                                    <th>Days Since Creation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>/college/new-private-institute-821</td>
                                    <td>14 Days</td>
                                    <td><button class="btn btn-primary" style="font-size: 0.65rem;" onclick="handleSEOAction('add_links', '/college/new-private-institute-821')">Add Internal Links</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'gaps'): ?>
                <!-- Screen 7.2.5 — Content Gap Finder -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <h3 class="widget-title">High-Impression Low-Click Keywords (GSC)</h3>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Search Query</th>
                                <th>Impressions</th>
                                <th>Clicks</th>
                                <th>CTR</th>
                                <th>Avg Position</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>"top private engineering colleges in delhi"</strong></td>
                                <td>142,802</td>
                                <td>2,410</td>
                                <td>1.68%</td>
                                <td style="color: var(--warning); font-weight: 700;">#12.4</td>
                                <td><button class="btn btn-primary" style="font-size: 0.7rem;" onclick="handleSEOAction('optimize', 'Delhi Private Colleges')">Optimize Page Content</button></td>
                            </tr>
                            <tr>
                                <td><strong>"bits pilani average package cse"</strong></td>
                                <td>42,100</td>
                                <td>812</td>
                                <td>1.92%</td>
                                <td style="color: var(--warning); font-weight: 700;">#8.2</td>
                                <td><button class="btn btn-primary" style="font-size: 0.7rem;" onclick="handleSEOAction('optimize', 'BITS Placements')">Add Placement Table</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script>
        if (document.getElementById('indexStatusChart')) {
            const ctx = document.getElementById('indexStatusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Indexed', 'Crawled (Not Indexed)', 'Discovered (Not Crawled)', 'Excluded'],
                    datasets: [{
                        data: [64000, 12000, 18000, 4000],
                        backgroundColor: ['#10b981', '#f59e0b', '#6366f1', '#475569'],
                        borderWidth: 0
                    }]
                },
                options: { cutout: '70%', plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', boxWidth: 12 } } } }
            });
        }
        async function handleSEOAction(action, target) {
            let config = {
                title: 'SEO Action',
                text: '',
                icon: 'info',
                showConfirmButton: false,
                timer: 2000,
                background: 'rgba(15, 23, 42, 0.95)',
                color: '#fff',
                backdrop: `rgba(0,0,0,0.4) blur(4px)`
            };

            if (action === 'bulk_apply') { config.text = 'Applying SEO template to ' + target + '...'; config.icon = 'success'; }
            else if (action === 'fix_404') { config.text = 'Initializing bulk redirect rules for ' + target + '...'; config.icon = 'warning'; }
            else if (action === 'submit_api') { config.text = 'Pushing ' + target + ' to Google Indexing API...'; config.icon = 'success'; }
            else if (action === 'validate_schema') { config.text = 'Fetching live DOM and validating JSON-LD for ' + target + '...'; config.icon = 'info'; }
            else if (action === 'add_links') { config.text = 'Generating internal link suggestions for ' + target + '...'; config.icon = 'info'; }
            else if (action === 'optimize') config.text = 'Opening content optimization workspace for ' + target + '...';
            
            Swal.fire(config);

            // Backend Integration
            /*
            try {
                const response = await fetch('/api/admin/seo', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, target })
                });
                const result = await response.json();
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Success', text: result.message, timer: 1500 });
                }
            } catch (error) {
                console.error("Backend Error:", error);
            }
            */
        }
    </script>
</body>
</html>
