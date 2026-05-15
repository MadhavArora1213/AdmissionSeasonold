<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | AdmissionSeason</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .float { animation: float 3s ease-in-out infinite; }
        @keyframes pulse-glow {
            0%,100% { box-shadow: 0 0 30px rgba(99,102,241,0.2); }
            50%      { box-shadow: 0 0 60px rgba(99,102,241,0.4); }
        }
        .glow { animation: pulse-glow 2s ease-in-out infinite; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter'] min-h-screen flex flex-col">
<?php include 'includes/navbar.php'; ?>

<div class="flex-1 flex items-center justify-center px-4 py-20 relative overflow-hidden">
    <!-- Background glows -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500/10 blur-[140px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/10 blur-[140px] rounded-full pointer-events-none"></div>

    <div class="text-center max-w-lg relative z-10">

        <!-- 404 Number -->
        <div class="float mb-6">
            <div class="text-[120px] sm:text-[160px] font-black leading-none select-none
                        bg-gradient-to-br from-indigo-400 via-purple-400 to-pink-400
                        bg-clip-text text-transparent">
                404
            </div>
        </div>

        <!-- Icon -->
        <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20
                    border border-indigo-500/30 flex items-center justify-center mb-6 glow">
            <i data-lucide="map-pin-off" class="w-10 h-10 text-indigo-400"></i>
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-3">Page Not Found</h1>
        <p class="text-[var(--text-secondary)] mb-8 leading-relaxed">
            Oops! The page you're looking for doesn't exist or has been moved.<br>
            Let's get you back on track.
        </p>

        <!-- Quick Links -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-8">
            <?php $links = [
                ['href'=>'index.php',         'icon'=>'home',        'label'=>'Homepage'],
                ['href'=>'colleges.php',       'icon'=>'building-2',  'label'=>'Colleges'],
                ['href'=>'exams.php',          'icon'=>'pen-line',    'label'=>'Exams'],
                ['href'=>'scholarships.php',   'icon'=>'award',       'label'=>'Scholarships'],
                ['href'=>'rankings.php',       'icon'=>'trophy',      'label'=>'Rankings'],
                ['href'=>'ai-counselor.php',   'icon'=>'bot',         'label'=>'AI Counselor'],
            ]; foreach ($links as $l): ?>
            <a href="<?= $l['href'] ?>"
               class="glass rounded-xl border border-[var(--border)] p-4 flex flex-col items-center gap-2
                      hover:border-indigo-500/40 hover:bg-white/[0.04] transition-all group text-center">
                <i data-lucide="<?= $l['icon'] ?>" class="w-5 h-5 text-[var(--text-muted)] group-hover:text-indigo-400 transition-colors"></i>
                <span class="text-xs font-medium text-[var(--text-secondary)] group-hover:text-white transition-colors"><?= $l['label'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Back & Search -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="history.back()"
                class="glass inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-[var(--border)] text-sm font-medium hover:bg-white/5 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Go Back
            </button>
            <a href="index.php"
               class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-medium">
                <i data-lucide="home" class="w-4 h-4"></i> Back to Home
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>lucide.createIcons();</script>
</body>
</html>
