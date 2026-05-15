<?php
require_once '../AdmissionSeason/admin/includes/db.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: exams.php'); exit; }

$stmt = $pdo->prepare("SELECT e.*, 
    (SELECT es.application_open  FROM exam_sessions es WHERE es.exam_id = e.id ORDER BY es.year DESC LIMIT 1) as app_open,
    (SELECT es.application_close FROM exam_sessions es WHERE es.exam_id = e.id ORDER BY es.year DESC LIMIT 1) as app_close,
    (SELECT es.exam_date         FROM exam_sessions es WHERE es.exam_id = e.id ORDER BY es.year DESC LIMIT 1) as exam_date,
    (SELECT es.admit_card_date   FROM exam_sessions es WHERE es.exam_id = e.id ORDER BY es.year DESC LIMIT 1) as admit_card_date,
    (SELECT es.result_date       FROM exam_sessions es WHERE es.exam_id = e.id ORDER BY es.year DESC LIMIT 1) as result_date
    FROM exams e WHERE e.slug = ?");
$stmt->execute([$slug]);
$e = $stmt->fetch();

if (!$e) { header('Location: exams.php'); exit; }

// Colleges that accept this exam
$colleges = $pdo->prepare("SELECT DISTINCT c.id, c.name, c.city, c.state, c.type, c.nirf_rank
    FROM colleges c
    JOIN courses co ON co.college_id = c.id
    JOIN course_exams ce ON ce.course_id = co.id
    WHERE ce.exam_id = ?
    ORDER BY c.nirf_rank ASC
    LIMIT 6");
$colleges->execute([$e['id']]);
$acceptingColleges = $colleges->fetchAll();

// Stream icon mapping
$streamIcons = [
    'engineering' => '⚙️', 'medical' => '🏥', 'management' => '📊',
    'law' => '⚖️', 'design' => '🎨', 'science' => '🔬',
    'arts' => '🎭', 'commerce' => '💼', 'pharmacy' => '💊',
];
$icon = $streamIcons[strtolower($e['stream'] ?? '')] ?? '📝';

$levelColors = [
    'NATIONAL'     => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
    'STATE'        => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
    'UNIVERSITY'   => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
    'COLLEGE_LEVEL'=> 'bg-purple-500/10 text-purple-400 border-purple-500/20',
];
$lc = $levelColors[$e['level']] ?? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($e['name']) ?> 2026 — Dates, Syllabus, Pattern | AdmissionSeason</title>
    <meta name="description" content="Complete details of <?= htmlspecialchars($e['full_name'] ?: $e['name']) ?> — exam dates, syllabus, pattern, eligibility and top accepting colleges.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">

  <!-- Hero Banner -->
  <div class="bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] relative overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 relative z-10">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-6">
        <a href="index.php" class="hover:text-white transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <a href="exams.php" class="hover:text-white transition-colors">Exams</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span class="text-white"><?= htmlspecialchars($e['name']) ?></span>
      </div>

      <div class="flex flex-col sm:flex-row items-start gap-6">
        <!-- Icon -->
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center text-4xl flex-shrink-0 shadow-lg">
          <?= $icon ?>
        </div>

        <div class="flex-1">
          <div class="flex flex-wrap items-center gap-3 mb-2">
            <span class="px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-full border <?= $lc ?>"><?= htmlspecialchars($e['level']) ?></span>
            <?php if ($e['stream']): ?>
            <span class="px-3 py-1 text-xs font-bold bg-[var(--bg-secondary)] border border-[var(--border)] text-[var(--text-secondary)] rounded-full uppercase tracking-widest"><?= htmlspecialchars($e['stream']) ?></span>
            <?php endif; ?>
          </div>
          <h1 class="text-3xl sm:text-4xl font-black text-white mb-1"><?= htmlspecialchars($e['name']) ?></h1>
          <p class="text-[var(--text-secondary)] mb-4"><?= htmlspecialchars($e['full_name'] ?: '') ?></p>

          <!-- Quick Stats -->
          <div class="flex flex-wrap gap-4 text-sm">
            <?php if ($e['conducting_body']): ?>
            <div class="flex items-center gap-1.5 text-[var(--text-secondary)]">
              <i data-lucide="building-2" class="w-4 h-4 text-indigo-400"></i>
              <?= htmlspecialchars($e['conducting_body']) ?>
            </div>
            <?php endif; ?>
            <?php if ($e['mode']): ?>
            <div class="flex items-center gap-1.5 text-[var(--text-secondary)]">
              <i data-lucide="monitor" class="w-4 h-4 text-teal-400"></i>
              <?= htmlspecialchars($e['mode']) ?>
            </div>
            <?php endif; ?>
            <?php if ($e['total_marks']): ?>
            <div class="flex items-center gap-1.5 text-[var(--text-secondary)]">
              <i data-lucide="clipboard-list" class="w-4 h-4 text-amber-400"></i>
              <?= $e['total_marks'] ?> Marks
            </div>
            <?php endif; ?>
            <?php if ($e['duration_minutes']): ?>
            <div class="flex items-center gap-1.5 text-[var(--text-secondary)]">
              <i data-lucide="clock" class="w-4 h-4 text-purple-400"></i>
              <?= $e['duration_minutes'] ?> Minutes
            </div>
            <?php endif; ?>
            <?php if ($e['negative_marking']): ?>
            <div class="flex items-center gap-1.5 text-red-400">
              <i data-lucide="minus-circle" class="w-4 h-4"></i>
              Negative Marking
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- CTAs -->
        <div class="flex flex-col gap-3 flex-shrink-0">
          <?php if ($e['official_url']): ?>
          <a href="<?= htmlspecialchars($e['official_url']) ?>" target="_blank" rel="noopener noreferrer"
             class="flex items-center gap-2 btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold">
            <i data-lucide="external-link" class="w-4 h-4"></i> Official Site
          </a>
          <?php endif; ?>
          <?php if ($e['syllabus_pdf_url']): ?>
          <a href="<?= htmlspecialchars($e['syllabus_pdf_url']) ?>" target="_blank" rel="noopener noreferrer"
             class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold glass border border-[var(--border)] hover:bg-white/5 transition-colors">
            <i data-lucide="file-text" class="w-4 h-4 text-indigo-400"></i> Download Syllabus
          </a>
          <?php endif; ?>
          <a href="ai-counselor.php?q=<?= urlencode($e['name'] . ' exam guidance') ?>"
             class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold glass border border-indigo-500/30 text-indigo-400 hover:bg-indigo-500/10 transition-colors">
            <i data-lucide="bot" class="w-4 h-4"></i> Ask AI Counselor
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- Left: Main Content -->
      <div class="lg:col-span-2 space-y-6">

        <!-- Important Dates -->
        <div class="glass rounded-2xl border border-[var(--border)] overflow-hidden">
          <div class="px-6 py-4 border-b border-[var(--border)] flex items-center gap-2">
            <i data-lucide="calendar-check" class="w-5 h-5 text-amber-400"></i>
            <h2 class="font-bold text-white text-lg">Important Dates 2026</h2>
          </div>
          <div class="divide-y divide-[var(--border)]">
            <?php
            $dates = [
              ['label'=>'Application Opens',  'val'=>$e['app_open'],       'icon'=>'calendar-plus',  'color'=>'text-emerald-400'],
              ['label'=>'Application Closes', 'val'=>$e['app_close'],      'icon'=>'calendar-x',     'color'=>'text-red-400'],
              ['label'=>'Admit Card',         'val'=>$e['admit_card_date'],'icon'=>'id-card',        'color'=>'text-blue-400'],
              ['label'=>'Exam Date',          'val'=>$e['exam_date'],      'icon'=>'pen-line',       'color'=>'text-indigo-400'],
              ['label'=>'Result Date',        'val'=>$e['result_date'],    'icon'=>'award',          'color'=>'text-purple-400'],
            ];
            $hasDate = false;
            foreach ($dates as $d):
              if (!$d['val']) continue;
              $hasDate = true;
              $isPast  = strtotime($d['val']) < time();
            ?>
            <div class="flex items-center justify-between px-6 py-4 hover:bg-white/[0.02] transition-colors">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-[var(--bg-secondary)] border border-[var(--border)] flex items-center justify-center">
                  <i data-lucide="<?= $d['icon'] ?>" class="w-4 h-4 <?= $d['color'] ?>"></i>
                </div>
                <span class="text-sm text-[var(--text-secondary)]"><?= $d['label'] ?></span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-sm font-semibold <?= $isPast ? 'text-[var(--text-muted)] line-through' : 'text-white' ?>">
                  <?= date('d M Y', strtotime($d['val'])) ?>
                </span>
                <?php if ($isPast): ?>
                <span class="text-[10px] px-2 py-0.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-full">Closed</span>
                <?php else: ?>
                <span class="text-[10px] px-2 py-0.5 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full">Open</span>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
            <?php if (!$hasDate): ?>
            <div class="px-6 py-8 text-center text-[var(--text-muted)] text-sm">
              <i data-lucide="calendar" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
              <p>Dates for 2026 will be announced soon. Check the official website.</p>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Exam Pattern -->
        <div class="glass rounded-2xl border border-[var(--border)] p-6">
          <h2 class="font-bold text-white text-lg mb-5 flex items-center gap-2">
            <i data-lucide="layout-list" class="w-5 h-5 text-indigo-400"></i> Exam Pattern
          </h2>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php
            $pattern = [
              ['label'=>'Total Marks',   'val'=> $e['total_marks']      ? $e['total_marks']           : 'N/A', 'icon'=>'hash',      'color'=>'text-indigo-400'],
              ['label'=>'Duration',      'val'=> $e['duration_minutes']  ? $e['duration_minutes'].' min': 'N/A', 'icon'=>'clock',     'color'=>'text-amber-400'],
              ['label'=>'Mode',          'val'=> $e['mode']              ?: 'N/A',                              'icon'=>'monitor',   'color'=>'text-teal-400'],
              ['label'=>'Neg. Marking',  'val'=> $e['negative_marking']  ? 'Yes' : 'No',                        'icon'=>'minus-circle','color'=> $e['negative_marking'] ? 'text-red-400':'text-emerald-400'],
            ];
            foreach ($pattern as $p):
            ?>
            <div class="glass rounded-xl border border-[var(--border)] p-4 text-center">
              <div class="w-9 h-9 mx-auto rounded-lg bg-[var(--bg-secondary)] border border-[var(--border)] flex items-center justify-center mb-2">
                <i data-lucide="<?= $p['icon'] ?>" class="w-4 h-4 <?= $p['color'] ?>"></i>
              </div>
              <div class="text-xs text-[var(--text-muted)] mb-1"><?= $p['label'] ?></div>
              <div class="font-bold text-white text-sm"><?= htmlspecialchars((string)$p['val']) ?></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Accepting Colleges -->
        <?php if (!empty($acceptingColleges)): ?>
        <div class="glass rounded-2xl border border-[var(--border)] overflow-hidden">
          <div class="px-6 py-4 border-b border-[var(--border)] flex items-center justify-between">
            <h2 class="font-bold text-white text-lg flex items-center gap-2">
              <i data-lucide="university" class="w-5 h-5 text-indigo-400"></i> Top Accepting Colleges
            </h2>
            <a href="colleges.php" class="text-xs text-indigo-400 hover:text-indigo-300">View All →</a>
          </div>
          <div class="divide-y divide-[var(--border)]">
            <?php foreach ($acceptingColleges as $c): ?>
            <a href="college.php?id=<?= $c['id'] ?>" class="flex items-center gap-4 px-6 py-4 hover:bg-white/[0.02] transition-colors group">
              <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500/10 to-purple-500/10 border border-indigo-500/20 flex items-center justify-center text-xl flex-shrink-0">🎓</div>
              <div class="flex-1 min-w-0">
                <div class="font-semibold text-white group-hover:text-indigo-400 transition-colors truncate"><?= htmlspecialchars($c['name']) ?></div>
                <div class="text-xs text-[var(--text-muted)]"><?= htmlspecialchars($c['city'] . ', ' . $c['state']) ?> • <?= $c['type'] ?></div>
              </div>
              <?php if ($c['nirf_rank']): ?>
              <span class="text-xs font-bold text-amber-400 flex-shrink-0">NIRF #<?= $c['nirf_rank'] ?></span>
              <?php endif; ?>
              <i data-lucide="arrow-right" class="w-4 h-4 text-[var(--text-muted)] group-hover:text-indigo-400 group-hover:translate-x-1 transition-all"></i>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Right: Sidebar -->
      <div class="space-y-5">

        <!-- Quick Info -->
        <div class="glass rounded-2xl border border-[var(--border)] p-6">
          <h3 class="font-bold text-white mb-4 flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4 text-indigo-400"></i> Quick Info
          </h3>
          <div class="space-y-3 text-sm">
            <?php if ($e['conducting_body']): ?>
            <div class="flex justify-between gap-2">
              <span class="text-[var(--text-muted)]">Conducting Body</span>
              <span class="text-white font-medium text-right"><?= htmlspecialchars($e['conducting_body']) ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between gap-2">
              <span class="text-[var(--text-muted)]">Level</span>
              <span class="text-white font-medium"><?= htmlspecialchars($e['level']) ?></span>
            </div>
            <?php if ($e['stream']): ?>
            <div class="flex justify-between gap-2">
              <span class="text-[var(--text-muted)]">Stream</span>
              <span class="text-white font-medium"><?= htmlspecialchars(ucfirst($e['stream'])) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($e['mode']): ?>
            <div class="flex justify-between gap-2">
              <span class="text-[var(--text-muted)]">Mode</span>
              <span class="text-white font-medium"><?= htmlspecialchars($e['mode']) ?></span>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Preparation Tips -->
        <div class="glass rounded-2xl border border-amber-500/20 p-6 bg-amber-500/5">
          <h3 class="font-bold text-amber-400 mb-4 flex items-center gap-2 text-sm">
            <i data-lucide="lightbulb" class="w-4 h-4"></i> Preparation Tips
          </h3>
          <ul class="space-y-2.5 text-xs text-[var(--text-secondary)]">
            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-amber-400 mt-0.5 flex-shrink-0"></i> Download the official syllabus and create a topic-wise plan</li>
            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-amber-400 mt-0.5 flex-shrink-0"></i> Solve at least 10 previous year question papers</li>
            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-amber-400 mt-0.5 flex-shrink-0"></i> Take weekly mock tests to build exam temperament</li>
            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-amber-400 mt-0.5 flex-shrink-0"></i> Focus on NCERT for foundation, then advanced books</li>
            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-amber-400 mt-0.5 flex-shrink-0"></i> Revise formulas and concepts 1 week before exam</li>
          </ul>
        </div>

        <!-- AI Counselor CTA -->
        <div class="glass rounded-2xl border border-indigo-500/20 p-6 bg-indigo-500/5 text-center">
          <div class="w-14 h-14 mx-auto rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mb-4 shadow-lg shadow-indigo-500/30">
            <i data-lucide="bot" class="w-7 h-7 text-white"></i>
          </div>
          <h3 class="font-bold text-white mb-2">Not sure about <?= htmlspecialchars($e['name']) ?>?</h3>
          <p class="text-xs text-[var(--text-secondary)] mb-4">Talk to our AI Counselor for personalized exam strategy and college selection.</p>
          <a href="ai-counselor.php" class="w-full btn-primary py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center gap-2">
            <i data-lucide="sparkles" class="w-4 h-4"></i> Chat with AI Counselor
          </a>
        </div>

        <!-- Rank Predictor CTA -->
        <a href="rank-predictor.php" class="glass rounded-2xl border border-emerald-500/20 p-5 bg-emerald-500/5 flex items-center gap-4 hover:border-emerald-500/40 transition-colors group">
          <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0">
            <i data-lucide="trending-up" class="w-5 h-5 text-emerald-400"></i>
          </div>
          <div>
            <div class="font-semibold text-white text-sm group-hover:text-emerald-400 transition-colors">Predict Your Rank</div>
            <div class="text-xs text-[var(--text-muted)]">Enter your score → get rank</div>
          </div>
          <i data-lucide="arrow-right" class="w-4 h-4 text-[var(--text-muted)] ml-auto group-hover:translate-x-1 transition-transform"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>lucide.createIcons();</script>
</body>
</html>
