<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'subs';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsorship & Billing | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .bill-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .bill-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .bill-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .churn-badge { font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; font-weight: 700; }
        .churn-high { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .churn-low { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        
        .trial-warning { color: var(--warning); font-weight: 700; }
        .trial-danger { color: var(--danger); font-weight: 700; }
        
        .ad-calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; margin-top: 1rem; }
        .ad-day { background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; min-height: 80px; position: relative; }
        .ad-day.booked { border-color: var(--accent-primary); background: rgba(99, 102, 241, 0.05); }
        .ad-tag { font-size: 0.6rem; background: var(--accent-primary); color: white; padding: 2px 4px; border-radius: 3px; position: absolute; bottom: 5px; right: 5px; }
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
                        <h1 class="page-title">Sponsorship & Billing Control</h1>
                        <p class="page-subtitle">Managing college subscriptions, ad inventory, and partner commissions.</p>
                    </div>
                </div>

                <div class="bill-tabs">
                    <a href="?view=subs" class="bill-tab <?php echo $view == 'subs' ? 'active' : ''; ?>">Subscriptions</a>
                    <a href="?view=invoices" class="bill-tab <?php echo $view == 'invoices' ? 'active' : ''; ?>">Invoice Engine</a>
                    <a href="?view=ads" class="bill-tab <?php echo $view == 'ads' ? 'active' : ''; ?>">Ad Inventory</a>
                    <a href="?view=commissions" class="bill-tab <?php echo $view == 'commissions' ? 'active' : ''; ?>">Commissions</a>
                </div>

                <?php if ($view == 'subs'): ?>
                <!-- Screen 4.2.1 — College Subscription Table -->
                <div class="widget w-full">
                    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.8rem;">Active Plans</button>
                            <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.8rem;">Trial Expiring (12)</button>
                        </div>
                        <div class="header-search" style="width: 250px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search colleges...">
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>College</th>
                                <th>Plan / Status</th>
                                <th>CPL Rate</th>
                                <th>Credits</th>
                                <th>Renewal / Trial</th>
                                <th>Churn Risk (AI)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>BITS Pilani</strong></td>
                                <td>
                                    <span class="status-badge" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);">Premium Elite</span>
                                    <div style="font-size: 0.7rem; color: var(--success); margin-top: 4px;">Verified B2B</div>
                                </td>
                                <td>₹150</td>
                                <td>
                                    <div style="font-weight: 700;">4,102 / 5,000</div>
                                    <div style="height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; margin-top: 5px; overflow: hidden;">
                                        <div style="width: 82%; height: 100%; background: var(--success);"></div>
                                    </div>
                                </td>
                                <td>12 Jun 2026</td>
                                <td><span class="churn-badge churn-low">LOW RISK</span></td>
                                <td><button class="action-btn"><i class="fas fa-edit"></i></button></td>
                            </tr>
                            <tr>
                                <td><strong>Galgotias Univ</strong></td>
                                <td>
                                    <span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">Free Trial</span>
                                </td>
                                <td>₹180</td>
                                <td>0 / 50</td>
                                <td><span class="trial-danger">Expires in 2 days</span></td>
                                <td><span class="churn-badge churn-high">HIGH RISK</span></td>
                                <td><button class="action-btn" style="color: var(--accent-primary);" title="Call for BD Upgrade"><i class="fas fa-phone-alt"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'invoices'): ?>
                <!-- Screen 4.2.2 — Invoice & Billing Engine -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Monthly Invoice Ledger</h3>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.75rem;"><i class="fas fa-magic"></i> Generate Batch (May 2026)</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Invoice ID</th>
                                <th>College</th>
                                <th>Leads</th>
                                <th>Net Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#INV-2026-0501</td>
                                <td><strong>Manipal University</strong></td>
                                <td>412</td>
                                <td style="font-weight: 700;">₹ 74,160</td>
                                <td><span class="status-badge status-pending">Sent</span></td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <button class="action-btn"><i class="fas fa-file-pdf"></i></button>
                                        <button class="action-btn" style="color: var(--success);"><i class="fas fa-check"></i> Mark Paid</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>#INV-2026-0502</td>
                                <td><strong>SRM Institute</strong></td>
                                <td>184</td>
                                <td style="font-weight: 700;">₹ 33,120</td>
                                <td><span class="status-badge status-rejected">Overdue (15d)</span></td>
                                <td><button class="action-btn" style="color: var(--danger);"><i class="fas fa-bell"></i> Resend</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'ads'): ?>
                <!-- Screen 4.2.3 — Sponsored Listing Manager -->
                <div class="dashboard-grid">
                    <div class="widget w-two-thirds">
                        <div class="widget-header">
                            <h3 class="widget-title">Ad Inventory Calendar (May 2026)</h3>
                            <select class="btn" style="background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); font-size: 0.8rem;">
                                <option>Homepage Featured Banner</option>
                                <option>Search Pos 1 (Engineering)</option>
                                <option>Exam Page Sidebar</option>
                            </select>
                        </div>
                        <div class="ad-calendar-grid">
                            <?php for($i=1; $i<=31; $i++): 
                                $isBooked = ($i < 15);
                            ?>
                                <div class="ad-day <?php echo $isBooked ? 'booked' : ''; ?>">
                                    <span style="font-size: 0.7rem; color: var(--text-secondary);"><?php echo $i; ?></span>
                                    <?php if($isBooked): ?>
                                        <span class="ad-tag">BITS</span>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="widget w-third">
                        <h3 class="widget-title">New Booking Form</h3>
                        <div style="margin-top: 1.5rem;">
                            <label style="font-size: 0.75rem; color: var(--text-secondary);">Select College</label>
                            <select style="width: 100%; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px; margin-top: 5px;">
                                <option>VIT Vellore</option>
                            </select>
                        </div>
                        <div style="margin-top: 1rem;">
                            <label style="font-size: 0.75rem; color: var(--text-secondary);">Targeting Rule</label>
                            <div style="font-size: 0.7rem; color: var(--success); background: rgba(16, 185, 129, 0.1); padding: 10px; border-radius: 4px; margin-top: 5px;">
                                <i class="fas fa-info-circle"></i> This slot is restricted to Engineering colleges in TN.
                            </div>
                        </div>
                        <div style="margin-top: 1.5rem;">
                            <label style="font-size: 0.75rem; color: var(--text-secondary);">Price (₹)</label>
                            <input type="text" value="45,000" style="width: 100%; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px; margin-top: 5px;">
                        </div>
                        <button class="btn btn-primary" style="width: 100%; margin-top: 2rem;">Generate Payment Link</button>
                    </div>
                </div>

                <?php elseif ($view == 'commissions'): ?>
                <!-- Screen 4.2.4 — Commission Tracker -->
                <div class="widget w-full">
                    <h3 class="widget-title">Partner Commissions Ledger</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Partner</th>
                                <th>Service</th>
                                <th>Student Referrals</th>
                                <th>Commission Due</th>
                                <th>Overdue Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Univ of Melbourne</strong></td>
                                <td>Study Abroad</td>
                                <td>14</td>
                                <td style="font-weight: 700;">$ 4,200</td>
                                <td>-</td>
                                <td><span class="status-badge status-approved">Paid</span></td>
                            </tr>
                            <tr>
                                <td><strong>HDFC Credila</strong></td>
                                <td>Edu Loan Referral</td>
                                <td>82</td>
                                <td style="font-weight: 700;">₹ 1,24,000</td>
                                <td><span style="color: var(--danger); font-weight: 700;">12 days</span></td>
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
