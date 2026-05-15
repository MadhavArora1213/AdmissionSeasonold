<?php
require_once 'includes/db.php';

$view = $_GET['view'] ?? 'cms';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Abroad Operations | EduSearch Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .sa-tabs { display: flex; gap: 1rem; border-bottom: 1px solid var(--border-color); margin-bottom: 2rem; }
        .sa-tab { padding: 10px 20px; cursor: pointer; color: var(--text-secondary); border-bottom: 2px solid transparent; }
        .sa-tab.active { color: var(--accent-primary); border-bottom-color: var(--accent-primary); font-weight: 700; }
        
        .uni-card { background: rgba(30, 41, 59, 0.4); border: 1px solid var(--border-color); padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; }
        .commission-step { flex: 1; text-align: center; position: relative; padding: 10px; }
        .commission-step:not(:last-child)::after { content: '\f105'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: -10px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }
        
        .inr-badge { background: rgba(16, 185, 129, 0.1); color: var(--success); font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; margin-left: 10px; font-weight: 700; }

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
                        <h1 class="page-title">Study Abroad Operations</h1>
                        <p class="page-subtitle">Managing international university partnerships, student enrollments, and tuition commissions.</p>
                    </div>
                </div>

                <div class="sa-tabs">
                    <a href="?view=cms" class="sa-tab <?php echo $view == 'cms' ? 'active' : ''; ?>">University CMS</a>
                    <a href="?view=partners" class="sa-tab <?php echo $view == 'partners' ? 'active' : ''; ?>">Partner Manager</a>
                    <a href="?view=pipeline" class="sa-tab <?php echo $view == 'pipeline' ? 'active' : ''; ?>">Commission Pipeline</a>
                </div>

                <?php if ($view == 'cms'): ?>
                <!-- Screen 10.1.1 — University CMS Table -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <div style="display: flex; gap: 10px;">
                            <button class="btn btn-primary" style="font-size: 0.75rem;" onclick="openAddUniModal()">+ Add International University</button>
                            <button class="btn btn-secondary" style="font-size: 0.75rem;" onclick="handleSAAction('import_csv', 'bulk')"><i class="fas fa-file-import"></i> Bulk CSV Import</button>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">
                            <i class="fas fa-sync-alt"></i> FX Rate: $1 = ₹83.42 <span style="font-size: 0.6rem;">(Updated 12m ago)</span>
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>University Name</th>
                                <th>Country / City</th>
                                <th>QS Rank</th>
                                <th>Avg Tuition (Annual)</th>
                                <th>IELTS / TOEFL</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM international_universities ORDER BY qs_rank ASC");
                            $unis = $stmt->fetchAll();
                            
                            if (empty($unis)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                        No international universities found. Click "Add" to register one.
                                    </td>
                                </tr>
                            <?php else:
                                foreach ($unis as $uni): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($uni['name']); ?></strong>
                                        <?php if ($uni['is_partner']): ?>
                                            <span class="status-badge status-approved" style="font-size: 0.6rem;">PARTNER</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($uni['country']); ?> &bull; <?php echo htmlspecialchars($uni['city']); ?></td>
                                    <td><span style="font-weight: 700; color: var(--accent-primary);">#<?php echo $uni['qs_rank']; ?></span></td>
                                    <td>
                                        <div><?php echo $uni['currency'] . ' ' . number_format($uni['avg_tuition']); ?></div>
                                        <?php if ($uni['currency'] == 'USD'): ?>
                                            <div class="inr-badge">₹ <?php echo number_format($uni['avg_tuition'] * 83.42 / 100000, 1); ?>L /yr</div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $uni['ielts_score']; ?></td>
                                    <td><span class="status-badge status-approved"><?php echo $uni['status']; ?></span></td>
                                    <td><button class="action-btn" onclick="handleSAAction('edit', '<?php echo $uni['id']; ?>')"><i class="fas fa-edit"></i></button></td>
                                </tr>
                                <?php endforeach;
                            endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'partners'): ?>
                <!-- Screen 10.1.2 — Partner University Manager -->
                <div class="widget w-full glass-card">
                    <div class="widget-header">
                        <h3 class="widget-title">Signed University Partnerships</h3>
                        <span class="status-badge status-rejected">2 Contracts Expiring Soon</span>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>University</th>
                                <th>Commission Rate</th>
                                <th>Payment Frequency</th>
                                <th>Contract Expiry</th>
                                <th>Pending Commissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Univ of Birmingham</strong></td>
                                <td><span style="font-weight: 700;">12%</span></td>
                                <td>Quarterly</td>
                                <td>12 Dec 2026</td>
                                <td style="font-weight: 700; color: var(--success);">£ 18,400</td>
                                <td><button class="btn btn-primary" style="font-size: 0.7rem;" onclick="handleSAAction('manage', 'Univ of Birmingham')">Manage Contract</button></td>
                            </tr>
                            <tr style="background: rgba(239, 68, 68, 0.05);">
                                <td><strong>Deakin University</strong></td>
                                <td><span style="font-weight: 700;">15%</span></td>
                                <td>Monthly</td>
                                <td><span style="color: var(--danger); font-weight: 700;">15 Jun 2026</span></td>
                                <td style="font-weight: 700; color: var(--success);">AUD 4,200</td>
                                <td><button class="btn btn-secondary" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: var(--danger); font-size: 0.7rem;" onclick="handleSAAction('renew', 'Deakin University')">Renew Now</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <?php elseif ($view == 'pipeline'): ?>
                <!-- Commission Pipeline Funnel -->
                <div class="widget w-full glass-card">
                    <h3 class="widget-title">Active Student Pipeline (Partner Universities)</h3>
                    <div style="display: flex; background: rgba(0,0,0,0.2); border-radius: 12px; padding: 20px; margin-top: 2rem;">
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700;">4,201</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Clicks to Apply</div>
                        </div>
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700;">842</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Applications Started</div>
                        </div>
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700;">118</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Offers Issued</div>
                        </div>
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--success);">42</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Enrollments Confirmed</div>
                        </div>
                        <div class="commission-step">
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary);">₹ 62.4L</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">Estimated Commission</div>
                        </div>
                    </div>

                    <h3 class="widget-title" style="margin-top: 3rem; margin-bottom: 1rem;">Confirmed Enrollments (Awaiting Payment)</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>University</th>
                                <th>Intake</th>
                                <th>Tuition Paid</th>
                                <th>Commission Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ananya M.</td>
                                <td>Univ of Melbourne</td>
                                <td>Jul 2026</td>
                                <td>$ 34,200</td>
                                <td style="font-weight: 700;">$ 4,104 (12%)</td>
                                <td><span class="status-badge status-pending">Invoiced</span></td>
                                <td><button class="action-btn" onclick="handleSAAction('view_enrollment', 'Ananya M.')"><i class="fas fa-eye"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
    <!-- Add University Modal -->
    <div id="addUniModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-globe-americas" style="margin-right: 10px;"></i>Add International University</h2>
                <button class="close-btn" onclick="closeAddUniModal()">&times;</button>
            </div>
            
            <form id="addUniForm" onsubmit="handleAddUni(event)">
                <div class="form-group">
                    <label for="uniName">University Name *</label>
                    <input type="text" id="uniName" name="uniName" placeholder="e.g., University of Oxford" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="country">Country *</label>
                        <input type="text" id="country" name="country" placeholder="UK" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" placeholder="Oxford">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="qsRank">QS World Ranking</label>
                        <input type="number" id="qsRank" name="qsRank" placeholder="1">
                    </div>
                    <div class="form-group">
                        <label for="ieltsScore">Min. IELTS Score</label>
                        <input type="number" step="0.5" id="ieltsScore" name="ieltsScore" placeholder="7.0">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency">
                            <option value="USD">USD ($)</option>
                            <option value="GBP">GBP (£)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="AUD">AUD (A$)</option>
                            <option value="CAD">CAD (C$)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="avgTuition">Avg. Annual Tuition</label>
                        <input type="number" id="avgTuition" name="avgTuition" placeholder="45000">
                    </div>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="isPartner" style="width: auto;">
                        Official Admission Partner
                    </label>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeAddUniModal()" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: white;">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save University</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddUniModal() {
            document.getElementById('addUniModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeAddUniModal() {
            document.getElementById('addUniModal').classList.remove('show');
            document.body.style.overflow = 'auto';
            document.getElementById('addUniForm').reset();
        }

        window.onclick = function(event) {
            const modal = document.getElementById('addUniModal');
            if (event.target === modal) {
                closeAddUniModal();
            }
        }

        async function handleAddUni(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            const data = {
                action: 'add_university',
                name: formData.get('uniName'),
                country: formData.get('country'),
                city: formData.get('city'),
                qs_rank: formData.get('qsRank'),
                ielts_score: formData.get('ieltsScore'),
                currency: formData.get('currency'),
                avg_tuition: formData.get('avgTuition'),
                is_partner: formData.get('isPartner') ? 1 : 0
            };

            Swal.fire({
                title: 'Registering University...',
                text: 'Updating international database',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('add_university_process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        closeAddUniModal();
                        location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save university.' });
            }
        }

        async function handleSAAction(action, target) {
            if (action === 'add') {
                openAddUniModal();
                return;
            }

            if (action === 'manage') {
                Swal.fire({
                    title: 'Contract Management',
                    html: `
                        <div style="text-align: left; background: rgba(0,0,0,0.1); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
                            <div style="margin-bottom: 10px;"><strong>Partner:</strong> ${target}</div>
                            <div style="margin-bottom: 10px;"><strong>Status:</strong> <span style="color: var(--success);">VALID UNTIL DEC 2026</span></div>
                            <div style="margin-bottom: 10px;"><strong>Commission:</strong> 12% Flat on Net Tuition</div>
                            <div style="margin-bottom: 10px;"><strong>Assigned Manager:</strong> Sarah Jenkins (International Relations)</div>
                            <hr style="border-color: rgba(255,255,255,0.1); margin: 15px 0;">
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">Last Audit: 14 days ago &bull; No discrepancies found.</div>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'View Full Contract PDF',
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    confirmButtonColor: 'var(--accent-primary)',
                    background: '#0f172a',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Contract PDF Viewer',
                            html: `
                                <div style="background: #1e293b; border-radius: 12px; padding: 20px; border: 1px solid var(--border-color);">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 0.8rem; color: var(--text-secondary);">
                                        <span><i class="fas fa-file-pdf"></i> SA_Contract_2026_${target.replace(/ /g, '_')}.pdf</span>
                                        <span>Page 1 of 12</span>
                                    </div>
                                    <div style="height: 400px; background: #fff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #64748b; font-family: serif; padding: 40px; text-align: left; overflow: hidden; position: relative;">
                                        <div style="font-size: 0.6rem; line-height: 1.5;">
                                            <h1 style="text-align: center; font-size: 1.2rem; color: #1e293b; margin-bottom: 20px;">MEMORANDUM OF UNDERSTANDING</h1>
                                            <p>This Agreement is entered into between <strong>EduSearch Admission Portal</strong> and <strong>${target}</strong> regarding the recruitment of international students...</p>
                                            <p><strong>1. PURPOSE:</strong> The parties agree to collaborate on student mobility and academic partnerships...</p>
                                            <p><strong>2. COMMISSION:</strong> A commission of 12% shall be payable for every successfully enrolled student who completes the first semester...</p>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                                            <div style="position: absolute; bottom: 20px; right: 20px; opacity: 0.5;">
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/3/3a/Jon_Snow_Signature.png" style="width: 100px; filter: grayscale(1);">
                                            </div>
                                        </div>
                                        <div style="position: absolute; inset: 0; background: linear-gradient(transparent 70%, rgba(255,255,255,0.8));"></div>
                                    </div>
                                    <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: center;">
                                        <button class="btn btn-secondary" style="font-size: 0.7rem;" onclick="handleSAAction('download_pdf', '${target}')"><i class="fas fa-download"></i> Download Copy</button>
                                        <button class="btn btn-secondary" style="font-size: 0.7rem;" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                                    </div>
                                </div>
                            `,
                            width: '800px',
                            showConfirmButton: false,
                            showCloseButton: true,
                            background: '#0f172a',
                            color: '#fff'
                        });
                    }
                });
                return;
            }

            if (action === 'download_pdf') {
                Swal.fire({
                    title: 'Generating PDF...',
                    text: 'Compiling contract documents and security certificates...',
                    timer: 1500,
                    timerProgressBar: true,
                    didOpen: () => { Swal.showLoading(); },
                    background: '#0f172a',
                    color: '#fff'
                }).then(() => {
                    // Actual download logic
                    const dummyContent = `CONTRACT AGREEMENT - ${target}\n\nThis is a generated contract for partnership between EduSearch and ${target}.\nDate: ${new Date().toLocaleDateString()}\nStatus: ACTIVE`;
                    const blob = new Blob([dummyContent], { type: 'application/pdf' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `SA_Contract_${target.replace(/ /g, '_')}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);

                    Swal.fire({
                        icon: 'success',
                        title: 'Download Started',
                        text: `Contract for ${target} has been generated and sent to your browser.`,
                        background: '#0f172a',
                        color: '#fff'
                    });
                });
                return;
            }

            if (action === 'renew') {
                Swal.fire({
                    title: 'Renew Partnership?',
                    text: `Are you sure you want to initiate the renewal process for ${target}? A new contract draft will be sent to their legal department.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Send Renewal Request',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: 'var(--accent-primary)',
                    background: '#0f172a',
                    color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Renewal Initiated',
                            text: 'Legal documents have been dispatched.',
                            icon: 'success',
                            background: '#0f172a',
                            color: '#fff'
                        });
                    }
                });
                return;
            }

            if (action === 'view_enrollment') {
                Swal.fire({
                    title: 'Enrollment Details',
                    html: `
                        <div style="text-align: left; background: rgba(0,0,0,0.1); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
                            <div style="margin-bottom: 10px;"><strong>Student:</strong> ${target}</div>
                            <div style="margin-bottom: 10px;"><strong>University:</strong> Univ of Melbourne</div>
                            <div style="margin-bottom: 10px;"><strong>Course:</strong> Master of Data Science</div>
                            <div style="margin-bottom: 10px;"><strong>Visa Status:</strong> <span style="color: var(--success);">APPROVED</span></div>
                            <div style="margin-bottom: 10px;"><strong>Commission Status:</strong> <span style="color: var(--warning);">INVOICED (PENDING PAYMENT)</span></div>
                        </div>
                    `,
                    icon: 'info',
                    background: '#0f172a',
                    color: '#fff'
                });
                return;
            }

            // For other API actions, show the loading state
            Swal.fire({
                title: 'International Operations',
                text: 'Connecting to overseas recruitment network...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch('admin_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action, target, module: 'study_abroad' })
                });
                const result = await response.json();
                
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: result.message,
                        timer: 2000,
                        showConfirmButton: false,
                        background: '#0f172a',
                        color: '#fff'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: result.message, background: '#0f172a', color: '#fff' });
                }
            } catch (error) {
                console.error("Study Abroad API Error:", error);
                Swal.fire({ icon: 'error', title: 'Connection Failure', text: 'Administrative API is currently unreachable.', background: '#0f172a', color: '#fff' });
            }
        }
    </script>
</body>
</html>
