<?php
// frontend/includes/footer.php
$footerLinks = [
  "Colleges" => [
    [ "label" => "Engineering", "href" => "colleges.php?type=engineering" ],
    [ "label" => "Medical", "href" => "colleges.php?type=medical" ],
    [ "label" => "Management", "href" => "colleges.php?type=management" ],
    [ "label" => "Law", "href" => "colleges.php?type=law" ],
    [ "label" => "Top 100 Colleges", "href" => "rankings.php" ],
  ],
  "Exams" => [
    [ "label" => "JEE Main 2026", "href" => "exams.php?slug=jee-main" ],
    [ "label" => "NEET UG 2026", "href" => "exams.php?slug=neet" ],
    [ "label" => "CAT 2025", "href" => "exams.php?slug=cat" ],
    [ "label" => "GATE 2026", "href" => "exams.php?slug=gate" ],
    [ "label" => "All Exams", "href" => "exams.php" ],
  ],
  "Tools" => [
    [ "label" => "AI Counselor", "href" => "ai-counselor.php" ],
    [ "label" => "Rank Predictor", "href" => "rank-predictor.php" ],
    [ "label" => "ROI Calculator", "href" => "roi-calculator.php" ],
    [ "label" => "Scholarship Finder", "href" => "scholarships.php" ],
    [ "label" => "Study Abroad", "href" => "study-abroad.php" ],
  ],
  "Company" => [
    [ "label" => "About Us", "href" => "about.php" ],
    [ "label" => "Blog", "href" => "blog.php" ],
    [ "label" => "For Colleges", "href" => "for-colleges.php" ],
    [ "label" => "Privacy Policy", "href" => "privacy.php" ],
    [ "label" => "Terms of Service", "href" => "terms.php" ],
  ],
];
?>

<footer class="relative border-t border-[var(--border)] bg-[var(--bg-secondary)] mt-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
    <!-- Top Grid -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-8 mb-12">
      <!-- Brand -->
      <div class="col-span-2">
        <a href="index.php" class="flex items-center gap-2 mb-4">
          <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
            <i data-lucide="graduation-cap" class="w-4 h-4 text-white"></i>
          </div>
          <span class="text-lg font-bold gradient-text">AdmissionSeason</span>
        </a>
        <p class="text-sm text-[var(--text-secondary)] leading-relaxed mb-5">
          India's smartest college discovery platform. AI-powered recommendations, verified reviews, and direct applications — all in one place.
        </p>
        <div class="flex gap-3">
          <?php
          $socials = [
            ["icon" => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>', "label" => "Twitter"],
            ["icon" => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>', "label" => "LinkedIn"],
            ["icon" => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>', "label" => "Instagram"],
            ["icon" => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>', "label" => "YouTube"],
          ];
          foreach ($socials as $soc):
          ?>
            <a href="#" aria-label="<?= $soc['label'] ?>" class="w-8 h-8 glass rounded-lg flex items-center justify-center border border-[var(--border)] text-[var(--text-muted)] hover:text-white hover:border-indigo-500/50 transition-all hover:-translate-y-0.5">
              <?= $soc['icon'] ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Links -->
      <?php foreach ($footerLinks as $category => $links): ?>
        <div>
          <h3 class="text-sm font-semibold text-white mb-4"><?= $category ?></h3>
          <ul class="space-y-2.5">
            <?php foreach ($links as $link): ?>
              <li>
                <a href="<?= $link['href'] ?>" class="text-sm text-[var(--text-secondary)] hover:text-indigo-400 transition-colors">
                  <?= $link['label'] ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Contact Strip -->
    <div class="flex flex-wrap gap-6 py-6 border-y border-[var(--border)] mb-8 text-sm text-[var(--text-secondary)]">
      <div class="flex items-center gap-2">
        <i data-lucide="mail" class="w-4 h-4 text-indigo-400"></i>
        <a href="mailto:hello@admissionseason.in" class="hover:text-white transition-colors">
          hello@admissionseason.in
        </a>
      </div>
      <div class="flex items-center gap-2">
        <i data-lucide="phone" class="w-4 h-4 text-teal-400"></i>
        <a href="tel:+911800000000" class="hover:text-white transition-colors">
          1800-000-0000 (Toll Free)
        </a>
      </div>
      <div class="flex items-center gap-2">
        <i data-lucide="map-pin" class="w-4 h-4 text-pink-400"></i>
        <span>New Delhi, India</span>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[var(--text-muted)]">
      <p>© <?= date('Y') ?> AdmissionSeason. All rights reserved.</p>
      <div class="flex gap-4">
        <a href="privacy.php" class="hover:text-white transition-colors">Privacy</a>
        <a href="terms.php" class="hover:text-white transition-colors">Terms</a>
        <a href="sitemap.xml" class="hover:text-white transition-colors">Sitemap</a>
      </div>
      <p class="flex items-center gap-1">
        Made with ❤️ for Indian students
      </p>
    </div>
  </div>
</footer>
