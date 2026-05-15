<?php
require_once '../AdmissionSeason/admin/includes/db.php';

// Fetch top ranked colleges from DB
$stmt = $pdo->query("SELECT id, name, city, state, type, nirf_rank, naac_grade, is_verified, total_students FROM colleges WHERE nirf_rank IS NOT NULL ORDER BY nirf_rank ASC LIMIT 100");
$colleges = $stmt->fetchAll();

// Fallback: hardcoded NIRF 2024 top colleges if DB is empty
if (empty($colleges)) {
    $colleges = [
        ['id'=>'','name'=>'IIT Madras','city'=>'Chennai','state'=>'Tamil Nadu','type'=>'CENTRAL','nirf_rank'=>1,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>8000],
        ['id'=>'','name'=>'IIT Bombay','city'=>'Mumbai','state'=>'Maharashtra','type'=>'CENTRAL','nirf_rank'=>2,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>10000],
        ['id'=>'','name'=>'IIT Delhi','city'=>'New Delhi','state'=>'Delhi','type'=>'CENTRAL','nirf_rank'=>3,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>8500],
        ['id'=>'','name'=>'IIT Kharagpur','city'=>'Kharagpur','state'=>'West Bengal','type'=>'CENTRAL','nirf_rank'=>4,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>12000],
        ['id'=>'','name'=>'IIT Roorkee','city'=>'Roorkee','state'=>'Uttarakhand','type'=>'CENTRAL','nirf_rank'=>5,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>9500],
        ['id'=>'','name'=>'IIT Kanpur','city'=>'Kanpur','state'=>'Uttar Pradesh','type'=>'CENTRAL','nirf_rank'=>6,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>7500],
        ['id'=>'','name'=>'IIT Guwahati','city'=>'Guwahati','state'=>'Assam','type'=>'CENTRAL','nirf_rank'=>7,'naac_grade'=>'A+','is_verified'=>1,'total_students'=>5500],
        ['id'=>'','name'=>'AIIMS New Delhi','city'=>'New Delhi','state'=>'Delhi','type'=>'CENTRAL','nirf_rank'=>8,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>3500],
        ['id'=>'','name'=>'Jadavpur University','city'=>'Kolkata','state'=>'West Bengal','type'=>'GOVERNMENT','nirf_rank'=>9,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>22000],
        ['id'=>'','name'=>'IIT Hyderabad','city'=>'Hyderabad','state'=>'Telangana','type'=>'CENTRAL','nirf_rank'=>10,'naac_grade'=>'A+','is_verified'=>1,'total_students'=>4000],
        ['id'=>'','name'=>'NIT Trichy','city'=>'Tiruchirappalli','state'=>'Tamil Nadu','type'=>'GOVERNMENT','nirf_rank'=>11,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>8500],
        ['id'=>'','name'=>'BITS Pilani','city'=>'Pilani','state'=>'Rajasthan','type'=>'DEEMED','nirf_rank'=>17,'naac_grade'=>'A+','is_verified'=>1,'total_students'=>15000],
        ['id'=>'','name'=>'Delhi University','city'=>'New Delhi','state'=>'Delhi','type'=>'CENTRAL','nirf_rank'=>21,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>300000],
        ['id'=>'','name'=>'Manipal Academy of Higher Education','city'=>'Manipal','state'=>'Karnataka','type'=>'DEEMED','nirf_rank'=>35,'naac_grade'=>'A+','is_verified'=>1,'total_students'=>28000],
        ['id'=>'','name'=>'VIT Vellore','city'=>'Vellore','state'=>'Tamil Nadu','type'=>'DEEMED','nirf_rank'=>38,'naac_grade'=>'A+','is_verified'=>1,'total_students'=>50000],
        ['id'=>'','name'=>'Amity University Noida','city'=>'Noida','state'=>'Uttar Pradesh','type'=>'PRIVATE','nirf_rank'=>72,'naac_grade'=>'A+','is_verified'=>1,'total_students'=>125000],
        ['id'=>'','name'=>'SRM Institute of Science and Technology','city'=>'Chennai','state'=>'Tamil Nadu','type'=>'DEEMED','nirf_rank'=>46,'naac_grade'=>'A++','is_verified'=>1,'total_students'=>52000],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Rankings 2026 — NIRF, NAAC | AdmissionSeason</title>
    <meta name="description" content="Explore top-ranked colleges in India by NIRF, NAAC, and other accreditations. Find the best engineering, medical, and management colleges.">
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
        <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500/10 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                    <i data-lucide="trophy" class="w-5 h-5 text-amber-400"></i>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-amber-400">NIRF Rankings 2026</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Top College Rankings in India</h1>
            <p class="text-[var(--text-secondary)] max-w-2xl">Comprehensive rankings based on NIRF, NAAC, QS, and AdmissionSeason's own quality score across 30,000+ institutions.</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <?php if (empty($colleges)): ?>
            <div class="text-center py-20 border border-dashed border-[var(--border)] rounded-3xl">
                <i data-lucide="bar-chart-3" class="w-12 h-12 mx-auto mb-4 text-[var(--text-muted)] opacity-30"></i>
                <h3 class="text-lg font-bold text-white mb-2">Rankings Coming Soon</h3>
                <p class="text-sm text-[var(--text-secondary)]">We're compiling verified NIRF data. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="glass rounded-2xl border border-[var(--border)] overflow-hidden">
                <div class="px-6 py-4 border-b border-[var(--border)] flex items-center justify-between">
                    <h2 class="font-bold text-white">Top <?= count($colleges) ?> Colleges by NIRF Rank</h2>
                    <span class="text-xs text-[var(--text-muted)]">Updated 2026</span>
                </div>
                <div class="divide-y divide-[var(--border)]">
                    <?php foreach ($colleges as $i => $c): ?>
                    <a href="college.php?id=<?= $c['id'] ?>" class="flex items-center gap-4 px-6 py-4 hover:bg-white/[0.02] transition-colors group">
                        <!-- Rank -->
                        <div class="w-12 text-center flex-shrink-0">
                            <?php if ($i < 3): ?>
                                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center font-bold text-sm shadow-md
                                    <?= $i===0 ? 'bg-amber-500 text-white shadow-amber-500/30' : ($i===1 ? 'bg-slate-400 text-white shadow-slate-400/30' : 'bg-orange-600 text-white shadow-orange-600/30') ?>">
                                    #<?= $c['nirf_rank'] ?>
                                </div>
                            <?php else: ?>
                                <span class="text-lg font-bold text-[var(--text-muted)]">#<?= $c['nirf_rank'] ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- College -->
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-white group-hover:text-indigo-400 transition-colors truncate"><?= htmlspecialchars($c['name']) ?></div>
                            <div class="text-xs text-[var(--text-muted)] flex items-center gap-2 mt-1">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                <?= htmlspecialchars($c['city'] . ', ' . $c['state']) ?>
                                <span class="text-[var(--border)]">•</span>
                                <?= htmlspecialchars($c['type']) ?>
                            </div>
                        </div>

                        <!-- NAAC -->
                        <?php if ($c['naac_grade']): ?>
                        <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-xs font-bold text-emerald-400">
                            NAAC <?= $c['naac_grade'] ?>
                        </div>
                        <?php endif; ?>

                        <!-- Arrow -->
                        <i data-lucide="chevron-right" class="w-4 h-4 text-[var(--text-muted)] group-hover:text-indigo-400 group-hover:translate-x-1 transition-all flex-shrink-0"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>lucide.createIcons();</script>
</body>
</html>
