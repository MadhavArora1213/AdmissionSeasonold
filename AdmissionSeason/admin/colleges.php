<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'list';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Management | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .college-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .coll-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .coll-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .kanban-board { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; overflow-x: auto; padding-bottom: 1rem; }
        .kanban-col { background: rgba(0,0,0,0.2); border-radius: 12px; min-height: 500px; padding: 10px; }
        .kanban-header { font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 1rem; padding: 5px 10px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; }
        .kanban-card { background: var(--sidebar-bg); border: 1px solid var(--border-color); padding: 1rem; border-radius: 8px; margin-bottom: 10px; cursor: grab; }
        .kanban-card:active { cursor: grabbing; }
        
        .diff-view { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px; padding: 10px; border-bottom: 1px solid var(--border-color); }
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
                        <h1 class="page-title">College & Course Management</h1>
                        <p class="page-subtitle">Managing 30,000+ institution profiles and verification pipelines.</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="?view=verify" class="btn" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fas fa-shield-alt"></i> Verification Pipeline</a>
                        <button class="btn btn-primary"><i class="fas fa-plus"></i> Add College</button>
                    </div>
                </div>

                <div class="college-tabs">
                    <a href="?view=list" class="coll-tab <?php echo $view == 'list' ? 'active' : ''; ?>">Active Colleges</a>
                    <a href="?view=verify" class="coll-tab <?php echo $view == 'verify' ? 'active' : ''; ?>">Verification Kanban</a>
                    <a href="?view=diff" class="coll-tab <?php echo $view == 'diff' ? 'active' : ''; ?>">Data Diff Viewer</a>
                    <a href="?view=duplicates" class="coll-tab <?php echo $view == 'duplicates' ? 'active' : ''; ?>">Duplicate Detection</a>
                </div>

                <?php if ($view == 'list'): ?>
                <!-- Screen 2.1.1 — College CMS Table -->
                <div class="widget w-full">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                        <div class="header-search" style="width: 300px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search by name, city or type...">
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <select class="btn" style="background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); font-size: 0.8rem;">
                                <option>All States</option>
                                <option>Karnataka</option>
                                <option>Maharashtra</option>
                            </select>
                            <button class="btn" style="font-size: 0.75rem; background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">Filter by Type</button>
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>College Name</th>
                                <th>Location</th>
                                <th>NIRF Rank</th>
                                <th>Admissions</th>
                                <th>Data Freshness</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--accent-primary);">BITS Pilani</div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Deemed University</div>
                                </td>
                                <td>Pilani, Rajasthan</td>
                                <td><span style="font-weight: 700; color: var(--accent-secondary);">#25</span></td>
                                <td><span class="status-badge status-approved">OPEN</span></td>
                                <td><span style="color: var(--success); font-size: 0.8rem;"><i class="fas fa-check-circle"></i> 2 days ago</span></td>
                                <td><span class="status-badge status-approved">Verified</span></td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <button class="action-btn"><i class="fas fa-edit"></i></button>
                                        <button class="action-btn"><i class="fas fa-eye"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'verify'): ?>
                <!-- Screen 2.1.3 — Verification Kanban -->
                <div class="kanban-board">
                    <div class="kanban-col">
                        <div class="kanban-header">Submitted <span>4</span></div>
                        <div class="kanban-card">
                            <div style="font-weight: 700; font-size: 0.85rem;">Galgotias University</div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 5px;">Source: College Dashboard</div>
                            <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <span class="status-badge" style="background: rgba(255,255,255,0.05); font-size: 0.6rem;">MBA, B.Tech</span>
                                <i class="fas fa-paperclip text-secondary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="kanban-col">
                        <div class="kanban-header">In Review <span>2</span></div>
                        <div class="kanban-card" style="border-left: 3px solid var(--warning);">
                            <div style="font-weight: 700; font-size: 0.85rem;">Amity University</div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 5px;">Checking Accreditation Docs</div>
                        </div>
                    </div>
                    <div class="kanban-col">
                        <div class="kanban-header">Clarification <span>1</span></div>
                    </div>
                    <div class="kanban-col">
                        <div class="kanban-header">Approved <span>420</span></div>
                    </div>
                </div>

                <?php elseif ($view == 'diff'): ?>
                <!-- Screen 2.1.4 — Data Diff Viewer -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Conflict Resolver (Scraped vs Submitted)</h3>
                        <span class="status-badge status-pending">8 Conflicts Detected</span>
                    </div>
                    
                    <div style="background: rgba(30, 41, 59, 0.4); padding: 1.5rem; border-radius: 12px; margin-top: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <h4 style="color: white;">College: IIT Madras</h4>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-primary" style="font-size: 0.75rem;">Trust Submitted (Keep New)</button>
                                <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.75rem;">Trust Scraper</button>
                            </div>
                        </div>
                        <div class="diff-view" style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">
                            <div>Field</div>
                            <div>Current (Live)</div>
                            <div>Proposed (New)</div>
                        </div>
                        <div class="diff-view">
                            <div style="font-weight: 700;">Total B.Tech Seats</div>
                            <div class="diff-old">1,120</div>
                            <div class="diff-new">1,240</div>
                        </div>
                        <div class="diff-view">
                            <div style="font-weight: 700;">Average Placement</div>
                            <div class="diff-old">₹ 18.2 LPA</div>
                            <div class="diff-new">₹ 21.4 LPA</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html>
