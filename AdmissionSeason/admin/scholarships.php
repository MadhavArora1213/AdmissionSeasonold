<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'list';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarships & Financial Aid | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .scholarship-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .sch-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .sch-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .rule-block { background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1rem; border-radius: 12px; margin-bottom: 10px; display: flex; align-items: center; gap: 1rem; }
        .rule-operator { background: var(--accent-primary); color: white; font-weight: 700; padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; }
        
        .test-profile-card { background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; }
        .status-pill { padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; }
        .status-pill.gov { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .status-pill.ngo { background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                
                <?php if ($view == 'list'): ?>
                <!-- Screen 2.3.1 — Scholarship CMS Table -->
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Scholarship Management</h1>
                        <p class="page-subtitle">Managing 200+ scholarships, grants, and financial aid opportunities.</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="?view=review" class="btn" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);"><i class="fas fa-university"></i> College Submissions (12)</a>
                        <button class="btn btn-primary" onclick="alert('Add Scholarship Form Modal')"><i class="fas fa-plus"></i> Add New Scholarship</button>
                    </div>
                </div>

                <div class="widget w-full">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                        <div class="header-search" style="width: 300px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search by name, provider or state...">
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn" style="font-size: 0.75rem; background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">Filter by State</button>
                            <button class="btn" style="font-size: 0.75rem; background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">Expired Archive</button>
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Scholarship Name</th>
                                <th>Provider</th>
                                <th>Amount (₹)</th>
                                <th>Scope</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--accent-primary);">Post Matric Scholarship (SC)</div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Merit-cum-Means Basis</div>
                                </td>
                                <td><span class="status-pill gov">Central Govt</span></td>
                                <td style="font-weight: 700;">Full Tuition</td>
                                <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">National</span></td>
                                <td>
                                    <div style="font-size: 0.85rem;">30 Dec 2025</div>
                                    <div style="font-size: 0.7rem; color: var(--warning);">230 days left</div>
                                </td>
                                <td><span class="status-badge status-approved">Active</span></td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <a href="?view=rules&id=1" class="action-btn" title="Rule Builder"><i class="fas fa-project-diagram"></i></a>
                                        <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: var(--accent-primary);">PM-YASASVI 2026</div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">OBC/EBC Merit</div>
                                </td>
                                <td><span class="status-pill gov">State Govt</span></td>
                                <td style="font-weight: 700;">75,000</td>
                                <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">Haryana</span></td>
                                <td>
                                    <div style="font-size: 0.85rem; color: var(--danger);">15 May 2026</div>
                                    <div style="font-size: 0.7rem; color: var(--danger);">Expires tomorrow!</div>
                                </td>
                                <td><span class="status-badge status-pending">Expiring</span></td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <a href="?view=rules&id=2" class="action-btn"><i class="fas fa-project-diagram"></i></a>
                                        <button class="action-btn"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'rules'): ?>
                <!-- Screen 2.3.2 — Eligibility Rule Builder -->
                <div class="page-header">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <a href="?view=list" class="action-btn" style="padding: 10px;"><i class="fas fa-arrow-left"></i></a>
                        <div>
                            <h1 class="page-title">Eligibility Rule Builder</h1>
                            <p class="page-subtitle">Configuring logic for: <strong>Post Matric Scholarship (SC)</strong></p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <span style="font-size: 0.8rem; color: var(--text-secondary); align-self: center;">Active Version: <strong style="color: white;">v2.1</strong></span>
                        <button class="btn btn-primary"><i class="fas fa-save"></i> Save v2.2</button>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="widget w-two-thirds">
                        <h3 class="widget-title">Visual Rule Logic</h3>
                        <div style="margin-top: 1.5rem; border-left: 2px solid var(--accent-primary); padding-left: 1.5rem; position: relative;">
                            
                            <div class="rule-block">
                                <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-primary);">IF</span>
                                <select class="btn" style="background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); font-size: 0.8rem;">
                                    <option>Caste Category</option>
                                    <option>Family Income</option>
                                    <option>12th Percentage</option>
                                </select>
                                <span class="rule-operator">IS ONE OF</span>
                                <div style="display: flex; gap: 5px;">
                                    <span class="status-badge" style="background: var(--accent-primary); color: white;">SC</span>
                                    <span class="status-badge" style="background: var(--accent-primary); color: white;">ST</span>
                                </div>
                                <button class="action-btn" style="margin-left: auto; color: var(--danger);"><i class="fas fa-trash"></i></button>
                            </div>

                            <div style="text-align: center; margin: 10px 0; font-weight: 700; color: var(--accent-primary); font-size: 0.75rem;">AND</div>

                            <div class="rule-block">
                                <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-primary);">IF</span>
                                <select class="btn" style="background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); font-size: 0.8rem;">
                                    <option>Family Income</option>
                                </select>
                                <span class="rule-operator">IS LESS THAN</span>
                                <input type="text" value="₹ 2,50,000" style="background: transparent; border: none; color: white; font-weight: 700; width: 100px;">
                                <button class="action-btn" style="margin-left: auto; color: var(--danger);"><i class="fas fa-trash"></i></button>
                            </div>

                            <div style="text-align: center; margin: 10px 0; font-weight: 700; color: var(--accent-primary); font-size: 0.75rem;">AND</div>

                            <div class="rule-block">
                                <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-primary);">IF</span>
                                <select class="btn" style="background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); font-size: 0.8rem;">
                                    <option>12th Percentage</option>
                                </select>
                                <span class="rule-operator">IS GREATER THAN</span>
                                <input type="text" value="60%" style="background: transparent; border: none; color: white; font-weight: 700; width: 50px;">
                                <button class="action-btn" style="margin-left: auto; color: var(--danger);"><i class="fas fa-trash"></i></button>
                            </div>

                            <button class="btn" style="margin-top: 1rem; background: var(--sidebar-bg); border: 1px dashed var(--border-color); color: var(--text-secondary); width: 100%;">+ Add Logical Condition</button>

                            <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                                <span style="font-size: 1rem; font-weight: 700;">THEN RESULT = </span>
                                <span class="status-badge status-approved" style="font-size: 1rem;">QUALIFIED</span>
                            </div>
                        </div>
                    </div>

                    <div class="widget w-third">
                        <h3 class="widget-title">Test Rule against Profile</h3>
                        <div class="test-profile-card" style="margin-top: 1.5rem;">
                            <div style="margin-bottom: 1rem;">
                                <label style="font-size: 0.75rem; color: var(--text-secondary);">Student Category</label>
                                <select class="btn" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; margin-top: 5px;">
                                    <option>SC</option>
                                    <option>OBC</option>
                                    <option>General</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label style="font-size: 0.75rem; color: var(--text-secondary);">Annual Income (₹)</label>
                                <input type="number" value="180000" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px; margin-top: 5px;">
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label style="font-size: 0.75rem; color: var(--text-secondary);">12th Percentage (%)</label>
                                <input type="number" value="72" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px; margin-top: 5px;">
                            </div>
                            <button class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Run Simulation</button>
                            
                            <div style="margin-top: 2rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); padding: 1rem; border-radius: 8px; text-align: center;">
                                <div style="font-size: 0.8rem; color: var(--success); font-weight: 700;"><i class="fas fa-check-circle"></i> ELIGIBLE</div>
                                <p style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 5px;">Passed all 3 criteria with 100% confidence.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'review'): ?>
                <!-- Screen 2.3.3 — College-Submitted Scholarship Review -->
                <div class="page-header">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <a href="?view=list" class="action-btn" style="padding: 10px;"><i class="fas fa-arrow-left"></i></a>
                        <div>
                            <h1 class="page-title">College Submission Queue</h1>
                            <p class="page-subtitle">Verifying 12 pending scholarships submitted by partner institutions.</p>
                        </div>
                    </div>
                </div>

                <div class="widget w-full">
                    <div class="review-item" style="background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                            <div>
                                <h3 style="color: var(--accent-primary);">Merit Scholarship for MBA (2026)</h3>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">Submitted by: <strong>LPU Jalandhar</strong> &bull; 2 hours ago</div>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-primary">Approve & Publish</button>
                                <button class="btn" style="background: var(--danger); color: white;">Reject Submission</button>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-top: 1.5rem; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px;">
                            <div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Scholarship Amount</div>
                                <div style="font-weight: 700;">₹ 1,50,000 / year</div>
                            </div>
                            <div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Total Slots</div>
                                <div style="font-weight: 700;">50 Students</div>
                            </div>
                            <div>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Verification Docs</div>
                                <div><a href="#" style="color: var(--accent-secondary); font-size: 0.8rem;"><i class="fas fa-file-pdf"></i> View Prospectus.pdf</a></div>
                            </div>
                        </div>
                        <div style="margin-top: 1.5rem;">
                            <h4 style="font-size: 0.85rem; margin-bottom: 5px;">Eligibility Criteria (College Specified)</h4>
                            <p style="font-size: 0.8rem; color: var(--text-secondary);">Student must have > 80% in Graduation and score > 90 percentile in CAT 2025. This is applicable only for first-year tuition fee.</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html>
