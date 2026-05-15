<?php
require_once '../AdmissionSeason/admin/includes/db.php';

$q      = trim($_GET['q'] ?? '');
$state  = $_GET['state'] ?? '';
$type   = $_GET['type']  ?? '';

$where  = ['1=1'];
$params = [];

if ($q)     { $where[] = '(name LIKE ? OR city LIKE ? OR state LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($state) { $where[] = 'state = ?'; $params[] = $state; }
if ($type)  { $where[] = 'type = ?';  $params[] = strtoupper($type); }

$stmt = $pdo->prepare('SELECT id, name, city, state, type, nirf_rank, naac_grade, is_verified FROM colleges WHERE ' . implode(' AND ', $where) . ' ORDER BY (nirf_rank IS NULL), nirf_rank ASC, name ASC LIMIT 30');
$stmt->execute($params);
$results = $stmt->fetchAll();

$states = $pdo->query("SELECT DISTINCT state FROM colleges ORDER BY state")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Colleges in India | AdmissionSeason</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-20 max-w-4xl mx-auto px-4 sm:px-6 py-10">
    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-8">Search Colleges</h1>

    <form method="GET" action="search.php" class="flex flex-col sm:flex-row gap-3 mb-8">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="College name, city or state..."
                autofocus
                class="w-full pl-11 pr-4 py-3.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
        </div>
        <select name="type" onchange="this.form.submit()" class="sm:w-40 appearance-none px-4 py-3.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white outline-none cursor-pointer">
            <option value="">All Types</option>
            <option value="GOVERNMENT" <?= $type==='GOVERNMENT'?'selected':'' ?>>Government</option>
            <option value="PRIVATE"    <?= $type==='PRIVATE'?'selected':'' ?>>Private</option>
            <option value="DEEMED"     <?= $type==='DEEMED'?'selected':'' ?>>Deemed</option>
        </select>
        <button type="submit" class="btn-primary px-6 rounded-xl">Search</button>
    </form>

    <!-- Results Section -->
    <div class="space-y-6">
        <?php if ($q || $state || $type): ?>
            <div class="flex items-center justify-between">
                <p class="text-sm text-[var(--text-muted)]">
                    Found <span class="text-white font-bold"><?= count($results) ?></span> 
                    <?= count($results) === 1 ? 'college' : 'colleges' ?> 
                    <?php if ($q): ?> for "<span class="text-white"><?= htmlspecialchars($q) ?></span>"<?php endif; ?>
                </p>
                <a href="search.php" class="text-xs text-indigo-400 hover:underline">Clear all</a>
            </div>
        <?php else: ?>
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <i data-lucide="award" class="w-5 h-5 text-amber-400"></i>
                Recommended Colleges
            </h2>
        <?php endif; ?>

        <?php if (empty($results)): ?>
            <div class="text-center py-20 border border-dashed border-[var(--border)] rounded-3xl bg-white/[0.01]">
                <div class="w-16 h-16 mx-auto bg-[var(--bg-secondary)] rounded-full flex items-center justify-center mb-4 border border-[var(--border)]">
                    <i data-lucide="search-x" class="w-8 h-8 text-[var(--text-muted)]"></i>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">No colleges found</h3>
                <p class="text-sm text-[var(--text-secondary)] mb-6 max-w-sm mx-auto">We couldn't find any colleges matching your criteria. Try adjusting your filters or search terms.</p>
                <a href="search.php" class="btn-primary inline-flex">View All Colleges</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-4">
                <?php foreach ($results as $c): ?>
                <a href="college.php?id=<?= $c['id'] ?>" class="glass flex flex-col sm:flex-row sm:items-center gap-5 p-6 rounded-2xl border border-[var(--border)] hover:border-indigo-500/40 transition-all group relative overflow-hidden">
                    <!-- Hover Effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/0 to-purple-500/0 group-hover:from-indigo-500/5 group-hover:to-purple-500/5 transition-all duration-500 pointer-events-none"></div>
                    
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center text-3xl flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        🎓
                    </div>
                    
                    <div class="flex-1 min-w-0 relative z-10">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold text-white text-lg group-hover:text-indigo-400 transition-colors truncate">
                                <?= htmlspecialchars($c['name']) ?>
                            </h3>
                            <?php if ($c['is_verified']): ?>
                                <i data-lucide="check-circle" class="w-4 h-4 text-indigo-400" title="Verified College"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-[var(--text-muted)]">
                            <span class="flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                <?= htmlspecialchars($c['city'] . ', ' . $c['state']) ?>
                            </span>
                            <span class="w-1 h-1 bg-[var(--border)] rounded-full"></span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="building" class="w-3 h-3"></i>
                                <?= htmlspecialchars($c['type']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 flex-shrink-0 relative z-10">
                        <div class="text-right hidden sm:block">
                            <?php if ($c['nirf_rank']): ?>
                                <div class="text-xs font-bold text-amber-400">NIRF #<?= $c['nirf_rank'] ?></div>
                            <?php endif; ?>
                            <?php if ($c['naac_grade']): ?>
                                <div class="text-[10px] text-emerald-400 font-medium">NAAC <?= $c['naac_grade'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-indigo-500/20 group-hover:text-indigo-400 transition-all duration-300">
                            <i data-lucide="arrow-right" class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1"></i>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>lucide.createIcons();</script>
</body>
</html>
