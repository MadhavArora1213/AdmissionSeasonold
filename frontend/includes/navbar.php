<?php
// frontend/includes/navbar.php
$current_page = basename($_SERVER['PHP_SELF'], '.php');

$navLinks = [
  [
    "label" => "Colleges",
    "href" => "colleges.php",
    "submenu" => [
      [ "label" => "Engineering", "href" => "colleges.php?type=engineering" ],
      [ "label" => "Medical", "href" => "colleges.php?type=medical" ],
      [ "label" => "Management", "href" => "colleges.php?type=management" ],
      [ "label" => "Law", "href" => "colleges.php?type=law" ],
      [ "label" => "Arts & Science", "href" => "colleges.php?type=arts" ],
    ]
  ],
  [
    "label" => "Exams",
    "href" => "exams.php",
    "submenu" => [
      [ "label" => "JEE Main & Advanced", "href" => "exams.php?slug=jee-main" ],
      [ "label" => "NEET UG", "href" => "exams.php?slug=neet" ],
      [ "label" => "CAT", "href" => "exams.php?slug=cat" ],
      [ "label" => "GATE", "href" => "exams.php?slug=gate" ],
      [ "label" => "All Exams", "href" => "exams.php" ],
    ]
  ],
  [ "label" => "Rankings", "href" => "rankings.php" ],
  [ "label" => "Scholarships", "href" => "scholarships.php" ],
  [ "label" => "Study Abroad", "href" => "study-abroad.php" ],
];
?>

<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 bg-transparent">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between h-16">
      <!-- Logo -->
      <a href="index.php" class="flex items-center gap-2 group">
        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#0B2447] to-[#19376D] flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
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
            <a href="<?= $link['href'] ?>" class="flex items-center gap-1 px-3 py-2 text-sm text-[var(--text-secondary)] hover:text-[var(--accent)] rounded-lg hover:bg-[var(--accent)]/5 transition-all duration-200">
              <?= $link['label'] ?>
              <?php if (isset($link['submenu'])): ?>
                <i data-lucide="chevron-down" class="w-3 h-3"></i>
              <?php endif; ?>
            </a>

            <?php if (isset($link['submenu'])): ?>
              <div class="absolute top-full left-0 mt-2 w-52 glass rounded-xl shadow-xl border border-[var(--border)] overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                <?php foreach ($link['submenu'] as $item): ?>
                  <a href="<?= $item['href'] ?>" class="block px-4 py-2.5 text-sm text-[var(--text-secondary)] hover:text-[var(--accent)] hover:bg-[var(--accent)]/5 transition-all">
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
        <a href="search.php" class="hidden md:flex items-center gap-2 px-3 py-2 text-sm text-[var(--text-secondary)] hover:text-[var(--accent)] glass rounded-xl border border-[var(--border)] transition-all hover:border-[var(--accent)]/50 group">
          <i data-lucide="search" class="w-4 h-4"></i>
          <span class="text-xs">Search colleges...</span>
          <span class="ml-2 text-xs text-[var(--text-muted)] glass px-1.5 py-0.5 rounded border border-[var(--border)]">
            ⌘K
          </span>
        </a>

        <!-- AI Counselor -->
        <a href="ai-counselor.php" class="hidden sm:flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white rounded-xl bg-gradient-to-r from-[#0B2447] to-[#19376D] hover:opacity-90 transition-all shadow-lg shadow-indigo-500/10 hover:-translate-y-0.5">
          <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
          AI Counselor
        </a>

        <!-- Auth -->
        <a href="login.php" class="px-3 py-2 text-sm text-[var(--text-secondary)] hover:text-[var(--accent)] transition-all">
          Login
        </a>

        <!-- Mobile Menu Toggle -->
        <button id="mobileMenuBtn" class="lg:hidden p-2 text-[var(--text-secondary)] hover:text-[var(--accent)] hover:bg-[var(--accent)]/5 rounded-lg transition-all active:scale-95">
          <i data-lucide="menu" class="w-6 h-6" id="menuIconOpen"></i>
          <i data-lucide="x" class="w-6 h-6 hidden" id="menuIconClose"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu Overlay -->
  <div id="mobileOverlay" class="fixed inset-0 bg-black/60 backdrop-blur-md z-40 hidden opacity-0 transition-opacity duration-300"></div>

  <!-- Mobile Menu Side Panel -->
  <div id="mobileMenu" class="fixed top-0 right-0 bottom-0 w-[80%] max-w-sm bg-[var(--bg-secondary)] border-l border-[var(--border)] z-50 translate-x-full transition-transform duration-300 ease-out overflow-y-auto">
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <span class="text-xl font-bold gradient-text">Menu</span>
        <button id="closeMenuBtn" class="p-2 text-[var(--text-secondary)] hover:text-[var(--accent)]">
          <i data-lucide="x" class="w-6 h-6"></i>
        </button>
      </div>
      
      <div class="space-y-2">
        <?php foreach ($navLinks as $link): ?>
          <div class="space-y-1">
            <a href="<?= $link['href'] ?>" class="flex items-center justify-between p-3 text-lg font-medium text-[var(--text-primary)] hover:bg-[var(--accent)]/5 rounded-xl transition-all">
              <?= $link['label'] ?>
              <?php if (isset($link['submenu'])): ?>
                <i data-lucide="chevron-right" class="w-4 h-4 text-[var(--text-muted)]"></i>
              <?php endif; ?>
            </a>
            <?php if (isset($link['submenu'])): ?>
              <div class="pl-4 space-y-1">
                <?php foreach ($link['submenu'] as $item): ?>
                  <a href="<?= $item['href'] ?>" class="block p-2 text-sm text-[var(--text-secondary)] hover:text-[var(--accent)] transition-all italic border-l border-[var(--border)] ml-2 pl-4">
                    <?= $item['label'] ?>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="pt-6 border-t border-[var(--border)] space-y-4">
        <a href="ai-counselor.php" class="flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-gradient-to-r from-[#0B2447] to-[#19376D] text-white font-bold shadow-lg shadow-indigo-500/10">
          <i data-lucide="sparkles" class="w-5 h-5"></i>
          AI Counselor
        </a>
        <div class="grid grid-cols-2 gap-3">
          <a href="login.php" class="flex items-center justify-center py-3 rounded-xl border border-[var(--border)] text-[var(--text-primary)] text-sm font-medium hover:bg-[var(--accent)]/5 transition-all">
            Login
          </a>
          <a href="signup.php" class="flex items-center justify-center py-3 rounded-xl bg-[var(--accent)]/5 text-[var(--text-primary)] text-sm font-medium hover:bg-[var(--accent)]/10 transition-all">
            Register
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>

<script>
  // Scroll Effect
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

  // Mobile Menu Logic
  const mobileBtn = document.getElementById('mobileMenuBtn');
  const closeBtn = document.getElementById('closeMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');
  const mobileOverlay = document.getElementById('mobileOverlay');

  function openNav() {
    mobileMenu.classList.remove('translate-x-full');
    mobileOverlay.classList.remove('hidden');
    setTimeout(() => mobileOverlay.classList.add('opacity-100'), 10);
    document.body.style.overflow = 'hidden';
  }

  function closeNav() {
    mobileMenu.classList.add('translate-x-full');
    mobileOverlay.classList.remove('opacity-100');
    setTimeout(() => mobileOverlay.classList.add('hidden'), 300);
    document.body.style.overflow = 'auto';
  }

  mobileBtn.addEventListener('click', openNav);
  closeBtn.addEventListener('click', closeNav);
  mobileOverlay.addEventListener('click', closeNav);

  document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      window.location.href = 'search.php';
    }
    if (e.key === 'Escape') closeNav();
  });
</script>
