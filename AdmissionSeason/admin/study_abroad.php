<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'cms';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Abroad Operations | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sa-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .sa-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .sa-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .uni-card { background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; }
        .commission-step { flex: 1; text-align: center; position: relative; padding: 10px; }
        .commission-step:not(:last-child)::after { content: '\f105'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: -10px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }
        
        .inr-badge { background: rgba(16, 185, 129, 0.1); color: var(--success); font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; margin-left: 10px; font-weight: 700; }
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
                        <h1 class="page-title">Study Abroad Operations</h1>
                        <p class="page-subtitle">Managing international university partnerships, student enrollments, and tuition commissions.</p>
                    </div>
                </div>

                <div class="sa-tabs">
                    <a href="?view=cms" class="sa-tab <?php echo $view == 'cms' ? 'active' : ''; ?>">University CMS</a>
                    <a href="?view=partners" class="sa-tab <?php echo $view == 'partners' ? 'active' : ''; ?>">Partner Manager</a>
                    <a href="?view=pipeline" class="sa-tab <?php echo $view == 'pipeline' ? 'active' : ''; ?>">Commission Pipeline</a>
                </div>

                <?php if ($view == 'cms'): ?>
                <!-- Screen 10.1.1 — University CMS Table -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.75rem;">+ Add International University</button>
                            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.75rem;"><i class="fas fa-file-import"></i> Bulk CSV Import</button>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">
                            <i class="fas fa-sync-alt"></i> FX Rate: $1 = ₹83.42 <span style="font-size: 0.6rem;">(Updated 12m ago)</span>
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>University Name</th>
                                <th>Country / City</th>
                                <th>QS Rank</th>
                                <th>Avg Tuition (Annual)</th>
                                <th>IELTS / TOEFL</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Univ of Melbourne</strong> <span class="status-badge status-approved" style="font-size: 0.6rem;">PARTNER</span></td>
                                <td>Australia &bull; Melbourne</td>
                                <td><span style="font-weight: 700; color: var(--accent-primary);">#14</span></td>
                                <td>
                                    <div>$ 34,200</div>
                                    <div class="inr-badge">₹ 28.5L /yr</div>
                                </td>
                                <td>7.0 (Min 6.5)</td>
                                <td><span class="status-badge status-approved">Active</span></td>
                                <td><button class="action-btn"><i class="fas fa-edit"></i></button></td>
                            </tr>
                            <tr>
                                <td><strong>Northeastern Univ</strong></td>
                                <td>USA &bull; Boston</td>
                                <td><span style="font-weight: 700; color: var(--accent-primary);">#322</span></td>
                                <td>
                                    <div>$ 58,100</div>
                                    <div class="inr-badge">₹ 48.4L /yr</div>
                                </td>
                                <td>6.5 (Min 6.0)</td>
                                <td><span class="status-badge status-approved">Active</span></td>
                                <td><button class="action-btn"><i class="fas fa-edit"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'partners'): ?>
                <!-- Screen 10.1.2 — Partner University Manager -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Signed University Partnerships</h3>
                        <span class="status-badge status-rejected">2 Contracts Expiring Soon</span>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>University</th>
                                <th>Commission Rate</th>
                                <th>Payment Frequency</th>
                                <th>Contract Expiry</th>
                                <th>Pending Commissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Univ of Birmingham</strong></td>
                                <td><span style="font-weight: 700;">12%</span></td>
                                <td>Quarterly</td>
                                <td>12 Dec 2026</td>
                                <td style="font-weight: 700; color: var(--success);">£ 18,400</td>
                                <td><button class="btn btn-primary" style="font-size: 0.7rem;">Manage Contract</button></td>
                            </tr>
                            <tr style="background: rgba(239, 68, 68, 0.05);">
                                <td><strong>Deakin University</strong></td>
                                <td><span style="font-weight: 700;">15%</span></td>
                                <td>Monthly</td>
                                <td><span style="color: var(--danger); font-weight: 700;">15 Jun 2026</span></td>
                                <td style="font-weight: 700; color: var(--success);">AUD 4,200</td>
                                <td><button class="btn" style="background: var(--danger); color: white; font-size: 0.7rem;">Renew Now</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'pipeline'): ?>
                <!-- Commission Pipeline Funnel -->
                <div class="widget w-full">
                    <h3 class="widget-title">Active Student Pipeline (Partner Universities)</h3>
                    <div style="display: flex; background: rgba(0,0,0,0.2); border-radius: 12px; padding: 20px; margin-top: 2rem;">
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700;">4,201</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Clicks to Apply</div>
                        </div>
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700;">842</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Applications Started</div>
                        </div>
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700;">118</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Offers Issued</div>
                        </div>
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--success);">42</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Enrollments Confirmed</div>
                        </div>
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary);">₹ 62.4L</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Estimated Commission</div>
                        </div>
                    </div>

                    <h3 class="widget-title" style="margin-top: 3rem; margin-bottom: 1rem;">Confirmed Enrollments (Awaiting Payment)</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>University</th>
                                <th>Intake</th>
                                <th>Tuition Paid</th>
                                <th>Commission Due</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ananya M.</td>
                                <td>Univ of Melbourne</td>
                                <td>Jul 2026</td>
                                <td>$ 34,200</td>
                                <td style="font-weight: 700;">$ 4,104 (12%)</td>
                                <td><span class="status-badge status-pending">Invoiced</span></td>
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
