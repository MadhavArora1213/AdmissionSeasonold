<?php
require_once '../AdmissionSeason/admin/includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { header('Location: /frontend/colleges.php'); exit; }

// Fetch college
$stmt = $pdo->prepare("SELECT * FROM colleges WHERE id = ?");
$stmt->execute([$id]);
$col = $stmt->fetch();
if (!$col) { header('Location: /frontend/colleges.php'); exit; }

// Fetch reviews
$reviews = $pdo->prepare("SELECT r.*, u.name as user_name FROM reviews r LEFT JOIN users u ON r.student_id = u.id WHERE r.college_id = ? AND r.status = 'APPROVED' ORDER BY r.created_at DESC LIMIT 5");
$reviews->execute([$id]);
$reviews = $reviews->fetchAll();

// Fetch courses
$courses = $pdo->prepare("SELECT * FROM courses WHERE college_id = ? LIMIT 10");
$courses->execute([$id]);
$courses = $courses->fetchAll();

$tabs = [
    'overview' => ['label' => 'Overview', 'icon' => 'info'],
    'courses' => ['label' => 'Courses & Fees', 'icon' => 'book-open'],
    'placements' => ['label' => 'Placements', 'icon' => 'briefcase'],
    'reviews' => ['label' => 'Reviews', 'icon' => 'star'],
    'gallery' => ['label' => 'Gallery', 'icon' => 'image']
];
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (v4) -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
    </style>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter'] selection:bg-indigo-500/30">

<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">
  <!-- College Hero Banner -->
  <div class="relative overflow-hidden bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] pt-12 pb-0">
    <!-- Decorative Glow -->
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-indigo-500/10 blur-[100px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-purple-500/10 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
      <!-- Breadcrumbs -->
      <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-8">
        <a href="index.php" class="hover:text-indigo-400 transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <a href="colleges.php" class="hover:text-indigo-400 transition-colors">Colleges</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-[var(--text-secondary)] truncate max-w-[200px] sm:max-w-xs"><?= $name ?></span>
      </div>

      <div class="flex flex-col lg:flex-row items-start lg:items-end justify-between gap-6 pb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
          <!-- Logo -->
          <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-600/20 border border-indigo-500/30 flex items-center justify-center text-4xl shadow-xl shadow-indigo-500/10 flex-shrink-0 relative group">
            🎓
            <?php if ($col['is_verified']): ?>
              <div class="absolute -top-2 -right-2 w-6 h-6 bg-teal-500 rounded-full border-2 border-[var(--bg-primary)] flex items-center justify-center" title="Verified College">
                <i data-lucide="check" class="w-3.5 h-3.5 text-white"></i>
              </div>
            <?php endif; ?>
          </div>

          <!-- Title & Meta -->
          <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
              <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight"><?= $name ?></h1>
            </div>
            
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-[var(--text-secondary)] mb-4">
              <span class="flex items-center gap-1.5">
                <i data-lucide="map-pin" class="w-4 h-4 text-indigo-400"></i>
                <?= htmlspecialchars($col['city']) ?>, <?= htmlspecialchars($col['state']) ?>
              </span>
              <?php if ($col['established_year']): ?>
                <span class="flex items-center gap-1.5">
                  <i data-lucide="calendar" class="w-4 h-4 text-[var(--text-muted)]"></i>
                  Est. <?= $col['established_year'] ?>
                </span>
              <?php endif; ?>
              <?php if ($col['affiliated_to']): ?>
                <span class="flex items-center gap-1.5">
                  <i data-lucide="building-2" class="w-4 h-4 text-[var(--text-muted)]"></i>
                  <?= htmlspecialchars($col['affiliated_to']) ?>
                </span>
              <?php endif; ?>
            </div>

            <!-- Badges -->
            <div class="flex flex-wrap gap-2">
              <span class="px-2.5 py-1 text-xs font-medium rounded-md glass border border-[var(--border)] text-indigo-300">
                <?= htmlspecialchars(ucfirst(strtolower($col['type']))) ?>
              </span>
              <?php if ($col['nirf_rank']): ?>
                <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center gap-1">
                  <i data-lucide="trophy" class="w-3 h-3"></i> NIRF #<?= $col['nirf_rank'] ?>
                </span>
              <?php endif; ?>
              <?php if ($col['naac_grade']): ?>
                <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                  NAAC <?= $col['naac_grade'] ?>
                </span>
              <?php endif; ?>
              <?php if ($col['is_featured']): ?>
                <span class="px-2.5 py-1 text-xs font-medium rounded-md bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center gap-1">
                  <i data-lucide="sparkles" class="w-3 h-3"></i> Featured
                </span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full lg:w-auto">
          <button onclick="addToCompare('<?= $col['id'] ?>', '<?= addslashes($col['name']) ?>')" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl glass border border-[var(--border)] text-sm font-medium hover:bg-white/5 transition-colors">
            <i data-lucide="scale" class="w-4 h-4"></i> Compare
          </button>
          <a href="#apply" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl btn-primary text-sm font-medium">
            <i data-lucide="send" class="w-4 h-4"></i> Apply Now
          </a>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex overflow-x-auto no-scrollbar border-b border-[var(--border)]">
        <?php foreach ($tabs as $key => $tab): ?>
          <a href="?id=<?= $id ?>&tab=<?= $key ?>" 
             class="flex items-center gap-2 px-6 py-4 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
             <?= $active_tab === $key 
                ? 'border-indigo-500 text-indigo-400' 
                : 'border-transparent text-[var(--text-secondary)] hover:text-white hover:border-[var(--border)]' ?>">
            <i data-lucide="<?= $tab['icon'] ?>" class="w-4 h-4 <?= $active_tab === $key ? 'text-indigo-400' : 'text-[var(--text-muted)]' ?>"></i>
            <?= $tab['label'] ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Content Area -->
      <div class="lg:col-span-2 space-y-6">
        
        <!-- Tab Content -->
        <?php if ($active_tab === 'overview'): ?>
          <!-- About -->
          <?php if ($col['about_description']): ?>
            <div class="glass p-6 sm:p-8 rounded-2xl border border-[var(--border)]">
              <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5 text-indigo-400"></i> About <?= $name ?>
              </h2>
              <div class="text-[var(--text-secondary)] leading-relaxed text-sm space-y-4">
                <p><?= nl2br(htmlspecialchars($col['about_description'])) ?></p>
              </div>
            </div>
          <?php endif; ?>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Key Details -->
            <div class="glass p-6 rounded-2xl border border-[var(--border)]">
              <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2 pb-4 border-b border-[var(--border)]">
                <i data-lucide="list-checks" class="w-4 h-4 text-indigo-400"></i> Key Details
              </h2>
              <div class="space-y-3 text-sm">
                <?php
                $details = [
                    ['Type', ucfirst(strtolower($col['type']))],
                    ['Established', $col['established_year'] ?: 'N/A'],
                    ['Location', htmlspecialchars($col['city'] . ', ' . $col['state'])],
                    ['Gender Type', htmlspecialchars($col['gender_type'] ?: 'Co-ed')],
                    ['Campus Area', $col['campus_area_acres'] ? $col['campus_area_acres'] . ' Acres' : 'N/A'],
                ];
                foreach ($details as [$label, $value]): ?>
                  <div class="flex justify-between items-center py-1">
                    <span class="text-[var(--text-muted)]"><?= $label ?></span>
                    <span class="font-medium text-[var(--text-primary)] text-right"><?= $value ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Rankings -->
            <div class="glass p-6 rounded-2xl border border-[var(--border)]">
              <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2 pb-4 border-b border-[var(--border)]">
                <i data-lucide="medal" class="w-4 h-4 text-amber-400"></i> Accreditations
              </h2>
              <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center py-1">
                  <span class="text-[var(--text-muted)]">NIRF Rank</span>
                  <span class="font-medium <?= $col['nirf_rank'] ? 'text-amber-400' : 'text-[var(--text-primary)]' ?>">
                    <?= $col['nirf_rank'] ? '#' . $col['nirf_rank'] : 'N/A' ?>
                  </span>
                </div>
                <div class="flex justify-between items-center py-1">
                  <span class="text-[var(--text-muted)]">NAAC Grade</span>
                  <span class="font-medium <?= $col['naac_grade'] ? 'text-emerald-400' : 'text-[var(--text-primary)]' ?>">
                    <?= $col['naac_grade'] ?: 'N/A' ?>
                  </span>
                </div>
                <div class="flex justify-between items-center py-1">
                  <span class="text-[var(--text-muted)]">QS World Rank</span>
                  <span class="font-medium text-[var(--text-primary)]">N/A</span>
                </div>
              </div>
              
              <?php if ($col['official_url']): ?>
                <a href="<?= htmlspecialchars($col['official_url']) ?>" target="_blank" rel="noopener noreferrer" class="mt-6 w-full flex items-center justify-center gap-2 py-2 rounded-lg bg-[var(--bg-secondary)] border border-[var(--border)] hover:bg-white/5 transition-colors text-sm text-[var(--text-secondary)] hover:text-white">
                  <i data-lucide="external-link" class="w-4 h-4"></i> Official Website
                </a>
              <?php endif; ?>
            </div>
          </div>

          <!-- Admission Process -->
          <?php if ($col['admission_process']): ?>
            <div class="glass p-6 sm:p-8 rounded-2xl border border-[var(--border)]">
              <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-5 h-5 text-teal-400"></i> Admission Process
              </h2>
              <div class="text-[var(--text-secondary)] leading-relaxed text-sm space-y-4">
                <p><?= nl2br(htmlspecialchars($col['admission_process'])) ?></p>
              </div>
            </div>
          <?php endif; ?>

        <?php elseif ($active_tab === 'courses'): ?>
          <div class="glass p-6 rounded-2xl border border-[var(--border)]">
            <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <i data-lucide="book-open" class="w-5 h-5 text-indigo-400"></i> Offered Courses & Fees
            </h2>
            <?php if (empty($courses)): ?>
              <div class="py-12 text-center text-[var(--text-secondary)]">
                <i data-lucide="library" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                <p>Course details are currently being updated.</p>
              </div>
            <?php else: ?>
              <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                  <thead class="text-xs text-[var(--text-muted)] uppercase border-b border-[var(--border)]">
                    <tr>
                      <th class="px-4 py-3 font-medium">Course Name</th>
                      <th class="px-4 py-3 font-medium">Duration</th>
                      <th class="px-4 py-3 font-medium">Seats</th>
                      <th class="px-4 py-3 font-medium text-right">Total Fees</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-[var(--border)]">
                    <?php foreach ($courses as $c): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                      <td class="px-4 py-4">
                        <div class="font-medium text-white"><?= htmlspecialchars($c['name']) ?></div>
                        <div class="text-xs text-[var(--text-muted)] mt-0.5"><?= htmlspecialchars($c['level'] ?? 'UG') ?> Level</div>
                      </td>
                      <td class="px-4 py-4 text-[var(--text-secondary)]"><?= htmlspecialchars($c['duration_years'] ?? 4) ?> Yrs</td>
                      <td class="px-4 py-4 text-[var(--text-secondary)]"><?= $c['total_seats'] ? number_format($c['total_seats']) : 'N/A' ?></td>
                      <td class="px-4 py-4 font-bold text-emerald-400 text-right"><?= $c['total_fees'] ? '₹' . number_format($c['total_fees']) : 'N/A' ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>

        <?php elseif ($active_tab === 'reviews'): ?>
          <div class="glass p-6 rounded-2xl border border-[var(--border)]">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-[var(--border)]">
              <h2 class="text-lg font-bold text-white flex items-center gap-2">
                <i data-lucide="star" class="w-5 h-5 text-amber-400"></i> Student Reviews
              </h2>
              <button class="px-4 py-2 bg-indigo-500/10 text-indigo-400 text-sm font-medium rounded-lg hover:bg-indigo-500/20 transition-colors">
                Write a Review
              </button>
            </div>
            
            <?php if (empty($reviews)): ?>
              <div class="py-12 text-center text-[var(--text-secondary)]">
                <i data-lucide="message-square-off" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                <p>No reviews yet. Be the first to share your experience!</p>
              </div>
            <?php else: ?>
              <div class="space-y-6">
                <?php foreach ($reviews as $r): ?>
                <div class="bg-[var(--bg-secondary)] border border-[var(--border)] p-5 rounded-xl">
                  <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-inner">
                      <?= strtoupper(substr($r['user_name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div>
                      <div class="font-medium text-[var(--text-primary)] text-sm"><?= htmlspecialchars($r['user_name'] ?? 'Anonymous') ?></div>
                      <div class="flex items-center gap-2 mt-0.5">
                        <div class="flex text-amber-400">
                          <?php for ($s=0;$s<5;$s++): ?>
                            <i data-lucide="star" class="w-3 h-3 <?= $s < ($r['overall_rating'] ?? 5) ? 'fill-amber-400' : 'text-gray-600' ?>"></i>
                          <?php endfor; ?>
                        </div>
                        <span class="text-xs text-[var(--text-muted)]"><?= date('M Y', strtotime($r['created_at'])) ?></span>
                      </div>
                    </div>
                  </div>
                  <p class="text-sm text-[var(--text-secondary)] leading-relaxed">
                    <?= htmlspecialchars($r['review_text'] ?? 'Great experience at this college.') ?>
                  </p>
                </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

        <?php elseif ($active_tab === 'placements'): ?>
          <div class="glass p-6 rounded-2xl border border-[var(--border)]">
             <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <i data-lucide="briefcase" class="w-5 h-5 text-teal-400"></i> Placement Statistics
            </h2>
            <div class="py-12 text-center text-[var(--text-secondary)] border border-dashed border-[var(--border)] rounded-xl">
              <i data-lucide="bar-chart-3" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
              <p>Detailed placement data is being compiled.</p>
            </div>
          </div>

        <?php elseif ($active_tab === 'gallery'): ?>
          <div class="glass p-6 rounded-2xl border border-[var(--border)]">
             <h2 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
              <i data-lucide="image" class="w-5 h-5 text-purple-400"></i> Campus Gallery
            </h2>
            <div class="py-12 text-center text-[var(--text-secondary)] border border-dashed border-[var(--border)] rounded-xl">
              <i data-lucide="camera" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
              <p>Campus photos are currently being uploaded.</p>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Right Sidebar (Sticky) -->
      <div class="lg:col-span-1">
        <div class="sticky top-24 space-y-6">
          
          <!-- Application Form Widget -->
          <div class="glass rounded-2xl border border-indigo-500/30 overflow-hidden relative">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 z-0"></div>
            <div class="p-6 relative z-10">
              <h3 class="text-lg font-bold text-white mb-1">Interested in Admission?</h3>
              <p class="text-xs text-indigo-300 mb-6">Get free counseling from our experts.</p>
              
              <form action="apply.php" method="POST" class="space-y-4">
                <input type="hidden" name="college_id" value="<?= $col['id'] ?>">
                
                <div>
                  <label class="sr-only">Full Name</label>
                  <div class="relative">
                    <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                    <input type="text" name="name" placeholder="Full Name" required class="w-full bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-[var(--text-muted)] focus:outline-none focus:border-indigo-500 transition-colors">
                  </div>
                </div>
                
                <div>
                  <label class="sr-only">Email</label>
                  <div class="relative">
                    <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                    <input type="email" name="email" placeholder="Email Address" required class="w-full bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-[var(--text-muted)] focus:outline-none focus:border-indigo-500 transition-colors">
                  </div>
                </div>
                
                <div>
                  <label class="sr-only">Phone Number</label>
                  <div class="relative">
                    <i data-lucide="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                    <input type="tel" name="phone" placeholder="Phone Number" required class="w-full bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl py-2.5 pl-10 pr-4 text-sm text-white placeholder-[var(--text-muted)] focus:outline-none focus:border-indigo-500 transition-colors">
                  </div>
                </div>
                
                <div>
                  <label class="sr-only">Interested Course</label>
                  <div class="relative">
                    <i data-lucide="book" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                    <select name="course" class="w-full bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl py-2.5 pl-10 pr-4 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors appearance-none">
                      <?php if (empty($courses)): ?>
                        <option value="">No courses added yet</option>
                      <?php else: ?>
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $c): ?>
                          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)] pointer-events-none"></i>
                  </div>
                </div>

                <button type="submit" class="w-full btn-primary py-3 rounded-xl flex items-center justify-center gap-2 group mt-2">
                  <span>Submit Application</span>
                  <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </button>
              </form>
              <div class="mt-4 text-center">
                <span class="inline-flex items-center gap-1.5 text-xs text-[var(--text-muted)]">
                  <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                  No spam guaranteed. 100% Secure.
                </span>
              </div>
            </div>
          </div>
          
          <!-- Need Help -->
          <div class="glass p-5 rounded-2xl border border-[var(--border)] text-center">
            <div class="w-12 h-12 mx-auto bg-indigo-500/20 text-indigo-400 rounded-full flex items-center justify-center mb-3">
              <i data-lucide="bot" class="w-6 h-6"></i>
            </div>
            <h4 class="text-sm font-bold text-white mb-1">Confused about admission?</h4>
            <p class="text-xs text-[var(--text-secondary)] mb-4">Talk to our AI Counselor instantly to clear your doubts.</p>
            <a href="ai-counselor.php?college=<?= urlencode($col['name']) ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">
              Chat with AI <i data-lucide="message-circle" class="w-3 h-3"></i>
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
  lucide.createIcons();

  function addToCompare(id, name) {
    try {
      let list = JSON.parse(localStorage.getItem('compare') || '[]');
      if (list.find(c => c.id === id)) {
        alert(name + ' is already in your compare list!');
        return;
      }
      if (list.length >= 3) {
        alert('You can only compare up to 3 colleges at a time.');
        return;
      }
      list.push({id, name});
      localStorage.setItem('compare', JSON.stringify(list));
      alert('✅ ' + name + ' added to compare list!');
    } catch(e) {
      console.error(e);
    }
  }
</script>
</body>
</html>
