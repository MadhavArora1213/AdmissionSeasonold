<?php $page_title = "Blog — Admission Guides, Exam Tips & College News | AdmissionSeason"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Read expert articles on JEE, NEET, CAT preparation, college reviews, scholarship tips, and admission strategies.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
    <div class="absolute top-0 right-1/3 w-96 h-96 bg-purple-500/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center">
          <i data-lucide="book-open" class="w-5 h-5 text-purple-400"></i>
        </div>
        <span class="text-xs font-bold uppercase tracking-widest text-purple-400">AdmissionSeason Blog</span>
      </div>
      <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Admission Guides & Expert Insights</h1>
      <p class="text-[var(--text-secondary)] max-w-2xl mb-8">JEE tips, NEET strategies, scholarship guides, and college reviews — written by toppers and counselors.</p>

      <!-- Search -->
      <div class="relative max-w-lg">
        <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)]"></i>
        <input type="text" id="blogSearch" oninput="filterPosts()" placeholder="Search articles..."
          class="w-full pl-11 pr-4 py-3.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-purple-500/60 transition-all">
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">

    <!-- Categories -->
    <div class="flex flex-wrap gap-2 mb-8" id="catFilters">
      <?php $cats = ['All','JEE','NEET','CAT','Scholarships','Study Abroad','College Reviews','Career Guide'];
      foreach ($cats as $i => $c): ?>
      <button onclick="filterCat('<?= $c ?>')" data-cat="<?= $c ?>"
        class="cat-btn px-4 py-1.5 rounded-full text-xs font-semibold border transition-all <?= $i===0 ? 'bg-purple-500/20 border-purple-500/40 text-purple-400' : 'border-[var(--border)] text-[var(--text-secondary)] hover:border-purple-500/30 hover:text-white' ?>">
        <?= $c ?>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- Blog Posts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="blogGrid">
      <?php
      $posts = [
        ['title'=>'JEE Main 2026 Complete Strategy: Week-by-Week Plan','cat'=>'JEE','date'=>'12 May 2026','read'=>'8 min','emoji'=>'📐','desc'=>'A detailed 12-week preparation roadmap covering Physics, Chemistry, and Mathematics with daily targets and mock test schedule.','tag'=>'bg-blue-500/10 text-blue-400 border-blue-500/20'],
        ['title'=>'NEET 2026: Biology Chapter Weightage & High-Priority Topics','cat'=>'NEET','date'=>'10 May 2026','read'=>'6 min','emoji'=>'🔬','desc'=>'Data-driven analysis of 5 years of NEET papers reveals which chapters to focus on for maximum marks. Spoiler: Genetics is king.','tag'=>'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'],
        ['title'=>'Top 10 Government Scholarships Every SC/ST Student Must Apply For','cat'=>'Scholarships','date'=>'8 May 2026','read'=>'5 min','emoji'=>'🏆','desc'=>'Detailed breakdown of 10 scholarships with deadlines, amounts, and step-by-step application links via NSP portal.','tag'=>'bg-amber-500/10 text-amber-400 border-amber-500/20'],
        ['title'=>'IIT vs NIT: Which One Is Right For You in 2026?','cat'=>'College Reviews','date'=>'5 May 2026','read'=>'10 min','emoji'=>'🎓','desc'=>'Honest comparison of placements, campus life, fees, research opportunities, and alumni networks of IITs and NITs.','tag'=>'bg-purple-500/10 text-purple-400 border-purple-500/20'],
        ['title'=>'CAT 2025 vs GMAT 2025: MBA in India or Abroad?','cat'=>'CAT','date'=>'2 May 2026','read'=>'7 min','emoji'=>'📊','desc'=>'If you are torn between an IIM MBA and an international business school, this detailed ROI comparison will help you decide.','tag'=>'bg-indigo-500/10 text-indigo-400 border-indigo-500/20'],
        ['title'=>'Study in Canada 2026: Universities, Costs & Visa Guide','cat'=>'Study Abroad','date'=>'28 Apr 2026','read'=>'9 min','emoji'=>'🍁','desc'=>'Everything Indian students need to know about applying to Canadian universities — rankings, IELTS requirements, PR pathways.','tag'=>'bg-red-500/10 text-red-400 border-red-500/20'],
        ['title'=>'How to Write a Winning SOP for Engineering Admissions','cat'=>'Career Guide','date'=>'25 Apr 2026','read'=>'6 min','emoji'=>'✍️','desc'=>'Step-by-step guide to writing a Statement of Purpose that stands out, with examples from successful admits.','tag'=>'bg-teal-500/10 text-teal-400 border-teal-500/20'],
        ['title'=>'BITSAT 2026: Complete Guide to Preparation & Strategy','cat'=>'JEE','date'=>'20 Apr 2026','read'=>'5 min','emoji'=>'⚡','desc'=>'How to score 300+ in BITSAT: sectional strategy, English & LR tips, and time management for the 3-hour online exam.','tag'=>'bg-blue-500/10 text-blue-400 border-blue-500/20'],
        ['title'=>'VIT vs Manipal vs SRM: The Definitive 2026 Comparison','cat'=>'College Reviews','date'=>'15 Apr 2026','read'=>'8 min','emoji'=>'⚖️','desc'=>'Campus facilities, placement records, branch options, and student reviews — all in one comprehensive comparison guide.','tag'=>'bg-purple-500/10 text-purple-400 border-purple-500/20'],
      ];
      foreach ($posts as $p): ?>
      <article class="post-card glass rounded-2xl border border-[var(--border)] overflow-hidden card-hover group flex flex-col"
               data-cat="<?= $p['cat'] ?>" data-title="<?= strtolower($p['title']) ?>">
        <!-- Thumbnail -->
        <div class="h-40 bg-gradient-to-br from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] flex items-center justify-center text-6xl">
          <?= $p['emoji'] ?>
        </div>
        <div class="p-5 flex flex-col flex-1">
          <div class="flex items-center gap-2 mb-3">
            <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded-full border <?= $p['tag'] ?>"><?= $p['cat'] ?></span>
            <span class="text-[10px] text-[var(--text-muted)]"><?= $p['read'] ?> read</span>
          </div>
          <h2 class="font-bold text-white text-sm mb-2 group-hover:text-purple-400 transition-colors leading-snug flex-1">
            <?= htmlspecialchars($p['title']) ?>
          </h2>
          <p class="text-xs text-[var(--text-secondary)] leading-relaxed mb-4">
            <?= htmlspecialchars($p['desc']) ?>
          </p>
          <div class="flex items-center justify-between mt-auto">
            <span class="text-[10px] text-[var(--text-muted)] flex items-center gap-1">
              <i data-lucide="calendar" class="w-3 h-3"></i> <?= $p['date'] ?>
            </span>
            <button class="text-xs font-medium text-purple-400 hover:text-purple-300 flex items-center gap-1 transition-colors">
              Read More <i data-lucide="arrow-right" class="w-3 h-3 group-hover:translate-x-1 transition-transform"></i>
            </button>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <!-- No Results -->
    <div id="noResults" class="hidden text-center py-16">
      <i data-lucide="search-x" class="w-12 h-12 mx-auto mb-4 text-[var(--text-muted)] opacity-40"></i>
      <p class="text-[var(--text-secondary)]">No articles found. Try a different search.</p>
    </div>

    <!-- Newsletter CTA -->
    <div class="mt-14 glass rounded-3xl border border-purple-500/20 bg-purple-500/5 p-10 text-center">
      <i data-lucide="mail" class="w-10 h-10 mx-auto mb-4 text-purple-400"></i>
      <h2 class="text-2xl font-bold text-white mb-2">Never Miss an Important Deadline</h2>
      <p class="text-[var(--text-secondary)] text-sm mb-6 max-w-md mx-auto">Get weekly exam alerts, scholarship deadlines, and admission tips delivered to your inbox.</p>
      <form class="flex gap-3 max-w-sm mx-auto" onsubmit="event.preventDefault(); this.innerHTML='<p class=\'text-emerald-400 font-medium\'>✅ Subscribed! Check your email.</p>'">
        <input type="email" placeholder="Your email address" required
          class="flex-1 px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-purple-500/60 transition-all">
        <button type="submit" class="btn-primary px-5 py-3 rounded-xl font-semibold text-sm whitespace-nowrap">Subscribe</button>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>
lucide.createIcons();
let activeCat = 'All';

function filterCat(cat) {
  activeCat = cat;
  document.querySelectorAll('.cat-btn').forEach(b => {
    const isActive = b.dataset.cat === cat;
    b.className = 'cat-btn px-4 py-1.5 rounded-full text-xs font-semibold border transition-all ' +
      (isActive ? 'bg-purple-500/20 border-purple-500/40 text-purple-400' : 'border-[var(--border)] text-[var(--text-secondary)] hover:border-purple-500/30 hover:text-white');
  });
  filterPosts();
}

function filterPosts() {
  const q = document.getElementById('blogSearch').value.toLowerCase();
  const cards = document.querySelectorAll('.post-card');
  let visible = 0;
  cards.forEach(c => {
    const matchCat = activeCat === 'All' || c.dataset.cat === activeCat;
    const matchQ   = !q || c.dataset.title.includes(q);
    const show = matchCat && matchQ;
    c.style.display = show ? '' : 'none';
    if (show) visible++;
  });
  document.getElementById('noResults').classList.toggle('hidden', visible > 0);
}
</script>
</body>
</html>
