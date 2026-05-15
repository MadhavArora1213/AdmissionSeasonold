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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

                <?php if ($view == 'queue'): 
                    $filter = $_GET['filter'] ?? 'pending';
                    $countPending = $pdo->query("SELECT COUNT(*) FROM college_qa WHERE status = 'PENDING'")->fetchColumn();
                    $countReported = $pdo->query("SELECT COUNT(*) FROM college_qa WHERE report_count > 0")->fetchColumn();
                ?>
                <!-- Screen 3.2.1 — Q&A Moderation Queue -->
                <div class="widget w-full glass-card">
                    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 10px;">
                            <a href="?view=queue&filter=pending" class="btn <?php echo $filter == 'pending' ? 'btn-primary' : 'btn-secondary'; ?>" style="font-size: 0.8rem;">Pending (<?php echo $countPending; ?>)</a>
                            <a href="?view=queue&filter=reported" class="btn <?php echo $filter == 'reported' ? 'btn-primary' : 'btn-secondary'; ?>" style="font-size: 0.8rem;"><i class="fas fa-flag"></i> Reported (<?php echo $countReported; ?>)</a>
                        </div>
                        <div class="header-search" style="width: 250px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search Q&A...">
                        </div>
                    </div>

                    <?php
                    // Fetch filtered questions
                    $sql = "SELECT q.*, c.name as college_name FROM college_qa q JOIN colleges c ON q.college_id = c.id ";
                    if ($filter == 'reported') {
                        $sql .= "WHERE q.report_count > 0 ORDER BY q.report_count DESC";
                    } else {
                        $sql .= "WHERE q.status = 'PENDING' ORDER BY q.created_at DESC";
                    }
                    
                    $stmtQ = $pdo->query($sql);
                    $questions = $stmtQ->fetchAll();
                    
                    if (empty($questions)): ?>
                        <div style="text-align: center; padding: 4rem; color: var(--text-secondary);">
                            <i class="fas fa-comments" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                            <p>No <?php echo $filter; ?> questions found.</p>
                        </div>
                    <?php else:
                        foreach ($questions as $q): ?>
                        <div class="qa-item <?php echo $filter == 'reported' ? 'reported' : 'question'; ?>">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <span class="qa-type" style="background: <?php echo $q['report_count'] > 0 ? 'var(--danger)' : 'rgba(99, 102, 241, 0.1)'; ?>; color: <?php echo $q['report_count'] > 0 ? 'white' : 'var(--accent-primary)'; ?>;">
                                        <?php echo $q['report_count'] > 0 ? 'REPORTED (' . $q['report_count'] . ' TIMES)' : 'QUESTION PENDING'; ?>
                                    </span>
                                    <h3 style="color: white; margin-bottom: 5px;">"<?php echo htmlspecialchars($q['question']); ?>"</h3>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                        Asker: <strong><?php echo $q['is_anonymous'] ? 'Anonymous' : 'User_'.substr($q['asked_by'], 0, 5); ?></strong> 
                                        &bull; <?php echo date('d M Y', strtotime($q['created_at'])); ?> 
                                        &bull; College: <?php echo htmlspecialchars($q['college_name']); ?>
                                    </div>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleQAAction('approve', '<?php echo $q['id']; ?>')">Approve</button>
                                    <?php if($q['report_count'] > 0): ?>
                                        <button class="btn btn-secondary" style="font-size: 0.75rem; background: rgba(239, 68, 68, 0.1); color: var(--danger);" onclick="handleQAAction('delete', '<?php echo $q['id']; ?>')"><i class="fas fa-trash"></i> Delete</button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleQAAction('edit', '<?php echo $q['id']; ?>')"><i class="fas fa-edit"></i> Edit</button>
                                    <?php endif; ?>
                                    <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleQAAction('reject', '<?php echo $q['id']; ?>')">Reject</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach;
                    endif; ?>
                </div>

                <?php elseif ($view == 'badges'): ?>
                <!-- Screen 3.2.2 — Badge Verification Centre -->
                <div class="widget w-full glass-card">
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
                            <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleQAAction('verify', 'Badge')"><i class="fas fa-check-circle"></i> Verify & Award Badge</button>
                            <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleQAAction('reject', 'Badge')">Reject</button>
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
                                <td><button class="btn btn-secondary" style="font-size: 0.7rem; padding: 4px 8px;" onclick="handleQAAction('revoke', 'Ananya Mishra')">Revoke Badge</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'analytics'): ?>
                <!-- Q&A Content Quality Analytics -->
                <div class="dashboard-grid">
                    <div class="widget w-full glass-card">
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
                                    <td><button class="btn btn-primary" style="font-size: 0.7rem;" onclick="handleQAAction('assign', 'Hostel Fee')">Assign Data Entry</button></td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 700;">Lateral Entry Eligibility</td>
                                    <td>118</td>
                                    <td style="font-size: 0.8rem;">DTU, NSUT, Anna Univ</td>
                                    <td><span class="status-badge status-pending">MEDIUM</span></td>
                                    <td><button class="btn btn-primary" style="font-size: 0.7rem;" onclick="handleQAAction('assign', 'Lateral Entry')">Assign Data Entry</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
    <script>
        async function handleQAAction(action, target) {
            Swal.fire({
                title: 'Q&A Moderation',
                text: 'Interfacing with community knowledge base...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('admin_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, target, module: 'community_qa' })
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
                console.error("QA API Error:", error);
                Swal.fire({ icon: 'error', title: 'Connection Failure', text: 'Administrative API is currently unreachable.' });
            }
        }
    </script>
</body>
</html>
