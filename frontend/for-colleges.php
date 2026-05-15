<?php $page_title = "List Your College on AdmissionSeason | Reach 10 Lakh+ Students"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Partner with AdmissionSeason to reach 10 lakh+ students actively looking for colleges. Get verified leads, analytics, and a premium profile.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .float { animation: float 4s ease-in-out infinite; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">

  <!-- Hero -->
  <div class="relative overflow-hidden bg-gradient-to-br from-[var(--bg-secondary)] via-indigo-950/30 to-[var(--bg-primary)] border-b border-[var(--border)] py-24">
    <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-indigo-500/10 blur-[140px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-purple-500/10 blur-[120px] rounded-full pointer-events-none"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
        <div>
          <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-6 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold uppercase tracking-widest">
            <i data-lucide="building-2" class="w-3.5 h-3.5"></i> For Colleges & Universities
          </div>
          <h1 class="text-4xl sm:text-5xl font-black text-white leading-tight mb-5">
            Reach <span class="gradient-text">10 Lakh+</span><br>Students Actively<br>Seeking Admission
          </h1>
          <p class="text-[var(--text-secondary)] text-lg leading-relaxed mb-8 max-w-lg">
            AdmissionSeason is India's fastest-growing college discovery platform. List your institution and connect with highly intent students through verified leads, AI-powered matching, and premium profiles.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <a href="#contact" class="btn-primary inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-base font-bold">
              <i data-lucide="rocket" class="w-5 h-5"></i> Get Listed Free
            </a>
            <a href="#pricing" class="glass inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-base font-semibold border border-[var(--border)] hover:bg-white/5 transition-colors">
              <i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i> See Premium Plans
            </a>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-5">
          <?php $stats = [
            ['val'=>'10L+','label'=>'Monthly Students','icon'=>'users','color'=>'text-indigo-400','border'=>'border-indigo-500/20'],
            ['val'=>'30K+','label'=>'Colleges Listed','icon'=>'building-2','color'=>'text-emerald-400','border'=>'border-emerald-500/20'],
            ['val'=>'95%','label'=>'Lead Accuracy','icon'=>'target','color'=>'text-amber-400','border'=>'border-amber-500/20'],
            ['val'=>'3X','label'=>'More Inquiries','icon'=>'trending-up','color'=>'text-purple-400','border'=>'border-purple-500/20'],
          ]; foreach ($stats as $s): ?>
          <div class="glass rounded-2xl border <?= $s['border'] ?> p-6 text-center float" style="animation-delay:<?= rand(0,2) ?>s">
            <div class="text-3xl font-black text-white mb-1"><?= $s['val'] ?></div>
            <div class="text-xs text-[var(--text-muted)]"><?= $s['label'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Features -->
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-20">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold text-white mb-3">Everything You Need to Fill Seats Faster</h2>
      <p class="text-[var(--text-secondary)] max-w-xl mx-auto">One platform. Complete admission marketing stack.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php $features = [
        ['icon'=>'shield-check','title'=>'Verified College Profile','desc'=>'Premium verified badge builds instant trust with students and parents. Showcase photos, videos, placements, and facilities.','color'=>'text-indigo-400','bg'=>'bg-indigo-500/10','border'=>'border-indigo-500/20'],
        ['icon'=>'users','title'=>'Targeted Lead Generation','desc'=>'We match students based on stream, location, budget, and exam scores — ensuring you only get highly qualified leads.','color'=>'text-emerald-400','bg'=>'bg-emerald-500/10','border'=>'border-emerald-500/20'],
        ['icon'=>'bot','title'=>'AI Counselor Integration','desc'=>'Our AI counselor recommends your college to students based on their profile — completely automated, 24/7.','color'=>'text-purple-400','bg'=>'bg-purple-500/10','border'=>'border-purple-500/20'],
        ['icon'=>'bar-chart-2','title'=>'Real-Time Analytics','desc'=>'Dashboard with profile views, inquiry rate, conversion metrics, and comparison reports against competing colleges.','color'=>'text-amber-400','bg'=>'bg-amber-500/10','border'=>'border-amber-500/20'],
        ['icon'=>'star','title'=>'Reviews & Ratings','desc'=>'Manage student reviews, respond to queries, and build your reputation. Colleges with 4+ ratings get 60% more inquiries.','color'=>'text-orange-400','bg'=>'bg-orange-500/10','border'=>'border-orange-500/20'],
        ['icon'=>'zap','title'=>'Priority Placement','desc'=>'Featured placement on search results, exam pages, and AI recommendations. Be the first college students see.','color'=>'text-teal-400','bg'=>'bg-teal-500/10','border'=>'border-teal-500/20'],
      ]; foreach ($features as $f): ?>
      <div class="glass rounded-2xl border <?= $f['border'] ?> p-6 hover:scale-[1.02] transition-transform">
        <div class="w-12 h-12 rounded-xl <?= $f['bg'] ?> border <?= $f['border'] ?> flex items-center justify-center mb-4">
          <i data-lucide="<?= $f['icon'] ?>" class="w-6 h-6 <?= $f['color'] ?>"></i>
        </div>
        <h3 class="font-bold text-white mb-2"><?= $f['title'] ?></h3>
        <p class="text-sm text-[var(--text-secondary)] leading-relaxed"><?= $f['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Pricing -->
  <div id="pricing" class="bg-[var(--bg-secondary)] border-y border-[var(--border)] py-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-white mb-3">Simple, Transparent Pricing</h2>
        <p class="text-[var(--text-secondary)]">Start free, upgrade when you need more leads.</p>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php $plans = [
          ['name'=>'Free','price'=>'₹0','period'=>'/month','desc'=>'Get started with a basic listing','color'=>'border-[var(--border)]','btnClass'=>'glass border border-[var(--border)] hover:bg-white/5','features'=>['Basic college profile','Up to 5 photos','Student reviews visible','Basic analytics','Standard placement in search']],
          ['name'=>'Growth','price'=>'₹4,999','period'=>'/month','desc'=>'For colleges actively recruiting','color'=>'border-indigo-500/40','btnClass'=>'btn-primary','badge'=>'Most Popular','features'=>['Verified ✓ badge','Unlimited photos + video','25 leads/month guaranteed','Advanced analytics dashboard','Priority in AI Counselor','Featured on exam pages']],
          ['name'=>'Elite','price'=>'₹14,999','period'=>'/month','desc'=>'For top institutions & universities','color'=>'border-amber-500/30','btnClass'=>'bg-gradient-to-r from-amber-500 to-orange-500 text-white hover:from-amber-400 hover:to-orange-400','badge'=>'Best ROI','features'=>['Everything in Growth','100+ leads/month','Dedicated account manager','Homepage banner placement','Custom landing page','Brochure PDF distribution','WhatsApp lead alerts']],
        ]; foreach ($plans as $p): ?>
        <div class="glass rounded-2xl border <?= $p['color'] ?> p-7 flex flex-col relative overflow-hidden">
          <?php if (!empty($p['badge'])): ?>
          <div class="absolute top-4 right-4 px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30"><?= $p['badge'] ?></div>
          <?php endif; ?>
          <div class="mb-5">
            <h3 class="text-lg font-bold text-white mb-1"><?= $p['name'] ?></h3>
            <div class="text-3xl font-black text-white"><?= $p['price'] ?><span class="text-sm font-normal text-[var(--text-muted)]"><?= $p['period'] ?></span></div>
            <p class="text-xs text-[var(--text-secondary)] mt-2"><?= $p['desc'] ?></p>
          </div>
          <ul class="space-y-2.5 mb-7 flex-1">
            <?php foreach ($p['features'] as $feat): ?>
            <li class="flex items-start gap-2 text-sm text-[var(--text-secondary)]">
              <i data-lucide="check" class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5"></i> <?= $feat ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <a href="#contact" class="w-full py-3 rounded-xl text-center font-semibold text-sm transition-all <?= $p['btnClass'] ?>">
            Get Started
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Contact / Lead Form -->
  <div id="contact" class="max-w-2xl mx-auto px-4 sm:px-6 py-20">
    <div class="text-center mb-10">
      <h2 class="text-3xl font-bold text-white mb-3">Get Your College Listed Today</h2>
      <p class="text-[var(--text-secondary)]">Fill out the form and our team will reach out within 24 hours.</p>
    </div>
    <div class="glass rounded-3xl border border-[var(--border)] p-8">
      <?php
      $formSuccess = false;
      $formError = '';
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          $cname  = trim($_POST['college_name'] ?? '');
          $cname2 = trim($_POST['contact_name'] ?? '');
          $cemail = trim($_POST['email'] ?? '');
          $cphone = trim($_POST['phone'] ?? '');
          $ctype  = trim($_POST['type'] ?? '');
          if ($cname && $cemail && $cphone) {
              // In production: save to DB / send email. For now just show success.
              $formSuccess = true;
          } else {
              $formError = 'Please fill all required fields.';
          }
      }
      ?>
      <?php if ($formSuccess): ?>
      <div class="text-center py-8">
        <div class="text-5xl mb-4">🎉</div>
        <h3 class="text-xl font-bold text-white mb-2">Request Received!</h3>
        <p class="text-[var(--text-secondary)] text-sm">Our team will contact you within 24 hours. Check your email for confirmation.</p>
      </div>
      <?php else: ?>
      <?php if ($formError): ?>
      <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-sm"><?= $formError ?></div>
      <?php endif; ?>
      <form method="POST" action="#contact" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1.5 block">College Name *</label>
            <input type="text" name="college_name" required placeholder="e.g. ABC Institute of Technology"
              class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
          </div>
          <div>
            <label class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1.5 block">Contact Person *</label>
            <input type="text" name="contact_name" required placeholder="Your full name"
              class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
          </div>
          <div>
            <label class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1.5 block">Email *</label>
            <input type="email" name="email" required placeholder="admissions@college.edu"
              class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
          </div>
          <div>
            <label class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1.5 block">Phone *</label>
            <input type="tel" name="phone" required placeholder="+91 98765 43210"
              class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all">
          </div>
        </div>
        <div>
          <label class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1.5 block">College Type</label>
          <div class="relative">
            <select name="type" class="w-full appearance-none px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white outline-none focus:border-indigo-500/60 cursor-pointer">
              <option>Engineering</option><option>Medical</option><option>Management</option>
              <option>Arts & Science</option><option>Law</option><option>Design</option>
              <option>Pharmacy</option><option>University (Multi-stream)</option>
            </select>
            <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)] pointer-events-none"></i>
          </div>
        </div>
        <div>
          <label class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1.5 block">Message (Optional)</label>
          <textarea name="message" rows="3" placeholder="Tell us about your college, current student intake, and what you're looking for..."
            class="w-full px-4 py-3 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white placeholder-[var(--text-muted)] outline-none focus:border-indigo-500/60 transition-all resize-none"></textarea>
        </div>
        <button type="submit" class="w-full btn-primary py-4 rounded-xl font-bold text-base flex items-center justify-center gap-2">
          <i data-lucide="send" class="w-5 h-5"></i> Submit — Get Listed Free
        </button>
        <p class="text-center text-xs text-[var(--text-muted)]">No spam. Our team responds within 24 hours. 🔒</p>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>lucide.createIcons();</script>
</body>
</html>
