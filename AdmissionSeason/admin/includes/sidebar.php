<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-graduation-cap"></i>
            EduSearch
        </div>
        <div class="sidebar-close" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </div>
    </div>
    
    <div class="sidebar-nav">
        <div class="nav-group-title">Main</div>
        <a href="index.php" class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        
        <div class="nav-group-title">Entity Management</div>
        <a href="colleges.php" class="nav-item <?php echo $current_page == 'colleges.php' ? 'active' : ''; ?>">
            <i class="fas fa-university"></i> Colleges
        </a>
        <a href="exams.php" class="nav-item <?php echo $current_page == 'exams.php' ? 'active' : ''; ?>">
            <i class="fas fa-file-signature"></i> Exams
        </a>
        <a href="scholarships.php" class="nav-item <?php echo $current_page == 'scholarships.php' ? 'active' : ''; ?>">
            <i class="fas fa-hand-holding-usd"></i> Scholarships
        </a>
        <a href="study_abroad.php" class="nav-item <?php echo $current_page == 'study_abroad.php' ? 'active' : ''; ?>">
            <i class="fas fa-plane-departure"></i> Study Abroad
        </a>
        
        <div class="nav-group-title">Moderation</div>
        <a href="reviews.php" class="nav-item <?php echo $current_page == 'reviews.php' ? 'active' : ''; ?>">
            <i class="fas fa-star"></i> Reviews
        </a>
        <a href="qa.php" class="nav-item <?php echo $current_page == 'qa.php' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i> Q&A
        </a>
        
        <div class="nav-group-title">Operations</div>
        <a href="leads.php" class="nav-item <?php echo $current_page == 'leads.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-graduate"></i> Leads
        </a>
        <a href="billing.php" class="nav-item <?php echo $current_page == 'billing.php' ? 'active' : ''; ?>">
            <i class="fas fa-credit-card"></i> Billing
        </a>
        
        <div class="nav-group-title">System</div>
        <a href="users.php" class="nav-item <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Student Accounts
        </a>
        <a href="security.php" class="nav-item <?php echo $current_page == 'security.php' ? 'active' : ''; ?>">
            <i class="fas fa-shield-alt"></i> Security & Access
        </a>
        <a href="ai_ops.php" class="nav-item <?php echo $current_page == 'ai_ops.php' ? 'active' : ''; ?>">
            <i class="fas fa-robot"></i> AI Operations
        </a>
        <a href="analytics.php" class="nav-item <?php echo $current_page == 'analytics.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-pie"></i> Growth & Analytics
        </a>
        <a href="scrapers.php" class="nav-item <?php echo $current_page == 'scrapers.php' ? 'active' : ''; ?>">
            <i class="fas fa-spider"></i> Scrapers
        </a>
        <a href="infra.php" class="nav-item <?php echo $current_page == 'infra.php' ? 'active' : ''; ?>">
            <i class="fas fa-server"></i> Infrastructure
        </a>
        <a href="backup.php" class="nav-item <?php echo $current_page == 'backup.php' ? 'active' : ''; ?>">
            <i class="fas fa-database"></i> Backup & Recovery
        </a>
        <a href="seo.php" class="nav-item <?php echo $current_page == 'seo.php' ? 'active' : ''; ?>">
            <i class="fas fa-search-plus"></i> SEO Panel
        </a>
        <a href="notifications.php" class="nav-item <?php echo $current_page == 'notifications.php' ? 'active' : ''; ?>">
            <i class="fas fa-bullhorn"></i> Notifications
        </a>
        <a href="audit.php" class="nav-item <?php echo $current_page == 'audit.php' ? 'active' : ''; ?>">
            <i class="fas fa-history"></i> Audit Log
        </a>
        <a href="dpdp.php" class="nav-item <?php echo $current_page == 'dpdp.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-lock"></i> DPDP Compliance
        </a>
    </div>
    
    <div style="margin-top: auto; padding: 20px; border-top: 1px solid var(--border-color); background: rgba(0,0,0,0.1);">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
            <div style="width: 35px; height: 35px; background: var(--accent-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: white;">
                <?php echo strtoupper(substr(isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'A', 0, 1)); ?>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 700; color: white;"><?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin'; ?></div>
                <div style="font-size: 0.65rem; color: var(--text-secondary);"><?php echo isset($_SESSION['admin_role']) ? str_replace('_', ' ', htmlspecialchars($_SESSION['admin_role'])) : 'Super Admin'; ?></div>
            </div>
        </div>
        <a href="logout.php" style="display: flex; align-items: center; gap: 10px; color: var(--danger); text-decoration: none; font-size: 0.85rem; font-weight: 700;">
            <i class="fas fa-sign-out-alt"></i> Logout System
        </a>
    </div>
</div>
