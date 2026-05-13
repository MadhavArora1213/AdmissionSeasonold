<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'students';
$user_id = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .user-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .u-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .u-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .masked-field { font-family: monospace; letter-spacing: 1px; color: var(--text-secondary); }
        .super-visible { color: var(--accent-primary) !important; font-weight: 700; }
        
        .detail-card { background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; }
        .activity-item { padding: 12px; border-left: 2px solid var(--accent-primary); background: rgba(99, 102, 241, 0.03); margin-bottom: 8px; border-radius: 0 8px 8px 0; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                
                <?php if (!$user_id): ?>
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Identity & Account Management</h1>
                        <p class="page-subtitle">Managing platform users with role-based visibility and privacy compliance.</p>
                    </div>
                </div>

                <div class="user-tabs">
                    <a href="?view=students" class="u-tab <?php echo $view == 'students' ? 'active' : ''; ?>">Student Accounts</a>
                    <a href="?view=college_users" class="u-tab <?php echo $view == 'college_users' ? 'active' : ''; ?>">College B2B Users</a>
                    <a href="?view=counselors" class="u-tab <?php echo $view == 'counselors' ? 'active' : ''; ?>">Premium Counselors</a>
                    <a href="?view=admins" class="u-tab <?php echo $view == 'admins' ? 'active' : ''; ?>">Admin RBAC</a>
                </div>

                <!-- Screen 5.1.1 — Student User Table -->
                <div class="widget w-full">
                    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 10px;">
                            <div class="header-search" style="width: 300px;">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Search users...">
                            </div>
                        </div>
                    </div>

                    <?php if ($view == 'students'): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email / Phone</th>
                                <th>Login Method</th>
                                <th>Activity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div style="font-weight: 700;">Vikram Aditya</div>
                                    <div style="font-size: 0.65rem; color: var(--text-secondary);">ID: #STU_8219</div>
                                </td>
                                <td>
                                    <div class="masked-field">vik***@gmail.com</div>
                                    <div class="masked-field">XXXXXX1292</div>
                                </td>
                                <td><span style="font-size: 0.8rem;"><i class="fab fa-google"></i> OAuth</span></td>
                                <td>
                                    <div style="font-size: 0.8rem; font-weight: 700;">12 Leads / 4 Reviews</div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">Active 5m ago</div>
                                </td>
                                <td><span class="status-badge status-approved">Active</span></td>
                                <td><a href="?id=8219" class="action-btn"><i class="fas fa-id-badge"></i> View Detail</a></td>
                            </tr>
                        </tbody>
                    </table>

                    <?php elseif ($view == 'counselors'): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Counselor Name</th>
                                <th>Specialization</th>
                                <th>Hourly Rate</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div style="font-weight: 700;">Dr. Shalini Sharma</div>
                                    <div style="font-size: 0.65rem; color: var(--text-secondary);">Ph.D. Education (DU)</div>
                                </td>
                                <td>MBA & Study Abroad</td>
                                <td>₹ 2,500 / hr</td>
                                <td><i class="fas fa-star" style="color: #f59e0b;"></i> 4.9 (142 reviews)</td>
                                <td><span class="status-badge status-approved">Verified</span></td>
                                <td><button class="action-btn"><i class="fas fa-calendar-alt"></i> Bookings</button></td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="font-weight: 700;">Ankit Mehta</div>
                                    <div style="font-size: 0.65rem; color: var(--text-secondary);">Ex-Admissions (BIT Mesra)</div>
                                </td>
                                <td>Engineering & B.Tech</td>
                                <td>₹ 1,500 / hr</td>
                                <td><i class="fas fa-star" style="color: #f59e0b;"></i> 4.7 (82 reviews)</td>
                                <td><span class="status-badge status-approved">Verified</span></td>
                                <td><button class="action-btn"><i class="fas fa-calendar-alt"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <?php else: ?>
                <!-- Screen 5.1.2 — Student Account Detail -->
                <div class="page-header">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <a href="users.php" class="action-btn" style="padding: 10px;"><i class="fas fa-arrow-left"></i></a>
                        <div>
                            <h1 class="page-title">Student Profile: Vikram Aditya</h1>
                            <p class="page-subtitle">Full data audit and administrative control console.</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--danger); color: var(--danger);">Suspend Account</button>
                        <button class="btn btn-primary">Reset Credentials</button>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="widget w-third">
                        <h3 class="widget-title">Personal Info (Admin View)</h3>
                        <div style="margin-top: 1.5rem; background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px;">
                            <div style="margin-bottom: 1rem;">
                                <label style="font-size: 0.7rem; color: var(--text-secondary);">Full Name</label>
                                <div style="font-weight: 700;">Vikram Aditya</div>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label style="font-size: 0.7rem; color: var(--text-secondary);">Verified Email</label>
                                <div class="super-visible">vikram.aditya.92@gmail.com</div>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label style="font-size: 0.7rem; color: var(--text-secondary);">Verified Phone</label>
                                <div class="super-visible">+91 98124 51292</div>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <label style="font-size: 0.7rem; color: var(--text-secondary);">Login Method</label>
                                <div>Google OAuth &bull; <span style="font-size: 0.7rem; color: var(--text-secondary);">IP: 103.21.12.XXX</span></div>
                            </div>
                        </div>

                        <h3 class="widget-title" style="margin-top: 2rem;">Lead History (Current)</h3>
                        <div style="margin-top: 1rem;">
                            <div class="activity-item">
                                <strong>BITS Pilani (B.Tech CSE)</strong>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Submitted: 12 May 2026 &bull; Quality: 9.2</div>
                            </div>
                            <div class="activity-item">
                                <strong>VIT Vellore (MBA)</strong>
                                <div style="font-size: 0.7rem; color: var(--text-secondary);">Submitted: 10 May 2026 &bull; Quality: 8.8</div>
                            </div>
                        </div>
                    </div>

                    <div class="widget w-two-thirds">
                        <h3 class="widget-title">Full Platform Activity Log</h3>
                        <div style="margin-top: 1.5rem; max-height: 500px; overflow-y: auto; padding-right: 10px;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Action</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="font-size: 0.75rem;">13 May 2026, 08:42:10</td>
                                        <td><span class="status-badge" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">VIEW</span></td>
                                        <td style="font-size: 0.8rem;">Viewed College Profile: <strong>IIT Delhi</strong></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 0.75rem;">13 May 2026, 08:35:42</td>
                                        <td><span class="status-badge" style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">AI_COUNSEL</span></td>
                                        <td style="font-size: 0.8rem;">Query: "Which NIT has best placements for ECE?"</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 0.75rem;">13 May 2026, 08:30:15</td>
                                        <td><span class="status-badge" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);">SEARCH</span></td>
                                        <td style="font-size: 0.8rem;">Searched: "Top MBA colleges in Pune"</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>
</html>
