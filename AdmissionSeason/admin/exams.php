<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'list';
$exam_id = $_GET['id'] ?? null;

// Fetch Exam Data if in edit mode
$exam = null;
if ($exam_id) {
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->execute([$exam_id]);
    $exam = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams & Cutoffs | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .exam-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .exam-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .exam-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .matching-row { background: rgba(0,0,0,0.1); padding: 10px; border-radius: 8px; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #ccc; }
        .matching-row.exact { border-left-color: var(--success); }
        .matching-row.high { border-left-color: var(--accent-primary); }
        .matching-row.low { border-left-color: var(--danger); }
        .tuning-slider { width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; outline: none; -webkit-appearance: none; }
        .tuning-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 15px; height: 15px; background: var(--accent-primary); border-radius: 50%; cursor: pointer; }

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
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                
                <?php if ($view == 'list'): ?>
                <!-- Screen 2.2.1 — Exam CMS Table -->
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Exam & Cutoff Database</h1>
                        <p class="page-subtitle">Managing 350+ national and state-level entrance exams.</p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="?view=importer" class="btn" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fas fa-file-import"></i> Import Cutoffs</a>
                        <a href="?view=harness" class="btn" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);"><i class="fas fa-vial"></i> Predictor Harness</a>
                        <button class="btn btn-primary" onclick="openAddExamModal()"><i class="fas fa-plus"></i> Add Exam</button>
                    </div>
                </div>

                <div class="widget w-full glass-card">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem;">
                        <div class="header-search" style="width: 300px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search exams by name or body...">
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <span style="font-size: 0.7rem; color: var(--text-secondary); align-self: center;">Filter Presets:</span>
                            <button class="btn btn-secondary" style="font-size: 0.7rem;" onclick="handleExamAction('filter', 'results_due')">Results Due This Month</button>
                            <button class="btn btn-secondary" style="font-size: 0.7rem;" onclick="handleExamAction('filter', 'missing_cutoff')">Missing Cutoff Data</button>
                            <button class="btn btn-secondary" style="font-size: 0.7rem;" onclick="handleExamAction('filter', 'no_colleges')">No Colleges Linked</button>
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Exam Name</th>
                                <th>Conducting Body</th>
                                <th>Level</th>
                                <th>Next Date</th>
                                <th>Colleges</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("
                                SELECT e.*, s.exam_date 
                                FROM exams e 
                                LEFT JOIN exam_sessions s ON e.id = s.exam_id 
                                ORDER BY s.exam_date ASC 
                                LIMIT 50
                            ");
                            $exams_list = $stmt->fetchAll();
                            
                            if (empty($exams_list)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                        No exams found in database. <a href="seed_exams.php" style="color: var(--accent-primary);">Run seeder?</a>
                                    </td>
                                </tr>
                            <?php else:
                                foreach ($exams_list as $row): ?>
                                    <tr>
                                        <td><a href="?view=edit&id=<?php echo $row['id']; ?>" style="font-weight: 700; color: var(--accent-primary); text-decoration: none;"><?php echo htmlspecialchars($row['name']); ?></a></td>
                                        <td style="font-size: 0.8rem;"><?php echo htmlspecialchars($row['conducting_body']); ?></td>
                                        <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;"><?php echo htmlspecialchars($row['level']); ?></span></td>
                                        <td><?php echo $row['exam_date'] ? date('d M Y', strtotime($row['exam_date'])) : 'TBD'; ?></td>
                                        <td><span class="status-badge" style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">Live</span></td>
                                        <td><span class="status-badge status-approved">Active</span></td>
                                        <td>
                                            <div style="display: flex; gap: 10px;">
                                                <a href="?view=edit&id=<?php echo $row['id']; ?>" class="action-btn"><i class="fas fa-edit"></i></a>
                                                <button class="action-btn" style="color: var(--success);" onclick="confirmResultDeclaration('<?php echo $row['id']; ?>')"><i class="fas fa-bullhorn"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; 
                            endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'edit'): ?>
                <!-- Screen 2.2.2 — Exam Profile Editor -->
                <div class="page-header">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <a href="?view=list" class="action-btn" style="padding: 10px;"><i class="fas fa-arrow-left"></i></a>
                        <div>
                            <h1 class="page-title">JEE Main 2026</h1>
                            <p class="page-subtitle">Editing Exam Profile & Dynamic Content</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn" style="background: var(--danger); color: white;" onclick="confirmResultDeclaration(142012)"><i class="fas fa-bullhorn"></i> Mark Result Declared</button>
                        <button class="btn btn-primary"><i class="fas fa-save"></i> Save Profile</button>
                    </div>
                </div>

                <div class="exam-tabs">
                    <div class="exam-tab active" data-tab="overview">Overview</div>
                    <div class="exam-tab" data-tab="syllabus">Pattern & Syllabus</div>
                    <div class="exam-tab" data-tab="dates">Important Dates</div>
                    <div class="exam-tab" data-tab="colleges">Accepting Colleges</div>
                    <div class="exam-tab" data-tab="seo">SEO & Metadata</div>
                </div>

                <div class="tab-content active" id="overview">
                    <div class="widget w-full glass-card">
                        <h3 class="widget-title">General Information</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1rem;">
                            <div>
                                <label style="font-size: 0.8rem; color: var(--text-secondary);">Full Exam Name</label>
                                <input type="text" value="Joint Entrance Examination (Main)" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px; margin-top: 5px;">
                            </div>
                            <div>
                                <label style="font-size: 0.8rem; color: var(--text-secondary);">Short Name</label>
                                <input type="text" value="JEE Main" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px; margin-top: 5px;">
                            </div>
                        </div>
                        <div style="margin-top: 1.5rem;">
                            <label style="font-size: 0.8rem; color: var(--text-secondary);">About the Exam</label>
                            <textarea style="width: 100%; height: 150px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 8px; margin-top: 5px;">JEE Main is a national-level entrance exam conducted for admission into premier engineering colleges...</textarea>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="dates">
                    <div class="widget w-full glass-card">
                        <div class="widget-header">
                            <h3 class="widget-title">Exam Cycle Schedule</h3>
                            <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="handleExamAction('add_date', 'JEE Main')">+ Add Date Event</button>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Type Tag</th>
                                    <th>Date</th>
                                    <th>Notification Trigger</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Application Opening</td>
                                    <td><span class="status-badge" style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">Application Start</span></td>
                                    <td><input type="date" value="2025-11-01" style="background: transparent; border: none; color: white;"></td>
                                    <td><span style="color: var(--success);"><i class="fas fa-check-circle"></i> SMS + Email</span></td>
                                    <td><button class="action-btn" onclick="handleExamAction('delete_date', 'Application Opening')"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php elseif ($view == 'importer'): ?>
                <!-- Screen 2.2.3 — Cutoff Import Tool -->
                <div class="page-header">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <a href="?view=list" class="action-btn" style="padding: 10px;"><i class="fas fa-arrow-left"></i></a>
                        <div>
                            <h1 class="page-title">Smart Cutoff Importer</h1>
                            <p class="page-subtitle">Batch process thousands of cutoff records with AI-driven name matching.</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="widget w-half glass-card">
                        <h3 class="widget-title">1. Upload Cutoff CSV</h3>
                        <div style="border: 2px dashed var(--border-color); padding: 3rem; text-align: center; border-radius: 12px; margin-top: 1rem;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: var(--text-secondary); margin-bottom: 1rem;"></i>
                            <p style="color: var(--text-secondary); font-size: 0.9rem;">Drop your file here or click to browse</p>
                            <input type="file" style="display: none;" id="file-upload">
                            <button class="btn btn-primary" style="margin-top: 1rem;" onclick="document.getElementById('file-upload').click()">Select CSV</button>
                        </div>
                        <div style="margin-top: 1.5rem; font-size: 0.75rem; color: var(--text-secondary);">
                            <p>Required Columns: Exam, Year, College Name, Course, Category, Opening Rank, Closing Rank</p>
                        </div>
                    </div>

                    <div class="widget w-half glass-card">
                        <h3 class="widget-title">2. Smart Name Matching (Preview)</h3>
                        <div style="margin-top: 1rem; max-height: 400px; overflow-y: auto; padding-right: 10px;">
                            <div class="matching-row exact">
                                <div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">CSV Value: <span style="color: white;">IIT Bombay</span></div>
                                    <div style="font-weight: 700;">Indian Institute of Technology Bombay</div>
                                </div>
                                <div style="text-align: right;">
                                    <span class="status-badge status-approved">Exact Match (100%)</span>
                                </div>
                            </div>
                            <div class="matching-row high">
                                <div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">CSV Value: <span style="color: white;">BITS Pilani Hyd</span></div>
                                    <div style="font-weight: 700;">Birla Institute of Technology and Science, Hyderabad</div>
                                </div>
                                <div style="text-align: right;">
                                    <span class="status-badge" style="background: rgba(168, 85, 247, 0.1); color: var(--accent-secondary);">High (92%)</span>
                                </div>
                            </div>
                            <div class="matching-row low">
                                <div>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">CSV Value: <span style="color: white;">Anna Uni Main</span></div>
                                    <select style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white; font-size: 0.8rem; padding: 5px; border-radius: 4px;">
                                        <option>Select Match...</option>
                                        <option selected>Anna University, Chennai</option>
                                    </select>
                                </div>
                                <div style="text-align: right;">
                                    <span class="status-badge status-rejected">Low (45%)</span>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary" style="width: 100%; margin-top: 1rem;" onclick="handleExamAction('commit_import', '842 rows')"><i class="fas fa-check-double"></i> Commit Import (842 rows)</button>
                    </div>
                </div>

                <?php elseif ($view == 'harness'): ?>
                <!-- Screen 2.2.4 — Rank Predictor Testing Harness -->
                <div class="page-header">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <a href="?view=list" class="action-btn" style="padding: 10px;"><i class="fas fa-arrow-left"></i></a>
                        <div>
                            <h1 class="page-title">Rank Predictor Testing Harness</h1>
                            <p class="page-subtitle">A/B testing and accuracy audits for admission algorithms.</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="widget w-third glass-card">
                        <h3 class="widget-title">Algorithm Tuning</h3>
                        <div style="margin-top: 1.5rem;">
                            <label style="font-size: 0.8rem; color: var(--text-secondary);">Historical Trend Weight (Last 3 years)</label>
                            <input type="range" class="tuning-slider" min="0" max="100" value="65">
                        </div>
                        <div style="margin-top: 1.5rem;">
                            <label style="font-size: 0.8rem; color: var(--text-secondary);">Category Seat Ratio Sensitivity</label>
                            <input type="range" class="tuning-slider" min="0" max="100" value="40">
                        </div>
                        <div style="margin-top: 1.5rem;">
                            <label style="font-size: 0.8rem; color: var(--text-secondary);">Regional Quota Bias (D-Ratio)</label>
                            <input type="range" class="tuning-slider" min="0" max="100" value="20">
                        </div>
                        <button class="btn btn-primary" style="width: 100%; margin-top: 2rem;" onclick="handleExamAction('run_harness', 'Model v2')">Run A/B Comparison</button>
                    </div>

                    <div class="widget w-two-thirds glass-card">
                        <div class="widget-header">
                            <h3 class="widget-title">Simulation Output (JEE Main - Rank 12,401)</h3>
                            <span class="status-badge status-approved">Accuracy: 94.2%</span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                            <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 12px; border-top: 3px solid var(--accent-primary);">
                                <h4 style="font-size: 0.9rem; margin-bottom: 10px;">Model v1 (Current)</h4>
                                <ul style="list-style: none; font-size: 0.8rem; color: var(--text-secondary);">
                                    <li>1. NIT Trichy (CSE) - <span style="color: var(--success);">High</span></li>
                                    <li>2. NIT Surathkal (ECE) - <span style="color: var(--success);">High</span></li>
                                    <li>3. IIIT Hyderabad (CSE) - <span style="color: var(--warning);">Medium</span></li>
                                </ul>
                            </div>
                            <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 12px; border-top: 3px solid var(--accent-secondary);">
                                <h4 style="font-size: 0.9rem; margin-bottom: 10px;">Model v2 (Candidate)</h4>
                                <ul style="list-style: none; font-size: 0.8rem; color: var(--text-secondary);">
                                    <li>1. NIT Trichy (CSE) - <span style="color: var(--success);">High</span></li>
                                    <li>2. NIT Warangal (CSE) - <span style="color: var(--success);">High</span></li>
                                    <li>3. IIIT Allahabad (IT) - <span style="color: var(--success);">High</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Result Modal Removed in favor of Swal2 -->

    <!-- Add Exam Modal -->
    <div id="addExamModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-plus-circle" style="margin-right: 10px;"></i>Add New Exam</h2>
                <button class="close-btn" onclick="closeAddExamModal()">&times;</button>
            </div>
            
            <form id="addExamForm" onsubmit="handleAddExam(event)">
                <div class="form-group">
                    <label for="examName">Exam Name *</label>
                    <input type="text" id="examName" name="examName" placeholder="e.g., JEE Advanced" required>
                </div>

                <div class="form-group">
                    <label for="conductingBody">Conducting Body</label>
                    <input type="text" id="conductingBody" name="conductingBody" placeholder="e.g., IIT Delhi">
                </div>

                <div class="form-group">
                    <label for="examLevel">Exam Level *</label>
                    <div class="custom-select">
                        <button type="button" class="custom-select-btn" data-select="examLevel">
                            <span id="examLevel-display">Select Level</span>
                            <span class="custom-select-icon"><i class="fas fa-chevron-down"></i></span>
                        </button>
                        <div class="custom-select-dropdown">
                            <div class="custom-select-option" data-value="">Select Level</div>
                            <div class="custom-select-option" data-value="NATIONAL">National</div>
                            <div class="custom-select-option" data-value="STATE">State</div>
                            <div class="custom-select-option" data-value="INSTITUTE">Institute</div>
                        </div>
                    </div>
                    <input type="hidden" id="examLevel" name="examLevel" required>
                </div>

                <div class="form-group">
                    <label for="officialUrl">Official Website</label>
                    <input type="url" id="officialUrl" name="officialUrl" placeholder="https://jeeadv.ac.in">
                </div>

                <div class="form-group">
                    <label for="examDescription">Description</label>
                    <textarea id="examDescription" name="examDescription" placeholder="Briefly describe the exam..."></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeAddExamModal()" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Exam</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab Switching Logic
        document.querySelectorAll('.exam-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.getAttribute('data-tab');
                document.querySelectorAll('.exam-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(target).classList.add('active');
            });
        });

        function openAddExamModal() {
            document.getElementById('addExamModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeAddExamModal() {
            document.getElementById('addExamModal').classList.remove('show');
            document.body.style.overflow = 'auto';
            document.getElementById('addExamForm').reset();
            // Reset custom select
            document.getElementById('examLevel-display').textContent = 'Select Level';
            document.getElementById('examLevel').value = '';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('addExamModal');
            if (event.target === modal) {
                closeAddExamModal();
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
                    
                    allDropdowns.forEach(d => {
                        if (d !== dropdown) d.classList.remove('show');
                    });
                    
                    dropdown.classList.toggle('show');
                    this.classList.toggle('open');
                });
            });
            
            const options = document.querySelectorAll('.custom-select-option');
            options.forEach(option => {
                option.addEventListener('click', function() {
                    const selectId = this.parentElement.previousElementSibling.dataset.select;
                    const value = this.dataset.value;
                    const display = this.textContent;
                    
                    document.getElementById(selectId).value = value;
                    document.getElementById(selectId + '-display').textContent = display;
                    
                    const siblings = this.parentElement.querySelectorAll('.custom-select-option');
                    siblings.forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');
                    
                    this.parentElement.classList.remove('show');
                    this.parentElement.previousElementSibling.classList.remove('open');
                });
            });
            
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.custom-select')) {
                    document.querySelectorAll('.custom-select-dropdown').forEach(d => d.classList.remove('show'));
                    document.querySelectorAll('.custom-select-btn').forEach(b => b.classList.remove('open'));
                }
            });
        });

        async function handleAddExam(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('addExamForm'));
            const data = {
                examName: formData.get('examName'),
                conductingBody: formData.get('conductingBody'),
                examLevel: formData.get('examLevel'),
                officialUrl: formData.get('officialUrl'),
                description: formData.get('examDescription')
            };

            Swal.fire({
                title: 'Saving Exam...',
                text: 'Registering ' + data.examName + ' in database',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('add_exam_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Exam Added!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        closeAddExamModal();
                        location.reload(); 
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while adding the exam.' });
            }
        }

        async function confirmResultDeclaration(count) {
            const { value: confirmText } = await Swal.fire({
                title: 'DANGEROUS ACTION',
                html: `
                    <p style="font-size: 0.9rem; margin-bottom: 1rem;">Marking <strong>JEE Main 2026</strong> results as declared.</p>
                    <div style="background: rgba(239, 68, 68, 0.1); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <div style="font-size: 1.2rem; font-weight: 700; color: white; text-align: center;">1,42,012 Students</div>
                        <div style="font-size: 0.75rem; text-align: center; color: var(--text-secondary);">will receive an immediate SMS + Email alert.</div>
                    </div>
                    <p style="font-size: 0.8rem;">Type <b>DECLARE</b> to confirm:</p>
                `,
                input: 'text',
                inputPlaceholder: 'DECLARE',
                showCancelButton: true,
                confirmButtonText: 'PROCEED WITH MASS NOTIFICATION',
                confirmButtonColor: '#ef4444',
                background: 'rgba(15, 23, 42, 0.95)',
                color: '#fff',
                preConfirm: (value) => {
                    if (value.toUpperCase() !== 'DECLARE') {
                        Swal.showValidationMessage('Type "DECLARE" exactly');
                    }
                    return value;
                }
            });

            if (confirmText) {
                Swal.fire({
                    title: 'Processing...',
                    didOpen: () => Swal.showLoading(),
                    background: 'rgba(15, 23, 42, 0.95)',
                    color: '#fff'
                });

                try {
                    const response = await fetch('process_result_declaration.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'declare_results', exam_id: 1, count: 142012 })
                    });
                    const result = await response.json();
                    
                    if (result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Results Declared!',
                            text: result.message,
                            timer: 2000,
                            background: 'rgba(15, 23, 42, 0.95)',
                            color: '#fff'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: result.message });
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to process' });
                }
            }
        }

        async function handleExamAction(action, target) {
            let config = {
                title: 'Exam Action',
                text: '',
                icon: 'info',
                showConfirmButton: false,
                timer: 2000,
                background: 'rgba(15, 23, 42, 0.95)',
                color: '#fff',
                backdrop: `rgba(0,0,0,0.4) blur(4px)`
            };

            if (action === 'filter') config.text = 'Applying preset filter: ' + target + '...';
            else if (action === 'add_date') config.text = 'Opening Event Scheduler for ' + target + '...';
            else if (action === 'delete_date') { config.text = 'Removing date event: ' + target + '...'; config.icon = 'warning'; }
            else if (action === 'commit_import') { config.text = 'Validating integrity for ' + target + '. Writing to ledger...'; config.icon = 'success'; }
            else if (action === 'run_harness') { config.text = 'Running Monte-Carlo simulation with ' + target + ' weights...'; config.icon = 'info'; }
            
            Swal.fire(config);
        }
    </script>
</body>
</html>
