<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'abuse';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security & Access Control | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sec-tabs { 
            display: flex; 
            gap: 1rem; 
            border-bottom: 1px solid var(--border-color); 
            margin-bottom: 2rem; 
            overflow-x: auto; 
            scrollbar-width: none; 
            -ms-overflow-style: none; 
        }
        .sec-tabs::-webkit-scrollbar { display: none; }
        .sec-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; white-space: nowrap; }
        .sec-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        @media (max-width: 768px) {
            .session-card { flex-direction: column; align-items: flex-start; gap: 15px; }
            .session-card > .btn { width: 100%; }
        }
        
        .matrix-table th, .matrix-table td { text-align: center; padding: 12px; border: 1px solid var(--border-color); }
        .matrix-table td i.fa-check { color: var(--success); }
        .matrix-table td i.fa-times { color: var(--danger); opacity: 0.3; }
        
        .session-card { background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1rem; border-radius: 12px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; }
        .anomaly-badge { background: rgba(239, 68, 68, 0.1); color: var(--danger); font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
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
                        <h1 class="page-title">Security & Access Control</h1>
                        <p class="page-subtitle">Protecting platform integrity with real-time abuse monitoring and granular RBAC.</p>
                    </div>
                </div>

                <div class="sec-tabs">
                    <a href="?view=abuse" class="sec-tab <?php echo $view == 'abuse' ? 'active' : ''; ?>">Abuse Dashboard</a>
                    <a href="?view=sessions" class="sec-tab <?php echo $view == 'sessions' ? 'active' : ''; ?>">Session Management</a>
                    <a href="?view=rbac" class="sec-tab <?php echo $view == 'rbac' ? 'active' : ''; ?>">RBAC Matrix</a>
                </div>

                <?php if ($view == 'abuse'): ?>
                <!-- Screen 5.2.1 — Rate Limit & Abuse Dashboard -->
                <div class="dashboard-grid">
                    <div class="widget w-full glass-card">
                        <div class="widget-header">
                            <h3 class="widget-title">Real-time Rate Limit Violations (24h)</h3>
                            <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleSecurityAction('bulk_block', 'selected')"><i class="fas fa-ban"></i> Bulk Block Selected</button>
                        </div>
                        <div class="data-table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="40"><input type="checkbox"></th>
                                        <th>IP Address</th>
                                        <th>Limit Type</th>
                                        <th>Hits</th>
                                        <th>Auto-Blocked</th>
                                        <th>Risk Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="background: rgba(239, 68, 68, 0.05);">
                                        <td><input type="checkbox"></td>
                                        <td><strong>103.24.12.82</strong> <div style="font-size: 0.65rem; color: var(--text-secondary);">Mumbai, India</div></td>
                                        <td><span class="status-badge" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);">Login Attempts</span></td>
                                        <td><strong>42</strong></td>
                                        <td><span style="color: var(--success);"><i class="fas fa-check-circle"></i> Yes</span></td>
                                        <td><span class="status-badge status-rejected">CRITICAL</span></td>
                                        <td><button class="action-btn" style="color: var(--danger);" onclick="handleSecurityAction('permanent_block', '103.24.12.82')"><i class="fas fa-gavel"></i> Permanent Block</button></td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox"></td>
                                        <td><strong>45.12.92.110</strong> <div style="font-size: 0.65rem; color: var(--text-secondary);">Frankfurt, DE</div></td>
                                        <td><span class="status-badge" style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">AI Counselor</span></td>
                                        <td><strong>158</strong></td>
                                        <td><span style="color: var(--warning);"><i class="fas fa-clock"></i> Temp Block</span></td>
                                        <td><span class="status-badge status-pending">HIGH</span></td>
                                        <td><button class="action-btn" onclick="handleSecurityAction('history', '45.12.92.110')"><i class="fas fa-history"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="widget w-half glass-card">
                        <h3 class="widget-title">Bot Detection Log</h3>
                        <div style="margin-top: 1rem;">
                            <div style="font-size: 0.75rem; color: var(--text-secondary); padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                                <span style="color: var(--danger);">[13:12]</span> IP 182.XX.XX.XX: <span style="color: white;">Headless browser fingerprint detected</span>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary); padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                                <span style="color: var(--danger);">[13:05]</span> IP 202.XX.XX.XX: <span style="color: white;">Sub-100ms rapid page traversal</span>
                            </div>
                        </div>
                    </div>

                    <div class="widget w-half glass-card">
                        <h3 class="widget-title">Manual IP Control</h3>
                        <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">Immediately pushes to Nginx & Cloudflare WAF.</p>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="manualIp" placeholder="Enter IP or CIDR Range..." style="flex: 1; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px;">
                            <button class="btn btn-primary" onclick="handleSecurityAction('deny_list', document.getElementById('manualIp').value)">Add to Deny List</button>
                        </div>

                        <h4 style="margin-top: 2rem; font-size: 0.9rem; color: var(--accent-primary);">Active Deny List</h4>
                        <div style="max-height: 200px; overflow-y: auto; margin-top: 1rem;">
                            <?php
                            $stmtB = $pdo->query("SELECT * FROM ip_blacklist ORDER BY created_at DESC");
                            $blacklist = $stmtB->fetchAll();
                            if (empty($blacklist)): ?>
                                <p style="font-size: 0.75rem; color: var(--text-secondary);">No IPs currently blocked.</p>
                            <?php else:
                                foreach ($blacklist as $b): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; border-bottom: 1px solid var(--border-color);">
                                    <span style="font-family: monospace; font-size: 0.8rem;"><?php echo $b['ip_address']; ?></span>
                                    <button class="action-btn" style="color: var(--danger); font-size: 0.7rem;" onclick="handleSecurityAction('remove_block', '<?php echo $b['id']; ?>')"><i class="fas fa-trash"></i></button>
                                </div>
                                <?php endforeach;
                            endif; ?>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'sessions'): ?>
                <!-- Screen 5.2.2 — Active Session Management -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <h3 class="widget-title">Active Administrative Sessions</h3>
                        <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleSecurityAction('terminate_all', 'admin')">Terminate All Sessions</button>
                    </div>
                    
                    <div class="session-card">
                        <div style="display: flex; gap: 1.5rem; align-items: center;">
                            <div style="width: 45px; height: 45px; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">AV</div>
                            <div>
                                <div style="font-weight: 700;">Ankit Verma <span style="font-size: 0.7rem; color: var(--success);">(You)</span></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">IP: 103.21.12.XX &bull; Chrome on macOS &bull; Started 2h ago</div>
                            </div>
                        </div>
                        <span class="status-badge status-approved">ACTIVE NOW</span>
                    </div>

                    <div class="session-card">
                        <div style="display: flex; gap: 1.5rem; align-items: center;">
                            <div style="width: 45px; height: 45px; background: var(--accent-secondary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">PS</div>
                            <div>
                                <div style="font-weight: 700;">Pooja Singh</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">IP: 122.14.82.XX &bull; Safari on iOS &bull; Last action 12m ago</div>
                            </div>
                        </div>
                        <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleSecurityAction('terminate_session', 'Pooja Singh')">Force Terminate</button>
                    </div>

                    <h3 class="widget-title" style="margin-top: 3rem; margin-bottom: 1rem;">Student Session Anomalies</h3>
                    <div class="session-card" style="border-left: 4px solid var(--danger);">
                        <div>
                            <div style="font-weight: 700;">Vikram Aditya (#STU_8219) <span class="anomaly-badge">MULTI-IP DETECTED</span></div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Active from 4 different IP addresses (Delhi, Mumbai, Bangalore, Pune) within 60 mins.</div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleSecurityAction('lock_account', 'Vikram Aditya')">Lock Account</button>
                            <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleSecurityAction('security_alert', 'Vikram Aditya')">Send Alert</button>
                        </div>
                    </div>
                </div>

                <?php elseif ($view == 'rbac'): ?>
                <!-- Screen 5.2.3 — Admin RBAC Permission Matrix -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <h3 class="widget-title">Granular Permission Matrix (RBAC)</h3>
                        <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleSecurityAction('save_rbac', 'all')">Save Changes</button>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="data-table matrix-table">
                            <thead>
                                <tr>
                                    <th style="text-align: left;">Section / Role</th>
                                    <th>Super Admin</th>
                                    <th>Content Mod</th>
                                    <th>Finance</th>
                                    <th>SEO</th>
                                    <th>Data Entry</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align: left; font-weight: 700;">Student PII Data</td>
                                    <td><i class="fas fa-check"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left; font-weight: 700;">Review Moderation</td>
                                    <td><i class="fas fa-check"></i></td>
                                    <td><i class="fas fa-check"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left; font-weight: 700;">Billing & Invoices</td>
                                    <td><i class="fas fa-check"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-check"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                </tr>
                                <tr>
                                    <td style="text-align: left; font-weight: 700;">SEO Metadata</td>
                                    <td><i class="fas fa-check"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-times"></i></td>
                                    <td><i class="fas fa-check"></i></td>
                                    <td><i class="fas fa-check"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="widget-title" style="margin-top: 3rem; margin-bottom: 1rem;">RBAC Audit Trail (Immutable)</h3>
                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                        <div style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                            <span style="color: var(--accent-primary);">[Today 10:15]</span> Super Admin <strong>AV</strong> changed Role: <strong>Pooja Singh</strong> from 'Viewer' to 'Content Mod'.
                        </div>
                        <div style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                            <span style="color: var(--accent-primary);">[Yesterday 14:22]</span> Super Admin <strong>AV</strong> revoked 'Billing' access for Role: <strong>SEO Manager</strong>.
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
    <script>
        async function handleSecurityAction(action, target) {
            if (action === 'deny_list' && !target) { 
                Swal.fire({ icon: 'error', title: 'Error', text: 'Please enter an IP address.' }); 
                return; 
            }

            Swal.fire({
                title: 'Security Override',
                text: 'Authorizing request via WAF gateway...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('admin_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, target, module: 'security' })
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Authorized',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        if (action === 'deny_list' || action === 'permanent_block' || action === 'remove_block') location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Unauthorized', text: result.message });
                }
            } catch (error) {
                console.error("Security Error:", error);
                Swal.fire({ icon: 'error', title: 'Connection Failure', text: 'WAF rejected the handshake or server is offline.' });
            }
        }
    </script>
</body>
</html>
