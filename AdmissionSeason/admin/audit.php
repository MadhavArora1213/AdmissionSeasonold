<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'full';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Audit Log | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .aud-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .aud-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .aud-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .audit-json { background: #0f172a; padding: 10px; border-radius: 4px; font-family: 'Fira Code', monospace; font-size: 0.7rem; color: #94a3b8; display: none; margin-top: 10px; }
        .audit-row:hover .audit-json { display: block; }
        
        .rbac-alert { background: rgba(239, 68, 68, 0.05); border: 1px solid var(--danger); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem; }
        .immutability-badge { background: rgba(16, 185, 129, 0.1); color: var(--success); font-size: 0.7rem; font-weight: 700; padding: 4px 10px; border-radius: 20px; border: 1px solid var(--success); }
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
                        <h1 class="page-title">Compliance & Audit Ledger</h1>
                        <p class="page-subtitle">Immutable accountability trail for all administrative actions and DPDP compliance verification.</p>
                    </div>
                    <div class="immutability-badge"><i class="fas fa-lock"></i> PostgreSQL Append-Only Ledger Active</div>
                </div>

                <div class="aud-tabs">
                    <a href="?view=full" class="aud-tab <?php echo $view == 'full' ? 'active' : ''; ?>">Complete Audit Log</a>
                    <a href="?view=access" class="aud-tab <?php echo $view == 'access' ? 'active' : ''; ?>">Access Change Log</a>
                    <a href="?view=dpdp" class="aud-tab <?php echo $view == 'dpdp' ? 'active' : ''; ?>">DPDP Compliance Logs</a>
                </div>

                <?php if ($view == 'full'): ?>
                <!-- Screen 12.1.1 — Audit Log Table -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <div style="display: flex; gap: 10px;">
                            <select class="btn" style="background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); font-size: 0.8rem;">
                                <option>All Actions</option>
                                <option>College Updates</option>
                                <option>User Deletions</option>
                                <option>Lead Disputes</option>
                                <option>System Config</option>
                            </select>
                            <input type="date" class="btn" style="background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); font-size: 0.8rem;">
                        </div>
                        <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.75rem;"><i class="fas fa-file-csv"></i> Export for Audit</button>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Admin User</th>
                                <th>Action Type</th>
                                <th>Entity (ID)</th>
                                <th>IP Address</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="audit-row">
                                <td style="font-size: 0.75rem;">13 May 2026, 12:42:10</td>
                                <td><strong>Ankit Verma</strong></td>
                                <td><span class="status-badge" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);">VERIFY_COLLEGE</span></td>
                                <td>College (#COL_821)</td>
                                <td>103.21.12.XX</td>
                                <td>
                                    <div style="font-size: 0.75rem;">Approved VIT Vellore verification.</div>
                                    <div class="audit-json">
                                        { "old": { "status": "pending" }, "new": { "status": "verified", "verified_by": 102 } }
                                    </div>
                                </td>
                            </tr>
                            <tr class="audit-row">
                                <td style="font-size: 0.75rem;">13 May 2026, 11:15:42</td>
                                <td><strong>Pooja Singh</strong></td>
                                <td><span class="status-badge status-approved">APPROVE_REVIEW</span></td>
                                <td>Review (#REV_1402)</td>
                                <td>122.14.82.XX</td>
                                <td>
                                    <div style="font-size: 0.75rem;">Moderated BITS Pilani review.</div>
                                    <div class="audit-json">
                                        { "action": "approved", "moderator_note": "Verified alumni via LinkedIn" }
                                    </div>
                                </td>
                            </tr>
                            <tr class="audit-row" style="border-left: 4px solid var(--danger);">
                                <td style="font-size: 0.75rem;">13 May 2026, 09:30:00</td>
                                <td><strong>SYSTEM_CRON</strong></td>
                                <td><span class="status-badge status-rejected">DPDP_ERASURE</span></td>
                                <td>Student (#STU_9210)</td>
                                <td>Server</td>
                                <td>
                                    <div style="font-size: 0.75rem;">Processed statutory erasure request.</div>
                                    <div class="audit-json">
                                        { "mode": "hard_delete", "pii_cleared": ["email", "phone", "name"], "files_removed": 4 }
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'access'): ?>
                <!-- Screen 12.1.2 — Admin Access Change Log -->
                <div class="widget w-full">
                    <div class="rbac-alert">
                        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: var(--danger);"></i>
                        <div>
                            <div style="font-weight: 700; color: white;">Privilege Escalation Monitoring Active</div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">Every role change triggers an instant Brevo notification to Super Admins.</div>
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Changed By</th>
                                <th>Target Admin</th>
                                <th>Old Role</th>
                                <th>New Role</th>
                                <th>IP Address</th>
                                <th>Approval</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-size: 0.75rem;">12 May 2026, 14:00</td>
                                <td><strong>Ankit Verma</strong></td>
                                <td>Pooja Singh</td>
                                <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">Viewer</span></td>
                                <td><span class="status-badge" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">Content Mod</span></td>
                                <td>103.21.12.XX</td>
                                <td><span class="status-badge status-approved">Auto-Approved</span></td>
                            </tr>
                            <tr style="background: rgba(239, 68, 68, 0.05);">
                                <td style="font-size: 0.75rem;">10 May 2026, 09:30</td>
                                <td><strong>Ankit Verma</strong></td>
                                <td>Finance Lead</td>
                                <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">None</span></td>
                                <td><span class="status-badge status-rejected">Finance Manager</span></td>
                                <td>103.21.12.XX</td>
                                <td><span class="status-badge status-pending">Pending 2nd Admin Approval</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'dpdp'): ?>
                <!-- DPDP Compliance Ledger -->
                <div class="widget w-full">
                    <h3 class="widget-title">Statutory Data Erasure Proof-of-Completion</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Processed On</th>
                                <th>Statutory Deadline</th>
                                <th>Compliance Gap</th>
                                <th>Admin Evidence</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#DEL_9210</td>
                                <td>13 May 2026</td>
                                <td>24 May 2026</td>
                                <td><span style="color: var(--success); font-weight: 700;">-11 Days (Ahead)</span></td>
                                <td><button class="btn" style="font-size: 0.65rem; background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">View Full JSON Audit</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html>
