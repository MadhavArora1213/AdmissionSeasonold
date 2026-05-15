<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'queue';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Moderation | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .mod-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .mod-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .mod-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .side-panel { 
            position: fixed; 
            top: 20px; 
            right: -620px; 
            width: 580px; 
            height: calc(100% - 40px); 
            background: rgba(15, 23, 42, 0.85); 
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border); 
            border-radius: 24px;
            z-index: 1100; 
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); 
            padding: 2.5rem; 
            overflow-y: auto; 
            box-shadow: -20px 0 50px rgba(0,0,0,0.6); 
        }
        .side-panel.open { right: 20px; }
        
        .ai-analysis { 
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
            border: 1px solid rgba(99, 102, 241, 0.3); 
            padding: 1.5rem; 
            border-radius: 16px; 
            margin-top: 1.5rem; 
        }
        .flagged-row { background: rgba(239, 68, 68, 0.03) !important; border-left: 4px solid var(--danger); }
        .priority-row { border-left: 4px solid var(--accent-primary); }
        
        .rule-card { 
            background: var(--glass-bg); 
            border: 1px solid var(--glass-border); 
            padding: 1.2rem; 
            border-radius: 16px; 
            margin-bottom: 1rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            transition: all 0.3s ease;
        }
        .rule-card:hover { border-color: var(--accent-primary); transform: translateX(5px); }

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
                        <h1 class="page-title">Content Moderation Queue</h1>
                        <p class="page-subtitle">Ensuring 100% authentic student reviews via AI-assisted verification.</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <span class="status-badge" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); font-weight: 700; font-size: 0.8rem; padding: 10px 15px;">
                            <i class="fas fa-clock"></i> SLA: 12 Reviews Overdue (48h+)
                        </span>
                    </div>
                </div>

                <div class="mod-tabs">
                    <a href="?view=queue" class="mod-tab <?php echo $view == 'queue' ? 'active' : ''; ?>">Moderation Queue</a>
                    <a href="?view=rules" class="mod-tab <?php echo $view == 'rules' ? 'active' : ''; ?>">Auto-Mod Rules</a>
                    <a href="?view=analytics" class="mod-tab <?php echo $view == 'analytics' ? 'active' : ''; ?>">Moderator Analytics</a>
                </div>

                <?php if ($view == 'queue'): ?>
                <!-- Screen 3.1.1 — Moderation Queue -->
                <div class="widget w-full glass-card">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; align-items: center;">
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" id="bulk-approve" style="display: none;"><i class="fas fa-check"></i> Bulk Approve</button>
                            <button class="btn btn-secondary" id="bulk-reject" style="display: none;"><i class="fas fa-times"></i> Bulk Reject</button>
                            <div class="header-search" style="width: 250px;">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Filter by college...">
                            </div>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">
                            Sort by: <select class="btn btn-secondary" style="font-weight: 700; color: white !important;">
                                <option style="background-color: #0f172a; color: white;">Priority (Low Reviews First)</option>
                                <option style="background-color: #0f172a; color: white;">Newest First</option>
                                <option style="background-color: #0f172a; color: white;">AI Score (High First)</option>
                            </select>
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" id="select-all"></th>
                                <th>Student</th>
                                <th>College / Course</th>
                                <th>Verification</th>
                                <th>AI Score</th>
                                <th>Sentiment</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT r.*, c.name as college_name FROM reviews r JOIN colleges c ON r.college_id = c.id WHERE r.status = 'PENDING' ORDER BY r.created_at DESC");
                            $pending_reviews = $stmt->fetchAll();
                            
                            if (empty($pending_reviews)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.3;"></i>
                                        <div>Queue is empty. All reviews moderated.</div>
                                    </td>
                                </tr>
                            <?php else:
                                foreach ($pending_reviews as $r): ?>
                                <tr class="<?php echo $r['quality_score'] < 30 ? 'flagged-row' : ($r['quality_score'] > 80 ? 'priority-row' : ''); ?>">
                                    <td><input type="checkbox" class="review-check" value="<?php echo $r['id']; ?>"></td>
                                    <td>
                                        <div style="font-weight: 700;">User_<?php echo substr($r['student_id'], 0, 5); ?></div>
                                        <div style="font-size: 0.7rem; color: var(--text-secondary);"><?php echo date('d M Y', strtotime($r['created_at'])); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($r['college_name']); ?></div>
                                        <div style="font-size: 0.7rem; color: var(--text-secondary);">Batch <?php echo $r['batch_year']; ?></div>
                                    </td>
                                    <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;"><?php echo str_replace('_', ' ', $r['verification_method']); ?></span></td>
                                    <td><span class="status-badge <?php echo $r['quality_score'] < 30 ? 'status-rejected' : ($r['quality_score'] > 70 ? 'status-approved' : 'status-pending'); ?>"><?php echo $r['quality_score']; ?>/100</span></td>
                                    <td><span style="color: var(--<?php echo strtolower($r['sentiment_label'] ?? 'neutral') == 'positive' ? 'success' : 'danger'; ?>); font-size: 0.75rem;"><i class="fas fa-<?php echo strtolower($r['sentiment_label'] ?? 'neutral') == 'positive' ? 'smile' : 'frown'; ?>"></i> <?php echo $r['sentiment_label']; ?></span></td>
                                    <td><span class="status-badge status-pending"><?php echo $r['status']; ?></span></td>
                                    <td><button class="action-btn view-detail" onclick="openDetail('<?php echo $r['id']; ?>')"><i class="fas fa-eye"></i></button></td>
                                </tr>
                                <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'rules'): ?>
                <!-- Screen 3.1.3 — Auto-Moderation Rules -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <h3 class="widget-title">Active Automation Rules</h3>
                        <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="openRuleModal()">+ Create New Rule</button>
                    </div>
                    
                    <?php
                    $stmtRules = $pdo->query("SELECT * FROM moderation_rules ORDER BY created_at DESC");
                    $rules = $stmtRules->fetchAll();
                    
                    if (empty($rules)): ?>
                        <div style="text-align: center; padding: 3rem; color: var(--text-secondary); border: 2px dashed var(--border-color); border-radius: 16px;">
                            No automation rules defined yet.
                        </div>
                    <?php else:
                        foreach ($rules as $rule): ?>
                        <div class="rule-card">
                            <div>
                                <div style="font-weight: 700; margin-bottom: 5px; color: white;"><?php echo htmlspecialchars($rule['rule_name']); ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);"><?php echo htmlspecialchars($rule['condition_text']); ?> → <span style="color: var(--<?php echo $rule['action'] == 'APPROVE' ? 'success' : ($rule['action'] == 'REJECT' ? 'danger' : 'warning'); ?>); font-weight: 700;"><?php echo $rule['action']; ?></span></div>
                            </div>
                            <div style="display: flex; gap: 2rem; align-items: center;">
                                <div class="status-badge <?php echo $rule['is_active'] ? 'status-approved' : 'status-rejected'; ?>"><?php echo $rule['is_active'] ? 'ACTIVE' : 'DISABLED'; ?></div>
                                <button class="action-btn" onclick="handleReviewAction('delete_rule', '<?php echo $rule['id']; ?>')"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <?php endforeach;
                    endif; ?>
                </div>

                <?php elseif ($view == 'analytics'): ?>
                <!-- Screen 3.1.4 — Moderation Analytics -->
                <div class="dashboard-grid">
                    <div class="widget w-two-thirds glass-card">
                        <h3 class="widget-title">Review Volume Trends (30 Days)</h3>
                        <canvas id="reviewTrendChart" height="200"></canvas>
                    </div>
                    <div class="widget w-third glass-card">
                        <h3 class="widget-title">Rejection Reasons</h3>
                        <canvas id="rejectionPie" height="200"></canvas>
                    </div>
                    <div class="widget w-full glass-card">
                        <h3 class="widget-title">Moderator Performance Leaderboard</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Moderator</th>
                                    <th>Processed Today</th>
                                    <th>Avg Time / Review</th>
                                    <th>SLA Compliance</th>
                                    <th>Accuracy (Overturns)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Arjun S.</strong></td>
                                    <td>142</td>
                                    <td>42s</td>
                                    <td><span style="color: var(--success); font-weight: 700;">98%</span></td>
                                    <td><span style="color: var(--success); font-weight: 700;">0.2%</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Priya V.</strong></td>
                                    <td>118</td>
                                    <td>58s</td>
                                    <td><span style="color: var(--warning); font-weight: 700;">92%</span></td>
                                    <td><span style="color: var(--success); font-weight: 700;">0.8%</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Screen 3.1.2 — Review Detail Pane -->
    <div class="side-panel" id="detail-panel">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 style="font-size: 1.2rem; color: var(--accent-primary);">Review Details</h2>
            <button class="action-btn" onclick="closeDetail()"><i class="fas fa-times"></i></button>
        </div>

        <div style="margin-bottom: 2rem;">
            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 5px;">Overall Verdict</div>
            <p style="font-size: 1rem; color: white; font-weight: 700; line-height: 1.4;">"Great campus life but faculty needs improvement. Placement cell is very active."</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
            <div style="background: rgba(16, 185, 129, 0.05); padding: 1rem; border-radius: 8px;">
                <div style="font-size: 0.7rem; color: var(--success); font-weight: 700; margin-bottom: 5px;">PROS</div>
                <div style="font-size: 0.8rem;">World-class labs, great canteen, strong alumni network.</div>
            </div>
            <div style="background: rgba(239, 68, 68, 0.05); padding: 1rem; border-radius: 8px;">
                <div style="font-size: 0.7rem; color: var(--danger); font-weight: 700; margin-bottom: 5px;">CONS</div>
                <div style="font-size: 0.8rem;">Very strict attendance policy (85% mandatory).</div>
            </div>
        </div>

        <div class="ai-analysis">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-primary);"><i class="fas fa-robot"></i> Llama AI Analysis</span>
                <span class="status-badge status-approved" style="font-size: 0.7rem;">Score: 8/10</span>
            </div>
            <p style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.5;">
                Detailed and balanced. Student mentions specific placement company names (TCS, Wipro) which is a strong authenticity signal. No promotional language detected. IP address matches student's registered city.
            </p>
        </div>

        <div style="margin-top: 2rem;">
            <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 10px;">Rejection Reason (if applicable)</div>
            <select class="btn" style="width: 100%; background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); margin-bottom: 1rem;">
                <option>Insufficient detail</option>
                <option>Promotional content</option>
                <option>Unverifiable claims</option>
                <option>Suspected fake identity</option>
            </select>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 2.5rem;">
            <button class="btn btn-primary" style="flex: 2; padding: 1rem;" onclick="processReview('approve')">
                <i class="fas fa-check-circle"></i> Approve Review
            </button>
            <button class="btn btn-secondary" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--danger); flex: 1;" onclick="processReview('reject')">
                <i class="fas fa-times"></i> Reject
            </button>
            <button class="btn btn-secondary" style="flex: 1;" onclick="processReview('escalate')">
                <i class="fas fa-shield-alt"></i> Escalate
            </button>
        </div>
    </div>

    <!-- Create Rule Modal -->
    <div id="ruleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-size: 1.2rem; color: var(--accent-primary);"><i class="fas fa-robot"></i> Create Auto-Mod Rule</h2>
                <button class="close-btn" onclick="closeRuleModal()">&times;</button>
            </div>
            <form id="ruleForm" onsubmit="handleSaveRule(event)">
                <div class="form-group">
                    <label>Rule Name</label>
                    <input type="text" name="ruleName" placeholder="e.g., Spam Filter v2" required>
                </div>
                <div class="form-group">
                    <label>Condition (Logic)</label>
                    <textarea name="conditionText" rows="3" placeholder="IF review contains phone numbers OR AI Score < 2" required></textarea>
                </div>
                <div class="form-group">
                    <label>Action</label>
                    <select name="ruleAction">
                        <option value="APPROVE">Auto-Approve</option>
                        <option value="REJECT">Auto-Reject</option>
                        <option value="ESCALATE">Flag for Audit</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeRuleModal()" style="flex: 1;">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="flex: 2;">Save Rule & Activate</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRuleModal() {
            document.getElementById('ruleModal').classList.add('show');
        }
        function closeRuleModal() {
            document.getElementById('ruleModal').classList.remove('show');
            document.getElementById('ruleForm').reset();
        }

        async function handleSaveRule(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = {
                action: 'create_rule',
                rule_name: formData.get('ruleName'),
                condition_text: formData.get('conditionText'),
                rule_action: formData.get('ruleAction')
            };

            Swal.fire({ title: 'Saving Rule...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const response = await fetch('add_rule_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) {
                    Swal.fire({ icon: 'success', title: 'Saved!', text: result.message, timer: 2000, showConfirmButton: false })
                    .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save rule.' });
            }
        }
        let currentReviewId = null;

        function openDetail(id) {
            currentReviewId = id;
            document.getElementById('detail-panel').classList.add('open');
            // In a real app, you would fetch details via AJAX here
        }
        function closeDetail() {
            document.getElementById('detail-panel').classList.remove('open');
            currentReviewId = null;
        }

        async function processReview(action) {
            if (!currentReviewId) return;
            
            let apiAction = '';
            if (action === 'approve') apiAction = 'approve_review';
            else if (action === 'reject') apiAction = 'reject_review';
            else if (action === 'escalate') apiAction = 'escalate_review';

            await handleReviewAction(apiAction, currentReviewId);
            closeDetail();
            setTimeout(() => location.reload(), 1500);
        }

        // Bulk selection logic
        const selectAll = document.getElementById('select-all');
        const checks = document.querySelectorAll('.review-check');
        const bulkApprove = document.getElementById('bulk-approve');
        const bulkReject = document.getElementById('bulk-reject');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checks.forEach(c => c.checked = this.checked);
                toggleBulkBtns();
            });
            checks.forEach(c => c.addEventListener('change', toggleBulkBtns));
        }

        function toggleBulkBtns() {
            const anyChecked = Array.from(checks).some(c => c.checked);
            bulkApprove.style.display = anyChecked ? 'inline-block' : 'none';
            bulkReject.style.display = anyChecked ? 'inline-block' : 'none';
        }

        if (bulkApprove) {
            bulkApprove.addEventListener('click', async function() {
                const selected = Array.from(checks).filter(c => c.checked).map(c => c.value);
                for (const id of selected) {
                    await handleReviewAction('approve_review', id);
                }
                location.reload();
            });
        }

        if (bulkReject) {
            bulkReject.addEventListener('click', async function() {
                const selected = Array.from(checks).filter(c => c.checked).map(c => c.value);
                for (const id of selected) {
                    await handleReviewAction('reject_review', id);
                }
                location.reload();
            });
        }

        // Analytics Charts
        if (document.getElementById('reviewTrendChart')) {
            const ctx1 = document.getElementById('reviewTrendChart').getContext('2d');
            new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: Array.from({length: 30}, (_, i) => i + 1),
                    datasets: [{
                        label: 'Reviews Submitted',
                        data: Array.from({length: 30}, () => Math.floor(Math.random() * 50) + 20),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } }, x: { grid: { display: false } } }
                }
            });

            const ctx2 = document.getElementById('rejectionPie').getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Insufficient Detail', 'Promotional', 'Unverifiable', 'Abusive'],
                    datasets: [{
                        data: [60, 20, 15, 5],
                        backgroundColor: ['#6366f1', '#a855f7', '#f59e0b', '#ef4444'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8', font: { size: 10 } } } }
                }
            });
        }
        async function handleReviewAction(action, target) {
            Swal.fire({
                title: 'Review Moderation',
                text: 'Updating moderation status in the ledger...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('admin_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, target, module: 'reviews' })
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message });
                }
            } catch (error) {
                console.error("Review API Error:", error);
                Swal.fire({ icon: 'error', title: 'Connection Failure', text: 'Administrative API is currently unreachable.' });
            }
        }
    </script>
</body>
</html>
