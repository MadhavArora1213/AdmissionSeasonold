<?php
require_once '../AdmissionSeason/admin/includes/db.php';

// Fetch exams
$search = $_GET['q'] ?? '';
$level = $_GET['level'] ?? '';
$stream = $_GET['stream'] ?? '';

$where = ['1=1'];
$params = [];

if ($search) {
    $where[] = '(name LIKE ? OR full_name LIKE ?)';
    $params[] = "%$search%"; $params[] = "%$search%";
}
if ($level) {
    $where[] = 'level = ?';
    $params[] = strtoupper($level);
}
if ($stream) {
    $where[] = 'stream = ?';
    $params[] = $stream;
}

$where_sql = implode(' AND ', $where);

$stmt = $pdo->prepare("SELECT e.*, 
    (SELECT exam_date FROM exam_sessions WHERE exam_id = e.id AND year = YEAR(CURRENT_DATE) LIMIT 1) as next_exam_date
    FROM exams e 
    WHERE $where_sql 
    ORDER BY name ASC");
$stmt->execute($params);
$exams = $stmt->fetchAll();

// Get unique streams
$streams = $pdo->query("SELECT DISTINCT stream FROM exams WHERE stream IS NOT NULL ORDER BY stream")->fetchAll(PDO::FETCH_COLUMN);

$page_title = "Entrance Exams in India 2026 | AdmissionSeason";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Explore top entrance exams in India. Get details on syllabus, dates, pattern, and participating colleges.">
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
  <!-- Header & Filters -->
  <div class="bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-purple-500/10 blur-[100px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-500/10 blur-[100px] rounded-full pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 relative z-10">
      <h1 class="text-3xl sm:text-4xl font-bold text-white tracking-tight mb-3">
        Entrance Exams Hub
      </h1>
      <p class="text-[var(--text-secondary)] text-sm sm:text-base mb-8 max-w-2xl">
        Your ultimate guide to 350+ entrance exams in India. Track dates, download syllabus, and find cutoffs for top colleges.
      </p>

      <form action="exams.php" method="GET" class="flex flex-col sm:flex-row gap-3">
        <!-- Search Input -->
        <div class="relative flex-1">
          <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
          <input
            type="text"
            name="q"
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Search exams (e.g. JEE Main, CAT)..."
            class="w-full pl-11 pr-4 py-3.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all shadow-inner"
          />
        </div>
        
        <!-- Stream Filter -->
        <div class="relative sm:w-48">
          <select
            name="stream"
            onchange="this.form.submit()"
            class="w-full appearance-none pl-4 pr-10 py-3.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white outline-none focus:border-indigo-500/60 cursor-pointer shadow-inner"
          >
            <option value="">All Streams</option>
            <?php foreach ($streams as $s): if(!$s) continue; ?>
              <option value="<?= htmlspecialchars($s) ?>" <?= $stream === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
            <?php endforeach; ?>
          </select>
          <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)] pointer-events-none"></i>
        </div>

        <!-- Level Filter -->
        <div class="relative sm:w-48">
          <select
            name="level"
            onchange="this.form.submit()"
            class="w-full appearance-none pl-4 pr-10 py-3.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white outline-none focus:border-indigo-500/60 cursor-pointer shadow-inner"
          >
            <option value="">All Levels</option>
            <option value="NATIONAL" <?= strcasecmp($level, 'NATIONAL')===0 ? 'selected' : '' ?>>National Level</option>
            <option value="STATE" <?= strcasecmp($level, 'STATE')===0 ? 'selected' : '' ?>>State Level</option>
            <option value="UNIVERSITY" <?= strcasecmp($level, 'UNIVERSITY')===0 ? 'selected' : '' ?>>University Level</option>
          </select>
          <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)] pointer-events-none"></i>
        </div>
        
        <input type="submit" class="hidden">
      </form>

      <!-- Active Filters -->
      <?php if ($search || $level || $stream): ?>
      <div class="flex flex-wrap gap-2 mt-4">
        <?php if ($search): ?>
          <span class="flex items-center gap-1.5 px-3 py-1 text-xs bg-indigo-500/10 text-indigo-300 rounded-lg border border-indigo-500/20">
            <?= htmlspecialchars($search) ?>
            <a href="?level=<?= urlencode($level) ?>&stream=<?= urlencode($stream) ?>"><i data-lucide="x" class="w-3 h-3 hover:text-white"></i></a>
          </span>
        <?php endif; ?>
        <?php if ($level): ?>
          <span class="flex items-center gap-1.5 px-3 py-1 text-xs bg-indigo-500/10 text-indigo-300 rounded-lg border border-indigo-500/20">
            <?= htmlspecialchars(ucfirst(strtolower($level))) ?> Level
            <a href="?q=<?= urlencode($search) ?>&stream=<?= urlencode($stream) ?>"><i data-lucide="x" class="w-3 h-3 hover:text-white"></i></a>
          </span>
        <?php endif; ?>
        <?php if ($stream): ?>
          <span class="flex items-center gap-1.5 px-3 py-1 text-xs bg-indigo-500/10 text-indigo-300 rounded-lg border border-indigo-500/20">
            <?= htmlspecialchars(ucfirst($stream)) ?>
            <a href="?q=<?= urlencode($search) ?>&level=<?= urlencode($level) ?>"><i data-lucide="x" class="w-3 h-3 hover:text-white"></i></a>
          </span>
        <?php endif; ?>
        <a href="?" class="text-xs text-[var(--text-muted)] hover:text-white transition-colors mt-1.5 ml-2">Clear filters</a>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Exams Grid -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
    <?php if (empty($exams)): ?>
      <div class="text-center py-20 border border-dashed border-[var(--border)] rounded-3xl bg-white/[0.01]">
        <div class="w-16 h-16 mx-auto bg-[var(--bg-secondary)] rounded-full flex items-center justify-center mb-4 border border-[var(--border)]">
          <i data-lucide="file-search" class="w-8 h-8 text-[var(--text-muted)]"></i>
        </div>
        <h3 class="text-lg font-bold text-white mb-2">No exams found</h3>
        <p class="text-sm text-[var(--text-secondary)] mb-6 max-w-sm mx-auto">We couldn't find any exams matching your current filters. Try adjusting your search.</p>
        <a href="?" class="btn-primary inline-flex">Clear All Filters</a>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($exams as $e): ?>
          <div class="glass rounded-2xl border border-[var(--border)] p-6 card-hover group flex flex-col h-full relative overflow-hidden">
            <!-- Decorative gradient on hover -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 to-purple-500/0 group-hover:from-indigo-500/5 group-hover:to-purple-500/5 transition-all duration-500 pointer-events-none z-0"></div>
            
            <div class="relative z-10 flex-1 flex flex-col">
              <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 font-bold tracking-wider">
                  <?= substr($e['name'], 0, 3) ?>
                </div>
                <?php if ($e['level']): ?>
                  <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full bg-[var(--bg-secondary)] border border-[var(--border)] text-[var(--text-secondary)] group-hover:border-indigo-500/30 group-hover:text-indigo-300 transition-colors">
                    <?= htmlspecialchars($e['level']) ?>
                  </span>
                <?php endif; ?>
              </div>

              <h2 class="text-lg font-bold text-white group-hover:text-indigo-300 transition-colors mb-1">
                <?= htmlspecialchars($e['name']) ?>
              </h2>
              <p class="text-xs text-[var(--text-secondary)] line-clamp-1 mb-4" title="<?= htmlspecialchars($e['full_name']) ?>">
                <?= htmlspecialchars($e['full_name'] ?: $e['name']) ?>
              </p>

              <div class="space-y-2 mt-auto text-sm">
                <?php if ($e['stream']): ?>
                <div class="flex items-center gap-2 text-[var(--text-muted)]">
                  <i data-lucide="book-open" class="w-4 h-4 text-indigo-400/70"></i>
                  <span class="text-[var(--text-secondary)]"><?= htmlspecialchars(ucfirst($e['stream'])) ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($e['mode']): ?>
                <div class="flex items-center gap-2 text-[var(--text-muted)]">
                  <i data-lucide="monitor" class="w-4 h-4 text-teal-400/70"></i>
                  <span class="text-[var(--text-secondary)]"><?= htmlspecialchars($e['mode']) ?></span>
                </div>
                <?php endif; ?>

                <?php if ($e['next_exam_date']): ?>
                <div class="flex items-center gap-2 text-[var(--text-muted)]">
                  <i data-lucide="calendar" class="w-4 h-4 text-amber-400/70"></i>
                  <span class="text-amber-400/90 font-medium"><?= date('d M Y', strtotime($e['next_exam_date'])) ?></span>
                </div>
                <?php endif; ?>
              </div>
            </div>

            <div class="relative z-10 pt-5 mt-5 border-t border-[var(--border)] flex items-center justify-between">
              <div class="text-xs text-[var(--text-muted)]">
                <?php if ($e['total_marks']): ?>
                  Max Marks: <span class="text-white font-medium"><?= $e['total_marks'] ?></span>
                <?php endif; ?>
              </div>
              <a href="exam.php?slug=<?= urlencode($e['slug']) ?>" class="text-sm font-semibold text-indigo-400 group-hover:text-indigo-300 flex items-center gap-1 transition-colors">
                View Details <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
  lucide.createIcons();
</script>
</body>
</html>
