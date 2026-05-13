<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'status';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup & Disaster Recovery | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bak-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .bak-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .bak-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .restore-gate { background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 1rem; position: relative; }
        .restore-gate.locked { opacity: 0.5; filter: grayscale(1); pointer-events: none; }
        .gate-number { position: absolute; top: -10px; left: -10px; width: 25px; height: 25px; background: var(--accent-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; }
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
                        <h1 class="page-title">Backup & Disaster Recovery</h1>
                        <p class="page-subtitle">Zero-data-loss protection with automated verification and WAL streaming.</p>
                    </div>
                </div>

                <div class="kpi-strip">
                    <div class="kpi-card">
                        <div class="kpi-label">RPO Target</div>
                        <div class="kpi-value">< 5 mins</div>
                        <div class="kpi-trend trend-up">WAL Streaming Active</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">RTO Target</div>
                        <div class="kpi-value">< 2 hours</div>
                        <div class="kpi-trend trend-up">Procedure Verified</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Retention Policy</div>
                        <div class="kpi-value">7D/4W/6M</div>
                        <div style="font-size: 0.7rem; color: var(--text-secondary);">R2 Lifecycle Active</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Monthly DR Test</div>
                        <div class="kpi-value" style="color: var(--success);">PASSED</div>
                        <div style="font-size: 0.7rem; color: var(--text-secondary);">Last Test: May 02, 2026</div>
                    </div>
                </div>

                <div class="bak-tabs">
                    <a href="?view=status" class="bak-tab <?php echo $view == 'status' ? 'active' : ''; ?>">Backup Status</a>
                    <a href="?view=restore" class="bak-tab <?php echo $view == 'restore' ? 'active' : ''; ?>">Restore Procedure</a>
                    <a href="?view=dr_log" class="bak-tab <?php echo $view == 'dr_log' ? 'active' : ''; ?>">DR Test Log</a>
                </div>

                <?php if ($view == 'status'): ?>
                <!-- Screen 6.3.1 — Backup Status Dashboard -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">PostgreSQL Backup History (Cloudflare R2)</h3>
                        <button class="btn btn-primary" style="font-size: 0.75rem;"><i class="fas fa-database"></i> Take Manual Backup (pg_dump)</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Backup Timestamp</th>
                                <th>Type</th>
                                <th>File Size</th>
                                <th>Status</th>
                                <th>Verification Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>13 May 2026, 08:00 AM</strong></td>
                                <td>Daily Automated</td>
                                <td>4.24 GB</td>
                                <td><span class="status-badge status-approved">Success</span></td>
                                <td><span style="color: var(--success); font-weight: 700;"><i class="fas fa-check-double"></i> Verified Restore</span></td>
                                <td><button class="action-btn"><i class="fas fa-download"></i></button></td>
                            </tr>
                            <tr style="background: rgba(239, 68, 68, 0.05);">
                                <td><strong>12 May 2026, 08:00 AM</strong></td>
                                <td>Daily Automated</td>
                                <td>4.18 GB</td>
                                <td><span class="status-badge status-approved">Success</span></td>
                                <td><span style="color: var(--warning); font-weight: 700;"><i class="fas fa-exclamation-triangle"></i> Pending Test</span></td>
                                <td><button class="action-btn"><i class="fas fa-vial"></i> Verify Now</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'restore'): ?>
                <!-- Screen 6.3.2 — Restore Procedure (Triple Gated) -->
                <div class="widget w-full" style="border: 2px solid var(--danger);">
                    <div class="widget-header">
                        <h3 class="widget-title" style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> HIGH-STAKES RESTORE CONSOLE</h3>
                    </div>
                    
                    <div style="padding: 1.5rem;">
                        <div class="restore-gate">
                            <div class="gate-number">1</div>
                            <h4 style="color: white; margin-bottom: 10px;">Select & Stage Recovery Instance</h4>
                            <div style="display: flex; gap: 1rem; align-items: center;">
                                <select class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; flex: 1;">
                                    <option>Select Backup: 13 May 2026 (4.24 GB)</option>
                                </select>
                                <button class="btn btn-primary">Restore to Staging</button>
                            </div>
                            <p style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 10px;">Safe operation: Spins up a shadow PostgreSQL instance to verify data integrity before production promotion.</p>
                        </div>

                        <div class="restore-gate locked">
                            <div class="gate-number">2</div>
                            <h4 style="color: white; margin-bottom: 10px;">Validation & Health Check</h4>
                            <p style="font-size: 0.8rem;">Staging restore in progress... Waiting for basic validation queries to pass.</p>
                        </div>

                        <div class="restore-gate locked">
                            <div class="gate-number">3</div>
                            <h4 style="color: white; margin-bottom: 10px;">Promote to Production</h4>
                            <p style="font-size: 0.8rem; color: var(--danger); font-weight: 700;">WARNING: This will overwrite the live production database.</p>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'dr_log'): ?>
                <!-- Monthly DR Test Log -->
                <div class="widget w-full">
                    <h3 class="widget-title">Statutory Disaster Recovery Audit Trail</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Test Date</th>
                                <th>Scenario</th>
                                <th>Tested By</th>
                                <th>Time Taken</th>
                                <th>Result</th>
                                <th>Audit Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>02 May 2026</td>
                                <td>Full VPS Failure Restore</td>
                                <td>Ankit V. (Super Admin)</td>
                                <td>82 mins</td>
                                <td><span class="status-badge status-approved">PASS</span></td>
                                <td style="font-size: 0.75rem;">Verified point-in-time recovery to within 5 mins of failure.</td>
                            </tr>
                            <tr>
                                <td>04 Apr 2026</td>
                                <td>Partial Table Corruption</td>
                                <td>Ankit V. (Super Admin)</td>
                                <td>14 mins</td>
                                <td><span class="status-badge status-approved">PASS</span></td>
                                <td style="font-size: 0.75rem;">Recovered 'colleges' table from WAL log stream successfully.</td>
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
