<header class="header">
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
    <div class="header-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search for colleges, students, or leads...">
    </div>
    
    <div class="header-actions">
        <div class="action-btn">
            <i class="far fa-bell"></i>
            <span class="badge">12</span>
        </div>
        <div class="action-btn">
            <i class="far fa-envelope"></i>
        </div>
        
        <div class="user-profile">
            <div class="user-avatar">AD</div>
            <div class="user-info" style="display: flex; flex-direction: column;">
                <span style="font-size: 0.85rem; font-weight: 600;">Super Admin</span>
                <span style="font-size: 0.7rem; color: var(--text-secondary);">EduSearch Platform</span>
            </div>
            <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-left: 10px;"></i>
        </div>
    </div>
</header>

<div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

<script>
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    
    // Prevent scrolling when sidebar is open on mobile
    if(sidebar.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
}
</script>
