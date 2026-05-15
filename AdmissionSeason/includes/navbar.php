<?php
// includes/navbar.php — Shared Student-Facing Navbar
$current_page = basename($_SERVER['PHP_SELF'], '.php');

$navLinks = [
  [
    "label" => "Colleges",
    "href" => "/AdmissionSeasonold/AdmissionSeason/colleges.php",
    "submenu" => [
      [ "label" => "Engineering", "href" => "/AdmissionSeasonold/AdmissionSeason/colleges.php?type=engineering" ],
      [ "label" => "Medical", "href" => "/AdmissionSeasonold/AdmissionSeason/colleges.php?type=medical" ],
      [ "label" => "Management", "href" => "/AdmissionSeasonold/AdmissionSeason/colleges.php?type=management" ],
      [ "label" => "Law", "href" => "/AdmissionSeasonold/AdmissionSeason/colleges.php?type=law" ],
      [ "label" => "Arts & Science", "href" => "/AdmissionSeasonold/AdmissionSeason/colleges.php?type=arts" ],
    ]
  ],
  [
    "label" => "Exams",
    "href" => "/AdmissionSeasonold/AdmissionSeason/exams.php",
    "submenu" => [
      [ "label" => "JEE Main & Advanced", "href" => "/AdmissionSeasonold/AdmissionSeason/exams.php?slug=jee-main" ],
      [ "label" => "NEET UG", "href" => "/AdmissionSeasonold/AdmissionSeason/exams.php?slug=neet" ],
      [ "label" => "CAT", "href" => "/AdmissionSeasonold/AdmissionSeason/exams.php?slug=cat" ],
      [ "label" => "GATE", "href" => "/AdmissionSeasonold/AdmissionSeason/exams.php?slug=gate" ],
      [ "label" => "All Exams", "href" => "/AdmissionSeasonold/AdmissionSeason/exams.php" ],
    ]
  ],
  [ "label" => "Rankings", "href" => "/AdmissionSeasonold/AdmissionSeason/rankings.php" ],
  [ "label" => "Scholarships", "href" => "/AdmissionSeasonold/AdmissionSeason/scholarships.php" ],
  [ "label" => "Study Abroad", "href" => "/AdmissionSeasonold/AdmissionSeason/study-abroad.php" ],
];
?>

<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 bg-transparent">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between h-16">
      <!-- Logo -->
      <a href="/AdmissionSeasonold/AdmissionSeason/index.php" class="flex items-center gap-2 group">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform">
          <i data-lucide="graduation-cap" class="w-4 h-4 text-white"></i>
        </div>
        <span class="text-lg font-bold gradient-text hidden sm:block">
          AdmissionSeason
        </span>
      </a>

      <!-- Desktop Nav -->
      <div class="hidden lg:flex items-center gap-1">
        <?php foreach ($navLinks as $link): ?>
          <div class="relative group">
            <a href="<?= $link['href'] ?>" class="flex items-center gap-1 px-3 py-2 text-sm text-[var(--text-secondary)] hover:text-white rounded-lg hover:bg-white/5 transition-all duration-200">
              <?= $link['label'] ?>
              <?php if (isset($link['submenu'])): ?>
                <i data-lucide="chevron-down" class="w-3 h-3"></i>
              <?php endif; ?>
            </a>

            <?php if (isset($link['submenu'])): ?>
              <div class="absolute top-full left-0 mt-2 w-52 glass rounded-xl shadow-xl border border-[var(--border)] overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                <?php foreach ($link['submenu'] as $item): ?>
                  <a href="<?= $item['href'] ?>" class="block px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-white hover:bg-indigo-500/10 transition-all">
                    <?= $item['label'] ?>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Right Actions -->
      <div class="flex items-center gap-2">
        <!-- Search -->
        <a href="/AdmissionSeasonold/AdmissionSeason/search.php" class="hidden md:flex items-center gap-2 px-3 py-2 text-sm text-[var(--text-secondary)] hover:text-white glass rounded-xl border border-[var(--border)] transition-all hover:border-indigo-500/50 group">
          <i data-lucide="search" class="w-4 h-4"></i>
          <span class="text-xs">Search colleges...</span>
          <span class="ml-2 text-xs text-[var(--text-muted)] glass px-1.5 py-0.5 rounded border border-[var(--border)]">
            ⌘K
          </span>
        </a>

        <!-- AI Counselor -->
        <a href="/AdmissionSeasonold/AdmissionSeason/ai-counselor.php" class="hidden sm:flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 transition-all shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5">
          <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
          AI Counselor
        </a>

        <!-- Auth -->
        <a href="/AdmissionSeasonold/AdmissionSeason/auth/login.php" class="px-3 py-2 text-sm text-[var(--text-secondary)] hover:text-white transition-all">
          Login
        </a>

        <!-- Mobile Menu Toggle -->
        <button id="mobileMenuBtn" class="lg:hidden p-2 text-[var(--text-secondary)] hover:text-white hover:bg-white/5 rounded-lg transition-all">
          <i data-lucide="menu" class="w-5 h-5" id="menuIcon"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobileMenu" class="hidden lg:hidden glass border-t border-[var(--border)]">
    <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
      <?php foreach ($navLinks as $link): ?>
        <a href="<?= $link['href'] ?>" class="block px-4 py-3 text-sm text-[var(--text-secondary)] hover:text-white hover:bg-white/5 rounded-xl transition-all">
          <?= $link['label'] ?>
        </a>
      <?php endforeach; ?>
      <div class="pt-2 border-t border-[var(--border)] flex gap-2">
        <a href="/AdmissionSeasonold/AdmissionSeason/auth/login.php" class="flex-1 text-center py-2.5 text-sm text-[var(--text-secondary)] glass rounded-xl border border-[var(--border)]">
          Login
        </a>
        <a href="/AdmissionSeasonold/AdmissionSeason/ai-counselor.php" class="flex-1 text-center py-2.5 text-sm font-medium text-white rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600">
          AI Counselor
        </a>
      </div>
    </div>
  </div>
</nav>

<script>
  window.addEventListener('scroll', () => {
    const nav = document.getElementById('navbar');
    if (window.scrollY > 20) {
      nav.classList.remove('bg-transparent');
      nav.classList.add('glass', 'border-b', 'border-[var(--border)]', 'shadow-2xl');
    } else {
      nav.classList.add('bg-transparent');
      nav.classList.remove('glass', 'border-b', 'border-[var(--border)]', 'shadow-2xl');
    }
  });

  const mobileBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  mobileBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    // For Lucide icon swap, we would normally re-render or toggle classes.
    // Assuming lucide.createIcons() runs after this script if dynamic.
  });

  document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      window.location.href = '/AdmissionSeasonold/AdmissionSeason/search.php';
    }
  });
</script>
