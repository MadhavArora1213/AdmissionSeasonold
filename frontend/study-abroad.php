<?php
$page_title = "Study Abroad 2026 | AdmissionSeason";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Explore top universities abroad for Indian students. Get guidance on applications, scholarships, visa, and more.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-slate-900 font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">
    <!-- Hero -->
    <div class="bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] py-20 relative overflow-hidden text-center">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/5 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-500/5 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="max-w-3xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="text-5xl mb-4">✈️</div>
            <h1 class="text-3xl sm:text-5xl font-bold text-slate-900 mb-4">Study Abroad</h1>
            <p class="text-[var(--text-secondary)] text-lg mb-8 max-w-xl mx-auto">Explore world-class universities in the USA, UK, Canada, Australia & Germany. Let our AI Counselor help you choose the right one.</p>
            <a href="ai-counselor.php" class="inline-flex items-center gap-2 btn-primary text-base px-8 py-3.5 rounded-xl">
                <i data-lucide="bot" class="w-5 h-5"></i> Get AI Guidance
            </a>
        </div>
    </div>

    <!-- Countries Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
        <h2 class="text-2xl font-bold text-slate-900 mb-8 text-center">Popular Destinations</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5">
            <?php
            $countries = [
                ['flag'=>'🇺🇸','name'=>'USA','unis'=>'4,000+ Universities','color'=>'from-blue-500/10 to-red-500/10','border'=>'border-blue-500/20'],
                ['flag'=>'🇬🇧','name'=>'UK','unis'=>'160+ Universities','color'=>'from-blue-500/10 to-indigo-500/10','border'=>'border-indigo-500/20'],
                ['flag'=>'🇨🇦','name'=>'Canada','unis'=>'100+ Universities','color'=>'from-red-500/10 to-orange-500/10','border'=>'border-red-500/20'],
                ['flag'=>'🇦🇺','name'=>'Australia','unis'=>'43 Universities','color'=>'from-yellow-500/10 to-amber-500/10','border'=>'border-amber-500/20'],
                ['flag'=>'🇩🇪','name'=>'Germany','unis'=>'400+ Universities','color'=>'from-slate-500/10 to-zinc-500/10','border'=>'border-slate-500/20'],
            ];
            foreach ($countries as $c): ?>
            <div class="glass rounded-2xl border <?= $c['border'] ?> p-6 text-center hover:scale-105 transition-transform cursor-pointer bg-gradient-to-br <?= $c['color'] ?>">
                <div class="text-5xl mb-3"><?= $c['flag'] ?></div>
                <h3 class="font-bold text-slate-900 text-lg"><?= $c['name'] ?></h3>
                <p class="text-xs text-[var(--text-muted)] mt-1"><?= $c['unis'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Steps -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 pb-16">
        <h2 class="text-2xl font-bold text-slate-900 mb-8 text-center">Your Journey Abroad</h2>
        <div class="space-y-4">
            <?php
            $steps = [
                ['icon'=>'target','num'=>'01','title'=>'Choose Your Course & Country','desc'=>'Use our AI Counselor to shortlist the best universities based on your profile, budget, and career goals.','color'=>'text-indigo-400'],
                ['icon'=>'file-text','num'=>'02','title'=>'Prepare Your Application','desc'=>'Get help with SOP, LOR, essays, and document preparation. Track deadlines in your dashboard.','color'=>'text-purple-400'],
                ['icon'=>'award','num'=>'03','title'=>'Apply for Scholarships','desc'=>'Discover merit & need-based scholarships worth crores. We\'ll match you with the ones you\'re eligible for.','color'=>'text-amber-400'],
                ['icon'=>'plane','num'=>'04','title'=>'Visa & Pre-Departure','desc'=>'Student visa guidance, travel prep, accommodation search, and pre-departure orientation.','color'=>'text-emerald-400'],
            ];
            foreach ($steps as $s): ?>
            <div class="glass rounded-2xl border border-[var(--border)] p-6 flex items-start gap-5 hover:border-indigo-500/30 transition-colors">
                <div class="w-12 h-12 rounded-xl bg-[var(--bg-secondary)] border border-[var(--border)] flex items-center justify-center flex-shrink-0">
                    <i data-lucide="<?= $s['icon'] ?>" class="w-5 h-5 <?= $s['color'] ?>"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-[var(--text-muted)] tracking-widest uppercase"><?= $s['num'] ?></span>
                    <h3 class="font-bold text-slate-900 mt-1 mb-2"><?= $s['title'] ?></h3>
                    <p class="text-sm text-[var(--text-secondary)] leading-relaxed"><?= $s['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12">
            <a href="ai-counselor.php" class="inline-flex items-center gap-2 btn-primary px-8 py-4 rounded-xl text-base font-semibold">
                <i data-lucide="sparkles" class="w-5 h-5"></i> Start Your Study Abroad Journey
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>lucide.createIcons();</script>
</body>
</html>
