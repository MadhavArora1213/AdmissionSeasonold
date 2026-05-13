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
                        <button class="btn btn-primary"><i class="fas fa-plus"></i> Add Exam</button>
                    </div>
                </div>

                <div class="widget w-full">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; gap: 1rem;">
                        <div class="header-search" style="width: 300px;">
                            <i class="fas fa-search"></i>
                            <input type="text" placeholder="Search exams by name or body...">
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <span style="font-size: 0.7rem; color: var(--text-secondary); align-self: center;">Filter Presets:</span>
                            <button class="btn" style="font-size: 0.7rem; background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">Results Due This Month</button>
                            <button class="btn" style="font-size: 0.7rem; background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">Missing Cutoff Data</button>
                            <button class="btn" style="font-size: 0.7rem; background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">No Colleges Linked</button>
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
                            <tr>
                                <td><a href="?view=edit&id=1" style="font-weight: 700; color: var(--accent-primary); text-decoration: none;">JEE Main 2026</a></td>
                                <td style="font-size: 0.8rem;">National Testing Agency (NTA)</td>
                                <td><span class="status-badge" style="background: rgba(255,255,255,0.05); color: white;">National</span></td>
                                <td>24 Jan 2026</td>
                                <td><span class="status-badge" style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">1,420 Linked</span></td>
                                <td><span class="status-badge status-approved">Active</span></td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <a href="?view=edit&id=1" class="action-btn"><i class="fas fa-edit"></i></a>
                                        <button class="action-btn" style="color: var(--success);" onclick="confirmResultDeclaration(142012)"><i class="fas fa-bullhorn"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <!-- More rows -->
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
                    <div class="widget w-full">
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
                    <div class="widget w-full">
                        <div class="widget-header">
                            <h3 class="widget-title">Exam Cycle Schedule</h3>
                            <button class="btn btn-primary" style="font-size: 0.75rem;">+ Add Date Event</button>
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
                                    <td><button class="action-btn"><i class="fas fa-trash"></i></button></td>
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
                    <div class="widget w-half">
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

                    <div class="widget w-half">
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
                        <button class="btn btn-primary" style="width: 100%; margin-top: 1rem;"><i class="fas fa-check-double"></i> Commit Import (842 rows)</button>
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
                    <div class="widget w-third">
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
                        <button class="btn btn-primary" style="width: 100%; margin-top: 2rem;">Run A/B Comparison</button>
                    </div>

                    <div class="widget w-two-thirds">
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

    <!-- Result Declaration Modal -->
    <div id="result-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; align-items: center; justify-content: center;">
        <div class="widget" style="width: 450px; background: var(--sidebar-bg); border: 2px solid var(--danger);">
            <div class="widget-header">
                <h3 class="widget-title" style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> DANGEROUS ACTION</h3>
                <button class="action-btn" onclick="document.getElementById('result-modal').style.display='none'"><i class="fas fa-times"></i></button>
            </div>
            <div style="padding: 1.5rem;">
                <p style="font-size: 0.9rem; margin-bottom: 1rem;">You are about to mark <strong>JEE Main 2026</strong> results as declared.</p>
                <div style="background: rgba(239, 68, 68, 0.1); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="font-size: 1.2rem; font-weight: 700; color: white; text-align: center;">1,42,012 Students</div>
                    <div style="font-size: 0.75rem; text-align: center; color: var(--text-secondary);">will receive an immediate SMS + Email alert.</div>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.8rem;">Type <strong>DECLARE</strong> to confirm:</label>
                    <input type="text" id="confirm-text" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--border-color); color: white; padding: 10px; border-radius: 4px; margin-top: 5px;">
                </div>
                <button class="btn" style="width: 100%; background: var(--danger); color: white; font-weight: 700;">PROCEED WITH MASS NOTIFICATION</button>
            </div>
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

        function confirmResultDeclaration(count) {
            document.getElementById('result-modal').style.display = 'flex';
        }
    </script>
</body>
</html>
