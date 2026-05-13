<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'queue';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q&A Moderation | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .qa-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .qa-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .qa-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .qa-item { background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; border-left: 4px solid transparent; }
        .qa-item.reported { border-left-color: var(--danger); background: rgba(239, 68, 68, 0.05); }
        .qa-item.question { border-left-color: var(--accent-primary); }
        .qa-item.answer { border-left-color: var(--accent-secondary); }
        
        .badge-request { background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .qa-type { font-size: 0.7rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; margin-bottom: 10px; display: inline-block; }
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
                        <h1 class="page-title">Q&A & Community Moderation</h1>
                        <p class="page-subtitle">Managing platform-wide questions, answers, and verified credentials.</p>
                    </div>
                </div>

                <div class="qa-tabs">
                    <a href="?view=queue" class="qa-tab <?php echo $view == 'queue' ? 'active' : ''; ?>">Moderation Queue</a>
                    <a href="?view=badges" class="qa-tab <?php echo $view == 'badges' ? 'active' : ''; ?>">Badge Verification Centre</a>
                    <a href="?view=analytics" class="qa-tab <?php echo $view == 'analytics' ? 'active' : ''; ?>">Quality Analytics</a>
                </div>

                <?php if ($view == 'queue'): ?>
                <!-- Screen 3.2.1 — Q&A Moderation Queue -->
                <div class="widget w-full">
                    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.8rem;">Pending (42)</button>
                            <button class="btn" style="background: var(--danger); color: white; font-size: 0.8rem;"><i class="fas fa-flag"></i> Reported (8)</button>
                        </div>
                        <div class="header-search" style="width: 250px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search Q&A...">
                        </div>
                    </div>

                    <!-- Reported Item -->
                    <div class="qa-item reported">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <span class="qa-type" style="background: var(--danger); color: white;">REPORTED (5 TIMES)</span>
                                <h3 style="color: white; margin-bottom: 5px;">"This college is a total scam, don't go here. They steal your money."</h3>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Asker: <strong>Student_110</strong> &bull; 2 hours ago &bull; Target: LPU Jalandhar</div>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn" style="background: var(--danger); color: white; font-size: 0.75rem;"><i class="fas fa-trash"></i> Delete Permanently</button>
                                <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.75rem;">Clear Reports</button>
                            </div>
                        </div>
                    </div>

                    <!-- New Question -->
                    <div class="qa-item question">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <span class="qa-type" style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">QUESTION PENDING</span>
                                <h3 style="color: white; margin-bottom: 5px;">"what is the fee for mba"</h3>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Asker: <strong>Anonymous</strong> &bull; 1 hour ago &bull; College: BITS Pilani</div>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-primary" style="font-size: 0.75rem;">Approve</button>
                                <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.75rem;"><i class="fas fa-edit"></i> Edit & Fix Typos</button>
                                <button class="btn" style="background: var(--danger); color: white; font-size: 0.75rem;">Reject</button>
                            </div>
                        </div>
                    </div>

                    <!-- New Answer -->
                    <div class="qa-item answer">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <span class="qa-type" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);">ANSWER PENDING</span>
                                <p style="color: white; font-size: 0.9rem; line-height: 1.5; margin-bottom: 10px;">"The fee is 5.4 lakhs per semester excluding hostel."</p>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">Answered by: <strong>Rahul S. (Claimed Alumni 2021)</strong></div>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-primary" style="font-size: 0.75rem;">Approve & Pin as Best</button>
                                <button class="btn" style="background: var(--danger); color: white; font-size: 0.75rem;">Reject</button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'badges'): ?>
                <!-- Screen 3.2.2 — Badge Verification Centre -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Badge Verification Requests</h3>
                        <span class="status-badge status-pending">8 Pending</span>
                    </div>
                    
                    <div class="badge-request">
                        <div style="display: flex; gap: 1.5rem; align-items: center;">
                            <div style="width: 60px; height: 60px; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem;">JS</div>
                            <div>
                                <div style="font-weight: 700; font-size: 1rem;">Jatin Sharma</div>
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">Role: <strong>Alumni (Batch 2021)</strong> &bull; College: IIT Delhi</div>
                                <div style="margin-top: 5px; font-size: 0.75rem;">
                                    <a href="#" style="color: var(--accent-primary); margin-right: 15px;"><i class="fas fa-file-image"></i> View Degree Certificate</a>
                                    <a href="#" style="color: var(--accent-primary);"><i class="fab fa-linkedin"></i> LinkedIn Profile</a>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Verify & Award Badge</button>
                            <button class="btn" style="background: var(--danger); color: white; font-size: 0.75rem;">Reject</button>
                        </div>
                    </div>

                    <h3 class="widget-title" style="margin-top: 3rem; margin-bottom: 1rem;">Verified Badge Holders</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>College</th>
                                <th>Verified On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight: 700;">Ananya Mishra</td>
                                <td><span class="status-badge" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);">Alumni</span></td>
                                <td>IIT Bombay</td>
                                <td>12 Jan 2026</td>
                                <td><button class="btn" style="background: var(--danger); color: white; font-size: 0.7rem; padding: 4px 8px;">Revoke Badge</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'analytics'): ?>
                <!-- Q&A Content Quality Analytics -->
                <div class="dashboard-grid">
                    <div class="widget w-full">
                        <div class="widget-header">
                            <h3 class="widget-title"><i class="fas fa-exclamation-triangle" style="color: var(--warning);"></i> Data Gap Analysis (From Student Queries)</h3>
                        </div>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.5rem;">These fields are missing in our database but were asked about by > 50 students this week.</p>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Missing Data Field</th>
                                    <th>Total Student Queries</th>
                                    <th>Top Impacted Colleges</th>
                                    <th>Priority</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-weight: 700;">Hostel Fee Breakdown</td>
                                    <td>242</td>
                                    <td style="font-size: 0.8rem;">BITS Pilani, LPU, VIT</td>
                                    <td><span class="status-badge status-rejected">URGENT</span></td>
                                    <td><button class="btn btn-primary" style="font-size: 0.7rem;">Assign Data Entry</button></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Lateral Entry Eligibility</td>
                                    <td>118</td>
                                    <td style="font-size: 0.8rem;">DTU, NSUT, Anna Univ</td>
                                    <td><span class="status-badge status-pending">MEDIUM</span></td>
                                    <td><button class="btn btn-primary" style="font-size: 0.7rem;">Assign Data Entry</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html>
