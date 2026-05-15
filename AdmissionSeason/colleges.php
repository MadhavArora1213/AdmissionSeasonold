<?php
require_once 'admin/includes/db.php';

// Filters from GET
$search = trim($_GET['q'] ?? '');
$type   = $_GET['type'] ?? '';
$state  = $_GET['state'] ?? '';
$stream = $_GET['stream'] ?? '';
$sort   = $_GET['sort'] ?? 'nirf';
$page   = max(1, (int)($_GET['page'] ?? 1));
$per_page = 15;

// Build WHERE
$where = ['1=1'];
$params = [];

if ($search) {
    $where[] = '(c.name LIKE ? OR c.city LIKE ? OR c.state LIKE ?)';
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
}
if ($type) {
    $where[] = 'c.type = ?';
    $params[] = strtoupper($type);
}
if ($state) {
    $where[] = 'c.state = ?';
    $params[] = $state;
}

$where_sql = implode(' AND ', $where);

// Sort
$order_map = [
    'nirf'     => 'c.nirf_rank ASC',
    'rating'   => 'c.data_quality_score DESC',
    'name'     => 'c.name ASC',
    'featured' => 'c.is_featured DESC, c.nirf_rank ASC',
];
$order_sql = $order_map[$sort] ?? 'c.nirf_rank ASC';

// Count
$count_sql = "SELECT COUNT(*) FROM colleges c WHERE $where_sql";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);
$offset = ($page - 1) * $per_page;

// Fetch
$sql = "SELECT c.id, c.name, c.city, c.state, c.type, c.nirf_rank, c.naac_grade, c.is_verified, c.is_featured, c.about_description
        FROM colleges c WHERE $where_sql ORDER BY $order_sql LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$colleges = $stmt->fetchAll();

// Get distinct states for filter dropdown
$states = $pdo->query("SELECT DISTINCT state FROM colleges ORDER BY state")->fetchAll(PDO::FETCH_COLUMN);

$page_title = ($search ? htmlspecialchars($search) . " — " : "") . "Colleges in India 2026 | AdmissionSeason";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Browse and compare 30,000+ colleges in India. Filter by stream, state, fees, NAAC grade and NIRF ranking.">
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
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter']">

<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">
  <!-- Header & Filters -->
  <div class="bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
      <h1 class="text-3xl font-bold text-white mb-2">
        Colleges in India <span class="text-[var(--text-muted)] text-xl font-normal">(<?= number_format($total) ?> results)</span>
      </h1>
      <p class="text-[var(--text-secondary)] mb-6">
        Compare colleges by fees, NIRF ranking, NAAC grade, placements & reviews
      </p>

      <form action="/AdmissionSeason/colleges.php" method="GET" id="filterForm">
        <!-- Search + Sort Row -->
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
            <input
              type="text"
              name="q"
              value="<?= htmlspecialchars($search) ?>"
              placeholder="Search college name or city..."
              class="w-full pl-10 pr-4 py-3 bg-[var(--bg-card)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all"
            />
          </div>
          
          <button
            type="button"
            onclick="document.getElementById('filterPanel').classList.toggle('hidden')"
            class="flex items-center gap-2 px-4 py-3 text-sm rounded-xl border transition-all glass border-[var(--border)] text-[var(--text-secondary)] hover:text-white"
          >
            <i data-lucide="filter" class="w-4 h-4"></i>
            Filters
            <?php
            $active_count = ($state ? 1 : 0) + ($type ? 1 : 0) + ($stream ? 1 : 0);
            if ($active_count > 0):
            ?>
              <span class="w-5 h-5 bg-indigo-500 text-white text-xs rounded-full flex items-center justify-center"><?= $active_count ?></span>
            <?php endif; ?>
          </button>
          
          <div class="relative">
            <select
              name="sort"
              onchange="this.form.submit()"
              class="appearance-none pl-4 pr-10 py-3 bg-[var(--bg-card)] border border-[var(--border)] rounded-xl text-sm text-white outline-none focus:border-indigo-500/60 cursor-pointer"
            >
              <option value="nirf" <?= $sort == 'nirf' ? 'selected' : '' ?>>NIRF Rank</option>
              <option value="rating" <?= $sort == 'rating' ? 'selected' : '' ?>>Rating</option>
              <option value="name" <?= $sort == 'name' ? 'selected' : '' ?>>Name (A-Z)</option>
              <option value="featured" <?= $sort == 'featured' ? 'selected' : '' ?>>Featured First</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)] pointer-events-none"></i>
          </div>
        </div>

        <!-- Filter Panel -->
        <div id="filterPanel" class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 glass rounded-xl border border-[var(--border)] <?= $active_count > 0 ? '' : 'hidden' ?>">
          <div>
            <label class="text-xs text-[var(--text-muted)] mb-1 block">State</label>
            <select name="state" onchange="this.form.submit()" class="w-full px-3 py-2 bg-[var(--bg-card)] border border-[var(--border)] rounded-lg text-sm text-white outline-none focus:border-indigo-500/60">
              <option value="">All States</option>
              <?php foreach ($states as $s): if(!$s) continue; ?>
                <option value="<?= htmlspecialchars($s) ?>" <?= $state === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="text-xs text-[var(--text-muted)] mb-1 block">Type</label>
            <select name="type" onchange="this.form.submit()" class="w-full px-3 py-2 bg-[var(--bg-card)] border border-[var(--border)] rounded-lg text-sm text-white outline-none focus:border-indigo-500/60">
              <option value="">All Types</option>
              <option value="GOVERNMENT" <?= strcasecmp($type, 'GOVERNMENT')===0 ? 'selected' : '' ?>>Government</option>
              <option value="PRIVATE" <?= strcasecmp($type, 'PRIVATE')===0 ? 'selected' : '' ?>>Private</option>
              <option value="DEEMED" <?= strcasecmp($type, 'DEEMED')===0 ? 'selected' : '' ?>>Deemed</option>
            </select>
          </div>
          <div>
            <label class="text-xs text-[var(--text-muted)] mb-1 block">Stream</label>
            <select name="stream" onchange="this.form.submit()" class="w-full px-3 py-2 bg-[var(--bg-card)] border border-[var(--border)] rounded-lg text-sm text-white outline-none focus:border-indigo-500/60">
              <option value="">All Streams</option>
              <option value="engineering" <?= $stream === 'engineering' ? 'selected' : '' ?>>Engineering</option>
              <option value="medical" <?= $stream === 'medical' ? 'selected' : '' ?>>Medical</option>
              <option value="management" <?= $stream === 'management' ? 'selected' : '' ?>>Management</option>
            </select>
          </div>
        </div>

        <!-- Hidden submit for input enter key -->
        <input type="submit" class="hidden">
      </form>

      <!-- Active Filter Chips -->
      <?php if ($active_count > 0 || $search): ?>
      <div class="flex flex-wrap gap-2 mt-3">
        <?php if ($search): ?>
          <span class="flex items-center gap-1.5 px-3 py-1 text-xs bg-indigo-500/20 text-indigo-300 rounded-full border border-indigo-500/30">
            <?= htmlspecialchars($search) ?>
            <a href="?type=<?= urlencode($type) ?>&state=<?= urlencode($state) ?>&stream=<?= urlencode($stream) ?>&sort=<?= urlencode($sort) ?>"><i data-lucide="x" class="w-3 h-3"></i></a>
          </span>
        <?php endif; ?>
        <?php if ($state): ?>
          <span class="flex items-center gap-1.5 px-3 py-1 text-xs bg-indigo-500/20 text-indigo-300 rounded-full border border-indigo-500/30">
            <?= htmlspecialchars($state) ?>
            <a href="?q=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>&stream=<?= urlencode($stream) ?>&sort=<?= urlencode($sort) ?>"><i data-lucide="x" class="w-3 h-3"></i></a>
          </span>
        <?php endif; ?>
        <?php if ($type): ?>
          <span class="flex items-center gap-1.5 px-3 py-1 text-xs bg-indigo-500/20 text-indigo-300 rounded-full border border-indigo-500/30">
            <?= htmlspecialchars($type) ?>
            <a href="?q=<?= urlencode($search) ?>&state=<?= urlencode($state) ?>&stream=<?= urlencode($stream) ?>&sort=<?= urlencode($sort) ?>"><i data-lucide="x" class="w-3 h-3"></i></a>
          </span>
        <?php endif; ?>
        <?php if ($stream): ?>
          <span class="flex items-center gap-1.5 px-3 py-1 text-xs bg-indigo-500/20 text-indigo-300 rounded-full border border-indigo-500/30">
            <?= htmlspecialchars(ucfirst($stream)) ?>
            <a href="?q=<?= urlencode($search) ?>&state=<?= urlencode($state) ?>&type=<?= urlencode($type) ?>&sort=<?= urlencode($sort) ?>"><i data-lucide="x" class="w-3 h-3"></i></a>
          </span>
        <?php endif; ?>
        <a href="?" class="text-xs text-[var(--text-muted)] hover:text-white transition-colors mt-1.5 ml-1">Clear all</a>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- College Grid -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <?php if (empty($colleges)): ?>
      <div class="text-center py-20 text-[var(--text-secondary)]">
        <div class="text-5xl mb-4">🔍</div>
        <p class="text-lg font-medium text-white mb-2">No colleges found</p>
        <p class="text-sm">Try adjusting your filters or search term</p>
        <a href="?" class="inline-block mt-4 text-indigo-400 hover:text-indigo-300 text-sm">Clear Filters</a>
      </div>
    <?php else: ?>
      <div class="grid gap-4">
        <?php
        $rank = $offset;
        foreach ($colleges as $c):
          $rank++;
          // Generate a fake rating based on DB data for display purposes
          $rating = 4.0 + (min($c['nirf_rank'] ?: 100, 100) / 100);
          $reviews = rand(500, 5000);
          $fee = rand(50000, 300000);
          $feeStr = $fee >= 100000 ? "₹" . round($fee/100000, 1) . "L/yr" : "₹" . round($fee/1000) . "K/yr";
        ?>
          <a href="/AdmissionSeason/college.php?id=<?= $c['id'] ?>" class="glass rounded-2xl border border-[var(--border)] p-5 card-hover group flex flex-col sm:flex-row gap-5">
            <!-- Rank Badge -->
            <div class="flex-shrink-0 flex sm:flex-col items-center gap-3 sm:gap-1 sm:w-16 sm:text-center">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center text-xl">🎓</div>
              <div class="sm:hidden text-xs text-[var(--text-muted)]">#<?= $rank ?></div>
              <div class="hidden sm:block text-xs text-[var(--text-muted)] mt-1">#<?= $rank ?></div>
            </div>

            <!-- Main Info -->
            <div class="flex-1 min-w-0">
              <div class="flex flex-wrap items-start gap-2 mb-1">
                <h2 class="text-base font-bold text-white group-hover:text-indigo-300 transition-colors line-clamp-2">
                  <?= htmlspecialchars($c['name']) ?>
                </h2>
                <?php if ($c['is_featured']): ?>
                  <span class="flex-shrink-0 px-2 py-0.5 text-xs font-semibold bg-amber-500/20 text-amber-300 rounded-full border border-amber-500/30">Featured</span>
                <?php endif; ?>
              </div>

              <div class="flex flex-wrap items-center gap-3 text-sm text-[var(--text-secondary)] mb-3">
                <span class="flex items-center gap-1">
                  <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                  <?= htmlspecialchars($c['city']) ?>, <?= htmlspecialchars($c['state']) ?>
                </span>
                <?php if ($c['nirf_rank']): ?>
                  <span class="flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-indigo-400"></i>
                    NIRF #<?= $c['nirf_rank'] ?>
                  </span>
                <?php endif; ?>
                <span class="px-2 py-0.5 text-xs glass rounded-full border border-[var(--border)]">
                  <?= htmlspecialchars($c['type']) ?>
                </span>
                <?php if ($c['naac_grade']): ?>
                  <span class="px-2 py-0.5 text-xs bg-emerald-500/10 text-emerald-400 rounded-full border border-emerald-500/30">
                    NAAC <?= htmlspecialchars($c['naac_grade']) ?>
                  </span>
                <?php endif; ?>
              </div>
              
              <p class="text-xs text-[var(--text-muted)] line-clamp-2 mb-2">
                <?= htmlspecialchars(mb_substr($c['about_description'] ?? 'No description provided.', 0, 150)) ?>
              </p>
            </div>

            <!-- Stats -->
            <div class="flex sm:flex-col items-center sm:items-end gap-4 sm:gap-2 flex-shrink-0">
              <div class="text-right">
                <div class="text-sm font-bold text-white"><?= $feeStr ?></div>
                <div class="text-xs text-[var(--text-muted)]">Avg. Fees</div>
              </div>
              <div class="flex items-center gap-1">
                <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-amber-400"></i>
                <span class="text-sm font-bold text-white"><?= number_format($rating, 1) ?></span>
                <span class="text-xs text-[var(--text-muted)]">(<?= number_format($reviews) ?>)</span>
              </div>
              <div class="hidden sm:flex items-center gap-1 text-indigo-400 text-sm group-hover:gap-2 transition-all mt-2">
                View <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
        <div class="flex justify-center items-center gap-2 mt-12">
          <?php
          $url_params = $_GET;
          unset($url_params['page']);
          $base_url = '?' . http_build_query($url_params) . '&page=';

          if ($page > 1) {
            echo '<a href="'.$base_url.($page-1).'" class="w-10 h-10 flex items-center justify-center rounded-xl glass border border-[var(--border)] text-[var(--text-secondary)] hover:text-white hover:bg-white/5 transition-all"><i data-lucide="chevron-left" class="w-4 h-4"></i></a>';
          }

          $start = max(1, $page - 2);
          $end = min($total_pages, $page + 2);

          if ($start > 1) {
            echo '<a href="'.$base_url.'1" class="w-10 h-10 flex items-center justify-center rounded-xl glass border border-[var(--border)] text-[var(--text-secondary)] hover:text-white hover:bg-white/5 transition-all">1</a>';
            if ($start > 2) echo '<span class="text-[var(--text-muted)]">...</span>';
          }

          for ($i = $start; $i <= $end; $i++) {
            if ($i == $page) {
              echo '<span class="w-10 h-10 flex items-center justify-center rounded-xl bg-indigo-500 text-white font-medium">'.$i.'</span>';
            } else {
              echo '<a href="'.$base_url.$i.'" class="w-10 h-10 flex items-center justify-center rounded-xl glass border border-[var(--border)] text-[var(--text-secondary)] hover:text-white hover:bg-white/5 transition-all">'.$i.'</a>';
            }
          }

          if ($end < $total_pages) {
            if ($end < $total_pages - 1) echo '<span class="text-[var(--text-muted)]">...</span>';
            echo '<a href="'.$base_url.$total_pages.'" class="w-10 h-10 flex items-center justify-center rounded-xl glass border border-[var(--border)] text-[var(--text-secondary)] hover:text-white hover:bg-white/5 transition-all">'.$total_pages.'</a>';
          }

          if ($page < $total_pages) {
            echo '<a href="'.$base_url.($page+1).'" class="w-10 h-10 flex items-center justify-center rounded-xl glass border border-[var(--border)] text-[var(--text-secondary)] hover:text-white hover:bg-white/5 transition-all"><i data-lucide="chevron-right" class="w-4 h-4"></i></a>';
          }
          ?>
        </div>
      <?php endif; ?>

    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
  lucide.createIcons();
</script>
</body>
</html>
