<?php
require_once '../AdmissionSeason/admin/includes/db.php';

$state  = $_GET['state']  ?? '';
$cat    = $_GET['cat']    ?? '';
$search = $_GET['q']      ?? '';

$where  = ["status = 'ACTIVE'"];
$params = [];

if ($state)  { $where[] = 'state_scope = ?';           $params[] = $state; }
if ($cat)    { $where[] = 'target_caste_category = ?'; $params[] = $cat; }
if ($search) { $where[] = 'name LIKE ?';               $params[] = "%$search%"; }

$sql   = 'SELECT * FROM scholarships WHERE ' . implode(' AND ', $where) . ' ORDER BY deadline ASC';
$stmt  = $pdo->prepare($sql);
$stmt->execute($params);
$scholarships = $stmt->fetchAll();

// Hardcoded full list — DB states merged as fallback
$allStates = [
    'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
    'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka',
    'Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram',
    'Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana',
    'Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
    'Andaman & Nicobar Islands','Chandigarh','Dadra & Nagar Haveli','Daman & Diu',
    'Delhi','Jammu & Kashmir','Ladakh','Lakshadweep','Puducherry'
];
$dbStates = $pdo->query("SELECT DISTINCT state_scope FROM scholarships WHERE state_scope IS NOT NULL ORDER BY state_scope")->fetchAll(PDO::FETCH_COLUMN);
$states   = array_unique(array_merge($allStates, $dbStates));
sort($states);

$allCats = ['SC','ST','OBC','EWS','Minority','Girl Students','Sports','Differently Abled','All Categories'];
$dbCats  = $pdo->query("SELECT DISTINCT target_caste_category FROM scholarships WHERE target_caste_category IS NOT NULL ORDER BY target_caste_category")->fetchAll(PDO::FETCH_COLUMN);
$cats    = array_unique(array_merge($allCats, $dbCats));
sort($cats);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Finder 2026 | AdmissionSeason</title>
    <meta name="description" content="Find government and private scholarships for Indian students. Filter by state, category, and amount.">
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

<div class="min-h-screen pt-16">
    <!-- Hero -->
    <div class="bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] py-14 relative overflow-hidden">
        <div class="absolute top-0 left-1/3 w-96 h-96 bg-emerald-500/10 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                    <i data-lucide="award" class="w-5 h-5 text-emerald-400"></i>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">Scholarship Finder</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Find Scholarships for Your Studies</h1>
            <p class="text-[var(--text-secondary)] max-w-2xl mb-8">Government, private, and institutional scholarships — all in one place. Don't let money stop your dreams.</p>

            <form method="GET" action="scholarships.php" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search scholarships..." class="w-full pl-11 pr-4 py-3.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
                </div>
                <select name="state" onchange="this.form.submit()" class="sm:w-44 appearance-none px-4 py-3.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white outline-none focus:border-indigo-500/60 cursor-pointer">
                    <option value="">All States</option>
                    <?php foreach ($states as $s): ?>
                        <option value="<?= htmlspecialchars($s) ?>" <?= $state===$s?'selected':'' ?>><?= htmlspecialchars($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="cat" onchange="this.form.submit()" class="sm:w-44 appearance-none px-4 py-3.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white outline-none focus:border-indigo-500/60 cursor-pointer">
                    <option value="">All Categories</option>
                    <?php foreach ($cats as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>" <?= $cat===$c?'selected':'' ?>><?= htmlspecialchars($c) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl">Search</button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <?php if (empty($scholarships)): ?>
            <div class="text-center py-20 border border-dashed border-[var(--border)] rounded-3xl">
                <i data-lucide="award" class="w-12 h-12 mx-auto mb-4 text-[var(--text-muted)] opacity-30"></i>
                <h3 class="text-lg font-bold text-white mb-2">No scholarships found</h3>
                <p class="text-sm text-[var(--text-secondary)] mb-6">Try adjusting your filters or check back later.</p>
                <a href="scholarships.php" class="btn-primary inline-flex">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($scholarships as $s):
                    $catColors = ['GOVERNMENT'=>'text-blue-400 bg-blue-500/10 border-blue-500/20','PRIVATE'=>'text-purple-400 bg-purple-500/10 border-purple-500/20','INSTITUTIONAL'=>'text-teal-400 bg-teal-500/10 border-teal-500/20','INTERNATIONAL'=>'text-amber-400 bg-amber-500/10 border-amber-500/20'];
                    $cc = $catColors[$s['category']] ?? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20';
                    $isExpiring = $s['deadline'] && strtotime($s['deadline']) < strtotime('+7 days');
                ?>
                <div class="glass rounded-2xl border border-[var(--border)] p-6 flex flex-col card-hover group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/0 to-teal-500/0 group-hover:from-emerald-500/5 group-hover:to-teal-500/5 transition-all duration-500 pointer-events-none"></div>
                    <div class="relative z-10 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full border <?= $cc ?>"><?= htmlspecialchars($s['category']) ?></span>
                            <?php if ($isExpiring && $s['deadline']): ?>
                                <span class="px-2.5 py-1 text-[10px] font-bold text-red-400 bg-red-500/10 border border-red-500/20 rounded-full animate-pulse">Closing Soon</span>
                            <?php endif; ?>
                        </div>

                        <h2 class="text-base font-bold text-white group-hover:text-emerald-400 transition-colors mb-1"><?= htmlspecialchars($s['name']) ?></h2>
                        <p class="text-xs text-[var(--text-secondary)] mb-4"><?= htmlspecialchars($s['provider_name']) ?></p>

                        <div class="space-y-2 text-sm mt-auto">
                            <?php if ($s['amount_inr']): ?>
                            <div class="flex items-center gap-2">
                                <i data-lucide="indian-rupee" class="w-4 h-4 text-emerald-400"></i>
                                <span class="font-bold text-emerald-400">₹<?= number_format($s['amount_inr']) ?></span>
                            </div>
                            <?php elseif ($s['amount_description']): ?>
                            <div class="flex items-center gap-2">
                                <i data-lucide="gift" class="w-4 h-4 text-emerald-400"></i>
                                <span class="text-[var(--text-secondary)]"><?= htmlspecialchars($s['amount_description']) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($s['deadline']): ?>
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-<?= $isExpiring ? 'red' : 'amber' ?>-400"></i>
                                <span class="text-<?= $isExpiring ? 'red' : '[var(--text-secondary)]' ?>-400">
                                    Deadline: <?= date('d M Y', strtotime($s['deadline'])) ?>
                                </span>
                            </div>
                            <?php endif; ?>

                            <?php if ($s['income_limit']): ?>
                            <div class="flex items-center gap-2">
                                <i data-lucide="users" class="w-4 h-4 text-indigo-400"></i>
                                <span class="text-[var(--text-secondary)]">Income &lt; ₹<?= number_format($s['income_limit']) ?>/yr</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($s['application_link']): ?>
                        <a href="<?= htmlspecialchars($s['application_link']) ?>" target="_blank" rel="noopener noreferrer"
                           class="mt-5 flex items-center justify-center gap-2 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 text-emerald-400 rounded-xl text-sm font-medium transition-all">
                            Apply Now <i data-lucide="external-link" class="w-4 h-4"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>lucide.createIcons();</script>
</body>
</html>
