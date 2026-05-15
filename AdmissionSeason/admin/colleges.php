<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'list';
// Only treat 'view' as a college ID if it's not 'list', 'verify', 'diff', or 'duplicates'
$special_views = ['list', 'verify', 'diff', 'duplicates'];
$collegeId = $_GET['edit'] ?? (!in_array($view, $special_views) ? $view : null);
$mode = isset($_GET['edit']) ? 'edit' : ($collegeId && !in_array($view, $special_views) ? 'view' : null);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Management | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .college-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .coll-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .coll-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .kanban-board { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; overflow-x: auto; padding-bottom: 1rem; }
        .kanban-col { background: rgba(0,0,0,0.2); border-radius: 12px; min-height: 500px; padding: 10px; }
        .kanban-header { font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 1rem; padding: 5px 10px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; }
        .kanban-card { background: var(--sidebar-bg); border: 1px solid var(--border-color); padding: 1rem; border-radius: 8px; margin-bottom: 10px; cursor: grab; }
        .kanban-card:active { cursor: grabbing; }
        
        .diff-view { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 10px; padding: 10px; border-bottom: 1px solid var(--border-color); }
        .diff-old { color: var(--danger); text-decoration: line-through; opacity: 0.6; }
        .diff-new { color: var(--success); font-weight: 700; }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            animation: fadeIn 0.3s ease;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
        }

        .close-btn:hover {
            color: var(--text-primary);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: inherit;
        }

        .form-group select {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.7em center;
            background-size: 1.2em auto;
            padding-right: 2.5rem;
            appearance: none;
            cursor: pointer;
        }

        .form-group select option {
            background: #0f172a;
            color: #f8fafc;
            padding: 0.8rem;
        }

        .form-group select option:checked {
            background: linear-gradient(var(--accent-primary), var(--accent-primary));
            background-color: var(--accent-primary);
        }

        .form-group select option:hover {
            background: rgba(99, 102, 241, 0.2);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-primary);
            background: rgba(99, 102, 241, 0.1);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        /* Custom Dropdown Styles */
        .custom-select {
            position: relative;
            width: 100%;
        }

        .custom-select-btn {
            width: 100%;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            text-align: left;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .custom-select-btn:hover {
            border-color: var(--accent-primary);
            background: rgba(99, 102, 241, 0.05);
        }

        .custom-select-btn.open {
            border-color: var(--accent-primary);
            background: rgba(99, 102, 241, 0.1);
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .custom-select-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
        }

        .custom-select-btn.open .custom-select-icon {
            transform: rotate(180deg);
        }

        .custom-select-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #0f172a;
            border: 1px solid var(--accent-primary);
            border-top: none;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1001;
            display: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        .custom-select-dropdown.show {
            display: block;
        }

        .custom-select-option {
            padding: 0.8rem 1rem;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .custom-select-option:last-child {
            border-bottom: none;
        }

        .custom-select-option:hover {
            background: rgba(99, 102, 241, 0.2);
            color: var(--accent-primary);
        }

        .custom-select-option.selected {
            background: rgba(99, 102, 241, 0.3);
            color: var(--accent-primary);
            font-weight: 600;
        }

        .custom-select-hidden {
            display: none;
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
                        <h1 class="page-title">College & Course Management</h1>
                        <p class="page-subtitle">Managing 30,000+ institution profiles and verification pipelines.</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="?view=verify" class="btn" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fas fa-shield-alt"></i> Verification Pipeline</a>
                        <button class="btn btn-primary" onclick="openAddCollegeModal()"><i class="fas fa-plus"></i> Add College</button>
                    </div>
                </div>

                <?php if ($mode && $collegeId): ?>
                <!-- Edit/View Mode -->
                <div class="widget w-full" style="margin-bottom: 1.5rem;">
                    <div class="widget-header">
                        <h3 class="widget-title"><?php echo $mode === 'edit' ? 'Edit College' : 'View College'; ?> (ID: <?php echo htmlspecialchars($collegeId); ?>)</h3>
                        <a href="?view=list" class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.9rem;"><i class="fas fa-arrow-left"></i> Back to List</a>
                    </div>
                    <div style="padding: 1.5rem; background: rgba(99, 102, 241, 0.05); border-radius: 8px; border: 1px solid var(--border-color);">
                        <p style="color: var(--text-secondary);">
                            <?php 
                            if ($mode === 'edit') {
                                echo 'Edit form for College ID: ' . htmlspecialchars($collegeId);
                            } else {
                                echo 'Details for College ID: ' . htmlspecialchars($collegeId);
                            }
                            ?>
                        </p>
                        <p style="color: var(--text-secondary); margin-top: 10px; font-size: 0.85rem;">
                            (Add your form/details here)
                        </p>
                    </div>
                </div>
                <?php endif; ?>


                <div class="college-tabs">
                    <a href="?view=list" class="coll-tab <?php echo $view == 'list' ? 'active' : ''; ?>">Active Colleges</a>
                    <a href="?view=verify" class="coll-tab <?php echo $view == 'verify' ? 'active' : ''; ?>">Verification Kanban</a>
                    <a href="?view=diff" class="coll-tab <?php echo $view == 'diff' ? 'active' : ''; ?>">Data Diff Viewer</a>
                    <a href="?view=duplicates" class="coll-tab <?php echo $view == 'duplicates' ? 'active' : ''; ?>">Duplicate Detection</a>
                </div>

                <?php if ($view == 'list'): ?>
                <!-- Screen 2.1.1 — College CMS Table -->
                <div class="widget w-full">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                        <div class="header-search" style="width: 300px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search by name, city or type...">
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <select class="btn" style="background: var(--sidebar-bg); color: white; border: 1px solid var(--border-color); font-size: 0.8rem;">
                                <option>All States</option>
                                <option>Karnataka</option>
                                <option>Maharashtra</option>
                            </select>
                            <button class="btn" style="font-size: 0.75rem; background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">Filter by Type</button>
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>College Name</th>
                                <th>Location</th>
                                <th>NIRF Rank</th>
                                <th>Admissions</th>
                                <th>Data Freshness</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM colleges ORDER BY (nirf_rank IS NULL), nirf_rank ASC, created_at DESC LIMIT 50");
                            $colleges = $stmt->fetchAll();
                            
                            if (empty($colleges)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                        No colleges found in database. <a href="seed_colleges.php" style="color: var(--accent-primary);">Run seeder?</a>
                                    </td>
                                </tr>
                            <?php else:
                                foreach ($colleges as $college): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 700; color: var(--accent-primary);"><?php echo htmlspecialchars($college['name']); ?></div>
                                            <div style="font-size: 0.7rem; color: var(--text-secondary);"><?php echo htmlspecialchars($college['type']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($college['city'] . ', ' . $college['state']); ?></td>
                                        <td><span style="font-weight: 700; color: var(--accent-secondary);">#<?php echo htmlspecialchars($college['nirf_rank']); ?></span></td>
                                        <td><span class="status-badge status-approved">OPEN</span></td>
                                        <td><span style="color: var(--success); font-size: 0.8rem;"><i class="fas fa-check-circle"></i> Live</span></td>
                                        <td><span class="status-badge status-approved"><?php echo htmlspecialchars($college['is_verified'] ? 'Verified' : 'Pending'); ?></span></td>
                                        <td>
                                            <div style="display: flex; gap: 10px;">
                                                <button class="action-btn" onclick="editCollege('<?php echo $college['id']; ?>')" title="Edit"><i class="fas fa-edit"></i></button>
                                                <button class="action-btn" onclick="viewCollege('<?php echo $college['id']; ?>')" title="View"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; 
                            endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'verify'): ?>
                <!-- Screen 2.1.3 — Verification Kanban -->
                <div class="kanban-board">
                    <div class="kanban-col">
                        <div class="kanban-header">Submitted <span>4</span></div>
                        <div class="kanban-card">
                            <div style="font-weight: 700; font-size: 0.85rem;">Galgotias University</div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 5px;">Source: College Dashboard</div>
                            <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                                <span class="status-badge" style="background: rgba(255,255,255,0.05); font-size: 0.6rem;">MBA, B.Tech</span>
                                <i class="fas fa-paperclip text-secondary"></i>
                            </div>
                        </div>
                    </div>
                    <div class="kanban-col">
                        <div class="kanban-header">In Review <span>2</span></div>
                        <div class="kanban-card" style="border-left: 3px solid var(--warning);">
                            <div style="font-weight: 700; font-size: 0.85rem;">Amity University</div>
                            <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 5px;">Checking Accreditation Docs</div>
                        </div>
                    </div>
                    <div class="kanban-col">
                        <div class="kanban-header">Clarification <span>1</span></div>
                    </div>
                    <div class="kanban-col">
                        <div class="kanban-header">Approved <span>420</span></div>
                    </div>
                </div>

                <?php elseif ($view == 'diff'): ?>
                <!-- Screen 2.1.4 — Data Diff Viewer -->
                <div class="widget w-full">
                    <div class="widget-header">
                        <h3 class="widget-title">Conflict Resolver (Scraped vs Submitted)</h3>
                        <span class="status-badge status-pending">8 Conflicts Detected</span>
                    </div>
                    
                    <div style="background: rgba(30, 41, 59, 0.4); padding: 1.5rem; border-radius: 12px; margin-top: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <h4 style="color: white;">College: IIT Madras</h4>
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleConflictAction('trust_submitted', 'IIT Madras')">Trust Submitted (Keep New)</button>
                                <button class="btn" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.75rem;" onclick="handleConflictAction('trust_scraper', 'IIT Madras')">Trust Scraper</button>
                            </div>
                        </div>
                        <div class="diff-view" style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary);">
                            <div>Field</div>
                            <div>Current (Live)</div>
                            <div>Proposed (New)</div>
                        </div>
                        <div class="diff-view">
                            <div style="font-weight: 700;">Total B.Tech Seats</div>
                            <div class="diff-old">1,120</div>
                            <div class="diff-new">1,240</div>
                        </div>
                        <div class="diff-view">
                            <div style="font-weight: 700;">Average Placement</div>
                            <div class="diff-old">₹ 18.2 LPA</div>
                            <div class="diff-new">₹ 21.4 LPA</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Add College Modal -->
    <div id="addCollegeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-plus-circle" style="margin-right: 10px;"></i>Add New College</h2>
                <button class="close-btn" onclick="closeAddCollegeModal()">&times;</button>
            </div>
            
            <form id="addCollegeForm" onsubmit="handleAddCollege(event)">
                <div class="form-group">
                    <label for="collegeName">College Name *</label>
                    <input type="text" id="collegeName" name="collegeName" placeholder="e.g., BITS Pilani" required>
                </div>

                <div class="form-group">
                    <label for="collegeType">College Type *</label>
                    <div class="custom-select">
                        <button type="button" class="custom-select-btn" data-select="collegeType">
                            <span id="collegeType-display">Select Type</span>
                            <span class="custom-select-icon"><i class="fas fa-chevron-down"></i></span>
                        </button>
                        <div class="custom-select-dropdown">
                            <div class="custom-select-option" data-value="">Select Type</div>
                            <div class="custom-select-option" data-value="GOVERNMENT">Government</div>
                            <div class="custom-select-option" data-value="PRIVATE">Private</div>
                            <div class="custom-select-option" data-value="DEEMED">Deemed University</div>
                            <div class="custom-select-option" data-value="CENTRAL">Central University</div>
                        </div>
                    </div>
                    <input type="hidden" id="collegeType" name="collegeType" required>
                </div>

                <div class="form-group">
                    <label for="state">State *</label>
                    <input type="text" id="state" name="state" placeholder="e.g., Rajasthan" required>
                </div>

                <div class="form-group">
                    <label for="city">City *</label>
                    <input type="text" id="city" name="city" placeholder="e.g., Pilani" required>
                </div>

                <div class="form-group">
                    <label for="location">Full Location</label>
                    <input type="text" id="location" name="location" placeholder="e.g., Pilani, Rajasthan">
                </div>

                <div class="form-group">
                    <label for="nirfRank">NIRF Rank</label>
                    <input type="number" id="nirfRank" name="nirfRank" placeholder="e.g., 25">
                </div>

                <div class="form-group">
                    <label for="admissionsStatus">Admissions Status *</label>
                    <div class="custom-select">
                        <button type="button" class="custom-select-btn" data-select="admissionsStatus">
                            <span id="admissionsStatus-display">Select Status</span>
                            <span class="custom-select-icon"><i class="fas fa-chevron-down"></i></span>
                        </button>
                        <div class="custom-select-dropdown">
                            <div class="custom-select-option" data-value="">Select Status</div>
                            <div class="custom-select-option" data-value="OPEN">OPEN</div>
                            <div class="custom-select-option" data-value="CLOSED">CLOSED</div>
                            <div class="custom-select-option" data-value="PAUSED">PAUSED</div>
                        </div>
                    </div>
                    <input type="hidden" id="admissionsStatus" name="admissionsStatus" required>
                </div>

                <div class="form-group">
                    <label for="website">Website URL</label>
                    <input type="url" id="website" name="website" placeholder="https://example.com">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter college description..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeAddCollegeModal()" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save College</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editCollege(collegeId) {
            window.location.href = '?view=list&edit=' + collegeId;
        }

        function viewCollege(collegeId) {
            window.location.href = '?view=list&view=' + collegeId;
        }

        function openAddCollegeModal() {
            document.getElementById('addCollegeModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeAddCollegeModal() {
            document.getElementById('addCollegeModal').classList.remove('show');
            document.body.style.overflow = 'auto';
            document.getElementById('addCollegeForm').reset();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addCollegeModal');
            if (event.target === modal) {
                closeAddCollegeModal();
            }
        }

        // Custom Dropdown Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const selectBtns = document.querySelectorAll('.custom-select-btn');
            
            selectBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdown = this.nextElementSibling;
                    const allDropdowns = document.querySelectorAll('.custom-select-dropdown');
                    
                    // Close all other dropdowns
                    allDropdowns.forEach(d => {
                        if (d !== dropdown) {
                            d.classList.remove('show');
                        }
                    });
                    
                    // Toggle current dropdown
                    dropdown.classList.toggle('show');
                    this.classList.toggle('open');
                });
            });
            
            // Handle option selection
            const options = document.querySelectorAll('.custom-select-option');
            options.forEach(option => {
                option.addEventListener('click', function() {
                    const selectId = this.parentElement.previousElementSibling.dataset.select;
                    const value = this.dataset.value;
                    const display = this.textContent;
                    
                    // Update hidden input
                    document.getElementById(selectId).value = value;
                    
                    // Update display text
                    document.getElementById(selectId + '-display').textContent = display;
                    
                    // Update selected state
                    const siblings = this.parentElement.querySelectorAll('.custom-select-option');
                    siblings.forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');
                    
                    // Close dropdown
                    this.parentElement.classList.remove('show');
                    this.parentElement.previousElementSibling.classList.remove('open');
                });
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.custom-select')) {
                    document.querySelectorAll('.custom-select-dropdown').forEach(d => {
                        d.classList.remove('show');
                    });
                    document.querySelectorAll('.custom-select-btn').forEach(b => {
                        b.classList.remove('open');
                    });
                }
            });
        });


        async function handleAddCollege(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('addCollegeForm'));
            const data = {
                collegeName: formData.get('collegeName'),
                collegeType: formData.get('collegeType'),
                state: formData.get('state'),
                city: formData.get('city'),
                location: formData.get('location'),
                nirfRank: formData.get('nirfRank'),
                admissionsStatus: formData.get('admissionsStatus'),
                website: formData.get('website'),
                description: formData.get('description')
            };

            Swal.fire({
                title: 'Saving College...',
                text: 'Please wait while we add ' + data.collegeName,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Simulate adding college (replace with actual API call)
            try {
                const response = await fetch('add_college_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'College Added!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        closeAddCollegeModal();
                        location.reload(); 
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while adding the college.' });
            }
        }
        async function handleConflictAction(action, target) {
            Swal.fire({
                title: 'Resolving Conflict',
                text: 'Updating master record and syncing with scrapers...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('admin_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, target, module: 'scrapers' })
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Conflict Resolved',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({ icon: 'error', title: 'Connection Error', text: 'Administrative API is currently unreachable.' });
            }
        }
    </script>
</body>
</html>
