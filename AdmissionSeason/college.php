<?php
require_once 'admin/includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: /AdmissionSeason/colleges.php'); exit; }

// Fetch college
$stmt = $pdo->prepare("SELECT * FROM colleges WHERE id = ?");
$stmt->execute([$id]);
$col = $stmt->fetch();
if (!$col) { header('Location: /AdmissionSeason/colleges.php'); exit; }

// Fetch reviews
$reviews = $pdo->prepare("SELECT r.*, u.name as user_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.college_id = ? AND r.status = 'APPROVED' ORDER BY r.created_at DESC LIMIT 5");
$reviews->execute([$id]);
$reviews = $reviews->fetchAll();

// Fetch courses
$courses = $pdo->prepare("SELECT * FROM courses WHERE college_id = ? LIMIT 10");
$courses->execute([$id]);
$courses = $courses->fetchAll();

$tabs = ['overview' => 'Overview', 'courses' => 'Courses & Fees', 'placements' => 'Placements', 'reviews' => 'Reviews', 'gallery' => 'Gallery'];
$active_tab = $_GET['tab'] ?? 'overview';
$name = htmlspecialchars($col['name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $name ?> — Fees, Courses, Placements 2026 | AdmissionSeason</title>
    <meta name="description" content="Complete details of <?= $name ?>: fees, courses, placements, NAAC grade, reviews from students. Apply directly on AdmissionSeason.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/AdmissionSeason/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Hero Banner */
        .college-hero {
            background: linear-gradient(180deg, var(--bg-secondary) 0%, var(--bg-primary) 100%);
            border-bottom: 1px solid var(--border);
            padding: 7rem 0 0;
        }
        .college-header {
            display: flex; align-items: flex-start; gap: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .college-logo-lg {
            width: 80px; height: 80px; border-radius: 18px;
            background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.3);
            display: flex; align-items: center; justify-content: center; font-size: 2.5rem;
            flex-shrink: 0;
        }
        .college-title { font-size: 1.6rem; font-weight: 800; color: white; line-height: 1.3; margin-bottom: 0.5rem; }
        .college-meta-strip { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 0.75rem; font-size: 0.85rem; color: var(--text-secondary); }
        .college-meta-strip i { font-size: 0.75rem; }
        .badge-strip { display: flex; flex-wrap: wrap; gap: 8px; }
        .header-actions { margin-left: auto; display: flex; flex-direction: column; gap: 8px; flex-shrink: 0; }

        /* Tabs */
        .tabs-bar {
            display: flex; gap: 0; overflow-x: auto; border-bottom: none;
            background: var(--bg-secondary); padding: 0;
        }
        .tab-link {
            padding: 14px 20px; font-size: 0.875rem; font-weight: 500;
            color: var(--text-secondary); border-bottom: 2px solid transparent;
            cursor: pointer; white-space: nowrap; transition: all 0.2s;
            text-decoration: none; display: block;
        }
        .tab-link:hover { color: white; }
        .tab-link.active { color: #818cf8; border-bottom-color: #6366f1; font-weight: 700; }

        /* Quick Stats */
        .quick-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; margin: 1.5rem 0; }
        .qs-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 1.25rem; text-align: center; }
        .qs-value { font-size: 1.4rem; font-weight: 800; color: white; }
        .qs-label { font-size: 0.72rem; color: var(--text-muted); margin-top: 4px; }

        /* Info Grid */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .info-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; }
        .info-card h3 { font-size: 0.875rem; font-weight: 700; color: white; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 0.85rem; border-bottom: 1px solid rgba(30,45,69,0.5); }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: var(--text-secondary); }
        .info-row .value { color: white; font-weight: 600; text-align: right; }

        /* Reviews */
        .review-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px; padding: 1.25rem; margin-bottom: 1rem; }
        .review-header { display: flex; align-items: center; gap: 10px; margin-bottom: 0.75rem; }
        .review-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg,#6366f1,#a855f7); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.85rem; }
        .review-stars { color: #f59e0b; font-size: 0.8rem; }
        .review-text { font-size: 0.875rem; color: var(--text-secondary); line-height: 1.7; }

        /* Course Table */
        .course-table { width: 100%; border-collapse: collapse; }
        .course-table th { text-align: left; padding: 10px 16px; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--border); }
        .course-table td { padding: 12px 16px; font-size: 0.875rem; border-bottom: 1px solid rgba(30,45,69,0.4); color: var(--text-secondary); }
        .course-table tr:hover td { background: rgba(99,102,241,0.04); color: white; }
        .course-table td:first-child { color: white; font-weight: 600; }

        /* Sticky sidebar */
        .detail-layout { display: grid; grid-template-columns: 1fr 300px; gap: 2rem; padding: 2rem 0 4rem; }
        .sticky-sidebar { position: sticky; top: 80px; height: fit-content; }
        .apply-card { background: var(--bg-card); border: 1px solid rgba(99,102,241,0.3); border-radius: 18px; padding: 1.5rem; }
        .apply-card h4 { font-size: 1rem; font-weight: 700; color: white; margin-bottom: 1.25rem; text-align: center; }
        .apply-form input { margin-bottom: 0.75rem; }

        @media (max-width: 900px) {
            .detail-layout { grid-template-columns: 1fr; }
            .sticky-sidebar { position: static; }
            .quick-stats { grid-template-columns: repeat(2,1fr); }
            .info-grid { grid-template-columns: 1fr; }
            .college-header { flex-direction: column; }
            .header-actions { margin-left: 0; flex-direction: row; }
        }
    </style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<!-- College Hero -->
<div class="college-hero">
    <div class="container">
        <div style="margin-bottom:1rem;font-size:0.8rem;color:var(--text-muted);">
            <a href="/AdmissionSeason/" style="color:var(--text-muted);">Home</a> ›
            <a href="/AdmissionSeason/colleges.php" style="color:var(--text-muted);">Colleges</a> ›
            <?= $name ?>
        </div>

        <div class="college-header">
            <div class="college-logo-lg">🎓</div>
            <div style="flex:1;">
                <div class="college-title"><?= $name ?></div>
                <div class="college-meta-strip">
                    <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($col['city'] . ', ' . $col['state']) ?></span>
                    <?php if ($col['established_year']): ?>
                    <span>• Est. <?= $col['established_year'] ?></span>
                    <?php endif; ?>
                    <?php if ($col['affiliated_to']): ?>
                    <span>• Affiliated to <?= htmlspecialchars($col['affiliated_to']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="badge-strip">
                    <span class="badge badge-primary"><?= htmlspecialchars($col['type']) ?></span>
                    <?php if ($col['nirf_rank']): ?><span class="badge badge-warning"><i class="fas fa-trophy"></i> NIRF #<?= $col['nirf_rank'] ?></span><?php endif; ?>
                    <?php if ($col['naac_grade']): ?><span class="badge badge-success">NAAC <?= $col['naac_grade'] ?></span><?php endif; ?>
                    <?php if ($col['is_verified']): ?><span class="badge badge-teal"><i class="fas fa-check-circle"></i> Verified</span><?php endif; ?>
                    <?php if ($col['is_featured']): ?><span class="badge badge-purple">⭐ Featured</span><?php endif; ?>
                </div>
            </div>
            <div class="header-actions">
                <a href="#apply" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Apply Now</a>
                <button class="btn btn-secondary btn-sm" onclick="addToCompare('<?= $col['id'] ?>', '<?= htmlspecialchars(addslashes($col['name'])) ?>')">
                    <i class="fas fa-balance-scale"></i> Compare
                </button>
                <button class="btn btn-outline btn-sm" onclick="navigator.share ? navigator.share({title:'<?= $name ?>',url:location.href}) : navigator.clipboard.writeText(location.href)">
                    <i class="fas fa-share"></i> Share
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-bar">
            <?php foreach ($tabs as $key => $label): ?>
            <a href="?id=<?= $id ?>&tab=<?= $key ?>" class="tab-link <?= $active_tab === $key ? 'active' : '' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="detail-layout">
        <!-- Main Content -->
        <div>
            <!-- Quick Stats -->
            <div class="quick-stats">
                <?php
                $qs = [
                    ['NIRF Rank', $col['nirf_rank'] ? '#' . $col['nirf_rank'] : 'N/A', '#f59e0b'],
                    ['NAAC Grade', $col['naac_grade'] ?: 'N/A', '#10b981'],
                    ['Campus Area', $col['campus_area_acres'] ? $col['campus_area_acres'] . ' Acres' : 'N/A', '#6366f1'],
                    ['Total Students', $col['total_students'] ? number_format($col['total_students']) : 'N/A', '#a855f7'],
                ];
                foreach ($qs as [$label, $value, $color]): ?>
                <div class="qs-card">
                    <div class="qs-value" style="color:<?= $color ?>"><?= $value ?></div>
                    <div class="qs-label"><?= $label ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Overview Tab -->
            <?php if ($active_tab === 'overview'): ?>
            <div>
                <!-- About -->
                <?php if ($col['about_description']): ?>
                <div class="info-card mb-4" style="margin-bottom:1.5rem;">
                    <h3><i class="fas fa-university" style="color:#6366f1;margin-right:8px;"></i> About <?= $name ?></h3>
                    <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.8;"><?= nl2br(htmlspecialchars($col['about_description'])) ?></p>
                </div>
                <?php endif; ?>

                <div class="info-grid">
                    <!-- Key Details -->
                    <div class="info-card">
                        <h3><i class="fas fa-info-circle" style="color:#6366f1;margin-right:8px;"></i> Key Details</h3>
                        <?php
                        $details = [
                            ['Type', ucfirst(strtolower($col['type']))],
                            ['Established', $col['established_year'] ?: 'N/A'],
                            ['Location', htmlspecialchars($col['city'] . ', ' . $col['state'])],
                            ['Affiliated To', htmlspecialchars($col['affiliated_to'] ?: 'N/A')],
                            ['Gender Type', htmlspecialchars($col['gender_type'] ?: 'Co-ed')],
                            ['Residential', htmlspecialchars($col['residential_type'] ?: 'N/A')],
                            ['Total Faculty', $col['total_faculty'] ? number_format($col['total_faculty']) : 'N/A'],
                            ['Campus Area', $col['campus_area_acres'] ? $col['campus_area_acres'] . ' Acres' : 'N/A'],
                        ];
                        foreach ($details as [$label, $value]): ?>
                        <div class="info-row">
                            <span class="label"><?= $label ?></span>
                            <span class="value"><?= $value ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Rankings & Accreditation -->
                    <div class="info-card">
                        <h3><i class="fas fa-trophy" style="color:#f59e0b;margin-right:8px;"></i> Rankings & Accreditation</h3>
                        <?php
                        $rankings = [
                            ['NIRF Overall Rank', $col['nirf_rank'] ? '#' . $col['nirf_rank'] . ' (' . ($col['nirf_year'] ?: '2024') . ')' : 'N/A'],
                            ['NAAC Grade', $col['naac_grade'] ? $col['naac_grade'] . ' (' . ($col['naac_year'] ?: '') . ')' : 'N/A'],
                            ['QS World Rank', 'N/A'],
                            ['THE World Rank', 'N/A'],
                        ];
                        foreach ($rankings as [$label, $value]): ?>
                        <div class="info-row">
                            <span class="label"><?= $label ?></span>
                            <span class="value" style="color:<?= $col['nirf_rank'] && $label === 'NIRF Overall Rank' ? '#f59e0b' : 'white' ?>"><?= $value ?></span>
                        </div>
                        <?php endforeach; ?>

                        <?php if ($col['official_url']): ?>
                        <div style="margin-top:1rem;">
                            <a href="<?= htmlspecialchars($col['official_url']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                                <i class="fas fa-external-link-alt"></i> Official Website
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Admission Process -->
                <?php if ($col['admission_process']): ?>
                <div class="info-card" style="margin-top:1.5rem;">
                    <h3><i class="fas fa-route" style="color:#14b8a6;margin-right:8px;"></i> Admission Process</h3>
                    <p style="font-size:0.9rem;color:var(--text-secondary);line-height:1.8;"><?= nl2br(htmlspecialchars($col['admission_process'])) ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Courses Tab -->
            <?php elseif ($active_tab === 'courses'): ?>
            <div class="info-card">
                <h3><i class="fas fa-book-open" style="color:#6366f1;margin-right:8px;"></i> Courses & Fees</h3>
                <?php if (empty($courses)): ?>
                <div style="text-align:center;padding:3rem;color:var(--text-secondary);">
                    <div style="font-size:2rem;margin-bottom:0.75rem;">📚</div>
                    <p>Course details coming soon</p>
                </div>
                <?php else: ?>
                <table class="course-table">
                    <thead>
                        <tr>
                            <th>Course</th><th>Level</th><th>Duration</th><th>Seats</th><th>Total Fees</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['name']) ?></td>
                            <td><?= htmlspecialchars($c['level'] ?? 'UG') ?></td>
                            <td><?= htmlspecialchars($c['duration_years'] ?? 4) ?> Yrs</td>
                            <td><?= $c['total_seats'] ? number_format($c['total_seats']) : 'N/A' ?></td>
                            <td style="color:#10b981;font-weight:700;"><?= $c['total_fees'] ? '₹' . number_format($c['total_fees']) : 'N/A' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <!-- Reviews Tab -->
            <?php elseif ($active_tab === 'reviews'): ?>
            <div>
                <div class="flex-between mb-4">
                    <h3 style="color:white;font-size:1rem;font-weight:700;">Student Reviews</h3>
                    <a href="#write-review" class="btn btn-primary btn-sm"><i class="fas fa-pen"></i> Write Review</a>
                </div>
                <?php if (empty($reviews)): ?>
                <div style="text-align:center;padding:3rem;color:var(--text-secondary);">
                    <div style="font-size:2rem;margin-bottom:0.75rem;">💬</div>
                    <p>No reviews yet. Be the first to review!</p>
                    <a href="#write-review" class="btn btn-primary btn-sm mt-4">Write a Review</a>
                </div>
                <?php else: ?>
                    <?php foreach ($reviews as $r): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-avatar"><?= strtoupper(substr($r['user_name'] ?? 'A', 0, 1)) ?></div>
                            <div>
                                <div style="font-weight:700;color:white;font-size:0.875rem;"><?= htmlspecialchars($r['user_name'] ?? 'Anonymous') ?></div>
                                <div class="review-stars">
                                    <?php for ($s=0;$s<5;$s++): ?><i class="fas fa-star<?= $s < ($r['overall_rating'] ?? 5) ? '' : '-o' ?>"></i><?php endfor; ?>
                                    <span style="color:var(--text-muted);font-size:0.75rem;margin-left:4px;"><?= date('M Y', strtotime($r['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>
                        <p class="review-text"><?= htmlspecialchars($r['review_text'] ?? 'Great experience at this college.') ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Placements Tab -->
            <?php elseif ($active_tab === 'placements'): ?>
            <div class="info-card">
                <h3><i class="fas fa-briefcase" style="color:#14b8a6;margin-right:8px;"></i> Placement Statistics</h3>
                <div style="text-align:center;padding:3rem;color:var(--text-secondary);">
                    <div style="font-size:2rem;margin-bottom:0.75rem;">📊</div>
                    <p>Detailed placement data coming soon</p>
                </div>
            </div>

            <!-- Gallery Tab -->
            <?php elseif ($active_tab === 'gallery'): ?>
            <div class="info-card">
                <h3><i class="fas fa-images" style="color:#a855f7;margin-right:8px;"></i> Campus Gallery</h3>
                <div style="text-align:center;padding:3rem;color:var(--text-secondary);">
                    <div style="font-size:2rem;margin-bottom:0.75rem;">🏛️</div>
                    <p>Campus photos coming soon</p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sticky Sidebar -->
        <aside class="sticky-sidebar" id="apply">
            <div class="apply-card">
                <h4>📋 Apply to <?= mb_substr($col['name'], 0, 25) ?>...</h4>
                <form action="/AdmissionSeason/apply.php" method="POST">
                    <input type="hidden" name="college_id" value="<?= $col['id'] ?>">
                    <input type="text" name="name" placeholder="Your Full Name" class="form-input" required>
                    <input type="email" name="email" placeholder="Email Address" class="form-input" required>
                    <input type="tel" name="phone" placeholder="Phone Number" class="form-input" required>
                    <select name="course" class="form-input form-select">
                        <option>Select Course</option>
                        <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem;">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </form>
                <p style="font-size:0.72rem;color:var(--text-muted);text-align:center;margin-top:0.75rem;">
                    🔒 Your data is safe. No spam guaranteed.
                </p>
            </div>

            <!-- Quick Info Widget -->
            <div class="info-card" style="margin-top:1rem;">
                <h3 style="font-size:0.8rem;"><i class="fas fa-bolt" style="color:#f59e0b;"></i> Quick Info</h3>
                <div class="info-row"><span class="label">City</span><span class="value"><?= htmlspecialchars($col['city']) ?></span></div>
                <div class="info-row"><span class="label">Type</span><span class="value"><?= htmlspecialchars($col['type']) ?></span></div>
                <?php if ($col['nirf_rank']): ?>
                <div class="info-row"><span class="label">NIRF Rank</span><span class="value" style="color:#f59e0b;">#<?= $col['nirf_rank'] ?></span></div>
                <?php endif; ?>
                <?php if ($col['naac_grade']): ?>
                <div class="info-row"><span class="label">NAAC Grade</span><span class="value" style="color:#10b981;"><?= $col['naac_grade'] ?></span></div>
                <?php endif; ?>
                <?php if ($col['official_url']): ?>
                <a href="<?= htmlspecialchars($col['official_url']) ?>" target="_blank" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;margin-top:1rem;">
                    <i class="fas fa-external-link-alt"></i> Official Site
                </a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
function addToCompare(id, name) {
    let list = JSON.parse(localStorage.getItem('compare') || '[]');
    if (list.find(c => c.id === id)) { alert(name + ' already in compare!'); return; }
    if (list.length >= 3) { alert('Max 3 colleges can be compared.'); return; }
    list.push({id, name});
    localStorage.setItem('compare', JSON.stringify(list));
    alert('✅ ' + name + ' added to compare list!');
}
</script>
</body>
</html>
