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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        /* Full Width Styles */
        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-area {
            padding: 2rem;
            flex: 1;
        }

        /* Two Column Grid */
        .rules-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .rules-column {
            display: flex;
            flex-direction: column;
        }

        .rules-section {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            flex: 1;
        }

        .rules-section h2 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            margin-bottom: 1.5rem;
        }

        .back-button:hover {
            background: rgba(99, 102, 241, 0.2);
            color: var(--accent-primary);
        }

        .header-with-back {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .header-content h1 {
            margin-bottom: 0.5rem;
        }
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
                        <a href="?view=review" class="btn btn-secondary"><i class="fas fa-university"></i> College Submissions (12)</a>
                        <button class="btn btn-primary" onclick="handleScholarshipAction('create', 'new')"><i class="fas fa-plus"></i> Add New Scholarship</button>
                    </div>
                </div>

                <div class="widget w-full glass-card">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                        <div class="header-search" style="width: 300px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search by name, provider or state...">
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleScholarshipAction('filter', 'state')">Filter by State</button>
                            <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleScholarshipAction('view_archive', 'expired')">Expired Archive</button>
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
                            <?php
                            $stmt = $pdo->query("SELECT * FROM scholarships ORDER BY deadline ASC LIMIT 50");
                            $scholarships = $stmt->fetchAll();
                            
                            if (empty($scholarships)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                        No scholarships found in database.
                                    </td>
                                </tr>
                            <?php else:
                                foreach ($scholarships as $sch): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--accent-primary);"><?php echo htmlspecialchars($sch['name']); ?></div>
                                        <div style="font-size: 0.7rem; color: var(--text-secondary);"><?php echo htmlspecialchars($sch['category']); ?></div>
                                    </td>
                                    <td><span class="status-pill <?php echo $sch['category'] == 'GOVERNMENT' ? 'gov' : 'ngo'; ?>"><?php echo htmlspecialchars($sch['provider_name']); ?></span></td>
                                    <td style="font-weight: 700;"><?php echo $sch['amount_inr'] ? '₹ ' . number_format($sch['amount_inr']) : 'Full Tuition'; ?></td>
                                    <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;"><?php echo htmlspecialchars($sch['state_scope'] ?: 'National'); ?></span></td>
                                    <td>
                                        <div style="font-size: 0.85rem;"><?php echo $sch['deadline'] ? date('d M Y', strtotime($sch['deadline'])) : 'TBD'; ?></div>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $sch['is_verified'] ? 'status-approved' : 'status-pending'; ?>">
                                            <?php echo $sch['is_verified'] ? 'Active' : 'Pending'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 10px;">
                                            <a href="?view=rules&id=<?php echo $sch['id']; ?>" class="action-btn" title="Rule Builder"><i class="fas fa-project-diagram"></i></a>
                                            <button class="action-btn" onclick="handleScholarshipAction('edit', '<?php echo $sch['id']; ?>')"><i class="fas fa-edit"></i></button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'rules'): ?>
                <!-- Screen 2.3.2 — Eligibility Rule Builder -->
                <div class="header-with-back">
                    <a href="?view=list" class="back-button"><i class="fas fa-arrow-left"></i></a>
                    <div class="header-content" style="flex: 1;">
                        <h1 class="page-title">Eligibility Rule Builder</h1>
                        <p class="page-subtitle">Configuring logic for: <strong>Post Matric Scholarship (SC)</strong></p>
                        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                            <span style="font-size: 0.8rem; color: var(--text-secondary);"><strong style="color: white;">Active Version:</strong> v2.1</span>
                            <button class="btn btn-primary" onclick="handleScholarshipAction('save_rules', 'v2.2')"><i class="fas fa-save"></i> Save v2.2</button>
                        </div>
                    </div>
                </div>

                <div class="rules-grid">
                    <div class="rules-column">
                        <div class="rules-section">
                            <h2><i class="fas fa-sitemap" style="margin-right: 10px;"></i>Visual Rule Logic</h2>
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

                                <button class="btn btn-secondary" style="margin-top: 1rem; width: 100%;" onclick="handleScholarshipAction('add_rule', 'new')">+ Add Logical Condition</button>

                                <div style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                                    <span style="font-size: 1rem; font-weight: 700;">THEN RESULT = </span>
                                    <span class="status-badge status-approved" style="font-size: 1rem;">QUALIFIED</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rules-column">
                        <div class="rules-section">
                            <h2><i class="fas fa-flask" style="margin-right: 10px;"></i>Test Rule against Profile</h2>
                            <div class="test-profile-card" style="margin-top: 1.5rem; background: transparent; border: none; padding: 0;">
                                <div style="margin-bottom: 1.5rem;">
                                    <label style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">Student Category</label>
                                    <select class="btn" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; margin-top: 8px; padding: 10px; cursor: pointer;">
                                        <option>SC</option>
                                        <option>OBC</option>
                                        <option>General</option>
                                    </select>
                                </div>
                                <div style="margin-bottom: 1.5rem;">
                                    <label style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">Annual Income (₹)</label>
                                    <input type="number" value="180000" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px; margin-top: 8px;">
                                </div>
                                <div style="margin-bottom: 1.5rem;">
                                    <label style="font-size: 0.8rem; color: var(--text-secondary); font-weight: 600;">12th Percentage (%)</label>
                                    <input type="number" value="72" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px; margin-top: 8px;">
                                </div>
                                <button class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 12px;" onclick="handleScholarshipAction('simulate', 'test_profile')">Run Simulation</button>
                                
                                <div style="margin-top: 2rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); padding: 1.5rem; border-radius: 12px; text-align: center;">
                                    <div style="font-size: 1rem; color: var(--success); font-weight: 700;"><i class="fas fa-check-circle"></i> ELIGIBLE</div>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 8px;">Passed all 3 criteria with 100% confidence.</p>
                                </div>
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

                <div class="widget w-full glass-card">
                    <?php
                    $stmt = $pdo->query("SELECT * FROM scholarships WHERE is_verified = 0 ORDER BY created_at DESC");
                    $pending_scholarships = $stmt->fetchAll();
                    
                    if (empty($pending_scholarships)): ?>
                        <div style="text-align: center; padding: 4rem; color: var(--text-secondary);">
                            <i class="fas fa-check-double" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p>Queue is empty. All college submissions have been processed.</p>
                        </div>
                    <?php else:
                        foreach ($pending_scholarships as $sch): ?>
                        <div class="review-item" style="background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <div>
                                    <h3 style="color: var(--accent-primary);"><?php echo htmlspecialchars($sch['name']); ?></h3>
                                    <div style="font-size: 0.8rem; color: var(--text-secondary);">Submitted by: <strong><?php echo htmlspecialchars($sch['provider_name']); ?></strong> &bull; <?php echo date('d M Y', strtotime($sch['created_at'])); ?></div>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <button class="btn btn-primary" onclick="handleScholarshipAction('approve', '<?php echo $sch['id']; ?>')">Approve & Publish</button>
                                    <button class="btn btn-secondary" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--danger);" onclick="handleScholarshipAction('reject', '<?php echo $sch['id']; ?>')">Reject Submission</button>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-top: 1.5rem; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px;">
                                <div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Scholarship Amount</div>
                                    <div style="font-weight: 700;"><?php echo htmlspecialchars($sch['amount_description'] ?: '₹ ' . number_format($sch['amount_inr'])); ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Category</div>
                                    <div style="font-weight: 700;"><?php echo htmlspecialchars($sch['category']); ?></div>
                                </div>
                                <div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Verification Docs</div>
                                    <div><a href="<?php echo htmlspecialchars($sch['application_link']); ?>" target="_blank" style="color: var(--accent-secondary); font-size: 0.8rem;"><i class="fas fa-file-pdf"></i> View Prospectus.pdf</a></div>
                                </div>
                            </div>
                            <div style="margin-top: 1.5rem;">
                                <h4 style="font-size: 0.85rem; margin-bottom: 5px;">About Scholarship</h4>
                                <p style="font-size: 0.8rem; color: var(--text-secondary);"><?php echo htmlspecialchars($sch['about_scholarship']); ?></p>
                            </div>
                        </div>
                        <?php endforeach;
                    endif; ?>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
    <script>
        async function handleScholarshipAction(action, target) {
            Swal.fire({
                title: 'Scholarship Operations',
                text: 'Interfacing with statutory grant engine...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('admin_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, target, module: 'scholarships' })
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
                console.error("Scholarship API Error:", error);
                Swal.fire({ icon: 'error', title: 'Connection Failure', text: 'Administrative API is currently unreachable.' });
            }
        }
    </script>
</body>
</html>
