<?php
require_once '../AdmissionSeason/admin/includes/db.php';

// Fetch live stats
$college_count = $pdo->query("SELECT COUNT(*) FROM colleges")->fetchColumn();
$exam_count = $pdo->query("SELECT COUNT(*) FROM exams")->fetchColumn();

// Fetch featured colleges
$featured = $pdo->query("SELECT id, name, city, state, type, nirf_rank, naac_grade, about_description FROM colleges WHERE is_verified = 1 ORDER BY nirf_rank ASC LIMIT 3")->fetchAll();

$page_title = "AdmissionSeason — India's Smartest College Discovery Platform";
$page_desc = "Discover 30,000+ colleges, get AI-powered personalized recommendations, compare fees, placements, reviews, and apply directly.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="<?= $page_desc ?>">
    <meta name="keywords" content="college admission india, best colleges, JEE counselling, NEET colleges, MBA admission">
    <meta property="og:title" content="<?= $page_title ?>">
    <meta property="og:description" content="<?= $page_desc ?>">
    <meta property="og:type" content="website">
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

<!-- HeroSection matching Next.js Component -->
<div class="relative min-h-screen overflow-hidden grid-pattern">
  <!-- Background Orbs -->
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-20 left-1/4 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl animate-pulse-slow"></div>
    <div class="absolute top-40 right-1/4 w-80 h-80 bg-purple-600/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s"></div>
    <div class="absolute bottom-20 left-1/3 w-72 h-72 bg-teal-600/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 4s"></div>
  </div>

  <!-- Hero Content -->
  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 pt-32 pb-16">
    <!-- Badge -->
    <div class="flex justify-center mb-6">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 glass rounded-full border border-indigo-500/30 text-sm text-indigo-300">
        <i data-lucide="sparkles" class="w-3.5 h-3.5 animate-pulse"></i>
        AI-Powered College Discovery Platform
        <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span>
      </div>
    </div>

    <!-- Headline -->
    <div class="text-center mb-8">
      <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight mb-6 leading-tight">
        Find Your Perfect <span class="gradient-text glow-text">College</span><br />
        <span class="text-[var(--text-secondary)]">with AI Guidance</span>
      </h1>
      <p class="text-xl text-[var(--text-secondary)] max-w-2xl mx-auto leading-relaxed">
        Explore <span class="text-white font-semibold"><?= number_format(max($college_count, 30000)) ?>+ colleges</span> across India. Compare fees, placements, and reviews. Get AI-powered recommendations tailored just for you.
      </p>
    </div>

    <!-- Search Bar -->
    <div class="max-w-3xl mx-auto mb-6 relative">
      <form action="/frontend/search.php" method="GET" id="searchForm">
        <div id="searchWrapper" class="relative flex items-center glass rounded-2xl border border-[var(--border)] transition-all duration-300">
          <i data-lucide="search" class="absolute left-4 w-5 h-5 text-[var(--text-muted)]"></i>
          <input
            type="text"
            name="q"
            id="searchInput"
            placeholder="Search colleges, exams, courses..."
            autocomplete="off"
            class="w-full bg-transparent pl-12 pr-40 py-4 text-base text-white placeholder-[var(--text-muted)] outline-none"
          />
          <button type="submit" class="absolute right-2 btn-primary text-sm flex items-center gap-2">
            <i data-lucide="search" class="w-4 h-4"></i> Search
          </button>
        </div>

        <!-- Autocomplete Suggestions -->
        <div id="searchSuggestions" class="hidden absolute top-full left-0 right-0 mt-2 glass rounded-xl border border-[var(--border)] overflow-hidden z-10 shadow-xl">
          <div class="px-4 py-2 text-xs text-[var(--text-muted)] border-b border-[var(--border)]">
            Popular searches
          </div>
          <?php
          $popular = ["IIT Delhi", "IIM Ahmedabad", "BITS Pilani", "VIT Vellore", "Engineering Colleges in Delhi", "Medical Colleges under 5 Lakhs"];
          foreach ($popular as $s):
          ?>
            <button type="button" onclick="window.location.href='/frontend/search.php?q=<?= urlencode($s) ?>'" class="w-full text-left px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-white hover:bg-indigo-500/10 flex items-center gap-3 transition-all">
              <i data-lucide="trending-up" class="w-3.5 h-3.5 text-indigo-400"></i>
              <?= $s ?>
            </button>
          <?php endforeach; ?>
        </div>
      </form>
    </div>

    <!-- Quick Filters -->
    <div class="flex flex-wrap justify-center gap-2 mb-16">
      <?php
      $quick_filters = [
        ["label" => "Engineering", "icon" => "⚙️", "href" => "/frontend/colleges.php?type=engineering"],
        ["label" => "Medical", "icon" => "🩺", "href" => "/frontend/colleges.php?type=medical"],
        ["label" => "Management", "icon" => "💼", "href" => "/frontend/colleges.php?type=management"],
        ["label" => "Law", "icon" => "⚖️", "href" => "/frontend/colleges.php?type=law"],
        ["label" => "Design", "icon" => "🎨", "href" => "/frontend/colleges.php?type=design"],
        ["label" => "Study Abroad", "icon" => "✈️", "href" => "/frontend/study-abroad.php"],
      ];
      foreach ($quick_filters as $f):
      ?>
        <a href="<?= $f['href'] ?>" class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--text-secondary)] glass rounded-full border border-[var(--border)] hover:border-indigo-500/50 hover:text-white hover:-translate-y-0.5 transition-all">
          <span><?= $f['icon'] ?></span> <?= $f['label'] ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-20">
      <?php
      $stats = [
        ["value" => number_format(max($college_count, 30000)) . "+", "label" => "Colleges Listed", "icon" => "book-open", "color" => "from-indigo-500 to-purple-600"],
        ["value" => number_format(max($exam_count, 350)) . "+", "label" => "Entrance Exams", "icon" => "award", "color" => "from-teal-500 to-cyan-600"],
        ["value" => "2.4M+", "label" => "Student Community", "icon" => "users", "color" => "from-pink-500 to-rose-600"],
        ["value" => "98%", "label" => "Placement Accuracy", "icon" => "trending-up", "color" => "from-amber-500 to-orange-600"],
      ];
      foreach ($stats as $stat):
      ?>
        <div class="glass rounded-2xl p-5 border border-[var(--border)] card-hover text-center">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br <?= $stat['color'] ?> flex items-center justify-center mx-auto mb-3">
            <i data-lucide="<?= $stat['icon'] ?>" class="w-5 h-5 text-white"></i>
          </div>
          <div class="text-2xl font-bold text-white mb-1"><?= $stat['value'] ?></div>
          <div class="text-xs text-[var(--text-secondary)]"><?= $stat['label'] ?></div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Featured Colleges -->
    <div class="mb-20">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h2 class="text-2xl font-bold text-white">🏆 Top Colleges</h2>
          <p class="text-sm text-[var(--text-secondary)] mt-1">NIRF-ranked institutions with verified data</p>
        </div>
        <a href="colleges.php" class="flex items-center gap-1 text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
          View All <i data-lucide="chevron-right" class="w-4 h-4"></i>
        </a>
      </div>

      <div class="grid md:grid-cols-3 gap-5">
        <?php
        $featured_mocks = [
          [
            "name" => "IIT Delhi", "location" => "New Delhi", "rating" => 4.8, "reviews" => 2841, "fee" => "₹2.2L/yr", "tags" => ["Engineering", "Research"],
            "badge" => "NIRF #2", "badgeColor" => "from-amber-500 to-orange-500", "gradient" => "from-indigo-900/40 to-purple-900/40", "url" => "/frontend/college.php?name=iit-delhi"
          ],
          [
            "name" => "IIM Ahmedabad", "location" => "Ahmedabad", "rating" => 4.9, "reviews" => 1924, "fee" => "₹23L/yr", "tags" => ["Management", "MBA"],
            "badge" => "NIRF #1", "badgeColor" => "from-emerald-500 to-teal-500", "gradient" => "from-emerald-900/40 to-teal-900/40", "url" => "/frontend/college.php?name=iim-ahmedabad"
          ],
          [
            "name" => "BITS Pilani", "location" => "Pilani, Rajasthan", "rating" => 4.7, "reviews" => 3102, "fee" => "₹5.3L/yr", "tags" => ["Engineering", "Sciences"],
            "badge" => "Top Private", "badgeColor" => "from-purple-500 to-pink-500", "gradient" => "from-purple-900/40 to-pink-900/40", "url" => "/frontend/college.php?name=bits-pilani"
          ],
        ];
        foreach ($featured_mocks as $col):
        ?>
          <a href="<?= $col['url'] ?>" class="relative glass rounded-2xl border border-[var(--border)] overflow-hidden card-hover group p-5 bg-gradient-to-br <?= $col['gradient'] ?>">
            <div class="absolute top-4 right-4 px-2.5 py-1 text-xs font-bold text-white rounded-full bg-gradient-to-r <?= $col['badgeColor'] ?>">
              <?= $col['badge'] ?>
            </div>
            <div class="mb-4">
              <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-2xl mb-3">🎓</div>
              <h3 class="text-lg font-bold text-white group-hover:text-indigo-300 transition-colors"><?= $col['name'] ?></h3>
              <div class="flex items-center gap-1 text-sm text-[var(--text-secondary)] mt-1">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i> <?= $col['location'] ?>
              </div>
            </div>
            <div class="flex items-center gap-3 mb-4">
              <div class="flex items-center gap-1">
                <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-amber-400"></i>
                <span class="text-sm font-semibold text-white"><?= $col['rating'] ?></span>
                <span class="text-xs text-[var(--text-muted)]">(<?= number_format($col['reviews']) ?>)</span>
              </div>
              <span class="text-xs text-[var(--text-muted)]">•</span>
              <span class="text-sm text-[var(--text-secondary)]"><?= $col['fee'] ?></span>
            </div>
            <div class="flex flex-wrap gap-1.5 mb-4">
              <?php foreach ($col['tags'] as $tag): ?>
                <span class="px-2 py-0.5 text-xs rounded-full glass border border-[var(--border)] text-[var(--text-secondary)]"><?= $tag ?></span>
              <?php endforeach; ?>
            </div>
            <div class="flex items-center gap-1 text-sm text-indigo-400 group-hover:gap-2 transition-all">
              View Profile <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Features -->
    <div class="mb-20">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-white mb-3">
          Why <span class="gradient-text">AdmissionSeason</span>?
        </h2>
        <p class="text-[var(--text-secondary)]">
          Everything you need for smarter college admissions decisions
        </p>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <?php
        $features = [
          [ "icon" => "sparkles", "title" => "AI College Counselor", "desc" => "Get hyper-personalized college recommendations based on your marks, budget, and career goals — powered by Llama 3.1.", "color" => "text-indigo-400", "bg" => "from-indigo-500/10 to-purple-500/10" ],
          [ "icon" => "bar-chart-3", "title" => "Rank Predictor", "desc" => "Know your admission chances across 1000+ programs instantly based on your JEE/NEET/CAT score and category.", "color" => "text-teal-400", "bg" => "from-teal-500/10 to-cyan-500/10" ],
          [ "icon" => "shield", "title" => "Verified Reviews", "desc" => "Authentic reviews from verified alumni and current students. No fake ratings. Real experiences only.", "color" => "text-emerald-400", "bg" => "from-emerald-500/10 to-green-500/10" ],
          [ "icon" => "zap", "title" => "Direct Application", "desc" => "Apply to colleges directly through AdmissionSeason. Track status, upload documents, and pay fees — all in one place.", "color" => "text-amber-400", "bg" => "from-amber-500/10 to-orange-500/10" ],
        ];
        foreach ($features as $f):
        ?>
          <div class="glass rounded-2xl p-6 border border-[var(--border)] card-hover bg-gradient-to-br <?= $f['bg'] ?>">
            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center mb-4 <?= $f['color'] ?>">
              <i data-lucide="<?= $f['icon'] ?>" class="w-5 h-5"></i>
            </div>
            <h3 class="text-base font-bold text-white mb-2"><?= $f['title'] ?></h3>
            <p class="text-sm text-[var(--text-secondary)] leading-relaxed"><?= $f['desc'] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- CTA Banner -->
    <div class="relative glass rounded-3xl border border-indigo-500/30 overflow-hidden p-8 sm:p-12 text-center glow-purple">
      <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/10 to-purple-600/10"></div>
      <div class="relative">
        <div class="text-4xl mb-4">🤖</div>
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">
          Not Sure Which College to Pick?
        </h2>
        <p class="text-[var(--text-secondary)] mb-6 max-w-xl mx-auto">
          Chat with our AI Counselor. Enter your marks, budget, and preferences — get a personalized shortlist in seconds.
        </p>
        <a href="ai-counselor.php" class="inline-flex items-center gap-2 btn-primary text-base">
          <i data-lucide="sparkles" class="w-4 h-4"></i>
          Try AI Counselor — Free
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
  // Search suggestions logic
  const searchInput = document.getElementById('searchInput');
  const searchWrapper = document.getElementById('searchWrapper');
  const searchSuggestions = document.getElementById('searchSuggestions');

  searchInput.addEventListener('focus', () => {
    searchWrapper.classList.add('border-indigo-500/70', 'shadow-lg', 'shadow-indigo-500/20');
    if (searchInput.value.length === 0) {
      searchSuggestions.classList.remove('hidden');
    }
  });

  searchInput.addEventListener('blur', () => {
    searchWrapper.classList.remove('border-indigo-500/70', 'shadow-lg', 'shadow-indigo-500/20');
    setTimeout(() => {
      searchSuggestions.classList.add('hidden');
    }, 200);
  });

  searchInput.addEventListener('input', () => {
    if (searchInput.value.length === 0) {
      searchSuggestions.classList.remove('hidden');
    } else {
      searchSuggestions.classList.add('hidden');
    }
  });

  // Initialize Lucide icons
  lucide.createIcons();
</script>
</body>
</html>
