<?php
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DPDP Compliance Centre | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">DPDP Act Compliance Centre</h1>
                        <p class="page-subtitle">Managing statutory data privacy rights and consent obligations under DPDP Act 2023.</p>
                    </div>
                </div>

                <div class="kpi-strip">
                    <div class="kpi-card">
                        <div class="kpi-label">Pending Deletions</div>
                        <div class="kpi-value">12</div>
                        <div class="kpi-trend trend-down">Avg age: 8 days</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Export Requests</div>
                        <div class="kpi-value">5</div>
                        <div class="kpi-trend trend-up">Right to Access</div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-label">Consent Log Hash</div>
                        <div class="kpi-value" style="font-size: 0.9rem; font-family: monospace;">SHA-256 Verified</div>
                        <div style="font-size: 0.6rem; color: var(--success);"><i class="fas fa-lock"></i> Immutable Ledger Active</div>
                    </div>
                    <div class="kpi-card" style="border-left: 4px solid var(--danger);">
                        <div class="kpi-label">Minor Account Flag</div>
                        <div class="kpi-value" style="color: var(--danger);">82</div>
                        <div style="font-size: 0.7rem; color: var(--text-secondary);">Guardian Consent Needed</div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <!-- Data Deletion Queue -->
                    <div class="widget w-full">
                        <div class="widget-header">
                            <h3 class="widget-title">Statutory Data Deletion Queue (Right to Erasure)</h3>
                            <span class="status-badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); font-size: 0.75rem;">Statutory Deadline: 30 Days</span>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Request Date</th>
                                    <th>Statutory Deadline</th>
                                    <th>Days Remaining</th>
                                    <th>Scope</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="border-left: 4px solid var(--danger);">
                                    <td>
                                        <div style="font-weight: 700;">Anand K.</div>
                                        <div style="font-size: 0.7rem; color: var(--text-secondary);">Request via: In-App Form</div>
                                    </td>
                                    <td>14 Apr 2026</td>
                                    <td>13 May 2026</td>
                                    <td><span style="color: var(--danger); font-weight: 700;">DUE TODAY</span></td>
                                    <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">Full Erasure</span></td>
                                    <td>
                                        <button class="btn" style="background: var(--danger); color: white; font-size: 0.7rem;" onclick="confirmDeletion('Anand K.')">Process Deletion</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700;">Meera Shah</div>
                                        <div style="font-size: 0.7rem; color: var(--text-secondary);">Request via: Support Email</div>
                                    </td>
                                    <td>02 May 2026</td>
                                    <td>31 May 2026</td>
                                    <td><span style="color: var(--warning); font-weight: 700;">18 Days</span></td>
                                    <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">Anonymization</span></td>
                                    <td>
                                        <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.7rem;">Process Deletion</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Right to Access (Export) -->
                    <div class="widget w-half">
                        <h3 class="widget-title">Data Export Requests (Right to Access)</h3>
                        <div style="margin-top: 1.5rem;">
                            <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 0.85rem; font-weight: 700;">Request #EXP_921</div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">User: rahul.s@gmail.com</div>
                                </div>
                                <button class="btn btn-primary" style="font-size: 0.7rem;"><i class="fas fa-file-export"></i> Generate JSON</button>
                            </div>
                        </div>
                    </div>

                    <!-- Consent Audit Log -->
                    <div class="widget w-half">
                        <h3 class="widget-title">Consent Audit Log (Immutable)</h3>
                        <div style="margin-top: 1.5rem; font-size: 0.75rem; color: var(--text-secondary); max-height: 200px; overflow-y: auto;">
                            <div style="padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                                <span style="color: var(--success); font-family: monospace;">[13-05-26 08:12]</span> User #8219 accepted Terms v4.2 &bull; IP: 103.21.12.XX
                            </div>
                            <div style="padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                                <span style="color: var(--success); font-family: monospace;">[13-05-26 07:45]</span> User #8240 accepted Terms v4.2 &bull; IP: 122.14.82.XX
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Process Deletion Modal -->
    <div id="deletion-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; align-items: center; justify-content: center;">
        <div class="widget" style="width: 450px; background: var(--sidebar-bg); border: 2px solid var(--danger);">
            <div class="widget-header">
                <h3 class="widget-title" style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> STATUTORY DELETION</h3>
                <button class="action-btn" onclick="document.getElementById('deletion-modal').style.display='none'"><i class="fas fa-times"></i></button>
            </div>
            <div style="padding: 1.5rem;">
                <p style="font-size: 0.9rem; margin-bottom: 1rem;">Process data erasure for <strong id="subject-name"></strong>?</p>
                <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; font-size: 0.8rem; margin-bottom: 1.5rem;">
                    <ul style="list-style: none;">
                        <li><i class="fas fa-check-circle" style="color: var(--success);"></i> Anonymize PostgreSQL PII fields</li>
                        <li><i class="fas fa-check-circle" style="color: var(--success);"></i> Hard-delete Cloudflare R2 documents</li>
                        <li><i class="fas fa-check-circle" style="color: var(--success);"></i> Send Brevo Confirmation Email</li>
                        <li><i class="fas fa-check-circle" style="color: var(--success);"></i> Log completion in Audit Trail</li>
                    </ul>
                </div>
                <button class="btn" style="width: 100%; background: var(--danger); color: white; font-weight: 700;">PERFORM STATUTORY ERASURE</button>
            </div>
        </div>
    </div>

    <script>
        function confirmDeletion(name) {
            document.getElementById('subject-name').innerText = name;
            document.getElementById('deletion-modal').style.display = 'flex';
        }
    </script>
</body>
</html>
