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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .bill-tabs { 
            display: flex; 
            gap: 1rem; 
            border-bottom: 1px solid var(--border-color); 
            margin-bottom: 2rem; 
            overflow-x: auto; 
            scrollbar-width: none; 
            -ms-overflow-style: none; 
        }
        .bill-tabs::-webkit-scrollbar { display: none; }
        .bill-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; white-space: nowrap; }
        .bill-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        @media (max-width: 768px) {
            .ad-calendar-grid { grid-template-columns: repeat(3, 1fr); }
            .modal-content { padding: 1.5rem; width: 95%; }
        }
        
        .churn-badge { font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; font-weight: 700; }
        .churn-high { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .churn-low { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        
        .trial-warning { color: var(--warning); font-weight: 700; }
        .trial-danger { color: var(--danger); font-weight: 700; }
        
        .ad-calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; margin-top: 1rem; }
        .ad-day { background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); padding: 10px; border-radius: 8px; min-height: 80px; position: relative; }
        .ad-day.booked { border-color: var(--accent-primary); background: rgba(99, 102, 241, 0.05); }
        .ad-tag { font-size: 0.6rem; background: var(--accent-primary); color: white; padding: 2px 4px; border-radius: 3px; position: absolute; bottom: 5px; right: 5px; }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            animation: fadeIn 0.3s ease;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #0f172a;
            padding: 2.5rem;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            max-width: 550px;
            width: 90%;
            animation: slideUp 0.3s ease;
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }

        .close-btn { background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; }
        
        .form-group { margin-bottom: 1.2rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: white;
        }
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
                <div class="widget w-full glass-card">
                    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.8rem;">Active Plans</button>
                            <button class="btn btn-secondary" style="font-size: 0.8rem;">Trial Expiring (12)</button>
                        </div>
                        <div class="header-search" style="width: 250px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search colleges...">
                        </div>
                    </div>

                    <div class="data-table-container">
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
                                <?php
                                $stmt = $pdo->query("SELECT b.*, c.name as college_name FROM college_b2b_accounts b JOIN colleges c ON b.college_id = c.id ORDER BY b.created_at DESC");
                                $subs = $stmt->fetchAll();
                                
                                if (empty($subs)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-secondary);">No active B2B subscriptions found.</td>
                                    </tr>
                                <?php else:
                                    foreach ($subs as $s): 
                                        $percent = ($s['lead_credit_balance'] > 0) ? min(100, ($s['lead_credit_balance'] / 5000) * 100) : 0;
                                    ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($s['college_name']); ?></strong></td>
                                        <td>
                                            <span class="status-badge" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);"><?php echo $s['plan']; ?></span>
                                            <div style="font-size: 0.7rem; color: var(--success); margin-top: 4px;">Verified B2B</div>
                                        </td>
                                        <td>₹<?php echo $s['cpl_rate']; ?></td>
                                        <td>
                                            <div style="font-weight: 700;"><?php echo number_format($s['lead_credit_balance']); ?> Credits</div>
                                            <div style="height: 4px; background: rgba(255,255,255,0.1); border-radius: 2px; margin-top: 5px; overflow: hidden;">
                                                <div style="width: <?php echo $percent; ?>%; height: 100%; background: var(--success);"></div>
                                            </div>
                                        </td>
                                        <td><?php echo $s['plan_expires_at'] ? date('d M Y', strtotime($s['plan_expires_at'])) : 'Ongoing'; ?></td>
                                        <td><span class="churn-badge churn-low">STABLE</span></td>
                                        <td>
                                            <button class="action-btn" onclick="openSubModal('<?php echo $s['id']; ?>', '<?php echo htmlspecialchars($s['college_name']); ?>', '<?php echo $s['plan']; ?>', '<?php echo $s['cpl_rate']; ?>', '<?php echo $s['lead_credit_balance']; ?>')"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    <?php endforeach;
                                endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'invoices'): ?>
                <!-- Screen 4.2.2 — Invoice & Billing Engine -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <h3 class="widget-title">Monthly Invoice Ledger</h3>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleBillingAction('batch', 'May 2026')"><i class="fas fa-magic"></i> Generate Batch (May 2026)</button>
                        </div>
                    </div>
                    <div class="data-table-container">
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
                                            <button class="action-btn" onclick="handleBillingAction('pdf', '#INV-2026-0501')"><i class="fas fa-file-pdf"></i></button>
                                            <button class="action-btn" style="color: var(--success);" onclick="handleBillingAction('pay', '#INV-2026-0501')"><i class="fas fa-check"></i> Mark Paid</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#INV-2026-0502</td>
                                    <td><strong>SRM Institute</strong></td>
                                    <td>184</td>
                                    <td style="font-weight: 700;">₹ 33,120</td>
                                    <td><span class="status-badge status-rejected">Overdue (15d)</span></td>
                                    <td><button class="action-btn" style="color: var(--danger);" onclick="handleBillingAction('resend', '#INV-2026-0502')"><i class="fas fa-bell"></i> Resend</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'ads'): ?>
                <!-- Screen 4.2.3 — Sponsored Listing Manager -->
                <div class="dashboard-grid">
                    <div class="widget w-two-thirds glass-card">
                        <div class="widget-header">
                            <h3 class="widget-title">Ad Inventory Calendar (May 2026)</h3>
                            <select class="btn btn-secondary" style="font-size: 0.8rem;">
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

                    <div class="widget w-third glass-card">
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
                        <button class="btn btn-primary" style="width: 100%; margin-top: 2rem;" onclick="handleBillingAction('link', 'VIT Vellore')">Generate Payment Link</button>
                    </div>
                </div>

                <?php elseif ($view == 'commissions'): ?>
                <!-- Screen 4.2.4 — Commission Tracker -->
                <div class="widget w-full glass-card">
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
                                <th>Actions</th>
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
                                <td><button class="action-btn" onclick="handleBillingAction('view_receipt', 'Melbourne')"><i class="fas fa-file-invoice-dollar"></i></button></td>
                            </tr>
                            <tr>
                                <td><strong>HDFC Credila</strong></td>
                                <td>Edu Loan Referral</td>
                                <td>82</td>
                                <td style="font-weight: 700;">₹ 1,24,000</td>
                                <td><span style="color: var(--danger); font-weight: 700;">12 days</span></td>
                                <td><span class="status-badge status-pending">Invoiced</span></td>
                                <td><button class="action-btn" onclick="handleBillingAction('remind', 'HDFC Credila')"><i class="fas fa-bell"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

        </main>
    </div>
    <!-- Edit Subscription Modal -->
    <div id="subModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-size: 1.2rem; color: var(--accent-primary);"><i class="fas fa-edit"></i> Edit Subscription: <span id="modalCollegeName"></span></h2>
                <button class="close-btn" onclick="closeSubModal()">&times;</button>
            </div>
            <form id="subForm" onsubmit="handleSaveSub(event)">
                <input type="hidden" name="subId" id="subId">
                <div class="form-group">
                    <label>Subscription Plan</label>
                    <select name="plan" id="subPlan">
                        <option value="FREE">FREE</option>
                        <option value="GROWTH">GROWTH</option>
                        <option value="PREMIUM">PREMIUM</option>
                        <option value="ENTERPRISE">ENTERPRISE</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>CPL Rate (₹)</label>
                    <input type="number" name="cplRate" id="subCpl" required>
                </div>
                <div class="form-group">
                    <label>Lead Credit Balance</label>
                    <input type="number" name="credits" id="subCredits" required>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeSubModal()" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 2;">Update Subscription</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openSubModal(id, name, plan, cpl, credits) {
            document.getElementById('subId').value = id;
            document.getElementById('modalCollegeName').innerText = name;
            document.getElementById('subPlan').value = plan;
            document.getElementById('subCpl').value = cpl;
            document.getElementById('subCredits').value = credits;
            document.getElementById('subModal').classList.add('show');
        }

        function closeSubModal() {
            document.getElementById('subModal').classList.remove('show');
        }

        async function handleSaveSub(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = {
                action: 'update_subscription',
                target: formData.get('subId'),
                plan: formData.get('plan'),
                cpl_rate: formData.get('cplRate'),
                credits: formData.get('credits'),
                module: 'billing'
            };

            Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const response = await fetch('admin_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Updated!', text: result.message, timer: 2000, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update subscription.' });
            }
        }

        async function handleBillingAction(action, target) {
            let config = {
                title: 'Billing Action',
                text: '',
                icon: 'info',
                showConfirmButton: false,
                timer: 2000,
                background: 'rgba(15, 23, 42, 0.95)',
                color: '#fff',
                backdrop: `rgba(0,0,0,0.4) blur(4px)`
            };

            if (action === 'pdf') { config.text = 'Generating PDF for invoice ' + target + '...'; config.icon = 'info'; }
            else if (action === 'pay') { config.text = 'Invoice ' + target + ' marked as PAID.'; config.icon = 'success'; }
            else if (action === 'resend') { config.text = 'Reminder notification sent for invoice ' + target + '.'; config.icon = 'warning'; }
            else if (action === 'batch') { config.text = 'Bulk invoice generation started for May 2026.'; config.icon = 'success'; }
            else if (action === 'link') { config.text = 'Payment link generated and copied to clipboard!'; config.icon = 'success'; }
            else if (action === 'remind') { config.text = 'Payment reminder sent to ' + target + '.'; config.icon = 'warning'; }
            
            Swal.fire(config);
            
            // Call API for backend processing
            try {
                await fetch('admin_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, target, module: 'billing' })
                });
            } catch (e) { console.error(e); }
        }
    </script>
</body>
</html>
